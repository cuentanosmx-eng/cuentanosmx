<?php
/**
 * Plugin Name: Cuentanos Hero
 * Plugin URI: https://cuentanos.mx
 * Description: Plugin para gestionar el Hero con carrusel de slides. Añade un CPT para controlar slides desde el admin.
 * Version: 1.0.0
 * Author: Cuentanos Team
 * Text Domain: Cuentanos Hero
 */

if (!defined('ABSPATH')) exit;

define('CNMX_HERO_PATH', plugin_dir_path(__FILE__));
define('CNMX_HERO_URL', plugin_dir_url(__FILE__));

class CNMX_Hero {
    
    public function __construct() {
        add_action('init', array($this, 'register_cpt'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_cnmx_hero_slide', array($this, 'save_meta'));
        add_shortcode('cnmx_hero', array($this, 'shortcode_hero'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    
    public function register_cpt() {
        register_post_type('cnmx_hero_slide', array(
            'labels' => array(
                'name' => 'Hero Slides',
                'singular_name' => 'Hero Slide',
                'add_new' => 'Añadir Slide',
                'add_new_item' => 'Añadir Nuevo Slide',
                'edit_item' => 'Editar Slide',
                'new_item' => 'Nuevo Slide',
                'view_item' => 'Ver Slide',
                'search_items' => 'Buscar Slides',
                'not_found' => 'No se encontraron slides',
                'not_found_in_trash' => 'No hay slides en la papelera',
            ),
            'public' => true,
            'show_in_rest' => true,
            'supports' => array('title', 'thumbnail'),
            'menu_icon' => 'dashicons-images-alt',
            'has_archive' => false,
            'rewrite' => false,
        ));
    }
    
    public function register_taxonomies() {
        register_taxonomy('cnmx_hero_categoria', 'cnmx_hero_slide', array(
            'labels' => array(
                'name' => 'Categorías del Hero',
                'singular_name' => 'Categoría',
            ),
            'hierarchical' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ));
    }
    
    public function add_meta_boxes() {
        add_meta_box(
            'cnmx_hero_meta',
            'Configuración del Slide',
            array($this, 'render_meta_box'),
            'cnmx_hero_slide',
            'normal',
            'high'
        );
    }
    
    public function render_meta_box($post) {
        wp_nonce_field('cnmx_hero_meta', 'cnmx_hero_nonce');
        
        $subtitulo = get_post_meta($post->ID, 'cnmx_subtitulo', true);
        $boton_texto = get_post_meta($post->ID, 'cnmx_boton_texto', true);
        $boton_enlace = get_post_meta($post->ID, 'cnmx_boton_enlace', true);
        $imagen_movil = get_post_meta($post->ID, 'cnmx_imagen_movil', true);
        $creditos = get_post_meta($post->ID, 'cnmx_creditos', true);
        $orden = get_post_meta($post->ID, 'cnmx_orden', true) ?: 0;
        $activo = get_post_meta($post->ID, 'cnmx_activo', true) ?: 'si';
        ?>
        
        <style>
            .cnmx-hero-meta { max-width: 600px; }
            .cnmx-hero-meta p { margin: 16px 0; }
            .cnmx-hero-meta label { display: block; font-weight: 600; margin-bottom: 6px; }
            .cnmx-hero-meta input[type="text"],
            .cnmx-hero-meta input[type="url"],
            .cnmx-hero-meta textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
            .cnmx-hero-meta textarea { height: 60px; }
            .cnmx-hero-meta input[type="number"] { width: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
            .cnmx-hero-meta select { padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
            .cnmx-hero-meta .description { color: #666; font-size: 12px; margin-top: 4px; }
            .cnmx-hero-meta .current-image { margin-top: 10px; }
            .cnmx-hero-meta .current-image img { max-width: 200px; height: auto; border: 2px solid #EB510C; border-radius: 8px; }
        </style>
        
        <div class="cnmx-hero-meta">
            <p>
                <label for="cnmx_subtitulo">Subtítulo</label>
                <input type="text" id="cnmx_subtitulo" name="cnmx_subtitulo" 
                       value="<?php echo esc_attr($subtitulo); ?>" 
                       placeholder="Ej: Descubre los mejores lugares">
            </p>
            
            <p>
                <label for="cnmx_boton_texto">Texto del Botón</label>
                <input type="text" id="cnmx_boton_texto" name="cnmx_boton_texto" 
                       value="<?php echo esc_attr($boton_texto); ?>" 
                       placeholder="Ej: Ver Negocios">
            </p>
            
            <p>
                <label for="cnmx_boton_enlace">Enlace del Botón</label>
                <input type="url" id="cnmx_boton_enlace" name="cnmx_boton_enlace" 
                       value="<?php echo esc_attr($boton_enlace); ?>" 
                       placeholder="https://...">
            </p>
            
            <p>
                <label>Imagen Destacada (1920x1080px recomendado)</label>
                <?php if (has_post_thumbnail($post->ID)): ?>
                    <div class="current-image">
                        <img src="<?php echo get_the_post_thumbnail_url($post->ID, 'medium'); ?>" alt="">
                        <p class="description">Imagen actual. Cambia la imagen destacada para actualizarla.</p>
                    </div>
                <?php else: ?>
                    <p class="description">Añade una imagen destacada desde el panel derecho.</p>
                <?php endif; ?>
            </p>
            
            <p>
                <label for="cnmx_imagen_movil">URL Imagen Móvil (opcional)</label>
                <input type="url" id="cnmx_imagen_movil" name="cnmx_imagen_movil" 
                       value="<?php echo esc_attr($imagen_movil); ?>" 
                       placeholder="URL de imagen optimizada para móviles">
                <span class="description">Si está vacío, se usará la imagen destacada.</span>
            </p>
            
            <p>
                <label for="cnmx_creditos">Créditos de la Imagen</label>
                <input type="text" id="cnmx_creditos" name="cnmx_creditos" 
                       value="<?php echo esc_attr($creditos); ?>" 
                       placeholder="Ej: Foto por Unsplash">
            </p>
            
            <p>
                <label for="cnmx_orden">Orden de aparición</label>
                <input type="number" id="cnmx_orden" name="cnmx_orden" 
                       value="<?php echo esc_attr($orden); ?>" min="0">
                <span class="description">Menor número = aparece primero.</span>
            </p>
            
            <p>
                <label for="cnmx_activo">¿Activo?</label>
                <select id="cnmx_activo" name="cnmx_activo">
                    <option value="si" <?php selected($activo, 'si'); ?>>Sí - Mostrar</option>
                    <option value="no" <?php selected($activo, 'no'); ?>>No - Ocultar</option>
                </select>
            </p>
        </div>
        <?php
    }
    
    public function save_meta($post_id) {
        if (!isset($_POST['cnmx_hero_nonce']) || !wp_verify_nonce($_POST['cnmx_hero_nonce'], 'cnmx_hero_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        $fields = array('cnmx_subtitulo', 'cnmx_boton_texto', 'cnmx_boton_enlace', 'cnmx_imagen_movil', 'cnmx_creditos', 'cnmx_orden', 'cnmx_activo');
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    public function enqueue_assets() {
        wp_register_style('cnmx-hero-css', CNMX_HERO_URL . 'assets/css/hero.css', array(), '1.0.0');
        wp_register_script('cnmx-hero-js', CNMX_HERO_URL . 'assets/js/hero.js', array('jquery'), '1.0.0', true);
    }
    
    public function shortcode_hero($atts) {
        $atts = shortcode_atts(array(
            'categoria' => '',
            'numslides' => 5,
        ), $atts);
        
        $args = array(
            'post_type' => 'cnmx_hero_slide',
            'post_status' => 'publish',
            'posts_per_page' => intval($atts['numslides']),
            'meta_key' => 'cnmx_orden',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
        );
        
        if (!empty($atts['categoria'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'cnmx_hero_categoria',
                    'field' => 'slug',
                    'terms' => $atts['categoria'],
                )
            );
        }
        
        $slides = get_posts($args);
        
        if (empty($slides)) {
            return '';
        }
        
        wp_enqueue_style('cnmx-hero-css');
        wp_enqueue_script('cnmx-hero-js');
        
        $html = '<section class="cnmx-hero-carousel">';
        $html .= '<div class="cnmx-hero-slides">';
        
        $first = true;
        foreach ($slides as $slide) {
            $imagen_id = get_post_thumbnail_id($slide->ID);
            $imagen_movil = get_post_meta($slide->ID, 'cnmx_imagen_movil', true);
            $subtitulo = get_post_meta($slide->ID, 'cnmx_subtitulo', true);
            $boton_texto = get_post_meta($slide->ID, 'cnmx_boton_texto', true);
            $boton_enlace = get_post_meta($slide->ID, 'cnmx_boton_enlace', true);
            $creditos = get_post_meta($slide->ID, 'cnmx_creditos', true);
            
            $imagen_full = $imagen_id ? wp_get_attachment_image_src($imagen_id, 'full')[0] : '';
            $imagen_url = !empty($imagen_movil) ? $imagen_movil : $imagen_full;
            
            $clase_activa = $first ? 'active' : '';
            $first = false;
            
            $html .= '<div class="cnmx-hero-slide ' . $clase_activa . '" data-slide="' . $slide->ID . '">';
            $html .= '<div class="cnmx-hero-bg" style="background-image: url(\'' . esc_url($imagen_url) . '\');"></div>';
            $html .= '<div class="cnmx-hero-overlay"></div>';
            $html .= '<div class="cnmx-hero-content">';
            $html .= '<h1 class="cnmx-hero-title">' . esc_html($slide->post_title) . '</h1>';
            if ($subtitulo) {
                $html .= '<p class="cnmx-hero-subtitle">' . esc_html($subtitulo) . '</p>';
            }
            if ($boton_texto && $boton_enlace) {
                $html .= '<a href="' . esc_url($boton_enlace) . '" class="cnmx-hero-btn">' . esc_html($boton_texto) . '</a>';
            }
            $html .= '</div>';
            if ($creditos) {
                $html .= '<div class="cnmx-hero-creditos">' . esc_html($creditos) . '</div>';
            }
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        // Navigation bullets
        $html .= '<div class="cnmx-hero-nav">';
        for ($i = 0; $i < count($slides); $i++) {
            $clase_activa = $i === 0 ? 'active' : '';
            $html .= '<button class="cnmx-hero-dot ' . $clase_activa . '" data-slide="' . $i . '"></button>';
        }
        $html .= '</div>';
        
        // Arrows
        $html .= '<button class="cnmx-hero-arrow cnmx-hero-prev">❮</button>';
        $html .= '<button class="cnmx-hero-arrow cnmx-hero-next">❯</button>';
        
        $html .= '</section>';
        
        return $html;
    }
}

new CNMX_Hero();
