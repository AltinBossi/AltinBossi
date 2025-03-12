<?php
require 'db.php';

// Example: Fetch data from the database
$query = "SELECT * FROM users";
$stmt = $conn->query($query);

while ($row = $stmt->fetch()) {
    echo "Username: " . htmlspecialchars($row['username']) . "<br>";
}
?>