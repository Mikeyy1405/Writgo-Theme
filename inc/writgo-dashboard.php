<?php
/**
 * Writgo Dashboard
 * 
 * Admin dashboard page with overview of all Writgo features
 *
 * @package Writgo_Affiliate
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get dashboard translation
 */
function writgo_dash_t($key) {
    $lang = writgo_get_language();
    
    $translations = array(
        'dashboard' => array(
            'nl' => 'Dashboard',
            'en' => 'Dashboard',
            'de' => 'Dashboard',
            'fr' => 'Tableau de bord',
        ),
        'seo_overview' => array(
            'nl' => 'SEO Overzicht',
            'en' => 'SEO Overview',
            'de' => 'SEO-Übersicht',
            'fr' => 'Aperçu SEO',
        ),
        'affiliate_links' => array(
            'nl' => 'Affiliate Links',
            'en' => 'Affiliate Links',
            'de' => 'Affiliate-Links',
            'fr' => 'Liens d\'affiliation',
        ),
        'shortcodes' => array(
            'nl' => 'Shortcodes',
            'en' => 'Shortcodes',
            'de' => 'Shortcodes',
            'fr' => 'Shortcodes',
        ),
        'help_docs' => array(
            'nl' => 'Help & Documentatie',
            'en' => 'Help & Documentation',
            'de' => 'Hilfe & Dokumentation',
            'fr' => 'Aide & Documentation',
        ),
        'help' => array(
            'nl' => 'Help',
            'en' => 'Help',
            'de' => 'Hilfe',
            'fr' => 'Aide',
        ),
        'quick_stats' => array(
            'nl' => 'Snelle Statistieken',
            'en' => 'Quick Stats',
            'de' => 'Schnelle Statistiken',
            'fr' => 'Statistiques rapides',
        ),
        'articles' => array(
            'nl' => 'Artikelen',
            'en' => 'Articles',
            'de' => 'Artikel',
            'fr' => 'Articles',
        ),
        'pages' => array(
            'nl' => 'Pagina\'s',
            'en' => 'Pages',
            'de' => 'Seiten',
            'fr' => 'Pages',
        ),
        'seo_optimized' => array(
            'nl' => 'SEO Geoptimaliseerd',
            'en' => 'SEO Optimized',
            'de' => 'SEO-optimiert',
            'fr' => 'Optimisé SEO',
        ),
        'featured' => array(
            'nl' => 'Uitgelicht',
            'en' => 'Featured',
            'de' => 'Hervorgehoben',
            'fr' => 'À la une',
        ),
        'seo_coverage' => array(
            'nl' => 'SEO Dekking',
            'en' => 'SEO Coverage',
            'de' => 'SEO-Abdeckung',
            'fr' => 'Couverture SEO',
        ),
        'keyword_coverage' => array(
            'nl' => 'Keyword Dekking',
            'en' => 'Keyword Coverage',
            'de' => 'Keyword-Abdeckung',
            'fr' => 'Couverture des mots-clés',
        ),
        'quick_actions' => array(
            'nl' => 'Snelle Acties',
            'en' => 'Quick Actions',
            'de' => 'Schnellaktionen',
            'fr' => 'Actions rapides',
        ),
        'new_article' => array(
            'nl' => '✏️ Nieuw Artikel',
            'en' => '✏️ New Article',
            'de' => '✏️ Neuer Artikel',
            'fr' => '✏️ Nouvel Article',
        ),
        'customize_theme' => array(
            'nl' => '🎨 Theme Aanpassen',
            'en' => '🎨 Customize Theme',
            'de' => '🎨 Theme anpassen',
            'fr' => '🎨 Personnaliser le thème',
        ),
        'view_site' => array(
            'nl' => '🌐 Bekijk Site',
            'en' => '🌐 View Site',
            'de' => '🌐 Website ansehen',
            'fr' => '🌐 Voir le site',
        ),
        'recent_articles' => array(
            'nl' => 'Recente Artikelen',
            'en' => 'Recent Articles',
            'de' => 'Aktuelle Artikel',
            'fr' => 'Articles récents',
        ),
        'no_articles' => array(
            'nl' => 'Nog geen artikelen gepubliceerd.',
            'en' => 'No articles published yet.',
            'de' => 'Noch keine Artikel veröffentlicht.',
            'fr' => 'Aucun article publié.',
        ),
        'view_all' => array(
            'nl' => 'Bekijk alle →',
            'en' => 'View all →',
            'de' => 'Alle anzeigen →',
            'fr' => 'Voir tout →',
        ),
    );
    
    if (isset($translations[$key][$lang])) {
        return $translations[$key][$lang];
    } elseif (isset($translations[$key]['en'])) {
        return $translations[$key]['en'];
    }
    
    return $key;
}

