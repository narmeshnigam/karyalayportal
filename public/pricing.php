<?php
/**
 * SellerPortal System
 * Pricing Page with Duration Filtering - Modern Design
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

use Karyalay\Services\PlanService;

$planService = new PlanService();
$selectedDuration = $_GET['duration'] ?? 'quarterly';

try {
    $filters = ['status' => 'ACTIVE'];
    $filters['duration'] = $selectedDuration;
    $plans = $planService->findAll($filters);
    $plansByDuration = $planService->getPlansByDuration();
} catch (Exception $e) {
    error_log('Error fetching plans: ' . $e->getMessage());
    $plans = [];
    $plansByDuration = ['monthly' => [], 'quarterly' => [], 'annual' => [], 'other' => []];
}

$totalPlans = count($plansByDuration['monthly']) + count($plansByDuration['quarterly']) + count($plansByDuration['annual']) + count($plansByDuration['other']);

$page_title = 'Pricing';
$page_description = 'Choose the perfect plan for your business needs';
include_header($page_title, $page_description);
?>

<!-- Hero Section - Dark Modern Style -->
<section class="pricing-hero-v2">
    <div class="pricing-hero-bg">
        <div class="pricing-hero-gradient"></div>
        <div class="pricing-hero-pattern"></div>
        <div class="pricing-hero-orbs">
            <div class="pricing-orb pricing-orb-1"></div>
            <div class="pricing-orb pricing-orb-2"></div>
            <div class="pricing-orb pricing-orb-3"></div>
        </div>
    </div>
    <div class="container">
        <div class="pricing-hero-content">
            <h1 class="pricing-hero-title">Simple, Transparent <span class="pricing-hero-title-highlight">Pricing</span></h1>
            <p class="pricing-hero-subtitle">Choose the plan that fits your business needs. All plans include core features and 24/7 support.</p>
        </div>
    </div>
</section>

<!-- Duration Filter Tabs -->
<section class="pricing-filter-section">
    <div class="container">
        <div class="pricing-duration-tabs">
            <a href="?duration=monthly" class="duration-tab <?php echo $selectedDuration === 'monthly' ? 'active' : ''; ?>">
                <span>Monthly</span>
                <?php if (count($plansByDuration['monthly']) > 0): ?><span class="tab-count"><?php echo count($plansByDuration['monthly']); ?></span><?php endif; ?>
            </a>
            <a href="?duration=quarterly" class="duration-tab <?php echo $selectedDuration === 'quarterly' ? 'active' : ''; ?>">
                <span>Quarterly</span>
                <?php if (count($plansByDuration['quarterly']) > 0): ?><span class="tab-count"><?php echo count($plansByDuration['quarterly']); ?></span><?php endif; ?>
            </a>
            <a href="?duration=annual" class="duration-tab <?php echo $selectedDuration === 'annual' ? 'active' : ''; ?>">
                <span>Annual</span>
                <?php if (count($plansByDuration['annual']) > 0): ?><span class="tab-count"><?php echo count($plansByDuration['annual']); ?></span><?php endif; ?>
            </a>
        </div>
    </div>
</section>

<!-- Pricing Cards Section -->
<section class="pricing-cards-section">
    <div class="container">
        <?php if (empty($plans)): ?>
            <div class="pricing-empty">
                <div class="pricing-empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h3>No Plans Available</h3>
                <p>No plans found for the selected duration. Try selecting a different category.</p>
                <div class="pricing-empty-actions">
                    <a href="?duration=all" class="btn btn-primary">View All Plans</a>
                </div>
            </div>
        <?php else: ?>
            <div class="pricing-grid">
                <?php foreach ($plans as $index => $plan): ?>
                    <?php 
                    $hasDiscount = $planService->hasDiscount($plan);
                    $discountPct = $planService->getDiscountPercentage($plan);
                    $effectivePrice = $planService->getEffectivePrice($plan);
                    $isFeatured = $index === 1 && count($plans) > 2;
                    $months = (int)$plan['billing_period_months'];
                    $durationLabel = match($months) {
                        1 => 'month',
                        3 => 'quarter',
                        6 => '6 months',
                        12 => 'year',
                        default => $months . ' months'
                    };
                    
                    // Gradient colors for cards
                    $gradients = [
                        ['#667eea', '#764ba2'],
                        ['#f093fb', '#f5576c'],
                        ['#4facfe', '#00f2fe'],
                        ['#43e97b', '#38f9d7'],
                        ['#fa709a', '#fee140'],
                    ];
                    $gradient = $gradients[$index % count($gradients)];
                    ?>
                    <article class="pricing-card <?php echo $isFeatured ? 'pricing-card-featured' : ''; ?>" style="--gradient-start: <?php echo $gradient[0]; ?>; --gradient-end: <?php echo $gradient[1]; ?>;">
                        <div class="pricing-card-glow"></div>
                        
                        <?php if ($isFeatured): ?>
                            <div class="pricing-card-badge">Most Popular</div>
                        <?php endif; ?>
                        
                        <?php if ($hasDiscount): ?>
                            <div class="pricing-discount-ribbon"><?php echo $discountPct; ?>% OFF</div>
                        <?php endif; ?>
                        
                        <div class="pricing-card-header">
                            <h3 class="pricing-card-title"><?php echo htmlspecialchars($plan['name']); ?></h3>
                            <?php if (!empty($plan['description'])): ?>
                                <p class="pricing-card-description"><?php echo htmlspecialchars($plan['description']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pricing-card-price">
                            <?php if ($hasDiscount): ?>
                                <div class="pricing-mrp">
                                    <span><?php echo format_price($plan['mrp'], false); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="pricing-price-wrapper">
                                <span class="pricing-currency"><?php echo get_currency_symbol(); ?></span>
                                <span class="pricing-amount"><?php echo number_format($effectivePrice, 0); ?></span>
                            </div>
                            <span class="pricing-period">/ <?php echo $durationLabel; ?></span>
                        </div>

                        <!-- Plan Limits -->
                        <div class="pricing-limits">
                            <div class="limit-item">
                                <svg class="limit-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span><?php echo !empty($plan['number_of_users']) ? $plan['number_of_users'] . ' Users' : 'Unlimited Users'; ?></span>
                            </div>
                            <div class="limit-item">
                                <svg class="limit-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path></svg>
                                <span><?php echo !empty($plan['allowed_storage_gb']) ? $plan['allowed_storage_gb'] . ' GB Storage' : 'Unlimited Storage'; ?></span>
                            </div>
                        </div>
                        
                        <?php if (!empty($plan['features_html'])): ?>
                            <div class="pricing-card-features pricing-features-rich">
                                <?php echo $plan['features_html']; ?>
                            </div>
                        <?php elseif (!empty($plan['features']) && is_array($plan['features'])): ?>
                            <div class="pricing-card-features">
                                <p class="pricing-features-label">What's included:</p>
                                <ul class="pricing-features-list">
                                    <?php foreach ($plan['features'] as $feature): ?>
                                        <li class="pricing-feature-item">
                                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span><?php echo htmlspecialchars($feature); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <div class="pricing-card-action">
                            <?php if (isAuthenticated()): ?>
                                <form method="POST" action="<?php echo get_base_url(); ?>/select-plan.php">
                                    <input type="hidden" name="plan_slug" value="<?php echo htmlspecialchars($plan['slug']); ?>">
                                    <button type="submit" class="btn-pricing <?php echo $isFeatured ? 'btn-pricing-featured' : ''; ?>">Buy Now</button>
                                </form>
                            <?php else: ?>
                                <a href="<?php echo get_base_url(); ?>/register.php?plan=<?php echo urlencode($plan['slug']); ?>" class="btn-pricing <?php echo $isFeatured ? 'btn-pricing-featured' : ''; ?>">Get Started</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- FAQ Section -->
<section class="pricing-faq-section">
    <div class="container">
        <div class="pricing-faq-header">
            <span class="pricing-section-label">Have Questions?</span>
            <h2 class="pricing-section-title">Frequently Asked Questions</h2>
            <p class="pricing-section-subtitle">Everything you need to know about our pricing and plans</p>
        </div>
        <div class="pricing-faq-grid">
            <div class="pricing-faq-item">
                <div class="pricing-faq-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
                <h3 class="pricing-faq-question">Can I change my plan later?</h3>
                <p class="pricing-faq-answer">Yes, you can upgrade or downgrade your plan at any time. Changes will be reflected in your next billing cycle.</p>
            </div>
            <div class="pricing-faq-item">
                <div class="pricing-faq-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <h3 class="pricing-faq-question">What payment methods do you accept?</h3>
                <p class="pricing-faq-answer">We accept all major credit cards, debit cards, and online payment methods through our secure payment gateway.</p>
            </div>
            <div class="pricing-faq-item">
                <div class="pricing-faq-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="pricing-faq-question">Is there a free trial?</h3>
                <p class="pricing-faq-answer">Contact us to discuss trial options for your business. We're happy to provide a demo of our platform.</p>
            </div>
            <div class="pricing-faq-item">
                <div class="pricing-faq-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
                <h3 class="pricing-faq-question">Do you offer custom plans?</h3>
                <p class="pricing-faq-answer">Yes! Contact us to discuss a custom solution tailored to your specific requirements and budget.</p>
            </div>
        </div>
    </div>
</section>

<?php
$cta_title = "Still Have Questions?";
$cta_subtitle = "Our team is here to help you choose the right plan";
$cta_source = "pricing-page";
include __DIR__ . '/../templates/cta-form.php';
?>

<style>
/* ============================================
   Pricing Page - Modern Design
   ============================================ */

