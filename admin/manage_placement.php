<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('admin');
$message = null;
$error = null;
$editPlacement = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_placement') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        if ($name && $email && $password) {
            $stmt = $pdo->prepare('INSERT INTO placement_heads (name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $message = 'Placement head added successfully.';
        }
    }
    if ($_POST['action'] === 'update_placement') {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        if ($id && $name && $email) {
            $sql = 'UPDATE placement_heads SET name = ?, email = ?';
            $params = [$name, $email];
            if ($password) {
                $sql .= ', password = ?';
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }
            $sql .= ' WHERE id = ?';
            $params[] = $id;
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                $message = 'Placement head updated successfully.';
            } else {
                $error = 'Unable to update placement head.';
            }
        }
    }
    if ($_POST['action'] === 'delete_placement') {
        $id = intval($_POST['id']);
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM placement_heads WHERE id = ?');
            if ($stmt->execute([$id])) {
                $message = 'Placement head deleted successfully.';
            } else {
                $error = 'Unable to delete placement head.';
            }
        }
    }
}
if (isset($_GET['edit_id'])) {
    $editId = intval($_GET['edit_id']);
    $stmt = $pdo->prepare('SELECT * FROM placement_heads WHERE id = ?');
    $stmt->execute([$editId]);
    $editPlacement = $stmt->fetch();
}
$placements = $pdo->query('SELECT * FROM placement_heads ORDER BY id DESC')->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/admin/manage_placement.php'); ?>
<?php render_topbar('Manage Placement Heads'); ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white"><?= $editPlacement ? 'Edit Placement Head' : 'Add Placement Head' ?></div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editPlacement ? 'update_placement' : 'add_placement' ?>" />
                    <?php if ($editPlacement): ?>
                        <input type="hidden" name="id" value="<?= $editPlacement['id'] ?>" />
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" value="<?= htmlspecialchars($editPlacement['name'] ?? '') ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($editPlacement['email'] ?? '') ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <?= $editPlacement ? '(leave blank to keep current)' : '' ?></label>
                        <input type="password" class="form-control" name="password" <?= $editPlacement ? '' : 'required' ?> />
                    </div>
                    <button class="btn btn-danger"><?= $editPlacement ? 'Update Placement Head' : 'Add Placement Head' ?></button>
                    <?php if ($editPlacement): ?>
                        <a href="/ODC/admin/manage_placement.php" class="btn btn-secondary ms-2">Add New</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white">Placement Head List</div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Email</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($placements as $placement): ?>
                        <tr>
                            <td><?= $placement['id'] ?></td>
                            <td><?= htmlspecialchars($placement['name']) ?></td>
                            <td><?= htmlspecialchars($placement['email']) ?></td>
                            <td>
                                <a href="?edit_id=<?= $placement['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="post" class="d-inline ms-1" onsubmit="return confirm('Delete this placement head?');">
                                    <input type="hidden" name="action" value="delete_placement" />
                                    <input type="hidden" name="id" value="<?= $placement['id'] ?>" />
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
