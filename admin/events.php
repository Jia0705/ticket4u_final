<?php
$page_title = 'Manage Events - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

// Get all events with category info
$events_query = "SELECT e.*, c.name as category_name,
                 (SELECT COUNT(*) FROM bookings WHERE event_id = e.id) as booking_count
                 FROM events e
                 LEFT JOIN categories c ON e.category_id = c.id
                 ORDER BY e.created_at DESC";
$events = $conn->query($events_query);

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.admin-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark);
}

.events-table {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: var(--radius-lg);
}

.table-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: var(--light);
}

th {
    padding: 1rem;
    text-align: left;
    font-weight: 700;
    color: var(--dark);
    font-size: 0.9rem;
}

td {
    padding: 1rem;
    border-bottom: 1px solid var(--light);
}

tr:hover {
    background: var(--light);
}

.event-thumb {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    object-fit: cover;
}

.status-badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-md);
    font-size: 0.8rem;
    font-weight: 600;
}

.status-published {
    background: #d4edda;
    color: #155724;
}

.status-draft {
    background: #fff3cd;
    color: #856404;
}

.action-btns {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border-radius: var(--radius-md);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: var(--transition-base);
    text-decoration: none;
}

.btn-edit {
    background: #667eea;
    color: white;
}

.btn-delete {
    background: #f5576c;
    color: white;
}

.btn-icon:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

@media (max-width: 968px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    table {
        font-size: 0.875rem;
    }
    
    th, td {
        padding: 0.75rem 0.5rem;
    }
    
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table-responsive table {
        min-width: 800px;
    }
}
</style>

<main class="main-content">
    <div class="admin-container">
        <div class="page-header">
            <h1><i class="fas fa-calendar"></i> Manage Events</h1>
            <div style="display: flex; gap: 1rem;">
                <a href="<?php echo SITE_URL; ?>/admin/event-add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Event
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <div class="events-table">
            <div class="table-header">
                <h2 style="margin: 0; font-size: 1.25rem;">
                    <i class="fas fa-list"></i> All Events
                </h2>
                <span><?php echo $events->num_rows; ?> total events</span>
            </div>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Bookings</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($events->num_rows > 0): ?>
                        <?php while ($event = $events->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="<?php echo SITE_URL . '/uploads/events/' . htmlspecialchars($event['featured_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($event['title']); ?>"
                                             class="event-thumb"
                                             onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder-event.jpg'">
                                        <div>
                                            <strong><?php echo htmlspecialchars($event['title']); ?></strong><br>
                                            <small style="color: var(--gray);"><?php echo htmlspecialchars($event['venue_city']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($event['category_name']); ?></td>
                                <td><?php echo formatDate($event['event_date']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $event['status']; ?>">
                                        <?php echo ucfirst($event['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo $event['booking_count']; ?></strong> bookings
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $event['slug']; ?>" 
                                           class="btn-icon" style="background: #11998e;" title="View" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/admin/event-edit.php?id=<?php echo $event['id']; ?>" 
                                           class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/admin/event-delete.php?id=<?php echo $event['id']; ?>" 
                                           class="btn-icon btn-delete" title="Delete" 
                                           onclick="return confirm('Are you sure you want to delete this event?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: var(--gray);">
                                <i class="fas fa-calendar" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                No events found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</main>

<script>
function confirmDelete(eventTitle) {
    if (confirm(`Are you sure you want to delete "${eventTitle}"?\n\nThis action cannot be undone.`)) {
        alert('Delete feature coming soon! Event: ' + eventTitle);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
