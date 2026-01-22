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

/**
 * =====================================================
 * WEBSITE SCHEMA (Homepage with SearchAction)
 * =====================================================
 */
add_action('wp_head', 'writgo_website_schema');
function writgo_website_schema() {
    if (!is_front_page()) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => get_bloginfo('name'),
        'description' => get_bloginfo('description'),
        'url' => home_url('/'),
        'potentialAction' => array(
            '@type' => 'SearchAction',
            'target' => array(
                '@type' => 'EntryPoint',
                'urlTemplate' => home_url('/?s={search_term_string}')
            ),
            'query-input' => 'required name=search_term_string'
        )
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * =====================================================
 * HOWTO SCHEMA SHORTCODE
 * [writgo_howto title="Hoe doe je X"]
 * <step title="Stap 1">Instructie hier</step>
 * <step title="Stap 2">Instructie hier</step>
 * [/writgo_howto]
 * =====================================================
 */
add_shortcode('writgo_howto', 'writgo_howto_shortcode');
function writgo_howto_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'title' => '',
        'time' => '',
    ), $atts);

    if (!$content) {
        return '';
    }

    // Parse steps from content
    $steps = array();
    preg_match_all('/<step\s+title=["\']([^"\']+)["\']>(.*?)<\/step>/is', $content, $matches, PREG_SET_ORDER);

    if (empty($matches)) {
        return '';
    }

    $schema_steps = array();
    $html = '<div class="writgo-howto">';

    if ($atts['title']) {
        $html .= '<h3 class="writgo-howto-title">' . esc_html($atts['title']) . '</h3>';
    }

    $html .= '<ol class="writgo-howto-steps">';
    $step_num = 1;

    foreach ($matches as $match) {
        $step_title = esc_html($match[1]);
        $step_content = wp_kses_post(trim($match[2]));

        $schema_steps[] = array(
            '@type' => 'HowToStep',
            'position' => $step_num,
            'name' => $step_title,
            'text' => strip_tags($step_content)
        );

        $html .= '<li class="writgo-howto-step">';
        $html .= '<strong>' . $step_title . '</strong>';
        $html .= '<div class="writgo-step-content">' . $step_content . '</div>';
        $html .= '</li>';

        $step_num++;
    }

    $html .= '</ol></div>';

    // Add HowTo Schema
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'HowTo',
        'name' => $atts['title'] ?: get_the_title(),
        'step' => $schema_steps
    );

    if ($atts['time']) {
        $schema['totalTime'] = 'PT' . intval($atts['time']) . 'M';
    }

    $html .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

    // Add styles
    $html .= '<style>
        .writgo-howto { margin: 2rem 0; padding: 20px; background: #f8fafc; border-radius: 12px; }
        .writgo-howto-title { margin: 0 0 20px; font-size: 1.25rem; color: #1e293b; }
        .writgo-howto-steps { margin: 0; padding-left: 0; list-style: none; counter-reset: step; }
        .writgo-howto-step { position: relative; padding: 15px 15px 15px 60px; margin-bottom: 12px; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; }
        .writgo-howto-step::before { counter-increment: step; content: counter(step); position: absolute; left: 15px; top: 15px; width: 32px; height: 32px; background: #f97316; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; }
        .writgo-howto-step strong { display: block; margin-bottom: 8px; color: #1e293b; }
        .writgo-step-content { color: #475569; line-height: 1.6; }
    </style>';

    return $html;
}

/**
 * =====================================================
 * VIDEO SCHEMA (Auto-detect YouTube/Vimeo embeds)
 * =====================================================
 */
add_action('wp_head', 'writgo_video_schema');
function writgo_video_schema() {
    if (!is_singular('post')) {
        return;
    }

    global $post;

    // Check for YouTube embeds
    if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $post->post_content, $yt_match) ||
        preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $post->post_content, $yt_match) ||
        preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $post->post_content, $yt_match)) {

        $video_id = $yt_match[1];
        $custom_description = get_post_meta($post->ID, '_writgo_seo_description', true);

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => get_the_title(),
            'description' => $custom_description ?: wp_trim_words(strip_tags($post->post_content), 30),
            'thumbnailUrl' => 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg',
            'uploadDate' => get_the_date('c'),
            'contentUrl' => 'https://www.youtube.com/watch?v=' . $video_id,
            'embedUrl' => 'https://www.youtube.com/embed/' . $video_id
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}

/**
 * =====================================================
 * ITEMLIST SCHEMA (For list/comparison posts)
 * =====================================================
 */
add_action('wp_head', 'writgo_itemlist_schema');
function writgo_itemlist_schema() {
    if (!is_singular('post')) {
        return;
    }

    global $post;

    // Check if title contains list indicators
    $title = strtolower(get_the_title());
    $is_list = preg_match('/(top\s*\d+|beste|\d+\s*beste|vergelijk|review)/i', $title);

    if (!$is_list) {
        return;
    }

    // Extract H2/H3 headings as list items
    preg_match_all('/<h[23][^>]*>(.*?)<\/h[23]>/i', $post->post_content, $headings);

    if (empty($headings[1]) || count($headings[1]) < 3) {
        return;
    }

    $items = array();
    $position = 1;

    foreach ($headings[1] as $heading) {
        $heading_text = strip_tags($heading);
        // Skip common non-item headings
        if (preg_match('/(conclusie|veelgestelde|faq|koopgids|wat is|waarom|hoe)/i', $heading_text)) {
            continue;
        }

        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $heading_text
        );

        if ($position > 10) break; // Limit to 10 items
    }

    if (count($items) >= 3) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => get_the_title(),
            'numberOfItems' => count($items),
            'itemListElement' => $items
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}

/**
 * =====================================================
 * AUTOMATIC INTERNAL LINKING
 * =====================================================
 */

// Admin page for internal link keywords
add_action('admin_menu', 'writgo_internal_links_menu');
function writgo_internal_links_menu() {
    add_submenu_page(
        'tools.php',
        __('Interne Links', 'writgo-affiliate'),
        __('Interne Links', 'writgo-affiliate'),
        'manage_options',
        'writgo-internal-links',
        'writgo_internal_links_page'
    );
}

