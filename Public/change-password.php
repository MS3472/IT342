<?php
session_start();

require_once __DIR__ . '/../Include/auth.php';
require_once __DIR__ . '/../Include/db.php';

if (!is_logged_in()) {
    header('Location: /Public/login.php?redirect=change-password.php');
    exit;
}

$user = get_logged_in_user();
$db = getDB();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($current_password === '' || $new_password === '' || $confirm_password === '') {
        $error_message = 'All fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'New password and confirmation do not match.';
    } elseif (strlen($new_password) < 8) {
        $error_message = 'New password must be at least 8 characters.';
    } else {
        // Get current password hash from DB
        $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row) {
            $error_message = 'User not found.';
        } elseif (!password_verify($current_password, $row['password_hash'])) {
            $error_message = 'Current password is incorrect.';
        } else {
            // Update password
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ? LIMIT 1");
            $stmt->bind_param("si", $new_hash, $user['id']);

            if ($stmt->execute()) {
                // Optional basic security: rotate session id after credential change
                session_regenerate_id(true);

                $success_message = 'Password updated successfully.';
            } else {
                $error_message = 'Failed to update password. Please try again.';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Change Password - PowerHub</title>

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
            --error-color: #ef4444;
            --success-color: #10b981;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            line-height: 1.6;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
        }

        .logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 26px;
            font-weight: 800;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            letter-spacing: 1px;
        }

        .nav-links { display: flex; gap: 28px; align-items: center; }
        .nav-links a { color: var(--text-secondary); text-decoration: none; font-weight: 500; }
        .nav-links a:hover { color: var(--primary-cyan); }

        .page-header {
            margin-top: 72px;
            padding: 50px 0;
            background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 100%);
            border-bottom: 1px solid rgba(0, 217, 255, 0.1);
        }

        .page-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 40px;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .content {
            padding: 50px 0 90px;
        }

        .card {
            background: var(--dark-card);
            border: 1px solid rgba(0, 217, 255, 0.12);
            border-radius: 18px;
            padding: 30px;
            max-width: 720px;
        }

        .subtitle {
            color: var(--text-secondary);
            margin-top: 10px;
        }

        .msg {
            margin: 20px 0;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid transparent;
        }

        .msg.error {
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.35);
            color: #fecaca;
        }

        .msg.success {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.35);
            color: #bbf7d0;
        }

        .form-group { margin-top: 18px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary); }
        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid rgba(0, 217, 255, 0.18);
            background: var(--dark-hover);
            color: var(--text-primary);
            font-size: 15px;
        }
        input:focus {
            outline: none;
            border-color: var(--primary-cyan);
            box-shadow: 0 0 0 3px rgba(0, 217, 255, 0.12);
        }

        .actions {
            margin-top: 22px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 18px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--gradient-2);
            color: white;
            box-shadow: 0 10px 30px rgba(0, 217, 255, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 40px rgba(0, 217, 255, 0.35);
        }

        .btn-ghost {
            background: transparent;
            color: var(--primary-cyan);
            border: 2px solid rgba(0, 217, 255, 0.45);
        }

        .btn-ghost:hover {
            background: rgba(0, 217, 255, 0.08);
        }

        .hint {
            margin-top: 12px;
            font-size: 13px;
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
                <a href="/Public/cart.php">Cart</a>
                <a href="/Public/account.php">Account</a>
                <a href="/Public/logout.php" style="color:#ff6b6b;">Logout</a>
            </div>
        </nav>
    </div>
</header>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Change Password</h1>
            <p class="subtitle">Update the password for <?= htmlspecialchars($user['email']) ?></p>
        </div>
    </section>

    <section class="content">
        <div class="container">
            <div class="card">
                <?php if ($error_message): ?>
                    <div class="msg error"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="msg success"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>

                <form method="POST" autocomplete="off">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input id="current_password" name="current_password" type="password" required />
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input id="new_password" name="new_password" type="password" required />
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input id="confirm_password" name="confirm_password" type="password" required />
                    </div>

                    <div class="actions">
                        <button class="btn btn-primary" type="submit">Update Password</button>
                        <a class="btn btn-ghost" href="/Public/account.php">Back to Account</a>
                    </div>

                    <div class="hint">
                        Tip: Use at least 8 characters. Your password is stored securely as a hash in the database.
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
</body>
</html>
