<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "../php/db_connect.php";

// Initialize variables
$all_skills = [];
$name = $type = "";
$proficiency = 80;
$display_order = 0;
$edit_id = null;
$form_action = "Add New Skill";

// Fetch all skills
$sql = "SELECT id, name, type, proficiency, display_order FROM skills ORDER BY display_order ASC, type, name";
$result = mysqli_query($link, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $all_skills[] = $row;
    }
}

// Handle Add/Edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_skill'])) {
    $edit_id = $_POST['edit_id'] ?? null;
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $proficiency = (int)$_POST['proficiency'];
    $display_order = (int)$_POST['display_order'];

    if ($edit_id) { // Update
        $sql = "UPDATE skills SET name = ?, type = ?, proficiency = ?, display_order = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssiii", $name, $type, $proficiency, $display_order, $edit_id);
            mysqli_stmt_execute($stmt);
        }
    } else { // Insert
        $sql = "INSERT INTO skills (name, type, proficiency, display_order) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssii", $name, $type, $proficiency, $display_order);
            mysqli_stmt_execute($stmt);
        }
    }
    header("location: manage_skills.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM skills WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
    }
    header("location: manage_skills.php");
    exit;
}

// Handle Edit (populate form)
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $sql = "SELECT name, type, proficiency, display_order FROM skills WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $name, $type, $proficiency, $display_order);
            mysqli_stmt_fetch($stmt);
            $form_action = "Update Skill";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Skills</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="crud_style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Manage Skills</h1>
        <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

    <div class="crud-container">
        <div class="crud-form">
            <h3><?php echo $form_action; ?></h3>
            <form action="manage_skills.php" method="post">
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                <div class="form-group">
                    <label>Skill Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="type">
                        <option value="Soft Skill" <?php if($type == 'Soft Skill') echo 'selected'; ?>>Soft Skill</option>
                        <option value="Hard Skill" <?php if($type == 'Hard Skill') echo 'selected'; ?>>Hard Skill</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Proficiency (0-100)</label>
                    <input type="range" name="proficiency" min="0" max="100" value="<?php echo $proficiency; ?>" oninput="this.nextElementSibling.value = this.value">
                    <output><?php echo $proficiency; ?></output>
                </div>
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="display_order" value="<?php echo $display_order; ?>">
                </div>
                <div class="form-group">
                    <input type="submit" name="submit_skill" class="btn-primary" value="<?php echo $form_action; ?>">
                    <?php if ($edit_id): ?>
                        <a href="manage_skills.php" class="btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="crud-table">
            <h3>Existing Skills</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Proficiency</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_skills as $skill): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($skill['name']); ?></td>
                        <td><?php echo htmlspecialchars($skill['type']); ?></td>
                        <td>
                            <div style="width: 100%; background: #eee; border-radius: 5px;">
                                <div style="width: <?php echo $skill['proficiency']; ?>%; background: #007bff; color: white; text-align: center; border-radius: 5px;"><?php echo $skill['proficiency']; ?>%</div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($skill['display_order']); ?></td>
                        <td>
                            <a href="manage_skills.php?edit=<?php echo $skill['id']; ?>" class="btn-edit">Edit</a>
                            <a href="manage_skills.php?delete=<?php echo $skill['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($all_skills)): ?>
                    <tr><td colspan="5">No skills found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>