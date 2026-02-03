<?php
/**
 * Writgo Meta Boxes
 * Multilingual support
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get meta box translations
 */
function writgo_meta_t($key) {
    $lang = writgo_get_language();
    
    $translations = array(
        'writgo_options' => array(
            'nl' => '⚙️ Writgo Opties',
            'en' => '⚙️ Writgo Options',
            'de' => '⚙️ Writgo Optionen',
            'fr' => '⚙️ Options Writgo',
        ),
        'affiliate_settings' => array(
            'nl' => '🛒 Affiliate / CTA Instellingen',
            'en' => '🛒 Affiliate / CTA Settings',
            'de' => '🛒 Affiliate / CTA Einstellungen',
            'fr' => '🛒 Paramètres Affiliation / CTA',
        ),
        'featured_homepage' => array(
            'nl' => 'Uitgelicht op homepage',
            'en' => 'Featured on homepage',
            'de' => 'Auf Startseite hervorgehoben',
            'fr' => 'À la une sur la page d\'accueil',
        ),
        'score' => array(
            'nl' => 'Score (0-10):',
            'en' => 'Score (0-10):',
            'de' => 'Bewertung (0-10):',
            'fr' => 'Score (0-10):',
        ),
        'sticky_cta_title' => array(
            'nl' => '📌 Sticky CTA Bar (onderaan artikel)',
            'en' => '📌 Sticky CTA Bar (bottom of article)',
            'de' => '📌 Sticky CTA Bar (am Ende des Artikels)',
            'fr' => '📌 Barre CTA fixe (bas de l\'article)',
        ),
        'sticky_cta_description' => array(
            'nl' => 'Toon een vaste balk onderaan het scherm met een directe call-to-action. Perfect voor reviews!',
            'en' => 'Show a fixed bar at the bottom of the screen with a direct call-to-action. Perfect for reviews!',
            'de' => 'Zeigen Sie eine feste Leiste am unteren Bildschirmrand mit direktem Call-to-Action. Perfekt für Bewertungen!',
            'fr' => 'Afficher une barre fixe en bas de l\'écran avec un appel à l\'action direct. Parfait pour les avis!',
        ),
        'activate_sticky' => array(
            'nl' => 'Activeer Sticky CTA Bar',
            'en' => 'Activate Sticky CTA Bar',
            'de' => 'Sticky CTA Bar aktivieren',
            'fr' => 'Activer la barre CTA fixe',
        ),
        'product_title' => array(
            'nl' => 'Product/Titel:',
            'en' => 'Product/Title:',
            'de' => 'Produkt/Titel:',
            'fr' => 'Produit/Titre:',
        ),
        'product_placeholder' => array(
            'nl' => 'bijv. Samsung Galaxy S24',
            'en' => 'e.g. Samsung Galaxy S24',
            'de' => 'z.B. Samsung Galaxy S24',
            'fr' => 'ex. Samsung Galaxy S24',
        ),
        'price' => array(
            'nl' => 'Prijs (€):',
            'en' => 'Price (€):',
            'de' => 'Preis (€):',
            'fr' => 'Prix (€):',
        ),
        'price_placeholder' => array(
            'nl' => 'bijv. 899',
            'en' => 'e.g. 899',
            'de' => 'z.B. 899',
            'fr' => 'ex. 899',
        ),
        'affiliate_url' => array(
            'nl' => 'Affiliate URL:',
            'en' => 'Affiliate URL:',
            'de' => 'Affiliate-URL:',
            'fr' => 'URL d\'affiliation:',
        ),
        'button_text' => array(
            'nl' => 'Knop tekst:',
            'en' => 'Button text:',
            'de' => 'Schaltflächentext:',
            'fr' => 'Texte du bouton:',
        ),
        'view_best_price' => array(
            'nl' => 'Bekijk beste prijs →',
            'en' => 'View best price →',
            'de' => 'Besten Preis ansehen →',
            'fr' => 'Voir le meilleur prix →',
        ),
    );
    
    if (isset($translations[$key][$lang])) {
        return $translations[$key][$lang];
    } elseif (isset($translations[$key]['en'])) {
        return $translations[$key]['en'];
    }
    
    return $key;
}

/**
 * Register Meta Boxes
 */
add_action('add_meta_boxes', 'writgo_register_meta_boxes');
function writgo_register_meta_boxes() {
    add_meta_box(
        'writgo_post_options',
        writgo_meta_t('writgo_options'),
        'writgo_post_options_callback',
        'post',
        'side',
        'high'
    );
    
    add_meta_box(
        'writgo_affiliate_options',
        writgo_meta_t('affiliate_settings'),
        'writgo_affiliate_options_callback',
        'post',
        'normal',
        'high'
    );
}

