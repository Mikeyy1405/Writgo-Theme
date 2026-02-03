<?php
/**
 * Writgo Performance Optimizations - REFACTORED
 *
 * Focus:
 * - Database query optimization
 * - Caching strategies (object cache, transients)
 * - Asset optimization (defer JS, async CSS)
 * - Image lazy loading
 * - Database indexing recommendations
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit('No direct access.');
}

// =============================================================================
// 1. CACHE WARMING & INVALIDATION
// =============================================================================

/**
 * Clear theme caches when post is published/updated
 */
add_action('publish_post', 'writgo_clear_post_caches');
add_action('save_post', 'writgo_clear_post_caches');

function writgo_clear_post_caches($post_id = 0) {
    if (!$post_id) {
        return;
    }
    
    // Clear related posts cache
    wp_cache_delete('writgo_related_' . $post_id, WRITGO_CACHE_GROUP);
    
    // Clear archive caches
    wp_cache_delete('writgo_archive_posts', WRITGO_CACHE_GROUP);
    wp_cache_delete('writgo_homepage_featured', WRITGO_CACHE_GROUP);
}

/**
 * Clear all theme caches
 */
function writgo_clear_all_caches() {
    wp_cache_flush();
}

/**
 * Clear caches when theme is updated
 */
add_action('after_switch_theme', 'writgo_clear_all_caches');

// =============================================================================
// 2. DOCUMENT HEAD OPTIMIZATION
// =============================================================================

/**
 * Preconnect to external domains
 * Reduces DNS lookup time for Google Fonts, analytics, etc.
 */
add_action('wp_head', function() {
    ?>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php
}, 1);

/**
 * Preload critical resources
 */
add_action('wp_head', function() {
    if (is_singular('post')) {
        echo '<link rel="preload" as="script" href="' . esc_url(WRITGO_URI . '/assets/js/single.js') . '">';
    }
}, 2);

/**
 * Remove unnecessary HTML comments in production
 */
add_action('wp_footer', function() {
    ob_start(function($buffer) {
        if (defined('WP_DEBUG') && !WP_DEBUG) {
            $buffer = preg_replace('/<!--(.|\s)*?-->/m', '', $buffer);
        }
        return $buffer;
    });
}, 0);

// =============================================================================
// 3. QUERY OPTIMIZATION
// =============================================================================

/**
 * Optimize home/archive queries
 */
add_filter('posts_request', function($query) {
    // Remove unnecessary joins in production
    if (!is_admin()) {
        $query = preg_replace('/LEFT JOIN.*postmeta/i', '', $query);
    }
    return $query;
});

/**
 * Set reasonable query limits
 */
add_filter('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Limit posts to prevent large queries
        if (is_archive() || is_home()) {
            $query->set('posts_per_page', 12);
        }
    }
    return $query;
});

// =============================================================================
// 4. IMAGE LAZY LOADING
// =============================================================================

/**
 * Add native lazy loading to all images in content
 */
add_filter('the_content', function($content) {
    if (is_singular() && !is_admin()) {
        // Add loading="lazy" to img tags
        $content = preg_replace(
            '/<img(?![^>]*\sloading\s*=)([^>]*)>/i',
            '<img loading="lazy" decoding="async"$1>',
            $content
        );
    }
    return $content;
});

/**
 * Optimize featured images
 */
add_filter('post_thumbnail_html', function($html) {
    if (!is_admin()) {
        $html = str_replace(
            '<img ',
            '<img loading="lazy" decoding="async" ',
            $html
        );
    }
    return $html;
});

// =============================================================================
// 5. MINIFY INLINE CSS/JS
// =============================================================================

/**
 * Minify CSS
 */
