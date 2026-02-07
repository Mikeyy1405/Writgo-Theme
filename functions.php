<?php
/**
 * Writgo Affiliate Theme - Functions
 *
 * Clean, lean affiliate marketing & blog theme.
 *
 * @package Writgo_Affiliate
 * @version 10.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// CONSTANTS
// =============================================================================

define('WRITGO_VERSION', '10.0.0');
define('WRITGO_DIR', get_template_directory());
define('WRITGO_URI', get_template_directory_uri());

// =============================================================================
// SANITIZE HELPERS
// =============================================================================

function writgo_sanitize_checkbox($input) {
    return !empty($input);
}

function writgo_sanitize_color($color) {
    if (preg_match('/^#([a-fA-F0-9]{3}){1,2}$/', $color)) {
        return $color;
    }
    return '';
}

function writgo_sanitize_select($input, $setting) {
    $choices = $setting->manager->get_control($setting->id)->choices;
    return array_key_exists($input, $choices) ? $input : $setting->default;
}

function writgo_sanitize_number($input) {
    return absint($input);
}

// =============================================================================
// TRANSLATION SYSTEM
// =============================================================================

/**
 * Get current theme language
 */
function writgo_get_language() {
    return get_theme_mod('writgo_language', 'nl');
}

/**
 * Get translated string
 */
