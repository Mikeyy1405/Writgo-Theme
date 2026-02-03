<?php
/**
 * Writgo Performance Optimization
 * 
 * Optimizations for Core Web Vitals (LCP, FID, CLS)
 * 
 * @package Writgo_Affiliate
 * @version 1.0.1
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// HELPER FUNCTIONS (defined first, before any hooks)
// =============================================================================

/**
 * Get WebP URL if available
 */
function writgo_get_webp_url($url) {
    if (empty($url)) {
        return false;
    }
    
    if (strpos($url, '.webp') !== false) {
        return $url;
    }
    
    $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $url);
    $upload_dir = wp_upload_dir();
    $path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $webp_url);
    
    if (file_exists($path)) {
        return $webp_url;
    }
    
    return false;
}

// =============================================================================
// PRECONNECT & DNS-PREFETCH
// =============================================================================

add_action('wp_head', 'writgo_perf_preconnect', 1);
function writgo_perf_preconnect() {
    echo '<!-- Preconnect -->' . "\n";
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
    
    if (get_option('show_avatars')) {
        echo '<link rel="preconnect" href="https://secure.gravatar.com" crossorigin>' . "\n";
    }
}

// =============================================================================
// PRELOAD LCP IMAGE
// =============================================================================

add_action('wp_head', 'writgo_perf_preload_lcp', 2);
function writgo_perf_preload_lcp() {
    if (!is_front_page()) {
        return;
    }
    
    $hero_bg = get_theme_mod('writgo_hero_bg', '');
    if ($hero_bg) {
        $url = writgo_get_webp_url($hero_bg);
        $url = $url ? $url : $hero_bg;
        echo '<link rel="preload" as="image" href="' . esc_url($url) . '" fetchpriority="high">' . "\n";
    }
}

// =============================================================================
// INLINE CRITICAL CSS
// =============================================================================

add_action('wp_head', 'writgo_perf_critical_css', 3);
function writgo_perf_critical_css() {
    ?>
<style id="writgo-critical">
:root{--wa-primary:#1a365d;--wa-accent:#f97316;--wa-bg:#fff;--wa-text:#1a202c}
*,*::before,*::after{box-sizing:border-box}
body{margin:0;font-family:Inter,-apple-system,BlinkMacSystemFont,sans-serif;line-height:1.6;color:var(--wa-text);background:var(--wa-bg)}
img{max-width:100%;height:auto}
.wa-header{position:sticky;top:0;z-index:100;background:var(--wa-bg);border-bottom:1px solid #e2e8f0;padding:.75rem 0}
.wa-home-hero{position:relative;min-height:50vh;display:flex;align-items:center;background-size:cover;background-position:center}
</style>
    <?php
}

// =============================================================================
// DEFER NON-CRITICAL JS
// =============================================================================

add_filter('script_loader_tag', 'writgo_perf_defer_js', 10, 3);
function writgo_perf_defer_js($tag, $handle, $src) {
    $no_defer = array('jquery', 'jquery-core', 'jquery-migrate', 'wp-polyfill');
    
    if (in_array($handle, $no_defer)) {
        return $tag;
    }
    
    if (strpos($tag, 'defer') === false && strpos($tag, 'async') === false) {
        $tag = str_replace(' src=', ' defer src=', $tag);
    }
    
    return $tag;
}

// =============================================================================
// DISABLE EMOJIS
// =============================================================================

add_action('init', 'writgo_perf_disable_emojis');
function writgo_perf_disable_emojis() {
    if (is_admin()) {
        return;
    }
    
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}

// =============================================================================
// REMOVE JQUERY MIGRATE
// =============================================================================

add_action('wp_default_scripts', 'writgo_perf_remove_jquery_migrate');
function writgo_perf_remove_jquery_migrate($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            $script->deps = array_diff($script->deps, array('jquery-migrate'));
        }
    }
}

// =============================================================================
// CLEANUP WP HEAD
// =============================================================================

add_action('wp_loaded', 'writgo_perf_cleanup_head');
function writgo_perf_cleanup_head() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
}

// =============================================================================
// OPTIMIZE IMAGE ATTRIBUTES
// =============================================================================

add_filter('wp_get_attachment_image_attributes', 'writgo_perf_image_attrs', 10, 3);
function writgo_perf_image_attrs($attr, $attachment, $size) {
    $attr['decoding'] = 'async';
    
    if (is_front_page() && in_array($size, array('writgo-featured', 'full', 'large'))) {
        $attr['loading'] = 'eager';
        $attr['fetchpriority'] = 'high';
    }
    
    return $attr;
}

// =============================================================================
// RESOURCE HINTS
// =============================================================================

add_filter('wp_resource_hints', 'writgo_perf_resource_hints', 10, 2);
function writgo_perf_resource_hints($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        $urls[] = array(
            'href' => 'https://fonts.googleapis.com',
            'crossorigin' => true,
        );
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => true,
        );
    }
    
    return $urls;
}
