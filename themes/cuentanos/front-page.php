<?php
/**
 * Front Page - Cuentanos.mx
 * Estilo Airbnb Minimalista
 */

if (!defined('ABSPATH')) exit;

$categorias = get_terms([
    'taxonomy' => 'categoria',
    'hide_empty' => true,
    'number' => 8,
    'orderby' => 'count',
    'order' => 'DESC',
]);

$user_megafonos = 0;
if (is_user_logged_in()) {
    global $wpdb;
    $meta = $wpdb->get_row($wpdb->prepare(
        "SELECT megafonos FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d",
        get_current_user_id()
    ));
    $user_megafonos = $meta ? $meta->megafonos : 0;
}

$is_logged_in = is_user_logged_in();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php bloginfo('name'); ?> - <?php bloginfo('description'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<style>
:root {
    --primary: #EB510C;
    --primary-dark: #C94409;
    --secondary: #F89D2F;
    --cream: #FFFCF8;
    --text: #1a1a2e;
    --text-light: #6b7280;
    --text-muted: #9ca3af;
    --border: #e5e7eb;
    --bg: #ffffff;
    --surface: #f9fafb;
    --radius: 16px;
    --radius-sm: 12px;
    --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.05);
    --shadow-lg: 0 4px 20px rgba(0,0,0,0.1);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--text);
    background: var(--cream);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
}

a { text-decoration: none; color: inherit; transition: var(--transition); }
img { max-width: 100%; display: block; }
button { font-family: inherit; cursor: pointer; border: none; background: none; }

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ===== NAVBAR ===== */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--border);
    transition: var(--transition);
}

.navbar .container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 72px;
}

.navbar-logo img {
    height: 36px;
    width: auto;
}

.navbar-center {
    position: relative;
}

.navbar-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--cream);
    border: 1px solid var(--border);
    border-radius: 100px;
    font-size: 14px;
    font-weight: 500;
    color: var(--text);
    transition: var(--transition);
}

.navbar-btn:hover {
    border-color: var(--text);
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-primary-nav {
    padding: 10px 20px;
    background: var(--text);
    color: white;
    border-radius: 100px;
    font-size: 14px;
    font-weight: 600;
    transition: var(--transition);
}

.btn-primary-nav:hover {
    background: var(--primary);
    transform: translateY(-1px);
}

.megafonos-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    border-radius: 100px;
    font-size: 14px;
    font-weight: 700;
}

/* ===== HERO ===== */
.hero {
    padding: 140px 0 80px;
    text-align: center;
    background: linear-gradient(180deg, var(--cream) 0%, white 100%);
}

.hero h1 {
    font-size: 56px;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 20px;
    color: var(--text);
}

.hero h1 span {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero p {
    font-size: 20px;
    color: var(--text-light);
    max-width: 600px;
    margin: 0 auto 40px;
}

/* ===== SEARCH ===== */
.search-box {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow-lg);
    padding: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.search-field {
    flex: 1;
    padding: 16px 20px;
}

.search-field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 4px;
}

.search-field input {
    width: 100%;
    border: none;
    outline: none;
    font-size: 15px;
    color: var(--text);
    background: transparent;
}

.search-field input::placeholder {
    color: var(--text-muted);
}

.search-divider {
    width: 1px;
    height: 40px;
    background: var(--border);
}

.search-btn {
    padding: 16px 32px;
    background: var(--primary);
    color: white;
    border-radius: var(--radius-sm);
    font-size: 15px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
}

.search-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

/* ===== MEGAFONOS BANNER ===== */
.megafonos-banner {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    padding: 20px 0;
    margin-top: 60px;
    border-radius: var(--radius);
}

.megafonos-banner .container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
}

.megafonos-info {
    display: flex;
    align-items: center;
    gap: 16px;
    color: white;
}

.megafonos-icon {
    font-size: 40px;
}

.megafonos-text h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 4px;
}

.megafonos-text p {
    font-size: 14px;
    opacity: 0.9;
}

.megafonos-link {
    padding: 12px 28px;
    background: white;
    color: var(--primary);
    border-radius: 100px;
    font-weight: 600;
    font-size: 14px;
    transition: var(--transition);
}

.megafonos-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* ===== SECTION HEADER ===== */
.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
}

.section-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text);
}

