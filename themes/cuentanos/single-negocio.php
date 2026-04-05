<?php
/**
 * Single Negocio Template - Estilo Airbnb
 */

if (!defined('ABSPATH')) exit;

$negocio_id = get_the_ID();
$rating = get_post_meta($negocio_id, 'cnmx_rating', true) ?: 0;
$reviews_count = get_post_meta($negocio_id, 'cnmx_reviews_count', true) ?: 0;
$direccion = get_post_meta($negocio_id, 'cnmx_direccion', true) ?: '';
$telefono = get_post_meta($negocio_id, 'cnmx_telefono', true) ?: '';
$whatsapp = get_post_meta($negocio_id, 'cnmx_whatsapp', true) ?: '';
$email = get_post_meta($negocio_id, 'cnmx_email', true) ?: '';
$sitio_web = get_post_meta($negocio_id, 'cnmx_sitio_web', true) ?: '';
$horarios = get_post_meta($negocio_id, 'cnmx_horarios', true);
if (is_string($horarios)) { $horarios = json_decode($horarios, true) ?: []; }

$cats = get_the_terms($negocio_id, 'categoria');
$categoria = $cats ? $cats[0]->name : 'General';
$imagen_principal = get_the_post_thumbnail_url($negocio_id, 'full') ?: 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=1200&h=600&fit=crop';

$user_megafonos = 0;
$is_favorite = false;
if (is_user_logged_in()) {
    global $wpdb;
    $user_id = get_current_user_id();
    $meta = $wpdb->get_row($wpdb->prepare("SELECT megafonos FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d", $user_id));
    $user_megafonos = $meta ? $meta->megafonos : 0;
    $fav = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}cnmx_favoritos WHERE user_id = %d AND negocio_id = %d", $user_id, $negocio_id));
    $is_favorite = (bool)$fav;
}

$resenas = $wpdb->get_results($wpdb->prepare(
    "SELECT r.*, u.display_name as user_name FROM {$wpdb->prefix}cnmx_resenas r JOIN {$wpdb->prefix}users u ON u.ID = r.user_id WHERE r.negocio_id = %d AND r.status = 'aprobado' ORDER BY r.fecha DESC LIMIT 10",
    $negocio_id
));
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php the_title(); ?> - Cuentanos.mx</title>
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
.navbar .container {
    display: flex; align-items: center; justify-content: space-between; height: 72px;
}
.navbar-logo img { height: 36px; width: auto; }
.navbar-right { display: flex; align-items: center; gap: 12px; }
.btn-outline {
    padding: 10px 20px; border: 1px solid var(--border); border-radius: 100px;
    font-size: 14px; font-weight: 500; color: var(--text);
}
.btn-outline:hover { border-color: var(--text); }
.btn-primary {
    padding: 10px 20px; background: var(--primary); color: white;
    border-radius: 100px; font-size: 14px; font-weight: 600;
}
.btn-primary:hover { background: var(--primary-dark); }
.megafonos-badge {
    display: flex; align-items: center; gap: 6px; padding: 8px 16px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white; border-radius: 100px; font-size: 14px; font-weight: 700;
}

/* HERO IMAGE */
.single-hero {
    position: relative; height: 400px; overflow: hidden;
}
.single-hero img {
    width: 100%; height: 100%; object-fit: cover;
}
.single-hero-overlay {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    padding: 60px 24px 24px; color: white;
}
.single-hero-cat {
    display: inline-block; background: var(--primary); color: white;
    padding: 4px 12px; border-radius: 100px; font-size: 12px; font-weight: 600;
    margin-bottom: 12px;
}
.single-hero-title { font-size: 36px; font-weight: 800; margin-bottom: 8px; }
.single-hero-meta {
    display: flex; align-items: center; gap: 16px; font-size: 14px;
}
.stars { color: var(--secondary); }

