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
 * Enqueue media uploader for SEO meta box
 */
add_action('admin_enqueue_scripts', 'writgo_enqueue_media_uploader');
function writgo_enqueue_media_uploader($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_media();
    }
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

    // Add nonce field for SEO meta box
    wp_nonce_field('writgo_seo_options', 'writgo_seo_options_nonce');

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
                    <div class="writgo-keyword-item">
                        <span class="status" id="kw-content-status">?</span>
                        <span>Keyword in eerste alinea</span>
                    </div>
                    <div class="writgo-keyword-item">
                        <span class="status" id="kw-headings-status">?</span>
                        <span>Keyword in subkoppen (H2/H3)</span>
                    </div>
                    <div class="writgo-keyword-item">
                        <span class="status" id="kw-density-status">?</span>
                        <span id="kw-density-text">Keyword density: -</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Analysis -->
        <div class="writgo-seo-section">
            <div class="writgo-content-analysis-box" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px;">
                <h4 style="margin: 0 0 15px; font-size: 14px; color: #1e293b; display: flex; align-items: center; gap: 8px;"><span>📊</span> Content Analyse</h4>

                <!-- Content Stats -->
                <div class="writgo-content-stats" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px;">
                    <div class="stat-box" style="background: #f8fafc; padding: 12px; border-radius: 8px; text-align: center;">
                        <div id="stat-word-count" style="font-size: 24px; font-weight: 700; color: #1e293b;">0</div>
                        <div style="font-size: 11px; color: #64748b;">Woorden</div>
                    </div>
                    <div class="stat-box" style="background: #f8fafc; padding: 12px; border-radius: 8px; text-align: center;">
                        <div id="stat-char-count" style="font-size: 24px; font-weight: 700; color: #1e293b;">0</div>
                        <div style="font-size: 11px; color: #64748b;">Tekens</div>
                    </div>
                    <div class="stat-box" style="background: #f8fafc; padding: 12px; border-radius: 8px; text-align: center;">
                        <div id="stat-sentence-count" style="font-size: 24px; font-weight: 700; color: #1e293b;">0</div>
                        <div style="font-size: 11px; color: #64748b;">Zinnen</div>
                    </div>
                    <div class="stat-box" style="background: #f8fafc; padding: 12px; border-radius: 8px; text-align: center;">
                        <div id="stat-paragraph-count" style="font-size: 24px; font-weight: 700; color: #1e293b;">0</div>
                        <div style="font-size: 11px; color: #64748b;">Paragrafen</div>
                    </div>
                </div>

                <!-- Readability Score -->
                <div class="writgo-readability" style="margin-bottom: 15px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-weight: 600; font-size: 13px; color: #334155;">Leesbaarheid (Flesch-Douma)</span>
                        <span id="readability-label" style="font-size: 12px; padding: 4px 10px; border-radius: 12px; background: #f1f5f9; color: #64748b;">Analyseren...</span>
                    </div>
                    <div style="background: #e5e7eb; border-radius: 8px; height: 12px; overflow: hidden;">
                        <div id="readability-bar" style="height: 100%; width: 0%; background: linear-gradient(90deg, #dc2626, #ca8a04, #16a34a); transition: width 0.3s; border-radius: 8px;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 4px; font-size: 10px; color: #94a3b8;">
                        <span>Moeilijk (0)</span>
                        <span>Gemiddeld (50)</span>
                        <span>Makkelijk (100)</span>
                    </div>
                </div>

                <!-- Content Recommendations -->
                <div class="writgo-content-checks">
                    <div class="writgo-keyword-item">
                        <span class="status" id="check-length-status">?</span>
                        <span id="check-length-text">Content lengte: -</span>
                    </div>
                    <div class="writgo-keyword-item">
                        <span class="status" id="check-images-status">?</span>
                        <span id="check-images-text">Afbeeldingen: -</span>
                    </div>
                    <div class="writgo-keyword-item">
                        <span class="status" id="check-headings-status">?</span>
                        <span id="check-headings-text">Subkoppen (H2/H3): -</span>
                    </div>
                    <div class="writgo-keyword-item">
                        <span class="status" id="check-links-status">?</span>
                        <span id="check-links-text">Interne/externe links: -</span>
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

        <!-- Social Media Preview & OG Image -->
        <?php
        $og_image_id = get_post_meta($post->ID, '_writgo_og_image', true);
        $og_image_url = $og_image_id ? wp_get_attachment_image_url($og_image_id, 'medium') : '';
        $featured_image_url = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID, 'medium') : '';
        $preview_image = $og_image_url ?: $featured_image_url;
        ?>
        <div class="writgo-seo-section">
            <h4><span>📱</span> Social Media</h4>

            <!-- Social Preview Tabs -->
            <div class="writgo-social-tabs" style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button type="button" class="writgo-tab-btn active" data-tab="facebook" style="padding: 8px 16px; border: 1px solid #e2e8f0; background: #f97316; color: #fff; border-radius: 6px; cursor: pointer;">Facebook</button>
                <button type="button" class="writgo-tab-btn" data-tab="twitter" style="padding: 8px 16px; border: 1px solid #e2e8f0; background: #fff; color: #334155; border-radius: 6px; cursor: pointer;">X / Twitter</button>
            </div>

            <!-- Facebook Preview -->
            <div class="writgo-social-preview" id="writgo-fb-preview" style="background: #fff; border: 1px solid #dddfe2; border-radius: 8px; overflow: hidden; max-width: 500px;">
                <div style="aspect-ratio: 1.91/1; background: #f0f2f5; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <?php if ($preview_image) : ?>
                        <img src="<?php echo esc_url($preview_image); ?>" id="writgo-fb-preview-img" style="width: 100%; height: 100%; object-fit: cover;" />
                    <?php else : ?>
                        <div id="writgo-fb-preview-img" style="color: #65676b; font-size: 14px;">Geen afbeelding</div>
                    <?php endif; ?>
                </div>
                <div style="padding: 12px; background: #f0f2f5;">
                    <div style="font-size: 12px; color: #65676b; text-transform: uppercase;"><?php echo esc_html(parse_url(home_url(), PHP_URL_HOST)); ?></div>
                    <div id="writgo-fb-title" style="font-size: 16px; font-weight: 600; color: #1c1e21; margin: 4px 0; line-height: 1.3;"><?php echo esc_html($seo_title ?: $default_title); ?></div>
                    <div id="writgo-fb-desc" style="font-size: 14px; color: #65676b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo esc_html($seo_description ?: $default_description); ?></div>
                </div>
            </div>

            <!-- Twitter Preview -->
            <div class="writgo-social-preview" id="writgo-tw-preview" style="background: #fff; border: 1px solid #cfd9de; border-radius: 16px; overflow: hidden; max-width: 500px; display: none;">
                <div style="aspect-ratio: 2/1; background: #f7f9f9; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <?php if ($preview_image) : ?>
                        <img src="<?php echo esc_url($preview_image); ?>" id="writgo-tw-preview-img" style="width: 100%; height: 100%; object-fit: cover;" />
                    <?php else : ?>
                        <div id="writgo-tw-preview-img" style="color: #536471; font-size: 14px;">Geen afbeelding</div>
                    <?php endif; ?>
                </div>
                <div style="padding: 12px;">
                    <div id="writgo-tw-title" style="font-size: 15px; font-weight: 400; color: #0f1419; line-height: 1.3;"><?php echo esc_html($seo_title ?: $default_title); ?></div>
                    <div id="writgo-tw-desc" style="font-size: 15px; color: #536471; margin-top: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo esc_html($seo_description ?: $default_description); ?></div>
                    <div style="font-size: 15px; color: #536471; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="#536471"><path d="M18.36 5.64c-1.95-1.96-5.11-1.96-7.07 0L9.88 7.05 8.46 5.64l1.42-1.42c2.73-2.73 7.16-2.73 9.9 0 2.73 2.74 2.73 7.17 0 9.9l-1.42 1.42-1.41-1.42 1.41-1.41c1.96-1.96 1.96-5.12 0-7.07zm-2.12 3.53l-7.07 7.07-1.41-1.41 7.07-7.07 1.41 1.41zm-12.02.71l1.42-1.42 1.41 1.42-1.41 1.41c-1.96 1.96-1.96 5.12 0 7.07 1.95 1.96 5.11 1.96 7.07 0l1.41-1.41 1.42 1.41-1.42 1.42c-2.73 2.73-7.16 2.73-9.9 0-2.73-2.74-2.73-7.17 0-9.9z"/></svg>
                        <?php echo esc_html(parse_url(home_url(), PHP_URL_HOST)); ?>
                    </div>
                </div>
            </div>

            <!-- OG Image Selector -->
            <div class="writgo-seo-row" style="margin-top: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Social Media Afbeelding</label>
                <p style="font-size: 12px; color: #64748b; margin: 0 0 10px;">Optioneel: Kies een andere afbeelding voor social media (1200x630px aanbevolen)</p>

                <div id="writgo-og-image-preview" style="margin-bottom: 10px;">
                    <?php if ($og_image_url) : ?>
                        <img src="<?php echo esc_url($og_image_url); ?>" style="max-width: 300px; height: auto; border-radius: 8px; border: 1px solid #e2e8f0;" />
                    <?php endif; ?>
                </div>

                <input type="hidden" name="writgo_og_image" id="writgo_og_image" value="<?php echo esc_attr($og_image_id); ?>" />

                <button type="button" id="writgo-og-image-btn" class="button" style="margin-right: 8px;">
                    <?php echo $og_image_id ? __('Afbeelding wijzigen', 'writgo-affiliate') : __('Afbeelding kiezen', 'writgo-affiliate'); ?>
                </button>
                <?php if ($og_image_id) : ?>
                    <button type="button" id="writgo-og-image-remove" class="button" style="color: #dc2626;"><?php _e('Verwijderen', 'writgo-affiliate'); ?></button>
                <?php endif; ?>
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

        // Get content from editor
        function getEditorContent() {
            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                return tinymce.get('content').getContent({ format: 'raw' });
            }
            var contentArea = document.getElementById('content');
            return contentArea ? contentArea.value : '';
        }

        function getPlainText(html) {
            var tmp = document.createElement('div');
            tmp.innerHTML = html;
            return tmp.textContent || tmp.innerText || '';
        }

        function countWords(text) {
            return text.trim().split(/\s+/).filter(function(w) { return w.length > 0; }).length;
        }

        function countSentences(text) {
            var matches = text.match(/[.!?]+/g);
            return matches ? matches.length : 0;
        }

        function countSyllables(word) {
            word = word.toLowerCase();
            if (word.length <= 3) return 1;
            // Dutch syllable estimation
            word = word.replace(/(?:[^laeiouy]es|ed|[^laeiouy]e)$/, '');
            word = word.replace(/^y/, '');
            var syl = word.match(/[aeiouy]{1,2}/g);
            return syl ? syl.length : 1;
        }

        function calculateReadability(text) {
            // Flesch-Douma formula for Dutch
            // 206.84 - 0.77 * (words/sentences) - 93 * (syllables/words)
            var plainText = getPlainText(text);
            var words = plainText.trim().split(/\s+/).filter(function(w) { return w.length > 0; });
            var wordCount = words.length;
            if (wordCount < 10) return -1;

            var sentenceCount = countSentences(plainText);
            if (sentenceCount < 1) sentenceCount = 1;

            var syllableCount = 0;
            words.forEach(function(word) {
                syllableCount += countSyllables(word.replace(/[^a-zA-Z]/g, ''));
            });

            var avgSentenceLength = wordCount / sentenceCount;
            var avgSyllablesPerWord = syllableCount / wordCount;

            // Flesch-Douma (Dutch adaptation)
            var score = 206.84 - (0.77 * avgSentenceLength) - (93 * avgSyllablesPerWord);
            return Math.max(0, Math.min(100, score));
        }

        function getReadabilityLabel(score) {
            if (score < 0) return { text: 'Te weinig tekst', color: '#94a3b8', bg: '#f1f5f9' };
            if (score >= 80) return { text: 'Zeer makkelijk', color: '#16a34a', bg: '#dcfce7' };
            if (score >= 60) return { text: 'Makkelijk', color: '#22c55e', bg: '#dcfce7' };
            if (score >= 40) return { text: 'Gemiddeld', color: '#ca8a04', bg: '#fef3c7' };
            if (score >= 20) return { text: 'Moeilijk', color: '#ea580c', bg: '#ffedd5' };
            return { text: 'Zeer moeilijk', color: '#dc2626', bg: '#fee2e2' };
        }

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

        function analyzeContent() {
            var content = getEditorContent();
            var plainText = getPlainText(content);
            var words = plainText.trim().split(/\s+/).filter(function(w) { return w.length > 0; });
            var wordCount = words.length;

            // Update stats
            document.getElementById('stat-word-count').textContent = wordCount.toLocaleString();
            document.getElementById('stat-char-count').textContent = plainText.length.toLocaleString();
            document.getElementById('stat-sentence-count').textContent = countSentences(plainText);

            // Count paragraphs (p tags or double line breaks)
            var paragraphs = content.match(/<p[^>]*>/gi) || [];
            document.getElementById('stat-paragraph-count').textContent = Math.max(1, paragraphs.length);

            // Readability score
            var readabilityScore = calculateReadability(content);
            var readabilityLabel = getReadabilityLabel(readabilityScore);
            document.getElementById('readability-bar').style.width = (readabilityScore >= 0 ? readabilityScore : 0) + '%';
            var labelEl = document.getElementById('readability-label');
            labelEl.textContent = readabilityLabel.text + (readabilityScore >= 0 ? ' (' + Math.round(readabilityScore) + ')' : '');
            labelEl.style.background = readabilityLabel.bg;
            labelEl.style.color = readabilityLabel.color;

            // Content length check
            var lengthStatus = document.getElementById('check-length-status');
            var lengthText = document.getElementById('check-length-text');
            if (wordCount >= 1500) {
                lengthStatus.className = 'status good';
                lengthStatus.textContent = '✓';
                lengthText.textContent = 'Content lengte: ' + wordCount + ' woorden (uitstekend!)';
            } else if (wordCount >= 800) {
                lengthStatus.className = 'status good';
                lengthStatus.textContent = '✓';
                lengthText.textContent = 'Content lengte: ' + wordCount + ' woorden (goed)';
            } else if (wordCount >= 300) {
                lengthStatus.className = 'status warning';
                lengthStatus.textContent = '!';
                lengthText.textContent = 'Content lengte: ' + wordCount + ' woorden (minimaal)';
            } else {
                lengthStatus.className = 'status bad';
                lengthStatus.textContent = '✗';
                lengthText.textContent = 'Content lengte: ' + wordCount + ' woorden (te kort)';
            }

            // Check images
            var images = content.match(/<img[^>]*>/gi) || [];
            var imageCount = images.length;
            var imagesStatus = document.getElementById('check-images-status');
            var imagesText = document.getElementById('check-images-text');
            if (imageCount >= 2) {
                imagesStatus.className = 'status good';
                imagesStatus.textContent = '✓';
                imagesText.textContent = 'Afbeeldingen: ' + imageCount + ' gevonden';
            } else if (imageCount >= 1) {
                imagesStatus.className = 'status warning';
                imagesStatus.textContent = '!';
                imagesText.textContent = 'Afbeeldingen: ' + imageCount + ' (voeg er meer toe)';
            } else {
                imagesStatus.className = 'status bad';
                imagesStatus.textContent = '✗';
                imagesText.textContent = 'Afbeeldingen: geen gevonden';
            }

            // Check headings
            var h2h3 = content.match(/<h[23][^>]*>/gi) || [];
            var headingCount = h2h3.length;
            var headingsStatus = document.getElementById('check-headings-status');
            var headingsText = document.getElementById('check-headings-text');
            if (headingCount >= 3) {
                headingsStatus.className = 'status good';
                headingsStatus.textContent = '✓';
                headingsText.textContent = 'Subkoppen: ' + headingCount + ' H2/H3 tags';
            } else if (headingCount >= 1) {
                headingsStatus.className = 'status warning';
                headingsStatus.textContent = '!';
                headingsText.textContent = 'Subkoppen: ' + headingCount + ' (voeg er meer toe)';
            } else {
                headingsStatus.className = 'status bad';
                headingsStatus.textContent = '✗';
                headingsText.textContent = 'Subkoppen: geen H2/H3 gevonden';
            }

            // Check links
            var links = content.match(/<a[^>]*href=[^>]*>/gi) || [];
            var linkCount = links.length;
            var linksStatus = document.getElementById('check-links-status');
            var linksText = document.getElementById('check-links-text');
            if (linkCount >= 3) {
                linksStatus.className = 'status good';
                linksStatus.textContent = '✓';
                linksText.textContent = 'Links: ' + linkCount + ' gevonden';
            } else if (linkCount >= 1) {
                linksStatus.className = 'status warning';
                linksStatus.textContent = '!';
                linksText.textContent = 'Links: ' + linkCount + ' (voeg er meer toe)';
            } else {
                linksStatus.className = 'status bad';
                linksStatus.textContent = '✗';
                linksText.textContent = 'Links: geen gevonden';
            }

            return { content: content, plainText: plainText, wordCount: wordCount };
        }

        function checkKeyword() {
            var keyword = keywordInput.value.toLowerCase().trim();
            var contentData = analyzeContent();

            if (!keyword) {
                document.getElementById('kw-title-status').className = 'status';
                document.getElementById('kw-title-status').textContent = '?';
                document.getElementById('kw-desc-status').className = 'status';
                document.getElementById('kw-desc-status').textContent = '?';
                document.getElementById('kw-url-status').className = 'status';
                document.getElementById('kw-url-status').textContent = '?';
                document.getElementById('kw-content-status').className = 'status';
                document.getElementById('kw-content-status').textContent = '?';
                document.getElementById('kw-headings-status').className = 'status';
                document.getElementById('kw-headings-status').textContent = '?';
                document.getElementById('kw-density-status').className = 'status';
                document.getElementById('kw-density-status').textContent = '?';
                document.getElementById('kw-density-text').textContent = 'Keyword density: -';
                return;
            }

            var title = (titleInput.value || titleInput.getAttribute('data-default')).toLowerCase();
            var desc = (descInput.value || descInput.getAttribute('data-default')).toLowerCase();
            var content = contentData.content.toLowerCase();
            var plainText = contentData.plainText.toLowerCase();

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

            // Check first paragraph
            var contentStatus = document.getElementById('kw-content-status');
            var firstParaMatch = content.match(/<p[^>]*>(.*?)<\/p>/i);
            var firstParagraph = firstParaMatch ? firstParaMatch[1].toLowerCase() : plainText.substring(0, 500);
            if (firstParagraph.indexOf(keyword) !== -1) {
                contentStatus.className = 'status good';
                contentStatus.textContent = '✓';
            } else {
                contentStatus.className = 'status bad';
                contentStatus.textContent = '✗';
            }

            // Check headings for keyword
            var headingsStatus = document.getElementById('kw-headings-status');
            var headingMatches = content.match(/<h[23][^>]*>.*?<\/h[23]>/gi) || [];
            var keywordInHeadings = headingMatches.some(function(h) {
                return h.toLowerCase().indexOf(keyword) !== -1;
            });
            if (keywordInHeadings) {
                headingsStatus.className = 'status good';
                headingsStatus.textContent = '✓';
            } else {
                headingsStatus.className = 'status bad';
                headingsStatus.textContent = '✗';
            }

            // Calculate keyword density
            var densityStatus = document.getElementById('kw-density-status');
            var densityText = document.getElementById('kw-density-text');
            var wordCount = contentData.wordCount;
            if (wordCount > 0) {
                var keywordRegex = new RegExp(keyword.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'gi');
                var keywordMatches = plainText.match(keywordRegex) || [];
                var keywordCount = keywordMatches.length;
                var density = (keywordCount / wordCount) * 100;

                densityText.textContent = 'Keyword density: ' + density.toFixed(2) + '% (' + keywordCount + 'x in ' + wordCount + ' woorden)';

                if (density >= 0.5 && density <= 2.5) {
                    densityStatus.className = 'status good';
                    densityStatus.textContent = '✓';
                } else if (density > 2.5) {
                    densityStatus.className = 'status warning';
                    densityStatus.textContent = '!';
                    densityText.textContent += ' - te hoog!';
                } else {
                    densityStatus.className = 'status bad';
                    densityStatus.textContent = '✗';
                    densityText.textContent += ' - te laag';
                }
            } else {
                densityText.textContent = 'Keyword density: geen content';
                densityStatus.className = 'status';
                densityStatus.textContent = '?';
            }
        }

        titleInput.addEventListener('input', function() { updateTitleCount(); checkKeyword(); });
        descInput.addEventListener('input', function() { updateDescCount(); checkKeyword(); });
        keywordInput.addEventListener('input', checkKeyword);

        // Listen for TinyMCE changes
        if (typeof tinymce !== 'undefined') {
            tinymce.on('AddEditor', function(e) {
                if (e.editor.id === 'content') {
                    e.editor.on('change keyup', function() {
                        setTimeout(checkKeyword, 100);
                    });
                }
            });
            // For already loaded editor
            setTimeout(function() {
                var editor = tinymce.get('content');
                if (editor) {
                    editor.on('change keyup', function() {
                        setTimeout(checkKeyword, 100);
                    });
                }
            }, 1000);
        }

        // Listen for text mode changes
        var contentArea = document.getElementById('content');
        if (contentArea) {
            contentArea.addEventListener('input', function() {
                setTimeout(checkKeyword, 100);
            });
        }

        // Initial update
        updateTitleCount();
        updateDescCount();
        setTimeout(checkKeyword, 500);

        // Social preview tabs
        document.querySelectorAll('.writgo-tab-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.writgo-tab-btn').forEach(function(b) {
                    b.style.background = '#fff';
                    b.style.color = '#334155';
                    b.classList.remove('active');
                });
                this.style.background = '#f97316';
                this.style.color = '#fff';
                this.classList.add('active');

                var tab = this.getAttribute('data-tab');
                document.getElementById('writgo-fb-preview').style.display = tab === 'facebook' ? 'block' : 'none';
                document.getElementById('writgo-tw-preview').style.display = tab === 'twitter' ? 'block' : 'none';
            });
        });

        // Update social previews when SEO fields change
        function updateSocialPreviews() {
            var title = titleInput.value || titleInput.getAttribute('data-default');
            var desc = descInput.value || descInput.getAttribute('data-default');

            document.getElementById('writgo-fb-title').textContent = title;
            document.getElementById('writgo-fb-desc').textContent = desc;
            document.getElementById('writgo-tw-title').textContent = title;
            document.getElementById('writgo-tw-desc').textContent = desc;
        }

        titleInput.addEventListener('input', updateSocialPreviews);
        descInput.addEventListener('input', updateSocialPreviews);

        // OG Image uploader
        var ogImageBtn = document.getElementById('writgo-og-image-btn');
        var ogImageInput = document.getElementById('writgo_og_image');
        var ogImagePreview = document.getElementById('writgo-og-image-preview');
        var ogImageRemove = document.getElementById('writgo-og-image-remove');

        if (ogImageBtn) {
            ogImageBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var frame = wp.media({
                    title: 'Kies Social Media Afbeelding',
                    button: { text: 'Selecteren' },
                    multiple: false,
                    library: { type: 'image' }
                });

                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    ogImageInput.value = attachment.id;
                    ogImagePreview.innerHTML = '<img src="' + attachment.sizes.medium.url + '" style="max-width: 300px; height: auto; border-radius: 8px; border: 1px solid #e2e8f0;" />';
                    ogImageBtn.textContent = 'Afbeelding wijzigen';

                    // Update social previews
                    var fbImg = document.getElementById('writgo-fb-preview-img');
                    var twImg = document.getElementById('writgo-tw-preview-img');
                    if (fbImg.tagName === 'IMG') {
                        fbImg.src = attachment.sizes.medium.url;
                    } else {
                        fbImg.outerHTML = '<img src="' + attachment.sizes.medium.url + '" id="writgo-fb-preview-img" style="width: 100%; height: 100%; object-fit: cover;" />';
                    }
                    if (twImg.tagName === 'IMG') {
                        twImg.src = attachment.sizes.medium.url;
                    } else {
                        twImg.outerHTML = '<img src="' + attachment.sizes.medium.url + '" id="writgo-tw-preview-img" style="width: 100%; height: 100%; object-fit: cover;" />';
                    }

                    // Add remove button if not exists
                    if (!document.getElementById('writgo-og-image-remove')) {
                        var removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.id = 'writgo-og-image-remove';
                        removeBtn.className = 'button';
                        removeBtn.style.color = '#dc2626';
                        removeBtn.textContent = 'Verwijderen';
                        ogImageBtn.parentNode.appendChild(removeBtn);
                        attachRemoveHandler(removeBtn);
                    }
                });

                frame.open();
            });
        }

        function attachRemoveHandler(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                ogImageInput.value = '';
                ogImagePreview.innerHTML = '';
                ogImageBtn.textContent = 'Afbeelding kiezen';
                this.remove();
            });
        }

        if (ogImageRemove) {
            attachRemoveHandler(ogImageRemove);
        }
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
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check post options nonce (for posts)
    $post_options_valid = isset($_POST['writgo_post_options_nonce']) &&
        wp_verify_nonce($_POST['writgo_post_options_nonce'], 'writgo_post_options');

    // Check SEO options nonce (for posts and pages)
    $seo_options_valid = isset($_POST['writgo_seo_options_nonce']) &&
        wp_verify_nonce($_POST['writgo_seo_options_nonce'], 'writgo_seo_options');

    // If neither nonce is valid, return
    if (!$post_options_valid && !$seo_options_valid) {
        return;
    }

    // Save featured (only if post options nonce is valid - posts only)
    if ($post_options_valid) {
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
    }

    // Save SEO options (if SEO nonce is valid - posts and pages)
    if ($seo_options_valid) {
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

        // Save OG Image
        if (isset($_POST['writgo_og_image'])) {
            $og_image = intval($_POST['writgo_og_image']);
            if ($og_image > 0) {
                update_post_meta($post_id, '_writgo_og_image', $og_image);
            } else {
                delete_post_meta($post_id, '_writgo_og_image');
            }
        }
    }

    // Save Sticky CTA options (only for posts)
    if ($post_options_valid) {
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
