<?php
session_start();

// If the user is not logged in, redirect them to the login page.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include a simple logout link
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Unset all of the session variables
    $_SESSION = [];

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_styles.css">
</head>
<body>
    <div class="sidebar">
        <h2>Portfolio Admin</h2>
        <a href="index.php" class="active">Dashboard</a>
        <a href="about.php">About Me</a>
        <a href="#">Education</a>
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
            <h1>Dashboard</h1>
            <a href="index.php?action=logout" class="logout-link">Logout</a>
        </div>

        <div class="container">
            <div class="card">
                <h2 style="text-align: center;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p style="text-align: center;">Select a section from the navigation on the left to manage your portfolio content.</p>
            </div>
        </div>
    </div>
</body>
</html>