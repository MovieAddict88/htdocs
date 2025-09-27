<?php
session_start();
// If user is already logged in, redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../includes/config.php'; // The config file needs to be accessible

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Verify the password
            if (password_verify($password, $user['password_hash'])) {
                // Password is correct, start a new session
                session_regenerate_id();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { text-align: center; color: #333; margin-bottom: 20px; }
        form { display: flex; flex-direction: column; }
        label { margin-bottom: 8px; font-weight: bold; }
        input[type="text"], input[type="password"] { padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        button { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.2s; }
        button:hover { background-color: #0056b3; }
        .error { padding: 15px; margin-bottom: 20px; border-radius: 4px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="index.php" method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>