<?php
/**
 * Plugin Name: Cuentanos Business
 * Description: Sistema de gestión para empresas - Dashboard, membresías, edición de negocio
 * Version: 1.0.0
 * Author: Cuentanos Team
 * Text Domain:cuentanos-business
 */

if (!defined('ABSPATH')) exit;

define('CNMX_BIZ_VERSION', '1.0.0');
define('CNMX_BIZ_PATH', plugin_dir_path(__FILE__));
define('CNMX_BIZ_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, 'cnmx_biz_activate');

function cnmx_biz_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $tabla_metricas = "CREATE TABLE {$wpdb->prefix}cnmx_metricas (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        negocio_id bigint(20) NOT NULL,
        tipo varchar(50) NOT NULL,
        fecha date NOT NULL,
        cantidad int(11) DEFAULT 0,
        PRIMARY KEY (id),
        KEY negocio_id (negocio_id),
        KEY fecha (fecha)
    ) $charset_collate;";
    
    dbDelta($tabla_metricas);
    
    update_option('cnmx_biz_version', CNMX_BIZ_VERSION);
}

require_once CNMX_BIZ_PATH . 'includes/class-cnmx-biz-setup.php';
require_once CNMX_BIZ_PATH . 'includes/class-cnmx-biz-dashboard.php';
require_once CNMX_BIZ_PATH . 'includes/class-cnmx-biz-membresia.php';
require_once CNMX_BIZ_PATH . 'includes/class-cnmx-biz-recompensas.php';
require_once CNMX_BIZ_PATH . 'includes/class-cnmx-biz-logros.php';
require_once CNMX_BIZ_PATH . 'includes/class-cnmx-biz-rest-api.php';
require_once CNMX_BIZ_PATH . 'includes/class-cnmx-biz-shortcodes.php';

new CNMX_Biz_Setup();
new CNMX_Biz_Logros();
new CNMX_Biz_Recompensas();
new CNMX_Biz_REST_API();
new CNMX_Biz_Shortcodes();

class CNMX_Biz_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_cnmx_crear_admin_directorio', [$this, 'crear_admin_directorio']);
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Cuentanos Admin',
            'Cuentanos Admin',
            'manage_options',
            'cnmx-admin',
            [$this, 'admin_page'],
            'dashicons-admin-tools',
            3
        );
    }
    
    public function admin_page() {
        $admin_existe = email_exists('admin@cuentanos.mx');
        ?>
        <div class="wrap">
            <h1>🔧 Cuentanos Admin Panel</h1>
            
            <hr>
            
            <h2>👤 Cuenta Admin del Directorio</h2>
            <p>Esta cuenta es para gestionar los planes de negocios desde el frontend.</p>
            
            <?php if ($admin_existe): ?>
                <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h3 style="color: #155724; margin-top: 0;">✓ Cuenta ya existe</h3>
                    <p><strong>Email:</strong> admin@cuentanos.mx</p>
                    <p><strong>ID de usuario:</strong> <?php echo $admin_existe; ?></p>
                    <p><a href="<?php echo home_url('/admin-directorio'); ?>" class="button button-primary">Ir al Panel</a></p>
                </div>
            <?php else: ?>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <input type="hidden" name="action" value="cnmx_crear_admin_directorio">
                    <?php wp_nonce_field('cnmx_crear_admin'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">Email</th>
                            <td>
                                <input type="email" name="email" value="admin@cuentanos.mx" class="regular-text" required>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Contraseña</th>
                            <td>
                                <input type="text" name="password" value="<?php echo wp_generate_password(16); ?>" class="regular-text" required>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Nombre</th>
                            <td>
                                <input type="text" name="nombre" value="Administrador" class="regular-text">
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="Crear Cuenta Admin">
                    </p>
                </form>
            <?php endif; ?>
            
            <hr>
            
            <h2>📊 Estadísticas Rápidas</h2>
            <?php
            $total_negocios = wp_count_posts('negocio');
            $negocios_pub = $total_negocios->publish ?? 0;
            $admin_exists = email_exists('admin@cuentanos.mx');
            ?>
            <ul>
                <li>Negocios publicados: <strong><?php echo $negocios_pub; ?></strong></li>
                <li>Cuenta Admin: <strong><?php echo $admin_exists ? 'Creada' : 'No creada'; ?></strong></li>
            </ul>
        </div>
        <?php
    }
    
    public function crear_admin_directorio() {
        check_admin_referer('cnmx_crear_admin');
        
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $nombre = sanitize_text_field($_POST['nombre']);
        
        if (email_exists($email)) {
            wp_die('Este email ya está registrado.');
        }
        
        $user_id = wp_create_user($email, $password, $email);
        
        if (is_wp_error($user_id)) {
            wp_die('Error al crear usuario: ' . $user_id->get_error_message());
        }
        
        wp_update_user([
            'ID' => $user_id,
            'display_name' => $nombre,
            'role' => 'admin_directorio',
        ]);
        
        update_user_meta($user_id, 'cnmx_tipo_cuenta', 'admin_directorio');
        
        wp_redirect(add_query_arg('created', 'true', wp_get_referer()));
        exit;
    }
}

new CNMX_Biz_Admin();