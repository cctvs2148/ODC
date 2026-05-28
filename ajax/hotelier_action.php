<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
if ($_SESSION['role'] !== 'hotelier') {
    echo json_encode(['message' => 'Unauthorized access.']);
    exit;
}
$applicationId = intval($_POST['application_id'] ?? 0);
$finalStatus = $_POST['final_status'] ?? '';
if (!$applicationId || !in_array($finalStatus, ['confirmed', 'rejected'])) {
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
if ($app['manager_status'] !== 'approved') {
    echo json_encode(['message' => 'Only manager-approved applications can be finalized.']);
    exit;
}
$update = $pdo->prepare('UPDATE applications SET hotel_status = ?, final_status = ? WHERE id = ?');
$update->execute([$finalStatus === 'confirmed' ? 'approved' : 'rejected', $finalStatus, $applicationId]);
echo json_encode(['message' => 'Student final status updated.', 'reload' => true]);
