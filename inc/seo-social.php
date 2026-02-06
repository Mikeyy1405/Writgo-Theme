<?php
/**
 * Writgo SEO - Social Media Module
 * 
 * Custom Open Graph & Twitter Card settings
 *
 * @package Writgo_Affiliate
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// SOCIAL META BOX
// =============================================================================

add_action('add_meta_boxes', 'writgo_social_register_meta_box');
function writgo_social_register_meta_box() {
    add_meta_box(
        'writgo_social_meta_box',
        '📱 Social Media SEO',
        'writgo_social_meta_box_callback',
        array('post', 'page'),
        'normal',
        'default'
    );
}

function writgo_social_meta_box_callback($post) {
    wp_nonce_field('writgo_social_meta_box', 'writgo_social_nonce');
    
    // Get saved values
    $og_title = get_post_meta($post->ID, '_writgo_og_title', true);
    $og_description = get_post_meta($post->ID, '_writgo_og_description', true);
    $og_image = get_post_meta($post->ID, '_writgo_og_image', true);
    
    $twitter_title = get_post_meta($post->ID, '_writgo_twitter_title', true);
    $twitter_description = get_post_meta($post->ID, '_writgo_twitter_description', true);
    $twitter_image = get_post_meta($post->ID, '_writgo_twitter_image', true);
    
    // Defaults
    $default_title = get_the_title($post->ID);
    $default_desc = has_excerpt($post->ID) ? get_the_excerpt($post->ID) : wp_trim_words(strip_tags($post->post_content), 30);
    $default_image = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID, 'large') : '';
    ?>
    
    <style>
        .writgo-social-box { padding: 15px 0; }
        .writgo-social-tabs { display: flex; gap: 0; margin-bottom: 20px; }
        .writgo-social-tab { padding: 12px 20px; cursor: pointer; background: #f3f4f6; border: none; font-weight: 500; font-size: 14px; transition: all 0.2s; }
        .writgo-social-tab:first-child { border-radius: 8px 0 0 8px; }
        .writgo-social-tab:last-child { border-radius: 0 8px 8px 0; }
        .writgo-social-tab.active { background: #1877f2; color: white; }
        .writgo-social-tab.twitter.active { background: #000; }
        
        .writgo-social-content { display: none; }
        .writgo-social-content.active { display: block; }
        
        .writgo-social-row { margin-bottom: 15px; }
        .writgo-social-row label { display: block; font-weight: 600; margin-bottom: 6px; color: #374151; font-size: 14px; }
        .writgo-social-row input, .writgo-social-row textarea { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .writgo-social-row input:focus, .writgo-social-row textarea:focus { border-color: #f97316; outline: none; }
        .writgo-tip { font-size: 12px; color: #9ca3af; margin-top: 4px; }
        
        .writgo-image-upload { display: flex; gap: 10px; align-items: flex-start; }
        .writgo-image-upload input { flex: 1; }
        .writgo-upload-btn { background: #f97316; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; white-space: nowrap; }
        .writgo-upload-btn:hover { background: #ea580c; }
        
        .writgo-preview-section { margin-top: 25px; padding-top: 25px; border-top: 1px solid #e5e7eb; }
        .writgo-preview-section h4 { margin: 0 0 15px; color: #374151; font-size: 15px; }
        
        /* Facebook Preview */
        .writgo-fb-preview { max-width: 500px; border: 1px solid #dddfe2; border-radius: 3px; overflow: hidden; font-family: Helvetica, Arial, sans-serif; background: white; }
        .writgo-fb-preview-image { width: 100%; height: 260px; background: #f0f2f5; background-size: cover; background-position: center; }
        .writgo-fb-preview-content { padding: 10px 12px; border-top: 1px solid #dddfe2; }
        .writgo-fb-preview-domain { font-size: 12px; color: #606770; text-transform: uppercase; margin-bottom: 5px; }
        .writgo-fb-preview-title { font-size: 16px; font-weight: 600; color: #1d2129; line-height: 1.3; margin-bottom: 5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .writgo-fb-preview-desc { font-size: 14px; color: #606770; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
        
        /* Twitter Preview */
        .writgo-twitter-preview { max-width: 500px; border: 1px solid #cfd9de; border-radius: 16px; overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: white; }
        .writgo-twitter-preview-image { width: 100%; height: 250px; background: #f7f9f9; background-size: cover; background-position: center; }
        .writgo-twitter-preview-content { padding: 12px; }
        .writgo-twitter-preview-domain { font-size: 13px; color: #536471; margin-bottom: 2px; }
        .writgo-twitter-preview-title { font-size: 15px; font-weight: 400; color: #0f1419; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
    
    <div class="writgo-social-box">
        <div class="writgo-social-tabs">
            <button type="button" class="writgo-social-tab active" data-tab="facebook">📘 Facebook / LinkedIn</button>
            <button type="button" class="writgo-social-tab twitter" data-tab="twitter">🐦 Twitter / X</button>
        </div>
        
        <!-- Facebook Tab -->
        <div class="writgo-social-content active" id="social-tab-facebook">
            <div class="writgo-social-row">
                <label>Facebook Titel</label>
                <input type="text" name="writgo_og_title" id="writgo_og_title" 
                       value="<?php echo esc_attr($og_title); ?>" 
                       placeholder="<?php echo esc_attr($default_title); ?>">
                <p class="writgo-tip">Laat leeg om de SEO titel te gebruiken</p>
            </div>
            
            <div class="writgo-social-row">
                <label>Facebook Beschrijving</label>
                <textarea name="writgo_og_description" id="writgo_og_description" rows="2" 
                          placeholder="<?php echo esc_attr($default_desc); ?>"><?php echo esc_textarea($og_description); ?></textarea>
                <p class="writgo-tip">Laat leeg om de meta omschrijving te gebruiken</p>
            </div>
            
            <div class="writgo-social-row">
                <label>Facebook Afbeelding</label>
                <div class="writgo-image-upload">
                    <input type="url" name="writgo_og_image" id="writgo_og_image" 
                           value="<?php echo esc_attr($og_image); ?>" 
                           placeholder="<?php echo esc_attr($default_image); ?>">
                    <button type="button" class="writgo-upload-btn" onclick="writgoUploadImage('og_image')">📷 Upload</button>
                </div>
                <p class="writgo-tip">Aanbevolen formaat: 1200 x 630 pixels. Laat leeg voor uitgelichte afbeelding.</p>
            </div>
            
            <div class="writgo-preview-section">
                <h4>👀 Facebook Preview</h4>
                <div class="writgo-fb-preview">
                    <div class="writgo-fb-preview-image" id="fb-preview-image" 
                         style="background-image: url('<?php echo esc_url($og_image ?: $default_image); ?>')"></div>
                    <div class="writgo-fb-preview-content">
                        <div class="writgo-fb-preview-domain"><?php echo esc_html(parse_url(home_url(), PHP_URL_HOST)); ?></div>
                        <div class="writgo-fb-preview-title" id="fb-preview-title"><?php echo esc_html($og_title ?: $default_title); ?></div>
                        <div class="writgo-fb-preview-desc" id="fb-preview-desc"><?php echo esc_html($og_description ?: $default_desc); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Twitter Tab -->
        <div class="writgo-social-content" id="social-tab-twitter">
            <div class="writgo-social-row">
                <label>Twitter Titel</label>
                <input type="text" name="writgo_twitter_title" id="writgo_twitter_title" 
                       value="<?php echo esc_attr($twitter_title); ?>" 
                       placeholder="<?php echo esc_attr($default_title); ?>">
                <p class="writgo-tip">Laat leeg om de Facebook titel te gebruiken</p>
            </div>
            
            <div class="writgo-social-content">
                <label>Twitter Beschrijving</label>
                <textarea name="writgo_twitter_description" id="writgo_twitter_description" rows="2" 
                          placeholder="<?php echo esc_attr($default_desc); ?>"><?php echo esc_textarea($twitter_description); ?></textarea>
                <p class="writgo-tip">Laat leeg om de Facebook beschrijving te gebruiken</p>
            </div>
            
            <div class="writgo-social-row">
                <label>Twitter Afbeelding</label>
                <div class="writgo-image-upload">
                    <input type="url" name="writgo_twitter_image" id="writgo_twitter_image" 
                           value="<?php echo esc_attr($twitter_image); ?>" 
                           placeholder="<?php echo esc_attr($default_image); ?>">
                    <button type="button" class="writgo-upload-btn" onclick="writgoUploadImage('twitter_image')">📷 Upload</button>
                </div>
                <p class="writgo-tip">Aanbevolen formaat: 1200 x 675 pixels (16:9)</p>
            </div>
            
            <div class="writgo-preview-section">
                <h4>👀 Twitter Preview</h4>
                <div class="writgo-twitter-preview">
                    <div class="writgo-twitter-preview-image" id="twitter-preview-image" 
                         style="background-image: url('<?php echo esc_url($twitter_image ?: $og_image ?: $default_image); ?>')"></div>
                    <div class="writgo-twitter-preview-content">
                        <div class="writgo-twitter-preview-title" id="twitter-preview-title"><?php echo esc_html($twitter_title ?: $og_title ?: $default_title); ?></div>
                        <div class="writgo-twitter-preview-domain"><?php echo esc_html(parse_url(home_url(), PHP_URL_HOST)); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    (function() {
        // Tab switching
        document.querySelectorAll('.writgo-social-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.writgo-social-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.writgo-social-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('social-tab-' + this.dataset.tab).classList.add('active');
            });
        });
        
        // Live preview updates
        var ogTitle = document.getElementById('writgo_og_title');
        var ogDesc = document.getElementById('writgo_og_description');
        var ogImage = document.getElementById('writgo_og_image');
        var twitterTitle = document.getElementById('writgo_twitter_title');
        var twitterImage = document.getElementById('writgo_twitter_image');
        
        var fbPreviewTitle = document.getElementById('fb-preview-title');
        var fbPreviewDesc = document.getElementById('fb-preview-desc');
        var fbPreviewImage = document.getElementById('fb-preview-image');
        var twitterPreviewTitle = document.getElementById('twitter-preview-title');
        var twitterPreviewImage = document.getElementById('twitter-preview-image');
        
        var defaultTitle = '<?php echo esc_js($default_title); ?>';
        var defaultDesc = '<?php echo esc_js($default_desc); ?>';
        var defaultImage = '<?php echo esc_js($default_image); ?>';
        
        function updatePreviews() {
            fbPreviewTitle.textContent = ogTitle.value || defaultTitle;
            fbPreviewDesc.textContent = ogDesc.value || defaultDesc;
            fbPreviewImage.style.backgroundImage = 'url(' + (ogImage.value || defaultImage) + ')';
            
            twitterPreviewTitle.textContent = twitterTitle.value || ogTitle.value || defaultTitle;
            twitterPreviewImage.style.backgroundImage = 'url(' + (twitterImage.value || ogImage.value || defaultImage) + ')';
        }
        
        ogTitle.addEventListener('input', updatePreviews);
        ogDesc.addEventListener('input', updatePreviews);
        ogImage.addEventListener('input', updatePreviews);
        twitterTitle.addEventListener('input', updatePreviews);
        twitterImage.addEventListener('input', updatePreviews);
    })();
    
    // Media uploader
    function writgoUploadImage(field) {
        var mediaUploader = wp.media({
            title: 'Kies afbeelding',
            button: { text: 'Selecteer' },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            document.getElementById('writgo_' + field).value = attachment.url;
            document.getElementById('writgo_' + field).dispatchEvent(new Event('input'));
        });
        
        mediaUploader.open();
    }
    </script>
    <?php
}

// Save social meta
add_action('save_post', 'writgo_social_save_meta');
function writgo_social_save_meta($post_id) {
    if (!isset($_POST['writgo_social_nonce']) || !wp_verify_nonce($_POST['writgo_social_nonce'], 'writgo_social_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    $fields = array('og_title', 'og_description', 'og_image', 'twitter_title', 'twitter_description', 'twitter_image');
    foreach ($fields as $field) {
        if (isset($_POST['writgo_' . $field])) {
            $value = $field === 'og_description' || $field === 'twitter_description' 
                ? sanitize_textarea_field($_POST['writgo_' . $field])
                : sanitize_text_field($_POST['writgo_' . $field]);
            
            if (!empty($value)) {
                update_post_meta($post_id, '_writgo_' . $field, $value);
            } else {
                delete_post_meta($post_id, '_writgo_' . $field);
            }
        }
    }
}

// =============================================================================
// ENHANCED SOCIAL META OUTPUT
// =============================================================================

// Remove default OG output and add enhanced version
remove_action('wp_head', 'writgo_seo_meta_tags', 1);
add_action('wp_head', 'writgo_enhanced_social_meta', 1);

function writgo_enhanced_social_meta() {
    if (defined('WPSEO_VERSION') || class_exists('RankMath')) {
        return;
    }
    
    global $post;
    
    // Defaults
    $title = get_bloginfo('name');
    $description = get_bloginfo('description');
    $image = get_site_icon_url(512);
    $url = home_url('/');
    $robots = array('index', 'follow');
    
    // OG specific
    $og_title = $title;
    $og_description = $description;
    $og_image = $image;
    
    // Twitter specific
    $twitter_title = $title;
    $twitter_description = $description;
    $twitter_image = $image;
    
    if (is_front_page()) {
        // Front page: use site name + tagline, og:type = website
        $title = get_bloginfo('name') . ' - ' . get_bloginfo('description');
        $description = get_bloginfo('description');
        $url = home_url('/');

        // Check if static front page has custom SEO values
        if ($post && $post->ID) {
            $seo_title = get_post_meta($post->ID, '_writgo_seo_title', true);
            $seo_desc = get_post_meta($post->ID, '_writgo_seo_description', true);
            $custom_og_title = get_post_meta($post->ID, '_writgo_og_title', true);
            $custom_og_desc = get_post_meta($post->ID, '_writgo_og_description', true);
            $custom_og_image = get_post_meta($post->ID, '_writgo_og_image', true);

            if ($seo_title) $title = $seo_title;
            if ($seo_desc) $description = $seo_desc;
            if ($custom_og_image) $image = $custom_og_image;
            if (has_post_thumbnail($post->ID)) {
                $image = get_the_post_thumbnail_url($post->ID, 'large');
            }
            if ($custom_og_title) $og_title = $custom_og_title;
            if ($custom_og_desc) $og_description = $custom_og_desc;
        }

        $og_title = !empty($og_title) && $og_title !== get_bloginfo('name') ? $og_title : $title;
        $og_description = !empty($og_description) && $og_description !== get_bloginfo('description') ? $og_description : $description;
        $og_image = $image;
        $twitter_title = $og_title;
        $twitter_description = $og_description;
        $twitter_image = $og_image;
        $is_front = true;

    } elseif (is_singular()) {
        // Get SEO values
        $seo_title = get_post_meta($post->ID, '_writgo_seo_title', true);
        $seo_desc = get_post_meta($post->ID, '_writgo_seo_description', true);
        $noindex = get_post_meta($post->ID, '_writgo_noindex', true);
        $nofollow = get_post_meta($post->ID, '_writgo_nofollow', true);

        // Get social specific values
        $custom_og_title = get_post_meta($post->ID, '_writgo_og_title', true);
        $custom_og_desc = get_post_meta($post->ID, '_writgo_og_description', true);
        $custom_og_image = get_post_meta($post->ID, '_writgo_og_image', true);
        $custom_twitter_title = get_post_meta($post->ID, '_writgo_twitter_title', true);
        $custom_twitter_desc = get_post_meta($post->ID, '_writgo_twitter_description', true);
        $custom_twitter_image = get_post_meta($post->ID, '_writgo_twitter_image', true);

        // Base values
        $title = $seo_title ?: get_the_title();
        $description = $seo_desc ?: (has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_tags($post->post_content), 30));
        $url = get_permalink();

        // Default image
        if (has_post_thumbnail()) {
            $image = get_the_post_thumbnail_url($post->ID, 'large');
        }

        // OG values (cascade: custom > seo > default)
        $og_title = $custom_og_title ?: $title;
        $og_description = $custom_og_desc ?: $description;
        $og_image = $custom_og_image ?: $image;

        // Twitter values (cascade: custom > og > seo > default)
        $twitter_title = $custom_twitter_title ?: $og_title;
        $twitter_description = $custom_twitter_desc ?: $og_description;
        $twitter_image = $custom_twitter_image ?: $og_image;

        // Robots
        if ($noindex === '1') $robots[0] = 'noindex';
        if ($nofollow === '1') $robots[1] = 'nofollow';

    } elseif (is_category()) {
        $cat_name = single_cat_title('', false);
        $cat_desc = category_description();
        $title = $cat_name . ' | ' . get_bloginfo('name');
        $description = $cat_desc ?: sprintf(__('Artikelen over %s op %s', 'writgo-affiliate'), $cat_name, get_bloginfo('name'));
        $url = get_category_link(get_queried_object_id());
        $og_title = $twitter_title = $title;
        $og_description = $twitter_description = wp_strip_all_tags($description);
    } elseif (is_tag()) {
        $tag_name = single_tag_title('', false);
        $title = $tag_name . ' | ' . get_bloginfo('name');
        $description = tag_description() ?: sprintf(__('Artikelen met tag %s op %s', 'writgo-affiliate'), $tag_name, get_bloginfo('name'));
        $url = get_tag_link(get_queried_object_id());
        $og_title = $twitter_title = $title;
        $og_description = $twitter_description = wp_strip_all_tags($description);
    } elseif (is_author()) {
        $author_name = get_the_author();
        $title = $author_name . ' | ' . get_bloginfo('name');
        $description = get_the_author_meta('description') ?: sprintf(__('Artikelen door %s', 'writgo-affiliate'), $author_name);
        $url = get_author_posts_url(get_queried_object_id());
        $og_title = $twitter_title = $title;
        $og_description = $twitter_description = $description;
    } elseif (is_search()) {
        $title = sprintf(__('Zoekresultaten voor: %s', 'writgo-affiliate'), get_search_query());
        $robots = array('noindex', 'follow');
    } elseif (is_archive()) {
        $title = get_the_archive_title() . ' | ' . get_bloginfo('name');
        $description = get_the_archive_description() ?: get_bloginfo('description');
        $og_title = $twitter_title = $title;
        $og_description = $twitter_description = wp_strip_all_tags($description);
    }
    
    // Clean descriptions
    $description = wp_strip_all_tags($description);
    $description = substr($description, 0, 160);
    $og_description = wp_strip_all_tags($og_description);
    $twitter_description = wp_strip_all_tags($twitter_description);
    
    ?>
    <meta name="description" content="<?php echo esc_attr($description); ?>">
    <meta name="robots" content="<?php echo esc_attr(implode(', ', $robots)); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo (is_singular() && !is_front_page()) ? 'article' : 'website'; ?>">
    <meta property="og:url" content="<?php echo esc_url($url); ?>">
    <meta property="og:title" content="<?php echo esc_attr($og_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($og_description); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <?php
    $locale_map = array('nl' => 'nl_NL', 'en' => 'en_US', 'de' => 'de_DE', 'fr' => 'fr_FR');
    $current_locale = $locale_map[writgo_get_language()] ?? 'nl_NL';
    ?>
    <meta property="og:locale" content="<?php echo esc_attr($current_locale); ?>">
    <?php if ($og_image) : ?>
    <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <?php endif; ?>
    
    <?php if (is_singular('post')) : ?>
    <meta property="article:published_time" content="<?php echo get_the_date('c'); ?>">
    <meta property="article:modified_time" content="<?php echo get_the_modified_date('c'); ?>">
    <meta property="article:author" content="<?php echo get_the_author(); ?>">
    <?php endif; ?>
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo esc_url($url); ?>">
    <meta name="twitter:title" content="<?php echo esc_attr($twitter_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($twitter_description); ?>">
    <?php if ($twitter_image) : ?>
    <meta name="twitter:image" content="<?php echo esc_url($twitter_image); ?>">
    <?php endif; ?>
    <?php
}
