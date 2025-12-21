<?php

/**
 * Feature Detail Page - Modern Razorpay-Style Design
 * A beautifully designed page showcasing feature details with:
 * - Hero with gradient background and badge
 * - Key benefits cards
 * - How it works workflow
 * - Feature highlights
 * - Screenshots gallery
 * - Use cases
 * - Related solutions
 * - FAQs
 * - CTA form
 */

require_once __DIR__ . '/../config/bootstrap.php';

$config = require __DIR__ . '/../config/app.php';

if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

require_once __DIR__ . '/../includes/auth_helpers.php';
startSecureSession();
require_once __DIR__ . '/../includes/template_helpers.php';

use Karyalay\Models\Feature;
use Karyalay\Models\Solution;
use Karyalay\Models\ClientLogo;

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: ' . get_base_url() . '/features.php');
    exit;
}

try {
    $featureModel = new Feature();
    $feature = $featureModel->findBySlug($slug);
    
    if (!$feature || $feature['status'] !== 'PUBLISHED') {
        header('HTTP/1.0 404 Not Found');
        $page_title = 'Feature Not Found';
        $page_description = 'The requested feature could not be found';
        include_header($page_title, $page_description);
        ?>
        <section class="section">
            <div class="container">
                <div class="feat-not-found-card">
                    <div class="feat-not-found-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1>Feature Not Found</h1>
                    <p>The feature you're looking for doesn't exist or is no longer available.</p>
                    <a href="<?php echo get_base_url(); ?>/features.php" class="btn btn-primary">View All Features</a>
                </div>
            </div>
        </section>
        <?php
        include_footer();
        exit;
    }
    
    // Fetch related solutions if available
    $relatedSolutions = [];
    if (!empty($feature['related_solutions']) && is_array($feature['related_solutions'])) {
        $solutionModel = new Solution();
        foreach ($feature['related_solutions'] as $solutionSlug) {
            $solution = $solutionModel->findBySlug($solutionSlug);
            if ($solution && $solution['status'] === 'PUBLISHED') {
                $relatedSolutions[] = $solution;
            }
        }
    }
    
    // Load client logos for marquee
    $clientLogos = [];
    try {
        $clientLogoModel = new ClientLogo();
        $clientLogos = $clientLogoModel->getPublishedLogos();
    } catch (Exception $e) {
        error_log("Client logos not available: " . $e->getMessage());
    }
    
} catch (Exception $e) {
    error_log('Error fetching feature: ' . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    $page_title = 'Error';
    include_header($page_title, '');
    ?>
    <section class="section">
        <div class="container">
            <div class="feat-not-found-card">
                <h1>Error</h1>
                <p>An error occurred while loading the feature. Please try again later.</p>
                <a href="<?php echo get_base_url(); ?>/features.php" class="btn btn-primary">View All Features</a>
            </div>
        </div>
    </section>
    <?php
    include_footer();
    exit;
}

$page_title = htmlspecialchars($feature['name']);
$page_description = htmlspecialchars($feature['description'] ?? '');

// Dynamic hero styling from database (with defaults)
$heroStyle = [
    'bg_color' => $feature['hero_bg_color'] ?? '#fafafa',
    'bg_gradient_start' => $feature['hero_bg_gradient_start'] ?? '#fafafa',
    'bg_gradient_end' => $feature['hero_bg_gradient_end'] ?? '#f5f5f5',
    'title_gradient_start' => $feature['hero_title_gradient_start'] ?? '#111827',
    'title_gradient_middle' => $feature['hero_title_gradient_middle'] ?? '#667eea',
    'title_gradient_end' => $feature['hero_title_gradient_end'] ?? '#764ba2',
    'subtitle_color' => $feature['hero_subtitle_color'] ?? '#6b7280',
    'cta_primary_text' => $feature['hero_cta_primary_text'] ?? 'Get Started',
    'cta_primary_link' => $feature['hero_cta_primary_link'] ?? '#contact-form',
    'cta_primary_bg_color' => $feature['hero_cta_primary_bg_color'] ?? '#667eea',
    'cta_primary_text_color' => $feature['hero_cta_primary_text_color'] ?? '#FFFFFF',
    'cta_primary_hover_bg_color' => $feature['hero_cta_primary_hover_bg_color'] ?? '#5a6fd6',
    'cta_secondary_text' => $feature['hero_cta_secondary_text'] ?? 'Learn how it works',
    'cta_secondary_link' => $feature['hero_cta_secondary_link'] ?? '#how-it-works',
    'cta_secondary_text_color' => $feature['hero_cta_secondary_text_color'] ?? '#374151',
    'cta_secondary_border_color' => $feature['hero_cta_secondary_border_color'] ?? '#e5e7eb',
    'stats_enabled' => $feature['hero_stats_enabled'] ?? true,
    'stat1_value' => $feature['hero_stat1_value'] ?? '30+',
    'stat1_label' => $feature['hero_stat1_label'] ?? 'Modules',
    'stat2_value' => $feature['hero_stat2_value'] ?? '500+',
    'stat2_label' => $feature['hero_stat2_label'] ?? 'Businesses',
    'stat3_value' => $feature['hero_stat3_value'] ?? '24/7',
    'stat3_label' => $feature['hero_stat3_label'] ?? 'Support',
    'stats_value_color' => $feature['hero_stats_value_color'] ?? '#111827',
    'stats_label_color' => $feature['hero_stats_label_color'] ?? '#9ca3af',
    'breadcrumb_link_color' => $feature['hero_breadcrumb_link_color'] ?? '#9ca3af',
    'breadcrumb_active_color' => $feature['hero_breadcrumb_active_color'] ?? '#374151',
    'breadcrumb_separator_color' => $feature['hero_breadcrumb_separator_color'] ?? '#d1d5db',
];

// Theme color for the feature (use primary CTA color)
$colorTheme = $heroStyle['cta_primary_bg_color'];

// Dynamic key benefits section styling from database (with defaults)
$benefitsStyle = [
    'enabled' => $feature['benefits_section_enabled'] ?? true,
    'heading1' => $feature['benefits_section_heading1'] ?? 'Why Choose',
    'heading2' => $feature['benefits_section_heading2'] ?? '',
    'subheading' => $feature['benefits_section_subheading'] ?? 'Discover the key advantages that make this feature essential for your business operations.',
    'bg_color' => $feature['benefits_section_bg_color'] ?? '#0f172a',
    'heading_color' => $feature['benefits_section_heading_color'] ?? '#FFFFFF',
    'subheading_color' => $feature['benefits_section_subheading_color'] ?? 'rgba(255,255,255,0.6)',
    'card_bg_color' => $feature['benefits_card_bg_color'] ?? 'rgba(255,255,255,0.06)',
    'card_border_color' => $feature['benefits_card_border_color'] ?? 'rgba(255,255,255,0.1)',
    'card_hover_bg_color' => $feature['benefits_card_hover_bg_color'] ?? '#667eea',
    'card_title_color' => $feature['benefits_card_title_color'] ?? '#FFFFFF',
    'card_text_color' => $feature['benefits_card_text_color'] ?? 'rgba(255,255,255,0.5)',
    'card_icon_color' => $feature['benefits_card_icon_color'] ?? 'rgba(255,255,255,0.5)',
    'card_hover_text_color' => $feature['benefits_card_hover_text_color'] ?? '#FFFFFF',
];

// Dynamic "How It Works" section styling from database (with defaults)
$howItWorksStyle = [
    'enabled' => $feature['how_it_works_enabled'] ?? true,
    'badge' => $feature['how_it_works_badge'] ?? 'Simple Process',
    'heading' => $feature['how_it_works_heading'] ?? 'How It Works',
    'subheading' => $feature['how_it_works_subheading'] ?? 'Get started in four simple steps and transform your business operations',
    'bg_color' => $feature['how_it_works_bg_color'] ?? '#f9fafb',
    'badge_bg_color' => $feature['how_it_works_badge_bg_color'] ?? 'rgba(102, 126, 234, 0.1)',
    'badge_text_color' => $feature['how_it_works_badge_text_color'] ?? '#667eea',
    'heading_color' => $feature['how_it_works_heading_color'] ?? '#111827',
    'subheading_color' => $feature['how_it_works_subheading_color'] ?? '#6b7280',
    'card_bg_color' => $feature['how_it_works_card_bg_color'] ?? '#ffffff',
    'card_border_color' => $feature['how_it_works_card_border_color'] ?? '#e5e7eb',
    'card_hover_border_color' => $feature['how_it_works_card_hover_border_color'] ?? '#667eea',
    'step_color' => $feature['how_it_works_step_color'] ?? '#667eea',
    'step_bg_color' => $feature['how_it_works_step_bg_color'] ?? 'rgba(102, 126, 234, 0.1)',
    'title_color' => $feature['how_it_works_title_color'] ?? '#111827',
    'desc_color' => $feature['how_it_works_desc_color'] ?? '#6b7280',
    'connector_color' => $feature['how_it_works_connector_color'] ?? '#d1d5db',
];

// Default steps if none defined in database
$defaultHowItWorksSteps = [
    ['step' => '01', 'title' => 'Configure', 'description' => 'Set up the feature according to your business requirements with our intuitive configuration wizard.'],
    ['step' => '02', 'title' => 'Integrate', 'description' => 'Seamlessly connect with your existing systems and workflows for unified operations.'],
    ['step' => '03', 'title' => 'Automate', 'description' => 'Enable automated processes to reduce manual work and increase efficiency.'],
    ['step' => '04', 'title' => 'Analyze', 'description' => 'Get real-time insights and reports to make data-driven decisions.']
];

// Use database steps if available, otherwise use defaults
$howItWorks = !empty($feature['how_it_works_steps']) && is_array($feature['how_it_works_steps']) 
    ? $feature['how_it_works_steps'] 
    : $defaultHowItWorksSteps;

// Dynamic "Feature Highlights" section styling from database (with defaults)
$highlightsStyle = [
    'enabled' => $feature['highlights_enabled'] ?? true,
    'badge' => $feature['highlights_badge'] ?? 'Capabilities',
    'heading' => $feature['highlights_heading'] ?? 'Feature Highlights',
    'subheading' => $feature['highlights_subheading'] ?? 'Powerful capabilities designed to streamline your business processes',
    'bg_color' => $feature['highlights_bg_color'] ?? 'linear-gradient(180deg, #f8fafc 0%, #fff 100%)',
    'badge_bg_color' => $feature['highlights_badge_bg_color'] ?? 'rgba(102, 126, 234, 0.1)',
    'badge_text_color' => $feature['highlights_badge_text_color'] ?? '#667eea',
    'heading_color' => $feature['highlights_heading_color'] ?? '#111827',
    'subheading_color' => $feature['highlights_subheading_color'] ?? '#6b7280',
    'card_bg_color' => $feature['highlights_card_bg_color'] ?? '#ffffff',
    'card_border_color' => $feature['highlights_card_border_color'] ?? '#e5e7eb',
    'card_hover_border_color' => $feature['highlights_card_hover_border_color'] ?? '#667eea',
    'icon_bg_color' => $feature['highlights_icon_bg_color'] ?? 'rgba(102, 126, 234, 0.1)',
    'icon_color' => $feature['highlights_icon_color'] ?? '#667eea',
    'title_color' => $feature['highlights_title_color'] ?? '#111827',
    'desc_color' => $feature['highlights_desc_color'] ?? '#6b7280',
];

// Default highlights if none defined in database
$defaultFeatureHighlights = [
    ['icon' => 'dashboard', 'title' => 'Intuitive Dashboard', 'description' => 'Access all key metrics and actions from a centralized, easy-to-use dashboard.'],
    ['icon' => 'automation', 'title' => 'Smart Automation', 'description' => 'Automate repetitive tasks and workflows to save time and reduce errors.'],
    ['icon' => 'integration', 'title' => 'Seamless Integration', 'description' => 'Connect with other modules and third-party applications effortlessly.'],
    ['icon' => 'reports', 'title' => 'Advanced Reports', 'description' => 'Generate comprehensive reports with customizable filters and export options.'],
    ['icon' => 'security', 'title' => 'Enterprise Security', 'description' => 'Role-based access control and audit trails for complete data security.'],
    ['icon' => 'mobile', 'title' => 'Mobile Ready', 'description' => 'Access features on-the-go with our responsive mobile interface.']
];

// Use database highlights if available, otherwise use defaults
$featureHighlights = !empty($feature['highlights_cards']) && is_array($feature['highlights_cards']) 
    ? $feature['highlights_cards'] 
    : $defaultFeatureHighlights;

// Dynamic "Use Cases" section styling from database (with defaults)
$useCasesStyle = [
    'enabled' => $feature['use_cases_enabled'] ?? true,
    'badge' => $feature['use_cases_badge'] ?? 'Industries',
    'heading' => $feature['use_cases_heading'] ?? 'Use Cases',
    'subheading' => $feature['use_cases_subheading'] ?? 'See how different industries leverage this feature to drive success',
    'bg_color' => $feature['use_cases_bg_color'] ?? 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)',
    'badge_bg_color' => $feature['use_cases_badge_bg_color'] ?? 'rgba(255, 255, 255, 0.1)',
    'badge_text_color' => $feature['use_cases_badge_text_color'] ?? '#ffffff',
    'heading_color' => $feature['use_cases_heading_color'] ?? '#ffffff',
    'subheading_color' => $feature['use_cases_subheading_color'] ?? 'rgba(255, 255, 255, 0.7)',
    'card_bg_color' => $feature['use_cases_card_bg_color'] ?? 'rgba(255, 255, 255, 0.05)',
    'card_border_color' => $feature['use_cases_card_border_color'] ?? 'rgba(255, 255, 255, 0.1)',
    'card_hover_border_color' => $feature['use_cases_card_hover_border_color'] ?? '#667eea',
    'title_color' => $feature['use_cases_title_color'] ?? '#ffffff',
    'desc_color' => $feature['use_cases_desc_color'] ?? 'rgba(255, 255, 255, 0.7)',
    'overlay_color' => $feature['use_cases_overlay_color'] ?? 'linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%)',
];

