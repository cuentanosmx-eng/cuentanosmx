<?php
/**
 * CNMX Biz Recompensas - Sistema de recompensas canjeables
 */

if (!defined('ABSPATH')) exit;

class CNMX_Biz_Recompensas {
    
    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('add_meta_boxes', [$this, 'add_metaboxes']);
        add_action('save_post', [$this, 'save_metabox'], 10, 2);
    }
    
    public function register_cpt() {
        $labels = [
            'name' => __('Recompensas', 'cuentanos-business'),
            'singular_name' => __('Recompensa', 'cuentanos-business'),
            'menu_name' => __('Recompensas', 'cuentanos-business'),
            'add_new' => __('Nueva Recompensa', 'cuentanos-business'),
            'add_new_item' => __('Agregar Recompensa', 'cuentanos-business'),
            'edit_item' => __('Editar Recompensa', 'cuentanos-business'),
        ];
        
        $args = [
            'label' => __('Recompensas', 'cuentanos-business'),
            'description' => __('Sistema de recompensas canjeables con Megáfonos', 'cuentanos-business'),
            'labels' => $labels,
            'supports' => ['title', 'thumbnail', 'editor'],
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => 'cuentanos',
            'menu_position' => 35,
            'menu_icon' => 'dashicons-awards',
            'capability_type' => 'post',
            'capabilities' => ['create_posts' => 'manage_options'],
            'map_meta_cap' => true,
        ];
        
        register_post_type('cnmx_recompensa', $args);
    }
    
    public function add_metaboxes() {
        add_meta_box(
            'cnmx_recompensa_config',
            'Configuración de la Recompensa',
            [$this, 'render_metabox'],
            'cnmx_recompensa',
            'normal',
            'high'
        );
        
        add_meta_box(
            'cnmx_recompensa_canjes',
            'Historial de Canjes',
            [$this, 'render_canjes'],
            'cnmx_recompensa',
            'side',
            'low'
        );
    }
    
    public function render_metabox($post) {
        $megafonos = get_post_meta($post->ID, 'cnmx_recompensa_megafonos', true) ?: 100;
        $cantidad = get_post_meta($post->ID, 'cnmx_recompensa_cantidad', true) ?: 0;
        $codigo = get_post_meta($post->ID, 'cnmx_recompensa_codigo', true) ?: '';
        $vigencia = get_post_meta($post->ID, 'cnmx_recompensa_vigencia', true) ?: '';
        $instrucciones = get_post_meta($post->ID, 'cnmx_recompensa_instrucciones', true) ?: '';
        $activo = get_post_meta($post->ID, 'cnmx_recompensa_activa', true) ?: 'si';
        
        ?>
        <div class="cnmx-metabox-recompensa">
            <p class="description">Configura la recompensa que los usuarios pueden canjear con sus Megáfonos.</p>
            
            <table class="form-table">
                <tr>
                    <th><label>Megáfonos requeridos</label></th>
                    <td>
                        <input type="number" name="cnmx_recompensa_megafonos" value="<?php echo esc_attr($megafonos); ?>" class="small-text" min="1">
                        <span class="description">Megáfonos que debe tener el usuario</span>
                    </td>
                </tr>
                <tr>
                    <th><label>Cantidad disponible (0 = ilimitado)</label></th>
                    <td>
                        <input type="number" name="cnmx_recompensa_cantidad" value="<?php echo esc_attr($cantidad); ?>" class="small-text" min="0">
                        <span class="description">Número de veces que se puede canjear (0 = ilimitado)</span>
                    </td>
                </tr>
                <tr>
                    <th><label>Código de recompensa</label></th>
                    <td>
                        <input type="text" name="cnmx_recompensa_codigo" value="<?php echo esc_attr($codigo); ?>" class="regular-text" placeholder="EJEMPLO20">
                        <span class="description">Código que recibirá el usuario al canjear</span>
                    </td>
                </tr>
                <tr>
                    <th><label>Fecha de vigencia</label></th>
                    <td>
                        <input type="date" name="cnmx_recompensa_vigencia" value="<?php echo esc_attr($vigencia); ?>">
                        <span class="description">Fecha límite para canjear (opcional)</span>
                    </td>
                </tr>
                <tr>
                    <th><label>Instrucciones de canje</label></th>
                    <td>
                        <textarea name="cnmx_recompensa_instrucciones" rows="3" class="large-text" placeholder="Instrucciones que verá el usuario al canjear..."><?php echo esc_textarea($instrucciones); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label>Estado</label></th>
                    <td>
                        <select name="cnmx_recompensa_activa">
                            <option value="si" <?php selected($activo, 'si'); ?>>Activa</option>
                            <option value="no" <?php selected($activo, 'no'); ?>>Inactiva</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }
    
    public function render_canjes($post) {
        $canjes = get_post_meta($post->ID, 'cnmx_recompensa_canjes', true) ?: [];
        
        if (empty($canjes)) {
            echo '<p style="color: var(--cnmx-text-muted);">Aún no hay canjes</p>';
            return;
        }
        
        echo '<ul style="margin: 0; padding: 0; list-style: none;">';
        foreach (array_reverse($canjes) as $canje) {
            echo '<li style="padding: 8px 0; border-bottom: 1px solid var(--cnmx-border);">';
            echo '<strong>' . esc_html($canje['usuario']) . '</strong><br>';
            echo '<span style="font-size: 12px; color: var(--cnmx-text-muted);">';
            echo date('d/m/Y H:i', strtotime($canje['fecha'])) . '</span>';
            echo '</li>';
        }
        echo '</ul>';
    }
    
    public function save_metabox($post_id, $post) {
        if ($post->post_type !== 'cnmx_recompensa') return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        
        $campos = ['cnmx_recompensa_megafonos', 'cnmx_recompensa_cantidad', 'cnmx_recompensa_codigo', 'cnmx_recompensa_vigencia', 'cnmx_recompensa_instrucciones', 'cnmx_recompensa_activa'];
        
        foreach ($campos as $campo) {
            if (isset($_POST[$campo])) {
                update_post_meta($post_id, $campo, sanitize_text_field($_POST[$campo]));
            }
        }
    }
    
    public static function get_recompensas_activas() {
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
            $recompensas[] = [
                'id' => $post->ID,
                'titulo' => $post->post_title,
                'descripcion' => $post->post_content,
                'imagen' => get_the_post_thumbnail_url($post->ID, 'medium'),
                'megafonos' => get_post_meta($post->ID, 'cnmx_recompensa_megafonos', true),
                'cantidad' => get_post_meta($post->ID, 'cnmx_recompensa_cantidad', true),
                'codigo' => get_post_meta($post->ID, 'cnmx_recompensa_codigo', true),
                'instrucciones' => get_post_meta($post->ID, 'cnmx_recompensa_instrucciones', true),
                'vigencia' => get_post_meta($post->ID, 'cnmx_recompensa_vigencia', true),
            ];
        }
        
        usort($recompensas, function($a, $b) {
            return $a['megafonos'] - $b['megafonos'];
        });
        
        return $recompensas;
    }
    
    public static function canjear($user_id, $recompensa_id) {
        $recompensa = get_post($recompensa_id);
        
        if (!$recompensa || $recompensa->post_type !== 'cnmx_recompensa') {
            return ['success' => false, 'message' => 'Recompensa no encontrada'];
        }
        
        $activa = get_post_meta($recompensa_id, 'cnmx_recompensa_activa', true);
        if ($activa !== 'si') {
            return ['success' => false, 'message' => 'Recompensa inactiva'];
        }
        
        $megafonos_req = get_post_meta($recompensa_id, 'cnmx_recompensa_megafonos', true);
        
        global $wpdb;
        $table = $wpdb->prefix . 'cnmx_usuarios_meta';
        $usuario = $wpdb->get_row($wpdb->prepare("SELECT megafonos FROM $table WHERE user_id = %d", $user_id));
        
        $megafonos_user = $usuario ? intval($usuario->megafonos) : 0;
        
        if ($megafonos_user < $megafonos_req) {
            return ['success' => false, 'message' => 'No tienes suficientes Megáfonos'];
        }
        
        $cantidad = get_post_meta($recompensa_id, 'cnmx_recompensa_cantidad', true);
        if ($cantidad > 0) {
            $usados = get_post_meta($recompensa_id, 'cnmx_recompensa_usados', true) ?: 0;
            
            if ($usados >= $cantidad) {
                return ['success' => false, 'message' => 'Se agotó esta recompensa'];
            }
            
            update_post_meta($recompensa_id, 'cnmx_recompensa_usados', $usados + 1);
        }
        
        $canjes = get_post_meta($recompensa_id, 'cnmx_recompensa_canjes', true) ?: [];
        $user = get_userdata($user_id);
        
        $canjes[] = [
            'user_id' => $user_id,
            'usuario' => $user->display_name,
            'email' => $user->user_email,
            'fecha' => current_time('mysql'),
            'codigo' => get_post_meta($recompensa_id, 'cnmx_recompensa_codigo', true),
        ];
        
        update_post_meta($recompensa_id, 'cnmx_recompensa_canjes', $canjes);
        
        $wpdb->update($table, [
            'megafonos' => $megafonos_user - $megafonos_req
        ], ['user_id' => $user_id]);
        
        $historial = get_user_meta($user_id, 'cnmx_recompensas_canjeadas', true) ?: [];
        $historial[] = [
            'recompensa_id' => $recompensa_id,
            'recompensa_nombre' => $recompensa->post_title,
            'megafonos_gastados' => $megafonos_req,
            'codigo' => get_post_meta($recompensa_id, 'cnmx_recompensa_codigo', true),
            'instrucciones' => get_post_meta($recompensa_id, 'cnmx_recompensa_instrucciones', true),
            'fecha' => current_time('mysql'),
        ];
        update_user_meta($user_id, 'cnmx_recompensas_canjeadas', $historial);
        
        return [
            'success' => true,
            'message' => '¡Canje exitoso!',
            'codigo' => get_post_meta($recompensa_id, 'cnmx_recompensa_codigo', true),
            'instrucciones' => get_post_meta($recompensa_id, 'cnmx_recompensa_instrucciones', true),
            'megafonos_restantes' => $megafonos_user - $megafonos_req,
        ];
    }
    
    public static function get_canjeadas_usuario($user_id) {
        return get_user_meta($user_id, 'cnmx_recompensas_canjeadas', true) ?: [];
    }
}