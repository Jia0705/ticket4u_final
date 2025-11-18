<?php
$page_title = 'Terms & Conditions';
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
            <h1><i class="fas fa-file-contract"></i> Terms & Conditions</h1>
            <p>Last Updated: November 18, 2025</p>
        </div>

        <div class="policy-content">
            <h2>1. Agreement to Terms</h2>
            <p>
                By accessing and using Ticket4U, you accept and agree to be bound by the terms and provision of 
                this agreement. If you do not agree to these terms, please do not use our service.
            </p>

            <h2>2. Use of Service</h2>
            <p>Our ticketing service allows you to:</p>
            <ul>
                <li>Browse and search for events</li>
                <li>Purchase tickets for available events</li>
                <li>Manage your bookings and profile</li>
                <li>Receive event updates and notifications</li>
            </ul>

            <h2>3. User Account</h2>
            <p>To use certain features of our service, you must:</p>
            <ul>
                <li>Create an account with accurate and complete information</li>
                <li>Maintain the security of your password</li>
                <li>Be at least 18 years old or have parental consent</li>
                <li>Accept responsibility for all activities under your account</li>
            </ul>

            <h2>4. Ticket Purchase and Payment</h2>
            <p>When purchasing tickets through Ticket4U:</p>
            <ul>
                <li>All ticket sales are final unless specified otherwise</li>
                <li>Prices are displayed in Malaysian Ringgit (MYR)</li>
                <li>Booking fees and service charges may apply</li>
                <li>Payment must be completed to confirm your booking</li>
                <li>You will receive a confirmation email with your tickets</li>
            </ul>

            <h2>5. Ticket Transfer and Resale</h2>
            <p>
                Tickets purchased through Ticket4U are for personal use only. Unauthorized resale or transfer of 
                tickets is strictly prohibited and may result in ticket cancellation without refund.
            </p>

            <h2>6. Event Cancellation or Changes</h2>
            <p>
                If an event is cancelled, postponed, or significantly changed, we will notify you via email. 
                Refund policies will be communicated at that time and may vary depending on the event organizer's policies.
            </p>

            <h2>7. Prohibited Activities</h2>
            <p>You agree not to:</p>
            <ul>
                <li>Use the service for any illegal purpose</li>
                <li>Attempt to gain unauthorized access to our systems</li>
                <li>Use automated systems to purchase tickets (bots)</li>
                <li>Harass, abuse, or harm other users</li>
                <li>Provide false or misleading information</li>
                <li>Violate any applicable laws or regulations</li>
            </ul>

            <h2>8. Intellectual Property</h2>
            <p>
                All content on Ticket4U, including text, graphics, logos, and software, is the property of 
                Ticket4U or its content suppliers and is protected by copyright and intellectual property laws.
            </p>

            <h2>9. Limitation of Liability</h2>
            <p>
                Ticket4U acts as an intermediary between event organizers and ticket purchasers. We are not 
                responsible for the quality, safety, or legality of events listed on our platform. Our liability 
                is limited to the refund of ticket prices in accordance with our refund policy.
            </p>

            <h2>10. Privacy</h2>
            <p>
                Your use of Ticket4U is also governed by our Privacy Policy. Please review our Privacy Policy 
                to understand our practices regarding your personal data.
            </p>

            <h2>11. Modifications to Terms</h2>
            <p>
                We reserve the right to modify these terms at any time. We will notify users of any material 
                changes by posting the updated terms on our website. Your continued use of the service after 
                such changes constitutes acceptance of the new terms.
            </p>

            <h2>12. Governing Law</h2>
            <p>
                These terms shall be governed by and construed in accordance with the laws of Malaysia, without 
                regard to its conflict of law provisions.
            </p>

            <h2>13. Contact Information</h2>
            <p>
                If you have any questions about these Terms & Conditions, please contact us at:
            </p>
            <ul>
                <li><strong>Email:</strong> support@ticket4u.com</li>
                <li><strong>Phone:</strong> +60 3-1234 5678</li>
                <li><strong>Address:</strong> 123 Event Street, Kuala Lumpur, Malaysia</li>
            </ul>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
