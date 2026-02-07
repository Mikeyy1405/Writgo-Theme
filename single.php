<?php
/**
 * Single Post Template
 *
 * @package Writgo_Affiliate
 */

get_header();

while (have_posts()) : the_post();
    $categories = get_the_category();
    $reading_time = writgo_get_reading_time();
?>

<article id="main-content" <?php post_class(); ?>>

    <!-- Hero Section -->
    <?php
    $hero_style = '';
    $hero_text_color = get_theme_mod('writgo_article_hero_text_color', '#ffffff');
    ?>
    <header class="relative overflow-hidden bg-gray-900 <?php echo has_post_thumbnail() ? 'min-h-[400px] lg:min-h-[500px]' : 'py-16 lg:py-24'; ?>">
        <?php if (has_post_thumbnail()) : ?>
            <div class="absolute inset-0">
                <?php the_post_thumbnail('writgo-hero', array('class' => 'w-full h-full object-cover')); ?>
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-gray-900/30"></div>
            </div>
        <?php else : ?>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-indigo-900 to-purple-900"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.08),transparent_50%)]"></div>
        <?php endif; ?>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 flex items-end min-h-[400px] lg:min-h-[500px]">
            <div class="w-full max-w-3xl">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-sm text-white/60 mb-5 flex-wrap" aria-label="Breadcrumbs">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-white transition-colors"><?php writgo_te('home'); ?></a>
                    <span class="text-white/30">/</span>
                    <?php if (!empty($categories)) : ?>
                        <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="hover:text-white transition-colors"><?php echo esc_html($categories[0]->name); ?></a>
                        <span class="text-white/30">/</span>
                    <?php endif; ?>
                    <span class="text-white/80 truncate"><?php the_title(); ?></span>
                </nav>

                <!-- Category -->
                <?php if (!empty($categories)) : ?>
                    <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="inline-block px-3 py-1 text-xs font-semibold bg-blue-500/90 text-white rounded-full mb-4 hover:bg-blue-500 transition-colors">
                        <?php echo esc_html($categories[0]->name); ?>
                    </a>
                <?php endif; ?>

                <!-- Title -->
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4 tracking-tight"><?php the_title(); ?></h1>

                <?php if (has_excerpt()) : ?>
                    <p class="text-lg text-white/80 mb-6 leading-relaxed max-w-2xl"><?php echo get_the_excerpt(); ?></p>
                <?php endif; ?>

                <!-- Author & Meta -->
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <div class="flex items-center gap-3">
                        <?php echo get_avatar(get_the_author_meta('ID'), 44, '', '', array('class' => 'w-10 h-10 rounded-full ring-2 ring-white/30')); ?>
                        <div>
                            <span class="block text-white font-medium"><?php the_author(); ?></span>
                            <time class="text-white/60" datetime="<?php echo get_the_date('c'); ?>">
                                <?php echo get_the_date('j F Y'); ?>
                                <?php if (get_the_modified_date() !== get_the_date()) : ?>
                                    &middot; <?php writgo_te('updated'); ?> <?php echo get_the_modified_date('j F Y'); ?>
                                <?php endif; ?>
                            </time>
                        </div>
                    </div>

                    <?php if ($reading_time) : ?>
                        <span class="flex items-center gap-1.5 text-white/60 bg-white/10 px-3 py-1 rounded-full">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                            </svg>
                            <?php echo esc_html(writgo_t('minutes_read', $reading_time)); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Content Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-10 lg:gap-14">

                <!-- Main Content -->
                <main class="min-w-0">

                    <!-- Mobile TOC -->
                    <div class="lg:hidden mb-8 bg-gray-50 rounded-xl border border-gray-200" id="toc-mobile">
                        <button class="w-full flex items-center justify-between p-4 text-sm font-semibold text-gray-700 wa-toc-toggle" aria-expanded="false">
                            <span class="flex items-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6h16M4 12h16M4 18h10"/>
                                </svg>
                                <?php writgo_te('table_of_contents'); ?>
                            </span>
                            <svg class="wa-toc-chevron w-5 h-5 text-gray-400 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                        <nav class="hidden px-4 pb-4" id="toc-mobile-list"></nav>
                    </div>

                    <!-- Affiliate Disclosure -->
                    <?php if (get_theme_mod('writgo_show_disclosure', true)) : ?>
                        <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl border border-blue-100 mb-8 text-sm text-blue-800">
                            <svg class="flex-shrink-0 mt-0.5 text-blue-500" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>
                            </svg>
                            <span><?php echo wp_kses_post(writgo_get_mod('writgo_disclosure_text', 'affiliate_disclosure', 'Dit artikel kan affiliate links bevatten. Bij aankoop via deze links ontvangen wij een commissie.')); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- The Content -->
                    <div class="wa-prose" id="article-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Tags -->
                    <?php if (has_tag()) : ?>
                        <div class="flex flex-wrap items-center gap-2 mt-10 pt-8 border-t border-gray-100">
                            <span class="text-sm font-medium text-gray-500"><?php writgo_te('tags'); ?>:</span>
                            <?php
                            $tags = get_the_tags();
                            if ($tags) :
                                foreach ($tags as $tag) :
                            ?>
                                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <?php echo esc_html($tag->name); ?>
                                </a>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Author Box -->
                    <?php echo writgo_author_box(); ?>

                </main>

                <!-- Sidebar TOC -->
                <aside class="hidden lg:block" id="sidebar-toc">
                    <div class="sticky top-24">
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
                            <h3 class="flex items-center gap-2 text-sm font-bold text-gray-900 mb-4">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6h16M4 12h16M4 18h10"/>
                                </svg>
                                <?php writgo_te('table_of_contents'); ?>
                            </h3>
                            <nav id="toc-sidebar-list"></nav>

                            <!-- Progress -->
                            <div class="mt-4 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-[width] duration-100" id="reading-progress" style="width:0%"></div>
                            </div>
                        </div>

                        <?php if (is_active_sidebar('below-toc')) : ?>
                        <div class="mt-6">
                            <?php dynamic_sidebar('below-toc'); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </aside>

            </div>
        </div>
    </div>

</article>

<!-- Related Posts -->
<section class="py-12 lg:py-16 bg-gray-50 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-8"><?php writgo_te('related_articles'); ?></h2>
        <?php writgo_related_posts(3); ?>
    </div>
</section>

<?php
endwhile;
get_footer();
?>
