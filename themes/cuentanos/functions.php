<?php
/**
 * Cuentanos MX - Theme Functions
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

define('CNMX_VERSION', '1.0.0');
define('CNMX_PATH', get_stylesheet_directory());
define('CNMX_URL', get_stylesheet_directory_uri());

// Quitar barra de admin de WordPress para todos excepto admins
add_filter('show_admin_bar', function($show) {
    if (current_user_can('administrator')) return true;
    return false;
});

// NO forzar templates de usuario del tema - el plugin maneja esto
// add_filter('template_include', function($template) {
//     global $post;
//     
//     if (!$post || $post->post_type !== 'page') {
//         return $template;
//     }
//     
//     $custom_pages = array(
//         'perfil' => 'page-perfil.php',
//         'mi-cuenta' => 'page-mi-cuenta.php',
//         'registro' => 'page-registro.php',
//         'recuperar-contrasena' => 'page-recuperar-contrasena.php',
//         'nueva-contrasena' => 'page-nueva-contrasena.php',
//         'mis-favoritos' => 'page-mis-favoritos.php',
//     );
//     
//     $slug = $post->post_name;
//     
//     if (isset($custom_pages[$slug])) {
//         $custom = get_stylesheet_directory() . '/' . $custom_pages[$slug];
//         if (file_exists($custom)) {
//             // Capturar output del template custom
//             ob_start();
//             include $custom;
//             $content = ob_get_clean();
//             
//             // Reemplazar el contenido de la página
//             add_filter('the_content', function($c) use ($content) {
//                 return $content;
//             }, 999);
//         }
//     }
//     
//     return $template;
// }, 999);

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
        
        // Popups CSS & JS
        wp_enqueue_style(
            'cnmx-popups',
            CNMX_URL . '/css/popups.css',
            array(),
            CNMX_VERSION
        );
        wp_enqueue_script(
            'cnmx-popups',
            CNMX_URL . '/js/popups.js',
            array(),
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
 * Meta Boxes para Negocios
 */
function cnmx_add_negocio_meta_boxes() {
    add_meta_box(
        'cnmx_negocio_plan',
        '⭐ Plan de Negocio',
        'cnmx_negocio_plan_callback',
        'negocio',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'cnmx_add_negocio_meta_boxes');

function cnmx_negocio_plan_callback($post) {
    wp_nonce_field('cnmx_negocio_plan', 'cnmx_negocio_plan_nonce');
    
    $destacado = get_post_meta($post->ID, 'cnmx_destacado', true);
    $prioridad = get_post_meta($post->ID, 'cnmx_prioridad', true);
    $anuncio_activo = get_post_meta($post->ID, 'cnmx_anuncio_activo', true);
    ?>
    <style>
        .cnmx-plan-metabox label {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
        }
        .cnmx-plan-metabox label:hover {
            background: #e9ecef;
            border-color: #EB510C;
        }
        .cnmx-plan-metabox label.active {
            background: #FFF3ED;
            border-color: #EB510C;
        }
        .cnmx-plan-metabox input[type="checkbox"] {
            margin-top: 4px;
            width: 20px;
            height: 20px;
        }
        .cnmx-plan-metabox .plan-info h4 {
            margin: 0 0 4px 0;
            font-size: 14px;
        }
        .cnmx-plan-metabox .plan-info p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }
        .cnmx-plan-metabox .plan-icon {
            font-size: 20px;
        }
    </style>
    <div class="cnmx-plan-metabox">
        <label class="<?php echo $destacado === 'si' ? 'active' : ''; ?>">
            <input type="checkbox" name="cnmx_destacado" value="si" <?php checked($destacado, 'si'); ?>>
            <div class="plan-info">
                <h4>⭐ Negocio Destacado</h4>
                <p>Aparece en la sección principal</p>
            </div>
        </label>
        <label class="<?php echo $prioridad === 'si' ? 'active' : ''; ?>">
            <input type="checkbox" name="cnmx_prioridad" value="si" <?php checked($prioridad, 'si'); ?>>
            <div class="plan-info">
                <h4>🚀 Mayor Prioridad</h4>
                <p>Aparece primero en búsquedas</p>
            </div>
        </label>
        <label class="<?php echo $anuncio_activo === 'si' ? 'active' : ''; ?>">
            <input type="checkbox" name="cnmx_anuncio_activo" value="si" <?php checked($anuncio_activo, 'si'); ?>>
            <div class="plan-info">
                <h4>🎯 Anuncio Activo</h4>
                <p>Aparece en el slider de anuncios</p>
            </div>
        </label>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('.cnmx-plan-metabox label input').on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).closest('label').addClass('active');
                } else {
                    $(this).closest('label').removeClass('active');
                }
            });
        });
    </script>
    <?php
}

