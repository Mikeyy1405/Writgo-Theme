<?php
/**
 * Writgo Shortcodes
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Product Box Shortcode
 * Usage: [product naam="Product Naam" score="8.5" prijs="€99" url="https://..." afbeelding="url"]
 */
add_shortcode('product', 'writgo_product_shortcode');
function writgo_product_shortcode($atts) {
    $atts = shortcode_atts(array(
        'naam'      => '',
        'score'     => '',
        'prijs'     => '',
        'url'       => '#',
        'afbeelding'=> '',
        'badge'     => '',
    ), $atts);
    
    ob_start();
    ?>
    <div class="wa-product-box">
        <?php if ($atts['badge']) : ?>
            <span class="wa-product-badge"><?php echo esc_html($atts['badge']); ?></span>
        <?php endif; ?>
        
        <?php if ($atts['afbeelding']) : ?>
            <div class="wa-product-image">
                <img src="<?php echo esc_url($atts['afbeelding']); ?>" alt="<?php echo esc_attr($atts['naam']); ?>">
            </div>
        <?php endif; ?>
        
        <div class="wa-product-content">
            <h3 class="wa-product-title"><?php echo esc_html($atts['naam']); ?></h3>
            
            <?php if ($atts['score']) : ?>
                <div class="wa-product-score">
                    <span class="wa-score-circle"><?php echo esc_html($atts['score']); ?></span>
                </div>
            <?php endif; ?>
            
            <div class="wa-product-footer">
                <?php if ($atts['prijs']) : ?>
                    <span class="wa-product-price"><?php echo esc_html($atts['prijs']); ?></span>
                <?php endif; ?>
                
                <a href="<?php echo esc_url($atts['url']); ?>" class="wa-product-button" rel="nofollow sponsored" target="_blank">
                    Bekijk product
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
    <style>
        .wa-product-box {
            position: relative;
            background: var(--wa-bg);
            border: 1px solid var(--wa-border);
            border-radius: var(--wa-radius-lg);
            padding: var(--wa-space-5);
            margin: var(--wa-space-6) 0;
        }
        .wa-product-badge {
            position: absolute;
            top: -10px;
            left: var(--wa-space-4);
            background: var(--wa-accent);
            color: white;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: var(--wa-radius-sm);
        }
        .wa-product-image {
            margin-bottom: var(--wa-space-4);
        }
        .wa-product-image img {
            width: 100%;
            max-width: 200px;
            margin: 0 auto;
        }
        .wa-product-title {
            font-size: var(--wa-text-xl);
            margin: 0 0 var(--wa-space-3);
        }
        .wa-score-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--wa-success);
            color: white;
            font-weight: 700;
        }
        .wa-product-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: var(--wa-space-4);
        }
        .wa-product-price {
            font-size: var(--wa-text-xl);
            font-weight: 700;
            color: var(--wa-primary);
        }
        .wa-product-button {
            display: inline-flex;
            align-items: center;
            gap: var(--wa-space-2);
            padding: var(--wa-space-3) var(--wa-space-5);
            background: var(--wa-accent);
            color: white;
            border-radius: var(--wa-radius);
            text-decoration: none;
            font-weight: 600;
        }
        .wa-product-button:hover {
            background: var(--wa-accent-hover);
            color: white;
        }
    </style>
    <?php
    return ob_get_clean();
}

/**
 * Pros/Cons Shortcode
 * Usage: [pros_cons pros="Pro 1|Pro 2" cons="Con 1|Con 2"]
 */
add_shortcode('pros_cons', 'writgo_pros_cons_shortcode');
function writgo_pros_cons_shortcode($atts) {
    $atts = shortcode_atts(array(
        'pros' => '',
        'cons' => '',
    ), $atts);
    
    $pros = array_filter(explode('|', $atts['pros']));
    $cons = array_filter(explode('|', $atts['cons']));
    
    ob_start();
    ?>
    <div class="wa-pros-cons">
        <div class="wa-pros">
            <h4>✓ Voordelen</h4>
            <ul>
                <?php foreach ($pros as $pro) : ?>
                    <li><?php echo esc_html(trim($pro)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="wa-cons">
            <h4>✗ Nadelen</h4>
            <ul>
                <?php foreach ($cons as $con) : ?>
                    <li><?php echo esc_html(trim($con)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    
    <style>
        .wa-pros-cons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--wa-space-4);
            margin: var(--wa-space-6) 0;
        }
        @media (max-width: 640px) {
            .wa-pros-cons {
                grid-template-columns: 1fr;
            }
        }
        .wa-pros, .wa-cons {
            padding: var(--wa-space-4);
            border-radius: var(--wa-radius);
        }
        .wa-pros {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .wa-cons {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .wa-pros h4 {
            color: #10b981;
            margin: 0 0 var(--wa-space-3);
        }
        .wa-cons h4 {
            color: #ef4444;
            margin: 0 0 var(--wa-space-3);
        }
        .wa-pros-cons ul {
            margin: 0;
            padding-left: var(--wa-space-4);
        }
        .wa-pros-cons li {
            margin-bottom: var(--wa-space-2);
        }
    </style>
    <?php
    return ob_get_clean();
}

/**
 * CTA Button Shortcode
 * Usage: [cta url="https://..." tekst="Bekijk nu" kleur="oranje"]
 */
add_shortcode('cta', 'writgo_cta_shortcode');
function writgo_cta_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url'   => '#',
        'tekst' => 'Meer info',
        'kleur' => 'oranje',
    ), $atts);
    
    $class = 'wa-cta-button';
    if ($atts['kleur'] === 'blauw') {
        $class .= ' wa-cta-blue';
    }
    
    return sprintf(
        '<a href="%s" class="%s" rel="nofollow sponsored" target="_blank">%s</a>',
        esc_url($atts['url']),
        esc_attr($class),
        esc_html($atts['tekst'])
    );
}

/**
 * Add shortcode styles
 */
add_action('wp_head', 'writgo_shortcode_styles');
function writgo_shortcode_styles() {
    ?>
    <style>
        .wa-cta-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--wa-accent);
            color: white !important;
            border-radius: var(--wa-radius);
            text-decoration: none !important;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .wa-cta-button:hover {
            background: var(--wa-accent-hover);
            transform: translateY(-1px);
        }
        .wa-cta-blue {
            background: var(--wa-primary);
        }
        .wa-cta-blue:hover {
            background: var(--wa-primary-dark);
        }
    </style>
    <?php
}
