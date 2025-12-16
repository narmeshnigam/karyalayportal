<?php
/**
 * Reusable CTA Form Component - Modern Glassy Design
 * 
 * Usage: include this file where you want the CTA to appear
 * Optional variables:
 * - $cta_title: Custom title (default: "Ready to Transform Your Business?")
 * - $cta_subtitle: Custom subtitle
 * - $cta_source: Source identifier for tracking (default: current page)
 * - $cta_theme: 'dark' (default) or 'light' - controls the color scheme
 * - $cta_badge_text: Badge text above title
 * - $cta_form_header: Form section header
 * - $cta_form_header_subtitle: Form section subtitle
 * - $cta_submit_text: Submit button text
 * - $cta_privacy_text: Privacy note text
 * - $cta_accent_color: Accent color for highlights
 * - $cta_btn_gradient_start: Button gradient start color
 * - $cta_btn_gradient_end: Button gradient end color
 */

// Safely get brand name with fallback
try {
    $brandName = get_brand_name();
} catch (\Throwable $e) {
    error_log("CTA Form: Error getting brand name - " . $e->getMessage());
    $brandName = 'SellerPortal';
}

$cta_title = $cta_title ?? "Ready to Transform Your Business?";
$cta_subtitle = $cta_subtitle ?? "Get in touch with us today and discover how " . $brandName . " can streamline your operations";
$cta_source = $cta_source ?? ($_SERVER['REQUEST_URI'] ?? 'unknown');
$cta_theme = $cta_theme ?? 'dark'; // 'dark' or 'light'
$cta_badge_text = $cta_badge_text ?? 'Trusted by 500+ Businesses';
$cta_form_header = $cta_form_header ?? 'Get Started Today';
$cta_form_header_subtitle = $cta_form_header_subtitle ?? 'Fill out the form and we\'ll get back to you shortly';
$cta_submit_text = $cta_submit_text ?? 'Send Message';
$cta_privacy_text = $cta_privacy_text ?? 'Your information is secure and will never be shared';
$cta_accent_color = $cta_accent_color ?? '#10b981';
$cta_btn_gradient_start = $cta_btn_gradient_start ?? '#10b981';
$cta_btn_gradient_end = $cta_btn_gradient_end ?? '#059669';
$cta_badge_bg = $cta_badge_bg ?? '#10b981';
$cta_icon_bg = $cta_icon_bg ?? '#ffffff';
?>

