<?php
// Simple Installation Script for Portfolio Website

// --- Configuration ---
$config_file = 'includes/config.php';
$lock_file = 'install.lock';
$error_message = '';
$success_message = '';

// --- Check if already installed ---
if (file_exists($lock_file)) {
    die("Installation is complete. Please delete the 'install.php' file for security reasons.");
}

// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $admin_user = $_POST['admin_user'];
    $admin_pass = $_POST['admin_pass'];

    // --- 1. Test Database Connection ---
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // --- 2. Write config.php file ---
        $config_content = "<?php
// Database Configuration
define('DB_HOST', '{$db_host}');
define('DB_NAME', '{$db_name}');
define('DB_USER', '{$db_user}');
define('DB_PASS', '{$db_pass}');

// Establish connection
\$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (\$conn->connect_error) {
    die('Connection failed: ' . \$conn->connect_error);
}
?>";
        if (!file_put_contents($config_file, $config_content)) {
            throw new Exception("Error: Could not write to config.php. Please check file permissions.");
        }

        // --- 3. Create Database Tables ---
        $sql_schema = "
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE about (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bio TEXT,
            philosophy TEXT,
            video_url VARCHAR(255),
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );

        CREATE TABLE education (
            id INT AUTO_INCREMENT PRIMARY KEY,
            year VARCHAR(255),
            degree VARCHAR(255),
            institution VARCHAR(255),
            description TEXT
        );

        CREATE TABLE experience (
            id INT AUTO_INCREMENT PRIMARY KEY,
            year_range VARCHAR(255),
            position VARCHAR(255),
            institution VARCHAR(255),
            description TEXT,
            media_url VARCHAR(255)
        );

        CREATE TABLE skills (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category VARCHAR(50),
            name VARCHAR(255),
            level INT
        );

        CREATE TABLE projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255),
            description TEXT,
            category VARCHAR(255),
            image_url VARCHAR(255),
            video_url VARCHAR(255),
            external_link VARCHAR(255)
        );

        CREATE TABLE testimonials (
            id INT AUTO_INCREMENT PRIMARY KEY,
            author VARCHAR(255),
            role VARCHAR(255),
            content TEXT,
            media_url VARCHAR(255)
        );

        CREATE TABLE downloads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            file_name VARCHAR(255),
            file_path VARCHAR(255),
            password VARCHAR(255),
            download_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE contact_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            email VARCHAR(255),
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE analytics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45),
            user_agent VARCHAR(255),
            page_visited VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        ";

        if (!$conn->multi_query($sql_schema)) {
            throw new Exception("Error creating tables: " . $conn->error);
        }

        // Clear multi_query results
        while ($conn->next_result()) {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        }

        // --- 4. Insert Admin User ---
        $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
        $stmt->bind_param("ss", $admin_user, $hashed_password);
        if (!$stmt->execute()) {
             throw new Exception("Error creating admin user: " . $stmt->error);
        }
        $stmt->close();

        $conn->close();

        // --- 5. Finalize Installation ---
        touch($lock_file);
        $success_message = "Installation successful! The database and config file have been created. Please delete this install.php file now.";

    } catch (Exception $e) {
        $error_message = "An error occurred: " . $e->getMessage();
        // Clean up config file on error
        if (file_exists($config_file)) {
            unlink($config_file);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Installation</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; }
        form { display: flex; flex-direction: column; }
        label { margin-bottom: 8px; font-weight: bold; }
        input[type="text"], input[type="password"] { padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        button { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.2s; }
        button:hover { background-color: #0056b3; }
        .message { padding: 15px; margin-top: 20px; border-radius: 4px; text-align: center; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Portfolio Setup</h1>

        <?php if (!empty($success_message)): ?>
            <div class="message success">
                <?php echo $success_message; ?>
                <p><a href="index.php">Go to your new website!</a></p>
            </div>
        <?php else: ?>
            <?php if (!empty($error_message)): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="install.php" method="post">
                <p>Enter your database details and create an admin account.</p>

                <label for="db_host">Database Host</label>
                <input type="text" id="db_host" name="db_host" required value="localhost">

                <label for="db_name">Database Name</label>
                <input type="text" id="db_name" name="db_name" required>

                <label for="db_user">Database User</label>
                <input type="text" id="db_user" name="db_user" required>

                <label for="db_pass">Database Password</label>
                <input type="password" id="db_pass" name="db_pass">

                <hr style="margin: 20px 0; border: 1px solid #eee;">

                <label for="admin_user">Admin Username</label>
                <input type="text" id="admin_user" name="admin_user" required value="admin">

                <label for="admin_pass">Admin Password</label>
                <input type="password" id="admin_pass" name="admin_pass" required>

                <button type="submit">Install Now</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>