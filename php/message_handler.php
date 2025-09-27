<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
    $message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING));

    if (empty($name) || empty($email) || empty($message)) {
        echo "Please fill out all fields.";
        exit;
    }

    if (!$email) {
        echo "Invalid email format.";
        exit;
    }

    // --- DATABASE INSERTION LOGIC ---
    // In a real application, you would insert the data into the 'messages' table.
    /*
    $sql = "INSERT INTO messages (name, email, message) VALUES (?, ?, ?)";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "sss", $param_name, $param_email, $param_message);

        $param_name = $name;
        $param_email = $email;
        $param_message = $message;

        if(mysqli_stmt_execute($stmt)){
            echo "Message sent successfully. Thank you!";
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
    */

    // For this demonstration, we'll just show a success message.
    echo "Thank you for your message, " . htmlspecialchars($name) . "! I will get back to you shortly.";

} else {
    echo "There was a problem with your submission. Please try again.";
}
?>