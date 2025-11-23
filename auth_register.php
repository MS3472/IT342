<?php
/**
 * Registration API Endpoint
 * Handles user registration requests and returns JSON responses
 */

session_start();

require_once __DIR__ . '/Include/auth.php';

// Set JSON response header
header('Content-Type: application/json');

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
$result = register_user($name, $email, $password);

// Set appropriate HTTP status code
if ($result['success']) {
    http_response_code(201); // Created
} else {
    http_response_code(400); // Bad Request
}

// Return JSON response
echo json_encode($result);
exit;