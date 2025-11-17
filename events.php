<?php
$page_title = 'Browse Events';
require_once __DIR__ . '/config/config.php';

// Get filter parameters
$category = isset($_GET['category']) ? clean($_GET['category']) : '';
$search = isset($_GET['search']) ? clean($_GET['search']) : '';
$city = isset($_GET['city']) ? clean($_GET['city']) : '';
$date_from = isset($_GET['date_from']) ? clean($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? clean($_GET['date_to']) : '';
$sort = isset($_GET['sort']) ? clean($_GET['sort']) : 'date_asc';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Build query
$where = ["e.status = 'published'", "e.event_date >= CURDATE()"];
$params = [];

if ($category) {
    $where[] = "c.slug = ?";
    $params[] = $category;
}

if ($search) {
    $where[] = "(e.title LIKE ? OR e.description LIKE ? OR e.venue_name LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($city) {
    $where[] = "e.venue_city LIKE ?";
    $params[] = "%$city%";
}

if ($date_from) {
    $where[] = "e.event_date >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $where[] = "e.event_date <= ?";
    $params[] = $date_to;
}

$where_clause = implode(' AND ', $where);

// Sorting
$order_by = match($sort) {
    'date_desc' => 'e.event_date DESC',
    'price_asc' => 'e.min_price ASC',
    'price_desc' => 'e.min_price DESC',
    'popular' => 'e.views DESC',
    default => 'e.event_date ASC',
};

// Pagination
$offset = ($page - 1) * EVENTS_PER_PAGE;

// Count total
$count_query = "SELECT COUNT(*) as total FROM events e 
                JOIN categories c ON e.category_id = c.id 
                WHERE $where_clause";
$stmt = $conn->prepare($count_query);
if ($params) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$total_events = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_events / EVENTS_PER_PAGE);

// Get current user ID for wishlist check
$current_user_id = isLoggedIn() ? getCurrentUser()['id'] : 0;

// Fetch events with wishlist status
$events_query = "SELECT e.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon,
                 (SELECT COUNT(*) FROM wishlists WHERE event_id = e.id AND user_id = ?) as in_wishlist
                 FROM events e
                 JOIN categories c ON e.category_id = c.id
                 WHERE $where_clause
                 ORDER BY $order_by
                 LIMIT ? OFFSET ?";
$stmt = $conn->prepare($events_query);
// Add user_id as first parameter
array_unshift($params, $current_user_id);
$params[] = EVENTS_PER_PAGE;
$params[] = $offset;
$stmt->bind_param('i' . str_repeat('s', count($params) - 3) . 'ii', ...$params);
$stmt->execute();
$events = $stmt->get_result();

// Get all cities for filter
$cities_query = "SELECT DISTINCT venue_city FROM events WHERE status = 'published' AND venue_city IS NOT NULL ORDER BY venue_city";
$cities = $conn->query($cities_query);

// Get all categories
$categories_query = "SELECT * FROM categories WHERE status = 'active' ORDER BY display_order ASC";
$categories = $conn->query($categories_query);

require_once __DIR__ . '/includes/header.php';
?>