// Handle form submissions
add_action('admin_init', 'writgo_handle_internal_link_actions');
function writgo_handle_internal_link_actions() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Add link rule
    if (isset($_POST['writgo_add_link']) && wp_verify_nonce($_POST['writgo_link_nonce'], 'writgo_link_action')) {
        $links = get_option('writgo_internal_links', array());
        $keyword = sanitize_text_field($_POST['link_keyword']);
        $url = esc_url_raw($_POST['link_url']);
        $max = intval($_POST['link_max']) ?: 1;

        if ($keyword && $url) {
            $links[] = array(
                'keyword' => $keyword,
                'url' => $url,
                'max' => $max,
                'created' => current_time('mysql')
            );
            update_option('writgo_internal_links', $links);
            add_settings_error('writgo_links', 'link_added', __('Link regel toegevoegd.', 'writgo-affiliate'), 'success');
        }
    }

    // Delete link rule
    if (isset($_GET['delete_link']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_link')) {
        $links = get_option('writgo_internal_links', array());
        $index = intval($_GET['delete_link']);
        if (isset($links[$index])) {
            unset($links[$index]);
            $links = array_values($links);
            update_option('writgo_internal_links', $links);
        }
        wp_redirect(admin_url('tools.php?page=writgo-internal-links&deleted=1'));
        exit;
    }

    // Auto-generate from focus keywords
    if (isset($_POST['writgo_auto_generate']) && wp_verify_nonce($_POST['writgo_link_nonce'], 'writgo_link_action')) {
        $links = get_option('writgo_internal_links', array());
        $existing_keywords = array_column($links, 'keyword');

        $posts = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 100,
            'meta_query' => array(
                array(
                    'key' => '_writgo_focus_keyword',
                    'compare' => '!=',
                    'value' => ''
                )
            )
        ));

        $added = 0;
        foreach ($posts as $post) {
            $keyword = get_post_meta($post->ID, '_writgo_focus_keyword', true);
            if ($keyword && !in_array(strtolower($keyword), array_map('strtolower', $existing_keywords))) {
                $links[] = array(
                    'keyword' => $keyword,
                    'url' => get_permalink($post->ID),
                    'max' => 1,
                    'created' => current_time('mysql')
                );
                $existing_keywords[] = $keyword;
                $added++;
            }
        }

        if ($added > 0) {
            update_option('writgo_internal_links', $links);
            add_settings_error('writgo_links', 'links_generated', sprintf(__('%d link regels automatisch gegenereerd.', 'writgo-affiliate'), $added), 'success');
        } else {
            add_settings_error('writgo_links', 'no_links', __('Geen nieuwe keywords gevonden.', 'writgo-affiliate'), 'info');
        }
    }
}

