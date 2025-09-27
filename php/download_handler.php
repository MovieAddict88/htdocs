<?php
session_start();
require_once 'db_config.php';

// This is a simplified example. In a real application, you would:
// 1. Fetch file details (including hashed password) from the 'downloads' table using the file_id.
// 2. Verify the submitted password against the stored hash.
// 3. Secure the file path to prevent directory traversal attacks.

$file_id = isset($_POST['file_id']) ? intval($_POST['file_id']) : 0;
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Placeholder data - in a real app, this comes from the DB
$files = [
    1 => ['filename' => 'resume_protected.pdf', 'password' => 'resume123'],
    2 => ['filename' => 'clearance_protected.pdf', 'password' => 'clearance123']
];

if ($file_id && isset($files[$file_id])) {
    $file = $files[$file_id];

    // Check if password is correct
    if ($file['password'] === $password) {
        // --- THIS IS A SIMULATED DOWNLOAD ---
        // In a real InfinityFree setup, the files would be in a protected directory.
        // For this demo, we'll just confirm it works and show a success message.
        // The actual file download headers would look like this:
        /*
        $filepath = 'path/to/protected/files/' . $file['filename'];
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);

            // Here you would also increment the download count in the database
            exit;
        }
        */

        echo "Success! If this were a live server, '" . htmlspecialchars($file['filename']) . "' would begin downloading now.";

    } else {
        echo "Incorrect password.";
    }
} else {
    echo "Invalid file specified.";
}
?>