<?php
session_start();
require_once '../includes/config.php';

// Authenticate: Ensure user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$success_message = '';
$error_message = '';
$upload_dir = '../uploads/';

// Ensure the uploads directory exists and is writable
if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
    $error_message = "Error: The uploads directory does not exist or is not writable.";
}

// Handle POST requests for Add/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error_message)) {
    // --- Delete Action ---
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $select_stmt = $conn->prepare("SELECT file_path FROM downloads WHERE id = ?");
        $select_stmt->bind_param("i", $id);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($file = $result->fetch_assoc()) {
            // If file doesn't exist or is deleted successfully
            if (!file_exists($file['file_path']) || unlink($file['file_path'])) {
                $delete_stmt = $conn->prepare("DELETE FROM downloads WHERE id = ?");
                $delete_stmt->bind_param("i", $id);
                if ($delete_stmt->execute()) {
                    $success_message = "File deleted successfully.";
                } else {
                    $error_message = "Error: Could not delete the database record, but the file was removed.";
                }
                $delete_stmt->close();
            } else {
                 $error_message = "Error: Could not delete the file from the server.";
            }
        } else {
            $error_message = "Error: File record not found.";
        }
        $select_stmt->close();
    }
    // --- Add Action ---
    elseif (isset($_FILES['file_upload'])) {
        if ($_FILES['file_upload']['error'] !== UPLOAD_ERR_OK) {
            // Map upload errors to user-friendly messages
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE   => 'File is larger than the server allows.',
                UPLOAD_ERR_FORM_SIZE  => 'File is larger than the form allows.',
                UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            ];
            $error_code = $_FILES['file_upload']['error'];
            $error_message = $upload_errors[$error_code] ?? 'An unknown error occurred during upload.';
        } else {
            $file_name = $_POST['file_name'];
            $password = $_POST['password'];

            // --- File Validation ---
            $max_file_size = 10 * 1024 * 1024; // 10 MB
            $allowed_extensions = ['pdf', 'zip', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
            $original_name = $_FILES['file_upload']['name'];
            $file_size = $_FILES['file_upload']['size'];
            $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

            if ($file_size > $max_file_size) {
                $error_message = "Error: File is too large. Maximum size is 10MB.";
            } elseif (!in_array($file_extension, $allowed_extensions)) {
                $error_message = "Error: Invalid file type. Allowed types are: " . implode(', ', $allowed_extensions);
            } else {
                $tmp_name = $_FILES['file_upload']['tmp_name'];
                $new_filename = uniqid('file_', true) . '.' . $file_extension;
                $file_path = $upload_dir . $new_filename;

                if (move_uploaded_file($tmp_name, $file_path)) {
                    $password_hash = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

                    $stmt = $conn->prepare("INSERT INTO downloads (file_name, file_path, password) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $file_name, $file_path, $password_hash);

                    if ($stmt->execute()) {
                        $success_message = "File uploaded and saved successfully.";
                    } else {
                        $error_message = "Error saving file details to database: " . $conn->error;
                        unlink($file_path);
                    }
                    $stmt->close();
                } else {
                    $error_message = "Error: Could not move uploaded file.";
                }
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $error_message = "No file was uploaded. Please select a file.";
    }
}

// Fetch all download entries to display
$download_entries = [];
$result = $conn->query("SELECT id, file_name, password, download_count, created_at FROM downloads ORDER BY created_at DESC");
if ($result) {
    $download_entries = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Downloads</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f4f4; margin: 0; }
        .header { background-color: #333; color: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 1.5rem; }
        .header a { color: #fff; text-decoration: none; }
        .container { max-width: 900px; margin: 30px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        form { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], input[type="password"], input[type="file"] { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions button { background: none; border: none; cursor: pointer; font-size: 1em; color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage Downloads</h1>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <div class="container">
        <?php if ($success_message): ?><div class="message success" role="alert"><?php echo $success_message; ?></div><?php endif; ?>
        <?php if ($error_message): ?><div class="message error" role="alert"><?php echo $error_message; ?></div><?php endif; ?>

        <!-- Add New File Form -->
        <form action="manage_downloads.php" method="post" enctype="multipart/form-data">
            <h2>Upload New File</h2>
            <label for="file_name">File Display Name</label>
            <input type="text" id="file_name" name="file_name" required>

            <label for="password">Password (Optional)</label>
            <input type="password" id="password" name="password" placeholder="Leave blank for no password">

            <label for="file_upload">Select File</label>
            <input type="file" id="file_upload" name="file_upload" required>

            <button type="submit">Upload File</button>
        </form>

        <!-- List of Files -->
        <h2>Managed Files</h2>
        <table>
            <thead>
                <tr>
                    <th>Display Name</th>
                    <th>Password Protected</th>
                    <th>Downloads</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($download_entries)): ?>
                    <tr><td colspan="5">No files have been uploaded.</td></tr>
                <?php else: ?>
                    <?php foreach ($download_entries as $entry): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($entry['file_name']); ?></td>
                        <td><?php echo !empty($entry['password']) ? 'Yes' : 'No'; ?></td>
                        <td><?php echo htmlspecialchars($entry['download_count']); ?></td>
                        <td><?php echo htmlspecialchars($entry['created_at']); ?></td>
                        <td class="actions">
                            <form action="manage_downloads.php" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                                <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this file permanently?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>