<?php
/**
 * Writgo Affiliate Theme Functions
 *
 * @package Writgo_Affiliate
 * @version 7.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme Constants
define('WRITGO_VERSION', '12.1.0');
define('WRITGO_DIR', get_template_directory());
define('WRITGO_URI', get_template_directory_uri());

// =============================================================================
// HELPER FUNCTIONS (must be defined before includes)
// =============================================================================

/**
 * Sanitize checkbox values for Customizer
 */
if (!function_exists('writgo_sanitize_checkbox')) {
    function writgo_sanitize_checkbox($input) {
        return (isset($input) && $input == true) ? true : false;
    }
}

/**
 * Detect if post is a review or list article based on title
 */
if (!function_exists('writgo_is_review_or_list')) {
    function writgo_is_review_or_list($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $title = strtolower(get_the_title($post_id));

        // Keywords for reviews
        $review_keywords = array('review', 'recensie', 'test', 'ervaring', 'ervaringen');

        // Keywords for lists (with variations)
        $list_keywords = array(
            'beste', 'top', 'tips', 'meest', 'populairste', 'goedkoopste',
            'duurste', 'hoogste', 'laagste', 'vergelijking', 'vergelijk',
            'best', 'most', 'popular', 'cheapest', 'comparison', 'compare'
        );

        // Check for reviews
        foreach ($review_keywords as $keyword) {
            if (strpos($title, $keyword) !== false) {
                return 'review';
            }
        }

        // Check for lists (including variations like "top 5", "beste 10", etc.)
        foreach ($list_keywords as $keyword) {
            if (strpos($title, $keyword) !== false) {
                // Also check for number variations (top 5, top 10, beste 3, etc.)
                if (preg_match('/' . preg_quote($keyword, '/') . '\s*\d+/i', $title) ||
                    preg_match('/\d+\s*' . preg_quote($keyword, '/') . '/i', $title) ||
                    strpos($title, $keyword) !== false) {
                    return 'list';
                }
            }
        }

        return false;
    }
}

/**
 * Get post badge label based on content type
 */
if (!function_exists('writgo_get_post_badge')) {
    function writgo_get_post_badge($post_id = null) {
        $type = writgo_is_review_or_list($post_id);
        $lang = writgo_get_language();

        if ($type === 'review') {
            $labels = array(
                'nl' => 'Review',
                'en' => 'Review',
                'de' => 'Rezension',
                'fr' => 'Test'
            );
            return isset($labels[$lang]) ? $labels[$lang] : 'Review';
        }

        if ($type === 'list') {
            $labels = array(
                'nl' => 'Lijstje',
                'en' => 'List',
                'de' => 'Liste',
                'fr' => 'Liste'
            );
            return isset($labels[$lang]) ? $labels[$lang] : 'Lijstje';
        }

        // Default to Blog
        $labels = array(
            'nl' => 'Blog',
            'en' => 'Blog',
            'de' => 'Blog',
            'fr' => 'Blog'
        );
        return isset($labels[$lang]) ? $labels[$lang] : 'Blog';
    }
}

// Include CTA Boxes module
require_once WRITGO_DIR . '/inc/cta-boxes.php';

// Include Popups & Slide-ins module
require_once WRITGO_DIR . '/inc/popups.php';

// Include Performance Optimization module
require_once WRITGO_DIR . '/inc/performance.php';

// =============================================================================
// MEERTALIGE ONDERSTEUNING
// =============================================================================

/**
 * Get current theme language
 */
function writgo_get_language() {
    return get_theme_mod('writgo_language', 'nl');
}

/**
 * Get translated string
 * Usage: writgo_t('read_more') or writgo_t('minutes_read', 5)
 */
function writgo_t($key, $var = null) {
    $lang = writgo_get_language();
    
    $translations = array(
        // General
        'home' => array(
            'nl' => 'Home',
            'en' => 'Home',
            'de' => 'Startseite',
            'fr' => 'Accueil',
        ),
        'search' => array(
            'nl' => 'Zoeken...',
            'en' => 'Search...',
            'de' => 'Suchen...',
            'fr' => 'Rechercher...',
        ),
        'search_button' => array(
            'nl' => 'Zoeken',
            'en' => 'Search',
            'de' => 'Suchen',
            'fr' => 'Rechercher',
        ),
        'search_articles' => array(
            'nl' => 'Zoek in artikelen...',
            'en' => 'Search articles...',
            'de' => 'Artikel suchen...',
            'fr' => 'Rechercher des articles...',
        ),
        'search_results' => array(
            'nl' => 'Zoekresultaten voor',
            'en' => 'Search results for',
            'de' => 'Suchergebnisse für',
            'fr' => 'Résultats de recherche pour',
        ),
        'no_results' => array(
            'nl' => 'Geen resultaten gevonden',
            'en' => 'No results found',
            'de' => 'Keine Ergebnisse gefunden',
            'fr' => 'Aucun résultat trouvé',
        ),
        'read_more' => array(
            'nl' => 'Lees meer',
            'en' => 'Read more',
            'de' => 'Weiterlesen',
            'fr' => 'Lire la suite',
        ),
        
        // Article
        'minutes_read' => array(
            'nl' => '%d min leestijd',
            'en' => '%d min read',
            'de' => '%d Min. Lesezeit',
            'fr' => '%d min de lecture',
        ),
        'table_of_contents' => array(
            'nl' => 'Inhoudsopgave',
            'en' => 'Table of Contents',
            'de' => 'Inhaltsverzeichnis',
            'fr' => 'Table des matières',
        ),
        'written_by' => array(
            'nl' => 'Geschreven door',
            'en' => 'Written by',
            'de' => 'Geschrieben von',
            'fr' => 'Écrit par',
        ),
        'updated' => array(
            'nl' => 'Bijgewerkt',
            'en' => 'Updated',
            'de' => 'Aktualisiert',
            'fr' => 'Mis à jour',
        ),
        'tags' => array(
            'nl' => 'Tags:',
            'en' => 'Tags:',
            'de' => 'Schlagwörter:',
            'fr' => 'Étiquettes:',
        ),
        'related_articles' => array(
            'nl' => 'Gerelateerde artikelen',
            'en' => 'Related articles',
            'de' => 'Ähnliche Artikel',
            'fr' => 'Articles similaires',
        ),
        'latest_articles' => array(
            'nl' => 'Laatste artikelen',
            'en' => 'Latest articles',
            'de' => 'Neueste Artikel',
            'fr' => 'Derniers articles',
        ),
        'blog' => array(
            'nl' => 'Blog',
            'en' => 'Blog',
            'de' => 'Blog',
            'fr' => 'Blog',
        ),
        
        // Archive
        'category' => array(
            'nl' => 'Categorie',
            'en' => 'Category',
            'de' => 'Kategorie',
            'fr' => 'Catégorie',
        ),
        'articles_in' => array(
            'nl' => 'artikelen in',
            'en' => 'articles in',
            'de' => 'Artikel in',
            'fr' => 'articles dans',
        ),
        'all_articles' => array(
            'nl' => 'Alle artikelen',
            'en' => 'All articles',
            'de' => 'Alle Artikel',
            'fr' => 'Tous les articles',
        ),
        'latest_articles' => array(
            'nl' => 'Laatste artikelen',
            'en' => 'Latest articles',
            'de' => 'Neueste Artikel',
            'fr' => 'Derniers articles',
        ),
        
        // 404
        'page_not_found' => array(
            'nl' => 'Pagina niet gevonden',
            'en' => 'Page not found',
            'de' => 'Seite nicht gefunden',
            'fr' => 'Page non trouvée',
        ),
        'error_404' => array(
            'nl' => 'Oeps! Deze pagina bestaat niet.',
            'en' => 'Oops! This page doesn\'t exist.',
            'de' => 'Hoppla! Diese Seite existiert nicht.',
            'fr' => 'Oups ! Cette page n\'existe pas.',
        ),
        'back_to_home' => array(
            'nl' => 'Terug naar home',
            'en' => 'Back to home',
            'de' => 'Zurück zur Startseite',
            'fr' => 'Retour à l\'accueil',
        ),
        
        // Footer
        'about_us' => array(
            'nl' => 'Over ons',
            'en' => 'About us',
            'de' => 'Über uns',
            'fr' => 'À propos',
        ),
        'quick_links' => array(
            'nl' => 'Snelle links',
            'en' => 'Quick links',
            'de' => 'Schnelllinks',
            'fr' => 'Liens rapides',
        ),
        'legal' => array(
            'nl' => 'Juridisch',
            'en' => 'Legal',
            'de' => 'Rechtliches',
            'fr' => 'Mentions légales',
        ),
        'privacy_policy' => array(
            'nl' => 'Privacyverklaring',
            'en' => 'Privacy Policy',
            'de' => 'Datenschutzerklärung',
            'fr' => 'Politique de confidentialité',
        ),
        'disclaimer' => array(
            'nl' => 'Disclaimer',
            'en' => 'Disclaimer',
            'de' => 'Haftungsausschluss',
            'fr' => 'Avertissement',
        ),
        'cookie_policy' => array(
            'nl' => 'Cookiebeleid',
            'en' => 'Cookie Policy',
            'de' => 'Cookie-Richtlinie',
            'fr' => 'Politique de cookies',
        ),
        'terms' => array(
            'nl' => 'Algemene Voorwaarden',
            'en' => 'Terms & Conditions',
            'de' => 'AGB',
            'fr' => 'Conditions générales',
        ),
        'contact' => array(
            'nl' => 'Contact',
            'en' => 'Contact',
            'de' => 'Kontakt',
            'fr' => 'Contact',
        ),
        'all_rights_reserved' => array(
            'nl' => 'Alle rechten voorbehouden',
            'en' => 'All rights reserved',
            'de' => 'Alle Rechte vorbehalten',
            'fr' => 'Tous droits réservés',
        ),
        
        // Affiliate
        'affiliate_disclosure' => array(
            'nl' => 'Dit artikel kan affiliate links bevatten. Bij aankoop via deze links ontvangen wij een commissie.',
            'en' => 'This article may contain affiliate links. We may earn a commission from purchases made through these links.',
            'de' => 'Dieser Artikel kann Affiliate-Links enthalten. Bei einem Kauf über diese Links erhalten wir eine Provision.',
            'fr' => 'Cet article peut contenir des liens affiliés. Nous pouvons recevoir une commission pour les achats effectués via ces liens.',
        ),
        'footer_disclosure' => array(
            'nl' => 'Deze website bevat affiliate links. Bij aankoop ontvangen wij een kleine commissie, zonder extra kosten voor jou.',
            'en' => 'This website contains affiliate links. When you make a purchase, we receive a small commission at no extra cost to you.',
            'de' => 'Diese Website enthält Affiliate-Links. Bei einem Kauf erhalten wir eine kleine Provision, ohne zusätzliche Kosten für Sie.',
            'fr' => 'Ce site contient des liens affiliés. Lors d\'un achat, nous recevons une petite commission sans frais supplémentaires pour vous.',
        ),
        'view_deal' => array(
            'nl' => 'Bekijk deal',
            'en' => 'View deal',
            'de' => 'Zum Angebot',
            'fr' => 'Voir l\'offre',
        ),
        'check_price' => array(
            'nl' => 'Bekijk prijs',
            'en' => 'Check price',
            'de' => 'Preis prüfen',
            'fr' => 'Voir le prix',
        ),
        'best_price' => array(
            'nl' => 'Beste prijs',
            'en' => 'Best price',
            'de' => 'Bester Preis',
            'fr' => 'Meilleur prix',
        ),
        'our_choice' => array(
            'nl' => 'Onze keuze',
            'en' => 'Our choice',
            'de' => 'Unsere Wahl',
            'fr' => 'Notre choix',
        ),
        'pros' => array(
            'nl' => 'Voordelen',
            'en' => 'Pros',
            'de' => 'Vorteile',
            'fr' => 'Avantages',
        ),
        'cons' => array(
            'nl' => 'Nadelen',
            'en' => 'Cons',
            'de' => 'Nachteile',
            'fr' => 'Inconvénients',
        ),
        
        // Contact Form
        'your_name' => array(
            'nl' => 'Je naam',
            'en' => 'Your name',
            'de' => 'Ihr Name',
            'fr' => 'Votre nom',
        ),
        'your_email' => array(
            'nl' => 'Je e-mailadres',
            'en' => 'Your email',
            'de' => 'Ihre E-Mail',
            'fr' => 'Votre e-mail',
        ),
        'subject' => array(
            'nl' => 'Onderwerp',
            'en' => 'Subject',
            'de' => 'Betreff',
            'fr' => 'Sujet',
        ),
        'message' => array(
            'nl' => 'Bericht',
            'en' => 'Message',
            'de' => 'Nachricht',
            'fr' => 'Message',
        ),
        'send_message' => array(
            'nl' => 'Verstuur bericht',
            'en' => 'Send message',
            'de' => 'Nachricht senden',
            'fr' => 'Envoyer',
        ),
        'message_sent' => array(
            'nl' => 'Bedankt! Je bericht is verzonden.',
            'en' => 'Thank you! Your message has been sent.',
            'de' => 'Vielen Dank! Ihre Nachricht wurde gesendet.',
            'fr' => 'Merci ! Votre message a été envoyé.',
        ),
        
        // SEO
        'seo_score' => array(
            'nl' => 'SEO Score',
            'en' => 'SEO Score',
            'de' => 'SEO-Bewertung',
            'fr' => 'Score SEO',
        ),
        'focus_keyword' => array(
            'nl' => 'Focus keyword',
            'en' => 'Focus keyword',
            'de' => 'Fokus-Keyword',
            'fr' => 'Mot-clé principal',
        ),
        
        // Misc
        'share' => array(
            'nl' => 'Delen',
            'en' => 'Share',
            'de' => 'Teilen',
            'fr' => 'Partager',
        ),
        'previous' => array(
            'nl' => 'Vorige',
            'en' => 'Previous',
            'de' => 'Vorherige',
            'fr' => 'Précédent',
        ),
        'next' => array(
            'nl' => 'Volgende',
            'en' => 'Next',
            'de' => 'Nächste',
            'fr' => 'Suivant',
        ),
        'load_more' => array(
            'nl' => 'Meer laden',
            'en' => 'Load more',
            'de' => 'Mehr laden',
            'fr' => 'Charger plus',
        ),
        
        // Homepage
        'popular' => array(
            'nl' => 'Populair',
            'en' => 'Popular',
            'de' => 'Beliebt',
            'fr' => 'Populaire',
        ),
        'featured' => array(
            'nl' => 'Uitgelicht',
            'en' => 'Featured',
            'de' => 'Empfohlen',
            'fr' => 'À la une',
        ),
        'most_read' => array(
            'nl' => 'Meest gelezen',
            'en' => 'Most read',
            'de' => 'Meistgelesen',
            'fr' => 'Les plus lus',
        ),
        'newsletter' => array(
            'nl' => 'Nieuwsbrief',
            'en' => 'Newsletter',
            'de' => 'Newsletter',
            'fr' => 'Newsletter',
        ),
        'newsletter_text' => array(
            'nl' => 'Wekelijks tips, nieuwe reviews en exclusieve aanbiedingen in je inbox.',
            'en' => 'Weekly tips, new reviews and exclusive offers in your inbox.',
            'de' => 'Wöchentliche Tipps, neue Bewertungen und exklusive Angebote in Ihrem Posteingang.',
            'fr' => 'Conseils hebdomadaires, nouveaux avis et offres exclusives dans votre boîte de réception.',
        ),
        'subscribe' => array(
            'nl' => 'Aanmelden',
            'en' => 'Subscribe',
            'de' => 'Abonnieren',
            'fr' => 'S\'abonner',
        ),
        'view_all' => array(
            'nl' => 'Bekijk alles',
            'en' => 'View all',
            'de' => 'Alle anzeigen',
            'fr' => 'Voir tout',
        ),
        'all_reviews' => array(
            'nl' => 'Alle reviews',
            'en' => 'All reviews',
            'de' => 'Alle Bewertungen',
            'fr' => 'Tous les avis',
        ),
        'all_lists' => array(
            'nl' => 'Alle lijstjes',
            'en' => 'All lists',
            'de' => 'Alle Listen',
            'fr' => 'Toutes les listes',
        ),
        'reviews' => array(
            'nl' => 'Reviews',
            'en' => 'Reviews',
            'de' => 'Bewertungen',
            'fr' => 'Avis',
        ),
        'best_lists' => array(
            'nl' => 'Beste lijstjes',
            'en' => 'Best lists',
            'de' => 'Beste Listen',
            'fr' => 'Meilleures listes',
        ),
        'top_list' => array(
            'nl' => 'Top lijst',
            'en' => 'Top list',
            'de' => 'Top Liste',
            'fr' => 'Top liste',
        ),
        'coming_soon' => array(
            'nl' => 'Binnenkort beschikbaar',
            'en' => 'Coming soon',
            'de' => 'Demnächst verfügbar',
            'fr' => 'Bientôt disponible',
        ),
        'add_tag_hint' => array(
            'nl' => 'Voeg de tag "%s" toe aan posts om ze hier te tonen.',
            'en' => 'Add the tag "%s" to posts to show them here.',
            'de' => 'Fügen Sie das Tag "%s" zu Beiträgen hinzu, um sie hier anzuzeigen.',
            'fr' => 'Ajoutez le tag "%s" aux articles pour les afficher ici.',
        ),
        'what_looking_for' => array(
            'nl' => 'Waar ben je naar op zoek?',
            'en' => 'What are you looking for?',
            'de' => 'Wonach suchen Sie?',
            'fr' => 'Que recherchez-vous?',
        ),
        
        // About Page
        'our_mission' => array(
            'nl' => 'Onze Missie',
            'en' => 'Our Mission',
            'de' => 'Unsere Mission',
            'fr' => 'Notre Mission',
        ),
        'why_trust_us' => array(
            'nl' => 'Waarom ons vertrouwen?',
            'en' => 'Why trust us?',
            'de' => 'Warum uns vertrauen?',
            'fr' => 'Pourquoi nous faire confiance?',
        ),
        'our_team' => array(
            'nl' => 'Ons Team',
            'en' => 'Our Team',
            'de' => 'Unser Team',
            'fr' => 'Notre Équipe',
        ),
        'years_experience' => array(
            'nl' => 'Jaar ervaring',
            'en' => 'Years experience',
            'de' => 'Jahre Erfahrung',
            'fr' => 'Années d\'expérience',
        ),
        'articles_written' => array(
            'nl' => 'Artikelen geschreven',
            'en' => 'Articles written',
            'de' => 'Geschriebene Artikel',
            'fr' => 'Articles écrits',
        ),
        'products_tested' => array(
            'nl' => 'Producten getest',
            'en' => 'Products tested',
            'de' => 'Getestete Produkte',
            'fr' => 'Produits testés',
        ),
        'happy_readers' => array(
            'nl' => 'Tevreden lezers',
            'en' => 'Happy readers',
            'de' => 'Zufriedene Leser',
            'fr' => 'Lecteurs satisfaits',
        ),
        'independent_reviews' => array(
            'nl' => 'Onafhankelijke Reviews',
            'en' => 'Independent Reviews',
            'de' => 'Unabhängige Bewertungen',
            'fr' => 'Avis Indépendants',
        ),
        'independent_reviews_text' => array(
            'nl' => 'Wij ontvangen geen betaling voor positieve reviews. Onze mening is altijd eerlijk en onbevooroordeeld.',
            'en' => 'We do not receive payment for positive reviews. Our opinion is always honest and unbiased.',
            'de' => 'Wir erhalten keine Bezahlung für positive Bewertungen. Unsere Meinung ist immer ehrlich und unvoreingenommen.',
            'fr' => 'Nous ne recevons pas de paiement pour les avis positifs. Notre opinion est toujours honnête et impartiale.',
        ),
        'expert_knowledge' => array(
            'nl' => 'Expertise & Kennis',
            'en' => 'Expert Knowledge',
            'de' => 'Expertenwissen',
            'fr' => 'Expertise',
        ),
        'expert_knowledge_text' => array(
            'nl' => 'Ons team bestaat uit experts die dagelijks bezig zijn met de producten die we reviewen.',
            'en' => 'Our team consists of experts who work with the products we review on a daily basis.',
            'de' => 'Unser Team besteht aus Experten, die täglich mit den von uns bewerteten Produkten arbeiten.',
            'fr' => 'Notre équipe est composée d\'experts qui travaillent quotidiennement avec les produits que nous évaluons.',
        ),
        'transparent' => array(
            'nl' => 'Transparant',
            'en' => 'Transparent',
            'de' => 'Transparent',
            'fr' => 'Transparent',
        ),
        'transparent_text' => array(
            'nl' => 'We zijn open over onze werkwijze en hoe we geld verdienen met affiliate links.',
            'en' => 'We are open about how we work and how we earn money through affiliate links.',
            'de' => 'Wir sind offen über unsere Arbeitsweise und wie wir mit Affiliate-Links Geld verdienen.',
            'fr' => 'Nous sommes transparents sur notre façon de travailler et comment nous gagnons de l\'argent via les liens affiliés.',
        ),
        
        // Contact Page
        'contact_us' => array(
            'nl' => 'Neem contact op',
            'en' => 'Contact us',
            'de' => 'Kontaktieren Sie uns',
            'fr' => 'Contactez-nous',
        ),
        'contact_intro' => array(
            'nl' => 'Heb je een vraag, opmerking of wil je samenwerken? Vul het formulier in en we nemen zo snel mogelijk contact met je op.',
            'en' => 'Do you have a question, comment or would you like to collaborate? Fill in the form and we will contact you as soon as possible.',
            'de' => 'Haben Sie eine Frage, einen Kommentar oder möchten Sie zusammenarbeiten? Füllen Sie das Formular aus und wir werden uns so schnell wie möglich bei Ihnen melden.',
            'fr' => 'Avez-vous une question, un commentaire ou souhaitez-vous collaborer? Remplissez le formulaire et nous vous contacterons dès que possible.',
        ),
        'contact_info' => array(
            'nl' => 'Contactgegevens',
            'en' => 'Contact Information',
            'de' => 'Kontaktdaten',
            'fr' => 'Coordonnées',
        ),
        'business_inquiries' => array(
            'nl' => 'Zakelijke aanvragen',
            'en' => 'Business inquiries',
            'de' => 'Geschäftsanfragen',
            'fr' => 'Demandes commerciales',
        ),
        'response_time' => array(
            'nl' => 'Reactietijd',
            'en' => 'Response time',
            'de' => 'Antwortzeit',
            'fr' => 'Délai de réponse',
        ),
        'within_24_hours' => array(
            'nl' => 'Binnen 24 uur',
            'en' => 'Within 24 hours',
            'de' => 'Innerhalb von 24 Stunden',
            'fr' => 'Sous 24 heures',
        ),
        'faq' => array(
            'nl' => 'Veelgestelde vragen',
            'en' => 'Frequently Asked Questions',
            'de' => 'Häufig gestellte Fragen',
            'fr' => 'Questions fréquentes',
        ),
        'email_us' => array(
            'nl' => 'E-mail ons',
            'en' => 'Email us',
            'de' => 'E-Mail an uns',
            'fr' => 'Envoyez-nous un e-mail',
        ),
        'call_us' => array(
            'nl' => 'Bel ons',
            'en' => 'Call us',
            'de' => 'Rufen Sie uns an',
            'fr' => 'Appelez-nous',
        ),
        'address' => array(
            'nl' => 'Adres',
            'en' => 'Address',
            'de' => 'Adresse',
            'fr' => 'Adresse',
        ),
        
        // Blog Archive
        'blog_description' => array(
            'nl' => 'Ontdek onze laatste artikelen, tips en reviews.',
            'en' => 'Discover our latest articles, tips and reviews.',
            'de' => 'Entdecken Sie unsere neuesten Artikel, Tipps und Bewertungen.',
            'fr' => 'Découvrez nos derniers articles, conseils et avis.',
        ),
        'load_more' => array(
            'nl' => 'Meer laden',
            'en' => 'Load more',
            'de' => 'Mehr laden',
            'fr' => 'Charger plus',
        ),
        'no_articles_found' => array(
            'nl' => 'Geen artikelen gevonden.',
            'en' => 'No articles found.',
            'de' => 'Keine Artikel gefunden.',
            'fr' => 'Aucun article trouvé.',
        ),
        'all_articles_loaded' => array(
            'nl' => 'Alle artikelen geladen',
            'en' => 'All articles loaded',
            'de' => 'Alle Artikel geladen',
            'fr' => 'Tous les articles chargés',
        ),
        'error_try_again' => array(
            'nl' => 'Er ging iets mis. Probeer opnieuw.',
            'en' => 'Something went wrong. Please try again.',
            'de' => 'Etwas ist schiefgelaufen. Bitte versuchen Sie es erneut.',
            'fr' => 'Une erreur s\'est produite. Veuillez réessayer.',
        ),
        'no_articles_in_category' => array(
            'nl' => 'Geen artikelen gevonden in deze categorie.',
            'en' => 'No articles found in this category.',
            'de' => 'Keine Artikel in dieser Kategorie gefunden.',
            'fr' => 'Aucun article trouvé dans cette catégorie.',
        ),
    );
    
    // Get translation
    if (isset($translations[$key][$lang])) {
        $text = $translations[$key][$lang];
    } elseif (isset($translations[$key]['en'])) {
        // Fallback to English
        $text = $translations[$key]['en'];
    } else {
        // Return key if not found
        return $key;
    }
    
    // Replace variable if provided
    if ($var !== null) {
        $text = sprintf($text, $var);
    }
    
    return $text;
}

