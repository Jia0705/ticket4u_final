<?php
require_once __DIR__ . '/config/config.php';

// Get event slug from URL
$slug = isset($_GET['slug']) ? clean($_GET['slug']) : '';

if (empty($slug)) {
    redirect(SITE_URL . '/events.php');
}

// Fetch event details
$query = "SELECT e.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
          FROM events e
          JOIN categories c ON e.category_id = c.id
          WHERE e.slug = ? AND e.status = 'published'";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setFlash('error', 'Event not found');
    redirect(SITE_URL . '/events.php');
}

$event = $result->fetch_assoc();

// Update view count
$conn->query("UPDATE events SET views = views + 1 WHERE id = " . $event['id']);

// Fetch ticket types
$tickets_query = "SELECT * FROM ticket_types 
                  WHERE event_id = ? AND status = 'active' 
                  ORDER BY display_order ASC, price ASC";
$stmt = $conn->prepare($tickets_query);
$stmt->bind_param('i', $event['id']);
$stmt->execute();
$tickets = $stmt->get_result();

// Fetch related events
$related_query = "SELECT e.*, c.name as category_name, c.slug as category_slug
                  FROM events e
                  JOIN categories c ON e.category_id = c.id
                  WHERE e.category_id = ? AND e.id != ? AND e.status = 'published' AND e.event_date >= CURDATE()
                  ORDER BY e.event_date ASC
                  LIMIT 4";
$stmt = $conn->prepare($related_query);
$stmt->bind_param('ii', $event['category_id'], $event['id']);
$stmt->execute();
$related_events = $stmt->get_result();

$page_title = htmlspecialchars($event['title']);
$page_description = htmlspecialchars(substr($event['description'], 0, 160));
require_once __DIR__ . '/includes/header.php';
?>

<style>
.event-details-page {
    padding: 2rem 0;
}

.breadcrumb {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    margin-bottom: 2rem;
    color: var(--gray);
}

.breadcrumb a {
    color: var(--primary-color);
}

.event-details-container {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
}

.event-main-image {
    width: 100%;
    height: 500px;
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-xl);
    margin-bottom: 2rem;
}

.event-main-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.event-header {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
}

.event-category-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--bg-light);
    color: var(--primary-color);
    padding: 0.5rem 1rem;
    border-radius: var(--radius-lg);
    font-weight: 600;
    margin-bottom: 1rem;
}

.event-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 1rem;
}

.event-meta-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
    background: var(--bg-light);
    border-radius: var(--radius-lg);
}

.meta-item {
    display: flex;
    align-items: start;
    gap: 1rem;
}

.meta-icon {
    width: 48px;
    height: 48px;
    background: white;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 1.25rem;
    flex-shrink: 0;
}

.meta-content h4 {
    font-size: 0.875rem;
    color: var(--gray);
    margin-bottom: 0.25rem;
}

.meta-content p {
    font-weight: 600;
    color: var(--dark);
}

