<?php
// --- Database Setup Script ---
// This script creates the database and all necessary tables.
// It should be run once during the initial setup.

echo "<h1>Database Setup</h1>";

// 1. Check for the configuration file and load it.
$config_file = __DIR__ . '/includes/config.php';
if (!file_exists($config_file)) {
    die(
        '<p style="color:red;"><strong>Configuration Error:</strong> The configuration file <code>includes/config.php</code> was not found.</p>' .
        '<p>Please create it by copying <code>includes/config.sample.php</code> to <code>includes/config.php</code> and filling in your database credentials before running this setup.</p>'
    );
}
require_once $config_file;
echo "<p>✅ Configuration file loaded successfully.</p>";

// --- Main Execution ---

try {
    // 2. Connect to MySQL Server (using credentials from config.php)
    $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Connected to MySQL server successfully.</p>";

    // 3. Create Database if it doesn't exist
    $conn->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✅ Database '" . DB_NAME . "' created or already exists.</p>";

    // 4. Select the new database for subsequent operations
    $conn->exec("USE `" . DB_NAME . "`");
    echo "<p>✅ Switched to database '" . DB_NAME . "'.</p>";

    // 5. Create Tables (if they don't exist)
    echo "<h3>🚀 Starting table creation...</h3>";

    $queries = [
        "users" => "CREATE TABLE IF NOT EXISTS `users` (
          `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `username` VARCHAR(50) NOT NULL UNIQUE,
          `password_hash` VARCHAR(255) NOT NULL,
          `role` VARCHAR(20) NOT NULL DEFAULT 'admin',
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "about" => "CREATE TABLE IF NOT EXISTS `about` (
          `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `bio` TEXT,
          `philosophy` TEXT,
          `video_url` VARCHAR(255),
          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "education" => "CREATE TABLE IF NOT EXISTS `education` (
          `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `year` VARCHAR(50) NOT NULL,
          `degree` VARCHAR(255) NOT NULL,
          `institution` VARCHAR(255) NOT NULL,
          `description` TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "experience" => "CREATE TABLE IF NOT EXISTS `experience` (
          `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `year_range` VARCHAR(50) NOT NULL,
          `position` VARCHAR(255) NOT NULL,
          `institution` VARCHAR(255) NOT NULL,
          `description` TEXT,
          `media_url` VARCHAR(255)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "skills" => "CREATE TABLE IF NOT EXISTS `skills` (
          `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `category` VARCHAR(50) NOT NULL,
          `name` VARCHAR(100) NOT NULL,
          `level` INT(3) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "projects" => "CREATE TABLE IF NOT EXISTS `projects` (
          `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `title` VARCHAR(255) NOT NULL,
          `description` TEXT,
          `category` VARCHAR(100),
          `image_url` VARCHAR(255),
          `video_url` VARCHAR(255),
          `external_link` VARCHAR(255)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "testimonials" => "CREATE TABLE IF NOT EXISTS `testimonials` (
          `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `author` VARCHAR(100) NOT NULL,
          `role` VARCHAR(100),
          `content` TEXT NOT NULL,
          `media_url` VARCHAR(255)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "downloads" => "CREATE TABLE IF NOT EXISTS `downloads` (
          `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `file_name` VARCHAR(255) NOT NULL,
          `file_path` VARCHAR(255) NOT NULL,
          `password` VARCHAR(255),
          `download_count` INT(11) NOT NULL DEFAULT 0,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "contact_submissions" => "CREATE TABLE IF NOT EXISTS `contact_submissions` (
          `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(100) NOT NULL,
          `email` VARCHAR(100) NOT NULL,
          `message` TEXT NOT NULL,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "analytics" => "CREATE TABLE IF NOT EXISTS `analytics` (
          `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `ip_address` VARCHAR(45),
          `user_agent` VARCHAR(255),
          `page_visited` VARCHAR(255),
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ];

    foreach ($queries as $table_name => $sql) {
        $conn->exec($sql);
        echo "-> Table '{$table_name}' created or already exists.<br>";
    }

    echo "<h3>🎉 All tables created successfully!</h3>";
    echo "<p style='color:red;'><strong>IMPORTANT:</strong> For security, please delete this <code>setup.php</code> file now that the setup is complete.</p>";

} catch(PDOException $e) {
    // Catch and display any errors
    die("<p style='color:red;'><strong>Database Error:</strong> " . $e->getMessage() . "</p>");
}

// Close the connection
$conn = null;
?>