<div class="wrap cnmx-admin-wrap">
    <h1>Configuración General</h1>
    
    <form method="post" action="options.php">
        <?php settings_fields('cnmx_settings_group'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="cnmx_google_maps_api">Google Maps API Key</label>
                </th>
                <td>
                    <input type="text" name="cnmx_google_maps_api" id="cnmx_google_maps_api" 
                           value="<?php echo esc_attr(get_option('cnmx_google_maps_api', '')); ?>" 
                           class="regular-text">
                    <p class="description">Opcional. Si no se configura, se usará OpenStreetMap (gratuito).</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cnmx_megafonos_bienvenida">Megáfonos de Bienvenida</label>
                </th>
                <td>
                    <input type="number" name="cnmx_megafonos_bienvenida" id="cnmx_megafonos_bienvenida" 
                           value="<?php echo esc_attr(get_option('cnmx_megafonos_bienvenida', 10)); ?>" 
                           class="small-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cnmx_megafonos_resena">Megáfonos por Reseña</label>
                </th>
                <td>
                    <input type="number" name="cnmx_megafonos_resena" id="cnmx_megafonos_resena" 
                           value="<?php echo esc_attr(get_option('cnmx_megafonos_resena', 5)); ?>" 
                           class="small-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cnmx_megafonos_favorito">Megáfonos por Favorito</label>
                </th>
                <td>
                    <input type="number" name="cnmx_megafonos_favorito" id="cnmx_megafonos_favorito" 
                           value="<?php echo esc_attr(get_option('cnmx_megafonos_favorito', 2)); ?>" 
                           class="small-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cnmx_megafonos_compartir">Megáfonos por Compartir</label>
                </th>
                <td>
                    <input type="number" name="cnmx_megafonos_compartir" id="cnmx_megafonos_compartir" 
                           value="<?php echo esc_attr(get_option('cnmx_megafonos_compartir', 3)); ?>" 
                           class="small-text">
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Guardar Cambios">
        </p>
    </form>
</div>
