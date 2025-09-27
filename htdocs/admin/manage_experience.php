<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "../php/db_connect.php";

// Initialize variables
$all_experience = [];
$year_range = $position = $institution = $description = $media_thumbnails = "";
$display_order = 0;
$edit_id = null;
$form_action = "Add New Entry";

// Fetch all experience entries
$sql = "SELECT id, year_range, position, institution, description, media_thumbnails, display_order FROM experience ORDER BY display_order ASC, year_range DESC";
$result = mysqli_query($link, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $all_experience[] = $row;
    }
}

// Handle Add/Edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_experience'])) {
    $edit_id = $_POST['edit_id'] ?? null;
    $year_range = trim($_POST['year_range']);
    $position = trim($_POST['position']);
    $institution = trim($_POST['institution']);
    $description = trim($_POST['description']);
    $media_thumbnails = trim($_POST['media_thumbnails']);
    $display_order = (int)$_POST['display_order'];

    if ($edit_id) { // Update
        $sql = "UPDATE experience SET year_range = ?, position = ?, institution = ?, description = ?, media_thumbnails = ?, display_order = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssii", $year_range, $position, $institution, $description, $media_thumbnails, $display_order, $edit_id);
            mysqli_stmt_execute($stmt);
        }
    } else { // Insert
        $sql = "INSERT INTO experience (year_range, position, institution, description, media_thumbnails, display_order) VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssi", $year_range, $position, $institution, $description, $media_thumbnails, $display_order);
            mysqli_stmt_execute($stmt);
        }
    }
    header("location: manage_experience.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM experience WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
    }
    header("location: manage_experience.php");
    exit;
}

// Handle Edit (populate form)
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $sql = "SELECT year_range, position, institution, description, media_thumbnails, display_order FROM experience WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $year_range, $position, $institution, $description, $media_thumbnails, $display_order);
            mysqli_stmt_fetch($stmt);
            $form_action = "Update Entry";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Experience</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="crud_style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Manage Experience</h1>
        <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

    <div class="crud-container">
        <div class="crud-form">
            <h3><?php echo $form_action; ?></h3>
            <form action="manage_experience.php" method="post">
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                <div class="form-group">
                    <label>Year Range (e.g., 2020-2022)</label>
                    <input type="text" name="year_range" value="<?php echo htmlspecialchars($year_range); ?>" required>
                </div>
                <div class="form-group">
                    <label>Position</label>
                    <input type="text" name="position" value="<?php echo htmlspecialchars($position); ?>" required>
                </div>
                <div class="form-group">
                    <label>Institution</label>
                    <input type="text" name="institution" value="<?php echo htmlspecialchars($institution); ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Media Thumbnails (JSON array of URLs)</label>
                    <textarea name="media_thumbnails" rows="2"><?php echo htmlspecialchars($media_thumbnails); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="display_order" value="<?php echo $display_order; ?>">
                </div>
                <div class="form-group">
                    <input type="submit" name="submit_experience" class="btn-primary" value="<?php echo $form_action; ?>">
                    <?php if ($edit_id): ?>
                        <a href="manage_experience.php" class="btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="crud-table">
            <h3>Existing Entries</h3>
            <table>
                <thead>
                    <tr>
                        <th>Year Range</th>
                        <th>Position</th>
                        <th>Institution</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_experience as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['year_range']); ?></td>
                        <td><?php echo htmlspecialchars($item['position']); ?></td>
                        <td><?php echo htmlspecialchars($item['institution']); ?></td>
                        <td><?php echo htmlspecialchars($item['display_order']); ?></td>
                        <td>
                            <a href="manage_experience.php?edit=<?php echo $item['id']; ?>" class="btn-edit">Edit</a>
                            <a href="manage_experience.php?delete=<?php echo $item['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($all_experience)): ?>
                    <tr><td colspan="5">No entries found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>