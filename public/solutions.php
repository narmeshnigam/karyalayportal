<?php

/**
 * Solutions Overview Page - Stacking Cards Design
 * Razorpay-style with categorized sections and sticky navigation
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

try {
    $solutionModel = new Solution();
    $solutions = $solutionModel->findAll(['status' => 'PUBLISHED']);
} catch (Exception $e) {
    error_log('Error fetching solutions: ' . $e->getMessage());
    $solutions = [];
}

// Category definitions with titles and subtitles
$categoryDefinitions = [
    'core' => [
        'title' => 'Core Business Solutions',
        'subtitle' => 'Essential tools to power your business operations'
    ],
    'finance' => [
        'title' => 'Finance & Accounting',
        'subtitle' => 'Streamline your financial operations and reporting'
    ],
    'operations' => [
        'title' => 'Operations & Logistics',
        'subtitle' => 'Optimize your supply chain and operational workflows'
    ],
    'sales' => [
        'title' => 'Sales & Marketing',
        'subtitle' => 'Drive growth with powerful sales and marketing tools'
    ],
    'hr' => [
        'title' => 'Human Resources',
        'subtitle' => 'Manage your workforce efficiently'
    ],
    'analytics' => [
        'title' => 'Analytics & Reporting',
        'subtitle' => 'Data-driven insights for better decisions'
    ]
];

// Group solutions by their database category
$categories = [];
foreach ($solutions as $solution) {
    $cat = $solution['category'] ?? 'core';
    if (!isset($categories[$cat])) {
        $categories[$cat] = [
            'title' => $categoryDefinitions[$cat]['title'] ?? ucfirst($cat),
            'subtitle' => $categoryDefinitions[$cat]['subtitle'] ?? '',
            'solutions' => []
        ];
    }
    $categories[$cat]['solutions'][] = $solution;
}

// Sort categories by predefined order
$categoryOrder = ['core', 'finance', 'operations', 'sales', 'hr', 'analytics'];
$sortedCategories = [];
foreach ($categoryOrder as $catKey) {
    if (isset($categories[$catKey]) && !empty($categories[$catKey]['solutions'])) {
        $sortedCategories[$catKey] = $categories[$catKey];
    }
}
// Add any remaining categories not in the predefined order
foreach ($categories as $catKey => $category) {
    if (!isset($sortedCategories[$catKey]) && !empty($category['solutions'])) {
        $sortedCategories[$catKey] = $category;
    }
}
$categories = $sortedCategories;

$page_title = 'Solutions';
$page_description = 'Explore our comprehensive suite of business management solutions';

include_header($page_title, $page_description);
?>

<!-- Hero Section - Modern Style with Animated Orbs -->
<section class="solutions-hero-v2">
    <div class="solutions-hero-bg">
        <div class="solutions-hero-gradient"></div>
        <div class="solutions-hero-pattern"></div>
        <div class="solutions-hero-orbs">
            <div class="solutions-orb solutions-orb-1"></div>
            <div class="solutions-orb solutions-orb-2"></div>
            <div class="solutions-orb solutions-orb-3"></div>
        </div>
    </div>
    <div class="container">
        <div class="solutions-hero-content">
            <span class="solutions-hero-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
                Our Solutions
            </span>
            <h1 class="solutions-hero-title">
                <span>Powerful solutions for</span>
                <span class="solutions-hero-title-highlight">every business need</span>
            </h1>
            <p class="solutions-hero-subtitle">
                Discover our comprehensive suite of business management solutions designed to streamline operations, boost productivity, and drive growth.
            </p>
            <div class="solutions-hero-stats">
                <div class="solutions-hero-stat">
                    <span class="solutions-hero-stat-value"><?php echo count($solutions); ?>+</span>
                    <span class="solutions-hero-stat-label">Solutions</span>
                </div>
                <div class="solutions-hero-stat-divider"></div>
                <div class="solutions-hero-stat">
                    <span class="solutions-hero-stat-value"><?php echo count($categories); ?></span>
                    <span class="solutions-hero-stat-label">Categories</span>
                </div>
                <div class="solutions-hero-stat-divider"></div>
                <div class="solutions-hero-stat">
                    <span class="solutions-hero-stat-value">100%</span>
                    <span class="solutions-hero-stat-label">Customizable</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (empty($solutions)): ?>
<!-- Empty State -->
<section class="solutions-empty-section">
    <div class="container">
        <div class="solutions-empty">
            <div class="solutions-empty-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3>No Solutions Available</h3>
            <p>Please check back later for our comprehensive business solutions.</p>
        </div>
    </div>
</section>
<?php else: ?>

<!-- Sticky Navigation -->
<nav class="solutions-nav" id="solutions-nav">
    <div class="container">
        <div class="solutions-nav-inner">
            <?php $catIndex = 0; foreach ($categories as $catKey => $category): ?>
                <a href="#category-<?php echo htmlspecialchars($catKey); ?>" 
                   class="solutions-nav-link <?php echo $catIndex === 0 ? 'active' : ''; ?>" 
                   data-target="category-<?php echo htmlspecialchars($catKey); ?>">
                    <?php echo htmlspecialchars($category['title']); ?>
                </a>
            <?php $catIndex++; endforeach; ?>
        </div>
    </div>
</nav>

<!-- Stacking Cards Sections -->
<div class="solutions-cards-wrapper">
    <?php foreach ($categories as $catKey => $category): ?>
    <section class="solutions-category" id="category-<?php echo htmlspecialchars($catKey); ?>">
        <div class="container">
            <div class="solutions-category-header">
                <h2 class="solutions-category-title"><?php echo htmlspecialchars($category['title']); ?></h2>
                <p class="solutions-category-subtitle"><?php echo htmlspecialchars($category['subtitle']); ?></p>
            </div>
            
            <div class="solutions-stacking-cards">
                <?php foreach ($category['solutions'] as $index => $solution): 
                    $colorTheme = $solution['color_theme'] ?? '#667eea';
                ?>
                <article class="solution-stack-card" data-card-index="<?php echo $index; ?>" style="--card-theme: <?php echo htmlspecialchars($colorTheme); ?>;">
                    <div class="solution-stack-card-inner">
                        <div class="solution-stack-media">
                            <div class="solution-stack-media-frame">
                                <?php if (!empty($solution['icon_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($solution['icon_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($solution['name']); ?>"
                                         class="solution-stack-icon">
                                <?php else: ?>
                                    <div class="solution-stack-icon-default">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="solution-stack-content">
                            <?php if (!empty($solution['tagline'])): ?>
                                <span class="solution-stack-badge"><?php echo htmlspecialchars($solution['tagline']); ?></span>
                            <?php endif; ?>
                            <h3 class="solution-stack-title"><?php echo htmlspecialchars($solution['name']); ?></h3>
                            <p class="solution-stack-desc"><?php echo htmlspecialchars($solution['description']); ?></p>
                            <a href="<?php echo get_base_url(); ?>/solution/<?php echo urlencode($solution['slug']); ?>" class="solution-stack-link">
                                <span>Learn More</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- CTA Section -->
<?php
$cta_title = "Find the Right Solution for Your Business";
$cta_subtitle = "Let us help you choose the perfect combination of solutions to meet your unique needs";
$cta_source = "solutions-page";
include __DIR__ . '/../templates/cta-form.php';
?>

<style>
/* ============================================
   Solutions Page - Stacking Cards Design
   Matching solution detail page card dimensions
   ============================================ */

