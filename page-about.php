<?php
/**
 * Template Name: Over Ons / About
 * 
 * Multilingual About page template
 *
 * @package Writgo_Affiliate
 */

get_header();

$lang = writgo_get_language();

// Default translations for About page
$about_defaults = array(
    'hero_title' => array(
        'nl' => 'Over Ons',
        'en' => 'About Us',
        'de' => 'Über Uns',
        'fr' => 'À Propos',
    ),
    'hero_subtitle' => array(
        'nl' => 'Wij helpen je de beste keuzes te maken met eerlijke reviews en vergelijkingen',
        'en' => 'We help you make the best choices with honest reviews and comparisons',
        'de' => 'Wir helfen Ihnen, die besten Entscheidungen mit ehrlichen Bewertungen und Vergleichen zu treffen',
        'fr' => 'Nous vous aidons à faire les meilleurs choix avec des avis honnêtes et des comparaisons',
    ),
    'intro_label' => array(
        'nl' => 'Wie zijn wij',
        'en' => 'Who we are',
        'de' => 'Wer wir sind',
        'fr' => 'Qui sommes-nous',
    ),
    'intro_title' => array(
        'nl' => 'Onze Missie',
        'en' => 'Our Mission',
        'de' => 'Unsere Mission',
        'fr' => 'Notre Mission',
    ),
    'intro_text' => array(
        'nl' => 'Wij geloven dat iedereen toegang verdient tot eerlijke, onafhankelijke informatie. In een wereld vol gesponsorde content en misleidende marketing, streven wij naar transparantie.

Ons team van experts test en vergelijkt producten grondig, zodat jij met vertrouwen de beste keuze kunt maken.',
        'en' => 'We believe everyone deserves access to honest, independent information. In a world full of sponsored content and misleading marketing, we strive for transparency.

Our team of experts thoroughly tests and compares products, so you can make the best choice with confidence.',
        'de' => 'Wir glauben, dass jeder Zugang zu ehrlichen, unabhängigen Informationen verdient. In einer Welt voller gesponserter Inhalte und irreführendem Marketing streben wir nach Transparenz.

Unser Expertenteam testet und vergleicht Produkte gründlich, damit Sie mit Vertrauen die beste Wahl treffen können.',
        'fr' => 'Nous croyons que chacun mérite un accès à des informations honnêtes et indépendantes. Dans un monde plein de contenu sponsorisé et de marketing trompeur, nous visons la transparence.

Notre équipe d\'experts teste et compare soigneusement les produits, afin que vous puissiez faire le meilleur choix en toute confiance.',
    ),
    'story_label' => array(
        'nl' => 'Ons verhaal',
        'en' => 'Our story',
        'de' => 'Unsere Geschichte',
        'fr' => 'Notre histoire',
    ),
    'story_title' => array(
        'nl' => 'Hoe het begon',
        'en' => 'How it started',
        'de' => 'Wie alles begann',
        'fr' => 'Comment tout a commencé',
    ),
    'story_text' => array(
        'nl' => 'Het begon met een simpele frustratie: het vinden van betrouwbare productinformatie was veel te moeilijk.

Daarom besloten we het anders te doen. Met een passie voor eerlijke journalistiek, begonnen we met het schrijven van diepgaande, onafhankelijke reviews.',
        'en' => 'It started with a simple frustration: finding reliable product information was way too difficult.

So we decided to do things differently. With a passion for honest journalism, we started writing in-depth, independent reviews.',
        'de' => 'Es begann mit einer einfachen Frustration: Zuverlässige Produktinformationen zu finden war viel zu schwierig.

Also beschlossen wir, es anders zu machen. Mit einer Leidenschaft für ehrlichen Journalismus begannen wir, ausführliche, unabhängige Bewertungen zu schreiben.',
        'fr' => 'Tout a commencé par une simple frustration : trouver des informations fiables sur les produits était beaucoup trop difficile.

Nous avons donc décidé de faire les choses différemment. Avec une passion pour le journalisme honnête, nous avons commencé à écrire des avis approfondis et indépendants.',
    ),
    'team_title' => array(
        'nl' => 'Ons Team',
        'en' => 'Our Team',
        'de' => 'Unser Team',
        'fr' => 'Notre Équipe',
    ),
    'team_subtitle' => array(
        'nl' => 'De mensen achter de reviews',
        'en' => 'The people behind the reviews',
        'de' => 'Die Menschen hinter den Bewertungen',
        'fr' => 'Les personnes derrière les avis',
    ),
    'process_title' => array(
        'nl' => 'Onze Werkwijze',
        'en' => 'Our Process',
        'de' => 'Unser Prozess',
        'fr' => 'Notre Processus',
    ),
    'process_subtitle' => array(
        'nl' => 'Hoe wij reviews maken',
        'en' => 'How we create reviews',
        'de' => 'Wie wir Bewertungen erstellen',
        'fr' => 'Comment nous créons nos avis',
    ),
    'process1_title' => array(
        'nl' => 'Onderzoek',
        'en' => 'Research',
        'de' => 'Recherche',
        'fr' => 'Recherche',
    ),
    'process1_text' => array(
        'nl' => 'We beginnen met uitgebreid marktonderzoek en verzamelen specificaties.',
        'en' => 'We start with extensive market research and gather specifications.',
        'de' => 'Wir beginnen mit umfangreicher Marktforschung und sammeln Spezifikationen.',
        'fr' => 'Nous commençons par une étude de marché approfondie et rassemblons les spécifications.',
    ),
    'process2_title' => array(
        'nl' => 'Testen',
        'en' => 'Testing',
        'de' => 'Testen',
        'fr' => 'Tests',
    ),
    'process2_text' => array(
        'nl' => 'Elk product wordt grondig getest in real-world scenarios.',
        'en' => 'Each product is thoroughly tested in real-world scenarios.',
        'de' => 'Jedes Produkt wird gründlich in realen Szenarien getestet.',
        'fr' => 'Chaque produit est testé minutieusement dans des scénarios réels.',
    ),
    'process3_title' => array(
        'nl' => 'Vergelijken',
        'en' => 'Compare',
        'de' => 'Vergleichen',
        'fr' => 'Comparer',
    ),
    'process3_text' => array(
        'nl' => 'We vergelijken prestaties en prijs-kwaliteit met concurrenten.',
        'en' => 'We compare performance and value with competitors.',
        'de' => 'Wir vergleichen Leistung und Preis-Leistung mit Wettbewerbern.',
        'fr' => 'Nous comparons les performances et le rapport qualité-prix avec les concurrents.',
    ),
    'process4_title' => array(
        'nl' => 'Publiceren',
        'en' => 'Publish',
        'de' => 'Veröffentlichen',
        'fr' => 'Publier',
    ),
    'process4_text' => array(
        'nl' => 'Onze bevindingen worden vertaald naar heldere reviews.',
        'en' => 'Our findings are translated into clear reviews.',
        'de' => 'Unsere Ergebnisse werden in klare Bewertungen übersetzt.',
        'fr' => 'Nos conclusions sont traduites en avis clairs.',
    ),
    'values_label' => array(
        'nl' => 'Waar we voor staan',
        'en' => 'What we stand for',
        'de' => 'Wofür wir stehen',
        'fr' => 'Ce que nous défendons',
    ),
    'values_title' => array(
        'nl' => 'Onze Kernwaarden',
        'en' => 'Our Core Values',
        'de' => 'Unsere Grundwerte',
        'fr' => 'Nos Valeurs Fondamentales',
    ),
    'value1_title' => array(
        'nl' => 'Onafhankelijk',
        'en' => 'Independent',
        'de' => 'Unabhängig',
        'fr' => 'Indépendant',
    ),
    'value1_text' => array(
        'nl' => 'Onze reviews zijn 100% onafhankelijk. Onze mening is niet te koop.',
        'en' => 'Our reviews are 100% independent. Our opinion is not for sale.',
        'de' => 'Unsere Bewertungen sind 100% unabhängig. Unsere Meinung ist nicht käuflich.',
        'fr' => 'Nos avis sont 100% indépendants. Notre opinion n\'est pas à vendre.',
    ),
    'value2_title' => array(
        'nl' => 'Betrouwbaar',
        'en' => 'Reliable',
        'de' => 'Zuverlässig',
        'fr' => 'Fiable',
    ),
    'value2_text' => array(
        'nl' => 'Alle informatie wordt zorgvuldig gecontroleerd en regelmatig bijgewerkt.',
        'en' => 'All information is carefully verified and regularly updated.',
        'de' => 'Alle Informationen werden sorgfältig überprüft und regelmäßig aktualisiert.',
        'fr' => 'Toutes les informations sont soigneusement vérifiées et régulièrement mises à jour.',
    ),
    'value3_title' => array(
        'nl' => 'Toegankelijk',
        'en' => 'Accessible',
        'de' => 'Zugänglich',
        'fr' => 'Accessible',
    ),
    'value3_text' => array(
        'nl' => 'Complexe informatie maken wij begrijpelijk voor iedereen.',
        'en' => 'We make complex information understandable for everyone.',
        'de' => 'Wir machen komplexe Informationen für jeden verständlich.',
        'fr' => 'Nous rendons les informations complexes compréhensibles pour tous.',
    ),
    'faq_label' => array(
        'nl' => 'FAQ',
        'en' => 'FAQ',
        'de' => 'FAQ',
        'fr' => 'FAQ',
    ),
    'faq_title' => array(
        'nl' => 'Veelgestelde Vragen',
        'en' => 'Frequently Asked Questions',
        'de' => 'Häufig Gestellte Fragen',
        'fr' => 'Questions Fréquentes',
    ),
    'faq1_q' => array(
        'nl' => 'Hoe verdienen jullie geld?',
        'en' => 'How do you make money?',
        'de' => 'Wie verdienen Sie Geld?',
        'fr' => 'Comment gagnez-vous de l\'argent?',
    ),
    'faq1_a' => array(
        'nl' => 'Wij verdienen een kleine commissie wanneer je een product koopt via onze links. Dit heeft geen invloed op onze beoordelingen.',
        'en' => 'We earn a small commission when you buy a product through our links. This does not affect our ratings.',
        'de' => 'Wir verdienen eine kleine Provision, wenn Sie ein Produkt über unsere Links kaufen. Dies hat keinen Einfluss auf unsere Bewertungen.',
        'fr' => 'Nous gagnons une petite commission lorsque vous achetez un produit via nos liens. Cela n\'affecte pas nos évaluations.',
    ),
    'faq2_q' => array(
        'nl' => 'Zijn jullie reviews echt onafhankelijk?',
        'en' => 'Are your reviews really independent?',
        'de' => 'Sind Ihre Bewertungen wirklich unabhängig?',
        'fr' => 'Vos avis sont-ils vraiment indépendants?',
    ),
    'faq2_a' => array(
        'nl' => 'Absoluut. Merken hebben geen invloed op onze conclusies. Als een product niet goed is, zeggen we dat eerlijk.',
        'en' => 'Absolutely. Brands have no influence on our conclusions. If a product isn\'t good, we say so honestly.',
        'de' => 'Absolut. Marken haben keinen Einfluss auf unsere Schlussfolgerungen. Wenn ein Produkt nicht gut ist, sagen wir das ehrlich.',
        'fr' => 'Absolument. Les marques n\'ont aucune influence sur nos conclusions. Si un produit n\'est pas bon, nous le disons honnêtement.',
    ),
    'faq3_q' => array(
        'nl' => 'Hoe vaak worden reviews bijgewerkt?',
        'en' => 'How often are reviews updated?',
        'de' => 'Wie oft werden Bewertungen aktualisiert?',
        'fr' => 'À quelle fréquence les avis sont-ils mis à jour?',
    ),
    'faq3_a' => array(
        'nl' => 'We herzien onze reviews minimaal elk kwartaal en direct wanneer er belangrijke updates uitkomen.',
        'en' => 'We revise our reviews at least quarterly and immediately when important updates are released.',
        'de' => 'Wir überarbeiten unsere Bewertungen mindestens vierteljährlich und sofort bei wichtigen Updates.',
        'fr' => 'Nous révisons nos avis au moins chaque trimestre et immédiatement lors de mises à jour importantes.',
    ),
    'cta_title' => array(
        'nl' => 'Klaar om te beginnen?',
        'en' => 'Ready to get started?',
        'de' => 'Bereit loszulegen?',
        'fr' => 'Prêt à commencer?',
    ),
    'cta_text' => array(
        'nl' => 'Ontdek onze nieuwste reviews en vind het perfecte product voor jou.',
        'en' => 'Discover our latest reviews and find the perfect product for you.',
        'de' => 'Entdecken Sie unsere neuesten Bewertungen und finden Sie das perfekte Produkt für Sie.',
        'fr' => 'Découvrez nos derniers avis et trouvez le produit parfait pour vous.',
    ),
    'cta_button' => array(
        'nl' => 'Bekijk Reviews',
        'en' => 'View Reviews',
        'de' => 'Bewertungen Ansehen',
        'fr' => 'Voir les Avis',
    ),
);

