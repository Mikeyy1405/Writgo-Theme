<?php
/**
 * Writgo Legal Pages - Auto-generated legal content (Multilingual)
 * 
 * Supports: NL, EN, DE, FR
 *
 * @package Writgo_Affiliate
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get legal page content with dynamic replacements
 */
function writgo_get_legal_content($page_type) {
    // Get site info for replacements
    $site_name = get_bloginfo('name');
    $company_name = get_theme_mod('writgo_company_name', $site_name);
    $company_address = get_theme_mod('writgo_company_address', '');
    $company_postcode = get_theme_mod('writgo_company_postcode', '');
    $company_city = get_theme_mod('writgo_company_city', '');
    $contact_email = get_theme_mod('writgo_contact_email', 'info@' . parse_url(home_url(), PHP_URL_HOST));
    $lang = function_exists('writgo_get_language') ? writgo_get_language() : 'nl';
    
    $content = '';
    
    switch ($page_type) {
        case 'disclaimer':
            $content = writgo_get_disclaimer_content($lang);
            break;
        case 'privacyverklaring':
        case 'privacy-policy':
        case 'datenschutz':
        case 'politique-de-confidentialite':
            $content = writgo_get_privacy_content($lang);
            break;
        case 'cookiebeleid':
        case 'cookie-policy':
        case 'cookie-richtlinie':
        case 'politique-cookies':
            $content = writgo_get_cookie_content($lang);
            break;
        case 'algemene-voorwaarden':
        case 'terms-conditions':
        case 'agb':
        case 'conditions-generales':
            $content = writgo_get_terms_content($lang);
            break;
    }
    
    // Date formats per language
    $date_formats = array(
        'nl' => 'j F Y',
        'en' => 'F j, Y',
        'de' => 'j. F Y',
        'fr' => 'j F Y',
    );
    $date_format = $date_formats[$lang] ?? 'j F Y';
    
    // Replace placeholders
    $replacements = array(
        '{{SITE_NAME}}' => $site_name,
        '{{COMPANY_NAME}}' => $company_name,
        '{{COMPANY_ADDRESS}}' => $company_address,
        '{{COMPANY_POSTCODE}}' => $company_postcode,
        '{{COMPANY_CITY}}' => $company_city,
        '{{CONTACT_EMAIL}}' => $contact_email,
        '{{YEAR}}' => date('Y'),
        '{{DATE}}' => date_i18n($date_format),
    );
    
    foreach ($replacements as $placeholder => $value) {
        $content = str_replace($placeholder, $value, $content);
    }
    
    return $content;
}

/**
 * Disclaimer Content - Multilingual
 */
