<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a href="#main-content" class="wa-skip-link"><?php writgo_te('skip_to_content'); ?></a>

<!-- Reading Progress Bar -->
<div class="wa-reading-progress" id="reading-progress-bar" aria-hidden="true">
    <div class="wa-reading-progress-fill"></div>
</div>

<header class="wa-header" id="site-header">
    <div class="wa-container-wide">
        <div class="wa-header-inner">

            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="wa-logo" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                <?php
                $custom_logo_id = get_theme_mod('custom_logo');
                if ($custom_logo_id) {
                    echo wp_get_attachment_image($custom_logo_id, 'full', false, array(
                        'class' => 'custom-logo',
                        'loading' => 'eager',
                    ));
                } else {
                    echo '<span class="wa-logo-text">' . esc_html(get_bloginfo('name')) . '</span>';
                }
                ?>
            </a>

            <!-- Navigation -->
            <?php if (has_nav_menu('primary')) : ?>
                <nav class="wa-nav" id="site-nav" role="navigation" aria-label="<?php echo esc_attr(writgo_t('navigation')); ?>">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'depth'          => 2,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </nav>
            <?php endif; ?>

            <!-- Header Actions -->
            <div class="wa-header-actions">
                <!-- Search Toggle -->
                <button class="wa-search-toggle" aria-label="<?php echo esc_attr(writgo_t('search')); ?>" aria-expanded="false">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                    </svg>
                </button>

                <!-- Mobile Menu Toggle -->
                <button class="wa-menu-toggle" aria-label="Menu" aria-expanded="false" aria-controls="site-nav">
                    <svg class="wa-icon-menu" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M3 6h18M3 18h18"/>
                    </svg>
                    <svg class="wa-icon-close" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>
</header>

<!-- Search Overlay -->
<div class="wa-search-overlay" id="search-overlay" aria-hidden="true">
    <div class="wa-search-overlay-inner">
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="wa-search-overlay-form">
            <input type="search" class="wa-search-overlay-input" placeholder="<?php echo esc_attr(writgo_t('search')); ?>..." name="s" autocomplete="off" />
            <button type="submit" class="wa-search-overlay-submit">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                </svg>
            </button>
        </form>
        <button class="wa-search-overlay-close" aria-label="<?php echo esc_attr(writgo_t('close')); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
