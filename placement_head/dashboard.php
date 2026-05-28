<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('placement');
$vacancies = count_table('vacancies');
$hotels = count_table('hotels');
$open = $pdo->query('SELECT COUNT(*) FROM vacancies WHERE available_vacancies > 0')->fetchColumn();
$recent = $pdo->query('SELECT v.*, h.name AS hotel_name FROM vacancies v JOIN hotels h ON v.hotel_id = h.id ORDER BY v.duty_date DESC LIMIT 5')->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/placement_head/dashboard.php'); ?>
<?php render_topbar('Placement Head Dashboard'); ?>
<div class="row g-4">
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Total Vacancies</h6><h2 class="text-danger"><?= $vacancies ?></h2></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Hotels</h6><h2 class="text-danger"><?= $hotels ?></h2></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><h6 class="text-muted">Open Slots</h6><h2 class="text-danger"><?= $open ?></h2></div></div></div>
</div>
<div class="card shadow-sm mt-4">
    <div class="card-header bg-danger text-white">Recent Vacancies</div>
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead><tr><th>#</th><th>Hotel</th><th>Date</th><th>Shift</th><th>Available</th></tr></thead>
            <tbody>
                <?php foreach ($recent as $vac): ?>
                    <tr>
                        <td><?= $vac['id'] ?></td>
                        <td><?= htmlspecialchars($vac['hotel_name']) ?></td>
                        <td><?= htmlspecialchars($vac['duty_date']) ?></td>
                        <td><?= htmlspecialchars($vac['shift_type']) ?></td>
                        <td><?= $vac['available_vacancies'] ?> / <?= $vac['total_vacancies'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
