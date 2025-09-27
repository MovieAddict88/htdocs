<?php
// Check if the configuration file exists. If not, provide instructions.
$config_file = __DIR__ . '/config.php';
if (!file_exists($config_file)) {
    die(
        '<h1>Configuration Error</h1>' .
        '<p>The configuration file <code>config.php</code> was not found.</p>' .
        '<p>Please create it by copying <code>config.sample.php</code> to <code>config.php</code> and filling in your database credentials.</p>' .
        '<p><strong>Do not commit <code>config.php</code> to version control.</strong></p>'
    );
}
require_once $config_file;

/**
 * Establishes a database connection using PDO.
 *
 * @return PDO|null Returns a PDO object on successful connection, or null on failure.
 */
function get_db_connection() {
    // DSN (Data Source Name)
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';

    // PDO options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
    ];

    try {
        // Create a new PDO instance
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // In a real application, you would log this error instead of displaying it
        // For development purposes, we'll display the error.
        // In production, you might want to show a generic error message.
        die('Connection failed: ' . $e->getMessage());
    }
}

// Example of how to use the connection function:
/*
    $pdo = get_db_connection();
    if ($pdo) {
        echo "Connection successful!";
        // You can now use the $pdo object to perform queries
    }
*/
?>