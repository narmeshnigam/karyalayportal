<?php

/**
 * SellerPortal System
 * Home Page
 */

// Load bootstrap (includes autoloader, env loading, and dual-environment credential resolution)
require_once __DIR__ . '/../config/bootstrap.php';

// Load configuration
$config = require __DIR__ . '/../config/app.php';

// Set error reporting based on environment
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Load authentication helpers
require_once __DIR__ . '/../includes/auth_helpers.php';

// Include template helpers
require_once __DIR__ . '/../includes/template_helpers.php';

// Load hero slides
use Karyalay\Models\HeroSlide;
use Karyalay\Models\WhyChooseCard;
use Karyalay\Models\Solution;
use Karyalay\Models\ClientLogo;

$heroSlideModel = new HeroSlide();
$heroSlides = $heroSlideModel->getPublishedSlides();

// Load client logos for marquee
$clientLogos = [];
$clientLogosError = null;
try {
    $clientLogoModel = new ClientLogo();
    $clientLogos = $clientLogoModel->getPublishedLogos();
} catch (Exception $e) {
    // Table might not exist yet - use sample logos for demo
    $clientLogosError = $e->getMessage();
    error_log("Client logos not available: " . $e->getMessage());
    
    // For demo purposes, show sample logos when database isn't available
    if (strpos($e->getMessage(), 'client_logos') !== false || strpos($e->getMessage(), 'connection') !== false) {
        $clientLogos = [
            [
                'client_name' => 'Google',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg',
                'website_url' => 'https://google.com'
            ],
            [
                'client_name' => 'Microsoft',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg',
                'website_url' => 'https://microsoft.com'
            ],
            [
                'client_name' => 'Apple',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg',
                'website_url' => 'https://apple.com'
            ],
            [
                'client_name' => 'Amazon',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg',
                'website_url' => 'https://amazon.com'
            ],
            [
                'client_name' => 'Netflix',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg',
                'website_url' => 'https://netflix.com'
            ],
            [
                'client_name' => 'Tesla',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/b/bb/Tesla_T_symbol.svg',
                'website_url' => 'https://tesla.com'
            ]
        ];
    }
}

// Load why choose cards
$whyChooseModel = new WhyChooseCard();
$whyChooseCards = $whyChooseModel->getPublishedCards(6);

// Load featured solutions
$solutionModel = new Solution();
$featuredSolutions = $solutionModel->getFeaturedSolutions(6);

// Load Powerful Solutions UI settings
use Karyalay\Models\Setting;
$settingModel = new Setting();
$solutionsUISettings = $settingModel->getMultiple([
    'solutions_section_title', 'solutions_section_subtitle',
    'solutions_section_bg_start', 'solutions_section_bg_mid', 'solutions_section_bg_end',
    'solutions_title_color', 'solutions_subtitle_color',
    'solutions_nav_bg', 'solutions_nav_border', 'solutions_nav_link_color',
    'solutions_nav_link_hover', 'solutions_nav_link_active', 'solutions_nav_active_border',
    'solutions_card_bg', 'solutions_card_border', 'solutions_badge_bg', 'solutions_badge_text',
    'solutions_heading_color', 'solutions_description_color', 'solutions_feature_color',
    'solutions_cta_color', 'solutions_cta_hover',
    'solutions_more_btn_bg_start', 'solutions_more_btn_bg_end', 'solutions_more_btn_text',
    'solutions_more_btn_hover_start', 'solutions_more_btn_hover_end'
]);

// Load Homepage CTA Banner settings
$ctaBannerSettings = $settingModel->getMultiple([
    'homepage_cta_banner_enabled', 'homepage_cta_banner_image_url',
    'homepage_cta_banner_overlay_color', 'homepage_cta_banner_overlay_intensity',
    'homepage_cta_banner_heading1', 'homepage_cta_banner_heading2', 'homepage_cta_banner_heading_color',
    'homepage_cta_banner_button_text', 'homepage_cta_banner_button_link',
    'homepage_cta_banner_button_bg_color', 'homepage_cta_banner_button_text_color'
]);

// Set CTA Banner defaults
$homepageCtaBanner = [
    'enabled' => ($ctaBannerSettings['homepage_cta_banner_enabled'] ?? '1') === '1',
    'image_url' => $ctaBannerSettings['homepage_cta_banner_image_url'] ?? 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1400&h=500&fit=crop',
    'overlay_color' => $ctaBannerSettings['homepage_cta_banner_overlay_color'] ?? 'rgba(0,0,0,0.5)',
    'overlay_intensity' => $ctaBannerSettings['homepage_cta_banner_overlay_intensity'] ?? '0.5',
    'heading1' => $ctaBannerSettings['homepage_cta_banner_heading1'] ?? 'Ready to transform your business?',
    'heading2' => $ctaBannerSettings['homepage_cta_banner_heading2'] ?? 'Get started with our powerful solutions today!',
    'heading_color' => $ctaBannerSettings['homepage_cta_banner_heading_color'] ?? '#FFFFFF',
    'button_text' => $ctaBannerSettings['homepage_cta_banner_button_text'] ?? 'Get Started Now',
    'button_link' => $ctaBannerSettings['homepage_cta_banner_button_link'] ?? '#contact-form',
    'button_bg_color' => $ctaBannerSettings['homepage_cta_banner_button_bg_color'] ?? '#FFFFFF',
    'button_text_color' => $ctaBannerSettings['homepage_cta_banner_button_text_color'] ?? '#2563eb'
];

// Load Why Choose section UI settings
$whyChooseUISettings = $settingModel->getMultiple([
    'why_choose_section_title', 'why_choose_section_subtitle',
    'why_choose_section_bg', 'why_choose_title_color', 'why_choose_subtitle_color',
    'why_choose_card_bg', 'why_choose_card_title_color', 'why_choose_card_desc_color'
]);

// Set Why Choose UI defaults
$whyChooseUI = [
    'title' => $whyChooseUISettings['why_choose_section_title'] ?? 'Why Choose Karyalay?',
    'subtitle' => $whyChooseUISettings['why_choose_section_subtitle'] ?? 'Everything you need to manage your business efficiently in one powerful platform',
    'section_bg' => $whyChooseUISettings['why_choose_section_bg'] ?? '#ffffff',
    'title_color' => $whyChooseUISettings['why_choose_title_color'] ?? '#111827',
    'subtitle_color' => $whyChooseUISettings['why_choose_subtitle_color'] ?? '#6b7280',
    'card_bg' => $whyChooseUISettings['why_choose_card_bg'] ?? '#ffffff',
    'card_title_color' => $whyChooseUISettings['why_choose_card_title_color'] ?? '#111827',
    'card_desc_color' => $whyChooseUISettings['why_choose_card_desc_color'] ?? '#6b7280'
];

// Load Success Stories section UI settings
$successStoriesUISettings = $settingModel->getMultiple([
    'success_stories_enabled', 'success_stories_section_title', 'success_stories_section_subtitle',
    'success_stories_bg_gradient_start', 'success_stories_bg_gradient_end',
    'success_stories_title_color', 'success_stories_subtitle_color',
    'success_stories_card_overlay', 'success_stories_card_title', 'success_stories_card_industry',
    'success_stories_card_desc', 'success_stories_card_badge_bg', 'success_stories_card_badge_text',
    'success_stories_card_btn_bg', 'success_stories_card_btn_text',
    'success_stories_view_all_bg', 'success_stories_view_all_text',
    'success_stories_view_all_icon_bg', 'success_stories_view_all_icon_hover_bg'
]);

