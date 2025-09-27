<?php
//-====================================================================
//-====================================================================
//-====================================================================
//-====================================================================
//-====================================================================
//-====================Portfolio Auto-Installer========================
//-====================================================================
//-====================================================================
//-====================================================================
//-====================================================================
//-====================================================================


// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Configuration & Security ---
define('MIN_PHP_VERSION', '7.4.0');
define('REQUIRED_EXTENSIONS', ['mysqli']);
define('CONFIG_FILE_PATH', __DIR__ . '/../config.php');
define('LOCK_FILE_PATH', __DIR__ . '/install.lock');

// Security check: if lock file or config file exists, halt.
if (file_exists(LOCK_FILE_PATH) || file_exists(CONFIG_FILE_PATH)) {
    die("Installation is complete and locked. Please delete the 'install' directory for security.");
}

// --- State Management ---
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// --- HTML Helper Functions ---
function render_header($title) {
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - Portfolio Installer</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #1C2B4A; border-bottom: 2px solid #E2B714; padding-bottom: 10px; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #1C2B4A; color: #fff; text-decoration: none; border-radius: 5px; font-size: 16px; border: none; cursor: pointer; transition: background-color 0.3s; }
        .btn:hover { background-color: #2c406b; }
        .btn-next { background-color: #E2B714; color: #1C2B4A; }
        .btn-next:hover { background-color: #f5c53d; }
        ul { list-style: none; padding: 0; }
        li { padding: 10px; border-bottom: 1px solid #eee; }
        li.success { color: green; }
        li.fail { color: red; font-weight: bold; }
        .error { background-color: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px; }
        .success-box { background-color: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px; }
        form div { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .note { font-size: 0.9em; color: #555; }
    </style>
</head>
<body>
    <div class="container">
HTML;
}

function render_footer() {
    echo <<<HTML
    </div>
</body>
</html>
HTML;
}

// --- Main Application Logic ---

switch ($step) {
    case 1:
        // Step 1: Welcome and Requirements Check
        render_header('Step 1: System Requirements');

        echo "<h1>Welcome to the Portfolio Installer!</h1>";
        echo "<p>This wizard will guide you through the setup process. Let's start by checking the server requirements.</p>";

        $php_version_ok = version_compare(PHP_VERSION, MIN_PHP_VERSION, '>=');
        $extensions_ok = true;

        echo "<h2>Requirements Check</h2><ul>";

        // Check PHP Version
        echo "<li class='" . ($php_version_ok ? 'success' : 'fail') . "'>PHP Version >= " . MIN_PHP_VERSION . " (Your version: " . PHP_VERSION . ")</li>";

        // Check Required Extensions
        foreach (REQUIRED_EXTENSIONS as $ext) {
            $is_loaded = extension_loaded($ext);
            echo "<li class='" . ($is_loaded ? 'success' : 'fail') . "'>{$ext} extension is " . ($is_loaded ? 'loaded' : 'not loaded') . "</li>";
            if (!$is_loaded) $extensions_ok = false;
        }

        echo "</ul>";

        if ($php_version_ok && $extensions_ok) {
            echo "<p class='success-box'>Congratulations! Your server meets all the requirements.</p>";
            echo "<a href='?step=2' class='btn btn-next'>Proceed to Database Setup &rarr;</a>";
        } else {
            echo "<p class='error'>Your server does not meet the minimum requirements. Please update your environment before proceeding.</p>";
        }

        render_footer();
        break;

    case 2:
        // Step 2: Database Credentials Form
        render_header('Step 2: Database Configuration');

        echo "<h1>Database Configuration</h1>";
        echo "<p>Please provide your database connection details. The installer will attempt to create the database for you.</p>";

        if (isset($_GET['error'])) {
            echo "<div class='error'>" . htmlspecialchars($_GET['error']) . "</div>";
        }

        ?>
        <form action="?step=3" method="post">
            <div>
                <label for="db_host">Database Host</label>
                <input type="text" id="db_host" name="db_host" value="localhost" required>
            </div>
            <div>
                <label for="db_name">Database Name</label>
                <input type="text" id="db_name" name="db_name" value="portfolio_db" required>
            </div>
            <div>
                <label for="db_user">Database Username</label>
                <input type="text" id="db_user" name="db_user" value="root" required>
            </div>
            <div>
                <label for="db_pass">Database Password</label>
                <input type="password" id="db_pass" name="db_pass">
            </div>
            <button type="submit" class="btn btn-next">Create Database & Install</button>
        </form>
        <?php

        render_footer();
        break;

    case 3:
        // Step 3: Process Database & Generate Config
        render_header('Step 3: Installation Progress');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?step=2');
            exit;
        }

        $db_host = $_POST['db_host'];
        $db_name = $_POST['db_name'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_pass'];

        echo "<h1>Installation in Progress...</h1><ul>";

        // 1. Connect to MySQL
        $conn = @new mysqli($db_host, $db_user, $db_pass);
        if ($conn->connect_error) {
            echo "<li class='fail'>Database Connection Failed: " . $conn->connect_error . "</li>";
            echo "</ul><a href='?step=2&error=" . urlencode('Connection failed: ' . $conn->connect_error) . "' class='btn'>&larr; Go Back and Check Credentials</a>";
            render_footer();
            die();
        }
        echo "<li class='success'>Successfully connected to MySQL server.</li>";

        // 2. Create Database
        $sql_create_db = "CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if ($conn->query($sql_create_db) === TRUE) {
            echo "<li class='success'>Database '{$db_name}' created or already exists.</li>";
            $conn->select_db($db_name);
        } else {
            echo "<li class='fail'>Error creating database: " . $conn->error . "</li>";
            echo "</ul><a href='?step=2&error=" . urlencode('Database creation failed: ' . $conn->error) . "' class='btn'>&larr; Go Back</a>";
            render_footer();
            die();
        }

        // 3. Create Tables (SQL Schema)
        $sql_schema = "
            CREATE TABLE IF NOT EXISTS `users` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `username` VARCHAR(50) NOT NULL UNIQUE,
              `password` VARCHAR(255) NOT NULL,
              `email` VARCHAR(100) NOT NULL,
              `role` VARCHAR(50) DEFAULT 'admin'
            ) ENGINE=InnoDB;

            CREATE TABLE IF NOT EXISTS `settings` (
              `setting_key` VARCHAR(50) PRIMARY KEY,
              `setting_value` TEXT
            ) ENGINE=InnoDB;

            CREATE TABLE IF NOT EXISTS `education` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `year` VARCHAR(50),
              `degree` VARCHAR(255),
              `institution` VARCHAR(255),
              `description` TEXT
            ) ENGINE=InnoDB;

            CREATE TABLE IF NOT EXISTS `experience` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `year_range` VARCHAR(50),
              `position` VARCHAR(255),
              `institution` VARCHAR(255),
              `description` TEXT
            ) ENGINE=InnoDB;

            CREATE TABLE IF NOT EXISTS `skills` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `name` VARCHAR(100),
              `type` ENUM('soft', 'hard'),
              `level` INT -- e.g., percentage 1-100
            ) ENGINE=InnoDB;

            CREATE TABLE IF NOT EXISTS `projects` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(255),
              `description` TEXT,
              `category` VARCHAR(100),
              `image_urls` TEXT, -- JSON array of image paths
              `external_link` VARCHAR(255)
            ) ENGINE=InnoDB;

            CREATE TABLE IF NOT EXISTS `testimonials` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `author` VARCHAR(100),
              `quote` TEXT,
              `role` VARCHAR(100),
              `is_visible` BOOLEAN DEFAULT TRUE
            ) ENGINE=InnoDB;

            CREATE TABLE IF NOT EXISTS `downloads` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `file_name` VARCHAR(255),
              `file_path` VARCHAR(255),
              `password_hash` VARCHAR(255),
              `download_count` INT DEFAULT 0
            ) ENGINE=InnoDB;
        ";

        if ($conn->multi_query($sql_schema)) {
            // Must clear results from multi_query
            while ($conn->next_result()) {;}
            echo "<li class='success'>Tables created successfully.</li>";
        } else {
            echo "<li class='fail'>Error creating tables: " . $conn->error . "</li>";
            echo "</ul><p class='note'>Attempting to drop the created database for a clean slate...</p>";
            $conn->query("DROP DATABASE `{$db_name}`");
            echo "<a href='?step=2&error=" . urlencode('Table creation failed: ' . $conn->error) . "' class='btn'>&larr; Go Back</a>";
            render_footer();
            die();
        }
        $conn->close();

        // 4. Generate config.php file
        $config_content = "<?php
