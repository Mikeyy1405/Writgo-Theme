<?php
/**
 * Writgo Legal Pages - Auto-generated legal content
 * 
 * These are officially approved legal texts for affiliate websites.
 * Placeholders are replaced dynamically with site-specific information.
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
    
    $content = '';
    
    switch ($page_type) {
        case 'disclaimer':
            $content = writgo_get_disclaimer_content();
            break;
        case 'privacyverklaring':
            $content = writgo_get_privacy_content();
            break;
        case 'cookiebeleid':
            $content = writgo_get_cookie_content();
            break;
        case 'algemene-voorwaarden':
            $content = writgo_get_terms_content();
            break;
    }
    
    // Replace placeholders
    $replacements = array(
        '[NAAM AFFILIATE]' => $company_name,
        '[Naam bedrijf of persoon]' => $company_name,
        '[Bedrijfsnaam invullen]' => $company_name,
        '[postcode invullen]' => $company_postcode,
        '[vestigingsplaats/woonplaats invullen]' => $company_city,
        '{{SITE_NAME}}' => $site_name,
        '{{COMPANY_NAME}}' => $company_name,
        '{{COMPANY_ADDRESS}}' => $company_address,
        '{{COMPANY_POSTCODE}}' => $company_postcode,
        '{{COMPANY_CITY}}' => $company_city,
        '{{CONTACT_EMAIL}}' => $contact_email,
        '{{YEAR}}' => date('Y'),
        '{{DATE}}' => date('j F Y'),
    );
    
    foreach ($replacements as $placeholder => $value) {
        $content = str_replace($placeholder, $value, $content);
    }
    
    return $content;
}

/**
 * Disclaimer Content
 */
function writgo_get_disclaimer_content() {
    return '
<p><em>Laatst bijgewerkt: {{DATE}}</em></p>

<p>Deze website is eigendom van en wordt beheerd door {{COMPANY_NAME}}. Deze Disclaimer is van toepassing op onze dienst en website. Door gebruik te maken van onze website en de daarop gepubliceerde informatie en functionaliteiten verklaar jij je akkoord met de voorwaarden uit deze disclaimer.</p>

<h2>Algemene informatie</h2>
<p>De informatie op deze website is uitsluitend bedoeld als algemene informatie. Deze informatie is zorgvuldig samengesteld. Toch kunnen we geen garanties geven over de correctheid van de getoonde informatie en tips. Er kunnen geen rechten aan de gegevens op onze website worden ontleend.</p>

<p>Wij zijn niet aansprakelijk voor schade welke kan ontstaan als gevolg van onjuiste of incomplete informatie op deze website. Informatie verzonden aan ons via de e-mail of de website is niet beveiligd en wordt als niet-vertrouwelijk beschouwd. Elke actie die je onderneemt op basis van onze informatie en/of tips is voor eigen verantwoordelijkheid en op eigen risico.</p>

<h2>Copyright</h2>
<p>Alle rechten van intellectuele eigendom op deze website berusten uitsluitend bij ons. De informatie op deze website is uitsluitend bedoeld voor eigen gebruik. Het is de gebruiker van deze website niet toegestaan om (delen van) deze website en van de nieuwsbrieven, te wijzigen of te bewerken, openbaar te maken, te vermenigvuldigen, tegen vergoeding beschikbaar te stellen aan derden of een link te creëren tussen de website van ons en een andere internetsite, zonder onze uitdrukkelijke schriftelijke toestemming.</p>

<h2>Affiliate links</h2>
<p>Op onze website maken wij gebruik van affiliate links. Door affiliate links op onze website te plaatsen ontvangen wij (meestal) een commissie over de producten die jij via onze website bij een andere aanbieder koopt. Deze commissie (kleine vergoeding) ontvangen wij voor het doorsturen van onze bezoekers.</p>

<p><strong>Belangrijk:</strong> Deze commissie heeft geen invloed op de prijs die jij betaalt. Onze reviews en aanbevelingen zijn altijd eerlijk en onafhankelijk.</p>

<p>Om affiliate diensten op onze website aan te kunnen bieden, maken wij gebruik van cookies. Om te weten welke partijen aan onze website zijn verbonden, verwijzen wij je door naar onze <a href="/cookiebeleid/">cookieverklaring</a>.</p>

<h2>Wijzigingen</h2>
<p>We kunnen deze disclaimer van tijd tot tijd veranderen. Wijzigingen worden hier bijgewerkt. Controleer daarom regelmatig deze pagina, zodat je van wijzigingen op de hoogte bent.</p>

<h2>Contact</h2>
<p>Kom je op onze website onjuiste informatie en/of zaken tegen? Laat het ons alsjeblieft weten via <a href="/contact/">het contactformulier</a> of stuur een e-mail naar {{CONTACT_EMAIL}}.</p>
';
}

