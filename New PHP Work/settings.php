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

// Initialize variables
$errors = [];
$success = false;

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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Update profile information
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        // Validate inputs
        if (empty($username)) {
            $errors[] = "Username is required.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required.";
        }

        // Update database if no errors
        if (empty($errors)) {
            $update_query = "UPDATE users SET username = ?, email = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssi", $username, $email, $user_id);
            if ($update_stmt->execute()) {
                $success = "Profile updated successfully!";
                // Refresh user data
                $user['username'] = $username;
                $user['email'] = $email;
            } else {
                $errors[] = "Failed to update profile.";
            }
        }
    } elseif (isset($_POST['change_password'])) {
        // Change password
        $current_password = trim($_POST['current_password']);
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);

        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $errors[] = "All password fields are required.";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect.";
        }

        // Update password if no errors
        if (empty($errors)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            if ($update_stmt->execute()) {
                $success = "Password changed successfully!";
            } else {
                $errors[] = "Failed to change password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Settings</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <section>
            <h2>Update Profile</h2>
            <form action="settings.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </section>

        <section>
            <h2>Change Password</h2>
            <form action="settings.php" method="POST">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>

                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit" name="change_password">Change Password</button>
            </form>
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
$conn->close();
?>