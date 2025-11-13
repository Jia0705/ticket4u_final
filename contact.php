<?php
$page_title = 'Contact Us';
$page_description = 'Get in touch with Ticket4U - We are here to help you';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $subject = clean($_POST['subject'] ?? '');
    $message = clean($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // In a real application, you would send an email here
        // For now, we'll just show a success message
        $success = 'Thank you for contacting us! We will get back to you within 24 hours.';
        
        // Clear form fields
        $name = $email = $subject = $message = '';
    }
}
?>

<style>
.contact-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 5rem 0 3rem;
    text-align: center;
}

.contact-hero h1 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
}

.contact-hero p {
    font-size: 1.25rem;
    max-width: 700px;
    margin: 0 auto;
    opacity: 0.95;
}

.contact-section {
    padding: 4rem 0;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-top: 2rem;
}

.contact-info {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
}

.contact-info h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 2rem;
    color: var(--dark);
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.contact-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.contact-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    flex-shrink: 0;
}

.contact-details h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--dark);
}

.contact-details p {
    color: var(--gray);
    line-height: 1.6;
}

.contact-details a {
    color: var(--primary-color);
    text-decoration: none;
}

.contact-details a:hover {
    text-decoration: underline;
}

.contact-form {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
}

.contact-form h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 2rem;
    color: var(--dark);
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-hero h1 {
        font-size: 2rem;
    }
}
</style>

<main class="main-content">
    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container">
            <h1>Contact Us</h1>
            <p>Have a question or need assistance? We're here to help! Get in touch with our team.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Information -->
                <div class="contact-info">
                    <h3>Get In Touch</h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Our Office</h4>
                            <p>123 Jalan Ampang,<br>50450 Kuala Lumpur,<br>Malaysia</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Phone</h4>
                            <p><a href="tel:+60123456789">+60 12-345 6789</a><br>
                            <small style="color: var(--gray);">Mon - Fri: 9:00 AM - 6:00 PM</small></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Email</h4>
                            <p>
                                General: <a href="mailto:info@ticket4u.com">info@ticket4u.com</a><br>
                                Support: <a href="mailto:support@ticket4u.com">support@ticket4u.com</a>
                            </p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Business Hours</h4>
                            <p>
                                Monday - Friday: 9:00 AM - 6:00 PM<br>
                                Saturday: 10:00 AM - 4:00 PM<br>
                                Sunday: Closed
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form">
                    <h3>Send Us a Message</h3>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" required 
                                   value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                                   placeholder="Enter your full name">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" required 
                                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                                   placeholder="your@email.com">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Subject *</label>
                            <select name="subject" class="form-control" required>
                                <option value="">Select a subject</option>
                                <option value="General Inquiry">General Inquiry</option>
                                <option value="Booking Issue">Booking Issue</option>
                                <option value="Payment Problem">Payment Problem</option>
                                <option value="Refund Request">Refund Request</option>
                                <option value="Event Organizer">Event Organizer Inquiry</option>
                                <option value="Partnership">Partnership Opportunity</option>
                                <option value="Feedback">Feedback</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Message *</label>
                            <textarea name="message" class="form-control" rows="6" required 
                                      placeholder="Tell us how we can help you..."><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- FAQ Link -->
            <div style="text-align: center; margin-top: 3rem; padding: 2rem; background: #f8f9fa; border-radius: var(--radius-xl);">
                <h3 style="margin-bottom: 1rem;">Looking for quick answers?</h3>
                <p style="color: var(--gray); margin-bottom: 1.5rem;">Check out our frequently asked questions</p>
                <a href="<?php echo SITE_URL; ?>/faq.php" class="btn btn-outline">
                    <i class="fas fa-question-circle"></i> Visit FAQ
                </a>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
