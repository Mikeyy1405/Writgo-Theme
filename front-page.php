<?php
/**
 * Front Page Template - Homepage
 *
 * @package Writgo_Affiliate
 */

get_header();

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
$popular_icon = get_theme_mod('writgo_popular_icon', '');
$popular_count = get_theme_mod('writgo_popular_count', 4);

$show_latest = get_theme_mod('writgo_latest_show', true);
$latest_title = writgo_get_mod('writgo_latest_title', 'latest_articles', 'Laatste artikelen');
$latest_count = get_theme_mod('writgo_latest_count', 6);

$show_reviews = get_theme_mod('writgo_reviews_show', true);
$reviews_title = writgo_get_mod('writgo_reviews_title', 'reviews', 'Reviews');
$reviews_icon = get_theme_mod('writgo_reviews_icon', '');
$reviews_tag = get_theme_mod('writgo_reviews_tag', 'review');
$reviews_count = get_theme_mod('writgo_reviews_count', 4);

$show_toplists = get_theme_mod('writgo_toplists_show', true);
$toplists_title = writgo_get_mod('writgo_toplists_title', 'best_lists', 'Beste lijstjes');
$toplists_icon = get_theme_mod('writgo_toplists_icon', '');
$toplists_tag = get_theme_mod('writgo_toplists_tag', 'beste,top');
$toplists_count = get_theme_mod('writgo_toplists_count', 4);

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

