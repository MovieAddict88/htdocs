<?php
// A default page title can be set here
if (!isset($page_title)) {
    $page_title = 'My Portfolio';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- PWA and Mobile Meta Tags -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#007bff">
    <link rel="apple-touch-icon" href="assets/images/icons/icon-192x192.png">

    <link rel="stylesheet" href="assets/css/style.css">
    <!-- You can add Font Awesome or other icon libraries here -->
</head>
<body class="light-mode">
    <a href="#main-content" class="skip-link">Skip to Main Content</a>

    <!-- Mobile Nav Toggle -->
    <button class="mobile-nav-toggle" id="mobile-nav-toggle" aria-label="Open navigation">
        <!-- Using a simple hamburger icon with spans -->
        <span></span>
        <span></span>
        <span></span>
    </button>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="profile">
            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/profile_placeholder.png" alt="Profile Photo" class="lazy">
            <h2>Your Name</h2>
            <p>Web Developer & Designer</p>
        </div>
        <nav>
            <ul>
                <!-- The 'active' class will be added dynamically with PHP -->
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="about.php">About Me</a></li>
                <li><a href="education.php">Education</a></li>
                <li><a href="experience.php">Experience</a></li>
                <li><a href="skills.php">Skills</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="testimonials.php">Testimonials</a></li>
                <li><a href="downloads.php">Downloads</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
        <div class="theme-switch-wrapper">
            <label class="theme-switch" for="checkbox">
                <input type="checkbox" id="checkbox" />
                <div class="slider round"></div>
            </label>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main id="main-content" class="main-content">