function writgo_get_disclaimer_content($lang = 'nl') {
    $content = array(
        'nl' => '
<p><em>Laatst bijgewerkt: {{DATE}}</em></p>

<p>Deze website is eigendom van en wordt beheerd door {{COMPANY_NAME}}. Deze Disclaimer is van toepassing op onze dienst en website. Door gebruik te maken van onze website en de daarop gepubliceerde informatie en functionaliteiten verklaar jij je akkoord met de voorwaarden uit deze disclaimer.</p>

<h2>Algemene informatie</h2>
<p>De informatie op deze website is uitsluitend bedoeld als algemene informatie. Deze informatie is zorgvuldig samengesteld. Toch kunnen we geen garanties geven over de correctheid van de getoonde informatie en tips. Er kunnen geen rechten aan de gegevens op onze website worden ontleend.</p>

<p>Wij zijn niet aansprakelijk voor schade welke kan ontstaan als gevolg van onjuiste of incomplete informatie op deze website. Informatie verzonden aan ons via de e-mail of de website is niet beveiligd en wordt als niet-vertrouwelijk beschouwd. Elke actie die je onderneemt op basis van onze informatie en/of tips is voor eigen verantwoordelijkheid en op eigen risico.</p>

<h2>Copyright</h2>
<p>Alle rechten van intellectuele eigendom op deze website berusten uitsluitend bij ons. De informatie op deze website is uitsluitend bedoeld voor eigen gebruik. Het is de gebruiker van deze website niet toegestaan om (delen van) deze website te wijzigen of te bewerken, openbaar te maken, te vermenigvuldigen, tegen vergoeding beschikbaar te stellen aan derden of een link te creëren tussen de website van ons en een andere internetsite, zonder onze uitdrukkelijke schriftelijke toestemming.</p>

<h2>Affiliate links</h2>
<p>Op onze website maken wij gebruik van affiliate links. Door affiliate links op onze website te plaatsen ontvangen wij (meestal) een commissie over de producten die jij via onze website bij een andere aanbieder koopt. Deze commissie (kleine vergoeding) ontvangen wij voor het doorsturen van onze bezoekers.</p>

<p><strong>Belangrijk:</strong> Deze commissie heeft geen invloed op de prijs die jij betaalt. Onze reviews en aanbevelingen zijn altijd eerlijk en onafhankelijk.</p>

<h2>Wijzigingen</h2>
<p>We kunnen deze disclaimer van tijd tot tijd veranderen. Wijzigingen worden hier bijgewerkt. Controleer daarom regelmatig deze pagina, zodat je van wijzigingen op de hoogte bent.</p>

<h2>Contact</h2>
<p>Kom je op onze website onjuiste informatie tegen? Laat het ons weten via {{CONTACT_EMAIL}}.</p>
',

        'en' => '
<p><em>Last updated: {{DATE}}</em></p>

<p>This website is owned and operated by {{COMPANY_NAME}}. This Disclaimer applies to our service and website. By using our website and the information and features published on it, you agree to the terms of this disclaimer.</p>

<h2>General Information</h2>
<p>The information on this website is intended solely as general information. This information has been carefully compiled. However, we cannot guarantee the accuracy of the information and tips displayed. No rights can be derived from the data on our website.</p>

<p>We are not liable for any damage that may arise as a result of incorrect or incomplete information on this website. Information sent to us via email or the website is not secure and is considered non-confidential. Any action you take based on our information and/or tips is at your own responsibility and risk.</p>

<h2>Copyright</h2>
<p>All intellectual property rights on this website rest exclusively with us. The information on this website is intended solely for personal use. Users of this website are not permitted to modify, edit, publish, reproduce, make available to third parties for a fee, or create a link between our website and another website without our express written permission.</p>

<h2>Affiliate Links</h2>
<p>On our website, we use affiliate links. By placing affiliate links on our website, we (usually) receive a commission on products that you purchase from another provider through our website. We receive this commission (small fee) for referring our visitors.</p>

<p><strong>Important:</strong> This commission does not affect the price you pay. Our reviews and recommendations are always honest and independent.</p>

<h2>Changes</h2>
<p>We may change this disclaimer from time to time. Changes will be updated here. Therefore, check this page regularly so that you are aware of any changes.</p>

<h2>Contact</h2>
<p>If you find incorrect information on our website, please let us know via {{CONTACT_EMAIL}}.</p>
',

        'de' => '
<p><em>Zuletzt aktualisiert: {{DATE}}</em></p>

<p>Diese Website ist Eigentum von {{COMPANY_NAME}} und wird von diesem betrieben. Dieser Haftungsausschluss gilt für unseren Service und unsere Website. Durch die Nutzung unserer Website und der darauf veröffentlichten Informationen und Funktionen erklären Sie sich mit den Bedingungen dieses Haftungsausschlusses einverstanden.</p>

<h2>Allgemeine Informationen</h2>
<p>Die Informationen auf dieser Website dienen ausschließlich als allgemeine Information. Diese Informationen wurden sorgfältig zusammengestellt. Wir können jedoch keine Garantie für die Richtigkeit der angezeigten Informationen und Tipps geben. Aus den Daten auf unserer Website können keine Rechte abgeleitet werden.</p>

<p>Wir haften nicht für Schäden, die durch falsche oder unvollständige Informationen auf dieser Website entstehen können. An uns per E-Mail oder über die Website gesendete Informationen sind nicht gesichert und gelten als nicht vertraulich. Jede Handlung, die Sie auf der Grundlage unserer Informationen und/oder Tipps vornehmen, erfolgt auf eigene Verantwortung und eigenes Risiko.</p>

<h2>Urheberrecht</h2>
<p>Alle geistigen Eigentumsrechte an dieser Website liegen ausschließlich bei uns. Die Informationen auf dieser Website sind ausschließlich für den persönlichen Gebrauch bestimmt. Es ist den Nutzern dieser Website nicht gestattet, diese Website ohne unsere ausdrückliche schriftliche Genehmigung zu ändern, zu bearbeiten, zu veröffentlichen, zu vervielfältigen, Dritten gegen Entgelt zur Verfügung zu stellen oder einen Link zwischen unserer Website und einer anderen Website zu erstellen.</p>

<h2>Affiliate-Links</h2>
<p>Auf unserer Website verwenden wir Affiliate-Links. Durch das Platzieren von Affiliate-Links auf unserer Website erhalten wir (in der Regel) eine Provision für Produkte, die Sie über unsere Website bei einem anderen Anbieter kaufen. Diese Provision (kleine Vergütung) erhalten wir für die Weiterleitung unserer Besucher.</p>

<p><strong>Wichtig:</strong> Diese Provision hat keinen Einfluss auf den Preis, den Sie zahlen. Unsere Bewertungen und Empfehlungen sind immer ehrlich und unabhängig.</p>

<h2>Änderungen</h2>
<p>Wir können diesen Haftungsausschluss von Zeit zu Zeit ändern. Änderungen werden hier aktualisiert. Überprüfen Sie diese Seite daher regelmäßig, um über Änderungen informiert zu sein.</p>

<h2>Kontakt</h2>
<p>Wenn Sie auf unserer Website falsche Informationen finden, teilen Sie uns dies bitte über {{CONTACT_EMAIL}} mit.</p>
',

        'fr' => '
<p><em>Dernière mise à jour : {{DATE}}</em></p>

<p>Ce site web est la propriété de {{COMPANY_NAME}} et est exploité par celui-ci. Cette clause de non-responsabilité s\'applique à notre service et à notre site web. En utilisant notre site web et les informations et fonctionnalités qui y sont publiées, vous acceptez les termes de cette clause de non-responsabilité.</p>

<h2>Informations générales</h2>
<p>Les informations sur ce site web sont destinées uniquement à titre d\'information générale. Ces informations ont été soigneusement compilées. Cependant, nous ne pouvons pas garantir l\'exactitude des informations et des conseils affichés. Aucun droit ne peut être dérivé des données de notre site web.</p>

<p>Nous ne sommes pas responsables des dommages pouvant résulter d\'informations incorrectes ou incomplètes sur ce site web. Les informations qui nous sont envoyées par e-mail ou via le site web ne sont pas sécurisées et sont considérées comme non confidentielles. Toute action que vous entreprenez sur la base de nos informations et/ou conseils est à vos propres risques et responsabilités.</p>

<h2>Droits d\'auteur</h2>
<p>Tous les droits de propriété intellectuelle sur ce site web appartiennent exclusivement à nous. Les informations sur ce site web sont destinées uniquement à un usage personnel. Il est interdit aux utilisateurs de ce site web de modifier, éditer, publier, reproduire, mettre à disposition de tiers contre rémunération ou créer un lien entre notre site web et un autre site sans notre autorisation écrite expresse.</p>

<h2>Liens d\'affiliation</h2>
<p>Sur notre site web, nous utilisons des liens d\'affiliation. En plaçant des liens d\'affiliation sur notre site web, nous recevons (généralement) une commission sur les produits que vous achetez auprès d\'un autre fournisseur via notre site web. Nous recevons cette commission (petite rémunération) pour avoir référé nos visiteurs.</p>

<p><strong>Important :</strong> Cette commission n\'affecte pas le prix que vous payez. Nos avis et recommandations sont toujours honnêtes et indépendants.</p>

<h2>Modifications</h2>
<p>Nous pouvons modifier cette clause de non-responsabilité de temps à autre. Les modifications seront mises à jour ici. Par conséquent, vérifiez régulièrement cette page pour être informé des modifications.</p>

<h2>Contact</h2>
<p>Si vous trouvez des informations incorrectes sur notre site web, veuillez nous en informer via {{CONTACT_EMAIL}}.</p>
',
    );
    
    return $content[$lang] ?? $content['en'];
}

