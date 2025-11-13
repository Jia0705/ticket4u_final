<?php
$page_title = 'Frequently Asked Questions';
$page_description = 'Find answers to common questions about booking tickets on Ticket4U';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.faq-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 5rem 0 3rem;
    text-align: center;
}

.faq-hero h1 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
}

.faq-hero p {
    font-size: 1.25rem;
    max-width: 700px;
    margin: 0 auto;
    opacity: 0.95;
}

.faq-section {
    padding: 4rem 0;
}

.faq-categories {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 3rem;
}

.faq-category-btn {
    padding: 1rem;
    background: white;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    font-weight: 600;
    color: var(--dark);
    cursor: pointer;
    transition: var(--transition-base);
}

.faq-category-btn:hover,
.faq-category-btn.active {
    border-color: var(--primary-color);
    color: var(--primary-color);
    background: rgba(102, 126, 234, 0.05);
}

.faq-list {
    max-width: 900px;
    margin: 0 auto;
}

.faq-item {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: 1rem;
    overflow: hidden;
}

.faq-question {
    padding: 1.5rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    color: var(--dark);
    transition: var(--transition-base);
}

.faq-question:hover {
    background: #f8f9fa;
}

.faq-question i {
    color: var(--primary-color);
    transition: var(--transition-base);
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 0 1.5rem;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    color: var(--gray);
    line-height: 1.8;
}

.faq-item.active .faq-answer {
    padding: 0 1.5rem 1.5rem;
    max-height: 500px;
}

.contact-cta {
    text-align: center;
    margin-top: 3rem;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: var(--radius-xl);
}