function cnmx_save_negocio_plan($post_id) {
    if (!isset($_POST['cnmx_negocio_plan_nonce']) || !wp_verify_nonce($_POST['cnmx_negocio_plan_nonce'], 'cnmx_negocio_plan')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    $campos = ['cnmx_destacado', 'cnmx_prioridad', 'cnmx_anuncio_activo'];
    foreach ($campos as $campo) {
        if (isset($_POST[$campo])) {
            update_post_meta($post_id, $campo, 'si');
        } else {
            update_post_meta($post_id, $campo, 'no');
        }
    }
}
add_action('save_post_negocio', 'cnmx_save_negocio_plan');

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
        'show_ui' => true,
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
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cnmx_nonce')) {
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'cnmx_nonce')) {
            wp_send_json_error('Nonce inválido');
            return;
        }
    }
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Debes iniciar sesión');
        return;
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

// Handler para compartir negocio
add_action('wp_ajax_cnmx_compartir', 'cnmx_ajax_compartir');
add_action('wp_ajax_nopriv_cnmx_compartir', function() {
    wp_send_json_error('Debes iniciar sesión');
});

function cnmx_ajax_compartir() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cnmx_nonce')) {
        wp_send_json_error('Nonce inválido');
        return;
    }
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Debes iniciar sesión');
        return;
    }
    
    $user_id = get_current_user_id();
    $negocio_id = intval($_POST['negocio_id']);
    
    // Dar Megáfonos por compartir (una vez por día)
    $hoy = date('Y-m-d');
    $ya_compartio = get_user_meta($user_id, 'cnmx_ultimo_compartir_' . $negocio_id, true);
    
    if ($ya_compartio !== $hoy) {
        $megafonos_compartir = get_option('cnmx_megafonos_compartir', 3);
        cnmx_add_megafonos($user_id, $megafonos_compartir, 'Compartiste un negocio');
        update_user_meta($user_id, 'cnmx_ultimo_compartir_' . $negocio_id, $hoy);
        wp_send_json_success(['message' => 'Megáfonos ganados', 'megafonos' => $megafonos_compartir]);
    } else {
        wp_send_json_success(['message' => 'Ya ganaste Megáfonos por compartir hoy']);
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
    $name = sanitize_text_field($_POST['nombre'] ?? $_POST['name'] ?? 'Usuario');
    
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
    
    wp_send_json_success(['redirect' => home_url('/perfil')]);
}

add_action('wp_ajax_cnmx_login', 'cnmx_ajax_login');
add_action('wp_ajax_nopriv_cnmx_login', 'cnmx_ajax_login');

function cnmx_ajax_login() {
    $email = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        wp_send_json_error(['message' => 'Email y contraseña son requeridos']);
    }
    
    $user = get_user_by('email', $email);
    
    if (!$user) {
        wp_send_json_error(['message' => 'No existe una cuenta con ese email']);
    }
    
    $auth = wp_authenticate($user->user_login, $password);
    
    if (is_wp_error($auth)) {
        wp_send_json_error(['message' => 'Contraseña incorrecta']);
    }
    
    wp_set_current_user($auth->ID);
    wp_set_auth_cookie($auth->ID);
    
    $redirect = home_url('/perfil');
    wp_send_json_success(['redirect' => $redirect]);
}

