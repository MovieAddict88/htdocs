<?php
// --- Database Credentials ---
// Replace with your actual database details

define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_db');
define('DB_USER', 'portfolio_user');
define('DB_PASS', 'WebAppP@ssw0rd!');

// --- Other Configurations ---

// Set the default timezone
date_default_timezone_set('UTC');

// Enable or disable error reporting for development
// In a production environment, this should be set to 0
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>