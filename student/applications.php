<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('student');
$studentId = $_SESSION['user_id'];
$applications = $pdo->prepare('SELECT a.*, v.duty_date, v.shift_type, h.name AS hotel_name FROM applications a JOIN vacancies v ON a.vacancy_id = v.id JOIN hotels h ON v.hotel_id = h.id WHERE a.student_id = ? ORDER BY a.apply_date DESC');
$applications->execute([$studentId]);
$applications = $applications->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/student/applications.php'); ?>
<?php render_topbar('My Applications'); ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-danger text-white">Application History</div>
    <div class="card-body table-responsive">
        <table class="table table-hover" id="studentAppsTable">
            <thead><tr><th>#</th><th>Hotel</th><th>Date</th><th>Shift</th><th>Manager</th><th>Hotel</th><th>Final</th></tr></thead>
            <tbody>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?= $app['id'] ?></td>
                    <td><?= htmlspecialchars($app['hotel_name']) ?></td>
                    <td><?= htmlspecialchars($app['duty_date']) ?></td>
                    <td><?= htmlspecialchars($app['shift_type']) ?></td>
                    <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($app['manager_status']) ?></span></td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($app['hotel_status']) ?></span></td>
                    <td><span class="badge bg-success text-dark"><?= htmlspecialchars($app['final_status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