// Set Success Stories UI defaults
$successStoriesUI = [
    'enabled' => ($successStoriesUISettings['success_stories_enabled'] ?? '1') === '1',
    'title' => $successStoriesUISettings['success_stories_section_title'] ?? 'Success Stories',
    'subtitle' => $successStoriesUISettings['success_stories_section_subtitle'] ?? 'See how businesses like yours are achieving remarkable results with our solutions',
    'bg_gradient_start' => $successStoriesUISettings['success_stories_bg_gradient_start'] ?? '#1a1a2e',
    'bg_gradient_end' => $successStoriesUISettings['success_stories_bg_gradient_end'] ?? '#16213e',
    'title_color' => $successStoriesUISettings['success_stories_title_color'] ?? '#ffffff',
    'subtitle_color' => $successStoriesUISettings['success_stories_subtitle_color'] ?? '#b3b3b3',
    'card_overlay' => $successStoriesUISettings['success_stories_card_overlay'] ?? '#000000',
    'card_title' => $successStoriesUISettings['success_stories_card_title'] ?? '#ffffff',
    'card_industry' => $successStoriesUISettings['success_stories_card_industry'] ?? '#cccccc',
    'card_desc' => $successStoriesUISettings['success_stories_card_desc'] ?? '#e6e6e6',
    'card_badge_bg' => $successStoriesUISettings['success_stories_card_badge_bg'] ?? '#333333',
    'card_badge_text' => $successStoriesUISettings['success_stories_card_badge_text'] ?? '#ffffff',
    'card_btn_bg' => $successStoriesUISettings['success_stories_card_btn_bg'] ?? '#262626',
    'card_btn_text' => $successStoriesUISettings['success_stories_card_btn_text'] ?? '#ffffff',
    'view_all_bg' => $successStoriesUISettings['success_stories_view_all_bg'] ?? '#0d0d0d',
    'view_all_text' => $successStoriesUISettings['success_stories_view_all_text'] ?? '#ffffff',
    'view_all_icon_bg' => $successStoriesUISettings['success_stories_view_all_icon_bg'] ?? '#1a1a1a',
    'view_all_icon_hover_bg' => $successStoriesUISettings['success_stories_view_all_icon_hover_bg'] ?? '#333333'
];

// Load Homepage Testimonials settings
$testimonialsSettings = $settingModel->getMultiple([
    'homepage_testimonials_enabled', 'homepage_testimonials_theme',
    'homepage_testimonials_heading', 'homepage_testimonials_subheading', 'homepage_testimonials_accent_color'
]);

// Set Testimonials UI defaults
$testimonialsUI = [
    'enabled' => ($testimonialsSettings['homepage_testimonials_enabled'] ?? '1') === '1',
    'theme' => $testimonialsSettings['homepage_testimonials_theme'] ?? 'light',
    'heading' => $testimonialsSettings['homepage_testimonials_heading'] ?? 'What Our Customers Say',
    'subheading' => $testimonialsSettings['homepage_testimonials_subheading'] ?? 'Don\'t just take our word for it - hear from businesses that trust us',
    'accent_color' => $testimonialsSettings['homepage_testimonials_accent_color'] ?? '#10b981'
];

// Load Homepage FAQ settings
$faqSettings = $settingModel->getMultiple([
    'homepage_faq_enabled', 'homepage_faq_theme', 'homepage_faq_heading',
    'homepage_faq_subheading', 'homepage_faq_items'
]);

// Set FAQ defaults
$homepageFaq = [
    'enabled' => ($faqSettings['homepage_faq_enabled'] ?? '1') === '1',
    'theme' => $faqSettings['homepage_faq_theme'] ?? 'light',
    'heading' => $faqSettings['homepage_faq_heading'] ?? 'Frequently Asked Questions',
    'subheading' => $faqSettings['homepage_faq_subheading'] ?? 'Everything you need to know about our platform. Can\'t find what you\'re looking for? Feel free to contact us.',
    'items' => []
];

// Parse FAQ items JSON
$faqItemsJson = $faqSettings['homepage_faq_items'] ?? '[]';
try {
    $homepageFaq['items'] = json_decode($faqItemsJson, true) ?: [];
} catch (Exception $e) {
    $homepageFaq['items'] = [];
}

// Load Blog section UI settings
$blogUISettings = $settingModel->getMultiple([
    'blog_section_enabled', 'blog_section_title', 'blog_section_subtitle', 'blog_section_theme',
    'blog_section_bg_start', 'blog_section_bg_end',
    'blog_title_color', 'blog_subtitle_color',
    'blog_card_bg', 'blog_card_border', 'blog_card_title_color', 'blog_card_excerpt_color',
    'blog_card_date_color', 'blog_card_tag_bg', 'blog_card_tag_text',
    'blog_card_link_color', 'blog_card_link_hover',
    'blog_view_all_bg', 'blog_view_all_border', 'blog_view_all_text', 'blog_view_all_icon_bg'
]);

// Set Blog UI defaults
$blogUI = [
    'enabled' => ($blogUISettings['blog_section_enabled'] ?? '1') === '1',
    'title' => $blogUISettings['blog_section_title'] ?? 'Latest from Our Blog',
    'subtitle' => $blogUISettings['blog_section_subtitle'] ?? 'Stay updated with the latest insights, tips, and news from our team',
    'theme' => $blogUISettings['blog_section_theme'] ?? 'light',
    'bg_start' => $blogUISettings['blog_section_bg_start'] ?? '#f8fafc',
    'bg_end' => $blogUISettings['blog_section_bg_end'] ?? '#f1f5f9',
    'title_color' => $blogUISettings['blog_title_color'] ?? '#0f172a',
    'subtitle_color' => $blogUISettings['blog_subtitle_color'] ?? '#64748b',
    'card_bg' => $blogUISettings['blog_card_bg'] ?? '#ffffff',
    'card_border' => $blogUISettings['blog_card_border'] ?? '#e2e8f0',
    'card_title_color' => $blogUISettings['blog_card_title_color'] ?? '#0f172a',
    'card_excerpt_color' => $blogUISettings['blog_card_excerpt_color'] ?? '#64748b',
    'card_date_color' => $blogUISettings['blog_card_date_color'] ?? '#a855f7',
    'card_tag_bg' => $blogUISettings['blog_card_tag_bg'] ?? '#f3e8ff',
    'card_tag_text' => $blogUISettings['blog_card_tag_text'] ?? '#9333ea',
    'card_link_color' => $blogUISettings['blog_card_link_color'] ?? '#a855f7',
    'card_link_hover' => $blogUISettings['blog_card_link_hover'] ?? '#9333ea',
    'view_all_bg' => $blogUISettings['blog_view_all_bg'] ?? '#faf5ff',
    'view_all_border' => $blogUISettings['blog_view_all_border'] ?? '#a855f7',
    'view_all_text' => $blogUISettings['blog_view_all_text'] ?? '#0f172a',
    'view_all_icon_bg' => $blogUISettings['blog_view_all_icon_bg'] ?? '#f3e8ff'
];

// Load Homepage CTA Form settings
$ctaFormSettings = $settingModel->getMultiple([
    'homepage_cta_form_enabled', 'homepage_cta_form_theme',
    'homepage_cta_form_title', 'homepage_cta_form_subtitle', 'homepage_cta_form_badge_text',
    'homepage_cta_form_header', 'homepage_cta_form_header_subtitle', 'homepage_cta_form_submit_text',
    'homepage_cta_form_privacy_text', 'homepage_cta_form_accent_color',
    'homepage_cta_form_btn_gradient_start', 'homepage_cta_form_btn_gradient_end',
    'homepage_cta_form_badge_bg', 'homepage_cta_form_icon_bg'
]);

