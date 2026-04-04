<?php
/**
 * Template para páginas de negocio (login, registro, dashboard, etc.)
 */

require_once CNMX_BIZ_PATH . 'includes/class-cnmx-biz-setup.php';
$slug = cnmx_biz_get_the_slug();

if ($slug === 'login-empresa') {
    get_template_part('templates/partials/header');
    ?>
    <div class="cnmx-business-page">
        <div class="cnmx-login-container">
            <div class="cnmx-login-card">
                <h1 class="cnmx-login-title">🐇 Login para Empresas</h1>
                <p class="cnmx-login-subtitle">Ingresa a tu cuenta de negocio</p>
                
                <form id="cnmx-login-form" class="cnmx-auth-form">
                    <div class="cnmx-form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="tu@email.com">
                    </div>
                    <div class="cnmx-form-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" required placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-primary cnmx-login-btn">Iniciar Sesión</button>
                </form>
                
                <p class="cnmx-login-footer">
                    ¿No tienes cuenta? <a href="<?php echo home_url('/registrar-negocio'); ?>">Registrar mi negocio</a>
                </p>
            </div>
        </div>
    </div>
    <?php
    get_template_part('templates/partials/footer');
    
} elseif ($slug === 'registrar-negocio') {
    get_template_part('templates/partials/header');
    ?>
    <div class="cnmx-business-page">
        <div class="cnmx-register-container">
            <div class="cnmx-register-card">
                <h1 class="cnmx-register-title">🏪 Registrar mi Negocio</h1>
                <p class="cnmx-register-subtitle">Crea tu cuenta y añade tu negocio a nuestro directorio</p>
                
                <form id="cnmx-register-form" class="cnmx-auth-form">
                    <h3 class="cnmx-form-section-title">Datos de tu Cuenta</h3>
                    <div class="cnmx-form-grid">
                        <div class="cnmx-form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required placeholder="tu@email.com">
                        </div>
                        <div class="cnmx-form-group">
                            <label>Contraseña *</label>
                            <input type="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
                        </div>
                    </div>
                    
                    <h3 class="cnmx-form-section-title">Datos del Negocio</h3>
                    <div class="cnmx-form-grid">
                        <div class="cnmx-form-group">
                            <label>Nombre del negocio *</label>
                            <input type="text" name="nombre_negocio" required placeholder="Ej: Restaurant Mi Casa">
                        </div>
                        <div class="cnmx-form-group">
                            <label>Categoría *</label>
                            <select name="categoria" required>
                                <option value="">Selecciona...</option>
                                <?php $cats = get_terms(['taxonomy' => 'categoria', 'hide_empty' => false]);
                                foreach ($cats as $cat): ?>
                                    <option value="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="cnmx-form-group full-width">
                            <label>Dirección *</label>
                            <input type="text" name="direccion" required placeholder="Calle, número, colonia, ciudad">
                        </div>
                        <div class="cnmx-form-group">
                            <label>Ciudad *</label>
                            <input type="text" name="ciudad" required placeholder="Ciudad de México">
                        </div>
                        <div class="cnmx-form-group">
                            <label>Teléfono</label>
                            <input type="tel" name="telefono" placeholder="55 1234 5678">
                        </div>
                        <div class="cnmx-form-group">
                            <label>WhatsApp</label>
                            <input type="tel" name="whatsapp" placeholder="52 55 1234 5678">
                        </div>
                        <div class="cnmx-form-group">
                            <label>Sitio web</label>
                            <input type="url" name="sitio_web" placeholder="https://tunegocio.com">
                        </div>
                    </div>
                    
                    <div class="cnmx-form-group full-width">
                        <label>Descripción del negocio</label>
                        <textarea name="descripcion" rows="4" placeholder="Cuéntanos sobre tu negocio..."></textarea>
                    </div>
                    
                    <div class="cnmx-terminos">
                        <input type="checkbox" id="terminos" required>
                        <label for="terminos">Acepto los <a href="#">Términos de uso</a> y <a href="#">Política de privacidad</a></label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary cnmx-register-btn">Crear mi cuenta</button>
                </form>
                
                <p class="cnmx-register-footer">
                    ¿Ya tienes cuenta? <a href="<?php echo home_url('/login-empresa'); ?>">Iniciar sesión</a>
                </p>
            </div>
        </div>
    </div>
    
    <style>
    .cnmx-business-page {
        min-height: calc(100vh - 200px);
        padding: 40px 0;
    }
    .cnmx-login-container, .cnmx-register-container {
        max-width: 480px;
        margin: 0 auto;
        padding: 0 16px;
    }
    .cnmx-register-container {
        max-width: 700px;
    }
    .cnmx-login-card, .cnmx-register-card {
        background: var(--cnmx-surface);
        border-radius: var(--cnmx-radius-xl);
        padding: 40px;
        box-shadow: var(--cnmx-shadow-lg);
    }
    .cnmx-login-title, .cnmx-register-title {
        font-size: 28px;
        font-weight: 800;
        text-align: center;
        margin-bottom: 8px;
        color: var(--cnmx-text);
    }
    .cnmx-login-subtitle, .cnmx-register-subtitle {
        text-align: center;
        color: var(--cnmx-text-light);
        margin-bottom: 32px;
    }
    .cnmx-form-section-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--cnmx-text);
        margin: 24px 0 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--cnmx-border);
    }
    .cnmx-form-section-title:first-of-type {
        margin-top: 0;
    }
    .cnmx-login-btn, .cnmx-register-btn {
        width: 100%;
        padding: 16px;
        font-size: 16px;
        margin-top: 16px;
    }
    .cnmx-login-footer, .cnmx-register-footer {
        text-align: center;
        margin-top: 24px;
        color: var(--cnmx-text-light);
    }
    .cnmx-login-footer a, .cnmx-register-footer a {
        color: var(--cnmx-primary);
        font-weight: 600;
    }
    .cnmx-terminos {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin: 20px 0;
        font-size: 14px;
        color: var(--cnmx-text-light);
    }
    .cnmx-terminos input {
        margin-top: 4px;
    }
    .cnmx-terminos a {
        color: var(--cnmx-primary);
    }
    .cnmx-form-group.full-width {
        grid-column: 1 / -1;
    }
    </style>
    <?php
    get_template_part('templates/partials/footer');
    
} elseif ($slug === 'dashboard-empresa') {
    get_template_part('templates/partials/header');
    ?>
    <div class="cnmx-dashboard-page">
        <div class="container">
            <div class="cnmx-dashboard-header">
                <h1>Dashboard de Mi Negocio</h1>
                <a href="<?php echo home_url('/mi-negocio'); ?>" class="btn btn-primary">Editar Negocio</a>
            </div>
            
            <div id="cnmx-dashboard-app"></div>
        </div>
    </div>
    <?php
    get_template_part('templates/partials/footer');
    
} elseif ($slug === 'mi-negocio') {
    $user_id = get_current_user_id();
    $negocio_id = get_user_meta($user_id, 'cnmx_negocio_asociado', true);
    
    if (!$negocio_id) {
        $negocios = get_posts([
            'post_type' => 'negocio',
            'post_status' => ['publish', 'pending', 'draft'],
            'posts_per_page' => 1,
            'meta_query' => [
                ['key' => 'cnmx_propietario_id', 'value' => $user_id]
            ]
        ]);
        if (!empty($negocios)) {
            $negocio_id = $negocios[0]->ID;
        }
    }
    
    $negocio = $negocio_id ? get_post($negocio_id) : null;
    $es_nuevo = !$negocio;
    
    get_template_part('templates/partials/header');
    ?>
    <div class="cnmx-edit-negocio-page">
        <div class="container">
            <div class="cnmx-edit-header">
                <h1><?php echo $es_nuevo ? 'Completar Datos del Negocio' : 'Editar Mi Negocio'; ?></h1>
                <a href="<?php echo home_url('/dashboard-empresa'); ?>" class="btn btn-secondary">← Volver al Dashboard</a>
            </div>
            
            <div class="cnmx-edit-tabs">
                <button class="cnmx-tab-btn active" data-tab="basicos">📋 Datos Básicos</button>
                <button class="cnmx-tab-btn" data-tab="contacto">📞 Contacto</button>
                <button class="cnmx-tab-btn" data-tab="horarios">🕐 Horarios</button>
                <button class="cnmx-tab-btn" data-tab="fotos">📷 Fotos</button>
                <button class="cnmx-tab-btn" data-tab="plan">⭐ Plan</button>
                <button class="cnmx-tab-btn" data-tab="animaciones">✨ Animaciones</button>
            </div>
            
            <form id="cnmx-edit-form" class="cnmx-edit-form">
                <input type="hidden" name="negocio_id" value="<?php echo $negocio_id ?: ''; ?>">
                
                <!-- Tab: Datos Básicos -->
                <div class="cnmx-tab-content active" id="tab-basicos">
                    <div class="cnmx-form-card">
                        <h3 class="cnmx-form-card-title">Información General</h3>
                        <div class="cnmx-form-grid">
                            <div class="cnmx-form-group">
                                <label>Nombre del negocio *</label>
                                <input type="text" name="nombre" value="<?php echo $negocio ? esc_attr($negocio->post_title) : ''; ?>" required placeholder="Ej: Restaurant El Buen Sabor">
                            </div>
                            <div class="cnmx-form-group">
                                <label>Categoría *</label>
                                <select name="categoria" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php 
                                    $cats = get_terms(['taxonomy' => 'categoria', 'hide_empty' => false]);
                                    $current_cat = $negocio ? wp_get_post_terms($negocio_id, 'categoria') : [];
                                    $current_cat_slug = !empty($current_cat) ? $current_cat[0]->slug : '';
                                    foreach ($cats as $cat): ?>
                                        <option value="<?php echo $cat->slug; ?>" <?php selected($current_cat_slug, $cat->slug); ?>><?php echo $cat->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="cnmx-form-group full-width">
                                <label>Descripción</label>
                                <textarea name="descripcion" rows="4" placeholder="Cuéntanos sobre tu negocio, qué ofreces, qué te hace especial..."><?php echo $negocio ? esc_textarea($negocio->post_content) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab: Contacto -->
                <div class="cnmx-tab-content" id="tab-contacto">
                    <div class="cnmx-form-card">
                        <h3 class="cnmx-form-card-title">Información de Contacto</h3>
                        <div class="cnmx-form-grid">
                            <div class="cnmx-form-group full-width">
                                <label>Dirección</label>
                                <input type="text" name="direccion" value="<?php echo esc_attr(get_post_meta($negocio_id, 'cnmx_direccion', true)); ?>" placeholder="Calle, número, colonia">
                            </div>
                            <div class="cnmx-form-group">
                                <label>Ciudad</label>
                                <input type="text" name="ciudad" value="<?php echo esc_attr(get_post_meta($negocio_id, 'cnmx_ciudad', true)); ?>" placeholder="Ciudad de México">
                            </div>
                            <div class="cnmx-form-group">
                                <label>Teléfono</label>
                                <input type="tel" name="telefono" value="<?php echo esc_attr(get_post_meta($negocio_id, 'cnmx_telefono', true)); ?>" placeholder="55 1234 5678">
                            </div>
                            <div class="cnmx-form-group">
                                <label>WhatsApp</label>
                                <input type="tel" name="whatsapp" value="<?php echo esc_attr(get_post_meta($negocio_id, 'cnmx_whatsapp', true)); ?>" placeholder="52 55 1234 5678">
                            </div>
                            <div class="cnmx-form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="<?php echo esc_attr(get_post_meta($negocio_id, 'cnmx_email', true)); ?>" placeholder="contacto@tunegocio.com">
                            </div>
                            <div class="cnmx-form-group">
                                <label>Sitio Web</label>
                                <input type="url" name="sitio_web" value="<?php echo esc_attr(get_post_meta($negocio_id, 'cnmx_sitio_web', true)); ?>" placeholder="https://tunegocio.com">
                            </div>
                            <div class="cnmx-form-group">
                                <label>Facebook</label>
                                <input type="text" name="facebook" value="<?php echo esc_attr(get_post_meta($negocio_id, 'cnmx_facebook', true)); ?>" placeholder="facebook.com/tunegocio">
                            </div>
                            <div class="cnmx-form-group">
                                <label>Instagram</label>
                                <input type="text" name="instagram" value="<?php echo esc_attr(get_post_meta($negocio_id, 'cnmx_instagram', true)); ?>" placeholder="@tunegocio">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab: Horarios -->
                <div class="cnmx-tab-content" id="tab-horarios">
                    <div class="cnmx-form-card">
                        <h3 class="cnmx-form-card-title">Horarios de Atención</h3>
                        <div class="cnmx-horarios-grid">
                            <?php
                            $dias_semana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                            $dias_labels = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                            foreach ($dias_semana as $i => $dia): 
                                $cerrado = get_post_meta($negocio_id, 'cnmx_' . $dia . '_cerrado', true);
                                $apertura = get_post_meta($negocio_id, 'cnmx_' . $dia . '_apertura', true);
                                $cierre = get_post_meta($negocio_id, 'cnmx_' . $dia . '_cierre', true);
                            ?>
                                <div class="cnmx-horario-row">
                                    <label class="cnmx-dia-label"><?php echo $dias_labels[$i]; ?></label>
                                    <input type="checkbox" name="<?php echo $dia; ?>_cerrado" class="cnmx-cerrado-check" <?php checked($cerrado, 'on'); ?>>
                                    <span class="cnmx-cerrado-label">Cerrado</span>
                                    <input type="time" name="<?php echo $dia; ?>_apertura" class="cnmx-hora-input" value="<?php echo esc_attr($apertura ?: '09:00'); ?>">
                                    <span class="cnmx-hora-sep">a</span>
                                    <input type="time" name="<?php echo $dia; ?>_cierre" class="cnmx-hora-input" value="<?php echo esc_attr($cierre ?: '18:00'); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Tab: Fotos -->
                <div class="cnmx-tab-content" id="tab-fotos">
                    <div class="cnmx-form-card">
                        <h3 class="cnmx-form-card-title">Galería de Fotos</h3>
                        <div class="cnmx-foto-upload-area" id="cnmx-foto-upload">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                            <p>Arrastra fotos aquí o haz clic para seleccionar</p>
                            <span class="cnmx-upload-hint">Máximo 10 fotos, 5MB cada una</span>
                            <input type="file" id="cnmx-fotos-input" accept="image/*" multiple hidden>
                        </div>
                        <div class="cnmx-fotos-preview" id="cnmx-fotos-preview"></div>
                    </div>
                </div>
                
                <!-- Tab: Plan/Membresía -->
                <div class="cnmx-tab-content" id="tab-plan">
                    <div class="cnmx-form-card">
                        <h3 class="cnmx-form-card-title">⭐ Plan y Membresía</h3>
                        <p class="cnmx-form-hint">Activa opciones premium para destacar tu negocio</p>
                        
                        <div class="cnmx-plan-options">
                            <label class="cnmx-plan-option <?php echo get_post_meta($negocio_id, 'cnmx_destacado', true) === 'si' ? 'active' : ''; ?>">
                                <input type="checkbox" name="destacado" value="si" <?php checked(get_post_meta($negocio_id, 'cnmx_destacado', true), 'si'); ?>>
                                <div class="cnmx-plan-content">
                                    <div class="cnmx-plan-icon">⭐</div>
                                    <div class="cnmx-plan-info">
                                        <h4>Negocios Destacados</h4>
                                        <p>Aparece en la sección de negocios destacados de la página principal con mayor visibilidad.</p>
                                    </div>
                                    <div class="cnmx-plan-check">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="cnmx-plan-option <?php echo get_post_meta($negocio_id, 'cnmx_prioridad', true) === 'si' ? 'active' : ''; ?>">
                                <input type="checkbox" name="prioridad" value="si" <?php checked(get_post_meta($negocio_id, 'cnmx_prioridad', true), 'si'); ?>>
                                <div class="cnmx-plan-content">
                                    <div class="cnmx-plan-icon">🚀</div>
                                    <div class="cnmx-plan-info">
                                        <h4>Mayor Prioridad</h4>
                                        <p>Tu negocio aparece primero en los resultados de búsqueda y filtros.</p>
                                    </div>
                                    <div class="cnmx-plan-check">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="cnmx-plan-option <?php echo get_post_meta($negocio_id, 'cnmx_anuncio_activo', true) === 'si' ? 'active' : ''; ?>">
                                <input type="checkbox" name="anuncio_activo" value="si" <?php checked(get_post_meta($negocio_id, 'cnmx_anuncio_activo', true), 'si'); ?>>
                                <div class="cnmx-plan-content">
                                    <div class="cnmx-plan-icon">🎯</div>
                                    <div class="cnmx-plan-info">
                                        <h4>Anuncio en Slider</h4>
                                        <p>Tu banner aparece en el slider de negocios destacados de la página principal.</p>
                                    </div>
                                    <div class="cnmx-plan-check">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Tab: Animaciones -->
                <div class="cnmx-tab-content" id="tab-animaciones">
                    <div class="cnmx-form-card">
                        <h3 class="cnmx-form-card-title">Animaciones del Negocio</h3>
                        <p class="cnmx-form-hint">Personaliza cómo se ve tu negocio en el directorio</p>
                        
                        <h4>Efecto de entrada en cards</h4>
                        <div class="cnmx-animaciones-grid">
                            <?php $anim_entrada = get_post_meta($negocio_id, 'cnmx_animacion_entrada', true) ?: 'fade-up'; ?>
                            <label class="cnmx-animacion-option">
                                <input type="radio" name="animacion_entrada" value="fade-up" <?php checked($anim_entrada, 'fade-up'); ?>>
                                <div class="cnmx-animacion-preview">
                                    <div class="cnmx-anim-demo fade-demo"></div>
                                    <span>Subir</span>
                                </div>
                            </label>
                            <label class="cnmx-animacion-option">
                                <input type="radio" name="animacion_entrada" value="zoom-in" <?php checked($anim_entrada, 'zoom-in'); ?>>
                                <div class="cnmx-animacion-preview">
                                    <div class="cnmx-anim-demo zoom-demo"></div>
                                    <span>Zoom</span>
                                </div>
                            </label>
                            <label class="cnmx-animacion-option">
                                <input type="radio" name="animacion_entrada" value="slide-left" <?php checked($anim_entrada, 'slide-left'); ?>>
                                <div class="cnmx-animacion-preview">
                                    <div class="cnmx-anim-demo slide-demo"></div>
                                    <span>Deslizar</span>
                                </div>
                            </label>
                            <label class="cnmx-animacion-option">
                                <input type="radio" name="animacion_entrada" value="bounce" <?php checked($anim_entrada, 'bounce'); ?>>
                                <div class="cnmx-animacion-preview">
                                    <div class="cnmx-anim-demo bounce-demo"></div>
                                    <span>Rebote</span>
                                </div>
                            </label>
                        </div>
                        
                        <h4>Colores del negocio</h4>
                        <div class="cnmx-form-grid">
                            <div class="cnmx-form-group">
                                <label>Color primario</label>
                                <input type="color" name="color_primario" value="<?php echo esc_attr(get_post_meta($negocio_id, 'cnmx_color_primario', true) ?: '#FF4D4D'); ?>">
                            </div>
                            <div class="cnmx-form-group">
                                <label>Color secundario</label>
                                <input type="color" name="color_secundario" value="<?php echo esc_attr(get_post_meta($negocio_id, 'cnmx_color_secundario', true) ?: '#2D3436'); ?>">
                            </div>
                        </div>
                        
                        <div class="cnmx-animacion-speed">
                            <label>Velocidad de animación:</label>
                            <input type="range" name="animacion_speed" min="0.3" max="1.5" step="0.1" value="0.5">
                            <span class="cnmx-speed-label">0.5s</span>
                        </div>
                    </div>
                </div>
                
                <div class="cnmx-form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <span class="btn-text"><?php echo $es_nuevo ? 'Crear Negocio' : 'Guardar Cambios'; ?></span>
                        <span class="btn-loading" style="display: none;">Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <style>
    .cnmx-edit-negocio-page {
        padding: 40px 0;
    }
    
    .cnmx-edit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }
    
    .cnmx-edit-header h1 {
        font-size: 28px;
        font-weight: 800;
    }
    
    .cnmx-edit-tabs {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 16px;
        margin-bottom: 24px;
        border-bottom: 2px solid var(--cnmx-border);
    }
    
    .cnmx-tab-btn {
        padding: 12px 20px;
        background: var(--cnmx-surface);
        border: 2px solid var(--cnmx-border);
        border-radius: var(--cnmx-radius-md);
        cursor: pointer;
        font-weight: 500;
        white-space: nowrap;
        transition: all var(--cnmx-transition-fast);
    }
    
    .cnmx-tab-btn:hover {
        border-color: var(--cnmx-primary);
    }
    
    .cnmx-tab-btn.active {
        background: var(--cnmx-primary);
        border-color: var(--cnmx-primary);
        color: white;
    }
    
    .cnmx-tab-content {
        display: none;
    }
    
    .cnmx-tab-content.active {
        display: block;
    }
    
    .cnmx-form-card {
        background: var(--cnmx-surface);
        border-radius: var(--cnmx-radius-lg);
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: var(--cnmx-shadow-sm);
    }
    
    .cnmx-form-card-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--cnmx-border);
    }
    
    .cnmx-form-hint {
        color: var(--cnmx-text-muted);
        font-size: 14px;
        margin-bottom: 20px;
    }
    
    .cnmx-horarios-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .cnmx-horario-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px;
        background: var(--cnmx-background);
        border-radius: var(--cnmx-radius-md);
    }
    
    .cnmx-dia-label {
        width: 100px;
        font-weight: 600;
    }
    
    .cnmx-cerrado-check {
        width: 20px;
        height: 20px;
    }
    
    .cnmx-cerrado-label {
        width: 60px;
        font-size: 14px;
        color: var(--cnmx-text-muted);
    }
    
    .cnmx-hora-input {
        padding: 8px 12px;
        border: 2px solid var(--cnmx-border);
        border-radius: var(--cnmx-radius-sm);
        font-size: 14px;
    }
    
    .cnmx-hora-sep {
        color: var(--cnmx-text-muted);
    }
    
    .cnmx-foto-upload-area {
        border: 2px dashed var(--cnmx-border);
        border-radius: var(--cnmx-radius-lg);
        padding: 48px;
        text-align: center;
        cursor: pointer;
        transition: all var(--cnmx-transition);
        background: var(--cnmx-background);
    }
    
    .cnmx-foto-upload-area:hover {
        border-color: var(--cnmx-primary);
        background: rgba(255, 77, 77, 0.05);
    }
    
    .cnmx-foto-upload-area svg {
        width: 48px;
        height: 48px;
        color: var(--cnmx-text-muted);
        margin-bottom: 16px;
    }
    
    .cnmx-foto-upload-area p {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    .cnmx-upload-hint {
        font-size: 13px;
        color: var(--cnmx-text-muted);
    }
    
    .cnmx-fotos-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 16px;
        margin-top: 24px;
    }
    
    .cnmx-foto-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: var(--cnmx-radius-md);
        overflow: hidden;
    }
    
    .cnmx-foto-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .cnmx-foto-remove {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 28px;
        height: 28px;
        background: rgba(0,0,0,0.7);
        border: none;
        border-radius: 50%;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .cnmx-animaciones-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .cnmx-animacion-option {
        cursor: pointer;
    }
    
    .cnmx-animacion-option input {
        display: none;
    }
    
    .cnmx-animacion-preview {
        padding: 16px;
        background: var(--cnmx-background);
        border: 2px solid var(--cnmx-border);
        border-radius: var(--cnmx-radius-md);
        text-align: center;
        transition: all var(--cnmx-transition);
    }
    
    .cnmx-animacion-option input:checked + .cnmx-animacion-preview {
        border-color: var(--cnmx-primary);
        background: rgba(255, 77, 77, 0.1);
    }
    
    .cnmx-anim-demo {
        width: 100%;
        height: 60px;
        background: linear-gradient(135deg, var(--cnmx-primary) 0%, #FF6B6B 100%);
        border-radius: 8px;
        margin-bottom: 12px;
    }
    
    .cnmx-animacion-option span {
        font-size: 14px;
        font-weight: 500;
    }
    
    .cnmx-animacion-speed {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        background: var(--cnmx-background);
        border-radius: var(--cnmx-radius-md);
    }
    
    .cnmx-animacion-speed input[type="range"] {
        flex: 1;
        height: 8px;
        border-radius: 4px;
        appearance: none;
        background: var(--cnmx-border);
    }
    
    .cnmx-animacion-speed input[type="range"]::-webkit-slider-thumb {
        appearance: none;
        width: 20px;
        height: 20px;
        background: var(--cnmx-primary);
        border-radius: 50%;
        cursor: pointer;
    }
    
    .cnmx-speed-label {
        font-weight: 600;
        min-width: 40px;
    }
    
    .cnmx-plan-options {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .cnmx-plan-option {
        cursor: pointer;
    }
    
    .cnmx-plan-option input {
        display: none;
    }
    
    .cnmx-plan-content {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background: var(--cnmx-background);
        border: 2px solid var(--cnmx-border);
        border-radius: var(--cnmx-radius-lg);
        transition: all var(--cnmx-transition);
    }
    
    .cnmx-plan-option:hover .cnmx-plan-content {
        border-color: var(--cnmx-primary);
    }
    
    .cnmx-plan-option input:checked + .cnmx-plan-content {
        border-color: var(--cnmx-primary);
        background: rgba(255, 77, 77, 0.05);
    }
    
    .cnmx-plan-icon {
        font-size: 32px;
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--cnmx-surface);
        border-radius: var(--cnmx-radius-md);
    }
    
    .cnmx-plan-info {
        flex: 1;
    }
    
    .cnmx-plan-info h4 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
    }
    
    .cnmx-plan-info p {
        font-size: 14px;
        color: var(--cnmx-text-muted);
        margin: 0;
    }
    
    .cnmx-plan-check {
        width: 32px;
        height: 32px;
        border: 2px solid var(--cnmx-border);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--cnmx-transition);
    }
    
    .cnmx-plan-check svg {
        width: 16px;
        height: 16px;
        color: white;
        opacity: 0;
        transition: opacity var(--cnmx-transition);
    }
    
    .cnmx-plan-option input:checked + .cnmx-plan-content .cnmx-plan-check {
        background: var(--cnmx-primary);
        border-color: var(--cnmx-primary);
    }
    
    .cnmx-plan-option input:checked + .cnmx-plan-content .cnmx-plan-check svg {
        opacity: 1;
    }
    
    @media (max-width: 768px) {
        .cnmx-plan-content {
            flex-wrap: wrap;
        }
        
        .cnmx-plan-icon {
            width: 48px;
            height: 48px;
            font-size: 24px;
        }
        
        .cnmx-plan-check {
            position: absolute;
            top: 12px;
            right: 12px;
        }
        
        .cnmx-plan-option {
            position: relative;
        }
    }
    
    .cnmx-form-actions {
        margin-top: 32px;
        text-align: center;
    }
    
    .btn-lg {
        padding: 16px 48px;
        font-size: 16px;
    }
    
    @media (max-width: 768px) {
        .cnmx-edit-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
        
        .cnmx-form-card {
            padding: 20px;
        }
        
        .cnmx-horario-row {
            flex-wrap: wrap;
        }
        
        .cnmx-dia-label {
            width: 100%;
            margin-bottom: 8px;
        }
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.cnmx-tab-btn');
        const contents = document.querySelectorAll('.cnmx-tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                contents.forEach(c => {
                    c.classList.remove('active');
                    if (c.id === 'tab-' + target) {
                        c.classList.add('active');
                    }
                });
            });
        });
        
        const form = document.getElementById('cnmx-edit-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = form.querySelector('button[type="submit"]');
            const btnText = btn.querySelector('.btn-text');
            const btnLoading = btn.querySelector('.btn-loading');
            
            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
            
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const result = await fetch('/wp-json/cnmx-biz/v1/negocio', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': cnmxBizData.nonce
                    },
                    body: JSON.stringify(data)
                }).then(r => r.json());
                
                if (result.success) {
                    window.CNMX.showToast('success', '¡Guardado!', 'Los cambios se han guardado correctamente');
                } else {
                    throw new Error(result.message || 'Error al guardar');
                }
            } catch (error) {
                window.CNMX.showToast('error', 'Error', error.message);
            } finally {
                btn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            }
        });
        
        const fotoUpload = document.getElementById('cnmx-foto-upload');
        const fotoInput = document.getElementById('cnmx-fotos-input');
        const preview = document.getElementById('cnmx-fotos-preview');
        
        fotoUpload.addEventListener('click', () => fotoInput.click());
        
        fotoUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            fotoUpload.style.borderColor = 'var(--cnmx-primary)';
        });
        
        fotoUpload.addEventListener('dragleave', () => {
            fotoUpload.style.borderColor = '';
        });
        
        fotoUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            fotoUpload.style.borderColor = '';
            handleFiles(e.dataTransfer.files);
        });
        
        fotoInput.addEventListener('change', () => {
            handleFiles(fotoInput.files);
        });
        
        function handleFiles(files) {
            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                
                const reader = new FileReader();
                reader.onload = async (e) => {
                    const div = document.createElement('div');
                    div.className = 'cnmx-foto-item uploading';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Foto">
                        <div class="cnmx-foto-progress">Subiendo...</div>
                        <button type="button" class="cnmx-foto-remove">✕</button>
                    `;
                    preview.appendChild(div);
                    
                    try {
                        const formData = new FormData();
                        formData.append('negocio_id', <?php echo $negocio_id ?: 0; ?>);
                        formData.append('foto', file);
                        
                        const response = await fetch('/wp-json/cnmx/v1/fotos/upload', {
                            method: 'POST',
                            headers: {
                                'X-WP-Nonce': cnmxBizData.nonce
                            },
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            div.classList.remove('uploading');
                            div.querySelector('.cnmx-foto-progress').remove();
                            div.dataset.fotoId = result.foto.id;
                        } else {
                            div.classList.add('error');
                            div.querySelector('.cnmx-foto-progress').textContent = 'Error';
                        }
                    } catch (error) {
                        div.classList.add('error');
                        div.querySelector('.cnmx-foto-progress').textContent = 'Error';
                    }
                    
                    div.querySelector('.cnmx-foto-remove').addEventListener('click', () => {
                        if (div.dataset.fotoId) {
                            deleteFoto(div.dataset.fotoId);
                        }
                        div.remove();
                    });
                };
                reader.readAsDataURL(file);
            });
        }
        
        async function deleteFoto(fotoId) {
            try {
                await fetch(`/wp-json/cnmx/v1/fotos/${fotoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-WP-Nonce': cnmxBizData.nonce
                    }
                });
            } catch (error) {
                console.error('Error deleting foto:', error);
            }
        }
    });
    </script>
    <?php
    get_template_part('templates/partials/footer');
    
} elseif ($slug === 'mis-logros') {
    get_template_part('templates/partials/header');
    ?>
    <div class="cnmx-logros-page">
        <div class="container">
            <div class="cnmx-page-header" style="margin-bottom: 32px;">
                <h1>🏆 Mis Logros</h1>
                <p style="color: var(--cnmx-text-light);">Completa acciones para ganar Megáfonos</p>
            </div>
            
            <div id="cnmx-logros-app"></div>
        </div>
    </div>
    <?php
    get_template_part('templates/partials/footer');
    
} elseif ($slug === 'mis-recompensas') {
    get_template_part('templates/partials/header');
    ?>
    <div class="cnmx-recompensas-page">
        <div class="container">
            <div class="cnmx-page-header" style="margin-bottom: 32px;">
                <h1>🎁 Recompensas</h1>
                <p style="color: var(--cnmx-text-light);">Canjea tus Megáfonos por recompensas exclusivas</p>
            </div>
            
            <div id="cnmx-recompensas-app"></div>
        </div>
    </div>
    <?php
    get_template_part('templates/partials/footer');
    
} elseif ($slug === 'admin-directorio') {
    get_template_part('templates/partials/header');
    ?>
    <div class="cnmx-admin-directorio">
        <div class="container">
            <div class="cnmx-admin-header">
                <h1>🔧 Panel de Administración</h1>
                <p style="color: var(--cnmx-text-light);">Gestiona todos los negocios del directorio</p>
            </div>
            
            <div class="cnmx-admin-content">
                <h2>⭐ Planes de Negocios</h2>
                <p style="margin-bottom: 20px; color: var(--cnmx-text-muted);">Marca los negocios que quieres destacar, dar prioridad o mostrar en el slider de anuncios.</p>
                
                <div class="cnmx-admin-filters">
                    <button class="cnmx-filter-btn active" data-filter="all">Todos</button>
                    <button class="cnmx-filter-btn" data-filter="destacado">⭐ Destacados</button>
                    <button class="cnmx-filter-btn" data-filter="prioridad">🚀 Prioridad</button>
                    <button class="cnmx-filter-btn" data-filter="anuncio">🎯 Anuncio</button>
                </div>
                
                <div class="cnmx-admin-list" id="cnmx-admin-list">
                    <div class="cnmx-admin-loading">Cargando negocios...</div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .cnmx-admin-directorio {
        padding: 40px 0;
    }
    .cnmx-admin-header {
        margin-bottom: 32px;
    }
    .cnmx-admin-header h1 {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 8px;
    }
    .cnmx-admin-content {
        background: var(--cnmx-surface);
        border-radius: var(--cnmx-radius-xl);
        padding: 32px;
        box-shadow: var(--cnmx-shadow-md);
    }
    .cnmx-admin-content h2 {
        font-size: 20px;
        margin-bottom: 8px;
    }
    .cnmx-admin-filters {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .cnmx-filter-btn {
        padding: 10px 20px;
        background: var(--cnmx-background);
        border: 2px solid var(--cnmx-border);
        border-radius: var(--cnmx-radius-full);
        cursor: pointer;
        font-weight: 500;
        transition: all var(--cnmx-transition-fast);
    }
    .cnmx-filter-btn:hover {
        border-color: var(--cnmx-primary);
    }
    .cnmx-filter-btn.active {
        background: var(--cnmx-primary);
        border-color: var(--cnmx-primary);
        color: white;
    }
    .cnmx-admin-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .cnmx-admin-loading {
        text-align: center;
        padding: 40px;
        color: var(--cnmx-text-muted);
    }
    .cnmx-negocio-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        background: var(--cnmx-background);
        border-radius: var(--cnmx-radius-lg);
        transition: all var(--cnmx-transition-fast);
    }
    .cnmx-negocio-row:hover {
        box-shadow: var(--cnmx-shadow-sm);
    }
    .cnmx-negocio-thumb {
        width: 80px;
        height: 80px;
        border-radius: var(--cnmx-radius-md);
        object-fit: cover;
    }
    .cnmx-negocio-info {
        flex: 1;
    }
    .cnmx-negocio-info h4 {
        font-size: 18px;
        margin-bottom: 4px;
    }
    .cnmx-negocio-info p {
        font-size: 14px;
        color: var(--cnmx-text-muted);
        margin: 0;
    }
    .cnmx-negocio-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .cnmx-negocio-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: var(--cnmx-radius-full);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all var(--cnmx-transition-fast);
    }
    .cnmx-negocio-badge.destacado {
        background: #FFF3ED;
        color: #EB510C;
        border-color: #EB510C;
    }
    .cnmx-negocio-badge.prioridad {
        background: #F0F9FF;
        color: #0066CC;
        border-color: #0066CC;
    }
    .cnmx-negocio-badge.anuncio {
        background: #F0FDF4;
        color: #16A34A;
        border-color: #16A34A;
    }
    .cnmx-negocio-badge.inactive {
        background: var(--cnmx-background);
        color: var(--cnmx-text-muted);
        border-color: var(--cnmx-border);
    }
    .cnmx-negocio-badge:hover {
        opacity: 0.8;
    }
    @media (max-width: 768px) {
        .cnmx-negocio-row {
            flex-wrap: wrap;
        }
        .cnmx-negocio-badges {
            width: 100%;
            justify-content: flex-start;
        }
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', async function() {
        let negocios = [];
        
        async function loadNegocios() {
            try {
                const response = await fetch('/wp-json/cuentanos/v1/todos-negocios', {
                    headers: { 'X-WP-Nonce': cnmxBizData.nonce }
                });
                negocios = await response.json();
                renderNegocios('all');
            } catch (error) {
                document.getElementById('cnmx-admin-list').innerHTML = '<p>Error al cargar negocios</p>';
            }
        }
        
        function renderNegocios(filter) {
            const container = document.getElementById('cnmx-admin-list');
            
            let filtered = negocios;
            if (filter !== 'all') {
                filtered = negocios.filter(n => {
                    if (filter === 'destacado') return n.destacado === 'si';
                    if (filter === 'prioridad') return n.prioridad === 'si';
                    if (filter === 'anuncio') return n.anuncio_activo === 'si';
                    return true;
                });
            }
            
            if (filtered.length === 0) {
                container.innerHTML = '<p style="text-align:center; padding: 40px; color: var(--cnmx-text-muted);">No hay negocios con este filtro</p>';
                return;
            }
            
            container.innerHTML = filtered.map(n => `
                <div class="cnmx-negocio-row" data-id="${n.id}">
                    <img src="${n.imagen || 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=160&h=160&fit=crop'}" class="cnmx-negocio-thumb" alt="${n.title}">
                    <div class="cnmx-negocio-info">
                        <h4>${n.title}</h4>
                        <p>${n.categoria || 'Sin categoría'} • ${n.ubicacion || 'Sin ubicación'}</p>
                    </div>
                    <div class="cnmx-negocio-badges">
                        <button class="cnmx-negocio-badge ${n.destacado === 'si' ? 'destacado' : 'inactive'}" 
                                data-field="destacado" 
                                data-value="${n.destacado === 'si' ? 'no' : 'si'}">
                            ⭐ Destacado
                        </button>
                        <button class="cnmx-negocio-badge ${n.prioridad === 'si' ? 'prioridad' : 'inactive'}" 
                                data-field="prioridad" 
                                data-value="${n.prioridad === 'si' ? 'no' : 'si'}">
                            🚀 Prioridad
                        </button>
                        <button class="cnmx-negocio-badge ${n.anuncio_activo === 'si' ? 'anuncio' : 'inactive'}" 
                                data-field="anuncio_activo" 
                                data-value="${n.anuncio_activo === 'si' ? 'no' : 'si'}">
                            🎯 Anuncio
                        </button>
                    </div>
                </div>
            `).join('');
            
            container.querySelectorAll('.cnmx-negocio-badge').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const row = this.closest('.cnmx-negocio-row');
                    const negocioId = row.dataset.id;
                    const field = this.dataset.field;
                    const value = this.dataset.value;
                    
                    try {
                        const response = await fetch('/wp-json/cnmx-biz/v1/admin-negocio', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': cnmxBizData.nonce
                            },
                            body: JSON.stringify({ negocio_id: negocioId, [field]: value })
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            loadNegocios();
                        }
                    } catch (error) {
                        alert('Error al actualizar');
                    }
                });
            });
        }
        
        document.querySelectorAll('.cnmx-filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.cnmx-filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                renderNegocios(this.dataset.filter);
            });
        });
        
        loadNegocios();
    });
    </script>
    <?php
    get_template_part('templates/partials/footer');
}

function get_the_slug() {
    global $post;
    return $post->post_name;
}