// Helper function to get translated default
function get_about_text($key, $defaults, $lang) {
    $customizer_value = get_theme_mod('writgo_about_' . $key, '');
    
    // Get Dutch default to compare
    $dutch_default = isset($defaults[$key]['nl']) ? $defaults[$key]['nl'] : '';
    
    // Use translation if empty or if it matches Dutch default
    if (empty($customizer_value) || $customizer_value === $dutch_default) {
        return isset($defaults[$key][$lang]) ? $defaults[$key][$lang] : ($defaults[$key]['en'] ?? '');
    }
    
    return $customizer_value;
}

// Get all values
$hero_title = get_about_text('hero_title', $about_defaults, $lang);
$hero_subtitle = get_about_text('hero_subtitle', $about_defaults, $lang);
$hero_image = get_theme_mod('writgo_about_hero_image', '');

$show_intro = get_theme_mod('writgo_about_intro_show', true);
$intro_label = get_about_text('intro_label', $about_defaults, $lang);
$intro_title = get_about_text('intro_title', $about_defaults, $lang);
$intro_text = get_about_text('intro_text', $about_defaults, $lang);
$intro_image = get_theme_mod('writgo_about_intro_image', '');

$show_story = get_theme_mod('writgo_about_story_show', true);
$story_label = get_about_text('story_label', $about_defaults, $lang);
$story_title = get_about_text('story_title', $about_defaults, $lang);
$story_text = get_about_text('story_text', $about_defaults, $lang);

