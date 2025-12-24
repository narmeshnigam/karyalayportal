<?php

/**
 * SellerPortal System
 * Case Study Detail Page - Modern Design
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

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: ' . get_base_url() . '/case-studies.php');
    exit;
}

try {
    $caseStudyModel = new CaseStudy();
    $caseStudy = $caseStudyModel->findBySlug($slug);
    
    if (!$caseStudy || $caseStudy['status'] !== 'PUBLISHED') {
        header('HTTP/1.0 404 Not Found');
        $page_title = 'Case Study Not Found';
        $page_description = 'The requested case study could not be found';
        include_header($page_title, $page_description);
        ?>
        <section class="csdetail-error">
            <div class="container">
                <div class="csdetail-error-card">
                    <div class="csdetail-error-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1>Case Study Not Found</h1>
                    <p>The case study you're looking for doesn't exist or is no longer available.</p>
                    <a href="<?php echo get_base_url(); ?>/case-studies.php" class="csdetail-btn-primary">View All Case Studies</a>
                </div>
            </div>
        </section>
        <?php
        include_footer();
        exit;
    }
    
} catch (Exception $e) {
    error_log('Error fetching case study: ' . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    $page_title = 'Error';
    include_header($page_title, '');
    ?>
    <section class="csdetail-error">
        <div class="container">
            <div class="csdetail-error-card">
                <h1>Error</h1>
                <p>An error occurred while loading the case study. Please try again later.</p>
                <a href="<?php echo get_base_url(); ?>/case-studies.php" class="csdetail-btn-primary">View All Case Studies</a>
            </div>
        </div>
    </section>
    <?php
    include_footer();
    exit;
}

$page_title = htmlspecialchars($caseStudy['title']);
$page_description = htmlspecialchars(substr($caseStudy['challenge'], 0, 160));

include_header($page_title, $page_description);
?>

<!-- Hero Section -->
<section class="csdetail-hero">
    <div class="csdetail-hero-bg">
        <div class="csdetail-hero-gradient"></div>
        <div class="csdetail-hero-orbs">
            <div class="csdetail-orb csdetail-orb-1"></div>
            <div class="csdetail-orb csdetail-orb-2"></div>
        </div>
    </div>
    <div class="container">
        <div class="csdetail-hero-content">
            <a href="<?php echo get_base_url(); ?>/case-studies.php" class="csdetail-back-link">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                All Case Studies
            </a>
            <?php if (!empty($caseStudy['industry'])): ?>
                <span class="csdetail-hero-badge"><?php echo htmlspecialchars($caseStudy['industry']); ?></span>
            <?php endif; ?>
            <h1 class="csdetail-hero-title"><?php echo htmlspecialchars($caseStudy['title']); ?></h1>
            <div class="csdetail-hero-client">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <?php echo htmlspecialchars($caseStudy['client_name']); ?>
            </div>
        </div>
    </div>
</section>

<!-- Cover Image -->
<?php if (!empty($caseStudy['cover_image'])): ?>
<section class="csdetail-cover">
    <div class="container">
        <div class="csdetail-cover-wrapper">
            <img src="<?php echo htmlspecialchars($caseStudy['cover_image']); ?>" 
                 alt="<?php echo htmlspecialchars($caseStudy['title']); ?>">
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Content Sections -->
<section class="csdetail-content">
    <div class="container">
        <div class="csdetail-content-wrapper">
            <!-- Challenge -->
            <div class="csdetail-section">
                <div class="csdetail-section-header">
                    <div class="csdetail-section-icon csdetail-icon-challenge">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h2 class="csdetail-section-title">The Challenge</h2>
                </div>
                <div class="csdetail-section-body">
                    <?php echo nl2br(htmlspecialchars($caseStudy['challenge'])); ?>
                </div>
            </div>

            <!-- Solution -->
            <div class="csdetail-section">
                <div class="csdetail-section-header">
                    <div class="csdetail-section-icon csdetail-icon-solution">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h2 class="csdetail-section-title">The Solution</h2>
                </div>
                <div class="csdetail-section-body">
                    <?php echo nl2br(htmlspecialchars($caseStudy['solution'])); ?>
                    
                    <?php if (!empty($caseStudy['modules_used']) && is_array($caseStudy['modules_used'])): ?>
                        <div class="csdetail-modules">
                            <h4>Modules Used</h4>
                            <div class="csdetail-modules-list">
                                <?php foreach ($caseStudy['modules_used'] as $module): ?>
                                    <span class="csdetail-module-tag"><?php echo htmlspecialchars($module); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Results -->
            <div class="csdetail-section">
                <div class="csdetail-section-header">
                    <div class="csdetail-section-icon csdetail-icon-results">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h2 class="csdetail-section-title">The Results</h2>
                </div>
                <div class="csdetail-section-body">
                    <?php echo nl2br(htmlspecialchars($caseStudy['results'])); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="csdetail-cta">
    <div class="container">
        <div class="csdetail-cta-card">
            <div class="csdetail-cta-content">
                <h2>Ready to Achieve Similar Results?</h2>
                <p>Let us help transform your business operations</p>
            </div>
            <div class="csdetail-cta-actions">
                <a href="<?php echo get_base_url(); ?>/pricing.php" class="csdetail-btn-primary">Get Started</a>
                <a href="<?php echo get_base_url(); ?>/case-studies.php" class="csdetail-btn-outline">More Case Studies</a>
            </div>
        </div>
    </div>
</section>

<style>
/* ============================================
   Case Study Detail Page - Modern Design
   ============================================ */

