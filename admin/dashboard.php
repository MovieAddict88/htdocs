<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard-container { padding: 20px; }
        .dashboard-nav a { display: block; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Admin Dashboard</h2>
        <p>Welcome, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</p>
        <p><a href="logout.php">Logout</a></p>

        <hr>

        <h3>Manage Content</h3>
        <nav class="dashboard-nav">
            <a href="manage_home.php">Manage Home Section</a>
            <a href="manage_about.php">Manage About Section</a>
            <a href="manage_education.php">Manage Education</a>
            <a href="manage_experience.php">Manage Experience</a>
            <a href="manage_skills.php">Manage Skills</a>
            <a href="manage_projects.php">Manage Projects</a>
            <a href="manage_testimonials.php">Manage Testimonials</a>
            <a href="manage_downloads.php">Manage Downloads</a>
        </nav>
    </div>
</body>
</html>