.section-link {
    color: var(--primary);
    font-weight: 600;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.section-link:hover {
    gap: 10px;
}

/* ===== CATEGORIAS ===== */
.categories-section {
    padding: 80px 0;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 16px;
}

.category-card {
    text-align: center;
    padding: 24px 16px;
    background: white;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    transition: var(--transition);
}

.category-card:hover {
    border-color: var(--primary);
    transform: translateY(-4px);
    box-shadow: var(--shadow);
}

.category-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 12px;
    color: var(--primary);
}

.category-icon svg {
    width: 100%;
    height: 100%;
}

.category-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--text);
}

.category-count {
    font-size: 12px;
    color: var(--text-muted);
}

/* ===== NEGOCIOS DESTACADOS ===== */
.featured-section {
    padding: 80px 0;
    background: white;
}

.businesses-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
}

.business-card {
    background: var(--cream);
    border-radius: var(--radius);
    overflow: hidden;
    transition: var(--transition);
    display: block;
}

.business-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

.business-img {
    position: relative;
    aspect-ratio: 4/3;
    overflow: hidden;
}

.business-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.business-card:hover .business-img img {
    transform: scale(1.05);
}

.business-fav {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow);
    color: var(--text-muted);
    transition: var(--transition);
}

.business-fav.active {
    color: #ef4444;
}

.business-fav svg {
    width: 18px;
    height: 18px;
}

.business-content {
    padding: 16px;
}

.business-category {
    font-size: 12px;
    font-weight: 600;
    color: var(--primary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.business-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 8px;
}

.business-rating {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.business-stars {
    color: var(--secondary);
}

.business-reviews {
    color: var(--text-muted);
}

.business-location {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--text-light);
    margin-top: 8px;
}

.business-location svg {
    width: 14px;
    height: 14px;
}

/* ===== CTA SECTION ===== */
.cta-section {
    padding: 100px 0;
    background: var(--text);
    text-align: center;
}

.cta-section h2 {
    font-size: 40px;
    font-weight: 800;
    color: white;
    margin-bottom: 16px;
}

.cta-section p {
    font-size: 18px;
    color: rgba(255,255,255,0.7);
    margin-bottom: 32px;
}

.cta-buttons {
    display: flex;
    gap: 16px;
    justify-content: center;
}

.btn-white {
    padding: 16px 32px;
    background: white;
    color: var(--text);
    border-radius: var(--radius-sm);
    font-weight: 600;
    font-size: 16px;
    transition: var(--transition);
}

.btn-white:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-outline {
    padding: 16px 32px;
    background: transparent;
    color: white;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: var(--radius-sm);
    font-weight: 600;
    font-size: 16px;
    transition: var(--transition);
}

.btn-outline:hover {
    border-color: white;
    background: rgba(255,255,255,0.1);
}

/* ===== FOOTER ===== */
.site-footer {
    background: var(--text);
    color: white;
}

.footer-main {
    padding: 60px 0 40px;
}

.footer-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr 1.5fr;
    gap: 40px;
}

.footer-brand img {
    height: 40px;
    margin-bottom: 16px;
}

.footer-brand p {
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
    transition: var(--transition);
}

.social-link:hover {
    background: var(--primary);
    transform: translateY(-2px);
}

.social-link svg {
    width: 18px;
    height: 18px;
    color: white;
}

.footer-col h4 {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 16px;
    color: white;
}

.footer-col ul {
    list-style: none;
}

.footer-col li {
    margin-bottom: 10px;
}

.footer-col a {
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    transition: var(--transition);
}

.footer-col a:hover {
    color: var(--primary);
}

.newsletter-form {
    display: flex;
    gap: 8px;
}

.newsletter-form input {
    flex: 1;
    padding: 12px 16px;
    border: none;
    border-radius: var(--radius-sm);
    background: rgba(255,255,255,0.1);
    color: white;
    font-size: 14px;
}

.newsletter-form input::placeholder {
    color: rgba(255,255,255,0.5);
}

.newsletter-form button {
    padding: 12px 16px;
    background: var(--primary);
    border-radius: var(--radius-sm);
    color: white;
}

.newsletter-form button:hover {
    background: var(--primary-dark);
}

.footer-bottom {
    background: #0f0f23;
    padding: 20px 0;
}

.footer-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footer-bottom p {
    color: rgba(255,255,255,0.6);
    font-size: 14px;
}

.footer-bottom-links {
    display: flex;
    gap: 24px;
}

.footer-bottom-links a {
    color: rgba(255,255,255,0.6);
    font-size: 14px;
}

