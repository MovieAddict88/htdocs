<?php
// --- CONTACT FORM HANDLER ---

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'db_connect.php';

    // 1. Fetch the administrator's email from the database
    $admin_email = 'your-email@example.com'; // Default fallback email
    if (isset($mysqli)) {
        $result = $mysqli->query("SELECT email FROM contact_info WHERE id = 1");
        if ($result && $result->num_rows > 0) {
            $contact_info = $result->fetch_assoc();
            $admin_email = $contact_info['email'];
        }
        $mysqli->close();
    }

    // 2. Sanitize and validate inputs
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Invalid email format
        header('Location: ../index.php?contact=error#contact');
        exit();
    }

    // 3. Prevent email header injection
    if (preg_match("/[\r\n]/", $name) || preg_match("/[\r\n]/", $email)) {
        // Injection attempt detected
        header('Location: ../index.php?contact=error#contact');
        exit();
    }

    // 4. Prepare email content
    $subject = "New Contact Form Submission from $name";
    $headers = "From: $name <$email>" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    $email_body = "You have received a new message from your website contact form.\n\n";
    $email_body .= "Name: $name\n";
    $email_body .= "Email: $email\n\n";
    $email_body .= "Message:\n$message\n";

    // 5. Send the email
    if (mail($admin_email, $subject, $email_body, $headers)) {
        // Success
        header('Location: ../index.php?contact=success#contact');
    } else {
        // Mail sending failed
        header('Location: ../index.php?contact=error#contact');
    }

} else {
    // Not a POST request, redirect to home
    header('Location: ../index.php');
    exit();
}
?>