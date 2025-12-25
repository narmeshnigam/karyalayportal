<?php
/**
 * Analytics & Monitoring Settings
 * Admin page to configure tracking and analytics integrations
 */

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/admin_helpers.php';
require_once __DIR__ . '/../includes/template_helpers.php';

use Karyalay\Models\Setting;

// Start session and check admin authentication
startSecureSession();
require_admin();
require_permission('settings.general');

$settingModel = new Setting();
$success = null;
$error = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid security token. Please try again.');
        }
        
        // Get form data - Google Analytics GA4
        $ga4MeasurementId = trim($_POST['ga4_measurement_id'] ?? '');
        $ga4Enabled = isset($_POST['ga4_enabled']) ? '1' : '0';
        
        // Google Search Console
        $gscVerificationCode = trim($_POST['gsc_verification_code'] ?? '');
        $gscEnabled = isset($_POST['gsc_enabled']) ? '1' : '0';
        
        // Google Tag Manager
        $gtmContainerId = trim($_POST['gtm_container_id'] ?? '');
        $gtmEnabled = isset($_POST['gtm_enabled']) ? '1' : '0';
        
        // Meta Pixel
        $metaPixelId = trim($_POST['meta_pixel_id'] ?? '');
        $metaPixelEnabled = isset($_POST['meta_pixel_enabled']) ? '1' : '0';
        
        // Microsoft Clarity
        $clarityProjectId = trim($_POST['clarity_project_id'] ?? '');
        $clarityEnabled = isset($_POST['clarity_enabled']) ? '1' : '0';
        
        // Validate GA4 Measurement ID format if provided
        if (!empty($ga4MeasurementId) && !preg_match('/^G-[A-Z0-9]+$/', $ga4MeasurementId)) {
            throw new Exception('Invalid GA4 Measurement ID format. It should start with "G-" followed by alphanumeric characters.');
        }
        
        // Validate GTM Container ID format if provided
        if (!empty($gtmContainerId) && !preg_match('/^GTM-[A-Z0-9]+$/', $gtmContainerId)) {
            throw new Exception('Invalid GTM Container ID format. It should start with "GTM-" followed by alphanumeric characters.');
        }
        
        // Prepare settings array
        $settings = [
            'ga4_measurement_id' => $ga4MeasurementId,
            'ga4_enabled' => $ga4Enabled,
            'gsc_verification_code' => $gscVerificationCode,
            'gsc_enabled' => $gscEnabled,
            'gtm_container_id' => $gtmContainerId,
            'gtm_enabled' => $gtmEnabled,
            'meta_pixel_id' => $metaPixelId,
            'meta_pixel_enabled' => $metaPixelEnabled,
            'clarity_project_id' => $clarityProjectId,
            'clarity_enabled' => $clarityEnabled
        ];
        
        // Save settings
        if ($settingModel->setMultiple($settings)) {
            $success = 'Analytics & Monitoring settings saved successfully!';
            
            // Log the action
            $currentUser = getCurrentUser();
            if ($currentUser) {
                error_log('Analytics settings updated by admin: ' . $currentUser['email']);
            }
        } else {
            throw new Exception('Failed to save analytics settings. Please try again.');
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log('Analytics settings error: ' . $e->getMessage());
    }
}

// Generate new CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Get current settings
$currentSettings = $settingModel->getMultiple([
    'ga4_measurement_id',
    'ga4_enabled',
    'gsc_verification_code',
    'gsc_enabled',
    'gtm_container_id',
    'gtm_enabled',
    'meta_pixel_id',
    'meta_pixel_enabled',
    'clarity_project_id',
    'clarity_enabled'
]);

$ga4MeasurementId = $currentSettings['ga4_measurement_id'] ?? '';
$ga4Enabled = ($currentSettings['ga4_enabled'] ?? '0') === '1';
$gscVerificationCode = $currentSettings['gsc_verification_code'] ?? '';
$gscEnabled = ($currentSettings['gsc_enabled'] ?? '0') === '1';
$gtmContainerId = $currentSettings['gtm_container_id'] ?? '';
$gtmEnabled = ($currentSettings['gtm_enabled'] ?? '0') === '1';
$metaPixelId = $currentSettings['meta_pixel_id'] ?? '';
$metaPixelEnabled = ($currentSettings['meta_pixel_enabled'] ?? '0') === '1';
$clarityProjectId = $currentSettings['clarity_project_id'] ?? '';
$clarityEnabled = ($currentSettings['clarity_enabled'] ?? '0') === '1';

