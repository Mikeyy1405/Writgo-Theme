<?php
/**
 * Writgo SEO Advanced Module
 *
 * Adds: XML Sitemap, Robots.txt, llms.txt, IndexNow, Ping, Redirects
 *
 * @package Writgo_Affiliate
 * @version 10.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// 1. XML SITEMAP (replaces WordPress default with optimized version)
// =============================================================================

/**
 * Register custom sitemap rewrite rules
 */
add_action('init', 'writgo_sitemap_init');
function writgo_sitemap_init() {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?writgo_sitemap=index', 'top');
    add_rewrite_rule('^sitemap-posts\.xml$', 'index.php?writgo_sitemap=posts', 'top');
    add_rewrite_rule('^sitemap-pages\.xml$', 'index.php?writgo_sitemap=pages', 'top');
    add_rewrite_rule('^sitemap-categories\.xml$', 'index.php?writgo_sitemap=categories', 'top');
    add_rewrite_rule('^sitemap-tags\.xml$', 'index.php?writgo_sitemap=tags', 'top');
}

add_filter('query_vars', 'writgo_sitemap_query_vars');
function writgo_sitemap_query_vars($vars) {
    $vars[] = 'writgo_sitemap';
    return $vars;
}

/**
 * Handle sitemap requests
 */
add_action('template_redirect', 'writgo_sitemap_render');
function writgo_sitemap_render() {
    $sitemap = get_query_var('writgo_sitemap');
    if (!$sitemap) {
        return;
    }

    header('Content-Type: application/xml; charset=UTF-8');
    header('X-Robots-Tag: noindex');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

    switch ($sitemap) {
        case 'index':
            writgo_sitemap_index();
            break;
        case 'posts':
            writgo_sitemap_posts();
            break;
        case 'pages':
            writgo_sitemap_pages();
            break;
        case 'categories':
            writgo_sitemap_categories();
            break;
        case 'tags':
            writgo_sitemap_tags();
            break;
    }
    exit;
}

function writgo_sitemap_index() {
    $sitemaps = array(
        array('loc' => home_url('/sitemap-posts.xml'), 'lastmod' => writgo_sitemap_latest_date('post')),
        array('loc' => home_url('/sitemap-pages.xml'), 'lastmod' => writgo_sitemap_latest_date('page')),
        array('loc' => home_url('/sitemap-categories.xml'), 'lastmod' => writgo_sitemap_latest_date('post')),
    );

    // Only include tags sitemap if there are tags with posts
    $tags = get_tags(array('hide_empty' => true, 'number' => 1));
    if (!empty($tags)) {
        $sitemaps[] = array('loc' => home_url('/sitemap-tags.xml'), 'lastmod' => writgo_sitemap_latest_date('post'));
    }

    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($sitemaps as $sm) {
        echo '  <sitemap>' . "\n";
        echo '    <loc>' . esc_url($sm['loc']) . '</loc>' . "\n";
        if ($sm['lastmod']) {
            echo '    <lastmod>' . $sm['lastmod'] . '</lastmod>' . "\n";
        }
        echo '  </sitemap>' . "\n";
    }
    echo '</sitemapindex>';
}

function writgo_sitemap_posts() {
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Homepage
    echo '  <url>' . "\n";
    echo '    <loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
    echo '    <changefreq>daily</changefreq>' . "\n";
    echo '    <priority>1.0</priority>' . "\n";
    echo '  </url>' . "\n";

    $posts = get_posts(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 1000,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'meta_query'     => array(
            'relation' => 'OR',
            array('key' => '_writgo_noindex', 'compare' => 'NOT EXISTS'),
            array('key' => '_writgo_noindex', 'value' => '1', 'compare' => '!='),
        ),
    ));

    foreach ($posts as $p) {
        $mod = get_the_modified_date('c', $p);
        $age = (time() - strtotime($p->post_modified)) / DAY_IN_SECONDS;
        $priority = $age < 7 ? '0.9' : ($age < 30 ? '0.8' : ($age < 90 ? '0.7' : '0.6'));
        $freq = $age < 7 ? 'daily' : ($age < 30 ? 'weekly' : 'monthly');

        echo '  <url>' . "\n";
        echo '    <loc>' . esc_url(get_permalink($p)) . '</loc>' . "\n";
        echo '    <lastmod>' . $mod . '</lastmod>' . "\n";
        echo '    <changefreq>' . $freq . '</changefreq>' . "\n";
        echo '    <priority>' . $priority . '</priority>' . "\n";
        echo '  </url>' . "\n";
    }

    echo '</urlset>';
}

