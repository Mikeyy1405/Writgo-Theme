<?php
/**
 * Writgo Meta Boxes - REFACTORED FOR SECURITY
 *
 * All meta boxes now include:
 * - Nonce verification (CSRF protection)
 * - Capability checks (authorization)
 * - Input sanitization (XSS prevention)
 * - Proper escaping on output
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit('No direct access.');
}

// =============================================================================
// REGISTER META BOXES
// =============================================================================

add_action('add_meta_boxes', 'writgo_register_meta_boxes');

function writgo_register_meta_boxes() {
    // Only on posts
    if (get_post_type() !== 'post') {
        return;
    }
    
    // SEO Meta Box
    add_meta_box(
        'writgo_seo_meta_box',
        __('SEO Settings', 'writgo'),
        'writgo_seo_meta_box_callback',
        'post',
        'normal',
        'high'
    );
    
    // Product Meta Box
    add_meta_box(
        'writgo_product_meta_box',
        __('Product Settings', 'writgo'),
        'writgo_product_meta_box_callback',
        'post',
        'normal',
        'high'
    );
    
    // Article Meta Box
    add_meta_box(
        'writgo_article_meta_box',
        __('Article Settings', 'writgo'),
        'writgo_article_meta_box_callback',
        'post',
        'normal',
        'high'
    );
}

// =============================================================================
// SEO META BOX
// =============================================================================

/**
 * SEO Meta Box Callback
 *
 * @param WP_Post $post Post object
 */
