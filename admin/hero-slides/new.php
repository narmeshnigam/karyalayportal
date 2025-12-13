<?php
/**
 * Admin Create Hero Slide Page
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';
require_once __DIR__ . '/../../includes/template_helpers.php';

use Karyalay\Models\HeroSlide;
use Karyalay\Services\InputSanitizationService;

startSecureSession();
require_admin();
require_permission('hero_slides.manage');

$heroSlideModel = new HeroSlide();
$sanitizationService = new InputSanitizationService();

$errors = [];
$form_data = [
    'title' => '',
    'highlight_line1' => '',
    'highlight_line2' => '',
    'description' => '',
    'subtitle' => '',
    'image_url' => '',
    'link_url' => '',
    'link_text' => '',
    'know_more_url' => '',
    'display_order' => 0,
    'status' => 'DRAFT',
    'highlight_line1_color' => '#FFFFFF',
    'highlight_line2_color' => '#FFFFFF',
    'description_color' => '#FFFFFF',
    'primary_btn_bg_color' => '#3B82F6',
    'primary_btn_text_color' => '#FFFFFF',
    'secondary_btn_bg_color' => 'transparent',
    'secondary_btn_text_color' => '#FFFFFF',
    'secondary_btn_border_color' => '#FFFFFF'
];

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
        
        // Color fields
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
            $result = $heroSlideModel->create($form_data);
            if ($result) {
                $_SESSION['admin_success'] = 'Hero slide created successfully!';
                header('Location: ' . get_app_base_url() . '/admin/hero-slides.php');
                exit;
            } else {
                $errors[] = 'Failed to create hero slide.';
            }
        }
    }
}

$csrf_token = getCsrfToken();
include_admin_header('Create Hero Slide');
?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <nav class="admin-breadcrumb">
            <a href="<?php echo get_app_base_url(); ?>/admin/hero-slides.php">Hero Slides</a>
            <span class="breadcrumb-separator">/</span>
            <span>Create Slide</span>
        </nav>
        <h1 class="admin-page-title">Create New Hero Slide</h1>
        <p class="admin-page-description">Add a new slide to the home page hero slider</p>
    </div>
    <div class="admin-page-header-actions">
        <a href="<?php echo get_app_base_url(); ?>/admin/hero-slides.php" class="btn btn-secondary">← Back to Slides</a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>Error:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Live Preview Section -->
<div class="admin-card preview-card">
    <h2 class="form-section-title">Live Preview</h2>
    <div class="slide-preview-container" id="slidePreview">
        <div class="slide-preview-bg" id="previewBg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="slide-preview-overlay"></div>
            <div class="slide-preview-content">
                <h1 class="preview-line1" id="previewLine1">Highlight Line 1</h1>
                <h1 class="preview-line2" id="previewLine2">Highlight Line 2</h1>
                <p class="preview-description" id="previewDesc">Your description text will appear here...</p>
                <div class="preview-buttons">
                    <span class="preview-btn-primary" id="previewPrimaryBtn">Get Started</span>
                    <span class="preview-btn-secondary" id="previewSecondaryBtn">Know More</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <form method="POST" action="<?php echo get_app_base_url(); ?>/admin/hero-slides/new.php" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-section">
            <h2 class="form-section-title">Highlight Text (Two-Liner)</h2>
            <p class="form-section-description">These are the main highlight texts displayed on the left side of the hero section.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="highlight_line1" class="form-label">Highlight Line 1</label>
                    <input type="text" id="highlight_line1" name="highlight_line1" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['highlight_line1']); ?>"
                           maxlength="30" placeholder="e.g., Transform Your">
                    <p class="form-help">Max 30 characters. <span class="char-count" data-for="highlight_line1">0/30</span></p>
                </div>
                
                <div class="form-group">
                    <label for="highlight_line1_color" class="form-label">Line 1 Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="highlight_line1_color" name="highlight_line1_color" 
                               value="<?php echo htmlspecialchars($form_data['highlight_line1_color']); ?>" class="form-color-input">
                        <input type="text" class="form-input color-hex-input" 
                               value="<?php echo htmlspecialchars($form_data['highlight_line1_color']); ?>" 
                               data-color-for="highlight_line1_color" maxlength="7">
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="highlight_line2" class="form-label">Highlight Line 2</label>
                    <input type="text" id="highlight_line2" name="highlight_line2" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['highlight_line2']); ?>"
                           maxlength="30" placeholder="e.g., Business Today">
                    <p class="form-help">Max 30 characters. <span class="char-count" data-for="highlight_line2">0/30</span></p>
                </div>
                
                <div class="form-group">
                    <label for="highlight_line2_color" class="form-label">Line 2 Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="highlight_line2_color" name="highlight_line2_color" 
                               value="<?php echo htmlspecialchars($form_data['highlight_line2_color']); ?>" class="form-color-input">
                        <input type="text" class="form-input color-hex-input" 
                               value="<?php echo htmlspecialchars($form_data['highlight_line2_color']); ?>" 
                               data-color-for="highlight_line2_color" maxlength="7">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">Description</h2>
            
            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label for="description" class="form-label">Small Description Text</label>
                    <textarea id="description" name="description" class="form-textarea" rows="3"
                              placeholder="Brief description or tagline..."><?php echo htmlspecialchars($form_data['description']); ?></textarea>
                    <p class="form-help">Short description displayed below the highlight text</p>
                </div>
                
                <div class="form-group" style="flex: 1;">
                    <label for="description_color" class="form-label">Description Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="description_color" name="description_color" 
                               value="<?php echo htmlspecialchars($form_data['description_color']); ?>" class="form-color-input">
                        <input type="text" class="form-input color-hex-input" 
                               value="<?php echo htmlspecialchars($form_data['description_color']); ?>" 
                               data-color-for="description_color" maxlength="7">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">Background Image</h2>
            
            <div class="form-group">
                <label for="image_url" class="form-label required">Background Image URL</label>
                <input type="url" id="image_url" name="image_url" class="form-input" 
                       value="<?php echo htmlspecialchars($form_data['image_url']); ?>"
                       required maxlength="500" placeholder="https://example.com/image.jpg">
                <p class="form-help">Full URL to the background image (recommended: 1920x1080px)</p>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">Button Settings</h2>
            <p class="form-section-description">Configure the "Get Started" and "Know More" buttons.</p>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Get Started Button (Primary)</h3>
                <p class="form-help">This button links to the sign-up page by default.</p>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="primary_btn_bg_color" class="form-label">Background Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="primary_btn_bg_color" name="primary_btn_bg_color" 
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_bg_color']); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" 
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_bg_color']); ?>" 
                                   data-color-for="primary_btn_bg_color" maxlength="7">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="primary_btn_text_color" class="form-label">Text Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="primary_btn_text_color" name="primary_btn_text_color" 
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_text_color']); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" 
                                   value="<?php echo htmlspecialchars($form_data['primary_btn_text_color']); ?>" 
                                   data-color-for="primary_btn_text_color" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Know More Button (Secondary)</h3>
                
                <div class="form-group">
                    <label for="know_more_url" class="form-label">Know More Link URL</label>
                    <input type="url" id="know_more_url" name="know_more_url" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['know_more_url']); ?>"
                           maxlength="500" placeholder="https://example.com/learn-more">
                    <p class="form-help">URL for the "Know More" button (can link to an image or page)</p>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="secondary_btn_text_color" class="form-label">Text Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="secondary_btn_text_color" name="secondary_btn_text_color" 
                                   value="<?php echo htmlspecialchars($form_data['secondary_btn_text_color']); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" 
                                   value="<?php echo htmlspecialchars($form_data['secondary_btn_text_color']); ?>" 
                                   data-color-for="secondary_btn_text_color" maxlength="7">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="secondary_btn_border_color" class="form-label">Border Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="secondary_btn_border_color" name="secondary_btn_border_color" 
                                   value="<?php echo htmlspecialchars($form_data['secondary_btn_border_color']); ?>" class="form-color-input">
                            <input type="text" class="form-input color-hex-input" 
                                   value="<?php echo htmlspecialchars($form_data['secondary_btn_border_color']); ?>" 
                                   data-color-for="secondary_btn_border_color" maxlength="7">
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
                           value="<?php echo htmlspecialchars($form_data['display_order']); ?>"
                           min="0" placeholder="0">
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
        
        <div class="form-section collapsible">
            <h2 class="form-section-title collapsible-toggle">Legacy Fields (Optional) <span class="toggle-icon">▼</span></h2>
            <div class="collapsible-content" style="display: none;">
                <p class="form-section-description">These fields are kept for backward compatibility.</p>
                
                <div class="form-group">
                    <label for="title" class="form-label">Title (Legacy)</label>
                    <input type="text" id="title" name="title" class="form-input" 
                           value="<?php echo htmlspecialchars($form_data['title']); ?>"
                           maxlength="255" placeholder="e.g., Transform Your Business">
                </div>
                
                <div class="form-group">
                    <label for="subtitle" class="form-label">Subtitle (Legacy)</label>
                    <textarea id="subtitle" name="subtitle" class="form-textarea" rows="2"
                              placeholder="Brief description..."><?php echo htmlspecialchars($form_data['subtitle']); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="link_url" class="form-label">Link URL (Legacy)</label>
                        <input type="url" id="link_url" name="link_url" class="form-input" 
                               value="<?php echo htmlspecialchars($form_data['link_url']); ?>"
                               maxlength="500" placeholder="https://example.com/page">
                    </div>
                    
                    <div class="form-group">
                        <label for="link_text" class="form-label">Link Button Text (Legacy)</label>
                        <input type="text" id="link_text" name="link_text" class="form-input" 
                               value="<?php echo htmlspecialchars($form_data['link_text']); ?>"
                               maxlength="100" placeholder="e.g., Learn More">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Slide</button>
            <a href="<?php echo get_app_base_url(); ?>/admin/hero-slides.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.admin-breadcrumb {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
    font-size: var(--font-size-sm);
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
    color: var(--color-gray-400);
}

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

.alert {
    padding: var(--spacing-4);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-6);
}

.alert-error {
    background-color: #fee2e2;
    border: 1px solid #fca5a5;
    color: #991b1b;
}

.alert ul {
    margin: var(--spacing-2) 0 0 var(--spacing-4);
    padding: 0;
}

.admin-form {
    padding: var(--spacing-6);
}

.form-section {
    margin-bottom: var(--spacing-8);
    padding-bottom: var(--spacing-6);
    border-bottom: 1px solid var(--color-gray-200);
}

.form-section:last-of-type {
    margin-bottom: 0;
    border-bottom: none;
}

.form-section-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--color-gray-900);
    margin: 0 0 var(--spacing-2) 0;
}

.form-section-description {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    margin: 0 0 var(--spacing-4) 0;
}

.form-subsection {
    background: var(--color-gray-50);
    border-radius: var(--radius-lg);
    padding: var(--spacing-4);
    margin-bottom: var(--spacing-4);
}

.form-subsection-title {
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-semibold);
    color: var(--color-gray-800);
    margin: 0 0 var(--spacing-3) 0;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-4);
}

.form-group {
    margin-bottom: var(--spacing-4);
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
    color: #dc2626;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: var(--spacing-2) var(--spacing-3);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-size: var(--font-size-sm);
    color: var(--color-gray-900);
    font-family: inherit;
    box-sizing: border-box;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-textarea {
    resize: vertical;
}

.form-help {
    font-size: var(--font-size-xs);
    color: var(--color-gray-500);
    margin: var(--spacing-1) 0 0 0;
}

.char-count {
    font-weight: var(--font-weight-semibold);
}

.color-input-wrapper {
    display: flex;
    gap: var(--spacing-2);
    align-items: center;
}

.form-color-input {
    width: 50px;
    height: 38px;
    padding: 2px;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    cursor: pointer;
}

.color-hex-input {
    flex: 1;
    font-family: monospace;
}

.form-actions {
    display: flex;
    gap: var(--spacing-3);
    padding-top: var(--spacing-6);
    border-top: 1px solid var(--color-gray-200);
}

.collapsible-toggle {
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.toggle-icon {
    font-size: var(--font-size-sm);
    transition: transform 0.2s;
}

.collapsible.open .toggle-icon {
    transform: rotate(180deg);
}

/* Preview Section Styles */
.preview-card {
    margin-bottom: var(--spacing-6);
    padding: var(--spacing-4);
}