// Default use cases if none defined in database
$defaultUseCases = [
    ['industry' => 'Manufacturing', 'description' => 'Streamline production planning, inventory management, and quality control processes.', 'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=400&h=300&fit=crop'],
    ['industry' => 'Retail', 'description' => 'Manage multi-location inventory, POS integration, and customer relationships.', 'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&h=300&fit=crop'],
    ['industry' => 'Healthcare', 'description' => 'Handle patient records, appointment scheduling, and billing with compliance.', 'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=400&h=300&fit=crop'],
    ['industry' => 'Education', 'description' => 'Manage student information, course scheduling, and fee collection efficiently.', 'image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400&h=300&fit=crop']
];

// Use database use cases if available, otherwise use defaults
$useCases = !empty($feature['use_cases_cards']) && is_array($feature['use_cases_cards']) 
    ? $feature['use_cases_cards'] 
    : $defaultUseCases;

// Dynamic FAQs section styling from database (with defaults)
$faqsStyle = [
    'theme' => $feature['faqs_section_theme'] ?? 'light',
    'heading' => $feature['faqs_section_heading'] ?? 'Frequently Asked Questions',
    'subheading' => $feature['faqs_section_subheading'] ?? 'Everything you need to know about this feature. Can\'t find what you\'re looking for?',
];

// Default FAQs if none defined in database
$defaultFaqs = [
    ['question' => 'How long does it take to implement this feature?', 'answer' => 'Implementation typically takes 1-2 weeks depending on your existing setup and customization requirements. Our team provides full support throughout the process.'],
    ['question' => 'Can this feature be customized for my business?', 'answer' => 'Yes, the feature is highly customizable. You can configure workflows, fields, reports, and integrations to match your specific business processes.'],
    ['question' => 'Is training provided for this feature?', 'answer' => 'Absolutely! We provide comprehensive training materials including video tutorials, documentation, and live training sessions for your team.'],
    ['question' => 'What kind of support is available?', 'answer' => 'We offer 24/7 technical support via chat, email, and phone. Enterprise customers also get a dedicated account manager.']
];

// Use database FAQs if available, otherwise use defaults
$faqs = !empty($feature['faqs_cards']) && is_array($feature['faqs_cards']) 
    ? $feature['faqs_cards'] 
    : $defaultFaqs;

include_header($page_title, $page_description);
?>

<!-- Hero Section - Minimalist Bold Typography Design with Dynamic Styling -->
<section class="feat-hero" style="
    --theme-color: <?php echo htmlspecialchars($colorTheme); ?>;
    --hero-bg-color: <?php echo htmlspecialchars($heroStyle['bg_color']); ?>;
    --hero-bg-gradient-start: <?php echo htmlspecialchars($heroStyle['bg_gradient_start']); ?>;
    --hero-bg-gradient-end: <?php echo htmlspecialchars($heroStyle['bg_gradient_end']); ?>;
    --hero-title-gradient-start: <?php echo htmlspecialchars($heroStyle['title_gradient_start']); ?>;
    --hero-title-gradient-middle: <?php echo htmlspecialchars($heroStyle['title_gradient_middle']); ?>;
    --hero-title-gradient-end: <?php echo htmlspecialchars($heroStyle['title_gradient_end']); ?>;
    --hero-subtitle-color: <?php echo htmlspecialchars($heroStyle['subtitle_color']); ?>;
    --hero-cta-primary-bg: <?php echo htmlspecialchars($heroStyle['cta_primary_bg_color']); ?>;
    --hero-cta-primary-text: <?php echo htmlspecialchars($heroStyle['cta_primary_text_color']); ?>;
    --hero-cta-primary-hover-bg: <?php echo htmlspecialchars($heroStyle['cta_primary_hover_bg_color']); ?>;
    --hero-cta-secondary-text: <?php echo htmlspecialchars($heroStyle['cta_secondary_text_color']); ?>;
    --hero-cta-secondary-border: <?php echo htmlspecialchars($heroStyle['cta_secondary_border_color']); ?>;
    --hero-stats-value-color: <?php echo htmlspecialchars($heroStyle['stats_value_color']); ?>;
    --hero-stats-label-color: <?php echo htmlspecialchars($heroStyle['stats_label_color']); ?>;
    --hero-breadcrumb-link-color: <?php echo htmlspecialchars($heroStyle['breadcrumb_link_color']); ?>;
    --hero-breadcrumb-active-color: <?php echo htmlspecialchars($heroStyle['breadcrumb_active_color']); ?>;
    --hero-breadcrumb-separator-color: <?php echo htmlspecialchars($heroStyle['breadcrumb_separator_color']); ?>;
