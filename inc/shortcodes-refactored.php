<?php
/**
 * Writgo Shortcodes - REFACTORED
 *
 * Security: All outputs escaped and sanitized
 * Performance: Inline styles removed, moved to CSS
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit('No direct access.');
}

// =============================================================================
// PRODUCT BOX SHORTCODE
// =============================================================================

/**
 * Product Box Shortcode - SECURED & OPTIMIZED
 * Usage: [product name="Product Name" score="8.5" price="€99" url="https://..." image="url"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function writgo_product_shortcode_refactored($atts) {
    // Sanitize all attributes
    $atts = shortcode_atts(array(
        'name'  => '',
        'score' => '',
        'price' => '',
        'url'   => '#',
        'image' => '',
        'badge' => '',
        'class' => '',
    ), $atts, 'product');
    
    // Validate required fields
    if (empty($atts['name'])) {
        return '<!-- Product shortcode: name attribute required -->';
    }
    
    // Sanitize all values
    $name  = sanitize_text_field($atts['name']);
    $badge = sanitize_text_field($atts['badge']);
    $image = esc_url($atts['image']);
    $url   = esc_url($atts['url']);
    $price = sanitize_text_field($atts['price']);
    $score = floatval($atts['score']);
    $class = sanitize_html_class($atts['class']);
    
    // Validate URL
    if (!empty($url) && !wp_http_validate_url($url)) {
        return '<!-- Product shortcode: invalid URL -->';
    }
    
    // Build output
    ob_start();
    ?>
    <div class="wa-product-box <?php echo esc_attr($class); ?>">
        <?php if ($badge) : ?>
            <span class="wa-product-badge"><?php echo esc_html($badge); ?></span>
        <?php endif; ?>
        
        <?php if ($image) : ?>
            <div class="wa-product-image">
                <img 
                    src="<?php echo esc_url($image); ?>" 
                    alt="<?php echo esc_attr($name); ?>"
                    loading="lazy"
                    decoding="async"
                    width="200"
                    height="200"
                >
            </div>
        <?php endif; ?>
        
        <div class="wa-product-content">
            <h3 class="wa-product-title"><?php echo esc_html($name); ?></h3>
            
            <?php if ($score) : ?>
                <div class="wa-product-score">
                    <span class="wa-score-value"><?php echo esc_html(number_format((float)$score, 1)); ?>/10</span>
                </div>
            <?php endif; ?>
            
            <div class="wa-product-footer">
                <?php if ($price) : ?>
                    <span class="wa-product-price"><?php echo esc_html($price); ?></span>
                <?php endif; ?>
                
                <a 
                    href="<?php echo esc_url($url); ?>" 
                    class="wa-product-button" 
                    rel="nofollow sponsored" 
                    target="_blank"
                    aria-label="<?php echo esc_attr(sprintf('View %s', $name)); ?>"
                >
                    <?php writgo_te('view_deal'); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}

// Use the original shortcode name for compatibility
add_shortcode('product', 'writgo_product_shortcode_refactored');

// =============================================================================
// PROS/CONS SHORTCODE
// =============================================================================

/**
 * Pros/Cons Shortcode - SECURED & OPTIMIZED
 * Usage: [pros_cons pros="Pro 1|Pro 2|Pro 3" cons="Con 1|Con 2"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function writgo_pros_cons_shortcode_refactored($atts) {
    // Sanitize attributes
    $atts = shortcode_atts(array(
        'pros' => '',
        'cons' => '',
    ), $atts, 'pros_cons');
    
    // Parse and sanitize lists
    $pros = array_filter(array_map('trim', explode('|', $atts['pros'])));
    $cons = array_filter(array_map('trim', explode('|', $atts['cons'])));
    
    // Sanitize each item
    $pros = array_map('sanitize_text_field', $pros);
    $cons = array_map('sanitize_text_field', $cons);
    
    if (empty($pros) && empty($cons)) {
        return '<!-- Pros/Cons: no items provided -->';
    }
    
    ob_start();
    ?>
    <div class="wa-pros-cons">
        <?php if (!empty($pros)) : ?>
            <div class="wa-pros">
                <h4 class="wa-pros-title">✓ <?php esc_html_e('Pros', 'writgo'); ?></h4>
                <ul class="wa-pros-list">
                    <?php foreach ($pros as $pro) : ?>
                        <li><?php echo esc_html($pro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($cons)) : ?>
            <div class="wa-cons">
                <h4 class="wa-cons-title">✗ <?php esc_html_e('Cons', 'writgo'); ?></h4>
                <ul class="wa-cons-list">
                    <?php foreach ($cons as $con) : ?>
                        <li><?php echo esc_html($con); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <?php
    
    return ob_get_clean();
}

add_shortcode('pros_cons', 'writgo_pros_cons_shortcode_refactored');

// =============================================================================
// CTA BUTTON SHORTCODE
// =============================================================================

/**
 * CTA Button Shortcode - SECURED
 * Usage: [cta url="https://..." text="Click here" style="primary"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function writgo_cta_button_shortcode_refactored($atts) {
    // Sanitize attributes
    $atts = shortcode_atts(array(
        'url'   => '#',
        'text'  => __('Learn More', 'writgo'),
        'style' => 'primary',
        'class' => '',
    ), $atts, 'cta');
    
    // Validate required fields
    if (empty($atts['text'])) {
        return '<!-- CTA: text attribute required -->';
    }
    
    // Sanitize values
    $url = esc_url($atts['url']);
    $text = sanitize_text_field($atts['text']);
    $style = sanitize_html_class($atts['style']);
    $class = sanitize_html_class($atts['class']);
    
    // Validate URL
    if (!wp_http_validate_url($url)) {
        return '<!-- CTA: invalid URL -->';
    }
    
    // Build class list
    $classes = array('wa-cta-button', 'wa-cta-' . $style);
    if (!empty($class)) {
        $classes[] = $class;
    }
    $classes_str = implode(' ', $classes);
    
    return sprintf(
        '<a href="%s" class="%s" rel="nofollow sponsored" target="_blank" aria-label="%s">%s</a>',
        esc_url($url),
        esc_attr($classes_str),
        esc_attr($text),
        esc_html($text)
    );
}

add_shortcode('cta', 'writgo_cta_button_shortcode_refactored');

// =============================================================================
// TESTIMONIAL SHORTCODE
// =============================================================================

/**
 * Testimonial Shortcode - SECURED
 * Usage: [testimonial author="John Doe" role="CEO" content="Great product!"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function writgo_testimonial_shortcode_refactored($atts) {
    // Sanitize attributes
    $atts = shortcode_atts(array(
        'author'  => '',
        'role'    => '',
        'content' => '',
        'image'   => '',
        'rating'  => '',
    ), $atts, 'testimonial');
    
    // Validate required fields
    if (empty($atts['author']) || empty($atts['content'])) {
        return '<!-- Testimonial: author and content required -->';
    }
    
    // Sanitize values
    $author = sanitize_text_field($atts['author']);
    $role = sanitize_text_field($atts['role']);
    $content = wp_kses_post($atts['content']);
    $image = esc_url($atts['image']);
    $rating = intval($atts['rating']);
    
    ob_start();
    ?>
    <div class="wa-testimonial">
        <?php if ($image) : ?>
            <img 
                src="<?php echo esc_url($image); ?>" 
                alt="<?php echo esc_attr($author); ?>"
                class="wa-testimonial-image"
                loading="lazy"
                width="80"
                height="80"
            >
        <?php endif; ?>
        
        <div class="wa-testimonial-content">
            <?php if ($rating && $rating > 0) : ?>
                <div class="wa-testimonial-rating" aria-label="<?php echo esc_attr($rating . ' out of 5 stars'); ?>">
                    <?php for ($i = 0; $i < 5; $i++) : ?>
                        <span class="wa-star <?php echo $i < $rating ? 'wa-star-filled' : ''; ?>">★</span>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
            
            <p class="wa-testimonial-text"><?php echo $content; ?></p>
            
            <footer class="wa-testimonial-footer">
                <strong><?php echo esc_html($author); ?></strong>
                <?php if ($role) : ?>
                    <span class="wa-testimonial-role"><?php echo esc_html($role); ?></span>
                <?php endif; ?>
            </footer>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}

add_shortcode('testimonial', 'writgo_testimonial_shortcode_refactored');

// =============================================================================
// COMPARISON TABLE SHORTCODE
// =============================================================================

/**
 * Comparison Table Shortcode - SECURED
 * Usage: [comparison items="Product A|Product B|Product C" features="Price|Quality|Speed"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function writgo_comparison_shortcode_refactored($atts) {
    // Sanitize attributes
    $atts = shortcode_atts(array(
        'items'    => '',
        'features' => '',
    ), $atts, 'comparison');
    
    // Parse lists
    $items = array_filter(array_map('trim', explode('|', $atts['items'])));
    $features = array_filter(array_map('trim', explode('|', $atts['features'])));
    
    // Sanitize
    $items = array_map('sanitize_text_field', $items);
    $features = array_map('sanitize_text_field', $features);
    
    if (empty($items) || empty($features)) {
        return '<!-- Comparison: items and features required -->';
    }
    
    ob_start();
    ?>
    <div class="wa-comparison-table-wrapper">
        <table class="wa-comparison-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('Feature', 'writgo'); ?></th>
                    <?php foreach ($items as $item) : ?>
                        <th><?php echo esc_html($item); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($features as $feature) : ?>
                    <tr>
                        <td class="wa-comparison-feature"><?php echo esc_html($feature); ?></td>
                        <?php for ($i = 0; $i < count($items); $i++) : ?>
                            <td class="wa-comparison-cell">✓</td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    
    return ob_get_clean();
}

add_shortcode('comparison', 'writgo_comparison_shortcode_refactored');

// =============================================================================
// ENQUEUE SHORTCODE STYLES
// =============================================================================

/**
 * Enqueue shortcode styles
 * All styles moved from inline to external CSS
 */
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'writgo-shortcodes',
        WRITGO_URI . '/assets/css/shortcodes.min.css',
        array(),
        WRITGO_VERSION
    );
});

// =============================================================================
// END OF SHORTCODES
// =============================================================================
