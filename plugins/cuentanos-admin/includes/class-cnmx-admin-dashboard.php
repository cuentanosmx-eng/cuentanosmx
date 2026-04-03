<?php
/**
 * Admin Dashboard Class
 */

if (!defined('ABSPATH')) exit;

class CNMX_Admin_Dashboard {
    
    public function __construct() {
        add_action('wp_ajax_cnmx_get_stats', [$this, 'ajax_get_stats']);
    }
    
    public function ajax_get_stats() {
        check_ajax_referer('cnmx_admin_nonce', 'nonce');
        
        global $wpdb;
        
        $total_negocios = wp_count_posts('negocio')->publish;
        $negocios_pendientes = wp_count_posts('negocio')->pending;
        $total_usuarios = count_users()['total_users'];
        
        $total_resenas = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cnmx_resenas WHERE status = 'aprobado'");
        $total_megafonos = $wpdb->get_var("SELECT SUM(meta_value) FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE meta_key = 'megafonos'");
        
        $negocios_recientes = $wpdb->get_results(
            "SELECT p.ID, p.post_title, p.post_date 
             FROM {$wpdb->posts} p 
             WHERE p.post_type = 'negocio' AND p.post_status = 'publish' 
             ORDER BY p.post_date DESC LIMIT 5"
        );
        
        wp_send_json([
            'total_negocios' => $total_negocios,
            'pendientes' => $negocios_pendientes,
            'total_usuarios' => $total_usuarios,
            'total_resenas' => intval($total_resenas),
            'total_megafonos' => intval($total_megafonos),
            'negocios_recientes' => $negocios_recientes,
        ]);
    }
}
