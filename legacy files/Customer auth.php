<?php
/**
 * Customer Authentication Functions
 * Skeleton implementation without database
 */

/**
 * Register a new customer
 * Validates input and stores user in session (no database yet)
 * 
 * @param string $name Customer's full name
 * @param string $email Customer's email address
 * @param string $password Customer's password
 * @return array Response with success status and message
 */
function customer_register($name, $email, $password) {
    // Validate name
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
    
    // Validate email
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
    
    // Validate password
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
    
    // In a real app, check if email already exists in database
    // For now, we'll simulate this with a simple check
    if (isset($_SESSION['registered_emails']) && in_array($email, $_SESSION['registered_emails'])) {
        return [
            'success' => false,
            'message' => 'Email already registered'
        ];
    }
    
    // Simulate registration (in real app, save to database)
    if (!isset($_SESSION['registered_emails'])) {
        $_SESSION['registered_emails'] = [];
    }
    $_SESSION['registered_emails'][] = $email;
    
    // Store fake user data (in real app, this would come from database)
    $_SESSION['registered_users'][$email] = [
        'name' => trim($name),
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'customer_id' => uniqid('cust_')
    ];
    
    // Auto-login after registration
    $_SESSION['customer_id'] = $_SESSION['registered_users'][$email]['customer_id'];
    $_SESSION['customer_name'] = trim($name);
    $_SESSION['customer_email'] = $email;
    
    return [
        'success' => true,
        'message' => 'Registration successful',
        'customer' => [
            'id' => $_SESSION['customer_id'],
            'name' => trim($name),
            'email' => $email
        ]
    ];
}

/**
 * Login a customer
 * Validates credentials and creates session (no database yet)
 * 
 * @param string $email Customer's email address
 * @param string $password Customer's password
 * @return array Response with success status and message
 */
function customer_login($email, $password) {
    // Validate email
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
    
    // Validate password
    if (empty($password)) {
        return [
            'success' => false,
            'message' => 'Password is required'
        ];
    }
    
    // In a real app, query database for user
    // For now, check our fake registered users
    if (!isset($_SESSION['registered_users'][$email])) {
        return [
            'success' => false,
            'message' => 'Invalid email or password'
        ];
    }
    
    $user = $_SESSION['registered_users'][$email];
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return [
            'success' => false,
            'message' => 'Invalid email or password'
        ];
    }
    
    // Set session variables
    $_SESSION['customer_id'] = $user['customer_id'];
    $_SESSION['customer_name'] = $user['name'];
    $_SESSION['customer_email'] = $user['email'];
    
    return [
        'success' => true,
        'message' => 'Login successful',
        'customer' => [
            'id' => $user['customer_id'],
            'name' => $user['name'],
            'email' => $user['email']
        ]
    ];
}

/**
 * Require customer to be logged in
 * Redirects to login page if not authenticated
 * 
 * @param string $redirect_url URL to redirect to if not logged in (default: /public/login.php)
 * @return void
 */
function require_customer($redirect_url = '/public/login.php') {
    if (!isset($_SESSION['customer_id'])) {
        header('Location: ' . $redirect_url);
        exit;
    }
}

/**
 * Check if customer is logged in
 * 
 * @return bool True if customer is logged in, false otherwise
 */
function is_customer_logged_in() {
    return isset($_SESSION['customer_id']);
}

/**
 * Get current customer data
 * 
 * @return array|null Customer data or null if not logged in
 */
function get_current_customer() {
    if (!is_customer_logged_in()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['customer_id'],
        'name' => $_SESSION['customer_name'],
        'email' => $_SESSION['customer_email']
    ];
}

/**
 * Logout customer
 * Clears customer session data
 * 
 * @return void
 */
function customer_logout() {
    unset($_SESSION['customer_id']);
    unset($_SESSION['customer_name']);
    unset($_SESSION['customer_email']);
}