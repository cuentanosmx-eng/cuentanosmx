<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Favoritos - Cuentanos.mx</title>
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
.navbar .container { display: flex; align-items: center; justify-content: space-between; height: 72px; }
.navbar-logo img { height: 36px; width: auto; }
.navbar-right { display: flex; align-items: center; gap: 12px; }
.btn-outline { padding: 10px 20px; border: 1px solid var(--border); border-radius: 100px; font-size: 14px; font-weight: 500; }
.btn-outline:hover { border-color: var(--text); }
.btn-primary { padding: 10px 20px; background: var(--primary); color: white; border-radius: 100px; font-size: 14px; font-weight: 600; }
.btn-primary:hover { background: var(--primary-dark); }

/* HEADER */
.page-header {
    padding: 120px 0 60px;
    background: linear-gradient(180deg, var(--cream) 0%, white 100%);
    text-align: center;
}
.page-header h1 { font-size: 40px; font-weight: 800; margin-bottom: 8px; }
.page-header p { font-size: 18px; color: var(--text-light); }

/* GRID */
.favorites-section { padding: 40px 0 80px; }
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
.business-remove {
    position: absolute !important;
    top: 12px !important;
    right: 12px !important;
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    min-height: 32px !important;
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
.business-remove:hover { background: white !important; color: #ef4444 !important; transform: scale(1.05) !important; }
.business-remove svg { width: 16px; height: 16px; }
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
.empty-state p { color: var(--text-light); margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto; }

/* LOADING */
.loading-state { text-align: center; padding: 80px 20px; }
.loading-spinner { width: 48px; height: 48px; border: 4px solid var(--border); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
@keyframes spin { to { transform: rotate(360deg); } }
.loading-state p { color: var(--text-light); }

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
@media (max-width: 1024px) { .businesses-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) {
    .page-header h1 { font-size: 28px; }
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
            <a href="<?php echo home_url('/directorio'); ?>" class="btn-outline">Explorar</a>
            <?php if (is_user_logged_in()): ?>
                <a href="<?php echo home_url('/perfil'); ?>" class="btn-outline">Mi Perfil</a>
            <?php else: ?>
                <a href="<?php echo home_url('/mi-cuenta'); ?>" class="btn-outline">Iniciar sesión</a>
                <a href="<?php echo home_url('/registro'); ?>" class="btn-primary">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main>
    <!-- HEADER -->
    <section class="page-header">
        <div class="container">
            <h1>❤️ Mis Favoritos</h1>
            <p>Los negocios que has guardado aparecerán aquí</p>
        </div>
    </section>
    
    <!-- FAVORITES GRID -->
    <section class="favorites-section">
        <div class="container">
            <?php if (!is_user_logged_in()): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <h3>Inicia sesión para ver tus favoritos</h3>
                    <p>Guarda tus negocios favoritos y accede a ellos rápidamente cuando quieras.</p>
                    <a href="<?php echo home_url('/mi-cuenta'); ?>" class="btn-primary">Iniciar sesión</a>
                </div>
            <?php else: ?>
                <div id="favorites-container">
                    <div class="loading-state">
                        <div class="loading-spinner"></div>
                        <p>Cargando tus favoritos...</p>
                    </div>
                </div>
            <?php endif; ?>
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
    const container = document.getElementById('favorites-container');
    if (!container) return;
    
    const nonce = window.cnmxUsersData?.nonce || '';
    
    fetch('/wp-json/cuentanos/v1/favoritos', {
        headers: {
            'X-WP-Nonce': nonce
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Error loading favorites');
        return response.json();
    })
    .then(data => {
        if (data.success && data.data && data.data.length > 0) {
            const html = `
                <div class="businesses-grid">
                    ${data.data.map(fav => {
                        const img = fav.imagen || 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=600&h=400&fit=crop';
                        const stars = '★'.repeat(Math.floor(fav.rating || 0)) + '☆'.repeat(5 - Math.floor(fav.rating || 0));
                        return `
                            <a href="${fav.url}" class="business-card">
                                <div class="business-img">
                                    <img src="${img}" alt="${fav.titulo}">
                                    <button class="business-remove" data-id="${fav.negocio_id}" title="Quitar de favoritos">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="business-content">
                                    <span class="business-category">${fav.categoria || 'General'}</span>
                                    <h3 class="business-title">${fav.titulo}</h3>
                                    <div class="business-rating">
                                        <span class="business-stars">${stars}</span>
                                        <span>${(fav.rating || 0).toFixed(1)}</span>
                                        <span class="business-reviews">(${fav.resenas || 0})</span>
                                    </div>
                                    ${fav.direccion ? `
                                        <div class="business-location">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            ${fav.direccion}
                                        </div>
                                    ` : ''}
                                </div>
                            </a>
                        `;
                    }).join('')}
                </div>
            `;
            container.innerHTML = html;
        } else {
            container.innerHTML = `
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    <h3>No tienes favoritos guardados</h3>
                    <p>Explora el directorio y guarda los negocios que más te gusten para verlos aquí.</p>
                    <a href="<?php echo home_url('/directorio'); ?>" class="btn-primary">Explorar directorio</a>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading favorites:', error);
        container.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <h3>Error al cargar favoritos</h3>
                <p>Intenta recargar la página.</p>
                <a href="<?php echo home_url('/directorio'); ?>" class="btn-outline">Explorar directorio</a>
            </div>
        `;
    });
    
    container.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.business-remove');
        if (removeBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            const card = removeBtn.closest('.business-card');
            const id = removeBtn.dataset.id;
            
            fetch('/wp-json/cuentanos/v1/favoritos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify({ negocio_id: id })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(() => card.remove(), 300);
                    
                    const remaining = document.querySelectorAll('.business-card').length;
                    if (remaining <= 1) {
                        container.innerHTML = `
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                <h3>No tienes favoritos guardados</h3>
                                <p>Explora el directorio y guarda los negocios que más te gusten.</p>
                                <a href="<?php echo home_url('/directorio'); ?>" class="btn-primary">Explorar directorio</a>
                            </div>
                        `;
                    }
                }
            });
        }
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
