<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('admin');
$students = count_table('students');
$managers = count_table('managers');
$placements = count_table('placement_heads');
$hotels = count_table('hotels');
$vacancies = count_table('vacancies');
$applications = count_table('applications');
$pending = $pdo->query('SELECT COUNT(*) FROM applications WHERE manager_status = "pending"')->fetchColumn();
$approved = $pdo->query('SELECT COUNT(*) FROM applications WHERE manager_status = "approved" AND hotel_status = "pending"')->fetchColumn();
$confirmed = $pdo->query('SELECT COUNT(*) FROM applications WHERE final_status = "confirmed"')->fetchColumn();
$announcements = get_announcements(false);
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/admin/analytics.php'); ?>
<?php render_topbar('Analytics'); ?>
<div class="row g-4">
    <div class="col-md-4"><div class="card shadow-sm border-0"><div class="card-body"><h6 class="text-muted">Students</h6><h2 class="text-danger"><?= $students ?></h2></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm border-0"><div class="card-body"><h6 class="text-muted">Managers</h6><h2 class="text-danger"><?= $managers ?></h2></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm border-0"><div class="card-body"><h6 class="text-muted">Placement Heads</h6><h2 class="text-danger"><?= $placements ?></h2></div></div></div>
</div>
<div class="row g-4 mt-3">
    <div class="col-md-4"><div class="card shadow-sm border-0"><div class="card-body"><h6 class="text-muted">Hotels</h6><h2 class="text-danger"><?= $hotels ?></h2></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm border-0"><div class="card-body"><h6 class="text-muted">Vacancies</h6><h2 class="text-danger"><?= $vacancies ?></h2></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm border-0"><div class="card-body"><h6 class="text-muted">Applications</h6><h2 class="text-danger"><?= $applications ?></h2></div></div></div>
</div>
<div class="row g-4 mt-3">
    <div class="col-md-4"><div class="card shadow-sm border-0"><div class="card-body"><h6 class="text-muted">Pending</h6><h2 class="text-danger"><?= $pending ?></h2></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm border-0"><div class="card-body"><h6 class="text-muted">Approved</h6><h2 class="text-danger"><?= $approved ?></h2></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm border-0"><div class="card-body"><h6 class="text-muted">Confirmed</h6><h2 class="text-danger"><?= $confirmed ?></h2></div></div></div>
</div>
<div class="card shadow-sm mt-4 border-0">
    <div class="card-header bg-danger text-white">Announcements</div>
    <div class="card-body">
        <?php if (count($announcements) === 0): ?>
            <div class="text-muted">No announcements found.</div>
        <?php else: ?>
            <ul class="list-group">
            <?php foreach ($announcements as $announcement): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= htmlspecialchars($announcement['title']) ?></strong>
                        <div class="small text-muted"><?= htmlspecialchars(substr($announcement['message'], 0, 120)) ?><?= strlen($announcement['message']) > 120 ? '...' : '' ?></div>
                    </div>
                    <span class="badge bg-secondary"><?= htmlspecialchars($announcement['status']) ?></span>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<script>
const ctx = document.createElement('canvas');
document.querySelector('.main-content').appendChild(ctx);
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Students','Managers','Placement Heads','Hotels','Vacancies','Applications'],
        datasets: [{
            data: [<?= $students ?>, <?= $managers ?>, <?= $placements ?>, <?= $hotels ?>, <?= $vacancies ?>, <?= $applications ?>],
            backgroundColor: ['#c71f1f','#ff9800','#ffc107','#1976d2','#9c27b0','#4caf50'],
        }]
    },
    options: {responsive: true}
});
</script>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
