<?php
/**
 * Cuentanos MX - Theme Functions
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

define('CNMX_VERSION', '1.0.0');
define('CNMX_PATH', get_stylesheet_directory());
define('CNMX_URL', get_stylesheet_directory_uri());

/**
 * Enqueue Scripts and Styles
 */
function cnmx_enqueue_scripts() {
    // Check if Elementor is editing
    $is_elementor = isset($_GET['elementor']) || (did_action('elementor/loaded') && \Elementor\Plugin::instance()->preview->is_preview_mode());
    
    // Google Fonts - Inter
    wp_enqueue_style(
        'cnmx-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );
    
    // Only load theme styles if NOT in Elementor editor mode
    if (!$is_elementor) {
        // Parent theme CSS (Astra)
        $parent_version = wp_get_theme('astra')->get('Version');
        wp_enqueue_style(
            'astra-theme',
            get_template_directory_uri() . '/style.css',
            array(),
            $parent_version
        );
        
        // Custom CSS
        wp_enqueue_style(
            'cnmx-main',
            CNMX_URL . '/css/main.css',
            array('astra-theme'),
            CNMX_VERSION
        );
        
        // Leaflet CSS
        wp_enqueue_style(
            'leaflet-css',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            array(),
            '1.9.4'
        );
        
        // Leaflet JS
        wp_enqueue_script(
            'leaflet-js',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            array(),
            '1.9.4',
            true
        );
        
        // Main JS
        wp_enqueue_script(
            'cnmx-app',
            CNMX_URL . '/js/app.js',
            array('jquery'),
            CNMX_VERSION,
            true
        );
        
        // Localize data for AJAX
        wp_localize_script('cnmx-app', 'cnmxData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url(),
            'nonce' => wp_create_nonce('cnmx_nonce'),
            'userId' => get_current_user_id(),
            'isLoggedIn' => is_user_logged_in(),
            'homeUrl' => home_url(),
            'strings' => array(
                'loading' => 'Cargando...',
                'error' => 'Algo salió mal',
                'saved' => 'Guardado',
                'megafonos' => 'Megáfonos',
            )
        ));
    }
}
add_action('wp_enqueue_scripts', 'cnmx_enqueue_scripts', 20);

/**
 * Theme Setup
 */
function cnmx_setup() {
    // Title tag
    add_theme_support('title-tag');
    
    // Post thumbnails
    add_theme_support('post-thumbnails');
    
    // HTML5 support
    add_theme_support('html5', array('search-form', 'gallery', 'caption'));
    
    // Custom logo
    add_theme_support('custom-logo');
    
    // Image sizes
    add_image_size('cnmx-card', 600, 400, true);
    add_image_size('cnmx-thumb', 150, 150, true);
    add_image_size('cnmx-gallery', 800, 600, false);
    
    // Register menus
    register_nav_menus(array(
        'primary' => 'Menú Principal',
        'mobile' => 'Menú Móvil',
        'footer' => 'Menú Footer',
    ));
    
    // Elementor support
    add_theme_support('elementor');
    
    // Wide images
    add_theme_support('align-wide');
    
    // Editor styles
    add_theme_support('editor-styles');
    
    // Responsive embeds
    add_theme_support('responsive-embeds');
}
add_action('after_setup_theme', 'cnmx_setup');

/**
 * Elementor Compatibility
 */
function cnmx_elementor_support() {
    // Check if Elementor is active
    if (did_action('elementor/loaded')) {
        add_action('elementor/frontend/before_register_scripts', function() {
            // Our scripts already loaded in main enqueue
        });
    }
}
add_action('init', 'cnmx_elementor_support');

/**
 * Disable Astra header/footer when using Elementor templates
 */
function cnmx_disable_astra_header_footer($is_available) {
    if (is_singular() && defined('ELEMENTOR_VERSION')) {
        $elementor_yes = get_post_meta(get_the_ID(), '_elementor_edit_mode', true);
        if ($elementor_yes === 'builder') {
            return false;
        }
    }
    return $is_available;
}

/**
 * Register Custom Post Types
 */
