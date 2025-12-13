<?php
/**
 * Business Hub Section - Dynamic Feature Showcase
 * Displays categories and nodes from database
 */

use Karyalay\Models\BusinessHubCategory;

// Get dynamic data
$hubModel = new BusinessHubCategory();
$categories = [];

try {
    $categories = $hubModel->getPublishedWithNodes();
} catch (Exception $e) {
    // Fallback to empty - will show nothing if DB not ready
    error_log('Business Hub: ' . $e->getMessage());
}

// Map positions to CSS classes
$positionMap = [
    'top-left' => 'people',
    'top-right' => 'operations', 
    'bottom-left' => 'finance',
    'bottom-right' => 'control'
];

// Build feature map for JavaScript
$featureMap = [];
foreach ($categories as $cat) {
    $slugs = [];
    foreach ($cat['nodes'] as $node) {
        $slugs[] = $node['slug'];
    }
    $featureMap[$cat['color_class']] = $slugs;
}
?>

<!-- Business Hub Section - Animated Feature Showcase -->
<section class="section business-hub-section" aria-label="Business Management Hub">
    <div class="container">
        <h2 class="section-title">One Platform, All Your Business Needs</h2>
        <p class="section-subtitle">
            Manage every aspect of your business from a single, unified hub
        </p>
        
        <?php if (!empty($categories)): ?>
        <!-- Desktop Layout -->
        <div class="business-hub-container" id="businessHub">
            <!-- Central Hub - Square Logo in Blue Circle with Orange Rotating Border -->
            <div class="central-hub">
                <div class="central-hub-inner">
                    <?php 
                    $squareLogoUrl = get_logo_square();
                    $logoUrl = $squareLogoUrl ?: get_logo_light_bg();
                    if ($logoUrl): ?>
                        <img src="<?php echo htmlspecialchars($logoUrl); ?>" 
                             alt="<?php echo htmlspecialchars(get_brand_name()); ?>" 
                             class="central-hub-logo-img">
                    <?php else: ?>
                        <span class="central-hub-text"><?php echo htmlspecialchars(get_brand_name()); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Wing Nodes (Categories) -->
            <?php foreach ($categories as $category): 
                $wingClass = 'wing-' . htmlspecialchars($category['color_class']);
                $hasLink = !empty($category['link_url']);
            ?>
            <?php if ($hasLink): ?>
            <a href="<?php echo htmlspecialchars($category['link_url']); ?>" class="wing-node <?php echo $wingClass; ?>" data-wing="<?php echo htmlspecialchars($category['color_class']); ?>">
            <?php else: ?>
            <div class="wing-node <?php echo $wingClass; ?>" data-wing="<?php echo htmlspecialchars($category['color_class']); ?>">
            <?php endif; ?>
                <span class="wing-node-title">
                    <?php echo htmlspecialchars($category['title']); ?>
                    <?php if (!empty($category['title_line2'])): ?>
                        <br><?php echo htmlspecialchars($category['title_line2']); ?>
                    <?php endif; ?>
                </span>
            <?php if ($hasLink): ?>
            </a>
            <?php else: ?>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            
            <!-- Feature Labels (Nodes) -->
            <?php 
            /**
             * Quadrant-based positioning system - TIGHT RADIUS
             * 
             * Category circles are at quadrant centers: (25%, 25%), (75%, 25%), etc.
             * Feature nodes are placed CLOSE to their category circles (~8-10% offset)
             * to appear within ~1cm on a 13" screen.
             * 
             * The radius is kept small so nodes orbit tightly around their category.
             * Different positions (top/bottom/left/right) have slightly different offsets
             * to account for visual balance.
             */
            
            // Expanded positioning - nodes pushed 1.5x farther from category circles
            // Calculated by taking the offset from center and multiplying by 1.5
            $positionMaps = [
                'people' => [ // Top Left quadrant - center at (25%, 25%)
                    ['top' => '7%', 'left' => '25%'],    // Top: 25-18=7 (was 13%, offset was 12%, new offset 18%)
                    ['top' => '8.5%', 'left' => '41.5%'], // Top-right: expanded 1.5x
                    ['top' => '35.5%', 'left' => '41.5%'], // Bottom-right: expanded 1.5x  
                    ['top' => '43%', 'left' => '25%'],   // Bottom: expanded 1.5x
                    ['top' => '35.5%', 'left' => '8.5%'], // Bottom-left: expanded 1.5x
                    ['top' => '8.5%', 'left' => '8.5%'], // Top-left: expanded 1.5x
                ],
                'operations' => [ // Top Right quadrant - center at (75%, 25%)
                    ['top' => '7%', 'left' => '75%'],    // Top: expanded 1.5x
                    ['top' => '8.5%', 'left' => '91.5%'], // Top-right: expanded 1.5x
                    ['top' => '35.5%', 'left' => '91.5%'], // Bottom-right: expanded 1.5x
                    ['top' => '43%', 'left' => '75%'],   // Bottom: expanded 1.5x
                    ['top' => '35.5%', 'left' => '58.5%'], // Bottom-left: expanded 1.5x
                    ['top' => '8.5%', 'left' => '58.5%'], // Top-left: expanded 1.5x
                ],
                'finance' => [ // Bottom Left quadrant - center at (25%, 75%)
                    ['top' => '57%', 'left' => '25%'],   // Top: expanded 1.5x
                    ['top' => '59.5%', 'left' => '41.5%'], // Top-right: expanded 1.5x
                    ['top' => '85.5%', 'left' => '41.5%'], // Bottom-right: expanded 1.5x
                    ['top' => '93%', 'left' => '25%'],   // Bottom: expanded 1.5x
                    ['top' => '85.5%', 'left' => '8.5%'], // Bottom-left: expanded 1.5x
                    ['top' => '59.5%', 'left' => '8.5%'], // Top-left: expanded 1.5x
                ],
                'control' => [ // Bottom Right quadrant - center at (75%, 75%)
                    ['top' => '57%', 'left' => '75%'],   // Top: expanded 1.5x
                    ['top' => '59.5%', 'left' => '91.5%'], // Top-right: expanded 1.5x
                    ['top' => '85.5%', 'left' => '91.5%'], // Bottom-right: expanded 1.5x
                    ['top' => '93%', 'left' => '75%'],   // Bottom: expanded 1.5x
                    ['top' => '85.5%', 'left' => '58.5%'], // Bottom-left: expanded 1.5x
                    ['top' => '59.5%', 'left' => '58.5%'], // Top-left: expanded 1.5x
                ],
            ];
            
            $globalLabelIndex = 0;
            foreach ($categories as $category): 
                $colorClass = $category['color_class'];
                $positions = $positionMaps[$colorClass] ?? $positionMaps['people'];
                
                foreach ($category['nodes'] as $nodeIndex => $node):
                    $labelClass = 'label-' . htmlspecialchars($node['slug']);
                    $hasNodeLink = !empty($node['link_url']);
                    $animDelay = 0.6 + ($globalLabelIndex * 0.1);
                    
                    // Get position for this node within its category
                    $pos = $positions[$nodeIndex % count($positions)];
                    $posStyle = '';
                    foreach ($pos as $prop => $val) {
                        $posStyle .= "$prop: $val; ";
                    }
            ?>
            <?php if ($hasNodeLink): ?>
            <a href="<?php echo htmlspecialchars($node['link_url']); ?>" 
               class="feature-label <?php echo $labelClass; ?> feature-<?php echo $colorClass; ?>"
               style="<?php echo $posStyle; ?>animation-delay: <?php echo $animDelay; ?>s !important;"
               data-category="<?php echo htmlspecialchars($colorClass); ?>">
                <?php echo htmlspecialchars($node['title']); ?>
            </a>
            <?php else: ?>
            <span class="feature-label <?php echo $labelClass; ?> feature-<?php echo $colorClass; ?>"
                  style="<?php echo $posStyle; ?>animation-delay: <?php echo $animDelay; ?>s !important;"
                  data-category="<?php echo htmlspecialchars($colorClass); ?>">
                <?php echo htmlspecialchars($node['title']); ?>
            </span>
            <?php endif; ?>
            <?php 
                    $globalLabelIndex++;
                endforeach;
            endforeach; 
            ?>
        </div>
        
        <!-- Mobile Layout -->
        <div class="wing-nodes-mobile" style="display: none;">
            <?php foreach ($categories as $category): 
                $wingClass = 'wing-' . htmlspecialchars($category['color_class']);
                $hasLink = !empty($category['link_url']);
            ?>
            <div class="wing-node <?php echo $wingClass; ?>">
                <?php if ($hasLink): ?>
                <a href="<?php echo htmlspecialchars($category['link_url']); ?>" class="wing-node-title-link">
                <?php endif; ?>
                <span class="wing-node-title">
                    <?php echo htmlspecialchars($category['title']); ?>
                    <?php if (!empty($category['title_line2'])): ?>
                        <?php echo htmlspecialchars($category['title_line2']); ?>
                    <?php endif; ?>
                </span>
                <?php if ($hasLink): ?>
                </a>
                <?php endif; ?>
                <div class="wing-features-mobile">
                    <?php foreach ($category['nodes'] as $node): ?>
                        <?php if (!empty($node['link_url'])): ?>
                        <a href="<?php echo htmlspecialchars($node['link_url']); ?>"><?php echo htmlspecialchars($node['title']); ?></a>
                        <?php else: ?>
                        <span><?php echo htmlspecialchars($node['title']); ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <!-- Fallback when no data -->
        <div class="business-hub-empty">
            <p>Business hub content is being configured.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hub = document.getElementById('businessHub');
    if (!hub) return;
    
    // Feature map from PHP
    const featureMap = <?php echo json_encode($featureMap); ?>;
    
    /**
     * Dynamic node positioning based on screen resolution
     * Keeps nodes within ~1cm of their category circles on any screen
     * 
     * On a 13" MacBook (2560x1600 or 1440x900 scaled), we want nodes
     * to appear approximately 1cm from category circles.
     * 1cm ≈ 38px at standard resolution
     */
    function adjustNodePositions() {
        const containerWidth = hub.offsetWidth;
        const containerHeight = hub.offsetHeight;
        
        // Get device pixel ratio for high-DPI screens
        const dpr = window.devicePixelRatio || 1;
        
        // Calculate optimal radius based on container size and screen density
        // Target: ~1cm visual distance (roughly 38-50px on standard screens)
        // For smaller containers, use tighter radius
        const containerMin = Math.min(containerWidth, containerHeight);
        
        // Base radius as percentage of container - increased by 1.5x
        let radiusPercent;
        if (containerMin < 500) {
            radiusPercent = 0.21; // 0.14 * 1.5 = 0.21
        } else if (containerMin < 600) {
            radiusPercent = 0.195; // 0.13 * 1.5 = 0.195
        } else if (containerMin < 700) {
            radiusPercent = 0.18; // 0.12 * 1.5 = 0.18
        } else {
            radiusPercent = 0.165; // 0.11 * 1.5 = 0.165
        }
        
        // Calculate actual radius in pixels, then clamp - increased by 1.5x
        const baseRadius = containerMin * radiusPercent;
        const minRadius = 82; // 55 * 1.5 = 82.5
        const maxRadius = 135; // 90 * 1.5 = 135
        const radius = Math.max(minRadius, Math.min(maxRadius, baseRadius));
        
        // Category centers (as percentages)
        const centers = {
            'people': { x: 25, y: 25 },
            'operations': { x: 75, y: 25 },
            'finance': { x: 25, y: 75 },
            'control': { x: 75, y: 75 }
        };
        
        // Get all feature labels and reposition them
        const labels = hub.querySelectorAll('.feature-label');
        labels.forEach((label, index) => {
            const category = label.dataset.category;
            if (!category || !centers[category]) return;
            
            const center = centers[category];
            const categoryLabels = hub.querySelectorAll(`.feature-${category}`);
            const labelIndex = Array.from(categoryLabels).indexOf(label);
            const totalLabels = categoryLabels.length;
            
            // Calculate angle for this label (distribute evenly around circle)
            // Start from top (-90deg) and go clockwise
            const angleStep = (2 * Math.PI) / Math.max(totalLabels, 6);
            const angle = -Math.PI / 2 + (labelIndex * angleStep);
            
            // Convert radius from pixels to percentage of container
            const radiusX = (radius / containerWidth) * 100;
            const radiusY = (radius / containerHeight) * 100;
            
            // Calculate position
            const x = center.x + radiusX * Math.cos(angle);
            const y = center.y + radiusY * Math.sin(angle);
            
            // Apply position
            label.style.left = `${x}%`;
            label.style.top = `${y}%`;
        });
    }
    
    // Run on load and resize
    adjustNodePositions();
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(adjustNodePositions, 100);
    });
    
    // Intersection Observer for scroll-triggered animations (Desktop)
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('hub-visible');
            }
        });
    }, { threshold: 0.2 });
    
    observer.observe(hub);
    
    // Wing hover effects - highlight connected features
    const wings = document.querySelectorAll('#businessHub .wing-node');
    wings.forEach(wing => {
        wing.addEventListener('mouseenter', function() {
            const wingType = this.dataset.wing;
            highlightFeatures(wingType, true);
        });
        
        wing.addEventListener('mouseleave', function() {
            const wingType = this.dataset.wing;
            highlightFeatures(wingType, false);
        });
    });
    
    function highlightFeatures(wingType, highlight) {
        const features = featureMap[wingType] || [];
        features.forEach(feature => {
            const label = document.querySelector('.label-' + feature);
            if (label) {
                if (highlight) {
                    label.style.transform = 'translate(-50%, -50%) scale(1.15)';
                    label.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
                } else {
                    label.style.transform = '';
                    label.style.boxShadow = '';
                }
            }
        });
    }
    
    // Mobile cascade animation with scroll
    function initMobileAnimation() {
        const mobileLayout = document.querySelector('.wing-nodes-mobile');
        if (!mobileLayout) return;
        
        const mobileCards = mobileLayout.querySelectorAll('.wing-node');
        if (mobileCards.length === 0) return;
        
        // Track visibility state for each card
        const cardStates = new Array(mobileCards.length).fill(false);
        
        // Create intersection observer for each card with cascade effect
        const mobileObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const card = entry.target;
                const index = Array.from(mobileCards).indexOf(card);
                
                if (entry.isIntersecting) {
                    // Card coming into view - smooth cascade from hub/previous card
                    cardStates[index] = true;
                    
                    // Animate this card and all previous cards that should be visible
                    for (let i = 0; i <= index; i++) {
                        setTimeout(() => {
                            mobileCards[i].classList.remove('mobile-hidden');
                            mobileCards[i].classList.add('mobile-visible');
                        }, i * 150); // Stagger by 150ms for smoother cascade
                    }
                } else {
                    // Card going out of view
                    const rect = card.getBoundingClientRect();
                    const isScrollingUp = rect.top > window.innerHeight;
                    
                    if (isScrollingUp) {
                        // Scrolling up - hide cards from bottom to top (reverse cascade)
                        cardStates[index] = false;
                        
                        // Hide this card and all cards after it
                        for (let i = mobileCards.length - 1; i >= index; i--) {
                            const delay = (mobileCards.length - 1 - i) * 120;
                            setTimeout(() => {
                                if (!cardStates[i]) {
                                    mobileCards[i].classList.remove('mobile-visible');
                                    mobileCards[i].classList.add('mobile-hidden');
                                }
                            }, delay);
                        }
                    }
                }
            });
        }, { 
            threshold: 0.2,
            rootMargin: '-5% 0px -5% 0px'
        });
        
        // Observe each mobile card
        mobileCards.forEach(card => {
            mobileObserver.observe(card);
        });
        
        // Initial check - show cards already in viewport with smooth stagger
        setTimeout(() => {
            mobileCards.forEach((card, index) => {
                const rect = card.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    for (let i = 0; i <= index; i++) {
                        setTimeout(() => {
                            mobileCards[i].classList.add('mobile-visible');
                        }, i * 150);
                    }
                }
            });
        }, 200);
    }
    
    // Mobile layout toggle
    function handleResize() {
        const mobileLayout = document.querySelector('.wing-nodes-mobile');
        const desktopHub = document.getElementById('businessHub');
        
        if (window.innerWidth <= 768) {
            if (mobileLayout) mobileLayout.style.display = 'flex';
            if (desktopHub) {
                desktopHub.querySelector('.central-hub').style.display = 'flex';
                desktopHub.querySelectorAll('.wing-node').forEach(w => w.style.display = 'none');
                desktopHub.querySelectorAll('.feature-label').forEach(f => f.style.display = 'none');
            }
            // Initialize mobile animation
            initMobileAnimation();
        } else {
            if (mobileLayout) mobileLayout.style.display = 'none';
            if (desktopHub) {
                desktopHub.querySelectorAll('.wing-node').forEach(w => w.style.display = 'flex');
                desktopHub.querySelectorAll('.feature-label').forEach(f => f.style.display = 'block');
            }
        }
    }
    
    handleResize();
    window.addEventListener('resize', handleResize);
});
</script>
