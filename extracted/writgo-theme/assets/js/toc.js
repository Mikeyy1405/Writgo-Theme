/**
 * Writgo TOC & Reading Progress
 * 
 * - Generates TOC from H2/H3 headings
 * - Tracks active section on scroll
 * - Shows reading progress
 * - Mobile TOC toggle
 */

(function() {
    'use strict';

    // Elements
    const articleContent = document.getElementById('article-content');
    const sidebarTocList = document.getElementById('toc-sidebar-list');
    const mobileTocList = document.getElementById('toc-mobile-list');
    const tocToggle = document.querySelector('.wa-toc-toggle');
    const progressBar = document.getElementById('reading-progress');
    const sidebarToc = document.getElementById('sidebar-toc');
    const mobileToc = document.getElementById('toc-mobile');

    if (!articleContent) return;

    // Config
    const headerHeight = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--wa-header-height')) || 70;
    const scrollOffset = headerHeight + 24;

    /**
     * Generate TOC from headings
     */
    function generateTOC() {
        const headings = articleContent.querySelectorAll('h2, h3');
        
        if (headings.length === 0) {
            // Hide TOC if no headings
            if (sidebarToc) sidebarToc.style.display = 'none';
            if (mobileToc) mobileToc.style.display = 'none';
            return;
        }

        let tocHTML = '<ul class="wa-toc-items">';

        headings.forEach((heading, index) => {
            // Create ID if not exists
            if (!heading.id) {
                const slug = heading.textContent
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .substring(0, 50);
                heading.id = slug + '-' + index;
            }

            const level = heading.tagName.toLowerCase();
            const text = heading.textContent.trim();
            const className = level === 'h3' ? 'toc-h3' : 'toc-h2';

            tocHTML += `
                <li>
                    <a href="#${heading.id}" class="${className}" data-target="${heading.id}">
                        ${text}
                    </a>
                </li>
            `;
        });

        tocHTML += '</ul>';

        // Insert into both TOC containers
        if (sidebarTocList) sidebarTocList.innerHTML = tocHTML;
        if (mobileTocList) mobileTocList.innerHTML = tocHTML;

        // Add click handlers for smooth scroll
        document.querySelectorAll('.wa-toc-items a').forEach(link => {
            link.addEventListener('click', handleTocClick);
        });
    }

    /**
     * Handle TOC link click
     */
    function handleTocClick(e) {
        e.preventDefault();
        const targetId = this.getAttribute('data-target');
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
            const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - scrollOffset;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });

            // Close mobile TOC
            if (mobileTocList && mobileTocList.classList.contains('open')) {
                mobileTocList.classList.remove('open');
                if (tocToggle) tocToggle.setAttribute('aria-expanded', 'false');
            }

            // Update URL hash (without jumping)
            history.pushState(null, null, '#' + targetId);
        }
    }

    /**
     * Track active section on scroll
     */
    function updateActiveSection() {
        const headings = articleContent.querySelectorAll('h2, h3');
        const tocLinks = document.querySelectorAll('.wa-toc-items a');
        
        if (headings.length === 0 || tocLinks.length === 0) return;
        
        let currentActive = null;

        headings.forEach(heading => {
            const rect = heading.getBoundingClientRect();
            if (rect.top <= scrollOffset + 100) {
                currentActive = heading.id;
            }
        });

        // Update active class
        tocLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-target') === currentActive) {
                link.classList.add('active');
            }
        });
    }

    /**
     * Update reading progress
     */
    function updateReadingProgress() {
        if (!progressBar) return;

        const articleTop = articleContent.offsetTop;
        const articleHeight = articleContent.offsetHeight;
        const windowHeight = window.innerHeight;
        const scrollY = window.pageYOffset;

        // Calculate progress
        const startPoint = articleTop - windowHeight;
        const endPoint = articleTop + articleHeight - windowHeight;
        const currentProgress = scrollY - startPoint;
        const totalDistance = endPoint - startPoint;

        let progress = (currentProgress / totalDistance) * 100;
        progress = Math.max(0, Math.min(100, progress));

        progressBar.style.setProperty('--progress', progress + '%');
    }

    /**
     * Mobile TOC toggle
     */
    function setupMobileToggle() {
        if (!tocToggle || !mobileTocList) return;

        tocToggle.addEventListener('click', () => {
            const isExpanded = tocToggle.getAttribute('aria-expanded') === 'true';
            tocToggle.setAttribute('aria-expanded', !isExpanded);
            mobileTocList.classList.toggle('open');
        });
    }

    /**
     * Throttle function for scroll events
     */
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    /**
     * Initialize
     */
    function init() {
        generateTOC();
        setupMobileToggle();

        // Scroll event listeners (throttled)
        const handleScroll = throttle(() => {
            updateActiveSection();
            updateReadingProgress();
        }, 50);

        window.addEventListener('scroll', handleScroll, { passive: true });

        // Initial update
        updateActiveSection();
        updateReadingProgress();
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
