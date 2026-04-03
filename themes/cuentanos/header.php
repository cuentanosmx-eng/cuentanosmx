<?php
/**
 * Custom Header for Cuentanos
 */

if (!defined('ABSPATH')) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav class="navbar">
    <div class="container">
        <a href="<?php echo home_url(); ?>" class="navbar-logo">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
            </svg>
            Cuentanos.mx
        </a>
        <div class="navbar-nav">
            <a href="<?php echo home_url(); ?>" class="navbar-link <?php echo is_front_page() ? 'active' : ''; ?>">Inicio</a>
            <a href="<?php echo home_url('/directorio'); ?>" class="navbar-link <?php echo is_page('directorio') ? 'active' : ''; ?>">Explorar</a>
        </div>
        <div class="navbar-actions">
            <?php 
            $user_megafonos = 0;
            if (is_user_logged_in()) {
                global $wpdb;
                $meta = $wpdb->get_row($wpdb->prepare(
                    "SELECT megafonos FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d",
                    get_current_user_id()
                ));
                $user_megafonos = $meta ? $meta->megafonos : 0;
            ?>
                <span class="megafonos-badge"><span>📣</span><span><?php echo $user_megafonos; ?></span></span>
                <a href="<?php echo home_url('/perfil'); ?>" class="btn btn-outline">Mi Perfil</a>
            <?php } else { ?>
                <a href="<?php echo home_url('/mi-cuenta'); ?>" class="btn btn-outline">Iniciar sesión</a>
                <a href="<?php echo home_url('/registro'); ?>" class="btn btn-primary">Registrarse</a>
            <?php } ?>
        </div>
    </div>
</nav>

<main>