function cnmx_register_post_types() {
    // Negocios Post Type
    register_post_type('negocio', array(
        'labels' => array(
            'name' => 'Negocios',
            'singular_name' => 'Negocio',
            'add_new' => 'Agregar Negocio',
            'add_new_item' => 'Agregar Nuevo Negocio',
            'edit_item' => 'Editar Negocio',
            'new_item' => 'Nuevo Negocio',
            'view_item' => 'Ver Negocio',
            'search_items' => 'Buscar Negocios',
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'negocio'),
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-store',
        'taxonomies' => array('categoria', 'etiqueta'),
    ));
    
    // Register taxonomies
    register_taxonomy('categoria', 'negocio', array(
        'labels' => array(
            'name' => 'Categorías',
            'singular_name' => 'Categoría',
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
    ));
    
    register_taxonomy('etiqueta', 'negocio', array(
        'labels' => array(
            'name' => 'Etiquetas',
            'singular_name' => 'Etiqueta',
        ),
        'hierarchical' => false,
        'show_in_rest' => true,
        'show_admin_column' => true,
    ));
    
    // Logros CPT
    register_post_type('cnmx_logro', array(
        'labels' => array(
            'name' => 'Logros',
            'singular_name' => 'Logro',
            'add_new' => 'Agregar Logro',
            'add_new_item' => 'Agregar Nuevo Logro',
            'edit_item' => 'Editar Logro',
        ),
        'public' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-awards',
    ));
    
    // Recompensas CPT
    register_post_type('cnmx_recompensa', array(
        'labels' => array(
            'name' => 'Recompensas',
            'singular_name' => 'Recompensa',
            'add_new' => 'Agregar Recompensa',
            'add_new_item' => 'Agregar Nueva Recompensa',
            'edit_item' => 'Editar Recompensa',
        ),
        'public' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-tickets-alt',
    ));
}
add_action('init', 'cnmx_register_post_types');

/**
 * Create Database Tables
 */
function cnmx_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    // Usuarios Meta Table
    $table1 = "CREATE TABLE {$wpdb->prefix}cnmx_usuarios_meta (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        megafonos int(11) DEFAULT 0,
        nivel varchar(50) DEFAULT 'explorador',
        fecha_registro datetime DEFAULT CURRENT_TIMESTAMP,
        ultimo_reset date DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY user_id (user_id)
    ) $charset_collate;";
    
    // Favoritos Table
    $table2 = "CREATE TABLE {$wpdb->prefix}cnmx_favoritos (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        negocio_id bigint(20) NOT NULL,
        fecha datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY user_negocio (user_id, negocio_id)
    ) $charset_collate;";
    
    // Reseñas Table
    $table3 = "CREATE TABLE {$wpdb->prefix}cnmx_resenas (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        negocio_id bigint(20) NOT NULL,
        user_id bigint(20) NOT NULL,
        calificacion tinyint(1) NOT NULL,
        contenido text,
        fecha datetime DEFAULT CURRENT_TIMESTAMP,
        status enum('pendiente','aprobado','spam') DEFAULT 'pendiente',
        PRIMARY KEY (id),
        KEY negocio_id (negocio_id),
        KEY user_id (user_id)
    ) $charset_collate;";
    
    // Historial Puntos
    $table4 = "CREATE TABLE {$wpdb->prefix}cnmx_historial (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        tipo varchar(50) NOT NULL,
        puntos int(11) NOT NULL,
        descripcion varchar(255),
        fecha datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id)
    ) $charset_collate;";
    
    // Recompensas
    $table5 = "CREATE TABLE {$wpdb->prefix}cnmx_recompensas (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        titulo varchar(100) NOT NULL,
        descripcion text,
        costo_megafonos int(11) NOT NULL,
        imagen_url varchar(255),
        activa tinyint(1) DEFAULT 1,
        fecha_creacion datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    // Canjes
    $table6 = "CREATE TABLE {$wpdb->prefix}cnmx_canjes (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        recompensa_id bigint(20) NOT NULL,
        codigo varchar(100),
        usado tinyint(1) DEFAULT 0,
        fecha_canje datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    dbDelta($table1);
    dbDelta($table2);
    dbDelta($table3);
    dbDelta($table4);
    dbDelta($table5);
    dbDelta($table6);
}
register_activation_hook(__FILE__, 'cnmx_create_tables');

