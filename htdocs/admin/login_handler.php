<?php
session_start();
require_once '../php/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header('Location: index.php?error=Please fill in all fields');
        exit();
    }

    // Prepare a statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a new session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            header('Location: dashboard.php');
            exit();
        } else {
            // Invalid password
            header('Location: index.php?error=Invalid username or password');
            exit();
        }
    } else {
        // No user found
        header('Location: index.php?error=Invalid username or password');
        exit();
    }

    $stmt->close();
    $mysqli->close();
} else {
    // Not a POST request
    header('Location: index.php');
    exit();
}
?>