<?php

/**
 * SellerPortal System
 * Case Studies Index Page - Modern Design
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

use Karyalay\Models\CaseStudy;

try {
    $caseStudyModel = new CaseStudy();
    $caseStudies = $caseStudyModel->findAll(['status' => 'PUBLISHED']);
} catch (Exception $e) {
    error_log('Error fetching case studies: ' . $e->getMessage());
    $caseStudies = [];
}

$page_title = 'Case Studies';
$page_description = 'See how businesses are succeeding with Karyalay';

include_header($page_title, $page_description);
?>

<!-- Hero Section - Compact Dark Style -->
<section class="casestudies-hero">
    <div class="casestudies-hero-bg">
        <div class="casestudies-hero-gradient"></div>
        <div class="casestudies-hero-orbs">
            <div class="casestudies-orb casestudies-orb-1"></div>
            <div class="casestudies-orb casestudies-orb-2"></div>
            <div class="casestudies-orb casestudies-orb-3"></div>
        </div>
    </div>
    <div class="container">
        <div class="casestudies-hero-content">
            <h1 class="casestudies-hero-title">Customer <span class="casestudies-hero-highlight">Success Stories</span></h1>
            <p class="casestudies-hero-subtitle">Discover how businesses like yours are transforming their operations and achieving remarkable results</p>
        </div>
    </div>
</section>

<!-- Case Studies Grid Section -->
<section class="casestudies-section">
    <div class="container">
        <?php if (empty($caseStudies)): ?>
            <div class="casestudies-empty">
                <div class="casestudies-empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3>No Case Studies Available</h3>
                <p>Check back soon for inspiring success stories from businesses using our platform!</p>
            </div>
        <?php else: ?>
            <div class="casestudies-grid">
                <?php foreach ($caseStudies as $index => $caseStudy): 
                    $gradients = [
                        ['#667eea', '#764ba2'],
                        ['#f093fb', '#f5576c'],
                        ['#4facfe', '#00f2fe'],
                        ['#43e97b', '#38f9d7'],
                        ['#fa709a', '#fee140'],
                    ];
                    $gradient = $gradients[$index % count($gradients)];
                ?>
                <article class="casestudy-card" style="--gradient-start: <?php echo $gradient[0]; ?>; --gradient-end: <?php echo $gradient[1]; ?>;">
                    <div class="casestudy-card-glow"></div>
                    <?php if (!empty($caseStudy['cover_image'])): ?>
                        <div class="casestudy-card-image">
                            <img src="<?php echo htmlspecialchars($caseStudy['cover_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($caseStudy['title']); ?>"
                                 loading="lazy">
                            <div class="casestudy-card-overlay"></div>
                        </div>
                    <?php else: ?>
                        <div class="casestudy-card-image casestudy-card-image-placeholder">
                            <div class="casestudy-card-placeholder-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="casestudy-card-content">
                        <?php if (!empty($caseStudy['industry'])): ?>
                            <span class="casestudy-card-industry"><?php echo htmlspecialchars($caseStudy['industry']); ?></span>
                        <?php endif; ?>
                        
                        <h3 class="casestudy-card-title"><?php echo htmlspecialchars($caseStudy['title']); ?></h3>
                        
                        <p class="casestudy-card-client">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <?php echo htmlspecialchars($caseStudy['client_name']); ?>
                        </p>
                        
                        <?php if (!empty($caseStudy['challenge'])): ?>
                            <div class="casestudy-card-challenge">
                                <span class="casestudy-card-label">Challenge</span>
                                <p><?php echo htmlspecialchars(substr($caseStudy['challenge'], 0, 120)); ?><?php echo strlen($caseStudy['challenge']) > 120 ? '...' : ''; ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <a href="<?php echo get_base_url(); ?>/case-study/<?php echo urlencode($caseStudy['slug']); ?>" class="casestudy-card-link">
                            <span>Read Full Story</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<?php
$cta_title = "Ready to Write Your Success Story?";
$cta_subtitle = "Join these successful businesses and transform your operations";
$cta_source = "case-studies-page";
include __DIR__ . '/../templates/cta-form.php';
?>

<style>
/* ============================================
   Case Studies Page - Modern Design
   ============================================ */

/* Hero Section - Compact */
.casestudies-hero {
    position: relative;
    height: 30vh;
    min-height: 200px;
    max-height: 30vh;
    overflow: hidden;
    background: #0f172a;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.casestudies-hero-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.casestudies-hero-gradient {
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(ellipse 80% 50% at 50% -20%, rgba(34, 197, 94, 0.3) 0%, transparent 50%),
        radial-gradient(ellipse 60% 50% at 0% 100%, rgba(16, 185, 129, 0.2) 0%, transparent 50%),
        radial-gradient(ellipse 40% 40% at 100% 50%, rgba(52, 211, 153, 0.15) 0%, transparent 50%);
}

