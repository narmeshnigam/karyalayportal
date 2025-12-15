<?php
/**
 * Solution Detail Page - Razorpay-Style Modern Design
 * A beautifully designed page showcasing solution details with:
 * - Hero with badge, stats, and dual CTAs
 * - Highlight cards with metrics
 * - How it works workflow
 * - Features grid
 * - Use cases
 * - Integrations
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

use Karyalay\Models\Solution;
use Karyalay\Models\ClientLogo;
use Karyalay\Models\Testimonial;

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: ' . get_base_url() . '/solutions.php');
    exit;
}

try {
    $solutionModel = new Solution();
    $solution = $solutionModel->findBySlug($slug);
    
    if (!$solution || $solution['status'] !== 'PUBLISHED') {
        header('HTTP/1.0 404 Not Found');
        $page_title = 'Solution Not Found';
        $page_description = 'The requested solution could not be found';
        include_header($page_title, $page_description);
        ?>
        <section class="section">
            <div class="container">
                <div class="not-found-card">
                    <div class="not-found-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1>Solution Not Found</h1>
                    <p>The solution you're looking for doesn't exist or is no longer available.</p>
                    <a href="<?php echo get_base_url(); ?>/solutions.php" class="btn btn-primary">View All Solutions</a>
                </div>
            </div>
        </section>
        <?php
        include_footer();
        exit;
    }

    // Get linked features
    $linkedFeatures = $solutionModel->getLinkedFeatures($solution['id']);
    
    // Get related solutions
    $relatedSolutions = $solutionModel->getRelatedSolutions($solution['id'], 3);
    
    // Load client logos for marquee
    $clientLogos = [];
    try {
        $clientLogoModel = new ClientLogo();
        $clientLogos = $clientLogoModel->getPublishedLogos();
    } catch (Exception $e) {
        error_log("Client logos not available: " . $e->getMessage());
    }
    
    // Load testimonials for showcase section
    $testimonials = [];
    try {
        $testimonialModel = new Testimonial();
        $testimonials = $testimonialModel->getFeatured(6);
    } catch (Exception $e) {
        error_log("Testimonials not available: " . $e->getMessage());
    }
    
} catch (Exception $e) {
    error_log('Error fetching solution: ' . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    $page_title = 'Error';
    include_header($page_title, '');
    ?>
    <section class="section">
        <div class="container">
            <div class="not-found-card">
                <h1>Error</h1>
                <p>An error occurred while loading the solution. Please try again later.</p>
                <a href="<?php echo get_base_url(); ?>/solutions.php" class="btn btn-primary">View All Solutions</a>
            </div>
        </div>
    </section>
    <?php
    include_footer();
    exit;
}

$page_title = !empty($solution['meta_title']) ? htmlspecialchars($solution['meta_title']) : htmlspecialchars($solution['name']);
$page_description = !empty($solution['meta_description']) ? htmlspecialchars($solution['meta_description']) : htmlspecialchars($solution['description'] ?? '');
$colorTheme = $solution['color_theme'] ?? '#667eea';

// Additional CSS for industries gallery and testimonials
$additional_css = [
    css_url('solution-industries-gallery.css'),
    css_url('testimonials-showcase.css')
];

include_header($page_title, $page_description, $additional_css);
?>


<?php
// Prepare hero styling variables
$heroTitleColor = $solution['hero_title_color'] ?? '#FFFFFF';
$heroSubtitleColor = $solution['hero_subtitle_color'] ?? '#FFFFFF';
$heroPrimaryBtnBg = $solution['hero_primary_btn_bg_color'] ?? 'rgba(255,255,255,0.15)';
$heroPrimaryBtnText = $solution['hero_primary_btn_text_color'] ?? '#FFFFFF';
$heroPrimaryBtnTextHover = $solution['hero_primary_btn_text_hover_color'] ?? '#FFFFFF';
$heroPrimaryBtnBorder = $solution['hero_primary_btn_border_color'] ?? 'rgba(255,255,255,0.3)';
$heroSecondaryBtnBg = $solution['hero_secondary_btn_bg_color'] ?? 'rgba(255,255,255,0.1)';
$heroSecondaryBtnText = $solution['hero_secondary_btn_text_color'] ?? '#FFFFFF';
$heroSecondaryBtnTextHover = $solution['hero_secondary_btn_text_hover_color'] ?? '#FFFFFF';
$heroSecondaryBtnBorder = $solution['hero_secondary_btn_border_color'] ?? 'rgba(255,255,255,0.2)';
$heroTitle = !empty($solution['hero_title_text']) ? $solution['hero_title_text'] : $solution['name'];
$heroMediaUrl = $solution['hero_media_url'] ?? '';
$heroMediaType = $solution['hero_media_type'] ?? 'image';
// Background effect settings
$heroBgGradientOpacity = $solution['hero_bg_gradient_opacity'] ?? 0.60;
$heroBgPatternOpacity = $solution['hero_bg_pattern_opacity'] ?? 0.03;
$heroBgGradientColor = !empty($solution['hero_bg_gradient_color']) ? $solution['hero_bg_gradient_color'] : $colorTheme;
$heroBgColor = $solution['hero_bg_color'] ?? '#0a1628';
?>

<!-- Hero Section - Razorpay Style with Glassy Effects -->
<section class="sol-hero sol-hero-glassy-buttons sol-hero-glassy-media" 
         style="--theme-color: <?php echo htmlspecialchars($colorTheme); ?>;
                --hero-bg-color: <?php echo htmlspecialchars($heroBgColor); ?>;
                --hero-gradient-color: <?php echo htmlspecialchars($heroBgGradientColor); ?>;
                --hero-gradient-opacity: <?php echo htmlspecialchars($heroBgGradientOpacity); ?>;
                --hero-pattern-opacity: <?php echo htmlspecialchars($heroBgPatternOpacity); ?>;
                --hero-title-color: <?php echo htmlspecialchars($heroTitleColor); ?>;
                --hero-subtitle-color: <?php echo htmlspecialchars($heroSubtitleColor); ?>;
                --hero-primary-btn-bg: <?php echo htmlspecialchars($heroPrimaryBtnBg); ?>;
                --hero-primary-btn-text: <?php echo htmlspecialchars($heroPrimaryBtnText); ?>;
                --hero-primary-btn-text-hover: <?php echo htmlspecialchars($heroPrimaryBtnTextHover); ?>;
                --hero-primary-btn-border: <?php echo htmlspecialchars($heroPrimaryBtnBorder); ?>;
                --hero-secondary-btn-bg: <?php echo htmlspecialchars($heroSecondaryBtnBg); ?>;
                --hero-secondary-btn-text: <?php echo htmlspecialchars($heroSecondaryBtnText); ?>;
                --hero-secondary-btn-text-hover: <?php echo htmlspecialchars($heroSecondaryBtnTextHover); ?>;
                --hero-secondary-btn-border: <?php echo htmlspecialchars($heroSecondaryBtnBorder); ?>;">
    <div class="sol-hero-bg">
        <div class="sol-hero-gradient"></div>
        <div class="sol-hero-pattern"></div>
    </div>
    
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="sol-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo get_base_url(); ?>/">Home</a>
            <svg viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <a href="<?php echo get_base_url(); ?>/solutions.php">Solutions</a>
            <svg viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <span aria-current="page"><?php echo htmlspecialchars($solution['name']); ?></span>
        </nav>
        
        <div class="sol-hero-content">
            <div class="sol-hero-text">
                <?php if (!empty($solution['hero_badge'])): ?>
                    <span class="sol-hero-badge"><?php echo htmlspecialchars($solution['hero_badge']); ?></span>
                <?php endif; ?>
                
                <?php if (!empty($solution['tagline'])): ?>
                    <p class="sol-hero-tagline"><?php echo htmlspecialchars($solution['tagline']); ?></p>
                <?php endif; ?>
                
                <h1 class="sol-hero-title" style="color: var(--hero-title-color);"><?php echo htmlspecialchars($heroTitle); ?></h1>
                
                <?php if (!empty($solution['subtitle'])): ?>
                    <p class="sol-hero-subtitle" style="color: var(--hero-subtitle-color);"><?php echo htmlspecialchars($solution['subtitle']); ?></p>
                <?php elseif (!empty($solution['description'])): ?>
                    <p class="sol-hero-subtitle" style="color: var(--hero-subtitle-color);"><?php echo htmlspecialchars($solution['description']); ?></p>
                <?php endif; ?>
                
                <div class="sol-hero-actions">
                    <a href="<?php echo !empty($solution['hero_cta_primary_link']) ? htmlspecialchars($solution['hero_cta_primary_link']) : '#contact-form'; ?>" 
                       class="btn btn-lg sol-btn-primary sol-btn-glassy">
                        <?php echo htmlspecialchars($solution['hero_cta_primary_text'] ?? 'Get Started'); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <?php if (!empty($solution['demo_video_url']) || !empty($solution['hero_cta_secondary_link'])): ?>
                        <a href="<?php echo !empty($solution['demo_video_url']) ? htmlspecialchars($solution['demo_video_url']) : htmlspecialchars($solution['hero_cta_secondary_link']); ?>" 
                           class="btn sol-btn-secondary sol-btn-glassy" <?php echo !empty($solution['demo_video_url']) ? 'data-video-modal' : ''; ?>>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="5 3 19 12 5 21 5 3"/>
                            </svg>
                            <?php echo htmlspecialchars($solution['hero_cta_secondary_text'] ?? 'Watch Demo'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="sol-hero-visual">
                <?php if (!empty($heroMediaUrl)): ?>
                    <!-- Animated Media (GIF/MP4) - Primary display when media URL is set -->
                    <div class="sol-hero-media-wrapper sol-media-glassy">
                        <?php if ($heroMediaType === 'video'): ?>
                            <video class="sol-hero-media" autoplay loop muted playsinline>
                                <source src="<?php echo htmlspecialchars($heroMediaUrl); ?>" type="video/mp4">
                            </video>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($heroMediaUrl); ?>" alt="<?php echo htmlspecialchars($solution['name']); ?>" class="sol-hero-media">
                        <?php endif; ?>
                    </div>
                <?php elseif (!empty($solution['icon_image'])): ?>
                    <!-- Icon Image - Secondary fallback (shown in white) -->
                    <div class="sol-hero-icon-large sol-media-glassy">
                        <img src="<?php echo htmlspecialchars($solution['icon_image']); ?>" alt="" class="sol-hero-icon-white">
                    </div>
                <?php else: ?>
                    <!-- Document Icon - Default fallback when no media or icon is available -->
                    <div class="sol-hero-icon-default sol-media-glassy">
                        <svg class="sol-hero-default-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10,9 9,9 8,9"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>
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


<!-- Key Benefits Section (Razorpay-style) -->
<?php if (!empty($solution['key_benefits_section_enabled']) && !empty($solution['key_benefits_cards'])): ?>
<?php
$keyBenefitsBgColor = $solution['key_benefits_section_bg_color'] ?? '#0a1628';
$keyBenefitsHeadingColor = $solution['key_benefits_section_heading_color'] ?? '#FFFFFF';
$keyBenefitsSubheadingColor = $solution['key_benefits_section_subheading_color'] ?? 'rgba(255,255,255,0.7)';
$keyBenefitsCardBgColor = $solution['key_benefits_section_card_bg_color'] ?? 'rgba(255,255,255,0.08)';
$keyBenefitsCardBorderColor = $solution['key_benefits_section_card_border_color'] ?? 'rgba(255,255,255,0.1)';
$keyBenefitsCardHoverBgColor = $solution['key_benefits_section_card_hover_bg_color'] ?? '#2563eb';
$keyBenefitsCardTextColor = $solution['key_benefits_section_card_text_color'] ?? '#FFFFFF';
$keyBenefitsCardIconColor = $solution['key_benefits_section_card_icon_color'] ?? 'rgba(255,255,255,0.6)';
?>
<section class="sol-key-benefits-section" style="
    --key-benefits-bg-color: <?php echo htmlspecialchars($keyBenefitsBgColor); ?>;
    --key-benefits-heading-color: <?php echo htmlspecialchars($keyBenefitsHeadingColor); ?>;
    --key-benefits-subheading-color: <?php echo htmlspecialchars($keyBenefitsSubheadingColor); ?>;
    --key-benefits-card-bg: <?php echo htmlspecialchars($keyBenefitsCardBgColor); ?>;
    --key-benefits-card-border: <?php echo htmlspecialchars($keyBenefitsCardBorderColor); ?>;
    --key-benefits-card-hover-bg: <?php echo htmlspecialchars($keyBenefitsCardHoverBgColor); ?>;
    --key-benefits-card-text: <?php echo htmlspecialchars($keyBenefitsCardTextColor); ?>;
    --key-benefits-card-icon: <?php echo htmlspecialchars($keyBenefitsCardIconColor); ?>;
">
    <div class="container">
        <div class="sol-key-benefits-content">
            <div class="sol-key-benefits-text">
                <?php if (!empty($solution['key_benefits_section_heading1']) || !empty($solution['key_benefits_section_heading2'])): ?>
                    <h2 class="sol-key-benefits-heading">
                        <?php if (!empty($solution['key_benefits_section_heading1'])): ?>
                            <span class="sol-key-benefits-heading-line"><?php echo htmlspecialchars($solution['key_benefits_section_heading1']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($solution['key_benefits_section_heading2'])): ?>
                            <span class="sol-key-benefits-heading-line"><?php echo htmlspecialchars($solution['key_benefits_section_heading2']); ?></span>
                        <?php endif; ?>
                    </h2>
                <?php endif; ?>
                <?php if (!empty($solution['key_benefits_section_subheading'])): ?>
                    <p class="sol-key-benefits-subheading"><?php echo htmlspecialchars($solution['key_benefits_section_subheading']); ?></p>
                <?php endif; ?>
            </div>
            <div class="sol-key-benefits-cards">
                <?php 
                $cardCount = 0;
                foreach ($solution['key_benefits_cards'] as $card): 
                    if ($cardCount >= 4) break;
                    $cardCount++;
                ?>
                    <div class="sol-key-benefits-card">
                        <div class="sol-key-benefits-card-icon">
                            <?php echo get_key_benefits_card_icon($card['icon'] ?? 'check'); ?>
                        </div>
                        <h3 class="sol-key-benefits-card-title"><?php echo htmlspecialchars(substr($card['title'] ?? '', 0, 24)); ?></h3>
                        <p class="sol-key-benefits-card-desc"><?php echo htmlspecialchars(substr($card['description'] ?? '', 0, 240)); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- Feature Showcase Section - Stacking Cards (Razorpay-style) -->
<?php if (!empty($solution['feature_showcase_section_enabled']) && !empty($solution['feature_showcase_cards'])): ?>
<?php
$featureShowcaseBgColor = $solution['feature_showcase_section_bg_color'] ?? '#ffffff';
$featureShowcaseTitleColor = $solution['feature_showcase_section_title_color'] ?? '#1a202c';
$featureShowcaseSubtitleColor = $solution['feature_showcase_section_subtitle_color'] ?? '#718096';
$featureShowcaseCardBgColor = $solution['feature_showcase_card_bg_color'] ?? '#ffffff';
$featureShowcaseCardBorderColor = $solution['feature_showcase_card_border_color'] ?? '#e2e8f0';
$featureShowcaseCardBadgeBgColor = $solution['feature_showcase_card_badge_bg_color'] ?? '#ebf8ff';
$featureShowcaseCardBadgeTextColor = $solution['feature_showcase_card_badge_text_color'] ?? '#2b6cb0';
$featureShowcaseCardHeadingColor = $solution['feature_showcase_card_heading_color'] ?? '#1a202c';
$featureShowcaseCardTextColor = $solution['feature_showcase_card_text_color'] ?? '#4a5568';
$featureShowcaseCardIconColor = $solution['feature_showcase_card_icon_color'] ?? '#38a169';
$featureShowcaseCards = array_slice($solution['feature_showcase_cards'], 0, 6); // Max 6 cards
?>
<section class="sol-feature-showcase" id="feature-showcase" style="
    --feature-showcase-bg-color: <?php echo htmlspecialchars($featureShowcaseBgColor); ?>;
    --feature-showcase-title-color: <?php echo htmlspecialchars($featureShowcaseTitleColor); ?>;
    --feature-showcase-subtitle-color: <?php echo htmlspecialchars($featureShowcaseSubtitleColor); ?>;
    --feature-showcase-card-bg: <?php echo htmlspecialchars($featureShowcaseCardBgColor); ?>;
    --feature-showcase-card-border: <?php echo htmlspecialchars($featureShowcaseCardBorderColor); ?>;
    --feature-showcase-card-badge-bg: <?php echo htmlspecialchars($featureShowcaseCardBadgeBgColor); ?>;
    --feature-showcase-card-badge-text: <?php echo htmlspecialchars($featureShowcaseCardBadgeTextColor); ?>;
    --feature-showcase-card-heading: <?php echo htmlspecialchars($featureShowcaseCardHeadingColor); ?>;
    --feature-showcase-card-text: <?php echo htmlspecialchars($featureShowcaseCardTextColor); ?>;
    --feature-showcase-card-icon: <?php echo htmlspecialchars($featureShowcaseCardIconColor); ?>;
">
    <div class="container">
        <!-- Section Header -->
        <div class="sol-showcase-header">
            <h2 class="sol-showcase-title"><?php echo htmlspecialchars($solution['feature_showcase_section_title'] ?? 'One solution. All business sizes.'); ?></h2>
            <p class="sol-showcase-subtitle"><?php echo htmlspecialchars($solution['feature_showcase_section_subtitle'] ?? 'From instant, self-serve payouts to custom integrations for enterprise scale operations'); ?></p>
        </div>
    </div>
    
    <!-- Anchor Navigation Strip - Outside container for full-width sticky -->
    <nav class="sol-showcase-nav" id="showcase-nav">
        <div class="container">
            <div class="sol-showcase-nav-inner">
                <?php foreach ($featureShowcaseCards as $cardIndex => $card): ?>
                    <a href="#showcase-card-<?php echo $cardIndex + 1; ?>" 
                       class="sol-showcase-nav-link <?php echo $cardIndex === 0 ? 'active' : ''; ?>" 
                       data-target="showcase-card-<?php echo $cardIndex + 1; ?>">
                        <?php echo htmlspecialchars($card['nav_label'] ?? 'Feature ' . ($cardIndex + 1)); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
    
    <!-- Stacking Cards Container -->
    <div class="sol-showcase-cards-wrapper">
        <div class="container">
            <div class="sol-showcase-cards">
                <?php foreach ($featureShowcaseCards as $cardIndex => $card): ?>
                <!-- Card <?php echo $cardIndex + 1; ?>: <?php echo htmlspecialchars($card['nav_label'] ?? ''); ?> -->
                <div class="sol-showcase-card" id="showcase-card-<?php echo $cardIndex + 1; ?>" data-card-index="<?php echo $cardIndex; ?>">
                    <div class="sol-showcase-card-inner">
                        <div class="sol-showcase-media">
                            <div class="sol-showcase-media-frame">
                                <?php if (!empty($card['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($card['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($card['nav_label'] ?? 'Feature'); ?>" 
                                         class="sol-showcase-img">
                                <?php else: ?>
                                    <img src="https://placehold.co/420x315/e8f4f8/1a365d?text=<?php echo urlencode($card['nav_label'] ?? 'Feature'); ?>" 
                                         alt="<?php echo htmlspecialchars($card['nav_label'] ?? 'Feature'); ?>" 
                                         class="sol-showcase-img">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="sol-showcase-content">
                            <?php if (!empty($card['badge'])): ?>
                                <span class="sol-showcase-badge"><?php echo htmlspecialchars($card['badge']); ?></span>
                            <?php endif; ?>
                            <h3 class="sol-showcase-heading"><?php echo htmlspecialchars($card['heading'] ?? ''); ?></h3>
                            <?php if (!empty($card['features']) && is_array($card['features'])): ?>
                                <ul class="sol-showcase-features">
                                    <?php foreach ($card['features'] as $feature): ?>
                                        <li>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            <?php echo htmlspecialchars($feature); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- More Features Button -->
                <div class="sol-showcase-more">
                    <a href="<?php echo get_base_url(); ?>/features.php" class="sol-showcase-more-btn">
                        <span>Explore All Features</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- Screenshots Section -->
<?php if (!empty($solution['screenshots'])): ?>
<section class="sol-screenshots">
    <div class="container">
        <div class="sol-section-header">
            <h2 class="sol-section-title">See It In Action</h2>
            <p class="sol-section-subtitle">Take a closer look at the interface</p>
        </div>
        
        <div class="sol-screenshots-carousel">
            <div class="sol-screenshots-track">
                <?php foreach ($solution['screenshots'] as $index => $screenshot): ?>
                    <div class="sol-screenshot-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars(is_array($screenshot) ? $screenshot['url'] : $screenshot); ?>" alt="<?php echo htmlspecialchars(is_array($screenshot) && !empty($screenshot['caption']) ? $screenshot['caption'] : 'Screenshot ' . ($index + 1)); ?>">
                        <?php if (is_array($screenshot) && !empty($screenshot['caption'])): ?>
                            <p class="sol-screenshot-caption"><?php echo htmlspecialchars($screenshot['caption']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($solution['screenshots']) > 1): ?>
                <div class="sol-screenshots-nav">
                    <button class="sol-screenshot-prev" aria-label="Previous screenshot">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <div class="sol-screenshots-dots">
                        <?php foreach ($solution['screenshots'] as $index => $screenshot): ?>
                            <button class="sol-screenshot-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" aria-label="Go to screenshot <?php echo $index + 1; ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <button class="sol-screenshot-next" aria-label="Next screenshot">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ERP CTA Banner Section -->
<?php 
$ctaBannerEnabled = $solution['cta_banner_enabled'] ?? true;
$ctaBannerImageUrl = !empty($solution['cta_banner_image_url']) ? $solution['cta_banner_image_url'] : 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1400&h=500&fit=crop';
$ctaBannerOverlayColor = $solution['cta_banner_overlay_color'] ?? 'rgba(0,0,0,0.5)';
$ctaBannerOverlayIntensity = $solution['cta_banner_overlay_intensity'] ?? 0.50;
$ctaBannerHeading1 = $solution['cta_banner_heading1'] ?? 'Streamline across 30+ modules.';
$ctaBannerHeading2 = $solution['cta_banner_heading2'] ?? 'Transform your business today!';
$ctaBannerHeadingColor = $solution['cta_banner_heading_color'] ?? '#FFFFFF';
$ctaBannerButtonText = $solution['cta_banner_button_text'] ?? 'Explore ERP Solutions';
$ctaBannerButtonLink = $solution['cta_banner_button_link'] ?? '#contact-form';
$ctaBannerButtonBgColor = $solution['cta_banner_button_bg_color'] ?? '#FFFFFF';
$ctaBannerButtonTextColor = $solution['cta_banner_button_text_color'] ?? '#2563eb';
?>
<?php if ($ctaBannerEnabled): ?>
<section class="sol-erp-cta-banner" style="
    --cta-banner-overlay-color: <?php echo htmlspecialchars($ctaBannerOverlayColor); ?>;
    --cta-banner-overlay-intensity: <?php echo htmlspecialchars($ctaBannerOverlayIntensity); ?>;
    --cta-banner-heading-color: <?php echo htmlspecialchars($ctaBannerHeadingColor); ?>;
    --cta-banner-button-bg: <?php echo htmlspecialchars($ctaBannerButtonBgColor); ?>;
    --cta-banner-button-text: <?php echo htmlspecialchars($ctaBannerButtonTextColor); ?>;
">
    <div class="sol-erp-cta-container">
        <div class="sol-erp-cta-image-wrapper">
            <img src="<?php echo htmlspecialchars($ctaBannerImageUrl); ?>" 
                 alt="<?php echo htmlspecialchars($ctaBannerHeading1); ?>" 
                 class="sol-erp-cta-image">
            <div class="sol-erp-cta-overlay"></div>
        </div>
        <div class="sol-erp-cta-content">
            <h2 class="sol-erp-cta-heading">
                <?php if (!empty($ctaBannerHeading1)): ?>
                    <span><?php echo htmlspecialchars($ctaBannerHeading1); ?></span>
                <?php endif; ?>
                <?php if (!empty($ctaBannerHeading2)): ?>
                    <span><?php echo htmlspecialchars($ctaBannerHeading2); ?></span>
                <?php endif; ?>
            </h2>
            <a href="<?php echo htmlspecialchars($ctaBannerButtonLink); ?>" class="sol-erp-cta-button">
                <?php echo htmlspecialchars($ctaBannerButtonText); ?>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Industries Gallery Section - Horizontal Scroll on Sticky -->
<section class="sol-industries-gallery" id="industries-gallery">
    <div class="sol-industries-sticky-wrapper">
        <div class="container">
            <div class="sol-industries-header">
                <h2 class="sol-industries-title">Industries We Serve</h2>
                <p class="sol-industries-subtitle">Trusted by leading organizations across diverse sectors</p>
            </div>
        </div>
        
        <div class="sol-industries-scroll-container">
            <div class="sol-industries-track">
                <!-- Industry Card 1 -->
                <div class="sol-industry-card">
                    <img src="https://images.unsplash.com/photo-1556740758-90de374c12ad?w=800&h=600&fit=crop" alt="Technology" class="sol-industry-image">
                    <div class="sol-industry-overlay"></div>
                    <div class="sol-industry-content">
                        <h3 class="sol-industry-title">Technology</h3>
                        <p class="sol-industry-description">Empowering tech companies with scalable solutions for rapid growth and innovation in the digital landscape.</p>
                        <a href="<?php echo get_base_url(); ?>/industries/technology" class="sol-industry-btn">
                            Read More
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Industry Card 2 -->
                <div class="sol-industry-card">
                    <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&h=600&fit=crop" alt="Healthcare" class="sol-industry-image">
                    <div class="sol-industry-overlay"></div>
                    <div class="sol-industry-content">
                        <h3 class="sol-industry-title">Healthcare</h3>
                        <p class="sol-industry-description">Supporting healthcare providers with secure, compliant systems that enhance patient care and operational efficiency.</p>
                        <a href="<?php echo get_base_url(); ?>/industries/healthcare" class="sol-industry-btn">
                            Read More
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Industry Card 3 -->
                <div class="sol-industry-card">
                    <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop" alt="Finance" class="sol-industry-image">
                    <div class="sol-industry-overlay"></div>
                    <div class="sol-industry-content">
                        <h3 class="sol-industry-title">Finance</h3>
                        <p class="sol-industry-description">Delivering robust financial solutions with enterprise-grade security and real-time transaction processing capabilities.</p>
                        <a href="<?php echo get_base_url(); ?>/industries/finance" class="sol-industry-btn">
                            Read More
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Industry Card 4 -->
                <div class="sol-industry-card">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=600&fit=crop" alt="Retail" class="sol-industry-image">
                    <div class="sol-industry-overlay"></div>
                    <div class="sol-industry-content">
                        <h3 class="sol-industry-title">Retail</h3>
                        <p class="sol-industry-description">Transforming retail experiences with seamless omnichannel solutions and intelligent inventory management systems.</p>
                        <a href="<?php echo get_base_url(); ?>/industries/retail" class="sol-industry-btn">
                            Read More
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Industry Card 5 -->
                <div class="sol-industry-card">
                    <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&h=600&fit=crop" alt="Education" class="sol-industry-image">
                    <div class="sol-industry-overlay"></div>
                    <div class="sol-industry-content">
                        <h3 class="sol-industry-title">Education</h3>
                        <p class="sol-industry-description">Enabling educational institutions with modern learning platforms and comprehensive student management tools.</p>
                        <a href="<?php echo get_base_url(); ?>/industries/education" class="sol-industry-btn">
                            Read More
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Industry Card 6 -->
                <div class="sol-industry-card">
                    <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop" alt="Manufacturing" class="sol-industry-image">
                    <div class="sol-industry-overlay"></div>
                    <div class="sol-industry-content">
                        <h3 class="sol-industry-title">Manufacturing</h3>
                        <p class="sol-industry-description">Optimizing production workflows with IoT-enabled systems and predictive maintenance for maximum efficiency.</p>
                        <a href="<?php echo get_base_url(); ?>/industries/manufacturing" class="sol-industry-btn">
                            Read More
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Industry Card 7 -->
                <div class="sol-industry-card">
                    <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&h=600&fit=crop" alt="Logistics" class="sol-industry-image">
                    <div class="sol-industry-overlay"></div>
                    <div class="sol-industry-content">
                        <h3 class="sol-industry-title">Logistics</h3>
                        <p class="sol-industry-description">Streamlining supply chain operations with real-time tracking and automated route optimization solutions.</p>
                        <a href="<?php echo get_base_url(); ?>/industries/logistics" class="sol-industry-btn">
                            Read More
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Industry Card 8 -->
                <div class="sol-industry-card">
                    <img src="https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=800&h=600&fit=crop" alt="Hospitality" class="sol-industry-image">
                    <div class="sol-industry-overlay"></div>
                    <div class="sol-industry-content">
                        <h3 class="sol-industry-title">Hospitality</h3>
                        <p class="sol-industry-description">Enhancing guest experiences with integrated booking systems and personalized service management platforms.</p>
                        <a href="<?php echo get_base_url(); ?>/industries/hospitality" class="sol-industry-btn">
                            Read More
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Showcase Section -->
<?php if (!empty($testimonials)): ?>
<?php
$testimonials_config = [
    'testimonials' => $testimonials,
    'title' => 'What Our Customers Say',
    'subtitle' => 'Trusted by leading businesses who have transformed their operations with our solutions',
    'theme' => 'light',
    'max_items' => 6,
    'accent_color' => $colorTheme
];
include __DIR__ . '/../templates/components/testimonials-showcase.php';
?>
<?php endif; ?>

<!-- FAQs Section - Professional Design (Dark Mode) -->
<?php if (!empty($solution['faqs'])): ?>
<section class="sol-faqs sol-faqs-light" style="--theme-color: <?php echo htmlspecialchars($colorTheme); ?>;">
    <div class="sol-faqs-bg">
        <div class="sol-faqs-gradient"></div>
        <div class="sol-faqs-pattern"></div>
        <div class="sol-faqs-orb sol-faqs-orb-1"></div>
        <div class="sol-faqs-orb sol-faqs-orb-2"></div>
    </div>
    <div class="container">
        <div class="sol-faqs-layout">
            <!-- Left Side - Header & Decoration -->
            <div class="sol-faqs-sidebar">
                <div class="sol-faqs-sidebar-content">
                    <h2 class="sol-faqs-title">Frequently Asked<br>Questions</h2>
                    <p class="sol-faqs-subtitle">Everything you need to know about our solution. Can't find what you're looking for? Feel free to contact us.</p>
                    <a href="#contact-form" class="sol-faqs-contact-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Contact Support
                    </a>
                </div>
                <div class="sol-faqs-decoration">
                    <div class="sol-faqs-decoration-circle sol-faqs-decoration-circle-1"></div>
                    <div class="sol-faqs-decoration-circle sol-faqs-decoration-circle-2"></div>
                    <div class="sol-faqs-decoration-circle sol-faqs-decoration-circle-3"></div>
                </div>
            </div>
            
            <!-- Right Side - FAQ Items -->
            <div class="sol-faqs-content">
                <div class="sol-faqs-list">
                    <?php foreach ($solution['faqs'] as $index => $faq): ?>
                        <div class="sol-faq-item" data-faq-index="<?php echo $index; ?>">
                            <button class="sol-faq-question" aria-expanded="false" aria-controls="faq-<?php echo $index; ?>">
                                <span class="sol-faq-number"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                                <span class="sol-faq-question-text"><?php echo htmlspecialchars($faq['question']); ?></span>
                                <span class="sol-faq-toggle">
                                    <svg class="sol-faq-icon-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"/>
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                    <svg class="sol-faq-icon-minus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                </span>
                            </button>
                            <div class="sol-faq-answer" id="faq-<?php echo $index; ?>">
                                <div class="sol-faq-answer-inner">
                                    <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- FAQ Footer -->
                <div class="sol-faqs-footer">
                    <div class="sol-faqs-footer-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="sol-faqs-footer-text">
                        <strong>Still have questions?</strong>
                        <span>Our team is here to help you 24/7</span>
                    </div>
                    <a href="#contact-form" class="sol-faqs-footer-btn">Get in Touch</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Related Solutions -->
<?php if (!empty($relatedSolutions)): ?>
<section class="sol-related">
    <div class="container">
        <div class="sol-section-header">
            <h2 class="sol-section-title">Related Solutions</h2>
            <p class="sol-section-subtitle">Explore other solutions that complement this offering</p>
        </div>
        
        <div class="sol-related-grid">
            <?php foreach ($relatedSolutions as $relatedSolution): ?>
                <a href="<?php echo get_base_url(); ?>/solution/<?php echo urlencode($relatedSolution['slug']); ?>" class="sol-related-card">
                    <?php if (!empty($relatedSolution['icon_image'])): ?>
                        <div class="sol-related-icon">
                            <img src="<?php echo htmlspecialchars($relatedSolution['icon_image']); ?>" alt="">
                        </div>
                    <?php endif; ?>
                    <h3 class="sol-related-title"><?php echo htmlspecialchars($relatedSolution['name']); ?></h3>
                    <p class="sol-related-desc"><?php echo htmlspecialchars($relatedSolution['description']); ?></p>
                    <span class="sol-related-link">
                        Learn more
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- CTA Section -->
<div id="contact-form">
<?php
$cta_title = "Ready to Get Started with " . htmlspecialchars($solution['name']) . "?";
$cta_subtitle = !empty($solution['pricing_note']) ? htmlspecialchars($solution['pricing_note']) : "Contact us today to learn how this solution can transform your business operations";
$cta_source = "solution-" . $slug;
include __DIR__ . '/../templates/cta-form.php';
?>
</div>


<?php
// Helper functions for icons
function get_key_benefits_card_icon($icon) {
    $icons = [
        'speed' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
        'security' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
        'money' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>',
        'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
        'check' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'globe' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>',
        'chart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
        'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    ];
    return $icons[$icon] ?? $icons['check'];
}


?>


<style>
/* ============================================
   Solution Detail Page - Razorpay Style
   ============================================ */