/* Hero Section */
.pricing-hero-v2 {
    position: relative;
    height: 30vh;
    min-height: 200px;
    max-height: 30vh;
    padding: 0;
    overflow: hidden;
    background: #0f172a;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pricing-hero-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.pricing-hero-gradient {
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(ellipse 80% 50% at 50% -20%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(ellipse 60% 50% at 0% 100%, rgba(37, 99, 235, 0.2) 0%, transparent 50%),
        radial-gradient(ellipse 40% 40% at 100% 50%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);
}

.pricing-hero-pattern {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.02;
}

.pricing-hero-orbs {
    position: absolute;
    inset: 0;
    overflow: hidden;
}

.pricing-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.4;
    animation: pricing-float 20s ease-in-out infinite;
}

.pricing-orb-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    top: -100px;
    right: 10%;
    animation-delay: 0s;
}

.pricing-orb-2 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    bottom: -50px;
    left: 5%;
    animation-delay: -7s;
}

.pricing-orb-3 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    top: 40%;
    left: 30%;
    animation-delay: -14s;
}

@keyframes pricing-float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(20px, -30px) scale(1.05); }
    50% { transform: translate(-10px, 20px) scale(0.95); }
    75% { transform: translate(30px, 10px) scale(1.02); }
}

.pricing-hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.pricing-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 12px;
    backdrop-filter: blur(10px);
}

