<?php
$page_title = 'Book Tickets';
require_once __DIR__ . '/config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    // Store POST data in session if this is a booking request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
        $_SESSION['pending_booking'] = $_POST;
    }
    $_SESSION['redirect_after_login'] = SITE_URL . '/booking.php';
    setFlash('error', 'Please login to book tickets');
    redirect(SITE_URL . '/auth/login.php');
}

// Check for pending booking data from session (after login redirect)
if (!isset($_POST['event_id']) && isset($_SESSION['pending_booking'])) {
    $_POST = $_SESSION['pending_booking'];
    $_SERVER['REQUEST_METHOD'] = 'POST';
    unset($_SESSION['pending_booking']);
}

// Validate POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['event_id'])) {
    setFlash('error', 'Invalid booking request');
    redirect(SITE_URL . '/events.php');
}

$event_id = (int)$_POST['event_id'];

// Get event details
$event_query = "SELECT e.*, c.name as category_name 
                FROM events e 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE e.id = ? AND e.status = 'published'";
$stmt = $conn->prepare($event_query);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    setFlash('error', 'Event not found');
    redirect(SITE_URL . '/events.php');
}

// Get selected tickets
$selected_tickets = [];
$total_tickets = 0;
$subtotal = 0;

// Get all ticket types for this event
$tickets_query = "SELECT * FROM ticket_types WHERE event_id = ? AND status = 'active' ORDER BY price ASC";
$stmt = $conn->prepare($tickets_query);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$tickets_result = $stmt->get_result();

while ($ticket = $tickets_result->fetch_assoc()) {
    $field_name = 'ticket_' . $ticket['id'];
    if (isset($_POST[$field_name]) && $_POST[$field_name] > 0) {
        $quantity = (int)$_POST[$field_name];
        
        // Check availability
        if (isset($ticket['available']) && $quantity > $ticket['available']) {
            setFlash('error', 'Not enough tickets available for ' . $ticket['name']);
            redirect(SITE_URL . '/event-details.php?slug=' . $event['slug']);
        }
        
        $selected_tickets[] = [
            'id' => $ticket['id'],
            'name' => $ticket['name'],
            'price' => $ticket['price'],
            'quantity' => $quantity,
            'subtotal' => $ticket['price'] * $quantity
        ];
        
        $total_tickets += $quantity;
        $subtotal += $ticket['price'] * $quantity;
    }
}

if (empty($selected_tickets)) {
    setFlash('error', 'Please select at least one ticket');
    redirect(SITE_URL . '/event-details.php?slug=' . $event['slug']);
}

// Calculate fees
$booking_fee = $subtotal * 0.05; // 5% booking fee
$total_amount = $subtotal + $booking_fee;

// Store booking data in session
$_SESSION['booking_data'] = [
    'event_id' => $event_id,
    'event' => $event,
    'tickets' => $selected_tickets,
    'total_tickets' => $total_tickets,
    'subtotal' => $subtotal,
    'booking_fee' => $booking_fee,
    'total_amount' => $total_amount
];

require_once __DIR__ . '/includes/header.php';
?>

<style>
.booking-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.booking-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
    margin-top: 2rem;
}

.booking-section {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
}

.booking-section h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.event-summary {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
}

.event-summary img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: var(--radius-md);
}

.event-summary-content h3 {
    font-size: 1.125rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--dark);
}

.event-summary-content p {
    color: var(--gray);
    font-size: 0.9rem;
    margin: 0.25rem 0;
}

.ticket-summary-item {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
}

.ticket-summary-item:last-child {
    border-bottom: none;
}

.ticket-summary-left {
    flex: 1;
}

.ticket-summary-name {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.25rem;
}

.ticket-summary-qty {
    color: var(--gray);
    font-size: 0.9rem;
}

.ticket-summary-price {
    font-weight: 700;
    color: var(--primary-color);
}

.order-summary {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    position: sticky;
    top: 100px;
}

.order-summary h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--dark);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    color: var(--gray);
}

.summary-total {
    display: flex;
    justify-content: space-between;
    padding-top: 1rem;
    margin-top: 1rem;
    border-top: 2px solid var(--border-color);
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark);
}

