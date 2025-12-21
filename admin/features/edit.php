<?php
/**
 * Admin Edit Feature Page
 * Updated for normalized table structure (features, feature_styling)
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';
require_once __DIR__ . '/../../includes/template_helpers.php';

use Karyalay\Services\ContentService;
use Karyalay\Models\Feature;

startSecureSession();
require_admin();
require_permission('content.edit');

$contentService = new ContentService();

$feature_id = $_GET['id'] ?? '';

if (empty($feature_id)) {
    $_SESSION['admin_error'] = 'Feature ID is required.';
    header('Location: ' . get_app_base_url() . '/admin/features.php');
    exit;
}

$feature = $contentService->read('feature', $feature_id);

if (!$feature) {
    $_SESSION['admin_error'] = 'Feature not found.';
    header('Location: ' . get_app_base_url() . '/admin/features.php');
    exit;
}

// Ensure styling record exists for this feature
$featureModel = new Feature();
$featureModel->ensureStylingExists($feature_id);
// Re-fetch to get styling data
$feature = $contentService->read('feature', $feature_id);

$errors = [];

// Ensure JSON fields are arrays
$benefits = $feature['benefits'] ?? [];
if (is_string($benefits)) {
    $benefits = json_decode($benefits, true) ?? [];
}

$related_solutions = $feature['related_solutions'] ?? [];
if (is_string($related_solutions)) {
    $related_solutions = json_decode($related_solutions, true) ?? [];
}

$screenshots = $feature['screenshots'] ?? [];
if (is_string($screenshots)) {
    $screenshots = json_decode($screenshots, true) ?? [];
}

$form_data = [
    // Core fields
    'name' => $feature['name'],
    'slug' => $feature['slug'],
    'description' => $feature['description'] ?? '',
    'icon_image' => $feature['icon_image'] ?? '',
    'benefits' => $benefits,
    'related_solutions' => $related_solutions,
    'screenshots' => $screenshots,
    'display_order' => $feature['display_order'] ?? 0,
    'status' => $feature['status'] ?? 'DRAFT',
    
    // Hero styling fields
    'hero_bg_color' => $feature['hero_bg_color'] ?? '#fafafa',
    'hero_bg_gradient_start' => $feature['hero_bg_gradient_start'] ?? '#fafafa',
    'hero_bg_gradient_end' => $feature['hero_bg_gradient_end'] ?? '#f5f5f5',
    'hero_title_gradient_start' => $feature['hero_title_gradient_start'] ?? '#111827',
    'hero_title_gradient_middle' => $feature['hero_title_gradient_middle'] ?? '#667eea',
    'hero_title_gradient_end' => $feature['hero_title_gradient_end'] ?? '#764ba2',
    'hero_subtitle_color' => $feature['hero_subtitle_color'] ?? '#6b7280',
    'hero_cta_primary_text' => $feature['hero_cta_primary_text'] ?? 'Get Started',
    'hero_cta_primary_link' => $feature['hero_cta_primary_link'] ?? '#contact-form',
    'hero_cta_primary_bg_color' => $feature['hero_cta_primary_bg_color'] ?? '#667eea',
    'hero_cta_primary_text_color' => $feature['hero_cta_primary_text_color'] ?? '#FFFFFF',
    'hero_cta_secondary_text' => $feature['hero_cta_secondary_text'] ?? 'Learn how it works',
    'hero_cta_secondary_link' => $feature['hero_cta_secondary_link'] ?? '#how-it-works',
    'hero_cta_secondary_text_color' => $feature['hero_cta_secondary_text_color'] ?? '#374151',
    'hero_cta_secondary_border_color' => $feature['hero_cta_secondary_border_color'] ?? '#e5e7eb',
    'hero_stats_enabled' => $feature['hero_stats_enabled'] ?? true,
    'hero_stat1_value' => $feature['hero_stat1_value'] ?? '30+',
    'hero_stat1_label' => $feature['hero_stat1_label'] ?? 'Modules',
    'hero_stat2_value' => $feature['hero_stat2_value'] ?? '500+',
    'hero_stat2_label' => $feature['hero_stat2_label'] ?? 'Businesses',
    'hero_stat3_value' => $feature['hero_stat3_value'] ?? '24/7',
    'hero_stat3_label' => $feature['hero_stat3_label'] ?? 'Support',
    'hero_stats_value_color' => $feature['hero_stats_value_color'] ?? '#111827',
    'hero_stats_label_color' => $feature['hero_stats_label_color'] ?? '#9ca3af',
    
    // Key Benefits Section styling fields
    'benefits_section_enabled' => $feature['benefits_section_enabled'] ?? true,
    'benefits_section_heading1' => $feature['benefits_section_heading1'] ?? 'Why Choose',
    'benefits_section_heading2' => $feature['benefits_section_heading2'] ?? '',
    'benefits_section_subheading' => $feature['benefits_section_subheading'] ?? 'Discover the key advantages that make this feature essential for your business operations.',
    'benefits_section_bg_color' => $feature['benefits_section_bg_color'] ?? '#0f172a',
    'benefits_section_heading_color' => $feature['benefits_section_heading_color'] ?? '#FFFFFF',
    'benefits_section_subheading_color' => $feature['benefits_section_subheading_color'] ?? 'rgba(255,255,255,0.6)',
    'benefits_card_bg_color' => $feature['benefits_card_bg_color'] ?? 'rgba(255,255,255,0.06)',
    'benefits_card_border_color' => $feature['benefits_card_border_color'] ?? 'rgba(255,255,255,0.1)',
    'benefits_card_hover_bg_color' => $feature['benefits_card_hover_bg_color'] ?? '#667eea',
    'benefits_card_title_color' => $feature['benefits_card_title_color'] ?? '#FFFFFF',
    'benefits_card_text_color' => $feature['benefits_card_text_color'] ?? 'rgba(255,255,255,0.5)',
    'benefits_card_icon_color' => $feature['benefits_card_icon_color'] ?? 'rgba(255,255,255,0.5)',
    'benefits_card_hover_text_color' => $feature['benefits_card_hover_text_color'] ?? '#FFFFFF',
    'benefits_cards' => $feature['benefits_cards'] ?? [],
    
    // How It Works Section styling fields
    'how_it_works_enabled' => $feature['how_it_works_enabled'] ?? true,
    'how_it_works_badge' => $feature['how_it_works_badge'] ?? 'Simple Process',
    'how_it_works_heading' => $feature['how_it_works_heading'] ?? 'How It Works',
    'how_it_works_subheading' => $feature['how_it_works_subheading'] ?? 'Get started in four simple steps and transform your business operations',
    'how_it_works_bg_color' => $feature['how_it_works_bg_color'] ?? '#f9fafb',
    'how_it_works_badge_bg_color' => $feature['how_it_works_badge_bg_color'] ?? 'rgba(102, 126, 234, 0.1)',
    'how_it_works_badge_text_color' => $feature['how_it_works_badge_text_color'] ?? '#667eea',
    'how_it_works_heading_color' => $feature['how_it_works_heading_color'] ?? '#111827',
    'how_it_works_subheading_color' => $feature['how_it_works_subheading_color'] ?? '#6b7280',
    'how_it_works_card_bg_color' => $feature['how_it_works_card_bg_color'] ?? '#ffffff',
    'how_it_works_card_border_color' => $feature['how_it_works_card_border_color'] ?? '#e5e7eb',
    'how_it_works_card_hover_border_color' => $feature['how_it_works_card_hover_border_color'] ?? '#667eea',
    'how_it_works_step_color' => $feature['how_it_works_step_color'] ?? '#667eea',
    'how_it_works_step_bg_color' => $feature['how_it_works_step_bg_color'] ?? 'rgba(102, 126, 234, 0.1)',
    'how_it_works_title_color' => $feature['how_it_works_title_color'] ?? '#111827',
    'how_it_works_desc_color' => $feature['how_it_works_desc_color'] ?? '#6b7280',
    'how_it_works_connector_color' => $feature['how_it_works_connector_color'] ?? '#d1d5db',
    'how_it_works_steps' => $feature['how_it_works_steps'] ?? [],
    
    // Feature Highlights Section styling fields
    'highlights_enabled' => $feature['highlights_enabled'] ?? true,
    'highlights_badge' => $feature['highlights_badge'] ?? 'Capabilities',
    'highlights_heading' => $feature['highlights_heading'] ?? 'Feature Highlights',
    'highlights_subheading' => $feature['highlights_subheading'] ?? 'Powerful capabilities designed to streamline your business processes',
    'highlights_bg_color' => $feature['highlights_bg_color'] ?? 'linear-gradient(180deg, #f8fafc 0%, #fff 100%)',
    'highlights_badge_bg_color' => $feature['highlights_badge_bg_color'] ?? 'rgba(102, 126, 234, 0.1)',
    'highlights_badge_text_color' => $feature['highlights_badge_text_color'] ?? '#667eea',
    'highlights_heading_color' => $feature['highlights_heading_color'] ?? '#111827',
    'highlights_subheading_color' => $feature['highlights_subheading_color'] ?? '#6b7280',
    'highlights_card_bg_color' => $feature['highlights_card_bg_color'] ?? '#ffffff',
    'highlights_card_border_color' => $feature['highlights_card_border_color'] ?? '#e5e7eb',
    'highlights_card_hover_border_color' => $feature['highlights_card_hover_border_color'] ?? '#667eea',
    'highlights_icon_bg_color' => $feature['highlights_icon_bg_color'] ?? 'rgba(102, 126, 234, 0.1)',
    'highlights_icon_color' => $feature['highlights_icon_color'] ?? '#667eea',
    'highlights_title_color' => $feature['highlights_title_color'] ?? '#111827',
    'highlights_desc_color' => $feature['highlights_desc_color'] ?? '#6b7280',
    'highlights_cards' => $feature['highlights_cards'] ?? [],
    
    // Use Cases Section styling fields
    'use_cases_enabled' => $feature['use_cases_enabled'] ?? true,
    'use_cases_badge' => $feature['use_cases_badge'] ?? 'Industries',
    'use_cases_heading' => $feature['use_cases_heading'] ?? 'Use Cases',
    'use_cases_subheading' => $feature['use_cases_subheading'] ?? 'See how different industries leverage this feature to drive success',
    'use_cases_bg_color' => $feature['use_cases_bg_color'] ?? 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)',
    'use_cases_badge_bg_color' => $feature['use_cases_badge_bg_color'] ?? 'rgba(255, 255, 255, 0.1)',
    'use_cases_badge_text_color' => $feature['use_cases_badge_text_color'] ?? '#ffffff',
    'use_cases_heading_color' => $feature['use_cases_heading_color'] ?? '#ffffff',
    'use_cases_subheading_color' => $feature['use_cases_subheading_color'] ?? 'rgba(255, 255, 255, 0.7)',
    'use_cases_card_bg_color' => $feature['use_cases_card_bg_color'] ?? 'rgba(255, 255, 255, 0.05)',
    'use_cases_card_border_color' => $feature['use_cases_card_border_color'] ?? 'rgba(255, 255, 255, 0.1)',
    'use_cases_card_hover_border_color' => $feature['use_cases_card_hover_border_color'] ?? '#667eea',
    'use_cases_title_color' => $feature['use_cases_title_color'] ?? '#ffffff',
    'use_cases_desc_color' => $feature['use_cases_desc_color'] ?? 'rgba(255, 255, 255, 0.7)',
    'use_cases_overlay_color' => $feature['use_cases_overlay_color'] ?? 'linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%)',
    'use_cases_cards' => $feature['use_cases_cards'] ?? [],
    
    // FAQs Section styling fields
    'faqs_section_theme' => $feature['faqs_section_theme'] ?? 'light',
    'faqs_section_heading' => $feature['faqs_section_heading'] ?? 'Frequently Asked Questions',
    'faqs_section_subheading' => $feature['faqs_section_subheading'] ?? 'Everything you need to know about this feature. Can\'t find what you\'re looking for?',
    'faqs_cards' => $feature['faqs_cards'] ?? [],
];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        // Core fields
        $form_data['name'] = sanitizeString($_POST['name'] ?? '');
        $form_data['slug'] = sanitizeString($_POST['slug'] ?? '');
        $form_data['description'] = sanitizeString($_POST['description'] ?? '');
        $form_data['icon_image'] = sanitizeString($_POST['icon_image'] ?? '');
        $form_data['display_order'] = sanitizeInt($_POST['display_order'] ?? 0);
        $form_data['status'] = sanitizeString($_POST['status'] ?? 'DRAFT');
        
        // Hero styling fields
        $form_data['hero_bg_color'] = sanitizeString($_POST['hero_bg_color'] ?? '#fafafa');
        $form_data['hero_bg_gradient_start'] = sanitizeString($_POST['hero_bg_gradient_start'] ?? '#fafafa');
        $form_data['hero_bg_gradient_end'] = sanitizeString($_POST['hero_bg_gradient_end'] ?? '#f5f5f5');
        $form_data['hero_title_gradient_start'] = sanitizeString($_POST['hero_title_gradient_start'] ?? '#111827');
        $form_data['hero_title_gradient_middle'] = sanitizeString($_POST['hero_title_gradient_middle'] ?? '#667eea');
        $form_data['hero_title_gradient_end'] = sanitizeString($_POST['hero_title_gradient_end'] ?? '#764ba2');
        $form_data['hero_subtitle_color'] = sanitizeString($_POST['hero_subtitle_color'] ?? '#6b7280');
        $form_data['hero_cta_primary_text'] = sanitizeString($_POST['hero_cta_primary_text'] ?? 'Get Started');
        $form_data['hero_cta_primary_link'] = sanitizeString($_POST['hero_cta_primary_link'] ?? '#contact-form');
        $form_data['hero_cta_primary_bg_color'] = sanitizeString($_POST['hero_cta_primary_bg_color'] ?? '#667eea');
        $form_data['hero_cta_primary_text_color'] = sanitizeString($_POST['hero_cta_primary_text_color'] ?? '#FFFFFF');
        $form_data['hero_cta_secondary_text'] = sanitizeString($_POST['hero_cta_secondary_text'] ?? 'Learn how it works');
        $form_data['hero_cta_secondary_link'] = sanitizeString($_POST['hero_cta_secondary_link'] ?? '#how-it-works');
        $form_data['hero_cta_secondary_text_color'] = sanitizeString($_POST['hero_cta_secondary_text_color'] ?? '#374151');
        $form_data['hero_cta_secondary_border_color'] = sanitizeString($_POST['hero_cta_secondary_border_color'] ?? '#e5e7eb');
        $form_data['hero_stats_enabled'] = isset($_POST['hero_stats_enabled']) ? true : false;
        $form_data['hero_stat1_value'] = sanitizeString($_POST['hero_stat1_value'] ?? '30+');
        $form_data['hero_stat1_label'] = sanitizeString($_POST['hero_stat1_label'] ?? 'Modules');
        $form_data['hero_stat2_value'] = sanitizeString($_POST['hero_stat2_value'] ?? '500+');
        $form_data['hero_stat2_label'] = sanitizeString($_POST['hero_stat2_label'] ?? 'Businesses');
        $form_data['hero_stat3_value'] = sanitizeString($_POST['hero_stat3_value'] ?? '24/7');
        $form_data['hero_stat3_label'] = sanitizeString($_POST['hero_stat3_label'] ?? 'Support');
        $form_data['hero_stats_value_color'] = sanitizeString($_POST['hero_stats_value_color'] ?? '#111827');
        $form_data['hero_stats_label_color'] = sanitizeString($_POST['hero_stats_label_color'] ?? '#9ca3af');
        
        // Key Benefits Section fields
        $form_data['benefits_section_enabled'] = isset($_POST['benefits_section_enabled']) ? true : false;
        $form_data['benefits_section_heading1'] = substr(sanitizeString($_POST['benefits_section_heading1'] ?? 'Why Choose'), 0, 50);
        $form_data['benefits_section_heading2'] = substr(sanitizeString($_POST['benefits_section_heading2'] ?? ''), 0, 50);
        $form_data['benefits_section_subheading'] = substr(sanitizeString($_POST['benefits_section_subheading'] ?? ''), 0, 255);
        $form_data['benefits_section_bg_color'] = sanitizeString($_POST['benefits_section_bg_color'] ?? '#0f172a');
        $form_data['benefits_section_heading_color'] = sanitizeString($_POST['benefits_section_heading_color'] ?? '#FFFFFF');
        $form_data['benefits_section_subheading_color'] = sanitizeString($_POST['benefits_section_subheading_color'] ?? 'rgba(255,255,255,0.6)');
        $form_data['benefits_card_bg_color'] = sanitizeString($_POST['benefits_card_bg_color'] ?? 'rgba(255,255,255,0.06)');
        $form_data['benefits_card_border_color'] = sanitizeString($_POST['benefits_card_border_color'] ?? 'rgba(255,255,255,0.1)');
        $form_data['benefits_card_hover_bg_color'] = sanitizeString($_POST['benefits_card_hover_bg_color'] ?? '#667eea');
        $form_data['benefits_card_title_color'] = sanitizeString($_POST['benefits_card_title_color'] ?? '#FFFFFF');
        $form_data['benefits_card_text_color'] = sanitizeString($_POST['benefits_card_text_color'] ?? 'rgba(255,255,255,0.5)');
        $form_data['benefits_card_icon_color'] = sanitizeString($_POST['benefits_card_icon_color'] ?? 'rgba(255,255,255,0.5)');
        $form_data['benefits_card_hover_text_color'] = sanitizeString($_POST['benefits_card_hover_text_color'] ?? '#FFFFFF');
        
        // Process benefits_cards JSON field
        if (!empty($_POST['benefits_cards'])) {
            $decoded = json_decode($_POST['benefits_cards'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $form_data['benefits_cards'] = $decoded;
            } else {
                $errors[] = 'Invalid JSON format for Benefits Cards.';
                $form_data['benefits_cards'] = [];
            }
        } else {
            $form_data['benefits_cards'] = [];
        }
        
        // How It Works Section fields
        $form_data['how_it_works_enabled'] = isset($_POST['how_it_works_enabled']) ? true : false;
        $form_data['how_it_works_badge'] = substr(sanitizeString($_POST['how_it_works_badge'] ?? 'Simple Process'), 0, 50);
        $form_data['how_it_works_heading'] = substr(sanitizeString($_POST['how_it_works_heading'] ?? 'How It Works'), 0, 100);
        $form_data['how_it_works_subheading'] = substr(sanitizeString($_POST['how_it_works_subheading'] ?? ''), 0, 255);
        $form_data['how_it_works_bg_color'] = sanitizeString($_POST['how_it_works_bg_color'] ?? '#f9fafb');
        $form_data['how_it_works_badge_bg_color'] = sanitizeString($_POST['how_it_works_badge_bg_color'] ?? 'rgba(102, 126, 234, 0.1)');
        $form_data['how_it_works_badge_text_color'] = sanitizeString($_POST['how_it_works_badge_text_color'] ?? '#667eea');
        $form_data['how_it_works_heading_color'] = sanitizeString($_POST['how_it_works_heading_color'] ?? '#111827');
        $form_data['how_it_works_subheading_color'] = sanitizeString($_POST['how_it_works_subheading_color'] ?? '#6b7280');
        $form_data['how_it_works_card_bg_color'] = sanitizeString($_POST['how_it_works_card_bg_color'] ?? '#ffffff');
        $form_data['how_it_works_card_border_color'] = sanitizeString($_POST['how_it_works_card_border_color'] ?? '#e5e7eb');
        $form_data['how_it_works_card_hover_border_color'] = sanitizeString($_POST['how_it_works_card_hover_border_color'] ?? '#667eea');
        $form_data['how_it_works_step_color'] = sanitizeString($_POST['how_it_works_step_color'] ?? '#667eea');
        $form_data['how_it_works_step_bg_color'] = sanitizeString($_POST['how_it_works_step_bg_color'] ?? 'rgba(102, 126, 234, 0.1)');
        $form_data['how_it_works_title_color'] = sanitizeString($_POST['how_it_works_title_color'] ?? '#111827');
        $form_data['how_it_works_desc_color'] = sanitizeString($_POST['how_it_works_desc_color'] ?? '#6b7280');
        $form_data['how_it_works_connector_color'] = sanitizeString($_POST['how_it_works_connector_color'] ?? '#d1d5db');
        
        // Process how_it_works_steps JSON field
        if (!empty($_POST['how_it_works_steps'])) {
            $decoded = json_decode($_POST['how_it_works_steps'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $form_data['how_it_works_steps'] = $decoded;
            } else {
                $errors[] = 'Invalid JSON format for How It Works Steps.';
                $form_data['how_it_works_steps'] = [];
            }
        } else {
            $form_data['how_it_works_steps'] = [];
        }
        
        // Feature Highlights Section fields
        $form_data['highlights_enabled'] = isset($_POST['highlights_enabled']) ? true : false;
        $form_data['highlights_badge'] = substr(sanitizeString($_POST['highlights_badge'] ?? 'Capabilities'), 0, 50);
        $form_data['highlights_heading'] = substr(sanitizeString($_POST['highlights_heading'] ?? 'Feature Highlights'), 0, 100);
        $form_data['highlights_subheading'] = substr(sanitizeString($_POST['highlights_subheading'] ?? ''), 0, 255);
        $form_data['highlights_bg_color'] = sanitizeString($_POST['highlights_bg_color'] ?? 'linear-gradient(180deg, #f8fafc 0%, #fff 100%)');
        $form_data['highlights_badge_bg_color'] = sanitizeString($_POST['highlights_badge_bg_color'] ?? 'rgba(102, 126, 234, 0.1)');
        $form_data['highlights_badge_text_color'] = sanitizeString($_POST['highlights_badge_text_color'] ?? '#667eea');
        $form_data['highlights_heading_color'] = sanitizeString($_POST['highlights_heading_color'] ?? '#111827');
        $form_data['highlights_subheading_color'] = sanitizeString($_POST['highlights_subheading_color'] ?? '#6b7280');
        $form_data['highlights_card_bg_color'] = sanitizeString($_POST['highlights_card_bg_color'] ?? '#ffffff');
        $form_data['highlights_card_border_color'] = sanitizeString($_POST['highlights_card_border_color'] ?? '#e5e7eb');
        $form_data['highlights_card_hover_border_color'] = sanitizeString($_POST['highlights_card_hover_border_color'] ?? '#667eea');
        $form_data['highlights_icon_bg_color'] = sanitizeString($_POST['highlights_icon_bg_color'] ?? 'rgba(102, 126, 234, 0.1)');
        $form_data['highlights_icon_color'] = sanitizeString($_POST['highlights_icon_color'] ?? '#667eea');
        $form_data['highlights_title_color'] = sanitizeString($_POST['highlights_title_color'] ?? '#111827');
        $form_data['highlights_desc_color'] = sanitizeString($_POST['highlights_desc_color'] ?? '#6b7280');
        
        // Process highlights_cards JSON field
        if (!empty($_POST['highlights_cards'])) {
            $decoded = json_decode($_POST['highlights_cards'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $form_data['highlights_cards'] = $decoded;
            } else {
                $errors[] = 'Invalid JSON format for Feature Highlights Cards.';
                $form_data['highlights_cards'] = [];
            }
        } else {
            $form_data['highlights_cards'] = [];
        }
        
        // Use Cases Section fields
        $form_data['use_cases_enabled'] = isset($_POST['use_cases_enabled']) ? true : false;
        $form_data['use_cases_badge'] = substr(sanitizeString($_POST['use_cases_badge'] ?? 'Industries'), 0, 50);
        $form_data['use_cases_heading'] = substr(sanitizeString($_POST['use_cases_heading'] ?? 'Use Cases'), 0, 100);
        $form_data['use_cases_subheading'] = substr(sanitizeString($_POST['use_cases_subheading'] ?? ''), 0, 255);
        $form_data['use_cases_bg_color'] = sanitizeString($_POST['use_cases_bg_color'] ?? 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)');
        $form_data['use_cases_badge_bg_color'] = sanitizeString($_POST['use_cases_badge_bg_color'] ?? 'rgba(255, 255, 255, 0.1)');
        $form_data['use_cases_badge_text_color'] = sanitizeString($_POST['use_cases_badge_text_color'] ?? '#ffffff');
        $form_data['use_cases_heading_color'] = sanitizeString($_POST['use_cases_heading_color'] ?? '#ffffff');
        $form_data['use_cases_subheading_color'] = sanitizeString($_POST['use_cases_subheading_color'] ?? 'rgba(255, 255, 255, 0.7)');
        $form_data['use_cases_card_bg_color'] = sanitizeString($_POST['use_cases_card_bg_color'] ?? 'rgba(255, 255, 255, 0.05)');
        $form_data['use_cases_card_border_color'] = sanitizeString($_POST['use_cases_card_border_color'] ?? 'rgba(255, 255, 255, 0.1)');
        $form_data['use_cases_card_hover_border_color'] = sanitizeString($_POST['use_cases_card_hover_border_color'] ?? '#667eea');
        $form_data['use_cases_title_color'] = sanitizeString($_POST['use_cases_title_color'] ?? '#ffffff');
        $form_data['use_cases_desc_color'] = sanitizeString($_POST['use_cases_desc_color'] ?? 'rgba(255, 255, 255, 0.7)');
        $form_data['use_cases_overlay_color'] = sanitizeString($_POST['use_cases_overlay_color'] ?? 'linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%)');
        
        // Process use_cases_cards JSON field
        if (!empty($_POST['use_cases_cards'])) {
            $decoded = json_decode($_POST['use_cases_cards'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $form_data['use_cases_cards'] = $decoded;
            } else {
                $errors[] = 'Invalid JSON format for Use Cases Cards.';
                $form_data['use_cases_cards'] = [];
            }
        } else {
            $form_data['use_cases_cards'] = [];
        }
        
        // Process FAQs Section fields
        $form_data['faqs_section_theme'] = in_array($_POST['faqs_section_theme'] ?? 'light', ['light', 'dark']) 
            ? $_POST['faqs_section_theme'] 
            : 'light';
        $form_data['faqs_section_heading'] = substr(sanitizeString($_POST['faqs_section_heading'] ?? ''), 0, 100);
        $form_data['faqs_section_subheading'] = substr(sanitizeString($_POST['faqs_section_subheading'] ?? ''), 0, 200);
        
        // Process faqs_cards JSON field
        if (!empty($_POST['faqs_cards'])) {
            $decoded = json_decode($_POST['faqs_cards'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $form_data['faqs_cards'] = $decoded;
            } else {
                $errors[] = 'Invalid JSON format for FAQs Cards.';
                $form_data['faqs_cards'] = [];
            }
        } else {
            $form_data['faqs_cards'] = [];
        }
        
        if (empty($form_data['name'])) {
            $errors[] = 'Feature name is required.';
        }
        
        if (empty($form_data['slug'])) {
            $errors[] = 'Feature slug is required.';
        }
        
        if (!in_array($form_data['status'], ['DRAFT', 'PUBLISHED', 'ARCHIVED'])) {
            $errors[] = 'Invalid status value.';
        }
        
        if (!empty($_POST['benefits'])) {
            $benefits_raw = explode("\n", $_POST['benefits']);
            $form_data['benefits'] = array_filter(array_map(function($benefit) {
                return trim(sanitizeString($benefit));
            }, $benefits_raw));
        } else {
            $form_data['benefits'] = [];
        }
        
        if (!empty($_POST['related_solutions'])) {
            $solutions_raw = explode("\n", $_POST['related_solutions']);
            $form_data['related_solutions'] = array_filter(array_map(function($solution) {
                return trim(sanitizeString($solution));
            }, $solutions_raw));
        } else {
            $form_data['related_solutions'] = [];
        }
        
        if (!empty($_POST['screenshots'])) {
            $screenshots_raw = explode("\n", $_POST['screenshots']);
            $form_data['screenshots'] = array_filter(array_map(function($url) {
                return trim(sanitizeString($url));
            }, $screenshots_raw));
        } else {
            $form_data['screenshots'] = [];
        }
        
        if (empty($errors)) {
            $result = $contentService->update('feature', $feature_id, $form_data);
            
            if ($result) {
                $_SESSION['admin_success'] = 'Feature updated successfully!';
                header('Location: ' . get_app_base_url() . '/admin/features.php');
                exit;
            } else {
                $errors[] = 'Failed to update feature. Please check if the slug is unique.';
            }
        }
    }
}

$csrf_token = getCsrfToken();
include_admin_header('Edit Feature');
?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <nav class="admin-breadcrumb">
            <a href="<?php echo get_app_base_url(); ?>/admin/features.php">Features</a>
            <span class="breadcrumb-separator">/</span>
            <span>Edit Feature</span>
        </nav>
        <h1 class="admin-page-title">Edit Feature</h1>
        <p class="admin-page-description">Update feature information and hero section styling</p>
    </div>
    <div class="admin-page-header-actions">
        <a href="<?php echo get_base_url(); ?>/feature/<?php echo urlencode($form_data['slug']); ?>" class="btn btn-outline" target="_blank">Preview →</a>
        <a href="<?php echo get_app_base_url(); ?>/admin/features.php" class="btn btn-secondary">← Back to Features</a>
        <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete Feature</button>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>Error:</strong>
        <ul><?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" action="<?php echo get_app_base_url(); ?>/admin/features/edit.php?id=<?php echo urlencode($feature_id); ?>" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-section">
            <h2 class="form-section-title">Basic Information</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label required">Feature Name</label>
                    <input type="text" id="name" name="name" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['name']); ?>" 
                        required maxlength="255" placeholder="e.g., Advanced Reporting">
                    <p class="form-help">The display name of the feature</p>
                </div>
                
                <div class="form-group">
                    <label for="slug" class="form-label required">Slug</label>
                    <input type="text" id="slug" name="slug" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['slug']); ?>" 
                        required pattern="[a-z0-9\-]+" maxlength="255" placeholder="e.g., advanced-reporting">
                    <p class="form-help">URL-friendly identifier</p>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="4"
                    placeholder="Brief description of what this feature does..."><?php echo htmlspecialchars($form_data['description']); ?></textarea>
                <p class="form-help">Brief description of the feature (shown in hero subtitle)</p>
            </div>
            
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
                <p class="form-help">URL to a PNG icon image (recommended: 64x64 or 128x128 pixels)</p>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="display_order" class="form-label">Display Order</label>
                    <input type="number" id="display_order" name="display_order" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['display_order']); ?>" min="0" placeholder="0">
                    <p class="form-help">Lower numbers appear first</p>
                </div>
                
                <div class="form-group">
                    <label for="status" class="form-label required">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="DRAFT" <?php echo $form_data['status'] === 'DRAFT' ? 'selected' : ''; ?>>Draft</option>
                        <option value="PUBLISHED" <?php echo $form_data['status'] === 'PUBLISHED' ? 'selected' : ''; ?>>Published</option>
                        <option value="ARCHIVED" <?php echo $form_data['status'] === 'ARCHIVED' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                    <p class="form-help">Only published features appear on the website</p>
                </div>
            </div>
        </div>

        
        <!-- Hero Section Styling -->
        <div class="form-section">
            <h2 class="form-section-title">🎨 Hero Section Styling</h2>
            <p class="form-section-desc">Customize the appearance of the hero section on the feature detail page</p>
            
            <h3 class="form-subsection-title">Background Colors</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="hero_bg_color" class="form-label">Background Color</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_bg_color_picker" value="<?php echo htmlspecialchars($form_data['hero_bg_color']); ?>" 
                            onchange="document.getElementById('hero_bg_color').value = this.value">
                        <input type="text" id="hero_bg_color" name="hero_bg_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_bg_color']); ?>" placeholder="#fafafa">
                    </div>
                </div>
                <div class="form-group">
                    <label for="hero_bg_gradient_start" class="form-label">Gradient Start</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_bg_gradient_start_picker" value="<?php echo htmlspecialchars($form_data['hero_bg_gradient_start']); ?>" 
                            onchange="document.getElementById('hero_bg_gradient_start').value = this.value">
                        <input type="text" id="hero_bg_gradient_start" name="hero_bg_gradient_start" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_bg_gradient_start']); ?>" placeholder="#fafafa">
                    </div>
                </div>
                <div class="form-group">
                    <label for="hero_bg_gradient_end" class="form-label">Gradient End</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_bg_gradient_end_picker" value="<?php echo htmlspecialchars($form_data['hero_bg_gradient_end']); ?>" 
                            onchange="document.getElementById('hero_bg_gradient_end').value = this.value">
                        <input type="text" id="hero_bg_gradient_end" name="hero_bg_gradient_end" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_bg_gradient_end']); ?>" placeholder="#f5f5f5">
                    </div>
                </div>
            </div>
            
            <h3 class="form-subsection-title">Title Gradient Colors</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="hero_title_gradient_start" class="form-label">Title Gradient Start</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_title_gradient_start_picker" value="<?php echo htmlspecialchars($form_data['hero_title_gradient_start']); ?>" 
                            onchange="document.getElementById('hero_title_gradient_start').value = this.value">
                        <input type="text" id="hero_title_gradient_start" name="hero_title_gradient_start" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_title_gradient_start']); ?>" placeholder="#111827">
                    </div>
                </div>
                <div class="form-group">
                    <label for="hero_title_gradient_middle" class="form-label">Title Gradient Middle</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_title_gradient_middle_picker" value="<?php echo htmlspecialchars($form_data['hero_title_gradient_middle']); ?>" 
                            onchange="document.getElementById('hero_title_gradient_middle').value = this.value">
                        <input type="text" id="hero_title_gradient_middle" name="hero_title_gradient_middle" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_title_gradient_middle']); ?>" placeholder="#667eea">
                    </div>
                </div>
                <div class="form-group">
                    <label for="hero_title_gradient_end" class="form-label">Title Gradient End</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_title_gradient_end_picker" value="<?php echo htmlspecialchars($form_data['hero_title_gradient_end']); ?>" 
                            onchange="document.getElementById('hero_title_gradient_end').value = this.value">
                        <input type="text" id="hero_title_gradient_end" name="hero_title_gradient_end" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_title_gradient_end']); ?>" placeholder="#764ba2">
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="hero_subtitle_color" class="form-label">Subtitle Color</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_subtitle_color_picker" value="<?php echo htmlspecialchars($form_data['hero_subtitle_color']); ?>" 
                            onchange="document.getElementById('hero_subtitle_color').value = this.value">
                        <input type="text" id="hero_subtitle_color" name="hero_subtitle_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_subtitle_color']); ?>" placeholder="#6b7280">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hero CTA Buttons -->
        <div class="form-section">
            <h2 class="form-section-title">🔘 Hero CTA Buttons</h2>
            
            <h3 class="form-subsection-title">Primary Button</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="hero_cta_primary_text" class="form-label">Button Text</label>
                    <input type="text" id="hero_cta_primary_text" name="hero_cta_primary_text" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['hero_cta_primary_text']); ?>" placeholder="Get Started">
                </div>
                <div class="form-group">
                    <label for="hero_cta_primary_link" class="form-label">Button Link</label>
                    <input type="text" id="hero_cta_primary_link" name="hero_cta_primary_link" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['hero_cta_primary_link']); ?>" placeholder="#contact-form">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="hero_cta_primary_bg_color" class="form-label">Background Color</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_cta_primary_bg_color_picker" value="<?php echo htmlspecialchars($form_data['hero_cta_primary_bg_color']); ?>" 
                            onchange="document.getElementById('hero_cta_primary_bg_color').value = this.value">
                        <input type="text" id="hero_cta_primary_bg_color" name="hero_cta_primary_bg_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_cta_primary_bg_color']); ?>" placeholder="#667eea">
                    </div>
                </div>
                <div class="form-group">
                    <label for="hero_cta_primary_text_color" class="form-label">Text Color</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_cta_primary_text_color_picker" value="<?php echo htmlspecialchars($form_data['hero_cta_primary_text_color']); ?>" 
                            onchange="document.getElementById('hero_cta_primary_text_color').value = this.value">
                        <input type="text" id="hero_cta_primary_text_color" name="hero_cta_primary_text_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_cta_primary_text_color']); ?>" placeholder="#FFFFFF">
                    </div>
                </div>
            </div>
            
            <h3 class="form-subsection-title">Secondary Button</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="hero_cta_secondary_text" class="form-label">Button Text</label>
                    <input type="text" id="hero_cta_secondary_text" name="hero_cta_secondary_text" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['hero_cta_secondary_text']); ?>" placeholder="Learn how it works">
                </div>
                <div class="form-group">
                    <label for="hero_cta_secondary_link" class="form-label">Button Link</label>
                    <input type="text" id="hero_cta_secondary_link" name="hero_cta_secondary_link" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['hero_cta_secondary_link']); ?>" placeholder="#how-it-works">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="hero_cta_secondary_text_color" class="form-label">Text Color</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_cta_secondary_text_color_picker" value="<?php echo htmlspecialchars($form_data['hero_cta_secondary_text_color']); ?>" 
                            onchange="document.getElementById('hero_cta_secondary_text_color').value = this.value">
                        <input type="text" id="hero_cta_secondary_text_color" name="hero_cta_secondary_text_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_cta_secondary_text_color']); ?>" placeholder="#374151">
                    </div>
                </div>
                <div class="form-group">
                    <label for="hero_cta_secondary_border_color" class="form-label">Border Color</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_cta_secondary_border_color_picker" value="<?php echo htmlspecialchars($form_data['hero_cta_secondary_border_color']); ?>" 
                            onchange="document.getElementById('hero_cta_secondary_border_color').value = this.value">
                        <input type="text" id="hero_cta_secondary_border_color" name="hero_cta_secondary_border_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_cta_secondary_border_color']); ?>" placeholder="#e5e7eb">
                    </div>
                </div>
            </div>
        </div>

        
        <!-- Hero Stats Section -->
        <div class="form-section">
            <h2 class="form-section-title">📊 Hero Stats</h2>
            
            <div class="form-group">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="hero_stats_enabled" value="1" <?php echo $form_data['hero_stats_enabled'] ? 'checked' : ''; ?>>
                    <span>Enable Stats Section</span>
                </label>
            </div>
            
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="hero_stat1_value" class="form-label">Stat 1 Value</label>
                    <input type="text" id="hero_stat1_value" name="hero_stat1_value" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['hero_stat1_value']); ?>" placeholder="30+">
                </div>
                <div class="form-group">
                    <label for="hero_stat2_value" class="form-label">Stat 2 Value</label>
                    <input type="text" id="hero_stat2_value" name="hero_stat2_value" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['hero_stat2_value']); ?>" placeholder="500+">
                </div>
                <div class="form-group">
                    <label for="hero_stat3_value" class="form-label">Stat 3 Value</label>
                    <input type="text" id="hero_stat3_value" name="hero_stat3_value" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['hero_stat3_value']); ?>" placeholder="24/7">
                </div>
            </div>
            
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="hero_stat1_label" class="form-label">Stat 1 Label</label>
                    <input type="text" id="hero_stat1_label" name="hero_stat1_label" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['hero_stat1_label']); ?>" placeholder="Modules">
                </div>
                <div class="form-group">
                    <label for="hero_stat2_label" class="form-label">Stat 2 Label</label>
                    <input type="text" id="hero_stat2_label" name="hero_stat2_label" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['hero_stat2_label']); ?>" placeholder="Businesses">
                </div>
                <div class="form-group">
                    <label for="hero_stat3_label" class="form-label">Stat 3 Label</label>
                    <input type="text" id="hero_stat3_label" name="hero_stat3_label" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['hero_stat3_label']); ?>" placeholder="Support">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="hero_stats_value_color" class="form-label">Stats Value Color</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_stats_value_color_picker" value="<?php echo htmlspecialchars($form_data['hero_stats_value_color']); ?>" 
                            onchange="document.getElementById('hero_stats_value_color').value = this.value">
                        <input type="text" id="hero_stats_value_color" name="hero_stats_value_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_stats_value_color']); ?>" placeholder="#111827">
                    </div>
                </div>
                <div class="form-group">
                    <label for="hero_stats_label_color" class="form-label">Stats Label Color</label>
                    <div class="color-input-group">
                        <input type="color" id="hero_stats_label_color_picker" value="<?php echo htmlspecialchars($form_data['hero_stats_label_color']); ?>" 
                            onchange="document.getElementById('hero_stats_label_color').value = this.value">
                        <input type="text" id="hero_stats_label_color" name="hero_stats_label_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['hero_stats_label_color']); ?>" placeholder="#9ca3af">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Key Benefits Section Styling -->
        <div class="form-section">
            <h2 class="form-section-title">🎯 Key Benefits Section (After Hero)</h2>
            <p class="form-section-desc">A Razorpay-style section with interactive cards that show key benefits on hover.</p>
            
            <div class="form-group">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="benefits_section_enabled" value="1" <?php echo $form_data['benefits_section_enabled'] ? 'checked' : ''; ?>>
                    <span>Enable Key Benefits Section</span>
                </label>
                <p class="form-help">Show this section right after the hero</p>
            </div>
            
            <h3 class="form-subsection-title">Section Headings</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="benefits_section_heading1" class="form-label">Heading Line 1</label>
                    <input type="text" id="benefits_section_heading1" name="benefits_section_heading1" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['benefits_section_heading1']); ?>" maxlength="50"
                        placeholder="e.g., Why Choose">
                    <p class="form-help">First line of the section heading</p>
                </div>
                <div class="form-group">
                    <label for="benefits_section_heading2" class="form-label">Heading Line 2</label>
                    <input type="text" id="benefits_section_heading2" name="benefits_section_heading2" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['benefits_section_heading2']); ?>" maxlength="50"
                        placeholder="e.g., This Feature?">
                    <p class="form-help">Second line (leave empty to auto-append feature name)</p>
                </div>
            </div>
            <div class="form-group">
                <label for="benefits_section_subheading" class="form-label">Subheading</label>
                <textarea id="benefits_section_subheading" name="benefits_section_subheading" class="form-textarea" rows="2"
                    maxlength="255" placeholder="e.g., Discover the key advantages that make this feature essential..."><?php echo htmlspecialchars($form_data['benefits_section_subheading']); ?></textarea>
                <p class="form-help">Brief description below the heading</p>
            </div>
            
            <h3 class="form-subsection-title">Section Colors</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="benefits_section_bg_color" class="form-label">Background Color</label>
                    <input type="text" id="benefits_section_bg_color" name="benefits_section_bg_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['benefits_section_bg_color']); ?>"
                        placeholder="#0f172a">
                    <p class="form-help">Supports gradients like: linear-gradient(135deg, #0f172a 0%, #1e293b 100%)</p>
                </div>
                <div class="form-group">
                    <label for="benefits_section_heading_color" class="form-label">Heading Color</label>
                    <div class="color-input-group">
                        <input type="color" id="benefits_section_heading_color_picker" value="<?php echo htmlspecialchars($form_data['benefits_section_heading_color']); ?>" 
                            onchange="document.getElementById('benefits_section_heading_color').value = this.value">
                        <input type="text" id="benefits_section_heading_color" name="benefits_section_heading_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['benefits_section_heading_color']); ?>" placeholder="#FFFFFF">
                    </div>
                </div>
                <div class="form-group">
                    <label for="benefits_section_subheading_color" class="form-label">Subheading Color</label>
                    <input type="text" id="benefits_section_subheading_color" name="benefits_section_subheading_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['benefits_section_subheading_color']); ?>"
                        placeholder="rgba(255,255,255,0.6)">
                </div>
            </div>
            
            <h3 class="form-subsection-title">Card Styling</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="benefits_card_bg_color" class="form-label">Card Background</label>
                    <input type="text" id="benefits_card_bg_color" name="benefits_card_bg_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['benefits_card_bg_color']); ?>"
                        placeholder="rgba(255,255,255,0.06)">
                </div>
                <div class="form-group">
                    <label for="benefits_card_border_color" class="form-label">Card Border</label>
                    <input type="text" id="benefits_card_border_color" name="benefits_card_border_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['benefits_card_border_color']); ?>"
                        placeholder="rgba(255,255,255,0.1)">
                </div>
                <div class="form-group">
                    <label for="benefits_card_hover_bg_color" class="form-label">Card Hover BG</label>
                    <div class="color-input-group">
                        <input type="color" id="benefits_card_hover_bg_color_picker" value="<?php echo htmlspecialchars($form_data['benefits_card_hover_bg_color']); ?>" 
                            onchange="document.getElementById('benefits_card_hover_bg_color').value = this.value">
                        <input type="text" id="benefits_card_hover_bg_color" name="benefits_card_hover_bg_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['benefits_card_hover_bg_color']); ?>" placeholder="#667eea">
                    </div>
                </div>
            </div>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="benefits_card_title_color" class="form-label">Card Title Color</label>
                    <div class="color-input-group">
                        <input type="color" id="benefits_card_title_color_picker" value="<?php echo htmlspecialchars($form_data['benefits_card_title_color']); ?>" 
                            onchange="document.getElementById('benefits_card_title_color').value = this.value">
                        <input type="text" id="benefits_card_title_color" name="benefits_card_title_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['benefits_card_title_color']); ?>" placeholder="#FFFFFF">
                    </div>
                </div>
                <div class="form-group">
                    <label for="benefits_card_text_color" class="form-label">Card Text Color</label>
                    <input type="text" id="benefits_card_text_color" name="benefits_card_text_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['benefits_card_text_color']); ?>"
                        placeholder="rgba(255,255,255,0.5)">
                </div>
                <div class="form-group">
                    <label for="benefits_card_icon_color" class="form-label">Card Icon Color</label>
                    <input type="text" id="benefits_card_icon_color" name="benefits_card_icon_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['benefits_card_icon_color']); ?>"
                        placeholder="rgba(255,255,255,0.5)">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="benefits_card_hover_text_color" class="form-label">Card Hover Text Color</label>
                    <div class="color-input-group">
                        <input type="color" id="benefits_card_hover_text_color_picker" value="<?php echo htmlspecialchars($form_data['benefits_card_hover_text_color']); ?>" 
                            onchange="document.getElementById('benefits_card_hover_text_color').value = this.value">
                        <input type="text" id="benefits_card_hover_text_color" name="benefits_card_hover_text_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['benefits_card_hover_text_color']); ?>" placeholder="#FFFFFF">
                    </div>
                    <p class="form-help">Text color when hovering over cards</p>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">📋 Key Benefits Cards (JSON)</h2>
            <p class="form-section-desc">Define up to 4 benefit cards with icon, title, and description. These will be displayed in the Key Benefits section.</p>
            <div class="form-group">
                <label for="benefits_cards" class="form-label">Benefits Cards (JSON - up to 4 cards)</label>
                <textarea id="benefits_cards" name="benefits_cards" class="form-textarea form-textarea-code" rows="12" 
                    placeholder='[
  {"icon": "speed", "title": "Fast Processing", "description": "Lightning-fast performance with optimized workflows"},
  {"icon": "security", "title": "Secure & Reliable", "description": "Enterprise-grade security with 99.9% uptime"},
  {"icon": "chart", "title": "Data Insights", "description": "Real-time analytics and reporting dashboards"},
  {"icon": "users", "title": "Easy to Use", "description": "Intuitive interface that teams love to use"}
]'><?php echo htmlspecialchars(json_encode($form_data['benefits_cards'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
                <p class="form-help">Icons: speed, security, chart, users, globe, clock, check, money. Title max 24 chars, description max 120 chars.</p>
            </div>
        </div>
        
        <!-- How It Works Section Styling -->
        <div class="form-section">
            <h2 class="form-section-title">⚙️ How It Works Section</h2>
            <p class="form-section-desc">A step-by-step workflow section showing how the feature works.</p>
            
            <div class="form-group">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="how_it_works_enabled" value="1" <?php echo $form_data['how_it_works_enabled'] ? 'checked' : ''; ?>>
                    <span>Enable How It Works Section</span>
                </label>
                <p class="form-help">Show this section after the Key Benefits section</p>
            </div>
            
            <h3 class="form-subsection-title">Section Headings</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="how_it_works_badge" class="form-label">Badge Text</label>
                    <input type="text" id="how_it_works_badge" name="how_it_works_badge" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['how_it_works_badge']); ?>" maxlength="50"
                        placeholder="e.g., Simple Process">
                    <p class="form-help">Small badge above the heading</p>
                </div>
                <div class="form-group">
                    <label for="how_it_works_heading" class="form-label">Heading</label>
                    <input type="text" id="how_it_works_heading" name="how_it_works_heading" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['how_it_works_heading']); ?>" maxlength="100"
                        placeholder="e.g., How It Works">
                </div>
                <div class="form-group">
                    <label for="how_it_works_subheading" class="form-label">Subheading</label>
                    <input type="text" id="how_it_works_subheading" name="how_it_works_subheading" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['how_it_works_subheading']); ?>" maxlength="255"
                        placeholder="e.g., Get started in four simple steps...">
                </div>
            </div>
            
            <h3 class="form-subsection-title">Section Colors</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="how_it_works_bg_color" class="form-label">Background Color</label>
                    <input type="text" id="how_it_works_bg_color" name="how_it_works_bg_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['how_it_works_bg_color']); ?>"
                        placeholder="#f9fafb">
                    <p class="form-help">Supports gradients</p>
                </div>
                <div class="form-group">
                    <label for="how_it_works_heading_color" class="form-label">Heading Color</label>
                    <div class="color-input-group">
                        <input type="color" id="how_it_works_heading_color_picker" value="<?php echo htmlspecialchars($form_data['how_it_works_heading_color']); ?>" 
                            onchange="document.getElementById('how_it_works_heading_color').value = this.value">
                        <input type="text" id="how_it_works_heading_color" name="how_it_works_heading_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['how_it_works_heading_color']); ?>" placeholder="#111827">
                    </div>
                </div>
                <div class="form-group">
                    <label for="how_it_works_subheading_color" class="form-label">Subheading Color</label>
                    <div class="color-input-group">
                        <input type="color" id="how_it_works_subheading_color_picker" value="<?php echo htmlspecialchars($form_data['how_it_works_subheading_color']); ?>" 
                            onchange="document.getElementById('how_it_works_subheading_color').value = this.value">
                        <input type="text" id="how_it_works_subheading_color" name="how_it_works_subheading_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['how_it_works_subheading_color']); ?>" placeholder="#6b7280">
                    </div>
                </div>
            </div>
            
            <h3 class="form-subsection-title">Badge Styling</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="how_it_works_badge_bg_color" class="form-label">Badge Background</label>
                    <input type="text" id="how_it_works_badge_bg_color" name="how_it_works_badge_bg_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['how_it_works_badge_bg_color']); ?>"
                        placeholder="rgba(102, 126, 234, 0.1)">
                </div>
                <div class="form-group">
                    <label for="how_it_works_badge_text_color" class="form-label">Badge Text Color</label>
                    <div class="color-input-group">
                        <input type="color" id="how_it_works_badge_text_color_picker" value="<?php echo htmlspecialchars($form_data['how_it_works_badge_text_color']); ?>" 
                            onchange="document.getElementById('how_it_works_badge_text_color').value = this.value">
                        <input type="text" id="how_it_works_badge_text_color" name="how_it_works_badge_text_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['how_it_works_badge_text_color']); ?>" placeholder="#667eea">
                    </div>
                </div>
            </div>
            
            <h3 class="form-subsection-title">Step Card Styling</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="how_it_works_card_bg_color" class="form-label">Card Background</label>
                    <div class="color-input-group">
                        <input type="color" id="how_it_works_card_bg_color_picker" value="<?php echo htmlspecialchars($form_data['how_it_works_card_bg_color']); ?>" 
                            onchange="document.getElementById('how_it_works_card_bg_color').value = this.value">
                        <input type="text" id="how_it_works_card_bg_color" name="how_it_works_card_bg_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['how_it_works_card_bg_color']); ?>" placeholder="#ffffff">
                    </div>
                </div>
                <div class="form-group">
                    <label for="how_it_works_card_border_color" class="form-label">Card Border</label>
                    <div class="color-input-group">
                        <input type="color" id="how_it_works_card_border_color_picker" value="<?php echo htmlspecialchars($form_data['how_it_works_card_border_color']); ?>" 
                            onchange="document.getElementById('how_it_works_card_border_color').value = this.value">
                        <input type="text" id="how_it_works_card_border_color" name="how_it_works_card_border_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['how_it_works_card_border_color']); ?>" placeholder="#e5e7eb">
                    </div>
                </div>
                <div class="form-group">
                    <label for="how_it_works_card_hover_border_color" class="form-label">Card Hover Border</label>
                    <div class="color-input-group">
                        <input type="color" id="how_it_works_card_hover_border_color_picker" value="<?php echo htmlspecialchars($form_data['how_it_works_card_hover_border_color']); ?>" 
                            onchange="document.getElementById('how_it_works_card_hover_border_color').value = this.value">
                        <input type="text" id="how_it_works_card_hover_border_color" name="how_it_works_card_hover_border_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['how_it_works_card_hover_border_color']); ?>" placeholder="#667eea">
                    </div>
                </div>
            </div>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="how_it_works_step_color" class="form-label">Step Number Color</label>
                    <div class="color-input-group">
                        <input type="color" id="how_it_works_step_color_picker" value="<?php echo htmlspecialchars($form_data['how_it_works_step_color']); ?>" 
                            onchange="document.getElementById('how_it_works_step_color').value = this.value">
                        <input type="text" id="how_it_works_step_color" name="how_it_works_step_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['how_it_works_step_color']); ?>" placeholder="#667eea">
                    </div>
                </div>
                <div class="form-group">
                    <label for="how_it_works_step_bg_color" class="form-label">Step Number BG</label>
                    <input type="text" id="how_it_works_step_bg_color" name="how_it_works_step_bg_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['how_it_works_step_bg_color']); ?>"
                        placeholder="rgba(102, 126, 234, 0.1)">
                </div>
                <div class="form-group">
                    <label for="how_it_works_connector_color" class="form-label">Connector Color</label>
                    <div class="color-input-group">
                        <input type="color" id="how_it_works_connector_color_picker" value="<?php echo htmlspecialchars($form_data['how_it_works_connector_color']); ?>" 
                            onchange="document.getElementById('how_it_works_connector_color').value = this.value">
                        <input type="text" id="how_it_works_connector_color" name="how_it_works_connector_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['how_it_works_connector_color']); ?>" placeholder="#d1d5db">
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="how_it_works_title_color" class="form-label">Step Title Color</label>
                    <div class="color-input-group">
                        <input type="color" id="how_it_works_title_color_picker" value="<?php echo htmlspecialchars($form_data['how_it_works_title_color']); ?>" 
                            onchange="document.getElementById('how_it_works_title_color').value = this.value">
                        <input type="text" id="how_it_works_title_color" name="how_it_works_title_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['how_it_works_title_color']); ?>" placeholder="#111827">
                    </div>
                </div>
                <div class="form-group">
                    <label for="how_it_works_desc_color" class="form-label">Step Description Color</label>
                    <div class="color-input-group">
                        <input type="color" id="how_it_works_desc_color_picker" value="<?php echo htmlspecialchars($form_data['how_it_works_desc_color']); ?>" 
                            onchange="document.getElementById('how_it_works_desc_color').value = this.value">
                        <input type="text" id="how_it_works_desc_color" name="how_it_works_desc_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['how_it_works_desc_color']); ?>" placeholder="#6b7280">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">📝 How It Works Steps (JSON)</h2>
            <p class="form-section-desc">Define up to 4 workflow steps with step number, title, and description.</p>
            <div class="form-group">
                <label for="how_it_works_steps" class="form-label">Workflow Steps (JSON - up to 4 steps)</label>
                <textarea id="how_it_works_steps" name="how_it_works_steps" class="form-textarea form-textarea-code" rows="12" 
                    placeholder='[
  {"step": "01", "title": "Configure", "description": "Set up the feature according to your business requirements with our intuitive configuration wizard."},
  {"step": "02", "title": "Integrate", "description": "Seamlessly connect with your existing systems and workflows for unified operations."},
  {"step": "03", "title": "Automate", "description": "Enable automated processes to reduce manual work and increase efficiency."},
  {"step": "04", "title": "Analyze", "description": "Get real-time insights and reports to make data-driven decisions."}
]'><?php echo htmlspecialchars(json_encode($form_data['how_it_works_steps'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
                <p class="form-help">Each step has: step (e.g., "01"), title (max 30 chars), description (max 200 chars)</p>
            </div>
        </div>
        
        <!-- Feature Highlights Section Styling -->
        <div class="form-section">
            <h2 class="form-section-title">✨ Feature Highlights Section</h2>
            <p class="form-section-desc">A grid of capability cards showcasing the feature's key highlights.</p>
            
            <div class="form-group">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="highlights_enabled" value="1" <?php echo $form_data['highlights_enabled'] ? 'checked' : ''; ?>>
                    <span>Enable Feature Highlights Section</span>
                </label>
                <p class="form-help">Show this section after the How It Works section</p>
            </div>
            
            <h3 class="form-subsection-title">Section Headings</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="highlights_badge" class="form-label">Badge Text</label>
                    <input type="text" id="highlights_badge" name="highlights_badge" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['highlights_badge']); ?>" maxlength="50"
                        placeholder="e.g., Capabilities">
                    <p class="form-help">Small badge above the heading</p>
                </div>
                <div class="form-group">
                    <label for="highlights_heading" class="form-label">Heading</label>
                    <input type="text" id="highlights_heading" name="highlights_heading" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['highlights_heading']); ?>" maxlength="100"
                        placeholder="e.g., Feature Highlights">
                </div>
                <div class="form-group">
                    <label for="highlights_subheading" class="form-label">Subheading</label>
                    <input type="text" id="highlights_subheading" name="highlights_subheading" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['highlights_subheading']); ?>" maxlength="255"
                        placeholder="e.g., Powerful capabilities designed to...">
                </div>
            </div>
            
            <h3 class="form-subsection-title">Section Colors</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="highlights_bg_color" class="form-label">Background</label>
                    <input type="text" id="highlights_bg_color" name="highlights_bg_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['highlights_bg_color']); ?>"
                        placeholder="linear-gradient(180deg, #f8fafc 0%, #fff 100%)">
                    <p class="form-help">Supports gradients</p>
                </div>
                <div class="form-group">
                    <label for="highlights_heading_color" class="form-label">Heading Color</label>
                    <div class="color-input-group">
                        <input type="color" id="highlights_heading_color_picker" value="<?php echo htmlspecialchars($form_data['highlights_heading_color']); ?>" 
                            onchange="document.getElementById('highlights_heading_color').value = this.value">
                        <input type="text" id="highlights_heading_color" name="highlights_heading_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['highlights_heading_color']); ?>" placeholder="#111827">
                    </div>
                </div>
                <div class="form-group">
                    <label for="highlights_subheading_color" class="form-label">Subheading Color</label>
                    <div class="color-input-group">
                        <input type="color" id="highlights_subheading_color_picker" value="<?php echo htmlspecialchars($form_data['highlights_subheading_color']); ?>" 
                            onchange="document.getElementById('highlights_subheading_color').value = this.value">
                        <input type="text" id="highlights_subheading_color" name="highlights_subheading_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['highlights_subheading_color']); ?>" placeholder="#6b7280">
                    </div>
                </div>
            </div>
            
            <h3 class="form-subsection-title">Badge Styling</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="highlights_badge_bg_color" class="form-label">Badge Background</label>
                    <input type="text" id="highlights_badge_bg_color" name="highlights_badge_bg_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['highlights_badge_bg_color']); ?>"
                        placeholder="rgba(102, 126, 234, 0.1)">
                </div>
                <div class="form-group">
                    <label for="highlights_badge_text_color" class="form-label">Badge Text Color</label>
                    <div class="color-input-group">
                        <input type="color" id="highlights_badge_text_color_picker" value="<?php echo htmlspecialchars($form_data['highlights_badge_text_color']); ?>" 
                            onchange="document.getElementById('highlights_badge_text_color').value = this.value">
                        <input type="text" id="highlights_badge_text_color" name="highlights_badge_text_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['highlights_badge_text_color']); ?>" placeholder="#667eea">
                    </div>
                </div>
            </div>
            
            <h3 class="form-subsection-title">Card Styling</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="highlights_card_bg_color" class="form-label">Card Background</label>
                    <div class="color-input-group">
                        <input type="color" id="highlights_card_bg_color_picker" value="<?php echo htmlspecialchars($form_data['highlights_card_bg_color']); ?>" 
                            onchange="document.getElementById('highlights_card_bg_color').value = this.value">
                        <input type="text" id="highlights_card_bg_color" name="highlights_card_bg_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['highlights_card_bg_color']); ?>" placeholder="#ffffff">
                    </div>
                </div>
                <div class="form-group">
                    <label for="highlights_card_border_color" class="form-label">Card Border</label>
                    <div class="color-input-group">
                        <input type="color" id="highlights_card_border_color_picker" value="<?php echo htmlspecialchars($form_data['highlights_card_border_color']); ?>" 
                            onchange="document.getElementById('highlights_card_border_color').value = this.value">
                        <input type="text" id="highlights_card_border_color" name="highlights_card_border_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['highlights_card_border_color']); ?>" placeholder="#e5e7eb">
                    </div>
                </div>
                <div class="form-group">
                    <label for="highlights_card_hover_border_color" class="form-label">Card Hover Border</label>
                    <div class="color-input-group">
                        <input type="color" id="highlights_card_hover_border_color_picker" value="<?php echo htmlspecialchars($form_data['highlights_card_hover_border_color']); ?>" 
                            onchange="document.getElementById('highlights_card_hover_border_color').value = this.value">
                        <input type="text" id="highlights_card_hover_border_color" name="highlights_card_hover_border_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['highlights_card_hover_border_color']); ?>" placeholder="#667eea">
                    </div>
                </div>
            </div>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="highlights_icon_bg_color" class="form-label">Icon Background</label>
                    <input type="text" id="highlights_icon_bg_color" name="highlights_icon_bg_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['highlights_icon_bg_color']); ?>"
                        placeholder="rgba(102, 126, 234, 0.1)">
                </div>
                <div class="form-group">
                    <label for="highlights_icon_color" class="form-label">Icon Color</label>
                    <div class="color-input-group">
                        <input type="color" id="highlights_icon_color_picker" value="<?php echo htmlspecialchars($form_data['highlights_icon_color']); ?>" 
                            onchange="document.getElementById('highlights_icon_color').value = this.value">
                        <input type="text" id="highlights_icon_color" name="highlights_icon_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['highlights_icon_color']); ?>" placeholder="#667eea">
                    </div>
                </div>
                <div class="form-group">
                    <label for="highlights_title_color" class="form-label">Title Color</label>
                    <div class="color-input-group">
                        <input type="color" id="highlights_title_color_picker" value="<?php echo htmlspecialchars($form_data['highlights_title_color']); ?>" 
                            onchange="document.getElementById('highlights_title_color').value = this.value">
                        <input type="text" id="highlights_title_color" name="highlights_title_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['highlights_title_color']); ?>" placeholder="#111827">
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="highlights_desc_color" class="form-label">Description Color</label>
                    <div class="color-input-group">
                        <input type="color" id="highlights_desc_color_picker" value="<?php echo htmlspecialchars($form_data['highlights_desc_color']); ?>" 
                            onchange="document.getElementById('highlights_desc_color').value = this.value">
                        <input type="text" id="highlights_desc_color" name="highlights_desc_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['highlights_desc_color']); ?>" placeholder="#6b7280">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">📋 Feature Highlights Cards (JSON)</h2>
            <p class="form-section-desc">Define up to 6 highlight cards with icon, title, and description.</p>
            <div class="form-group">
                <label for="highlights_cards" class="form-label">Highlights Cards (JSON - up to 6 cards)</label>
                <textarea id="highlights_cards" name="highlights_cards" class="form-textarea form-textarea-code" rows="14" 
                    placeholder='[
  {"icon": "dashboard", "title": "Intuitive Dashboard", "description": "Access all key metrics and actions from a centralized, easy-to-use dashboard."},
  {"icon": "automation", "title": "Smart Automation", "description": "Automate repetitive tasks and workflows to save time and reduce errors."},
  {"icon": "integration", "title": "Seamless Integration", "description": "Connect with other modules and third-party applications effortlessly."},
  {"icon": "reports", "title": "Advanced Reports", "description": "Generate comprehensive reports with customizable filters and export options."},
  {"icon": "security", "title": "Enterprise Security", "description": "Role-based access control and audit trails for complete data security."},
  {"icon": "mobile", "title": "Mobile Ready", "description": "Access features on-the-go with our responsive mobile interface."}
]'><?php echo htmlspecialchars(json_encode($form_data['highlights_cards'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
                <p class="form-help">Icons: dashboard, automation, integration, reports, security, mobile, speed, chart, users, globe, clock, check, money</p>
            </div>
        </div>
        
        <!-- Use Cases Section Styling -->
        <div class="form-section">
            <h2 class="form-section-title">🏭 Use Cases Section</h2>
            <p class="form-section-desc">Industry-specific use cases with images showing how the feature is used.</p>
            
            <div class="form-group">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="use_cases_enabled" value="1" <?php echo $form_data['use_cases_enabled'] ? 'checked' : ''; ?>>
                    <span>Enable Use Cases Section</span>
                </label>
                <p class="form-help">Show this section after the Feature Highlights section</p>
            </div>
            
            <h3 class="form-subsection-title">Section Headings</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="use_cases_badge" class="form-label">Badge Text</label>
                    <input type="text" id="use_cases_badge" name="use_cases_badge" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['use_cases_badge']); ?>" maxlength="50"
                        placeholder="e.g., Industries">
                </div>
                <div class="form-group">
                    <label for="use_cases_heading" class="form-label">Heading</label>
                    <input type="text" id="use_cases_heading" name="use_cases_heading" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['use_cases_heading']); ?>" maxlength="100"
                        placeholder="e.g., Use Cases">
                </div>
                <div class="form-group">
                    <label for="use_cases_subheading" class="form-label">Subheading</label>
                    <input type="text" id="use_cases_subheading" name="use_cases_subheading" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['use_cases_subheading']); ?>" maxlength="255"
                        placeholder="e.g., See how different industries...">
                </div>
            </div>
            
            <h3 class="form-subsection-title">Section Colors (Dark Theme)</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="use_cases_bg_color" class="form-label">Background</label>
                    <input type="text" id="use_cases_bg_color" name="use_cases_bg_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['use_cases_bg_color']); ?>"
                        placeholder="linear-gradient(135deg, #0f172a 0%, #1e293b 100%)">
                    <p class="form-help">Supports gradients</p>
                </div>
                <div class="form-group">
                    <label for="use_cases_heading_color" class="form-label">Heading Color</label>
                    <div class="color-input-group">
                        <input type="color" id="use_cases_heading_color_picker" value="<?php echo htmlspecialchars($form_data['use_cases_heading_color']); ?>" 
                            onchange="document.getElementById('use_cases_heading_color').value = this.value">
                        <input type="text" id="use_cases_heading_color" name="use_cases_heading_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['use_cases_heading_color']); ?>" placeholder="#ffffff">
                    </div>
                </div>
                <div class="form-group">
                    <label for="use_cases_subheading_color" class="form-label">Subheading Color</label>
                    <input type="text" id="use_cases_subheading_color" name="use_cases_subheading_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['use_cases_subheading_color']); ?>"
                        placeholder="rgba(255, 255, 255, 0.7)">
                </div>
            </div>
            
            <h3 class="form-subsection-title">Badge Styling</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="use_cases_badge_bg_color" class="form-label">Badge Background</label>
                    <input type="text" id="use_cases_badge_bg_color" name="use_cases_badge_bg_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['use_cases_badge_bg_color']); ?>"
                        placeholder="rgba(255, 255, 255, 0.1)">
                </div>
                <div class="form-group">
                    <label for="use_cases_badge_text_color" class="form-label">Badge Text Color</label>
                    <div class="color-input-group">
                        <input type="color" id="use_cases_badge_text_color_picker" value="<?php echo htmlspecialchars($form_data['use_cases_badge_text_color']); ?>" 
                            onchange="document.getElementById('use_cases_badge_text_color').value = this.value">
                        <input type="text" id="use_cases_badge_text_color" name="use_cases_badge_text_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['use_cases_badge_text_color']); ?>" placeholder="#ffffff">
                    </div>
                </div>
            </div>
            
            <h3 class="form-subsection-title">Card Styling</h3>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="use_cases_card_bg_color" class="form-label">Card Background</label>
                    <input type="text" id="use_cases_card_bg_color" name="use_cases_card_bg_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['use_cases_card_bg_color']); ?>"
                        placeholder="rgba(255, 255, 255, 0.05)">
                </div>
                <div class="form-group">
                    <label for="use_cases_card_border_color" class="form-label">Card Border</label>
                    <input type="text" id="use_cases_card_border_color" name="use_cases_card_border_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['use_cases_card_border_color']); ?>"
                        placeholder="rgba(255, 255, 255, 0.1)">
                </div>
                <div class="form-group">
                    <label for="use_cases_card_hover_border_color" class="form-label">Card Hover Border</label>
                    <div class="color-input-group">
                        <input type="color" id="use_cases_card_hover_border_color_picker" value="<?php echo htmlspecialchars($form_data['use_cases_card_hover_border_color']); ?>" 
                            onchange="document.getElementById('use_cases_card_hover_border_color').value = this.value">
                        <input type="text" id="use_cases_card_hover_border_color" name="use_cases_card_hover_border_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['use_cases_card_hover_border_color']); ?>" placeholder="#667eea">
                    </div>
                </div>
            </div>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label for="use_cases_title_color" class="form-label">Title Color</label>
                    <div class="color-input-group">
                        <input type="color" id="use_cases_title_color_picker" value="<?php echo htmlspecialchars($form_data['use_cases_title_color']); ?>" 
                            onchange="document.getElementById('use_cases_title_color').value = this.value">
                        <input type="text" id="use_cases_title_color" name="use_cases_title_color" class="form-input" 
                            value="<?php echo htmlspecialchars($form_data['use_cases_title_color']); ?>" placeholder="#ffffff">
                    </div>
                </div>
                <div class="form-group">
                    <label for="use_cases_desc_color" class="form-label">Description Color</label>
                    <input type="text" id="use_cases_desc_color" name="use_cases_desc_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['use_cases_desc_color']); ?>"
                        placeholder="rgba(255, 255, 255, 0.7)">
                </div>
                <div class="form-group">
                    <label for="use_cases_overlay_color" class="form-label">Image Overlay</label>
                    <input type="text" id="use_cases_overlay_color" name="use_cases_overlay_color" class="form-input" 
                        value="<?php echo htmlspecialchars($form_data['use_cases_overlay_color']); ?>"
                        placeholder="linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%)">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">📋 Use Cases Cards (JSON)</h2>
            <p class="form-section-desc">Define up to 4 industry use case cards with image, title, and description.</p>
            <div class="form-group">
                <label for="use_cases_cards" class="form-label">Use Cases Cards (JSON - up to 4 cards)</label>
                <textarea id="use_cases_cards" name="use_cases_cards" class="form-textarea form-textarea-code" rows="12" 
                    placeholder='[
  {"industry": "Manufacturing", "description": "Streamline production planning, inventory management, and quality control processes.", "image": "https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=400&h=300&fit=crop"},
  {"industry": "Retail", "description": "Manage multi-location inventory, POS integration, and customer relationships.", "image": "https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&h=300&fit=crop"},
  {"industry": "Healthcare", "description": "Handle patient records, appointment scheduling, and billing with compliance.", "image": "https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=400&h=300&fit=crop"},
  {"industry": "Education", "description": "Manage student information, course scheduling, and fee collection efficiently.", "image": "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400&h=300&fit=crop"}
]'><?php echo htmlspecialchars(json_encode($form_data['use_cases_cards'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
                <p class="form-help">Each card has: industry (title), description, image (URL)</p>
            </div>
        </div>
        
        <!-- FAQs Section Settings -->
        <div class="form-section">
            <h2 class="form-section-title">❓ FAQs Section</h2>
            <p class="form-section-desc">Customize the FAQs section appearance on the feature detail page.</p>
            
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
                    value="<?php echo htmlspecialchars($form_data['faqs_section_heading']); ?>" maxlength="100"
                    placeholder="e.g., Frequently Asked Questions">
                <p class="form-help">Max 100 characters (<span id="faqs_heading_count"><?php echo strlen($form_data['faqs_section_heading']); ?></span>/100)</p>
            </div>
            
            <div class="form-group">
                <label for="faqs_section_subheading" class="form-label">Section Subheading</label>
                <input type="text" id="faqs_section_subheading" name="faqs_section_subheading" class="form-input" 
                    value="<?php echo htmlspecialchars($form_data['faqs_section_subheading']); ?>" maxlength="200"
                    placeholder="e.g., Everything you need to know about this feature">
                <p class="form-help">Max 200 characters (<span id="faqs_subheading_count"><?php echo strlen($form_data['faqs_section_subheading']); ?></span>/200)</p>
            </div>
            
            <div class="form-group">
                <label for="faqs_cards" class="form-label">FAQs (JSON)</label>
                <textarea id="faqs_cards" name="faqs_cards" class="form-textarea form-textarea-code" rows="12" 
                    placeholder='[
  {"question": "How long does it take to implement this feature?", "answer": "Implementation typically takes 1-2 weeks depending on your existing setup and customization requirements."},
  {"question": "Can this feature be customized for my business?", "answer": "Yes, the feature is highly customizable. You can configure workflows, fields, reports, and integrations."},
  {"question": "Is training provided for this feature?", "answer": "Absolutely! We provide comprehensive training materials including video tutorials and documentation."},
  {"question": "What kind of support is available?", "answer": "We offer 24/7 technical support via chat, email, and phone."}
]'><?php echo htmlspecialchars(json_encode($form_data['faqs_cards'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></textarea>
                <p class="form-help">Enter FAQs in JSON format. Each FAQ has: question, answer</p>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">Related Solutions</h2>
            <div class="form-group">
                <label for="related_solutions" class="form-label">Related Solution Slugs</label>
                <textarea id="related_solutions" name="related_solutions" class="form-textarea" rows="4"
                    placeholder="Enter one solution slug per line"><?php echo htmlspecialchars(is_array($form_data['related_solutions']) ? implode("\n", $form_data['related_solutions']) : ''); ?></textarea>
                <p class="form-help">Enter one solution slug per line (e.g., inventory-management)</p>
            </div>
        </div>
        
        <div class="form-section">
            <h2 class="form-section-title">Media</h2>
            <div class="form-group">
                <label for="screenshots" class="form-label">Screenshots</label>
                <textarea id="screenshots" name="screenshots" class="form-textarea" rows="4"
                    placeholder="Enter one image URL per line"><?php echo htmlspecialchars(is_array($form_data['screenshots']) ? implode("\n", $form_data['screenshots']) : ''); ?></textarea>
                <p class="form-help">Enter one image URL per line</p>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Feature</button>
            <a href="<?php echo get_app_base_url(); ?>/admin/features.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<form id="deleteForm" method="POST" action="<?php echo get_app_base_url(); ?>/admin/features/delete.php" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($feature_id); ?>">
</form>

<style>
.admin-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 14px; margin-bottom: 8px; }
.admin-breadcrumb a { color: var(--color-primary); text-decoration: none; }
.admin-breadcrumb a:hover { text-decoration: underline; }
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
.form-section-desc { font-size: 14px; color: var(--color-gray-600); margin: -8px 0 16px 0; }
.form-subsection-title { font-size: 14px; font-weight: 600; color: var(--color-gray-700); margin: 16px 0 12px 0; }
.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.form-row-3 { grid-template-columns: repeat(3, 1fr); }
.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 14px; font-weight: 600; color: var(--color-gray-700); margin-bottom: 8px; }
.form-label.required::after { content: ' *'; color: #dc2626; }
.form-input, .form-select, .form-textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--color-gray-300); border-radius: 6px; font-size: 14px; color: var(--color-gray-900); font-family: inherit; box-sizing: border-box; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.form-textarea { resize: vertical; }
.form-help { font-size: 12px; color: var(--color-gray-500); margin: 4px 0 0 0; }
.form-actions { display: flex; gap: 12px; padding-top: 24px; border-top: 1px solid var(--color-gray-200); }
.image-input-group { display: flex; flex-direction: column; gap: 12px; }
.image-preview { width: 80px; height: 80px; border: 1px solid var(--color-gray-300); border-radius: 8px; overflow: hidden; background: var(--color-gray-50); display: flex; align-items: center; justify-content: center; }
.image-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
.color-input-group { display: flex; gap: 8px; align-items: center; }
.color-input-group input[type="color"] { width: 40px; height: 38px; padding: 2px; border: 1px solid var(--color-gray-300); border-radius: 6px; cursor: pointer; }
.color-input-group .form-input { flex: 1; }
.form-checkbox-label { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; color: var(--color-gray-700); }
.form-checkbox-label input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
.btn-outline { background: transparent; border: 1px solid var(--color-gray-300); color: var(--color-gray-700); }
.btn-outline:hover { background: var(--color-gray-50); border-color: var(--color-gray-400); }
@media (max-width: 768px) { .admin-page-header { flex-direction: column; } .form-row, .form-row-3 { grid-template-columns: 1fr; } }
</style>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this feature? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

// FAQs section character counters
document.getElementById('faqs_section_heading')?.addEventListener('input', function() {
    document.getElementById('faqs_heading_count').textContent = this.value.length;
});
document.getElementById('faqs_section_subheading')?.addEventListener('input', function() {
    document.getElementById('faqs_subheading_count').textContent = this.value.length;
});
</script>

<?php include_admin_footer(); ?>
