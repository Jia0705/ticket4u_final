<?php
$page_title = 'Admin Dashboard';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

$user = getCurrentUser();

// Get dashboard stats
$stats = [];

// Total revenue
$revenue_query = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue FROM bookings WHERE booking_status = 'confirmed'";
$stats['revenue'] = $conn->query($revenue_query)->fetch_assoc()['total_revenue'];

// Total bookings
$bookings_query = "SELECT COUNT(*) as total_bookings FROM bookings";
$stats['bookings'] = $conn->query($bookings_query)->fetch_assoc()['total_bookings'];

// Total users
$users_query = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
$stats['users'] = $conn->query($users_query)->fetch_assoc()['total_users'];

// Total events
$events_query = "SELECT COUNT(*) as total_events FROM events WHERE status = 'published'";
$stats['events'] = $conn->query($events_query)->fetch_assoc()['total_events'];

// Recent bookings
$recent_bookings = "SELECT b.*, u.name as user_name, e.title as event_title
                    FROM bookings b
                    JOIN users u ON b.user_id = u.id
                    JOIN events e ON b.event_id = e.id
                    ORDER BY b.created_at DESC
                    LIMIT 10";
$recent = $conn->query($recent_bookings);

// Popular events
$popular_events = "SELECT e.title, COUNT(b.id) as booking_count, SUM(b.total_amount) as revenue
                   FROM events e
                   LEFT JOIN bookings b ON e.id = b.event_id
                   GROUP BY e.id
                   ORDER BY booking_count DESC
                   LIMIT 5";
$popular = $conn->query($popular_events);

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.admin-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.admin-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--radius-xl);
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-header h1 {
    font-size: 2rem;
    font-weight: 800;
}

.admin-nav {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 1rem;
    margin-bottom: 2rem;
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
}

.admin-nav a {
    padding: 0.75rem 1.25rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--gray);
    font-weight: 600;
    transition: var(--transition-base);
    white-space: nowrap;
}

.admin-nav a:hover,
.admin-nav a.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-content h3 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 0.25rem;
}

.stat-content p {
    color: var(--gray);
    font-size: 0.9rem;
    margin: 0;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-icon.revenue { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.stat-icon.bookings { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-icon.users { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-icon.events { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.section-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 1.5rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.booking-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--light);
}

.booking-item:last-child {
    border-bottom: none;
}

.booking-info h4 {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.25rem;
}

.booking-info p {
    font-size: 0.85rem;
    color: var(--gray);
    margin: 0;
}

.booking-amount {
    font-weight: 700;
    color: var(--primary-color);
}

.event-rank {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid var(--light);
}

.rank-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.rank-info {
    flex: 1;
}

.rank-info h4 {
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.rank-stats {
    font-size: 0.85rem;
    color: var(--gray);
}

@media (max-width: 968px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}
</style>

<main class="main-content">
    <div class="admin-container">
        <div class="admin-header">
            <div>
                <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</p>
            </div>
            <div>
                <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-outline" style="color: white; border-color: white;">
                    <i class="fas fa-home"></i> Back to Site
                </a>
            </div>
        </div>

        <div class="admin-nav">
            <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/events.php">
                <i class="fas fa-calendar"></i> Events
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/bookings.php">
                <i class="fas fa-ticket-alt"></i> Bookings
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/users.php">
                <i class="fas fa-users"></i> Users
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/categories.php">
                <i class="fas fa-tags"></i> Categories
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <h3><?php echo formatPrice($stats['revenue']); ?></h3>
                    <p>Total Revenue</p>
                </div>
                <div class="stat-icon revenue">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <h3><?php echo number_format($stats['bookings']); ?></h3>
                    <p>Total Bookings</p>
                </div>
                <div class="stat-icon bookings">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <h3><?php echo number_format($stats['users']); ?></h3>
                    <p>Registered Users</p>
                </div>
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <h3><?php echo number_format($stats['events']); ?></h3>
                    <p>Active Events</p>
                </div>
                <div class="stat-icon events">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-clock"></i> Recent Bookings
                </h2>
                <?php if ($recent->num_rows > 0): ?>
                    <?php while ($booking = $recent->fetch_assoc()): ?>
                        <div class="booking-item">
                            <div class="booking-info">
                                <h4><?php echo htmlspecialchars($booking['event_title']); ?></h4>
                                <p><?php echo htmlspecialchars($booking['user_name']); ?> • <?php echo $booking['booking_reference']; ?></p>
                            </div>
                            <div class="booking-amount">
                                <?php echo formatPrice($booking['total_amount']); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; color: var(--gray); padding: 2rem;">No bookings yet</p>
                <?php endif; ?>
            </div>

            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-fire"></i> Popular Events
                </h2>
                <?php if ($popular->num_rows > 0): ?>
                    <?php 
                    $rank = 1;
                    while ($event = $popular->fetch_assoc()): 
                    ?>
                        <div class="event-rank">
                            <div class="rank-number"><?php echo $rank++; ?></div>
                            <div class="rank-info">
                                <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                                <div class="rank-stats">
                                    <?php echo $event['booking_count']; ?> bookings • <?php echo formatPrice($event['revenue'] ?? 0); ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; color: var(--gray); padding: 2rem;">No events yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