<main id="main-content">

    <?php if ($show_hero) :
        $hero_overlay = get_theme_mod('writgo_hero_overlay_color', 'rgba(0,0,0,0.5)');
    ?>
    <!-- Hero Section -->
    <section class="relative overflow-hidden <?php echo $hero_bg ? 'min-h-[420px] lg:min-h-[500px]' : 'bg-gradient-to-br from-blue-900 via-indigo-900 to-purple-900 min-h-[380px] lg:min-h-[460px]'; ?> flex items-center" <?php echo $hero_bg ? 'style="background-image:url(' . esc_url($hero_bg) . ');background-size:cover;background-position:center;"' : ''; ?>>
        <?php if ($hero_bg) : ?>
        <div class="absolute inset-0" style="background: <?php echo esc_attr($hero_overlay); ?>;"></div>
        <?php endif; ?>
        <!-- Decorative elements -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.1),transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(99,102,241,0.15),transparent_50%)]"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 text-center w-full">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-extrabold text-white leading-tight mb-4 lg:mb-6 tracking-tight">
                <?php echo esc_html($hero_title ?: get_bloginfo('name')); ?>
            </h1>
            <p class="text-lg lg:text-xl text-white/80 mb-8 lg:mb-10 max-w-2xl mx-auto leading-relaxed">
                <?php echo esc_html($hero_subtitle ?: get_bloginfo('description')); ?>
            </p>

            <div class="max-w-xl mx-auto">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="relative group">
                        <input type="search" class="w-full h-14 lg:h-16 pl-6 pr-36 text-gray-900 bg-white rounded-2xl shadow-xl border-0 focus:ring-4 focus:ring-white/30 outline-none placeholder-gray-400 text-base lg:text-lg" placeholder="<?php echo esc_attr($hero_search_placeholder); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 h-10 lg:h-12 px-5 lg:px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-sm lg:text-base transition-colors shadow-lg shadow-blue-600/25 flex items-center gap-2">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                            </svg>
                            <span class="hidden sm:inline"><?php echo esc_html($hero_search_button); ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Category Chips -->
    <?php
    $categories = get_categories(array('orderby' => 'count', 'order' => 'DESC', 'number' => 8, 'hide_empty' => true));
    if (!empty($categories)) :
    ?>
    <section class="border-b border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center gap-3 overflow-x-auto scrollbar-hide">
                <span class="text-sm font-medium text-gray-500 whitespace-nowrap"><?php writgo_te('popular'); ?>:</span>
                <?php foreach ($categories as $cat) : ?>
                    <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="whitespace-nowrap px-4 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors"><?php echo esc_html($cat->name); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_featured) : ?>
    <!-- Featured + Sidebar Section -->
    <section class="py-10 lg:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">

                <!-- Featured Post -->
                <div class="lg:col-span-2">
                    <span class="inline-block text-xs font-semibold uppercase tracking-wider text-blue-600 mb-4"><?php echo esc_html($featured_title); ?></span>

                    <?php if ($featured_query->have_posts()) : while ($featured_query->have_posts()) : $featured_query->the_post(); ?>
                        <article class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="block relative overflow-hidden aspect-[16/9]">
                                    <?php the_post_thumbnail('writgo-hero', array('class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500')); ?>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                                </a>
                            <?php endif; ?>

                            <div class="p-6 lg:p-8">
                                <h2 class="text-xl lg:text-2xl font-bold text-gray-900 mb-3 group-hover:text-blue-700 transition-colors">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <p class="text-gray-600 mb-4 line-clamp-2 leading-relaxed"><?php echo wp_trim_words(get_the_excerpt() ?: get_the_content(), 30); ?></p>
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 32, '', '', array('class' => 'w-8 h-8 rounded-full ring-2 ring-white')); ?>
                                    <span class="font-medium text-gray-700"><?php the_author(); ?></span>
                                    <span class="text-gray-300">|</span>
                                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_modified_date('j M Y'); ?></time>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; endif; wp_reset_postdata(); ?>
                </div>

                <!-- Sidebar -->
                <aside class="space-y-8">

                    <?php if ($show_popular) : ?>
                    <!-- Popular Posts -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 mb-5 flex items-center gap-2">
                            <svg class="text-orange-500" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 23a7.5 7.5 0 01-5.138-12.963C8.204 8.774 11.5 6.5 11 1.5c6 4 9 8 3 14 1 0 2.5 0 5-2.5.024.5.024 1 0 1.5A7.5 7.5 0 0112 23z"/></svg>
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
                        <div class="space-y-4">
                            <?php $counter = 1; while ($popular_query->have_posts()) : $popular_query->the_post(); ?>
                                <a href="<?php the_permalink(); ?>" class="flex items-start gap-3 group">
                                    <span class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-100 text-gray-500 text-xs font-bold flex items-center justify-center group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors"><?php echo $counter; ?></span>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2 leading-snug"><?php the_title(); ?></h4>
                                        <time class="text-xs text-gray-400 mt-1 block"><?php echo get_the_date('j M Y'); ?></time>
                                    </div>
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="flex-shrink-0 w-16 h-12 rounded-lg overflow-hidden">
                                            <?php the_post_thumbnail('writgo-thumb', array('class' => 'w-full h-full object-cover')); ?>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            <?php $counter++; endwhile; ?>
                        </div>
                        <?php endif; wp_reset_postdata(); ?>
                    </div>
                    <?php endif; ?>

                    <?php
                    $widget_type = get_theme_mod('writgo_sidebar_widget_type', 'newsletter');
                    $widget_title = writgo_get_mod('writgo_newsletter_title', 'newsletter', 'Nieuwsbrief');
                    $widget_text = writgo_get_mod('writgo_newsletter_text', 'newsletter_text', 'Wekelijks tips, nieuwe reviews en exclusieve aanbiedingen in je inbox.');
                    $widget_button = writgo_get_mod('writgo_newsletter_button', 'subscribe', 'Aanmelden');
                    $widget_url = get_theme_mod('writgo_sidebar_widget_url', '');
                    $widget_image_raw = get_theme_mod('writgo_sidebar_widget_image', '');
                    $widget_image = '';
                    if ($widget_image_raw) {
                        $widget_image = is_numeric($widget_image_raw) ? wp_get_attachment_url($widget_image_raw) : $widget_image_raw;
                    }
                    $widget_html = get_theme_mod('writgo_sidebar_widget_html', '');

                    if ($widget_type !== 'none') :
                    ?>
                    <!-- Sidebar Widget -->
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white">
                        <?php if ($widget_type === 'newsletter') : ?>
                            <h3 class="font-bold text-lg mb-2"><?php echo esc_html($widget_title); ?></h3>
                            <p class="text-blue-100 text-sm mb-4"><?php echo esc_html($widget_text); ?></p>
                            <form class="space-y-3" action="#" method="post">
                                <input type="email" placeholder="<?php echo esc_attr(writgo_t('your_email')); ?>" class="w-full h-11 px-4 rounded-xl bg-white/20 border border-white/20 text-white placeholder-white/60 focus:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/40" required />
                                <button type="submit" class="w-full h-11 bg-white text-blue-700 rounded-xl font-semibold hover:bg-blue-50 transition-colors"><?php echo esc_html($widget_button); ?></button>
                            </form>
                        <?php elseif ($widget_type === 'cta') : ?>
                            <h3 class="font-bold text-lg mb-2"><?php echo esc_html($widget_title); ?></h3>
                            <p class="text-blue-100 text-sm mb-4"><?php echo esc_html($widget_text); ?></p>
                            <?php if ($widget_url) : ?>
                                <a href="<?php echo esc_url($widget_url); ?>" class="block w-full text-center h-11 leading-[2.75rem] bg-white text-blue-700 rounded-xl font-semibold hover:bg-blue-50 transition-colors"><?php echo esc_html($widget_button); ?></a>
                            <?php endif; ?>
                        <?php elseif ($widget_type === 'ad') : ?>
                            <?php if ($widget_image) : ?>
                                <?php if ($widget_url) : ?>
                                    <a href="<?php echo esc_url($widget_url); ?>" target="_blank" rel="noopener noreferrer sponsored">
                                        <img src="<?php echo esc_url($widget_image); ?>" alt="<?php echo esc_attr($widget_title); ?>" class="w-full rounded-xl" />
                                    </a>
                                <?php else : ?>
                                    <img src="<?php echo esc_url($widget_image); ?>" alt="<?php echo esc_attr($widget_title); ?>" class="w-full rounded-xl" />
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php elseif ($widget_type === 'custom') : ?>
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
    <!-- Latest Articles -->
    <section class="py-10 lg:py-14 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900"><?php echo esc_html($latest_title); ?></h2>
                <?php $blog_page = get_option('page_for_posts'); if ($blog_page) : ?>
                    <a href="<?php echo esc_url(get_permalink($blog_page)); ?>" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <?php while ($latest_query->have_posts()) : $latest_query->the_post(); ?>
                        <article class="group bg-gray-50 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300 border border-gray-100 hover:border-gray-200">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="block relative overflow-hidden aspect-[16/10]">
                                    <?php the_post_thumbnail('writgo-card', array('class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500')); ?>
                                </a>
                            <?php endif; ?>
                            <div class="p-5">
                                <?php $cats = get_the_category(); if (!empty($cats)) : ?>
                                    <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>" class="inline-block text-xs font-semibold uppercase tracking-wider text-blue-600 hover:text-blue-700 mb-2"><?php echo esc_html($cats[0]->name); ?></a>
                                <?php endif; ?>
                                <h3 class="font-bold text-gray-900 mb-2 group-hover:text-blue-700 transition-colors line-clamp-2 leading-snug">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <time class="text-xs text-gray-400" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('j M Y'); ?></time>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php endif; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_reviews) : ?>
    <!-- Reviews -->
    <?php
    $reviews_query = new WP_Query(array(
        'posts_per_page' => $reviews_count,
        'post_status'    => 'publish',
        'tag'            => $reviews_tag,
    ));
    $review_tag_obj = get_term_by('slug', $reviews_tag, 'post_tag');
    ?>
    <section class="py-10 lg:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="text-yellow-500" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <?php echo esc_html($reviews_title); ?>
                </h2>
                <?php if ($review_tag_obj) : ?>
                    <a href="<?php echo esc_url(get_tag_link($review_tag_obj->term_id)); ?>" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
                        <?php writgo_te('all_reviews'); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($reviews_query->have_posts()) : ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php while ($reviews_query->have_posts()) : $reviews_query->the_post();
                        $score = get_post_meta(get_the_ID(), '_writgo_score', true);
                    ?>
                        <article class="group bg-white rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300 border border-gray-100">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="block relative overflow-hidden aspect-[16/10]">
                                    <?php the_post_thumbnail('writgo-card', array('class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500')); ?>
                                    <span class="absolute top-3 left-3 px-2.5 py-1 text-xs font-bold bg-yellow-400 text-yellow-900 rounded-lg">Review</span>
                                    <?php if ($score) : ?>
                                        <span class="absolute top-3 right-3 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg"><?php echo number_format((float)$score, 1); ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                            <div class="p-5">
                                <h3 class="font-bold text-gray-900 group-hover:text-blue-700 transition-colors line-clamp-2 leading-snug">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <time class="text-xs text-gray-400 mt-2 block" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('j M Y'); ?></time>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                    <svg class="mx-auto text-gray-300 mb-4" width="48" height="48" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <p class="text-gray-400"><?php echo esc_html(writgo_t('reviews') . ' ' . strtolower(writgo_t('coming_soon'))); ?></p>
                    <p class="text-sm text-gray-300 mt-1"><?php echo esc_html(sprintf(writgo_t('add_tag_hint'), $reviews_tag)); ?></p>
                </div>
            <?php endif; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_toplists) : ?>
    <!-- Toplists -->
    <?php
    $top_query = new WP_Query(array(
        'posts_per_page' => $toplists_count,
        'post_status'    => 'publish',
        'tag'            => $toplists_tag,
    ));
    $top_tags = explode(',', $toplists_tag);
    $top_tag_obj = get_term_by('slug', trim($top_tags[0]), 'post_tag');
    ?>
    <section class="py-10 lg:py-14 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="text-amber-500" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    <?php echo esc_html($toplists_title); ?>
                </h2>
                <?php if ($top_tag_obj) : ?>
                    <a href="<?php echo esc_url(get_tag_link($top_tag_obj->term_id)); ?>" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
                        <?php writgo_te('all_lists'); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($top_query->have_posts()) : ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php while ($top_query->have_posts()) : $top_query->the_post(); ?>
                        <article class="group bg-gray-50 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300 border border-gray-100">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="block relative overflow-hidden aspect-[16/10]">
                                    <?php the_post_thumbnail('writgo-card', array('class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500')); ?>
                                    <span class="absolute top-3 left-3 px-2.5 py-1 text-xs font-bold bg-amber-400 text-amber-900 rounded-lg"><?php writgo_te('top_list'); ?></span>
                                </a>
                            <?php endif; ?>
                            <div class="p-5">
                                <h3 class="font-bold text-gray-900 group-hover:text-blue-700 transition-colors line-clamp-2 leading-snug">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <time class="text-xs text-gray-400 mt-2 block" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('j M Y'); ?></time>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="text-center py-16 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                    <svg class="mx-auto text-gray-300 mb-4" width="48" height="48" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    <p class="text-gray-400"><?php echo esc_html(writgo_t('best_lists') . ' ' . strtolower(writgo_t('coming_soon'))); ?></p>
                    <p class="text-sm text-gray-300 mt-1"><?php echo esc_html(sprintf(writgo_t('add_tag_hint'), $toplists_tag)); ?></p>
                </div>
            <?php endif; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
