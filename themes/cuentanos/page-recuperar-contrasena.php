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
    <a href="<?php echo home_url('/mi-cuenta'); ?>" class="cnmx-back-home">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    
    <div class="cnmx-users-container">
        <div class="cnmx-users-card">
            <div class="cnmx-users-header">
                <a href="<?php echo home_url(); ?>" class="cnmx-users-logo">
                    <img src="https://cuentanos.mx/wp-content/uploads/2026/04/LOGO-PRINCIPAL.png" alt="Cuentanos.mx">
                </a>
            </div>
            
            <h1 class="cnmx-users-title">Recuperar contraseña</h1>
            <p class="cnmx-users-subtitle">Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña</p>
            
            <div id="reset-form-container">
                <form id="cnmx-reset-form" class="cnmx-auth-form">
                    <div class="cnmx-form-group">
                        <input type="email" name="email" required placeholder="Correo electrónico">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="btn-text">Enviar enlace</span>
                        <span class="btn-loading">Enviando...</span>
                    </button>
                </form>
                
                <p class="cnmx-users-footer">
                    ¿Recordaste tu contraseña? <a href="<?php echo home_url('/mi-cuenta'); ?>">Inicia sesión</a>
                </p>
            </div>
            
            <div id="success-message" style="display: none; text-align: center; padding: 20px;">
                <div style="font-size: 48px; margin-bottom: 16px;">📧</div>
                <h3 style="margin-bottom: 8px;">Revisa tu correo</h3>
                <p style="color: var(--cnmx-text-light);">Si existe una cuenta con ese email, recibirás un enlace para restablecer tu contraseña.</p>
                <p style="margin-top: 16px;"><a href="<?php echo home_url('/mi-cuenta'); ?>">Volver al login</a></p>
            </div>
            
            <div id="error-message" style="display: none; text-align: center; padding: 20px; color: #ef4444;">
                <p id="error-text"></p>
            </div>
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
    --cnmx-radius: 12px;
    --cnmx-shadow: 0 4px 24px rgba(0,0,0,0.08);
}

body { font-family: 'Inter', sans-serif; background: var(--cnmx-background); margin: 0; }

.cnmx-users-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; position: relative; }

.cnmx-back-home { position: absolute; top: 24px; left: 24px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: white; border-radius: 50%; box-shadow: var(--cnmx-shadow); color: var(--cnmx-text); text-decoration: none; }
.cnmx-back-home:hover { transform: translateX(-4px); color: var(--cnmx-primary); }

.cnmx-users-container { width: 100%; max-width: 400px; }

.cnmx-users-card { background: white; border-radius: 20px; padding: 40px 32px; box-shadow: var(--cnmx-shadow); }

.cnmx-users-header { text-align: center; margin-bottom: 32px; }
.cnmx-users-logo img { height: 48px; width: auto; }

.cnmx-users-title { font-size: 24px; font-weight: 700; text-align: center; margin: 0 0 8px; }
.cnmx-users-subtitle { text-align: center; color: var(--cnmx-text-light); font-size: 15px; margin: 0 0 28px; line-height: 1.5; }

.cnmx-auth-form .cnmx-form-group { margin-bottom: 16px; }
.cnmx-auth-form input { width: 100%; padding: 14px 16px; border: 1.5px solid var(--cnmx-border); border-radius: var(--cnmx-radius); font-size: 15px; transition: all 0.2s; box-sizing: border-box; font-family: inherit; }
.cnmx-auth-form input:focus { outline: none; border-color: var(--cnmx-primary); box-shadow: 0 0 0 3px rgba(235, 81, 12, 0.1); }

.btn { display: flex; align-items: center; justify-content: center; padding: 14px 24px; border-radius: var(--cnmx-radius); font-weight: 600; font-size: 15px; border: none; cursor: pointer; transition: all 0.2s; font-family: inherit; }
.btn-primary { background: var(--cnmx-primary); color: white; }
.btn-primary:hover { background: var(--cnmx-primary-dark); transform: translateY(-1px); }
.btn-block { width: 100%; }
.btn-loading { display: none; }
.cnmx-auth-form.loading .btn-text { display: none; }
.cnmx-auth-form.loading .btn-loading { display: inline; }

.cnmx-users-footer { text-align: center; margin-top: 24px; color: var(--cnmx-text-light); font-size: 14px; }
.cnmx-users-footer a { color: var(--cnmx-primary); font-weight: 600; text-decoration: none; }
.cnmx-users-footer a:hover { text-decoration: underline; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cnmx-reset-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = form.querySelector('[name="email"]').value;
        
        form.classList.add('loading');
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=cnmx_reset_password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(r => r.json())
        .then(data => {
            form.classList.remove('loading');
            if (data.success) {
                document.getElementById('reset-form-container').style.display = 'none';
                document.getElementById('success-message').style.display = 'block';
            } else {
                document.getElementById('error-text').textContent = data.data?.message || 'Error al enviar el email';
                document.getElementById('error-message').style.display = 'block';
            }
        })
        .catch(err => {
            form.classList.remove('loading');
            document.getElementById('error-text').textContent = 'Error de conexión';
            document.getElementById('error-message').style.display = 'block';
        });
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
