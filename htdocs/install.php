<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$configFile = __DIR__ . '/php/db_connect.php';
$sqlFile = __DIR__ . '/database.sql';
$errors = [];
$success_message = '';
$step = 1;

// If config already exists, show a message to delete install.php
if (file_exists($configFile)) {
    $step = 'complete';
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $step === 1) {
    $db_host = trim($_POST['db_host']);
    $db_user = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']);
    $db_name = trim($_POST['db_name']);

    // 1. Connect to MySQL server
    $conn = @mysqli_connect($db_host, $db_user, $db_pass);
    if (!$conn) {
        $errors[] = "Failed to connect to MySQL server. Please check your host, username, and password. Error: " . mysqli_connect_error();
    } else {
        // 2. Create the database
        $sql_create_db = "CREATE DATABASE IF NOT EXISTS `$db_name`";
        if (!mysqli_query($conn, $sql_create_db)) {
            $errors[] = "Error creating database: " . mysqli_error($conn);
        } else {
            // 3. Write the db_connect.php file
            $config_content = "<?php
define('DB_SERVER', '$db_host');
define('DB_USERNAME', '$db_user');
define('DB_PASSWORD', '$db_pass');
define('DB_NAME', '$db_name');

\$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(\$link === false){
    die('ERROR: Could not connect. ' . mysqli_connect_error());
}
?>";
            if (!file_put_contents($configFile, $config_content)) {
                $errors[] = "Cannot write to config file (php/db_connect.php). Please check file permissions.";
            } else {
                // 4. Select the new database and import the schema
                mysqli_select_db($conn, $db_name);
                $sql_schema = file_get_contents($sqlFile);
                if (mysqli_multi_query($conn, $sql_schema)) {
                    // Clear buffer from multi_query
                    while (mysqli_next_result($conn)) {;}

                    // 5. Add a default admin user
                    $admin_user = 'admin';
                    $admin_pass = 'password';
                    $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
                    $sql_admin = "INSERT INTO users (username, password) VALUES ('$admin_user', '$hashed_password') ON DUPLICATE KEY UPDATE password = VALUES(password)";

                    if(mysqli_query($conn, $sql_admin)) {
                        $success_message = "Installation successful! Your portfolio is ready.";
                        $step = 'complete';
                    } else {
                        $errors[] = "Failed to create default admin user: " . mysqli_error($conn);
                    }
                } else {
                    $errors[] = "Error importing database schema from database.sql: " . mysqli_error($conn);
                }
            }
        }
        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Portfolio Installer</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .installer-container { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h1 { text-align: center; color: #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { display: block; width: 100%; padding: 12px; border: none; border-radius: 4px; background-color: #007bff; color: white; font-size: 16px; cursor: pointer; transition: background-color 0.2s; }
        .btn:hover { background-color: #0056b3; }
        .error-list { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .success-box { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; text-align: center; }
        .security-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px; border-radius: 4px; margin-top: 20px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="installer-container">
        <h1>Portfolio Installer</h1>

        <?php if ($step === 1): ?>
            <p>Welcome! Please enter your database details below to set up your portfolio.</p>

            <?php if (!empty($errors)): ?>
                <div class="error-list">
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="install.php" method="post">
                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>
                <div class="form-group">
                    <label for="db_user">Database Username</label>
                    <input type="text" id="db_user" name="db_user" required placeholder="e.g., root">
                </div>
                <div class="form-group">
                    <label for="db_pass">Database Password</label>
                    <input type="password" id="db_pass" name="db_pass">
                </div>
                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <input type="text" id="db_name" name="db_name" value="portfolio_db" required>
                </div>
                <button type="submit" class="btn">Install Now</button>
            </form>
        <?php elseif ($step === 'complete'): ?>
            <div class="success-box">
                <h2><?php echo $success_message ?: 'Installation Complete!'; ?></h2>
                <p>You can now <a href="index.php">view your portfolio</a> or <a href="admin/">log in to the admin panel</a>.</p>
                <p>Default admin credentials are: <strong>Username:</strong> admin / <strong>Password:</strong> password</p>
            </div>
            <div class="security-warning">
                For security reasons, please delete this <code>install.php</code> file immediately.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>