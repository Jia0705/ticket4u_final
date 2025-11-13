<?php
$page_title = 'Manage Categories - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

// Get all categories with event count
$categories_query = "SELECT c.*,
                     COUNT(e.id) as event_count
                     FROM categories c
                     LEFT JOIN events e ON c.id = e.category_id
                     GROUP BY c.id
                     ORDER BY c.display_order ASC";
$categories = $conn->query($categories_query);

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.admin-container {
    max-width: 1200px;
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

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.category-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 1.5rem;
    transition: var(--transition-base);
}

.category-card:hover {
    box-shadow: var(--shadow-xl);
    transform: translateY(-2px);
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.category-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.category-status {
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-md);
    font-size: 0.8rem;
    font-weight: 600;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.category-name {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 1rem 0 0.5rem;
    color: var(--dark);
}

.category-desc {
    color: var(--gray);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.category-stats {
    display: flex;
    gap: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid var(--light);
}

.stat {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--primary-color);
}

.stat-label {
    font-size: 0.8rem;
    color: var(--gray);
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<main class="main-content">
    <div class="admin-container">
        <div class="page-header">
            <h1><i class="fas fa-tags"></i> Manage Categories</h1>
            <div style="display: flex; gap: 1rem;">
                <a href="<?php echo SITE_URL; ?>/admin/category-add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Category
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <div class="categories-grid">
            <?php if ($categories->num_rows > 0): ?>
                <?php while ($category = $categories->fetch_assoc()): ?>
                    <div class="category-card">
                        <div class="category-header">
                            <div class="category-icon">
                                <i class="fas <?php echo htmlspecialchars($category['icon']); ?>"></i>
                            </div>
                            <span class="category-status status-<?php echo $category['status']; ?>">
                                <?php echo ucfirst($category['status']); ?>
                            </span>
                        </div>

                        <h3 class="category-name"><?php echo htmlspecialchars($category['name']); ?></h3>
                        <p class="category-desc"><?php echo htmlspecialchars($category['description'] ?? 'No description'); ?></p>

                        <div class="category-stats">
                            <div class="stat">
                                <div class="stat-value"><?php echo $category['event_count']; ?></div>
                                <div class="stat-label">Events</div>
                            </div>
                            <div class="stat">
                                <div class="stat-value">#<?php echo $category['display_order']; ?></div>
                                <div class="stat-label">Order</div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 0.75rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--light);">
                            <a href="<?php echo SITE_URL; ?>/admin/category-edit.php?id=<?php echo $category['id']; ?>" 
                               class="btn btn-sm btn-outline" style="flex: 1;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="<?php echo SITE_URL; ?>/admin/category-delete.php?id=<?php echo $category['id']; ?>" 
                               class="btn btn-sm btn-danger" style="flex: 1;"
                               onclick="return confirm('Are you sure you want to delete this category?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; background: white; border-radius: var(--radius-lg);">
                    <i class="fas fa-tags" style="font-size: 4rem; color: var(--gray); margin-bottom: 1rem;"></i>
                    <h2>No Categories Found</h2>
                    <p style="color: var(--gray);">Start by creating your first category.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
