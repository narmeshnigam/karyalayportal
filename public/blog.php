<?php

/**
 * SellerPortal System
 * Blog Index Page - Modern Design
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

use Karyalay\Models\BlogPost;

try {
    $blogPostModel = new BlogPost();
    $blogPosts = $blogPostModel->findAll(['status' => 'PUBLISHED']);
} catch (Exception $e) {
    error_log('Error fetching blog posts: ' . $e->getMessage());
    $blogPosts = [];
}

$page_title = 'Blog';
$page_description = 'Insights, tips, and updates from the team';

include_header($page_title, $page_description);
?>

<!-- Hero Section - Compact Dark Style -->
<section class="blog-hero">
    <div class="blog-hero-bg">
        <div class="blog-hero-gradient"></div>
        <div class="blog-hero-orbs">
            <div class="blog-orb blog-orb-1"></div>
            <div class="blog-orb blog-orb-2"></div>
            <div class="blog-orb blog-orb-3"></div>
        </div>
    </div>
    <div class="container">
        <div class="blog-hero-content">
            <h1 class="blog-hero-title">Insights & <span class="blog-hero-highlight">Updates</span></h1>
            <p class="blog-hero-subtitle">Tips, guides, and news to help you get the most out of our platform</p>
        </div>
    </div>
</section>

<!-- Blog Posts Section -->
<section class="blog-section">
    <div class="container">
        <?php if (empty($blogPosts)): ?>
            <div class="blog-empty">
                <div class="blog-empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                </div>
                <h3>No Posts Yet</h3>
                <p>Check back soon for new content!</p>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($blogPosts as $index => $post): 
                    $gradients = [
                        ['#667eea', '#764ba2'],
                        ['#f093fb', '#f5576c'],
                        ['#4facfe', '#00f2fe'],
                        ['#43e97b', '#38f9d7'],
                        ['#fa709a', '#fee140'],
                    ];
                    $gradient = $gradients[$index % count($gradients)];
                    $isFeatured = $index === 0;
                ?>
                <article class="blog-card <?php echo $isFeatured ? 'blog-card-featured' : ''; ?>" style="--gradient-start: <?php echo $gradient[0]; ?>; --gradient-end: <?php echo $gradient[1]; ?>;">
                    <div class="blog-card-glow"></div>
                    
                    <?php if (!empty($post['featured_image'])): ?>
                        <div class="blog-card-image">
                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>"
                                 loading="lazy">
                        </div>
                    <?php elseif ($isFeatured): ?>
                        <div class="blog-card-image blog-card-image-placeholder">
                            <div class="blog-card-placeholder-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="blog-card-content">
                        <?php if (!empty($post['tags']) && is_array($post['tags'])): ?>
                            <div class="blog-card-tags">
                                <?php foreach (array_slice($post['tags'], 0, 2) as $tag): ?>
                                    <span class="blog-card-tag"><?php echo htmlspecialchars($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <h2 class="blog-card-title">
                            <a href="<?php echo get_base_url(); ?>/blog/<?php echo urlencode($post['slug']); ?>">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h2>
                        
                        <?php if (!empty($post['published_at'])): ?>
                            <div class="blog-card-date">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($post['excerpt'])): ?>
                            <p class="blog-card-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        <?php endif; ?>
                        
                        <a href="<?php echo get_base_url(); ?>/blog/<?php echo urlencode($post['slug']); ?>" class="blog-card-link">
                            <span>Read Article</span>
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
$cta_title = "Want to Stay Updated?";
$cta_subtitle = "Get the latest insights and updates delivered to your inbox";
$cta_source = "blog-page";
include __DIR__ . '/../templates/cta-form.php';
?>

<style>
/* ============================================
   Blog Page - Modern Design
   ============================================ */

/* Hero Section - Compact */
.blog-hero {
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

.blog-hero-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.blog-hero-gradient {
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(ellipse 80% 50% at 50% -20%, rgba(102, 126, 234, 0.3) 0%, transparent 50%),
        radial-gradient(ellipse 60% 50% at 0% 100%, rgba(240, 147, 251, 0.2) 0%, transparent 50%),
        radial-gradient(ellipse 40% 40% at 100% 50%, rgba(79, 172, 254, 0.15) 0%, transparent 50%);
}

.blog-hero-orbs {
    position: absolute;
    inset: 0;
    overflow: hidden;
}

.blog-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.4;
    animation: blog-float 20s ease-in-out infinite;
}

.blog-orb-1 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    top: -80px;
    right: 15%;
    animation-delay: 0s;
}

