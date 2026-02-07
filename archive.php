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

<main id="main-content">

    <header class="bg-white border-b border-gray-200 py-10 lg:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php writgo_breadcrumbs(); ?>
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mt-4 tracking-tight"><?php the_archive_title(); ?></h1>
            <?php if (get_the_archive_description()) : ?>
                <p class="text-lg text-gray-500 mt-2 max-w-2xl"><?php echo get_the_archive_description(); ?></p>
            <?php endif; ?>

            <!-- Search Bar -->
            <div class="mt-6 max-w-lg">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                        </svg>
                        <input type="search" class="w-full h-12 pl-12 pr-24 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="<?php echo esc_attr(writgo_t('search_articles')); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
                        <input type="hidden" name="post_type" value="post" />
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 h-8 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
                            <?php writgo_te('search_button'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </header>

    <div class="py-10 lg:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <?php if (have_posts()) : ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8" id="posts-container" data-page="<?php echo $current_page; ?>" data-max-pages="<?php echo $max_pages; ?>">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="group bg-white rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300 border border-gray-100 hover:border-gray-200">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="block relative overflow-hidden aspect-[16/10]">
                                    <?php the_post_thumbnail('writgo-card', array('class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500')); ?>
                                </a>
                            <?php endif; ?>
                            <div class="p-5">
                                <?php $cats = get_the_category(); if (!empty($cats)) : ?>
                                    <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>" class="inline-block text-xs font-semibold uppercase tracking-wider text-blue-600 hover:text-blue-700 mb-2"><?php echo esc_html($cats[0]->name); ?></a>
                                <?php endif; ?>
                                <h2 class="font-bold text-gray-900 mb-2 group-hover:text-blue-700 transition-colors line-clamp-2 leading-snug">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <time class="text-xs text-gray-400" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('j M Y'); ?></time>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php if ($max_pages > 1) : ?>
                    <div class="mt-12 text-center">
                        <button class="inline-flex items-center gap-2 px-8 py-3 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm" id="load-more-btn" data-loading="false" data-all-loaded="<?php echo esc_attr(writgo_t('all_articles_loaded')); ?>" data-error="<?php echo esc_attr(writgo_t('error_try_again')); ?>">
                            <span class="wa-load-more-text"><?php writgo_te('load_more'); ?></span>
                            <svg class="wa-load-more-spinner hidden animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                        </button>
                        <p class="mt-3 text-sm text-gray-400" id="load-more-status"></p>
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
                        if (currentPage >= maxPages) { loadMoreBtn.style.display = 'none'; return; }
                        loadMoreBtn.addEventListener('click', loadMore);
                        window.addEventListener('scroll', function() {
                            if (isLoading || currentPage >= maxPages) return;
                            const btnRect = loadMoreBtn.getBoundingClientRect();
                            if (btnRect.top < window.innerHeight + 200) loadMore();
                        }, { passive: true });
                        function loadMore() {
                            if (isLoading || currentPage >= maxPages) return;
                            isLoading = true;
                            loadMoreBtn.dataset.loading = 'true';
                            loadMoreBtn.querySelector('.wa-load-more-spinner').classList.remove('hidden');
                            const nextPage = currentPage + 1;
                            const currentUrl = window.location.href.split('/page/')[0].replace(/\/$/, '');
                            fetch(currentUrl + '/page/' + nextPage + '/')
                                .then(r => r.text())
                                .then(html => {
                                    const doc = new DOMParser().parseFromString(html, 'text/html');
                                    doc.querySelectorAll('#posts-container > article').forEach(post => container.appendChild(post.cloneNode(true)));
                                    currentPage = nextPage;
                                    container.dataset.page = currentPage;
                                    if (currentPage >= maxPages) { loadMoreBtn.style.display = 'none'; statusEl.textContent = loadMoreBtn.dataset.allLoaded; }
                                    isLoading = false;
                                    loadMoreBtn.dataset.loading = 'false';
                                    loadMoreBtn.querySelector('.wa-load-more-spinner').classList.add('hidden');
                                })
                                .catch(() => {
                                    statusEl.textContent = loadMoreBtn.dataset.error;
                                    isLoading = false;
                                    loadMoreBtn.dataset.loading = 'false';
                                    loadMoreBtn.querySelector('.wa-load-more-spinner').classList.add('hidden');
                                });
                        }
                    });
                    </script>
                <?php endif; ?>

                <noscript>
                    <nav class="mt-10">
                        <?php echo paginate_links(array('prev_text' => '&laquo;', 'next_text' => '&raquo;')); ?>
                    </nav>
                </noscript>

            <?php else : ?>
                <p class="text-center text-gray-500 py-16"><?php writgo_te('no_articles_in_category'); ?></p>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php get_footer(); ?>
