<?php
/**
 * Writgo Affiliate Shortcodes - Conversion Toolkit
 * 
 * Complete set of shortcodes optimized for affiliate marketing conversions
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================================
 * PRODUCT BOX - Main product showcase
 * ============================================================================
 * Usage: [product_box 
 *   naam="Samsung Galaxy S24" 
 *   score="9.2" 
 *   prijs="899" 
 *   oude_prijs="999"
 *   url="https://..." 
 *   afbeelding="url" 
 *   badge="Beste Keuze"
 *   knop_tekst="Bekijk laagste prijs"
 *   kenmerken="5G|128GB|6.2 inch"
 * ]
 */
add_shortcode('product_box', 'writgo_product_box_shortcode');
function writgo_product_box_shortcode($atts) {
    $atts = shortcode_atts(array(
        'naam'        => '',
        'score'       => '',
        'prijs'       => '',
        'oude_prijs'  => '',
        'url'         => '#',
        'afbeelding'  => '',
        'badge'       => '',
        'knop_tekst'  => 'Bekijk laagste prijs →',
        'kenmerken'   => '',
        'merk'        => '',
    ), $atts);
    
    $kenmerken = array_filter(explode('|', $atts['kenmerken']));
    $score_class = '';
    if ($atts['score']) {
        $score = floatval($atts['score']);
        if ($score >= 9) $score_class = 'excellent';
        elseif ($score >= 7.5) $score_class = 'good';
        elseif ($score >= 6) $score_class = 'average';
        else $score_class = 'poor';
    }
    
    ob_start();
    ?>
    <div class="waff-product-box">
        <?php if ($atts['badge']) : ?>
            <span class="waff-badge waff-badge-top"><?php echo esc_html($atts['badge']); ?></span>
        <?php endif; ?>
        
        <div class="waff-product-inner">
            <?php if ($atts['afbeelding']) : ?>
                <div class="waff-product-image">
                    <a href="<?php echo esc_url($atts['url']); ?>" rel="nofollow sponsored" target="_blank">
                        <img src="<?php echo esc_url($atts['afbeelding']); ?>" alt="<?php echo esc_attr($atts['naam']); ?>" loading="lazy">
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="waff-product-content">
                <?php if ($atts['merk']) : ?>
                    <span class="waff-product-brand"><?php echo esc_html($atts['merk']); ?></span>
                <?php endif; ?>
                
                <h3 class="waff-product-title">
                    <a href="<?php echo esc_url($atts['url']); ?>" rel="nofollow sponsored" target="_blank">
                        <?php echo esc_html($atts['naam']); ?>
                    </a>
                </h3>
                
                <?php if (!empty($kenmerken)) : ?>
                    <ul class="waff-product-features">
                        <?php foreach ($kenmerken as $kenmerk) : ?>
                            <li><span class="waff-check">✓</span> <?php echo esc_html(trim($kenmerk)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <div class="waff-product-action">
                <?php if ($atts['score']) : ?>
                    <div class="waff-score waff-score-<?php echo esc_attr($score_class); ?>">
                        <span class="waff-score-number"><?php echo esc_html($atts['score']); ?></span>
                        <span class="waff-score-label">Score</span>
                    </div>
                <?php endif; ?>
                
                <div class="waff-price-block">
                    <?php if ($atts['oude_prijs']) : ?>
                        <span class="waff-old-price">€<?php echo esc_html($atts['oude_prijs']); ?></span>
                    <?php endif; ?>
                    <?php if ($atts['prijs']) : ?>
                        <span class="waff-price">€<?php echo esc_html($atts['prijs']); ?></span>
                    <?php endif; ?>
                </div>
                
                <a href="<?php echo esc_url($atts['url']); ?>" class="waff-cta-button" rel="nofollow sponsored" target="_blank">
                    <?php echo esc_html($atts['knop_tekst']); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * ============================================================================
 * BEST PICK - Highlighted recommendation box
 * ============================================================================
 * Usage: [beste_keuze 
 *   naam="Product X" 
 *   reden="Beste prijs-kwaliteit verhouding"
 *   prijs="149"
 *   url="https://..."
 *   afbeelding="url"
 * ]
 */
add_shortcode('beste_keuze', 'writgo_best_pick_shortcode');
function writgo_best_pick_shortcode($atts) {
    $atts = shortcode_atts(array(
        'naam'       => '',
        'reden'      => 'Onze top aanbeveling',
        'prijs'      => '',
        'url'        => '#',
        'afbeelding' => '',
        'score'      => '',
        'knop_tekst' => 'Bekijk beste deal →',
    ), $atts);
    
    ob_start();
    ?>
    <div class="waff-best-pick">
        <div class="waff-best-pick-header">
            <span class="waff-trophy">🏆</span>
            <span class="waff-best-pick-label">BESTE KEUZE</span>
        </div>
        
        <div class="waff-best-pick-inner">
            <?php if ($atts['afbeelding']) : ?>
                <div class="waff-best-pick-image">
                    <img src="<?php echo esc_url($atts['afbeelding']); ?>" alt="<?php echo esc_attr($atts['naam']); ?>">
                </div>
            <?php endif; ?>
            
            <div class="waff-best-pick-content">
                <h3 class="waff-best-pick-title"><?php echo esc_html($atts['naam']); ?></h3>
                <p class="waff-best-pick-reason"><?php echo esc_html($atts['reden']); ?></p>
                
                <div class="waff-best-pick-footer">
                    <div class="waff-best-pick-meta">
                        <?php if ($atts['score']) : ?>
                            <span class="waff-mini-score"><?php echo esc_html($atts['score']); ?>/10</span>
                        <?php endif; ?>
                        <?php if ($atts['prijs']) : ?>
                            <span class="waff-best-pick-price">€<?php echo esc_html($atts['prijs']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <a href="<?php echo esc_url($atts['url']); ?>" class="waff-cta-button waff-cta-gold" rel="nofollow sponsored" target="_blank">
                        <?php echo esc_html($atts['knop_tekst']); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * ============================================================================
 * COMPARISON TABLE - Compare multiple products
 * ============================================================================
 * Usage: [vergelijking]
 * [vergelijk_product naam="Product A" score="8.5" prijs="99" url="..." winner="ja"]
 * [vergelijk_product naam="Product B" score="7.8" prijs="79" url="..."]
 * [/vergelijking]
 */
add_shortcode('vergelijking', 'writgo_comparison_wrapper_shortcode');
function writgo_comparison_wrapper_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'titel' => 'Producten Vergelijken',
    ), $atts);
    
    ob_start();
    ?>
    <div class="waff-comparison-table">
        <div class="waff-comparison-header">
            <h3><?php echo esc_html($atts['titel']); ?></h3>
        </div>
        <div class="waff-comparison-body">
            <?php echo do_shortcode($content); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('vergelijk_product', 'writgo_comparison_product_shortcode');
function writgo_comparison_product_shortcode($atts) {
    $atts = shortcode_atts(array(
        'naam'       => '',
        'score'      => '',
        'prijs'      => '',
        'url'        => '#',
        'afbeelding' => '',
        'winner'     => '',
        'kenmerken'  => '',
    ), $atts);
    
    $is_winner = $atts['winner'] === 'ja' || $atts['winner'] === 'true' || $atts['winner'] === '1';
    
    ob_start();
    ?>
    <div class="waff-compare-item <?php echo $is_winner ? 'waff-compare-winner' : ''; ?>">
        <?php if ($is_winner) : ?>
            <span class="waff-winner-badge">👑 Winnaar</span>
        <?php endif; ?>
        
        <?php if ($atts['afbeelding']) : ?>
            <div class="waff-compare-image">
                <img src="<?php echo esc_url($atts['afbeelding']); ?>" alt="<?php echo esc_attr($atts['naam']); ?>">
            </div>
        <?php endif; ?>
        
        <h4 class="waff-compare-name"><?php echo esc_html($atts['naam']); ?></h4>
        
        <?php if ($atts['score']) : ?>
            <div class="waff-compare-score"><?php echo esc_html($atts['score']); ?></div>
        <?php endif; ?>
        
        <?php if ($atts['prijs']) : ?>
            <div class="waff-compare-price">€<?php echo esc_html($atts['prijs']); ?></div>
        <?php endif; ?>
        
        <a href="<?php echo esc_url($atts['url']); ?>" class="waff-compare-button" rel="nofollow sponsored" target="_blank">
            Bekijk prijs
        </a>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * ============================================================================
 * VERDICT BOX - Conclusion/final verdict
 * ============================================================================
 * Usage: [verdict score="8.5" titel="Ons Oordeel"]Conclusie tekst hier...[/verdict]
 */
add_shortcode('verdict', 'writgo_verdict_shortcode');
function writgo_verdict_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'score' => '',
        'titel' => 'Ons Oordeel',
        'label' => '',
    ), $atts);
    
    $score = floatval($atts['score']);
    if ($score >= 9) $label = $atts['label'] ?: 'Uitstekend';
    elseif ($score >= 8) $label = $atts['label'] ?: 'Zeer goed';
    elseif ($score >= 7) $label = $atts['label'] ?: 'Goed';
    elseif ($score >= 6) $label = $atts['label'] ?: 'Redelijk';
    else $label = $atts['label'] ?: 'Matig';
    
    ob_start();
    ?>
    <div class="waff-verdict">
        <div class="waff-verdict-header">
            <h3 class="waff-verdict-title"><?php echo esc_html($atts['titel']); ?></h3>
        </div>
        <div class="waff-verdict-body">
            <div class="waff-verdict-score-wrap">
                <div class="waff-verdict-score">
                    <span class="waff-verdict-number"><?php echo esc_html($atts['score']); ?></span>
                    <span class="waff-verdict-max">/10</span>
                </div>
                <span class="waff-verdict-label"><?php echo esc_html($label); ?></span>
            </div>
            <div class="waff-verdict-content">
                <?php echo wp_kses_post($content); ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * ============================================================================
 * PROS/CONS - Improved version
 * ============================================================================
 * Usage: [voordelen_nadelen 
 *   voordelen="Voordeel 1|Voordeel 2|Voordeel 3" 
 *   nadelen="Nadeel 1|Nadeel 2"
 * ]
 */
add_shortcode('voordelen_nadelen', 'writgo_proscons_shortcode');
function writgo_proscons_shortcode($atts) {
    $atts = shortcode_atts(array(
        'voordelen' => '',
        'nadelen'   => '',
    ), $atts);
    
    $pros = array_filter(explode('|', $atts['voordelen']));
    $cons = array_filter(explode('|', $atts['nadelen']));
    
    ob_start();
    ?>
    <div class="waff-proscons">
        <div class="waff-pros">
            <div class="waff-proscons-header waff-pros-header">
                <span class="waff-proscons-icon">👍</span>
                <h4>Voordelen</h4>
            </div>
            <ul>
                <?php foreach ($pros as $pro) : ?>
                    <li><span class="waff-pro-check">✓</span> <?php echo esc_html(trim($pro)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="waff-cons">
            <div class="waff-proscons-header waff-cons-header">
                <span class="waff-proscons-icon">👎</span>
                <h4>Nadelen</h4>
            </div>
            <ul>
                <?php foreach ($cons as $con) : ?>
                    <li><span class="waff-con-x">✗</span> <?php echo esc_html(trim($con)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * ============================================================================
 * CTA BUTTON - Call to action
 * ============================================================================
 * Usage: [koop_knop url="https://..." tekst="Nu kopen" kleur="oranje"]
 */
add_shortcode('koop_knop', 'writgo_buy_button_shortcode');
function writgo_buy_button_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url'   => '#',
        'tekst' => 'Bekijk beste prijs →',
        'kleur' => 'oranje',
        'groot' => '',
        'prijs' => '',
    ), $atts);
    
    $classes = array('waff-cta-button');
    if ($atts['kleur'] === 'blauw') $classes[] = 'waff-cta-blue';
    if ($atts['kleur'] === 'groen') $classes[] = 'waff-cta-green';
    if ($atts['kleur'] === 'goud') $classes[] = 'waff-cta-gold';
    if ($atts['groot'] === 'ja') $classes[] = 'waff-cta-large';
    
    ob_start();
    ?>
    <div class="waff-cta-wrap">
        <a href="<?php echo esc_url($atts['url']); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>" rel="nofollow sponsored" target="_blank">
            <?php echo esc_html($atts['tekst']); ?>
        </a>
        <?php if ($atts['prijs']) : ?>
            <span class="waff-cta-price">Vanaf €<?php echo esc_html($atts['prijs']); ?></span>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * ============================================================================
 * ALERT BOX - Urgency/notification
 * ============================================================================
 * Usage: [alert type="deal"]Tijdelijke aanbieding! Nog 24 uur geldig.[/alert]
 */
add_shortcode('alert', 'writgo_alert_shortcode');
function writgo_alert_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'type'  => 'info', // info, deal, warning, tip
        'titel' => '',
    ), $atts);
    
    $icons = array(
        'info'    => 'ℹ️',
        'deal'    => '🔥',
        'warning' => '⚠️',
        'tip'     => '💡',
        'check'   => '✅',
    );
    
    $icon = $icons[$atts['type']] ?? $icons['info'];
    
    ob_start();
    ?>
    <div class="waff-alert waff-alert-<?php echo esc_attr($atts['type']); ?>">
        <span class="waff-alert-icon"><?php echo $icon; ?></span>
        <div class="waff-alert-content">
            <?php if ($atts['titel']) : ?>
                <strong class="waff-alert-title"><?php echo esc_html($atts['titel']); ?></strong>
            <?php endif; ?>
            <span class="waff-alert-text"><?php echo wp_kses_post($content); ?></span>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * ============================================================================
 * STAR RATING - Visual rating
 * ============================================================================
 * Usage: [sterren score="4.5" max="5"]
 */
