<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "../php/db_connect.php";

$bio = $philosophy = $video_embed_url = "";
$update_success = "";

// Fetch current data
$sql = "SELECT bio, philosophy, video_embed_url FROM about LIMIT 1";
$result = mysqli_query($link, $sql);
$about = mysqli_fetch_assoc($result);
if ($about) {
    $bio = $about['bio'];
    $philosophy = $about['philosophy'];
    $video_embed_url = $about['video_embed_url'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bio = trim($_POST["bio"]);
    $philosophy = trim($_POST["philosophy"]);
    $video_embed_url = trim($_POST["video_embed_url"]);

    // Use an UPSERT approach (UPDATE or INSERT)
    $upsert_sql = "INSERT INTO about (id, bio, philosophy, video_embed_url) VALUES (1, ?, ?, ?) ON DUPLICATE KEY UPDATE bio = VALUES(bio), philosophy = VALUES(philosophy), video_embed_url = VALUES(video_embed_url)";

    if ($stmt = mysqli_prepare($link, $upsert_sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $bio, $philosophy, $video_embed_url);
        if (mysqli_stmt_execute($stmt)) {
            $update_success = "About section updated successfully!";
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
    <title>Manage About Section</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Manage About Section</h1>
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
                <label>Biography</label>
                <textarea name="bio" rows="5"><?php echo htmlspecialchars($bio); ?></textarea>
            </div>
            <div class="form-group">
                <label>Education Philosophy</label>
                <textarea name="philosophy" rows="3"><?php echo htmlspecialchars($philosophy); ?></textarea>
            </div>
            <div class="form-group">
                <label>Intro Video Embed URL (Optional)</label>
                <input type="text" name="video_embed_url" value="<?php echo htmlspecialchars($video_embed_url); ?>">
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
textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}
</style>