// Admin page
function writgo_internal_links_page() {
    $links = get_option('writgo_internal_links', array());
    ?>
    <div class="wrap">
        <h1><?php _e('Automatische Interne Links', 'writgo-affiliate'); ?></h1>
        <p><?php _e('Definieer keywords die automatisch gelinkt worden naar specifieke pagina\'s in je content.', 'writgo-affiliate'); ?></p>

        <?php settings_errors('writgo_links'); ?>

        <?php if (isset($_GET['deleted'])) : ?>
            <div class="notice notice-success is-dismissible"><p><?php _e('Link regel verwijderd.', 'writgo-affiliate'); ?></p></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-top: 20px;">
            <!-- Add New -->
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;"><?php _e('Nieuwe Link Regel', 'writgo-affiliate'); ?></h2>
                <form method="post">
                    <?php wp_nonce_field('writgo_link_action', 'writgo_link_nonce'); ?>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Keyword', 'writgo-affiliate'); ?></label>
                        <input type="text" name="link_keyword" placeholder="bijv. beste laptop" style="width: 100%; padding: 8px;" required />
                    </p>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Link naar URL', 'writgo-affiliate'); ?></label>
                        <input type="url" name="link_url" placeholder="https://..." style="width: 100%; padding: 8px;" required />
                    </p>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Max per artikel', 'writgo-affiliate'); ?></label>
                        <input type="number" name="link_max" value="1" min="1" max="5" style="width: 80px; padding: 8px;" />
                        <span style="font-size: 12px; color: #666;"><?php _e('Hoeveel keer mag dit keyword gelinkt worden per artikel?', 'writgo-affiliate'); ?></span>
                    </p>

                    <p style="display: flex; gap: 10px;">
                        <button type="submit" name="writgo_add_link" class="button button-primary"><?php _e('Toevoegen', 'writgo-affiliate'); ?></button>
                        <button type="submit" name="writgo_auto_generate" class="button"><?php _e('Auto-genereer van Focus Keywords', 'writgo-affiliate'); ?></button>
                    </p>
                </form>
            </div>

            <!-- List -->
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;"><?php _e('Actieve Link Regels', 'writgo-affiliate'); ?> <span style="font-weight: normal; color: #666;">(<?php echo count($links); ?>)</span></h2>

                <?php if (empty($links)) : ?>
                    <p style="color: #666;"><?php _e('Nog geen link regels. Voeg handmatig toe of genereer automatisch vanuit je focus keywords.', 'writgo-affiliate'); ?></p>
                <?php else : ?>
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th><?php _e('Keyword', 'writgo-affiliate'); ?></th>
                                <th><?php _e('URL', 'writgo-affiliate'); ?></th>
                                <th><?php _e('Max', 'writgo-affiliate'); ?></th>
                                <th><?php _e('Actie', 'writgo-affiliate'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($links as $index => $link) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html($link['keyword']); ?></strong></td>
                                    <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;"><a href="<?php echo esc_url($link['url']); ?>" target="_blank"><?php echo esc_url($link['url']); ?></a></td>
                                    <td><?php echo intval($link['max']); ?>x</td>
                                    <td>
                                        <a href="<?php echo wp_nonce_url(admin_url('tools.php?page=writgo-internal-links&delete_link=' . $index), 'delete_link'); ?>" onclick="return confirm('Verwijderen?');" style="color: #dc2626;"><?php _e('Verwijderen', 'writgo-affiliate'); ?></a>
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

// Apply internal links to content
add_filter('the_content', 'writgo_auto_internal_links', 99);
function writgo_auto_internal_links($content) {
    // Only on single posts
    if (!is_singular('post') || is_admin()) {
        return $content;
    }

    // Check if enabled
    if (!get_theme_mod('writgo_auto_links_enabled', true)) {
        return $content;
    }

    $links = get_option('writgo_internal_links', array());
    if (empty($links)) {
        return $content;
    }

    global $post;
    $current_url = get_permalink($post->ID);

    // Sort by keyword length (longest first to avoid partial matches)
    usort($links, function($a, $b) {
        return strlen($b['keyword']) - strlen($a['keyword']);
    });

    foreach ($links as $link) {
        // Skip if linking to current page
        if (trailingslashit($link['url']) === trailingslashit($current_url)) {
            continue;
        }

        $keyword = preg_quote($link['keyword'], '/');
        $max = intval($link['max']) ?: 1;
        $count = 0;

        // Replace keyword with link (only in text, not in existing links/tags)
        $content = preg_replace_callback(
            '/(?<!["\'>])(\b' . $keyword . '\b)(?![^<]*<\/a>)(?![^<]*>)/i',
            function($matches) use ($link, &$count, $max) {
                if ($count >= $max) {
                    return $matches[0];
                }
                $count++;
                return '<a href="' . esc_url($link['url']) . '" class="writgo-auto-link">' . $matches[0] . '</a>';
            },
            $content
        );
    }

    return $content;
}

// Add toggle in Customizer
add_action('customize_register', 'writgo_internal_links_customizer');
function writgo_internal_links_customizer($wp_customize) {
    $wp_customize->add_setting('writgo_auto_links_enabled', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_auto_links_enabled', array(
        'label' => __('Automatische interne links inschakelen', 'writgo-affiliate'),
        'section' => 'writgo_seo_settings',
        'type' => 'checkbox',
    ));
}

/**
 * =====================================================
 * AFFILIATE LINK MANAGER
 * Link cloaking: /go/product-naam/ -> affiliate URL
 * Click tracking, auto nofollow/sponsored
 * =====================================================
 */

// Register /go/ rewrite rules
add_action('init', 'writgo_register_affiliate_routes');
function writgo_register_affiliate_routes() {
    add_rewrite_rule('^go/([^/]+)/?$', 'index.php?writgo_affiliate_link=$matches[1]', 'top');
}

add_filter('query_vars', 'writgo_affiliate_query_vars');
function writgo_affiliate_query_vars($vars) {
    $vars[] = 'writgo_affiliate_link';
    return $vars;
}

// Handle affiliate redirects
add_action('template_redirect', 'writgo_handle_affiliate_redirect');
function writgo_handle_affiliate_redirect() {
    $slug = get_query_var('writgo_affiliate_link');
    if (!$slug) {
        return;
    }

    $links = get_option('writgo_affiliate_links', array());

    foreach ($links as $index => $link) {
        if ($link['slug'] === $slug) {
            // Update click count
            $links[$index]['clicks'] = ($link['clicks'] ?? 0) + 1;
            $links[$index]['last_click'] = current_time('mysql');
            update_option('writgo_affiliate_links', $links);

            // Redirect to affiliate URL
            wp_redirect($link['url'], 302);
            exit;
        }
    }

    // Link not found - redirect to homepage
    wp_redirect(home_url('/'), 302);
    exit;
}

// Admin menu
add_action('admin_menu', 'writgo_affiliate_links_menu');
function writgo_affiliate_links_menu() {
    add_submenu_page(
        'tools.php',
        __('Affiliate Links', 'writgo-affiliate'),
        __('Affiliate Links', 'writgo-affiliate'),
        'manage_options',
        'writgo-affiliate-links',
        'writgo_affiliate_links_page'
    );
}

// Handle form submissions
add_action('admin_init', 'writgo_handle_affiliate_link_actions');
function writgo_handle_affiliate_link_actions() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Add affiliate link
    if (isset($_POST['writgo_add_affiliate']) && wp_verify_nonce($_POST['writgo_aff_nonce'], 'writgo_aff_action')) {
        $links = get_option('writgo_affiliate_links', array());
        $name = sanitize_text_field($_POST['aff_name']);
        $url = esc_url_raw($_POST['aff_url']);
        $slug = sanitize_title($_POST['aff_slug'] ?: $name);
        $network = sanitize_text_field($_POST['aff_network']);

        // Check for duplicate slug
        foreach ($links as $link) {
            if ($link['slug'] === $slug) {
                $slug = $slug . '-' . rand(100, 999);
                break;
            }
        }

        $keywords = sanitize_text_field($_POST['aff_keywords']);
        $max = intval($_POST['aff_max']) ?: 1;

        if ($name && $url) {
            $links[] = array(
                'name' => $name,
                'url' => $url,
                'slug' => $slug,
                'network' => $network,
                'keywords' => $keywords,
                'max' => $max,
                'clicks' => 0,
                'created' => current_time('mysql')
            );
            update_option('writgo_affiliate_links', $links);

            // Flush rewrite rules
            flush_rewrite_rules();

            add_settings_error('writgo_aff', 'aff_added', __('Affiliate link toegevoegd.', 'writgo-affiliate'), 'success');
        }
    }

    // Delete affiliate link
    if (isset($_GET['delete_aff']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_aff')) {
        $links = get_option('writgo_affiliate_links', array());
        $index = intval($_GET['delete_aff']);
        if (isset($links[$index])) {
            unset($links[$index]);
            $links = array_values($links);
            update_option('writgo_affiliate_links', $links);
        }
        wp_redirect(admin_url('tools.php?page=writgo-affiliate-links&deleted=1'));
        exit;
    }
}

// Admin page
function writgo_affiliate_links_page() {
    $links = get_option('writgo_affiliate_links', array());
    $total_clicks = array_sum(array_column($links, 'clicks'));
    ?>
    <div class="wrap">
        <h1><?php _e('Affiliate Link Manager', 'writgo-affiliate'); ?></h1>
        <p><?php _e('Beheer je affiliate links met cloaking (/go/naam/) en click tracking.', 'writgo-affiliate'); ?></p>

        <?php settings_errors('writgo_aff'); ?>

        <?php if (isset($_GET['deleted'])) : ?>
            <div class="notice notice-success is-dismissible"><p><?php _e('Affiliate link verwijderd.', 'writgo-affiliate'); ?></p></div>
        <?php endif; ?>

        <!-- Stats -->
        <div style="display: flex; gap: 20px; margin: 20px 0;">
            <div style="background: linear-gradient(135deg, #f97316, #ea580c); color: #fff; padding: 20px 30px; border-radius: 12px;">
                <div style="font-size: 32px; font-weight: 700;"><?php echo count($links); ?></div>
                <div style="opacity: 0.9;">Affiliate Links</div>
            </div>
            <div style="background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; padding: 20px 30px; border-radius: 12px;">
                <div style="font-size: 32px; font-weight: 700;"><?php echo number_format($total_clicks); ?></div>
                <div style="opacity: 0.9;">Totaal Clicks</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
            <!-- Add New -->
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;"><?php _e('Nieuwe Affiliate Link', 'writgo-affiliate'); ?></h2>
                <form method="post">
                    <?php wp_nonce_field('writgo_aff_action', 'writgo_aff_nonce'); ?>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Naam', 'writgo-affiliate'); ?></label>
                        <input type="text" name="aff_name" placeholder="bijv. Bol.com Laptop X" style="width: 100%; padding: 8px;" required />
                    </p>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Affiliate URL', 'writgo-affiliate'); ?></label>
                        <input type="url" name="aff_url" placeholder="https://partner.bol.com/..." style="width: 100%; padding: 8px;" required />
                    </p>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Slug (optioneel)', 'writgo-affiliate'); ?></label>
                        <input type="text" name="aff_slug" placeholder="laptop-x" style="width: 100%; padding: 8px;" />
                        <span style="font-size: 12px; color: #666;"><?php echo home_url('/go/'); ?><strong>slug</strong>/</span>
                    </p>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Netwerk', 'writgo-affiliate'); ?></label>
                        <select name="aff_network" style="width: 100%; padding: 8px;">
                            <option value="">-- Selecteer --</option>
                            <option value="bol">Bol.com</option>
                            <option value="amazon">Amazon</option>
                            <option value="coolblue">Coolblue</option>
                            <option value="tradetracker">TradeTracker</option>
                            <option value="daisycon">Daisycon</option>
                            <option value="awin">Awin</option>
                            <option value="other">Anders</option>
                        </select>
                    </p>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Auto-link keywords (optioneel)', 'writgo-affiliate'); ?></label>
                        <input type="text" name="aff_keywords" placeholder="laptop, beste laptop, laptop kopen" style="width: 100%; padding: 8px;" />
                        <span style="font-size: 12px; color: #666;"><?php _e('Komma-gescheiden. Deze keywords worden automatisch gelinkt in je content.', 'writgo-affiliate'); ?></span>
                    </p>

                    <p>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e('Max auto-links per artikel', 'writgo-affiliate'); ?></label>
                        <input type="number" name="aff_max" value="1" min="1" max="5" style="width: 80px; padding: 8px;" />
                    </p>

                    <p>
                        <button type="submit" name="writgo_add_affiliate" class="button button-primary"><?php _e('Toevoegen', 'writgo-affiliate'); ?></button>
                    </p>
                </form>
            </div>

            <!-- List -->
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;"><?php _e('Actieve Affiliate Links', 'writgo-affiliate'); ?></h2>

                <?php if (empty($links)) : ?>
                    <p style="color: #666;"><?php _e('Nog geen affiliate links. Voeg je eerste link toe!', 'writgo-affiliate'); ?></p>
                <?php else : ?>
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th><?php _e('Naam', 'writgo-affiliate'); ?></th>
                                <th><?php _e('Cloaked URL', 'writgo-affiliate'); ?></th>
                                <th><?php _e('Auto-link Keywords', 'writgo-affiliate'); ?></th>
                                <th><?php _e('Clicks', 'writgo-affiliate'); ?></th>
                                <th><?php _e('Actie', 'writgo-affiliate'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($links as $index => $link) : ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($link['name']); ?></strong>
                                        <?php if (!empty($link['network'])) : ?>
                                            <br><span style="font-size: 11px; color: #666;"><?php echo esc_html(ucfirst($link['network'])); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 11px;">/go/<?php echo esc_html($link['slug']); ?>/</code>
                                        <button type="button" onclick="navigator.clipboard.writeText('<?php echo esc_url(home_url('/go/' . $link['slug'] . '/')); ?>')" style="border: none; background: none; cursor: pointer; padding: 2px;" title="Kopieer">📋</button>
                                    </td>
                                    <td>
                                        <?php if (!empty($link['keywords'])) : ?>
                                            <span style="font-size: 12px; color: #16a34a;">✓ <?php echo esc_html($link['keywords']); ?></span>
                                            <br><span style="font-size: 11px; color: #666;">Max <?php echo intval($link['max'] ?? 1); ?>x per artikel</span>
                                        <?php else : ?>
                                            <span style="color: #999;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong style="color: #16a34a;"><?php echo number_format($link['clicks'] ?? 0); ?></strong></td>
                                    <td>
                                        <a href="<?php echo esc_url($link['url']); ?>" target="_blank" style="margin-right: 8px;">🔗</a>
                                        <a href="<?php echo wp_nonce_url(admin_url('tools.php?page=writgo-affiliate-links&delete_aff=' . $index), 'delete_aff'); ?>" onclick="return confirm('Verwijderen?');" style="color: #dc2626;"><?php _e('X', 'writgo-affiliate'); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Shortcode Help -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 30px;">
            <h3 style="margin-top: 0;"><?php _e('Shortcode Gebruik', 'writgo-affiliate'); ?></h3>
            <p><?php _e('Gebruik deze shortcode om affiliate links in je content te plaatsen:', 'writgo-affiliate'); ?></p>
            <code style="display: block; background: #f1f5f9; padding: 15px; border-radius: 6px; margin: 10px 0;">[affiliate slug="laptop-x"]Bekijk op Bol.com[/affiliate]</code>
            <code style="display: block; background: #f1f5f9; padding: 15px; border-radius: 6px; margin: 10px 0;">[affiliate slug="laptop-x" button="true"]Bekijk aanbieding[/affiliate]</code>
        </div>
    </div>
    <?php
}

// Affiliate shortcode
add_shortcode('affiliate', 'writgo_affiliate_shortcode');
function writgo_affiliate_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'slug' => '',
        'button' => false,
        'class' => '',
    ), $atts);

    if (!$atts['slug']) {
        return $content;
    }

    $url = home_url('/go/' . sanitize_title($atts['slug']) . '/');
    $text = $content ?: __('Bekijk product', 'writgo-affiliate');

    if ($atts['button']) {
        return '<a href="' . esc_url($url) . '" class="writgo-aff-button ' . esc_attr($atts['class']) . '" rel="nofollow sponsored" target="_blank">' . esc_html($text) . '</a>';
    }

    return '<a href="' . esc_url($url) . '" class="writgo-aff-link ' . esc_attr($atts['class']) . '" rel="nofollow sponsored" target="_blank">' . esc_html($text) . '</a>';
}

