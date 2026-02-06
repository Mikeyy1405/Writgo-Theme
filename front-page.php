<?php
/**
 * Front Page Template - Homepage
 * All sections customizable via Customizer
 *
 * @package Writgo_Affiliate
 */

get_header();

// Get Customizer settings with auto-translation
$show_hero = get_theme_mod('writgo_hero_show', true);
$hero_bg = get_theme_mod('writgo_hero_bg', '');
$hero_title = get_theme_mod('writgo_hero_title', '');
$hero_subtitle = get_theme_mod('writgo_hero_subtitle', '');
$hero_search_placeholder = writgo_get_mod('writgo_hero_search_placeholder', 'what_looking_for', 'Waar ben je naar op zoek?');
$hero_search_button = writgo_get_mod('writgo_hero_search_button', 'search_button', 'Zoeken');

$show_featured = get_theme_mod('writgo_featured_show', true);
$featured_title = writgo_get_mod('writgo_featured_title', 'featured', 'Uitgelicht');

$show_popular = get_theme_mod('writgo_popular_show', true);
$popular_title = writgo_get_mod('writgo_popular_title', 'most_read', 'Meest gelezen');
$popular_icon = get_theme_mod('writgo_popular_icon', '🔥');
$popular_count = get_theme_mod('writgo_popular_count', 4);

$show_latest = get_theme_mod('writgo_latest_show', true);
$latest_title = writgo_get_mod('writgo_latest_title', 'latest_articles', 'Laatste artikelen');
$latest_count = get_theme_mod('writgo_latest_count', 4);

$show_reviews = get_theme_mod('writgo_reviews_show', true);
$reviews_title = writgo_get_mod('writgo_reviews_title', 'reviews', 'Reviews');
$reviews_icon = get_theme_mod('writgo_reviews_icon', '⭐');
$reviews_tag = get_theme_mod('writgo_reviews_tag', 'review');
$reviews_count = get_theme_mod('writgo_reviews_count', 4);

$show_toplists = get_theme_mod('writgo_toplists_show', true);
$toplists_title = writgo_get_mod('writgo_toplists_title', 'best_lists', 'Beste lijstjes');
$toplists_icon = get_theme_mod('writgo_toplists_icon', '🏆');
$toplists_tag = get_theme_mod('writgo_toplists_tag', 'beste,top');
$toplists_count = get_theme_mod('writgo_toplists_count', 4);

// Get featured post
$featured_args = array(
    'posts_per_page' => 1,
    'meta_key'       => '_writgo_featured',
    'meta_value'     => '1',
    'post_status'    => 'publish'
);
$featured_query = new WP_Query($featured_args);

if (!$featured_query->have_posts()) {
    $featured_query = new WP_Query(array('posts_per_page' => 1, 'post_status' => 'publish'));
}

$featured_id = $featured_query->have_posts() ? $featured_query->posts[0]->ID : 0;
?>

