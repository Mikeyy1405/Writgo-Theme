<?php
/**
 * Writgo SEO
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add SEO Meta Tags
 */
add_action('wp_head', 'writgo_seo_meta_tags', 1);
function writgo_seo_meta_tags() {
    // Skip if Yoast or RankMath is active
    if (defined('WPSEO_VERSION') || class_exists('RankMath')) {
        return;
    }

    global $post;

    // Default values
    $title = get_bloginfo('name');
    $description = get_bloginfo('description');
    $image = '';
    $url = home_url('/');
    $robots = '';

    if (is_singular()) {
        // Check for custom SEO title first
        $custom_title = get_post_meta($post->ID, '_writgo_seo_title', true);
        $title = $custom_title ? $custom_title : get_the_title();

        // Check for custom meta description
        $custom_description = get_post_meta($post->ID, '_writgo_seo_description', true);
        if ($custom_description) {
            $description = $custom_description;
        } else {
            $description = has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_tags($post->post_content), 30);
        }

        $url = get_permalink();

        // Check for custom OG image first, then featured image
        $og_image_id = get_post_meta($post->ID, '_writgo_og_image', true);
        if ($og_image_id) {
            $image = wp_get_attachment_image_url($og_image_id, 'large');
        } elseif (has_post_thumbnail()) {
            $image = get_the_post_thumbnail_url($post->ID, 'large');
        }

        // Get robots settings
        $robots_index = get_post_meta($post->ID, '_writgo_robots_index', true) ?: 'index';
        $robots_follow = get_post_meta($post->ID, '_writgo_robots_follow', true) ?: 'follow';
        $robots = $robots_index . ', ' . $robots_follow;

    } elseif (is_category()) {
        $title = single_cat_title('', false);
        $description = category_description() ?: $title;
        $url = get_category_link(get_queried_object_id());
    } elseif (is_search()) {
        $title = 'Zoekresultaten voor: ' . get_search_query();
        $robots = 'noindex, follow';
    }

    // Clean description
    $description = wp_strip_all_tags($description);
    $description = substr($description, 0, 160);

    // Output meta tags
    ?>
    <meta name="description" content="<?php echo esc_attr($description); ?>">
    <?php if ($robots) : ?>
    <meta name="robots" content="<?php echo esc_attr($robots); ?>">
    <?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($description); ?>">
    <meta property="og:url" content="<?php echo esc_url($url); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <meta property="og:type" content="<?php echo is_singular() ? 'article' : 'website'; ?>">
    <?php if ($image) : ?>
    <meta property="og:image" content="<?php echo esc_url($image); ?>">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($description); ?>">
    <?php if ($image) : ?>
    <meta name="twitter:image" content="<?php echo esc_url($image); ?>">
    <?php endif; ?>
    <?php
}

/**
 * Add Schema.org Article markup
 */
add_action('wp_head', 'writgo_schema_markup');
function writgo_schema_markup() {
    if (!is_singular('post')) {
        return;
    }

    global $post;

    // Use custom SEO fields if available
    $custom_title = get_post_meta($post->ID, '_writgo_seo_title', true);
    $custom_description = get_post_meta($post->ID, '_writgo_seo_description', true);

    $headline = $custom_title ? $custom_title : get_the_title();
    $description = $custom_description ? $custom_description : (has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_tags($post->post_content), 30));

    $schema = array(
        '@context'      => 'https://schema.org',
        '@type'         => 'Article',
        'headline'      => $headline,
        'description'   => $description,
        'datePublished' => get_the_date('c'),
        'dateModified'  => get_the_modified_date('c'),
        'url'           => get_permalink(),
        'author'        => array(
            '@type' => 'Person',
            'name'  => get_the_author(),
        ),
        'publisher'     => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
        ),
    );

    // Add focus keyword as keywords if set
    $focus_keyword = get_post_meta($post->ID, '_writgo_focus_keyword', true);
    if ($focus_keyword) {
        $schema['keywords'] = $focus_keyword;
    }

    if (has_post_thumbnail()) {
        $schema['image'] = get_the_post_thumbnail_url($post->ID, 'large');
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * Optimize title tag
 */
add_filter('document_title_separator', function() {
    return '|';
});

/**
 * Use custom SEO title if set
 */
add_filter('document_title_parts', 'writgo_custom_document_title');
function writgo_custom_document_title($title_parts) {
    // Skip if Yoast or RankMath is active
    if (defined('WPSEO_VERSION') || class_exists('RankMath')) {
        return $title_parts;
    }

    if (is_singular()) {
        global $post;
        $custom_title = get_post_meta($post->ID, '_writgo_seo_title', true);
        if ($custom_title) {
            $title_parts['title'] = $custom_title;
        }
    }

    return $title_parts;
}

/**
 * Add canonical URL
 */
add_action('wp_head', 'writgo_canonical_url');
function writgo_canonical_url() {
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    } elseif (is_category()) {
        echo '<link rel="canonical" href="' . esc_url(get_category_link(get_queried_object_id())) . '">' . "\n";
    }
}

