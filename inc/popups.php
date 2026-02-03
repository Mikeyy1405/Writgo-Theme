<?php
/**
 * Writgo Popups & Slide-ins System
 * 
 * Features:
 * - Center popups (modal)
 * - Slide-in boxes (bottom right)
 * - Exit intent detection
 * - Scroll trigger
 * - Time delay trigger
 * - Page visit trigger
 * - Cookie-based display control (don't annoy visitors)
 * 
 * @package Writgo_Affiliate
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// CUSTOM POST TYPE: POPUPS
// =============================================================================

add_action('init', 'writgo_register_popup_cpt');
function writgo_register_popup_cpt() {
    $lang = function_exists('writgo_get_language') ? writgo_get_language() : 'nl';
    
    $labels = array(
        'nl' => array(
            'name'          => 'Popups & Slide-ins',
            'singular_name' => 'Popup',
            'add_new'       => 'Nieuwe Popup',
            'add_new_item'  => 'Nieuwe Popup toevoegen',
            'edit_item'     => 'Popup bewerken',
            'menu_name'     => '🎯 Popups',
        ),
        'en' => array(
            'name'          => 'Popups & Slide-ins',
            'singular_name' => 'Popup',
            'add_new'       => 'New Popup',
            'add_new_item'  => 'Add New Popup',
            'edit_item'     => 'Edit Popup',
            'menu_name'     => '🎯 Popups',
        ),
        'de' => array(
            'name'          => 'Popups & Slide-ins',
            'singular_name' => 'Popup',
            'add_new'       => 'Neues Popup',
            'add_new_item'  => 'Neues Popup hinzufügen',
            'edit_item'     => 'Popup bearbeiten',
            'menu_name'     => '🎯 Popups',
        ),
        'fr' => array(
            'name'          => 'Popups & Slide-ins',
            'singular_name' => 'Popup',
            'add_new'       => 'Nouveau Popup',
            'add_new_item'  => 'Ajouter un Popup',
            'edit_item'     => 'Modifier le Popup',
            'menu_name'     => '🎯 Popups',
        ),
    );
    
    register_post_type('writgo_popup', array(
        'labels'       => $labels[$lang] ?? $labels['en'],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-welcome-widgets-menus',
        'supports'     => array('title'),
        'menu_position'=> 26,
    ));
}

// =============================================================================
// META BOXES
// =============================================================================

add_action('add_meta_boxes', 'writgo_add_popup_meta_boxes');
function writgo_add_popup_meta_boxes() {
    add_meta_box(
        'writgo_popup_settings',
        '🎯 Popup Instellingen',
        'writgo_popup_meta_callback',
        'writgo_popup',
        'normal',
        'high'
    );
}

function writgo_popup_meta_callback($post) {
    wp_nonce_field('writgo_popup_save', 'writgo_popup_nonce');
    
    // Get saved values
    $type = get_post_meta($post->ID, '_popup_type', true) ?: 'popup';
    $trigger = get_post_meta($post->ID, '_popup_trigger', true) ?: 'time';
    $trigger_delay = get_post_meta($post->ID, '_popup_trigger_delay', true) ?: 5;
    $trigger_scroll = get_post_meta($post->ID, '_popup_trigger_scroll', true) ?: 50;
    $trigger_pages = get_post_meta($post->ID, '_popup_trigger_pages', true) ?: 2;
    
    $icon = get_post_meta($post->ID, '_popup_icon', true) ?: '🎁';
    $title = get_post_meta($post->ID, '_popup_title', true);
    $text = get_post_meta($post->ID, '_popup_text', true);
    $image = get_post_meta($post->ID, '_popup_image', true);
    
    $button_text = get_post_meta($post->ID, '_popup_button_text', true) ?: 'Bekijk Aanbieding';
    $button_url = get_post_meta($post->ID, '_popup_button_url', true);
    $button_color = get_post_meta($post->ID, '_popup_button_color', true) ?: '#f97316';
    
    $show_email = get_post_meta($post->ID, '_popup_show_email', true);
    $email_placeholder = get_post_meta($post->ID, '_popup_email_placeholder', true) ?: 'Je e-mailadres';
    $email_button = get_post_meta($post->ID, '_popup_email_button', true) ?: 'Aanmelden';
    
    $bg_color = get_post_meta($post->ID, '_popup_bg_color', true) ?: '#ffffff';
    $text_color = get_post_meta($post->ID, '_popup_text_color', true) ?: '#1a202c';
    $overlay_color = get_post_meta($post->ID, '_popup_overlay_color', true) ?: 'rgba(0,0,0,0.6)';
    
    $cookie_days = get_post_meta($post->ID, '_popup_cookie_days', true) ?: 7;
    $show_on = get_post_meta($post->ID, '_popup_show_on', true) ?: 'all';
    $show_on_pages = get_post_meta($post->ID, '_popup_show_on_pages', true);
    $is_active = get_post_meta($post->ID, '_popup_active', true);
    
    $close_text = get_post_meta($post->ID, '_popup_close_text', true) ?: '';
    $countdown_enabled = get_post_meta($post->ID, '_popup_countdown', true);
    $countdown_minutes = get_post_meta($post->ID, '_popup_countdown_minutes', true) ?: 15;
    ?>
    
    <style>
        .writgo-popup-settings { display: grid; gap: 20px; }
        .writgo-popup-section { border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; background: #fafafa; }
        .writgo-popup-section h4 { margin: 0 0 15px; padding-bottom: 10px; border-bottom: 2px solid #1a365d; color: #1a365d; }
        .writgo-popup-row { display: grid; grid-template-columns: 180px 1fr; gap: 10px; align-items: start; margin-bottom: 15px; }
        .writgo-popup-row:last-child { margin-bottom: 0; }
        .writgo-popup-row label { font-weight: 600; padding-top: 5px; }
        .writgo-popup-row input[type="text"],
        .writgo-popup-row input[type="url"],
        .writgo-popup-row input[type="number"],
        .writgo-popup-row input[type="email"],
        .writgo-popup-row textarea,
        .writgo-popup-row select { width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; }
        .writgo-popup-row textarea { min-height: 80px; }
        .writgo-popup-row small { display: block; color: #666; margin-top: 5px; font-size: 12px; }
        .writgo-popup-row .color-row { display: flex; align-items: center; gap: 10px; }
        .writgo-checkbox-row { display: flex; align-items: center; gap: 10px; }
        .writgo-image-preview { max-width: 200px; margin-top: 10px; border-radius: 4px; }
        .writgo-popup-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 1200px) { .writgo-popup-grid { grid-template-columns: 1fr; } }
        .writgo-trigger-options { background: #fff; padding: 15px; border-radius: 6px; margin-top: 10px; border: 1px solid #e0e0e0; }
        .writgo-active-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .writgo-active-badge.active { background: #10b981; color: #fff; }
        .writgo-active-badge.inactive { background: #ef4444; color: #fff; }
    </style>
    
    <div class="writgo-popup-settings">
        
        <!-- Status -->
        <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: <?php echo $is_active ? '#ecfdf5' : '#fef2f2'; ?>; border-radius: 8px;">
            <span class="writgo-active-badge <?php echo $is_active ? 'active' : 'inactive'; ?>">
                <?php echo $is_active ? '✓ Actief' : '✗ Inactief'; ?>
            </span>
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600;">
                <input type="checkbox" name="popup_active" value="1" <?php checked($is_active, '1'); ?>>
                Popup Activeren
            </label>
        </div>
        
        <div class="writgo-popup-grid">
            
            <!-- Left Column: Type & Trigger -->
            <div>
                <!-- Type & Trigger Section -->
                <div class="writgo-popup-section">
                    <h4>📦 Type & Trigger</h4>
                    
                    <div class="writgo-popup-row">
                        <label>Popup Type</label>
                        <div>
                            <select name="popup_type" id="popup_type">
                                <option value="popup" <?php selected($type, 'popup'); ?>>🖼️ Popup (center modal)</option>
                                <option value="slidein" <?php selected($type, 'slidein'); ?>>📥 Slide-in (rechtsonder)</option>
                                <option value="exit" <?php selected($type, 'exit'); ?>>🚪 Exit Intent Popup</option>
                                <option value="fullscreen" <?php selected($type, 'fullscreen'); ?>>📺 Fullscreen Overlay</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="writgo-popup-row" id="trigger_row">
                        <label>Trigger</label>
                        <div>
                            <select name="popup_trigger" id="popup_trigger">
                                <option value="time" <?php selected($trigger, 'time'); ?>>⏱️ Na X seconden</option>
                                <option value="scroll" <?php selected($trigger, 'scroll'); ?>>📜 Bij X% scroll</option>
                                <option value="exit" <?php selected($trigger, 'exit'); ?>>🚪 Exit intent</option>
                                <option value="pages" <?php selected($trigger, 'pages'); ?>>📄 Na X pagina's bekeken</option>
                                <option value="click" <?php selected($trigger, 'click'); ?>>🖱️ Bij klik op element</option>
                            </select>
                            
                            <div class="writgo-trigger-options">
                                <div id="trigger_time" style="<?php echo $trigger !== 'time' ? 'display:none;' : ''; ?>">
                                    <label>Vertraging (seconden):</label>
                                    <input type="number" name="popup_trigger_delay" value="<?php echo esc_attr($trigger_delay); ?>" min="0" max="120" style="width: 80px;">
                                </div>
                                <div id="trigger_scroll" style="<?php echo $trigger !== 'scroll' ? 'display:none;' : ''; ?>">
                                    <label>Scroll percentage:</label>
                                    <input type="number" name="popup_trigger_scroll" value="<?php echo esc_attr($trigger_scroll); ?>" min="10" max="100" style="width: 80px;"> %
                                </div>
                                <div id="trigger_pages" style="<?php echo $trigger !== 'pages' ? 'display:none;' : ''; ?>">
                                    <label>Aantal pagina's:</label>
                                    <input type="number" name="popup_trigger_pages" value="<?php echo esc_attr($trigger_pages); ?>" min="1" max="10" style="width: 80px;">
                                </div>
                                <div id="trigger_click" style="<?php echo $trigger !== 'click' ? 'display:none;' : ''; ?>">
                                    <small>Voeg <code>data-popup="<?php echo $post->ID; ?>"</code> toe aan een element om de popup te triggeren.</small>
                                </div>
                                <div id="trigger_exit" style="<?php echo $trigger !== 'exit' ? 'display:none;' : ''; ?>">
                                    <small>Popup verschijnt wanneer de bezoeker de pagina probeert te verlaten.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="writgo-popup-row">
                        <label>Tonen op</label>
                        <div>
                            <select name="popup_show_on" id="popup_show_on">
                                <option value="all" <?php selected($show_on, 'all'); ?>>Alle pagina's</option>
                                <option value="home" <?php selected($show_on, 'home'); ?>>Alleen homepage</option>
                                <option value="posts" <?php selected($show_on, 'posts'); ?>>Alleen blogposts</option>
                                <option value="pages" <?php selected($show_on, 'pages'); ?>>Alleen pagina's</option>
                                <option value="specific" <?php selected($show_on, 'specific'); ?>>Specifieke pagina's (ID's)</option>
                            </select>
                            <div id="show_on_specific" style="<?php echo $show_on !== 'specific' ? 'display:none;' : ''; ?> margin-top: 10px;">
                                <input type="text" name="popup_show_on_pages" value="<?php echo esc_attr($show_on_pages); ?>" placeholder="123, 456, 789">
                                <small>Komma-gescheiden post/page ID's</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="writgo-popup-row">
                        <label>Cookie duur</label>
                        <div>
                            <input type="number" name="popup_cookie_days" value="<?php echo esc_attr($cookie_days); ?>" min="0" max="365" style="width: 80px;"> dagen
                            <small>Hoe lang popup niet meer getoond wordt na sluiten (0 = altijd tonen)</small>
                        </div>
                    </div>
                </div>
                
                <!-- Design Section -->
                <div class="writgo-popup-section" style="margin-top: 20px;">
                    <h4>🎨 Kleuren</h4>
                    
                    <div class="writgo-popup-row">
                        <label>Achtergrond</label>
                        <div class="color-row">
                            <input type="color" name="popup_bg_color" value="<?php echo esc_attr($bg_color); ?>">
                            <input type="text" value="<?php echo esc_attr($bg_color); ?>" style="width: 100px;" readonly>
                        </div>
                    </div>
                    
                    <div class="writgo-popup-row">
                        <label>Tekst kleur</label>
                        <div class="color-row">
                            <input type="color" name="popup_text_color" value="<?php echo esc_attr($text_color); ?>">
                            <input type="text" value="<?php echo esc_attr($text_color); ?>" style="width: 100px;" readonly>
                        </div>
                    </div>
                    
                    <div class="writgo-popup-row">
                        <label>Button kleur</label>
                        <div class="color-row">
                            <input type="color" name="popup_button_color" value="<?php echo esc_attr($button_color); ?>">
                            <input type="text" value="<?php echo esc_attr($button_color); ?>" style="width: 100px;" readonly>
                        </div>
                    </div>
                    
                    <div class="writgo-popup-row">
                        <label>Overlay kleur</label>
                        <div>
                            <input type="text" name="popup_overlay_color" value="<?php echo esc_attr($overlay_color); ?>" placeholder="rgba(0,0,0,0.6)">
                            <small>Gebruik rgba() voor transparantie</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Content -->
            <div>
                <div class="writgo-popup-section">
                    <h4>📝 Content</h4>
                    
                    <div class="writgo-popup-row">
                        <label>Icoon (emoji)</label>
                        <div>
                            <input type="text" name="popup_icon" value="<?php echo esc_attr($icon); ?>" style="width: 80px;">
                            <small>🎁 💰 🔥 ⭐ 🚀 💡 📢 🏆 ⏰ 🎉</small>
                        </div>
                    </div>
                    
                    <div class="writgo-popup-row">
                        <label>Titel</label>
                        <input type="text" name="popup_title" value="<?php echo esc_attr($title); ?>" placeholder="Exclusieve Aanbieding!">
                    </div>
                    
                    <div class="writgo-popup-row">
                        <label>Tekst</label>
                        <textarea name="popup_text" placeholder="Beschrijving van je aanbieding..."><?php echo esc_textarea($text); ?></textarea>
                    </div>
                    
                    <div class="writgo-popup-row">
                        <label>Afbeelding</label>
                        <div>
                            <input type="url" name="popup_image" value="<?php echo esc_url($image); ?>" placeholder="https://..." id="popup_image_url">
                            <button type="button" class="button" id="upload_popup_image">Upload</button>
                            <?php if ($image) : ?>
                                <img src="<?php echo esc_url($image); ?>" class="writgo-image-preview" id="popup_image_preview">
                            <?php else : ?>
                                <img src="" class="writgo-image-preview" id="popup_image_preview" style="display:none;">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <hr style="margin: 20px 0; border: none; border-top: 1px solid #e0e0e0;">
                    
                    <div class="writgo-popup-row">
                        <label>Button tekst</label>
                        <input type="text" name="popup_button_text" value="<?php echo esc_attr($button_text); ?>" placeholder="Bekijk Aanbieding">
                    </div>
                    
                    <div class="writgo-popup-row">
                        <label>Button URL</label>
                        <input type="url" name="popup_button_url" value="<?php echo esc_url($button_url); ?>" placeholder="https://...">
                    </div>
                    
                    <hr style="margin: 20px 0; border: none; border-top: 1px solid #e0e0e0;">
                    
                    <div class="writgo-popup-row">
                        <label>E-mail formulier</label>
                        <div class="writgo-checkbox-row">
                            <input type="checkbox" name="popup_show_email" value="1" <?php checked($show_email, '1'); ?> id="show_email_check">
                            <span>Toon e-mail inschrijfveld</span>
                        </div>
                    </div>
                    
                    <div id="email_options" style="<?php echo !$show_email ? 'display:none;' : ''; ?>">
                        <div class="writgo-popup-row">
                            <label>Placeholder</label>
                            <input type="text" name="popup_email_placeholder" value="<?php echo esc_attr($email_placeholder); ?>">
                        </div>
                        <div class="writgo-popup-row">
                            <label>Button tekst</label>
                            <input type="text" name="popup_email_button" value="<?php echo esc_attr($email_button); ?>">
                        </div>
                    </div>
                    
                    <hr style="margin: 20px 0; border: none; border-top: 1px solid #e0e0e0;">
                    
                    <div class="writgo-popup-row">
                        <label>Countdown timer</label>
                        <div class="writgo-checkbox-row">
                            <input type="checkbox" name="popup_countdown" value="1" <?php checked($countdown_enabled, '1'); ?> id="countdown_check">
                            <span>Toon countdown timer</span>
                        </div>
                    </div>
                    
                    <div id="countdown_options" style="<?php echo !$countdown_enabled ? 'display:none;' : ''; ?>">
                        <div class="writgo-popup-row">
                            <label>Minuten</label>
                            <input type="number" name="popup_countdown_minutes" value="<?php echo esc_attr($countdown_minutes); ?>" min="1" max="1440" style="width: 80px;">
                        </div>
                    </div>
                    
                    <div class="writgo-popup-row">
                        <label>Sluit tekst</label>
                        <div>
                            <input type="text" name="popup_close_text" value="<?php echo esc_attr($close_text); ?>" placeholder="Nee bedankt, ik wil geen korting">
                            <small>Optionele tekst onder de button (laat leeg voor alleen X knop)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Trigger type toggle
        $('#popup_trigger').on('change', function() {
            var val = $(this).val();
            $('#trigger_time, #trigger_scroll, #trigger_pages, #trigger_click, #trigger_exit').hide();
            $('#trigger_' + val).show();
        });
        
        // Show on toggle
        $('#popup_show_on').on('change', function() {
            $('#show_on_specific').toggle($(this).val() === 'specific');
        });
        
        // Email toggle
        $('#show_email_check').on('change', function() {
            $('#email_options').toggle(this.checked);
        });
        
        // Countdown toggle
        $('#countdown_check').on('change', function() {
            $('#countdown_options').toggle(this.checked);
        });
        
        // Type change - auto set trigger for exit intent
        $('#popup_type').on('change', function() {
            if ($(this).val() === 'exit') {
                $('#popup_trigger').val('exit').trigger('change');
                $('#trigger_row').hide();
            } else {
                $('#trigger_row').show();
            }
        }).trigger('change');
        
        // Media uploader
        $('#upload_popup_image').on('click', function(e) {
            e.preventDefault();
            var image = wp.media({
                title: 'Kies een afbeelding',
                multiple: false
            }).open().on('select', function() {
                var uploaded = image.state().get('selection').first().toJSON();
                $('#popup_image_url').val(uploaded.url);
                $('#popup_image_preview').attr('src', uploaded.url).show();
            });
        });
        
        // Color sync
        $('input[type="color"]').on('input', function() {
            $(this).next('input[type="text"]').val($(this).val());
        });
    });
    </script>
    <?php
}

// =============================================================================
// SAVE META
// =============================================================================

add_action('save_post_writgo_popup', 'writgo_save_popup_meta');
function writgo_save_popup_meta($post_id) {
    if (!isset($_POST['writgo_popup_nonce']) || !wp_verify_nonce($_POST['writgo_popup_nonce'], 'writgo_popup_save')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    $fields = array(
        'popup_type' => 'sanitize_text_field',
        'popup_trigger' => 'sanitize_text_field',
        'popup_trigger_delay' => 'absint',
        'popup_trigger_scroll' => 'absint',
        'popup_trigger_pages' => 'absint',
        'popup_icon' => 'sanitize_text_field',
        'popup_title' => 'sanitize_text_field',
        'popup_text' => 'wp_kses_post',
        'popup_image' => 'esc_url_raw',
        'popup_button_text' => 'sanitize_text_field',
        'popup_button_url' => 'esc_url_raw',
        'popup_button_color' => 'sanitize_hex_color',
        'popup_show_email' => 'sanitize_text_field',
        'popup_email_placeholder' => 'sanitize_text_field',
        'popup_email_button' => 'sanitize_text_field',
        'popup_bg_color' => 'sanitize_hex_color',
        'popup_text_color' => 'sanitize_hex_color',
        'popup_overlay_color' => 'sanitize_text_field',
        'popup_cookie_days' => 'absint',
        'popup_show_on' => 'sanitize_text_field',
        'popup_show_on_pages' => 'sanitize_text_field',
        'popup_active' => 'sanitize_text_field',
        'popup_close_text' => 'sanitize_text_field',
        'popup_countdown' => 'sanitize_text_field',
        'popup_countdown_minutes' => 'absint',
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
// FRONTEND OUTPUT
// =============================================================================

add_action('wp_footer', 'writgo_render_popups');
function writgo_render_popups() {
    // Don't show in admin or to logged-in admins editing
    if (is_admin()) {
        return;
    }
    
    // Get all active popups
    $popups = get_posts(array(
        'post_type'      => 'writgo_popup',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => '_popup_active',
        'meta_value'     => '1',
    ));
    
    if (empty($popups)) {
        return;
    }
    
    $popups_data = array();
    
    foreach ($popups as $popup) {
        $id = $popup->ID;
        
        // Check show_on conditions
        $show_on = get_post_meta($id, '_popup_show_on', true) ?: 'all';
        $should_show = false;
        
        switch ($show_on) {
            case 'all':
                $should_show = true;
                break;
            case 'home':
                $should_show = is_front_page() || is_home();
                break;
            case 'posts':
                $should_show = is_singular('post');
                break;
            case 'pages':
                $should_show = is_page();
                break;
            case 'specific':
                $page_ids = array_map('trim', explode(',', get_post_meta($id, '_popup_show_on_pages', true)));
                $should_show = in_array(get_the_ID(), $page_ids);
                break;
        }
        
        if (!$should_show) {
            continue;
        }
        
        // Gather popup data
        $type = get_post_meta($id, '_popup_type', true) ?: 'popup';
        $trigger = get_post_meta($id, '_popup_trigger', true) ?: 'time';
        
        // For exit type, always use exit trigger
        if ($type === 'exit') {
            $trigger = 'exit';
        }
        
        $data = array(
            'id' => $id,
            'type' => $type,
            'trigger' => $trigger,
            'triggerDelay' => (int) get_post_meta($id, '_popup_trigger_delay', true) ?: 5,
            'triggerScroll' => (int) get_post_meta($id, '_popup_trigger_scroll', true) ?: 50,
            'triggerPages' => (int) get_post_meta($id, '_popup_trigger_pages', true) ?: 2,
            'cookieDays' => (int) get_post_meta($id, '_popup_cookie_days', true) ?: 7,
            'countdownMinutes' => (int) get_post_meta($id, '_popup_countdown_minutes', true) ?: 15,
        );
        
        $popups_data[] = $data;
        
        // Render popup HTML
        $icon = get_post_meta($id, '_popup_icon', true) ?: '🎁';
        $title = get_post_meta($id, '_popup_title', true);
        $text = get_post_meta($id, '_popup_text', true);
        $image = get_post_meta($id, '_popup_image', true);
        $button_text = get_post_meta($id, '_popup_button_text', true) ?: 'Bekijk Aanbieding';
        $button_url = get_post_meta($id, '_popup_button_url', true);
        $button_color = get_post_meta($id, '_popup_button_color', true) ?: '#f97316';
        $show_email = get_post_meta($id, '_popup_show_email', true);
        $email_placeholder = get_post_meta($id, '_popup_email_placeholder', true) ?: 'Je e-mailadres';
        $email_button = get_post_meta($id, '_popup_email_button', true) ?: 'Aanmelden';
        $bg_color = get_post_meta($id, '_popup_bg_color', true) ?: '#ffffff';
        $text_color = get_post_meta($id, '_popup_text_color', true) ?: '#1a202c';
        $overlay_color = get_post_meta($id, '_popup_overlay_color', true) ?: 'rgba(0,0,0,0.6)';
        $close_text = get_post_meta($id, '_popup_close_text', true);
        $countdown_enabled = get_post_meta($id, '_popup_countdown', true);
        
        $popup_class = 'wa-popup wa-popup-' . $type;
        ?>
        
        <div id="wa-popup-<?php echo $id; ?>" class="<?php echo esc_attr($popup_class); ?>" data-popup-id="<?php echo $id; ?>" style="--popup-bg: <?php echo esc_attr($bg_color); ?>; --popup-text: <?php echo esc_attr($text_color); ?>; --popup-button: <?php echo esc_attr($button_color); ?>; --popup-overlay: <?php echo esc_attr($overlay_color); ?>;">
            
            <?php if ($type !== 'slidein') : ?>
            <div class="wa-popup-overlay"></div>
            <?php endif; ?>
            
            <div class="wa-popup-container">
                <button class="wa-popup-close" aria-label="Sluiten">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
                
                <?php if ($image) : ?>
                <div class="wa-popup-image">
                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                </div>
                <?php endif; ?>
                
                <div class="wa-popup-content">
                    <?php if ($icon) : ?>
                    <span class="wa-popup-icon"><?php echo esc_html($icon); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($title) : ?>
                    <h3 class="wa-popup-title"><?php echo esc_html($title); ?></h3>
                    <?php endif; ?>
                    
                    <?php if ($countdown_enabled) : ?>
                    <div class="wa-popup-countdown" data-minutes="<?php echo esc_attr(get_post_meta($id, '_popup_countdown_minutes', true) ?: 15); ?>">
                        <span class="wa-countdown-label">Aanbieding verloopt over:</span>
                        <div class="wa-countdown-timer">
                            <span class="wa-countdown-minutes">00</span>:<span class="wa-countdown-seconds">00</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($text) : ?>
                    <div class="wa-popup-text"><?php echo wp_kses_post($text); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($show_email) : ?>
                    <form class="wa-popup-email-form" action="#" method="post">
                        <input type="email" placeholder="<?php echo esc_attr($email_placeholder); ?>" required>
                        <button type="submit"><?php echo esc_html($email_button); ?></button>
                    </form>
                    <?php endif; ?>
                    
                    <?php if ($button_url && !$show_email) : ?>
                    <a href="<?php echo esc_url($button_url); ?>" class="wa-popup-button" target="_blank" rel="noopener sponsored">
                        <?php echo esc_html($button_text); ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($close_text) : ?>
                    <button class="wa-popup-decline"><?php echo esc_html($close_text); ?></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php
    }
    
    // Output popup data for JavaScript
    if (!empty($popups_data)) {
        echo '<script>var writgoPopups = ' . json_encode($popups_data) . ';</script>';
    }
}

// =============================================================================
// FRONTEND STYLES
// =============================================================================

add_action('wp_head', 'writgo_popup_styles');
function writgo_popup_styles() {
    ?>
    <style>
    /* ==========================================================================
       POPUP BASE STYLES
       ========================================================================== */
    .wa-popup {
        position: fixed;
        z-index: 99999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    
    .wa-popup.active {
        opacity: 1;
        visibility: visible;
    }
    
    .wa-popup-overlay {
        position: fixed;
        inset: 0;
        background: var(--popup-overlay, rgba(0,0,0,0.6));
        backdrop-filter: blur(4px);
    }
    
    /* ==========================================================================
       CENTER POPUP (MODAL)
       ========================================================================== */
    .wa-popup-popup,
    .wa-popup-exit {
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .wa-popup-popup .wa-popup-container,
    .wa-popup-exit .wa-popup-container {
        position: relative;
        background: var(--popup-bg, #fff);
        color: var(--popup-text, #1a202c);
        border-radius: 20px;
        max-width: 480px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 80px rgba(0,0,0,0.3);
        transform: scale(0.9) translateY(20px);
        transition: transform 0.3s ease;
    }
    
    .wa-popup-popup.active .wa-popup-container,
    .wa-popup-exit.active .wa-popup-container {
        transform: scale(1) translateY(0);
    }
    
    /* ==========================================================================
       SLIDE-IN (BOTTOM RIGHT)
       ========================================================================== */
    .wa-popup-slidein {
        bottom: 20px;
        right: 20px;
        max-width: 380px;
        width: calc(100% - 40px);
    }
    
    .wa-popup-slidein .wa-popup-container {
        position: relative;
        background: var(--popup-bg, #fff);
        color: var(--popup-text, #1a202c);
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        transform: translateX(120%);
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .wa-popup-slidein.active .wa-popup-container {
        transform: translateX(0);
    }
    
    /* ==========================================================================
       FULLSCREEN
       ========================================================================== */
    .wa-popup-fullscreen {
        inset: 0;
    }
    
    .wa-popup-fullscreen .wa-popup-overlay {
        background: var(--popup-bg, #1a365d);
    }
    
    .wa-popup-fullscreen .wa-popup-container {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 40px 20px;
        text-align: center;
        color: var(--popup-text, #fff);
        max-width: 600px;
        margin: 0 auto;
    }
    
    /* ==========================================================================
       POPUP ELEMENTS
       ========================================================================== */
    .wa-popup-close {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 36px;
        height: 36px;
        border: none;
        background: rgba(0,0,0,0.1);
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        z-index: 10;
        color: inherit;
    }
    
    .wa-popup-close:hover {
        background: rgba(0,0,0,0.2);
        transform: rotate(90deg);
    }
    
    .wa-popup-image {
        border-radius: 20px 20px 0 0;
        overflow: hidden;
    }
    
    .wa-popup-slidein .wa-popup-image {
        border-radius: 16px 16px 0 0;
    }
    
    .wa-popup-image img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .wa-popup-content {
        padding: 24px;
        text-align: center;
    }
    
    .wa-popup-icon {
        font-size: 3rem;
        display: block;
        margin-bottom: 12px;
        animation: wa-bounce 1s ease infinite;
    }
    
    @keyframes wa-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    
    .wa-popup-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0 0 12px;
        line-height: 1.3;
    }
    
    .wa-popup-slidein .wa-popup-title {
        font-size: 1.25rem;
    }
    
    .wa-popup-text {
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 20px;
        opacity: 0.9;
    }
    
    .wa-popup-text p {
        margin: 0;
    }
    
    /* Countdown */
    .wa-popup-countdown {
        background: rgba(0,0,0,0.08);
        border-radius: 10px;
        padding: 12px 20px;
        margin-bottom: 20px;
        display: inline-block;
    }
    
    .wa-countdown-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        opacity: 0.7;
        display: block;
        margin-bottom: 4px;
    }
    
    .wa-countdown-timer {
        font-size: 1.75rem;
        font-weight: 800;
        font-family: monospace;
        color: var(--popup-button, #f97316);
    }
    
    /* Button */
    .wa-popup-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 32px;
        background: var(--popup-button, #f97316);
        color: #fff !important;
        font-size: 1rem;
        font-weight: 700;
        text-decoration: none !important;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .wa-popup-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        filter: brightness(1.1);
    }
    
    /* Email Form */
    .wa-popup-email-form {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
    }
    
    .wa-popup-email-form input {
        flex: 1;
        padding: 12px 16px;
        border: 2px solid rgba(0,0,0,0.1);
        border-radius: 8px;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.2s;
    }
    
    .wa-popup-email-form input:focus {
        border-color: var(--popup-button, #f97316);
    }
    
    .wa-popup-email-form button {
        padding: 12px 20px;
        background: var(--popup-button, #f97316);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }
    
    .wa-popup-email-form button:hover {
        filter: brightness(1.1);
    }
    
    /* Decline button */
    .wa-popup-decline {
        display: block;
        width: 100%;
        margin-top: 12px;
        padding: 8px;
        background: none;
        border: none;
        color: inherit;
        opacity: 0.6;
        font-size: 0.875rem;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    
    .wa-popup-decline:hover {
        opacity: 1;
        text-decoration: underline;
    }
    
    /* ==========================================================================
       RESPONSIVE
       ========================================================================== */
    @media (max-width: 480px) {
        .wa-popup-slidein {
            bottom: 10px;
            right: 10px;
            left: 10px;
            max-width: none;
            width: auto;
        }
        
        .wa-popup-content {
            padding: 20px;
        }
        
        .wa-popup-email-form {
            flex-direction: column;
        }
        
        .wa-popup-title {
            font-size: 1.25rem;
        }
    }
    </style>
    <?php
}

// =============================================================================
// FRONTEND JAVASCRIPT
// =============================================================================

add_action('wp_footer', 'writgo_popup_scripts', 99);
function writgo_popup_scripts() {
    ?>
    <script>
    (function() {
        if (typeof writgoPopups === 'undefined' || !writgoPopups.length) return;
        
        // Cookie helpers
        function setCookie(name, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + (value || '') + expires + '; path=/';
        }
        
        function getCookie(name) {
            var nameEQ = name + '=';
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
        
        // Page views counter
        var pageViews = parseInt(getCookie('wa_page_views') || '0') + 1;
        setCookie('wa_page_views', pageViews, 1);
        
        // Show popup function
        function showPopup(id) {
            var popup = document.getElementById('wa-popup-' + id);
            if (!popup || popup.classList.contains('active')) return;
            
            popup.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Start countdown if exists
            var countdown = popup.querySelector('.wa-popup-countdown');
            if (countdown) {
                startCountdown(countdown);
            }
        }
        
        // Hide popup function
        function hidePopup(id, setCookieFlag) {
            var popup = document.getElementById('wa-popup-' + id);
            if (!popup) return;
            
            popup.classList.remove('active');
            document.body.style.overflow = '';
            
            // Set cookie if needed
            if (setCookieFlag !== false) {
                var config = writgoPopups.find(function(p) { return p.id == id; });
                if (config && config.cookieDays > 0) {
                    setCookie('wa_popup_closed_' + id, '1', config.cookieDays);
                }
            }
        }
        
        // Countdown timer
        function startCountdown(el) {
            var minutes = parseInt(el.dataset.minutes) || 15;
            var totalSeconds = minutes * 60;
            var stored = getCookie('wa_countdown_' + el.closest('.wa-popup').dataset.popupId);
            
            if (stored) {
                totalSeconds = parseInt(stored);
            }
            
            var minutesEl = el.querySelector('.wa-countdown-minutes');
            var secondsEl = el.querySelector('.wa-countdown-seconds');
            
            function update() {
                if (totalSeconds <= 0) {
                    minutesEl.textContent = '00';
                    secondsEl.textContent = '00';
                    return;
                }
                
                var m = Math.floor(totalSeconds / 60);
                var s = totalSeconds % 60;
                
                minutesEl.textContent = m.toString().padStart(2, '0');
                secondsEl.textContent = s.toString().padStart(2, '0');
                
                totalSeconds--;
                setCookie('wa_countdown_' + el.closest('.wa-popup').dataset.popupId, totalSeconds, 1);
                
                setTimeout(update, 1000);
            }
            
            update();
        }
        
        // Exit intent detection
        var exitIntentTriggered = false;
        function handleExitIntent(e) {
            if (exitIntentTriggered) return;
            if (e.clientY > 50) return;
            
            writgoPopups.forEach(function(config) {
                if (config.trigger === 'exit' && !getCookie('wa_popup_closed_' + config.id)) {
                    exitIntentTriggered = true;
                    showPopup(config.id);
                }
            });
        }
        
        // Initialize each popup
        writgoPopups.forEach(function(config) {
            // Check if already closed
            if (getCookie('wa_popup_closed_' + config.id)) {
                return;
            }
            
            switch (config.trigger) {
                case 'time':
                    setTimeout(function() {
                        showPopup(config.id);
                    }, config.triggerDelay * 1000);
                    break;
                    
                case 'scroll':
                    var scrollTriggered = false;
                    window.addEventListener('scroll', function() {
                        if (scrollTriggered) return;
                        var scrollPercent = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
                        if (scrollPercent >= config.triggerScroll) {
                            scrollTriggered = true;
                            showPopup(config.id);
                        }
                    });
                    break;
                    
                case 'exit':
                    document.addEventListener('mouseout', handleExitIntent);
                    break;
                    
                case 'pages':
                    if (pageViews >= config.triggerPages) {
                        setTimeout(function() {
                            showPopup(config.id);
                        }, 1000);
                    }
                    break;
                    
                case 'click':
                    document.querySelectorAll('[data-popup="' + config.id + '"]').forEach(function(el) {
                        el.addEventListener('click', function(e) {
                            e.preventDefault();
                            showPopup(config.id);
                        });
                    });
                    break;
            }
        });
        
        // Close handlers
        document.querySelectorAll('.wa-popup-close, .wa-popup-decline').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var popup = this.closest('.wa-popup');
                if (popup) {
                    hidePopup(popup.dataset.popupId);
                }
            });
        });
        
        // Close on overlay click
        document.querySelectorAll('.wa-popup-overlay').forEach(function(overlay) {
            overlay.addEventListener('click', function() {
                var popup = this.closest('.wa-popup');
                if (popup) {
                    hidePopup(popup.dataset.popupId);
                }
            });
        });
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.wa-popup.active').forEach(function(popup) {
                    hidePopup(popup.dataset.popupId);
                });
            }
        });
        
        // Email form handling
        document.querySelectorAll('.wa-popup-email-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var email = this.querySelector('input[type="email"]').value;
                var popup = this.closest('.wa-popup');
                
                // You can integrate with your email service here
                // For now, just show success
                this.innerHTML = '<p style="color: #10b981; font-weight: 600;">✓ Bedankt voor je aanmelding!</p>';
                
                setTimeout(function() {
                    hidePopup(popup.dataset.popupId);
                }, 2000);
            });
        });
    })();
    </script>
    <?php
}