/* Hero Section */
.solutions-hero-v2 {
    position: relative;
    min-height: 60vh;
    padding: 120px 0 100px;
    overflow: hidden;
    background: #0f172a;
    color: #fff;
    display: flex;
    align-items: center;
}

.solutions-hero-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.solutions-hero-gradient {
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(ellipse 80% 50% at 50% -20%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(ellipse 60% 50% at 0% 100%, rgba(37, 99, 235, 0.2) 0%, transparent 50%),
        radial-gradient(ellipse 40% 40% at 100% 50%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);
}

.solutions-hero-pattern {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.02;
}

.solutions-hero-orbs {
    position: absolute;
    inset: 0;
    overflow: hidden;
}

.solutions-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.4;
    animation: solutions-float 20s ease-in-out infinite;
}

.solutions-orb-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    top: -100px;
    right: 10%;
    animation-delay: 0s;
}

.solutions-orb-2 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    bottom: -50px;
    left: 5%;
    animation-delay: -7s;
}

.solutions-orb-3 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    top: 40%;
    left: 30%;
    animation-delay: -14s;
}

@keyframes solutions-float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(20px, -30px) scale(1.05); }
    50% { transform: translate(-10px, 20px) scale(0.95); }
    75% { transform: translate(30px, 10px) scale(1.02); }
}