add_shortcode('sterren', 'writgo_stars_shortcode');
function writgo_stars_shortcode($atts) {
    $atts = shortcode_atts(array(
        'score' => '5',
        'max'   => '5',
    ), $atts);
    
    $score = floatval($atts['score']);
    $max = intval($atts['max']);
    
    ob_start();
    ?>
    <span class="waff-stars" title="<?php echo esc_attr($score . ' van ' . $max); ?>">
        <?php for ($i = 1; $i <= $max; $i++) : ?>
            <?php if ($i <= floor($score)) : ?>
                <span class="waff-star waff-star-full">★</span>
            <?php elseif ($i - 0.5 <= $score) : ?>
                <span class="waff-star waff-star-half">★</span>
            <?php else : ?>
                <span class="waff-star waff-star-empty">☆</span>
            <?php endif; ?>
        <?php endfor; ?>
        <span class="waff-stars-number">(<?php echo esc_html($score); ?>)</span>
    </span>
    <?php
    return ob_get_clean();
}

/**
 * ============================================================================
 * PRICE TAG - Standalone price display
 * ============================================================================
 * Usage: [prijs bedrag="99.99" oud="129.99" label="Beste prijs"]
 */
add_shortcode('prijs', 'writgo_price_shortcode');
function writgo_price_shortcode($atts) {
    $atts = shortcode_atts(array(
        'bedrag' => '',
        'oud'    => '',
        'label'  => '',
    ), $atts);
    
    $korting = '';
    if ($atts['oud'] && $atts['bedrag']) {
        $oud = floatval($atts['oud']);
        $nieuw = floatval($atts['bedrag']);
        $korting = round((($oud - $nieuw) / $oud) * 100);
    }
    
    ob_start();
    ?>
    <span class="waff-price-tag">
        <?php if ($korting) : ?>
            <span class="waff-discount-badge">-<?php echo esc_html($korting); ?>%</span>
        <?php endif; ?>
        <?php if ($atts['oud']) : ?>
            <span class="waff-price-old">€<?php echo esc_html($atts['oud']); ?></span>
        <?php endif; ?>
        <span class="waff-price-current">€<?php echo esc_html($atts['bedrag']); ?></span>
        <?php if ($atts['label']) : ?>
            <span class="waff-price-label"><?php echo esc_html($atts['label']); ?></span>
        <?php endif; ?>
    </span>
    <?php
    return ob_get_clean();
}

