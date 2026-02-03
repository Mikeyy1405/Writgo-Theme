<?php
/**
 * Writgo CTA Boxes System
 * 
 * Provides reusable CTA boxes that can be:
 * - Automatically inserted at the end of blog posts
 * - Inserted after X paragraphs
 * - Manually placed via shortcode
 * 
 * @package Writgo_Affiliate
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// ADMIN: CTA BOXES CUSTOM POST TYPE
// =============================================================================

/**
 * Register CTA Box Custom Post Type
 */
add_action('init', 'writgo_register_cta_box_cpt');
function writgo_register_cta_box_cpt() {
    $lang = function_exists('writgo_get_language') ? writgo_get_language() : 'nl';
    
    $labels = array(
        'nl' => array(
            'name'          => 'CTA Boxes',
            'singular_name' => 'CTA Box',
            'add_new'       => 'Nieuwe CTA Box',
            'add_new_item'  => 'Nieuwe CTA Box toevoegen',
            'edit_item'     => 'CTA Box bewerken',
            'menu_name'     => '📢 CTA Boxes',
        ),
        'en' => array(
            'name'          => 'CTA Boxes',
            'singular_name' => 'CTA Box',
            'add_new'       => 'New CTA Box',
            'add_new_item'  => 'Add New CTA Box',
            'edit_item'     => 'Edit CTA Box',
            'menu_name'     => '📢 CTA Boxes',
        ),
        'de' => array(
            'name'          => 'CTA Boxes',
            'singular_name' => 'CTA Box',
            'add_new'       => 'Neue CTA Box',
            'add_new_item'  => 'Neue CTA Box hinzufügen',
            'edit_item'     => 'CTA Box bearbeiten',
            'menu_name'     => '📢 CTA Boxes',
        ),
        'fr' => array(
            'name'          => 'CTA Boxes',
            'singular_name' => 'CTA Box',
            'add_new'       => 'Nouvelle CTA Box',
            'add_new_item'  => 'Ajouter une CTA Box',
            'edit_item'     => 'Modifier la CTA Box',
            'menu_name'     => '📢 CTA Boxes',
        ),
    );
    
    $current_labels = $labels[$lang] ?? $labels['en'];
    
    register_post_type('writgo_cta_box', array(
        'labels'       => $current_labels,
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-megaphone',
        'supports'     => array('title'),
        'menu_position'=> 25,
    ));
}

/**
 * Add CTA Box Meta Boxes
 */
add_action('add_meta_boxes', 'writgo_add_cta_box_meta_boxes');
function writgo_add_cta_box_meta_boxes() {
    add_meta_box(
        'writgo_cta_box_settings',
        '📢 CTA Box Instellingen',
        'writgo_cta_box_meta_callback',
        'writgo_cta_box',
        'normal',
        'high'
    );
}

