<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "../php/db_connect.php";

$name = $tagline = $profile_pic_url = $animated_bg_url = "";
$update_success = "";

// Fetch current data
$sql = "SELECT name, tagline, profile_pic_url, animated_bg_url FROM home LIMIT 1";
$result = mysqli_query($link, $sql);
$home = mysqli_fetch_assoc($result);
if ($home) {
    $name = $home['name'];
    $tagline = $home['tagline'];
    $profile_pic_url = $home['profile_pic_url'];
    $animated_bg_url = $home['animated_bg_url'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $tagline = trim($_POST["tagline"]);
    $profile_pic_url = trim($_POST["profile_pic_url"]);
    $animated_bg_url = trim($_POST["animated_bg_url"]);

    // Use an UPSERT approach (UPDATE or INSERT)
    $upsert_sql = "INSERT INTO home (id, name, tagline, profile_pic_url, animated_bg_url) VALUES (1, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), tagline = VALUES(tagline), profile_pic_url = VALUES(profile_pic_url), animated_bg_url = VALUES(animated_bg_url)";

    if ($stmt = mysqli_prepare($link, $upsert_sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $name, $tagline, $profile_pic_url, $animated_bg_url);
        if (mysqli_stmt_execute($stmt)) {
            $update_success = "Home section updated successfully!";
        } else {
            echo "Error: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Home Section</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Manage Home Section</h1>
        <div>
            <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
            <a href="logout.php" class="btn-secondary">Logout</a>
        </div>
    </div>

    <div class="dashboard-wrapper">
        <?php if ($update_success): ?>
            <div class="alert alert-success"><?php echo $update_success; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div class="form-group">
                <label>Tagline</label>
                <input type="text" name="tagline" value="<?php echo htmlspecialchars($tagline); ?>">
            </div>
            <div class="form-group">
                <label>Profile Picture URL</label>
                <input type="text" name="profile_pic_url" value="<?php echo htmlspecialchars($profile_pic_url); ?>">
            </div>
            <div class="form-group">
                <label>Animated Background URL (Optional)</label>
                <input type="text" name="animated_bg_url" value="<?php echo htmlspecialchars($animated_bg_url); ?>">
            </div>
            <div class="form-group">
                <input type="submit" class="btn-primary" value="Save Changes">
            </div>
        </form>
    </div>
</body>
</html>

<style>
.alert-success {
    background-color: #d4edda;
    color: #155724;
    padding: 10px;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
    margin-bottom: 20px;
    text-align: center;
}
</style>