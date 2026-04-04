<?php
/**
 * CNMX Biz Logros - Sistema de logros/achievements
 */

if (!defined('ABSPATH')) exit;

class CNMX_Biz_Logros {
    
    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('add_meta_boxes', [$this, 'add_metaboxes']);
        add_action('save_post', [$this, 'save_metabox'], 10, 2);
    }
    
    public function register_cpt() {
        $labels = [
            'name' => __('Logros', 'cuentanos-business'),
            'singular_name' => __('Logro', 'cuentanos-business'),
            'menu_name' => __('Logros', 'cuentanos-business'),
            'add_new' => __('Nuevo Logro', 'cuentanos-business'),
            'add_new_item' => __('Agregar Logro', 'cuentanos-business'),
            'edit_item' => __('Editar Logro', 'cuentanos-business'),
        ];
        
        $args = [
            'label' => __('Logros', 'cuentanos-business'),
            'description' => __('Sistema de logros y achievements', 'cuentanos-business'),
            'labels' => $labels,
            'supports' => ['title', 'thumbnail'],
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => 'cuentanos',
            'menu_position' => 34,
            'menu_icon' => 'dashicons-trophy',
            'capability_type' => 'post',
            'capabilities' => ['create_posts' => 'manage_options'],
            'map_meta_cap' => true,
        ];
        
        register_post_type('cnmx_logro', $args);
    }
    
    public function add_metaboxes() {
        add_meta_box(
            'cnmx_logro_config',
            'Configuración del Logro',
            [$this, 'render_metabox'],
            'cnmx_logro',
            'normal',
            'high'
        );
    }
    
    public function render_metabox($post) {
        $megafonos = get_post_meta($post->ID, 'cnmx_logro_megafonos', true) ?: 10;
        $icono = get_post_meta($post->ID, 'cnmx_logro_icono', true) ?: '🏆';
        $descripcion = get_post_meta($post->ID, 'cnmx_logro_descripcion', true) ?: '';
        $limite_diario = get_post_meta($post->ID, 'cnmx_logro_limite_diario', true) ?: 0;
        $tipo_accion = get_post_meta($post->ID, 'cnmx_logro_tipo', true) ?: 'custom';
        $activo = get_post_meta($post->ID, 'cnmx_logro_activo', true) ?: 'si';
        
        ?>
        <div class="cnmx-metabox-logro">
            <p class="description">Configura los parámetros del logro. Los usuarios ganarán Megáfonos al completar este logro.</p>
            
            <table class="form-table">
                <tr>
                    <th><label>Icono (Emoji)</label></th>
                    <td>
                        <input type="text" name="cnmx_logro_icono" value="<?php echo esc_attr($icono); ?>" class="regular-text" style="width: 80px; font-size: 24px; text-align: center;">
                        <span class="description">Usa un emoji como icono</span>
                    </td>
                </tr>
                <tr>
                    <th><label>Megáfonos a otorgar</label></th>
                    <td>
                        <input type="number" name="cnmx_logro_megafonos" value="<?php echo esc_attr($megafonos); ?>" class="small-text" min="1">
                        <span class="description">Cantidad de Megáfonos que ganará el usuario</span>
                    </td>
                </tr>
                <tr>
                    <th><label>Tipo de Acción</label></th>
                    <td>
                        <select name="cnmx_logro_tipo">
                            <option value="custom" <?php selected($tipo_accion, 'custom'); ?>>Personalizado</option>
                            <option value="resena" <?php selected($tipo_accion, 'resena'); ?>>Nueva Reseña</option>
                            <option value="favorito" <?php selected($tipo_accion, 'favorito'); ?>>Guardar Favorito</option>
                            <option value="compartir" <?php selected($tipo_accion, 'compartir'); ?>>Compartir Negocio</option>
                            <option value="visita" <?php selected($tipo_accion, 'visita'); ?>>Visitar Negocio</option>
                            <option value="registro" <?php selected($tipo_accion, 'registro'); ?>>Registro de Cuenta</option>
                            <option value="perfil" <?php selected($tipo_accion, 'perfil'); ?>>Completar Perfil</option>
                            <option value="primera_resena" <?php selected($tipo_accion, 'primera_resena'); ?>>Primera Reseña</option>
                            <option value="10_resenas" <?php selected($tipo_accion, '10_resenas'); ?>>10 Reseñas</option>
                            <option value="50_favoritos" <?php selected($tipo_accion, '50_favoritos'); ?>>50 Favoritos</option>
                        </select>
                        <span class="description">Tipo de acción que-triggerá este logro</span>
                    </td>
                </tr>
                <tr>
                    <th><label>Límite diario (0 = sin límite)</label></th>
                    <td>
                        <input type="number" name="cnmx_logro_limite_diario" value="<?php echo esc_attr($limite_diario); ?>" class="small-text" min="0">
                        <span class="description">Veces que se puede obtener al día (0 = infinito)</span>
                    </td>
                </tr>
                <tr>
                    <th><label>Descripción</label></th>
                    <td>
                        <textarea name="cnmx_logro_descripcion" rows="3" class="large-text"><?php echo esc_textarea($descripcion); ?></textarea>
                        <span class="description">Descripción visible para el usuario</span>
                    </td>
                </tr>
                <tr>
                    <th><label>Estado</label></th>
                    <td>
                        <select name="cnmx_logro_activo">
                            <option value="si" <?php selected($activo, 'si'); ?>>Activo</option>
                            <option value="no" <?php selected($activo, 'no'); ?>>Inactivo</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }
    
    public function save_metabox($post_id, $post) {
        if ($post->post_type !== 'cnmx_logro') return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        
        $campos = ['cnmx_logro_icono', 'cnmx_logro_megafonos', 'cnmx_logro_descripcion', 'cnmx_logro_limite_diario', 'cnmx_logro_tipo', 'cnmx_logro_activo'];
        
        foreach ($campos as $campo) {
            if (isset($_POST[$campo])) {
                update_post_meta($post_id, $campo, sanitize_text_field($_POST[$campo]));
            }
        }
    }
    
    public static function get_logros_activos() {
        $args = [
            'post_type' => 'cnmx_logro',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'cnmx_logro_activo',
                    'value' => 'si',
                ]
            ]
        ];
        
        $query = new WP_Query($args);
        $logros = [];
        
        foreach ($query->posts as $post) {
            $logros[] = [
                'id' => $post->ID,
                'titulo' => $post->post_title,
                'icono' => get_post_meta($post->ID, 'cnmx_logro_icono', true),
                'megafonos' => get_post_meta($post->ID, 'cnmx_logro_megafonos', true),
                'descripcion' => get_post_meta($post->ID, 'cnmx_logro_descripcion', true),
                'tipo' => get_post_meta($post->ID, 'cnmx_logro_tipo', true),
                'limite_diario' => get_post_meta($post->ID, 'cnmx_logro_limite_diario', true),
            ];
        }
        
        return $logros;
    }
    
    public static function verificar_logro($user_id, $tipo_accion) {
        $logros = self::get_logros_activos();
        
        foreach ($logros as $logro) {
            if ($logro['tipo'] === $tipo_accion || $logro['tipo'] === 'custom') {
                $ya_obtuvo = get_user_meta($user_id, 'cnmx_logro_' . $logro['id'], true);
                
                if (!$ya_obtuvo) {
                    $limite = intval($logro['limite_diario']);
                    
                    if ($limite === 0 || self::contar_hoy($user_id, $logro['id']) < $limite) {
                        self::otorgar_logro($user_id, $logro);
                    }
                }
            }
        }
    }
    
    private static function contar_hoy($user_id, $logro_id) {
        $historial = get_user_meta($user_id, 'cnmx_logros_historial', true);
        $historial = $historial ?: [];
        
        $hoy = date('Y-m-d');
        $count = 0;
        
        foreach ($historial as $h) {
            if ($h['logro_id'] == $logro_id && $h['fecha'] === $hoy) {
                $count++;
            }
        }
        
        return $count;
    }
    
    private static function otorgar_logro($user_id, $logro) {
        update_user_meta($user_id, 'cnmx_logro_' . $logro['id'], current_time('mysql'));
        
        $historial = get_user_meta($user_id, 'cnmx_logros_historial', true) ?: [];
        $historial[] = [
            'logro_id' => $logro['id'],
            'logro_nombre' => $logro['titulo'],
            'megafonos' => $logro['megafonos'],
            'fecha' => date('Y-m-d'),
        ];
        update_user_meta($user_id, 'cnmx_logros_historial', $historial);
        
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'cnmx_historial', [
            'user_id' => $user_id,
            'tipo' => 'ganar',
            'puntos' => $logro['megafonos'],
            'descripcion' => 'Obtuviste el logro: ' . $logro['titulo'],
        ]);
        
        $meta = $wpdb->get_row($wpdb->prepare(
            "SELECT megafonos FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d",
            $user_id
        ));
        $nuevos = $meta ? $meta->megafonos + $logro['megafonos'] : $logro['megafonos'];
        $wpdb->update(
            $wpdb->prefix . 'cnmx_usuarios_meta',
            ['megafonos' => $nuevos],
            ['user_id' => $user_id]
        );
    }
}