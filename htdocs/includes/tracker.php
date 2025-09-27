<?php
// This script is designed to be included in other pages to log visits.
// It should not output anything to avoid breaking pages.

// Ensure config is available, but don't fail hard if it's not.
// The '@' suppresses errors if the file is included from a context where $conn is already defined.
@require_once 'config.php';

// Check if the connection object exists and is valid.
if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {

    // --- Data Collection ---
    // Use filter_input for safer data retrieval
    $ip_address = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
    $user_agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
    $page_visited = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING);

    // Basic bot check: ignore requests with empty user agents or common bot-like patterns
    if (empty($user_agent) || preg_match('/bot|crawl|slurp|spider|mediapartners/i', $user_agent)) {
        // Do not log bots to keep analytics clean.
        return;
    }

    // --- Database Insertion ---
    if ($ip_address && $user_agent && $page_visited) {
        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO analytics (ip_address, user_agent, page_visited) VALUES (?, ?, ?)");

        // Check if statement was prepared successfully
        if ($stmt) {
            $stmt->bind_param("sss", $ip_address, $user_agent, $page_visited);
            // Execute quietly. We don't want to halt page execution if logging fails.
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>