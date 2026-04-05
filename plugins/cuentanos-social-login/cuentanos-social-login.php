<?php
/**
 * Plugin Name: Cuentanos Social Login
 * Description: Login con Google y Facebook OAuth - Funcional
 * Version: 1.0.0
 * Author: Cuentanos Team
 */

if (!defined('ABSPATH')) exit;

define('CNMX_SOCIAL_VERSION', '1.0.0');
define('CNMX_SOCIAL_PATH', plugin_dir_path(__FILE__));
define('CNMX_SOCIAL_URL', plugin_dir_url(__FILE__));

class CNMX_Social_Login {
    
    private function get_google_client_id() {
        return get_option('cnmx_google_client_id', '');
    }
    
    private function get_google_client_secret() {
        return get_option('cnmx_google_client_secret', '');
    }
    
    private function get_facebook_app_id() {
        return get_option('cnmx_facebook_app_id', '');
    }
    
    private function get_facebook_app_secret() {
        return get_option('cnmx_facebook_app_secret', '');
    }
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_cnmx_save_social_settings', [$this, 'save_settings']);
        
        add_action('wp_loaded', [$this, 'check_social_login']);
        add_action('wp_ajax_cnmx_social_login_url', [$this, 'get_login_url']);
        add_action('wp_ajax_nopriv_cnmx_social_login_url', [$this, 'get_login_url']);
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Social Login',
            'Social Login',
            'manage_options',
            'cnmx-social-login',
            [$this, 'admin_page']
        );
    }
    
    public function admin_page() {
        if (isset($_GET['settings_saved'])) {
            echo '<div class="notice notice-success"><p>Configuración guardada.</p></div>';
        }
        ?>
        <div class="wrap">
            <h1>Configuración Social Login</h1>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="cnmx_save_social_settings">
                <?php wp_nonce_field('cnmx_save_social'); ?>
                
                <h2>Google OAuth</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Client ID</th>
                        <td>
                            <input type="text" name="google_client_id" value="<?php echo esc_attr($this->get_google_client_id()); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Client Secret</th>
                        <td>
                            <input type="password" name="google_client_secret" value="<?php echo esc_attr($this->get_google_client_secret()); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">URI de redirección</th>
                        <td>
                            <code style="padding: 10px; display: block; background: #f0f0f0;"><?php echo home_url('/?cnmx_social=callback&provider=google'); ?></code>
                            <p class="description">Copia esta URL en Google Cloud Console</p>
                        </td>
                    </tr>
                </table>
                
                <h2>Facebook OAuth</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">App ID</th>
                        <td>
                            <input type="text" name="facebook_app_id" value="<?php echo esc_attr($this->get_facebook_app_id()); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">App Secret</th>
                        <td>
                            <input type="password" name="facebook_app_secret" value="<?php echo esc_attr($this->get_facebook_app_secret()); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">URI de redirección</th>
                        <td>
                            <code style="padding: 10px; display: block; background: #f0f0f0;"><?php echo home_url('/?cnmx_social=callback&provider=facebook'); ?></code>
                            <p class="description">Copia esta URL en Facebook Developer</p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" class="button button-primary" value="Guardar configuración">
                </p>
            </form>
        </div>
        <?php
    }
    
    public function save_settings() {
        check_admin_referer('cnmx_save_social');
        
        update_option('cnmx_google_client_id', sanitize_text_field($_POST['google_client_id'] ?? ''));
        update_option('cnmx_google_client_secret', sanitize_text_field($_POST['google_client_secret'] ?? ''));
        update_option('cnmx_facebook_app_id', sanitize_text_field($_POST['facebook_app_id'] ?? ''));
        update_option('cnmx_facebook_app_secret', sanitize_text_field($_POST['facebook_app_secret'] ?? ''));
        
        wp_redirect(add_query_arg('settings_saved', 'true', wp_get_referer()));
        exit;
    }
    
    public function check_social_login() {
        if (isset($_GET['cnmx_social']) && $_GET['cnmx_social'] === 'callback') {
            $this->handle_callback();
        }
    }
    
    public function get_login_url() {
        $provider = sanitize_text_field($_POST['provider'] ?? '');
        $redirect = sanitize_text_field($_POST['redirect'] ?? home_url('/perfil'));
        
        if ($provider === 'google') {
            $url = $this->get_google_auth_url($redirect);
        } elseif ($provider === 'facebook') {
            $url = $this->get_facebook_auth_url($redirect);
        } else {
            wp_send_json_error(['message' => 'Proveedor no válido']);
        }
        
        wp_send_json_success(['url' => $url]);
    }
    
    private function get_google_auth_url($redirect) {
        $client_id = $this->get_google_client_id();
        $redirect_uri = home_url('/?cnmx_social=callback&provider=google');
        
        if (empty($client_id)) {
            return home_url('/mi-cuenta?error=google_not_configured');
        }
        
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => $client_id,
            'redirect_uri' => $redirect_uri,
            'response_type' => 'code',
            'scope' => 'email profile',
            'state' => base64_encode($redirect),
        ]);
    }
    
    private function get_facebook_auth_url($redirect) {
        $app_id = $this->get_facebook_app_id();
        $redirect_uri = home_url('/?cnmx_social=callback&provider=facebook');
        
        if (empty($app_id)) {
            return home_url('/mi-cuenta?error=facebook_not_configured');
        }
        
        return 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query([
            'client_id' => $app_id,
            'redirect_uri' => $redirect_uri,
            'scope' => 'email',
            'state' => base64_encode($redirect),
        ]);
    }
    
    private function handle_callback() {
        $provider = sanitize_text_field($_GET['provider'] ?? '');
        $state = isset($_GET['state']) ? base64_decode($_GET['state']) : home_url('/perfil');
        $error = isset($_GET['error']) ? sanitize_text_field($_GET['error']) : '';
        
        if ($error) {
            wp_redirect(home_url('/mi-cuenta?error=' . $error));
            exit;
        }
        
        if ($provider === 'google') {
            $this->handle_google_callback($state);
        } elseif ($provider === 'facebook') {
            $this->handle_facebook_callback($state);
        }
        
        exit;
    }
    
    private function handle_google_callback($redirect) {
        $code = sanitize_text_field($_GET['code'] ?? '');
        
        if (empty($code)) {
            wp_redirect(home_url('/mi-cuenta?error=google_no_code'));
            exit;
        }
        
        $client_id = $this->get_google_client_id();
        $client_secret = $this->get_google_client_secret();
        $redirect_uri = home_url('/?cnmx_social=callback&provider=google');
        
        $token_response = wp_remote_post('https://oauth2.googleapis.com/token', [
            'body' => [
                'code' => $code,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'grant_type' => 'authorization_code',
            ]
        ]);
        
        if (is_wp_error($token_response)) {
            wp_redirect(home_url('/mi-cuenta?error=google_token'));
            exit;
        }
        
        $token_data = json_decode(wp_remote_retrieve_body($token_response), true);
        $access_token = $token_data['access_token'] ?? '';
        
        if (empty($access_token)) {
            wp_redirect(home_url('/mi-cuenta?error=google_token'));
            exit;
        }
        
        $user_response = wp_remote_get('https://www.googleapis.com/oauth2/v2/userinfo', [
            'headers' => ['Authorization' => 'Bearer ' . $access_token]
        ]);
        
        $user_data = json_decode(wp_remote_retrieve_body($user_response), true);
        
        if (!empty($user_data['email'])) {
            $this->create_or_login_user('google', [
                'email' => $user_data['email'],
                'name' => $user_data['name'] ?? 'Usuario Google',
                'picture' => $user_data['picture'] ?? '',
            ], $redirect);
        } else {
            wp_redirect(home_url('/mi-cuenta?error=google_userinfo'));
        }
    }
    
    private function handle_facebook_callback($redirect) {
        $code = sanitize_text_field($_GET['code'] ?? '');
        
        if (empty($code)) {
            wp_redirect(home_url('/mi-cuenta?error=facebook_no_code'));
            exit;
        }
        
        $app_id = $this->get_facebook_app_id();
        $app_secret = $this->get_facebook_app_secret();
        $redirect_uri = home_url('/?cnmx_social=callback&provider=facebook');
        
        $token_response = wp_remote_get('https://graph.facebook.com/v18.0/oauth/access_token', [
            'body' => [
                'client_id' => $app_id,
                'client_secret' => $app_secret,
                'redirect_uri' => $redirect_uri,
                'code' => $code,
            ]
        ]);
        
        $token_data = json_decode(wp_remote_retrieve_body($token_response), true);
        $access_token = $token_data['access_token'] ?? '';
        
        if (empty($access_token)) {
            wp_redirect(home_url('/mi-cuenta?error=facebook_token'));
            exit;
        }
        
        $user_response = wp_remote_get('https://graph.facebook.com/me?fields=id,name,email,picture&access_token=' . $access_token);
        $user_data = json_decode(wp_remote_retrieve_body($user_response), true);
        
        if (!empty($user_data['email'])) {
            $this->create_or_login_user('facebook', [
                'email' => $user_data['email'],
                'name' => $user_data['name'] ?? 'Usuario Facebook',
                'picture' => isset($user_data['picture']['url']) ? $user_data['picture']['url'] : '',
            ], $redirect);
        } else {
            wp_redirect(home_url('/mi-cuenta?error=facebook_email'));
        }
    }
    
    private function create_or_login_user($provider, $user_data, $redirect) {
        $email = $user_data['email'];
        $name = $user_data['name'];
        
        $user = get_user_by('email', $email);
        
        if ($user) {
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID);
            wp_redirect($redirect);
            exit;
        }
        
        $username = sanitize_user(explode('@', $email)[0]);
        $username = preg_replace('/[^a-z0-9]/', '', strtolower($username));
        
        $counter = 1;
        $original_username = $username;
        while (username_exists($username)) {
            $username = $original_username . $counter;
            $counter++;
        }
        
        $password = wp_generate_password(16);
        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            wp_redirect(home_url('/mi-cuenta?error=user_create'));
            exit;
        }
        
        wp_update_user([
            'ID' => $user_id,
            'display_name' => $name,
            'first_name' => explode(' ', $name)[0],
            'last_name' => implode(' ', array_slice(explode(' ', $name), 1)),
            'role' => 'subscriber',
        ]);
        
        update_user_meta($user_id, 'cnmx_social_provider', $provider);
        update_user_meta($user_id, 'cnmx_social_avatar', $user_data['picture']);
        update_user_meta($user_id, 'cnmx_tipo_cuenta', 'usuario');
        
        global $wpdb;
        $table = $wpdb->prefix . 'cnmx_usuarios_meta';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table) {
            $wpdb->insert($table, [
                'user_id' => $user_id,
                'megafonos' => 50,
                'nivel' => 'explorador',
                'fecha_registro' => current_time('mysql'),
            ]);
        }
        
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        
        wp_redirect($redirect);
        exit;
    }
}

new CNMX_Social_Login();
