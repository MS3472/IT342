<?php
/**
 * PowerHub Reusable Header
 * Location: Public/header.php
 * Include this in all Public/ pages with: include __DIR__ . '/header.php';
 */

// Only start session if one hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include authentication functions - check multiple paths
$auth_paths = [
    __DIR__ . '/../Include/auth.php',
    __DIR__ . '/Include/auth.php',
    dirname(__DIR__) . '/Include/auth.php'
];

$auth_loaded = false;
foreach ($auth_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $auth_loaded = true;
        break;
    }
}

// Fallback if auth.php not found
if (!$auth_loaded) {
    function get_logged_in_user() {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'] ?? null,
                'name' => $_SESSION['user_name'] ?? 'User',
                'email' => $_SESSION['user_email'] ?? '',
                'role' => $_SESSION['user_role'] ?? 'customer'
            ];
        }
        return null;
    }
}

// Get logged-in user (null if not logged in)
$user = get_logged_in_user();

// Debug: Uncomment to see session data
// echo "<!-- DEBUG Session: "; print_r($_SESSION); echo " -->";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerHub - Premium Portable Chargers</title>
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
            --error-color: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 217, 255, 0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .header.scrolled {
            background: rgba(10, 14, 39, 0.98);
            box-shadow: 0 10px 30px rgba(0, 217, 255, 0.1);
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
            text-shadow: 0 0 30px rgba(0, 217, 255, 0.5);
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
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--primary-cyan);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-2);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .user-greeting {
            color: var(--primary-cyan);
            font-weight: 600;
            margin-right: 5px;
            white-space: nowrap;
        }

        .logout-link {
            color: #ff6b6b !important;
        }

        .logout-link:hover {
            color: #ff5252 !important;
        }

        .cart-link {
            position: relative;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -12px;
            background: var(--gradient-3);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }

        /* Mobile Menu Toggle */
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
            transition: all 0.3s ease;
        }

        @media (max-width: 968px) {
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
                gap: 20px;
            }

            .nav-links.active {
                display: flex;
            }

            .user-greeting {
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <header class="header" id="header">
        <div class="container">
            <nav class="nav">
                <a href="/Public/index.php" class="logo">PowerHub</a>
                
                <div class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                
                <div class="nav-links" id="navLinks">
                    <a href="/Public/index.php">Home</a>
                    <a href="/Public/products.php">Products</a>
                    <a href="/Public/cart.php" class="cart-link">
                        Cart <span class="cart-badge" id="cartBadge">0</span>
                    </a>
                    
                    <?php if ($user): ?>
                        <span class="user-greeting">Hello, <?= htmlspecialchars($user['name']) ?></span>
                        <a href="/Public/account.php">Account</a>
                        <a href="/Public/logout.php" class="logout-link">Logout</a>
                    <?php else: ?>
                        <a href="/Public/login.php">Login</a>
                        <a href="/Public/register.php">Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <script>
        // Header scroll effect
        const header = document.getElementById('header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }

        // Update cart badge from localStorage
        function updateCartBadge() {
            const cartBadge = document.getElementById('cartBadge');
            if (cartBadge) {
                const cart = JSON.parse(localStorage.getItem('cart') || '[]');
                const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
                cartBadge.textContent = totalItems;
            }
        }

        // Initialize cart badge
        updateCartBadge();
        
        // Listen for storage changes (cart updates)
        window.addEventListener('storage', updateCartBadge);
    </script>