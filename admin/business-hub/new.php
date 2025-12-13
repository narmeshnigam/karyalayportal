<?php
/**
 * Admin Business Hub - New Category Page
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';

use Karyalay\Models\BusinessHubCategory;

startSecureSession();
require_admin();
require_permission('hero_slides.manage');

$hubModel = new BusinessHubCategory();

// Check if can add more categories
if (!$hubModel->canAddCategory()) {
    $_SESSION['admin_error'] = 'Maximum number of categories (' . BusinessHubCategory::getMaxCategories() . ') reached.';
    header('Location: ' . get_app_base_url() . '/admin/business-hub.php');
    exit;
}

$errors = [];
$formData = [
    'title' => '',
    'title_line2' => '',
    'link_url' => '',
    'color_class' => 'people',
    'position' => 'top-left',
    'display_order' => 0,
    'status' => 'DRAFT'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'title' => trim($_POST['title'] ?? ''),
        'title_line2' => trim($_POST['title_line2'] ?? ''),
        'link_url' => trim($_POST['link_url'] ?? ''),
        'color_class' => $_POST['color_class'] ?? 'people',
        'position' => $_POST['position'] ?? 'top-left',
        'display_order' => intval($_POST['display_order'] ?? 0),
        'status' => $_POST['status'] ?? 'DRAFT'
    ];

    // Validation
    if (empty($formData['title'])) {
        $errors['title'] = 'Title is required';
    }

    if (!in_array($formData['color_class'], ['people', 'operations', 'finance', 'control'])) {
        $errors['color_class'] = 'Invalid color class';
    }

    if (!in_array($formData['position'], ['top-left', 'top-right', 'bottom-left', 'bottom-right'])) {
        $errors['position'] = 'Invalid position';
    }

    if (!in_array($formData['status'], ['DRAFT', 'PUBLISHED', 'ARCHIVED'])) {
        $errors['status'] = 'Invalid status';
    }

    if (!empty($formData['link_url']) && !filter_var($formData['link_url'], FILTER_VALIDATE_URL)) {
        // Allow relative URLs
        if (!preg_match('/^\//', $formData['link_url'])) {
            $errors['link_url'] = 'Invalid URL format';
        }
    }

    if (empty($errors)) {
        $id = $hubModel->create($formData);
        if ($id) {
            $_SESSION['admin_success'] = 'Category created successfully.';
            header('Location: ' . get_app_base_url() . '/admin/business-hub/edit.php?id=' . urlencode($id));
            exit;
        } else {
            $errors['general'] = 'Failed to create category. Maximum limit may have been reached.';
        }
    }
}

include_admin_header('New Business Hub Category');
?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <nav class="breadcrumb">
            <a href="<?php echo get_app_base_url(); ?>/admin/business-hub.php">Business Hub</a>
            <span class="separator">/</span>
            <span>New Category</span>
        </nav>
        <h1 class="admin-page-title">New Category</h1>
    </div>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($errors['general']); ?></div>
<?php endif; ?>

<form method="POST" action="" class="admin-form">
    <div class="form-grid">
        <div class="form-main">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">Category Details</h2>
                </div>
                <div class="admin-card-body">
                    <div class="form-group">
                        <label for="title" class="form-label">Title (Line 1) <span class="required">*</span></label>
                        <input type="text" id="title" name="title" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>"
                               value="<?php echo htmlspecialchars($formData['title']); ?>" required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="form-error"><?php echo htmlspecialchars($errors['title']); ?></div>
                        <?php endif; ?>
                        <small class="form-help">First line of the category title (e.g., "People")</small>
                    </div>

                    <div class="form-group">
                        <label for="title_line2" class="form-label">Title (Line 2)</label>
                        <input type="text" id="title_line2" name="title_line2" class="form-control"
                               value="<?php echo htmlspecialchars($formData['title_line2']); ?>">
                        <small class="form-help">Second line of the category title (e.g., "Management")</small>
                    </div>

                    <div class="form-group">
                        <label for="link_url" class="form-label">Link URL</label>
                        <input type="text" id="link_url" name="link_url" class="form-control <?php echo isset($errors['link_url']) ? 'is-invalid' : ''; ?>"
                               value="<?php echo htmlspecialchars($formData['link_url']); ?>" placeholder="/features or https://...">
                        <?php if (isset($errors['link_url'])): ?>
                            <div class="form-error"><?php echo htmlspecialchars($errors['link_url']); ?></div>
                        <?php endif; ?>
                        <small class="form-help">Optional link when clicking the category</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Color Theme</label>
                        <div class="color-theme-selector">
                            <label class="color-option <?php echo $formData['color_class'] === 'people' ? 'selected' : ''; ?>">
                                <input type="radio" name="color_class" value="people" <?php echo $formData['color_class'] === 'people' ? 'checked' : ''; ?>>
                                <span class="color-swatch swatch-people"></span>
                            </label>
                            <label class="color-option <?php echo $formData['color_class'] === 'operations' ? 'selected' : ''; ?>">
                                <input type="radio" name="color_class" value="operations" <?php echo $formData['color_class'] === 'operations' ? 'checked' : ''; ?>>
                                <span class="color-swatch swatch-operations"></span>
                            </label>
                            <label class="color-option <?php echo $formData['color_class'] === 'finance' ? 'selected' : ''; ?>">
                                <input type="radio" name="color_class" value="finance" <?php echo $formData['color_class'] === 'finance' ? 'checked' : ''; ?>>
                                <span class="color-swatch swatch-finance"></span>
                            </label>
                            <label class="color-option <?php echo $formData['color_class'] === 'control' ? 'selected' : ''; ?>">
                                <input type="radio" name="color_class" value="control" <?php echo $formData['color_class'] === 'control' ? 'checked' : ''; ?>>
                                <span class="color-swatch swatch-control"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="position" class="form-label">Position</label>
                        <select id="position" name="position" class="form-control">
                            <option value="top-left" <?php echo $formData['position'] === 'top-left' ? 'selected' : ''; ?>>Top Left</option>
                            <option value="top-right" <?php echo $formData['position'] === 'top-right' ? 'selected' : ''; ?>>Top Right</option>
                            <option value="bottom-left" <?php echo $formData['position'] === 'bottom-left' ? 'selected' : ''; ?>>Bottom Left</option>
                            <option value="bottom-right" <?php echo $formData['position'] === 'bottom-right' ? 'selected' : ''; ?>>Bottom Right</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-sidebar">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">Publish</h2>
                </div>
                <div class="admin-card-body">
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="DRAFT" <?php echo $formData['status'] === 'DRAFT' ? 'selected' : ''; ?>>Draft</option>
                            <option value="PUBLISHED" <?php echo $formData['status'] === 'PUBLISHED' ? 'selected' : ''; ?>>Published</option>
                            <option value="ARCHIVED" <?php echo $formData['status'] === 'ARCHIVED' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="display_order" class="form-label">Display Order</label>
                        <input type="number" id="display_order" name="display_order" class="form-control"
                               value="<?php echo htmlspecialchars($formData['display_order']); ?>" min="0">
                    </div>
                </div>
                <div class="admin-card-footer">
                    <button type="submit" class="btn btn-primary btn-block">Create Category</button>
                    <a href="<?php echo get_app_base_url(); ?>/admin/business-hub.php" class="btn btn-text btn-block">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 14px; margin-bottom: 8px; }
.breadcrumb a { color: var(--color-primary); text-decoration: none; }
.breadcrumb a:hover { text-decoration: underline; }
.breadcrumb .separator { color: var(--color-gray-400); }
.admin-page-title { font-size: 24px; font-weight: 700; color: var(--color-gray-900); margin: 0; }
.form-grid { display: grid; grid-template-columns: 1fr 320px; gap: 24px; }
.admin-card { background: white; border: 1px solid var(--color-gray-200); border-radius: 8px; }
.admin-card-header { padding: 16px 24px; border-bottom: 1px solid var(--color-gray-200); }
.admin-card-title { font-size: 16px; font-weight: 600; margin: 0; }
.admin-card-body { padding: 24px; }
.admin-card-footer { padding: 16px 24px; border-top: 1px solid var(--color-gray-200); }
.form-group { margin-bottom: 20px; }
.form-group:last-child { margin-bottom: 0; }
.form-label { display: block; font-size: 14px; font-weight: 600; color: var(--color-gray-700); margin-bottom: 6px; }
.form-label .required { color: #dc2626; }
.form-control { width: 100%; padding: 10px 12px; border: 1px solid var(--color-gray-300); border-radius: 6px; font-size: 14px; }
.form-control:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.form-control.is-invalid { border-color: #dc2626; }
.form-error { color: #dc2626; font-size: 13px; margin-top: 4px; }
.form-help { color: var(--color-gray-500); font-size: 13px; margin-top: 4px; display: block; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.btn-block { width: 100%; margin-top: 8px; }
.btn-block:first-child { margin-top: 0; }
.alert { padding: 16px; border-radius: 8px; margin-bottom: 24px; }
.alert-error { background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
/* Color Theme Selector */
.color-theme-selector { display: flex; gap: 12px; flex-wrap: wrap; }
.color-option { cursor: pointer; position: relative; }
.color-option input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
.color-swatch { display: block; width: 48px; height: 48px; border-radius: 50%; border: 3px solid transparent; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.color-option:hover .color-swatch { transform: scale(1.1); }
.color-option.selected .color-swatch,
.color-option input[type="radio"]:checked + .color-swatch { border-color: var(--color-gray-900); box-shadow: 0 0 0 2px white, 0 0 0 4px var(--color-gray-400); }
.swatch-people { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.swatch-operations { background: linear-gradient(135deg, #f59e0b, #d97706); }
.swatch-finance { background: linear-gradient(135deg, #10b981, #059669); }
.swatch-control { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }

@media (max-width: 1024px) { .form-grid { grid-template-columns: 1fr; } }
@media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }
</style>

<script>
// Color theme selector
document.querySelectorAll('.color-option input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
        this.closest('.color-option').classList.add('selected');
    });
});
</script>

<?php include_admin_footer(); ?>