function writgo_t($key, $var = null) {
    $lang = writgo_get_language();

    $t = array(
        // Navigation & General
        'home'              => array('nl' => 'Home', 'en' => 'Home', 'de' => 'Startseite', 'fr' => 'Accueil'),
        'search'            => array('nl' => 'Zoeken...', 'en' => 'Search...', 'de' => 'Suchen...', 'fr' => 'Rechercher...'),
        'read_more'         => array('nl' => 'Lees meer', 'en' => 'Read more', 'de' => 'Weiterlesen', 'fr' => 'Lire la suite'),
        'minutes_read'      => array('nl' => '%d min leestijd', 'en' => '%d min read', 'de' => '%d Min. Lesezeit', 'fr' => '%d min de lecture'),
        'table_of_contents' => array('nl' => 'Inhoudsopgave', 'en' => 'Table of Contents', 'de' => 'Inhaltsverzeichnis', 'fr' => 'Table des matières'),
        'written_by'        => array('nl' => 'Geschreven door', 'en' => 'Written by', 'de' => 'Geschrieben von', 'fr' => 'Écrit par'),
        'updated'           => array('nl' => 'Bijgewerkt op', 'en' => 'Updated on', 'de' => 'Aktualisiert am', 'fr' => 'Mis à jour le'),
        'tags'              => array('nl' => 'Tags', 'en' => 'Tags', 'de' => 'Schlagwörter', 'fr' => 'Étiquettes'),

        // Articles & Archive
        'related_articles'  => array('nl' => 'Gerelateerde artikelen', 'en' => 'Related articles', 'de' => 'Verwandte Artikel', 'fr' => 'Articles similaires'),
        'latest_articles'   => array('nl' => 'Laatste artikelen', 'en' => 'Latest articles', 'de' => 'Neueste Artikel', 'fr' => 'Derniers articles'),
        'blog'              => array('nl' => 'Blog', 'en' => 'Blog', 'de' => 'Blog', 'fr' => 'Blog'),
        'category'          => array('nl' => 'Categorie', 'en' => 'Category', 'de' => 'Kategorie', 'fr' => 'Catégorie'),
        'all_articles'      => array('nl' => 'Alle artikelen', 'en' => 'All articles', 'de' => 'Alle Artikel', 'fr' => 'Tous les articles'),

        // Errors
        'page_not_found'    => array('nl' => 'Pagina niet gevonden', 'en' => 'Page not found', 'de' => 'Seite nicht gefunden', 'fr' => 'Page non trouvée'),
        'error_404'         => array('nl' => 'De pagina die je zoekt bestaat niet.', 'en' => 'The page you are looking for does not exist.', 'de' => 'Die gesuchte Seite existiert nicht.', 'fr' => 'La page que vous cherchez n\'existe pas.'),
        'back_to_home'      => array('nl' => 'Terug naar home', 'en' => 'Back to home', 'de' => 'Zurück zur Startseite', 'fr' => 'Retour à l\'accueil'),

        // Footer & Legal
        'about_us'          => array('nl' => 'Over ons', 'en' => 'About us', 'de' => 'Über uns', 'fr' => 'À propos'),
        'quick_links'       => array('nl' => 'Snelle links', 'en' => 'Quick links', 'de' => 'Schnelllinks', 'fr' => 'Liens rapides'),
        'legal'             => array('nl' => 'Juridisch', 'en' => 'Legal', 'de' => 'Rechtliches', 'fr' => 'Mentions légales'),
        'privacy_policy'    => array('nl' => 'Privacybeleid', 'en' => 'Privacy Policy', 'de' => 'Datenschutz', 'fr' => 'Politique de confidentialité'),
        'disclaimer'        => array('nl' => 'Disclaimer', 'en' => 'Disclaimer', 'de' => 'Haftungsausschluss', 'fr' => 'Avertissement'),
        'cookie_policy'     => array('nl' => 'Cookiebeleid', 'en' => 'Cookie Policy', 'de' => 'Cookie-Richtlinie', 'fr' => 'Politique des cookies'),
        'terms'             => array('nl' => 'Algemene voorwaarden', 'en' => 'Terms & Conditions', 'de' => 'Allgemeine Geschäftsbedingungen', 'fr' => 'Conditions générales'),
        'contact'           => array('nl' => 'Contact', 'en' => 'Contact', 'de' => 'Kontakt', 'fr' => 'Contact'),
        'all_rights_reserved' => array('nl' => 'Alle rechten voorbehouden', 'en' => 'All rights reserved', 'de' => 'Alle Rechte vorbehalten', 'fr' => 'Tous droits réservés'),

        // Affiliate
        'affiliate_disclosure' => array(
            'nl' => 'Dit artikel kan affiliate links bevatten. Bij aankoop via deze links ontvangen wij een commissie.',
            'en' => 'This article may contain affiliate links. We may earn a commission from purchases made through these links.',
            'de' => 'Dieser Artikel kann Affiliate-Links enthalten. Bei einem Kauf über diese Links erhalten wir eine Provision.',
            'fr' => 'Cet article peut contenir des liens affiliés. Nous pouvons recevoir une commission pour les achats effectués via ces liens.',
        ),
        'footer_disclosure' => array(
            'nl' => 'Deze website bevat affiliate links. Bij aankoop ontvangen wij een kleine commissie, zonder extra kosten voor jou.',
            'en' => 'This website contains affiliate links. When you make a purchase, we receive a small commission at no extra cost to you.',
            'de' => 'Diese Website enthält Affiliate-Links. Bei einem Kauf erhalten wir eine kleine Provision, ohne zusätzliche Kosten für Sie.',
            'fr' => 'Ce site contient des liens affiliés. Lors d\'un achat, nous recevons une petite commission sans frais supplémentaires pour vous.',
        ),
        'view_deal'         => array('nl' => 'Bekijk deal', 'en' => 'View deal', 'de' => 'Angebot ansehen', 'fr' => 'Voir l\'offre'),
        'check_price'       => array('nl' => 'Bekijk prijs', 'en' => 'Check price', 'de' => 'Preis prüfen', 'fr' => 'Vérifier le prix'),
        'best_price'        => array('nl' => 'Beste prijs', 'en' => 'Best price', 'de' => 'Bester Preis', 'fr' => 'Meilleur prix'),
        'our_choice'        => array('nl' => 'Onze keuze', 'en' => 'Our choice', 'de' => 'Unsere Wahl', 'fr' => 'Notre choix'),
        'pros'              => array('nl' => 'Voordelen', 'en' => 'Pros', 'de' => 'Vorteile', 'fr' => 'Avantages'),
        'cons'              => array('nl' => 'Nadelen', 'en' => 'Cons', 'de' => 'Nachteile', 'fr' => 'Inconvénients'),

        // Header & Navigation
        'skip_to_content'   => array('nl' => 'Naar inhoud', 'en' => 'Skip to content', 'de' => 'Zum Inhalt', 'fr' => 'Aller au contenu'),
        'navigation'        => array('nl' => 'Hoofdnavigatie', 'en' => 'Main navigation', 'de' => 'Hauptnavigation', 'fr' => 'Navigation principale'),
        'close'             => array('nl' => 'Sluiten', 'en' => 'Close', 'de' => 'Schließen', 'fr' => 'Fermer'),
        'back_to_top'       => array('nl' => 'Terug naar boven', 'en' => 'Back to top', 'de' => 'Nach oben', 'fr' => 'Retour en haut'),

        // Trust Bar
        'trust_safe'        => array('nl' => 'Veilig & Betrouwbaar', 'en' => 'Safe & Secure', 'de' => 'Sicher & Zuverlässig', 'fr' => 'Sûr & Fiable'),
        'trust_independent' => array('nl' => 'Onafhankelijke Reviews', 'en' => 'Independent Reviews', 'de' => 'Unabhängige Bewertungen', 'fr' => 'Avis Indépendants'),
        'trust_gdpr'        => array('nl' => 'AVG Compliant', 'en' => 'GDPR Compliant', 'de' => 'DSGVO Konform', 'fr' => 'Conforme RGPD'),

        // UI Elements
        'share'             => array('nl' => 'Delen', 'en' => 'Share', 'de' => 'Teilen', 'fr' => 'Partager'),
        'previous'          => array('nl' => 'Vorige', 'en' => 'Previous', 'de' => 'Zurück', 'fr' => 'Précédent'),
        'next'              => array('nl' => 'Volgende', 'en' => 'Next', 'de' => 'Weiter', 'fr' => 'Suivant'),
        'load_more'         => array('nl' => 'Meer laden', 'en' => 'Load more', 'de' => 'Mehr laden', 'fr' => 'Charger plus'),
        'popular'           => array('nl' => 'Populair', 'en' => 'Popular', 'de' => 'Beliebt', 'fr' => 'Populaire'),
        'featured'          => array('nl' => 'Uitgelicht', 'en' => 'Featured', 'de' => 'Empfohlen', 'fr' => 'En vedette'),
        'most_read'         => array('nl' => 'Meest gelezen', 'en' => 'Most read', 'de' => 'Meistgelesen', 'fr' => 'Les plus lus'),
        'newsletter'        => array('nl' => 'Nieuwsbrief', 'en' => 'Newsletter', 'de' => 'Newsletter', 'fr' => 'Newsletter'),
        'subscribe'         => array('nl' => 'Aanmelden', 'en' => 'Subscribe', 'de' => 'Abonnieren', 'fr' => 'S\'abonner'),
        'view_all'          => array('nl' => 'Bekijk alles', 'en' => 'View all', 'de' => 'Alle anzeigen', 'fr' => 'Voir tout'),
        'no_results'        => array('nl' => 'Geen resultaten gevonden', 'en' => 'No results found', 'de' => 'Keine Ergebnisse gefunden', 'fr' => 'Aucun résultat trouvé'),
        'search_results'    => array('nl' => 'Zoekresultaten voor', 'en' => 'Search results for', 'de' => 'Suchergebnisse für', 'fr' => 'Résultats de recherche pour'),
        'search_button'     => array('nl' => 'Zoeken', 'en' => 'Search', 'de' => 'Suchen', 'fr' => 'Rechercher'),
        'search_articles'   => array('nl' => 'Zoek artikelen...', 'en' => 'Search articles...', 'de' => 'Artikel suchen...', 'fr' => 'Rechercher des articles...'),
        'blog_description'  => array('nl' => 'Ontdek al onze artikelen', 'en' => 'Discover all our articles', 'de' => 'Entdecken Sie alle unsere Artikel', 'fr' => 'Découvrez tous nos articles'),
        'no_articles_found' => array('nl' => 'Geen artikelen gevonden.', 'en' => 'No articles found.', 'de' => 'Keine Artikel gefunden.', 'fr' => 'Aucun article trouvé.'),
        'no_articles_in_category' => array('nl' => 'Geen artikelen in deze categorie.', 'en' => 'No articles in this category.', 'de' => 'Keine Artikel in dieser Kategorie.', 'fr' => 'Aucun article dans cette catégorie.'),
        'all_articles_loaded' => array('nl' => 'Alle artikelen geladen', 'en' => 'All articles loaded', 'de' => 'Alle Artikel geladen', 'fr' => 'Tous les articles chargés'),
        'error_try_again'   => array('nl' => 'Fout. Probeer opnieuw.', 'en' => 'Error. Please try again.', 'de' => 'Fehler. Bitte versuchen Sie es erneut.', 'fr' => 'Erreur. Veuillez réessayer.'),
        'reviews'           => array('nl' => 'Reviews', 'en' => 'Reviews', 'de' => 'Bewertungen', 'fr' => 'Avis'),
        'all_reviews'       => array('nl' => 'Alle reviews', 'en' => 'All reviews', 'de' => 'Alle Bewertungen', 'fr' => 'Tous les avis'),
        'best_lists'        => array('nl' => 'Beste lijstjes', 'en' => 'Best lists', 'de' => 'Beste Listen', 'fr' => 'Meilleures listes'),
        'all_lists'         => array('nl' => 'Alle lijstjes', 'en' => 'All lists', 'de' => 'Alle Listen', 'fr' => 'Toutes les listes'),
        'top_list'          => array('nl' => 'Top lijst', 'en' => 'Top list', 'de' => 'Top-Liste', 'fr' => 'Top liste'),
        'coming_soon'       => array('nl' => 'Binnenkort', 'en' => 'Coming soon', 'de' => 'Demnächst', 'fr' => 'Bientôt'),
        'what_looking_for'  => array('nl' => 'Waar ben je naar op zoek?', 'en' => 'What are you looking for?', 'de' => 'Was suchen Sie?', 'fr' => 'Que cherchez-vous ?'),
        'your_email'        => array('nl' => 'Je e-mailadres', 'en' => 'Your email', 'de' => 'Ihre E-Mail', 'fr' => 'Votre email'),
        'newsletter_text'   => array('nl' => 'Wekelijks tips en exclusieve aanbiedingen.', 'en' => 'Weekly tips and exclusive offers.', 'de' => 'Wöchentliche Tipps und exklusive Angebote.', 'fr' => 'Conseils hebdomadaires et offres exclusives.'),
        'add_tag_hint'      => array('nl' => 'Voeg de tag "%s" toe aan posts.', 'en' => 'Add the tag "%s" to posts.', 'de' => 'Fügen Sie das Tag "%s" zu Posts hinzu.', 'fr' => 'Ajoutez le tag "%s" aux articles.'),
    );

    if (!isset($t[$key])) {
        return $key;
    }

    $string = $t[$key][$lang] ?? $t[$key]['nl'] ?? $key;

    if ($var !== null) {
        return sprintf($string, $var);
    }

    return $string;
}