/**
 * Create Default Pages on Theme Activation
 */
function cnmx_create_pages() {
    $pages = array(
        'directorio' => 'Directorio',
        'registro' => 'Registro',
        'mi-cuenta' => 'Mi Cuenta',
        'perfil' => 'Mi Perfil',
        'registrar-negocio' => 'Registrar Negocio',
        'login-negocio' => 'Login Negocio',
        'dashboard-negocio' => 'Dashboard Negocio',
        'mis-favoritos' => 'Mis Favoritos',
        'mis-resenas' => 'Mis Reseñas',
        'recompensas' => 'Recompensas',
    );
    
    foreach ($pages as $slug => $title) {
        $existing = get_page_by_path($slug);
        if (!$existing) {
            wp_insert_post(array(
                'post_title' => $title,
                'post_name' => $slug,
                'post_status' => 'publish',
                'post_type' => 'page',
            ));
        }
    }
}
add_action('after_switch_theme', 'cnmx_create_pages');

/**
 * Create Default Categories
 */
function cnmx_create_categories() {
    $categories = array(
        'restaurantes' => 'Restaurantes',
        'cafeterias' => 'Cafeterías',
        'hoteles' => 'Hoteles',
        'bares' => 'Bares y Cantinas',
        'tiendas' => 'Tiendas',
        'spa' => 'Spa y Bienestar',
        'gimnasios' => 'Gimnasios',
        'medicos' => 'Servicios Médicos',
        'educacion' => 'Educación',
        'turismo' => 'Turismo',
        'entretenimiento' => 'Entretenimiento',
        'automotriz' => 'Automotriz',
        'hogar' => 'Hogar',
        'tecnologia' => 'Tecnología',
    );
    
    foreach ($categories as $slug => $name) {
        if (!term_exists($slug, 'categoria')) {
            wp_insert_term($name, 'categoria', array('slug' => $slug));
        }
    }
}
add_action('after_switch_theme', 'cnmx_create_categories');

/**
 * Create Demo Data
 */
