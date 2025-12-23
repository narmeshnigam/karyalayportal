<?php

/**
 * Features Overview Page - Modern Card Design
 * Razorpay-style with animated cards and visual hierarchy
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

try {
    $featureModel = new Feature();
    $features = $featureModel->findAll(['status' => 'PUBLISHED']);
} catch (Exception $e) {
    error_log('Error fetching features: ' . $e->getMessage());
    $features = [];
} catch (\Throwable $e) {
    error_log('Fatal error fetching features: ' . $e->getMessage());
    $features = [];
}

$page_title = 'Features';
$page_description = 'Explore the powerful features that make our platform the perfect solution for your business';

try {
    include_header($page_title, $page_description);
} catch (\Throwable $e) {
    error_log('Error including header on features page: ' . $e->getMessage());
    echo '<!DOCTYPE html><html><head><title>' . htmlspecialchars($page_title) . '</title>';
    echo '<link rel="stylesheet" href="' . htmlspecialchars(get_base_url()) . '/../assets/css/main.css">';
    echo '</head><body><div class="page-wrapper"><main class="main-content">';
}
?>

<!-- Hero Section - Dark Modern Style -->
<section class="features-hero-v2">
    <div class="features-hero-bg">
        <div class="features-hero-gradient"></div>
        <div class="features-hero-pattern"></div>
        <div class="features-hero-orbs">
            <div class="features-orb features-orb-1"></div>
            <div class="features-orb features-orb-2"></div>
            <div class="features-orb features-orb-3"></div>
        </div>
    </div>
    <div class="container">
        <div class="features-hero-content">
            <span class="features-hero-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Platform Features
            </span>
            <h1 class="features-hero-title">
                <span>Powerful features to</span>
                <span class="features-hero-title-highlight">supercharge your business</span>
            </h1>
            <p class="features-hero-subtitle">
                Discover the comprehensive toolkit designed to streamline operations, boost productivity, and drive growth across your entire organization.
            </p>
            <div class="features-hero-stats">
                <div class="features-hero-stat">
                    <span class="features-hero-stat-value"><?php echo count($features); ?>+</span>
                    <span class="features-hero-stat-label">Features</span>
                </div>
                <div class="features-hero-stat-divider"></div>
                <div class="features-hero-stat">
                    <span class="features-hero-stat-value">99.9%</span>
                    <span class="features-hero-stat-label">Uptime</span>
                </div>
                <div class="features-hero-stat-divider"></div>
                <div class="features-hero-stat">
                    <span class="features-hero-stat-value">24/7</span>
                    <span class="features-hero-stat-label">Support</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (empty($features)): ?>
<!-- Empty State -->
<section class="features-empty-section">
    <div class="container">
        <div class="features-empty">
            <div class="features-empty-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
            <h3>No Features Available</h3>
            <p>Please check back later for our comprehensive feature list.</p>
        </div>
    </div>
</section>
<?php else: ?>

<!-- Features Grid Section -->
<section class="features-grid-section">
    <div class="container">
        <div class="features-section-header">
            <h2 class="features-section-title">Everything you need to succeed</h2>
            <p class="features-section-subtitle">Our platform provides all the tools and capabilities to transform your business operations</p>
        </div>
        
        <div class="features-grid">
            <?php foreach ($features as $index => $feature):
                
                // Generate gradient colors based on index
                $gradients = [
                    ['#667eea', '#764ba2'],
                    ['#f093fb', '#f5576c'],
                    ['#4facfe', '#00f2fe'],
                    ['#43e97b', '#38f9d7'],
                    ['#fa709a', '#fee140'],
                    ['#a8edea', '#fed6e3'],
                    ['#5ee7df', '#b490ca'],
                    ['#d299c2', '#fef9d7']
                ];
                $gradient = $gradients[$index % count($gradients)];
            ?>
            <article class="feature-card" style="--gradient-start: <?php echo $gradient[0]; ?>; --gradient-end: <?php echo $gradient[1]; ?>;">
                <div class="feature-card-glow"></div>
                <div class="feature-card-inner">
                    <div class="feature-card-header">
                        <div class="feature-card-icon">
                            <?php if (!empty($feature['icon_image'])): ?>
                                <img src="<?php echo htmlspecialchars($feature['icon_image']); ?>" alt="">
                            <?php else: ?>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <h3 class="feature-card-title"><?php echo htmlspecialchars($feature['name']); ?></h3>
                    </div>
                    <div class="feature-card-content">
                        <p class="feature-card-desc"><?php echo htmlspecialchars($feature['description']); ?></p>
                        
                        <?php if (!empty($feature['benefits']) && is_array($feature['benefits'])): ?>
                        <ul class="feature-card-benefits">
                            <?php 
                            $benefitsToShow = array_slice($feature['benefits'], 0, 3);
                            foreach ($benefitsToShow as $benefit): 
                                if (!empty($benefit) && is_string($benefit)):
                            ?>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                <span><?php echo htmlspecialchars($benefit); ?></span>
                            </li>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo get_base_url(); ?>/feature/<?php echo urlencode($feature['slug']); ?>" class="feature-card-link">
                        <span>Explore Feature</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<?php
try {
    $cta_title = "Ready to Experience These Features?";
    $cta_subtitle = "Get started today and unlock all these powerful capabilities for your business";
    $cta_source = "features-page";
    include __DIR__ . '/../templates/cta-form.php';
} catch (\Throwable $e) {
    error_log('Error rendering CTA form on features page: ' . $e->getMessage());
    echo '<section class="cta-section" style="padding: 4rem 0; background: #1e293b; color: white; text-align: center;">';
    echo '<div class="container">';
    echo '<h2 style="margin-bottom: 1rem;">Ready to Experience These Features?</h2>';
    echo '<p style="margin-bottom: 2rem;">Get started today and unlock all these powerful capabilities for your business</p>';
    echo '<a href="' . htmlspecialchars(get_base_url()) . '/register.php" class="btn btn-primary" style="display: inline-block; padding: 0.75rem 2rem; background: #667eea; color: white; text-decoration: none; border-radius: 0.5rem;">Get Started</a>';
    echo '</div>';
    echo '</section>';
}
?>

<style>
/* ============================================
   Features Page - Modern Card Design
   ============================================ */