<section class="cta-section-modern <?php echo $cta_theme === 'light' ? 'cta-theme-light' : 'cta-theme-dark'; ?>" style="--cta-accent: <?php echo htmlspecialchars($cta_accent_color); ?>; --cta-btn-start: <?php echo htmlspecialchars($cta_btn_gradient_start); ?>; --cta-btn-end: <?php echo htmlspecialchars($cta_btn_gradient_end); ?>; --cta-badge-bg: <?php echo htmlspecialchars($cta_badge_bg); ?>; --cta-icon-bg: <?php echo htmlspecialchars($cta_icon_bg); ?>;">
    <div class="cta-bg-effects">
        <div class="cta-gradient-orb cta-orb-1"></div>
        <div class="cta-gradient-orb cta-orb-2"></div>
        <div class="cta-pattern-overlay"></div>
    </div>
    
    <div class="cta-container-modern">
        <div class="cta-card-glassy">
            <!-- Left Side: CTA Content -->
            <div class="cta-content-modern">
                <div class="cta-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span><?php echo htmlspecialchars($cta_badge_text); ?></span>
                </div>
                
                <h2 class="cta-title-modern"><?php echo htmlspecialchars($cta_title); ?></h2>
                <p class="cta-subtitle-modern"><?php echo htmlspecialchars($cta_subtitle); ?></p>
                
                <div class="cta-features-grid">
                    <div class="cta-feature-item">
                        <div class="cta-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="cta-feature-text">
                            <strong>Quick Response</strong>
                            <span>Within 24 hours</span>
                        </div>
                    </div>
                    <div class="cta-feature-item">
                        <div class="cta-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                            </svg>
                        </div>
                        <div class="cta-feature-text">
                            <strong>Expert Team</strong>
                            <span>Dedicated support</span>
                        </div>
                    </div>
                    <div class="cta-feature-item">
                        <div class="cta-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="cta-feature-text">
                            <strong>Secure & Reliable</strong>
                            <span>Enterprise-grade</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side: Form -->
            <div class="cta-form-modern">
                <div class="cta-form-header">
                    <h3><?php echo htmlspecialchars($cta_form_header); ?></h3>
                    <p><?php echo htmlspecialchars($cta_form_header_subtitle); ?></p>
                </div>
                
                <form id="ctaForm" class="cta-form-glassy" method="POST" action="<?php echo get_base_url(); ?>/submit-lead.php">
                    <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                    <input type="hidden" name="source" value="<?php echo htmlspecialchars($cta_source); ?>">
                    
                    <div class="cta-form-row">
                        <div class="cta-input-group">
                            <label for="cta-name" class="cta-label">Full Name</label>
                            <div class="cta-input-wrapper">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <input type="text" id="cta-name" name="name" class="cta-input-modern" placeholder="John Doe" required>
                            </div>
                        </div>
                        
                        <div class="cta-input-group">
                            <label for="cta-email" class="cta-label">Email Address</label>
                            <div class="cta-input-wrapper">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                                <input type="email" id="cta-email" name="email" class="cta-input-modern" placeholder="john@company.com" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cta-form-row">
                        <div class="cta-input-group">
                            <label for="cta-phone" class="cta-label">Phone Number</label>
                            <?php 
                            try {
                                echo render_phone_input([
                                    'id' => 'cta-phone',
                                    'name' => 'phone',
                                    'value' => '',
                                    'required' => false,
                                    'class' => 'cta-phone-modern',
                                ]);
                            } catch (\Throwable $e) {
                                error_log("CTA Form: Error rendering phone input - " . $e->getMessage());
                                echo '<div class="cta-input-wrapper">';
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>';
                                echo '<input type="tel" name="phone" class="cta-input-modern" placeholder="+91 98765 43210">';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        
                        <div class="cta-input-group">
                            <label for="cta-company" class="cta-label">Company Name</label>
                            <div class="cta-input-wrapper">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/>
                                </svg>
                                <input type="text" id="cta-company" name="company" class="cta-input-modern" placeholder="Your Company">
                            </div>
                        </div>
                    </div>
                    
                    <div class="cta-input-group cta-input-full">
                        <label for="cta-message" class="cta-label">How can we help?</label>
                        <div class="cta-textarea-wrapper">
                            <textarea id="cta-message" name="message" class="cta-textarea-modern" rows="3" placeholder="Tell us about your project or requirements..."></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="cta-submit-modern">
                        <span class="cta-submit-text"><?php echo htmlspecialchars($cta_submit_text); ?></span>
                        <span class="cta-submit-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                            </svg>
                        </span>
                    </button>
                    
                    <div id="ctaFormMessage" class="cta-message-modern" style="display: none;"></div>
                    
                    <p class="cta-privacy-note">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                        <?php echo htmlspecialchars($cta_privacy_text); ?>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
/* ============================================
   CTA Form - Modern Glassy Design
   Aligned with Solution Page Aesthetics
   ============================================ */

.cta-section-modern {
    position: relative;
    padding: 100px 0;
    background: #0a1628;
    overflow: hidden;
}

/* Background Effects */
.cta-bg-effects {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.cta-gradient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
}

.cta-orb-1 {
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, transparent 70%);
    top: -200px;
    left: -100px;
}

.cta-orb-2 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, transparent 70%);
    bottom: -150px;
    right: -100px;
}

.cta-pattern-overlay {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.02;
}

/* Container */
.cta-container-modern {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    position: relative;
    z-index: 1;
}

/* Main Card - Glassy Effect */
.cta-card-glassy {
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 
        0 40px 80px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

/* Left Content Side */
.cta-content-modern {
    padding: 60px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
}

.cta-content-modern::after {
    content: '';
    position: absolute;
    right: 0;
    top: 10%;
    bottom: 10%;
    width: 1px;
    background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.1), transparent);
}

/* Badge */
.cta-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--cta-badge-bg, #10b981);
    border: 1px solid color-mix(in srgb, var(--cta-badge-bg, #10b981) 80%, black);
    border-radius: 50px;
    color: #ffffff;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 24px;
    width: fit-content;
}

.cta-badge svg {
    width: 16px;
    height: 16px;
}

/* Title & Subtitle */
.cta-title-modern {
    font-size: clamp(28px, 4vw, 40px);
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
    margin: 0 0 16px;
    letter-spacing: -0.5px;
}

.cta-subtitle-modern {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.7);
    line-height: 1.7;
    margin: 0 0 40px;
    max-width: 400px;
}

/* Features Grid */
.cta-features-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cta-feature-item {
    display: flex;
    align-items: center;
    gap: 16px;
}

