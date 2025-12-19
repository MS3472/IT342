<?php
// Public/process_checkout.php
require_once '../Include/db.php';
require_once '../Include/auth.php';

// Must be logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user = get_current_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$state = trim($_POST['state'] ?? '');
$zip = trim($_POST['zip'] ?? '');
$country = trim($_POST['country'] ?? 'US');
$phone = trim($_POST['phone'] ?? '');

// Get cart data
$cart_json = $_POST['cart_data'] ?? '[]';
$cart = json_decode($cart_json, true);

// Validate
if (empty($name) || empty($email) || empty($address) || empty($city) || empty($zip)) {
    $_SESSION['checkout_error'] = 'Please fill in all required fields';
    header('Location: checkout.php');
    exit;
}

if (empty($cart) || !is_array($cart)) {
    $_SESSION['checkout_error'] = 'Your cart is empty';
    header('Location: cart.php');
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += floatval($item['price']) * intval($item['quantity']);
}
$shipping = 10.00;
$tax = $subtotal * 0.10;
$total = $subtotal + $shipping + $tax;

// Generate order number
$order_number = 'ORD-' . strtoupper(uniqid());

// Start transaction
$conn->begin_transaction();

try {
    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders (
            user_id, order_number, total_amount, status,
            shipping_name, shipping_email, shipping_phone,
            shipping_address, shipping_city, shipping_state,
            shipping_zip, shipping_country
        ) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param(
        "isdsssssss",
        $user['id'],
        $order_number,
        $total,
        $name,
        $email,
        $phone,
        $address,
        $city,
        $state,
        $zip,
        $country
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $order_id = $conn->insert_id;
    $stmt->close();
    
    // Insert order items
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, subtotal)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    foreach ($cart as $item) {
        $item_subtotal = floatval($item['price']) * intval($item['quantity']);
        $stmt->bind_param(
            "iisdid",
            $order_id,
            $item['id'],
            $item['name'],
            $item['price'],
            $item['quantity'],
            $item_subtotal
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
    }
    $stmt->close();
    
    // Commit
    $conn->commit();
    
    // Success - store order number in session
    $_SESSION['order_success'] = true;
    $_SESSION['order_number'] = $order_number;
    
    header('Location: order_success.php');
    exit;
    
} catch (Exception $e) {
    $conn->rollback();
    error_log("Checkout error: " . $e->getMessage());
    $_SESSION['checkout_error'] = 'Order processing failed. Please try again.';
    header('Location: checkout.php');
    exit;
}