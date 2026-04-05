<?php
/**
 * Archive Negocio Template - Directorio estilo Airbnb
 */

if (!defined('ABSPATH')) exit;

$categoria = isset($_GET['categoria']) ? sanitize_text_field($_GET['categoria']) : '';
$busqueda = isset($_GET['buscar']) ? sanitize_text_field($_GET['buscar']) : '';
$paged = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : (isset($wp_query->query_vars['paged']) ? max(1, intval($wp_query->query_vars['paged'])) : 1);

$args = [
    'post_type' => 'negocio',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'paged' => $paged,
];

if ($categoria) {
    $args['tax_query'] = [['taxonomy' => 'categoria', 'field' => 'slug', 'terms' => $categoria]];
}
if ($busqueda) {
    $args['s'] = $busqueda;
}

$negocios = new WP_Query($args);
$categories = get_terms(['taxonomy' => 'categoria', 'hide_empty' => true]);

$user_megafonos = 0;
if (is_user_logged_in()) {
    global $wpdb;
    $meta = $wpdb->get_row($wpdb->prepare("SELECT megafonos FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d", get_current_user_id()));
    $user_megafonos = $meta ? $meta->megafonos : 0;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Directorio - Cuentanos.mx</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body>
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
    --space-sm: 8px;
    --space-md: 12px;
    --space-lg: 16px;
    --space-xl: 24px;
}

* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Inter', sans-serif; color: var(--text); background: var(--cream); line-height: 1.6; }
a { text-decoration: none; color: inherit; transition: var(--transition); }
img { max-width: 100%; display: block; }
button { font-family: inherit; cursor: pointer; border: none; background: none; }

.container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

/* NAVBAR */
.navbar {
    position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
    background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--border);
}
.navbar .container { display: flex; align-items: center; justify-content: space-between; height: 72px; }
.navbar-logo img { height: 36px; width: auto; }
.navbar-right { display: flex; align-items: center; gap: 12px; }
.btn-outline { padding: 10px 20px; border: 1px solid var(--border); border-radius: 100px; font-size: 14px; font-weight: 500; }
.btn-outline:hover { border-color: var(--text); }
.btn-primary { padding: 10px 20px; background: var(--primary); color: white; border-radius: 100px; font-size: 14px; font-weight: 600; }
.btn-primary:hover { background: var(--primary-dark); }
.megafonos-badge { display: flex; align-items: center; gap: 6px; padding: 8px 16px; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; border-radius: 100px; font-size: 14px; font-weight: 700; }

/* HERO */
.directory-hero {
    padding: 140px 0 60px; text-align: center;
    background: linear-gradient(180deg, var(--cream) 0%, white 100%);
}
.directory-hero h1 { font-size: 40px; font-weight: 800; margin-bottom: 8px; }
.directory-hero p { font-size: 18px; color: var(--text-light); }

/* SEARCH */
.search-section { padding: 0 0 40px; }
.search-box {
    max-width: 800px; margin: 0 auto; background: white;
    border-radius: var(--radius); box-shadow: var(--shadow-lg);
    padding: 8px; display: flex; align-items: center; gap: 8px;
}
.search-field { flex: 1; padding: 16px 20px; }
.search-field input, .search-field select {
    width: 100%; border: none; outline: none; font-size: 15px;
    color: var(--text); background: transparent;
}
.search-field input::placeholder { color: var(--text-muted); }
.search-divider { width: 1px; height: 40px; background: var(--border); }
.search-btn {
    padding: 16px 28px; background: var(--primary); color: white;
    border-radius: var(--radius-sm); font-size: 15px; font-weight: 600;
    display: flex; align-items: center; gap: 8px;
}
.search-btn:hover { background: var(--primary-dark); }

/* RESULTS */
.results-section { padding: 40px 0 80px; }
.section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; }
.section-title { font-size: 28px; font-weight: 700; }
.results-count { font-size: 16px; font-weight: 400; color: var(--text-muted); margin-left: 8px; }

