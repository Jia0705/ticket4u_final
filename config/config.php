<?php
/**
 * Ticket4U - Configuration File
 * Professional Event Ticketing System
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ticket4u');

// Site Configuration
define('SITE_NAME', 'Ticket4U');
define('SITE_URL', 'http://localhost/ticket4u_final');
define('ADMIN_EMAIL', 'admin@ticket4u.com');

// File Upload Configuration
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);

// Pagination
define('EVENTS_PER_PAGE', 12);
define('BOOKINGS_PER_PAGE', 10);

// Booking Configuration
define('BOOKING_FEE_PERCENTAGE', 0.05); // 5% booking fee
define('MAX_TICKETS_PER_ORDER', 10);

// Timezone
date_default_timezone_set('Asia/Kuala_Lumpur');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
} catch (Exception $e) {
    die('
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #fee; border: 1px solid #fcc; border-radius: 8px;">
            <h2 style="color: #c00;">Database Connection Error</h2>
            <p>Unable to connect to the database. Please check your configuration.</p>
            <p style="color: #666; font-size: 14px;">' . $e->getMessage() . '</p>
        </div>
    ');
}

// Helper function to sanitize input
function clean($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $conn->real_escape_string($data);
}

// Helper function to redirect
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Helper function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect(SITE_URL . '/auth/login.php');
    }
}

// Helper function to require admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect(SITE_URL . '/index.php');
    }
}

// Get current user data
function getCurrentUser() {
    global $conn;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $user_id = (int)$_SESSION['user_id'];
    $query = "SELECT id, email, name, phone, role, profile_image, password, created_at FROM users WHERE id = $user_id AND status = 'active'";
    $result = $conn->query($query);
    
    return $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

// Flash message functions
function setFlash($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

// Format price
function formatPrice($amount) {
    return 'RM ' . number_format($amount ?? 0, 2);
}

// Format date
function formatDate($date, $format = 'd M Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

// Format time
function formatTime($time) {
    return date('h:i A', strtotime($time));
}

// Generate booking reference
function generateBookingReference() {
    return 'TK4U' . strtoupper(substr(uniqid(), -8));
}

// Generate ticket number
function generateTicketNumber() {
    return 'TKT' . strtoupper(substr(uniqid(), -10));
}
