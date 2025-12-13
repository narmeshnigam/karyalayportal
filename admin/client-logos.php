<?php
/**
 * Admin Client Logos List Page
 * Displays table of all client logos for hero slider marquee
 */

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/admin_helpers.php';

use Karyalay\Models\ClientLogo;

startSecureSession();
require_admin();
require_permission('client_logos.manage');

// Check if table exists and handle gracefully
$tableExists = false;
$logos = [];
$total_logos = 0;
$total_pages = 0;

try {
    $clientLogoModel = new ClientLogo();
    
    // Get filters from query parameters
    $status_filter = $_GET['status'] ?? '';
    $search_query = $_GET['search'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 20;
    $offset = ($page - 1) * $per_page;

    // Build filters array
    $filters = [];
    if (!empty($status_filter) && in_array($status_filter, ['DRAFT', 'PUBLISHED', 'ARCHIVED'])) {
        $filters['status'] = $status_filter;
    }
    if (!empty($search_query)) {
        $filters['search'] = $search_query;
    }

    // Fetch logos
    $logos = $clientLogoModel->getAll($filters, $per_page, $offset);
    $total_logos = $clientLogoModel->count($filters);
    $total_pages = ceil($total_logos / $per_page);
    $tableExists = true;
    
} catch (Exception $e) {
    // Table doesn't exist - show setup message
    $tableError = $e->getMessage();
}

include_admin_header('Client Logos');
?>

<?php if (!$tableExists): ?>
<div class="admin-card">
    <div class="admin-card-body">
        <div class="setup-message">
            <h2>Setup Required</h2>
            <p>The client logos feature needs to be set up. Please run the database migration:</p>
            <pre><code>php database/run_migration_052.php</code></pre>
            <p>If you're on a production server, contact your system administrator to run this migration.</p>
        </div>
    </div>
</div>

<style>
.setup-message {
    text-align: center;
    padding: var(--spacing-8);
}

.setup-message h2 {
    color: var(--color-gray-900);
    margin-bottom: var(--spacing-4);
}

.setup-message p {
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-4);
}

.setup-message pre {
    background: var(--color-gray-100);
    padding: var(--spacing-3);
    border-radius: var(--radius-md);
    display: inline-block;
    margin: var(--spacing-4) 0;
}
</style>

<?php include_admin_footer(); ?>
<?php exit; ?>
<?php endif; ?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <h1 class="admin-page-title">Client Logos</h1>
        <p class="admin-page-description">Manage client logos displayed in the hero slider marquee</p>
    </div>
    <div class="admin-page-header-actions">
        <a href="<?php echo get_app_base_url(); ?>/admin/client-logos/new.php" class="btn btn-primary">
            Add New Logo
        </a>
    </div>
</div>

<!-- Filters and Search -->
<div class="admin-filters-section">
    <form method="GET" action="<?php echo get_app_base_url(); ?>/admin/client-logos.php" class="admin-filters-form">
        <div class="admin-filter-group">
            <label for="search" class="admin-filter-label">Search</label>
            <input 
                type="text" 
                id="search" 
                name="search" 
                class="admin-filter-input" 
                placeholder="Search by client name..."
                value="<?php echo htmlspecialchars($search_query); ?>"
            >
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
            <a href="<?php echo get_app_base_url(); ?>/admin/client-logos.php" class="btn btn-text">Clear</a>
        </div>
    </form>
</div>

<!-- Logos Table -->
<div class="admin-card">
    <?php if (empty($logos)): ?>
        <?php 
        render_empty_state(
            'No client logos found',
            'Get started by adding your first client logo',
            '/admin/client-logos/new.php',
            'Add Logo'
        );
        ?>
    <?php else: ?>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Client Name</th>
                        <th>Website</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logos as $logo): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($logo['logo_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($logo['client_name']); ?>"
                                     class="logo-preview-thumb">
                            </td>
                            <td>
                                <div class="table-cell-primary">
                                    <?php echo htmlspecialchars($logo['client_name']); ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($logo['website_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($logo['website_url']); ?>" target="_blank" class="link-preview">
                                        View Website
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo get_status_badge($logo['status']); ?></td>
                            <td><?php echo htmlspecialchars($logo['display_order']); ?></td>
                            <td><?php echo get_relative_time($logo['created_at']); ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?php echo get_app_base_url(); ?>/admin/client-logos/edit.php?id=<?php echo urlencode($logo['id']); ?>" 
                                       class="btn btn-sm btn-secondary">Edit</a>
                                    <a href="<?php echo get_app_base_url(); ?>/admin/client-logos/delete.php?id=<?php echo urlencode($logo['id']); ?>" 
                                       class="btn btn-sm btn-text btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this logo?');">Delete</a>
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
                $base_url = '/admin/client-logos.php';
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
                Showing <?php echo count($logos); ?> of <?php echo $total_logos; ?> logo<?php echo $total_logos !== 1 ? 's' : ''; ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<style>
.admin-page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--spacing-6);
    gap: var(--spacing-4);
}

.admin-page-header-content {
    flex: 1;
}

.admin-page-title {
    font-size: var(--font-size-2xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-gray-900);
    margin: 0 0 var(--spacing-2) 0;
}

.admin-page-description {
    font-size: var(--font-size-base);
    color: var(--color-gray-600);
    margin: 0;
}

.admin-page-header-actions {
    display: flex;
    gap: var(--spacing-3);
}

.admin-filters-section {
    background: white;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    padding: var(--spacing-4);
    margin-bottom: var(--spacing-6);
}

.admin-filters-form {
    display: flex;
    gap: var(--spacing-4);
    align-items: flex-end;
    flex-wrap: wrap;
}

.admin-filter-group {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-2);
    flex: 1;
    min-width: 200px;
}

.admin-filter-label {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-gray-700);
}

.admin-filter-input,
.admin-filter-select {
    padding: var(--spacing-2) var(--spacing-3);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-size: var(--font-size-base);
    color: var(--color-gray-900);
}

.admin-filter-input:focus,
.admin-filter-select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.admin-filter-actions {
    display: flex;
    gap: var(--spacing-2);
}

.table-cell-primary {
    font-weight: var(--font-weight-semibold);
    color: var(--color-gray-900);
}

.logo-preview-thumb {
    width: 100px;
    height: 50px;
    object-fit: contain;
    border-radius: var(--radius-md);
    border: 1px solid var(--color-gray-200);
    background: #f9fafb;
    padding: 4px;
}

.link-preview {
    color: var(--color-primary);
    text-decoration: none;
    font-size: var(--font-size-sm);
}

.link-preview:hover {
    text-decoration: underline;
}

.table-actions {
    display: flex;
    gap: var(--spacing-2);
}

.btn-danger {
    background-color: #dc2626 !important;
    color: white !important;
    border: 1px solid #dc2626 !important;
}

.btn-danger:hover {
    background-color: #991b1b !important;
    border-color: #991b1b !important;
    color: white !important;
}

.admin-card-footer {
    padding: var(--spacing-4);
    border-top: 1px solid var(--color-gray-200);
}

.admin-card-footer-text {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    margin: 0;
}

@media (max-width: 768px) {
    .admin-page-header {
        flex-direction: column;
    }
    
    .admin-filters-form {
        flex-direction: column;
    }
    
    .admin-filter-group {
        width: 100%;
    }
}
</style>

<?php include_admin_footer(); ?>
