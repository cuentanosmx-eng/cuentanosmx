<?php
/**
 * CNMX Biz Setup - Inicialización del plugin
 */

if (!defined('ABSPATH')) exit;

function cnmx_biz_get_the_slug() {
    global $post;
    return $post ? $post->post_name : '';
}

class CNMX_Biz_Setup {
    
    public function __construct() {
        add_action('init', [$this, 'init']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_filter('template_include', [$this, 'page_templates']);
        add_action('user_register', [$this, 'on_user_register']);
    }
    
    public function init() {
        $this->create_pages();
        $this->add_roles();
    }
    
    private function create_pages() {
        $pages = [
            'login-empresa' => 'Login - Empresas',
            'registrar-negocio' => 'Registrar Negocio',
            'dashboard-empresa' => 'Dashboard Empresa',
            'mi-negocio' => 'Editar Mi Negocio',
            'mis-logros' => 'Mis Logros',
            'mis-recompensas' => 'Mis Recompensas',
            'admin-directorio' => 'Admin Directorio',
        ];
        
        foreach ($pages as $slug => $title) {
            if (!get_page_by_path($slug)) {
                wp_insert_post([
                    'post_title' => $title,
                    'post_name' => $slug,
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'post_content' => '<!-- template: business -->',
                ]);
            }
        }
    }
    
    private function add_roles() {
        add_role('empresa', 'Empresa', [
            'read' => true,
            'edit_posts' => false,
            'edit_others_posts' => false,
            'publish_posts' => false,
            'upload_files' => true,
        ]);
        
        add_role('admin_directorio', 'Admin Directorio', [
            'read' => true,
            'edit_others_posts' => true,
            'edit_published_posts' => true,
            'upload_files' => true,
        ]);
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('cnmx-biz', CNMX_BIZ_URL . 'assets/css/business.css', [], CNMX_BIZ_VERSION);
        wp_enqueue_script('cnmx-biz', CNMX_BIZ_URL . 'assets/js/business.js', ['jquery'], CNMX_BIZ_VERSION, true);
        
        wp_localize_script('cnmx-biz', 'cnmxBizData', [
            'apiUrl' => rest_url('cnmx-biz/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    }
    
    public function page_templates($template) {
        global $post;
        
        if (is_page()) {
            $slug = $post->post_name;
            
            $business_pages = ['login-empresa', 'registrar-negocio', 'dashboard-empresa', 'mi-negocio', 'mis-logros', 'mis-recompensas', 'admin-directorio'];
            
            if (in_array($slug, $business_pages)) {
                return CNMX_BIZ_PATH . 'templates/page-business.php';
            }
        }
        
        return $template;
    }
    
    public function on_user_register($user_id) {
        $es_empresa = isset($_POST['es_empresa']) && $_POST['es_empresa'];
        
        if ($es_empresa) {
            $user = new WP_User($user_id);
            $user->set_role('empresa');
            update_user_meta($user_id, 'cnmx_tipo_cuenta', 'empresa');
        }
    }
}