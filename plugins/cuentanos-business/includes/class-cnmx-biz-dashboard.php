<?php
/**
 * CNMX Biz Dashboard - Panel de control para empresas
 */

if (!defined('ABSPATH')) exit;

class CNMX_Biz_Dashboard {
    
    public function __construct() {
        add_action('wp_loaded', [$this, 'handle_requests']);
    }
    
    public function handle_requests() {
        if (!is_user_logged_in()) {
            return;
        }
        
        $negocio_id = get_user_meta(get_current_user_id(), 'cnmx_negocio_asociado', true);
        
        if (!$negocio_id) {
            return;
        }
        
        $this->actualizar_stats($negocio_id);
    }
    
    public function get_dashboard_data($user_id) {
        $negocio_id = get_user_meta($user_id, 'cnmx_negocio_asociado', true);
        
        if (!$negocio_id) {
            return null;
        }
        
        $negocio = get_post($negocio_id);
        
        if (!$negocio) {
            return null;
        }
        
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        
        $meta = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_prefix}cnmx_negocios_meta WHERE negocio_id = %d",
            $negocio_id
        ));
        
        $membresia = $this->get_membresia($negocio_id);
        
        $resenas = $wpdb->get_results($wpdb->prepare(
            "SELECT COUNT(*) as total, AVG(calificacion) as promedio 
             FROM {$table_prefix}cnmx_resenas 
             WHERE negocio_id = %d AND status = 'aprobado'",
            $negocio_id
        ));
        
        return [
            'negocio' => [
                'id' => $negocio->ID,
                'nombre' => $negocio->post_title,
                'slug' => $negocio->post_name,
                'url' => get_permalink($negocio->ID),
                'estado' => $meta ? $meta->status_admin : 'pendiente',
            ],
            'membresia' => $membresia,
            'metricas' => [
                'vistas' => $meta ? intval($meta->total_vistas) : 0,
                'resenas' => $resenas[0]->total ?? 0,
                'rating' => round($resenas[0]->promedio ?? 0, 1),
                'favoritos' => $meta ? intval($meta->total_favoritos) : 0,
            ],
            'resenas_recientes' => $this->get_resenas_recientes($negocio_id),
        ];
    }
    
    private function get_membresia($negocio_id) {
        $inicio = get_post_meta($negocio_id, 'cnmx_membresia_inicio', true);
        $plan = get_post_meta($negocio_id, 'cnmx_membresia_plan', true);
        $renovacion = get_post_meta($negocio_id, 'cnmx_membresia_renovacion', true);
        
        if (!$inicio) {
            return [
                'activa' => false,
                'plan' => 'gratis',
                'inicio' => null,
                'renovacion' => null,
                'dias_restantes' => 0,
            ];
        }
        
        $dias_restantes = 0;
        if ($renovacion) {
            $dias_restantes = ceil((strtotime($renovacion) - time()) / 86400);
        }
        
        return [
            'activa' => $dias_restantes > 0,
            'plan' => $plan ?: 'basico',
            'inicio' => $inicio,
            'renovacion' => $renovacion,
            'dias_restantes' => max(0, $dias_restantes),
        ];
    }
    
    private function get_resenas_recientes($negocio_id, $limit = 5) {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT r.*, u.display_name 
             FROM {$table_prefix}cnmx_resenas r
             JOIN {$table_prefix}users u ON u.ID = r.user_id
             WHERE r.negocio_id = %d AND r.status = 'aprobado'
             ORDER BY r.fecha DESC
             LIMIT %d",
            $negocio_id, $limit
        ));
    }
    
    private function actualizar_stats($negocio_id) {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        
        $favoritos = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_prefix}cnmx_favoritos WHERE negocio_id = %d",
            $negocio_id
        ));
        
        $wpdb->query($wpdb->prepare(
            "UPDATE {$table_prefix}cnmx_negocios_meta SET total_favoritos = %d WHERE negocio_id = %d",
            $favoritos, $negocio_id
        ));
    }
}