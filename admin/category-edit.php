<?php
$page_title = 'Edit Category - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

$error = '';

// Get category ID
$category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$category_id) {
    setFlash('error', 'Invalid category ID.');
    redirect(SITE_URL . '/admin/categories.php');
}

// Fetch category details
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    setFlash('error', 'Category not found.');
    redirect(SITE_URL . '/admin/categories.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = !empty($_POST['slug']) ? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug']))) : strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fa-ticket');
    $display_order = filter_input(INPUT_POST, 'display_order', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? 'active';

    // Validate required fields
    if (empty($name)) {
        $error = 'Category name is required.';
    } else {
        // Check if slug is unique (except for this category)
        $slug_check = $conn->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
        $slug_check->bind_param("si", $slug, $category_id);
        $slug_check->execute();
        $slug_exists = $slug_check->get_result();

        if ($slug_exists->num_rows > 0) {
            $error = 'A category with this slug already exists.';
        } else {
            // Update category
            $update_stmt = $conn->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, icon = ?, display_order = ?, status = ? WHERE id = ?");
            $update_stmt->bind_param("ssssisi", $name, $slug, $description, $icon, $display_order, $status, $category_id);
            
            if ($update_stmt->execute()) {
                setFlash('success', 'Category updated successfully!');
                redirect(SITE_URL . '/admin/categories.php');
            } else {
                $error = 'Failed to update category: ' . $conn->error;
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.admin-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 0.5rem;
}

.breadcrumb {
    display: flex;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--gray);
}

.breadcrumb a {
    color: var(--primary-color);
    text-decoration: none;
}

.form-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--dark);
}

.form-label.required::after {
    content: ' *';
    color: #f5576c;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.875rem;
    border: 2px solid var(--light);
    border-radius: var(--radius-md);
    font-size: 1rem;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--primary-color);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.icon-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 0.5rem;
}

.icon-display {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="main-content">
    <div class="admin-container">
        <div class="page-header">
            <h1><i class="fas fa-edit"></i> Edit Category</h1>
            <div class="breadcrumb">
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">Dashboard</a>
                <span>/</span>
                <a href="<?php echo SITE_URL; ?>/admin/categories.php">Categories</a>
                <span>/</span>
                <span>Edit</span>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label required">Category Name</label>
                    <input type="text" name="name" class="form-input" required 
                           placeholder="e.g., Concerts"
                           value="<?php echo htmlspecialchars($category['name']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Slug (URL-friendly name)</label>
                    <input type="text" name="slug" class="form-input" 
                           placeholder="e.g., concerts (leave empty to auto-generate)"
                           value="<?php echo htmlspecialchars($category['slug']); ?>">
                    <small style="color: var(--gray);">Used in URLs. Only lowercase letters, numbers, and hyphens.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" 
                              placeholder="Brief description of this category..."><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Icon (FontAwesome class)</label>
                    <input type="text" name="icon" id="iconInput" class="form-input" 
                           placeholder="e.g., fa-music"
                           value="<?php echo htmlspecialchars($category['icon']); ?>">
                    <small style="color: var(--gray);">Enter FontAwesome icon class (e.g., fa-music, fa-ticket, fa-theater-masks)</small>
                    <div class="icon-preview">
                        <div class="icon-display">
                            <i id="iconPreview" class="fas <?php echo htmlspecialchars($category['icon']); ?>"></i>
                        </div>
                        <span style="color: var(--gray);">Icon preview</span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-input" min="0" 
                               value="<?php echo $category['display_order']; ?>">
                        <small style="color: var(--gray);">Lower numbers appear first</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?php echo $category['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $category['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Category
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
// Real-time icon preview
document.getElementById('iconInput').addEventListener('input', function() {
    const iconClass = this.value.trim() || 'fa-ticket';
    document.getElementById('iconPreview').className = 'fas ' + iconClass;
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
