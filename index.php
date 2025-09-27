<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portfolio</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/sections.css">
    <link rel="manifest" href="manifest.json">
    <!-- Add theme color for PWA -->
    <meta name="theme-color" content="#007bff">
</head>
<body>
    <script>
        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('Service Worker registered with scope:', registration.scope);
                    })
                    .catch(error => {
                        console.error('Service Worker registration failed:', error);
                    });
            });
        }
    </script>
    <div id="app-container">
        <!-- Sidebar Navigation (for Desktop) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="assets/images/profile.jpg" alt="Profile Photo" class="profile-photo" loading="lazy">
                <h3>Your Name</h3>
            </div>
            <nav class="sidebar-nav">
                <a href="#home" class="nav-link active">Home</a>
                <a href="#about" class="nav-link">About Me</a>
                <a href="#education" class="nav-link">Education</a>
                <a href="#experience" class="nav-link">Experience</a>
                <a href="#skills" class="nav-link">Skills</a>
                <a href="#projects" class="nav-link">Projects</a>
                <a href="#testimonials" class="nav-link">Testimonials</a>
                <a href="#downloads" class="nav-link">Downloads</a>
                <a href="#contact" class="nav-link">Contact</a>
            </nav>
            <div class="sidebar-footer">
                <p>&copy; <?php echo date('Y'); ?> Your Name</p>
            </div>
        </aside>

        <!-- Mobile Navigation Toggle -->
        <button class="mobile-nav-toggle" aria-label="Open navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Main Content Area -->
        <main class="main-content">
            <?php
            // Establish database connection and track visit
            require_once 'includes/database.php';
            $pdo = get_db_connection();
            require_once 'includes/analytics_tracker.php';

            // Include all the content sections
            include 'sections/home.php';
            include 'sections/about.php';
            include 'sections/education.php';
            // The rest of the sections will be included here later
            ?>
        </main>

        <!-- Light/Dark Mode Toggle -->
        <div class="theme-switcher">
            <label for="theme-toggle">Dark Mode</label>
            <input type="checkbox" id="theme-toggle" class="theme-toggle-checkbox">
            <label for="theme-toggle" class="theme-toggle-label"></label>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>