function writgo_sitemap_pages() {
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $pages = get_posts(array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => 500,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'meta_query'     => array(
            'relation' => 'OR',
            array('key' => '_writgo_noindex', 'compare' => 'NOT EXISTS'),
            array('key' => '_writgo_noindex', 'value' => '1', 'compare' => '!='),
        ),
    ));

    foreach ($pages as $p) {
        // Skip front page (already in posts sitemap)
        if (intval(get_option('page_on_front')) === $p->ID) {
            continue;
        }
        echo '  <url>' . "\n";
        echo '    <loc>' . esc_url(get_permalink($p)) . '</loc>' . "\n";
        echo '    <lastmod>' . get_the_modified_date('c', $p) . '</lastmod>' . "\n";
        echo '    <changefreq>monthly</changefreq>' . "\n";
        echo '    <priority>0.6</priority>' . "\n";
        echo '  </url>' . "\n";
    }

    echo '</urlset>';
}

function writgo_sitemap_categories() {
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $cats = get_categories(array('hide_empty' => true));
    foreach ($cats as $cat) {
        if ($cat->slug === 'uncategorized') continue;
        echo '  <url>' . "\n";
        echo '    <loc>' . esc_url(get_category_link($cat)) . '</loc>' . "\n";
        echo '    <changefreq>weekly</changefreq>' . "\n";
        echo '    <priority>0.5</priority>' . "\n";
        echo '  </url>' . "\n";
    }

    echo '</urlset>';
}

function writgo_sitemap_tags() {
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $tags = get_tags(array('hide_empty' => true, 'number' => 200));
    foreach ($tags as $tag) {
        echo '  <url>' . "\n";
        echo '    <loc>' . esc_url(get_tag_link($tag)) . '</loc>' . "\n";
        echo '    <changefreq>weekly</changefreq>' . "\n";
        echo '    <priority>0.3</priority>' . "\n";
        echo '  </url>' . "\n";
    }

    echo '</urlset>';
}

function writgo_sitemap_latest_date($post_type) {
    $latest = get_posts(array(
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
    ));
    return !empty($latest) ? get_the_modified_date('c', $latest[0]) : '';
}

/**
 * Disable WordPress default sitemap (we have our own)
 */
add_filter('wp_sitemaps_enabled', '__return_false');

// =============================================================================
// 2. ROBOTS.TXT OPTIMIZATION
// =============================================================================

