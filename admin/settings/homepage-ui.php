<?php
/**
 * Admin Homepage UI Settings Page
 * Manage UI components on the homepage including Powerful Solutions section
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';
require_once __DIR__ . '/../../includes/template_helpers.php';

use Karyalay\Models\Setting;
use Karyalay\Middleware\CsrfMiddleware;

// Start secure session
startSecureSession();

// Require admin authentication and settings.general permission
require_admin();
require_permission('settings.general');

// Initialize services
$settingModel = new Setting();
$csrfMiddleware = new CsrfMiddleware();

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$csrfMiddleware->validate()) {
        $error_message = 'Invalid security token. Please try again.';
    } else {
        try {
            // Validate color formats
            $colorFields = [
                'solutions_section_bg_start', 'solutions_section_bg_mid', 'solutions_section_bg_end',
                'solutions_title_color', 'solutions_subtitle_color',
                'solutions_nav_bg', 'solutions_nav_border', 'solutions_nav_link_color',
                'solutions_nav_link_hover', 'solutions_nav_link_active', 'solutions_nav_active_border',
                'solutions_card_bg', 'solutions_card_border', 'solutions_badge_bg', 'solutions_badge_text',
                'solutions_heading_color', 'solutions_description_color', 'solutions_feature_color',
                'solutions_cta_color', 'solutions_cta_hover',
                'solutions_more_btn_bg_start', 'solutions_more_btn_bg_end', 'solutions_more_btn_text',
                'solutions_more_btn_hover_start', 'solutions_more_btn_hover_end',
                // Why Choose section colors
                'why_choose_section_bg', 'why_choose_title_color', 'why_choose_subtitle_color',
                'why_choose_card_bg', 'why_choose_card_title_color', 'why_choose_card_desc_color',
                // Success Stories section colors
                'success_stories_title_color', 'success_stories_subtitle_color',
                'success_stories_card_overlay', 'success_stories_card_title', 'success_stories_card_industry',
                'success_stories_card_desc', 'success_stories_card_badge_bg', 'success_stories_card_badge_text',
                'success_stories_card_btn_bg', 'success_stories_card_btn_text',
                'success_stories_view_all_bg', 'success_stories_view_all_text',
                'success_stories_view_all_icon_bg', 'success_stories_view_all_icon_hover_bg',
                // Blog section colors
                'blog_section_bg_start', 'blog_section_bg_end',
                'blog_title_color', 'blog_subtitle_color',
                'blog_card_bg', 'blog_card_border', 'blog_card_title_color', 'blog_card_excerpt_color',
                'blog_card_date_color', 'blog_card_tag_bg', 'blog_card_tag_text',
                'blog_card_link_color', 'blog_card_link_hover',
                'blog_view_all_bg', 'blog_view_all_border', 'blog_view_all_text', 'blog_view_all_icon_bg',
                // Testimonials section colors
                'homepage_testimonials_accent_color',
                // CTA Form section colors
                'homepage_cta_form_accent_color', 'homepage_cta_form_btn_gradient_start', 'homepage_cta_form_btn_gradient_end',
                'homepage_cta_form_badge_bg', 'homepage_cta_form_icon_bg'
            ];

            foreach ($colorFields as $field) {
                $value = trim($_POST[$field] ?? '');
                if (!empty($value) && !preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
                    throw new Exception("Invalid color format for {$field}. Use hex format (e.g., #3b82f6)");
                }
            }

            // Save text settings
            $settingModel->set('solutions_section_title', trim($_POST['solutions_section_title'] ?? 'Powerful Solutions'));
            $settingModel->set('solutions_section_subtitle', trim($_POST['solutions_section_subtitle'] ?? ''));

            // Save color settings
            foreach ($colorFields as $field) {
                $value = trim($_POST[$field] ?? '');
                if (!empty($value)) {
                    $settingModel->set($field, $value);
                }
            }

            // Save Why Choose section settings
            $settingModel->set('why_choose_section_title', trim($_POST['why_choose_section_title'] ?? 'Why Choose Karyalay?'));
            $settingModel->set('why_choose_section_subtitle', trim($_POST['why_choose_section_subtitle'] ?? ''));

            // Save Success Stories section settings
            $settingModel->set('success_stories_enabled', isset($_POST['success_stories_enabled']) ? '1' : '0');
            $settingModel->set('success_stories_section_title', trim($_POST['success_stories_section_title'] ?? 'Success Stories'));
            $settingModel->set('success_stories_section_subtitle', trim($_POST['success_stories_section_subtitle'] ?? ''));
            $settingModel->set('success_stories_bg_gradient_start', trim($_POST['success_stories_bg_gradient_start'] ?? '#1a1a2e'));
            $settingModel->set('success_stories_bg_gradient_end', trim($_POST['success_stories_bg_gradient_end'] ?? '#16213e'));

            // Save Blog section settings
            $settingModel->set('blog_section_enabled', isset($_POST['blog_section_enabled']) ? '1' : '0');
            $settingModel->set('blog_section_title', trim($_POST['blog_section_title'] ?? 'Latest from Our Blog'));
            $settingModel->set('blog_section_subtitle', trim($_POST['blog_section_subtitle'] ?? ''));
            $settingModel->set('blog_section_theme', in_array($_POST['blog_section_theme'] ?? 'light', ['light', 'dark']) ? $_POST['blog_section_theme'] : 'light');

            // Save CTA Banner settings
            $settingModel->set('homepage_cta_banner_enabled', isset($_POST['homepage_cta_banner_enabled']) ? '1' : '0');
            $settingModel->set('homepage_cta_banner_image_url', trim($_POST['homepage_cta_banner_image_url'] ?? ''));
            $settingModel->set('homepage_cta_banner_overlay_color', trim($_POST['homepage_cta_banner_overlay_color'] ?? 'rgba(0,0,0,0.5)'));
            $settingModel->set('homepage_cta_banner_overlay_intensity', floatval($_POST['homepage_cta_banner_overlay_intensity'] ?? 0.5));
            $settingModel->set('homepage_cta_banner_heading1', substr(trim($_POST['homepage_cta_banner_heading1'] ?? ''), 0, 100));
            $settingModel->set('homepage_cta_banner_heading2', substr(trim($_POST['homepage_cta_banner_heading2'] ?? ''), 0, 100));
            $settingModel->set('homepage_cta_banner_heading_color', trim($_POST['homepage_cta_banner_heading_color'] ?? '#FFFFFF'));
            $settingModel->set('homepage_cta_banner_button_text', substr(trim($_POST['homepage_cta_banner_button_text'] ?? ''), 0, 50));
            $settingModel->set('homepage_cta_banner_button_link', trim($_POST['homepage_cta_banner_button_link'] ?? '#contact-form'));
            $settingModel->set('homepage_cta_banner_button_bg_color', trim($_POST['homepage_cta_banner_button_bg_color'] ?? '#FFFFFF'));
            $settingModel->set('homepage_cta_banner_button_text_color', trim($_POST['homepage_cta_banner_button_text_color'] ?? '#2563eb'));

            // Save Homepage Testimonials Section settings
            $settingModel->set('homepage_testimonials_enabled', isset($_POST['homepage_testimonials_enabled']) ? '1' : '0');
            $settingModel->set('homepage_testimonials_theme', in_array($_POST['homepage_testimonials_theme'] ?? 'light', ['light', 'dark']) ? $_POST['homepage_testimonials_theme'] : 'light');
            $settingModel->set('homepage_testimonials_heading', substr(trim($_POST['homepage_testimonials_heading'] ?? ''), 0, 48));
            $settingModel->set('homepage_testimonials_subheading', substr(trim($_POST['homepage_testimonials_subheading'] ?? ''), 0, 120));

            // Save Homepage FAQ Section settings
            $settingModel->set('homepage_faq_enabled', isset($_POST['homepage_faq_enabled']) ? '1' : '0');
            $settingModel->set('homepage_faq_theme', in_array($_POST['homepage_faq_theme'] ?? 'light', ['light', 'dark']) ? $_POST['homepage_faq_theme'] : 'light');
            $settingModel->set('homepage_faq_heading', substr(trim($_POST['homepage_faq_heading'] ?? ''), 0, 48));
            $settingModel->set('homepage_faq_subheading', substr(trim($_POST['homepage_faq_subheading'] ?? ''), 0, 120));
            $settingModel->set('homepage_faq_items', trim($_POST['homepage_faq_items'] ?? '[]'));

            // Save Homepage CTA Form Section settings
            $settingModel->set('homepage_cta_form_enabled', isset($_POST['homepage_cta_form_enabled']) ? '1' : '0');
            $settingModel->set('homepage_cta_form_theme', in_array($_POST['homepage_cta_form_theme'] ?? 'dark', ['light', 'dark']) ? $_POST['homepage_cta_form_theme'] : 'dark');
            $settingModel->set('homepage_cta_form_title', substr(trim($_POST['homepage_cta_form_title'] ?? ''), 0, 60));
            $settingModel->set('homepage_cta_form_subtitle', substr(trim($_POST['homepage_cta_form_subtitle'] ?? ''), 0, 150));
            $settingModel->set('homepage_cta_form_badge_text', substr(trim($_POST['homepage_cta_form_badge_text'] ?? ''), 0, 40));
            $settingModel->set('homepage_cta_form_header', substr(trim($_POST['homepage_cta_form_header'] ?? ''), 0, 40));
            $settingModel->set('homepage_cta_form_header_subtitle', substr(trim($_POST['homepage_cta_form_header_subtitle'] ?? ''), 0, 60));
            $settingModel->set('homepage_cta_form_submit_text', substr(trim($_POST['homepage_cta_form_submit_text'] ?? ''), 0, 30));
            $settingModel->set('homepage_cta_form_privacy_text', substr(trim($_POST['homepage_cta_form_privacy_text'] ?? ''), 0, 80));

            $success_message = 'Homepage UI settings saved successfully!';
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
}

// Fetch current settings with defaults
$settings = $settingModel->getMultiple([
    'solutions_section_title', 'solutions_section_subtitle',
    'solutions_section_bg_start', 'solutions_section_bg_mid', 'solutions_section_bg_end',
    'solutions_title_color', 'solutions_subtitle_color',
    'solutions_nav_bg', 'solutions_nav_border', 'solutions_nav_link_color',
    'solutions_nav_link_hover', 'solutions_nav_link_active', 'solutions_nav_active_border',
    'solutions_card_bg', 'solutions_card_border', 'solutions_badge_bg', 'solutions_badge_text',
    'solutions_heading_color', 'solutions_description_color', 'solutions_feature_color',
    'solutions_cta_color', 'solutions_cta_hover',
    'solutions_more_btn_bg_start', 'solutions_more_btn_bg_end', 'solutions_more_btn_text',
    'solutions_more_btn_hover_start', 'solutions_more_btn_hover_end',
    // Why Choose section settings
    'why_choose_section_title', 'why_choose_section_subtitle',
    'why_choose_section_bg', 'why_choose_title_color', 'why_choose_subtitle_color',
    'why_choose_card_bg', 'why_choose_card_title_color', 'why_choose_card_desc_color',
    // Success Stories section settings
    'success_stories_enabled', 'success_stories_section_title', 'success_stories_section_subtitle',
    'success_stories_bg_gradient_start', 'success_stories_bg_gradient_end',
    'success_stories_title_color', 'success_stories_subtitle_color',
    'success_stories_card_overlay', 'success_stories_card_title', 'success_stories_card_industry',
    'success_stories_card_desc', 'success_stories_card_badge_bg', 'success_stories_card_badge_text',
    'success_stories_card_btn_bg', 'success_stories_card_btn_text',
    'success_stories_view_all_bg', 'success_stories_view_all_text',
    'success_stories_view_all_icon_bg', 'success_stories_view_all_icon_hover_bg',
    // Blog section settings
    'blog_section_enabled', 'blog_section_title', 'blog_section_subtitle', 'blog_section_theme',
    'blog_section_bg_start', 'blog_section_bg_end',
    'blog_title_color', 'blog_subtitle_color',
    'blog_card_bg', 'blog_card_border', 'blog_card_title_color', 'blog_card_excerpt_color',
    'blog_card_date_color', 'blog_card_tag_bg', 'blog_card_tag_text',
    'blog_card_link_color', 'blog_card_link_hover',
    'blog_view_all_bg', 'blog_view_all_border', 'blog_view_all_text', 'blog_view_all_icon_bg',
    // Homepage Testimonials settings
    'homepage_testimonials_enabled', 'homepage_testimonials_theme',
    'homepage_testimonials_heading', 'homepage_testimonials_subheading', 'homepage_testimonials_accent_color',
    // CTA Banner settings
    'homepage_cta_banner_enabled', 'homepage_cta_banner_image_url',
    'homepage_cta_banner_overlay_color', 'homepage_cta_banner_overlay_intensity',
    'homepage_cta_banner_heading1', 'homepage_cta_banner_heading2', 'homepage_cta_banner_heading_color',
    'homepage_cta_banner_button_text', 'homepage_cta_banner_button_link',
    'homepage_cta_banner_button_bg_color', 'homepage_cta_banner_button_text_color',
    // Homepage FAQ settings
    'homepage_faq_enabled', 'homepage_faq_theme', 'homepage_faq_heading', 
    'homepage_faq_subheading', 'homepage_faq_items',
    // Homepage CTA Form settings
    'homepage_cta_form_enabled', 'homepage_cta_form_theme',
    'homepage_cta_form_title', 'homepage_cta_form_subtitle', 'homepage_cta_form_badge_text',
    'homepage_cta_form_header', 'homepage_cta_form_header_subtitle', 'homepage_cta_form_submit_text',
    'homepage_cta_form_privacy_text', 'homepage_cta_form_accent_color',
    'homepage_cta_form_btn_gradient_start', 'homepage_cta_form_btn_gradient_end',
    'homepage_cta_form_badge_bg', 'homepage_cta_form_icon_bg'
]);

// Set defaults
$defaults = [
    'solutions_section_title' => 'Powerful Solutions',
    'solutions_section_subtitle' => 'Explore our comprehensive suite of business management solutions designed to streamline your operations',
    'solutions_section_bg_start' => '#f7fef9',
    'solutions_section_bg_mid' => '#f0fdf4',
    'solutions_section_bg_end' => '#dcfce7',
    'solutions_title_color' => '#111827',
    'solutions_subtitle_color' => '#6b7280',
    'solutions_nav_bg' => '#ffffff',
    'solutions_nav_border' => '#e5e7eb',
    'solutions_nav_link_color' => '#6b7280',
    'solutions_nav_link_hover' => '#111827',
    'solutions_nav_link_active' => '#111827',
    'solutions_nav_active_border' => '#10b981',
    'solutions_card_bg' => '#ffffff',
    'solutions_card_border' => '#e5e7eb',
    'solutions_badge_bg' => '#ecfdf5',
    'solutions_badge_text' => '#059669',
    'solutions_heading_color' => '#111827',
    'solutions_description_color' => '#6b7280',
    'solutions_feature_color' => '#374151',
    'solutions_cta_color' => '#10b981',
    'solutions_cta_hover' => '#059669',
    'solutions_more_btn_bg_start' => '#10b981',
    'solutions_more_btn_bg_end' => '#059669',
    'solutions_more_btn_text' => '#ffffff',
    'solutions_more_btn_hover_start' => '#059669',
    'solutions_more_btn_hover_end' => '#047857',
    // Why Choose section defaults
    'why_choose_section_title' => 'Why Choose Karyalay?',
    'why_choose_section_subtitle' => 'Everything you need to manage your business efficiently in one powerful platform',
    'why_choose_section_bg' => '#ffffff',
    'why_choose_title_color' => '#111827',
    'why_choose_subtitle_color' => '#6b7280',
    'why_choose_card_bg' => '#ffffff',
    'why_choose_card_title_color' => '#111827',
    'why_choose_card_desc_color' => '#6b7280',
    // Success Stories section defaults
    'success_stories_enabled' => '1',
    'success_stories_section_title' => 'Success Stories',
    'success_stories_section_subtitle' => 'See how businesses like yours are achieving remarkable results with our solutions',
    'success_stories_bg_gradient_start' => '#1a1a2e',
    'success_stories_bg_gradient_end' => '#16213e',
    'success_stories_title_color' => '#ffffff',
    'success_stories_subtitle_color' => '#b3b3b3',
    'success_stories_card_overlay' => '#000000',
    'success_stories_card_title' => '#ffffff',
    'success_stories_card_industry' => '#cccccc',
    'success_stories_card_desc' => '#e6e6e6',
    'success_stories_card_badge_bg' => '#333333',
    'success_stories_card_badge_text' => '#ffffff',
    'success_stories_card_btn_bg' => '#262626',
    'success_stories_card_btn_text' => '#ffffff',
    'success_stories_view_all_bg' => '#0d0d0d',
    'success_stories_view_all_text' => '#ffffff',
    'success_stories_view_all_icon_bg' => '#1a1a1a',
    'success_stories_view_all_icon_hover_bg' => '#333333',
    // Blog section defaults
    'blog_section_enabled' => '1',
    'blog_section_title' => 'Latest from Our Blog',
    'blog_section_subtitle' => 'Stay updated with the latest insights, tips, and news from our team',
    'blog_section_theme' => 'light',
    'blog_section_bg_start' => '#f8fafc',
    'blog_section_bg_end' => '#f1f5f9',
    'blog_title_color' => '#0f172a',
    'blog_subtitle_color' => '#64748b',
    'blog_card_bg' => '#ffffff',
    'blog_card_border' => '#e2e8f0',
    'blog_card_title_color' => '#0f172a',
    'blog_card_excerpt_color' => '#64748b',
    'blog_card_date_color' => '#a855f7',
    'blog_card_tag_bg' => '#f3e8ff',
    'blog_card_tag_text' => '#9333ea',
    'blog_card_link_color' => '#a855f7',
    'blog_card_link_hover' => '#9333ea',
    'blog_view_all_bg' => '#faf5ff',
    'blog_view_all_border' => '#a855f7',
    'blog_view_all_text' => '#0f172a',
    'blog_view_all_icon_bg' => '#f3e8ff',
    // Homepage Testimonials defaults
    'homepage_testimonials_enabled' => '1',
    'homepage_testimonials_theme' => 'light',
    'homepage_testimonials_heading' => 'What Our Customers Say',
    'homepage_testimonials_subheading' => 'Don\'t just take our word for it - hear from businesses that trust us',
    'homepage_testimonials_accent_color' => '#10b981',
    // CTA Banner defaults
    'homepage_cta_banner_enabled' => '1',
    'homepage_cta_banner_image_url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1400&h=500&fit=crop',
    'homepage_cta_banner_overlay_color' => 'rgba(0,0,0,0.5)',
    'homepage_cta_banner_overlay_intensity' => '0.5',
    'homepage_cta_banner_heading1' => 'Ready to transform your business?',
    'homepage_cta_banner_heading2' => 'Get started with our powerful solutions today!',
    'homepage_cta_banner_heading_color' => '#FFFFFF',
    'homepage_cta_banner_button_text' => 'Get Started Now',
    'homepage_cta_banner_button_link' => '#contact-form',
    'homepage_cta_banner_button_bg_color' => '#FFFFFF',
    'homepage_cta_banner_button_text_color' => '#2563eb',
    // Homepage FAQ defaults
    'homepage_faq_enabled' => '1',
    'homepage_faq_theme' => 'light',
    'homepage_faq_heading' => 'Frequently Asked Questions',
    'homepage_faq_subheading' => 'Everything you need to know about our platform. Can\'t find what you\'re looking for? Feel free to contact us.',
    'homepage_faq_items' => '[{"question":"What is Karyalay?","answer":"Karyalay is a comprehensive business management platform that helps you streamline operations, manage customers, handle subscriptions, and grow your business efficiently."},{"question":"How do I get started?","answer":"Getting started is easy! Simply register for an account, choose your plan, and our team will help you set up your workspace. You can also request a demo to see the platform in action."},{"question":"Is there a free trial available?","answer":"Yes! We offer a 14-day free trial with full access to all features. No credit card required to start your trial."},{"question":"What kind of support do you offer?","answer":"We provide 24/7 customer support via email, chat, and phone. Our dedicated support team is always ready to help you with any questions or issues."}]',
    // Homepage CTA Form defaults
    'homepage_cta_form_enabled' => '1',
    'homepage_cta_form_theme' => 'dark',
    'homepage_cta_form_title' => 'Ready to Transform Your Business?',
    'homepage_cta_form_subtitle' => 'Get in touch with us today and discover how we can streamline your operations',
    'homepage_cta_form_badge_text' => 'Trusted by 500+ Businesses',
    'homepage_cta_form_header' => 'Get Started Today',
    'homepage_cta_form_header_subtitle' => 'Fill out the form and we\'ll get back to you shortly',
    'homepage_cta_form_submit_text' => 'Send Message',
    'homepage_cta_form_privacy_text' => 'Your information is secure and will never be shared',
    'homepage_cta_form_accent_color' => '#10b981',
    'homepage_cta_form_btn_gradient_start' => '#10b981',
    'homepage_cta_form_btn_gradient_end' => '#059669',
    'homepage_cta_form_badge_bg' => '#10b981',
    'homepage_cta_form_icon_bg' => '#ffffff'
];

foreach ($defaults as $key => $default) {
    if (!isset($settings[$key]) || $settings[$key] === '') {
        $settings[$key] = $default;
    }
}

// Generate CSRF token
$csrf_token = getCsrfToken();

// Include admin header
include_admin_header('Homepage UI Settings');
?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <h1 class="admin-page-title">Homepage UI Settings</h1>
        <p class="admin-page-description">Customize the appearance of homepage components</p>
    </div>
</div>

<?php $base_url = get_app_base_url(); ?>
<!-- Settings Navigation -->
<div class="settings-nav">
    <a href="<?php echo $base_url; ?>/admin/settings/general.php" class="settings-nav-item">General</a>
    <a href="<?php echo $base_url; ?>/admin/settings/branding.php" class="settings-nav-item">Branding</a>
    <a href="<?php echo $base_url; ?>/admin/settings/seo.php" class="settings-nav-item">SEO</a>
    <a href="<?php echo $base_url; ?>/admin/settings/legal-identity.php" class="settings-nav-item">Legal Identity</a>
    <a href="<?php echo $base_url; ?>/admin/settings/homepage-ui.php" class="settings-nav-item active">Homepage UI</a>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<!-- Homepage UI Settings Form -->
<div class="admin-card">
    <form method="POST" action="<?php echo $base_url; ?>/admin/settings/homepage-ui.php" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <!-- Powerful Solutions Section -->
        <div class="form-section">
            <h3 class="form-section-title">🚀 Powerful Solutions Section</h3>
            <p class="form-section-description">Customize the heading texts and colors for the Powerful Solutions section on the homepage.</p>
            
            <!-- Text Settings -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Section Headings</h4>
                
                <div class="form-group">
                    <label for="solutions_section_title" class="form-label">Section Title</label>
                    <input type="text" id="solutions_section_title" name="solutions_section_title" class="form-input" 
                           value="<?php echo htmlspecialchars($settings['solutions_section_title']); ?>" maxlength="100">
                    <p class="form-help">Main heading displayed at the top of the section</p>
                </div>
                
                <div class="form-group">
                    <label for="solutions_section_subtitle" class="form-label">Section Subtitle</label>
                    <textarea id="solutions_section_subtitle" name="solutions_section_subtitle" class="form-input form-textarea" 
                              rows="2" maxlength="300"><?php echo htmlspecialchars($settings['solutions_section_subtitle']); ?></textarea>
                    <p class="form-help">Descriptive text below the main heading</p>
                </div>
            </div>
            
            <!-- Background Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Section Background (Gradient)</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="solutions_section_bg_start" class="form-label">Gradient Start</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_section_bg_start" name="solutions_section_bg_start" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_section_bg_start']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_section_bg_start"
                                   value="<?php echo htmlspecialchars($settings['solutions_section_bg_start']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_section_bg_mid" class="form-label">Gradient Middle</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_section_bg_mid" name="solutions_section_bg_mid" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_section_bg_mid']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_section_bg_mid"
                                   value="<?php echo htmlspecialchars($settings['solutions_section_bg_mid']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_section_bg_end" class="form-label">Gradient End</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_section_bg_end" name="solutions_section_bg_end" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_section_bg_end']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_section_bg_end"
                                   value="<?php echo htmlspecialchars($settings['solutions_section_bg_end']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Title Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Title Colors</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="solutions_title_color" class="form-label">Title Color</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_title_color" name="solutions_title_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_title_color']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_title_color"
                                   value="<?php echo htmlspecialchars($settings['solutions_title_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_subtitle_color" class="form-label">Subtitle Color</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_subtitle_color" name="solutions_subtitle_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_subtitle_color']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_subtitle_color"
                                   value="<?php echo htmlspecialchars($settings['solutions_subtitle_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Strip Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Navigation Strip</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="solutions_nav_bg" class="form-label">Background</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_nav_bg" name="solutions_nav_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_bg']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_nav_bg"
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_bg']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_nav_border" class="form-label">Border Color</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_nav_border" name="solutions_nav_border" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_border']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_nav_border"
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_border']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_nav_link_color" class="form-label">Link Color</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_nav_link_color" name="solutions_nav_link_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_link_color']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_nav_link_color"
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_link_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="solutions_nav_link_hover" class="form-label">Link Hover</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_nav_link_hover" name="solutions_nav_link_hover" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_link_hover']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_nav_link_hover"
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_link_hover']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_nav_link_active" class="form-label">Active Link</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_nav_link_active" name="solutions_nav_link_active" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_link_active']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_nav_link_active"
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_link_active']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_nav_active_border" class="form-label">Active Border</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_nav_active_border" name="solutions_nav_active_border" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_active_border']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_nav_active_border"
                                   value="<?php echo htmlspecialchars($settings['solutions_nav_active_border']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Solution Cards</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="solutions_card_bg" class="form-label">Card Background</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_card_bg" name="solutions_card_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_card_bg']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_card_bg"
                                   value="<?php echo htmlspecialchars($settings['solutions_card_bg']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_card_border" class="form-label">Card Border</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_card_border" name="solutions_card_border" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_card_border']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_card_border"
                                   value="<?php echo htmlspecialchars($settings['solutions_card_border']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_heading_color" class="form-label">Card Heading</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_heading_color" name="solutions_heading_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_heading_color']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_heading_color"
                                   value="<?php echo htmlspecialchars($settings['solutions_heading_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="solutions_description_color" class="form-label">Description</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_description_color" name="solutions_description_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_description_color']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_description_color"
                                   value="<?php echo htmlspecialchars($settings['solutions_description_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_feature_color" class="form-label">Feature Text</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_feature_color" name="solutions_feature_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_feature_color']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_feature_color"
                                   value="<?php echo htmlspecialchars($settings['solutions_feature_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_badge_bg" class="form-label">Badge Background</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_badge_bg" name="solutions_badge_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_badge_bg']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_badge_bg"
                                   value="<?php echo htmlspecialchars($settings['solutions_badge_bg']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="solutions_badge_text" class="form-label">Badge Text</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_badge_text" name="solutions_badge_text" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_badge_text']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_badge_text"
                                   value="<?php echo htmlspecialchars($settings['solutions_badge_text']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_cta_color" class="form-label">CTA Link Color</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_cta_color" name="solutions_cta_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_cta_color']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_cta_color"
                                   value="<?php echo htmlspecialchars($settings['solutions_cta_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_cta_hover" class="form-label">CTA Hover</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_cta_hover" name="solutions_cta_hover" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_cta_hover']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_cta_hover"
                                   value="<?php echo htmlspecialchars($settings['solutions_cta_hover']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>

            <!-- View All Button Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">View All Solutions Button</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="solutions_more_btn_bg_start" class="form-label">Button Gradient Start</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_more_btn_bg_start" name="solutions_more_btn_bg_start" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_more_btn_bg_start']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_more_btn_bg_start"
                                   value="<?php echo htmlspecialchars($settings['solutions_more_btn_bg_start']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_more_btn_bg_end" class="form-label">Button Gradient End</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_more_btn_bg_end" name="solutions_more_btn_bg_end" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_more_btn_bg_end']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_more_btn_bg_end"
                                   value="<?php echo htmlspecialchars($settings['solutions_more_btn_bg_end']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_more_btn_text" class="form-label">Button Text</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_more_btn_text" name="solutions_more_btn_text" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_more_btn_text']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_more_btn_text"
                                   value="<?php echo htmlspecialchars($settings['solutions_more_btn_text']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="solutions_more_btn_hover_start" class="form-label">Hover Gradient Start</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_more_btn_hover_start" name="solutions_more_btn_hover_start" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_more_btn_hover_start']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_more_btn_hover_start"
                                   value="<?php echo htmlspecialchars($settings['solutions_more_btn_hover_start']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solutions_more_btn_hover_end" class="form-label">Hover Gradient End</label>
                        <div class="color-input-group">
                            <input type="color" id="solutions_more_btn_hover_end" name="solutions_more_btn_hover_end" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['solutions_more_btn_hover_end']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="solutions_more_btn_hover_end"
                                   value="<?php echo htmlspecialchars($settings['solutions_more_btn_hover_end']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Live Preview -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Live Preview</h4>
                <div class="preview-container" id="solutions-preview">
                    <div class="preview-section" id="preview-section">
                        <div class="preview-header">
                            <h2 class="preview-title" id="preview-title"><?php echo htmlspecialchars($settings['solutions_section_title']); ?></h2>
                            <p class="preview-subtitle" id="preview-subtitle"><?php echo htmlspecialchars($settings['solutions_section_subtitle']); ?></p>
                        </div>
                        <div class="preview-nav" id="preview-nav">
                            <span class="preview-nav-link active">Solution 1</span>
                            <span class="preview-nav-link">Solution 2</span>
                            <span class="preview-nav-link">Solution 3</span>
                        </div>
                        <div class="preview-card" id="preview-card">
                            <span class="preview-badge" id="preview-badge">Solution</span>
                            <h3 class="preview-heading" id="preview-heading">Sample Solution</h3>
                            <p class="preview-description" id="preview-description">This is a sample description for the solution card.</p>
                            <ul class="preview-features">
                                <li id="preview-feature">✓ Feature item</li>
                            </ul>
                            <a href="#" class="preview-cta" id="preview-cta">Learn More →</a>
                        </div>
                        <div class="preview-btn-container">
                            <span class="preview-more-btn" id="preview-more-btn">View All Solutions →</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Why Choose Section -->
        <div class="form-section">
            <h3 class="form-section-title">❓ Why Choose Section</h3>
            <p class="form-section-description">Customize the headings and colors for the "Why Choose" section. Card content is managed separately in the Why Choose admin panel.</p>
            
            <!-- Text Settings -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Section Headings</h4>
                
                <div class="form-group">
                    <label for="why_choose_section_title" class="form-label">Section Title</label>
                    <input type="text" id="why_choose_section_title" name="why_choose_section_title" class="form-input" 
                           value="<?php echo htmlspecialchars($settings['why_choose_section_title']); ?>" maxlength="100">
                    <p class="form-help">Main heading displayed at the top of the section (e.g., "Why Choose Karyalay?")</p>
                </div>
                
                <div class="form-group">
                    <label for="why_choose_section_subtitle" class="form-label">Section Subtitle</label>
                    <textarea id="why_choose_section_subtitle" name="why_choose_section_subtitle" class="form-input form-textarea" 
                              rows="2" maxlength="300"><?php echo htmlspecialchars($settings['why_choose_section_subtitle']); ?></textarea>
                    <p class="form-help">Descriptive text below the main heading</p>
                </div>
            </div>
            
            <!-- Background & Title Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Section Colors</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="why_choose_section_bg" class="form-label">Section Background</label>
                        <div class="color-input-group">
                            <input type="color" id="why_choose_section_bg" name="why_choose_section_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['why_choose_section_bg']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="why_choose_section_bg"
                                   value="<?php echo htmlspecialchars($settings['why_choose_section_bg']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="why_choose_title_color" class="form-label">Title Color</label>
                        <div class="color-input-group">
                            <input type="color" id="why_choose_title_color" name="why_choose_title_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['why_choose_title_color']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="why_choose_title_color"
                                   value="<?php echo htmlspecialchars($settings['why_choose_title_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="why_choose_subtitle_color" class="form-label">Subtitle Color</label>
                        <div class="color-input-group">
                            <input type="color" id="why_choose_subtitle_color" name="why_choose_subtitle_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['why_choose_subtitle_color']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="why_choose_subtitle_color"
                                   value="<?php echo htmlspecialchars($settings['why_choose_subtitle_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Card Colors</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="why_choose_card_bg" class="form-label">Card Background</label>
                        <div class="color-input-group">
                            <input type="color" id="why_choose_card_bg" name="why_choose_card_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['why_choose_card_bg']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="why_choose_card_bg"
                                   value="<?php echo htmlspecialchars($settings['why_choose_card_bg']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="why_choose_card_title_color" class="form-label">Card Title Color</label>
                        <div class="color-input-group">
                            <input type="color" id="why_choose_card_title_color" name="why_choose_card_title_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['why_choose_card_title_color']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="why_choose_card_title_color"
                                   value="<?php echo htmlspecialchars($settings['why_choose_card_title_color']); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="why_choose_card_desc_color" class="form-label">Card Description Color</label>
                        <div class="color-input-group">
                            <input type="color" id="why_choose_card_desc_color" name="why_choose_card_desc_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['why_choose_card_desc_color']); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="why_choose_card_desc_color"
                                   value="<?php echo htmlspecialchars($settings['why_choose_card_desc_color']); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Live Preview -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Live Preview</h4>
                <div class="preview-container" id="why-choose-preview">
                    <div class="preview-why-choose-section" id="preview-why-choose-section">
                        <div class="preview-header">
                            <h2 class="preview-why-choose-title" id="preview-why-choose-title"><?php echo htmlspecialchars($settings['why_choose_section_title']); ?></h2>
                            <p class="preview-why-choose-subtitle" id="preview-why-choose-subtitle"><?php echo htmlspecialchars($settings['why_choose_section_subtitle']); ?></p>
                        </div>
                        <div class="preview-why-choose-cards">
                            <div class="preview-why-choose-card" id="preview-why-choose-card">
                                <div class="preview-why-choose-card-image">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 40px; height: 40px; color: #6b7280;">
                                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="preview-why-choose-card-content">
                                    <h3 class="preview-why-choose-card-title" id="preview-why-choose-card-title">Sample Card Title</h3>
                                    <p class="preview-why-choose-card-desc" id="preview-why-choose-card-desc">This is a sample card description text.</p>
                                </div>
                            </div>
                            <div class="preview-why-choose-card" id="preview-why-choose-card-2">
                                <div class="preview-why-choose-card-image">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 40px; height: 40px; color: #6b7280;">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="preview-why-choose-card-content">
                                    <h3 class="preview-why-choose-card-title">Another Card</h3>
                                    <p class="preview-why-choose-card-desc">Another sample description.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Success Stories Section -->
        <div class="form-section">
            <h3 class="form-section-title">🏆 Success Stories Section</h3>
            <p class="form-section-description">Customize the Success Stories gallery section. Case study content is managed separately in the Case Studies admin panel.</p>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">Enable/Disable</h4>
                <div class="form-group">
                    <label class="form-checkbox-wrapper">
                        <input type="checkbox" id="success_stories_enabled" name="success_stories_enabled" 
                               class="form-checkbox" value="1"
                               <?php echo ($settings['success_stories_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
                        <span class="form-checkbox-label-text">
                            <strong>Enable Success Stories Section</strong>
                            <span class="form-help">Show the Success Stories gallery on the homepage</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <!-- Text Settings -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Section Headings</h4>
                
                <div class="form-group">
                    <label for="success_stories_section_title" class="form-label">Section Title</label>
                    <input type="text" id="success_stories_section_title" name="success_stories_section_title" class="form-input" 
                           value="<?php echo htmlspecialchars($settings['success_stories_section_title'] ?? 'Success Stories'); ?>" maxlength="100">
                    <p class="form-help">Main heading displayed at the top of the section</p>
                </div>
                
                <div class="form-group">
                    <label for="success_stories_section_subtitle" class="form-label">Section Subtitle</label>
                    <textarea id="success_stories_section_subtitle" name="success_stories_section_subtitle" class="form-input form-textarea" 
                              rows="2" maxlength="300"><?php echo htmlspecialchars($settings['success_stories_section_subtitle'] ?? 'See how businesses like yours are achieving remarkable results with our solutions'); ?></textarea>
                    <p class="form-help">Descriptive text below the main heading</p>
                </div>
            </div>
            
            <!-- Background Gradient -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Section Background (Gradient)</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="success_stories_bg_gradient_start" class="form-label">Gradient Start Color</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_bg_gradient_start" name="success_stories_bg_gradient_start" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_bg_gradient_start'] ?? '#1a1a2e'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_bg_gradient_start"
                                   value="<?php echo htmlspecialchars($settings['success_stories_bg_gradient_start'] ?? '#1a1a2e'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="success_stories_bg_gradient_end" class="form-label">Gradient End Color</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_bg_gradient_end" name="success_stories_bg_gradient_end" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_bg_gradient_end'] ?? '#16213e'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_bg_gradient_end"
                                   value="<?php echo htmlspecialchars($settings['success_stories_bg_gradient_end'] ?? '#16213e'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Title Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Title Colors</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="success_stories_title_color" class="form-label">Title Color</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_title_color" name="success_stories_title_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_title_color'] ?? '#ffffff'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_title_color"
                                   value="<?php echo htmlspecialchars($settings['success_stories_title_color'] ?? '#ffffff'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="success_stories_subtitle_color" class="form-label">Subtitle Color</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_subtitle_color" name="success_stories_subtitle_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_subtitle_color'] ?? '#b3b3b3'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_subtitle_color"
                                   value="<?php echo htmlspecialchars($settings['success_stories_subtitle_color'] ?? '#b3b3b3'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Story Card Colors</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="success_stories_card_overlay" class="form-label">Card Overlay</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_card_overlay" name="success_stories_card_overlay" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_overlay'] ?? '#000000'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_card_overlay"
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_overlay'] ?? '#000000'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="success_stories_card_title" class="form-label">Card Title</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_card_title" name="success_stories_card_title" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_title'] ?? '#ffffff'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_card_title"
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_title'] ?? '#ffffff'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="success_stories_card_industry" class="form-label">Industry Text</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_card_industry" name="success_stories_card_industry" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_industry'] ?? '#cccccc'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_card_industry"
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_industry'] ?? '#cccccc'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="success_stories_card_desc" class="form-label">Description</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_card_desc" name="success_stories_card_desc" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_desc'] ?? '#e6e6e6'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_card_desc"
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_desc'] ?? '#e6e6e6'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="success_stories_card_badge_bg" class="form-label">Badge Background</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_card_badge_bg" name="success_stories_card_badge_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_badge_bg'] ?? '#333333'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_card_badge_bg"
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_badge_bg'] ?? '#333333'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="success_stories_card_badge_text" class="form-label">Badge Text</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_card_badge_text" name="success_stories_card_badge_text" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_badge_text'] ?? '#ffffff'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_card_badge_text"
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_badge_text'] ?? '#ffffff'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="success_stories_card_btn_bg" class="form-label">Button Background</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_card_btn_bg" name="success_stories_card_btn_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_btn_bg'] ?? '#262626'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_card_btn_bg"
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_btn_bg'] ?? '#262626'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="success_stories_card_btn_text" class="form-label">Button Text</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_card_btn_text" name="success_stories_card_btn_text" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_btn_text'] ?? '#ffffff'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_card_btn_text"
                                   value="<?php echo htmlspecialchars($settings['success_stories_card_btn_text'] ?? '#ffffff'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- View All Button Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">View All Button</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="success_stories_view_all_bg" class="form-label">Background</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_view_all_bg" name="success_stories_view_all_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_view_all_bg'] ?? '#0d0d0d'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_view_all_bg"
                                   value="<?php echo htmlspecialchars($settings['success_stories_view_all_bg'] ?? '#0d0d0d'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="success_stories_view_all_text" class="form-label">Text Color</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_view_all_text" name="success_stories_view_all_text" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_view_all_text'] ?? '#ffffff'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_view_all_text"
                                   value="<?php echo htmlspecialchars($settings['success_stories_view_all_text'] ?? '#ffffff'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="success_stories_view_all_icon_bg" class="form-label">Icon Background</label>
                        <div class="color-input-group">
                            <input type="color" id="success_stories_view_all_icon_bg" name="success_stories_view_all_icon_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['success_stories_view_all_icon_bg'] ?? '#1a1a1a'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="success_stories_view_all_icon_bg"
                                   value="<?php echo htmlspecialchars($settings['success_stories_view_all_icon_bg'] ?? '#1a1a1a'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="success_stories_view_all_icon_hover_bg" class="form-label">Icon Hover Background</label>
                    <div class="color-input-group">
                        <input type="color" id="success_stories_view_all_icon_hover_bg" name="success_stories_view_all_icon_hover_bg" class="form-input-color" 
                               value="<?php echo htmlspecialchars($settings['success_stories_view_all_icon_hover_bg'] ?? '#333333'); ?>">
                        <input type="text" class="form-input form-input-hex" data-color-target="success_stories_view_all_icon_hover_bg"
                               value="<?php echo htmlspecialchars($settings['success_stories_view_all_icon_hover_bg'] ?? '#333333'); ?>" maxlength="7">
                    </div>
                </div>
            </div>
            
            <!-- Live Preview -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Live Preview</h4>
                <div class="preview-container" id="success-stories-preview">
                    <div class="preview-success-stories-section" id="preview-success-stories-section" style="padding: 30px 20px; background: linear-gradient(135deg, <?php echo htmlspecialchars($settings['success_stories_bg_gradient_start'] ?? '#1a1a2e'); ?> 0%, <?php echo htmlspecialchars($settings['success_stories_bg_gradient_end'] ?? '#16213e'); ?> 100%);">
                        <div class="preview-header" style="text-align: left; padding-left: 20px;">
                            <h2 id="preview-success-stories-title" style="font-size: 24px; font-weight: 400; margin: 0 0 8px 0; color: <?php echo htmlspecialchars($settings['success_stories_title_color'] ?? '#ffffff'); ?>;"><?php echo htmlspecialchars($settings['success_stories_section_title'] ?? 'Success Stories'); ?></h2>
                            <p id="preview-success-stories-subtitle" style="font-size: 14px; margin: 0 0 20px 0; color: <?php echo htmlspecialchars($settings['success_stories_subtitle_color'] ?? '#b3b3b3'); ?>;"><?php echo htmlspecialchars($settings['success_stories_section_subtitle'] ?? 'See how businesses like yours are achieving remarkable results with our solutions'); ?></p>
                        </div>
                        <div style="display: flex; gap: 0; overflow: hidden;">
                            <div class="preview-story-card" style="position: relative; width: 200px; height: 150px; flex-shrink: 0; overflow: hidden; border-radius: 0;">
                                <img src="https://images.unsplash.com/photo-1553877522-43269d4ea984?w=400&h=300&fit=crop" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                <div id="preview-story-overlay" style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.6) 100%);"></div>
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 15px;">
                                    <span id="preview-story-badge" style="display: inline-block; font-size: 10px; font-weight: 600; text-transform: uppercase; padding: 2px 6px; border-radius: 3px; margin-bottom: 6px; background: <?php echo htmlspecialchars($settings['success_stories_card_badge_bg'] ?? '#333333'); ?>; color: <?php echo htmlspecialchars($settings['success_stories_card_badge_text'] ?? '#ffffff'); ?>;">Client Name</span>
                                    <h3 id="preview-story-title" style="font-size: 14px; font-weight: 600; margin: 0 0 4px 0; color: <?php echo htmlspecialchars($settings['success_stories_card_title'] ?? '#ffffff'); ?>;">Case Study Title</h3>
                                    <p id="preview-story-industry" style="font-size: 11px; margin: 0; color: <?php echo htmlspecialchars($settings['success_stories_card_industry'] ?? '#cccccc'); ?>;">Industry</p>
                                </div>
                            </div>
                            <div class="preview-story-card" style="position: relative; width: 200px; height: 150px; flex-shrink: 0; overflow: hidden; border-radius: 0;">
                                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=300&fit=crop" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.6) 100%);"></div>
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 15px;">
                                    <span style="display: inline-block; font-size: 10px; font-weight: 600; text-transform: uppercase; padding: 2px 6px; border-radius: 3px; margin-bottom: 6px; background: <?php echo htmlspecialchars($settings['success_stories_card_badge_bg'] ?? '#333333'); ?>; color: <?php echo htmlspecialchars($settings['success_stories_card_badge_text'] ?? '#ffffff'); ?>;">Another Client</span>
                                    <h3 style="font-size: 14px; font-weight: 600; margin: 0 0 4px 0; color: <?php echo htmlspecialchars($settings['success_stories_card_title'] ?? '#ffffff'); ?>;">Another Story</h3>
                                    <p style="font-size: 11px; margin: 0; color: <?php echo htmlspecialchars($settings['success_stories_card_industry'] ?? '#cccccc'); ?>;">Technology</p>
                                </div>
                            </div>
                            <div id="preview-view-all" style="display: flex; align-items: center; justify-content: center; width: 100px; height: 150px; flex-shrink: 0; background: <?php echo htmlspecialchars($settings['success_stories_view_all_bg'] ?? '#0d0d0d'); ?>;">
                                <div style="text-align: center; color: <?php echo htmlspecialchars($settings['success_stories_view_all_text'] ?? '#ffffff'); ?>;">
                                    <div id="preview-view-all-icon" style="width: 40px; height: 40px; border-radius: 50%; background: <?php echo htmlspecialchars($settings['success_stories_view_all_icon_bg'] ?? '#1a1a1a'); ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                    </div>
                                    <span style="font-size: 11px;">View All</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Blog Section -->
        <div class="form-section">
            <h3 class="form-section-title">📝 Blog Section</h3>
            <p class="form-section-description">Customize the Blog gallery section on the homepage. Blog posts are managed separately in the Blog admin panel.</p>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">Enable/Disable</h4>
                <div class="form-group">
                    <label class="form-checkbox-wrapper">
                        <input type="checkbox" id="blog_section_enabled" name="blog_section_enabled" 
                               class="form-checkbox" value="1"
                               <?php echo ($settings['blog_section_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
                        <span class="form-checkbox-label-text">
                            <strong>Enable Blog Section</strong>
                            <span class="form-help">Show the Blog gallery on the homepage</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <!-- Text Settings -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Section Headings</h4>
                
                <div class="form-group">
                    <label for="blog_section_title" class="form-label">Section Title</label>
                    <input type="text" id="blog_section_title" name="blog_section_title" class="form-input" 
                           value="<?php echo htmlspecialchars($settings['blog_section_title'] ?? 'Latest from Our Blog'); ?>" maxlength="100">
                    <p class="form-help">Main heading displayed at the top of the section</p>
                </div>
                
                <div class="form-group">
                    <label for="blog_section_subtitle" class="form-label">Section Subtitle</label>
                    <textarea id="blog_section_subtitle" name="blog_section_subtitle" class="form-input form-textarea" 
                              rows="2" maxlength="300"><?php echo htmlspecialchars($settings['blog_section_subtitle'] ?? 'Stay updated with the latest insights, tips, and news from our team'); ?></textarea>
                    <p class="form-help">Descriptive text below the main heading</p>
                </div>
            </div>
            
            <!-- Theme Selection -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Theme</h4>
                <div class="form-group">
                    <label for="blog_section_theme" class="form-label">Color Theme</label>
                    <select id="blog_section_theme" name="blog_section_theme" class="form-select" style="width: 100%; padding: var(--spacing-3); border: 1px solid var(--color-gray-300); border-radius: var(--radius-md);">
                        <option value="light" <?php echo ($settings['blog_section_theme'] ?? 'light') === 'light' ? 'selected' : ''; ?>>Light</option>
                        <option value="dark" <?php echo ($settings['blog_section_theme'] ?? 'light') === 'dark' ? 'selected' : ''; ?>>Dark</option>
                    </select>
                    <p class="form-help">Choose between light or dark background theme</p>
                </div>
            </div>
            
            <!-- Background Gradient -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Section Background (Gradient)</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="blog_section_bg_start" class="form-label">Gradient Start Color</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_section_bg_start" name="blog_section_bg_start" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_section_bg_start'] ?? '#f8fafc'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_section_bg_start"
                                   value="<?php echo htmlspecialchars($settings['blog_section_bg_start'] ?? '#f8fafc'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="blog_section_bg_end" class="form-label">Gradient End Color</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_section_bg_end" name="blog_section_bg_end" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_section_bg_end'] ?? '#f1f5f9'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_section_bg_end"
                                   value="<?php echo htmlspecialchars($settings['blog_section_bg_end'] ?? '#f1f5f9'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Title Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Title Colors</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="blog_title_color" class="form-label">Title Color</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_title_color" name="blog_title_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_title_color'] ?? '#0f172a'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_title_color"
                                   value="<?php echo htmlspecialchars($settings['blog_title_color'] ?? '#0f172a'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="blog_subtitle_color" class="form-label">Subtitle Color</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_subtitle_color" name="blog_subtitle_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_subtitle_color'] ?? '#64748b'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_subtitle_color"
                                   value="<?php echo htmlspecialchars($settings['blog_subtitle_color'] ?? '#64748b'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Blog Card Colors</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="blog_card_bg" class="form-label">Card Background</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_card_bg" name="blog_card_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_card_bg'] ?? '#ffffff'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_card_bg"
                                   value="<?php echo htmlspecialchars($settings['blog_card_bg'] ?? '#ffffff'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="blog_card_border" class="form-label">Card Border</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_card_border" name="blog_card_border" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_card_border'] ?? '#e2e8f0'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_card_border"
                                   value="<?php echo htmlspecialchars($settings['blog_card_border'] ?? '#e2e8f0'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="blog_card_title_color" class="form-label">Card Title</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_card_title_color" name="blog_card_title_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_card_title_color'] ?? '#0f172a'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_card_title_color"
                                   value="<?php echo htmlspecialchars($settings['blog_card_title_color'] ?? '#0f172a'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="blog_card_excerpt_color" class="form-label">Excerpt Text</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_card_excerpt_color" name="blog_card_excerpt_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_card_excerpt_color'] ?? '#64748b'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_card_excerpt_color"
                                   value="<?php echo htmlspecialchars($settings['blog_card_excerpt_color'] ?? '#64748b'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="blog_card_date_color" class="form-label">Date Color</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_card_date_color" name="blog_card_date_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_card_date_color'] ?? '#a855f7'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_card_date_color"
                                   value="<?php echo htmlspecialchars($settings['blog_card_date_color'] ?? '#a855f7'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="blog_card_tag_bg" class="form-label">Tag Background</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_card_tag_bg" name="blog_card_tag_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_card_tag_bg'] ?? '#f3e8ff'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_card_tag_bg"
                                   value="<?php echo htmlspecialchars($settings['blog_card_tag_bg'] ?? '#f3e8ff'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="blog_card_tag_text" class="form-label">Tag Text</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_card_tag_text" name="blog_card_tag_text" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_card_tag_text'] ?? '#9333ea'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_card_tag_text"
                                   value="<?php echo htmlspecialchars($settings['blog_card_tag_text'] ?? '#9333ea'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="blog_card_link_color" class="form-label">Read More Link</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_card_link_color" name="blog_card_link_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_card_link_color'] ?? '#a855f7'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_card_link_color"
                                   value="<?php echo htmlspecialchars($settings['blog_card_link_color'] ?? '#a855f7'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="blog_card_link_hover" class="form-label">Link Hover</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_card_link_hover" name="blog_card_link_hover" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_card_link_hover'] ?? '#9333ea'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_card_link_hover"
                                   value="<?php echo htmlspecialchars($settings['blog_card_link_hover'] ?? '#9333ea'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- View All Button Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">View All Button</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="blog_view_all_bg" class="form-label">Background</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_view_all_bg" name="blog_view_all_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_view_all_bg'] ?? '#faf5ff'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_view_all_bg"
                                   value="<?php echo htmlspecialchars($settings['blog_view_all_bg'] ?? '#faf5ff'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="blog_view_all_border" class="form-label">Border Color</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_view_all_border" name="blog_view_all_border" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_view_all_border'] ?? '#a855f7'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_view_all_border"
                                   value="<?php echo htmlspecialchars($settings['blog_view_all_border'] ?? '#a855f7'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="blog_view_all_text" class="form-label">Text Color</label>
                        <div class="color-input-group">
                            <input type="color" id="blog_view_all_text" name="blog_view_all_text" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['blog_view_all_text'] ?? '#0f172a'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="blog_view_all_text"
                                   value="<?php echo htmlspecialchars($settings['blog_view_all_text'] ?? '#0f172a'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="blog_view_all_icon_bg" class="form-label">Icon Background</label>
                    <div class="color-input-group">
                        <input type="color" id="blog_view_all_icon_bg" name="blog_view_all_icon_bg" class="form-input-color" 
                               value="<?php echo htmlspecialchars($settings['blog_view_all_icon_bg'] ?? '#f3e8ff'); ?>">
                        <input type="text" class="form-input form-input-hex" data-color-target="blog_view_all_icon_bg"
                               value="<?php echo htmlspecialchars($settings['blog_view_all_icon_bg'] ?? '#f3e8ff'); ?>" maxlength="7">
                    </div>
                </div>
            </div>
            
            <!-- Live Preview -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Live Preview</h4>
                <div class="preview-container" id="blog-preview">
                    <div class="preview-blog-section" id="preview-blog-section" style="padding: 30px 20px; background: linear-gradient(135deg, <?php echo htmlspecialchars($settings['blog_section_bg_start'] ?? '#f8fafc'); ?> 0%, <?php echo htmlspecialchars($settings['blog_section_bg_end'] ?? '#f1f5f9'); ?> 100%);">
                        <div class="preview-header" style="text-align: left; padding-left: 20px;">
                            <h2 id="preview-blog-title" style="font-size: 24px; font-weight: 400; margin: 0 0 8px 0; color: <?php echo htmlspecialchars($settings['blog_title_color'] ?? '#0f172a'); ?>;"><?php echo htmlspecialchars($settings['blog_section_title'] ?? 'Latest from Our Blog'); ?></h2>
                            <p id="preview-blog-subtitle" style="font-size: 14px; margin: 0 0 20px 0; color: <?php echo htmlspecialchars($settings['blog_subtitle_color'] ?? '#64748b'); ?>;"><?php echo htmlspecialchars($settings['blog_section_subtitle'] ?? 'Stay updated with the latest insights, tips, and news from our team'); ?></p>
                        </div>
                        <div style="display: flex; gap: 16px; overflow: hidden;">
                            <div class="preview-blog-card" id="preview-blog-card" style="width: 220px; flex-shrink: 0; border-radius: 12px; overflow: hidden; background: <?php echo htmlspecialchars($settings['blog_card_bg'] ?? '#ffffff'); ?>; border: 1px solid <?php echo htmlspecialchars($settings['blog_card_border'] ?? '#e2e8f0'); ?>;">
                                <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=400&h=200&fit=crop" alt="Preview" style="width: 100%; height: 100px; object-fit: cover;">
                                <div style="padding: 12px;">
                                    <div style="display: flex; gap: 8px; margin-bottom: 8px; align-items: center;">
                                        <span id="preview-blog-date" style="font-size: 11px; color: <?php echo htmlspecialchars($settings['blog_card_date_color'] ?? '#a855f7'); ?>;">Dec 16, 2025</span>
                                        <span id="preview-blog-tag" style="font-size: 9px; padding: 2px 6px; border-radius: 10px; background: <?php echo htmlspecialchars($settings['blog_card_tag_bg'] ?? '#f3e8ff'); ?>; color: <?php echo htmlspecialchars($settings['blog_card_tag_text'] ?? '#9333ea'); ?>;">TIPS</span>
                                    </div>
                                    <h3 id="preview-blog-card-title" style="font-size: 14px; font-weight: 600; margin: 0 0 6px 0; color: <?php echo htmlspecialchars($settings['blog_card_title_color'] ?? '#0f172a'); ?>;">Blog Post Title</h3>
                                    <p id="preview-blog-excerpt" style="font-size: 12px; margin: 0 0 8px 0; color: <?php echo htmlspecialchars($settings['blog_card_excerpt_color'] ?? '#64748b'); ?>;">A brief excerpt from the blog post...</p>
                                    <a id="preview-blog-link" href="#" style="font-size: 12px; font-weight: 600; text-decoration: none; color: <?php echo htmlspecialchars($settings['blog_card_link_color'] ?? '#a855f7'); ?>;">Read Article →</a>
                                </div>
                            </div>
                            <div id="preview-blog-view-all" style="display: flex; align-items: center; justify-content: center; width: 100px; flex-shrink: 0; border-radius: 12px; background: <?php echo htmlspecialchars($settings['blog_view_all_bg'] ?? '#faf5ff'); ?>; border: 2px dashed <?php echo htmlspecialchars($settings['blog_view_all_border'] ?? '#a855f7'); ?>;">
                                <div style="text-align: center; color: <?php echo htmlspecialchars($settings['blog_view_all_text'] ?? '#0f172a'); ?>;">
                                    <div id="preview-blog-view-all-icon" style="width: 36px; height: 36px; border-radius: 50%; background: <?php echo htmlspecialchars($settings['blog_view_all_icon_bg'] ?? '#f3e8ff'); ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: <?php echo htmlspecialchars($settings['blog_card_link_color'] ?? '#a855f7'); ?>;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                    </div>
                                    <span style="font-size: 11px; font-weight: 600;">View All</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CTA Banner Section -->
        <div class="form-section">
            <h3 class="form-section-title">🎯 CTA Banner Section</h3>
            <p class="form-section-description">A full-width banner with background image, overlay, two heading lines, and a CTA button. Appears after the Powerful Solutions section.</p>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">Enable/Disable</h4>
                <div class="form-group">
                    <label class="form-checkbox-wrapper">
                        <input type="checkbox" id="homepage_cta_banner_enabled" name="homepage_cta_banner_enabled" 
                               class="form-checkbox" value="1"
                               <?php echo ($settings['homepage_cta_banner_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
                        <span class="form-checkbox-label-text">
                            <strong>Enable CTA Banner Section</strong>
                            <span class="form-help">Show this banner section after the Powerful Solutions section</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">Background Image & Overlay</h4>
                <div class="form-group">
                    <label for="homepage_cta_banner_image_url" class="form-label">Background Image URL</label>
                    <input type="url" id="homepage_cta_banner_image_url" name="homepage_cta_banner_image_url" class="form-input" 
                        value="<?php echo htmlspecialchars($settings['homepage_cta_banner_image_url'] ?? ''); ?>"
                        placeholder="https://example.com/banner-image.jpg">
                    <?php if (!empty($settings['homepage_cta_banner_image_url'])): ?>
                        <div class="image-preview-small" style="margin-top: 10px;">
                            <img src="<?php echo htmlspecialchars($settings['homepage_cta_banner_image_url']); ?>" alt="Banner preview" style="max-width: 300px; border-radius: 8px;">
                        </div>
                    <?php endif; ?>
                    <p class="form-help">Recommended: 1400x500 pixels. Leave empty to use default image.</p>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="homepage_cta_banner_overlay_color" class="form-label">Overlay Color</label>
                        <input type="text" id="homepage_cta_banner_overlay_color" name="homepage_cta_banner_overlay_color" class="form-input" 
                            value="<?php echo htmlspecialchars($settings['homepage_cta_banner_overlay_color'] ?? 'rgba(0,0,0,0.5)'); ?>"
                            placeholder="rgba(0,0,0,0.5)">
                        <p class="form-help">Use rgba format for transparency, e.g., rgba(0,0,0,0.5)</p>
                    </div>
                    <div class="form-group">
                        <label for="homepage_cta_banner_overlay_intensity" class="form-label">Overlay Intensity</label>
                        <input type="range" id="homepage_cta_banner_overlay_intensity" name="homepage_cta_banner_overlay_intensity" 
                            class="form-range" min="0" max="1" step="0.05" style="width: 100%;"
                            value="<?php echo htmlspecialchars($settings['homepage_cta_banner_overlay_intensity'] ?? '0.5'); ?>">
                        <p class="form-help">Opacity: <span id="cta_overlay_intensity_value"><?php echo number_format(floatval($settings['homepage_cta_banner_overlay_intensity'] ?? 0.5) * 100, 0); ?>%</span></p>
                    </div>
                </div>
            </div>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">Heading Text</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="homepage_cta_banner_heading1" class="form-label">Heading Line 1</label>
                        <input type="text" id="homepage_cta_banner_heading1" name="homepage_cta_banner_heading1" class="form-input" 
                            value="<?php echo htmlspecialchars($settings['homepage_cta_banner_heading1'] ?? ''); ?>" maxlength="100"
                            placeholder="e.g., Ready to transform your business?">
                        <p class="form-help">Max 100 characters</p>
                    </div>
                    <div class="form-group">
                        <label for="homepage_cta_banner_heading2" class="form-label">Heading Line 2</label>
                        <input type="text" id="homepage_cta_banner_heading2" name="homepage_cta_banner_heading2" class="form-input" 
                            value="<?php echo htmlspecialchars($settings['homepage_cta_banner_heading2'] ?? ''); ?>" maxlength="100"
                            placeholder="e.g., Get started with our powerful solutions today!">
                        <p class="form-help">Max 100 characters</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="homepage_cta_banner_heading_color" class="form-label">Heading Color</label>
                    <div class="color-input-group">
                        <input type="color" id="homepage_cta_banner_heading_color" name="homepage_cta_banner_heading_color" class="form-input-color" 
                            value="<?php echo htmlspecialchars($settings['homepage_cta_banner_heading_color'] ?? '#FFFFFF'); ?>">
                        <input type="text" class="form-input form-input-hex" data-color-target="homepage_cta_banner_heading_color"
                            value="<?php echo htmlspecialchars($settings['homepage_cta_banner_heading_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                    </div>
                </div>
            </div>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">CTA Button</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="homepage_cta_banner_button_text" class="form-label">Button Text</label>
                        <input type="text" id="homepage_cta_banner_button_text" name="homepage_cta_banner_button_text" class="form-input" 
                            value="<?php echo htmlspecialchars($settings['homepage_cta_banner_button_text'] ?? ''); ?>" maxlength="50"
                            placeholder="e.g., Get Started Now">
                        <p class="form-help">Max 50 characters</p>
                    </div>
                    <div class="form-group">
                        <label for="homepage_cta_banner_button_link" class="form-label">Button Link</label>
                        <input type="text" id="homepage_cta_banner_button_link" name="homepage_cta_banner_button_link" class="form-input" 
                            value="<?php echo htmlspecialchars($settings['homepage_cta_banner_button_link'] ?? '#contact-form'); ?>"
                            placeholder="#contact-form">
                        <p class="form-help">Use #contact-form to scroll to contact section, or enter a URL</p>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="homepage_cta_banner_button_bg_color" class="form-label">Button Background Color</label>
                        <div class="color-input-group">
                            <input type="color" id="homepage_cta_banner_button_bg_color" name="homepage_cta_banner_button_bg_color" class="form-input-color" 
                                value="<?php echo htmlspecialchars($settings['homepage_cta_banner_button_bg_color'] ?? '#FFFFFF'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="homepage_cta_banner_button_bg_color"
                                value="<?php echo htmlspecialchars($settings['homepage_cta_banner_button_bg_color'] ?? '#FFFFFF'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="homepage_cta_banner_button_text_color" class="form-label">Button Text Color</label>
                        <div class="color-input-group">
                            <input type="color" id="homepage_cta_banner_button_text_color" name="homepage_cta_banner_button_text_color" class="form-input-color" 
                                value="<?php echo htmlspecialchars($settings['homepage_cta_banner_button_text_color'] ?? '#2563eb'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="homepage_cta_banner_button_text_color"
                                value="<?php echo htmlspecialchars($settings['homepage_cta_banner_button_text_color'] ?? '#2563eb'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CTA Banner Preview -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Live Preview</h4>
                <div class="cta-banner-preview" id="cta-banner-preview" style="position: relative; border-radius: 12px; overflow: hidden; min-height: 200px;">
                    <img id="cta-preview-image" src="<?php echo htmlspecialchars($settings['homepage_cta_banner_image_url'] ?? 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1400&h=500&fit=crop'); ?>" 
                         alt="Banner preview" style="width: 100%; height: 200px; object-fit: cover;">
                    <div id="cta-preview-overlay" style="position: absolute; inset: 0; background: <?php echo htmlspecialchars($settings['homepage_cta_banner_overlay_color'] ?? 'rgba(0,0,0,0.5)'); ?>; opacity: <?php echo htmlspecialchars($settings['homepage_cta_banner_overlay_intensity'] ?? '0.5'); ?>;"></div>
                    <div style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; padding: 20px;">
                        <h3 id="cta-preview-heading1" style="color: <?php echo htmlspecialchars($settings['homepage_cta_banner_heading_color'] ?? '#FFFFFF'); ?>; font-size: 1.5rem; font-weight: 400; margin: 0 0 5px 0;"><?php echo htmlspecialchars($settings['homepage_cta_banner_heading1'] ?? 'Ready to transform your business?'); ?></h3>
                        <h3 id="cta-preview-heading2" style="color: <?php echo htmlspecialchars($settings['homepage_cta_banner_heading_color'] ?? '#FFFFFF'); ?>; font-size: 1.5rem; font-weight: 400; margin: 0 0 15px 0;"><?php echo htmlspecialchars($settings['homepage_cta_banner_heading2'] ?? 'Get started with our powerful solutions today!'); ?></h3>
                        <div>
                            <span id="cta-preview-button" style="display: inline-block; padding: 10px 20px; background: <?php echo htmlspecialchars($settings['homepage_cta_banner_button_bg_color'] ?? '#FFFFFF'); ?>; color: <?php echo htmlspecialchars($settings['homepage_cta_banner_button_text_color'] ?? '#2563eb'); ?>; border-radius: 6px; font-weight: 500;"><?php echo htmlspecialchars($settings['homepage_cta_banner_button_text'] ?? 'Get Started Now'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Homepage Testimonials Section -->
        <div class="form-section">
            <h3 class="form-section-title">💬 Testimonials Section</h3>
            <p class="form-section-description">Customize the Testimonials showcase section on the homepage. Testimonial content is managed separately in the Testimonials admin panel.</p>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">Enable/Disable</h4>
                <div class="form-group">
                    <label class="form-checkbox-wrapper">
                        <input type="checkbox" id="homepage_testimonials_enabled" name="homepage_testimonials_enabled" 
                               class="form-checkbox" value="1"
                               <?php echo ($settings['homepage_testimonials_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
                        <span class="form-checkbox-label-text">
                            <strong>Enable Testimonials Section</strong>
                            <span class="form-help">Show the Testimonials showcase on the homepage</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <!-- Theme Selection -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Theme</h4>
                <div class="form-group">
                    <label for="homepage_testimonials_theme" class="form-label">Color Theme</label>
                    <select id="homepage_testimonials_theme" name="homepage_testimonials_theme" class="form-select" style="width: 100%; padding: var(--spacing-3); border: 1px solid var(--color-gray-300); border-radius: var(--radius-md);">
                        <option value="light" <?php echo ($settings['homepage_testimonials_theme'] ?? 'light') === 'light' ? 'selected' : ''; ?>>Light</option>
                        <option value="dark" <?php echo ($settings['homepage_testimonials_theme'] ?? 'light') === 'dark' ? 'selected' : ''; ?>>Dark</option>
                    </select>
                    <p class="form-help">Choose between light or dark background theme</p>
                </div>
            </div>
            
            <!-- Section Headings -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Section Headings</h4>
                
                <div class="form-group">
                    <label for="homepage_testimonials_heading" class="form-label">Section Heading</label>
                    <input type="text" id="homepage_testimonials_heading" name="homepage_testimonials_heading" class="form-input" 
                           value="<?php echo htmlspecialchars($settings['homepage_testimonials_heading'] ?? 'What Our Customers Say'); ?>" maxlength="48"
                           placeholder="e.g., What Our Customers Say">
                    <p class="form-help">Max 48 characters (<span id="testimonials_heading_count"><?php echo strlen($settings['homepage_testimonials_heading'] ?? ''); ?></span>/48)</p>
                </div>
                
                <div class="form-group">
                    <label for="homepage_testimonials_subheading" class="form-label">Section Subheading</label>
                    <input type="text" id="homepage_testimonials_subheading" name="homepage_testimonials_subheading" class="form-input" 
                           value="<?php echo htmlspecialchars($settings['homepage_testimonials_subheading'] ?? 'Don\'t just take our word for it - hear from businesses that trust us'); ?>" maxlength="120"
                           placeholder="e.g., Don't just take our word for it - hear from businesses that trust us">
                    <p class="form-help">Max 120 characters (<span id="testimonials_subheading_count"><?php echo strlen($settings['homepage_testimonials_subheading'] ?? ''); ?></span>/120)</p>
                </div>
            </div>
            
            <!-- Accent Color -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Accent Color</h4>
                <div class="form-group">
                    <label for="homepage_testimonials_accent_color" class="form-label">Accent Color</label>
                    <div class="color-input-group">
                        <input type="color" id="homepage_testimonials_accent_color" name="homepage_testimonials_accent_color" class="form-input-color" 
                               value="<?php echo htmlspecialchars($settings['homepage_testimonials_accent_color'] ?? '#10b981'); ?>">
                        <input type="text" class="form-input form-input-hex" data-color-target="homepage_testimonials_accent_color"
                               value="<?php echo htmlspecialchars($settings['homepage_testimonials_accent_color'] ?? '#10b981'); ?>" maxlength="7">
                    </div>
                    <p class="form-help">Used for highlights, borders, and interactive elements</p>
                </div>
            </div>
        </div>
        
        <!-- Homepage FAQ Section -->
        <div class="form-section">
            <h3 class="form-section-title">❓ FAQ Section</h3>
            <p class="form-section-description">Add a Frequently Asked Questions section to the homepage. This section appears right before the CTA form.</p>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">Enable/Disable</h4>
                <div class="form-group">
                    <label class="form-checkbox-wrapper">
                        <input type="checkbox" id="homepage_faq_enabled" name="homepage_faq_enabled" 
                               class="form-checkbox" value="1"
                               <?php echo ($settings['homepage_faq_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
                        <span class="form-checkbox-label-text">
                            <strong>Enable FAQ Section</strong>
                            <span class="form-help">Show the FAQ section on the homepage before the contact form</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">Theme & Appearance</h4>
                <div class="form-group">
                    <label for="homepage_faq_theme" class="form-label">Theme / Mode</label>
                    <select id="homepage_faq_theme" name="homepage_faq_theme" class="form-select" style="width: 100%; padding: var(--spacing-3); border: 1px solid var(--color-gray-300); border-radius: var(--radius-md);">
                        <option value="light" <?php echo ($settings['homepage_faq_theme'] ?? 'light') === 'light' ? 'selected' : ''; ?>>Light</option>
                        <option value="dark" <?php echo ($settings['homepage_faq_theme'] ?? 'light') === 'dark' ? 'selected' : ''; ?>>Dark</option>
                    </select>
                    <p class="form-help">Choose between light or dark background theme</p>
                </div>
            </div>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">Section Headings</h4>
                <div class="form-group">
                    <label for="homepage_faq_heading" class="form-label">Section Heading</label>
                    <input type="text" id="homepage_faq_heading" name="homepage_faq_heading" class="form-input" 
                        value="<?php echo htmlspecialchars($settings['homepage_faq_heading'] ?? ''); ?>" maxlength="48"
                        placeholder="e.g., Frequently Asked Questions">
                    <p class="form-help">Max 48 characters (<span id="faq_heading_count"><?php echo strlen($settings['homepage_faq_heading'] ?? ''); ?></span>/48)</p>
                </div>
                
                <div class="form-group">
                    <label for="homepage_faq_subheading" class="form-label">Section Subheading</label>
                    <input type="text" id="homepage_faq_subheading" name="homepage_faq_subheading" class="form-input" 
                        value="<?php echo htmlspecialchars($settings['homepage_faq_subheading'] ?? ''); ?>" maxlength="120"
                        placeholder="e.g., Everything you need to know about our platform">
                    <p class="form-help">Max 120 characters (<span id="faq_subheading_count"><?php echo strlen($settings['homepage_faq_subheading'] ?? ''); ?></span>/120)</p>
                </div>
            </div>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">FAQ Items</h4>
                <div class="form-group">
                    <label for="homepage_faq_items" class="form-label">FAQs (JSON)</label>
                    <textarea id="homepage_faq_items" name="homepage_faq_items" class="form-input form-textarea" 
                              rows="10" style="font-family: monospace; font-size: 13px;"
                              placeholder='[{"question":"Your question here?","answer":"Your answer here."}]'><?php echo htmlspecialchars($settings['homepage_faq_items'] ?? '[]'); ?></textarea>
                    <p class="form-help">Enter FAQ items as JSON array. Each item should have "question" and "answer" fields.</p>
                    <div style="margin-top: 10px;">
                        <button type="button" class="btn btn-outline btn-sm" onclick="addFaqItem()">+ Add FAQ Item</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="formatFaqJson()">Format JSON</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="validateFaqJson()">Validate JSON</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Homepage CTA Form Section -->
        <div class="form-section">
            <h3 class="form-section-title">📬 CTA Contact Form Section</h3>
            <p class="form-section-description">Customize the Contact Form section that appears at the bottom of the homepage.</p>
            
            <div class="form-subsection">
                <h4 class="form-subsection-title">Enable/Disable</h4>
                <div class="form-group">
                    <label class="form-checkbox-wrapper">
                        <input type="checkbox" id="homepage_cta_form_enabled" name="homepage_cta_form_enabled" 
                               class="form-checkbox" value="1"
                               <?php echo ($settings['homepage_cta_form_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
                        <span class="form-checkbox-label-text">
                            <strong>Enable CTA Form Section</strong>
                            <span class="form-help">Show the contact form section on the homepage</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <!-- Theme Selection -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Theme</h4>
                <div class="form-group">
                    <label for="homepage_cta_form_theme" class="form-label">Color Theme</label>
                    <select id="homepage_cta_form_theme" name="homepage_cta_form_theme" class="form-select" style="width: 100%; padding: var(--spacing-3); border: 1px solid var(--color-gray-300); border-radius: var(--radius-md);">
                        <option value="dark" <?php echo ($settings['homepage_cta_form_theme'] ?? 'dark') === 'dark' ? 'selected' : ''; ?>>Dark</option>
                        <option value="light" <?php echo ($settings['homepage_cta_form_theme'] ?? 'dark') === 'light' ? 'selected' : ''; ?>>Light</option>
                    </select>
                    <p class="form-help">Choose between dark or light background theme</p>
                </div>
            </div>
            
            <!-- Content Text -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Content Text</h4>
                
                <div class="form-group">
                    <label for="homepage_cta_form_badge_text" class="form-label">Badge Text</label>
                    <input type="text" id="homepage_cta_form_badge_text" name="homepage_cta_form_badge_text" class="form-input" 
                           value="<?php echo htmlspecialchars($settings['homepage_cta_form_badge_text'] ?? 'Trusted by 500+ Businesses'); ?>" maxlength="40"
                           placeholder="e.g., Trusted by 500+ Businesses">
                    <p class="form-help">Small badge text above the title (max 40 characters)</p>
                </div>
                
                <div class="form-group">
                    <label for="homepage_cta_form_title" class="form-label">Section Title</label>
                    <input type="text" id="homepage_cta_form_title" name="homepage_cta_form_title" class="form-input" 
                           value="<?php echo htmlspecialchars($settings['homepage_cta_form_title'] ?? 'Ready to Transform Your Business?'); ?>" maxlength="60"
                           placeholder="e.g., Ready to Transform Your Business?">
                    <p class="form-help">Main heading (max 60 characters)</p>
                </div>
                
                <div class="form-group">
                    <label for="homepage_cta_form_subtitle" class="form-label">Section Subtitle</label>
                    <textarea id="homepage_cta_form_subtitle" name="homepage_cta_form_subtitle" class="form-input form-textarea" 
                              rows="2" maxlength="150"
                              placeholder="e.g., Get in touch with us today and discover how we can streamline your operations"><?php echo htmlspecialchars($settings['homepage_cta_form_subtitle'] ?? 'Get in touch with us today and discover how we can streamline your operations'); ?></textarea>
                    <p class="form-help">Descriptive text below the title (max 150 characters)</p>
                </div>
            </div>
            
            <!-- Form Text -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Form Text</h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="homepage_cta_form_header" class="form-label">Form Header</label>
                        <input type="text" id="homepage_cta_form_header" name="homepage_cta_form_header" class="form-input" 
                               value="<?php echo htmlspecialchars($settings['homepage_cta_form_header'] ?? 'Get Started Today'); ?>" maxlength="40"
                               placeholder="e.g., Get Started Today">
                        <p class="form-help">Form section header (max 40 characters)</p>
                    </div>
                    <div class="form-group">
                        <label for="homepage_cta_form_header_subtitle" class="form-label">Form Header Subtitle</label>
                        <input type="text" id="homepage_cta_form_header_subtitle" name="homepage_cta_form_header_subtitle" class="form-input" 
                               value="<?php echo htmlspecialchars($settings['homepage_cta_form_header_subtitle'] ?? 'Fill out the form and we\'ll get back to you shortly'); ?>" maxlength="60"
                               placeholder="e.g., Fill out the form and we'll get back to you shortly">
                        <p class="form-help">Text below form header (max 60 characters)</p>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="homepage_cta_form_submit_text" class="form-label">Submit Button Text</label>
                        <input type="text" id="homepage_cta_form_submit_text" name="homepage_cta_form_submit_text" class="form-input" 
                               value="<?php echo htmlspecialchars($settings['homepage_cta_form_submit_text'] ?? 'Send Message'); ?>" maxlength="30"
                               placeholder="e.g., Send Message">
                        <p class="form-help">Button text (max 30 characters)</p>
                    </div>
                    <div class="form-group">
                        <label for="homepage_cta_form_privacy_text" class="form-label">Privacy Note</label>
                        <input type="text" id="homepage_cta_form_privacy_text" name="homepage_cta_form_privacy_text" class="form-input" 
                               value="<?php echo htmlspecialchars($settings['homepage_cta_form_privacy_text'] ?? 'Your information is secure and will never be shared'); ?>" maxlength="80"
                               placeholder="e.g., Your information is secure and will never be shared">
                        <p class="form-help">Privacy note below form (max 80 characters)</p>
                    </div>
                </div>
            </div>
            
            <!-- Colors -->
            <div class="form-subsection">
                <h4 class="form-subsection-title">Colors</h4>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="homepage_cta_form_accent_color" class="form-label">Accent Color</label>
                        <div class="color-input-group">
                            <input type="color" id="homepage_cta_form_accent_color" name="homepage_cta_form_accent_color" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['homepage_cta_form_accent_color'] ?? '#10b981'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="homepage_cta_form_accent_color"
                                   value="<?php echo htmlspecialchars($settings['homepage_cta_form_accent_color'] ?? '#10b981'); ?>" maxlength="7">
                        </div>
                        <p class="form-help">Used for badge text, icons, focus states</p>
                    </div>
                    <div class="form-group">
                        <label for="homepage_cta_form_badge_bg" class="form-label">Badge Background</label>
                        <div class="color-input-group">
                            <input type="color" id="homepage_cta_form_badge_bg" name="homepage_cta_form_badge_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['homepage_cta_form_badge_bg'] ?? '#10b981'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="homepage_cta_form_badge_bg"
                                   value="<?php echo htmlspecialchars($settings['homepage_cta_form_badge_bg'] ?? '#10b981'); ?>" maxlength="7">
                        </div>
                        <p class="form-help">Badge background color</p>
                    </div>
                    <div class="form-group">
                        <label for="homepage_cta_form_icon_bg" class="form-label">Feature Icon Background</label>
                        <div class="color-input-group">
                            <input type="color" id="homepage_cta_form_icon_bg" name="homepage_cta_form_icon_bg" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['homepage_cta_form_icon_bg'] ?? '#ffffff'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="homepage_cta_form_icon_bg"
                                   value="<?php echo htmlspecialchars($settings['homepage_cta_form_icon_bg'] ?? '#ffffff'); ?>" maxlength="7">
                        </div>
                        <p class="form-help">Feature icons background</p>
                    </div>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="homepage_cta_form_btn_gradient_start" class="form-label">Button Gradient Start</label>
                        <div class="color-input-group">
                            <input type="color" id="homepage_cta_form_btn_gradient_start" name="homepage_cta_form_btn_gradient_start" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['homepage_cta_form_btn_gradient_start'] ?? '#10b981'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="homepage_cta_form_btn_gradient_start"
                                   value="<?php echo htmlspecialchars($settings['homepage_cta_form_btn_gradient_start'] ?? '#10b981'); ?>" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="homepage_cta_form_btn_gradient_end" class="form-label">Button Gradient End</label>
                        <div class="color-input-group">
                            <input type="color" id="homepage_cta_form_btn_gradient_end" name="homepage_cta_form_btn_gradient_end" class="form-input-color" 
                                   value="<?php echo htmlspecialchars($settings['homepage_cta_form_btn_gradient_end'] ?? '#059669'); ?>">
                            <input type="text" class="form-input form-input-hex" data-color-target="homepage_cta_form_btn_gradient_end"
                                   value="<?php echo htmlspecialchars($settings['homepage_cta_form_btn_gradient_end'] ?? '#059669'); ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Settings</button>
            <a href="<?php echo get_app_base_url(); ?>/admin/dashboard.php" class="btn btn-secondary">Cancel</a>
            <button type="button" class="btn btn-outline" onclick="resetToDefaults()">Reset to Defaults</button>
        </div>
    </form>
</div>

<style>
.settings-nav {
    display: flex;
    gap: var(--spacing-2);
    margin-bottom: var(--spacing-6);
    border-bottom: 2px solid var(--color-gray-200);
    overflow-x: auto;
}

.settings-nav-item {
    padding: var(--spacing-3) var(--spacing-4);
    color: var(--color-gray-600);
    text-decoration: none;
    font-weight: var(--font-weight-semibold);
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all var(--transition-fast);
    white-space: nowrap;
}

.settings-nav-item:hover { color: var(--color-gray-900); }
.settings-nav-item.active { color: var(--color-primary); border-bottom-color: var(--color-primary); }

.admin-form { padding: var(--spacing-6); }

.form-section { margin-bottom: var(--spacing-8); }
.form-section:last-of-type { margin-bottom: var(--spacing-6); }

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
    margin: 0 0 var(--spacing-5) 0;
}

.form-subsection { margin-bottom: var(--spacing-6); padding: var(--spacing-4); background: var(--color-gray-50); border-radius: var(--radius-md); }
.form-subsection-title { font-size: var(--font-size-base); font-weight: var(--font-weight-semibold); color: var(--color-gray-800); margin: 0 0 var(--spacing-4) 0; }

.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--spacing-4); }
.form-row-3 { grid-template-columns: repeat(3, 1fr); }

.form-group { margin-bottom: var(--spacing-4); }
.form-label { display: block; font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold); color: var(--color-gray-700); margin-bottom: var(--spacing-2); }

.form-input, .form-textarea {
    width: 100%;
    padding: var(--spacing-3);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-size: var(--font-size-base);
    color: var(--color-gray-900);
    transition: border-color var(--transition-fast);
}

.form-input:focus, .form-textarea:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.form-help { font-size: var(--font-size-sm); color: var(--color-gray-600); margin: var(--spacing-2) 0 0 0; }

.form-checkbox-wrapper { display: flex; align-items: flex-start; gap: var(--spacing-3); cursor: pointer; }
.form-checkbox { width: 18px; height: 18px; margin-top: 2px; cursor: pointer; }
.form-checkbox-label-text { display: flex; flex-direction: column; gap: 2px; }
.form-checkbox-label-text strong { font-weight: var(--font-weight-semibold); color: var(--color-gray-900); }
.form-range { width: 100%; height: 8px; cursor: pointer; }

.color-input-group { display: flex; gap: var(--spacing-2); align-items: center; }
.form-input-color { width: 50px; height: 38px; border: 1px solid var(--color-gray-300); border-radius: var(--radius-md); cursor: pointer; padding: 2px; }
.form-input-hex { flex: 1; max-width: 100px; font-family: monospace; }

.form-actions { display: flex; gap: var(--spacing-3); padding-top: var(--spacing-4); border-top: 1px solid var(--color-gray-200); flex-wrap: wrap; }
.btn-outline { background: transparent; border: 1px solid var(--color-gray-300); color: var(--color-gray-700); }
.btn-outline:hover { background: var(--color-gray-100); }

.alert { padding: var(--spacing-4); border-radius: var(--radius-md); margin-bottom: var(--spacing-6); }
.alert-success { background-color: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
.alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

/* Preview Styles */
.preview-container { border: 1px solid var(--color-gray-300); border-radius: var(--radius-lg); overflow: hidden; }
.preview-section { padding: 30px 20px; transition: background 0.3s; }
.preview-header { text-align: center; margin-bottom: 20px; }

