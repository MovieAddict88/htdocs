<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Portfolio</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Sidebar for Desktop -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <!-- Logo / Profile Pic -->
            <img src="images/profile.jpg" alt="Profile Picture" class="profile-pic">
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About Me</a></li>
                <li><a href="#education">Education</a></li>
                <li><a href="#experience">Experience</a></li>
                <li><a href="#skills">Skills</a></li>
                <li><a href="#projects">Projects</a></li>
                <li><a href="#testimonials">Testimonials</a></li>
                <li><a href="#downloads">Downloads</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="admin/login.php" class="admin-login">⚙️ Admin Login</a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Bar for Mobile -->
        <header class="top-bar">
            <button class="menu-toggle" id="menu-toggle">☰</button>
            <h1 class="portfolio-title">Portfolio</h1>
            <button class="theme-toggle" id="theme-toggle">🌙</button>
        </header>

        <!-- Dynamic Sections will be loaded here -->
        <main>
            <?php include 'php/sections/home.php'; ?>
            <?php include 'php/sections/about.php'; ?>
            <?php include 'php/sections/education.php'; ?>
            <?php include 'php/sections/experience.php'; ?>
            <?php include 'php/sections/skills.php'; ?>
            <?php include 'php/sections/projects.php'; ?>
            <?php include 'php/sections/testimonials.php'; ?>
            <?php include 'php/sections/downloads.php'; ?>
            <?php include 'php/sections/contact.php'; ?>
        </main>
    </div>

    <script src="js/main.js"></script>
</body>
</html>