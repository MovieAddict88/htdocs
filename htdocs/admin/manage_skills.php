<?php
require_once 'auth_check.php';
require_once '../php/db_connect.php';

// Handle Add/Delete actions
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id = $_POST['id'] ?? $_GET['id'] ?? null;
$message = '';
$error = '';

try {
    if ($action == 'delete' && $id) {
        $stmt = $mysqli->prepare("DELETE FROM skills WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Skill deleted successfully.";
        } else {
            throw new Exception("Error deleting skill: " . $stmt->error);
        }
        $stmt->close();
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'add') {
        $name = $_POST['name'];
        $type = $_POST['type'];

        $stmt = $mysqli->prepare("INSERT INTO skills (name, type) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $type);
        if ($stmt->execute()) {
            $message = "New skill added successfully.";
        } else {
            throw new Exception("Error adding skill: " . $stmt->error);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Fetch all skills
$skills = ['Hard Skill' => [], 'Soft Skill' => []];
$result = $mysqli->query("SELECT * FROM skills ORDER BY type, name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $skills[$row['type']][] = $row;
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Skills</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <header class="admin-header">
        <h1>Manage Skills</h1>
        <div><a href="dashboard.php">Dashboard</a><a href="logout.php">Logout</a></div>
    </header>
    <div class="admin-container">
        <?php if ($message): ?><p style="color: green;"><?php echo $message; ?></p><?php endif; ?>
        <?php if ($error): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>

        <div class="content-card">
            <h2>Add New Skill</h2>
            <form action="manage_skills.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="name">Skill Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="type">Skill Type</label>
                    <select id="type" name="type" required>
                        <option value="Hard Skill">Hard Skill</option>
                        <option value="Soft Skill">Soft Skill</option>
                    </select>
                </div>
                <button type="submit" class="btn">Add Skill</button>
            </form>
        </div>

        <div class="content-card">
            <h2>Existing Skills</h2>
            <div style="display: flex; gap: 2rem;">
                <div style="flex: 1;">
                    <h3>Hard Skills</h3>
                    <table>
                        <tbody>
                            <?php foreach ($skills['Hard Skill'] as $skill): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($skill['name']); ?></td>
                                <td class="actions">
                                    <a href="?action=delete&id=<?php echo $skill['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="flex: 1;">
                    <h3>Soft Skills</h3>
                    <table>
                        <tbody>
                            <?php foreach ($skills['Soft Skill'] as $skill): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($skill['name']); ?></td>
                                <td class="actions">
                                    <a href="?action=delete&id=<?php echo $skill['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>