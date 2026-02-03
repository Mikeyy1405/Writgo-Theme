<?php
/**
 * Writgo SEO - Advanced Analysis Module
 * 
 * Readability score, secondary keywords, advanced checks
 *
 * @package Writgo_Affiliate
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// =============================================================================
// READABILITY META BOX
// =============================================================================

add_action('add_meta_boxes', 'writgo_readability_register_meta_box');
function writgo_readability_register_meta_box() {
    add_meta_box(
        'writgo_readability_meta_box',
        '📖 Leesbaarheid & Content Analyse',
        'writgo_readability_meta_box_callback',
        array('post', 'page'),
        'normal',
        'default'
    );
}

function writgo_readability_meta_box_callback($post) {
    wp_nonce_field('writgo_readability_meta_box', 'writgo_readability_nonce');
    
    $secondary_keywords = get_post_meta($post->ID, '_writgo_secondary_keywords', true);
    $cornerstone = get_post_meta($post->ID, '_writgo_cornerstone', true);
    
    $content = $post->post_content;
    $text = wp_strip_all_tags($content);
    
    // Calculate metrics
    $word_count = str_word_count($text);
    $sentence_count = preg_match_all('/[.!?]+/', $text, $matches);
    $sentence_count = max(1, $sentence_count);
    $paragraph_count = substr_count($content, '</p>') + substr_count($content, "\n\n");
    $paragraph_count = max(1, $paragraph_count);
    
    // Average sentence length
    $avg_sentence_length = round($word_count / $sentence_count, 1);
    
    // Average words per paragraph
    $avg_paragraph_length = round($word_count / $paragraph_count, 1);
    
    // Syllable estimation (Dutch approximation)
    $syllables = writgo_count_syllables($text);
    
    // Flesch Reading Ease (adjusted for Dutch)
    $flesch_score = 206.835 - (1.015 * $avg_sentence_length) - (84.6 * ($syllables / max(1, $word_count)));
    $flesch_score = max(0, min(100, round($flesch_score)));
    
    // Count headings
    $h2_count = substr_count(strtolower($content), '<h2');
    $h3_count = substr_count(strtolower($content), '<h3');
    $h4_count = substr_count(strtolower($content), '<h4');
    
    // Count images
    $image_count = substr_count(strtolower($content), '<img');
    
    // Count links
    $total_links = substr_count(strtolower($content), '<a ');
    $internal_links = substr_count(strtolower($content), 'href="' . strtolower(home_url()));
    $external_links = $total_links - $internal_links;
    
    // Passive voice detection (basic Dutch patterns)
    $passive_patterns = array('wordt', 'worden', 'werd', 'werden', 'is gemaakt', 'zijn gemaakt', 'is gedaan', 'wordt gezien');
    $passive_count = 0;
    foreach ($passive_patterns as $pattern) {
        $passive_count += substr_count(strtolower($text), $pattern);
    }
    
    // Transition words (Dutch)
    $transition_words = array('daarom', 'echter', 'bovendien', 'bijvoorbeeld', 'namelijk', 'dus', 'want', 'maar', 'ook', 'verder', 'allereerst', 'ten eerste', 'ten tweede', 'vervolgens', 'daarna', 'tenslotte', 'kortom', 'samenvattend', 'concluderend', 'met andere woorden', 'dat wil zeggen');
    $transition_count = 0;
    foreach ($transition_words as $word) {
        $transition_count += substr_count(strtolower($text), $word);
    }
    $transition_percentage = $sentence_count > 0 ? round(($transition_count / $sentence_count) * 100) : 0;
    
    // Long sentences (>20 words)
    $sentences = preg_split('/[.!?]+/', $text);
    $long_sentences = 0;
    foreach ($sentences as $sentence) {
        if (str_word_count(trim($sentence)) > 20) {
            $long_sentences++;
        }
    }
    $long_sentence_percentage = $sentence_count > 0 ? round(($long_sentences / $sentence_count) * 100) : 0;
    ?>
    
    <style>
        .writgo-readability-box { padding: 15px 0; }
        .writgo-readability-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .writgo-metric-card { background: #f9fafb; padding: 20px; border-radius: 10px; text-align: center; }
        .writgo-metric-value { font-size: 32px; font-weight: 700; color: #1f2937; }
        .writgo-metric-label { font-size: 13px; color: #6b7280; margin-top: 5px; }
        .writgo-metric-card.good .writgo-metric-value { color: #16a34a; }
        .writgo-metric-card.warning .writgo-metric-value { color: #d97706; }
        .writgo-metric-card.bad .writgo-metric-value { color: #dc2626; }
        
        .writgo-readability-score { display: flex; align-items: center; gap: 20px; background: linear-gradient(135deg, #f0fdf4, #dcfce7); padding: 20px; border-radius: 12px; margin-bottom: 25px; }
        .writgo-readability-score.warning { background: linear-gradient(135deg, #fefce8, #fef3c7); }
        .writgo-readability-score.bad { background: linear-gradient(135deg, #fef2f2, #fee2e2); }
        .writgo-score-circle { width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; color: white; background: #16a34a; }
        .writgo-readability-score.warning .writgo-score-circle { background: #d97706; }
        .writgo-readability-score.bad .writgo-score-circle { background: #dc2626; }
        .writgo-score-info h4 { margin: 0 0 5px; color: #1f2937; }
        .writgo-score-info p { margin: 0; color: #6b7280; font-size: 14px; }
        
        .writgo-analysis-section { margin-bottom: 25px; }
        .writgo-analysis-section h4 { margin: 0 0 15px; color: #374151; font-size: 15px; }
        
        .writgo-check-list { list-style: none; padding: 0; margin: 0; }
        .writgo-check-list li { display: flex; align-items: flex-start; gap: 10px; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .writgo-check-list li:last-child { border-bottom: none; }
        .writgo-check-icon { width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0; }
        .writgo-check-icon.good { background: #dcfce7; color: #16a34a; }
        .writgo-check-icon.warning { background: #fef3c7; color: #d97706; }
        .writgo-check-icon.bad { background: #fee2e2; color: #dc2626; }
        .writgo-check-text { flex: 1; font-size: 14px; color: #374151; }
        
        .writgo-secondary-keywords { margin-top: 20px; }
        .writgo-secondary-keywords input { width: 100%; padding: 12px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; }
        .writgo-secondary-keywords input:focus { border-color: #f97316; outline: none; }
        .writgo-secondary-keywords label { display: block; font-weight: 600; margin-bottom: 8px; color: #374151; }
        .writgo-tip { font-size: 12px; color: #6b7280; margin-top: 6px; }
        
        .writgo-cornerstone { margin-top: 20px; padding: 15px; background: #fef3c7; border-radius: 8px; }
        .writgo-cornerstone label { display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 500; }
    </style>
    
    <div class="writgo-readability-box">
        
        <!-- Readability Score -->
        <?php 
        $score_class = $flesch_score >= 60 ? '' : ($flesch_score >= 40 ? 'warning' : 'bad');
        $score_label = $flesch_score >= 60 ? 'Goed leesbaar' : ($flesch_score >= 40 ? 'Redelijk leesbaar' : 'Moeilijk leesbaar');
        ?>
        <div class="writgo-readability-score <?php echo $score_class; ?>">
            <div class="writgo-score-circle"><?php echo $flesch_score; ?></div>
            <div class="writgo-score-info">
                <h4>Leesbaarheid Score: <?php echo $score_label; ?></h4>
                <p>Gebaseerd op Flesch Reading Ease (aangepast voor Nederlands). Score 60+ is ideaal voor de meeste lezers.</p>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="writgo-readability-grid">
            <div class="writgo-metric-card <?php echo $word_count >= 300 ? 'good' : 'warning'; ?>">
                <div class="writgo-metric-value"><?php echo number_format($word_count); ?></div>
                <div class="writgo-metric-label">Woorden</div>
            </div>
            <div class="writgo-metric-card">
                <div class="writgo-metric-value"><?php echo $sentence_count; ?></div>
                <div class="writgo-metric-label">Zinnen</div>
            </div>
            <div class="writgo-metric-card">
                <div class="writgo-metric-value"><?php echo $paragraph_count; ?></div>
                <div class="writgo-metric-label">Paragrafen</div>
            </div>
            <div class="writgo-metric-card <?php echo $avg_sentence_length <= 20 ? 'good' : 'warning'; ?>">
                <div class="writgo-metric-value"><?php echo $avg_sentence_length; ?></div>
                <div class="writgo-metric-label">Gem. woorden/zin</div>
            </div>
            <div class="writgo-metric-card">
                <div class="writgo-metric-value"><?php echo $h2_count + $h3_count; ?></div>
                <div class="writgo-metric-label">Subkoppen</div>
            </div>
            <div class="writgo-metric-card">
                <div class="writgo-metric-value"><?php echo $image_count; ?></div>
                <div class="writgo-metric-label">Afbeeldingen</div>
            </div>
        </div>
        
        <!-- Detailed Analysis -->
        <div class="writgo-analysis-section">
            <h4>📋 Gedetailleerde Analyse</h4>
            <ul class="writgo-check-list">
                <!-- Sentence Length -->
                <li>
                    <span class="writgo-check-icon <?php echo $long_sentence_percentage <= 25 ? 'good' : ($long_sentence_percentage <= 40 ? 'warning' : 'bad'); ?>">
                        <?php echo $long_sentence_percentage <= 25 ? '✓' : '!'; ?>
                    </span>
                    <span class="writgo-check-text">
                        <strong>Zinslengte:</strong> <?php echo $long_sentence_percentage; ?>% van je zinnen is langer dan 20 woorden.
                        <?php echo $long_sentence_percentage <= 25 ? 'Prima!' : 'Probeer zinnen korter te maken.'; ?>
                    </span>
                </li>
                
                <!-- Transition Words -->
                <li>
                    <span class="writgo-check-icon <?php echo $transition_percentage >= 30 ? 'good' : ($transition_percentage >= 20 ? 'warning' : 'bad'); ?>">
                        <?php echo $transition_percentage >= 30 ? '✓' : '!'; ?>
                    </span>
                    <span class="writgo-check-text">
                        <strong>Overgangswoorden:</strong> <?php echo $transition_percentage; ?>% van je zinnen bevat overgangswoorden.
                        <?php echo $transition_percentage >= 30 ? 'Goed!' : 'Voeg meer overgangswoorden toe (daarom, echter, bovendien).'; ?>
                    </span>
                </li>
                
                <!-- Passive Voice -->
                <li>
                    <span class="writgo-check-icon <?php echo $passive_count <= 3 ? 'good' : ($passive_count <= 6 ? 'warning' : 'bad'); ?>">
                        <?php echo $passive_count <= 3 ? '✓' : '!'; ?>
                    </span>
                    <span class="writgo-check-text">
                        <strong>Passieve zinnen:</strong> <?php echo $passive_count; ?> gevonden.
                        <?php echo $passive_count <= 3 ? 'Prima!' : 'Probeer actiever te schrijven.'; ?>
                    </span>
                </li>
                
                <!-- Paragraph Length -->
                <li>
                    <span class="writgo-check-icon <?php echo $avg_paragraph_length <= 150 ? 'good' : 'warning'; ?>">
                        <?php echo $avg_paragraph_length <= 150 ? '✓' : '!'; ?>
                    </span>
                    <span class="writgo-check-text">
                        <strong>Paragraaflengte:</strong> Gemiddeld <?php echo $avg_paragraph_length; ?> woorden per paragraaf.
                        <?php echo $avg_paragraph_length <= 150 ? 'Goed!' : 'Overweeg kortere paragrafen.'; ?>
                    </span>
                </li>
                
                <!-- Subheading Distribution -->
                <li>
                    <span class="writgo-check-icon <?php echo ($word_count / max(1, $h2_count + $h3_count)) <= 300 ? 'good' : 'warning'; ?>">
                        <?php echo ($word_count / max(1, $h2_count + $h3_count)) <= 300 ? '✓' : '!'; ?>
                    </span>
                    <span class="writgo-check-text">
                        <strong>Subkoppen verdeling:</strong> <?php echo round($word_count / max(1, $h2_count + $h3_count)); ?> woorden per sectie.
                        <?php echo ($word_count / max(1, $h2_count + $h3_count)) <= 300 ? 'Prima!' : 'Voeg meer subkoppen toe om de tekst op te breken.'; ?>
                    </span>
                </li>
                
                <!-- Images -->
                <li>
                    <span class="writgo-check-icon <?php echo $image_count >= 1 ? 'good' : 'warning'; ?>">
                        <?php echo $image_count >= 1 ? '✓' : '!'; ?>
                    </span>
                    <span class="writgo-check-text">
                        <strong>Afbeeldingen:</strong> <?php echo $image_count; ?> afbeelding(en) in de content.
                        <?php echo $image_count >= 1 ? 'Goed!' : 'Voeg minimaal één afbeelding toe.'; ?>
                    </span>
                </li>
                
                <!-- Internal Links -->
                <li>
                    <span class="writgo-check-icon <?php echo $internal_links >= 2 ? 'good' : ($internal_links >= 1 ? 'warning' : 'bad'); ?>">
                        <?php echo $internal_links >= 2 ? '✓' : '!'; ?>
                    </span>
                    <span class="writgo-check-text">
                        <strong>Interne links:</strong> <?php echo $internal_links; ?> link(s) naar andere pagina's op je site.
                        <?php echo $internal_links >= 2 ? 'Goed!' : 'Voeg meer interne links toe.'; ?>
                    </span>
                </li>
                
                <!-- External Links -->
                <li>
                    <span class="writgo-check-icon <?php echo $external_links >= 1 ? 'good' : 'warning'; ?>">
                        <?php echo $external_links >= 1 ? '✓' : '!'; ?>
                    </span>
                    <span class="writgo-check-text">
                        <strong>Externe links:</strong> <?php echo $external_links; ?> link(s) naar externe bronnen.
                        <?php echo $external_links >= 1 ? 'Goed voor betrouwbaarheid!' : 'Overweeg bronvermelding toe te voegen.'; ?>
                    </span>
                </li>
            </ul>
        </div>
        
        <!-- Secondary Keywords -->
        <div class="writgo-secondary-keywords">
            <label for="writgo_secondary_keywords">🔑 Secundaire Keywords (synoniemen)</label>
            <input type="text" 
                   id="writgo_secondary_keywords" 
                   name="writgo_secondary_keywords" 
                   value="<?php echo esc_attr($secondary_keywords); ?>" 
                   placeholder="keyword1, keyword2, keyword3">
            <p class="writgo-tip">Voeg gerelateerde zoektermen toe, gescheiden door komma's. Deze helpen bij de SEO analyse.</p>
        </div>
        
        <!-- Cornerstone Content -->
        <div class="writgo-cornerstone">
            <label>
                <input type="checkbox" name="writgo_cornerstone" value="1" <?php checked($cornerstone, '1'); ?>>
                ⭐ Dit is <strong>Cornerstone Content</strong> (belangrijkste artikel over dit onderwerp)
            </label>
        </div>
    </div>
    <?php
}

// Save readability meta
add_action('save_post', 'writgo_readability_save_meta');
function writgo_readability_save_meta($post_id) {
    if (!isset($_POST['writgo_readability_nonce']) || !wp_verify_nonce($_POST['writgo_readability_nonce'], 'writgo_readability_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    if (isset($_POST['writgo_secondary_keywords'])) {
        update_post_meta($post_id, '_writgo_secondary_keywords', sanitize_text_field($_POST['writgo_secondary_keywords']));
    }
    
    update_post_meta($post_id, '_writgo_cornerstone', isset($_POST['writgo_cornerstone']) ? '1' : '');
}

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

/**
 * Count syllables in text (Dutch approximation)
 */
