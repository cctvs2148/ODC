<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('admin');
$message = null;
$error = null;
$editStudent = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_student') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        if ($name && $email && $password) {
            $stmt = $pdo->prepare('INSERT INTO students (name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $message = 'Student added successfully.';
        }
    }
    if ($_POST['action'] === 'update_student') {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        if ($id && $name && $email) {
            $sql = 'UPDATE students SET name = ?, email = ?';
            $params = [$name, $email];
            if ($password) {
                $sql .= ', password = ?';
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }
            $sql .= ' WHERE id = ?';
            $params[] = $id;
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                $message = 'Student updated successfully.';
            } else {
                $error = 'Unable to update student.';
            }
        }
    }
    if ($_POST['action'] === 'delete_student') {
        $id = intval($_POST['id']);
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
            if ($stmt->execute([$id])) {
                $message = 'Student deleted successfully.';
            } else {
                $error = 'Unable to delete student.';
            }
        }
    }
}
if (isset($_GET['edit_id'])) {
    $editId = intval($_GET['edit_id']);
    $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
    $stmt->execute([$editId]);
    $editStudent = $stmt->fetch();
}
$students = $pdo->query('SELECT * FROM students ORDER BY id DESC')->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/admin/manage_students.php'); ?>
<?php render_topbar('Manage Students'); ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white"><?= $editStudent ? 'Edit Student' : 'Add Student' ?></div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editStudent ? 'update_student' : 'add_student' ?>" />
                    <?php if ($editStudent): ?>
                        <input type="hidden" name="id" value="<?= $editStudent['id'] ?>" />
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" value="<?= htmlspecialchars($editStudent['name'] ?? '') ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($editStudent['email'] ?? '') ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <?= $editStudent ? '(leave blank to keep current)' : '' ?></label>
                        <input type="password" class="form-control" name="password" <?= $editStudent ? '' : 'required' ?> />
                    </div>
                    <button class="btn btn-danger"><?= $editStudent ? 'Update Student' : 'Add Student' ?></button>
                    <?php if ($editStudent): ?>
                        <a href="/ODC/admin/manage_students.php" class="btn btn-secondary ms-2">Add New</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white">Student List</div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Email</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= $student['id'] ?></td>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td>
                                <a href="?edit_id=<?= $student['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="post" class="d-inline ms-1" onsubmit="return confirm('Delete this student?');">
                                    <input type="hidden" name="action" value="delete_student" />
                                    <input type="hidden" name="id" value="<?= $student['id'] ?>" />
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
