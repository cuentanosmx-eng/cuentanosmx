<div class="wrap cnmx-admin-wrap">
    <h1>Dashboard - Cuentanos.mx</h1>
    
    <div class="cnmx-stats-grid">
        <div class="cnmx-stat-card">
            <div class="cnmx-stat-icon">🏪</div>
            <div class="cnmx-stat-content">
                <span class="cnmx-stat-value" id="stat-negocios">0</span>
                <span class="cnmx-stat-label">Negocios</span>
            </div>
        </div>
        <div class="cnmx-stat-card pending">
            <div class="cnmx-stat-icon">⏳</div>
            <div class="cnmx-stat-content">
                <span class="cnmx-stat-value" id="stat-pendientes">0</span>
                <span class="cnmx-stat-label">Pendientes</span>
            </div>
        </div>
        <div class="cnmx-stat-card">
            <div class="cnmx-stat-icon">👥</div>
            <div class="cnmx-stat-content">
                <span class="cnmx-stat-value" id="stat-usuarios">0</span>
                <span class="cnmx-stat-label">Usuarios</span>
            </div>
        </div>
        <div class="cnmx-stat-card">
            <div class="cnmx-stat-icon">⭐</div>
            <div class="cnmx-stat-content">
                <span class="cnmx-stat-value" id="stat-resenas">0</span>
                <span class="cnmx-stat-label">Reseñas</span>
            </div>
        </div>
        <div class="cnmx-stat-card">
            <div class="cnmx-stat-icon">🎤</div>
            <div class="cnmx-stat-content">
                <span class="cnmx-stat-value" id="stat-megafonos">0</span>
                <span class="cnmx-stat-label">Megáfonos Totales</span>
            </div>
        </div>
    </div>
    
    <div class="cnmx-dashboard-grid">
        <div class="cnmx-dashboard-card">
            <h2>Negocios Recientes</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="recent-businesses">
                    <tr><td colspan="3">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="cnmx-dashboard-card">
            <h2>Acciones Rápidas</h2>
            <div class="cnmx-quick-actions">
                <a href="<?php echo admin_url('admin.php?page=cuentanos-pendientes'); ?>" class="cnmx-quick-action">
                    <span class="cnmx-qa-icon">⏳</span>
                    <span>Revisar Pendientes</span>
                </a>
                <a href="<?php echo admin_url('admin.php?page=cuentanos-negocios'); ?>" class="cnmx-quick-action">
                    <span class="cnmx-qa-icon">📝</span>
                    <span>Gestionar Negocios</span>
                </a>
                <a href="<?php echo admin_url('admin.php?page=cuentanos-usuarios'); ?>" class="cnmx-quick-action">
                    <span class="cnmx-qa-icon">👥</span>
                    <span>Ver Usuarios</span>
                </a>
                <a href="<?php echo admin_url('post-new.php?post_type=cnmx_logro'); ?>" class="cnmx-quick-action">
                    <span class="cnmx-qa-icon">🏆</span>
                    <span>Crear Logro</span>
                </a>
            </div>
        </div>
    </div>
</div>
