<?php
session_start();
// If the user is not logged in, redirect to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></h1>
        <a href="logout.php" class="btn-secondary">Logout</a>
    </div>

    <div class="dashboard-wrapper">
        <h2>Portfolio Content Management</h2>
        <div class="dashboard-menu">
            <a href="manage_home.php" class="menu-item">Manage Home</a>
            <a href="manage_about.php" class="menu-item">Manage About Me</a>
            <a href="manage_education.php" class="menu-item">Manage Education</a>
            <a href="manage_experience.php" class="menu-item">Manage Experience</a>
            <a href="manage_skills.php" class="menu-item">Manage Skills</a>
            <a href="manage_projects.php" class="menu-item">Manage Projects</a>
            <a href="manage_testimonials.php" class="menu-item">Manage Testimonials</a>
            <a href="manage_downloads.php" class="menu-item">Manage Downloads</a>
            <a href="manage_contact.php" class="menu-item">Manage Contact Info</a>
            <a href="view_messages.php" class="menu-item">View Messages</a>
        </div>
    </div>
</body>
</html>