/**
 * Echo translated string
 */
function writgo_te($key, $var = null) {
    echo writgo_t($key, $var);
}

/**
 * Get translated theme mod value
 * Automatically uses translation if value matches old Dutch default or is empty
 */
function writgo_get_mod($mod_key, $translation_key, $old_dutch_default = '') {
    $value = get_theme_mod($mod_key, '');
    
    // Use translation if empty or if it matches the old Dutch default
    if (empty($value) || $value === $old_dutch_default) {
        return writgo_t($translation_key);
    }
    
    return $value;
}

/**
 * Get admin/Customizer translated string
 * For backend labels, sections, descriptions
 */
function writgo_admin_t($key) {
    $lang = writgo_get_language();
    
    $admin_translations = array(
        // Customizer Sections
        'language_section' => array(
            'nl' => '🌍 Taal / Language',
            'en' => '🌍 Language',
            'de' => '🌍 Sprache',
            'fr' => '🌍 Langue',
        ),
        'language_description' => array(
            'nl' => 'Kies de taal voor alle theme teksten.',
            'en' => 'Choose the language for all theme texts.',
            'de' => 'Wählen Sie die Sprache für alle Theme-Texte.',
            'fr' => 'Choisissez la langue pour tous les textes du thème.',
        ),
        'theme_language' => array(
            'nl' => 'Theme Taal',
            'en' => 'Theme Language',
            'de' => 'Theme Sprache',
            'fr' => 'Langue du thème',
        ),
        'layout_settings' => array(
            'nl' => 'Layout Instellingen',
            'en' => 'Layout Settings',
            'de' => 'Layout-Einstellungen',
            'fr' => 'Paramètres de mise en page',
        ),
        'container_width' => array(
            'nl' => 'Container Breedte (px)',
            'en' => 'Container Width (px)',
            'de' => 'Container-Breite (px)',
            'fr' => 'Largeur du conteneur (px)',
        ),
        'logo_height' => array(
            'nl' => 'Logo Hoogte (px)',
            'en' => 'Logo Height (px)',
            'de' => 'Logo-Höhe (px)',
            'fr' => 'Hauteur du logo (px)',
        ),
        'theme_colors' => array(
            'nl' => 'Thema Kleuren',
            'en' => 'Theme Colors',
            'de' => 'Theme-Farben',
            'fr' => 'Couleurs du thème',
        ),
        'primary_color' => array(
            'nl' => 'Primaire Kleur',
            'en' => 'Primary Color',
            'de' => 'Primärfarbe',
            'fr' => 'Couleur principale',
        ),
        'accent_color' => array(
            'nl' => 'Accent Kleur',
            'en' => 'Accent Color',
            'de' => 'Akzentfarbe',
            'fr' => 'Couleur d\'accent',
        ),
        'text_color' => array(
            'nl' => 'Tekst Kleur',
            'en' => 'Text Color',
            'de' => 'Textfarbe',
            'fr' => 'Couleur du texte',
        ),
        'background_color' => array(
            'nl' => 'Achtergrond Kleur',
            'en' => 'Background Color',
            'de' => 'Hintergrundfarbe',
            'fr' => 'Couleur de fond',
        ),
        
        // Homepage Sections
        'homepage_panel' => array(
            'nl' => '🏠 Homepage',
            'en' => '🏠 Homepage',
            'de' => '🏠 Startseite',
            'fr' => '🏠 Page d\'accueil',
        ),
        'homepage_hero' => array(
            'nl' => 'Homepage: Hero',
            'en' => 'Homepage: Hero',
            'de' => 'Startseite: Hero',
            'fr' => 'Page d\'accueil: Hero',
        ),
        'show_hero' => array(
            'nl' => 'Toon Hero sectie',
            'en' => 'Show Hero section',
            'de' => 'Hero-Bereich anzeigen',
            'fr' => 'Afficher la section Hero',
        ),
        'hero_background' => array(
            'nl' => 'Hero Achtergrond',
            'en' => 'Hero Background',
            'de' => 'Hero-Hintergrund',
            'fr' => 'Arrière-plan Hero',
        ),
        'hero_title' => array(
            'nl' => 'Hero Titel',
            'en' => 'Hero Title',
            'de' => 'Hero-Titel',
            'fr' => 'Titre Hero',
        ),
        'leave_empty_sitename' => array(
            'nl' => 'Laat leeg voor site naam',
            'en' => 'Leave empty for site name',
            'de' => 'Leer lassen für Seitenname',
            'fr' => 'Laisser vide pour le nom du site',
        ),
        'hero_subtitle' => array(
            'nl' => 'Hero Subtitel',
            'en' => 'Hero Subtitle',
            'de' => 'Hero-Untertitel',
            'fr' => 'Sous-titre Hero',
        ),
        'leave_empty_tagline' => array(
            'nl' => 'Laat leeg voor site tagline',
            'en' => 'Leave empty for site tagline',
            'de' => 'Leer lassen für Site-Tagline',
            'fr' => 'Laisser vide pour le slogan du site',
        ),
        'search_placeholder' => array(
            'nl' => 'Zoek placeholder tekst',
            'en' => 'Search placeholder text',
            'de' => 'Such-Platzhaltertext',
            'fr' => 'Texte de l\'espace réservé',
        ),
        'search_button_text' => array(
            'nl' => 'Zoek knop tekst',
            'en' => 'Search button text',
            'de' => 'Suchschaltflächen-Text',
            'fr' => 'Texte du bouton de recherche',
        ),
        'leave_empty_auto' => array(
            'nl' => 'Laat leeg voor automatische vertaling',
            'en' => 'Leave empty for automatic translation',
            'de' => 'Leer lassen für automatische Übersetzung',
            'fr' => 'Laisser vide pour traduction automatique',
        ),
        
        // Featured Section
        'homepage_featured' => array(
            'nl' => 'Homepage: Uitgelicht',
            'en' => 'Homepage: Featured',
            'de' => 'Startseite: Empfohlen',
            'fr' => 'Page d\'accueil: À la une',
        ),
        'show_featured' => array(
            'nl' => 'Toon Uitgelicht sectie',
            'en' => 'Show Featured section',
            'de' => 'Empfohlen-Bereich anzeigen',
            'fr' => 'Afficher la section À la une',
        ),
        'section_label' => array(
            'nl' => 'Sectie Label',
            'en' => 'Section Label',
            'de' => 'Bereichs-Label',
            'fr' => 'Étiquette de section',
        ),
        
        // Popular Section
        'homepage_popular' => array(
            'nl' => 'Homepage: Meest Gelezen',
            'en' => 'Homepage: Most Read',
            'de' => 'Startseite: Meistgelesen',
            'fr' => 'Page d\'accueil: Les plus lus',
        ),
        'show_most_read' => array(
            'nl' => 'Toon Meest Gelezen',
            'en' => 'Show Most Read',
            'de' => 'Meistgelesen anzeigen',
            'fr' => 'Afficher Les plus lus',
        ),
        'title' => array(
            'nl' => 'Titel',
            'en' => 'Title',
            'de' => 'Titel',
            'fr' => 'Titre',
        ),
        'icon_emoji' => array(
            'nl' => 'Icoon (emoji)',
            'en' => 'Icon (emoji)',
            'de' => 'Symbol (Emoji)',
            'fr' => 'Icône (emoji)',
        ),
        'number_of_posts' => array(
            'nl' => 'Aantal posts',
            'en' => 'Number of posts',
            'de' => 'Anzahl Beiträge',
            'fr' => 'Nombre d\'articles',
        ),
        
        // Sidebar Widget Section (was Newsletter)
        'sidebar_widget' => array(
            'nl' => 'Homepage: Sidebar Widget',
            'en' => 'Homepage: Sidebar Widget',
            'de' => 'Startseite: Sidebar Widget',
            'fr' => 'Page d\'accueil: Widget Sidebar',
        ),
        'sidebar_widget_desc' => array(
            'nl' => 'Kies wat je in de sidebar wilt tonen: nieuwsbrief, CTA, advertentie of custom HTML.',
            'en' => 'Choose what to show in the sidebar: newsletter, CTA, advertisement or custom HTML.',
            'de' => 'Wählen Sie, was in der Seitenleiste angezeigt werden soll.',
            'fr' => 'Choisissez ce que vous voulez afficher dans la barre latérale.',
        ),
        'widget_type' => array(
            'nl' => 'Widget type',
            'en' => 'Widget type',
            'de' => 'Widget-Typ',
            'fr' => 'Type de widget',
        ),
        'widget_none' => array(
            'nl' => '— Geen widget —',
            'en' => '— No widget —',
            'de' => '— Kein Widget —',
            'fr' => '— Pas de widget —',
        ),
        'widget_newsletter' => array(
            'nl' => '📬 Nieuwsbrief',
            'en' => '📬 Newsletter',
            'de' => '📬 Newsletter',
            'fr' => '📬 Newsletter',
        ),
        'widget_cta' => array(
            'nl' => '🔥 Call-to-Action',
            'en' => '🔥 Call-to-Action',
            'de' => '🔥 Call-to-Action',
            'fr' => '🔥 Appel à l\'action',
        ),
        'widget_ad' => array(
            'nl' => '📢 Advertentie',
            'en' => '📢 Advertisement',
            'de' => '📢 Werbung',
            'fr' => '📢 Publicité',
        ),
        'widget_custom' => array(
            'nl' => '✏️ Custom HTML',
            'en' => '✏️ Custom HTML',
            'de' => '✏️ Benutzerdefiniertes HTML',
            'fr' => '✏️ HTML personnalisé',
        ),
        'widget_icon' => array(
            'nl' => 'Icoon (emoji)',
            'en' => 'Icon (emoji)',
            'de' => 'Symbol (Emoji)',
            'fr' => 'Icône (emoji)',
        ),
        'cta_url_desc' => array(
            'nl' => 'Voor CTA en Advertentie: link wanneer geklikt',
            'en' => 'For CTA and Ad: link when clicked',
            'de' => 'Für CTA und Anzeige: Link beim Klicken',
            'fr' => 'Pour CTA et pub: lien au clic',
        ),
        'ad_image' => array(
            'nl' => 'Advertentie afbeelding',
            'en' => 'Advertisement image',
            'de' => 'Werbebild',
            'fr' => 'Image publicitaire',
        ),
        'ad_image_desc' => array(
            'nl' => 'Upload een banner afbeelding (300x250 aanbevolen)',
            'en' => 'Upload a banner image (300x250 recommended)',
            'de' => 'Laden Sie ein Bannerbild hoch (300x250 empfohlen)',
            'fr' => 'Téléchargez une image de bannière (300x250 recommandé)',
        ),
        'custom_html' => array(
            'nl' => 'Custom HTML',
            'en' => 'Custom HTML',
            'de' => 'Benutzerdefiniertes HTML',
            'fr' => 'HTML personnalisé',
        ),
        'custom_html_desc' => array(
            'nl' => 'Voeg je eigen HTML toe (bijv. embed code, shortcode)',
            'en' => 'Add your own HTML (e.g. embed code, shortcode)',
            'de' => 'Fügen Sie Ihr eigenes HTML hinzu',
            'fr' => 'Ajoutez votre propre HTML',
        ),
        'homepage_newsletter' => array(
            'nl' => 'Homepage: Nieuwsbrief',
            'en' => 'Homepage: Newsletter',
            'de' => 'Startseite: Newsletter',
            'fr' => 'Page d\'accueil: Newsletter',
        ),
        'show_newsletter' => array(
            'nl' => 'Toon Nieuwsbrief widget',
            'en' => 'Show Newsletter widget',
            'de' => 'Newsletter-Widget anzeigen',
            'fr' => 'Afficher le widget Newsletter',
        ),
        'text' => array(
            'nl' => 'Tekst',
            'en' => 'Text',
            'de' => 'Text',
            'fr' => 'Texte',
        ),
        'button_text' => array(
            'nl' => 'Knop tekst',
            'en' => 'Button text',
            'de' => 'Schaltflächentext',
            'fr' => 'Texte du bouton',
        ),
        
        // Latest Articles Section
        'homepage_latest' => array(
            'nl' => 'Homepage: Laatste Artikelen',
            'en' => 'Homepage: Latest Articles',
            'de' => 'Startseite: Neueste Artikel',
            'fr' => 'Page d\'accueil: Derniers articles',
        ),
        'show_latest' => array(
            'nl' => 'Toon Laatste Artikelen',
            'en' => 'Show Latest Articles',
            'de' => 'Neueste Artikel anzeigen',
            'fr' => 'Afficher les derniers articles',
        ),
        
        // Reviews Section
        'homepage_reviews' => array(
            'nl' => 'Homepage: Reviews',
            'en' => 'Homepage: Reviews',
            'de' => 'Startseite: Bewertungen',
            'fr' => 'Page d\'accueil: Avis',
        ),
        'show_reviews' => array(
            'nl' => 'Toon Reviews sectie',
            'en' => 'Show Reviews section',
            'de' => 'Bewertungen-Bereich anzeigen',
            'fr' => 'Afficher la section Avis',
        ),
        'filter_by_tag' => array(
            'nl' => 'Filter op tag (slug)',
            'en' => 'Filter by tag (slug)',
            'de' => 'Nach Tag filtern (Slug)',
            'fr' => 'Filtrer par tag (slug)',
        ),
        
        // Top Lists Section
        'homepage_toplists' => array(
            'nl' => 'Homepage: Beste Lijstjes',
            'en' => 'Homepage: Best Lists',
            'de' => 'Startseite: Beste Listen',
            'fr' => 'Page d\'accueil: Meilleures listes',
        ),
        'show_toplists' => array(
            'nl' => 'Toon Beste Lijstjes sectie',
            'en' => 'Show Best Lists section',
            'de' => 'Beste Listen-Bereich anzeigen',
            'fr' => 'Afficher la section Meilleures listes',
        ),
        'multiple_tags' => array(
            'nl' => 'Meerdere tags met komma',
            'en' => 'Multiple tags with comma',
            'de' => 'Mehrere Tags mit Komma',
            'fr' => 'Plusieurs tags avec virgule',
        ),
        
        // Affiliate Section
        'affiliate_settings' => array(
            'nl' => 'Affiliate Instellingen',
            'en' => 'Affiliate Settings',
            'de' => 'Affiliate-Einstellungen',
            'fr' => 'Paramètres d\'affiliation',
        ),
        'show_disclosure' => array(
            'nl' => 'Toon Affiliate Disclosure',
            'en' => 'Show Affiliate Disclosure',
            'de' => 'Affiliate-Hinweis anzeigen',
            'fr' => 'Afficher la divulgation d\'affiliation',
        ),
        'disclosure_text' => array(
            'nl' => 'Disclosure Tekst',
            'en' => 'Disclosure Text',
            'de' => 'Hinweistext',
            'fr' => 'Texte de divulgation',
        ),
        
        // Company Info Section
        'company_info' => array(
            'nl' => 'Bedrijfsgegevens',
            'en' => 'Company Information',
            'de' => 'Firmeninformationen',
            'fr' => 'Informations de l\'entreprise',
        ),
        'company_info_description' => array(
            'nl' => 'Vul hier je bedrijfsgegevens in. Deze worden automatisch gebruikt in de juridische paginas en footer.',
            'en' => 'Enter your company details here. These are automatically used in legal pages and footer.',
            'de' => 'Geben Sie hier Ihre Firmendaten ein. Diese werden automatisch in rechtlichen Seiten und Footer verwendet.',
            'fr' => 'Entrez les détails de votre entreprise ici. Ils sont utilisés automatiquement dans les pages légales et le pied de page.',
        ),
        'company_name' => array(
            'nl' => 'Bedrijfsnaam',
            'en' => 'Company Name',
            'de' => 'Firmenname',
            'fr' => 'Nom de l\'entreprise',
        ),
        'street_address' => array(
            'nl' => 'Straat + Huisnummer',
            'en' => 'Street Address',
            'de' => 'Straße + Hausnummer',
            'fr' => 'Adresse',
        ),
        'postal_code' => array(
            'nl' => 'Postcode',
            'en' => 'Postal Code',
            'de' => 'Postleitzahl',
            'fr' => 'Code postal',
        ),
        'city' => array(
            'nl' => 'Plaats',
            'en' => 'City',
            'de' => 'Stadt',
            'fr' => 'Ville',
        ),
        'email' => array(
            'nl' => 'E-mailadres',
            'en' => 'Email Address',
            'de' => 'E-Mail-Adresse',
            'fr' => 'Adresse e-mail',
        ),
        'phone' => array(
            'nl' => 'Telefoonnummer',
            'en' => 'Phone Number',
            'de' => 'Telefonnummer',
            'fr' => 'Numéro de téléphone',
        ),
        'kvk_number' => array(
            'nl' => 'KvK Nummer',
            'en' => 'Chamber of Commerce Number',
            'de' => 'Handelsregisternummer',
            'fr' => 'Numéro SIRET',
        ),
        'btw_number' => array(
            'nl' => 'BTW Nummer',
            'en' => 'VAT Number',
            'de' => 'USt-IdNr.',
            'fr' => 'Numéro de TVA',
        ),
        'footer_disclosure' => array(
            'nl' => 'Footer Affiliate Tekst',
            'en' => 'Footer Affiliate Text',
            'de' => 'Footer Affiliate-Text',
            'fr' => 'Texte d\'affiliation du pied de page',
        ),
        
        // Social Media
        'social_media' => array(
            'nl' => 'Social Media',
            'en' => 'Social Media',
            'de' => 'Soziale Medien',
            'fr' => 'Réseaux sociaux',
        ),
        
        // About Page Section
        'about_page' => array(
            'nl' => '📄 Over Ons Pagina',
            'en' => '📄 About Page',
            'de' => '📄 Über Uns Seite',
            'fr' => '📄 Page À propos',
        ),
        'about_hero' => array(
            'nl' => 'Over Ons: Hero',
            'en' => 'About: Hero',
            'de' => 'Über Uns: Hero',
            'fr' => 'À propos: Hero',
        ),
        'about_intro' => array(
            'nl' => 'Over Ons: Introductie',
            'en' => 'About: Introduction',
            'de' => 'Über Uns: Einführung',
            'fr' => 'À propos: Introduction',
        ),
        'about_story' => array(
            'nl' => 'Over Ons: Verhaal',
            'en' => 'About: Story',
            'de' => 'Über Uns: Geschichte',
            'fr' => 'À propos: Histoire',
        ),
        'about_team' => array(
            'nl' => 'Over Ons: Team',
            'en' => 'About: Team',
            'de' => 'Über Uns: Team',
            'fr' => 'À propos: Équipe',
        ),
        'about_process' => array(
            'nl' => 'Over Ons: Werkwijze',
            'en' => 'About: Process',
            'de' => 'Über Uns: Prozess',
            'fr' => 'À propos: Processus',
        ),
        'about_stats' => array(
            'nl' => 'Over Ons: Statistieken',
            'en' => 'About: Statistics',
            'de' => 'Über Uns: Statistiken',
            'fr' => 'À propos: Statistiques',
        ),
        'about_values' => array(
            'nl' => 'Over Ons: Waarden',
            'en' => 'About: Values',
            'de' => 'Über Uns: Werte',
            'fr' => 'À propos: Valeurs',
        ),
        'about_faq' => array(
            'nl' => 'Over Ons: FAQ',
            'en' => 'About: FAQ',
            'de' => 'Über Uns: FAQ',
            'fr' => 'À propos: FAQ',
        ),
        'about_cta' => array(
            'nl' => 'Over Ons: Call to Action',
            'en' => 'About: Call to Action',
            'de' => 'Über Uns: Call to Action',
            'fr' => 'À propos: Appel à l\'action',
        ),
        'show_section' => array(
            'nl' => 'Toon sectie',
            'en' => 'Show section',
            'de' => 'Bereich anzeigen',
            'fr' => 'Afficher la section',
        ),
        'subtitle' => array(
            'nl' => 'Subtitel',
            'en' => 'Subtitle',
            'de' => 'Untertitel',
            'fr' => 'Sous-titre',
        ),
        'label' => array(
            'nl' => 'Label',
            'en' => 'Label',
            'de' => 'Label',
            'fr' => 'Étiquette',
        ),
        'image' => array(
            'nl' => 'Afbeelding',
            'en' => 'Image',
            'de' => 'Bild',
            'fr' => 'Image',
        ),
        'name' => array(
            'nl' => 'Naam',
            'en' => 'Name',
            'de' => 'Name',
            'fr' => 'Nom',
        ),
        'role' => array(
            'nl' => 'Rol/Functie',
            'en' => 'Role/Position',
            'de' => 'Rolle/Position',
            'fr' => 'Rôle/Poste',
        ),
        'bio' => array(
            'nl' => 'Bio',
            'en' => 'Bio',
            'de' => 'Bio',
            'fr' => 'Bio',
        ),
        'number' => array(
            'nl' => 'Nummer',
            'en' => 'Number',
            'de' => 'Nummer',
            'fr' => 'Numéro',
        ),
        'question' => array(
            'nl' => 'Vraag',
            'en' => 'Question',
            'de' => 'Frage',
            'fr' => 'Question',
        ),
        'answer' => array(
            'nl' => 'Antwoord',
            'en' => 'Answer',
            'de' => 'Antwort',
            'fr' => 'Réponse',
        ),
        'url' => array(
            'nl' => 'URL',
            'en' => 'URL',
            'de' => 'URL',
            'fr' => 'URL',
        ),
        
        // Contact Page
        'contact_page' => array(
            'nl' => '📧 Contact Pagina',
            'en' => '📧 Contact Page',
            'de' => '📧 Kontaktseite',
            'fr' => '📧 Page de contact',
        ),
        'contact_hero' => array(
            'nl' => 'Contact: Hero',
            'en' => 'Contact: Hero',
            'de' => 'Kontakt: Hero',
            'fr' => 'Contact: Hero',
        ),
        'contact_faq' => array(
            'nl' => 'Contact: FAQ',
            'en' => 'Contact: FAQ',
            'de' => 'Kontakt: FAQ',
            'fr' => 'Contact: FAQ',
        ),
        'form_shortcode' => array(
            'nl' => 'Formulier Shortcode',
            'en' => 'Form Shortcode',
            'de' => 'Formular-Shortcode',
            'fr' => 'Shortcode du formulaire',
        ),
        'form_shortcode_desc' => array(
            'nl' => 'Bijv. [contact-form-7] of [wpforms]. Laat leeg voor standaard formulier.',
            'en' => 'E.g. [contact-form-7] or [wpforms]. Leave empty for default form.',
            'de' => 'Z.B. [contact-form-7] oder [wpforms]. Leer lassen für Standardformular.',
            'fr' => 'Ex. [contact-form-7] ou [wpforms]. Laisser vide pour le formulaire par défaut.',
        ),
        
        // SEO Section
        'seo_settings' => array(
            'nl' => '🔍 SEO Instellingen',
            'en' => '🔍 SEO Settings',
            'de' => '🔍 SEO-Einstellungen',
            'fr' => '🔍 Paramètres SEO',
        ),
        
        // Meta Box Labels
        'seo_meta_box' => array(
            'nl' => '🔍 Writgo SEO',
            'en' => '🔍 Writgo SEO',
            'de' => '🔍 Writgo SEO',
            'fr' => '🔍 Writgo SEO',
        ),
        'affiliate_meta_box' => array(
            'nl' => '💰 Affiliate Instellingen',
            'en' => '💰 Affiliate Settings',
            'de' => '💰 Affiliate-Einstellungen',
            'fr' => '💰 Paramètres d\'affiliation',
        ),
        'article_settings' => array(
            'nl' => '📝 Artikel Instellingen',
            'en' => '📝 Article Settings',
            'de' => '📝 Artikeleinstellungen',
            'fr' => '📝 Paramètres de l\'article',
        ),
        'focus_keyword' => array(
            'nl' => 'Focus Keyword',
            'en' => 'Focus Keyword',
            'de' => 'Fokus-Keyword',
            'fr' => 'Mot-clé principal',
        ),
        'seo_title' => array(
            'nl' => 'SEO Titel',
            'en' => 'SEO Title',
            'de' => 'SEO-Titel',
            'fr' => 'Titre SEO',
        ),
        'meta_description' => array(
            'nl' => 'Meta Omschrijving',
            'en' => 'Meta Description',
            'de' => 'Meta-Beschreibung',
            'fr' => 'Meta description',
        ),
        'featured_post' => array(
            'nl' => 'Uitgelicht artikel',
            'en' => 'Featured post',
            'de' => 'Hervorgehobener Beitrag',
            'fr' => 'Article en vedette',
        ),
        'show_toc' => array(
            'nl' => 'Toon inhoudsopgave',
            'en' => 'Show table of contents',
            'de' => 'Inhaltsverzeichnis anzeigen',
            'fr' => 'Afficher la table des matières',
        ),
        'show_author_box' => array(
            'nl' => 'Toon auteur box',
            'en' => 'Show author box',
            'de' => 'Autorenbox anzeigen',
            'fr' => 'Afficher la boîte auteur',
        ),
        'product_score' => array(
            'nl' => 'Product Score (1-10)',
            'en' => 'Product Score (1-10)',
            'de' => 'Produktbewertung (1-10)',
            'fr' => 'Score du produit (1-10)',
        ),
        'affiliate_link' => array(
            'nl' => 'Affiliate Link',
            'en' => 'Affiliate Link',
            'de' => 'Affiliate-Link',
            'fr' => 'Lien d\'affiliation',
        ),
        'product_price' => array(
            'nl' => 'Product Prijs',
            'en' => 'Product Price',
            'de' => 'Produktpreis',
            'fr' => 'Prix du produit',
        ),
        'save' => array(
            'nl' => 'Opslaan',
            'en' => 'Save',
            'de' => 'Speichern',
            'fr' => 'Enregistrer',
        ),
        
        // Dashboard
        'dashboard_title' => array(
            'nl' => 'Writgo Dashboard',
            'en' => 'Writgo Dashboard',
            'de' => 'Writgo Dashboard',
            'fr' => 'Tableau de bord Writgo',
        ),
        'quick_stats' => array(
            'nl' => 'Snelle Statistieken',
            'en' => 'Quick Stats',
            'de' => 'Schnelle Statistiken',
            'fr' => 'Statistiques rapides',
        ),
        'total_posts' => array(
            'nl' => 'Totaal Artikelen',
            'en' => 'Total Posts',
            'de' => 'Gesamtbeiträge',
            'fr' => 'Total des articles',
        ),
        'total_pages' => array(
            'nl' => 'Totaal Paginas',
            'en' => 'Total Pages',
            'de' => 'Gesamtseiten',
            'fr' => 'Total des pages',
        ),
        'total_categories' => array(
            'nl' => 'Categorieën',
            'en' => 'Categories',
            'de' => 'Kategorien',
            'fr' => 'Catégories',
        ),
        'total_tags' => array(
            'nl' => 'Tags',
            'en' => 'Tags',
            'de' => 'Tags',
            'fr' => 'Tags',
        ),
        
        // Menus & Widgets
        'main_menu' => array(
            'nl' => 'Hoofdmenu',
            'en' => 'Main Menu',
            'de' => 'Hauptmenü',
            'fr' => 'Menu principal',
        ),
        'footer_menu' => array(
            'nl' => 'Footermenu',
            'en' => 'Footer Menu',
            'de' => 'Footer-Menü',
            'fr' => 'Menu pied de page',
        ),
        'sidebar' => array(
            'nl' => 'Sidebar',
            'en' => 'Sidebar',
            'de' => 'Seitenleiste',
            'fr' => 'Barre latérale',
        ),
        'add_widgets_sidebar' => array(
            'nl' => 'Voeg widgets toe aan de sidebar.',
            'en' => 'Add widgets to the sidebar.',
            'de' => 'Widgets zur Seitenleiste hinzufügen.',
            'fr' => 'Ajouter des widgets à la barre latérale.',
        ),
        
        // About Page Sections
        'introduction' => array(
            'nl' => 'Introductie',
            'en' => 'Introduction',
            'de' => 'Einführung',
            'fr' => 'Introduction',
        ),
        'show_introduction' => array(
            'nl' => 'Toon introductie',
            'en' => 'Show introduction',
            'de' => 'Einführung anzeigen',
            'fr' => 'Afficher l\'introduction',
        ),
        'our_story' => array(
            'nl' => 'Ons Verhaal',
            'en' => 'Our Story',
            'de' => 'Unsere Geschichte',
            'fr' => 'Notre Histoire',
        ),
        'show_our_story' => array(
            'nl' => 'Toon ons verhaal',
            'en' => 'Show our story',
            'de' => 'Unsere Geschichte anzeigen',
            'fr' => 'Afficher notre histoire',
        ),
        'statistics' => array(
            'nl' => 'Statistieken',
            'en' => 'Statistics',
            'de' => 'Statistiken',
            'fr' => 'Statistiques',
        ),
        'show_statistics' => array(
            'nl' => 'Toon statistieken',
            'en' => 'Show statistics',
            'de' => 'Statistiken anzeigen',
            'fr' => 'Afficher les statistiques',
        ),
        'stat_number' => array(
            'nl' => 'Stat %d: Nummer',
            'en' => 'Stat %d: Number',
            'de' => 'Stat %d: Nummer',
            'fr' => 'Stat %d: Numéro',
        ),
        'stat_label' => array(
            'nl' => 'Stat %d: Label',
            'en' => 'Stat %d: Label',
            'de' => 'Stat %d: Label',
            'fr' => 'Stat %d: Étiquette',
        ),
        'process' => array(
            'nl' => 'Werkwijze',
            'en' => 'Process',
            'de' => 'Prozess',
            'fr' => 'Processus',
        ),
        'show_process' => array(
            'nl' => 'Toon werkwijze',
            'en' => 'Show process',
            'de' => 'Prozess anzeigen',
            'fr' => 'Afficher le processus',
        ),
        'team' => array(
            'nl' => 'Team',
            'en' => 'Team',
            'de' => 'Team',
            'fr' => 'Équipe',
        ),
        'show_team' => array(
            'nl' => 'Toon team',
            'en' => 'Show team',
            'de' => 'Team anzeigen',
            'fr' => 'Afficher l\'équipe',
        ),
        'core_values' => array(
            'nl' => 'Kernwaarden',
            'en' => 'Core Values',
            'de' => 'Grundwerte',
            'fr' => 'Valeurs fondamentales',
        ),
        'show_core_values' => array(
            'nl' => 'Toon kernwaarden',
            'en' => 'Show core values',
            'de' => 'Grundwerte anzeigen',
            'fr' => 'Afficher les valeurs fondamentales',
        ),
        'section_title' => array(
            'nl' => 'Sectie titel',
            'en' => 'Section title',
            'de' => 'Abschnittstitel',
            'fr' => 'Titre de section',
        ),
        'faq' => array(
            'nl' => 'FAQ',
            'en' => 'FAQ',
            'de' => 'FAQ',
            'fr' => 'FAQ',
        ),
        'show_faq' => array(
            'nl' => 'Toon FAQ',
            'en' => 'Show FAQ',
            'de' => 'FAQ anzeigen',
            'fr' => 'Afficher la FAQ',
        ),
        'faq_question' => array(
            'nl' => 'FAQ %d - Vraag',
            'en' => 'FAQ %d - Question',
            'de' => 'FAQ %d - Frage',
            'fr' => 'FAQ %d - Question',
        ),
        'faq_answer' => array(
            'nl' => 'FAQ %d - Antwoord',
            'en' => 'FAQ %d - Answer',
            'de' => 'FAQ %d - Antwort',
            'fr' => 'FAQ %d - Réponse',
        ),
        'call_to_action' => array(
            'nl' => 'Call to Action',
            'en' => 'Call to Action',
            'de' => 'Call to Action',
            'fr' => 'Appel à l\'action',
        ),
        'show_cta' => array(
            'nl' => 'Toon CTA sectie',
            'en' => 'Show CTA section',
            'de' => 'CTA-Bereich anzeigen',
            'fr' => 'Afficher la section CTA',
        ),
        'button_url' => array(
            'nl' => 'Knop URL',
            'en' => 'Button URL',
            'de' => 'Schaltflächen-URL',
            'fr' => 'URL du bouton',
        ),
        
        // Contact Page
        'contact_settings' => array(
            'nl' => 'Contact Pagina',
            'en' => 'Contact Page',
            'de' => 'Kontaktseite',
            'fr' => 'Page de contact',
        ),
        'contact_settings_desc' => array(
            'nl' => 'Instellingen voor de contact pagina.',
            'en' => 'Settings for the contact page.',
            'de' => 'Einstellungen für die Kontaktseite.',
            'fr' => 'Paramètres pour la page de contact.',
        ),
        'hero_title' => array(
            'nl' => 'Hero Titel',
            'en' => 'Hero Title',
            'de' => 'Hero-Titel',
            'fr' => 'Titre Hero',
        ),
        'hero_subtitle' => array(
            'nl' => 'Hero Subtitel',
            'en' => 'Hero Subtitle',
            'de' => 'Hero-Untertitel',
            'fr' => 'Sous-titre Hero',
        ),
        'response_time_text' => array(
            'nl' => 'Reactietijd tekst',
            'en' => 'Response time text',
            'de' => 'Antwortzeit-Text',
            'fr' => 'Texte de temps de réponse',
        ),
        
        // Author Box
        'author_box' => array(
            'nl' => 'Author Box',
            'en' => 'Author Box',
            'de' => 'Autorenbox',
            'fr' => 'Boîte auteur',
        ),
        'author_box_desc' => array(
            'nl' => 'Pas de auteur box aan die onder artikelen wordt getoond.',
            'en' => 'Customize the author box shown below articles.',
            'de' => 'Passen Sie die Autorenbox an, die unter Artikeln angezeigt wird.',
            'fr' => 'Personnalisez la boîte auteur affichée sous les articles.',
        ),
        'show_author_box' => array(
            'nl' => 'Author Box tonen',
            'en' => 'Show Author Box',
            'de' => 'Autorenbox anzeigen',
            'fr' => 'Afficher la boîte auteur',
        ),
        'use_custom_author' => array(
            'nl' => 'Gebruik custom auteur (site-breed)',
            'en' => 'Use custom author (site-wide)',
            'de' => 'Benutzerdefinierten Autor verwenden (seitenweit)',
            'fr' => 'Utiliser un auteur personnalisé (sur tout le site)',
        ),
        'use_custom_author_desc' => array(
            'nl' => 'Vink aan om een vaste auteur/team te tonen in plaats van de WordPress auteur.',
            'en' => 'Check to show a fixed author/team instead of the WordPress author.',
            'de' => 'Aktivieren, um einen festen Autor/Team anstelle des WordPress-Autors anzuzeigen.',
            'fr' => 'Cochez pour afficher un auteur/équipe fixe au lieu de l\'auteur WordPress.',
        ),
        'author_team_name' => array(
            'nl' => 'Auteur/Team Naam',
            'en' => 'Author/Team Name',
            'de' => 'Autor/Team-Name',
            'fr' => 'Nom de l\'auteur/équipe',
        ),
        'author_team_name_desc' => array(
            'nl' => 'Bijv: "Team Phoception.nl" of "Redactie"',
            'en' => 'E.g.: "Team Example.com" or "Editorial"',
            'de' => 'Z.B.: "Team Example.de" oder "Redaktion"',
            'fr' => 'Ex.: "Équipe Example.fr" ou "Rédaction"',
        ),
        
        // Admin notices
        'pages_missing' => array(
            'nl' => 'De volgende pagina\'s ontbreken:',
            'en' => 'The following pages are missing:',
            'de' => 'Die folgenden Seiten fehlen:',
            'fr' => 'Les pages suivantes sont manquantes:',
        ),
        'create_all_pages' => array(
            'nl' => '📄 Maak alle pagina\'s aan',
            'en' => '📄 Create all pages',
            'de' => '📄 Alle Seiten erstellen',
            'fr' => '📄 Créer toutes les pages',
        ),
        
        // Extra Author Box translations
        'author_name_desc' => array(
            'nl' => 'Bijv: "Team Phoception.nl" of "Redactie"',
            'en' => 'E.g.: "Team Example.com" or "Editorial"',
            'de' => 'Z.B.: "Team Example.de" oder "Redaktion"',
            'fr' => 'Ex.: "Équipe Example.fr" ou "Rédaction"',
        ),
        'author_prefix' => array(
            'nl' => 'Tekst voor auteur naam',
            'en' => 'Text before author name',
            'de' => 'Text vor dem Autorennamen',
            'fr' => 'Texte avant le nom de l\'auteur',
        ),
        'author_prefix_desc' => array(
            'nl' => 'Bijv: "Geschreven door" of "Door het team van"',
            'en' => 'E.g.: "Written by" or "By the team of"',
            'de' => 'Z.B.: "Geschrieben von" oder "Vom Team von"',
            'fr' => 'Ex.: "Écrit par" ou "Par l\'équipe de"',
        ),
        'author_bio' => array(
            'nl' => 'Auteur Biografie',
            'en' => 'Author Biography',
            'de' => 'Autor-Biografie',
            'fr' => 'Biographie de l\'auteur',
        ),
        'author_bio_desc' => array(
            'nl' => 'Uitgebreide beschrijving van de auteur of het team. HTML toegestaan.',
            'en' => 'Extended description of the author or team. HTML allowed.',
            'de' => 'Ausführliche Beschreibung des Autors oder Teams. HTML erlaubt.',
            'fr' => 'Description détaillée de l\'auteur ou de l\'équipe. HTML autorisé.',
        ),
        'author_image' => array(
            'nl' => 'Auteur Afbeelding/Logo',
            'en' => 'Author Image/Logo',
            'de' => 'Autor-Bild/Logo',
            'fr' => 'Image/Logo de l\'auteur',
        ),
        'author_image_desc' => array(
            'nl' => 'Upload een foto of logo. Aanbevolen: vierkant, min. 150x150px.',
            'en' => 'Upload a photo or logo. Recommended: square, min. 150x150px.',
            'de' => 'Laden Sie ein Foto oder Logo hoch. Empfohlen: quadratisch, min. 150x150px.',
            'fr' => 'Téléchargez une photo ou un logo. Recommandé: carré, min. 150x150px.',
        ),
        'author_website_url' => array(
            'nl' => 'Auteur Website URL',
            'en' => 'Author Website URL',
            'de' => 'Autor-Website-URL',
            'fr' => 'URL du site web de l\'auteur',
        ),
        'layout_style' => array(
            'nl' => 'Layout Stijl',
            'en' => 'Layout Style',
            'de' => 'Layout-Stil',
            'fr' => 'Style de mise en page',
        ),
        'horizontal' => array(
            'nl' => 'Horizontaal (afbeelding links)',
            'en' => 'Horizontal (image left)',
            'de' => 'Horizontal (Bild links)',
            'fr' => 'Horizontal (image à gauche)',
        ),
        'vertical' => array(
            'nl' => 'Verticaal (afbeelding boven)',
            'en' => 'Vertical (image top)',
            'de' => 'Vertikal (Bild oben)',
            'fr' => 'Vertical (image en haut)',
        ),
        'minimal' => array(
            'nl' => 'Minimaal (alleen tekst)',
            'en' => 'Minimal (text only)',
            'de' => 'Minimal (nur Text)',
            'fr' => 'Minimal (texte uniquement)',
        ),
        'background_color' => array(
            'nl' => 'Achtergrondkleur',
            'en' => 'Background Color',
            'de' => 'Hintergrundfarbe',
            'fr' => 'Couleur de fond',
        ),
        'show_on_pages' => array(
            'nl' => 'Ook tonen op pagina\'s',
            'en' => 'Also show on pages',
            'de' => 'Auch auf Seiten anzeigen',
            'fr' => 'Afficher aussi sur les pages',
        ),
    );
    
    if (isset($admin_translations[$key][$lang])) {
        return $admin_translations[$key][$lang];
    } elseif (isset($admin_translations[$key]['en'])) {
        return $admin_translations[$key]['en'];
    }
    
    return $key;
}

