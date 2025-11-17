<?php
$page_title = 'Add Ticket Type - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

$event_id = (int)($_GET['event_id'] ?? 0);

if ($event_id <= 0) {
    setFlash('error', 'Invalid event ID');
    redirect(SITE_URL . '/admin/events.php');
}

// Get event
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
    $name = clean($_POST['name']);
    $price = (float)$_POST['price'];
    $available = (int)$_POST['available'];
    $description = clean($_POST['description']);
    
    if (empty($name) || $price < 0 || $available < 0) {
        $error = 'Please fill in all required fields';
    } else {
        $insert = "INSERT INTO ticket_types (event_id, name, price, quantity, available, description) 
                   VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param('isdiis', $event_id, $name, $price, $available, $available, $description);
        
        if ($stmt->execute()) {
            // Update event min/max prices
            $update_prices = "UPDATE events SET 
                min_price = (SELECT MIN(price) FROM ticket_types WHERE event_id = ?),
                max_price = (SELECT MAX(price) FROM ticket_types WHERE event_id = ?),
                total_seats = (SELECT SUM(quantity) FROM ticket_types WHERE event_id = ?),
                available_seats = (SELECT SUM(available) FROM ticket_types WHERE event_id = ?)
                WHERE id = ?";
            $price_stmt = $conn->prepare($update_prices);
            $price_stmt->bind_param('iiiii', $event_id, $event_id, $event_id, $event_id, $event_id);
            $price_stmt->execute();
            
            setFlash('success', 'Ticket type added successfully');
            redirect(SITE_URL . '/admin/event-edit.php?id=' . $event_id);
        } else {
            $error = 'Failed to add ticket type';
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
</style>

<main class="main-content">
    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2rem; font-weight: 800;"><i class="fas fa-plus"></i> Add Ticket Type</h1>
            <a href="<?php echo SITE_URL; ?>/admin/event-edit.php?id=<?php echo $event_id; ?>" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div style="background: #f8f9fa; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
            <strong>Event:</strong> <?php echo htmlspecialchars($event['title']); ?>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Ticket Name *</label>
                    <input type="text" name="name" class="form-input" required
                           placeholder="e.g., VIP, Regular, Student"
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Price (RM) *</label>
                        <input type="number" name="price" class="form-input" required
                               step="0.01" min="0"
                               placeholder="0.00"
                               value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Available Quantity *</label>
                        <input type="number" name="available" class="form-input" required
                               min="0"
                               placeholder="100"
                               value="<?php echo htmlspecialchars($_POST['available'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea"
                              placeholder="Ticket benefits, restrictions, etc."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Add Ticket Type
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/event-edit.php?id=<?php echo $event_id; ?>" 
                       class="btn btn-outline" style="flex: 1;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