">
    <!-- Subtle geometric decorations -->
    <div class="feat-hero-decor">
        <div class="feat-hero-line feat-hero-line-1"></div>
        <div class="feat-hero-line feat-hero-line-2"></div>
        <div class="feat-hero-circle feat-hero-circle-1"></div>
        <div class="feat-hero-circle feat-hero-circle-2"></div>
    </div>
    
    <div class="container">
        <!-- Breadcrumb - minimal style -->
        <nav class="feat-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo get_base_url(); ?>/">Home</a>
            <span class="feat-breadcrumb-sep">/</span>
            <a href="<?php echo get_base_url(); ?>/features.php">Features</a>
            <span class="feat-breadcrumb-sep">/</span>
            <span aria-current="page"><?php echo htmlspecialchars($feature['name']); ?></span>
        </nav>
        
        <!-- Main Hero Content -->
        <div class="feat-hero-content">
            <!-- Large gradient title -->
            <h1 class="feat-hero-title">
                <span class="feat-hero-title-gradient"><?php echo htmlspecialchars($feature['name']); ?></span>
            </h1>
            
            <!-- Clean description -->
            <?php if (!empty($feature['description'])): ?>
                <p class="feat-hero-subtitle"><?php echo htmlspecialchars($feature['description']); ?></p>
            <?php endif; ?>
            
            <!-- Single accent CTA -->
            <div class="feat-hero-actions">
                <a href="<?php echo htmlspecialchars($heroStyle['cta_primary_link']); ?>" class="feat-btn-primary">
                    <?php echo htmlspecialchars($heroStyle['cta_primary_text']); ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="<?php echo htmlspecialchars($heroStyle['cta_secondary_link']); ?>" class="feat-btn-text">
                    <?php echo htmlspecialchars($heroStyle['cta_secondary_text']); ?>
                </a>
            </div>
            
            <!-- Minimal stats row -->
            <?php if ($heroStyle['stats_enabled']): ?>
            <div class="feat-hero-stats">
                <div class="feat-hero-stat">
                    <span class="feat-hero-stat-value"><?php echo htmlspecialchars($heroStyle['stat1_value']); ?></span>
                    <span class="feat-hero-stat-label"><?php echo htmlspecialchars($heroStyle['stat1_label']); ?></span>
                </div>
                <div class="feat-hero-stat">
                    <span class="feat-hero-stat-value"><?php echo htmlspecialchars($heroStyle['stat2_value']); ?></span>
                    <span class="feat-hero-stat-label"><?php echo htmlspecialchars($heroStyle['stat2_label']); ?></span>
                </div>
                <div class="feat-hero-stat">
                    <span class="feat-hero-stat-value"><?php echo htmlspecialchars($heroStyle['stat3_value']); ?></span>
                    <span class="feat-hero-stat-label"><?php echo htmlspecialchars($heroStyle['stat3_label']); ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
    </div>
    
    <?php if (!empty($clientLogos)): ?>
    <!-- Client Logo Marquee -->
    <div class="client-logo-marquee" aria-label="Our Clients">
        <div class="marquee-track">
            <div class="marquee-content">
                <?php foreach ($clientLogos as $logo): ?>
                    <?php if (!empty($logo['website_url'])): ?>
                        <a href="<?php echo htmlspecialchars($logo['website_url']); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="marquee-logo-link"
                           title="<?php echo htmlspecialchars($logo['client_name']); ?>">
                            <img src="<?php echo htmlspecialchars($logo['logo_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($logo['client_name']); ?>" 
                                 class="marquee-logo" loading="lazy">
                        </a>
                    <?php else: ?>
                        <span class="marquee-logo-link" title="<?php echo htmlspecialchars($logo['client_name']); ?>">
                            <img src="<?php echo htmlspecialchars($logo['logo_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($logo['client_name']); ?>" 
                                 class="marquee-logo" loading="lazy">
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <!-- Duplicate for seamless loop -->
            <div class="marquee-content" aria-hidden="true">
                <?php foreach ($clientLogos as $logo): ?>
                    <?php if (!empty($logo['website_url'])): ?>
                        <a href="<?php echo htmlspecialchars($logo['website_url']); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="marquee-logo-link"
                           tabindex="-1">
                            <img src="<?php echo htmlspecialchars($logo['logo_url']); ?>" 
                                 alt="" 
                                 class="marquee-logo" loading="lazy">
                        </a>
                    <?php else: ?>
                        <span class="marquee-logo-link">
                            <img src="<?php echo htmlspecialchars($logo['logo_url']); ?>" 
                                 alt="" 
                                 class="marquee-logo" loading="lazy">
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php if ($benefitsStyle['enabled'] && !empty($feature['benefits_cards']) && is_array($feature['benefits_cards'])): ?>
<!-- Key Benefits Section - Razorpay Style Cards with Dynamic Styling (JSON-based) -->
<section class="feat-benefits-section" style="
    --benefits-bg-color: <?php echo htmlspecialchars($benefitsStyle['bg_color']); ?>;
    --benefits-heading-color: <?php echo htmlspecialchars($benefitsStyle['heading_color']); ?>;
    --benefits-subheading-color: <?php echo htmlspecialchars($benefitsStyle['subheading_color']); ?>;
    --benefits-card-bg: <?php echo htmlspecialchars($benefitsStyle['card_bg_color']); ?>;
    --benefits-card-border: <?php echo htmlspecialchars($benefitsStyle['card_border_color']); ?>;
    --benefits-card-hover-bg: <?php echo htmlspecialchars($benefitsStyle['card_hover_bg_color']); ?>;
    --benefits-card-title-color: <?php echo htmlspecialchars($benefitsStyle['card_title_color']); ?>;
    --benefits-card-text-color: <?php echo htmlspecialchars($benefitsStyle['card_text_color']); ?>;
    --benefits-card-icon-color: <?php echo htmlspecialchars($benefitsStyle['card_icon_color']); ?>;
    --benefits-card-hover-text: <?php echo htmlspecialchars($benefitsStyle['card_hover_text_color']); ?>;
">
    <div class="container">
        <div class="feat-benefits-content">
            <div class="feat-benefits-text">
                <h2 class="feat-benefits-heading">
                    <span class="feat-benefits-heading-line"><?php echo htmlspecialchars($benefitsStyle['heading1']); ?></span>
                    <span class="feat-benefits-heading-line"><?php echo htmlspecialchars(!empty($benefitsStyle['heading2']) ? $benefitsStyle['heading2'] : $feature['name'] . '?'); ?></span>
                </h2>
                <p class="feat-benefits-subheading"><?php echo htmlspecialchars($benefitsStyle['subheading']); ?></p>
            </div>
            <div class="feat-benefits-cards">
                <?php 
                $cardCount = 0;
                foreach ($feature['benefits_cards'] as $card): 
                    if ($cardCount >= 4) break;
                    $cardCount++;
                ?>
                    <div class="feat-benefits-card">
                        <div class="feat-benefits-card-icon">
                            <?php echo get_feature_icon($card['icon'] ?? 'check'); ?>
                        </div>
                        <h3 class="feat-benefits-card-title"><?php echo htmlspecialchars(substr($card['title'] ?? '', 0, 24)); ?></h3>
                        <p class="feat-benefits-card-desc"><?php echo htmlspecialchars(substr($card['description'] ?? '', 0, 120)); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if ($howItWorksStyle['enabled']): ?>
<!-- How It Works Section - Dynamic Styling -->
<section class="feat-how-it-works" id="how-it-works" style="
    --hiw-bg-color: <?php echo htmlspecialchars($howItWorksStyle['bg_color']); ?>;
    --hiw-badge-bg: <?php echo htmlspecialchars($howItWorksStyle['badge_bg_color']); ?>;
    --hiw-badge-text: <?php echo htmlspecialchars($howItWorksStyle['badge_text_color']); ?>;
    --hiw-heading-color: <?php echo htmlspecialchars($howItWorksStyle['heading_color']); ?>;
    --hiw-subheading-color: <?php echo htmlspecialchars($howItWorksStyle['subheading_color']); ?>;
    --hiw-card-bg: <?php echo htmlspecialchars($howItWorksStyle['card_bg_color']); ?>;
    --hiw-card-border: <?php echo htmlspecialchars($howItWorksStyle['card_border_color']); ?>;
    --hiw-card-hover-border: <?php echo htmlspecialchars($howItWorksStyle['card_hover_border_color']); ?>;
    --hiw-step-color: <?php echo htmlspecialchars($howItWorksStyle['step_color']); ?>;
    --hiw-step-bg: <?php echo htmlspecialchars($howItWorksStyle['step_bg_color']); ?>;
    --hiw-title-color: <?php echo htmlspecialchars($howItWorksStyle['title_color']); ?>;
    --hiw-desc-color: <?php echo htmlspecialchars($howItWorksStyle['desc_color']); ?>;
    --hiw-connector-color: <?php echo htmlspecialchars($howItWorksStyle['connector_color']); ?>;