<style>
.events-page {
    padding: 2rem 0;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 2rem 2rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.breadcrumb {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    opacity: 0.9;
}

.breadcrumb a {
    color: white;
}

.events-container {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 2rem;
}

.filters-sidebar {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    height: fit-content;
    position: sticky;
    top: 100px;
}

.filter-section {
    margin-bottom: 1.5rem;
}

.filter-title {
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: var(--transition-base);
}

.filter-option:hover {
    background: var(--bg-light);
}

.filter-option input {
    cursor: pointer;
}

.events-content {
    flex: 1;
}

.events-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.results-count {
    font-weight: 600;
    color: var(--gray);
}

.sort-select {
    padding: 0.5rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    background: white;
    cursor: pointer;
    transition: var(--transition-base);
}

.sort-select:focus {
    outline: none;
    border-color: var(--primary-color);
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
}

.pagination-btn {
    padding: 0.75rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    background: white;
    color: var(--dark);
    font-weight: 600;
    transition: var(--transition-base);
}

.pagination-btn:hover:not(:disabled) {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.pagination-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.no-results {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
}

.no-results i {
    font-size: 4rem;
    color: var(--gray-light);
    margin-bottom: 1rem;
}

@media (max-width: 1024px) {
    .events-container {
        grid-template-columns: 1fr;
    }
    
    .filters-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .events-grid {
        grid-template-columns: 1fr;
    }
    
    .page-title {
        font-size: 1.75rem;
    }
}
</style>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>"><i class="fas fa-home"></i></a>
            <span>/</span>
            <span>Events</span>
            <?php if ($category): ?>
                <span>/</span>
                <span><?php echo ucfirst($category); ?></span>
            <?php endif; ?>
        </div>
        <h1 class="page-title">
            <?php 
            if ($category) {
                echo ucfirst(str_replace('-', ' ', $category));
            } elseif ($search) {
                echo "Search Results for '$search'";
            } else {
                echo "All Events";
            }
            ?>
        </h1>
    </div>
</div>

<div class="container events-page">
    <div class="events-container">
        <!-- Filters Sidebar -->
        <aside class="filters-sidebar">
            <form method="GET" action="">
                <!-- Category Filter -->
                <div class="filter-section">
                    <h3 class="filter-title">
                        <i class="fas fa-th"></i>
                        Categories
                    </h3>
                    <div class="filter-group">
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <label class="filter-option">
                                <input type="radio" name="category" value="<?php echo $cat['slug']; ?>" 
                                       <?php echo $category === $cat['slug'] ? 'checked' : ''; ?>
                                       onchange="this.form.submit()">
                                <i class="fas <?php echo $cat['icon']; ?>"></i>
                                <span><?php echo htmlspecialchars($cat['name']); ?></span>
                            </label>
                        <?php endwhile; ?>
                        <?php if ($category): ?>
                            <button type="button" class="btn btn-sm btn-outline" onclick="window.location.href='events.php'">
                                Clear Filter
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- City Filter -->
                <div class="filter-section">
                    <h3 class="filter-title">
                        <i class="fas fa-map-marker-alt"></i>
                        City
                    </h3>
                    <select name="city" class="form-control" onchange="this.form.submit()">
                        <option value="">All Cities</option>
                        <?php while ($city_row = $cities->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($city_row['venue_city']); ?>" 
                                    <?php echo $city === $city_row['venue_city'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($city_row['venue_city']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="filter-section">
                    <h3 class="filter-title">
                        <i class="fas fa-calendar"></i>
                        Date Range
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
                        <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                    </div>
                </div>

                <?php if ($search): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
            </form>
        </aside>

        <!-- Events Content -->
        <div class="events-content">
            <div class="events-toolbar">
                <div class="results-count">
                    <?php echo $total_events; ?> event<?php echo $total_events != 1 ? 's' : ''; ?> found
                </div>
                
                <form method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
                    <?php if ($category): ?><input type="hidden" name="category" value="<?php echo $category; ?>"><?php endif; ?>
                    <?php if ($search): ?><input type="hidden" name="search" value="<?php echo $search; ?>"><?php endif; ?>
                    <?php if ($city): ?><input type="hidden" name="city" value="<?php echo $city; ?>"><?php endif; ?>
                    <?php if ($date_from): ?><input type="hidden" name="date_from" value="<?php echo $date_from; ?>"><?php endif; ?>
                    <?php if ($date_to): ?><input type="hidden" name="date_to" value="<?php echo $date_to; ?>"><?php endif; ?>
                    
                    <label>Sort by:</label>
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="date_asc" <?php echo $sort === 'date_asc' ? 'selected' : ''; ?>>Date (Earliest)</option>
                        <option value="date_desc" <?php echo $sort === 'date_desc' ? 'selected' : ''; ?>>Date (Latest)</option>
                        <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
                        <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                        <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                    </select>
                </form>
            </div>

            <?php if ($events && $events->num_rows > 0): ?>
                <div class="events-grid">
                    <?php while ($event = $events->fetch_assoc()): ?>
                        <div class="event-card">
                            <div class="event-image">
                                <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $event['slug']; ?>">
                                    <img src="<?php echo SITE_URL; ?>/uploads/events/<?php echo htmlspecialchars($event['featured_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($event['title']); ?>"
                                         onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder-event.jpg'">
                                </a>
                                <button class="wishlist-btn<?php echo $event['in_wishlist'] > 0 ? ' active' : ''; ?>" data-event-id="<?php echo $event['id']; ?>">
                                    <i class="fa<?php echo $event['in_wishlist'] > 0 ? 's' : 'r'; ?> fa-heart"></i>
                                </button>
                                <?php if ($event['featured']): ?>
                                    <div class="event-badge">Featured</div>
                                <?php endif; ?>
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
                                        <span><?php echo htmlspecialchars($event['venue_name']); ?>, <?php echo htmlspecialchars($event['venue_city']); ?></span>
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
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php
                        $query_params = array_filter([
                            'category' => $category,
                            'search' => $search,
                            'city' => $city,
                            'date_from' => $date_from,
                            'date_to' => $date_to,
                            'sort' => $sort
                        ]);
                        
                        function buildUrl($page, $params) {
                            $params['page'] = $page;
                            return '?'. http_build_query($params);
                        }
                        ?>
                        
                        <button class="pagination-btn" <?php echo $page <= 1 ? 'disabled' : ''; ?> 
                                onclick="window.location.href='<?php echo buildUrl($page - 1, $query_params); ?>'">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <button class="pagination-btn <?php echo $i === $page ? 'active' : ''; ?>"
                                    onclick="window.location.href='<?php echo buildUrl($i, $query_params); ?>'">
                                <?php echo $i; ?>
                            </button>
                        <?php endfor; ?>
                        
                        <button class="pagination-btn" <?php echo $page >= $total_pages ? 'disabled' : ''; ?>
                                onclick="window.location.href='<?php echo buildUrl($page + 1, $query_params); ?>'">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No Events Found</h3>
                    <p>We couldn't find any events matching your criteria.</p>
                    <a href="<?php echo SITE_URL; ?>/events.php" class="btn btn-primary mt-2">
                        View All Events
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
