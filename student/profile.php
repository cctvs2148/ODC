<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('student');
$studentId = $_SESSION['user_id'];
$message = null;
$error = null;
$student = $pdo->prepare('SELECT * FROM students WHERE id = ?');
$student->execute([$studentId]);
$studentData = $student->fetch();
if (!$studentData) {
    header('Location: /ODC/student/dashboard.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name = trim($_POST['name'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');
    if (!$name) {
        $error = 'Name is required.';
    } elseif ($password && $password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $sql = 'UPDATE students SET name = ?';
        $params = [$name];
        if ($password) {
            $sql .= ', password = ?';
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }
        $sql .= ' WHERE id = ?';
        $params[] = $studentId;
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            $message = 'Profile updated successfully.';
            $studentData['name'] = $name;
        } else {
            $error = 'Unable to update profile. Please try again.';
        }
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/student/profile.php'); ?>
<?php render_topbar('Student Profile'); ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-danger text-white">My Profile</div>
    <div class="card-body">
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="action" value="update_profile" />
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input class="form-control" name="name" value="<?= htmlspecialchars($studentData['name']) ?>" required />
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" value="<?= htmlspecialchars($studentData['email']) ?>" disabled />
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input class="form-control" name="password" type="password" />
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input class="form-control" name="confirm_password" type="password" />
            </div>
            <button class="btn btn-danger">Save Changes</button>
        </form>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
