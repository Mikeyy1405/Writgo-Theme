<?php
/**
 * Writgo SEO - Complete SEO Module
 *
 * All-in-one SEO solution for the Writgo Affiliate Theme.
 * Combines meta box, schema markup, social media, and frontend output.
 *
 * Features:
 * - SEO meta box with focus keyword, title, description, noindex/nofollow
 * - Schema meta box with Article, Product Review, FAQ, HowTo, Top Lijst
 * - Social meta box with OG and Twitter Card settings + live preview
 * - Frontend meta output (description, robots, canonical, OG, Twitter)
 * - Structured data output (Article, Product, FAQ, HowTo, ItemList, Breadcrumb, WebSite, Organization)
 *
 * All output is SKIPPED when Yoast SEO or RankMath is active.
 *
 * @package Writgo_Affiliate
 * @version 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

/**
 * Check if a third-party SEO plugin is active (Yoast or RankMath)
 *
 * @return bool True if Yoast or RankMath is detected
 */
function writgo_seo_plugin_active() {
    return defined('WPSEO_VERSION') || class_exists('RankMath');
}

/**
 * Clean a description string: strip tags and limit to max length
 *
 * @param string $text    Raw text
 * @param int    $length  Maximum character length (default 160)
 * @return string Cleaned description
 */
function writgo_clean_description($text, $length = 160) {
    $text = wp_strip_all_tags($text);
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    if (mb_strlen($text) > $length) {
        $text = mb_substr($text, 0, $length);
    }
    return $text;
}

/**
 * Get the OG locale based on theme language setting
 *
 * @return string Locale string (e.g. nl_NL, en_US)
 */
function writgo_get_og_locale() {
    $locale_map = array(
        'nl' => 'nl_NL',
        'en' => 'en_US',
        'de' => 'de_DE',
        'fr' => 'fr_FR',
    );
    $lang = function_exists('writgo_get_language') ? writgo_get_language() : 'nl';
    return isset($locale_map[$lang]) ? $locale_map[$lang] : 'nl_NL';
}


// =============================================================================
// 1. SEO META BOX (posts + pages)
// =============================================================================