add_filter('robots_txt', 'writgo_robots_txt', 10, 2);
function writgo_robots_txt($output, $public) {
    if ($public == '0') {
        return "User-agent: *\nDisallow: /\n";
    }

    $site_url = home_url('/');

    $robots = "# Writgo SEO Optimized Robots.txt\n";
    $robots .= "# Generated by Writgo Theme v" . WRITGO_VERSION . "\n\n";

    // All crawlers
    $robots .= "User-agent: *\n";
    $robots .= "Allow: /\n";
    $robots .= "Disallow: /wp-admin/\n";
    $robots .= "Allow: /wp-admin/admin-ajax.php\n";
    $robots .= "Disallow: /wp-includes/\n";
    $robots .= "Disallow: /wp-content/plugins/\n";
    $robots .= "Disallow: /wp-content/cache/\n";
    $robots .= "Disallow: /wp-json/\n";
    $robots .= "Disallow: /feed/\n";
    $robots .= "Disallow: /comments/feed/\n";
    $robots .= "Disallow: /?s=\n";
    $robots .= "Disallow: /search/\n";
    $robots .= "Disallow: /*?replytocom=\n";
    $robots .= "Disallow: /tag/*/page/\n";
    $robots .= "Disallow: /author/\n";
    $robots .= "Disallow: /trackback/\n";
    $robots .= "Disallow: /xmlrpc.php\n";
    $robots .= "Disallow: /readme.html\n";
    $robots .= "Disallow: /license.txt\n\n";

    // AI crawlers - specific rules
    $robots .= "# AI Crawlers\n";
    $robots .= "User-agent: GPTBot\n";
    $robots .= "Allow: /\n";
    $robots .= "Disallow: /wp-admin/\n\n";

    $robots .= "User-agent: Google-Extended\n";
    $robots .= "Allow: /\n\n";

    $robots .= "User-agent: ClaudeBot\n";
    $robots .= "Allow: /\n\n";

    $robots .= "User-agent: PerplexityBot\n";
    $robots .= "Allow: /\n\n";

    $robots .= "User-agent: Applebot-Extended\n";
    $robots .= "Allow: /\n\n";

    // Bad bots
    $robots .= "# Block bad bots\n";
    $robots .= "User-agent: AhrefsBot\n";
    $robots .= "Disallow: /\n\n";
    $robots .= "User-agent: SemrushBot\n";
    $robots .= "Disallow: /\n\n";
    $robots .= "User-agent: MJ12bot\n";
    $robots .= "Disallow: /\n\n";
    $robots .= "User-agent: DotBot\n";
    $robots .= "Disallow: /\n\n";
    $robots .= "User-agent: BLEXBot\n";
    $robots .= "Disallow: /\n\n";

    // Crawl-delay for aggressive bots
    $robots .= "User-agent: Baiduspider\n";
    $robots .= "Crawl-delay: 10\n\n";

    $robots .= "User-agent: YandexBot\n";
    $robots .= "Crawl-delay: 5\n\n";

    // Sitemaps
    $robots .= "# Sitemaps\n";
    $robots .= "Sitemap: " . $site_url . "sitemap.xml\n\n";

    // LLMs.txt
    $robots .= "# AI/LLM info\n";
    $robots .= "# See also: " . $site_url . "llms.txt\n";

    return $robots;
}

// =============================================================================
// 3. LLMS.TXT - AI/LLM Crawler Instructions
// =============================================================================

add_action('init', 'writgo_llms_txt_init');
function writgo_llms_txt_init() {
    add_rewrite_rule('^llms\.txt$', 'index.php?writgo_llms_txt=1', 'top');
    add_rewrite_rule('^llms-full\.txt$', 'index.php?writgo_llms_txt=full', 'top');
    add_rewrite_rule('^\.well-known/llms\.txt$', 'index.php?writgo_llms_txt=1', 'top');
}

add_filter('query_vars', 'writgo_llms_txt_query_vars');
function writgo_llms_txt_query_vars($vars) {
    $vars[] = 'writgo_llms_txt';
    return $vars;
}

