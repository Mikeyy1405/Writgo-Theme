<?php
/**
 * Writgo Affiliate Blocks - Shortcodes voor affiliate marketing
 *
 * Clean, simpele shortcodes voor productaanbevelingen, vergelijkingen,
 * en affiliate links binnen het Writgo Affiliate Theme.
 *
 * @package Writgo_Affiliate
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// 1. [product-box] - Product aanbeveling box
// =============================================================================
//
// Usage: [product-box name="Samsung Galaxy S24" image="url" price="79,99"
//         old_price="99,99" rating="4" url="https://..." button_text="Bekijk deal"
//         badge="Beste koop" store="bol.com"]
//
add_shortcode('product-box', 'writgo_ab_product_box');
function writgo_ab_product_box($atts) {
    $atts = shortcode_atts(array(
        'name'        => '',
        'image'       => '',
        'price'       => '',
        'old_price'   => '',
        'rating'      => '',
        'url'         => '#',
        'button_text' => 'Bekijk deal',
        'badge'       => '',
        'store'       => '',
    ), $atts, 'product-box');

    ob_start();
    ?>
    <div class="wa-product-box">
        <?php if ($atts['badge']) : ?>
            <div class="wa-product-badge"><?php echo esc_html($atts['badge']); ?></div>
        <?php endif; ?>

        <?php if ($atts['image']) : ?>
            <div class="wa-product-image">
                <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['name']); ?>" loading="lazy">
            </div>
        <?php endif; ?>

        <div class="wa-product-info">
            <?php if ($atts['name']) : ?>
                <h3 class="wa-product-name"><?php echo esc_html($atts['name']); ?></h3>
            <?php endif; ?>

            <?php if ($atts['rating']) : ?>
                <div class="wa-product-rating">
                    <?php
                    $rating = max(0, min(5, intval($atts['rating'])));
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            echo '<span class="wa-star wa-star-full">&#9733;</span>';
                        } else {
                            echo '<span class="wa-star wa-star-empty">&#9734;</span>';
                        }
                    }
                    ?>
                </div>
            <?php endif; ?>

            <div class="wa-product-price">
                <?php if ($atts['old_price']) : ?>
                    <span class="wa-price-old">&euro;<?php echo esc_html($atts['old_price']); ?></span>
                <?php endif; ?>
                <?php if ($atts['price']) : ?>
                    <span class="wa-price-current">&euro;<?php echo esc_html($atts['price']); ?></span>
                <?php endif; ?>
            </div>

            <a class="wa-product-button" href="<?php echo esc_url($atts['url']); ?>" target="_blank" rel="nofollow sponsored noopener">
                <?php echo esc_html($atts['button_text']); ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>

            <?php if ($atts['store']) : ?>
                <span class="wa-product-store">Via <?php echo esc_html($atts['store']); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// =============================================================================
// 2. [pros-cons] - Voordelen / Nadelen box
// =============================================================================
//
// Usage:
// [pros-cons title="Voordelen & Nadelen"]
// Snel en krachtig
// Mooi design
// Goede batterij
// ---
// Duur
// Geen koptelefoonaansluiting
// [/pros-cons]
//
add_shortcode('pros-cons', 'writgo_ab_pros_cons');
function writgo_ab_pros_cons($atts, $content = null) {
    $atts = shortcode_atts(array(
        'title' => 'Voordelen & Nadelen',
    ), $atts, 'pros-cons');

    $content = trim($content);
    $parts   = preg_split('/\n\s*---\s*\n/', $content, 2);

    $pros_lines = array();
    $cons_lines = array();

    if (isset($parts[0])) {
        $pros_lines = array_filter(array_map('trim', explode("\n", trim($parts[0]))));
    }
    if (isset($parts[1])) {
        $cons_lines = array_filter(array_map('trim', explode("\n", trim($parts[1]))));
    }

    ob_start();
    ?>
    <div class="wa-proscons">
        <h4><?php echo esc_html($atts['title']); ?></h4>
        <div class="wa-proscons-grid">
            <div class="wa-pros">
                <h5>&#10003; Voordelen</h5>
                <?php if (!empty($pros_lines)) : ?>
                    <ul>
                        <?php foreach ($pros_lines as $pro) : ?>
                            <li><?php echo esc_html($pro); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="wa-cons">
                <h5>&#10007; Nadelen</h5>
                <?php if (!empty($cons_lines)) : ?>
                    <ul>
                        <?php foreach ($cons_lines as $con) : ?>
                            <li><?php echo esc_html($con); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// =============================================================================
// 3. [comparison-table] - Vergelijkingstabel
// =============================================================================
//
// Usage:
// [comparison-table]
// Product | Prijs | Score | Link
// Samsung Galaxy S24 | €899 | 9.2 | https://...
// iPhone 15 | €949 | 9.0 | https://...
// [/comparison-table]
//
add_shortcode('comparison-table', 'writgo_ab_comparison_table');
function writgo_ab_comparison_table($atts, $content = null) {
    $content = trim($content);
    $lines   = array_filter(array_map('trim', explode("\n", $content)));

    if (empty($lines)) {
        return '';
    }

    ob_start();
    ?>
    <div class="wa-comparison-table-wrapper">
        <table class="wa-comparison-table">
            <?php foreach ($lines as $index => $line) :
                $cells = array_map('trim', explode('|', $line));
                if ($index === 0) : ?>
                    <thead>
                        <tr>
                            <?php foreach ($cells as $cell) : ?>
                                <th><?php echo esc_html($cell); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                <?php else : ?>
                    <tr>
                        <?php foreach ($cells as $cell) :
                            // Detecteer of de cel een URL is
                            if (filter_var($cell, FILTER_VALIDATE_URL)) : ?>
                                <td><a class="wa-table-link" href="<?php echo esc_url($cell); ?>" target="_blank" rel="nofollow sponsored noopener">Bekijk</a></td>
                            <?php else : ?>
                                <td><?php echo esc_html($cell); ?></td>
                            <?php endif;
                        endforeach; ?>
                    </tr>
                <?php endif;
            endforeach; ?>
                    </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}

// =============================================================================
// 4. [cta-button] - Call-to-action knop
// =============================================================================
//
// Usage: [cta-button url="https://..." text="Bekijk aanbieding" color="accent"
//         size="large" new_tab="yes"]
//
add_shortcode('cta-button', 'writgo_ab_cta_button');
function writgo_ab_cta_button($atts) {
    $atts = shortcode_atts(array(
        'url'     => '#',
        'text'    => 'Bekijk aanbieding',
        'color'   => 'accent',
        'size'    => 'large',
        'new_tab' => 'yes',
    ), $atts, 'cta-button');

    $size  = in_array($atts['size'], array('small', 'medium', 'large'), true) ? $atts['size'] : 'large';
    $color = sanitize_html_class($atts['color']);

    $target = '';
    $rel    = '';
    if ($atts['new_tab'] === 'yes') {
        $target = ' target="_blank"';
        $rel    = ' rel="nofollow sponsored noopener"';
    }

    return sprintf(
        '<a class="wa-cta-button wa-cta-%s wa-cta-color-%s" href="%s"%s%s>%s</a>',
        esc_attr($size),
        esc_attr($color),
        esc_url($atts['url']),
        $target,
        $rel,
        esc_html($atts['text'])
    );
}

// =============================================================================
// 5. [affiliate-link] - Inline affiliate link
// =============================================================================
//
// Usage: [affiliate-link url="https://..." text="Samsung Galaxy S24" store="bol.com"]
//
add_shortcode('affiliate-link', 'writgo_ab_affiliate_link');
function writgo_ab_affiliate_link($atts) {
    $atts = shortcode_atts(array(
        'url'   => '#',
        'text'  => '',
        'store' => '',
    ), $atts, 'affiliate-link');

    $store_html = '';
    if ($atts['store']) {
        $store_html = ' <small>(via ' . esc_html($atts['store']) . ')</small>';
    }

    return sprintf(
        '<a class="wa-affiliate-link" href="%s" target="_blank" rel="nofollow sponsored noopener">%s%s</a>',
        esc_url($atts['url']),
        esc_html($atts['text']),
        $store_html
    );
}

// =============================================================================
// 6. [bol-link] - Bol.com partner link
// =============================================================================
//
// Usage: [bol-link search="Samsung Galaxy S24" text="Bekijk op bol.com"]
//
// Partner ID: 1105819
//
add_shortcode('bol-link', 'writgo_ab_bol_link');
function writgo_ab_bol_link($atts) {
    $atts = shortcode_atts(array(
        'search' => '',
        'text'   => '',
    ), $atts, 'bol-link');

    if (empty($atts['search'])) {
        return '';
    }

    $button_text = $atts['text'] ? $atts['text'] : $atts['search'] . ' op bol.com';
    $search_url  = 'https://www.bol.com/nl/nl/s/?searchtext=' . rawurlencode($atts['search']);
    $partner_url = 'https://partner.bol.com/click/click?p=1&t=url&s=1105819&url=' . rawurlencode($search_url);

    return sprintf(
        '<a class="wa-affiliate-link wa-bol-link" href="%s" target="_blank" rel="nofollow sponsored noopener">%s <small>(via bol.com)</small></a>',
        esc_url($partner_url),
        esc_html($button_text)
    );
}

// =============================================================================
// 7. [disclosure] - Affiliate disclosure box
// =============================================================================
//
// Usage: [disclosure]
//
add_shortcode('disclosure', 'writgo_ab_disclosure');
function writgo_ab_disclosure($atts) {
    $default_text = 'Dit artikel bevat affiliate links. Als je via deze links een product koopt, ontvangen wij een kleine commissie. Dit kost jou niets extra. Zo kunnen wij deze website gratis blijven aanbieden.';
    $text = get_theme_mod('writgo_disclosure_text', $default_text);

    ob_start();
    ?>
    <div class="wa-disclosure">
        <span class="wa-disclosure-icon" aria-hidden="true">&#9432;</span>
        <p class="wa-disclosure-text"><?php echo esc_html($text); ?></p>
    </div>
    <?php
    return ob_get_clean();
}

// =============================================================================
// 8. [current_year] en [jaar] - Toon het huidige jaar
// =============================================================================
//
// Usage: Copyright [current_year] of Copyright [jaar]
//
add_shortcode('current_year', 'writgo_ab_current_year');
add_shortcode('jaar', 'writgo_ab_current_year');
function writgo_ab_current_year($atts) {
    return esc_html(date('Y'));
}