function writgo_cta_box_meta_callback($post) {
    wp_nonce_field('writgo_cta_box_save', 'writgo_cta_box_nonce');
    
    // Get saved values
    $style = get_post_meta($post->ID, '_cta_style', true) ?: 'box';
    $icon = get_post_meta($post->ID, '_cta_icon', true) ?: '🎁';
    $title = get_post_meta($post->ID, '_cta_title', true);
    $text = get_post_meta($post->ID, '_cta_text', true);
    $button_text = get_post_meta($post->ID, '_cta_button_text', true) ?: 'Bekijk Aanbieding';
    $button_url = get_post_meta($post->ID, '_cta_button_url', true);
    $image = get_post_meta($post->ID, '_cta_image', true);
    $bg_color = get_post_meta($post->ID, '_cta_bg_color', true) ?: '#f8fafc';
    $border_color = get_post_meta($post->ID, '_cta_border_color', true) ?: '#e2e8f0';
    $button_color = get_post_meta($post->ID, '_cta_button_color', true) ?: '#f97316';
    $is_default_bottom = get_post_meta($post->ID, '_cta_default_bottom', true);
    $insert_after_paragraphs = get_post_meta($post->ID, '_cta_insert_after_paragraphs', true) ?: 0;
    
    // 10 High-Converting Presets
    $presets = array(
        'deal' => array(
            'name'         => '🔥 Flash Deal',
            'style'        => 'highlight',
            'icon'         => '🔥',
            'title'        => 'Exclusieve Deal - Alleen Vandaag!',
            'text'         => 'Profiteer nu van deze tijdelijke aanbieding voordat het te laat is.',
            'button_text'  => 'Claim Je Korting →',
            'bg_color'     => '#fef3c7',
            'border_color' => '#f59e0b',
            'button_color' => '#dc2626',
        ),
        'winner' => array(
            'name'         => '🏆 Beste Keuze',
            'style'        => 'highlight',
            'icon'         => '🏆',
            'title'        => 'Onze #1 Aanbeveling',
            'text'         => 'Dit product scoort het hoogst in onze uitgebreide tests en reviews.',
            'button_text'  => 'Bekijk Winnaar →',
            'bg_color'     => '#ecfdf5',
            'border_color' => '#10b981',
            'button_color' => '#059669',
        ),
        'budget' => array(
            'name'         => '💰 Budget Tip',
            'style'        => 'box',
            'icon'         => '💰',
            'title'        => 'Beste Prijs-Kwaliteit',
            'text'         => 'De slimste keuze voor je portemonnee zonder in te leveren op kwaliteit.',
            'button_text'  => 'Bekijk Prijs →',
            'bg_color'     => '#eff6ff',
            'border_color' => '#3b82f6',
            'button_color' => '#2563eb',
        ),
        'compare' => array(
            'name'         => '⚖️ Vergelijk Prijzen',
            'style'        => 'box',
            'icon'         => '⚖️',
            'title'        => 'Vergelijk & Bespaar',
            'text'         => 'Check de prijzen bij verschillende winkels en vind de beste deal.',
            'button_text'  => 'Vergelijk Prijzen →',
            'bg_color'     => '#f5f3ff',
            'border_color' => '#8b5cf6',
            'button_color' => '#7c3aed',
        ),
        'exclusive' => array(
            'name'         => '⭐ Exclusief',
            'style'        => 'highlight',
            'icon'         => '⭐',
            'title'        => 'Exclusief Voor Onze Lezers',
            'text'         => 'Speciale korting die je nergens anders vindt. Gebruik onze link!',
            'button_text'  => 'Activeer Korting →',
            'bg_color'     => '#fdf4ff',
            'border_color' => '#d946ef',
            'button_color' => '#c026d3',
        ),
        'quick' => array(
            'name'         => '⚡ Snelle Keuze',
            'style'        => 'inline',
            'icon'         => '⚡',
            'title'        => 'Geen tijd? Dit is jouw keuze!',
            'text'         => 'De beste optie als je snel wilt beslissen.',
            'button_text'  => 'Direct Bestellen →',
            'bg_color'     => '#fff7ed',
            'border_color' => '#f97316',
            'button_color' => '#ea580c',
        ),
        'premium' => array(
            'name'         => '👑 Premium Pick',
            'style'        => 'highlight',
            'icon'         => '👑',
            'title'        => 'Premium Keuze',
            'text'         => 'Voor wie het beste van het beste wil, zonder compromissen.',
            'button_text'  => 'Bekijk Premium →',
            'bg_color'     => '#1a365d',
            'border_color' => '#f59e0b',
            'button_color' => '#f59e0b',
            'text_light'   => true,
        ),
        'beginner' => array(
            'name'         => '🌱 Starter Tip',
            'style'        => 'box',
            'icon'         => '🌱',
            'title'        => 'Perfect Voor Beginners',
            'text'         => 'Makkelijk te gebruiken en ideaal om mee te starten.',
            'button_text'  => 'Start Nu →',
            'bg_color'     => '#f0fdf4',
            'border_color' => '#22c55e',
            'button_color' => '#16a34a',
        ),
        'limited' => array(
            'name'         => '⏰ Beperkte Voorraad',
            'style'        => 'highlight',
            'icon'         => '⏰',
            'title'        => 'Op = Op!',
            'text'         => 'Beperkte voorraad beschikbaar. Wacht niet te lang!',
            'button_text'  => 'Check Beschikbaarheid →',
            'bg_color'     => '#fef2f2',
            'border_color' => '#ef4444',
            'button_color' => '#dc2626',
        ),
        'free_shipping' => array(
            'name'         => '🚚 Gratis Verzending',
            'style'        => 'box',
            'icon'         => '🚚',
            'title'        => 'Gratis Verzending!',
            'text'         => 'Bestel vandaag en ontvang gratis verzending naar huis.',
            'button_text'  => 'Bestel Met Gratis Verzending →',
            'bg_color'     => '#ecfeff',
            'border_color' => '#06b6d4',
            'button_color' => '#0891b2',
        ),
    );
    ?>
    
    <style>
        .writgo-cta-settings { display: grid; gap: 20px; }
        .writgo-cta-row { display: grid; grid-template-columns: 200px 1fr; gap: 10px; align-items: start; }
        .writgo-cta-row label { font-weight: 600; padding-top: 5px; }
        .writgo-cta-row input[type="text"],
        .writgo-cta-row input[type="url"],
        .writgo-cta-row input[type="number"],
        .writgo-cta-row textarea,
        .writgo-cta-row select { width: 100%; max-width: 500px; }
        .writgo-cta-row textarea { min-height: 80px; }
        .writgo-cta-row small { display: block; color: #666; margin-top: 5px; }
        .writgo-cta-preview { background: #f0f0f0; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .writgo-cta-section { border-bottom: 1px solid #e0e0e0; padding-bottom: 20px; margin-bottom: 20px; }
        .writgo-cta-section h4 { margin: 0 0 15px; color: #1a365d; }
        .writgo-image-preview { max-width: 200px; margin-top: 10px; border-radius: 4px; }
        .writgo-checkbox-row { display: flex; align-items: center; gap: 10px; }
        
        /* Preset Styles */
        .writgo-presets-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 20px; }
        @media (max-width: 1400px) { .writgo-presets-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 900px) { .writgo-presets-grid { grid-template-columns: repeat(2, 1fr); } }
        .writgo-preset-btn {
            padding: 12px 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
            font-size: 13px;
        }
        .writgo-preset-btn:hover {
            border-color: #f97316;
            background: #fff7ed;
        }
        .writgo-preset-btn .preset-icon {
            font-size: 24px;
            display: block;
            margin-bottom: 5px;
        }
        .writgo-preset-btn .preset-name {
            font-weight: 600;
            color: #1a365d;
        }
    </style>
    
    <div class="writgo-cta-settings">
        
        <!-- Presets Section -->
        <div class="writgo-cta-section">
            <h4>🎨 Kies een Preset (klik om toe te passen)</h4>
            <div class="writgo-presets-grid">
                <?php foreach ($presets as $key => $preset) : ?>
                    <button type="button" class="writgo-preset-btn" data-preset="<?php echo esc_attr($key); ?>">
                        <span class="preset-icon"><?php echo esc_html($preset['icon']); ?></span>
                        <span class="preset-name"><?php echo esc_html($preset['name']); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Content Section -->
        <div class="writgo-cta-section">
            <h4>📝 Content</h4>
            
            <div class="writgo-cta-row">
                <label>Stijl</label>
                <div>
                    <select name="cta_style" id="cta_style">
                        <option value="box" <?php selected($style, 'box'); ?>>📦 Box (met achtergrond)</option>
                        <option value="banner" <?php selected($style, 'banner'); ?>>🖼️ Banner (met afbeelding)</option>
                        <option value="inline" <?php selected($style, 'inline'); ?>>📄 Inline (minimaal)</option>
                        <option value="highlight" <?php selected($style, 'highlight'); ?>>⭐ Highlight (opvallend)</option>
                    </select>
                </div>
            </div>
            
            <div class="writgo-cta-row">
                <label>Icoon (emoji)</label>
                <div>
                    <input type="text" name="cta_icon" id="cta_icon" value="<?php echo esc_attr($icon); ?>" placeholder="🎁">
                    <small>Kies een emoji die bij je CTA past: 🎁 💰 🔥 ⭐ 🚀 💡 📢 🏆</small>
                </div>
            </div>
            
            <div class="writgo-cta-row">
                <label>Titel</label>
                <div>
                    <input type="text" name="cta_title" id="cta_title" value="<?php echo esc_attr($title); ?>" placeholder="Exclusieve Aanbieding!">
                </div>
            </div>
            
            <div class="writgo-cta-row">
                <label>Tekst</label>
                <div>
                    <textarea name="cta_text" id="cta_text" placeholder="Beschrijving van je aanbieding..."><?php echo esc_textarea($text); ?></textarea>
                    <small>Korte beschrijving (1-2 zinnen werkt het beste)</small>
                </div>
            </div>
            
            <div class="writgo-cta-row">
                <label>Button Tekst</label>
                <div>
                    <input type="text" name="cta_button_text" id="cta_button_text" value="<?php echo esc_attr($button_text); ?>" placeholder="Bekijk Aanbieding">
                </div>
            </div>
            
            <div class="writgo-cta-row">
                <label>Button URL</label>
                <div>
                    <input type="url" name="cta_button_url" id="cta_button_url" value="<?php echo esc_url($button_url); ?>" placeholder="https://example.com/aanbieding">
                    <small>Affiliate link of bestemmingspagina</small>
                </div>
            </div>
            
            <div class="writgo-cta-row">
                <label>Afbeelding (optioneel)</label>
                <div>
                    <input type="url" name="cta_image" value="<?php echo esc_url($image); ?>" placeholder="https://example.com/afbeelding.jpg" id="cta_image_url">
                    <button type="button" class="button" id="upload_cta_image">Afbeelding uploaden</button>
                    <?php if ($image) : ?>
                        <img src="<?php echo esc_url($image); ?>" class="writgo-image-preview" id="cta_image_preview">
                    <?php else : ?>
                        <img src="" class="writgo-image-preview" id="cta_image_preview" style="display:none;">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Colors Section -->
        <div class="writgo-cta-section">
            <h4>🎨 Kleuren</h4>
            
            <div class="writgo-cta-row">
                <label>Achtergrond kleur</label>
                <div>
                    <input type="color" name="cta_bg_color" value="<?php echo esc_attr($bg_color); ?>">
                </div>
            </div>
            
            <div class="writgo-cta-row">
                <label>Border kleur</label>
                <div>
                    <input type="color" name="cta_border_color" value="<?php echo esc_attr($border_color); ?>">
                </div>
            </div>
            
            <div class="writgo-cta-row">
                <label>Button kleur</label>
                <div>
                    <input type="color" name="cta_button_color" value="<?php echo esc_attr($button_color); ?>">
                </div>
            </div>
        </div>
        
        <!-- Placement Section -->
        <div class="writgo-cta-section">
            <h4>📍 Plaatsing</h4>
            
            <div class="writgo-cta-row">
                <label>Standaard onderaan blogs</label>
                <div class="writgo-checkbox-row">
                    <input type="checkbox" name="cta_default_bottom" value="1" <?php checked($is_default_bottom, '1'); ?>>
                    <span>Toon deze CTA automatisch onderaan alle blogposts</span>
                </div>
            </div>
            
            <div class="writgo-cta-row">
                <label>Invoegen na X paragrafen</label>
                <div>
                    <input type="number" name="cta_insert_after_paragraphs" value="<?php echo esc_attr($insert_after_paragraphs); ?>" min="0" max="20" step="1">
                    <small>0 = niet automatisch invoegen. Bijv. 3 = invoegen na de 3e paragraaf</small>
                </div>
            </div>
        </div>
        
        <!-- Shortcode Info -->
        <div class="writgo-cta-section" style="border-bottom: none;">
            <h4>🔧 Shortcode</h4>
            <p>Gebruik deze shortcode om de CTA handmatig te plaatsen:</p>
            <code style="background: #f0f0f0; padding: 10px; display: block; margin: 10px 0;">[writgo_cta id="<?php echo $post->ID; ?>"]</code>
            <p>Of gebruik de algemene shortcode voor de standaard bottom CTA:</p>
            <code style="background: #f0f0f0; padding: 10px; display: block;">[writgo_cta]</code>
        </div>
        
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Preset data
        var presets = <?php echo json_encode($presets); ?>;
        
        // Apply preset on click
        $('.writgo-preset-btn').on('click', function() {
            var presetKey = $(this).data('preset');
            var preset = presets[presetKey];
            
            if (preset) {
                $('#cta_style').val(preset.style);
                $('#cta_icon').val(preset.icon);
                $('#cta_title').val(preset.title);
                $('#cta_text').val(preset.text);
                $('#cta_button_text').val(preset.button_text);
                $('input[name="cta_bg_color"]').val(preset.bg_color).trigger('change');
                $('input[name="cta_border_color"]').val(preset.border_color).trigger('change');
                $('input[name="cta_button_color"]').val(preset.button_color).trigger('change');
                
                // Update color picker displays
                $('input[name="cta_bg_color"]').next('input').val(preset.bg_color);
                $('input[name="cta_border_color"]').next('input').val(preset.border_color);
                $('input[name="cta_button_color"]').next('input').val(preset.button_color);
                
                // Visual feedback
                $('.writgo-preset-btn').css('border-color', '#e0e0e0');
                $(this).css('border-color', '#f97316');
                
                // Flash success
                $(this).addClass('preset-applied');
                setTimeout(function() {
                    $('.writgo-preset-btn').removeClass('preset-applied');
                }, 500);
            }
        });
        
        // Media uploader
        $('#upload_cta_image').click(function(e) {
            e.preventDefault();
            var image = wp.media({
                title: 'Kies een afbeelding',
                multiple: false
            }).open().on('select', function() {
                var uploaded = image.state().get('selection').first().toJSON();
                $('#cta_image_url').val(uploaded.url);
                $('#cta_image_preview').attr('src', uploaded.url).show();
            });
        });
    });
    </script>
    <?php
}

