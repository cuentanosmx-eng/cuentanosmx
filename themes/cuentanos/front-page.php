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
            <img src="https://cuentanos.mx/wp-content/uploads/2026/04/LOGO-HORIZONTAL.png" alt="Cuentanos.mx" class="logo-img">
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
            <?php else: ?>
                <a href="<?php echo home_url('/mi-cuenta'); ?>" class="btn-nav">Iniciar sesión</a>
                <a href="<?php echo home_url('/registro'); ?>" class="btn-nav btn-nav-primary">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

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

<?php wp_footer(); ?>
</body>
</html>
