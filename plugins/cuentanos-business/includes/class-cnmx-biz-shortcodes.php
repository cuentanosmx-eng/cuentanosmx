<?php
/**
 * CNMX Biz Shortcodes
 */

if (!defined('ABSPATH')) exit;

class CNMX_Biz_Shortcodes {
    
    public function __construct() {
        add_shortcode('cnmx_biz_login', [$this, 'shortcode_login']);
        add_shortcode('cnmx_biz_registro', [$this, 'shortcode_registro']);
        add_shortcode('cnmx_biz_dashboard', [$this, 'shortcode_dashboard']);
        add_shortcode('cnmx_biz_editar', [$this, 'shortcode_editar']);
    }
    
    public function shortcode_login($atts) {
        ob_start();
        ?>
        <div class="cnmx-biz-login-shortcode">
            <form id="cnmx-login-form" class="cnmx-auth-form">
                <div class="cnmx-form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="cnmx-form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function shortcode_registro($atts) {
        ob_start();
        ?>
        <div class="cnmx-biz-register-shortcode">
            <form id="cnmx-register-form" class="cnmx-auth-form">
                <div class="cnmx-form-group">
                    <label>Nombre del negocio *</label>
                    <input type="text" name="nombre_negocio" required>
                </div>
                <div class="cnmx-form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>
                <div class="cnmx-form-group">
                    <label>Contraseña *</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Crear Cuenta</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function shortcode_dashboard($atts) {
        if (!is_user_logged_in()) {
            return '<p class="cnmx-biz-aviso">Debes <a href="/login-empresa">iniciar sesión</a> para ver el dashboard</p>';
        }
        
        $negocio_id = get_user_meta(get_current_user_id(), 'cnmx_negocio_asociado', true);
        
        if (!$negocio_id) {
            return '<p class="cnmx-biz-aviso">No tienes un negocio asociado. <a href="/registrar-negocio">Registrar negocio</a></p>';
        }
        
        return '<div id="cnmx-dashboard-app"></div>';
    }
    
    public function shortcode_editar($atts) {
        if (!is_user_logged_in()) {
            return '<p class="cnmx-biz-aviso">Debes iniciar sesión para editar</p>';
        }
        
        return '<div id="cnmx-edit-negocio-app"></div>';
    }
}

new CNMX_Biz_Shortcodes();