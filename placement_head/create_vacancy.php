<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('placement');
$hotels = $pdo->query('SELECT * FROM hotels ORDER BY name')->fetchAll();
$message = '';
$error = '';
$vacancy = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotel_id = intval($_POST['hotel_id'] ?? 0);
    $duty_date = trim($_POST['duty_date'] ?? '');
    $shift = trim($_POST['shift_type'] ?? '');
    $vacancies = intval($_POST['total_vacancies'] ?? 0);
    $available_vacancies = intval($_POST['available_vacancies'] ?? $vacancies);
    $reporting_time = trim($_POST['reporting_time'] ?? '');
    $action = $_POST['action'] ?? 'create_vacancy';
    if ($hotel_id && $duty_date && $shift && $vacancies > 0 && $reporting_time) {
        if ($action === 'create_vacancy') {
            $stmt = $pdo->prepare('INSERT INTO vacancies (hotel_id, duty_date, shift_type, total_vacancies, available_vacancies, reporting_time) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$hotel_id, $duty_date, $shift, $vacancies, $available_vacancies, $reporting_time]);
            $message = 'Vacancy created successfully.';
        }
        if ($action === 'update_vacancy') {
            $id = intval($_POST['id'] ?? 0);
            if ($id) {
                $stmt = $pdo->prepare('UPDATE vacancies SET hotel_id = ?, duty_date = ?, shift_type = ?, total_vacancies = ?, available_vacancies = ?, reporting_time = ? WHERE id = ?');
                if ($stmt->execute([$hotel_id, $duty_date, $shift, $vacancies, $available_vacancies, $reporting_time, $id])) {
                    $message = 'Vacancy updated successfully.';
                } else {
                    $error = 'Unable to update vacancy.';
                }
            }
        }
    } else {
        $error = 'Please fill in all required fields.';
    }
}
if (isset($_GET['edit_id'])) {
    $vacancyId = intval($_GET['edit_id']);
    $stmt = $pdo->prepare('SELECT * FROM vacancies WHERE id = ?');
    $stmt->execute([$vacancyId]);
    $vacancy = $stmt->fetch();
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/placement_head/create_vacancy.php'); ?>
<?php render_topbar('Create Hotel Vacancy'); ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-danger text-white"><?= $vacancy ? 'Edit Vacancy' : 'New Vacancy' ?></div>
    <div class="card-body">
        <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-warning"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="post">
            <input type="hidden" name="action" value="<?= $vacancy ? 'update_vacancy' : 'create_vacancy' ?>" />
            <?php if ($vacancy): ?><input type="hidden" name="id" value="<?= $vacancy['id'] ?>" /><?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Hotel</label>
                    <select name="hotel_id" class="form-select" required>
                        <option value="">Select hotel</option>
                        <?php foreach ($hotels as $hotel): ?>
                        <option value="<?= $hotel['id'] ?>" <?= $vacancy && $vacancy['hotel_id'] == $hotel['id'] ? 'selected' : '' ?>><?= htmlspecialchars($hotel['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Duty Date</label>
                    <input type="date" name="duty_date" class="form-control" value="<?= htmlspecialchars($vacancy['duty_date'] ?? '') ?>" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Shift</label>
                    <select name="shift_type" class="form-select" required>
                        <option value="FN" <?= $vacancy && $vacancy['shift_type'] === 'FN' ? 'selected' : '' ?>>FN</option>
                        <option value="AN" <?= $vacancy && $vacancy['shift_type'] === 'AN' ? 'selected' : '' ?>>AN</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Total Vacancies</label>
                    <input type="number" name="total_vacancies" class="form-control" min="1" value="<?= htmlspecialchars($vacancy['total_vacancies'] ?? 1) ?>" required />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Available Vacancies</label>
                    <input type="number" name="available_vacancies" class="form-control" min="0" value="<?= htmlspecialchars($vacancy['available_vacancies'] ?? ($vacancy['total_vacancies'] ?? 1)) ?>" required />
                </div>
                <div class="col-12">
                    <label class="form-label">Reporting Time</label>
                    <input type="text" name="reporting_time" class="form-control" placeholder="e.g. 8:00 AM" value="<?= htmlspecialchars($vacancy['reporting_time'] ?? '') ?>" required />
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-danger"><?= $vacancy ? 'Update Vacancy' : 'Create Vacancy' ?></button>
                    <?php if ($vacancy): ?><a href="/ODC/placement_head/create_vacancy.php" class="btn btn-secondary ms-2">Create New</a><?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
