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
        $stmt = $mysqli->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Testimonial deleted successfully.";
        } else {
            throw new Exception("Error deleting testimonial: " . $stmt->error);
        }
        $stmt->close();
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $action) {
        $quote = $_POST['quote'];
        $author = $_POST['author'];
        $author_position = $_POST['author_position'];

        if ($action == 'add') {
            $stmt = $mysqli->prepare("INSERT INTO testimonials (quote, author, author_position) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $quote, $author, $author_position);
            if ($stmt->execute()) {
                $message = "New testimonial added successfully.";
            } else {
                throw new Exception("Error adding testimonial: " . $stmt->error);
            }
        } elseif ($action == 'edit' && $id) {
            $stmt = $mysqli->prepare("UPDATE testimonials SET quote = ?, author = ?, author_position = ? WHERE id = ?");
            $stmt->bind_param("sssi", $quote, $author, $author_position, $id);
            if ($stmt->execute()) {
                $message = "Testimonial updated successfully.";
            } else {
                throw new Exception("Error updating testimonial: " . $stmt->error);
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
    $stmt = $mysqli->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_item = $result->fetch_assoc();
    $stmt->close();
}

// Fetch all testimonials
$testimonials = [];
$result = $mysqli->query("SELECT * FROM testimonials ORDER BY display_order ASC, author ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $testimonials[] = $row;
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Testimonials</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <header class="admin-header">
        <h1>Manage Testimonials</h1>
        <div><a href="dashboard.php">Dashboard</a><a href="logout.php">Logout</a></div>
    </header>
    <div class="admin-container">
        <?php if ($message): ?><p style="color: green;"><?php echo $message; ?></p><?php endif; ?>
        <?php if ($error): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>

        <div class="content-card">
            <h2><?php echo $edit_item ? 'Edit' : 'Add'; ?> Testimonial</h2>
            <form action="manage_testimonials.php" method="POST">
                <input type="hidden" name="action" value="<?php echo $edit_item ? 'edit' : 'add'; ?>">
                <?php if ($edit_item): ?><input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>"><?php endif; ?>
                <div class="form-group"><label>Quote</label><textarea name="quote" rows="4" required><?php echo htmlspecialchars($edit_item['quote'] ?? ''); ?></textarea></div>
                <div class="form-group"><label>Author</label><input type="text" name="author" value="<?php echo htmlspecialchars($edit_item['author'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Author's Position</label><input type="text" name="author_position" value="<?php echo htmlspecialchars($edit_item['author_position'] ?? ''); ?>"></div>
                <button type="submit" class="btn"><?php echo $edit_item ? 'Update' : 'Add'; ?> Testimonial</button>
                <?php if ($edit_item): ?><a href="manage_testimonials.php" style="margin-left: 1rem;">Cancel Edit</a><?php endif; ?>
            </form>
        </div>

        <div class="content-card">
            <h2>Existing Testimonials</h2>
            <table>
                <thead><tr><th>Quote</th><th>Author</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($testimonials)): ?>
                        <tr><td colspan="3">No testimonials found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($testimonials as $item): ?>
                        <tr>
                            <td>"<?php echo htmlspecialchars(substr($item['quote'], 0, 50)); ?>..."</td>
                            <td><?php echo htmlspecialchars($item['author']); ?></td>
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