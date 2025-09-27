<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "../php/db_connect.php";

$address = $email = $facebook = $tiktok = $youtube = $instagram = "";
$update_success = "";

// Fetch current data
$sql = "SELECT address, email, facebook, tiktok, youtube, instagram FROM contact_info LIMIT 1";
$result = mysqli_query($link, $sql);
$contact = mysqli_fetch_assoc($result);
if ($contact) {
    $address = $contact['address'];
    $email = $contact['email'];
    $facebook = $contact['facebook'];
    $tiktok = $contact['tiktok'];
    $youtube = $contact['youtube'];
    $instagram = $contact['instagram'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = trim($_POST["address"]);
    $email = trim($_POST["email"]);
    $facebook = trim($_POST["facebook"]);
    $tiktok = trim($_POST["tiktok"]);
    $youtube = trim($_POST["youtube"]);
    $instagram = trim($_POST["instagram"]);

    // Use an UPSERT approach (UPDATE or INSERT)
    $upsert_sql = "INSERT INTO contact_info (id, address, email, facebook, tiktok, youtube, instagram) VALUES (1, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE address = VALUES(address), email = VALUES(email), facebook = VALUES(facebook), tiktok = VALUES(tiktok), youtube = VALUES(youtube), instagram = VALUES(instagram)";

    if ($stmt = mysqli_prepare($link, $upsert_sql)) {
        mysqli_stmt_bind_param($stmt, "ssssss", $address, $email, $facebook, $tiktok, $youtube, $instagram);
        if (mysqli_stmt_execute($stmt)) {
            $update_success = "Contact info updated successfully!";
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
    <title>Manage Contact Info</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="crud_style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Manage Contact Information</h1>
        <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

    <div class="crud-container">
        <div class="crud-form">
            <h3>Update Contact Details</h3>
            <?php if ($update_success): ?>
                <div class="alert alert-success"><?php echo $update_success; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="form-group">
                    <label>Facebook URL</label>
                    <input type="text" name="facebook" value="<?php echo htmlspecialchars($facebook); ?>">
                </div>
                <div class="form-group">
                    <label>TikTok URL</label>
                    <input type="text" name="tiktok" value="<?php echo htmlspecialchars($tiktok); ?>">
                </div>
                <div class="form-group">
                    <label>YouTube URL</label>
                    <input type="text" name="youtube" value="<?php echo htmlspecialchars($youtube); ?>">
                </div>
                <div class="form-group">
                    <label>Instagram URL</label>
                    <input type="text" name="instagram" value="<?php echo htmlspecialchars($instagram); ?>">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn-primary" value="Save Changes">
                </div>
            </form>
        </div>
    </div>
</body>
</html>