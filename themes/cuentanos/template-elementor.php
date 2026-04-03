<?php
/**
 * Template Name: Pagina con Elementor
 * Description: Plantilla para editar con Elementor
 */

get_header();

while (have_posts()) : the_post();
    the_content();
endwhile;

get_footer();
