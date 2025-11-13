<?php
$page_title = 'About Us';
$page_description = 'Learn more about Ticket4U - Your premier event ticketing platform in Malaysia';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.about-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 5rem 0 3rem;
    text-align: center;
}

.about-hero h1 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
}

.about-hero p {
    font-size: 1.25rem;
    max-width: 700px;
    margin: 0 auto;
    opacity: 0.95;
}

.about-section {
    padding: 4rem 0;
}

.about-section h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 2rem;
    color: var(--dark);
}

.about-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.about-card {
    background: white;
    padding: 2rem;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    text-align: center;
}

.about-card-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: white;
}

.about-card h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--dark);
}

.about-card p {
    color: var(--gray);
    line-height: 1.6;
}

.stats-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.stat-box {
    padding: 2rem;
}

.stat-number {
    font-size: 3rem;
    font-weight: 800;
    display: block;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 1.1rem;
    opacity: 0.95;
}
</style>

<main class="main-content">
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <h1>About Ticket4U</h1>
            <p>Your trusted partner for discovering and booking the best events across Malaysia. We connect event-goers with unforgettable experiences.</p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="about-section">
        <div class="container">
            <h2 style="text-align: center;">Our Mission</h2>
            <p style="text-align: center; max-width: 800px; margin: 0 auto; font-size: 1.1rem; color: var(--gray); line-height: 1.8;">
                At Ticket4U, we believe that live events have the power to inspire, connect, and create lasting memories. 
                Our mission is to make event discovery and ticket booking simple, secure, and accessible for everyone. 
                Whether it's a concert, sports event, theatre show, or festival, we're here to help you experience the best moments life has to offer.
            </p>

            <!-- Value Props -->
            <div class="about-grid">
                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure & Trusted</h3>
                    <p>100% secure payment processing and verified events. Your safety is our priority.</p>
                </div>

                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Instant Confirmation</h3>
                    <p>Get your e-tickets instantly via email. No waiting, no hassle.</p>
                </div>

                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>24/7 Support</h3>
                    <p>Our dedicated support team is always here to help you.</p>
                </div>

                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Best Selection</h3>
                    <p>Curated events from top organizers and venues across Malaysia.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <h2 style="margin-bottom: 1rem;">Ticket4U by the Numbers</h2>
            <p style="font-size: 1.1rem; opacity: 0.95; margin-bottom: 2rem;">Join thousands of happy event-goers</p>
            
            <div class="stats-grid">
                <div class="stat-box">
                    <span class="stat-number">10,000+</span>
                    <span class="stat-label">Events Hosted</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number">50,000+</span>
                    <span class="stat-label">Tickets Sold</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number">25,000+</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number">100+</span>
                    <span class="stat-label">Event Partners</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="about-section">
        <div class="container">
            <h2 style="text-align: center;">Why Choose Ticket4U?</h2>
            <div class="about-grid">
                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile-First</h3>
                    <p>Access your tickets anytime, anywhere with our mobile-friendly platform.</p>
                </div>

                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3>Best Prices</h3>
                    <p>Competitive pricing with no hidden fees. What you see is what you pay.</p>
                </div>

                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3>Easy Entry</h3>
                    <p>QR code tickets for quick and contactless event entry.</p>
                </div>

                <div class="about-card">
                    <div class="about-card-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h3>Flexible Refunds</h3>
                    <p>Easy refund process for cancelled or rescheduled events.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="about-section" style="background: #f8f9fa;">
        <div class="container" style="text-align: center;">
            <h2>Ready to Experience Amazing Events?</h2>
            <p style="font-size: 1.1rem; color: var(--gray); margin-bottom: 2rem;">
                Browse our extensive collection of events and book your tickets today!
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo SITE_URL; ?>/events.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-calendar"></i> Browse Events
                </a>
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-outline btn-lg">
                    <i class="fas fa-envelope"></i> Contact Us
                </a>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
