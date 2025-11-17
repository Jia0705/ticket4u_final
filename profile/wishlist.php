<?php
$page_title = 'My Wishlist';
require_once __DIR__ . '/../config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('error', 'Please login to view your wishlist');
    redirect(SITE_URL . '/auth/login.php');
}

$user = getCurrentUser();

// Get wishlist items
$wishlist_query = "SELECT e.*, c.name as category_name, c.icon as category_icon,
                   w.created_at as added_at
                   FROM wishlists w
                   JOIN events e ON w.event_id = e.id
                   JOIN categories c ON e.category_id = c.id
                   WHERE w.user_id = ?
                   ORDER BY w.created_at DESC";
$stmt = $conn->prepare($wishlist_query);
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$wishlist = $stmt->get_result();

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.wishlist-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.wishlist-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 2rem;
    border-radius: var(--radius-xl);
    margin-bottom: 2rem;
}

.wishlist-header h1 {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.wishlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
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
</style>

<main class="main-content">
    <div class="wishlist-container">
        <div class="wishlist-header">
            <h1><i class="fas fa-heart"></i> My Wishlist</h1>
            <p>Events you've saved for later</p>
        </div>

        <?php if ($wishlist->num_rows > 0): ?>
            <div class="wishlist-grid">
                <?php while ($event = $wishlist->fetch_assoc()): ?>
                    <div class="event-card">
                        <div class="event-image">
                            <img src="<?php echo SITE_URL . '/uploads/events/' . htmlspecialchars($event['featured_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($event['title']); ?>"
                                 onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder-event.jpg'">
                            <span class="event-badge">
                                <i class="<?php echo htmlspecialchars($event['category_icon']); ?>"></i>
                                <?php echo htmlspecialchars($event['category_name']); ?>
                            </span>
                            <button class="wishlist-btn active" 
                                    onclick="toggleWishlist(<?php echo $event['id']; ?>, this, event)"
                                    title="Remove from wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="event-content">
                            <div class="event-meta">
                                <span><i class="far fa-calendar"></i> <?php echo formatDate($event['event_date']); ?></span>
                                <span><i class="far fa-clock"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?></span>
                            </div>
                            <h3 class="event-title">
                                <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $event['slug']; ?>">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </a>
                            </h3>
                            <p class="event-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($event['venue_name']); ?>, <?php echo htmlspecialchars($event['venue_city']); ?>
                            </p>
                            <?php
                            // Get min price
                            $price_query = "SELECT MIN(price) as min_price FROM ticket_types WHERE event_id = ?";
                            $price_stmt = $conn->prepare($price_query);
                            $price_stmt->bind_param('i', $event['id']);
                            $price_stmt->execute();
                            $price = $price_stmt->get_result()->fetch_assoc();
                            ?>
                            <div class="event-footer">
                                <div class="event-price">
                                    From <?php echo formatPrice($price['min_price'] ?? 0); ?>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $event['slug']; ?>" 
                                   class="btn btn-primary btn-sm">
                                    View Details
                                </a>
                            </div>
                            <p style="margin-top: 0.75rem; font-size: 0.875rem; color: var(--gray);">
                                <i class="far fa-clock"></i> Added <?php echo formatDate($event['added_at']); ?>
                            </p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-heart-broken"></i>
                </div>
                <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Your Wishlist is Empty</h2>
                <p style="color: var(--gray); margin-bottom: 2rem;">
                    Start adding events to your wishlist to save them for later!
                </p>
                <a href="<?php echo SITE_URL; ?>/events.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-calendar"></i> Browse Events
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
