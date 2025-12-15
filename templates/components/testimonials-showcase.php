<?php
/**
 * Testimonials Showcase Component - People-First Design
 * Shows founder images and info prominently, reveals testimonial on hover
 * 
 * Usage:
 * $testimonials_config = [
 *     'testimonials' => $testimonials,
 *     'title' => 'What Our Customers Say',
 *     'subtitle' => 'Trusted by businesses worldwide',
 *     'theme' => 'dark', // 'dark' or 'light'
 *     'max_items' => 6,
 *     'accent_color' => '#2563eb'
 * ];
 * include __DIR__ . '/components/testimonials-showcase.php';
 */

// Default configuration
$config = $testimonials_config ?? [];
$testimonials = $config['testimonials'] ?? [];
$title = $config['title'] ?? 'What Our Customers Say';
$subtitle = $config['subtitle'] ?? 'Trusted by leading businesses worldwide';
$theme = $config['theme'] ?? 'dark';
$maxItems = $config['max_items'] ?? 6;
$accentColor = $config['accent_color'] ?? '#2563eb';

// Limit testimonials
$testimonials = array_slice($testimonials, 0, $maxItems);

if (empty($testimonials)) {
    return;
}

$themeClass = $theme === 'dark' ? 'tsc-dark' : 'tsc-light';
?>

<section class="testimonials-showcase <?php echo $themeClass; ?>" 
         style="--tsc-accent: <?php echo htmlspecialchars($accentColor); ?>;">
    <div class="tsc-bg-effects">
        <div class="tsc-gradient-orb tsc-orb-1"></div>
        <div class="tsc-gradient-orb tsc-orb-2"></div>
    </div>
    
    <div class="container">
        <!-- Left-aligned thin header -->
        <div class="tsc-header">
            <h2 class="tsc-title"><?php echo htmlspecialchars($title); ?></h2>
            <p class="tsc-subtitle"><?php echo htmlspecialchars($subtitle); ?></p>
        </div>
        
        <!-- Scroll wrapper for horizontal auto-scroll -->
        <div class="tsc-scroll-wrapper">
            <div class="tsc-scroll-container">
                <!-- People-first cards -->
                <div class="tsc-people-grid">
            <?php foreach ($testimonials as $index => $testimonial): ?>
            <div class="tsc-person-card" tabindex="0">
                <!-- Vector art decorative elements -->
                <div class="tsc-card-decor">
                    <svg class="tsc-decor-pattern" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="grad-<?php echo $index; ?>" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:var(--tsc-accent);stop-opacity:0.3" />
                                <stop offset="100%" style="stop-color:var(--tsc-accent);stop-opacity:0.05" />
                            </linearGradient>
                        </defs>
                        <circle cx="80" cy="20" r="40" fill="url(#grad-<?php echo $index; ?>)" />
                        <circle cx="20" cy="80" r="25" fill="url(#grad-<?php echo $index; ?>)" opacity="0.5" />
                    </svg>
                    <div class="tsc-decor-lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                
                <!-- Person info - always visible -->
                <div class="tsc-person-info">
                    <div class="tsc-person-avatar">
                        <?php if (!empty($testimonial['customer_image'])): ?>
                        <img src="<?php echo htmlspecialchars($testimonial['customer_image']); ?>" 
                             alt="<?php echo htmlspecialchars($testimonial['customer_name']); ?>"
                             loading="lazy">
                        <?php else: ?>
                        <div class="tsc-avatar-placeholder">
                            <?php echo strtoupper(substr($testimonial['customer_name'], 0, 1)); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <h3 class="tsc-person-name"><?php echo htmlspecialchars($testimonial['customer_name']); ?></h3>
                    <?php if (!empty($testimonial['customer_title']) || !empty($testimonial['customer_company'])): ?>
                    <p class="tsc-person-title">
                        <?php 
                        $titleParts = array_filter([
                            $testimonial['customer_title'] ?? '',
                            $testimonial['customer_company'] ?? ''
                        ]);
                        echo htmlspecialchars(implode(' at ', $titleParts));
                        ?>
                    </p>
                    <?php endif; ?>
                </div>
                
                <!-- Testimonial content - revealed on hover -->
                <div class="tsc-testimonial-reveal">
                    <div class="tsc-reveal-inner">
                        <!-- Rating stars -->
                        <?php if (!empty($testimonial['rating'])): ?>
                        <div class="tsc-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="tsc-star <?php echo $i <= $testimonial['rating'] ? 'filled' : ''; ?>">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </span>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Quote -->
                        <blockquote class="tsc-quote">
                            <?php echo htmlspecialchars($testimonial['testimonial_text']); ?>
                        </blockquote>
                        
                        <!-- Quote icon - opening quotes -->
                        <div class="tsc-quote-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Glass effect slider controls -->
        <div class="tsc-slider-controls">
            <button class="tsc-slider-btn tsc-prev-btn" aria-label="Previous testimonial">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            
            <div class="tsc-slider-progress">
                <?php 
                $dotCount = min(count($testimonials), 5);
                for ($i = 0; $i < $dotCount; $i++): 
                ?>
                <span class="tsc-progress-dot <?php echo $i === 0 ? 'active' : ''; ?>"></span>
                <?php endfor; ?>
            </div>
            
            <button class="tsc-slider-btn tsc-play-pause" aria-label="Pause auto-scroll">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                </svg>
            </button>
            
            <button class="tsc-slider-btn tsc-next-btn" aria-label="Next testimonial">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>
    </div>
</section>
