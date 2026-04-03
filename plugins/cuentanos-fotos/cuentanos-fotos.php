<?php
/**
 * Plugin Name: Cuentanos Fotos
 * Description: Sistema de galerías de fotos para negocios - Compatible con Elementor
 * Version: 1.0.0
 * Author: Cuentanos Team
 */

if (!defined('ABSPATH')) exit;

define('CNMX_FOTOS_VERSION', '1.0.0');
define('CNMX_FOTOS_PATH', plugin_dir_path(__FILE__));
define('CNMX_FOTOS_URL', plugin_dir_url(__FILE__));

require_once CNMX_FOTOS_PATH . 'includes/class-cnmx-fotos-setup.php';
require_once CNMX_FOTOS_PATH . 'includes/class-cnmx-fotos-rest-api.php';

new CNMX_Fotos_Setup();
new CNMX_Fotos_REST_API();
