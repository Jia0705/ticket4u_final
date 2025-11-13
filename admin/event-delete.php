<?php
$page_title = 'Delete Event - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

$event_id = (int)($_GET['id'] ?? 0);

if ($event_id <= 0) {
    setFlash('error', 'Invalid event ID');
    redirect(SITE_URL . '/admin/events.php');
}

// Get event details
$event_query = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($event_query);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    setFlash('error', 'Event not found');
    redirect(SITE_URL . '/admin/events.php');
}

// Check if event has bookings
$booking_check = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE event_id = ?");
$booking_check->bind_param('i', $event_id);
$booking_check->execute();
$booking_count = $booking_check->get_result()->fetch_assoc()['count'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($booking_count > 0) {
        setFlash('error', 'Cannot delete event with existing bookings. Archive it instead.');
    } else {
        // Delete related ticket types first
        $conn->query("DELETE FROM ticket_types WHERE event_id = $event_id");
        
        // Delete event
        $delete = $conn->prepare("DELETE FROM events WHERE id = ?");
        $delete->bind_param('i', $event_id);
        
        if ($delete->execute()) {
            setFlash('success', 'Event deleted successfully');
            redirect(SITE_URL . '/admin/events.php');
        } else {
            setFlash('error', 'Failed to delete event');
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.delete-container {
    max-width: 600px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.warning-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 2rem;
    text-align: center;
}

.warning-icon {
    font-size: 4rem;
    color: #f5576c;
    margin-bottom: 1rem;
}

.event-info {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: var(--radius-md);
    margin: 1.5rem 0;
}
</style>

<main class="main-content">
    <div class="delete-container">
        <div class="warning-card">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1 style="font-size: 1.75rem; margin-bottom: 1rem;">Delete Event</h1>
            
            <?php if ($booking_count > 0): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle"></i>
                    This event has <?php echo $booking_count; ?> booking(s) and cannot be deleted.
                </div>
            <?php else: ?>
                <p style="color: var(--gray); margin-bottom: 1.5rem;">
                    Are you sure you want to delete this event? This action cannot be undone.
                </p>
            <?php endif; ?>

            <div class="event-info">
                <h3 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($event['title']); ?></h3>
                <p style="color: var(--gray); margin: 0;">
                    <?php echo formatDate($event['event_date']); ?> at <?php echo htmlspecialchars($event['venue_name']); ?>
                </p>
            </div>

            <?php if ($booking_count > 0): ?>
                <a href="<?php echo SITE_URL; ?>/admin/events.php" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-arrow-left"></i> Back to Events
                </a>
            <?php else: ?>
                <form method="POST" style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; background: #f5576c; border-color: #f5576c;">
                        <i class="fas fa-trash"></i> Yes, Delete
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/events.php" class="btn btn-outline" style="flex: 1;">
                        Cancel
                    </a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
