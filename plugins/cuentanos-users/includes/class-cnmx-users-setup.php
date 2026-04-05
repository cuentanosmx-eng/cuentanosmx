<?php
/**
 * Users Setup Class
 */

if (!defined('ABSPATH')) exit;

class CNMX_Users_Setup {
    
    public static function activate() {
        self::create_pages();
        flush_rewrite_rules();
    }
    
    public static function create_pages() {
        $pages = array(
            'mi-cuenta' => array(
                'title' => 'Mi Cuenta',
                'template' => 'page-mi-cuenta.php'
            ),
            'registro' => array(
                'title' => 'Registro',
                'template' => 'page-registro.php'
            ),
            'perfil' => array(
                'title' => 'Mi Perfil',
                'template' => 'page-perfil.php'
            ),
            'mis-favoritos' => array(
                'title' => 'Mis Favoritos',
                'template' => 'page-mis-favoritos.php'
            ),
            'recuperar-contrasena' => array(
                'title' => 'Recuperar Contraseña',
                'template' => 'page-recuperar-contrasena.php'
            ),
            'nueva-contrasena' => array(
                'title' => 'Nueva Contraseña',
                'template' => 'page-nueva-contrasena.php'
            ),
        );
        
        foreach ($pages as $slug => $page) {
            $existing = get_page_by_path($slug);
            if (!$existing) {
                wp_insert_post(array(
                    'post_type' => 'page',
                    'post_title' => $page['title'],
                    'post_name' => $slug,
                    'post_status' => 'publish',
                    'page_template' => $page['template']
                ));
            }
        }
    }
    
    public function maybe_flush_rules() {
        if (get_option('cnmx_rewrite_flushed') !== '1') {
            flush_rewrite_rules();
            update_option('cnmx_rewrite_flushed', '1');
        }
    }
    
    public function __construct() {
        add_action('init', [$this, 'maybe_flush_rules']);
        add_action('init', [$this, 'register_pages']);
        add_action('init', [$this, 'ensure_pages_exist']);
        add_filter('query_vars', [$this, 'add_query_vars']);
        add_filter('template_include', [$this, 'load_template']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        
        add_action('wp_ajax_cnmx_reset_password', [$this, 'handle_reset_password']);
        add_action('wp_ajax_nopriv_cnmx_reset_password', [$this, 'handle_reset_password']);
        
        add_action('wp_ajax_cnmx_set_new_password', [$this, 'handle_set_new_password']);
        add_action('wp_ajax_nopriv_cnmx_set_new_password', [$this, 'handle_set_new_password']);
    }
    
    public function ensure_pages_exist() {
        if (get_option('cnmx_users_pages_created') !== '1') {
            self::create_pages();
            update_option('cnmx_users_pages_created', '1');
        }
    }
    
    public function load_template($template) {
        global $post;
        
        if (!$post || $post->post_type !== 'page') {
            return $template;
        }
        
        $slug_map = array(
            'mi-cuenta' => 'page-mi-cuenta.php',
            'registro' => 'page-registro.php',
            'perfil' => 'page-perfil.php',
            'mis-favoritos' => 'page-mis-favoritos.php',
            'recuperar-contrasena' => 'page-recuperar-contrasena.php',
            'nueva-contrasena' => 'page-nueva-contrasena.php',
        );
        
        $slug = $post->post_name;
        
        if (isset($slug_map[$slug])) {
            $custom_template = CNMX_USERS_PATH . 'templates/' . $slug_map[$slug];
            
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        return $template;
    }
    
    public function handle_set_new_password() {
        $user_key = sanitize_text_field($_POST['user_key'] ?? '');
        $user_login = sanitize_text_field($_POST['user_login'] ?? '');
        $new_password = $_POST['new_password'] ?? '';
        
        if (empty($user_key) || empty($user_login) || empty($new_password)) {
            wp_send_json_error(['message' => 'Todos los campos son requeridos']);
        }
        
        $user = check_password_reset_key($user_key, $user_login);
        
        if (!$user || is_wp_error($user)) {
            wp_send_json_error(['message' => 'El enlace ha expirado o es inválido. Solicita uno nuevo.']);
        }
        
        if (strlen($new_password) < 8) {
            wp_send_json_error(['message' => 'La contraseña debe tener al menos 8 caracteres']);
        }
        
        wp_set_password($new_password, $user->ID);
        
        wp_send_json_success(['message' => 'Contraseña actualizada correctamente']);
    }
    
    public function handle_reset_password() {
        $email = sanitize_email($_POST['email'] ?? '');
        
        if (empty($email) || !is_email($email)) {
            wp_send_json_error(['message' => 'Email inválido']);
        }
        
        $user = get_user_by('email', $email);
        
        if ($user) {
            $key = get_password_reset_key($user);
            $reset_url = home_url('/nueva-contrasena') . '?key=' . $key . '&login=' . rawurlencode($user->user_login);
            
            $to = $email;
            $subject = 'Restablecer contraseña - Cuentanos.mx';
            $message = 'Hola ' . $user->display_name . ",\n\n";
            $message .= 'Haz clic en el siguiente enlace para restablecer tu contraseña:\n';
            $message .= $reset_url . "\n\n";
            $message .= 'Si no solicitaste este cambio, ignora este email.';
            
            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail($to, $subject, nl2br($message), $headers);
        }
        
        wp_send_json_success(['message' => 'Email enviado (si existe la cuenta)']);
    }
    
    public function register_pages() {
        add_rewrite_rule('^mi-cuenta/?$', 'index.php?cnmx_page=mi-cuenta', 'top');
        add_rewrite_rule('^registro/?$', 'index.php?cnmx_page=registro', 'top');
        add_rewrite_rule('^perfil/?$', 'index.php?cnmx_page=perfil', 'top');
        add_rewrite_rule('^mis-favoritos/?$', 'index.php?cnmx_page=mis-favoritos', 'top');
        add_rewrite_rule('^recuperar-contrasena/?$', 'index.php?cnmx_page=recuperar-contrasena', 'top');
        add_rewrite_rule('^nueva-contrasena/?$', 'index.php?cnmx_page=nueva-contrasena', 'top');
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
        
        $nonce = is_user_logged_in() ? wp_create_nonce('wp_rest') : '';
        
        wp_localize_script('cnmx-users-js', 'cnmxUsersData', [
            'apiUrl' => rest_url('cuentanos/v1'),
            'nonce' => $nonce,
            'isLoggedIn' => is_user_logged_in(),
            'userId' => get_current_user_id(),
            'homeUrl' => home_url(),
        ]);
    }
}