/**
 * Add language to Customizer
 */
add_action('customize_register', 'writgo_language_customizer', 5);
function writgo_language_customizer($wp_customize) {
    // Language Section
    $wp_customize->add_section('writgo_language', array(
        'title'       => writgo_admin_t('language_section'),
        'description' => writgo_admin_t('language_description'),
        'priority'    => 5,
    ));
    
    // Language Setting
    $wp_customize->add_setting('writgo_language', array(
        'default'           => 'nl',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_language', array(
        'label'   => writgo_admin_t('theme_language'),
        'section' => 'writgo_language',
        'type'    => 'select',
        'choices' => array(
            'nl' => '🇳🇱 Nederlands',
            'en' => '🇬🇧 English',
            'de' => '🇩🇪 Deutsch',
            'fr' => '🇫🇷 Français',
        ),
    ));
}

/**
 * Set HTML lang attribute based on theme language
 */
add_filter('language_attributes', 'writgo_html_lang_attribute');
function writgo_html_lang_attribute($output) {
    $lang = writgo_get_language();
    $lang_codes = array(
        'nl' => 'nl-NL',
        'en' => 'en-US',
        'de' => 'de-DE',
        'fr' => 'fr-FR',
    );
    
    $lang_code = isset($lang_codes[$lang]) ? $lang_codes[$lang] : 'nl-NL';
    
    return 'lang="' . $lang_code . '"';
}

