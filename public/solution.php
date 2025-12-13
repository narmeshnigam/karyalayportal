<?php
/**
 * Solution Detail Page - Modern ERP Office Management
 * A beautifully designed page showcasing solution details with linked features
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

$page_title = htmlspecialchars($solution['name']);
$page_description = htmlspecialchars($solution['description'] ?? '');
$colorTheme = $solution['color_theme'] ?? '#667eea';

include_header($page_title, $page_description);
?>

<!-- Hero Section with Animated Background -->
<section class="solution-hero" style="--theme-color: <?php echo htmlspecialchars($colorTheme); ?>">
    <div class="hero-bg-pattern"></div>
    <div class="hero-gradient-orb hero-orb-1"></div>
    <div class="hero-gradient-orb hero-orb-2"></div>
    
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo get_base_url(); ?>/">Home</a>
            <svg class="breadcrumb-sep" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <a href="<?php echo get_base_url(); ?>/solutions.php">Solutions</a>
            <svg class="breadcrumb-sep" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <span aria-current="page"><?php echo htmlspecialchars($solution['name']); ?></span>
        </nav>
        
        <div class="hero-content">
            <div class="hero-text animate-fade-up">
                <?php if (!empty($solution['icon_image'])): ?>
                    <div class="hero-icon">
                        <img src="<?php echo htmlspecialchars($solution['icon_image']); ?>" alt="">
                    </div>
                <?php else: ?>
                    <div class="hero-icon hero-icon-default">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($solution['tagline'])): ?>
                    <p class="hero-tagline"><?php echo htmlspecialchars($solution['tagline']); ?></p>
                <?php endif; ?>
                
                <h1 class="hero-title"><?php echo htmlspecialchars($solution['name']); ?></h1>
                
                <?php if (!empty($solution['description'])): ?>
                    <p class="hero-description"><?php echo htmlspecialchars($solution['description']); ?></p>
                <?php endif; ?>
                
                <div class="hero-actions">
                    <a href="#contact-form" class="btn btn-primary btn-lg">
                        Get Started
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#features" class="btn btn-outline-light btn-lg">
                        Explore Features
                    </a>
                </div>
            </div>
            
            <?php if (!empty($solution['hero_image'])): ?>
                <div class="hero-visual animate-fade-up" style="animation-delay: 0.2s">
                    <img src="<?php echo htmlspecialchars($solution['hero_image']); ?>" alt="<?php echo htmlspecialchars($solution['name']); ?>">
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<?php if (!empty($linkedFeatures)): ?>
<section id="features" class="solution-features-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Key Features</h2>
            <p class="section-subtitle">Powerful capabilities designed to meet your business needs</p>
        </div>
        
        <div class="features-grid">
            <?php foreach ($linkedFeatures as $feature): ?>
                <div class="feature-card">
                    <div class="feature-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title"><?php echo htmlspecialchars($feature['name']); ?></h3>
                    <p class="feature-card-description"><?php echo htmlspecialchars($feature['description']); ?></p>
                    <a href="<?php echo get_base_url(); ?>/feature/<?php echo urlencode($feature['slug']); ?>" class="feature-card-link">
                        Learn More →
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Related Solutions -->
<?php if (!empty($relatedSolutions)): ?>
<section class="related-solutions-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Related Solutions</h2>
            <p class="section-subtitle">Explore other solutions that complement this offering</p>
        </div>
        
        <div class="solutions-grid">
            <?php foreach ($relatedSolutions as $relatedSolution): ?>
                <div class="solution-card">
                    <?php if (!empty($relatedSolution['icon_image'])): ?>
                        <div class="solution-card-icon">
                            <img src="<?php echo htmlspecialchars($relatedSolution['icon_image']); ?>" alt="">
                        </div>
                    <?php endif; ?>
                    <h3 class="solution-card-title"><?php echo htmlspecialchars($relatedSolution['name']); ?></h3>
                    <p class="solution-card-description"><?php echo htmlspecialchars($relatedSolution['description']); ?></p>
                    <a href="<?php echo get_base_url(); ?>/solution/<?php echo urlencode($relatedSolution['slug']); ?>" class="solution-card-link">
                        View Solution →
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<div id="contact-form">
<?php
$cta_title = "Ready to Get Started with " . htmlspecialchars($solution['name']) . "?";
$cta_subtitle = "Contact us today to learn how this solution can transform your business operations";
$cta_source = "solution-" . $slug;
include __DIR__ . '/../templates/cta-form.php';
?>
</div>

<style>
/* Solution Hero Styles */
.solution-hero {
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    padding: var(--spacing-20) 0 var(--spacing-16) 0;
    position: relative;
    overflow: hidden;
    color: var(--color-white);
}

.hero-bg-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
    pointer-events: none;
}

.hero-gradient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.3;
    pointer-events: none;
}

.hero-orb-1 {
    width: 400px;
    height: 400px;
    background: rgba(255, 255, 255, 0.2);
    top: -100px;
    left: -100px;
}

.hero-orb-2 {
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.15);
    bottom: -50px;
    right: -50px;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
    margin-bottom: var(--spacing-8);
    font-size: var(--font-size-sm);
    position: relative;
    z-index: 1;
}

