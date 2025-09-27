<?php
require_once 'auth_check.php';
require_once '../php/db_connect.php';

// Handle form submission for updating settings
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_title = $_POST['site_title'];
    $hero_name = $_POST['hero_name'];
    $hero_tagline = $_POST['hero_tagline'];
    $hero_photo_url = $_POST['hero_photo_url'];

    $stmt = $mysqli->prepare("UPDATE site_settings SET site_title = ?, hero_name = ?, hero_tagline = ?, hero_photo_url = ? WHERE id = 1");
    $stmt->bind_param("ssss", $site_title, $hero_name, $hero_tagline, $hero_photo_url);

    if ($stmt->execute()) {
        $message = "Site settings updated successfully!";
    } else {
        $error = "Error updating settings: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch current settings
$result = $mysqli->query("SELECT * FROM site_settings WHERE id = 1");
$settings = $result->fetch_assoc();

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Site Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

    <header class="admin-header">
        <h1>Manage Site Settings</h1>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="admin-container">
        <div class="content-card">
            <h2>Update Site Settings</h2>
            <?php if (isset($message)): ?><p style="color: green;"><?php echo $message; ?></p><?php endif; ?>
            <?php if (isset($error)): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>

            <form action="manage_settings.php" method="POST">
                <div class="form-group">
                    <label for="site_title">Site Title</label>
                    <input type="text" id="site_title" name="site_title" value="<?php echo htmlspecialchars($settings['site_title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="hero_name">Hero Name</label>
                    <input type="text" id="hero_name" name="hero_name" value="<?php echo htmlspecialchars($settings['hero_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="hero_tagline">Hero Tagline</label>
                    <input type="text" id="hero_tagline" name="hero_tagline" value="<?php echo htmlspecialchars($settings['hero_tagline']); ?>" required>
                </div>
                 <div class="form-group">
                    <label for="hero_photo_url">Hero Photo URL</label>
                    <input type="text" id="hero_photo_url" name="hero_photo_url" value="<?php echo htmlspecialchars($settings['hero_photo_url']); ?>" required>
                </div>
                <button type="submit" class="btn">Save Settings</button>
            </form>
        </div>
    </div>

</body>
</html>