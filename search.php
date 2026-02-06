<?php
/**
 * Search Results Template
 *
 * @package Writgo_Affiliate
 */

get_header();
?>

<main id="main-content" class="wa-archive">
    
    <header class="wa-archive-header">
        <div class="wa-container">
            <h1 class="wa-archive-title">
                <?php writgo_te('search_results'); ?>: "<?php echo get_search_query(); ?>"
            </h1>
            <p class="wa-archive-description">
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
        </div>
    </header>
    
    <div class="wa-archive-content">
        <div class="wa-container-wide">
            
            <!-- Search Form -->
            <div style="max-width: 600px; margin: 0 auto var(--wa-space-10);">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="wa-search-wrapper">
                        <input type="search" 
                               class="wa-search-input" 
                               placeholder="<?php echo esc_attr(writgo_t('search')); ?>" 
                               value="<?php echo get_search_query(); ?>" 
                               name="s" />
                        <button type="submit" class="wa-search-button">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="M21 21l-4.35-4.35"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if (have_posts()) : ?>
                <div class="wa-posts-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="wa-post-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="wa-card-image-link">
                                    <?php the_post_thumbnail('writgo-card', array('class' => 'wa-card-image')); ?>
                                </a>
                            <?php endif; ?>
                            
                            <div class="wa-card-content">
                                <h2 class="wa-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <time class="wa-card-date" datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date('j M Y'); ?>
                                </time>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                
                <nav class="wa-pagination">
                    <?php
                    echo paginate_links(array(
                        'prev_text' => '&laquo; ' . writgo_t('previous'),
                        'next_text' => writgo_t('next') . ' &raquo;',
                    ));
                    ?>
                </nav>
                
            <?php else : ?>
                <div class="text-center" style="padding: var(--wa-space-10);">
                    <p><?php writgo_te('no_results'); ?></p>
                    <p><a href="<?php echo esc_url(home_url('/')); ?>"><?php writgo_te('back_to_home'); ?></a></p>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
    
</main>

<?php get_footer(); ?>