/**
 * Save CTA Box Meta
 */
add_action('save_post_writgo_cta_box', 'writgo_save_cta_box_meta');
function writgo_save_cta_box_meta($post_id) {
    if (!isset($_POST['writgo_cta_box_nonce']) || !wp_verify_nonce($_POST['writgo_cta_box_nonce'], 'writgo_cta_box_save')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    $fields = array(
        'cta_style' => 'sanitize_text_field',
        'cta_icon' => 'sanitize_text_field',
        'cta_title' => 'sanitize_text_field',
        'cta_text' => 'sanitize_textarea_field',
        'cta_button_text' => 'sanitize_text_field',
        'cta_button_url' => 'esc_url_raw',
        'cta_image' => 'esc_url_raw',
        'cta_bg_color' => 'sanitize_hex_color',
        'cta_border_color' => 'sanitize_hex_color',
        'cta_button_color' => 'sanitize_hex_color',
        'cta_default_bottom' => 'sanitize_text_field',
        'cta_insert_after_paragraphs' => 'absint',
    );
    
    foreach ($fields as $field => $sanitizer) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, $sanitizer($_POST[$field]));
        } else {
            delete_post_meta($post_id, '_' . $field);
        }
    }
}

// =============================================================================
// FRONTEND: RENDER CTA BOXES
// =============================================================================