.pricing-hero-badge svg {
    color: #10b981;
}

.pricing-hero-title {
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 12px;
    white-space: nowrap;
}

.pricing-hero-title span {
    display: inline;
}

.pricing-hero-title-highlight {
    background: linear-gradient(135deg, #10b981 0%, #4facfe 50%, #667eea 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.pricing-hero-subtitle {
    font-size: 16px;
    line-height: 1.5;
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    white-space: nowrap;
}

.pricing-hero-stats {
    display: inline-flex;
    align-items: center;
    gap: 32px;
    padding: 20px 40px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    backdrop-filter: blur(10px);
}

.pricing-hero-stat {
    text-align: center;
}

.pricing-hero-stat-value {
    display: block;
    font-size: 28px;
    font-weight: 700;
    color: #fff;
    line-height: 1;
    margin-bottom: 4px;
}

.pricing-hero-stat-label {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pricing-hero-stat-divider {
    width: 1px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
}

/* Duration Filter Tabs */
.pricing-filter-section {
    background: #fff;
    padding: 24px 0;
    border-bottom: 1px solid #e5e7eb;
    position: sticky;
    top: 64px;
    z-index: 100;
}

.pricing-duration-tabs {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}

.duration-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
    background: #f1f5f9;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.duration-tab:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.duration-tab.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
}

.tab-count {
    font-size: 12px;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 10px;
    border-radius: 50px;
}

.duration-tab:not(.active) .tab-count {
    background: rgba(0, 0, 0, 0.08);
}

.duration-tab.active .tab-count {
    background: rgba(255, 255, 255, 0.25);
}

/* Pricing Cards Section */
.pricing-cards-section {
    padding: 80px 0;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 32px;
    max-width: 1200px;
    margin: 0 auto;
    justify-items: center;
}

/* Pricing Card */
.pricing-card {
    position: relative;
    background: #fff;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 380px;
    overflow: hidden;
}

.pricing-card-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.pricing-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
    border-color: transparent;
}