function writgo_seo_meta_box_callback($post) {
    // Verify nonce for output
    wp_nonce_field('writgo_seo_meta_box', 'writgo_seo_nonce');
    
    // Retrieve meta values with sanitization
    $seo_title = get_post_meta($post->ID, '_writgo_seo_title', true);
    $description = get_post_meta($post->ID, '_writgo_seo_description', true);
    $focus_keyword = get_post_meta($post->ID, '_writgo_focus_keyword', true);
    $noindex = get_post_meta($post->ID, '_writgo_noindex', true);
    
    ?>
    <div class="writgo-meta-box-field">
        <label for="writgo_seo_title">
            <strong><?php esc_html_e('SEO Title', 'writgo'); ?></strong>
            <span class="writgo-char-count">(0/60)</span>
        </label>
        <input 
            type="text" 
            id="writgo_seo_title" 
            name="writgo_seo_title" 
            value="<?php echo esc_attr($seo_title); ?>"
            maxlength="60"
            placeholder="<?php esc_attr_e('Optimize for search engines', 'writgo'); ?>"
            class="widefat writgo-seo-input"
        >
        <p class="description"><?php esc_html_e('Recommended: 50-60 characters', 'writgo'); ?></p>
    </div>
    
    <div class="writgo-meta-box-field">
        <label for="writgo_seo_description">
            <strong><?php esc_html_e('Meta Description', 'writgo'); ?></strong>
            <span class="writgo-char-count">(0/160)</span>
        </label>
        <textarea 
            id="writgo_seo_description" 
            name="writgo_seo_description"
            rows="3"
            maxlength="160"
            placeholder="<?php esc_attr_e('Compelling meta description...', 'writgo'); ?>"
            class="widefat writgo-seo-input"
        ><?php echo esc_textarea($description); ?></textarea>
        <p class="description"><?php esc_html_e('Recommended: 150-160 characters', 'writgo'); ?></p>
    </div>
    
    <div class="writgo-meta-box-field">
        <label for="writgo_focus_keyword">
            <strong><?php esc_html_e('Focus Keyword', 'writgo'); ?></strong>
        </label>
        <input 
            type="text" 
            id="writgo_focus_keyword" 
            name="writgo_focus_keyword" 
            value="<?php echo esc_attr($focus_keyword); ?>"
            placeholder="<?php esc_attr_e('Main keyword for this post', 'writgo'); ?>"
            class="widefat"
        >
    </div>
    
    <div class="writgo-meta-box-field">
        <label for="writgo_noindex">
            <input 
                type="checkbox" 
                id="writgo_noindex" 
                name="writgo_noindex" 
                value="1"
                <?php checked($noindex, 1); ?>
            >
            <?php esc_html_e('Hide from search engines (noindex)', 'writgo'); ?>
        </label>
    </div>
    
    <style>
        .writgo-meta-box-field {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .writgo-meta-box-field:last-child {
            border-bottom: none;
        }
        .writgo-char-count {
            float: right;
            color: #999;
            font-size: 12px;
        }
        .writgo-seo-input {
            margin-top: 5px;
        }
    </style>
    <?php
}

/**
 * Save SEO Meta Box
 *
 * @param int     $post_id Post ID
 * @param WP_Post $post    Post object
 */
add_action('save_post', function($post_id, $post) {
    // Verify nonce
    if (!isset($_POST['writgo_seo_nonce']) || 
        !wp_verify_nonce($_POST['writgo_seo_nonce'], 'writgo_seo_meta_box')) {
        return;
    }
    
    // Check capabilities
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Don't save on autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Save SEO title
    if (isset($_POST['writgo_seo_title'])) {
        $seo_title = sanitize_text_field(wp_unslash($_POST['writgo_seo_title']));
        update_post_meta($post_id, '_writgo_seo_title', $seo_title);
    }
    
    // Save meta description
    if (isset($_POST['writgo_seo_description'])) {
        $description = sanitize_textarea_field(wp_unslash($_POST['writgo_seo_description']));
        update_post_meta($post_id, '_writgo_seo_description', $description);
    }
    
    // Save focus keyword
    if (isset($_POST['writgo_focus_keyword'])) {
        $keyword = sanitize_text_field(wp_unslash($_POST['writgo_focus_keyword']));
        update_post_meta($post_id, '_writgo_focus_keyword', $keyword);
    }
    
    // Save noindex flag
    if (isset($_POST['writgo_noindex'])) {
        update_post_meta($post_id, '_writgo_noindex', 1);
    } else {
        delete_post_meta($post_id, '_writgo_noindex');
    }
}, 10, 2);

// =============================================================================
// PRODUCT META BOX
// =============================================================================

/**
 * Product Meta Box Callback
 *
 * @param WP_Post $post Post object
 */
function writgo_product_meta_box_callback($post) {
    wp_nonce_field('writgo_product_meta_box', 'writgo_product_nonce');
    
    $price = get_post_meta($post->ID, '_writgo_product_price', true);
    $url = get_post_meta($post->ID, '_writgo_product_url', true);
    $rating = get_post_meta($post->ID, '_writgo_product_rating', true);
    $affiliate_network = get_post_meta($post->ID, '_writgo_affiliate_network', true);
    
    ?>
    <div class="writgo-meta-box-field">
        <label for="writgo_product_price">
            <strong><?php esc_html_e('Product Price', 'writgo'); ?></strong>
        </label>
        <input 
            type="text" 
            id="writgo_product_price" 
            name="writgo_product_price" 
            value="<?php echo esc_attr($price); ?>"
            placeholder="€99,99"
            class="widefat"
        >
    </div>
    
    <div class="writgo-meta-box-field">
        <label for="writgo_product_url">
            <strong><?php esc_html_e('Affiliate Link', 'writgo'); ?></strong>
        </label>
        <input 
            type="url" 
            id="writgo_product_url" 
            name="writgo_product_url" 
            value="<?php echo esc_url($url); ?>"
            placeholder="https://example.com/product"
            class="widefat"
        >
    </div>
    
    <div class="writgo-meta-box-field">
        <label for="writgo_product_rating">
            <strong><?php esc_html_e('Product Rating (0-10)', 'writgo'); ?></strong>
        </label>
        <input 
            type="number" 
            id="writgo_product_rating" 
            name="writgo_product_rating" 
            value="<?php echo esc_attr($rating); ?>"
            min="0"
            max="10"
            step="0.1"
            class="small-text"
        >
    </div>
    
    <div class="writgo-meta-box-field">
        <label for="writgo_affiliate_network">
            <strong><?php esc_html_e('Affiliate Network', 'writgo'); ?></strong>
        </label>
        <select id="writgo_affiliate_network" name="writgo_affiliate_network" class="widefat">
            <option value=""><?php esc_html_e('Select network...', 'writgo'); ?></option>
            <option value="amazon" <?php selected($affiliate_network, 'amazon'); ?>>Amazon</option>
            <option value="affiliate" <?php selected($affiliate_network, 'affiliate'); ?>>Affiliate</option>
            <option value="direct" <?php selected($affiliate_network, 'direct'); ?>>Direct Link</option>
        </select>
    </div>
    <?php
}

/**
 * Save Product Meta Box
 */
add_action('save_post', function($post_id, $post) {
    // Verify nonce
    if (!isset($_POST['writgo_product_nonce']) || 
        !wp_verify_nonce($_POST['writgo_product_nonce'], 'writgo_product_meta_box')) {
        return;
    }
    
    // Check capabilities
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Don't save on autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Save price
    if (isset($_POST['writgo_product_price'])) {
        $price = sanitize_text_field(wp_unslash($_POST['writgo_product_price']));
        update_post_meta($post_id, '_writgo_product_price', $price);
    }
    
    // Save URL
    if (isset($_POST['writgo_product_url'])) {
        $url = esc_url_raw(wp_unslash($_POST['writgo_product_url']));
        update_post_meta($post_id, '_writgo_product_url', $url);
    }
    
    // Save rating
    if (isset($_POST['writgo_product_rating'])) {
        $rating = floatval($_POST['writgo_product_rating']);
        update_post_meta($post_id, '_writgo_product_rating', $rating);
    }
    
    // Save affiliate network
    if (isset($_POST['writgo_affiliate_network'])) {
        $network = sanitize_text_field(wp_unslash($_POST['writgo_affiliate_network']));
        update_post_meta($post_id, '_writgo_affiliate_network', $network);
    }
}, 10, 2);

// =============================================================================
// ARTICLE META BOX
// =============================================================================

/**
 * Article Meta Box Callback
 *
 * @param WP_Post $post Post object
 */
function writgo_article_meta_box_callback($post) {
    wp_nonce_field('writgo_article_meta_box', 'writgo_article_nonce');
    
    $reading_time = get_post_meta($post->ID, '_writgo_reading_time', true);
    $featured_image_alt = get_post_meta($post->ID, '_writgo_featured_image_alt', true);
    
    ?>
    <div class="writgo-meta-box-field">
        <label for="writgo_reading_time">
            <strong><?php esc_html_e('Reading Time (minutes)', 'writgo'); ?></strong>
        </label>
        <input 
            type="number" 
            id="writgo_reading_time" 
            name="writgo_reading_time" 
            value="<?php echo esc_attr($reading_time); ?>"
            min="1"
            max="120"
            class="small-text"
        >
        <p class="description"><?php esc_html_e('Leave empty for automatic calculation', 'writgo'); ?></p>
    </div>
    
    <div class="writgo-meta-box-field">
        <label for="writgo_featured_image_alt">
            <strong><?php esc_html_e('Featured Image Alt Text', 'writgo'); ?></strong>
        </label>
        <input 
            type="text" 
            id="writgo_featured_image_alt" 
            name="writgo_featured_image_alt" 
            value="<?php echo esc_attr($featured_image_alt); ?>"
            placeholder="Descriptive alt text..."
            class="widefat"
        >
    </div>
    <?php
}

/**
 * Save Article Meta Box
 */
add_action('save_post', function($post_id, $post) {
    // Verify nonce
    if (!isset($_POST['writgo_article_nonce']) || 
        !wp_verify_nonce($_POST['writgo_article_nonce'], 'writgo_article_meta_box')) {
        return;
    }
    
    // Check capabilities
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Don't save on autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Save reading time
    if (isset($_POST['writgo_reading_time'])) {
        $reading_time = intval($_POST['writgo_reading_time']);
        if ($reading_time > 0) {
            update_post_meta($post_id, '_writgo_reading_time', $reading_time);
        }
    }
    
    // Save featured image alt
    if (isset($_POST['writgo_featured_image_alt'])) {
        $alt = sanitize_text_field(wp_unslash($_POST['writgo_featured_image_alt']));
        update_post_meta($post_id, '_writgo_featured_image_alt', $alt);
    }
}, 10, 2);

// =============================================================================
// END OF META BOXES
// =============================================================================
