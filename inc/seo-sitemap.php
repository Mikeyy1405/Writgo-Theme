<?php
/**
 * Writgo SEO - XML Sitemap Generator
 * @package Writgo_Affiliate
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

// =============================================================================
// XML SITEMAP
// =============================================================================

// Register sitemap endpoint
add_action('init', 'writgo_sitemap_init');
function writgo_sitemap_init() {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?writgo_sitemap=index', 'top');
    add_rewrite_rule('^sitemap-posts\.xml$', 'index.php?writgo_sitemap=posts', 'top');
    add_rewrite_rule('^sitemap-pages\.xml$', 'index.php?writgo_sitemap=pages', 'top');
    add_rewrite_rule('^sitemap-categories\.xml$', 'index.php?writgo_sitemap=categories', 'top');
}

add_filter('query_vars', 'writgo_sitemap_query_vars');
function writgo_sitemap_query_vars($vars) {
    $vars[] = 'writgo_sitemap';
    return $vars;
}

add_action('template_redirect', 'writgo_sitemap_template');
function writgo_sitemap_template() {
    $sitemap = get_query_var('writgo_sitemap');
    if (!$sitemap) return;
    
    // Skip if WordPress native sitemap or Yoast/RankMath active
    if (defined('WPSEO_VERSION') || class_exists('RankMath')) return;
    
    header('Content-Type: application/xml; charset=UTF-8');
    header('X-Robots-Tag: noindex, follow');
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    
    switch ($sitemap) {
        case 'posts':
            writgo_sitemap_posts();
            break;
        case 'pages':
            writgo_sitemap_pages();
            break;
        case 'categories':
            writgo_sitemap_categories();
            break;
        default:
            writgo_sitemap_index();
    }
    
    exit;
}

function writgo_sitemap_index() {
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Posts sitemap
    $posts = get_posts(array('numberposts' => 1, 'post_status' => 'publish'));
    if (!empty($posts)) {
        echo '<sitemap>';
        echo '<loc>' . home_url('/sitemap-posts.xml') . '</loc>';
        echo '<lastmod>' . get_the_modified_date('c', $posts[0]) . '</lastmod>';
        echo '</sitemap>' . "\n";
    }
    
    // Pages sitemap
    $pages = get_posts(array('post_type' => 'page', 'numberposts' => 1, 'post_status' => 'publish'));
    if (!empty($pages)) {
        echo '<sitemap>';
        echo '<loc>' . home_url('/sitemap-pages.xml') . '</loc>';
        echo '<lastmod>' . get_the_modified_date('c', $pages[0]) . '</lastmod>';
        echo '</sitemap>' . "\n";
    }
    
    // Categories sitemap
    $cats = get_categories(array('hide_empty' => true));
    if (!empty($cats)) {
        echo '<sitemap>';
        echo '<loc>' . home_url('/sitemap-categories.xml') . '</loc>';
        echo '</sitemap>' . "\n";
    }
    
    echo '</sitemapindex>';
}

function writgo_sitemap_posts() {
    $posts = get_posts(array(
        'numberposts' => 1000,
        'post_status' => 'publish',
        'post_type'   => 'post',
        'orderby'     => 'modified',
        'order'       => 'DESC',
    ));
    
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
    
    foreach ($posts as $post) {
        // Skip noindex posts
        $noindex = get_post_meta($post->ID, '_writgo_noindex', true);
        if ($noindex === '1') continue;
        
        echo '<url>';
        echo '<loc>' . get_permalink($post) . '</loc>';
        echo '<lastmod>' . get_the_modified_date('c', $post) . '</lastmod>';
        echo '<changefreq>weekly</changefreq>';
        echo '<priority>0.8</priority>';
        
        // Add featured image
        if (has_post_thumbnail($post)) {
            $img_url = get_the_post_thumbnail_url($post, 'large');
            $img_title = get_the_title($post);
            echo '<image:image>';
            echo '<image:loc>' . esc_url($img_url) . '</image:loc>';
            echo '<image:title>' . esc_html($img_title) . '</image:title>';
            echo '</image:image>';
        }
        
        echo '</url>' . "\n";
    }
    
    echo '</urlset>';
}

function writgo_sitemap_pages() {
    $pages = get_posts(array(
        'numberposts' => 500,
        'post_status' => 'publish',
        'post_type'   => 'page',
        'orderby'     => 'modified',
        'order'       => 'DESC',
    ));
    
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Homepage first
    echo '<url>';
    echo '<loc>' . home_url('/') . '</loc>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>1.0</priority>';
    echo '</url>' . "\n";
    
    foreach ($pages as $page) {
        // Skip noindex pages
        $noindex = get_post_meta($page->ID, '_writgo_noindex', true);
        if ($noindex === '1') continue;
        
        echo '<url>';
        echo '<loc>' . get_permalink($page) . '</loc>';
        echo '<lastmod>' . get_the_modified_date('c', $page) . '</lastmod>';
        echo '<changefreq>monthly</changefreq>';
        echo '<priority>0.6</priority>';
        echo '</url>' . "\n";
    }
    
    echo '</urlset>';
}

function writgo_sitemap_categories() {
    $categories = get_categories(array('hide_empty' => true));
    
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    foreach ($categories as $cat) {
        echo '<url>';
        echo '<loc>' . get_category_link($cat->term_id) . '</loc>';
        echo '<changefreq>weekly</changefreq>';
        echo '<priority>0.5</priority>';
        echo '</url>' . "\n";
    }
    
    echo '</urlset>';
}

// Flush rewrite rules on theme activation
add_action('after_switch_theme', 'writgo_sitemap_flush_rules');
function writgo_sitemap_flush_rules() {
    writgo_sitemap_init();
    flush_rewrite_rules();
}

// Add sitemap link to robots.txt
add_filter('robots_txt', 'writgo_robots_sitemap', 10, 2);
function writgo_robots_sitemap($output, $public) {
    if ($public) {
        $output .= "\n# Writgo Sitemap\n";
        $output .= "Sitemap: " . home_url('/sitemap.xml') . "\n";
    }
    return $output;
}

// Flush rewrite rules on theme activation
add_action('after_switch_theme', 'writgo_sitemap_flush_on_activation');
function writgo_sitemap_flush_on_activation() {
    writgo_sitemap_init();
    flush_rewrite_rules();
    update_option('writgo_sitemap_flushed', true);
}

// Also flush when permalinks are saved
add_action('permalink_structure_changed', 'writgo_sitemap_on_permalink_save');
function writgo_sitemap_on_permalink_save() {
    update_option('writgo_sitemap_flushed', true);
}

// Admin notice to flush permalinks (only show once, dismissible)
add_action('admin_notices', 'writgo_sitemap_admin_notice');
function writgo_sitemap_admin_notice() {
    if (!current_user_can('manage_options')) return;
    
    // Check if already flushed
    if (get_option('writgo_sitemap_flushed')) return;
    
    // Check if rewrite rules exist
    $rules = get_option('rewrite_rules');
    if (is_array($rules) && isset($rules['^sitemap\.xml$'])) {
        update_option('writgo_sitemap_flushed', true);
        return;
    }
    
    echo '<div class="notice notice-warning is-dismissible" id="writgo-sitemap-notice">';
    echo '<p><strong>Writgo SEO:</strong> Ga naar <a href="' . admin_url('options-permalink.php') . '">Instellingen → Permalinks</a> en klik op "Wijzigingen opslaan" om de sitemap te activeren.</p>';
    echo '</div>';
    
    // Add JS to dismiss permanently
    ?>
    <script>
    jQuery(document).on('click', '#writgo-sitemap-notice .notice-dismiss', function() {
        jQuery.post(ajaxurl, {action: 'writgo_dismiss_sitemap_notice'});
    });
    </script>
    <?php
}

// AJAX handler to dismiss notice
add_action('wp_ajax_writgo_dismiss_sitemap_notice', 'writgo_dismiss_sitemap_notice');
function writgo_dismiss_sitemap_notice() {
    update_option('writgo_sitemap_flushed', true);
    wp_die();
}

// =============================================================================
// SITEMAP ADMIN PAGE
// =============================================================================

add_action('admin_menu', 'writgo_sitemap_admin_menu', 20);
function writgo_sitemap_admin_menu() {
    add_submenu_page(
        'writgo-dashboard',
        'XML Sitemap',
        '🗺️ XML Sitemap',
        'edit_posts',
        'writgo-sitemap',
        'writgo_sitemap_admin_page'
    );
}

function writgo_sitemap_admin_page() {
    // Get sitemap stats
    $posts_count = wp_count_posts('post')->publish;
    $pages_count = wp_count_posts('page')->publish;
    $cats_count = count(get_categories(array('hide_empty' => true)));
    
    // Check noindex counts
    global $wpdb;
    $noindex_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = '_writgo_noindex' AND meta_value = '1'");
    ?>
    
    <style>
        .writgo-sitemap-page { max-width: 900px; margin: 20px auto; padding: 0 20px; }
        .writgo-sitemap-header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 25px 30px; border-radius: 12px; color: white; margin-bottom: 20px; }
        .writgo-sitemap-header h1 { margin: 0 0 10px; font-size: 24px; display: flex; align-items: center; gap: 10px; }
        .writgo-sitemap-header p { margin: 0; opacity: 0.9; }
        .writgo-sitemap-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .writgo-sitemap-card h2 { margin: 0 0 15px; font-size: 18px; display: flex; align-items: center; gap: 10px; }
        .writgo-sitemap-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
        @media (max-width: 782px) { .writgo-sitemap-stats { grid-template-columns: repeat(2, 1fr); } }
        .writgo-sitemap-stat { background: #f9fafb; padding: 20px; border-radius: 8px; text-align: center; }
        .writgo-sitemap-stat .number { font-size: 32px; font-weight: 700; color: #1f2937; }
        .writgo-sitemap-stat .label { font-size: 13px; color: #6b7280; margin-top: 5px; }
        .writgo-sitemap-links { display: flex; flex-direction: column; gap: 10px; }
        .writgo-sitemap-link { display: flex; align-items: center; justify-content: space-between; padding: 15px; background: #f9fafb; border-radius: 8px; }
        .writgo-sitemap-link code { background: white; padding: 8px 12px; border-radius: 6px; font-size: 13px; }
        .writgo-sitemap-link a { color: #10b981; font-weight: 600; }
    </style>
    
    <div class="writgo-sitemap-page">
        <div class="writgo-sitemap-header">
            <h1>🗺️ XML Sitemap</h1>
            <p>Je sitemap helpt zoekmachines om al je content te vinden en indexeren.</p>
        </div>
        
        <div class="writgo-sitemap-card">
            <h2>📊 Sitemap Statistieken</h2>
            <div class="writgo-sitemap-stats">
                <div class="writgo-sitemap-stat">
                    <div class="number"><?php echo $posts_count; ?></div>
                    <div class="label">Artikelen</div>
                </div>
                <div class="writgo-sitemap-stat">
                    <div class="number"><?php echo $pages_count; ?></div>
                    <div class="label">Pagina's</div>
                </div>
                <div class="writgo-sitemap-stat">
                    <div class="number"><?php echo $cats_count; ?></div>
                    <div class="label">Categorieën</div>
                </div>
                <div class="writgo-sitemap-stat">
                    <div class="number" style="color: #ef4444;"><?php echo $noindex_count ?: 0; ?></div>
                    <div class="label">Uitgesloten (noindex)</div>
                </div>
            </div>
        </div>
        
        <div class="writgo-sitemap-card">
            <h2>🔗 Sitemap URLs</h2>
            <div class="writgo-sitemap-links">
                <div class="writgo-sitemap-link">
                    <div>
                        <strong>Hoofd Sitemap (Index)</strong><br>
                        <code><?php echo home_url('/sitemap.xml'); ?></code>
                    </div>
                    <a href="<?php echo home_url('/sitemap.xml'); ?>" target="_blank">Bekijken →</a>
                </div>
                <div class="writgo-sitemap-link">
                    <div>
                        <strong>Artikelen Sitemap</strong><br>
                        <code><?php echo home_url('/sitemap-posts.xml'); ?></code>
                    </div>
                    <a href="<?php echo home_url('/sitemap-posts.xml'); ?>" target="_blank">Bekijken →</a>
                </div>
                <div class="writgo-sitemap-link">
                    <div>
                        <strong>Pagina's Sitemap</strong><br>
                        <code><?php echo home_url('/sitemap-pages.xml'); ?></code>
                    </div>
                    <a href="<?php echo home_url('/sitemap-pages.xml'); ?>" target="_blank">Bekijken →</a>
                </div>
                <div class="writgo-sitemap-link">
                    <div>
                        <strong>Categorieën Sitemap</strong><br>
                        <code><?php echo home_url('/sitemap-categories.xml'); ?></code>
                    </div>
                    <a href="<?php echo home_url('/sitemap-categories.xml'); ?>" target="_blank">Bekijken →</a>
                </div>
            </div>
        </div>
        
        <div class="writgo-sitemap-card">
            <h2>🔧 Sitemap Indienen bij Google</h2>
            <ol style="color: #4b5563; line-height: 2;">
                <li>Ga naar <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>
                <li>Selecteer je website property</li>
                <li>Ga naar <strong>Sitemaps</strong> in het menu</li>
                <li>Voer in: <code>sitemap.xml</code></li>
                <li>Klik op <strong>Verzenden</strong></li>
            </ol>
        </div>
    </div>
    <?php
}

// =============================================================================
// PING SEARCH ENGINES
// =============================================================================

add_action('publish_post', 'writgo_ping_search_engines');
add_action('publish_page', 'writgo_ping_search_engines');
function writgo_ping_search_engines($post_id) {
    // Only ping once per hour max
    $last_ping = get_transient('writgo_last_sitemap_ping');
    if ($last_ping) return;
    
    $sitemap_url = urlencode(home_url('/sitemap.xml'));
    
    // Ping Google
    wp_remote_get("https://www.google.com/ping?sitemap={$sitemap_url}", array('timeout' => 5, 'blocking' => false));
    
    // Ping Bing
    wp_remote_get("https://www.bing.com/ping?sitemap={$sitemap_url}", array('timeout' => 5, 'blocking' => false));
    
    set_transient('writgo_last_sitemap_ping', true, HOUR_IN_SECONDS);
}