function cnmx_create_demo_data() {
    if (get_option('cnmx_demo_created')) return;
    
    // Create demo categories if not exist
    cnmx_create_categories();
    
    // Demo businesses
    $businesses = array(
        array(
            'title' => 'La Casa de los Tacos',
            'slug' => 'la-casa-de-los-tacos',
            'cat' => 'restaurantes',
            'desc' => 'Los mejores tacos de la ciudad con ingredientes frescos y tradicionales.',
            'rating' => 4.8,
            'reviews' => 156,
            'location' => 'Centro Histórico',
        ),
        array(
            'title' => 'Café del Mar',
            'slug' => 'cafe-del-mar',
            'cat' => 'cafeterias',
            'desc' => 'Café de especialidad en un ambiente acogedor.',
            'rating' => 4.6,
            'reviews' => 89,
            'location' => 'Zona Romántica',
        ),
        array(
            'title' => 'Hotel Boutique La Paz',
            'slug' => 'hotel-boutique-la-paz',
            'cat' => 'hoteles',
            'desc' => 'Hotel boutique con vista al mar y servicios premium.',
            'rating' => 4.9,
            'reviews' => 234,
            'location' => 'Playa Principal',
        ),
        array(
            'title' => 'Spa Relax & Wellness',
            'slug' => 'spa-relax',
            'cat' => 'spa',
            'desc' => 'Centro de bienestar integral con masajes y tratamientos.',
            'rating' => 4.7,
            'reviews' => 112,
            'location' => 'Zona Hotelera',
        ),
        array(
            'title' => 'Bar El Dorado',
            'slug' => 'bar-el-dorado',
            'cat' => 'bares',
            'desc' => 'Coctelería artesanal y ambiente sofisticado.',
            'rating' => 4.4,
            'reviews' => 78,
            'location' => 'Centro',
        ),
        array(
            'title' => 'Gimnasio PowerFit',
            'slug' => 'gimnasio-powerfit',
            'cat' => 'gimnasios',
            'desc' => 'Gimnasio completo con equipos modernos.',
            'rating' => 4.3,
            'reviews' => 145,
            'location' => 'Av. Principal',
        ),
    );
    
    foreach ($businesses as $biz) {
        $existing = get_page_by_path($biz['slug'], OBJECT, 'negocio');
        if ($existing) continue;
        
        $term = get_term_by('slug', $biz['cat'], 'categoria');
        $cat_id = $term ? $term->term_id : 0;
        
        $post_id = wp_insert_post(array(
            'post_type' => 'negocio',
            'post_title' => $biz['title'],
            'post_name' => $biz['slug'],
            'post_content' => $biz['desc'],
            'post_status' => 'publish',
        ));
        
        if ($cat_id) {
            wp_set_object_terms($post_id, $cat_id, 'categoria');
        }
        
        // Add meta
        update_post_meta($post_id, 'cnmx_rating', $biz['rating']);
        update_post_meta($post_id, 'cnmx_reviews_count', $biz['reviews']);
        update_post_meta($post_id, 'cnmx_direccion', $biz['location']);
        update_post_meta($post_id, 'cnmx_telefono', '55 1234 5678');
        update_post_meta($post_id, 'cnmx_whatsapp', '525512345678');
        update_post_meta($post_id, 'cnmx_horarios', json_encode(array(
            'lunes' => '9:00 - 21:00',
            'martes' => '9:00 - 21:00',
            'miercoles' => '9:00 - 21:00',
            'jueves' => '9:00 - 21:00',
            'viernes' => '9:00 - 22:00',
            'sabado' => '10:00 - 22:00',
            'domingo' => '10:00 - 18:00',
        )));
    }
    
    // Create demo rewards using CPT
    $recompensas_data = array(
        array('titulo' => 'Descuento 10%', 'desc' => 'Descuento en tu próxima visita', 'costo' => 50, 'codigo' => 'DESCUENTO10'),
        array('titulo' => 'Café Gratis', 'desc' => 'Un café gratis en cualquier cafetería partner', 'costo' => 30, 'codigo' => 'CAFEFREE'),
        array('titulo' => '1 Mes Premium', 'desc' => 'Acceso premium por un mes - Características exclusivas', 'costo' => 200, 'codigo' => 'PREMIUM1M'),
        array('titulo' => 'Tour Gratuito', 'desc' => 'Un tour gratuito en cualquier tour partner', 'costo' => 100, 'codigo' => 'TOURFREE'),
    );
    
    foreach ($recompensas_data as $rec) {
        $existing = get_page_by_path(sanitize_title($rec['titulo']), OBJECT, 'cnmx_recompensa');
        if (!$existing) {
            $post_id = wp_insert_post(array(
                'post_type' => 'cnmx_recompensa',
                'post_title' => $rec['titulo'],
                'post_content' => $rec['desc'],
                'post_status' => 'publish',
            ));
            update_post_meta($post_id, 'cnmx_recompensa_megafonos', $rec['costo']);
            update_post_meta($post_id, 'cnmx_recompensa_codigo', $rec['codigo']);
            update_post_meta($post_id, 'cnmx_recompensa_activa', 'si');
        }
    }
    
    // Create demo achievements using CPT
    $logros_data = array(
        array('titulo' => 'Primera Reseña', 'desc' => 'Escribe tu primera reseña', 'megafonos' => 10),
        array('titulo' => 'Explorador', 'desc' => 'Guarda 5 negocios en favoritos', 'megafonos' => 25),
        array('titulo' => 'Crítico', 'desc' => 'Escribe 10 reseñas', 'megafonos' => 50),
        array('titulo' => 'Colaborador', 'desc' => 'Ayuda a otros con tus reseñas - 100 upvote', 'megafonos' => 75),
        array('titulo' => 'Embajador', 'desc' => 'Invita a 5 amigos', 'megafonos' => 100),
    );
    
    foreach ($logros_data as $logro) {
        $existing = get_page_by_path(sanitize_title($logro['titulo']), OBJECT, 'cnmx_logro');
        if (!$existing) {
            $post_id = wp_insert_post(array(
                'post_type' => 'cnmx_logro',
                'post_title' => $logro['titulo'],
                'post_content' => $logro['desc'],
                'post_status' => 'publish',
            ));
            update_post_meta($post_id, 'cnmx_logro_megafonos', $logro['megafonos']);
            update_post_meta($post_id, 'cnmx_logro_activo', 'si');
        }
    }
    
    update_option('cnmx_demo_created', true);
}
add_action('after_switch_theme', 'cnmx_create_demo_data');

