<?php
$page_title = 'Delete Ticket Type - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

$ticket_id = (int)($_GET['id'] ?? 0);
$event_id = (int)($_GET['event_id'] ?? 0);

if ($ticket_id <= 0 || $event_id <= 0) {
    setFlash('error', 'Invalid parameters');
    redirect(SITE_URL . '/admin/events.php');
}

// Check if ticket has bookings
$check = $conn->prepare("SELECT COUNT(*) as count FROM booking_items WHERE ticket_type_id = ?");
$check->bind_param('i', $ticket_id);
$check->execute();
$count = $check->get_result()->fetch_assoc()['count'];

if ($count > 0) {
    setFlash('error', 'Cannot delete ticket type with existing bookings');
    redirect(SITE_URL . '/admin/event-edit.php?id=' . $event_id);
}

// Delete ticket type
$delete = $conn->prepare("DELETE FROM ticket_types WHERE id = ?");
$delete->bind_param('i', $ticket_id);

if ($delete->execute()) {
    // Update event min/max prices after deletion
    $update_prices = "UPDATE events SET 
        min_price = (SELECT MIN(price) FROM ticket_types WHERE event_id = ?),
        max_price = (SELECT MAX(price) FROM ticket_types WHERE event_id = ?),
        total_seats = (SELECT COALESCE(SUM(quantity), 0) FROM ticket_types WHERE event_id = ?),
        available_seats = (SELECT COALESCE(SUM(available), 0) FROM ticket_types WHERE event_id = ?)
        WHERE id = ?";
    $price_stmt = $conn->prepare($update_prices);
    $price_stmt->bind_param('iiiii', $event_id, $event_id, $event_id, $event_id, $event_id);
    $price_stmt->execute();
    
    setFlash('success', 'Ticket type deleted successfully');
} else {
    setFlash('error', 'Failed to delete ticket type');
}

redirect(SITE_URL . '/admin/event-edit.php?id=' . $event_id);
?>