// =============================================================================
// AUTOMATISCHE JAAR VERVANGING (Evergreen Content)
// =============================================================================
// Vervangt oude jaren (2020-vorig jaar) automatisch met het huidige jaar
// Werkt in: titels, SEO titels, meta descriptions, content
// Gebruik [current_year] voor expliciete jaar placeholder

/**
 * Vervang oude jaren met huidig jaar
 */
function writgo_replace_year($text) {
    if (empty($text)) {
        return $text;
    }
    
    $current_year = date('Y');
    
    // Vervang [current_year] placeholder
    $text = str_replace('[current_year]', $current_year, $text);
    $text = str_replace('[jaar]', $current_year, $text);
    
    // Automatisch oude jaren vervangen (laatste 5 jaar)
    // Alleen vervangen als het in een "lijst" context staat (beste ... 2023, top 10 ... 2024, etc)
    for ($year = $current_year - 5; $year < $current_year; $year++) {
        // Patronen die wijzen op "actuele" content die bijgewerkt moet worden
        $patterns = array(
            '/\b(beste|top|review|test|vergelijk|kopen|goedkoopste|tips)\s+(.{0,50})\s+' . $year . '\b/i',
            '/\b' . $year . '\s+(.{0,50})\s+(beste|top|review|test|vergelijk|gids|guide)\b/i',
            '/\b(in|van|voor)\s+' . $year . '\b/i',
        );
        
        foreach ($patterns as $pattern) {
            $text = preg_replace_callback($pattern, function($matches) use ($current_year, $year) {
                return str_replace($year, $current_year, $matches[0]);
            }, $text);
        }
    }
    
    return $text;
}

/**
 * Filter: Post titels
 */
add_filter('the_title', 'writgo_auto_year_title', 10, 2);
function writgo_auto_year_title($title, $post_id = null) {
    // Niet in admin (behalve AJAX)
    if (is_admin() && !wp_doing_ajax()) {
        return $title;
    }
    return writgo_replace_year($title);
}

/**
 * Filter: Document title (browser tab)
 */
add_filter('document_title_parts', 'writgo_auto_year_document_title');
function writgo_auto_year_document_title($title_parts) {
    if (isset($title_parts['title'])) {
        $title_parts['title'] = writgo_replace_year($title_parts['title']);
    }
    return $title_parts;
}

/**
 * Filter: SEO titel (Writgo SEO)
 */
add_filter('writgo_seo_title', 'writgo_replace_year');

/**
 * Filter: Meta description (Writgo SEO)
 */
add_filter('writgo_seo_description', 'writgo_replace_year');

/**
 * Filter: Content
 */
add_filter('the_content', 'writgo_auto_year_content', 5);
function writgo_auto_year_content($content) {
    // Alleen [current_year] en [jaar] in content vervangen
    // Niet automatische jaar vervanging (te agressief voor content)
    $current_year = date('Y');
    $content = str_replace('[current_year]', $current_year, $content);
    $content = str_replace('[jaar]', $current_year, $content);
    return $content;
}

/**
 * Filter: Excerpts
 */
add_filter('the_excerpt', 'writgo_replace_year');
add_filter('get_the_excerpt', 'writgo_replace_year');

/**
 * Shortcode: [current_year] of [jaar]
 */
add_shortcode('current_year', function() {
    return date('Y');
});
add_shortcode('jaar', function() {
    return date('Y');
});

/**
 * Theme Setup
 */
add_action('after_setup_theme', 'writgo_setup');
function writgo_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    
    // Image sizes
    add_image_size('writgo-hero', 1920, 800, true);
    add_image_size('writgo-featured', 800, 450, true);
    add_image_size('writgo-card', 400, 250, true);
    add_image_size('writgo-thumb', 150, 150, true);
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => writgo_admin_t('main_menu'),
        'footer'  => writgo_admin_t('footer_menu'),
    ));
    
    // Load textdomain
    load_theme_textdomain('writgo-affiliate', WRITGO_DIR . '/languages');
}

/**
 * Handle Contact Form Submission
 */
add_action('admin_post_writgo_contact_form', 'writgo_handle_contact_form');
add_action('admin_post_nopriv_writgo_contact_form', 'writgo_handle_contact_form');
function writgo_handle_contact_form() {
    // Verify nonce
    if (!isset($_POST['writgo_contact_nonce']) || !wp_verify_nonce($_POST['writgo_contact_nonce'], 'writgo_contact_form')) {
        wp_die('Beveiligingscontrole mislukt. Probeer het opnieuw.');
    }
    
    // Sanitize input
    $name = sanitize_text_field($_POST['contact_name'] ?? '');
    $email = sanitize_email($_POST['contact_email'] ?? '');
    $subject = sanitize_text_field($_POST['contact_subject'] ?? 'algemeen');
    $message = sanitize_textarea_field($_POST['contact_message'] ?? '');
    $privacy = isset($_POST['contact_privacy']);
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($message) || !$privacy) {
        wp_redirect(add_query_arg('contact', 'error', home_url('/contact/')));
        exit;
    }
    
    // Get admin email
    $to = get_theme_mod('writgo_contact_email', get_option('admin_email'));
    
    // Subject mapping
    $subjects = array(
        'algemeen'     => 'Algemene vraag',
        'samenwerking' => 'Samenwerking',
        'feedback'     => 'Feedback',
        'fout'         => 'Fout melden',
        'anders'       => 'Anders',
    );
    $subject_text = $subjects[$subject] ?? 'Contact';
    
    // Build email
    $email_subject = sprintf('[%s] %s van %s', get_bloginfo('name'), $subject_text, $name);
    
    $email_body = sprintf(
        "Nieuw contactbericht via %s\n\n" .
        "Naam: %s\n" .
        "E-mail: %s\n" .
        "Onderwerp: %s\n\n" .
        "Bericht:\n%s\n\n" .
        "---\n" .
        "Dit bericht is verzonden via het contactformulier op %s",
        get_bloginfo('name'),
        $name,
        $email,
        $subject_text,
        $message,
        home_url()
    );
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );
    
    // Send email
    $sent = wp_mail($to, $email_subject, $email_body, $headers);
    
    if ($sent) {
        wp_redirect(add_query_arg('contact', 'success', home_url('/contact/')));
    } else {
        wp_redirect(add_query_arg('contact', 'error', home_url('/contact/')));
    }
    exit;
}

/**
 * Admin Notice for Missing Pages + Create Pages Button
 */