.pricing-card:hover .pricing-card-glow {
    opacity: 1;
}

.pricing-card-featured {
    border-color: #667eea;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);
    transform: scale(1.02);
}

.pricing-card-featured:hover {
    transform: scale(1.02) translateY(-12px);
}

.pricing-card-badge {
    position: absolute;
    top: -1px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 8px 24px;
    border-radius: 0 0 12px 12px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pricing-discount-ribbon {
    position: absolute;
    top: 20px;
    right: -32px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 6px 40px;
    font-size: 12px;
    font-weight: 700;
    transform: rotate(45deg);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
}

/* Card Header */
.pricing-card-header {
    text-align: center;
    margin-bottom: 24px;
    padding-top: 16px;
}

.pricing-card-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 8px;
}

.pricing-card-description {
    font-size: 14px;
    color: #64748b;
    margin: 0;
    line-height: 1.5;
}

/* Card Price */
.pricing-card-price {
    text-align: center;
    padding: 24px 0;
    margin-bottom: 20px;
    border-bottom: 2px solid #f1f5f9;
}

.pricing-mrp {
    margin-bottom: 8px;
}

.pricing-mrp span {
    font-size: 16px;
    color: #94a3b8;
    text-decoration: line-through;
}

.pricing-price-wrapper {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    gap: 4px;
}

.pricing-currency {
    font-size: 20px;
    font-weight: 600;
    color: #64748b;
    margin-top: 8px;
}

.pricing-amount {
    font-size: 56px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1;
    letter-spacing: -2px;
}

.pricing-period {
    font-size: 14px;
    color: #64748b;
    display: block;
    margin-top: 8px;
}

/* Plan Limits */
.pricing-limits {
    display: flex;
    justify-content: center;
    gap: 24px;
    padding: 16px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 12px;
    margin-bottom: 24px;
}

.limit-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 500;
    color: #475569;
}

.limit-icon {
    width: 18px;
    height: 18px;
    color: var(--gradient-start, #667eea);
}

/* Card Features */
.pricing-card-features {
    flex: 1;
    margin-bottom: 24px;
}

.pricing-features-label {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0 0 16px;
}

.pricing-features-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.pricing-feature-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 14px;
    color: #475569;
    margin-bottom: 12px;
    line-height: 1.5;
}

.pricing-feature-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    color: #10b981;
    margin-top: 1px;
}

.pricing-features-rich {
    line-height: 1.6;
    font-size: 14px;
    color: #475569;
}

.pricing-features-rich ul {
    padding-left: 20px;
    margin: 8px 0;
}

.pricing-features-rich li {
    margin-bottom: 8px;
}

/* Card Action */
.pricing-card-action {
    margin-top: auto;
}

.pricing-card-action form {
    margin: 0;
}

