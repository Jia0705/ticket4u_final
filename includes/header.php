<?php
if (!isset($conn)) {
    require_once __DIR__ . '/../config/config.php';
}

$current_user = getCurrentUser();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : 'Discover and book tickets for the best events in Malaysia - Concerts, Sports, Theatre, Festivals and more!'; ?>">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?><?php echo SITE_NAME; ?> - Your Premier Event Ticketing Platform</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/favicon.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?php echo SITE_URL . $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Flash Messages -->
    <?php if ($flash): ?>
        <div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMessage">
            <div class="container">
                <div class="flash-content">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
                    <span><?php echo htmlspecialchars($flash['message']); ?></span>
                    <button onclick="this.parentElement.parentElement.remove()" class="flash-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <header class="main-header">
        <div class="top-bar">
            <div class="container">
                <div class="top-bar-content">
                    <div class="top-bar-left">
                        <a href="mailto:<?php echo ADMIN_EMAIL; ?>">
                            <i class="fas fa-envelope"></i>
                            <?php echo ADMIN_EMAIL; ?>
                        </a>
                        <a href="tel:+60123456789">
                            <i class="fas fa-phone"></i>
                            +60 12-345 6789
                        </a>
                    </div>
                    <div class="top-bar-right">
                        <span><i class="fas fa-map-marker-alt"></i> Malaysia</span>
                    </div>
                </div>
            </div>
        </div>

        <nav class="navbar">
            <div class="container">
                <div class="navbar-content">
                    <!-- Logo -->
                    <a href="<?php echo SITE_URL; ?>/index.php" class="logo">
                        <i class="fas fa-ticket"></i>
                        <span><?php echo SITE_NAME; ?></span>
                    </a>

                    <!-- Search Bar -->
                    <form class="search-form" action="<?php echo SITE_URL; ?>/events.php" method="GET">
                        <div class="search-input-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" placeholder="Search events, artists, venues..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit" class="search-btn">Search</button>
                        </div>
                    </form>

                    <!-- Navigation Links -->
                    <div class="nav-links">
                        <a href="<?php echo SITE_URL; ?>/index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                            Home
                        </a>
                        <a href="<?php echo SITE_URL; ?>/events.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : ''; ?>">
                            Events
                        </a>
                        <div class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle">
                                Categories <i class="fas fa-chevron-down"></i>
                            </a>
                            <div class="dropdown-menu">
                                <?php
                                $categories_query = "SELECT * FROM categories WHERE status = 'active' ORDER BY display_order ASC";
                                $categories_result = $conn->query($categories_query);
                                while ($cat = $categories_result->fetch_assoc()):
                                ?>
                                    <a href="<?php echo SITE_URL; ?>/events.php?category=<?php echo $cat['slug']; ?>" class="dropdown-item">
                                        <i class="fas <?php echo $cat['icon']; ?>"></i>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </a>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="user-menu">
                        <?php if ($current_user): ?>
                            <div class="dropdown">
                                <button class="user-btn dropdown-toggle">
                                    <?php if (!empty($current_user['profile_image'])): ?>
                                        <img src="<?php echo SITE_URL; ?>/uploads/profiles/<?php echo htmlspecialchars($current_user['profile_image']); ?>" alt="Profile" class="user-avatar">
                                    <?php else: ?>
                                        <div class="user-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                            <?php echo strtoupper(substr($current_user['name'] ?? 'U', 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($current_user['name'] ?? 'User'); ?></span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-header">
                                        <strong><?php echo htmlspecialchars($current_user['name'] ?? 'User'); ?></strong>
                                        <small><?php echo htmlspecialchars($current_user['email']); ?></small>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <?php if (isAdmin()): ?>
                                        <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="dropdown-item">
                                            <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    <?php endif; ?>
                                    <a href="<?php echo SITE_URL; ?>/profile/profile.php" class="dropdown-item">
                                        <i class="fas fa-user"></i> My Profile
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/profile/bookings.php" class="dropdown-item">
                                        <i class="fas fa-ticket-alt"></i> My Bookings
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/profile/wishlist.php" class="dropdown-item">
                                        <i class="fas fa-heart"></i> My Wishlist
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-outline">Login</a>
                            <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-primary">Sign Up</a>
                        <?php endif; ?>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu-header">
                <span class="logo-text"><?php echo SITE_NAME; ?></span>
                <button class="mobile-menu-close" id="mobileMenuClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mobile-menu-content">
                <?php if ($current_user): ?>
                    <div class="mobile-user-info">
                        <img src="<?php echo SITE_URL; ?>/uploads/profiles/<?php echo htmlspecialchars($current_user['profile_image']); ?>" alt="Profile" class="user-avatar">
                        <div>
                            <strong><?php echo htmlspecialchars($current_user['name']); ?></strong>
                            <small><?php echo htmlspecialchars($current_user['email']); ?></small>
                        </div>
                    </div>
                <?php endif; ?>
                
                <nav class="mobile-nav">
                    <a href="<?php echo SITE_URL; ?>/index.php"><i class="fas fa-home"></i> Home</a>
                    <a href="<?php echo SITE_URL; ?>/events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                    
                    <div class="mobile-submenu">
                        <button class="mobile-submenu-toggle">
                            <i class="fas fa-th"></i> Categories
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="mobile-submenu-content">
                            <?php
                            $categories_result->data_seek(0);
                            while ($cat = $categories_result->fetch_assoc()):
                            ?>
                                <a href="<?php echo SITE_URL; ?>/events.php?category=<?php echo $cat['slug']; ?>">
                                    <i class="fas <?php echo $cat['icon']; ?>"></i>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    
                    <?php if ($current_user): ?>
                        <?php if (isAdmin()): ?>
                            <a href="<?php echo SITE_URL; ?>/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
                        <?php endif; ?>
                        <a href="<?php echo SITE_URL; ?>/profile/bookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
                        <a href="<?php echo SITE_URL; ?>/profile/profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="<?php echo SITE_URL; ?>/profile/wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
                        <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="<?php echo SITE_URL; ?>/auth/register.php"><i class="fas fa-user-plus"></i> Sign Up</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
