<?php
/**
 * Admin Business Hub - Edit Category Page
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';

use Karyalay\Models\BusinessHubCategory;

startSecureSession();
require_admin();
require_permission('hero_slides.manage');

$hubModel = new BusinessHubCategory();

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header('Location: ' . get_app_base_url() . '/admin/business-hub.php');
    exit;
}

$category = $hubModel->getById($id);
if (!$category) {
    $_SESSION['admin_error'] = 'Category not found.';
    header('Location: ' . get_app_base_url() . '/admin/business-hub.php');
    exit;
}

$errors = [];
$formData = $category;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'update_category';

    if ($action === 'update_category') {
        $formData = [
            'title' => trim($_POST['title'] ?? ''),
            'title_line2' => trim($_POST['title_line2'] ?? ''),
            'link_url' => trim($_POST['link_url'] ?? ''),
            'color_class' => $_POST['color_class'] ?? 'people',
            'position' => $_POST['position'] ?? 'top-left',
            'display_order' => intval($_POST['display_order'] ?? 0),
            'status' => $_POST['status'] ?? 'DRAFT'
        ];

        if (empty($formData['title'])) {
            $errors['title'] = 'Title is required';
        }

        if (empty($errors)) {
            if ($hubModel->update($id, $formData)) {
                $_SESSION['admin_success'] = 'Category updated successfully.';
                header('Location: ' . get_app_base_url() . '/admin/business-hub/edit.php?id=' . urlencode($id));
                exit;
            } else {
                $errors['general'] = 'Failed to update category.';
            }
        }
    } elseif ($action === 'add_node') {
        $nodeData = [
            'category_id' => $id,
            'title' => trim($_POST['node_title'] ?? ''),
            'link_url' => trim($_POST['node_link_url'] ?? ''),
            'display_order' => intval($_POST['node_display_order'] ?? 0),
            'status' => $_POST['node_status'] ?? 'DRAFT'
        ];

        if (empty($nodeData['title'])) {
            $errors['node_title'] = 'Node title is required';
        } elseif (!$hubModel->canAddNode($id)) {
            $errors['node_title'] = 'Maximum nodes (' . BusinessHubCategory::getMaxNodesPerCategory() . ') reached for this category';
        } else {
            $nodeId = $hubModel->createNode($nodeData);
            if ($nodeId) {
                $_SESSION['admin_success'] = 'Node added successfully.';
                header('Location: ' . get_app_base_url() . '/admin/business-hub/edit.php?id=' . urlencode($id));
                exit;
            } else {
                $errors['node_title'] = 'Failed to add node.';
            }
        }
    } elseif ($action === 'update_node') {
        $nodeId = $_POST['node_id'] ?? '';
        $nodeData = [
            'title' => trim($_POST['node_title'] ?? ''),
            'link_url' => trim($_POST['node_link_url'] ?? ''),
            'display_order' => intval($_POST['node_display_order'] ?? 0),
            'status' => $_POST['node_status'] ?? 'DRAFT'
        ];

        if (!empty($nodeId) && !empty($nodeData['title'])) {
            if ($hubModel->updateNode($nodeId, $nodeData)) {
                $_SESSION['admin_success'] = 'Node updated successfully.';
            }
        }
        header('Location: ' . get_app_base_url() . '/admin/business-hub/edit.php?id=' . urlencode($id));
        exit;
    } elseif ($action === 'delete_node') {
        $nodeId = $_POST['node_id'] ?? '';
        if (!empty($nodeId)) {
            if ($hubModel->deleteNode($nodeId)) {
                $_SESSION['admin_success'] = 'Node deleted successfully.';
            }
        }
        header('Location: ' . get_app_base_url() . '/admin/business-hub/edit.php?id=' . urlencode($id));
        exit;
    }

    // Refresh category data
    $category = $hubModel->getById($id);
}

$nodes = $category['nodes'] ?? [];
$canAddNode = $hubModel->canAddNode($id);

include_admin_header('Edit Business Hub Category');
?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <nav class="breadcrumb">
            <a href="<?php echo get_app_base_url(); ?>/admin/business-hub.php">Business Hub</a>
            <span class="separator">/</span>
            <span>Edit Category</span>
        </nav>
        <h1 class="admin-page-title">Edit: <?php echo htmlspecialchars($category['title']); ?></h1>
    </div>
</div>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_SESSION['admin_success']); unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($errors['general']); ?></div>
<?php endif; ?>

<form method="POST" action="" class="admin-form">
    <input type="hidden" name="action" value="update_category">
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
                    </div>

                    <div class="form-group">
                        <label for="title_line2" class="form-label">Title (Line 2)</label>
                        <input type="text" id="title_line2" name="title_line2" class="form-control"
                               value="<?php echo htmlspecialchars($formData['title_line2'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="link_url" class="form-label">Link URL</label>
                        <input type="text" id="link_url" name="link_url" class="form-control"
                               value="<?php echo htmlspecialchars($formData['link_url'] ?? ''); ?>" placeholder="/features or https://...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Color Theme</label>
                        <div class="color-theme-selector">
                            <label class="color-option <?php echo ($formData['color_class'] ?? '') === 'people' ? 'selected' : ''; ?>">
                                <input type="radio" name="color_class" value="people" <?php echo ($formData['color_class'] ?? '') === 'people' ? 'checked' : ''; ?>>
                                <span class="color-swatch swatch-people"></span>
                            </label>
                            <label class="color-option <?php echo ($formData['color_class'] ?? '') === 'operations' ? 'selected' : ''; ?>">
                                <input type="radio" name="color_class" value="operations" <?php echo ($formData['color_class'] ?? '') === 'operations' ? 'checked' : ''; ?>>
                                <span class="color-swatch swatch-operations"></span>
                            </label>
                            <label class="color-option <?php echo ($formData['color_class'] ?? '') === 'finance' ? 'selected' : ''; ?>">
                                <input type="radio" name="color_class" value="finance" <?php echo ($formData['color_class'] ?? '') === 'finance' ? 'checked' : ''; ?>>
                                <span class="color-swatch swatch-finance"></span>
                            </label>
                            <label class="color-option <?php echo ($formData['color_class'] ?? '') === 'control' ? 'selected' : ''; ?>">
                                <input type="radio" name="color_class" value="control" <?php echo ($formData['color_class'] ?? '') === 'control' ? 'checked' : ''; ?>>
                                <span class="color-swatch swatch-control"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="position" class="form-label">Position</label>
                        <select id="position" name="position" class="form-control">
                            <option value="top-left" <?php echo ($formData['position'] ?? '') === 'top-left' ? 'selected' : ''; ?>>Top Left</option>
                            <option value="top-right" <?php echo ($formData['position'] ?? '') === 'top-right' ? 'selected' : ''; ?>>Top Right</option>
                            <option value="bottom-left" <?php echo ($formData['position'] ?? '') === 'bottom-left' ? 'selected' : ''; ?>>Bottom Left</option>
                            <option value="bottom-right" <?php echo ($formData['position'] ?? '') === 'bottom-right' ? 'selected' : ''; ?>>Bottom Right</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Nodes Section -->
            <div class="admin-card" style="margin-top: 24px;">
                <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h2 class="admin-card-title">Feature Nodes (<?php echo count($nodes); ?>/<?php echo BusinessHubCategory::getMaxNodesPerCategory(); ?>)</h2>
                </div>
                <div class="admin-card-body">
                    <?php if (empty($nodes)): ?>
                        <p class="empty-message">No nodes added yet. Add your first node below.</p>
                    <?php else: ?>
                        <div class="nodes-list">
                            <?php foreach ($nodes as $node): ?>
                                <div class="node-item">
                                    <div class="node-info">
                                        <span class="node-title"><?php echo htmlspecialchars($node['title']); ?></span>
                                        <?php if (!empty($node['link_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($node['link_url']); ?>" target="_blank" class="node-link">🔗</a>
                                        <?php endif; ?>
                                        <span class="node-status badge badge-<?php echo $node['status'] === 'PUBLISHED' ? 'success' : 'secondary'; ?>">
                                            <?php echo $node['status']; ?>
                                        </span>
                                    </div>
                                    <div class="node-actions">
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="editNode('<?php echo htmlspecialchars($node['id']); ?>', '<?php echo htmlspecialchars(addslashes($node['title'])); ?>', '<?php echo htmlspecialchars(addslashes($node['link_url'] ?? '')); ?>', <?php echo $node['display_order']; ?>, '<?php echo $node['status']; ?>')">Edit</button>
                                        <button type="button" class="btn btn-sm btn-text btn-danger" onclick="deleteNode('<?php echo htmlspecialchars($node['id']); ?>')">Delete</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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
                            <option value="DRAFT" <?php echo ($formData['status'] ?? '') === 'DRAFT' ? 'selected' : ''; ?>>Draft</option>
                            <option value="PUBLISHED" <?php echo ($formData['status'] ?? '') === 'PUBLISHED' ? 'selected' : ''; ?>>Published</option>
                            <option value="ARCHIVED" <?php echo ($formData['status'] ?? '') === 'ARCHIVED' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="display_order" class="form-label">Display Order</label>
                        <input type="number" id="display_order" name="display_order" class="form-control"
                               value="<?php echo htmlspecialchars($formData['display_order'] ?? 0); ?>" min="0">
                    </div>
                </div>
                <div class="admin-card-footer">
                    <button type="submit" class="btn btn-primary btn-block">Update Category</button>
                    <a href="<?php echo get_app_base_url(); ?>/admin/business-hub.php" class="btn btn-text btn-block">Back to List</a>
                </div>
            </div>

            <div class="admin-card" style="margin-top: 16px;">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">Preview</h2>
                </div>
                <div class="admin-card-body">
                    <div class="preview-wing wing-<?php echo htmlspecialchars($formData['color_class'] ?? 'people'); ?>">
                        <span class="preview-title">
                            <?php echo htmlspecialchars($formData['title']); ?>
                            <?php if (!empty($formData['title_line2'])): ?>
                                <br><?php echo htmlspecialchars($formData['title_line2']); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Add/Edit Node Form (separate from main form) -->
<div class="admin-card" style="margin-top: 24px; max-width: calc(100% - 344px);" id="nodeFormCard">
    <div class="admin-card-header">
        <h2 class="admin-card-title" id="nodeFormTitle"><?php echo $canAddNode ? 'Add New Node' : 'Edit Node'; ?></h2>
    </div>
    <div class="admin-card-body">
        <?php if ($canAddNode || !empty($nodes)): ?>
        <form method="POST" action="" id="nodeForm">
            <input type="hidden" name="action" value="add_node" id="nodeAction">
            <input type="hidden" name="node_id" value="" id="nodeId">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="node_title" class="form-label">Node Title <span class="required">*</span></label>
                    <input type="text" id="node_title" name="node_title" class="form-control <?php echo isset($errors['node_title']) ? 'is-invalid' : ''; ?>" required>
                    <?php if (isset($errors['node_title'])): ?>
                        <div class="form-error"><?php echo htmlspecialchars($errors['node_title']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="node_link_url" class="form-label">Link URL</label>
                    <input type="text" id="node_link_url" name="node_link_url" class="form-control" placeholder="/page or https://...">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="node_display_order" class="form-label">Display Order</label>
                    <input type="number" id="node_display_order" name="node_display_order" class="form-control" value="0" min="0">
                </div>
                <div class="form-group">
                    <label for="node_status" class="form-label">Status</label>
                    <select id="node_status" name="node_status" class="form-control">
                        <option value="DRAFT">Draft</option>
                        <option value="PUBLISHED">Published</option>
                        <option value="ARCHIVED">Archived</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-secondary" id="nodeSubmitBtn"><?php echo $canAddNode ? 'Add Node' : 'Update Node'; ?></button>
                <button type="button" class="btn btn-text" onclick="resetNodeForm()" id="nodeCancelBtn" style="display: none;">Cancel</button>
            </div>
        </form>
        <?php else: ?>
            <p class="text-muted">Maximum nodes reached for this category.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Separate form for node deletion -->
<form method="POST" action="" id="deleteNodeForm" style="display: none;">
    <input type="hidden" name="action" value="delete_node">
    <input type="hidden" name="node_id" value="" id="deleteNodeId">
</form>

<script>
function deleteNode(nodeId) {
    if (confirm('Delete this node?')) {
        document.getElementById('deleteNodeId').value = nodeId;
        document.getElementById('deleteNodeForm').submit();
    }
}

function editNode(id, title, linkUrl, order, status) {
    document.getElementById('nodeAction').value = 'update_node';
    document.getElementById('nodeId').value = id;
    document.getElementById('node_title').value = title;
    document.getElementById('node_link_url').value = linkUrl;
    document.getElementById('node_display_order').value = order;
    document.getElementById('node_status').value = status;
    document.getElementById('nodeFormTitle').textContent = 'Edit Node';
    document.getElementById('nodeSubmitBtn').textContent = 'Update Node';
    document.getElementById('nodeCancelBtn').style.display = 'inline-block';
    document.getElementById('nodeFormCard').scrollIntoView({ behavior: 'smooth' });
}

function resetNodeForm() {
    document.getElementById('nodeAction').value = 'add_node';
    document.getElementById('nodeId').value = '';
    document.getElementById('node_title').value = '';
    document.getElementById('node_link_url').value = '';
    document.getElementById('node_display_order').value = '0';
    document.getElementById('node_status').value = 'DRAFT';
    document.getElementById('nodeFormTitle').textContent = 'Add New Node';
    document.getElementById('nodeSubmitBtn').textContent = 'Add Node';
    document.getElementById('nodeCancelBtn').style.display = 'none';
}

// Color theme selector
document.querySelectorAll('.color-option input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
        this.closest('.color-option').classList.add('selected');
        // Update preview
        const preview = document.querySelector('.preview-wing');
        if (preview) {
            preview.className = 'preview-wing wing-' + this.value;
        }
    });
});
</script>

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
.form-control { width: 100%; padding: 10px 12px; border: 1px solid var(--color-gray-300); border-radius: 6px; font-size: 14px; box-sizing: border-box; }
.form-control:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.form-control.is-invalid { border-color: #dc2626; }
.form-error { color: #dc2626; font-size: 13px; margin-top: 4px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-actions { display: flex; gap: 12px; margin-top: 16px; }
.btn-block { width: 100%; margin-top: 8px; }
.btn-block:first-child { margin-top: 0; }
.alert { padding: 16px; border-radius: 8px; margin-bottom: 24px; }
.alert-success { background-color: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; }
.alert-error { background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
.nodes-list { display: flex; flex-direction: column; gap: 12px; }
.node-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #f9fafb; border-radius: 8px; }
.node-info { display: flex; align-items: center; gap: 12px; }
.node-title { font-weight: 600; color: var(--color-gray-900); }
.node-link { text-decoration: none; }
.node-status { font-size: 11px; }
.node-actions { display: flex; gap: 8px; }
.empty-message { color: var(--color-gray-500); text-align: center; padding: 20px; }
.badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
.badge-success { background-color: #d1fae5; color: #065f46; }
.badge-secondary { background-color: #f3f4f6; color: #6b7280; }
.btn-danger { background-color: #dc2626 !important; color: white !important; border: 1px solid #dc2626 !important; }
.btn-danger:hover { background-color: #991b1b !important; border-color: #991b1b !important; color: white !important; }
.preview-wing { width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-align: center; margin: 0 auto; }
.preview-title { font-size: 12px; font-weight: 700; line-height: 1.2; }
.wing-people { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
.wing-operations { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
.wing-finance { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
.wing-control { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #3730a3; }

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

@media (max-width: 1024px) { .form-grid { grid-template-columns: 1fr; } #nodeFormCard { max-width: 100% !important; } }
@media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }
</style>

<?php include_admin_footer(); ?>
