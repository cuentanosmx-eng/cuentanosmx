<?php
/**
 * Front Page Template - Custom Home
 */

get_header();

// Demo data for businesses
$negocios_data = array(
    array('title' => 'La Casa de los Tacos', 'category' => 'Restaurantes', 'rating' => 4.8, 'reviews' => 156, 'location' => 'Centro Historico', 'imagen' => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=600&h=400&fit=crop'),
    array('title' => 'Cafe del Mar', 'category' => 'Cafeterias', 'rating' => 4.6, 'reviews' => 89, 'location' => 'Zona Romantica', 'imagen' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=600&h=400&fit=crop'),
    array('title' => 'Hotel Boutique La Paz', 'category' => 'Hoteles', 'rating' => 4.9, 'reviews' => 234, 'location' => 'Playa Principal', 'imagen' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&h=400&fit=crop'),
    array('title' => 'Boutique Moda Latina', 'category' => 'Tiendas de ropa', 'rating' => 4.5, 'reviews' => 67, 'location' => 'Centro Comercial', 'imagen' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=600&h=400&fit=crop'),
    array('title' => 'Spa Relax', 'category' => 'Spa', 'rating' => 4.7, 'reviews' => 112, 'location' => 'Zona Hotelera', 'imagen' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=600&h=400&fit=crop'),
    array('title' => 'Bar El Dorado', 'category' => 'Bares', 'rating' => 4.4, 'reviews' => 78, 'location' => 'Barrio nightlife', 'imagen' => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?w=600&h=400&fit=crop'),
    array('title' => 'Gimnasio PowerFit', 'category' => 'Gimnasios', 'rating' => 4.3, 'reviews' => 145, 'location' => 'Av. Principal', 'imagen' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600&h=400&fit=crop'),
    array('title' => 'Pasteleria Dulces Suenos', 'category' => 'Postres', 'rating' => 4.9, 'reviews' => 203, 'location' => 'Colonia Centro', 'imagen' => 'https://images.unsplash.com/photo-1558301211-0d8c8ddee6ec?w=600&h=400&fit=crop'),
);

$categories = array(
    array('name' => 'Restaurantes', 'slug' => 'restaurantes', 'icon' => '🍽️'),
    array('name' => 'Cafeterias', 'slug' => 'cafeterias', 'icon' => '☕'),
    array('name' => 'Hoteles', 'slug' => 'hoteles', 'icon' => '🏨'),
    array('name' => 'Bares', 'slug' => 'bares', 'icon' => '🍺'),
    array('name' => 'Tiendas', 'slug' => 'tiendas', 'icon' => '🛍️'),
    array('name' => 'Spa', 'slug' => 'spa', 'icon' => '💆'),
);
?>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <div class="container">
        <div class="navbar-inner">
            <a href="<?php echo home_url(); ?>" class="navbar-logo">
                <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                Cuentanos.mx
            </a>
            <div class="navbar-nav">
                <a href="<?php echo home_url(); ?>" class="navbar-link">Inicio</a>
                <a href="<?php echo home_url('/directorio'); ?>" class="navbar-link">Explorar</a>
            </div>
            <div class="navbar-actions">
                <a href="<?php echo home_url('/mi-cuenta'); ?>" class="btn btn-outline">Iniciar sesion</a>
                <a href="<?php echo home_url('/registro'); ?>" class="btn btn-primary">Registrarse</a>
            </div>
        </div>
    </div>
</nav>

<main>
    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Descubre los mejores lugares cerca de ti</h1>
            <p class="hero-subtitle">Negocios locales recomendados por la comunidad</p>
            
            <div class="search-box">
                <form class="search-form" action="<?php echo home_url('/directorio'); ?>" method="GET">
                    <div class="search-field">
                        <label>Que buscas?</label>
                        <input type="text" name="q" placeholder="Restaurantes, cafes, hoteles...">
                    </div>
                    <div class="search-field">
                        <label>Categoria</label>
                        <select name="categoria">
                            <option value="">Todas</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="search-btn">Buscar</button>
                </form>
            </div>
            
            <div class="quick-cats">
                <?php foreach ($categories as $cat): ?>
                    <a href="<?php echo home_url('/directorio?categoria=' . $cat['slug']); ?>" class="quick-cat">
                        <span class="quick-cat-icon"><?php echo $cat['icon']; ?></span>
                        <span><?php echo $cat['name']; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- MEGAFONOS BANNER -->
    <section class="megafonos-banner">
        <div class="container">
            <div class="megafonos-banner-inner">
                <span class="megafonos-banner-icon">🎤</span>
                <div class="megafonos-banner-text">
                    <h3>Unete a Cuentanos.mx!</h3>
                    <p>Gana Megafonos con cada resena y favorito.</p>
                </div>
                <a href="<?php echo home_url('/registro'); ?>" class="btn btn-white btn-lg">Registrate gratis</a>
            </div>
        </div>
    </section>

    <!-- FEATURED BUSINESSES -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Negocios Destacados</h2>
                <a href="<?php echo home_url('/directorio'); ?>" class="section-link">Ver todos</a>
            </div>
            
            <div class="businesses-grid">
                <?php foreach ($negocios_data as $biz): ?>
                    <a href="#" class="business-card">
                        <div class="business-card-img">
                            <img src="<?php echo $biz['imagen']; ?>" alt="<?php echo $biz['title']; ?>">
                            <button class="business-card-fav" onclick="event.preventDefault(); this.classList.toggle('active');">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="business-card-content">
                            <span class="business-card-cat"><?php echo $biz['category']; ?></span>
                            <h3 class="business-card-title"><?php echo $biz['title']; ?></h3>
                            <div class="business-card-rating">
                                <span class="business-card-stars">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <span class="<?php echo $i < floor($biz['rating']) ? '' : 'empty'; ?>">★</span>
                                    <?php endfor; ?>
                                </span>
                                <span class="business-card-rating-num"><?php echo number_format($biz['rating'], 1); ?></span>
                                <span class="business-card-reviews">(<?php echo $biz['reviews']; ?>)</span>
                            </div>
                            <div class="business-card-location">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span><?php echo $biz['location']; ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Tienes un negocio?</h2>
                <p>Llega a miles de clientes locales en Cuentanos.mx</p>
                <div class="cta-buttons">
                    <a href="<?php echo home_url('/registrar-negocio'); ?>" class="btn btn-primary btn-lg">Registrar mi negocio</a>
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
                <p>Descubre los mejores negocios locales en Mexico.</p>
            </div>
            <div class="footer-links">
                <div class="footer-col">
                    <h4>Explorar</h4>
                    <a href="<?php echo home_url('/directorio'); ?>">Directorio</a>
                </div>
                <div class="footer-col">
                    <h4>Negocios</h4>
                    <a href="<?php echo home_url('/registrar-negocio'); ?>">Registrar negocio</a>
                </div>
                <div class="footer-col">
                    <h4>Cuenta</h4>
                    <a href="<?php echo home_url('/registro'); ?>">Registrarse</a>
                    <a href="<?php echo home_url('/mi-cuenta'); ?>">Iniciar sesion</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Cuentanos.mx. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<?php get_footer(); ?>
