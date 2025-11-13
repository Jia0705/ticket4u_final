<?php
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

// Get user ID
$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$user_id) {
    setFlash('error', 'Invalid user ID.');
    redirect(SITE_URL . '/admin/users.php');
}

// Prevent deleting yourself
if ($user_id == $_SESSION['user_id']) {
    setFlash('error', 'You cannot delete your own account.');
    redirect(SITE_URL . '/admin/users.php');
}

// Check if user exists
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    setFlash('error', 'User not found.');
    redirect(SITE_URL . '/admin/users.php');
}

// Delete user's bookings first (CASCADE)
$delete_bookings = $conn->prepare("DELETE FROM bookings WHERE user_id = ?");
$delete_bookings->bind_param("i", $user_id);
$delete_bookings->execute();

// Delete user's wishlists
$delete_wishlists = $conn->prepare("DELETE FROM wishlists WHERE user_id = ?");
$delete_wishlists->bind_param("i", $user_id);
$delete_wishlists->execute();

// Delete user
$delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$delete_stmt->bind_param("i", $user_id);

if ($delete_stmt->execute()) {
    setFlash('success', 'User "' . htmlspecialchars($user['name']) . '" and all their data have been deleted successfully.');
} else {
    setFlash('error', 'Failed to delete user: ' . $conn->error);
}

redirect(SITE_URL . '/admin/users.php');
?>
