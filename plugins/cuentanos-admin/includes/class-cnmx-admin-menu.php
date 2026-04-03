<?php
/**
 * Admin Menu Class
 */

if (!defined('ABSPATH')) exit;

class CNMX_Admin_Menu {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_menus']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }
    
    public function add_menus() {
        add_menu_page(
            'Cuentanos.mx',
            'Cuentanos',
            'manage_options',
            'cuentanos',
            null,
            'dashicons-location',
            30
        );
        
        add_submenu_page(
            'cuentanos',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'cuentanos',
            [$this, 'render_dashboard_page']
        );
        
        add_submenu_page(
            'cuentanos',
            'Negocios',
            'Negocios',
            'manage_options',
            'cuentanos-negocios',
            [$this, 'render_negocios_page']
        );
        
        add_submenu_page(
            'cuentanos',
            'Pendientes',
            'Pendientes',
            'manage_options',
            'cuentanos-pendientes',
            [$this, 'render_pendientes_page']
        );
        
        add_submenu_page(
            'cuentanos',
            'Usuarios',
            'Usuarios',
            'manage_options',
            'cuentanos-usuarios',
            [$this, 'render_usuarios_page']
        );
        
        add_submenu_page(
            'cuentanos',
            'Métricas',
            'Métricas',
            'manage_options',
            'cuentanos-metricas',
            [$this, 'render_metricas_page']
        );
        
        add_submenu_page(
            'cuentanos',
            'Configuración',
            'Configuración',
            'manage_options',
            'cuentanos-config',
            [$this, 'render_config_page']
        );
    }
    
    public function enqueue_assets($hook) {
        if (strpos($hook, 'cuentanos') === false) return;
        
        wp_enqueue_style('cnmx-admin', CNMX_ADMIN_URL . 'assets/css/admin.css', [], CNMX_ADMIN_VERSION);
        wp_enqueue_script('cnmx-admin', CNMX_ADMIN_URL . 'assets/js/admin.js', ['jquery'], CNMX_ADMIN_VERSION, true);
        
        wp_localize_script('cnmx-admin', 'cnmxAdminData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cnmx_admin_nonce'),
        ]);
    }
    
    public function render_dashboard_page() {
        require_once CNMX_ADMIN_PATH . 'templates/page-dashboard.php';
    }
    
    public function render_negocios_page() {
        require_once CNMX_ADMIN_PATH . 'templates/page-negocios.php';
    }
    
    public function render_pendientes_page() {
        require_once CNMX_ADMIN_PATH . 'templates/page-pendientes.php';
    }
    
    public function render_usuarios_page() {
        require_once CNMX_ADMIN_PATH . 'templates/page-usuarios.php';
    }
    
    public function render_metricas_page() {
        require_once CNMX_ADMIN_PATH . 'templates/page-metricas.php';
    }
    
    public function render_config_page() {
        require_once CNMX_ADMIN_PATH . 'templates/page-config.php';
    }
}
