<?php
// --- Automated Setup Script ---
// Run this script once to set up the database and tables.
// For security, DELETE THIS FILE after successful setup.

// --- 1. Database Connection ---
// We need to connect to MySQL without selecting a database initially
// to check if the database exists and create it if it doesn't.

// Load credentials, but suppress the initial connection error if the DB doesn't exist.
@include_once 'php/db_config.php';

// Manually connect to the MySQL server
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (!$conn) {
    die("ERROR: Could not connect to MySQL server. " . mysqli_connect_error());
}

echo "Successfully connected to MySQL server.<br>";

// --- 2. Create Database ---
$db_name = DB_NAME;
$sql_create_db = "CREATE DATABASE IF NOT EXISTS `$db_name`";

if (mysqli_query($conn, $sql_create_db)) {
    echo "Database '$db_name' created successfully or already exists.<br>";
} else {
    die("ERROR: Could not create database. " . mysqli_error($conn));
}

// Select the database
mysqli_select_db($conn, $db_name);

// --- 3. Create Tables from Schema ---
$schema_file = 'schema.sql';
if (!file_exists($schema_file)) {
    die("ERROR: `schema.sql` file not found. Cannot create tables.");
}

$sql_schema = file_get_contents($schema_file);

// Execute all queries from the schema file
if (mysqli_multi_query($conn, $sql_schema)) {
    // Clear multi_query results
    while (mysqli_next_result($conn)) {;}
    echo "Tables created successfully from `$schema_file`.<br>";
} else {
    die("ERROR: Could not execute schema. " . mysqli_error($conn));
}

// --- 4. Create Default Admin User ---
$admin_user = 'admin';
$admin_pass = 'password123'; // Default password
$hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);

$sql_admin = "INSERT INTO admin (username, password) VALUES (?, ?)
              ON DUPLICATE KEY UPDATE password = VALUES(password)";

if ($stmt = mysqli_prepare($conn, $sql_admin)) {
    mysqli_stmt_bind_param($stmt, "ss", $admin_user, $hashed_password);
    if (mysqli_stmt_execute($stmt)) {
        echo "Default admin user created/updated successfully.<br>";
        echo "<b>Username:</b> " . htmlspecialchars($admin_user) . "<br>";
        echo "<b>Password:</b> " . htmlspecialchars($admin_pass) . "<br>";
    } else {
        echo "ERROR: Could not create admin user. " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// --- 5. Final Instructions ---
echo "<hr>";
echo "<h2>Setup Complete!</h2>";
echo "<p style='color: red; font-weight: bold;'>IMPORTANT: For security reasons, you must delete this `setup.php` file from your server immediately.</p>";

mysqli_close($conn);
?>