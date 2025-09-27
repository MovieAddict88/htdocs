<?php
session_start();
require_once '../includes/config.php';

// Authenticate: Ensure user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$success_message = '';
$error_message = '';

// Handle form submission for updating content
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = $_POST['bio'];
    $philosophy = $_POST['philosophy'];
    $video_url = $_POST['video_url'];
    $id = 1; // Assuming a single entry for the about page

    // Check if an entry already exists
    $stmt = $conn->prepare("SELECT id FROM about WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        // Update existing entry
        $stmt = $conn->prepare("UPDATE about SET bio = ?, philosophy = ?, video_url = ? WHERE id = ?");
        $stmt->bind_param("sssi", $bio, $philosophy, $video_url, $id);
    } else {
        // Insert new entry
        $stmt = $conn->prepare("INSERT INTO about (id, bio, philosophy, video_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id, $bio, $philosophy, $video_url);
    }

    if ($stmt->execute()) {
        $success_message = "About page content updated successfully!";
    } else {
        $error_message = "Error updating content: " . $conn->error;
    }
    $stmt->close();
}

// Fetch the current content to populate the form
$about_content = null;
$stmt = $conn->prepare("SELECT bio, philosophy, video_url FROM about WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $about_content = $result->fetch_assoc();
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About Page</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f4f4; margin: 0; }
        .header { background-color: #333; color: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 1.5rem; }
        .header a { color: #fff; text-decoration: none; }
        .container { max-width: 800px; margin: 30px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        form { display: flex; flex-direction: column; }
        label { margin-bottom: 10px; font-weight: bold; }
        textarea, input[type="text"] { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; font-family: inherit; }
        textarea { min-height: 150px; resize: vertical; }
        button { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.2s; align-self: flex-start; }
        button:hover { background-color: #0056b3; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage About Page</h1>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
    <div class="container">
        <?php if ($success_message): ?>
            <div class="message success" role="alert"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="message error" role="alert"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="manage_about.php" method="post">
            <label for="bio">Biography</label>
            <textarea id="bio" name="bio" required><?php echo htmlspecialchars($about_content['bio'] ?? ''); ?></textarea>

            <label for="philosophy">Teaching/Work Philosophy</label>
            <textarea id="philosophy" name="philosophy"><?php echo htmlspecialchars($about_content['philosophy'] ?? ''); ?></textarea>

            <label for="video_url">Intro Video URL (Optional)</label>
            <input type="text" id="video_url" name="video_url" placeholder="e.g., https://www.youtube.com/embed/your_video_id" value="<?php echo htmlspecialchars($about_content['video_url'] ?? ''); ?>">

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>