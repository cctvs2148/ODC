<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('admin');
$applications = $pdo->query('SELECT a.*, s.name AS student_name, h.name AS hotel_name, v.duty_date, v.shift_type FROM applications a JOIN students s ON a.student_id = s.id JOIN vacancies v ON a.vacancy_id = v.id JOIN hotels h ON v.hotel_id = h.id ORDER BY a.apply_date DESC')->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/admin/view_applications.php'); ?>
<?php render_topbar('All Applications'); ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
        <span>Application Reports</span>
        <button class="btn btn-secondary btn-sm" onclick="tableToExcel('applicationsTable', 'applications.xlsx')">Export Excel</button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover" id="applicationsTable">
            <thead>
                <tr><th>#</th><th>Student</th><th>Hotel</th><th>Date</th><th>Shift</th><th>Manager</th><th>Hotel</th><th>Final</th></tr>
            </thead>
            <tbody>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?= $app['id'] ?></td>
                    <td><?= htmlspecialchars($app['student_name']) ?></td>
                    <td><?= htmlspecialchars($app['hotel_name']) ?></td>
                    <td><?= htmlspecialchars($app['duty_date']) ?></td>
                    <td><?= htmlspecialchars($app['shift_type']) ?></td>
                    <td><?= htmlspecialchars($app['manager_status']) ?></td>
                    <td><?= htmlspecialchars($app['hotel_status']) ?></td>
                    <td><?= htmlspecialchars($app['final_status']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