// Add button styles
add_action('wp_head', 'writgo_affiliate_styles');
function writgo_affiliate_styles() {
    ?>
    <style>
        .writgo-aff-button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: #fff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .writgo-aff-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
            color: #fff !important;
        }
        .writgo-aff-link {
            color: #f97316;
            text-decoration: underline;
        }
    </style>
    <?php
}

/**
 * Auto-link affiliate keywords in content
 */
add_filter('the_content', 'writgo_auto_affiliate_links', 98);
function writgo_auto_affiliate_links($content) {
    if (!is_singular('post') || is_admin()) {
        return $content;
    }

    if (!get_theme_mod('writgo_auto_affiliate_enabled', true)) {
        return $content;
    }

    $links = get_option('writgo_affiliate_links', array());
    if (empty($links)) {
        return $content;
    }

    // Collect all keywords with their links
    $keyword_links = array();
    foreach ($links as $link) {
        if (empty($link['keywords'])) {
            continue;
        }

        $keywords = array_map('trim', explode(',', $link['keywords']));
        $url = home_url('/go/' . $link['slug'] . '/');
        $max = intval($link['max'] ?? 1);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword_links[] = array(
                    'keyword' => $keyword,
                    'url' => $url,
                    'max' => $max,
                    'name' => $link['name']
                );
            }
        }
    }

    if (empty($keyword_links)) {
        return $content;
    }

    // Sort by keyword length (longest first)
    usort($keyword_links, function($a, $b) {
        return strlen($b['keyword']) - strlen($a['keyword']);
    });

    foreach ($keyword_links as $kw_link) {
        $keyword = preg_quote($kw_link['keyword'], '/');
        $max = $kw_link['max'];
        $count = 0;

        $content = preg_replace_callback(
            '/(?<!["\'>])(\b' . $keyword . '\b)(?![^<]*<\/a>)(?![^<]*>)/i',
            function($matches) use ($kw_link, &$count, $max) {
                if ($count >= $max) {
                    return $matches[0];
                }
                $count++;
                return '<a href="' . esc_url($kw_link['url']) . '" class="writgo-auto-aff-link" rel="nofollow sponsored" target="_blank" title="' . esc_attr($kw_link['name']) . '">' . $matches[0] . '</a>';
            },
            $content
        );
    }

    return $content;
}

