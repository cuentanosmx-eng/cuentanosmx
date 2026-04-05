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
    <div class="cnmx-users-container">
        <a href="<?php echo home_url(); ?>" class="cnmx-back-home">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        
        <div class="cnmx-users-card">
            <div class="cnmx-users-header">
                <a href="<?php echo home_url(); ?>" class="cnmx-users-logo">
                    <img src="https://cuentanos.mx/wp-content/uploads/2026/04/LOGO-PRINCIPAL.png" alt="Cuentanos.mx">
                </a>
            </div>
            
            <h1 class="cnmx-users-title">Nueva contraseña</h1>
            <p class="cnmx-users-subtitle">Ingresa tu nueva contraseña</p>
            
            <div id="error-message" class="cnmx-error-message" style="display: none;">
                <span id="error-text"></span>
            </div>
            
            <form id="cnmx-new-password-form" class="cnmx-auth-form">
                <input type="hidden" name="user_key" value="<?php echo esc_attr($_GET['key'] ?? ''); ?>">
                <input type="hidden" name="user_login" value="<?php echo esc_attr($_GET['login'] ?? ''); ?>">
                
                <div class="cnmx-form-group">
                    <input type="password" name="new_password" placeholder="Nueva contraseña" required>
                </div>
                
                <div class="cnmx-form-group">
                    <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
                </div>
                
                <button type="submit" class="cnmx-btn-primary">
                    <span>Guardar contraseña</span>
                </button>
            </form>
            
            <div id="success-message" class="cnmx-success-message" style="display: none;">
                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#10B981" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M8 12l3 3 5-5"/>
                </svg>
                <h3>¡Contraseña actualizada!</h3>
                <p>Ahora puedes iniciar sesión con tu nueva contraseña.</p>
                <a href="<?php echo home_url('/mi-cuenta'); ?>" class="cnmx-btn-primary" style="margin-top: 16px;">
                    <span>Iniciar sesión</span>
                </a>
            </div>
            
            <div class="cnmx-users-footer">
                <a href="<?php echo home_url('/mi-cuenta'); ?>">Volver al inicio de sesión</a>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --cnmx-primary: #EB510C;
    --cnmx-primary-dark: #D44A0B;
    --cnmx-bg: #FFFCF8;
    --cnmx-card: #FFFFFF;
    --cnmx-text: #1A1A1A;
    --cnmx-text-light: #6B7280;
    --cnmx-text-muted: #9CA3AF;
    --cnmx-border: #E5E7EB;
    --cnmx-radius: 12px;
    --cnmx-shadow: 0 4px 24px rgba(0,0,0,0.08);
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--cnmx-bg);
    color: var(--cnmx-text);
    min-height: 100vh;
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
    background: var(--cnmx-card);
    border-radius: var(--cnmx-radius);
    padding: 40px;
    box-shadow: var(--cnmx-shadow);
    text-align: center;
}

.cnmx-users-header {
    margin-bottom: 24px;
}

.cnmx-users-logo img {
    height: 48px;
    width: auto;
}

.cnmx-users-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--cnmx-text);
}

.cnmx-users-subtitle {
    font-size: 14px;
    color: var(--cnmx-text-light);
    margin-bottom: 24px;
}

.cnmx-error-message {
    background: #FEE2E2;
    color: #DC2626;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 16px;
}

.cnmx-success-message {
    padding: 24px;
}

.cnmx-success-message h3 {
    font-size: 20px;
    margin: 16px 0 8px;
    color: #10B981;
}

.cnmx-success-message p {
    color: var(--cnmx-text-light);
    font-size: 14px;
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
}

.cnmx-auth-form input:focus {
    outline: none;
    border-color: var(--cnmx-primary);
    box-shadow: 0 0 0 3px rgba(235, 81, 12, 0.1);
}

.cnmx-btn-primary {
    width: 100%;
    padding: 14px 24px;
    background: var(--cnmx-primary);
    color: white;
    border: none;
    border-radius: var(--cnmx-radius);
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.cnmx-btn-primary:hover {
    background: var(--cnmx-primary-dark);
    transform: translateY(-1px);
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

.loading .cnmx-btn-primary {
    opacity: 0.7;
    pointer-events: none;
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cnmx-new-password-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const newPass = form.querySelector('[name="new_password"]').value;
        const confirmPass = form.querySelector('[name="confirm_password"]').value;
        const userKey = form.querySelector('[name="user_key"]').value;
        const userLogin = form.querySelector('[name="user_login"]').value;
        
        if (newPass !== confirmPass) {
            document.getElementById('error-text').textContent = 'Las contraseñas no coinciden';
            document.getElementById('error-message').style.display = 'block';
            return;
        }
        
        if (newPass.length < 8) {
            document.getElementById('error-text').textContent = 'La contraseña debe tener al menos 8 caracteres';
            document.getElementById('error-message').style.display = 'block';
            return;
        }
        
        form.classList.add('loading');
        
        const formData = new FormData();
        formData.append('user_key', userKey);
        formData.append('user_login', userLogin);
        formData.append('new_password', newPass);
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=cnmx_set_new_password', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            form.classList.remove('loading');
            if (data.success) {
                form.style.display = 'none';
                document.getElementById('success-message').style.display = 'block';
            } else {
                document.getElementById('error-text').textContent = data.data?.message || 'Error al guardar la contraseña';
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