/* Why Choose Preview Styles */
.preview-why-choose-section { padding: 30px 20px; transition: background 0.3s; }
.preview-why-choose-title { font-size: 24px; font-weight: 600; margin: 0 0 8px 0; text-align: center; }
.preview-why-choose-subtitle { font-size: 14px; margin: 0 0 20px 0; text-align: center; }
.preview-why-choose-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.preview-why-choose-card { padding: 16px; border-radius: var(--radius-md); box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.3s; }
.preview-why-choose-card-image { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: var(--radius-md); margin-bottom: 12px; }
.preview-why-choose-card-content { }
.preview-why-choose-card-title { font-size: 16px; font-weight: 600; margin: 0 0 6px 0; }
.preview-why-choose-card-desc { font-size: 13px; margin: 0; }
.preview-title { font-size: 24px; font-weight: 300; margin: 0 0 8px 0; }
.preview-subtitle { font-size: 14px; margin: 0; }
.preview-nav { display: flex; justify-content: center; gap: 10px; padding: 12px; margin-bottom: 20px; border-radius: var(--radius-md); }
.preview-nav-link { padding: 8px 16px; font-size: 13px; cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.2s; }
.preview-nav-link.active { font-weight: 600; }
.preview-card { padding: 20px; border-radius: var(--radius-lg); margin-bottom: 20px; }
.preview-badge { display: inline-block; padding: 4px 10px; font-size: 11px; font-weight: 600; border-radius: 20px; margin-bottom: 10px; }
.preview-heading { font-size: 18px; font-weight: 600; margin: 0 0 8px 0; }
.preview-description { font-size: 14px; margin: 0 0 12px 0; }
.preview-features { list-style: none; padding: 0; margin: 0 0 12px 0; }
.preview-features li { font-size: 13px; margin-bottom: 4px; }
.preview-cta { font-size: 14px; font-weight: 600; text-decoration: none; }
.preview-btn-container { text-align: center; }
.preview-more-btn { display: inline-block; padding: 12px 24px; font-size: 14px; font-weight: 600; border-radius: 8px; cursor: pointer; transition: all 0.2s; }

