<?php
$page_title = 'Edit Event - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

$event_id = (int)($_GET['id'] ?? 0);

if ($event_id <= 0) {
    setFlash('error', 'Invalid event ID');
    redirect(SITE_URL . '/admin/events.php');
}

// Get event details
$event_query = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($event_query);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    setFlash('error', 'Event not found');
    redirect(SITE_URL . '/admin/events.php');
}

$error = '';

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
    
    // Validation
    if (empty($title) || empty($category_id) || empty($event_date) || empty($venue_name)) {
        $error = 'Please fill in all required fields';
    } else {
        // Check if slug already exists (excluding current event)
        $check_slug = $conn->prepare("SELECT id FROM events WHERE slug = ? AND id != ?");
        $check_slug->bind_param('si', $slug, $event_id);
        $check_slug->execute();
        if ($check_slug->get_result()->num_rows > 0) {
            $error = 'Slug already exists. Please use a different slug.';
        } else {
            // Handle image upload
            $featured_image = $event['featured_image']; // Keep existing image
            
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
                        // Delete old image if not placeholder
                        if ($event['featured_image'] != 'placeholder-event.jpg' && file_exists($upload_dir . $event['featured_image'])) {
                            unlink($upload_dir . $event['featured_image']);
                        }
                        $featured_image = $new_filename;
                    }
                }
            }
            
            // Update event
            $update = "UPDATE events SET 
                       title = ?, slug = ?, category_id = ?, description = ?, 
                       event_date = ?, event_time = ?, venue_name = ?, venue_address = ?, 
                       venue_city = ?, venue_state = ?, venue_country = ?, featured_image = ?, status = ?, featured = ?
                       WHERE id = ?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param('ssissssssssssii', 
                $title, $slug, $category_id, $description, $event_date, $event_time,
                $venue_name, $venue_address, $venue_city, $venue_state, $venue_country,
                $featured_image, $status, $featured, $event_id
            );
            
            if ($stmt->execute()) {
                setFlash('success', 'Event updated successfully!');
                redirect(SITE_URL . '/admin/events.php');
            } else {
                $error = 'Failed to update event';
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
            <h1 style="font-size: 2rem; font-weight: 800;"><i class="fas fa-edit"></i> Edit Event</h1>
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
                           value="<?php echo htmlspecialchars($event['title']); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Slug (URL-friendly name)</label>
                        <input type="text" name="slug" class="form-input" 
                               value="<?php echo htmlspecialchars($event['slug']); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo $event['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" 
                              placeholder="Event description..."><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Event Image</label>
                    <?php if ($event['featured_image']): ?>
                        <div style="margin-bottom: 1rem;">
                            <img src="<?php echo SITE_URL . '/uploads/events/' . htmlspecialchars($event['featured_image']); ?>" 
                                 alt="Current image" 
                                 style="max-width: 300px; border-radius: var(--radius-md);"
                                 onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder-event.jpg'">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-input" accept="image/*">
                    <small style="color: var(--gray);">Accepted: JPG, PNG, GIF (Max 5MB). Leave empty to keep current image.</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Event Date</label>
                        <input type="date" name="event_date" class="form-input" required
                               value="<?php echo htmlspecialchars($event['event_date']); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Event Time</label>
                        <input type="time" name="event_time" class="form-input"
                               value="<?php echo htmlspecialchars($event['event_time'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required">Venue Name</label>
                    <input type="text" name="venue_name" class="form-input" required
                           placeholder="e.g., Axiata Arena"
                           value="<?php echo htmlspecialchars($event['venue_name']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Venue Address</label>
                    <input type="text" name="venue_address" class="form-input"
                           placeholder="Street address"
                           value="<?php echo htmlspecialchars($event['venue_address'] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="venue_city" class="form-input"
                               placeholder="e.g., Kuala Lumpur"
                               value="<?php echo htmlspecialchars($event['venue_city'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input type="text" name="venue_state" class="form-input"
                               placeholder="e.g., Selangor"
                               value="<?php echo htmlspecialchars($event['venue_state'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Country</label>
                    <input type="text" name="venue_country" class="form-input"
                           placeholder="Malaysia"
                           value="<?php echo htmlspecialchars($event['venue_country'] ?? 'Malaysia'); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="draft" <?php echo $event['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo $event['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="featured" value="1" 
                                   <?php echo $event['featured'] ? 'checked' : ''; ?>>
                            <span>Featured Event</span>
                        </label>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Update Event
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/events.php" class="btn btn-outline" style="flex: 1;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Ticket Types Section -->
        <div class="form-card" style="margin-top: 2rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;">
                <i class="fas fa-ticket-alt"></i> Ticket Types
            </h2>
            
            <?php
            $tickets_query = "SELECT * FROM ticket_types WHERE event_id = ? ORDER BY price ASC";
            $stmt = $conn->prepare($tickets_query);
            $stmt->bind_param('i', $event_id);
            $stmt->execute();
            $tickets = $stmt->get_result();
            ?>
            
            <?php if ($tickets->num_rows > 0): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--light);">
                            <th style="padding: 1rem; text-align: left;">Name</th>
                            <th style="padding: 1rem; text-align: left;">Price</th>
                            <th style="padding: 1rem; text-align: left;">Available</th>
                            <th style="padding: 1rem; text-align: left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($ticket = $tickets->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid var(--light);">
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($ticket['name']); ?></td>
                                <td style="padding: 1rem;"><?php echo formatPrice($ticket['price']); ?></td>
                                <td style="padding: 1rem;"><?php echo $ticket['available']; ?></td>
                                <td style="padding: 1rem;">
                                    <a href="<?php echo SITE_URL; ?>/admin/ticket-delete.php?id=<?php echo $ticket['id']; ?>&event_id=<?php echo $event_id; ?>" 
                                       class="btn btn-sm" style="background: #f5576c; color: white;"
                                       onclick="return confirm('Delete this ticket type?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--gray); text-align: center; padding: 2rem;">No ticket types added yet</p>
            <?php endif; ?>
            
            <a href="<?php echo SITE_URL; ?>/admin/ticket-add.php?event_id=<?php echo $event_id; ?>" 
               class="btn btn-primary" style="margin-top: 1rem; width: 100%;">
                <i class="fas fa-plus"></i> Add Ticket Type
            </a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
