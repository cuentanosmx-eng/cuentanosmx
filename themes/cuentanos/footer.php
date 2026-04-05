<?php
/**
 * Custom Footer for Cuentanos
 */

if (!defined('ABSPATH')) exit;

$categorias = get_terms([
    'taxonomy' => 'categoria',
    'hide_empty' => true,
    'number' => 8,
    'orderby' => 'count',
    'order' => 'DESC',
]);
?>
</main>

<footer class="site-footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="<?php echo home_url(); ?>" class="footer-logo">
                        <img src="https://cuentanos.mx/wp-content/uploads/2026/04/LOGO-PRINCIPAL.png" alt="Cuentanos.mx" class="footer-logo-img">
                    </a>
                    <p class="footer-brand-desc">El directorio de negocios locales más grande de México. Descubre, evalúa y apoya a los negocios de tu comunidad.</p>
                    <div class="footer-social">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="#" class="social-link" aria-label="TikTok">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                        </a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Explorar</h4>
                    <ul>
                        <li><a href="<?php echo home_url('/directorio'); ?>">Directorio Completo</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=restaurantes'); ?>">Restaurantes</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=hoteles'); ?>">Hoteles</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=cafes'); ?>">Cafeterías</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=bares'); ?>">Bares y Cantinas</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Más Categorías</h4>
                    <ul>
                        <li><a href="<?php echo home_url('/directorio?categoria=gimnasios'); ?>">Gimnasios</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=spa'); ?>">Spa y Bienestar</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=tiendas'); ?>">Tiendas</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=educacion'); ?>">Educación</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=servicios'); ?>">Servicios</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Para Negocios</h4>
                    <ul>
                        <li><a href="<?php echo home_url('/registrar-negocio'); ?>">Registrar mi Negocio</a></li>
                        <li><a href="<?php echo home_url('/login-empresa'); ?>">Acceso Empresas</a></li>
                        <li><a href="<?php echo home_url('/dashboard-empresa'); ?>">Dashboard</a></li>
                        <li><a href="<?php echo home_url('/mi-negocio'); ?>">Editar mi Negocio</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Cuenta</h4>
                    <ul>
                        <?php if (is_user_logged_in()): ?>
                        <li><a href="<?php echo home_url('/perfil'); ?>">Mi Perfil</a></li>
                        <li><a href="<?php echo home_url('/mis-favoritos'); ?>">Mis Favoritos</a></li>
                        <li><a href="<?php echo home_url('/mis-resenas'); ?>">Mis Reseñas</a></li>
                        <li><a href="<?php echo wp_logout_url(home_url()); ?>">Cerrar Sesión</a></li>
                        <?php else: ?>
                        <li><a href="<?php echo home_url('/registro'); ?>">Crear Cuenta</a></li>
                        <li><a href="<?php echo home_url('/mi-cuenta'); ?>">Iniciar Sesión</a></li>
                        <li><a href="<?php echo home_url('/recuperar-contrasena'); ?>">Olvidé mi Contraseña</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-col footer-newsletter">
                    <h4>Newsletter</h4>
                    <p>Recibe las mejores recomendaciones cada semana.</p>
                    <form class="newsletter-form" onsubmit="event.preventDefault(); this.querySelector('button').textContent = '¡Suscrito!'; this.querySelector('input').value = '';">
                        <input type="email" placeholder="tu@email.com" required>
                        <button type="submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <p>&copy; <?php echo date('Y'); ?> <strong>Cuentanos.mx</strong>. Todos los derechos reservados.</p>
                <div class="footer-bottom-links">
                    <a href="#">Términos de Uso</a>
                    <a href="#">Política de Privacidad</a>
                    <a href="#">Contacto</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
.site-footer {
    margin-top: auto;
}

.footer-main {
    background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
    color: #fff;
    padding: 60px 0 40px;
}

.footer-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr 1.5fr;
    gap: 40px;
}

@media (max-width: 1200px) {
    .footer-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    .footer-brand {
        grid-column: 1 / -1;
    }
}

@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .footer-newsletter {
        grid-column: 1 / -1;
    }
}

@media (max-width: 480px) {
    .footer-grid {
        grid-template-columns: 1fr;
    }
}

.footer-logo-img {
    height: 40px;
    width: auto;
    margin-bottom: 16px;
}

.footer-brand-desc {
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 20px;
}

.footer-social {
    display: flex;
    gap: 12px;
}

.social-link {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.social-link svg {
    width: 18px;
    height: 18px;
    color: #fff;
}

.social-link:hover {
    background: var(--cnmx-primary, #EB510C);
    transform: translateY(-2px);
}

.footer-col h4 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #fff;
}

.footer-col ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-col ul li {
    margin-bottom: 12px;
}

.footer-col a {
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
}

.footer-col a:hover {
    color: var(--cnmx-primary, #EB510C);
}

.footer-newsletter p {
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    margin-bottom: 16px;
}

.newsletter-form {
    display: flex;
    gap: 8px;
}

.newsletter-form input {
    flex: 1;
    padding: 12px 16px;
    border: none;
    border-radius: var(--cnmx-radius-md, 8px);
    background: rgba(255,255,255,0.1);
    color: #fff;
    font-size: 14px;
}

.newsletter-form input::placeholder {
    color: rgba(255,255,255,0.5);
}

.newsletter-form input:focus {
    outline: none;
    background: rgba(255,255,255,0.15);
}

.newsletter-form button {
    padding: 12px 16px;
    background: var(--cnmx-primary, #EB510C);
    border: none;
    border-radius: var(--cnmx-radius-md, 8px);
    color: #fff;
    cursor: pointer;
    transition: background 0.3s;
}

.newsletter-form button:hover {
    background: #d4490b;
}

.newsletter-form button svg {
    width: 18px;
    height: 18px;
}

.footer-bottom {
    background: #0f0f23;
    padding: 20px 0;
}

.footer-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.footer-bottom p {
    color: rgba(255,255,255,0.6);
    font-size: 14px;
    margin: 0;
}

.footer-bottom strong {
    color: #fff;
}

.footer-bottom-links {
    display: flex;
    gap: 24px;
}

.footer-bottom-links a {
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
}

.footer-bottom-links a:hover {
    color: var(--cnmx-primary, #EB510C);
}

@media (max-width: 768px) {
    .footer-bottom-content {
        flex-direction: column;
        text-align: center;
    }
    .footer-bottom-links {
        justify-content: center;
    }
}
</style>

<?php wp_footer(); ?>
</body>
</html>
