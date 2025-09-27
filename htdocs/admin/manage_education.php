<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "../php/db_connect.php";

$all_education = [];
$sql = "SELECT id, year, degree, institution, description, display_order FROM education ORDER BY display_order ASC, year DESC";
$result = mysqli_query($link, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $all_education[] = $row;
    }
}

$year = $degree = $institution = $description = "";
$display_order = 0;
$edit_id = null;
$form_action = "Add New Entry";

// Handle Add/Edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_education'])) {
    $edit_id = $_POST['edit_id'] ?? null;
    $year = trim($_POST['year']);
    $degree = trim($_POST['degree']);
    $institution = trim($_POST['institution']);
    $description = trim($_POST['description']);
    $display_order = (int)$_POST['display_order'];

    if ($edit_id) { // Update existing entry
        $sql = "UPDATE education SET year = ?, degree = ?, institution = ?, description = ?, display_order = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssii", $year, $degree, $institution, $description, $display_order, $edit_id);
            mysqli_stmt_execute($stmt);
        }
    } else { // Insert new entry
        $sql = "INSERT INTO education (year, degree, institution, description, display_order) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssi", $year, $degree, $institution, $description, $display_order);
            mysqli_stmt_execute($stmt);
        }
    }
    header("location: manage_education.php");
    exit;
}

// Handle Delete action
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM education WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
    }
    header("location: manage_education.php");
    exit;
}

// Handle Edit action (populate form)
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $sql = "SELECT year, degree, institution, description, display_order FROM education WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $year, $degree, $institution, $description, $display_order);
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
    <title>Manage Education</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Manage Education</h1>
        <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

    <div class="crud-container">
        <!-- Form for Adding/Editing -->
        <div class="crud-form">
            <h3><?php echo $form_action; ?></h3>
            <form action="manage_education.php" method="post">
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                <div class="form-group">
                    <label>Year</label>
                    <input type="text" name="year" value="<?php echo htmlspecialchars($year); ?>" required>
                </div>
                <div class="form-group">
                    <label>Degree</label>
                    <input type="text" name="degree" value="<?php echo htmlspecialchars($degree); ?>" required>
                </div>
                <div class="form-group">
                    <label>Institution</label>
                    <input type="text" name="institution" value="<?php echo htmlspecialchars($institution); ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="display_order" value="<?php echo $display_order; ?>">
                </div>
                <div class="form-group">
                    <input type="submit" name="submit_education" class="btn-primary" value="<?php echo $form_action; ?>">
                    <?php if ($edit_id): ?>
                        <a href="manage_education.php" class="btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Table of Existing Entries -->
        <div class="crud-table">
            <h3>Existing Entries</h3>
            <table>
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Degree</th>
                        <th>Institution</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_education as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['year']); ?></td>
                        <td><?php echo htmlspecialchars($item['degree']); ?></td>
                        <td><?php echo htmlspecialchars($item['institution']); ?></td>
                        <td><?php echo htmlspecialchars($item['display_order']); ?></td>
                        <td>
                            <a href="manage_education.php?edit=<?php echo $item['id']; ?>" class="btn-edit">Edit</a>
                            <a href="manage_education.php?delete=<?php echo $item['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($all_education)): ?>
                    <tr>
                        <td colspan="5">No entries found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<style>
.crud-container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
.crud-form, .crud-table { margin-bottom: 30px; }
textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
th { background-color: #f2f2f2; }
.btn-edit, .btn-delete {
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    color: white;
    margin-right: 5px;
}
.btn-edit { background-color: #ffc107; }
.btn-delete { background-color: #dc3545; }
</style>