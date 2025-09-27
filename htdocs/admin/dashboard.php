<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

    <header class="admin-header">
        <h1>Admin Dashboard</h1>
        <a href="logout.php">Logout</a>
    </header>

    <div class="admin-container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>From here you can manage all the content on your portfolio website.</p>

        <div class="dashboard-grid">
            <a href="manage_settings.php" class="dashboard-card">
                <i class="fas fa-cogs"></i>
                <h3>Site Settings</h3>
            </a>
            <a href="manage_about.php" class="dashboard-card">
                <i class="fas fa-user"></i>
                <h3>About Me</h3>
            </a>
            <a href="manage_education.php" class="dashboard-card">
                <i class="fas fa-graduation-cap"></i>
                <h3>Education</h3>
            </a>
            <a href="manage_experience.php" class="dashboard-card">
                <i class="fas fa-briefcase"></i>
                <h3>Experience</h3>
            </a>
            <a href="manage_skills.php" class="dashboard-card">
                <i class="fas fa-star"></i>
                <h3>Skills</h3>
            </a>
            <a href="manage_projects.php" class="dashboard-card">
                <i class="fas fa-project-diagram"></i>
                <h3>Projects</h3>
            </a>
            <a href="manage_testimonials.php" class="dashboard-card">
                <i class="fas fa-comments"></i>
                <h3>Testimonials</h3>
            </a>
            <a href="manage_downloads.php" class="dashboard-card">
                <i class="fas fa-download"></i>
                <h3>Downloads</h3>
            </a>
            <a href="manage_contact.php" class="dashboard-card">
                <i class="fas fa-address-book"></i>
                <h3>Contact Info</h3>
            </a>
        </div>
    </div>

</body>
</html>