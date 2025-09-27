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
        $stmt = $mysqli->prepare("DELETE FROM experience WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Entry deleted successfully.";
        } else {
            throw new Exception("Error deleting entry: " . $stmt->error);
        }
        $stmt->close();
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $action) {
        $year_range = $_POST['year_range'];
        $position = $_POST['position'];
        $institution = $_POST['institution'];
        $description = $_POST['description'];

        if ($action == 'add') {
            $stmt = $mysqli->prepare("INSERT INTO experience (year_range, position, institution, description) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $year_range, $position, $institution, $description);
            if ($stmt->execute()) {
                $message = "New experience entry added successfully.";
            } else {
                throw new Exception("Error adding entry: " . $stmt->error);
            }
        } elseif ($action == 'edit' && $id) {
            $stmt = $mysqli->prepare("UPDATE experience SET year_range = ?, position = ?, institution = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $year_range, $position, $institution, $description, $id);
            if ($stmt->execute()) {
                $message = "Experience entry updated successfully.";
            } else {
                throw new Exception("Error updating entry: " . $stmt->error);
            }
        }
        $stmt->close();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Fetch data for editing if ID is provided
$edit_item = null;
if (($action == 'edit' || $action == 'show_edit') && $id) {
    $stmt = $mysqli->prepare("SELECT * FROM experience WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_item = $result->fetch_assoc();
    $stmt->close();
}

// Fetch all experience entries
$experience_entries = [];
$result = $mysqli->query("SELECT * FROM experience ORDER BY year_range DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $experience_entries[] = $row;
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Experience</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <header class="admin-header">
        <h1>Manage Experience</h1>
        <div><a href="dashboard.php">Dashboard</a><a href="logout.php">Logout</a></div>
    </header>
    <div class="admin-container">
        <?php if ($message): ?><p style="color: green;"><?php echo $message; ?></p><?php endif; ?>
        <?php if ($error): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>

        <div class="content-card">
            <h2><?php echo $edit_item ? 'Edit' : 'Add'; ?> Experience Entry</h2>
            <form action="manage_experience.php" method="POST">
                <input type="hidden" name="action" value="<?php echo $edit_item ? 'edit' : 'add'; ?>">
                <?php if ($edit_item): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                <?php endif; ?>
                <div class="form-group"><label>Year Range</label><input type="text" name="year_range" value="<?php echo htmlspecialchars($edit_item['year_range'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Position</label><input type="text" name="position" value="<?php echo htmlspecialchars($edit_item['position'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Institution</label><input type="text" name="institution" value="<?php echo htmlspecialchars($edit_item['institution'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" rows="5" required><?php echo htmlspecialchars($edit_item['description'] ?? ''); ?></textarea></div>
                <button type="submit" class="btn"><?php echo $edit_item ? 'Update' : 'Add'; ?> Entry</button>
                <?php if ($edit_item): ?><a href="manage_experience.php" style="margin-left: 1rem;">Cancel Edit</a><?php endif; ?>
            </form>
        </div>

        <div class="content-card">
            <h2>Existing Entries</h2>
            <table>
                <thead><tr><th>Year Range</th><th>Position</th><th>Institution</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($experience_entries)): ?>
                        <tr><td colspan="4">No entries found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($experience_entries as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['year_range']); ?></td>
                            <td><?php echo htmlspecialchars($item['position']); ?></td>
                            <td><?php echo htmlspecialchars($item['institution']); ?></td>
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