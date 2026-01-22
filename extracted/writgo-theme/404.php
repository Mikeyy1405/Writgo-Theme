<?php
/**
 * 404 Page Template
 *
 * @package Writgo_Affiliate
 */

get_header();
?>

<main class="wa-page">
    
    <div class="wa-article-wrapper">
        <div class="wa-container">
            <div class="text-center" style="max-width: 600px; margin: 0 auto; padding: var(--wa-space-16) 0;">
                
                <h1 style="font-size: 6rem; font-weight: 800; color: var(--wa-primary); margin-bottom: var(--wa-space-4);">404</h1>
                
                <h2 style="font-size: var(--wa-text-2xl); margin-bottom: var(--wa-space-4);">Pagina niet gevonden</h2>
                
                <p style="color: var(--wa-text-light); margin-bottom: var(--wa-space-8);">
                    De pagina die je zoekt bestaat niet of is verplaatst.
                </p>
                
                <!-- Search Form -->
                <div class="wa-home-search" style="margin-bottom: var(--wa-space-8);">
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="wa-search-wrapper">
                            <input type="search" 
                                   class="wa-search-input" 
                                   placeholder="Zoek op de site..." 
                                   name="s" />
                            <button type="submit" class="wa-search-button">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="M21 21l-4.35-4.35"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
                
                <a href="<?php echo esc_url(home_url('/')); ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: var(--wa-primary); color: white; border-radius: var(--wa-radius); text-decoration: none; font-weight: 600;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Terug naar home
                </a>
                
            </div>
        </div>
    </div>
    
</main>

<?php get_footer(); ?>
