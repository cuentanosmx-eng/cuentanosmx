<?php
/**
 * Custom Footer for Cuentanos
 */

if (!defined('ABSPATH')) exit;
?>
</main>

<!-- FOOTER -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-brand">
                <a href="<?php echo home_url(); ?>" class="footer-logo">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                    </svg>
                    Cuentanos.mx
                </a>
                <p>Descubre los mejores negocios locales en México.</p>
            </div>
            <div class="footer-col">
                <h4>Explorar</h4>
                <a href="<?php echo home_url('/directorio'); ?>">Directorio</a>
                <a href="<?php echo home_url('/directorio?categoria=restaurantes'); ?>">Restaurantes</a>
                <a href="<?php echo home_url('/directorio?categoria=hoteles'); ?>">Hoteles</a>
            </div>
            <div class="footer-col">
                <h4>Negocios</h4>
                <a href="<?php echo home_url('/registrar-negocio'); ?>">Registrar negocio</a>
                <a href="<?php echo home_url('/dashboard-empresa'); ?>">Acceso negocios</a>
            </div>
            <div class="footer-col">
                <h4>Cuenta</h4>
                <a href="<?php echo home_url('/registro'); ?>">Registrarse</a>
                <a href="<?php echo home_url('/mi-cuenta'); ?>">Iniciar sesión</a>
                <a href="<?php echo home_url('/perfil'); ?>">Mi perfil</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Cuentanos.mx. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
