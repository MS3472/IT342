<?php
// Public/checkout.php
session_start();
require_once __DIR__ . '/../Include/auth.php';

if (!is_logged_in()) {
    header('Location: /Public/login.php?redirect=checkout.php');
    exit;
}

$user = get_current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Checkout - PowerHub</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Orbitron:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary-cyan: #00d9ff;
            --primary-blue: #4c6fff;
            --dark-bg: #0a0e27;
            --dark-card: #131937;
            --dark-hover: #1a2347;
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --gradient-2: linear-gradient(135deg, #00d9ff 0%, #4c6fff 100%);
            --gradient-3: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --error-color: #ef4444;
            --success-color: #10b981;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }
        .container { max-width: 1,400px; margin: 0 auto; padding: 0 20px; }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 217, 255, 0.1);
            z-index: 1000;
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
            text-decoration: none;
            letter-spacing: 1px;
        }
        .nav-links { display: flex; gap: 40px; align-items: center; }
        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s ease;
        }
        .nav-links a:hover { color: var(--primary-cyan); }

        .cart-link { position: relative; }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -12px;
            background: var(--gradient-3);
            color: #fff;
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
        }

        /* Layout */
        .checkout-section { padding: 60px 0 120px; }
        .checkout-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; }
        @media (max-width: 968px) { .checkout-layout { grid-template-columns: 1fr; } }

        .card {
            background: var(--dark-card);
            border: 1px solid rgba(0, 217, 255, 0.1);
            border-radius: 20px;
            padding: 35px;
        }
        .card h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 24px;
            margin-bottom: 20px;
            color: var(--primary-cyan);
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }

        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 12px;
            background: var(--dark-hover);
            color: var(--text-primary);
            font-size: 15px;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-cyan);
            box-shadow: 0 0 0 3px rgba(0, 217, 255, 0.12);
        }
        .note {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.25);
            color: #f59e0b;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        /* Summary */
        .summary { position: sticky; top: 120px; height: fit-content; }
        @media (max-width: 968px) { .summary { position: static; } }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 217, 255, 0.08);
        }
        .summary-item:last-child { border-bottom: none; }
        .summary-label { color: var(--text-secondary); }
        .summary-value { font-weight: 700; }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid rgba(0, 217, 255, 0.2);
            font-size: 18px;
            font-weight: 800;
        }
        .summary-total .summary-value { color: var(--primary-cyan); font-size: 26px; }

        .checkout-items {
            margin-bottom: 18px;
        }
        .checkout-line {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 217, 255, 0.08);
        }
        .checkout-line:last-child { border-bottom: none; }
        .checkout-line small { color: var(--text-secondary); display: block; margin-top: 2px; }

        /* Buttons */
        .btn {
            width: 100%;
            padding: 16px 18px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-primary {
            background: var(--gradient-2);
            color: #fff;
            box-shadow: 0 10px 30px rgba(0, 217, 255, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 217, 255, 0.45);
        }
        .btn-secondary {
            margin-top: 12px;
            background: transparent;
            color: var(--primary-cyan);
            border: 2px solid var(--primary-cyan);
        }
        .btn-secondary:hover { background: rgba(0, 217, 255, 0.08); }

        /* Success state */
        .success-wrap {
            max-width: 720px;
            margin: 80px auto 0;
            text-align: center;
        }
        .success-icon {
            font-size: 86px;
            margin-bottom: 14px;
            color: var(--success-color);
        }
        .success-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 34px;
            margin-bottom: 10px;
            color: var(--success-color);
        }
        .success-text { color: var(--text-secondary); margin-bottom: 22px; }
        .success-box {
            background: rgba(0, 217, 255, 0.08);
            border: 1px solid rgba(0, 217, 255, 0.25);
            border-radius: 16px;
            padding: 18px 16px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            color: var(--primary-cyan);
            font-weight: 800;
            display: inline-block;
            margin-bottom: 26px;
        }

        /* Footer */
        .footer {
            background: #070a1f;
            padding: 40px 0;
            border-top: 1px solid rgba(0, 217, 255, 0.1);
            text-align: center;
            color: var(--text-secondary);
        }
    </style>
</head>

<body>
<header class="header">
    <div class="container">
        <nav class="nav">
            <a href="/Public/index.php" class="logo">PowerHub</a>
            <div class="nav-links">
                <a href="/Public/index.php">Home</a>
                <a href="/Public/products.php">Products</a>
                <a href="/Public/cart.php" class="cart-link">
                    Cart <span class="cart-badge" id="cartBadge">0</span>
                </a>
                <a href="/Public/account.php">Account</a>
                <a href="/Public/logout.php" style="color:#ff6b6b;">Logout</a>
            </div>
        </nav>
    </div>
