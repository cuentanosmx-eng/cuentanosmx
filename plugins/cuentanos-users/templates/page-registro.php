<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="cnmx-users-page">
    <div class="cnmx-users-container">
        <div class="cnmx-users-card">
            <div class="cnmx-users-header">
                <a href="<?php echo home_url(); ?>" class="cnmx-users-logo">
                    <span class="cnmx-logo-icon">📍</span>
                    <span>Cuentanos.mx</span>
                </a>
            </div>
            
            <h1 class="cnmx-users-title">🎤 ¡Únete a la comunidad!</h1>
            <p class="cnmx-users-subtitle">Crea tu cuenta gratis y empieza a ganar Megáfonos</p>
            
            <form id="cnmx-user-register-form" class="cnmx-auth-form">
                <div class="cnmx-form-group">
                    <label>Nombre completo</label>
                    <input type="text" name="nombre" required placeholder="Ej: Juan Pérez">
                </div>
                <div class="cnmx-form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="tu@email.com">
                </div>
                <div class="cnmx-form-group">
                    <label>Fecha de cumpleaños</label>
                    <input type="date" name="cumpleanos" required placeholder="DD/MM/AAAA">
                    <span class="description">Recibe Megáfonos extra en tu cumpleaños 🎂</span>
                </div>
                <div class="cnmx-form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
                </div>
                
                <div class="cnmx-bonus-box">
                    <span class="cnmx-bonus-icon">🎁</span>
                    <span>Te regalamos <strong>10 Megáfonos</strong> de bienvenida</span>
                </div>
                
                <div class="cnmx-terminos">
                    <input type="checkbox" id="terminos" required>
                    <label for="terminos">Acepto los <a href="#">Términos</a> y <a href="#">Privacidad</a></label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="btn-text">Crear mi cuenta gratis</span>
                    <span class="btn-loading" style="display: none;">Creando...</span>
                </button>
            </form>
            
            <p class="cnmx-users-footer">
                ¿Ya tienes cuenta? <a href="<?php echo home_url('/mi-cuenta'); ?>">Inicia sesión</a>
            </p>
        </div>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
