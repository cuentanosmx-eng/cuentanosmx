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

global $wpdb;

// Get user data
$meta_table = $wpdb->prefix . 'cnmx_usuarios_meta';
$meta = $wpdb->get_row($wpdb->prepare("SELECT * FROM $meta_table WHERE user_id = %d", $user_id));
$megafonos = $meta ? $meta->megafonos : 0;
$nivel_actual = $meta ? $meta->nivel : 'explorador';

// Get user achievements/logros
$user_logros = $wpdb->get_col($wpdb->prepare(
    "SELECT logro_id FROM {$wpdb->prefix}cnmx_usuarios_logros WHERE user_id = %d",
    $user_id
));

// Get user rewards
$user_recompensas = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}cnmx_usuarios_recompensas WHERE user_id = %d",
    $user_id
));

// Stats
$favoritos_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}cnmx_favoritos WHERE user_id = %d", $user_id));
$resenas_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}cnmx_resenas WHERE user_id = %d", $user_id));

// Get favorites list directly from PHP
$favoritos_lista = $wpdb->get_results($wpdb->prepare(
    "SELECT f.*, p.post_title, p.guid as post_url 
     FROM {$wpdb->prefix}cnmx_favoritos f 
     LEFT JOIN {$wpdb->posts} p ON p.ID = f.negocio_id 
     WHERE f.user_id = %d 
     ORDER BY f.fecha DESC",
    $user_id
));

// Get negocio data for each favorite
foreach ($favoritos_lista as &$fav) {
    if ($fav->negocio_id) {
        $cats = get_the_terms($fav->negocio_id, 'categoria');
        $fav->categoria = $cats && !is_wp_error($cats) ? $cats[0]->name : 'General';
        $fav->imagen = get_the_post_thumbnail_url($fav->negocio_id, 'thumbnail') ?: '';
    }
}

// Level config
$niveles = [
    'explorador' => ['min' => 0, 'max' => 50, 'icon' => '🔍', 'label' => 'Explorador', 'color' => '#6B7280'],
    'principiante' => ['min' => 50, 'max' => 100, 'icon' => '🌱', 'label' => 'Principiante', 'color' => '#10B981'],
    'intermedio' => ['min' => 100, 'max' => 200, 'icon' => '⭐', 'label' => 'Intermedio', 'color' => '#F59E0B'],
    'avanzado' => ['min' => 200, 'max' => 500, 'icon' => '🌟', 'label' => 'Avanzado', 'color' => '#3B82F6'],
    'experto' => ['min' => 500, 'max' => 1000, 'icon' => '🏆', 'label' => 'Experto', 'color' => '#8B5CF6'],
    'leyenda' => ['min' => 1000, 'max' => 99999, 'icon' => '👑', 'label' => 'Leyenda', 'color' => '#EB510C']
];

// Get next level
$current_nivel_data = $niveles[$nivel_actual] ?? $niveles['explorador'];
$next_level = null;
$next_level_megafonos = $current_nivel_data['max'];
foreach ($niveles as $key => $data) {
    if ($data['min'] > $megafonos) {
        $next_level = ['key' => $key, 'data' => $data];
        break;
    }
}

// Progress percentage
$progress_in_level = 0;
if ($next_level) {
    $progress_in_level = (($megafonos - $current_nivel_data['min']) / ($next_level['data']['min'] - $current_nivel_data['min'])) * 100;
} else {
    $progress_in_level = 100;
}

// Get all rewards from CPT
$recompensas_posts = get_posts([
    'post_type' => 'cnmx_recompensa',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'meta_value_num',
    'meta_key' => 'cnmx_recompensa_megafonos',
]);

// Get all logros from CPT
$logros_posts = get_posts([
    'post_type' => 'cnmx_logro',
    'post_status' => 'publish',
    'posts_per_page' => -1,
]);

// Get user reviews from custom table
$user_resenas = $wpdb->get_results($wpdb->prepare(
    "SELECT r.*, p.post_title as negocio_nombre, p.guid as negocio_url 
     FROM {$wpdb->prefix}cnmx_resenas r 
     LEFT JOIN {$wpdb->prefix}posts p ON p.ID = r.negocio_id 
     WHERE r.user_id = %d 
     ORDER BY r.fecha DESC 
     LIMIT 20",
    $user_id
));
?>