.footer-bottom-links a:hover {
    color: white;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 1024px) {
    .categories-grid { grid-template-columns: repeat(4, 1fr); }
    .businesses-grid { grid-template-columns: repeat(2, 1fr); }
    .footer-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 768px) {
    .hero h1 { font-size: 36px; }
    .hero p { font-size: 16px; }
    .search-box { flex-direction: column; padding: 16px; }
    .search-field { width: 100%; border-bottom: 1px solid var(--border); }
    .search-divider { display: none; }
    .search-btn { width: 100%; justify-content: center; }
    .categories-grid { grid-template-columns: repeat(4, 1fr); gap: 12px; }
    .businesses-grid { grid-template-columns: 1fr; }
    .cta-buttons { flex-direction: column; }
    .footer-grid { grid-template-columns: 1fr 1fr; }
    .footer-bottom-content { flex-direction: column; gap: 16px; text-align: center; }
    .megafonos-banner .container { flex-direction: column; text-align: center; }
}

@media (max-width: 480px) {
    .categories-grid { grid-template-columns: repeat(2, 1fr); }
    .footer-grid { grid-template-columns: 1fr; }
}
</style>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="container">
        <a href="<?php echo home_url(); ?>" class="navbar-logo">
            <img src="https://cuentanos.mx/wp-content/uploads/2026/04/LOGO-HORIZONTAL.png" alt="Cuentanos.mx">
        </a>
        
        <div class="navbar-center">
            <button class="navbar-btn" onclick="document.getElementById('search-section').scrollIntoView({behavior: 'smooth'})">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Buscar...
            </button>
        </div>
        
        <div class="navbar-right">
            <?php if ($is_logged_in): ?>
                <span class="megafonos-badge"><span>📣</span><span><?php echo $user_megafonos; ?></span></span>
            <?php else: ?>
                <a href="<?php echo home_url('/mi-cuenta'); ?>" class="btn-primary-nav">Iniciar sesión</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main>
    <!-- HERO -->
    <section class="hero">
        <div class="container">
            <h1>Descubre los mejores<br><span>negocios locales</span></h1>
            <p>Explora, evalúa y apoya a los negocios de tu comunidad en un solo lugar.</p>
            
            <div class="search-box" id="search-section">
                <form action="<?php echo home_url('/directorio'); ?>" method="GET">
                    <div class="search-field" style="flex: 2;">
                        <label>¿Qué buscas?</label>
                        <input type="text" name="buscar" placeholder="Restaurante, hotel, cafetería...">
                    </div>
                    <div class="search-divider"></div>
                    <div class="search-field">
                        <label>Categoría</label>
                        <select name="categoria" style="border: none; outline: none; font-size: 15px; background: transparent; width: 100%;">
                            <option value="">Todas</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="search-btn">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Buscar
                    </button>
                </form>
            </div>
        </div>
    </section>
    
    <!-- MEGAFONOS BANNER -->
    <section class="container" style="margin-top: -40px; position: relative; z-index: 10;">
        <div class="megafonos-banner">
            <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
                <div class="megafonos-info">
                    <span class="megafonos-icon">📣</span>
                    <div class="megafonos-text">
                        <h3>Gana Megáfonos</h3>
                        <p>Escribe reseñas, guarda favoritos y alcanza nuevas metas</p>
                    </div>
                </div>
                <a href="<?php echo home_url('/recompensas'); ?>" class="megafonos-link">Ver recompensas →</a>
            </div>
        </div>
    </section>
    
    <!-- CATEGORIAS -->
    <section class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Explora por categoría</h2>
                <a href="<?php echo home_url('/directorio'); ?>" class="section-link">Ver todas →</a>
            </div>
            
            <div class="categories-grid">
                <?php
                $iconos = [
                    'restaurantes' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/></svg>',
                    'hoteles' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/></svg>',
                    'cafeterias' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/></svg>',
                    'tiendas' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/></svg>',
                    'gimnasios' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6.5 6.5h11"/><path d="M6.5 17.5h11"/><path d="M2 12h4"/><path d="M18 12h4"/><path d="M6.5 6.5v11"/><path d="M17.5 6.5v11"/></svg>',
                    'spa' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22c-4-3-8-6-8-11a8 8 0 0 1 16 0c0 5-4 8-8 11Z"/><path d="M12 8a3 3 0 1 0 0 6"/></svg>',
                    'bares' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 22h8"/><path d="M12 11v11"/><path d="m19 3-7 8-7-8Z"/></svg>',
                    'educacion' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
                    'default' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/></svg>',
                ];
                
                foreach ($categorias as $cat):
                    $slug = sanitize_title($cat->name);
                    $icono = isset($iconos[$slug]) ? $iconos[$slug] : $iconos['default'];
                ?>
                    <a href="<?php echo home_url('/directorio?categoria=' . $cat->slug); ?>" class="category-card">
                        <div class="category-icon"><?php echo $icono; ?></div>
                        <div class="category-name"><?php echo $cat->name; ?></div>
                        <div class="category-count"><?php echo $cat->count; ?> lugares</div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- NEGOCIOS DESTACADOS -->
    <section class="featured-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Negocios destacados</h2>
                <a href="<?php echo home_url('/directorio'); ?>" class="section-link">Ver todos →</a>
            </div>
            
            <div class="businesses-grid" id="businesses-grid">
                <!-- Cargado via AJAX -->
            </div>
        </div>
    </section>
    
    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <h2>¿Tienes un negocio?</h2>
            <p>Llega a miles de clientes locales y muestra lo mejor de tu empresa</p>
            <div class="cta-buttons">
                <a href="<?php echo home_url('/registrar-negocio'); ?>" class="btn-white">Registrar mi negocio</a>
                <a href="<?php echo home_url('/directorio'); ?>" class="btn-outline">Explorar directorio</a>
            </div>
        </div>
    </section>
