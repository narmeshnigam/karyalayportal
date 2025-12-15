<?php
/**
 * Admin Edit Solution Page
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';
require_once __DIR__ . '/../../includes/template_helpers.php';

use Karyalay\Services\ContentService;
use Karyalay\Models\Solution;

startSecureSession();
require_admin();
require_permission('solutions.manage');

$contentService = new ContentService();

$solution_id = $_GET['id'] ?? '';

if (empty($solution_id)) {
    $_SESSION['admin_error'] = 'Solution ID is required.';
    header('Location: ' . get_app_base_url() . '/admin/solutions.php');
    exit;
}

$solution = $contentService->read('solution', $solution_id);

if (!$solution) {
    $_SESSION['admin_error'] = 'Solution not found.';
    header('Location: ' . get_app_base_url() . '/admin/solutions.php');
    exit;
}

$errors = [];
$form_data = [
    'name' => $solution['name'],
    'slug' => $solution['slug'],
    'description' => $solution['description'] ?? '',
    'tagline' => $solution['tagline'] ?? '',
    'subtitle' => $solution['subtitle'] ?? '',
    'hero_badge' => $solution['hero_badge'] ?? '',
    'hero_title_text' => $solution['hero_title_text'] ?? '',
    'hero_title_color' => $solution['hero_title_color'] ?? '#FFFFFF',
    'hero_subtitle_color' => $solution['hero_subtitle_color'] ?? '#FFFFFF',
    'icon_image' => $solution['icon_image'] ?? '',
    'hero_media_url' => $solution['hero_media_url'] ?? '',
    'hero_media_type' => $solution['hero_media_type'] ?? 'image',
    'demo_video_url' => $solution['demo_video_url'] ?? '',
    'hero_cta_primary_text' => $solution['hero_cta_primary_text'] ?? 'Get Started',
    'hero_cta_primary_link' => $solution['hero_cta_primary_link'] ?? '',
    'hero_cta_secondary_text' => $solution['hero_cta_secondary_text'] ?? 'Watch Demo',
    'hero_cta_secondary_link' => $solution['hero_cta_secondary_link'] ?? '',
    'hero_primary_btn_bg_color' => $solution['hero_primary_btn_bg_color'] ?? 'rgba(255,255,255,0.15)',
    'hero_primary_btn_text_color' => $solution['hero_primary_btn_text_color'] ?? '#FFFFFF',
    'hero_primary_btn_text_hover_color' => $solution['hero_primary_btn_text_hover_color'] ?? '#FFFFFF',
    'hero_primary_btn_border_color' => $solution['hero_primary_btn_border_color'] ?? 'rgba(255,255,255,0.3)',
    'hero_secondary_btn_bg_color' => $solution['hero_secondary_btn_bg_color'] ?? 'rgba(255,255,255,0.1)',
    'hero_secondary_btn_text_color' => $solution['hero_secondary_btn_text_color'] ?? '#FFFFFF',
    'hero_secondary_btn_text_hover_color' => $solution['hero_secondary_btn_text_hover_color'] ?? '#FFFFFF',
    'hero_secondary_btn_border_color' => $solution['hero_secondary_btn_border_color'] ?? 'rgba(255,255,255,0.2)',
    'hero_bg_gradient_opacity' => $solution['hero_bg_gradient_opacity'] ?? 0.60,
    'hero_bg_pattern_opacity' => $solution['hero_bg_pattern_opacity'] ?? 0.03,
    'hero_bg_gradient_color' => $solution['hero_bg_gradient_color'] ?? '',
    'hero_bg_color' => $solution['hero_bg_color'] ?? '#0a1628',
    'color_theme' => $solution['color_theme'] ?? '#667eea',
    'features' => $solution['features'] ?? [],
    'screenshots' => $solution['screenshots'] ?? [],
    'faqs' => $solution['faqs'] ?? [],
    'key_benefits_section_enabled' => $solution['key_benefits_section_enabled'] ?? false,
    'key_benefits_section_bg_color' => $solution['key_benefits_section_bg_color'] ?? '#0a1628',
    'key_benefits_section_heading1' => $solution['key_benefits_section_heading1'] ?? '',
    'key_benefits_section_heading2' => $solution['key_benefits_section_heading2'] ?? '',
    'key_benefits_section_subheading' => $solution['key_benefits_section_subheading'] ?? '',
    'key_benefits_section_heading_color' => $solution['key_benefits_section_heading_color'] ?? '#FFFFFF',
    'key_benefits_section_subheading_color' => $solution['key_benefits_section_subheading_color'] ?? 'rgba(255,255,255,0.7)',
    'key_benefits_section_card_bg_color' => $solution['key_benefits_section_card_bg_color'] ?? 'rgba(255,255,255,0.08)',
    'key_benefits_section_card_border_color' => $solution['key_benefits_section_card_border_color'] ?? 'rgba(255,255,255,0.1)',
    'key_benefits_section_card_hover_bg_color' => $solution['key_benefits_section_card_hover_bg_color'] ?? '#2563eb',
    'key_benefits_section_card_text_color' => $solution['key_benefits_section_card_text_color'] ?? '#FFFFFF',
    'key_benefits_section_card_icon_color' => $solution['key_benefits_section_card_icon_color'] ?? 'rgba(255,255,255,0.6)',
    'key_benefits_cards' => $solution['key_benefits_cards'] ?? [],
    'feature_showcase_section_enabled' => $solution['feature_showcase_section_enabled'] ?? false,
    'feature_showcase_section_title' => $solution['feature_showcase_section_title'] ?? 'One solution. All business sizes.',
    'feature_showcase_section_subtitle' => $solution['feature_showcase_section_subtitle'] ?? 'From instant, self-serve payouts to custom integrations for enterprise scale operations',
    'feature_showcase_section_bg_color' => $solution['feature_showcase_section_bg_color'] ?? '#ffffff',
    'feature_showcase_section_title_color' => $solution['feature_showcase_section_title_color'] ?? '#1a202c',
    'feature_showcase_section_subtitle_color' => $solution['feature_showcase_section_subtitle_color'] ?? '#718096',
    'feature_showcase_card_bg_color' => $solution['feature_showcase_card_bg_color'] ?? '#ffffff',
    'feature_showcase_card_border_color' => $solution['feature_showcase_card_border_color'] ?? '#e2e8f0',
    'feature_showcase_card_badge_bg_color' => $solution['feature_showcase_card_badge_bg_color'] ?? '#ebf8ff',
    'feature_showcase_card_badge_text_color' => $solution['feature_showcase_card_badge_text_color'] ?? '#2b6cb0',
    'feature_showcase_card_heading_color' => $solution['feature_showcase_card_heading_color'] ?? '#1a202c',
    'feature_showcase_card_text_color' => $solution['feature_showcase_card_text_color'] ?? '#4a5568',
    'feature_showcase_card_icon_color' => $solution['feature_showcase_card_icon_color'] ?? '#38a169',
    'feature_showcase_cards' => $solution['feature_showcase_cards'] ?? [],
    'cta_banner_enabled' => $solution['cta_banner_enabled'] ?? true,
    'cta_banner_image_url' => $solution['cta_banner_image_url'] ?? '',
    'cta_banner_overlay_color' => $solution['cta_banner_overlay_color'] ?? 'rgba(0,0,0,0.5)',
    'cta_banner_overlay_intensity' => $solution['cta_banner_overlay_intensity'] ?? 0.50,
    'cta_banner_heading1' => $solution['cta_banner_heading1'] ?? 'Streamline across 30+ modules.',
    'cta_banner_heading2' => $solution['cta_banner_heading2'] ?? 'Transform your business today!',
    'cta_banner_heading_color' => $solution['cta_banner_heading_color'] ?? '#FFFFFF',
    'cta_banner_button_text' => $solution['cta_banner_button_text'] ?? 'Explore ERP Solutions',
    'cta_banner_button_link' => $solution['cta_banner_button_link'] ?? '#contact-form',
    'cta_banner_button_bg_color' => $solution['cta_banner_button_bg_color'] ?? '#FFFFFF',
    'cta_banner_button_text_color' => $solution['cta_banner_button_text_color'] ?? '#2563eb',
    'pricing_note' => $solution['pricing_note'] ?? '',
    'meta_title' => $solution['meta_title'] ?? '',
    'meta_description' => $solution['meta_description'] ?? '',
    'meta_keywords' => $solution['meta_keywords'] ?? '',
    'display_order' => $solution['display_order'] ?? 0,
    'status' => $solution['status'] ?? 'DRAFT',
    'featured_on_homepage' => $solution['featured_on_homepage'] ?? false
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        $form_data['name'] = sanitizeString($_POST['name'] ?? '');
        $form_data['slug'] = sanitizeString($_POST['slug'] ?? '');
        $form_data['description'] = sanitizeString($_POST['description'] ?? '');
        $form_data['tagline'] = sanitizeString($_POST['tagline'] ?? '');
        $form_data['subtitle'] = sanitizeString($_POST['subtitle'] ?? '');
        $form_data['hero_badge'] = sanitizeString($_POST['hero_badge'] ?? '');
        $form_data['hero_title_text'] = substr(sanitizeString($_POST['hero_title_text'] ?? ''), 0, 24);
        $form_data['hero_title_color'] = sanitizeString($_POST['hero_title_color'] ?? '#FFFFFF');
        $form_data['hero_subtitle_color'] = sanitizeString($_POST['hero_subtitle_color'] ?? '#FFFFFF');
        $form_data['icon_image'] = sanitizeString($_POST['icon_image'] ?? '');
        $form_data['hero_media_url'] = sanitizeString($_POST['hero_media_url'] ?? '');
        $form_data['hero_media_type'] = sanitizeString($_POST['hero_media_type'] ?? 'image');
        $form_data['demo_video_url'] = sanitizeString($_POST['demo_video_url'] ?? '');
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
        $form_data['hero_bg_gradient_opacity'] = floatval($_POST['hero_bg_gradient_opacity'] ?? 0.60);
        $form_data['hero_bg_pattern_opacity'] = floatval($_POST['hero_bg_pattern_opacity'] ?? 0.03);
        $form_data['hero_bg_gradient_color'] = sanitizeString($_POST['hero_bg_gradient_color'] ?? '');
        $form_data['hero_bg_color'] = sanitizeString($_POST['hero_bg_color'] ?? '#0a1628');
        $form_data['color_theme'] = sanitizeString($_POST['color_theme'] ?? '#667eea');
        $form_data['pricing_note'] = sanitizeString($_POST['pricing_note'] ?? '');
        $form_data['meta_title'] = sanitizeString($_POST['meta_title'] ?? '');
        $form_data['meta_description'] = sanitizeString($_POST['meta_description'] ?? '');
        $form_data['meta_keywords'] = sanitizeString($_POST['meta_keywords'] ?? '');
        $form_data['display_order'] = sanitizeInt($_POST['display_order'] ?? 0);
        $form_data['status'] = sanitizeString($_POST['status'] ?? 'DRAFT');
        $form_data['featured_on_homepage'] = isset($_POST['featured_on_homepage']) ? true : false;
        
        if (empty($form_data['name'])) {
            $errors[] = 'Solution name is required.';
        }
        
        if (empty($form_data['slug'])) {
            $errors[] = 'Solution slug is required.';
        }
        
        if (!in_array($form_data['status'], ['DRAFT', 'PUBLISHED', 'ARCHIVED'])) {
            $errors[] = 'Invalid status value.';
        }
        
        if (!empty($_POST['features'])) {
            $features_raw = explode("\n", $_POST['features']);
            $form_data['features'] = array_filter(array_map(function($feature) {
                return trim(sanitizeString($feature));
            }, $features_raw));
        } else {
            $form_data['features'] = [];
        }
        
        if (!empty($_POST['screenshots'])) {
            $screenshots_raw = explode("\n", $_POST['screenshots']);
            $form_data['screenshots'] = array_filter(array_map(function($url) {
                return trim(sanitizeString($url));
            }, $screenshots_raw));
        } else {
            $form_data['screenshots'] = [];
        }
        
        if (!empty($_POST['faqs'])) {
            $faqs_decoded = json_decode($_POST['faqs'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($faqs_decoded)) {
                $form_data['faqs'] = $faqs_decoded;
            } else {
                $errors[] = 'FAQs must be valid JSON format.';
            }
        } else {
            $form_data['faqs'] = [];
        }
        
        // Process new JSON fields
        $jsonFields = ['key_benefits_cards', 'feature_showcase_cards'];
        foreach ($jsonFields as $field) {
            if (!empty($_POST[$field])) {
                $decoded = json_decode($_POST[$field], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // Limit feature_showcase_cards to max 6
                    if ($field === 'feature_showcase_cards' && count($decoded) > 6) {
                        $decoded = array_slice($decoded, 0, 6);
                    }
                    $form_data[$field] = $decoded;
                } else {
                    $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' must be valid JSON format.';
                }
            } else {
                $form_data[$field] = [];
            }
        }
        
        // Process key benefits section fields
        $form_data['key_benefits_section_enabled'] = isset($_POST['key_benefits_section_enabled']) ? true : false;
        $form_data['key_benefits_section_bg_color'] = sanitizeString($_POST['key_benefits_section_bg_color'] ?? '#0a1628');
        $form_data['key_benefits_section_heading1'] = substr(sanitizeString($_POST['key_benefits_section_heading1'] ?? ''), 0, 24);
        $form_data['key_benefits_section_heading2'] = substr(sanitizeString($_POST['key_benefits_section_heading2'] ?? ''), 0, 24);
        $form_data['key_benefits_section_subheading'] = substr(sanitizeString($_POST['key_benefits_section_subheading'] ?? ''), 0, 240);
        $form_data['key_benefits_section_heading_color'] = sanitizeString($_POST['key_benefits_section_heading_color'] ?? '#FFFFFF');
        $form_data['key_benefits_section_subheading_color'] = sanitizeString($_POST['key_benefits_section_subheading_color'] ?? 'rgba(255,255,255,0.7)');
        $form_data['key_benefits_section_card_bg_color'] = sanitizeString($_POST['key_benefits_section_card_bg_color'] ?? 'rgba(255,255,255,0.08)');
        $form_data['key_benefits_section_card_border_color'] = sanitizeString($_POST['key_benefits_section_card_border_color'] ?? 'rgba(255,255,255,0.1)');
        $form_data['key_benefits_section_card_hover_bg_color'] = sanitizeString($_POST['key_benefits_section_card_hover_bg_color'] ?? '#2563eb');
        $form_data['key_benefits_section_card_text_color'] = sanitizeString($_POST['key_benefits_section_card_text_color'] ?? '#FFFFFF');
        $form_data['key_benefits_section_card_icon_color'] = sanitizeString($_POST['key_benefits_section_card_icon_color'] ?? 'rgba(255,255,255,0.6)');
        
        // Process feature showcase section fields
        $form_data['feature_showcase_section_enabled'] = isset($_POST['feature_showcase_section_enabled']) ? true : false;
        $form_data['feature_showcase_section_title'] = substr(sanitizeString($_POST['feature_showcase_section_title'] ?? ''), 0, 100);
        $form_data['feature_showcase_section_subtitle'] = substr(sanitizeString($_POST['feature_showcase_section_subtitle'] ?? ''), 0, 255);
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
        
        // Process CTA Banner section fields
        $form_data['cta_banner_enabled'] = isset($_POST['cta_banner_enabled']) ? true : false;
        $form_data['cta_banner_image_url'] = sanitizeString($_POST['cta_banner_image_url'] ?? '');
        $form_data['cta_banner_overlay_color'] = sanitizeString($_POST['cta_banner_overlay_color'] ?? 'rgba(0,0,0,0.5)');
        $form_data['cta_banner_overlay_intensity'] = floatval($_POST['cta_banner_overlay_intensity'] ?? 0.50);
        $form_data['cta_banner_heading1'] = substr(sanitizeString($_POST['cta_banner_heading1'] ?? ''), 0, 100);
        $form_data['cta_banner_heading2'] = substr(sanitizeString($_POST['cta_banner_heading2'] ?? ''), 0, 100);
        $form_data['cta_banner_heading_color'] = sanitizeString($_POST['cta_banner_heading_color'] ?? '#FFFFFF');
        $form_data['cta_banner_button_text'] = substr(sanitizeString($_POST['cta_banner_button_text'] ?? ''), 0, 50);
        $form_data['cta_banner_button_link'] = sanitizeString($_POST['cta_banner_button_link'] ?? '#contact-form');
        $form_data['cta_banner_button_bg_color'] = sanitizeString($_POST['cta_banner_button_bg_color'] ?? '#FFFFFF');
        $form_data['cta_banner_button_text_color'] = sanitizeString($_POST['cta_banner_button_text_color'] ?? '#2563eb');
        
        if (empty($errors)) {
            try {
                // Remove new hero fields if they might not exist in database yet
                // This allows updates to work even before migrations 056 and 060 are run
                $safeFormData = $form_data;
                $newHeroFields = [
                    'hero_title_text', 'hero_title_color', 'hero_subtitle_color',
                    'hero_media_url', 'hero_media_type', 'hero_bg_color',
                    'hero_primary_btn_bg_color', 'hero_primary_btn_text_color', 'hero_primary_btn_text_hover_color', 'hero_primary_btn_border_color',
                    'hero_secondary_btn_bg_color', 'hero_secondary_btn_text_color', 'hero_secondary_btn_text_hover_color', 'hero_secondary_btn_border_color'
                ];
                
                // Try update with all fields first
                $result = $contentService->update('solution', $solution_id, $form_data);
                
                if ($result) {
                    $_SESSION['admin_success'] = 'Solution updated successfully!';
                    header('Location: ' . get_app_base_url() . '/admin/solutions.php');
                    exit;
                } else {
                    // Check if slug conflict is the issue
                    $solutionModel = new Solution();
                    $sanitizedSlug = strtolower(preg_replace('/[^a-z0-9\-]/', '', preg_replace('/[\s_]+/', '-', strtolower($form_data['slug']))));
                    if ($solutionModel->slugExists($sanitizedSlug, $solution_id)) {
                        $errors[] = 'The slug "' . htmlspecialchars($form_data['slug']) . '" is already in use by another solution.';
                    } else {
                        // Try without new hero fields (in case migration hasn't been run)
                        foreach ($newHeroFields as $field) {
                            unset($safeFormData[$field]);
                        }
                        $retryResult = $contentService->update('solution', $solution_id, $safeFormData);
                        if ($retryResult) {
                            $_SESSION['admin_success'] = 'Solution updated (some hero styling fields were skipped - run migrations 056 and 060 to enable all features).';
                            header('Location: ' . get_app_base_url() . '/admin/solutions.php');
                            exit;
                        } else {
                            $errors[] = 'Failed to update solution. Please check the error log for details.';
                        }
                    }
                }
            } catch (Exception $e) {
                error_log('Solution update error: ' . $e->getMessage());
                $errors[] = 'Database error: ' . htmlspecialchars($e->getMessage());
            }
        }
    }
}

$csrf_token = getCsrfToken();
include_admin_header('Edit Solution');
?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <nav class="admin-breadcrumb">
            <a href="<?php echo get_app_base_url(); ?>/admin/solutions.php">Solutions</a>
            <span class="breadcrumb-separator">/</span>
            <span>Edit Solution</span>
        </nav>
        <h1 class="admin-page-title">Edit Solution</h1>
        <p class="admin-page-description">Update solution information</p>
    </div>
    <div class="admin-page-header-actions">
        <a href="<?php echo get_app_base_url(); ?>/admin/solutions.php" class="btn btn-secondary">← Back to Solutions</a>
        <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete Solution</button>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>Error:</strong>
        <ul><?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" action="<?php echo get_app_base_url(); ?>/admin/solutions/edit.php?id=<?php echo urlencode($solution_id); ?>" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <!-- Basic Information & Settings -->
        <div class="form-section">
            <h2 class="form-section-title">Basic Information & Settings</h2>
            <p class="form-section-desc">Core solution details, branding, and publication settings.</p>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Solution Identity</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label required">Solution Name</label>
                        <input type="text" id="name" name="name" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['name']); ?>" required maxlength="255">
                        <p class="form-help">The display name of the solution</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="slug" class="form-label required">Slug</label>
                        <input type="text" id="slug" name="slug" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['slug']); ?>" required pattern="[a-z0-9\-]+" maxlength="255">
                        <p class="form-help">URL-friendly identifier</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea" rows="3"><?php echo htmlspecialchars($form_data['description']); ?></textarea>
                    <p class="form-help">Brief description of the solution</p>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Branding & Media</h3>
                <div class="form-group">
                    <label for="icon_image" class="form-label">Icon Image</label>
                    <div class="image-input-group">
                        <input type="text" id="icon_image" name="icon_image" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['icon_image']); ?>"
                            placeholder="https://example.com/icon.png">
                        <?php if (!empty($form_data['icon_image'])): ?>
                            <div class="image-preview">
                                <img src="<?php echo htmlspecialchars($form_data['icon_image']); ?>" alt="Icon preview">
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="form-help">URL to a PNG icon image (recommended: 64x64 or 128x128 pixels). Displayed on homepage cards.</p>
                </div>
                
                <div class="form-group">
                    <label for="demo_video_url" class="form-label">Demo Video URL</label>
                    <input type="url" id="demo_video_url" name="demo_video_url" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['demo_video_url']); ?>"
                        placeholder="https://youtube.com/watch?v=...">
                    <p class="form-help">YouTube or Vimeo video URL</p>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Publication Settings</h3>
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
                        <p class="form-help">Only published solutions appear on the website</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-checkbox-group">
                        <label class="form-checkbox-label">
                            <input type="checkbox" id="featured_on_homepage" name="featured_on_homepage" 
                                   class="form-checkbox" value="1"
                                   <?php echo !empty($form_data['featured_on_homepage']) ? 'checked' : ''; ?>>
                            <span class="form-checkbox-text">
                                <strong>Feature on Homepage</strong>
                                <span class="form-checkbox-help">Display this solution in the "Powerful Solutions" section on the homepage</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hero Section (First Section on Page) -->
        <div class="form-section">
            <h2 class="form-section-title">Hero Section (First Section on Page)</h2>
            <p class="form-section-desc">Configure the main hero section with title, content, media, background effects, and CTA buttons.</p>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Hero Content & Text</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tagline" class="form-label">Tagline</label>
                        <input type="text" id="tagline" name="tagline" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['tagline']); ?>" maxlength="255"
                            placeholder="e.g., ENTERPRISE SOLUTION">
                        <p class="form-help">Short uppercase tagline above title</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="hero_badge" class="form-label">Hero Badge</label>
                        <input type="text" id="hero_badge" name="hero_badge" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_badge']); ?>" maxlength="100"
                            placeholder="e.g., New, Popular, Beta">
                        <p class="form-help">Badge displayed in hero section</p>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="hero_title_text" class="form-label">Hero Title</label>
                        <input type="text" id="hero_title_text" name="hero_title_text" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_title_text']); ?>" maxlength="24"
                            placeholder="Max 24 characters">
                        <p class="form-help"><span id="hero_title_char_count"><?php echo strlen($form_data['hero_title_text']); ?></span>/24 characters (uses solution name if empty)</p>
                    </div>
                    <div class="form-group">
                        <label for="hero_title_color" class="form-label">Title Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="hero_title_color" name="hero_title_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['hero_title_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="hero_title_color_hex"
                                value="<?php echo htmlspecialchars($form_data['hero_title_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="subtitle" class="form-label">Subtitle</label>
                    <textarea id="subtitle" name="subtitle" class="form-textarea" rows="2"
                        placeholder="Extended description for the hero section"><?php echo htmlspecialchars($form_data['subtitle']); ?></textarea>
                    <p class="form-help">Longer description shown below the title in hero</p>
                </div>
                
                <div class="form-group">
                    <label for="hero_subtitle_color" class="form-label">Subtitle/Description Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="hero_subtitle_color" name="hero_subtitle_color" class="form-color-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_subtitle_color']); ?>">
                        <input type="text" class="form-input color-hex-input" id="hero_subtitle_color_hex"
                            value="<?php echo htmlspecialchars($form_data['hero_subtitle_color']); ?>" maxlength="7">
                    </div>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Hero Media (Animation)</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="hero_media_url" class="form-label">Media URL (GIF/MP4)</label>
                        <input type="url" id="hero_media_url" name="hero_media_url" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_media_url']); ?>"
                            placeholder="https://example.com/animation.gif or .mp4">
                        <p class="form-help">URL to GIF or MP4 file for autoplaying animation</p>
                    </div>
                    <div class="form-group">
                        <label for="hero_media_type" class="form-label">Media Type</label>
                        <select id="hero_media_type" name="hero_media_type" class="form-select">
                            <option value="image" <?php echo ($form_data['hero_media_type'] ?? 'image') === 'image' ? 'selected' : ''; ?>>Image (Static)</option>
                            <option value="gif" <?php echo ($form_data['hero_media_type'] ?? '') === 'gif' ? 'selected' : ''; ?>>GIF (Animated)</option>
                            <option value="video" <?php echo ($form_data['hero_media_type'] ?? '') === 'video' ? 'selected' : ''; ?>>Video (MP4)</option>
                        </select>
                    </div>
                </div>
                

            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Colors & Theme</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="color_theme" class="form-label">Color Theme</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="color_theme" name="color_theme" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['color_theme']); ?>">
                            <input type="text" class="form-input color-hex-input" id="color_theme_hex"
                                value="<?php echo htmlspecialchars($form_data['color_theme']); ?>" maxlength="7">
                        </div>
                        <p class="form-help">Primary color for this solution (used in gradients, buttons, etc.)</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="hero_bg_color" class="form-label">Hero Background Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="hero_bg_color" name="hero_bg_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['hero_bg_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="hero_bg_color_hex"
                                value="<?php echo htmlspecialchars($form_data['hero_bg_color']); ?>" maxlength="7">
                        </div>
                        <p class="form-help">Base background color for the hero section</p>
                    </div>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Background Effects</h3>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="hero_bg_gradient_opacity" class="form-label">Gradient Intensity</label>
                        <input type="range" id="hero_bg_gradient_opacity" name="hero_bg_gradient_opacity" 
                            class="form-range" min="0" max="1" step="0.05"
                            value="<?php echo htmlspecialchars($form_data['hero_bg_gradient_opacity']); ?>">
                        <p class="form-help">Opacity: <span id="gradient_opacity_value"><?php echo number_format($form_data['hero_bg_gradient_opacity'] * 100, 0); ?>%</span></p>
                    </div>
                    <div class="form-group">
                        <label for="hero_bg_pattern_opacity" class="form-label">Pattern Intensity</label>
                        <input type="range" id="hero_bg_pattern_opacity" name="hero_bg_pattern_opacity" 
                            class="form-range" min="0" max="0.1" step="0.005"
                            value="<?php echo htmlspecialchars($form_data['hero_bg_pattern_opacity']); ?>">
                        <p class="form-help">Opacity: <span id="pattern_opacity_value"><?php echo number_format($form_data['hero_bg_pattern_opacity'] * 100, 1); ?>%</span></p>
                    </div>
                    <div class="form-group">
                        <label for="hero_bg_gradient_color" class="form-label">Gradient Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="hero_bg_gradient_color" name="hero_bg_gradient_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['hero_bg_gradient_color'] ?: $form_data['color_theme']); ?>">
                            <input type="text" class="form-input color-hex-input" id="hero_bg_gradient_color_hex"
                                value="<?php echo htmlspecialchars($form_data['hero_bg_gradient_color'] ?: ''); ?>" 
                                maxlength="7" placeholder="Use theme">
                        </div>
                        <p class="form-help">Leave empty to use theme color</p>
                    </div>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">CTA Buttons</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="hero_cta_primary_text" class="form-label">Primary Button Text</label>
                        <input type="text" id="hero_cta_primary_text" name="hero_cta_primary_text" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_cta_primary_text']); ?>" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="hero_cta_primary_link" class="form-label">Primary Button Link</label>
                        <input type="text" id="hero_cta_primary_link" name="hero_cta_primary_link" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_cta_primary_link']); ?>"
                            placeholder="Leave empty to scroll to contact form">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="hero_cta_secondary_text" class="form-label">Secondary Button Text</label>
                        <input type="text" id="hero_cta_secondary_text" name="hero_cta_secondary_text" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_cta_secondary_text']); ?>" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="hero_cta_secondary_link" class="form-label">Secondary Button Link</label>
                        <input type="text" id="hero_cta_secondary_link" name="hero_cta_secondary_link" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_cta_secondary_link']); ?>"
                            placeholder="Uses demo video URL if empty">
                    </div>
                </div>
                
                <h4 style="margin: 20px 0 12px 0; font-size: 14px; font-weight: 600; color: var(--color-gray-700);">Primary Button Styling</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="hero_primary_btn_bg_color" class="form-label">Background</label>
                        <input type="text" id="hero_primary_btn_bg_color" name="hero_primary_btn_bg_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_primary_btn_bg_color']); ?>"
                            placeholder="rgba(255,255,255,0.15)">
                    </div>
                    <div class="form-group">
                        <label for="hero_primary_btn_border_color" class="form-label">Border</label>
                        <input type="text" id="hero_primary_btn_border_color" name="hero_primary_btn_border_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_primary_btn_border_color']); ?>"
                            placeholder="rgba(255,255,255,0.3)">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="hero_primary_btn_text_color" class="form-label">Text Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="hero_primary_btn_text_color" name="hero_primary_btn_text_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['hero_primary_btn_text_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="hero_primary_btn_text_color_hex"
                                value="<?php echo htmlspecialchars($form_data['hero_primary_btn_text_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hero_primary_btn_text_hover_color" class="form-label">Text Hover Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="hero_primary_btn_text_hover_color" name="hero_primary_btn_text_hover_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['hero_primary_btn_text_hover_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="hero_primary_btn_text_hover_color_hex"
                                value="<?php echo htmlspecialchars($form_data['hero_primary_btn_text_hover_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                
                <h4 style="margin: 20px 0 12px 0; font-size: 14px; font-weight: 600; color: var(--color-gray-700);">Secondary Button Styling</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="hero_secondary_btn_bg_color" class="form-label">Background</label>
                        <input type="text" id="hero_secondary_btn_bg_color" name="hero_secondary_btn_bg_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_secondary_btn_bg_color']); ?>"
                            placeholder="rgba(255,255,255,0.1)">
                    </div>
                    <div class="form-group">
                        <label for="hero_secondary_btn_border_color" class="form-label">Border</label>
                        <input type="text" id="hero_secondary_btn_border_color" name="hero_secondary_btn_border_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_secondary_btn_border_color']); ?>"
                            placeholder="rgba(255,255,255,0.2)">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="hero_secondary_btn_text_color" class="form-label">Text Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="hero_secondary_btn_text_color" name="hero_secondary_btn_text_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['hero_secondary_btn_text_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="hero_secondary_btn_text_color_hex"
                                value="<?php echo htmlspecialchars($form_data['hero_secondary_btn_text_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hero_secondary_btn_text_hover_color" class="form-label">Text Hover Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="hero_secondary_btn_text_hover_color" name="hero_secondary_btn_text_hover_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['hero_secondary_btn_text_hover_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="hero_secondary_btn_text_hover_color_hex"
                                value="<?php echo htmlspecialchars($form_data['hero_secondary_btn_text_hover_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">Key Benefits Section (After Hero)</h2>
            <p class="form-section-desc">A Razorpay-style section with 4 interactive cards that show key benefits on hover.</p>
            
            <div class="form-group">
                <div class="form-checkbox-group">
                    <label class="form-checkbox-label">
                        <input type="checkbox" id="key_benefits_section_enabled" name="key_benefits_section_enabled" 
                               class="form-checkbox" value="1"
                               <?php echo !empty($form_data['key_benefits_section_enabled']) ? 'checked' : ''; ?>>
                        <span class="form-checkbox-text">
                            <strong>Enable Key Benefits Section</strong>
                            <span class="form-checkbox-help">Show this section right after the hero</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Section Headings</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="key_benefits_section_heading1" class="form-label">Heading Line 1</label>
                        <input type="text" id="key_benefits_section_heading1" name="key_benefits_section_heading1" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['key_benefits_section_heading1']); ?>" maxlength="24"
                            placeholder="e.g., Key Benefits">
                        <p class="form-help"><span id="key_benefits_heading1_count"><?php echo strlen($form_data['key_benefits_section_heading1']); ?></span>/24 characters</p>
                    </div>
                    <div class="form-group">
                        <label for="key_benefits_section_heading2" class="form-label">Heading Line 2</label>
                        <input type="text" id="key_benefits_section_heading2" name="key_benefits_section_heading2" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['key_benefits_section_heading2']); ?>" maxlength="24"
                            placeholder="e.g., That Matter">
                        <p class="form-help"><span id="key_benefits_heading2_count"><?php echo strlen($form_data['key_benefits_section_heading2']); ?></span>/24 characters</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="key_benefits_section_subheading" class="form-label">Subheading</label>
                    <textarea id="key_benefits_section_subheading" name="key_benefits_section_subheading" class="form-textarea" rows="2"
                        maxlength="240" placeholder="e.g., Discover the advantages that make our solution stand out"><?php echo htmlspecialchars($form_data['key_benefits_section_subheading']); ?></textarea>
                    <p class="form-help"><span id="key_benefits_subheading_count"><?php echo strlen($form_data['key_benefits_section_subheading']); ?></span>/240 characters</p>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Section Colors</h3>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="key_benefits_section_bg_color" class="form-label">Background Color</label>
                        <input type="text" id="key_benefits_section_bg_color" name="key_benefits_section_bg_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['key_benefits_section_bg_color']); ?>"
                            placeholder="#0a1628">
                    </div>
                    <div class="form-group">
                        <label for="key_benefits_section_heading_color" class="form-label">Heading Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="key_benefits_section_heading_color" name="key_benefits_section_heading_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['key_benefits_section_heading_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="key_benefits_section_heading_color_hex"
                                value="<?php echo htmlspecialchars($form_data['key_benefits_section_heading_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="key_benefits_section_subheading_color" class="form-label">Subheading Color</label>
                        <input type="text" id="key_benefits_section_subheading_color" name="key_benefits_section_subheading_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['key_benefits_section_subheading_color']); ?>"
                            placeholder="rgba(255,255,255,0.7)">
                    </div>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Card Styling</h3>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="key_benefits_section_card_bg_color" class="form-label">Card Background</label>
                        <input type="text" id="key_benefits_section_card_bg_color" name="key_benefits_section_card_bg_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['key_benefits_section_card_bg_color']); ?>"
                            placeholder="rgba(255,255,255,0.08)">
                    </div>
                    <div class="form-group">
                        <label for="key_benefits_section_card_border_color" class="form-label">Card Border</label>
                        <input type="text" id="key_benefits_section_card_border_color" name="key_benefits_section_card_border_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['key_benefits_section_card_border_color']); ?>"
                            placeholder="rgba(255,255,255,0.1)">
                    </div>
                    <div class="form-group">
                        <label for="key_benefits_section_card_hover_bg_color" class="form-label">Card Hover BG</label>
                        <input type="text" id="key_benefits_section_card_hover_bg_color" name="key_benefits_section_card_hover_bg_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['key_benefits_section_card_hover_bg_color']); ?>"
                            placeholder="#2563eb">
                    </div>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="key_benefits_section_card_text_color" class="form-label">Card Text Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="key_benefits_section_card_text_color" name="key_benefits_section_card_text_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['key_benefits_section_card_text_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="key_benefits_section_card_text_color_hex"
                                value="<?php echo htmlspecialchars($form_data['key_benefits_section_card_text_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="key_benefits_section_card_icon_color" class="form-label">Card Icon Color</label>
                        <input type="text" id="key_benefits_section_card_icon_color" name="key_benefits_section_card_icon_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['key_benefits_section_card_icon_color']); ?>"
                            placeholder="rgba(255,255,255,0.6)">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="key_benefits_cards" class="form-label">Key Benefits Cards (JSON - exactly 4 cards)</label>
                <textarea id="key_benefits_cards" name="key_benefits_cards" class="form-textarea form-textarea-code" rows="12" 
                    placeholder='[
  {"icon": "speed", "title": "Fast Processing", "description": "Lightning-fast performance with optimized workflows"},
  {"icon": "security", "title": "Secure & Reliable", "description": "Enterprise-grade security with 99.9% uptime"},
  {"icon": "money", "title": "Cost Effective", "description": "Reduce operational costs by up to 40%"},
  {"icon": "users", "title": "Easy to Use", "description": "Intuitive interface that teams love to use"}
]'><?php echo htmlspecialchars(json_encode($form_data['key_benefits_cards'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
                <p class="form-help">Icons: speed, security, money, users, check, globe, chart, clock. Title max 24 chars, description max 240 chars.</p>
            </div>
        </div>
        
        <!-- Feature Showcase Section (Stacking Cards) -->
        <div class="form-section">
            <h2 class="form-section-title">Feature Showcase Section (Stacking Cards)</h2>
            <p class="form-section-desc">A Razorpay-style section with up to 6 stacking cards showcasing key features. Each card has a navigation anchor, badge, heading, image, and feature list.</p>
            
            <div class="form-group">
                <div class="form-checkbox-group">
                    <label class="form-checkbox-label">
                        <input type="checkbox" id="feature_showcase_section_enabled" name="feature_showcase_section_enabled" 
                               class="form-checkbox" value="1"
                               <?php echo !empty($form_data['feature_showcase_section_enabled']) ? 'checked' : ''; ?>>
                        <span class="form-checkbox-text">
                            <strong>Enable Feature Showcase Section</strong>
                            <span class="form-checkbox-help">Show this section after the key benefits section</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Section Header</h3>
                <div class="form-group">
                    <label for="feature_showcase_section_title" class="form-label">Section Title</label>
                    <input type="text" id="feature_showcase_section_title" name="feature_showcase_section_title" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['feature_showcase_section_title']); ?>" maxlength="100"
                        placeholder="e.g., One solution. All business sizes.">
                    <p class="form-help">Max 100 characters</p>
                </div>
                <div class="form-group">
                    <label for="feature_showcase_section_subtitle" class="form-label">Section Subtitle</label>
                    <textarea id="feature_showcase_section_subtitle" name="feature_showcase_section_subtitle" class="form-textarea" rows="2"
                        maxlength="255" placeholder="e.g., From instant, self-serve payouts to custom integrations for enterprise scale operations"><?php echo htmlspecialchars($form_data['feature_showcase_section_subtitle']); ?></textarea>
                    <p class="form-help">Max 255 characters</p>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Section Colors</h3>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="feature_showcase_section_bg_color" class="form-label">Background Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="feature_showcase_section_bg_color" name="feature_showcase_section_bg_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_section_bg_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="feature_showcase_section_bg_color_hex"
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_section_bg_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="feature_showcase_section_title_color" class="form-label">Title Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="feature_showcase_section_title_color" name="feature_showcase_section_title_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_section_title_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="feature_showcase_section_title_color_hex"
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_section_title_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="feature_showcase_section_subtitle_color" class="form-label">Subtitle Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="feature_showcase_section_subtitle_color" name="feature_showcase_section_subtitle_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_section_subtitle_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="feature_showcase_section_subtitle_color_hex"
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_section_subtitle_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Card Styling</h3>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="feature_showcase_card_bg_color" class="form-label">Card Background</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="feature_showcase_card_bg_color" name="feature_showcase_card_bg_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_bg_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="feature_showcase_card_bg_color_hex"
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_bg_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="feature_showcase_card_border_color" class="form-label">Card Border</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="feature_showcase_card_border_color" name="feature_showcase_card_border_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_border_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="feature_showcase_card_border_color_hex"
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_border_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="feature_showcase_card_heading_color" class="form-label">Card Heading</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="feature_showcase_card_heading_color" name="feature_showcase_card_heading_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_heading_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="feature_showcase_card_heading_color_hex"
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_heading_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="feature_showcase_card_badge_bg_color" class="form-label">Badge Background</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="feature_showcase_card_badge_bg_color" name="feature_showcase_card_badge_bg_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_badge_bg_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="feature_showcase_card_badge_bg_color_hex"
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_badge_bg_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="feature_showcase_card_badge_text_color" class="form-label">Badge Text</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="feature_showcase_card_badge_text_color" name="feature_showcase_card_badge_text_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_badge_text_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="feature_showcase_card_badge_text_color_hex"
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_badge_text_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="feature_showcase_card_text_color" class="form-label">Card Text</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="feature_showcase_card_text_color" name="feature_showcase_card_text_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_text_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="feature_showcase_card_text_color_hex"
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_text_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="feature_showcase_card_icon_color" class="form-label">Checkmark Icon Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="feature_showcase_card_icon_color" name="feature_showcase_card_icon_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_icon_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="feature_showcase_card_icon_color_hex"
                                value="<?php echo htmlspecialchars($form_data['feature_showcase_card_icon_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="feature_showcase_cards" class="form-label">Feature Showcase Cards (JSON - max 6 cards)</label>
                <textarea id="feature_showcase_cards" name="feature_showcase_cards" class="form-textarea form-textarea-code" rows="20" 
                    placeholder='[
  {
    "nav_label": "Dashboard Payouts",
    "badge": "QUICK SINGLE PAYOUTS",
    "heading": "Keep it simple with dashboard payouts",
    "image_url": "https://example.com/image1.png",
    "features": [
      "Categorise payouts at source",
      "Enrich context with attachments & notes",
      "No cooling period for new beneficiaries"
    ]
  },
  {
    "nav_label": "Bulk Payouts",
    "badge": "MAKING PAYOUTS AT SCALE?",
    "heading": "Experience error-free file uploads",
    "image_url": "https://example.com/image2.png",
    "features": [
      "Smart bulk templates, catch errors at source",
      "Supports 50000 payouts in a single file",
      "One-click batch approvals with audit trail"
    ]
  }
]'><?php echo htmlspecialchars(json_encode($form_data['feature_showcase_cards'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
                <p class="form-help">Each card requires: nav_label (for sticky nav), badge (uppercase label), heading, image_url, and features array (3 bullet points recommended). Maximum 6 cards.</p>
            </div>
        </div>
        
        <!-- Additional Content Sections -->
        <div class="form-section">
            <h2 class="form-section-title">Additional Content Sections</h2>
            <p class="form-section-desc">Screenshots carousel and FAQs displayed after the feature showcase section.</p>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Screenshots Carousel</h3>
                <div class="form-group">
                    <label for="screenshots" class="form-label">Screenshots</label>
                    <textarea id="screenshots" name="screenshots" class="form-textarea" rows="4" 
                        placeholder="Enter one image URL per line"><?php echo htmlspecialchars(implode("\n", $form_data['screenshots'])); ?></textarea>
                    <p class="form-help">Enter one image URL per line</p>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">FAQs Section</h3>
                <div class="form-group">
                    <label for="faqs" class="form-label">FAQs (JSON)</label>
                    <textarea id="faqs" name="faqs" class="form-textarea form-textarea-code" rows="8" 
                        placeholder='[{"question": "What is this?", "answer": "This is..."}]'><?php echo htmlspecialchars(json_encode($form_data['faqs'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
                    <p class="form-help">Enter FAQs in JSON format</p>
                </div>
            </div>
        </div>
        
        <!-- CTA Banner Section (Image with Overlay) -->
        <div class="form-section">
            <h2 class="form-section-title">CTA Banner Section (Image with Overlay)</h2>
            <p class="form-section-desc">A full-width banner with background image, overlay, two heading lines, and a CTA button.</p>
            
            <div class="form-group">
                <div class="form-checkbox-group">
                    <label class="form-checkbox-label">
                        <input type="checkbox" id="cta_banner_enabled" name="cta_banner_enabled" 
                               class="form-checkbox" value="1"
                               <?php echo !empty($form_data['cta_banner_enabled']) ? 'checked' : ''; ?>>
                        <span class="form-checkbox-text">
                            <strong>Enable CTA Banner Section</strong>
                            <span class="form-checkbox-help">Show this banner section after the screenshots carousel</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Background Image & Overlay</h3>
                <div class="form-group">
                    <label for="cta_banner_image_url" class="form-label">Background Image URL</label>
                    <div class="image-input-group">
                        <input type="url" id="cta_banner_image_url" name="cta_banner_image_url" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['cta_banner_image_url']); ?>"
                            placeholder="https://example.com/banner-image.jpg">
                        <?php if (!empty($form_data['cta_banner_image_url'])): ?>
                            <div class="image-preview image-preview-large">
                                <img src="<?php echo htmlspecialchars($form_data['cta_banner_image_url']); ?>" alt="Banner preview">
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="form-help">Recommended: 1400x500 pixels. Leave empty to use default image.</p>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="cta_banner_overlay_color" class="form-label">Overlay Color</label>
                        <input type="text" id="cta_banner_overlay_color" name="cta_banner_overlay_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['cta_banner_overlay_color']); ?>"
                            placeholder="rgba(0,0,0,0.5)">
                        <p class="form-help">Use rgba format for transparency, e.g., rgba(0,0,0,0.5)</p>
                    </div>
                    <div class="form-group">
                        <label for="cta_banner_overlay_intensity" class="form-label">Overlay Intensity</label>
                        <input type="range" id="cta_banner_overlay_intensity" name="cta_banner_overlay_intensity" 
                            class="form-range" min="0" max="1" step="0.05"
                            value="<?php echo htmlspecialchars($form_data['cta_banner_overlay_intensity']); ?>">
                        <p class="form-help">Opacity: <span id="cta_overlay_intensity_value"><?php echo number_format($form_data['cta_banner_overlay_intensity'] * 100, 0); ?>%</span></p>
                    </div>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Heading Text</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="cta_banner_heading1" class="form-label">Heading Line 1</label>
                        <input type="text" id="cta_banner_heading1" name="cta_banner_heading1" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['cta_banner_heading1']); ?>" maxlength="100"
                            placeholder="e.g., Streamline across 30+ modules.">
                        <p class="form-help">Max 100 characters</p>
                    </div>
                    <div class="form-group">
                        <label for="cta_banner_heading2" class="form-label">Heading Line 2</label>
                        <input type="text" id="cta_banner_heading2" name="cta_banner_heading2" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['cta_banner_heading2']); ?>" maxlength="100"
                            placeholder="e.g., Transform your business today!">
                        <p class="form-help">Max 100 characters</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cta_banner_heading_color" class="form-label">Heading Color</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="cta_banner_heading_color" name="cta_banner_heading_color" class="form-color-input" 
                            value="<?php echo htmlspecialchars($form_data['cta_banner_heading_color']); ?>">
                        <input type="text" class="form-input color-hex-input" id="cta_banner_heading_color_hex"
                            value="<?php echo htmlspecialchars($form_data['cta_banner_heading_color']); ?>" maxlength="7">
                    </div>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">CTA Button</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="cta_banner_button_text" class="form-label">Button Text</label>
                        <input type="text" id="cta_banner_button_text" name="cta_banner_button_text" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['cta_banner_button_text']); ?>" maxlength="50"
                            placeholder="e.g., Explore ERP Solutions">
                        <p class="form-help">Max 50 characters</p>
                    </div>
                    <div class="form-group">
                        <label for="cta_banner_button_link" class="form-label">Button Link</label>
                        <input type="text" id="cta_banner_button_link" name="cta_banner_button_link" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['cta_banner_button_link']); ?>"
                            placeholder="#contact-form">
                        <p class="form-help">Use #contact-form to scroll to contact section, or enter a URL</p>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="cta_banner_button_bg_color" class="form-label">Button Background Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="cta_banner_button_bg_color" name="cta_banner_button_bg_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['cta_banner_button_bg_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="cta_banner_button_bg_color_hex"
                                value="<?php echo htmlspecialchars($form_data['cta_banner_button_bg_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cta_banner_button_text_color" class="form-label">Button Text Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="cta_banner_button_text_color" name="cta_banner_button_text_color" class="form-color-input" 
                                value="<?php echo htmlspecialchars($form_data['cta_banner_button_text_color']); ?>">
                            <input type="text" class="form-input color-hex-input" id="cta_banner_button_text_color_hex"
                                value="<?php echo htmlspecialchars($form_data['cta_banner_button_text_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CTA Section & SEO Settings -->
        <div class="form-section">
            <h2 class="form-section-title">CTA Section & SEO Settings</h2>
            <p class="form-section-desc">Contact form section customization and search engine optimization settings.</p>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">Contact Form Section (Final CTA)</h3>
                <div class="form-group">
                    <label for="pricing_note" class="form-label">CTA Section Note</label>
                    <textarea id="pricing_note" name="pricing_note" class="form-textarea" rows="2"
                        placeholder="Custom text for the contact form section"><?php echo htmlspecialchars($form_data['pricing_note']); ?></textarea>
                    <p class="form-help">Custom subtitle for the contact form section</p>
                </div>
            </div>
            
            <div class="form-subsection">
                <h3 class="form-subsection-title">SEO Meta Tags</h3>
                <div class="form-group">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['meta_title']); ?>" maxlength="255"
                        placeholder="Leave empty to use solution name">
                    <p class="form-help">SEO title for search engines</p>
                </div>
                
                <div class="form-group">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-textarea" rows="2"
                        placeholder="SEO description for search engines"><?php echo htmlspecialchars($form_data['meta_description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                    <input type="text" id="meta_keywords" name="meta_keywords" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['meta_keywords']); ?>" maxlength="500"
                        placeholder="keyword1, keyword2, keyword3">
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Solution</button>
            <a href="<?php echo get_app_base_url(); ?>/admin/solutions.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<form id="deleteForm" method="POST" action="<?php echo get_app_base_url(); ?>/admin/solutions/delete.php" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($solution_id); ?>">
</form>

<style>
.admin-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 14px; margin-bottom: 8px; }
.admin-breadcrumb a { color: var(--color-primary); text-decoration: none; }
.breadcrumb-separator { color: var(--color-gray-400); }
.admin-page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 16px; }
.admin-page-header-content { flex: 1; }
.admin-page-title { font-size: 24px; font-weight: 700; color: var(--color-gray-900); margin: 0 0 8px 0; }
.admin-page-description { font-size: 14px; color: var(--color-gray-600); margin: 0; }
.admin-page-header-actions { display: flex; gap: 12px; }
.alert { padding: 16px; border-radius: 8px; margin-bottom: 24px; }
.alert-error { background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
.alert ul { margin: 8px 0 0 16px; padding: 0; }
.admin-form { padding: 24px; }
.form-section { margin-bottom: 32px; }
.form-section:last-of-type { margin-bottom: 0; }
.form-section-title { font-size: 18px; font-weight: 600; color: var(--color-gray-900); margin: 0 0 16px 0; padding-bottom: 12px; border-bottom: 1px solid var(--color-gray-200); }
.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 14px; font-weight: 600; color: var(--color-gray-700); margin-bottom: 8px; }
.form-label.required::after { content: ' *'; color: #dc2626; }
.form-input, .form-select, .form-textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--color-gray-300); border-radius: 6px; font-size: 14px; color: var(--color-gray-900); font-family: inherit; box-sizing: border-box; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.form-textarea { resize: vertical; }
.form-textarea-code { font-family: 'Courier New', Consolas, monospace; font-size: 13px; }
.form-help { font-size: 12px; color: var(--color-gray-500); margin: 4px 0 0 0; }
.form-actions { display: flex; gap: 12px; padding-top: 24px; border-top: 1px solid var(--color-gray-200); }
.form-checkbox-group { padding: var(--spacing-4); background-color: var(--color-gray-50); border-radius: var(--radius-md); border: 1px solid var(--color-gray-200); }
.form-checkbox-label { display: flex; align-items: flex-start; gap: var(--spacing-3); cursor: pointer; }
.form-checkbox { width: 20px; height: 20px; margin-top: 2px; cursor: pointer; }
.form-checkbox-text { flex: 1; }
.form-checkbox-text strong { display: block; font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold); color: var(--color-gray-900); margin-bottom: var(--spacing-1); }
.form-checkbox-help { display: block; font-size: var(--font-size-xs); color: var(--color-gray-600); }
.image-input-group { display: flex; flex-direction: column; gap: 12px; }
.image-preview { width: 80px; height: 80px; border: 1px solid var(--color-gray-300); border-radius: 8px; overflow: hidden; background: var(--color-gray-50); display: flex; align-items: center; justify-content: center; }
.image-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
.image-preview-large { width: 200px; height: 150px; }
.form-input-color { width: 60px; height: 40px; padding: 4px; cursor: pointer; }
.form-section-desc { font-size: 14px; color: var(--color-gray-500); margin: -8px 0 16px 0; }
.form-subsection { background: var(--color-gray-50); border-radius: 8px; padding: 16px; margin-bottom: 16px; border: 1px solid var(--color-gray-200); }
.form-subsection-title { font-size: 15px; font-weight: 600; color: var(--color-gray-800); margin: 0 0 12px 0; }
.form-row-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.color-input-wrapper { display: flex; gap: 8px; align-items: center; }
.form-color-input { width: 50px; height: 40px; padding: 2px; border: 1px solid var(--color-gray-300); border-radius: 6px; cursor: pointer; }
.color-hex-input { flex: 1; font-family: monospace; text-transform: uppercase; }
.form-range { width: 100%; height: 8px; border-radius: 4px; background: var(--color-gray-200); outline: none; -webkit-appearance: none; }
.form-range::-webkit-slider-thumb { -webkit-appearance: none; width: 20px; height: 20px; border-radius: 50%; background: var(--color-primary); cursor: pointer; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
.form-range::-moz-range-thumb { width: 20px; height: 20px; border-radius: 50%; background: var(--color-primary); cursor: pointer; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
@media (max-width: 768px) { .admin-page-header { flex-direction: column; } .form-row { grid-template-columns: 1fr; } .form-row-3 { grid-template-columns: 1fr; } }
</style>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this solution? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

// Character counter for hero title
document.getElementById('hero_title_text')?.addEventListener('input', function() {
    document.getElementById('hero_title_char_count').textContent = this.value.length;
});

// Character counters for key benefits section
document.getElementById('key_benefits_section_heading1')?.addEventListener('input', function() {
    document.getElementById('key_benefits_heading1_count').textContent = this.value.length;
});
document.getElementById('key_benefits_section_heading2')?.addEventListener('input', function() {
    document.getElementById('key_benefits_heading2_count').textContent = this.value.length;
});
document.getElementById('key_benefits_section_subheading')?.addEventListener('input', function() {
    document.getElementById('key_benefits_subheading_count').textContent = this.value.length;
});

// Color input sync
document.querySelectorAll('.form-color-input').forEach(colorInput => {
    const hexInput = document.getElementById(colorInput.id + '_hex');
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

// Range slider value display
document.getElementById('hero_bg_gradient_opacity')?.addEventListener('input', function() {
    document.getElementById('gradient_opacity_value').textContent = Math.round(this.value * 100) + '%';
});

document.getElementById('hero_bg_pattern_opacity')?.addEventListener('input', function() {
    document.getElementById('pattern_opacity_value').textContent = (this.value * 100).toFixed(1) + '%';
});

// CTA Banner overlay intensity slider
document.getElementById('cta_banner_overlay_intensity')?.addEventListener('input', function() {
    document.getElementById('cta_overlay_intensity_value').textContent = Math.round(this.value * 100) + '%';
});
</script>

<?php include_admin_footer(); ?>
