<?php
$page_title = 'Sign Up';
require_once __DIR__ . '/../config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = clean($_POST['full_name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if email exists
        $check_query = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'This email is already registered';
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insert_query = "INSERT INTO users (email, password, full_name, phone, role, status) 
                            VALUES (?, ?, ?, ?, 'user', 'active')";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param('ssss', $email, $hashed_password, $full_name, $phone);
            
            if ($stmt->execute()) {
                // Auto login
                $user_id = $conn->insert_id;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_role'] = 'user';
                
                setFlash('success', 'Welcome to Ticket4U! Your account has been created successfully.');
                redirect(SITE_URL . '/index.php');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.auth-container {
    width: 100%;
    max-width: 500px;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    padding: 2.5rem;
    margin: auto;
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    margin: 0 auto 1rem;
}

.auth-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 0.5rem;
}

.auth-subtitle {
    color: var(--gray);
}

.alert {
    padding: 1rem;
    border-radius: var(--radius-md);
    margin-bottom: 1.5rem;
}

.alert-danger {
    background: #fee;
    color: var(--error);
    border: 1px solid var(--error);
}

.form-footer {
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.password-strength {
    height: 4px;
    background: var(--border-color);
    border-radius: 2px;
    margin-top: 0.5rem;
    overflow: hidden;
}

.password-strength-bar {
    height: 100%;
    width: 0;
    transition: var(--transition-base);
}

.password-strength-bar.weak { width: 33%; background: var(--error); }
.password-strength-bar.medium { width: 66%; background: var(--warning); }
.password-strength-bar.strong { width: 100%; background: var(--success); }

@media (max-width: 576px) {
    .auth-container {
        padding: 1.5rem;
    }
    
    .auth-title {
        font-size: 1.5rem;
    }
}
</style>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <div class="auth-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join Ticket4U and start booking amazing events</p>
        </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" required 
                           placeholder="John Doe"
                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" required 
                           placeholder="your@email.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number (Optional)</label>
                    <input type="tel" name="phone" class="form-control" 
                           placeholder="+60 12-345 6789"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" id="password" class="form-control" required 
                           placeholder="At least 6 characters">
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <small class="text-muted">Use at least 6 characters with a mix of letters and numbers</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" required 
                           placeholder="Re-enter your password">
                </div>

                <div class="form-check" style="margin-bottom: 1.5rem;">
                    <input type="checkbox" name="terms" id="terms" class="form-check-input" required>
                    <label for="terms">I agree to the <a href="#" style="color: var(--primary-color);">Terms & Conditions</a> and <a href="#" style="color: var(--primary-color);">Privacy Policy</a></label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </form>

            <div class="form-footer">
                <p>Already have an account? <a href="<?php echo SITE_URL; ?>/auth/login.php" style="color: var(--primary-color); font-weight: 600;">Login here</a></p>
            </div>
    </div>
</div>

<script>
// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strengthBar');
    
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z\d]/.test(password)) strength++;
    
    strengthBar.className = 'password-strength-bar';
    if (strength <= 2) {
        strengthBar.classList.add('weak');
    } else if (strength <= 4) {
        strengthBar.classList.add('medium');
    } else {
        strengthBar.classList.add('strong');
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