@media (max-width: 992px) {
    .form-row, .form-row-3 { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .settings-nav { overflow-x: auto; }
    .admin-form { padding: var(--spacing-4); }
    .form-row, .form-row-3 { grid-template-columns: 1fr; }
    .form-actions { flex-direction: column; }
    .form-actions .btn { width: 100%; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sync color pickers with hex inputs
    document.querySelectorAll('.form-input-color').forEach(colorInput => {
        const hexInput = colorInput.nextElementSibling;
        if (hexInput && hexInput.classList.contains('form-input-hex')) {
            colorInput.addEventListener('input', function() {
                hexInput.value = this.value;
                updatePreview();
                updateCtaBannerPreview();
            });
            hexInput.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                    colorInput.value = this.value;
                    updatePreview();
                    updateCtaBannerPreview();
                }
            });
        }
    });

    // Update preview on text input changes
    document.getElementById('solutions_section_title').addEventListener('input', updatePreview);
    document.getElementById('solutions_section_subtitle').addEventListener('input', updatePreview);

    // Why Choose section preview updates
    const whyChooseFields = [
        'why_choose_section_title', 'why_choose_section_subtitle',
        'why_choose_section_bg', 'why_choose_title_color', 'why_choose_subtitle_color',
        'why_choose_card_bg', 'why_choose_card_title_color', 'why_choose_card_desc_color'
    ];
    whyChooseFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updateWhyChoosePreview);
        }
    });

    // CTA Banner preview updates
    const ctaFields = [
        'homepage_cta_banner_image_url', 'homepage_cta_banner_overlay_color', 
        'homepage_cta_banner_overlay_intensity', 'homepage_cta_banner_heading1', 
        'homepage_cta_banner_heading2', 'homepage_cta_banner_heading_color',
        'homepage_cta_banner_button_text', 'homepage_cta_banner_button_bg_color',
        'homepage_cta_banner_button_text_color'
    ];
    ctaFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updateCtaBannerPreview);
        }
    });

    // Overlay intensity slider value display
    const intensitySlider = document.getElementById('homepage_cta_banner_overlay_intensity');
    if (intensitySlider) {
        intensitySlider.addEventListener('input', function() {
            document.getElementById('cta_overlay_intensity_value').textContent = Math.round(this.value * 100) + '%';
            updateCtaBannerPreview();
        });
    }

    // Success Stories section preview updates
    const successStoriesFields = [
        'success_stories_section_title', 'success_stories_section_subtitle',
        'success_stories_bg_gradient_start', 'success_stories_bg_gradient_end',
        'success_stories_title_color', 'success_stories_subtitle_color',
        'success_stories_card_overlay', 'success_stories_card_title', 'success_stories_card_industry',
        'success_stories_card_desc', 'success_stories_card_badge_bg', 'success_stories_card_badge_text',
        'success_stories_card_btn_bg', 'success_stories_card_btn_text',
        'success_stories_view_all_bg', 'success_stories_view_all_text',
        'success_stories_view_all_icon_bg', 'success_stories_view_all_icon_hover_bg'
    ];
    successStoriesFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updateSuccessStoriesPreview);
        }
    });

    // Blog section preview updates
    const blogFields = [
        'blog_section_title', 'blog_section_subtitle', 'blog_section_theme',
        'blog_section_bg_start', 'blog_section_bg_end',
        'blog_title_color', 'blog_subtitle_color',
        'blog_card_bg', 'blog_card_border', 'blog_card_title_color', 'blog_card_excerpt_color',
        'blog_card_date_color', 'blog_card_tag_bg', 'blog_card_tag_text',
        'blog_card_link_color', 'blog_card_link_hover',
        'blog_view_all_bg', 'blog_view_all_border', 'blog_view_all_text', 'blog_view_all_icon_bg'
    ];
    blogFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updateBlogPreview);
        }
    });

    // Initial preview update
    updatePreview();
    updateCtaBannerPreview();
    updateWhyChoosePreview();
    updateSuccessStoriesPreview();
    updateBlogPreview();
});