/* Hero Section */
.features-hero-v2 {
    position: relative;
    min-height: 60vh;
    padding: 120px 0 100px;
    overflow: hidden;
    background: #0f172a;
    color: #fff;
    display: flex;
    align-items: center;
}

.features-hero-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.features-hero-gradient {
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(ellipse 80% 50% at 50% -20%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(ellipse 60% 50% at 0% 100%, rgba(37, 99, 235, 0.2) 0%, transparent 50%),
        radial-gradient(ellipse 40% 40% at 100% 50%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);
}

.features-hero-pattern {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.02;
}

.features-hero-orbs {
    position: absolute;
    inset: 0;
    overflow: hidden;
}

.features-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.4;
    animation: float 20s ease-in-out infinite;
}

.features-orb-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    top: -100px;
    right: 10%;
    animation-delay: 0s;
}

.features-orb-2 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    bottom: -50px;
    left: 5%;
    animation-delay: -7s;
}

.features-orb-3 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    top: 40%;
    left: 30%;
    animation-delay: -14s;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(20px, -30px) scale(1.05); }
    50% { transform: translate(-10px, 20px) scale(0.95); }
    75% { transform: translate(30px, 10px) scale(1.02); }
}

.features-hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.features-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 28px;
    backdrop-filter: blur(10px);
}

.features-hero-badge svg {
    color: #fbbf24;
}

.features-hero-title {
    font-size: clamp(36px, 5vw, 60px);
    font-weight: 700;
    line-height: 1.1;
    margin: 0 0 24px;
}

.features-hero-title span {
    display: block;
}

