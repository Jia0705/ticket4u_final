<?php
$page_title = 'Home';
$page_description = 'Discover and book tickets for the best concerts, sports, theatre, and entertainment events in Malaysia';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/header.php';

// Get current user ID for wishlist check
$current_user_id = isLoggedIn() ? getCurrentUser()['id'] : 0;

// Fetch featured events
$featured_query = "SELECT e.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon,
                   (SELECT COUNT(*) FROM wishlists WHERE event_id = e.id AND user_id = ?) as in_wishlist
                   FROM events e
                   JOIN categories c ON e.category_id = c.id
                   WHERE e.status = 'published' AND e.featured = TRUE AND e.event_date >= CURDATE()
                   ORDER BY e.event_date ASC
                   LIMIT 6";
$stmt = $conn->prepare($featured_query);
$stmt->bind_param('i', $current_user_id);
$stmt->execute();
$featured_events = $stmt->get_result();

// Fetch upcoming events
$upcoming_query = "SELECT e.*, c.name as category_name, c.slug as category_slug,
                   (SELECT COUNT(*) FROM wishlists WHERE event_id = e.id AND user_id = ?) as in_wishlist
                   FROM events e
                   JOIN categories c ON e.category_id = c.id
                   WHERE e.status = 'published' AND e.event_date >= CURDATE()
                   ORDER BY e.event_date ASC
                   LIMIT 8";
$stmt = $conn->prepare($upcoming_query);
$stmt->bind_param('i', $current_user_id);
$stmt->execute();
$upcoming_events = $stmt->get_result();

// Fetch categories
$categories_query = "SELECT * FROM categories WHERE status = 'active' ORDER BY display_order ASC";
$categories = $conn->query($categories_query);

// Fetch stats
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM events WHERE status = 'published') as total_events,
    (SELECT COUNT(*) FROM users WHERE role = 'user') as total_users,
    (SELECT COUNT(*) FROM bookings WHERE payment_status = 'paid') as total_bookings";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();
?>

<style>
/* Homepage Specific Styles */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4rem 0;
    margin-bottom: 3rem;
    border-radius: 0 0 3rem 3rem;
}

.hero-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
    animation: fadeInUp 0.6s ease-out;
}

.hero-subtitle {
    font-size: 1.25rem;
    opacity: 0.95;
    margin-bottom: 2rem;
    animation: fadeInUp 0.6s ease-out 0.2s both;
}

.hero-search {
    max-width: 600px;
    margin: 0 auto;
    animation: fadeInUp 0.6s ease-out 0.4s both;
}

.hero-search .search-input-wrapper {
    background: white;
    padding: 0.5rem;
}

.hero-search input {
    color: var(--dark);
}

.hero-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 3rem;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    display: block;
}

.stat-label {
    opacity: 0.9;
    font-size: 0.95rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--dark);
}

.section-title i {
    color: var(--primary-color);
    margin-right: 0.5rem;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.category-card {
    background: white;
    padding: 2rem 1rem;
    border-radius: var(--radius-xl);
    text-align: center;
    box-shadow: var(--shadow-md);
    transition: var(--transition-base);
    cursor: pointer;
    border: 2px solid transparent;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-color);
}

.category-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
}

.category-name {
    font-weight: 600;
    color: var(--dark);
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.event-card {
    background: white;
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: var(--transition-base);
    position: relative;
}

.event-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
}

.event-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.event-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition-slow);
}

.event-card:hover .event-image img {
    transform: scale(1.1);
}

.event-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-lg);
    font-size: 0.875rem;
    font-weight: 600;
    box-shadow: var(--shadow-md);
}

.wishlist-btn {
    position: absolute;
    top: 1rem;
    left: 1rem;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--gray);
    box-shadow: var(--shadow-md);
    transition: var(--transition-base);
    z-index: 10;
}

.wishlist-btn:hover, .wishlist-btn.active {
    color: var(--error);
    transform: scale(1.1);
}

.event-content {
    padding: 1.5rem;
}

.event-category {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.event-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.event-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.event-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--gray);
}

.event-meta-item i {
    color: var(--primary-color);
    width: 16px;
}

.event-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.event-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
}

.event-price small {
    font-size: 0.875rem;
    color: var(--gray);
    font-weight: 400;
}

.cta-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4rem 2rem;
    border-radius: var(--radius-xl);
    text-align: center;
    margin: 3rem 0;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
}

