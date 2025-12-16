/**
 * Blog Gallery - Horizontal Scroll with Drag Support
 * Modern scrollable gallery with navigation controls
 */

(function() {
    'use strict';
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBlogGallery);
    } else {
        initBlogGallery();
    }
    
    function initBlogGallery() {
        const gallery = document.querySelector('.blog-gallery');
        const track = document.querySelector('.blog-gallery-track');
        const prevBtn = document.querySelector('.blog-gallery-btn-prev');
        const nextBtn = document.querySelector('.blog-gallery-btn-next');
        const dotsContainer = document.querySelector('.blog-gallery-dots');
        
        if (!gallery || !track) return;
        
        // Drag to scroll functionality
        let isDown = false;
        let startX;
        let scrollLeft;
        
        track.addEventListener('mousedown', (e) => {
            isDown = true;
            track.classList.add('grabbing');
            startX = e.pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
        });
        
        track.addEventListener('mouseleave', () => {
            isDown = false;
            track.classList.remove('grabbing');
        });
        
        track.addEventListener('mouseup', () => {
            isDown = false;
            track.classList.remove('grabbing');
        });
        
        track.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - track.offsetLeft;
            const walk = (x - startX) * 2;
            track.scrollLeft = scrollLeft - walk;
        });
        
        // Touch support
        track.addEventListener('touchstart', (e) => {
            startX = e.touches[0].pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
        }, { passive: true });
        
        track.addEventListener('touchmove', (e) => {
            const x = e.touches[0].pageX - track.offsetLeft;
            const walk = (x - startX) * 1.5;
            track.scrollLeft = scrollLeft - walk;
        }, { passive: true });
        
        // Navigation buttons
        const scrollAmount = 420; // Card width + gap
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        }
        
        // Progress dots
        if (dotsContainer) {
            const cards = track.querySelectorAll('.blog-gallery-card');
            const totalCards = cards.length;
            const dotsCount = Math.min(totalCards, 5); // Max 5 dots
            
            // Create dots
            for (let i = 0; i < dotsCount; i++) {
                const dot = document.createElement('span');
                dot.className = 'blog-gallery-dot' + (i === 0 ? ' active' : '');
                dot.setAttribute('data-index', i);
                dot.addEventListener('click', () => {
                    const scrollTo = (track.scrollWidth / dotsCount) * i;
                    track.scrollTo({ left: scrollTo, behavior: 'smooth' });
                });
                dotsContainer.appendChild(dot);
            }
            
            // Update active dot on scroll
            const dots = dotsContainer.querySelectorAll('.blog-gallery-dot');
            
            track.addEventListener('scroll', () => {
                const scrollProgress = track.scrollLeft / (track.scrollWidth - track.clientWidth);
                const activeIndex = Math.min(
                    Math.floor(scrollProgress * dotsCount),
                    dotsCount - 1
                );
                
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === activeIndex);
                });
            }, { passive: true });
        }
        
        // Keyboard navigation
        track.setAttribute('tabindex', '0');
        track.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        });
    }
})();