add_action('admin_notices', 'writgo_admin_notice_missing_pages');
function writgo_admin_notice_missing_pages() {
    // Only show to admins
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Check if we should create pages
    if (isset($_GET['writgo_create_pages']) && $_GET['writgo_create_pages'] === '1') {
        if (wp_verify_nonce($_GET['_wpnonce'], 'writgo_create_pages')) {
            writgo_create_default_pages();
            echo '<div class="notice notice-success is-dismissible"><p><strong>Writgo:</strong> Alle pagina\'s zijn aangemaakt! <a href="' . admin_url('edit.php?post_type=page') . '">Bekijk pagina\'s</a></p></div>';
            return;
        }
    }
    
    // Check which pages are missing based on language
    $lang = function_exists('writgo_get_language') ? writgo_get_language() : 'nl';
    
    $required_pages_by_lang = array(
        'nl' => array(
            'disclaimer'            => 'Disclaimer',
            'privacyverklaring'     => 'Privacyverklaring',
            'cookiebeleid'          => 'Cookiebeleid',
            'algemene-voorwaarden'  => 'Algemene Voorwaarden',
            'over-ons'              => 'Over Ons',
            'contact'               => 'Contact',
        ),
        'en' => array(
            'disclaimer'            => 'Disclaimer',
            'privacy-policy'        => 'Privacy Policy',
            'cookie-policy'         => 'Cookie Policy',
            'terms-conditions'      => 'Terms & Conditions',
            'about-us'              => 'About Us',
            'contact'               => 'Contact',
        ),
        'de' => array(
            'haftungsausschluss'    => 'Haftungsausschluss',
            'datenschutz'           => 'Datenschutzerklärung',
            'cookie-richtlinie'     => 'Cookie-Richtlinie',
            'agb'                   => 'AGB',
            'ueber-uns'             => 'Über Uns',
            'kontakt'               => 'Kontakt',
        ),
        'fr' => array(
            'avertissement'                   => 'Avertissement',
            'politique-de-confidentialite'    => 'Politique de confidentialité',
            'politique-cookies'               => 'Politique de cookies',
            'conditions-generales'            => 'Conditions générales',
            'a-propos'                        => 'À propos',
            'contact'                         => 'Contact',
        ),
    );
    
    $required_pages = $required_pages_by_lang[$lang] ?? $required_pages_by_lang['nl'];
    
    $missing = array();
    foreach ($required_pages as $slug => $title) {
        if (!get_page_by_path($slug)) {
            $missing[] = $title;
        }
    }
    
    if (empty($missing)) {
        return;
    }
    
    $create_url = wp_nonce_url(
        admin_url('?writgo_create_pages=1'),
        'writgo_create_pages'
    );
    
    echo '<div class="notice notice-warning">';
    echo '<p><strong>Writgo Theme:</strong> ' . esc_html(writgo_admin_t('pages_missing')) . ' <em>' . implode(', ', $missing) . '</em></p>';
    echo '<p><a href="' . esc_url($create_url) . '" class="button button-primary">' . esc_html(writgo_admin_t('create_all_pages')) . '</a></p>';
    echo '</div>';
}

/**
 * Create Default Pages on Theme Activation
 */
