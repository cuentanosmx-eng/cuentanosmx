<?php
/**
 * Plugin Name: Cuentanos Reviews
 * Plugin URI: https://cuentanos.mx
 * Description: Sistema completo de reseñas con reacciones, fotos y gamificación.
 * Version: 1.0.0
 * Author: Cuentanos Team
 */

if (!defined('ABSPATH')) exit;

define('CNMX_REVIEWS_PATH', plugin_dir_path(__FILE__));
define('CNMX_REVIEWS_URL', plugin_dir_url(__FILE__));

class CNMX_Reviews {
    
    public function __construct() {
        add_action('init', array($this, 'register_cpt'));
        add_action('init', array($this, 'create_tables'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_cnmx_resena', array($this, 'save_meta'));
        add_shortcode('cnmx_actividad_reciente', array($this, 'shortcode_actividad_reciente'));
        add_shortcode('cnmx_resenas', array($this, 'shortcode_resenas'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_cnmx_reaccionar', array($this, 'ajax_reaccionar'));
        add_action('wp_ajax_nopriv_cnmx_reaccionar', array($this, 'ajax_reaccionar'));
        add_action('wp_ajax_cnmx_get_resenas', array($this, 'ajax_get_resenas'));
    }
    
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_reacciones = $wpdb->prefix . 'cnmx_resena_reacciones';
        $table_fotos = $wpdb->prefix . 'cnmx_resena_fotos';
        
        $sql_reacciones = "CREATE TABLE IF NOT EXISTS $table_reacciones (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            resena_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            emoji varchar(50) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY unique_reaccion (resena_id, user_id, emoji),
            KEY resena_id (resena_id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        $sql_fotos = "CREATE TABLE IF NOT EXISTS $table_fotos (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            resena_id bigint(20) NOT NULL,
            foto_url text NOT NULL,
            orden int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY resena_id (resena_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_reacciones);
        dbDelta($sql_fotos);
    }
    
    public function register_cpt() {
        register_post_type('cnmx_resena', array(
            'labels' => array(
                'name' => 'Reseñas',
                'singular_name' => 'Reseña',
                'add_new' => 'Añadir Reseña',
                'add_new_item' => 'Añadir Nueva Reseña',
                'edit_item' => 'Editar Reseña',
                'new_item' => 'Nueva Reseña',
                'view_item' => 'Ver Reseña',
                'search_items' => 'Buscar Reseñas',
                'not_found' => 'No se encontraron reseñas',
                'not_found_in_trash' => 'No hay reseñas en la papelera',
            ),
            'public' => true,
            'show_in_rest' => true,
            'supports' => array('title', 'editor'),
            'menu_icon' => 'dashicons-star-filled',
            'has_archive' => false,
            'rewrite' => false,
        ));
    }
    
    public function add_meta_boxes() {
        add_meta_box(
            'cnmx_resena_meta',
            'Configuración de la Reseña',
            array($this, 'render_meta_box'),
            'cnmx_resena',
            'normal',
            'high'
        );
        
        add_meta_box(
            'cnmx_resena_fotos',
            'Fotos de la Reseña',
            array($this, 'render_fotos_meta'),
            'cnmx_resena',
            'side',
            'default'
        );
    }
    
    public function render_meta_box($post) {
        wp_nonce_field('cnmx_resena_meta', 'cnmx_resena_nonce');
        
        $negocio_id = get_post_meta($post->ID, 'cnmx_negocio_id', true);
        $user_id = get_post_meta($post->ID, 'cnmx_user_id', true);
        $rating = get_post_meta($post->ID, 'cnmx_rating', true) ?: 5;
        $fotos_urls = get_post_meta($post->ID, 'cnmx_fotos', true);
        $fecha_resena = get_post_meta($post->ID, 'cnmx_fecha_resena', true);
        
        $negocios = get_posts(array(
            'post_type' => 'negocio',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        ));
        
        $users = get_users(array(
            'number' => -1,
            'orderby' => 'display_name',
        ));
        ?>
        
        <style>
            .cnmx-resena-meta { max-width: 600px; }
            .cnmx-resena-meta p { margin: 16px 0; }
            .cnmx-resena-meta label { display: block; font-weight: 600; margin-bottom: 6px; }
            .cnmx-resena-meta input[type="text"],
            .cnmx-resena-meta input[type="number"],
            .cnmx-resena-meta input[type="url"],
            .cnmx-resena-meta select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
            .cnmx-resena-meta .description { color: #666; font-size: 12px; margin-top: 4px; }
            
            .rating-stars { display: flex; gap: 4px; }
            .rating-stars span { font-size: 24px; cursor: pointer; color: #ddd; transition: color 0.2s; }
            .rating-stars span:hover,
            .rating-stars span.active { color: #F89D2F; }
            
            .fotos-preview { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
            .fotos-preview img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #EB510C; }
        </style>
        
        <div class="cnmx-resena-meta">
            <p>
                <label for="cnmx_negocio_id">Negocio</label>
                <select id="cnmx_negocio_id" name="cnmx_negocio_id">
                    <option value="">-- Seleccionar negocio --</option>
                    <?php foreach ($negocios as $negocio): ?>
                        <option value="<?php echo $negocio->ID; ?>" <?php selected($negocio_id, $negocio->ID); ?>>
                            <?php echo esc_html($negocio->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            
            <p>
                <label for="cnmx_user_id">Usuario</label>
                <select id="cnmx_user_id" name="cnmx_user_id">
                    <option value="">-- Seleccionar usuario --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user->ID; ?>" <?php selected($user_id, $user->ID); ?>>
                            <?php echo esc_html($user->display_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            
            <p>
                <label>Calificación (Estrellas)</label>
                <input type="hidden" id="cnmx_rating" name="cnmx_rating" value="<?php echo esc_attr($rating); ?>">
                <div class="rating-stars" id="rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="<?php echo $i <= $rating ? 'active' : ''; ?>" data-rating="<?php echo $i; ?>">★</span>
                    <?php endfor; ?>
                </div>
            </p>
            
            <p>
                <label for="cnmx_fecha_resena">Fecha de la Reseña</label>
                <input type="datetime-local" id="cnmx_fecha_resena" name="cnmx_fecha_resena" 
                       value="<?php echo esc_attr($fecha_resena ? date('Y-m-d\TH:i', strtotime($fecha_resena)) : ''); ?>">
            </p>
            
            <p>
                <label for="cnmx_fotos">URLs de Fotos (una por línea)</label>
                <textarea id="cnmx_fotos" name="cnmx_fotos" rows="4" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:4px;" placeholder="https://...\nhttps://..."><?php echo esc_textarea($fotos_urls); ?></textarea>
                <span class="description">Añade las URLs de las fotos, una por línea.</span>
            </p>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#rating-stars span').on('click', function() {
                var rating = $(this).data('rating');
                $('#cnmx_rating').val(rating);
                $('#rating-stars span').removeClass('active');
                $('#rating-stars span').each(function() {
                    if ($(this).data('rating') <= rating) {
                        $(this).addClass('active');
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    public function render_fotos_meta($post) {
        $fotos = get_post_meta($post->ID, 'cnmx_fotos', true);
        $fotos_array = $fotos ? explode("\n", trim($fotos)) : array();
        ?>
        <p>Añade las URLs de las fotos en el panel principal.</p>
        <?php if (!empty($fotos_array)): ?>
            <div class="fotos-preview">
                <?php foreach ($fotos_array as $foto): ?>
                    <?php $foto = trim($foto); if ($foto): ?>
                        <img src="<?php echo esc_url($foto); ?>" alt="Foto">
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php
    }
    
    public function save_meta($post_id) {
        if (!isset($_POST['cnmx_resena_nonce']) || !wp_verify_nonce($_POST['cnmx_resena_nonce'], 'cnmx_resena_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        $fields = array('cnmx_negocio_id', 'cnmx_user_id', 'cnmx_rating', 'cnmx_fotos', 'cnmx_fecha_resena');
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                if ($field === 'cnmx_fotos') {
                    update_post_meta($post_id, $field, sanitize_textarea_field($_POST[$field]));
                } else {
                    update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
                }
            }
        }
    }
    
    public function enqueue_assets() {
        wp_register_style('cnmx-reviews-css', CNMX_REVIEWS_URL . 'assets/css/reviews.css', array(), '1.0.0');
        wp_register_script('cnmx-reviews-js', CNMX_REVIEWS_URL . 'assets/js/reviews.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('cnmx-reviews-js', 'cnmxReviews', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cnmx_reviews_nonce'),
        ));
    }
    
    public function ajax_reaccionar() {
        check_ajax_referer('cnmx_reviews_nonce', 'nonce');
        
        global $wpdb;
        $table = $wpdb->prefix . 'cnmx_resena_reacciones';
        
        $resena_id = intval($_POST['resena_id']);
        $user_id = get_current_user_id();
        $emoji = sanitize_text_field($_POST['emoji']);
        
        if (!$user_id) {
            wp_send_json_error('Debes iniciar sesión');
            return;
        }
        
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE resena_id = %d AND user_id = %d AND emoji = %s",
            $resena_id, $user_id, $emoji
        ));
        
        if ($exists) {
            $wpdb->delete($table, array('id' => $exists));
            $action = 'removed';
        } else {
            $wpdb->insert($table, array(
                'resena_id' => $resena_id,
                'user_id' => $user_id,
                'emoji' => $emoji,
            ));
            $action = 'added';
        }
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE resena_id = %d AND emoji = %s",
            $resena_id, $emoji
        ));
        
        $user_reacted = !$exists;
        
        wp_send_json_success(array(
            'count' => $count,
            'action' => $action,
            'user_reacted' => $user_reacted,
        ));
    }
    
    public function ajax_get_resenas() {
        check_ajax_referer('cnmx_reviews_nonce', 'nonce');
        
        $negocio_id = intval($_POST['negocio_id']);
        $offset = intval($_POST['offset']);
        $limit = intval($_POST['limit']);
        
        $resenas = $this->get_resenas($negocio_id, $limit, $offset);
        $html = $this->render_resenas($resenas);
        
        wp_send_json_success(array('html' => $html));
    }
    
    public function get_resenas($negocio_id = null, $limit = 12, $offset = 0) {
        $args = array(
            'post_type' => 'cnmx_resena',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'offset' => $offset,
            'orderby' => 'meta_value',
            'meta_key' => 'cnmx_fecha_resena',
            'order' => 'DESC',
        );
        
        if ($negocio_id) {
            $args['meta_query'] = array(
                array(
                    'key' => 'cnmx_negocio_id',
                    'value' => $negocio_id,
                )
            );
        }
        
        return get_posts($args);
    }
    
    public function get_reacciones($resena_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'cnmx_resena_reacciones';
        
        $reacciones = $wpdb->get_results($wpdb->prepare(
            "SELECT emoji, COUNT(*) as count FROM $table WHERE resena_id = %d GROUP BY emoji ORDER BY count DESC",
            $resena_id
        ));
        
        $user_reacciones = array();
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $user_reac = $wpdb->get_results($wpdb->prepare(
                "SELECT emoji FROM $table WHERE resena_id = %d AND user_id = %d",
                $resena_id, $user_id
            ));
            $user_reacciones = wp_list_pluck($user_reac, 'emoji');
        }
        
        return array('reacciones' => $reacciones, 'user_reacciones' => $user_reacciones);
    }
    
    public function get_fotos($resena_id) {
        $fotos = get_post_meta($resena_id, 'cnmx_fotos', true);
        if ($fotos) {
            $fotos_array = array_filter(array_map('trim', explode("\n", $fotos)));
            return $fotos_array;
        }
        return array();
    }
    
    public function get_resena_foto_negocio($resena_id) {
        $negocio_id = get_post_meta($resena_id, 'cnmx_negocio_id', true);
        if ($negocio_id && has_post_thumbnail($negocio_id)) {
            return get_the_post_thumbnail_url($negocio_id, 'thumbnail');
        }
        return '';
    }
    
    public function render_resenas($resenas, $args = array()) {
        $defaults = array(
            'show_negocio' => true,
            'show_fotos' => true,
            'layout' => 'grid',
        );
        $args = wp_parse_args($args, $defaults);
        
        $html = '';
        
        foreach ($resenas as $resena) {
            $negocio_id = get_post_meta($resena->ID, 'cnmx_negocio_id', true);
            $user_id = get_post_meta($resena->ID, 'cnmx_user_id', true);
            $rating = get_post_meta($resena->ID, 'cnmx_rating', true) ?: 5;
            $fecha = get_post_meta($resena->ID, 'cnmx_fecha_resena', true);
            $fotos = $this->get_fotos($resena->ID);
            
            $user = get_userdata($user_id);
            $user_name = $user ? $user->display_name : 'Usuario';
            $user_avatar = get_user_meta($user_id, 'cnmx_avatar', true);
            if (!$user_avatar) {
                $user_avatar = get_avatar_url($user_id, array('size' => 80));
            }
            $user_profile_url = home_url('/perfil/' . $user_id);
            
            $negocio = $negocio_id ? get_post($negocio_id) : null;
            $negocio_nombre = $negocio ? $negocio->post_title : '';
            $negocio_url = $negocio ? get_permalink($negocio_id) : '#';
            $negocio_foto = $this->get_resena_foto_negocio($resena->ID);
            
            $reacciones_data = $this->get_reacciones($resena->ID);
            $reacciones = $reacciones_data['reacciones'];
            $user_reacciones = $reacciones_data['user_reacciones'];
            
            $fecha_formato = $fecha ? $this->time_ago($fecha) : get_the_date('d M Y', $resena);
            
            $clase_layout = $args['layout'] === 'grid' ? 'review-card-grid' : 'review-card-list';
            
            $html .= '<article class="review-card ' . $clase_layout . '" data-resena-id="' . $resena->ID . '">';
            
            if ($args['show_negocio'] && $negocio): 
                $html .= '<a href="' . esc_url($negocio_url) . '" class="review-negocio-link">';
                $html .= '<div class="review-negocio">';
                if ($negocio_foto):
                    $html .= '<img src="' . esc_url($negocio_foto) . '" alt="' . esc_attr($negocio_nombre) . '" class="review-negocio-img">';
                endif;
                $html .= '<div class="review-negocio-info">';
                $html .= '<span class="review-negocio-nombre">' . esc_html($negocio_nombre) . '</span>';
                $html .= '<span class="review-fecha">' . esc_html($fecha_formato) . '</span>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</a>';
            endif;
            
            $html .= '<div class="review-content">';
            
            $html .= '<div class="review-header">';
            $html .= '<a href="' . esc_url($user_profile_url) . '" class="review-user-link">';
            $html .= '<img src="' . esc_url($user_avatar) . '" alt="' . esc_attr($user_name) . '" class="review-user-avatar">';
            $html .= '<div class="review-user-info">';
            $html .= '<span class="review-user-name">' . esc_html($user_name) . '</span>';
            $html .= '<div class="review-rating">';
            for ($i = 1; $i <= 5; $i++) {
                $html .= '<span class="' . ($i <= $rating ? 'filled' : 'empty') . '">★</span>';
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</a>';
            $html .= '</div>';
            
            $html .= '<div class="review-text">' . wpautop(get_the_content('', false, $resena)) . '</div>';
            
            if ($args['show_fotos'] && !empty($fotos)):
                $html .= '<div class="review-fotos">';
                foreach ($fotos as $foto):
                    $html .= '<a href="' . esc_url($foto) . '" class="review-foto" data-lightbox="resena-' . $resena->ID . '">';
                    $html .= '<img src="' . esc_url($foto) . '" alt="Foto de reseña">';
                    $html .= '</a>';
                endforeach;
                $html .= '</div>';
            endif;
            
            $emojis = array('👍', '❤️', '😂', '😮', '😢', '😡');
            
            $html .= '<div class="review-reacciones">';
            $html .= '<div class="review-reacciones-list">';
            foreach ($reacciones as $reac):
                if ($reac->count > 0):
                    $html .= '<span class="reaccion-badge">' . $reac->emoji . ' <span class="reaccion-count">' . $reac->count . '</span></span>';
                endif;
            endforeach;
            $html .= '</div>';
            
            if (is_user_logged_in()):
                $html .= '<div class="review-reacciones-emojis">';
                foreach ($emojis as $emoji):
                    $active = in_array($emoji, $user_reacciones) ? 'active' : '';
                    $html .= '<button class="reaccion-btn ' . $active . '" data-emoji="' . $emoji . '" data-resena="' . $resena->ID . '">' . $emoji . '</button>';
                endforeach;
                $html .= '</div>';
            endif;
            $html .= '</div>';
            
            $html .= '</div>';
            $html .= '</article>';
        }
        
        return $html;
    }
    
    public function shortcode_actividad_reciente($atts) {
        $atts = shortcode_atts(array(
            'num' => 12,
        ), $atts);
        
        $resenas = $this->get_resenas(null, intval($atts['num']), 0);
        
        if (empty($resenas)) {
            return '<p class="no-resenas">No hay reseñas todavía. ¡Sé el primero en opinar!</p>';
        }
        
        wp_enqueue_style('cnmx-reviews-css');
        wp_enqueue_script('cnmx-reviews-js');
        
        $html = '<section class="actividad-reciente">';
        $html .= '<div class="container">';
        $html .= '<div class="section-header">';
        $html .= '<h2 class="section-title">Actividad Reciente</h2>';
        $html .= '<a href="' . home_url('/resenas') . '" class="section-link">Ver todas →</a>';
        $html .= '</div>';
        $html .= '<div class="reviews-grid">';
        $html .= $this->render_resenas($resenas, array('show_negocio' => true, 'layout' => 'grid'));
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</section>';
        
        return $html;
    }
    
    public function shortcode_resenas($atts) {
        $atts = shortcode_atts(array(
            'negocio' => '',
            'num' => 10,
        ), $atts);
        
        $negocio_id = 0;
        if (!empty($atts['negocio'])) {
            $negocio = get_page_by_path($atts['negocio'], OBJECT, 'negocio');
            if ($negocio) {
                $negocio_id = $negocio->ID;
            }
        }
        
        $resenas = $this->get_resenas($negocio_id, intval($atts['num']), 0);
        
        if (empty($resenas)) {
            return '<p class="no-resenas">Esta sección aún no tiene reseñas.</p>';
        }
        
        wp_enqueue_style('cnmx-reviews-css');
        wp_enqueue_script('cnmx-reviews-js');
        
        $html = '<section class="resenas-section">';
        $html .= '<div class="resenas-list">';
        $html .= $this->render_resenas($resenas, array('show_negocio' => false, 'layout' => 'list'));
        $html .= '</div>';
        $html .= '</section>';
        
        return $html;
    }
    
    private function time_ago($datetime) {
        $time = strtotime($datetime);
        $diff = time() - $time;
        
        if ($diff < 60) return 'hace un momento';
        if ($diff < 3600) return 'hace ' . floor($diff / 60) . ' min';
        if ($diff < 86400) return 'hace ' . floor($diff / 3600) . ' h';
        if ($diff < 604800) return 'hace ' . floor($diff / 86400) . ' d';
        
        return date('d M Y', $time);
    }
}

new CNMX_Reviews();
