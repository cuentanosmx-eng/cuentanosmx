<?php
/**
 * Users Setup Class
 */

if (!defined('ABSPATH')) exit;

class CNMX_Users_Setup {
    
    public function __construct() {
        add_action('init', [$this, 'register_pages']);
        add_filter('query_vars', [$this, 'add_query_vars']);
        add_action('template_redirect', [$this, 'handle_pages']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    
    public function register_pages() {
        add_rewrite_rule('^mi-cuenta/?$', 'index.php?cnmx_page=mi-cuenta', 'top');
        add_rewrite_rule('^registro/?$', 'index.php?cnmx_page=registro', 'top');
        add_rewrite_rule('^perfil/?$', 'index.php?cnmx_page=perfil', 'top');
        add_rewrite_rule('^mis-favoritos/?$', 'index.php?cnmx_page=mis-favoritos', 'top');
    }
    
    public function add_query_vars($vars) {
        $vars[] = 'cnmx_page';
        return $vars;
    }
    
    public function handle_pages() {
        $page = get_query_var('cnmx_page');
        
        if ($page) {
            include CNMX_USERS_PATH . 'templates/page-' . $page . '.php';
            exit;
        }
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('cnmx-users-css', CNMX_USERS_URL . 'assets/css/users.css', [], CNMX_USERS_VERSION);
        wp_enqueue_script('cnmx-users-js', CNMX_USERS_URL . 'assets/js/users.js', ['jquery'], CNMX_USERS_VERSION, true);
        
        wp_localize_script('cnmx-users-js', 'cnmxUsersData', [
            'apiUrl' => rest_url('cuentanos/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
            'isLoggedIn' => is_user_logged_in(),
            'userId' => get_current_user_id(),
            'homeUrl' => home_url(),
        ]);
    }
}