/**
 * Echo translated string
 */
function writgo_te($key, $var = null) {
    echo writgo_t($key, $var);
}

/**
 * Get translated theme mod value with fallback to translation
 */
function writgo_get_mod($mod_key, $translation_key, $old_dutch_default = '') {
    $value = get_theme_mod($mod_key, '');

    if (empty($value) || $value === $old_dutch_default) {
        return writgo_t($translation_key);
    }

    return $value;
}

// =============================================================================
// THEME SETUP
// =============================================================================

add_action('after_setup_theme', 'writgo_setup');
function writgo_setup() {
    // Core support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script',
    ));
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');

    // Image sizes
    add_image_size('writgo-hero', 1920, 800, true);
    add_image_size('writgo-card', 400, 250, true);
    add_image_size('writgo-thumb', 150, 150, true);

    // Navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'writgo-affiliate'),
        'footer'  => __('Footer Menu', 'writgo-affiliate'),
    ));
}

// =============================================================================
// WIDGET AREAS
// =============================================================================

add_action('widgets_init', 'writgo_widgets_init');
function writgo_widgets_init() {
    register_sidebar(array(
        'name'          => 'Below TOC (Article Sidebar)',
        'id'            => 'below-toc',
        'description'   => 'Widget area below the Table of Contents on single articles.',
        'before_widget' => '<div id="%1$s" class="wa-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="wa-widget-title">',
        'after_title'   => '</h3>',
    ));
}

