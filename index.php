<?php
/**
 * Blog Index Template
 *
 * @package Writgo_Affiliate
 */

get_header();

// Get total pages for infinite scroll
global $wp_query;
$max_pages = $wp_query->max_num_pages;
$current_page = max(1, get_query_var('paged'));
?>

<main class="wa-archive">
    
    <header class="wa-archive-header">
        <div class="wa-container">
            <h1 class="wa-archive-title">Blog</h1>
            <p class="wa-archive-description"><?php writgo_te('blog_description'); ?></p>
            
            <!-- Blog Search Bar -->
            <div class="wa-blog-search">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="wa-blog-search-form">
                    <div class="wa-blog-search-wrapper">
                        <svg class="wa-blog-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                        </svg>
                        <input type="search" 
                               class="wa-blog-search-input" 
                               placeholder="<?php echo esc_attr(writgo_t('search_articles')); ?>" 
                               value="<?php echo get_search_query(); ?>" 
                               name="s" 
                               id="blog-search-input"
                               autocomplete="off" />
                        <input type="hidden" name="post_type" value="post" />
                        <button type="submit" class="wa-blog-search-button">
                            <?php writgo_te('search_button'); ?>
                        </button>
                    </div>
                    
                    <!-- Live Search Results -->
                    <div class="wa-live-search-results" id="live-search-results"></div>
                </form>
            </div>
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
                                    <span class="wa-card-badge">Blog</span>
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
                        <button class="wa-load-more" id="load-more-btn" data-loading="false" data-all-loaded="<?php echo esc_attr(writgo_t('all_articles_loaded')); ?>" data-error="<?php echo esc_attr(writgo_t('error_try_again')); ?>">
                            <span class="wa-load-more-text"><?php writgo_te('load_more'); ?></span>
                            <span class="wa-load-more-spinner">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                                </svg>
                            </span>
                        </button>
                        <p class="wa-load-more-status" id="load-more-status"></p>
                    </div>
                <?php endif; ?>
                
                <!-- Fallback Pagination (for no-JS) -->
                <noscript>
                    <nav class="wa-pagination">
                        <?php
                        echo paginate_links(array(
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                        ));
                        ?>
                    </nav>
                </noscript>
                
            <?php else : ?>
                <p class="text-center"><?php writgo_te('no_articles_found'); ?></p>
            <?php endif; ?>
            
        </div>
    </div>
    
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('posts-container');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const statusEl = document.getElementById('load-more-status');
    
    if (!container || !loadMoreBtn) return;
    
    let currentPage = parseInt(container.dataset.page);
    const maxPages = parseInt(container.dataset.maxPages);
    let isLoading = false;
    
    // Check if we're already at the last page
    if (currentPage >= maxPages) {
        loadMoreBtn.style.display = 'none';
        return;
    }
    
    loadMoreBtn.addEventListener('click', loadMore);
    
    // Optional: Auto-load on scroll
    let autoLoad = true;
    if (autoLoad) {
        window.addEventListener('scroll', function() {
            if (isLoading || currentPage >= maxPages) return;
            
            const btnRect = loadMoreBtn.getBoundingClientRect();
            if (btnRect.top < window.innerHeight + 200) {
                loadMore();
            }
        }, { passive: true });
    }
    
    function loadMore() {
        if (isLoading || currentPage >= maxPages) return;
        
        isLoading = true;
        loadMoreBtn.dataset.loading = 'true';
        
        const nextPage = currentPage + 1;
        const url = '<?php echo esc_url(home_url('/')); ?>page/' + nextPage + '/';
        
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
                    statusEl.textContent = loadMoreBtn.dataset.allLoaded;
                }
                
                isLoading = false;
                loadMoreBtn.dataset.loading = 'false';
            })
            .catch(error => {
                console.error('Error loading posts:', error);
                statusEl.textContent = loadMoreBtn.dataset.error;
                isLoading = false;
                loadMoreBtn.dataset.loading = 'false';
            });
    }
});
</script>

<?php get_footer(); ?>