/* BUSINESS GRID */
.businesses-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;
}
.business-card {
    background: white; border-radius: var(--radius); overflow: hidden;
    transition: var(--transition); display: block;
}
.business-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); }
.business-img { position: relative; aspect-ratio: 4/3; overflow: hidden; }
.business-img img { width: 100%; height: 100%; object-fit: cover; transition: var(--transition); }
.business-card:hover .business-img img { transform: scale(1.05); }
.business-fav {
    position: absolute !important;
    top: 12px !important;
    right: 12px !important;
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    min-height: 32px !important;
    max-width: 32px !important;
    max-height: 32px !important;
    background: rgba(255,255,255,0.95) !important;
    border-radius: 50% !important;
    border: none !important;
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #6b7280 !important;
    cursor: pointer !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
}
.business-fav:hover {
    background: white !important;
    color: #1a1a2e !important;
    transform: scale(1.05) !important;
}
.business-fav.active {
    color: #ef4444 !important;
    background: white !important;
}
.business-fav.active svg {
    fill: #ef4444 !important;
}
.business-fav svg {
    width: 16px !important;
    height: 16px !important;
    flex-shrink: 0 !important;
}
.business-content { padding: 20px; }
.business-category { font-size: 12px; font-weight: 600; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.business-title { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
.business-rating { display: flex; align-items: center; gap: 8px; font-size: 14px; }
.business-stars { color: var(--secondary); }
.business-reviews { color: var(--text-muted); }
.business-location { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-light); margin-top: 8px; }
.business-location svg { width: 14px; height: 14px; }

/* EMPTY STATE */
.empty-state { text-align: center; padding: 80px 20px; }
.empty-state svg { width: 80px; height: 80px; color: var(--text-muted); margin-bottom: 20px; }
.empty-state h3 { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
.empty-state p { color: var(--text-light); margin-bottom: 24px; }

/* CTA */
.cta-section { padding: 80px 0; background: var(--text); text-align: center; }
.cta-section h2 { font-size: 36px; font-weight: 800; color: white; margin-bottom: 12px; }
.cta-section p { font-size: 18px; color: rgba(255,255,255,0.7); margin-bottom: 24px; }
.btn-white { padding: 16px 32px; background: white; color: var(--text); border-radius: var(--radius-sm); font-weight: 600; }
.btn-white:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }

/* FOOTER */
.site-footer { background: var(--text); color: white; padding: 40px 0 20px; }
.footer-content { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
.footer-content p { color: rgba(255,255,255,0.7); font-size: 14px; }
.footer-links { display: flex; gap: 24px; }
.footer-links a { color: rgba(255,255,255,0.7); font-size: 14px; }
.footer-links a:hover { color: white; }
.footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); margin-top: 20px; padding-top: 20px; text-align: center; }
.footer-bottom p { color: rgba(255,255,255,0.5); font-size: 13px; }

/* PAGINATION */
.pagination { display: flex; justify-content: center; gap: 8px; margin-top: 48px; }
.pagination a, .pagination span { display: flex; align-items: center; justify-content: center; padding: 10px 16px; border-radius: var(--radius-sm); font-weight: 500; transition: var(--transition); }
.pagination a { background: white; border: 1px solid var(--border); color: var(--text); }
.pagination a:hover { border-color: var(--primary); color: var(--primary); }
.pagination .current { background: var(--primary); color: white; border-color: var(--primary); }

