<?php

// Define the path to the configuration file
define('CONFIG_PATH', __DIR__ . '/config.php');

// Check if the configuration file exists
if (!file_exists(CONFIG_PATH)) {
    // If the config file doesn't exist, redirect to the installer
    header('Location: install/index.php');
    exit;
}

// Include the configuration file
require_once CONFIG_PATH;

// At this point, the application can assume the database connection is available.
// The main application logic will go here.

echo "<h1>Welcome to your Portfolio!</h1>";
echo "<p>The application is configured and ready to go.</p>";

?>