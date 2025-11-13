<?php
$page_title = 'Manage Users - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

// Get all users with booking stats
$users_query = "SELECT u.*,
                COUNT(DISTINCT b.id) as booking_count,
                COALESCE(SUM(b.total_amount), 0) as total_spent
                FROM users u
                LEFT JOIN bookings b ON u.id = b.user_id
                GROUP BY u.id
                ORDER BY u.created_at DESC";
$users = $conn->query($users_query);

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

.users-table {
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
}

tr:hover {
    background: var(--light);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    margin-right: 0.75rem;
}

.role-badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-md);
    font-size: 0.8rem;
    font-weight: 600;
}

.role-admin {
    background: #f093fb;
    color: white;
}

.role-user {
    background: #d4edda;
    color: #155724;
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
}
</style>

<main class="main-content">
    <div class="admin-container">
        <div class="page-header">
            <h1><i class="fas fa-users"></i> Manage Users</h1>
            <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="users-table">
            <div class="table-header">
                <h2 style="margin: 0; font-size: 1.25rem;">
                    <i class="fas fa-list"></i> All Users
                </h2>
                <span><?php echo $users->num_rows; ?> total users</span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Bookings</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users->num_rows > 0): ?>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                        </div>
                                        <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo $user['booking_count']; ?></td>
                                <td><strong><?php echo formatPrice($user['total_spent']); ?></strong></td>
                                <td><?php echo formatDate($user['created_at']); ?></td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="<?php echo SITE_URL; ?>/admin/user-edit.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-outline" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="<?php echo SITE_URL; ?>/admin/user-delete.php?id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-danger" title="Delete User"
                                               onclick="return confirm('Are you sure you want to delete this user? This will also delete all their bookings.');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: var(--gray);">
                                <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                No users found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