add_action('add_meta_boxes', 'writgo_seo_register_meta_box');
function writgo_seo_register_meta_box() {
    if (writgo_seo_plugin_active()) {
        return;
    }
    foreach (array('post', 'page') as $post_type) {
        add_meta_box(
            'writgo_seo_meta_box',
            'Writgo SEO',
            'writgo_seo_meta_box_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}

/**
 * SEO Meta Box Callback
 */
function writgo_seo_meta_box_callback($post) {
    wp_nonce_field('writgo_seo_meta_box', 'writgo_seo_nonce');

    $seo_title       = get_post_meta($post->ID, '_writgo_seo_title', true);
    $seo_description = get_post_meta($post->ID, '_writgo_seo_description', true);
    $focus_keyword   = get_post_meta($post->ID, '_writgo_focus_keyword', true);
    $noindex         = get_post_meta($post->ID, '_writgo_noindex', true);
    $nofollow        = get_post_meta($post->ID, '_writgo_nofollow', true);
    $post_title      = $post->post_title;
    ?>

    <style>
        .writgo-seo-meta-box { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 15px 0; }
        .writgo-seo-field { margin-bottom: 20px; }
        .writgo-seo-field label { display: block; font-weight: 600; margin-bottom: 8px; color: #374151; font-size: 14px; }
        .writgo-seo-field input[type="text"],
        .writgo-seo-field textarea {
            width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;
            font-size: 14px; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .writgo-seo-field input:focus,
        .writgo-seo-field textarea:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
        .writgo-seo-char-count { font-size: 12px; color: #9ca3af; margin-top: 6px; }
        .writgo-seo-char-count.good { color: #22c55e; }
        .writgo-seo-char-count.warning { color: #f59e0b; }
        .writgo-seo-char-count.danger { color: #ef4444; }
        .writgo-seo-preview { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
        .writgo-seo-preview h4 { margin: 0 0 12px; font-size: 13px; color: #6b7280; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        .writgo-seo-preview-title { color: #1a0dab; font-size: 20px; line-height: 1.3; margin-bottom: 4px; }
        .writgo-seo-preview-url { color: #006621; font-size: 14px; margin-bottom: 4px; }
        .writgo-seo-preview-desc { color: #545454; font-size: 14px; line-height: 1.5; }
        .writgo-robot-settings { display: flex; gap: 24px; margin-top: 6px; }
        .writgo-robot-settings label { display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 400; font-size: 14px; color: #374151; }
    </style>

    <div class="writgo-seo-meta-box">

        <!-- Focus Keyword -->
        <div class="writgo-seo-field">
            <label for="writgo_focus_keyword">Focus Keyword</label>
            <input type="text"
                   id="writgo_focus_keyword"
                   name="writgo_focus_keyword"
                   value="<?php echo esc_attr($focus_keyword); ?>"
                   placeholder="bijv. beste bluetooth speaker" />
        </div>

        <!-- SEO Title -->
        <div class="writgo-seo-field">
            <label for="writgo_seo_title">SEO Titel</label>
            <input type="text"
                   id="writgo_seo_title"
                   name="writgo_seo_title"
                   value="<?php echo esc_attr($seo_title); ?>"
                   placeholder="<?php echo esc_attr($post_title); ?>"
                   maxlength="60" />
            <p class="writgo-seo-char-count" id="writgoSeoTitleCount">0 / 60 tekens</p>
        </div>

        <!-- Meta Description -->
        <div class="writgo-seo-field">
            <label for="writgo_seo_description">Meta Omschrijving</label>
            <textarea id="writgo_seo_description"
                      name="writgo_seo_description"
                      rows="3"
                      maxlength="160"
                      placeholder="Schrijf een pakkende omschrijving voor in de zoekresultaten..."><?php echo esc_textarea($seo_description); ?></textarea>
            <p class="writgo-seo-char-count" id="writgoSeoDescCount">0 / 160 tekens</p>
        </div>

        <!-- Google Preview -->
        <div class="writgo-seo-preview">
            <h4>Google Preview</h4>
            <div class="writgo-seo-preview-title" id="writgoSeoPreviewTitle"><?php echo esc_html($post_title ?: 'Titel van je artikel'); ?></div>
            <div class="writgo-seo-preview-url"><?php echo esc_url(get_permalink($post->ID) ?: home_url('/voorbeeld/')); ?></div>
            <div class="writgo-seo-preview-desc" id="writgoSeoPreviewDesc">Meta omschrijving verschijnt hier...</div>
        </div>

        <!-- Noindex / Nofollow -->
        <div class="writgo-seo-field">
            <label>Robot Instellingen</label>
            <div class="writgo-robot-settings">
                <label>
                    <input type="checkbox" name="writgo_noindex" value="1" <?php checked($noindex, '1'); ?> />
                    No Index (verberg voor zoekmachines)
                </label>
                <label>
                    <input type="checkbox" name="writgo_nofollow" value="1" <?php checked($nofollow, '1'); ?> />
                    No Follow (volg geen links)
                </label>
            </div>
        </div>
    </div>

    <script>
    (function(){
        var titleInput = document.getElementById('writgo_seo_title');
        var descInput  = document.getElementById('writgo_seo_description');
        var titleCount = document.getElementById('writgoSeoTitleCount');
        var descCount  = document.getElementById('writgoSeoDescCount');
        var previewT   = document.getElementById('writgoSeoPreviewTitle');
        var previewD   = document.getElementById('writgoSeoPreviewDesc');
        var postTitle  = <?php echo wp_json_encode($post_title); ?>;

        function charCount(input, el, max) {
            var len = input.value.length;
            el.textContent = len + ' / ' + max + ' tekens';
            el.className = 'writgo-seo-char-count';
            if (len === 0) return;
            if (len <= max * 0.6) el.classList.add('warning');
            else if (len <= max) el.classList.add('good');
            else el.classList.add('danger');
        }

        function updatePreview() {
            previewT.textContent = (titleInput.value || postTitle || 'Titel').substring(0, 65);
            previewD.textContent = (descInput.value || 'Meta omschrijving verschijnt hier...').substring(0, 165);
        }

        titleInput.addEventListener('input', function(){ charCount(titleInput, titleCount, 60); updatePreview(); });
        descInput.addEventListener('input', function(){ charCount(descInput, descCount, 160); updatePreview(); });

        charCount(titleInput, titleCount, 60);
        charCount(descInput, descCount, 160);
        updatePreview();
    })();
    </script>
    <?php
}

/**
 * Save SEO Meta Box Data
 */
add_action('save_post', 'writgo_seo_save_meta_box');
function writgo_seo_save_meta_box($post_id) {
    if (!isset($_POST['writgo_seo_nonce']) || !wp_verify_nonce($_POST['writgo_seo_nonce'], 'writgo_seo_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $text_fields = array(
        'writgo_seo_title'       => 'sanitize_text_field',
        'writgo_seo_description' => 'sanitize_textarea_field',
        'writgo_focus_keyword'   => 'sanitize_text_field',
    );

    foreach ($text_fields as $field => $sanitizer) {
        if (isset($_POST[$field])) {
            $value = call_user_func($sanitizer, $_POST[$field]);
            if (!empty($value)) {
                update_post_meta($post_id, '_' . $field, $value);
            } else {
                delete_post_meta($post_id, '_' . $field);
            }
        }
    }

    // Checkboxes
    update_post_meta($post_id, '_writgo_noindex', isset($_POST['writgo_noindex']) ? '1' : '');
    update_post_meta($post_id, '_writgo_nofollow', isset($_POST['writgo_nofollow']) ? '1' : '');
}


// =============================================================================
// 2. SCHEMA META BOX (posts only)
// =============================================================================

add_action('add_meta_boxes', 'writgo_schema_register_meta_box');
function writgo_schema_register_meta_box() {
    if (writgo_seo_plugin_active()) {
        return;
    }
    add_meta_box(
        'writgo_schema_meta_box',
        'Schema Markup (Rich Snippets)',
        'writgo_schema_meta_box_callback',
        'post',
        'normal',
        'default'
    );
}

/**
 * Schema Meta Box Callback
 */
function writgo_schema_meta_box_callback($post) {
    wp_nonce_field('writgo_schema_meta_box', 'writgo_schema_nonce');

    $schema_type          = get_post_meta($post->ID, '_writgo_schema_type', true) ?: 'article';
    $product_name         = get_post_meta($post->ID, '_writgo_product_name', true);
    $product_brand        = get_post_meta($post->ID, '_writgo_product_brand', true);
    $product_price        = get_post_meta($post->ID, '_writgo_product_price', true);
    $product_currency     = get_post_meta($post->ID, '_writgo_product_currency', true) ?: 'EUR';
    $product_availability = get_post_meta($post->ID, '_writgo_product_availability', true) ?: 'InStock';
    $product_condition    = get_post_meta($post->ID, '_writgo_product_condition', true) ?: 'NewCondition';
    $product_url          = get_post_meta($post->ID, '_writgo_product_url', true);
    $review_rating        = get_post_meta($post->ID, '_writgo_review_rating', true);
    $review_pros          = get_post_meta($post->ID, '_writgo_review_pros', true);
    $review_cons          = get_post_meta($post->ID, '_writgo_review_cons', true);
    $faq_items            = get_post_meta($post->ID, '_writgo_faq_items', true) ?: array();
    $howto_time           = get_post_meta($post->ID, '_writgo_howto_time', true);
    $howto_cost           = get_post_meta($post->ID, '_writgo_howto_cost', true);
    ?>

    <style>
        .writgo-schema-box { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 15px 0; }
        .writgo-schema-type-selector { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; padding: 15px; background: #f9fafb; border-radius: 10px; }
        .writgo-schema-type { display: flex; align-items: center; gap: 8px; padding: 12px 18px; background: white; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s; }
        .writgo-schema-type:hover { border-color: #f97316; }
        .writgo-schema-type.active { border-color: #f97316; background: #fff7ed; }
        .writgo-schema-type input { display: none; }
        .writgo-schema-type .s-label { font-weight: 600; color: #374151; font-size: 14px; }
        .writgo-schema-section { display: none; padding: 20px; background: #f9fafb; border-radius: 10px; margin-top: 15px; }
        .writgo-schema-section.active { display: block; }
        .writgo-schema-section h4 { margin: 0 0 15px; color: #1f2937; font-size: 15px; }
        .writgo-schema-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 15px; }
        @media (max-width: 782px) { .writgo-schema-row { grid-template-columns: 1fr; } }
        .writgo-schema-field { display: flex; flex-direction: column; gap: 6px; }
        .writgo-schema-field label { font-weight: 600; font-size: 13px; color: #374151; }
        .writgo-schema-field input,
        .writgo-schema-field select,
        .writgo-schema-field textarea { padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .writgo-schema-field input:focus,
        .writgo-schema-field select:focus,
        .writgo-schema-field textarea:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
        .writgo-schema-field .hint { font-size: 12px; color: #6b7280; }
        .writgo-faq-items { display: flex; flex-direction: column; gap: 15px; }
        .writgo-faq-item { background: white; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .writgo-faq-item input,
        .writgo-faq-item textarea { width: 100%; margin-top: 8px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .writgo-faq-remove { background: #fee2e2; color: #dc2626; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; margin-top: 10px; }
        .writgo-faq-remove:hover { background: #fecaca; }
        .writgo-faq-add { background: #f97316; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; margin-top: 10px; font-size: 14px; }
        .writgo-faq-add:hover { background: #ea580c; }
    </style>

    <div class="writgo-schema-box">
        <p style="color: #6b7280; margin: 0 0 15px; font-size: 14px;">Kies het schema type dat het beste bij je artikel past voor rich snippets in Google.</p>

        <!-- Type Selector -->
        <div class="writgo-schema-type-selector">
            <?php
            $types = array(
                'article'  => 'Artikel',
                'product'  => 'Product Review',
                'faq'      => 'FAQ',
                'howto'    => 'HowTo',
                'listicle' => 'Top Lijst',
            );
            foreach ($types as $value => $label) :
            ?>
            <label class="writgo-schema-type <?php echo $schema_type === $value ? 'active' : ''; ?>">
                <input type="radio" name="writgo_schema_type" value="<?php echo esc_attr($value); ?>" <?php checked($schema_type, $value); ?>>
                <span class="s-label"><?php echo esc_html($label); ?></span>
            </label>
            <?php endforeach; ?>
        </div>

        <!-- Article Section -->
        <div class="writgo-schema-section <?php echo $schema_type === 'article' ? 'active' : ''; ?>" id="schema-article">
            <h4>Artikel Schema</h4>
            <p style="color: #6b7280; font-size: 14px;">Wordt automatisch gegenereerd op basis van je post.</p>
        </div>

        <!-- Product Review Section -->
        <div class="writgo-schema-section <?php echo $schema_type === 'product' ? 'active' : ''; ?>" id="schema-product">
            <h4>Product Review Schema</h4>
            <div class="writgo-schema-row">
                <div class="writgo-schema-field">
                    <label>Product Naam *</label>
                    <input type="text" name="writgo_product_name" value="<?php echo esc_attr($product_name); ?>" placeholder="Samsung Galaxy S24">
                </div>
                <div class="writgo-schema-field">
                    <label>Merk</label>
                    <input type="text" name="writgo_product_brand" value="<?php echo esc_attr($product_brand); ?>" placeholder="Samsung">
                </div>
            </div>
            <div class="writgo-schema-row">
                <div class="writgo-schema-field">
                    <label>Prijs</label>
                    <input type="text" name="writgo_product_price" value="<?php echo esc_attr($product_price); ?>" placeholder="1299.00">
                </div>
                <div class="writgo-schema-field">
                    <label>Valuta</label>
                    <select name="writgo_product_currency">
                        <option value="EUR" <?php selected($product_currency, 'EUR'); ?>>EUR</option>
                        <option value="USD" <?php selected($product_currency, 'USD'); ?>>USD</option>
                        <option value="GBP" <?php selected($product_currency, 'GBP'); ?>>GBP</option>
                    </select>
                </div>
            </div>
            <div class="writgo-schema-row">
                <div class="writgo-schema-field">
                    <label>Beschikbaarheid</label>
                    <select name="writgo_product_availability">
                        <option value="InStock" <?php selected($product_availability, 'InStock'); ?>>Op voorraad</option>
                        <option value="OutOfStock" <?php selected($product_availability, 'OutOfStock'); ?>>Niet op voorraad</option>
                        <option value="PreOrder" <?php selected($product_availability, 'PreOrder'); ?>>Pre-order</option>
                    </select>
                </div>
                <div class="writgo-schema-field">
                    <label>Conditie</label>
                    <select name="writgo_product_condition">
                        <option value="NewCondition" <?php selected($product_condition, 'NewCondition'); ?>>Nieuw</option>
                        <option value="UsedCondition" <?php selected($product_condition, 'UsedCondition'); ?>>Gebruikt</option>
                        <option value="RefurbishedCondition" <?php selected($product_condition, 'RefurbishedCondition'); ?>>Refurbished</option>
                    </select>
                </div>
            </div>
            <div class="writgo-schema-row">
                <div class="writgo-schema-field">
                    <label>Beoordeling (1-5)</label>
                    <input type="number" name="writgo_review_rating" value="<?php echo esc_attr($review_rating); ?>" min="1" max="5" step="0.1" placeholder="4.5">
                    <span class="hint">Toont sterren in Google zoekresultaten</span>
                </div>
                <div class="writgo-schema-field">
                    <label>Affiliate URL</label>
                    <input type="url" name="writgo_product_url" value="<?php echo esc_attr($product_url); ?>" placeholder="https://...">
                </div>
            </div>
            <div class="writgo-schema-row">
                <div class="writgo-schema-field">
                    <label>Voordelen (een per regel)</label>
                    <textarea name="writgo_review_pros" rows="3" placeholder="Uitstekende camera&#10;Lange batterijduur"><?php echo esc_textarea($review_pros); ?></textarea>
                </div>
                <div class="writgo-schema-field">
                    <label>Nadelen (een per regel)</label>
                    <textarea name="writgo_review_cons" rows="3" placeholder="Hoge prijs&#10;Geen jack"><?php echo esc_textarea($review_cons); ?></textarea>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="writgo-schema-section <?php echo $schema_type === 'faq' ? 'active' : ''; ?>" id="schema-faq">
            <h4>FAQ Schema</h4>
            <div class="writgo-faq-items" id="writgoFaqItems">
                <?php if (!empty($faq_items) && is_array($faq_items)) : foreach ($faq_items as $i => $faq) : ?>
                <div class="writgo-faq-item">
                    <label><strong>Vraag <?php echo $i + 1; ?></strong></label>
                    <input type="text" name="writgo_faq_question[]" value="<?php echo esc_attr($faq['question'] ?? ''); ?>" placeholder="Stel hier je vraag...">
                    <label style="margin-top:10px"><strong>Antwoord</strong></label>
                    <textarea name="writgo_faq_answer[]" rows="2" placeholder="Geef hier het antwoord..."><?php echo esc_textarea($faq['answer'] ?? ''); ?></textarea>
                    <button type="button" class="writgo-faq-remove" onclick="this.parentElement.remove()">Verwijderen</button>
                </div>
                <?php endforeach; endif; ?>
            </div>
            <button type="button" class="writgo-faq-add" onclick="writgoAddFaqItem()">+ Vraag toevoegen</button>
        </div>

        <!-- HowTo Section -->
        <div class="writgo-schema-section <?php echo $schema_type === 'howto' ? 'active' : ''; ?>" id="schema-howto">
            <h4>How-To Schema</h4>
            <p style="color:#6b7280; margin:0 0 15px; font-size:14px;">Stappen worden automatisch uit H2/H3 koppen gehaald.</p>
            <div class="writgo-schema-row">
                <div class="writgo-schema-field">
                    <label>Geschatte Tijd (minuten)</label>
                    <input type="number" name="writgo_howto_time" value="<?php echo esc_attr($howto_time); ?>" placeholder="30">
                </div>
                <div class="writgo-schema-field">
                    <label>Geschatte Kosten</label>
                    <input type="text" name="writgo_howto_cost" value="<?php echo esc_attr($howto_cost); ?>" placeholder="50">
                    <span class="hint">Alleen het bedrag, zonder valutateken</span>
                </div>
            </div>
        </div>

        <!-- Top Lijst / Listicle Section -->
        <div class="writgo-schema-section <?php echo $schema_type === 'listicle' ? 'active' : ''; ?>" id="schema-listicle">
            <h4>Top Lijst Schema</h4>
            <p style="color:#6b7280; font-size:14px;">Items worden automatisch uit H2 koppen gehaald als ItemList.</p>
        </div>
    </div>

    <script>
    /* Schema type switcher */
    document.querySelectorAll('.writgo-schema-type input').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.writgo-schema-type').forEach(function(t){ t.classList.remove('active'); });
            this.parentElement.classList.add('active');
            document.querySelectorAll('.writgo-schema-section').forEach(function(s){ s.classList.remove('active'); });
            var target = document.getElementById('schema-' + this.value);
            if (target) target.classList.add('active');
        });
    });

    /* Add FAQ item */
    function writgoAddFaqItem() {
        var container = document.getElementById('writgoFaqItems');
        var num = container.querySelectorAll('.writgo-faq-item').length + 1;
        var item = document.createElement('div');
        item.className = 'writgo-faq-item';
        item.innerHTML = '<label><strong>Vraag ' + num + '</strong></label>'
            + '<input type="text" name="writgo_faq_question[]" placeholder="Stel hier je vraag...">'
            + '<label style="margin-top:10px"><strong>Antwoord</strong></label>'
            + '<textarea name="writgo_faq_answer[]" rows="2" placeholder="Geef hier het antwoord..."></textarea>'
            + '<button type="button" class="writgo-faq-remove" onclick="this.parentElement.remove()">Verwijderen</button>';
        container.appendChild(item);
    }
    </script>
    <?php
}

/**
 * Save Schema Meta Box Data
 */
add_action('save_post', 'writgo_schema_save_meta_box');
function writgo_schema_save_meta_box($post_id) {
    if (!isset($_POST['writgo_schema_nonce']) || !wp_verify_nonce($_POST['writgo_schema_nonce'], 'writgo_schema_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Schema type
    if (isset($_POST['writgo_schema_type'])) {
        update_post_meta($post_id, '_writgo_schema_type', sanitize_text_field($_POST['writgo_schema_type']));
    }

    // Product / review fields
    $product_fields = array(
        'writgo_product_name',
        'writgo_product_brand',
        'writgo_product_price',
        'writgo_product_currency',
        'writgo_product_availability',
        'writgo_product_condition',
        'writgo_product_url',
        'writgo_review_rating',
        'writgo_review_pros',
        'writgo_review_cons',
        'writgo_howto_time',
        'writgo_howto_cost',
    );
    foreach ($product_fields as $field) {
        if (isset($_POST[$field])) {
            $value = sanitize_text_field($_POST[$field]);
            if (!empty($value)) {
                update_post_meta($post_id, '_' . $field, $value);
            } else {
                delete_post_meta($post_id, '_' . $field);
            }
        }
    }

    // FAQ items
    if (isset($_POST['writgo_faq_question']) && isset($_POST['writgo_faq_answer'])) {
        $questions = $_POST['writgo_faq_question'];
        $answers   = $_POST['writgo_faq_answer'];
        $items     = array();
        for ($i = 0; $i < count($questions); $i++) {
            $q = sanitize_text_field($questions[$i]);
            $a = sanitize_textarea_field($answers[$i]);
            if (!empty($q) && !empty($a)) {
                $items[] = array('question' => $q, 'answer' => $a);
            }
        }
        update_post_meta($post_id, '_writgo_faq_items', $items);
    } else {
        delete_post_meta($post_id, '_writgo_faq_items');
    }
}


// =============================================================================
// 3. SOCIAL META BOX (posts + pages)
// =============================================================================

add_action('add_meta_boxes', 'writgo_social_register_meta_box');
function writgo_social_register_meta_box() {
    if (writgo_seo_plugin_active()) {
        return;
    }
    add_meta_box(
        'writgo_social_meta_box',
        'Social Media SEO',
        'writgo_social_meta_box_callback',
        array('post', 'page'),
        'normal',
        'default'
    );
}

/**
 * Social Meta Box Callback
 */
function writgo_social_meta_box_callback($post) {
    wp_nonce_field('writgo_social_meta_box', 'writgo_social_nonce');

    $og_title            = get_post_meta($post->ID, '_writgo_og_title', true);
    $og_description      = get_post_meta($post->ID, '_writgo_og_description', true);
    $og_image            = get_post_meta($post->ID, '_writgo_og_image', true);
    $twitter_title       = get_post_meta($post->ID, '_writgo_twitter_title', true);
    $twitter_description = get_post_meta($post->ID, '_writgo_twitter_description', true);
    $twitter_image       = get_post_meta($post->ID, '_writgo_twitter_image', true);

    $default_title = get_the_title($post->ID);
    $default_desc  = has_excerpt($post->ID) ? get_the_excerpt($post->ID) : wp_trim_words(strip_tags($post->post_content), 30);
    $default_image = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID, 'large') : '';
    ?>

    <style>
        .writgo-social-box { padding: 15px 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .writgo-social-tabs { display: flex; gap: 0; margin-bottom: 20px; }
        .writgo-social-tab { padding: 12px 20px; cursor: pointer; background: #f3f4f6; border: none; font-weight: 500; font-size: 14px; transition: all 0.2s; color: #374151; }
        .writgo-social-tab:first-child { border-radius: 8px 0 0 8px; }
        .writgo-social-tab:last-child { border-radius: 0 8px 8px 0; }
        .writgo-social-tab.active { background: #1877f2; color: white; }
        .writgo-social-tab.writgo-twitter-tab.active { background: #000; color: white; }
        .writgo-social-panel { display: none; }
        .writgo-social-panel.active { display: block; }
        .writgo-social-row { margin-bottom: 15px; }
        .writgo-social-row label { display: block; font-weight: 600; margin-bottom: 6px; color: #374151; font-size: 14px; }
        .writgo-social-row input,
        .writgo-social-row textarea { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .writgo-social-row input:focus,
        .writgo-social-row textarea:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
        .writgo-social-tip { font-size: 12px; color: #9ca3af; margin-top: 4px; }
        .writgo-image-upload { display: flex; gap: 10px; align-items: flex-start; }
        .writgo-image-upload input { flex: 1; }
        .writgo-upload-btn { background: #f97316; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; white-space: nowrap; font-size: 14px; }
        .writgo-upload-btn:hover { background: #ea580c; }
        .writgo-social-preview { margin-top: 25px; padding-top: 25px; border-top: 1px solid #e5e7eb; }
        .writgo-social-preview h4 { margin: 0 0 15px; color: #374151; font-size: 14px; font-weight: 600; }

        /* Facebook Preview */
        .writgo-fb-preview { max-width: 500px; border: 1px solid #dddfe2; border-radius: 3px; overflow: hidden; font-family: Helvetica, Arial, sans-serif; background: white; }
        .writgo-fb-preview-image { width: 100%; height: 260px; background: #f0f2f5; background-size: cover; background-position: center; }
        .writgo-fb-preview-content { padding: 10px 12px; border-top: 1px solid #dddfe2; }
        .writgo-fb-preview-domain { font-size: 12px; color: #606770; text-transform: uppercase; margin-bottom: 5px; }
        .writgo-fb-preview-title { font-size: 16px; font-weight: 600; color: #1d2129; line-height: 1.3; margin-bottom: 5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .writgo-fb-preview-desc { font-size: 14px; color: #606770; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }

        /* Twitter Preview */
        .writgo-tw-preview { max-width: 500px; border: 1px solid #cfd9de; border-radius: 16px; overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: white; }
        .writgo-tw-preview-image { width: 100%; height: 250px; background: #f7f9f9; background-size: cover; background-position: center; }
        .writgo-tw-preview-content { padding: 12px; }
        .writgo-tw-preview-domain { font-size: 13px; color: #536471; margin-bottom: 2px; }
        .writgo-tw-preview-title { font-size: 15px; font-weight: 400; color: #0f1419; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>

    <div class="writgo-social-box">
        <!-- Tab Buttons -->
        <div class="writgo-social-tabs">
            <button type="button" class="writgo-social-tab active" data-social-tab="facebook">Facebook / LinkedIn</button>
            <button type="button" class="writgo-social-tab writgo-twitter-tab" data-social-tab="twitter">Twitter / X</button>
        </div>

        <!-- Facebook / OG Panel -->
        <div class="writgo-social-panel active" id="writgo-social-facebook">
            <div class="writgo-social-row">
                <label>Facebook Titel</label>
                <input type="text" name="writgo_og_title" id="writgo_og_title"
                       value="<?php echo esc_attr($og_title); ?>"
                       placeholder="<?php echo esc_attr($default_title); ?>">
                <p class="writgo-social-tip">Laat leeg om de SEO titel te gebruiken</p>
            </div>
            <div class="writgo-social-row">
                <label>Facebook Beschrijving</label>
                <textarea name="writgo_og_description" id="writgo_og_description" rows="2"
                          placeholder="<?php echo esc_attr($default_desc); ?>"><?php echo esc_textarea($og_description); ?></textarea>
                <p class="writgo-social-tip">Laat leeg om de meta omschrijving te gebruiken</p>
            </div>
            <div class="writgo-social-row">
                <label>Facebook Afbeelding</label>
                <div class="writgo-image-upload">
                    <input type="url" name="writgo_og_image" id="writgo_og_image"
                           value="<?php echo esc_attr($og_image); ?>"
                           placeholder="<?php echo esc_attr($default_image); ?>">
                    <button type="button" class="writgo-upload-btn" onclick="writgoSocialUpload('og_image')">Upload</button>
                </div>
                <p class="writgo-social-tip">Aanbevolen: 1200 x 630 pixels. Laat leeg voor uitgelichte afbeelding.</p>
            </div>

            <!-- Facebook Live Preview -->
            <div class="writgo-social-preview">
                <h4>Facebook Preview</h4>
                <div class="writgo-fb-preview">
                    <div class="writgo-fb-preview-image" id="writgoFbPreviewImg"
                         style="background-image: url('<?php echo esc_url($og_image ?: $default_image); ?>')"></div>
                    <div class="writgo-fb-preview-content">
                        <div class="writgo-fb-preview-domain"><?php echo esc_html(wp_parse_url(home_url(), PHP_URL_HOST)); ?></div>
                        <div class="writgo-fb-preview-title" id="writgoFbPreviewTitle"><?php echo esc_html($og_title ?: $default_title); ?></div>
                        <div class="writgo-fb-preview-desc" id="writgoFbPreviewDesc"><?php echo esc_html($og_description ?: $default_desc); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Twitter Panel -->
        <div class="writgo-social-panel" id="writgo-social-twitter">
            <div class="writgo-social-row">
                <label>Twitter Titel</label>
                <input type="text" name="writgo_twitter_title" id="writgo_twitter_title"
                       value="<?php echo esc_attr($twitter_title); ?>"
                       placeholder="<?php echo esc_attr($default_title); ?>">
                <p class="writgo-social-tip">Laat leeg om de Facebook titel te gebruiken</p>
            </div>
            <div class="writgo-social-row">
                <label>Twitter Beschrijving</label>
                <textarea name="writgo_twitter_description" id="writgo_twitter_description" rows="2"
                          placeholder="<?php echo esc_attr($default_desc); ?>"><?php echo esc_textarea($twitter_description); ?></textarea>
                <p class="writgo-social-tip">Laat leeg om de Facebook beschrijving te gebruiken</p>
            </div>
            <div class="writgo-social-row">
                <label>Twitter Afbeelding</label>
                <div class="writgo-image-upload">
                    <input type="url" name="writgo_twitter_image" id="writgo_twitter_image"
                           value="<?php echo esc_attr($twitter_image); ?>"
                           placeholder="<?php echo esc_attr($default_image); ?>">
                    <button type="button" class="writgo-upload-btn" onclick="writgoSocialUpload('twitter_image')">Upload</button>
                </div>
                <p class="writgo-social-tip">Aanbevolen: 1200 x 675 pixels (16:9)</p>
            </div>

            <!-- Twitter Live Preview -->
            <div class="writgo-social-preview">
                <h4>Twitter Preview</h4>
                <div class="writgo-tw-preview">
                    <div class="writgo-tw-preview-image" id="writgoTwPreviewImg"
                         style="background-image: url('<?php echo esc_url($twitter_image ?: $og_image ?: $default_image); ?>')"></div>
                    <div class="writgo-tw-preview-content">
                        <div class="writgo-tw-preview-title" id="writgoTwPreviewTitle"><?php echo esc_html($twitter_title ?: $og_title ?: $default_title); ?></div>
                        <div class="writgo-tw-preview-domain"><?php echo esc_html(wp_parse_url(home_url(), PHP_URL_HOST)); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function(){
        /* Tab switching */
        document.querySelectorAll('.writgo-social-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.writgo-social-tab').forEach(function(t){ t.classList.remove('active'); });
                document.querySelectorAll('.writgo-social-panel').forEach(function(p){ p.classList.remove('active'); });
                this.classList.add('active');
                var panel = document.getElementById('writgo-social-' + this.getAttribute('data-social-tab'));
                if (panel) panel.classList.add('active');
            });
        });

        /* Live preview */
        var ogT  = document.getElementById('writgo_og_title');
        var ogD  = document.getElementById('writgo_og_description');
        var ogI  = document.getElementById('writgo_og_image');
        var twT  = document.getElementById('writgo_twitter_title');
        var twI  = document.getElementById('writgo_twitter_image');

        var fbPT = document.getElementById('writgoFbPreviewTitle');
        var fbPD = document.getElementById('writgoFbPreviewDesc');
        var fbPI = document.getElementById('writgoFbPreviewImg');
        var twPT = document.getElementById('writgoTwPreviewTitle');
        var twPI = document.getElementById('writgoTwPreviewImg');

        var defT = <?php echo wp_json_encode($default_title); ?>;
        var defD = <?php echo wp_json_encode($default_desc); ?>;
        var defI = <?php echo wp_json_encode($default_image); ?>;

        function refreshPreviews() {
            fbPT.textContent = ogT.value || defT;
            fbPD.textContent = ogD.value || defD;
            fbPI.style.backgroundImage = 'url(' + (ogI.value || defI) + ')';
            twPT.textContent = twT.value || ogT.value || defT;
            twPI.style.backgroundImage = 'url(' + (twI.value || ogI.value || defI) + ')';
        }

        ogT.addEventListener('input', refreshPreviews);
        ogD.addEventListener('input', refreshPreviews);
        ogI.addEventListener('input', refreshPreviews);
        twT.addEventListener('input', refreshPreviews);
        twI.addEventListener('input', refreshPreviews);
    })();

    /* Media uploader */
    function writgoSocialUpload(field) {
        if (typeof wp === 'undefined' || !wp.media) return;
        var uploader = wp.media({
            title: 'Kies afbeelding',
            button: { text: 'Selecteer' },
            multiple: false
        });
        uploader.on('select', function() {
            var attachment = uploader.state().get('selection').first().toJSON();
            var input = document.getElementById('writgo_' + field);
            input.value = attachment.url;
            input.dispatchEvent(new Event('input'));
        });
        uploader.open();
    }
    </script>
    <?php
}

/**
 * Save Social Meta Box Data
 */
add_action('save_post', 'writgo_social_save_meta_box');
function writgo_social_save_meta_box($post_id) {
    if (!isset($_POST['writgo_social_nonce']) || !wp_verify_nonce($_POST['writgo_social_nonce'], 'writgo_social_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array(
        'writgo_og_title',
        'writgo_og_description',
        'writgo_og_image',
        'writgo_twitter_title',
        'writgo_twitter_description',
        'writgo_twitter_image',
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $is_textarea = in_array($field, array('writgo_og_description', 'writgo_twitter_description'));
            $value = $is_textarea ? sanitize_textarea_field($_POST[$field]) : sanitize_text_field($_POST[$field]);
            if (!empty($value)) {
                update_post_meta($post_id, '_' . $field, $value);
            } else {
                delete_post_meta($post_id, '_' . $field);
            }
        }
    }
}


// =============================================================================
// 4. META OUTPUT (wp_head)
// =============================================================================

/**
 * Output all SEO meta tags in <head>
 *
 * Handles: description, robots, canonical, Open Graph, article timestamps,
 * Twitter Card, and rel prev/next for archives.
 */
add_action('wp_head', 'writgo_seo_head_output', 1);
function writgo_seo_head_output() {
    if (writgo_seo_plugin_active()) {
        return;
    }

    global $post;

    // Defaults
    $title       = get_bloginfo('name');
    $description = get_bloginfo('description');
    $url         = home_url('/');
    $image       = get_site_icon_url(512);
    $robots      = array('index', 'follow');
    $og_type     = 'website';

    // Social overrides (start with defaults)
    $og_title            = $title;
    $og_description      = $description;
    $og_image            = $image;
    $twitter_title       = $title;
    $twitter_description = $description;
    $twitter_image       = $image;

    // -------------------------------------------------------------------------
    // Front Page
    // -------------------------------------------------------------------------
    if (is_front_page()) {
        $title       = get_bloginfo('name') . ' - ' . get_bloginfo('description');
        $description = get_bloginfo('description');
        $url         = home_url('/');
        $og_type     = 'website';

        // If a static front page has custom SEO values
        if ($post && $post->ID) {
            $seo_title = get_post_meta($post->ID, '_writgo_seo_title', true);
            $seo_desc  = get_post_meta($post->ID, '_writgo_seo_description', true);
            if ($seo_title) $title = $seo_title;
            if ($seo_desc) $description = $seo_desc;

            if (has_post_thumbnail($post->ID)) {
                $image = get_the_post_thumbnail_url($post->ID, 'large');
            }

            $custom_og_title = get_post_meta($post->ID, '_writgo_og_title', true);
            $custom_og_desc  = get_post_meta($post->ID, '_writgo_og_description', true);
            $custom_og_img   = get_post_meta($post->ID, '_writgo_og_image', true);
            if ($custom_og_img) $image = $custom_og_img;
        }

        $og_title       = !empty($custom_og_title) ? $custom_og_title : $title;
        $og_description = !empty($custom_og_desc) ? $custom_og_desc : $description;
        $og_image       = $image;

        $twitter_title       = $og_title;
        $twitter_description = $og_description;
        $twitter_image       = $og_image;

    // -------------------------------------------------------------------------
    // Singular (posts & pages, not front page)
    // -------------------------------------------------------------------------
    } elseif (is_singular()) {
        $seo_title = get_post_meta($post->ID, '_writgo_seo_title', true);
        $seo_desc  = get_post_meta($post->ID, '_writgo_seo_description', true);
        $noindex   = get_post_meta($post->ID, '_writgo_noindex', true);
        $nofollow  = get_post_meta($post->ID, '_writgo_nofollow', true);

        $title       = $seo_title ?: get_the_title();
        $description = $seo_desc ?: (has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_tags($post->post_content), 30));
        $url         = get_permalink();
        $og_type     = 'article';

        if (has_post_thumbnail()) {
            $image = get_the_post_thumbnail_url($post->ID, 'large');
        }

        // Social overrides from meta box
        $custom_og_title   = get_post_meta($post->ID, '_writgo_og_title', true);
        $custom_og_desc    = get_post_meta($post->ID, '_writgo_og_description', true);
        $custom_og_img     = get_post_meta($post->ID, '_writgo_og_image', true);
        $custom_tw_title   = get_post_meta($post->ID, '_writgo_twitter_title', true);
        $custom_tw_desc    = get_post_meta($post->ID, '_writgo_twitter_description', true);
        $custom_tw_img     = get_post_meta($post->ID, '_writgo_twitter_image', true);

        $og_title       = $custom_og_title ?: $title;
        $og_description = $custom_og_desc ?: $description;
        $og_image       = $custom_og_img ?: $image;

        $twitter_title       = $custom_tw_title ?: $og_title;
        $twitter_description = $custom_tw_desc ?: $og_description;
        $twitter_image       = $custom_tw_img ?: $og_image;

        if ($noindex === '1') $robots[0] = 'noindex';
        if ($nofollow === '1') $robots[1] = 'nofollow';

    // -------------------------------------------------------------------------
    // Category
    // -------------------------------------------------------------------------
    } elseif (is_category()) {
        $cat_name    = single_cat_title('', false);
        $cat_desc    = category_description();
        $title       = $cat_name . ' | ' . get_bloginfo('name');
        $description = $cat_desc ?: 'Artikelen over ' . $cat_name . ' op ' . get_bloginfo('name');
        $url         = get_category_link(get_queried_object_id());
        $og_title    = $twitter_title = $title;
        $og_description = $twitter_description = writgo_clean_description($description);

    // -------------------------------------------------------------------------
    // Tag
    // -------------------------------------------------------------------------
    } elseif (is_tag()) {
        $tag_name    = single_tag_title('', false);
        $title       = $tag_name . ' | ' . get_bloginfo('name');
        $description = tag_description() ?: 'Artikelen met tag ' . $tag_name . ' op ' . get_bloginfo('name');
        $url         = get_tag_link(get_queried_object_id());
        $og_title    = $twitter_title = $title;
        $og_description = $twitter_description = writgo_clean_description($description);

    // -------------------------------------------------------------------------
    // Author
    // -------------------------------------------------------------------------
    } elseif (is_author()) {
        $author_name = get_the_author();
        $title       = $author_name . ' | ' . get_bloginfo('name');
        $description = get_the_author_meta('description') ?: 'Artikelen door ' . $author_name;
        $url         = get_author_posts_url(get_queried_object_id());
        $og_title    = $twitter_title = $title;
        $og_description = $twitter_description = $description;

    // -------------------------------------------------------------------------
    // Search
    // -------------------------------------------------------------------------
    } elseif (is_search()) {
        $title  = 'Zoekresultaten voor: ' . get_search_query();
        $robots = array('noindex', 'follow');

    // -------------------------------------------------------------------------
    // Other archives (date, post type, etc.)
    // -------------------------------------------------------------------------
    } elseif (is_archive()) {
        $title       = get_the_archive_title() . ' | ' . get_bloginfo('name');
        $description = get_the_archive_description() ?: get_bloginfo('description');
        $og_title    = $twitter_title = $title;
        $og_description = $twitter_description = writgo_clean_description($description);
    }

    // Clean all descriptions
    $description         = writgo_clean_description($description);
    $og_description      = writgo_clean_description($og_description);
    $twitter_description = writgo_clean_description($twitter_description);

    // Locale
    $locale = writgo_get_og_locale();

    // ---- Output Meta Tags ----
    echo "\n<!-- Writgo SEO -->\n";

    // Meta description
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";

    // Robots
    echo '<meta name="robots" content="' . esc_attr(implode(', ', $robots)) . '">' . "\n";

    // Canonical
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    } elseif (is_category()) {
        echo '<link rel="canonical" href="' . esc_url(get_category_link(get_queried_object_id())) . '">' . "\n";
    } elseif (is_tag()) {
        echo '<link rel="canonical" href="' . esc_url(get_tag_link(get_queried_object_id())) . '">' . "\n";
    }

    // Open Graph
    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($og_description) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:locale" content="' . esc_attr($locale) . '">' . "\n";

    if (!empty($og_image)) {
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta property="og:image:width" content="1200">' . "\n";
        echo '<meta property="og:image:height" content="630">' . "\n";
    }

    // Article timestamps (only for singular posts, not pages)
    if (is_singular('post')) {
        echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c')) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c')) . '">' . "\n";
        echo '<meta property="article:author" content="' . esc_attr(get_the_author()) . '">' . "\n";
    }

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($twitter_title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($twitter_description) . '">' . "\n";

    if (!empty($twitter_image)) {
        echo '<meta name="twitter:image" content="' . esc_url($twitter_image) . '">' . "\n";
    }

    // Pagination: rel prev/next for archives
    if (is_paged() || is_archive() || is_home()) {
        global $wp_query;
        $paged     = max(1, get_query_var('paged'));
        $max_pages = $wp_query->max_num_pages;

        if ($paged > 1) {
            echo '<link rel="prev" href="' . esc_url(get_pagenum_link($paged - 1)) . '">' . "\n";
        }
        if ($paged < $max_pages) {
            echo '<link rel="next" href="' . esc_url(get_pagenum_link($paged + 1)) . '">' . "\n";
        }
    }

    echo "<!-- / Writgo SEO -->\n\n";
}

/**
 * Filter document title when using Writgo SEO
 */
add_filter('pre_get_document_title', 'writgo_seo_document_title', 20);
function writgo_seo_document_title($title) {
    if (writgo_seo_plugin_active()) {
        return $title;
    }
    if (is_singular()) {
        global $post;
        $seo_title = get_post_meta($post->ID, '_writgo_seo_title', true);
        if ($seo_title) {
            return $seo_title . ' | ' . get_bloginfo('name');
        }
    }
    return $title;
}

/**
 * Optimize title tag separator
 */
add_filter('document_title_separator', 'writgo_seo_title_separator');
function writgo_seo_title_separator() {
    return '|';
}


// =============================================================================
// 5. SCHEMA OUTPUT (wp_head)
// =============================================================================

/**
 * Output structured data based on schema type selected in meta box
 */
add_action('wp_head', 'writgo_seo_schema_output', 5);
function writgo_seo_schema_output() {
    if (writgo_seo_plugin_active()) {
        return;
    }
    if (!is_singular('post')) {
        return;
    }

    global $post;
    $type = get_post_meta($post->ID, '_writgo_schema_type', true) ?: 'article';

    switch ($type) {
        case 'product':
            writgo_seo_render_product_schema($post);
            break;
        case 'faq':
            writgo_seo_render_faq_schema($post);
            writgo_seo_render_article_schema($post);
            break;
        case 'howto':
            writgo_seo_render_howto_schema($post);
            break;
        case 'listicle':
            writgo_seo_render_itemlist_schema($post);
            writgo_seo_render_article_schema($post);
            break;
        default:
            writgo_seo_render_article_schema($post);
            break;
    }
}

/**
 * Render Article schema
 */
function writgo_seo_render_article_schema($post) {
    $schema = array(
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        'headline'         => get_the_title($post),
        'description'      => has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(strip_tags($post->post_content), 30),
        'datePublished'    => get_the_date('c', $post),
        'dateModified'     => get_the_modified_date('c', $post),
        'url'              => get_permalink($post),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id'   => get_permalink($post),
        ),
        'author'           => array(
            '@type' => 'Person',
            'name'  => get_the_author_meta('display_name', $post->post_author),
        ),
        'publisher'        => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
            'logo'  => array(
                '@type' => 'ImageObject',
                'url'   => get_site_icon_url(512) ?: '',
            ),
        ),
    );

    if (has_post_thumbnail($post)) {
        $img = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'full');
        if ($img) {
            $schema['image'] = array(
                '@type'  => 'ImageObject',
                'url'    => $img[0],
                'width'  => $img[1],
                'height' => $img[2],
            );
        }
    }

    $keyword = get_post_meta($post->ID, '_writgo_focus_keyword', true);
    if ($keyword) {
        $schema['keywords'] = $keyword;
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * Render Product schema (with review, offers, aggregateRating, pros/cons)
 */
function writgo_seo_render_product_schema($post) {
    $name = get_post_meta($post->ID, '_writgo_product_name', true);
    if (empty($name)) {
        writgo_seo_render_article_schema($post);
        return;
    }

    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => $name,
        'description' => has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(strip_tags($post->post_content), 50),
    );

    $brand = get_post_meta($post->ID, '_writgo_product_brand', true);
    if ($brand) {
        $schema['brand'] = array('@type' => 'Brand', 'name' => $brand);
    }

    if (has_post_thumbnail($post)) {
        $schema['image'] = get_the_post_thumbnail_url($post, 'large');
    }

    // Offers
    $price = get_post_meta($post->ID, '_writgo_product_price', true);
    if ($price) {
        $currency     = get_post_meta($post->ID, '_writgo_product_currency', true) ?: 'EUR';
        $availability = get_post_meta($post->ID, '_writgo_product_availability', true) ?: 'InStock';
        $condition    = get_post_meta($post->ID, '_writgo_product_condition', true) ?: 'NewCondition';
        $product_url  = get_post_meta($post->ID, '_writgo_product_url', true) ?: get_permalink($post);

        $schema['offers'] = array(
            '@type'         => 'Offer',
            'price'         => $price,
            'priceCurrency' => $currency,
            'availability'  => 'https://schema.org/' . $availability,
            'itemCondition' => 'https://schema.org/' . $condition,
            'url'           => $product_url,
        );
    }

    // Review + rating
    $rating = get_post_meta($post->ID, '_writgo_review_rating', true);
    if ($rating) {
        $review = array(
            '@type'        => 'Review',
            'reviewRating' => array(
                '@type'       => 'Rating',
                'ratingValue' => floatval($rating),
                'bestRating'  => 5,
            ),
            'author'        => array(
                '@type' => 'Organization',
                'name'  => get_bloginfo('name'),
            ),
            'datePublished' => get_the_date('c', $post),
        );

        // Pros (positiveNotes)
        $pros = get_post_meta($post->ID, '_writgo_review_pros', true);
        if ($pros) {
            $pros_arr = array_values(array_filter(array_map('trim', explode("\n", $pros))));
            if (!empty($pros_arr)) {
                $review['positiveNotes'] = array(
                    '@type'           => 'ItemList',
                    'itemListElement' => array(),
                );
                foreach ($pros_arr as $i => $pro) {
                    $review['positiveNotes']['itemListElement'][] = array(
                        '@type'    => 'ListItem',
                        'position' => $i + 1,
                        'name'     => $pro,
                    );
                }
            }
        }

        // Cons (negativeNotes)
        $cons = get_post_meta($post->ID, '_writgo_review_cons', true);
        if ($cons) {
            $cons_arr = array_values(array_filter(array_map('trim', explode("\n", $cons))));
            if (!empty($cons_arr)) {
                $review['negativeNotes'] = array(
                    '@type'           => 'ItemList',
                    'itemListElement' => array(),
                );
                foreach ($cons_arr as $i => $con) {
                    $review['negativeNotes']['itemListElement'][] = array(
                        '@type'    => 'ListItem',
                        'position' => $i + 1,
                        'name'     => $con,
                    );
                }
            }
        }

        $schema['review'] = $review;

        // Aggregate rating
        $schema['aggregateRating'] = array(
            '@type'       => 'AggregateRating',
            'ratingValue' => floatval($rating),
            'bestRating'  => 5,
            'reviewCount' => 1,
        );
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * Render FAQ schema (FAQPage with Question/Answer pairs)
 */
function writgo_seo_render_faq_schema($post) {
    $items = get_post_meta($post->ID, '_writgo_faq_items', true);
    if (empty($items) || !is_array($items)) {
        return;
    }

    $schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array(),
    );

    foreach ($items as $faq) {
        if (!empty($faq['question']) && !empty($faq['answer'])) {
            $schema['mainEntity'][] = array(
                '@type'          => 'Question',
                'name'           => $faq['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => $faq['answer'],
                ),
            );
        }
    }

    if (!empty($schema['mainEntity'])) {
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}

/**
 * Render HowTo schema (steps from H2/H3 headings, totalTime, estimatedCost)
 */
function writgo_seo_render_howto_schema($post) {
    preg_match_all('/<h[23][^>]*>(.*?)<\/h[23]>/si', $post->post_content, $matches);
    if (empty($matches[1])) {
        writgo_seo_render_article_schema($post);
        return;
    }

    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'HowTo',
        'name'        => get_the_title($post),
        'description' => has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(strip_tags($post->post_content), 50),
        'step'        => array(),
    );

    // Total time
    $time = get_post_meta($post->ID, '_writgo_howto_time', true);
    if ($time) {
        $schema['totalTime'] = 'PT' . intval($time) . 'M';
    }

    // Estimated cost
    $cost = get_post_meta($post->ID, '_writgo_howto_cost', true);
    if ($cost) {
        $schema['estimatedCost'] = array(
            '@type'    => 'MonetaryAmount',
            'currency' => 'EUR',
            'value'    => preg_replace('/[^0-9.]/', '', $cost),
        );
    }

    if (has_post_thumbnail($post)) {
        $schema['image'] = get_the_post_thumbnail_url($post, 'large');
    }

    foreach ($matches[1] as $i => $step) {
        $schema['step'][] = array(
            '@type'    => 'HowToStep',
            'position' => $i + 1,
            'name'     => strip_tags($step),
            'text'     => strip_tags($step),
        );
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * Render ItemList schema for top lists (items from H2 headings)
 */
function writgo_seo_render_itemlist_schema($post) {
    preg_match_all('/<h2[^>]*>(.*?)<\/h2>/si', $post->post_content, $matches);
    if (empty($matches[1])) {
        return;
    }

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        'name'            => get_the_title($post),
        'numberOfItems'   => count($matches[1]),
        'itemListElement' => array(),
    );

    foreach ($matches[1] as $i => $item) {
        $schema['itemListElement'][] = array(
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => strip_tags($item),
            'url'      => get_permalink($post) . '#' . sanitize_title($item),
        );
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}


// =============================================================================
// BREADCRUMB SCHEMA (all pages except front page)
// =============================================================================

add_action('wp_head', 'writgo_seo_breadcrumb_schema', 6);
function writgo_seo_breadcrumb_schema() {
    if (writgo_seo_plugin_active()) {
        return;
    }
    if (is_front_page() || is_home()) {
        return;
    }

    $crumbs   = array();
    $position = 1;

    // Home
    $crumbs[] = array(
        '@type'    => 'ListItem',
        'position' => $position++,
        'name'     => 'Home',
        'item'     => home_url('/'),
    );

    if (is_singular('post')) {
        $cats = get_the_category();
        if (!empty($cats)) {
            $crumbs[] = array(
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $cats[0]->name,
                'item'     => get_category_link($cats[0]->term_id),
            );
        }
        $crumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif (is_page()) {
        $crumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif (is_category()) {
        $crumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => single_cat_title('', false),
            'item'     => get_category_link(get_queried_object_id()),
        );
    } elseif (is_tag()) {
        $crumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => single_tag_title('', false),
            'item'     => get_tag_link(get_queried_object_id()),
        );
    } elseif (is_author()) {
        $crumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_author(),
            'item'     => get_author_posts_url(get_queried_object_id()),
        );
    } elseif (is_search()) {
        $crumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => 'Zoekresultaten: ' . get_search_query(),
            'item'     => get_search_link(),
        );
    } elseif (is_archive()) {
        $crumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_archive_title(),
            'item'     => '',
        );
    }

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $crumbs,
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}


// =============================================================================
// WEBSITE + ORGANIZATION SCHEMA (front page only)
// =============================================================================

add_action('wp_head', 'writgo_seo_sitewide_schema', 2);
function writgo_seo_sitewide_schema() {
    if (writgo_seo_plugin_active()) {
        return;
    }
    if (!is_front_page() && !is_home()) {
        return;
    }

    // WebSite schema with SearchAction
    $website = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        'name'            => get_bloginfo('name'),
        'url'             => home_url('/'),
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => array(
                '@type'       => 'EntryPoint',
                'urlTemplate' => home_url('/?s={search_term_string}'),
            ),
            'query-input' => 'required name=search_term_string',
        ),
    );

    $site_desc = get_bloginfo('description');
    if ($site_desc) {
        $website['description'] = $site_desc;
    }

    echo '<script type="application/ld+json">' . wp_json_encode($website, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";

    // Organization schema with sameAs
    $org = array(
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => get_bloginfo('name'),
        'url'      => home_url('/'),
    );

    $logo = get_site_icon_url(512);
    if ($logo) {
        $org['logo'] = $logo;
    }

    // Collect social media URLs from customizer settings
    $social_networks = array('facebook', 'instagram', 'twitter', 'linkedin', 'youtube', 'pinterest', 'tiktok');
    $same_as = array();
    foreach ($social_networks as $network) {
        $url = get_theme_mod('writgo_social_' . $network);
        if (!empty($url)) {
            $same_as[] = $url;
        }
    }
    if (!empty($same_as)) {
        $org['sameAs'] = $same_as;
    }

    echo '<script type="application/ld+json">' . wp_json_encode($org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}