.event-content {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.event-description {
    line-height: 1.8;
    color: var(--dark-light);
}

.booking-sidebar {
    position: sticky;
    top: 100px;
}

.booking-card {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    border: 2px solid var(--primary-color);
}

.price-display {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: var(--radius-lg);
    margin-bottom: 1.5rem;
}

.price-label {
    font-size: 0.875rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.price-amount {
    font-size: 2.5rem;
    font-weight: 800;
}

.ticket-type {
    padding: 1rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    margin-bottom: 1rem;
    transition: var(--transition-base);
}

.ticket-type:hover {
    border-color: var(--primary-color);
    background: var(--bg-light);
}

.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.ticket-name {
    font-weight: 700;
    color: var(--dark);
}

.ticket-price {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--primary-color);
}

.ticket-info {
    font-size: 0.875rem;
    color: var(--gray);
    margin-bottom: 0.5rem;
}

.ticket-available {
    font-size: 0.875rem;
    color: var(--success);
}

.related-events {
    margin-top: 3rem;
}

.related-events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

@media (max-width: 1024px) {
    .event-details-container {
        grid-template-columns: 1fr;
    }
    
    .booking-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .event-title {
        font-size: 1.75rem;
    }
    
    .event-meta-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="container event-details-page">
    <div class="breadcrumb">
        <a href="<?php echo SITE_URL; ?>"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="<?php echo SITE_URL; ?>/events.php">Events</a>
        <span>/</span>
        <a href="<?php echo SITE_URL; ?>/events.php?category=<?php echo $event['category_slug']; ?>">
            <?php echo htmlspecialchars($event['category_name']); ?>
        </a>
        <span>/</span>
        <span><?php echo htmlspecialchars($event['title']); ?></span>
    </div>

    <div class="event-details-container">
        <div class="event-main-content">
            <div class="event-main-image">
                <img src="<?php echo SITE_URL; ?>/uploads/events/<?php echo htmlspecialchars($event['featured_image']); ?>" 
                     alt="<?php echo htmlspecialchars($event['title']); ?>"
                     onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder-event.jpg'">
            </div>

            <div class="event-header">
                <div class="event-category-badge">
                    <i class="fas <?php echo $event['category_icon']; ?>"></i>
                    <?php echo htmlspecialchars($event['category_name']); ?>
                </div>

                <h1 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h1>

                <div class="event-meta-row">
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="meta-content">
                            <h4>Date</h4>
                            <p><?php echo formatDate($event['event_date'], 'l, d F Y'); ?></p>
                        </div>
                    </div>

                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="meta-content">
                            <h4>Time</h4>
                            <p><?php echo formatTime($event['event_time']); ?></p>
                        </div>
                    </div>

                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="meta-content">
                            <h4>Venue</h4>
                            <p><?php echo htmlspecialchars($event['venue_name']); ?></p>
                            <small style="color: var(--gray);"><?php echo htmlspecialchars($event['venue_city']); ?></small>
                        </div>
                    </div>

                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="meta-content">
                            <h4>Availability</h4>
                            <p><?php echo number_format($event['available_seats']); ?> / <?php echo number_format($event['total_seats']); ?> seats</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="event-content">
                <h2 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Event Description
                </h2>
                <div class="event-description">
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </div>
            </div>

            <div class="event-content">
                <h2 class="section-title">
                    <i class="fas fa-map"></i>
                    Venue Information
                </h2>
                <div class="event-description">
                    <p><strong><?php echo htmlspecialchars($event['venue_name']); ?></strong></p>
                    <p><?php echo htmlspecialchars($event['venue_address']); ?></p>
                    <p><?php echo htmlspecialchars($event['venue_city']); ?>, <?php echo htmlspecialchars($event['venue_state'] ?? 'Malaysia'); ?></p>
                    <?php if (!empty($event['venue_country'])): ?>
                        <p><?php echo htmlspecialchars($event['venue_country']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($event['organizer_name'])): ?>
                <div class="event-content">
                    <h2 class="section-title">
                        <i class="fas fa-user-tie"></i>
                        Organizer
                    </h2>
                    <div class="event-description">
                        <p><strong><?php echo htmlspecialchars($event['organizer_name']); ?></strong></p>
                        <?php if ($event['organizer_email']): ?>
                            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($event['organizer_email']); ?></p>
                        <?php endif; ?>
                        <?php if ($event['organizer_phone']): ?>
                            <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($event['organizer_phone']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="booking-sidebar">
            <div class="booking-card">
                <div class="price-display">
                    <div class="price-label">Starting from</div>
                    <div class="price-amount"><?php echo formatPrice($event['min_price']); ?></div>
                </div>

                <h3 class="section-title" style="font-size: 1.125rem;">
                    <i class="fas fa-ticket-alt"></i>
                    Select Ticket Type
                </h3>

                <form action="<?php echo SITE_URL; ?>/booking.php" method="POST" id="bookingForm">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                    
                    <?php if ($tickets && $tickets->num_rows > 0): ?>
                        <?php while ($ticket = $tickets->fetch_assoc()): ?>
                            <div class="ticket-type">
                                <div class="ticket-header">
                                    <div class="ticket-name"><?php echo htmlspecialchars($ticket['name']); ?></div>
                                    <div class="ticket-price"><?php echo formatPrice($ticket['price']); ?></div>
                                </div>
                                <?php if ($ticket['description']): ?>
                                    <div class="ticket-info"><?php echo htmlspecialchars($ticket['description']); ?></div>
                                <?php endif; ?>
                                <div class="ticket-available">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo $ticket['available']; ?> tickets available
                                </div>
                                <div style="margin-top: 0.5rem;">
                                    <select name="ticket_<?php echo $ticket['id']; ?>" class="form-control">
                                        <option value="0">Select quantity</option>
                                        <?php for ($i = 1; $i <= min($ticket['max_per_order'], $ticket['available']); $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> ticket<?php echo $i > 1 ? 's' : ''; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endwhile; ?>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            <i class="fas fa-shopping-cart"></i>
                            Book Now
                        </button>

                        <button type="button" class="wishlist-btn btn btn-outline" style="width: 100%; margin-top: 0.5rem;" data-event-id="<?php echo $event['id']; ?>">
                            <i class="far fa-heart"></i>
                            Add to Wishlist
                        </button>
                    <?php else: ?>
                        <p class="text-center text-muted">No tickets available at the moment</p>
                    <?php endif; ?>
                </form>

                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                    <p style="font-size: 0.875rem; color: var(--gray); text-align: center;">
                        <i class="fas fa-shield-alt"></i>
                        Secure booking powered by Ticket4U
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if ($related_events && $related_events->num_rows > 0): ?>
        <div class="related-events">
            <h2 class="section-title">
                <i class="fas fa-calendar-week"></i>
                Similar Events You Might Like
            </h2>

            <div class="related-events-grid">
                <?php while ($related = $related_events->fetch_assoc()): ?>
                    <div class="event-card">
                        <div class="event-image">
                            <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $related['slug']; ?>">
                                <img src="<?php echo SITE_URL; ?>/uploads/events/<?php echo htmlspecialchars($related['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($related['title']); ?>"
                                     onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder-event.jpg'">
                            </a>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title">
                                <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $related['slug']; ?>">
                                    <?php echo htmlspecialchars($related['title']); ?>
                                </a>
                            </h3>
                            <div class="event-meta">
                                <div class="event-meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?php echo formatDate($related['event_date'], 'd M Y'); ?></span>
                                </div>
                            </div>
                            <div class="event-footer">
                                <div class="event-price"><?php echo formatPrice($related['min_price']); ?></div>
                                <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $related['slug']; ?>" class="btn btn-primary btn-sm" style="margin-top: 0.5rem;">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
