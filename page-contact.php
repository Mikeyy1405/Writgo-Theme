<?php
/**
 * Template Name: Contact / Kontakt
 * 
 * Multilingual Contact page template
 *
 * @package Writgo_Affiliate
 */

get_header();

$lang = writgo_get_language();

// Default translations for Contact page
$contact_defaults = array(
    'hero_title' => array(
        'nl' => 'Neem Contact Op',
        'en' => 'Get In Touch',
        'de' => 'Kontaktieren Sie Uns',
        'fr' => 'Contactez-Nous',
    ),
    'hero_subtitle' => array(
        'nl' => 'Heb je een vraag, feedback of wil je samenwerken? We horen graag van je!',
        'en' => 'Have a question, feedback or want to collaborate? We\'d love to hear from you!',
        'de' => 'Haben Sie eine Frage, Feedback oder möchten Sie zusammenarbeiten? Wir freuen uns von Ihnen zu hören!',
        'fr' => 'Avez-vous une question, un commentaire ou souhaitez-vous collaborer? Nous serions ravis de vous entendre!',
    ),
    'send_message' => array(
        'nl' => 'Stuur een bericht',
        'en' => 'Send a message',
        'de' => 'Nachricht senden',
        'fr' => 'Envoyer un message',
    ),
    'name_label' => array(
        'nl' => 'Naam',
        'en' => 'Name',
        'de' => 'Name',
        'fr' => 'Nom',
    ),
    'name_placeholder' => array(
        'nl' => 'Je volledige naam',
        'en' => 'Your full name',
        'de' => 'Ihr vollständiger Name',
        'fr' => 'Votre nom complet',
    ),
    'email_label' => array(
        'nl' => 'E-mail',
        'en' => 'Email',
        'de' => 'E-Mail',
        'fr' => 'E-mail',
    ),
    'email_placeholder' => array(
        'nl' => 'je@email.nl',
        'en' => 'you@email.com',
        'de' => 'ihre@email.de',
        'fr' => 'vous@email.fr',
    ),
    'subject_label' => array(
        'nl' => 'Onderwerp',
        'en' => 'Subject',
        'de' => 'Betreff',
        'fr' => 'Sujet',
    ),
    'subject_general' => array(
        'nl' => 'Algemene vraag',
        'en' => 'General question',
        'de' => 'Allgemeine Frage',
        'fr' => 'Question générale',
    ),
    'subject_collaboration' => array(
        'nl' => 'Samenwerking',
        'en' => 'Collaboration',
        'de' => 'Zusammenarbeit',
        'fr' => 'Collaboration',
    ),
    'subject_feedback' => array(
        'nl' => 'Feedback',
        'en' => 'Feedback',
        'de' => 'Feedback',
        'fr' => 'Retour d\'information',
    ),
    'subject_error' => array(
        'nl' => 'Fout melden',
        'en' => 'Report error',
        'de' => 'Fehler melden',
        'fr' => 'Signaler une erreur',
    ),
    'subject_other' => array(
        'nl' => 'Anders',
        'en' => 'Other',
        'de' => 'Sonstiges',
        'fr' => 'Autre',
    ),
    'message_label' => array(
        'nl' => 'Bericht',
        'en' => 'Message',
        'de' => 'Nachricht',
        'fr' => 'Message',
    ),
    'message_placeholder' => array(
        'nl' => 'Vertel ons waar je mee kunnen helpen...',
        'en' => 'Tell us how we can help you...',
        'de' => 'Erzählen Sie uns, wie wir Ihnen helfen können...',
        'fr' => 'Dites-nous comment nous pouvons vous aider...',
    ),
    'send_button' => array(
        'nl' => 'Verstuur bericht',
        'en' => 'Send message',
        'de' => 'Nachricht senden',
        'fr' => 'Envoyer le message',
    ),
    'success_title' => array(
        'nl' => 'Bedankt voor je bericht!',
        'en' => 'Thank you for your message!',
        'de' => 'Vielen Dank für Ihre Nachricht!',
        'fr' => 'Merci pour votre message!',
    ),
    'success_text' => array(
        'nl' => 'We hebben je bericht ontvangen en nemen zo snel mogelijk contact met je op.',
        'en' => 'We have received your message and will contact you as soon as possible.',
        'de' => 'Wir haben Ihre Nachricht erhalten und werden uns so schnell wie möglich bei Ihnen melden.',
        'fr' => 'Nous avons reçu votre message et vous contacterons dès que possible.',
    ),
    'error_title' => array(
        'nl' => 'Er ging iets mis',
        'en' => 'Something went wrong',
        'de' => 'Etwas ist schief gelaufen',
        'fr' => 'Une erreur s\'est produite',
    ),
    'error_text' => array(
        'nl' => 'Je bericht kon niet worden verzonden. Probeer het opnieuw of stuur een e-mail.',
        'en' => 'Your message could not be sent. Please try again or send an email.',
        'de' => 'Ihre Nachricht konnte nicht gesendet werden. Bitte versuchen Sie es erneut oder senden Sie eine E-Mail.',
        'fr' => 'Votre message n\'a pas pu être envoyé. Veuillez réessayer ou envoyer un e-mail.',
    ),
    'contact_info' => array(
        'nl' => 'Contactgegevens',
        'en' => 'Contact Information',
        'de' => 'Kontaktdaten',
        'fr' => 'Coordonnées',
    ),
    'response_time' => array(
        'nl' => 'We reageren meestal binnen 24-48 uur',
        'en' => 'We usually respond within 24-48 hours',
        'de' => 'Wir antworten normalerweise innerhalb von 24-48 Stunden',
        'fr' => 'Nous répondons généralement dans les 24-48 heures',
    ),
    'follow_us' => array(
        'nl' => 'Volg ons',
        'en' => 'Follow us',
        'de' => 'Folgen Sie uns',
        'fr' => 'Suivez-nous',
    ),
    'social_text' => array(
        'nl' => 'Blijf op de hoogte via onze social media kanalen.',
        'en' => 'Stay updated via our social media channels.',
        'de' => 'Bleiben Sie über unsere Social-Media-Kanäle auf dem Laufenden.',
        'fr' => 'Restez informé via nos réseaux sociaux.',
    ),
    'faq_title' => array(
        'nl' => 'Veelgestelde vragen',
        'en' => 'Frequently Asked Questions',
        'de' => 'Häufig gestellte Fragen',
        'fr' => 'Questions fréquentes',
    ),
    'faq_subtitle' => array(
        'nl' => 'Misschien vind je hier al het antwoord op je vraag',
        'en' => 'You might find the answer to your question here',
        'de' => 'Vielleicht finden Sie hier bereits die Antwort auf Ihre Frage',
        'fr' => 'Vous trouverez peut-être la réponse à votre question ici',
    ),
    'faq1_q' => array(
        'nl' => 'Hoe snel krijg ik antwoord?',
        'en' => 'How quickly will I get a response?',
        'de' => 'Wie schnell bekomme ich eine Antwort?',
        'fr' => 'Combien de temps pour une réponse?',
    ),
    'faq1_a' => array(
        'nl' => 'We proberen alle berichten binnen 24-48 uur te beantwoorden.',
        'en' => 'We try to respond to all messages within 24-48 hours.',
        'de' => 'Wir versuchen, alle Nachrichten innerhalb von 24-48 Stunden zu beantworten.',
        'fr' => 'Nous essayons de répondre à tous les messages dans les 24 à 48 heures.',
    ),
    'faq2_q' => array(
        'nl' => 'Kan ik samenwerken met jullie?',
        'en' => 'Can I collaborate with you?',
        'de' => 'Kann ich mit Ihnen zusammenarbeiten?',
        'fr' => 'Puis-je collaborer avec vous?',
    ),
    'faq2_a' => array(
        'nl' => 'Ja! We staan open voor samenwerkingen. Stuur ons een bericht met je voorstel.',
        'en' => 'Yes! We are open to collaborations. Send us a message with your proposal.',
        'de' => 'Ja! Wir sind offen für Zusammenarbeit. Senden Sie uns eine Nachricht mit Ihrem Vorschlag.',
        'fr' => 'Oui! Nous sommes ouverts aux collaborations. Envoyez-nous un message avec votre proposition.',
    ),
    'faq3_q' => array(
        'nl' => 'Ik heb een fout gevonden op de website',
        'en' => 'I found an error on the website',
        'de' => 'Ich habe einen Fehler auf der Website gefunden',
        'fr' => 'J\'ai trouvé une erreur sur le site',
    ),
    'faq3_a' => array(
        'nl' => 'Bedankt dat je dit wilt melden! Stuur ons een bericht met de details.',
        'en' => 'Thank you for reporting this! Send us a message with the details.',
        'de' => 'Vielen Dank für die Meldung! Senden Sie uns eine Nachricht mit den Details.',
        'fr' => 'Merci de nous le signaler! Envoyez-nous un message avec les détails.',
    ),
    'privacy_guaranteed' => array(
        'nl' => 'Privacy Gegarandeerd',
        'en' => 'Privacy Guaranteed',
        'de' => 'Datenschutz Garantiert',
        'fr' => 'Confidentialité Garantie',
    ),
    'privacy_text' => array(
        'nl' => 'Je gegevens worden veilig verwerkt volgens onze privacyverklaring',
        'en' => 'Your data is securely processed according to our privacy policy',
        'de' => 'Ihre Daten werden sicher gemäß unserer Datenschutzerklärung verarbeitet',
        'fr' => 'Vos données sont traitées en toute sécurité conformément à notre politique de confidentialité',
    ),
    'fast_response' => array(
        'nl' => 'Snelle Reactie',
        'en' => 'Fast Response',
        'de' => 'Schnelle Antwort',
        'fr' => 'Réponse Rapide',
    ),
    'fast_response_text' => array(
        'nl' => 'We reageren meestal binnen 24-48 uur op je bericht',
        'en' => 'We usually respond to your message within 24-48 hours',
        'de' => 'Wir antworten normalerweise innerhalb von 24-48 Stunden auf Ihre Nachricht',
        'fr' => 'Nous répondons généralement à votre message dans les 24-48 heures',
    ),
    'personal_contact' => array(
        'nl' => 'Persoonlijk Contact',
        'en' => 'Personal Contact',
        'de' => 'Persönlicher Kontakt',
        'fr' => 'Contact Personnel',
    ),
    'personal_contact_text' => array(
        'nl' => 'Geen standaard antwoorden, maar persoonlijke hulp',
        'en' => 'No standard answers, but personal assistance',
        'de' => 'Keine Standardantworten, sondern persönliche Hilfe',
        'fr' => 'Pas de réponses standard, mais une aide personnelle',
    ),
);