/**
 * Privacy Policy Content - Multilingual
 */
function writgo_get_privacy_content($lang = 'nl') {
    $content = array(
        'nl' => '
<p><em>Laatst bijgewerkt: {{DATE}}</em></p>

<p>Door gebruik te maken van ons platform verwerken wij jouw persoonsgegevens. In deze privacyverklaring staat beschreven welke persoonsgegevens wij verwerken en hoe wij deze gegevens verwerken.</p>

<h2>Persoonsgegevens die wij verwerken</h2>
<p>Wij verwerken persoonsgegevens die jij ten behoeve van onze dienstverlening aan ons verstrekt:</p>

<p><strong>Contactgegevens:</strong> wanneer je het contactformulier op de website invult verwerken wij de persoonsgegevens die jij aan ons verstrekt, zoals je naam, e-mailadres en de inhoud van het bericht.</p>

<p><strong>Gebruiksgegevens:</strong> door onze website te bezoeken verwerken wij gegevens over hoe jij op onze website terecht bent gekomen, welke delen van de website je hebt bezocht, de datum en duur van je bezoek, jouw geanonimiseerde IP-adres, informatie over het apparaat waarop jij onze website bezocht.</p>

<h2>Bewaartermijnen</h2>
<p>Wij bewaren je persoonsgegevens niet langer dan strikt noodzakelijk is voor de doeleinden waarvoor de gegevens zijn verzameld. In de meeste gevallen bewaren wij gegevens maximaal 2 jaar.</p>

<h2>Delen met derden</h2>
<p>Wij verkopen jouw gegevens niet aan derden. Wij delen alleen gegevens met derden als dit noodzakelijk is voor onze dienstverlening of wanneer wij hiertoe wettelijk verplicht zijn.</p>

<h2>Beveiliging</h2>
<p>Wij nemen de bescherming van jouw gegevens serieus en nemen passende maatregelen om misbruik, verlies, onbevoegde toegang, ongewenste openbaarmaking en ongeoorloofde wijziging tegen te gaan.</p>

<h2>Jouw rechten</h2>
<p>Je hebt het recht om je persoonsgegevens in te zien, te corrigeren of te verwijderen. Daarnaast heb je het recht om bezwaar te maken tegen de verwerking van je persoonsgegevens. Je kunt een verzoek tot inzage, correctie, verwijdering of bezwaar sturen naar {{CONTACT_EMAIL}}.</p>

<h2>Contact</h2>
<p>Heb je vragen over deze privacyverklaring? Neem dan contact met ons op via {{CONTACT_EMAIL}}.</p>
',

        'en' => '
<p><em>Last updated: {{DATE}}</em></p>

<p>By using our platform, we process your personal data. This privacy statement describes which personal data we process and how we process this data.</p>

<h2>Personal Data We Process</h2>
<p>We process personal data that you provide to us for our services:</p>

<p><strong>Contact information:</strong> when you fill in the contact form on the website, we process the personal data you provide to us, such as your name, email address, and the content of the message.</p>

<p><strong>Usage data:</strong> by visiting our website, we process data about how you arrived at our website, which parts of the website you visited, the date and duration of your visit, your anonymized IP address, and information about the device on which you visited our website.</p>

<h2>Retention Periods</h2>
<p>We do not retain your personal data longer than strictly necessary for the purposes for which the data was collected. In most cases, we retain data for a maximum of 2 years.</p>

<h2>Sharing with Third Parties</h2>
<p>We do not sell your data to third parties. We only share data with third parties if this is necessary for our services or when we are legally obligated to do so.</p>

<h2>Security</h2>
<p>We take the protection of your data seriously and take appropriate measures to prevent misuse, loss, unauthorized access, unwanted disclosure, and unauthorized modification.</p>

<h2>Your Rights</h2>
<p>You have the right to access, correct, or delete your personal data. You also have the right to object to the processing of your personal data. You can send a request for access, correction, deletion, or objection to {{CONTACT_EMAIL}}.</p>

<h2>Contact</h2>
<p>Do you have questions about this privacy statement? Please contact us via {{CONTACT_EMAIL}}.</p>
',

        'de' => '
<p><em>Zuletzt aktualisiert: {{DATE}}</em></p>

<p>Durch die Nutzung unserer Plattform verarbeiten wir Ihre personenbezogenen Daten. In dieser Datenschutzerklärung wird beschrieben, welche personenbezogenen Daten wir verarbeiten und wie wir diese Daten verarbeiten.</p>

<h2>Personenbezogene Daten, die wir verarbeiten</h2>
<p>Wir verarbeiten personenbezogene Daten, die Sie uns für unsere Dienstleistungen zur Verfügung stellen:</p>

<p><strong>Kontaktdaten:</strong> Wenn Sie das Kontaktformular auf der Website ausfüllen, verarbeiten wir die personenbezogenen Daten, die Sie uns mitteilen, wie Ihren Namen, Ihre E-Mail-Adresse und den Inhalt der Nachricht.</p>

<p><strong>Nutzungsdaten:</strong> Durch den Besuch unserer Website verarbeiten wir Daten darüber, wie Sie auf unsere Website gekommen sind, welche Teile der Website Sie besucht haben, das Datum und die Dauer Ihres Besuchs, Ihre anonymisierte IP-Adresse und Informationen über das Gerät, mit dem Sie unsere Website besucht haben.</p>

<h2>Aufbewahrungsfristen</h2>
<p>Wir bewahren Ihre personenbezogenen Daten nicht länger auf, als es für die Zwecke, für die die Daten erhoben wurden, unbedingt erforderlich ist. In den meisten Fällen bewahren wir Daten maximal 2 Jahre auf.</p>

<h2>Weitergabe an Dritte</h2>
<p>Wir verkaufen Ihre Daten nicht an Dritte. Wir geben Daten nur an Dritte weiter, wenn dies für unsere Dienstleistungen erforderlich ist oder wenn wir gesetzlich dazu verpflichtet sind.</p>

<h2>Sicherheit</h2>
<p>Wir nehmen den Schutz Ihrer Daten ernst und ergreifen geeignete Maßnahmen, um Missbrauch, Verlust, unbefugten Zugriff, unerwünschte Offenlegung und unbefugte Änderung zu verhindern.</p>

<h2>Ihre Rechte</h2>
<p>Sie haben das Recht, Ihre personenbezogenen Daten einzusehen, zu korrigieren oder zu löschen. Sie haben auch das Recht, der Verarbeitung Ihrer personenbezogenen Daten zu widersprechen. Sie können eine Anfrage zur Einsicht, Korrektur, Löschung oder einen Widerspruch an {{CONTACT_EMAIL}} senden.</p>

<h2>Kontakt</h2>
<p>Haben Sie Fragen zu dieser Datenschutzerklärung? Kontaktieren Sie uns bitte über {{CONTACT_EMAIL}}.</p>
',

        'fr' => '
<p><em>Dernière mise à jour : {{DATE}}</em></p>

<p>En utilisant notre plateforme, nous traitons vos données personnelles. Cette déclaration de confidentialité décrit quelles données personnelles nous traitons et comment nous les traitons.</p>

<h2>Données personnelles que nous traitons</h2>
<p>Nous traitons les données personnelles que vous nous fournissez pour nos services :</p>

<p><strong>Coordonnées :</strong> lorsque vous remplissez le formulaire de contact sur le site web, nous traitons les données personnelles que vous nous fournissez, telles que votre nom, votre adresse e-mail et le contenu du message.</p>

<p><strong>Données d\'utilisation :</strong> en visitant notre site web, nous traitons des données sur la façon dont vous êtes arrivé sur notre site web, les parties du site que vous avez visitées, la date et la durée de votre visite, votre adresse IP anonymisée et des informations sur l\'appareil avec lequel vous avez visité notre site web.</p>

<h2>Durées de conservation</h2>
<p>Nous ne conservons pas vos données personnelles plus longtemps que strictement nécessaire aux fins pour lesquelles les données ont été collectées. Dans la plupart des cas, nous conservons les données pendant un maximum de 2 ans.</p>

<h2>Partage avec des tiers</h2>
<p>Nous ne vendons pas vos données à des tiers. Nous ne partageons des données avec des tiers que si cela est nécessaire pour nos services ou lorsque nous y sommes légalement obligés.</p>

<h2>Sécurité</h2>
<p>Nous prenons la protection de vos données au sérieux et prenons les mesures appropriées pour prévenir les abus, les pertes, les accès non autorisés, les divulgations indésirables et les modifications non autorisées.</p>

<h2>Vos droits</h2>
<p>Vous avez le droit d\'accéder à vos données personnelles, de les corriger ou de les supprimer. Vous avez également le droit de vous opposer au traitement de vos données personnelles. Vous pouvez envoyer une demande d\'accès, de correction, de suppression ou d\'opposition à {{CONTACT_EMAIL}}.</p>

<h2>Contact</h2>
<p>Avez-vous des questions concernant cette déclaration de confidentialité ? Veuillez nous contacter via {{CONTACT_EMAIL}}.</p>
',
    );
    
    return $content[$lang] ?? $content['en'];
}