</header>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Checkout</h1>
        </div>
    </section>

    <section class="checkout-section">
        <div class="container">

            <!-- Demo success view -->
            <div id="successView" class="success-wrap" style="display:none;">
                <div class="success-icon">✓</div>
                <div class="success-title">Order Placed (Demo)</div>
                <p class="success-text">No payment was processed. Your cart was cleared.</p>
                <div class="success-box" id="orderNumberBox">Order #</div>

                <button class="btn btn-primary" onclick="window.location.href='/Public/products.php'">
                    Continue Shopping
                </button>
                <button class="btn btn-secondary" onclick="window.location.href='/Public/cart.php'">
                    Back to Cart
                </button>
            </div>

            <!-- Checkout form view -->
            <div id="checkoutView">
                <div class="checkout-layout">
                    <div class="card">
                        <h2>Shipping Information</h2>

                        <div class="note">
                            <strong>Demo checkout:</strong> Fill anything in. Clicking “Place Order” will clear your cart and show a success screen.
                        </div>

                        <form id="checkoutForm">
                            <div class="form-group">
                                <label for="fullName">Full Name *</label>
                                <input class="form-control" type="text" id="fullName" name="fullName" required
                                       value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input class="form-control" type="email" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="address">Street Address *</label>
                                <input class="form-control" type="text" id="address" name="address" required placeholder="123 Main St">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">City *</label>
                                    <input class="form-control" type="text" id="city" name="city" required>
                                </div>
                                <div class="form-group">
                                    <label for="zip">ZIP *</label>
                                    <input class="form-control" type="text" id="zip" name="zip" required>
                                </div>
                            </div>

                            <h2 style="margin-top: 26px;">Payment Information</h2>

                            <div class="note" style="margin-top: 12px;">
                                Enter any card details. This is a demo only.
                            </div>

                            <div class="form-group">
                                <label for="cardNumber">Card Number *</label>
                                <input class="form-control" type="text" id="cardNumber" required placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry">Expiry (MM/YY) *</label>
                                    <input class="form-control" type="text" id="expiry" required placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV *</label>
                                    <input class="form-control" type="text" id="cvv" required placeholder="123" maxlength="4">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" id="submitBtn">Place Order</button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='/Public/cart.php'">Back to Cart</button>
                        </form>
                    </div>

                    <div class="card summary">
                        <h2>Order Summary</h2>

                        <div class="checkout-items" id="checkoutItems"></div>

                        <div class="summary-item">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value" id="checkoutSubtotal">$0.00</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Shipping</span>
                            <span class="summary-value" id="checkoutShipping">$10.00</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Tax (10%)</span>
                            <span class="summary-value" id="checkoutTax">$0.00</span>
                        </div>

                        <div class="summary-total">
                            <span>Total</span>
                            <span class="summary-value" id="checkoutTotal">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <p>&copy; 2024 PowerHub. All rights reserved.</p>
    </div>
</footer>

<script>
    // Cart storage key matches your cart.php
    function getCart() {
        return JSON.parse(localStorage.getItem('cart') || '[]');
    }
    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartBadge();
    }
    function clearCart() {
        localStorage.removeItem('cart');
        updateCartBadge();
    }

    function updateCartBadge() {
        const badge = document.getElementById('cartBadge');
        if (!badge) return;
        const cart = getCart();
        const count = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
        badge.textContent = count;
    }

    function money(n) {
        return '$' + Number(n || 0).toFixed(2);
    }

    function renderSummary() {
        const cart = getCart();
        const itemsEl = document.getElementById('checkoutItems');

        if (!cart.length) {
            window.location.href = '/Public/cart.php';
            return;
        }

        let subtotal = 0;
        itemsEl.innerHTML = cart.map(item => {
            const qty = Number(item.quantity || 0);
            const price = Number(item.price || 0);
            const lineTotal = qty * price;
            subtotal += lineTotal;

            return `
              <div class="checkout-line">
                <div>
                  <div style="font-weight:700;">${escapeHtml(item.name || 'Item')}</div>
                  <small>Qty: ${qty}</small>
                </div>
                <div style="font-weight:800;">${money(lineTotal)}</div>
              </div>
            `;
        }).join('');

        const shipping = 10.00;
        const tax = subtotal * 0.10;
        const total = subtotal + shipping + tax;

        document.getElementById('checkoutSubtotal').textContent = money(subtotal);
        document.getElementById('checkoutShipping').textContent = money(shipping);
        document.getElementById('checkoutTax').textContent = money(tax);
        document.getElementById('checkoutTotal').textContent = money(total);
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, s => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[s]));
    }

    // Input formatting
    document.getElementById('cardNumber').addEventListener('input', (e) => {
        let v = e.target.value.replace(/\s/g, '');
        e.target.value = (v.match(/.{1,4}/g) || []).join(' ').substring(0, 19);
    });
    document.getElementById('expiry').addEventListener('input', (e) => {
        let v = e.target.value.replace(/\D/g, '').substring(0, 4);
        if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2);
        e.target.value = v;
    });
    document.getElementById('cvv').addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
    });

    // Demo submit handler: clear cart + show success
    document.getElementById('checkoutForm').addEventListener('submit', (e) => {
        e.preventDefault();

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = 'Processing...';

        const orderNum = 'ORD-' + Math.random().toString(16).slice(2, 10).toUpperCase();

        setTimeout(() => {
            clearCart();

            document.getElementById('orderNumberBox').textContent = 'Order #' + orderNum;
            document.getElementById('checkoutView').style.display = 'none';
            document.getElementById('successView').style.display = 'block';
        }, 700);
    });

    // Init
    updateCartBadge();
    renderSummary();
</script>
</body>
</html>
