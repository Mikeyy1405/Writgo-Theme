<?php
/**
 * Writgo Performance Optimizations
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * =====================================================
 * LAZY LOADING FOR IMAGES
 * =====================================================
 */

// Add lazy loading to content images
add_filter('the_content', 'writgo_add_lazy_loading_to_images', 99);
function writgo_add_lazy_loading_to_images($content) {
    if (is_admin() || is_feed() || is_preview()) {
        return $content;
    }

    // Don't add lazy loading to first image (above fold)
    $first_image = true;

    $content = preg_replace_callback(
        '/<img([^>]+)>/i',
        function($matches) use (&$first_image) {
            $img_tag = $matches[0];
            $attrs = $matches[1];

            // Skip if already has loading attribute
            if (strpos($attrs, 'loading=') !== false) {
                return $img_tag;
            }

            // Skip first image (likely above the fold)
            if ($first_image) {
                $first_image = false;
                // Add fetchpriority for LCP image
                if (strpos($attrs, 'fetchpriority=') === false) {
                    return '<img' . $attrs . ' fetchpriority="high">';
                }
                return $img_tag;
            }

            // Add lazy loading
            return '<img' . $attrs . ' loading="lazy">';
        },
        $content
    );

    return $content;
}

// Add lazy loading to post thumbnails (except on single where it's hero)
add_filter('wp_get_attachment_image_attributes', 'writgo_lazy_load_thumbnails', 10, 3);
function writgo_lazy_load_thumbnails($attr, $attachment, $size) {
    // Don't lazy load featured images on single posts (above fold)
    if (is_singular() && in_the_loop() && is_main_query()) {
        $attr['fetchpriority'] = 'high';
        return $attr;
    }

    // Add lazy loading to all other thumbnails
    if (!isset($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }

    return $attr;
}

// Add decoding async to all images for better performance
add_filter('wp_get_attachment_image_attributes', 'writgo_add_decoding_async', 10, 1);
function writgo_add_decoding_async($attr) {
    if (!isset($attr['decoding'])) {
        $attr['decoding'] = 'async';
    }
    return $attr;
}

/**
 * =====================================================
 * WEBP IMAGE SUPPORT
 * =====================================================
 */

// Enable WebP upload support
add_filter('mime_types', 'writgo_add_webp_mime_type');
function writgo_add_webp_mime_type($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}

// Enable WebP in media library
add_filter('upload_mimes', 'writgo_allow_webp_upload');
function writgo_allow_webp_upload($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}

// Fix WebP display in media library
add_filter('file_is_displayable_image', 'writgo_webp_is_displayable', 10, 2);
function writgo_webp_is_displayable($result, $path) {
    if ($result === false) {
        $displayable_image_types = array(IMAGETYPE_WEBP);
        $info = @getimagesize($path);
        if (empty($info)) {
            $result = false;
        } elseif (!in_array($info[2], $displayable_image_types)) {
            $result = false;
        } else {
            $result = true;
        }
    }
    return $result;
}

/**
 * =====================================================
 * RESOURCE HINTS (Preconnect, DNS-Prefetch)
 * =====================================================
 */

add_action('wp_head', 'writgo_resource_hints', 1);
function writgo_resource_hints() {
    ?>
    <!-- DNS Prefetch & Preconnect for faster external resources -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php

    // Add preconnect for affiliate networks if used
    $affiliate_links = get_option('writgo_affiliate_links', array());
    $preconnected = array();

    foreach ($affiliate_links as $link) {
        if (!empty($link['url'])) {
            $host = parse_url($link['url'], PHP_URL_HOST);
            if ($host && !in_array($host, $preconnected)) {
                echo '<link rel="dns-prefetch" href="//' . esc_attr($host) . '">' . "\n";
                $preconnected[] = $host;
            }
        }
        // Limit to 5 preconnects
        if (count($preconnected) >= 5) break;
    }
}

/**
 * =====================================================
 * PRELOAD CRITICAL ASSETS
 * =====================================================
 */

add_action('wp_head', 'writgo_preload_critical_assets', 2);
function writgo_preload_critical_assets() {
    // Preload main font
    ?>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" as="style">
    <?php

    // Preload hero image on single posts
    if (is_singular() && has_post_thumbnail()) {
        $image_url = get_the_post_thumbnail_url(get_the_ID(), 'writgo-featured');
        if ($image_url) {
            echo '<link rel="preload" href="' . esc_url($image_url) . '" as="image">' . "\n";
        }
    }
}

/**
 * =====================================================
 * OPTIMIZE SCRIPT LOADING
 * =====================================================
 */

// Add defer to non-critical scripts
add_filter('script_loader_tag', 'writgo_defer_scripts', 10, 3);
function writgo_defer_scripts($tag, $handle, $src) {
    // Scripts that should be deferred
    $defer_scripts = array(
        'writgo-toc',
    );

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}

// Remove jQuery migrate if not needed
add_action('wp_default_scripts', 'writgo_remove_jquery_migrate');
function writgo_remove_jquery_migrate($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            $script->deps = array_diff($script->deps, array('jquery-migrate'));
        }
    }
}

