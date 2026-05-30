<?php
require_once __DIR__ . '/../database/connection.php';
function fetch_user_by_email($email) {
    global $pdo;
    // Ensure all UNION branches return the same columns: user_id, name, email, password, role
    $sql = "SELECT id AS user_id, name, email, password, 'admin' AS role FROM admin WHERE email = ? ";
    $sql .= "UNION SELECT id AS user_id, name, email, password, 'student' AS role FROM students WHERE email = ? ";
    $sql .= "UNION SELECT id AS user_id, name, email, password, 'manager' AS role FROM managers WHERE email = ? ";
    $sql .= "UNION SELECT id AS user_id, name, email, password, 'placement' AS role FROM placement_heads WHERE email = ? ";
    $sql .= "UNION SELECT id AS user_id, name, email, password, 'hotelier' AS role FROM hoteliers WHERE email = ?";
    $stmt = $pdo->prepare($sql);
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
function ensure_site_tables() {
    global $pdo;
    $pdo->exec('CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    $pdo->exec('CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        image VARCHAR(255) DEFAULT NULL,
        status ENUM("active","inactive") NOT NULL DEFAULT "active",
        start_date DATE DEFAULT NULL,
        end_date DATE DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
}
function get_site_setting($key, $default = null) {
    global $pdo;
    ensure_site_tables();
    $stmt = $pdo->prepare('SELECT setting_value FROM site_settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    $value = $stmt->fetchColumn();
    return $value !== false ? $value : $default;
}
function set_site_setting($key, $value) {
    global $pdo;
    ensure_site_tables();
    $stmt = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    $stmt->execute([$key, $value]);
}
function get_announcements($activeOnly = true) {
    global $pdo;
    ensure_site_tables();
    $sql = 'SELECT * FROM announcements';
    $params = [];
    if ($activeOnly) {
        $sql .= ' WHERE status = "active" AND (start_date IS NULL OR start_date <= CURDATE()) AND (end_date IS NULL OR end_date >= CURDATE())';
    }
    $sql .= ' ORDER BY created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
function get_announcement($id) {
    global $pdo;
    ensure_site_tables();
    $stmt = $pdo->prepare('SELECT * FROM announcements WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}
function fetch_user_by_email_and_role($email, $role) {
    global $pdo;
    switch ($role) {
        case 'student':
            $stmt = $pdo->prepare('SELECT * FROM students WHERE email = ?');
            break;
        case 'manager':
            $stmt = $pdo->prepare('SELECT * FROM managers WHERE email = ?');
            break;
        case 'placement':
            $stmt = $pdo->prepare('SELECT * FROM placement_heads WHERE email = ?');
            break;
        case 'hotelier':
            $stmt = $pdo->prepare('SELECT * FROM hoteliers WHERE email = ?');
            break;
        case 'admin':
            $stmt = $pdo->prepare('SELECT * FROM admin WHERE email = ?');
            break;
        default:
            return false;
    }
    $stmt->execute([$email]);
    return $stmt->fetch();
}
function update_user_password($role, $email, $password) {
    global $pdo;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    switch ($role) {
        case 'student':
            $stmt = $pdo->prepare('UPDATE students SET password = ? WHERE email = ?');
            break;
        case 'manager':
            $stmt = $pdo->prepare('UPDATE managers SET password = ? WHERE email = ?');
            break;
        case 'placement':
            $stmt = $pdo->prepare('UPDATE placement_heads SET password = ? WHERE email = ?');
            break;
        case 'hotelier':
            $stmt = $pdo->prepare('UPDATE hoteliers SET password = ? WHERE email = ?');
            break;
        case 'admin':
            $stmt = $pdo->prepare('UPDATE admin SET password = ? WHERE email = ?');
            break;
        default:
            return false;
    }
    return $stmt->execute([$hash, $email]);
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