.breadcrumb {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.breadcrumb a {
    color: var(--gray);
    text-decoration: none;
}

.breadcrumb a:hover {
    color: var(--primary-color);
}

.breadcrumb span {
    color: var(--gray);
}

@media (max-width: 968px) {
    .booking-grid {
        grid-template-columns: 1fr;
    }
    
    .order-summary {
        position: static;
    }
    
    .event-summary {
        flex-direction: column;
    }
    
    .event-summary img {
        width: 100%;
        height: 200px;
    }
}

@media (max-width: 576px) {
    .booking-container {
        padding: 0 0.5rem;
    }
    
    .booking-section {
        padding: 1.5rem;
    }
    
    h1 {
        font-size: 1.5rem;
    }
}
</style>

<main class="main-content">
    <div class="booking-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>/index.php">Home</a>
            <span>/</span>
            <a href="<?php echo SITE_URL; ?>/events.php">Events</a>
            <span>/</span>
            <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $event['slug']; ?>"><?php echo htmlspecialchars($event['title']); ?></a>
            <span>/</span>
            <span>Booking</span>
        </div>

        <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 1rem;">Complete Your Booking</h1>

        <div class="booking-grid">
            <!-- Left Column - Customer Details -->
            <div>
                <!-- Event Summary -->
                <div class="booking-section">
                    <h2><i class="fas fa-calendar-check"></i> Event Details</h2>
                    <div class="event-summary">
                        <img src="<?php echo SITE_URL . '/uploads/events/' . htmlspecialchars($event['featured_image'] ?? 'placeholder.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($event['title']); ?>"
                             onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder-event.jpg'">
                        <div class="event-summary-content">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p><i class="fas fa-calendar"></i> <?php echo formatDate($event['event_date']); ?> at <?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['venue_name']); ?>, <?php echo htmlspecialchars($event['venue_city'] ?? ''); ?></p>
                        </div>
                    </div>

                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Selected Tickets</h3>
                    <div>
                        <?php foreach ($selected_tickets as $ticket): ?>
                            <div class="ticket-summary-item">
                                <div class="ticket-summary-left">
                                    <div class="ticket-summary-name"><?php echo htmlspecialchars($ticket['name']); ?></div>
                                    <div class="ticket-summary-qty">Quantity: <?php echo $ticket['quantity']; ?></div>
                                </div>
                                <div class="ticket-summary-price">
                                    <?php echo formatPrice($ticket['subtotal']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Customer Information Form -->
                <div class="booking-section" style="margin-top: 2rem;">
                    <h2><i class="fas fa-user"></i> Customer Information</h2>
                    <form action="<?php echo SITE_URL; ?>/payment.php" method="POST" id="bookingForm">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="customer_name" class="form-control" required 
                                   value="<?php echo htmlspecialchars(getCurrentUser()['full_name']); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="customer_email" class="form-control" required 
                                   value="<?php echo htmlspecialchars(getCurrentUser()['email']); ?>">
                            <small class="text-muted">Your tickets will be sent to this email</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" name="customer_phone" class="form-control" required 
                                   value="<?php echo htmlspecialchars(getCurrentUser()['phone'] ?? ''); ?>"
                                   placeholder="+60 12-345 6789">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Special Requests (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Any special requirements or notes..."></textarea>
                        </div>

                        <div class="form-check" style="margin-top: 1.5rem;">
                            <input type="checkbox" name="terms" id="terms" class="form-check-input" required>
                            <label for="terms">
                                I agree to the <a href="#" style="color: var(--primary-color);">Terms & Conditions</a> 
                                and understand the <a href="#" style="color: var(--primary-color);">Refund Policy</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 2rem; font-size: 1.125rem; padding: 1rem;">
                            <i class="fas fa-lock"></i>
                            Proceed to Payment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div>
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal (<?php echo $total_tickets; ?> ticket<?php echo $total_tickets > 1 ? 's' : ''; ?>)</span>
                        <span><?php echo formatPrice($subtotal); ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Booking Fee (5%)</span>
                        <span><?php echo formatPrice($booking_fee); ?></span>
                    </div>

                    <div class="summary-total">
                        <span>Total Amount</span>
                        <span><?php echo formatPrice($total_amount); ?></span>
                    </div>

                    <div style="margin-top: 2rem; padding: 1rem; background: #f8f9fa; border-radius: var(--radius-md);">
                        <p style="font-size: 0.875rem; color: var(--gray); margin: 0; line-height: 1.6;">
                            <i class="fas fa-shield-alt" style="color: var(--success);"></i>
                            <strong>Secure Payment</strong><br>
                            Your payment information is encrypted and secure
                        </p>
                    </div>

                    <div style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: var(--radius-md);">
                        <p style="font-size: 0.875rem; color: var(--gray); margin: 0; line-height: 1.6;">
                            <i class="fas fa-ticket-alt" style="color: var(--primary-color);"></i>
                            <strong>Instant E-Tickets</strong><br>
                            Tickets will be sent to your email immediately
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
