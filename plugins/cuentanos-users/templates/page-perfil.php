<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php 
if (!is_user_logged_in()) {
    wp_redirect(home_url('/mi-cuenta'));
    exit;
}

$user_id = get_current_user_id();
$user = wp_get_current_user();
$avatar = get_avatar_url($user_id, ['size' => 120]);
$megafonos = 0;
$nivel = 'explorador';

global $wpdb;
$meta_table = $wpdb->prefix . 'cnmx_usuarios_meta';
$meta = $wpdb->get_row($wpdb->prepare("SELECT * FROM $meta_table WHERE user_id = %d", $user_id));
if ($meta) {
    $megafonos = $meta->megafonos;
    $nivel = $meta->nivel;
}

// Stats
$favoritos_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}cnmx_favoritos WHERE user_id = %d", $user_id));
$resenas_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}cnmx_resenas WHERE user_id = %d", $user_id));
$logros_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}cnmx_usuarios_logros WHERE user_id = %d", $user_id));

$nivel_icons = [
    'explorador' => '🔍',
    'principiante' => '🌱',
    'intermedio' => '⭐',
    'avanzado' => '🌟',
    'experto' => '🏆',
    'leyenda' => '👑'
];

$nivel_labels = [
    'explorador' => 'Explorador',
    'principiante' => 'Principiante',
    'intermedio' => 'Intermedio',
    'avanzado' => 'Avanzado',
    'experto' => 'Experto',
    'leyenda' => 'Leyenda'
];

$next_nivel_megafonos = [
    'explorador' => 50,
    'principiante' => 100,
    'intermedio' => 200,
    'avanzado' => 500,
    'experto' => 1000,
    'leyenda' => 999999
];
?>

<style>
:root {
    --primary: #EB510C;
    --primary-dark: #D44A0B;
    --bg: #FFFCF8;
    --card: #FFFFFF;
    --text: #1A1A1A;
    --text-light: #717171;
    --text-muted: #9CA3AF;
    --border: #E5E7EB;
    --radius: 16px;
    --shadow: 0 1px 3px rgba(0,0,0,0.08);
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg);
    color: var(--text);
    line-height: 1.5;
}

/* Header */
.profile-header {
    background: var(--card);
    border-bottom: 1px solid var(--border);
    padding: 16px 0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.profile-header-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.profile-logo {
    display: flex;
    align-items: center;
    gap: 8px;
}

.profile-logo img {
    height: 32px;
}

.profile-logo span {
    font-weight: 600;
    font-size: 18px;
}

.profile-nav {
    display: flex;
    align-items: center;
    gap: 16px;
}

.profile-nav a {
    color: var(--text-light);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: color 0.2s;
}

.profile-nav a:hover {
    color: var(--text);
}

/* Main */
.profile-main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 48px 24px;
}

/* Hero Section */
.profile-hero {
    background: linear-gradient(135deg, var(--primary) 0%, #FF7B3D 100%);
    border-radius: var(--radius);
    padding: 48px;
    color: white;
    margin-bottom: 48px;
    position: relative;
    overflow: hidden;
}

.profile-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.profile-hero-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 32px;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 4px solid white;
    overflow: hidden;
    background: white;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info h1 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 4px;
}

.profile-info p {
    opacity: 0.9;
    font-size: 14px;
}

.profile-stats {
    display: flex;
    gap: 32px;
    margin-top: 24px;
}

.profile-stat {
    text-align: center;
}

.profile-stat-value {
    font-size: 28px;
    font-weight: 700;
}

.profile-stat-label {
    font-size: 12px;
    opacity: 0.8;
}

/* Megafonos Card */
.megafonos-card {
    background: rgba(255,255,255,0.15);
    border-radius: 12px;
    padding: 24px;
    backdrop-filter: blur(10px);
    margin-left: auto;
    text-align: center;
}

.megafonos-card .icon {
    font-size: 40px;
    display: block;
    margin-bottom: 8px;
}

.megafonos-card .count {
    font-size: 36px;
    font-weight: 700;
}

.megafonos-card .label {
    font-size: 12px;
    opacity: 0.8;
}

.megafonos-card .nivel {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 8px;
}

/* Grid */
.profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .profile-hero-content {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-stats {
        justify-content: center;
    }
    
    .megafonos-card {
        margin: 24px auto 0;
    }
}

/* Cards */
.profile-card {
    background: var(--card);
    border-radius: var(--radius);
    padding: 24px;
    box-shadow: var(--shadow);
}

.profile-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.profile-card-title {
    font-size: 18px;
    font-weight: 600;
}

.profile-card-action {
    color: var(--primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
}

.profile-card-action:hover {
    text-decoration: underline;
}

/* Favorites List */
.favorites-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.favorite-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--bg);
    border-radius: 12px;
    transition: all 0.2s;
    text-decoration: none;
    color: var(--text);
}

.favorite-item:hover {
    background: var(--border);
}

.favorite-img {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    object-fit: cover;
    background: var(--border);
}

.favorite-info {
    flex: 1;
}

.favorite-name {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
}

.favorite-cat {
    font-size: 12px;
    color: var(--text-light);
}

.favorite-rating {
    font-size: 12px;
    color: #F59E0B;
}

/* Reviews List */
.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.review-item {
    padding: 16px;
    background: var(--bg);
    border-radius: 12px;
}

.review-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.review-negocio {
    font-weight: 600;
    font-size: 14px;
    color: var(--text);
    text-decoration: none;
}

.review-stars {
    color: #F59E0B;
    font-size: 12px;
}

