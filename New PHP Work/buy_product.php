<?php
// Start the session
session_start();

// Include database connection
require 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the product ID is provided
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    // Fetch product details from the database
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Check if the product is in stock
        if ($product['stock'] > 0) {
            // Deduct the stock
            $new_stock = $product['stock'] - 1;
            $update_query = "UPDATE products SET stock = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ii", $new_stock, $product_id);
            $update_stmt->execute();

            // Record the purchase in the orders table
            $user_id = $_SESSION['user_id'];
            $insert_query = "INSERT INTO orders (user_id, product_id, quantity, purchase_date) VALUES (?, ?, 1, NOW())";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("ii", $user_id, $product_id);
            $insert_stmt->execute();

            // Redirect to a success page
            header("Location: purchase_success.php");
            exit();
        } else {
            // Redirect to an out-of-stock page
            header("Location: out_of_stock.php");
            exit();
        }
    } else {
        // Redirect to a product not found page
        header("Location: product_not_found.php");
        exit();
    }
} else {
    // Redirect to an error page if no product ID is provided
    header("Location: error.php");
    exit();
}

// Close the database connection
$stmt->close();
$conn->close();
?>