function updateCtaBannerPreview() {
    const getValue = (id) => document.getElementById(id)?.value || '';
    
    // Update image
    const imageUrl = getValue('homepage_cta_banner_image_url') || 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1400&h=500&fit=crop';
    const previewImage = document.getElementById('cta-preview-image');
    if (previewImage) previewImage.src = imageUrl;
    
    // Update overlay
    const overlay = document.getElementById('cta-preview-overlay');
    if (overlay) {
        overlay.style.background = getValue('homepage_cta_banner_overlay_color') || 'rgba(0,0,0,0.5)';
        overlay.style.opacity = getValue('homepage_cta_banner_overlay_intensity') || '0.5';
    }
    
    // Update headings
    const headingColor = getValue('homepage_cta_banner_heading_color') || '#FFFFFF';
    const heading1 = document.getElementById('cta-preview-heading1');
    const heading2 = document.getElementById('cta-preview-heading2');
    if (heading1) {
        heading1.textContent = getValue('homepage_cta_banner_heading1') || 'Ready to transform your business?';
        heading1.style.color = headingColor;
    }
    if (heading2) {
        heading2.textContent = getValue('homepage_cta_banner_heading2') || 'Get started with our powerful solutions today!';
        heading2.style.color = headingColor;
    }
    
    // Update button
    const button = document.getElementById('cta-preview-button');
    if (button) {
        button.textContent = getValue('homepage_cta_banner_button_text') || 'Get Started Now';
        button.style.background = getValue('homepage_cta_banner_button_bg_color') || '#FFFFFF';
        button.style.color = getValue('homepage_cta_banner_button_text_color') || '#2563eb';
    }
}

