<footer class="wa-footer">

    <!-- Trust Bar -->
    <div class="wa-trust-bar">
        <div class="wa-container-wide">
            <div class="wa-trust-items">
                <div class="wa-trust-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <span><?php writgo_te('trust_safe'); ?></span>
                </div>
                <div class="wa-trust-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                    <span><?php writgo_te('trust_independent'); ?></span>
                </div>
                <div class="wa-trust-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span><?php writgo_te('trust_gdpr'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer -->
    <div class="wa-footer-main">
        <div class="wa-container-wide">
            <div class="wa-footer-grid">

                <!-- Column 1: About -->
                <div class="wa-footer-col wa-footer-about">
                    <div class="wa-footer-logo">
                        <?php
                        $custom_logo_id = get_theme_mod('custom_logo');
                        if ($custom_logo_id) {
                            echo wp_get_attachment_image($custom_logo_id, 'medium', false, array('class' => 'wa-footer-logo-img'));
                        } else {
                            echo '<span class="wa-footer-logo-text">' . esc_html(get_bloginfo('name')) . '</span>';
                        }
                        ?>
                    </div>
                    <p class="wa-footer-disclosure">
                        <?php echo esc_html(writgo_get_mod('writgo_disclosure_text', 'footer_disclosure', 'Deze website bevat affiliate links. Bij aankoop ontvangen wij een kleine commissie, zonder extra kosten voor jou.')); ?>
                    </p>
                </div>

                <!-- Column 2: Navigation -->
                <div class="wa-footer-col">
                    <h4 class="wa-footer-title"><?php writgo_te('quick_links'); ?></h4>
                    <?php if (has_nav_menu('footer')) : ?>
                        <?php wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'container'      => false,
                            'menu_class'     => 'wa-footer-menu',
                            'depth'          => 1,
                        )); ?>
                    <?php elseif (has_nav_menu('primary')) : ?>
                        <?php wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'container'      => false,
                            'menu_class'     => 'wa-footer-menu',
                            'depth'          => 1,
                        )); ?>
                    <?php endif; ?>
                </div>

                <!-- Column 3: Legal -->
                <div class="wa-footer-col">
                    <h4 class="wa-footer-title"><?php writgo_te('legal'); ?></h4>
                    <?php
                    $lang = writgo_get_language();
                    $legal_links = array(
                        'nl' => array('privacyverklaring' => 'privacy_policy', 'cookiebeleid' => 'cookie_policy', 'disclaimer' => 'disclaimer'),
                        'en' => array('privacy-policy' => 'privacy_policy', 'cookie-policy' => 'cookie_policy', 'disclaimer' => 'disclaimer'),
                        'de' => array('datenschutz' => 'privacy_policy', 'cookie-richtlinie' => 'cookie_policy', 'haftungsausschluss' => 'disclaimer'),
                        'fr' => array('politique-de-confidentialite' => 'privacy_policy', 'politique-cookies' => 'cookie_policy', 'avertissement' => 'disclaimer'),
                    );
                    $current_links = $legal_links[$lang] ?? $legal_links['nl'];
                    ?>
                    <ul class="wa-footer-menu">
                        <?php foreach ($current_links as $slug => $translation_key) : ?>
                            <li><a href="<?php echo esc_url(home_url('/' . $slug . '/')); ?>"><?php writgo_te($translation_key); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Column 4: Contact & Social -->
                <div class="wa-footer-col">
                    <h4 class="wa-footer-title"><?php writgo_te('contact'); ?></h4>
                    <?php
                    $company_name = get_theme_mod('writgo_company_name', '');
                    $company_email = get_theme_mod('writgo_company_email', '');
                    $company_phone = get_theme_mod('writgo_company_phone', '');
                    $company_kvk = get_theme_mod('writgo_company_kvk', '');
                    $company_address = get_theme_mod('writgo_company_address', '');
                    $company_city = get_theme_mod('writgo_company_city', '');
                    $company_postcode = get_theme_mod('writgo_company_postcode', '');
                    ?>
                    <ul class="wa-footer-contact">
                        <?php if ($company_name) : ?>
                            <li><strong><?php echo esc_html($company_name); ?></strong></li>
                        <?php endif; ?>
                        <?php if ($company_address || $company_city) : ?>
                            <li><?php echo esc_html(trim($company_address . ', ' . $company_postcode . ' ' . $company_city, ', ')); ?></li>
                        <?php endif; ?>
                        <?php if ($company_email) : ?>
                            <li><a href="mailto:<?php echo esc_attr($company_email); ?>"><?php echo esc_html($company_email); ?></a></li>
                        <?php endif; ?>
                        <?php if ($company_phone) : ?>
                            <li><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $company_phone)); ?>"><?php echo esc_html($company_phone); ?></a></li>
                        <?php endif; ?>
                        <?php if ($company_kvk) : ?>
                            <li>KvK: <?php echo esc_html($company_kvk); ?></li>
                        <?php endif; ?>
                    </ul>

                    <!-- Social Media -->
                    <?php
                    $socials = array(
                        'facebook'  => array('mod' => 'writgo_social_facebook', 'svg' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>'),
                        'instagram' => array('mod' => 'writgo_social_instagram', 'svg' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>'),
                        'twitter'   => array('mod' => 'writgo_social_twitter', 'svg' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'),
                        'linkedin'  => array('mod' => 'writgo_social_linkedin', 'svg' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>'),
                        'youtube'   => array('mod' => 'writgo_social_youtube', 'svg' => '<path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>'),
                        'pinterest' => array('mod' => 'writgo_social_pinterest', 'svg' => '<path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/>'),
                    );
                    $has_social = false;
                    foreach ($socials as $name => $data) {
                        if (get_theme_mod($data['mod'], '')) { $has_social = true; break; }
                    }
                    if ($has_social) : ?>
                    <div class="wa-footer-social">
                        <?php foreach ($socials as $name => $data) :
                            $url = get_theme_mod($data['mod'], '');
                            if ($url) : ?>
                            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(ucfirst($name)); ?>" class="wa-social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><?php echo $data['svg']; ?></svg>
                            </a>
                        <?php endif; endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="wa-footer-bottom">
        <div class="wa-container-wide">
            <p class="wa-footer-copy">
                &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php writgo_te('all_rights_reserved'); ?>.
            </p>
        </div>
    </div>

</footer>

<!-- Scroll to Top -->
<button class="wa-scroll-top" id="scroll-top" aria-label="<?php echo esc_attr(writgo_t('back_to_top')); ?>">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 15l-6-6-6 6"/>
    </svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