add_action('template_redirect', 'writgo_llms_txt_render');
function writgo_llms_txt_render() {
    $llms = get_query_var('writgo_llms_txt');
    if (!$llms) {
        return;
    }

    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: public, max-age=86400');

    $site_name = get_bloginfo('name');
    $site_desc = get_bloginfo('description');
    $site_url  = home_url('/');
    $lang      = get_bloginfo('language');

    echo "# " . $site_name . "\n\n";
    echo "> " . $site_desc . "\n\n";
    echo "Website: " . $site_url . "\n";
    echo "Language: " . $lang . "\n\n";

    // About section
    echo "## About\n\n";
    $about_page = get_page_by_path('over-ons');
    if (!$about_page) {
        $about_page = get_page_by_path('about');
    }
    if ($about_page) {
        $about_excerpt = wp_trim_words(strip_tags($about_page->post_content), 80);
        echo $about_excerpt . "\n\n";
    } else {
        echo $site_desc . "\n\n";
    }

    // Categories
    $cats = get_categories(array('hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC'));
    if (!empty($cats)) {
        echo "## Topics\n\n";
        foreach ($cats as $cat) {
            if ($cat->slug === 'uncategorized') continue;
            echo "- [" . $cat->name . "](" . get_category_link($cat) . "): " . ($cat->description ?: $cat->count . " articles") . "\n";
        }
        echo "\n";
    }

    // Latest/important content
    echo "## Key Content\n\n";
    $posts = get_posts(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => ($llms === 'full') ? 100 : 20,
        'orderby'        => 'modified',
        'order'          => 'DESC',
    ));

    foreach ($posts as $p) {
        $excerpt = has_excerpt($p->ID) ? get_the_excerpt($p) : wp_trim_words(strip_tags($p->post_content), 25);
        echo "- [" . get_the_title($p) . "](" . get_permalink($p) . "): " . $excerpt . "\n";
    }
    echo "\n";

    // Important pages
    $pages = get_posts(array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => 20,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ));

    $important_pages = array();
    foreach ($pages as $p) {
        if (intval(get_option('page_on_front')) === $p->ID) continue;
        $important_pages[] = $p;
    }

    if (!empty($important_pages)) {
        echo "## Pages\n\n";
        foreach ($important_pages as $p) {
            echo "- [" . get_the_title($p) . "](" . get_permalink($p) . ")\n";
        }
        echo "\n";
    }

    // Contact info
    echo "## Contact\n\n";
    echo "- Website: " . $site_url . "\n";
    $contact_page = get_page_by_path('contact');
    if ($contact_page) {
        echo "- Contact: " . get_permalink($contact_page) . "\n";
    }
    echo "\n";

    // Technical info
    echo "## Technical\n\n";
    echo "- Sitemap: " . $site_url . "sitemap.xml\n";
    echo "- Feed: " . get_bloginfo('rss2_url') . "\n";
    echo "- Robots: " . $site_url . "robots.txt\n";
    echo "- Full version: " . $site_url . "llms-full.txt\n";

    exit;
}

// =============================================================================
// 4. INDEXNOW - Instant Indexing (Bing, Yandex, Seznam, Naver)
// =============================================================================

/**
 * Generate and store IndexNow API key
 */
function writgo_get_indexnow_key() {
    $key = get_option('writgo_indexnow_key');
    if (!$key) {
        $key = wp_generate_uuid4();
        $key = str_replace('-', '', $key); // IndexNow wants alphanumeric only
        update_option('writgo_indexnow_key', $key);
    }
    return $key;
}

/**
 * Serve IndexNow key verification file
 */
add_action('init', 'writgo_indexnow_init');
function writgo_indexnow_init() {
    $key = writgo_get_indexnow_key();
    add_rewrite_rule('^' . $key . '\.txt$', 'index.php?writgo_indexnow_verify=1', 'top');
}

add_filter('query_vars', 'writgo_indexnow_query_vars');
function writgo_indexnow_query_vars($vars) {
    $vars[] = 'writgo_indexnow_verify';
    return $vars;
}

