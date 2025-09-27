<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "../php/db_connect.php";

// Initialize variables
$all_projects = [];
$title = $description = $images = $external_links = "";
$display_order = 0;
$edit_id = null;
$form_action = "Add New Project";

// Fetch all projects
$sql = "SELECT id, title, description, display_order FROM projects ORDER BY display_order ASC";
$result = mysqli_query($link, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $all_projects[] = $row;
    }
}

// Handle Add/Edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_project'])) {
    $edit_id = $_POST['edit_id'] ?? null;
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $images = trim($_POST['images']);
    $external_links = trim($_POST['external_links']);
    $display_order = (int)$_POST['display_order'];

    if ($edit_id) { // Update
        $sql = "UPDATE projects SET title = ?, description = ?, images = ?, external_links = ?, display_order = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssii", $title, $description, $images, $external_links, $display_order, $edit_id);
            mysqli_stmt_execute($stmt);
        }
    } else { // Insert
        $sql = "INSERT INTO projects (title, description, images, external_links, display_order) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssi", $title, $description, $images, $external_links, $display_order);
            mysqli_stmt_execute($stmt);
        }
    }
    header("location: manage_projects.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM projects WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
    }
    header("location: manage_projects.php");
    exit;
}

// Handle Edit (populate form)
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $sql = "SELECT title, description, images, external_links, display_order FROM projects WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $title, $description, $images, $external_links, $display_order);
            mysqli_stmt_fetch($stmt);
            $form_action = "Update Project";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Projects</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="crud_style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Manage Projects</h1>
        <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

    <div class="crud-container">
        <div class="crud-form">
            <h3><?php echo $form_action; ?></h3>
            <form action="manage_projects.php" method="post">
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                <div class="form-group">
                    <label>Project Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Images (JSON Array)</label>
                    <textarea name="images" rows="3" placeholder='["assets/project1.jpg", "assets/project2.jpg"]'><?php echo htmlspecialchars($images); ?></textarea>
                </div>
                <div class="form-group">
                    <label>External Links (JSON Object)</label>
                    <textarea name="external_links" rows="3" placeholder='{"GitHub": "https://github.com", "Live Demo": "https://example.com"}'><?php echo htmlspecialchars($external_links); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="display_order" value="<?php echo $display_order; ?>">
                </div>
                <div class="form-group">
                    <input type="submit" name="submit_project" class="btn-primary" value="<?php echo $form_action; ?>">
                    <?php if ($edit_id): ?>
                        <a href="manage_projects.php" class="btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="crud-table">
            <h3>Existing Projects</h3>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_projects as $project): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['title']); ?></td>
                        <td><?php echo substr(htmlspecialchars($project['description']), 0, 100) . '...'; ?></td>
                        <td><?php echo htmlspecialchars($project['display_order']); ?></td>
                        <td>
                            <a href="manage_projects.php?edit=<?php echo $project['id']; ?>" class="btn-edit">Edit</a>
                            <a href="manage_projects.php?delete=<?php echo $project['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($all_projects)): ?>
                    <tr><td colspan="4">No projects found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>