/* Hero Section */
.csdetail-hero {
    position: relative;
    padding: 60px 0 50px;
    overflow: hidden;
    background: #0f172a;
    color: #fff;
}

.csdetail-hero-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.csdetail-hero-gradient {
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(ellipse 80% 50% at 50% -20%, rgba(34, 197, 94, 0.25) 0%, transparent 50%),
        radial-gradient(ellipse 60% 50% at 100% 100%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
}

.csdetail-hero-orbs {
    position: absolute;
    inset: 0;
    overflow: hidden;
}

.csdetail-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.3;
    animation: csdetail-float 20s ease-in-out infinite;
}

.csdetail-orb-1 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    top: -100px;
    right: 10%;
}

.csdetail-orb-2 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
    bottom: -80px;
    left: 5%;
    animation-delay: -10s;
}

@keyframes csdetail-float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(20px, -20px) scale(1.05); }
}

.csdetail-hero-content {
    position: relative;
    z-index: 1;
    max-width: 900px;
}

.csdetail-back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    margin-bottom: 24px;
    transition: color 0.2s;
}

.csdetail-back-link:hover {
    color: #fff;
}

.csdetail-hero-badge {
    display: inline-block;
    padding: 8px 16px;
    background: rgba(34, 197, 94, 0.2);
    border: 1px solid rgba(34, 197, 94, 0.3);
    color: #4ade80;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 50px;
    margin-bottom: 20px;
}

.csdetail-hero-title {
    font-size: clamp(28px, 4vw, 44px);
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 20px;
}

.csdetail-hero-client {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    color: rgba(255, 255, 255, 0.8);
}

.csdetail-hero-client svg {
    color: rgba(255, 255, 255, 0.5);
}

/* Cover Image */
.csdetail-cover {
    padding: 0;
    margin-top: -30px;
    position: relative;
    z-index: 2;
}

.csdetail-cover-wrapper {
    max-width: 900px;
    margin: 0 auto;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.csdetail-cover-wrapper img {
    width: 100%;
    height: auto;
    display: block;
}

/* Content Section */
.csdetail-content {
    padding: 80px 0;
    background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
}

.csdetail-content-wrapper {
    max-width: 800px;
    margin: 0 auto;
}

.csdetail-section {
    margin-bottom: 48px;
}

.csdetail-section:last-child {
    margin-bottom: 0;
}

.csdetail-section-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}

.csdetail-section-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    flex-shrink: 0;
}

.csdetail-section-icon svg {
    width: 24px;
    height: 24px;
    stroke: #fff;
}

