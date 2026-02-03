<?php
/**
 * Writgo SEO - Complete SEO Solution
 * 
 * Features:
 * - Custom SEO title & meta description
 * - Focus keyword with analysis
 * - Real-time SEO score (like RankMath)
 * - SEO recommendations
 * - Schema markup
 * - Open Graph & Twitter Cards
 *
 * @package Writgo_Affiliate
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// SEO META BOX
// =============================================================================

/**
 * Register SEO Meta Box
 */
add_action('add_meta_boxes', 'writgo_seo_register_meta_box');
function writgo_seo_register_meta_box() {
    $post_types = array('post', 'page');
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'writgo_seo_meta_box',
            '🎯 Writgo SEO',
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
    
    // Get saved values
    $seo_title = get_post_meta($post->ID, '_writgo_seo_title', true);
    $seo_description = get_post_meta($post->ID, '_writgo_seo_description', true);
    $focus_keyword = get_post_meta($post->ID, '_writgo_focus_keyword', true);
    $canonical_url = get_post_meta($post->ID, '_writgo_canonical_url', true);
    $noindex = get_post_meta($post->ID, '_writgo_noindex', true);
    $nofollow = get_post_meta($post->ID, '_writgo_nofollow', true);
    
    // Get post content for analysis
    $post_title = $post->post_title;
    $post_content = $post->post_content;
    ?>
    
    <style>
        .writgo-seo-box { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        
        /* Tabs */
        .writgo-seo-tabs { display: flex; border-bottom: 2px solid #e5e7eb; margin-bottom: 20px; }
        .writgo-seo-tab { padding: 12px 20px; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; font-weight: 500; color: #6b7280; transition: all 0.2s; }
        .writgo-seo-tab:hover { color: #f97316; }
        .writgo-seo-tab.active { color: #f97316; border-bottom-color: #f97316; }
        
        /* Tab Content */
        .writgo-seo-tab-content { display: none; }
        .writgo-seo-tab-content.active { display: block; }
        
        /* Score Circle */
        .writgo-seo-score-wrapper { display: flex; align-items: center; gap: 20px; padding: 20px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 12px; margin-bottom: 20px; }
        .writgo-seo-score-circle { position: relative; width: 80px; height: 80px; }
        .writgo-seo-score-circle svg { transform: rotate(-90deg); }
        .writgo-seo-score-circle circle { fill: none; stroke-width: 8; }
        .writgo-seo-score-circle .bg { stroke: #e5e7eb; }
        .writgo-seo-score-circle .progress { stroke: #22c55e; stroke-linecap: round; transition: stroke-dashoffset 0.5s ease, stroke 0.3s ease; }
        .writgo-seo-score-value { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 24px; font-weight: 700; }
        .writgo-seo-score-label { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Score Info */
        .writgo-seo-score-info h3 { margin: 0 0 5px; font-size: 18px; color: #1f2937; }
        .writgo-seo-score-info p { margin: 0; color: #6b7280; font-size: 14px; }
        
        /* Form Fields */
        .writgo-seo-field { margin-bottom: 20px; }
        .writgo-seo-field label { display: block; font-weight: 600; margin-bottom: 8px; color: #374151; }
        .writgo-seo-field input[type="text"],
        .writgo-seo-field input[type="url"],
        .writgo-seo-field textarea { width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s; }
        .writgo-seo-field input:focus,
        .writgo-seo-field textarea:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1); }
        .writgo-seo-field .char-count { font-size: 12px; color: #9ca3af; margin-top: 6px; }
        .writgo-seo-field .char-count.warning { color: #f59e0b; }
        .writgo-seo-field .char-count.danger { color: #ef4444; }
        .writgo-seo-field .char-count.good { color: #22c55e; }
        
        /* Preview */
        .writgo-seo-preview { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .writgo-seo-preview h4 { margin: 0 0 15px; font-size: 14px; color: #6b7280; font-weight: 500; }
        .writgo-seo-preview-google { font-family: Arial, sans-serif; }
        .writgo-seo-preview-title { color: #1a0dab; font-size: 20px; line-height: 1.3; margin-bottom: 4px; text-decoration: none; cursor: pointer; }
        .writgo-seo-preview-title:hover { text-decoration: underline; }
        .writgo-seo-preview-url { color: #006621; font-size: 14px; margin-bottom: 4px; }
        .writgo-seo-preview-desc { color: #545454; font-size: 14px; line-height: 1.5; }
        
        /* Checklist */
        .writgo-seo-checklist { list-style: none; padding: 0; margin: 0; }
        .writgo-seo-checklist li { display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
        .writgo-seo-checklist li:last-child { border-bottom: none; }
        .writgo-seo-checklist .icon { flex-shrink: 0; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .writgo-seo-checklist .icon.good { background: #dcfce7; color: #16a34a; }
        .writgo-seo-checklist .icon.warning { background: #fef3c7; color: #d97706; }
        .writgo-seo-checklist .icon.bad { background: #fee2e2; color: #dc2626; }
        .writgo-seo-checklist .text { flex: 1; }
        .writgo-seo-checklist .text strong { display: block; font-size: 14px; color: #1f2937; margin-bottom: 2px; }
        .writgo-seo-checklist .text span { font-size: 13px; color: #6b7280; }
        
        /* Focus Keyword Tag */
        .writgo-focus-keyword-input { display: flex; gap: 10px; }
        .writgo-focus-keyword-input input { flex: 1; }
        .writgo-keyword-tag { display: inline-flex; align-items: center; gap: 6px; background: #f97316; color: white; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 500; }
        
        /* Robot Settings */
        .writgo-robot-settings { display: flex; gap: 20px; }
        .writgo-robot-settings label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        
        /* Responsive */
        @media (max-width: 782px) {
            .writgo-seo-score-wrapper { flex-direction: column; text-align: center; }
            .writgo-seo-tabs { flex-wrap: wrap; }
        }
    </style>
    
    <div class="writgo-seo-box">
        <!-- Score Display -->
        <div class="writgo-seo-score-wrapper">
            <div class="writgo-seo-score-circle">
                <svg width="80" height="80" viewBox="0 0 80 80">
                    <circle class="bg" cx="40" cy="40" r="36"/>
                    <circle class="progress" cx="40" cy="40" r="36" 
                            stroke-dasharray="226.2" 
                            stroke-dashoffset="226.2"
                            id="writgoSeoScoreCircle"/>
                </svg>
                <div class="writgo-seo-score-value" id="writgoSeoScoreValue">0</div>
            </div>
            <div class="writgo-seo-score-info">
                <h3 id="writgoSeoScoreLabel">SEO Score wordt berekend...</h3>
                <p id="writgoSeoScoreDesc">Vul de velden in voor een analyse</p>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="writgo-seo-tabs">
            <div class="writgo-seo-tab active" data-tab="general">📝 Algemeen</div>
            <div class="writgo-seo-tab" data-tab="analysis">📊 Analyse</div>
            <div class="writgo-seo-tab" data-tab="advanced">⚙️ Geavanceerd</div>
        </div>
        
        <!-- General Tab -->
        <div class="writgo-seo-tab-content active" id="tab-general">
            <!-- Focus Keyword -->
            <div class="writgo-seo-field">
                <label for="writgo_focus_keyword">🎯 Focus Keyword</label>
                <div class="writgo-focus-keyword-input">
                    <input type="text" 
                           id="writgo_focus_keyword" 
                           name="writgo_focus_keyword" 
                           value="<?php echo esc_attr($focus_keyword); ?>" 
                           placeholder="bijv. beste bluetooth speaker" />
                </div>
                <p class="char-count">Het woord of de woordcombinatie waar je op wilt ranken</p>
            </div>
            
            <!-- SEO Title -->
            <div class="writgo-seo-field">
                <label for="writgo_seo_title">SEO Titel</label>
                <input type="text" 
                       id="writgo_seo_title" 
                       name="writgo_seo_title" 
                       value="<?php echo esc_attr($seo_title); ?>" 
                       placeholder="<?php echo esc_attr($post_title); ?>" 
                       data-default="<?php echo esc_attr($post_title); ?>" />
                <p class="char-count"><span id="titleCharCount">0</span>/60 tekens <span id="titleCharStatus"></span></p>
            </div>
            
            <!-- SEO Description -->
            <div class="writgo-seo-field">
                <label for="writgo_seo_description">Meta Omschrijving</label>
                <textarea id="writgo_seo_description" 
                          name="writgo_seo_description" 
                          rows="3" 
                          placeholder="Schrijf een pakkende omschrijving voor in de zoekresultaten..."><?php echo esc_textarea($seo_description); ?></textarea>
                <p class="char-count"><span id="descCharCount">0</span>/160 tekens <span id="descCharStatus"></span></p>
            </div>
            
            <!-- Google Preview -->
            <div class="writgo-seo-preview">
                <h4>👀 Google Preview</h4>
                <div class="writgo-seo-preview-google">
                    <div class="writgo-seo-preview-title" id="previewTitle"><?php echo esc_html($post_title ?: 'Titel van je artikel'); ?></div>
                    <div class="writgo-seo-preview-url"><?php echo esc_url(get_permalink($post->ID) ?: home_url('/voorbeeld-url/')); ?></div>
                    <div class="writgo-seo-preview-desc" id="previewDesc">Meta omschrijving verschijnt hier...</div>
                </div>
            </div>
        </div>
        
        <!-- Analysis Tab -->
        <div class="writgo-seo-tab-content" id="tab-analysis">
            <h4 style="margin: 0 0 15px; color: #374151;">📋 SEO Checklist</h4>
            <ul class="writgo-seo-checklist" id="seoChecklist">
                <li>
                    <span class="icon" id="check-keyword-title">?</span>
                    <div class="text">
                        <strong>Focus keyword in titel</strong>
                        <span id="check-keyword-title-text">Wordt gecontroleerd...</span>
                    </div>
                </li>
                <li>
                    <span class="icon" id="check-keyword-desc">?</span>
                    <div class="text">
                        <strong>Focus keyword in meta omschrijving</strong>
                        <span id="check-keyword-desc-text">Wordt gecontroleerd...</span>
                    </div>
                </li>
                <li>
                    <span class="icon" id="check-keyword-content">?</span>
                    <div class="text">
                        <strong>Focus keyword in content</strong>
                        <span id="check-keyword-content-text">Wordt gecontroleerd...</span>
                    </div>
                </li>
                <li>
                    <span class="icon" id="check-title-length">?</span>
                    <div class="text">
                        <strong>SEO titel lengte</strong>
                        <span id="check-title-length-text">Wordt gecontroleerd...</span>
                    </div>
                </li>
                <li>
                    <span class="icon" id="check-desc-length">?</span>
                    <div class="text">
                        <strong>Meta omschrijving lengte</strong>
                        <span id="check-desc-length-text">Wordt gecontroleerd...</span>
                    </div>
                </li>
                <li>
                    <span class="icon" id="check-content-length">?</span>
                    <div class="text">
                        <strong>Content lengte</strong>
                        <span id="check-content-length-text">Wordt gecontroleerd...</span>
                    </div>
                </li>
                <li>
                    <span class="icon" id="check-has-image">?</span>
                    <div class="text">
                        <strong>Uitgelichte afbeelding</strong>
                        <span id="check-has-image-text">Wordt gecontroleerd...</span>
                    </div>
                </li>
                <li>
                    <span class="icon" id="check-internal-links">?</span>
                    <div class="text">
                        <strong>Interne links</strong>
                        <span id="check-internal-links-text">Wordt gecontroleerd...</span>
                    </div>
                </li>
                <li>
                    <span class="icon" id="check-external-links">?</span>
                    <div class="text">
                        <strong>Externe links</strong>
                        <span id="check-external-links-text">Wordt gecontroleerd...</span>
                    </div>
                </li>
                <li>
                    <span class="icon" id="check-headings">?</span>
                    <div class="text">
                        <strong>Heading structuur (H2/H3)</strong>
                        <span id="check-headings-text">Wordt gecontroleerd...</span>
                    </div>
                </li>
            </ul>
        </div>
        
        <!-- Advanced Tab -->
        <div class="writgo-seo-tab-content" id="tab-advanced">
            <!-- Canonical URL -->
            <div class="writgo-seo-field">
                <label for="writgo_canonical_url">Canonical URL</label>
                <input type="url" 
                       id="writgo_canonical_url" 
                       name="writgo_canonical_url" 
                       value="<?php echo esc_attr($canonical_url); ?>" 
                       placeholder="Laat leeg voor standaard URL" />
                <p class="char-count">Gebruik dit voor duplicate content of als je de originele bron wilt aangeven</p>
            </div>
            
            <!-- Robot Settings -->
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
    </div>
    
    <!-- Hidden field for content analysis -->
    <input type="hidden" id="writgo_post_content" value="<?php echo esc_attr($post_content); ?>" />
    <input type="hidden" id="writgo_post_title" value="<?php echo esc_attr($post_title); ?>" />
    <input type="hidden" id="writgo_has_thumbnail" value="<?php echo has_post_thumbnail($post->ID) ? '1' : '0'; ?>" />
    <input type="hidden" id="writgo_home_url" value="<?php echo esc_url(home_url()); ?>" />
    
    <script>
    (function() {
        // Tab switching
        document.querySelectorAll('.writgo-seo-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.writgo-seo-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.writgo-seo-tab-content').forEach(c => c.classList.remove('active'));
                
                this.classList.add('active');
                document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            });
        });
        
        // Elements
        var titleInput = document.getElementById('writgo_seo_title');
        var descInput = document.getElementById('writgo_seo_description');
        var keywordInput = document.getElementById('writgo_focus_keyword');
        var titleCount = document.getElementById('titleCharCount');
        var descCount = document.getElementById('descCharCount');
        var titleStatus = document.getElementById('titleCharStatus');
        var descStatus = document.getElementById('descCharStatus');
        var previewTitle = document.getElementById('previewTitle');
        var previewDesc = document.getElementById('previewDesc');
        var scoreCircle = document.getElementById('writgoSeoScoreCircle');
        var scoreValue = document.getElementById('writgoSeoScoreValue');
        var scoreLabel = document.getElementById('writgoSeoScoreLabel');
        var scoreDesc = document.getElementById('writgoSeoScoreDesc');
        
        var postContent = document.getElementById('writgo_post_content').value;
        var postTitle = document.getElementById('writgo_post_title').value;
        var hasThumbnail = document.getElementById('writgo_has_thumbnail').value === '1';
        var homeUrl = document.getElementById('writgo_home_url').value;
        
        // Character count functions
        function updateCharCount(input, countEl, statusEl, min, optimal, max) {
            var len = input.value.length;
            countEl.textContent = len;
            
            var parent = countEl.parentElement;
            parent.classList.remove('good', 'warning', 'danger');
            
            if (len === 0) {
                statusEl.textContent = '';
            } else if (len < min) {
                parent.classList.add('warning');
                statusEl.textContent = '(te kort)';
            } else if (len <= optimal) {
                parent.classList.add('good');
                statusEl.textContent = '(✓ perfect)';
            } else if (len <= max) {
                parent.classList.add('warning');
                statusEl.textContent = '(goed, maar aan de lange kant)';
            } else {
                parent.classList.add('danger');
                statusEl.textContent = '(te lang!)';
            }
        }
        
        // Update preview
        function updatePreview() {
            var title = titleInput.value || postTitle || 'Titel van je artikel';
            var desc = descInput.value || 'Meta omschrijving verschijnt hier...';
            
            previewTitle.textContent = title.substring(0, 65) + (title.length > 65 ? '...' : '');
            previewDesc.textContent = desc.substring(0, 165) + (desc.length > 165 ? '...' : '');
        }
        
        // SEO Analysis
        function analyzeSEO() {
            var keyword = keywordInput.value.toLowerCase().trim();
            var title = (titleInput.value || postTitle).toLowerCase();
            var desc = descInput.value.toLowerCase();
            var content = postContent.toLowerCase();
            var contentText = content.replace(/<[^>]*>/g, '');
            var wordCount = contentText.split(/\s+/).filter(w => w.length > 0).length;
            
            var score = 0;
            var maxScore = 100;
            var checks = [];
            
            // 1. Keyword in title (15 points)
            if (keyword) {
                if (title.includes(keyword)) {
                    checks.push({ id: 'keyword-title', status: 'good', text: 'Focus keyword staat in de titel', points: 15 });
                    score += 15;
                } else {
                    checks.push({ id: 'keyword-title', status: 'bad', text: 'Focus keyword ontbreekt in de titel', points: 0 });
                }
            } else {
                checks.push({ id: 'keyword-title', status: 'warning', text: 'Voeg een focus keyword toe', points: 5 });
                score += 5;
            }
            
            // 2. Keyword in description (10 points)
            if (keyword) {
                if (desc.includes(keyword)) {
                    checks.push({ id: 'keyword-desc', status: 'good', text: 'Focus keyword staat in de meta omschrijving', points: 10 });
                    score += 10;
                } else {
                    checks.push({ id: 'keyword-desc', status: 'bad', text: 'Focus keyword ontbreekt in de meta omschrijving', points: 0 });
                }
            } else {
                checks.push({ id: 'keyword-desc', status: 'warning', text: 'Voeg een focus keyword toe', points: 5 });
                score += 5;
            }
            
            // 3. Keyword in content (15 points)
            if (keyword) {
                var keywordRegex = new RegExp(keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
                var keywordMatches = (contentText.match(keywordRegex) || []).length;
                var keywordDensity = wordCount > 0 ? (keywordMatches / wordCount) * 100 : 0;
                
                if (keywordMatches >= 3 && keywordDensity >= 0.5 && keywordDensity <= 2.5) {
                    checks.push({ id: 'keyword-content', status: 'good', text: 'Focus keyword komt ' + keywordMatches + 'x voor (dichtheid: ' + keywordDensity.toFixed(1) + '%)', points: 15 });
                    score += 15;
                } else if (keywordMatches >= 1) {
                    checks.push({ id: 'keyword-content', status: 'warning', text: 'Focus keyword komt ' + keywordMatches + 'x voor. Probeer 3-5x voor betere resultaten', points: 8 });
                    score += 8;
                } else {
                    checks.push({ id: 'keyword-content', status: 'bad', text: 'Focus keyword niet gevonden in de content', points: 0 });
                }
            } else {
                checks.push({ id: 'keyword-content', status: 'warning', text: 'Voeg een focus keyword toe', points: 5 });
                score += 5;
            }
            
            // 4. Title length (10 points)
            var titleLen = (titleInput.value || postTitle).length;
            if (titleLen >= 30 && titleLen <= 60) {
                checks.push({ id: 'title-length', status: 'good', text: 'SEO titel heeft de perfecte lengte (' + titleLen + ' tekens)', points: 10 });
                score += 10;
            } else if (titleLen > 0 && titleLen < 30) {
                checks.push({ id: 'title-length', status: 'warning', text: 'SEO titel is te kort (' + titleLen + ' tekens). Streef naar 30-60 tekens', points: 5 });
                score += 5;
            } else if (titleLen > 60) {
                checks.push({ id: 'title-length', status: 'warning', text: 'SEO titel is te lang (' + titleLen + ' tekens). Wordt mogelijk afgeknipt', points: 5 });
                score += 5;
            } else {
                checks.push({ id: 'title-length', status: 'bad', text: 'Voeg een SEO titel toe', points: 0 });
            }
            
            // 5. Description length (10 points)
            var descLen = descInput.value.length;
            if (descLen >= 120 && descLen <= 160) {
                checks.push({ id: 'desc-length', status: 'good', text: 'Meta omschrijving heeft de perfecte lengte (' + descLen + ' tekens)', points: 10 });
                score += 10;
            } else if (descLen >= 50 && descLen < 120) {
                checks.push({ id: 'desc-length', status: 'warning', text: 'Meta omschrijving is wat kort (' + descLen + ' tekens). Streef naar 120-160', points: 5 });
                score += 5;
            } else if (descLen > 160) {
                checks.push({ id: 'desc-length', status: 'warning', text: 'Meta omschrijving is te lang (' + descLen + ' tekens). Wordt afgeknipt', points: 5 });
                score += 5;
            } else {
                checks.push({ id: 'desc-length', status: 'bad', text: 'Voeg een meta omschrijving toe', points: 0 });
            }
            
            // 6. Content length (15 points)
            if (wordCount >= 1000) {
                checks.push({ id: 'content-length', status: 'good', text: 'Uitstekende content lengte (' + wordCount + ' woorden)', points: 15 });
                score += 15;
            } else if (wordCount >= 500) {
                checks.push({ id: 'content-length', status: 'good', text: 'Goede content lengte (' + wordCount + ' woorden)', points: 12 });
                score += 12;
            } else if (wordCount >= 300) {
                checks.push({ id: 'content-length', status: 'warning', text: 'Content is wat kort (' + wordCount + ' woorden). Streef naar 500+', points: 7 });
                score += 7;
            } else {
                checks.push({ id: 'content-length', status: 'bad', text: 'Content is te kort (' + wordCount + ' woorden). Voeg meer toe', points: 3 });
                score += 3;
            }
            
            // 7. Featured image (8 points)
            if (hasThumbnail) {
                checks.push({ id: 'has-image', status: 'good', text: 'Uitgelichte afbeelding is ingesteld', points: 8 });
                score += 8;
            } else {
                checks.push({ id: 'has-image', status: 'warning', text: 'Voeg een uitgelichte afbeelding toe', points: 0 });
            }
            
            // 8. Internal links (8 points)
            var internalLinkRegex = new RegExp('href=["\']' + homeUrl.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
            var internalLinks = (content.match(internalLinkRegex) || []).length;
            if (internalLinks >= 2) {
                checks.push({ id: 'internal-links', status: 'good', text: internalLinks + ' interne links gevonden', points: 8 });
                score += 8;
            } else if (internalLinks === 1) {
                checks.push({ id: 'internal-links', status: 'warning', text: '1 interne link gevonden. Voeg er meer toe', points: 4 });
                score += 4;
            } else {
                checks.push({ id: 'internal-links', status: 'bad', text: 'Geen interne links gevonden. Voeg links naar andere pagina\'s toe', points: 0 });
            }
            
            // 9. External links (5 points)
            var allLinks = (content.match(/href=["'][^"']+["']/gi) || []).length;
            var externalLinks = allLinks - internalLinks;
            if (externalLinks >= 1) {
                checks.push({ id: 'external-links', status: 'good', text: externalLinks + ' externe link(s) gevonden', points: 5 });
                score += 5;
            } else {
                checks.push({ id: 'external-links', status: 'warning', text: 'Geen externe links. Overweeg bronvermelding toe te voegen', points: 2 });
                score += 2;
            }
            
            // 10. Headings (4 points)
            var h2Count = (content.match(/<h2/gi) || []).length;
            var h3Count = (content.match(/<h3/gi) || []).length;
            if (h2Count >= 2 && h3Count >= 1) {
                checks.push({ id: 'headings', status: 'good', text: 'Goede heading structuur (H2: ' + h2Count + ', H3: ' + h3Count + ')', points: 4 });
                score += 4;
            } else if (h2Count >= 1) {
                checks.push({ id: 'headings', status: 'warning', text: 'Voeg meer subkoppen toe (H2: ' + h2Count + ', H3: ' + h3Count + ')', points: 2 });
                score += 2;
            } else {
                checks.push({ id: 'headings', status: 'bad', text: 'Geen subkoppen (H2/H3) gevonden', points: 0 });
            }
            
            // Update UI
            checks.forEach(function(check) {
                var iconEl = document.getElementById('check-' + check.id);
                var textEl = document.getElementById('check-' + check.id + '-text');
                
                if (iconEl && textEl) {
                    iconEl.className = 'icon ' + check.status;
                    iconEl.textContent = check.status === 'good' ? '✓' : (check.status === 'warning' ? '!' : '✗');
                    textEl.textContent = check.text;
                }
            });
            
            // Update score
            var circumference = 2 * Math.PI * 36;
            var offset = circumference - (score / maxScore) * circumference;
            scoreCircle.style.strokeDashoffset = offset;
            scoreValue.textContent = score;
            
            // Score color
            if (score >= 80) {
                scoreCircle.style.stroke = '#22c55e';
                scoreLabel.textContent = 'Uitstekend!';
                scoreDesc.textContent = 'Je content is goed geoptimaliseerd voor zoekmachines';
            } else if (score >= 60) {
                scoreCircle.style.stroke = '#84cc16';
                scoreLabel.textContent = 'Goed';
                scoreDesc.textContent = 'Er zijn nog een paar verbeterpunten';
            } else if (score >= 40) {
                scoreCircle.style.stroke = '#f59e0b';
                scoreLabel.textContent = 'Kan beter';
                scoreDesc.textContent = 'Volg de aanbevelingen voor betere SEO';
            } else {
                scoreCircle.style.stroke = '#ef4444';
                scoreLabel.textContent = 'Actie nodig';
                scoreDesc.textContent = 'Je content heeft SEO-optimalisatie nodig';
            }
        }
        
        // Event listeners
        titleInput.addEventListener('input', function() {
            updateCharCount(titleInput, titleCount, titleStatus, 30, 55, 60);
            updatePreview();
            analyzeSEO();
        });
        
        descInput.addEventListener('input', function() {
            updateCharCount(descInput, descCount, descStatus, 100, 155, 160);
            updatePreview();
            analyzeSEO();
        });
        
        keywordInput.addEventListener('input', function() {
            analyzeSEO();
        });
        
        // Listen for content changes in Gutenberg
        if (typeof wp !== 'undefined' && wp.data && wp.data.subscribe) {
            var lastContent = '';
            var lastThumbnail = hasThumbnail;
            
            wp.data.subscribe(function() {
                var editor = wp.data.select('core/editor');
                if (editor) {
                    var newContent = editor.getEditedPostContent();
                    var newThumbnail = editor.getEditedPostAttribute('featured_media') > 0;
                    
                    if (newContent !== lastContent || newThumbnail !== lastThumbnail) {
                        lastContent = newContent;
                        lastThumbnail = newThumbnail;
                        postContent = newContent;
                        hasThumbnail = newThumbnail;
                        analyzeSEO();
                    }
                }
            });
        }
        
        // Initial analysis
        setTimeout(function() {
            updateCharCount(titleInput, titleCount, titleStatus, 30, 55, 60);
            updateCharCount(descInput, descCount, descStatus, 100, 155, 160);
            updatePreview();
            analyzeSEO();
        }, 500);
    })();
    </script>
    
    <?php
}

/**
 * Save SEO Meta Box Data
 */
add_action('save_post', 'writgo_seo_save_meta_box');
function writgo_seo_save_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['writgo_seo_nonce']) || !wp_verify_nonce($_POST['writgo_seo_nonce'], 'writgo_seo_meta_box')) {
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
    
    // Save fields
    $fields = array(
        'writgo_seo_title'       => 'sanitize_text_field',
        'writgo_seo_description' => 'sanitize_textarea_field',
        'writgo_focus_keyword'   => 'sanitize_text_field',
        'writgo_canonical_url'   => 'esc_url_raw',
    );
    
    foreach ($fields as $field => $sanitize) {
        if (isset($_POST[$field])) {
            $value = call_user_func($sanitize, $_POST[$field]);
            if (!empty($value)) {
                update_post_meta($post_id, '_' . $field, $value);
            } else {
                delete_post_meta($post_id, '_' . $field);
            }
        }
    }
    
    // Save checkboxes
    update_post_meta($post_id, '_writgo_noindex', isset($_POST['writgo_noindex']) ? '1' : '');
    update_post_meta($post_id, '_writgo_nofollow', isset($_POST['writgo_nofollow']) ? '1' : '');
}

// =============================================================================
// FRONTEND SEO OUTPUT
// =============================================================================

/**
 * Add SEO Meta Tags to <head>
 */
add_action('wp_head', 'writgo_seo_meta_tags', 1);
function writgo_seo_meta_tags() {
    // Skip if Yoast or RankMath is active
    if (defined('WPSEO_VERSION') || class_exists('RankMath')) {
        return;
    }
    
    global $post;
    
    // Default values
    $title = get_bloginfo('name');
    $description = get_bloginfo('description');
    $image = '';
    $url = home_url('/');
    $robots = array('index', 'follow');
    
    if (is_singular()) {
        // Get custom SEO values
        $seo_title = get_post_meta($post->ID, '_writgo_seo_title', true);
        $seo_desc = get_post_meta($post->ID, '_writgo_seo_description', true);
        $noindex = get_post_meta($post->ID, '_writgo_noindex', true);
        $nofollow = get_post_meta($post->ID, '_writgo_nofollow', true);
        
        $title = $seo_title ?: get_the_title();
        $description = $seo_desc ?: (has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_tags($post->post_content), 30));
        $url = get_permalink();
        
        // Apply year filter
        $title = apply_filters('writgo_seo_title', $title);
        $description = apply_filters('writgo_seo_description', $description);
        
        if (has_post_thumbnail()) {
            $image = get_the_post_thumbnail_url($post->ID, 'large');
        }
        
        if ($noindex === '1') {
            $robots[0] = 'noindex';
        }
        if ($nofollow === '1') {
            $robots[1] = 'nofollow';
        }
    } elseif (is_category()) {
        $title = single_cat_title('', false);
        $description = category_description() ?: $title;
        $url = get_category_link(get_queried_object_id());
        
        // Apply year filter
        $title = apply_filters('writgo_seo_title', $title);
        $description = apply_filters('writgo_seo_description', $description);
    } elseif (is_search()) {
        $title = 'Zoekresultaten voor: ' . get_search_query();
        $robots = array('noindex', 'follow');
    }
    
    // Clean description
    $description = wp_strip_all_tags($description);
    $description = substr($description, 0, 160);
    
    // Output meta tags
    ?>
    <meta name="description" content="<?php echo esc_attr($description); ?>">
    <meta name="robots" content="<?php echo esc_attr(implode(', ', $robots)); ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($description); ?>">
    <meta property="og:url" content="<?php echo esc_url($url); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <meta property="og:type" content="<?php echo is_singular() ? 'article' : 'website'; ?>">
    <meta property="og:locale" content="nl_NL">
    <?php if ($image) : ?>
    <meta property="og:image" content="<?php echo esc_url($image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <?php endif; ?>
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($description); ?>">
    <?php if ($image) : ?>
    <meta name="twitter:image" content="<?php echo esc_url($image); ?>">
    <?php endif; ?>
    <?php
}

/**
 * Filter document title
 */
add_filter('pre_get_document_title', 'writgo_seo_document_title', 20);
function writgo_seo_document_title($title) {
    if (defined('WPSEO_VERSION') || class_exists('RankMath')) {
        return $title;
    }
    
    if (is_singular()) {
        global $post;
        $seo_title = get_post_meta($post->ID, '_writgo_seo_title', true);
        
        if ($seo_title) {
            // Apply year filter
            $seo_title = apply_filters('writgo_seo_title', $seo_title);
            return $seo_title . ' | ' . get_bloginfo('name');
        }
    }
    
    return $title;;
}

/**
 * Add Schema.org Article markup
 */
add_action('wp_head', 'writgo_schema_markup');
function writgo_schema_markup() {
    if (!is_singular('post')) {
        return;
    }
    
    global $post;
    
    // Get focus keyword for keywords
    $focus_keyword = get_post_meta($post->ID, '_writgo_focus_keyword', true);
    
    $schema = array(
        '@context'      => 'https://schema.org',
        '@type'         => 'Article',
        'headline'      => get_the_title(),
        'description'   => has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_tags($post->post_content), 30),
        'datePublished' => get_the_date('c'),
        'dateModified'  => get_the_modified_date('c'),
        'url'           => get_permalink(),
        'author'        => array(
            '@type' => 'Person',
            'name'  => get_the_author(),
        ),
        'publisher'     => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
        ),
    );
    
    if (has_post_thumbnail()) {
        $schema['image'] = array(
            '@type'  => 'ImageObject',
            'url'    => get_the_post_thumbnail_url($post->ID, 'large'),
            'width'  => 1200,
            'height' => 630,
        );
    }
    
    if ($focus_keyword) {
        $schema['keywords'] = $focus_keyword;
    }
    
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/**
 * Optimize title tag separator
 */
add_filter('document_title_separator', function() {
    return '|';
});

/**
 * Add canonical URL
 */
add_action('wp_head', 'writgo_canonical_url');
function writgo_canonical_url() {
    if (defined('WPSEO_VERSION') || class_exists('RankMath')) {
        return;
    }
    
    if (is_singular()) {
        global $post;
        $canonical = get_post_meta($post->ID, '_writgo_canonical_url', true);
        $url = $canonical ?: get_permalink();
        echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
    } elseif (is_category()) {
        echo '<link rel="canonical" href="' . esc_url(get_category_link(get_queried_object_id())) . '">' . "\n";
    }
}

/**
 * Add focus keyword to post class
 */
add_filter('post_class', 'writgo_seo_post_class');
function writgo_seo_post_class($classes) {
    if (is_singular()) {
        global $post;
        $focus_keyword = get_post_meta($post->ID, '_writgo_focus_keyword', true);
        if ($focus_keyword) {
            $classes[] = 'has-focus-keyword';
        }
    }
    return $classes;
}
