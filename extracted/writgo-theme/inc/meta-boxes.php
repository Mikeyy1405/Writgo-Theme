<?php
/**
 * Writgo Meta Boxes
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Meta Boxes
 */
add_action('add_meta_boxes', 'writgo_register_meta_boxes');
function writgo_register_meta_boxes() {
    add_meta_box(
        'writgo_post_options',
        __('Writgo Opties', 'writgo-affiliate'),
        'writgo_post_options_callback',
        'post',
        'side',
        'high'
    );

    add_meta_box(
        'writgo_seo_options',
        __('🔍 SEO Instellingen', 'writgo-affiliate'),
        'writgo_seo_options_callback',
        array('post', 'page'),
        'normal',
        'high'
    );

    add_meta_box(
        'writgo_affiliate_options',
        __('🛒 Affiliate / CTA Instellingen', 'writgo-affiliate'),
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
            <?php _e('Uitgelicht op homepage', 'writgo-affiliate'); ?>
        </label>
    </p>
    
    <p>
        <label for="writgo_score"><?php _e('Score (0-10):', 'writgo-affiliate'); ?></label><br>
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
 * SEO Options Meta Box Callback
 */
function writgo_seo_options_callback($post) {
    // Skip if Yoast or RankMath is active
    if (defined('WPSEO_VERSION') || class_exists('RankMath')) {
        echo '<p style="padding: 15px; background: #fff3cd; border-radius: 6px; color: #856404;">Je hebt Yoast SEO of RankMath geactiveerd. Gebruik hun SEO instellingen in plaats van deze.</p>';
        return;
    }

    $seo_title = get_post_meta($post->ID, '_writgo_seo_title', true);
    $seo_description = get_post_meta($post->ID, '_writgo_seo_description', true);
    $focus_keyword = get_post_meta($post->ID, '_writgo_focus_keyword', true);
    $robots_index = get_post_meta($post->ID, '_writgo_robots_index', true);
    $robots_follow = get_post_meta($post->ID, '_writgo_robots_follow', true);

    // Defaults
    if ($robots_index === '') $robots_index = 'index';
    if ($robots_follow === '') $robots_follow = 'follow';

    $site_name = get_bloginfo('name');
    $default_title = get_the_title($post->ID);
    $default_description = has_excerpt($post->ID) ? get_the_excerpt($post->ID) : wp_trim_words(strip_tags($post->post_content), 25, '...');
    ?>

    <style>
        .writgo-seo-box { padding: 20px; background: #f8fafc; border-radius: 10px; }
        .writgo-seo-section { margin-bottom: 25px; }
        .writgo-seo-section:last-child { margin-bottom: 0; }
        .writgo-seo-section h4 { margin: 0 0 15px; font-size: 14px; color: #1e293b; display: flex; align-items: center; gap: 8px; }
        .writgo-seo-section h4 span { font-size: 16px; }
        .writgo-seo-row { margin-bottom: 15px; }
        .writgo-seo-row:last-child { margin-bottom: 0; }
        .writgo-seo-row label { display: block; font-weight: 600; margin-bottom: 6px; color: #334155; font-size: 13px; }
        .writgo-seo-row input[type="text"],
        .writgo-seo-row textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }
        .writgo-seo-row input:focus,
        .writgo-seo-row textarea:focus {
            border-color: #f97316;
            outline: none;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
        }
        .writgo-seo-row textarea { resize: vertical; min-height: 80px; }
        .writgo-char-count {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 6px;
            font-size: 12px;
        }
        .writgo-char-count .count { font-weight: 600; }
        .writgo-char-count .count.good { color: #16a34a; }
        .writgo-char-count .count.warning { color: #ca8a04; }
        .writgo-char-count .count.bad { color: #dc2626; }
        .writgo-char-count .hint { color: #64748b; }

        /* Google Preview */
        .writgo-google-preview {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .writgo-preview-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 12px;
            font-weight: 600;
        }
        .writgo-preview-url {
            color: #202124;
            font-size: 14px;
            margin-bottom: 4px;
            font-family: Arial, sans-serif;
        }
        .writgo-preview-title {
            color: #1a0dab;
            font-size: 20px;
            line-height: 1.3;
            margin-bottom: 4px;
            font-family: Arial, sans-serif;
            cursor: pointer;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .writgo-preview-title:hover { text-decoration: underline; }
        .writgo-preview-desc {
            color: #4d5156;
            font-size: 14px;
            line-height: 1.58;
            font-family: Arial, sans-serif;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Focus Keyword */
        .writgo-keyword-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
        }
        .writgo-keyword-analysis {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        .writgo-keyword-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            font-size: 13px;
        }
        .writgo-keyword-item .status {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            flex-shrink: 0;
        }
        .writgo-keyword-item .status.good { background: #dcfce7; color: #16a34a; }
        .writgo-keyword-item .status.bad { background: #fee2e2; color: #dc2626; }
        .writgo-keyword-item .status.warning { background: #fef3c7; color: #ca8a04; }

        /* Robots Settings */
        .writgo-robots-row {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        .writgo-robots-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .writgo-robots-option select {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
        }
        .writgo-robots-option select:focus {
            border-color: #f97316;
            outline: none;
        }
        .writgo-robots-option label {
            font-size: 13px;
            color: #475569;
        }
    </style>

    <div class="writgo-seo-box">
        <!-- Google Preview -->
        <div class="writgo-google-preview">
            <div class="writgo-preview-label">Google Preview</div>
            <div class="writgo-preview-url" id="writgo-preview-url"><?php echo esc_url(get_permalink($post->ID)); ?></div>
            <div class="writgo-preview-title" id="writgo-preview-title"><?php echo esc_html($seo_title ?: $default_title); ?> | <?php echo esc_html($site_name); ?></div>
            <div class="writgo-preview-desc" id="writgo-preview-desc"><?php echo esc_html($seo_description ?: $default_description); ?></div>
        </div>

        <!-- SEO Title -->
        <div class="writgo-seo-section">
            <div class="writgo-seo-row">
                <label for="writgo_seo_title">SEO Titel</label>
                <input type="text"
                       id="writgo_seo_title"
                       name="writgo_seo_title"
                       value="<?php echo esc_attr($seo_title); ?>"
                       placeholder="<?php echo esc_attr($default_title); ?>"
                       data-default="<?php echo esc_attr($default_title); ?>"
                       maxlength="70" />
                <div class="writgo-char-count">
                    <span class="hint">Optimaal: 50-60 karakters</span>
                    <span class="count" id="writgo-title-count">0/60</span>
                </div>
            </div>
        </div>

        <!-- Meta Description -->
        <div class="writgo-seo-section">
            <div class="writgo-seo-row">
                <label for="writgo_seo_description">Meta Omschrijving</label>
                <textarea id="writgo_seo_description"
                          name="writgo_seo_description"
                          placeholder="<?php echo esc_attr($default_description); ?>"
                          data-default="<?php echo esc_attr($default_description); ?>"
                          maxlength="170"><?php echo esc_textarea($seo_description); ?></textarea>
                <div class="writgo-char-count">
                    <span class="hint">Optimaal: 120-160 karakters</span>
                    <span class="count" id="writgo-desc-count">0/160</span>
                </div>
            </div>
        </div>

        <!-- Focus Keyword -->
        <div class="writgo-seo-section">
            <div class="writgo-keyword-box">
                <div class="writgo-seo-row">
                    <label for="writgo_focus_keyword">Focus Keyword</label>
                    <input type="text"
                           id="writgo_focus_keyword"
                           name="writgo_focus_keyword"
                           value="<?php echo esc_attr($focus_keyword); ?>"
                           placeholder="bijv. beste laptop 2024" />
                </div>
                <div class="writgo-keyword-analysis" id="writgo-keyword-analysis">
                    <div class="writgo-keyword-item">
                        <span class="status" id="kw-title-status">?</span>
                        <span>Keyword in SEO titel</span>
                    </div>
                    <div class="writgo-keyword-item">
                        <span class="status" id="kw-desc-status">?</span>
                        <span>Keyword in meta omschrijving</span>
                    </div>
                    <div class="writgo-keyword-item">
                        <span class="status" id="kw-url-status">?</span>
                        <span>Keyword in URL (slug)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Robots Settings -->
        <div class="writgo-seo-section">
            <h4><span>🤖</span> Robots Instellingen</h4>
            <div class="writgo-robots-row">
                <div class="writgo-robots-option">
                    <label for="writgo_robots_index">Indexering:</label>
                    <select id="writgo_robots_index" name="writgo_robots_index">
                        <option value="index" <?php selected($robots_index, 'index'); ?>>Index (standaard)</option>
                        <option value="noindex" <?php selected($robots_index, 'noindex'); ?>>Noindex</option>
                    </select>
                </div>
                <div class="writgo-robots-option">
                    <label for="writgo_robots_follow">Links volgen:</label>
                    <select id="writgo_robots_follow" name="writgo_robots_follow">
                        <option value="follow" <?php selected($robots_follow, 'follow'); ?>>Follow (standaard)</option>
                        <option value="nofollow" <?php selected($robots_follow, 'nofollow'); ?>>Nofollow</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var titleInput = document.getElementById('writgo_seo_title');
        var descInput = document.getElementById('writgo_seo_description');
        var keywordInput = document.getElementById('writgo_focus_keyword');
        var previewTitle = document.getElementById('writgo-preview-title');
        var previewDesc = document.getElementById('writgo-preview-desc');
        var titleCount = document.getElementById('writgo-title-count');
        var descCount = document.getElementById('writgo-desc-count');
        var siteName = '<?php echo esc_js($site_name); ?>';
        var postSlug = '<?php echo esc_js($post->post_name); ?>';

        function updateTitleCount() {
            var text = titleInput.value || titleInput.getAttribute('data-default');
            var len = text.length;
            titleCount.textContent = len + '/60';
            titleCount.className = 'count ' + (len >= 50 && len <= 60 ? 'good' : (len > 60 ? 'bad' : 'warning'));
            previewTitle.textContent = text + ' | ' + siteName;
        }

        function updateDescCount() {
            var text = descInput.value || descInput.getAttribute('data-default');
            var len = text.length;
            descCount.textContent = len + '/160';
            descCount.className = 'count ' + (len >= 120 && len <= 160 ? 'good' : (len > 160 ? 'bad' : 'warning'));
            previewDesc.textContent = text;
        }

        function checkKeyword() {
            var keyword = keywordInput.value.toLowerCase().trim();
            if (!keyword) {
                document.getElementById('kw-title-status').className = 'status';
                document.getElementById('kw-title-status').textContent = '?';
                document.getElementById('kw-desc-status').className = 'status';
                document.getElementById('kw-desc-status').textContent = '?';
                document.getElementById('kw-url-status').className = 'status';
                document.getElementById('kw-url-status').textContent = '?';
                return;
            }

            var title = (titleInput.value || titleInput.getAttribute('data-default')).toLowerCase();
            var desc = (descInput.value || descInput.getAttribute('data-default')).toLowerCase();

            // Check title
            var titleStatus = document.getElementById('kw-title-status');
            if (title.indexOf(keyword) !== -1) {
                titleStatus.className = 'status good';
                titleStatus.textContent = '✓';
            } else {
                titleStatus.className = 'status bad';
                titleStatus.textContent = '✗';
            }

            // Check description
            var descStatus = document.getElementById('kw-desc-status');
            if (desc.indexOf(keyword) !== -1) {
                descStatus.className = 'status good';
                descStatus.textContent = '✓';
            } else {
                descStatus.className = 'status bad';
                descStatus.textContent = '✗';
            }

            // Check URL
            var urlStatus = document.getElementById('kw-url-status');
            var keywordSlug = keyword.replace(/\s+/g, '-');
            if (postSlug.indexOf(keywordSlug) !== -1 || postSlug.indexOf(keyword.replace(/\s+/g, '')) !== -1) {
                urlStatus.className = 'status good';
                urlStatus.textContent = '✓';
            } else {
                urlStatus.className = 'status warning';
                urlStatus.textContent = '!';
            }
        }

        titleInput.addEventListener('input', function() { updateTitleCount(); checkKeyword(); });
        descInput.addEventListener('input', function() { updateDescCount(); checkKeyword(); });
        keywordInput.addEventListener('input', checkKeyword);

        // Initial update
        updateTitleCount();
        updateDescCount();
        checkKeyword();
    })();
    </script>
    <?php
}

/**
 * Affiliate Options Meta Box Callback
 */
function writgo_affiliate_options_callback($post) {
    $sticky_enabled = get_post_meta($post->ID, '_writgo_sticky_cta', true);
    $sticky_title = get_post_meta($post->ID, '_writgo_sticky_title', true);
    $sticky_price = get_post_meta($post->ID, '_writgo_sticky_price', true);
    $sticky_url = get_post_meta($post->ID, '_writgo_sticky_url', true);
    $sticky_button = get_post_meta($post->ID, '_writgo_sticky_button', true) ?: 'Bekijk beste prijs →';
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
        <h4>📌 Sticky CTA Bar (onderaan artikel)</h4>
        <p class="writgo-tip">Toon een vaste balk onderaan het scherm met een directe call-to-action. Perfect voor reviews!</p>
        
        <div class="writgo-affiliate-row">
            <label>
                <input type="checkbox" 
                       name="writgo_sticky_cta" 
                       value="1" 
                       <?php checked($sticky_enabled, '1'); ?> />
                Activeer Sticky CTA Bar
            </label>
        </div>
        
        <div class="writgo-affiliate-row">
            <label for="writgo_sticky_title">Product/Titel:</label>
            <input type="text" 
                   id="writgo_sticky_title" 
                   name="writgo_sticky_title" 
                   value="<?php echo esc_attr($sticky_title); ?>" 
                   placeholder="bijv. Samsung Galaxy S24" />
        </div>
        
        <div class="writgo-affiliate-row">
            <label for="writgo_sticky_price">Prijs (€):</label>
            <input type="text" 
                   id="writgo_sticky_price" 
                   name="writgo_sticky_price" 
                   value="<?php echo esc_attr($sticky_price); ?>" 
                   placeholder="bijv. 899" />
        </div>
        
        <div class="writgo-affiliate-row">
            <label for="writgo_sticky_url">Affiliate URL:</label>
            <input type="url" 
                   id="writgo_sticky_url" 
                   name="writgo_sticky_url" 
                   value="<?php echo esc_attr($sticky_url); ?>" 
                   placeholder="https://partner.bol.com/..." />
        </div>
        
        <div class="writgo-affiliate-row">
            <label for="writgo_sticky_button">Knop tekst:</label>
            <input type="text" 
                   id="writgo_sticky_button" 
                   name="writgo_sticky_button" 
                   value="<?php echo esc_attr($sticky_button); ?>" 
                   placeholder="Bekijk beste prijs →" />
        </div>
    </div>
    
    <?php
}

/**
 * Save Meta Box Data
 */
add_action('save_post', 'writgo_save_meta_boxes');
function writgo_save_meta_boxes($post_id) {
    // Check nonce
    if (!isset($_POST['writgo_post_options_nonce']) || 
        !wp_verify_nonce($_POST['writgo_post_options_nonce'], 'writgo_post_options')) {
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
    
    // Save SEO options
    if (isset($_POST['writgo_seo_title'])) {
        update_post_meta($post_id, '_writgo_seo_title', sanitize_text_field($_POST['writgo_seo_title']));
    }

    if (isset($_POST['writgo_seo_description'])) {
        update_post_meta($post_id, '_writgo_seo_description', sanitize_textarea_field($_POST['writgo_seo_description']));
    }

    if (isset($_POST['writgo_focus_keyword'])) {
        update_post_meta($post_id, '_writgo_focus_keyword', sanitize_text_field($_POST['writgo_focus_keyword']));
    }

    if (isset($_POST['writgo_robots_index'])) {
        $robots_index = in_array($_POST['writgo_robots_index'], array('index', 'noindex')) ? $_POST['writgo_robots_index'] : 'index';
        update_post_meta($post_id, '_writgo_robots_index', $robots_index);
    }

    if (isset($_POST['writgo_robots_follow'])) {
        $robots_follow = in_array($_POST['writgo_robots_follow'], array('follow', 'nofollow')) ? $_POST['writgo_robots_follow'] : 'follow';
        update_post_meta($post_id, '_writgo_robots_follow', $robots_follow);
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
    $button = get_post_meta($post_id, '_writgo_sticky_button', true) ?: 'Bekijk beste prijs →';
    
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
