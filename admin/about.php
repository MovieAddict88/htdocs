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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = trim($_POST['bio']);
    $philosophy = trim($_POST['philosophy']);
    $video_url = trim($_POST['video_url']);

    try {
        // Check if data already exists
        $stmt = $pdo->query("SELECT id FROM about LIMIT 1");
        $exists = $stmt->fetch();

        if ($exists) {
            // Update existing record
            $sql = "UPDATE about SET bio = ?, philosophy = ?, video_url = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$bio, $philosophy, $video_url, $exists['id']]);
        } else {
            // Insert new record
            $sql = "INSERT INTO about (bio, philosophy, video_url) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$bio, $philosophy, $video_url]);
        }
        $message = '✅ "About Me" section updated successfully!';
    } catch (PDOException $e) {
        $message = '❌ Error updating "About Me" section: ' . $e->getMessage();
    }
}

// Fetch the current 'About Me' data
try {
    $stmt = $pdo->query("SELECT * FROM about LIMIT 1");
    $about = $stmt->fetch(PDO::FETCH_ASSOC);
    // If no data, initialize with empty values to avoid errors in the form
    if (!$about) {
        $about = ['bio' => '', 'philosophy' => '', 'video_url' => ''];
    }
} catch (PDOException $e) {
    die("❌ Database error fetching 'About Me' data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About Me</title>
    <link rel="stylesheet" href="css/admin_styles.css">
</head>
<body>
    <div class="sidebar">
        <h2>Portfolio Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="about.php" class="active">About Me</a>
        <a href="education.php">Education</a>
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
            <h1>Manage "About Me" Section</h1>
            <a href="index.php?action=logout" class="logout-link">Logout</a>
        </div>

        <div class="container">
            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <form action="about.php" method="post">
                    <div class="form-group">
                        <label for="bio">Biography</label>
                        <textarea id="bio" name="bio" required><?php echo htmlspecialchars($about['bio']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="philosophy">Teaching/Work Philosophy</label>
                        <textarea id="philosophy" name="philosophy"><?php echo htmlspecialchars($about['philosophy']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="video_url">Intro Video URL (Optional)</label>
                        <input type="text" id="video_url" name="video_url" value="<?php echo htmlspecialchars($about['video_url']); ?>" placeholder="e.g., https://www.youtube.com/watch?v=...">
                    </div>
                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>