/**
 * Cookie Policy Content - Multilingual
 */
function writgo_get_cookie_content($lang = 'nl') {
    $content = array(
        'nl' => '
<p><em>Laatst bijgewerkt: {{DATE}}</em></p>

<p>Op onze website maken wij gebruik van cookies. Cookies zijn kleine gegevensbestanden die door je bezoek aan onze website op jouw apparaat worden geplaatst.</p>

<h2>Gebruik van cookies</h2>
<p>Dankzij cookies kunnen wij jouw browser herkennen wanneer je een volgende keer onze website bezoekt. Hierdoor kunnen wij het gebruiksgemak van onze website voor je vergroten en optimaliseren wij hiermee de werking van onze website. Tevens gebruiken wij cookies om onze affiliate diensten aan te kunnen bieden.</p>

<h2>Soorten cookies</h2>
<p><strong>Functionele cookies:</strong> Deze cookies zijn nodig om de website goed te laten functioneren.</p>
<p><strong>Analytische cookies:</strong> Deze cookies helpen ons om het gebruik van de website te analyseren en te verbeteren.</p>
<p><strong>Marketing cookies:</strong> Deze cookies worden gebruikt voor affiliate tracking en om gepersonaliseerde advertenties te tonen.</p>

<h2>Google Analytics</h2>
<p>Onze website maakt gebruik van Google Analytics, een webanalyse-service. Google Analytics gebruikt cookies om te analyseren hoe bezoekers onze website gebruiken. Dit helpt ons om de werking van de website te verbeteren.</p>

<h2>Cookies beheren</h2>
<p>Je kunt cookies uitschakelen in je browserinstellingen. Houd er rekening mee dat sommige functies van onze website mogelijk niet goed werken als je cookies uitschakelt.</p>

<h2>Wijzigingen</h2>
<p>Wij behouden ons het recht voor om wijzigingen aan te brengen in dit cookiebeleid.</p>

<h2>Contact</h2>
<p>Heb je vragen over ons cookiebeleid? Neem dan contact met ons op via {{CONTACT_EMAIL}}.</p>
',

        'en' => '
<p><em>Last updated: {{DATE}}</em></p>

<p>Our website uses cookies. Cookies are small data files that are placed on your device when you visit our website.</p>

<h2>Use of Cookies</h2>
<p>Thanks to cookies, we can recognize your browser when you visit our website again. This allows us to improve your user experience and optimize the functionality of our website. We also use cookies to provide our affiliate services.</p>

<h2>Types of Cookies</h2>
<p><strong>Functional cookies:</strong> These cookies are necessary for the website to function properly.</p>
<p><strong>Analytical cookies:</strong> These cookies help us analyze and improve the use of the website.</p>
<p><strong>Marketing cookies:</strong> These cookies are used for affiliate tracking and to show personalized advertisements.</p>

<h2>Google Analytics</h2>
<p>Our website uses Google Analytics, a web analytics service. Google Analytics uses cookies to analyze how visitors use our website. This helps us improve the functionality of the website.</p>

<h2>Managing Cookies</h2>
<p>You can disable cookies in your browser settings. Please note that some features of our website may not function properly if you disable cookies.</p>

<h2>Changes</h2>
<p>We reserve the right to make changes to this cookie policy.</p>

<h2>Contact</h2>
<p>Do you have questions about our cookie policy? Please contact us via {{CONTACT_EMAIL}}.</p>
',

        'de' => '
<p><em>Zuletzt aktualisiert: {{DATE}}</em></p>

<p>Unsere Website verwendet Cookies. Cookies sind kleine Datendateien, die beim Besuch unserer Website auf Ihrem Gerät platziert werden.</p>

<h2>Verwendung von Cookies</h2>
<p>Dank Cookies können wir Ihren Browser erkennen, wenn Sie unsere Website erneut besuchen. Dies ermöglicht es uns, Ihr Benutzererlebnis zu verbessern und die Funktionalität unserer Website zu optimieren. Wir verwenden Cookies auch, um unsere Affiliate-Dienste anzubieten.</p>

<h2>Arten von Cookies</h2>
<p><strong>Funktionale Cookies:</strong> Diese Cookies sind für das ordnungsgemäße Funktionieren der Website erforderlich.</p>
<p><strong>Analytische Cookies:</strong> Diese Cookies helfen uns, die Nutzung der Website zu analysieren und zu verbessern.</p>
<p><strong>Marketing-Cookies:</strong> Diese Cookies werden für Affiliate-Tracking und zur Anzeige personalisierter Werbung verwendet.</p>

<h2>Google Analytics</h2>
<p>Unsere Website verwendet Google Analytics, einen Webanalysedienst. Google Analytics verwendet Cookies, um zu analysieren, wie Besucher unsere Website nutzen. Dies hilft uns, die Funktionalität der Website zu verbessern.</p>

<h2>Cookies verwalten</h2>
<p>Sie können Cookies in Ihren Browsereinstellungen deaktivieren. Bitte beachten Sie, dass einige Funktionen unserer Website möglicherweise nicht ordnungsgemäß funktionieren, wenn Sie Cookies deaktivieren.</p>

<h2>Änderungen</h2>
<p>Wir behalten uns das Recht vor, Änderungen an dieser Cookie-Richtlinie vorzunehmen.</p>

<h2>Kontakt</h2>
<p>Haben Sie Fragen zu unserer Cookie-Richtlinie? Kontaktieren Sie uns bitte über {{CONTACT_EMAIL}}.</p>
',

        'fr' => '
<p><em>Dernière mise à jour : {{DATE}}</em></p>

<p>Notre site web utilise des cookies. Les cookies sont de petits fichiers de données qui sont placés sur votre appareil lorsque vous visitez notre site web.</p>

<h2>Utilisation des cookies</h2>
<p>Grâce aux cookies, nous pouvons reconnaître votre navigateur lorsque vous visitez à nouveau notre site web. Cela nous permet d\'améliorer votre expérience utilisateur et d\'optimiser la fonctionnalité de notre site web. Nous utilisons également des cookies pour fournir nos services d\'affiliation.</p>

<h2>Types de cookies</h2>
<p><strong>Cookies fonctionnels :</strong> Ces cookies sont nécessaires au bon fonctionnement du site web.</p>
<p><strong>Cookies analytiques :</strong> Ces cookies nous aident à analyser et à améliorer l\'utilisation du site web.</p>
<p><strong>Cookies marketing :</strong> Ces cookies sont utilisés pour le suivi des affiliés et pour afficher des publicités personnalisées.</p>

<h2>Google Analytics</h2>
<p>Notre site web utilise Google Analytics, un service d\'analyse web. Google Analytics utilise des cookies pour analyser la façon dont les visiteurs utilisent notre site web. Cela nous aide à améliorer la fonctionnalité du site web.</p>

<h2>Gestion des cookies</h2>
<p>Vous pouvez désactiver les cookies dans les paramètres de votre navigateur. Veuillez noter que certaines fonctionnalités de notre site web peuvent ne pas fonctionner correctement si vous désactivez les cookies.</p>

<h2>Modifications</h2>
<p>Nous nous réservons le droit d\'apporter des modifications à cette politique de cookies.</p>

<h2>Contact</h2>
<p>Avez-vous des questions concernant notre politique de cookies ? Veuillez nous contacter via {{CONTACT_EMAIL}}.</p>
',
    );
    
    return $content[$lang] ?? $content['en'];
}