<style>
:root {
    --primary: #EB510C;
    --primary-light: #FF7B3D;
    --secondary: #F89D2F;
    --bg: #FFFCF8;
    --card: #FFFFFF;
    --text: #1A1A1A;
    --text-light: #717171;
    --text-muted: #9CA3AF;
    --border: #E5E7EB;
    --success: #10B981;
    --warning: #F59E0B;
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

.profile-logo img {
    height: 32px;
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
}

/* Main */
.profile-main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 24px 120px;
}

/* Progress Header */
.progress-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 20px;
    padding: 32px;
    color: white;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.progress-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.progress-header-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 32px;
    flex-wrap: wrap;
}

.avatar-section {
    text-align: center;
}

.avatar-section img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid white;
    object-fit: cover;
}

.avatar-section .user-name {
    font-size: 16px;
    font-weight: 600;
    margin-top: 8px;
}

.avatar-section .user-level {
    font-size: 12px;
    opacity: 0.9;
}

.progress-info {
    flex: 1;
    min-width: 280px;
}

.progress-points {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 16px;
}

.progress-points .icon {
    font-size: 32px;
}

.progress-points .value {
    font-size: 48px;
    font-weight: 700;
    line-height: 1;
}

.progress-points .label {
    font-size: 14px;
    opacity: 0.9;
}

.progress-bar-container {
    background: rgba(255,255,255,0.2);
    border-radius: 10px;
    height: 20px;
    position: relative;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: white;
    border-radius: 10px;
    transition: width 0.5s ease;
    min-width: 4px;
}

.progress-markers {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    font-size: 12px;
    opacity: 0.8;
}

.progress-next {
    text-align: center;
    min-width: 160px;
}

.progress-next .level-icon {
    font-size: 40px;
}

.progress-next .level-name {
    font-size: 14px;
    font-weight: 600;
}

.progress-next .level-distance {
    font-size: 12px;
    opacity: 0.9;
    margin-top: 4px;
}

.progress-next .level-distance span {
    font-weight: 700;
}

/* Sections */
.section {
    background: var(--card);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title .icon {
    font-size: 24px;
}

.section-link {
    color: var(--primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
}

/* Rewards Grid */
.rewards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 16px;
}

.reward-card {
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s;
    position: relative;
}

.reward-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.reward-card.locked {
    opacity: 0.6;
}

.reward-card.locked .reward-image {
    filter: grayscale(100%);
}

.reward-card.locked .reward-lock {
    display: flex;
}

.reward-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
    background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
}

.reward-lock {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 32px;
    height: 32px;
    background: rgba(0,0,0,0.6);
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.reward-content {
    padding: 12px;
}

.reward-name {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
    color: var(--text);
}

.reward-cost {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    color: var(--primary);
    font-weight: 600;
}

.reward-btn {
    display: block;
    width: 100%;
    padding: 8px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 8px;
    transition: background 0.2s;
}

.reward-btn:hover {
    background: #D44A0B;
}

.reward-btn:disabled {
    background: var(--border);
    color: var(--text-muted);
    cursor: not-allowed;
}

/* Achievements Grid */
.achievements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
}

.achievement-card {
    background: var(--bg);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    position: relative;
}

.achievement-card.unlocked {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.achievement-card.locked {
    opacity: 0.5;
}

.achievement-icon {
    font-size: 48px;
    margin-bottom: 12px;
}

.achievement-name {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
}

.achievement-desc {
    font-size: 12px;
    color: var(--text-light);
}

.achievement-megafonos {
    margin-top: 8px;
    font-size: 12px;
    color: var(--primary);
    font-weight: 600;
}

/* Reviews List */
.reviews-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
}

.review-card {
    background: var(--bg);
    border-radius: 12px;
    padding: 16px;
}

.review-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.review-business {
    font-weight: 600;
    color: var(--text);
    text-decoration: none;
}

.review-business:hover {
    color: var(--primary);
}

