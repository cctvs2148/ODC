<?php
require_once __DIR__ . '/../database/connection.php';
function fetch_user_by_email($email) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM admin WHERE email = ? UNION SELECT id as user_id, email, password, "student" as role FROM students WHERE email = ? UNION SELECT id as user_id, email, password, "manager" as role FROM managers WHERE email = ? UNION SELECT id as user_id, email, password, "placement" as role FROM placement_heads WHERE email = ? UNION SELECT id as user_id, email, password, "hotelier" as role FROM hoteliers WHERE email = ?');
    $stmt->execute([$email, $email, $email, $email, $email]);
    return $stmt->fetch();
}
function get_role_label($role) {
    $map = [
        'admin' => 'Admin',
        'placement' => 'Placement Head',
        'student' => 'Student',
        'manager' => 'Manager',
        'hotelier' => 'Hotelier',
    ];
    return $map[$role] ?? ucfirst($role);
}
function count_table($table) {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
    return $stmt->fetchColumn();
}
function flash_message($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}
function get_notifications($userId, $role) {
    global $pdo;
    if ($role === 'student') {
        $stmt = $pdo->prepare('SELECT a.*, v.duty_date, v.shift_type, h.name AS hotel_name FROM applications a JOIN vacancies v ON a.vacancy_id = v.id JOIN hotels h ON v.hotel_id = h.id WHERE a.student_id = ? ORDER BY a.apply_date DESC LIMIT 5');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    if ($role === 'manager') {
        $stmt = $pdo->query('SELECT a.*, s.name AS student_name, v.duty_date, v.shift_type, h.name AS hotel_name FROM applications a JOIN students s ON a.student_id = s.id JOIN vacancies v ON a.vacancy_id = v.id JOIN hotels h ON v.hotel_id = h.id WHERE a.manager_status = "pending" ORDER BY a.apply_date DESC LIMIT 5');
        return $stmt->fetchAll();
    }
    if ($role === 'hotelier') {
        $stmt = $pdo->query('SELECT a.*, s.name AS student_name, v.duty_date, v.shift_type, h.name AS hotel_name FROM applications a JOIN students s ON a.student_id = s.id JOIN vacancies v ON a.vacancy_id = v.id JOIN hotels h ON v.hotel_id = h.id WHERE a.manager_status = "approved" AND a.hotel_status = "pending" ORDER BY a.apply_date DESC LIMIT 5');
        return $stmt->fetchAll();
    }
    return [];
}
