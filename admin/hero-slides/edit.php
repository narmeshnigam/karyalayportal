<?php
/**
 * Admin Edit Hero Slide Page
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';
require_once __DIR__ . '/../../includes/template_helpers.php';

use Karyalay\Models\HeroSlide;

startSecureSession();
require_admin();
require_permission('hero_slides.manage');

$heroSlideModel = new HeroSlide();

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header('Location: ' . get_app_base_url() . '/admin/hero-slides.php');
    exit;
}

$slide = $heroSlideModel->getById($id);
if (!$slide) {
    $_SESSION['admin_error'] = 'Slide not found.';
    header('Location: ' . get_app_base_url() . '/admin/hero-slides.php');
    exit;
}

$errors = [];
$form_data = array_merge([
    'highlight_line1' => '',
    'highlight_line2' => '',
    'description' => '',
    'know_more_url' => '',
    'mobile_image_url' => '',
    'highlight_line1_color' => '#FFFFFF',
    'highlight_line2_color' => '#FFFFFF',
    'description_color' => '#FFFFFF',
    'primary_btn_bg_color' => '#3B82F6',
    'primary_btn_text_color' => '#FFFFFF',
    'secondary_btn_bg_color' => 'transparent',
    'secondary_btn_text_color' => '#FFFFFF',
    'secondary_btn_border_color' => '#FFFFFF'
], $slide);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        $form_data['title'] = sanitizeString($_POST['title'] ?? '');
        $form_data['highlight_line1'] = sanitizeString($_POST['highlight_line1'] ?? '');
        $form_data['highlight_line2'] = sanitizeString($_POST['highlight_line2'] ?? '');
        $form_data['description'] = sanitizeString($_POST['description'] ?? '');
        $form_data['subtitle'] = sanitizeString($_POST['subtitle'] ?? '');
        $form_data['image_url'] = sanitizeString($_POST['image_url'] ?? '');
        $form_data['mobile_image_url'] = sanitizeString($_POST['mobile_image_url'] ?? '');
        $form_data['link_url'] = sanitizeString($_POST['link_url'] ?? '');
        $form_data['link_text'] = sanitizeString($_POST['link_text'] ?? '');
        $form_data['know_more_url'] = sanitizeString($_POST['know_more_url'] ?? '');
        $form_data['display_order'] = sanitizeInt($_POST['display_order'] ?? 0);
        $form_data['status'] = sanitizeString($_POST['status'] ?? 'DRAFT');
        $form_data['highlight_line1_color'] = sanitizeString($_POST['highlight_line1_color'] ?? '#FFFFFF');
        $form_data['highlight_line2_color'] = sanitizeString($_POST['highlight_line2_color'] ?? '#FFFFFF');
        $form_data['description_color'] = sanitizeString($_POST['description_color'] ?? '#FFFFFF');
        $form_data['primary_btn_bg_color'] = sanitizeString($_POST['primary_btn_bg_color'] ?? '#3B82F6');
        $form_data['primary_btn_text_color'] = sanitizeString($_POST['primary_btn_text_color'] ?? '#FFFFFF');
        $form_data['secondary_btn_bg_color'] = sanitizeString($_POST['secondary_btn_bg_color'] ?? 'transparent');
        $form_data['secondary_btn_text_color'] = sanitizeString($_POST['secondary_btn_text_color'] ?? '#FFFFFF');
        $form_data['secondary_btn_border_color'] = sanitizeString($_POST['secondary_btn_border_color'] ?? '#FFFFFF');

        if (empty($form_data['image_url'])) {
            $errors[] = 'Background image URL is required.';
        }
        if (!empty($form_data['highlight_line1']) && strlen($form_data['highlight_line1']) > 30) {
            $errors[] = 'Highlight Line 1 must be 30 characters or less.';
        }
        if (!empty($form_data['highlight_line2']) && strlen($form_data['highlight_line2']) > 30) {
            $errors[] = 'Highlight Line 2 must be 30 characters or less.';
        }
        if (!in_array($form_data['status'], ['DRAFT', 'PUBLISHED', 'ARCHIVED'])) {
            $errors[] = 'Invalid status value.';
        }

        if (empty($errors)) {
            $result = $heroSlideModel->update($id, $form_data);
            if ($result) {
                $_SESSION['admin_success'] = 'Hero slide updated successfully!';
                header('Location: ' . get_app_base_url() . '/admin/hero-slides.php');
                exit;
            } else {
                $errors[] = 'Failed to update hero slide.';
            }
        }
    }
}

$csrf_token = getCsrfToken();
include_admin_header('Edit Hero Slide');
?>

<!-- Link to main.css for real hero styles -->
<link rel="stylesheet" href="<?php echo css_url('main.css'); ?>">

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <nav class="admin-breadcrumb">
            <a href="<?php echo get_app_base_url(); ?>/admin/hero-slides.php">Hero Slides</a>
            <span class="breadcrumb-separator">/</span>
            <span>Edit Slide</span>
        </nav>
        <h1 class="admin-page-title">Edit Hero Slide</h1>
    </div>
    <div class="admin-page-header-actions">
        <a href="<?php echo get_app_base_url(); ?>/admin/hero-slides.php" class="btn btn-secondary">← Back</a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul><?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<!-- Real Live Preview Section using actual hero CSS -->
<div class="admin-card preview-card">
    <div class="preview-header">
        <h2 class="form-section-title">Live Preview</h2>
        <div class="preview-toggle">
            <button type="button" class="preview-toggle-btn active" data-view="desktop">Desktop</button>
            <button type="button" class="preview-toggle-btn" data-view="mobile">Mobile</button>
        </div>
    </div>
    <div class="preview-wrapper" id="previewWrapper">
        <section class="hero-slider preview-hero" aria-label="Hero Preview">
            <div class="hero-slider-container">
                <div class="hero-slide active" id="previewSlide"
                     style="background-image: url('<?php echo htmlspecialchars($form_data['image_url'] ?? ''); ?>')">
                    <div class="hero-slide-content">
                        <div class="container">
                            <div class="hero-content-wrapper">
                                <div class="hero-text-content">
                                    <div class="hero-highlight-text">
                                        <h1 class="hero-highlight-line" id="previewLine1" 
                                            style="color: <?php echo htmlspecialchars($form_data['highlight_line1_color']); ?>">
                                            <?php echo htmlspecialchars($form_data['highlight_line1'] ?: 'Highlight Line 1'); ?>
                                        </h1>
                                        <h1 class="hero-highlight-line" id="previewLine2"
                                            style="color: <?php echo htmlspecialchars($form_data['highlight_line2_color']); ?>">
                                            <?php echo htmlspecialchars($form_data['highlight_line2'] ?: 'Highlight Line 2'); ?>
                                        </h1>
                                    </div>
                                    <p class="hero-description" id="previewDesc"
                                       style="color: <?php echo htmlspecialchars($form_data['description_color']); ?>">
                                        <?php echo htmlspecialchars($form_data['description'] ?: 'Your description text will appear here...'); ?>
                                    </p>
                                    <div class="hero-actions">
                                        <a href="#" class="btn btn-hero-primary btn-lg" id="previewPrimaryBtn"
                                           style="background-color: <?php echo htmlspecialchars($form_data['primary_btn_bg_color']); ?>; 
                                                  color: <?php echo htmlspecialchars($form_data['primary_btn_text_color']); ?>; 
                                                  border-color: <?php echo htmlspecialchars($form_data['primary_btn_bg_color']); ?>;">
                                            Get Started
                                        </a>
                                        <a href="#" class="btn btn-hero-secondary btn-lg" id="previewSecondaryBtn"
                                           style="color: <?php echo htmlspecialchars($form_data['secondary_btn_text_color']); ?>; 
                                                  border-color: <?php echo htmlspecialchars($form_data['secondary_btn_border_color']); ?>;">
                                            Know More
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="admin-card">
    <form method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <!-- Hidden fields for legacy data preservation -->
        <input type="hidden" name="title" value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>">
        <input type="hidden" name="subtitle" value="<?php echo htmlspecialchars($form_data['subtitle'] ?? ''); ?>">
        <input type="hidden" name="link_url" value="<?php echo htmlspecialchars($form_data['link_url'] ?? ''); ?>">
        <input type="hidden" name="link_text" value="<?php echo htmlspecialchars($form_data['link_text'] ?? ''); ?>">
        
        <div class="form-section">
            <h2 class="form-section-title">Highlight Text</h2>
            <p class="form-section-desc">Two-line headline displayed on the left side of the hero section.</p>
            <div class="form-row">
                <div class="form-group">
                    <label for="highlight_line1" class="form-label">Line 1</label>
                    <input type="text" id="highlight_line1" name="highlight_line1" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['highlight_line1'] ?? ''); ?>" 
                           maxlength="30" placeholder="e.g., Transform Your">
                    <p class="form-help"><span class="char-count" data-for="highlight_line1"><?php echo strlen($form_data['highlight_line1'] ?? ''); ?></span>/30 characters</p>
                </div>
                <div class="form-group">
                    <label for="highlight_line1_color" class="form-label">Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="highlight_line1_color" name="highlight_line1_color" 
                               value="<?php echo htmlspecialchars($form_data['highlight_line1_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                        <input type="text" class="form-input color-hex-input" data-color-for="highlight_line1_color"
                               value="<?php echo htmlspecialchars($form_data['highlight_line1_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="highlight_line2" class="form-label">Line 2</label>
                    <input type="text" id="highlight_line2" name="highlight_line2" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['highlight_line2'] ?? ''); ?>" 
                           maxlength="30" placeholder="e.g., Business Today">
                    <p class="form-help"><span class="char-count" data-for="highlight_line2"><?php echo strlen($form_data['highlight_line2'] ?? ''); ?></span>/30 characters</p>
                </div>
                <div class="form-group">
                    <label for="highlight_line2_color" class="form-label">Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="highlight_line2_color" name="highlight_line2_color" 
                               value="<?php echo htmlspecialchars($form_data['highlight_line2_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                        <input type="text" class="form-input color-hex-input" data-color-for="highlight_line2_color"
                               value="<?php echo htmlspecialchars($form_data['highlight_line2_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">Description</h2>
            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label for="description" class="form-label">Description Text</label>
                    <textarea id="description" name="description" class="form-textarea" rows="3" 
                              placeholder="Brief description or tagline..."><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="description_color" class="form-label">Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="description_color" name="description_color" 
                               value="<?php echo htmlspecialchars($form_data['description_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                        <input type="text" class="form-input color-hex-input" data-color-for="description_color"
                               value="<?php echo htmlspecialchars($form_data['description_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Background Images</h2>
            <p class="form-section-desc">Upload separate images for desktop and mobile views for optimal display.</p>
            <div class="form-row">
                <div class="form-group">
                    <label for="image_url" class="form-label required">Desktop/Tablet Image</label>
                    <input type="url" id="image_url" name="image_url" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['image_url']); ?>" required maxlength="500"
                           placeholder="https://example.com/desktop-image.jpg">
                    <p class="form-help">Recommended: 1920×1080px (16:9 ratio)</p>
                </div>
                <div class="form-group">
                    <label for="mobile_image_url" class="form-label">Mobile Image <span class="optional-tag">Optional</span></label>
                    <input type="url" id="mobile_image_url" name="mobile_image_url" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['mobile_image_url'] ?? ''); ?>" maxlength="500"
                           placeholder="https://example.com/mobile-image.jpg">
                    <p class="form-help">Recommended: 768×1024px. Falls back to desktop if not set.</p>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">Button Settings</h2>
            <div class="form-subsection">
                <h3 class="form-subsection-title">Get Started Button (Primary)</h3>
                <p class="form-help" style="margin-bottom: 12px;">Links to the registration page by default.</p>
                <div class="form-row">
                    <div class="form-group">
                        <label for="primary_btn_bg_color" class="form-label">Background</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="primary_btn_bg_color" name="primary_btn_bg_color" 
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_bg_color'] ?? '#3B82F6'); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" data-color-for="primary_btn_bg_color"
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_bg_color'] ?? '#3B82F6'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="primary_btn_text_color" class="form-label">Text</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="primary_btn_text_color" name="primary_btn_text_color" 
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_text_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" data-color-for="primary_btn_text_color"
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_text_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-subsection">
                <h3 class="form-subsection-title">Know More Button (Secondary)</h3>
                <div class="form-group">
                    <label for="know_more_url" class="form-label">Link URL <span class="optional-tag">Optional</span></label>
                    <input type="url" id="know_more_url" name="know_more_url" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['know_more_url'] ?? ''); ?>" maxlength="500"
                           placeholder="https://example.com/learn-more">
                    <p class="form-help">Leave empty to link to Solutions page</p>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="secondary_btn_text_color" class="form-label">Text</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="secondary_btn_text_color" name="secondary_btn_text_color" 
                                   value="<?php echo htmlspecialchars($form_data['secondary_btn_text_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" data-color-for="secondary_btn_text_color"
                                   value="<?php echo htmlspecialchars($form_data['secondary_btn_text_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="secondary_btn_border_color" class="form-label">Border</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="secondary_btn_border_color" name="secondary_btn_border_color" 
                                   value="<?php echo htmlspecialchars($form_data['secondary_btn_border_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" data-color-for="secondary_btn_border_color"
                                   value="<?php echo htmlspecialchars($form_data['secondary_btn_border_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Display Settings</h2>
            <div class="form-row">
                <div class="form-group">
                    <label for="display_order" class="form-label">Display Order</label>
                    <input type="number" id="display_order" name="display_order" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['display_order']); ?>" min="0">
                    <p class="form-help">Lower numbers appear first</p>
                </div>
                <div class="form-group">
                    <label for="status" class="form-label required">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="DRAFT" <?php echo $form_data['status'] === 'DRAFT' ? 'selected' : ''; ?>>Draft</option>
                        <option value="PUBLISHED" <?php echo $form_data['status'] === 'PUBLISHED' ? 'selected' : ''; ?>>Published</option>
                        <option value="ARCHIVED" <?php echo $form_data['status'] === 'ARCHIVED' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                    <p class="form-help">Only published slides appear on the website</p>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Slide</button>
            <a href="<?php echo get_app_base_url(); ?>/admin/hero-slides.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
/* Page Layout */
.admin-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 14px; margin-bottom: 8px; }
.admin-breadcrumb a { color: var(--color-primary); text-decoration: none; }
.breadcrumb-separator { color: var(--color-gray-400); }
.admin-page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 16px; }
.admin-page-title { font-size: 24px; font-weight: bold; color: var(--color-gray-900); margin: 0; }
.alert { padding: 16px; border-radius: 8px; margin-bottom: 24px; }
.alert-error { background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
.alert ul { margin: 8px 0 0 16px; padding: 0; }

/* Preview Section */
.preview-card { margin-bottom: 24px; }
.preview-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid var(--color-gray-200); }
.preview-toggle { display: flex; gap: 4px; background: var(--color-gray-100); padding: 4px; border-radius: 8px; }
.preview-toggle-btn { padding: 8px 16px; border: none; background: transparent; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; color: var(--color-gray-600); transition: all 0.2s; }
.preview-toggle-btn.active { background: white; color: var(--color-gray-900); box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.preview-wrapper { padding: 20px; background: var(--color-gray-100); }
.preview-wrapper.mobile-view { display: flex; justify-content: center; }
.preview-wrapper.mobile-view .preview-hero { width: 375px; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }

/* Override hero styles for preview */
.preview-hero.hero-slider { position: relative; height: 400px; min-height: unset; max-height: unset; border-radius: 8px; overflow: hidden; }
.preview-hero .hero-slide { position: relative; opacity: 1; visibility: visible; height: 100%; }
.preview-wrapper.mobile-view .preview-hero.hero-slider { height: 500px; }

/* Form Styles */
.admin-form { padding: 24px; }
.form-section { margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid var(--color-gray-200); }
.form-section:last-of-type { margin-bottom: 0; border-bottom: none; }
.form-section-title { font-size: 18px; font-weight: 600; color: var(--color-gray-900); margin: 0 0 4px 0; }
.form-section-desc { font-size: 14px; color: var(--color-gray-500); margin: 0 0 16px 0; }
.form-subsection { background: var(--color-gray-50); border-radius: 8px; padding: 16px; margin-bottom: 16px; }
.form-subsection-title { font-size: 15px; font-weight: 600; color: var(--color-gray-800); margin: 0 0 8px 0; }
.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 14px; font-weight: 600; color: var(--color-gray-700); margin-bottom: 8px; }
.form-label.required::after { content: ' *'; color: #dc2626; }
.optional-tag { font-weight: normal; color: var(--color-gray-400); font-size: 12px; }
.form-input, .form-select, .form-textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--color-gray-300); border-radius: 6px; font-size: 14px; box-sizing: border-box; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.form-textarea { resize: vertical; font-family: inherit; }
.form-help { font-size: 12px; color: var(--color-gray-500); margin: 4px 0 0 0; }
.color-input-wrapper { display: flex; gap: 8px; align-items: center; }
.form-color-input { width: 50px; height: 40px; padding: 2px; border: 1px solid var(--color-gray-300); border-radius: 6px; cursor: pointer; }
.color-hex-input { flex: 1; font-family: monospace; text-transform: uppercase; }
.form-actions { display: flex; gap: 12px; padding-top: 24px; border-top: 1px solid var(--color-gray-200); }

