<?php

/**
 * SellerPortal System
 * Blog Post Detail Page - Modern Design
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
use Karyalay\Models\User;

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: ' . get_base_url() . '/blog.php');
    exit;
}

try {
    $blogPostModel = new BlogPost();
    $post = $blogPostModel->findBySlug($slug);
    
    if (!$post || $post['status'] !== 'PUBLISHED') {
        header('HTTP/1.0 404 Not Found');
        $page_title = 'Post Not Found';
        include_header($page_title, '');
        ?>
        <section class="blogpost-error">
            <div class="container">
                <div class="blogpost-error-card">
                    <div class="blogpost-error-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1>Post Not Found</h1>
                    <p>The blog post you're looking for doesn't exist or is no longer available.</p>
                    <a href="<?php echo get_base_url(); ?>/blog.php" class="blogpost-btn-primary">View All Posts</a>
                </div>
            </div>
        </section>
        <?php
        include_footer();
        exit;
    }
    
    $author = null;
    if (!empty($post['author_id'])) {
        $userModel = new User();
        $author = $userModel->findById($post['author_id']);
    }
    
} catch (Exception $e) {
    error_log('Error fetching blog post: ' . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    $page_title = 'Error';
    include_header($page_title, '');
    ?>
    <section class="blogpost-error">
        <div class="container">
            <div class="blogpost-error-card">
                <h1>Error</h1>
                <p>An error occurred while loading the blog post. Please try again later.</p>
                <a href="<?php echo get_base_url(); ?>/blog.php" class="blogpost-btn-primary">View All Posts</a>
            </div>
        </div>
    </section>
    <?php
    include_footer();
    exit;
}

$page_title = htmlspecialchars($post['title']);
$page_description = htmlspecialchars($post['excerpt']);

include_header($page_title, $page_description);
?>

<!-- Hero Section -->
<section class="blogpost-hero">
    <div class="blogpost-hero-bg">
        <div class="blogpost-hero-gradient"></div>
        <div class="blogpost-hero-orbs">
            <div class="blogpost-orb blogpost-orb-1"></div>
            <div class="blogpost-orb blogpost-orb-2"></div>
        </div>
    </div>
    <div class="container">
        <div class="blogpost-hero-content">
            <a href="<?php echo get_base_url(); ?>/blog.php" class="blogpost-back-link">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                All Posts
            </a>
            
            <?php if (!empty($post['tags']) && is_array($post['tags'])): ?>
                <div class="blogpost-hero-tags">
                    <?php foreach ($post['tags'] as $tag): ?>
                        <span class="blogpost-hero-tag"><?php echo htmlspecialchars($tag); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <h1 class="blogpost-hero-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="blogpost-hero-meta">
                <?php if ($author): ?>
                    <div class="blogpost-author">
                        <div class="blogpost-author-avatar">
                            <?php echo strtoupper(substr($author['name'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($author['name']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($post['published_at'])): ?>
                    <div class="blogpost-date">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <?php echo date('F j, Y', strtotime($post['published_at'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Featured Image -->
<?php if (!empty($post['featured_image'])): ?>
<section class="blogpost-cover">
    <div class="container">
        <div class="blogpost-cover-wrapper">
            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                 alt="<?php echo htmlspecialchars($post['title']); ?>">
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Content Section -->
<section class="blogpost-content">
    <div class="container">
        <article class="blogpost-article">
            <?php echo $post['content']; ?>
        </article>
    </div>
</section>

<!-- CTA Section -->
<section class="blogpost-cta">
    <div class="container">
        <div class="blogpost-cta-card">
            <div class="blogpost-cta-content">
                <h2>Enjoyed this article?</h2>
                <p>Explore more insights and updates on our blog</p>
            </div>
            <div class="blogpost-cta-actions">
                <a href="<?php echo get_base_url(); ?>/blog.php" class="blogpost-btn-primary">More Articles</a>
                <a href="<?php echo get_base_url(); ?>/pricing.php" class="blogpost-btn-outline">Get Started</a>
            </div>
        </div>
    </div>
</section>

<style>
/* ============================================
   Blog Post Detail Page - Modern Design
   ============================================ */

/* Hero Section */
.blogpost-hero {
    position: relative;
    padding: 60px 0 50px;
    overflow: hidden;
    background: #0f172a;
    color: #fff;
}

.blogpost-hero-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.blogpost-hero-gradient {
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(ellipse 80% 50% at 50% -20%, rgba(102, 126, 234, 0.25) 0%, transparent 50%),
        radial-gradient(ellipse 60% 50% at 100% 100%, rgba(240, 147, 251, 0.15) 0%, transparent 50%);
}

.blogpost-hero-orbs {
    position: absolute;
    inset: 0;
    overflow: hidden;
}

.blogpost-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.3;
    animation: blogpost-float 20s ease-in-out infinite;
}

.blogpost-orb-1 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    top: -100px;
    right: 10%;
}

.blogpost-orb-2 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    bottom: -80px;
    left: 5%;
    animation-delay: -10s;
}

@keyframes blogpost-float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(20px, -20px) scale(1.05); }
}

.blogpost-hero-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    margin: 0 auto;
}

.blogpost-back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    margin-bottom: 24px;
    transition: color 0.2s;
}

