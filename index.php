<?php
define('APP_INIT', true);
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) {
    user_role_redirect();
}
$loginError = flash_message('login_error');
$companyLogo = get_site_setting('company_logo');
$companyName = get_site_setting('company_name', 'ODC Student Hotel Duty Management');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ODC Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/ODC/assets/css/style.css" rel="stylesheet" />
</head>
<body class="login-page d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="card shadow-sm rounded-4 login-card">
        <div class="card-body p-4 text-center">
            <?php if ($companyLogo): ?>
                <img src="<?= htmlspecialchars($companyLogo) ?>" alt="Logo" class="mb-3" style="max-width: 140px; max-height: 90px;" />
            <?php endif; ?>
            <h1 class="h3 mb-3 text-danger"><?= htmlspecialchars($companyName) ?></h1>
            <p class="text-muted">Sign in with your email and password</p>
            <?php if ($loginError): ?>
            <div class="alert alert-warning"><?= htmlspecialchars($loginError) ?></div>
            <?php endif; ?>
            <form id="loginForm" action="/ODC/ajax/login.php" method="post">
                <div class="mb-3 text-start">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required />
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required />
                </div>
                <button type="submit" class="btn btn-danger w-100">Login</button>
            </form>
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <a href="/ODC/forgot_password.php" class="small">Forgot Password?</a>
                <span class="text-muted small">Role-based dashboard access for Admin, Placement Head, Student, Manager, Hotelier.</span>
            </div>
        </div>
    </div>
</body>
</html>