// Helper function
function get_contact_text($key, $defaults, $lang) {
    $customizer_value = get_theme_mod('writgo_contact_' . $key, '');
    
    // Get Dutch default to compare
    $dutch_default = isset($defaults[$key]['nl']) ? $defaults[$key]['nl'] : '';
    
    // Use translation if empty or if it matches Dutch default
    if (empty($customizer_value) || $customizer_value === $dutch_default) {
        return isset($defaults[$key][$lang]) ? $defaults[$key][$lang] : ($defaults[$key]['en'] ?? '');
    }
    
    return $customizer_value;
}

// Get contact settings
$company_name = get_theme_mod('writgo_company_name', get_bloginfo('name'));
$company_address = get_theme_mod('writgo_company_address', '');
$company_postcode = get_theme_mod('writgo_company_postcode', '');
$company_city = get_theme_mod('writgo_company_city', '');
$contact_email = get_theme_mod('writgo_contact_email', '');
$contact_phone = get_theme_mod('writgo_contact_phone', '');
$kvk_nummer = get_theme_mod('writgo_kvk_nummer', '');

$contact_form_shortcode = get_theme_mod('writgo_contact_form_shortcode', '');

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
                <h1 class="wa-contact-hero-title"><?php echo esc_html(get_contact_text('hero_title', $contact_defaults, $lang)); ?></h1>
                <p class="wa-contact-hero-subtitle"><?php echo esc_html(get_contact_text('hero_subtitle', $contact_defaults, $lang)); ?></p>
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
                            <h2><?php echo esc_html(get_contact_text('send_message', $contact_defaults, $lang)); ?></h2>
                        </div>
                        
                        <div class="wa-contact-card-body">
                            <?php if (isset($_GET['contact']) && $_GET['contact'] === 'success') : ?>
                                <div class="wa-contact-message wa-contact-success">
                                    <span class="wa-message-icon">✅</span>
                                    <div>
                                        <strong><?php echo esc_html(get_contact_text('success_title', $contact_defaults, $lang)); ?></strong>
                                        <p><?php echo esc_html(get_contact_text('success_text', $contact_defaults, $lang)); ?></p>
                                    </div>
                                </div>
                            <?php elseif (isset($_GET['contact']) && $_GET['contact'] === 'error') : ?>
                                <div class="wa-contact-message wa-contact-error">
                                    <span class="wa-message-icon">❌</span>
                                    <div>
                                        <strong><?php echo esc_html(get_contact_text('error_title', $contact_defaults, $lang)); ?></strong>
                                        <p><?php echo esc_html(get_contact_text('error_text', $contact_defaults, $lang)); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($contact_form_shortcode) : ?>
                                <?php echo do_shortcode($contact_form_shortcode); ?>
                            <?php else : ?>
                                <form class="wa-contact-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                                    <input type="hidden" name="action" value="writgo_contact_form">
                                    <?php wp_nonce_field('writgo_contact_form', 'writgo_contact_nonce'); ?>
                                    
                                    <div class="wa-form-row wa-form-row-2">
                                        <div class="wa-form-group">
                                            <label for="contact_name"><?php echo esc_html(get_contact_text('name_label', $contact_defaults, $lang)); ?> <span class="required">*</span></label>
                                            <input type="text" id="contact_name" name="contact_name" required placeholder="<?php echo esc_attr(get_contact_text('name_placeholder', $contact_defaults, $lang)); ?>">
                                        </div>
                                        <div class="wa-form-group">
                                            <label for="contact_email"><?php echo esc_html(get_contact_text('email_label', $contact_defaults, $lang)); ?> <span class="required">*</span></label>
                                            <input type="email" id="contact_email" name="contact_email" required placeholder="<?php echo esc_attr(get_contact_text('email_placeholder', $contact_defaults, $lang)); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="wa-form-group">
                                        <label for="contact_subject"><?php echo esc_html(get_contact_text('subject_label', $contact_defaults, $lang)); ?></label>
                                        <select id="contact_subject" name="contact_subject">
                                            <option value="algemeen"><?php echo esc_html(get_contact_text('subject_general', $contact_defaults, $lang)); ?></option>
                                            <option value="samenwerking"><?php echo esc_html(get_contact_text('subject_collaboration', $contact_defaults, $lang)); ?></option>
                                            <option value="feedback"><?php echo esc_html(get_contact_text('subject_feedback', $contact_defaults, $lang)); ?></option>
                                            <option value="fout"><?php echo esc_html(get_contact_text('subject_error', $contact_defaults, $lang)); ?></option>
                                            <option value="anders"><?php echo esc_html(get_contact_text('subject_other', $contact_defaults, $lang)); ?></option>
                                        </select>
                                    </div>
                                    
                                    <div class="wa-form-group">
                                        <label for="contact_message"><?php echo esc_html(get_contact_text('message_label', $contact_defaults, $lang)); ?> <span class="required">*</span></label>
                                        <textarea id="contact_message" name="contact_message" rows="6" required placeholder="<?php echo esc_attr(get_contact_text('message_placeholder', $contact_defaults, $lang)); ?>"></textarea>
                                    </div>
                                    
                                    <button type="submit" class="wa-contact-submit">
                                        <span><?php echo esc_html(get_contact_text('send_button', $contact_defaults, $lang)); ?></span>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                                        </svg>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Info Cards -->
                <div class="wa-contact-info-section">
                    
                    <!-- Contact Info Card -->
                    <div class="wa-contact-card wa-contact-info-card">
                        <div class="wa-contact-card-header">
                            <span class="wa-contact-card-icon">📍</span>
                            <h2><?php echo esc_html(get_contact_text('contact_info', $contact_defaults, $lang)); ?></h2>
                        </div>
                        
                        <div class="wa-contact-card-body">
                            <ul class="wa-contact-details">
                                <?php if ($company_name) : ?>
                                    <li>
                                        <span class="wa-detail-icon">🏠</span>
                                        <div class="wa-detail-content">
                                            <strong><?php echo esc_html($company_name); ?></strong>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($company_address || $company_city) : ?>
                                    <li>
                                        <span class="wa-detail-icon">📍</span>
                                        <div class="wa-detail-content">
                                            <?php if ($company_address) echo esc_html($company_address) . '<br>'; ?>
                                            <?php echo esc_html($company_postcode . ' ' . $company_city); ?>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($contact_email) : ?>
                                    <li>
                                        <span class="wa-detail-icon">📧</span>
                                        <div class="wa-detail-content">
                                            <a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($contact_phone) : ?>
                                    <li>
                                        <span class="wa-detail-icon">📞</span>
                                        <div class="wa-detail-content">
                                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $contact_phone)); ?>"><?php echo esc_html($contact_phone); ?></a>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($kvk_nummer) : ?>
                                    <li>
                                        <span class="wa-detail-icon">🏢</span>
                                        <div class="wa-detail-content">
                                            KvK: <?php echo esc_html($kvk_nummer); ?>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            
                            <div class="wa-contact-response-info">
                                <span class="wa-response-icon">⏰</span>
                                <span><?php echo esc_html(get_contact_text('response_time', $contact_defaults, $lang)); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media Card -->
                    <?php if ($facebook || $instagram || $twitter || $linkedin || $youtube) : ?>
                        <div class="wa-contact-card wa-contact-social-card">
                            <div class="wa-contact-card-header">
                                <span class="wa-contact-card-icon">🌐</span>
                                <h2><?php echo esc_html(get_contact_text('follow_us', $contact_defaults, $lang)); ?></h2>
                            </div>
                            
                            <div class="wa-contact-card-body">
                                <p><?php echo esc_html(get_contact_text('social_text', $contact_defaults, $lang)); ?></p>
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
    <section class="wa-contact-faq">
        <div class="wa-container">
            <div class="wa-contact-faq-header">
                <h2><?php echo esc_html(get_contact_text('faq_title', $contact_defaults, $lang)); ?></h2>
                <p><?php echo esc_html(get_contact_text('faq_subtitle', $contact_defaults, $lang)); ?></p>
            </div>
            
            <div class="wa-contact-faq-grid">
                <div class="wa-contact-faq-item">
                    <details>
                        <summary>
                            <span class="wa-faq-icon">❓</span>
                            <span class="wa-faq-question"><?php echo esc_html(get_contact_text('faq1_q', $contact_defaults, $lang)); ?></span>
                            <span class="wa-faq-toggle">+</span>
                        </summary>
                        <div class="wa-faq-answer">
                            <?php echo wp_kses_post(get_contact_text('faq1_a', $contact_defaults, $lang)); ?>
                        </div>
                    </details>
                </div>
                
                <div class="wa-contact-faq-item">
                    <details>
                        <summary>
                            <span class="wa-faq-icon">❓</span>
                            <span class="wa-faq-question"><?php echo esc_html(get_contact_text('faq2_q', $contact_defaults, $lang)); ?></span>
                            <span class="wa-faq-toggle">+</span>
                        </summary>
                        <div class="wa-faq-answer">
                            <?php echo wp_kses_post(get_contact_text('faq2_a', $contact_defaults, $lang)); ?>
                        </div>
                    </details>
                </div>
                
                <div class="wa-contact-faq-item">
                    <details>
                        <summary>
                            <span class="wa-faq-icon">❓</span>
                            <span class="wa-faq-question"><?php echo esc_html(get_contact_text('faq3_q', $contact_defaults, $lang)); ?></span>
                            <span class="wa-faq-toggle">+</span>
                        </summary>
                        <div class="wa-faq-answer">
                            <?php echo wp_kses_post(get_contact_text('faq3_a', $contact_defaults, $lang)); ?>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Trust Section -->
    <section class="wa-contact-trust">
        <div class="wa-container">
            <div class="wa-contact-trust-grid">
                <div class="wa-contact-trust-item">
                    <span class="wa-trust-icon-large">🔒</span>
                    <h3><?php echo esc_html(get_contact_text('privacy_guaranteed', $contact_defaults, $lang)); ?></h3>
                    <p><?php echo esc_html(get_contact_text('privacy_text', $contact_defaults, $lang)); ?></p>
                </div>
                <div class="wa-contact-trust-item">
                    <span class="wa-trust-icon-large">⚡</span>
                    <h3><?php echo esc_html(get_contact_text('fast_response', $contact_defaults, $lang)); ?></h3>
                    <p><?php echo esc_html(get_contact_text('fast_response_text', $contact_defaults, $lang)); ?></p>
                </div>
                <div class="wa-contact-trust-item">
                    <span class="wa-trust-icon-large">💬</span>
                    <h3><?php echo esc_html(get_contact_text('personal_contact', $contact_defaults, $lang)); ?></h3>
                    <p><?php echo esc_html(get_contact_text('personal_contact_text', $contact_defaults, $lang)); ?></p>
                </div>
            </div>
        </div>
    </section>
    
</main>

<?php get_footer(); ?>
