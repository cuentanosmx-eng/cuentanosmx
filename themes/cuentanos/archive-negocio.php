<?php
/**
 * Archive Negocio Template - Listado de negocios (directorio)
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
$categoria = isset($_GET['categoria']) ? sanitize_text_field($_GET['categoria']) : '';
$busqueda = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';

$args = array(
    'post_type' => 'negocio',
    'post_status' => 'publish',
    'posts_per_page' => 12,
);

if ($categoria) {
    $args['tax_query'] = array(array('taxonomy' => 'categoria', 'field' => 'slug', 'terms' => $categoria));
}

if ($busqueda) {
    $args['s'] = $busqueda;
}

$negocios = new WP_Query($args);

$categories = get_terms(array('taxonomy' => 'categoria', 'hide_empty' => true));

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
            <a href="<?php echo home_url('/directorio'); ?>" class="navbar-link active">Explorar</a>
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
    <section class="directory-hero">
        <div class="container">
            <h1>Explora negocios locales</h1>
            <p>Descubre los mejores lugares cerca de ti</p>
        </div>
    </section>
    
    <!-- FILTERS -->
    <section class="directory-filters">
        <div class="container">
            <form class="directory-search" method="GET">
                <div class="search-field">
                    <input type="text" name="q" placeholder="Buscar negocios..." value="<?php echo esc_attr($busqueda); ?>">
                </div>
                <div class="search-field">
                    <select name="categoria">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat->slug; ?>" <?php selected($categoria, $cat->slug); ?>>
                                <?php echo $cat->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="search-btn">Buscar</button>
                <?php if ($categoria || $busqueda): ?>
                    <a href="<?php echo home_url('/directorio'); ?>" class="btn btn-outline">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>
    </section>
    
    <!-- RESULTS -->
    <section class="section">
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
                        <a href="<?php the_permalink(); ?>" class="business-card" data-negocio-id="<?php the_ID(); ?>">
                            <div class="business-card-img">
                                <img src="<?php echo esc_url($imagen); ?>" alt="<?php the_title_attribute(); ?>">
                                <button class="business-card-fav" data-id="<?php the_ID(); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="business-card-content">
                                <span class="business-card-cat"><?php echo esc_html($categoria_nombre); ?></span>
                                <h3 class="business-card-title"><?php the_title(); ?></h3>
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
                    <?php endwhile; ?>
                </div>
                
                <?php if ($negocios->max_num_pages > 1): ?>
                    <div class="pagination">
                        <?php
                        echo paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '?paged=%#%',
                            'current' => max(1, get_query_var('paged')),
                            'total' => $negocios->max_num_pages,
                            'prev_text' => '&laquo; Anterior',
                            'next_text' => 'Siguiente &raquo;',
                        ));
                        ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🔍</div>
                    <h3>No se encontraron negocios</h3>
                    <p>Intenta con otros términos de búsqueda o explora todas las categorías.</p>
                    <a href="<?php echo home_url('/directorio'); ?>" class="btn btn-primary mt-lg">Ver todos los negocios</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>¿Tienes un negocio?</h2>
                <p>Llega a miles de clientes locales y muestra lo mejor de tu empresa</p>
                <a href="<?php echo home_url('/registrar-negocio'); ?>" class="btn btn-white btn-lg">Registrar mi negocio</a>
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
            </div>
            <div class="footer-col">
                <h4>Negocios</h4>
                <a href="<?php echo home_url('/registrar-negocio'); ?>">Registrar negocio</a>
            </div>
            <div class="footer-col">
                <h4>Cuenta</h4>
                <a href="<?php echo home_url('/registro'); ?>">Registrarse</a>
                <a href="<?php echo home_url('/mi-cuenta'); ?>">Iniciar sesión</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Cuentanos.mx. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<style>
.results-count{font-size:var(--font-size-base);font-weight:400;color:var(--gray-500);margin-left:var(--space-sm)}
.pagination{display:flex;justify-content:center;gap:var(--space-sm);margin-top:var(--space-2xl)}
.pagination a,.pagination span{display:flex;align-items:center;justify-content:center;padding:10px 16px;border-radius:var(--radius-md);font-weight:500;transition:var(--transition)}
.pagination a{background:var(--brand-white);border:1px solid var(--gray-200)}
.pagination a:hover{border-color:var(--brand-green);color:var(--brand-green)}
.pagination .current{background:var(--brand-green);color:var(--brand-white)}
@media(max-width:768px){.directory-search{flex-direction:column}.search-field{width:100%}}
</style>

<?php 
wp_reset_postdata();
wp_footer();
?>
</body>
</html>
