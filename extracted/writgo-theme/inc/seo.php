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

        if (has_post_thumbnail()) {
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
