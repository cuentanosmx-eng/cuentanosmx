<?php
/**
 * Front Page Template - Custom Home
 */

if (!defined('ABSPATH')) exit;
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
$categories = get_terms(array(
    'taxonomy' => 'categoria',
    'hide_empty' => true,
    'number' => 8,
));

$user_megafonos = 0;
if (is_user_logged_in()) {
    global $wpdb;
    $meta = $wpdb->get_row($wpdb->prepare(
        "SELECT megafonos FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d",
        get_current_user_id()
    ));
    $user_megafonos = $meta ? $meta->megafonos : 0;
}
?>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="container">
        <a href="<?php echo home_url(); ?>" class="navbar-logo">
            <img src="https://cuentanos.mx/wp-content/uploads/2026/04/LOGO-PRINCIPAL.png" alt="Cuentanos.mx" class="logo-img">
        </a>
        
        <div class="navbar-center">
            <div class="dropdown">
                <button class="dropdown-toggle" onclick="toggleDropdown()">
                    Cuéntanos para empresa
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="<?php echo home_url('/registrar-negocio'); ?>" class="dropdown-item">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        Agregar una empresa
                    </a>
                    <a href="<?php echo home_url('/login-empresa'); ?>" class="dropdown-item">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Iniciar sesión como empresa
                    </a>
                </div>
            </div>
        </div>
        
        <div class="navbar-right">
            <?php if (is_user_logged_in()): ?>
                <span class="megafonos-badge"><span>📣</span><span><?php echo $user_megafonos; ?></span></span>
                <div class="user-dropdown">
                    <button class="user-dropdown-btn" onclick="toggleUserDropdown()">
                        <?php echo get_avatar(get_current_user_id(), 32); ?>
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="user-dropdown-menu" id="user-dropdown-menu">
                        <a href="<?php echo home_url('/perfil'); ?>" class="user-dropdown-item">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Mi Perfil
                        </a>
                        <a href="<?php echo home_url('/mis-favoritos'); ?>" class="user-dropdown-item">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            Mis Favoritos
                        </a>
                        <a href="<?php echo home_url('/negocio'); ?>" class="user-dropdown-item">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Explorar Negocios
                        </a>
                        <div class="user-dropdown-divider"></div>
                        <a href="<?php echo wp_logout_url(home_url()); ?>" class="user-dropdown-item logout">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo home_url('/mi-cuenta'); ?>" class="btn-nav">Iniciar sesión</a>
                <a href="<?php echo home_url('/registro'); ?>" class="btn-nav btn-nav-primary">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
.user-dropdown {
    position: relative;
}

.user-dropdown-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    border-radius: 24px;
    transition: background 0.2s;
}

.user-dropdown-btn:hover {
    background: rgba(0,0,0,0.05);
}

.user-dropdown-btn img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.user-dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 8px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    min-width: 220px;
    padding: 8px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.2s ease;
    z-index: 1000;
}

.user-dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.user-dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 8px;
    color: #1a1a1a;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: background 0.2s;
}

.user-dropdown-item:hover {
    background: #f7f7f7;
}

.user-dropdown-item.logout {
    color: #dc2626;
}

.user-dropdown-item.logout:hover {
    background: #fef2f2;
}

.user-dropdown-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 8px 0;
}
</style>

<script>
function toggleUserDropdown() {
    const menu = document.getElementById('user-dropdown-menu');
    menu.classList.toggle('show');
}

document.addEventListener('click', function(e) {
    const dropdown = document.querySelector('.user-dropdown');
    const menu = document.getElementById('user-dropdown-menu');
    if (dropdown && !dropdown.contains(e.target)) {
        menu.classList.remove('show');
    }
});
</script>

<script>
function toggleDropdown() {
    const menu = document.getElementById('dropdown-menu');
    menu.classList.toggle('show');
}

document.addEventListener('click', function(e) {
    const dropdown = document.querySelector('.dropdown');
    const menu = document.getElementById('dropdown-menu');
    if (!dropdown.contains(e.target)) {
        menu.classList.remove('show');
    }
});
</script>

