<?php
session_start(); // Start session explicitly
header('Content-Type: application/json');
require_once __DIR__ . '/config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Please login to add to wishlist'
    ]);
    exit;
}

// Validate event_id
if (!isset($_POST['event_id']) || !is_numeric($_POST['event_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid event ID'
    ]);
    exit;
}

$user_id = getCurrentUser()['id'];
$event_id = (int)$_POST['event_id'];

// Check if event exists
$event_check = $conn->prepare("SELECT id FROM events WHERE id = ? AND status = 'published'");
$event_check->bind_param('i', $event_id);
$event_check->execute();
if ($event_check->get_result()->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Event not found'
    ]);
    exit;
}

// Check if already in wishlist
$check_query = "SELECT id FROM wishlists WHERE user_id = ? AND event_id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param('ii', $user_id, $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Remove from wishlist
    $delete_query = "DELETE FROM wishlists WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('ii', $user_id, $event_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'action' => 'removed',
            'message' => 'Removed from wishlist'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to remove from wishlist'
        ]);
    }
} else {
    // Add to wishlist
    $insert_query = "INSERT INTO wishlists (user_id, event_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('ii', $user_id, $event_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'action' => 'added',
            'message' => 'Added to wishlist'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add to wishlist'
        ]);
    }
}
?>