.btn-pricing {
    display: block;
    width: 100%;
    padding: 16px 24px;
    font-size: 15px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    background: #fff;
    color: #0f172a;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-pricing:hover {
    background: #f8fafc;
    border-color: var(--gradient-start, #667eea);
    color: var(--gradient-start, #667eea);
}

.btn-pricing-featured {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
}

.btn-pricing-featured:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    background: linear-gradient(135deg, #5a6fd6 0%, #6b2f96 100%);
    color: #fff;
}

/* Empty State */
.pricing-empty {
    text-align: center;
    padding: 80px 40px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    max-width: 500px;
    margin: 0 auto;
}

.pricing-empty-icon {
    color: #94a3b8;
    margin-bottom: 24px;
}

.pricing-empty h3 {
    font-size: 24px;
    color: #0f172a;
    margin: 0 0 12px;
}

.pricing-empty p {
    font-size: 16px;
    color: #64748b;
    margin: 0 0 24px;
}

/* FAQ Section */
.pricing-faq-section {
    padding: 100px 0;
    background: #fff;
}

.pricing-faq-header {
    text-align: center;
    margin-bottom: 60px;
}

.pricing-section-label {
    display: inline-block;
    font-size: 13px;
    font-weight: 700;
    color: #667eea;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 12px;
}

.pricing-section-title {
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 16px;
    letter-spacing: -0.5px;
}

.pricing-section-subtitle {
    font-size: 18px;
    color: #64748b;
    margin: 0;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.pricing-faq-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    max-width: 1000px;
    margin: 0 auto;
}

.pricing-faq-item {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 20px;
    padding: 32px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.pricing-faq-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
    border-color: transparent;
}

.pricing-faq-icon {
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 14px;
    color: #fff;
    margin-bottom: 20px;
}

.pricing-faq-icon svg {
    width: 24px;
    height: 24px;
}

.pricing-faq-question {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 12px;
}

.pricing-faq-answer {
    font-size: 15px;
    color: #64748b;
    line-height: 1.7;
    margin: 0;
}

/* Responsive Styles */
@media (max-width: 1024px) {
    .pricing-faq-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .pricing-hero-v2 {
        height: auto;
        min-height: 150px;
        max-height: none;
        padding: 30px 0;
    }
    
    .pricing-hero-title {
        font-size: 22px;
        white-space: normal;
    }
    
    .pricing-hero-subtitle {
        font-size: 13px;
        white-space: normal;
    }
    
    .pricing-filter-section {
        top: 56px;
        padding: 16px 0;
    }
    
    .pricing-duration-tabs {
        gap: 6px;
    }
    
    .duration-tab {
        padding: 10px 16px;
        font-size: 13px;
    }
    
    .pricing-cards-section {
        padding: 60px 0;
    }
    
    .pricing-grid {
        grid-template-columns: 1fr;
        padding: 0 16px;
        gap: 24px;
    }
    
    .pricing-card {
        max-width: 100%;
        padding: 24px;
    }
    
    .pricing-card-featured {
        transform: none;
    }
    
    .pricing-card-featured:hover {
        transform: translateY(-12px);
    }
    
    .pricing-amount {
        font-size: 48px;
    }
    
    .pricing-limits {
        flex-direction: column;
        gap: 12px;
    }
    
    .pricing-faq-section {
        padding: 60px 0;
    }
    
    .pricing-faq-header {
        margin-bottom: 40px;
    }
    
    .pricing-faq-item {
        padding: 24px;
    }
}

@media (max-width: 480px) {
    .pricing-hero-title {
        font-size: 18px;
    }
    
    .duration-tab span:first-child {
        display: none;
    }
    
    .duration-tab::before {
        content: attr(data-short);
    }
    
    .pricing-card {
        padding: 20px;
    }
    
    .pricing-card-title {
        font-size: 20px;
    }
    
    .pricing-amount {
        font-size: 42px;
    }
}
</style>

<?php include_footer(); ?>
