<?php
/**
 * Writgo SEO - Image SEO Module
 * 
 * Auto alt-text, optimization warnings, image sitemap
 *
 * @package Writgo_Affiliate
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// AUTO ALT TEXT
// =============================================================================

// Add alt text automatically on upload if empty
add_filter('wp_generate_attachment_metadata', 'writgo_auto_alt_text', 10, 2);
function writgo_auto_alt_text($metadata, $attachment_id) {
    $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
    
    if (empty($alt)) {
        $attachment = get_post($attachment_id);
        $filename = pathinfo(get_attached_file($attachment_id), PATHINFO_FILENAME);
        
        // Clean filename: remove numbers, dashes, underscores
        $alt_text = preg_replace('/[-_]/', ' ', $filename);
        $alt_text = preg_replace('/[0-9]+/', '', $alt_text);
        $alt_text = trim(preg_replace('/\s+/', ' ', $alt_text));
        $alt_text = ucfirst($alt_text);
        
        if (!empty($alt_text)) {
            update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt_text);
        }
    }
    
    return $metadata;
}

// =============================================================================
// IMAGE COLUMNS IN MEDIA LIBRARY
// =============================================================================

add_filter('manage_media_columns', 'writgo_media_columns');
function writgo_media_columns($columns) {
    $columns['writgo_alt'] = 'Alt Tekst';
    $columns['writgo_size'] = 'Bestandsgrootte';
    return $columns;
}

add_action('manage_media_custom_column', 'writgo_media_column_content', 10, 2);
function writgo_media_column_content($column_name, $attachment_id) {
    if ($column_name === 'writgo_alt') {
        $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        if ($alt) {
            echo '<span style="color: #16a34a;">✓ ' . esc_html(wp_trim_words($alt, 5, '...')) . '</span>';
        } else {
            echo '<span style="color: #dc2626;">✗ Ontbreekt</span>';
        }
    }
    
    if ($column_name === 'writgo_size') {
        $file = get_attached_file($attachment_id);
        if ($file && file_exists($file)) {
            $size = filesize($file);
            $size_kb = round($size / 1024);
            
            if ($size_kb > 500) {
                echo '<span style="color: #dc2626;">' . $size_kb . ' KB</span>';
            } elseif ($size_kb > 200) {
                echo '<span style="color: #d97706;">' . $size_kb . ' KB</span>';
            } else {
                echo '<span style="color: #16a34a;">' . $size_kb . ' KB</span>';
            }
        }
    }
}

// =============================================================================
// IMAGE OPTIMIZATION CHECK
// =============================================================================

add_action('add_meta_boxes', 'writgo_image_check_meta_box');
function writgo_image_check_meta_box() {
    add_meta_box(
        'writgo_image_check',
        '🖼️ Afbeelding SEO',
        'writgo_image_check_callback',
        array('post', 'page'),
        'side',
        'default'
    );
}

function writgo_image_check_callback($post) {
    $content = $post->post_content;
    
    // Count images
    preg_match_all('/<img[^>]+>/i', $content, $images);
    $total_images = count($images[0]);
    
    // Check for missing alt
    $missing_alt = 0;
    $large_images = 0;
    
    foreach ($images[0] as $img) {
        // Check alt
        if (!preg_match('/alt=["\'][^"\']+["\']/', $img)) {
            $missing_alt++;
        }
        
        // Check for width attribute (large images indicator)
        if (preg_match('/width=["\'](\d+)["\']/', $img, $width_match)) {
            if (intval($width_match[1]) > 1200) {
                $large_images++;
            }
        }
    }
    
    // Featured image check
    $has_featured = has_post_thumbnail($post->ID);
    $featured_alt = '';
    if ($has_featured) {
        $thumb_id = get_post_thumbnail_id($post->ID);
        $featured_alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
    }
    ?>
    
    <style>
        .writgo-img-check { padding: 10px 0; }
        .writgo-img-stat { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
        .writgo-img-stat:last-child { border-bottom: none; }
        .writgo-img-stat .label { color: #6b7280; font-size: 13px; }
        .writgo-img-stat .value { font-weight: 600; }
        .writgo-img-stat .value.good { color: #16a34a; }
        .writgo-img-stat .value.warning { color: #d97706; }
        .writgo-img-stat .value.bad { color: #dc2626; }
    </style>
    
    <div class="writgo-img-check">
        <div class="writgo-img-stat">
            <span class="label">Afbeeldingen in content</span>
            <span class="value"><?php echo $total_images; ?></span>
        </div>
        
        <div class="writgo-img-stat">
            <span class="label">Zonder alt-tekst</span>
            <span class="value <?php echo $missing_alt === 0 ? 'good' : 'bad'; ?>">
                <?php echo $missing_alt === 0 ? '✓ Geen' : $missing_alt; ?>
            </span>
        </div>
        
        <div class="writgo-img-stat">
            <span class="label">Uitgelichte afbeelding</span>
            <span class="value <?php echo $has_featured ? 'good' : 'bad'; ?>">
                <?php echo $has_featured ? '✓ Ja' : '✗ Nee'; ?>
            </span>
        </div>
        
        <?php if ($has_featured) : ?>
        <div class="writgo-img-stat">
            <span class="label">Featured alt-tekst</span>
            <span class="value <?php echo $featured_alt ? 'good' : 'warning'; ?>">
                <?php echo $featured_alt ? '✓ Ja' : '⚠ Ontbreekt'; ?>
            </span>
        </div>
        <?php endif; ?>
        
        <?php if ($large_images > 0) : ?>
        <div class="writgo-img-stat">
            <span class="label">Grote afbeeldingen</span>
            <span class="value warning">⚠ <?php echo $large_images; ?> gevonden</span>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($missing_alt > 0 || !$has_featured || ($has_featured && !$featured_alt)) : ?>
    <p style="font-size: 12px; color: #6b7280; margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
        💡 <strong>Tip:</strong> Voeg alt-teksten toe met je focus keyword voor betere SEO.
    </p>
    <?php endif; ?>
    
    <?php
}

// =============================================================================
// LAZY LOADING CONTROL
// =============================================================================

// Disable lazy loading for above-the-fold images
add_filter('wp_img_tag_add_loading_attr', 'writgo_control_lazy_loading', 10, 3);
function writgo_control_lazy_loading($value, $image, $context) {
    // Disable lazy loading for featured images
    if ($context === 'the_content') {
        // First image in content should not be lazy loaded
        static $first_image = true;
        if ($first_image) {
            $first_image = false;
            return false;
        }
    }
    
    return $value;
}

// =============================================================================
// WEBP DETECTION
// =============================================================================

add_action('admin_notices', 'writgo_webp_notice');
function writgo_webp_notice() {
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'upload') return;
    
    // Check for non-WebP images
    $non_webp = get_posts(array(
        'post_type' => 'attachment',
        'post_mime_type' => array('image/jpeg', 'image/png'),
        'posts_per_page' => 1,
    ));
    
    if (!empty($non_webp)) {
        ?>
        <div class="notice notice-info is-dismissible">
            <p>💡 <strong>Writgo Tip:</strong> Overweeg je afbeeldingen te converteren naar WebP formaat voor snellere laadtijden. 
            <a href="https://squoosh.app" target="_blank">Gebruik Squoosh</a> voor gratis conversie.</p>
        </div>
        <?php
    }
}

// =============================================================================
// IMAGE SITEMAP ADDITION
// =============================================================================

// Add images to main sitemap (already included in seo-technical.php)
// This filter can be used to add more image data
add_filter('writgo_sitemap_post_images', 'writgo_get_post_images', 10, 2);
function writgo_get_post_images($images, $post_id) {
    $content = get_post_field('post_content', $post_id);
    
    // Get images from content
    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $src) {
            // Only include images from our domain
            if (strpos($src, home_url()) !== false) {
                $images[] = array(
                    'url' => $src,
                    'title' => get_the_title($post_id),
                );
            }
        }
    }
    
    return $images;
}

// =============================================================================
// BULK ALT TEXT EDITOR
// =============================================================================

add_action('admin_menu', 'writgo_image_seo_menu', 23);
function writgo_image_seo_menu() {
    add_submenu_page(
        'writgo-dashboard',
        'Image SEO',
        '🖼️ Image SEO',
        'edit_posts',
        'writgo-image-seo',
        'writgo_image_seo_page'
    );
}

function writgo_image_seo_page() {
    // Handle form submission
    if (isset($_POST['writgo_save_alts']) && wp_verify_nonce($_POST['writgo_alt_nonce'], 'writgo_save_alts')) {
        if (isset($_POST['alt_text']) && is_array($_POST['alt_text'])) {
            foreach ($_POST['alt_text'] as $attachment_id => $alt) {
                update_post_meta(intval($attachment_id), '_wp_attachment_image_alt', sanitize_text_field($alt));
            }
        }
        echo '<div class="notice notice-success"><p>Alt-teksten opgeslagen!</p></div>';
    }
    
    // Get images
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => $per_page,
        'paged' => $paged,
        'post_status' => 'inherit',
    );
    
    // Filter for missing alt
    if (isset($_GET['filter']) && $_GET['filter'] === 'no_alt') {
        $args['meta_query'] = array(
            'relation' => 'OR',
            array('key' => '_wp_attachment_image_alt', 'compare' => 'NOT EXISTS'),
            array('key' => '_wp_attachment_image_alt', 'value' => ''),
        );
    }
    
    $query = new WP_Query($args);
    $images = $query->posts;
    $total_pages = $query->max_num_pages;
    
    // Count images without alt
    $no_alt_count = new WP_Query(array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => -1,
        'post_status' => 'inherit',
        'meta_query' => array(
            'relation' => 'OR',
            array('key' => '_wp_attachment_image_alt', 'compare' => 'NOT EXISTS'),
            array('key' => '_wp_attachment_image_alt', 'value' => ''),
        ),
    ));
    ?>
    
    <style>
        .writgo-img-page { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
        .writgo-img-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
        .writgo-img-stat-card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .writgo-img-stat-card .number { font-size: 32px; font-weight: 700; }
        .writgo-img-stat-card .number.good { color: #16a34a; }
        .writgo-img-stat-card .number.bad { color: #dc2626; }
        .writgo-img-stat-card .label { font-size: 13px; color: #6b7280; }
        .writgo-img-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .writgo-img-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .writgo-img-card img { width: 100%; height: 150px; object-fit: cover; }
        .writgo-img-card-body { padding: 15px; }
        .writgo-img-card input { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; }
        .writgo-img-card input:focus { border-color: #f97316; outline: none; }
        .writgo-img-filename { font-size: 12px; color: #9ca3af; margin-top: 8px; word-break: break-all; }
        .writgo-filters { margin-bottom: 20px; display: flex; gap: 10px; }
        .writgo-filters a { padding: 8px 16px; background: #f3f4f6; border-radius: 6px; text-decoration: none; color: #374151; }
        .writgo-filters a.active { background: #f97316; color: white; }
    </style>
    
    <div class="writgo-img-page">
        <h1 style="display: flex; align-items: center; gap: 10px;">🖼️ Image SEO</h1>
        
        <div class="writgo-img-stats">
            <div class="writgo-img-stat-card">
                <div class="number"><?php echo wp_count_posts('attachment')->inherit; ?></div>
                <div class="label">Totaal afbeeldingen</div>
            </div>
            <div class="writgo-img-stat-card">
                <div class="number bad"><?php echo $no_alt_count->found_posts; ?></div>
                <div class="label">Zonder alt-tekst</div>
            </div>
            <div class="writgo-img-stat-card">
                <div class="number good"><?php echo wp_count_posts('attachment')->inherit - $no_alt_count->found_posts; ?></div>
                <div class="label">Met alt-tekst</div>
            </div>
        </div>
        
        <div class="writgo-filters">
            <a href="<?php echo admin_url('admin.php?page=writgo-image-seo'); ?>" class="<?php echo !isset($_GET['filter']) ? 'active' : ''; ?>">Alle</a>
            <a href="<?php echo admin_url('admin.php?page=writgo-image-seo&filter=no_alt'); ?>" class="<?php echo isset($_GET['filter']) && $_GET['filter'] === 'no_alt' ? 'active' : ''; ?>">Zonder Alt</a>
        </div>
        
        <form method="post">
            <?php wp_nonce_field('writgo_save_alts', 'writgo_alt_nonce'); ?>
            
            <div class="writgo-img-grid">
                <?php foreach ($images as $img) : 
                    $alt = get_post_meta($img->ID, '_wp_attachment_image_alt', true);
                    $src = wp_get_attachment_image_src($img->ID, 'medium');
                ?>
                <div class="writgo-img-card">
                    <img src="<?php echo esc_url($src[0]); ?>" alt="">
                    <div class="writgo-img-card-body">
                        <input type="text" name="alt_text[<?php echo $img->ID; ?>]" value="<?php echo esc_attr($alt); ?>" placeholder="Voer alt-tekst in...">
                        <div class="writgo-img-filename"><?php echo esc_html(basename(get_attached_file($img->ID))); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="margin-top: 25px; text-align: center;">
                <button type="submit" name="writgo_save_alts" class="button button-primary button-large">
                    💾 Alt-teksten Opslaan
                </button>
            </div>
        </form>
        
        <?php if ($total_pages > 1) : ?>
        <div style="display: flex; justify-content: center; gap: 5px; margin-top: 25px;">
            <?php for ($i = 1; $i <= min($total_pages, 10); $i++) : ?>
                <?php if ($i === $paged) : ?>
                    <span style="padding: 8px 14px; background: #f97316; color: white; border-radius: 6px;"><?php echo $i; ?></span>
                <?php else : ?>
                    <a href="<?php echo add_query_arg('paged', $i); ?>" style="padding: 8px 14px; background: #f3f4f6; border-radius: 6px; text-decoration: none; color: #374151;"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
}
