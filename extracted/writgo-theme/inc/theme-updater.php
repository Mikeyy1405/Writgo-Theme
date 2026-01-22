<?php
/**
 * Writgo Theme Updater
 * 
 * Enables automatic theme updates from a remote server/GitHub
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit;
}

class Writgo_Theme_Updater {
    
    private $theme_slug;
    private $theme_version;
    private $update_url;
    private $cache_key;
    private $cache_expiry = 12; // hours
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->theme_slug = 'writgo-theme';
        $this->theme_version = WRITGO_VERSION;
        $this->cache_key = 'writgo_theme_update_check';
        
        // Update URL - change this to your hosting location
        // Options: GitHub raw URL, own server, or any URL that returns the update JSON
        $this->update_url = get_option('writgo_update_url', '');
        
        // Only run if update URL is configured
        if (!empty($this->update_url)) {
            add_filter('pre_set_site_transient_update_themes', array($this, 'check_for_update'));
            add_filter('themes_api', array($this, 'theme_info'), 20, 3);
            add_action('upgrader_process_complete', array($this, 'after_update'), 10, 2);
        }
        
        // Admin settings
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_notices', array($this, 'admin_notices'));
    }
    
    /**
     * Check for theme updates
     */
    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $remote_data = $this->get_remote_data();
        
        if ($remote_data && isset($remote_data->version)) {
            if (version_compare($this->theme_version, $remote_data->version, '<')) {
                $transient->response[$this->theme_slug] = array(
                    'theme'       => $this->theme_slug,
                    'new_version' => $remote_data->version,
                    'url'         => $remote_data->details_url ?? '',
                    'package'     => $remote_data->download_url ?? '',
                );
            }
        }
        
        return $transient;
    }
    
    /**
     * Get remote update data
     */
    private function get_remote_data() {
        // Check cache first
        $cached = get_transient($this->cache_key);
        if ($cached !== false) {
            return $cached;
        }
        
        // Fetch from remote
        $response = wp_remote_get($this->update_url, array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/json',
            ),
        ));
        
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);
        
        if (!$data) {
            return false;
        }
        
        // Cache for X hours
        set_transient($this->cache_key, $data, $this->cache_expiry * HOUR_IN_SECONDS);
        
        return $data;
    }
    
    /**
     * Theme info popup
     */
    public function theme_info($result, $action, $args) {
        if ($action !== 'theme_information') {
            return $result;
        }
        
        if (!isset($args->slug) || $args->slug !== $this->theme_slug) {
            return $result;
        }
        
        $remote_data = $this->get_remote_data();
        
        if (!$remote_data) {
            return $result;
        }
        
        $result = new stdClass();
        $result->name = $remote_data->name ?? 'Writgo Affiliate Theme';
        $result->slug = $this->theme_slug;
        $result->version = $remote_data->version ?? $this->theme_version;
        $result->author = $remote_data->author ?? 'Writgo Media';
        $result->homepage = $remote_data->homepage ?? '';
        $result->requires = $remote_data->requires ?? '5.0';
        $result->tested = $remote_data->tested ?? '6.4';
        $result->requires_php = $remote_data->requires_php ?? '7.4';
        $result->downloaded = $remote_data->downloaded ?? 0;
        $result->last_updated = $remote_data->last_updated ?? date('Y-m-d');
        $result->sections = array(
            'description' => $remote_data->description ?? 'Professioneel WordPress affiliate thema.',
            'changelog'   => $remote_data->changelog ?? 'Zie de changelog op de website.',
        );
        $result->download_link = $remote_data->download_url ?? '';
        
        return $result;
    }
    
    /**
     * Clear cache after update
     */
    public function after_update($upgrader, $options) {
        if ($options['action'] === 'update' && $options['type'] === 'theme') {
            if (isset($options['themes']) && in_array($this->theme_slug, $options['themes'])) {
                delete_transient($this->cache_key);
            }
        }
    }
    
    /**
     * Add admin menu
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
     * Register settings
     */
    public function register_settings() {
        register_setting('writgo_updater', 'writgo_update_url', 'esc_url_raw');
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        $update_url = get_option('writgo_update_url', '');
        $remote_data = !empty($update_url) ? $this->get_remote_data() : null;
        $update_available = $remote_data && version_compare($this->theme_version, $remote_data->version, '<');
        ?>
        <div class="wrap">
            <h1>🔄 Writgo Theme Updates</h1>
            
            <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
                <h2>Huidige Versie</h2>
                <p style="font-size: 16px;">
                    <strong>Geïnstalleerd:</strong> v<?php echo esc_html($this->theme_version); ?>
                    <?php if ($remote_data && isset($remote_data->version)) : ?>
                        <br><strong>Beschikbaar:</strong> v<?php echo esc_html($remote_data->version); ?>
                        <?php if ($update_available) : ?>
                            <span style="color: #d63638; font-weight: bold;"> ← Update beschikbaar!</span>
                        <?php else : ?>
                            <span style="color: #00a32a;"> ✓ Je hebt de nieuwste versie</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
                
                <?php if ($update_available) : ?>
                    <p>
                        <a href="<?php echo esc_url(admin_url('update-core.php')); ?>" class="button button-primary button-hero">
                            🚀 Update nu naar v<?php echo esc_html($remote_data->version); ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
            
            <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
                <h2>Update URL Configuratie</h2>
                <p>Vul hier de URL in naar je update JSON bestand. Dit kan zijn:</p>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li>GitHub raw URL naar <code>update.json</code></li>
                    <li>URL op je eigen server</li>
                    <li>Dropbox/Google Drive directe link</li>
                </ul>
                
                <form method="post" action="options.php">
                    <?php settings_fields('writgo_updater'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="writgo_update_url">Update JSON URL</label>
                            </th>
                            <td>
                                <input type="url" 
                                       id="writgo_update_url" 
                                       name="writgo_update_url" 
                                       value="<?php echo esc_attr($update_url); ?>" 
                                       class="regular-text"
                                       style="width: 100%;"
                                       placeholder="https://raw.githubusercontent.com/username/repo/main/update.json">
                                <p class="description">
                                    Voorbeeld: <code>https://raw.githubusercontent.com/jouwusername/writgo-theme/main/update.json</code>
                                </p>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('URL Opslaan'); ?>
                </form>
                
                <?php if (!empty($update_url)) : ?>
                    <p>
                        <a href="<?php echo esc_url(admin_url('themes.php?page=writgo-updates&check=1')); ?>" class="button">
                            🔍 Controleer nu op updates
                        </a>
                        <?php if (isset($_GET['check'])) : 
                            delete_transient($this->cache_key);
                        ?>
                            <span style="color: #00a32a; margin-left: 10px;">✓ Cache geleegd, pagina opnieuw laden...</span>
                            <script>setTimeout(function(){ window.location.href = '<?php echo esc_url(admin_url('themes.php?page=writgo-updates')); ?>'; }, 1000);</script>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
                <h2>📄 Update JSON Formaat</h2>
                <p>Maak een <code>update.json</code> bestand met deze structuur:</p>
                <pre style="background: #f0f0f1; padding: 15px; border-radius: 4px; overflow-x: auto;">
{
    "name": "Writgo Affiliate Theme",
    "version": "<?php echo esc_html($this->theme_version); ?>",
    "author": "Writgo Media",
    "homepage": "https://writgo.nl",
    "requires": "5.0",
    "tested": "6.4",
    "requires_php": "7.4",
    "download_url": "https://jouw-server.nl/themes/writgo-theme.zip",
    "details_url": "https://writgo.nl/changelog/",
    "description": "Professioneel WordPress affiliate thema.",
    "changelog": "&lt;h4&gt;v7.5.4&lt;/h4&gt;&lt;ul&gt;&lt;li&gt;Contact pagina toegevoegd&lt;/li&gt;&lt;/ul&gt;",
    "last_updated": "<?php echo date('Y-m-d'); ?>"
}</pre>
                <p><strong>Let op:</strong> De <code>download_url</code> moet een directe link zijn naar de ZIP file.</p>
            </div>
            
            <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
                <h2>🚀 Snelle Setup met GitHub</h2>
                <ol style="line-height: 2;">
                    <li>Maak een GitHub repository (public of private met token)</li>
                    <li>Upload <code>update.json</code> naar de root</li>
                    <li>Maak een Release aan met de theme ZIP als asset</li>
                    <li>Kopieer de raw URL van update.json hierboven</li>
                </ol>
                <p>
                    <strong>Voorbeeld GitHub Raw URL:</strong><br>
                    <code>https://raw.githubusercontent.com/jouwusername/writgo-theme/main/update.json</code>
                </p>
            </div>
            
        </div>
        <?php
    }
    
    /**
     * Admin notices
     */
    public function admin_notices() {
        // Only on themes page
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'themes') {
            return;
        }
        
        $update_url = get_option('writgo_update_url', '');
        
        if (empty($update_url)) {
            ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <strong>Writgo Theme:</strong> Wil je automatische updates? 
                    <a href="<?php echo esc_url(admin_url('themes.php?page=writgo-updates')); ?>">Configureer de Update URL</a>
                </p>
            </div>
            <?php
            return;
        }
        
        $remote_data = $this->get_remote_data();
        
        if ($remote_data && isset($remote_data->version)) {
            if (version_compare($this->theme_version, $remote_data->version, '<')) {
                ?>
                <div class="notice notice-warning">
                    <p>
                        <strong>Writgo Theme Update!</strong> 
                        Versie <?php echo esc_html($remote_data->version); ?> is beschikbaar. 
                        Je hebt nu v<?php echo esc_html($this->theme_version); ?>.
                        <a href="<?php echo esc_url(admin_url('update-core.php')); ?>">Update nu</a>
                    </p>
                </div>
                <?php
            }
        }
    }
}

// Initialize
new Writgo_Theme_Updater();
