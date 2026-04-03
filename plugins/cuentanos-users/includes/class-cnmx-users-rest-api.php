<?php
/**
 * REST API for Users
 */

if (!defined('ABSPATH')) exit;

class CNMX_Users_REST_API {
    
    private $namespace = 'cuentanos/v1';
    
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    public function register_routes() {
        register_rest_route($this->namespace, '/auth/register', [
            'methods' => 'POST',
            'callback' => [$this, 'register_user'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/auth/login', [
            'methods' => 'POST',
            'callback' => [$this, 'login_user'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/usuario/perfil', [
            'methods' => 'GET',
            'callback' => [$this, 'get_profile'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/usuario/perfil', [
            'methods' => 'POST',
            'callback' => [$this, 'update_profile'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/favoritos', [
            'methods' => 'GET',
            'callback' => [$this, 'get_favoritos'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/usuario/megafonos', [
            'methods' => 'GET',
            'callback' => [$this, 'get_megafonos'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/usuario/logros', [
            'methods' => 'GET',
            'callback' => [$this, 'get_logros'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/recompensas', [
            'methods' => 'GET',
            'callback' => [$this, 'get_recompensas'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/recompensas/canjear', [
            'methods' => 'POST',
            'callback' => [$this, 'canjear_recompensa'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/recompensas/mis', [
            'methods' => 'GET',
            'callback' => [$this, 'get_canjeadas'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/usuario/negocio', [
            'methods' => 'GET',
            'callback' => [$this, 'get_negocio_usuario'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
    }
    
    public function check_auth() {
        return is_user_logged_in();
    }
    
    public function register_user($request) {
        $email = sanitize_email($request->get_param('email'));
        $password = $request->get_param('password');
        $nombre = sanitize_text_field($request->get_param('nombre'));
        $cumpleanos = sanitize_text_field($request->get_param('cumpleanos'));
        
        if (empty($email) || empty($password) || empty($nombre)) {
            return new WP_Error('missing_fields', 'Todos los campos son requeridos', ['status' => 400]);
        }
        
        if (email_exists($email)) {
            return new WP_Error('email_exists', 'Este email ya está registrado', ['status' => 400]);
        }
        
        $user_id = wp_create_user(sanitize_user($email), $password, $email);
        
        if (is_wp_error($user_id)) {
            return new WP_Error('create_failed', $user_id->get_error_message(), ['status' => 500]);
        }
        
        wp_update_user([
            'ID' => $user_id,
            'display_name' => $nombre,
            'first_name' => $nombre,
        ]);
        
        if ($cumpleanos) {
            update_user_meta($user_id, 'cnmx_cumpleanos', $cumpleanos);
        }
        
        require_once ABSPATH . WPINC . '/pluggable.php';
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        
        $this->init_user_meta($user_id);
        
        return [
            'success' => true,
            'user' => [
                'id' => $user_id,
                'email' => $email,
                'nombre' => $nombre,
                'megafonos' => 10,
            ],
        ];
    }
    
    public function login_user($request) {
        $email = sanitize_email($request->get_param('email'));
        $password = $request->get_param('password');
        
        $user = get_user_by('email', $email);
        
        if (!$user || !wp_check_password($password, $user->user_pass, $user->ID)) {
            return new WP_Error('invalid_login', 'Email o contraseña incorrectos', ['status' => 401]);
        }
        
        require_once ABSPATH . WPINC . '/pluggable.php';
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);
        
        $megafonos = $this->get_user_megafonos($user->ID);
        
        return [
            'success' => true,
            'user' => [
                'id' => $user->ID,
                'email' => $user->user_email,
                'nombre' => $user->display_name,
                'megafonos' => $megafonos,
            ],
        ];
    }
    
    public function get_profile($request) {
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);
        $megafonos = $this->get_user_megafonos($user_id);
        $nivel = $this->get_user_nivel($megafonos);
        
        $cumpleanos_bonus = $this->check_birthday_bonus($user_id);
        if ($cumpleanos_bonus) {
            $megafonos = $this->get_user_megafonos($user_id);
        }
        
        $historial_bonos = get_user_meta($user_id, 'cnmx_historial_bonos', true) ?: [];
        
        return [
            'id' => $user_id,
            'email' => $user->user_email,
            'nombre' => $user->display_name,
            'avatar' => get_avatar_url($user_id),
            'megafonos' => $megafonos,
            'nivel' => $nivel,
            'cumpleanos' => get_user_meta($user_id, 'cnmx_cumpleanos', true),
            'cumpleanos_bonus' => $cumpleanos_bonus,
            'fecha_registro' => $user->user_registered,
            'stats' => $this->get_user_stats($user_id),
            'historial_bonos' => $historial_bonos,
        ];
    }
    
    public function update_profile($request) {
        $user_id = get_current_user_id();
        $nombre = sanitize_text_field($request->get_param('nombre'));
        
        if ($nombre) {
            wp_update_user([
                'ID' => $user_id,
                'display_name' => $nombre,
                'first_name' => $nombre,
            ]);
        }
        
        $avatar_id = intval($request->get_param('avatar_id'));
        if ($avatar_id) {
            update_user_meta($user_id, '_cnmx_avatar_id', $avatar_id);
        }
        
        return ['success' => true, 'message' => 'Perfil actualizado'];
    }
    
    public function get_favoritos($request) {
        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'cnmx_favoritos';
        
        $favoritos = $wpdb->get_results($wpdb->prepare(
            "SELECT f.*, p.post_title, p.guid as post_url 
             FROM {$table} f 
             JOIN {$wpdb->posts} p ON p.ID = f.negocio_id 
             WHERE f.user_id = %d 
             ORDER BY f.fecha DESC",
            $user_id
        ));
        
        return ['favoritos' => $favoritos];
    }
    
    public function get_megafonos($request) {
        $user_id = get_current_user_id();
        $megafonos = $this->get_user_megafonos($user_id);
        $nivel = $this->get_user_nivel($megafonos);
        
        return [
            'megafonos' => $megafonos,
            'nivel' => $nivel,
            'siguiente_nivel' => $this->get_siguiente_nivel($megafonos),
        ];
    }
    
    public function get_logros($request) {
        $user_id = get_current_user_id();
        
        $logros = get_posts([
            'post_type' => 'cnmx_logro',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ]);
        
        $resultados = [];
        foreach ($logros as $logro) {
            $obtenido = get_user_meta($user_id, 'cnmx_logro_' . $logro->ID, true);
            $resultados[] = [
                'id' => $logro->ID,
                'post_title' => $logro->post_title,
                'post_content' => $logro->post_content,
                'progreso' => $obtenido ? 'completado' : null,
                'obtenido' => !empty($obtenido),
            ];
        }
        
        return ['logros' => $resultados];
    }
    
    private function init_user_meta($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'cnmx_usuarios_meta';
        
        $wpdb->insert($table, [
            'user_id' => $user_id,
            'megafonos' => 50,
            'nivel' => 'explorador',
            'ultimos_megafonos' => json_encode([['tipo' => 'registro', 'puntos' => 50, 'fecha' => current_time('mysql')]]),
            'acciones_hoy' => json_encode(['registro' => 1]),
            'ultimo_reset' => current_time('Y-m-d'),
            'fecha_registro' => current_time('mysql'),
        ]);
    }
    
    private function get_user_megafonos($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'cnmx_usuarios_meta';
        
        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT megafonos FROM {$table} WHERE user_id = %d",
            $user_id
        ));
        
        return intval($result) ?: 0;
    }
    
    private function add_megafonos($user_id, $puntos, $tipo, $descripcion) {
        global $wpdb;
        $table = $wpdb->prefix . 'cnmx_usuarios_meta';
        
        $actual = $this->get_user_megafonos($user_id);
        $nuevo = max(0, $actual + $puntos);
        
        $wpdb->update($table, ['megafonos' => $nuevo], ['user_id' => $user_id]);
        
        $historial_table = $wpdb->prefix . 'cnmx_historial_puntos';
        $wpdb->insert($historial_table, [
            'user_id' => $user_id,
            'tipo' => $tipo,
            'puntos' => $puntos,
            'descripcion' => $descripcion,
            'created_at' => current_time('mysql'),
        ]);
        
        return $nuevo;
    }
    
    private function get_user_nivel($megafonos) {
        if ($megafonos >= 1000) return 'influencer';
        if ($megafonos >= 500) return 'critico';
        return 'explorador';
    }
    
    private function get_siguiente_nivel($megafonos) {
        if ($megafonos < 500) return ['nombre' => 'Crítico', 'necesita' => 500 - $megafonos];
        if ($megafonos < 1000) return ['nombre' => 'Influencer', 'necesita' => 1000 - $megafonos];
        return null;
    }
    
    private function get_user_stats($user_id) {
        global $wpdb;
        
        $resenas = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cnmx_resenas WHERE user_id = %d",
            $user_id
        ));
        
        $favoritos = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cnmx_favoritos WHERE user_id = %d",
            $user_id
        ));
        
        return [
            'resenas' => intval($resenas),
            'favoritos' => intval($favoritos),
        ];
    }
    
    public function get_recompensas($request) {
        $user_id = get_current_user_id();
        $megafonos = $this->get_user_megafonos($user_id);
        
        $args = [
            'post_type' => 'cnmx_recompensa',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'cnmx_recompensa_activa',
                    'value' => 'si',
                ]
            ]
        ];
        
        $query = new WP_Query($args);
        $recompensas = [];
        
        foreach ($query->posts as $post) {
            $megafonos_req = intval(get_post_meta($post->ID, 'cnmx_recompensa_megafonos', true));
            $recompensas[] = [
                'id' => $post->ID,
                'titulo' => $post->post_title,
                'descripcion' => $post->post_content,
                'imagen' => get_the_post_thumbnail_url($post->ID, 'medium') ?: 'https://via.placeholder.com/300x200?text=Recompensa',
                'megafonos' => $megafonos_req,
                'codigo' => get_post_meta($post->ID, 'cnmx_recompensa_codigo', true),
                'instrucciones' => get_post_meta($post->ID, 'cnmx_recompensa_instrucciones', true),
                'disponible' => $megafonos >= $megafonos_req,
            ];
        }
        
        usort($recompensas, function($a, $b) {
            return $a['megafonos'] - $b['megafonos'];
        });
        
        return [
            'recompensas' => $recompensas,
            'megafonos_usuario' => $megafonos,
        ];
    }
    
    public function canjear_recompensa($request) {
        $user_id = get_current_user_id();
        $recompensa_id = intval($request->get_param('recompensa_id'));
        
        if (!$recompensa_id) {
            return new WP_Error('missing_id', 'ID de recompensa requerido', ['status' => 400]);
        }
        
        if (!class_exists('CNMX_Biz_Recompensas')) {
            require_once WP_PLUGIN_DIR . '/cuentanos-business/includes/class-cnmx-biz-recompensas.php';
        }
        
        $resultado = CNMX_Biz_Recompensas::canjear($user_id, $recompensa_id);
        
        if (!$resultado['success']) {
            return new WP_Error('canje_failed', $resultado['message'], ['status' => 400]);
        }
        
        return $resultado;
    }
    
    public function get_canjeadas($request) {
        $user_id = get_current_user_id();
        $canjeadas = get_user_meta($user_id, 'cnmx_recompensas_canjeadas', true) ?: [];
        
        return ['canjeadas' => $canjeadas];
    }
    
    public function check_birthday_bonus($user_id) {
        $cumpleanos = get_user_meta($user_id, 'cnmx_cumpleanos', true);
        
        if (!$cumpleanos) return false;
        
        $ultimo_bono = get_user_meta($user_id, 'cnmx_cumpleanos_bono_fecha', true);
        $hoy = date('Y-m-d');
        
        $fecha_cumple = date('Y') . '-' . substr($cumpleanos, 5, 2) . '-' . substr($cumpleanos, 8, 2);
        
        if ($ultimo_bono === $fecha_cumple) return false;
        
        $dia_mes_hoy = date('m-d');
        $dia_mes_cumple = substr($cumpleanos, 5, 2) . '-' . substr($cumpleanos, 8, 2);
        
        if ($dia_mes_hoy !== $dia_mes_cumple) return false;
        
        $bonus = 50;
        $this->add_megafonos($user_id, $bonus, 'cumpleanos', '¡Feliz cumpleaños!');
        
        update_user_meta($user_id, 'cnmx_cumpleanos_bono_fecha', $hoy);
        
        return ['bonus' => $bonus, 'mensaje' => '¡Feliz cumpleaños! Recibiste ' . $bonus . ' Megáfonos'];
    }
    
    public function get_negocio_usuario($request) {
        $user_id = get_current_user_id();
        $negocio_id = get_user_meta($user_id, 'cnmx_negocio_asociado', true);
        
        if (!$negocio_id) {
            return ['negocio' => null, 'mensaje' => 'No tienes un negocio registrado'];
        }
        
        $negocio = get_post($negocio_id);
        
        if (!$negocio || $negocio->post_type !== 'negocio') {
            return ['negocio' => null, 'mensaje' => 'Negocio no encontrado'];
        }
        
        $telefono = get_post_meta($negocio_id, 'cnmx_telefono', true);
        $whatsapp = get_post_meta($negocio_id, 'cnmx_whatsapp', true);
        $email = get_post_meta($negocio_id, 'cnmx_email', true);
        $direccion = get_post_meta($negocio_id, 'cnmx_direccion', true);
        $ciudad = get_post_meta($negocio_id, 'cnmx_ciudad', true);
        
        return [
            'negocio' => [
                'id' => $negocio_id,
                'nombre' => $negocio->post_title,
                'descripcion' => $negocio->post_content,
                'status' => $negocio->post_status,
                'telefono' => $telefono,
                'whatsapp' => $whatsapp,
                'email' => $email,
                'direccion' => $direccion,
                'ciudad' => $ciudad,
                'plan' => get_post_meta($negocio_id, 'cnmx_membresia_plan', true) ?: 'gratis',
            ]
        ];
    }
}

new CNMX_Users_REST_API();