/**
 * =====================================================
 * OPTIMIZE CSS LOADING
 * =====================================================
 */

// Add media attribute for non-critical CSS
add_filter('style_loader_tag', 'writgo_optimize_css_loading', 10, 4);
function writgo_optimize_css_loading($html, $handle, $href, $media) {
    // Make Google Fonts non-render-blocking
    if ($handle === 'writgo-fonts') {
        // Use print media and swap to all on load
        $html = str_replace("media='all'", "media='print' onload=\"this.media='all'\"", $html);
        // Add noscript fallback
        $html .= '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>' . "\n";
    }

    return $html;
}

/**
 * =====================================================
 * INLINE CRITICAL CSS
 * =====================================================
 */

add_action('wp_head', 'writgo_inline_critical_css', 3);
function writgo_inline_critical_css() {
    ?>
    <style id="writgo-critical-css">
    /* Critical CSS for above-the-fold content */
    *,*::before,*::after{box-sizing:border-box}
    body{margin:0;font-family:Inter,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;line-height:1.6;color:#1e293b;background:#fff}
    .wa-header{position:sticky;top:0;z-index:1000;background:#fff;border-bottom:1px solid #e5e7eb;transition:box-shadow .3s}
    .wa-header.scrolled{box-shadow:0 2px 20px rgba(0,0,0,.08)}
    .wa-header-inner{max-width:1280px;margin:0 auto;padding:0 24px;display:flex;align-items:center;justify-content:space-between;height:70px}
    .wa-logo img{height:40px;width:auto}
    .wa-nav{display:flex;gap:32px;list-style:none;margin:0;padding:0}
    .wa-nav a{color:#475569;text-decoration:none;font-weight:500;transition:color .2s}
    .wa-nav a:hover{color:#f97316}
    .wa-container{max-width:1280px;margin:0 auto;padding:0 24px}
    .wa-hero{padding:60px 0;background:linear-gradient(135deg,#fff9f5 0%,#fff 100%)}
    .wa-hero h1{font-size:clamp(2rem,5vw,3.5rem);font-weight:800;line-height:1.1;margin:0 0 20px}
    @media(max-width:768px){.wa-nav{display:none}.wa-header-inner{height:60px}}
    </style>
    <?php
}

/**
 * =====================================================
 * DISABLE EMOJIS (Performance boost)
 * =====================================================
 */

add_action('init', 'writgo_disable_emojis');
function writgo_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // Remove from TinyMCE
    add_filter('tiny_mce_plugins', 'writgo_disable_emojis_tinymce');
    add_filter('wp_resource_hints', 'writgo_disable_emojis_dns_prefetch', 10, 2);
}

function writgo_disable_emojis_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    }
    return array();
}

function writgo_disable_emojis_dns_prefetch($urls, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
        $emoji_url = 'https://s.w.org/images/core/emoji/';
        foreach ($urls as $key => $url) {
            if (strpos($url, $emoji_url) !== false) {
                unset($urls[$key]);
            }
        }
    }
    return $urls;
}

/**
 * =====================================================
 * REMOVE UNNECESSARY META TAGS
 * =====================================================
 */

add_action('init', 'writgo_cleanup_head');
function writgo_cleanup_head() {
    // Remove WordPress version
    remove_action('wp_head', 'wp_generator');

    // Remove wlwmanifest link
    remove_action('wp_head', 'wlwmanifest_link');

    // Remove RSD link
    remove_action('wp_head', 'rsd_link');

    // Remove shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');

    // Remove REST API link
    remove_action('wp_head', 'rest_output_link_wp_head');

    // Remove oEmbed discovery links
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
}

/**
 * =====================================================
 * LIMIT POST REVISIONS
 * =====================================================
 */

if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 5);
}

