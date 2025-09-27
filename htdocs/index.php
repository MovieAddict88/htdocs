<?php
// Define the path to the configuration file
$config_file = 'includes/config.php';

// Check if the config file exists. If not, redirect to the installation script.
if (!file_exists($config_file)) {
    header('Location: install.php');
    exit;
}

// Include the configuration file
require_once $config_file;

// Set the page title for the header
$page_title = 'Home | Your Name';

// Include the header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h1>Welcome! I'm [Your Name]</h1>
        <p class="tagline">A Passionate Web Developer Ready to Build the Future</p>
        <div class="hero-buttons">
            <a href="downloads/resume.pdf" class="btn btn-primary">Download Resume</a>
            <a href="contact.php" class="btn btn-secondary">Get In Touch</a>
        </div>
    </div>
    <div class="animated-background">
        <!-- This can be a canvas animation or a CSS-only animation -->
    </div>
</section>

<?php
// Include the footer
include 'includes/footer.php';
?>