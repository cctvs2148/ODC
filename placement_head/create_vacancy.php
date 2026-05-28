<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('placement');
$hotels = $pdo->query('SELECT * FROM hotels ORDER BY name')->fetchAll();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotel_id = $_POST['hotel_id'];
    $duty_date = $_POST['duty_date'];
    $shift = $_POST['shift_type'];
    $vacancies = intval($_POST['total_vacancies']);
    $reporting_time = trim($_POST['reporting_time']);
    if ($hotel_id && $duty_date && $shift && $vacancies > 0 && $reporting_time) {
        $stmt = $pdo->prepare('INSERT INTO vacancies (hotel_id, duty_date, shift_type, total_vacancies, available_vacancies, reporting_time) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$hotel_id, $duty_date, $shift, $vacancies, $vacancies, $reporting_time]);
        $message = 'Vacancy created and notifications sent to students.';
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/placement_head/create_vacancy.php'); ?>
<?php render_topbar('Create Hotel Vacancy'); ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-danger text-white">New Vacancy</div>
    <div class="card-body">
        <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <form method="post">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Hotel</label>
                    <select name="hotel_id" class="form-select" required>
                        <option value="">Select hotel</option>
                        <?php foreach ($hotels as $hotel): ?>
                        <option value="<?= $hotel['id'] ?>"><?= htmlspecialchars($hotel['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Duty Date</label>
                    <input type="date" name="duty_date" class="form-control" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Shift</label>
                    <select name="shift_type" class="form-select" required>
                        <option value="FN">FN</option>
                        <option value="AN">AN</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Vacancies</label>
                    <input type="number" name="total_vacancies" class="form-control" min="1" required />
                </div>
                <div class="col-12">
                    <label class="form-label">Reporting Time</label>
                    <input type="text" name="reporting_time" class="form-control" placeholder="e.g. 8:00 AM" required />
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-danger">Create Vacancy</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