function updateWhyChoosePreview() {
    const getValue = (id) => document.getElementById(id)?.value || '';
    
    // Update section background
    const section = document.getElementById('preview-why-choose-section');
    if (section) {
        section.style.background = getValue('why_choose_section_bg') || '#ffffff';
    }
    
    // Update title
    const title = document.getElementById('preview-why-choose-title');
    if (title) {
        title.textContent = getValue('why_choose_section_title') || 'Why Choose Karyalay?';
        title.style.color = getValue('why_choose_title_color') || '#111827';
    }
    
    // Update subtitle
    const subtitle = document.getElementById('preview-why-choose-subtitle');
    if (subtitle) {
        subtitle.textContent = getValue('why_choose_section_subtitle') || 'Everything you need to manage your business efficiently';
        subtitle.style.color = getValue('why_choose_subtitle_color') || '#6b7280';
    }
    
    // Update cards
    const cardBg = getValue('why_choose_card_bg') || '#ffffff';
    const cardTitleColor = getValue('why_choose_card_title_color') || '#111827';
    const cardDescColor = getValue('why_choose_card_desc_color') || '#6b7280';
    
    document.querySelectorAll('.preview-why-choose-card').forEach(card => {
        card.style.background = cardBg;
    });
    document.querySelectorAll('.preview-why-choose-card-title').forEach(el => {
        el.style.color = cardTitleColor;
    });
    document.querySelectorAll('.preview-why-choose-card-desc').forEach(el => {
        el.style.color = cardDescColor;
    });
}

