<?php
$page_title = 'Edit User - Admin';
require_once __DIR__ . '/../config/config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/index.php');
}

$error = '';

// Get user ID
$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$user_id) {
    setFlash('error', 'Invalid user ID.');
    redirect(SITE_URL . '/admin/users.php');
}

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    setFlash('error', 'User not found.');
    redirect(SITE_URL . '/admin/users.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $status = $_POST['status'] ?? 'active';
    $password = $_POST['password'] ?? '';

    // Validate required fields
    if (empty($name) || empty($email)) {
        $error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($user_id == $_SESSION['user_id'] && $status === 'inactive') {
        $error = 'You cannot set your own account to inactive.';
    } elseif ($user_id == $_SESSION['user_id'] && $role !== 'admin') {
        $error = 'You cannot remove your own admin privileges.';
    } else {
        // Check if email is unique (except for this user)
        $email_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $email_check->bind_param("si", $email, $user_id);
        $email_check->execute();
        $email_exists = $email_check->get_result();

        if ($email_exists->num_rows > 0) {
            $error = 'This email is already in use by another user.';
        } else {
            // Update user
            if (!empty($password)) {
                // Update with new password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ?, status = ?, password = ? WHERE id = ?");
                $update_stmt->bind_param("ssssssi", $name, $email, $phone, $role, $status, $hashed_password, $user_id);
            } else {
                // Update without changing password
                $update_stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ?, status = ? WHERE id = ?");
                $update_stmt->bind_param("sssssi", $name, $email, $phone, $role, $status, $user_id);
            }
            
            if ($update_stmt->execute()) {
                setFlash('success', 'User updated successfully!');
                redirect(SITE_URL . '/admin/users.php');
            } else {
                $error = 'Failed to update user: ' . $conn->error;
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.admin-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 0.5rem;
}

.breadcrumb {
    display: flex;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--gray);
}

.breadcrumb a {
    color: var(--primary-color);
    text-decoration: none;
}

.form-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 2rem;
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

.form-label.required::after {
    content: ' *';
    color: #f5576c;
}

.form-input,
.form-select {
    width: 100%;
    padding: 0.875rem;
    border: 2px solid var(--light);
    border-radius: var(--radius-md);
    font-size: 1rem;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary-color);
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.info-box {
    background: #e7f3ff;
    border-left: 4px solid var(--primary-color);
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: var(--radius-md);
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="main-content">
    <div class="admin-container">
        <div class="page-header">
            <h1><i class="fas fa-user-edit"></i> Edit User</h1>
            <div class="breadcrumb">
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">Dashboard</a>
                <span>/</span>
                <a href="<?php echo SITE_URL; ?>/admin/users.php">Users</a>
                <span>/</span>
                <span>Edit</span>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label required">Full Name</label>
                    <input type="text" name="name" class="form-input" required 
                           placeholder="e.g., John Doe"
                           value="<?php echo htmlspecialchars($user['name']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label required">Email Address</label>
                    <input type="email" name="email" class="form-input" required 
                           placeholder="user@email.com"
                           value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-input" 
                           placeholder="+60 12-345 6789"
                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>

                <div class="info-box">
                    <i class="fas fa-info-circle"></i> Leave password field empty to keep current password
                </div>

                <div class="form-group">
                    <label class="form-label">New Password (Optional)</label>
                    <input type="password" name="password" class="form-input" 
                           placeholder="Enter new password (min 6 characters)">
                    <small style="color: var(--gray);">Only fill this if you want to change the password</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/users.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
