<?php
/**
 * Writgo Affiliate Theme Functions
 *
 * @package Writgo_Affiliate
 * @version 7.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme Constants
define('WRITGO_VERSION', '7.5.5');
define('WRITGO_DIR', get_template_directory());
define('WRITGO_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
add_action('after_setup_theme', 'writgo_setup');
function writgo_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    
    // Image sizes
    add_image_size('writgo-hero', 1920, 800, true);
    add_image_size('writgo-featured', 800, 450, true);
    add_image_size('writgo-card', 400, 250, true);
    add_image_size('writgo-thumb', 150, 150, true);
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Hoofdmenu', 'writgo-affiliate'),
        'footer'  => __('Footermenu', 'writgo-affiliate'),
    ));
    
    // Load textdomain
    load_theme_textdomain('writgo-affiliate', WRITGO_DIR . '/languages');
}

/**
 * Handle Contact Form Submission
 */
add_action('admin_post_writgo_contact_form', 'writgo_handle_contact_form');
add_action('admin_post_nopriv_writgo_contact_form', 'writgo_handle_contact_form');
function writgo_handle_contact_form() {
    // Verify nonce
    if (!isset($_POST['writgo_contact_nonce']) || !wp_verify_nonce($_POST['writgo_contact_nonce'], 'writgo_contact_form')) {
        wp_die('Beveiligingscontrole mislukt. Probeer het opnieuw.');
    }
    
    // Sanitize input
    $name = sanitize_text_field($_POST['contact_name'] ?? '');
    $email = sanitize_email($_POST['contact_email'] ?? '');
    $subject = sanitize_text_field($_POST['contact_subject'] ?? 'algemeen');
    $message = sanitize_textarea_field($_POST['contact_message'] ?? '');
    $privacy = isset($_POST['contact_privacy']);
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($message) || !$privacy) {
        wp_redirect(add_query_arg('contact', 'error', home_url('/contact/')));
        exit;
    }
    
    // Get admin email
    $to = get_theme_mod('writgo_contact_email', get_option('admin_email'));
    
    // Subject mapping
    $subjects = array(
        'algemeen'     => 'Algemene vraag',
        'samenwerking' => 'Samenwerking',
        'feedback'     => 'Feedback',
        'fout'         => 'Fout melden',
        'anders'       => 'Anders',
    );
    $subject_text = $subjects[$subject] ?? 'Contact';
    
    // Build email
    $email_subject = sprintf('[%s] %s van %s', get_bloginfo('name'), $subject_text, $name);
    
    $email_body = sprintf(
        "Nieuw contactbericht via %s\n\n" .
        "Naam: %s\n" .
        "E-mail: %s\n" .
        "Onderwerp: %s\n\n" .
        "Bericht:\n%s\n\n" .
        "---\n" .
        "Dit bericht is verzonden via het contactformulier op %s",
        get_bloginfo('name'),
        $name,
        $email,
        $subject_text,
        $message,
        home_url()
    );
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );
    
    // Send email
    $sent = wp_mail($to, $email_subject, $email_body, $headers);
    
    if ($sent) {
        wp_redirect(add_query_arg('contact', 'success', home_url('/contact/')));
    } else {
        wp_redirect(add_query_arg('contact', 'error', home_url('/contact/')));
    }
    exit;
}

/**
 * Admin Notice for Missing Pages + Create Pages Button
 */
add_action('admin_notices', 'writgo_admin_notice_missing_pages');
function writgo_admin_notice_missing_pages() {
    // Only show to admins
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Check if we should create pages
    if (isset($_GET['writgo_create_pages']) && $_GET['writgo_create_pages'] === '1') {
        if (wp_verify_nonce($_GET['_wpnonce'], 'writgo_create_pages')) {
            writgo_create_default_pages();
            echo '<div class="notice notice-success is-dismissible"><p><strong>Writgo:</strong> Alle pagina\'s zijn aangemaakt! <a href="' . admin_url('edit.php?post_type=page') . '">Bekijk pagina\'s</a></p></div>';
            return;
        }
    }
    
    // Check which pages are missing
    $required_pages = array(
        'disclaimer'            => 'Disclaimer',
        'privacyverklaring'     => 'Privacyverklaring',
        'cookiebeleid'          => 'Cookiebeleid',
        'algemene-voorwaarden'  => 'Algemene Voorwaarden',
        'over-ons'              => 'Over Ons',
        'contact'               => 'Contact',
    );
    
    $missing = array();
    foreach ($required_pages as $slug => $title) {
        if (!get_page_by_path($slug)) {
            $missing[] = $title;
        }
    }
    
    if (empty($missing)) {
        return;
    }
    
    $create_url = wp_nonce_url(
        admin_url('?writgo_create_pages=1'),
        'writgo_create_pages'
    );
    
    echo '<div class="notice notice-warning">';
    echo '<p><strong>Writgo Theme:</strong> De volgende pagina\'s ontbreken: <em>' . implode(', ', $missing) . '</em></p>';
    echo '<p><a href="' . esc_url($create_url) . '" class="button button-primary">📄 Maak alle pagina\'s aan</a></p>';
    echo '</div>';
}

/**
 * Create Default Pages on Theme Activation
 */
