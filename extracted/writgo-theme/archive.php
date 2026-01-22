<?php
/**
 * Archive Template
 *
 * @package Writgo_Affiliate
 */

get_header();

global $wp_query;
$max_pages = $wp_query->max_num_pages;
$current_page = max(1, get_query_var('paged'));
?>

<main class="wa-archive">
    
    <header class="wa-archive-header">
        <div class="wa-container">
            <?php writgo_breadcrumbs(); ?>
            <h1 class="wa-archive-title"><?php the_archive_title(); ?></h1>
            <?php if (get_the_archive_description()) : ?>
                <p class="wa-archive-description"><?php echo get_the_archive_description(); ?></p>
            <?php endif; ?>
        </div>
    </header>
    
    <div class="wa-archive-content">
        <div class="wa-container-wide">
            
            <?php if (have_posts()) : ?>
                <div class="wa-posts-grid" id="posts-container" data-page="<?php echo $current_page; ?>" data-max-pages="<?php echo $max_pages; ?>">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="wa-post-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="wa-card-image-link">
                                    <?php the_post_thumbnail('writgo-card', array('class' => 'wa-card-image')); ?>
                                </a>
                            <?php endif; ?>
                            
                            <div class="wa-card-content">
                                <?php $cats = get_the_category(); if (!empty($cats)) : ?>
                                    <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>" class="wa-card-category">
                                        <?php echo esc_html($cats[0]->name); ?>
                                    </a>
                                <?php endif; ?>
                                
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
                
                <!-- Infinite Scroll Loader -->
                <?php if ($max_pages > 1) : ?>
                    <div class="wa-infinite-scroll">
                        <button class="wa-load-more" id="load-more-btn" data-loading="false">
                            <span class="wa-load-more-text">Meer laden</span>
                            <span class="wa-load-more-spinner">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                                </svg>
                            </span>
                        </button>
                        <p class="wa-load-more-status" id="load-more-status"></p>
                    </div>
                    
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const container = document.getElementById('posts-container');
                        const loadMoreBtn = document.getElementById('load-more-btn');
                        const statusEl = document.getElementById('load-more-status');
                        
                        if (!container || !loadMoreBtn) return;
                        
                        let currentPage = parseInt(container.dataset.page);
                        const maxPages = parseInt(container.dataset.maxPages);
                        let isLoading = false;
                        
                        if (currentPage >= maxPages) {
                            loadMoreBtn.style.display = 'none';
                            return;
                        }
                        
                        loadMoreBtn.addEventListener('click', loadMore);
                        
                        // Auto-load on scroll
                        window.addEventListener('scroll', function() {
                            if (isLoading || currentPage >= maxPages) return;
                            const btnRect = loadMoreBtn.getBoundingClientRect();
                            if (btnRect.top < window.innerHeight + 200) {
                                loadMore();
                            }
                        }, { passive: true });
                        
                        function loadMore() {
                            if (isLoading || currentPage >= maxPages) return;
                            
                            isLoading = true;
                            loadMoreBtn.dataset.loading = 'true';
                            
                            const nextPage = currentPage + 1;
                            const currentUrl = window.location.href.split('/page/')[0].replace(/\/$/, '');
                            const url = currentUrl + '/page/' + nextPage + '/';
                            
                            fetch(url)
                                .then(response => response.text())
                                .then(html => {
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(html, 'text/html');
                                    const newPosts = doc.querySelectorAll('#posts-container .wa-post-card');
                                    
                                    newPosts.forEach(post => {
                                        container.appendChild(post.cloneNode(true));
                                    });
                                    
                                    currentPage = nextPage;
                                    container.dataset.page = currentPage;
                                    
                                    if (currentPage >= maxPages) {
                                        loadMoreBtn.style.display = 'none';
                                        statusEl.textContent = 'Alle artikelen geladen';
                                    }
                                    
                                    isLoading = false;
                                    loadMoreBtn.dataset.loading = 'false';
                                })
                                .catch(error => {
                                    console.error('Error loading posts:', error);
                                    statusEl.textContent = 'Er ging iets mis. Probeer opnieuw.';
                                    isLoading = false;
                                    loadMoreBtn.dataset.loading = 'false';
                                });
                        }
                    });
                    </script>
                <?php endif; ?>
                
                <noscript>
                    <nav class="wa-pagination">
                        <?php echo paginate_links(array('prev_text' => '&laquo;', 'next_text' => '&raquo;')); ?>
                    </nav>
                </noscript>
                
            <?php else : ?>
                <p class="text-center">Geen artikelen gevonden in deze categorie.</p>
            <?php endif; ?>
            
        </div>
    </div>
    
</main>

<?php get_footer(); ?>
