/**
 * Writgo Theme - Main JavaScript
 * 
 * @package Writgo_Affiliate
 */

(function() {
    'use strict';

    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        const menuToggle = document.querySelector('.wa-menu-toggle');
        const nav = document.querySelector('.wa-nav');
        
        if (!menuToggle || !nav) return;
        
        menuToggle.addEventListener('click', () => {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            nav.classList.toggle('active');
            
            // Update icon
            const icon = menuToggle.querySelector('svg');
            if (icon) {
                if (nav.classList.contains('active')) {
                    icon.innerHTML = '<path d="M18 6L6 18M6 6l12 12"/>';
                } else {
                    icon.innerHTML = '<path d="M3 12h18M3 6h18M3 18h18"/>';
                }
            }
        });
    }

    /**
     * Smooth scroll for anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                
                // Skip if it's just "#" or empty
                if (targetId === '#' || !targetId) return;
                
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    
                    const headerHeight = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--wa-header-height')) || 70;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Update URL
                    history.pushState(null, null, targetId);
                }
            });
        });
    }

    /**
     * Lazy load images
     */
    function initLazyLoad() {
        if ('IntersectionObserver' in window) {
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            });
            
            lazyImages.forEach(img => imageObserver.observe(img));
        }
    }

    /**
     * Add external link indicator
     */
    function initExternalLinks() {
        const homeUrl = window.location.hostname;
        
        document.querySelectorAll('.wa-content a[href^="http"]').forEach(link => {
            const linkUrl = new URL(link.href);
            
            if (linkUrl.hostname !== homeUrl) {
                link.setAttribute('target', '_blank');
                link.setAttribute('rel', 'nofollow sponsored noopener');
                
                // Add visual indicator if not already present
                if (!link.querySelector('.external-icon')) {
                    const icon = document.createElement('svg');
                    icon.classList.add('external-icon');
                    icon.setAttribute('width', '12');
                    icon.setAttribute('height', '12');
                    icon.setAttribute('viewBox', '0 0 24 24');
                    icon.setAttribute('fill', 'none');
                    icon.setAttribute('stroke', 'currentColor');
                    icon.setAttribute('stroke-width', '2');
                    icon.innerHTML = '<path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/>';
                    icon.style.marginLeft = '4px';
                    icon.style.display = 'inline';
                    icon.style.verticalAlign = 'middle';
                    link.appendChild(icon);
                }
            }
        });
    }

    /**
     * Header scroll behavior
     */
    function initHeaderScroll() {
        const header = document.querySelector('.wa-header');
        if (!header) return;
        
        let lastScroll = 0;
        
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            
            lastScroll = currentScroll;
        }, { passive: true });
    }

    /**
     * Initialize all functions
     */
    function init() {
        initMobileMenu();
        initSmoothScroll();
        initLazyLoad();
        initExternalLinks();
        initHeaderScroll();
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