// Set CTA Form UI defaults
$ctaFormUI = [
    'enabled' => ($ctaFormSettings['homepage_cta_form_enabled'] ?? '1') === '1',
    'theme' => $ctaFormSettings['homepage_cta_form_theme'] ?? 'dark',
    'title' => $ctaFormSettings['homepage_cta_form_title'] ?? 'Ready to Transform Your Business?',
    'subtitle' => $ctaFormSettings['homepage_cta_form_subtitle'] ?? 'Get in touch with us today and discover how we can streamline your operations',
    'badge_text' => $ctaFormSettings['homepage_cta_form_badge_text'] ?? 'Trusted by 500+ Businesses',
    'form_header' => $ctaFormSettings['homepage_cta_form_header'] ?? 'Get Started Today',
    'form_header_subtitle' => $ctaFormSettings['homepage_cta_form_header_subtitle'] ?? 'Fill out the form and we\'ll get back to you shortly',
    'submit_text' => $ctaFormSettings['homepage_cta_form_submit_text'] ?? 'Send Message',
    'privacy_text' => $ctaFormSettings['homepage_cta_form_privacy_text'] ?? 'Your information is secure and will never be shared',
    'accent_color' => $ctaFormSettings['homepage_cta_form_accent_color'] ?? '#10b981',
    'btn_gradient_start' => $ctaFormSettings['homepage_cta_form_btn_gradient_start'] ?? '#10b981',
    'btn_gradient_end' => $ctaFormSettings['homepage_cta_form_btn_gradient_end'] ?? '#059669',
    'badge_bg' => $ctaFormSettings['homepage_cta_form_badge_bg'] ?? '#10b981',
    'icon_bg' => $ctaFormSettings['homepage_cta_form_icon_bg'] ?? '#ffffff'
];

// Set defaults for solutions UI
$solutionsUI = [
    'title' => $solutionsUISettings['solutions_section_title'] ?? 'Powerful Solutions',
    'subtitle' => $solutionsUISettings['solutions_section_subtitle'] ?? 'Explore our comprehensive suite of business management solutions designed to streamline your operations',
    'bg_start' => $solutionsUISettings['solutions_section_bg_start'] ?? '#f7fef9',
    'bg_mid' => $solutionsUISettings['solutions_section_bg_mid'] ?? '#f0fdf4',
    'bg_end' => $solutionsUISettings['solutions_section_bg_end'] ?? '#dcfce7',
    'title_color' => $solutionsUISettings['solutions_title_color'] ?? '#111827',
    'subtitle_color' => $solutionsUISettings['solutions_subtitle_color'] ?? '#6b7280',
    'nav_bg' => $solutionsUISettings['solutions_nav_bg'] ?? '#ffffff',
    'nav_border' => $solutionsUISettings['solutions_nav_border'] ?? '#e5e7eb',
    'nav_link_color' => $solutionsUISettings['solutions_nav_link_color'] ?? '#6b7280',
    'nav_link_hover' => $solutionsUISettings['solutions_nav_link_hover'] ?? '#111827',
    'nav_link_active' => $solutionsUISettings['solutions_nav_link_active'] ?? '#111827',
    'nav_active_border' => $solutionsUISettings['solutions_nav_active_border'] ?? '#10b981',
    'card_bg' => $solutionsUISettings['solutions_card_bg'] ?? '#ffffff',
    'card_border' => $solutionsUISettings['solutions_card_border'] ?? '#e5e7eb',
    'badge_bg' => $solutionsUISettings['solutions_badge_bg'] ?? '#ecfdf5',
    'badge_text' => $solutionsUISettings['solutions_badge_text'] ?? '#059669',
    'heading_color' => $solutionsUISettings['solutions_heading_color'] ?? '#111827',
    'description_color' => $solutionsUISettings['solutions_description_color'] ?? '#6b7280',
    'feature_color' => $solutionsUISettings['solutions_feature_color'] ?? '#374151',
    'cta_color' => $solutionsUISettings['solutions_cta_color'] ?? '#10b981',
    'cta_hover' => $solutionsUISettings['solutions_cta_hover'] ?? '#059669',
    'btn_bg_start' => $solutionsUISettings['solutions_more_btn_bg_start'] ?? '#10b981',
    'btn_bg_end' => $solutionsUISettings['solutions_more_btn_bg_end'] ?? '#059669',
    'btn_text' => $solutionsUISettings['solutions_more_btn_text'] ?? '#ffffff',
    'btn_hover_start' => $solutionsUISettings['solutions_more_btn_hover_start'] ?? '#059669',
    'btn_hover_end' => $solutionsUISettings['solutions_more_btn_hover_end'] ?? '#047857',
];

// Load testimonials
use Karyalay\Models\Testimonial;
$testimonialModel = new Testimonial();
$testimonials = $testimonialModel->getFeatured(6);

// Load featured case studies
use Karyalay\Models\CaseStudy;
$caseStudyModel = new CaseStudy();
$featuredCaseStudies = $caseStudyModel->getFeatured(3);

// Load featured blog posts
use Karyalay\Models\BlogPost;
$blogPostModel = new BlogPost();
$featuredBlogPosts = $blogPostModel->getFeatured(3);

// Set page variables
$page_title = 'Home';
$page_description = get_brand_name() . ' - ' . get_footer_company_description();

// Additional CSS for this page
$additional_css = [
    css_url('business-hub.css'),
    css_url('success-stories-gallery.css'),
    css_url('blog-gallery.css'),
    css_url('testimonials-showcase.css')
];

// Include header with additional CSS
include_header($page_title, $page_description, $additional_css);
?>

