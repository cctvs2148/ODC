<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('placement');
$vacancies = $pdo->query('SELECT v.*, h.name AS hotel_name FROM vacancies v JOIN hotels h ON v.hotel_id = h.id ORDER BY v.duty_date DESC')->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/placement_head/vacancies.php'); ?>
<?php render_topbar('All Vacancies'); ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
        <span>Vacancy List</span>
        <button class="btn btn-secondary btn-sm" onclick="tableToExcel('vacanciesTable', 'vacancies.xlsx')">Export Excel</button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover" id="vacanciesTable">
            <thead><tr><th>#</th><th>Hotel</th><th>Date</th><th>Shift</th><th>Available</th><th>Reporting</th></tr></thead>
            <tbody>
                <?php foreach ($vacancies as $vac): ?>
                <tr>
                    <td><?= $vac['id'] ?></td>
                    <td><?= htmlspecialchars($vac['hotel_name']) ?></td>
                    <td><?= htmlspecialchars($vac['duty_date']) ?></td>
                    <td><?= htmlspecialchars($vac['shift_type']) ?></td>
                    <td><?= $vac['available_vacancies'] ?> / <?= $vac['total_vacancies'] ?></td>
                    <td><?= htmlspecialchars($vac['reporting_time']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