function writgo_count_syllables($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z]/', ' ', $text);
    $words = explode(' ', $text);
    
    $syllables = 0;
    foreach ($words as $word) {
        if (strlen($word) < 2) continue;
        
        // Count vowel groups
        $word = preg_replace('/[^aeiouyàáâãäåèéêëìíîïòóôõöùúûüý]/', '', $word);
        $count = max(1, strlen(preg_replace('/(.)\1+/', '$1', $word)));
        
        // Adjust for common Dutch patterns
        if (preg_match('/(ij|ei|au|ou|eu|oe|ie|ui)/', $word)) {
            $count = max(1, $count - 1);
        }
        
        $syllables += $count;
    }
    
    return max(1, $syllables);
}

// =============================================================================
// ENHANCED SEO CHECKS (Add to main SEO analysis)
// =============================================================================

/**
 * Get advanced SEO checks for a post
 */
function writgo_get_advanced_seo_checks($post_id) {
    $post = get_post($post_id);
    $content = $post->post_content;
    $text = wp_strip_all_tags($content);
    $title = $post->post_title;
    $url = get_post_field('post_name', $post_id);
    
    $focus_keyword = get_post_meta($post_id, '_writgo_focus_keyword', true);
    $secondary_keywords = get_post_meta($post_id, '_writgo_secondary_keywords', true);
    
    $checks = array();
    
    if ($focus_keyword) {
        $keyword_lower = strtolower($focus_keyword);
        
        // Keyword in URL
        $checks['keyword_in_url'] = array(
            'status' => strpos(strtolower($url), str_replace(' ', '-', $keyword_lower)) !== false,
            'label' => 'Focus keyword in URL',
        );
        
        // Keyword in H1
        preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $content, $h1_matches);
        $h1_text = $h1_matches[1] ?? $title;
        $checks['keyword_in_h1'] = array(
            'status' => strpos(strtolower($h1_text), $keyword_lower) !== false,
            'label' => 'Focus keyword in H1',
        );
        
        // Keyword in first paragraph
        $first_para = '';
        if (preg_match('/<p[^>]*>(.*?)<\/p>/is', $content, $p_matches)) {
            $first_para = strip_tags($p_matches[1]);
        } else {
            $first_para = substr($text, 0, 300);
        }
        $checks['keyword_in_intro'] = array(
            'status' => strpos(strtolower($first_para), $keyword_lower) !== false,
            'label' => 'Focus keyword in eerste alinea',
        );
        
        // Keyword in image alt
        preg_match_all('/alt=["\']([^"\']*)["\']/', $content, $alt_matches);
        $alts_with_keyword = 0;
        foreach ($alt_matches[1] as $alt) {
            if (strpos(strtolower($alt), $keyword_lower) !== false) {
                $alts_with_keyword++;
            }
        }
        $checks['keyword_in_alt'] = array(
            'status' => $alts_with_keyword > 0,
            'label' => 'Focus keyword in afbeelding alt-tekst',
        );
    }
    
    // Content freshness
    $modified = get_the_modified_date('U', $post_id);
    $days_since_update = (time() - $modified) / DAY_IN_SECONDS;
    $checks['content_fresh'] = array(
        'status' => $days_since_update < 180,
        'label' => 'Content recent bijgewerkt (< 6 maanden)',
    );
    
    // Has featured image
    $checks['has_featured_image'] = array(
        'status' => has_post_thumbnail($post_id),
        'label' => 'Uitgelichte afbeelding ingesteld',
    );
    
    return $checks;
}