</main>

<!-- FOOTER -->
<footer class="site-footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <img src="https://cuentanos.mx/wp-content/uploads/2026/04/LOGO-HORIZONTAL.png" alt="Cuentanos.mx">
                    <p>El directorio de negocios locales más grande de México. Descubre, evalúa y apoya a los negocios de tu comunidad.</p>
                    <div class="footer-social">
                        <a href="#" class="social-link" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <a href="#" class="social-link" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/></svg></a>
                        <a href="#" class="social-link" aria-label="Twitter"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Explorar</h4>
                    <ul>
                        <li><a href="<?php echo home_url('/directorio'); ?>">Directorio</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=restaurantes'); ?>">Restaurantes</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=hoteles'); ?>">Hoteles</a></li>
                        <li><a href="<?php echo home_url('/directorio?categoria=cafeterias'); ?>">Cafeterías</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Negocios</h4>
                    <ul>
                        <li><a href="<?php echo home_url('/registrar-negocio'); ?>">Registrar negocio</a></li>
                        <li><a href="<?php echo home_url('/login-empresa'); ?>">Acceso empresas</a></li>
                        <li><a href="<?php echo home_url('/dashboard-empresa'); ?>">Dashboard</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Cuenta</h4>
                    <ul>
                        <li><a href="<?php echo home_url('/registro'); ?>">Registrarse</a></li>
                        <li><a href="<?php echo home_url('/mi-cuenta'); ?>">Iniciar sesión</a></li>
                        <li><a href="<?php echo home_url('/perfil'); ?>">Mi perfil</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Newsletter</h4>
                    <p style="color: rgba(255,255,255,0.7); font-size: 14px; margin-bottom: 12px;">Recibe las mejores recomendaciones cada semana.</p>
                    <form class="newsletter-form" onsubmit="event.preventDefault(); this.querySelector('button').textContent = '✓';">
                        <input type="email" placeholder="tu@email.com" required>
                        <button type="submit">→</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <p>© <?php echo date('Y'); ?> <strong>Cuentanos.mx</strong>. Todos los derechos reservados.</p>
                <div class="footer-bottom-links">
                    <a href="#">Términos</a>
                    <a href="#">Privacidad</a>
                    <a href="#">Contacto</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar negocios destacados via AJAX
    fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=cnmx_get_featured_businesses')
        .then(response => response.text())
        .then(html => {
            document.getElementById('businesses-grid').innerHTML = html || '<p style="text-align:center; padding:60px; color:#9ca3af;">No hay negocios destacados aún</p>';
        })
        .catch(error => {
            document.getElementById('businesses-grid').innerHTML = '<p style="text-align:center; padding:60px; color:#9ca3af;">Error al cargar negocios</p>';
        });
    
    // Toggle favoritos
    document.addEventListener('click', function(e) {
        if (e.target.closest('.business-fav')) {
            e.preventDefault();
            const btn = e.target.closest('.business-fav');
            const negocioId = btn.dataset.id;
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=cnmx_favorito', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'negocio_id=' + negocioId + '&nonce=<?php echo wp_create_nonce('cnmx_nonce'); ?>'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.classList.toggle('active');
                    btn.querySelector('svg').setAttribute('fill', btn.classList.contains('active') ? 'currentColor' : 'none');
                }
            });
        }
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
