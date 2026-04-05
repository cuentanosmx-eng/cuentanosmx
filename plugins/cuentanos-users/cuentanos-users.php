<?php
/**
 * Plugin Name: Cuentanos Users
 * Description: Sistema de registro, login y perfil para usuarios - Compatible con gamificación Megáfonos
 * Version: 1.0.0
 * Author: Cuentanos Team
 * Text Domain:cuentanos-users
 */

if (!defined('ABSPATH')) exit;

define('CNMX_USERS_VERSION', '1.0.3');
define('CNMX_USERS_PATH', plugin_dir_path(__FILE__));
define('CNMX_USERS_URL', plugin_dir_url(__FILE__));

require_once CNMX_USERS_PATH . 'includes/class-cnmx-users-setup.php';
require_once CNMX_USERS_PATH . 'includes/class-cnmx-users-rest-api.php';
require_once CNMX_USERS_PATH . 'includes/class-cnmx-users-profile.php';
require_once CNMX_USERS_PATH . 'includes/class-cnmx-users-shortcodes.php';

new CNMX_Users_Setup();
new CNMX_Users_Profile();
new CNMX_Users_REST_API();
new CNMX_Users_Shortcodes();

register_activation_hook(__FILE__, function() {
    CNMX_Users_Setup::activate();
});
