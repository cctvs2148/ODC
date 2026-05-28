<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('hotelier');
$pending = $pdo->query('SELECT COUNT(*) FROM applications WHERE manager_status = "approved" AND hotel_status = "pending"')->fetchColumn();
$confirmed = $pdo->query('SELECT COUNT(*) FROM applications WHERE final_status = "confirmed"')->fetchColumn();
$rejected = $pdo->query('SELECT COUNT(*) FROM applications WHERE final_status = "rejected"')->fetchColumn();
$recent = $pdo->query('SELECT a.*, s.name AS student_name, h.name AS hotel_name, v.duty_date, v.shift_type FROM applications a JOIN students s ON a.student_id = s.id JOIN vacancies v ON a.vacancy_id = v.id JOIN hotels h ON v.hotel_id = h.id WHERE a.manager_status = "approved" AND a.hotel_status = "pending" ORDER BY a.apply_date DESC LIMIT 5')->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/hotelier/dashboard.php'); ?>
<?php render_topbar('Hotelier Dashboard'); ?>
<div class="row g-4">
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Pending Final Review</h6><h2 class="text-danger"><?= $pending ?></h2></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Confirmed</h6><h2 class="text-danger"><?= $confirmed ?></h2></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Rejected</h6><h2 class="text-danger"><?= $rejected ?></h2></div></div></div>
</div>
<div class="card shadow-sm mt-4">
    <div class="card-header bg-danger text-white">Awaiting Final Approval</div>
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead><tr><th>#</th><th>Student</th><th>Hotel</th><th>Date</th><th>Shift</th></tr></thead>
            <tbody>
            <?php foreach ($recent as $app): ?>
            <tr>
                <td><?= $app['id'] ?></td>
                <td><?= htmlspecialchars($app['student_name']) ?></td>
                <td><?= htmlspecialchars($app['hotel_name']) ?></td>
                <td><?= htmlspecialchars($app['duty_date']) ?></td>
                <td><?= htmlspecialchars($app['shift_type']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
