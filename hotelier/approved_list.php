<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('hotelier');
$applications = $pdo->query('SELECT a.*, s.name AS student_name, h.name AS hotel_name, v.duty_date, v.shift_type FROM applications a JOIN students s ON a.student_id = s.id JOIN vacancies v ON a.vacancy_id = v.id JOIN hotels h ON v.hotel_id = h.id WHERE a.manager_status = "approved" ORDER BY a.apply_date DESC')->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/hotelier/approved_list.php'); ?>
<?php render_topbar('Approved Student List'); ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-danger text-white">Finalize Admissions</div>
    <div class="card-body table-responsive">
        <table class="table table-hover" id="hotelierAppsTable">
            <thead><tr><th>#</th><th>Student</th><th>Hotel</th><th>Date</th><th>Shift</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?= $app['id'] ?></td>
                    <td><?= htmlspecialchars($app['student_name']) ?></td>
                    <td><?= htmlspecialchars($app['hotel_name']) ?></td>
                    <td><?= htmlspecialchars($app['duty_date']) ?></td>
                    <td><?= htmlspecialchars($app['shift_type']) ?></td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($app['hotel_status']) ?></span></td>
                    <td>
                        <?php if ($app['final_status'] === 'pending'): ?>
                            <form class="ajax-submit d-inline" action="/ODC/ajax/hotelier_action.php" method="post">
                                <input type="hidden" name="application_id" value="<?= $app['id'] ?>" />
                                <input type="hidden" name="final_status" value="confirmed" />
                                <button class="btn btn-sm btn-success">Confirm</button>
                            </form>
                            <form class="ajax-submit d-inline" action="/ODC/ajax/hotelier_action.php" method="post">
                                <input type="hidden" name="application_id" value="<?= $app['id'] ?>" />
                                <input type="hidden" name="final_status" value="rejected" />
                                <button class="btn btn-sm btn-danger">Reject</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted"><?= htmlspecialchars($app['final_status']) ?></span>
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
