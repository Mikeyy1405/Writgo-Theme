<?php
/**
 * Single Post Template - Hero Layout met Sticky TOC
 *
 * @package Writgo_Affiliate
 */

get_header();

while (have_posts()) : the_post();
    $categories = get_the_category();
    $reading_time = writgo_get_reading_time();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('wa-article'); ?>>
    
    <!-- Full-Width Hero Section -->
    <header class="wa-hero-section">
        <?php if (has_post_thumbnail()) : ?>
            <div class="wa-hero-background">
                <?php the_post_thumbnail('writgo-hero', array('class' => 'wa-hero-image')); ?>
                <div class="wa-hero-gradient"></div>
            </div>
        <?php else : ?>
            <div class="wa-hero-background wa-hero-no-image">
                <div class="wa-hero-gradient"></div>
            </div>
        <?php endif; ?>
        
        <div class="wa-hero-content">
            <div class="wa-container-wide">
                <!-- Breadcrumbs -->
                <nav class="wa-breadcrumbs" aria-label="Breadcrumbs">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                    <span class="wa-breadcrumb-sep">›</span>
                    <?php if (!empty($categories)) : ?>
                        <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>">
                            <?php echo esc_html($categories[0]->name); ?>
                        </a>
                        <span class="wa-breadcrumb-sep">›</span>
                    <?php endif; ?>
                    <span class="wa-breadcrumb-current"><?php the_title(); ?></span>
                </nav>
                
                <!-- Badge & Category -->
                <div class="wa-hero-meta-top">
                    <span class="wa-badge">Blog</span>
                    <?php if (!empty($categories)) : ?>
                        <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="wa-hero-category">
                            <?php echo esc_html($categories[0]->name); ?>
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Title -->
                <h1 class="wa-hero-title"><?php the_title(); ?></h1>
                
                <!-- Excerpt if available -->
                <?php if (has_excerpt()) : ?>
                    <p class="wa-hero-excerpt"><?php echo get_the_excerpt(); ?></p>
                <?php endif; ?>
                
                <!-- Author & Date -->
                <div class="wa-hero-meta-bottom">
                    <div class="wa-author-info">
                        <?php echo get_avatar(get_the_author_meta('ID'), 44, '', '', array('class' => 'wa-author-avatar')); ?>
                        <div class="wa-author-details">
                            <span class="wa-author-name"><?php the_author(); ?></span>
                            <time class="wa-post-date" datetime="<?php echo get_the_date('c'); ?>">
                                <?php echo get_the_date('j F Y'); ?>
                                <?php if (get_the_modified_date() !== get_the_date()) : ?>
                                    · Bijgewerkt <?php echo get_the_modified_date('j F Y'); ?>
                                <?php endif; ?>
                            </time>
                        </div>
                    </div>
                    
                    <?php if ($reading_time) : ?>
                        <span class="wa-reading-time">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            <?php echo esc_html($reading_time); ?> min leestijd
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Content Section with Sidebar TOC -->
    <div class="wa-article-wrapper">
        <div class="wa-container-wide">
            <div class="wa-article-grid">
                
                <!-- Main Content -->
                <main class="wa-article-content">
                    
                    <!-- Mobile TOC (collapsible) -->
                    <div class="wa-toc-mobile" id="toc-mobile">
                        <button class="wa-toc-toggle" aria-expanded="false">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 6h16M4 12h16M4 18h10"/>
                            </svg>
                            <span>Inhoudsopgave</span>
                            <svg class="wa-toc-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                        <nav class="wa-toc-mobile-list" id="toc-mobile-list"></nav>
                    </div>
                    
                    <!-- Affiliate Disclosure -->
                    <?php if (get_theme_mod('writgo_show_disclosure', true)) : ?>
                        <div class="wa-disclosure">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4M12 8h.01"/>
                            </svg>
                            <?php echo wp_kses_post(get_theme_mod('writgo_disclosure_text', 'Dit artikel kan affiliate links bevatten. Bij aankoop via deze links ontvangen wij een commissie.')); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- The Content -->
                    <div class="wa-content entry-content" id="article-content">
                        <?php the_content(); ?>
                    </div>
                    
                    <!-- Tags -->
                    <?php if (has_tag()) : ?>
                        <div class="wa-tags">
                            <span class="wa-tags-label">Tags:</span>
                            <?php the_tags('', '', ''); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Author Box -->
                    <div class="wa-author-box">
                        <?php echo get_avatar(get_the_author_meta('ID'), 80, '', '', array('class' => 'wa-author-box-avatar')); ?>
                        <div class="wa-author-box-content">
                            <h4 class="wa-author-box-name">
                                Geschreven door <?php the_author(); ?>
                            </h4>
                            <?php if (get_the_author_meta('description')) : ?>
                                <p class="wa-author-box-bio"><?php echo get_the_author_meta('description'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </main>
                
                <!-- Sticky Sidebar TOC -->
                <aside class="wa-sidebar-toc" id="sidebar-toc">
                    <div class="wa-toc-sticky">
                        <div class="wa-toc-card">
                            <h3 class="wa-toc-heading">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6h16M4 12h16M4 18h10"/>
                                </svg>
                                Inhoudsopgave
                            </h3>
                            <nav class="wa-toc-list" id="toc-sidebar-list">
                                <!-- Generated by JavaScript -->
                            </nav>
                            
                            <!-- Progress indicator -->
                            <div class="wa-toc-progress">
                                <div class="wa-toc-progress-bar" id="reading-progress"></div>
                            </div>
                        </div>
                    </div>
                </aside>
                
            </div>
        </div>
    </div>
    
</article>

<!-- Related Posts -->
<section class="wa-related-section">
    <div class="wa-container-wide">
        <h2 class="wa-section-title">Gerelateerde artikelen</h2>
        <?php writgo_related_posts(3); ?>
    </div>
</section>

<?php
endwhile;
get_footer();
?>