.review-text {
    font-size: 14px;
    color: var(--text-light);
    line-height: 1.5;
}

.review-date {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 8px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 32px;
    color: var(--text-light);
}

.empty-state .icon {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.empty-state p {
    font-size: 14px;
}

/* Bottom Nav */
.profile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--card);
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: space-around;
    padding: 12px 0;
}

.bottom-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    color: var(--text-light);
    text-decoration: none;
    font-size: 11px;
    transition: color 0.2s;
}

.bottom-nav-item.active,
.bottom-nav-item:hover {
    color: var(--primary);
}

.bottom-nav-item .icon {
    font-size: 20px;
}

@media (min-width: 769px) {
    .profile-bottom-nav {
        display: none;
    }
    
    .profile-main {
        padding-bottom: 100px;
    }
}
</style>

<header class="profile-header">
    <div class="profile-header-inner">
        <a href="<?php echo home_url(); ?>" class="profile-logo">
            <img src="https://cuentanos.mx/wp-content/uploads/2026/04/LOGO-PRINCIPAL.png" alt="Cuentanos">
        </a>
        <nav class="profile-nav">
            <a href="<?php echo home_url(); ?>">Explorar</a>
            <a href="<?php echo wp_logout_url(home_url()); ?>">Cerrar sesión</a>
        </nav>
    </div>
</header>

<main class="profile-main">
    <section class="profile-hero">
        <div class="profile-hero-content">
            <div class="profile-avatar">
                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($user->display_name); ?>">
            </div>
            <div class="profile-info">
                <h1><?php echo esc_html($user->display_name); ?></h1>
                <p><?php echo esc_html($user->user_email); ?></p>
                <div class="profile-stats">
                    <div class="profile-stat">
                        <div class="profile-stat-value"><?php echo number_format($favoritos_count); ?></div>
                        <div class="profile-stat-label">Favoritos</div>
                    </div>
                    <div class="profile-stat">
                        <div class="profile-stat-value"><?php echo number_format($resenas_count); ?></div>
                        <div class="profile-stat-label">Reseñas</div>
                    </div>
                </div>
            </div>
            <div class="megafonos-card">
                <span class="icon">📣</span>
                <div class="count"><?php echo number_format($megafonos); ?></div>
                <div class="label">Megáfonos</div>
                <span class="nivel"><?php echo $nivel_icons[$nivel]; ?> <?php echo $nivel_labels[$nivel]; ?></span>
            </div>
        </div>
    </section>

    <div class="profile-grid">
        <div class="profile-card">
            <div class="profile-card-header">
                <h2 class="profile-card-title">❤️ Favoritos</h2>
                <a href="#" class="profile-card-action">Ver todos</a>
            </div>
            <div class="favorites-list" id="favorites-list">
                <div class="empty-state">
                    <div class="icon">❤️</div>
                    <p>No tienes favoritos aún</p>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-card-header">
                <h2 class="profile-card-title">⭐ Mis Reseñas</h2>
                <a href="#" class="profile-card-action">Ver todas</a>
            </div>
            <div class="reviews-list" id="reviews-list">
                <div class="empty-state">
                    <div class="icon">⭐</div>
                    <p>No has escrito reseñas aún</p>
                </div>
            </div>
        </div>
    </div>
</main>

<nav class="profile-bottom-nav">
    <a href="<?php echo home_url(); ?>" class="bottom-nav-item">
        <span class="icon">🏠</span>
        <span>Inicio</span>
    </a>
    <a href="<?php echo home_url('/negocio'); ?>" class="bottom-nav-item">
        <span class="icon">🔍</span>
        <span>Explorar</span>
    </a>
    <a href="#" class="bottom-nav-item">
        <span class="icon">❤️</span>
        <span>Favoritos</span>
    </a>
    <a href="#" class="bottom-nav-item active">
        <span class="icon">👤</span>
        <span>Perfil</span>
    </a>
</nav>

<script>
// Load favorites
fetch('/wp-json/cuentanos/v1/favoritos', {
    headers: {
        'X-WP-Nonce': cnmxUsersData.nonce
    }
})
.then(r => r.json())
.then(data => {
    const list = document.getElementById('favorites-list');
    if (data.favoritos && data.favoritos.length > 0) {
        list.innerHTML = data.favoritos.slice(0, 3).map(fav => `
            <a href="${fav.post_url}" class="favorite-item">
                <div class="favorite-img" style="background: linear-gradient(135deg, #EB510C, #F89D2F); display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">📍</div>
                <div class="favorite-info">
                    <div class="favorite-name">${fav.post_title}</div>
                    <div class="favorite-cat">Negocio local</div>
                </div>
            </a>
        `).join('');
    }
})
.catch(() => {});

// Load reviews
fetch('/wp-json/cuentanos/v1/usuario/resenas', {
    headers: {
        'X-WP-Nonce': cnmxUsersData.nonce
    }
})
.then(r => r.json())
.then(data => {
    const list = document.getElementById('reviews-list');
    if (data.resenas && data.resenas.length > 0) {
        list.innerHTML = data.resenas.slice(0, 3).map(res => `
            <div class="review-item">
                <div class="review-header">
                    <a href="${res.negocio_link}" class="review-negocio">${res.negocio_nombre}</a>
                    <span class="review-stars">${'★'.repeat(res.calificacion)}${'☆'.repeat(5-res.calificacion)}</span>
                </div>
                <p class="review-text">${res.contenido}</p>
            </div>
        `).join('');
    }
})
.catch(() => {});
</script>

<?php wp_footer(); ?>
</body>
</html>
