<?php
session_start();
require_once '../includes/database.php';

// If the user is not logged in, redirect them to the login page.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$pdo = get_db_connection();

// --- Action Handling (Add, Edit, Delete) ---

// Handle form submission for adding or editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation can be added here for better security

    $year = trim($_POST['year']);
    $degree = trim($_POST['degree']);
    $institution = trim($_POST['institution']);
    $description = trim($_POST['description']);
    $id = $_POST['id'] ?? null;

    // Basic validation
    if (empty($year) || empty($degree) || empty($institution)) {
        $message = '❌ Year, Degree, and Institution are required fields.';
    } else {
        try {
            if ($id) {
                // Update existing record
                $sql = "UPDATE education SET year = ?, degree = ?, institution = ?, description = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$year, $degree, $institution, $description, $id]);
                $message = '✅ Education entry updated successfully!';
            } else {
                // Insert new record
                $sql = "INSERT INTO education (year, degree, institution, description) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$year, $degree, $institution, $description]);
                $message = '✅ Education entry added successfully!';
            }
        } catch (PDOException $e) {
            $message = '❌ Database error: ' . $e->getMessage();
        }
    }
}

// Handle deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    // CSRF token validation should be used here as well
    $id_to_delete = $_GET['id'];
    try {
        $sql = "DELETE FROM education WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_to_delete]);
        $message = '✅ Education entry deleted successfully!';
    } catch (PDOException $e) {
        $message = '❌ Error deleting entry: ' . $e->getMessage();
    }
}

// --- Data Fetching ---

// Fetch all education entries to display
$education_entries = [];
try {
    $stmt = $pdo->query("SELECT * FROM education ORDER BY year DESC");
    $education_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("❌ Database error fetching education data: " . $e->getMessage());
}

// Fetch a single entry for editing
$entry_to_edit = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id_to_edit = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM education WHERE id = ?");
    $stmt->execute([$id_to_edit]);
    $entry_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Education</title>
    <link rel="stylesheet" href="css/admin_styles.css">
</head>
<body>
    <div class="sidebar">
        <h2>Portfolio Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="about.php">About Me</a>
        <a href="education.php" class="active">Education</a>
        <a href="#">Experience</a>
        <a href="#">Skills</a>
        <a href="#">Projects</a>
        <a href="#">Testimonials</a>
        <a href="#">Downloads</a>
        <a href="#">Contact Submissions</a>
        <a href="#">Analytics</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Manage Education</h1>
            <a href="index.php?action=logout" class="logout-link">Logout</a>
        </div>

        <div class="container">
            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="card">
                <h3><?php echo $entry_to_edit ? 'Edit' : 'Add New'; ?> Education Entry</h3>
                <form action="education.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $entry_to_edit['id'] ?? ''; ?>">
                    <div class="form-group">
                        <label for="year">Year</label>
                        <input type="text" id="year" name="year" value="<?php echo htmlspecialchars($entry_to_edit['year'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="degree">Degree / Certificate</label>
                        <input type="text" id="degree" name="degree" value="<?php echo htmlspecialchars($entry_to_edit['degree'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="institution">Institution</label>
                        <input type="text" id="institution" name="institution" value="<?php echo htmlspecialchars($entry_to_edit['institution'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <textarea id="description" name="description"><?php echo htmlspecialchars($entry_to_edit['description'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn-primary"><?php echo $entry_to_edit ? 'Update Entry' : 'Add Entry'; ?></button>
                </form>
            </div>

            <!-- Education List -->
            <div class="card">
                <h3>Existing Entries</h3>
                <div class="table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Degree</th>
                                <th>Institution</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($education_entries)): ?>
                                <tr>
                                    <td colspan="4" style="text-align:center;">No education entries found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($education_entries as $entry): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($entry['year']); ?></td>
                                        <td><?php echo htmlspecialchars($entry['degree']); ?></td>
                                        <td><?php echo htmlspecialchars($entry['institution']); ?></td>
                                        <td class="actions">
                                            <a href="education.php?action=edit&id=<?php echo $entry['id']; ?>">Edit</a>
                                            <a href="education.php?action=delete&id=<?php echo $entry['id']; ?>" onclick="return confirm('Are you sure you want to delete this entry?');" style="color:red;">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>