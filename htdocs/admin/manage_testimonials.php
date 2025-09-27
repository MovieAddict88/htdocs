<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "../php/db_connect.php";

// Initialize variables
$all_testimonials = [];
$quote = $author = $position = "";
$display_order = 0;
$edit_id = null;
$form_action = "Add New Testimonial";

// Fetch all testimonials
$sql = "SELECT id, quote, author, position, display_order FROM testimonials ORDER BY display_order ASC";
$result = mysqli_query($link, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $all_testimonials[] = $row;
    }
}

// Handle Add/Edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_testimonial'])) {
    $edit_id = $_POST['edit_id'] ?? null;
    $quote = trim($_POST['quote']);
    $author = trim($_POST['author']);
    $position = trim($_POST['position']);
    $display_order = (int)$_POST['display_order'];

    if ($edit_id) { // Update
        $sql = "UPDATE testimonials SET quote = ?, author = ?, position = ?, display_order = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssii", $quote, $author, $position, $display_order, $edit_id);
            mysqli_stmt_execute($stmt);
        }
    } else { // Insert
        $sql = "INSERT INTO testimonials (quote, author, position, display_order) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssi", $quote, $author, $position, $display_order);
            mysqli_stmt_execute($stmt);
        }
    }
    header("location: manage_testimonials.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM testimonials WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
    }
    header("location: manage_testimonials.php");
    exit;
}

// Handle Edit (populate form)
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $sql = "SELECT quote, author, position, display_order FROM testimonials WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $quote, $author, $position, $display_order);
            mysqli_stmt_fetch($stmt);
            $form_action = "Update Testimonial";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Testimonials</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="crud_style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Manage Testimonials</h1>
        <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

    <div class="crud-container">
        <div class="crud-form">
            <h3><?php echo $form_action; ?></h3>
            <form action="manage_testimonials.php" method="post">
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                <div class="form-group">
                    <label>Quote</label>
                    <textarea name="quote" rows="4" required><?php echo htmlspecialchars($quote); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Author</label>
                    <input type="text" name="author" value="<?php echo htmlspecialchars($author); ?>" required>
                </div>
                <div class="form-group">
                    <label>Author's Position (e.g., Mentor)</label>
                    <input type="text" name="position" value="<?php echo htmlspecialchars($position); ?>">
                </div>
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="display_order" value="<?php echo $display_order; ?>">
                </div>
                <div class="form-group">
                    <input type="submit" name="submit_testimonial" class="btn-primary" value="<?php echo $form_action; ?>">
                    <?php if ($edit_id): ?>
                        <a href="manage_testimonials.php" class="btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="crud-table">
            <h3>Existing Testimonials</h3>
            <table>
                <thead>
                    <tr>
                        <th>Quote</th>
                        <th>Author</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_testimonials as $testimonial): ?>
                    <tr>
                        <td><?php echo substr(htmlspecialchars($testimonial['quote']), 0, 100) . '...'; ?></td>
                        <td><?php echo htmlspecialchars($testimonial['author']); ?></td>
                        <td><?php echo htmlspecialchars($testimonial['display_order']); ?></td>
                        <td>
                            <a href="manage_testimonials.php?edit=<?php echo $testimonial['id']; ?>" class="btn-edit">Edit</a>
                            <a href="manage_testimonials.php?delete=<?php echo $testimonial['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($all_testimonials)): ?>
                    <tr><td colspan="4">No testimonials found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>