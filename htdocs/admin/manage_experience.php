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
$edit_item = null;

// Handle POST requests for Add/Update/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Delete Action ---
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM experience WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success_message = "Experience entry deleted successfully.";
        } else {
            $error_message = "Error deleting entry: " . $conn->error;
        }
        $stmt->close();
    }
    // --- Add/Update Action ---
    else {
        $id = $_POST['id'] ?? null;
        $year_range = $_POST['year_range'];
        $position = $_POST['position'];
        $institution = $_POST['institution'];
        $description = $_POST['description'];
        // Note: Media URL handling would typically involve file uploads,
        // but for now we'll just save the URL as text.
        $media_url = $_POST['media_url'];

        if ($id) { // Update
            $stmt = $conn->prepare("UPDATE experience SET year_range = ?, position = ?, institution = ?, description = ?, media_url = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $year_range, $position, $institution, $description, $media_url, $id);
            $action = "updated";
        } else { // Insert
            $stmt = $conn->prepare("INSERT INTO experience (year_range, position, institution, description, media_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $year_range, $position, $institution, $description, $media_url);
            $action = "added";
        }

        if ($stmt->execute()) {
            $success_message = "Experience entry {$action} successfully.";
        } else {
            $error_message = "Error: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle GET request for editing an item
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM experience WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_item = $result->fetch_assoc();
    }
    $stmt->close();
}

// Fetch all experience entries to display
$experience_entries = [];
$result = $conn->query("SELECT * FROM experience ORDER BY year_range DESC");
if ($result) {
    $experience_entries = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Experience</title>
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
        input[type="text"], textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions a, .actions button { margin-right: 10px; text-decoration: none; color: #007bff; background: none; border: none; cursor: pointer; font-size: 1em; }
        .actions .delete { color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage Experience</h1>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <div class="container">
        <?php if ($success_message): ?><div class="message success" role="alert"><?php echo $success_message; ?></div><?php endif; ?>
        <?php if ($error_message): ?><div class="message error" role="alert"><?php echo $error_message; ?></div><?php endif; ?>

        <!-- Add/Edit Form -->
        <form action="manage_experience.php" method="post">
            <h2><?php echo $edit_item ? 'Edit' : 'Add New'; ?> Experience Entry</h2>
            <input type="hidden" name="id" value="<?php echo $edit_item['id'] ?? ''; ?>">

            <label for="year_range">Year Range (e.g., 2020-2022)</label>
            <input type="text" id="year_range" name="year_range" value="<?php echo htmlspecialchars($edit_item['year_range'] ?? ''); ?>" required>

            <label for="position">Position</label>
            <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($edit_item['position'] ?? ''); ?>" required>

            <label for="institution">Institution/Company</label>
            <input type="text" id="institution" name="institution" value="<?php echo htmlspecialchars($edit_item['institution'] ?? ''); ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($edit_item['description'] ?? ''); ?></textarea>

            <label for="media_url">Media Thumbnail URL (Optional)</label>
            <input type="text" id="media_url" name="media_url" value="<?php echo htmlspecialchars($edit_item['media_url'] ?? ''); ?>">

            <button type="submit"><?php echo $edit_item ? 'Update' : 'Add'; ?> Entry</button>
        </form>

        <!-- List of Entries -->
        <h2>Existing Entries</h2>
        <table>
            <thead>
                <tr>
                    <th>Year Range</th>
                    <th>Position</th>
                    <th>Institution</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($experience_entries)): ?>
                    <tr><td colspan="4">No experience entries found.</td></tr>
                <?php else: ?>
                    <?php foreach ($experience_entries as $entry): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($entry['year_range']); ?></td>
                        <td><?php echo htmlspecialchars($entry['position']); ?></td>
                        <td><?php echo htmlspecialchars($entry['institution']); ?></td>
                        <td class="actions">
                            <a href="manage_experience.php?edit=<?php echo $entry['id']; ?>">Edit</a>
                            <form action="manage_experience.php" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                                <button type="submit" name="delete" class="delete" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</button>
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