<!-- Hero Slider Section -->
<section class="hero-slider" aria-label="Hero Slider">
    <div class="hero-slider-container">
        <?php if (!empty($heroSlides)): ?>
            <?php foreach ($heroSlides as $index => $slide): 
                // Get colors with defaults
                $line1Color = $slide['highlight_line1_color'] ?? '#FFFFFF';
                $line2Color = $slide['highlight_line2_color'] ?? '#FFFFFF';
                $descColor = $slide['description_color'] ?? '#FFFFFF';
                $primaryBtnBg = $slide['primary_btn_bg_color'] ?? '#3B82F6';
                $primaryBtnText = $slide['primary_btn_text_color'] ?? '#FFFFFF';
                $secondaryBtnText = $slide['secondary_btn_text_color'] ?? '#FFFFFF';
                $secondaryBtnBorder = $slide['secondary_btn_border_color'] ?? '#FFFFFF';
                
                // Get mobile image (fallback to desktop if not set)
                $desktopImage = htmlspecialchars($slide['image_url']);
                $mobileImage = !empty($slide['mobile_image_url']) ? htmlspecialchars($slide['mobile_image_url']) : $desktopImage;
            ?>
                <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                     style="background-image: url('<?php echo $desktopImage; ?>')"
                     data-index="<?php echo $index; ?>"
                     data-desktop-image="<?php echo $desktopImage; ?>"
                     data-mobile-image="<?php echo $mobileImage; ?>">
                    <div class="hero-slide-content">
                        <div class="container">
                            <div class="hero-content-wrapper">
                                <div class="hero-text-content">
                                    <?php if (!empty($slide['highlight_line1']) || !empty($slide['highlight_line2'])): ?>
                                        <div class="hero-highlight-text">
                                            <?php if (!empty($slide['highlight_line1'])): ?>
                                                <h1 class="hero-highlight-line" style="color: <?php echo htmlspecialchars($line1Color); ?>">
                                                    <?php echo htmlspecialchars($slide['highlight_line1']); ?>
                                                </h1>
                                            <?php endif; ?>
                                            <?php if (!empty($slide['highlight_line2'])): ?>
                                                <h1 class="hero-highlight-line" style="color: <?php echo htmlspecialchars($line2Color); ?>">
                                                    <?php echo htmlspecialchars($slide['highlight_line2']); ?>
                                                </h1>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif (!empty($slide['title'])): ?>
                                        <h1 class="hero-title" style="color: <?php echo htmlspecialchars($line1Color); ?>">
                                            <?php echo htmlspecialchars($slide['title']); ?>
                                        </h1>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($slide['description'])): ?>
                                        <p class="hero-description" style="color: <?php echo htmlspecialchars($descColor); ?>">
                                            <?php echo htmlspecialchars($slide['description']); ?>
                                        </p>
                                    <?php elseif (!empty($slide['subtitle'])): ?>
                                        <p class="hero-description" style="color: <?php echo htmlspecialchars($descColor); ?>">
                                            <?php echo htmlspecialchars($slide['subtitle']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="hero-actions">
                                        <a href="<?php echo get_base_url(); ?>/register.php" 
                                           class="btn btn-hero-primary btn-lg"
                                           style="background-color: <?php echo htmlspecialchars($primaryBtnBg); ?>; color: <?php echo htmlspecialchars($primaryBtnText); ?>; border-color: <?php echo htmlspecialchars($primaryBtnBg); ?>;">
                                            Get Started
                                        </a>
                                        <?php if (!empty($slide['know_more_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($slide['know_more_url']); ?>" 
                                               class="btn btn-hero-secondary btn-lg"
                                               style="color: <?php echo htmlspecialchars($secondaryBtnText); ?>; border-color: <?php echo htmlspecialchars($secondaryBtnBorder); ?>;"
                                               target="_blank">
                                                Know More
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo get_base_url(); ?>/solutions.php" 
                                               class="btn btn-hero-secondary btn-lg"
                                               style="color: <?php echo htmlspecialchars($secondaryBtnText); ?>; border-color: <?php echo htmlspecialchars($secondaryBtnBorder); ?>;">
                                                Know More
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Default slide when no slides are configured -->
            <div class="hero-slide active" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="hero-slide-content">
                    <div class="container">
                        <div class="hero-content-wrapper">
                            <div class="hero-text-content">
                                <div class="hero-highlight-text">
                                    <h1 class="hero-highlight-line">Transform Your</h1>
                                    <h1 class="hero-highlight-line">Business Today</h1>
                                </div>
                                <p class="hero-description"><?php echo htmlspecialchars(get_footer_company_description()); ?></p>
                                <div class="hero-actions">
                                    <a href="<?php echo get_base_url(); ?>/register.php" class="btn btn-hero-primary btn-lg">Get Started</a>
                                    <a href="<?php echo get_base_url(); ?>/solutions.php" class="btn btn-hero-secondary btn-lg">Know More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($clientLogos)): ?>
        <!-- Client Logo Marquee (<?php echo count($clientLogos); ?> logos) -->
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
        <?php else: ?>
        <!-- Client logos not available: <?php echo $clientLogosError ? htmlspecialchars($clientLogosError) : 'No published logos found'; ?> -->
        <?php endif; ?>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slide');
    const MOBILE_BREAKPOINT = 768;
    
    // Function to update slide background based on screen size
    function updateSlideBackgrounds() {
        const isMobile = window.innerWidth <= MOBILE_BREAKPOINT;
        slides.forEach(slide => {
            const desktopImage = slide.dataset.desktopImage;
            const mobileImage = slide.dataset.mobileImage;
            const imageToUse = isMobile ? mobileImage : desktopImage;
            if (imageToUse) {
                slide.style.backgroundImage = `url('${imageToUse}')`;
            }
        });
    }
    
    // Initial background update
    updateSlideBackgrounds();
    
    // Update on resize (debounced)
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateSlideBackgrounds, 150);
    });
    
    if (slides.length <= 1) return;
    
    let currentIndex = 0;
    
    function showSlide(index) {
        const currentSlide = slides[currentIndex];
        const nextSlide = slides[index];
        
        slides.forEach(slide => {
            slide.classList.remove('active', 'slide-in-right', 'slide-out-left');
        });
        
        currentSlide.classList.add('slide-out-left');
        nextSlide.classList.add('slide-in-right', 'active');
        
        currentIndex = index;
    }
    
    function nextSlide() {
        const nextIndex = (currentIndex + 1) % slides.length;
        showSlide(nextIndex);
    }
    
    // Auto slide every 5 seconds
    setInterval(nextSlide, 5000);
    
    // Initialize infinite marquee
    initMarquee();
});

function initMarquee() {
    const marqueeTrack = document.querySelector('.marquee-track');
    if (!marqueeTrack) return;
    
    const marqueeContents = marqueeTrack.querySelectorAll('.marquee-content');
    if (marqueeContents.length < 2) return;
    
    // Calculate the width of one content set
    const contentWidth = marqueeContents[0].offsetWidth;
    
    // Set animation duration based on content width for consistent speed
    const speed = 50; // pixels per second
    const duration = contentWidth / speed;
    
    marqueeContents.forEach(content => {
        content.style.animationDuration = duration + 's';
    });
}
</script>

<?php 
// Include Business Hub Section
include __DIR__ . '/../templates/business-hub.php'; 
?>

