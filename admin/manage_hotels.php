<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_hotel') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    if ($name && $address) {
        $stmt = $pdo->prepare('INSERT INTO hotels (name, address) VALUES (?, ?)');
        $stmt->execute([$name, $address]);
    }
}
$hotels = $pdo->query('SELECT * FROM hotels ORDER BY id DESC')->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/admin/manage_hotels.php'); ?>
<?php render_topbar('Manage Hotels'); ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white">Add Hotel</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="action" value="add_hotel" />
                    <div class="mb-3">
                        <label class="form-label">Hotel Name</label>
                        <input class="form-control" name="name" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="3" required></textarea>
                    </div>
                    <button class="btn btn-danger">Add Hotel</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white">Hotel List</div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Address</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hotels as $hotel): ?>
                        <tr>
                            <td><?= $hotel['id'] ?></td>
                            <td><?= htmlspecialchars($hotel['name']) ?></td>
                            <td><?= htmlspecialchars($hotel['address']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