.slide-preview-container {
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.slide-preview-bg {
    position: relative;
    height: 300px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.slide-preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.2) 50%, transparent 100%);
}

.slide-preview-content {
    position: relative;
    z-index: 1;
    padding: var(--spacing-6);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    max-width: 60%;
}

.preview-line1,
.preview-line2 {
    font-size: 1.75rem;
    font-weight: 600;
    line-height: 1.15;
    margin: 0;
    color: #FFFFFF;
    text-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
}

.preview-description {
    font-size: 0.95rem;
    line-height: 1.6;
    margin: var(--spacing-3) 0 var(--spacing-4) 0;
    color: #FFFFFF;
    opacity: 0.95;
}

.preview-buttons {
    display: flex;
    gap: var(--spacing-3);
}

.preview-btn-primary,
.preview-btn-secondary {
    padding: 8px 20px;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: 6px;
    display: inline-block;
}

.preview-btn-primary {
    background-color: #3B82F6;
    color: #FFFFFF;
    border: 2px solid #3B82F6;
}

.preview-btn-secondary {
    background: transparent;
    color: #FFFFFF;
    border: 2px solid #FFFFFF;
}

@media (max-width: 768px) {
    .admin-page-header {
        flex-direction: column;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .slide-preview-content {
        max-width: 100%;
    }
    
    .preview-line1,
    .preview-line2 {
        font-size: 1.25rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character count for highlight lines
    const charCountInputs = document.querySelectorAll('input[maxlength]');
    charCountInputs.forEach(input => {
        const countSpan = document.querySelector(`.char-count[data-for="${input.id}"]`);
        if (countSpan) {
            const updateCount = () => {
                countSpan.textContent = `${input.value.length}/${input.maxLength}`;
            };
            updateCount();
            input.addEventListener('input', updateCount);
        }
    });
    
    // Color input sync
    const colorInputs = document.querySelectorAll('.form-color-input');
    colorInputs.forEach(colorInput => {
        const hexInput = document.querySelector(`.color-hex-input[data-color-for="${colorInput.id}"]`);
        if (hexInput) {
            colorInput.addEventListener('input', () => {
                hexInput.value = colorInput.value.toUpperCase();
            });
            hexInput.addEventListener('input', () => {
                if (/^#[0-9A-Fa-f]{6}$/.test(hexInput.value)) {
                    colorInput.value = hexInput.value;
                }
            });
        }
    });
    
    // Collapsible sections
    const collapsibleToggles = document.querySelectorAll('.collapsible-toggle');
    collapsibleToggles.forEach(toggle => {
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
    
    // Text inputs
    document.getElementById('highlight_line1')?.addEventListener('input', (e) => {
        previewLine1.textContent = e.target.value || 'Highlight Line 1';
    });
    
    document.getElementById('highlight_line2')?.addEventListener('input', (e) => {
        previewLine2.textContent = e.target.value || 'Highlight Line 2';
    });
    
    document.getElementById('description')?.addEventListener('input', (e) => {
        previewDesc.textContent = e.target.value || 'Your description text will appear here...';
    });
    
    // Background image
    document.getElementById('image_url')?.addEventListener('input', (e) => {
        if (e.target.value) {
            previewBg.style.backgroundImage = `url('${e.target.value}')`;
            previewBg.style.background = '';
        } else {
            previewBg.style.backgroundImage = '';
            previewBg.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        }
    });
    
    // Color inputs - sync with preview
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
    
    // Add listeners to all color inputs
    document.querySelectorAll('.form-color-input').forEach(input => {
        input.addEventListener('input', updatePreviewColors);
    });
    
    document.querySelectorAll('.color-hex-input').forEach(input => {
        input.addEventListener('input', updatePreviewColors);
    });
});
</script>

<?php include_admin_footer(); ?>
