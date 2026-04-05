<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body>
<div class="cnmx-users-page">
    <a href="<?php echo home_url(); ?>" class="cn mx-back-home">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    
    <div class="cnmx-users-container">
        <div class="cnmx-users-card">
            <div class="cnmx-users-header">
                <a href="<?php echo home_url(); ?>" class="cnmx-users-logo">
                    <img src="https://cuentanos.mx/wp-content/uploads/2026/04/LOGO-PRINCIPAL.png" alt="Cuentanos.mx">
                </a>
            </div>
            
            <h1 class="cnmx-users-title">Crear cuenta</h1>
            <p class="cnmx-users-subtitle">¡Únete a la comunidad Cuentanos!</p>
            
            <div class="cnmx-social-buttons">
                <button type="button" class="cnmx-social-btn cnmx-btn-google" onclick="socialLogin('google')">
                    <svg viewBox="0 0 24 24" width="20" height="20">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>Continuar con Google</span>
                </button>
                
                <button type="button" class="cnmx-social-btn cnmx-btn-facebook" onclick="socialLogin('facebook')">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>Continuar con Facebook</span>
                </button>
            </div>
            
            <div class="cnmx-divider">
                <span>o</span>
            </div>
            
            <form id="cnmx-user-register-form" class="cnmx-auth-form">
                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('cnmx_nonce'); ?>">
                <div class="cnmx-form-group">
                    <input type="text" name="nombre" required placeholder="Nombre completo">
                </div>
                <div class="cnmx-form-group">
                    <input type="email" name="email" required placeholder="Correo electrónico">
                </div>
                <div class="cnmx-form-group">
                    <input type="date" name="cumpleanos" required>
                </div>
                <div class="cnmx-form-group">
                    <input type="password" name="password" required minlength="6" placeholder="Contraseña (mínimo 6 caracteres)">
                </div>
                
                <div class="cnmx-bonus-box">
                    <span class="cnmx-bonus-icon">🎁</span>
                    <span>Te regalamos <strong>50 Megáfonos</strong> de bienvenida</span>
                </div>
                
                <div class="cnmx-terminos">
                    <input type="checkbox" id="terminos" required>
                    <label for="terminos">Acepto los <a href="#">Términos</a> y <a href="#">Privacidad</a></label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="btn-text">Crear mi cuenta</span>
                    <span class="btn-loading">Creando...</span>
                </button>
            </form>
            
            <p class="cnmx-users-footer">
                ¿Ya tienes cuenta? <a href="<?php echo home_url('/mi-cuenta'); ?>">Inicia sesión</a>
            </p>
        </div>
    </div>
</div>

<style>
:root {
    --cnmx-primary: #EB510C;
    --cnmx-primary-dark: #C94409;
    --cnmx-surface: #FFFCF8;
    --cnmx-background: #F5F5F5;
    --cnmx-border: #E0E0E0;
    --cnmx-text: #1a1a2e;
    --cnmx-text-light: #717171;
    --cnmx-text-muted: #9CA3AF;
    --cnmx-radius: 12px;
    --cnmx-shadow: 0 4px 24px rgba(0,0,0,0.08);
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--cnmx-background);
    color: var(--cnmx-text);
    margin: 0;
    padding: 0;
}

.cnmx-users-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    position: relative;
}

.cnmx-back-home {
    position: absolute;
    top: 24px;
    left: 24px;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 50%;
    box-shadow: var(--cnmx-shadow);
    color: var(--cnmx-text);
    transition: all 0.2s;
    text-decoration: none;
}

.cnmx-back-home:hover {
    transform: translateX(-4px);
    color: var(--cnmx-primary);
}

.cnmx-users-container {
    width: 100%;
    max-width: 400px;
}

.cnmx-users-card {
    background: white;
    border-radius: 20px;
    padding: 40px 32px;
    box-shadow: var(--cnmx-shadow);
}

.cnmx-users-header {
    text-align: center;
    margin-bottom: 32px;
}

.cnmx-users-logo img {
    height: 48px;
    width: auto;
}

.cnmx-users-title {
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    margin: 0 0 8px;
    color: var(--cnmx-text);
}

.cnmx-users-subtitle {
    text-align: center;
    color: var(--cnmx-text-light);
    font-size: 15px;
    margin: 0 0 28px;
}

