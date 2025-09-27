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
        $stmt = $mysqli->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Project deleted successfully.";
        } else {
            throw new Exception("Error deleting project: " . $stmt->error);
        }
        $stmt->close();
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $action) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $image_url = $_POST['image_url'];
        $project_link = $_POST['project_link'];

        if ($action == 'add') {
            $stmt = $mysqli->prepare("INSERT INTO projects (title, description, image_url, project_link) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $title, $description, $image_url, $project_link);
            if ($stmt->execute()) {
                $message = "New project added successfully.";
            } else {
                throw new Exception("Error adding project: " . $stmt->error);
            }
        } elseif ($action == 'edit' && $id) {
            $stmt = $mysqli->prepare("UPDATE projects SET title = ?, description = ?, image_url = ?, project_link = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $title, $description, $image_url, $project_link, $id);
            if ($stmt->execute()) {
                $message = "Project updated successfully.";
            } else {
                throw new Exception("Error updating project: " . $stmt->error);
            }
        }
        $stmt->close();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Fetch data for editing
$edit_item = null;
if (($action == 'edit' || $action == 'show_edit') && $id) {
    $stmt = $mysqli->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_item = $result->fetch_assoc();
    $stmt->close();
}

// Fetch all projects
$projects = [];
$result = $mysqli->query("SELECT * FROM projects ORDER BY display_order ASC, title ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Projects</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <header class="admin-header">
        <h1>Manage Projects</h1>
        <div><a href="dashboard.php">Dashboard</a><a href="logout.php">Logout</a></div>
    </header>
    <div class="admin-container">
        <?php if ($message): ?><p style="color: green;"><?php echo $message; ?></p><?php endif; ?>
        <?php if ($error): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>

        <div class="content-card">
            <h2><?php echo $edit_item ? 'Edit' : 'Add'; ?> Project</h2>
            <form action="manage_projects.php" method="POST">
                <input type="hidden" name="action" value="<?php echo $edit_item ? 'edit' : 'add'; ?>">
                <?php if ($edit_item): ?><input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>"><?php endif; ?>
                <div class="form-group"><label>Title</label><input type="text" name="title" value="<?php echo htmlspecialchars($edit_item['title'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" required><?php echo htmlspecialchars($edit_item['description'] ?? ''); ?></textarea></div>
                <div class="form-group"><label>Image URL</label><input type="text" name="image_url" value="<?php echo htmlspecialchars($edit_item['image_url'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Project Link (Optional)</label><input type="text" name="project_link" value="<?php echo htmlspecialchars($edit_item['project_link'] ?? ''); ?>"></div>
                <button type="submit" class="btn"><?php echo $edit_item ? 'Update' : 'Add'; ?> Project</button>
                <?php if ($edit_item): ?><a href="manage_projects.php" style="margin-left: 1rem;">Cancel Edit</a><?php endif; ?>
            </form>
        </div>

        <div class="content-card">
            <h2>Existing Projects</h2>
            <table>
                <thead><tr><th>Title</th><th>Image</th><th>Link</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr><td colspan="4">No projects found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($projects as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><img src="../<?php echo htmlspecialchars($item['image_url']); ?>" alt="" width="100"></td>
                            <td><a href="<?php echo htmlspecialchars($item['project_link']); ?>" target="_blank">Link</a></td>
                            <td class="actions">
                                <a href="?action=show_edit&id=<?php echo $item['id']; ?>">Edit</a>
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