add_action('template_redirect', 'writgo_indexnow_verify_render');
function writgo_indexnow_verify_render() {
    if (!get_query_var('writgo_indexnow_verify')) {
        return;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo writgo_get_indexnow_key();
    exit;
}

/**
 * Submit URL to IndexNow when post is published or updated
 */
add_action('publish_post', 'writgo_indexnow_submit', 20, 2);
add_action('publish_page', 'writgo_indexnow_submit', 20, 2);
function writgo_indexnow_submit($post_id, $post) {
    // Don't submit noindex posts
    if (get_post_meta($post_id, '_writgo_noindex', true) === '1') {
        return;
    }

    // Rate limit: max 1 submission per post per hour
    $last = get_post_meta($post_id, '_writgo_indexnow_last', true);
    if ($last && (time() - intval($last)) < 3600) {
        return;
    }

    $key = writgo_get_indexnow_key();
    $url = get_permalink($post_id);

    $response = wp_remote_post('https://api.indexnow.org/indexnow', array(
        'timeout' => 10,
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => wp_json_encode(array(
            'host'    => wp_parse_url(home_url(), PHP_URL_HOST),
            'key'     => $key,
            'keyLocation' => home_url('/' . $key . '.txt'),
            'urlList' => array($url),
        )),
    ));

    if (!is_wp_error($response)) {
        update_post_meta($post_id, '_writgo_indexnow_last', time());
    }
}

// =============================================================================
// 5. PING SEARCH ENGINES ON PUBLISH
// =============================================================================

add_action('publish_post', 'writgo_ping_search_engines', 30, 2);
function writgo_ping_search_engines($post_id, $post) {
    // Only ping for new posts (within 5 minutes of creation)
    if ((time() - strtotime($post->post_date)) > 300) {
        return;
    }

    $sitemap_url = home_url('/sitemap.xml');

    // Google (sitemap ping is deprecated but Google still accepts it)
    wp_remote_get('https://www.google.com/ping?sitemap=' . urlencode($sitemap_url), array(
        'timeout'  => 5,
        'blocking' => false,
    ));

    // Bing (handled by IndexNow above, but also ping sitemap)
    wp_remote_get('https://www.bing.com/ping?sitemap=' . urlencode($sitemap_url), array(
        'timeout'  => 5,
        'blocking' => false,
    ));
}

// =============================================================================
// 6. REDIRECT MANAGER (301/302)
// =============================================================================

/**
 * Admin page for managing redirects
 */
add_action('admin_menu', 'writgo_redirects_menu');
function writgo_redirects_menu() {
    add_submenu_page(
        'options-general.php',
        'Redirects',
        'Redirects',
        'manage_options',
        'writgo-redirects',
        'writgo_redirects_page'
    );
}

function writgo_redirects_page() {
    // Save redirects
    if (isset($_POST['writgo_redirects_nonce']) && wp_verify_nonce($_POST['writgo_redirects_nonce'], 'writgo_save_redirects')) {
        $redirects = array();
        if (!empty($_POST['redirect_from']) && is_array($_POST['redirect_from'])) {
            foreach ($_POST['redirect_from'] as $i => $from) {
                $from = sanitize_text_field(trim($from));
                $to   = esc_url_raw(trim($_POST['redirect_to'][$i]));
                $type = in_array($_POST['redirect_type'][$i], array('301', '302')) ? $_POST['redirect_type'][$i] : '301';
                if ($from && $to) {
                    $redirects[] = array('from' => $from, 'to' => $to, 'type' => $type);
                }
            }
        }
        update_option('writgo_redirects', $redirects);
        echo '<div class="notice notice-success"><p>Redirects opgeslagen.</p></div>';
    }

    $redirects = get_option('writgo_redirects', array());
    ?>
    <div class="wrap">
        <h1>Writgo Redirects</h1>
        <form method="post">
            <?php wp_nonce_field('writgo_save_redirects', 'writgo_redirects_nonce'); ?>
            <table class="widefat striped" id="writgo-redirects-table">
                <thead>
                    <tr>
                        <th>Van (pad)</th>
                        <th>Naar (URL)</th>
                        <th>Type</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($redirects)) : foreach ($redirects as $r) : ?>
                    <tr>
                        <td><input type="text" name="redirect_from[]" value="<?php echo esc_attr($r['from']); ?>" style="width:100%" placeholder="/oud-pad/"></td>
                        <td><input type="text" name="redirect_to[]" value="<?php echo esc_attr($r['to']); ?>" style="width:100%" placeholder="https://example.com/nieuw-pad/"></td>
                        <td>
                            <select name="redirect_type[]">
                                <option value="301" <?php selected($r['type'], '301'); ?>>301 (Permanent)</option>
                                <option value="302" <?php selected($r['type'], '302'); ?>>302 (Tijdelijk)</option>
                            </select>
                        </td>
                        <td><button type="button" class="button" onclick="this.closest('tr').remove()">Verwijder</button></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
            <p>
                <button type="button" class="button" onclick="writgoAddRedirectRow()">+ Redirect toevoegen</button>
                <?php submit_button('Opslaan', 'primary', 'submit', false); ?>
            </p>
        </form>
        <script>
        function writgoAddRedirectRow() {
            var tbody = document.querySelector('#writgo-redirects-table tbody');
            var tr = document.createElement('tr');
            tr.innerHTML = '<td><input type="text" name="redirect_from[]" style="width:100%" placeholder="/oud-pad/"></td>' +
                '<td><input type="text" name="redirect_to[]" style="width:100%" placeholder="https://example.com/nieuw-pad/"></td>' +
                '<td><select name="redirect_type[]"><option value="301">301 (Permanent)</option><option value="302">302 (Tijdelijk)</option></select></td>' +
                '<td><button type="button" class="button" onclick="this.closest(\'tr\').remove()">Verwijder</button></td>';
            tbody.appendChild(tr);
        }
        </script>
    </div>
    <?php
}

