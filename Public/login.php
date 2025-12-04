<?php
/**
 * PowerHub Login Page
 * Location: Public/login.php
 */
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PowerHub</title>
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
            --dark-bg: #0a0e27;
            --dark-card: #131937;
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --gradient-2: linear-gradient(135deg, #00d9ff 0%, #4c6fff 100%);
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

        .auth-section {
            margin-top: 80px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
        }

        .auth-container {
            max-width: 480px;
            width: 100%;
        }

        .auth-card {
            background: var(--dark-card);
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 217, 255, 0.1);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .auth-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 12px;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-header p {
            color: var(--text-secondary);
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input {
            width: 100%;
            padding: 16px 20px;
            background: rgba(10, 14, 39, 0.6);
            border: 1px solid rgba(0, 217, 255, 0.2);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-cyan);
            box-shadow: 0 0 0 3px rgba(0, 217, 255, 0.1);
            background: rgba(10, 14, 39, 0.8);
        }

        .form-group input::placeholder {
            color: rgba(160, 174, 192, 0.5);
        }

        #loginError {
            background: rgba(245, 87, 108, 0.1);
            border: 1px solid rgba(245, 87, 108, 0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            color: #ff6b6b;
            font-size: 14px;
            text-align: center;
            display: none;
        }

        .btn-submit {
            width: 100%;
            padding: 18px;
            background: var(--gradient-2);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 217, 255, 0.4);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .auth-footer {
            text-align: center;
            margin-top: 30px;
            color: var(--text-secondary);
            font-size: 15px;
        }

        .auth-footer a {
            color: var(--primary-cyan);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
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
                    <a href="/Public/login.php">Login</a>
                    <a href="/Public/register.php">Register</a>
                </div>
            </nav>
        </div>
    </header>

    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h1>Welcome Back</h1>
                    <p>Login to access your PowerHub account</p>
                </div>

                <div id="loginError"></div>

                <form id="loginForm">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn-submit">Login</button>
                </form>

                <div class="auth-footer">
                    Don't have an account? <a href="/Public/register.php">Register here</a>
                </div>
            </div>
        </div>
    </section>

    <script src="/Public/assets/js/auth.js"></script>
</body>
</html>