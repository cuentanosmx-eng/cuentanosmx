<?php
/**
 * Plugin Name: Cuentanos Categorías
 * Plugin URI: https://cuentanos.mx
 * Description: Plugin para gestionar las categorías del directorio de negocios.
 * Version: 1.0.0
 * Author: Cuentanos Team
 */

if (!defined('ABSPATH')) exit;

define('CNMX_CAT_PATH', plugin_dir_path(__FILE__));
define('CNMX_CAT_URL', plugin_dir_url(__FILE__));

class CNMX_Categorias {
    
    public function __construct() {
        add_action('init', array($this, 'register_taxonomy'));
        add_action('categoria_add_form_fields', array($this, 'add_icon_field'));
        add_action('categoria_edit_form_fields', array($this, 'edit_icon_field'));
        add_action('created_categoria', array($this, 'save_icon'));
        add_action('edited_categoria', array($this, 'save_icon'));
        add_shortcode('cnmx_categorias', array($this, 'shortcode_categorias'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
    }
    
    public function register_taxonomy() {
        register_taxonomy('categoria', 'negocio', array(
            'labels' => array(
                'name' => 'Categorías',
                'singular_name' => 'Categoría',
                'add_new_item' => 'Añadir nueva categoría',
                'edit_item' => 'Editar categoría',
                'update_item' => 'Actualizar categoría',
                'search_items' => 'Buscar categorías',
            ),
            'hierarchical' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'show_ui' => true,
        ));
    }
    
    public function add_icon_field() {
        $iconos = $this->get_iconos_predefinidos();
        ?>
        <div class="form-field term-icono-wrap">
            <label for="cnmx_icono">Icono SVG</label>
            <select name="cnmx_icono" id="cnmx_icono" class="cnmx-icono-select">
                <option value="">-- Seleccionar icono --</option>
                <?php foreach ($iconos as $key => $icon): ?>
                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($icon['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description">Selecciona un icono para esta categoría.</p>
            <div id="icono-preview" style="margin-top: 10px;"></div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('#cnmx_icono').on('change', function() {
                var val = $(this).val();
                var preview = $('#icono-preview');
                if (val && window.cnmxIconos && window.cnmxIconos[val]) {
                    preview.html('<div style="width:50px;height:50px;background:#f5f5f5;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#EB510C;">' + window.cnmxIconos[val] + '</div>');
                } else {
                    preview.html('');
                }
            });
        });
        </script>
        <?php
    }
    
    public function edit_icon_field($term) {
        $iconos = $this->get_iconos_predefinidos();
        $icono_actual = get_term_meta($term->term_id, 'cnmx_icono', true);
        ?>
        <tr class="form-field term-icono-wrap">
            <th scope="row"><label for="cnmx_icono">Icono SVG</label></th>
            <td>
                <select name="cnmx_icono" id="cnmx_icono" class="cnmx-icono-select">
                    <option value="">-- Seleccionar icono --</option>
                    <?php foreach ($iconos as $key => $icon): ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($icono_actual, $key); ?>><?php echo esc_html($icon['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description">Selecciona un icono para esta categoría.</p>
            </td>
        </tr>
        <?php
    }
    
    public function save_icon($term_id) {
        if (isset($_POST['cnmx_icono'])) {
            update_term_meta($term_id, 'cnmx_icono', sanitize_text_field($_POST['cnmx_icono']));
        }
    }
    
    public function get_iconos_predefinidos() {
        return array(
            'restaurantes' => array(
                'nombre' => '🍽️ Restaurantes',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/></svg>',
            ),
            'hoteles' => array(
                'nombre' => '🏨 Hoteles',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/></svg>',
            ),
            'cafes' => array(
                'nombre' => '☕ Cafés',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>',
            ),
            'tiendas' => array(
                'nombre' => '🛒 Tiendas',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/><path d="M12 3v6"/></svg>',
            ),
            'bares' => array(
                'nombre' => '🍸 Bares',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 22h8"/><path d="M12 11v11"/><path d="m19 3-7 8-7-8Z"/></svg>',
            ),
            'gimnasios' => array(
                'nombre' => '💪 Gimnasios',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6.5 6.5h11"/><path d="M6.5 17.5h11"/><path d="M2 12h4"/><path d="M18 12h4"/><path d="M6.5 6.5v11"/><path d="M17.5 6.5v11"/></svg>',
            ),
            'spa' => array(
                'nombre' => '🧖 Spa',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22c-4-3-8-6-8-11a8 8 0 0 1 16 0c0 5-4 8-8 11Z"/><path d="M12 8a3 3 0 1 0 0 6"/><path d="M12 6v2"/></svg>',
            ),
            'medicos' => array(
                'nombre' => '🏥 Médicos',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/></svg>',
            ),
            'belleza' => array(
                'nombre' => '💇 Belleza',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v.01"/><path d="M8 12h8"/></svg>',
            ),
            'automotriz' => array(
                'nombre' => '🚗 Automotriz',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17h14v-5l-2-4H7l-2 4v5Z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>',
            ),
            'educacion' => array(
                'nombre' => '📚 Educación',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
            ),
            'entretenimiento' => array(
                'nombre' => '🎬 Entretenimiento',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"/><polyline points="17 2 12 7 7 2"/></svg>',
            ),
            'mascotas' => array(
                'nombre' => '🐾 Mascotas',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="20" cy="16" r="2"/><path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"/></svg>',
            ),
            'servicios' => array(
                'nombre' => '🔧 Servicios',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
            ),
        );
    }
    
    public function enqueue() {
        wp_register_style('cnmx-cat-css', CNMX_CAT_URL . 'assets/css/categorias.css', array(), '1.0.0');
    }
    
    public function shortcode_categorias($atts) {
        $atts = shortcode_atts(array(
            'num' => 12,
        ), $atts);
        
        $categorias = get_terms(array(
            'taxonomy' => 'categoria',
            'hide_empty' => true,
            'number' => intval($atts['num']),
        ));
        
        if (empty($categorias)) {
            return '';
        }
        
        $iconos = $this->get_iconos_predefinidos();
        
        wp_enqueue_style('cnmx-cat-css');
        
        $html = '<section class="categorias-section">';
        $html .= '<div class="container">';
        $html .= '<div class="section-header">';
        $html .= '<h2 class="section-title">Explora por Categoría</h2>';
        $html .= '<a href="' . home_url('/directorio') . '" class="section-link">Ver todas →</a>';
        $html .= '</div>';
        $html .= '<div class="categorias-grid">';
        
        foreach ($categorias as $cat) {
            $icono_key = get_term_meta($cat->term_id, 'cnmx_icono', true);
            $icono_data = isset($iconos[$icono_key]) ? $iconos[$icono_key] : null;
            $svg = $icono_data ? $icono_data['svg'] : $iconos['servicios']['svg'];
            
            $html .= '<a href="' . home_url('/directorio?categoria=' . $cat->slug) . '" class="categoria-card">';
            $html .= '<div class="categoria-icono">' . $svg . '</div>';
            $html .= '<span class="categoria-nombre">' . esc_html($cat->name) . '</span>';
            $html .= '<span class="categoria-count">' . $cat->count . ' lugares</span>';
            $html .= '</a>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</section>';
        
        return $html;
    }
}

new CNMX_Categorias();
