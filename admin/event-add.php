<?php
$page_title = 'Add Event - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean($_POST['title']);
    $slug = clean($_POST['slug']);
    $category_id = (int)$_POST['category_id'];
    $description = clean($_POST['description']);
    $event_date = clean($_POST['event_date']);
    $event_time = clean($_POST['event_time']);
    $venue_name = clean($_POST['venue_name']);
    $venue_address = clean($_POST['venue_address']);
    $venue_city = clean($_POST['venue_city']);
    $venue_state = clean($_POST['venue_state']);
    $venue_country = clean($_POST['venue_country']);
    $status = clean($_POST['status']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Generate slug if empty
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }
    
    // Validation
    if (empty($title) || empty($category_id) || empty($event_date) || empty($venue_name)) {
        $error = 'Please fill in all required fields';
    } else {
        // Check if slug already exists
        $check_slug = $conn->prepare("SELECT id FROM events WHERE slug = ?");
        $check_slug->bind_param('s', $slug);
        $check_slug->execute();
        if ($check_slug->get_result()->num_rows > 0) {
            $error = 'Slug already exists. Please use a different slug.';
        } else {
            // Handle image upload
            $featured_image = 'placeholder-event.jpg';
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $upload_dir = __DIR__ . '/../uploads/events/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($file_ext, $allowed_ext)) {
                    $new_filename = 'event_' . time() . '_' . uniqid() . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        $featured_image = $new_filename;
                    }
                }
            }
            
            // Insert event
            $insert = "INSERT INTO events (title, slug, category_id, description, event_date, event_time, 
                       venue_name, venue_address, venue_city, venue_state, venue_country, 
                       featured_image, status, featured) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param('ssissssssssssi', 
                $title, $slug, $category_id, $description, $event_date, $event_time,
                $venue_name, $venue_address, $venue_city, $venue_state, $venue_country,
                $featured_image, $status, $featured
            );
            
            if ($stmt->execute()) {
                $event_id = $conn->insert_id;
                setFlash('success', 'Event created successfully! Now add ticket types.');
                redirect(SITE_URL . '/admin/ticket-add.php?event_id=' . $event_id);
            } else {
                $error = 'Failed to create event';
            }
        }
    }
}

// Get categories
$categories = $conn->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC");

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.admin-container {
    max-width: 1000px;
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
    min-height: 120px;
    resize: vertical;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="main-content">
    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2rem; font-weight: 800;"><i class="fas fa-plus-circle"></i> Add New Event</h1>
            <a href="<?php echo SITE_URL; ?>/admin/events.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Events
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label required">Event Title</label>
                    <input type="text" name="title" class="form-input" required 
                           placeholder="e.g., Taylor Swift - The Eras Tour"
                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Slug (URL-friendly name)</label>
                        <input type="text" name="slug" class="form-input" 
                               placeholder="auto-generated-from-title"
                               value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                        <small style="color: var(--gray);">Leave empty to auto-generate</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" 
                              placeholder="Event description..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Event Image</label>
                    <input type="file" name="image" class="form-input" accept="image/*">
                    <small style="color: var(--gray);">Accepted: JPG, PNG, GIF (Max 5MB)</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Event Date</label>
                        <input type="date" name="event_date" class="form-input" required
                               value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Event Time</label>
                        <input type="time" name="event_time" class="form-input"
                               value="<?php echo htmlspecialchars($_POST['event_time'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required">Venue Name</label>
                    <input type="text" name="venue_name" class="form-input" required
                           placeholder="e.g., Axiata Arena"
                           value="<?php echo htmlspecialchars($_POST['venue_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Venue Address</label>
                    <input type="text" name="venue_address" class="form-input"
                           placeholder="Street address"
                           value="<?php echo htmlspecialchars($_POST['venue_address'] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="venue_city" class="form-input"
                               placeholder="e.g., Kuala Lumpur"
                               value="<?php echo htmlspecialchars($_POST['venue_city'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input type="text" name="venue_state" class="form-input"
                               placeholder="e.g., Selangor"
                               value="<?php echo htmlspecialchars($_POST['venue_state'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Country</label>
                    <input type="text" name="venue_country" class="form-input"
                           placeholder="Malaysia"
                           value="<?php echo htmlspecialchars($_POST['venue_country'] ?? 'Malaysia'); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="featured" value="1">
                            <span>Featured Event</span>
                        </label>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Create Event
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/events.php" class="btn btn-outline" style="flex: 1;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