.casestudies-hero-orbs {
    position: absolute;
    inset: 0;
    overflow: hidden;
}

.casestudies-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.4;
    animation: casestudies-float 20s ease-in-out infinite;
}

.casestudies-orb-1 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    top: -80px;
    right: 15%;
    animation-delay: 0s;
}

.casestudies-orb-2 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
    bottom: -40px;
    left: 10%;
    animation-delay: -7s;
}

.casestudies-orb-3 {
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, #86efac 0%, #4ade80 100%);
    top: 30%;
    left: 25%;
    animation-delay: -14s;
}

@keyframes casestudies-float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(20px, -30px) scale(1.05); }
    50% { transform: translate(-10px, 20px) scale(0.95); }
    75% { transform: translate(30px, 10px) scale(1.02); }
}

.casestudies-hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

.casestudies-hero-title {
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 12px;
    white-space: nowrap;
}

.casestudies-hero-highlight {
    background: linear-gradient(135deg, #22c55e 0%, #4ade80 50%, #86efac 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.casestudies-hero-subtitle {
    font-size: 16px;
    line-height: 1.5;
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

/* Case Studies Section */
.casestudies-section {
    padding: 80px 0;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}

/* Case Studies Grid */
.casestudies-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Case Study Card */
.casestudy-card {
    position: relative;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
}

.casestudy-card-glow {
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

.casestudy-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
    border-color: transparent;
}

.casestudy-card:hover .casestudy-card-glow {
    opacity: 1;
}

/* Card Image */
.casestudy-card-image {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
}

.casestudy-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.casestudy-card:hover .casestudy-card-image img {
    transform: scale(1.08);
}

.casestudy-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.4) 100%);
}

.casestudy-card-image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--gradient-start, #667eea), var(--gradient-end, #764ba2));
}

.casestudy-card-placeholder-icon {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
}

.casestudy-card-placeholder-icon svg {
    width: 40px;
    height: 40px;
    stroke: #fff;
}

/* Card Content */
.casestudy-card-content {
    padding: 24px;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.casestudy-card-industry {
    display: inline-block;
    padding: 6px 14px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    color: var(--gradient-start, #667eea);
    font-size: 12px;
    font-weight: 600;
    border-radius: 50px;
    margin-bottom: 16px;
    align-self: flex-start;
}

.casestudy-card-title {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 12px;
    line-height: 1.3;
}

.casestudy-card-client {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #64748b;
    margin: 0 0 16px;
}

.casestudy-card-client svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    color: #94a3b8;
}

.casestudy-card-challenge {
    margin-bottom: 20px;
    flex: 1;
}

.casestudy-card-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.casestudy-card-challenge p {
    font-size: 14px;
    color: #475569;
    line-height: 1.6;
    margin: 0;
}

.casestudy-card-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--gradient-start, #667eea);
    text-decoration: none;
    margin-top: auto;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

.casestudy-card-link svg {
    width: 18px;
    height: 18px;
    transition: transform 0.3s ease;
}

.casestudy-card:hover .casestudy-card-link {
    gap: 12px;
}

.casestudy-card:hover .casestudy-card-link svg {
    transform: translateX(4px);
}

/* Empty State */
.casestudies-empty {
    text-align: center;
    padding: 80px 40px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    max-width: 500px;
    margin: 0 auto;
}

.casestudies-empty-icon {
    color: #94a3b8;
    margin-bottom: 24px;
}

.casestudies-empty h3 {
    font-size: 24px;
    color: #0f172a;
    margin: 0 0 12px;
}

.casestudies-empty p {
    font-size: 16px;
    color: #64748b;
    margin: 0;
}

/* Responsive Styles */
@media (max-width: 1024px) {
    .casestudies-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
}

@media (max-width: 768px) {
    .casestudies-hero {
        height: auto;
        min-height: 150px;
        max-height: none;
        padding: 40px 0;
    }
    
    .casestudies-hero-title {
        font-size: 24px;
        white-space: normal;
    }
    
    .casestudies-hero-subtitle {
        font-size: 14px;
    }
    
    .casestudies-section {
        padding: 60px 0;
    }
    
    .casestudies-grid {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 0 16px;
    }
    
    .casestudy-card-image {
        height: 180px;
    }
    
    .casestudy-card-content {
        padding: 20px;
    }
    
    .casestudy-card-title {
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .casestudies-hero-title {
        font-size: 20px;
    }
    
    .casestudy-card-image {
        height: 160px;
    }
}
</style>

<?php include_footer(); ?>
