<?php
// Database configuration
$host = 'localhost'; // Database server hostname
$dbname = 'your_database_name'; // Database name
$username = 'your_database_username'; // Database username
$password = 'your_database_password'; // Database password

// Create a connection to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Set PDO to throw exceptions on errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: Set default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Uncomment the following line to enable emulated prepared statements (if needed)
    // $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

    // Uncomment the following line to disable persistent connections (if needed)
    // $conn->setAttribute(PDO::ATTR_PERSISTENT, false);

} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}
?>