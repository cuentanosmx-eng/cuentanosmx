<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="cnmx-profile-page">
    <header class="cnmx-profile-header">
        <div class="container">
            <a href="<?php echo home_url(); ?>" class="cnmx-back-link">← Volver al inicio</a>
        </div>
    </header>
    
    <main class="cnmx-profile-main">
        <div class="container">
            <h1>❤️ Mis Favoritos</h1>
            <p>Los negocios que has guardado aparecerán aquí</p>
            
            <div class="cnmx-favoritos-grid" id="cnmx-favoritos-grid">
                <p>Cargando...</p>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const grid = document.getElementById('cnmx-favoritos-grid');
    
    try {
        const response = await fetch('/wp-json/cnmx/v1/usuario/favoritos', {
            headers: {
                'X-WP-Nonce': window.cnmxUsersData?.nonce || ''
            }
        });
        const data = await response.json();
        
        if (data.favoritos && data.favoritos.length > 0) {
            grid.innerHTML = data.favoritos.map(fav => `
                <a href="${fav.post_url}" class="cnmx-favorito-card">
                    <div class="cnmx-favorito-info">
                        <h3>${fav.post_title}</h3>
                        <p>Guardado el ${new Date(fav.fecha).toLocaleDateString()}</p>
                    </div>
                </a>
            `).join('');
        } else {
            grid.innerHTML = '<p class="cnmx-empty-state">Aún no tienes favoritos guardados</p>';
        }
    } catch (error) {
        grid.innerHTML = '<p class="cnmx-empty-state">Debes iniciar sesión para ver tus favoritos</p>';
    }
});
</script>

<?php wp_footer(); ?>
</body>
</html>
