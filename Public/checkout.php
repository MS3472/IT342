<?php
session_start();

// Include authentication functions
$auth_file = __DIR__ . '/../Include/auth.php';
if (!file_exists($auth_file)) {
    // If auth.php doesn't exist, redirect to login
    header('Location: /Public/login.php');
    exit;
}

require_once $auth_file;

// Require user to be logged in to access checkout
require_login();

// Get current user data
$user = get_logged_in_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - PowerHub</title>
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
            --success-color: #10b981;
            --error-color: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Header */
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

        .nav-links a:hover {
            color: var(--primary-cyan);
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
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-welcome {
            margin-top: 15px;
            color: var(--text-secondary);
            font-size: 16px;
        }

        .user-welcome span {
            color: var(--primary-cyan);
            font-weight: 600;
        }

        /* Checkout Section */
        .checkout-section {
            padding: 60px 0 120px;
        }

        .checkout-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        /* Checkout Form */
        .checkout-form {
            background: var(--dark-card);
            border: 1px solid rgba(0, 217, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-section h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 24px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(0, 217, 255, 0.2);
            color: var(--primary-cyan);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 10px;
            color: var(--text-primary);
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 15px 18px;
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            transition: all 0.3s ease;
            background: var(--dark-hover);
            color: var(--text-primary);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-cyan);
            box-shadow: 0 0 0 3px rgba(0, 217, 255, 0.1);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
        }

        .form-control:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        select.form-control {
            cursor: pointer;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Checkout Summary */
        .checkout-summary {
            background: var(--dark-card);
            border: 1px solid rgba(0, 217, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            height: fit-content;
            position: sticky;
            top: 120px;
        }

        .checkout-summary h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 24px;
            margin-bottom: 25px;
            color: var(--primary-cyan);
        }

        .checkout-items {
            margin-bottom: 25px;
        }

        .checkout-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0, 217, 255, 0.1);
        }

        .checkout-item:last-child {
            border-bottom: none;
        }

        .checkout-item-info {
            flex: 1;
        }

        .checkout-item-name {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .checkout-item-qty {
            color: var(--text-secondary);
            font-size: 13px;
        }

        .checkout-item-price {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 15px;
        }

        .checkout-summary hr {
            border: none;
            border-top: 1px solid rgba(0, 217, 255, 0.2);
            margin: 20px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: var(--text-secondary);
            font-size: 15px;
        }

        .summary-row span:last-child {
            font-weight: 600;
            color: var(--text-primary);
        }

        .summary-total {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin-top: 20px;
        }

        .summary-total span:last-child {
            color: var(--primary-cyan);
            font-size: 28px;
        }

        /* Button */
        .btn {
            display: inline-block;
            padding: 18px 40px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-align: center;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: var(--gradient-2);
            color: white;
            box-shadow: 0 10px 30px rgba(0, 217, 255, 0.3);
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 217, 255, 0.5);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-lg {
            padding: 20px 45px;
            font-size: 18px;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        /* Message */
        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .message-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .message-error {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
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

        /* Responsive */
        @media (max-width: 968px) {
            .checkout-layout {
                grid-template-columns: 1fr;
            }

            .checkout-summary {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 36px;
            }

            .checkout-form {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .nav-links {
                gap: 20px;
            }
        }

        @media (max-width: 480px) {
            .checkout-summary {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">PowerHub</a>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="products.php">Products</a>
                    <a href="account.php">My Account</a>
                    <a href="cart.php" class="cart-link">
                        Cart <span class="cart-badge" id="cartBadge">0</span>
                    </a>
                    <a href="/logout.php" style="color: var(--error-color);">Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <section class="page-header">
            <div class="container">
                <h1>Secure Checkout</h1>
                <p class="user-welcome">Welcome, <span><?php echo htmlspecialchars($user['name']); ?></span>! Complete your order below.</p>
            </div>
        </section>

        <section class="checkout-section">
            <div class="container">
                <div class="checkout-layout">
                    <div class="checkout-form">
                        <form id="checkoutForm">
                            <div class="form-section">
                                <h3>📍 Shipping Information</h3>
                                
                                <div class="form-group">
                                    <label for="fullName">Full Name *</label>
                                    <input type="text" id="fullName" name="fullName" required class="form-control" 
                                           value="<?php echo htmlspecialchars($user['name']); ?>" placeholder="John Doe">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" id="email" name="email" required class="form-control" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" 
                                           placeholder="john@example.com" disabled>
                                    <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($user['email']); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <input type="tel" id="phone" name="phone" required class="form-control" placeholder="+1 (555) 123-4567">
                                </div>

                                <div class="form-group">
                                    <label for="address">Street Address *</label>
                                    <input type="text" id="address" name="address" required class="form-control" placeholder="123 Main Street">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="city">City *</label>
                                        <input type="text" id="city" name="city" required class="form-control" placeholder="New York">
                                    </div>
                                    <div class="form-group">
                                        <label for="state">State *</label>
                                        <input type="text" id="state" name="state" required class="form-control" placeholder="NY">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="zip">ZIP Code *</label>
                                        <input type="text" id="zip" name="zip" required class="form-control" placeholder="10001">
                                    </div>
                                    <div class="form-group">
                                        <label for="country">Country *</label>
                                        <select id="country" name="country" required class="form-control">
                                            <option value="US">United States</option>
                                            <option value="CA">Canada</option>
                                            <option value="UK">United Kingdom</option>
                                            <option value="AU">Australia</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3>💳 Payment Information</h3>
                                
                                <div class="form-group">
                                    <label for="cardName">Name on Card *</label>
                                    <input type="text" id="cardName" name="cardName" required class="form-control" 
                                           value="<?php echo htmlspecialchars($user['name']); ?>" placeholder="John Doe">
                                </div>

                                <div class="form-group">
                                    <label for="cardNumber">Card Number *</label>
                                    <input type="text" id="cardNumber" name="cardNumber" 
                                           placeholder="1234 5678 9012 3456" required class="form-control"
                                           maxlength="19">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="expiry">Expiry Date *</label>
                                        <input type="text" id="expiry" name="expiry" 
                                               placeholder="MM/YY" required class="form-control"
                                               maxlength="5">
                                    </div>
                                    <div class="form-group">
                                        <label for="cvv">CVV *</label>
                                        <input type="text" id="cvv" name="cvv" 
                                               placeholder="123" required class="form-control"
                                               maxlength="4">
                                    </div>
                                </div>
                            </div>

                            <div id="checkoutMessage" class="message"></div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                💳 Place Order Securely
                            </button>
                        </form>
                    </div>

                    <div class="checkout-summary">
                        <h3>Order Summary</h3>
                        <div id="checkoutItems" class="checkout-items">
                            <!-- Items loaded via JS -->
                        </div>
                        <hr>
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="checkoutSubtotal">$0.00</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span id="checkoutShipping">$10.00</span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (10%):</span>
                            <span id="checkoutTax">$0.00</span>
                        </div>
                        <hr>
                        <div class="summary-row summary-total">
                            <span>Total:</span>
                            <span id="checkoutTotal">$0.00</span>
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

    <script src="assets/js/main.js"></script>
    <script>
        // Initialize checkout page (user is already authenticated)
        displayCheckoutSummary();
        
        // Display checkout summary
        function displayCheckoutSummary() {
            const cart = getCart();
            const container = document.getElementById('checkoutItems');
            
            if (cart.length === 0) {
                window.location.href = 'cart.php';
                return;
            }

            container.innerHTML = '';
            let subtotal = 0;

            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                const itemHTML = `
                    <div class="checkout-item">
                        <div class="checkout-item-info">
                            <div class="checkout-item-name">${item.name}</div>
                            <div class="checkout-item-qty">Quantity: ${item.quantity}</div>
                        </div>
                        <div class="checkout-item-price">$${itemTotal.toFixed(2)}</div>
                    </div>
                `;
                container.innerHTML += itemHTML;
            });

            const shipping = 10.00;
            const tax = subtotal * 0.10;
            const total = subtotal + shipping + tax;

            document.getElementById('checkoutSubtotal').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('checkoutShipping').textContent = '$' + shipping.toFixed(2);
            document.getElementById('checkoutTax').textContent = '$' + tax.toFixed(2);
            document.getElementById('checkoutTotal').textContent = '$' + total.toFixed(2);
        }

        // Handle checkout form submission
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            handleCheckoutSubmit();
        });

        function handleCheckoutSubmit() {
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            const messageEl = document.getElementById('checkoutMessage');
            
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Processing Order...';
            
            // Simulate order processing
            setTimeout(() => {
                // Clear cart
                localStorage.removeItem('cart');
                updateCartBadge();
                
                // Show success message
                messageEl.textContent = '✓ Order placed successfully! Redirecting to your account...';
                messageEl.className = 'message message-success';
                messageEl.style.display = 'block';
                
                // Redirect to account page
                setTimeout(() => {
                    window.location.href = 'account.php';
                }, 2000);
            }, 2000);
        }

        // Format card number as user types
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formatted;
        });

        // Format expiry date
        document.getElementById('expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        // Only allow numbers for CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        function getCart() {
            return JSON.parse(localStorage.getItem('cart') || '[]');
        }
    </script>
</body>
</html>