.cnmx-social-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.cnmx-social-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 14px 20px;
    border: 1.5px solid var(--cnmx-border);
    border-radius: var(--cnmx-radius);
    background: white;
    font-size: 15px;
    font-weight: 500;
    color: var(--cnmx-text);
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
}

.cnmx-social-btn:hover {
    border-color: var(--cnmx-text);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.cnmx-btn-google:hover {
    border-color: #4285F4;
}

.cnmx-btn-facebook:hover {
    border-color: #1877F2;
}

.cnmx-divider {
    display: flex;
    align-items: center;
    gap: 16px;
    margin: 24px 0;
}

.cnmx-divider::before,
.cnmx-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--cnmx-border);
}

.cnmx-divider span {
    color: var(--cnmx-text-muted);
    font-size: 13px;
}

.cnmx-auth-form .cnmx-form-group {
    margin-bottom: 16px;
}

.cnmx-auth-form input {
    width: 100%;
    padding: 14px 16px;
    border: 1.5px solid var(--cnmx-border);
    border-radius: var(--cnmx-radius);
    font-size: 15px;
    transition: all 0.2s;
    font-family: inherit;
    box-sizing: border-box;
}

.cnmx-auth-form input:focus {
    outline: none;
    border-color: var(--cnmx-primary);
    box-shadow: 0 0 0 3px rgba(235, 81, 12, 0.1);
}

.cnmx-auth-form input::placeholder {
    color: var(--cnmx-text-muted);
}

.cnmx-bonus-box {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
    background: linear-gradient(135deg, rgba(235, 81, 12, 0.08), rgba(248, 157, 47, 0.08));
    border-radius: var(--cnmx-radius);
    margin-bottom: 16px;
    font-size: 14px;
}

.cnmx-bonus-icon {
    font-size: 20px;
}

.cnmx-terminos {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
    font-size: 13px;
    color: var(--cnmx-text-light);
}

.cnmx-terminos input {
    margin-top: 3px;
}

.cnmx-terminos a {
    color: var(--cnmx-primary);
    text-decoration: none;
}

.cnmx-terminos a:hover {
    text-decoration: underline;
}

.btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px 24px;
    border-radius: var(--cnmx-radius);
    font-weight: 600;
    font-size: 15px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
}

.btn-primary {
    background: var(--cnmx-primary);
    color: white;
}

.btn-primary:hover {
    background: var(--cnmx-primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(235, 81, 12, 0.3);
}

.btn-block {
    width: 100%;
}

.btn-loading {
    display: none;
}

.cnmx-auth-form.loading .btn-text {
    display: none;
}

.cnmx-auth-form.loading .btn-loading {
    display: inline;
}

.cnmx-users-footer {
    text-align: center;
    margin-top: 24px;
    color: var(--cnmx-text-light);
    font-size: 14px;
}

.cnmx-users-footer a {
    color: var(--cnmx-primary);
    font-weight: 600;
    text-decoration: none;
}

.cnmx-users-footer a:hover {
    text-decoration: underline;
}

@media (max-width: 480px) {
    .cnmx-users-card {
        padding: 32px 24px;
    }
    
    .cnmx-back-home {
        top: 16px;
        left: 16px;
    }
}
</style>

<script>
function socialLogin(provider) {
    const redirectUrl = '<?php echo home_url('/perfil'); ?>';
    const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>?action=cnmx_social_login_url';
    
    const btn = event.target.closest('button');
    const originalText = btn.querySelector('span').textContent;
    btn.querySelector('span').textContent = 'Cargando...';
    btn.disabled = true;
    
    fetch(ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'provider=' + provider + '&redirect=' + encodeURIComponent(redirectUrl)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.data.url) {
            window.location.href = data.data.url;
        } else {
            btn.querySelector('span').textContent = originalText;
            btn.disabled = false;
            alert('Error al iniciar sesión social');
        }
    })
    .catch(err => {
        btn.querySelector('span').textContent = originalText;
        btn.disabled = false;
        alert('Error de conexión');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cnmx-user-register-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const data = new FormData(form);
        
        form.classList.add('loading');
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=cnmx_register', {
            method: 'POST',
            body: data
        })
        .then(r => r.json())
        .then(data => {
            form.classList.remove('loading');
            if (data.success) {
                window.location.href = data.data?.redirect || '<?php echo home_url('/perfil'); ?>';
            } else {
                alert(data.data || 'Error al crear cuenta');
            }
        })
        .catch(err => {
            form.classList.remove('loading');
            alert('Error de conexión');
        });
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