$show_team = get_theme_mod('writgo_about_team_show', true);
$team_title = get_about_text('team_title', $about_defaults, $lang);
$team_subtitle = get_about_text('team_subtitle', $about_defaults, $lang);

$show_process = get_theme_mod('writgo_about_process_show', true);
$process_title = get_about_text('process_title', $about_defaults, $lang);
$process_subtitle = get_about_text('process_subtitle', $about_defaults, $lang);

$show_stats = get_theme_mod('writgo_about_stats_show', true);
$show_values = get_theme_mod('writgo_about_values_show', true);
$values_title = get_about_text('values_title', $about_defaults, $lang);

$show_faq = get_theme_mod('writgo_about_faq_show', true);
$faq_title = get_about_text('faq_title', $about_defaults, $lang);

$show_cta = get_theme_mod('writgo_about_cta_show', true);
$cta_title = get_about_text('cta_title', $about_defaults, $lang);
$cta_text = get_about_text('cta_text', $about_defaults, $lang);
$cta_button = get_about_text('cta_button', $about_defaults, $lang);
$cta_url = get_theme_mod('writgo_about_cta_url', home_url('/'));
?>

<main class="wa-about-page">

    <!-- Hero Section -->
    <section class="wa-about-hero" <?php if ($hero_image) echo 'style="background-image: url(' . esc_url($hero_image) . ');"'; ?>>
        <div class="wa-about-hero-overlay"></div>
        <div class="wa-container">
            <div class="wa-about-hero-content">
                <h1 class="wa-about-hero-title"><?php echo esc_html($hero_title); ?></h1>
                <p class="wa-about-hero-subtitle"><?php echo esc_html($hero_subtitle); ?></p>
            </div>
        </div>
    </section>

    <?php if ($show_intro) : ?>
    <!-- Intro/Mission Section -->
    <section class="wa-about-intro">
        <div class="wa-container">
            <div class="wa-about-intro-grid <?php echo $intro_image ? '' : 'wa-single-column'; ?>">
                <div class="wa-about-intro-content">
                    <span class="wa-about-label"><?php echo esc_html($intro_label); ?></span>
                    <h2 class="wa-about-intro-title"><?php echo esc_html($intro_title); ?></h2>
                    <div class="wa-about-intro-text">
                        <?php echo wp_kses_post(nl2br($intro_text)); ?>
                    </div>
                </div>
                
                <?php if ($intro_image) : ?>
                <div class="wa-about-intro-image">
                    <img src="<?php echo esc_url($intro_image); ?>" alt="<?php echo esc_attr($intro_title); ?>">
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_story) : ?>
    <!-- Story Section -->
    <section class="wa-about-story">
        <div class="wa-container">
            <div class="wa-about-story-content">
                <span class="wa-about-label"><?php echo esc_html($story_label); ?></span>
                <h2 class="wa-about-story-title"><?php echo esc_html($story_title); ?></h2>
                <div class="wa-about-story-text">
                    <?php echo wp_kses_post(nl2br($story_text)); ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_stats) : ?>
    <!-- Stats Section -->
    <section class="wa-about-stats">
        <div class="wa-container">
            <div class="wa-stats-grid">
                <div class="wa-stat-item">
                    <span class="wa-stat-number"><?php echo esc_html(get_theme_mod('writgo_about_stat1_number', '500+')); ?></span>
                    <span class="wa-stat-label"><?php echo esc_html(get_theme_mod('writgo_about_stat1_label', '') ?: writgo_t('articles_written')); ?></span>
                </div>
                <div class="wa-stat-item">
                    <span class="wa-stat-number"><?php echo esc_html(get_theme_mod('writgo_about_stat2_number', '100+')); ?></span>
                    <span class="wa-stat-label"><?php echo esc_html(get_theme_mod('writgo_about_stat2_label', '') ?: writgo_t('reviews')); ?></span>
                </div>
                <div class="wa-stat-item">
                    <span class="wa-stat-number"><?php echo esc_html(get_theme_mod('writgo_about_stat3_number', '50K+')); ?></span>
                    <span class="wa-stat-label"><?php echo esc_html(get_theme_mod('writgo_about_stat3_label', '') ?: writgo_t('happy_readers')); ?></span>
                </div>
                <div class="wa-stat-item">
                    <span class="wa-stat-number"><?php echo esc_html(get_theme_mod('writgo_about_stat4_number', '5+')); ?></span>
                    <span class="wa-stat-label"><?php echo esc_html(get_theme_mod('writgo_about_stat4_label', '') ?: writgo_t('years_experience')); ?></span>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_process) : ?>
    <!-- Process Section -->
    <section class="wa-about-process">
        <div class="wa-container">
            <div class="wa-about-process-header">
                <h2 class="wa-about-process-title"><?php echo esc_html($process_title); ?></h2>
                <p class="wa-about-process-subtitle"><?php echo esc_html($process_subtitle); ?></p>
            </div>
            
            <div class="wa-process-grid">
                <div class="wa-process-step">
                    <span class="wa-process-icon"><?php echo esc_html(get_theme_mod('writgo_about_process1_icon', '🔍')); ?></span>
                    <span class="wa-process-number">1</span>
                    <h3 class="wa-process-title"><?php echo esc_html(get_about_text('process1_title', $about_defaults, $lang)); ?></h3>
                    <p class="wa-process-text"><?php echo esc_html(get_about_text('process1_text', $about_defaults, $lang)); ?></p>
                </div>
                
                <div class="wa-process-step">
                    <span class="wa-process-icon"><?php echo esc_html(get_theme_mod('writgo_about_process2_icon', '🧪')); ?></span>
                    <span class="wa-process-number">2</span>
                    <h3 class="wa-process-title"><?php echo esc_html(get_about_text('process2_title', $about_defaults, $lang)); ?></h3>
                    <p class="wa-process-text"><?php echo esc_html(get_about_text('process2_text', $about_defaults, $lang)); ?></p>
                </div>
                
                <div class="wa-process-step">
                    <span class="wa-process-icon"><?php echo esc_html(get_theme_mod('writgo_about_process3_icon', '📊')); ?></span>
                    <span class="wa-process-number">3</span>
                    <h3 class="wa-process-title"><?php echo esc_html(get_about_text('process3_title', $about_defaults, $lang)); ?></h3>
                    <p class="wa-process-text"><?php echo esc_html(get_about_text('process3_text', $about_defaults, $lang)); ?></p>
                </div>
                
                <div class="wa-process-step">
                    <span class="wa-process-icon"><?php echo esc_html(get_theme_mod('writgo_about_process4_icon', '✍️')); ?></span>
                    <span class="wa-process-number">4</span>
                    <h3 class="wa-process-title"><?php echo esc_html(get_about_text('process4_title', $about_defaults, $lang)); ?></h3>
                    <p class="wa-process-text"><?php echo esc_html(get_about_text('process4_text', $about_defaults, $lang)); ?></p>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_team) : ?>
    <!-- Team Section -->
    <section class="wa-about-team">
        <div class="wa-container">
            <div class="wa-about-team-header">
                <h2 class="wa-about-team-title"><?php echo esc_html($team_title); ?></h2>
                <p class="wa-about-team-subtitle"><?php echo esc_html($team_subtitle); ?></p>
            </div>
            
            <div class="wa-team-grid">
                <?php for ($i = 1; $i <= 3; $i++) : 
                    $name = get_theme_mod("writgo_about_team{$i}_name", '');
                    $role = get_theme_mod("writgo_about_team{$i}_role", '');
                    $bio = get_theme_mod("writgo_about_team{$i}_bio", '');
                    $image = get_theme_mod("writgo_about_team{$i}_image", '');
                    if ($name) :
                ?>
                <div class="wa-team-member">
                    <div class="wa-team-avatar">
                        <?php if ($image) : ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($name); ?>">
                        <?php else : ?>
                            <span class="wa-team-initials"><?php echo esc_html(mb_substr($name, 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="wa-team-name"><?php echo esc_html($name); ?></h3>
                    <span class="wa-team-role"><?php echo esc_html($role); ?></span>
                    <p class="wa-team-bio"><?php echo esc_html($bio); ?></p>
                </div>
                <?php endif; endfor; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_values) : ?>
    <!-- Values Section -->
    <section class="wa-about-values">
        <div class="wa-container">
            <div class="wa-about-values-header">
                <span class="wa-about-label"><?php echo esc_html(get_about_text('values_label', $about_defaults, $lang)); ?></span>
                <h2 class="wa-about-values-title"><?php echo esc_html($values_title); ?></h2>
            </div>
            
            <div class="wa-values-grid">
                <div class="wa-value-card">
                    <span class="wa-value-icon"><?php echo esc_html(get_theme_mod('writgo_about_value1_icon', '🎯')); ?></span>
                    <h3 class="wa-value-title"><?php echo esc_html(get_about_text('value1_title', $about_defaults, $lang)); ?></h3>
                    <p class="wa-value-text"><?php echo esc_html(get_about_text('value1_text', $about_defaults, $lang)); ?></p>
                </div>
                
                <div class="wa-value-card">
                    <span class="wa-value-icon"><?php echo esc_html(get_theme_mod('writgo_about_value2_icon', '✅')); ?></span>
                    <h3 class="wa-value-title"><?php echo esc_html(get_about_text('value2_title', $about_defaults, $lang)); ?></h3>
                    <p class="wa-value-text"><?php echo esc_html(get_about_text('value2_text', $about_defaults, $lang)); ?></p>
                </div>
                
                <div class="wa-value-card">
                    <span class="wa-value-icon"><?php echo esc_html(get_theme_mod('writgo_about_value3_icon', '💡')); ?></span>
                    <h3 class="wa-value-title"><?php echo esc_html(get_about_text('value3_title', $about_defaults, $lang)); ?></h3>
                    <p class="wa-value-text"><?php echo esc_html(get_about_text('value3_text', $about_defaults, $lang)); ?></p>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_faq) : ?>
    <!-- FAQ Section -->
    <section class="wa-about-faq">
        <div class="wa-container">
            <div class="wa-about-faq-header">
                <span class="wa-about-label"><?php echo esc_html(get_about_text('faq_label', $about_defaults, $lang)); ?></span>
                <h2 class="wa-about-faq-title"><?php echo esc_html($faq_title); ?></h2>
            </div>
            
            <div class="wa-faq-list">
                <details class="wa-faq-item">
                    <summary class="wa-faq-question"><?php echo esc_html(get_about_text('faq1_q', $about_defaults, $lang)); ?></summary>
                    <div class="wa-faq-answer"><?php echo wp_kses_post(get_about_text('faq1_a', $about_defaults, $lang)); ?></div>
                </details>
                
                <details class="wa-faq-item">
                    <summary class="wa-faq-question"><?php echo esc_html(get_about_text('faq2_q', $about_defaults, $lang)); ?></summary>
                    <div class="wa-faq-answer"><?php echo wp_kses_post(get_about_text('faq2_a', $about_defaults, $lang)); ?></div>
                </details>
                
                <details class="wa-faq-item">
                    <summary class="wa-faq-question"><?php echo esc_html(get_about_text('faq3_q', $about_defaults, $lang)); ?></summary>
                    <div class="wa-faq-answer"><?php echo wp_kses_post(get_about_text('faq3_a', $about_defaults, $lang)); ?></div>
                </details>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php 
    // Show page content from WordPress editor if available
    if (have_posts()) : while (have_posts()) : the_post();
        if (trim(get_the_content())) :
    ?>
    <section class="wa-about-custom-content">
        <div class="wa-container">
            <div class="wa-about-custom-inner">
                <?php the_content(); ?>
            </div>
        </div>
    </section>
    <?php 
        endif;
    endwhile; endif; 
    ?>

    <?php if ($show_cta) : ?>
    <!-- CTA Section -->
    <section class="wa-about-cta">
        <div class="wa-container">
            <div class="wa-about-cta-content">
                <h2 class="wa-about-cta-title"><?php echo esc_html($cta_title); ?></h2>
                <p class="wa-about-cta-text"><?php echo esc_html($cta_text); ?></p>
                <a href="<?php echo esc_url($cta_url ?: home_url('/')); ?>" class="wa-about-cta-button">
                    <?php echo esc_html($cta_button); ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
