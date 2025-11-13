<?php
$page_title = 'Add Category - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name']);
    $slug = clean($_POST['slug']);
    $icon = clean($_POST['icon']);
    $description = clean($_POST['description']);
    $status = clean($_POST['status']);
    $display_order = (int)$_POST['display_order'];
    
    // Generate slug if empty
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }
    
    if (empty($name)) {
        $error = 'Category name is required';
    } else {
        // Check if slug exists
        $check_slug = $conn->prepare("SELECT id FROM categories WHERE slug = ?");
        $check_slug->bind_param('s', $slug);
        $check_slug->execute();
        if ($check_slug->get_result()->num_rows > 0) {
            $error = 'Slug already exists';
        } else {
            $insert = "INSERT INTO categories (name, slug, icon, description, status, display_order) 
                       VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param('sssssi', $name, $slug, $icon, $description, $status, $display_order);
            
            if ($stmt->execute()) {
                setFlash('success', 'Category created successfully');
                redirect(SITE_URL . '/admin/categories.php');
            } else {
                $error = 'Failed to create category';
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

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.875rem;
    border: 2px solid var(--light);
    border-radius: var(--radius-md);
    font-size: 1rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.icon-preview {
    font-size: 3rem;
    color: var(--primary-color);
    text-align: center;
    padding: 1rem;
    background: var(--light);
    border-radius: var(--radius-md);
    margin-top: 0.5rem;
}
</style>

<main class="main-content">
    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2rem; font-weight: 800;"><i class="fas fa-plus"></i> Add Category</h1>
            <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" class="form-input" required
                           placeholder="e.g., Concerts"
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-input"
                           placeholder="auto-generated"
                           value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                    <small style="color: var(--gray);">URL-friendly name (leave empty to auto-generate)</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Icon (Font Awesome class)</label>
                    <input type="text" name="icon" class="form-input" id="iconInput"
                           placeholder="e.g., fa-music"
                           value="<?php echo htmlspecialchars($_POST['icon'] ?? 'fa-tag'); ?>"
                           onkeyup="updateIconPreview()">
                    <small style="color: var(--gray);">Visit <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a> for icons</small>
                    <div class="icon-preview" id="iconPreview">
                        <i class="fas fa-tag"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" 
                              placeholder="Category description..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-input" 
                               value="<?php echo htmlspecialchars($_POST['display_order'] ?? '1'); ?>">
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Create Category
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-outline" style="flex: 1;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function updateIconPreview() {
    const iconInput = document.getElementById('iconInput');
    const iconPreview = document.getElementById('iconPreview');
    const iconClass = iconInput.value || 'fa-tag';
    iconPreview.innerHTML = '<i class="fas ' + iconClass + '"></i>';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