add_action('after_switch_theme', 'writgo_create_default_pages');
function writgo_create_default_pages() {
    
    // Include legal pages content
    require_once WRITGO_DIR . '/inc/legal-pages.php';
    
    // Define pages to create
    $pages = array(
        'over-ons' => array(
            'title'    => 'Over Ons',
            'template' => 'page-about.php',
            'content'  => '',
        ),
        'contact' => array(
            'title'    => 'Contact',
            'template' => 'page-contact.php',
            'content'  => '',
        ),
        'disclaimer' => array(
            'title'    => 'Disclaimer',
            'template' => '',
            'content'  => writgo_get_legal_content('disclaimer'),
        ),
        'privacyverklaring' => array(
            'title'    => 'Privacyverklaring',
            'template' => '',
            'content'  => writgo_get_legal_content('privacyverklaring'),
        ),
        'cookiebeleid' => array(
            'title'    => 'Cookiebeleid',
            'template' => '',
            'content'  => writgo_get_legal_content('cookiebeleid'),
        ),
        'algemene-voorwaarden' => array(
            'title'    => 'Algemene Voorwaarden',
            'template' => '',
            'content'  => writgo_get_legal_content('algemene-voorwaarden'),
        ),
    );
    
    // Create each page if it doesn\'t exist
    foreach ($pages as $slug => $page_data) {
        
        // Check if page with this slug already exists
        $existing_page = get_page_by_path($slug);
        
        if (!$existing_page) {
            // Create the page
            $page_id = wp_insert_post(array(
                'post_title'     => $page_data['title'],
                'post_name'      => $slug,
                'post_content'   => $page_data['content'],
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => get_current_user_id() ?: 1,
                'comment_status' => 'closed',
            ));
            
            // Set page template if specified
            if ($page_id && !is_wp_error($page_id) && !empty($page_data['template'])) {
                update_post_meta($page_id, '_wp_page_template', $page_data['template']);
            }
        }
    }
    
    // Create a primary menu if it does not exist
    $menu_name = 'Hoofdmenu';
    $menu_exists = wp_get_nav_menu_object($menu_name);
    
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);
        
        if (!is_wp_error($menu_id)) {
            // Add Home
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title'   => 'Home',
                'menu-item-url'     => home_url('/'),
                'menu-item-status'  => 'publish',
                'menu-item-type'    => 'custom',
            ));
            
            // Add pages to menu
            $menu_pages = array('Over Ons', 'Contact');
            foreach ($menu_pages as $page_title) {
                $page = get_page_by_title($page_title);
                if ($page) {
                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title'     => $page_title,
                        'menu-item-object'    => 'page',
                        'menu-item-object-id' => $page->ID,
                        'menu-item-type'      => 'post_type',
                        'menu-item-status'    => 'publish',
                    ));
                }
            }
            
            // Assign menu to primary location
            $locations = get_theme_mod('nav_menu_locations', array());
            $locations['primary'] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
    
    // Create footer menu
    $footer_menu_name = 'Footermenu';
    $footer_menu_exists = wp_get_nav_menu_object($footer_menu_name);
    
    if (!$footer_menu_exists) {
        $footer_menu_id = wp_create_nav_menu($footer_menu_name);
        
        if (!is_wp_error($footer_menu_id)) {
            $footer_pages = array(
                'Privacyverklaring' => 'Privacyverklaring',
                'Cookiebeleid' => 'Cookiebeleid',
                'Disclaimer' => 'Disclaimer',
                'Algemene Voorwaarden' => 'Algemene Voorwaarden',
            );
            foreach ($footer_pages as $page_title => $menu_title) {
                $page = get_page_by_title($page_title);
                if ($page) {
                    wp_update_nav_menu_item($footer_menu_id, 0, array(
                        'menu-item-title'     => $menu_title,
                        'menu-item-object'    => 'page',
                        'menu-item-object-id' => $page->ID,
                        'menu-item-type'      => 'post_type',
                        'menu-item-status'    => 'publish',
                    ));
                }
            }
            
            // Assign menu to footer location
            $locations = get_theme_mod('nav_menu_locations', array());
            $locations['footer'] = $footer_menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
    
    // Flush rewrite rules to ensure new pages work
    flush_rewrite_rules();
}

/**
 * Register Widget Areas
 */
add_action('widgets_init', 'writgo_widgets_init');
function writgo_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'writgo-affiliate'),
        'id'            => 'sidebar-1',
        'description'   => __('Voeg widgets toe aan de sidebar.', 'writgo-affiliate'),
        'before_widget' => '<div id="%1$s" class="wa-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="wa-widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer 1', 'writgo-affiliate'),
        'id'            => 'footer-1',
        'before_widget' => '<div id="%1$s" class="wa-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="wa-footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer 2', 'writgo-affiliate'),
        'id'            => 'footer-2',
        'before_widget' => '<div id="%1$s" class="wa-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="wa-footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer 3', 'writgo-affiliate'),
        'id'            => 'footer-3',
        'before_widget' => '<div id="%1$s" class="wa-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="wa-footer-widget-title">',
        'after_title'   => '</h4>',
    ));
}

/**
 * Enqueue Styles and Scripts
 */
add_action('wp_enqueue_scripts', 'writgo_enqueue_assets');
function writgo_enqueue_assets() {
    // Main stylesheet
    wp_enqueue_style(
        'writgo-main',
        WRITGO_URI . '/assets/css/main.css',
        array(),
        WRITGO_VERSION
    );
    
    // Affiliate/Conversion stylesheet
    wp_enqueue_style(
        'writgo-affiliate',
        WRITGO_URI . '/assets/css/affiliate.css',
        array('writgo-main'),
        WRITGO_VERSION
    );
    
    // Google Fonts
    wp_enqueue_style(
        'writgo-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );
    
    // TOC script (single posts only)
    if (is_singular('post')) {
        wp_enqueue_script(
            'writgo-toc',
            WRITGO_URI . '/assets/js/toc.js',
            array(),
            WRITGO_VERSION,
            true
        );
    }
    
    // Main JavaScript
    wp_enqueue_script(
        'writgo-main',
        WRITGO_URI . '/assets/js/main.js',
        array(),
        WRITGO_VERSION,
        true
    );
}

/**
 * Calculate Reading Time
 */
function writgo_get_reading_time($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200);
    
    return max(1, $reading_time);
}

/**
 * Breadcrumbs
 */
function writgo_breadcrumbs() {
    if (is_front_page()) {
        return;
    }
    
    echo '<nav class="wa-breadcrumbs" aria-label="Breadcrumbs">';
    echo '<a href="' . esc_url(home_url('/')) . '">Home</a>';
    
    if (is_single()) {
        $categories = get_the_category();
        if (!empty($categories)) {
            echo '<span class="wa-breadcrumb-sep">›</span>';
            echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
        }
        echo '<span class="wa-breadcrumb-sep">›</span>';
        echo '<span class="wa-breadcrumb-current">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_category()) {
        echo '<span class="wa-breadcrumb-sep">›</span>';
        echo '<span class="wa-breadcrumb-current">' . single_cat_title('', false) . '</span>';
    } elseif (is_page()) {
        echo '<span class="wa-breadcrumb-sep">›</span>';
        echo '<span class="wa-breadcrumb-current">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_search()) {
        echo '<span class="wa-breadcrumb-sep">›</span>';
        echo '<span class="wa-breadcrumb-current">Zoekresultaten</span>';
    } elseif (is_archive()) {
        echo '<span class="wa-breadcrumb-sep">›</span>';
        echo '<span class="wa-breadcrumb-current">' . get_the_archive_title() . '</span>';
    }
    
    echo '</nav>';
}