<!-- Powerful Solutions Section - Razorpay-style Stacking Cards -->
<section class="section home-solutions-showcase" id="solutions-showcase" style="background: linear-gradient(180deg, <?php echo htmlspecialchars($solutionsUI['bg_start']); ?> 0%, <?php echo htmlspecialchars($solutionsUI['bg_mid']); ?> 50%, <?php echo htmlspecialchars($solutionsUI['bg_end']); ?> 100%);">
    <div class="container">
        <!-- Section Header -->
        <div class="home-solutions-header">
            <h2 class="home-solutions-title" style="color: <?php echo htmlspecialchars($solutionsUI['title_color']); ?>;"><?php echo htmlspecialchars($solutionsUI['title']); ?></h2>
            <p class="home-solutions-subtitle" style="color: <?php echo htmlspecialchars($solutionsUI['subtitle_color']); ?>;"><?php echo htmlspecialchars($solutionsUI['subtitle']); ?></p>
        </div>
    </div>
    
    <!-- Dynamic CSS Variables for Solutions Section -->
    <style>
        #solutions-showcase .home-solutions-nav { background: <?php echo htmlspecialchars($solutionsUI['nav_bg']); ?>; border-bottom-color: <?php echo htmlspecialchars($solutionsUI['nav_border']); ?>; }
        #solutions-showcase .home-solutions-nav-link { color: <?php echo htmlspecialchars($solutionsUI['nav_link_color']); ?>; }
        #solutions-showcase .home-solutions-nav-link:hover { color: <?php echo htmlspecialchars($solutionsUI['nav_link_hover']); ?>; }
        #solutions-showcase .home-solutions-nav-link.active { color: <?php echo htmlspecialchars($solutionsUI['nav_link_active']); ?>; }
        #solutions-showcase .home-solutions-nav-link.active::after { background-color: <?php echo htmlspecialchars($solutionsUI['nav_active_border']); ?>; }
        #solutions-showcase .home-solution-card-inner { background: <?php echo htmlspecialchars($solutionsUI['card_bg']); ?>; border-color: <?php echo htmlspecialchars($solutionsUI['card_border']); ?>; }
        #solutions-showcase .home-solution-badge { background: <?php echo htmlspecialchars($solutionsUI['badge_bg']); ?>; color: <?php echo htmlspecialchars($solutionsUI['badge_text']); ?>; }
        #solutions-showcase .home-solution-heading { color: <?php echo htmlspecialchars($solutionsUI['heading_color']); ?>; }
        #solutions-showcase .home-solution-description { color: <?php echo htmlspecialchars($solutionsUI['description_color']); ?>; }
        #solutions-showcase .home-solution-features li { color: <?php echo htmlspecialchars($solutionsUI['feature_color']); ?>; }
        #solutions-showcase .home-solution-cta { color: <?php echo htmlspecialchars($solutionsUI['cta_color']); ?>; }
        #solutions-showcase .home-solution-cta:hover { color: <?php echo htmlspecialchars($solutionsUI['cta_hover']); ?>; }
        #solutions-showcase .home-solutions-more-btn { background: linear-gradient(135deg, <?php echo htmlspecialchars($solutionsUI['btn_bg_start']); ?> 0%, <?php echo htmlspecialchars($solutionsUI['btn_bg_end']); ?> 100%); color: <?php echo htmlspecialchars($solutionsUI['btn_text']); ?>; }
        #solutions-showcase .home-solutions-more-btn:hover { background: linear-gradient(135deg, <?php echo htmlspecialchars($solutionsUI['btn_hover_start']); ?> 0%, <?php echo htmlspecialchars($solutionsUI['btn_hover_end']); ?> 100%); }
    </style>
    
    <?php if (!empty($featuredSolutions)): ?>
    <!-- Anchor Navigation Strip -->
    <nav class="home-solutions-nav" id="solutions-nav">
        <div class="container">
            <div class="home-solutions-nav-inner">
                <?php foreach ($featuredSolutions as $index => $solution): ?>
                    <a href="#solution-card-<?php echo $index + 1; ?>" 
                       class="home-solutions-nav-link <?php echo $index === 0 ? 'active' : ''; ?>" 
                       data-target="solution-card-<?php echo $index + 1; ?>">
                        <?php echo htmlspecialchars($solution['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
    
    <!-- Stacking Cards Container -->
    <div class="home-solutions-cards-wrapper">
        <div class="container">
            <div class="home-solutions-cards">
                <?php foreach ($featuredSolutions as $index => $solution): ?>
                <!-- Solution Card <?php echo $index + 1; ?> -->
                <div class="home-solution-card" id="solution-card-<?php echo $index + 1; ?>" data-card-index="<?php echo $index; ?>">
                    <div class="home-solution-card-inner">
                        <div class="home-solution-media">
                            <div class="home-solution-media-frame">
                                <?php if (!empty($solution['icon_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($solution['icon_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($solution['name']); ?>" 
                                         class="home-solution-img"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="home-solution-icon-placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="home-solution-content">
                            <span class="home-solution-badge">Solution</span>
                            <h3 class="home-solution-heading"><?php echo htmlspecialchars($solution['name']); ?></h3>
                            <?php if (!empty($solution['description'])): ?>
                                <p class="home-solution-description"><?php echo htmlspecialchars($solution['description']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($solution['features'])): ?>
                                <ul class="home-solution-features">
                                    <?php 
                                    $features = is_array($solution['features']) ? $solution['features'] : explode("\n", $solution['features']);
                                    $features = array_slice(array_filter($features), 0, 4);
                                    foreach ($features as $feature): 
                                    ?>
                                        <li>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            <?php echo htmlspecialchars(trim(is_array($feature) ? ($feature['title'] ?? $feature['name'] ?? '') : $feature)); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <a href="<?php echo get_base_url(); ?>/solution/<?php echo urlencode($solution['slug']); ?>" class="home-solution-cta">
                                <span>Learn More</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- View All Solutions Button -->
                <div class="home-solutions-more">
                    <a href="<?php echo get_base_url(); ?>/solutions.php" class="home-solutions-more-btn">
                        <span>View All Solutions</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Default solutions when none featured - Stacking Cards -->
    <nav class="home-solutions-nav" id="solutions-nav">
        <div class="container">
            <div class="home-solutions-nav-inner">
                <a href="#solution-card-1" class="home-solutions-nav-link active" data-target="solution-card-1">Customer Management</a>
                <a href="#solution-card-2" class="home-solutions-nav-link" data-target="solution-card-2">Subscription Management</a>
                <a href="#solution-card-3" class="home-solutions-nav-link" data-target="solution-card-3">Support Ticketing</a>
            </div>
        </div>
    </nav>
    
    <div class="home-solutions-cards-wrapper">
        <div class="container">
            <div class="home-solutions-cards">
                <!-- Default Card 1 -->
                <div class="home-solution-card" id="solution-card-1" data-card-index="0">
                    <div class="home-solution-card-inner">
                        <div class="home-solution-media">
                            <div class="home-solution-media-frame">
                                <div class="home-solution-icon-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="home-solution-content">
                            <span class="home-solution-badge">Solution</span>
                            <h3 class="home-solution-heading">Customer Management</h3>
                            <p class="home-solution-description">Manage customer relationships, track interactions, and provide excellent service with our comprehensive CRM solution.</p>
                            <ul class="home-solution-features">
                                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>360° customer view</li>
                                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Interaction tracking</li>
                                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Automated follow-ups</li>
                            </ul>
                            <a href="<?php echo get_base_url(); ?>/solutions.php" class="home-solution-cta">
                                <span>Learn More</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Default Card 2 -->
                <div class="home-solution-card" id="solution-card-2" data-card-index="1">
                    <div class="home-solution-card-inner">
                        <div class="home-solution-media">
                            <div class="home-solution-media-frame">
                                <div class="home-solution-icon-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="home-solution-content">
                            <span class="home-solution-badge">Solution</span>
                            <h3 class="home-solution-heading">Subscription Management</h3>
                            <p class="home-solution-description">Handle subscriptions, billing, and renewals automatically with ease. Perfect for SaaS and recurring revenue businesses.</p>
                            <ul class="home-solution-features">
                                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Automated billing cycles</li>
                                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Plan upgrades & downgrades</li>
                                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Revenue analytics</li>
                            </ul>
                            <a href="<?php echo get_base_url(); ?>/solutions.php" class="home-solution-cta">
                                <span>Learn More</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Default Card 3 -->
                <div class="home-solution-card" id="solution-card-3" data-card-index="2">
                    <div class="home-solution-card-inner">
                        <div class="home-solution-media">
                            <div class="home-solution-media-frame">
                                <div class="home-solution-icon-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="home-solution-content">
                            <span class="home-solution-badge">Solution</span>
                            <h3 class="home-solution-heading">Support Ticketing</h3>
                            <p class="home-solution-description">Provide exceptional customer support with our integrated ticketing system. Track, prioritize, and resolve issues efficiently.</p>
                            <ul class="home-solution-features">
                                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Multi-channel support</li>
                                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>SLA management</li>
                                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Knowledge base integration</li>
                            </ul>
                            <a href="<?php echo get_base_url(); ?>/solutions.php" class="home-solution-cta">
                                <span>Learn More</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- View All Solutions Button -->
                <div class="home-solutions-more">
                    <a href="<?php echo get_base_url(); ?>/solutions.php" class="home-solutions-more-btn">
                        <span>View All Solutions</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

<!-- Homepage CTA Banner Section -->
<?php if ($homepageCtaBanner['enabled']): ?>
<section class="home-cta-banner" style="
    --cta-banner-overlay-color: <?php echo htmlspecialchars($homepageCtaBanner['overlay_color']); ?>;
    --cta-banner-overlay-intensity: <?php echo htmlspecialchars($homepageCtaBanner['overlay_intensity']); ?>;
    --cta-banner-heading-color: <?php echo htmlspecialchars($homepageCtaBanner['heading_color']); ?>;
    --cta-banner-button-bg: <?php echo htmlspecialchars($homepageCtaBanner['button_bg_color']); ?>;
    --cta-banner-button-text: <?php echo htmlspecialchars($homepageCtaBanner['button_text_color']); ?>;
">
    <div class="home-cta-container">
        <div class="home-cta-image-wrapper">
            <img src="<?php echo htmlspecialchars($homepageCtaBanner['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($homepageCtaBanner['heading1']); ?>" 
                 class="home-cta-image">
            <div class="home-cta-overlay"></div>
        </div>
        <div class="home-cta-content">
            <h2 class="home-cta-heading">
                <?php if (!empty($homepageCtaBanner['heading1'])): ?>
                    <span><?php echo htmlspecialchars($homepageCtaBanner['heading1']); ?></span>
                <?php endif; ?>
                <?php if (!empty($homepageCtaBanner['heading2'])): ?>
                    <span><?php echo htmlspecialchars($homepageCtaBanner['heading2']); ?></span>
                <?php endif; ?>
            </h2>
            <a href="<?php echo htmlspecialchars($homepageCtaBanner['button_link']); ?>" class="home-cta-button">
                <?php echo htmlspecialchars($homepageCtaBanner['button_text']); ?>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Home Solutions Stacking Cards JavaScript -->
<script>
(function initHomeSolutionsShowcase() {
    const showcaseSection = document.querySelector('.home-solutions-showcase');
    if (!showcaseSection) return;
    
    const nav = document.getElementById('solutions-nav');
    const navLinks = document.querySelectorAll('.home-solutions-nav-link');
    const cards = document.querySelectorAll('.home-solution-card');
    
    if (!nav || !cards.length) return;
    
    // Get header height for offset calculations
    const header = document.querySelector('header, .header, .site-header');
    const headerHeight = header ? header.offsetHeight : 64;
    
    // Sticky nav detection
    const navTop = nav.offsetTop - headerHeight;
    let isNavSticky = false;
    
    // Update active nav link based on scroll position
    function updateActiveNav() {
        const scrollY = window.scrollY;
        const navHeight = nav.offsetHeight;
        const totalOffset = headerHeight + navHeight + 20;
        
        // Check if nav should be sticky
        if (scrollY > navTop) {
            if (!isNavSticky) {
                nav.classList.add('is-sticky');
                isNavSticky = true;
            }
        } else {
            if (isNavSticky) {
                nav.classList.remove('is-sticky');
                isNavSticky = false;
            }
        }
        
        // Find the current active card
        let activeIndex = 0;
        cards.forEach((card, index) => {
            const rect = card.getBoundingClientRect();
            const cardTop = rect.top;
            
            // Card is considered active when its top is near the viewport top
            if (cardTop <= totalOffset + 50) {
                activeIndex = index;
            }
            
            // Apply stacking effect to cards that have scrolled past
            if (cardTop < totalOffset) {
                card.classList.add('is-stacked');
            } else {
                card.classList.remove('is-stacked');
            }
        });
        
        // Update nav links
        navLinks.forEach((link, index) => {
            if (index === activeIndex) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }
    
    // Smooth scroll to card on nav click
    navLinks.forEach((link, index) => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('data-target');
            const targetCard = document.getElementById(targetId);
            
            if (targetCard) {
                const navHeight = nav.offsetHeight;
                const cardRect = targetCard.getBoundingClientRect();
                const currentScrollY = window.scrollY;
                const cardAbsoluteTop = cardRect.top + currentScrollY;
                const scrollTarget = cardAbsoluteTop - headerHeight - navHeight - 20;
                
                window.scrollTo({
                    top: Math.max(0, scrollTarget),
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Throttled scroll handler
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                updateActiveNav();
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // Initial check
    updateActiveNav();
})();
</script>

<!-- Why Choose Section -->
<section class="section why-choose-section" style="background-color: <?php echo htmlspecialchars($whyChooseUI['section_bg']); ?>;">
    <div class="container">
        <h2 class="section-title" style="color: <?php echo htmlspecialchars($whyChooseUI['title_color']); ?>;"><?php echo htmlspecialchars($whyChooseUI['title']); ?></h2>
        <p class="section-subtitle" style="color: <?php echo htmlspecialchars($whyChooseUI['subtitle_color']); ?>;">
            <?php echo htmlspecialchars($whyChooseUI['subtitle']); ?>
        </p>
        
        <div class="why-choose-grid">
            <?php if (!empty($whyChooseCards)): ?>
                <?php foreach ($whyChooseCards as $card): ?>
                    <div class="why-choose-card<?php echo !empty($card['link_url']) ? ' clickable' : ''; ?>"
                         style="background-color: <?php echo htmlspecialchars($whyChooseUI['card_bg']); ?>;"
                         <?php if (!empty($card['link_url'])): ?>onclick="window.location.href='<?php echo htmlspecialchars($card['link_url']); ?>'"<?php endif; ?>>
                        <div class="why-choose-card-image">
                            <img src="<?php echo htmlspecialchars($card['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($card['title']); ?>"
                                 loading="lazy">
                        </div>
                        <div class="why-choose-card-content">
                            <h3 class="why-choose-card-title" style="color: <?php echo htmlspecialchars($whyChooseUI['card_title_color']); ?>;"><?php echo htmlspecialchars($card['title']); ?></h3>
                            <?php if (!empty($card['description'])): ?>
                                <p class="why-choose-card-description" style="color: <?php echo htmlspecialchars($whyChooseUI['card_desc_color']); ?>;"><?php echo htmlspecialchars($card['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default cards when none configured -->
                <div class="why-choose-card" style="background-color: <?php echo htmlspecialchars($whyChooseUI['card_bg']); ?>;">
                    <div class="why-choose-card-image">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=640&h=360&fit=crop" alt="Modular Design" loading="lazy">
                    </div>
                    <div class="why-choose-card-content">
                        <h3 class="why-choose-card-title" style="color: <?php echo htmlspecialchars($whyChooseUI['card_title_color']); ?>;">Modular Design</h3>
                        <p class="why-choose-card-description" style="color: <?php echo htmlspecialchars($whyChooseUI['card_desc_color']); ?>;">Choose the modules you need and scale as your business grows.</p>
                    </div>
                </div>
                <div class="why-choose-card" style="background-color: <?php echo htmlspecialchars($whyChooseUI['card_bg']); ?>;">
                    <div class="why-choose-card-image">
                        <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=640&h=360&fit=crop" alt="Easy to Use" loading="lazy">
                    </div>
                    <div class="why-choose-card-content">
                        <h3 class="why-choose-card-title" style="color: <?php echo htmlspecialchars($whyChooseUI['card_title_color']); ?>;">Easy to Use</h3>
                        <p class="why-choose-card-description" style="color: <?php echo htmlspecialchars($whyChooseUI['card_desc_color']); ?>;">Intuitive interface designed for users of all technical levels.</p>
                    </div>
                </div>
                <div class="why-choose-card" style="background-color: <?php echo htmlspecialchars($whyChooseUI['card_bg']); ?>;">
                    <div class="why-choose-card-image">
                        <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?w=640&h=360&fit=crop" alt="Secure & Reliable" loading="lazy">
                    </div>
                    <div class="why-choose-card-content">
                        <h3 class="why-choose-card-title" style="color: <?php echo htmlspecialchars($whyChooseUI['card_title_color']); ?>;">Secure & Reliable</h3>
                        <p class="why-choose-card-description" style="color: <?php echo htmlspecialchars($whyChooseUI['card_desc_color']); ?>;">Enterprise-grade security with regular backups.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Success Stories Gallery Section - Horizontal Scroll on Sticky -->
<?php if (!empty($featuredCaseStudies) && $successStoriesUI['enabled']): ?>
<section class="success-stories-gallery" id="success-stories-gallery" style="
    --stories-bg-color: linear-gradient(135deg, <?php echo htmlspecialchars($successStoriesUI['bg_gradient_start']); ?> 0%, <?php echo htmlspecialchars($successStoriesUI['bg_gradient_end']); ?> 100%);
    --stories-title-color: <?php echo htmlspecialchars($successStoriesUI['title_color']); ?>;
    --stories-subtitle-color: <?php echo htmlspecialchars($successStoriesUI['subtitle_color']); ?>;
    --stories-card-overlay: <?php echo htmlspecialchars($successStoriesUI['card_overlay']); ?>;
    --stories-card-title: <?php echo htmlspecialchars($successStoriesUI['card_title']); ?>;
    --stories-card-industry: <?php echo htmlspecialchars($successStoriesUI['card_industry']); ?>;
    --stories-card-desc: <?php echo htmlspecialchars($successStoriesUI['card_desc']); ?>;
    --stories-card-badge-bg: <?php echo htmlspecialchars($successStoriesUI['card_badge_bg']); ?>;
    --stories-card-badge-text: <?php echo htmlspecialchars($successStoriesUI['card_badge_text']); ?>;
    --stories-card-btn-bg: <?php echo htmlspecialchars($successStoriesUI['card_btn_bg']); ?>;
    --stories-card-btn-text: <?php echo htmlspecialchars($successStoriesUI['card_btn_text']); ?>;
    --stories-view-all-bg: <?php echo htmlspecialchars($successStoriesUI['view_all_bg']); ?>;
    --stories-view-all-text: <?php echo htmlspecialchars($successStoriesUI['view_all_text']); ?>;
    --stories-view-all-icon-bg: <?php echo htmlspecialchars($successStoriesUI['view_all_icon_bg']); ?>;
    --stories-view-all-icon-hover-bg: <?php echo htmlspecialchars($successStoriesUI['view_all_icon_hover_bg']); ?>;
">
    <div class="success-stories-sticky-wrapper">
        <div class="container">
            <div class="success-stories-header">
                <h2 class="success-stories-title"><?php echo htmlspecialchars($successStoriesUI['title']); ?></h2>
                <p class="success-stories-subtitle"><?php echo htmlspecialchars($successStoriesUI['subtitle']); ?></p>
            </div>
        </div>
        
        <div class="success-stories-scroll-container">
            <div class="success-stories-track">
                <?php foreach ($featuredCaseStudies as $caseStudy): ?>
                <div class="success-story-card">
                    <?php 
                    // Use cover image or a default placeholder
                    $imageUrl = !empty($caseStudy['cover_image']) 
                        ? htmlspecialchars($caseStudy['cover_image']) 
                        : 'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=800&h=600&fit=crop';
                    ?>
                    <img src="<?php echo $imageUrl; ?>" alt="<?php echo htmlspecialchars($caseStudy['title']); ?>" class="success-story-image" loading="lazy">
                    <div class="success-story-overlay"></div>
                    <div class="success-story-content">
                        <?php if (!empty($caseStudy['client_name'])): ?>
                        <span class="success-story-client"><?php echo htmlspecialchars($caseStudy['client_name']); ?></span>
                        <?php endif; ?>
                        <h3 class="success-story-title"><?php echo htmlspecialchars($caseStudy['title']); ?></h3>
                        <?php if (!empty($caseStudy['industry'])): ?>
                        <p class="success-story-industry"><?php echo htmlspecialchars($caseStudy['industry']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($caseStudy['challenge'])): ?>
                        <p class="success-story-description"><?php echo htmlspecialchars(substr($caseStudy['challenge'], 0, 120)); ?><?php echo strlen($caseStudy['challenge']) > 120 ? '...' : ''; ?></p>
                        <?php endif; ?>
                        <a href="<?php echo get_base_url(); ?>/case-study/<?php echo urlencode($caseStudy['slug']); ?>" class="success-story-btn">
                            Read Full Story
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- View All Button -->
                <div class="success-stories-view-all">
                    <a href="<?php echo get_base_url(); ?>/case-studies.php" class="success-stories-view-all-link">
                        <div class="success-stories-view-all-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <span class="success-stories-view-all-text">View All<br>Case Studies</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Blog Gallery Section - Modern Horizontal Scroll -->
<?php if (!empty($featuredBlogPosts) && $blogUI['enabled']): ?>
<section class="blog-gallery bg-<?php echo htmlspecialchars($blogUI['theme']); ?>" id="blog-gallery" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($blogUI['bg_start']); ?> 0%, <?php echo htmlspecialchars($blogUI['bg_end']); ?> 100%);">
    <!-- Background Effects -->
    <div class="blog-gallery-bg-effects">
        <div class="blog-gallery-orb blog-gallery-orb-1"></div>
        <div class="blog-gallery-orb blog-gallery-orb-2"></div>
    </div>
    
    <div class="container">
        <div class="blog-gallery-header">
            <h2 class="blog-gallery-title" style="color: <?php echo htmlspecialchars($blogUI['title_color']); ?>;"><?php echo htmlspecialchars($blogUI['title']); ?></h2>
            <p class="blog-gallery-subtitle" style="color: <?php echo htmlspecialchars($blogUI['subtitle_color']); ?>;"><?php echo htmlspecialchars($blogUI['subtitle']); ?></p>
        </div>
    </div>
    
    <!-- Dynamic CSS Variables for Blog Section -->
    <style>
        #blog-gallery .blog-gallery-card {
            background: <?php echo htmlspecialchars($blogUI['card_bg']); ?>;
            border-color: <?php echo htmlspecialchars($blogUI['card_border']); ?>;
        }
        #blog-gallery .blog-gallery-card-title a {
            color: <?php echo htmlspecialchars($blogUI['card_title_color']); ?>;
        }
        #blog-gallery .blog-gallery-card-title a:hover {
            color: <?php echo htmlspecialchars($blogUI['card_link_hover']); ?>;
        }
        #blog-gallery .blog-gallery-card-excerpt {
            color: <?php echo htmlspecialchars($blogUI['card_excerpt_color']); ?>;
        }
        #blog-gallery .blog-gallery-card-date {
            color: <?php echo htmlspecialchars($blogUI['card_date_color']); ?>;
        }
        #blog-gallery .blog-gallery-card-tag {
            background: <?php echo htmlspecialchars($blogUI['card_tag_bg']); ?>;
            color: <?php echo htmlspecialchars($blogUI['card_tag_text']); ?>;
        }
        #blog-gallery .blog-gallery-card-link {
            color: <?php echo htmlspecialchars($blogUI['card_link_color']); ?>;
        }
        #blog-gallery .blog-gallery-card-link:hover {
            color: <?php echo htmlspecialchars($blogUI['card_link_hover']); ?>;
        }
        #blog-gallery .blog-gallery-view-all {
            background: <?php echo htmlspecialchars($blogUI['view_all_bg']); ?>;
            border-color: <?php echo htmlspecialchars($blogUI['view_all_border']); ?>;
        }
        #blog-gallery .blog-gallery-view-all-text {
            color: <?php echo htmlspecialchars($blogUI['view_all_text']); ?>;
        }
        #blog-gallery .blog-gallery-view-all-icon {
            background: <?php echo htmlspecialchars($blogUI['view_all_icon_bg']); ?>;
            color: <?php echo htmlspecialchars($blogUI['card_link_color']); ?>;
        }
    </style>
    
    <div class="blog-gallery-scroll-wrapper">
        <div class="container">
            <div class="blog-gallery-track">
                <?php foreach ($featuredBlogPosts as $post): ?>
                <article class="blog-gallery-card">
                    <div class="blog-gallery-card-image">
                        <?php 
                        $imageUrl = !empty($post['featured_image']) 
                            ? htmlspecialchars($post['featured_image']) 
                            : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=800&h=600&fit=crop';
                        ?>
                        <img src="<?php echo $imageUrl; ?>" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>"
                             loading="lazy">
                    </div>
                    <div class="blog-gallery-card-content">
                        <div class="blog-gallery-card-meta">
                            <?php if (!empty($post['published_at'])): ?>
                            <span class="blog-gallery-card-date">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($post['tags']) && is_array($post['tags']) && count($post['tags']) > 0): ?>
                            <span class="blog-gallery-card-tag"><?php echo htmlspecialchars($post['tags'][0]); ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="blog-gallery-card-title">
                            <a href="<?php echo get_base_url(); ?>/blog/<?php echo urlencode($post['slug']); ?>">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h3>
                        <?php if (!empty($post['excerpt'])): ?>
                        <p class="blog-gallery-card-excerpt">
                            <?php echo htmlspecialchars(substr($post['excerpt'], 0, 200)); ?>
                            <?php echo strlen($post['excerpt']) > 200 ? '...' : ''; ?>
                        </p>
                        <?php endif; ?>
                        <a href="<?php echo get_base_url(); ?>/blog/<?php echo urlencode($post['slug']); ?>" class="blog-gallery-card-link">
                            Read Article
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
                
                <!-- View All Card -->
                <div class="blog-gallery-view-all">
                    <a href="<?php echo get_base_url(); ?>/blog.php" class="blog-gallery-view-all-link">
                        <div class="blog-gallery-view-all-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <span class="blog-gallery-view-all-text">View All<br>Blog Posts</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Navigation Controls -->
        <div class="blog-gallery-controls">
            <button class="blog-gallery-btn blog-gallery-btn-prev" aria-label="Previous posts">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="blog-gallery-dots"></div>
            <button class="blog-gallery-btn blog-gallery-btn-next" aria-label="Next posts">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Testimonials Showcase Section -->
