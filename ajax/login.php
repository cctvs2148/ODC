<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /ODC/index.php');
    exit;
}
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
if (!$email || !$password) {
    flash_message('login_error', 'Email and password are required.');
    header('Location: /ODC/index.php');
    exit;
}
$user = fetch_user_by_email($email);
if (!$user || !password_verify($password, $user['password'])) {
    flash_message('login_error', 'Invalid email or password.');
    header('Location: /ODC/index.php');
    exit;
}
$_SESSION['user_id'] = $user['user_id'] ?? $user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['user_name'] = $user['name'] ?? $user['email'];
user_role_redirect();