function writgo_minify_css($css) {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+(?:[^/*][^*]*\*+)*/!', '', $css);
    // Remove spaces
    $css = preg_replace('/\s+/', ' ', $css);
    $css = str_replace('{ ', '{', $css);
    $css = str_replace(' }', '}', $css);
    $css = str_replace('; ', ';', $css);
    $css = str_replace(', ', ',', $css);
    $css = str_replace(' : ', ':', $css);
    return trim($css);
}

/**
 * Minify JavaScript
 */
function writgo_minify_js($js) {
    // Remove single-line comments
    $js = preg_replace('/(\/\/.*)$/m', '', $js);
    // Remove multi-line comments
    $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js);
    // Remove unnecessary spaces
    $js = preg_replace('/\s+/', ' ', $js);
    $js = str_replace('{ ', '{', $js);
    $js = str_replace(' }', '}', $js);
    return trim($js);
}

// =============================================================================
// 6. BROWSER CACHING HEADERS
// =============================================================================

/**
 * Set cache headers for static assets
 * Add to .htaccess for better performance
 */
function writgo_add_cache_headers() {
    if (!is_admin() && !is_user_logged_in()) {
        // Cache static assets for 30 days
        header('Cache-Control: public, max-age=2592000');
        header('Expires: ' . gmdate('r', time() + 2592000));
    } else {
        // No cache for logged-in users or admin
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}
add_action('send_headers', 'writgo_add_cache_headers');

// =============================================================================
// 7. DATABASE OPTIMIZATION
// =============================================================================

/**
 * Recommend database indexes for better performance
 * These should be added manually or via optimization plugins
 */
function writgo_get_recommended_indexes() {
    return array(
        'wp_postmeta' => array(
            'KEY post_id_meta_key (post_id, meta_key(10))',
        ),
        'wp_posts' => array(
            'KEY post_type_status (post_type, post_status)',
            'KEY post_date (post_date)',
        ),
    );
}

// =============================================================================
// 8. TRANSIENT CACHING (Fallback when object cache unavailable)
// =============================================================================

/**
 * Get transient with fallback to option
 */
function writgo_get_transient($key, $callback, $ttl = HOUR_IN_SECONDS) {
    // Try object cache first
    $value = wp_cache_get($key, WRITGO_CACHE_GROUP);
    
    if ($value === false) {
        // Try transient
        $value = get_transient($key);
        
        if ($value === false) {
            // Run callback and cache
            $value = $callback();
            set_transient($key, $value, $ttl);
            wp_cache_set($key, $value, WRITGO_CACHE_GROUP, $ttl);
        }
    }
    
    return $value;
}

// =============================================================================
// 9. OPTIMIZATION REPORT
// =============================================================================

/**
 * Add admin notice with optimization tips
 */
add_action('admin_notices', function() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Check if optimization notice already shown today
    $notice_key = 'writgo_optimization_notice_' . date('Y-m-d');
    if (get_transient($notice_key)) {
        return;
    }
    
    ?>
    <div class="notice notice-info is-dismissible">
        <p>
            <strong><?php esc_html_e('Writgo Performance Tips:', 'writgo'); ?></strong>
        </p>
        <ul style="margin-top: 10px; margin-left: 20px;">
            <li><?php esc_html_e('✓ Enable WP Object Cache (Redis/Memcached)', 'writgo'); ?></li>
            <li><?php esc_html_e('✓ Compress images before uploading', 'writgo'); ?></li>
            <li><?php esc_html_e('✓ Use a CDN for static assets', 'writgo'); ?></li>
            <li><?php esc_html_e('✓ Enable GZIP compression on server', 'writgo'); ?></li>
        </ul>
    </div>
    <?php
    
    set_transient($notice_key, 1, DAY_IN_SECONDS);
});

// =============================================================================
// 10. CORE WEB VITALS MONITORING
// =============================================================================

/**
 * Add Core Web Vitals monitoring script
 */
add_action('wp_footer', function() {
    if (!is_admin()) {
        ?>
        <script>
        // Core Web Vitals monitoring (non-blocking)
        if ('web-vital' in window) {
            window.addEventListener('load', function() {
                // LCP - Largest Contentful Paint
                new PerformanceObserver((entryList) => {
                    const entries = entryList.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    console.log('LCP:', lastEntry.renderTime || lastEntry.loadTime);
                }).observe({entryTypes: ['largest-contentful-paint']});
            });
        }
        </script>
        <?php
    }
});

// =============================================================================
// END OF PERFORMANCE OPTIMIZATIONS
// =============================================================================