<main id="main-content" class="wa-home">

    <?php if ($show_hero) : 
        $hero_overlay = get_theme_mod('writgo_hero_overlay_color', 'rgba(0,0,0,0.5)');
        $hero_text_color = get_theme_mod('writgo_hero_text_color', '#ffffff');
        
        // Check for WebP version of hero image
        $hero_bg_webp = '';
        if ($hero_bg && function_exists('writgo_get_webp_url')) {
            $hero_bg_webp = writgo_get_webp_url($hero_bg);
        }
        
        $hero_styles = array();
        if ($hero_bg) {
            // Use WebP if available
            $bg_url = $hero_bg_webp ? $hero_bg_webp : $hero_bg;
            $hero_styles[] = "background-image: url(" . esc_url($bg_url) . ")";
            $hero_styles[] = "background-size: cover";
            $hero_styles[] = "background-position: center";
        }
        if ($hero_text_color && $hero_text_color !== '#ffffff') {
            $hero_styles[] = "--hero-text-color: " . esc_attr($hero_text_color);
        }
    ?>
    <!-- Hero/Search Section -->
    <section class="wa-home-hero" <?php echo !empty($hero_styles) ? 'style="' . implode('; ', $hero_styles) . ';"' : ''; ?>>
        <?php if ($hero_bg && $hero_overlay) : ?>
        <div class="wa-home-hero-overlay" style="background: <?php echo esc_attr($hero_overlay); ?>;"></div>
        <?php endif; ?>
        <div class="wa-container-wide">
            <div class="wa-hero-inner" <?php echo $hero_text_color ? 'style="color: ' . esc_attr($hero_text_color) . ';"' : ''; ?>>
                <h1 class="wa-home-title"><?php echo esc_html($hero_title ?: get_bloginfo('name')); ?></h1>
                <p class="wa-home-tagline"><?php echo esc_html($hero_subtitle ?: get_bloginfo('description')); ?></p>
                
                <div class="wa-home-search">
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="wa-search-wrapper">
                            <input type="search" class="wa-search-input" placeholder="<?php echo esc_attr($hero_search_placeholder); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                            <button type="submit" class="wa-search-button">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                                </svg>
                                <span><?php echo esc_html($hero_search_button); ?></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Category Chips -->
    <?php
    $categories = get_categories(array('orderby' => 'count', 'order' => 'DESC', 'number' => 8, 'hide_empty' => true));
    if (!empty($categories)) :
    ?>
    <section class="wa-categories-bar">
        <div class="wa-container-wide">
            <div class="wa-category-chips">
                <span class="wa-chips-label"><?php writgo_te('popular'); ?>:</span>
                <?php foreach ($categories as $cat) : ?>
                    <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="wa-chip"><?php echo esc_html($cat->name); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_featured) : ?>
    <!-- Featured + Sidebar Section -->
    <section class="wa-featured-section">
        <div class="wa-container-wide">
            <div class="wa-featured-grid">
                
                <!-- Featured Post -->
                <div class="wa-featured-main">
                    <span class="wa-section-label"><?php echo esc_html($featured_title); ?></span>
                    
                    <?php if ($featured_query->have_posts()) : while ($featured_query->have_posts()) : $featured_query->the_post(); ?>
                        <article class="wa-featured-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="wa-featured-image-link">
                                    <?php the_post_thumbnail('writgo-featured', array('class' => 'wa-featured-image')); ?>
                                    <span class="wa-card-badge">Blog</span>
                                </a>
                            <?php endif; ?>
                            
                            <div class="wa-featured-content">
                                <h2 class="wa-featured-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <p class="wa-featured-excerpt"><?php echo wp_trim_words(get_the_excerpt() ?: get_the_content(), 30); ?></p>
                                <div class="wa-featured-meta">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 32, '', '', array('class' => 'wa-meta-avatar')); ?>
                                    <span class="wa-meta-author"><?php the_author(); ?></span>
                                    <span class="wa-meta-sep">·</span>
                                    <time datetime="<?php echo get_the_date('c'); ?>"><?php writgo_te('updated'); ?> <?php echo get_the_modified_date('j M Y'); ?></time>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; endif; wp_reset_postdata(); ?>
                </div>
                
                <!-- Sidebar -->
                <aside class="wa-home-sidebar">
                    
                    <?php if ($show_popular) : ?>
                    <!-- Meest Gelezen -->
                    <div class="wa-sidebar-widget">
                        <h3 class="wa-widget-title">
                            <span class="wa-widget-icon"><?php echo esc_html($popular_icon); ?></span>
                            <?php echo esc_html($popular_title); ?>
                        </h3>
                        
                        <?php
                        $popular_query = new WP_Query(array(
                            'posts_per_page' => $popular_count,
                            'orderby'        => 'comment_count',
                            'order'          => 'DESC',
                            'post_status'    => 'publish'
                        ));
                        
                        if ($popular_query->have_posts()) :
                        ?>
                        <ul class="wa-popular-list">
                            <?php while ($popular_query->have_posts()) : $popular_query->the_post(); ?>
                                <li class="wa-popular-item">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <a href="<?php the_permalink(); ?>" class="wa-popular-thumb"><?php the_post_thumbnail('writgo-thumb'); ?></a>
                                    <?php endif; ?>
                                    <div class="wa-popular-content">
                                        <a href="<?php the_permalink(); ?>" class="wa-popular-title"><?php the_title(); ?></a>
                                        <time class="wa-popular-date"><?php echo get_the_date('j M Y'); ?></time>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                        <?php endif; wp_reset_postdata(); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php 
                    // Sidebar Widget - supports multiple types
                    $widget_type = get_theme_mod('writgo_sidebar_widget_type', 'newsletter');
                    $widget_icon = get_theme_mod('writgo_sidebar_widget_icon', '📬');
                    $widget_title = writgo_get_mod('writgo_newsletter_title', 'newsletter', 'Nieuwsbrief');
                    $widget_text = writgo_get_mod('writgo_newsletter_text', 'newsletter_text', 'Wekelijks tips, nieuwe reviews en exclusieve aanbiedingen in je inbox.');
                    $widget_button = writgo_get_mod('writgo_newsletter_button', 'subscribe', 'Aanmelden');
                    $widget_url = get_theme_mod('writgo_sidebar_widget_url', '');
                    
                    // Get widget image - handle both URL and attachment ID
                    $widget_image_raw = get_theme_mod('writgo_sidebar_widget_image', '');
                    $widget_image = '';
                    if ($widget_image_raw) {
                        // Check if it's a numeric attachment ID
                        if (is_numeric($widget_image_raw)) {
                            $widget_image = wp_get_attachment_url($widget_image_raw);
                        } else {
                            $widget_image = $widget_image_raw;
                        }
                    }
                    
                    $widget_html = get_theme_mod('writgo_sidebar_widget_html', '');
                    
                    if ($widget_type !== 'none') : 
                    ?>
                    <!-- Sidebar Widget -->
                    <div class="wa-sidebar-widget wa-newsletter-widget">
                        <?php if ($widget_type === 'newsletter') : ?>
                            <!-- Newsletter Form -->
                            <h3 class="wa-widget-title">
                                <span class="wa-widget-icon"><?php echo esc_html($widget_icon); ?></span>
                                <?php echo esc_html($widget_title); ?>
                            </h3>
                            <p class="wa-newsletter-text"><?php echo esc_html($widget_text); ?></p>
                            <form class="wa-newsletter-form" action="#" method="post">
                                <input type="email" placeholder="<?php echo esc_attr(writgo_t('your_email')); ?>" class="wa-newsletter-input" required />
                                <button type="submit" class="wa-newsletter-button"><?php echo esc_html($widget_button); ?></button>
                            </form>
                            
                        <?php elseif ($widget_type === 'cta') : ?>
                            <!-- Call-to-Action -->
                            <h3 class="wa-widget-title">
                                <span class="wa-widget-icon"><?php echo esc_html($widget_icon); ?></span>
                                <?php echo esc_html($widget_title); ?>
                            </h3>
                            <p class="wa-newsletter-text"><?php echo esc_html($widget_text); ?></p>
                            <?php if ($widget_url) : ?>
                                <a href="<?php echo esc_url($widget_url); ?>" class="wa-newsletter-button wa-cta-button" style="display: block; text-align: center; text-decoration: none;">
                                    <?php echo esc_html($widget_button); ?>
                                </a>
                            <?php endif; ?>
                            
                        <?php elseif ($widget_type === 'ad') : ?>
                            <!-- Advertisement -->
                            <?php if ($widget_image) : ?>
                                <?php if ($widget_url) : ?>
                                    <a href="<?php echo esc_url($widget_url); ?>" target="_blank" rel="noopener noreferrer sponsored">
                                        <img src="<?php echo esc_url($widget_image); ?>" alt="<?php echo esc_attr($widget_title); ?>" style="width: 100%; height: auto; border-radius: 8px;" />
                                    </a>
                                <?php else : ?>
                                    <img src="<?php echo esc_url($widget_image); ?>" alt="<?php echo esc_attr($widget_title); ?>" style="width: 100%; height: auto; border-radius: 8px;" />
                                <?php endif; ?>
                            <?php else : ?>
                                <h3 class="wa-widget-title">
                                    <span class="wa-widget-icon"><?php echo esc_html($widget_icon); ?></span>
                                    <?php echo esc_html($widget_title); ?>
                                </h3>
                                <p class="wa-newsletter-text"><?php echo esc_html($widget_text); ?></p>
                                <?php if ($widget_url) : ?>
                                    <a href="<?php echo esc_url($widget_url); ?>" class="wa-newsletter-button" style="display: block; text-align: center; text-decoration: none;" target="_blank" rel="noopener noreferrer sponsored">
                                        <?php echo esc_html($widget_button); ?>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                        <?php elseif ($widget_type === 'custom') : ?>
                            <!-- Custom HTML -->
                            <?php echo wp_kses_post($widget_html); ?>
                            
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                </aside>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_latest) : ?>
    <!-- Laatste Artikelen -->
    <section class="wa-latest-section">
        <div class="wa-container-wide">
            <div class="wa-section-header">
                <h2 class="wa-section-title"><?php echo esc_html($latest_title); ?></h2>
                <?php $blog_page = get_option('page_for_posts'); if ($blog_page) : ?>
                    <a href="<?php echo esc_url(get_permalink($blog_page)); ?>" class="wa-section-link">
                        <?php writgo_te('view_all'); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                <?php endif; ?>
            </div>
            
            <?php
            $latest_query = new WP_Query(array(
                'posts_per_page' => $latest_count,
                'post__not_in'   => array($featured_id),
                'post_status'    => 'publish'
            ));
            
            if ($latest_query->have_posts()) :
            ?>
                <div class="wa-posts-grid">
                    <?php while ($latest_query->have_posts()) : $latest_query->the_post(); ?>
                        <article class="wa-post-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="wa-card-image-link">
                                    <?php the_post_thumbnail('writgo-card', array('class' => 'wa-card-image')); ?>
                                    <span class="wa-card-badge">Blog</span>
                                </a>
                            <?php endif; ?>
                            <div class="wa-card-content">
                                <?php $cats = get_the_category(); if (!empty($cats)) : ?>
                                    <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>" class="wa-card-category"><?php echo esc_html($cats[0]->name); ?></a>
                                <?php endif; ?>
                                <h3 class="wa-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <time class="wa-card-date" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('j M Y'); ?></time>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php endif; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_reviews) : ?>
    <!-- Reviews Sectie -->
    <?php
    $reviews_query = new WP_Query(array(
        'posts_per_page' => $reviews_count,
        'post_status'    => 'publish',
        'tag'            => $reviews_tag,
    ));
    $review_tag_obj = get_term_by('slug', $reviews_tag, 'post_tag');
    ?>
    <section class="wa-content-section wa-reviews-section">
        <div class="wa-container-wide">
            <div class="wa-section-header">
                <h2 class="wa-section-title">
                    <span class="wa-section-icon"><?php echo esc_html($reviews_icon); ?></span>
                    <?php echo esc_html($reviews_title); ?>
                </h2>
                <?php if ($review_tag_obj) : ?>
                    <a href="<?php echo esc_url(get_tag_link($review_tag_obj->term_id)); ?>" class="wa-section-link">
                        <?php writgo_te('all_reviews'); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if ($reviews_query->have_posts()) : ?>
                <div class="wa-posts-grid">
                    <?php while ($reviews_query->have_posts()) : $reviews_query->the_post(); 
                        $score = get_post_meta(get_the_ID(), '_writgo_score', true);
                    ?>
                        <article class="wa-post-card wa-review-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="wa-card-image-link">
                                    <?php the_post_thumbnail('writgo-card', array('class' => 'wa-card-image')); ?>
                                    <span class="wa-card-badge wa-badge-review">Review</span>
                                    <?php if ($score) : ?><span class="wa-card-score"><?php echo number_format((float)$score, 1); ?></span><?php endif; ?>
                                </a>
                            <?php endif; ?>
                            <div class="wa-card-content">
                                <h3 class="wa-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <time class="wa-card-date" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('j M Y'); ?></time>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="wa-empty-section">
                    <div class="wa-empty-icon"><?php echo esc_html($reviews_icon); ?></div>
                    <p class="wa-empty-text"><?php echo esc_html(writgo_t('reviews') . ' ' . strtolower(writgo_t('coming_soon'))); ?></p>
                    <p class="wa-empty-hint"><?php echo esc_html(sprintf(writgo_t('add_tag_hint'), $reviews_tag)); ?></p>
                </div>
            <?php endif; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_toplists) : ?>
    <!-- Beste Lijstjes Sectie -->
    <?php
    $top_query = new WP_Query(array(
        'posts_per_page' => $toplists_count,
        'post_status'    => 'publish',
        'tag'            => $toplists_tag,
    ));
    $top_tags = explode(',', $toplists_tag);
    $top_tag_obj = get_term_by('slug', trim($top_tags[0]), 'post_tag');
    ?>
    <section class="wa-content-section wa-toplist-section">
        <div class="wa-container-wide">
            <div class="wa-section-header">
                <h2 class="wa-section-title">
                    <span class="wa-section-icon"><?php echo esc_html($toplists_icon); ?></span>
                    <?php echo esc_html($toplists_title); ?>
                </h2>
                <?php if ($top_tag_obj) : ?>
                    <a href="<?php echo esc_url(get_tag_link($top_tag_obj->term_id)); ?>" class="wa-section-link">
                        <?php writgo_te('all_lists'); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if ($top_query->have_posts()) : ?>
                <div class="wa-posts-grid">
                    <?php while ($top_query->have_posts()) : $top_query->the_post(); ?>
                        <article class="wa-post-card wa-toplist-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="wa-card-image-link">
                                    <?php the_post_thumbnail('writgo-card', array('class' => 'wa-card-image')); ?>
                                    <span class="wa-card-badge wa-badge-top"><?php writgo_te('top_list'); ?></span>
                                </a>
                            <?php endif; ?>
                            <div class="wa-card-content">
                                <h3 class="wa-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <time class="wa-card-date" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('j M Y'); ?></time>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="wa-empty-section">
                    <div class="wa-empty-icon"><?php echo esc_html($toplists_icon); ?></div>
                    <p class="wa-empty-text"><?php echo esc_html(writgo_t('best_lists') . ' ' . strtolower(writgo_t('coming_soon'))); ?></p>
                    <p class="wa-empty-hint"><?php echo esc_html(sprintf(writgo_t('add_tag_hint'), $toplists_tag)); ?></p>
                </div>
            <?php endif; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
