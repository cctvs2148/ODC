<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');
require_once __DIR__ . '/../includes/layout.php';
$students = count_table('students');
$managers = count_table('managers');
$placements = count_table('placement_heads');
$hotels = count_table('hotels');
$vacancies = count_table('vacancies');
$applications = count_table('applications');
$pending = $pdo->query('SELECT COUNT(*) FROM applications WHERE manager_status = "pending"')->fetchColumn();
$approved = $pdo->query('SELECT COUNT(*) FROM applications WHERE manager_status = "approved" AND hotel_status = "pending"')->fetchColumn();
$confirmed = $pdo->query('SELECT COUNT(*) FROM applications WHERE final_status = "confirmed"')->fetchColumn();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/admin/dashboard.php'); ?>
<?php render_topbar('Admin Dashboard'); ?>
<div class="row g-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Students</h6>
                <h2 class="text-danger"><?= $students ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Managers</h6>
                <h2 class="text-danger"><?= $managers ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Placement Heads</h6>
                <h2 class="text-danger"><?= $placements ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Hotels</h6>
                <h2 class="text-danger"><?= $hotels ?></h2>
            </div>
        </div>
    </div>
</div>
<div class="row g-4 mt-3">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Vacancies</h6>
                <h2 class="text-danger"><?= $vacancies ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Total Applications</h6>
                <h2 class="text-danger"><?= $applications ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Pending Review</h6>
                <h2 class="text-danger"><?= $pending ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted">Confirmed</h6>
                <h2 class="text-danger"><?= $confirmed ?></h2>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm mt-4">
    <div class="card-header bg-danger text-white">Analytics</div>
    <div class="card-body">
        <canvas id="adminChart" height="120"></canvas>
    </div>
</div>
<script>
const ctx = document.getElementById('adminChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Students', 'Managers', 'Placement Heads', 'Hotels', 'Vacancies', 'Applications', 'Confirmed'],
        datasets: [{
            label: 'Counts',
            backgroundColor: '#c71f1f',
            borderColor: '#c71f1f',
            data: [<?= $students ?>, <?= $managers ?>, <?= $placements ?>, <?= $hotels ?>, <?= $vacancies ?>, <?= $applications ?>, <?= $confirmed ?>],
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {beginAtZero: true}
        }
    }
});
</script>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
