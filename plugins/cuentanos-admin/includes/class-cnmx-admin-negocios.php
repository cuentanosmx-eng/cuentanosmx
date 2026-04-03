<?php
/**
 * Admin Negocios Class
 */

if (!defined('ABSPATH')) exit;

class CNMX_Admin_Negocios {
    
    public function __construct() {
        add_action('wp_ajax_cnmx_approve_negocio', [$this, 'ajax_approve_negocio']);
        add_action('wp_ajax_cnmx_reject_negocio', [$this, 'ajax_reject_negocio']);
        add_action('wp_ajax_cnmx_delete_negocio', [$this, 'ajax_delete_negocio']);
        add_action('wp_ajax_cnmx_feature_negocio', [$this, 'ajax_feature_negocio']);
    }
    
    public function ajax_approve_negocio() {
        check_ajax_referer('cnmx_admin_nonce', 'nonce');
        
        $negocio_id = intval($_POST['negocio_id']);
        
        wp_update_post([
            'ID' => $negocio_id,
            'post_status' => 'publish',
        ]);
        
        wp_set_object_terms($negocio_id, 'publicado', 'etiqueta', true);
        
        $propietario_id = get_post_meta($negocio_id, 'cnmx_propietario_id', true);
        $email_usuario = get_userdata($propietario_id)->user_email;
        $nombre_negocio = get_the_title($negocio_id);
        
        $to = $email_usuario;
        $subject = '¡Tu negocio ha sido aprobado! - Cuentanos.mx';
        $message = "¡Buenas noticias!\n\n";
        $message .= "Tu negocio \"$nombre_negocio\" ha sido aprobado y ya está publicado en Cuentanos.mx\n\n";
        $message .= "Ahora puedes:\n";
        $message .= "- Completar la información de tu negocio\n";
        $message .= "- Agregar fotos\n";
        $message .= "- Ver tus métricas\n";
        $message .= "- Canjear recompensas con tus Megáfonos\n\n";
        $message .= "Inicia sesión en: " . home_url('/mi-negocio') . "\n\n";
        $message .= "¡Bienvenido a Cuentanos.mx!";
        
        wp_mail($to, $subject, $message);
        
        wp_send_json(['success' => true, 'message' => 'Negocio aprobado y usuario notificado']);
    }
    
    public function ajax_reject_negocio() {
        check_ajax_referer('cnmx_admin_nonce', 'nonce');
        
        $negocio_id = intval($_POST['negocio_id']);
        
        wp_update_post([
            'ID' => $negocio_id,
            'post_status' => 'draft',
        ]);
        
        wp_send_json(['success' => true, 'message' => 'Negocio rechazado']);
    }
    
    public function ajax_delete_negocio() {
        check_ajax_referer('cnmx_admin_nonce', 'nonce');
        
        $negocio_id = intval($_POST['negocio_id']);
        
        wp_delete_post($negocio_id, true);
        
        wp_send_json(['success' => true, 'message' => 'Negocio eliminado']);
    }
    
    public function ajax_feature_negocio() {
        check_ajax_referer('cnmx_admin_nonce', 'nonce');
        
        $negocio_id = intval($_POST['negocio_id']);
        $featured = $_POST['featured'] === 'true';
        
        if ($featured) {
            wp_set_object_terms($negocio_id, 'destacado', 'etiqueta', true);
        } else {
            wp_remove_object_terms($negocio_id, 'destacado', 'etiqueta');
        }
        
        wp_send_json(['success' => true]);
    }
}
