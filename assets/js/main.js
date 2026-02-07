/**
 * Writgo Theme - Main JavaScript
 * v10.0.0
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
        const nav = document.getElementById('site-nav');

        if (!menuToggle || !nav) return;

        const iconMenu = menuToggle.querySelector('.wa-icon-menu');
        const iconClose = menuToggle.querySelector('.wa-icon-close');

        function toggleMenu(open) {
            menuToggle.setAttribute('aria-expanded', open);
            nav.classList.toggle('active', open);
            document.body.classList.toggle('menu-open', open);
            if (iconMenu) iconMenu.style.display = open ? 'none' : '';
            if (iconClose) iconClose.style.display = open ? '' : 'none';
        }

        menuToggle.addEventListener('click', () => {
            const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
            toggleMenu(!isOpen);
        });

        // Close on link click
        nav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => toggleMenu(false));
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (nav.classList.contains('active') &&
                !nav.contains(e.target) &&
                !menuToggle.contains(e.target)) {
                toggleMenu(false);
            }
        });

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && nav.classList.contains('active')) {
                toggleMenu(false);
                menuToggle.focus();
            }
        });
    }

    /**
     * Search Overlay
     */
    function initSearchOverlay() {
        const toggle = document.querySelector('.wa-search-toggle');
        const overlay = document.getElementById('search-overlay');
        const closeBtn = overlay ? overlay.querySelector('.wa-search-overlay-close') : null;
        const input = overlay ? overlay.querySelector('.wa-search-overlay-input') : null;

        if (!toggle || !overlay) return;

        function openSearch() {
            overlay.classList.add('active');
            overlay.setAttribute('aria-hidden', 'false');
            toggle.setAttribute('aria-expanded', 'true');
            document.body.classList.add('search-open');
            if (input) setTimeout(() => input.focus(), 100);
        }

        function closeSearch() {
            overlay.classList.remove('active');
            overlay.setAttribute('aria-hidden', 'true');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('search-open');
            toggle.focus();
        }

        toggle.addEventListener('click', openSearch);
        if (closeBtn) closeBtn.addEventListener('click', closeSearch);

        // Close on Escape
        overlay.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeSearch();
        });

        // Close on backdrop click
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeSearch();
        });
    }

    /**
     * Smooth scroll for anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#' || !targetId) return;

                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    const headerHeight = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--wa-header-height')) || 72;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;

                    window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                    history.pushState(null, null, targetId);
                }
            });
        });
    }

    /**
     * External links: add target, rel, and icon
     */
    function initExternalLinks() {
        const homeUrl = window.location.hostname;

        document.querySelectorAll('.wa-content a[href^="http"]').forEach(link => {
            try {
                const linkUrl = new URL(link.href);
                if (linkUrl.hostname !== homeUrl) {
                    link.setAttribute('target', '_blank');
                    link.setAttribute('rel', 'nofollow sponsored noopener');
                }
            } catch (e) {}
        });
    }

    /**
     * Header scroll behavior
     */
    function initHeaderScroll() {
        const header = document.getElementById('site-header');
        if (!header) return;

        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    header.classList.toggle('scrolled', window.pageYOffset > 80);
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }

    /**
     * Reading Progress Bar (top of page)
     */
    function initReadingProgress() {
        const bar = document.querySelector('.wa-reading-progress-fill');
        const article = document.getElementById('article-content');
        if (!bar || !article) return;

        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    const articleTop = article.offsetTop;
                    const articleHeight = article.offsetHeight;
                    const scrollY = window.pageYOffset;
                    const windowHeight = window.innerHeight;

                    const start = articleTop - windowHeight;
                    const end = articleTop + articleHeight - windowHeight;
                    let progress = ((scrollY - start) / (end - start)) * 100;
                    progress = Math.max(0, Math.min(100, progress));

                    bar.style.width = progress + '%';
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }

    /**
     * Scroll to Top button
     */
    function initScrollToTop() {
        const btn = document.getElementById('scroll-top');
        if (!btn) return;

        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    btn.classList.toggle('visible', window.pageYOffset > 400);
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        btn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /**
     * Initialize
     */
    function init() {
        initMobileMenu();
        initSearchOverlay();
        initSmoothScroll();
        initExternalLinks();
        initHeaderScroll();
        initReadingProgress();
        initScrollToTop();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
