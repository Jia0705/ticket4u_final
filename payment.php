<?php
$page_title = 'Payment';
require_once __DIR__ . '/config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('error', 'Please login to continue');
    redirect(SITE_URL . '/auth/login.php');
}

// Check if booking data exists in session
if (!isset($_SESSION['booking_data'])) {
    setFlash('error', 'No booking data found. Please start booking again.');
    redirect(SITE_URL . '/events.php');
}

$booking_data = $_SESSION['booking_data'];
$event = $booking_data['event'];
$tickets = $booking_data['tickets'];
$total_tickets = $booking_data['total_tickets'];
$subtotal = $booking_data['subtotal'];
$booking_fee = $booking_data['booking_fee'];
$total_amount = $booking_data['total_amount'];

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = clean($_POST['customer_name'] ?? '');
    $customer_email = clean($_POST['customer_email'] ?? '');
    $customer_phone = clean($_POST['customer_phone'] ?? '');
    $payment_method = clean($_POST['payment_method'] ?? '');
    $notes = clean($_POST['notes'] ?? '');
    
    // Validation
    if (empty($customer_name) || empty($customer_email) || empty($customer_phone) || empty($payment_method)) {
        $error = 'Please fill in all required fields';
    } elseif (!in_array($payment_method, ['card', 'fpx', 'ewallet'])) {
        $error = 'Invalid payment method';
    } else {
        // Generate booking reference
        $booking_reference = 'TKT' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert booking
            $insert_booking = "INSERT INTO bookings (
                user_id, event_id, booking_reference, total_tickets, 
                subtotal, booking_fee, total_amount, payment_method, 
                payment_status, customer_name, customer_email, customer_phone, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'paid', ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($insert_booking);
            $user_id = getCurrentUser()['id'];
            $stmt->bind_param(
                'iisidddsssss',
                $user_id,
                $event['id'],
                $booking_reference,
                $total_tickets,
                $subtotal,
                $booking_fee,
                $total_amount,
                $payment_method,
                $customer_name,
                $customer_email,
                $customer_phone,
                $notes
            );
            $stmt->execute();
            $booking_id = $conn->insert_id;
            
            // Insert booking items
            foreach ($tickets as $ticket) {
                $insert_item = "INSERT INTO booking_items (booking_id, ticket_type_id, quantity, unit_price, subtotal) 
                               VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_item);
                $stmt->bind_param('iiidd', $booking_id, $ticket['id'], $ticket['quantity'], $ticket['price'], $ticket['subtotal']);
                $stmt->execute();
                
                // Update ticket availability
                $update_tickets = "UPDATE ticket_types SET available = available - ? WHERE id = ?";
                $stmt = $conn->prepare($update_tickets);
                $stmt->bind_param('ii', $ticket['quantity'], $ticket['id']);
                $stmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Clear booking data from session
            unset($_SESSION['booking_data']);
            
            // Store booking reference for success page
            $_SESSION['booking_success'] = [
                'reference' => $booking_reference,
                'booking_id' => $booking_id
            ];
            
            $success = true;
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Booking failed. Please try again.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
.payment-container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.payment-section {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
}

.payment-section h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.payment-method {
    position: relative;
}

.payment-method input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.payment-method label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: var(--transition-base);
    background: white;
}

.payment-method input[type="radio"]:checked + label {
    border-color: var(--primary-color);
    background: rgba(102, 126, 234, 0.05);
}

.payment-method label:hover {
    border-color: var(--primary-color);
}