.blog-orb-2 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    bottom: -40px;
    left: 10%;
    animation-delay: -7s;
}

.blog-orb-3 {
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    top: 30%;
    left: 25%;
    animation-delay: -14s;
}

@keyframes blog-float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(20px, -30px) scale(1.05); }
    50% { transform: translate(-10px, 20px) scale(0.95); }
    75% { transform: translate(30px, 10px) scale(1.02); }
}

.blog-hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

.blog-hero-title {
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 12px;
    white-space: nowrap;
}

.blog-hero-highlight {
    background: linear-gradient(135deg, #667eea 0%, #f093fb 50%, #4facfe 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.blog-hero-subtitle {
    font-size: 16px;
    line-height: 1.5;
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

/* Blog Section */
.blog-section {
    padding: 80px 0;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}

/* Blog Grid */
.blog-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Featured card spans 2 columns */
.blog-card-featured {
    grid-column: span 2;
    display: grid;
    grid-template-columns: 1fr 1fr;
}

/* Blog Card */
.blog-card {
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

.blog-card-glow {
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

.blog-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
    border-color: transparent;
}

.blog-card:hover .blog-card-glow {
    opacity: 1;
}

/* Card Image */
.blog-card-image {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
}

.blog-card-featured .blog-card-image {
    height: 100%;
    min-height: 300px;
}

.blog-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.blog-card:hover .blog-card-image img {
    transform: scale(1.08);
}

.blog-card-image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--gradient-start, #667eea), var(--gradient-end, #764ba2));
}

.blog-card-placeholder-icon {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
}

.blog-card-placeholder-icon svg {
    width: 40px;
    height: 40px;
    stroke: #fff;
}

/* Card Content */
.blog-card-content {
    padding: 24px;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.blog-card-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.blog-card-tag {
    display: inline-block;
    padding: 4px 12px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    color: var(--gradient-start, #667eea);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 50px;
}

.blog-card-title {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 12px;
    line-height: 1.3;
}

.blog-card-featured .blog-card-title {
    font-size: 24px;
}

.blog-card-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s;
}

.blog-card-title a:hover {
    color: var(--gradient-start, #667eea);
}

.blog-card-date {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #94a3b8;
    margin-bottom: 16px;
}

.blog-card-date svg {
    width: 16px;
    height: 16px;
}

.blog-card-excerpt {
    font-size: 14px;
    color: #64748b;
    line-height: 1.6;
    margin: 0 0 20px;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-card-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--gradient-start, #667eea);
    text-decoration: none;
    margin-top: auto;
    transition: all 0.3s ease;
}

.blog-card-link svg {
    width: 18px;
    height: 18px;
    transition: transform 0.3s ease;
}

.blog-card:hover .blog-card-link {
    gap: 12px;
}

.blog-card:hover .blog-card-link svg {
    transform: translateX(4px);
}

/* Empty State */
.blog-empty {
    text-align: center;
    padding: 80px 40px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    max-width: 500px;
    margin: 0 auto;
}

.blog-empty-icon {
    color: #94a3b8;
    margin-bottom: 24px;
}

.blog-empty h3 {
    font-size: 24px;
    color: #0f172a;
    margin: 0 0 12px;
}

.blog-empty p {
    font-size: 16px;
    color: #64748b;
    margin: 0;
}

/* Responsive Styles */
@media (max-width: 1024px) {
    .blog-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
    
    .blog-card-featured {
        grid-column: span 2;
    }
}

@media (max-width: 768px) {
    .blog-hero {
        height: auto;
        min-height: 150px;
        max-height: none;
        padding: 40px 0;
    }
    
    .blog-hero-title {
        font-size: 24px;
        white-space: normal;
    }
    
    .blog-hero-subtitle {
        font-size: 14px;
    }
    
    .blog-section {
        padding: 60px 0;
    }
    
    .blog-grid {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 0 16px;
    }
    
    .blog-card-featured {
        grid-column: span 1;
        display: flex;
        flex-direction: column;
    }
    
    .blog-card-featured .blog-card-image {
        height: 200px;
        min-height: auto;
    }
    
    .blog-card-image {
        height: 180px;
    }
    
    .blog-card-content {
        padding: 20px;
    }
    
    .blog-card-title {
        font-size: 18px;
    }
    
    .blog-card-featured .blog-card-title {
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .blog-hero-title {
        font-size: 20px;
    }
    
    .blog-card-image {
        height: 160px;
    }
}
</style>

<?php include_footer(); ?>