/* RESPONSIVE */
@media (max-width: 1024px) { .businesses-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) {
    .directory-hero h1 { font-size: 28px; }
    .search-box { flex-direction: column; padding: 16px; }
    .search-field { width: 100%; border-bottom: 1px solid var(--border); }
    .search-divider { display: none; }
    .search-btn { width: 100%; justify-content: center; }
    .businesses-grid { grid-template-columns: 1fr; }
    .footer-content { flex-direction: column; text-align: center; }
}
</style>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="container">
        <a href="<?php echo home_url(); ?>" class="navbar-logo">
            <img src="https://cuentanos.mx/wp-content/uploads/2026/04/LOGO-PRINCIPAL.png" alt="Cuentanos.mx">
        </a>
        <div class="navbar-right">
            <?php if (is_user_logged_in()): ?>
                <span class="megafonos-badge"><span>📣</span><span><?php echo $user_megafonos; ?></span></span>
                <a href="<?php echo home_url('/perfil'); ?>" class="btn-outline">Mi Perfil</a>
            <?php else: ?>
                <a href="<?php echo home_url('/mi-cuenta'); ?>" class="btn-outline">Iniciar sesión</a>
                <a href="<?php echo home_url('/registro'); ?>" class="btn-primary">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main>
    <!-- HERO -->
    <section class="directory-hero">
        <div class="container">
            <h1>Explora negocios locales</h1>
            <p>Descubre los mejores lugares cerca de ti</p>
        </div>
    </section>
    
    <!-- SEARCH -->
    <section class="search-section">
        <div class="container">
            <form class="search-box" method="GET">
                <div class="search-field" style="flex: 2;">
                    <input type="text" name="buscar" placeholder="Buscar negocios..." value="<?php echo esc_attr($busqueda); ?>">
                </div>
                <div class="search-divider"></div>
                <div class="search-field">
                    <select name="categoria">
                        <option value="">Todas</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat->slug; ?>" <?php selected($categoria, $cat->slug); ?>><?php echo $cat->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="search-btn">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Buscar
                </button>
            </form>
        </div>
    </section>
    
    <!-- RESULTS -->
    <section class="results-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <?php 
                    if ($categoria) {
                        $cat_obj = get_term_by('slug', $categoria, 'categoria');
                        echo $cat_obj ? $cat_obj->name : 'Negocios';
                    } elseif ($busqueda) {
                        echo 'Resultados para "' . esc_html($busqueda) . '"';
                    } else {
                        echo 'Todos los negocios';
                    }
                    ?>
                    <span class="results-count">(<?php echo $negocios->found_posts; ?>)</span>
                </h2>
            </div>
            
            <?php if ($negocios->have_posts()): ?>
                <div class="businesses-grid">
                    <?php while ($negocios->have_posts()): $negocios->the_post();
                        $rating = get_post_meta(get_the_ID(), 'cnmx_rating', true) ?: 0;
                        $reviews = get_post_meta(get_the_ID(), 'cnmx_reviews_count', true) ?: 0;
                        $direccion = get_post_meta(get_the_ID(), 'cnmx_direccion', true) ?: '';
                        $imagen = get_the_post_thumbnail_url(get_the_ID(), 'cnmx-card') ?: 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=600&h=400&fit=crop';
                        $cats = get_the_terms(get_the_ID(), 'categoria');
                        $categoria_nombre = $cats ? $cats[0]->name : 'General';
                    ?>
                        <a href="<?php the_permalink(); ?>" class="business-card">
                            <div class="business-img">
                                <img src="<?php echo esc_url($imagen); ?>" alt="<?php the_title_attribute(); ?>">
                                <button class="business-fav" data-id="<?php the_ID(); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                </button>
                            </div>
                            <div class="business-content">
                                <span class="business-category"><?php echo esc_html($categoria_nombre); ?></span>
                                <h3 class="business-title"><?php the_title(); ?></h3>
                                <div class="business-rating">
                                    <span class="business-stars"><?php for($i=0;$i<5;$i++) echo $i<floor($rating)?'★':'☆'; ?></span>
                                    <span><?php echo number_format($rating, 1); ?></span>
                                    <span class="business-reviews">(<?php echo $reviews; ?>)</span>
                                </div>
                                <?php if ($direccion): ?>
                                    <div class="business-location">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <?php echo esc_html($direccion); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
                
                <?php if ($negocios->max_num_pages > 1): ?>
                    <div class="pagination">
                        <?php
                        $base = home_url('/directorio');
                        if ($categoria) $base = add_query_arg('categoria', $categoria, $base);
                        if ($busqueda) $base = add_query_arg('buscar', $busqueda, $base);
                        
                        echo paginate_links([
                            'base' => trailingslashit($base) . 'pagina/%#%/',
                            'format' => '',
                            'current' => $paged,
                            'total' => $negocios->max_num_pages,
                            'prev_text' => '← Anterior',
                            'next_text' => 'Siguiente →',
                        ]);
                        ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <h3>No se encontraron negocios</h3>
                    <p>Intenta con otros términos de búsqueda o explora todas las categorías.</p>
                    <a href="<?php echo home_url('/directorio'); ?>" class="btn-primary">Ver todos los negocios</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <h2>¿Tienes un negocio?</h2>
            <p>Llega a miles de clientes locales y muestra lo mejor de tu empresa</p>
            <a href="<?php echo home_url('/registrar-negocio'); ?>" class="btn-white">Registrar mi negocio</a>
        </div>
    </section>
</main>

<!-- FOOTER -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <p>© <?php echo date('Y'); ?> <strong>Cuentanos.mx</strong>. Todos los derechos reservados.</p>
            <div class="footer-links">
                <a href="<?php echo home_url('/directorio'); ?>">Directorio</a>
                <a href="<?php echo home_url('/registrar-negocio'); ?>">Para negocios</a>
                <a href="#">Términos</a>
                <a href="#">Privacidad</a>
            </div>
        </div>
    </div>
</footer>

<script>
document.addEventListener('click', function(e) {
    if (e.target.closest('.business-fav')) {
        e.preventDefault();
        e.stopPropagation();
        const btn = e.target.closest('.business-fav');
        const id = btn.dataset.id;
        fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=cnmx_favorito', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'negocio_id=' + id + '&nonce=<?php echo wp_create_nonce('cnmx_nonce'); ?>'
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
</script>

<?php wp_reset_postdata(); wp_footer(); ?>
</body>
</html>