.csdetail-icon-challenge {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.csdetail-icon-solution {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.csdetail-icon-results {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
}

.csdetail-section-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.csdetail-section-body {
    padding-left: 64px;
}

.csdetail-section-body p {
    font-size: 16px;
    line-height: 1.8;
    color: #475569;
    margin: 0 0 16px;
}

.csdetail-section-body p:last-child {
    margin-bottom: 0;
}

.csdetail-section-body h1,
.csdetail-section-body h2,
.csdetail-section-body h3,
.csdetail-section-body h4 {
    color: #0f172a;
    font-weight: 600;
    margin-top: 24px;
    margin-bottom: 12px;
    line-height: 1.3;
}

.csdetail-section-body ul,
.csdetail-section-body ol {
    margin: 16px 0;
    padding-left: 24px;
}

.csdetail-section-body li {
    margin-bottom: 8px;
    line-height: 1.7;
    color: #475569;
}

.csdetail-section-body a {
    color: #667eea;
    text-decoration: underline;
}

.csdetail-section-body img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 16px 0;
}

.csdetail-section-body blockquote {
    border-left: 4px solid #22c55e;
    padding-left: 16px;
    margin: 16px 0;
    font-style: italic;
    color: #64748b;
}

.csdetail-section-body table {
    width: 100%;
    border-collapse: collapse;
    margin: 16px 0;
}

.csdetail-section-body th,
.csdetail-section-body td {
    padding: 10px;
    border: 1px solid #e5e7eb;
    text-align: left;
}

.csdetail-section-body th {
    background: #f8fafc;
    font-weight: 600;
}

/* Modules */
.csdetail-modules {
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.csdetail-modules h4 {
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0 0 16px;
}

.csdetail-modules-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.csdetail-module-tag {
    display: inline-block;
    padding: 8px 16px;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #475569;
    font-size: 14px;
    font-weight: 500;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

/* CTA Section */
.csdetail-cta {
    padding: 0 0 80px;
    background: #fff;
}

.csdetail-cta-card {
    max-width: 800px;
    margin: 0 auto;
    padding: 48px;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 32px;
}

.csdetail-cta-content h2 {
    font-size: 24px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 8px;
}

.csdetail-cta-content p {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.csdetail-cta-actions {
    display: flex;
    gap: 12px;
    flex-shrink: 0;
}

.csdetail-btn-primary {
    display: inline-flex;
    align-items: center;
    padding: 14px 28px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px rgba(34, 197, 94, 0.4);
}

.csdetail-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(34, 197, 94, 0.5);
}

.csdetail-btn-outline {
    display: inline-flex;
    align-items: center;
    padding: 14px 28px;
    background: transparent;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    border-radius: 12px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    text-decoration: none;
    transition: all 0.3s ease;
}

.csdetail-btn-outline:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
}

/* Error State */
.csdetail-error {
    padding: 120px 0;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}

.csdetail-error-card {
    max-width: 500px;
    margin: 0 auto;
    padding: 60px 40px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    text-align: center;
}

.csdetail-error-icon {
    color: #94a3b8;
    margin-bottom: 24px;
}

.csdetail-error-card h1 {
    font-size: 24px;
    color: #0f172a;
    margin: 0 0 12px;
}

.csdetail-error-card p {
    font-size: 16px;
    color: #64748b;
    margin: 0 0 24px;
}

/* Responsive */
@media (max-width: 768px) {
    .csdetail-hero {
        padding: 40px 0 40px;
    }
    
    .csdetail-hero-title {
        font-size: 26px;
    }
    
    .csdetail-cover {
        margin-top: -20px;
    }
    
    .csdetail-cover-wrapper {
        border-radius: 12px;
        margin: 0 16px;
    }
    
    .csdetail-content {
        padding: 60px 0;
    }
    
    .csdetail-section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .csdetail-section-body {
        padding-left: 0;
    }
    
    .csdetail-section-title {
        font-size: 20px;
    }
    
    .csdetail-cta-card {
        flex-direction: column;
        text-align: center;
        padding: 32px 24px;
    }
    
    .csdetail-cta-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .csdetail-btn-primary,
    .csdetail-btn-outline {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .csdetail-hero-title {
        font-size: 22px;
    }
    
    .csdetail-section-body p {
        font-size: 15px;
    }
}
</style>

<?php include_footer(); ?>
