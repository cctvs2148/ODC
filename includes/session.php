<?php
session_start();
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
function require_login() {
    if (!is_logged_in()) {
        header('Location: /ODC/index.php');
        exit;
    }
}
function require_role($role) {
    require_login();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header('Location: /ODC/index.php');
        exit;
    }
}
function user_role_redirect() {
    if (!is_logged_in()) {
        header('Location: /ODC/index.php');
        exit;
    }
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: /ODC/admin/dashboard.php');
            break;
        case 'placement':
            header('Location: /ODC/placement_head/dashboard.php');
            break;
        case 'student':
            header('Location: /ODC/student/dashboard.php');
            break;
        case 'manager':
            header('Location: /ODC/manager/dashboard.php');
            break;
        case 'hotelier':
            header('Location: /ODC/hotelier/dashboard.php');
            break;
        default:
            header('Location: /ODC/index.php');
    }
    exit;
}
