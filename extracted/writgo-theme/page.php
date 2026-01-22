<?php
/**
 * Page Template
 *
 * @package Writgo_Affiliate
 */

get_header();

while (have_posts()) : the_post();
?>

<main class="wa-page">
    
    <header class="wa-archive-header">
        <div class="wa-container">
            <?php writgo_breadcrumbs(); ?>
            <h1 class="wa-archive-title"><?php the_title(); ?></h1>
        </div>
    </header>
    
    <div class="wa-article-wrapper">
        <div class="wa-container">
            <div class="wa-content entry-content" style="max-width: 800px; margin: 0 auto;">
                <?php the_content(); ?>
            </div>
        </div>
    </div>
    
</main>

<?php
endwhile;
get_footer();
?>