/**
 * Render a CTA Box
 */
function writgo_render_cta_box($cta_id) {
    $cta = get_post($cta_id);
    if (!$cta || $cta->post_status !== 'publish') {
        return '';
    }
    
    $style = get_post_meta($cta_id, '_cta_style', true) ?: 'box';
    $icon = get_post_meta($cta_id, '_cta_icon', true) ?: '🎁';
    $title = get_post_meta($cta_id, '_cta_title', true);
    $text = get_post_meta($cta_id, '_cta_text', true);
    $button_text = get_post_meta($cta_id, '_cta_button_text', true) ?: 'Bekijk Aanbieding';
    $button_url = get_post_meta($cta_id, '_cta_button_url', true);
    $image = get_post_meta($cta_id, '_cta_image', true);
    $bg_color = get_post_meta($cta_id, '_cta_bg_color', true) ?: '#f8fafc';
    $border_color = get_post_meta($cta_id, '_cta_border_color', true) ?: '#e2e8f0';
    $button_color = get_post_meta($cta_id, '_cta_button_color', true) ?: '#f97316';
    
    ob_start();
    ?>
    <div class="wa-cta-box wa-cta-<?php echo esc_attr($style); ?>" style="--cta-bg: <?php echo esc_attr($bg_color); ?>; --cta-border: <?php echo esc_attr($border_color); ?>; --cta-button: <?php echo esc_attr($button_color); ?>;">
        
        <?php if ($style === 'banner' && $image) : ?>
            <div class="wa-cta-image">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
            </div>
        <?php endif; ?>
        
        <div class="wa-cta-content">
            <?php if ($icon && $style !== 'banner') : ?>
                <span class="wa-cta-icon"><?php echo esc_html($icon); ?></span>
            <?php endif; ?>
            
            <?php if ($title) : ?>
                <h4 class="wa-cta-title"><?php echo esc_html($title); ?></h4>
            <?php endif; ?>
            
            <?php if ($text) : ?>
                <p class="wa-cta-text"><?php echo wp_kses_post($text); ?></p>
            <?php endif; ?>
            
            <?php if ($button_url) : ?>
                <a href="<?php echo esc_url($button_url); ?>" class="wa-cta-button" target="_blank" rel="noopener sponsored">
                    <?php echo esc_html($button_text); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            <?php endif; ?>
        </div>
        
        <?php if ($style === 'box' && $image) : ?>
            <div class="wa-cta-image-side">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
            </div>
        <?php endif; ?>
        
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: [writgo_cta id="123"] or [writgo_cta]
 */
add_shortcode('writgo_cta', 'writgo_cta_box_shortcode');
function writgo_cta_box_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
    ), $atts);
    
    // If no ID specified, get the default bottom CTA
    if (!$atts['id']) {
        $default_cta = get_posts(array(
            'post_type'      => 'writgo_cta_box',
            'posts_per_page' => 1,
            'meta_key'       => '_cta_default_bottom',
            'meta_value'     => '1',
            'post_status'    => 'publish',
        ));
        
        if (!empty($default_cta)) {
            $atts['id'] = $default_cta[0]->ID;
        }
    }
    
    if (!$atts['id']) {
        return '';
    }
    
    return writgo_render_cta_box($atts['id']);
}