/**
 * Auto add nofollow/sponsored to external links
 */
add_filter('the_content', 'writgo_external_links_nofollow', 100);
function writgo_external_links_nofollow($content) {
    if (!get_theme_mod('writgo_external_nofollow', true)) {
        return $content;
    }

    $site_url = parse_url(home_url(), PHP_URL_HOST);

    // Find all links
    $content = preg_replace_callback(
        '/<a\s+([^>]*href=["\']([^"\']+)["\'][^>]*)>/i',
        function($matches) use ($site_url) {
            $full_tag = $matches[0];
            $url = $matches[2];
            $url_host = parse_url($url, PHP_URL_HOST);

            // Skip internal links
            if (!$url_host || strpos($url_host, $site_url) !== false) {
                return $full_tag;
            }

            // Skip if already has rel attribute with nofollow
            if (preg_match('/rel=["\'][^"\']*nofollow[^"\']*["\']/i', $full_tag)) {
                return $full_tag;
            }

            // Add or modify rel attribute
            if (preg_match('/rel=["\']([^"\']*)["\']/', $full_tag)) {
                // Add to existing rel
                $full_tag = preg_replace(
                    '/rel=["\']([^"\']*)["\']/',
                    'rel="$1 nofollow sponsored"',
                    $full_tag
                );
            } else {
                // Add new rel attribute
                $full_tag = str_replace('<a ', '<a rel="nofollow sponsored" ', $full_tag);
            }

            // Add target="_blank" if not present
            if (strpos($full_tag, 'target=') === false) {
                $full_tag = str_replace('<a ', '<a target="_blank" ', $full_tag);
            }

            return $full_tag;
        },
        $content
    );

    return $content;
}

/**
 * Auto add affiliate disclaimer
 */
add_filter('the_content', 'writgo_affiliate_disclaimer', 5);
function writgo_affiliate_disclaimer($content) {
    if (!is_singular('post') || is_admin()) {
        return $content;
    }

    if (!get_theme_mod('writgo_affiliate_disclaimer', true)) {
        return $content;
    }

    $disclaimer_text = get_theme_mod('writgo_disclaimer_text', 'Dit artikel bevat affiliate links. Als je via deze links een product koopt, ontvangen wij een kleine commissie. Dit kost jou niets extra.');

    $disclaimer = '<div class="writgo-affiliate-disclaimer" style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px 16px; margin-bottom: 24px; border-radius: 0 8px 8px 0; font-size: 14px; color: #92400e;">
        <strong>📢 Affiliate Disclosure:</strong> ' . esc_html($disclaimer_text) . '
    </div>';

    return $disclaimer . $content;
}

