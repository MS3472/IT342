<?php
session_start();

// Set JSON response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include authentication functions
require_once __DIR__ . '/includes/customer_auth.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get POST data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Attempt registration
$result = customer_register($name, $email, $password);

// Set appropriate HTTP status code
if ($result['success']) {
    http_response_code(201); // Created
} else {
    http_response_code(400); // Bad Request
}

// Return JSON response
echo json_encode($result);
exit;