/**
 * AJAX: Get Featured Businesses (Destacados)
 */
add_action('wp_ajax_cnmx_get_featured_businesses', 'cnmx_get_featured_businesses');
add_action('wp_ajax_nopriv_cnmx_get_featured_businesses', 'cnmx_get_featured_businesses');

function cnmx_get_featured_businesses() {
    $negocios = get_posts(array(
        'post_type' => 'negocio',
        'post_status' => 'publish',
        'posts_per_page' => 8,
        'orderby' => 'rand',
        'meta_query' => array(
            array(
                'key' => 'cnmx_destacado',
                'value' => 'si',
            )
        ),
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
            <h3>No hay negocios destacados</h3>
            <p>¡Explora todas nuestras categorías!</p>
        </div>
    <?php
    endif;
    
    $html = ob_get_clean();
    
    echo $html;
    wp_die();
}

/**
 * Shortcode: Categorías con iconos SVG
 */
add_shortcode('cnmx_categorias', 'cnmx_categorias_shortcode');

function cnmx_categorias_shortcode($atts) {
    $atts = shortcode_atts(array(
        'num' => 12,
    ), $atts);
    
    $categorias = get_terms(array(
        'taxonomy' => 'categoria',
        'hide_empty' => true,
        'number' => intval($atts['num']),
    ));
    
    if (empty($categorias) || is_wp_error($categorias)) {
        return '';
    }
    
    $iconos = array(
        'restaurantes' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/></svg>',
        'hoteles' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/></svg>',
        'cafes' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>',
        'tiendas' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/><path d="M12 3v6"/></svg>',
        'bares' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 22h8"/><path d="M12 11v11"/><path d="m19 3-7 8-7-8Z"/></svg>',
        'gimnasios' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6.5 6.5h11"/><path d="M6.5 17.5h11"/><path d="M2 12h4"/><path d="M18 12h4"/><path d="M6.5 6.5v11"/><path d="M17.5 6.5v11"/></svg>',
        'spa' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22c-4-3-8-6-8-11a8 8 0 0 1 16 0c0 5-4 8-8 11Z"/><path d="M12 8a3 3 0 1 0 0 6"/><path d="M12 6v2"/></svg>',
        'medicos' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/></svg>',
        'belleza' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v.01"/><path d="M8 12h8"/></svg>',
        'automotriz' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17h14v-5l-2-4H7l-2 4v5Z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>',
        'educacion' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
        'entretenimiento' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"/><polyline points="17 2 12 7 7 2"/></svg>',
        'mascotas' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="20" cy="16" r="2"/><path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"/></svg>',
        'servicios' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
        'default' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>',
    );
    
    $html = '<section class="categorias-section">';
    $html .= '<div class="container">';
    $html .= '<div class="section-header">';
    $html .= '<h2 class="section-title">Explora por Categoría</h2>';
    $html .= '<a href="' . home_url('/directorio') . '" class="section-link">Ver todas →</a>';
    $html .= '</div>';
    $html .= '<div class="categorias-grid">';
    
    foreach ($categorias as $cat) {
        $slug = sanitize_title($cat->name);
        $icono = isset($iconos[$slug]) ? $iconos[$slug] : $iconos['default'];
        
        $html .= '<a href="' . home_url('/directorio?categoria=' . $cat->slug) . '" class="categoria-card">';
        $html .= '<div class="categoria-icono">' . $icono . '</div>';
        $html .= '<span class="categoria-nombre">' . esc_html($cat->name) . '</span>';
        $html .= '<span class="categoria-count">' . $cat->count . ' lugares</span>';
        $html .= '</a>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</section>';
    
    return $html;
}


