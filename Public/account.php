<?php
/**
 * Customer Account Page (Protected)
 * Example of using require_login() to protect a page
 */

session_start();

require_once __DIR__ . '/../Include/auth.php';

// Require user to be logged in
require_login();

// Get current user data
$user = get_current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - PowerHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Orbitron:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-cyan: #00d9ff;
            --primary-purple: #b84cff;
            --primary-blue: #4c6fff;
            --dark-bg: #0a0e27;
            --dark-card: #131937;
            --dark-hover: #1a2347;
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #00d9ff 0%, #4c6fff 100%);
            --gradient-3: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 217, 255, 0.1);
            z-index: 1000;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 28px;
            font-weight: 800;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 40px;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--primary-cyan);
        }

        .account-section {
            margin-top: 120px;
            padding: 60px 20px;
            min-height: 80vh;
        }

        .page-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .page-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 48px;
            font-weight: 800;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
        }

        .page-header p {
            color: var(--text-secondary);
            font-size: 18px;
        }

        .account-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .account-card {
            background: var(--dark-card);
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            transition: all 0.3s ease;
        }

        .account-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-cyan);
            box-shadow: 0 20px 50px rgba(0, 217, 255, 0.2);
        }

        .account-card h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 24px;
            color: var(--primary-cyan);
            margin-bottom: 25px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0, 217, 255, 0.1);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .info-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .quick-links {
            list-style: none;
        }

        .quick-links li {
            margin-bottom: 15px;
        }

        .quick-links a {
            color: var(--text-secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            padding: 10px;
            border-radius: 8px;
        }

        .quick-links a:hover {
            color: var(--primary-cyan);
            background: rgba(0, 217, 255, 0.05);
            padding-left: 15px;
        }

        .btn-logout {
            display: inline-block;
            padding: 12px 30px;
            background: var(--gradient-3);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(245, 87, 108, 0.4);
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 5px;
        }

        .menu-toggle span {
            width: 25px;
            height: 3px;
            background: var(--primary-cyan);
            border-radius: 3px;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            .nav-links {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(10, 14, 39, 0.98);
                flex-direction: column;
                padding: 20px;
                display: none;
                border-bottom: 1px solid rgba(0, 217, 255, 0.1);
            }

            .nav-links.active {
                display: flex;
            }

            .page-header h1 {
                font-size: 36px;
            }

            .account-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">PowerHub</a>
                <div class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="nav-links" id="navLinks">
                    <a href="index.php">Home</a>
                    <a href="products.php">Products</a>
                    <a href="account.php">My Account</a>
                    <a href="cart.php">Cart</a>
                </div>
            </nav>
        </div>
    </header>

    <section class="account-section">
        <div class="container">
            <div class="page-header">
                <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
                <p>Manage your PowerHub account and orders</p>
            </div>

            <div class="account-grid">
                <div class="account-card">
                    <h2>Account Information</h2>
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Account Type:</span>
                        <span class="info-value"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Customer ID:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['id']); ?></span>
                    </div>
                    <a href="/logout.php" class="btn-logout">Logout</a>
                </div>

                <div class="account-card">
                    <h2>Quick Actions</h2>
                    <ul class="quick-links">
                        <li><a href="products.php">🛍️ Browse Products</a></li>
                        <li><a href="cart.php">🛒 View Shopping Cart</a></li>
                        <li><a href="orders.php">📦 My Orders</a></li>
                        <li><a href="profile.php">👤 Edit Profile</a></li>
                        <li><a href="change-password.php">🔒 Change Password</a></li>
                    </ul>
                </div>

                <div class="account-card">
                    <h2>Recent Activity</h2>
                    <p style="color: var(--text-secondary); text-align: center; padding: 20px 0;">
                        No recent activity to display.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }
    </script>
</body>
</html>