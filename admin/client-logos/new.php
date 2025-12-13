<?php
/**
 * Admin - Add New Client Logo
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';

use Karyalay\Models\ClientLogo;

startSecureSession();
require_admin();
require_permission('client_logos.manage');

// Check if table exists
try {
    $clientLogoModel = new ClientLogo();
    $tableExists = true;
} catch (Exception $e) {
    $tableExists = false;
    $tableError = $e->getMessage();
}

$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = trim($_POST['client_name'] ?? '');
    $logo_url = trim($_POST['logo_url'] ?? '');
    $website_url = trim($_POST['website_url'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    $status = $_POST['status'] ?? 'DRAFT';
    
    // Validation
    if (empty($client_name)) {
        $errors[] = 'Client name is required.';
    }
    
    if (empty($logo_url)) {
        $errors[] = 'Logo URL is required.';
    } elseif (!filter_var($logo_url, FILTER_VALIDATE_URL)) {
        $errors[] = 'Please enter a valid logo URL.';
    }
    
    if (!empty($website_url) && !filter_var($website_url, FILTER_VALIDATE_URL)) {
        $errors[] = 'Please enter a valid website URL.';
    }
    
    if (!in_array($status, ['DRAFT', 'PUBLISHED', 'ARCHIVED'])) {
        $errors[] = 'Invalid status selected.';
    }
    
    if (empty($errors)) {
        $id = $clientLogoModel->create([
            'client_name' => $client_name,
            'logo_url' => $logo_url,
            'website_url' => $website_url ?: null,
            'display_order' => $display_order,
            'status' => $status
        ]);
        
        if ($id) {
            $_SESSION['flash_message'] = 'Client logo created successfully.';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . get_app_base_url() . '/admin/client-logos.php');
            exit;
        } else {
            $errors[] = 'Failed to create client logo. Please try again.';
        }
    }
}

include_admin_header('Add Client Logo');
?>

<?php if (!$tableExists): ?>
<div class="admin-card">
    <div class="admin-card-body">
        <div class="setup-message">
            <h2>Setup Required</h2>
            <p>The client logos feature needs to be set up. Please run the database migration:</p>
            <pre><code>php database/run_migration_052.php</code></pre>
            <p><a href="<?php echo get_app_base_url(); ?>/admin/client-logos.php">← Back to Client Logos</a></p>
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
        <nav class="admin-breadcrumb">
            <a href="<?php echo get_app_base_url(); ?>/admin/client-logos.php">Client Logos</a>
            <span class="breadcrumb-separator">›</span>
            <span>Add New</span>
        </nav>
        <h1 class="admin-page-title">Add Client Logo</h1>
        <p class="admin-page-description">Add a new client logo to the hero slider marquee</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" class="admin-form">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Logo Details</h2>
        </div>
        <div class="admin-card-body">
            <div class="form-group">
                <label for="client_name" class="form-label required">Client Name</label>
                <input type="text" id="client_name" name="client_name" class="form-input" 
                       value="<?php echo htmlspecialchars($_POST['client_name'] ?? ''); ?>" required>
                <p class="form-help">The name of the client (for alt text and admin reference)</p>
            </div>
            
            <div class="form-group">
                <label for="logo_url" class="form-label required">Logo URL</label>
                <input type="url" id="logo_url" name="logo_url" class="form-input" 
                       value="<?php echo htmlspecialchars($_POST['logo_url'] ?? ''); ?>" required
                       placeholder="https://example.com/logo.png">
                <p class="form-help">URL to the client's logo image (recommended: transparent PNG, max height 60px)</p>
            </div>
            
            <div class="form-group">
                <label for="website_url" class="form-label">Website URL (Optional)</label>
                <input type="url" id="website_url" name="website_url" class="form-input" 
                       value="<?php echo htmlspecialchars($_POST['website_url'] ?? ''); ?>"
                       placeholder="https://example.com">
                <p class="form-help">Link to the client's website (logo will be clickable if provided)</p>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="display_order" class="form-label">Display Order</label>
                    <input type="number" id="display_order" name="display_order" class="form-input" 
                           value="<?php echo htmlspecialchars($_POST['display_order'] ?? '0'); ?>" min="0">
                    <p class="form-help">Lower numbers appear first</p>
                </div>
                
                <div class="form-group">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="DRAFT" <?php echo ($_POST['status'] ?? '') === 'DRAFT' ? 'selected' : ''; ?>>Draft</option>
                        <option value="PUBLISHED" <?php echo ($_POST['status'] ?? '') === 'PUBLISHED' ? 'selected' : ''; ?>>Published</option>
                        <option value="ARCHIVED" <?php echo ($_POST['status'] ?? '') === 'ARCHIVED' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                    <p class="form-help">Only published logos appear on the website</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Logo Preview -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Preview</h2>
        </div>
        <div class="admin-card-body">
            <div class="logo-preview-container">
                <img id="logo-preview" src="" alt="Logo preview" style="display: none; max-height: 60px; max-width: 200px;">
                <p id="preview-placeholder" class="text-muted">Enter a logo URL to see preview</p>
            </div>
        </div>
    </div>
    
    <div class="form-actions">
        <a href="<?php echo get_app_base_url(); ?>/admin/client-logos.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Logo</button>
    </div>
</form>

<script>
document.getElementById('logo_url').addEventListener('input', function() {
    const url = this.value;
    const preview = document.getElementById('logo-preview');
    const placeholder = document.getElementById('preview-placeholder');
    
    if (url && url.match(/^https?:\/\/.+/)) {
        preview.src = url;
        preview.style.display = 'block';
        placeholder.style.display = 'none';
        
        preview.onerror = function() {
            preview.style.display = 'none';
            placeholder.style.display = 'block';
            placeholder.textContent = 'Unable to load image';
        };
    } else {
        preview.style.display = 'none';
        placeholder.style.display = 'block';
        placeholder.textContent = 'Enter a logo URL to see preview';
    }
});
</script>

<style>
.admin-breadcrumb {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-2);
}

.admin-breadcrumb a {
    color: var(--color-primary);
    text-decoration: none;
}

.admin-breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb-separator {
    margin: 0 var(--spacing-2);
}

.admin-form {
    max-width: 800px;
}

.admin-card {
    background: white;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-6);
}

.admin-card-header {
    padding: var(--spacing-4) var(--spacing-6);
    border-bottom: 1px solid var(--color-gray-200);
}

.admin-card-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--color-gray-900);
    margin: 0;
}

.admin-card-body {
    padding: var(--spacing-6);
}

.form-group {
    margin-bottom: var(--spacing-5);
}

.form-label {
    display: block;
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-gray-700);
    margin-bottom: var(--spacing-2);
}

.form-label.required::after {
    content: ' *';
    color: var(--color-error);
}

.form-input,
.form-select {
    width: 100%;
    padding: var(--spacing-3);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-size: var(--font-size-base);
    color: var(--color-gray-900);
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-help {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    margin-top: var(--spacing-1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-4);
}

.form-actions {
    display: flex;
    gap: var(--spacing-3);
    justify-content: flex-end;
}

.logo-preview-container {
    padding: var(--spacing-4);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
    text-align: center;
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.error-list {
    margin: 0;
    padding-left: var(--spacing-4);
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include_admin_footer(); ?>
