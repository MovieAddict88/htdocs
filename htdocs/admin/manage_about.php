<?php
require_once 'auth_check.php';
require_once '../php/db_connect.php';

// Handle form submission for updating the about section
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = $_POST['bio'];
    $philosophy = $_POST['philosophy'];
    $video_embed_url = $_POST['video_embed_url'];

    $stmt = $mysqli->prepare("UPDATE about SET bio = ?, philosophy = ?, video_embed_url = ? WHERE id = 1");
    $stmt->bind_param("sss", $bio, $philosophy, $video_embed_url);

    if ($stmt->execute()) {
        $message = "About section updated successfully!";
    } else {
        $error = "Error updating about section: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch current about section content
$result = $mysqli->query("SELECT * FROM about WHERE id = 1");
$about = $result->fetch_assoc();

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About Section</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

    <header class="admin-header">
        <h1>Manage About Section</h1>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="admin-container">
        <div class="content-card">
            <h2>Update About Me Content</h2>
            <?php if (isset($message)): ?><p style="color: green;"><?php echo $message; ?></p><?php endif; ?>
            <?php if (isset($error)): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>

            <form action="manage_about.php" method="POST">
                <div class="form-group">
                    <label for="bio">Biography</label>
                    <textarea id="bio" name="bio" rows="8" required><?php echo htmlspecialchars($about['bio']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="philosophy">My Philosophy</label>
                    <textarea id="philosophy" name="philosophy" rows="5"><?php echo htmlspecialchars($about['philosophy']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="video_embed_url">YouTube Video Embed URL</label>
                    <input type="text" id="video_embed_url" name="video_embed_url" value="<?php echo htmlspecialchars($about['video_embed_url']); ?>">
                </div>
                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>

</body>
</html>