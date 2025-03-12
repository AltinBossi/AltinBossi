<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require 'db_connection.php';

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Redirect if user not found
    header("Location: logout.php");
    exit();
}

// Fetch user's recent orders (example for an e-commerce system)
$order_query = "SELECT orders.id, products.name, orders.quantity, orders.purchase_date 
                FROM orders 
                JOIN products ON orders.product_id = products.id 
                WHERE orders.user_id = ? 
                ORDER BY orders.purchase_date DESC 
                LIMIT 5";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Your Recent Orders</h2>
            <?php if ($order_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $order_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['name']); ?></td>
                                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($order['purchase_date']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No recent orders found.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Account Summary</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Joined:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 Your Company. All rights reserved.</p>
    </footer>
</body>
</html>

<?php
// Close the database connection
$stmt->close();
$order_stmt->close();
$conn->close();
?>