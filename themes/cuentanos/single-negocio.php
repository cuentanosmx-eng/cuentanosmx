<?php
/**
 * Single Negocio Template - Pagina individual de negocio
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
// Get negocio data
$negocio_id = get_the_ID();
$rating = get_post_meta($negocio_id, 'cnmx_rating', true) ?: 0;
$reviews_count = get_post_meta($negocio_id, 'cnmx_reviews_count', true) ?: 0;
$direccion = get_post_meta($negocio_id, 'cnmx_direccion', true) ?: '';
$telefono = get_post_meta($negocio_id, 'cnmx_telefono', true) ?: '';
$whatsapp = get_post_meta($negocio_id, 'cnmx_whatsapp', true) ?: '';
$email = get_post_meta($negocio_id, 'cnmx_email', true) ?: '';
$sitio_web = get_post_meta($negocio_id, 'cnmx_sitio_web', true) ?: '';
$horarios = get_post_meta($negocio_id, 'cnmx_horarios', true);

if (is_string($horarios)) {
    $horarios = json_decode($horarios, true) ?: array();
}

$cats = get_the_terms($negocio_id, 'categoria');
$categoria = $cats ? $cats[0]->name : 'General';
$imagen_principal = get_the_post_thumbnail_url($negocio_id, 'full') ?: 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=1200&h=600&fit=crop';

$user_megafonos = 0;
$is_favorite = false;
if (is_user_logged_in()) {
    global $wpdb;
    $user_id = get_current_user_id();
    $meta = $wpdb->get_row($wpdb->prepare(
        "SELECT megafonos FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d",
        $user_id
    ));
    $user_megafonos = $meta ? $meta->megafonos : 0;
    
    $fav = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}cnmx_favoritos WHERE user_id = %d AND negocio_id = %d",
        $user_id, $negocio_id
    ));
    $is_favorite = (bool)$fav;
}

$resenas = $wpdb->get_results($wpdb->prepare(
    "SELECT r.*, u.display_name as user_name 
     FROM {$wpdb->prefix}cnmx_resenas r 
     JOIN {$wpdb->prefix}users u ON u.ID = r.user_id 
     WHERE r.negocio_id = %d AND r.status = 'aprobado' 
     ORDER BY r.fecha DESC LIMIT 10",
    $negocio_id
));
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
    <!-- HERO IMAGE -->
    <section class="single-hero">
        <img src="<?php echo esc_url($imagen_principal); ?>" alt="<?php the_title_attribute(); ?>" class="single-hero-img">
        <div class="single-hero-overlay">
            <div class="container">
                <span class="single-hero-cat"><?php echo esc_html($categoria); ?></span>
                <h1 class="single-hero-title"><?php the_title(); ?></h1>
                <div class="single-hero-meta">
                    <span class="business-card-stars">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <span class="<?php echo $i < floor($rating) ? '' : 'empty'; ?>">★</span>
                        <?php endfor; ?>
                    </span>
                    <span><?php echo number_format($rating, 1); ?> (<?php echo $reviews_count; ?> reseñas)</span>
                    <?php if ($direccion): ?>
                        <span><?php echo esc_html($direccion); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ACTIONS -->
    <section class="container">
        <div class="single-actions">
            <button class="action-btn btn-favorito <?php echo $is_favorite ? 'active' : ''; ?>" data-id="<?php echo $negocio_id; ?>">
                <svg viewBox="0 0 24 24" fill="<?php echo $is_favorite ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                <span><?php echo $is_favorite ? 'Guardado' : 'Guardar'; ?></span>
            </button>
            
            <?php if ($telefono): ?>
                <a href="tel:<?php echo esc_attr($telefono); ?>" class="action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span><?php echo esc_html($telefono); ?></span>
                </a>
            <?php endif; ?>
            
            <?php if ($whatsapp): ?>
                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $whatsapp); ?>" target="_blank" class="action-btn action-btn-whatsapp">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span>WhatsApp</span>
                </a>
            <?php endif; ?>
            
            <?php if ($sitio_web): ?>
                <a href="<?php echo esc_url($sitio_web); ?>" target="_blank" class="action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span>Sitio Web</span>
                </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <section class="section">
        <div class="container">
            <div class="single-content">
                <div class="single-main">
                    <div class="single-section">
                        <h2 class="single-section-title">Acerca de <?php the_title(); ?></h2>
                        <div class="single-description"><?php the_content(); ?></div>
                    </div>
                    
                    <div class="single-section">
                        <div class="section-header">
                            <h2 class="single-section-title">Reseñas</h2>
                            <?php if (is_user_logged_in()): ?>
                                <button id="btn-write-review" class="btn btn-outline">Escribir reseña</button>
                            <?php else: ?>
                                <a href="<?php echo home_url('/registro'); ?>" class="btn btn-outline">Inicia sesión para reseñar</a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (is_user_logged_in()): ?>
                        <div id="review-form" class="review-form" style="display: none;">
                            <div class="rating-input" data-rating="0">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button class="star-btn" data-rating="<?php echo $i; ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="32" height="32"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    </button>
                                <?php endfor; ?>
                            </div>
                            <textarea id="review-text" class="form-textarea" placeholder="Cuéntanos tu experiencia..."></textarea>
                            <button id="btn-submit-review" class="btn btn-primary" data-negocio="<?php echo $negocio_id; ?>">Enviar Reseña</button>
                        </div>
                        <?php endif; ?>
                        
                        <div class="reviews-list">
                            <?php if (empty($resenas)): ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon">⭐</div>
                                    <h3>No hay reseñas aún</h3>
                                    <p>¡Sé el primero en compartir tu experiencia!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($resenas as $resena): ?>
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="review-avatar"><?php echo substr($resena->user_name, 0, 1); ?></div>
                                            <div class="review-info">
                                                <span class="review-name"><?php echo esc_html($resena->user_name); ?></span>
                                                <span class="review-meta"><?php echo date('d M Y', strtotime($resena->fecha)); ?></span>
                                            </div>
                                            <div class="review-stars">
                                                <?php for ($i = 0; $i < 5; $i++): ?>
                                                    <span class="<?php echo $i < $resena->calificacion ? '' : 'empty'; ?>">★</span>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <p class="review-text"><?php echo esc_html($resena->contenido); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <aside class="single-sidebar">
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">Ubicación</h3>
                        <?php if ($direccion): ?><p class="sidebar-address"><?php echo esc_html($direccion); ?></p><?php endif; ?>
                        <div id="map" class="map-container"></div>
                    </div>
                    
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">Horario</h3>
                        <div class="hours-list">
                            <?php
                            $dias_semana = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo');
                            $dias_labels = array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');
                            $dia_actual = strtolower(date('l'));
                            foreach ($dias_semana as $i => $dia):
                                $horario = $horarios[$dia] ?? '9:00 - 18:00';
                                $es_hoy = ($dia === $dia_actual);
                            ?>
                                <div class="hours-row <?php echo $es_hoy ? 'today' : ''; ?>">
                                    <span class="hours-day"><?php echo $dias_labels[$i]; ?></span>
                                    <span class="hours-time"><?php echo esc_html($horario); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">Contacto</h3>
                        <?php if ($telefono): ?><div class="contact-item"><strong>Teléfono:</strong><a href="tel:<?php echo esc_attr($telefono); ?>"><?php echo esc_html($telefono); ?></a></div><?php endif; ?>
                        <?php if ($whatsapp): ?><div class="contact-item"><strong>WhatsApp:</strong><a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $whatsapp); ?>" target="_blank"><?php echo esc_html($whatsapp); ?></a></div><?php endif; ?>
                        <?php if ($email): ?><div class="contact-item"><strong>Email:</strong><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></div><?php endif; ?>
                    </div>
                </aside>
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
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
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
.single-content{display:grid;grid-template-columns:1fr 350px;gap:48px}
.single-section{background:var(--brand-white);border-radius:var(--radius-lg);padding:var(--space-xl);margin-bottom:var(--space-xl)}
.single-section-title{font-size:var(--font-size-xl);font-weight:700;margin-bottom:var(--space-lg)}
.single-description{line-height:1.8;color:var(--gray-600)}
.sidebar-card{background:var(--brand-white);border-radius:var(--radius-lg);padding:var(--space-lg);margin-bottom:var(--space-lg);box-shadow:var(--shadow-sm)}
.sidebar-card-title{font-size:var(--font-size-lg);font-weight:600;margin-bottom:var(--space-md);color:var(--gray-800)}
.sidebar-address{color:var(--gray-600);margin-bottom:var(--space-md)}
.hours-list{display:flex;flex-direction:column;gap:8px}
.hours-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--gray-100)}
.hours-row.today{background:var(--brand-green-light);margin:0 -16px;padding:8px 16px;border-radius:var(--radius-sm);font-weight:600}
.hours-day{color:var(--gray-600)}
.hours-time{color:var(--gray-800)}
.contact-item{margin-bottom:12px}
.contact-item strong{display:block;font-size:var(--font-size-sm);color:var(--gray-500)}
.contact-item a{color:var(--brand-coral);font-weight:500}
.review-form{background:var(--gray-50);padding:var(--space-lg);border-radius:var(--radius-lg);margin-bottom:var(--space-xl)}
.rating-input{display:flex;gap:8px;margin-bottom:var(--space-md)}
.star-btn{cursor:pointer;color:var(--gray-300);transition:var(--transition)}
.star-btn:hover,.star-btn.active{color:var(--warning)}
.review-form .form-textarea{margin-bottom:var(--space-md)}
.review-item{padding:var(--space-lg) 0;border-bottom:1px solid var(--gray-100)}
.review-item:last-child{border-bottom:none}
.review-header{display:flex;align-items:center;gap:var(--space-md);margin-bottom:var(--space-sm)}
@media(max-width:900px){.single-content{grid-template-columns:1fr}.single-sidebar{order:-1}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof L !== 'undefined' && document.getElementById('map')) {
        const map = L.map('map').setView([19.4326, -99.1332], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; OpenStreetMap contributors'}).addTo(map);
        L.marker([<?php echo get_post_meta($negocio_id, 'cnmx_latitud', true) ?: '19.4326'; ?>, <?php echo get_post_meta($negocio_id, 'cnmx_longitud', true) ?: '-99.1332'; ?>]).addTo(map);
    }
});
</script>

<?php wp_footer(); ?>
</body>
</html>
