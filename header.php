<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-gray-50 text-gray-800 antialiased'); ?>>
<?php wp_body_open(); ?>
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:bg-white focus:px-4 focus:py-2 focus:rounded-lg focus:shadow-lg focus:text-blue-700 focus:font-semibold"><?php writgo_te('skip_to_content'); ?></a>

<!-- Reading Progress Bar -->
<div class="fixed top-0 left-0 w-full h-[3px] z-[60] bg-transparent pointer-events-none" id="reading-progress-bar" aria-hidden="true">
    <div class="wa-reading-progress-fill h-full bg-gradient-to-r from-blue-600 to-indigo-600 w-0 transition-[width] duration-100"></div>
</div>

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 transition-shadow duration-300" id="site-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-[72px]">

            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="flex-shrink-0 flex items-center" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                <?php
                $custom_logo_id = get_theme_mod('custom_logo');
                if ($custom_logo_id) {
                    echo wp_get_attachment_image($custom_logo_id, 'full', false, array(
                        'class' => 'h-8 lg:h-10 w-auto',
                        'loading' => 'eager',
                    ));
                } else {
                    echo '<span class="text-xl font-bold bg-gradient-to-r from-blue-700 to-indigo-600 bg-clip-text text-transparent">' . esc_html(get_bloginfo('name')) . '</span>';
                }
                ?>
            </a>

            <!-- Navigation -->
            <?php if (has_nav_menu('primary')) : ?>
                <nav class="hidden lg:flex items-center space-x-1" id="site-nav" role="navigation" aria-label="<?php echo esc_attr(writgo_t('navigation')); ?>">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'flex items-center space-x-1',
                        'depth'          => 2,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </nav>
            <?php endif; ?>

            <!-- Header Actions -->
            <div class="flex items-center gap-2">
                <!-- Search Toggle -->
                <button class="p-2 rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors wa-search-toggle" aria-label="<?php echo esc_attr(writgo_t('search')); ?>" aria-expanded="false">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                    </svg>
                </button>

                <!-- Mobile Menu Toggle -->
                <button class="lg:hidden p-2 rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors wa-menu-toggle" aria-label="Menu" aria-expanded="false" aria-controls="mobile-menu">
                    <svg class="wa-icon-menu" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M3 6h18M3 18h18"/>
                    </svg>
                    <svg class="wa-icon-close hidden" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="lg:hidden hidden bg-white border-t border-gray-100 shadow-lg" id="mobile-menu">
        <?php if (has_nav_menu('primary')) : ?>
            <nav class="px-4 py-4 space-y-1">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'space-y-1',
                    'depth'          => 2,
                    'fallback_cb'    => false,
                ));
                ?>
            </nav>
        <?php endif; ?>
    </div>
</header>

<!-- Search Overlay -->
<div class="fixed inset-0 z-[70] bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4 wa-search-overlay" id="search-overlay" aria-hidden="true">
    <div class="w-full max-w-2xl">
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="relative">
            <input type="search" class="w-full h-16 px-6 pr-14 text-lg bg-white rounded-2xl shadow-2xl border-0 focus:ring-4 focus:ring-blue-500/20 outline-none placeholder-gray-400" placeholder="<?php echo esc_attr(writgo_t('search')); ?>..." name="s" autocomplete="off" />
            <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-blue-600 transition-colors">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                </svg>
            </button>
        </form>
        <p class="text-center text-white/60 text-sm mt-4"><?php writgo_te('close'); ?>: ESC</p>
    </div>
</div>
