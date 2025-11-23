<?php
session_start();
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
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Navigation */
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

        .nav-links a:hover {
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

        .nav-links a:hover::after {
            width: 100%;
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

        /* Hero Section */
        .hero {
            margin-top: 80px;
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(10, 14, 39, 0.85) 0%, rgba(26, 31, 58, 0.85) 50%, rgba(10, 14, 39, 0.85) 100%), 
                        url('https://i.ibb.co/zWsjjWTC/BVLgf-JA-Imgur.png') center/cover;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 217, 255, 0.15) 0%, transparent 70%);
            top: -200px;
            right: -200px;
            animation: pulse 8s ease-in-out infinite;
        }

        .hero::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(184, 76, 255, 0.15) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            animation: pulse 6s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            text-align: center;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .hero h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 72px;
            font-weight: 800;
            margin-bottom: 30px;
            line-height: 1.1;
            background: linear-gradient(135deg, #ffffff 0%, #00d9ff 50%, #b84cff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 1s ease;
        }

        .hero p {
            font-size: 20px;
            color: var(--text-secondary);
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 1s ease 0.2s backwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--gradient-2);
            color: white;
            box-shadow: 0 10px 40px rgba(0, 217, 255, 0.4);
            animation: fadeInUp 1s ease 0.4s backwards;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 50px rgba(0, 217, 255, 0.6);
        }

        .btn-secondary {
            background: transparent;
            color: var(--primary-cyan);
            border: 2px solid var(--primary-cyan);
        }

        .btn-secondary:hover {
            background: var(--primary-cyan);
            color: var(--dark-bg);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 217, 255, 0.4);
        }

        /* Features Section */
        .features {
            padding: 120px 0;
            position: relative;
        }

        .features h2 {
            text-align: center;
            font-size: 48px;
            font-family: 'Orbitron', sans-serif;
            margin-bottom: 80px;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: var(--dark-card);
            padding: 40px 30px;
            border-radius: 20px;
            border: 1px solid rgba(0, 217, 255, 0.1);
            transition: all 0.3s ease;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary-cyan);
            box-shadow: 0 20px 50px rgba(0, 217, 255, 0.2);
        }

        .feature-icon {
            font-size: 60px;
            margin-bottom: 20px;
            filter: drop-shadow(0 0 20px rgba(0, 217, 255, 0.5));
        }

        .feature-card h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: var(--primary-cyan);
        }

        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.8;
        }

        /* Products Section */
        .products-section {
            padding: 80px 0 120px;
            background: linear-gradient(180deg, var(--dark-bg) 0%, #0f1330 100%);
        }

        .products-section h2 {
            text-align: center;
            font-size: 48px;
            font-family: 'Orbitron', sans-serif;
            margin-bottom: 60px;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
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
            transform: translateY(-15px);
            border-color: var(--primary-cyan);
            box-shadow: 0 25px 60px rgba(0, 217, 255, 0.3);
        }

        .product-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--gradient-3);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            z-index: 10;
        }

        .product-image {
            width: 100%;
            height: 280px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #1a2347 0%, #0f1330 100%);
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.1);
        }

        .product-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 217, 255, 0.15) 0%, rgba(76, 111, 255, 0.15) 100%);
            z-index: 1;
            pointer-events: none;
        }

        .product-image::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40%;
            background: linear-gradient(to top, rgba(19, 25, 55, 0.9) 0%, transparent 100%);
            z-index: 1;
            pointer-events: none;
        }

        .product-info {
            padding: 30px;
        }

        .product-info h3 {
            font-size: 22px;
            margin-bottom: 12px;
            color: var(--text-primary);
        }

        .product-info p {
            color: var(--text-secondary);
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.6;
        }

        .product-price {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-cyan);
            margin-bottom: 20px;
        }

        .product-btn {
            width: 100%;
            padding: 15px;
            background: var(--gradient-2);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .product-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 217, 255, 0.4);
        }

        /* Promo Banner */
        .promo-banner {
            background: var(--gradient-1);
            padding: 60px 20px;
            text-align: center;
            margin: 80px 0;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
        }

        .promo-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: moveGrid 20s linear infinite;
        }

        @keyframes moveGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 30px); }
        }

        .promo-content {
            position: relative;
            z-index: 2;
        }

        .promo-banner h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 42px;
            margin-bottom: 15px;
        }

        .promo-banner p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
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

        .text-center {
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                gap: 20px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .hero h1 {
                font-size: 42px;
            }

            .hero p {
                font-size: 16px;
            }

            .features h2,
            .products-section h2 {
                font-size: 36px;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .promo-banner h2 {
                font-size: 28px;
            }
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
        }
    </style>
</head>
<body>
    <header class="header" id="header">
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
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                    <a href="cart.php" class="cart-link">
                        Cart <span class="cart-badge" id="cartBadge">0</span>
                    </a>
                    <div id="authNav"></div>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Power Up Your Life</h1>
                    <p>Fast, reliable, modern charging solutions for the digital age. Experience premium portable power that keeps you connected anywhere, anytime.</p>
                    <a href="products.php" class="btn btn-primary">Shop Now</a>
                </div>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <h2>Why Choose PowerHub?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <h3>Ultra Fast Charging</h3>
                        <p>Advanced GaN technology delivers power up to 3x faster than standard chargers with intelligent power distribution.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🔋</div>
                        <h3>Massive Capacity</h3>
                        <p>High-capacity batteries up to 30,000mAh keep your devices powered for days, not hours. Charge multiple devices simultaneously.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🛡️</div>
                        <h3>Military Grade Safety</h3>
                        <p>Built-in multi-layer protection against overcharging, overheating, short circuits, and power surges for complete peace of mind.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">✈️</div>
                        <h3>Travel Ready</h3>
                        <p>Compact, lightweight design with TSA-approved capacity. Perfect companion for business trips, vacations, and adventures.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="products-section">
            <div class="container">
                <h2>Featured Products</h2>
                <div class="products-grid">
                    <div class="product-card">
                        <div class="product-badge">BESTSELLER</div>
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=800&q=80" alt="UltraCharge Pro 20K portable power bank">
                        </div>
                        <div class="product-info">
                            <h3>UltraCharge Pro 20K</h3>
                            <p>20,000mAh powerhouse with 65W fast charging, USB-C PD 3.0, and wireless charging pad. Perfect for laptops and phones.</p>
                            <div class="product-price">$89.99</div>
                            <button class="product-btn">View Product</button>
                        </div>
                    </div>

                    <div class="product-card">
                        <div class="product-badge">NEW</div>
                        <div class="product-image">
                            <img src="https://i.ibb.co/JwbgcDyQ/y6jc5ky-Imgur.png" alt="SlimCharge Mini 10K portable charger">
                        </div>
                        <div class="product-info">
                            <h3>SlimCharge Mini 10K</h3>
                            <p>Ultra-portable 10,000mAh power bank with sleek aluminum body. Pocket-sized power for everyday carry.</p>
                            <div class="product-price">$49.99</div>
                            <button class="product-btn">View Product</button>
                        </div>
                    </div>

                    <div class="product-card">
                        <div class="product-badge">PREMIUM</div>
                        <div class="product-image">
                            <img src="https://i.ibb.co/Xrb966sV/81f6-ZL6-Imgur.jpg" alt="MegaCharge Elite 30K high capacity power bank">
                        </div>
                        <div class="product-info">
                            <h3>MegaCharge Elite 30K</h3>
                            <p>Maximum capacity 30,000mAh with 100W output. Charge laptops, tablets, phones simultaneously with smart power distribution.</p>
                            <div class="product-price">$129.99</div>
                            <button class="product-btn">View Product</button>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="products.php" class="btn btn-secondary">View All Products</a>
                </div>
            </div>
        </section>

        <div class="container">
            <div class="promo-banner">
                <div class="promo-content">
                    <h2>🚀 New Launch: UltraCharge Pro 20K</h2>
                    <p>Experience next-generation charging technology. Now available with 25% launch discount!</p>
                    <a href="products.php" class="btn btn-primary">Shop Now</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>PowerHub</h4>
                    <p>Premium portable charging solutions engineered for modern life. Power up anywhere, stay connected everywhere.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="account.php">My Account</a></li>
                        <li><a href="cart.php">Shopping Cart</a></li>
                        <li><a href="login.php">Login</a></li>
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

        // Load cart count (placeholder - integrate with your cart system)
        function updateCartBadge() {
            const cartBadge = document.getElementById('cartBadge');
            // Replace with actual cart logic
            const cartCount = 0; // Get from localStorage or your cart system
            cartBadge.textContent = cartCount;
        }

        // Product button click handlers
        const productButtons = document.querySelectorAll('.product-btn');
        productButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const productName = e.target.closest('.product-card').querySelector('h3').textContent;
                console.log('Viewing product:', productName);
                // Redirect to product detail page or add to cart
                // window.location.href = 'products.php';
            });
        });

        // Initialize
        updateCartBadge();

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>