/**
 * =====================================================
 * OPTIMIZE HEARTBEAT API
 * =====================================================
 */

add_action('init', 'writgo_optimize_heartbeat', 1);
function writgo_optimize_heartbeat() {
    // Disable heartbeat on frontend
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
}

// Slow down heartbeat in admin
add_filter('heartbeat_settings', 'writgo_heartbeat_settings');
function writgo_heartbeat_settings($settings) {
    $settings['interval'] = 60; // 60 seconds instead of 15
    return $settings;
}

/**
 * =====================================================
 * ASYNC/DEFER ATTRIBUTE FOR SCRIPTS
 * =====================================================
 */

add_filter('script_loader_tag', 'writgo_add_async_defer', 10, 3);
function writgo_add_async_defer($tag, $handle, $src) {
    // Add async to analytics scripts
    $async_scripts = array(
        'google-analytics',
        'gtag',
    );

    if (in_array($handle, $async_scripts)) {
        return str_replace(' src', ' async src', $tag);
    }

    return $tag;
}

/**
 * =====================================================
 * RESPONSIVE IMAGES WITH SRCSET
 * =====================================================
 */

// Ensure proper srcset generation for all image sizes
add_filter('wp_calculate_image_srcset_meta', 'writgo_enhance_srcset', 10, 4);
function writgo_enhance_srcset($image_meta, $size_array, $image_src, $attachment_id) {
    return $image_meta;
}

// Add sizes attribute for responsive images
add_filter('wp_get_attachment_image_attributes', 'writgo_responsive_image_sizes', 10, 3);
function writgo_responsive_image_sizes($attr, $attachment, $size) {
    // Add proper sizes attribute based on context
    if (!isset($attr['sizes'])) {
        if ($size === 'writgo-hero' || $size === 'full') {
            $attr['sizes'] = '100vw';
        } elseif ($size === 'writgo-featured') {
            $attr['sizes'] = '(max-width: 768px) 100vw, 800px';
        } elseif ($size === 'writgo-card') {
            $attr['sizes'] = '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 400px';
        }
    }

    return $attr;
}

/**
 * =====================================================
 * BROWSER CACHE HEADERS VIA PHP
 * =====================================================
 */

add_action('send_headers', 'writgo_add_cache_headers');
function writgo_add_cache_headers() {
    // Only for frontend, non-logged-in users
    if (is_admin() || is_user_logged_in()) {
        return;
    }

    // Don't cache search results or dynamic pages
    if (is_search() || is_404()) {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        return;
    }

    // Set cache headers for static pages
    if (!is_singular() || !comments_open()) {
        header('Cache-Control: public, max-age=3600'); // 1 hour
    }
}

/**
 * =====================================================
 * ADMIN NOTICE FOR .HTACCESS CACHING
 * =====================================================
 */

add_action('admin_notices', 'writgo_htaccess_notice');
function writgo_htaccess_notice() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Check if dismissed
    if (get_option('writgo_htaccess_notice_dismissed')) {
        return;
    }

    // Check if on Apache
    if (strpos($_SERVER['SERVER_SOFTWARE'] ?? '', 'Apache') === false) {
        return;
    }

    $htaccess_file = ABSPATH . '.htaccess';
    $htaccess_content = file_exists($htaccess_file) ? file_get_contents($htaccess_file) : '';

    // Check if caching rules already exist
    if (strpos($htaccess_content, 'mod_expires') !== false) {
        return;
    }

    ?>
    <div class="notice notice-info is-dismissible" id="writgo-htaccess-notice">
        <p><strong>Writgo Performance:</strong> Voeg caching regels toe aan je .htaccess bestand voor betere prestaties.</p>
        <p><a href="<?php echo admin_url('admin.php?page=writgo-performance'); ?>" class="button button-primary">Bekijk instructies</a></p>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $(document).on('click', '#writgo-htaccess-notice .notice-dismiss', function() {
            $.post(ajaxurl, {action: 'writgo_dismiss_htaccess_notice'});
        });
    });
    </script>
    <?php
}

add_action('wp_ajax_writgo_dismiss_htaccess_notice', 'writgo_dismiss_htaccess_notice');
function writgo_dismiss_htaccess_notice() {
    update_option('writgo_htaccess_notice_dismissed', true);
    wp_die();
}