// --- Database Configuration ---
define('DB_HOST', '{$db_host}');
define('DB_USER', '{$db_user}');
define('DB_PASS', '{$db_pass}');
define('DB_NAME', '{$db_name}');

// --- Site Settings ---
define('SITE_URL', 'http://' . \$_SERVER['HTTP_HOST']);
define('BASE_PATH', __DIR__);

// --- Create a database connection ---
\$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (\$conn->connect_error) {
    die('Connection Failed: ' . \$conn->connect_error);
}

// Set character set
\$conn->set_charset('utf8mb4');
?>";

        if (file_put_contents(CONFIG_FILE_PATH, $config_content)) {
            echo "<li class='success'><code>config.php</code> file created successfully.</li>";
        } else {
            echo "<li class='fail'>Error creating <code>config.php</code>. Please check file permissions for the <code>htdocs</code> directory.</li>";
            echo "</ul><a href='?step=2&error=" . urlencode('Could not write config.php. Check permissions.') . "' class='btn'>&larr; Go Back</a>";
            render_footer();
            die();
        }

        echo "</ul>";

        // All steps are done, redirect to completion page
        header('Location: ?step=4');
        exit;

    case 4:
        // Step 4: Completion
        render_header('Installation Complete');

        echo "<h1>Installation Successful!</h1>";
        echo "<div class='success-box'>";
        echo "Your portfolio website has been installed and configured correctly. You can now access the main page.";
        echo "</div>";

        echo "<h2>IMPORTANT: Security Warning</h2>";
        echo "<div class='error'>";
        echo "For your site's security, you <strong>MUST</strong> delete the entire <code>/install</code> directory now. Leaving it on the server is a major security risk.";
        echo "</div>";

        echo "<br><a href='../' class='btn btn-next'>Go to Your Portfolio &rarr;</a>";

        // Create a lock file to prevent re-running
        file_put_contents(LOCK_FILE_PATH, 'Installation completed on ' . date('Y-m-d H:i:s'));

        render_footer();
        break;

    default:
        header('Location: ?step=1');
        exit;
}
?>