// =============================================================================
// ENQUEUE STYLES & SCRIPTS
// =============================================================================

add_action('wp_enqueue_scripts', 'writgo_enqueue_assets');
function writgo_enqueue_assets() {
    // Google Fonts: Inter
    wp_enqueue_style(
        'writgo-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );

    // Main stylesheet
    $css_file = WRITGO_DIR . '/assets/css/main.css';
    $css_ver  = file_exists($css_file) ? filemtime($css_file) : WRITGO_VERSION;
    wp_enqueue_style('writgo-main', WRITGO_URI . '/assets/css/main.css', array(), $css_ver);

    // Main script
    $js_file = WRITGO_DIR . '/assets/js/main.js';
    $js_ver  = file_exists($js_file) ? filemtime($js_file) : WRITGO_VERSION;
    wp_enqueue_script('writgo-main', WRITGO_URI . '/assets/js/main.js', array(), $js_ver, true);

    // TOC script on singular posts only
    if (is_singular('post')) {
        $toc_file = WRITGO_DIR . '/assets/js/toc.js';
        $toc_ver  = file_exists($toc_file) ? filemtime($toc_file) : WRITGO_VERSION;
        wp_enqueue_script('writgo-toc', WRITGO_URI . '/assets/js/toc.js', array(), $toc_ver, true);
    }
}

// =============================================================================
// PERFORMANCE OPTIMIZATIONS
// =============================================================================

/**
 * Remove emoji scripts & styles
 */
add_action('init', 'writgo_disable_emojis');
function writgo_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}

/**
 * Remove jQuery Migrate
 */
add_action('wp_default_scripts', 'writgo_remove_jquery_migrate');
function writgo_remove_jquery_migrate($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            $script->deps = array_diff($script->deps, array('jquery-migrate'));
        }
    }
}

/**
 * Add preconnect for Google Fonts and resource hints
 */
add_filter('wp_resource_hints', 'writgo_resource_hints', 10, 2);
function writgo_resource_hints($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        $urls[] = array(
            'href'        => 'https://fonts.googleapis.com',
            'crossorigin' => '',
        );
        $urls[] = array(
            'href'        => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        );
    }
    return $urls;
}

/**
 * Preload hero image on front page
 */
add_action('wp_head', 'writgo_preload_hero', 1);
function writgo_preload_hero() {
    if (is_front_page()) {
        $hero_bg = get_theme_mod('writgo_hero_bg', '');
        if ($hero_bg) {
            echo '<link rel="preload" as="image" href="' . esc_url($hero_bg) . '">' . "\n";
        }
    }
}

// =============================================================================
// HTML LANG ATTRIBUTE FILTER
// =============================================================================

add_filter('language_attributes', 'writgo_lang_attribute');
function writgo_lang_attribute($output) {
    $lang_map = array(
        'nl' => 'nl-NL',
        'en' => 'en-US',
        'de' => 'de-DE',
        'fr' => 'fr-FR',
    );
    $lang = writgo_get_language();
    $locale = $lang_map[$lang] ?? 'nl-NL';
    return 'lang="' . esc_attr($locale) . '"';
}

// =============================================================================
// YEAR SHORTCODES & FILTER
// =============================================================================

add_shortcode('current_year', 'writgo_current_year_shortcode');
add_shortcode('jaar', 'writgo_current_year_shortcode');
function writgo_current_year_shortcode() {
    return date('Y');
}

add_filter('the_title', 'writgo_replace_year_in_title');
add_filter('single_post_title', 'writgo_replace_year_in_title');
add_filter('the_content', 'writgo_replace_year_in_content');
function writgo_replace_year_in_title($title) {
    $year = date('Y');
    $title = str_replace('[current_year]', $year, $title);
    $title = str_replace('[jaar]', $year, $title);
    return $title;
}
function writgo_replace_year_in_content($content) {
    $year = date('Y');
    $content = str_replace('[current_year]', $year, $content);
    $content = str_replace('[jaar]', $year, $content);
    return $content;
}

// =============================================================================
// READING TIME
// =============================================================================

/**
 * Calculate reading time for current post
 *
 * @param  int|null $post_id  Optional post ID, defaults to current post.
 * @return int      Reading time in minutes (minimum 1).
 */
