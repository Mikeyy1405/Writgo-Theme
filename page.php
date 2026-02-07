<?php
/**
 * Page Template
 *
 * @package Writgo_Affiliate
 */

get_header();

while (have_posts()) : the_post();
?>

<main id="main-content">

    <header class="bg-white border-b border-gray-200 py-10 lg:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php writgo_breadcrumbs(); ?>
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mt-4 tracking-tight"><?php the_title(); ?></h1>
        </div>
    </header>

    <div class="py-10 lg:py-14">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="wa-prose">
                <?php the_content(); ?>
            </div>
        </div>
    </div>

</main>

<?php
endwhile;
get_footer();
?>
