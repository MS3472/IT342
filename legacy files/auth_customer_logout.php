<?php
session_start();

// Include authentication functions
require_once __DIR__ . '/includes/customer_auth.php';

// Logout the customer
customer_logout();

// Redirect to login page
header('Location: /public/login.php');
exit;