/**
 * REST API Endpoints
 */
add_action('rest_api_init', function() {
    register_rest_route('cuentanos/v1', '/negocios', array(
        'methods' => 'GET',
        'callback' => function() {
            $args = array(
                'post_type' => 'negocio',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            );
            $negocios = get_posts($args);
            $data = array();
            
            foreach ($negocios as $negocio) {
                $data[] = array(
                    'id' => $negocio->ID,
                    'title' => $negocio->post_title,
                    'slug' => $negocio->post_name,
                    'excerpt' => $negocio->post_excerpt,
                    'rating' => get_post_meta($negocio->ID, 'cnmx_rating', true),
                    'reviews' => get_post_meta($negocio->ID, 'cnmx_reviews_count', true),
                    'image' => get_the_post_thumbnail_url($negocio->ID, 'cnmx-card'),
                );
            }
            
            return new WP_REST_Response($data, 200);
        }
    ));
    
    // Toggle Favorito
    register_rest_route('cuentanos/v1', '/favorito', array(
        'methods' => 'POST',
        'callback' => function($request) {
            if (!is_user_logged_in()) {
                return new WP_Error('unauthorized', 'Debes iniciar sesión', array('status' => 401));
            }
            
            global $wpdb;
            $user_id = get_current_user_id();
            $negocio_id = intval($request->get_param('negocio_id'));
            
            $table = $wpdb->prefix . 'cnmx_favoritos';
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE user_id = %d AND negocio_id = %d",
                $user_id, $negocio_id
            ));
            
            if ($exists) {
                $wpdb->delete($table, array('id' => $exists));
                return new WP_REST_Response(array('success' => true, 'action' => 'removed'), 200);
            } else {
                $wpdb->insert($table, array(
                    'user_id' => $user_id,
                    'negocio_id' => $negocio_id,
                ));
                // Add megafonos
                cnmx_add_megafonos($user_id, 5, 'Agregaste a favoritos');
                return new WP_REST_Response(array('success' => true, 'action' => 'added'), 200);
            }
        }
    ));
    
    // Submit Review
    register_rest_route('cuentanos/v1', '/resena', array(
        'methods' => 'POST',
        'callback' => function($request) {
            if (!is_user_logged_in()) {
                return new WP_Error('unauthorized', 'Debes iniciar sesión', array('status' => 401));
            }
            
            global $wpdb;
            $user_id = get_current_user_id();
            $negocio_id = intval($request->get_param('negocio_id'));
            $calificacion = intval($request->get_param('calificacion'));
            $contenido = sanitize_textarea_field($request->get_param('contenido'));
            
            if (!$negocio_id || !$calificacion || !$contenido) {
                return new WP_Error('missing_data', 'Faltan datos', array('status' => 400));
            }
            
            $wpdb->insert($wpdb->prefix . 'cnmx_resenas', array(
                'negocio_id' => $negocio_id,
                'user_id' => $user_id,
                'calificacion' => $calificacion,
                'contenido' => $contenido,
                'status' => 'aprobado',
            ));
            
            // Add megafonos
            cnmx_add_megafonos($user_id, 10, 'Escribiste una reseña');
            
            return new WP_REST_Response(array('success' => true), 200);
        }
    ));
    
    // Get User Data
    register_rest_route('cuentanos/v1', '/usuario', array(
        'methods' => 'GET',
        'callback' => function() {
            if (!is_user_logged_in()) {
                return new WP_Error('unauthorized', 'Debes iniciar sesión', array('status' => 401));
            }
            
            global $wpdb;
            $user_id = get_current_user_id();
            
            $meta = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d",
                $user_id
            ));
            
            if (!$meta) {
                $wpdb->insert($wpdb->prefix . 'cnmx_usuarios_meta', array(
                    'user_id' => $user_id,
                    'megafonos' => 0,
                    'nivel' => 'explorador',
                ));
                $meta = $wpdb->get_row($wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d",
                    $user_id
                ));
            }
            
            // Get favorites count
            $favoritos = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}cnmx_favoritos WHERE user_id = %d",
                $user_id
            ));
            
            // Get reviews count
            $resenas = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}cnmx_resenas WHERE user_id = %d",
                $user_id
            ));
            
            return new WP_REST_Response(array(
                'megafonos' => $meta ? $meta->megafonos : 0,
                'nivel' => $meta ? $meta->nivel : 'explorador',
                'favoritos' => $favoritos,
                'resenas' => $resenas,
            ), 200);
        }
    ));
});