function writgo_get_reading_time($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $content    = get_post_field('post_content', $post_id);
    $word_count = str_word_count(wp_strip_all_tags($content));
    $minutes    = max(1, (int) ceil($word_count / 250));
    return $minutes;
}

// =============================================================================
// RELATED POSTS
// =============================================================================

/**
 * Get related posts based on shared categories
 *
 * @param  int $count Number of related posts to return.
 * @return WP_Query
 */
function writgo_related_posts($count = 3) {
    $post_id    = get_the_ID();
    $categories = wp_get_post_categories($post_id);

    $args = array(
        'post_type'           => 'post',
        'posts_per_page'      => $count,
        'post__not_in'        => array($post_id),
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
        'orderby'             => 'rand',
    );

    if (!empty($categories)) {
        $args['category__in'] = $categories;
    }

    return new WP_Query($args);
}

// =============================================================================
// AUTHOR BOX
// =============================================================================

/**
 * Output author box using Customizer settings or post author data
 */
function writgo_author_box() {
    $use_custom = get_theme_mod('writgo_author_custom', false);

    if ($use_custom) {
        $name  = get_theme_mod('writgo_author_name', '');
        $bio   = get_theme_mod('writgo_author_bio', '');
        $image = get_theme_mod('writgo_author_image', '');
    } else {
        $author_id = get_the_author_meta('ID');
        $name      = get_the_author();
        $bio       = get_the_author_meta('description');
        $image     = get_avatar_url($author_id, array('size' => 96));
    }

    if (empty($name)) {
        return;
    }

    echo '<div class="wa-author-box">';
    if ($image) {
        echo '<img class="wa-author-avatar" src="' . esc_url($image) . '" alt="' . esc_attr($name) . '" width="96" height="96" loading="lazy">';
    }
    echo '<div class="wa-author-info">';
    echo '<span class="wa-author-label">' . esc_html(writgo_t('written_by')) . '</span>';
    echo '<h4 class="wa-author-name">' . esc_html($name) . '</h4>';
    if ($bio) {
        echo '<p class="wa-author-bio">' . esc_html($bio) . '</p>';
    }
    echo '</div>';
    echo '</div>';
}

// =============================================================================
// BREADCRUMBS
// =============================================================================

/**
 * Output breadcrumbs navigation
 */
function writgo_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    echo '<nav class="wa-breadcrumbs" aria-label="Breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">';

    // Home
    echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    echo '<a itemprop="item" href="' . esc_url(home_url('/')) . '"><span itemprop="name">' . esc_html(writgo_t('home')) . '</span></a>';
    echo '<meta itemprop="position" content="1">';
    echo '</span>';

    $position = 2;

    if (is_single()) {
        $categories = get_the_category();
        if (!empty($categories)) {
            echo '<span class="wa-breadcrumb-sep">&rsaquo;</span>';
            echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a itemprop="item" href="' . esc_url(get_category_link($categories[0]->term_id)) . '"><span itemprop="name">' . esc_html($categories[0]->name) . '</span></a>';
            echo '<meta itemprop="position" content="' . $position . '">';
            echo '</span>';
            $position++;
        }
        echo '<span class="wa-breadcrumb-sep">&rsaquo;</span>';
        echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name" class="wa-breadcrumb-current">' . esc_html(get_the_title()) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</span>';
    } elseif (is_category()) {
        echo '<span class="wa-breadcrumb-sep">&rsaquo;</span>';
        echo '<span class="wa-breadcrumb-current">' . single_cat_title('', false) . '</span>';
    } elseif (is_page()) {
        echo '<span class="wa-breadcrumb-sep">&rsaquo;</span>';
        echo '<span class="wa-breadcrumb-current">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_search()) {
        echo '<span class="wa-breadcrumb-sep">&rsaquo;</span>';
        echo '<span class="wa-breadcrumb-current">' . esc_html(writgo_t('search_results')) . '</span>';
    } elseif (is_archive()) {
        echo '<span class="wa-breadcrumb-sep">&rsaquo;</span>';
        echo '<span class="wa-breadcrumb-current">' . get_the_archive_title() . '</span>';
    }

    echo '</nav>';
}

// =============================================================================
// AFFILIATE LINK BUILDER
// =============================================================================

/**
 * Build a Bol.com partner search link
 *
 * @param  string $search_term  Product search term.
 * @return string               Bol.com affiliate URL.
 */
function writgo_build_bol_link($search_term) {
    $partner_id = get_theme_mod('writgo_bol_partner_id', '');
    $base_url   = 'https://partner.bol.com/click/click?p=1&t=url&s=' . urlencode($partner_id);
    $base_url  .= '&url=' . urlencode('https://www.bol.com/nl/nl/s/?searchtext=' . urlencode($search_term));
    $base_url  .= '&f=SP';
    return $base_url;
}

// =============================================================================
// CUSTOMIZER REGISTRATION
// =============================================================================

