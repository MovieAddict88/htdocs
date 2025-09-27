<?php
require_once 'auth_check.php';
require_once '../php/db_connect.php';

// Handle Add, Edit, Delete actions
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id = $_POST['id'] ?? $_GET['id'] ?? null;
$message = '';
$error = '';

try {
    if ($action == 'delete' && $id) {
        // Optional: Also delete the file from the server
        // $stmt = $mysqli->prepare("SELECT file_path FROM downloads WHERE id = ?");
        // $stmt->bind_param("i", $id);
        // $stmt->execute();
        // $result = $stmt->get_result();
        // $file = $result->fetch_assoc();
        // if ($file && file_exists('../' . $file['file_path'])) {
        //     unlink('../' . $file['file_path']);
        // }

        $stmt = $mysqli->prepare("DELETE FROM downloads WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Download entry deleted successfully.";
        } else {
            throw new Exception("Error deleting entry: " . $stmt->error);
        }
        $stmt->close();
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'add') {
        $file_name = $_POST['file_name'];
        $password = $_POST['password'];
        $file_path = '';

        // Handle file upload
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $target_dir = "../downloads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $target_file = $target_dir . basename($_FILES["file"]["name"]);
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $file_path = "downloads/" . basename($_FILES["file"]["name"]);
            } else {
                throw new Exception("Sorry, there was an error uploading your file.");
            }
        } else {
             throw new Exception("File upload is required.");
        }

        // Hash the password for storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("INSERT INTO downloads (file_name, file_path, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $file_name, $file_path, $hashed_password);
        if ($stmt->execute()) {
            $message = "New download added successfully.";
        } else {
            throw new Exception("Error adding download: " . $stmt->error);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Fetch all downloads
$downloads = [];
$result = $mysqli->query("SELECT id, file_name, file_path, download_count FROM downloads ORDER BY file_name ASC");
if ($result) {
    $downloads = $result->fetch_all(MYSQLI_ASSOC);
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Downloads</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <header class="admin-header"><h1>Manage Downloads</h1><div><a href="dashboard.php">Dashboard</a><a href="logout.php">Logout</a></div></header>
    <div class="admin-container">
        <?php if ($message): ?><p style="color: green;"><?php echo $message; ?></p><?php endif; ?>
        <?php if ($error): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>

        <div class="content-card">
            <h2>Add New Downloadable File</h2>
            <form action="manage_downloads.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="form-group"><label>File Name (e.g., "Resume.pdf")</label><input type="text" name="file_name" required></div>
                <div class="form-group"><label>File</label><input type="file" name="file" required></div>
                <div class="form-group"><label>Password</label><input type="text" name="password" required></div>
                <button type="submit" class="btn">Add File</button>
            </form>
        </div>

        <div class="content-card">
            <h2>Existing Downloads</h2>
            <table>
                <thead><tr><th>File Name</th><th>Path</th><th>Downloads</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($downloads)): ?>
                        <tr><td colspan="4">No files found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($downloads as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['file_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['file_path']); ?></td>
                            <td><?php echo htmlspecialchars($item['download_count']); ?></td>
                            <td class="actions">
                                <a href="?action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>