/**
 * Auto-insert CTA boxes in content
 */
add_filter('the_content', 'writgo_auto_insert_cta_boxes', 20);
function writgo_auto_insert_cta_boxes($content) {
    // Only on single posts
    if (!is_singular('post') || is_admin()) {
        return $content;
    }
    
    // Get all CTA boxes with auto-insert settings
    $cta_boxes = get_posts(array(
        'post_type'      => 'writgo_cta_box',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ));
    
    $bottom_cta = '';
    $paragraph_inserts = array();
    
    foreach ($cta_boxes as $cta) {
        $is_default_bottom = get_post_meta($cta->ID, '_cta_default_bottom', true);
        $insert_after = (int) get_post_meta($cta->ID, '_cta_insert_after_paragraphs', true);
        
        if ($is_default_bottom === '1') {
            $bottom_cta = writgo_render_cta_box($cta->ID);
        }
        
        if ($insert_after > 0) {
            $paragraph_inserts[$insert_after] = writgo_render_cta_box($cta->ID);
        }
    }
    
    // Insert CTAs after specific paragraphs
    if (!empty($paragraph_inserts)) {
        // Split content by closing </p> tags
        $paragraphs = preg_split('/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        $new_content = '';
        $p_count = 0;
        
        for ($i = 0; $i < count($paragraphs); $i++) {
            $new_content .= $paragraphs[$i];
            
            // Check if this is a closing </p> tag
            if (strtolower($paragraphs[$i]) === '</p>') {
                $p_count++;
                
                // Insert CTA after this paragraph if configured
                if (isset($paragraph_inserts[$p_count])) {
                    $new_content .= $paragraph_inserts[$p_count];
                }
            }
        }
        
        $content = $new_content;
    }
    
    // Add bottom CTA
    if ($bottom_cta) {
        $content .= $bottom_cta;
    }
    
    return $content;
}

// =============================================================================
// CSS FOR CTA BOXES
// =============================================================================

add_action('wp_head', 'writgo_cta_box_styles');
function writgo_cta_box_styles() {
    ?>
    <style>
    /* CTA Box Base Styles - Beautiful & Centered */
    .wa-cta-box {
        margin: 2.5rem auto;
        padding: 2.5rem 2rem;
        background: var(--cta-bg, linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%));
        border: 2px solid var(--cta-border, #e2e8f0);
        border-radius: 16px;
        text-align: center;
        max-width: 600px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    .wa-cta-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--cta-button, #f97316);
    }
    
    .wa-cta-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }
    
    .wa-cta-icon {
        font-size: 3rem;
        display: block;
        margin-bottom: 1rem;
        line-height: 1;
    }
    
    .wa-cta-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0 0 0.75rem;
        color: var(--wa-primary, #1a365d);
        line-height: 1.3;
    }
    
    .wa-cta-text {
        color: #64748b;
        margin: 0 0 1.5rem;
        line-height: 1.7;
        font-size: 1.05rem;
        max-width: 450px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .wa-cta-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        background: var(--cta-button, #f97316);
        color: #fff !important;
        text-decoration: none !important;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.05rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
        min-width: 200px;
    }
    
    .wa-cta-button:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 6px 25px rgba(249, 115, 22, 0.4);
        color: #fff !important;
    }
    
    .wa-cta-button svg,
    .wa-cta-button::after {
        transition: transform 0.3s ease;
    }
    
    .wa-cta-button:hover svg,
    .wa-cta-button:hover::after {
        transform: translateX(4px);
    }
    
    /* Content wrapper for flex layouts */
    .wa-cta-content {
        width: 100%;
    }
    
    /* Image support */
    .wa-cta-image-side {
        margin-bottom: 1.5rem;
    }
    
    .wa-cta-image-side img {
        max-width: 180px;
        height: auto;
        border-radius: 12px;
        margin: 0 auto;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    /* ============================================
       STYLE: BOX (default - centered with icon)
       ============================================ */
    .wa-cta-box:not(.wa-cta-banner):not(.wa-cta-inline):not(.wa-cta-highlight) {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    /* ============================================
       STYLE: BANNER (with large image)
       ============================================ */
    .wa-cta-banner {
        padding: 0;
        max-width: 700px;
        text-align: center;
    }
    
    .wa-cta-banner::before {
        display: none;
    }
    
    .wa-cta-banner .wa-cta-image {
        position: relative;
        height: 220px;
        overflow: hidden;
    }
    
    .wa-cta-banner .wa-cta-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .wa-cta-banner .wa-cta-image::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 80px;
        background: linear-gradient(to top, var(--cta-bg, #f8fafc), transparent);
    }
    
    .wa-cta-banner .wa-cta-content {
        padding: 2rem;
    }
    
    .wa-cta-banner .wa-cta-icon {
        margin-top: -2rem;
        background: var(--cta-bg, #fff);
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
        margin-right: auto;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        position: relative;
        z-index: 1;
    }
    
    /* ============================================
       STYLE: INLINE (minimal, side-aligned)
       ============================================ */
    .wa-cta-inline {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.25rem 1.5rem;
        text-align: left;
        max-width: 100%;
        background: var(--cta-bg, #f8fafc);
        border-left: 5px solid var(--cta-button, #f97316);
        border-top: 1px solid var(--cta-border, #e2e8f0);
        border-right: 1px solid var(--cta-border, #e2e8f0);
        border-bottom: 1px solid var(--cta-border, #e2e8f0);
        border-radius: 0 12px 12px 0;
    }
    
    .wa-cta-inline::before {
        display: none;
    }
    
    .wa-cta-inline .wa-cta-icon {
        font-size: 2rem;
        margin: 0;
        flex-shrink: 0;
    }
    
    .wa-cta-inline .wa-cta-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        flex: 1;
    }
    
    .wa-cta-inline .wa-cta-title {
        margin: 0;
        font-size: 1.1rem;
    }
    
    .wa-cta-inline .wa-cta-text {
        margin: 0.25rem 0 0;
        font-size: 0.95rem;
        max-width: none;
    }
    
    .wa-cta-inline .wa-cta-text-wrap {
        flex: 1;
        min-width: 200px;
    }
    
    .wa-cta-inline .wa-cta-button {
        padding: 0.65rem 1.25rem;
        font-size: 0.9rem;
        min-width: auto;
        white-space: nowrap;
    }
    
    /* ============================================
       STYLE: HIGHLIGHT (attention-grabbing)
       ============================================ */
    .wa-cta-highlight {
        background: linear-gradient(145deg, var(--cta-bg, #fff7ed) 0%, rgba(255,255,255,0.9) 100%);
        border: 3px solid var(--cta-button, #f97316);
        padding: 3rem 2rem;
        position: relative;
    }
    
    .wa-cta-highlight::before {
        height: 6px;
        background: linear-gradient(90deg, var(--cta-button, #f97316), #fbbf24, var(--cta-button, #f97316));
    }
    
    .wa-cta-highlight::after {
        content: '';
        position: absolute;
        top: 15px;
        right: 15px;
        width: 50px;
        height: 50px;
        background: var(--cta-button, #f97316);
        opacity: 0.1;
        border-radius: 50%;
    }
    
    .wa-cta-highlight .wa-cta-icon {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
    }
    
    .wa-cta-highlight .wa-cta-title {
        font-size: 1.75rem;
        color: var(--wa-primary, #1a365d);
    }
    
    .wa-cta-highlight .wa-cta-text {
        font-size: 1.1rem;
    }
    
    .wa-cta-highlight .wa-cta-button {
        padding: 1.1rem 2.5rem;
        font-size: 1.1rem;
        animation: pulse-glow 2s ease-in-out infinite;
    }
    
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3); }
        50% { box-shadow: 0 4px 25px rgba(249, 115, 22, 0.5); }
    }
    
    /* ============================================
       DARK/PREMIUM VARIANT
       ============================================ */
    .wa-cta-box[style*="background: #1a365d"],
    .wa-cta-box[style*="background:#1a365d"] {
        color: #fff;
    }
    
    .wa-cta-box[style*="background: #1a365d"] .wa-cta-title,
    .wa-cta-box[style*="background:#1a365d"] .wa-cta-title {
        color: #fff;
    }
    
    .wa-cta-box[style*="background: #1a365d"] .wa-cta-text,
    .wa-cta-box[style*="background:#1a365d"] .wa-cta-text {
        color: rgba(255,255,255,0.85);
    }
    
    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 600px) {
        .wa-cta-box {
            padding: 2rem 1.5rem;
            margin: 2rem auto;
        }
        
        .wa-cta-title {
            font-size: 1.3rem;
        }
        
        .wa-cta-text {
            font-size: 1rem;
        }
        
        .wa-cta-button {
            width: 100%;
            justify-content: center;
        }
        
        .wa-cta-inline {
            flex-direction: column;
            text-align: center;
            padding: 1.5rem;
            border-left: none;
            border-top: 5px solid var(--cta-button, #f97316);
            border-radius: 0 0 12px 12px;
        }
        
        .wa-cta-inline .wa-cta-content {
            flex-direction: column;
            text-align: center;
        }
        
        .wa-cta-inline .wa-cta-button {
            width: 100%;
        }
        
        .wa-cta-highlight {
            padding: 2rem 1.5rem;
        }
        
        .wa-cta-highlight .wa-cta-title {
            font-size: 1.4rem;
        }
    }
    </style>
    <?php
}
