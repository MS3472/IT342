<?php
/**
 * PowerHub Login API Endpoint
 * Location: auth_login.php (project root)
 * 
 * Handles user login requests and returns JSON responses only.
 * No HTML output - pure JSON API.
 */

// Capture and suppress any stray output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

// Start session
session_start();

// Include authentication functions
require_once __DIR__ . '/Include/auth.php';

// Clear output buffer and log any stray output
$buffer = ob_get_clean();
if (!empty($buffer)) {
    error_log("auth_login stray output: " . $buffer);
}

// Start new output buffer for our JSON response
ob_start();

// Set JSON response header
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    ob_end_flush();
    exit;
}

// Get POST data
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Call login function
$result = login_user($email, $password);

// Set HTTP status code
if ($result['success']) {
    http_response_code(200); // OK
} else {
    http_response_code(401); // Unauthorized
}

// Output JSON response
echo json_encode($result);

// Flush output buffer
ob_end_flush();
exit;