@media (max-width: 768px) {
    .admin-page-header { flex-direction: column; }
    .form-row { grid-template-columns: 1fr; }
    .preview-header { flex-direction: column; gap: 12px; align-items: flex-start; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewSlide = document.getElementById('previewSlide');
    const previewLine1 = document.getElementById('previewLine1');
    const previewLine2 = document.getElementById('previewLine2');
    const previewDesc = document.getElementById('previewDesc');
    const previewPrimaryBtn = document.getElementById('previewPrimaryBtn');
    const previewSecondaryBtn = document.getElementById('previewSecondaryBtn');
    const previewWrapper = document.getElementById('previewWrapper');
    
    // Preview toggle (Desktop/Mobile)
    document.querySelectorAll('.preview-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.preview-toggle-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const view = this.dataset.view;
            if (view === 'mobile') {
                previewWrapper.classList.add('mobile-view');
                const mobileImg = document.getElementById('mobile_image_url').value;
                const desktopImg = document.getElementById('image_url').value;
                previewSlide.style.backgroundImage = `url('${mobileImg || desktopImg}')`;
            } else {
                previewWrapper.classList.remove('mobile-view');
                previewSlide.style.backgroundImage = `url('${document.getElementById('image_url').value}')`;
            }
        });
    });
    
    // Text inputs
    document.getElementById('highlight_line1')?.addEventListener('input', function() {
        previewLine1.textContent = this.value || 'Highlight Line 1';
        document.querySelector('.char-count[data-for="highlight_line1"]').textContent = this.value.length;
    });
    
    document.getElementById('highlight_line2')?.addEventListener('input', function() {
        previewLine2.textContent = this.value || 'Highlight Line 2';
        document.querySelector('.char-count[data-for="highlight_line2"]').textContent = this.value.length;
    });
    
    document.getElementById('description')?.addEventListener('input', function() {
        previewDesc.textContent = this.value || 'Your description text will appear here...';
    });
    
    // Image inputs
    document.getElementById('image_url')?.addEventListener('input', function() {
        if (!previewWrapper.classList.contains('mobile-view')) {
            previewSlide.style.backgroundImage = this.value ? `url('${this.value}')` : '';
        }
    });
    
    document.getElementById('mobile_image_url')?.addEventListener('input', function() {
        if (previewWrapper.classList.contains('mobile-view')) {
            const img = this.value || document.getElementById('image_url').value;
            previewSlide.style.backgroundImage = `url('${img}')`;
        }
    });
    
    // Color inputs
    function syncColorInputs(colorInput, hexInput) {
        colorInput.addEventListener('input', () => { hexInput.value = colorInput.value.toUpperCase(); updateColors(); });
        hexInput.addEventListener('input', () => { if (/^#[0-9A-Fa-f]{6}$/.test(hexInput.value)) { colorInput.value = hexInput.value; updateColors(); } });
    }
    
    document.querySelectorAll('.form-color-input').forEach(colorInput => {
        const hexInput = document.querySelector(`.color-hex-input[data-color-for="${colorInput.id}"]`);
        if (hexInput) syncColorInputs(colorInput, hexInput);
    });
    
    function updateColors() {
        previewLine1.style.color = document.getElementById('highlight_line1_color')?.value || '#FFFFFF';
        previewLine2.style.color = document.getElementById('highlight_line2_color')?.value || '#FFFFFF';
        previewDesc.style.color = document.getElementById('description_color')?.value || '#FFFFFF';
        const primaryBg = document.getElementById('primary_btn_bg_color')?.value || '#3B82F6';
        previewPrimaryBtn.style.backgroundColor = primaryBg;
        previewPrimaryBtn.style.borderColor = primaryBg;
        previewPrimaryBtn.style.color = document.getElementById('primary_btn_text_color')?.value || '#FFFFFF';
        previewSecondaryBtn.style.color = document.getElementById('secondary_btn_text_color')?.value || '#FFFFFF';
        previewSecondaryBtn.style.borderColor = document.getElementById('secondary_btn_border_color')?.value || '#FFFFFF';
    }
    
    document.querySelectorAll('.form-color-input').forEach(input => input.addEventListener('input', updateColors));
});
</script>

<?php include_admin_footer(); ?>
