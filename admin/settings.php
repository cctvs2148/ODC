<?php
define('APP_INIT', true);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/layout.php';
require_role('admin');
$message = null;
$error = null;
$uploadPath = '/ODC/uploads/logo/';
$uploadDir = __DIR__ . '/../uploads/logo/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_branding') {
        $companyName = trim($_POST['company_name'] ?? '') ?: 'ODC Hotel Duty';
        $tagline = trim($_POST['company_tagline'] ?? '');
        set_site_setting('company_name', $companyName);
        set_site_setting('company_tagline', $tagline);
        $message = 'Branding settings saved successfully.';
    }
    if ($_POST['action'] === 'upload_logo') {
        if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $fileName = basename($_FILES['logo']['name']);
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($ext, $allowed, true)) {
                $error = 'Only JPG, PNG, and GIF images are allowed.';
            } else {
                $newName = 'company_logo_' . time() . '.' . $ext;
                $destination = $uploadDir . $newName;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) {
                    set_site_setting('company_logo', $uploadPath . $newName);
                    $message = 'Logo uploaded successfully.';
                } else {
                    $error = 'Unable to upload the logo file.';
                }
            }
        } else {
            $error = 'Please select a logo file to upload.';
        }
    }
}
$companyName = get_site_setting('company_name', 'ODC Hotel Duty');
$companyTagline = get_site_setting('company_tagline', 'Hotel duty management and student placement.');
$currentLogo = get_site_setting('company_logo');
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php render_sidebar($_SESSION['role'], '/ODC/admin/settings.php'); ?>
<?php render_topbar('Branding Settings'); ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white">Company Branding</div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="action" value="save_branding" />
                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input class="form-control" name="company_name" value="<?= htmlspecialchars($companyName) ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tagline</label>
                        <input class="form-control" name="company_tagline" value="<?= htmlspecialchars($companyTagline) ?>" />
                    </div>
                    <button class="btn btn-danger">Save Branding</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white">Logo Upload</div>
            <div class="card-body">
                <?php if ($currentLogo): ?>
                    <div class="mb-3 text-center">
                        <img src="<?= htmlspecialchars($currentLogo) ?>" alt="Current Logo" style="max-width: 220px; max-height: 120px; object-fit: contain;" />
                    </div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload_logo" />
                    <div class="mb-3">
                        <label class="form-label">Upload New Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*" required />
                    </div>
                    <button class="btn btn-danger">Upload Logo</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
