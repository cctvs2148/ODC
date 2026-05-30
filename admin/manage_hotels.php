<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('admin');
$message = null;
$error = null;
$editHotel = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_hotel') {
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        if ($name && $address) {
            $stmt = $pdo->prepare('INSERT INTO hotels (name, address) VALUES (?, ?)');
            $stmt->execute([$name, $address]);
            $message = 'Hotel added successfully.';
        }
    }
    if ($_POST['action'] === 'update_hotel') {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        if ($id && $name && $address) {
            $stmt = $pdo->prepare('UPDATE hotels SET name = ?, address = ? WHERE id = ?');
            if ($stmt->execute([$name, $address, $id])) {
                $message = 'Hotel updated successfully.';
            } else {
                $error = 'Unable to update hotel.';
            }
        }
    }
    if ($_POST['action'] === 'delete_hotel') {
        $id = intval($_POST['id']);
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM hotels WHERE id = ?');
            if ($stmt->execute([$id])) {
                $message = 'Hotel deleted successfully.';
            } else {
                $error = 'Unable to delete hotel.';
            }
        }
    }
}
if (isset($_GET['edit_id'])) {
    $editId = intval($_GET['edit_id']);
    $stmt = $pdo->prepare('SELECT * FROM hotels WHERE id = ?');
    $stmt->execute([$editId]);
    $editHotel = $stmt->fetch();
}
$hotels = $pdo->query('SELECT * FROM hotels ORDER BY id DESC')->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/admin/manage_hotels.php'); ?>
<?php render_topbar('Manage Hotels'); ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white"><?= $editHotel ? 'Edit Hotel' : 'Add Hotel' ?></div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editHotel ? 'update_hotel' : 'add_hotel' ?>" />
                    <?php if ($editHotel): ?>
                        <input type="hidden" name="id" value="<?= $editHotel['id'] ?>" />
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Hotel Name</label>
                        <input class="form-control" name="name" value="<?= htmlspecialchars($editHotel['name'] ?? '') ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="3" required><?= htmlspecialchars($editHotel['address'] ?? '') ?></textarea>
                    </div>
                    <button class="btn btn-danger"><?= $editHotel ? 'Update Hotel' : 'Add Hotel' ?></button>
                    <?php if ($editHotel): ?>
                        <a href="/ODC/admin/manage_hotels.php" class="btn btn-secondary ms-2">Add New</a>
                    <?php endif; ?>
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
                        <tr><th>#</th><th>Name</th><th>Address</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hotels as $hotel): ?>
                        <tr>
                            <td><?= $hotel['id'] ?></td>
                            <td><?= htmlspecialchars($hotel['name']) ?></td>
                            <td><?= htmlspecialchars($hotel['address']) ?></td>
                            <td>
                                <a href="?edit_id=<?= $hotel['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="post" class="d-inline ms-1" onsubmit="return confirm('Delete this hotel?');">
                                    <input type="hidden" name="action" value="delete_hotel" />
                                    <input type="hidden" name="id" value="<?= $hotel['id'] ?>" />
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
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