function updateBlogPreview() {
    const getValue = (id) => document.getElementById(id)?.value || '';
    
    // Update section background gradient
    const section = document.getElementById('preview-blog-section');
    if (section) {
        const gradientStart = getValue('blog_section_bg_start') || '#f8fafc';
        const gradientEnd = getValue('blog_section_bg_end') || '#f1f5f9';
        section.style.background = `linear-gradient(135deg, ${gradientStart} 0%, ${gradientEnd} 100%)`;
    }
    
    // Update title
    const title = document.getElementById('preview-blog-title');
    if (title) {
        title.textContent = getValue('blog_section_title') || 'Latest from Our Blog';
        title.style.color = getValue('blog_title_color') || '#0f172a';
    }
    
    // Update subtitle
    const subtitle = document.getElementById('preview-blog-subtitle');
    if (subtitle) {
        subtitle.textContent = getValue('blog_section_subtitle') || 'Stay updated with the latest insights, tips, and news from our team';
        subtitle.style.color = getValue('blog_subtitle_color') || '#64748b';
    }
    
    // Update card
    const card = document.getElementById('preview-blog-card');
    if (card) {
        card.style.background = getValue('blog_card_bg') || '#ffffff';
        card.style.borderColor = getValue('blog_card_border') || '#e2e8f0';
    }
    
    // Update card elements
    const cardTitle = document.getElementById('preview-blog-card-title');
    if (cardTitle) {
        cardTitle.style.color = getValue('blog_card_title_color') || '#0f172a';
    }
    
    const excerpt = document.getElementById('preview-blog-excerpt');
    if (excerpt) {
        excerpt.style.color = getValue('blog_card_excerpt_color') || '#64748b';
    }
    
    const date = document.getElementById('preview-blog-date');
    if (date) {
        date.style.color = getValue('blog_card_date_color') || '#a855f7';
    }
    
    const tag = document.getElementById('preview-blog-tag');
    if (tag) {
        tag.style.background = getValue('blog_card_tag_bg') || '#f3e8ff';
        tag.style.color = getValue('blog_card_tag_text') || '#9333ea';
    }
    
    const link = document.getElementById('preview-blog-link');
    if (link) {
        link.style.color = getValue('blog_card_link_color') || '#a855f7';
    }
    
    // Update view all button
    const viewAll = document.getElementById('preview-blog-view-all');
    if (viewAll) {
        viewAll.style.background = getValue('blog_view_all_bg') || '#faf5ff';
        viewAll.style.borderColor = getValue('blog_view_all_border') || '#a855f7';
        viewAll.style.color = getValue('blog_view_all_text') || '#0f172a';
    }
    
    const viewAllIcon = document.getElementById('preview-blog-view-all-icon');
    if (viewAllIcon) {
        viewAllIcon.style.background = getValue('blog_view_all_icon_bg') || '#f3e8ff';
        viewAllIcon.style.color = getValue('blog_card_link_color') || '#a855f7';
    }
}

