<?php
// Include the database configuration file
require_once 'db_config.php';

// --- DATABASE CONNECTION ---

// Create connection
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    // For a real-world application, you might want to log this error instead of showing it to the user.
    // For setup purposes, we'll die and show the error.
    die("Connection failed: " . $mysqli->connect_error);
}

// Set the character set to utf8mb4 for full Unicode support
$mysqli->set_charset("utf8mb4");

?>