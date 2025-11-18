<?php
$page_title = 'Privacy Policy';
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
            <h1><i class="fas fa-shield-alt"></i> Privacy Policy</h1>
            <p>Last Updated: November 18, 2025</p>
        </div>

        <div class="policy-content">
            <h2>1. Introduction</h2>
            <p>
                Welcome to Ticket4U. We respect your privacy and are committed to protecting your personal data. 
                This privacy policy will inform you about how we look after your personal data when you visit our 
                website and tell you about your privacy rights.
            </p>

            <h2>2. Information We Collect</h2>
            <p>We may collect, use, store and transfer different kinds of personal data about you:</p>
            <ul>
                <li><strong>Identity Data:</strong> Name, username, date of birth</li>
                <li><strong>Contact Data:</strong> Email address, phone number</li>
                <li><strong>Transaction Data:</strong> Details about bookings and payments</li>
                <li><strong>Technical Data:</strong> IP address, browser type, device information</li>
                <li><strong>Usage Data:</strong> Information about how you use our website and services</li>
            </ul>

            <h2>3. How We Use Your Information</h2>
            <p>We will only use your personal data when the law allows us to. We use your information to:</p>
            <ul>
                <li>Process your ticket bookings and manage your account</li>
                <li>Provide customer support and respond to your inquiries</li>
                <li>Send you booking confirmations and event updates</li>
                <li>Improve our website and services</li>
                <li>Comply with legal obligations</li>
            </ul>

            <h2>4. Data Security</h2>
            <p>
                We have put in place appropriate security measures to prevent your personal data from being 
                accidentally lost, used, or accessed in an unauthorized way. All payment transactions are encrypted 
                using SSL technology.
            </p>

            <h2>5. Data Retention</h2>
            <p>
                We will only retain your personal data for as long as necessary to fulfill the purposes we collected 
                it for, including for the purposes of satisfying any legal, accounting, or reporting requirements.
            </p>

            <h2>6. Your Rights</h2>
            <p>Under data protection laws, you have rights including:</p>
            <ul>
                <li>Right to access your personal data</li>
                <li>Right to correct your personal data</li>
                <li>Right to erase your personal data</li>
                <li>Right to object to processing of your personal data</li>
                <li>Right to data portability</li>
            </ul>

            <h2>7. Third-Party Links</h2>
            <p>
                Our website may include links to third-party websites. Clicking on those links may allow third parties 
                to collect or share data about you. We do not control these third-party websites and are not responsible 
                for their privacy statements.
            </p>

            <h2>8. Cookies</h2>
            <p>
                We use cookies to distinguish you from other users of our website. This helps us to provide you with 
                a good experience when you browse our website and also allows us to improve our site.
            </p>

            <h2>9. Changes to This Policy</h2>
            <p>
                We may update this privacy policy from time to time. We will notify you of any changes by posting the 
                new privacy policy on this page and updating the "Last Updated" date.
            </p>

            <h2>10. Contact Us</h2>
            <p>
                If you have any questions about this privacy policy or our privacy practices, please contact us at:
            </p>
            <ul>
                <li><strong>Email:</strong> privacy@ticket4u.com</li>
                <li><strong>Phone:</strong> +60 3-1234 5678</li>
                <li><strong>Address:</strong> 123 Event Street, Kuala Lumpur, Malaysia</li>
            </ul>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