/* Hero Section */
.sol-hero {
    position: relative;
    height: 90vh;
    padding: 80px 0 100px;
    overflow: hidden;
    background: var(--hero-bg-color, #0a1628);
    color: #fff;
    display: flex;
    flex-direction: column;
}

.sol-hero > .container {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.sol-hero-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.sol-hero-gradient {
    position: absolute;
    inset: 0;
    background: 
        linear-gradient(90deg, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0.15) 30%, transparent 60%),
        radial-gradient(ellipse 50% 35% at 100% 100%, var(--hero-gradient-color, var(--theme-color, #667eea)) 0%, transparent 45%);
    opacity: var(--hero-gradient-opacity, 0.5);
}

.sol-hero-pattern {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: var(--hero-pattern-opacity, 0.03);
}

.sol-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 40px;
    font-size: 14px;
    position: relative;
    z-index: 1;
}

.sol-breadcrumb a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color 0.2s;
}

.sol-breadcrumb a:hover {
    color: #fff;
}

.sol-breadcrumb svg {
    width: 16px;
    height: 16px;
    fill: none;
    stroke: rgba(255, 255, 255, 0.4);
    stroke-width: 2;
}

.sol-breadcrumb span {
    color: #fff;
}

.sol-hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    position: relative;
    z-index: 1;
}

.sol-hero-badge {
    display: inline-block;
    padding: 6px 16px;
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 16px;
}

.sol-hero-tagline {
    font-size: 14px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: var(--theme-color, #667eea);
    margin: 0 0 12px;
}

.sol-hero-title {
    font-size: clamp(36px, 5vw, 56px);
    font-weight: 700;
    line-height: 1.1;
    margin: 0 0 20px;
    color: var(--hero-title-color, #fff);
    /* Single line title - max 24 chars enforced by admin */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sol-hero-subtitle {
    font-size: 18px;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.85);
    margin: 0 0 32px;
    max-width: 540px;
}

.sol-hero-actions {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

/* Glassy Button Styles */
.sol-btn-glassy {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    font-weight: 600;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.sol-btn-primary {
    background: var(--hero-primary-btn-bg, rgba(255, 255, 255, 0.15));
    color: var(--hero-primary-btn-text, #fff);
    border: 1px solid var(--hero-primary-btn-border, rgba(255, 255, 255, 0.3));
}

.sol-hero-glassy-buttons .sol-btn-primary {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.sol-btn-primary:hover {
    transform: translateY(-2px);
    background: var(--hero-primary-btn-bg, rgba(255, 255, 255, 0.25));
    color: var(--hero-primary-btn-text-hover, var(--hero-primary-btn-text, #fff));
    box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.sol-btn-primary svg {
    width: 20px;
    height: 20px;
}

.sol-btn-secondary {
    background: var(--hero-secondary-btn-bg, rgba(255, 255, 255, 0.1));
    color: var(--hero-secondary-btn-text, #fff);
    border: 1px solid var(--hero-secondary-btn-border, rgba(255, 255, 255, 0.2));
}

.sol-hero-glassy-buttons .sol-btn-secondary {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.sol-btn-secondary:hover {
    transform: translateY(-2px);
    background: var(--hero-secondary-btn-bg, rgba(255, 255, 255, 0.2));
    color: var(--hero-secondary-btn-text-hover, var(--hero-secondary-btn-text, #fff));
    border-color: var(--hero-secondary-btn-border, rgba(255, 255, 255, 0.3));
}

.sol-btn-secondary svg {
    width: 18px;
    height: 18px;
}

.sol-hero-visual {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Media Container with Glassy Effect */
.sol-hero-media-wrapper,
.sol-hero-image-wrapper {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
}

.sol-media-glassy {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 16px;
    box-shadow: 
        0 30px 60px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.sol-hero-media,
.sol-hero-image {
    display: block;
    border-radius: 12px;
}

/* Square container that holds any aspect ratio image */
.sol-hero-media-wrapper {
    width: min(450px, 45vh);
    height: min(450px, 45vh);
    display: flex;
    align-items: center;
    justify-content: center;
}

.sol-hero-media-wrapper .sol-hero-media {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
}

/* Video specific styles */
.sol-hero-media-wrapper video {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
}

.sol-hero-icon-large {
    width: 200px;
    height: 200px;
    padding: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 32px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.sol-hero-icon-large.sol-media-glassy {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.sol-hero-icon-large img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

/* White filter for icon images when used as fallback */
.sol-hero-icon-white {
    filter: brightness(0) invert(1);
    opacity: 0.9;
}

.sol-hero-icon-default {
    width: 200px;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 32px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.sol-hero-icon-default.sol-media-glassy {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.sol-hero-default-icon {
    width: 80px;
    height: 80px;
    stroke: rgba(255, 255, 255, 0.8);
}




/* Key Benefits Section (Razorpay-style) */
.sol-key-benefits-section {
    background: var(--key-benefits-bg-color, #0a1628);
    min-height: 80vh;
    display: flex;
    align-items: center;
    overflow: hidden;
}

.sol-key-benefits-section > .container {
    width: 100%;
}

.sol-key-benefits-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: flex-start;
}

.sol-key-benefits-text {
    max-width: 520px;
}

.sol-key-benefits-heading {
    font-size: clamp(32px, 4vw, 48px);
    font-weight: 300;
    line-height: 1.15;
    margin: 0 0 20px;
    color: var(--key-benefits-heading-color, #FFFFFF);
    min-width: 420px;
}

.sol-key-benefits-heading-line {
    display: block;
    white-space: nowrap;
}

.sol-key-benefits-subheading {
    font-size: 16px;
    line-height: 1.6;
    color: var(--key-benefits-subheading-color, rgba(255,255,255,0.7));
    margin: 0;
    max-width: 420px;
}

.sol-key-benefits-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.sol-key-benefits-card {
    position: relative;
    background: var(--key-benefits-card-bg, rgba(255,255,255,0.08));
    border: 1px solid var(--key-benefits-card-border, rgba(255,255,255,0.1));
    border-radius: 16px;
    padding: 24px;
    height: 170px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.sol-key-benefits-card:hover {
    background: var(--key-benefits-card-hover-bg, #2563eb);
    border-color: var(--key-benefits-card-hover-bg, #2563eb);
}

.sol-key-benefits-card-icon {
    width: 28px;
    height: 28px;
    margin-bottom: 12px;
    color: var(--key-benefits-card-icon, rgba(255,255,255,0.6));
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}

.sol-key-benefits-card:hover .sol-key-benefits-card-icon {
    opacity: 0;
    height: 0;
    margin-bottom: 0;
    overflow: hidden;
}

.sol-key-benefits-card-icon svg {
    width: 100%;
    height: 100%;
}

.sol-key-benefits-card-title {
    font-size: 17px;
    font-weight: 600;
    color: var(--key-benefits-card-text, #FFFFFF);
    margin: 0 0 8px;
    line-height: 1.3;
    transition: all 0.3s ease;
}

.sol-key-benefits-card-desc {
    font-size: 13px;
    line-height: 1.5;
    color: var(--key-benefits-card-text, #FFFFFF);
    margin: 0;
    opacity: 0.4;
    max-height: 40px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.sol-key-benefits-card:hover .sol-key-benefits-card-desc {
    opacity: 0.9;
    max-height: 100px;
    margin-top: 4px;
    -webkit-line-clamp: unset;
}

/* Responsive styles for key benefits section */
@media (max-width: 992px) {
    .sol-key-benefits-section {
        min-height: auto;
        padding: 60px 0;
    }
    
    .sol-key-benefits-content {
        grid-template-columns: 1fr;
        gap: 40px;
        text-align: center;
    }
    
    .sol-key-benefits-text {
        max-width: 100%;
    }
    
    .sol-key-benefits-subheading {
        max-width: 100%;
        margin: 0 auto 24px;
    }
    
    .sol-key-benefits-cards {
        max-width: 500px;
        margin: 0 auto;
    }
}

@media (max-width: 576px) {
    .sol-key-benefits-cards {
        grid-template-columns: 1fr;
    }
    
    .sol-key-benefits-card {
        height: auto;
        min-height: 100px;
    }
}


/* ============================================
   Feature Showcase - Stacking Cards Section
   ============================================ */
.sol-feature-showcase {
    background: var(--feature-showcase-bg-color, #fff);
    padding: 80px 0 0;
    position: relative;
}

/* Section Header */
.sol-showcase-header {
    text-align: center;
    margin-bottom: 40px;
}

.sol-showcase-title {
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 300;
    color: var(--feature-showcase-title-color, #111827);
    margin: 0 0 16px;
    letter-spacing: -0.5px;
    max-width: 48ch;
    margin-left: auto;
    margin-right: auto;
}

.sol-showcase-subtitle {
    font-size: 16px;
    color: var(--feature-showcase-subtitle-color, #6b7280);
    margin: 0;
    max-width: 240ch;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

/* Anchor Navigation Strip */
.sol-showcase-nav {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    position: sticky;
    top: 64px; /* Account for main header height */
    z-index: 50; /* Lower than header z-index (100) so it slides under */
    transition: box-shadow 0.3s ease;
}

.sol-showcase-nav.is-sticky {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

@media (max-width: 768px) {
    .sol-showcase-nav {
        top: 56px; /* Smaller header on mobile */
    }
}

.sol-showcase-nav-inner {
    display: flex;
    justify-content: center;
    gap: 8px;
    padding: 0 20px;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.sol-showcase-nav-inner::-webkit-scrollbar {
    display: none;
}

.sol-showcase-nav-link {
    display: inline-block;
    padding: 16px 20px;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    text-decoration: none;
    white-space: nowrap;
    position: relative;
    transition: color 0.2s ease;
}

.sol-showcase-nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: var(--theme-color, #2563eb);
    transition: width 0.3s ease;
}

.sol-showcase-nav-link:hover {
    color: #111827;
}

.sol-showcase-nav-link.active {
    color: #111827;
    font-weight: 600;
}

.sol-showcase-nav-link.active::after {
    width: calc(100% - 40px);
}

/* Stacking Cards Container */
.sol-showcase-cards-wrapper {
    padding: 60px 0 100px;
    position: relative;
}

.sol-showcase-cards {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0;
    /* Center cards with 2:1 aspect ratio (width = 2 * height) */
    max-width: calc(70vh * 2);
    margin: 0 auto;
}

/* Individual Card */
.sol-showcase-card {
    position: sticky;
    top: 140px; /* Account for header + nav */
    background: var(--feature-showcase-card-bg, #ffffff);
    border-radius: 24px;
    margin-bottom: 40px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--feature-showcase-card-border, #e5e7eb);
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
                box-shadow 0.4s ease;
    will-change: transform;
    /* Fixed height at 70vh */
    height: 70vh;
    min-height: 500px;
    max-height: 700px;
}

.sol-showcase-card:nth-child(1) { top: 140px; z-index: 1; }
.sol-showcase-card:nth-child(2) { top: 140px; z-index: 2; }
.sol-showcase-card:nth-child(3) { top: 140px; z-index: 3; }
.sol-showcase-card:nth-child(4) { top: 140px; z-index: 4; }
.sol-showcase-card:nth-child(5) { top: 140px; z-index: 5; }
.sol-showcase-card:nth-child(6) { top: 140px; z-index: 6; }

.sol-showcase-card.is-stacked {
    transform: scale(0.98);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
}

.sol-showcase-card-inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 48px;
    padding: 40px;
    align-items: center;
    height: 100%;
}

/* Media Section */
.sol-showcase-media {
    display: flex;
    justify-content: center;
    align-items: center;
}

.sol-showcase-media-frame {
    background: linear-gradient(145deg, #e8f4f8 0%, #f0f7f4 100%);
    border-radius: 16px;
    width: 100%;
    aspect-ratio: 1/1;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    padding: 24px;
}

.sol-showcase-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 12px;
}



/* Content Section */
.sol-showcase-content {
    padding: 20px 0;
}

.sol-showcase-badge {
    display: inline-block;
    padding: 6px 14px;
    background: var(--feature-showcase-card-badge-bg, #eff6ff);
    color: var(--feature-showcase-card-badge-text, #2563eb);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 4px;
    margin-bottom: 16px;
}

.sol-showcase-heading {
    font-size: clamp(22px, 3vw, 32px);
    font-weight: 600;
    color: var(--feature-showcase-card-heading, #111827);
    line-height: 1.3;
    margin: 0 0 24px;
}

.sol-showcase-features {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.sol-showcase-features li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 15px;
    color: var(--feature-showcase-card-text, #4b5563);
    line-height: 1.5;
}

.sol-showcase-features li svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    stroke: var(--feature-showcase-card-icon, #10b981);
    margin-top: 2px;
}

/* More Features Button */
.sol-showcase-more {
    display: flex;
    justify-content: center;
    padding: 40px 0;
    margin-top: 590px;
    position: relative;
    z-index: 10;
}

.sol-showcase-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 32px;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
}

.sol-showcase-more-btn:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
    color: #fff;
}

.sol-showcase-more-btn svg {
    width: 20px;
    height: 20px;
    transition: transform 0.3s ease;
}

.sol-showcase-more-btn:hover svg {
    transform: translateX(4px);
}

/* Responsive Styles for Feature Showcase */
@media (max-width: 992px) {
    .sol-showcase-cards {
        max-width: 100%;
    }
    
    .sol-showcase-card {
        height: auto;
        min-height: auto;
        max-height: none;
    }
    
    .sol-showcase-card-inner {
        grid-template-columns: 1fr;
        gap: 32px;
        padding: 32px;
        height: auto;
    }
    
    .sol-showcase-media {
        order: -1;
    }
    
    .sol-showcase-media-frame {
        max-width: 100%;
        padding: 20px;
    }
    
    .sol-showcase-nav-link {
        padding: 14px 16px;
        font-size: 13px;
    }
}

@media (max-width: 768px) {
    .sol-feature-showcase {
        padding: 60px 0 0;
    }
    
    .sol-showcase-header {
        margin-bottom: 32px;
    }
    
    .sol-showcase-nav-inner {
        justify-content: flex-start;
        gap: 4px;
    }
    
    .sol-showcase-cards-wrapper {
        padding: 40px 0 60px;
    }
    
    .sol-showcase-card {
        border-radius: 16px;
        margin-bottom: 24px;
    }
    
    .sol-showcase-card:nth-child(n) {
        top: 120px;
    }
    
    .sol-showcase-card-inner {
        padding: 24px;
        gap: 24px;
    }
    
    .sol-showcase-heading {
        font-size: 22px;
    }
    
    .sol-showcase-features li {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .sol-showcase-card-inner {
        padding: 20px;
    }
    
    .sol-showcase-media-frame {
        aspect-ratio: 1/1;
        padding: 16px;
    }
    
    .sol-demo-code {
        font-size: 11px;
        padding: 16px;
    }
}


/* Stats Bar */
.sol-stats-bar {
    background: #fff;
    padding: 40px 0;
    border-bottom: 1px solid #e5e7eb;
}

.sol-stats-grid {
    display: flex;
    justify-content: center;
    gap: 80px;
    flex-wrap: wrap;
}

.sol-stat-item {
    text-align: center;
}

.sol-stat-value {
    display: block;
    font-size: 36px;
    font-weight: 700;
    color: var(--theme-color, #667eea);
    line-height: 1;
    margin-bottom: 8px;
}

.sol-stat-label {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

/* Highlight Cards */
.sol-highlights {
    padding: 80px 0;
    background: #f9fafb;
}

.sol-highlights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.sol-highlight-card {
    display: flex;
    gap: 20px;
    padding: 28px;
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s;
}

.sol-highlight-card:hover {
    border-color: var(--theme-color, #667eea);
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.1);
    transform: translateY(-4px);
}

.sol-highlight-icon {
    flex-shrink: 0;
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    border-radius: 12px;
}

.sol-highlight-icon svg {
    width: 28px;
    height: 28px;
    stroke: #fff;
}

.sol-highlight-content {
    flex: 1;
}

.sol-highlight-value {
    display: block;
    font-size: 24px;
    font-weight: 700;
    color: var(--theme-color, #667eea);
    margin-bottom: 4px;
}

.sol-highlight-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 8px;
}

.sol-highlight-desc {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
    line-height: 1.5;
}

/* Section Headers */
.sol-section-header {
    text-align: center;
    margin-bottom: 60px;
}

.sol-section-title {
    font-size: 36px;
    font-weight: 700;
    color: #111827;
    margin: 0 0 16px;
}

.sol-section-subtitle {
    font-size: 18px;
    color: #6b7280;
    margin: 0;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

/* Workflow Section */
.sol-workflow {
    padding: 100px 0;
    background: #fff;
}

.sol-workflow-steps {
    display: flex;
    justify-content: center;
    gap: 0;
    position: relative;
}

.sol-workflow-step {
    flex: 1;
    max-width: 300px;
    text-align: center;
    position: relative;
    padding: 0 20px;
}

.sol-workflow-number {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    color: #fff;
    font-size: 20px;
    font-weight: 700;
    border-radius: 50%;
    margin: 0 auto 24px;
    position: relative;
    z-index: 2;
}

.sol-workflow-connector {
    position: absolute;
    top: 24px;
    left: calc(50% + 24px);
    right: calc(-50% + 24px);
    height: 2px;
    background: linear-gradient(90deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    z-index: 1;
}

.sol-workflow-step:last-child .sol-workflow-connector {
    display: none;
}

.sol-workflow-icon {
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 16px;
    margin: 0 auto 20px;
}

.sol-workflow-icon svg {
    width: 32px;
    height: 32px;
    stroke: var(--theme-color, #667eea);
}

.sol-workflow-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 12px;
}

.sol-workflow-desc {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
    line-height: 1.6;
}

/* Benefits Section */
.sol-benefits {
    padding: 100px 0;
    background: #f9fafb;
}

.sol-benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 32px;
}

.sol-benefit-card {
    padding: 32px;
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s;
}

.sol-benefit-card:hover {
    border-color: var(--theme-color, #667eea);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
}

.sol-benefit-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 12px;
    margin-bottom: 20px;
}

.sol-benefit-icon svg {
    width: 24px;
    height: 24px;
    stroke: var(--theme-color, #667eea);
}

.sol-benefit-title {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 12px;
}

.sol-benefit-desc {
    font-size: 15px;
    color: #6b7280;
    margin: 0;
    line-height: 1.6;
}


/* Features Section */
.sol-features {
    padding: 100px 0;
    background: #fff;
}

.sol-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 28px;
}

.sol-feature-card {
    position: relative;
    padding: 32px;
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s;
}

.sol-feature-card:hover {
    border-color: var(--theme-color, #667eea);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
    transform: translateY(-4px);
}

.sol-feature-highlighted {
    border-color: var(--theme-color, #667eea);
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
}

.sol-feature-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    padding: 4px 12px;
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 50px;
}

.sol-feature-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    border-radius: 14px;
    margin-bottom: 24px;
}

.sol-feature-icon svg {
    width: 28px;
    height: 28px;
    stroke: #fff;
}

.sol-feature-title {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 12px;
}

.sol-feature-desc {
    font-size: 15px;
    color: #6b7280;
    margin: 0 0 20px;
    line-height: 1.6;
}

.sol-feature-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--theme-color, #667eea);
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: gap 0.2s;
}

.sol-feature-link:hover {
    gap: 10px;
}

.sol-feature-link svg {
    width: 16px;
    height: 16px;
}

/* Use Cases Section */
.sol-use-cases {
    padding: 100px 0;
    background: linear-gradient(135deg, #0a1628 0%, #1a365d 100%);
    color: #fff;
}

.sol-use-cases .sol-section-title {
    color: #fff;
}

.sol-use-cases .sol-section-subtitle {
    color: rgba(255, 255, 255, 0.7);
}

.sol-use-cases-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.sol-use-case-card {
    padding: 32px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    transition: all 0.3s;
}

.sol-use-case-card:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-4px);
}

.sol-use-case-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    border-radius: 14px;
    margin-bottom: 20px;
}

.sol-use-case-icon svg {
    width: 28px;
    height: 28px;
    stroke: #fff;
}

.sol-use-case-title {
    font-size: 20px;
    font-weight: 600;
    color: #fff;
    margin: 0 0 12px;
}

.sol-use-case-desc {
    font-size: 15px;
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    line-height: 1.6;
}

/* Integrations Section */
.sol-integrations {
    padding: 100px 0;
    background: #f9fafb;
}

.sol-integrations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 24px;
}

.sol-integration-card {
    padding: 32px 24px;
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    text-align: center;
    transition: all 0.3s;
}

.sol-integration-card:hover {
    border-color: var(--theme-color, #667eea);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.sol-integration-logo {
    width: 64px;
    height: 64px;
    object-fit: contain;
    margin: 0 auto 16px;
    display: block;
}

.sol-integration-placeholder {
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    border-radius: 12px;
    margin: 0 auto 16px;
}

.sol-integration-placeholder span {
    color: #fff;
    font-size: 20px;
    font-weight: 700;
}

.sol-integration-name {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 8px;
}

.sol-integration-desc {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
}


/* Screenshots Section */
.sol-screenshots {
    padding: 100px 0;
    background: #fff;
}

.sol-screenshots-carousel {
    max-width: 900px;
    margin: 0 auto;
}

.sol-screenshots-track {
    position: relative;
}

.sol-screenshot-item {
    display: none;
}

.sol-screenshot-item.active {
    display: block;
}

.sol-screenshot-item img {
    width: 100%;
    height: auto;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.sol-screenshot-caption {
    text-align: center;
    font-size: 14px;
    color: #6b7280;
    margin: 20px 0 0;
}

.sol-screenshots-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-top: 32px;
}

.sol-screenshot-prev,
.sol-screenshot-next {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s;
}

.sol-screenshot-prev:hover,
.sol-screenshot-next:hover {
    background: var(--theme-color, #667eea);
}

.sol-screenshot-prev:hover svg,
.sol-screenshot-next:hover svg {
    stroke: #fff;
}

.sol-screenshot-prev svg,
.sol-screenshot-next svg {
    width: 20px;
    height: 20px;
    stroke: #374151;
}

.sol-screenshots-dots {
    display: flex;
    gap: 8px;
}

.sol-screenshot-dot {
    width: 10px;
    height: 10px;
    background: #d1d5db;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s;
}

.sol-screenshot-dot.active,
.sol-screenshot-dot:hover {
    background: var(--theme-color, #667eea);
}

/* FAQs Section - Professional Design with Dark/Light Theme Support */
.sol-faqs {
    position: relative;
    padding: 120px 0;
    overflow: hidden;
}

/* Dark Theme (Default) */
.sol-faqs.sol-faqs-dark {
    background: linear-gradient(135deg, #0a1628 0%, #1a2332 100%);
    color: #ffffff;
}

/* Light Theme */
.sol-faqs.sol-faqs-light {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    color: #1e293b;
}

/* Background Effects */
.sol-faqs-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
    overflow: hidden;
}

.sol-faqs-gradient {
    position: absolute;
    top: -50%;
    right: -20%;
    width: 80%;
    height: 100%;
}

.sol-faqs-dark .sol-faqs-gradient {
    background: radial-gradient(ellipse at center, rgba(102, 126, 234, 0.15) 0%, transparent 70%);
}

.sol-faqs-light .sol-faqs-gradient {
    background: radial-gradient(ellipse at center, rgba(102, 126, 234, 0.08) 0%, transparent 70%);
}

.sol-faqs-pattern {
    position: absolute;
    inset: 0;
    background-size: 32px 32px;
}

.sol-faqs-dark .sol-faqs-pattern {
    background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.03) 1px, transparent 0);
}

.sol-faqs-light .sol-faqs-pattern {
    background-image: radial-gradient(circle at 1px 1px, rgba(0, 0, 0, 0.03) 1px, transparent 0);
}

/* Floating Orbs */
.sol-faqs-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
}

.sol-faqs-dark .sol-faqs-orb-1 {
    width: 500px;
    height: 500px;
    background: var(--theme-color, #667eea);
    top: -200px;
    right: -150px;
    opacity: 0.12;
}

.sol-faqs-dark .sol-faqs-orb-2 {
    width: 350px;
    height: 350px;
    background: #8b5cf6;
    bottom: -150px;
    left: -100px;
    opacity: 0.08;
}

.sol-faqs-light .sol-faqs-orb-1 {
    width: 500px;
    height: 500px;
    background: var(--theme-color, #667eea);
    top: -200px;
    right: -150px;
    opacity: 0.06;
}

.sol-faqs-light .sol-faqs-orb-2 {
    width: 350px;
    height: 350px;
    background: #8b5cf6;
    bottom: -150px;
    left: -100px;
    opacity: 0.04;
}

.sol-faqs-layout {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 80px;
    align-items: start;
    position: relative;
    z-index: 1;
}

/* Sidebar */
.sol-faqs-sidebar {
    position: sticky;
    top: 120px;
}

.sol-faqs-sidebar-content {
    position: relative;
    z-index: 2;
}

.sol-faqs-title {
    font-size: clamp(2.5rem, 5vw, 3.5rem);
    font-weight: 400;
    line-height: 1.2;
    margin: 0 0 20px;
    letter-spacing: -0.02em;
}

.sol-faqs-dark .sol-faqs-title {
    color: #ffffff;
}

.sol-faqs-light .sol-faqs-title {
    color: #0f172a;
}

.sol-faqs-subtitle {
    font-size: 16px;
    line-height: 1.7;
    margin: 0 0 32px;
}

.sol-faqs-dark .sol-faqs-subtitle {
    color: rgba(255, 255, 255, 0.6);
}

.sol-faqs-light .sol-faqs-subtitle {
    color: #64748b;
}

.sol-faqs-contact-btn {
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

.sol-faqs-dark .sol-faqs-contact-btn {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
}

.sol-faqs-dark .sol-faqs-contact-btn:hover {
    background: var(--theme-color, #667eea);
    border-color: var(--theme-color, #667eea);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.35);
}

.sol-faqs-light .sol-faqs-contact-btn {
    background: #fff;
    color: #0f172a;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.sol-faqs-light .sol-faqs-contact-btn:hover {
    background: var(--theme-color, #667eea);
    color: #fff;
    border-color: var(--theme-color, #667eea);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);
}

.sol-faqs-contact-btn svg {
    width: 18px;
    height: 18px;
}

/* Decoration Circles */
.sol-faqs-decoration {
    position: absolute;
    bottom: -60px;
    left: -40px;
    width: 200px;
    height: 200px;
}

.sol-faqs-decoration-circle {
    position: absolute;
    border-radius: 50%;
    border: 1px solid;
}

.sol-faqs-dark .sol-faqs-decoration-circle {
    border-color: rgba(255, 255, 255, 0.1);
}

.sol-faqs-light .sol-faqs-decoration-circle {
    border-color: rgba(102, 126, 234, 0.15);
}

.sol-faqs-decoration-circle-1 {
    width: 200px;
    height: 200px;
    animation: faqPulse 4s ease-in-out infinite;
}

.sol-faqs-decoration-circle-2 {
    width: 150px;
    height: 150px;
    top: 25px;
    left: 25px;
    animation: faqPulse 4s ease-in-out infinite 0.5s;
}

.sol-faqs-decoration-circle-3 {
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
.sol-faqs-content {
    position: relative;
}

.sol-faqs-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.sol-faq-item {
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
}

/* Dark Theme - FAQ Items */
.sol-faqs-dark .sol-faq-item {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.sol-faqs-dark .sol-faq-item:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.sol-faqs-dark .sol-faq-item[data-expanded="true"] {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--theme-color, #667eea);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.2);
}

/* Light Theme - FAQ Items */
.sol-faqs-light .sol-faq-item {
    background: #fff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.sol-faqs-light .sol-faq-item:hover {
    border-color: rgba(102, 126, 234, 0.4);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
}

.sol-faqs-light .sol-faq-item[data-expanded="true"] {
    border-color: var(--theme-color, #667eea);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.12);
}

.sol-faq-question {
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

.sol-faqs-dark .sol-faq-question {
    color: #ffffff;
}

.sol-faqs-dark .sol-faq-question:hover {
    background: rgba(255, 255, 255, 0.05);
}

.sol-faqs-light .sol-faq-question {
    color: #0f172a;
}

.sol-faqs-light .sol-faq-question:hover {
    background: #f8fafc;
}

.sol-faq-number {
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

.sol-faqs-dark .sol-faq-number {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.6);
}

.sol-faqs-light .sol-faq-number {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #64748b;
}

.sol-faq-item[data-expanded="true"] .sol-faq-number,
.sol-faq-question[aria-expanded="true"] .sol-faq-number {
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    color: #fff;
}

.sol-faq-question-text {
    flex: 1;
    line-height: 1.5;
}

.sol-faq-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.sol-faqs-dark .sol-faq-toggle {
    background: rgba(255, 255, 255, 0.1);
}

.sol-faqs-dark .sol-faq-toggle svg {
    stroke: rgba(255, 255, 255, 0.6);
}

.sol-faqs-light .sol-faq-toggle {
    background: #f1f5f9;
}

.sol-faqs-light .sol-faq-toggle svg {
    stroke: #64748b;
}

.sol-faq-toggle svg {
    width: 16px;
    height: 16px;
    transition: all 0.3s ease;
}

.sol-faq-icon-minus {
    display: none;
}

.sol-faq-question[aria-expanded="true"] .sol-faq-toggle {
    background: var(--theme-color, #667eea);
}

.sol-faq-question[aria-expanded="true"] .sol-faq-toggle svg {
    stroke: #fff;
}

.sol-faq-question[aria-expanded="true"] .sol-faq-icon-plus {
    display: none;
}

.sol-faq-question[aria-expanded="true"] .sol-faq-icon-minus {
    display: block;
}

.sol-faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, padding 0.3s ease;
}

.sol-faq-question[aria-expanded="true"] + .sol-faq-answer {
    max-height: 500px;
}

.sol-faq-answer-inner {
    padding: 0 28px 28px 80px;
}

.sol-faq-answer p {
    font-size: 15px;
    line-height: 1.8;
    margin: 0;
}

.sol-faqs-dark .sol-faq-answer p {
    color: rgba(255, 255, 255, 0.7);
}

.sol-faqs-light .sol-faq-answer p {
    color: #64748b;
}

/* FAQ Footer */
.sol-faqs-footer {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-top: 40px;
    padding: 28px 32px;
    border-radius: 20px;
    transition: all 0.3s ease;
}

.sol-faqs-dark .sol-faqs-footer {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.sol-faqs-light .sol-faqs-footer {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.2);
}

.sol-faqs-footer-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    border-radius: 14px;
    flex-shrink: 0;
}

.sol-faqs-footer-icon svg {
    width: 26px;
    height: 26px;
    stroke: #fff;
}

.sol-faqs-footer-text {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sol-faqs-footer-text strong {
    font-size: 16px;
    font-weight: 700;
    color: #fff;
}

.sol-faqs-footer-text span {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.6);
}

.sol-faqs-footer-btn {
    padding: 14px 28px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.sol-faqs-dark .sol-faqs-footer-btn {
    background: var(--theme-color, #667eea);
    color: #fff;
}

.sol-faqs-dark .sol-faqs-footer-btn:hover {
    background: #fff;
    color: #0f172a;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(255, 255, 255, 0.2);
}

.sol-faqs-light .sol-faqs-footer-btn {
    background: #fff;
    color: #0f172a;
}

.sol-faqs-light .sol-faqs-footer-btn:hover {
    background: var(--theme-color, #667eea);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

/* Responsive */
@media (max-width: 1024px) {
    .sol-faqs-layout {
        grid-template-columns: 1fr;
        gap: 48px;
    }
    
    .sol-faqs-sidebar {
        position: relative;
        top: 0;
        text-align: center;
    }
    
    .sol-faqs-title {
        font-size: 36px;
    }
    
    .sol-faqs-title br {
        display: none;
    }
    
    .sol-faqs-decoration {
        display: none;
    }
}

@media (max-width: 768px) {
    .sol-faqs {
        padding: 80px 0;
    }
    
    .sol-faqs-title {
        font-size: 28px;
    }
    
    .sol-faq-question {
        padding: 20px;
        gap: 12px;
    }
    
    .sol-faq-number {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }
    
    .sol-faq-answer-inner {
        padding: 0 20px 20px 64px;
    }
    
    .sol-faqs-footer {
        flex-direction: column;
        text-align: center;
        padding: 24px;
    }
    
    .sol-faqs-footer-text {
        align-items: center;
    }
}

/* Accessibility - Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .sol-faq-item,
    .sol-faq-question,
    .sol-faq-number,
    .sol-faq-toggle,
    .sol-faq-toggle svg,
    .sol-faq-answer,
    .sol-faqs-contact-btn,
    .sol-faqs-footer-btn,
    .sol-faqs-decoration-circle {
        transition: none;
        animation: none;
    }
}

/* Focus visible for keyboard navigation */
.sol-faq-question:focus {
    outline: none;
}

.sol-faq-question:focus-visible {
    outline: 2px solid var(--theme-color, #667eea);
    outline-offset: 2px;
    border-radius: 12px;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .sol-faqs-dark .sol-faq-item {
        border-color: rgba(255, 255, 255, 0.3);
    }
    
    .sol-faqs-light .sol-faq-item {
        border-color: rgba(0, 0, 0, 0.2);
    }
}

/* Related Solutions */
.sol-related {
    padding: 100px 0;
    background: #fff;
}

.sol-related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 28px;
}

.sol-related-card {
    display: block;
    padding: 32px;
    background: #f9fafb;
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.3s;
}

.sol-related-card:hover {
    background: #fff;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
    transform: translateY(-4px);
}

.sol-related-icon {
    width: 64px;
    height: 64px;
    margin-bottom: 20px;
}

.sol-related-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.sol-related-title {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 12px;
}

.sol-related-desc {
    font-size: 15px;
    color: #6b7280;
    margin: 0 0 20px;
    line-height: 1.6;
}

.sol-related-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--theme-color, #667eea);
    font-weight: 600;
    font-size: 14px;
}

.sol-related-link svg {
    width: 16px;
    height: 16px;
}

/* Not Found Card */
.not-found-card {
    text-align: center;
    padding: 80px 40px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.not-found-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 24px;
    color: #9ca3af;
}

.not-found-icon svg {
    width: 100%;
    height: 100%;
}

.not-found-card h1 {
    font-size: 32px;
    color: #111827;
    margin: 0 0 16px;
}

.not-found-card p {
    font-size: 18px;
    color: #6b7280;
    margin: 0 0 32px;
}


/* Responsive Styles */
@media (max-width: 1024px) {
    .sol-hero-content {
        grid-template-columns: 1fr;
        gap: 40px;
        text-align: center;
    }
    
    .sol-hero-subtitle {
        margin-left: auto;
        margin-right: auto;
    }
    
    .sol-hero-actions {
        justify-content: center;
    }
    
    .sol-workflow-steps {
        flex-direction: column;
        gap: 40px;
    }
    
    .sol-workflow-step {
        max-width: 100%;
    }
    
    .sol-workflow-connector {
        display: none;
    }
    
    .sol-stats-grid {
        gap: 40px;
    }
}

@media (max-width: 768px) {
    .sol-hero {
        height: auto;
        min-height: auto;
        padding: 80px 0 100px;
    }
    
    .sol-hero-title {
        font-size: 32px;
    }
    
    .sol-hero-subtitle {
        font-size: 16px;
    }
    
    .sol-hero-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .sol-btn-primary,
    .sol-btn-secondary {
        width: 100%;
        justify-content: center;
    }
    
    .sol-hero-media-wrapper {
        width: 280px;
        height: 280px;
    }
    
    .sol-stats-grid {
        flex-direction: column;
        gap: 24px;
    }
    
    .sol-stat-value {
        font-size: 28px;
    }
    
    .sol-section-title {
        font-size: 28px;
    }
    
    .sol-section-subtitle {
        font-size: 16px;
    }
    
    .sol-highlights,
    .sol-workflow,
    .sol-benefits,
    .sol-features,
    .sol-use-cases,
    .sol-integrations,
    .sol-screenshots,
    .sol-related {
        padding: 60px 0;
    }
    
    .sol-section-header {
        margin-bottom: 40px;
    }
    
    .sol-features-grid,
    .sol-benefits-grid,
    .sol-use-cases-grid,
    .sol-related-grid {
        grid-template-columns: 1fr;
    }
    
    .sol-integrations-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .sol-breadcrumb {
        font-size: 12px;
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .sol-hero {
        height: auto;
        min-height: auto;
        padding: 60px 0 80px;
    }
    
    .sol-hero-badge {
        font-size: 11px;
        padding: 5px 12px;
    }
    
    .sol-hero-icon-large,
    .sol-hero-icon-default {
        width: 140px;
        height: 140px;
    }
    
    .sol-hero-media-wrapper {
        width: 220px;
        height: 220px;
    }
    
    .sol-highlight-card {
        flex-direction: column;
        text-align: center;
    }
    
    .sol-integrations-grid {
        grid-template-columns: 1fr;
    }
}

/* Solution Page - Client Logo Marquee Override */
.sol-hero .client-logo-marquee {
    height: 7vh;
    min-height: 50px;
    max-height: 70px;
}

.sol-hero .marquee-logo {
    max-height: 30px;
    max-width: 90px;
}

.sol-hero .marquee-content {
    gap: var(--spacing-8);
}

@media (max-width: 768px) {
    .sol-hero .client-logo-marquee {
        height: 50px;
        min-height: 50px;
    }
    
    .sol-hero .marquee-logo {
        max-height: 24px;
        max-width: 70px;
    }
}
</style>

<script>
// Feature Showcase - Stacking Cards & Navigation
(function initFeatureShowcase() {
    const showcaseSection = document.querySelector('.sol-feature-showcase');
    if (!showcaseSection) return;
    
    const nav = document.getElementById('showcase-nav');
    const navLinks = document.querySelectorAll('.sol-showcase-nav-link');
    const cards = document.querySelectorAll('.sol-showcase-card');
    
    if (!nav || !cards.length) return;
    
    // Get header height for offset calculations
    const header = document.querySelector('header, .header, .site-header');
    const headerHeight = header ? header.offsetHeight : 64;
    
    // Sticky nav detection - account for header
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
            
            // Card is considered active when its top is near the viewport top (accounting for header + nav)
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
                
                // Get the actual position of the target card relative to the document
                // This works correctly for both forward and backward navigation
                const cardRect = targetCard.getBoundingClientRect();
                const currentScrollY = window.scrollY;
                
                // Calculate the absolute position of the card in the document
                const cardAbsoluteTop = cardRect.top + currentScrollY;
                
                // Calculate scroll target: position the card just below the sticky header + nav
                // Add a small offset (20px) for visual breathing room
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

// FAQ Accordion - Enhanced with smooth animations
document.querySelectorAll('.sol-faq-question').forEach(button => {
    button.addEventListener('click', () => {
        const expanded = button.getAttribute('aria-expanded') === 'true';
        const parentItem = button.closest('.sol-faq-item');
        
        // Close all other FAQs
        document.querySelectorAll('.sol-faq-item').forEach(item => {
            item.removeAttribute('data-expanded');
            item.querySelector('.sol-faq-question').setAttribute('aria-expanded', 'false');
        });
        
        // Toggle current FAQ
        if (!expanded) {
            button.setAttribute('aria-expanded', 'true');
            parentItem.setAttribute('data-expanded', 'true');
        }
    });
});

// Add entrance animation for FAQ items
const faqObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            setTimeout(() => {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, index * 100);
            faqObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.sol-faq-item').forEach((item, index) => {
    item.style.opacity = '0';
    item.style.transform = 'translateY(20px)';
    item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    faqObserver.observe(item);
});

// Screenshot Carousel
const screenshotItems = document.querySelectorAll('.sol-screenshot-item');
const screenshotDots = document.querySelectorAll('.sol-screenshot-dot');
const prevBtn = document.querySelector('.sol-screenshot-prev');
const nextBtn = document.querySelector('.sol-screenshot-next');
let currentSlide = 0;

function showSlide(index) {
    if (screenshotItems.length === 0) return;
    
    currentSlide = (index + screenshotItems.length) % screenshotItems.length;
    
    screenshotItems.forEach((item, i) => {
        item.classList.toggle('active', i === currentSlide);
    });
    
    screenshotDots.forEach((dot, i) => {
        dot.classList.toggle('active', i === currentSlide);
    });
}

if (prevBtn) {
    prevBtn.addEventListener('click', () => showSlide(currentSlide - 1));
}

if (nextBtn) {
    nextBtn.addEventListener('click', () => showSlide(currentSlide + 1));
}

screenshotDots.forEach((dot, index) => {
    dot.addEventListener('click', () => showSlide(index));
});

// Video Modal (if demo video exists)
document.querySelectorAll('[data-video-modal]').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const videoUrl = link.getAttribute('href');
        // You can implement a modal here or redirect to video
        window.open(videoUrl, '_blank');
    });
});

// Initialize infinite marquee
(function initMarquee() {
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
})();
</script>

<?php 
// Additional JS for industries gallery and testimonials
$additional_js = [
    js_url('solution-industries-gallery.js'),
    js_url('testimonials-showcase.js')
];

include_footer($additional_js); 
?>
