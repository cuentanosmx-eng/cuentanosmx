<?php
/**
 * Script para crear cuenta de Admin del Directorio
 * Ejecutar desde WP-CLI: wp eval-file crear-admin-directorio.php
 */

// Datos del admin (cambia estos valores)
$email = 'admin@cuentanos.mx';
$password = 'AdminCnmx2024!';
$nombre = 'Administrador';

// Verificar si el email ya existe
if (email_exists($email)) {
    $user = get_user_by('email', $email);
    // Asignar rol de admin_directorio
    $user->set_role('admin_directorio');
    update_user_meta($user->ID, 'cnmx_tipo_cuenta', 'admin_directorio');
    echo "✓ Usuario actualizado: $email\n";
    echo "  - Rol: admin_directorio\n";
    echo "  - ID: {$user->ID}\n";
} else {
    // Crear nuevo usuario
    $user_id = wp_create_user($email, $password, $email);
    
    if (is_wp_error($user_id)) {
        echo "✗ Error al crear usuario: " . $user_id->get_error_message() . "\n";
        exit(1);
    }
    
    wp_update_user([
        'ID' => $user_id,
        'display_name' => $nombre,
        'role' => 'admin_directorio',
    ]);
    
    update_user_meta($user_id, 'cnmx_tipo_cuenta', 'admin_directorio');
    
    echo "✓ Cuenta creada exitosamente:\n";
    echo "  - Email: $email\n";
    echo "  - Contraseña: $password\n";
    echo "  - ID: $user_id\n";
}

echo "\n📝 Ahora puedes iniciar sesión en:\n";
echo "  cuentanos.mx/login-negocio\n";
echo "   (o la página que uses para login)\n";