/**
 * Add Megafonos to User
 */
function cnmx_add_megafonos($user_id, $puntos, $descripcion) {
    global $wpdb;
    
    // Update user meta
    $meta = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d",
        $user_id
    ));
    
    if ($meta) {
        $nuevos = $meta->megafonos + $puntos;
        $wpdb->update(
            $wpdb->prefix . 'cnmx_usuarios_meta',
            array('megafonos' => $nuevos),
            array('user_id' => $user_id)
        );
    }
    
    // Add to history
    $wpdb->insert($wpdb->prefix . 'cnmx_historial', array(
        'user_id' => $user_id,
        'tipo' => 'ganar',
        'puntos' => $puntos,
        'descripcion' => $descripcion,
    ));
}

/**
 * AJAX Handlers
 */
add_action('wp_ajax_cnmx_favorito', 'cnmx_ajax_favorito');
add_action('wp_ajax_nopriv_cnmx_favorito', function() {
    wp_send_json_error('Debes iniciar sesión');
});

function cnmx_ajax_favorito() {
    check_ajax_referer('cnmx_nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Debes iniciar sesión');
    }
    
    $negocio_id = intval($_POST['negocio_id']);
    $user_id = get_current_user_id();
    
    global $wpdb;
    $table = $wpdb->prefix . 'cnmx_favoritos';
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table WHERE user_id = %d AND negocio_id = %d",
        $user_id, $negocio_id
    ));
    
    if ($exists) {
        $wpdb->delete($table, array('id' => $exists));
        wp_send_json_success(array('action' => 'removed'));
    } else {
        $wpdb->insert($table, array(
            'user_id' => $user_id,
            'negocio_id' => $negocio_id,
        ));
        cnmx_add_megafonos($user_id, 5, 'Agregaste a favoritos');
        wp_send_json_success(array('action' => 'added'));
    }
}

/**
 * Get User Level based on Megafonos
 */
function cnmx_get_nivel($megafonos) {
    if ($megafonos >= 1000) return 'leyenda';
    if ($megafonos >= 500) return 'experto';
    if ($megafonos >= 200) return 'avanzado';
    if ($megafonos >= 100) return 'intermedio';
    if ($megafonos >= 50) return 'principiante';
    return 'explorador';
}

/**
 * Shortcodes
 */
add_shortcode('cnmx_megafonos', function() {
    if (!is_user_logged_in()) return '';
    
    global $wpdb;
    $user_id = get_current_user_id();
    $meta = $wpdb->get_row($wpdb->prepare(
        "SELECT megafonos FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d",
        $user_id
    ));
    
    $megafonos = $meta ? $meta->megafonos : 0;
    
    return '<span class="megafonos-badge"><span>📣</span><span>' . $megafonos . '</span></span>';
});

add_shortcode('cnmx_directorio', function() {
    ob_start();
    include CNMX_PATH . '/template-parts/directorio-loop.php';
    return ob_get_clean();
});

/**
 * Custom Login/Registration
 */
add_action('wp_ajax_cnmx_register', 'cnmx_ajax_register');
add_action('wp_ajax_nopriv_cnmx_register', 'cnmx_ajax_register');

function cnmx_ajax_register() {
    check_ajax_referer('cnmx_nonce');
    
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $name = sanitize_text_field($_POST['name']);
    
    if (email_exists($email)) {
        wp_send_json_error('El email ya está registrado');
    }
    
    $user_id = wp_create_user($email, $password, $email);
    
    if (is_wp_error($user_id)) {
        wp_send_json_error('Error al crear la cuenta');
    }
    
    wp_update_user(array(
        'ID' => $user_id,
        'display_name' => $name,
        'role' => 'subscriber',
    ));
    
    // Create user meta
    global $wpdb;
    $wpdb->insert($wpdb->prefix . 'cnmx_usuarios_meta', array(
        'user_id' => $user_id,
        'megafonos' => 50, // Bonus de registro
        'nivel' => 'explorador',
    ));
    
    // Log in user
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    
    wp_send_json_success('Cuenta creada exitosamente');
}

