<?php
/**
 * Admin Business Hub Management Page
 */

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

use Karyalay\Models\BusinessHubCategory;

startSecureSession();
require_admin();
require_permission('hero_slides.manage');

$hubModel = new BusinessHubCategory();

$status_filter = $_GET['status'] ?? '';
$search_query = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$filters = [];
if (!empty($status_filter) && in_array($status_filter, ['DRAFT', 'PUBLISHED', 'ARCHIVED'])) {
    $filters['status'] = $status_filter;
}
if (!empty($search_query)) {
    $filters['search'] = $search_query;
}

$categories = $hubModel->getAll($filters, $per_page, $offset);
$total_categories = $hubModel->count($filters);
$total_pages = ceil($total_categories / $per_page);
$canAddMore = $hubModel->canAddCategory();

include_admin_header('Business Hub');
?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <h1 class="admin-page-title">Business Hub</h1>
        <p class="admin-page-description">
            Manage the Business Hub section categories and feature nodes 
            (max <?php echo BusinessHubCategory::getMaxCategories(); ?> categories, 
            <?php echo BusinessHubCategory::getMaxNodesPerCategory(); ?> nodes each)
        </p>
    </div>
    <div class="admin-page-header-actions">
        <?php if ($canAddMore): ?>
            <a href="<?php echo get_app_base_url(); ?>/admin/business-hub/new.php" class="btn btn-primary">
                + Add Category
            </a>
        <?php else: ?>
            <span class="btn btn-secondary disabled" title="Maximum categories reached">
                Max Categories Reached
            </span>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="alert alert-success">
        <?php 
        echo htmlspecialchars($_SESSION['admin_success']); 
        unset($_SESSION['admin_success']);
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['admin_error'])): ?>
    <div class="alert alert-error">
        <?php 
        echo htmlspecialchars($_SESSION['admin_error']); 
        unset($_SESSION['admin_error']);
        ?>
    </div>
<?php endif; ?>

<div class="admin-filters-section">
    <form method="GET" action="<?php echo get_app_base_url(); ?>/admin/business-hub.php" class="admin-filters-form">
        <div class="admin-filter-group">
            <label for="search" class="admin-filter-label">Search</label>
            <input type="text" id="search" name="search" class="admin-filter-input" 
                   placeholder="Search by title..."
                   value="<?php echo htmlspecialchars($search_query); ?>">
        </div>
        
        <div class="admin-filter-group">
            <label for="status" class="admin-filter-label">Status</label>
            <select id="status" name="status" class="admin-filter-select">
                <option value="">All Statuses</option>
                <option value="DRAFT" <?php echo $status_filter === 'DRAFT' ? 'selected' : ''; ?>>Draft</option>
                <option value="PUBLISHED" <?php echo $status_filter === 'PUBLISHED' ? 'selected' : ''; ?>>Published</option>
                <option value="ARCHIVED" <?php echo $status_filter === 'ARCHIVED' ? 'selected' : ''; ?>>Archived</option>
            </select>
        </div>
        
        <div class="admin-filter-actions">
            <button type="submit" class="btn btn-secondary">Apply Filters</button>
            <a href="<?php echo get_app_base_url(); ?>/admin/business-hub.php" class="btn btn-text">Clear</a>
        </div>
    </form>
</div>

