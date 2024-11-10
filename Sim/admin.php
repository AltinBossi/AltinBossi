<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "diamond_store";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO products (name, description, price, image_url) VALUES ('Round Brilliant Cut', 'A stunning round cut diamond.', 5000, 'images/diamond1.jpg')";

if ($conn->query($sql) === TRUE) {
    echo "New product inserted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
