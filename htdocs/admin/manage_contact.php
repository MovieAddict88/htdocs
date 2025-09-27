<?php
require_once 'auth_check.php';
require_once '../php/db_connect.php';

// Handle form submission for updating contact info
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = $_POST['address'];
    $email = $_POST['email'];
    $facebook_url = $_POST['facebook_url'];
    $tiktok_url = $_POST['tiktok_url'];
    $youtube_url = $_POST['youtube_url'];
    $instagram_url = $_POST['instagram_url'];

    $stmt = $mysqli->prepare("UPDATE contact_info SET address = ?, email = ?, facebook_url = ?, tiktok_url = ?, youtube_url = ?, instagram_url = ? WHERE id = 1");
    $stmt->bind_param("ssssss", $address, $email, $facebook_url, $tiktok_url, $youtube_url, $instagram_url);

    if ($stmt->execute()) {
        $message = "Contact information updated successfully!";
    } else {
        $error = "Error updating contact info: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch current contact info
$result = $mysqli->query("SELECT * FROM contact_info WHERE id = 1");
$contact = $result->fetch_assoc();

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Contact Info</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

    <header class="admin-header">
        <h1>Manage Contact Info</h1>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="admin-container">
        <div class="content-card">
            <h2>Update Contact Information</h2>
            <?php if (isset($message)): ?><p style="color: green;"><?php echo $message; ?></p><?php endif; ?>
            <?php if (isset($error)): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>

            <form action="manage_contact.php" method="POST">
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($contact['address']); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($contact['email']); ?>">
                </div>
                <div class="form-group">
                    <label for="facebook_url">Facebook URL</label>
                    <input type="text" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($contact['facebook_url']); ?>">
                </div>
                <div class="form-group">
                    <label for="tiktok_url">TikTok URL</label>
                    <input type="text" id="tiktok_url" name="tiktok_url" value="<?php echo htmlspecialchars($contact['tiktok_url']); ?>">
                </div>
                <div class="form-group">
                    <label for="youtube_url">YouTube URL</label>
                    <input type="text" id="youtube_url" name="youtube_url" value="<?php echo htmlspecialchars($contact['youtube_url']); ?>">
                </div>
                <div class="form-group">
                    <label for="instagram_url">Instagram URL</label>
                    <input type="text" id="instagram_url" name="instagram_url" value="<?php echo htmlspecialchars($contact['instagram_url']); ?>">
                </div>
                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>

</body>
</html>