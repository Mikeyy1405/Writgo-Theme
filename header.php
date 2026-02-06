<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a href="#main-content" class="wa-skip-link"><?php esc_html_e('Naar inhoud', 'writgo-affiliate'); ?></a>

<header class="wa-header">
    <div class="wa-container-wide">
        <div class="wa-header-inner">
            
            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="wa-logo">
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
                <nav class="wa-nav" role="navigation" aria-label="<?php esc_attr_e('Hoofdnavigatie', 'writgo-affiliate'); ?>">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'depth'          => 1,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </nav>
            <?php else : ?>
                <nav class="wa-nav">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>">Blog</a>
                </nav>
            <?php endif; ?>
            
            <!-- Header Search -->
            <div class="wa-header-search" role="button" tabindex="0">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
                <span><?php writgo_te('search'); ?></span>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="wa-menu-toggle" aria-label="Menu" aria-expanded="false">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12h18M3 6h18M3 18h18"/>
                </svg>
            </button>
            
        </div>
    </div>
</header>