/**
 * Add Google Search Console verification meta tag
 */
add_action('wp_head', 'writgo_gsc_verification', 1);
function writgo_gsc_verification() {
    $gsc_code = get_theme_mod('writgo_gsc_verification', '');
    if ($gsc_code) {
        echo '<meta name="google-site-verification" content="' . esc_attr($gsc_code) . '">' . "\n";
    }

    $bing_code = get_theme_mod('writgo_bing_verification', '');
    if ($bing_code) {
        echo '<meta name="msvalidate.01" content="' . esc_attr($bing_code) . '">' . "\n";
    }
}

/**
 * =====================================================
 * BREADCRUMB SCHEMA
 * =====================================================
 */
add_action('wp_head', 'writgo_breadcrumb_schema');
function writgo_breadcrumb_schema() {
    if (is_front_page() || is_home()) {
        return;
    }

    $items = array();
    $position = 1;

    // Home
    $items[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => __('Home', 'writgo-affiliate'),
        'item' => home_url('/')
    );

    if (is_category()) {
        $cat = get_queried_object();
        if ($cat->parent) {
            $parent = get_category($cat->parent);
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $parent->name,
                'item' => get_category_link($parent->term_id)
            );
        }
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $cat->name,
            'item' => get_category_link($cat->term_id)
        );
    } elseif (is_singular('post')) {
        $cats = get_the_category();
        if ($cats) {
            $cat = $cats[0];
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $cat->name,
                'item' => get_category_link($cat->term_id)
            );
        }
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title()
        );
    } elseif (is_page()) {
        global $post;
        if ($post->post_parent) {
            $parent = get_post($post->post_parent);
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $parent->post_title,
                'item' => get_permalink($parent->ID)
            );
        }
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title()
        );
    } elseif (is_search()) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => __('Zoekresultaten', 'writgo-affiliate')
        );
    }

    if (count($items) > 1) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        );
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}

/**
 * =====================================================
 * LOCALBUSINESS SCHEMA
 * =====================================================
 */
