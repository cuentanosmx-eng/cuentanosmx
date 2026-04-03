<?php
/**
 * Template Name: Elementor Compatible
 * Description: Page template compatible with Elementor page builder
 */

get_header();
?>

<main class="elementor-page-wrapper">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php the_content(); ?>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
