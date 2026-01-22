<?php
/**
 * Template Name: Over Ons
 * 
 * Complete About page template with Hero, Intro, Story, Team, Process, Stats, Values, FAQ and CTA
 *
 * @package Writgo_Affiliate
 */

get_header();

// Get Customizer settings - Hero
$hero_title = get_theme_mod('writgo_about_hero_title', 'Over Ons');
$hero_subtitle = get_theme_mod('writgo_about_hero_subtitle', 'Wij helpen je de beste keuzes te maken met eerlijke reviews en vergelijkingen');
$hero_image = get_theme_mod('writgo_about_hero_image', '');

// Intro Section
$show_intro = get_theme_mod('writgo_about_intro_show', true);
$intro_label = get_theme_mod('writgo_about_intro_label', 'Wie zijn wij');
$intro_title = get_theme_mod('writgo_about_intro_title', 'Onze Missie');
$intro_text = get_theme_mod('writgo_about_intro_text', 'Wij geloven dat iedereen toegang verdient tot eerlijke, onafhankelijke informatie. In een wereld vol gesponsorde content en misleidende marketing, streven wij naar transparantie.

Ons team van experts test en vergelijkt producten grondig, zodat jij met vertrouwen de beste keuze kunt maken. Of het nu gaat om technologie, huishoudelijke apparaten of lifestyle producten - wij duiken diep in de materie zodat jij dat niet hoeft te doen.');
$intro_image = get_theme_mod('writgo_about_intro_image', '');

// Story Section
$show_story = get_theme_mod('writgo_about_story_show', true);
$story_label = get_theme_mod('writgo_about_story_label', 'Ons verhaal');
$story_title = get_theme_mod('writgo_about_story_title', 'Hoe het begon');
$story_text = get_theme_mod('writgo_about_story_text', 'Het begon met een simpele frustratie: het vinden van betrouwbare productinformatie was veel te moeilijk. Overal gesponsorde reviews, verborgen affiliate links en misleidende "beste koop" labels.

Daarom besloten we het anders te doen. Met een achtergrond in consumentenonderzoek en een passie voor eerlijke journalistiek, begonnen we met het schrijven van diepgaande, onafhankelijke reviews.

Vandaag de dag helpen we maandelijks duizenden mensen bij het maken van weloverwogen aankoopbeslissingen. En we zijn nog maar net begonnen.');

// Team Section
$show_team = get_theme_mod('writgo_about_team_show', true);
$team_title = get_theme_mod('writgo_about_team_title', 'Ons Team');
$team_subtitle = get_theme_mod('writgo_about_team_subtitle', 'De mensen achter de reviews');

$team1_name = get_theme_mod('writgo_about_team1_name', 'Jan de Vries');
$team1_role = get_theme_mod('writgo_about_team1_role', 'Hoofdredacteur');
$team1_bio = get_theme_mod('writgo_about_team1_bio', '10+ jaar ervaring in consumentenonderzoek en productjournalistiek.');
$team1_image = get_theme_mod('writgo_about_team1_image', '');

$team2_name = get_theme_mod('writgo_about_team2_name', 'Lisa Bakker');
$team2_role = get_theme_mod('writgo_about_team2_role', 'Tech Expert');
$team2_bio = get_theme_mod('writgo_about_team2_bio', 'Gespecialiseerd in elektronica, software en smart home producten.');
$team2_image = get_theme_mod('writgo_about_team2_image', '');

$team3_name = get_theme_mod('writgo_about_team3_name', 'Mark Jansen');
$team3_role = get_theme_mod('writgo_about_team3_role', 'Product Tester');
$team3_bio = get_theme_mod('writgo_about_team3_bio', 'Test dagelijks producten op duurzaamheid, gebruiksgemak en waarde.');
$team3_image = get_theme_mod('writgo_about_team3_image', '');

// Process Section
$show_process = get_theme_mod('writgo_about_process_show', true);
$process_title = get_theme_mod('writgo_about_process_title', 'Onze Werkwijze');
$process_subtitle = get_theme_mod('writgo_about_process_subtitle', 'Hoe wij reviews maken');

$process1_icon = get_theme_mod('writgo_about_process1_icon', '🔍');
$process1_title = get_theme_mod('writgo_about_process1_title', 'Onderzoek');
$process1_text = get_theme_mod('writgo_about_process1_text', 'We beginnen met uitgebreid marktonderzoek en verzamelen specificaties van alle relevante producten.');

$process2_icon = get_theme_mod('writgo_about_process2_icon', '🧪');
$process2_title = get_theme_mod('writgo_about_process2_title', 'Testen');
$process2_text = get_theme_mod('writgo_about_process2_text', 'Elk product wordt grondig getest in real-world scenarios gedurende minimaal 2 weken.');

$process3_icon = get_theme_mod('writgo_about_process3_icon', '📊');
$process3_title = get_theme_mod('writgo_about_process3_title', 'Vergelijken');
$process3_text = get_theme_mod('writgo_about_process3_text', 'We vergelijken prestaties, prijs-kwaliteit en gebruikerservaring met concurrenten.');

$process4_icon = get_theme_mod('writgo_about_process4_icon', '✍️');
$process4_title = get_theme_mod('writgo_about_process4_title', 'Publiceren');
$process4_text = get_theme_mod('writgo_about_process4_text', 'Onze bevindingen worden vertaald naar heldere, bruikbare reviews en koopgidsen.');

// Stats Section
$show_stats = get_theme_mod('writgo_about_stats_show', true);
$stat1_number = get_theme_mod('writgo_about_stat1_number', '500+');
$stat1_label = get_theme_mod('writgo_about_stat1_label', 'Artikelen');
$stat2_number = get_theme_mod('writgo_about_stat2_number', '100+');
$stat2_label = get_theme_mod('writgo_about_stat2_label', 'Reviews');
$stat3_number = get_theme_mod('writgo_about_stat3_number', '50K+');
$stat3_label = get_theme_mod('writgo_about_stat3_label', 'Lezers per maand');
$stat4_number = get_theme_mod('writgo_about_stat4_number', '5+');
$stat4_label = get_theme_mod('writgo_about_stat4_label', 'Jaar ervaring');

// Values Section
$show_values = get_theme_mod('writgo_about_values_show', true);
$values_title = get_theme_mod('writgo_about_values_title', 'Onze Kernwaarden');
$value1_icon = get_theme_mod('writgo_about_value1_icon', '🎯');
$value1_title = get_theme_mod('writgo_about_value1_title', 'Onafhankelijk');
$value1_text = get_theme_mod('writgo_about_value1_text', 'Onze reviews zijn 100% onafhankelijk. Wij laten ons niet beïnvloeden door merken of adverteerders. Onze mening is niet te koop.');
$value2_icon = get_theme_mod('writgo_about_value2_icon', '✅');
$value2_title = get_theme_mod('writgo_about_value2_title', 'Betrouwbaar');
$value2_text = get_theme_mod('writgo_about_value2_text', 'Alle informatie wordt zorgvuldig gecontroleerd en regelmatig bijgewerkt. We staan achter elke aanbeveling die we doen.');
$value3_icon = get_theme_mod('writgo_about_value3_icon', '💡');
$value3_title = get_theme_mod('writgo_about_value3_title', 'Toegankelijk');
$value3_text = get_theme_mod('writgo_about_value3_text', 'Complexe informatie maken wij begrijpelijk voor iedereen, zonder vakjargon. Iedereen verdient goede productinformatie.');

// FAQ Section
$show_faq = get_theme_mod('writgo_about_faq_show', true);
$faq_title = get_theme_mod('writgo_about_faq_title', 'Veelgestelde Vragen');

$faq1_q = get_theme_mod('writgo_about_faq1_q', 'Hoe verdienen jullie geld?');
$faq1_a = get_theme_mod('writgo_about_faq1_a', 'Wij verdienen een kleine commissie wanneer je een product koopt via onze links. Dit heeft geen invloed op onze beoordelingen of aanbevelingen - we raden alleen producten aan waar we echt achter staan.');

$faq2_q = get_theme_mod('writgo_about_faq2_q', 'Zijn jullie reviews echt onafhankelijk?');
$faq2_a = get_theme_mod('writgo_about_faq2_a', 'Absoluut. We kopen zelf producten of lenen ze tijdelijk voor tests. Merken hebben geen invloed op onze conclusies. Als een product niet goed is, zeggen we dat eerlijk.');

$faq3_q = get_theme_mod('writgo_about_faq3_q', 'Hoe vaak worden reviews bijgewerkt?');
$faq3_a = get_theme_mod('writgo_about_faq3_a', 'We herzien onze reviews minimaal elk kwartaal en direct wanneer er belangrijke updates of nieuwe versies uitkomen. Je ziet altijd de datum van laatste update.');

// CTA Section
$show_cta = get_theme_mod('writgo_about_cta_show', true);
$cta_title = get_theme_mod('writgo_about_cta_title', 'Klaar om te beginnen?');
$cta_text = get_theme_mod('writgo_about_cta_text', 'Ontdek onze nieuwste reviews en vind het perfecte product voor jou.');
$cta_button = get_theme_mod('writgo_about_cta_button', 'Bekijk Reviews');
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
                    <span class="wa-stat-number"><?php echo esc_html($stat1_number); ?></span>
                    <span class="wa-stat-label"><?php echo esc_html($stat1_label); ?></span>
                </div>
                <div class="wa-stat-item">
                    <span class="wa-stat-number"><?php echo esc_html($stat2_number); ?></span>
                    <span class="wa-stat-label"><?php echo esc_html($stat2_label); ?></span>
                </div>
                <div class="wa-stat-item">
                    <span class="wa-stat-number"><?php echo esc_html($stat3_number); ?></span>
                    <span class="wa-stat-label"><?php echo esc_html($stat3_label); ?></span>
                </div>
                <div class="wa-stat-item">
                    <span class="wa-stat-number"><?php echo esc_html($stat4_number); ?></span>
                    <span class="wa-stat-label"><?php echo esc_html($stat4_label); ?></span>
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
                <span class="wa-about-label">Zo werken wij</span>
                <h2 class="wa-about-process-title"><?php echo esc_html($process_title); ?></h2>
                <p class="wa-about-process-subtitle"><?php echo esc_html($process_subtitle); ?></p>
            </div>
            
            <div class="wa-process-grid">
                <div class="wa-process-step">
                    <div class="wa-process-number">1</div>
                    <span class="wa-process-icon"><?php echo esc_html($process1_icon); ?></span>
                    <h3 class="wa-process-title"><?php echo esc_html($process1_title); ?></h3>
                    <p class="wa-process-text"><?php echo esc_html($process1_text); ?></p>
                </div>
                
                <div class="wa-process-step">
                    <div class="wa-process-number">2</div>
                    <span class="wa-process-icon"><?php echo esc_html($process2_icon); ?></span>
                    <h3 class="wa-process-title"><?php echo esc_html($process2_title); ?></h3>
                    <p class="wa-process-text"><?php echo esc_html($process2_text); ?></p>
                </div>
                
                <div class="wa-process-step">
                    <div class="wa-process-number">3</div>
                    <span class="wa-process-icon"><?php echo esc_html($process3_icon); ?></span>
                    <h3 class="wa-process-title"><?php echo esc_html($process3_title); ?></h3>
                    <p class="wa-process-text"><?php echo esc_html($process3_text); ?></p>
                </div>
                
                <div class="wa-process-step">
                    <div class="wa-process-number">4</div>
                    <span class="wa-process-icon"><?php echo esc_html($process4_icon); ?></span>
                    <h3 class="wa-process-title"><?php echo esc_html($process4_title); ?></h3>
                    <p class="wa-process-text"><?php echo esc_html($process4_text); ?></p>
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
                <span class="wa-about-label">Maak kennis</span>
                <h2 class="wa-about-team-title"><?php echo esc_html($team_title); ?></h2>
                <p class="wa-about-team-subtitle"><?php echo esc_html($team_subtitle); ?></p>
            </div>
            
            <div class="wa-team-grid">
                <div class="wa-team-member">
                    <div class="wa-team-avatar">
                        <?php if ($team1_image) : ?>
                            <img src="<?php echo esc_url($team1_image); ?>" alt="<?php echo esc_attr($team1_name); ?>">
                        <?php else : ?>
                            <span class="wa-team-initials"><?php echo esc_html(mb_substr($team1_name, 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="wa-team-name"><?php echo esc_html($team1_name); ?></h3>
                    <span class="wa-team-role"><?php echo esc_html($team1_role); ?></span>
                    <p class="wa-team-bio"><?php echo esc_html($team1_bio); ?></p>
                </div>
                
                <div class="wa-team-member">
                    <div class="wa-team-avatar">
                        <?php if ($team2_image) : ?>
                            <img src="<?php echo esc_url($team2_image); ?>" alt="<?php echo esc_attr($team2_name); ?>">
                        <?php else : ?>
                            <span class="wa-team-initials"><?php echo esc_html(mb_substr($team2_name, 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="wa-team-name"><?php echo esc_html($team2_name); ?></h3>
                    <span class="wa-team-role"><?php echo esc_html($team2_role); ?></span>
                    <p class="wa-team-bio"><?php echo esc_html($team2_bio); ?></p>
                </div>
                
                <div class="wa-team-member">
                    <div class="wa-team-avatar">
                        <?php if ($team3_image) : ?>
                            <img src="<?php echo esc_url($team3_image); ?>" alt="<?php echo esc_attr($team3_name); ?>">
                        <?php else : ?>
                            <span class="wa-team-initials"><?php echo esc_html(mb_substr($team3_name, 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="wa-team-name"><?php echo esc_html($team3_name); ?></h3>
                    <span class="wa-team-role"><?php echo esc_html($team3_role); ?></span>
                    <p class="wa-team-bio"><?php echo esc_html($team3_bio); ?></p>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_values) : ?>
    <!-- Values Section -->
    <section class="wa-about-values">
        <div class="wa-container">
            <div class="wa-about-values-header">
                <span class="wa-about-label">Waar we voor staan</span>
                <h2 class="wa-about-values-title"><?php echo esc_html($values_title); ?></h2>
            </div>
            
            <div class="wa-values-grid">
                <div class="wa-value-card">
                    <span class="wa-value-icon"><?php echo esc_html($value1_icon); ?></span>
                    <h3 class="wa-value-title"><?php echo esc_html($value1_title); ?></h3>
                    <p class="wa-value-text"><?php echo esc_html($value1_text); ?></p>
                </div>
                
                <div class="wa-value-card">
                    <span class="wa-value-icon"><?php echo esc_html($value2_icon); ?></span>
                    <h3 class="wa-value-title"><?php echo esc_html($value2_title); ?></h3>
                    <p class="wa-value-text"><?php echo esc_html($value2_text); ?></p>
                </div>
                
                <div class="wa-value-card">
                    <span class="wa-value-icon"><?php echo esc_html($value3_icon); ?></span>
                    <h3 class="wa-value-title"><?php echo esc_html($value3_title); ?></h3>
                    <p class="wa-value-text"><?php echo esc_html($value3_text); ?></p>
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
                <span class="wa-about-label">FAQ</span>
                <h2 class="wa-about-faq-title"><?php echo esc_html($faq_title); ?></h2>
            </div>
            
            <div class="wa-faq-list">
                <details class="wa-faq-item">
                    <summary class="wa-faq-question"><?php echo esc_html($faq1_q); ?></summary>
                    <div class="wa-faq-answer"><?php echo wp_kses_post($faq1_a); ?></div>
                </details>
                
                <details class="wa-faq-item">
                    <summary class="wa-faq-question"><?php echo esc_html($faq2_q); ?></summary>
                    <div class="wa-faq-answer"><?php echo wp_kses_post($faq2_a); ?></div>
                </details>
                
                <details class="wa-faq-item">
                    <summary class="wa-faq-question"><?php echo esc_html($faq3_q); ?></summary>
                    <div class="wa-faq-answer"><?php echo wp_kses_post($faq3_a); ?></div>
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