/**
 * Terms and Conditions Content - Multilingual
 */
function writgo_get_terms_content($lang = 'nl') {
    $content = array(
        'nl' => '
<p><em>Laatst bijgewerkt: {{DATE}}</em></p>

<h2>1. Definities</h2>
<p>In deze Algemene voorwaarden wordt verstaan onder:</p>
<ul>
<li><strong>Platform:</strong> de website(s), social media en andere tools van {{COMPANY_NAME}}.</li>
<li><strong>Gebruiker:</strong> iedere persoon die van het Platform gebruik maakt.</li>
<li><strong>Diensten:</strong> de informatie en diensten die via het Platform worden aangeboden.</li>
</ul>

<h2>2. Toepasselijkheid</h2>
<p>Deze Algemene voorwaarden zijn van toepassing op alle rechtsbetrekkingen met {{COMPANY_NAME}}. Door gebruik te maken van het Platform aanvaardt de Gebruiker deze Algemene voorwaarden.</p>

<h2>3. Affiliate diensten</h2>
<p>{{COMPANY_NAME}} publiceert informatie en aanbiedingen namens derden op het Platform. Wij ontvangen een commissie wanneer je via onze links producten of diensten aanschaft bij andere aanbieders. Dit heeft geen invloed op de prijs die je betaalt.</p>

<h2>4. Gebruik van het Platform</h2>
<p>Het is niet toegestaan om het Platform te gebruiken voor handelingen die in strijd zijn met de wet of openbare orde. Het is voorts niet toegestaan om het Platform te gebruiken voor commerciële doeleinden zonder onze toestemming.</p>

<h2>5. Intellectueel eigendom</h2>
<p>Alle rechten van intellectuele eigendom op de inhoud van het Platform berusten bij {{COMPANY_NAME}}. Het is niet toegestaan om zonder toestemming inhoud te kopiëren of te verspreiden.</p>

<h2>6. Aansprakelijkheid</h2>
<p>Gebruikers gebruiken het Platform voor eigen rekening en risico. {{COMPANY_NAME}} is niet aansprakelijk voor indirecte schade of gevolgschade.</p>

<h2>7. Wijzigingen</h2>
<p>{{COMPANY_NAME}} heeft het recht om deze voorwaarden te wijzigen.</p>

<h2>8. Toepasselijk recht</h2>
<p>Op deze Algemene voorwaarden is Nederlands recht van toepassing.</p>

<h2>Contact</h2>
<p>Heb je vragen? Neem dan contact met ons op via {{CONTACT_EMAIL}}.</p>
',

        'en' => '
<p><em>Last updated: {{DATE}}</em></p>

<h2>1. Definitions</h2>
<p>In these Terms and Conditions, the following terms are defined as:</p>
<ul>
<li><strong>Platform:</strong> the website(s), social media, and other tools of {{COMPANY_NAME}}.</li>
<li><strong>User:</strong> any person who uses the Platform.</li>
<li><strong>Services:</strong> the information and services offered through the Platform.</li>
</ul>

<h2>2. Applicability</h2>
<p>These Terms and Conditions apply to all legal relationships with {{COMPANY_NAME}}. By using the Platform, the User accepts these Terms and Conditions.</p>

<h2>3. Affiliate Services</h2>
<p>{{COMPANY_NAME}} publishes information and offers on behalf of third parties on the Platform. We receive a commission when you purchase products or services from other providers through our links. This does not affect the price you pay.</p>

<h2>4. Use of the Platform</h2>
<p>It is not permitted to use the Platform for actions that are contrary to the law or public order. It is also not permitted to use the Platform for commercial purposes without our permission.</p>

<h2>5. Intellectual Property</h2>
<p>All intellectual property rights to the content of the Platform belong to {{COMPANY_NAME}}. It is not permitted to copy or distribute content without permission.</p>

<h2>6. Liability</h2>
<p>Users use the Platform at their own expense and risk. {{COMPANY_NAME}} is not liable for indirect or consequential damages.</p>

<h2>7. Changes</h2>
<p>{{COMPANY_NAME}} has the right to change these terms.</p>

<h2>8. Applicable Law</h2>
<p>These Terms and Conditions are governed by the laws of the Netherlands.</p>

<h2>Contact</h2>
<p>Do you have questions? Please contact us via {{CONTACT_EMAIL}}.</p>
',

        'de' => '
<p><em>Zuletzt aktualisiert: {{DATE}}</em></p>

<h2>1. Definitionen</h2>
<p>In diesen Allgemeinen Geschäftsbedingungen werden folgende Begriffe definiert:</p>
<ul>
<li><strong>Plattform:</strong> die Website(s), Social Media und andere Tools von {{COMPANY_NAME}}.</li>
<li><strong>Nutzer:</strong> jede Person, die die Plattform nutzt.</li>
<li><strong>Dienste:</strong> die über die Plattform angebotenen Informationen und Dienste.</li>
</ul>

<h2>2. Geltungsbereich</h2>
<p>Diese Allgemeinen Geschäftsbedingungen gelten für alle Rechtsbeziehungen mit {{COMPANY_NAME}}. Durch die Nutzung der Plattform akzeptiert der Nutzer diese Allgemeinen Geschäftsbedingungen.</p>

<h2>3. Affiliate-Dienste</h2>
<p>{{COMPANY_NAME}} veröffentlicht Informationen und Angebote im Auftrag von Dritten auf der Plattform. Wir erhalten eine Provision, wenn Sie Produkte oder Dienstleistungen über unsere Links bei anderen Anbietern erwerben. Dies hat keinen Einfluss auf den Preis, den Sie zahlen.</p>

<h2>4. Nutzung der Plattform</h2>
<p>Es ist nicht gestattet, die Plattform für Handlungen zu nutzen, die gegen das Gesetz oder die öffentliche Ordnung verstoßen. Es ist auch nicht gestattet, die Plattform ohne unsere Genehmigung für kommerzielle Zwecke zu nutzen.</p>

<h2>5. Geistiges Eigentum</h2>
<p>Alle geistigen Eigentumsrechte am Inhalt der Plattform gehören {{COMPANY_NAME}}. Es ist nicht gestattet, Inhalte ohne Genehmigung zu kopieren oder zu verbreiten.</p>

<h2>6. Haftung</h2>
<p>Nutzer nutzen die Plattform auf eigene Kosten und Gefahr. {{COMPANY_NAME}} haftet nicht für indirekte Schäden oder Folgeschäden.</p>

<h2>7. Änderungen</h2>
<p>{{COMPANY_NAME}} hat das Recht, diese Bedingungen zu ändern.</p>

<h2>8. Anwendbares Recht</h2>
<p>Auf diese Allgemeinen Geschäftsbedingungen ist niederländisches Recht anwendbar.</p>

<h2>Kontakt</h2>
<p>Haben Sie Fragen? Kontaktieren Sie uns bitte über {{CONTACT_EMAIL}}.</p>
',

        'fr' => '
<p><em>Dernière mise à jour : {{DATE}}</em></p>

<h2>1. Définitions</h2>
<p>Dans les présentes Conditions Générales, les termes suivants sont définis comme suit :</p>
<ul>
<li><strong>Plateforme :</strong> le(s) site(s) web, les réseaux sociaux et autres outils de {{COMPANY_NAME}}.</li>
<li><strong>Utilisateur :</strong> toute personne qui utilise la Plateforme.</li>
<li><strong>Services :</strong> les informations et services offerts via la Plateforme.</li>
</ul>

<h2>2. Applicabilité</h2>
<p>Les présentes Conditions Générales s\'appliquent à toutes les relations juridiques avec {{COMPANY_NAME}}. En utilisant la Plateforme, l\'Utilisateur accepte les présentes Conditions Générales.</p>

<h2>3. Services d\'affiliation</h2>
<p>{{COMPANY_NAME}} publie des informations et des offres pour le compte de tiers sur la Plateforme. Nous recevons une commission lorsque vous achetez des produits ou des services auprès d\'autres fournisseurs via nos liens. Cela n\'affecte pas le prix que vous payez.</p>

<h2>4. Utilisation de la Plateforme</h2>
<p>Il est interdit d\'utiliser la Plateforme pour des actions contraires à la loi ou à l\'ordre public. Il est également interdit d\'utiliser la Plateforme à des fins commerciales sans notre autorisation.</p>

<h2>5. Propriété intellectuelle</h2>
<p>Tous les droits de propriété intellectuelle sur le contenu de la Plateforme appartiennent à {{COMPANY_NAME}}. Il est interdit de copier ou de distribuer du contenu sans autorisation.</p>

<h2>6. Responsabilité</h2>
<p>Les utilisateurs utilisent la Plateforme à leurs propres frais et risques. {{COMPANY_NAME}} n\'est pas responsable des dommages indirects ou consécutifs.</p>

<h2>7. Modifications</h2>
<p>{{COMPANY_NAME}} a le droit de modifier ces conditions.</p>

<h2>8. Droit applicable</h2>
<p>Les présentes Conditions Générales sont régies par le droit néerlandais.</p>

<h2>Contact</h2>
<p>Avez-vous des questions ? Veuillez nous contacter via {{CONTACT_EMAIL}}.</p>
',
    );
    
    return $content[$lang] ?? $content['en'];
}
