<?php
/**
 * Front Page Template - Elementor Compatible
 */

get_header();

// Check if Elementor is active and has content
if (have_posts()) : the_post();
    
    // Check if page has Elementor content
    if (function_exists('elementor_load_plugin_textdomain') || defined('ELEMENTOR_VERSION')) {
        // Use Elementor content
        the_content();
    } else {
        // Fallback content if no Elementor
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
                                    <option value="restaurantes">Restaurantes</option>
                                    <option value="cafeterias">Cafeterias</option>
                                    <option value="hoteles">Hoteles</option>
                                    <option value="bares">Bares</option>
                                    <option value="tiendas">Tiendas</option>
                                </select>
                            </div>
                            <button type="submit" class="search-btn">Buscar</button>
                        </form>
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
                </div>
                <div class="footer-bottom">
                    <p>&copy; <?php echo date('Y'); ?> Cuentanos.mx.</p>
                </div>
            </div>
        </footer>

        <?php
    }

endif;

get_footer();
