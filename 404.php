<?php
/**
 * 404 Page Template
 *
 * @package Writgo_Affiliate
 */

get_header();
?>

<main id="main-content" class="flex items-center justify-center min-h-[60vh] py-16">
    <div class="text-center max-w-lg mx-auto px-4">

        <div class="text-8xl lg:text-9xl font-extrabold bg-gradient-to-br from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-4">404</div>

        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3"><?php writgo_te('page_not_found'); ?></h1>

        <p class="text-gray-500 mb-8 leading-relaxed"><?php writgo_te('error_404'); ?></p>

        <!-- Search Form -->
        <div class="max-w-md mx-auto mb-6">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="relative">
                    <input type="search" class="w-full h-12 pl-5 pr-12 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm" placeholder="<?php echo esc_attr(writgo_t('search')); ?>" name="s" />
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-colors shadow-lg shadow-blue-600/25">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            <?php writgo_te('back_to_home'); ?>
        </a>

    </div>
</main>

<?php get_footer(); ?>
