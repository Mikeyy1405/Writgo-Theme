<?php
/**
 * Template Name: Contact Pagina
 * 
 * Professional contact page with form, info blocks, FAQ and social links
 *
 * @package Writgo_Affiliate
 */

get_header();

// Get contact settings
$company_name = get_theme_mod('writgo_company_name', get_bloginfo('name'));
$company_address = get_theme_mod('writgo_company_address', '');
$company_postcode = get_theme_mod('writgo_company_postcode', '');
$company_city = get_theme_mod('writgo_company_city', '');
$contact_email = get_theme_mod('writgo_contact_email', '');
$contact_phone = get_theme_mod('writgo_contact_phone', '');
$kvk_nummer = get_theme_mod('writgo_kvk_nummer', '');

// Contact page specific settings
$contact_hero_title = get_theme_mod('writgo_contact_hero_title', 'Neem Contact Op');
$contact_hero_subtitle = get_theme_mod('writgo_contact_hero_subtitle', 'Heb je een vraag, feedback of wil je samenwerken? We horen graag van je!');
$contact_response_time = get_theme_mod('writgo_contact_response_time', 'We reageren meestal binnen 24-48 uur');
$contact_form_shortcode = get_theme_mod('writgo_contact_form_shortcode', '');

// FAQ items
$faq_items = array(
    array(
        'question' => get_theme_mod('writgo_contact_faq1_q', 'Hoe snel krijg ik antwoord?'),
        'answer'   => get_theme_mod('writgo_contact_faq1_a', 'We proberen alle berichten binnen 24-48 uur te beantwoorden. In drukke periodes kan dit iets langer duren.'),
    ),
    array(
        'question' => get_theme_mod('writgo_contact_faq2_q', 'Kan ik samenwerken met jullie?'),
        'answer'   => get_theme_mod('writgo_contact_faq2_a', 'Ja! We staan open voor samenwerkingen. Stuur ons een bericht met je voorstel en we nemen zo snel mogelijk contact op.'),
    ),
    array(
        'question' => get_theme_mod('writgo_contact_faq3_q', 'Ik heb een fout gevonden op de website'),
        'answer'   => get_theme_mod('writgo_contact_faq3_a', 'Bedankt dat je dit wilt melden! Stuur ons een bericht met de details en we lossen het zo snel mogelijk op.'),
    ),
);

// Social media
$facebook = get_theme_mod('writgo_social_facebook', '');
$instagram = get_theme_mod('writgo_social_instagram', '');
$twitter = get_theme_mod('writgo_social_twitter', '');
$linkedin = get_theme_mod('writgo_social_linkedin', '');
$youtube = get_theme_mod('writgo_social_youtube', '');
?>

