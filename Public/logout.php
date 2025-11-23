<?php
/**
 * Logout Endpoint
 * Logs out the current user and redirects to login page
 */

session_start();

require_once __DIR__ . '/Include/auth.php';

// Logout the user
logout_user();

// Redirect to login page
header('Location: /Public/login.php');
exit;