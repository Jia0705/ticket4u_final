<?php
$page_title = 'Login';
require_once __DIR__ . '/../config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $query = "SELECT * FROM users WHERE email = ? AND status = 'active'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                
                setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');
                
                // Redirect to intended page or dashboard
                $redirect_to = $_SESSION['redirect_after_login'] ?? SITE_URL . '/index.php';
                unset($_SESSION['redirect_after_login']);
                redirect($redirect_to);
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
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
    max-width: 450px;
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

.divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 1.5rem 0;
    color: var(--gray);
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border-color);
}

.social-login {
    display: flex;
    gap: 1rem;
}

.social-btn {
    flex: 1;
    padding: 0.75rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-weight: 600;
    transition: var(--transition-base);
}

.social-btn:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

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
                <i class="fas fa-user-lock"></i>
            </div>
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Login to your Ticket4U account</p>
        </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required 
                           placeholder="your@email.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required 
                           placeholder="Enter your password">
                </div>

                <div class="form-check" style="margin-bottom: 1.5rem;">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i>
                    Login to Account
                </button>
            </form>

            <div class="divider">OR</div>

            <div class="social-login">
                <button class="social-btn" onclick="alert('Coming soon!')">
                    <i class="fab fa-google"></i>
                    Google
                </button>
                <button class="social-btn" onclick="alert('Coming soon!')">
                    <i class="fab fa-facebook"></i>
                    Facebook
                </button>
            </div>

            <div class="form-footer">
                <p>Don't have an account? <a href="<?php echo SITE_URL; ?>/auth/register.php" style="color: var(--primary-color); font-weight: 600;">Sign up here</a></p>
                <p><a href="#" style="color: var(--gray);">Forgot your password?</a></p>
            </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
