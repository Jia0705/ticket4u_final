<?php
$page_title = 'My Profile';
require_once __DIR__ . '/../config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('error', 'Please login to view your profile');
    redirect(SITE_URL . '/auth/login.php');
}

$user = getCurrentUser();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = clean($_POST['name']);
    $email = clean($_POST['email']);
    $phone = clean($_POST['phone']);
    
    // Validate
    if (empty($name) || empty($email)) {
        setFlash('error', 'Name and email are required');
    } else {
        // Check if email exists for other users
        $check_query = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param('si', $email, $user['id']);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            setFlash('error', 'Email already in use by another account');
        } else {
            $update_query = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('sssi', $name, $email, $phone, $user['id']);
            
            if ($stmt->execute()) {
                setFlash('success', 'Profile updated successfully');
                redirect(SITE_URL . '/profile/profile.php');
            } else {
                setFlash('error', 'Failed to update profile');
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        setFlash('error', 'All password fields are required');
    } elseif ($new_password !== $confirm_password) {
        setFlash('error', 'New passwords do not match');
    } elseif (strlen($new_password) < 6) {
        setFlash('error', 'Password must be at least 6 characters');
    } else {
        // Verify current password
        if (password_verify($current_password, $user['password'])) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('si', $hashed, $user['id']);
            
            if ($stmt->execute()) {
                setFlash('success', 'Password changed successfully');
                redirect(SITE_URL . '/profile/profile.php');
            } else {
                setFlash('error', 'Failed to change password');
            }
        } else {
            setFlash('error', 'Current password is incorrect');
        }
    }
}

// Refresh user data
$user = getCurrentUser();

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.profile-container {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.profile-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 2rem;
    border-radius: var(--radius-xl);
    margin-bottom: 2rem;
    text-align: center;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 auto 1rem;
    border: 4px solid rgba(255, 255, 255, 0.3);
}

.profile-banner h1 {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.profile-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 2px solid var(--light);
}

.tab-btn {
    padding: 1rem 1.5rem;
    background: none;
    border: none;
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray);
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: var(--transition-base);
}

.tab-btn.active {
    color: var(--primary-color);
    border-bottom-color: var(--primary-color);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.section-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    padding: 2rem;
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--dark);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--dark);
}

.form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--light);
    border-radius: var(--radius-md);
    font-size: 1rem;
    transition: var(--transition-base);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    text-align: center;
}

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.stat-label {
    opacity: 0.9;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .profile-tabs {
        overflow-x: auto;
    }
    
    .tab-btn {
        white-space: nowrap;
    }
}
</style>

<main class="main-content">
    <div class="profile-container">
        <div class="profile-banner">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
            </div>
            <h1><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></h1>
            <p><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
            <p style="margin-top: 1rem; opacity: 0.9; font-size: 0.9rem;">
                Member since <?php echo formatDate($user['created_at'] ?? date('Y-m-d')); ?>
            </p>
        </div>

        <?php
        // Get user stats
        $stats_query = "SELECT 
                        COUNT(*) as total_bookings,
                        COALESCE(SUM(total_amount), 0) as total_spent,
                        COALESCE(SUM(total_tickets), 0) as total_tickets
                        FROM bookings WHERE user_id = ?";
        $stmt = $conn->prepare($stats_query);
        $stmt->bind_param('i', $user['id']);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        
        $wishlist_query = "SELECT COUNT(*) as wishlist_count FROM wishlists WHERE user_id = ?";
        $stmt = $conn->prepare($wishlist_query);
        $stmt->bind_param('i', $user['id']);
        $stmt->execute();
        $wishlist_stats = $stmt->get_result()->fetch_assoc();
        ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['total_bookings']; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['total_tickets']; ?></div>
                <div class="stat-label">Tickets Purchased</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo formatPrice($stats['total_spent']); ?></div>
                <div class="stat-label">Total Spent</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $wishlist_stats['wishlist_count']; ?></div>
                <div class="stat-label">Wishlist Items</div>
            </div>
        </div>

        <div class="profile-tabs">
            <button class="tab-btn active" onclick="switchTab('personal')">
                <i class="fas fa-user"></i> Personal Info
            </button>
            <button class="tab-btn" onclick="switchTab('security')">
                <i class="fas fa-lock"></i> Security
            </button>
        </div>

        <div id="personal" class="tab-content active">
            <div class="section-card">
                <h2 class="section-title">Personal Information</h2>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-input" 
                               value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" 
                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                               placeholder="+60123456789">
                    </div>

                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <div id="security" class="tab-content">
            <div class="section-card">
                <h2 class="section-title">Change Password</h2>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-input" 
                               minlength="6" required>
                        <small style="color: var(--gray); font-size: 0.875rem;">
                            Minimum 6 characters
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-input" 
                               minlength="6" required>
                    </div>

                    <button type="submit" name="change_password" class="btn btn-primary">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
