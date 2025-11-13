<?php
require_once __DIR__ . '/../config/config.php';

// Destroy session
session_destroy();

// Clear session variables
$_SESSION = array();

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Restart session for flash message
session_start();
setFlash('success', 'You have been logged out successfully');

// Redirect to homepage
redirect(SITE_URL . '/index.php');
