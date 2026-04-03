<?php
/**
 * CNMX Biz REST API - Endpoints para empresas
 */

if (!defined('ABSPATH')) exit;

class CNMX_Biz_REST_API {
    
    private $namespace = 'cnmx-biz/v1';
    
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    public function register_routes() {
        register_rest_route($this->namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$this, 'get_dashboard'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/membresia', [
            'methods' => 'GET',
            'callback' => [$this, 'get_membresia'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/membresia/renovar', [
            'methods' => 'POST',
            'callback' => [$this, 'renovar_membresia'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/negocio', [
            'methods' => 'GET',
            'callback' => [$this, 'get_negocio'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/negocio', [
            'methods' => 'POST',
            'callback' => [$this, 'actualizar_negocio'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/metricas', [
            'methods' => 'GET',
            'callback' => [$this, 'get_metricas'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
        
        register_rest_route($this->namespace, '/registro', [
            'methods' => 'POST',
            'callback' => [$this, 'registrar_empresa'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/login', [
            'methods' => 'POST',
            'callback' => [$this, 'login_empresa'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/logros', [
            'methods' => 'GET',
            'callback' => [$this, 'get_logros'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route($this->namespace, '/mis-logros', [
            'methods' => 'GET',
            'callback' => [$this, 'get_mis_logros'],
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
        
        register_rest_route($this->namespace, '/mis-recompensas', [
            'methods' => 'GET',
            'callback' => [$this, 'get_mis_recompensas'],
            'permission_callback' => [$this, 'check_auth'],
        ]);
    }
    
    public function check_auth() {
        return is_user_logged_in();
    }
    
    public function get_dashboard($request) {
        require_once CNMX_BIZ_PATH . 'includes/class-cnmx-biz-dashboard.php';
        $dashboard = new CNMX_Biz_Dashboard();
        
        $data = $dashboard->get_dashboard_data(get_current_user_id());
        
        if (!$data) {
            return new WP_Error('no_negocio', 'No tienes un negocio asociado', ['status' => 404]);
        }
        
        return rest_ensure_response($data);
    }
    
    public function get_membresia($request) {
        require_once CNMX_BIZ_PATH . 'includes/class-cnmx-biz-membresia.php';
        $membresia = new CNMX_Biz_Membresia();
        
        $negocio_id = get_user_meta(get_current_user_id(), 'cnmx_negocio_asociado', true);
        
        if (!$negocio_id) {
            return new WP_Error('no_negocio', 'No tienes un negocio asociado', ['status' => 404]);
        }
        
        $plan_actual = $membresia->get_plan_actual($negocio_id);
        $planes = $membresia->get_planes();
        
        return rest_ensure_response([
            'actual' => $plan_actual,
            'planes' => $planes,
        ]);
    }
    
    public function renovar_membresia($request) {
        $plan = $request->get_param('plan');
        
        if (!in_array($plan, ['basico', 'premium', 'enterprise'])) {
            return new WP_Error('plan_invalido', 'Plan inválido', ['status' => 400]);
        }
        
        $negocio_id = get_user_meta(get_current_user_id(), 'cnmx_negocio_asociado', true);
        
        if (!$negocio_id) {
            return new WP_Error('no_negocio', 'No tienes un negocio asociado', ['status' => 404]);
        }
        
        require_once CNMX_BIZ_PATH . 'includes/class-cnmx-biz-membresia.php';
        $membresia = new CNMX_Biz_Membresia();
        
        $membresia->activar_plan($negocio_id, $plan, 'manual_' . time());
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Membresía actualizada correctamente',
        ]);
    }
    
    public function get_negocio($request) {
        $user_id = get_current_user_id();
        $negocio_id = get_user_meta($user_id, 'cnmx_negocio_asociado', true);
        
        if (!$negocio_id) {
            $negocios = get_posts([
                'post_type' => 'negocio',
                'post_status' => ['publish', 'pending', 'draft'],
                'posts_per_page' => 1,
                'meta_query' => [
                    ['key' => 'cnmx_propietario_id', 'value' => $user_id]
                ]
            ]);
            
            if (!empty($negocios)) {
                $negocio_id = $negocios[0]->ID;
            }
        }
        
        if (!$negocio_id) {
            return new WP_Error('no_negocio', 'No tienes un negocio asociado', ['status' => 404]);
        }
        
        $negocio = get_post($negocio_id);
        
        $categorias = wp_get_post_terms($negocio_id, 'categoria');
        $categoria_slug = !empty($categorias) ? $categorias[0]->slug : '';
        
        $data = [
            'id' => $negocio_id,
            'nombre' => $negocio->post_title,
            'descripcion' => $negocio->post_content,
            'categoria' => $categoria_slug,
            'telefono' => get_post_meta($negocio_id, 'cnmx_telefono', true),
            'whatsapp' => get_post_meta($negocio_id, 'cnmx_whatsapp', true),
            'email' => get_post_meta($negocio_id, 'cnmx_email', true),
            'direccion' => get_post_meta($negocio_id, 'cnmx_direccion', true),
            'ciudad' => get_post_meta($negocio_id, 'cnmx_ciudad', true),
            'sitio_web' => get_post_meta($negocio_id, 'cnmx_sitio_web', true),
            'facebook' => get_post_meta($negocio_id, 'cnmx_facebook', true),
            'instagram' => get_post_meta($negocio_id, 'cnmx_instagram', true),
            'twitter' => get_post_meta($negocio_id, 'cnmx_twitter', true),
            'horarios' => [
                'lunes' => ['apertura' => get_post_meta($negocio_id, 'cnmx_lunes_apertura', true), 'cierre' => get_post_meta($negocio_id, 'cnmx_lunes_cierre', true), 'cerrado' => get_post_meta($negocio_id, 'cnmx_lunes_cerrado', true)],
                'martes' => ['apertura' => get_post_meta($negocio_id, 'cnmx_martes_apertura', true), 'cierre' => get_post_meta($negocio_id, 'cnmx_martes_cierre', true), 'cerrado' => get_post_meta($negocio_id, 'cnmx_martes_cerrado', true)],
                'miercoles' => ['apertura' => get_post_meta($negocio_id, 'cnmx_miercoles_apertura', true), 'cierre' => get_post_meta($negocio_id, 'cnmx_miercoles_cierre', true), 'cerrado' => get_post_meta($negocio_id, 'cnmx_miercoles_cerrado', true)],
                'jueves' => ['apertura' => get_post_meta($negocio_id, 'cnmx_jueves_apertura', true), 'cierre' => get_post_meta($negocio_id, 'cnmx_jueves_cierre', true), 'cerrado' => get_post_meta($negocio_id, 'cnmx_jueves_cerrado', true)],
                'viernes' => ['apertura' => get_post_meta($negocio_id, 'cnmx_viernes_apertura', true), 'cierre' => get_post_meta($negocio_id, 'cnmx_viernes_cierre', true), 'cerrado' => get_post_meta($negocio_id, 'cnmx_viernes_cerrado', true)],
                'sabado' => ['apertura' => get_post_meta($negocio_id, 'cnmx_sabado_apertura', true), 'cierre' => get_post_meta($negocio_id, 'cnmx_sabado_cierre', true), 'cerrado' => get_post_meta($negocio_id, 'cnmx_sabado_cerrado', true)],
                'domingo' => ['apertura' => get_post_meta($negocio_id, 'cnmx_domingo_apertura', true), 'cierre' => get_post_meta($negocio_id, 'cnmx_domingo_cierre', true), 'cerrado' => get_post_meta($negocio_id, 'cnmx_domingo_cerrado', true)],
            ],
            'animacion' => [
                'entrada' => get_post_meta($negocio_id, 'cnmx_animacion_entrada', true),
                'hover' => get_post_meta($negocio_id, 'cnmx_animacion_hover', true),
                'icono' => get_post_meta($negocio_id, 'cnmx_animacion_icono', true),
                'color_primario' => get_post_meta($negocio_id, 'cnmx_color_primario', true),
                'color_secundario' => get_post_meta($negocio_id, 'cnmx_color_secundario', true),
            ],
            'status' => $negocio->post_status,
        ];
        
        return rest_ensure_response($data);
    }
    
    public function actualizar_negocio($request) {
        $user_id = get_current_user_id();
        $negocio_id = get_user_meta($user_id, 'cnmx_negocio_asociado', true);
        
        if (!$negocio_id) {
            $negocios = get_posts([
                'post_type' => 'negocio',
                'post_status' => ['publish', 'pending', 'draft'],
                'posts_per_page' => 1,
                'meta_query' => [
                    ['key' => 'cnmx_propietario_id', 'value' => $user_id]
                ]
            ]);
            
            if (!empty($negocios)) {
                $negocio_id = $negocios[0]->ID;
            }
        }
        
        if (!$negocio_id) {
            error_log("CNMX Debug - User ID: $user_id, Meta: " . var_export(get_user_meta($user_id), true));
            return new WP_Error('no_negocio', 'No tienes un negocio asociado', ['status' => 404]);
        }
        
        $nombre = $request->get_param('nombre');
        if ($nombre) {
            wp_update_post([
                'ID' => $negocio_id,
                'post_title' => sanitize_text_field($nombre),
            ]);
        }
        
        $descripcion = $request->get_param('descripcion');
        if ($descripcion !== null) {
            wp_update_post([
                'ID' => $negocio_id,
                'post_content' => sanitize_textarea_field($descripcion),
            ]);
        }
        
        $categoria = $request->get_param('categoria');
        if ($categoria) {
            wp_set_object_terms($negocio_id, $categoria, 'categoria');
        }
        
        $campos_meta = [
            'direccion', 'ciudad', 'telefono', 'whatsapp', 'email', 'sitio_web',
            'facebook', 'instagram', 'twitter',
            'lunes_apertura', 'lunes_cierre', 'lunes_cerrado',
            'martes_apertura', 'martes_cierre', 'martes_cerrado',
            'miercoles_apertura', 'miercoles_cierre', 'miercoles_cerrado',
            'jueves_apertura', 'jueves_cierre', 'jueves_cerrado',
            'viernes_apertura', 'viernes_cierre', 'viernes_cerrado',
            'sabado_apertura', 'sabado_cierre', 'sabado_cerrado',
            'domingo_apertura', 'domingo_cierre', 'domingo_cerrado',
            'latitud', 'longitud',
            'animacion_entrada', 'animacion_hover', 'animacion_icono',
            'color_primario', 'color_secundario'
        ];
        
        foreach ($campos_meta as $campo) {
            $valor = $request->get_param($campo);
            if ($valor !== null && $valor !== '') {
                update_post_meta($negocio_id, 'cnmx_' . $campo, sanitize_text_field($valor));
            }
        }
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Negocio actualizado correctamente',
        ]);
    }
    
    public function get_metricas($request) {
        $dias = intval($request->get_param('dias')) ?: 30;
        
        $negocio_id = get_user_meta(get_current_user_id(), 'cnmx_negocio_asociado', true);
        
        if (!$negocio_id) {
            return new WP_Error('no_negocio', 'No tienes un negocio asociado', ['status' => 404]);
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'cnmx_metricas';
        
        $metricas = $wpdb->get_results($wpdb->prepare(
            "SELECT tipo, fecha, cantidad 
             FROM $table 
             WHERE negocio_id = %d 
             AND fecha >= DATE_SUB(CURDATE(), INTERVAL %d DAY)
             ORDER BY fecha ASC",
            $negocio_id, $dias
        ));
        
        return rest_ensure_response(['metricas' => $metricas]);
    }
    
    public function registrar_empresa($request) {
        $email = $request->get_param('email');
        $password = $request->get_param('password');
        $nombre_negocio = $request->get_param('nombre_negocio');
        
        if (!$email || !$password || !$nombre_negocio) {
            return new WP_Error('campos_requeridos', 'Todos los campos son requeridos', ['status' => 400]);
        }
        
        if (email_exists($email)) {
            return new WP_Error('email_existe', 'El email ya está registrado', ['status' => 400]);
        }
        
        $user_id = wp_create_user($email, $password, $email);
        
        if (is_wp_error($user_id)) {
            return $user_id;
        }
        
        wp_update_user([
            'ID' => $user_id,
            'display_name' => $nombre_negocio,
        ]);
        
        update_user_meta($user_id, 'cnmx_tipo_cuenta', 'empresa');
        
        $negocio_id = wp_insert_post([
            'post_title' => sanitize_text_field($nombre_negocio),
            'post_type' => 'negocio',
            'post_status' => 'pending',
        ]);
        
        update_user_meta($user_id, 'cnmx_negocio_asociado', $negocio_id);
        update_post_meta($negocio_id, 'cnmx_propietario_id', $user_id);
        update_post_meta($negocio_id, 'cnmx_membresia_plan', 'gratis');
        
        $credenciales = [
            'user_login' => $email,
            'user_password' => $password,
            'remember' => true,
        ];
        
        wp_signon($credenciales);
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Empresa registrada correctamente',
            'negocio_id' => $negocio_id,
        ]);
    }
    
    public function login_empresa($request) {
        $email = $request->get_param('email');
        $password = $request->get_param('password');
        
        if (!$email || !$password) {
            return new WP_Error('campos_requeridos', 'Email y password requeridos', ['status' => 400]);
        }
        
        $credenciales = [
            'user_login' => $email,
            'user_password' => $password,
            'remember' => true,
        ];
        
        $user = wp_signon($credenciales, false);
        
        if (is_wp_error($user)) {
            return new WP_Error('login_fallido', 'Credenciales incorrectas', ['status' => 401]);
        }
        
        $tipo_cuenta = get_user_meta($user->ID, 'cnmx_tipo_cuenta', true);
        
        if ($tipo_cuenta !== 'empresa') {
            wp_logout();
            return new WP_Error('no_empresa', 'Esta cuenta no es de empresa', ['status' => 403]);
        }
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Login exitoso',
            'redirect' => home_url('/dashboard-empresa'),
        ]);
    }
    
    public function get_logros($request) {
        $logros = CNMX_Biz_Logros::get_logros_activos();
        return rest_ensure_response(['logros' => $logros]);
    }
    
    public function get_mis_logros($request) {
        $user_id = get_current_user_id();
        $logros = CNMX_Biz_Logros::get_logros_activos();
        
        $mis_logros = [];
        foreach ($logros as $logro) {
            $obtuvo = get_user_meta($user_id, 'cnmx_logro_' . $logro['id'], true);
            $logro['obtenido'] = !empty($obtuvo);
            $logro['fecha_obtenido'] = $obtuvo;
            $mis_logros[] = $logro;
        }
        
        return rest_ensure_response(['logros' => $mis_logros]);
    }
    
    public function get_recompensas($request) {
        $recompensas = CNMX_Biz_Recompensas::get_recompensas_activas();
        return rest_ensure_response(['recompensas' => $recompensas]);
    }
    
    public function canjear_recompensa($request) {
        $user_id = get_current_user_id();
        $recompensa_id = intval($request->get_param('recompensa_id'));
        
        if (!$recompensa_id) {
            return new WP_Error('recompensa_requerida', 'ID de recompensa requerido', ['status' => 400]);
        }
        
        $resultado = CNMX_Biz_Recompensas::canjear($user_id, $recompensa_id);
        
        if (!$resultado['success']) {
            return new WP_Error('canje_fallido', $resultado['message'], ['status' => 400]);
        }
        
        return rest_ensure_response($resultado);
    }
    
    public function get_mis_recompensas($request) {
        $user_id = get_current_user_id();
        $canjeadas = CNMX_Biz_Recompensas::get_canjeadas_usuario($user_id);
        
        global $wpdb;
        $table = $wpdb->prefix . 'cnmx_usuarios_meta';
        $usuario = $wpdb->get_row($wpdb->prepare("SELECT megafonos FROM $table WHERE user_id = %d", $user_id));
        $megafonos = $usuario ? intval($usuario->megafonos) : 0;
        
        return rest_ensure_response([
            'canjeadas' => $canjeadas,
            'megafonos' => $megafonos,
        ]);
    }
}

new CNMX_Biz_REST_API();