<main>
    <!-- HERO + SEARCH WRAPPER (search SUPERPUESTO sobre hero) -->
    <div class="hero-wrapper">
        <!-- HERO CAROUSEL -->
        <?php echo do_shortcode('[cnmx_hero]'); ?>
        
        <!-- Search Box SUPERPUESTO sobre el hero, debajo de barras de progreso -->
        <div class="hero-search-box">
            <div class="container">
                <div class="home-search-box">
                    <form class="home-search-form" action="<?php echo home_url('/directorio'); ?>" method="GET">
                        <div class="home-search-field">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text" name="q" placeholder="Buscar restaurantes, cafés, hoteles...">
                        </div>
                        <div class="home-search-field">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <select name="categoria">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="home-search-btn">Buscar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MEGAFONOS BANNER -->
    <section class="megafonos-banner">
        <div class="container">
            <div class="megafonos-banner-inner">
                <span class="megafonos-banner-icon">📣</span>
                <div class="megafonos-banner-text">
                    <h3>¡Únete a Cuentanos.mx!</h3>
                    <p>Gana Megáfonos con cada reseña y favorito. Cánjealos por recompensas exclusivas.</p>
                </div>
                <?php if (!is_user_logged_in()): ?>
                    <a href="<?php echo home_url('/registro'); ?>" class="btn btn-white btn-lg">Regístrate gratis</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- ANUNCIOS SLIDER -->
    <section class="anuncios-section">
        <div class="container">
            <?php echo do_shortcode('[cnmx_anuncios]'); ?>
        </div>
    </section>
    
    <!-- ACTIVIDAD RECIENTE -->
    <?php echo do_shortcode('[cnmx_actividad_reciente num="12"]'); ?>
    
    <!-- FEATURED BUSINESSES -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Negocios Destacados</h2>
                <a href="<?php echo home_url('/directorio'); ?>" class="section-link">Ver todos →</a>
            </div>
            
            <div class="businesses-grid" id="businesses-grid">
                <!-- Cargado via AJAX -->
                <div class="loading-spinner">Cargando...</div>
            </div>
        </div>
    </section>
    
    <!-- CATEGORIAS -->
    <?php echo do_shortcode('[cnmx_categorias num="8"]'); ?>

    <!-- CTA SECTION -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>¿Tienes un negocio?</h2>
                <p>Llega a miles de clientes locales y muestra lo mejor de tu empresa en Cuentanos.mx</p>
                <div class="cta-buttons">
                    <a href="<?php echo home_url('/registrar-negocio'); ?>" class="btn btn-white btn-lg">Registrar mi negocio</a>
                    <a href="<?php echo home_url('/directorio'); ?>" class="btn btn-lg" style="background: rgba(255,255,255,0.15); color: white; border: 2px solid rgba(255,255,255,0.3);">Explorar directorio</a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
/* Hero Wrapper */
.hero-wrapper {
    position: relative;
    padding-top: 70px;
}

/* Search Box - Superpuesto sobre el hero, debajo de las barras de progreso */
.hero-search-box {
    position: absolute;
    bottom: -80px;
    left: 0;
    right: 0;
    z-index: 100;
    padding: 0 24px;
    animation: slideUpSearch 0.8s ease-out 0.3s both;
}

@keyframes slideUpSearch {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.home-search-box {
    background: #fff;
    border-radius: 24px;
    padding: 10px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25), 0 8px 20px rgba(0,0,0,0.1);
    max-width: 900px;
    margin: 0 auto;
}

.home-search-form {
    display: flex;
    gap: 8px;
    align-items: center;
}

.home-search-field {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 16px;
    background: #f8f8f8;
    transition: all 0.2s ease;
}

.home-search-field:hover {
    background: #f0f0f0;
}

.home-search-field svg {
    color: #666;
    flex-shrink: 0;
}

.home-search-field input,
.home-search-field select {
    flex: 1;
    border: none;
    outline: none;
    font-size: 15px;
    background: transparent;
    color: #333;
}

.home-search-field input::placeholder {
    color: #999;
}

.home-search-btn {
    background: #EB510C;
    color: #fff;
    border: none;
    padding: 14px 28px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    border-radius: 16px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.home-search-btn:hover {
    background: #C94409;
    transform: scale(1.02);
}

.loading-spinner {
    text-align: center;
    padding: 60px;
    color: #999;
    grid-column: 1 / -1;
}

@media (max-width: 768px) {
    .hero-wrapper {
        padding-top: 70px;
    }
    
    .hero-search-box {
        position: relative;
        top: auto;
        padding: 16px;
        margin-top: -40px;
        transform: none;
    }
    
    .home-search-form {
        flex-direction: column;
        gap: 8px;
    }
    
    .home-search-field {
        width: 100%;
    }
    
    .home-search-btn {
        width: 100%;
        justify-content: center;
        padding: 16px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load businesses via AJAX
    fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=cnmx_get_featured_businesses')
        .then(response => response.text())
        .then(html => {
            document.getElementById('businesses-grid').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('businesses-grid').innerHTML = '<p class="loading-spinner">Error al cargar negocios</p>';
        });
});
</script>

<!-- FOOTER -->
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
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Explorar</h4>
                    <ul>
                        <li><a href="<?php echo home_url('/directorio'); ?>">Directorio Completo</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=restaurantes'); ?>">Restaurantes</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=hoteles'); ?>">Hoteles</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=cafeterias'); ?>">Cafeterías</a></li>
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
                        <li><a href="<?php echo home_url('/registro'); ?>">Crear Cuenta</a></li>
                        <li><a href="<?php echo home_url('/mi-cuenta'); ?>">Iniciar Sesión</a></li>
                        <li><a href="<?php echo home_url('/perfil'); ?>">Mi Perfil</a></li>
                        <li><a href="<?php echo home_url('/mis-favoritos'); ?>">Mis Favoritos</a></li>
                        <li><a href="<?php echo home_url('/mis-resenas'); ?>">Mis Reseñas</a></li>
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
    background: #EB510C;
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
    color: #EB510C;
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
    border-radius: 8px;
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
    background: #EB510C;
    border: none;
    border-radius: 8px;
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
    color: #EB510C;
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
