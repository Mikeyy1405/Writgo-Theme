<?php
/**
 * Writgo Theme Updater
 * 
 * Enables automatic theme updates - works out of the box!
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit;
}

class Writgo_Theme_Updater {
    
    private $theme_slug = 'writgo-theme';
    private $theme_version;
    private $cache_key = 'writgo_theme_update';
    private $cache_expiry = 12; // hours
    
    // =========================================================================
    // 🔧 CONFIGURATIE - GitHub Repository URL
    // =========================================================================
    private $update_url = 'https://raw.githubusercontent.com/Mikeyy1405/Writgo-Theme/main/update.json';
    // =========================================================================
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->theme_version = WRITGO_VERSION;
        
        // Hook into WordPress update system
        add_filter('pre_set_site_transient_update_themes', array($this, 'check_for_update'));
        add_filter('themes_api', array($this, 'theme_info'), 20, 3);
        add_action('upgrader_process_complete', array($this, 'clear_cache'), 10, 2);
        
        // Admin page for manual check
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    /**
     * Check for theme updates
     */
    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $remote = $this->get_remote_data();
        
        if ($remote && isset($remote->version)) {
            if (version_compare($this->theme_version, $remote->version, '<')) {
                $transient->response[$this->theme_slug] = array(
                    'theme'       => $this->theme_slug,
                    'new_version' => $remote->version,
                    'url'         => $remote->details_url ?? '',
                    'package'     => $remote->download_url ?? '',
                );
            }
        }
        
        return $transient;
    }
    
    /**
     * Get remote update data with caching
     */
    private function get_remote_data() {
        $cached = get_transient($this->cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $response = wp_remote_get($this->update_url, array(
            'timeout' => 15,
            'headers' => array('Accept' => 'application/json'),
        ));
        
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }
        
        $data = json_decode(wp_remote_retrieve_body($response));
        
        if ($data) {
            set_transient($this->cache_key, $data, $this->cache_expiry * HOUR_IN_SECONDS);
        }
        
        return $data;
    }
    
    /**
     * Theme info for the update details popup
     */
    public function theme_info($result, $action, $args) {
        if ($action !== 'theme_information' || !isset($args->slug) || $args->slug !== $this->theme_slug) {
            return $result;
        }
        
        $remote = $this->get_remote_data();
        
        if (!$remote) {
            return $result;
        }
        
        return (object) array(
            'name'           => $remote->name ?? 'Writgo Affiliate Theme',
            'slug'           => $this->theme_slug,
            'version'        => $remote->version ?? $this->theme_version,
            'author'         => $remote->author ?? 'Writgo Media',
            'homepage'       => $remote->homepage ?? 'https://writgo.nl',
            'requires'       => $remote->requires ?? '5.0',
            'tested'         => $remote->tested ?? '6.4',
            'requires_php'   => $remote->requires_php ?? '7.4',
            'last_updated'   => $remote->last_updated ?? '',
            'download_link'  => $remote->download_url ?? '',
            'sections'       => array(
                'description' => $remote->description ?? '',
                'changelog'   => $remote->changelog ?? '',
            ),
        );
    }
    
    /**
     * Clear cache after update
     */
    public function clear_cache($upgrader, $options) {
        if ($options['action'] === 'update' && $options['type'] === 'theme') {
            delete_transient($this->cache_key);
        }
    }
    
    /**
     * Add admin submenu
     */
    public function add_admin_menu() {
        add_theme_page(
            'Theme Updates',
            'Theme Updates',
            'manage_options',
            'writgo-updates',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        // Force refresh if requested
        if (isset($_GET['force-check']) && wp_verify_nonce($_GET['_wpnonce'] ?? '', 'writgo_force_check')) {
            delete_transient($this->cache_key);
            delete_site_transient('update_themes');
            wp_clean_themes_cache();
        }
        
        $remote = $this->get_remote_data();
        $has_update = $remote && version_compare($this->theme_version, $remote->version ?? '', '<');
        ?>
        <div class="wrap">
            <h1>🔄 Writgo Theme Updates</h1>
            
            <div class="card" style="max-width: 600px; padding: 20px; margin: 20px 0;">
                <h2 style="margin-top: 0;">Versie Status</h2>
                
                <table class="widefat" style="border: none;">
                    <tr>
                        <td><strong>Geïnstalleerd:</strong></td>
                        <td>v<?php echo esc_html($this->theme_version); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Laatste versie:</strong></td>
                        <td>
                            <?php if ($remote && isset($remote->version)) : ?>
                                v<?php echo esc_html($remote->version); ?>
                                <?php if ($has_update) : ?>
                                    <span style="color: #d63638; font-weight: bold;"> ⬆️ Update beschikbaar!</span>
                                <?php else : ?>
                                    <span style="color: #00a32a;"> ✅ Je hebt de nieuwste versie</span>
                                <?php endif; ?>
                            <?php else : ?>
                                <span style="color: #996800;">⚠️ Kon niet controleren</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <?php if ($has_update) : ?>
                        <a href="<?php echo esc_url(admin_url('update-core.php')); ?>" class="button button-primary button-large">
                            🚀 Nu Updaten naar v<?php echo esc_html($remote->version); ?>
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('themes.php?page=writgo-updates&force-check=1'), 'writgo_force_check')); ?>" class="button button-secondary">
                        🔍 Controleer op Updates
                    </a>
                </div>
            </div>
            
            <?php if ($remote && !empty($remote->changelog)) : ?>
                <div class="card" style="max-width: 600px; padding: 20px;">
                    <h2 style="margin-top: 0;">📋 Changelog</h2>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <?php echo wp_kses_post($remote->changelog); ?>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
        <?php
    }
}

// Initialize updater
new Writgo_Theme_Updater();