<main class="wa-contact-page">
    
    <!-- Hero Section -->
    <section class="wa-contact-hero">
        <div class="wa-container">
            <div class="wa-contact-hero-content">
                <h1 class="wa-contact-hero-title"><?php echo esc_html($contact_hero_title); ?></h1>
                <p class="wa-contact-hero-subtitle"><?php echo esc_html($contact_hero_subtitle); ?></p>
            </div>
        </div>
    </section>
    
    <!-- Main Content -->
    <section class="wa-contact-main">
        <div class="wa-container">
            <div class="wa-contact-grid">
                
                <!-- Left: Contact Form -->
                <div class="wa-contact-form-section">
                    <div class="wa-contact-card">
                        <div class="wa-contact-card-header">
                            <span class="wa-contact-card-icon">✉️</span>
                            <h2>Stuur een bericht</h2>
                        </div>
                        
                        <div class="wa-contact-card-body">
                            <?php if (isset($_GET['contact']) && $_GET['contact'] === 'success') : ?>
                                <div class="wa-contact-message wa-contact-success">
                                    <span class="wa-message-icon">✅</span>
                                    <div>
                                        <strong>Bedankt voor je bericht!</strong>
                                        <p>We hebben je bericht ontvangen en nemen zo snel mogelijk contact met je op.</p>
                                    </div>
                                </div>
                            <?php elseif (isset($_GET['contact']) && $_GET['contact'] === 'error') : ?>
                                <div class="wa-contact-message wa-contact-error">
                                    <span class="wa-message-icon">❌</span>
                                    <div>
                                        <strong>Er ging iets mis</strong>
                                        <p>Je bericht kon niet worden verzonden. Probeer het opnieuw of stuur een e-mail.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($contact_form_shortcode) : ?>
                                <!-- Use configured form plugin -->
                                <?php echo do_shortcode($contact_form_shortcode); ?>
                            <?php else : ?>
                                <!-- Default HTML form -->
                                <form class="wa-contact-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                                    <input type="hidden" name="action" value="writgo_contact_form">
                                    <?php wp_nonce_field('writgo_contact_form', 'writgo_contact_nonce'); ?>
                                    
                                    <div class="wa-form-row wa-form-row-2">
                                        <div class="wa-form-group">
                                            <label for="contact_name">Naam <span class="required">*</span></label>
                                            <input type="text" id="contact_name" name="contact_name" required placeholder="Je volledige naam">
                                        </div>
                                        <div class="wa-form-group">
                                            <label for="contact_email">E-mail <span class="required">*</span></label>
                                            <input type="email" id="contact_email" name="contact_email" required placeholder="je@email.nl">
                                        </div>
                                    </div>
                                    
                                    <div class="wa-form-group">
                                        <label for="contact_subject">Onderwerp</label>
                                        <select id="contact_subject" name="contact_subject">
                                            <option value="algemeen">Algemene vraag</option>
                                            <option value="samenwerking">Samenwerking</option>
                                            <option value="feedback">Feedback</option>
                                            <option value="fout">Fout melden</option>
                                            <option value="anders">Anders</option>
                                        </select>
                                    </div>
                                    
                                    <div class="wa-form-group">
                                        <label for="contact_message">Bericht <span class="required">*</span></label>
                                        <textarea id="contact_message" name="contact_message" rows="6" required placeholder="Schrijf hier je bericht..."></textarea>
                                    </div>
                                    
                                    <div class="wa-form-group wa-form-checkbox">
                                        <label>
                                            <input type="checkbox" name="contact_privacy" required>
                                            <span>Ik ga akkoord met de <a href="<?php echo home_url('/privacyverklaring/'); ?>" target="_blank">privacyverklaring</a></span>
                                        </label>
                                    </div>
                                    
                                    <button type="submit" class="wa-contact-submit">
                                        <span>Verstuur bericht</span>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                                        </svg>
                                    </button>
                                </form>
                                
                                <p class="wa-form-note">
                                    <strong>💡 Tip:</strong> Installeer <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7</a> of <a href="https://wordpress.org/plugins/wpforms-lite/" target="_blank">WPForms</a> voor een uitgebreider formulier met e-mail notificaties.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Contact Info -->
                <div class="wa-contact-info-section">
                    
                    <!-- Contact Details Card -->
                    <div class="wa-contact-card wa-contact-details-card">
                        <div class="wa-contact-card-header">
                            <span class="wa-contact-card-icon">📋</span>
                            <h2>Contactgegevens</h2>
                        </div>
                        
                        <div class="wa-contact-card-body">
                            <ul class="wa-contact-details-list">
                                <?php if ($company_name) : ?>
                                    <li>
                                        <div class="wa-contact-detail-icon">🏠</div>
                                        <div class="wa-contact-detail-content">
                                            <strong><?php echo esc_html($company_name); ?></strong>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($company_address || $company_city) : ?>
                                    <li>
                                        <div class="wa-contact-detail-icon">📍</div>
                                        <div class="wa-contact-detail-content">
                                            <?php if ($company_address) : ?>
                                                <span><?php echo esc_html($company_address); ?></span><br>
                                            <?php endif; ?>
                                            <?php if ($company_postcode || $company_city) : ?>
                                                <span><?php echo esc_html(trim($company_postcode . ' ' . $company_city)); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($contact_email) : ?>
                                    <li>
                                        <div class="wa-contact-detail-icon">📧</div>
                                        <div class="wa-contact-detail-content">
                                            <a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($contact_phone) : ?>
                                    <li>
                                        <div class="wa-contact-detail-icon">📞</div>
                                        <div class="wa-contact-detail-content">
                                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $contact_phone)); ?>"><?php echo esc_html($contact_phone); ?></a>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($kvk_nummer) : ?>
                                    <li>
                                        <div class="wa-contact-detail-icon">🏢</div>
                                        <div class="wa-contact-detail-content">
                                            <span>KvK: <?php echo esc_html($kvk_nummer); ?></span>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            
                            <?php if ($contact_response_time) : ?>
                                <div class="wa-contact-response-time">
                                    <span class="wa-response-icon">⏱️</span>
                                    <span><?php echo esc_html($contact_response_time); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Social Media Card -->
                    <?php if ($facebook || $instagram || $twitter || $linkedin || $youtube) : ?>
                        <div class="wa-contact-card wa-contact-social-card">
                            <div class="wa-contact-card-header">
                                <span class="wa-contact-card-icon">🌐</span>
                                <h2>Volg ons</h2>
                            </div>
                            
                            <div class="wa-contact-card-body">
                                <p>Blijf op de hoogte via onze social media kanalen.</p>
                                <div class="wa-contact-social-links">
                                    <?php if ($facebook) : ?>
                                        <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener noreferrer" class="wa-social-btn wa-social-facebook">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                            <span>Facebook</span>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($instagram) : ?>
                                        <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener noreferrer" class="wa-social-btn wa-social-instagram">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                            <span>Instagram</span>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($twitter) : ?>
                                        <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener noreferrer" class="wa-social-btn wa-social-twitter">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                            <span>Twitter/X</span>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($linkedin) : ?>
                                        <a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener noreferrer" class="wa-social-btn wa-social-linkedin">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                            <span>LinkedIn</span>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($youtube) : ?>
                                        <a href="<?php echo esc_url($youtube); ?>" target="_blank" rel="noopener noreferrer" class="wa-social-btn wa-social-youtube">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                            <span>YouTube</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
            </div>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <?php 
    $show_faq = false;
    foreach ($faq_items as $faq) {
        if (!empty($faq['question']) && !empty($faq['answer'])) {
            $show_faq = true;
            break;
        }
    }
    ?>
    
    <?php if ($show_faq) : ?>
        <section class="wa-contact-faq">
            <div class="wa-container">
                <div class="wa-contact-faq-header">
                    <h2>Veelgestelde vragen</h2>
                    <p>Misschien vind je hier al het antwoord op je vraag</p>
                </div>
                
                <div class="wa-contact-faq-grid">
                    <?php foreach ($faq_items as $faq) : ?>
                        <?php if (!empty($faq['question']) && !empty($faq['answer'])) : ?>
                            <div class="wa-contact-faq-item">
                                <details>
                                    <summary>
                                        <span class="wa-faq-icon">❓</span>
                                        <span class="wa-faq-question"><?php echo esc_html($faq['question']); ?></span>
                                        <span class="wa-faq-toggle">+</span>
                                    </summary>
                                    <div class="wa-faq-answer">
                                        <?php echo wp_kses_post($faq['answer']); ?>
                                    </div>
                                </details>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
    
    <!-- Trust Section -->
    <section class="wa-contact-trust">
        <div class="wa-container">
            <div class="wa-contact-trust-grid">
                <div class="wa-contact-trust-item">
                    <span class="wa-trust-icon-large">🔒</span>
                    <h3>Privacy Gegarandeerd</h3>
                    <p>Je gegevens worden veilig verwerkt volgens onze privacyverklaring</p>
                </div>
                <div class="wa-contact-trust-item">
                    <span class="wa-trust-icon-large">⚡</span>
                    <h3>Snelle Reactie</h3>
                    <p>We reageren meestal binnen 24-48 uur op je bericht</p>
                </div>
                <div class="wa-contact-trust-item">
                    <span class="wa-trust-icon-large">💬</span>
                    <h3>Persoonlijk Contact</h3>
                    <p>Geen standaard antwoorden, maar persoonlijke hulp</p>
                </div>
            </div>
        </div>
    </section>
    
</main>

<?php get_footer(); ?>
