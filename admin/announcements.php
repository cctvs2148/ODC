<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('admin');
$message = null;
$error = null;
$editAnnouncement = null;
$uploadPath = '/ODC/uploads/announcements/';
$uploadDir = __DIR__ . '/../uploads/announcements/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $title = trim($_POST['title'] ?? '');
    $messageText = trim($_POST['message'] ?? '');
    $status = in_array($_POST['status'] ?? 'active', ['active', 'inactive'], true) ? $_POST['status'] : 'active';
    $startDate = trim($_POST['start_date'] ?? '') ?: null;
    $endDate = trim($_POST['end_date'] ?? '') ?: null;
    $imagePath = null;
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed, true)) {
            $error = 'Only JPG, PNG, and GIF images are allowed.';
        } else {
            $newName = 'announcement_' . time() . '_' . preg_replace('/[^a-z0-9_\.-]/i', '_', $fileName);
            $destination = $uploadDir . $newName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $imagePath = $uploadPath . $newName;
            } else {
                $error = 'Unable to save uploaded image.';
            }
        }
    }
    if (!$error) {
        if ($_POST['action'] === 'add_announcement') {
            if ($title && $messageText) {
                $stmt = $pdo->prepare('INSERT INTO announcements (title, message, image, status, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$title, $messageText, $imagePath, $status, $startDate, $endDate]);
                $message = 'Announcement created successfully.';
            } else {
                $error = 'Title and message are required.';
            }
        }
        if ($_POST['action'] === 'update_announcement') {
            $id = intval($_POST['id']);
            if ($id && $title && $messageText) {
                $current = get_announcement($id);
                if ($imagePath === null) {
                    $imagePath = $current['image'];
                } elseif (!empty($current['image'])) {
                    $oldFile = rtrim(realpath(__DIR__ . '/../..'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($current['image'], '/\\'));
                    if (is_file($oldFile)) {
                        @unlink($oldFile);
                    }
                }
                $stmt = $pdo->prepare('UPDATE announcements SET title = ?, message = ?, image = ?, status = ?, start_date = ?, end_date = ? WHERE id = ?');
                $stmt->execute([$title, $messageText, $imagePath, $status, $startDate, $endDate, $id]);
                $message = 'Announcement updated successfully.';
            } else {
                $error = 'Title and message are required.';
            }
        }
        if ($_POST['action'] === 'delete_announcement') {
            $id = intval($_POST['id']);
            if ($id) {
                $current = get_announcement($id);
                if ($current && !empty($current['image'])) {
                    $oldFile = rtrim(realpath(__DIR__ . '/../..'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($current['image'], '/\\'));
                    if (is_file($oldFile)) {
                        @unlink($oldFile);
                    }
                }
                $stmt = $pdo->prepare('DELETE FROM announcements WHERE id = ?');
                $stmt->execute([$id]);
                $message = 'Announcement deleted.';
            }
        }
    }
}
if (isset($_GET['edit_id'])) {
    $editId = intval($_GET['edit_id']);
    $editAnnouncement = get_announcement($editId);
}
$announcements = get_announcements(false);
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/admin/announcements.php'); ?>
<?php render_topbar('Manage Announcements'); ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white"><?= $editAnnouncement ? 'Edit Announcement' : 'Add Announcement' ?></div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?= $editAnnouncement ? 'update_announcement' : 'add_announcement' ?>" />
                    <?php if ($editAnnouncement): ?>
                        <input type="hidden" name="id" value="<?= $editAnnouncement['id'] ?>" />
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input class="form-control" name="title" value="<?= htmlspecialchars($editAnnouncement['title'] ?? '') ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="4" required><?= htmlspecialchars($editAnnouncement['message'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" />
                        <?php if (!empty($editAnnouncement['image'])): ?>
                            <p class="mt-2"><strong>Current image:</strong><br><img src="<?= htmlspecialchars($editAnnouncement['image']) ?>" alt="Announcement Image" style="max-width: 180px; height:auto; border-radius:0.5rem;" /></p>
                        <?php endif; ?>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input class="form-control" type="date" name="start_date" value="<?= htmlspecialchars($editAnnouncement['start_date'] ?? '') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input class="form-control" type="date" name="end_date" value="<?= htmlspecialchars($editAnnouncement['end_date'] ?? '') ?>" />
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= (!isset($editAnnouncement['status']) || $editAnnouncement['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= (isset($editAnnouncement['status']) && $editAnnouncement['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <button class="btn btn-danger"><?= $editAnnouncement ? 'Update Announcement' : 'Create Announcement' ?></button>
                    <?php if ($editAnnouncement): ?>
                        <a href="/ODC/admin/announcements.php" class="btn btn-secondary ms-2">Create New</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white">Announcements</div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr><th>#</th><th>Title</th><th>Status</th><th>Period</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $announcement): ?>
                        <tr>
                            <td><?= $announcement['id'] ?></td>
                            <td><?= htmlspecialchars($announcement['title']) ?></td>
                            <td><?= htmlspecialchars($announcement['status']) ?></td>
                            <td><?= htmlspecialchars($announcement['start_date'] ?? 'Any') ?> - <?= htmlspecialchars($announcement['end_date'] ?? 'Any') ?></td>
                            <td>
                                <a href="?edit_id=<?= $announcement['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="post" class="d-inline ms-1" onsubmit="return confirm('Delete this announcement?');">
                                    <input type="hidden" name="action" value="delete_announcement" />
                                    <input type="hidden" name="id" value="<?= $announcement['id'] ?>" />
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($announcements)): ?>
                        <tr><td colspan="5" class="text-muted text-center">No announcements found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
