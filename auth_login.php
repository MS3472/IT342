<?php
/**
 * Login API Endpoint
 * Handles user login requests and returns JSON responses
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
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Attempt login
$result = login_user($email, $password);

// Set appropriate HTTP status code
if ($result['success']) {
    http_response_code(200); // OK
} else {
    http_response_code(401); // Unauthorized
}

// Return JSON response
echo json_encode($result);
exit;