.blogpost-back-link:hover {
    color: #fff;
}

.blogpost-hero-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 20px;
}

.blogpost-hero-tag {
    display: inline-block;
    padding: 6px 14px;
    background: rgba(102, 126, 234, 0.2);
    border: 1px solid rgba(102, 126, 234, 0.3);
    color: #a5b4fc;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 50px;
}

.blogpost-hero-title {
    font-size: clamp(28px, 4vw, 44px);
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 24px;
}

.blogpost-hero-meta {
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}

.blogpost-author {
    display: flex;
    align-items: center;
    gap: 10px;
}

.blogpost-author-avatar {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    font-size: 14px;
    font-weight: 600;
}

.blogpost-author span {
    font-size: 15px;
    color: rgba(255, 255, 255, 0.9);
}

.blogpost-date {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.6);
}

.blogpost-date svg {
    color: rgba(255, 255, 255, 0.4);
}

/* Cover Image */
.blogpost-cover {
    padding: 0;
    margin-top: -30px;
    position: relative;
    z-index: 2;
}

.blogpost-cover-wrapper {
    max-width: 900px;
    margin: 0 auto;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.blogpost-cover-wrapper img {
    width: 100%;
    height: auto;
    display: block;
}

/* Content Section */
.blogpost-content {
    padding: 80px 0;
    background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
}

.blogpost-article {
    max-width: 720px;
    margin: 0 auto;
    font-size: 17px;
    line-height: 1.9;
    color: #374151;
}

.blogpost-article p {
    margin-bottom: 24px;
}

.blogpost-article h1,
.blogpost-article h2,
.blogpost-article h3,
.blogpost-article h4 {
    color: #0f172a;
    font-weight: 700;
    margin-top: 40px;
    margin-bottom: 16px;
    line-height: 1.3;
}

.blogpost-article h2 { font-size: 28px; }
.blogpost-article h3 { font-size: 22px; }
.blogpost-article h4 { font-size: 18px; }

.blogpost-article ul,
.blogpost-article ol {
    margin-bottom: 24px;
    padding-left: 24px;
}

.blogpost-article li {
    margin-bottom: 8px;
}

.blogpost-article a {
    color: #667eea;
    text-decoration: underline;
}

.blogpost-article a:hover {
    color: #764ba2;
}

.blogpost-article img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 24px 0;
}

.blogpost-article blockquote {
    border-left: 4px solid #667eea;
    padding-left: 20px;
    margin: 24px 0;
    font-style: italic;
    color: #64748b;
}

.blogpost-article pre,
.blogpost-article code {
    background: #f1f5f9;
    border-radius: 6px;
    font-family: monospace;
}

.blogpost-article code {
    padding: 2px 6px;
    font-size: 14px;
}

.blogpost-article pre {
    padding: 16px;
    overflow-x: auto;
    margin-bottom: 24px;
}

.blogpost-article pre code {
    padding: 0;
    background: none;
}

.blogpost-article table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 24px;
}

.blogpost-article th,
.blogpost-article td {
    padding: 12px;
    border: 1px solid #e5e7eb;
    text-align: left;
}

.blogpost-article th {
    background: #f8fafc;
    font-weight: 600;
}

/* CTA Section */
.blogpost-cta {
    padding: 0 0 80px;
    background: #fff;
}

.blogpost-cta-card {
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

.blogpost-cta-content h2 {
    font-size: 24px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 8px;
}

.blogpost-cta-content p {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.blogpost-cta-actions {
    display: flex;
    gap: 12px;
    flex-shrink: 0;
}

.blogpost-btn-primary {
    display: inline-flex;
    align-items: center;
    padding: 14px 28px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
}

.blogpost-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.blogpost-btn-outline {
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

.blogpost-btn-outline:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
}

/* Error State */
.blogpost-error {
    padding: 120px 0;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}

.blogpost-error-card {
    max-width: 500px;
    margin: 0 auto;
    padding: 60px 40px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    text-align: center;
}

.blogpost-error-icon {
    color: #94a3b8;
    margin-bottom: 24px;
}

.blogpost-error-card h1 {
    font-size: 24px;
    color: #0f172a;
    margin: 0 0 12px;
}

.blogpost-error-card p {
    font-size: 16px;
    color: #64748b;
    margin: 0 0 24px;
}

/* Responsive */
@media (max-width: 768px) {
    .blogpost-hero {
        padding: 40px 0 40px;
    }
    
    .blogpost-hero-title {
        font-size: 26px;
    }
    
    .blogpost-hero-meta {
        gap: 16px;
    }
    
    .blogpost-cover {
        margin-top: -20px;
    }
    
    .blogpost-cover-wrapper {
        border-radius: 12px;
        margin: 0 16px;
    }
    
    .blogpost-content {
        padding: 60px 0;
    }
    
    .blogpost-article {
        font-size: 16px;
        padding: 0 16px;
    }
    
    .blogpost-cta-card {
        flex-direction: column;
        text-align: center;
        padding: 32px 24px;
    }
    
    .blogpost-cta-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .blogpost-btn-primary,
    .blogpost-btn-outline {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .blogpost-hero-title {
        font-size: 22px;
    }
    
    .blogpost-author-avatar {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }
}
</style>

<?php include_footer(); ?>