add_action('after_switch_theme', 'writgo_create_default_pages');
function writgo_create_default_pages() {
    
    // Include legal pages content
    require_once WRITGO_DIR . '/inc/legal-pages.php';
    
    // Get current language
    $lang = function_exists('writgo_get_language') ? writgo_get_language() : 'nl';
    
    // Define pages to create per language
    $pages_by_lang = array(
        'nl' => array(
            'over-ons' => array(
                'title'    => 'Over Ons',
                'template' => 'page-about.php',
                'content'  => '',
            ),
            'contact' => array(
                'title'    => 'Contact',
                'template' => 'page-contact.php',
                'content'  => '',
            ),
            'disclaimer' => array(
                'title'    => 'Disclaimer',
                'template' => '',
                'content'  => writgo_get_legal_content('disclaimer'),
            ),
            'privacyverklaring' => array(
                'title'    => 'Privacyverklaring',
                'template' => '',
                'content'  => writgo_get_legal_content('privacyverklaring'),
            ),
            'cookiebeleid' => array(
                'title'    => 'Cookiebeleid',
                'template' => '',
                'content'  => writgo_get_legal_content('cookiebeleid'),
            ),
            'algemene-voorwaarden' => array(
                'title'    => 'Algemene Voorwaarden',
                'template' => '',
                'content'  => writgo_get_legal_content('algemene-voorwaarden'),
            ),
        ),
        'en' => array(
            'about-us' => array(
                'title'    => 'About Us',
                'template' => 'page-about.php',
                'content'  => '',
            ),
            'contact' => array(
                'title'    => 'Contact',
                'template' => 'page-contact.php',
                'content'  => '',
            ),
            'disclaimer' => array(
                'title'    => 'Disclaimer',
                'template' => '',
                'content'  => writgo_get_legal_content('disclaimer'),
            ),
            'privacy-policy' => array(
                'title'    => 'Privacy Policy',
                'template' => '',
                'content'  => writgo_get_legal_content('privacy-policy'),
            ),
            'cookie-policy' => array(
                'title'    => 'Cookie Policy',
                'template' => '',
                'content'  => writgo_get_legal_content('cookie-policy'),
            ),
            'terms-conditions' => array(
                'title'    => 'Terms & Conditions',
                'template' => '',
                'content'  => writgo_get_legal_content('terms-conditions'),
            ),
        ),
        'de' => array(
            'ueber-uns' => array(
                'title'    => 'Über Uns',
                'template' => 'page-about.php',
                'content'  => '',
            ),
            'kontakt' => array(
                'title'    => 'Kontakt',
                'template' => 'page-contact.php',
                'content'  => '',
            ),
            'haftungsausschluss' => array(
                'title'    => 'Haftungsausschluss',
                'template' => '',
                'content'  => writgo_get_legal_content('disclaimer'),
            ),
            'datenschutz' => array(
                'title'    => 'Datenschutzerklärung',
                'template' => '',
                'content'  => writgo_get_legal_content('datenschutz'),
            ),
            'cookie-richtlinie' => array(
                'title'    => 'Cookie-Richtlinie',
                'template' => '',
                'content'  => writgo_get_legal_content('cookie-richtlinie'),
            ),
            'agb' => array(
                'title'    => 'AGB',
                'template' => '',
                'content'  => writgo_get_legal_content('agb'),
            ),
        ),
        'fr' => array(
            'a-propos' => array(
                'title'    => 'À propos',
                'template' => 'page-about.php',
                'content'  => '',
            ),
            'contact' => array(
                'title'    => 'Contact',
                'template' => 'page-contact.php',
                'content'  => '',
            ),
            'avertissement' => array(
                'title'    => 'Avertissement',
                'template' => '',
                'content'  => writgo_get_legal_content('disclaimer'),
            ),
            'politique-de-confidentialite' => array(
                'title'    => 'Politique de confidentialité',
                'template' => '',
                'content'  => writgo_get_legal_content('politique-de-confidentialite'),
            ),
            'politique-cookies' => array(
                'title'    => 'Politique de cookies',
                'template' => '',
                'content'  => writgo_get_legal_content('politique-cookies'),
            ),
            'conditions-generales' => array(
                'title'    => 'Conditions générales',
                'template' => '',
                'content'  => writgo_get_legal_content('conditions-generales'),
            ),
        ),
    );
    
    // Use the language-specific pages or fallback to Dutch
    $pages = $pages_by_lang[$lang] ?? $pages_by_lang['nl'];
    
    // Create each page if it doesn\'t exist
    foreach ($pages as $slug => $page_data) {
        
        // Check if page with this slug already exists
        $existing_page = get_page_by_path($slug);
        
        if (!$existing_page) {
            // Create the page
            $page_id = wp_insert_post(array(
                'post_title'     => $page_data['title'],
                'post_name'      => $slug,
                'post_content'   => $page_data['content'],
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => get_current_user_id() ?: 1,
                'comment_status' => 'closed',
            ));
            
            // Set page template if specified
            if ($page_id && !is_wp_error($page_id) && !empty($page_data['template'])) {
                update_post_meta($page_id, '_wp_page_template', $page_data['template']);
            }
        }
    }
    
    // Create a primary menu if it does not exist
    $menu_name = 'Hoofdmenu';
    $menu_exists = wp_get_nav_menu_object($menu_name);
    
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);
        
        if (!is_wp_error($menu_id)) {
            // Add Home
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title'   => 'Home',
                'menu-item-url'     => home_url('/'),
                'menu-item-status'  => 'publish',
                'menu-item-type'    => 'custom',
            ));
            
            // Add pages to menu
            $menu_pages = array('Over Ons', 'Contact');
            foreach ($menu_pages as $page_title) {
                $page = get_page_by_title($page_title);
                if ($page) {
                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title'     => $page_title,
                        'menu-item-object'    => 'page',
                        'menu-item-object-id' => $page->ID,
                        'menu-item-type'      => 'post_type',
                        'menu-item-status'    => 'publish',
                    ));
                }
            }
            
            // Assign menu to primary location
            $locations = get_theme_mod('nav_menu_locations', array());
            $locations['primary'] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
    
    // Create footer menu
    $footer_menu_name = 'Footermenu';
    $footer_menu_exists = wp_get_nav_menu_object($footer_menu_name);
    
    if (!$footer_menu_exists) {
        $footer_menu_id = wp_create_nav_menu($footer_menu_name);
        
        if (!is_wp_error($footer_menu_id)) {
            $footer_pages = array(
                'Privacyverklaring' => 'Privacyverklaring',
                'Cookiebeleid' => 'Cookiebeleid',
                'Disclaimer' => 'Disclaimer',
                'Algemene Voorwaarden' => 'Algemene Voorwaarden',
            );
            foreach ($footer_pages as $page_title => $menu_title) {
                $page = get_page_by_title($page_title);
                if ($page) {
                    wp_update_nav_menu_item($footer_menu_id, 0, array(
                        'menu-item-title'     => $menu_title,
                        'menu-item-object'    => 'page',
                        'menu-item-object-id' => $page->ID,
                        'menu-item-type'      => 'post_type',
                        'menu-item-status'    => 'publish',
                    ));
                }
            }
            
            // Assign menu to footer location
            $locations = get_theme_mod('nav_menu_locations', array());
            $locations['footer'] = $footer_menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
    
    // Flush rewrite rules to ensure new pages work
    flush_rewrite_rules();
}

/**
 * Register Widget Areas
 */
add_action('widgets_init', 'writgo_widgets_init');
function writgo_widgets_init() {
    register_sidebar(array(
        'name'          => writgo_admin_t('sidebar'),
        'id'            => 'sidebar-1',
        'description'   => writgo_admin_t('add_widgets_sidebar'),
        'before_widget' => '<div id="%1$s" class="wa-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="wa-widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => 'Footer 1',
        'id'            => 'footer-1',
        'before_widget' => '<div id="%1$s" class="wa-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="wa-footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => 'Footer 2',
        'id'            => 'footer-2',
        'before_widget' => '<div id="%1$s" class="wa-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="wa-footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => 'Footer 3',
        'id'            => 'footer-3',
        'before_widget' => '<div id="%1$s" class="wa-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="wa-footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Below TOC sidebar (for article pages)
    register_sidebar(array(
        'name'          => 'Onder Inhoudsopgave',
        'id'            => 'below-toc',
        'description'   => 'Widgets die onder de inhoudsopgave worden getoond op artikelpagina\'s.',
        'before_widget' => '<div id="%1$s" class="wa-below-toc-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="wa-below-toc-title">',
        'after_title'   => '</h4>',
    ));
}

/**
 * Enqueue Styles and Scripts
 */
add_action('wp_enqueue_scripts', 'writgo_enqueue_assets');
function writgo_enqueue_assets() {
    // Main stylesheet
    wp_enqueue_style(
        'writgo-main',
        WRITGO_URI . '/assets/css/main.css',
        array(),
        WRITGO_VERSION
    );
    
    // Affiliate/Conversion stylesheet
    wp_enqueue_style(
        'writgo-affiliate',
        WRITGO_URI . '/assets/css/affiliate.css',
        array('writgo-main'),
        WRITGO_VERSION
    );
    
    // Google Fonts
    wp_enqueue_style(
        'writgo-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );
    
    // TOC script (single posts only)
    if (is_singular('post')) {
        wp_enqueue_script(
            'writgo-toc',
            WRITGO_URI . '/assets/js/toc.js',
            array(),
            WRITGO_VERSION,
            true
        );
    }
    
    // Main JavaScript
    wp_enqueue_script(
        'writgo-main',
        WRITGO_URI . '/assets/js/main.js',
        array(),
        WRITGO_VERSION,
        true
    );
}

/**
 * Calculate Reading Time
 */
function writgo_get_reading_time($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200);
    
    return max(1, $reading_time);
}

/**
 * Breadcrumbs
 */
function writgo_breadcrumbs() {
    if (is_front_page()) {
        return;
    }
    
    echo '<nav class="wa-breadcrumbs" aria-label="Breadcrumbs">';
    echo '<a href="' . esc_url(home_url('/')) . '">Home</a>';
    
    if (is_single()) {
        $categories = get_the_category();
        if (!empty($categories)) {
            echo '<span class="wa-breadcrumb-sep">›</span>';
            echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
        }
        echo '<span class="wa-breadcrumb-sep">›</span>';
        echo '<span class="wa-breadcrumb-current">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_category()) {
        echo '<span class="wa-breadcrumb-sep">›</span>';
        echo '<span class="wa-breadcrumb-current">' . single_cat_title('', false) . '</span>';
    } elseif (is_page()) {
        echo '<span class="wa-breadcrumb-sep">›</span>';
        echo '<span class="wa-breadcrumb-current">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_search()) {
        echo '<span class="wa-breadcrumb-sep">›</span>';
        echo '<span class="wa-breadcrumb-current">Zoekresultaten</span>';
    } elseif (is_archive()) {
        echo '<span class="wa-breadcrumb-sep">›</span>';
        echo '<span class="wa-breadcrumb-current">' . get_the_archive_title() . '</span>';
    }
    
    echo '</nav>';
}

/**
 * Related Posts
 */
function writgo_related_posts($count = 3) {
    $post_id = get_the_ID();
    $categories = wp_get_post_categories($post_id);
    
    if (empty($categories)) {
        return;
    }
    
    $args = array(
        'category__in'   => $categories,
        'post__not_in'   => array($post_id),
        'posts_per_page' => $count,
        'orderby'        => 'rand',
        'post_status'    => 'publish'
    );
    
    $related = new WP_Query($args);
    
    if ($related->have_posts()) :
    ?>
    <div class="wa-related-grid">
        <?php while ($related->have_posts()) : $related->the_post(); ?>
            <article class="wa-post-card">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" class="wa-card-image-link">
                        <?php the_post_thumbnail('writgo-card', array('class' => 'wa-card-image')); ?>
                    </a>
                <?php endif; ?>
                
                <div class="wa-card-content">
                    <h3 class="wa-card-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <time class="wa-card-date"><?php echo get_the_date('j M Y'); ?></time>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
    <?php
    endif;
    
    wp_reset_postdata();
}

/**
 * Customizer Settings
 */
add_action('customize_register', 'writgo_customizer');
function writgo_customizer($wp_customize) {
    
    // =========================================================================
    // LAYOUT SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_layout', array(
        'title'    => writgo_admin_t('layout_settings'),
        'priority' => 30,
    ));
    
    // Container Width
    $wp_customize->add_setting('writgo_container_width', array(
        'default'           => 1400,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('writgo_container_width', array(
        'label'       => writgo_admin_t('container_width'),
        'section'     => 'writgo_layout',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1000,
            'max'  => 1800,
            'step' => 50,
        ),
    ));
    
    // Logo Height
    $wp_customize->add_setting('writgo_logo_height', array(
        'default'           => 50,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('writgo_logo_height', array(
        'label'       => writgo_admin_t('logo_height'),
        'section'     => 'title_tagline',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 30,
            'max'  => 100,
            'step' => 5,
        ),
        'priority'    => 9,
    ));
    
    // =========================================================================
    // COLORS SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_colors', array(
        'title'    => writgo_admin_t('theme_colors'),
        'priority' => 40,
    ));
    
    // Primary Color
    $wp_customize->add_setting('writgo_primary_color', array(
        'default'           => '#1a365d',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'writgo_primary_color', array(
        'label'   => writgo_admin_t('primary_color'),
        'section' => 'writgo_colors',
    )));
    
    // Accent Color
    $wp_customize->add_setting('writgo_accent_color', array(
        'default'           => '#f97316',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'writgo_accent_color', array(
        'label'   => writgo_admin_t('accent_color'),
        'section' => 'writgo_colors',
    )));
    
    // =========================================================================
    // HOMEPAGE SECTIONS
    // =========================================================================
    
    // --- Hero Section ---
    $wp_customize->add_section('writgo_homepage_hero', array(
        'title'    => writgo_admin_t('homepage_hero'),
        'priority' => 50,
        'panel'    => 'writgo_homepage',
    ));
    
    // Create Homepage Panel
    $wp_customize->add_panel('writgo_homepage', array(
        'title'    => writgo_admin_t('homepage_panel'),
        'priority' => 45,
    ));
    
    // Hero Show
    $wp_customize->add_setting('writgo_hero_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_hero_show', array(
        'label'   => writgo_admin_t('show_hero'),
        'section' => 'writgo_homepage_hero',
        'type'    => 'checkbox',
    ));
    
    // Hero Background Image
    $wp_customize->add_setting('writgo_hero_bg', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'writgo_hero_bg', array(
        'label'   => writgo_admin_t('hero_background'),
        'section' => 'writgo_homepage_hero',
    )));
    
    // Hero Title
    $wp_customize->add_setting('writgo_hero_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_hero_title', array(
        'label'       => writgo_admin_t('title'),
        'description' => writgo_admin_t('leave_empty_sitename'),
        'section'     => 'writgo_homepage_hero',
        'type'        => 'text',
    ));
    
    // Hero Subtitle
    $wp_customize->add_setting('writgo_hero_subtitle', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_hero_subtitle', array(
        'label'       => writgo_admin_t('subtitle'),
        'description' => writgo_admin_t('leave_empty_tagline'),
        'section'     => 'writgo_homepage_hero',
        'type'        => 'textarea',
    ));
    
    // Hero Search Placeholder
    $wp_customize->add_setting('writgo_hero_search_placeholder', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_hero_search_placeholder', array(
        'label'       => writgo_admin_t('search_placeholder'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_homepage_hero',
        'type'        => 'text',
    ));
    
    // Hero Search Button
    $wp_customize->add_setting('writgo_hero_search_button', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_hero_search_button', array(
        'label'       => writgo_admin_t('search_button_text'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_homepage_hero',
        'type'        => 'text',
    ));
    
    // Hero Overlay Color
    $wp_customize->add_setting('writgo_hero_overlay_color', array(
        'default'           => 'rgba(0,0,0,0.5)',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_hero_overlay_color', array(
        'label'       => 'Hero Overlay Kleur',
        'description' => 'Bijv: rgba(0,0,0,0.5) of #1a365d',
        'section'     => 'writgo_homepage_hero',
        'type'        => 'text',
    ));
    
    // Hero Text Color
    $wp_customize->add_setting('writgo_hero_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'writgo_hero_text_color', array(
        'label'   => 'Hero Tekst Kleur',
        'section' => 'writgo_homepage_hero',
    )));
    
    // --- Article Hero Section ---
    $wp_customize->add_section('writgo_article_hero', array(
        'title'    => '📝 Artikel Hero',
        'priority' => 55,
    ));
    
    // Article Hero Overlay Color
    $wp_customize->add_setting('writgo_article_hero_overlay', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_article_hero_overlay', array(
        'label'       => 'Hero Overlay Kleur',
        'description' => 'Standaard is een donkere gradient. Bijv: rgba(26,54,93,0.8) of laat leeg voor standaard.',
        'section'     => 'writgo_article_hero',
        'type'        => 'text',
    ));
    
    // Article Hero Text Color
    $wp_customize->add_setting('writgo_article_hero_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'writgo_article_hero_text_color', array(
        'label'   => 'Hero Tekst Kleur',
        'section' => 'writgo_article_hero',
    )));
    
    // --- Featured Section ---
    $wp_customize->add_section('writgo_homepage_featured', array(
        'title' => writgo_admin_t('homepage_featured'),
        'panel' => 'writgo_homepage',
    ));
    
    $wp_customize->add_setting('writgo_featured_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_featured_show', array(
        'label'   => writgo_admin_t('show_featured'),
        'section' => 'writgo_homepage_featured',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_featured_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_featured_title', array(
        'label'       => writgo_admin_t('section_label'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_homepage_featured',
        'type'        => 'text',
    ));
    
    // --- Popular Section ---
    $wp_customize->add_section('writgo_homepage_popular', array(
        'title' => writgo_admin_t('homepage_popular'),
        'panel' => 'writgo_homepage',
    ));
    
    $wp_customize->add_setting('writgo_popular_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_popular_show', array(
        'label'   => writgo_admin_t('show_most_read'),
        'section' => 'writgo_homepage_popular',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_popular_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_popular_title', array(
        'label'       => writgo_admin_t('title'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_homepage_popular',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('writgo_popular_icon', array(
        'default'           => '🔥',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_popular_icon', array(
        'label'   => writgo_admin_t('icon_emoji'),
        'section' => 'writgo_homepage_popular',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_popular_count', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('writgo_popular_count', array(
        'label'   => writgo_admin_t('number_of_posts'),
        'section' => 'writgo_homepage_popular',
        'type'    => 'number',
        'input_attrs' => array('min' => 2, 'max' => 10),
    ));
    
    // --- Sidebar Widget Section (was Newsletter) ---
    $wp_customize->add_section('writgo_homepage_newsletter', array(
        'title'       => writgo_admin_t('sidebar_widget'),
        'description' => writgo_admin_t('sidebar_widget_desc'),
        'panel'       => 'writgo_homepage',
    ));
    
    // Widget Type
    $wp_customize->add_setting('writgo_sidebar_widget_type', array(
        'default'           => 'newsletter',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_sidebar_widget_type', array(
        'label'   => writgo_admin_t('widget_type'),
        'section' => 'writgo_homepage_newsletter',
        'type'    => 'select',
        'choices' => array(
            'none'       => writgo_admin_t('widget_none'),
            'newsletter' => writgo_admin_t('widget_newsletter'),
            'cta'        => writgo_admin_t('widget_cta'),
            'ad'         => writgo_admin_t('widget_ad'),
            'custom'     => writgo_admin_t('widget_custom'),
        ),
    ));
    
    // Icon/Emoji
    $wp_customize->add_setting('writgo_sidebar_widget_icon', array(
        'default'           => '📬',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_sidebar_widget_icon', array(
        'label'       => writgo_admin_t('widget_icon'),
        'description' => '📬 🔥 💡 🎁 📢 ⭐ 🚀 💰',
        'section'     => 'writgo_homepage_newsletter',
        'type'        => 'text',
    ));
    
    // Title
    $wp_customize->add_setting('writgo_newsletter_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_newsletter_title', array(
        'label'       => writgo_admin_t('title'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_homepage_newsletter',
        'type'        => 'text',
    ));
    
    // Text/Description
    $wp_customize->add_setting('writgo_newsletter_text', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_newsletter_text', array(
        'label'       => writgo_admin_t('text'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_homepage_newsletter',
        'type'        => 'textarea',
    ));
    
    // Button Text
    $wp_customize->add_setting('writgo_newsletter_button', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_newsletter_button', array(
        'label'       => writgo_admin_t('button_text'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_homepage_newsletter',
        'type'        => 'text',
    ));
    
    // Button/CTA URL (for CTA type)
    $wp_customize->add_setting('writgo_sidebar_widget_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('writgo_sidebar_widget_url', array(
        'label'       => writgo_admin_t('button_url'),
        'description' => writgo_admin_t('cta_url_desc'),
        'section'     => 'writgo_homepage_newsletter',
        'type'        => 'url',
    ));
    
    // Ad Image
    $wp_customize->add_setting('writgo_sidebar_widget_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'writgo_sidebar_widget_image', array(
        'label'       => writgo_admin_t('ad_image'),
        'description' => writgo_admin_t('ad_image_desc'),
        'section'     => 'writgo_homepage_newsletter',
    )));
    
    // Custom HTML
    $wp_customize->add_setting('writgo_sidebar_widget_html', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('writgo_sidebar_widget_html', array(
        'label'       => writgo_admin_t('custom_html'),
        'description' => writgo_admin_t('custom_html_desc'),
        'section'     => 'writgo_homepage_newsletter',
        'type'        => 'textarea',
    ));
    
    // --- Latest Articles Section ---
    $wp_customize->add_section('writgo_homepage_latest', array(
        'title' => writgo_admin_t('homepage_latest'),
        'panel' => 'writgo_homepage',
    ));
    
    $wp_customize->add_setting('writgo_latest_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_latest_show', array(
        'label'   => writgo_admin_t('show_latest'),
        'section' => 'writgo_homepage_latest',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_latest_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_latest_title', array(
        'label'       => writgo_admin_t('title'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_homepage_latest',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('writgo_latest_count', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('writgo_latest_count', array(
        'label'   => writgo_admin_t('number_of_posts'),
        'section' => 'writgo_homepage_latest',
        'type'    => 'number',
        'input_attrs' => array('min' => 2, 'max' => 12),
    ));
    
    // --- Reviews Section ---
    $wp_customize->add_section('writgo_homepage_reviews', array(
        'title' => writgo_admin_t('homepage_reviews'),
        'panel' => 'writgo_homepage',
    ));
    
    $wp_customize->add_setting('writgo_reviews_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_reviews_show', array(
        'label'   => writgo_admin_t('show_reviews'),
        'section' => 'writgo_homepage_reviews',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_reviews_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_reviews_title', array(
        'label'       => writgo_admin_t('title'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_homepage_reviews',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('writgo_reviews_icon', array(
        'default'           => '⭐',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_reviews_icon', array(
        'label'   => writgo_admin_t('icon_emoji'),
        'section' => 'writgo_homepage_reviews',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_reviews_tag', array(
        'default'           => 'review',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_reviews_tag', array(
        'label'   => writgo_admin_t('filter_by_tag'),
        'section' => 'writgo_homepage_reviews',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_reviews_count', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('writgo_reviews_count', array(
        'label'   => writgo_admin_t('number_of_posts'),
        'section' => 'writgo_homepage_reviews',
        'type'    => 'number',
        'input_attrs' => array('min' => 2, 'max' => 8),
    ));
    
    // --- Top Lists Section ---
    $wp_customize->add_section('writgo_homepage_toplists', array(
        'title' => writgo_admin_t('homepage_toplists'),
        'panel' => 'writgo_homepage',
    ));
    
    $wp_customize->add_setting('writgo_toplists_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_toplists_show', array(
        'label'   => writgo_admin_t('show_toplists'),
        'section' => 'writgo_homepage_toplists',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_toplists_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_toplists_title', array(
        'label'       => writgo_admin_t('title'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_homepage_toplists',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('writgo_toplists_icon', array(
        'default'           => '🏆',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_toplists_icon', array(
        'label'   => writgo_admin_t('icon_emoji'),
        'section' => 'writgo_homepage_toplists',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_toplists_tag', array(
        'default'           => 'beste,top',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_toplists_tag', array(
        'label'       => writgo_admin_t('filter_by_tag'),
        'description' => writgo_admin_t('multiple_tags'),
        'section'     => 'writgo_homepage_toplists',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('writgo_toplists_count', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('writgo_toplists_count', array(
        'label'   => writgo_admin_t('number_of_posts'),
        'section' => 'writgo_homepage_toplists',
        'type'    => 'number',
        'input_attrs' => array('min' => 2, 'max' => 8),
    ));
    
    // =========================================================================
    // ABOUT PAGE PANEL
    // =========================================================================
    $wp_customize->add_panel('writgo_about', array(
        'title'    => writgo_admin_t('about_page'),
        'priority' => 46,
    ));
    
    // --- About Hero ---
    $wp_customize->add_section('writgo_about_hero', array(
        'title' => writgo_admin_t('about_hero'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_hero_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_hero_title', array(
        'label'       => writgo_admin_t('title'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_about_hero',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_hero_subtitle', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_hero_subtitle', array(
        'label'       => writgo_admin_t('subtitle'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_about_hero',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_hero_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'writgo_about_hero_image', array(
        'label'   => writgo_admin_t('image'),
        'section' => 'writgo_about_hero',
    )));
    
    // --- About Intro ---
    $wp_customize->add_section('writgo_about_intro', array(
        'title' => writgo_admin_t('introduction'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_intro_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_intro_show', array(
        'label'   => writgo_admin_t('show_introduction'),
        'section' => 'writgo_about_intro',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_intro_label', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_intro_label', array(
        'label'       => writgo_admin_t('label'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_about_intro',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_intro_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_intro_title', array(
        'label'       => writgo_admin_t('title'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_about_intro',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_intro_text', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('writgo_about_intro_text', array(
        'label'       => writgo_admin_t('text'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_about_intro',
        'type'        => 'textarea',
    ));
    
    $wp_customize->add_setting('writgo_about_intro_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'writgo_about_intro_image', array(
        'label'   => writgo_admin_t('image'),
        'section' => 'writgo_about_intro',
    )));
    
    // --- About Story ---
    $wp_customize->add_section('writgo_about_story', array(
        'title' => writgo_admin_t('our_story'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_story_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_story_show', array(
        'label'   => writgo_admin_t('show_our_story'),
        'section' => 'writgo_about_story',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_story_label', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_story_label', array(
        'label'       => writgo_admin_t('label'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_about_story',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_story_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_story_title', array(
        'label'       => writgo_admin_t('title'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_about_story',
        'type'        => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_story_text', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('writgo_about_story_text', array(
        'label'       => writgo_admin_t('text'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_about_story',
        'type'        => 'textarea',
    ));
    
    // --- About Stats ---
    $wp_customize->add_section('writgo_about_stats', array(
        'title' => writgo_admin_t('statistics'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_stats_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_stats_show', array(
        'label'   => writgo_admin_t('show_statistics'),
        'section' => 'writgo_about_stats',
        'type'    => 'checkbox',
    ));
    
    // Stat 1
    $wp_customize->add_setting('writgo_about_stat1_number', array(
        'default'           => '500+',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat1_number', array(
        'label'   => sprintf(writgo_admin_t('stat_number'), 1),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    $wp_customize->add_setting('writgo_about_stat1_label', array(
        'default'           => 'Artikelen',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat1_label', array(
        'label'   => sprintf(writgo_admin_t('stat_label'), 1),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    
    // Stat 2
    $wp_customize->add_setting('writgo_about_stat2_number', array(
        'default'           => '100+',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat2_number', array(
        'label'   => sprintf(writgo_admin_t('stat_number'), 2),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    $wp_customize->add_setting('writgo_about_stat2_label', array(
        'default'           => 'Reviews',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat2_label', array(
        'label'   => sprintf(writgo_admin_t('stat_label'), 2),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    
    // Stat 3
    $wp_customize->add_setting('writgo_about_stat3_number', array(
        'default'           => '50K+',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat3_number', array(
        'label'   => sprintf(writgo_admin_t('stat_number'), 3),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    $wp_customize->add_setting('writgo_about_stat3_label', array(
        'default'           => 'Lezers',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat3_label', array(
        'label'   => sprintf(writgo_admin_t('stat_label'), 3),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    
    // Stat 4
    $wp_customize->add_setting('writgo_about_stat4_number', array(
        'default'           => '5+',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat4_number', array(
        'label'   => sprintf(writgo_admin_t('stat_number'), 4),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    $wp_customize->add_setting('writgo_about_stat4_label', array(
        'default'           => 'Jaar ervaring',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_stat4_label', array(
        'label'   => sprintf(writgo_admin_t('stat_label'), 4),
        'section' => 'writgo_about_stats',
        'type'    => 'text',
    ));
    
    // --- About Process ---
    $wp_customize->add_section('writgo_about_process', array(
        'title' => writgo_admin_t('process'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_process_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_process_show', array(
        'label'   => writgo_admin_t('show_process'),
        'section' => 'writgo_about_process',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_process_title', array('default' => 'Onze Werkwijze', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_process_subtitle', array('default' => 'Hoe wij reviews maken', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_about_process_title', array('label' => 'Titel', 'section' => 'writgo_about_process', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_process_subtitle', array('label' => 'Subtitel', 'section' => 'writgo_about_process', 'type' => 'text'));
    
    // Process steps
    for ($i = 1; $i <= 4; $i++) {
        $defaults = array(
            1 => array('🔍', 'Onderzoek', 'We beginnen met uitgebreid marktonderzoek.'),
            2 => array('🧪', 'Testen', 'Elk product wordt grondig getest.'),
            3 => array('📊', 'Vergelijken', 'We vergelijken prestaties en prijs-kwaliteit.'),
            4 => array('✍️', 'Publiceren', 'Onze bevindingen worden vertaald naar reviews.'),
        );
        $wp_customize->add_setting("writgo_about_process{$i}_icon", array('default' => $defaults[$i][0], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_process{$i}_title", array('default' => $defaults[$i][1], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_process{$i}_text", array('default' => $defaults[$i][2], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control("writgo_about_process{$i}_icon", array('label' => "Stap {$i}: Icoon", 'section' => 'writgo_about_process', 'type' => 'text'));
        $wp_customize->add_control("writgo_about_process{$i}_title", array('label' => "Stap {$i}: Titel", 'section' => 'writgo_about_process', 'type' => 'text'));
        $wp_customize->add_control("writgo_about_process{$i}_text", array('label' => "Stap {$i}: Tekst", 'section' => 'writgo_about_process', 'type' => 'textarea'));
    }
    
    // --- About Team ---
    $wp_customize->add_section('writgo_about_team', array(
        'title' => writgo_admin_t('team'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_team_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_team_show', array(
        'label'   => writgo_admin_t('show_team'),
        'section' => 'writgo_about_team',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_team_title', array('default' => 'Ons Team', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_team_subtitle', array('default' => 'De mensen achter de reviews', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_about_team_title', array('label' => 'Titel', 'section' => 'writgo_about_team', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_team_subtitle', array('label' => 'Subtitel', 'section' => 'writgo_about_team', 'type' => 'text'));
    
    // Team members
    for ($i = 1; $i <= 3; $i++) {
        $defaults = array(
            1 => array('Jan de Vries', 'Hoofdredacteur', '10+ jaar ervaring in consumentenonderzoek.'),
            2 => array('Lisa Bakker', 'Tech Expert', 'Gespecialiseerd in elektronica en smart home.'),
            3 => array('Mark Jansen', 'Product Tester', 'Test producten op duurzaamheid en waarde.'),
        );
        $wp_customize->add_setting("writgo_about_team{$i}_name", array('default' => $defaults[$i][0], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_team{$i}_role", array('default' => $defaults[$i][1], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_team{$i}_bio", array('default' => $defaults[$i][2], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_team{$i}_image", array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control("writgo_about_team{$i}_name", array('label' => "Lid {$i}: Naam", 'section' => 'writgo_about_team', 'type' => 'text'));
        $wp_customize->add_control("writgo_about_team{$i}_role", array('label' => "Lid {$i}: Functie", 'section' => 'writgo_about_team', 'type' => 'text'));
        $wp_customize->add_control("writgo_about_team{$i}_bio", array('label' => "Lid {$i}: Bio", 'section' => 'writgo_about_team', 'type' => 'textarea'));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "writgo_about_team{$i}_image", array('label' => "Lid {$i}: Foto", 'section' => 'writgo_about_team')));
    }
    
    // --- About Values ---
    $wp_customize->add_section('writgo_about_values', array(
        'title' => writgo_admin_t('core_values'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_values_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_values_show', array(
        'label'   => writgo_admin_t('show_core_values'),
        'section' => 'writgo_about_values',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_values_title', array(
        'default'           => 'Onze Kernwaarden',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_values_title', array(
        'label'   => writgo_admin_t('section_title'),
        'section' => 'writgo_about_values',
        'type'    => 'text',
    ));
    
    // Value 1
    $wp_customize->add_setting('writgo_about_value1_icon', array('default' => '🎯', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value1_title', array('default' => 'Onafhankelijk', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value1_text', array('default' => 'Onze reviews zijn 100% onafhankelijk. Wij laten ons niet beïnvloeden door merken of adverteerders.', 'sanitize_callback' => 'sanitize_text_field'));
    
    $wp_customize->add_control('writgo_about_value1_icon', array('label' => 'Waarde 1: Icoon', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value1_title', array('label' => 'Waarde 1: Titel', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value1_text', array('label' => 'Waarde 1: Tekst', 'section' => 'writgo_about_values', 'type' => 'textarea'));
    
    // Value 2
    $wp_customize->add_setting('writgo_about_value2_icon', array('default' => '✅', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value2_title', array('default' => 'Betrouwbaar', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value2_text', array('default' => 'Alle informatie wordt zorgvuldig gecontroleerd en regelmatig bijgewerkt voor nauwkeurigheid.', 'sanitize_callback' => 'sanitize_text_field'));
    
    $wp_customize->add_control('writgo_about_value2_icon', array('label' => 'Waarde 2: Icoon', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value2_title', array('label' => 'Waarde 2: Titel', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value2_text', array('label' => 'Waarde 2: Tekst', 'section' => 'writgo_about_values', 'type' => 'textarea'));
    
    // Value 3
    $wp_customize->add_setting('writgo_about_value3_icon', array('default' => '💡', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value3_title', array('default' => 'Toegankelijk', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_setting('writgo_about_value3_text', array('default' => 'Complexe informatie maken wij begrijpelijk voor iedereen, zonder vakjargon.', 'sanitize_callback' => 'sanitize_text_field'));
    
    $wp_customize->add_control('writgo_about_value3_icon', array('label' => 'Waarde 3: Icoon', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value3_title', array('label' => 'Waarde 3: Titel', 'section' => 'writgo_about_values', 'type' => 'text'));
    $wp_customize->add_control('writgo_about_value3_text', array('label' => 'Waarde 3: Tekst', 'section' => 'writgo_about_values', 'type' => 'textarea'));
    
    // --- About FAQ ---
    $wp_customize->add_section('writgo_about_faq', array(
        'title' => writgo_admin_t('faq'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_faq_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_faq_show', array(
        'label'   => writgo_admin_t('show_faq'),
        'section' => 'writgo_about_faq',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_faq_title', array('default' => 'Veelgestelde Vragen', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('writgo_about_faq_title', array('label' => 'Titel', 'section' => 'writgo_about_faq', 'type' => 'text'));
    
    // FAQ items
    $faq_defaults = array(
        1 => array('Hoe verdienen jullie geld?', 'Wij verdienen een kleine commissie wanneer je een product koopt via onze links.'),
        2 => array('Zijn jullie reviews echt onafhankelijk?', 'Absoluut. We kopen zelf producten of lenen ze tijdelijk voor tests.'),
        3 => array('Hoe vaak worden reviews bijgewerkt?', 'We herzien onze reviews minimaal elk kwartaal.'),
    );
    for ($i = 1; $i <= 3; $i++) {
        $wp_customize->add_setting("writgo_about_faq{$i}_q", array('default' => $faq_defaults[$i][0], 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_setting("writgo_about_faq{$i}_a", array('default' => $faq_defaults[$i][1], 'sanitize_callback' => 'wp_kses_post'));
        $wp_customize->add_control("writgo_about_faq{$i}_q", array('label' => "Vraag {$i}", 'section' => 'writgo_about_faq', 'type' => 'text'));
        $wp_customize->add_control("writgo_about_faq{$i}_a", array('label' => "Antwoord {$i}", 'section' => 'writgo_about_faq', 'type' => 'textarea'));
    }
    
    // --- About CTA ---
    $wp_customize->add_section('writgo_about_cta', array(
        'title' => writgo_admin_t('call_to_action'),
        'panel' => 'writgo_about',
    ));
    
    $wp_customize->add_setting('writgo_about_cta_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('writgo_about_cta_show', array(
        'label'   => writgo_admin_t('show_cta'),
        'section' => 'writgo_about_cta',
        'type'    => 'checkbox',
    ));
    
    $wp_customize->add_setting('writgo_about_cta_title', array(
        'default'           => 'Klaar om te beginnen?',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_cta_title', array(
        'label'   => writgo_admin_t('title'),
        'section' => 'writgo_about_cta',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_cta_text', array(
        'default'           => 'Ontdek onze nieuwste reviews en vind het perfecte product voor jou.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_cta_text', array(
        'label'   => writgo_admin_t('text'),
        'section' => 'writgo_about_cta',
        'type'    => 'textarea',
    ));
    
    $wp_customize->add_setting('writgo_about_cta_button', array(
        'default'           => 'Bekijk Reviews',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('writgo_about_cta_button', array(
        'label'   => writgo_admin_t('button_text'),
        'section' => 'writgo_about_cta',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('writgo_about_cta_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('writgo_about_cta_url', array(
        'label'   => writgo_admin_t('button_url'),
        'section' => 'writgo_about_cta',
        'type'    => 'url',
    ));
    
    // =========================================================================
    // AFFILIATE SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_affiliate', array(
        'title'    => writgo_admin_t('affiliate_settings'),
        'priority' => 50,
    ));
    
    // Show Disclosure
    $wp_customize->add_setting('writgo_show_disclosure', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('writgo_show_disclosure', array(
        'label'   => writgo_admin_t('show_disclosure'),
        'section' => 'writgo_affiliate',
        'type'    => 'checkbox',
    ));
    
    // Disclosure Text
    $wp_customize->add_setting('writgo_disclosure_text', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control('writgo_disclosure_text', array(
        'label'       => writgo_admin_t('disclosure_text'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_affiliate',
        'type'        => 'textarea',
    ));
    
    // =========================================================================
    // COMPANY INFO SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_company', array(
        'title'       => writgo_admin_t('company_info'),
        'description' => writgo_admin_t('company_info_description'),
        'priority'    => 25,
    ));
    
    // Company Name
    $wp_customize->add_setting('writgo_company_name', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_company_name', array(
        'label'       => writgo_admin_t('company_name'),
        'section'     => 'writgo_company',
        'type'        => 'text',
    ));
    
    // Company Address
    $wp_customize->add_setting('writgo_company_address', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_company_address', array(
        'label'   => writgo_admin_t('street_address'),
        'section' => 'writgo_company',
        'type'    => 'text',
    ));
    
    // Postcode
    $wp_customize->add_setting('writgo_company_postcode', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_company_postcode', array(
        'label'   => writgo_admin_t('postal_code'),
        'section' => 'writgo_company',
        'type'    => 'text',
    ));
    
    // City
    $wp_customize->add_setting('writgo_company_city', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_company_city', array(
        'label'   => writgo_admin_t('city'),
        'section' => 'writgo_company',
        'type'    => 'text',
    ));
    
    // KvK Number
    $wp_customize->add_setting('writgo_kvk_nummer', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_kvk_nummer', array(
        'label'   => writgo_admin_t('kvk_number'),
        'section' => 'writgo_company',
        'type'    => 'text',
    ));
    
    // Contact Email
    $wp_customize->add_setting('writgo_contact_email', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('writgo_contact_email', array(
        'label'   => writgo_admin_t('email'),
        'section' => 'writgo_company',
        'type'    => 'email',
    ));
    
    // Contact Phone
    $wp_customize->add_setting('writgo_contact_phone', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_contact_phone', array(
        'label'   => writgo_admin_t('phone'),
        'section' => 'writgo_company',
        'type'    => 'text',
    ));
    
    // Footer Disclosure
    $wp_customize->add_setting('writgo_footer_disclosure', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_footer_disclosure', array(
        'label'       => writgo_admin_t('footer_disclosure'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_company',
        'type'        => 'textarea',
    ));
    
    // =========================================================================
    // SOCIAL MEDIA SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_social', array(
        'title'    => writgo_admin_t('social_media'),
        'priority' => 26,
    ));
    
    $social_networks = array(
        'facebook'  => 'Facebook URL',
        'instagram' => 'Instagram URL',
        'twitter'   => 'Twitter/X URL',
        'linkedin'  => 'LinkedIn URL',
        'youtube'   => 'YouTube URL',
        'pinterest' => 'Pinterest URL',
        'tiktok'    => 'TikTok URL',
    );
    
    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting('writgo_social_' . $network, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        $wp_customize->add_control('writgo_social_' . $network, array(
            'label'   => __($label, 'writgo-affiliate'),
            'section' => 'writgo_social',
            'type'    => 'url',
        ));
    }
    
    // =========================================================================
    // CONTACT PAGE SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_contact_page', array(
        'title'       => writgo_admin_t('contact_settings'),
        'description' => writgo_admin_t('contact_settings_desc'),
        'priority'    => 47,
    ));
    
    // Hero Title
    $wp_customize->add_setting('writgo_contact_hero_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_contact_hero_title', array(
        'label'       => writgo_admin_t('hero_title'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_contact_page',
        'type'        => 'text',
    ));
    
    // Hero Subtitle
    $wp_customize->add_setting('writgo_contact_hero_subtitle', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_contact_hero_subtitle', array(
        'label'       => writgo_admin_t('hero_subtitle'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_contact_page',
        'type'        => 'textarea',
    ));
    
    // Response Time
    $wp_customize->add_setting('writgo_contact_response_time', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_contact_response_time', array(
        'label'       => writgo_admin_t('response_time_text'),
        'description' => writgo_admin_t('leave_empty_auto'),
        'section'     => 'writgo_contact_page',
        'type'        => 'text',
    ));
    
    // Contact Form Shortcode
    $wp_customize->add_setting('writgo_contact_form_shortcode', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_contact_form_shortcode', array(
        'label'       => writgo_admin_t('form_shortcode'),
        'description' => writgo_admin_t('form_shortcode_desc'),
        'section'     => 'writgo_contact_page',
        'type'        => 'text',
    ));
    
    // FAQ Items
    for ($i = 1; $i <= 3; $i++) {
        $wp_customize->add_setting('writgo_contact_faq' . $i . '_q', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('writgo_contact_faq' . $i . '_q', array(
            'label'   => sprintf(writgo_admin_t('faq_question'), $i),
            'section' => 'writgo_contact_page',
            'type'    => 'text',
        ));
        
        $wp_customize->add_setting('writgo_contact_faq' . $i . '_a', array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        ));
        
        $wp_customize->add_control('writgo_contact_faq' . $i . '_a', array(
            'label'   => sprintf(writgo_admin_t('faq_answer'), $i),
            'section' => 'writgo_contact_page',
            'type'    => 'textarea',
        ));
    }
    
    // =========================================================================
    // AUTHOR BOX SECTION
    // =========================================================================
    $wp_customize->add_section('writgo_author_box', array(
        'title'       => writgo_admin_t('author_box'),
        'description' => writgo_admin_t('author_box_desc'),
        'priority'    => 48,
    ));
    
    // Enable Author Box
    $wp_customize->add_setting('writgo_author_box_enabled', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('writgo_author_box_enabled', array(
        'label'   => writgo_admin_t('show_author_box'),
        'section' => 'writgo_author_box',
        'type'    => 'checkbox',
    ));
    
    // Use Custom Author (instead of WP author)
    $wp_customize->add_setting('writgo_author_box_custom', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('writgo_author_box_custom', array(
        'label'       => writgo_admin_t('use_custom_author'),
        'description' => writgo_admin_t('use_custom_author_desc'),
        'section'     => 'writgo_author_box',
        'type'        => 'checkbox',
    ));
    
    // Custom Author Name
    $wp_customize->add_setting('writgo_author_name', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_author_name', array(
        'label'       => writgo_admin_t('author_team_name'),
        'description' => writgo_admin_t('author_name_desc'),
        'section'     => 'writgo_author_box',
        'type'        => 'text',
    ));
    
    // Author Title Prefix
    $wp_customize->add_setting('writgo_author_prefix', array(
        'default'           => 'Geschreven door',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_author_prefix', array(
        'label'       => writgo_admin_t('author_prefix'),
        'description' => writgo_admin_t('author_prefix_desc'),
        'section'     => 'writgo_author_box',
        'type'        => 'text',
    ));
    
    // Custom Author Bio
    $wp_customize->add_setting('writgo_author_bio', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control('writgo_author_bio', array(
        'label'       => writgo_admin_t('author_bio'),
        'description' => writgo_admin_t('author_bio_desc'),
        'section'     => 'writgo_author_box',
        'type'        => 'textarea',
    ));
    
    // Author Image
    $wp_customize->add_setting('writgo_author_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'writgo_author_image', array(
        'label'       => writgo_admin_t('author_image'),
        'description' => writgo_admin_t('author_image_desc'),
        'section'     => 'writgo_author_box',
    )));
    
    // Author Website URL
    $wp_customize->add_setting('writgo_author_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('writgo_author_url', array(
        'label'   => writgo_admin_t('author_website_url'),
        'section' => 'writgo_author_box',
        'type'    => 'url',
    ));
    
    // Author Box Style
    $wp_customize->add_setting('writgo_author_box_style', array(
        'default'           => 'horizontal',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('writgo_author_box_style', array(
        'label'   => writgo_admin_t('layout_style'),
        'section' => 'writgo_author_box',
        'type'    => 'select',
        'choices' => array(
            'horizontal' => writgo_admin_t('horizontal'),
            'vertical'   => writgo_admin_t('vertical'),
            'minimal'    => writgo_admin_t('minimal'),
        ),
    ));
    
    // Author Box Background
    $wp_customize->add_setting('writgo_author_box_bg', array(
        'default'           => '#f8fafc',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'writgo_author_box_bg', array(
        'label'   => writgo_admin_t('background_color'),
        'section' => 'writgo_author_box',
    )));
    
    // Show on Pages too
    $wp_customize->add_setting('writgo_author_box_pages', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('writgo_author_box_pages', array(
        'label'   => writgo_admin_t('show_on_pages'),
        'section' => 'writgo_author_box',
        'type'    => 'checkbox',
    ));
}

/**
 * Darken a hex color by a percentage
 */
function writgo_darken_color($hex, $percent = 20) {
    // Remove #
    $hex = ltrim($hex, '#');
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Darken
    $r = max(0, round($r * (100 - $percent) / 100));
    $g = max(0, round($g * (100 - $percent) / 100));
    $b = max(0, round($b * (100 - $percent) / 100));
    
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

/**
 * Lighten a hex color by a percentage
 */
function writgo_lighten_color($hex, $percent = 20) {
    // Remove #
    $hex = ltrim($hex, '#');
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Lighten
    $r = min(255, round($r + (255 - $r) * $percent / 100));
    $g = min(255, round($g + (255 - $g) * $percent / 100));
    $b = min(255, round($b + (255 - $b) * $percent / 100));
    
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

/**
 * Output Custom CSS Variables
 */
add_action('wp_head', 'writgo_custom_css');
function writgo_custom_css() {
    $container_width = get_theme_mod('writgo_container_width', 1400);
    $primary_color = get_theme_mod('writgo_primary_color', '#1a365d');
    $accent_color = get_theme_mod('writgo_accent_color', '#f97316');
    $logo_height = get_theme_mod('writgo_logo_height', 50);
    $author_box_bg = get_theme_mod('writgo_author_box_bg', '#f8fafc');
    
    // Calculate color variations
    $primary_dark = writgo_darken_color($primary_color, 15);
    $primary_light = writgo_lighten_color($primary_color, 15);
    $accent_dark = writgo_darken_color($accent_color, 15);
    $accent_light = writgo_lighten_color($accent_color, 15);
    
    echo '<style>
        :root {
            --wa-container-max: ' . intval($container_width) . 'px;
            --wa-primary: ' . esc_attr($primary_color) . ';
            --wa-primary-dark: ' . esc_attr($primary_dark) . ';
            --wa-primary-light: ' . esc_attr($primary_light) . ';
            --wa-accent: ' . esc_attr($accent_color) . ';
            --wa-accent-dark: ' . esc_attr($accent_dark) . ';
            --wa-accent-light: ' . esc_attr($accent_light) . ';
            --wa-logo-height: ' . intval($logo_height) . 'px;
            --wa-author-box-bg: ' . esc_attr($author_box_bg) . ';
        }
        .wa-logo img,
        .wa-logo .custom-logo {
            height: var(--wa-logo-height) !important;
            width: auto !important;
            max-width: 200px;
        }
    </style>';
}

// =============================================================================
// AUTHOR BOX FUNCTION
// =============================================================================

/**
 * Render the author box
 * Can be called in templates or via shortcode [writgo_author_box]
 */
function writgo_author_box($args = array()) {
    // Check if enabled
    if (!get_theme_mod('writgo_author_box_enabled', true)) {
        return;
    }
    
    // Check post type
    $show_on_pages = get_theme_mod('writgo_author_box_pages', false);
    if (is_page() && !$show_on_pages) {
        return;
    }
    
    // Get settings
    $use_custom = get_theme_mod('writgo_author_box_custom', false);
    $style = get_theme_mod('writgo_author_box_style', 'horizontal');
    $prefix = get_theme_mod('writgo_author_prefix', '');
    
    // Use translation if no custom prefix set
    if (empty($prefix)) {
        $prefix = writgo_t('written_by');
    }
    
    // Determine author info
    if ($use_custom) {
        $author_name = get_theme_mod('writgo_author_name', get_bloginfo('name'));
        $author_bio = get_theme_mod('writgo_author_bio', '');
        $author_image = get_theme_mod('writgo_author_image', '');
        $author_url = get_theme_mod('writgo_author_url', '');
    } else {
        // Use WordPress author
        $author_id = get_the_author_meta('ID');
        $author_name = get_the_author();
        $author_bio = get_the_author_meta('description');
        $author_image = get_avatar_url($author_id, array('size' => 150));
        $author_url = get_the_author_meta('url');
    }
    
    // Style class
    $style_class = 'wa-author-box--' . $style;
    
    ob_start();
    ?>
    <div class="wa-author-box <?php echo esc_attr($style_class); ?>">
        <?php if ($author_image && $style !== 'minimal') : ?>
            <div class="wa-author-box-image">
                <?php if ($author_url) : ?>
                    <a href="<?php echo esc_url($author_url); ?>" rel="author">
                        <img src="<?php echo esc_url($author_image); ?>" alt="<?php echo esc_attr($author_name); ?>" loading="lazy">
                    </a>
                <?php else : ?>
                    <img src="<?php echo esc_url($author_image); ?>" alt="<?php echo esc_attr($author_name); ?>" loading="lazy">
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="wa-author-box-content">
            <h4 class="wa-author-box-name">
                <?php if ($prefix) : ?>
                    <span class="wa-author-box-prefix"><?php echo esc_html($prefix); ?></span>
                <?php endif; ?>
                <?php if ($author_url) : ?>
                    <a href="<?php echo esc_url($author_url); ?>" rel="author"><?php echo esc_html($author_name); ?></a>
                <?php else : ?>
                    <?php echo esc_html($author_name); ?>
                <?php endif; ?>
            </h4>
            
            <?php if ($author_bio) : ?>
                <div class="wa-author-box-bio">
                    <?php echo wp_kses_post($author_bio); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Shortcode
add_shortcode('writgo_author_box', 'writgo_author_box_shortcode');
function writgo_author_box_shortcode($atts) {
    return writgo_author_box($atts);
}

/**
 * Add nofollow to external links
 */
add_filter('the_content', 'writgo_external_links');
function writgo_external_links($content) {
    $home_url = home_url();
    
    return preg_replace_callback(
        '/<a\s+([^>]*href=["\']([^"\']+)["\'][^>]*)>/i',
        function($matches) use ($home_url) {
            $url = $matches[2];
            $attrs = $matches[1];
            
            // Check if external
            if (strpos($url, $home_url) === false && strpos($url, 'http') === 0) {
                // Add rel="nofollow sponsored" if not already present
                if (strpos($attrs, 'rel=') === false) {
                    return '<a ' . $attrs . ' rel="nofollow sponsored" target="_blank">';
                }
            }
            
            return $matches[0];
        },
        $content
    );
}

/**
 * Set posts per page for archives
 */
add_action('pre_get_posts', 'writgo_posts_per_page');
function writgo_posts_per_page($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_home() || is_archive() || is_search()) {
            $query->set('posts_per_page', 12);
        }
    }
}

/**
 * Include additional modules
 */
$modules = array(
    '/inc/meta-boxes.php',
    '/inc/shortcodes.php',
    '/inc/affiliate-shortcodes.php',
    '/inc/legal-pages.php',
    '/inc/seo.php',
    '/inc/seo-schema.php',
    '/inc/seo-sitemap.php',
    '/inc/seo-analysis.php',
    '/inc/seo-social.php',
    '/inc/seo-technical.php',
    '/inc/seo-bulk.php',
    '/inc/seo-images.php',
    '/inc/writgo-dashboard.php',
    '/inc/theme-updater.php',
);

foreach ($modules as $module) {
    $file = WRITGO_DIR . $module;
    if (file_exists($file)) {
        require_once $file;
    }
}

// =============================================================================
// AJAX LIVE SEARCH FOR BLOG
// =============================================================================

add_action('wp_ajax_writgo_live_search', 'writgo_live_search_handler');
add_action('wp_ajax_nopriv_writgo_live_search', 'writgo_live_search_handler');

function writgo_live_search_handler() {
    $search = sanitize_text_field($_GET['s'] ?? '');
    
    if (strlen($search) < 2) {
        wp_send_json_error('Query too short');
    }
    
    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        's'              => $search,
        'posts_per_page' => 6,
        'orderby'        => 'relevance',
    );
    
    $query = new WP_Query($args);
    $results = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            $thumb = '';
            if (has_post_thumbnail()) {
                $thumb = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
            }
            
            $cats = get_the_category();
            $category = !empty($cats) ? $cats[0]->name : '';
            
            $results[] = array(
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'url'       => get_permalink(),
                'thumbnail' => $thumb,
                'date'      => get_the_date('j M Y'),
                'category'  => $category,
            );
        }
        wp_reset_postdata();
    }
    
    wp_send_json_success(array(
        'results'     => $results,
        'total'       => $query->found_posts,
        'search_url'  => home_url('/?s=' . urlencode($search) . '&post_type=post'),
    ));
}

// Add AJAX URL to footer for live search
add_action('wp_footer', 'writgo_live_search_script');
function writgo_live_search_script() {
    // Only on archive and blog pages
    if (!is_archive() && !is_home()) {
        return;
    }
    ?>
    <script>
    (function() {
        const searchInput = document.getElementById('blog-search-input');
        const resultsContainer = document.getElementById('live-search-results');
        
        if (!searchInput || !resultsContainer) return;
        
        let debounceTimer;
        let currentRequest = null;
        
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(debounceTimer);
            
            if (query.length < 2) {
                resultsContainer.classList.remove('active');
                resultsContainer.innerHTML = '';
                return;
            }
            
            debounceTimer = setTimeout(function() {
                performSearch(query);
            }, 300);
        });
        
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && resultsContainer.innerHTML) {
                resultsContainer.classList.add('active');
            }
        });
        
        // Close on click outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.wa-blog-search-form')) {
                resultsContainer.classList.remove('active');
            }
        });
        
        // Keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                resultsContainer.classList.remove('active');
            }
        });
        
        function performSearch(query) {
            // Cancel previous request
            if (currentRequest) {
                currentRequest.abort();
            }
            
            resultsContainer.innerHTML = '<div class="wa-live-search-loading">Zoeken...</div>';
            resultsContainer.classList.add('active');
            
            const controller = new AbortController();
            currentRequest = controller;
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=writgo_live_search&s=' + encodeURIComponent(query), {
                signal: controller.signal
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.results.length > 0) {
                    let html = '';
                    
                    data.data.results.forEach(function(item) {
                        html += '<a href="' + item.url + '" class="wa-live-search-item">';
                        if (item.thumbnail) {
                            html += '<img src="' + item.thumbnail + '" alt="" class="wa-live-search-thumb">';
                        }
                        html += '<div class="wa-live-search-content">';
                        html += '<div class="wa-live-search-title">' + item.title + '</div>';
                        html += '<div class="wa-live-search-meta">';
                        if (item.category) {
                            html += item.category + ' • ';
                        }
                        html += item.date + '</div>';
                        html += '</div></a>';
                    });
                    
                    if (data.data.total > 6) {
                        html += '<a href="' + data.data.search_url + '" class="wa-live-search-view-all">';
                        html += 'Bekijk alle ' + data.data.total + ' resultaten →</a>';
                    }
                    
                    resultsContainer.innerHTML = html;
                } else {
                    resultsContainer.innerHTML = '<div class="wa-live-search-no-results"><?php echo esc_js(writgo_t('no_results')); ?></div>';
                }
            })
            .catch(function(error) {
                if (error.name !== 'AbortError') {
                    resultsContainer.innerHTML = '<div class="wa-live-search-no-results">Er ging iets mis</div>';
                }
            });
        }
    })();
    </script>
    <?php
}

// =============================================================================
// REST API - REGISTER META FIELDS
// =============================================================================
// Dit maakt het mogelijk om alle Writgo SEO velden via de REST API te vullen
// Bijvoorbeeld via een AI writer of externe tool

add_action('init', 'writgo_register_rest_meta_fields');
function writgo_register_rest_meta_fields() {
    
    // Alle Writgo meta keys
    $meta_keys = array(
        // Basis SEO
        '_writgo_seo_title',
        '_writgo_seo_description',
        '_writgo_focus_keyword',
        '_writgo_secondary_keywords',
        '_writgo_canonical_url',
        '_writgo_noindex',
        '_writgo_nofollow',
        '_writgo_cornerstone',
        '_writgo_featured',
        '_writgo_score',
        
        // Schema
        '_writgo_schema_type',
        
        // Product Schema
        '_writgo_product_name',
        '_writgo_product_brand',
        '_writgo_product_price',
        '_writgo_product_currency',
        '_writgo_product_availability',
        '_writgo_product_condition',
        '_writgo_product_url',
        
        // Review Schema
        '_writgo_review_rating',
        '_writgo_review_pros',
        '_writgo_review_cons',
        
        // FAQ Schema
        '_writgo_faq_items',
        
        // HowTo Schema
        '_writgo_howto_time',
        '_writgo_howto_cost',
        
        // Social Media
        '_writgo_og_title',
        '_writgo_og_description',
        '_writgo_og_image',
        '_writgo_twitter_title',
        '_writgo_twitter_description',
        '_writgo_twitter_image',
        
        // Sticky CTA Bar
        '_writgo_sticky_button',
        '_writgo_sticky_title',
        '_writgo_sticky_price',
        '_writgo_sticky_cta',
        '_writgo_sticky_url',
    );
    
    // Registreer voor posts en pages
    $post_types = array('post', 'page');
    
    foreach ($post_types as $post_type) {
        foreach ($meta_keys as $meta_key) {
            register_post_meta($post_type, $meta_key, array(
                'show_in_rest'  => true,
                'single'        => true,
                'type'          => 'string',
                'auth_callback' => function() {
                    return current_user_can('edit_posts');
                },
            ));
        }
    }
}

// =============================================================================
// REST API - CUSTOM ENDPOINT VOOR BULK SEO UPDATE
// =============================================================================

add_action('rest_api_init', 'writgo_register_rest_routes');
function writgo_register_rest_routes() {

    // POST /wp-json/writgo/v1/switch-theme
    register_rest_route('writgo/v1', '/switch-theme', array(
        'methods'  => 'POST',
        'callback' => function($request) {
            $theme = $request->get_param('theme');
            if (!$theme) {
                return new WP_Error('missing_theme', 'Theme parameter required', array('status' => 400));
            }
            $theme_obj = wp_get_theme($theme);
            if (!$theme_obj->exists()) {
                return new WP_Error('theme_not_found', 'Theme not found: ' . $theme, array('status' => 404));
            }
            switch_theme($theme);
            return array('success' => true, 'active_theme' => get_stylesheet());
        },
        'permission_callback' => function() {
            return current_user_can('switch_themes');
        },
    ));

    // GET /wp-json/writgo/v1/debug
    register_rest_route('writgo/v1', '/debug', array(
        'methods'  => 'GET',
        'callback' => function() {
            return array(
                'php_version' => PHP_VERSION,
                'memory_limit' => ini_get('memory_limit'),
                'active_theme' => get_stylesheet(),
                'template' => get_template(),
                'wp_version' => get_bloginfo('version'),
                'menus' => get_nav_menu_locations(),
            );
        },
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
    ));

    // POST /wp-json/writgo/v1/fix-customizer
    register_rest_route('writgo/v1', '/fix-customizer', array(
        'methods'  => 'POST',
        'callback' => function() {
            global $wpdb;
            $deleted = $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'customize_changeset' AND post_status != 'publish'");
            wp_cache_flush();
            return array('success' => true, 'deleted_changesets' => $deleted);
        },
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
    ));

    // POST /wp-json/writgo/v1/update-theme-from-url
    register_rest_route('writgo/v1', '/update-theme-from-url', array(
        'methods'  => 'POST',
        'callback' => function($request) {
            $url = esc_url_raw($request->get_param('package_url'));
            if (!$url) return new WP_Error('missing', 'Need package_url', array('status' => 400));
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/misc.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            $skin = new WP_Ajax_Upgrader_Skin();
            $upgrader = new Theme_Upgrader($skin);
            $result = $upgrader->install($url, array('overwrite_package' => true));
            if (is_wp_error($result)) {
                return new WP_Error('failed', $result->get_error_message(), array('status' => 500));
            }
            return array('success' => true, 'result' => $result);
        },
        'permission_callback' => function() {
            return current_user_can('install_themes');
        },
    ));

    // GET /wp-json/writgo/v1/seo-fields
    // Retourneert lijst van alle beschikbare SEO velden
    register_rest_route('writgo/v1', '/seo-fields', array(
        'methods'  => 'GET',
        'callback' => 'writgo_rest_get_seo_fields',
        'permission_callback' => '__return_true',
    ));
    
    // GET /wp-json/writgo/v1/post/(?P<id>\d+)/seo
    // Haal SEO data op voor specifieke post
    register_rest_route('writgo/v1', '/post/(?P<id>\d+)/seo', array(
        'methods'  => 'GET',
        'callback' => 'writgo_rest_get_post_seo',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        },
    ));
    
    // POST /wp-json/writgo/v1/post/(?P<id>\d+)/seo
    // Update SEO data voor specifieke post
    register_rest_route('writgo/v1', '/post/(?P<id>\d+)/seo', array(
        'methods'  => 'POST',
        'callback' => 'writgo_rest_update_post_seo',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        },
    ));
}

/**
 * GET /wp-json/writgo/v1/seo-fields
 * Retourneert documentatie van alle SEO velden
 */
function writgo_rest_get_seo_fields() {
    return array(
        'version' => WRITGO_VERSION,
        'fields' => array(
            'basic_seo' => array(
                '_writgo_seo_title' => array(
                    'type' => 'string',
                    'description' => 'SEO titel (30-60 karakters)',
                    'example' => 'Beste Stofzuiger 2024 | Top 10 Getest',
                ),
                '_writgo_seo_description' => array(
                    'type' => 'string',
                    'description' => 'Meta description (120-160 karakters)',
                    'example' => 'Ontdek de beste stofzuigers van 2024...',
                ),
                '_writgo_focus_keyword' => array(
                    'type' => 'string',
                    'description' => 'Primaire focus keyword',
                    'example' => 'beste stofzuiger',
                ),
                '_writgo_secondary_keywords' => array(
                    'type' => 'string',
                    'description' => 'Secundaire keywords (komma-gescheiden)',
                    'example' => 'stofzuiger test, goede stofzuiger',
                ),
            ),
            'schema' => array(
                '_writgo_schema_type' => array(
                    'type' => 'string',
                    'description' => 'Type schema markup',
                    'options' => array('article', 'product', 'review', 'faq', 'howto'),
                ),
            ),
            'product' => array(
                '_writgo_product_name' => 'Productnaam',
                '_writgo_product_brand' => 'Merknaam',
                '_writgo_product_price' => 'Prijs (getal)',
                '_writgo_product_currency' => 'Valuta (EUR, USD)',
                '_writgo_product_availability' => 'InStock, OutOfStock, PreOrder',
                '_writgo_product_condition' => 'NewCondition, UsedCondition',
                '_writgo_product_url' => 'Affiliate URL',
            ),
            'review' => array(
                '_writgo_review_rating' => 'Score 1-5 (bijv. 4.5)',
                '_writgo_review_pros' => 'Voordelen (1 per regel)',
                '_writgo_review_cons' => 'Nadelen (1 per regel)',
            ),
            'faq' => array(
                '_writgo_faq_items' => 'JSON array: [{"question":"...","answer":"..."}]',
            ),
            'social' => array(
                '_writgo_og_title' => 'Facebook titel',
                '_writgo_og_description' => 'Facebook beschrijving',
                '_writgo_og_image' => 'Facebook afbeelding URL',
                '_writgo_twitter_title' => 'Twitter titel',
                '_writgo_twitter_description' => 'Twitter beschrijving',
                '_writgo_twitter_image' => 'Twitter afbeelding URL',
            ),
            'sticky_cta' => array(
                '_writgo_sticky_button' => '"1" om te tonen',
                '_writgo_sticky_title' => 'Product titel',
                '_writgo_sticky_price' => 'Prijs met valuta',
                '_writgo_sticky_cta' => 'Button tekst',
                '_writgo_sticky_url' => 'Affiliate URL',
            ),
        ),
    );
}

/**
 * GET /wp-json/writgo/v1/post/{id}/seo
 * Haal alle SEO data op voor een post
 */
function writgo_rest_get_post_seo($request) {
    $post_id = $request['id'];
    
    if (!get_post($post_id)) {
        return new WP_Error('not_found', 'Post niet gevonden', array('status' => 404));
    }
    
    $meta_keys = array(
        '_writgo_seo_title',
        '_writgo_seo_description',
        '_writgo_focus_keyword',
        '_writgo_secondary_keywords',
        '_writgo_canonical_url',
        '_writgo_noindex',
        '_writgo_nofollow',
        '_writgo_cornerstone',
        '_writgo_schema_type',
        '_writgo_product_name',
        '_writgo_product_brand',
        '_writgo_product_price',
        '_writgo_product_currency',
        '_writgo_product_availability',
        '_writgo_product_condition',
        '_writgo_product_url',
        '_writgo_review_rating',
        '_writgo_review_pros',
        '_writgo_review_cons',
        '_writgo_faq_items',
        '_writgo_howto_time',
        '_writgo_howto_cost',
        '_writgo_og_title',
        '_writgo_og_description',
        '_writgo_og_image',
        '_writgo_twitter_title',
        '_writgo_twitter_description',
        '_writgo_twitter_image',
        '_writgo_sticky_button',
        '_writgo_sticky_title',
        '_writgo_sticky_price',
        '_writgo_sticky_cta',
        '_writgo_sticky_url',
    );
    
    $data = array(
        'post_id' => $post_id,
        'post_title' => get_the_title($post_id),
        'seo' => array(),
    );
    
    foreach ($meta_keys as $key) {
        $value = get_post_meta($post_id, $key, true);
        // Verwijder _writgo_ prefix voor cleaner output
        $clean_key = str_replace('_writgo_', '', $key);
        $data['seo'][$clean_key] = $value;
    }
    
    return $data;
}

/**
 * POST /wp-json/writgo/v1/post/{id}/seo
 * Update SEO data voor een post
 */
function writgo_rest_update_post_seo($request) {
    $post_id = $request['id'];
    
    if (!get_post($post_id)) {
        return new WP_Error('not_found', 'Post niet gevonden', array('status' => 404));
    }
    
    $body = $request->get_json_params();
    $updated = array();
    
    // Toegestane velden
    $allowed_fields = array(
        'seo_title' => '_writgo_seo_title',
        'seo_description' => '_writgo_seo_description',
        'focus_keyword' => '_writgo_focus_keyword',
        'secondary_keywords' => '_writgo_secondary_keywords',
        'canonical_url' => '_writgo_canonical_url',
        'noindex' => '_writgo_noindex',
        'nofollow' => '_writgo_nofollow',
        'cornerstone' => '_writgo_cornerstone',
        'schema_type' => '_writgo_schema_type',
        'product_name' => '_writgo_product_name',
        'product_brand' => '_writgo_product_brand',
        'product_price' => '_writgo_product_price',
        'product_currency' => '_writgo_product_currency',
        'product_availability' => '_writgo_product_availability',
        'product_condition' => '_writgo_product_condition',
        'product_url' => '_writgo_product_url',
        'review_rating' => '_writgo_review_rating',
        'review_pros' => '_writgo_review_pros',
        'review_cons' => '_writgo_review_cons',
        'faq_items' => '_writgo_faq_items',
        'howto_time' => '_writgo_howto_time',
        'howto_cost' => '_writgo_howto_cost',
        'og_title' => '_writgo_og_title',
        'og_description' => '_writgo_og_description',
        'og_image' => '_writgo_og_image',
        'twitter_title' => '_writgo_twitter_title',
        'twitter_description' => '_writgo_twitter_description',
        'twitter_image' => '_writgo_twitter_image',
        'sticky_button' => '_writgo_sticky_button',
        'sticky_title' => '_writgo_sticky_title',
        'sticky_price' => '_writgo_sticky_price',
        'sticky_cta' => '_writgo_sticky_cta',
        'sticky_url' => '_writgo_sticky_url',
    );
    
    foreach ($body as $key => $value) {
        if (isset($allowed_fields[$key])) {
            $meta_key = $allowed_fields[$key];
            
            // Sanitize based on field type
            if (strpos($key, 'url') !== false) {
                $value = esc_url_raw($value);
            } elseif ($key === 'faq_items') {
                // Validate JSON
                if (is_array($value)) {
                    $value = wp_json_encode($value);
                }
            } else {
                $value = sanitize_text_field($value);
            }
            
            update_post_meta($post_id, $meta_key, $value);
            $updated[$key] = $value;
        }
    }
    
    return array(
        'success' => true,
        'post_id' => $post_id,
        'updated' => $updated,
    );
}
