<?php
session_start();
require_once __DIR__ . '/../Include/auth.php';
$user = get_logged_in_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - PowerHub</title>
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

        /* Header styles */
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
            color: var(--primary-cyan) !important;
            font-weight: 600;
            white-space: nowrap;
            padding: 8px 16px;
            border-radius: 20px;
            background: rgba(0, 217, 255, 0.1);
            transition: all 0.3s ease;
            text-decoration: none !important;
        }

        .user-greeting:hover {
            background: rgba(0, 217, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 217, 255, 0.3);
        }

        .user-greeting::after {
            display: none;
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

        /* Page header */
        .page-header {
            margin-top: 80px;
            padding: 60px 0;
            background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 100%);
            border-bottom: 1px solid rgba(0, 217, 255, 0.1);
        }

        .page-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 48px;
            margin-bottom: 10px;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-header p {
            font-size: 18px;
            color: var(--text-secondary);
        }

        /* Products section */
        .products-section {
            padding: 60px 0 120px;
        }

        .products-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
        }

        /* Filters sidebar */
        .filters-sidebar {
            background: var(--dark-card);
            border: 1px solid rgba(0, 217, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            height: fit-content;
            position: sticky;
            top: 120px;
        }

        .filters-sidebar h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 20px;
            margin-bottom: 30px;
            color: var(--primary-cyan);
        }

        .filter-group {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(0, 217, 255, 0.1);
        }

        .filter-group:last-child {
            border-bottom: none;
        }

        .filter-group h4 {
            font-size: 16px;
            margin-bottom: 15px;
            color: var(--text-primary);
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 14px;
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        .filter-option:hover {
            color: var(--primary-cyan);
        }

        .filter-checkbox,
        .filter-radio {
            cursor: pointer;
            width: 18px;
            height: 18px;
            accent-color: var(--primary-cyan);
        }

        .btn {
            display: inline-block;
            padding: 18px 45px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-secondary {
            background: transparent;
            color: var(--primary-cyan);
            border: 2px solid var(--primary-cyan);
            padding: 12px 30px;
            font-size: 14px;
        }

        .btn-secondary:hover {
            background: var(--primary-cyan);
            color: var(--dark-bg);
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 10px 24px;
            font-size: 14px;
        }

        /* Products toolbar */
        .products-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding: 20px 30px;
            background: var(--dark-card);
            border-radius: 15px;
            border: 1px solid rgba(0, 217, 255, 0.1);
        }

        .products-count {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 16px;
        }

        .sort-select {
            padding: 12px 20px;
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            cursor: pointer;
            background: var(--dark-hover);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .sort-select:focus {
            outline: none;
            border-color: var(--primary-cyan);
            box-shadow: 0 0 0 3px rgba(0, 217, 255, 0.1);
        }

        /* Products grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: var(--dark-card);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(0, 217, 255, 0.1);
            transition: all 0.4s ease;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary-cyan);
            box-shadow: 0 20px 50px rgba(0, 217, 255, 0.3);
        }

        .product-image {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #1a2347 0%, #0f1330 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 25px;
        }

        .product-name {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-primary);
        }

        .product-description {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-price {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-cyan);
        }

        .add-to-cart-btn {
            background: var(--gradient-2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .add-to-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 217, 255, 0.4);
        }

        /* Footer */
        .footer {
            background: #070a1f;
            padding: 60px 0 30px;
            border-top: 1px solid rgba(0, 217, 255, 0.1);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h4 {
            font-size: 20px;
            margin-bottom: 20px;
            color: var(--primary-cyan);
        }

        .footer-section p {
            color: var(--text-secondary);
            line-height: 1.8;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 12px;
        }

        .footer-section a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-section a:hover {
            color: var(--primary-cyan);
            padding-left: 5px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
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

        /* Responsive */
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

            .products-layout {
                grid-template-columns: 1fr;
            }

            .filters-sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 36px;
            }

            .products-grid {
                grid-template-columns: 1fr;
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
                    <a href="/Public/products.php" class="active">Products</a>
                    <a href="/Public/cart.php" class="cart-link">
                        Cart <span class="cart-badge" id="cartBadge">0</span>
                    </a>
                    
                    <?php if ($user): ?>
                        <a href="/Public/account.php" class="user-greeting">
                            Hello, <?= htmlspecialchars($user['name']) ?> 👤
                        </a>
                        <a href="/Public/logout.php" class="logout-link">Logout</a>
                    <?php else: ?>
                        <a href="/Public/login.php">Login</a>
                        <a href="/Public/register.php">Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <section class="page-header">
            <div class="container">
                <h1>Our Products</h1>
                <p>Find the perfect power solution for your needs</p>
            </div>
        </section>

        <section class="products-section">
            <div class="container">
                <div class="products-layout">
                    <aside class="filters-sidebar">
                        <h3>Filters</h3>
                        
                        <div class="filter-group">
                            <h4>Capacity</h4>
                            <label class="filter-option">
                                <input type="checkbox" name="capacity" value="10000" class="filter-checkbox">
                                <span>10,000 mAh</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="capacity" value="20000" class="filter-checkbox">
                                <span>20,000 mAh</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="capacity" value="30000" class="filter-checkbox">
                                <span>30,000+ mAh</span>
                            </label>
                        </div>

                        <div class="filter-group">
                            <h4>Features</h4>
                            <label class="filter-option">
                                <input type="checkbox" name="feature" value="fast-charging" class="filter-checkbox">
                                <span>Fast Charging</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="feature" value="wireless" class="filter-checkbox">
                                <span>Wireless</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="feature" value="solar" class="filter-checkbox">
                                <span>Solar Panel</span>
                            </label>
                        </div>

                        <div class="filter-group">
                            <h4>Price Range</h4>
                            <label class="filter-option">
                                <input type="radio" name="price" value="0-30" class="filter-radio">
                                <span>Under $30</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="price" value="30-50" class="filter-radio">
                                <span>$30 - $50</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="price" value="50-100" class="filter-radio">
                                <span>$50 - $100</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="price" value="100+" class="filter-radio">
                                <span>$100+</span>
                            </label>
                        </div>

                        <button class="btn btn-secondary btn-sm" id="clearFilters">Clear Filters</button>
                    </aside>

                    <div class="products-main">
                        <div class="products-toolbar">
                            <div class="products-count" id="productsCount">12 Products</div>
                            <select id="sortSelect" class="sort-select">
                                <option value="featured">Featured</option>
                                <option value="price-low">Price: Low to High</option>
                                <option value="price-high">Price: High to Low</option>
                                <option value="name">Name: A-Z</option>
                            </select>
                        </div>

                        <div class="products-grid" id="productsGrid">
                            <!-- Product 1 -->
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=800&q=80" alt="Power Bank">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">UltraCharge Pro 20K</h3>
                                    <p class="product-description">20,000mAh with fast charging technology</p>
                                    <div class="product-footer">
                                        <span class="product-price">$89.99</span>
                                        <button class="add-to-cart-btn">Add to Cart</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Product 2 -->
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="https://i.ibb.co/JwbgcDyQ/y6jc5ky-Imgur.png" alt="SlimCharge Mini">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">SlimCharge Mini 10K</h3>
                                    <p class="product-description">Ultra-portable 10,000mAh power bank</p>
                                    <div class="product-footer">
                                        <span class="product-price">$49.99</span>
                                        <button class="add-to-cart-btn">Add to Cart</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Product 3 -->
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="https://i.ibb.co/Xrb966sV/81f6-ZL6-Imgur.jpg" alt="MegaCharge Elite">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">MegaCharge Elite 30K</h3>
                                    <p class="product-description">Maximum capacity 30,000mAh with 100W output</p>
                                    <div class="product-footer">
                                        <span class="product-price">$129.99</span>
                                        <button class="add-to-cart-btn">Add to Cart</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add more products as needed -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>PowerHub</h4>
                    <p>Premium portable charging solutions engineered for modern life.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="/Public/products.php">Products</a></li>
                        <li><a href="/Public/account.php">My Account</a></li>
                        <li><a href="/Public/cart.php">Shopping Cart</a></li>
                        <li><a href="/Public/login.php">Login</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Customer Support</h4>
                    <ul>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Shipping Information</a></li>
                        <li><a href="#">Returns & Warranty</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Our Technology</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 PowerHub. All rights reserved. Designed for the future.</p>
            </div>
        </div>
    </footer>

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
        
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // Update cart badge
        function updateCartBadge() {
            const cartBadge = document.getElementById('cartBadge');
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
            cartBadge.textContent = totalItems;
        }

        updateCartBadge();

        // Clear filters
        document.getElementById('clearFilters').addEventListener('click', () => {
            document.querySelectorAll('.filter-checkbox, .filter-radio').forEach(input => {
                input.checked = false;
            });
        });
    </script>
    <script src="assets/js/cart.js"></script>

</body>
</html>