.features-hero-title-highlight {
    background: linear-gradient(135deg, #667eea 0%, #f093fb 50%, #4facfe 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.features-hero-subtitle {
    font-size: 18px;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.7);
    margin: 0 0 40px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.features-hero-stats {
    display: inline-flex;
    align-items: center;
    gap: 32px;
    padding: 20px 40px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    backdrop-filter: blur(10px);
}

.features-hero-stat {
    text-align: center;
}

.features-hero-stat-value {
    display: block;
    font-size: 28px;
    font-weight: 700;
    color: #fff;
    line-height: 1;
    margin-bottom: 4px;
}

.features-hero-stat-label {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.features-hero-stat-divider {
    width: 1px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
}

/* Features Grid Section */
.features-grid-section {
    padding: 100px 0;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}

.features-section-header {
    text-align: center;
    margin-bottom: 60px;
}

.features-section-title {
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 16px;
    letter-spacing: -0.5px;
}

.features-section-subtitle {
    font-size: 18px;
    color: #64748b;
    margin: 0;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

/* Masonry Grid */
.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

/* Feature Card */
.feature-card {
    position: relative;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
}

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    border-color: transparent;
}

.feature-card-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 2;
}

.feature-card:hover .feature-card-glow {
    opacity: 1;
}

.feature-card-inner {
    position: relative;
    z-index: 1;
    padding: 28px;
    display: flex;
    flex-direction: column;
    flex: 1;
}

/* Card Header */
.feature-card-header {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
}

/* Card Icon */
.feature-card-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    border-radius: 12px;
    flex-shrink: 0;
}

.feature-card-icon svg {
    width: 24px;
    height: 24px;
    stroke: #fff;
}

.feature-card-icon img {
    width: 28px;
    height: 28px;
    object-fit: contain;
    filter: brightness(0) invert(1);
}

/* Card Title in Header */
.feature-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
    flex: 1;
}

/* Card Content */
.feature-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.feature-card-desc {
    font-size: 14px;
    color: #64748b;
    line-height: 1.6;
    margin: 0 0 16px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Card Benefits */
.feature-card-benefits {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}

.feature-card-benefits li {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 13px;
    color: #475569;
    line-height: 1.4;
}

.feature-card-benefits svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    stroke: #10b981;
    margin-top: 2px;
}

/* Card Link */
.feature-card-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    text-decoration: none;
    margin-top: auto;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

.feature-card-link svg {
    width: 18px;
    height: 18px;
    transition: transform 0.3s ease;
}

.feature-card:hover .feature-card-link {
    color: var(--gradient-start);
}

.feature-card:hover .feature-card-link svg {
    transform: translateX(4px);
    stroke: var(--gradient-start);
}

/* Empty State */
.features-empty-section {
    padding: 100px 0;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}

.features-empty {
    text-align: center;
    padding: 60px 40px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    max-width: 500px;
    margin: 0 auto;
}

.features-empty-icon {
    color: #94a3b8;
    margin-bottom: 24px;
}

.features-empty h3 {
    font-size: 24px;
    color: #0f172a;
    margin: 0 0 12px;
}

.features-empty p {
    font-size: 16px;
    color: #64748b;
    margin: 0;
}

/* Responsive Styles */
@media (max-width: 1024px) {
    .features-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .features-hero-v2 {
        min-height: auto;
        padding: 100px 0 80px;
    }
    
    .features-hero-title {
        font-size: 32px;
    }
    
    .features-hero-subtitle {
        font-size: 16px;
        margin-bottom: 32px;
    }
    
    .features-hero-stats {
        flex-direction: column;
        gap: 20px;
        padding: 24px 32px;
    }
    
    .features-hero-stat-divider {
        width: 60px;
        height: 1px;
    }
    
    .features-grid-section {
        padding: 60px 0;
    }
    
    .features-section-header {
        margin-bottom: 40px;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .feature-card-inner {
        padding: 20px;
    }
    
    .feature-card-header {
        gap: 12px;
    }
    
    .feature-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
    }
    
    .feature-card-icon svg {
        width: 22px;
        height: 22px;
    }
    
    .feature-card-title {
        font-size: 16px;
    }
    
    .feature-card-desc {
        font-size: 13px;
    }
    
    .feature-card-benefits li {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .features-hero-badge {
        font-size: 11px;
        padding: 8px 16px;
    }
    
    .features-hero-stats {
        width: 100%;
        padding: 20px;
    }
    
    .features-hero-stat-value {
        font-size: 24px;
    }
}
</style>

<?php 
try {
    include_footer();
} catch (\Throwable $e) {
    error_log('Error including footer on features page: ' . $e->getMessage());
    echo '</main></div></body></html>';
}
?>
