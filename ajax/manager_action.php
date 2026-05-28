<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
if ($_SESSION['role'] !== 'manager') {
    echo json_encode(['message' => 'Unauthorized access.']);
    exit;
}
$applicationId = intval($_POST['application_id'] ?? 0);
$status = $_POST['status'] ?? '';
$remarks = trim($_POST['remarks'] ?? '');
if (!$applicationId || !in_array($status, ['approved', 'rejected'])) {
    echo json_encode(['message' => 'Invalid request.']);
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM applications WHERE id = ?');
$stmt->execute([$applicationId]);
$app = $stmt->fetch();
if (!$app) {
    echo json_encode(['message' => 'Application not found.']);
    exit;
}
if ($app['manager_status'] !== 'pending') {
    echo json_encode(['message' => 'Application already processed.']);
    exit;
}
$hotelStatus = 'pending';
$finalStatus = 'pending';
if ($status === 'rejected') {
    $hotelStatus = 'rejected';
    $finalStatus = 'rejected';
}
$update = $pdo->prepare('UPDATE applications SET manager_status = ?, hotel_status = ?, final_status = ?, manager_remarks = ? WHERE id = ?');
$update->execute([$status, $hotelStatus, $finalStatus, $remarks, $applicationId]);
echo json_encode(['message' => 'Application updated successfully.', 'reload' => true]);
