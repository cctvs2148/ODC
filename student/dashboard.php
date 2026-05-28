<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('student');
$studentId = $_SESSION['user_id'];
$pending = $pdo->prepare('SELECT COUNT(*) FROM applications WHERE student_id = ? AND manager_status = "pending"');
$pending->execute([$studentId]);
$pendingCount = $pending->fetchColumn();
$approved = $pdo->prepare('SELECT COUNT(*) FROM applications WHERE student_id = ? AND manager_status = "approved" AND hotel_status = "pending"');
$approved->execute([$studentId]);
$approvedCount = $approved->fetchColumn();
$confirmed = $pdo->prepare('SELECT COUNT(*) FROM applications WHERE student_id = ? AND final_status = "confirmed"');
$confirmed->execute([$studentId]);
$confirmedCount = $confirmed->fetchColumn();
$vacancies = $pdo->query('SELECT COUNT(*) FROM vacancies WHERE available_vacancies > 0')->fetchColumn();
$notifications = $pdo->prepare('SELECT a.*, v.duty_date, v.shift_type, h.name AS hotel_name FROM applications a JOIN vacancies v ON a.vacancy_id = v.id JOIN hotels h ON v.hotel_id = h.id WHERE a.student_id = ? ORDER BY a.apply_date DESC LIMIT 5');
$notifications->execute([$studentId]);
$notificationsData = $notifications->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/student/dashboard.php'); ?>
<?php render_topbar('Student Dashboard'); ?>
<div class="row g-4">
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Open Vacancies</h6><h2 class="text-danger"><?= $vacancies ?></h2></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Pending</h6><h2 class="text-danger"><?= $pendingCount ?></h2></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Manager Approved</h6><h2 class="text-danger"><?= $approvedCount ?></h2></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Confirmed</h6><h2 class="text-danger"><?= $confirmedCount ?></h2></div></div></div>
</div>
<div class="card shadow-sm mt-4">
    <div class="card-header bg-danger text-white">Application Status</div>
    <div class="card-body">
        <?php if (count($notificationsData) === 0): ?>
            <div class="text-muted">No applications yet. Visit vacancies to apply.</div>
        <?php else: ?>
            <ul class="list-group">
            <?php foreach ($notificationsData as $app): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= htmlspecialchars($app['hotel_name']) ?></strong> - <?= htmlspecialchars($app['duty_date']) ?> (<?= htmlspecialchars($app['shift_type']) ?>)
                        <div class="small text-muted">Manager: <?= htmlspecialchars($app['manager_status']) ?> | Hotel: <?= htmlspecialchars($app['hotel_status']) ?> | Final: <?= htmlspecialchars($app['final_status']) ?></div>
                    </div>
                    <span class="badge bg-danger"><?= htmlspecialchars($app['manager_status']) ?></span>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