// Add affiliate settings to Customizer
add_action('customize_register', 'writgo_affiliate_customizer');
function writgo_affiliate_customizer($wp_customize) {
    // Affiliate Section
    $wp_customize->add_section('writgo_affiliate_settings', array(
        'title' => __('🔗 Affiliate Instellingen', 'writgo-affiliate'),
        'priority' => 36,
    ));

    // Enable auto affiliate links
    $wp_customize->add_setting('writgo_auto_affiliate_enabled', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_auto_affiliate_enabled', array(
        'label' => __('Automatische affiliate links in content', 'writgo-affiliate'),
        'description' => __('Keywords worden automatisch gelinkt naar affiliate URLs', 'writgo-affiliate'),
        'section' => 'writgo_affiliate_settings',
        'type' => 'checkbox',
    ));

    // Enable external nofollow
    $wp_customize->add_setting('writgo_external_nofollow', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_external_nofollow', array(
        'label' => __('Automatisch nofollow op externe links', 'writgo-affiliate'),
        'section' => 'writgo_affiliate_settings',
        'type' => 'checkbox',
    ));

    // Enable disclaimer
    $wp_customize->add_setting('writgo_affiliate_disclaimer', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_affiliate_disclaimer', array(
        'label' => __('Toon affiliate disclaimer', 'writgo-affiliate'),
        'section' => 'writgo_affiliate_settings',
        'type' => 'checkbox',
    ));

    // Disclaimer text
    $wp_customize->add_setting('writgo_disclaimer_text', array(
        'default' => 'Dit artikel bevat affiliate links. Als je via deze links een product koopt, ontvangen wij een kleine commissie. Dit kost jou niets extra.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('writgo_disclaimer_text', array(
        'label' => __('Disclaimer tekst', 'writgo-affiliate'),
        'section' => 'writgo_affiliate_settings',
        'type' => 'textarea',
    ));
}

// Flush rewrite rules on theme activation for affiliate links
add_action('after_switch_theme', 'writgo_flush_affiliate_rules');
function writgo_flush_affiliate_rules() {
    writgo_register_affiliate_routes();
    flush_rewrite_rules();
}

/**
 * =====================================================
 * COMPARISON TABLE SHORTCODE
 * [writgo_compare]
 * <product name="Product 1" price="€99" rating="8.5" url="https://..." image="url">
 *   <pro>Voordeel 1</pro>
 *   <con>Nadeel 1</con>
 * </product>
 * [/writgo_compare]
 * =====================================================
 */