// Page title
$page_title = 'Analytics & Monitoring';

// Include admin header
include __DIR__ . '/../templates/admin-header.php';
?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <h1 class="admin-page-title">Analytics & Monitoring</h1>
        <p class="admin-page-description">Configure tracking and analytics integrations to understand user behavior</p>
    </div>
    <div class="admin-page-header-actions">
        <a href="<?php echo get_app_base_url(); ?>/admin/dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">
        <svg class="alert-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span><?php echo htmlspecialchars($success); ?></span>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <svg class="alert-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span><?php echo htmlspecialchars($error); ?></span>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Analytics Configuration</h2>
    </div>
    <div class="card-body">
        <p class="card-description">Configure your analytics and tracking services to monitor website performance and user behavior.</p>

        <form method="POST" action="" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <!-- Google Analytics GA4 -->
            <div class="form-section">
                <div class="form-section-header">
                    <h3 class="form-section-title">
                        <svg class="section-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Google Analytics GA4
                    </h3>
                    <label class="toggle-switch">
                        <input type="checkbox" name="ga4_enabled" <?php echo $ga4Enabled ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="form-group">
                    <label for="ga4_measurement_id" class="form-label">Measurement ID</label>
                    <input 
                        type="text" 
                        id="ga4_measurement_id" 
                        name="ga4_measurement_id" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($ga4MeasurementId); ?>"
                        placeholder="G-XXXXXXXXXX"
                    >
                    <span class="form-help">Find this in Google Analytics → Admin → Data Streams → Measurement ID</span>
                </div>
            </div>

            <!-- Google Search Console -->
            <div class="form-section">
                <div class="form-section-header">
                    <h3 class="form-section-title">
                        <svg class="section-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Google Search Console
                    </h3>
                    <label class="toggle-switch">
                        <input type="checkbox" name="gsc_enabled" <?php echo $gscEnabled ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="form-group">
                    <label for="gsc_verification_code" class="form-label">Verification Code</label>
                    <input 
                        type="text" 
                        id="gsc_verification_code" 
                        name="gsc_verification_code" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($gscVerificationCode); ?>"
                        placeholder="google-site-verification=XXXXXXXX"
                    >
                    <span class="form-help">Enter the verification code from Google Search Console</span>
                </div>
            </div>

            <!-- Google Tag Manager -->
            <div class="form-section">
                <div class="form-section-header">
                    <h3 class="form-section-title">
                        <svg class="section-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Google Tag Manager
                    </h3>
                    <label class="toggle-switch">
                        <input type="checkbox" name="gtm_enabled" <?php echo $gtmEnabled ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="form-group">
                    <label for="gtm_container_id" class="form-label">Container ID</label>
                    <input 
                        type="text" 
                        id="gtm_container_id" 
                        name="gtm_container_id" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($gtmContainerId); ?>"
                        placeholder="GTM-XXXXXXX"
                    >
                    <span class="form-help">Find this in GTM → Admin → Container Settings → Container ID</span>
                </div>
            </div>

            <!-- Meta Pixel -->
            <div class="form-section">
                <div class="form-section-header">
                    <h3 class="form-section-title">
                        <svg class="section-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                        Meta Pixel (Facebook)
                    </h3>
                    <label class="toggle-switch">
                        <input type="checkbox" name="meta_pixel_enabled" <?php echo $metaPixelEnabled ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="form-group">
                    <label for="meta_pixel_id" class="form-label">Pixel ID</label>
                    <input 
                        type="text" 
                        id="meta_pixel_id" 
                        name="meta_pixel_id" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($metaPixelId); ?>"
                        placeholder="XXXXXXXXXXXXXXXX"
                    >
                    <span class="form-help">Find this in Meta Events Manager → Data Sources → Pixel ID</span>
                </div>
            </div>

            <!-- Microsoft Clarity -->
            <div class="form-section">
                <div class="form-section-header">
                    <h3 class="form-section-title">
                        <svg class="section-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Microsoft Clarity
                    </h3>
                    <label class="toggle-switch">
                        <input type="checkbox" name="clarity_enabled" <?php echo $clarityEnabled ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="form-group">
                    <label for="clarity_project_id" class="form-label">Project ID</label>
                    <input 
                        type="text" 
                        id="clarity_project_id" 
                        name="clarity_project_id" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($clarityProjectId); ?>"
                        placeholder="XXXXXXXXXX"
                    >
                    <span class="form-help">Find this in Microsoft Clarity → Settings → Project ID</span>
                </div>
            </div>

            <!-- Help Section -->
            <div class="alert alert-info">
                <svg class="alert-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <strong>Note:</strong> If you use Google Tag Manager, you can manage GA4 and other tags through GTM instead of adding them separately here.
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg class="btn-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Page Header */
.admin-page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    gap: 1rem;
}