.payment-icon {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.payment-name {
    font-weight: 600;
    color: var(--dark);
}

.order-summary-box {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    color: var(--gray);
}

.summary-total {
    display: flex;
    justify-content: space-between;
    padding-top: 1rem;
    margin-top: 1rem;
    border-top: 2px solid var(--border-color);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
}

.success-message {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem;
    border-radius: var(--radius-xl);
    text-align: center;
}

.success-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.success-message h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.booking-ref {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 1rem 0;
    padding: 1rem;
    background: rgba(255,255,255,0.2);
    border-radius: var(--radius-md);
}
</style>

<main class="main-content">
    <div class="payment-container">
        <?php if ($success): ?>
            <!-- Success Message -->
            <div class="success-message">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1>Booking Confirmed!</h1>
                <p style="font-size: 1.125rem; margin-bottom: 1rem;">Your tickets have been successfully booked</p>
                <div class="booking-ref">
                    Booking Reference: <?php echo $_SESSION['booking_success']['reference']; ?>
                </div>
                <p style="margin-bottom: 2rem;">
                    We've sent a confirmation email to <strong><?php echo htmlspecialchars($customer_email); ?></strong><br>
                    with your e-tickets and booking details.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="<?php echo SITE_URL; ?>/profile/bookings.php" class="btn btn-lg" style="background: white; color: var(--primary-color);">
                        <i class="fas fa-ticket-alt"></i> View My Bookings
                    </a>
                    <a href="<?php echo SITE_URL; ?>/events.php" class="btn btn-outline btn-lg" style="border-color: white; color: white;">
                        <i class="fas fa-calendar"></i> Browse More Events
                    </a>
                </div>
            </div>
            
            <div class="payment-section" style="margin-top: 2rem;">
                <h2><i class="fas fa-info-circle"></i> What's Next?</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div style="padding: 1rem; background: #f8f9fa; border-radius: var(--radius-md);">
                        <div style="font-size: 2rem; color: var(--primary-color); margin-bottom: 0.5rem;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem;">Check Your Email</h3>
                        <p style="color: var(--gray); font-size: 0.9rem;">Your e-tickets have been sent to your email address</p>
                    </div>
                    <div style="padding: 1rem; background: #f8f9fa; border-radius: var(--radius-md);">
                        <div style="font-size: 2rem; color: var(--success); margin-bottom: 0.5rem;">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem;">Save Your Tickets</h3>
                        <p style="color: var(--gray); font-size: 0.9rem;">Download or save tickets to your phone</p>
                    </div>
                    <div style="padding: 1rem; background: #f8f9fa; border-radius: var(--radius-md);">
                        <div style="font-size: 2rem; color: var(--warning); margin-bottom: 0.5rem;">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem;">Bring QR Code</h3>
                        <p style="color: var(--gray); font-size: 0.9rem;">Show your QR code at the venue entrance</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 2rem;">Complete Payment</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Order Summary -->
            <div class="payment-section">
                <h2><i class="fas fa-shopping-cart"></i> Order Summary</h2>
                <div class="order-summary-box">
                    <h3 style="font-weight: 700; margin-bottom: 1rem;"><?php echo htmlspecialchars($event['title']); ?></h3>
                    <p style="color: var(--gray); margin-bottom: 1rem;">
                        <i class="fas fa-calendar"></i> <?php echo formatDate($event['event_date']); ?> at <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                    </p>
                    
                    <?php foreach ($tickets as $ticket): ?>
                        <div class="summary-row">
                            <span><?php echo htmlspecialchars($ticket['name']); ?> × <?php echo $ticket['quantity']; ?></span>
                            <span><?php echo formatPrice($ticket['subtotal']); ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="summary-row">
                        <span>Booking Fee</span>
                        <span><?php echo formatPrice($booking_fee); ?></span>
                    </div>
                    
                    <div class="summary-total">
                        <span>Total Amount</span>
                        <span><?php echo formatPrice($total_amount); ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="payment-section">
                <h2><i class="fas fa-credit-card"></i> Select Payment Method</h2>
                <form method="POST" action="" id="paymentForm">
                    <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars(getCurrentUser()['name'] ?? ''); ?>">
                    <input type="hidden" name="customer_email" value="<?php echo htmlspecialchars(getCurrentUser()['email'] ?? ''); ?>">
                    <input type="hidden" name="customer_phone" value="<?php echo htmlspecialchars(getCurrentUser()['phone'] ?? ''); ?>">
                    <input type="hidden" name="notes" value="">
                    
                    <div class="payment-methods">
                        <div class="payment-method">
                            <input type="radio" name="payment_method" id="card" value="card" required>
                            <label for="card">
                                <div class="payment-icon"><i class="fas fa-credit-card"></i></div>
                                <div class="payment-name">Credit/Debit Card</div>
                            </label>
                        </div>
                        
                        <div class="payment-method">
                            <input type="radio" name="payment_method" id="fpx" value="fpx">
                            <label for="fpx">
                                <div class="payment-icon"><i class="fas fa-university"></i></div>
                                <div class="payment-name">FPX Online Banking</div>
                            </label>
                        </div>
                        
                        <div class="payment-method">
                            <input type="radio" name="payment_method" id="ewallet" value="ewallet">
                            <label for="ewallet">
                                <div class="payment-icon"><i class="fas fa-mobile-alt"></i></div>
                                <div class="payment-name">E-Wallet</div>
                            </label>
                        </div>
                    </div>

                    <div style="background: #f8f9fa; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                        <p style="font-size: 0.875rem; color: var(--gray); margin: 0;">
                            <i class="fas fa-lock" style="color: var(--success);"></i>
                            <strong>Secure Payment:</strong> Your payment information is encrypted and processed securely. 
                            We never store your card details.
                        </p>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.125rem; padding: 1rem;">
                        <i class="fas fa-lock"></i>
                        Pay <?php echo formatPrice($total_amount); ?>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php 
// Clear booking success data after display
if (isset($_SESSION['booking_success'])) {
    unset($_SESSION['booking_success']);
}
require_once __DIR__ . '/includes/footer.php'; 
?>
