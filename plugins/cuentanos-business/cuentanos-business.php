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