/**
 * Privacy Policy Content
 */
function writgo_get_privacy_content() {
    return '
<p><em>Laatst bijgewerkt: {{DATE}}</em></p>

<p>Door gebruik te maken van ons platform verwerken wij jouw persoonsgegevens. In deze privacyverklaring staat beschreven welke persoonsgegevens wij verwerken en hoe wij deze gegevens verwerken. Ook zie je wat je rechten zijn, hoe lang we je gegevens bewaren en wie toegang heeft tot je data.</p>

<h2>Persoonsgegevens die wij verwerken</h2>
<p>Wij verwerken persoonsgegevens die jij ten behoeve van onze dienstverlening aan ons verstrekt. Wij verwerken:</p>

<p><strong>Contactgegevens:</strong> wanneer je het contactformulier op de website invult verwerken wij de persoonsgegevens die jij aan ons verstrekt, zoals je naam, e-mailadres en de inhoud van het bericht. We gebruiken deze gegevens uitsluitend om je bericht te beantwoorden.</p>

<p><strong>Gebruiksgegevens:</strong> door onze website te bezoeken verwerken wij gegevens over hoe jij op onze website terecht bent gekomen, welke delen van de website je hebt bezocht, de datum en duur van je bezoek, jouw geanonimiseerde IP-adres, informatie over het apparaat waarop jij onze website bezocht etc.</p>

<p><strong>Cookies:</strong> door gebruik te maken van onze website worden er cookies op je apparaat geplaatst. Cookies zijn kleine gegevensbestanden die door site naar apparaten worden overgebracht voor archivering doeleinden en om de functionaliteit van onze website en diensten te verbeteren.</p>

<h2>Verwerkingsdoeleinden</h2>
<p>Wij verwerken persoonsgegevens uitsluitend voor de hierna te noemen doeleinden:</p>
<ul>
<li>het faciliteren en bieden van toegang tot, het leveren van en in gebruik kunnen nemen van de door ons aangeboden diensten;</li>
<li>om te analyseren hoe vaak onze website wordt bezocht en hoe er door bezoekers op de website wordt genavigeerd;</li>
<li>de continuïteit en goede werking van producten en diensten;</li>
<li>het verbeteren van onze dienstverlening;</li>
<li>het kunnen informeren over relevante producten en diensten;</li>
<li>om te voldoen aan wettelijke verplichtingen die voor ons gelden.</li>
</ul>

<h2>Verwerkingsgronden</h2>
<p>Wij verwerken de hierboven genoemde persoonsgegevens uitsluitend op basis van de volgende gronden als bedoeld in artikel 6 van de AVG:</p>
<ul>
<li>het voldoen aan een wettelijke verplichting;</li>
<li>de uitvoering van een overeenkomst;</li>
<li>uitdrukkelijk gegeven toestemming;</li>
<li>een gerechtvaardigd belang.</li>
</ul>

<h2>Bewaartermijnen</h2>
<p>Wij bewaren persoonsgegevens niet langer dan noodzakelijk is voor de hiervoor genoemde doeleinden van de gegevensverwerking dan wel op grond van wet- en regelgeving is vereist.</p>

<h2>Delen van persoonsgegevens</h2>
<p>Wij verkopen jouw gegevens niet door aan derden. Wij geven jouw persoonsgegevens alleen door aan andere partijen als dat echt nodig is voor onze dienstverlening. Partijen die van ons toegang krijgen tot je gegevens, mogen deze gegevens alleen gebruiken voor de dienstverlening namens ons en met inachtneming van de hiervoor genoemde doeleinden.</p>

<p>Daarnaast kan de wettelijke verplichting bestaan om persoonsgegevens te verstrekken aan een derde partij, zoals een toezichthouder of een andere met openbaar gezag. Indien nodig zullen wij je hierover informeren.</p>

<h2>Verantwoordelijkheid andere webpaginas</h2>
<p>Deze privacyverklaring is niet van toepassing op websites van derden die door middel van buttons en/of links met onze website zijn verbonden. Wij kunnen niet garanderen dat deze derden op een betrouwbare en veilige manier met je persoonsgegevens omgaan. Om te weten welke partijen aan onze website zijn verbonden, verwijzen wij je door naar onze <a href="/cookiebeleid/">cookieverklaring</a>.</p>

<h2>Persoonsgegevens minderjarigen</h2>
<p>Wij verwerken geen persoonsgegevens van kinderen op onze websites en/of applicaties, voor zover bekend is dat dit informatie over minderjarigen (jonger dan 16 jaar) bevat. Als je een ouder bent en ontdekt dat jouw kind ons heeft voorzien van persoonsgegevens, neem dan contact met ons op. Wij zullen dan samen met jou proberen een oplossing te vinden.</p>

<h2>Jouw rechten</h2>
<p>Voor vragen over ons privacybeleid of vragen omtrent het inzien, wijzigen of verwijderen van persoonsgegevens kun je altijd contact met ons opnemen. Daarnaast heb je op grond van de AVG een aantal rechten. Je kunt ons verzoeken om:</p>
<ul>
<li>je persoonsgegevens te verwijderen;</li>
<li>de verwerking van je persoonsgegevens te beperken;</li>
<li>bezwaar te maken tegen het verwerken van je persoonsgegevens;</li>
<li>een kopie van je persoonsgegevens op te vragen;</li>
<li>de toestemming die je eerder gaf om je persoonsgegevens te verwerken, intrekken;</li>
<li>bezwaar te maken tegen het geautomatiseerd verwerken van je persoonsgegevens.</li>
</ul>

<h2>Contact</h2>
<p>Heb je vragen over deze privacyverklaring? Neem dan contact met ons op via {{CONTACT_EMAIL}} of via onze <a href="/contact/">contactpagina</a>.</p>
';
}

