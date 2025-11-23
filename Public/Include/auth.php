<?php
/**
 * Authentication System for PowerHub E-commerce
 * Handles user registration, login, logout, and access control
 */

require_once __DIR__ . '/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Register a new user
 * 
 * @param string $name User's full name
 * @param string $email User's email address
 * @param string $password User's password
 * @param string $role User role (customer or admin)
 * @return array Response with success status and message
 */
function register_user($name, $email, $password, $role = 'customer') {
    // Validate input fields
    if (empty(trim($name))) {
        return [
            'success' => false,
            'message' => 'Name is required'
        ];
    }
    
    if (strlen(trim($name)) < 2) {
        return [
            'success' => false,
            'message' => 'Name must be at least 2 characters'
        ];
    }
    
    if (empty(trim($email))) {
        return [
            'success' => false,
            'message' => 'Email is required'
        ];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'success' => false,
            'message' => 'Invalid email format'
        ];
    }
    
    if (empty($password)) {
        return [
            'success' => false,
            'message' => 'Password is required'
        ];
    }
    
    if (strlen($password) < 6) {
        return [
            'success' => false,
            'message' => 'Password must be at least 6 characters'
        ];
    }
    
    // Validate role
    if (!in_array($role, ['customer', 'admin'])) {
        return [
            'success' => false,
            'message' => 'Invalid role specified'
        ];
    }
    
    try {
        $db = getDB();
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $db->error
            ];
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return [
                'success' => false,
                'message' => 'Email already registered'
            ];
        }
        $stmt->close();
        
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $db->error
            ];
        }
        
        $stmt->bind_param("ssss", $name, $email, $password_hash, $role);
        
        if (!$stmt->execute()) {
            $stmt->close();
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $stmt->error
            ];
        }
        
        $user_id = $db->insert_id;
        $stmt->close();
        
        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = trim($name);
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
        
        return [
            'success' => true,
            'message' => 'Registration successful'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'An error occurred during registration'
        ];
    }
}

/**
 * Login a user
 * 
 * @param string $email User's email address
 * @param string $password User's password
 * @return array Response with success status and message
 */
function login_user($email, $password) {
    // Validate input
    if (empty(trim($email))) {
        return [
            'success' => false,
            'message' => 'Email is required'
        ];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'success' => false,
            'message' => 'Invalid email format'
        ];
    }
    
    if (empty($password)) {
        return [
            'success' => false,
            'message' => 'Password is required'
        ];
    }
    
    try {
        $db = getDB();
        
        // Retrieve user from database
        $stmt = $db->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $db->error
            ];
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        return [
            'success' => true,
            'message' => 'Login successful'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'An error occurred during login'
        ];
    }
}

/**
 * Logout the current user
 * Clears all session data and destroys the session
 * 
 * @return void
 */
function logout_user() {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Require user to be logged in
 * Redirects to login page if not authenticated
 * 
 * @return void
 */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /Public/login.php');
        exit;
    }
}

/**
 * Require user to be logged in as admin
 * Redirects to login page if not authenticated or not admin
 * 
 * @return void
 */
function require_admin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: /Public/login.php');
        exit;
    }
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user data
 * 
 * @return array|null User data or null if not logged in
 */
function get_current_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ];
}