<?php
/**
 * Plugin Name: Cuentanos Admin
 * Description: Panel de administración completo para Cuentanos.mx - Compatible con Elementor
 * Version: 1.0.0
 * Author: Cuentanos Team
 */

if (!defined('ABSPATH')) exit;

define('CNMX_ADMIN_VERSION', '1.0.0');
define('CNMX_ADMIN_PATH', plugin_dir_path(__FILE__));
define('CNMX_ADMIN_URL', plugin_dir_url(__FILE__));

require_once CNMX_ADMIN_PATH . 'includes/class-cnmx-admin-menu.php';
require_once CNMX_ADMIN_PATH . 'includes/class-cnmx-admin-dashboard.php';
require_once CNMX_ADMIN_PATH . 'includes/class-cnmx-admin-negocios.php';
require_once CNMX_ADMIN_PATH . 'includes/class-cnmx-admin-rest-api.php';

new CNMX_Admin_Menu();
new CNMX_Admin_Dashboard();
new CNMX_Admin_Negocios();