/**
 * Cookie Policy Content
 */
function writgo_get_cookie_content() {
    return '
<p><em>Laatst bijgewerkt: {{DATE}}</em></p>

<p>Op onze website maken wij gebruik van cookies. Cookies zijn kleine gegevensbestanden die door je bezoek aan onze website op jouw telefoon/laptop worden geplaatst. Cookies zijn niet schadelijk voor je computer of je opgeslagen bestanden.</p>

<h2>Gebruik van cookies</h2>
<p>Dankzij cookies kunnen wij jouw browser herkennen wanneer je een volgende keer onze website bezoekt. Hierdoor kunnen wij het gebruiksgemak van onze website voor je vergroten en optimaliseren wij hiermee de werking van onze website. Tevens gebruiken wij cookies om onze affiliate diensten aan te kunnen bieden en de effectiviteit van onze affiliate diensten bij te houden.</p>

<p>Behalve je IP-adres slaan wij geen naam- en adresgegevens of andere persoonlijke gegevens op. De langs deze weg verzamelde persoonsgegevens zijn in beginsel anoniem en worden niet door ons verkocht aan derden.</p>

<p>Meer informatie over cookies kun je vinden op de website van Consuwijzer: <a href="https://www.consuwijzer.nl/veilig-online/kan-ik-altijd-cookies-uitzetten" target="_blank" rel="noopener">https://www.consuwijzer.nl/veilig-online/kan-ik-altijd-cookies-uitzetten</a></p>

<h2>Cookies van derden</h2>
<p>De meeste cookies worden niet door ons zelf geplaatst of uitgelezen, maar door derde partijen. Dat zijn bijvoorbeeld Google Analytics en social media. Wij hebben geen volledige controle over wat deze aanbieders met de cookies doen, wanneer zij ze uitlezen. Voor meer informatie over deze verwerkingen, verwijzen wij je dan ook door naar de privacyverklaring van deze partijen.</p>

<h2>Social media</h2>
<p>Wij maken gebruik van verschillende social media kanalen om onze diensten aan te bieden. Lees de Privacyverklaringen van de betreffende social media kanalen om te weten hoe zij met privacy omgaan:</p>
<ul>
<li>Facebook: <a href="https://www.facebook.com/privacy/explanation" target="_blank" rel="noopener">facebook.com/privacy/explanation</a></li>
<li>Instagram: <a href="https://help.instagram.com/519522125107875" target="_blank" rel="noopener">help.instagram.com/519522125107875</a></li>
<li>LinkedIn: <a href="https://www.linkedin.com/legal/privacy-policy" target="_blank" rel="noopener">linkedin.com/legal/privacy-policy</a></li>
<li>Twitter/X: <a href="https://twitter.com/privacy" target="_blank" rel="noopener">twitter.com/privacy</a></li>
<li>Pinterest: <a href="https://policy.pinterest.com/nl/privacy-policy" target="_blank" rel="noopener">policy.pinterest.com/nl/privacy-policy</a></li>
</ul>

<h2>YouTube</h2>
<p>Ter promotie van onze diensten kan het zijn dat wij YouTube filmpjes op onze website plaatsen. YouTube is een service van Google. Google plaatst cookies via YouTube om de advertenties te laten zien die aangepast zijn naar jouw YouTube voorkeuren. Voor de Privacyverklaring van Google verwijzen wij je naar: <a href="https://policies.google.com/privacy?hl=nl" target="_blank" rel="noopener">policies.google.com/privacy</a></p>

<h2>Google Analytics</h2>
<p>Onze website maakt gebruik van Google Analytics, een webanalyse-service die wordt aangeboden door Google Inc. ("Google"). Google Analytics gebruikt cookies die een analyse mogelijk maken van de manier waarop bezoekers onze website gebruiken. Dit helpt om de werking van de website te verbeteren.</p>

<p>Google kan deze informatie aan derden verschaffen indien Google hiertoe wettelijk wordt verplicht of voor zover derde partijen de informatie namens Google verwerken. Door gebruik te maken van onze website geef je toestemming voor het verwerken van de informatie door Google op de wijze en voor de doeleinden zoals omschreven in onze Privacyverklaring.</p>

<h2>Cookies beheren</h2>
<p>Je kunt cookies uitschakelen in je browserinstellingen. Houd er rekening mee dat sommige functies van onze website mogelijk niet goed werken als je cookies uitschakelt.</p>

<h2>Wijzigingen</h2>
<p>Wij behouden ons het recht voor om wijzigingen aan te brengen in deze cookieverklaring. Het verdient aanbeveling om deze cookieverklaring regelmatig te raadplegen, zodat je van deze wijzigingen op de hoogte bent.</p>

<h2>Contact</h2>
<p>Heb je vragen over ons cookiebeleid? Neem dan contact met ons op via {{CONTACT_EMAIL}}.</p>
';
}