add_shortcode('writgo_compare', 'writgo_compare_shortcode');
function writgo_compare_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'title' => '',
        'columns' => 'auto',
    ), $atts);

    if (!$content) {
        return '';
    }

    // Parse products from content
    preg_match_all('/<product\s+([^>]+)>(.*?)<\/product>/is', $content, $products, PREG_SET_ORDER);

    if (empty($products)) {
        return '';
    }

    $html = '<div class="writgo-compare-table">';

    if ($atts['title']) {
        $html .= '<h3 class="writgo-compare-title">' . esc_html($atts['title']) . '</h3>';
    }

    $html .= '<div class="writgo-compare-grid" style="--cols: ' . (count($products) > 4 ? 4 : count($products)) . ';">';

    foreach ($products as $index => $product) {
        // Parse attributes
        $attrs = array();
        preg_match_all('/(\w+)=["\']([^"\']+)["\']/', $product[1], $attr_matches, PREG_SET_ORDER);
        foreach ($attr_matches as $am) {
            $attrs[strtolower($am[1])] = $am[2];
        }

        $name = isset($attrs['name']) ? esc_html($attrs['name']) : 'Product ' . ($index + 1);
        $price = isset($attrs['price']) ? esc_html($attrs['price']) : '';
        $rating = isset($attrs['rating']) ? floatval($attrs['rating']) : 0;
        $url = isset($attrs['url']) ? esc_url($attrs['url']) : '';
        $image = isset($attrs['image']) ? esc_url($attrs['image']) : '';
        $badge = isset($attrs['badge']) ? esc_html($attrs['badge']) : '';

        // Parse pros/cons
        $pros = array();
        $cons = array();
        preg_match_all('/<pro>(.*?)<\/pro>/is', $product[2], $pro_matches);
        preg_match_all('/<con>(.*?)<\/con>/is', $product[2], $con_matches);
        if (!empty($pro_matches[1])) $pros = $pro_matches[1];
        if (!empty($con_matches[1])) $cons = $con_matches[1];

        $html .= '<div class="writgo-compare-item' . ($index === 0 ? ' featured' : '') . '">';

        if ($badge) {
            $html .= '<div class="writgo-compare-badge">' . $badge . '</div>';
        } elseif ($index === 0) {
            $html .= '<div class="writgo-compare-badge">' . __('Beste Keuze', 'writgo-affiliate') . '</div>';
        }

        if ($image) {
            $html .= '<div class="writgo-compare-image"><img src="' . $image . '" alt="' . $name . '" loading="lazy"></div>';
        }

        $html .= '<h4 class="writgo-compare-name">' . $name . '</h4>';

        if ($rating) {
            $html .= '<div class="writgo-compare-rating">';
            $html .= '<span class="rating-score">' . number_format($rating, 1) . '</span>';
            $html .= '<span class="rating-stars">';
            $full_stars = floor($rating / 2);
            for ($i = 0; $i < 5; $i++) {
                $html .= $i < $full_stars ? '★' : '☆';
            }
            $html .= '</span></div>';
        }

        if ($price) {
            $html .= '<div class="writgo-compare-price">' . $price . '</div>';
        }

        if (!empty($pros) || !empty($cons)) {
            $html .= '<div class="writgo-compare-proscons">';
            if (!empty($pros)) {
                $html .= '<div class="compare-pros">';
                foreach (array_slice($pros, 0, 3) as $pro) {
                    $html .= '<div class="pro-item">✓ ' . wp_kses_post(trim($pro)) . '</div>';
                }
                $html .= '</div>';
            }
            if (!empty($cons)) {
                $html .= '<div class="compare-cons">';
                foreach (array_slice($cons, 0, 2) as $con) {
                    $html .= '<div class="con-item">✗ ' . wp_kses_post(trim($con)) . '</div>';
                }
                $html .= '</div>';
            }
            $html .= '</div>';
        }

        if ($url) {
            $html .= '<a href="' . $url . '" class="writgo-compare-button" rel="nofollow sponsored" target="_blank">' . __('Bekijk aanbieding', 'writgo-affiliate') . '</a>';
        }

        $html .= '</div>';
    }

    $html .= '</div></div>';

    // Add styles
    $html .= '<style>
        .writgo-compare-table { margin: 2rem 0; }
        .writgo-compare-title { text-align: center; margin-bottom: 1.5rem; font-size: 1.5rem; color: #1e293b; }
        .writgo-compare-grid { display: grid; grid-template-columns: repeat(var(--cols, 3), 1fr); gap: 20px; }
        @media (max-width: 768px) { .writgo-compare-grid { grid-template-columns: 1fr; } }
        .writgo-compare-item { background: #fff; border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; text-align: center; position: relative; transition: all 0.3s; }
        .writgo-compare-item:hover { border-color: #f97316; box-shadow: 0 8px 30px rgba(249,115,22,0.15); }
        .writgo-compare-item.featured { border-color: #f97316; }
        .writgo-compare-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #f97316, #ea580c); color: #fff; padding: 4px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; white-space: nowrap; }
        .writgo-compare-image { margin: 10px 0 15px; }
        .writgo-compare-image img { max-width: 150px; height: auto; border-radius: 8px; }
        .writgo-compare-name { margin: 0 0 10px; font-size: 1.1rem; color: #1e293b; }
        .writgo-compare-rating { margin: 10px 0; }
        .writgo-compare-rating .rating-score { background: #f97316; color: #fff; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 14px; margin-right: 8px; }
        .writgo-compare-rating .rating-stars { color: #f59e0b; font-size: 14px; }
        .writgo-compare-price { font-size: 1.5rem; font-weight: 700; color: #16a34a; margin: 15px 0; }
        .writgo-compare-proscons { text-align: left; margin: 15px 0; font-size: 13px; }
        .writgo-compare-proscons .pro-item { color: #16a34a; margin: 4px 0; }
        .writgo-compare-proscons .con-item { color: #dc2626; margin: 4px 0; }
        .writgo-compare-button { display: block; padding: 12px 20px; background: linear-gradient(135deg, #f97316, #ea580c); color: #fff !important; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 15px; transition: all 0.2s; }
        .writgo-compare-button:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(249,115,22,0.4); }
    </style>';

    return $html;
}

/**
 * =====================================================
 * PROS/CONS BOX SHORTCODE
 * [writgo_proscons title="Product Review"]
 * <pro>Voordeel 1</pro>
 * <pro>Voordeel 2</pro>
 * <con>Nadeel 1</con>
 * [/writgo_proscons]
 * =====================================================
 */
add_shortcode('writgo_proscons', 'writgo_proscons_shortcode');
function writgo_proscons_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'title' => '',
        'pros_title' => __('Voordelen', 'writgo-affiliate'),
        'cons_title' => __('Nadelen', 'writgo-affiliate'),
    ), $atts);

    if (!$content) {
        return '';
    }

    // Parse pros and cons
    $pros = array();
    $cons = array();
    preg_match_all('/<pro>(.*?)<\/pro>/is', $content, $pro_matches);
    preg_match_all('/<con>(.*?)<\/con>/is', $content, $con_matches);
    if (!empty($pro_matches[1])) $pros = $pro_matches[1];
    if (!empty($con_matches[1])) $cons = $con_matches[1];

    if (empty($pros) && empty($cons)) {
        return '';
    }

    $html = '<div class="writgo-proscons-box">';

    if ($atts['title']) {
        $html .= '<div class="writgo-proscons-header">' . esc_html($atts['title']) . '</div>';
    }

    $html .= '<div class="writgo-proscons-content">';

    // Pros column
    $html .= '<div class="writgo-pros-column">';
    $html .= '<div class="column-header pros-header"><span class="icon">👍</span> ' . esc_html($atts['pros_title']) . '</div>';
    $html .= '<ul class="pros-list">';
    foreach ($pros as $pro) {
        $html .= '<li><span class="check">✓</span> ' . wp_kses_post(trim($pro)) . '</li>';
    }
    $html .= '</ul></div>';

    // Cons column
    $html .= '<div class="writgo-cons-column">';
    $html .= '<div class="column-header cons-header"><span class="icon">👎</span> ' . esc_html($atts['cons_title']) . '</div>';
    $html .= '<ul class="cons-list">';
    foreach ($cons as $con) {
        $html .= '<li><span class="cross">✗</span> ' . wp_kses_post(trim($con)) . '</li>';
    }
    $html .= '</ul></div>';

    $html .= '</div></div>';

    // Add styles
    $html .= '<style>
        .writgo-proscons-box { margin: 2rem 0; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; background: #fff; }
        .writgo-proscons-header { background: #1e293b; color: #fff; padding: 15px 20px; font-weight: 600; font-size: 1.1rem; }
        .writgo-proscons-content { display: grid; grid-template-columns: 1fr 1fr; }
        @media (max-width: 600px) { .writgo-proscons-content { grid-template-columns: 1fr; } }
        .writgo-pros-column, .writgo-cons-column { padding: 0; }
        .column-header { padding: 12px 20px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .column-header .icon { font-size: 1.2rem; }
        .pros-header { background: #dcfce7; color: #166534; }
        .cons-header { background: #fee2e2; color: #991b1b; }
        .pros-list, .cons-list { list-style: none; margin: 0; padding: 15px 20px; }
        .pros-list li, .cons-list li { padding: 8px 0; display: flex; align-items: flex-start; gap: 10px; border-bottom: 1px solid #f1f5f9; }
        .pros-list li:last-child, .cons-list li:last-child { border-bottom: none; }
        .pros-list .check { color: #16a34a; font-weight: 700; flex-shrink: 0; }
        .cons-list .cross { color: #dc2626; font-weight: 700; flex-shrink: 0; }
    </style>';

    return $html;
}

/**
 * =====================================================
 * PRODUCT BOX SHORTCODE
 * [writgo_product name="Product Name" price="€99" rating="8.5"
 *    image="url" url="affiliate-url" badge="Aanrader"]
 * Product beschrijving hier...
 * [/writgo_product]
 * =====================================================
 */
add_shortcode('writgo_product', 'writgo_product_shortcode');
function writgo_product_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'name' => '',
        'price' => '',
        'old_price' => '',
        'rating' => '',
        'image' => '',
        'url' => '',
        'button' => __('Bekijk aanbieding', 'writgo-affiliate'),
        'badge' => '',
        'store' => '',
    ), $atts);

    $html = '<div class="writgo-product-box">';

    // Badge
    if ($atts['badge']) {
        $html .= '<div class="product-badge">' . esc_html($atts['badge']) . '</div>';
    }

    $html .= '<div class="product-inner">';

    // Image
    if ($atts['image']) {
        $html .= '<div class="product-image">';
        $html .= '<img src="' . esc_url($atts['image']) . '" alt="' . esc_attr($atts['name']) . '" loading="lazy">';
        $html .= '</div>';
    }

    // Content
    $html .= '<div class="product-content">';

    // Name
    if ($atts['name']) {
        $html .= '<h4 class="product-name">' . esc_html($atts['name']) . '</h4>';
    }

    // Rating
    if ($atts['rating']) {
        $rating = floatval($atts['rating']);
        $html .= '<div class="product-rating">';
        $html .= '<span class="rating-value">' . number_format($rating, 1) . '/10</span>';
        $html .= '<span class="rating-bar"><span class="rating-fill" style="width: ' . ($rating * 10) . '%;"></span></span>';
        $html .= '</div>';
    }

    // Description
    if ($content) {
        $html .= '<div class="product-description">' . wp_kses_post(trim($content)) . '</div>';
    }

    $html .= '</div>'; // end product-content

    // Sidebar (price & button)
    $html .= '<div class="product-sidebar">';

    if ($atts['price']) {
        $html .= '<div class="product-price">';
        if ($atts['old_price']) {
            $html .= '<span class="old-price">' . esc_html($atts['old_price']) . '</span>';
        }
        $html .= '<span class="current-price">' . esc_html($atts['price']) . '</span>';
        $html .= '</div>';
    }

    if ($atts['store']) {
        $html .= '<div class="product-store">' . esc_html($atts['store']) . '</div>';
    }

    if ($atts['url']) {
        $html .= '<a href="' . esc_url($atts['url']) . '" class="product-button" rel="nofollow sponsored" target="_blank">' . esc_html($atts['button']) . ' →</a>';
    }

    $html .= '</div>'; // end product-sidebar
    $html .= '</div>'; // end product-inner
    $html .= '</div>'; // end writgo-product-box

    // Add styles
    $html .= '<style>
        .writgo-product-box { position: relative; margin: 2rem 0; background: #fff; border: 2px solid #e5e7eb; border-radius: 12px; overflow: hidden; transition: all 0.3s; }
        .writgo-product-box:hover { border-color: #f97316; box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .product-badge { position: absolute; top: 15px; left: -35px; background: linear-gradient(135deg, #f97316, #ea580c); color: #fff; padding: 6px 40px; font-size: 12px; font-weight: 600; transform: rotate(-45deg); z-index: 1; }
        .product-inner { display: grid; grid-template-columns: 180px 1fr 200px; gap: 20px; padding: 25px; }
        @media (max-width: 768px) { .product-inner { grid-template-columns: 1fr; text-align: center; } }
        .product-image { display: flex; align-items: center; justify-content: center; }
        .product-image img { max-width: 150px; max-height: 150px; object-fit: contain; border-radius: 8px; }
        .product-content { display: flex; flex-direction: column; justify-content: center; }
        .product-name { margin: 0 0 10px; font-size: 1.25rem; color: #1e293b; }
        .product-rating { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .product-rating .rating-value { background: #f97316; color: #fff; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 14px; }
        .product-rating .rating-bar { flex: 1; max-width: 150px; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; }
        .product-rating .rating-fill { height: 100%; background: linear-gradient(90deg, #f97316, #ea580c); border-radius: 4px; }
        .product-description { color: #475569; line-height: 1.6; font-size: 14px; }
        .product-sidebar { display: flex; flex-direction: column; justify-content: center; align-items: center; padding-left: 20px; border-left: 1px solid #e5e7eb; }
        @media (max-width: 768px) { .product-sidebar { border-left: none; border-top: 1px solid #e5e7eb; padding: 20px 0 0; } }
        .product-price { margin-bottom: 10px; text-align: center; }
        .product-price .old-price { text-decoration: line-through; color: #94a3b8; font-size: 14px; display: block; }
        .product-price .current-price { font-size: 1.75rem; font-weight: 700; color: #16a34a; }
        .product-store { font-size: 12px; color: #64748b; margin-bottom: 10px; }
        .product-button { display: inline-block; padding: 14px 28px; background: linear-gradient(135deg, #f97316, #ea580c); color: #fff !important; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.2s; white-space: nowrap; }
        .product-button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(249,115,22,0.4); }
    </style>';

    return $html;
}

/**
 * =====================================================
 * STAR RATING SHORTCODE (Simple inline rating)
 * [writgo_rating score="8.5" max="10"]
 * =====================================================
 */
add_shortcode('writgo_rating', 'writgo_rating_shortcode');
function writgo_rating_shortcode($atts) {
    $atts = shortcode_atts(array(
        'score' => '0',
        'max' => '10',
        'label' => '',
    ), $atts);

    $score = floatval($atts['score']);
    $max = floatval($atts['max']);
    $percentage = ($score / $max) * 100;

    $html = '<span class="writgo-rating-inline">';
    if ($atts['label']) {
        $html .= '<span class="rating-label">' . esc_html($atts['label']) . ': </span>';
    }
    $html .= '<span class="rating-score" style="background: hsl(' . ($percentage * 1.2) . ', 70%, 45%);">' . number_format($score, 1) . '</span>';
    $html .= '<span class="rating-max">/' . number_format($max, 0) . '</span>';
    $html .= '</span>';

    $html .= '<style>
        .writgo-rating-inline { display: inline-flex; align-items: center; gap: 4px; font-weight: 600; }
        .writgo-rating-inline .rating-label { color: #64748b; font-weight: normal; }
        .writgo-rating-inline .rating-score { color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 14px; }
        .writgo-rating-inline .rating-max { color: #94a3b8; font-size: 12px; }
    </style>';

    return $html;
}
