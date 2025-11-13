<?php
$page_title = 'Booking Details';
require_once __DIR__ . '/../config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('error', 'Please login to view booking details');
    redirect(SITE_URL . '/auth/login.php');
}

$user = getCurrentUser();
$ref = $_GET['ref'] ?? '';

if (empty($ref)) {
    setFlash('error', 'Invalid booking reference');
    redirect(SITE_URL . '/profile/bookings.php');
}

// Get booking details
$booking_query = "SELECT b.*, e.title as event_title, e.slug as event_slug, e.featured_image,
                  e.description, e.event_date, e.event_time, e.venue_name, e.venue_address,
                  e.venue_city, e.venue_state, e.venue_country
                  FROM bookings b
                  JOIN events e ON b.event_id = e.id
                  WHERE b.booking_reference = ? AND b.user_id = ?";
$stmt = $conn->prepare($booking_query);
$stmt->bind_param('si', $ref, $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setFlash('error', 'Booking not found');
    redirect(SITE_URL . '/profile/bookings.php');
}

$booking = $result->fetch_assoc();

// Get booking items
$items_query = "SELECT bi.*, tt.name as ticket_name, tt.price
                FROM booking_items bi
                JOIN ticket_types tt ON bi.ticket_type_id = tt.id
                WHERE bi.booking_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param('i', $booking['id']);
$stmt->execute();
$items = $stmt->get_result();

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.details-container {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-color);
    text-decoration: none;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.back-link:hover {
    color: var(--primary-dark);
}

.booking-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--radius-xl);
    margin-bottom: 2rem;
}

.booking-ref {
    font-size: 1.75rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.booking-status-badge {
    display: inline-block;
    padding: 0.5rem 1.25rem;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: 0.875rem;
    background: rgba(255, 255, 255, 0.2);
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
    margin-bottom: 2rem;
}

.section-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    padding: 2rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--dark);
}

.section-title i {
    color: var(--primary-color);
}

.event-info {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.event-image {
    width: 120px;
    height: 120px;
    border-radius: var(--radius-md);
    object-fit: cover;
}

.event-text h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--dark);
}

.info-row {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    color: var(--gray);
}

.info-row i {
    width: 20px;
    color: var(--primary-color);
    margin-top: 0.25rem;
}

.ticket-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--light);
    border-radius: var(--radius-md);
    margin-bottom: 0.75rem;
}

.ticket-name {
    font-weight: 600;
    color: var(--dark);
}

.ticket-quantity {
    color: var(--gray);
    font-size: 0.9rem;
}

.ticket-price {
    font-weight: 700;
    color: var(--primary-color);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--light);
}

.summary-row:last-child {
    border-bottom: none;
    padding-top: 1rem;
    margin-top: 0.5rem;
    border-top: 2px solid var(--primary-color);
}

.summary-label {
    color: var(--gray);
}

.summary-value {
    font-weight: 600;
    color: var(--dark);
}

.summary-row:last-child .summary-value {
    font-size: 1.5rem;
    color: var(--primary-color);
}

.qr-code {
    text-align: center;
    padding: 2rem;
    background: var(--light);
    border-radius: var(--radius-md);
    margin-top: 1.5rem;
}

