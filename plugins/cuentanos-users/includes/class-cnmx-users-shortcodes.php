<?php
/**
 * Users Shortcodes
 */

if (!defined('ABSPATH')) exit;

class CNMX_Users_Shortcodes {
    
    public function __construct() {
        add_shortcode('cnmx_user_megafonos', [$this, 'shortcode_megafonos']);
        add_shortcode('cnmx_user_profile_btn', [$this, 'shortcode_profile_btn']);
        add_shortcode('cnmx_login_form', [$this, 'shortcode_login_form']);
        add_shortcode('cnmx_register_form', [$this, 'shortcode_register_form']);
    }
    
    public function shortcode_megafonos($atts) {
        if (!is_user_logged_in()) {
            return '<a href="' . home_url('/registro') . '" class="btn btn-secondary">Regístrate para ganar</a>';
        }
        
        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'cnmx_usuarios_meta';
        
        $megafonos = $wpdb->get_var($wpdb->prepare(
            "SELECT meta_value FROM {$table} WHERE user_id = %d AND meta_key = 'megafonos'",
            $user_id
        ));
        
        $megafonos = intval($megafonos) ?: 0;
        
        $nivel = 'explorador';
        if ($megafonos >= 1000) $nivel = 'influencer';
        elseif ($megafonos >= 500) $nivel = 'critico';
        
        ob_start(); ?>
        <div class="cnmx-megafonos-widget">
            <div class="cnmx-megafonos-header">
                <span class="cnmx-megafonos-icon">🎤</span>
                <div class="cnmx-megafonos-info">
                    <div class="cnmx-nivel"><?php echo ucfirst($nivel); ?></div>
                    <div class="cnmx-puntos"><?php echo number_format($megafonos); ?></div>
                </div>
            </div>
            <div class="cnmx-megafonos-progress">
                <div class="cnmx-progress-bar">
                    <?php 
                    $next = $megafonos >= 1000 ? 2000 : ($megafonos >= 500 ? 1000 : 500);
                    $progress = ($megafonos / $next) * 100;
                    ?>
                    <div class="cnmx-progress-fill" style="width: <?php echo $progress; ?>%"></div>
                </div>
                <div class="cnmx-progress-text">
                    <?php if ($megafonos >= 1000): ?>
                        ¡Eres un Influencer! 🎉
                    <?php else: ?>
                        <?php echo number_format($next - $megafonos); ?> puntos para el siguiente nivel
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function shortcode_profile_btn($atts) {
        $atts = shortcode_atts([
            'texto' => 'Mi Cuenta',
        ], $atts);
        
        if (is_user_logged_in()) {
            return '<a href="' . home_url('/mi-cuenta') . '" class="btn btn-primary">' . esc_html($atts['texto']) . '</a>';
        }
        
        return '<a href="' . home_url('/registro') . '" class="btn btn-primary">' . esc_html($atts['texto']) . '</a>';
    }
    
    public function shortcode_login_form($atts) {
        ob_start(); ?>
        <div class="cnmx-login-form-shortcode">
            <h3>Iniciar Sesión</h3>
            <form id="cnmx-login-shortcode-form">
                <div class="cnmx-form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="tu@email.com">
                </div>
                <div class="cnmx-form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                <p class="cnmx-form-link">
                    ¿No tienes cuenta? <a href="<?php echo home_url('/registro'); ?>">Regístrate</a>
                </p>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function shortcode_register_form($atts) {
        ob_start(); ?>
        <div class="cnmx-register-form-shortcode">
            <h3>Crear Cuenta</h3>
            <form id="cnmx-register-shortcode-form">
                <div class="cnmx-form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" required placeholder="Tu nombre">
                </div>
                <div class="cnmx-form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="tu@email.com">
                </div>
                <div class="cnmx-form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
                </div>
                <button type="submit" class="btn btn-primary">Crear Cuenta</button>
                <p class="cnmx-form-link">
                    ¿Ya tienes cuenta? <a href="<?php echo home_url('/mi-cuenta'); ?>">Inicia sesión</a>
                </p>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}

new CNMX_Users_Shortcodes();
