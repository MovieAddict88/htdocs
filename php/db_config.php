<?php
// Database configuration for InfinityFree
// Replace with your actual database credentials

define('DB_SERVER', 'sqlXXX.infinityfree.com'); // e.g., sql201.infinityfree.com
define('DB_USERNAME', 'if0_XXXXXXXX');      // e.g., if0_34567890
define('DB_PASSWORD', 'YourPassword');      // Your InfinityFree account password
define('DB_NAME', 'if0_XXXXXXXX_portfolio'); // e.g., if0_34567890_portfolio

// Attempt to connect to MySQL database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>