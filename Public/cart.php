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
    <title>Shopping Cart - PowerHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Orbitron:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

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

        .container { max-width: 1400px; margin: 0 auto; padding: 0 20px; }

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
        .nav-links a.active { color: var(--primary-cyan); }

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
        .nav-links a.active::after { width: 100%; }

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

        .user-greeting::after { display: none; }

        .logout-link { color: #ff6b6b !important; }
        .logout-link:hover { color: #ff5252 !important; }

        .cart-link { position: relative; }

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

        .cart-section { padding: 60px 0 120px; min-height: 60vh; }

        .cart-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; }

        .cart-items {
            background: var(--dark-card);
            border: 1px solid rgba(0, 217, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
        }

        .cart-items h2 {
            font-size: 24px;
            margin-bottom: 30px;
            color: var(--primary-cyan);
            font-family: 'Orbitron', sans-serif;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 20px;
            padding: 25px 0;
            border-bottom: 1px solid rgba(0, 217, 255, 0.1);
        }

        .cart-item:last-child { border-bottom: none; }

        .cart-item-image {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #1a2347 0%, #0f1330 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            overflow: hidden;
        }

        .cart-item-image img { width: 100%; height: 100%; object-fit: cover; }

        .cart-item-details { display: flex; flex-direction: column; justify-content: space-between; }

        .cart-item-details h3 { font-size: 20px; margin-bottom: 8px; color: var(--text-primary); }

        .cart-item-description { font-size: 14px; color: var(--text-secondary); margin-bottom: 15px; }

        .cart-item-price { font-size: 22px; font-weight: 700; color: var(--primary-cyan); }

        .cart-item-actions { display: flex; align-items: center; gap: 15px; }

        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--dark-hover);
            border-radius: 10px;
            padding: 5px;
            border: 1px solid rgba(0, 217, 255, 0.2);
        }

        .quantity-btn {
            background: none;
            border: none;
            color: var(--primary-cyan);
            cursor: pointer;
            font-size: 18px;
            font-weight: 700;
            padding: 5px 12px;
            transition: all 0.3s ease;
            border-radius: 6px;
        }

        .quantity-btn:hover { background: rgba(0, 217, 255, 0.1); }

        .quantity-input {
            width: 50px;
            text-align: center;
            border: none;
            background: transparent;
            color: var(--text-primary);
            font-size: 16px;
            font-weight: 600;
        }

        .remove-item-btn {
            background: none;
            border: none;
            color: var(--error-color);
            cursor: pointer;
            font-size: 14px;
            text-decoration: underline;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .remove-item-btn:hover { color: #dc2626; }

        .cart-item-right { display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; }

        .cart-item-total { font-size: 24px; font-weight: 700; color: var(--text-primary); }

        .empty-cart { text-align: center; padding: 80px 40px; }
        .empty-cart-icon { font-size: 80px; margin-bottom: 20px; opacity: 0.5; }
        .empty-cart h2 { font-size: 28px; margin-bottom: 15px; color: var(--text-secondary); }
        .empty-cart p { font-size: 16px; color: var(--text-secondary); margin-bottom: 30px; }

        .cart-summary {
            background: var(--dark-card);
            border: 1px solid rgba(0, 217, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            height: fit-content;
            position: sticky;
            top: 120px;
        }

        .cart-summary h3 {
            font-size: 24px;
            margin-bottom: 25px;
            color: var(--primary-cyan);
            font-family: 'Orbitron', sans-serif;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: var(--text-secondary);
            font-size: 15px;
        }

        .summary-row span:last-child { font-weight: 600; color: var(--text-primary); }

        .cart-summary hr {
            border: none;
            border-top: 1px solid rgba(0, 217, 255, 0.2);
            margin: 20px 0;
        }

        .summary-total { font-size: 20px; font-weight: 700; color: var(--text-primary); margin-top: 20px; }
        .summary-total span:last-child { color: var(--primary-cyan); font-size: 28px; }

        .btn {
            display: inline-block;
            padding: 16px 35px;
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

        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .btn-secondary {
            background: transparent;
            color: var(--primary-cyan);
            border: 2px solid var(--primary-cyan);
            width: 100%;
            margin-top: 15px;
        }

        .btn-secondary:hover { background: rgba(0, 217, 255, 0.1); }

        .btn-lg { padding: 18px 40px; font-size: 17px; }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(10px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .modal.active { display: flex; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .modal-content {
            background: var(--dark-card);
            border: 1px solid rgba(0, 217, 255, 0.3);
            border-radius: 24px;
            padding: 50px 40px;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 30px 80px rgba(0, 217, 255, 0.3);
            position: relative;
            animation: slideUp 0.4s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 28px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .modal-close:hover { background: rgba(0, 217, 255, 0.1); color: var(--primary-cyan); }

        .modal-header { text-align: center; margin-bottom: 30px; }
        .modal-icon { font-size: 60px; margin-bottom: 20px; }

        .modal-header h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 28px;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
        }

        .modal-header p { color: var(--text-secondary); font-size: 15px; line-height: 1.6; }

        .modal-actions { display: flex; flex-direction: column; gap: 15px; margin-top: 30px; }

        .modal-btn {
            padding: 18px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            text-align: center;
            display: block;
        }

        .modal-btn-primary {
            background: var(--gradient-2);
            color: white;
            box-shadow: 0 10px 30px rgba(0, 217, 255, 0.3);
        }

        .modal-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 217, 255, 0.5);
        }

        .modal-btn-secondary {
            background: transparent;
            color: var(--primary-cyan);
            border: 2px solid var(--primary-cyan);
        }

        .modal-btn-secondary:hover { background: rgba(0, 217, 255, 0.1); }

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

        .footer-section h4 { font-size: 20px; margin-bottom: 20px; color: var(--primary-cyan); }
        .footer-section p { color: var(--text-secondary); line-height: 1.8; }
        .footer-section ul { list-style: none; }
        .footer-section ul li { margin-bottom: 12px; }
        .footer-section a { color: var(--text-secondary); text-decoration: none; transition: all 0.3s ease; }
        .footer-section a:hover { color: var(--primary-cyan); padding-left: 5px; }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
        }

        .menu-toggle { display: none; flex-direction: column; cursor: pointer; gap: 5px; }
        .menu-toggle span { width: 25px; height: 3px; background: var(--primary-cyan); border-radius: 3px; transition: all 0.3s ease; }

        @media (max-width: 968px) {
            .menu-toggle { display: flex; }
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
            .nav-links.active { display: flex; }
            .cart-layout { grid-template-columns: 1fr; }
            .cart-summary { position: static; }
        }

        @media (max-width: 768px) {
            .page-header h1 { font-size: 36px; }
            .cart-item { grid-template-columns: 80px 1fr; gap: 15px; }
            .cart-item-right {
                grid-column: 1 / -1;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                margin-top: 15px;
            }
            .modal-content { padding: 40px 30px; }
        }

        @media (max-width: 480px) {
            .cart-items, .cart-summary { padding: 20px; }
            .empty-cart { padding: 60px 20px; }
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
                <a href="/Public/cart.php" class="cart-link active">
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
            <h1>Shopping Cart</h1>
        </div>
    </section>

    <section class="cart-section">
        <div class="container">
            <div class="cart-layout">
                <div class="cart-items">
                    <h2>Your Items</h2>
                    <div id="cartItemsContainer"></div>
                </div>

                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="cartSubtotal">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span id="cartShipping">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (10%):</span>
                        <span id="cartTax">$0.00</span>
                    </div>
                    <hr>
                    <div class="summary-row summary-total">
                        <span>Total:</span>
                        <span id="cartTotal">$0.00</span>
                    </div>
                    <button class="btn btn-primary btn-lg" id="proceedCheckoutBtn">
                        Proceed to Checkout
                    </button>
                    <a href="/Public/products.php" class="btn btn-secondary">Continue Shopping</a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Authentication Required Modal -->
<div id="authModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeAuthModal()">&times;</button>
        <div class="modal-header">
            <div class="modal-icon">🔒</div>
            <h2>Login Required</h2>
            <p>Please log in or create an account to proceed with checkout. It only takes a minute!</p>
        </div>
        <div class="modal-actions">
            <a href="/Public/login.php?redirect=checkout.php" class="modal-btn modal-btn-primary">
                🚀 Login to Continue
            </a>
            <a href="/Public/register.php?redirect=checkout.php" class="modal-btn modal-btn-secondary">
                ✨ Create New Account
            </a>
        </div>
    </div>
</div>

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
        if (window.scrollY > 50) header.classList.add('scrolled');
        else header.classList.remove('scrolled');
    });

    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');
    if (menuToggle) {
        menuToggle.addEventListener('click', () => navLinks.classList.toggle('active'));
    }

    function getCart() {
        return JSON.parse(localStorage.getItem('cart') || '[]');
    }

    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartBadge();
    }

    function updateCartBadge() {
        const cartBadge = document.getElementById('cartBadge');
        if (!cartBadge) return;
        const cart = getCart();
        const totalItems = cart.reduce((sum, item) => sum + (Number(item.quantity) || 1), 0);
        cartBadge.textContent = totalItems;
    }

    function setMoney(id, val) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = `$${val.toFixed(2)}`;
    }

    function updateSummaryFromCart(cart) {
        const subtotal = cart.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 1), 0);
        const shipping = subtotal > 0 ? 10.00 : 0.00;
        const tax = subtotal * 0.10;
        const total = subtotal + shipping + tax;

        setMoney('cartSubtotal', subtotal);
        setMoney('cartShipping', shipping);
        setMoney('cartTax', tax);
        setMoney('cartTotal', total);

        const proceedBtn = document.getElementById('proceedCheckoutBtn');
        if (proceedBtn) proceedBtn.disabled = cart.length === 0;
    }

    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        if (!container) return;

        const cart = getCart();

        if (cart.length === 0) {
            container.innerHTML = `
                <div class="empty-cart">
                    <div class="empty-cart-icon">🛒</div>
                    <h2>Your cart is empty</h2>
                    <p>Add some amazing power banks to get started!</p>
                    <a href="/Public/products.php" class="btn btn-primary">Browse Products</a>
                </div>
            `;
            updateSummaryFromCart([]);
            return;
        }

        container.innerHTML = cart.map((item, index) => {
            const qty = Number(item.quantity) || 1;
            const price = Number(item.price) || 0;
            const lineTotal = price * qty;
            const imgHtml = item.image
                ? `<img src="${item.image}" alt="${item.name}">`
                : `🔋`;

            return `
                <div class="cart-item">
                    <div class="cart-item-image">${imgHtml}</div>

                    <div class="cart-item-details">
                        <div>
                            <h3>${item.name}</h3>
                            <div class="cart-item-description">${item.description || ''}</div>
                            <div class="cart-item-price">$${price.toFixed(2)}</div>
                        </div>

                        <div class="cart-item-actions">
                            <div class="cart-item-quantity">
                                <button class="quantity-btn" data-action="dec" data-index="${index}">−</button>
                                <input class="quantity-input" value="${qty}" readonly />
                                <button class="quantity-btn" data-action="inc" data-index="${index}">+</button>
                            </div>
                            <button class="remove-item-btn" data-action="remove" data-index="${index}">Remove</button>
                        </div>
                    </div>

                    <div class="cart-item-right">
                        <div class="cart-item-total">$${lineTotal.toFixed(2)}</div>
                    </div>
                </div>
            `;
        }).join('');

        updateSummaryFromCart(cart);
    }

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;

        const action = btn.dataset.action;
        const index = Number(btn.dataset.index);

        const cart = getCart();
        const item = cart[index];
        if (!item) return;

        if (action === 'inc') item.quantity = (Number(item.quantity) || 1) + 1;
        if (action === 'dec') item.quantity = Math.max(1, (Number(item.quantity) || 1) - 1);
        if (action === 'remove') cart.splice(index, 1);

        saveCart(cart);
        renderCart();
    });

    function openAuthModal() {
        const modal = document.getElementById('authModal');
        if (modal) modal.classList.add('active');
    }

    function closeAuthModal() {
        const modal = document.getElementById('authModal');
        if (modal) modal.classList.remove('active');
    }

    const proceedBtn = document.getElementById('proceedCheckoutBtn');
    if (proceedBtn) {
        proceedBtn.addEventListener('click', () => {
            const cart = getCart();
            if (cart.length === 0) return;

            const isLoggedIn = <?php echo $user ? 'true' : 'false'; ?>;
            if (!isLoggedIn) {
                openAuthModal();
                return;
            }

            window.location.href = '/Public/checkout.php';
        });
    }

    // Init
    updateCartBadge();
    renderCart();
</script>
</body>