// =============================================================================
// ADMIN MENU
// =============================================================================

/**
 * Register Writgo Admin Menu
 */
add_action('admin_menu', 'writgo_register_admin_menu', 5);
function writgo_register_admin_menu() {
    // Main menu
    add_menu_page(
        'Writgo',                           // Page title
        'Writgo',                           // Menu title
        'edit_posts',                       // Capability
        'writgo-dashboard',                 // Menu slug
        'writgo_dashboard_page',            // Callback
        'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>'),
        3                                    // Position (after Dashboard)
    );
    
    // Dashboard submenu
    add_submenu_page(
        'writgo-dashboard',
        writgo_dash_t('dashboard'),
        writgo_dash_t('dashboard'),
        'edit_posts',
        'writgo-dashboard',
        'writgo_dashboard_page'
    );
    
    // SEO Overview submenu
    add_submenu_page(
        'writgo-dashboard',
        writgo_dash_t('seo_overview'),
        '🎯 ' . writgo_dash_t('seo_overview'),
        'edit_posts',
        'writgo-seo-overview',
        'writgo_seo_overview_page'
    );
    
    // Affiliate submenu
    add_submenu_page(
        'writgo-dashboard',
        writgo_dash_t('affiliate_links'),
        '🔗 ' . writgo_dash_t('affiliate_links'),
        'edit_posts',
        'writgo-affiliates',
        'writgo_affiliates_page'
    );
    
    // Shortcodes submenu
    add_submenu_page(
        'writgo-dashboard',
        writgo_dash_t('shortcodes'),
        '📦 ' . writgo_dash_t('shortcodes'),
        'edit_posts',
        'writgo-shortcodes',
        'writgo_shortcodes_page'
    );
    
    // Help submenu
    add_submenu_page(
        'writgo-dashboard',
        writgo_dash_t('help_docs'),
        '❓ ' . writgo_dash_t('help'),
        'edit_posts',
        'writgo-help',
        'writgo_help_page'
    );
}

/**
 * Dashboard Page Callback
 */
