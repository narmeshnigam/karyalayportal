<?php
/**
 * Admin Branding Settings Page
 * Manage logo, favicon, and color scheme
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';
require_once __DIR__ . '/../../includes/template_helpers.php';

use Karyalay\Models\Setting;
use Karyalay\Middleware\CsrfMiddleware;
use Karyalay\Services\MediaUploadService;

// Start secure session
startSecureSession();

// Require admin authentication and settings.general permission (ADMIN only)
require_admin();
require_permission('settings.general');

// Initialize services
$settingModel = new Setting();
$csrfMiddleware = new CsrfMiddleware();
$mediaUploadService = new MediaUploadService();

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!$csrfMiddleware->validate()) {
        $error_message = 'Invalid security token. Please try again.';
    } else {
        try {
            // Get form data
            $primary_color = trim($_POST['primary_color'] ?? '#3b82f6');
            $secondary_color = trim($_POST['secondary_color'] ?? '#10b981');
            
            // Validate color format (hex color)
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $primary_color)) {
                throw new Exception('Invalid primary color format. Use hex format (e.g., #3b82f6)');
            }
            
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $secondary_color)) {
                throw new Exception('Invalid secondary color format. Use hex format (e.g., #10b981)');
            }
            
            // Handle logo for light backgrounds (dark logo) upload
            if (isset($_FILES['logo_light_bg']) && $_FILES['logo_light_bg']['error'] === UPLOAD_ERR_OK) {
                $logo_result = $mediaUploadService->uploadFile(
                    $_FILES['logo_light_bg'],
                    $_SESSION['user_id']
                );
                
                if ($logo_result['success']) {
                    $settingModel->set('logo_light_bg', $logo_result['data']['url']);
                } else {
                    throw new Exception('Logo (light bg) upload failed: ' . $logo_result['error']);
                }
            }
            
            // Handle logo for dark backgrounds (light logo) upload
            if (isset($_FILES['logo_dark_bg']) && $_FILES['logo_dark_bg']['error'] === UPLOAD_ERR_OK) {
                $logo_result = $mediaUploadService->uploadFile(
                    $_FILES['logo_dark_bg'],
                    $_SESSION['user_id']
                );
                
                if ($logo_result['success']) {
                    $settingModel->set('logo_dark_bg', $logo_result['data']['url']);
                } else {
                    throw new Exception('Logo (dark bg) upload failed: ' . $logo_result['error']);
                }
            }
            
            // Handle square logo upload (for hub section)
            if (isset($_FILES['logo_square']) && $_FILES['logo_square']['error'] === UPLOAD_ERR_OK) {
                $logo_result = $mediaUploadService->uploadFile(
                    $_FILES['logo_square'],
                    $_SESSION['user_id']
                );
                
                if ($logo_result['success']) {
                    $settingModel->set('logo_square', $logo_result['data']['url']);
                } else {
                    throw new Exception('Square logo upload failed: ' . $logo_result['error']);
                }
            }
            
            // Handle favicon upload
            if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
                $favicon_result = $mediaUploadService->uploadFile(
                    $_FILES['favicon'],
                    $_SESSION['user_id']
                );
                
                if ($favicon_result['success']) {
                    $settingModel->set('favicon_url', $favicon_result['data']['url']);
                } else {
                    throw new Exception('Favicon upload failed: ' . $favicon_result['error']);
                }
            }
            
            // Save brand name
            $brand_name = trim($_POST['brand_name'] ?? 'SellerPortal');
            if (empty($brand_name)) {
                $brand_name = 'SellerPortal';
            }
            $settingModel->set('brand_name', $brand_name);
            
            // Save color settings
            $settingModel->set('primary_color', $primary_color);
            $settingModel->set('secondary_color', $secondary_color);
            
            // Save social links
            $social_facebook = trim($_POST['social_facebook'] ?? '');
            $social_twitter = trim($_POST['social_twitter'] ?? '');
            $social_linkedin = trim($_POST['social_linkedin'] ?? '');
            $social_instagram = trim($_POST['social_instagram'] ?? '');
            $social_youtube = trim($_POST['social_youtube'] ?? '');
            
            $settingModel->set('social_facebook', $social_facebook);
            $settingModel->set('social_twitter', $social_twitter);
            $settingModel->set('social_linkedin', $social_linkedin);
            $settingModel->set('social_instagram', $social_instagram);
            $settingModel->set('social_youtube', $social_youtube);
            
            $success_message = 'Branding settings saved successfully!';
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
}

// Fetch current settings
$settings = $settingModel->getMultiple([
    'brand_name',
    'logo_light_bg',
    'logo_dark_bg',
    'logo_square',
    'favicon_url',
    'primary_color',
    'secondary_color',
    'social_facebook',
    'social_twitter',
    'social_linkedin',
    'social_instagram',
    'social_youtube'
]);

// Set defaults if not found
$brand_name = $settings['brand_name'] ?? 'SellerPortal';
$logo_light_bg_raw = $settings['logo_light_bg'] ?? '';
$logo_dark_bg_raw = $settings['logo_dark_bg'] ?? '';
$logo_square_raw = $settings['logo_square'] ?? '';
$favicon_url_raw = $settings['favicon_url'] ?? '';
$primary_color = $settings['primary_color'] ?? '#3b82f6';
$secondary_color = $settings['secondary_color'] ?? '#10b981';
$social_facebook = $settings['social_facebook'] ?? '';
$social_twitter = $settings['social_twitter'] ?? '';
$social_linkedin = $settings['social_linkedin'] ?? '';
$social_instagram = $settings['social_instagram'] ?? '';
$social_youtube = $settings['social_youtube'] ?? '';

// Build full URLs for preview display
$preview_base_url = get_app_base_url();
$logo_light_bg = $logo_light_bg_raw ? $preview_base_url . $logo_light_bg_raw : '';
$logo_dark_bg = $logo_dark_bg_raw ? $preview_base_url . $logo_dark_bg_raw : '';
$logo_square = $logo_square_raw ? $preview_base_url . $logo_square_raw : '';
$favicon_url = $favicon_url_raw ? $preview_base_url . $favicon_url_raw : '';

// Generate CSRF token
$csrf_token = getCsrfToken();

// Include admin header
include_admin_header('Branding Settings');
?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <h1 class="admin-page-title">Branding Settings</h1>
        <p class="admin-page-description">Customize your site's visual identity</p>
    </div>
</div>

<?php $base_url = get_app_base_url(); ?>
<!-- Settings Navigation -->
<div class="settings-nav">
    <a href="<?php echo $base_url; ?>/admin/settings/general.php" class="settings-nav-item">General</a>
    <a href="<?php echo $base_url; ?>/admin/settings/branding.php" class="settings-nav-item active">Branding</a>
    <a href="<?php echo $base_url; ?>/admin/settings/seo.php" class="settings-nav-item">SEO</a>
    <a href="<?php echo $base_url; ?>/admin/settings/legal-identity.php" class="settings-nav-item">Legal Identity</a>
</div>

<!-- Success/Error Messages -->
<?php if ($success_message): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($success_message); ?>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<!-- Branding Settings Form -->
<div class="admin-card">
    <form method="POST" action="<?php echo $base_url; ?>/admin/settings/branding.php" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-section">
            <h3 class="form-section-title">Brand Identity</h3>
            
            <div class="form-group">
                <label for="brand_name" class="form-label">
                    Brand Name
                </label>
                <input 
                    type="text" 
                    id="brand_name" 
                    name="brand_name" 
                    class="form-input" 
                    value="<?php echo htmlspecialchars($brand_name); ?>"
                    maxlength="100"
                    placeholder="SellerPortal"
                >
                <p class="form-help">The brand name displayed throughout the application (e.g., in headers, emails). Default: SellerPortal</p>
            </div>
        </div>
        
        <div class="form-section">
            <h3 class="form-section-title">Logo & Favicon</h3>
            
            <div class="form-group">
                <label for="logo_light_bg" class="form-label">
                    Logo for Light Backgrounds
                </label>
                <p class="form-help" style="margin-top: 0; margin-bottom: var(--spacing-3);">Used on the public website header. Should be a dark-colored logo that's visible on light backgrounds.</p>
                
                <?php if ($logo_light_bg): ?>
                    <div class="image-preview">
                        <img src="<?php echo htmlspecialchars($logo_light_bg); ?>" alt="Current logo for light backgrounds" class="preview-image">
                        <p class="preview-label">Current Logo (Light BG)</p>
                    </div>
                <?php endif; ?>
                
                <input 
                    type="file" 
                    id="logo_light_bg" 
                    name="logo_light_bg" 
                    class="form-input-file" 
                    accept="image/jpeg,image/png,image/svg+xml"
                >
                <p class="form-help">Upload a logo (JPG, PNG, or SVG, max 5MB). Recommended size: 200x60px</p>
            </div>
            
            <div class="form-group">
                <label for="logo_dark_bg" class="form-label">
                    Logo for Dark Backgrounds
                </label>
                <p class="form-help" style="margin-top: 0; margin-bottom: var(--spacing-3);">Used on the public footer, admin panel, and customer portal. Should be a light-colored logo that's visible on dark backgrounds.</p>
                
                <?php if ($logo_dark_bg): ?>
                    <div class="image-preview" style="background-color: var(--color-gray-800); border-color: var(--color-gray-700);">
                        <img src="<?php echo htmlspecialchars($logo_dark_bg); ?>" alt="Current logo for dark backgrounds" class="preview-image">
                        <p class="preview-label" style="color: var(--color-gray-300);">Current Logo (Dark BG)</p>
                    </div>
                <?php endif; ?>
                
                <input 
                    type="file" 
                    id="logo_dark_bg" 
                    name="logo_dark_bg" 
                    class="form-input-file" 
                    accept="image/jpeg,image/png,image/svg+xml"
                >
                <p class="form-help">Upload a logo (JPG, PNG, or SVG, max 5MB). Recommended size: 200x60px</p>
            </div>
            
            <div class="form-group">
                <label for="logo_square" class="form-label">
                    Square Logo (Hub Section)
                </label>
                <p class="form-help" style="margin-top: 0; margin-bottom: var(--spacing-3);">Used in the Business Hub section on the homepage. Should be a square logo that works well inside a circular container.</p>
                
                <?php if ($logo_square): ?>
                    <div class="image-preview" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); border-color: var(--color-primary);">
                        <img src="<?php echo htmlspecialchars($logo_square); ?>" alt="Current square logo" class="preview-image" style="max-width: 80px; max-height: 80px;">
                        <p class="preview-label" style="color: white;">Current Square Logo</p>
                    </div>
                <?php endif; ?>
                
                <input 
                    type="file" 
                    id="logo_square" 
                    name="logo_square" 
                    class="form-input-file" 
                    accept="image/jpeg,image/png,image/svg+xml"
                >
                <p class="form-help">Upload a square logo (JPG, PNG, or SVG, max 5MB). Recommended size: 200x200px</p>
            </div>
            
            <div class="form-group">
                <label for="favicon" class="form-label">
                    Favicon
                </label>
                
                <?php if ($favicon_url): ?>
                    <div class="image-preview">
                        <img src="<?php echo htmlspecialchars($favicon_url); ?>" alt="Current favicon" class="preview-favicon">
                        <p class="preview-label">Current Favicon</p>
                    </div>
                <?php endif; ?>
                
                <input 
                    type="file" 
                    id="favicon" 
                    name="favicon" 
                    class="form-input-file" 
                    accept="image/x-icon,image/png,image/svg+xml"
                >
                <p class="form-help">Upload a new favicon (ICO, PNG, or SVG, max 1MB). Recommended size: 32x32px or 64x64px</p>
            </div>
        </div>
        
        <div class="form-section">
            <h3 class="form-section-title">Color Scheme</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="primary_color" class="form-label">
                        Primary Color
                    </label>
                    <div class="color-input-group">
                        <input 
                            type="color" 
                            id="primary_color" 
                            name="primary_color" 
                            class="form-input-color" 
                            value="<?php echo htmlspecialchars($primary_color); ?>"
                        >
                        <input 
                            type="text" 
                            class="form-input form-input-hex" 
                            value="<?php echo htmlspecialchars($primary_color); ?>"
                            pattern="^#[0-9A-Fa-f]{6}$"
                            maxlength="7"
                            id="primary_color_hex"
                        >
                    </div>
                    <p class="form-help">Main brand color used for buttons, links, and accents</p>
                </div>
                
                <div class="form-group">
                    <label for="secondary_color" class="form-label">
                        Secondary Color
                    </label>
                    <div class="color-input-group">
                        <input 
                            type="color" 
                            id="secondary_color" 
                            name="secondary_color" 
                            class="form-input-color" 
                            value="<?php echo htmlspecialchars($secondary_color); ?>"
                        >
                        <input 
                            type="text" 
                            class="form-input form-input-hex" 
                            value="<?php echo htmlspecialchars($secondary_color); ?>"
                            pattern="^#[0-9A-Fa-f]{6}$"
                            maxlength="7"
                            id="secondary_color_hex"
                        >
                    </div>
                    <p class="form-help">Secondary brand color for complementary elements</p>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3 class="form-section-title">Social Media Links</h3>
            <p class="form-section-description">Add your social media profile URLs. These will be displayed in the website footer. Leave empty to hide a link.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="social_facebook" class="form-label">
                        <svg class="social-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </label>
                    <input 
                        type="url" 
                        id="social_facebook" 
                        name="social_facebook" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($social_facebook); ?>"
                        placeholder="https://facebook.com/yourpage"
                    >
                </div>
                
                <div class="form-group">
                    <label for="social_twitter" class="form-label">
                        <svg class="social-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                        X (Twitter)
                    </label>
                    <input 
                        type="url" 
                        id="social_twitter" 
                        name="social_twitter" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($social_twitter); ?>"
                        placeholder="https://x.com/yourhandle"
                    >
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="social_linkedin" class="form-label">
                        <svg class="social-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                        LinkedIn
                    </label>
                    <input 
                        type="url" 
                        id="social_linkedin" 
                        name="social_linkedin" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($social_linkedin); ?>"
                        placeholder="https://linkedin.com/company/yourcompany"
                    >
                </div>
                
                <div class="form-group">
                    <label for="social_instagram" class="form-label">
                        <svg class="social-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                        </svg>
                        Instagram
                    </label>
                    <input 
                        type="url" 
                        id="social_instagram" 
                        name="social_instagram" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($social_instagram); ?>"
                        placeholder="https://instagram.com/yourhandle"
                    >
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="social_youtube" class="form-label">
                        <svg class="social-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                        YouTube
                    </label>
                    <input 
                        type="url" 
                        id="social_youtube" 
                        name="social_youtube" 
                        class="form-input" 
                        value="<?php echo htmlspecialchars($social_youtube); ?>"
                        placeholder="https://youtube.com/@yourchannel"
                    >
                </div>
                
                <div class="form-group">
                    <!-- Empty placeholder for grid alignment -->
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                Save Settings
            </button>
            <a href="<?php echo get_app_base_url(); ?>/admin/dashboard.php" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

<style>
.settings-nav {
    display: flex;
    gap: var(--spacing-2);
    margin-bottom: var(--spacing-6);
    border-bottom: 2px solid var(--color-gray-200);
}

.settings-nav-item {
    padding: var(--spacing-3) var(--spacing-4);
    color: var(--color-gray-600);
    text-decoration: none;
    font-weight: var(--font-weight-semibold);
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all var(--transition-fast);
}

.settings-nav-item:hover {
    color: var(--color-gray-900);
}

.settings-nav-item.active {
    color: var(--color-primary);
    border-bottom-color: var(--color-primary);
}

.admin-form {
    padding: var(--spacing-6);
}

.form-section {
    margin-bottom: var(--spacing-8);
}

.form-section:last-of-type {
    margin-bottom: var(--spacing-6);
}

.form-section-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--color-gray-900);
    margin: 0 0 var(--spacing-2) 0;
    padding-bottom: var(--spacing-3);
    border-bottom: 1px solid var(--color-gray-200);
}

.form-section-description {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    margin: 0 0 var(--spacing-4) 0;
}

.social-icon {
    vertical-align: middle;
    margin-right: var(--spacing-2);
    color: var(--color-gray-500);
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-5);
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

.form-input,
.form-input-file {
    width: 100%;
    padding: var(--spacing-3);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-size: var(--font-size-base);
    color: var(--color-gray-900);
    transition: border-color var(--transition-fast);
}

.form-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input-file {
    padding: var(--spacing-2);
}

.form-help {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    margin: var(--spacing-2) 0 0 0;
}

.image-preview {
    display: flex;
    align-items: center;
    gap: var(--spacing-3);
    padding: var(--spacing-4);
    background-color: var(--color-gray-50);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-3);
}

.preview-image {
    max-width: 200px;
    max-height: 60px;
    object-fit: contain;
}

.preview-favicon {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

.preview-label {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    margin: 0;
}

.color-input-group {
    display: flex;
    gap: var(--spacing-3);
    align-items: center;
}

.form-input-color {
    width: 60px;
    height: 40px;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    cursor: pointer;
}

.form-input-hex {
    flex: 1;
    max-width: 150px;
}

.form-actions {
    display: flex;
    gap: var(--spacing-3);
    padding-top: var(--spacing-4);
    border-top: 1px solid var(--color-gray-200);
}

.alert {
    padding: var(--spacing-4);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-6);
    font-size: var(--font-size-base);
}

.alert-success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

@media (max-width: 768px) {
    .settings-nav {
        overflow-x: auto;
    }
    
    .admin-form {
        padding: var(--spacing-4);
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
    }
}
</style>

<script>
// Sync color picker with hex input
document.addEventListener('DOMContentLoaded', function() {
    const primaryColor = document.getElementById('primary_color');
    const primaryColorHex = document.getElementById('primary_color_hex');
    const secondaryColor = document.getElementById('secondary_color');
    const secondaryColorHex = document.getElementById('secondary_color_hex');
    
    if (primaryColor && primaryColorHex) {
        primaryColor.addEventListener('input', function() {
            primaryColorHex.value = this.value;
        });
        
        primaryColorHex.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                primaryColor.value = this.value;
            }
        });
    }
    
    if (secondaryColor && secondaryColorHex) {
        secondaryColor.addEventListener('input', function() {
            secondaryColorHex.value = this.value;
        });
        
        secondaryColorHex.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                secondaryColor.value = this.value;
            }
        });
    }
});
</script>

<?php include_admin_footer(); ?>
