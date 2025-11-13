<?php
$page_title = 'My Bookings';
require_once __DIR__ . '/../config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('error', 'Please login to view your bookings');
    redirect(SITE_URL . '/auth/login.php');
}

$user = getCurrentUser();

// Get user's bookings
$bookings_query = "SELECT b.*, e.title as event_title, e.slug as event_slug, e.featured_image, 
                   e.event_date, e.event_time, e.venue_name, e.venue_city
                   FROM bookings b
                   JOIN events e ON b.event_id = e.id
                   WHERE b.user_id = ?
                   ORDER BY b.created_at DESC";
$stmt = $conn->prepare($bookings_query);
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$bookings = $stmt->get_result();

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.profile-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 2rem;
    border-radius: var(--radius-xl);
    margin-bottom: 2rem;
}

.profile-header h1 {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.booking-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: var(--transition-base);
}

.booking-card:hover {
    box-shadow: var(--shadow-xl);
}

.booking-grid {
    display: grid;
    grid-template-columns: 150px 1fr auto;
    gap: 1.5rem;
    padding: 1.5rem;
}

.booking-image img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: var(--radius-md);
}

.booking-details h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: var(--dark);
}

.booking-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    color: var(--gray);
}

.booking-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.booking-meta-item i {
    width: 16px;
    color: var(--primary-color);
}

.booking-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
}

.booking-status {
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.875rem;
}

.status-confirmed {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: var(--radius-xl);
}

.empty-icon {
    font-size: 4rem;
    color: var(--gray);
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .booking-grid {
        grid-template-columns: 1fr;
    }
    
    .booking-actions {
        align-items: flex-start;
    }
}
</style>

<main class="main-content">
    <div class="profile-container">
        <div class="profile-header">
            <h1><i class="fas fa-ticket-alt"></i> My Bookings</h1>
            <p>View and manage all your ticket bookings</p>
        </div>

        <?php if ($bookings->num_rows > 0): ?>
            <?php while ($booking = $bookings->fetch_assoc()): ?>
                <div class="booking-card">
                    <div class="booking-grid">
                        <div class="booking-image">
                            <img src="<?php echo SITE_URL . '/uploads/events/' . htmlspecialchars($booking['featured_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($booking['event_title']); ?>"
                                 onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder-event.jpg'">
                        </div>

                        <div class="booking-details">
                            <h3><?php echo htmlspecialchars($booking['event_title']); ?></h3>
                            <div class="booking-meta">
                                <div class="booking-meta-item">
                                    <i class="fas fa-ticket-alt"></i>
                                    <span>Booking Ref: <strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong></span>
                                </div>
                                <div class="booking-meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo formatDate($booking['event_date']); ?> at <?php echo date('g:i A', strtotime($booking['event_time'])); ?></span>
                                </div>
                                <div class="booking-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($booking['venue_name']); ?>, <?php echo htmlspecialchars($booking['venue_city']); ?></span>
                                </div>
                                <div class="booking-meta-item">
                                    <i class="fas fa-users"></i>
                                    <span><?php echo $booking['total_tickets']; ?> ticket<?php echo $booking['total_tickets'] > 1 ? 's' : ''; ?></span>
                                </div>
                                <div class="booking-meta-item">
                                    <i class="fas fa-money-bill"></i>
                                    <span><strong><?php echo formatPrice($booking['total_amount']); ?></strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="booking-actions">
                            <span class="booking-status status-<?php echo $booking['booking_status']; ?>">
                                <?php echo ucfirst($booking['booking_status']); ?>
                            </span>
                            <a href="<?php echo SITE_URL; ?>/profile/booking-details.php?ref=<?php echo $booking['booking_reference']; ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $booking['event_slug']; ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-info-circle"></i> Event Info
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">No Bookings Yet</h2>
                <p style="color: var(--gray); margin-bottom: 2rem;">
                    You haven't booked any tickets yet. Explore amazing events and book your first ticket!
                </p>
                <a href="<?php echo SITE_URL; ?>/events.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-calendar"></i> Browse Events
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