function writgo_dashboard_page() {
    // Get stats
    $total_posts = wp_count_posts('post')->publish;
    $total_pages = wp_count_posts('page')->publish;
    $posts_with_seo = writgo_count_posts_with_meta('_writgo_seo_title');
    $posts_with_keyword = writgo_count_posts_with_meta('_writgo_focus_keyword');
    $posts_featured = writgo_count_posts_with_meta('_writgo_featured', '1');
    
    // Calculate SEO coverage
    $seo_coverage = $total_posts > 0 ? round(($posts_with_seo / $total_posts) * 100) : 0;
    $keyword_coverage = $total_posts > 0 ? round(($posts_with_keyword / $total_posts) * 100) : 0;
    
    // Recent posts without SEO
    $posts_without_seo = get_posts(array(
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => '_writgo_seo_title',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'   => '_writgo_seo_title',
                'value' => '',
            ),
        ),
    ));
    ?>
    
    <style>
        .writgo-dashboard { max-width: 1400px; margin: 20px auto; padding: 0 20px; }
        .writgo-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; padding: 25px 30px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 16px; color: white; }
        .writgo-header h1 { margin: 0; font-size: 28px; font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .writgo-header .version { background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 13px; }
        .writgo-header-actions { display: flex; gap: 10px; }
        .writgo-header-actions a { background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s; }
        .writgo-header-actions a:hover { background: rgba(255,255,255,0.3); }
        
        .writgo-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .writgo-stat-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .writgo-stat-card .icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; }
        .writgo-stat-card .icon.blue { background: #dbeafe; }
        .writgo-stat-card .icon.green { background: #dcfce7; }
        .writgo-stat-card .icon.orange { background: #fed7aa; }
        .writgo-stat-card .icon.purple { background: #e9d5ff; }
        .writgo-stat-card .number { font-size: 32px; font-weight: 700; color: #1f2937; margin-bottom: 5px; }
        .writgo-stat-card .label { font-size: 14px; color: #6b7280; }
        .writgo-stat-card .progress { margin-top: 10px; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; }
        .writgo-stat-card .progress-bar { height: 100%; border-radius: 3px; transition: width 0.5s ease; }
        .writgo-stat-card .progress-bar.green { background: #22c55e; }
        .writgo-stat-card .progress-bar.orange { background: #f97316; }
        .writgo-stat-card .progress-bar.red { background: #ef4444; }
        
        .writgo-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        @media (max-width: 1024px) { .writgo-grid { grid-template-columns: 1fr; } }
        
        .writgo-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .writgo-card h2 { margin: 0 0 20px; font-size: 18px; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 10px; }
        
        .writgo-quick-actions { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .writgo-quick-action { display: flex; align-items: center; gap: 12px; padding: 16px; background: #f9fafb; border-radius: 10px; text-decoration: none; color: #374151; transition: all 0.2s; border: 1px solid transparent; }
        .writgo-quick-action:hover { background: #fff7ed; border-color: #f97316; color: #ea580c; }
        .writgo-quick-action .icon { width: 40px; height: 40px; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .writgo-quick-action span { font-weight: 500; }
        
        .writgo-posts-list { list-style: none; padding: 0; margin: 0; }
        .writgo-posts-list li { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
        .writgo-posts-list li:last-child { border-bottom: none; }
        .writgo-posts-list .title { color: #374151; text-decoration: none; font-weight: 500; }
        .writgo-posts-list .title:hover { color: #f97316; }
        .writgo-posts-list .badge { background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; }
        
        .writgo-features { display: grid; gap: 12px; }
        .writgo-feature { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: #f9fafb; border-radius: 8px; }
        .writgo-feature .check { width: 24px; height: 24px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .writgo-feature span { font-size: 14px; color: #374151; }
        
        .writgo-pro-banner { margin-top: 20px; padding: 20px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 12px; color: white; text-align: center; }
        .writgo-pro-banner h3 { margin: 0 0 8px; font-size: 16px; }
        .writgo-pro-banner p { margin: 0; font-size: 13px; opacity: 0.9; }
    </style>
    
    <div class="writgo-dashboard">
        <!-- Header -->
        <div class="writgo-header">
            <h1>
                <span>🚀</span> Writgo Dashboard
                <span class="version">v<?php echo WRITGO_VERSION; ?></span>
            </h1>
            <div class="writgo-header-actions">
                <a href="<?php echo admin_url('customize.php'); ?>">⚙️ Instellingen</a>
                <a href="<?php echo admin_url('admin.php?page=writgo-help'); ?>">❓ Help</a>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="writgo-stats">
            <div class="writgo-stat-card">
                <div class="icon blue">📝</div>
                <div class="number"><?php echo $total_posts; ?></div>
                <div class="label">Gepubliceerde artikelen</div>
            </div>
            
            <div class="writgo-stat-card">
                <div class="icon green">🎯</div>
                <div class="number"><?php echo $seo_coverage; ?>%</div>
                <div class="label">SEO Dekking</div>
                <div class="progress">
                    <div class="progress-bar <?php echo $seo_coverage >= 70 ? 'green' : ($seo_coverage >= 40 ? 'orange' : 'red'); ?>" style="width: <?php echo $seo_coverage; ?>%"></div>
                </div>
            </div>
            
            <div class="writgo-stat-card">
                <div class="icon orange">🔑</div>
                <div class="number"><?php echo $keyword_coverage; ?>%</div>
                <div class="label">Focus Keywords</div>
                <div class="progress">
                    <div class="progress-bar <?php echo $keyword_coverage >= 70 ? 'green' : ($keyword_coverage >= 40 ? 'orange' : 'red'); ?>" style="width: <?php echo $keyword_coverage; ?>%"></div>
                </div>
            </div>
            
            <div class="writgo-stat-card">
                <div class="icon purple">⭐</div>
                <div class="number"><?php echo $posts_featured; ?></div>
                <div class="label">Uitgelichte artikelen</div>
            </div>
        </div>
        
        <!-- Main Grid -->
        <div class="writgo-grid">
            <div>
                <!-- Quick Actions -->
                <div class="writgo-card" style="margin-bottom: 20px;">
                    <h2>⚡ Snelle Acties</h2>
                    <div class="writgo-quick-actions">
                        <a href="<?php echo admin_url('post-new.php'); ?>" class="writgo-quick-action">
                            <div class="icon">✏️</div>
                            <span>Nieuw Artikel</span>
                        </a>
                        <a href="<?php echo admin_url('edit.php'); ?>" class="writgo-quick-action">
                            <div class="icon">📋</div>
                            <span>Alle Artikelen</span>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=writgo-seo-overview'); ?>" class="writgo-quick-action">
                            <div class="icon">🎯</div>
                            <span>SEO Overzicht</span>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=writgo-shortcodes'); ?>" class="writgo-quick-action">
                            <div class="icon">📦</div>
                            <span>Shortcodes</span>
                        </a>
                        <a href="<?php echo admin_url('customize.php'); ?>" class="writgo-quick-action">
                            <div class="icon">🎨</div>
                            <span>Theme Customizer</span>
                        </a>
                        <a href="<?php echo admin_url('edit.php?post_type=page'); ?>" class="writgo-quick-action">
                            <div class="icon">📄</div>
                            <span>Pagina's</span>
                        </a>
                    </div>
                </div>
                
                <!-- Posts Without SEO -->
                <div class="writgo-card">
                    <h2>⚠️ SEO Aandacht Nodig</h2>
                    <?php if (!empty($posts_without_seo)) : ?>
                        <ul class="writgo-posts-list">
                            <?php foreach ($posts_without_seo as $p) : ?>
                                <li>
                                    <a href="<?php echo get_edit_post_link($p->ID); ?>" class="title">
                                        <?php echo esc_html($p->post_title); ?>
                                    </a>
                                    <span class="badge">Geen SEO</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (count($posts_without_seo) >= 5) : ?>
                            <p style="margin: 15px 0 0; text-align: center;">
                                <a href="<?php echo admin_url('admin.php?page=writgo-seo-overview'); ?>" style="color: #f97316;">
                                    Bekijk alle artikelen →
                                </a>
                            </p>
                        <?php endif; ?>
                    <?php else : ?>
                        <p style="color: #22c55e; text-align: center; padding: 20px;">
                            ✅ Alle artikelen hebben SEO instellingen!
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div>
                <!-- Active Features -->
                <div class="writgo-card">
                    <h2>✅ Actieve Features</h2>
                    <div class="writgo-features">
                        <div class="writgo-feature">
                            <span class="check">✓</span>
                            <span>SEO Meta Box met Score</span>
                        </div>
                        <div class="writgo-feature">
                            <span class="check">✓</span>
                            <span>Focus Keyword Analyse</span>
                        </div>
                        <div class="writgo-feature">
                            <span class="check">✓</span>
                            <span>Schema.org Markup</span>
                        </div>
                        <div class="writgo-feature">
                            <span class="check">✓</span>
                            <span>Open Graph & Twitter Cards</span>
                        </div>
                        <div class="writgo-feature">
                            <span class="check">✓</span>
                            <span>Affiliate Shortcodes</span>
                        </div>
                        <div class="writgo-feature">
                            <span class="check">✓</span>
                            <span>Sticky CTA Bar</span>
                        </div>
                        <div class="writgo-feature">
                            <span class="check">✓</span>
                            <span>Product Vergelijk Tabellen</span>
                        </div>
                        <div class="writgo-feature">
                            <span class="check">✓</span>
                            <span>Automatische Legal Pages</span>
                        </div>
                    </div>
                    
                    <div class="writgo-pro-banner">
                        <h3>💡 Writgo Media</h3>
                        <p>AI-powered content automation platform</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php
}

/**
 * SEO Overview Page Callback
 */
function writgo_seo_overview_page() {
    // Get all posts with SEO data
    $posts = get_posts(array(
        'post_type'      => array('post', 'page'),
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ));
    ?>
    
    <style>
        .writgo-seo-page { max-width: 1400px; margin: 20px auto; padding: 0 20px; }
        .writgo-seo-page h1 { display: flex; align-items: center; gap: 10px; }
        
        .writgo-seo-table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .writgo-seo-table th, .writgo-seo-table td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #f3f4f6; }
        .writgo-seo-table th { background: #f9fafb; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .writgo-seo-table tr:hover { background: #fefce8; }
        .writgo-seo-table .title { font-weight: 500; color: #1f2937; text-decoration: none; }
        .writgo-seo-table .title:hover { color: #f97316; }
        
        .seo-status { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; }
        .seo-status.good { background: #dcfce7; color: #16a34a; }
        .seo-status.warning { background: #fef3c7; color: #92400e; }
        .seo-status.bad { background: #fee2e2; color: #dc2626; }
        
        .seo-score-badge { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; font-weight: 700; font-size: 13px; color: white; }
        .seo-score-badge.excellent { background: #22c55e; }
        .seo-score-badge.good { background: #84cc16; }
        .seo-score-badge.average { background: #f59e0b; }
        .seo-score-badge.poor { background: #ef4444; }
        
        .writgo-filter-bar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .writgo-filter-bar select, .writgo-filter-bar input { padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; }
        
        .seo-data-cell { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 13px; color: #6b7280; }
        .seo-data-cell.has-data { color: #059669; }
        .seo-data-cell.no-data { color: #9ca3af; font-style: italic; }
    </style>
    
    <div class="writgo-seo-page">
        <h1>🎯 SEO Overzicht</h1>
        
        <div class="writgo-filter-bar">
            <select id="filterType">
                <option value="">Alle types</option>
                <option value="post">Artikelen</option>
                <option value="page">Pagina's</option>
            </select>
            <select id="filterStatus">
                <option value="">Alle statussen</option>
                <option value="good">Goed (70+)</option>
                <option value="warning">Aandacht nodig (40-69)</option>
                <option value="bad">Actie vereist (0-39)</option>
            </select>
            <input type="text" id="filterSearch" placeholder="Zoeken op titel...">
        </div>
        
        <table class="writgo-seo-table">
            <thead>
                <tr>
                    <th style="width: 40px;">Score</th>
                    <th>Titel</th>
                    <th>Focus Keyword</th>
                    <th>SEO Titel</th>
                    <th>Meta Description</th>
                    <th>Auteur</th>
                    <th>Datum</th>
                    <th>Actie</th>
                </tr>
            </thead>
            <tbody id="seoTableBody">
                <?php foreach ($posts as $p) : 
                    $keyword = get_post_meta($p->ID, '_writgo_focus_keyword', true);
                    $seo_title = get_post_meta($p->ID, '_writgo_seo_title', true);
                    $seo_desc = get_post_meta($p->ID, '_writgo_seo_description', true);
                    
                    // Calculate real SEO score using the same function as post editor
                    $score = function_exists('writgo_calculate_seo_score') ? writgo_calculate_seo_score($p->ID) : 0;
                    
                    // Determine score class
                    if ($score >= 70) {
                        $score_class = 'excellent';
                        $status = 'good';
                    } elseif ($score >= 50) {
                        $score_class = 'good';
                        $status = 'good';
                    } elseif ($score >= 40) {
                        $score_class = 'average';
                        $status = 'warning';
                    } else {
                        $score_class = 'poor';
                        $status = 'bad';
                    }
                    
                    $author = get_the_author_meta('display_name', $p->post_author);
                ?>
                <tr data-type="<?php echo $p->post_type; ?>" data-score="<?php echo $score; ?>" data-status="<?php echo $status; ?>">
                    <td>
                        <span class="seo-score-badge <?php echo $score_class; ?>"><?php echo $score; ?></span>
                    </td>
                    <td>
                        <a href="<?php echo get_edit_post_link($p->ID); ?>" class="title">
                            <?php echo esc_html($p->post_title); ?>
                        </a>
                        <br><small style="color: #9ca3af;"><?php echo ucfirst($p->post_type); ?></small>
                    </td>
                    <td class="seo-data-cell <?php echo $keyword ? 'has-data' : 'no-data'; ?>">
                        <?php echo $keyword ? esc_html($keyword) : '—'; ?>
                    </td>
                    <td class="seo-data-cell <?php echo $seo_title ? 'has-data' : 'no-data'; ?>" title="<?php echo esc_attr($seo_title); ?>">
                        <?php echo $seo_title ? esc_html(substr($seo_title, 0, 40)) . (strlen($seo_title) > 40 ? '...' : '') : '—'; ?>
                    </td>
                    <td class="seo-data-cell <?php echo $seo_desc ? 'has-data' : 'no-data'; ?>" title="<?php echo esc_attr($seo_desc); ?>">
                        <?php echo $seo_desc ? esc_html(substr($seo_desc, 0, 50)) . (strlen($seo_desc) > 50 ? '...' : '') : '—'; ?>
                    </td>
                    <td style="font-size: 13px; color: #6b7280;">
                        <?php echo esc_html($author); ?>
                    </td>
                    <td style="font-size: 12px; color: #9ca3af;">
                        <?php echo get_the_date('d M Y', $p->ID); ?>
                    </td>
                    <td>
                        <a href="<?php echo get_edit_post_link($p->ID); ?>" class="button button-small">Bewerken</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <script>
    (function() {
        var filterType = document.getElementById('filterType');
        var filterStatus = document.getElementById('filterStatus');
        var filterSearch = document.getElementById('filterSearch');
        var rows = document.querySelectorAll('#seoTableBody tr');
        
        function filterTable() {
            var type = filterType.value;
            var status = filterStatus.value;
            var search = filterSearch.value.toLowerCase();
            
            rows.forEach(function(row) {
                var rowType = row.dataset.type;
                var rowStatus = row.dataset.status;
                var title = row.querySelector('.title').textContent.toLowerCase();
                
                var showType = !type || rowType === type;
                var showStatus = !status || rowStatus === status;
                var showSearch = !search || title.includes(search);
                
                row.style.display = showType && showStatus && showSearch ? '' : 'none';
            });
        }
        
        filterType.addEventListener('change', filterTable);
        filterStatus.addEventListener('change', filterTable);
        filterSearch.addEventListener('input', filterTable);
    })();
    </script>
    
    <?php
}

/**
 * Affiliates Page Callback
 */
function writgo_affiliates_page() {
    ?>
    <div class="wrap">
        <h1>🔗 Affiliate Links Beheer</h1>
        <p>Binnenkort: Centraal beheer van al je affiliate links met automatische link rotatie en tracking.</p>
        
        <div style="background: white; padding: 30px; border-radius: 12px; margin-top: 20px; text-align: center;">
            <h2>🚧 Coming Soon</h2>
            <p style="color: #6b7280;">Deze functie wordt nog ontwikkeld. Gebruik voorlopig de shortcodes in je artikelen.</p>
            <a href="<?php echo admin_url('admin.php?page=writgo-shortcodes'); ?>" class="button button-primary">
                Bekijk Shortcodes
            </a>
        </div>
    </div>
    <?php
}

/**
 * Shortcodes Page Callback
 */
function writgo_shortcodes_page() {
    ?>
    <style>
        .writgo-shortcodes { max-width: 1000px; margin: 20px auto; padding: 0 20px; }
        .shortcode-card { background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .shortcode-card h3 { margin: 0 0 10px; color: #1f2937; display: flex; align-items: center; gap: 10px; }
        .shortcode-card p { color: #6b7280; margin: 0 0 15px; }
        .shortcode-code { background: #1f2937; color: #f97316; padding: 15px 20px; border-radius: 8px; font-family: monospace; font-size: 14px; overflow-x: auto; }
        .shortcode-example { background: #f9fafb; padding: 15px 20px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #f97316; }
        .shortcode-example h4 { margin: 0 0 10px; font-size: 14px; color: #374151; }
    </style>
    
    <div class="writgo-shortcodes">
        <h1 style="display: flex; align-items: center; gap: 10px;">📦 Writgo Shortcodes</h1>
        <p style="color: #6b7280; margin-bottom: 30px;">Gebruik deze shortcodes in je artikelen voor professionele affiliate content.</p>
        
        <!-- Product Box -->
        <div class="shortcode-card">
            <h3>🛍️ Product Box</h3>
            <p>Toon een opvallende product box met afbeelding, prijs en CTA button.</p>
            <div class="shortcode-code">
                [writgo_product title="Product Naam" price="€99" rating="4.5" image="URL" url="affiliate-url" pros="Voordeel 1, Voordeel 2" cons="Nadeel 1"]
            </div>
            <div class="shortcode-example">
                <h4>Parameters:</h4>
                <ul style="margin: 0; padding-left: 20px; color: #4b5563;">
                    <li><code>title</code> - Product naam (verplicht)</li>
                    <li><code>price</code> - Prijs met valuta</li>
                    <li><code>rating</code> - Beoordeling (0-5)</li>
                    <li><code>image</code> - Product afbeelding URL</li>
                    <li><code>url</code> - Affiliate link (verplicht)</li>
                    <li><code>pros</code> - Voordelen (komma-gescheiden)</li>
                    <li><code>cons</code> - Nadelen (komma-gescheiden)</li>
                    <li><code>badge</code> - Badge tekst (bijv. "Beste koop")</li>
                </ul>
            </div>
        </div>
        
        <!-- Comparison Table -->
        <div class="shortcode-card">
            <h3>📊 Vergelijkingstabel</h3>
            <p>Maak een professionele vergelijkingstabel voor meerdere producten.</p>
            <div class="shortcode-code">
                [writgo_comparison]<br>
                Product 1 | €99 | 4.5 | affiliate-url-1<br>
                Product 2 | €149 | 4.8 | affiliate-url-2<br>
                [/writgo_comparison]
            </div>
        </div>
        
        <!-- CTA Button -->
        <div class="shortcode-card">
            <h3>🔘 CTA Button</h3>
            <p>Opvallende call-to-action button.</p>
            <div class="shortcode-code">
                [writgo_button url="affiliate-url" text="Bekijk aanbieding" color="orange"]
            </div>
            <div class="shortcode-example">
                <h4>Kleuren:</h4>
                <p style="margin: 0; color: #4b5563;">orange (standaard), blue, green, red, purple</p>
            </div>
        </div>
        
        <!-- Pros Cons -->
        <div class="shortcode-card">
            <h3>✅ Voor- en Nadelen</h3>
            <p>Overzichtelijke voor- en nadelen lijst.</p>
            <div class="shortcode-code">
                [writgo_proscons pros="Voordeel 1, Voordeel 2, Voordeel 3" cons="Nadeel 1, Nadeel 2"]
            </div>
        </div>
        
        <!-- Alert Box -->
        <div class="shortcode-card">
            <h3>💡 Alert Box</h3>
            <p>Opvallende informatieboxen.</p>
            <div class="shortcode-code">
                [writgo_alert type="info" title="Tip"]Je bericht hier[/writgo_alert]
            </div>
            <div class="shortcode-example">
                <h4>Types:</h4>
                <p style="margin: 0; color: #4b5563;">info (blauw), success (groen), warning (geel), danger (rood)</p>
            </div>
        </div>
        
        <!-- Disclosure -->
        <div class="shortcode-card">
            <h3>📋 Affiliate Disclosure</h3>
            <p>Verplichte affiliate melding.</p>
            <div class="shortcode-code">
                [writgo_disclosure]
            </div>
        </div>
    </div>
    <?php
}

/**
 * Help Page Callback
 */
function writgo_help_page() {
    ?>
    <style>
        .writgo-help { max-width: 900px; margin: 20px auto; padding: 0 20px; }
        .help-section { background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .help-section h2 { margin: 0 0 15px; color: #1f2937; }
        .help-section p { color: #4b5563; line-height: 1.7; }
        .help-section ul { color: #4b5563; line-height: 2; }
        .faq-item { border-bottom: 1px solid #f3f4f6; padding: 15px 0; }
        .faq-item:last-child { border-bottom: none; }
        .faq-item h4 { margin: 0 0 8px; color: #1f2937; cursor: pointer; }
        .faq-item p { margin: 0; color: #6b7280; }
    </style>
    
    <div class="writgo-help">
        <h1 style="display: flex; align-items: center; gap: 10px;">❓ Help & Documentatie</h1>
        
        <div class="help-section">
            <h2>🚀 Aan de slag</h2>
            <p>Welkom bij Writgo Theme! Dit thema is speciaal ontwikkeld voor affiliate websites en biedt uitgebreide SEO-functionaliteit ingebouwd.</p>
            <ul>
                <li><strong>Stap 1:</strong> Ga naar Customizer om je logo, kleuren en bedrijfsgegevens in te stellen</li>
                <li><strong>Stap 2:</strong> Maak je eerste artikel en vul de SEO velden in</li>
                <li><strong>Stap 3:</strong> Gebruik shortcodes voor professionele affiliate content</li>
                <li><strong>Stap 4:</strong> Bekijk het SEO Overzicht om je voortgang te monitoren</li>
            </ul>
        </div>
        
        <div class="help-section">
            <h2>🎯 SEO Optimalisatie</h2>
            <p>Elke post en pagina heeft een Writgo SEO meta box met:</p>
            <ul>
                <li><strong>Focus Keyword:</strong> Het zoekwoord waar je op wilt ranken</li>
                <li><strong>SEO Titel:</strong> De titel die in Google verschijnt (max 60 tekens)</li>
                <li><strong>Meta Omschrijving:</strong> De omschrijving in zoekresultaten (max 160 tekens)</li>
                <li><strong>SEO Score:</strong> Real-time analyse van je content optimalisatie</li>
                <li><strong>Google Preview:</strong> Zie hoe je resultaat eruitziet in Google</li>
            </ul>
        </div>
        
        <div class="help-section">
            <h2>💬 Veelgestelde vragen</h2>
            
            <div class="faq-item">
                <h4>Kan ik Yoast of RankMath tegelijk gebruiken?</h4>
                <p>Ja, Writgo detecteert automatisch of Yoast of RankMath actief is en schakelt dan zijn eigen SEO output uit om conflicten te voorkomen.</p>
            </div>
            
            <div class="faq-item">
                <h4>Hoe werkt de SEO score?</h4>
                <p>De score wordt berekend op basis van 10 factoren: focus keyword in titel, meta, en content, lengte van titel en meta, content lengte, afbeelding, interne/externe links, en heading structuur.</p>
            </div>
            
            <div class="faq-item">
                <h4>Worden affiliate links automatisch nofollow?</h4>
                <p>Ja, alle externe links in je content krijgen automatisch rel="nofollow sponsored" toegevoegd.</p>
            </div>
            
            <div class="faq-item">
                <h4>Waar vind ik de theme instellingen?</h4>
                <p>Ga naar Weergave → Customizer voor alle theme instellingen zoals kleuren, logo, bedrijfsgegevens en meer.</p>
            </div>
        </div>
        
        <div class="help-section" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white;">
            <h2 style="color: white;">🤝 Support nodig?</h2>
            <p style="color: rgba(255,255,255,0.9);">Heb je hulp nodig of een feature request? Neem contact op via de Writgo Media website of stuur een e-mail naar support.</p>
        </div>
    </div>
    <?php
}

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

/**
 * Count posts with specific meta key
 */
function writgo_count_posts_with_meta($meta_key, $meta_value = null) {
    global $wpdb;
    
    if ($meta_value !== null) {
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             WHERE pm.meta_key = %s AND pm.meta_value = %s AND p.post_status = 'publish' AND p.post_type = 'post'",
            $meta_key,
            $meta_value
        ));
    } else {
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             WHERE pm.meta_key = %s AND pm.meta_value != '' AND p.post_status = 'publish' AND p.post_type = 'post'",
            $meta_key
        ));
    }
    
    return (int) $count;
}

/**
 * Add admin bar menu item
 */
add_action('admin_bar_menu', 'writgo_admin_bar_menu', 100);
function writgo_admin_bar_menu($wp_admin_bar) {
    if (!current_user_can('edit_posts')) {
        return;
    }
    
    $wp_admin_bar->add_node(array(
        'id'    => 'writgo-dashboard',
        'title' => '<span class="ab-icon" style="margin-right: 5px;">🚀</span> Writgo',
        'href'  => admin_url('admin.php?page=writgo-dashboard'),
    ));
    
    $wp_admin_bar->add_node(array(
        'id'     => 'writgo-seo',
        'parent' => 'writgo-dashboard',
        'title'  => '🎯 SEO Overzicht',
        'href'   => admin_url('admin.php?page=writgo-seo-overview'),
    ));
    
    $wp_admin_bar->add_node(array(
        'id'     => 'writgo-new-post',
        'parent' => 'writgo-dashboard',
        'title'  => '✏️ Nieuw Artikel',
        'href'   => admin_url('post-new.php'),
    ));
}

/**
 * Custom admin footer text
 */
add_filter('admin_footer_text', 'writgo_admin_footer_text');
function writgo_admin_footer_text($text) {
    $screen = get_current_screen();
    if (strpos($screen->id, 'writgo') !== false) {
        return 'Powered by <strong>Writgo Theme v' . WRITGO_VERSION . '</strong> | Made with ❤️ by Writgo Media';
    }
    return $text;
}
