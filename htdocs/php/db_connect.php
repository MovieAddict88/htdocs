<?php
// Database connection details for Local Development
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'portfolio_user');
define('DB_PASSWORD', 'password');
define('DB_NAME', 'portfolio_db');

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    // In a real app, you might log this error to a file instead of outputting it
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>