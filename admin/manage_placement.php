<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_placement') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    if ($name && $email && $password) {
        $stmt = $pdo->prepare('INSERT INTO placement_heads (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    }
}
$placements = $pdo->query('SELECT * FROM placement_heads ORDER BY id DESC')->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/admin/manage_placement.php'); ?>
<?php render_topbar('Manage Placement Heads'); ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white">Add Placement Head</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="action" value="add_placement" />
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required />
                    </div>
                    <button class="btn btn-danger">Add Placement Head</button>
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
                        <tr><th>#</th><th>Name</th><th>Email</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($placements as $placement): ?>
                        <tr>
                            <td><?= $placement['id'] ?></td>
                            <td><?= htmlspecialchars($placement['name']) ?></td>
                            <td><?= htmlspecialchars($placement['email']) ?></td>
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