add_action('wp_head', 'writgo_localbusiness_schema');
function writgo_localbusiness_schema() {
    if (!is_front_page()) {
        return;
    }

    $company_name = get_theme_mod('writgo_company_name', '');
    if (!$company_name) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url('/'),
    );

    // Add logo if custom logo exists
    if (has_custom_logo()) {
        $logo_id = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_url($logo_id, 'full');
        if ($logo_url) {
            $schema['logo'] = $logo_url;
        }
    }

    // Add address if set
    $address = get_theme_mod('writgo_company_address', '');
    $city = get_theme_mod('writgo_company_city', '');
    $postal = get_theme_mod('writgo_company_postal', '');
    if ($address || $city) {
        $schema['address'] = array(
            '@type' => 'PostalAddress',
            'streetAddress' => $address,
            'addressLocality' => $city,
            'postalCode' => $postal,
            'addressCountry' => 'NL'
        );
    }

    // Add contact info
    $email = get_theme_mod('writgo_contact_email', get_option('admin_email'));
    if ($email) {
        $schema['email'] = $email;
    }

    // Add social profiles
    $social_urls = array();
    $social_platforms = array('facebook', 'instagram', 'twitter', 'linkedin', 'youtube', 'pinterest', 'tiktok');
    foreach ($social_platforms as $platform) {
        $url = get_theme_mod('writgo_social_' . $platform, '');
        if ($url) {
            $social_urls[] = $url;
        }
    }
    if ($social_urls) {
        $schema['sameAs'] = $social_urls;
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * =====================================================
 * FAQ SCHEMA SHORTCODE
 * [writgo_faq]
 * <faq question="Vraag hier?">Antwoord hier</faq>
 * [/writgo_faq]
 * =====================================================
 */
add_shortcode('writgo_faq', 'writgo_faq_shortcode');
function writgo_faq_shortcode($atts, $content = null) {
    if (!$content) {
        return '';
    }

    // Parse FAQ items from content
    $faq_items = array();
    preg_match_all('/<faq\s+question=["\']([^"\']+)["\']>(.*?)<\/faq>/is', $content, $matches, PREG_SET_ORDER);

    if (empty($matches)) {
        return '';
    }

    $html = '<div class="writgo-faq-list">';
    foreach ($matches as $match) {
        $question = esc_html($match[1]);
        $answer = wp_kses_post(trim($match[2]));

        $faq_items[] = array(
            '@type' => 'Question',
            'name' => $question,
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text' => strip_tags($answer)
            )
        );

        $html .= '<div class="writgo-faq-item">';
        $html .= '<button class="writgo-faq-question" aria-expanded="false">';
        $html .= '<span>' . $question . '</span>';
        $html .= '<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $html .= '</button>';
        $html .= '<div class="writgo-faq-answer">' . $answer . '</div>';
        $html .= '</div>';
    }
    $html .= '</div>';

    // Add FAQ Schema
    if ($faq_items) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faq_items
        );
        $html .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }

    // Add inline styles
    $html .= '<style>
        .writgo-faq-list { margin: 2rem 0; }
        .writgo-faq-item { border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 12px; overflow: hidden; }
        .writgo-faq-question { width: 100%; padding: 16px 20px; background: #f9fafb; border: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 16px; font-weight: 600; color: #1f2937; text-align: left; transition: background 0.2s; }
        .writgo-faq-question:hover { background: #f3f4f6; }
        .writgo-faq-question svg { transition: transform 0.3s; flex-shrink: 0; margin-left: 12px; }
        .writgo-faq-question[aria-expanded="true"] svg { transform: rotate(180deg); }
        .writgo-faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.3s ease, padding 0.3s ease; padding: 0 20px; }
        .writgo-faq-item.active .writgo-faq-answer { max-height: 500px; padding: 16px 20px; }
    </style>';

    // Add inline script
    $html .= '<script>
        document.querySelectorAll(".writgo-faq-question").forEach(function(btn) {
            btn.addEventListener("click", function() {
                var item = this.closest(".writgo-faq-item");
                var isActive = item.classList.contains("active");
                document.querySelectorAll(".writgo-faq-item").forEach(function(i) { i.classList.remove("active"); });
                document.querySelectorAll(".writgo-faq-question").forEach(function(b) { b.setAttribute("aria-expanded", "false"); });
                if (!isActive) {
                    item.classList.add("active");
                    this.setAttribute("aria-expanded", "true");
                }
            });
        });
    </script>';

    return $html;
}

/**
 * =====================================================
 * XML SITEMAP
 * =====================================================
 */
add_action('init', 'writgo_register_sitemap_routes');
function writgo_register_sitemap_routes() {
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

add_action('template_redirect', 'writgo_render_sitemap');
function writgo_render_sitemap() {
    $sitemap_type = get_query_var('writgo_sitemap');
    if (!$sitemap_type) {
        return;
    }

    // Skip if Yoast or RankMath is active
    if (defined('WPSEO_VERSION') || class_exists('RankMath')) {
        return;
    }

    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

    if ($sitemap_type === 'index') {
        writgo_sitemap_index();
    } elseif ($sitemap_type === 'posts') {
        writgo_sitemap_posts();
    } elseif ($sitemap_type === 'pages') {
        writgo_sitemap_pages();
    } elseif ($sitemap_type === 'categories') {
        writgo_sitemap_categories();
    }
    exit;
}

function writgo_sitemap_index() {
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Posts sitemap
    echo '<sitemap>';
    echo '<loc>' . esc_url(home_url('/sitemap-posts.xml')) . '</loc>';
    echo '<lastmod>' . date('c') . '</lastmod>';
    echo '</sitemap>' . "\n";

    // Pages sitemap
    echo '<sitemap>';
    echo '<loc>' . esc_url(home_url('/sitemap-pages.xml')) . '</loc>';
    echo '<lastmod>' . date('c') . '</lastmod>';
    echo '</sitemap>' . "\n";

    // Categories sitemap
    echo '<sitemap>';
    echo '<loc>' . esc_url(home_url('/sitemap-categories.xml')) . '</loc>';
    echo '<lastmod>' . date('c') . '</lastmod>';
    echo '</sitemap>' . "\n";

    echo '</sitemapindex>';
}

function writgo_sitemap_posts() {
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 1000,
        'orderby' => 'modified',
        'order' => 'DESC',
        'meta_query' => array(
            'relation' => 'OR',
            array('key' => '_writgo_robots_index', 'compare' => 'NOT EXISTS'),
            array('key' => '_writgo_robots_index', 'value' => 'index')
        )
    ));

    // Add homepage
    echo '<url>';
    echo '<loc>' . esc_url(home_url('/')) . '</loc>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>1.0</priority>';
    echo '</url>' . "\n";

    foreach ($posts as $post) {
        echo '<url>';
        echo '<loc>' . esc_url(get_permalink($post)) . '</loc>';
        echo '<lastmod>' . get_the_modified_date('c', $post) . '</lastmod>';
        echo '<changefreq>weekly</changefreq>';
        echo '<priority>0.8</priority>';
        echo '</url>' . "\n";
    }

    echo '</urlset>';
}