/* ACTIONS */
.actions-bar {
    padding: 20px 0; border-bottom: 1px solid var(--border); background: white;
}
.actions-bar .container { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.action-btn {
    display: flex; align-items: center; gap: 8px; padding: 12px 20px;
    background: white; border: 1px solid var(--border); border-radius: var(--radius-sm);
    font-size: 14px; font-weight: 500; color: var(--text); transition: var(--transition);
}
.action-btn:hover { border-color: var(--primary); color: var(--primary); }
.action-btn.active { border-color: var(--primary); color: var(--primary); background: #FFF3ED; }
.action-btn-whatsapp { background: #25D366; color: white; border-color: #25D366; }
.action-btn-whatsapp:hover { background: #128C7E; color: white; }

/* CONTENT */
.content-section { padding: 40px 0 80px; }
.content-grid { display: grid; grid-template-columns: 1fr 380px; gap: 48px; }

.section-title { font-size: 24px; font-weight: 700; margin-bottom: 20px; }
.description { color: var(--text-light); line-height: 1.8; }

/* REVIEWS */
.reviews-section { margin-top: 40px; }
.section-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 24px;
}
.btn-write-review {
    padding: 10px 20px; background: var(--primary); color: white;
    border-radius: var(--radius-sm); font-size: 14px; font-weight: 600;
}
.btn-write-review:hover { background: var(--primary-dark); }

.review-form {
    background: var(--surface); padding: 24px; border-radius: var(--radius);
    margin-bottom: 24px; display: none;
}
.review-form.active { display: block; }
.rating-input { display: flex; gap: 8px; margin-bottom: 16px; }
.star-btn { color: var(--border); font-size: 28px; transition: var(--transition); }
.star-btn:hover, .star-btn.active { color: var(--secondary); }
.review-textarea {
    width: 100%; padding: 16px; border: 1px solid var(--border);
    border-radius: var(--radius-sm); font-size: 15px; resize: vertical;
    min-height: 120px; margin-bottom: 16px;
}
.review-textarea:focus { outline: none; border-color: var(--primary); }

.reviews-list { display: flex; flex-direction: column; gap: 20px; }
.review-card {
    padding: 20px; background: var(--cream); border-radius: var(--radius);
}
.review-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.review-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white; display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 16px;
}
.review-info { flex: 1; }
.review-name { font-weight: 600; display: block; }
.review-date { font-size: 13px; color: var(--text-muted); }
.review-stars { color: var(--secondary); font-size: 14px; }
.review-content { color: var(--text-light); line-height: 1.6; }

/* SIDEBAR */
.sidebar { display: flex; flex-direction: column; gap: 24px; }
.sidebar-card {
    background: white; border-radius: var(--radius); padding: 24px;
    box-shadow: var(--shadow);
}
.sidebar-card h3 { font-size: 18px; font-weight: 700; margin-bottom: 16px; }

.map-container { height: 200px; border-radius: var(--radius-sm); overflow: hidden; margin-top: 12px; }

.hours-list { display: flex; flex-direction: column; gap: 8px; }
.hours-row {
    display: flex; justify-content: space-between; padding: 10px 0;
    border-bottom: 1px solid var(--border); font-size: 14px;
}
.hours-row:last-child { border-bottom: none; }
.hours-row.today { background: #FFF3ED; margin: 0 -24px; padding: 10px 24px; border-radius: var(--radius-sm); font-weight: 600; }
.hours-day { color: var(--text-light); }
.hours-time { color: var(--text); }

.contact-list { display: flex; flex-direction: column; gap: 12px; }
.contact-item { display: flex; align-items: center; gap: 12px; }
.contact-item svg { color: var(--primary); flex-shrink: 0; }
.contact-item a { color: var(--primary); font-weight: 500; font-size: 14px; }

/* FOOTER */
.site-footer { background: var(--text); color: white; padding: 40px 0 20px; }
.footer-content { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
.footer-content p { color: rgba(255,255,255,0.7); font-size: 14px; }
.footer-links { display: flex; gap: 24px; }
.footer-links a { color: rgba(255,255,255,0.7); font-size: 14px; }
.footer-links a:hover { color: white; }
.footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); margin-top: 20px; padding-top: 20px; text-align: center; }
.footer-bottom p { color: rgba(255,255,255,0.5); font-size: 13px; }

/* RESPONSIVE */
@media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }
@media (max-width: 768px) {
    .single-hero { height: 300px; }
    .single-hero-title { font-size: 28px; }
    .actions-bar .container { gap: 8px; }
    .action-btn { padding: 10px 14px; font-size: 13px; }
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
    <!-- HERO IMAGE -->
    <section class="single-hero">
        <img src="<?php echo esc_url($imagen_principal); ?>" alt="<?php the_title_attribute(); ?>">
        <div class="single-hero-overlay">
            <div class="container">
                <span class="single-hero-cat"><?php echo esc_html($categoria); ?></span>
                <h1 class="single-hero-title"><?php the_title(); ?></h1>
                <div class="single-hero-meta">
                    <span class="stars"><?php for($i=0;$i<5;$i++) echo $i<floor($rating)?'★':'☆'; ?></span>
                    <span><?php echo number_format($rating, 1); ?> (<?php echo $reviews_count; ?> reseñas)</span>
                    <?php if ($direccion): ?><span>📍 <?php echo esc_html($direccion); ?></span><?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <!-- ACTIONS -->
    <div class="actions-bar">
        <div class="container">
            <button class="action-btn <?php echo $is_favorite ? 'active' : ''; ?>" id="btn-fav" data-id="<?php echo $negocio_id; ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="<?php echo $is_favorite ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                <?php echo $is_favorite ? 'Guardado' : 'Guardar'; ?>
            </button>
            
            <?php if ($telefono): ?>
                <a href="tel:<?php echo esc_attr($telefono); ?>" class="action-btn">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72"/></svg>
                    <?php echo esc_html($telefono); ?>
                </a>
            <?php endif; ?>
            
            <?php if ($whatsapp): ?>
                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $whatsapp); ?>" target="_blank" class="action-btn action-btn-whatsapp">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                    WhatsApp
                </a>
            <?php endif; ?>
            
            <?php if ($sitio_web): ?>
                <a href="<?php echo esc_url($sitio_web); ?>" target="_blank" class="action-btn">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10"/></svg>
                    Sitio Web
                </a>
            <?php endif; ?>
            
            <a href="https://maps.google.com/?q=<?php echo urlencode($direccion); ?>" target="_blank" class="action-btn">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Ver en mapa
            </a>
        </div>
    </div>
    
    <!-- CONTENT -->
    <section class="content-section">
        <div class="container">
            <div class="content-grid">
                <div class="main-content">
                    <!-- About -->
                    <div class="section-block">
                        <h2 class="section-title">Acerca de <?php the_title(); ?></h2>
                        <p class="description"><?php the_content(); ?></p>
                    </div>
                    
                    <!-- Reviews -->
                    <div class="reviews-section">
                        <div class="section-header">
                            <h2 class="section-title">Reseñas (<?php echo $reviews_count; ?>)</h2>
                            <?php if (is_user_logged_in()): ?>
                                <button class="btn-write-review" id="btn-toggle-review">Escribir reseña</button>
                            <?php else: ?>
                                <a href="<?php echo home_url('/registro'); ?>" class="btn-outline" style="padding: 10px 16px;">Inicia sesión para reseñar</a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (is_user_logged_in()): ?>
                        <div class="review-form" id="review-form">
                            <div class="rating-input" id="rating-input">
                                <?php for($i=1;$i<=5;$i++): ?>
                                    <button class="star-btn" data-rating="<?php echo $i; ?>">★</button>
                                <?php endfor; ?>
                            </div>
                            <textarea class="review-textarea" id="review-text" placeholder="Cuéntanos tu experiencia..."></textarea>
                            <button class="btn-write-review" id="btn-submit-review" data-negocio="<?php echo $negocio_id; ?>">Enviar Reseña</button>
                        </div>
                        <?php endif; ?>
                        
                        <div class="reviews-list">
                            <?php if (empty($resenas)): ?>
                                <div style="text-align:center; padding: 40px; color: var(--text-muted);">
                                    <p style="font-size: 48px; margin-bottom: 12px;">⭐</p>
                                    <p>No hay reseñas aún. ¡Sé el primero!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($resenas as $resena): ?>
                                    <div class="review-card">
                                        <div class="review-header">
                                            <div class="review-avatar"><?php echo substr($resena->user_name, 0, 1); ?></div>
                                            <div class="review-info">
                                                <span class="review-name"><?php echo esc_html($resena->user_name); ?></span>
                                                <span class="review-date"><?php echo date('d M Y', strtotime($resena->fecha)); ?></span>
                                            </div>
                                            <div class="review-stars"><?php for($i=0;$i<5;$i++) echo $i<$resena->calificacion?'★':'☆'; ?></div>
                                        </div>
                                        <p class="review-content"><?php echo esc_html($resena->contenido); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- SIDEBAR -->
                <aside class="sidebar">
                    <?php if ($direccion): ?>
                    <div class="sidebar-card">
                        <h3>📍 Ubicación</h3>
                        <p style="color: var(--text-light); font-size: 14px; margin-bottom: 12px;"><?php echo esc_html($direccion); ?></p>
                        <a href="https://maps.google.com/?q=<?php echo urlencode($direccion); ?>" target="_blank" class="action-btn" style="width: 100%; justify-content: center;">
                            Ver en Google Maps
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="sidebar-card">
                        <h3>🕐 Horario</h3>
                        <div class="hours-list">
                            <?php
                            $dias = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
                            $labels = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
                            $hoy = strtolower(date('l'));
                            foreach($dias as $i => $dia):
                                $horario = $horarios[$dia] ?? '9:00 - 18:00';
                            ?>
                                <div class="hours-row <?php echo $dia === $hoy ? 'today' : ''; ?>">
                                    <span class="hours-day"><?php echo $labels[$i]; ?></span>
                                    <span class="hours-time"><?php echo esc_html($horario); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="sidebar-card">
                        <h3>📞 Contacto</h3>
                        <div class="contact-list">
                            <?php if($telefono): ?>
                                <div class="contact-item">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3"/></svg>
                                    <a href="tel:<?php echo esc_attr($telefono); ?>"><?php echo esc_html($telefono); ?></a>
                                </div>
                            <?php endif; ?>
                            <?php if($whatsapp): ?>
                                <div class="contact-item">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $whatsapp); ?>" target="_blank">WhatsApp</a>
                                </div>
                            <?php endif; ?>
                            <?php if($email): ?>
                                <div class="contact-item">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Toggle favorite
    document.getElementById('btn-fav')?.addEventListener('click', function() {
        const btn = this;
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
                btn.innerHTML = btn.classList.contains('active') ? '❤️ Guardado' : '🤍 Guardar';
            }
        });
    });
    
    // Toggle review form
    document.getElementById('btn-toggle-review')?.addEventListener('click', function() {
        document.getElementById('review-form').classList.toggle('active');
    });
    
    // Star rating
    document.querySelectorAll('.star-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const rating = this.dataset.rating;
            document.querySelectorAll('.star-btn').forEach((b, i) => {
                b.classList.toggle('active', i < rating);
            });
            document.getElementById('rating-input').dataset.rating = rating;
        });
    });
    
    // Submit review
    document.getElementById('btn-submit-review')?.addEventListener('click', function() {
        const rating = document.getElementById('rating-input').dataset.rating || 0;
        const contenido = document.getElementById('review-text').value;
        const negocioId = this.dataset.negocio;
        
        if (!rating || !contenido) {
            cnmxToastError('Selecciona una calificación y escribe tu reseña');
            return;
        }
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=cnmx_guardar_resena', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'negocio_id=' + negocioId + '&rating=' + rating + '&texto=' + encodeURIComponent(contenido) + '&nonce=<?php echo wp_create_nonce('cnmx_nonce'); ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                cnmxToastSuccess('¡Reseña publicada!');
                setTimeout(() => location.reload(), 1500);
            } else {
                cnmxToastError(data.data || 'Error al guardar la reseña');
            }
        })
        .catch(err => {
            cnmxToastError('Error de conexión');
        });
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
