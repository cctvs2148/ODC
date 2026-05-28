<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
if ($_SESSION['role'] !== 'student') {
    echo json_encode(['message' => 'Unauthorized access.']);
    exit;
}
$studentId = $_SESSION['user_id'];
$vacancyId = intval($_POST['vacancy_id'] ?? 0);
if (!$vacancyId) {
    echo json_encode(['message' => 'Invalid vacancy selected.']);
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM vacancies WHERE id = ?');
$stmt->execute([$vacancyId]);
$vacancy = $stmt->fetch();
if (!$vacancy) {
    echo json_encode(['message' => 'Vacancy not found.']);
    exit;
}
if ($vacancy['available_vacancies'] <= 0) {
    echo json_encode(['message' => 'This vacancy is no longer available.']);
    exit;
}
$existing = $pdo->prepare('SELECT a.*, v.duty_date, v.shift_type FROM applications a JOIN vacancies v ON a.vacancy_id = v.id WHERE a.student_id = ? AND v.duty_date = ?');
$existing->execute([$studentId, $vacancy['duty_date']]);
$app = $existing->fetch();
if ($app) {
    if ($app['shift_type'] === $vacancy['shift_type']) {
        echo json_encode(['message' => 'You have already applied for this shift on the same date.']);
    } else {
        echo json_encode(['message' => 'You cannot apply for both FN and AN on the same date.']);
    }
    exit;
}
$pdo->beginTransaction();
try {
    $insert = $pdo->prepare('INSERT INTO applications (student_id, vacancy_id, apply_date, shift_type, manager_status, hotel_status, final_status) VALUES (?, ?, NOW(), ?, "pending", "pending", "pending")');
    $insert->execute([$studentId, $vacancyId, $vacancy['shift_type']]);
    $update = $pdo->prepare('UPDATE vacancies SET available_vacancies = available_vacancies - 1 WHERE id = ? AND available_vacancies > 0');
    $update->execute([$vacancyId]);
    if ($update->rowCount() === 0) {
        $pdo->rollBack();
        echo json_encode(['message' => 'Vacancy just became unavailable.']);
        exit;
    }
    $pdo->commit();
    echo json_encode(['message' => 'Application submitted successfully.', 'reload' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['message' => 'Unable to submit application.']);
}