function updateSuccessStoriesPreview() {
    const getValue = (id) => document.getElementById(id)?.value || '';
    
    // Update section background gradient
    const section = document.getElementById('preview-success-stories-section');
    if (section) {
        const gradientStart = getValue('success_stories_bg_gradient_start') || '#1a1a2e';
        const gradientEnd = getValue('success_stories_bg_gradient_end') || '#16213e';
        section.style.background = `linear-gradient(135deg, ${gradientStart} 0%, ${gradientEnd} 100%)`;
    }
    
    // Update title
    const title = document.getElementById('preview-success-stories-title');
    if (title) {
        title.textContent = getValue('success_stories_section_title') || 'Success Stories';
        title.style.color = getValue('success_stories_title_color') || '#ffffff';
    }
    
    // Update subtitle
    const subtitle = document.getElementById('preview-success-stories-subtitle');
    if (subtitle) {
        subtitle.textContent = getValue('success_stories_section_subtitle') || 'See how businesses like yours are achieving remarkable results';
        subtitle.style.color = getValue('success_stories_subtitle_color') || '#b3b3b3';
    }
    
    // Update card elements
    const storyBadge = document.getElementById('preview-story-badge');
    if (storyBadge) {
        storyBadge.style.background = getValue('success_stories_card_badge_bg') || '#333333';
        storyBadge.style.color = getValue('success_stories_card_badge_text') || '#ffffff';
    }
    
    const storyTitle = document.getElementById('preview-story-title');
    if (storyTitle) {
        storyTitle.style.color = getValue('success_stories_card_title') || '#ffffff';
    }
    
    const storyIndustry = document.getElementById('preview-story-industry');
    if (storyIndustry) {
        storyIndustry.style.color = getValue('success_stories_card_industry') || '#cccccc';
    }
    
    // Update view all button
    const viewAll = document.getElementById('preview-view-all');
    if (viewAll) {
        viewAll.style.background = getValue('success_stories_view_all_bg') || '#0d0d0d';
        viewAll.style.color = getValue('success_stories_view_all_text') || '#ffffff';
    }
    
    const viewAllIcon = document.getElementById('preview-view-all-icon');
    if (viewAllIcon) {
        viewAllIcon.style.background = getValue('success_stories_view_all_icon_bg') || '#1a1a1a';
    }
}

