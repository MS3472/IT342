<?php
session_start();

// Include authentication functions
require_once __DIR__ . '/../includes/customer_auth.php';

// Require customer to be logged in (will redirect to login if not)
require_customer();

// Get current customer data
$customer = get_current_customer();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
</head>
<body>
    <div class="account-container">
        <header>
            <h1>My Account</h1>
            <nav>
                <a href="/public/account.php">Account</a>
                <a href="/public/orders.php">Orders</a>
                <a href="/auth_customer_logout.php">Logout</a>
            </nav>
        </header>
        
        <main>
            <h2>Welcome, <?php echo htmlspecialchars($customer['name']); ?>!</h2>
            
            <div class="account-info">
                <h3>Account Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                <p><strong>Customer ID:</strong> <?php echo htmlspecialchars($customer['id']); ?></p>
            </div>
            
            <div class="account-actions">
                <h3>Quick Actions</h3>
                <ul>
                    <li><a href="/public/orders.php">View My Orders</a></li>
                    <li><a href="/public/profile.php">Edit Profile</a></li>
                    <li><a href="/public/change-password.php">Change Password</a></li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
