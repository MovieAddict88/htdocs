<?php
// --- DOWNLOAD HANDLER ---

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'db_connect.php';

    $file_id = $_POST['file_id'];
    $password = $_POST['password'];

    if (empty($file_id) || empty($password)) {
        die("Invalid request.");
    }

    // Prepare a statement to get file details
    $stmt = $mysqli->prepare("SELECT file_path, password FROM downloads WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $file = $result->fetch_assoc();

        // Verify the provided password against the stored hash
        if (password_verify($password, $file['password'])) {
            // Password is correct, proceed with download
            $file_path = '../' . $file['file_path'];

            if (file_exists($file_path)) {
                // Increment download count
                $update_stmt = $mysqli->prepare("UPDATE downloads SET download_count = download_count + 1 WHERE id = ?");
                $update_stmt->bind_param("i", $file_id);
                $update_stmt->execute();
                $update_stmt->close();

                // Send headers to force download
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_path));

                // Clear output buffer
                flush();

                // Read the file and send it to the browser
                readfile($file_path);

                $stmt->close();
                $mysqli->close();
                exit();
            } else {
                die("File not found on server.");
            }
        } else {
            // Invalid password
            die("Incorrect password.");
        }
    } else {
        // No file found with that ID
        die("File not found.");
    }

    $stmt->close();
    $mysqli->close();
} else {
    // Not a POST request
    header('Location: ../index.php');
    exit();
}
?>