    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="container">
                <div class="footer-grid">
                    <!-- About Section -->
                    <div class="footer-column">
                        <h3 class="footer-title">
                            <i class="fas fa-ticket"></i>
                            <?php echo SITE_NAME; ?>
                        </h3>
                        <p class="footer-text">
                            Your premier event ticketing platform in Malaysia. Discover and book tickets for concerts, sports, theatre, festivals, and more!
                        </p>
                        <div class="social-links">
                            <a href="javascript:void(0)" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                            <a href="javascript:void(0)" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="javascript:void(0)" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="javascript:void(0)" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="footer-column">
                        <h4 class="footer-heading">Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="<?php echo SITE_URL; ?>/index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/events.php"><i class="fas fa-chevron-right"></i> Browse Events</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/about.php"><i class="fas fa-chevron-right"></i> About Us</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/contact.php"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/faq.php"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                        </ul>
                    </div>

                    <!-- Categories -->
                    <div class="footer-column">
                        <h4 class="footer-heading">Event Categories</h4>
                        <ul class="footer-links">
                            <?php
                            $footer_categories = $conn->query("SELECT name, slug FROM categories WHERE status = 'active' ORDER BY display_order ASC LIMIT 6");
                            while ($cat = $footer_categories->fetch_assoc()):
                            ?>
                                <li>
                                    <a href="<?php echo SITE_URL; ?>/events.php?category=<?php echo $cat['slug']; ?>">
                                        <i class="fas fa-chevron-right"></i>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div class="footer-column">
                        <h4 class="footer-heading">Contact Us</h4>
                        <ul class="footer-contact">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Kuala Lumpur, Malaysia</span>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <a href="tel:+60123456789">+60 12-345 6789</a>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                    <div class="footer-bottom-links">
                        <a href="<?php echo SITE_URL; ?>/privacy-policy.php">Privacy Policy</a>
                        <span>|</span>
                        <a href="<?php echo SITE_URL; ?>/terms.php">Terms & Conditions</a>
                        <span>|</span>
                        <a href="<?php echo SITE_URL; ?>/refund-policy.php">Refund Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Main JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?php echo SITE_URL . $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
