<div class="wrap cnmx-admin-wrap">
    <h1>Usuarios Registrados</h1>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th width="50">ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Megáfonos</th>
                <th>Rol</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody id="usuarios-list">
            <?php
            $users = get_users([
                'number' => 50,
                'orderby' => 'registered',
                'order' => 'DESC',
            ]);
            
            global $wpdb;
            
            if ($users):
                foreach ($users as $user):
                    $megafonos = $wpdb->get_var($wpdb->prepare(
                        "SELECT meta_value FROM {$wpdb->prefix}cnmx_usuarios_meta WHERE user_id = %d AND meta_key = 'megafonos'",
                        $user->ID
                    ));
            ?>
                <tr>
                    <td><?php echo $user->ID; ?></td>
                    <td><strong><?php echo esc_html($user->display_name); ?></strong></td>
                    <td><?php echo esc_html($user->user_email); ?></td>
                    <td>
                        <span class="cnmx-megafonos-badge">🎤 <?php echo intval($megafonos); ?></span>
                    </td>
                    <td><?php echo implode(', ', $user->roles); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($user->user_registered)); ?></td>
                </tr>
            <?php 
                endforeach;
            else:
            ?>
                <tr><td colspan="6">No hay usuarios</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