.admin-page-header-content {
    flex: 1;
}

.admin-page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-gray-900);
    margin: 0 0 0.5rem 0;
}

.admin-page-description {
    font-size: 0.875rem;
    color: var(--color-gray-600);
    margin: 0;
}

/* Card Styles */
.admin-card {
    background: white;
    border: 1px solid var(--color-gray-200);
    border-radius: 8px;
    overflow: hidden;
}

.card-header {
    padding: 1rem 1.5rem;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--color-gray-900);
    margin: 0;
}

.card-body {
    padding: 1.5rem;
}

.card-description {
    color: var(--color-gray-600);
    margin: 0 0 1.5rem 0;
    font-size: 0.875rem;
}

/* Form Sections */
.form-section {
    padding: 1.25rem 0;
    border-bottom: 1px solid var(--color-gray-200);
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.form-section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: var(--color-gray-900);
    margin: 0;
}

.section-icon {
    color: var(--color-primary);
}

/* Form Elements */
.form-group {
    margin-bottom: 1rem;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-gray-700);
    margin-bottom: 0.5rem;
}

.form-input {
    width: 100%;
    padding: 0.625rem 0.75rem;
    border: 1px solid var(--color-gray-300);
    border-radius: 6px;
    font-size: 0.875rem;
    color: var(--color-gray-900);
    transition: border-color 0.15s, box-shadow 0.15s;
}

.form-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input::placeholder {
    color: var(--color-gray-400);
}

.form-help {
    display: block;
    font-size: 0.75rem;
    color: var(--color-gray-500);
    margin-top: 0.375rem;
}

/* Toggle Switch */
.toggle-switch {
    position: relative;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}

.toggle-slider {
    position: relative;
    width: 44px;
    height: 24px;
    background-color: var(--color-gray-300);
    border-radius: 24px;
    transition: background-color 0.2s;
}

.toggle-slider::before {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background-color: white;
    border-radius: 50%;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.toggle-switch input:checked + .toggle-slider {
    background-color: var(--color-success, #10b981);
}

.toggle-switch input:checked + .toggle-slider::before {
    transform: translateX(20px);
}

/* Alerts */
.alert {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
}

.alert-icon {
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.alert-success {
    background-color: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
}

.alert-danger {
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.alert-info {
    background-color: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
    margin-top: 1.5rem;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--color-gray-200);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    border: 1px solid transparent;
}

.btn-primary {
    background-color: var(--color-primary);
    color: white;
}

.btn-primary:hover {
    background-color: var(--color-primary-dark, #2563eb);
}

.btn-secondary {
    background-color: white;
    color: var(--color-gray-700);
    border-color: var(--color-gray-300);
}

.btn-secondary:hover {
    background-color: var(--color-gray-50);
}

.btn-icon {
    width: 18px;
    height: 18px;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-page-header {
        flex-direction: column;
    }
    
    .form-section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>

<?php include __DIR__ . '/../templates/admin-footer.php'; ?>