/**
 * Related Posts
 */
function writgo_related_posts($count = 3) {
    $post_id = get_the_ID();
    $categories = wp_get_post_categories($post_id);
    
    if (empty($categories)) {
        return;
    }
    
    $args = array(
        'category__in'   => $categories,
        'post__not_in'   => array($post_id),
        'posts_per_page' => $count,
        'orderby'        => 'rand',
        'post_status'    => 'publish'
    );
    
    $related = new WP_Query($args);
    
    if ($related->have_posts()) :
    ?>
    <div class="wa-related-grid">
        <?php while ($related->have_posts()) : $related->the_post(); ?>
            <article class="wa-post-card">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" class="wa-card-image-link">
                        <?php the_post_thumbnail('writgo-card', array('class' => 'wa-card-image')); ?>
                    </a>
                <?php endif; ?>
                
                <div class="wa-card-content">
                    <h3 class="wa-card-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <time class="wa-card-date"><?php echo get_the_date('j M Y'); ?></time>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
    <?php
    endif;
    
    wp_reset_postdata();
}

/**
 * Customizer Settings
 */
add_action('customize_register', 'writgo_customizer');
function writgo_customizer($wp_customize) {
    
    // =========================================================================
    // LAYOUT SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_layout', array(
        'title'    => __('Layout Instellingen', 'writgo-affiliate'),
        'priority' => 30,
    ));
    
    // Container Width
    $wp_customize->add_setting('writgo_container_width', array(
        'default'           => 1400,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('writgo_container_width', array(
        'label'       => __('Container Breedte (px)', 'writgo-affiliate'),
        'section'     => 'writgo_layout',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1000,
            'max'  => 1800,
            'step' => 50,
        ),
    ));
    
    // Logo Height
    $wp_customize->add_setting('writgo_logo_height', array(
        'default'           => 50,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('writgo_logo_height', array(
        'label'       => __('Logo Hoogte (px)', 'writgo-affiliate'),
        'section'     => 'title_tagline',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 30,
            'max'  => 100,
            'step' => 5,
        ),
        'priority'    => 9,
    ));
    
    // =========================================================================
    // COLORS SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_colors', array(
        'title'    => __('Thema Kleuren', 'writgo-affiliate'),
        'priority' => 40,
    ));
    
    // Primary Color
    $wp_customize->add_setting('writgo_primary_color', array(
        'default'           => '#1a365d',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'writgo_primary_color', array(
        'label'   => __('Primaire Kleur', 'writgo-affiliate'),
        'section' => 'writgo_colors',
    )));
    
    // Accent Color
    $wp_customize->add_setting('writgo_accent_color', array(
        'default'           => '#f97316',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'writgo_accent_color', array(
        'label'   => __('Accent Kleur', 'writgo-affiliate'),
        'section' => 'writgo_colors',
    )));
    
    // =========================================================================
    // HOMEPAGE SECTIONS
    // =========================================================================
    
    // --- Hero Section ---
    $wp_customize->add_section('writgo_homepage_hero', array(
        'title'    => __('Homepage: Hero', 'writgo-affiliate'),
        'priority' => 50,
        'panel'    => 'writgo_homepage',
    ));
    
    // Create Homepage Panel
    $wp_customize->add_panel('writgo_homepage', array(
        'title'    => __('Homepage Instellingen', 'writgo-affiliate'),
        'priority' => 45,
    ));
    
    // Hero Show
    $wp_customize->add_setting('writgo_hero_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_hero_show', array(
        'label'   => __('Toon Hero sectie', 'writgo-affiliate'),
        'section' => 'writgo_homepage_hero',
        'type'    => 'checkbox',
    ));
    
    // Hero Background Image
    $wp_customize->add_setting('writgo_hero_bg', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'writgo_hero_bg', array(
        'label'   => __('Hero Achtergrond', 'writgo-affiliate'),
        'section' => 'writgo_homepage_hero',
    )));
    
    // Hero Title
    $wp_customize->add_setting('writgo_hero_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_hero_title', array(
        'label'       => __('Titel', 'writgo-affiliate'),
        'description' => __('Laat leeg voor site naam', 'writgo-affiliate'),
        'section'     => 'writgo_homepage_hero',
        'type'        => 'text',
    ));
    
    // Hero Subtitle
    $wp_customize->add_setting('writgo_hero_subtitle', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_hero_subtitle', array(
        'label'       => __('Subtitel', 'writgo-affiliate'),
        'description' => __('Laat leeg voor site tagline', 'writgo-affiliate'),
        'section'     => 'writgo_homepage_hero',
        'type'        => 'textarea',
    ));
    
    // Hero Search Placeholder
    $wp_customize->add_setting('writgo_hero_search_placeholder', array(
        'default'           => 'Waar ben je naar op zoek?',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_hero_search_placeholder', array(
        'label'   => __('Zoek placeholder tekst', 'writgo-affiliate'),
        'section' => 'writgo_homepage_hero',
        'type'    => 'text',
    ));
    
    // Hero Search Button
    $wp_customize->add_setting('writgo_hero_search_button', array(
        'default'           => 'Zoeken',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_hero_search_button', array(
        'label'   => __('Zoek knop tekst', 'writgo-affiliate'),
        'section' => 'writgo_homepage_hero',
        'type'    => 'text',
    ));
    
    // --- Featured Section ---
    $wp_customize->add_section('writgo_homepage_featured', array(
        'title' => __('Homepage: Uitgelicht', 'writgo-affiliate'),
        'panel' => 'writgo_homepage',
    ));
    
    $wp_customize->add_setting('writgo_featured_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_featured_show', array(
        'label'   => __('Toon Uitgelicht sectie', 'writgo-affiliate'),
        'section' => 'writgo_homepage_featured',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_featured_title', array(
        'default'           => 'Uitgelicht',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_featured_title', array(
        'label'   => __('Sectie Label', 'writgo-affiliate'),
        'section' => 'writgo_homepage_featured',
        'type'    => 'text',
    ));
    
    // --- Popular Section ---
    $wp_customize->add_section('writgo_homepage_popular', array(
        'title' => __('Homepage: Meest Gelezen', 'writgo-affiliate'),
        'panel' => 'writgo_homepage',
    ));
    
    $wp_customize->add_setting('writgo_popular_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_popular_show', array(
        'label'   => __('Toon Meest Gelezen', 'writgo-affiliate'),
        'section' => 'writgo_homepage_popular',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_popular_title', array(
        'default'           => 'Meest gelezen',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_popular_title', array(
        'label'   => __('Titel', 'writgo-affiliate'),
        'section' => 'writgo_homepage_popular',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_popular_icon', array(
        'default'           => '🔥',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_popular_icon', array(
        'label'   => __('Icoon (emoji)', 'writgo-affiliate'),
        'section' => 'writgo_homepage_popular',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_popular_count', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('writgo_popular_count', array(
        'label'   => __('Aantal posts', 'writgo-affiliate'),
        'section' => 'writgo_homepage_popular',
        'type'    => 'number',
        'input_attrs' => array('min' => 2, 'max' => 10),
    ));
    
    // --- Newsletter Section ---
    $wp_customize->add_section('writgo_homepage_newsletter', array(
        'title' => __('Homepage: Nieuwsbrief', 'writgo-affiliate'),
        'panel' => 'writgo_homepage',
    ));
    
    $wp_customize->add_setting('writgo_newsletter_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_newsletter_show', array(
        'label'   => __('Toon Nieuwsbrief widget', 'writgo-affiliate'),
        'section' => 'writgo_homepage_newsletter',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_newsletter_title', array(
        'default'           => 'Nieuwsbrief',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_newsletter_title', array(
        'label'   => __('Titel', 'writgo-affiliate'),
        'section' => 'writgo_homepage_newsletter',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_newsletter_text', array(
        'default'           => 'Wekelijks tips, nieuwe reviews en exclusieve aanbiedingen in je inbox.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_newsletter_text', array(
        'label'   => __('Tekst', 'writgo-affiliate'),
        'section' => 'writgo_homepage_newsletter',
        'type'    => 'textarea',
    ));
    
    $wp_customize->add_setting('writgo_newsletter_button', array(
        'default'           => 'Aanmelden',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_newsletter_button', array(
        'label'   => __('Knop tekst', 'writgo-affiliate'),
        'section' => 'writgo_homepage_newsletter',
        'type'    => 'text',
    ));
    
    // --- Latest Articles Section ---
    $wp_customize->add_section('writgo_homepage_latest', array(
        'title' => __('Homepage: Laatste Artikelen', 'writgo-affiliate'),
        'panel' => 'writgo_homepage',
    ));
    
    $wp_customize->add_setting('writgo_latest_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_latest_show', array(
        'label'   => __('Toon Laatste Artikelen', 'writgo-affiliate'),
        'section' => 'writgo_homepage_latest',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_latest_title', array(
        'default'           => 'Laatste artikelen',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_latest_title', array(
        'label'   => __('Titel', 'writgo-affiliate'),
        'section' => 'writgo_homepage_latest',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_latest_count', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('writgo_latest_count', array(
        'label'   => __('Aantal posts', 'writgo-affiliate'),
        'section' => 'writgo_homepage_latest',
        'type'    => 'number',
        'input_attrs' => array('min' => 2, 'max' => 12),
    ));
    
    // --- Reviews Section ---
    $wp_customize->add_section('writgo_homepage_reviews', array(
        'title' => __('Homepage: Reviews', 'writgo-affiliate'),
        'panel' => 'writgo_homepage',
    ));
    
    $wp_customize->add_setting('writgo_reviews_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_reviews_show', array(
        'label'   => __('Toon Reviews sectie', 'writgo-affiliate'),
        'section' => 'writgo_homepage_reviews',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_reviews_title', array(
        'default'           => 'Reviews',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_reviews_title', array(
        'label'   => __('Titel', 'writgo-affiliate'),
        'section' => 'writgo_homepage_reviews',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_reviews_icon', array(
        'default'           => '⭐',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_reviews_icon', array(
        'label'   => __('Icoon (emoji)', 'writgo-affiliate'),
        'section' => 'writgo_homepage_reviews',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_reviews_tag', array(
        'default'           => 'review',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_reviews_tag', array(
        'label'   => __('Filter op tag (slug)', 'writgo-affiliate'),
        'section' => 'writgo_homepage_reviews',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_reviews_count', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('writgo_reviews_count', array(
        'label'   => __('Aantal posts', 'writgo-affiliate'),
        'section' => 'writgo_homepage_reviews',
        'type'    => 'number',
        'input_attrs' => array('min' => 2, 'max' => 8),
    ));
    
    // --- Top Lists Section ---
    $wp_customize->add_section('writgo_homepage_toplists', array(
        'title' => __('Homepage: Beste Lijstjes', 'writgo-affiliate'),
        'panel' => 'writgo_homepage',
    ));
    
    $wp_customize->add_setting('writgo_toplists_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_toplists_show', array(
        'label'   => __('Toon Beste Lijstjes sectie', 'writgo-affiliate'),
        'section' => 'writgo_homepage_toplists',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_toplists_title', array(
        'default'           => 'Beste lijstjes',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_toplists_title', array(
        'label'   => __('Titel', 'writgo-affiliate'),
        'section' => 'writgo_homepage_toplists',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_toplists_icon', array(
        'default'           => '🏆',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_toplists_icon', array(
        'label'   => __('Icoon (emoji)', 'writgo-affiliate'),
        'section' => 'writgo_homepage_toplists',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_toplists_tag', array(
        'default'           => 'beste,top',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_toplists_tag', array(
        'label'   => __('Filter op tags (slugs, komma gescheiden)', 'writgo-affiliate'),
        'section' => 'writgo_homepage_toplists',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_toplists_count', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('writgo_toplists_count', array(
        'label'   => __('Aantal posts', 'writgo-affiliate'),
        'section' => 'writgo_homepage_toplists',
        'type'    => 'number',
        'input_attrs' => array('min' => 2, 'max' => 8),
    ));
    
    // =========================================================================
    // ABOUT PAGE PANEL
    // =========================================================================
    $wp_customize->add_panel('writgo_about', array(
        'title'    => __('Over Ons Pagina', 'writgo-affiliate'),
        'priority' => 46,
    ));
    
    // --- About Hero ---
    $wp_customize->add_section('writgo_about_hero', array(
        'title' => __('Hero Sectie', 'writgo-affiliate'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_hero_title', array(
        'default'           => 'Over Ons',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_hero_title', array(
        'label'   => __('Titel', 'writgo-affiliate'),
        'section' => 'writgo_about_hero',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_hero_subtitle', array(
        'default'           => 'Wij helpen je de beste keuzes te maken',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_hero_subtitle', array(
        'label'   => __('Subtitel', 'writgo-affiliate'),
        'section' => 'writgo_about_hero',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_hero_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'writgo_about_hero_image', array(
        'label'   => __('Achtergrond Afbeelding', 'writgo-affiliate'),
        'section' => 'writgo_about_hero',
    )));
    
    // --- About Intro ---
    $wp_customize->add_section('writgo_about_intro', array(
        'title' => __('Introductie', 'writgo-affiliate'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_intro_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_intro_show', array(
        'label'   => __('Toon introductie', 'writgo-affiliate'),
        'section' => 'writgo_about_intro',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_intro_label', array(
        'default'           => 'Wie zijn wij',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_intro_label', array(
        'label'   => __('Label', 'writgo-affiliate'),
        'section' => 'writgo_about_intro',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_intro_title', array(
        'default'           => 'Onze Missie',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_intro_title', array(
        'label'   => __('Titel', 'writgo-affiliate'),
        'section' => 'writgo_about_intro',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_intro_text', array(
        'default'           => 'Wij geloven dat iedereen toegang verdient tot eerlijke, onafhankelijke informatie. Onze experts testen en vergelijken producten zodat jij de beste keuze kunt maken.',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('writgo_about_intro_text', array(
        'label'   => __('Tekst', 'writgo-affiliate'),
        'section' => 'writgo_about_intro',
        'type'    => 'textarea',
    ));
    
    $wp_customize->add_setting('writgo_about_intro_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'writgo_about_intro_image', array(
        'label'   => __('Afbeelding', 'writgo-affiliate'),
        'section' => 'writgo_about_intro',
    )));
    
    // --- About Story ---
    $wp_customize->add_section('writgo_about_story', array(
        'title' => __('Ons Verhaal', 'writgo-affiliate'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_story_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_story_show', array(
        'label'   => __('Toon ons verhaal', 'writgo-affiliate'),
        'section' => 'writgo_about_story',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_story_label', array(
        'default'           => 'Ons verhaal',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_story_label', array(
        'label'   => __('Label', 'writgo-affiliate'),
        'section' => 'writgo_about_story',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_story_title', array(
        'default'           => 'Hoe het begon',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_story_title', array(
        'label'   => __('Titel', 'writgo-affiliate'),
        'section' => 'writgo_about_story',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_story_text', array(
        'default'           => 'Het begon met een simpele frustratie: het vinden van betrouwbare productinformatie was veel te moeilijk.',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('writgo_about_story_text', array(
        'label'   => __('Tekst', 'writgo-affiliate'),
        'section' => 'writgo_about_story',
        'type'    => 'textarea',
    ));
    
    // --- About Stats ---
    $wp_customize->add_section('writgo_about_stats', array(
        'title' => __('Statistieken', 'writgo-affiliate'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_stats_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_stats_show', array(
        'label'   => __('Toon statistieken', 'writgo-affiliate'),
        'section' => 'writgo_about_stats',
        'type'    => 'checkbox',
    ));
    
    // Stat 1
    $wp_customize->add_setting('writgo_about_stat1_number', array(
        'default'           => '500+',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat1_number', array(
        'label'   => __('Stat 1: Nummer', 'writgo-affiliate'),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    $wp_customize->add_setting('writgo_about_stat1_label', array(
        'default'           => 'Artikelen',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat1_label', array(
        'label'   => __('Stat 1: Label', 'writgo-affiliate'),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    
    // Stat 2
    $wp_customize->add_setting('writgo_about_stat2_number', array(
        'default'           => '100+',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat2_number', array(
        'label'   => __('Stat 2: Nummer', 'writgo-affiliate'),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    $wp_customize->add_setting('writgo_about_stat2_label', array(
        'default'           => 'Reviews',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat2_label', array(
        'label'   => __('Stat 2: Label', 'writgo-affiliate'),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    
    // Stat 3
    $wp_customize->add_setting('writgo_about_stat3_number', array(
        'default'           => '50K+',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat3_number', array(
        'label'   => __('Stat 3: Nummer', 'writgo-affiliate'),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    $wp_customize->add_setting('writgo_about_stat3_label', array(
        'default'           => 'Lezers',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat3_label', array(
        'label'   => __('Stat 3: Label', 'writgo-affiliate'),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    
    // Stat 4
    $wp_customize->add_setting('writgo_about_stat4_number', array(
        'default'           => '5+',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat4_number', array(
        'label'   => __('Stat 4: Nummer', 'writgo-affiliate'),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    $wp_customize->add_setting('writgo_about_stat4_label', array(
        'default'           => 'Jaar ervaring',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat4_label', array(
        'label'   => __('Stat 4: Label', 'writgo-affiliate'),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    
    // --- About Process ---
    $wp_customize->add_section('writgo_about_process', array(
        'title' => __('Werkwijze', 'writgo-affiliate'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_process_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_process_show', array(
        'label'   => __('Toon werkwijze', 'writgo-affiliate'),
        'section' => 'writgo_about_process',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_process_title', array('default' => 'Onze Werkwijze', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_process_subtitle', array('default' => 'Hoe wij reviews maken', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_about_process_title', array('label' => 'Titel', 'section' => 'writgo_about_process', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_process_subtitle', array('label' => 'Subtitel', 'section' => 'writgo_about_process', 'type' => 'text'));
    
    // Process steps
    for ($i = 1; $i <= 4; $i++) {
        $defaults = array(
            1 => array('🔍', 'Onderzoek', 'We beginnen met uitgebreid marktonderzoek.'),
            2 => array('🧪', 'Testen', 'Elk product wordt grondig getest.'),
            3 => array('📊', 'Vergelijken', 'We vergelijken prestaties en prijs-kwaliteit.'),
            4 => array('✍️', 'Publiceren', 'Onze bevindingen worden vertaald naar reviews.'),
        );
        $wp_customize->add_setting("writgo_about_process{$i}_icon", array('default' => $defaults[$i][0], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_process{$i}_title", array('default' => $defaults[$i][1], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_process{$i}_text", array('default' => $defaults[$i][2], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control("writgo_about_process{$i}_icon", array('label' => "Stap {$i}: Icoon", 'section' => 'writgo_about_process', 'type' => 'text'));
        $wp_customize->add_control("writgo_about_process{$i}_title", array('label' => "Stap {$i}: Titel", 'section' => 'writgo_about_process', 'type' => 'text'));
        $wp_customize->add_control("writgo_about_process{$i}_text", array('label' => "Stap {$i}: Tekst", 'section' => 'writgo_about_process', 'type' => 'textarea'));
    }
    
    // --- About Team ---
    $wp_customize->add_section('writgo_about_team', array(
        'title' => __('Team', 'writgo-affiliate'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_team_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_team_show', array(
        'label'   => __('Toon team', 'writgo-affiliate'),
        'section' => 'writgo_about_team',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_team_title', array('default' => 'Ons Team', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_team_subtitle', array('default' => 'De mensen achter de reviews', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_about_team_title', array('label' => 'Titel', 'section' => 'writgo_about_team', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_team_subtitle', array('label' => 'Subtitel', 'section' => 'writgo_about_team', 'type' => 'text'));
    
    // Team members
    for ($i = 1; $i <= 3; $i++) {
        $defaults = array(
            1 => array('Jan de Vries', 'Hoofdredacteur', '10+ jaar ervaring in consumentenonderzoek.'),
            2 => array('Lisa Bakker', 'Tech Expert', 'Gespecialiseerd in elektronica en smart home.'),
            3 => array('Mark Jansen', 'Product Tester', 'Test producten op duurzaamheid en waarde.'),
        );
        $wp_customize->add_setting("writgo_about_team{$i}_name", array('default' => $defaults[$i][0], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_team{$i}_role", array('default' => $defaults[$i][1], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_team{$i}_bio", array('default' => $defaults[$i][2], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_team{$i}_image", array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control("writgo_about_team{$i}_name", array('label' => "Lid {$i}: Naam", 'section' => 'writgo_about_team', 'type' => 'text'));
        $wp_customize->add_control("writgo_about_team{$i}_role", array('label' => "Lid {$i}: Functie", 'section' => 'writgo_about_team', 'type' => 'text'));
        $wp_customize->add_control("writgo_about_team{$i}_bio", array('label' => "Lid {$i}: Bio", 'section' => 'writgo_about_team', 'type' => 'textarea'));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "writgo_about_team{$i}_image", array('label' => "Lid {$i}: Foto", 'section' => 'writgo_about_team')));
    }
    
    // --- About Values ---
    $wp_customize->add_section('writgo_about_values', array(
        'title' => __('Kernwaarden', 'writgo-affiliate'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_values_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_values_show', array(
        'label'   => __('Toon kernwaarden', 'writgo-affiliate'),
        'section' => 'writgo_about_values',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_values_title', array(
        'default'           => 'Onze Kernwaarden',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_values_title', array(
        'label'   => __('Sectie titel', 'writgo-affiliate'),
        'section' => 'writgo_about_values',
        'type'    => 'text',
    ));
    
    // Value 1
    $wp_customize->add_setting('writgo_about_value1_icon', array('default' => '🎯', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value1_title', array('default' => 'Onafhankelijk', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value1_text', array('default' => 'Onze reviews zijn 100% onafhankelijk. Wij laten ons niet beïnvloeden door merken of adverteerders.', 'sanitize_callback' => 'sanitize_text_field'));
    
    $wp_customize->add_control('writgo_about_value1_icon', array('label' => 'Waarde 1: Icoon', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value1_title', array('label' => 'Waarde 1: Titel', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value1_text', array('label' => 'Waarde 1: Tekst', 'section' => 'writgo_about_values', 'type' => 'textarea'));
    
    // Value 2
    $wp_customize->add_setting('writgo_about_value2_icon', array('default' => '✅', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value2_title', array('default' => 'Betrouwbaar', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value2_text', array('default' => 'Alle informatie wordt zorgvuldig gecontroleerd en regelmatig bijgewerkt voor nauwkeurigheid.', 'sanitize_callback' => 'sanitize_text_field'));
    
    $wp_customize->add_control('writgo_about_value2_icon', array('label' => 'Waarde 2: Icoon', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value2_title', array('label' => 'Waarde 2: Titel', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value2_text', array('label' => 'Waarde 2: Tekst', 'section' => 'writgo_about_values', 'type' => 'textarea'));
    
    // Value 3
    $wp_customize->add_setting('writgo_about_value3_icon', array('default' => '💡', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value3_title', array('default' => 'Toegankelijk', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value3_text', array('default' => 'Complexe informatie maken wij begrijpelijk voor iedereen, zonder vakjargon.', 'sanitize_callback' => 'sanitize_text_field'));
    
    $wp_customize->add_control('writgo_about_value3_icon', array('label' => 'Waarde 3: Icoon', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value3_title', array('label' => 'Waarde 3: Titel', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value3_text', array('label' => 'Waarde 3: Tekst', 'section' => 'writgo_about_values', 'type' => 'textarea'));
    
    // --- About FAQ ---
    $wp_customize->add_section('writgo_about_faq', array(
        'title' => __('FAQ', 'writgo-affiliate'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_faq_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_faq_show', array(
        'label'   => __('Toon FAQ', 'writgo-affiliate'),
        'section' => 'writgo_about_faq',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_faq_title', array('default' => 'Veelgestelde Vragen', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_about_faq_title', array('label' => 'Titel', 'section' => 'writgo_about_faq', 'type' => 'text'));
    
    // FAQ items
    $faq_defaults = array(
        1 => array('Hoe verdienen jullie geld?', 'Wij verdienen een kleine commissie wanneer je een product koopt via onze links.'),
        2 => array('Zijn jullie reviews echt onafhankelijk?', 'Absoluut. We kopen zelf producten of lenen ze tijdelijk voor tests.'),
        3 => array('Hoe vaak worden reviews bijgewerkt?', 'We herzien onze reviews minimaal elk kwartaal.'),
    );
    for ($i = 1; $i <= 3; $i++) {
        $wp_customize->add_setting("writgo_about_faq{$i}_q", array('default' => $faq_defaults[$i][0], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_faq{$i}_a", array('default' => $faq_defaults[$i][1], 'sanitize_callback' => 'wp_kses_post'));
        $wp_customize->add_control("writgo_about_faq{$i}_q", array('label' => "Vraag {$i}", 'section' => 'writgo_about_faq', 'type' => 'text'));
        $wp_customize->add_control("writgo_about_faq{$i}_a", array('label' => "Antwoord {$i}", 'section' => 'writgo_about_faq', 'type' => 'textarea'));
    }
    
    // --- About CTA ---
    $wp_customize->add_section('writgo_about_cta', array(
        'title' => __('Call to Action', 'writgo-affiliate'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_cta_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_cta_show', array(
        'label'   => __('Toon CTA sectie', 'writgo-affiliate'),
        'section' => 'writgo_about_cta',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_cta_title', array(
        'default'           => 'Klaar om te beginnen?',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_cta_title', array(
        'label'   => __('Titel', 'writgo-affiliate'),
        'section' => 'writgo_about_cta',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_cta_text', array(
        'default'           => 'Ontdek onze nieuwste reviews en vind het perfecte product voor jou.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_cta_text', array(
        'label'   => __('Tekst', 'writgo-affiliate'),
        'section' => 'writgo_about_cta',
        'type'    => 'textarea',
    ));
    
    $wp_customize->add_setting('writgo_about_cta_button', array(
        'default'           => 'Bekijk Reviews',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_cta_button', array(
        'label'   => __('Knop tekst', 'writgo-affiliate'),
        'section' => 'writgo_about_cta',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_cta_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('writgo_about_cta_url', array(
        'label'   => __('Knop URL', 'writgo-affiliate'),
        'section' => 'writgo_about_cta',
        'type'    => 'url',
    ));
    
    // =========================================================================
    // AFFILIATE SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_affiliate', array(
        'title'    => __('Affiliate Instellingen', 'writgo-affiliate'),
        'priority' => 50,
    ));
    
    // Show Disclosure
    $wp_customize->add_setting('writgo_show_disclosure', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('writgo_show_disclosure', array(
        'label'   => __('Toon affiliate disclosure', 'writgo-affiliate'),
        'section' => 'writgo_affiliate',
        'type'    => 'checkbox',
    ));
    
    // Disclosure Text
    $wp_customize->add_setting('writgo_disclosure_text', array(
        'default'           => 'Dit artikel kan affiliate links bevatten. Bij aankoop via deze links ontvangen wij een commissie.',
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control('writgo_disclosure_text', array(
        'label'   => __('Disclosure Tekst', 'writgo-affiliate'),
        'section' => 'writgo_affiliate',
        'type'    => 'textarea',
    ));
    
    // =========================================================================
    // COMPANY INFO SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_company', array(
        'title'       => __('Bedrijfsgegevens', 'writgo-affiliate'),
        'description' => __('Vul hier je bedrijfsgegevens in. Deze worden automatisch gebruikt in de juridische paginas en footer.', 'writgo-affiliate'),
        'priority'    => 25,
    ));
    
    // Company Name
    $wp_customize->add_setting('writgo_company_name', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_company_name', array(
        'label'       => __('Bedrijfsnaam', 'writgo-affiliate'),
        'description' => __('Wordt gebruikt in juridische documenten. Laat leeg om sitenaam te gebruiken.', 'writgo-affiliate'),
        'section'     => 'writgo_company',
        'type'        => 'text',
    ));
    
    // Company Address
    $wp_customize->add_setting('writgo_company_address', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_company_address', array(
        'label'   => __('Adres', 'writgo-affiliate'),
        'section' => 'writgo_company',
        'type'    => 'text',
    ));
    
    // Postcode
    $wp_customize->add_setting('writgo_company_postcode', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_company_postcode', array(
        'label'   => __('Postcode', 'writgo-affiliate'),
        'section' => 'writgo_company',
        'type'    => 'text',
    ));
    
    // City
    $wp_customize->add_setting('writgo_company_city', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_company_city', array(
        'label'   => __('Plaats', 'writgo-affiliate'),
        'section' => 'writgo_company',
        'type'    => 'text',
    ));
    
    // KvK Number
    $wp_customize->add_setting('writgo_kvk_nummer', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_kvk_nummer', array(
        'label'   => __('KvK Nummer', 'writgo-affiliate'),
        'section' => 'writgo_company',
        'type'    => 'text',
    ));
    
    // Contact Email
    $wp_customize->add_setting('writgo_contact_email', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('writgo_contact_email', array(
        'label'   => __('Contact E-mail', 'writgo-affiliate'),
        'section' => 'writgo_company',
        'type'    => 'email',
    ));
    
    // Contact Phone
    $wp_customize->add_setting('writgo_contact_phone', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_contact_phone', array(
        'label'   => __('Telefoonnummer', 'writgo-affiliate'),
        'section' => 'writgo_company',
        'type'    => 'text',
    ));
    
    // Footer Disclosure
    $wp_customize->add_setting('writgo_footer_disclosure', array(
        'default'           => 'Deze website bevat affiliate links. Bij aankoop ontvangen wij een kleine commissie, zonder extra kosten voor jou.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_footer_disclosure', array(
        'label'   => __('Footer Affiliate Tekst', 'writgo-affiliate'),
        'section' => 'writgo_company',
        'type'    => 'textarea',
    ));
    
    // =========================================================================
    // SOCIAL MEDIA SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_social', array(
        'title'    => __('Social Media', 'writgo-affiliate'),
        'priority' => 26,
    ));
    
    $social_networks = array(
        'facebook'  => 'Facebook URL',
        'instagram' => 'Instagram URL',
        'twitter'   => 'Twitter/X URL',
        'linkedin'  => 'LinkedIn URL',
        'youtube'   => 'YouTube URL',
        'pinterest' => 'Pinterest URL',
        'tiktok'    => 'TikTok URL',
    );
    
    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting('writgo_social_' . $network, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        $wp_customize->add_control('writgo_social_' . $network, array(
            'label'   => __($label, 'writgo-affiliate'),
            'section' => 'writgo_social',
            'type'    => 'url',
        ));
    }
    
    // =========================================================================
    // CONTACT PAGE SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_contact_page', array(
        'title'       => __('Contact Pagina', 'writgo-affiliate'),
        'description' => __('Instellingen voor de contact pagina.', 'writgo-affiliate'),
        'priority'    => 47,
    ));
    
    // Hero Title
    $wp_customize->add_setting('writgo_contact_hero_title', array(
        'default'           => 'Neem Contact Op',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_contact_hero_title', array(
        'label'   => __('Hero Titel', 'writgo-affiliate'),
        'section' => 'writgo_contact_page',
        'type'    => 'text',
    ));
    
    // Hero Subtitle
    $wp_customize->add_setting('writgo_contact_hero_subtitle', array(
        'default'           => 'Heb je een vraag, feedback of wil je samenwerken? We horen graag van je!',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_contact_hero_subtitle', array(
        'label'   => __('Hero Subtitel', 'writgo-affiliate'),
        'section' => 'writgo_contact_page',
        'type'    => 'textarea',
    ));
    
    // Response Time
    $wp_customize->add_setting('writgo_contact_response_time', array(
        'default'           => 'We reageren meestal binnen 24-48 uur',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_contact_response_time', array(
        'label'   => __('Reactietijd tekst', 'writgo-affiliate'),
        'section' => 'writgo_contact_page',
        'type'    => 'text',
    ));
    
    // Contact Form Shortcode
    $wp_customize->add_setting('writgo_contact_form_shortcode', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_contact_form_shortcode', array(
        'label'       => __('Formulier Shortcode', 'writgo-affiliate'),
        'description' => __('Bijv. [contact-form-7 id="123"] of [wpforms id="123"]. Laat leeg voor standaard formulier.', 'writgo-affiliate'),
        'section'     => 'writgo_contact_page',
        'type'        => 'text',
    ));
    
    // FAQ Items
    for ($i = 1; $i <= 3; $i++) {
        $wp_customize->add_setting('writgo_contact_faq' . $i . '_q', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('writgo_contact_faq' . $i . '_q', array(
            'label'   => sprintf(__('FAQ %d - Vraag', 'writgo-affiliate'), $i),
            'section' => 'writgo_contact_page',
            'type'    => 'text',
        ));
        
        $wp_customize->add_setting('writgo_contact_faq' . $i . '_a', array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        ));
        
        $wp_customize->add_control('writgo_contact_faq' . $i . '_a', array(
            'label'   => sprintf(__('FAQ %d - Antwoord', 'writgo-affiliate'), $i),
            'section' => 'writgo_contact_page',
            'type'    => 'textarea',
        ));
    }
}

/**
 * Output Custom CSS Variables
 */
add_action('wp_head', 'writgo_custom_css');
function writgo_custom_css() {
    $container_width = get_theme_mod('writgo_container_width', 1400);
    $primary_color = get_theme_mod('writgo_primary_color', '#1a365d');
    $accent_color = get_theme_mod('writgo_accent_color', '#f97316');
    $logo_height = get_theme_mod('writgo_logo_height', 50);
    
    echo '<style>
        :root {
            --wa-container-max: ' . intval($container_width) . 'px;
            --wa-primary: ' . esc_attr($primary_color) . ';
            --wa-accent: ' . esc_attr($accent_color) . ';
            --wa-logo-height: ' . intval($logo_height) . 'px;
        }
        .wa-logo img,
        .wa-logo .custom-logo {
            height: var(--wa-logo-height) !important;
            width: auto !important;
            max-width: 200px;
        }
    </style>';
}

/**
 * Add nofollow to external links
 */
add_filter('the_content', 'writgo_external_links');
function writgo_external_links($content) {
    $home_url = home_url();
    
    return preg_replace_callback(
        '/<a\s+([^>]*href=["\']([^"\']+)["\'][^>]*)>/i',
        function($matches) use ($home_url) {
            $url = $matches[2];
            $attrs = $matches[1];
            
            // Check if external
            if (strpos($url, $home_url) === false && strpos($url, 'http') === 0) {
                // Add rel="nofollow sponsored" if not already present
                if (strpos($attrs, 'rel=') === false) {
                    return '<a ' . $attrs . ' rel="nofollow sponsored" target="_blank">';
                }
            }
            
            return $matches[0];
        },
        $content
    );
}

/**
 * Set posts per page for archives
 */
add_action('pre_get_posts', 'writgo_posts_per_page');
function writgo_posts_per_page($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_home() || is_archive() || is_search()) {
            $query->set('posts_per_page', 12);
        }
    }
}

/**
 * Include additional modules
 */
$modules = array(
    '/inc/meta-boxes.php',
    '/inc/shortcodes.php',
    '/inc/affiliate-shortcodes.php',
    '/inc/legal-pages.php',
    '/inc/seo.php',
    '/inc/theme-updater.php',
);

foreach ($modules as $module) {
    $file = WRITGO_DIR . $module;
    if (file_exists($file)) {
        require_once $file;
    }
}
