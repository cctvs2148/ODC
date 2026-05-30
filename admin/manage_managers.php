<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('admin');
$message = null;
$error = null;
$editManager = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_manager') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        if ($name && $email && $password) {
            $stmt = $pdo->prepare('INSERT INTO managers (name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $message = 'Manager added successfully.';
        }
    }
    if ($_POST['action'] === 'update_manager') {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        if ($id && $name && $email) {
            $sql = 'UPDATE managers SET name = ?, email = ?';
            $params = [$name, $email];
            if ($password) {
                $sql .= ', password = ?';
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }
            $sql .= ' WHERE id = ?';
            $params[] = $id;
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                $message = 'Manager updated successfully.';
            } else {
                $error = 'Unable to update manager.';
            }
        }
    }
    if ($_POST['action'] === 'delete_manager') {
        $id = intval($_POST['id']);
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM managers WHERE id = ?');
            if ($stmt->execute([$id])) {
                $message = 'Manager deleted successfully.';
            } else {
                $error = 'Unable to delete manager.';
            }
        }
    }
}
if (isset($_GET['edit_id'])) {
    $editId = intval($_GET['edit_id']);
    $stmt = $pdo->prepare('SELECT * FROM managers WHERE id = ?');
    $stmt->execute([$editId]);
    $editManager = $stmt->fetch();
}
$managers = $pdo->query('SELECT * FROM managers ORDER BY id DESC')->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/admin/manage_managers.php'); ?>
<?php render_topbar('Manage Managers'); ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white"><?= $editManager ? 'Edit Manager' : 'Add Manager' ?></div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editManager ? 'update_manager' : 'add_manager' ?>" />
                    <?php if ($editManager): ?>
                        <input type="hidden" name="id" value="<?= $editManager['id'] ?>" />
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" value="<?= htmlspecialchars($editManager['name'] ?? '') ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($editManager['email'] ?? '') ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <?= $editManager ? '(leave blank to keep current)' : '' ?></label>
                        <input type="password" class="form-control" name="password" <?= $editManager ? '' : 'required' ?> />
                    </div>
                    <button class="btn btn-danger"><?= $editManager ? 'Update Manager' : 'Add Manager' ?></button>
                    <?php if ($editManager): ?>
                        <a href="/ODC/admin/manage_managers.php" class="btn btn-secondary ms-2">Add New</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white">Manager List</div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Email</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($managers as $manager): ?>
                        <tr>
                            <td><?= $manager['id'] ?></td>
                            <td><?= htmlspecialchars($manager['name']) ?></td>
                            <td><?= htmlspecialchars($manager['email']) ?></td>
                            <td>
                                <a href="?edit_id=<?= $manager['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="post" class="d-inline ms-1" onsubmit="return confirm('Delete this manager?');">
                                    <input type="hidden" name="action" value="delete_manager" />
                                    <input type="hidden" name="id" value="<?= $manager['id'] ?>" />
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
