<?php
/**
 * Writgo SEO - Advanced Schema Markup
 * Product, Review, FAQ, HowTo, Breadcrumb, Organization Schema
 * @package Writgo_Affiliate
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

// =============================================================================
// SCHEMA META BOX
// =============================================================================

add_action('add_meta_boxes', 'writgo_schema_register_meta_box');
function writgo_schema_register_meta_box() {
    add_meta_box('writgo_schema_meta_box', '📊 Schema Markup (Rich Snippets)', 'writgo_schema_meta_box_callback', 'post', 'normal', 'default');
}

function writgo_schema_meta_box_callback($post) {
    wp_nonce_field('writgo_schema_meta_box', 'writgo_schema_nonce');
    
    $schema_type = get_post_meta($post->ID, '_writgo_schema_type', true) ?: 'article';
    $product_name = get_post_meta($post->ID, '_writgo_product_name', true);
    $product_brand = get_post_meta($post->ID, '_writgo_product_brand', true);
    $product_price = get_post_meta($post->ID, '_writgo_product_price', true);
    $product_currency = get_post_meta($post->ID, '_writgo_product_currency', true) ?: 'EUR';
    $product_availability = get_post_meta($post->ID, '_writgo_product_availability', true) ?: 'InStock';
    $product_condition = get_post_meta($post->ID, '_writgo_product_condition', true) ?: 'NewCondition';
    $product_url = get_post_meta($post->ID, '_writgo_product_url', true);
    $review_rating = get_post_meta($post->ID, '_writgo_review_rating', true);
    $review_pros = get_post_meta($post->ID, '_writgo_review_pros', true);
    $review_cons = get_post_meta($post->ID, '_writgo_review_cons', true);
    $faq_items = get_post_meta($post->ID, '_writgo_faq_items', true) ?: array();
    $howto_time = get_post_meta($post->ID, '_writgo_howto_time', true);
    $howto_cost = get_post_meta($post->ID, '_writgo_howto_cost', true);
    ?>
    
    <style>
        .writgo-schema-box { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .writgo-schema-type-selector { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; padding: 15px; background: #f9fafb; border-radius: 10px; }
        .writgo-schema-type { display: flex; align-items: center; gap: 8px; padding: 12px 18px; background: white; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s; }
        .writgo-schema-type:hover { border-color: #f97316; }
        .writgo-schema-type.active { border-color: #f97316; background: #fff7ed; }
        .writgo-schema-type input { display: none; }
        .writgo-schema-type .icon { font-size: 20px; }
        .writgo-schema-type .label { font-weight: 600; color: #374151; }
        .writgo-schema-section { display: none; padding: 20px; background: #f9fafb; border-radius: 10px; margin-top: 15px; }
        .writgo-schema-section.active { display: block; }
        .writgo-schema-section h4 { margin: 0 0 15px; color: #1f2937; }
        .writgo-schema-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 15px; }
        @media (max-width: 782px) { .writgo-schema-row { grid-template-columns: 1fr; } }
        .writgo-schema-field { display: flex; flex-direction: column; gap: 6px; }
        .writgo-schema-field label { font-weight: 600; font-size: 13px; color: #374151; }
        .writgo-schema-field input, .writgo-schema-field select, .writgo-schema-field textarea { padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .writgo-schema-field input:focus, .writgo-schema-field select:focus, .writgo-schema-field textarea:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
        .writgo-schema-field .hint { font-size: 12px; color: #6b7280; }
        .writgo-faq-items { display: flex; flex-direction: column; gap: 15px; }
        .writgo-faq-item { background: white; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .writgo-faq-item input, .writgo-faq-item textarea { width: 100%; margin-top: 8px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; }
        .writgo-faq-remove { background: #fee2e2; color: #dc2626; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; margin-top: 10px; }
        .writgo-faq-add { background: #f97316; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; margin-top: 10px; }
        .writgo-faq-add:hover { background: #ea580c; }
        .writgo-schema-preview { margin-top: 20px; padding: 15px; background: #ecfdf5; border: 1px solid #10b981; border-radius: 8px; }
        .writgo-schema-preview h5 { margin: 0 0 10px; color: #065f46; }
    </style>
    
    <div class="writgo-schema-box">
        <p style="color: #6b7280; margin: 0 0 15px;">Kies het schema type dat het beste bij je artikel past voor rich snippets in Google.</p>
        
        <div class="writgo-schema-type-selector">
            <label class="writgo-schema-type <?php echo $schema_type === 'article' ? 'active' : ''; ?>">
                <input type="radio" name="writgo_schema_type" value="article" <?php checked($schema_type, 'article'); ?>>
                <span class="icon">📝</span><span class="label">Artikel</span>
            </label>
            <label class="writgo-schema-type <?php echo $schema_type === 'product' ? 'active' : ''; ?>">
                <input type="radio" name="writgo_schema_type" value="product" <?php checked($schema_type, 'product'); ?>>
                <span class="icon">🛍️</span><span class="label">Product Review</span>
            </label>
            <label class="writgo-schema-type <?php echo $schema_type === 'faq' ? 'active' : ''; ?>">
                <input type="radio" name="writgo_schema_type" value="faq" <?php checked($schema_type, 'faq'); ?>>
                <span class="icon">❓</span><span class="label">FAQ</span>
            </label>
            <label class="writgo-schema-type <?php echo $schema_type === 'howto' ? 'active' : ''; ?>">
                <input type="radio" name="writgo_schema_type" value="howto" <?php checked($schema_type, 'howto'); ?>>
                <span class="icon">📋</span><span class="label">How-To</span>
            </label>
            <label class="writgo-schema-type <?php echo $schema_type === 'listicle' ? 'active' : ''; ?>">
                <input type="radio" name="writgo_schema_type" value="listicle" <?php checked($schema_type, 'listicle'); ?>>
                <span class="icon">📊</span><span class="label">Top Lijst</span>
            </label>
        </div>
        
        <!-- Article -->
        <div class="writgo-schema-section <?php echo $schema_type === 'article' ? 'active' : ''; ?>" id="schema-article">
            <h4>📝 Artikel Schema</h4>
            <p style="color: #6b7280;">Wordt automatisch gegenereerd op basis van je post.</p>
        </div>
        
        <!-- Product -->
        <div class="writgo-schema-section <?php echo $schema_type === 'product' ? 'active' : ''; ?>" id="schema-product">
            <h4>🛍️ Product Review Schema</h4>
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
                        <option value="EUR" <?php selected($product_currency, 'EUR'); ?>>EUR (€)</option>
                        <option value="USD" <?php selected($product_currency, 'USD'); ?>>USD ($)</option>
                        <option value="GBP" <?php selected($product_currency, 'GBP'); ?>>GBP (£)</option>
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
                    <label>Beoordeling (1-5) ⭐</label>
                    <input type="number" name="writgo_review_rating" value="<?php echo esc_attr($review_rating); ?>" min="1" max="5" step="0.1" placeholder="4.5">
                    <span class="hint">Toont sterren in Google</span>
                </div>
                <div class="writgo-schema-field">
                    <label>Affiliate URL</label>
                    <input type="url" name="writgo_product_url" value="<?php echo esc_attr($product_url); ?>" placeholder="https://...">
                </div>
            </div>
            <div class="writgo-schema-row">
                <div class="writgo-schema-field">
                    <label>Voordelen (één per regel)</label>
                    <textarea name="writgo_review_pros" rows="3" placeholder="Uitstekende camera&#10;Lange batterijduur"><?php echo esc_textarea($review_pros); ?></textarea>
                </div>
                <div class="writgo-schema-field">
                    <label>Nadelen (één per regel)</label>
                    <textarea name="writgo_review_cons" rows="3" placeholder="Hoge prijs&#10;Geen jack"><?php echo esc_textarea($review_cons); ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- FAQ -->
        <div class="writgo-schema-section <?php echo $schema_type === 'faq' ? 'active' : ''; ?>" id="schema-faq">
            <h4>❓ FAQ Schema</h4>
            <div class="writgo-faq-items" id="faqItems">
                <?php if (!empty($faq_items)) : foreach ($faq_items as $i => $faq) : ?>
                <div class="writgo-faq-item">
                    <label><strong>Vraag <?php echo $i+1; ?></strong></label>
                    <input type="text" name="writgo_faq_question[]" value="<?php echo esc_attr($faq['question'] ?? ''); ?>" placeholder="Vraag...">
                    <label style="margin-top:10px"><strong>Antwoord</strong></label>
                    <textarea name="writgo_faq_answer[]" rows="2" placeholder="Antwoord..."><?php echo esc_textarea($faq['answer'] ?? ''); ?></textarea>
                    <button type="button" class="writgo-faq-remove" onclick="this.parentElement.remove()">✕ Verwijderen</button>
                </div>
                <?php endforeach; endif; ?>
            </div>
            <button type="button" class="writgo-faq-add" onclick="addFaqItem()">+ Vraag Toevoegen</button>
        </div>
        
        <!-- HowTo -->
        <div class="writgo-schema-section <?php echo $schema_type === 'howto' ? 'active' : ''; ?>" id="schema-howto">
            <h4>📋 How-To Schema</h4>
            <p style="color:#6b7280;margin:0 0 15px">Stappen worden automatisch uit H2/H3 koppen gehaald.</p>
            <div class="writgo-schema-row">
                <div class="writgo-schema-field">
                    <label>Geschatte Tijd (minuten)</label>
                    <input type="number" name="writgo_howto_time" value="<?php echo esc_attr($howto_time); ?>" placeholder="30">
                </div>
                <div class="writgo-schema-field">
                    <label>Geschatte Kosten</label>
                    <input type="text" name="writgo_howto_cost" value="<?php echo esc_attr($howto_cost); ?>" placeholder="€50">
                </div>
            </div>
        </div>
        
        <!-- Listicle -->
        <div class="writgo-schema-section <?php echo $schema_type === 'listicle' ? 'active' : ''; ?>" id="schema-listicle">
            <h4>📊 Top Lijst Schema</h4>
            <p style="color:#6b7280">Items worden automatisch uit H2 koppen gehaald.</p>
        </div>
    </div>
    
    <script>
    document.querySelectorAll('.writgo-schema-type input').forEach(function(r){
        r.addEventListener('change',function(){
            document.querySelectorAll('.writgo-schema-type').forEach(t=>t.classList.remove('active'));
            this.parentElement.classList.add('active');
            document.querySelectorAll('.writgo-schema-section').forEach(s=>s.classList.remove('active'));
            document.getElementById('schema-'+this.value).classList.add('active');
        });
    });
    function addFaqItem(){
        var c=document.getElementById('faqItems'),n=c.querySelectorAll('.writgo-faq-item').length+1;
        var d=document.createElement('div');d.className='writgo-faq-item';
        d.innerHTML='<label><strong>Vraag '+n+'</strong></label><input type="text" name="writgo_faq_question[]" placeholder="Vraag..."><label style="margin-top:10px"><strong>Antwoord</strong></label><textarea name="writgo_faq_answer[]" rows="2" placeholder="Antwoord..."></textarea><button type="button" class="writgo-faq-remove" onclick="this.parentElement.remove()">✕ Verwijderen</button>';
        c.appendChild(d);
    }
    </script>
    <?php
}

// Save Schema Meta Box
add_action('save_post', 'writgo_schema_save_meta_box');
function writgo_schema_save_meta_box($post_id) {
    if (!isset($_POST['writgo_schema_nonce']) || !wp_verify_nonce($_POST['writgo_schema_nonce'], 'writgo_schema_meta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    if (isset($_POST['writgo_schema_type'])) update_post_meta($post_id, '_writgo_schema_type', sanitize_text_field($_POST['writgo_schema_type']));
    
    $fields = array('writgo_product_name','writgo_product_brand','writgo_product_price','writgo_product_currency','writgo_product_availability','writgo_product_condition','writgo_product_url','writgo_review_rating','writgo_review_pros','writgo_review_cons','writgo_howto_time','writgo_howto_cost');
    foreach ($fields as $f) { if (isset($_POST[$f])) update_post_meta($post_id, '_'.$f, sanitize_text_field($_POST[$f])); }
    
    if (isset($_POST['writgo_faq_question']) && isset($_POST['writgo_faq_answer'])) {
        $q=$_POST['writgo_faq_question']; $a=$_POST['writgo_faq_answer']; $items=array();
        for ($i=0; $i<count($q); $i++) { if (!empty($q[$i]) && !empty($a[$i])) $items[]=array('question'=>sanitize_text_field($q[$i]),'answer'=>sanitize_textarea_field($a[$i])); }
        update_post_meta($post_id, '_writgo_faq_items', $items);
    }
}

// =============================================================================
// SCHEMA OUTPUT
// =============================================================================

remove_action('wp_head', 'writgo_schema_markup'); // Remove old basic schema

add_action('wp_head', 'writgo_output_advanced_schema', 5);
function writgo_output_advanced_schema() {
    if (!is_singular('post')) return;
    if (defined('WPSEO_VERSION') || class_exists('RankMath')) return;
    
    global $post;
    $type = get_post_meta($post->ID, '_writgo_schema_type', true) ?: 'article';
    
    switch ($type) {
        case 'product': writgo_schema_product($post); break;
        case 'faq': writgo_schema_faq($post); writgo_schema_article($post); break;
        case 'howto': writgo_schema_howto($post); break;
        case 'listicle': writgo_schema_itemlist($post); writgo_schema_article($post); break;
        default: writgo_schema_article($post);
    }
}

function writgo_schema_article($post) {
    $s = array('@context'=>'https://schema.org','@type'=>'Article','headline'=>get_the_title($post),'description'=>has_excerpt($post)?get_the_excerpt($post):wp_trim_words(strip_tags($post->post_content),30),'datePublished'=>get_the_date('c',$post),'dateModified'=>get_the_modified_date('c',$post),'url'=>get_permalink($post),'mainEntityOfPage'=>array('@type'=>'WebPage','@id'=>get_permalink($post)),'author'=>array('@type'=>'Person','name'=>get_the_author_meta('display_name',$post->post_author)),'publisher'=>array('@type'=>'Organization','name'=>get_bloginfo('name'),'logo'=>array('@type'=>'ImageObject','url'=>get_site_icon_url(512)?:'')));
    if (has_post_thumbnail($post)) { $img=wp_get_attachment_image_src(get_post_thumbnail_id($post),'full'); $s['image']=array('@type'=>'ImageObject','url'=>$img[0],'width'=>$img[1],'height'=>$img[2]); }
    $kw=get_post_meta($post->ID,'_writgo_focus_keyword',true); if($kw) $s['keywords']=$kw;
    echo '<script type="application/ld+json">'.wp_json_encode($s,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>'."\n";
}

function writgo_schema_product($post) {
    $name=get_post_meta($post->ID,'_writgo_product_name',true);
    if (empty($name)) { writgo_schema_article($post); return; }
    
    $s=array('@context'=>'https://schema.org','@type'=>'Product','name'=>$name,'description'=>has_excerpt($post)?get_the_excerpt($post):wp_trim_words(strip_tags($post->post_content),50));
    $brand=get_post_meta($post->ID,'_writgo_product_brand',true); if($brand) $s['brand']=array('@type'=>'Brand','name'=>$brand);
    if (has_post_thumbnail($post)) $s['image']=get_the_post_thumbnail_url($post,'large');
    
    $price=get_post_meta($post->ID,'_writgo_product_price',true);
    if ($price) {
        $s['offers']=array('@type'=>'Offer','price'=>$price,'priceCurrency'=>get_post_meta($post->ID,'_writgo_product_currency',true)?:'EUR','availability'=>'https://schema.org/'.(get_post_meta($post->ID,'_writgo_product_availability',true)?:'InStock'),'itemCondition'=>'https://schema.org/'.(get_post_meta($post->ID,'_writgo_product_condition',true)?:'NewCondition'),'url'=>get_post_meta($post->ID,'_writgo_product_url',true)?:get_permalink($post));
    }
    
    $rating=get_post_meta($post->ID,'_writgo_review_rating',true);
    if ($rating) {
        $s['review']=array('@type'=>'Review','reviewRating'=>array('@type'=>'Rating','ratingValue'=>floatval($rating),'bestRating'=>5),'author'=>array('@type'=>'Organization','name'=>get_bloginfo('name')),'datePublished'=>get_the_date('c',$post));
        $pros=get_post_meta($post->ID,'_writgo_review_pros',true);
        if ($pros) { $pa=array_filter(array_map('trim',explode("\n",$pros))); $s['review']['positiveNotes']=array('@type'=>'ItemList','itemListElement'=>array_map(function($p,$i){return array('@type'=>'ListItem','position'=>$i+1,'name'=>$p);},$pa,array_keys($pa))); }
        $cons=get_post_meta($post->ID,'_writgo_review_cons',true);
        if ($cons) { $ca=array_filter(array_map('trim',explode("\n",$cons))); $s['review']['negativeNotes']=array('@type'=>'ItemList','itemListElement'=>array_map(function($c,$i){return array('@type'=>'ListItem','position'=>$i+1,'name'=>$c);},$ca,array_keys($ca))); }
        $s['aggregateRating']=array('@type'=>'AggregateRating','ratingValue'=>floatval($rating),'bestRating'=>5,'reviewCount'=>1);
    }
    echo '<script type="application/ld+json">'.wp_json_encode($s,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>'."\n";
}

function writgo_schema_faq($post) {
    $items=get_post_meta($post->ID,'_writgo_faq_items',true);
    if (empty($items)||!is_array($items)) return;
    $s=array('@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>array());
    foreach ($items as $f) { if(!empty($f['question'])&&!empty($f['answer'])) $s['mainEntity'][]=array('@type'=>'Question','name'=>$f['question'],'acceptedAnswer'=>array('@type'=>'Answer','text'=>$f['answer'])); }
    if (!empty($s['mainEntity'])) echo '<script type="application/ld+json">'.wp_json_encode($s,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>'."\n";
}

function writgo_schema_howto($post) {
    preg_match_all('/<h[23][^>]*>(.*?)<\/h[23]>/si',$post->post_content,$m);
    if (empty($m[1])) { writgo_schema_article($post); return; }
    $s=array('@context'=>'https://schema.org','@type'=>'HowTo','name'=>get_the_title($post),'description'=>has_excerpt($post)?get_the_excerpt($post):wp_trim_words(strip_tags($post->post_content),50),'step'=>array());
    $time=get_post_meta($post->ID,'_writgo_howto_time',true); if($time) $s['totalTime']='PT'.intval($time).'M';
    $cost=get_post_meta($post->ID,'_writgo_howto_cost',true); if($cost) $s['estimatedCost']=array('@type'=>'MonetaryAmount','currency'=>'EUR','value'=>preg_replace('/[^0-9.]/','', $cost));
    if (has_post_thumbnail($post)) $s['image']=get_the_post_thumbnail_url($post,'large');
    foreach ($m[1] as $i=>$step) $s['step'][]=array('@type'=>'HowToStep','position'=>$i+1,'name'=>strip_tags($step),'text'=>strip_tags($step));
    echo '<script type="application/ld+json">'.wp_json_encode($s,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>'."\n";
}

function writgo_schema_itemlist($post) {
    preg_match_all('/<h2[^>]*>(.*?)<\/h2>/si',$post->post_content,$m);
    if (empty($m[1])) return;
    $s=array('@context'=>'https://schema.org','@type'=>'ItemList','name'=>get_the_title($post),'numberOfItems'=>count($m[1]),'itemListElement'=>array());
    foreach ($m[1] as $i=>$item) $s['itemListElement'][]=array('@type'=>'ListItem','position'=>$i+1,'name'=>strip_tags($item),'url'=>get_permalink($post).'#'.sanitize_title($item));
    echo '<script type="application/ld+json">'.wp_json_encode($s,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>'."\n";
}

// =============================================================================
// BREADCRUMB SCHEMA
// =============================================================================

add_action('wp_head', 'writgo_schema_breadcrumb', 6);
function writgo_schema_breadcrumb() {
    if (is_front_page()||is_home()) return;
    if (defined('WPSEO_VERSION')||class_exists('RankMath')) return;
    
    $c=array(); $p=1;
    $c[]=array('@type'=>'ListItem','position'=>$p++,'name'=>'Home','item'=>home_url('/'));
    if (is_singular('post')) {
        $cats=get_the_category();
        if (!empty($cats)) $c[]=array('@type'=>'ListItem','position'=>$p++,'name'=>$cats[0]->name,'item'=>get_category_link($cats[0]->term_id));
        $c[]=array('@type'=>'ListItem','position'=>$p++,'name'=>get_the_title(),'item'=>get_permalink());
    } elseif (is_category()) {
        $c[]=array('@type'=>'ListItem','position'=>$p++,'name'=>single_cat_title('',false),'item'=>get_category_link(get_queried_object_id()));
    } elseif (is_page()) {
        $c[]=array('@type'=>'ListItem','position'=>$p++,'name'=>get_the_title(),'item'=>get_permalink());
    }
    echo '<script type="application/ld+json">'.wp_json_encode(array('@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>$c),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>'."\n";
}

// Note: writgo_breadcrumbs() function is defined in functions.php

// =============================================================================
// WEBSITE & ORGANIZATION SCHEMA
// =============================================================================

add_action('wp_head', 'writgo_schema_sitewide', 1);
function writgo_schema_sitewide() {
    if (!is_front_page()&&!is_home()) return;
    if (defined('WPSEO_VERSION')||class_exists('RankMath')) return;
    
    // WebSite
    $ws=array('@context'=>'https://schema.org','@type'=>'WebSite','name'=>get_bloginfo('name'),'url'=>home_url('/'),'potentialAction'=>array('@type'=>'SearchAction','target'=>array('@type'=>'EntryPoint','urlTemplate'=>home_url('/?s={search_term_string}')),'query-input'=>'required name=search_term_string'));
    $d=get_bloginfo('description'); if($d) $ws['description']=$d;
    echo '<script type="application/ld+json">'.wp_json_encode($ws,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>'."\n";
    
    // Organization
    $org=array('@context'=>'https://schema.org','@type'=>'Organization','name'=>get_bloginfo('name'),'url'=>home_url('/'));
    $logo=get_site_icon_url(512); if($logo) $org['logo']=$logo;
    $socials=array(); foreach(array('facebook','instagram','twitter','linkedin','youtube','pinterest','tiktok') as $n) { $u=get_theme_mod('writgo_social_'.$n); if($u) $socials[]=$u; }
    if (!empty($socials)) $org['sameAs']=$socials;
    echo '<script type="application/ld+json">'.wp_json_encode($org,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>'."\n";
}