function updatePreview() {
    const getValue = (id) => document.getElementById(id)?.value || '';
    
    // Update text
    document.getElementById('preview-title').textContent = getValue('solutions_section_title') || 'Powerful Solutions';
    document.getElementById('preview-subtitle').textContent = getValue('solutions_section_subtitle') || 'Section subtitle';
    
    // Update section background
    const bgStart = getValue('solutions_section_bg_start');
    const bgMid = getValue('solutions_section_bg_mid');
    const bgEnd = getValue('solutions_section_bg_end');
    document.getElementById('preview-section').style.background = `linear-gradient(180deg, ${bgStart} 0%, ${bgMid} 50%, ${bgEnd} 100%)`;
    
    // Update title colors
    document.getElementById('preview-title').style.color = getValue('solutions_title_color');
    document.getElementById('preview-subtitle').style.color = getValue('solutions_subtitle_color');
    
    // Update nav
    const nav = document.getElementById('preview-nav');
    nav.style.background = getValue('solutions_nav_bg');
    nav.style.border = `1px solid ${getValue('solutions_nav_border')}`;
    
    document.querySelectorAll('.preview-nav-link').forEach((link, i) => {
        if (link.classList.contains('active')) {
            link.style.color = getValue('solutions_nav_link_active');
            link.style.borderBottomColor = getValue('solutions_nav_active_border');
        } else {
            link.style.color = getValue('solutions_nav_link_color');
        }
    });
    
    // Update card
    const card = document.getElementById('preview-card');
    card.style.background = getValue('solutions_card_bg');
    card.style.border = `1px solid ${getValue('solutions_card_border')}`;
    
    document.getElementById('preview-badge').style.background = getValue('solutions_badge_bg');
    document.getElementById('preview-badge').style.color = getValue('solutions_badge_text');
    document.getElementById('preview-heading').style.color = getValue('solutions_heading_color');
    document.getElementById('preview-description').style.color = getValue('solutions_description_color');
    document.getElementById('preview-feature').style.color = getValue('solutions_feature_color');
    document.getElementById('preview-cta').style.color = getValue('solutions_cta_color');
    
    // Update button
    const btn = document.getElementById('preview-more-btn');
    btn.style.background = `linear-gradient(135deg, ${getValue('solutions_more_btn_bg_start')} 0%, ${getValue('solutions_more_btn_bg_end')} 100%)`;
    btn.style.color = getValue('solutions_more_btn_text');
}

function resetToDefaults() {
    if (!confirm('Reset all Homepage UI settings to defaults?')) return;
    
    const defaults = {
        'solutions_section_title': 'Powerful Solutions',
        'solutions_section_subtitle': 'Explore our comprehensive suite of business management solutions designed to streamline your operations',
        'solutions_section_bg_start': '#f7fef9',
        'solutions_section_bg_mid': '#f0fdf4',
        'solutions_section_bg_end': '#dcfce7',
        'solutions_title_color': '#111827',
        'solutions_subtitle_color': '#6b7280',
        'solutions_nav_bg': '#ffffff',
        'solutions_nav_border': '#e5e7eb',
        'solutions_nav_link_color': '#6b7280',
        'solutions_nav_link_hover': '#111827',
        'solutions_nav_link_active': '#111827',
        'solutions_nav_active_border': '#10b981',
        'solutions_card_bg': '#ffffff',
        'solutions_card_border': '#e5e7eb',
        'solutions_badge_bg': '#ecfdf5',
        'solutions_badge_text': '#059669',
        'solutions_heading_color': '#111827',
        'solutions_description_color': '#6b7280',
        'solutions_feature_color': '#374151',
        'solutions_cta_color': '#10b981',
        'solutions_cta_hover': '#059669',
        'solutions_more_btn_bg_start': '#10b981',
        'solutions_more_btn_bg_end': '#059669',
        'solutions_more_btn_text': '#ffffff',
        'solutions_more_btn_hover_start': '#059669',
        'solutions_more_btn_hover_end': '#047857',
        // Why Choose defaults
        'why_choose_section_title': 'Why Choose Karyalay?',
        'why_choose_section_subtitle': 'Everything you need to manage your business efficiently in one powerful platform',
        'why_choose_section_bg': '#ffffff',
        'why_choose_title_color': '#111827',
        'why_choose_subtitle_color': '#6b7280',
        'why_choose_card_bg': '#ffffff',
        'why_choose_card_title_color': '#111827',
        'why_choose_card_desc_color': '#6b7280',
        // Success Stories defaults
        'success_stories_section_title': 'Success Stories',
        'success_stories_section_subtitle': 'See how businesses like yours are achieving remarkable results with our solutions',
        'success_stories_bg_gradient_start': '#1a1a2e',
        'success_stories_bg_gradient_end': '#16213e',
        'success_stories_title_color': '#ffffff',
        'success_stories_subtitle_color': '#b3b3b3',
        'success_stories_card_overlay': '#000000',
        'success_stories_card_title': '#ffffff',
        'success_stories_card_industry': '#cccccc',
        'success_stories_card_desc': '#e6e6e6',
        'success_stories_card_badge_bg': '#333333',
        'success_stories_card_badge_text': '#ffffff',
        'success_stories_card_btn_bg': '#262626',
        'success_stories_card_btn_text': '#ffffff',
        'success_stories_view_all_bg': '#0d0d0d',
        'success_stories_view_all_text': '#ffffff',
        'success_stories_view_all_icon_bg': '#1a1a1a',
        'success_stories_view_all_icon_hover_bg': '#333333',
        // Blog section defaults
        'blog_section_title': 'Latest from Our Blog',
        'blog_section_subtitle': 'Stay updated with the latest insights, tips, and news from our team',
        'blog_section_theme': 'light',
        'blog_section_bg_start': '#f8fafc',
        'blog_section_bg_end': '#f1f5f9',
        'blog_title_color': '#0f172a',
        'blog_subtitle_color': '#64748b',
        'blog_card_bg': '#ffffff',
        'blog_card_border': '#e2e8f0',
        'blog_card_title_color': '#0f172a',
        'blog_card_excerpt_color': '#64748b',
        'blog_card_date_color': '#a855f7',
        'blog_card_tag_bg': '#f3e8ff',
        'blog_card_tag_text': '#9333ea',
        'blog_card_link_color': '#a855f7',
        'blog_card_link_hover': '#9333ea',
        'blog_view_all_bg': '#faf5ff',
        'blog_view_all_border': '#a855f7',
        'blog_view_all_text': '#0f172a',
        'blog_view_all_icon_bg': '#f3e8ff',
        // CTA Banner defaults
        'homepage_cta_banner_image_url': 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1400&h=500&fit=crop',
        'homepage_cta_banner_overlay_color': 'rgba(0,0,0,0.5)',
        'homepage_cta_banner_overlay_intensity': '0.5',
        'homepage_cta_banner_heading1': 'Ready to transform your business?',
        'homepage_cta_banner_heading2': 'Get started with our powerful solutions today!',
        'homepage_cta_banner_heading_color': '#FFFFFF',
        'homepage_cta_banner_button_text': 'Get Started Now',
        'homepage_cta_banner_button_link': '#contact-form',
        'homepage_cta_banner_button_bg_color': '#FFFFFF',
        'homepage_cta_banner_button_text_color': '#2563eb',
        // Testimonials defaults
        'homepage_testimonials_theme': 'light',
        'homepage_testimonials_heading': 'What Our Customers Say',
        'homepage_testimonials_subheading': 'Don\'t just take our word for it - hear from businesses that trust us',
        'homepage_testimonials_accent_color': '#10b981',
        // FAQ defaults
        'homepage_faq_theme': 'light',
        'homepage_faq_heading': 'Frequently Asked Questions',
        'homepage_faq_subheading': 'Everything you need to know about our platform. Can\'t find what you\'re looking for? Feel free to contact us.',
        'homepage_faq_items': '[{"question":"What is Karyalay?","answer":"Karyalay is a comprehensive business management platform that helps you streamline operations, manage customers, handle subscriptions, and grow your business efficiently."},{"question":"How do I get started?","answer":"Getting started is easy! Simply register for an account, choose your plan, and our team will help you set up your workspace. You can also request a demo to see the platform in action."},{"question":"Is there a free trial available?","answer":"Yes! We offer a 14-day free trial with full access to all features. No credit card required to start your trial."},{"question":"What kind of support do you offer?","answer":"We provide 24/7 customer support via email, chat, and phone. Our dedicated support team is always ready to help you with any questions or issues."}]',
        // CTA Form defaults
        'homepage_cta_form_theme': 'dark',
        'homepage_cta_form_title': 'Ready to Transform Your Business?',
        'homepage_cta_form_subtitle': 'Get in touch with us today and discover how we can streamline your operations',
        'homepage_cta_form_badge_text': 'Trusted by 500+ Businesses',
        'homepage_cta_form_header': 'Get Started Today',
        'homepage_cta_form_header_subtitle': 'Fill out the form and we\'ll get back to you shortly',
        'homepage_cta_form_submit_text': 'Send Message',
        'homepage_cta_form_privacy_text': 'Your information is secure and will never be shared',
        'homepage_cta_form_accent_color': '#10b981',
        'homepage_cta_form_btn_gradient_start': '#10b981',
        'homepage_cta_form_btn_gradient_end': '#059669',
        'homepage_cta_form_badge_bg': '#10b981',
        'homepage_cta_form_icon_bg': '#ffffff'
    };
    
    for (const [key, value] of Object.entries(defaults)) {
        const input = document.getElementById(key);
        if (input) {
            input.value = value;
            // Also update hex input if it's a color
            const hexInput = input.nextElementSibling;
            if (hexInput && hexInput.classList.contains('form-input-hex')) {
                hexInput.value = value;
            }
        }
    }
    
    // Reset checkboxes
    const ctaEnabledCheckbox = document.getElementById('homepage_cta_banner_enabled');
    if (ctaEnabledCheckbox) ctaEnabledCheckbox.checked = true;
    const faqEnabledCheckbox = document.getElementById('homepage_faq_enabled');
    if (faqEnabledCheckbox) faqEnabledCheckbox.checked = true;
    const successStoriesEnabledCheckbox = document.getElementById('success_stories_enabled');
    if (successStoriesEnabledCheckbox) successStoriesEnabledCheckbox.checked = true;
    const blogEnabledCheckbox = document.getElementById('blog_section_enabled');
    if (blogEnabledCheckbox) blogEnabledCheckbox.checked = true;
    const testimonialsEnabledCheckbox = document.getElementById('homepage_testimonials_enabled');
    if (testimonialsEnabledCheckbox) testimonialsEnabledCheckbox.checked = true;
    const ctaFormEnabledCheckbox = document.getElementById('homepage_cta_form_enabled');
    if (ctaFormEnabledCheckbox) ctaFormEnabledCheckbox.checked = true;
    
    // Update intensity display
    document.getElementById('cta_overlay_intensity_value').textContent = '50%';
    
    updatePreview();
    updateCtaBannerPreview();
    updateWhyChoosePreview();
    updateSuccessStoriesPreview();
    updateBlogPreview();
}

// FAQ Section Helper Functions
function addFaqItem() {
    const textarea = document.getElementById('homepage_faq_items');
    let items = [];
    try {
        items = JSON.parse(textarea.value || '[]');
    } catch (e) {
        items = [];
    }
    items.push({
        question: "New question?",
        answer: "Answer goes here."
    });
    textarea.value = JSON.stringify(items, null, 2);
}

function formatFaqJson() {
    const textarea = document.getElementById('homepage_faq_items');
    try {
        const items = JSON.parse(textarea.value || '[]');
        textarea.value = JSON.stringify(items, null, 2);
        alert('JSON formatted successfully!');
    } catch (e) {
        alert('Invalid JSON format. Please check your syntax.');
    }
}

function validateFaqJson() {
    const textarea = document.getElementById('homepage_faq_items');
    try {
        const items = JSON.parse(textarea.value || '[]');
        if (!Array.isArray(items)) {
            alert('Error: FAQs must be an array.');
            return;
        }
        let valid = true;
        items.forEach((item, index) => {
            if (!item.question || !item.answer) {
                alert(`Error: Item ${index + 1} is missing "question" or "answer" field.`);
                valid = false;
            }
        });
        if (valid) {
            alert(`Valid! ${items.length} FAQ item(s) found.`);
        }
    } catch (e) {
        alert('Invalid JSON format: ' + e.message);
    }
}

// FAQ heading character counters
document.getElementById('homepage_faq_heading')?.addEventListener('input', function() {
    document.getElementById('faq_heading_count').textContent = this.value.length;
});
document.getElementById('homepage_faq_subheading')?.addEventListener('input', function() {
    document.getElementById('faq_subheading_count').textContent = this.value.length;
});

// Testimonials heading character counters
document.getElementById('homepage_testimonials_heading')?.addEventListener('input', function() {
    document.getElementById('testimonials_heading_count').textContent = this.value.length;
});
document.getElementById('homepage_testimonials_subheading')?.addEventListener('input', function() {
    document.getElementById('testimonials_subheading_count').textContent = this.value.length;
});
</script>

<?php include_admin_footer(); ?>
