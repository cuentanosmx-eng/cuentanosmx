<?php
/**
 * Fotos REST API
 */

if (!defined('ABSPATH')) exit;

class CNMX_Fotos_REST_API {
    
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    public function register_routes() {
        register_rest_route('cnmx/v1', '/fotos/upload', [
            'methods' => 'POST',
            'callback' => [$this, 'upload_foto'],
            'permission_callback' => [$this, 'check_permissions'],
        ]);
        
        register_rest_route('cnmx/v1', '/fotos/negocio/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_fotos_negocio'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route('cnmx/v1', '/fotos/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$this, 'delete_foto'],
            'permission_callback' => [$this, 'check_permissions'],
        ]);
    }
    
    public function check_permissions() {
        return current_user_can('upload_files');
    }
    
    public function upload_foto($request) {
        $negocio_id = intval($request->get_param('negocio_id'));
        $descripcion = sanitize_text_field($request->get_param('descripcion') ?: '');
        
        if (empty($_FILES['foto'])) {
            return new WP_Error('no_file', 'No se encontró archivo de imagen', ['status' => 400]);
        }
        
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        $file = $_FILES['foto'];
        
        if (!wp_match_mime_types('image', $file['type'])) {
            return new WP_Error('invalid_type', 'Solo se permiten imágenes', ['status' => 400]);
        }
        
        $attachment_id = media_handle_upload('foto', 0);
        
        if (is_wp_error($attachment_id)) {
            return new WP_Error('upload_failed', $attachment_id->get_error_message(), ['status' => 500]);
        }
        
        update_post_meta($attachment_id, 'negocio_id', $negocio_id);
        update_post_meta($attachment_id, 'descripcion', $descripcion);
        
        $attachment = get_post($attachment_id);
        
        return [
            'success' => true,
            'foto' => [
                'id' => $attachment_id,
                'url' => wp_get_attachment_url($attachment_id),
                'thumb' => wp_get_attachment_image_url($attachment_id, 'thumbnail'),
                'descripcion' => $descripcion,
            ],
        ];
    }
    
    public function get_fotos_negocio($request) {
        $negocio_id = intval($request->get_param('id'));
        
        $fotos = get_posts([
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_status' => 'inherit',
            'meta_query' => [
                [
                    'key' => 'negocio_id',
                    'value' => $negocio_id,
                ],
            ],
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ]);
        
        return ['fotos' => array_map(function($foto) {
            return [
                'id' => $foto->ID,
                'url' => wp_get_attachment_url($foto->ID),
                'thumb' => wp_get_attachment_image_url($foto->ID, 'medium'),
                'full' => wp_get_attachment_image_url($foto->ID, 'large'),
                'descripcion' => get_post_meta($foto->ID, 'descripcion', true),
            ];
        }, $fotos)];
    }
    
    public function delete_foto($request) {
        $foto_id = intval($request->get_param('id'));
        
        if (!current_user_can('delete_post', $foto_id)) {
            return new WP_Error('forbidden', 'No tienes permisos', ['status' => 403]);
        }
        
        $result = wp_delete_attachment($foto_id, true);
        
        if (!$result) {
            return new WP_Error('delete_failed', 'Error al eliminar', ['status' => 500]);
        }
        
        return ['success' => true, 'deleted' => $foto_id];
    }
}

new CNMX_Fotos_REST_API();
