<?php
require_once "db_connect.php";
session_start();

$file_id = $_GET['id'] ?? null;
if (!$file_id) {
    die("Invalid file ID.");
}

// Fetch file details from DB
$sql = "SELECT filepath, filename, password, is_protected FROM downloads WHERE id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $file_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $filepath, $filename, $db_password, $is_protected);
        mysqli_stmt_fetch($stmt);

        // If file is not protected, just download it
        if (!$is_protected) {
            trigger_download($filepath, $filename, $link, $file_id);
        }

        // --- Handle Protected File ---

        // Check if password was submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $submitted_password = $_POST['password'];
            if (password_verify($submitted_password, $db_password)) {
                // Correct password, trigger download
                trigger_download($filepath, $filename, $link, $file_id);
            } else {
                // Incorrect password, show error
                $_SESSION['download_error'] = "Incorrect password.";
                header("Location: " . $_SERVER['REQUEST_URI']); // Redirect back to password form
                exit;
            }
        }

        // If we're here, it means the file is protected and we need to show the password form
        display_password_form($filename);

    } else {
        die("File not found.");
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);


function trigger_download($filepath, $filename, $db_link, $file_id) {
    if (file_exists($filepath)) {
        // Increment download count
        $update_sql = "UPDATE downloads SET download_count = download_count + 1 WHERE id = ?";
        if ($update_stmt = mysqli_prepare($db_link, $update_sql)) {
            mysqli_stmt_bind_param($update_stmt, "i", $file_id);
            mysqli_stmt_execute($update_stmt);
        }

        // Force download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        die("File does not exist on server.");
    }
}

function display_password_form($filename) {
    $error_message = '';
    if (isset($_SESSION['download_error'])) {
        $error_message = $_SESSION['download_error'];
        unset($_SESSION['download_error']); // Clear error after displaying
    }

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Required</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; }
        .form-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
        h2 { margin-top: 0; }
        .error { color: #dc3545; margin-bottom: 15px; }
        input[type="password"] { padding: 10px; border: 1px solid #ccc; border-radius: 4px; width: 250px; }
        input[type="submit"] { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Password Required</h2>
        <p>The file "<strong>{$filename}</strong>" is password protected.</p>
        <form action="" method="post">
            <input type="password" name="password" placeholder="Enter password" required>
            <br>
            <input type="submit" value="Download">
        </form>
        <p class="error">{$error_message}</p>
    </div>
</body>
</html>
HTML;
    exit;
}
?>