<?php
/**
 * Writgo SEO - Technical SEO Module
 * 
 * Redirects, 404 Monitor, Robots.txt, Analytics
 * Note: Sitemap functionality is in seo-sitemap.php
 *
 * @package Writgo_Affiliate
 * @version 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// REDIRECT MANAGER
// =============================================================================

// Create redirects table
add_action('after_switch_theme', 'writgo_create_redirects_table');
function writgo_create_redirects_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'writgo_redirects';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        source_url varchar(500) NOT NULL,
        target_url varchar(500) NOT NULL,
        redirect_type varchar(10) DEFAULT '301',
        hits int DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY source_url (source_url(191))
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Handle redirects
add_action('template_redirect', 'writgo_handle_redirects', 1);
function writgo_handle_redirects() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'writgo_redirects';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        return;
    }
    
    $current_url = trim($_SERVER['REQUEST_URI'], '/');
    
    $redirect = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE source_url = %s OR source_url = %s",
        $current_url,
        '/' . $current_url
    ));
    
    if ($redirect) {
        // Update hit counter
        $wpdb->update($table_name, array('hits' => $redirect->hits + 1), array('id' => $redirect->id));
        
        $status = $redirect->redirect_type === '302' ? 302 : 301;
        wp_redirect($redirect->target_url, $status);
        exit;
    }
}

// =============================================================================
// 404 MONITOR
// =============================================================================

add_action('template_redirect', 'writgo_log_404', 99);
function writgo_log_404() {
    if (!is_404()) return;
    
    $url = $_SERVER['REQUEST_URI'];
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    
    $log = get_option('writgo_404_log', array());
    
    // Keep only last 100 entries
    if (count($log) > 100) {
        $log = array_slice($log, -100);
    }
    
    $log[] = array(
        'url' => $url,
        'referer' => $referer,
        'time' => current_time('mysql'),
        'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
    );
    
    update_option('writgo_404_log', $log);
}

// =============================================================================
// GOOGLE ANALYTICS & SEARCH CONSOLE
// =============================================================================

// Add settings to Customizer
add_action('customize_register', 'writgo_analytics_customizer');
function writgo_analytics_customizer($wp_customize) {
    // Analytics Section
    $wp_customize->add_section('writgo_analytics', array(
        'title'    => __('Analytics & Search Console', 'writgo-affiliate'),
        'priority' => 200,
    ));
    
    // GA4 Measurement ID
    $wp_customize->add_setting('writgo_ga4_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_ga4_id', array(
        'label'       => __('Google Analytics 4 ID', 'writgo-affiliate'),
        'description' => __('Bijv: G-XXXXXXXXXX', 'writgo-affiliate'),
        'section'     => 'writgo_analytics',
        'type'        => 'text',
    ));
    
    // Search Console Verification
    $wp_customize->add_setting('writgo_gsc_verification', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_gsc_verification', array(
        'label'       => __('Google Search Console Verificatie', 'writgo-affiliate'),
        'description' => __('Content van de meta tag (alleen de code)', 'writgo-affiliate'),
        'section'     => 'writgo_analytics',
        'type'        => 'text',
    ));
    
    // Bing Verification
    $wp_customize->add_setting('writgo_bing_verification', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_bing_verification', array(
        'label'       => __('Bing Webmaster Verificatie', 'writgo-affiliate'),
        'section'     => 'writgo_analytics',
        'type'        => 'text',
    ));
}

// Output Analytics & Verification codes
add_action('wp_head', 'writgo_output_analytics', 1);
function writgo_output_analytics() {
    // GA4
    $ga4_id = get_theme_mod('writgo_ga4_id', '');
    if ($ga4_id && !is_admin()) {
        ?>
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga4_id); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo esc_js($ga4_id); ?>');
</script>
        <?php
    }
    
    // Google Search Console
    $gsc = get_theme_mod('writgo_gsc_verification', '');
    if ($gsc) {
        echo '<meta name="google-site-verification" content="' . esc_attr($gsc) . '">' . "\n";
    }
    
    // Bing
    $bing = get_theme_mod('writgo_bing_verification', '');
    if ($bing) {
        echo '<meta name="msvalidate.01" content="' . esc_attr($bing) . '">' . "\n";
    }
}

// =============================================================================
// TECHNICAL SEO ADMIN PAGE
// =============================================================================

add_action('admin_menu', 'writgo_technical_seo_menu', 21);
function writgo_technical_seo_menu() {
    add_submenu_page(
        'writgo-dashboard',
        'Technische SEO',
        '🔧 Technische SEO',
        'edit_posts',
        'writgo-technical-seo',
        'writgo_technical_seo_page'
    );
}

function writgo_technical_seo_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'writgo_redirects';
    
    // Handle redirect actions
    if (isset($_POST['writgo_add_redirect']) && wp_verify_nonce($_POST['_wpnonce'], 'writgo_redirect')) {
        $source = sanitize_text_field($_POST['source_url']);
        $target = esc_url_raw($_POST['target_url']);
        $type = in_array($_POST['redirect_type'], array('301', '302')) ? $_POST['redirect_type'] : '301';
        
        if ($source && $target) {
            $wpdb->insert($table_name, array(
                'source_url' => $source,
                'target_url' => $target,
                'redirect_type' => $type,
            ));
            echo '<div class="notice notice-success"><p>Redirect toegevoegd!</p></div>';
        }
    }
    
    if (isset($_GET['delete_redirect']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_redirect')) {
        $wpdb->delete($table_name, array('id' => intval($_GET['delete_redirect'])));
        echo '<div class="notice notice-success"><p>Redirect verwijderd!</p></div>';
    }
    
    // Get redirects
    $redirects = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT 50");
    
    // Get 404 log
    $log_404 = get_option('writgo_404_log', array());
    $log_404 = array_reverse($log_404);
    ?>
    <div class="wrap">
        <h1>🔧 Technische SEO</h1>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-top: 20px;">
            
            <!-- Redirects -->
            <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">↪️ Redirects</h2>
                
                <form method="post" style="margin-bottom: 20px; padding: 15px; background: #f8fafc; border-radius: 8px;">
                    <?php wp_nonce_field('writgo_redirect'); ?>
                    <p>
                        <label><strong>Van URL:</strong></label><br>
                        <input type="text" name="source_url" placeholder="/oude-pagina" style="width: 100%; padding: 8px;" required>
                    </p>
                    <p>
                        <label><strong>Naar URL:</strong></label><br>
                        <input type="url" name="target_url" placeholder="https://example.com/nieuwe-pagina" style="width: 100%; padding: 8px;" required>
                    </p>
                    <p>
                        <label><strong>Type:</strong></label><br>
                        <select name="redirect_type" style="padding: 8px;">
                            <option value="301">301 (Permanent)</option>
                            <option value="302">302 (Tijdelijk)</option>
                        </select>
                    </p>
                    <button type="submit" name="writgo_add_redirect" class="button button-primary">Redirect Toevoegen</button>
                </form>
                
                <?php if ($redirects) : ?>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th>Van</th>
                            <th>Naar</th>
                            <th>Type</th>
                            <th>Hits</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($redirects as $r) : ?>
                        <tr>
                            <td><code><?php echo esc_html($r->source_url); ?></code></td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;"><?php echo esc_html($r->target_url); ?></td>
                            <td><?php echo esc_html($r->redirect_type); ?></td>
                            <td><?php echo intval($r->hits); ?></td>
                            <td>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=writgo-technical-seo&delete_redirect=' . $r->id), 'delete_redirect'); ?>" 
                                   onclick="return confirm('Redirect verwijderen?');" 
                                   style="color: #dc2626;">🗑️</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else : ?>
                <p style="color: #6b7280;">Nog geen redirects ingesteld.</p>
                <?php endif; ?>
            </div>
            
            <!-- 404 Log -->
            <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">🚫 404 Log <span style="font-size: 14px; color: #6b7280;">(laatste 100)</span></h2>
                
                <?php if ($log_404) : ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th>URL</th>
                                <th>Referer</th>
                                <th>Tijd</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($log_404, 0, 50) as $entry) : ?>
                            <tr>
                                <td><code style="font-size: 11px;"><?php echo esc_html($entry['url']); ?></code></td>
                                <td style="font-size: 11px; max-width: 150px; overflow: hidden; text-overflow: ellipsis;"><?php echo esc_html($entry['referer'] ?: '-'); ?></td>
                                <td style="font-size: 11px;"><?php echo esc_html($entry['time']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p style="margin-top: 10px;">
                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=writgo-technical-seo&clear_404=1'), 'clear_404'); ?>" 
                       class="button" onclick="return confirm('404 log wissen?');">Log Wissen</a>
                </p>
                <?php else : ?>
                <p style="color: #10b981;">✓ Geen 404 fouten gelogd!</p>
                <?php endif; ?>
            </div>
            
            <!-- Analytics Info -->
            <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">📊 Analytics & Verificatie</h2>
                
                <p>Stel je tracking codes in via de <a href="<?php echo admin_url('customize.php?autofocus[section]=writgo_analytics'); ?>">Customizer</a>.</p>
                
                <table class="widefat" style="margin-top: 15px;">
                    <tr>
                        <td><strong>Google Analytics 4</strong></td>
                        <td><?php echo get_theme_mod('writgo_ga4_id') ? '<span style="color: #10b981;">✓ Actief</span>' : '<span style="color: #9ca3af;">Niet ingesteld</span>'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Search Console</strong></td>
                        <td><?php echo get_theme_mod('writgo_gsc_verification') ? '<span style="color: #10b981;">✓ Actief</span>' : '<span style="color: #9ca3af;">Niet ingesteld</span>'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Bing Webmaster</strong></td>
                        <td><?php echo get_theme_mod('writgo_bing_verification') ? '<span style="color: #10b981;">✓ Actief</span>' : '<span style="color: #9ca3af;">Niet ingesteld</span>'; ?></td>
                    </tr>
                </table>
                
                <h3 style="margin-top: 25px;">🔗 Handige Links</h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 8px;"><a href="https://search.google.com/search-console" target="_blank">→ Google Search Console</a></li>
                    <li style="margin-bottom: 8px;"><a href="https://analytics.google.com/" target="_blank">→ Google Analytics</a></li>
                    <li style="margin-bottom: 8px;"><a href="https://www.bing.com/webmasters" target="_blank">→ Bing Webmaster Tools</a></li>
                    <li style="margin-bottom: 8px;"><a href="<?php echo home_url('/sitemap.xml'); ?>" target="_blank">→ Sitemap bekijken</a></li>
                </ul>
            </div>
            
            <!-- Robots.txt Info -->
            <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">🤖 Robots.txt</h2>
                <p>WordPress genereert automatisch een virtuele robots.txt.</p>
                <p><a href="<?php echo home_url('/robots.txt'); ?>" target="_blank" class="button">Bekijk robots.txt</a></p>
                
                <div style="margin-top: 15px; padding: 12px; background: #fef3c7; border-radius: 8px; font-size: 13px;">
                    <strong>💡 Tip:</strong> Wil je de robots.txt aanpassen? Maak dan een fysiek <code>robots.txt</code> bestand in je WordPress root folder.
                </div>
                
                <h4 style="margin-top: 20px;">Aanbevolen inhoud:</h4>
                <pre style="margin: 10px 0 0; padding: 10px; background: #1f2937; color: #f9fafb; border-radius: 6px; overflow-x: auto; font-size: 12px;">User-agent: *
Allow: /

Sitemap: <?php echo home_url('/sitemap.xml'); ?></pre>
            </div>
            
        </div>
    </div>
    <?php
}

// Handle clear 404 log
add_action('admin_init', 'writgo_handle_clear_404');
function writgo_handle_clear_404() {
    if (isset($_GET['clear_404']) && isset($_GET['_wpnonce'])) {
        if (wp_verify_nonce($_GET['_wpnonce'], 'clear_404') && current_user_can('manage_options')) {
            delete_option('writgo_404_log');
            wp_redirect(admin_url('admin.php?page=writgo-technical-seo&cleared=1'));
            exit;
        }
    }
}
