<?php
/**
 * Fotos Setup Class
 */

if (!defined('ABSPATH')) exit;

class CNMX_Fotos_Setup {
    
    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_cnmx_foto', [$this, 'save_meta']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('rest_api_init', [$this, 'register_rest_fields']);
    }
    
    public function register_cpt() {
        register_post_type('cnmx_foto', [
            'labels' => [
                'name' => 'Fotos',
                'singular_name' => 'Foto',
                'add_new' => 'Añadir Foto',
                'add_new_item' => 'Nueva Foto',
                'edit_item' => 'Editar Foto',
                'view_item' => 'Ver Foto',
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'cuentanos-business',
            'supports' => ['title', 'thumbnail'],
            'capability_type' => 'post',
            'capabilities' => [
                'create_posts' => 'manage_options',
                'edit_posts' => 'manage_options',
                'delete_posts' => 'manage_options',
            ],
        ]);
    }
    
    public function add_meta_boxes() {
        add_meta_box(
            'cnmx_foto_info',
            'Información de la Foto',
            [$this, 'render_meta_box'],
            'cnmx_foto',
            'normal',
            'high'
        );
    }
    
    public function render_meta_box($post) {
        $negocio_id = get_post_meta($post->ID, 'negocio_id', true);
        $descripcion = get_post_meta($post->ID, 'descripcion', true);
        $orden = get_post_meta($post->ID, 'orden', true);
        
        $negocios = get_posts([
            'post_type' => 'negocio',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ]);
        ?>
        <p>
            <label for="negocio_id">Negocio:</label>
            <select name="negocio_id" id="negocio_id" style="width: 100%;">
                <option value="">-- Seleccionar --</option>
                <?php foreach ($negocios as $n): ?>
                    <option value="<?php echo $n->ID; ?>" <?php selected($negocio_id, $n->ID); ?>>
                        <?php echo esc_html($n->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" id="descripcion" value="<?php echo esc_attr($descripcion); ?>" style="width: 100%;">
        </p>
        <p>
            <label for="orden">Orden:</label>
            <input type="number" name="orden" id="orden" value="<?php echo esc_attr($orden ?: 0); ?>" style="width: 100px;">
        </p>
        <?php
    }
    
    public function save_meta($post_id) {
        if (isset($_POST['negocio_id'])) {
            update_post_meta($post_id, 'negocio_id', intval($_POST['negocio_id']));
        }
        if (isset($_POST['descripcion'])) {
            update_post_meta($post_id, 'descripcion', sanitize_text_field($_POST['descripcion']));
        }
        if (isset($_POST['orden'])) {
            update_post_meta($post_id, 'orden', intval($_POST['orden']));
        }
    }
    
    public function enqueue_assets() {
        wp_enqueue_style('cnmx-fotos', CNMX_FOTOS_URL . 'assets/css/fotos.css', [], CNMX_FOTOS_VERSION);
        wp_enqueue_script('cnmx-fotos', CNMX_FOTOS_URL . 'assets/js/fotos.js', ['jquery'], CNMX_FOTOS_VERSION, true);
        
        wp_localize_script('cnmx-fotos', 'cnmxFotosData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cnmx_fotos_nonce'),
        ]);
    }
    
    public function register_rest_fields() {
        register_rest_field('cnmx_foto', 'negocio_id', [
            'get_callback' => function($post) {
                return get_post_meta($post['id'], 'negocio_id', true);
            },
        ]);
        
        register_rest_field('cnmx_foto', 'descripcion', [
            'get_callback' => function($post) {
                return get_post_meta($post['id'], 'descripcion', true);
            },
        ]);
    }
}
