<?php
session_start();

// If the user is not logged in, redirect them to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Optional: Add a check for session timeout
// For example, if the session has been inactive for more than 30 minutes
$inactive = 1800; // 30 minutes
if (isset($_SESSION['last_action']) && (time() - $_SESSION['last_action'] > $inactive)) {
    session_unset();     // unset $_SESSION variable for the run-time
    session_destroy();   // destroy session data in storage
    header('Location: index.php?error=Session expired. Please log in again.');
    exit();
}
$_SESSION['last_action'] = time(); // update last activity time stamp
?>