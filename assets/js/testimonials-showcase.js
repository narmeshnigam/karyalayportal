/**
 * Testimonials Showcase Component
 * Handles infinite loop auto-scroll, slider controls, and touch interactions
 */

(function() {
    'use strict';

    function initTestimonialsShowcase() {
        const section = document.querySelector('.testimonials-showcase');
        if (!section) return;

        const scrollContainer = section.querySelector('.tsc-scroll-container');
        const peopleGrid = section.querySelector('.tsc-people-grid');
        const cards = section.querySelectorAll('.tsc-person-card');
        const prevBtn = section.querySelector('.tsc-prev-btn');
        const nextBtn = section.querySelector('.tsc-next-btn');
        const playPauseBtn = section.querySelector('.tsc-play-pause');
        const progressDots = section.querySelectorAll('.tsc-progress-dot');
        
        if (!scrollContainer || !peopleGrid || !cards.length) return;

        // Clone cards for infinite loop
        const originalCards = Array.from(cards);
        const cardCount = originalCards.length;
        
        // Clone all cards and append to create seamless loop
        originalCards.forEach(card => {
            const clone = card.cloneNode(true);
            clone.setAttribute('aria-hidden', 'true');
            clone.classList.add('tsc-clone');
            peopleGrid.appendChild(clone);
        });

        // Get all cards including clones
        const allCards = section.querySelectorAll('.tsc-person-card');

        // Auto-scroll configuration
        let isAutoScrolling = true;
        let scrollSpeed = 0.5; // pixels per frame
        let animationId = null;
        let isPaused = false;

        // Calculate the width of original cards (for loop reset point)
        function getOriginalWidth() {
            let width = 0;
            const gap = 24; // 1.5rem gap
            originalCards.forEach((card, index) => {
                width += card.offsetWidth;
                if (index < cardCount - 1) width += gap;
            });
            return width + gap; // Add one more gap for spacing
        }

        // Auto-scroll animation with infinite loop
        function autoScroll() {
            if (!isAutoScrolling || isPaused) {
                animationId = requestAnimationFrame(autoScroll);
                return;
            }

            scrollContainer.scrollLeft += scrollSpeed;

            // When we've scrolled past all original cards, reset to start seamlessly
            const originalWidth = getOriginalWidth();
            if (scrollContainer.scrollLeft >= originalWidth) {
                scrollContainer.scrollLeft = scrollContainer.scrollLeft - originalWidth;
            }

            updateProgressDots();
            animationId = requestAnimationFrame(autoScroll);
        }

        // Update progress dots based on scroll position
        function updateProgressDots() {
            if (!progressDots.length) return;
            
            const originalWidth = getOriginalWidth();
            const scrollPercent = (scrollContainer.scrollLeft % originalWidth) / originalWidth;
            const activeIndex = Math.min(
                Math.floor(scrollPercent * progressDots.length),
                progressDots.length - 1
            );

            progressDots.forEach((dot, index) => {
                dot.classList.toggle('active', index === activeIndex);
            });
        }

        // Scroll by card width
        function scrollByCard(direction) {
            const cardWidth = originalCards[0].offsetWidth + 24; // card width + gap
            const targetScroll = scrollContainer.scrollLeft + (cardWidth * direction);
            
            scrollContainer.scrollTo({
                left: targetScroll,
                behavior: 'smooth'
            });

            // Temporarily pause auto-scroll after manual navigation
            pauseAutoScroll(3000);
        }

        // Pause auto-scroll temporarily
        function pauseAutoScroll(duration = 3000) {
            isPaused = true;
            setTimeout(() => {
                if (isAutoScrolling) {
                    isPaused = false;
                }
            }, duration);
        }

        // Toggle play/pause
        function togglePlayPause() {
            isAutoScrolling = !isAutoScrolling;
            isPaused = !isAutoScrolling;
            
            if (playPauseBtn) {
                const icon = playPauseBtn.querySelector('svg');
                if (isAutoScrolling) {
                    icon.innerHTML = '<path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>'; // Pause icon
                    playPauseBtn.setAttribute('aria-label', 'Pause auto-scroll');
                } else {
                    icon.innerHTML = '<path d="M8 5v14l11-7z"/>'; // Play icon
                    playPauseBtn.setAttribute('aria-label', 'Play auto-scroll');
                }
            }
        }

        // Event listeners for controls
        if (prevBtn) {
            prevBtn.addEventListener('click', () => scrollByCard(-1));
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => scrollByCard(1));
        }

        if (playPauseBtn) {
            playPauseBtn.addEventListener('click', togglePlayPause);
        }

        // Pause on hover over scroll container
        scrollContainer.addEventListener('mouseenter', () => {
            isPaused = true;
            scrollContainer.classList.add('tsc-paused');
        });

        scrollContainer.addEventListener('mouseleave', () => {
            if (isAutoScrolling) {
                isPaused = false;
            }
            scrollContainer.classList.remove('tsc-paused');
        });

        // Handle touch devices - toggle reveal on tap
        allCards.forEach(card => {
            card.addEventListener('click', function(e) {
                // Only handle on touch devices
                if (!('ontouchstart' in window)) return;
                
                const isRevealed = card.classList.contains('tsc-revealed');
                
                if (!isRevealed) {
                    // Close any other open cards
                    allCards.forEach(c => {
                        if (c !== card) {
                            c.classList.remove('tsc-revealed');
                        }
                    });
                    
                    // Open this card
                    card.classList.add('tsc-revealed');
                } else {
                    // Close this card
                    card.classList.remove('tsc-revealed');
                }
            });
        });

        // Close revealed card when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.tsc-person-card')) {
                allCards.forEach(card => {
                    card.classList.remove('tsc-revealed');
                });
            }
        });

        // Keyboard navigation
        allCards.forEach(card => {
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    card.classList.toggle('tsc-revealed');
                }
                if (e.key === 'Escape') {
                    card.classList.remove('tsc-revealed');
                }
            });
        });

        // Touch/drag scroll handling
        let isDown = false;
        let startX;
        let scrollLeft;

        scrollContainer.addEventListener('mousedown', (e) => {
            isDown = true;
            scrollContainer.classList.add('tsc-grabbing');
            startX = e.pageX - scrollContainer.offsetLeft;
            scrollLeft = scrollContainer.scrollLeft;
            isPaused = true;
        });

        scrollContainer.addEventListener('mouseleave', () => {
            isDown = false;
            scrollContainer.classList.remove('tsc-grabbing');
        });

        scrollContainer.addEventListener('mouseup', () => {
            isDown = false;
            scrollContainer.classList.remove('tsc-grabbing');
            if (isAutoScrolling) {
                pauseAutoScroll(2000);
            }
        });

        scrollContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scrollContainer.offsetLeft;
            const walk = (x - startX) * 1.5;
            scrollContainer.scrollLeft = scrollLeft - walk;
        });

        // Touch events for mobile drag
        scrollContainer.addEventListener('touchstart', (e) => {
            isPaused = true;
        }, { passive: true });

        scrollContainer.addEventListener('touchend', () => {
            if (isAutoScrolling) {
                pauseAutoScroll(3000);
            }
        }, { passive: true });

        // Respect reduced motion preference
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            isAutoScrolling = false;
            isPaused = true;
            if (playPauseBtn) {
                const icon = playPauseBtn.querySelector('svg');
                icon.innerHTML = '<path d="M8 5v14l11-7z"/>'; // Play icon
            }
        }

        // Start auto-scroll
        animationId = requestAnimationFrame(autoScroll);

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (animationId) {
                cancelAnimationFrame(animationId);
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTestimonialsShowcase);
    } else {
        initTestimonialsShowcase();
    }
})();
