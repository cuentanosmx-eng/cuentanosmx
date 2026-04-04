<?php
/**
 * Plugin Name: Cuentanos Anuncios
 * Plugin URI: https://cuentanos.mx
 * Description: Plugin para gestionar anuncios de negocios destacados en un slider smart.
 * Version: 1.0.0
 * Author: Cuentanos Team
 */

if (!defined('ABSPATH')) exit;

define('CNMX_ANUNCIOS_PATH', plugin_dir_path(__FILE__));
define('CNMX_ANUNCIOS_URL', plugin_dir_url(__FILE__));

class CNMX_Anuncios {
    
    public function __construct() {
        add_action('init', array($this, 'register_cpt'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_cnmx_anuncio', array($this, 'save_meta'));
        add_shortcode('cnmx_anuncios', array($this, 'shortcode_anuncios'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    
    public function register_cpt() {
        register_post_type('cnmx_anuncio', array(
            'labels' => array(
                'name' => 'Anuncios',
                'singular_name' => 'Anuncio',
                'add_new' => 'Añadir Anuncio',
                'add_new_item' => 'Añadir Nuevo Anuncio',
                'edit_item' => 'Editar Anuncio',
                'new_item' => 'Nuevo Anuncio',
                'view_item' => 'Ver Anuncio',
                'search_items' => 'Buscar Anuncios',
                'not_found' => 'No se encontraron anuncios',
                'not_found_in_trash' => 'No hay anuncios en la papelera',
            ),
            'public' => true,
            'show_in_rest' => true,
            'supports' => array('title', 'thumbnail'),
            'menu_icon' => 'dashicons-megaphone',
            'has_archive' => false,
            'rewrite' => false,
        ));
    }
    
    public function add_meta_boxes() {
        add_meta_box(
            'cnmx_anuncio_meta',
            'Configuración del Anuncio',
            array($this, 'render_meta_box'),
            'cnmx_anuncio',
            'normal',
            'high'
        );
    }
    
    public function render_meta_box($post) {
        wp_nonce_field('cnmx_anuncio_meta', 'cnmx_anuncio_nonce');
        
        $enlace = get_post_meta($post->ID, 'cnmx_enlace', true);
        $orden = get_post_meta($post->ID, 'cnmx_orden', true) ?: 0;
        $activo = get_post_meta($post->ID, 'cnmx_activo', true) ?: 'si';
        $mostrar_nombre = get_post_meta($post->ID, 'cnmx_mostrar_nombre', true) ?: 'no';
        ?>
        
        <style>
            .cnmx-anuncio-meta { max-width: 600px; }
            .cnmx-anuncio-meta p { margin: 16px 0; }
            .cnmx-anuncio-meta label { display: block; font-weight: 600; margin-bottom: 6px; }
            .cnmx-anuncio-meta input[type="text"],
            .cnmx-anuncio-meta input[type="url"],
            .cnmx-anuncio-meta input[type="number"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
            .cnmx-anuncio-meta select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
            .cnmx-anuncio-meta .description { color: #666; font-size: 12px; margin-top: 4px; }
            .cnmx-anuncio-meta .current-image { margin-top: 10px; }
            .cnmx-anuncio-meta .current-image img { max-width: 400px; max-height: 100px; width: auto; height: auto; border: 2px solid #EB510C; border-radius: 8px; object-fit: contain; background: #f0f0f0; }
            .cnmx-anuncio-meta .info-box { background: #f0f9ff; border: 1px solid #b3e0ff; border-radius: 8px; padding: 16px; margin: 16px 0; }
            .cnmx-anuncio-meta .info-box h4 { margin: 0 0 8px 0; color: #0066cc; }
            .cnmx-anuncio-meta .info-box p { margin: 0; font-size: 13px; color: #333; }
        </style>
        
        <div class="cnmx-anuncio-meta">
            <div class="info-box">
                <h4>📐 Tamaño recomendado</h4>
                <p>2048 x 378 píxeles (relación 5.4:1). La imagen debe ser horizontal tipo banner.</p>
            </div>
            
            <p>
                <label for="cnmx_enlace">Enlace del Anuncio</label>
                <input type="url" id="cnmx_enlace" name="cnmx_enlace" 
                       value="<?php echo esc_attr($enlace); ?>" 
                       placeholder="https://cuentanos.mx/negocio/...">
                <span class="description">URL a la que llevará el clic en el banner.</span>
            </p>
            
            <p>
                <label for="cnmx_orden">Orden de aparición</label>
                <input type="number" id="cnmx_orden" name="cnmx_orden" 
                       value="<?php echo esc_attr($orden); ?>" min="0">
                <span class="description">Menor número = aparece primero. Use 0, 1, 2, etc.</span>
            </p>
            
            <p>
                <label for="cnmx_mostrar_nombre">¿Mostrar nombre del negocio?</label>
                <select id="cnmx_mostrar_nombre" name="cnmx_mostrar_nombre">
                    <option value="no" <?php selected($mostrar_nombre, 'no'); ?>>No</option>
                    <option value="si" <?php selected($mostrar_nombre, 'si'); ?>>Sí</option>
                </select>
                <span class="description">Muestra el título del anuncio sobre la imagen.</span>
            </p>
            
            <p>
                <label for="cnmx_activo">¿Activo?</label>
                <select id="cnmx_activo" name="cnmx_activo">
                    <option value="si" <?php selected($activo, 'si'); ?>>Sí - Mostrar</option>
                    <option value="no" <?php selected($activo, 'no'); ?>>No - Ocultar</option>
                </select>
            </p>
            
            <p>
                <label>Imagen del Anuncio</label>
                <?php if (has_post_thumbnail($post->ID)): ?>
                    <div class="current-image">
                        <?php echo get_the_post_thumbnail($post->ID, 'large', array('style' => 'max-width:400px;max-height:100px;width:auto;height:auto;border:2px solid #EB510C;border-radius:8px;object-fit:contain;background:#f0f0f0;')); ?>
                        <p class="description">Imagen actual. Cambia la imagen destacada para actualizarla.</p>
                    </div>
                <?php else: ?>
                    <p class="description">Añade una imagen destacada (2048x378px) desde el panel derecho.</p>
                <?php endif; ?>
            </p>
        </div>
        <?php
    }
    
    public function save_meta($post_id) {
        if (!isset($_POST['cnmx_anuncio_nonce']) || !wp_verify_nonce($_POST['cnmx_anuncio_nonce'], 'cnmx_anuncio_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        $fields = array('cnmx_enlace', 'cnmx_orden', 'cnmx_activo', 'cnmx_mostrar_nombre');
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    public function enqueue_assets() {
        wp_register_style('cnmx-anuncios-css', CNMX_ANUNCIOS_URL . 'assets/css/anuncios.css', array(), '1.0.0');
        wp_register_script('cnmx-anuncios-js', CNMX_ANUNCIOS_URL . 'assets/js/anuncios.js', array('jquery'), '1.0.0', true);
    }
    
    public function shortcode_anuncios($atts) {
        $atts = shortcode_atts(array(
            'num' => 10,
        ), $atts);
        
        $args = array(
            'post_type' => 'cnmx_anuncio',
            'post_status' => 'publish',
            'posts_per_page' => intval($atts['num']),
            'meta_query' => array(
                array(
                    'key' => 'cnmx_activo',
                    'value' => 'si',
                )
            ),
            'meta_key' => 'cnmx_orden',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
        );
        
        $anuncios = get_posts($args);
        
        if (empty($anuncios)) {
            return '';
        }
        
        wp_enqueue_style('cnmx-anuncios-css');
        wp_enqueue_script('cnmx-anuncios-js');
        wp_localize_script('cnmx-anuncios-js', 'cnmxAnuncios', array(
            'count' => count($anuncios),
            'autoplay' => true,
            'interval' => 5000,
        ));
        
        $html = '<section class="cnmx-anuncios-slider">';
        $html .= '<div class="cnmx-anuncios-track">';
        
        $indicators = '';
        $first = true;
        $index = 0;
        
        foreach ($anuncios as $anuncio) {
            $imagen_id = get_post_thumbnail_id($anuncio->ID);
            $imagen_url = $imagen_id ? wp_get_attachment_image_src($imagen_id, 'full')[0] : '';
            $enlace = get_post_meta($anuncio->ID, 'cnmx_enlace', true);
            $mostrar_nombre = get_post_meta($anuncio->ID, 'cnmx_mostrar_nombre', true) === 'si';
            $titulo = get_the_title($anuncio->ID);
            
            $clase_activa = $first ? 'active' : '';
            $first = false;
            
            $html .= '<div class="cnmx-anuncio-slide ' . $clase_activa . '" data-index="' . $index . '">';
            
            if ($enlace) {
                $html .= '<a href="' . esc_url($enlace) . '" class="cnmx-anuncio-link" target="_blank" rel="noopener">';
            }
            
            $html .= '<img src="' . esc_url($imagen_url) . '" alt="' . esc_attr($titulo) . '" class="cnmx-anuncio-img">';
            
            if ($mostrar_nombre) {
                $html .= '<div class="cnmx-anuncio-overlay">';
                $html .= '<span class="cnmx-anuncio-nombre">' . esc_html($titulo) . '</span>';
                $html .= '</div>';
            }
            
            if ($enlace) {
                $html .= '</a>';
            }
            
            $html .= '</div>';
            
            $indicators .= '<button class="cnmx-anuncio-dot ' . $clase_activa . '" data-index="' . $index . '" aria-label="Ir a anuncio ' . ($index + 1) . '"></button>';
            
            $index++;
        }
        
        $html .= '</div>';
        
        if (count($anuncios) > 1) {
            $html .= '<div class="cnmx-anuncios-nav">';
            $html .= '<button class="cnmx-anuncio-arrow prev" aria-label="Anuncio anterior"></button>';
            $html .= '<div class="cnmx-anuncios-dots">' . $indicators . '</div>';
            $html .= '<button class="cnmx-anuncio-arrow next" aria-label="Siguiente anuncio"></button>';
            $html .= '</div>';
        }
        
        $html .= '</section>';
        
        return $html;
    }
}

new CNMX_Anuncios();