/**
 * Post Options Meta Box Callback
 */
function writgo_post_options_callback($post) {
    wp_nonce_field('writgo_post_options', 'writgo_post_options_nonce');
    
    $featured = get_post_meta($post->ID, '_writgo_featured', true);
    $score = get_post_meta($post->ID, '_writgo_score', true);
    ?>
    
    <p>
        <label>
            <input type="checkbox" 
                   name="writgo_featured" 
                   value="1" 
                   <?php checked($featured, '1'); ?> />
            <?php echo esc_html(writgo_meta_t('featured_homepage')); ?>
        </label>
    </p>
    
    <p>
        <label for="writgo_score"><?php echo esc_html(writgo_meta_t('score')); ?></label><br>
        <input type="number" 
               id="writgo_score" 
               name="writgo_score" 
               value="<?php echo esc_attr($score); ?>" 
               min="0" 
               max="10" 
               step="0.1" 
               style="width: 80px;" />
    </p>
    
    <?php
}

/**
 * Affiliate Options Meta Box Callback
 */
function writgo_affiliate_options_callback($post) {
    wp_nonce_field('writgo_affiliate_options', 'writgo_affiliate_options_nonce');
    
    $sticky_enabled = get_post_meta($post->ID, '_writgo_sticky_cta', true);
    $sticky_title = get_post_meta($post->ID, '_writgo_sticky_title', true);
    $sticky_price = get_post_meta($post->ID, '_writgo_sticky_price', true);
    $sticky_url = get_post_meta($post->ID, '_writgo_sticky_url', true);
    $sticky_button = get_post_meta($post->ID, '_writgo_sticky_button', true) ?: writgo_meta_t('view_best_price');
    ?>
    
    <style>
        .writgo-affiliate-box { padding: 15px; background: #f9fafb; border-radius: 8px; margin-bottom: 15px; }
        .writgo-affiliate-box h4 { margin: 0 0 15px; padding-bottom: 10px; border-bottom: 1px solid #e5e7eb; }
        .writgo-affiliate-row { margin-bottom: 12px; }
        .writgo-affiliate-row label { display: block; font-weight: 600; margin-bottom: 4px; }
        .writgo-affiliate-row input[type="text"],
        .writgo-affiliate-row input[type="url"],
        .writgo-affiliate-row input[type="number"] { width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; }
        .writgo-affiliate-row input:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1); }
        .writgo-tip { font-size: 12px; color: #6b7280; margin-top: 4px; }
    </style>
    
    <div class="writgo-affiliate-box">
        <h4><?php echo esc_html(writgo_meta_t('sticky_cta_title')); ?></h4>
        <p class="writgo-tip"><?php echo esc_html(writgo_meta_t('sticky_cta_description')); ?></p>
        
        <div class="writgo-affiliate-row">
            <label>
                <input type="checkbox" 
                       name="writgo_sticky_cta" 
                       value="1" 
                       <?php checked($sticky_enabled, '1'); ?> />
                <?php echo esc_html(writgo_meta_t('activate_sticky')); ?>
            </label>
        </div>
        
        <div class="writgo-affiliate-row">
            <label for="writgo_sticky_title"><?php echo esc_html(writgo_meta_t('product_title')); ?></label>
            <input type="text" 
                   id="writgo_sticky_title" 
                   name="writgo_sticky_title" 
                   value="<?php echo esc_attr($sticky_title); ?>" 
                   placeholder="<?php echo esc_attr(writgo_meta_t('product_placeholder')); ?>" />
        </div>
        
        <div class="writgo-affiliate-row">
            <label for="writgo_sticky_price"><?php echo esc_html(writgo_meta_t('price')); ?></label>
            <input type="text" 
                   id="writgo_sticky_price" 
                   name="writgo_sticky_price" 
                   value="<?php echo esc_attr($sticky_price); ?>" 
                   placeholder="<?php echo esc_attr(writgo_meta_t('price_placeholder')); ?>" />
        </div>
        
        <div class="writgo-affiliate-row">
            <label for="writgo_sticky_url"><?php echo esc_html(writgo_meta_t('affiliate_url')); ?></label>
            <input type="url" 
                   id="writgo_sticky_url" 
                   name="writgo_sticky_url" 
                   value="<?php echo esc_attr($sticky_url); ?>" 
                   placeholder="https://partner.bol.com/..." />
        </div>
        
        <div class="writgo-affiliate-row">
            <label for="writgo_sticky_button"><?php echo esc_html(writgo_meta_t('button_text')); ?></label>
            <input type="text" 
                   id="writgo_sticky_button" 
                   name="writgo_sticky_button" 
                   value="<?php echo esc_attr($sticky_button); ?>" 
                   placeholder="<?php echo esc_attr(writgo_meta_t('view_best_price')); ?>" />
        </div>
    </div>
    
    <?php
}

/**
 * Save Meta Box Data
 */
add_action('save_post', 'writgo_save_meta_boxes', 10, 1);
function writgo_save_meta_boxes($post_id) {
    // Check if nonce is present (from either meta box)
    $has_valid_nonce = false;
    
    if (isset($_POST['writgo_post_options_nonce']) && 
        wp_verify_nonce($_POST['writgo_post_options_nonce'], 'writgo_post_options')) {
        $has_valid_nonce = true;
    }
    
    if (isset($_POST['writgo_affiliate_options_nonce']) && 
        wp_verify_nonce($_POST['writgo_affiliate_options_nonce'], 'writgo_affiliate_options')) {
        $has_valid_nonce = true;
    }
    
    // Only proceed if we have at least one valid nonce
    if (!$has_valid_nonce) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save featured
    $featured = isset($_POST['writgo_featured']) ? '1' : '';
    update_post_meta($post_id, '_writgo_featured', $featured);
    
    // Save score
    if (isset($_POST['writgo_score']) && $_POST['writgo_score'] !== '') {
        $score = floatval($_POST['writgo_score']);
        $score = max(0, min(10, $score));
        update_post_meta($post_id, '_writgo_score', $score);
    } else {
        delete_post_meta($post_id, '_writgo_score');
    }
    
    // Save Sticky CTA options
    $sticky_enabled = isset($_POST['writgo_sticky_cta']) ? '1' : '';
    update_post_meta($post_id, '_writgo_sticky_cta', $sticky_enabled);
    
    if (isset($_POST['writgo_sticky_title'])) {
        update_post_meta($post_id, '_writgo_sticky_title', sanitize_text_field($_POST['writgo_sticky_title']));
    }
    
    if (isset($_POST['writgo_sticky_price'])) {
        update_post_meta($post_id, '_writgo_sticky_price', sanitize_text_field($_POST['writgo_sticky_price']));
    }
    
    if (isset($_POST['writgo_sticky_url'])) {
        update_post_meta($post_id, '_writgo_sticky_url', esc_url_raw($_POST['writgo_sticky_url']));
    }
    
    if (isset($_POST['writgo_sticky_button'])) {
        update_post_meta($post_id, '_writgo_sticky_button', sanitize_text_field($_POST['writgo_sticky_button']));
    }
}

/**
 * Output Sticky CTA Bar on single posts
 */
add_action('wp_footer', 'writgo_output_sticky_cta');
function writgo_output_sticky_cta() {
    if (!is_singular('post')) {
        return;
    }
    
    $post_id = get_the_ID();
    $sticky_enabled = get_post_meta($post_id, '_writgo_sticky_cta', true);
    
    if ($sticky_enabled !== '1') {
        return;
    }
    
    $title = get_post_meta($post_id, '_writgo_sticky_title', true);
    $price = get_post_meta($post_id, '_writgo_sticky_price', true);
    $url = get_post_meta($post_id, '_writgo_sticky_url', true);
    $button = get_post_meta($post_id, '_writgo_sticky_button', true) ?: writgo_t('view_deal');
    
    if (!$url) {
        return;
    }
    ?>
    <div class="waff-sticky-cta" id="stickyCta">
        <div class="waff-sticky-info">
            <?php if ($title) : ?>
                <span class="waff-sticky-title"><?php echo esc_html($title); ?></span>
            <?php endif; ?>
            <?php if ($price) : ?>
                <span class="waff-sticky-price">€<?php echo esc_html($price); ?></span>
            <?php endif; ?>
        </div>
        <a href="<?php echo esc_url($url); ?>" class="waff-cta-button" rel="nofollow sponsored" target="_blank">
            <?php echo esc_html($button); ?>
        </a>
    </div>
    
    <script>
    (function() {
        var stickyCta = document.getElementById('stickyCta');
        if (!stickyCta) return;
        
        var shown = false;
        var scrollThreshold = 500;
        
        function checkScroll() {
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            var docHeight = document.documentElement.scrollHeight - window.innerHeight;
            var scrollPercent = (scrollTop / docHeight) * 100;
            
            // Show after scrolling 500px or 30% of page
            if ((scrollTop > scrollThreshold || scrollPercent > 30) && !shown) {
                stickyCta.classList.add('visible');
                shown = true;
            }
            
            // Hide when near bottom (footer)
            if (scrollPercent > 95) {
                stickyCta.classList.remove('visible');
            } else if (shown && scrollPercent <= 95 && scrollTop > scrollThreshold) {
                stickyCta.classList.add('visible');
            }
        }
        
        window.addEventListener('scroll', checkScroll);
        checkScroll();
    })();
    </script>
    <?php
}
