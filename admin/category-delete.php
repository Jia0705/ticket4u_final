<?php
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

// Get category ID
$category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$category_id) {
    setFlash('error', 'Invalid category ID.');
    redirect(SITE_URL . '/admin/categories.php');
}

// Check if category exists
$stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    setFlash('error', 'Category not found.');
    redirect(SITE_URL . '/admin/categories.php');
}

// Check if category has events
$check_events = $conn->prepare("SELECT COUNT(*) as event_count FROM events WHERE category_id = ?");
$check_events->bind_param("i", $category_id);
$check_events->execute();
$event_check = $check_events->get_result()->fetch_assoc();

if ($event_check['event_count'] > 0) {
    setFlash('error', 'Cannot delete category "' . htmlspecialchars($category['name']) . '". It has ' . $event_check['event_count'] . ' event(s) associated with it. Please reassign or delete those events first.');
    redirect(SITE_URL . '/admin/categories.php');
}

// Delete category
$delete_stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
$delete_stmt->bind_param("i", $category_id);

if ($delete_stmt->execute()) {
    setFlash('success', 'Category "' . htmlspecialchars($category['name']) . '" has been deleted successfully.');
} else {
    setFlash('error', 'Failed to delete category: ' . $conn->error);
}

redirect(SITE_URL . '/admin/categories.php');
?>
