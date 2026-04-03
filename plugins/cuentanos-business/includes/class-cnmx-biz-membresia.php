<?php
/**
 * CNMX Biz Membresía - Sistema de membresías para empresas
 */

if (!defined('ABSPATH')) exit;

class CNMX_Biz_Membresia {
    
    private $planes = [
        'gratis' => [
            'nombre' => 'Plan Gratis',
            'precio' => 0,
            'duracion' => 0,
            'caracteristicas' => [
                'Perfil básico',
                ' hasta 3 fotos',
                'Información de contacto',
                'Reseñas de usuarios',
            ],
        ],
        'basico' => [
            'nombre' => 'Plan Básico',
            'precio' => 299,
            'duracion' => 30,
            'caracteristicas' => [
                'Todo del plan gratis',
                '10 fotos',
                'Horario completo',
                'Galería de imágenes',
                'Botón de WhatsApp',
            ],
        ],
        'premium' => [
            'nombre' => 'Plan Premium',
            'precio' => 599,
            'duracion' => 30,
            'caracteristicas' => [
                'Todo del plan básico',
                'Fotos ilimitadas',
                'Video promotional',
                'Posición destacada',
                'Estadísticas avanzadas',
                'Cupón de descuento',
            ],
        ],
        'enterprise' => [
            'nombre' => 'Plan Enterprise',
            'precio' => 1499,
            'duracion' => 30,
            'caracteristicas' => [
                'Todo del plan premium',
                'Banner publicitario',
                'API access',
                'Soporte prioritario',
                'Reportes mensuales',
                'Gestión de campañas',
            ],
        ],
    ];
    
    public function get_planes() {
        return $this->planes;
    }
    
    public function get_plan_actual($negocio_id) {
        $plan = get_post_meta($negocio_id, 'cnmx_membresia_plan', true);
        $renovacion = get_post_meta($negocio_id, 'cnmx_membresia_renovacion', true);
        
        if (!$plan) {
            return $this->planes['gratis'];
        }
        
        $dias_restantes = 0;
        if ($renovacion) {
            $dias_restantes = ceil((strtotime($renovacion) - time()) / 86400);
        }
        
        $plan_data = isset($this->planes[$plan]) ? $this->planes[$plan] : $this->planes['gratis'];
        $plan_data['dias_restantes'] = max(0, $dias_restantes);
        $plan_data['renovacion'] = $renovacion;
        
        return $plan_data;
    }
    
    public function activar_plan($negocio_id, $plan, $payment_id = null) {
        $plan_data = $this->planes[$plan];
        
        $inicio = current_time('mysql');
        $renovacion = date('Y-m-d H:i:s', strtotime('+' . $plan_data['duracion'] . ' days'));
        
        update_post_meta($negocio_id, 'cnmx_membresia_plan', $plan);
        update_post_meta($negocio_id, 'cnmx_membresia_inicio', $inicio);
        update_post_meta($negocio_id, 'cnmx_membresia_renovacion', $renovacion);
        update_post_meta($negocio_id, 'cnmx_membresia_payment_id', $payment_id);
        
        update_post_meta($negocio_id, 'es_premium', in_array($plan, ['premium', 'enterprise']) ? 1 : 0);
        update_post_meta($negocio_id, 'es_destacado', $plan === 'enterprise' ? 1 : 0);
        
        return true;
    }
    
    public function verificar_renovacion() {
        global $wpdb;
        
        $renovaciones = $wpdb->get_results(
            "SELECT p.ID, p.post_title, pm.meta_value as renovacion 
             FROM {$wpdb->posts} p 
             JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = 'cnmx_membresia_renovacion'
             WHERE p.post_type = 'negocio' AND p.post_status = 'publish'
             AND pm.meta_value < NOW()"
        );
        
        foreach ($renovaciones as $negocio) {
            $this->procesar_renovacion($negocio->ID);
        }
    }
    
    private function procesar_renovacion($negocio_id) {
        $plan = get_post_meta($negocio_id, 'cnmx_membresia_plan', true);
        
        if ($plan === 'gratis') {
            return;
        }
        
        $this->activar_plan($negocio_id, $plan);
        
        $user_id = get_post_meta($negocio_id, 'cnmx_propietario_id', true);
        if ($user_id) {
            wp_mail(
                get_userdata($user_id)->user_email,
                'Tu membresía ha sido renovada',
                'Tu membresía en cuentanos.mx ha sido renovada automáticamente.'
            );
        }
    }
    
    public function downgradear_gratis($negocio_id) {
        delete_post_meta($negocio_id, 'cnmx_membresia_plan');
        delete_post_meta($negocio_id, 'cnmx_membresia_inicio');
        delete_post_meta($negocio_id, 'cnmx_membresia_renovacion');
        delete_post_meta($negocio_id, 'es_premium');
        delete_post_meta($negocio_id, 'es_destacado');
    }
}

new CNMX_Biz_Membresia();