function writgo_sitemap_pages() {
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => 500,
        'orderby' => 'modified',
        'order' => 'DESC',
        'meta_query' => array(
            'relation' => 'OR',
            array('key' => '_writgo_robots_index', 'compare' => 'NOT EXISTS'),
            array('key' => '_writgo_robots_index', 'value' => 'index')
        )
    ));

    foreach ($pages as $page) {
        echo '<url>';
        echo '<loc>' . esc_url(get_permalink($page)) . '</loc>';
        echo '<lastmod>' . get_the_modified_date('c', $page) . '</lastmod>';
        echo '<changefreq>monthly</changefreq>';
        echo '<priority>0.6</priority>';
        echo '</url>' . "\n";
    }

    echo '</urlset>';
}

function writgo_sitemap_categories() {
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $categories = get_categories(array('hide_empty' => true));

    foreach ($categories as $cat) {
        echo '<url>';
        echo '<loc>' . esc_url(get_category_link($cat->term_id)) . '</loc>';
        echo '<changefreq>weekly</changefreq>';
        echo '<priority>0.7</priority>';
        echo '</url>' . "\n";
    }

    echo '</urlset>';
}

/**
 * =====================================================
 * LLMS.TXT (AI Crawler Instructions)
 * =====================================================
 */
add_action('init', 'writgo_register_llms_routes');
function writgo_register_llms_routes() {
    add_rewrite_rule('^llms\.txt$', 'index.php?writgo_llms=1', 'top');
}

add_filter('query_vars', 'writgo_llms_query_vars');
function writgo_llms_query_vars($vars) {
    $vars[] = 'writgo_llms';
    return $vars;
}

add_action('template_redirect', 'writgo_render_llms');
function writgo_render_llms() {
    if (!get_query_var('writgo_llms')) {
        return;
    }

    header('Content-Type: text/plain; charset=utf-8');

    $site_name = get_bloginfo('name');
    $site_desc = get_bloginfo('description');
    $site_url = home_url('/');

    // Get custom llms.txt content or use default
    $custom_content = get_theme_mod('writgo_llms_content', '');

    if ($custom_content) {
        echo $custom_content;
    } else {
        // Default llms.txt content
        echo "# " . $site_name . "\n\n";
        echo "> " . $site_desc . "\n\n";
        echo "Website: " . $site_url . "\n\n";
        echo "## Over deze website\n\n";
        echo "Dit is een affiliate website met reviews en vergelijkingen.\n";
        echo "De content is bedoeld om bezoekers te helpen bij aankoopbeslissingen.\n\n";
        echo "## Belangrijke pagina's\n\n";

        // List recent posts
        $posts = get_posts(array('numberposts' => 10, 'post_status' => 'publish'));
        foreach ($posts as $post) {
            echo "- [" . $post->post_title . "](" . get_permalink($post) . ")\n";
        }

        echo "\n## Categorieën\n\n";
        $cats = get_categories(array('hide_empty' => true, 'number' => 10));
        foreach ($cats as $cat) {
            echo "- [" . $cat->name . "](" . get_category_link($cat) . ")\n";
        }

        echo "\n## Contact\n\n";
        echo "Neem contact op via de contactpagina op de website.\n";

        echo "\n## Licentie\n\n";
        echo "Alle content is auteursrechtelijk beschermd. Gebruik met bronvermelding toegestaan.\n";
    }

    exit;
}

