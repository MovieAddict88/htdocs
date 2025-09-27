<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "../php/db_connect.php";

$upload_dir = "../uploads/";
$all_downloads = [];
$error_msg = $success_msg = "";

// Fetch all downloads
$sql = "SELECT id, filename, filepath, is_protected, download_count FROM downloads ORDER BY filename ASC";
$result = mysqli_query($link, $sql);
if ($result) {
    $all_downloads = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    // First, get the filepath to delete the actual file
    $sql = "SELECT filepath FROM downloads WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $filepath_to_delete);
        if (mysqli_stmt_fetch($stmt)) {
            if (file_exists($filepath_to_delete)) {
                unlink($filepath_to_delete);
            }
        }
        mysqli_stmt_close($stmt);
    }
    // Then, delete the record from the database
    $sql = "DELETE FROM downloads WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
    }
    header("location: manage_downloads.php");
    exit;
}

// Handle Add form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file_to_upload'])) {
    $filename = trim($_POST['filename']);
    $password = trim($_POST['password']);
    $is_protected = !empty($password);
    $hashed_password = $is_protected ? password_hash($password, PASSWORD_DEFAULT) : null;

    if (isset($_FILES['file_to_upload']) && $_FILES['file_to_upload']['error'] == 0) {
        $original_filename = basename($_FILES["file_to_upload"]["name"]);
        $target_file = $upload_dir . time() . "_" . $original_filename; // Add timestamp to avoid name conflicts

        if (move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO downloads (filename, filepath, password, is_protected) VALUES (?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssi", $filename, $target_file, $hashed_password, $is_protected);
                if(mysqli_stmt_execute($stmt)){
                    $success_msg = "File uploaded and record added successfully.";
                } else {
                    $error_msg = "Database error: " . mysqli_error($link);
                }
            }
        } else {
            $error_msg = "Sorry, there was an error uploading your file.";
        }
    } else {
        $error_msg = "No file selected or upload error.";
    }
    header("location: manage_downloads.php"); // Refresh to show the new file
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Downloads</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="crud_style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Manage Download Center</h1>
        <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

    <div class="crud-container">
        <div class="crud-form">
            <h3>Upload New File</h3>
            <?php if ($error_msg): ?><div class="alert alert-danger"><?php echo $error_msg; ?></div><?php endif; ?>
            <?php if ($success_msg): ?><div class="alert alert-success"><?php echo $success_msg; ?></div><?php endif; ?>

            <form action="manage_downloads.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Display Name</label>
                    <input type="text" name="filename" required placeholder="e.g., My Resume">
                </div>
                <div class="form-group">
                    <label>File</label>
                    <input type="file" name="file_to_upload" required>
                </div>
                <div class="form-group">
                    <label>Password (Optional)</label>
                    <input type="text" name="password" placeholder="Leave blank for public access">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn-primary" value="Upload File">
                </div>
            </form>
        </div>

        <div class="crud-table">
            <h3>Existing Files</h3>
            <table>
                <thead>
                    <tr>
                        <th>Display Name</th>
                        <th>File Path</th>
                        <th>Protected</th>
                        <th>Downloads</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_downloads as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['filename']); ?></td>
                        <td><?php echo htmlspecialchars($item['filepath']); ?></td>
                        <td><?php echo $item['is_protected'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo htmlspecialchars($item['download_count']); ?></td>
                        <td>
                            <a href="manage_downloads.php?delete=<?php echo $item['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure? This will delete the file and the record.');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($all_downloads)): ?>
                    <tr><td colspan="5">No files found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<style>
.alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px; }
input[type="file"] { padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: #fff; }
</style>