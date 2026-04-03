<div class="wrap cnmx-admin-wrap">
    <h1>Gestionar Negocios</h1>
    
    <div class="cnmx-admin-toolbar">
        <form method="get">
            <input type="hidden" name="page" value="cuentanos-negocios">
            <select name="status">
                <option value="publish">Publicados</option>
                <option value="pending">Pendientes</option>
                <option value="draft">Borradores</option>
            </select>
            <input type="search" name="s" placeholder="Buscar...">
            <button type="submit" class="button">Filtrar</button>
        </form>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th width="50">ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Ciudad</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th width="200">Acciones</th>
            </tr>
        </thead>
        <tbody id="negocios-list">
            <?php
            $negocios = new WP_Query([
                'post_type' => 'negocio',
                'post_status' => isset($_GET['status']) ? $_GET['status'] : 'publish',
                'posts_per_page' => 20,
            ]);
            
            if ($negocios->have_posts()):
                while ($negocios->have_posts()): $negocios->the_post();
                    global $post;
                    $categorias = get_the_terms($post->ID, 'categoria');
                    $meta = get_post_meta($post->ID);
            ?>
                <tr data-id="<?php echo $post->ID; ?>">
                    <td><?php echo $post->ID; ?></td>
                    <td>
                        <strong><?php echo esc_html($post->post_title); ?></strong>
                    </td>
                    <td><?php echo $categorias ? implode(', ', wp_list_pluck($categorias, 'name')) : '-'; ?></td>
                    <td><?php echo esc_html($meta['ciudad'][0] ?? '-'); ?></td>
                    <td>
                        <span class="cnmx-status-badge status-<?php echo $post->post_status; ?>">
                            <?php echo ucfirst($post->post_status); ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($post->post_date)); ?></td>
                    <td>
                        <a href="<?php echo get_permalink($post->ID); ?>" class="button button-small" target="_blank">Ver</a>
                        <a href="<?php echo get_edit_post_link($post->ID); ?>" class="button button-small">Editar</a>
                        <button class="button button-small cnmx-btn-featured" data-id="<?php echo $post->ID; ?>">
                            Destacar
                        </button>
                    </td>
                </tr>
            <?php 
                endwhile;
                wp_reset_postdata();
            else:
            ?>
                <tr><td colspan="7">No hay negocios</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
