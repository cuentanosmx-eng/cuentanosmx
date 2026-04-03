<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        .cnmx-profile-page { min-height: 100vh; background: #F7F7F7; }
        .cnmx-profile-header { background: white; padding: 16px 0; border-bottom: 1px solid #EBEBEB; }
        .cnmx-back-link { color: #717171; font-size: 14px; transition: all 0.2s; }
        .cnmx-back-link:hover { color: #222222; }
        .cnmx-profile-main { padding: 32px 0; }
        .cnmx-profile-content { display: grid; grid-template-columns: 280px 1fr; gap: 32px; max-width: 1200px; margin: 0 auto; }
        @media (max-width: 900px) { .cnmx-profile-content { grid-template-columns: 1fr; } }
        .cnmx-profile-sidebar { background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); height: fit-content; }
        .cnmx-profile-avatar-section { text-align: center; margin-bottom: 24px; }
        .cnmx-profile-avatar { position: relative; width: 120px; height: 120px; margin: 0 auto 16px; border-radius: 50%; overflow: hidden; }
        .cnmx-profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .cnmx-avatar-edit { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.6); color: white; border: none; padding: 8px; cursor: pointer; font-size: 12px; transition: all 0.2s; }
        .cnmx-avatar-edit:hover { background: rgba(0,0,0,0.8); }
        .cnmx-profile-name { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .cnmx-profile-email { font-size: 14px; color: #717171; }
        .cnmx-profile-nav { border-top: 1px solid #EBEBEB; padding-top: 24px; margin-top: 24px; }
        .cnmx-profile-nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; color: #717171; font-size: 14px; font-weight: 500; transition: all 0.2s; margin-bottom: 4px; }
        .cnmx-profile-nav-item:hover, .cnmx-profile-nav-item.active { background: #F7F7F7; color: #222222; }
        .cnmx-profile-nav-item.active { background: rgba(255,56,92,0.1); color: #FF385C; }
        .cnmx-logout-btn { display: block; text-align: center; margin-top: 24px; padding: 12px 16px; border-radius: 8px; background: #F7F7F7; color: #717171; font-size: 14px; font-weight: 500; transition: all 0.2s; }
        .cnmx-logout-btn:hover { background: #FF385C; color: white; }
        .cnmx-profile-section { display: none; background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .cnmx-profile-section.active { display: block; }
        .cnmx-profile-section h2 { font-size: 24px; font-weight: 700; margin-bottom: 24px; }
        .cnmx-profile-section h3 { font-size: 18px; font-weight: 600; margin: 32px 0 16px; }
        .cnmx-megafonos-card { background: linear-gradient(135deg, #FF385C 0%, #E31C5F 100%); color: white; border-radius: 16px; padding: 24px; margin-bottom: 24px; }
        .cnmx-megafonos-header { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
        .cnmx-megafonos-icon { font-size: 48px; }
        .cnmx-megafonos-label { font-size: 14px; opacity: 0.9; }
        .cnmx-megafonos-count { font-size: 36px; font-weight: 700; display: block; }
        .cnmx-nivel-info { display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.2); }
        .cnmx-nivel-badge { background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .cnmx-nivel-progress { font-size: 12px; opacity: 0.9; }
        .cnmx-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        @media (max-width: 640px) { .cnmx-stats-grid { grid-template-columns: 1fr; } }
        .cnmx-stat-card { background: #F7F7F7; border-radius: 12px; padding: 20px; text-align: center; transition: all 0.2s; }
        .cnmx-stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .cnmx-stat-icon { font-size: 28px; display: block; margin-bottom: 8px; }
        .cnmx-stat-value { font-size: 28px; font-weight: 700; display: block; margin-bottom: 4px; }
        .cnmx-stat-label { font-size: 13px; color: #717171; }
        .cnmx-favoritos-list, .cnmx-resenas-list { display: flex; flex-direction: column; gap: 16px; }
        .cnmx-fav-item, .cnmx-resena-item { display: flex; gap: 16px; padding: 16px; background: #F7F7F7; border-radius: 12px; transition: all 0.2s; }
        .cnmx-fav-item:hover, .cnmx-resena-item:hover { transform: translateX(4px); }
        .cnmx-fav-img { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; background: #E5E5E5; }
        .cnmx-fav-info, .cnmx-resena-info { flex: 1; }
        .cnmx-fav-name { font-weight: 600; margin-bottom: 4px; }
        .cnmx-fav-cat { font-size: 13px; color: #717171; }
        .cnmx-resena-negocio { font-weight: 600; margin-bottom: 4px; }
        .cnmx-resena-meta { font-size: 13px; color: #717171; display: flex; gap: 12px; }
        .cnmx-resena-text { margin-top: 8px; font-size: 14px; color: #444; }
        .cnmx-empty-state { text-align: center; padding: 48px 24px; color: #717171; font-size: 14px; background: #F7F7F7; border-radius: 12px; }
        .cnmx-logros-grid, .cnmx-recompensas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
        .cnmx-logro-card, .cnmx-recompensa-card { background: #F7F7F7; border-radius: 12px; padding: 20px; text-align: center; transition: all 0.2s; }
        .cnmx-logro-card:hover, .cnmx-recompensa-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
        .cnmx-logro-card.locked, .cnmx-recompensa-card.locked { opacity: 0.5; }
        .cnmx-logro-icon, .cnmx-recompensa-icon { font-size: 40px; margin-bottom: 12px; }
        .cnmx-logro-name, .cnmx-recompensa-name { font-weight: 600; margin-bottom: 4px; }
        .cnmx-logro-desc, .cnmx-recompensa-desc { font-size: 12px; color: #717171; }
        .cnmx-canje-btn { margin-top: 12px; padding: 8px 16px; background: #FF385C; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .cnmx-canje-btn:hover { background: #E31C5F; }
        .cnmx-canje-btn:disabled { background: #E5E5E5; color: #717171; cursor: not-allowed; }
        .cnmx-section-desc { color: #717171; margin-bottom: 24px; font-size: 14px; }
        .cnmx-ajustes-form { max-width: 400px; }
        .cnmx-form-group { margin-bottom: 20px; }
        .cnmx-form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; }
        .cnmx-form-group input { width: 100%; padding: 12px 16px; border: 1px solid #E5E5E5; border-radius: 8px; font-size: 14px; transition: all 0.2s; }
        .cnmx-form-group input:focus { outline: none; border-color: #FF385C; box-shadow: 0 0 0 3px rgba(255,56,92,0.1); }
        .cnmx-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; }
        .cnmx-btn-primary { background: #FF385C; color: white; }
        .cnmx-btn-primary:hover { background: #E31C5F; transform: translateY(-1px); }
        .cnmx-negocio-card { background: #F7F7F7; border-radius: 12px; padding: 24px; }
        .cnmx-negocio-header { display: flex; gap: 16px; margin-bottom: 16px; }
        .cnmx-negocio-img { width: 100px; height: 100px; border-radius: 8px; object-fit: cover; background: #E5E5E5; }
        .cnmx-negocio-nombre { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .cnmx-negocio-status { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .cnmx-negocio-status.aprobado { background: rgba(0,138,5,0.1); color: #008A05; }
        .cnmx-negocio-status.pendiente { background: rgba(255,180,0,0.1); color: #FFB400; }
        .cnmx-negocio-stats { display: flex; gap: 24px; padding-top: 16px; border-top: 1px solid #E5E5E5; }
        .cnmx-negocio-stat { text-align: center; }
        .cnmx-negocio-stat-val { font-size: 20px; font-weight: 700; }
        .cnmx-negocio-stat-label { font-size: 12px; color: #717171; }
        .cnmx-agregar-negocio { text-align: center; padding: 48px; }
        .cnmx-agregar-negocio p { color: #717171; margin-bottom: 16px; }
        .cnmx-stars { color: #FFB400; }
    </style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="cnmx-profile-page">
    <header class="cnmx-profile-header">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 24px;">
            <a href="<?php echo home_url(); ?>" class="cnmx-back-link">← Volver al inicio</a>
        </div>
    </header>
    
    <main class="cnmx-profile-main">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 24px;">
            <div class="cnmx-profile-content">
                <aside class="cnmx-profile-sidebar">
                    <div class="cnmx-profile-avatar-section">
                        <div class="cnmx-profile-avatar" id="cnmx-profile-avatar">
                            <?php echo get_avatar(get_current_user_id(), 120); ?>
                            <button class="cnmx-avatar-edit" id="cnmx-change-avatar">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                            </button>
                        </div>
                        <h2 class="cnmx-profile-name"><?php echo wp_get_current_user()->display_name; ?></h2>
                        <p class="cnmx-profile-email"><?php echo wp_get_current_user()->user_email; ?></p>
                    </div>
                    
                    <nav class="cnmx-profile-nav">
                        <a href="#" class="cnmx-profile-nav-item active" data-section="resumen">📊 Resumen</a>
                        <a href="#" class="cnmx-profile-nav-item" data-section="favoritos">❤️ Favoritos</a>
                        <a href="#" class="cnmx-profile-nav-item" data-section="resenas">⭐ Mis Reseñas</a>
                        <a href="#" class="cnmx-profile-nav-item" data-section="logros">🏆 Logros</a>
                        <a href="#" class="cnmx-profile-nav-item" data-section="recompensas">🎁 Recompensas</a>
                        <a href="#" class="cnmx-profile-nav-item" data-section="negocio">🏪 Mi Negocio</a>
                        <a href="#" class="cnmx-profile-nav-item" data-section="ajustes">⚙️ Ajustes</a>
                    </nav>
                    
                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="cnmx-logout-btn">🚪 Cerrar sesión</a>
                </aside>
                
                <section class="cnmx-profile-section active" id="section-resumen">
                    <h2>Tu Resumen</h2>
                    <div class="cnmx-megafonos-card">
                        <div class="cnmx-megafonos-header">
                            <span class="cnmx-megafonos-icon">🎤</span>
                            <div>
                                <span class="cnmx-megafonos-label">Tus Megáfonos</span>
                                <span class="cnmx-megafonos-count" id="cnmx-megafonos-count">0</span>
                            </div>
                        </div>
                        <div class="cnmx-nivel-info">
                            <span class="cnmx-nivel-badge" id="cnmx-nivel-badge">Explorador</span>
                            <span class="cnmx-nivel-progress" id="cnmx-nivel-progress">0/500 para siguiente nivel</span>
                        </div>
                    </div>
                    
                    <div class="cnmx-stats-grid">
                        <div class="cnmx-stat-card">
                            <span class="cnmx-stat-icon">⭐</span>
                            <span class="cnmx-stat-value" id="cnmx-stat-resenas">0</span>
                            <span class="cnmx-stat-label">Reseñas</span>
                        </div>
                        <div class="cnmx-stat-card">
                            <span class="cnmx-stat-icon">❤️</span>
                            <span class="cnmx-stat-value" id="cnmx-stat-favoritos">0</span>
                            <span class="cnmx-stat-label">Favoritos</span>
                        </div>
                        <div class="cnmx-stat-card">
                            <span class="cnmx-stat-icon">🏆</span>
                            <span class="cnmx-stat-value" id="cnmx-stat-logros">0</span>
                            <span class="cnmx-stat-label">Logros</span>
                        </div>
                    </div>
                </section>
                
                <section class="cnmx-profile-section" id="section-favoritos">
                    <h2>❤️ Mis Favoritos</h2>
                    <div class="cnmx-favoritos-list" id="cnmx-favoritos-list">
                        <div class="cnmx-empty-state">Cargando...</div>
                    </div>
                </section>
                
                <section class="cnmx-profile-section" id="section-resenas">
                    <h2>⭐ Mis Reseñas</h2>
                    <div class="cnmx-resenas-list" id="cnmx-resenas-list">
                        <div class="cnmx-empty-state">Cargando...</div>
                    </div>
                </section>
                
                <section class="cnmx-profile-section" id="section-logros">
                    <h2>🏆 Mis Logros</h2>
                    <div class="cnmx-logros-grid" id="cnmx-logros-grid">
                        <div class="cnmx-empty-state">Cargando...</div>
                    </div>
                </section>
                
                <section class="cnmx-profile-section" id="section-recompensas">
                    <h2>🎁 Recompensas Disponibles</h2>
                    <p class="cnmx-section-desc">Canjea tus Megáfonos por recompensas exclusivas</p>
                    <div class="cnmx-recompensas-grid" id="cnmx-recompensas-grid">
                        <div class="cnmx-empty-state">Cargando...</div>
                    </div>
                    
                    <h3>📜 Mis Códigos Canjeados</h3>
                    <div class="cnmx-canjeados-list" id="cnmx-canjeados-list">
                        <div class="cnmx-empty-state">Aún no has canjeado recompensas</div>
                    </div>
                </section>
                
                <section class="cnmx-profile-section" id="section-negocio">
                    <h2>🏪 Mi Negocio</h2>
                    <div id="cnmx-negocio-info">
                        <div class="cnmx-empty-state">Cargando...</div>
                    </div>
                </section>
                
                <section class="cnmx-profile-section" id="section-ajustes">
                    <h2>⚙️ Ajustes de Cuenta</h2>
                    <form class="cnmx-ajustes-form" id="cnmx-ajustes-form">
                        <div class="cnmx-form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" value="<?php echo wp_get_current_user()->display_name; ?>">
                        </div>
                        <div class="cnmx-form-group">
                            <label>Email</label>
                            <input type="email" value="<?php echo wp_get_current_user()->user_email; ?>" disabled>
                        </div>
                        <button type="submit" class="cnmx-btn cnmx-btn-primary">Guardar cambios</button>
                    </form>
                </section>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cnmx-profile-nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.dataset.section;
            
            document.querySelectorAll('.cnmx-profile-nav-item').forEach(i => i.classList.remove('active'));
            document.querySelectorAll('.cnmx-profile-section').forEach(s => s.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById('section-' + section).classList.add('active');
        });
    });
    
    loadUserData();
    loadFavoritos();
    loadResenas();
    loadLogros();
    loadRecompensas();
    loadNegocio();
    
    function loadUserData() {
        fetch('/wp-json/cuentanos/v1/usuario/me', {
            headers: { 'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('cnmx-megafonos-count').textContent = data.megafonos || 0;
            
            let nivel = 'Explorador';
            let progress = '0/500 para siguiente nivel';
            const mega = data.megafonos || 0;
            
            if (mega >= 1000) { nivel = 'Influencer'; progress = mega + '/∞ ¡Nivel máximo!'; }
            else if (mega >= 500) { nivel = 'Crítico'; progress = mega + '/1000 para Influencer'; }
            else { progress = mega + '/500 para Crítico'; }
            
            document.getElementById('cnmx-nivel-badge').textContent = nivel;
            document.getElementById('cnmx-nivel-progress').textContent = progress;
        })
        .catch(() => { document.getElementById('cnmx-megafonos-count').textContent = '0'; });
    }
    
    function loadFavoritos() {
        const container = document.getElementById('cnmx-favoritos-list');
        fetch('/wp-json/cuentanos/v1/favoritos', {
            headers: { 'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>' }
        })
        .then(r => r.ok ? r.json() : [])
        .then(data => {
            if (!data.length) { container.innerHTML = '<div class="cnmx-empty-state">No tienes favoritos aún</div>'; return; }
            container.innerHTML = data.map(f => `
                <div class="cnmx-fav-item">
                    <img src="${f.imagen || 'https://via.placeholder.com/80'}" class="cnmx-fav-img" alt="${f.nombre}">
                    <div class="cnmx-fav-info">
                        <a href="${f.link}" class="cnmx-fav-name">${f.nombre}</a>
                        <span class="cnmx-fav-cat">${f.categoria || 'Negocio'}</span>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => { container.innerHTML = '<div class="cnmx-empty-state">Error al cargar favoritos</div>'; });
    }
    
    function loadResenas() {
        const container = document.getElementById('cnmx-resenas-list');
        fetch('/wp-json/cuentanos/v1/usuario/resenas', {
            headers: { 'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>' }
        })
        .then(r => r.ok ? r.json() : [])
        .then(data => {
            if (!data.length) { container.innerHTML = '<div class="cnmx-empty-state">No has escrito reseñas aún</div>'; return; }
            container.innerHTML = data.map(r => `
                <div class="cnmx-resena-item">
                    <div class="cnmx-resena-info">
                        <a href="${r.negocio_link}" class="cnmx-resena-negocio">${r.negocio_nombre}</a>
                        <div class="cnmx-resena-meta">
                            <span class="cnmx-stars">${'★'.repeat(r.calificacion)}${'☆'.repeat(5-r.calificacion)}</span>
                            <span>${new Date(r.fecha).toLocaleDateString('es-MX')}</span>
                        </div>
                        <p class="cnmx-resena-text">${r.contenido}</p>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => { container.innerHTML = '<div class="cnmx-empty-state">Error al cargar reseñas</div>'; });
    }
    
    function loadLogros() {
        const container = document.getElementById('cnmx-logros-grid');
        fetch('/wp-json/cuentanos/v1/logros', {
            headers: { 'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>' }
        })
        .then(r => r.ok ? r.json() : [])
        .then(data => {
            if (!data.length) { container.innerHTML = '<div class="cnmx-empty-state">No hay logros disponibles</div>'; return; }
            container.innerHTML = data.map(l => `
                <div class="cnmx-logro-card ${l.desbloqueado ? '' : 'locked'}">
                    <div class="cnmx-logro-icon">${l.icono || '🏆'}</div>
                    <div class="cnmx-logro-name">${l.nombre}</div>
                    <div class="cnmx-logro-desc">${l.descripcion || ''}</div>
                </div>
            `).join('');
            document.getElementById('cnmx-stat-logros').textContent = data.filter(l => l.desbloqueado).length;
        })
        .catch(() => { container.innerHTML = '<div class="cnmx-empty-state">Error al cargar logros</div>'; });
    }
    
    function loadRecompensas() {
        const container = document.getElementById('cnmx-recompensas-grid');
        fetch('/wp-json/cuentanos/v1/recompensas', {
            headers: { 'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>' }
        })
        .then(r => r.ok ? r.json() : [])
        .then(data => {
            if (!data.length) { container.innerHTML = '<div class="cnmx-empty-state">No hay recompensas disponibles</div>'; return; }
            container.innerHTML = data.map(r => `
                <div class="cnmx-recompensa-card ${r.disponible ? '' : 'locked'}">
                    <div class="cnmx-recompensa-icon">${r.icono || '🎁'}</div>
                    <div class="cnmx-recompensa-name">${r.nombre}</div>
                    <div class="cnmx-recompensa-desc">${r.puntos} Megáfonos</div>
                    <button class="cnmx-canje-btn" onclick="canjearRecompensa(${r.id}, ${r.puntos})" ${r.disponible ? '' : 'disabled'}>
                        ${r.disponible ? 'Canjear' : 'No disponible'}
                    </button>
                </div>
            `).join('');
        })
        .catch(() => { container.innerHTML = '<div class="cnmx-empty-state">Error al cargar recompensas</div>'; });
    }
    
    window.canjeRecompensa = function(id, puntos) {
        if (!confirm('¿Canjeas esta recompensa por ' + puntos + ' Megáfonos?')) return;
        fetch('/wp-json/cuentanos/v1/canjear', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
            },
            body: JSON.stringify({ recompensa_id: id })
        })
        .then(r => r.json())
        .then(data => {
            if (data.codigo) { alert('¡Felicidades! Tu código: ' + data.codigo); loadRecompensas(); }
            else { alert(data.error || 'Error al canjear'); }
        })
        .catch(() => alert('Error al canjear'));
    };
    
    function loadNegocio() {
        const container = document.getElementById('cnmx-negocio-info');
        fetch('/wp-json/cuentanos/v1/usuario/negocio', {
            headers: { 'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>' }
        })
        .then(r => r.ok ? r.json() : { encontrado: false })
        .then(data => {
            if (!data.encontrado) {
                container.innerHTML = `
                    <div class="cnmx-agregar-negocio">
                        <p>¿Tienes un negocio? ¡Agrégalo a Cuentanos.mx!</p>
                        <a href="<?php echo home_url('/registro-negocio'); ?>" class="cnmx-btn cnmx-btn-primary">Agregar mi negocio</a>
                    </div>
                `;
                return;
            }
            const statusClass = data.estado === 'aprobado' ? 'aprobado' : 'pendiente';
            const statusText = data.estado === 'aprobado' ? 'Aprobado' : 'Pendiente de aprobación';
            container.innerHTML = `
                <div class="cnmx-negocio-card">
                    <div class="cnmx-negocio-header">
                        <img src="${data.imagen || 'https://via.placeholder.com/100'}" class="cnmx-negocio-img" alt="${data.nombre}">
                        <div>
                            <h3 class="cnmx-negocio-nombre">${data.nombre}</h3>
                            <span class="cnmx-negocio-status ${statusClass}">${statusText}</span>
                        </div>
                    </div>
                    <div class="cnmx-negocio-stats">
                        <div class="cnmx-negocio-stat">
                            <span class="cnmx-negocio-stat-val">${data.resenas || 0}</span>
                            <span class="cnmx-negocio-stat-label">Reseñas</span>
                        </div>
                        <div class="cnmx-negocio-stat">
                            <span class="cnmx-negocio-stat-val">${data.favoritos || 0}</span>
                            <span class="cnmx-negocio-stat-label">Favoritos</span>
                        </div>
                        <div class="cnmx-negocio-stat">
                            <span class="cnmx-negocio-stat-val">${data.plan || 'Gratis'}</span>
                            <span class="cnmx-negocio-stat-label">Plan</span>
                        </div>
                    </div>
                    <p style="margin-top: 16px; font-size: 14px; color: #717171;">
                        <a href="${data.link}" style="color: #FF385C;">Ver mi negocio →</a>
                    </p>
                </div>
            `;
        })
        .catch(() => { container.innerHTML = '<div class="cnmx-empty-state">Error al cargar negocio</div>'; });
    }
    
    document.getElementById('cnmx-ajustes-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const nombre = this.querySelector('[name="nombre"]').value;
        fetch('/wp-json/wp/v2/users/<?php echo get_current_user_id(); ?>', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
            },
            body: JSON.stringify({ name: nombre })
        })
        .then(r => r.json())
        .then(() => alert('Cambios guardados'))
        .catch(() => alert('Error al guardar'));
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