/**
 * =====================================================
 * ROBOTS.TXT CUSTOMIZATION
 * =====================================================
 */
add_filter('robots_txt', 'writgo_custom_robots_txt', 10, 2);
function writgo_custom_robots_txt($output, $public) {
    // Check for custom robots.txt content
    $custom_robots = get_theme_mod('writgo_robots_txt', '');

    if ($custom_robots) {
        return $custom_robots;
    }

    // Default enhanced robots.txt
    $output = "User-agent: *\n";
    $output .= "Allow: /\n";
    $output .= "Disallow: /wp-admin/\n";
    $output .= "Disallow: /wp-includes/\n";
    $output .= "Disallow: /wp-content/plugins/\n";
    $output .= "Disallow: /wp-content/cache/\n";
    $output .= "Disallow: /wp-content/themes/*/assets/\n";
    $output .= "Disallow: /*?*\n";
    $output .= "Disallow: /search/\n\n";

    // Add sitemap reference
    $output .= "Sitemap: " . home_url('/sitemap.xml') . "\n";

    // Add llms.txt reference
    $output .= "\n# AI Crawlers\n";
    $output .= "User-agent: GPTBot\n";
    $output .= "Allow: /\n\n";
    $output .= "User-agent: ChatGPT-User\n";
    $output .= "Allow: /\n\n";
    $output .= "User-agent: Claude-Web\n";
    $output .= "Allow: /\n\n";
    $output .= "User-agent: anthropic-ai\n";
    $output .= "Allow: /\n";

    return $output;
}

/**
 * =====================================================
 * SEO ADMIN SETTINGS (Customizer)
 * =====================================================
 */