add_action('wp_ajax_cnmx_login', 'cnmx_ajax_login');
add_action('wp_ajax_nopriv_cnmx_login', 'cnmx_ajax_login');

function cnmx_ajax_login() {
    check_ajax_referer('cnmx_nonce');
    
    $credentials = array(
        'user_login' => sanitize_text_field($_POST['username']),
        'user_password' => $_POST['password'],
        'remember' => isset($_POST['remember']),
    );
    
    $user = wp_signon($credentials);
    
    if (is_wp_error($user)) {
        wp_send_json_error('Credenciales incorrectas');
    }
    
    wp_send_json_success('Sesión iniciada');
}

/**
 * AJAX: Get Featured Businesses
 */
add_action('wp_ajax_cnmx_get_featured_businesses', 'cnmx_get_featured_businesses');
add_action('wp_ajax_nopriv_cnmx_get_featured_businesses', 'cnmx_get_featured_businesses');

function cnmx_get_featured_businesses() {
    $negocios = get_posts(array(
        'post_type' => 'negocio',
        'post_status' => 'publish',
        'posts_per_page' => 8,
    ));
    
    $is_logged_in = is_user_logged_in();
    $user_id = get_current_user_id();
    
    ob_start();
    
    if (!empty($negocios)):
        foreach ($negocios as $biz):
            $rating = get_post_meta($biz->ID, 'cnmx_rating', true) ?: 0;
            $reviews = get_post_meta($biz->ID, 'cnmx_reviews_count', true) ?: 0;
            $direccion = get_post_meta($biz->ID, 'cnmx_direccion', true) ?: '';
            $imagen = get_the_post_thumbnail_url($biz->ID, 'cnmx-card') ?: 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=600&h=400&fit=crop';
            $cats = get_the_terms($biz->ID, 'categoria');
            $categoria = $cats ? $cats[0]->name : 'General';
            
            $is_fav = false;
            if ($is_logged_in) {
                global $wpdb;
                $fav = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$wpdb->prefix}cnmx_favoritos WHERE user_id = %d AND negocio_id = %d",
                    $user_id, $biz->ID
                ));
                $is_fav = (bool)$fav;
            }
    ?>
        <a href="<?php echo get_permalink($biz->ID); ?>" class="business-card" data-negocio-id="<?php echo $biz->ID; ?>">
            <div class="business-card-img">
                <img src="<?php echo esc_url($imagen); ?>" alt="<?php echo esc_attr($biz->post_title); ?>">
                <button class="business-card-fav <?php echo $is_fav ? 'active' : ''; ?>" data-id="<?php echo $biz->ID; ?>">
                    <svg viewBox="0 0 24 24" fill="<?php echo $is_fav ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </button>
            </div>
            <div class="business-card-content">
                <span class="business-card-cat"><?php echo esc_html($categoria); ?></span>
                <h3 class="business-card-title"><?php echo esc_html($biz->post_title); ?></h3>
                <div class="business-card-rating">
                    <span class="business-card-stars">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <span class="<?php echo $i < floor($rating) ? '' : 'empty'; ?>">★</span>
                        <?php endfor; ?>
                    </span>
                    <span class="business-card-rating-num"><?php echo number_format($rating, 1); ?></span>
                    <span class="business-card-reviews">(<?php echo $reviews; ?>)</span>
                </div>
                <?php if ($direccion): ?>
                <div class="business-card-location">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span><?php echo esc_html($direccion); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </a>
    <?php 
        endforeach;
    else:
    ?>
        <div class="empty-state">
            <div class="empty-state-icon">🏪</div>
            <h3>No hay negocios aún</h3>
            <p>¡Sé el primero en registrar un negocio!</p>
        </div>
    <?php
    endif;
    
    $html = ob_get_clean();
    
    echo $html;
    wp_die();
}