/**
 * Process redirects on every request (early hook)
 */
add_action('template_redirect', 'writgo_process_redirects', 1);
function writgo_process_redirects() {
    $redirects = get_option('writgo_redirects', array());
    if (empty($redirects)) {
        return;
    }

    $request_path = rtrim(wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if (!$request_path) {
        $request_path = '/';
    }

    foreach ($redirects as $r) {
        $from = rtrim($r['from'], '/');
        if (!$from) $from = '/';

        if (strtolower($request_path) === strtolower($from)) {
            wp_redirect($r['to'], intval($r['type']));
            exit;
        }
    }
}

// =============================================================================
// 7. ADDITIONAL SEO ENHANCEMENTS
// =============================================================================

/**
 * Add rel="prev"/"next" for paginated archives
 */
add_action('wp_head', 'writgo_seo_pagination_links', 3);
function writgo_seo_pagination_links() {
    if (!is_archive() && !is_home()) {
        return;
    }

    global $wp_query;
    $current = max(1, get_query_var('paged'));
    $total   = $wp_query->max_num_pages;

    if ($total <= 1) {
        return;
    }

    if ($current > 1) {
        echo '<link rel="prev" href="' . esc_url(get_pagenum_link($current - 1)) . '" />' . "\n";
    }
    if ($current < $total) {
        echo '<link rel="next" href="' . esc_url(get_pagenum_link($current + 1)) . '" />' . "\n";
    }
}

/**
 * Remove unnecessary head clutter
 */
remove_action('wp_head', 'wp_generator');                // WordPress version
remove_action('wp_head', 'wlwmanifest_link');            // Windows Live Writer
remove_action('wp_head', 'rsd_link');                    // Really Simple Discovery
remove_action('wp_head', 'wp_shortlink_wp_head');        // Shortlink
remove_action('wp_head', 'rest_output_link_wp_head');    // REST API link
remove_action('wp_head', 'wp_oembed_add_discovery_links'); // oEmbed discovery
remove_action('wp_head', 'feed_links_extra', 3);         // Extra feed links

/**
 * Security headers via PHP (fallback when no htaccess)
 */
add_action('send_headers', 'writgo_security_headers');
function writgo_security_headers() {
    if (is_admin()) {
        return;
    }
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}

/**
 * Add last-modified header for better caching/crawling
 */
add_action('template_redirect', 'writgo_last_modified_header', 99);
function writgo_last_modified_header() {
    if (is_singular() && !is_admin()) {
        global $post;
        if ($post) {
            $modified = get_the_modified_date('D, d M Y H:i:s', $post) . ' GMT';
            header('Last-Modified: ' . $modified);
        }
    }
}

/**
 * Flush rewrite rules on theme activation (needed for sitemap/llms.txt/indexnow)
 */
add_action('after_switch_theme', 'writgo_seo_advanced_flush_rules');
function writgo_seo_advanced_flush_rules() {
    writgo_sitemap_init();
    writgo_llms_txt_init();
    writgo_indexnow_init();
    flush_rewrite_rules();
}

// Also flush on version change
add_action('init', 'writgo_seo_advanced_version_check', 99);
function writgo_seo_advanced_version_check() {
    $stored = get_option('writgo_seo_advanced_version');
    if ($stored !== WRITGO_VERSION) {
        flush_rewrite_rules();
        update_option('writgo_seo_advanced_version', WRITGO_VERSION);
    }
}
