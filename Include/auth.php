<?php
/**
 * PowerHub Authentication System
 * Location: Include/auth.php
 * 
 * This file contains all authentication functions for the PowerHub e-commerce system.
 * No HTML output - only returns arrays with success/failure status.
 */

// Prevent direct access
if (!defined('AUTH_INCLUDED')) {
    define('AUTH_INCLUDED', true);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/db.php';

/**
 * Register a new user
 * 
 * @param string $name User's full name
 * @param string $email User's email address
 * @param string $password User's password (will be hashed)
 * @param string $role User role: 'customer' or 'admin'
 * @return array ['success' => bool, 'message' => string]
 */
function register_user($name, $email, $password, $role = 'customer') {
    // Validate name
    $name = trim($name);
    if (empty($name)) {
        return ['success' => false, 'message' => 'Name is required'];
    }
    if (strlen($name) < 2) {
        return ['success' => false, 'message' => 'Name must be at least 2 characters'];
    }
    
    // Validate email
    $email = trim($email);
    if (empty($email)) {
        return ['success' => false, 'message' => 'Email is required'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    // Validate password
    if (empty($password)) {
        return ['success' => false, 'message' => 'Password is required'];
    }
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }
    
    // Validate role
    if (!in_array($role, ['customer', 'admin'])) {
        return ['success' => false, 'message' => 'Invalid role specified'];
    }
    
    try {
        $db = getDB();
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            error_log("Register prepare failed: " . $db->error);
            return ['success' => false, 'message' => 'Database error occurred'];
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Email already registered'];
        }
        $stmt->close();
        
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            error_log("Register insert prepare failed: " . $db->error);
            return ['success' => false, 'message' => 'Database error occurred'];
        }
        
        $stmt->bind_param("ssss", $name, $email, $password_hash, $role);
        
        if (!$stmt->execute()) {
            error_log("Register execute failed: " . $stmt->error);
            $stmt->close();
            return ['success' => false, 'message' => 'Registration failed'];
        }
        
        $user_id = $db->insert_id;
        $stmt->close();
        
        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
        
        return ['success' => true, 'message' => 'Registration successful'];
        
    } catch (Exception $e) {
        error_log("Register exception: " . $e->getMessage());
        return ['success' => false, 'message' => 'An error occurred during registration'];
    }
}

/**
 * Login a user
 * 
 * @param string $email User's email address
 * @param string $password User's password
 * @return array ['success' => bool, 'message' => string]
 */
function login_user($email, $password) {
    // Validate email
    $email = trim($email);
    if (empty($email)) {
        return ['success' => false, 'message' => 'Email is required'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    // Validate password
    if (empty($password)) {
        return ['success' => false, 'message' => 'Password is required'];
    }
    
    try {
        $db = getDB();
        
        // Retrieve user from database
        $stmt = $db->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
        if (!$stmt) {
            error_log("Login prepare failed: " . $db->error);
            return ['success' => false, 'message' => 'Database error occurred'];
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        return ['success' => true, 'message' => 'Login successful'];
        
    } catch (Exception $e) {
        error_log("Login exception: " . $e->getMessage());
        return ['success' => false, 'message' => 'An error occurred during login'];
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
 * @param string $redirect_url URL to redirect to if not logged in
 * @return void
 */
function require_login($redirect_url = '/Public/login.php') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . $redirect_url);
        exit;
    }
}

/**
 * Require user to be logged in as admin
 * Redirects to login page if not authenticated or not admin
 * 
 * @param string $redirect_url URL to redirect to if not admin
 * @return void
 */
function require_admin($redirect_url = '/Public/login.php') {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: ' . $redirect_url);
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
 * Get current logged-in user data
 * 
 * @return array|null User data array or null if not logged in
 */
function get_logged_in_user() {
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