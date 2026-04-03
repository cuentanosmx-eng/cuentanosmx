<?php
/**
 * Admin REST API
 */

if (!defined('ABSPATH')) exit;

class CNMX_Admin_REST_API {
    
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    public function register_routes() {
        register_rest_route('cnmx-admin/v1', '/negocios', [
            'methods' => 'GET',
            'callback' => [$this, 'get_negocios'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);
        
        register_rest_route('cnmx-admin/v1', '/usuarios', [
            'methods' => 'GET',
            'callback' => [$this, 'get_usuarios'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);
        
        register_rest_route('cnmx-admin/v1', '/metricas', [
            'methods' => 'GET',
            'callback' => [$this, 'get_metricas'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);
    }
    
    public function get_negocios($request) {
        $status = sanitize_text_field($request->get_param('status') ?: 'publish');
        $page = intval($request->get_param('page') ?: 1);
        $per_page = intval($request->get_param('per_page') ?: 20);
        
        $negocios = new WP_Query([
            'post_type' => 'negocio',
            'post_status' => $status,
            'posts_per_page' => $per_page,
            'paged' => $page,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
        
        $items = [];
        while ($negocios->have_posts()) {
            $negocios->the_post();
            global $post;
            
            $meta = get_post_meta($post->ID, '', true);
            $categorias = get_the_terms($post->ID, 'categoria');
            
            $items[] = [
                'id' => $post->ID,
                'nombre' => $post->post_title,
                'status' => $post->post_status,
                'fecha' => $post->post_date,
                'categorias' => $categorias ? array_map(function($c) {
                    return $c->name;
                }, $categorias) : [],
                'ciudad' => $meta['ciudad'][0] ?? '',
                'email' => $meta['email'][0] ?? '',
            ];
        }
        
        return [
            'items' => $items,
            'total' => $negocios->found_posts,
            'pages' => $negocios->max_num_pages,
        ];
    }
    
    public function get_usuarios($request) {
        $page = intval($request->get_param('page') ?: 1);
        $per_page = intval($request->get_param('per_page') ?: 20);
        
        $args = [
            'number' => $per_page,
            'offset' => ($page - 1) * $per_page,
            'orderby' => 'registered',
            'order' => 'DESC',
        ];
        
        $users = get_users($args);
        $total = count_users()['total_users'];
        
        global $wpdb;
        $items = [];
        foreach ($users as $user) {
            $megafonos = $wpdb->get_var($wpdb->prepare(
                "SELECT megafonos FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d",
                $user->ID
            ));
            
            $items[] = [
                'id' => $user->ID,
                'nombre' => $user->display_name,
                'email' => $user->user_email,
                'fecha' => $user->user_registered,
                'megafonos' => intval($megafonos) ?: 0,
                'rol' => implode(', ', $user->roles),
            ];
        }
        
        return [
            'items' => $items,
            'total' => $total,
        ];
    }
    
    public function get_metricas($request) {
        global $wpdb;
        
        $metricas_table = $wpdb->prefix . 'cnmx_metricas';
        $resenas_table = $wpdb->prefix . 'cnmx_resenas';
        
        $vistas_hoy = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$metricas_table} WHERE tipo = 'vista' AND DATE(fecha) = CURDATE()"
        );
        
        $clicks_hoy = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$metricas_table} WHERE tipo = 'click' AND DATE(fecha) = CURDATE()"
        );
        
        $resenas_hoy = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$resenas_table} WHERE DATE(fecha) = CURDATE()"
        );
        
        $top_negocios = $wpdb->get_results(
            "SELECT m.negocio_id, p.post_title, COUNT(*) as views 
             FROM {$metricas_table} m 
             JOIN {$wpdb->posts} p ON p.ID = m.negocio_id 
             WHERE m.tipo = 'vista' AND DATE(m.fecha) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
             GROUP BY m.negocio_id 
             ORDER BY views DESC LIMIT 10"
        );
        
        return [
            'vistas_hoy' => intval($vistas_hoy),
            'clicks_hoy' => intval($clicks_hoy),
            'resenas_hoy' => intval($resenas_hoy),
            'top_negocios' => $top_negocios,
        ];
    }
}

new CNMX_Admin_REST_API();