@media (max-width: 768px) {
    .faq-hero h1 {
        font-size: 2rem;
    }
    
    .faq-categories {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="main-content">
    <!-- Hero Section -->
    <section class="faq-hero">
        <div class="container">
            <h1><i class="fas fa-question-circle"></i> FAQ</h1>
            <p>Find answers to the most commonly asked questions about our ticketing platform</p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <!-- Category Filters -->
            <div class="faq-categories">
                <button class="faq-category-btn active" onclick="filterFAQ('all')">All Questions</button>
                <button class="faq-category-btn" onclick="filterFAQ('booking')">Booking</button>
                <button class="faq-category-btn" onclick="filterFAQ('payment')">Payment</button>
                <button class="faq-category-btn" onclick="filterFAQ('tickets')">Tickets</button>
                <button class="faq-category-btn" onclick="filterFAQ('account')">Account</button>
            </div>

            <!-- FAQ List -->
            <div class="faq-list">
                <!-- Booking Questions -->
                <div class="faq-item" data-category="booking">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>How do I book tickets on Ticket4U?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Booking tickets is easy! Simply browse our events, select the event you want to attend, 
                        choose your preferred tickets and quantity, and proceed to checkout. You'll receive your 
                        e-tickets via email immediately after payment confirmation.
                    </div>
                </div>

                <div class="faq-item" data-category="booking">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>Can I book tickets for multiple people?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Yes! You can purchase multiple tickets in a single transaction. Simply select the quantity 
                        you need when booking. All tickets will be sent to your registered email address.
                    </div>
                </div>

                <div class="faq-item" data-category="booking">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>What if the event I want is sold out?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        If an event is sold out, you can add it to your wishlist and we'll notify you if more 
                        tickets become available. Some events may also have a waiting list option.
                    </div>
                </div>

                <!-- Payment Questions -->
                <div class="faq-item" data-category="payment">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>What payment methods do you accept?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        We accept major credit cards (Visa, Mastercard), FPX online banking, and popular e-wallets 
                        like GrabPay, Touch 'n Go eWallet, and Boost. All payments are processed securely through 
                        our payment gateway partners.
                    </div>
                </div>

                <div class="faq-item" data-category="payment">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>Are there any booking fees?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        A small booking fee may apply to cover processing costs. This fee is clearly displayed 
                        before you complete your purchase, so there are no surprises. The fee varies depending 
                        on the event and ticket price.
                    </div>
                </div>

                <div class="faq-item" data-category="payment">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>Is my payment information secure?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Absolutely! We use industry-standard SSL encryption and work with trusted payment gateways. 
                        Your credit card information is never stored on our servers and is processed securely 
                        through PCI-compliant payment processors.
                    </div>
                </div>

                <!-- Tickets Questions -->
                <div class="faq-item" data-category="tickets">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>How will I receive my tickets?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Your e-tickets will be sent to your registered email address immediately after payment 
                        confirmation. You can also access your tickets anytime from your account dashboard. 
                        Simply show the QR code on your phone at the event entrance.
                    </div>
                </div>

                <div class="faq-item" data-category="tickets">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>Can I transfer my tickets to someone else?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Ticket transfer policies vary by event. Some events allow ticket transfers through your 
                        account dashboard, while others may have restrictions. Check the event details or 
                        contact our support team for specific event policies.
                    </div>
                </div>

                <div class="faq-item" data-category="tickets">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>What if I lose my ticket email?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        No worries! You can always access your tickets by logging into your Ticket4U account 
                        and visiting "My Bookings". You can download or resend your tickets from there at any time.
                    </div>
                </div>

                <!-- Account Questions -->
                <div class="faq-item" data-category="account">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>Do I need an account to book tickets?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Yes, you need to create a free account to book tickets. This allows us to send you your 
                        tickets, manage your bookings, and keep you updated about your events. Registration 
                        takes less than a minute!
                    </div>
                </div>

                <div class="faq-item" data-category="account">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>How do I reset my password?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Click on the "Forgot Password" link on the login page. Enter your registered email address, 
                        and we'll send you a password reset link. Follow the instructions in the email to create 
                        a new password.
                    </div>
                </div>

                <div class="faq-item" data-category="account">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>Can I delete my account?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Yes, you can request account deletion by contacting our support team. Please note that 
                        you won't be able to access your past bookings after deletion. We recommend keeping 
                        your account for record-keeping purposes.
                    </div>
                </div>

                <!-- General Questions -->
                <div class="faq-item" data-category="all">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>What is your refund policy?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Refund policies vary by event and are set by the event organizers. Generally, tickets 
                        are non-refundable unless the event is cancelled or rescheduled. In such cases, you'll 
                        be eligible for a full refund or the option to attend the rescheduled event. Check the 
                        specific event's terms and conditions for details.
                    </div>
                </div>

                <div class="faq-item" data-category="all">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>How do I contact customer support?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        You can reach our customer support team via email at support@ticket4u.com or call us 
                        at +60 12-345 6789 during business hours (Mon-Fri: 9AM-6PM). You can also use our 
                        contact form on the Contact Us page.
                    </div>
                </div>

                <div class="faq-item" data-category="all">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span>Are children required to have tickets?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Age policies vary by event. Some events are free for children under a certain age, 
                        while others require tickets for all attendees regardless of age. Check the specific 
                        event details for age requirements and policies.
                    </div>
                </div>
            </div>

            <!-- Contact CTA -->
            <div class="contact-cta">
                <h3 style="margin-bottom: 1rem;">Still have questions?</h3>
                <p style="color: var(--gray); margin-bottom: 1.5rem;">
                    Our support team is here to help you!
                </p>
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">
                    <i class="fas fa-envelope"></i> Contact Support
                </a>
            </div>
        </div>
    </section>
</main>

<script>
function toggleFAQ(element) {
    const faqItem = element.closest('.faq-item');
    const isActive = faqItem.classList.contains('active');
    
    // Close all FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Open clicked item if it wasn't active
    if (!isActive) {
        faqItem.classList.add('active');
    }
}

function filterFAQ(category) {
    // Update active button
    document.querySelectorAll('.faq-category-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Filter FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