/**
 * Terms and Conditions Content
 */
function writgo_get_terms_content() {
    return '
<p><em>Laatst bijgewerkt: {{DATE}}</em></p>

<h2>1. Definities</h2>
<p>In deze Algemene voorwaarden wordt verstaan onder:</p>
<ul>
<li><strong>Aanbod:</strong> het (product– en)dienstenaanbod van een Derde dat door een ander natuurlijk persoon of rechtspersoon via het Platform kan worden aanvaard.</li>
<li><strong>Algemene voorwaarden:</strong> onderhavige Algemene voorwaarden van {{COMPANY_NAME}}.</li>
<li><strong>Diensten/Producten:</strong> de (commerciële) diensten en/of Producten die via het Platform voor Derden door {{COMPANY_NAME}} in hoedanigheid als affiliate worden aangeboden aan Gebruikers.</li>
<li><strong>Gebruiker(s):</strong> iedere natuurlijke en/of rechtspersoon die van het Platform gebruik maakt.</li>
<li><strong>Derde(n):</strong> de natuurlijke persoon of organisatie/rechtspersoon waarvoor {{COMPANY_NAME}} affiliate diensten aanbiedt.</li>
<li><strong>Platform:</strong> de website(s), social media account(s), app(s) en andere tools van {{COMPANY_NAME}}.</li>
<li><strong>{{COMPANY_NAME}}:</strong> {{COMPANY_NAME}}, gevestigd te {{COMPANY_POSTCODE}} {{COMPANY_CITY}}, die via het Platform de Diensten/Producten van Derden aan de Gebruikers aanbiedt en hiervoor een commissie ontvangt.</li>
</ul>

<h2>2. Toepasselijkheid</h2>
<p>Op alle rechtsbetrekkingen met {{COMPANY_NAME}} zijn uitsluitend deze Algemene voorwaarden van toepassing. Door de Gebruiker(s) gehanteerde algemene voorwaarden en andere (van de Algemene voorwaarden afwijkende) bedingen worden door {{COMPANY_NAME}} uitdrukkelijk van de hand gewezen.</p>
<p>Door gebruik te maken van het Platform, aanvaardt de Gebruiker deze Algemene voorwaarden.</p>

<h2>3. Aanbod</h2>
<p>{{COMPANY_NAME}} publiceert het Aanbod namens Derden op het Platform, overeenkomstig de door Derden aangeleverde informatie, dan wel op basis van informatie en ervaringen van {{COMPANY_NAME}}.</p>
<p>{{COMPANY_NAME}} aanvaardt in alle gevallen geen verantwoordelijkheid of aansprakelijkheid voor de inhoud van het Aanbod en van de informatie op het Platform.</p>
<p>Informatie uit of afkomstig van het Platform mogen zonder toestemming van {{COMPANY_NAME}} niet worden vermenigvuldigd, noch aan derden ter inzage worden gegeven.</p>

<h2>4. Misbruik Platform</h2>
<p>Het gebruik van het Platform is niet toegestaan voor handelingen of gedragingen die in strijd zijn met de wet- en regelgeving, openbare orde en goede zeden. Meer in het bijzonder is het niet toegestaan onfatsoenlijke, immorele of discriminerende uitingen te verspreiden via het Platform.</p>
<p>Het is de Gebruiker voorts niet toegestaan het Platform:</p>
<ul>
<li>te deassembleren, decompileren of onderwerpen aan reverse engineering;</li>
<li>te gebruiken voor commerciële doeleinden;</li>
<li>aan te sluiten op een netwerk waardoor andere apparaten van het Platform gebruik kunnen maken;</li>
<li>op welke wijze dan ook aan derden beschikbaar te stellen;</li>
<li>te gebruiken voor het overmatig verzenden van gegevens of berichten, verspreiden van virussen en andere schadelijke software;</li>
<li>te gebruiken op een wijze die nadelig of schadelijk kan zijn voor {{COMPANY_NAME}}.</li>
</ul>

<h2>5. Onderhoud en aanpassingen</h2>
<p>{{COMPANY_NAME}} zal zich inspannen om het Platform naar behoren en met zorg te laten functioneren, maar zal en kan nimmer garanderen dat het Platform te allen tijde, onophoudelijk en zonder storingen beschikbaar zal zijn.</p>
<p>{{COMPANY_NAME}} onderhoudt het Platform en kan updates uitbrengen om aanpassingen aan te brengen, nieuwe functionaliteiten toe te voegen of prestaties te verbeteren.</p>
<p>{{COMPANY_NAME}} heeft het recht om informatie of de functionaliteit op het Platform te wijzigen of te verwijderen.</p>

<h2>6. Privacy</h2>
<p>{{COMPANY_NAME}} houdt zich aan alle geldende wet- en regelgeving ten aanzien van de bescherming van de persoonsgegevens. Voor meer informatie verwijst {{COMPANY_NAME}} de Gebruiker naar de <a href="/privacyverklaring/">Privacyverklaring</a>.</p>

<h2>7. Aansprakelijkheid</h2>
<p>Gebruikers gebruiken het Platform volledig voor eigen rekening en risico. {{COMPANY_NAME}} neemt geen verantwoordelijkheid voor het gedrag van Gebruiker(s) binnen en buiten het Platform.</p>
<p>{{COMPANY_NAME}} is niet aansprakelijk voor indirecte schade, waaronder begrepen gevolgschade, gederfde winst, gemiste besparingen, schade door bedrijfsstagnatie en enige andere schade dan die welke het gevolg is van opzet of grove schuld van {{COMPANY_NAME}}.</p>

<h2>8. Wijzigingen</h2>
<p>{{COMPANY_NAME}} heeft het recht om deze voorwaarden te wijzigen en/of aan te vullen.</p>

<h2>9. Toepasselijk recht en geschillen</h2>
<p>Op deze Algemene voorwaarden en alle rechtsverhoudingen van {{COMPANY_NAME}} is uitsluitend Nederlands recht van toepassing.</p>

<h2>Contact</h2>
<p>Heb je vragen over deze algemene voorwaarden? Neem dan contact met ons op via {{CONTACT_EMAIL}}.</p>
';
}
