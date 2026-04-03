<?php
/**
 * Template Name: Elementor Full Width
 * Template Post Type: page, negocio
 * Description: Template de ancho completo para Elementor - sin header ni footer del tema
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<div class="elementor-template-fullwidth">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php the_content(); ?>
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
