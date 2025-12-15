<?php
/**
 * Admin Create Solution Page
 * Updated for normalized table structure (solutions, solution_styling, solution_content)
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';
require_once __DIR__ . '/../../includes/template_helpers.php';

use Karyalay\Services\ContentService;
use Karyalay\Services\InputSanitizationService;

startSecureSession();
require_admin();
require_permission('solutions.manage');

$contentService = new ContentService();
$sanitizationService = new InputSanitizationService();

$errors = [];
$form_data = [
    // Core fields (solutions table)
    'name' => '',
    'slug' => '',
    'description' => '',
    'tagline' => '',
    'subtitle' => '',
    'icon_image' => '',
    'video_url' => '',
    'demo_video_url' => '',
    'color_theme' => '#667eea',
    'pricing_note' => '',
    'meta_title' => '',
    'meta_description' => '',
    'meta_keywords' => '',
    'display_order' => 0,
    'status' => 'DRAFT',
    'featured_on_homepage' => false,
    
    // Styling fields (solution_styling table)
    'hero_badge' => '',
    'hero_title_text' => '',
    'hero_title_color' => '#FFFFFF',
    'hero_subtitle_color' => '#FFFFFF',
    'hero_media_url' => '',
    'hero_media_type' => 'image',
    'hero_bg_color' => '#0a1628',
    'hero_bg_gradient_color' => '',
    'hero_bg_gradient_opacity' => 0.60,
    'hero_bg_pattern_opacity' => 0.03,
    'hero_cta_primary_text' => 'Get Started',
    'hero_cta_primary_link' => '',
    'hero_cta_secondary_text' => 'Watch Demo',
    'hero_cta_secondary_link' => '',
    'hero_primary_btn_bg_color' => 'rgba(255,255,255,0.15)',
    'hero_primary_btn_text_color' => '#FFFFFF',
    'hero_primary_btn_text_hover_color' => '#FFFFFF',
    'hero_primary_btn_border_color' => 'rgba(255,255,255,0.3)',
    'hero_secondary_btn_bg_color' => 'rgba(255,255,255,0.1)',
    'hero_secondary_btn_text_color' => '#FFFFFF',
    'hero_secondary_btn_text_hover_color' => '#FFFFFF',
    'hero_secondary_btn_border_color' => 'rgba(255,255,255,0.2)',
    'key_benefits_section_enabled' => false,
    'key_benefits_section_bg_color' => '#0a1628',
    'key_benefits_section_heading1' => '',
    'key_benefits_section_heading2' => '',
    'key_benefits_section_subheading' => '',
    'key_benefits_section_heading_color' => '#FFFFFF',
    'key_benefits_section_subheading_color' => 'rgba(255,255,255,0.7)',
    'key_benefits_section_card_bg_color' => 'rgba(255,255,255,0.08)',
    'key_benefits_section_card_border_color' => 'rgba(255,255,255,0.1)',
    'key_benefits_section_card_hover_bg_color' => '#2563eb',
    'key_benefits_section_card_text_color' => '#FFFFFF',
    'key_benefits_section_card_icon_color' => 'rgba(255,255,255,0.6)',
    'feature_showcase_section_enabled' => false,
    'feature_showcase_section_title' => 'One solution. All business sizes.',
    'feature_showcase_section_subtitle' => '',
    'feature_showcase_section_bg_color' => '#ffffff',
    'feature_showcase_section_title_color' => '#1a202c',
    'feature_showcase_section_subtitle_color' => '#718096',
    'feature_showcase_card_bg_color' => '#ffffff',
    'feature_showcase_card_border_color' => '#e2e8f0',
    'feature_showcase_card_badge_bg_color' => '#ebf8ff',
    'feature_showcase_card_badge_text_color' => '#2b6cb0',
    'feature_showcase_card_heading_color' => '#1a202c',
    'feature_showcase_card_text_color' => '#4a5568',
    'feature_showcase_card_icon_color' => '#38a169',
    'cta_banner_enabled' => true,
    'cta_banner_image_url' => '',
    'cta_banner_overlay_color' => 'rgba(0,0,0,0.5)',
    'cta_banner_overlay_intensity' => 0.50,
    'cta_banner_heading1' => 'Streamline across 30+ modules.',
    'cta_banner_heading2' => 'Transform your business today!',
    'cta_banner_heading_color' => '#FFFFFF',
    'cta_banner_button_text' => 'Explore ERP Solutions',
    'cta_banner_button_link' => '#contact-form',
    'cta_banner_button_bg_color' => '#FFFFFF',
    'cta_banner_button_text_color' => '#2563eb',
    
    // Industries Section
    'industries_section_enabled' => true,
    'industries_section_title' => 'Industries We Serve',
    'industries_section_subtitle' => 'Trusted by leading organizations across diverse sectors',
    'industries_section_bg_color' => '#f8fafc',
    'industries_section_title_color' => '#1a202c',
    'industries_section_subtitle_color' => '#718096',
    'industries_section_card_overlay_color' => 'rgba(0,0,0,0.4)',
    'industries_section_card_title_color' => '#FFFFFF',
    'industries_section_card_desc_color' => 'rgba(255,255,255,0.9)',
    'industries_section_card_btn_bg_color' => 'rgba(255,255,255,0.2)',
    'industries_section_card_btn_text_color' => '#FFFFFF',
    
    // Testimonials Section
    'testimonials_section_theme' => 'light',
    'testimonials_section_heading' => 'What Our Customers Say',
    'testimonials_section_subheading' => 'Trusted by leading businesses who have transformed their operations with our solutions',
    
    // FAQs Section
    'faqs_section_theme' => 'light',
    'faqs_section_heading' => 'Frequently Asked Questions',
    'faqs_section_subheading' => 'Everything you need to know about our solution. Can\'t find what you\'re looking for? Feel free to contact us.',
    
    // Content fields (solution_content table)
    'features' => [],
    'screenshots' => [],
    'faqs' => [],
    'key_benefits_cards' => [],
    'feature_showcase_cards' => [],
    'industries_cards' => []
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        // Core fields
        $form_data['name'] = sanitizeString($_POST['name'] ?? '');
        $form_data['slug'] = sanitizeString($_POST['slug'] ?? '');
        $form_data['description'] = sanitizeString($_POST['description'] ?? '');
        $form_data['tagline'] = sanitizeString($_POST['tagline'] ?? '');
        $form_data['subtitle'] = sanitizeString($_POST['subtitle'] ?? '');
        $form_data['icon_image'] = sanitizeString($_POST['icon_image'] ?? '');
        $form_data['video_url'] = sanitizeString($_POST['video_url'] ?? '');
        $form_data['demo_video_url'] = sanitizeString($_POST['demo_video_url'] ?? '');
        $form_data['color_theme'] = sanitizeString($_POST['color_theme'] ?? '#667eea');
        $form_data['pricing_note'] = sanitizeString($_POST['pricing_note'] ?? '');
        $form_data['meta_title'] = sanitizeString($_POST['meta_title'] ?? '');
        $form_data['meta_description'] = sanitizeString($_POST['meta_description'] ?? '');
        $form_data['meta_keywords'] = sanitizeString($_POST['meta_keywords'] ?? '');
        $form_data['display_order'] = sanitizeInt($_POST['display_order'] ?? 0);
        $form_data['status'] = sanitizeString($_POST['status'] ?? 'DRAFT');
        $form_data['featured_on_homepage'] = isset($_POST['featured_on_homepage']);
        
        // Styling fields
        $form_data['hero_badge'] = sanitizeString($_POST['hero_badge'] ?? '');
        $form_data['hero_title_text'] = substr(sanitizeString($_POST['hero_title_text'] ?? ''), 0, 24);
        $form_data['hero_title_color'] = sanitizeString($_POST['hero_title_color'] ?? '#FFFFFF');
        $form_data['hero_subtitle_color'] = sanitizeString($_POST['hero_subtitle_color'] ?? '#FFFFFF');
        $form_data['hero_media_url'] = sanitizeString($_POST['hero_media_url'] ?? '');
        $form_data['hero_media_type'] = sanitizeString($_POST['hero_media_type'] ?? 'image');
        $form_data['hero_bg_color'] = sanitizeString($_POST['hero_bg_color'] ?? '#0a1628');
        $form_data['hero_bg_gradient_color'] = sanitizeString($_POST['hero_bg_gradient_color'] ?? '');
        $form_data['hero_bg_gradient_opacity'] = floatval($_POST['hero_bg_gradient_opacity'] ?? 0.60);
        $form_data['hero_bg_pattern_opacity'] = floatval($_POST['hero_bg_pattern_opacity'] ?? 0.03);
        $form_data['hero_cta_primary_text'] = sanitizeString($_POST['hero_cta_primary_text'] ?? 'Get Started');
        $form_data['hero_cta_primary_link'] = sanitizeString($_POST['hero_cta_primary_link'] ?? '');
        $form_data['hero_cta_secondary_text'] = sanitizeString($_POST['hero_cta_secondary_text'] ?? 'Watch Demo');
        $form_data['hero_cta_secondary_link'] = sanitizeString($_POST['hero_cta_secondary_link'] ?? '');
        $form_data['hero_primary_btn_bg_color'] = sanitizeString($_POST['hero_primary_btn_bg_color'] ?? 'rgba(255,255,255,0.15)');
        $form_data['hero_primary_btn_text_color'] = sanitizeString($_POST['hero_primary_btn_text_color'] ?? '#FFFFFF');
        $form_data['hero_primary_btn_text_hover_color'] = sanitizeString($_POST['hero_primary_btn_text_hover_color'] ?? '#FFFFFF');
        $form_data['hero_primary_btn_border_color'] = sanitizeString($_POST['hero_primary_btn_border_color'] ?? 'rgba(255,255,255,0.3)');
        $form_data['hero_secondary_btn_bg_color'] = sanitizeString($_POST['hero_secondary_btn_bg_color'] ?? 'rgba(255,255,255,0.1)');
        $form_data['hero_secondary_btn_text_color'] = sanitizeString($_POST['hero_secondary_btn_text_color'] ?? '#FFFFFF');
        $form_data['hero_secondary_btn_text_hover_color'] = sanitizeString($_POST['hero_secondary_btn_text_hover_color'] ?? '#FFFFFF');
        $form_data['hero_secondary_btn_border_color'] = sanitizeString($_POST['hero_secondary_btn_border_color'] ?? 'rgba(255,255,255,0.2)');
        
        // Key Benefits Section
        $form_data['key_benefits_section_enabled'] = isset($_POST['key_benefits_section_enabled']);
        $form_data['key_benefits_section_bg_color'] = sanitizeString($_POST['key_benefits_section_bg_color'] ?? '#0a1628');
        $form_data['key_benefits_section_heading1'] = substr(sanitizeString($_POST['key_benefits_section_heading1'] ?? ''), 0, 24);
        $form_data['key_benefits_section_heading2'] = substr(sanitizeString($_POST['key_benefits_section_heading2'] ?? ''), 0, 24);
        $form_data['key_benefits_section_subheading'] = sanitizeString($_POST['key_benefits_section_subheading'] ?? '');
        $form_data['key_benefits_section_heading_color'] = sanitizeString($_POST['key_benefits_section_heading_color'] ?? '#FFFFFF');
        $form_data['key_benefits_section_subheading_color'] = sanitizeString($_POST['key_benefits_section_subheading_color'] ?? 'rgba(255,255,255,0.7)');
        $form_data['key_benefits_section_card_bg_color'] = sanitizeString($_POST['key_benefits_section_card_bg_color'] ?? 'rgba(255,255,255,0.08)');
        $form_data['key_benefits_section_card_border_color'] = sanitizeString($_POST['key_benefits_section_card_border_color'] ?? 'rgba(255,255,255,0.1)');
        $form_data['key_benefits_section_card_hover_bg_color'] = sanitizeString($_POST['key_benefits_section_card_hover_bg_color'] ?? '#2563eb');
        $form_data['key_benefits_section_card_text_color'] = sanitizeString($_POST['key_benefits_section_card_text_color'] ?? '#FFFFFF');
        $form_data['key_benefits_section_card_icon_color'] = sanitizeString($_POST['key_benefits_section_card_icon_color'] ?? 'rgba(255,255,255,0.6)');
        
        // Feature Showcase Section
        $form_data['feature_showcase_section_enabled'] = isset($_POST['feature_showcase_section_enabled']);
        $form_data['feature_showcase_section_title'] = sanitizeString($_POST['feature_showcase_section_title'] ?? '');
        $form_data['feature_showcase_section_subtitle'] = sanitizeString($_POST['feature_showcase_section_subtitle'] ?? '');
        $form_data['feature_showcase_section_bg_color'] = sanitizeString($_POST['feature_showcase_section_bg_color'] ?? '#ffffff');
        $form_data['feature_showcase_section_title_color'] = sanitizeString($_POST['feature_showcase_section_title_color'] ?? '#1a202c');
        $form_data['feature_showcase_section_subtitle_color'] = sanitizeString($_POST['feature_showcase_section_subtitle_color'] ?? '#718096');
        $form_data['feature_showcase_card_bg_color'] = sanitizeString($_POST['feature_showcase_card_bg_color'] ?? '#ffffff');
        $form_data['feature_showcase_card_border_color'] = sanitizeString($_POST['feature_showcase_card_border_color'] ?? '#e2e8f0');
        $form_data['feature_showcase_card_badge_bg_color'] = sanitizeString($_POST['feature_showcase_card_badge_bg_color'] ?? '#ebf8ff');
        $form_data['feature_showcase_card_badge_text_color'] = sanitizeString($_POST['feature_showcase_card_badge_text_color'] ?? '#2b6cb0');
        $form_data['feature_showcase_card_heading_color'] = sanitizeString($_POST['feature_showcase_card_heading_color'] ?? '#1a202c');
        $form_data['feature_showcase_card_text_color'] = sanitizeString($_POST['feature_showcase_card_text_color'] ?? '#4a5568');
        $form_data['feature_showcase_card_icon_color'] = sanitizeString($_POST['feature_showcase_card_icon_color'] ?? '#38a169');
        
        // CTA Banner Section
        $form_data['cta_banner_enabled'] = isset($_POST['cta_banner_enabled']);
        $form_data['cta_banner_image_url'] = sanitizeString($_POST['cta_banner_image_url'] ?? '');
        $form_data['cta_banner_overlay_color'] = sanitizeString($_POST['cta_banner_overlay_color'] ?? 'rgba(0,0,0,0.5)');
        $form_data['cta_banner_overlay_intensity'] = floatval($_POST['cta_banner_overlay_intensity'] ?? 0.50);
        $form_data['cta_banner_heading1'] = sanitizeString($_POST['cta_banner_heading1'] ?? '');
        $form_data['cta_banner_heading2'] = sanitizeString($_POST['cta_banner_heading2'] ?? '');
        $form_data['cta_banner_heading_color'] = sanitizeString($_POST['cta_banner_heading_color'] ?? '#FFFFFF');
        $form_data['cta_banner_button_text'] = sanitizeString($_POST['cta_banner_button_text'] ?? '');
        $form_data['cta_banner_button_link'] = sanitizeString($_POST['cta_banner_button_link'] ?? '#contact-form');
        $form_data['cta_banner_button_bg_color'] = sanitizeString($_POST['cta_banner_button_bg_color'] ?? '#FFFFFF');
        $form_data['cta_banner_button_text_color'] = sanitizeString($_POST['cta_banner_button_text_color'] ?? '#2563eb');
        
        // Validation
        if (empty($form_data['name'])) {
            $errors[] = 'Solution name is required.';
        }
        
        if (!in_array($form_data['status'], ['DRAFT', 'PUBLISHED', 'ARCHIVED'])) {
            $errors[] = 'Invalid status value.';
        }
        
        // Process JSON content fields
        if (!empty($_POST['features'])) {
            $features_raw = explode("\n", $_POST['features']);
            $form_data['features'] = array_values(array_filter(array_map('trim', $features_raw)));
        }
        
        if (!empty($_POST['screenshots'])) {
            $screenshots_raw = explode("\n", $_POST['screenshots']);
            $form_data['screenshots'] = array_values(array_filter(array_map('trim', $screenshots_raw)));
        }
        
        if (!empty($_POST['faqs'])) {
            $faqs_decoded = json_decode($_POST['faqs'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($faqs_decoded)) {
                $form_data['faqs'] = $faqs_decoded;
            } else {
                $errors[] = 'FAQs must be valid JSON format.';
            }
        }
        
        if (!empty($_POST['key_benefits_cards'])) {
            $decoded = json_decode($_POST['key_benefits_cards'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $form_data['key_benefits_cards'] = $decoded;
            } else {
                $errors[] = 'Key benefits cards must be valid JSON format.';
            }
        }
        
        if (!empty($_POST['feature_showcase_cards'])) {
            $decoded = json_decode($_POST['feature_showcase_cards'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $form_data['feature_showcase_cards'] = array_slice($decoded, 0, 6);
            } else {
                $errors[] = 'Feature showcase cards must be valid JSON format.';
            }
        }
        
        // Industries Section
        $form_data['industries_section_enabled'] = isset($_POST['industries_section_enabled']);
        $form_data['industries_section_title'] = sanitizeString($_POST['industries_section_title'] ?? 'Industries We Serve');
        $form_data['industries_section_subtitle'] = sanitizeString($_POST['industries_section_subtitle'] ?? '');
        $form_data['industries_section_bg_color'] = sanitizeString($_POST['industries_section_bg_color'] ?? '#f8fafc');
        $form_data['industries_section_title_color'] = sanitizeString($_POST['industries_section_title_color'] ?? '#1a202c');
        $form_data['industries_section_subtitle_color'] = sanitizeString($_POST['industries_section_subtitle_color'] ?? '#718096');
        $form_data['industries_section_card_overlay_color'] = sanitizeString($_POST['industries_section_card_overlay_color'] ?? 'rgba(0,0,0,0.4)');
        $form_data['industries_section_card_title_color'] = sanitizeString($_POST['industries_section_card_title_color'] ?? '#FFFFFF');
        $form_data['industries_section_card_desc_color'] = sanitizeString($_POST['industries_section_card_desc_color'] ?? 'rgba(255,255,255,0.9)');
        $form_data['industries_section_card_btn_bg_color'] = sanitizeString($_POST['industries_section_card_btn_bg_color'] ?? 'rgba(255,255,255,0.2)');
        $form_data['industries_section_card_btn_text_color'] = sanitizeString($_POST['industries_section_card_btn_text_color'] ?? '#FFFFFF');
        
        if (!empty($_POST['industries_cards'])) {
            $decoded = json_decode($_POST['industries_cards'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $form_data['industries_cards'] = array_slice($decoded, 0, 10);
            } else {
                $errors[] = 'Industries cards must be valid JSON format.';
            }
        }
        
        // Process Testimonials Section fields
        $form_data['testimonials_section_theme'] = in_array($_POST['testimonials_section_theme'] ?? 'light', ['light', 'dark']) 
            ? $_POST['testimonials_section_theme'] 
            : 'light';
        $form_data['testimonials_section_heading'] = substr(sanitizeString($_POST['testimonials_section_heading'] ?? ''), 0, 48);
        $form_data['testimonials_section_subheading'] = substr(sanitizeString($_POST['testimonials_section_subheading'] ?? ''), 0, 120);
        
        // Process FAQs Section fields
        $form_data['faqs_section_theme'] = in_array($_POST['faqs_section_theme'] ?? 'light', ['light', 'dark']) 
            ? $_POST['faqs_section_theme'] 
            : 'light';
        $form_data['faqs_section_heading'] = substr(sanitizeString($_POST['faqs_section_heading'] ?? ''), 0, 48);
        $form_data['faqs_section_subheading'] = substr(sanitizeString($_POST['faqs_section_subheading'] ?? ''), 0, 120);
        
        if (empty($errors)) {
            $result = $contentService->create('solution', $form_data);
            
            if ($result) {
                $_SESSION['admin_success'] = 'Solution created successfully!';
                header('Location: ' . get_app_base_url() . '/admin/solutions.php');
                exit;
            } else {
                $errors[] = 'Failed to create solution. Please check if the slug is unique.';
            }
        }
    }
}

$csrf_token = getCsrfToken();
include_admin_header('Create Solution');
?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <nav class="admin-breadcrumb">
            <a href="<?php echo get_app_base_url(); ?>/admin/solutions.php">Solutions</a>
            <span class="breadcrumb-separator">/</span>
            <span>Create Solution</span>
        </nav>
        <h1 class="admin-page-title">Create New Solution</h1>
        <p class="admin-page-description">Add a new solution to display on the public website</p>
    </div>
    <div class="admin-page-header-actions">
        <a href="<?php echo get_app_base_url(); ?>/admin/solutions.php" class="btn btn-secondary">← Back to Solutions</a>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <strong>Error:</strong>
    <ul><?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" action="<?php echo get_app_base_url(); ?>/admin/solutions/new.php" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <!-- Basic Information -->
        <div class="form-section">
            <h2 class="form-section-title">Basic Information</h2>
            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label required">Solution Name</label>
                    <input type="text" id="name" name="name" class="form-input" value="<?php echo htmlspecialchars($form_data['name']); ?>" required maxlength="255">
                </div>
                <div class="form-group">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" id="slug" name="slug" class="form-input" value="<?php echo htmlspecialchars($form_data['slug']); ?>" pattern="[a-z0-9\-]+" maxlength="255">
                    <p class="form-help">Auto-generated if left empty</p>
                </div>
            </div>
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="3"><?php echo htmlspecialchars($form_data['description']); ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="tagline" class="form-label">Tagline</label>
                    <input type="text" id="tagline" name="tagline" class="form-input" value="<?php echo htmlspecialchars($form_data['tagline']); ?>">
                </div>
                <div class="form-group">
                    <label for="icon_image" class="form-label">Icon Image URL</label>
                    <input type="text" id="icon_image" name="icon_image" class="form-input" value="<?php echo htmlspecialchars($form_data['icon_image']); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="display_order" class="form-label">Display Order</label>
                    <input type="number" id="display_order" name="display_order" class="form-input" value="<?php echo $form_data['display_order']; ?>" min="0">
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
            <div class="form-group">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="featured_on_homepage" class="form-checkbox" <?php echo $form_data['featured_on_homepage'] ? 'checked' : ''; ?>>
                    <span>Feature on Homepage</span>
                </label>
            </div>
        </div>

        <!-- Content Section -->
        <div class="form-section">
            <h2 class="form-section-title">Content</h2>
            <div class="form-group">
                <label for="features" class="form-label">Features (one per line)</label>
                <textarea id="features" name="features" class="form-textarea" rows="5"><?php echo htmlspecialchars(implode("\n", $form_data['features'])); ?></textarea>
            </div>
            <div class="form-group">
                <label for="screenshots" class="form-label">Screenshots (URLs, one per line)</label>
                <textarea id="screenshots" name="screenshots" class="form-textarea" rows="4"><?php echo htmlspecialchars(implode("\n", $form_data['screenshots'])); ?></textarea>
            </div>
            <div class="form-group">
                <label for="faqs" class="form-label">FAQs (JSON)</label>
                <textarea id="faqs" name="faqs" class="form-textarea form-textarea-code" rows="6"><?php echo !empty($form_data['faqs']) ? htmlspecialchars(json_encode($form_data['faqs'], JSON_PRETTY_PRINT)) : ''; ?></textarea>
                <p class="form-help">Format: [{"question": "...", "answer": "..."}]</p>
            </div>
            <div class="form-group">
                <label for="key_benefits_cards" class="form-label">Key Benefits Cards (JSON)</label>
                <textarea id="key_benefits_cards" name="key_benefits_cards" class="form-textarea form-textarea-code" rows="6"><?php echo !empty($form_data['key_benefits_cards']) ? htmlspecialchars(json_encode($form_data['key_benefits_cards'], JSON_PRETTY_PRINT)) : ''; ?></textarea>
                <p class="form-help">Format: [{"icon": "...", "title": "...", "description": "..."}]</p>
            </div>
            <div class="form-group">
                <label for="feature_showcase_cards" class="form-label">Feature Showcase Cards (JSON, max 6)</label>
                <textarea id="feature_showcase_cards" name="feature_showcase_cards" class="form-textarea form-textarea-code" rows="6"><?php echo !empty($form_data['feature_showcase_cards']) ? htmlspecialchars(json_encode($form_data['feature_showcase_cards'], JSON_PRETTY_PRINT)) : ''; ?></textarea>
                <p class="form-help">Format: [{"nav_label": "...", "badge": "...", "heading": "...", "image_url": "...", "features": ["...", "..."]}]</p>
            </div>
        </div>
        
        <!-- Hero Section Styling -->
        <div class="form-section">
            <h2 class="form-section-title">Hero Section Styling</h2>
            <div class="form-row">
                <div class="form-group">
                    <label for="hero_badge" class="form-label">Hero Badge</label>
                    <input type="text" id="hero_badge" name="hero_badge" class="form-input" value="<?php echo htmlspecialchars($form_data['hero_badge']); ?>">
                </div>
                <div class="form-group">
                    <label for="hero_title_text" class="form-label">Hero Title (max 24 chars)</label>
                    <input type="text" id="hero_title_text" name="hero_title_text" class="form-input" value="<?php echo htmlspecialchars($form_data['hero_title_text']); ?>" maxlength="24">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="hero_bg_color" class="form-label">Background Color</label>
                    <input type="text" id="hero_bg_color" name="hero_bg_color" class="form-input" value="<?php echo htmlspecialchars($form_data['hero_bg_color']); ?>">
                </div>
                <div class="form-group">
                    <label for="hero_media_url" class="form-label">Hero Media URL</label>
                    <input type="text" id="hero_media_url" name="hero_media_url" class="form-input" value="<?php echo htmlspecialchars($form_data['hero_media_url']); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="hero_cta_primary_text" class="form-label">Primary CTA Text</label>
                    <input type="text" id="hero_cta_primary_text" name="hero_cta_primary_text" class="form-input" value="<?php echo htmlspecialchars($form_data['hero_cta_primary_text']); ?>">
                </div>
                <div class="form-group">
                    <label for="hero_cta_primary_link" class="form-label">Primary CTA Link</label>
                    <input type="text" id="hero_cta_primary_link" name="hero_cta_primary_link" class="form-input" value="<?php echo htmlspecialchars($form_data['hero_cta_primary_link']); ?>">
                </div>
            </div>
        </div>
        
        <!-- Key Benefits Section -->
        <div class="form-section">
            <h2 class="form-section-title">Key Benefits Section</h2>
            <div class="form-group">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="key_benefits_section_enabled" class="form-checkbox" <?php echo $form_data['key_benefits_section_enabled'] ? 'checked' : ''; ?>>
                    <span>Enable Key Benefits Section</span>
                </label>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="key_benefits_section_heading1" class="form-label">Heading Line 1</label>
                    <input type="text" id="key_benefits_section_heading1" name="key_benefits_section_heading1" class="form-input" value="<?php echo htmlspecialchars($form_data['key_benefits_section_heading1']); ?>" maxlength="24">
                </div>
                <div class="form-group">
                    <label for="key_benefits_section_heading2" class="form-label">Heading Line 2</label>
                    <input type="text" id="key_benefits_section_heading2" name="key_benefits_section_heading2" class="form-input" value="<?php echo htmlspecialchars($form_data['key_benefits_section_heading2']); ?>" maxlength="24">
                </div>
            </div>
            <div class="form-group">
                <label for="key_benefits_section_subheading" class="form-label">Subheading</label>
                <textarea id="key_benefits_section_subheading" name="key_benefits_section_subheading" class="form-textarea" rows="2"><?php echo htmlspecialchars($form_data['key_benefits_section_subheading']); ?></textarea>
            </div>
        </div>

        <!-- Feature Showcase Section -->
        <div class="form-section">
            <h2 class="form-section-title">Feature Showcase Section</h2>
            <div class="form-group">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="feature_showcase_section_enabled" class="form-checkbox" <?php echo $form_data['feature_showcase_section_enabled'] ? 'checked' : ''; ?>>
                    <span>Enable Feature Showcase Section</span>
                </label>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="feature_showcase_section_title" class="form-label">Section Title</label>
                    <input type="text" id="feature_showcase_section_title" name="feature_showcase_section_title" class="form-input" value="<?php echo htmlspecialchars($form_data['feature_showcase_section_title']); ?>">
                </div>
                <div class="form-group">
                    <label for="feature_showcase_section_subtitle" class="form-label">Section Subtitle</label>
                    <input type="text" id="feature_showcase_section_subtitle" name="feature_showcase_section_subtitle" class="form-input" value="<?php echo htmlspecialchars($form_data['feature_showcase_section_subtitle']); ?>">
                </div>
            </div>
        </div>
        
        <!-- CTA Banner Section -->
        <div class="form-section">
            <h2 class="form-section-title">CTA Banner Section</h2>
            <div class="form-group">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="cta_banner_enabled" class="form-checkbox" <?php echo $form_data['cta_banner_enabled'] ? 'checked' : ''; ?>>
                    <span>Enable CTA Banner</span>
                </label>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="cta_banner_heading1" class="form-label">Heading Line 1</label>
                    <input type="text" id="cta_banner_heading1" name="cta_banner_heading1" class="form-input" value="<?php echo htmlspecialchars($form_data['cta_banner_heading1']); ?>">
                </div>
                <div class="form-group">
                    <label for="cta_banner_heading2" class="form-label">Heading Line 2</label>
                    <input type="text" id="cta_banner_heading2" name="cta_banner_heading2" class="form-input" value="<?php echo htmlspecialchars($form_data['cta_banner_heading2']); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="cta_banner_button_text" class="form-label">Button Text</label>
                    <input type="text" id="cta_banner_button_text" name="cta_banner_button_text" class="form-input" value="<?php echo htmlspecialchars($form_data['cta_banner_button_text']); ?>">
                </div>
                <div class="form-group">
                    <label for="cta_banner_button_link" class="form-label">Button Link</label>
                    <input type="text" id="cta_banner_button_link" name="cta_banner_button_link" class="form-input" value="<?php echo htmlspecialchars($form_data['cta_banner_button_link']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="cta_banner_image_url" class="form-label">Background Image URL</label>
                <input type="text" id="cta_banner_image_url" name="cta_banner_image_url" class="form-input" value="<?php echo htmlspecialchars($form_data['cta_banner_image_url']); ?>">
            </div>
        </div>
        
        <!-- Industries Gallery Section -->
        <div class="form-section">
            <h2 class="form-section-title">Industries Gallery Section</h2>
            <div class="form-group">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="industries_section_enabled" class="form-checkbox" <?php echo $form_data['industries_section_enabled'] ? 'checked' : ''; ?>>
                    <span>Enable Industries Section</span>
                </label>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="industries_section_title" class="form-label">Section Title</label>
                    <input type="text" id="industries_section_title" name="industries_section_title" class="form-input" value="<?php echo htmlspecialchars($form_data['industries_section_title']); ?>">
                </div>
                <div class="form-group">
                    <label for="industries_section_subtitle" class="form-label">Section Subtitle</label>
                    <input type="text" id="industries_section_subtitle" name="industries_section_subtitle" class="form-input" value="<?php echo htmlspecialchars($form_data['industries_section_subtitle']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="industries_cards" class="form-label">Industries Cards (JSON, max 10)</label>
                <textarea id="industries_cards" name="industries_cards" class="form-textarea form-textarea-code" rows="10"><?php echo !empty($form_data['industries_cards']) ? htmlspecialchars(json_encode($form_data['industries_cards'], JSON_PRETTY_PRINT)) : ''; ?></textarea>
                <p class="form-help">Format: [{"title": "...", "description": "...", "image_url": "...", "link_url": "...", "link_text": "Read More"}]</p>
            </div>
        </div>
        
        <!-- Testimonials Section -->
        <div class="form-section">
            <h2 class="form-section-title">Testimonials Section</h2>
            <p class="form-section-desc">Customize the testimonials showcase section that displays customer reviews.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="testimonials_section_theme" class="form-label">Theme / Mode</label>
                    <select id="testimonials_section_theme" name="testimonials_section_theme" class="form-select">
                        <option value="light" <?php echo ($form_data['testimonials_section_theme'] ?? 'light') === 'light' ? 'selected' : ''; ?>>Light</option>
                        <option value="dark" <?php echo ($form_data['testimonials_section_theme'] ?? 'light') === 'dark' ? 'selected' : ''; ?>>Dark</option>
                    </select>
                    <p class="form-help">Choose between light or dark background theme</p>
                </div>
            </div>
            
            <div class="form-group">
                <label for="testimonials_section_heading" class="form-label">Section Heading</label>
                <input type="text" id="testimonials_section_heading" name="testimonials_section_heading" class="form-input" 
                    value="<?php echo htmlspecialchars($form_data['testimonials_section_heading']); ?>" maxlength="48"
                    placeholder="e.g., What Our Customers Say">
                <p class="form-help">Max 48 characters</p>
            </div>
            
            <div class="form-group">
                <label for="testimonials_section_subheading" class="form-label">Section Subheading</label>
                <input type="text" id="testimonials_section_subheading" name="testimonials_section_subheading" class="form-input" 
                    value="<?php echo htmlspecialchars($form_data['testimonials_section_subheading']); ?>" maxlength="120"
                    placeholder="e.g., Trusted by leading businesses who have transformed their operations">
                <p class="form-help">Max 120 characters</p>
            </div>
        </div>
        
        <!-- FAQs Section -->
        <div class="form-section">
            <h2 class="form-section-title">FAQs Section</h2>
            <p class="form-section-desc">Customize the FAQs section appearance on the solution detail page.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="faqs_section_theme" class="form-label">Theme / Mode</label>
                    <select id="faqs_section_theme" name="faqs_section_theme" class="form-select">
                        <option value="light" <?php echo ($form_data['faqs_section_theme'] ?? 'light') === 'light' ? 'selected' : ''; ?>>Light</option>
                        <option value="dark" <?php echo ($form_data['faqs_section_theme'] ?? 'light') === 'dark' ? 'selected' : ''; ?>>Dark</option>
                    </select>
                    <p class="form-help">Choose between light or dark background theme</p>
                </div>
            </div>
            
            <div class="form-group">
                <label for="faqs_section_heading" class="form-label">Section Heading</label>
                <input type="text" id="faqs_section_heading" name="faqs_section_heading" class="form-input" 
                    value="<?php echo htmlspecialchars($form_data['faqs_section_heading']); ?>" maxlength="48"
                    placeholder="e.g., Frequently Asked Questions">
                <p class="form-help">Max 48 characters</p>
            </div>
            
            <div class="form-group">
                <label for="faqs_section_subheading" class="form-label">Section Subheading</label>
                <input type="text" id="faqs_section_subheading" name="faqs_section_subheading" class="form-input" 
                    value="<?php echo htmlspecialchars($form_data['faqs_section_subheading']); ?>" maxlength="120"
                    placeholder="e.g., Everything you need to know about our solution">
                <p class="form-help">Max 120 characters</p>
            </div>
        </div>
        
        <!-- SEO Section -->
        <div class="form-section">
            <h2 class="form-section-title">SEO Settings</h2>
            <div class="form-group">
                <label for="meta_title" class="form-label">Meta Title</label>
                <input type="text" id="meta_title" name="meta_title" class="form-input" value="<?php echo htmlspecialchars($form_data['meta_title']); ?>">
            </div>
            <div class="form-group">
                <label for="meta_description" class="form-label">Meta Description</label>
                <textarea id="meta_description" name="meta_description" class="form-textarea" rows="2"><?php echo htmlspecialchars($form_data['meta_description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                <input type="text" id="meta_keywords" name="meta_keywords" class="form-input" value="<?php echo htmlspecialchars($form_data['meta_keywords']); ?>">
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Solution</button>
            <a href="<?php echo get_app_base_url(); ?>/admin/solutions.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.admin-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 14px; margin-bottom: 8px; }
.admin-breadcrumb a { color: var(--color-primary); text-decoration: none; }
.breadcrumb-separator { color: var(--color-gray-400); }
.admin-page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.admin-page-title { font-size: 24px; font-weight: 700; margin: 0 0 8px 0; }
.admin-page-description { font-size: 14px; color: var(--color-gray-600); margin: 0; }
.alert { padding: 16px; border-radius: 8px; margin-bottom: 24px; }
.alert-error { background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
.alert ul { margin: 8px 0 0 16px; }
.admin-form { padding: 24px; }
.form-section { margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid var(--color-gray-200); }
.form-section:last-of-type { border-bottom: none; }
.form-section-title { font-size: 18px; font-weight: 600; margin: 0 0 16px 0; }
.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; }
.form-label.required::after { content: ' *'; color: #dc2626; }
.form-input, .form-select, .form-textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--color-gray-300); border-radius: 6px; font-size: 14px; box-sizing: border-box; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--color-primary); }
.form-textarea { resize: vertical; }
.form-textarea-code { font-family: monospace; font-size: 13px; }
.form-help { font-size: 12px; color: var(--color-gray-500); margin: 4px 0 0 0; }
.form-checkbox-label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
.form-checkbox { width: 18px; height: 18px; }
.form-actions { display: flex; gap: 12px; padding-top: 24px; }
@media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
</style>

<script>
document.getElementById('name').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
        slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9\s\-]/g, '').replace(/[\s_]+/g, '-').replace(/-+/g, '-').replace(/^-+|-+$/g, '');
        slugInput.dataset.autoGenerated = 'true';
    }
});
document.getElementById('slug').addEventListener('input', function() { if (this.value) this.dataset.autoGenerated = 'false'; });
</script>

<?php include_admin_footer(); ?>
