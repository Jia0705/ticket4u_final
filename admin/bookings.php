<?php
$page_title = 'Manage Bookings - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT b.*, u.name as user_name, u.email as user_email, e.title as event_title, e.event_date
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          JOIN events e ON b.event_id = e.id
          WHERE 1=1";

if ($status_filter !== 'all') {
    $query .= " AND b.booking_status = '" . $conn->real_escape_string($status_filter) . "'";
}

if (!empty($search)) {
    $search_term = $conn->real_escape_string($search);
    $query .= " AND (b.booking_reference LIKE '%$search_term%' 
                OR u.name LIKE '%$search_term%' 
                OR e.title LIKE '%$search_term%')";
}

$query .= " ORDER BY b.created_at DESC";
$bookings = $conn->query($query);

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

.filters-bar {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 1.5rem;
    margin-bottom: 2rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--dark);
    font-size: 0.9rem;
}

.filter-select,
.filter-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--light);
    border-radius: var(--radius-md);
    font-size: 1rem;
}

.bookings-table {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
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
    font-size: 0.9rem;
}

tr:hover {
    background: var(--light);
}

.status-badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-md);
    font-size: 0.8rem;
    font-weight: 600;
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

@media (max-width: 968px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    table {
        font-size: 0.8rem;
    }
    
    th, td {
        padding: 0.75rem 0.5rem;
    }
}
</style>

<main class="main-content">
    <div class="admin-container">
        <div class="page-header">
            <h1><i class="fas fa-ticket-alt"></i> Manage Bookings</h1>
            <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <form class="filters-bar" method="GET">
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div class="filter-group" style="flex: 2;">
                <label class="filter-label">Search</label>
                <input type="text" name="search" class="filter-input" 
                       placeholder="Search by reference, customer, or event..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <div class="filter-group" style="flex: 0; min-width: auto;">
                <label class="filter-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>

        <div class="bookings-table">
            <div class="table-header">
                <h2 style="margin: 0; font-size: 1.25rem;">
                    <i class="fas fa-list"></i> All Bookings
                </h2>
                <span><?php echo $bookings->num_rows; ?> bookings</span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Event</th>
                        <th>Event Date</th>
                        <th>Tickets</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Booked On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookings->num_rows > 0): ?>
                        <?php while ($booking = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($booking['user_name']); ?><br>
                                    <small style="color: var(--gray);"><?php echo htmlspecialchars($booking['user_email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($booking['event_title']); ?></td>
                                <td><?php echo formatDate($booking['event_date']); ?></td>
                                <td><?php echo $booking['total_tickets']; ?></td>
                                <td><strong><?php echo formatPrice($booking['total_amount']); ?></strong></td>
                                <td>
                                    <span class="status-badge status-<?php echo $booking['booking_status']; ?>">
                                        <?php echo ucfirst($booking['booking_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($booking['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: var(--gray);">
                                <i class="fas fa-ticket-alt" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                No bookings found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
