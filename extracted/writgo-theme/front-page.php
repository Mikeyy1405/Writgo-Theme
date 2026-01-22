<?php
/**
 * Front Page Template - Homepage
 * All sections customizable via Customizer
 *
 * @package Writgo_Affiliate
 */

get_header();

// Get Customizer settings
$show_hero = get_theme_mod('writgo_hero_show', true);
$hero_bg = get_theme_mod('writgo_hero_bg', '');
$hero_title = get_theme_mod('writgo_hero_title', '');
$hero_subtitle = get_theme_mod('writgo_hero_subtitle', '');
$hero_search_placeholder = get_theme_mod('writgo_hero_search_placeholder', 'Waar ben je naar op zoek?');
$hero_search_button = get_theme_mod('writgo_hero_search_button', 'Zoeken');

$show_featured = get_theme_mod('writgo_featured_show', true);
$featured_title = get_theme_mod('writgo_featured_title', 'Uitgelicht');

$show_popular = get_theme_mod('writgo_popular_show', true);
$popular_title = get_theme_mod('writgo_popular_title', 'Meest gelezen');
$popular_icon = get_theme_mod('writgo_popular_icon', '🔥');
$popular_count = get_theme_mod('writgo_popular_count', 4);

$show_newsletter = get_theme_mod('writgo_newsletter_show', true);
$newsletter_title = get_theme_mod('writgo_newsletter_title', 'Nieuwsbrief');
$newsletter_text = get_theme_mod('writgo_newsletter_text', 'Wekelijks tips, nieuwe reviews en exclusieve aanbiedingen in je inbox.');
$newsletter_button = get_theme_mod('writgo_newsletter_button', 'Aanmelden');

$show_latest = get_theme_mod('writgo_latest_show', true);
$latest_title = get_theme_mod('writgo_latest_title', 'Laatste artikelen');
$latest_count = get_theme_mod('writgo_latest_count', 4);

$show_reviews = get_theme_mod('writgo_reviews_show', true);
$reviews_title = get_theme_mod('writgo_reviews_title', 'Reviews');
$reviews_icon = get_theme_mod('writgo_reviews_icon', '⭐');
$reviews_tag = get_theme_mod('writgo_reviews_tag', 'review');
$reviews_count = get_theme_mod('writgo_reviews_count', 4);

$show_toplists = get_theme_mod('writgo_toplists_show', true);
$toplists_title = get_theme_mod('writgo_toplists_title', 'Beste lijstjes');
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

<main class="wa-home">

    <?php if ($show_hero) : ?>
    <!-- Hero/Search Section -->
    <section class="wa-home-hero" <?php if ($hero_bg) echo 'style="background-image: url(' . esc_url($hero_bg) . '); background-size: cover; background-position: center;"'; ?>>
        <div class="wa-container-wide">
            <div class="wa-hero-inner">
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
                <span class="wa-chips-label">Populair:</span>
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
                                    <time datetime="<?php echo get_the_date('c'); ?>">Bijgewerkt <?php echo get_the_modified_date('j M Y'); ?></time>
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
                    
                    <?php if ($show_newsletter) : ?>
                    <!-- Newsletter -->
                    <div class="wa-sidebar-widget wa-newsletter-widget">
                        <h3 class="wa-widget-title">
                            <span class="wa-widget-icon">📬</span>
                            <?php echo esc_html($newsletter_title); ?>
                        </h3>
                        <p class="wa-newsletter-text"><?php echo esc_html($newsletter_text); ?></p>
                        <form class="wa-newsletter-form" action="#" method="post">
                            <input type="email" placeholder="Je e-mailadres" class="wa-newsletter-input" required />
                            <button type="submit" class="wa-newsletter-button"><?php echo esc_html($newsletter_button); ?></button>
                        </form>
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
                        Bekijk alles
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
                        Alle reviews
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
                    <p class="wa-empty-text">Binnenkort reviews beschikbaar</p>
                    <p class="wa-empty-hint">Voeg de tag "<?php echo esc_html($reviews_tag); ?>" toe aan posts om ze hier te tonen.</p>
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
                        Alle lijstjes
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
                                    <span class="wa-card-badge wa-badge-top">Top lijst</span>
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
                    <p class="wa-empty-text">Binnenkort beste lijstjes beschikbaar</p>
                    <p class="wa-empty-hint">Voeg de tag "<?php echo esc_html($toplists_tag); ?>" toe aan posts om ze hier te tonen.</p>
                </div>
            <?php endif; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