.cta-text {
    font-size: 1.125rem;
    opacity: 0.95;
    margin-bottom: 2rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
    
    .category-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
    }
    
    .events-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-title {
        font-size: 1.75rem;
    }
}
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Discover Amazing Events</h1>
            <p class="hero-subtitle">Book tickets for concerts, sports, theatre, and entertainment events across Malaysia</p>
            
            <form action="<?php echo SITE_URL; ?>/events.php" method="GET" class="hero-search">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search for events, artists, venues..." required>
                    <button type="submit" class="search-btn">Search Events</button>
                </div>
            </form>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo number_format($stats['total_events']); ?>+</span>
                    <span class="stat-label">Live Events</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo number_format($stats['total_bookings']); ?>+</span>
                    <span class="stat-label">Tickets Sold</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo number_format($stats['total_users']); ?>+</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <!-- Categories Section -->
    <section class="categories-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-th"></i>
                Browse by Category
            </h2>
        </div>
        
        <div class="category-grid">
            <?php while ($category = $categories->fetch_assoc()): ?>
                <a href="<?php echo SITE_URL; ?>/events.php?category=<?php echo $category['slug']; ?>" class="category-card">
                    <div class="category-icon">
                        <i class="fas <?php echo $category['icon']; ?>"></i>
                    </div>
                    <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
                </a>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Featured Events Section -->
    <section class="featured-events-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-star"></i>
                Featured Events
            </h2>
            <a href="<?php echo SITE_URL; ?>/events.php?featured=1" class="btn btn-outline">View All</a>
        </div>
        
        <div class="events-grid">
            <?php if ($featured_events && $featured_events->num_rows > 0): ?>
                <?php while ($event = $featured_events->fetch_assoc()): ?>
                    <div class="event-card">
                        <div class="event-image">
                            <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $event['slug']; ?>">
                                <img src="<?php echo SITE_URL; ?>/uploads/events/<?php echo htmlspecialchars($event['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($event['title']); ?>"
                                     onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder-event.jpg'">
                            </a>
                            <button class="wishlist-btn<?php echo $event['in_wishlist'] > 0 ? ' active' : ''; ?>" 
                                    onclick="toggleWishlist(<?php echo $event['id']; ?>, this)"
                                    title="<?php echo $event['in_wishlist'] > 0 ? 'Remove from wishlist' : 'Add to wishlist'; ?>">
                                <i class="fa<?php echo $event['in_wishlist'] > 0 ? 's' : 'r'; ?> fa-heart"></i>
                            </button>
                            <div class="event-badge">Featured</div>
                        </div>
                        
                        <div class="event-content">
                            <div class="event-category">
                                <i class="fas <?php echo $event['category_icon']; ?>"></i>
                                <?php echo htmlspecialchars($event['category_name']); ?>
                            </div>
                            
                            <h3 class="event-title">
                                <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $event['slug']; ?>">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </a>
                            </h3>
                            
                            <div class="event-meta">
                                <div class="event-meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?php echo formatDate($event['event_date'], 'D, d M Y'); ?></span>
                                </div>
                                <div class="event-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo formatTime($event['event_time']); ?></span>
                                </div>
                                <div class="event-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($event['venue_name']); ?></span>
                                </div>
                            </div>
                            
                            <div class="event-footer">
                                <div class="event-price">
                                    <?php echo formatPrice($event['min_price']); ?>
                                    <?php if ($event['max_price'] > $event['min_price']): ?>
                                        <small>- <?php echo formatPrice($event['max_price']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $event['slug']; ?>" class="btn btn-primary btn-sm">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-muted">No featured events available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section class="upcoming-events-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-calendar-week"></i>
                Upcoming Events
            </h2>
            <a href="<?php echo SITE_URL; ?>/events.php" class="btn btn-outline">View All Events</a>
        </div>
        
        <div class="events-grid">
            <?php if ($upcoming_events && $upcoming_events->num_rows > 0): ?>
                <?php while ($event = $upcoming_events->fetch_assoc()): ?>
                    <div class="event-card">
                        <div class="event-image">
                            <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $event['slug']; ?>">
                                <img src="<?php echo SITE_URL; ?>/uploads/events/<?php echo htmlspecialchars($event['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($event['title']); ?>"
                                     onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder-event.jpg'">
                            </a>
                            <button class="wishlist-btn<?php echo $event['in_wishlist'] > 0 ? ' active' : ''; ?>" 
                                    onclick="toggleWishlist(<?php echo $event['id']; ?>, this)"
                                    title="<?php echo $event['in_wishlist'] > 0 ? 'Remove from wishlist' : 'Add to wishlist'; ?>">
                                <i class="fa<?php echo $event['in_wishlist'] > 0 ? 's' : 'r'; ?> fa-heart"></i>
                            </button>
                        </div>
                        
                        <div class="event-content">
                            <div class="event-category">
                                <i class="fas fa-music"></i>
                                <?php echo htmlspecialchars($event['category_name']); ?>
                            </div>
                            
                            <h3 class="event-title">
                                <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $event['slug']; ?>">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </a>
                            </h3>
                            
                            <div class="event-meta">
                                <div class="event-meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?php echo formatDate($event['event_date'], 'D, d M Y'); ?></span>
                                </div>
                                <div class="event-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($event['venue_city']); ?></span>
                                </div>
                            </div>
                            
                            <div class="event-footer">
                                <div class="event-price">
                                    <?php echo formatPrice($event['min_price']); ?>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $event['slug']; ?>" class="btn btn-primary btn-sm">
                                    Get Tickets
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-muted">No upcoming events available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">Ready to Experience Live Events?</h2>
            <p class="cta-text">Join thousands of event-goers and book your tickets today. Don't miss out on the best entertainment experiences in Malaysia!</p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <?php if (!isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-lg" style="background: white; color: var(--primary-color);">
                        <i class="fas fa-user-plus"></i> Sign Up Now
                    </a>
                <?php endif; ?>
                <a href="<?php echo SITE_URL; ?>/events.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-ticket-alt"></i> Browse All Events
                </a>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
