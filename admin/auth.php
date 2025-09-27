<?php
session_start();
require_once '../php/db_config.php';

// --- Hardcoded credentials for demonstration ---
// In a real application, fetch the hashed password from the database
$admin_username = "admin";
$admin_password_hash = password_hash("password123", PASSWORD_DEFAULT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // For now, we check against the hardcoded user.
    // Later, this will query the 'admin' table.
    if ($username === $admin_username && password_verify($password, $admin_password_hash)) {
        // Password is correct, so start a new session
        session_start();

        // Store data in session variables
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;

        // Redirect user to dashboard page
        header("location: dashboard.php");
    } else {
        // Display an error message if password is not valid
        echo "The username or password you entered was not valid.";
    }
}
?>