<?php
define('APP_INIT', true);
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';
$message = null;
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = trim($_POST['role'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');
    if (!$role || !$email || !$password || !$confirm) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $allowedRoles = ['student', 'manager'];
        if (!in_array($role, $allowedRoles, true)) {
            $error = 'Invalid user role selected.';
        } else {
            $user = fetch_user_by_email_and_role($email, $role);
            if (!$user) {
                $error = 'No account found for that email and role.';
            } else {
                if (update_user_password($role, $email, $password)) {
                    $message = 'Password reset successfully. You can now login.';
                } else {
                    $error = 'Unable to reset password. Please try again later.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password - ODC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/ODC/assets/css/style.css" rel="stylesheet" />
</head>
<body class="login-page d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="card shadow-sm rounded-4 login-card">
        <div class="card-body p-4 text-center">
            <h1 class="h3 mb-3 text-danger">Forgot Password</h1>
            <p class="text-muted">Reset your password for student or manager accounts.</p>
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3 text-start">
                    <label class="form-label">Select Role</label>
                    <select name="role" class="form-select" required>
                        <option value="">Choose Role</option>
                        <option value="student">Student</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required />
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required />
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required />
                </div>
                <button type="submit" class="btn btn-danger w-100">Reset Password</button>
            </form>
            <div class="mt-3 text-muted small">
                <a href="/ODC/index.php">Back to login</a>
            </div>
        </div>
    </div>
</body>
</html>
