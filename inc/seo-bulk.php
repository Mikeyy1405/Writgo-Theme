<?php
/**
 * Writgo SEO - Bulk Editing & Admin Columns
 * 
 * SEO columns in post list, quick edit, bulk editing
 *
 * @package Writgo_Affiliate
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// ADMIN COLUMNS
// =============================================================================

// Add SEO columns to posts list
add_filter('manage_posts_columns', 'writgo_add_seo_columns');
add_filter('manage_pages_columns', 'writgo_add_seo_columns');
function writgo_add_seo_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        // Add SEO column after title
        if ($key === 'title') {
            $new_columns['writgo_seo_score'] = '<span title="SEO Score">📊 SEO</span>';
            $new_columns['writgo_focus_keyword'] = '🔑 Keyword';
            $new_columns['writgo_seo_title'] = '📝 SEO Titel';
        }
    }
    
    return $new_columns;
}

// Populate SEO columns
add_action('manage_posts_custom_column', 'writgo_populate_seo_columns', 10, 2);
add_action('manage_pages_custom_column', 'writgo_populate_seo_columns', 10, 2);
function writgo_populate_seo_columns($column, $post_id) {
    switch ($column) {
        case 'writgo_seo_score':
            $score = writgo_calculate_seo_score($post_id);
            $color = $score >= 70 ? '#16a34a' : ($score >= 40 ? '#d97706' : '#dc2626');
            echo '<div style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; background: ' . $color . '; color: white; font-weight: 700; font-size: 13px;">' . $score . '</div>';
            break;
            
        case 'writgo_focus_keyword':
            $keyword = get_post_meta($post_id, '_writgo_focus_keyword', true);
            if ($keyword) {
                echo '<span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 12px; font-size: 12px;">' . esc_html($keyword) . '</span>';
            } else {
                echo '<span style="color: #9ca3af;">—</span>';
            }
            break;
            
        case 'writgo_seo_title':
            $seo_title = get_post_meta($post_id, '_writgo_seo_title', true);
            if ($seo_title) {
                $len = strlen($seo_title);
                $color = ($len >= 30 && $len <= 60) ? '#16a34a' : '#d97706';
                echo '<span style="color: ' . $color . '; font-size: 13px;" title="' . esc_attr($seo_title) . '">' . esc_html(wp_trim_words($seo_title, 6, '...')) . ' <small>(' . $len . ')</small></span>';
            } else {
                echo '<span style="color: #9ca3af; font-size: 12px;">Auto</span>';
            }
            break;
    }
}

// Make columns sortable
add_filter('manage_edit-post_sortable_columns', 'writgo_sortable_seo_columns');
add_filter('manage_edit-page_sortable_columns', 'writgo_sortable_seo_columns');
function writgo_sortable_seo_columns($columns) {
    $columns['writgo_seo_score'] = 'writgo_seo_score';
    return $columns;
}

// Set column widths
add_action('admin_head', 'writgo_seo_column_styles');
function writgo_seo_column_styles() {
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->id, array('edit-post', 'edit-page'))) return;
    ?>
    <style>
        .column-writgo_seo_score { width: 60px; text-align: center; }
        .column-writgo_focus_keyword { width: 150px; }
        .column-writgo_seo_title { width: 200px; }
    </style>
    <?php
}

// =============================================================================
// QUICK EDIT
// =============================================================================

// Add quick edit fields
add_action('quick_edit_custom_box', 'writgo_quick_edit_fields', 10, 2);
function writgo_quick_edit_fields($column_name, $post_type) {
    if ($column_name !== 'writgo_focus_keyword') return;
    ?>
    <fieldset class="inline-edit-col-right" style="margin-top: 10px;">
        <div class="inline-edit-col">
            <label class="inline-edit-group">
                <span class="title" style="width: auto; margin-right: 10px;">🔑 Focus Keyword</span>
                <input type="text" name="writgo_focus_keyword" value="" style="width: 200px;">
            </label>
            <label class="inline-edit-group" style="margin-top: 5px;">
                <span class="title" style="width: auto; margin-right: 10px;">📝 SEO Titel</span>
                <input type="text" name="writgo_seo_title" value="" style="width: 300px;">
            </label>
        </div>
    </fieldset>
    <?php
}

// Save quick edit
add_action('save_post', 'writgo_save_quick_edit');
function writgo_save_quick_edit($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    // Only process from quick edit (check for inline-save action)
    if (!isset($_POST['_inline_edit'])) return;
    
    if (isset($_POST['writgo_focus_keyword'])) {
        $keyword = sanitize_text_field($_POST['writgo_focus_keyword']);
        if ($keyword) {
            update_post_meta($post_id, '_writgo_focus_keyword', $keyword);
        }
    }
    
    if (isset($_POST['writgo_seo_title'])) {
        $title = sanitize_text_field($_POST['writgo_seo_title']);
        if ($title) {
            update_post_meta($post_id, '_writgo_seo_title', $title);
        }
    }
}

// Quick edit JavaScript to populate fields
add_action('admin_footer', 'writgo_quick_edit_js');
function writgo_quick_edit_js() {
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->id, array('edit-post', 'edit-page'))) return;
    ?>
    <script>
    jQuery(function($) {
        var $inline_editor = inlineEditPost;
        var $original_inline_editor = $inline_editor.edit;
        
        $inline_editor.edit = function(id) {
            $original_inline_editor.apply(this, arguments);
            
            var post_id = 0;
            if (typeof(id) === 'object') {
                post_id = parseInt(this.getId(id));
            }
            
            if (post_id > 0) {
                var $row = $('#post-' + post_id);
                var $edit_row = $('#edit-' + post_id);
                
                // Get keyword from column
                var keyword = $row.find('.column-writgo_focus_keyword span').text();
                if (keyword && keyword !== '—') {
                    $edit_row.find('input[name="writgo_focus_keyword"]').val(keyword);
                }
                
                // Get SEO title from data attribute or fetch via AJAX
                // For now, leave empty to allow editing
            }
        };
    });
    </script>
    <?php
}

// =============================================================================
// BULK ACTIONS
// =============================================================================

// Add bulk action
add_filter('bulk_actions-edit-post', 'writgo_bulk_actions');
add_filter('bulk_actions-edit-page', 'writgo_bulk_actions');
function writgo_bulk_actions($bulk_actions) {
    $bulk_actions['writgo_generate_seo'] = '🤖 Auto-genereer SEO Titels';
    $bulk_actions['writgo_clear_seo'] = '🗑️ Verwijder SEO Data';
    return $bulk_actions;
}

// Handle bulk action
add_filter('handle_bulk_actions-edit-post', 'writgo_handle_bulk_actions', 10, 3);
add_filter('handle_bulk_actions-edit-page', 'writgo_handle_bulk_actions', 10, 3);
function writgo_handle_bulk_actions($redirect_to, $action, $post_ids) {
    if ($action === 'writgo_generate_seo') {
        foreach ($post_ids as $post_id) {
            $post = get_post($post_id);
            
            // Generate SEO title if empty
            if (!get_post_meta($post_id, '_writgo_seo_title', true)) {
                $title = $post->post_title;
                // Truncate to 60 chars if needed
                if (strlen($title) > 60) {
                    $title = substr($title, 0, 57) . '...';
                }
                update_post_meta($post_id, '_writgo_seo_title', $title);
            }
            
            // Generate meta description if empty
            if (!get_post_meta($post_id, '_writgo_seo_description', true)) {
                $excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(strip_tags($post->post_content), 25);
                $excerpt = substr($excerpt, 0, 160);
                update_post_meta($post_id, '_writgo_seo_description', $excerpt);
            }
        }
        
        $redirect_to = add_query_arg('writgo_bulk_seo', count($post_ids), $redirect_to);
        
    } elseif ($action === 'writgo_clear_seo') {
        foreach ($post_ids as $post_id) {
            delete_post_meta($post_id, '_writgo_seo_title');
            delete_post_meta($post_id, '_writgo_seo_description');
            delete_post_meta($post_id, '_writgo_focus_keyword');
        }
        
        $redirect_to = add_query_arg('writgo_bulk_clear', count($post_ids), $redirect_to);
    }
    
    return $redirect_to;
}

// Bulk action admin notice
add_action('admin_notices', 'writgo_bulk_action_notices');
function writgo_bulk_action_notices() {
    if (isset($_GET['writgo_bulk_seo'])) {
        $count = intval($_GET['writgo_bulk_seo']);
        echo '<div class="notice notice-success is-dismissible"><p>SEO data gegenereerd voor ' . $count . ' item(s).</p></div>';
    }
    
    if (isset($_GET['writgo_bulk_clear'])) {
        $count = intval($_GET['writgo_bulk_clear']);
        echo '<div class="notice notice-success is-dismissible"><p>SEO data verwijderd voor ' . $count . ' item(s).</p></div>';
    }
}

// =============================================================================
// SEO SCORE CALCULATION
// =============================================================================

function writgo_calculate_seo_score($post_id) {
    $post = get_post($post_id);
    if (!$post) return 0;
    
    $score = 0;
    
    $title = $post->post_title;
    $content = $post->post_content;
    $text = wp_strip_all_tags($content);
    $word_count = str_word_count($text);
    
    $seo_title = get_post_meta($post_id, '_writgo_seo_title', true);
    $seo_desc = get_post_meta($post_id, '_writgo_seo_description', true);
    $focus_keyword = get_post_meta($post_id, '_writgo_focus_keyword', true);
    
    // Use SEO title or post title
    $effective_title = $seo_title ?: $title;
    
    // 1. Focus keyword (15 points)
    if (!empty($focus_keyword)) {
        $kw_lower = mb_strtolower($focus_keyword, 'UTF-8');
        $score += 5; // Has keyword
        
        // Keyword in title
        if (mb_stripos($effective_title, $kw_lower) !== false) {
            $score += 5;
        }
        
        // Keyword in description
        if (!empty($seo_desc) && mb_stripos($seo_desc, $kw_lower) !== false) {
            $score += 5;
        }
    }
    
    // 2. Keyword in content (10 points)
    if (!empty($focus_keyword)) {
        $kw_lower = mb_strtolower($focus_keyword, 'UTF-8');
        $text_lower = mb_strtolower($text, 'UTF-8');
        if (mb_stripos($text_lower, $kw_lower) !== false) {
            $score += 5;
        }
        // Keyword in first paragraph (5 points)
        $first_para = mb_substr($text_lower, 0, 300);
        if (mb_stripos($first_para, $kw_lower) !== false) {
            $score += 5;
        }
    }
    
    // 3. Title length (10 points)
    $title_len = mb_strlen($effective_title, 'UTF-8');
    if ($title_len >= 30 && $title_len <= 60) {
        $score += 10;
    } elseif ($title_len > 0 && ($title_len < 30 || $title_len > 60)) {
        $score += 5;
    }
    
    // 4. Description length (10 points)
    if (!empty($seo_desc)) {
        $desc_len = mb_strlen($seo_desc, 'UTF-8');
        if ($desc_len >= 120 && $desc_len <= 160) {
            $score += 10;
        } elseif ($desc_len >= 50) {
            $score += 5;
        }
    }
    
    // 5. Content length (15 points)
    if ($word_count >= 1000) {
        $score += 15;
    } elseif ($word_count >= 500) {
        $score += 12;
    } elseif ($word_count >= 300) {
        $score += 7;
    } else {
        $score += 3;
    }
    
    // 6. Featured image (8 points)
    if (has_post_thumbnail($post_id)) {
        $score += 8;
    }
    
    // 7. Internal links (8 points)
    $home_url = home_url();
    preg_match_all('/href=["\']([^"\']+)["\']/', $content, $link_matches);
    $internal_links = 0;
    $external_links = 0;
    if (!empty($link_matches[1])) {
        foreach ($link_matches[1] as $link) {
            if (strpos($link, $home_url) !== false || strpos($link, '/') === 0) {
                $internal_links++;
            } elseif (strpos($link, 'http') === 0) {
                $external_links++;
            }
        }
    }
    
    if ($internal_links >= 2) {
        $score += 8;
    } elseif ($internal_links === 1) {
        $score += 4;
    }
    
    // 8. External links (5 points)
    if ($external_links >= 1) {
        $score += 5;
    } else {
        $score += 2;
    }
    
    // 9. Headings (4 points)
    $h2_count = preg_match_all('/<h2/i', $content);
    $h3_count = preg_match_all('/<h3/i', $content);
    if ($h2_count >= 2 && $h3_count >= 1) {
        $score += 4;
    } elseif ($h2_count >= 1) {
        $score += 2;
    }
    
    return min(100, $score);
}

// =============================================================================
// BULK EDIT PAGE
// =============================================================================

add_action('admin_menu', 'writgo_bulk_edit_menu', 22);
function writgo_bulk_edit_menu() {
    add_submenu_page(
        'writgo-dashboard',
        'Bulk SEO Editor',
        '✏️ Bulk Editor',
        'edit_posts',
        'writgo-bulk-editor',
        'writgo_bulk_editor_page'
    );
}

function writgo_bulk_editor_page() {
    // Handle form submission
    if (isset($_POST['writgo_bulk_save']) && wp_verify_nonce($_POST['writgo_bulk_nonce'], 'writgo_bulk_save')) {
        if (isset($_POST['seo_data']) && is_array($_POST['seo_data'])) {
            foreach ($_POST['seo_data'] as $post_id => $data) {
                $post_id = intval($post_id);
                if (!current_user_can('edit_post', $post_id)) continue;
                
                if (isset($data['title'])) {
                    update_post_meta($post_id, '_writgo_seo_title', sanitize_text_field($data['title']));
                }
                if (isset($data['desc'])) {
                    update_post_meta($post_id, '_writgo_seo_description', sanitize_textarea_field($data['desc']));
                }
                if (isset($data['keyword'])) {
                    update_post_meta($post_id, '_writgo_focus_keyword', sanitize_text_field($data['keyword']));
                }
            }
        }
        echo '<div class="notice notice-success"><p>SEO data opgeslagen!</p></div>';
    }
    
    // Get posts
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    
    $args = array(
        'post_type' => array('post', 'page'),
        'post_status' => 'publish',
        'posts_per_page' => $per_page,
        'paged' => $paged,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    // Filter
    if (isset($_GET['filter']) && $_GET['filter'] === 'no_seo') {
        $args['meta_query'] = array(
            'relation' => 'OR',
            array('key' => '_writgo_seo_title', 'compare' => 'NOT EXISTS'),
            array('key' => '_writgo_seo_title', 'value' => ''),
        );
    }
    
    $query = new WP_Query($args);
    $posts = $query->posts;
    $total_pages = $query->max_num_pages;
    ?>
    
    <style>
        .writgo-bulk-page { max-width: 1400px; margin: 20px auto; padding: 0 20px; }
        .writgo-bulk-filters { display: flex; gap: 10px; margin-bottom: 20px; }
        .writgo-bulk-filters a { padding: 8px 16px; background: #f3f4f6; border-radius: 6px; text-decoration: none; color: #374151; }
        .writgo-bulk-filters a.active { background: #f97316; color: white; }
        .writgo-bulk-table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .writgo-bulk-table th, .writgo-bulk-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .writgo-bulk-table th { background: #f9fafb; font-weight: 600; font-size: 13px; }
        .writgo-bulk-table input[type="text"], .writgo-bulk-table textarea { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; }
        .writgo-bulk-table input:focus, .writgo-bulk-table textarea:focus { border-color: #f97316; outline: none; }
        .writgo-bulk-table textarea { resize: vertical; min-height: 50px; }
        .writgo-post-title { font-weight: 600; color: #1f2937; margin-bottom: 5px; }
        .writgo-post-title a { color: inherit; text-decoration: none; }
        .writgo-post-title a:hover { color: #f97316; }
        .writgo-post-type { font-size: 11px; color: #9ca3af; text-transform: uppercase; }
        .writgo-char-count { font-size: 11px; color: #9ca3af; margin-top: 3px; }
        .writgo-char-count.good { color: #16a34a; }
        .writgo-char-count.warning { color: #d97706; }
        .writgo-bulk-submit { position: sticky; bottom: 0; background: white; padding: 20px; border-top: 1px solid #e5e7eb; text-align: center; }
        .writgo-pagination { display: flex; justify-content: center; gap: 5px; margin-top: 20px; }
        .writgo-pagination a, .writgo-pagination span { padding: 8px 14px; background: #f3f4f6; border-radius: 6px; text-decoration: none; color: #374151; }
        .writgo-pagination span.current { background: #f97316; color: white; }
    </style>
    
    <div class="writgo-bulk-page">
        <h1 style="display: flex; align-items: center; gap: 10px;">✏️ Bulk SEO Editor</h1>
        
        <div class="writgo-bulk-filters">
            <a href="<?php echo admin_url('admin.php?page=writgo-bulk-editor'); ?>" class="<?php echo !isset($_GET['filter']) ? 'active' : ''; ?>">Alle</a>
            <a href="<?php echo admin_url('admin.php?page=writgo-bulk-editor&filter=no_seo'); ?>" class="<?php echo isset($_GET['filter']) && $_GET['filter'] === 'no_seo' ? 'active' : ''; ?>">Zonder SEO</a>
        </div>
        
        <form method="post">
            <?php wp_nonce_field('writgo_bulk_save', 'writgo_bulk_nonce'); ?>
            
            <table class="writgo-bulk-table">
                <thead>
                    <tr>
                        <th style="width: 200px;">Artikel</th>
                        <th>SEO Titel</th>
                        <th>Meta Omschrijving</th>
                        <th style="width: 150px;">Focus Keyword</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $p) : 
                        $seo_title = get_post_meta($p->ID, '_writgo_seo_title', true);
                        $seo_desc = get_post_meta($p->ID, '_writgo_seo_description', true);
                        $keyword = get_post_meta($p->ID, '_writgo_focus_keyword', true);
                    ?>
                    <tr>
                        <td>
                            <div class="writgo-post-title">
                                <a href="<?php echo get_edit_post_link($p->ID); ?>"><?php echo esc_html($p->post_title); ?></a>
                            </div>
                            <div class="writgo-post-type"><?php echo $p->post_type; ?></div>
                        </td>
                        <td>
                            <input type="text" 
                                   name="seo_data[<?php echo $p->ID; ?>][title]" 
                                   value="<?php echo esc_attr($seo_title); ?>" 
                                   placeholder="<?php echo esc_attr($p->post_title); ?>"
                                   onkeyup="updateCharCount(this, 60)">
                            <div class="writgo-char-count" id="count-title-<?php echo $p->ID; ?>">
                                <?php echo strlen($seo_title); ?>/60
                            </div>
                        </td>
                        <td>
                            <textarea name="seo_data[<?php echo $p->ID; ?>][desc]" 
                                      rows="2" 
                                      placeholder="Voer meta omschrijving in..."
                                      onkeyup="updateCharCount(this, 160)"><?php echo esc_textarea($seo_desc); ?></textarea>
                            <div class="writgo-char-count" id="count-desc-<?php echo $p->ID; ?>">
                                <?php echo strlen($seo_desc); ?>/160
                            </div>
                        </td>
                        <td>
                            <input type="text" 
                                   name="seo_data[<?php echo $p->ID; ?>][keyword]" 
                                   value="<?php echo esc_attr($keyword); ?>" 
                                   placeholder="keyword">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="writgo-bulk-submit">
                <button type="submit" name="writgo_bulk_save" class="button button-primary button-large">
                    💾 Alle Wijzigingen Opslaan
                </button>
            </div>
        </form>
        
        <?php if ($total_pages > 1) : ?>
        <div class="writgo-pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <?php if ($i === $paged) : ?>
                    <span class="current"><?php echo $i; ?></span>
                <?php else : ?>
                    <a href="<?php echo add_query_arg('paged', $i); ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
    function updateCharCount(el, max) {
        var len = el.value.length;
        var countEl = el.parentElement.querySelector('.writgo-char-count');
        countEl.textContent = len + '/' + max;
        countEl.className = 'writgo-char-count ' + (len > max ? 'warning' : (len > 0 ? 'good' : ''));
    }
    </script>
    <?php
}

// =============================================================================
// CATEGORY & TAG SEO
// =============================================================================

// Add SEO fields to category/tag edit forms
add_action('category_edit_form_fields', 'writgo_taxonomy_seo_fields', 10, 1);
add_action('post_tag_edit_form_fields', 'writgo_taxonomy_seo_fields', 10, 1);
function writgo_taxonomy_seo_fields($term) {
    $seo_title = get_term_meta($term->term_id, '_writgo_seo_title', true);
    $seo_desc = get_term_meta($term->term_id, '_writgo_seo_description', true);
    $noindex = get_term_meta($term->term_id, '_writgo_noindex', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="writgo_seo_title">SEO Titel</label></th>
        <td>
            <input type="text" name="writgo_seo_title" id="writgo_seo_title" value="<?php echo esc_attr($seo_title); ?>" style="width: 100%;">
            <p class="description">Laat leeg om de standaard categorie titel te gebruiken.</p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="writgo_seo_description">Meta Omschrijving</label></th>
        <td>
            <textarea name="writgo_seo_description" id="writgo_seo_description" rows="3" style="width: 100%;"><?php echo esc_textarea($seo_desc); ?></textarea>
            <p class="description">Omschrijving voor zoekmachines (max 160 tekens).</p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row">Robots</th>
        <td>
            <label>
                <input type="checkbox" name="writgo_noindex" value="1" <?php checked($noindex, '1'); ?>>
                Verberg deze categorie voor zoekmachines (noindex)
            </label>
        </td>
    </tr>
    <?php
}

// Save taxonomy SEO fields
add_action('edited_category', 'writgo_save_taxonomy_seo', 10, 1);
add_action('edited_post_tag', 'writgo_save_taxonomy_seo', 10, 1);
function writgo_save_taxonomy_seo($term_id) {
    if (isset($_POST['writgo_seo_title'])) {
        update_term_meta($term_id, '_writgo_seo_title', sanitize_text_field($_POST['writgo_seo_title']));
    }
    if (isset($_POST['writgo_seo_description'])) {
        update_term_meta($term_id, '_writgo_seo_description', sanitize_textarea_field($_POST['writgo_seo_description']));
    }
    update_term_meta($term_id, '_writgo_noindex', isset($_POST['writgo_noindex']) ? '1' : '');
}