<div class="admin-card">
    <?php if (empty($categories)): ?>
        <?php 
        render_empty_state(
            'No categories found',
            'Get started by creating your first business hub category',
            '/admin/business-hub/new.php',
            'Add Category'
        );
        ?>
    <?php else: ?>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Position</th>
                        <th>Color</th>
                        <th>Nodes</th>
                        <th>Link</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td>
                                <div class="table-cell-primary">
                                    <?php echo htmlspecialchars($category['title']); ?>
                                    <?php if (!empty($category['title_line2'])): ?>
                                        <br><span class="text-muted"><?php echo htmlspecialchars($category['title_line2']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="position-badge position-<?php echo htmlspecialchars($category['position']); ?>">
                                    <?php echo ucwords(str_replace('-', ' ', $category['position'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="color-preview color-<?php echo htmlspecialchars($category['color_class']); ?>">
                                    <?php echo ucfirst($category['color_class']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="node-count">
                                    <?php echo $category['node_count']; ?>/<?php echo BusinessHubCategory::getMaxNodesPerCategory(); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($category['link_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($category['link_url']); ?>" target="_blank" class="link-preview">View</a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo get_status_badge($category['status']); ?></td>
                            <td><?php echo htmlspecialchars($category['display_order']); ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?php echo get_app_base_url(); ?>/admin/business-hub/edit.php?id=<?php echo urlencode($category['id']); ?>" 
                                       class="btn btn-sm btn-secondary">Edit</a>
                                    <a href="<?php echo get_app_base_url(); ?>/admin/business-hub/delete.php?id=<?php echo urlencode($category['id']); ?>" 
                                       class="btn btn-sm btn-text btn-danger"
                                       onclick="return confirm('Delete this category and all its nodes?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <div class="admin-card-footer">
                <?php 
                $base_url = '/admin/business-hub.php';
                $query_params = [];
                if (!empty($status_filter)) $query_params[] = 'status=' . urlencode($status_filter);
                if (!empty($search_query)) $query_params[] = 'search=' . urlencode($search_query);
                if (!empty($query_params)) $base_url .= '?' . implode('&', $query_params);
                render_pagination($page, $total_pages, $base_url);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="admin-card-footer">
            <p class="admin-card-footer-text">
                Showing <?php echo count($categories); ?> of <?php echo $total_categories; ?> categor<?php echo $total_categories !== 1 ? 'ies' : 'y'; ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<style>
.admin-page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--spacing-6); gap: var(--spacing-4); }
.admin-page-header-content { flex: 1; }
.admin-page-title { font-size: var(--font-size-2xl); font-weight: var(--font-weight-bold); color: var(--color-gray-900); margin: 0 0 var(--spacing-2) 0; }
.admin-page-description { font-size: var(--font-size-base); color: var(--color-gray-600); margin: 0; }
.admin-page-header-actions { display: flex; gap: var(--spacing-3); }
.admin-filters-section { background: white; border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); margin-bottom: var(--spacing-6); }
.admin-filters-form { display: flex; gap: var(--spacing-4); align-items: flex-end; flex-wrap: wrap; }
.admin-filter-group { display: flex; flex-direction: column; gap: var(--spacing-2); flex: 1; min-width: 200px; }
.admin-filter-label { font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold); color: var(--color-gray-700); }
.admin-filter-input, .admin-filter-select { padding: var(--spacing-2) var(--spacing-3); border: 1px solid var(--color-gray-300); border-radius: var(--radius-md); font-size: var(--font-size-base); }
.admin-filter-actions { display: flex; gap: var(--spacing-2); }
.table-cell-primary { font-weight: var(--font-weight-semibold); color: var(--color-gray-900); }
.text-muted { color: var(--color-gray-500); font-size: var(--font-size-sm); }
.position-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; background: #f3f4f6; }
.color-preview { padding: 4px 10px; border-radius: 4px; font-size: 12px; color: white; }
.color-people { background: linear-gradient(135deg, #3b82f6, #1e40af); }
.color-operations { background: linear-gradient(135deg, #f59e0b, #d97706); }
.color-finance { background: linear-gradient(135deg, #10b981, #059669); }
.color-control { background: linear-gradient(135deg, #6366f1, #4f46e5); }
.node-count { font-weight: 600; color: var(--color-gray-700); }
.link-preview { color: var(--color-primary); text-decoration: none; font-size: var(--font-size-sm); }
.link-preview:hover { text-decoration: underline; }
.table-actions { display: flex; gap: var(--spacing-2); }
.btn-danger { background-color: #dc2626 !important; color: white !important; border: 1px solid #dc2626 !important; }
.btn-danger:hover { background-color: #991b1b !important; border-color: #991b1b !important; color: white !important; }
.admin-card-footer { padding: var(--spacing-4); border-top: 1px solid var(--color-gray-200); }
.admin-card-footer-text { font-size: var(--font-size-sm); color: var(--color-gray-600); margin: 0; }
.alert { padding: 16px; border-radius: 8px; margin-bottom: 24px; }
.alert-success { background-color: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; }
.alert-error { background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
.disabled { opacity: 0.6; cursor: not-allowed; pointer-events: none; }
@media (max-width: 768px) { .admin-page-header { flex-direction: column; } .admin-filters-form { flex-direction: column; } }
</style>

<?php include_admin_footer(); ?>