.cta-feature-icon {
    width: 48px;
    height: 48px;
    background: color-mix(in srgb, var(--cta-icon-bg, #ffffff) 8%, transparent);
    border: 1px solid color-mix(in srgb, var(--cta-icon-bg, #ffffff) 10%, transparent);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cta-feature-icon svg {
    width: 22px;
    height: 22px;
    stroke: var(--cta-accent, #10b981);
}

.cta-feature-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.cta-feature-text strong {
    color: #fff;
    font-size: 15px;
    font-weight: 600;
}

.cta-feature-text span {
    color: rgba(255, 255, 255, 0.5);
    font-size: 13px;
}

/* Right Form Side */
.cta-form-modern {
    padding: 50px;
    background: rgba(255, 255, 255, 0.97);
    position: relative;
}

.cta-form-header {
    margin-bottom: 28px;
}

.cta-form-header h3 {
    font-size: 22px;
    font-weight: 700;
    color: #1a202c;
    margin: 0 0 6px;
}

.cta-form-header p {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

/* Form Layout */
.cta-form-glassy {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cta-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.cta-input-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.cta-input-full {
    grid-column: 1 / -1;
}

/* Labels */
.cta-label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    letter-spacing: 0.3px;
}

/* Input Wrapper with Icon */
.cta-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.cta-input-wrapper svg {
    position: absolute;
    left: 14px;
    width: 18px;
    height: 18px;
    stroke: #9ca3af;
    pointer-events: none;
    transition: stroke 0.2s;
}

.cta-input-wrapper:focus-within svg {
    stroke: var(--cta-accent, #10b981);
}

/* Modern Input Styles */
.cta-input-modern {
    width: 100%;
    padding: 14px 14px 14px 44px;
    font-size: 15px;
    font-family: inherit;
    color: #1a202c;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

.cta-input-modern::placeholder {
    color: #94a3b8;
}

.cta-input-modern:hover {
    border-color: #cbd5e1;
    background: #fff;
}

.cta-input-modern:focus {
    outline: none;
    border-color: var(--cta-accent, #10b981);
    background: #fff;
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--cta-accent, #10b981) 10%, transparent);
}

/* Phone Input Modern Styling */
.cta-phone-modern {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
    transition: all 0.2s ease;
}

.cta-phone-modern:hover {
    border-color: #cbd5e1;
    background: #fff;
}

.cta-phone-modern:focus-within {
    border-color: var(--cta-accent, #10b981);
    background: #fff;
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--cta-accent, #10b981) 10%, transparent);
}

.cta-phone-modern .phone-isd-prefix {
    background: transparent;
    border-right: 1px solid #e2e8f0;
    padding: 14px 12px;
    font-size: 15px;
}

.cta-phone-modern .phone-input-field {
    padding: 14px;
    font-size: 15px;
    background: transparent;
    border: none;
}

.cta-phone-modern .phone-input-field:focus {
    outline: none;
    box-shadow: none;
}

/* Textarea */
.cta-textarea-wrapper {
    position: relative;
}

.cta-textarea-modern {
    width: 100%;
    padding: 14px;
    font-size: 15px;
    font-family: inherit;
    color: #1a202c;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    resize: vertical;
    min-height: 100px;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

.cta-textarea-modern::placeholder {
    color: #94a3b8;
}

.cta-textarea-modern:hover {
    border-color: #cbd5e1;
    background: #fff;
}

.cta-textarea-modern:focus {
    outline: none;
    border-color: var(--cta-accent, #10b981);
    background: #fff;
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--cta-accent, #10b981) 10%, transparent);
}

/* Submit Button */
.cta-submit-modern {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 16px 24px;
    font-size: 16px;
    font-weight: 600;
    font-family: inherit;
    color: #fff;
    background: linear-gradient(135deg, var(--cta-btn-start, #10b981) 0%, var(--cta-btn-end, #059669) 100%);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px color-mix(in srgb, var(--cta-btn-start, #10b981) 35%, transparent);
    margin-top: 8px;
}

.cta-submit-modern:hover {
    background: linear-gradient(135deg, var(--cta-btn-end, #059669) 0%, color-mix(in srgb, var(--cta-btn-end, #059669) 80%, black) 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px color-mix(in srgb, var(--cta-btn-start, #10b981) 40%, transparent);
}

.cta-submit-modern:active {
    transform: translateY(0);
}

.cta-submit-icon {
    display: flex;
    transition: transform 0.3s ease;
}

.cta-submit-icon svg {
    width: 18px;
    height: 18px;
}

.cta-submit-modern:hover .cta-submit-icon {
    transform: translateX(4px);
}

/* Message States */
.cta-message-modern {
    padding: 14px 18px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.cta-message-modern.success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.cta-message-modern.error {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    border: 1px solid #fca5a5;
}

/* Privacy Note */
.cta-privacy-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 12px;
    color: #94a3b8;
    margin: 12px 0 0;
}

.cta-privacy-note svg {
    width: 14px;
    height: 14px;
    stroke: #94a3b8;
}

/* ============================================
   Light Mode Theme
   ============================================ */

.cta-section-modern.cta-theme-light {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.cta-theme-light .cta-orb-1 {
    background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
}

.cta-theme-light .cta-orb-2 {
    background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, transparent 70%);
}

.cta-theme-light .cta-pattern-overlay {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23000000' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.015;
}

.cta-theme-light .cta-card-glassy {
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(0, 0, 0, 0.08);
    box-shadow: 
        0 40px 80px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

.cta-theme-light .cta-content-modern::after {
    background: linear-gradient(to bottom, transparent, rgba(0, 0, 0, 0.08), transparent);
}

.cta-theme-light .cta-badge {
    background: var(--cta-badge-bg, #10b981);
    border-color: color-mix(in srgb, var(--cta-badge-bg, #10b981) 80%, black);
    color: #ffffff;
}

.cta-theme-light .cta-title-modern {
    color: #1a202c;
}

.cta-theme-light .cta-subtitle-modern {
    color: #64748b;
}

.cta-theme-light .cta-feature-icon {
    background: color-mix(in srgb, var(--cta-icon-bg, #10b981) 8%, transparent);
    border-color: color-mix(in srgb, var(--cta-icon-bg, #10b981) 15%, transparent);
}

.cta-theme-light .cta-feature-text strong {
    color: #1a202c;
}

.cta-theme-light .cta-feature-text span {
    color: #64748b;
}

.cta-theme-light .cta-form-modern {
    background: rgba(255, 255, 255, 0.95);
    border-left: 1px solid rgba(0, 0, 0, 0.05);
}

/* ============================================
   Responsive Styles
   ============================================ */

@media (max-width: 1024px) {
    .cta-card-glassy {
        grid-template-columns: 1fr;
    }
    
    .cta-content-modern {
        padding: 50px 40px;
    }
    
    .cta-content-modern::after {
        display: none;
    }
    
    .cta-form-modern {
        padding: 40px;
    }
    
    .cta-features-grid {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .cta-feature-item {
        flex: 1;
        min-width: 180px;
    }
}

@media (max-width: 768px) {
    .cta-section-modern {
        padding: 60px 0;
    }
    
    .cta-content-modern {
        padding: 40px 24px;
    }
    
    .cta-form-modern {
        padding: 32px 24px;
    }
    
    .cta-title-modern {
        font-size: 26px;
    }
    
    .cta-subtitle-modern {
        font-size: 15px;
        margin-bottom: 32px;
    }
    
    .cta-form-row {
        grid-template-columns: 1fr;
    }
    
    .cta-features-grid {
        flex-direction: column;
    }
    
    .cta-feature-item {
        min-width: auto;
    }
}

@media (max-width: 480px) {
    .cta-container-modern {
        padding: 0 16px;
    }
    
    .cta-content-modern {
        padding: 32px 20px;
    }
    
    .cta-form-modern {
        padding: 28px 20px;
    }
    
    .cta-badge {
        font-size: 12px;
        padding: 6px 12px;
    }
    
    .cta-title-modern {
        font-size: 22px;
    }
    
    .cta-feature-icon {
        width: 42px;
        height: 42px;
    }
    
    .cta-feature-icon svg {
        width: 18px;
        height: 18px;
    }
    
    .cta-input-modern,
    .cta-textarea-modern {
        padding: 12px 12px 12px 40px;
        font-size: 14px;
    }
    
    .cta-textarea-modern {
        padding: 12px;
    }
    
    .cta-submit-modern {
        padding: 14px 20px;
        font-size: 15px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctaForm = document.getElementById('ctaForm');
    if (!ctaForm) return;
    
    ctaForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('.cta-submit-modern');
        const messageDiv = document.getElementById('ctaFormMessage');
        const originalText = submitBtn.innerHTML;
        
        // Disable submit button and show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="cta-submit-text">Sending...</span>
            <span class="cta-submit-icon">
                <svg class="cta-spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                    <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
                </svg>
            </span>
        `;
        submitBtn.style.opacity = '0.8';
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server did not return JSON');
            }
            
            const result = await response.json();
            
            if (result.success) {
                messageDiv.className = 'cta-message-modern success';
                messageDiv.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    ${result.message || 'Thank you! We\'ll be in touch soon.'}
                `;
                messageDiv.style.display = 'flex';
                form.reset();
            } else {
                messageDiv.className = 'cta-message-modern error';
                messageDiv.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    ${result.message || 'Something went wrong. Please try again.'}
                `;
                messageDiv.style.display = 'flex';
            }
        } catch (error) {
            console.error('Form submission error:', error);
            messageDiv.className = 'cta-message-modern error';
            messageDiv.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Network error. Please try again.
            `;
            messageDiv.style.display = 'flex';
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            submitBtn.style.opacity = '1';
            
            // Hide message after 6 seconds
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 6000);
        }
    });
});

// Add spinner animation
const spinnerStyle = document.createElement('style');
spinnerStyle.textContent = `
    .cta-spinner {
        animation: cta-spin 1s linear infinite;
    }
    @keyframes cta-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(spinnerStyle);
</script>