.solutions-hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.solutions-hero-badge {
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

.solutions-hero-badge svg {
    color: #4facfe;
}

.solutions-hero-title {
    font-size: clamp(36px, 5vw, 60px);
    font-weight: 700;
    line-height: 1.1;
    margin: 0 0 24px;
}

.solutions-hero-title span {
    display: block;
}

.solutions-hero-title-highlight {
    background: linear-gradient(135deg, #667eea 0%, #4facfe 50%, #43e97b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.solutions-hero-subtitle {
    font-size: 18px;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.7);
    margin: 0 0 40px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.solutions-hero-stats {
    display: inline-flex;
    align-items: center;
    gap: 32px;
    padding: 20px 40px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    backdrop-filter: blur(10px);
}

.solutions-hero-stat {
    text-align: center;
}

.solutions-hero-stat-value {
    display: block;
    font-size: 28px;
    font-weight: 700;
    color: #fff;
    line-height: 1;
    margin-bottom: 4px;
}

.solutions-hero-stat-label {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.solutions-hero-stat-divider {
    width: 1px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
}

/* Sticky Navigation */
.solutions-nav {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    position: sticky;
    top: 64px;
    z-index: 50;
    transition: box-shadow 0.3s ease;
}

.solutions-nav.is-sticky {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

@media (max-width: 768px) {
    .solutions-nav {
        top: 56px;
    }
}

.solutions-nav-inner {
    display: flex;
    justify-content: center;
    gap: 8px;
    padding: 0 20px;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.solutions-nav-inner::-webkit-scrollbar {
    display: none;
}

.solutions-nav-link {
    display: inline-block;
    padding: 16px 24px;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    text-decoration: none;
    white-space: nowrap;
    position: relative;
    transition: color 0.2s ease;
}

.solutions-nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: #2563eb;
    transition: width 0.3s ease;
}

.solutions-nav-link:hover {
    color: #111827;
}

.solutions-nav-link.active {
    color: #111827;
    font-weight: 600;
}

.solutions-nav-link.active::after {
    width: calc(100% - 48px);
}

/* Stacking Cards Wrapper */
.solutions-cards-wrapper {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 0 0 60px;
}

/* Category Section */
.solutions-category {
    padding: 80px 0 40px;
}

.solutions-category-header {
    text-align: center;
    margin-bottom: 48px;
}

.solutions-category-title {
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 300;
    color: #111827;
    margin: 0 0 16px;
    letter-spacing: -0.5px;
}

.solutions-category-subtitle {
    font-size: 16px;
    color: #6b7280;
    margin: 0;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

/* Stacking Cards Container - Matching solution detail page */
.solutions-stacking-cards {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0;
    /* Match solution detail page: 2:1 aspect ratio container */
    max-width: calc(70vh * 2);
    margin: 0 auto;
}

/* Individual Stack Card - Matching solution detail page dimensions */
.solution-stack-card {
    position: sticky;
    top: 140px;
    background: #ffffff;
    border-radius: 24px;
    margin-bottom: 40px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
    border: 1px solid #e5e7eb;
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.4s ease;
    will-change: transform;
    /* Match solution detail page: 70vh height */
    height: 70vh;
    min-height: 500px;
    max-height: 700px;
}

.solution-stack-card:nth-child(1) { z-index: 1; }
.solution-stack-card:nth-child(2) { z-index: 2; }
.solution-stack-card:nth-child(3) { z-index: 3; }
.solution-stack-card:nth-child(4) { z-index: 4; }
.solution-stack-card:nth-child(5) { z-index: 5; }
.solution-stack-card:nth-child(6) { z-index: 6; }
.solution-stack-card:nth-child(7) { z-index: 7; }
.solution-stack-card:nth-child(8) { z-index: 8; }

.solution-stack-card.is-stacked {
    transform: scale(0.98);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
}

.solution-stack-card:hover {
    border-color: var(--card-theme, #2563eb);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
}

/* Card Inner - Match solution detail page grid */
.solution-stack-card-inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 48px;
    padding: 40px;
    align-items: center;
    height: 100%;
}

/* Media Section */
.solution-stack-media {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Media Frame - Match solution detail page square aspect ratio */
.solution-stack-media-frame {
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

.solution-stack-icon {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.solution-stack-icon-default {
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--card-theme, #2563eb) 0%, #7c3aed 100%);
    border-radius: 24px;
}

.solution-stack-icon-default svg {
    width: 50px;
    height: 50px;
    stroke: #fff;
}

/* Content Section */
.solution-stack-content {
    padding: 20px 0;
}

.solution-stack-badge {
    display: inline-block;
    padding: 6px 14px;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
    color: var(--card-theme, #2563eb);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 4px;
    margin-bottom: 16px;
}

.solution-stack-title {
    font-size: clamp(22px, 3vw, 32px);
    font-weight: 600;
    color: #111827;
    line-height: 1.3;
    margin: 0 0 24px;
}

.solution-stack-desc {
    font-size: 15px;
    color: #4b5563;
    line-height: 1.7;
    margin: 0 0 32px;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.solution-stack-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: linear-gradient(135deg, var(--card-theme, #2563eb) 0%, #1d4ed8 100%);
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
}

.solution-stack-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
    color: #fff;
}

.solution-stack-link svg {
    transition: transform 0.3s ease;
}

.solution-stack-link:hover svg {
    transform: translateX(4px);
}

/* Empty State */
.solutions-empty-section {
    padding: 100px 0;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}

.solutions-empty {
    text-align: center;
    padding: 60px 40px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    max-width: 500px;
    margin: 0 auto;
}

.solutions-empty-icon {
    color: #9ca3af;
    margin-bottom: 24px;
}

.solutions-empty h3 {
    font-size: 24px;
    color: #111827;
    margin: 0 0 12px;
}

.solutions-empty p {
    font-size: 16px;
    color: #6b7280;
    margin: 0;
}

/* Responsive Styles */
@media (max-width: 992px) {
    .solutions-stacking-cards {
        max-width: 100%;
    }
    
    .solution-stack-card {
        height: auto;
        min-height: auto;
        max-height: none;
    }
    
    .solution-stack-card-inner {
        grid-template-columns: 1fr;
        gap: 32px;
        padding: 32px;
        height: auto;
    }
    
    .solution-stack-media {
        order: -1;
    }
    
    .solution-stack-media-frame {
        max-width: 100%;
        padding: 20px;
    }
    
    .solutions-nav-link {
        padding: 14px 16px;
        font-size: 13px;
    }
}

@media (max-width: 768px) {
    .solutions-hero-v2 {
        min-height: auto;
        padding: 100px 0 80px;
    }
    
    .solutions-hero-title {
        font-size: 32px;
    }
    
    .solutions-hero-subtitle {
        font-size: 16px;
        margin-bottom: 32px;
    }
    
    .solutions-hero-stats {
        flex-direction: column;
        gap: 20px;
        padding: 24px 32px;
    }
    
    .solutions-hero-stat-divider {
        width: 60px;
        height: 1px;
    }
    
    .solutions-nav-inner {
        justify-content: flex-start;
        gap: 4px;
    }
    
    .solutions-category {
        padding: 60px 0 30px;
    }
    
    .solution-stack-card {
        border-radius: 16px;
        margin-bottom: 24px;
    }
    
    .solution-stack-card:nth-child(n) {
        top: 120px;
    }
    
    .solution-stack-card-inner {
        padding: 24px;
        gap: 24px;
    }
    
    .solution-stack-title {
        font-size: 22px;
    }
    
    .solution-stack-desc {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .solutions-hero-badge {
        font-size: 11px;
        padding: 8px 16px;
    }
    
    .solutions-hero-stats {
        width: 100%;
        padding: 20px;
    }
    
    .solutions-hero-stat-value {
        font-size: 24px;
    }
}

@media (max-width: 480px) {
    .solution-stack-card-inner {
        padding: 20px;
    }
    
    .solution-stack-media-frame {
        aspect-ratio: 1/1;
        padding: 16px;
    }
    
    .solution-stack-link {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sticky navigation highlight on scroll
    const nav = document.getElementById('solutions-nav');
    const navLinks = document.querySelectorAll('.solutions-nav-link');
    const categories = document.querySelectorAll('.solutions-category');
    
    if (!nav || navLinks.length === 0) return;
    
    // Add sticky class on scroll
    const navTop = nav.offsetTop;
    
    function handleScroll() {
        // Sticky nav shadow
        if (window.scrollY > navTop - 64) {
            nav.classList.add('is-sticky');
        } else {
            nav.classList.remove('is-sticky');
        }
        
        // Highlight active nav link based on scroll position
        let currentCategory = '';
        categories.forEach(category => {
            const rect = category.getBoundingClientRect();
            if (rect.top <= 200 && rect.bottom > 200) {
                currentCategory = category.id;
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-target') === currentCategory) {
                link.classList.add('active');
            }
        });
        
        // Handle stacking card effect
        document.querySelectorAll('.solution-stack-card').forEach(card => {
            const rect = card.getBoundingClientRect();
            const threshold = 160;
            
            if (rect.top < threshold && rect.bottom > threshold) {
                card.classList.add('is-stacked');
            } else {
                card.classList.remove('is-stacked');
            }
        });
    }
    
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
    
    // Smooth scroll for nav links
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const target = document.getElementById(targetId);
            if (target) {
                const offset = 140;
                const targetPosition = target.getBoundingClientRect().top + window.scrollY - offset;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }
        });
    });
});
</script>

<?php include_footer(); ?>