/**
 * ============================================================================
 * SPECIFICATION TABLE - Product specs
 * ============================================================================
 * Usage: [specificaties]
 * Merk|Samsung
 * Model|Galaxy S24
 * Scherm|6.2 inch AMOLED
 * [/specificaties]
 */
add_shortcode('specificaties', 'writgo_specs_shortcode');
function writgo_specs_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'titel' => 'Specificaties',
    ), $atts);
    
    $lines = array_filter(explode("\n", trim($content)));
    
    ob_start();
    ?>
    <div class="waff-specs">
        <h4 class="waff-specs-title"><?php echo esc_html($atts['titel']); ?></h4>
        <table class="waff-specs-table">
            <?php foreach ($lines as $line) : 
                $parts = explode('|', $line, 2);
                if (count($parts) === 2) :
            ?>
                <tr>
                    <th><?php echo esc_html(trim($parts[0])); ?></th>
                    <td><?php echo esc_html(trim($parts[1])); ?></td>
                </tr>
            <?php endif; endforeach; ?>
        </table>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * ============================================================================
 * AFFILIATE DISCLOSURE - Trust element
 * ============================================================================
 * Usage: [affiliate_disclosure]
 */
add_shortcode('affiliate_disclosure', 'writgo_disclosure_shortcode');
function writgo_disclosure_shortcode($atts) {
    $text = get_theme_mod('writgo_disclosure_text', 'Dit artikel bevat affiliate links. Bij aankoop via deze links ontvangen wij een commissie, zonder extra kosten voor jou.');
    
    ob_start();
    ?>
    <div class="waff-disclosure">
        <span class="waff-disclosure-icon">ℹ️</span>
        <span class="waff-disclosure-text"><?php echo esc_html($text); ?></span>
    </div>
    <?php
    return ob_get_clean();
}