">
    <div class="container">
        <div class="feat-section-header">
            <span class="feat-section-badge"><?php echo htmlspecialchars($howItWorksStyle['badge']); ?></span>
            <h2 class="feat-section-title"><?php echo htmlspecialchars($howItWorksStyle['heading']); ?></h2>
            <p class="feat-section-subtitle"><?php echo htmlspecialchars($howItWorksStyle['subheading']); ?></p>
        </div>
        
        <div class="feat-workflow-grid">
            <?php foreach ($howItWorks as $index => $step): ?>
                <div class="feat-workflow-card" data-step="<?php echo $index + 1; ?>">
                    <div class="feat-workflow-step"><?php echo htmlspecialchars($step['step'] ?? sprintf('%02d', $index + 1)); ?></div>
                    <h3 class="feat-workflow-title"><?php echo htmlspecialchars($step['title']); ?></h3>
                    <p class="feat-workflow-desc"><?php echo htmlspecialchars($step['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if ($highlightsStyle['enabled']): ?>
<!-- Feature Highlights Section - Dynamic Styling -->
<section class="feat-highlights-section" style="
    --hl-bg-color: <?php echo htmlspecialchars($highlightsStyle['bg_color']); ?>;
    --hl-badge-bg: <?php echo htmlspecialchars($highlightsStyle['badge_bg_color']); ?>;
    --hl-badge-text: <?php echo htmlspecialchars($highlightsStyle['badge_text_color']); ?>;
    --hl-heading-color: <?php echo htmlspecialchars($highlightsStyle['heading_color']); ?>;
    --hl-subheading-color: <?php echo htmlspecialchars($highlightsStyle['subheading_color']); ?>;
    --hl-card-bg: <?php echo htmlspecialchars($highlightsStyle['card_bg_color']); ?>;
    --hl-card-border: <?php echo htmlspecialchars($highlightsStyle['card_border_color']); ?>;
    --hl-card-hover-border: <?php echo htmlspecialchars($highlightsStyle['card_hover_border_color']); ?>;
    --hl-icon-bg: <?php echo htmlspecialchars($highlightsStyle['icon_bg_color']); ?>;
    --hl-icon-color: <?php echo htmlspecialchars($highlightsStyle['icon_color']); ?>;
    --hl-title-color: <?php echo htmlspecialchars($highlightsStyle['title_color']); ?>;
    --hl-desc-color: <?php echo htmlspecialchars($highlightsStyle['desc_color']); ?>;
">
    <div class="container">
        <div class="feat-section-header">
            <span class="feat-section-badge"><?php echo htmlspecialchars($highlightsStyle['badge']); ?></span>
            <h2 class="feat-section-title"><?php echo htmlspecialchars($highlightsStyle['heading']); ?></h2>
            <p class="feat-section-subtitle"><?php echo htmlspecialchars($highlightsStyle['subheading']); ?></p>
        </div>
        
        <div class="feat-highlights-grid">
            <?php foreach ($featureHighlights as $highlight): ?>
                <div class="feat-highlight-card">
                    <div class="feat-highlight-icon">
                        <?php echo get_feature_icon($highlight['icon'] ?? 'check'); ?>
                    </div>
                    <h3 class="feat-highlight-title"><?php echo htmlspecialchars($highlight['title']); ?></h3>
                    <p class="feat-highlight-desc"><?php echo htmlspecialchars($highlight['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if (!empty($feature['screenshots']) && is_array($feature['screenshots'])): ?>
<!-- Screenshots Gallery Section -->
<section class="feat-screenshots-section">
    <div class="container">
        <div class="feat-section-header">
            <span class="feat-section-badge">Visual Tour</span>
            <h2 class="feat-section-title">See It In Action</h2>
            <p class="feat-section-subtitle">Take a closer look at the intuitive interface and powerful features</p>
        </div>
        
        <div class="feat-screenshots-carousel">
            <div class="feat-screenshots-track">
                <?php foreach ($feature['screenshots'] as $index => $screenshot): ?>
                    <div class="feat-screenshot-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($screenshot); ?>" 
                             alt="Screenshot <?php echo $index + 1; ?>" 
                             loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($feature['screenshots']) > 1): ?>
                <div class="feat-screenshots-nav">
                    <button class="feat-screenshot-prev" aria-label="Previous screenshot">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <div class="feat-screenshots-dots">
                        <?php foreach ($feature['screenshots'] as $index => $screenshot): ?>
                            <button class="feat-screenshot-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                    data-index="<?php echo $index; ?>" 
                                    aria-label="Go to screenshot <?php echo $index + 1; ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <button class="feat-screenshot-next" aria-label="Next screenshot">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if ($useCasesStyle['enabled']): ?>
<!-- Use Cases Section - Dynamic Styling -->
<section class="feat-usecases-section" style="
    --uc-bg-color: <?php echo htmlspecialchars($useCasesStyle['bg_color']); ?>;
    --uc-badge-bg: <?php echo htmlspecialchars($useCasesStyle['badge_bg_color']); ?>;
    --uc-badge-text: <?php echo htmlspecialchars($useCasesStyle['badge_text_color']); ?>;
    --uc-heading-color: <?php echo htmlspecialchars($useCasesStyle['heading_color']); ?>;
    --uc-subheading-color: <?php echo htmlspecialchars($useCasesStyle['subheading_color']); ?>;
    --uc-card-bg: <?php echo htmlspecialchars($useCasesStyle['card_bg_color']); ?>;
    --uc-card-border: <?php echo htmlspecialchars($useCasesStyle['card_border_color']); ?>;
    --uc-card-hover-border: <?php echo htmlspecialchars($useCasesStyle['card_hover_border_color']); ?>;
    --uc-title-color: <?php echo htmlspecialchars($useCasesStyle['title_color']); ?>;
    --uc-desc-color: <?php echo htmlspecialchars($useCasesStyle['desc_color']); ?>;
    --uc-overlay: <?php echo htmlspecialchars($useCasesStyle['overlay_color']); ?>;
">
    <div class="container">
        <div class="feat-section-header feat-section-header-light">
            <span class="feat-section-badge feat-section-badge-light"><?php echo htmlspecialchars($useCasesStyle['badge']); ?></span>
            <h2 class="feat-section-title feat-section-title-light"><?php echo htmlspecialchars($useCasesStyle['heading']); ?></h2>
            <p class="feat-section-subtitle feat-section-subtitle-light"><?php echo htmlspecialchars($useCasesStyle['subheading']); ?></p>
        </div>
        
        <div class="feat-usecases-grid">
            <?php foreach ($useCases as $useCase): ?>
                <div class="feat-usecase-card">
                    <div class="feat-usecase-image">
                        <img src="<?php echo htmlspecialchars($useCase['image'] ?? ''); ?>" 
                             alt="<?php echo htmlspecialchars($useCase['industry'] ?? ''); ?>" 
                             loading="lazy">
                        <div class="feat-usecase-overlay"></div>
                    </div>
                    <div class="feat-usecase-content">
                        <h3 class="feat-usecase-title"><?php echo htmlspecialchars($useCase['industry'] ?? ''); ?></h3>
                        <p class="feat-usecase-desc"><?php echo htmlspecialchars($useCase['description'] ?? ''); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>



<?php if (!empty($relatedSolutions)): ?>
<!-- Related Solutions Section -->
<section class="feat-related-section">
    <div class="container">
        <div class="feat-section-header">
            <span class="feat-section-badge">Explore More</span>
            <h2 class="feat-section-title">Related Solutions</h2>
            <p class="feat-section-subtitle">Discover solutions that include this feature and more</p>
        </div>
        
        <div class="feat-related-grid">
            <?php foreach ($relatedSolutions as $solution): ?>
                <a href="<?php echo get_base_url(); ?>/solution.php?slug=<?php echo urlencode($solution['slug']); ?>" 
                   class="feat-related-card">
                    <?php if (!empty($solution['icon_image'])): ?>
                        <div class="feat-related-icon">
                            <img src="<?php echo htmlspecialchars($solution['icon_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($solution['name']); ?>">
                        </div>
                    <?php else: ?>
                        <div class="feat-related-icon feat-related-icon-default">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <h3 class="feat-related-title"><?php echo htmlspecialchars($solution['name']); ?></h3>
                    <?php if (!empty($solution['description'])): ?>
                        <p class="feat-related-desc"><?php echo htmlspecialchars(substr($solution['description'], 0, 100)); ?>...</p>
                    <?php endif; ?>
                    <span class="feat-related-link">
                        Learn more
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- FAQs Section - Professional Design with Dark/Light Theme Support -->
<?php if (!empty($faqs)): ?>
<section class="feat-faqs-section feat-faqs-<?php echo htmlspecialchars($faqsStyle['theme']); ?>" id="faqs" style="--theme-color: <?php echo htmlspecialchars($colorTheme); ?>;">
    <div class="feat-faqs-bg">
        <div class="feat-faqs-gradient"></div>
        <div class="feat-faqs-pattern"></div>
        <div class="feat-faqs-orb feat-faqs-orb-1"></div>
        <div class="feat-faqs-orb feat-faqs-orb-2"></div>
    </div>
    <div class="container">
        <div class="feat-faqs-layout">
            <div class="feat-faqs-sidebar">
                <div class="feat-faqs-sidebar-content">
                    <h2 class="feat-faqs-title"><?php echo nl2br(htmlspecialchars($faqsStyle['heading'])); ?></h2>
                    <p class="feat-faqs-subtitle"><?php echo htmlspecialchars($faqsStyle['subheading']); ?></p>
                    <a href="#contact-form" class="feat-faqs-contact-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Contact Support
                    </a>
                </div>
                <div class="feat-faqs-decoration">
                    <div class="feat-faqs-decoration-circle feat-faqs-decoration-circle-1"></div>
                    <div class="feat-faqs-decoration-circle feat-faqs-decoration-circle-2"></div>
                    <div class="feat-faqs-decoration-circle feat-faqs-decoration-circle-3"></div>
                </div>
            </div>
            
            <div class="feat-faqs-content">
                <div class="feat-faqs-list">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <div class="feat-faq-item" data-faq-index="<?php echo $index; ?>">
                            <button class="feat-faq-question" aria-expanded="false" aria-controls="faq-<?php echo $index; ?>">
                                <span class="feat-faq-number"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                                <span class="feat-faq-question-text"><?php echo htmlspecialchars($faq['question']); ?></span>
                                <span class="feat-faq-toggle">
                                    <svg class="feat-faq-icon-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"/>
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                    <svg class="feat-faq-icon-minus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                </span>
                            </button>
                            <div class="feat-faq-answer" id="faq-<?php echo $index; ?>">
                                <div class="feat-faq-answer-inner">
                                    <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- FAQ Footer -->
                <div class="feat-faqs-footer">
                    <div class="feat-faqs-footer-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="feat-faqs-footer-text">
                        <strong>Still have questions?</strong>
                        <span>Our team is here to help you 24/7</span>
                    </div>
                    <a href="#contact-form" class="feat-faqs-footer-btn">Get in Touch</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- CTA Section -->
<div id="contact-form">
<?php
$cta_title = "Ready to Get Started with " . htmlspecialchars($feature['name']) . "?";
$cta_subtitle = "Contact us today to learn how this feature can transform your business operations";
$cta_source = "feature-" . htmlspecialchars($feature['slug']);
include __DIR__ . '/../templates/cta-form.php';
?>
</div>


<?php
// Helper function for feature icons
function get_feature_icon($icon) {
    $icons = [
        'speed' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
        'security' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
        'chart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
        'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
        'globe' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>',
        'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'dashboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
        'automation' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>',
        'integration' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>',
        'reports' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
        'mobile' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>',
    ];
    return $icons[$icon] ?? $icons['speed'];
}
?>



<style>
/* ============================================
   Feature Detail Page - Modern Razorpay Style
   ============================================ */

/* Not Found Card */
.feat-not-found-card {
    text-align: center;
    padding: 80px 40px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.feat-not-found-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 24px;
    background: #fef2f2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.feat-not-found-icon svg {
    width: 40px;
    height: 40px;
    stroke: #ef4444;
}

.feat-not-found-card h1 {
    font-size: 28px;
    color: #111827;
    margin-bottom: 12px;
}

.feat-not-found-card p {
    color: #6b7280;
    margin-bottom: 24px;
}

/* Hero Section - Minimalist Bold Typography Design with Dynamic Styling */
.feat-hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: visible;
    background: var(--hero-bg-color, #fafafa);
    background: linear-gradient(135deg, var(--hero-bg-gradient-start, #fafafa) 0%, var(--hero-bg-gradient-end, #f5f5f5) 100%);
    padding-bottom: 0;
}

.feat-hero > .container {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    flex: 1;
    padding-top: 80px;
    padding-bottom: 24px;
}

/* Geometric Decorations */
.feat-hero-decor {
    position: absolute;
    inset: 0;
    pointer-events: none;
    overflow: hidden;
}

.feat-hero-line {
    position: absolute;
    background: linear-gradient(180deg, transparent, rgba(102, 126, 234, 0.08), transparent);
    width: 1px;
}

.feat-hero-line-1 {
    left: 15%;
    top: 0;
    height: 60%;
}

.feat-hero-line-2 {
    right: 20%;
    bottom: 0;
    height: 50%;
}

.feat-hero-circle {
    position: absolute;
    border-radius: 50%;
    border: 1px solid rgba(102, 126, 234, 0.1);
}

.feat-hero-circle-1 {
    width: 400px;
    height: 400px;
    top: -100px;
    right: -100px;
}

.feat-hero-circle-2 {
    width: 300px;
    height: 300px;
    bottom: -50px;
    left: -80px;
}

/* Breadcrumb - Minimal with Dynamic Colors */
.feat-breadcrumb {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
    margin-bottom: 32px;
}

.feat-breadcrumb a {
    color: var(--hero-breadcrumb-link-color, #9ca3af);
    text-decoration: none;
    transition: color 0.2s;
}

.feat-breadcrumb a:hover {
    color: var(--hero-breadcrumb-active-color, #111827);
}

.feat-breadcrumb-sep {
    color: var(--hero-breadcrumb-separator-color, #d1d5db);
}

.feat-breadcrumb span[aria-current] {
    color: var(--hero-breadcrumb-active-color, #374151);
}

/* Hero Content */
.feat-hero-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    max-width: 900px;
}

/* Bold Gradient Title - Matching Solution Page */
.feat-hero-title {
    font-size: clamp(36px, 5vw, 56px);
    font-weight: 700;
    line-height: 1.1;
    margin: 0 0 20px;
    letter-spacing: -0.02em;
}

.feat-hero-title-gradient {
    background: linear-gradient(135deg, var(--hero-title-gradient-start, #111827) 0%, var(--hero-title-gradient-middle, #667eea) 50%, var(--hero-title-gradient-end, #764ba2) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Subtitle - Matching Solution Page with Dynamic Color */
.feat-hero-subtitle {
    font-size: 18px;
    line-height: 1.7;
    color: var(--hero-subtitle-color, #6b7280);
    margin: 0 0 32px;
    max-width: 540px;
    font-weight: 400;
}

/* Actions - Matching Solution Page */
.feat-hero-actions {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 40px;
}

.feat-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    background: var(--hero-cta-primary-bg, #667eea);
    color: var(--hero-cta-primary-text, #fff);
    font-weight: 600;
    font-size: 15px;
    border-radius: 12px;
    border: 1px solid var(--hero-cta-primary-bg, #667eea);
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);
}

.feat-btn-primary:hover {
    background: var(--hero-cta-primary-hover-bg, #5a6fd6);
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.3);
}

.feat-btn-primary svg {
    width: 20px;
    height: 20px;
    transition: transform 0.3s ease;
}

.feat-btn-primary:hover svg {
    transform: translateX(4px);
}

.feat-btn-text {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    background: transparent;
    color: var(--hero-cta-secondary-text, #374151);
    font-weight: 600;
    font-size: 15px;
    border-radius: 12px;
    border: 1px solid var(--hero-cta-secondary-border, #e5e7eb);
    text-decoration: none;
    transition: all 0.3s ease;
}

.feat-btn-text:hover {
    background: #f9fafb;
    border-color: var(--hero-cta-secondary-border, #d1d5db);
    transform: translateY(-2px);
}

/* Stats - Minimal with Dynamic Colors */
.feat-hero-stats {
    display: flex;
    gap: 40px;
}

.feat-hero-stat {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.feat-hero-stat-value {
    font-size: 26px;
    font-weight: 700;
    color: var(--hero-stats-value-color, #111827);
    line-height: 1;
    letter-spacing: -0.02em;
}

.feat-hero-stat-label {
    font-size: 11px;
    color: var(--hero-stats-label-color, #9ca3af);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}



/* Keep outline button for other sections */
.feat-btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    background: #fff;
    color: #374151;
    font-weight: 600;
    font-size: 15px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    text-decoration: none;
    transition: all 0.3s ease;
}

.feat-btn-outline:hover {
    border-color: var(--theme-color, #667eea);
    color: var(--theme-color, #667eea);
    background: #fafaff;
}

/* Feature Page - Client Logo Marquee */
.feat-hero .client-logo-marquee {
    height: 100px;
    min-height: 100px;
    background: var(--hero-bg-color, #fafafa);
    display: flex;
    align-items: center;
    overflow: hidden;
    position: relative;
    z-index: 10;
    margin-top: auto;
    padding: 20px 0;
}

.feat-hero .client-logo-marquee::before,
.feat-hero .client-logo-marquee::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 80px;
    z-index: 3;
    pointer-events: none;
}

.feat-hero .client-logo-marquee::before {
    left: 0;
    background: linear-gradient(90deg, #fafafa 0%, transparent 100%);
}

.feat-hero .client-logo-marquee::after {
    right: 0;
    background: linear-gradient(270deg, #fafafa 0%, transparent 100%);
}

.feat-hero .marquee-track {
    display: flex;
    width: max-content;
    animation: marqueeScroll 30s linear infinite;
    position: relative;
    z-index: 1;
}

.feat-hero .marquee-content {
    display: flex;
    align-items: center;
    gap: 48px;
    padding: 0 24px;
    position: relative;
    z-index: 1;
}

.feat-hero .marquee-logo-link {
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    filter: none;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.feat-hero .marquee-logo-link:hover {
    opacity: 1;
    filter: none;
    transform: scale(1.05);
}

.feat-hero .marquee-logo {
    max-height: 36px;
    max-width: 100px;
    width: auto;
    height: auto;
    filter: none !important;
    opacity: 1 !important;
    object-fit: contain;
}

@keyframes marqueeScroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}



/* Key Benefits Section - Dynamic Styling */
.feat-benefits-section {
    background: var(--benefits-bg-color, linear-gradient(135deg, #0f172a 0%, #1e293b 100%));
    padding: 100px 0;
    overflow: hidden;
}

.feat-benefits-content {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 60px;
    align-items: flex-start;
}

.feat-benefits-text {
    position: sticky;
    top: 120px;
}

.feat-benefits-heading {
    font-size: clamp(32px, 4vw, 48px);
    font-weight: 300;
    line-height: 1.2;
    margin: 0 0 20px;
    color: var(--benefits-heading-color, #fff);
}

.feat-benefits-heading-line {
    display: block;
}

.feat-benefits-subheading {
    font-size: 16px;
    line-height: 1.7;
    color: var(--benefits-subheading-color, rgba(255, 255, 255, 0.6));
    margin: 0;
    max-width: 400px;
}

.feat-benefits-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.feat-benefits-card {
    background: var(--benefits-card-bg, rgba(255, 255, 255, 0.06));
    border: 1px solid var(--benefits-card-border, rgba(255, 255, 255, 0.1));
    border-radius: 16px;
    padding: 24px;
    min-height: 180px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}

.feat-benefits-card:hover {
    background: var(--benefits-card-hover-bg, var(--theme-color, #667eea));
    border-color: var(--benefits-card-hover-bg, var(--theme-color, #667eea));
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
}

.feat-benefits-card-icon {
    width: 32px;
    height: 32px;
    margin-bottom: 16px;
    color: var(--benefits-card-icon-color, rgba(255, 255, 255, 0.5));
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}

.feat-benefits-card:hover .feat-benefits-card-icon {
    opacity: 0;
    height: 0;
    margin-bottom: 0;
    overflow: hidden;
}

.feat-benefits-card-icon svg {
    width: 100%;
    height: 100%;
}

.feat-benefits-card-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--benefits-card-title-color, #fff);
    margin: 0 0 8px;
    transition: color 0.3s ease;
}

.feat-benefits-card:hover .feat-benefits-card-title {
    color: var(--benefits-card-hover-text, #fff);
}

.feat-benefits-card-desc {
    font-size: 14px;
    line-height: 1.6;
    color: var(--benefits-card-text-color, rgba(255, 255, 255, 0.5));
    margin: 0;
    transition: color 0.3s ease;
}

.feat-benefits-card:hover .feat-benefits-card-desc {
    color: var(--benefits-card-hover-text, rgba(255, 255, 255, 0.9));
}


/* Section Headers */
.feat-section-header {
    text-align: center;
    margin-bottom: 60px;
}

.feat-section-badge {
    display: inline-block;
    padding: 6px 16px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--theme-color, #667eea);
    margin-bottom: 16px;
}

.feat-section-badge-light {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    color: #fff;
}

.feat-section-title {
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 300;
    color: #111827;
    margin: 0 0 16px;
    line-height: 1.15;
    letter-spacing: -0.5px;
    max-width: 48ch;
    margin-left: auto;
    margin-right: auto;
}

.feat-section-subtitle {
    font-size: 16px;
    color: #6b7280;
    margin: 0;
    max-width: 60ch;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

.feat-section-header-light .feat-section-title {
    color: #fff;
}

.feat-section-header-light .feat-section-subtitle {
    color: rgba(255, 255, 255, 0.7);
}


/* How It Works Section - Dynamic Styling */
.feat-how-it-works {
    background: var(--hiw-bg-color, #f9fafb);
    padding: 100px 0;
}

.feat-how-it-works .feat-section-badge {
    background: var(--hiw-badge-bg, rgba(102, 126, 234, 0.1));
    color: var(--hiw-badge-text, #667eea);
    border-color: var(--hiw-badge-text, #667eea);
    border-width: 1px;
    border-style: solid;
    opacity: 0.8;
}

.feat-how-it-works .feat-section-title {
    color: var(--hiw-heading-color, #111827);
}

.feat-how-it-works .feat-section-subtitle {
    color: var(--hiw-subheading-color, #6b7280);
}

.feat-workflow-grid {
    display: flex;
    justify-content: center;
    gap: 24px;
    position: relative;
}

.feat-workflow-card {
    flex: 1;
    max-width: 280px;
    background: var(--hiw-card-bg, transparent);
    border: 1px solid var(--hiw-card-border, transparent);
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    position: relative;
    transition: all 0.3s ease;
}

.feat-workflow-card:hover {
    transform: translateY(-4px);
    border-color: var(--hiw-card-hover-border, #667eea);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.1);
}

.feat-workflow-card:hover .feat-workflow-step {
    transform: scale(1.1);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

.feat-workflow-step {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: var(--hiw-step-bg, linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%));
    color: var(--hiw-step-color, #fff);
    font-size: 18px;
    font-weight: 700;
    border-radius: 50%;
    margin-bottom: 24px;
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

.feat-workflow-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--hiw-title-color, #111827);
    margin: 0 0 12px;
}

.feat-workflow-desc {
    font-size: 14px;
    color: var(--hiw-desc-color, #6b7280);
    line-height: 1.6;
    margin: 0;
}

/* Feature Highlights Section - Dynamic Styling */
.feat-highlights-section {
    background: var(--hl-bg-color, linear-gradient(180deg, #f8fafc 0%, #fff 100%));
    padding: 100px 0;
}

.feat-highlights-section .feat-section-badge {
    background: var(--hl-badge-bg, rgba(102, 126, 234, 0.1));
    color: var(--hl-badge-text, #667eea);
    border-color: var(--hl-badge-text, #667eea);
    border-width: 1px;
    border-style: solid;
    opacity: 0.8;
}

.feat-highlights-section .feat-section-title {
    color: var(--hl-heading-color, #111827);
}

.feat-highlights-section .feat-section-subtitle {
    color: var(--hl-subheading-color, #6b7280);
}

.feat-highlights-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

.feat-highlight-card {
    background: var(--hl-card-bg, #fff);
    border: 1px solid var(--hl-card-border, #e5e7eb);
    border-radius: 20px;
    padding: 32px;
    transition: all 0.3s ease;
}

.feat-highlight-card:hover {
    border-color: var(--hl-card-hover-border, var(--theme-color, #667eea));
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
    transform: translateY(-4px);
}

.feat-highlight-icon {
    width: 56px;
    height: 56px;
    background: var(--hl-icon-bg, linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%));
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.feat-highlight-card:hover .feat-highlight-icon {
    background: linear-gradient(135deg, var(--hl-card-hover-border, var(--theme-color, #667eea)) 0%, #764ba2 100%);
}

.feat-highlight-icon svg {
    width: 28px;
    height: 28px;
    stroke: var(--hl-icon-color, var(--theme-color, #667eea));
    transition: stroke 0.3s ease;
}

.feat-highlight-card:hover .feat-highlight-icon svg {
    stroke: #fff;
}

.feat-highlight-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--hl-title-color, #111827);
    margin: 0 0 12px;
}

.feat-highlight-desc {
    font-size: 14px;
    color: var(--hl-desc-color, #6b7280);
    line-height: 1.6;
    margin: 0;
}



/* Screenshots Gallery Section */
.feat-screenshots-section {
    background: #fff;
    padding: 100px 0;
}

.feat-screenshots-carousel {
    position: relative;
    max-width: 1000px;
    margin: 0 auto;
}

.feat-screenshots-track {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
    background: #f1f5f9;
    /* Fixed 4:7 aspect ratio (height:width) = 4/7 = 57.14% */
    aspect-ratio: 7 / 4;
}

.feat-screenshot-item {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.feat-screenshot-item.active {
    display: block;
}

.feat-screenshot-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top;
    display: block;
}

.feat-screenshots-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-top: 24px;
}

.feat-screenshot-prev,
.feat-screenshot-next {
    width: 48px;
    height: 48px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.feat-screenshot-prev:hover,
.feat-screenshot-next:hover {
    background: var(--theme-color, #667eea);
    border-color: var(--theme-color, #667eea);
}

.feat-screenshot-prev svg,
.feat-screenshot-next svg {
    width: 20px;
    height: 20px;
    stroke: #374151;
    transition: stroke 0.3s ease;
}

.feat-screenshot-prev:hover svg,
.feat-screenshot-next:hover svg {
    stroke: #fff;
}

.feat-screenshots-dots {
    display: flex;
    gap: 8px;
}

.feat-screenshot-dot {
    width: 10px;
    height: 10px;
    background: #d1d5db;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
}

.feat-screenshot-dot.active,
.feat-screenshot-dot:hover {
    background: var(--theme-color, #667eea);
    transform: scale(1.2);
}


/* Use Cases Section */
.feat-usecases-section {
    background: var(--uc-bg-color, linear-gradient(135deg, #0f172a 0%, #1e293b 100%));
    padding: 100px 0;
    position: relative;
    overflow: hidden;
}

.feat-usecases-section .feat-section-badge-light {
    background: var(--uc-badge-bg, rgba(255, 255, 255, 0.1));
    color: var(--uc-badge-text, #fff);
}

.feat-usecases-section .feat-section-title-light {
    color: var(--uc-heading-color, #fff);
}

.feat-usecases-section .feat-section-subtitle-light {
    color: var(--uc-subheading-color, rgba(255, 255, 255, 0.7));
}

.feat-section-header-light {
    text-align: center;
    margin-bottom: 60px;
}

.feat-usecases-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    max-width: 1200px;
    margin: 0 auto;
}

.feat-usecase-card {
    position: relative;
    border-radius: var(--uc-card-radius, 20px);
    overflow: hidden;
    height: 320px;
    cursor: pointer;
    background: var(--uc-card-bg, #1e293b);
    border: 1px solid var(--uc-card-border, transparent);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.feat-usecase-card:hover {
    border-color: var(--uc-card-hover-border, var(--theme-color, #667eea));
}

.feat-usecase-image {
    position: absolute;
    inset: 0;
}

.feat-usecase-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.feat-usecase-card:hover .feat-usecase-image img {
    transform: scale(1.1);
}

.feat-usecase-overlay {
    position: absolute;
    inset: 0;
    background: var(--uc-overlay, linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.8) 100%));
}

.feat-usecase-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 24px;
    z-index: 1;
}

.feat-usecase-title {
    font-size: 20px;
    font-weight: 600;
    color: var(--uc-title-color, #fff);
    margin: 0 0 8px;
}

.feat-usecase-desc {
    font-size: 14px;
    color: var(--uc-desc-color, rgba(255, 255, 255, 0.8));
    line-height: 1.5;
    margin: 0;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
}

.feat-usecase-card:hover .feat-usecase-desc {
    opacity: 1;
    transform: translateY(0);
}


/* Related Solutions Section */
.feat-related-section {
    background: #f8fafc;
    padding: 100px 0;
}

.feat-related-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

.feat-related-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 32px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: block;
}

.feat-related-card:hover {
    border-color: var(--theme-color, #667eea);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    transform: translateY(-4px);
}

.feat-related-icon {
    width: 64px;
    height: 64px;
    background: #f8fafc;
    border-radius: 16px;
    padding: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.feat-related-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.feat-related-icon-default {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
}

.feat-related-icon-default svg {
    width: 32px;
    height: 32px;
    stroke: var(--theme-color, #667eea);
}

.feat-related-title {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 12px;
}

.feat-related-desc {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.6;
    margin: 0 0 16px;
}

.feat-related-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: var(--theme-color, #667eea);
    transition: gap 0.3s ease;
}

.feat-related-card:hover .feat-related-link {
    gap: 10px;
}

.feat-related-link svg {
    width: 18px;
    height: 18px;
}


/* FAQs Section - Professional Design with Dark/Light Theme Support */
.feat-faqs-section {
    position: relative;
    padding: 120px 0;
    overflow: hidden;
}

/* Dark Theme */
.feat-faqs-dark {
    background: linear-gradient(135deg, #0a1628 0%, #1a2332 100%);
    color: #ffffff;
}

/* Light Theme (Default) */
.feat-faqs-light {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    color: #1e293b;
}

/* Background Effects */
.feat-faqs-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
    overflow: hidden;
}

.feat-faqs-gradient {
    position: absolute;
    top: -50%;
    right: -20%;
    width: 80%;
    height: 100%;
}

.feat-faqs-dark .feat-faqs-gradient {
    background: radial-gradient(ellipse at center, rgba(102, 126, 234, 0.15) 0%, transparent 70%);
}

.feat-faqs-light .feat-faqs-gradient {
    background: radial-gradient(ellipse at center, rgba(102, 126, 234, 0.08) 0%, transparent 70%);
}

.feat-faqs-pattern {
    position: absolute;
    inset: 0;
    background-size: 32px 32px;
}

.feat-faqs-dark .feat-faqs-pattern {
    background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.03) 1px, transparent 0);
}

.feat-faqs-light .feat-faqs-pattern {
    background-image: radial-gradient(circle at 1px 1px, rgba(0, 0, 0, 0.03) 1px, transparent 0);
}

/* Floating Orbs */
.feat-faqs-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
}

.feat-faqs-dark .feat-faqs-orb-1 {
    width: 500px;
    height: 500px;
    background: var(--theme-color, #667eea);
    top: -200px;
    right: -150px;
    opacity: 0.12;
}

.feat-faqs-dark .feat-faqs-orb-2 {
    width: 350px;
    height: 350px;
    background: #8b5cf6;
    bottom: -150px;
    left: -100px;
    opacity: 0.08;
}

.feat-faqs-light .feat-faqs-orb-1 {
    width: 500px;
    height: 500px;
    background: var(--theme-color, #667eea);
    top: -200px;
    right: -150px;
    opacity: 0.06;
}

.feat-faqs-light .feat-faqs-orb-2 {
    width: 350px;
    height: 350px;
    background: #8b5cf6;
    bottom: -150px;
    left: -100px;
    opacity: 0.04;
}

.feat-faqs-layout {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 80px;
    align-items: start;
    position: relative;
    z-index: 1;
}

/* Sidebar */
.feat-faqs-sidebar {
    position: sticky;
    top: 120px;
}

.feat-faqs-sidebar-content {
    position: relative;
    z-index: 2;
}

.feat-faqs-title {
    font-size: clamp(2.5rem, 5vw, 3.5rem);
    font-weight: 400;
    line-height: 1.2;
    margin: 0 0 20px;
    letter-spacing: -0.02em;
}

.feat-faqs-dark .feat-faqs-title {
    color: #ffffff;
}

.feat-faqs-light .feat-faqs-title {
    color: #0f172a;
}

.feat-faqs-subtitle {
    font-size: 16px;
    line-height: 1.7;
    margin: 0 0 32px;
}

.feat-faqs-dark .feat-faqs-subtitle {
    color: rgba(255, 255, 255, 0.6);
}

.feat-faqs-light .feat-faqs-subtitle {
    color: #64748b;
}

.feat-faqs-contact-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 24px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.feat-faqs-dark .feat-faqs-contact-btn {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
}

.feat-faqs-dark .feat-faqs-contact-btn:hover {
    background: var(--theme-color, #667eea);
    border-color: var(--theme-color, #667eea);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.35);
}

.feat-faqs-light .feat-faqs-contact-btn {
    background: #fff;
    color: #0f172a;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.feat-faqs-light .feat-faqs-contact-btn:hover {
    background: var(--theme-color, #667eea);
    color: #fff;
    border-color: var(--theme-color, #667eea);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);
}

.feat-faqs-contact-btn svg {
    width: 18px;
    height: 18px;
}

/* Decoration Circles */
.feat-faqs-decoration {
    position: absolute;
    bottom: -60px;
    left: -40px;
    width: 200px;
    height: 200px;
}

.feat-faqs-decoration-circle {
    position: absolute;
    border-radius: 50%;
    border: 1px solid;
}

.feat-faqs-dark .feat-faqs-decoration-circle {
    border-color: rgba(255, 255, 255, 0.1);
}

.feat-faqs-light .feat-faqs-decoration-circle {
    border-color: rgba(102, 126, 234, 0.15);
}

.feat-faqs-decoration-circle-1 {
    width: 200px;
    height: 200px;
    animation: faqPulse 4s ease-in-out infinite;
}

.feat-faqs-decoration-circle-2 {
    width: 150px;
    height: 150px;
    top: 25px;
    left: 25px;
    animation: faqPulse 4s ease-in-out infinite 0.5s;
}

.feat-faqs-decoration-circle-3 {
    width: 100px;
    height: 100px;
    top: 50px;
    left: 50px;
    animation: faqPulse 4s ease-in-out infinite 1s;
}

@keyframes faqPulse {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.05); }
}

/* FAQ Content */
.feat-faqs-content {
    position: relative;
}

.feat-faqs-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.feat-faq-item {
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
}

/* Dark Theme - FAQ Items */
.feat-faqs-dark .feat-faq-item {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.feat-faqs-dark .feat-faq-item:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.feat-faqs-dark .feat-faq-item.active {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--theme-color, #667eea);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.2);
}

/* Light Theme - FAQ Items */
.feat-faqs-light .feat-faq-item {
    background: #fff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.feat-faqs-light .feat-faq-item:hover {
    border-color: rgba(102, 126, 234, 0.4);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
}

.feat-faqs-light .feat-faq-item.active {
    border-color: var(--theme-color, #667eea);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.12);
}

.feat-faq-question {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px 28px;
    background: none;
    border: none;
    font-size: 16px;
    font-weight: 600;
    text-align: left;
    cursor: pointer;
    transition: all 0.3s ease;
}

.feat-faqs-dark .feat-faq-question {
    color: #ffffff;
}

.feat-faqs-dark .feat-faq-question:hover {
    background: rgba(255, 255, 255, 0.05);
}

.feat-faqs-light .feat-faq-question {
    color: #0f172a;
}

.feat-faqs-light .feat-faq-question:hover {
    background: #f8fafc;
}

.feat-faq-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    font-size: 13px;
    font-weight: 700;
    border-radius: 10px;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.feat-faqs-dark .feat-faq-number {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.6);
}

.feat-faqs-light .feat-faq-number {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #64748b;
}

.feat-faq-item.active .feat-faq-number {
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    color: #fff;
}

.feat-faq-question-text {
    flex: 1;
    line-height: 1.5;
}

.feat-faq-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.feat-faqs-dark .feat-faq-toggle {
    background: rgba(255, 255, 255, 0.1);
}

.feat-faqs-dark .feat-faq-toggle svg {
    stroke: rgba(255, 255, 255, 0.6);
}

.feat-faqs-light .feat-faq-toggle {
    background: #f1f5f9;
}

.feat-faqs-light .feat-faq-toggle svg {
    stroke: #64748b;
}

.feat-faq-toggle svg {
    width: 16px;
    height: 16px;
    transition: all 0.3s ease;
}

.feat-faq-icon-minus {
    display: none;
}

.feat-faq-item.active .feat-faq-toggle {
    background: var(--theme-color, #667eea);
}

.feat-faq-item.active .feat-faq-toggle svg {
    stroke: #fff;
}

.feat-faq-item.active .feat-faq-icon-plus {
    display: none;
}

.feat-faq-item.active .feat-faq-icon-minus {
    display: block;
}

.feat-faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, padding 0.3s ease;
}

.feat-faq-item.active .feat-faq-answer {
    max-height: 500px;
}

.feat-faq-answer-inner {
    padding: 0 28px 28px 80px;
}

.feat-faq-answer p {
    font-size: 15px;
    line-height: 1.8;
    margin: 0;
}

.feat-faqs-dark .feat-faq-answer p {
    color: rgba(255, 255, 255, 0.7);
}

.feat-faqs-light .feat-faq-answer p {
    color: #64748b;
}

/* FAQ Footer */
.feat-faqs-footer {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-top: 40px;
    padding: 28px 32px;
    border-radius: 20px;
    transition: all 0.3s ease;
}

.feat-faqs-dark .feat-faqs-footer {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.feat-faqs-light .feat-faqs-footer {
    background: #fff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}

.feat-faqs-footer-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    flex-shrink: 0;
}

.feat-faqs-dark .feat-faqs-footer-icon {
    background: rgba(102, 126, 234, 0.2);
}

.feat-faqs-light .feat-faqs-footer-icon {
    background: rgba(102, 126, 234, 0.1);
}

.feat-faqs-footer-icon svg {
    width: 24px;
    height: 24px;
    stroke: var(--theme-color, #667eea);
}

.feat-faqs-footer-text {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.feat-faqs-dark .feat-faqs-footer-text strong {
    color: #fff;
    font-size: 15px;
}

.feat-faqs-dark .feat-faqs-footer-text span {
    color: rgba(255, 255, 255, 0.6);
    font-size: 14px;
}

.feat-faqs-light .feat-faqs-footer-text strong {
    color: #0f172a;
    font-size: 15px;
}

.feat-faqs-light .feat-faqs-footer-text span {
    color: #64748b;
    font-size: 14px;
}

.feat-faqs-footer-btn {
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 10px;
    transition: all 0.3s ease;
    background: var(--theme-color, #667eea);
    color: #fff;
}

.feat-faqs-footer-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.35);
}

/* Responsive Styles */
@media (max-width: 1200px) {
    .feat-workflow-grid {
        flex-wrap: wrap;
        gap: 40px;
    }
    
    .feat-workflow-card {
        flex: 0 0 calc(50% - 20px);
        max-width: calc(50% - 20px);
    }
    
    .feat-usecases-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .feat-hero-floating {
        display: none;
    }
}

@media (max-width: 992px) {
    .feat-benefits-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .feat-benefits-text {
        position: static;
    }
    
    .feat-benefits-subheading {
        max-width: 100%;
        margin: 0 auto;
    }
    
    .feat-highlights-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .feat-related-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .feat-faqs-layout {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .feat-faqs-sidebar {
        position: static;
        text-align: center;
        text-align: center;
    }
    
    .feat-faqs-decoration {
        display: none;
    }
}

@media (max-width: 768px) {
    .feat-hero {
        height: auto;
        min-height: 90vh;
        padding: 0;
    }
    
    .feat-hero > .container {
        padding-top: 80px;
        padding-bottom: 24px;
    }
    
    .feat-hero-content {
        padding: 0 8px;
    }
    
    .feat-hero-title {
        font-size: 32px;
        margin-bottom: 16px;
    }
    
    .feat-hero-subtitle {
        font-size: 16px;
        margin-bottom: 24px;
    }
    
    .feat-hero-actions {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 32px;
    }
    
    .feat-btn-primary,
    .feat-btn-text {
        padding: 14px 24px;
        width: 100%;
        justify-content: center;
    }
    
    .feat-hero-stats {
        flex-wrap: wrap;
        gap: 24px;
    }
    
    .feat-hero-stat-value {
        font-size: 22px;
    }
    
    .feat-hero-circle-1,
    .feat-hero-circle-2 {
        display: none;
    }
    
    .feat-breadcrumb {
        margin-bottom: 24px;
    }
    
    .feat-hero .client-logo-marquee {
        height: 80px;
        min-height: 80px;
        padding: 15px 0;
    }
    
    .feat-hero .marquee-logo {
        max-height: 30px;
        max-width: 85px;
        filter: none !important;
        opacity: 1 !important;
    }
    
    .feat-hero .marquee-content {
        gap: 32px;
    }
    
    .feat-hero .marquee-logo-link {
        opacity: 1;
        filter: none;
    }
    
    .feat-hero-card-inner {
        padding: 32px 24px;
    }
    
    .feat-hero-icon-box {
        width: 72px;
        height: 72px;
    }
    
    .feat-hero-stats {
        flex-direction: column;
        gap: 16px;
    }
    
    .feat-hero-stat-divider {
        width: 40px;
        height: 1px;
    }
    
    .feat-hero-badge {
        font-size: 11px;
    }
    
    .feat-benefits-cards {
        grid-template-columns: 1fr;
    }
    
    .feat-workflow-grid {
        flex-direction: column;
        align-items: center;
        gap: 32px;
    }
    
    .feat-workflow-card {
        flex: none;
        max-width: 100%;
        width: 100%;
    }
    
    .feat-highlights-grid {
        grid-template-columns: 1fr;
    }
    
    .feat-usecases-grid {
        grid-template-columns: 1fr;
    }
    
    .feat-usecase-card {
        height: 280px;
    }
    
    .feat-usecase-desc {
        opacity: 1;
        transform: translateY(0);
    }
    
    .feat-related-grid {
        grid-template-columns: 1fr;
    }
    
    .feat-faq-answer-inner {
        padding-left: 24px;
    }
    
    .feat-faqs-footer {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }
    
    .feat-faqs-footer-text {
        align-items: center;
    }
}

@media (max-width: 480px) {
    .feat-hero-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .feat-btn-primary,
    .feat-btn-secondary {
        width: 100%;
        justify-content: center;
    }
}
</style>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Accordion
    const faqItems = document.querySelectorAll('.feat-faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.feat-faq-question');
        
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close all other items
            faqItems.forEach(otherItem => {
                otherItem.classList.remove('active');
                otherItem.querySelector('.feat-faq-question').setAttribute('aria-expanded', 'false');
            });
            
            // Toggle current item
            if (!isActive) {
                item.classList.add('active');
                question.setAttribute('aria-expanded', 'true');
            }
        });
    });
    
    // Screenshots Carousel
    const screenshotItems = document.querySelectorAll('.feat-screenshot-item');
    const screenshotDots = document.querySelectorAll('.feat-screenshot-dot');
    const prevBtn = document.querySelector('.feat-screenshot-prev');
    const nextBtn = document.querySelector('.feat-screenshot-next');
    let currentIndex = 0;
    
    function showScreenshot(index) {
        if (screenshotItems.length === 0) return;
        
        // Wrap around
        if (index < 0) index = screenshotItems.length - 1;
        if (index >= screenshotItems.length) index = 0;
        
        currentIndex = index;
        
        // Update items
        screenshotItems.forEach((item, i) => {
            item.classList.toggle('active', i === index);
        });
        
        // Update dots
        screenshotDots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => showScreenshot(currentIndex - 1));
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => showScreenshot(currentIndex + 1));
    }
    
    screenshotDots.forEach((dot, index) => {
        dot.addEventListener('click', () => showScreenshot(index));
    });
    
    // Auto-advance screenshots every 5 seconds
    if (screenshotItems.length > 1) {
        setInterval(() => {
            showScreenshot(currentIndex + 1);
        }, 5000);
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php include_footer(); ?>
