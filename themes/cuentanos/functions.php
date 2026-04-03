<?php
/**
 * Cuentanos MX - Child Theme Functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

define('CNMX_VERSION', '2.0.2');
define('CNMX_PATH', get_stylesheet_directory());
define('CNMX_URL', get_stylesheet_directory_uri());

/**
 * Enqueue parent and custom styles
 */
function Cuentanos_enqueue_styles() {
    // Parent theme CSS
    $parent_version = wp_get_theme('astra')->get('Version');
    wp_enqueue_style(
        'astra-theme',
        get_template_directory_uri() . '/style.css',
        array(),
        $parent_version
    );
    
    // Custom CSS - Airbnb Style (correct path)
    wp_enqueue_style(
        'cnmx-main',
        CNMX_URL . '/css/main.css',
        array('astra-theme'),
        CNMX_VERSION
    );
    
    // Animations CSS
    wp_enqueue_style(
        'cnmx-animations',
        CNMX_URL . '/css/animations.css',
        array('cnmx-main'),
        CNMX_VERSION
    );
}
add_action('wp_enqueue_scripts', 'Cuentanos_enqueue_styles', 20);

/**
 * Enqueue JavaScript
 */
function Cuentanos_enqueue_scripts() {
    // Main App JS
    wp_enqueue_script(
        'cnmx-app',
        CNMX_URL . '/js/app.js',
        array('jquery'),
        CNMX_VERSION,
        true
    );
    
    // Localize data
    wp_localize_script('cnmx-app', 'cnmxData', array(
        'apiUrl' => rest_url('cnmx/v1'),
        'nonce' => wp_create_nonce('wp_rest'),
        'userId' => get_current_user_id(),
        'isLoggedIn' => is_user_logged_in(),
        'homeUrl' => home_url(),
    ));
}
add_action('wp_enqueue_scripts', 'Cuentanos_enqueue_scripts', 20);

/**
 * Setup theme
 */
function Cuentanos_setup() {
    // Theme supports
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'gallery', 'caption'));
    
    // Image sizes
    add_image_size('cnmx-card', 400, 300, true);
    add_image_size('cnmx-thumbnail', 150, 150, true);
    add_image_size('cnmx-gallery', 800, 600, true);
}
add_action('after_setup_theme', 'Cuentanos_setup');

/**
 * Create required pages on theme activation
 */
function Cuentanos_create_pages() {
    $pages = array(
        'directorio' => array('title' => 'Directorio', 'content' => ''),
        'registro' => array('title' => 'Registro', 'content' => ''),
        'mi-cuenta' => array('title' => 'Mi Cuenta', 'content' => ''),
        'perfil' => array('title' => 'Mi Perfil', 'content' => ''),
        'registrar-negocio' => array('title' => 'Registrar Negocio', 'content' => ''),
    );
    
    foreach ($pages as $slug => $page) {
        $existing = get_page_by_path($slug);
        if (!$existing) {
            wp_insert_post(array(
                'post_title' => $page['title'],
                'post_name' => $slug,
                'post_content' => $page['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
            ));
        }
    }
}
add_action('after_switch_theme', 'Cuentanos_create_pages');

/**
 * Disable Astra default header/footer when using custom templates
 */
function Cuentanos_disable_astra_header_footer() {
    // Disable for front page
    if (is_front_page()) {
        remove_action('astra_header', 'astra_header_markup');
        remove_action('astra_footer', 'astra_footer_markup');
    }
}
add_action('wp', 'Cuentanos_disable_astra_header_footer');