.review-stars {
    color: #F59E0B;
    font-size: 14px;
}

.review-body {
    font-size: 14px;
    color: var(--text-light);
    line-height: 1.5;
    margin-bottom: 8px;
}

.review-date {
    font-size: 12px;
    color: var(--text-muted);
}

/* Favorites List */
.favorites-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.favorite-card {
    display: flex;
    gap: 12px;
    padding: 12px;
    background: var(--bg);
    border-radius: 12px;
    text-decoration: none;
    color: var(--text);
    transition: all 0.2s;
}

.favorite-card:hover {
    background: var(--border);
}

.favorite-img {
    width: 64px;
    height: 64px;
    border-radius: 8px;
    object-fit: cover;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
}

.favorite-info {
    flex: 1;
    min-width: 0;
}

.favorite-name {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.favorite-cat {
    font-size: 12px;
    color: var(--text-light);
}

/* Stats */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.stat-card {
    background: var(--bg);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
}

.stat-icon {
    font-size: 28px;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--text);
}

.stat-label {
    font-size: 12px;
    color: var(--text-light);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 48px 24px;
    color: var(--text-light);
}

.empty-state .icon {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
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
    padding: 8px 0 12px;
    z-index: 100;
}

.bottom-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    color: var(--text-light);
    text-decoration: none;
    font-size: 10px;
    transition: color 0.2s;
}

.bottom-nav-item .icon {
    font-size: 22px;
}

.bottom-nav-item.active,
.bottom-nav-item:hover {
    color: var(--primary);
}

/* Tabs */
.tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    overflow-x: auto;
    padding-bottom: 4px;
}

.tab {
    padding: 10px 20px;
    background: var(--bg);
    border: none;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s;
}