<?php if (!empty($testimonials) && $testimonialsUI['enabled']): ?>
<?php
$testimonials_config = [
    'testimonials' => $testimonials,
    'title' => $testimonialsUI['heading'],
    'subtitle' => $testimonialsUI['subheading'],
    'theme' => $testimonialsUI['theme'],
    'max_items' => 6,
    'accent_color' => $testimonialsUI['accent_color']
];
include __DIR__ . '/../templates/components/testimonials-showcase.php';
?>
<?php endif; ?>

<!-- Homepage FAQ Section -->
<?php if ($homepageFaq['enabled'] && !empty($homepageFaq['items'])): ?>
<section class="home-faqs home-faqs-<?php echo htmlspecialchars($homepageFaq['theme']); ?>" id="homepage-faqs">
    <div class="home-faqs-bg">
        <div class="home-faqs-gradient"></div>
        <div class="home-faqs-pattern"></div>
        <div class="home-faqs-orb home-faqs-orb-1"></div>
        <div class="home-faqs-orb home-faqs-orb-2"></div>
    </div>
    <div class="container">
        <div class="home-faqs-layout">
            <!-- Left Side - Header & Decoration -->
            <div class="home-faqs-sidebar">
                <div class="home-faqs-sidebar-content">
                    <h2 class="home-faqs-title"><?php echo nl2br(htmlspecialchars($homepageFaq['heading'])); ?></h2>
                    <p class="home-faqs-subtitle"><?php echo htmlspecialchars($homepageFaq['subheading']); ?></p>
                    <a href="#contact-form" class="home-faqs-contact-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Contact Support
                    </a>
                </div>
                <div class="home-faqs-decoration">
                    <div class="home-faqs-decoration-circle home-faqs-decoration-circle-1"></div>
                    <div class="home-faqs-decoration-circle home-faqs-decoration-circle-2"></div>
                    <div class="home-faqs-decoration-circle home-faqs-decoration-circle-3"></div>
                </div>
            </div>
            
            <!-- Right Side - FAQ Items -->
            <div class="home-faqs-content">
                <div class="home-faqs-list">
                    <?php foreach ($homepageFaq['items'] as $index => $faq): ?>
                        <div class="home-faq-item" data-faq-index="<?php echo $index; ?>">
                            <button class="home-faq-question" aria-expanded="false" aria-controls="home-faq-<?php echo $index; ?>">
                                <span class="home-faq-number"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                                <span class="home-faq-question-text"><?php echo htmlspecialchars($faq['question']); ?></span>
                                <span class="home-faq-toggle">
                                    <svg class="home-faq-icon-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"/>
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                    <svg class="home-faq-icon-minus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                </span>
                            </button>
                            <div class="home-faq-answer" id="home-faq-<?php echo $index; ?>">
                                <div class="home-faq-answer-inner">
                                    <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- FAQ Footer -->
                <div class="home-faqs-footer">
                    <div class="home-faqs-footer-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="home-faqs-footer-text">
                        <strong>Still have questions?</strong>
                        <span>Our team is here to help you 24/7</span>
                    </div>
                    <a href="#contact-form" class="home-faqs-footer-btn">Get in Touch</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Homepage FAQ JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Accordion functionality
    document.querySelectorAll('.home-faq-question').forEach(button => {
        button.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            const parentItem = this.closest('.home-faq-item');
            
            // Close all other FAQs
            document.querySelectorAll('.home-faq-question').forEach(otherBtn => {
                otherBtn.setAttribute('aria-expanded', 'false');
                otherBtn.closest('.home-faq-item').setAttribute('data-expanded', 'false');
            });
            
            // Toggle current FAQ
            if (!isExpanded) {
                this.setAttribute('aria-expanded', 'true');
                parentItem.setAttribute('data-expanded', 'true');
            }
        });
    });

    // Add entrance animation for FAQ items
    const homeFaqObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
                homeFaqObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.home-faq-item').forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        homeFaqObserver.observe(item);
    });
});
</script>
<?php endif; ?>