.breadcrumb a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb a:hover {
    color: var(--color-white);
}

.breadcrumb-sep {
    width: 16px;
    height: 16px;
    fill: none;
    stroke: rgba(255, 255, 255, 0.5);
    stroke-width: 2;
}

.breadcrumb span {
    color: var(--color-white);
}

.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-12);
    align-items: center;
    position: relative;
    z-index: 1;
}

.hero-icon {
    width: 80px;
    height: 80px;
    margin-bottom: var(--spacing-4);
}

.hero-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.hero-icon-default {
    background: rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-icon-default svg {
    width: 48px;
    height: 48px;
    stroke: var(--color-white);
}

.hero-tagline {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(255, 255, 255, 0.9);
    margin: 0 0 var(--spacing-3) 0;
}

.hero-title {
    font-size: var(--font-size-5xl);
    font-weight: var(--font-weight-bold);
    line-height: 1.1;
    margin: 0 0 var(--spacing-5) 0;
}

.hero-description {
    font-size: var(--font-size-lg);
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.95);
    margin: 0 0 var(--spacing-8) 0;
}

.hero-actions {
    display: flex;
    gap: var(--spacing-4);
    flex-wrap: wrap;
}

.hero-visual img {
    width: 100%;
    height: auto;
    border-radius: var(--radius-2xl);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.animate-fade-up {
    animation: fadeUp 0.8s ease-out;
}

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Features Section */
.solution-features-section {
    padding: var(--spacing-16) 0;
    background: var(--color-gray-50);
}

.section-header {
    text-align: center;
    margin-bottom: var(--spacing-12);
}

.section-title {
    font-size: var(--font-size-3xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-gray-900);
    margin: 0 0 var(--spacing-3) 0;
}

.section-subtitle {
    font-size: var(--font-size-lg);
    color: var(--color-gray-600);
    margin: 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-6);
}

.feature-card {
    background: var(--color-white);
    padding: var(--spacing-8);
    border-radius: var(--radius-xl);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

.feature-card-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--theme-color, #667eea) 0%, #764ba2 100%);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--spacing-4);
}

.feature-card-icon svg {
    width: 28px;
    height: 28px;
    stroke: var(--color-white);
}

.feature-card-title {
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-semibold);
    color: var(--color-gray-900);
    margin: 0 0 var(--spacing-3) 0;
}

.feature-card-description {
    font-size: var(--font-size-base);
    color: var(--color-gray-600);
    line-height: 1.6;
    margin: 0 0 var(--spacing-4) 0;
}

.feature-card-link {
    color: var(--theme-color, #667eea);
    text-decoration: none;
    font-weight: var(--font-weight-semibold);
    font-size: var(--font-size-sm);
    transition: color 0.2s;
}

.feature-card-link:hover {
    color: #764ba2;
}

/* Related Solutions */
.related-solutions-section {
    padding: var(--spacing-16) 0;
    background: var(--color-white);
}

.solutions-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-6);
}

.solution-card {
    background: var(--color-gray-50);
    padding: var(--spacing-8);
    border-radius: var(--radius-xl);
    transition: all 0.3s;
}

.solution-card:hover {
    background: var(--color-white);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

.solution-card-icon {
    width: 64px;
    height: 64px;
    margin-bottom: var(--spacing-4);
}

.solution-card-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.solution-card-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--color-gray-900);
    margin: 0 0 var(--spacing-3) 0;
}

.solution-card-description {
    font-size: var(--font-size-base);
    color: var(--color-gray-600);
    line-height: 1.6;
    margin: 0 0 var(--spacing-4) 0;
}

.solution-card-link {
    color: var(--color-primary);
    text-decoration: none;
    font-weight: var(--font-weight-semibold);
    font-size: var(--font-size-sm);
    transition: color 0.2s;
}

.solution-card-link:hover {
    color: var(--color-primary-dark);
}

/* Not Found Card */
.not-found-card {
    text-align: center;
    padding: var(--spacing-16) var(--spacing-8);
    background: var(--color-white);
    border-radius: var(--radius-2xl);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.not-found-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto var(--spacing-6) auto;
    color: var(--color-gray-400);
}

.not-found-icon svg {
    width: 100%;
    height: 100%;
}

.not-found-card h1 {
    font-size: var(--font-size-3xl);
    color: var(--color-gray-900);
    margin: 0 0 var(--spacing-4) 0;
}

.not-found-card p {
    font-size: var(--font-size-lg);
    color: var(--color-gray-600);
    margin: 0 0 var(--spacing-8) 0;
}

/* Responsive */
@media (max-width: 1024px) {
    .hero-content {
        grid-template-columns: 1fr;
    }
    
    .features-grid,
    .solutions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .solution-hero {
        padding: var(--spacing-12) 0 var(--spacing-10) 0;
    }
    
    .hero-title {
        font-size: var(--font-size-3xl);
    }
    
    .hero-description {
        font-size: var(--font-size-base);
    }
    
    .hero-actions {
        flex-direction: column;
    }
    
    .hero-actions .btn {
        width: 100%;
        justify-content: center;
    }
    
    .features-grid,
    .solutions-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include_footer(); ?>
