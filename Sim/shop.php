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

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='product'>
                <img src='" . $row["image_url"] . "' alt='" . $row["name"] . "'>
                <h3>" . $row["name"] . "</h3>
                <p>Price: $" . $row["price"] . "</p>
                <a href='product-detail.php?id=" . $row["id"] . "'>View Details</a>
              </div>";
    }
} else {
    echo "0 results";
}

$conn->close();
?>