.tab.active {
    background: var(--primary);
    color: white;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Responsive */
@media (max-width: 768px) {
    .progress-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .progress-info {
        width: 100%;
    }
    
    .progress-points {
        justify-content: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .profile-bottom-nav {
        display: flex;
    }
}

@media (min-width: 769px) {
    .profile-bottom-nav {
        display: none;
    }
}
</style>

<header class="profile-header">
    <div class="profile-header-inner">
        <a href="<?php echo home_url(); ?>" class="profile-logo">
            <img src="https://cuentanos.mx/wp-content/uploads/2026/04/LOGO-PRINCIPAL.png" alt="Cuentanos">
        </a>
        <nav class="profile-nav">
            <a href="<?php echo home_url(); ?>">Inicio</a>
            <a href="<?php echo home_url('/negocio'); ?>">Explorar</a>
            <a href="<?php echo wp_logout_url(home_url()); ?>">Cerrar sesión</a>
        </nav>
    </div>
</header>

<main class="profile-main">
    <!-- Progress Header -->
    <section class="progress-header">
        <div class="progress-header-content">
            <div class="avatar-section">
                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($user->display_name); ?>">
                <div class="user-name"><?php echo esc_html($user->display_name); ?></div>
                <div class="user-level"><?php echo $current_nivel_data['icon']; ?> <?php echo $current_nivel_data['label']; ?></div>
            </div>
            
            <div class="progress-info">
                <div class="progress-points">
                    <span class="icon">📣</span>
                    <span class="value"><?php echo number_format($megafonos); ?></span>
                    <span class="label">Megáfonos</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: <?php echo min(100, $progress_in_level); ?>%"></div>
                </div>
                <div class="progress-markers">
                    <span><?php echo $current_nivel_data['min']; ?></span>
                    <span><?php echo $next_level ? $next_level['data']['min'] : 'MAX'; ?></span>
                </div>
            </div>
            
            <?php if ($next_level): ?>
            <div class="progress-next">
                <div class="level-icon"><?php echo $next_level['data']['icon']; ?></div>
                <div class="level-name"><?php echo $next_level['data']['label']; ?></div>
                <div class="level-distance">Estás a <span><?php echo number_format($next_level['data']['min'] - $megafonos); ?></span> Megáfonos</div>
            </div>
            <?php else: ?>
            <div class="progress-next">
                <div class="level-icon">👑</div>
                <div class="level-name">¡Máximo Nivel!</div>
                <div class="level-distance">Eres una Leyenda</div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Tabs Navigation -->
    <div class="tabs">
        <button class="tab active" data-tab="resumen">Resumen</button>
        <button class="tab" data-tab="recompensas">Recompensas</button>
        <button class="tab" data-tab="logros">Logros</button>
        <button class="tab" data-tab="resenas">Reseñas</button>
        <button class="tab" data-tab="favoritos">Favoritos</button>
    </div>

    <!-- Resumen Tab -->
    <div class="tab-content active" id="tab-resumen">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📣</div>
                <div class="stat-value"><?php echo number_format($megafonos); ?></div>
                <div class="stat-label">Megáfonos</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">❤️</div>
                <div class="stat-value"><?php echo number_format($favoritos_count); ?></div>
                <div class="stat-label">Favoritos</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⭐</div>
                <div class="stat-value"><?php echo number_format($resenas_count); ?></div>
                <div class="stat-label">Reseñas</div>
            </div>
        </div>
    </div>

    <!-- Rewards Tab -->
    <div class="tab-content" id="tab-recompensas">
        <section class="section">
            <div class="section-header">
                <h2 class="section-title"><span class="icon">🎁</span> Recompensas Disponibles</h2>
            </div>
            <?php if (!empty($recompensas_posts)): ?>
            <div class="rewards-grid">
                <?php foreach ($recompensas_posts as $rec): 
                    $costo = get_post_meta($rec->ID, 'cnmx_recompensa_megafonos', true) ?: 100;
                    $unlocked = $megafonos >= $costo;
                    $canjeado = in_array($rec->ID, array_column($user_recompensas, 'recompensa_id'));
                ?>
                <div class="reward-card <?php echo $unlocked ? '' : 'locked'; ?>">
                    <div class="reward-image">
                        <?php if ($canjeado): ?>
                        ✓
                        <?php elseif ($unlocked): ?>
                        🎁
                        <?php else: ?>
                        🔒
                        <?php endif; ?>
                    </div>
                    <div class="reward-lock">🔒</div>
                    <div class="reward-content">
                        <div class="reward-name"><?php echo esc_html($rec->post_title); ?></div>
                        <div class="reward-cost">
                            <span>📣</span> <?php echo number_format($costo); ?>
                        </div>
                        <?php if ($canjeado): ?>
                        <button class="reward-btn" disabled>✓ Canjeado</button>
                        <?php elseif ($unlocked): ?>
                        <button class="reward-btn" onclick="canjearRecompensa(<?php echo $rec->ID; ?>, <?php echo $costo; ?>)">Canjear</button>
                        <?php else: ?>
                        <button class="reward-btn" disabled>Bloqueado</button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="icon">🎁</div>
                <p>Próximamente habrá recompensas disponibles</p>
            </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Achievements Tab -->
    <div class="tab-content" id="tab-logros">
        <section class="section">
            <div class="section-header">
                <h2 class="section-title"><span class="icon">🏆</span> Mis Logros</h2>
            </div>
            <?php if (!empty($logros_posts)): ?>
            <div class="achievements-grid">
                <?php foreach ($logros_posts as $logro): 
                    $unlocked = in_array($logro->ID, $user_logros);
                    $megafonos_otorga = get_post_meta($logro->ID, 'cnmx_logro_megafonos', true) ?: 10;
                    $logro_icono = get_post_meta($logro->ID, 'cnmx_logro_icono', true) ?: ($unlocked ? '🏆' : '🔒');
                ?>
                <div class="achievement-card <?php echo $unlocked ? 'unlocked' : 'locked'; ?>">
                    <div class="achievement-status"><?php echo $unlocked ? '✅' : '🔒'; ?></div>
                    <div class="achievement-icon"><?php echo esc_html($logro_icono); ?></div>
                    <div class="achievement-name"><?php echo esc_html($logro->post_title); ?></div>
                    <div class="achievement-desc"><?php echo esc_html($logro->post_content ?: 'Completa acciones para desbloquear'); ?></div>
                    <div class="achievement-megafonos">📣 +<?php echo number_format($megafonos_otorga); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="icon">🏆</div>
                <p>Próximamente habrá logros disponibles</p>
            </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Reviews Tab -->
    <div class="tab-content" id="tab-resenas">
        <section class="section">
            <div class="section-header">
                <h2 class="section-title"><span class="icon">⭐</span> Mis Reseñas</h2>
            </div>
            <?php if (!empty($user_resenas)): ?>
            <div class="reviews-grid">
                <?php foreach ($user_resenas as $resena): ?>
                <div class="review-card">
                    <div class="review-header">
                        <a href="<?php echo esc_url($resena->negocio_url); ?>" class="review-business">
                            <?php echo esc_html($resena->negocio_nombre ?: 'Negocio'); ?>
                        </a>
                        <span class="review-stars">
                            <?php echo str_repeat('★', $resena->calificacion); ?><?php echo str_repeat('☆', 5 - $resena->calificacion); ?>
                        </span>
                    </div>
                    <p class="review-body"><?php echo esc_html($resena->contenido); ?></p>
                    <span class="review-date"><?php echo date('d M Y', strtotime($resena->fecha)); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="icon">⭐</div>
                <p>No has escrito ninguna reseña aún</p>
                <a href="<?php echo home_url('/negocio'); ?>" style="color: var(--primary);">Explorar negocios y escribir mi primera reseña</a>
            </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Favorites Tab -->
    <div class="tab-content" id="tab-favoritos">
        <section class="section">
            <div class="section-header">
                <h2 class="section-title"><span class="icon">❤️</span> Mis Favoritos</h2>
            </div>
            <div class="favorites-list" id="favorites-list">
                <?php if (!empty($favoritos_lista)): ?>
                    <?php foreach ($favoritos_lista as $fav): ?>
                        <a href="<?php echo esc_url($fav->post_url ?: '/'); ?>" class="favorite-card">
                            <div class="favorite-img">
                                <?php if ($fav->imagen): ?>
                                    <img src="<?php echo esc_url($fav->imagen); ?>" alt="">
                                <?php else: ?>
                                    📍
                                <?php endif; ?>
                            </div>
                            <div class="favorite-info">
                                <div class="favorite-name"><?php echo esc_html($fav->post_title ?: 'Negocio'); ?></div>
                                <div class="favorite-cat"><?php echo esc_html($fav->categoria ?: 'Negocio local'); ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="icon">❤️</div>
                        <p>No tienes favoritos aún</p>
                        <a href="<?php echo home_url('/negocio'); ?>" style="color: var(--primary); margin-top: 12px; display: inline-block;">Explorar negocios</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
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
    <a href="#" class="bottom-nav-item" onclick="switchTab('recompensas')">
        <span class="icon">🎁</span>
        <span>Recompensas</span>
    </a>
    <a href="#" class="bottom-nav-item active" onclick="switchTab('resumen')">
        <span class="icon">👤</span>
        <span>Perfil</span>
    </a>
</nav>

<script>
// Tab switching
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const tabId = this.dataset.tab;
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + tabId).classList.add('active');
    });
});

function switchTab(tabId) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
    document.getElementById('tab-' + tabId).classList.add('active');
}

// Redeem reward


// Redeem reward
function canjearRecompensa(id, costo) {
    if (!confirm('¿Canjeas esta recompensa por ' + costo + ' Megáfonos?')) return;
    
    fetch('/wp-json/cuentanos/v1/recompensas/canjear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': cnmxUsersData.nonce
        },
        body: JSON.stringify({ recompensa_id: id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            cnmxToastSuccess('¡Recompensa canjeada!');
            location.reload();
        } else {
            cnmxToastError(data.message || 'Error al canjear');
        }
    })
    .catch(() => cnmxToastError('Error de conexión'));
}
</script>

<?php wp_footer(); ?>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/popups.css">
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/popups.js"></script>
<script>
window.cnmxUsersData = window.cnmxUsersData || { nonce: '<?php echo is_user_logged_in() ? wp_create_nonce('wp_rest') : ''; ?>' };
CNMX.Toast.init();
</script>
</body>
</html>
