<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('student');
$studentId = $_SESSION['user_id'];
$vacancies = $pdo->query('SELECT v.*, h.name AS hotel_name FROM vacancies v JOIN hotels h ON v.hotel_id = h.id WHERE v.available_vacancies > 0 ORDER BY v.duty_date, v.shift_type')->fetchAll();
$existing = $pdo->prepare('SELECT v.duty_date, v.shift_type FROM applications a JOIN vacancies v ON a.vacancy_id = v.id WHERE a.student_id = ?');
$existing->execute([$studentId]);
$applied = $existing->fetchAll();
$appliedMap = [];
foreach ($applied as $row) {
    $appliedMap[$row['duty_date']][$row['shift_type']] = true;
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/student/vacancies.php'); ?>
<?php render_topbar('Available Vacancies'); ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
        <span>Open Vacancy List</span>
        <button class="btn btn-secondary btn-sm" onclick="tableToExcel('vacanciesStudentTable', 'vacancies.xlsx')">Export Excel</button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover" id="vacanciesStudentTable">
            <thead><tr><th>#</th><th>Hotel</th><th>Date</th><th>Shift</th><th>Available</th><th>Reporting</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($vacancies as $vac): ?>
                <?php $appliedCurrentDate = isset($appliedMap[$vac['duty_date']]); ?>
                <tr>
                    <td><?= $vac['id'] ?></td>
                    <td><?= htmlspecialchars($vac['hotel_name']) ?></td>
                    <td><?= htmlspecialchars($vac['duty_date']) ?></td>
                    <td><?= htmlspecialchars($vac['shift_type']) ?></td>
                    <td><?= $vac['available_vacancies'] ?></td>
                    <td><?= htmlspecialchars($vac['reporting_time']) ?></td>
                    <td>
                        <?php if ($appliedMap[$vac['duty_date']][$vac['shift_type']] ?? false): ?>
                            <span class="badge bg-secondary">Already Applied</span>
                        <?php elseif ($appliedCurrentDate && !($appliedMap[$vac['duty_date']][$vac['shift_type']] ?? false)): ?>
                            <span class="badge bg-warning">Other shift locked</span>
                        <?php else: ?>
                            <form class="ajax-submit" action="/ODC/ajax/student_apply.php" method="post">
                                <input type="hidden" name="vacancy_id" value="<?= $vac['id'] ?>" />
                                <button class="btn btn-danger btn-sm">Apply</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
