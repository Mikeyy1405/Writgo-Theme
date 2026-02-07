<?php
/**
 * Search Results Template
 *
 * @package Writgo_Affiliate
 */

get_header();
?>

<main id="main-content">

    <header class="bg-white border-b border-gray-200 py-10 lg:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">
                <?php writgo_te('search_results'); ?>: <span class="text-blue-600">"<?php echo get_search_query(); ?>"</span>
            </h1>
            <p class="text-gray-500 mt-2">
                <?php
                global $wp_query;
                $results_text = array(
                    'nl' => $wp_query->found_posts === 1 ? '%d resultaat gevonden' : '%d resultaten gevonden',
                    'en' => $wp_query->found_posts === 1 ? '%d result found' : '%d results found',
                    'de' => $wp_query->found_posts === 1 ? '%d Ergebnis gefunden' : '%d Ergebnisse gefunden',
                    'fr' => $wp_query->found_posts === 1 ? '%d résultat trouvé' : '%d résultats trouvés',
                );
                $lang = writgo_get_language();
                printf($results_text[$lang] ?? $results_text['en'], $wp_query->found_posts);
                ?>
            </p>

            <!-- Search Form -->
            <div class="mt-6 max-w-lg">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="relative">
                        <input type="search" class="w-full h-12 pl-5 pr-12 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="<?php echo esc_attr(writgo_t('search')); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </header>

    <div class="py-10 lg:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <?php if (have_posts()) : ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="group bg-white rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300 border border-gray-100 hover:border-gray-200">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="block relative overflow-hidden aspect-[16/10]">
                                    <?php the_post_thumbnail('writgo-card', array('class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500')); ?>
                                </a>
                            <?php endif; ?>
                            <div class="p-5">
                                <h2 class="font-bold text-gray-900 mb-2 group-hover:text-blue-700 transition-colors line-clamp-2 leading-snug">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <time class="text-xs text-gray-400" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('j M Y'); ?></time>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <nav class="mt-10 flex justify-center">
                    <?php
                    echo paginate_links(array(
                        'prev_text' => '&laquo; ' . writgo_t('previous'),
                        'next_text' => writgo_t('next') . ' &raquo;',
                    ));
                    ?>
                </nav>

            <?php else : ?>
                <div class="text-center py-16">
                    <svg class="mx-auto text-gray-300 mb-4" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                    </svg>
                    <p class="text-gray-500 mb-2"><?php writgo_te('no_results'); ?></p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="text-blue-600 hover:text-blue-700 font-medium"><?php writgo_te('back_to_home'); ?></a>
                </div>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php get_footer(); ?>
