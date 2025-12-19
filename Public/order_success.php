<?php
// Public/order_success.php
require_once '../Include/db.php';
require_once '../Include/auth.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['order_success']) || !isset($_SESSION['order_number'])) {
    header('Location: index.php');
    exit;
}

$order_number = $_SESSION['order_number'];
$user = get_current_user();

// Clear session flags
unset($_SESSION['order_success']);
unset($_SESSION['order_number']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Success - PowerBank Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .success-container { max-width: 600px; margin: 100px auto; text-align: center; background: #fff; padding: 60px 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success-icon { font-size: 80px; margin-bottom: 20px; }
        .success-container h1 { color: #28a745; margin-bottom: 20px; }
        .order-number { background: #f8f9fa; padding: 20px; border-radius: 4px; margin: 30px 0; font-family: monospace; font-size: 1.2rem; font-weight: 700; }
        .btn-primary { display: inline-block; padding: 12px 30px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
        .btn-primary:hover { background: #0056b3; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">PowerBank Pro</a>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="products.php">Products</a>
                    <a href="cart.php">Cart (<span id="cartBadge">0</span>)</a>
                    <a href="account.php">Account</a>
                    <a href="logout.php">Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="success-container">
            <div class="success-icon">✓</div>
            <h1>Order Placed Successfully!</h1>
            <p>Thank you for your purchase, <strong><?php echo htmlspecialchars($user['name']); ?></strong>!</p>
            <div class="order-number">
                Order #<?php echo htmlspecialchars($order_number); ?>
            </div>
            <p>We've sent a confirmation email to <strong><?php echo htmlspecialchars($user['email']); ?></strong></p>
            <a href="account.php" class="btn-primary">View My Orders</a>
            <p style="margin-top: 20px;">
                <a href="products.php" style="color: #007bff;">Continue Shopping</a>
            </p>
        </div>
    </main>

    <footer style="background: #333; color: white; padding: 20px; text-align: center; margin-top: 50px;">
        <p>&copy; 2024 PowerBank Pro. All rights reserved.</p>
    </footer>

    <script src="assets/js/cart.js"></script>
    <script>
        // Clear cart after successful order
        clearCart();
    </script>
</body>
</html>