add_action('customize_register', 'writgo_customizer');
function writgo_customizer($wp_customize) {

    // -------------------------------------------------------------------------
    // Panel: Writgo Theme
    // -------------------------------------------------------------------------
    $wp_customize->add_panel('writgo_panel', array(
        'title'    => 'Writgo Theme',
        'priority' => 25,
    ));

    // -------------------------------------------------------------------------
    // Section: Language
    // -------------------------------------------------------------------------
    $wp_customize->add_section('writgo_language_section', array(
        'title'    => 'Language / Taal',
        'panel'    => 'writgo_panel',
        'priority' => 1,
    ));

    $wp_customize->add_setting('writgo_language', array(
        'default'           => 'nl',
        'sanitize_callback' => 'writgo_sanitize_select',
    ));
    $wp_customize->add_control('writgo_language', array(
        'label'   => 'Theme Language',
        'section' => 'writgo_language_section',
        'type'    => 'select',
        'choices' => array('nl' => 'Nederlands', 'en' => 'English', 'de' => 'Deutsch', 'fr' => 'Français'),
    ));

    // -------------------------------------------------------------------------
    // Section: Layout
    // -------------------------------------------------------------------------
    $wp_customize->add_section('writgo_layout', array(
        'title' => 'Layout',
        'panel' => 'writgo_panel',
    ));

    $wp_customize->add_setting('writgo_container_width', array('default' => 1200, 'sanitize_callback' => 'writgo_sanitize_number'));
    $wp_customize->add_control('writgo_container_width', array(
        'label'       => 'Container Width (px)',
        'section'     => 'writgo_layout',
        'type'        => 'number',
        'input_attrs' => array('min' => 960, 'max' => 1600, 'step' => 10),
    ));

    $wp_customize->add_setting('writgo_logo_height', array('default' => 40, 'sanitize_callback' => 'writgo_sanitize_number'));
    $wp_customize->add_control('writgo_logo_height', array(
        'label'       => 'Logo Height (px)',
        'section'     => 'writgo_layout',
        'type'        => 'number',
        'input_attrs' => array('min' => 20, 'max' => 120, 'step' => 2),
    ));

    // -------------------------------------------------------------------------
    // Section: Colors
    // -------------------------------------------------------------------------
    $wp_customize->add_section('writgo_colors', array(
        'title' => 'Colors',
        'panel' => 'writgo_panel',
    ));

    $colors = array(
        'writgo_primary_color'    => array('Primary Color',    '#1a56db'),
        'writgo_accent_color'     => array('Accent Color',     '#f97316'),
        'writgo_text_color'       => array('Text Color',       '#1f2937'),
        'writgo_background_color' => array('Background Color', '#ffffff'),
    );

    foreach ($colors as $id => $config) {
        $wp_customize->add_setting($id, array('default' => $config[1], 'sanitize_callback' => 'writgo_sanitize_color'));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $id, array(
            'label'   => $config[0],
            'section' => 'writgo_colors',
        )));
    }

    // -------------------------------------------------------------------------
    // Section: Homepage — Hero
    // -------------------------------------------------------------------------
    $wp_customize->add_section('writgo_homepage_hero', array(
        'title' => 'Homepage: Hero',
        'panel' => 'writgo_panel',
    ));

    $wp_customize->add_setting('writgo_hero_show', array('default' => true, 'sanitize_callback' => 'writgo_sanitize_checkbox'));
    $wp_customize->add_control('writgo_hero_show', array('label' => 'Show Hero Section', 'section' => 'writgo_homepage_hero', 'type' => 'checkbox'));

    $wp_customize->add_setting('writgo_hero_title', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_hero_title', array('label' => 'Hero Title', 'section' => 'writgo_homepage_hero', 'type' => 'text'));

    $wp_customize->add_setting('writgo_hero_subtitle', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_hero_subtitle', array('label' => 'Hero Subtitle', 'section' => 'writgo_homepage_hero', 'type' => 'text'));

    $wp_customize->add_setting('writgo_hero_bg', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'writgo_hero_bg', array(
        'label'   => 'Hero Background Image',
        'section' => 'writgo_homepage_hero',
    )));

    // -------------------------------------------------------------------------
    // Section: Homepage — Sections
    // -------------------------------------------------------------------------
    $wp_customize->add_section('writgo_homepage_sections', array(
        'title' => 'Homepage: Sections',
        'panel' => 'writgo_panel',
    ));

    // Featured
    $wp_customize->add_setting('writgo_featured_show', array('default' => true, 'sanitize_callback' => 'writgo_sanitize_checkbox'));
    $wp_customize->add_control('writgo_featured_show', array('label' => 'Show Featured Section', 'section' => 'writgo_homepage_sections', 'type' => 'checkbox'));

    // Latest
    $wp_customize->add_setting('writgo_latest_show', array('default' => true, 'sanitize_callback' => 'writgo_sanitize_checkbox'));
    $wp_customize->add_control('writgo_latest_show', array('label' => 'Show Latest Articles', 'section' => 'writgo_homepage_sections', 'type' => 'checkbox'));

    $wp_customize->add_setting('writgo_latest_count', array('default' => 6, 'sanitize_callback' => 'writgo_sanitize_number'));
    $wp_customize->add_control('writgo_latest_count', array(
        'label'       => 'Latest Articles Count',
        'section'     => 'writgo_homepage_sections',
        'type'        => 'number',
        'input_attrs' => array('min' => 2, 'max' => 24, 'step' => 1),
    ));

    // Reviews
    $wp_customize->add_setting('writgo_reviews_show', array('default' => false, 'sanitize_callback' => 'writgo_sanitize_checkbox'));
    $wp_customize->add_control('writgo_reviews_show', array('label' => 'Show Reviews Section', 'section' => 'writgo_homepage_sections', 'type' => 'checkbox'));

    $wp_customize->add_setting('writgo_reviews_tag', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_reviews_tag', array('label' => 'Reviews Tag (slug)', 'section' => 'writgo_homepage_sections', 'type' => 'text'));

    $wp_customize->add_setting('writgo_reviews_count', array('default' => 4, 'sanitize_callback' => 'writgo_sanitize_number'));
    $wp_customize->add_control('writgo_reviews_count', array('label' => 'Reviews Count', 'section' => 'writgo_homepage_sections', 'type' => 'number', 'input_attrs' => array('min' => 2, 'max' => 12)));

    // Toplists
    $wp_customize->add_setting('writgo_toplists_show', array('default' => false, 'sanitize_callback' => 'writgo_sanitize_checkbox'));
    $wp_customize->add_control('writgo_toplists_show', array('label' => 'Show Toplists Section', 'section' => 'writgo_homepage_sections', 'type' => 'checkbox'));

    $wp_customize->add_setting('writgo_toplists_tag', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_toplists_tag', array('label' => 'Toplists Tag (slug)', 'section' => 'writgo_homepage_sections', 'type' => 'text'));

    $wp_customize->add_setting('writgo_toplists_count', array('default' => 4, 'sanitize_callback' => 'writgo_sanitize_number'));
    $wp_customize->add_control('writgo_toplists_count', array('label' => 'Toplists Count', 'section' => 'writgo_homepage_sections', 'type' => 'number', 'input_attrs' => array('min' => 2, 'max' => 12)));

    // -------------------------------------------------------------------------
    // Section: Affiliate Settings
    // -------------------------------------------------------------------------
    $wp_customize->add_section('writgo_affiliate', array(
        'title' => 'Affiliate',
        'panel' => 'writgo_panel',
    ));

    $wp_customize->add_setting('writgo_show_disclosure', array('default' => true, 'sanitize_callback' => 'writgo_sanitize_checkbox'));
    $wp_customize->add_control('writgo_show_disclosure', array('label' => 'Show Affiliate Disclosure', 'section' => 'writgo_affiliate', 'type' => 'checkbox'));

    $wp_customize->add_setting('writgo_disclosure_text', array('default' => '', 'sanitize_callback' => 'sanitize_textarea_field'));
    $wp_customize->add_control('writgo_disclosure_text', array('label' => 'Custom Disclosure Text (leave empty for default)', 'section' => 'writgo_affiliate', 'type' => 'textarea'));

    $wp_customize->add_setting('writgo_bol_partner_id', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_bol_partner_id', array('label' => 'Bol.com Partner ID', 'section' => 'writgo_affiliate', 'type' => 'text'));

    // -------------------------------------------------------------------------
    // Section: Company Info
    // -------------------------------------------------------------------------
    $wp_customize->add_section('writgo_company', array(
        'title' => 'Company Info',
        'panel' => 'writgo_panel',
    ));

    $company_fields = array(
        'writgo_company_name'    => 'Company Name',
        'writgo_company_address' => 'Address',
        'writgo_company_postcode'=> 'Postcode',
        'writgo_company_city'    => 'City',
        'writgo_company_email'   => 'Email',
        'writgo_company_phone'   => 'Phone',
        'writgo_company_kvk'     => 'KvK Number',
    );

    foreach ($company_fields as $id => $label) {
        $wp_customize->add_setting($id, array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control($id, array('label' => $label, 'section' => 'writgo_company', 'type' => 'text'));
    }

    // -------------------------------------------------------------------------
    // Section: Social Media
    // -------------------------------------------------------------------------
    $wp_customize->add_section('writgo_social', array(
        'title' => 'Social Media',
        'panel' => 'writgo_panel',
    ));

    $socials = array(
        'writgo_social_facebook'  => 'Facebook URL',
        'writgo_social_instagram' => 'Instagram URL',
        'writgo_social_twitter'   => 'X / Twitter URL',
        'writgo_social_linkedin'  => 'LinkedIn URL',
        'writgo_social_youtube'   => 'YouTube URL',
        'writgo_social_pinterest' => 'Pinterest URL',
    );

    foreach ($socials as $id => $label) {
        $wp_customize->add_setting($id, array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control($id, array('label' => $label, 'section' => 'writgo_social', 'type' => 'url'));
    }

    // -------------------------------------------------------------------------
    // Section: Author Box
    // -------------------------------------------------------------------------
    $wp_customize->add_section('writgo_author', array(
        'title' => 'Author Box',
        'panel' => 'writgo_panel',
    ));

    $wp_customize->add_setting('writgo_author_custom', array('default' => false, 'sanitize_callback' => 'writgo_sanitize_checkbox'));
    $wp_customize->add_control('writgo_author_custom', array('label' => 'Use Custom Author (override post author)', 'section' => 'writgo_author', 'type' => 'checkbox'));

    $wp_customize->add_setting('writgo_author_name', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_author_name', array('label' => 'Author Name', 'section' => 'writgo_author', 'type' => 'text'));

    $wp_customize->add_setting('writgo_author_bio', array('default' => '', 'sanitize_callback' => 'sanitize_textarea_field'));
    $wp_customize->add_control('writgo_author_bio', array('label' => 'Author Bio', 'section' => 'writgo_author', 'type' => 'textarea'));

    $wp_customize->add_setting('writgo_author_image', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'writgo_author_image', array(
        'label'   => 'Author Image',
        'section' => 'writgo_author',
    )));

    // -------------------------------------------------------------------------
    // Section: Analytics & Verification
    // -------------------------------------------------------------------------
    $wp_customize->add_section('writgo_analytics', array(
        'title' => 'Analytics & Verification',
        'panel' => 'writgo_panel',
    ));

    $wp_customize->add_setting('writgo_ga4_id', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_ga4_id', array('label' => 'GA4 Measurement ID (G-XXXXXXXXXX)', 'section' => 'writgo_analytics', 'type' => 'text'));

    $wp_customize->add_setting('writgo_gsc_verification', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_gsc_verification', array('label' => 'Google Search Console Verification', 'section' => 'writgo_analytics', 'type' => 'text'));

    $wp_customize->add_setting('writgo_bing_verification', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_bing_verification', array('label' => 'Bing Webmaster Verification', 'section' => 'writgo_analytics', 'type' => 'text'));

    // -------------------------------------------------------------------------
    // Section: Article Hero
    // -------------------------------------------------------------------------
    $wp_customize->add_section('writgo_article_hero', array(
        'title' => 'Article Hero',
        'panel' => 'writgo_panel',
    ));

    $wp_customize->add_setting('writgo_article_overlay_color', array('default' => '#000000', 'sanitize_callback' => 'writgo_sanitize_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'writgo_article_overlay_color', array(
        'label'   => 'Hero Overlay Color',
        'section' => 'writgo_article_hero',
    )));

    $wp_customize->add_setting('writgo_article_text_color', array('default' => '#ffffff', 'sanitize_callback' => 'writgo_sanitize_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'writgo_article_text_color', array(
        'label'   => 'Hero Text Color',
        'section' => 'writgo_article_hero',
    )));
}

// =============================================================================
// ANALYTICS & VERIFICATION OUTPUT
// =============================================================================

add_action('wp_head', 'writgo_output_analytics', 1);
function writgo_output_analytics() {
    // GA4
    $ga4_id = get_theme_mod('writgo_ga4_id', '');
    if ($ga4_id && preg_match('/^G-[A-Z0-9]+$/', $ga4_id)) {
        echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr($ga4_id) . '"></script>' . "\n";
        echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','" . esc_js($ga4_id) . "');</script>\n";
    }

    // Google Search Console
    $gsc = get_theme_mod('writgo_gsc_verification', '');
    if ($gsc) {
        echo '<meta name="google-site-verification" content="' . esc_attr($gsc) . '">' . "\n";
    }

    // Bing Webmaster
    $bing = get_theme_mod('writgo_bing_verification', '');
    if ($bing) {
        echo '<meta name="msvalidate.01" content="' . esc_attr($bing) . '">' . "\n";
    }
}

// =============================================================================
// DYNAMIC CSS (Customizer Color Variables)
// =============================================================================

add_action('wp_head', 'writgo_output_dynamic_css', 20);
function writgo_output_dynamic_css() {
    $primary    = get_theme_mod('writgo_primary_color', '#1a56db');
    $accent     = get_theme_mod('writgo_accent_color', '#f97316');
    $text       = get_theme_mod('writgo_text_color', '#1f2937');
    $bg         = get_theme_mod('writgo_background_color', '#ffffff');
    $container  = get_theme_mod('writgo_container_width', 1200);
    $logo_h     = get_theme_mod('writgo_logo_height', 40);
    $overlay    = get_theme_mod('writgo_article_overlay_color', '#000000');
    $hero_text  = get_theme_mod('writgo_article_text_color', '#ffffff');

    echo '<style id="writgo-dynamic-css">:root{';
    echo '--wa-primary:' . esc_attr($primary) . ';';
    echo '--wa-accent:' . esc_attr($accent) . ';';
    echo '--wa-text:' . esc_attr($text) . ';';
    echo '--wa-bg:' . esc_attr($bg) . ';';
    echo '--wa-container:' . absint($container) . 'px;';
    echo '--wa-logo-height:' . absint($logo_h) . 'px;';
    echo '--wa-overlay:' . esc_attr($overlay) . ';';
    echo '--wa-hero-text:' . esc_attr($hero_text) . ';';
    echo '}</style>' . "\n";
}

// =============================================================================
// INCLUDE FILES
// =============================================================================

// SEO: meta tags, schema, Open Graph, Twitter Cards
if (file_exists(WRITGO_DIR . '/inc/seo.php')) {
    require_once WRITGO_DIR . '/inc/seo.php';
}

// Theme auto-updater (GitHub-based)
if (file_exists(WRITGO_DIR . '/inc/theme-updater.php')) {
    require_once WRITGO_DIR . '/inc/theme-updater.php';
}

// Affiliate blocks: product box & comparison table shortcodes
if (file_exists(WRITGO_DIR . '/inc/affiliate-blocks.php')) {
    require_once WRITGO_DIR . '/inc/affiliate-blocks.php';
}