.qr-placeholder {
    width: 200px;
    height: 200px;
    margin: 0 auto 1rem;
    background: white;
    border: 2px dashed var(--gray);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: var(--gray);
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

@media (max-width: 968px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .event-info {
        flex-direction: column;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<main class="main-content">
    <div class="details-container">
        <a href="<?php echo SITE_URL; ?>/profile/bookings.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to My Bookings
        </a>

        <div class="booking-header">
            <div class="booking-ref">
                <i class="fas fa-ticket-alt"></i> <?php echo htmlspecialchars($booking['booking_reference']); ?>
            </div>
            <div class="booking-status-badge">
                <?php echo ucfirst($booking['booking_status']); ?>
            </div>
            <p style="margin-top: 1rem; opacity: 0.9;">
                Booked on <?php echo formatDate($booking['created_at']); ?>
            </p>
        </div>

        <div class="details-grid">
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i> Event Details
                </h2>

                <div class="event-info">
                    <img src="<?php echo SITE_URL . '/uploads/events/' . htmlspecialchars($booking['featured_image']); ?>" 
                         alt="<?php echo htmlspecialchars($booking['event_title']); ?>"
                         class="event-image"
                         onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder-event.jpg'">
                    <div class="event-text">
                        <h3><?php echo htmlspecialchars($booking['event_title']); ?></h3>
                        <a href="<?php echo SITE_URL; ?>/event-details.php?slug=<?php echo $booking['event_slug']; ?>" 
                           class="btn btn-outline btn-sm">
                            <i class="fas fa-info-circle"></i> View Event Page
                        </a>
                    </div>
                </div>

                <div class="info-row">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <strong>Date & Time</strong><br>
                        <?php echo formatDate($booking['event_date']); ?> at <?php echo date('g:i A', strtotime($booking['event_time'])); ?>
                    </div>
                </div>

                <div class="info-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Venue</strong><br>
                        <?php echo htmlspecialchars($booking['venue_name']); ?><br>
                        <?php echo htmlspecialchars($booking['venue_address']); ?><br>
                        <?php echo htmlspecialchars($booking['venue_city']); ?>, <?php echo htmlspecialchars($booking['venue_state'] ?? 'Malaysia'); ?>
                    </div>
                </div>

                <h2 class="section-title" style="margin-top: 2rem;">
                    <i class="fas fa-user"></i> Customer Details
                </h2>

                <div class="info-row">
                    <i class="fas fa-user-circle"></i>
                    <div><?php echo htmlspecialchars($booking['customer_name']); ?></div>
                </div>

                <div class="info-row">
                    <i class="fas fa-envelope"></i>
                    <div><?php echo htmlspecialchars($booking['customer_email']); ?></div>
                </div>

                <div class="info-row">
                    <i class="fas fa-phone"></i>
                    <div><?php echo htmlspecialchars($booking['customer_phone']); ?></div>
                </div>
            </div>

            <div>
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-receipt"></i> Order Summary
                    </h2>

                    <?php while ($item = $items->fetch_assoc()): ?>
                        <div class="ticket-item">
                            <div>
                                <div class="ticket-name"><?php echo htmlspecialchars($item['ticket_name']); ?></div>
                                <div class="ticket-quantity"><?php echo $item['quantity']; ?> × <?php echo formatPrice($item['price']); ?></div>
                            </div>
                            <div class="ticket-price">
                                <?php echo formatPrice($item['subtotal']); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>

                    <div style="margin-top: 1.5rem;">
                        <div class="summary-row">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value"><?php echo formatPrice($booking['subtotal']); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Booking Fee</span>
                            <span class="summary-value"><?php echo formatPrice($booking['booking_fee'] ?? 0); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label"><strong>Total Paid</strong></span>
                            <span class="summary-value"><?php echo formatPrice($booking['total_amount']); ?></span>
                        </div>
                    </div>

                    <div class="qr-code">
                        <div class="qr-placeholder">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 1rem;">
                            Show this QR code at the venue entrance
                        </p>
                        <button onclick="window.print()" class="btn btn-outline btn-sm">
                            <i class="fas fa-download"></i> Download Ticket
                        </button>
                    </div>
                </div>

                <div class="action-buttons">
                    <button onclick="downloadTicket()" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-download"></i> Download as PDF
                    </button>
                    <a href="mailto:<?php echo htmlspecialchars($booking['customer_email']); ?>?subject=Booking%20<?php echo $booking['booking_reference']; ?>" 
                       class="btn btn-outline" style="flex: 1;">
                        <i class="fas fa-envelope"></i> Email Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Download ticket as PDF using browser's print-to-PDF
function downloadTicket() {
    // Set document title for the PDF filename
    const originalTitle = document.title;
    document.title = 'Ticket_<?php echo $booking['booking_reference']; ?>';
    
    // Trigger print dialog (user can save as PDF)
    window.print();
    
    // Restore original title
    setTimeout(() => {
        document.title = originalTitle;
    }, 100);
}

// Print styles
if (window.matchMedia) {
    const mediaQueryList = window.matchMedia('print');
    mediaQueryList.addListener(function(mql) {
        if (mql.matches) {
            document.querySelector('.back-link').style.display = 'none';
            document.querySelector('.action-buttons').style.display = 'none';
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