add_action('customize_register', 'writgo_seo_customizer');
function writgo_seo_customizer($wp_customize) {
    // SEO Section
    $wp_customize->add_section('writgo_seo_settings', array(
        'title' => __('🔍 SEO Instellingen', 'writgo-affiliate'),
        'priority' => 35,
    ));

    // Google Search Console Verification
    $wp_customize->add_setting('writgo_gsc_verification', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_gsc_verification', array(
        'label' => __('Google Search Console Code', 'writgo-affiliate'),
        'description' => __('Alleen de code, niet de volledige meta tag', 'writgo-affiliate'),
        'section' => 'writgo_seo_settings',
        'type' => 'text',
    ));

    // Bing Verification
    $wp_customize->add_setting('writgo_bing_verification', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_bing_verification', array(
        'label' => __('Bing Webmaster Code', 'writgo-affiliate'),
        'section' => 'writgo_seo_settings',
        'type' => 'text',
    ));

    // Custom robots.txt
    $wp_customize->add_setting('writgo_robots_txt', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('writgo_robots_txt', array(
        'label' => __('Custom robots.txt', 'writgo-affiliate'),
        'description' => __('Laat leeg voor standaard. Voer volledige robots.txt inhoud in.', 'writgo-affiliate'),
        'section' => 'writgo_seo_settings',
        'type' => 'textarea',
    ));

    // Custom llms.txt
    $wp_customize->add_setting('writgo_llms_content', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('writgo_llms_content', array(
        'label' => __('Custom llms.txt', 'writgo-affiliate'),
        'description' => __('Instructies voor AI crawlers. Laat leeg voor automatisch.', 'writgo-affiliate'),
        'section' => 'writgo_seo_settings',
        'type' => 'textarea',
    ));
}

/**
 * Flush rewrite rules on theme activation
 */
add_action('after_switch_theme', 'writgo_flush_rewrite_rules');
function writgo_flush_rewrite_rules() {
    writgo_register_sitemap_routes();
    writgo_register_llms_routes();
    flush_rewrite_rules();
}

/**
 * =====================================================
 * REDIRECT MANAGER
 * =====================================================
 */

// Add admin menu
add_action('admin_menu', 'writgo_redirect_manager_menu');
function writgo_redirect_manager_menu() {
    add_submenu_page(
        'tools.php',
        __('Redirect Manager', 'writgo-affiliate'),
        __('Redirects', 'writgo-affiliate'),
        'manage_options',
        'writgo-redirects',
        'writgo_redirect_manager_page'
    );
}

// Handle form submissions
add_action('admin_init', 'writgo_handle_redirect_actions');
function writgo_handle_redirect_actions() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Add redirect
    if (isset($_POST['writgo_add_redirect']) && wp_verify_nonce($_POST['writgo_redirect_nonce'], 'writgo_redirect_action')) {
        $redirects = get_option('writgo_redirects', array());
        $from = sanitize_text_field($_POST['redirect_from']);
        $to = esc_url_raw($_POST['redirect_to']);
        $type = in_array($_POST['redirect_type'], array('301', '302')) ? $_POST['redirect_type'] : '301';

        if ($from && $to) {
            $redirects[] = array(
                'from' => $from,
                'to' => $to,
                'type' => $type,
                'hits' => 0,
                'created' => current_time('mysql')
            );
            update_option('writgo_redirects', $redirects);
            add_settings_error('writgo_redirects', 'redirect_added', __('Redirect toegevoegd.', 'writgo-affiliate'), 'success');
        }
    }

    // Delete redirect
    if (isset($_GET['delete_redirect']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_redirect')) {
        $redirects = get_option('writgo_redirects', array());
        $index = intval($_GET['delete_redirect']);
        if (isset($redirects[$index])) {
            unset($redirects[$index]);
            $redirects = array_values($redirects);
            update_option('writgo_redirects', $redirects);
        }
        wp_redirect(admin_url('tools.php?page=writgo-redirects&deleted=1'));
        exit;
    }
}

// Admin page
function writgo_redirect_manager_page() {
    $redirects = get_option('writgo_redirects', array());
    ?>
    <div class="wrap">
        <h1><?php _e('Redirect Manager', 'writgo-affiliate'); ?></h1>

        <?php settings_errors('writgo_redirects'); ?>

        <?php if (isset($_GET['deleted'])) : ?>
            <div class="notice notice-success is-dismissible"><p><?php _e('Redirect verwijderd.', 'writgo-affiliate'); ?></p></div>
        <?php endif; ?>

        <div class="writgo-redirect-wrapper" style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-top: 20px;">
            <!-- Add New -->
            <div class="writgo-redirect-add" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;"><?php _e('Nieuwe Redirect', 'writgo-affiliate'); ?></h2>
                <form method="post">
                    <?php wp_nonce_field('writgo_redirect_action', 'writgo_redirect_nonce'); ?>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Van URL (relatief)', 'writgo-affiliate'); ?></label>
                        <input type="text" name="redirect_from" placeholder="/oude-pagina/" style="width: 100%; padding: 8px;" required />
                        <span style="font-size: 12px; color: #666;"><?php _e('Bijv: /oude-url/ of /categorie/oude-post/', 'writgo-affiliate'); ?></span>
                    </p>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Naar URL', 'writgo-affiliate'); ?></label>
                        <input type="url" name="redirect_to" placeholder="https://..." style="width: 100%; padding: 8px;" required />
                    </p>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Type', 'writgo-affiliate'); ?></label>
                        <select name="redirect_type" style="width: 100%; padding: 8px;">
                            <option value="301"><?php _e('301 - Permanent', 'writgo-affiliate'); ?></option>
                            <option value="302"><?php _e('302 - Tijdelijk', 'writgo-affiliate'); ?></option>
                        </select>
                    </p>

                    <p>
                        <button type="submit" name="writgo_add_redirect" class="button button-primary"><?php _e('Redirect Toevoegen', 'writgo-affiliate'); ?></button>
                    </p>
                </form>
            </div>

            <!-- List -->
            <div class="writgo-redirect-list" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;"><?php _e('Actieve Redirects', 'writgo-affiliate'); ?> <span style="font-weight: normal; color: #666;">(<?php echo count($redirects); ?>)</span></h2>

                <?php if (empty($redirects)) : ?>
                    <p style="color: #666;"><?php _e('Nog geen redirects aangemaakt.', 'writgo-affiliate'); ?></p>
                <?php else : ?>
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th><?php _e('Van', 'writgo-affiliate'); ?></th>
                                <th><?php _e('Naar', 'writgo-affiliate'); ?></th>
                                <th><?php _e('Type', 'writgo-affiliate'); ?></th>
                                <th><?php _e('Hits', 'writgo-affiliate'); ?></th>
                                <th><?php _e('Actie', 'writgo-affiliate'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($redirects as $index => $redirect) : ?>
                                <tr>
                                    <td><code><?php echo esc_html($redirect['from']); ?></code></td>
                                    <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis;"><?php echo esc_url($redirect['to']); ?></td>
                                    <td><span style="background: <?php echo $redirect['type'] === '301' ? '#dcfce7' : '#fef3c7'; ?>; padding: 2px 8px; border-radius: 4px; font-size: 12px;"><?php echo esc_html($redirect['type']); ?></span></td>
                                    <td><?php echo intval($redirect['hits'] ?? 0); ?></td>
                                    <td>
                                        <a href="<?php echo wp_nonce_url(admin_url('tools.php?page=writgo-redirects&delete_redirect=' . $index), 'delete_redirect'); ?>" onclick="return confirm('<?php _e('Weet je zeker dat je deze redirect wilt verwijderen?', 'writgo-affiliate'); ?>');" style="color: #dc2626;"><?php _e('Verwijderen', 'writgo-affiliate'); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

// Execute redirects
add_action('template_redirect', 'writgo_execute_redirects', 1);
function writgo_execute_redirects() {
    if (is_admin()) {
        return;
    }

    $redirects = get_option('writgo_redirects', array());
    if (empty($redirects)) {
        return;
    }

    $current_path = $_SERVER['REQUEST_URI'];
    $current_path = strtok($current_path, '?'); // Remove query string

    foreach ($redirects as $index => $redirect) {
        $from = rtrim($redirect['from'], '/');
        $current = rtrim($current_path, '/');

        if ($from === $current || $redirect['from'] === $current_path) {
            // Update hit counter
            $redirects[$index]['hits'] = ($redirect['hits'] ?? 0) + 1;
            update_option('writgo_redirects', $redirects);

            // Perform redirect
            $type = $redirect['type'] === '302' ? 302 : 301;
            wp_redirect($redirect['to'], $type);
            exit;
        }
    }
}

/**
 * =====================================================
 * REVIEW/PRODUCT SCHEMA
 * =====================================================
 */
add_action('wp_head', 'writgo_review_schema');
function writgo_review_schema() {
    if (!is_singular('post')) {
        return;
    }

    global $post;

    // Check if this post has a review score
    $score = get_post_meta($post->ID, '_writgo_score', true);
    if (!$score) {
        return;
    }

    // Get product info from sticky CTA if available
    $product_name = get_post_meta($post->ID, '_writgo_sticky_title', true);
    if (!$product_name) {
        $product_name = get_the_title();
    }

    $custom_description = get_post_meta($post->ID, '_writgo_seo_description', true);
    $description = $custom_description ? $custom_description : wp_trim_words(strip_tags($post->post_content), 30);

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Review',
        'itemReviewed' => array(
            '@type' => 'Product',
            'name' => $product_name,
        ),
        'reviewRating' => array(
            '@type' => 'Rating',
            'ratingValue' => floatval($score),
            'bestRating' => '10',
            'worstRating' => '0'
        ),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author()
        ),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => get_bloginfo('name')
        ),
        'datePublished' => get_the_date('c'),
        'reviewBody' => $description
    );

    // Add product image if featured image exists
    if (has_post_thumbnail()) {
        $schema['itemReviewed']['image'] = get_the_post_thumbnail_url($post->ID, 'large');
    }

    // Add price if available
    $price = get_post_meta($post->ID, '_writgo_sticky_price', true);
    if ($price) {
        $schema['itemReviewed']['offers'] = array(
            '@type' => 'Offer',
            'price' => floatval(str_replace(',', '.', $price)),
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock'
        );
    }

    // Add affiliate URL if available
    $url = get_post_meta($post->ID, '_writgo_sticky_url', true);
    if ($url) {
        $schema['itemReviewed']['url'] = $url;
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * =====================================================
 * CUSTOM OG IMAGE & SOCIAL PREVIEWS
 * =====================================================
 */

// Add OG image meta field support (handled in meta-boxes.php)
// This filter uses the custom OG image if set
add_filter('wp_head', 'writgo_custom_og_image_output', 2);
function writgo_custom_og_image_output() {
    if (!is_singular()) {
        return;
    }

    // Skip if Yoast or RankMath is active
    if (defined('WPSEO_VERSION') || class_exists('RankMath')) {
        return;
    }

    global $post;
    $og_image_id = get_post_meta($post->ID, '_writgo_og_image', true);

    if ($og_image_id) {
        $og_image_url = wp_get_attachment_image_url($og_image_id, 'large');
        if ($og_image_url) {
            // The main og:image is output in writgo_seo_meta_tags
            // We add additional image metadata here
            echo '<meta property="og:image:width" content="1200">' . "\n";
            echo '<meta property="og:image:height" content="630">' . "\n";
        }
    }
}
