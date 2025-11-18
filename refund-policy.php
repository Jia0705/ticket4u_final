<?php
$page_title = 'Refund Policy';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.policy-container {
    max-width: 900px;
    margin: 3rem auto;
    padding: 0 1.5rem;
}

.policy-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 2rem;
    border-radius: var(--radius-xl);
    text-align: center;
    margin-bottom: 3rem;
}

.policy-header h1 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.policy-header p {
    opacity: 0.95;
    font-size: 1.1rem;
}

.policy-content {
    background: white;
    padding: 3rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
}

.policy-content h2 {
    color: var(--primary-color);
    font-size: 1.75rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.policy-content h2:first-child {
    margin-top: 0;
}

.policy-content p {
    color: var(--gray);
    line-height: 1.8;
    margin-bottom: 1.5rem;
}

.policy-content ul {
    color: var(--gray);
    line-height: 1.8;
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.policy-content li {
    margin-bottom: 0.5rem;
}

.highlight-box {
    background: #f8f9fa;
    border-left: 4px solid var(--primary-color);
    padding: 1.5rem;
    margin: 1.5rem 0;
    border-radius: var(--radius-md);
}

.highlight-box strong {
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .policy-header {
        padding: 2rem 1.5rem;
    }
    
    .policy-header h1 {
        font-size: 1.75rem;
    }
    
    .policy-content {
        padding: 2rem 1.5rem;
    }
    
    .policy-content h2 {
        font-size: 1.5rem;
    }
}
</style>

<main class="main-content">
    <div class="policy-container">
        <div class="policy-header">
            <h1><i class="fas fa-undo-alt"></i> Refund Policy</h1>
            <p>Last Updated: November 18, 2025</p>
        </div>

        <div class="policy-content">
            <h2>1. General Refund Policy</h2>
            <p>
                At Ticket4U, we understand that plans can change. This refund policy outlines the circumstances 
                under which refunds may be issued for ticket purchases made through our platform.
            </p>
            <div class="highlight-box">
                <strong>Important:</strong> All ticket sales are generally final. Refunds are only available 
                under specific circumstances as outlined in this policy.
            </div>

            <h2>2. Refund Eligibility</h2>
            <p>You may be eligible for a refund in the following situations:</p>
            <ul>
                <li><strong>Event Cancellation:</strong> Full refund if the event is cancelled by the organizer</li>
                <li><strong>Event Postponement:</strong> Refund or ticket transfer option if the new date doesn't work for you</li>
                <li><strong>Technical Errors:</strong> Refund for duplicate charges or technical issues during purchase</li>
                <li><strong>Venue Change:</strong> Refund if there's a significant change to the event venue</li>
            </ul>

            <h2>3. Refund Request Process</h2>
            <p>To request a refund, please follow these steps:</p>
            <ul>
                <li>Log in to your Ticket4U account</li>
                <li>Navigate to "My Bookings"</li>
                <li>Select the booking you wish to cancel</li>
                <li>Click on "Request Refund" and provide the reason</li>
                <li>Submit your request and await confirmation</li>
            </ul>
            <p>
                Alternatively, you can contact our support team at <strong>support@ticket4u.com</strong> with 
                your booking reference number and reason for the refund request.
            </p>

            <h2>4. Refund Processing Time</h2>
            <p>Once your refund request is approved:</p>
            <ul>
                <li>Refunds will be processed within <strong>7-10 business days</strong></li>
                <li>The refund will be credited to the original payment method</li>
                <li>You will receive a confirmation email once the refund is processed</li>
                <li>Bank processing times may vary (typically 3-5 business days)</li>
            </ul>

            <h2>5. Non-Refundable Situations</h2>
            <p>Refunds will <strong>NOT</strong> be issued in the following cases:</p>
            <ul>
                <li>Change of mind after purchase</li>
                <li>Inability to attend the event for personal reasons</li>
                <li>Failure to bring required identification or tickets to the event</li>
                <li>Late arrival or missing the event</li>
                <li>Weather conditions (unless the event is officially cancelled)</li>
                <li>Dissatisfaction with the event experience</li>
            </ul>

            <h2>6. Booking Fees and Service Charges</h2>
            <div class="highlight-box">
                <strong>Note:</strong> Booking fees and service charges are non-refundable unless the event 
                is cancelled by the organizer.
            </div>

            <h2>7. Event Organizer Cancellation</h2>
            <p>If an event is cancelled by the organizer:</p>
            <ul>
                <li>You will receive a <strong>full refund</strong> including all fees</li>
                <li>Refunds will be automatically processed to your original payment method</li>
                <li>You will receive an email notification within 48 hours of cancellation</li>
                <li>Processing time: 7-10 business days</li>
            </ul>

            <h2>8. Partial Refunds</h2>
            <p>
                Partial refunds may be issued in cases where there are significant changes to the event that 
                do not result in full cancellation. This is evaluated on a case-by-case basis in consultation 
                with the event organizer.
            </p>

            <h2>9. Refund for Technical Issues</h2>
            <p>If you experience any of the following technical issues, you are eligible for a full refund:</p>
            <ul>
                <li>Duplicate charges on your account</li>
                <li>Payment processed but no booking confirmation received</li>
                <li>System errors during the checkout process</li>
                <li>Incorrect ticket types or quantities purchased due to system malfunction</li>
            </ul>
            <p>
                Please contact us immediately at <strong>support@ticket4u.com</strong> if you experience any 
                technical issues during purchase.
            </p>

            <h2>10. Special Circumstances</h2>
            <p>
                We understand that extraordinary circumstances may arise. If you have a special situation not 
                covered by this policy, please contact our support team. We will review your case individually 
                and provide a fair resolution.
            </p>

            <h2>11. Contact Us</h2>
            <p>
                If you have any questions about our refund policy or need assistance with a refund request, 
                please contact us:
            </p>
            <ul>
                <li><strong>Email:</strong> support@ticket4u.com</li>
                <li><strong>Phone:</strong> +60 3-1234 5678</li>
                <li><strong>Business Hours:</strong> Monday - Friday, 9:00 AM - 6:00 PM (GMT+8)</li>
            </ul>
            <p>
                We are committed to providing excellent customer service and will do our best to resolve any 
                issues promptly and fairly.
            </p>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
