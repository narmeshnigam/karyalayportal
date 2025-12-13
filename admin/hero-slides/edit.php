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

<!-- Live Preview Section -->
<div class="admin-card preview-card">
    <h2 class="form-section-title">Live Preview</h2>
    <div class="slide-preview-container" id="slidePreview">
        <div class="slide-preview-bg" id="previewBg" style="<?php echo !empty($form_data['image_url']) ? "background-image: url('" . htmlspecialchars($form_data['image_url']) . "')" : "background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)"; ?>">
            <div class="slide-preview-overlay"></div>
            <div class="slide-preview-content">
                <h1 class="preview-line1" id="previewLine1" style="color: <?php echo htmlspecialchars($form_data['highlight_line1_color'] ?? '#FFFFFF'); ?>"><?php echo htmlspecialchars($form_data['highlight_line1'] ?? 'Highlight Line 1'); ?></h1>
                <h1 class="preview-line2" id="previewLine2" style="color: <?php echo htmlspecialchars($form_data['highlight_line2_color'] ?? '#FFFFFF'); ?>"><?php echo htmlspecialchars($form_data['highlight_line2'] ?? 'Highlight Line 2'); ?></h1>
                <p class="preview-description" id="previewDesc" style="color: <?php echo htmlspecialchars($form_data['description_color'] ?? '#FFFFFF'); ?>"><?php echo htmlspecialchars($form_data['description'] ?? 'Your description text will appear here...'); ?></p>
                <div class="preview-buttons">
                    <span class="preview-btn-primary" id="previewPrimaryBtn" style="background-color: <?php echo htmlspecialchars($form_data['primary_btn_bg_color'] ?? '#3B82F6'); ?>; border-color: <?php echo htmlspecialchars($form_data['primary_btn_bg_color'] ?? '#3B82F6'); ?>; color: <?php echo htmlspecialchars($form_data['primary_btn_text_color'] ?? '#FFFFFF'); ?>">Get Started</span>
                    <span class="preview-btn-secondary" id="previewSecondaryBtn" style="color: <?php echo htmlspecialchars($form_data['secondary_btn_text_color'] ?? '#FFFFFF'); ?>; border-color: <?php echo htmlspecialchars($form_data['secondary_btn_border_color'] ?? '#FFFFFF'); ?>">Know More</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <form method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-section">
            <h2 class="form-section-title">Highlight Text (Two-Liner)</h2>
            <div class="form-row">
                <div class="form-group">
                    <label for="highlight_line1" class="form-label">Highlight Line 1</label>
                    <input type="text" id="highlight_line1" name="highlight_line1" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['highlight_line1'] ?? ''); ?>" maxlength="30">
                    <p class="form-help">Max 30 characters</p>
                </div>
                <div class="form-group">
                    <label for="highlight_line1_color" class="form-label">Line 1 Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="highlight_line1_color" name="highlight_line1_color" 
                               value="<?php echo htmlspecialchars($form_data['highlight_line1_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                        <input type="text" class="form-input color-hex-input" 
                               value="<?php echo htmlspecialchars($form_data['highlight_line1_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="highlight_line2" class="form-label">Highlight Line 2</label>
                    <input type="text" id="highlight_line2" name="highlight_line2" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['highlight_line2'] ?? ''); ?>" maxlength="30">
                    <p class="form-help">Max 30 characters</p>
                </div>
                <div class="form-group">
                    <label for="highlight_line2_color" class="form-label">Line 2 Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="highlight_line2_color" name="highlight_line2_color" 
                               value="<?php echo htmlspecialchars($form_data['highlight_line2_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                        <input type="text" class="form-input color-hex-input" 
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
                    <textarea id="description" name="description" class="form-textarea" rows="3"><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="description_color" class="form-label">Description Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="description_color" name="description_color" 
                               value="<?php echo htmlspecialchars($form_data['description_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                        <input type="text" class="form-input color-hex-input" 
                               value="<?php echo htmlspecialchars($form_data['description_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">Background Image</h2>
            <div class="form-group">
                <label for="image_url" class="form-label required">Background Image URL</label>
                <input type="url" id="image_url" name="image_url" class="form-input" 
                       value="<?php echo htmlspecialchars($form_data['image_url']); ?>" required maxlength="500">
                <?php if (!empty($form_data['image_url'])): ?>
                    <div class="image-preview"><img src="<?php echo htmlspecialchars($form_data['image_url']); ?>" alt="Preview"></div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">Button Settings</h2>
            <div class="form-subsection">
                <h3 class="form-subsection-title">Get Started Button (Primary)</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="primary_btn_bg_color" class="form-label">Background Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="primary_btn_bg_color" name="primary_btn_bg_color" 
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_bg_color'] ?? '#3B82F6'); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" 
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_bg_color'] ?? '#3B82F6'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="primary_btn_text_color" class="form-label">Text Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="primary_btn_text_color" name="primary_btn_text_color" 
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_text_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" 
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_text_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-subsection">
                <h3 class="form-subsection-title">Know More Button (Secondary)</h3>
                <div class="form-group">
                    <label for="know_more_url" class="form-label">Know More Link URL</label>
                    <input type="url" id="know_more_url" name="know_more_url" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['know_more_url'] ?? ''); ?>" maxlength="500">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="secondary_btn_text_color" class="form-label">Text Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="secondary_btn_text_color" name="secondary_btn_text_color" 
                                   value="<?php echo htmlspecialchars($form_data['secondary_btn_text_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" 
                                   value="<?php echo htmlspecialchars($form_data['secondary_btn_text_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="secondary_btn_border_color" class="form-label">Border Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="secondary_btn_border_color" name="secondary_btn_border_color" 
                                   value="<?php echo htmlspecialchars($form_data['secondary_btn_border_color'] ?? '#FFFFFF'); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" 
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
                </div>
                <div class="form-group">
                    <label for="status" class="form-label required">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="DRAFT" <?php echo $form_data['status'] === 'DRAFT' ? 'selected' : ''; ?>>Draft</option>
                        <option value="PUBLISHED" <?php echo $form_data['status'] === 'PUBLISHED' ? 'selected' : ''; ?>>Published</option>
                        <option value="ARCHIVED" <?php echo $form_data['status'] === 'ARCHIVED' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-section collapsible">
            <h2 class="form-section-title collapsible-toggle">Legacy Fields <span class="toggle-icon">▼</span></h2>
            <div class="collapsible-content" style="display: none;">
                <div class="form-group">
                    <label for="title" class="form-label">Title (Legacy)</label>
                    <input type="text" id="title" name="title" class="form-input" value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" maxlength="255">
                </div>
                <div class="form-group">
                    <label for="subtitle" class="form-label">Subtitle (Legacy)</label>
                    <textarea id="subtitle" name="subtitle" class="form-textarea" rows="2"><?php echo htmlspecialchars($form_data['subtitle'] ?? ''); ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="link_url" class="form-label">Link URL (Legacy)</label>
                        <input type="url" id="link_url" name="link_url" class="form-input" value="<?php echo htmlspecialchars($form_data['link_url'] ?? ''); ?>" maxlength="500">
                    </div>
                    <div class="form-group">
                        <label for="link_text" class="form-label">Link Text (Legacy)</label>
                        <input type="text" id="link_text" name="link_text" class="form-input" value="<?php echo htmlspecialchars($form_data['link_text'] ?? ''); ?>" maxlength="100">
                    </div>
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
.admin-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 14px; margin-bottom: 8px; }
.admin-breadcrumb a { color: var(--color-primary); text-decoration: none; }
.breadcrumb-separator { color: var(--color-gray-400); }
.admin-page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 16px; }
.admin-page-title { font-size: 24px; font-weight: bold; color: var(--color-gray-900); margin: 0; }
.alert { padding: 16px; border-radius: 8px; margin-bottom: 24px; }
.alert-error { background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
.alert ul { margin: 8px 0 0 16px; padding: 0; }
.admin-form { padding: 24px; }
.form-section { margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid var(--color-gray-200); }
.form-section:last-of-type { margin-bottom: 0; border-bottom: none; }
.form-section-title { font-size: 18px; font-weight: 600; color: var(--color-gray-900); margin: 0 0 16px 0; }
.form-subsection { background: var(--color-gray-50); border-radius: 8px; padding: 16px; margin-bottom: 16px; }
.form-subsection-title { font-size: 16px; font-weight: 600; color: var(--color-gray-800); margin: 0 0 12px 0; }
.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 14px; font-weight: 600; color: var(--color-gray-700); margin-bottom: 8px; }
.form-label.required::after { content: ' *'; color: #dc2626; }
.form-input, .form-select, .form-textarea { width: 100%; padding: 8px 12px; border: 1px solid var(--color-gray-300); border-radius: 6px; font-size: 14px; box-sizing: border-box; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.form-textarea { resize: vertical; font-family: inherit; }
.form-help { font-size: 12px; color: var(--color-gray-500); margin: 4px 0 0 0; }
.color-input-wrapper { display: flex; gap: 8px; align-items: center; }
.form-color-input { width: 50px; height: 38px; padding: 2px; border: 1px solid var(--color-gray-300); border-radius: 6px; cursor: pointer; }
.color-hex-input { flex: 1; font-family: monospace; }
.form-actions { display: flex; gap: 12px; padding-top: 24px; border-top: 1px solid var(--color-gray-200); }
.image-preview { margin-top: 12px; }
.image-preview img { max-width: 300px; height: auto; border-radius: 8px; border: 1px solid var(--color-gray-200); }
.collapsible-toggle { cursor: pointer; display: flex; align-items: center; justify-content: space-between; }
.toggle-icon { font-size: 14px; transition: transform 0.2s; }
.collapsible.open .toggle-icon { transform: rotate(180deg); }
/* Preview Section Styles */
.preview-card { margin-bottom: 24px; padding: 16px; }
.slide-preview-container { border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); }
.slide-preview-bg { position: relative; height: 300px; background-size: cover; background-position: center; background-repeat: no-repeat; }
.slide-preview-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.2) 50%, transparent 100%); }
.slide-preview-content { position: relative; z-index: 1; padding: 24px; height: 100%; display: flex; flex-direction: column; justify-content: center; max-width: 60%; }
.preview-line1, .preview-line2 { font-size: 1.75rem; font-weight: 600; line-height: 1.15; margin: 0; color: #FFFFFF; text-shadow: 0 2px 6px rgba(0, 0, 0, 0.25); }
.preview-description { font-size: 0.95rem; line-height: 1.6; margin: 12px 0 16px 0; color: #FFFFFF; opacity: 0.95; }
.preview-buttons { display: flex; gap: 12px; }
.preview-btn-primary, .preview-btn-secondary { padding: 8px 20px; font-size: 0.85rem; font-weight: 600; border-radius: 6px; display: inline-block; }
.preview-btn-primary { background-color: #3B82F6; color: #FFFFFF; border: 2px solid #3B82F6; }
.preview-btn-secondary { background: transparent; color: #FFFFFF; border: 2px solid #FFFFFF; }
@media (max-width: 768px) { .admin-page-header { flex-direction: column; } .form-row { grid-template-columns: 1fr; } .image-preview img { max-width: 100%; } .slide-preview-content { max-width: 100%; } .preview-line1, .preview-line2 { font-size: 1.25rem; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.collapsible-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const section = toggle.closest('.collapsible');
            const content = section.querySelector('.collapsible-content');
            section.classList.toggle('open');
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
        });
    });
    
    // Live Preview Updates
    const previewBg = document.getElementById('previewBg');
    const previewLine1 = document.getElementById('previewLine1');
    const previewLine2 = document.getElementById('previewLine2');
    const previewDesc = document.getElementById('previewDesc');
    const previewPrimaryBtn = document.getElementById('previewPrimaryBtn');
    const previewSecondaryBtn = document.getElementById('previewSecondaryBtn');
    
    document.getElementById('highlight_line1')?.addEventListener('input', (e) => {
        previewLine1.textContent = e.target.value || 'Highlight Line 1';
    });
    
    document.getElementById('highlight_line2')?.addEventListener('input', (e) => {
        previewLine2.textContent = e.target.value || 'Highlight Line 2';
    });
    
    document.getElementById('description')?.addEventListener('input', (e) => {
        previewDesc.textContent = e.target.value || 'Your description text will appear here...';
    });
    
    document.getElementById('image_url')?.addEventListener('input', (e) => {
        if (e.target.value) {
            previewBg.style.backgroundImage = `url('${e.target.value}')`;
            previewBg.style.background = '';
        } else {
            previewBg.style.backgroundImage = '';
            previewBg.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        }
    });
    
    function updatePreviewColors() {
        const line1Color = document.getElementById('highlight_line1_color')?.value || '#FFFFFF';
        const line2Color = document.getElementById('highlight_line2_color')?.value || '#FFFFFF';
        const descColor = document.getElementById('description_color')?.value || '#FFFFFF';
        const primaryBg = document.getElementById('primary_btn_bg_color')?.value || '#3B82F6';
        const primaryText = document.getElementById('primary_btn_text_color')?.value || '#FFFFFF';
        const secondaryText = document.getElementById('secondary_btn_text_color')?.value || '#FFFFFF';
        const secondaryBorder = document.getElementById('secondary_btn_border_color')?.value || '#FFFFFF';
        
        previewLine1.style.color = line1Color;
        previewLine2.style.color = line2Color;
        previewDesc.style.color = descColor;
        previewPrimaryBtn.style.backgroundColor = primaryBg;
        previewPrimaryBtn.style.borderColor = primaryBg;
        previewPrimaryBtn.style.color = primaryText;
        previewSecondaryBtn.style.color = secondaryText;
        previewSecondaryBtn.style.borderColor = secondaryBorder;
    }
    
    document.querySelectorAll('.form-color-input').forEach(input => {
        input.addEventListener('input', updatePreviewColors);
    });
});
</script>

<?php include_admin_footer(); ?>