<!-- CTA Section -->
<?php if ($ctaFormUI['enabled']): ?>
<?php
$cta_title = $ctaFormUI['title'];
$cta_subtitle = $ctaFormUI['subtitle'];
$cta_theme = $ctaFormUI['theme'];
$cta_source = "homepage";
$cta_badge_text = $ctaFormUI['badge_text'];
$cta_form_header = $ctaFormUI['form_header'];
$cta_form_header_subtitle = $ctaFormUI['form_header_subtitle'];
$cta_submit_text = $ctaFormUI['submit_text'];
$cta_privacy_text = $ctaFormUI['privacy_text'];
$cta_accent_color = $ctaFormUI['accent_color'];
$cta_btn_gradient_start = $ctaFormUI['btn_gradient_start'];
$cta_btn_gradient_end = $ctaFormUI['btn_gradient_end'];
$cta_badge_bg = $ctaFormUI['badge_bg'];
$cta_icon_bg = $ctaFormUI['icon_bg'];
include __DIR__ . '/../templates/cta-form.php';
?>
<?php endif; ?>

<?php
// Include footer with additional JS for galleries and testimonials
include_footer([
    js_url('success-stories-gallery.js'),
    js_url('blog-gallery.js'),
    js_url('testimonials-showcase.js')
]);
?>
