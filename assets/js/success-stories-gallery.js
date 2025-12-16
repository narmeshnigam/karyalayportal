/**
 * Success Stories Gallery - Scroll-Synced Horizontal Animation
 * Scrolls the gallery horizontally based on page scroll position
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSuccessStoriesGallery);
    } else {
        initSuccessStoriesGallery();
    }
    
    function initSuccessStoriesGallery() {
        const gallery = document.querySelector('.success-stories-gallery');
        const track = document.querySelector('.success-stories-track');
        
        if (!gallery || !track) {
            return;
        }
        
        // Check for reduced motion preference
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReducedMotion) {
            return;
        }
        
        // Calculate scroll parameters
        let trackWidth = 0;
        let maxScroll = 0;
        
        function calculateDimensions() {
            const cards = track.querySelectorAll('.success-story-card');
            const viewAll = track.querySelector('.success-stories-view-all');
            trackWidth = 0;
            
            cards.forEach(card => {
                trackWidth += card.offsetWidth;
            });
            
            // Add view all button width if present
            if (viewAll) {
                trackWidth += viewAll.offsetWidth;
            }
            
            // Maximum scroll distance (track width minus viewport width)
            maxScroll = Math.max(0, trackWidth - window.innerWidth);
        }
        
        // Initial calculation
        calculateDimensions();
        
        // Recalculate on resize (debounced)
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(calculateDimensions, 250);
        });
        
        // Scroll handler - syncs horizontal scroll with vertical page scroll
        function handleScroll() {
            const galleryRect = gallery.getBoundingClientRect();
            const galleryTop = galleryRect.top;
            const galleryHeight = gallery.offsetHeight;
            
            // Get header height (64px desktop, 56px mobile)
            const headerHeight = window.innerWidth <= 768 ? 56 : 64;
            const stickyHeight = window.innerHeight - headerHeight;
            
            // Calculate the scroll range within the gallery section
            const scrollRange = galleryHeight - stickyHeight;
            
            // Dead zone threshold - delay before horizontal scroll starts (15% of scroll range)
            // This keeps the first card visible for a few scroll ticks
            const deadZoneThreshold = 0.15;
            
            // Sticky starts when gallery top reaches header bottom
            if (galleryTop <= headerHeight && galleryTop >= -(scrollRange - headerHeight)) {
                // Gallery is in the sticky zone - calculate progress
                const rawProgress = (headerHeight - galleryTop) / scrollRange;
                
                // Apply dead zone - no movement until past threshold
                let adjustedProgress = 0;
                if (rawProgress > deadZoneThreshold) {
                    // Remap progress: 0 starts after dead zone, 1 at end
                    adjustedProgress = (rawProgress - deadZoneThreshold) / (1 - deadZoneThreshold);
                }
                
                // Clamp between 0 and 1
                const clampedProgress = Math.max(0, Math.min(1, adjustedProgress));
                
                // Calculate horizontal translation
                // Scroll left (negative) as user scrolls down
                const translateX = -(clampedProgress * maxScroll);
                
                // Apply transform
                track.style.transform = `translateX(${translateX}px)`;
            } else if (galleryTop > headerHeight) {
                // Before sticky zone - reset to start
                track.style.transform = 'translateX(0px)';
            } else {
                // After sticky zone - keep at end
                track.style.transform = `translateX(-${maxScroll}px)`;
            }
        }
        
        // Use requestAnimationFrame for smooth animation
        let ticking = false;
        
        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        }
        
        // Listen to scroll events
        window.addEventListener('scroll', requestTick, { passive: true });
        
        // Initial call
        handleScroll();
        
        // Make cards keyboard accessible
        const cards = track.querySelectorAll('.success-story-card');
        cards.forEach(card => {
            // Add tabindex for keyboard navigation
            card.setAttribute('tabindex', '0');
            
            // Add keyboard event listeners
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    // Toggle hover state on keyboard activation
                    card.classList.toggle('keyboard-active');
                    
                    // If Enter, navigate to the link
                    if (e.key === 'Enter') {
                        const link = card.querySelector('.success-story-btn');
                        if (link) {
                            link.click();
                        }
                    }
                }
            });
            
            card.addEventListener('blur', function() {
                card.classList.remove('keyboard-active');
            });
        });
    }
})();
