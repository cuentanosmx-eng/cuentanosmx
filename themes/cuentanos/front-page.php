<?php
/**
 * Front Page Template - Custom Home
 * Note: Includes its own header/footer, no get_header/get_footer needed
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

<?php
// Get businesses
$negocios = get_posts(array(
    'post_type' => 'negocio',
    'post_status' => 'publish',
    'posts_per_page' => 8,
));

// Get categories
$categories = get_terms(array(
    'taxonomy' => 'categoria',
    'hide_empty' => true,
    'number' => 6,
));

// Get user megafonos if logged in
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
            <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
            </svg>
            Cuentanos.mx
        </a>
        <div class="navbar-nav">
            <a href="<?php echo home_url(); ?>" class="navbar-link">Inicio</a>
            <a href="<?php echo home_url('/directorio'); ?>" class="navbar-link">Explorar</a>
        </div>
        <div class="navbar-actions">
            <?php if (is_user_logged_in()): ?>
                <span class="megafonos-badge"><span>📣</span><span><?php echo $user_megafonos; ?></span></span>
                <a href="<?php echo home_url('/perfil'); ?>" class="btn btn-outline">Mi Perfil</a>
            <?php else: ?>
                <a href="<?php echo home_url('/mi-cuenta'); ?>" class="btn btn-outline">Iniciar sesión</a>
                <a href="<?php echo home_url('/registro'); ?>" class="btn btn-primary">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main>
    <!-- HERO -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Descubre los mejores lugares cerca de ti</h1>
            <p class="hero-subtitle">Negocios locales recomendados por la comunidad</p>
            
            <!-- Search Box -->
            <div class="search-box">
                <form class="search-form" action="<?php echo home_url('/directorio'); ?>" method="GET">
                    <div class="search-field">
                        <label>¿Qué buscas?</label>
                        <input type="text" name="q" placeholder="Restaurantes, cafés, hoteles...">
                    </div>
                    <div class="search-field">
                        <label>Categoría</label>
                        <select name="categoria">
                            <option value="">Todas</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="search-btn">Buscar</button>
                </form>
            </div>
            
            <!-- Quick Categories -->
            <div class="quick-cats">
                <?php foreach ($categories as $cat): ?>
                    <a href="<?php echo home_url('/directorio?categoria=' . $cat->slug); ?>" class="quick-cat">
                        <span class="quick-cat-icon">📍</span>
                        <span><?php echo $cat->name; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

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

    <!-- FEATURED BUSINESSES -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Negocios Destacados</h2>
                <a href="<?php echo home_url('/directorio'); ?>" class="section-link">Ver todos →</a>
            </div>
            
            <div class="businesses-grid">
                <?php if (!empty($negocios)): ?>
                    <?php foreach ($negocios as $biz): 
                        $rating = get_post_meta($biz->ID, 'cnmx_rating', true) ?: 0;
                        $reviews = get_post_meta($biz->ID, 'cnmx_reviews_count', true) ?: 0;
                        $direccion = get_post_meta($biz->ID, 'cnmx_direccion', true) ?: '';
                        $imagen = get_the_post_thumbnail_url($biz->ID, 'cnmx-card') ?: 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=600&h=400&fit=crop';
                        $cats = get_the_terms($biz->ID, 'categoria');
                        $categoria = $cats ? $cats[0]->name : 'General';
                    ?>
                        <a href="<?php echo get_permalink($biz->ID); ?>" class="business-card" data-negocio-id="<?php echo $biz->ID; ?>">
                            <div class="business-card-img">
                                <img src="<?php echo $imagen; ?>" alt="<?php echo esc_attr($biz->post_title); ?>">
                                <button class="business-card-fav" data-id="<?php echo $biz->ID; ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="business-card-content">
                                <span class="business-card-cat"><?php echo esc_html($categoria); ?></span>
                                <h3 class="business-card-title"><?php echo esc_html($biz->post_title); ?></h3>
                                <div class="business-card-rating">
                                    <span class="business-card-stars">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <span class="<?php echo $i < floor($rating) ? '' : 'empty'; ?>">★</span>
                                        <?php endfor; ?>
                                    </span>
                                    <span class="business-card-rating-num"><?php echo number_format($rating, 1); ?></span>
                                    <span class="business-card-reviews">(<?php echo $reviews; ?>)</span>
                                </div>
                                <?php if ($direccion): ?>
                                <div class="business-card-location">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    <span><?php echo esc_html($direccion); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-businesses">Aún no hay negocios registrados. <a href="<?php echo home_url('/registrar-negocio'); ?>">Registra el primero</a></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

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

<?php wp_footer(); ?>
</body>
</html>
