<?php
/**
 * Handles basic analytics tracking for page visits.
 *
 * This script should be included on pages you want to track.
 * It logs the visitor's IP address, user agent, and the page visited.
 *
 * Note: For a production environment, consider the privacy implications
 * and ensure compliance with regulations like GDPR. Anonymizing IPs
 * might be necessary.
 */

// The $pdo variable must be available from the file that includes this script.
if (isset($pdo)) {
    try {
        // --- Gather Visitor Information ---

        // 1. IP Address
        // Use a function to try multiple server variables for the best accuracy.
        function get_ip_address() {
            foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key){
                if (array_key_exists($key, $_SERVER) === true){
                    foreach (explode(',', $_SERVER[$key]) as $ip){
                        $ip = trim($ip); // just to be safe
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                            return $ip;
                        }
                    }
                }
            }
            return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }

        $ip_address = get_ip_address();

        // 2. User Agent
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

        // 3. Page Visited
        $page_visited = $_SERVER['REQUEST_URI'] ?? '/';

        // --- Insert into Database ---

        // To avoid spamming the database with the same user reloading the page,
        // you could implement session-based tracking to log only one visit per session.
        // For this implementation, we will log every page view.

        $sql = "INSERT INTO analytics (ip_address, user_agent, page_visited) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        // Execute the query, but don't halt the page load if it fails.
        // The website should still work even if analytics fails.
        $stmt->execute([$ip_address, $user_agent, $page_visited]);

    } catch (PDOException $e) {
        // Silently fail or log the error to a private file.
        // We don't want to break the user's experience if analytics has a problem.
        // error_log('Analytics Tracker Error: ' . $e->getMessage());
    }
}
?>