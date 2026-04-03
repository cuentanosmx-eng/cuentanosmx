<div class="wrap cnmx-admin-wrap">
    <h1>Negocios Pendientes <span class="count">(<span id="pending-count">0</span>)</span></h1>
    
    <p class="description">Negocios que esperan aprobación para publicarse en el directorio.</p>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th width="50">ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Categoría</th>
                <th>Fecha</th>
                <th width="250">Acciones</th>
            </tr>
        </thead>
        <tbody id="pendientes-list">
            <?php
            $negocios = new WP_Query([
                'post_type' => 'negocio',
                'post_status' => 'pending',
                'posts_per_page' => -1,
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
                    <td><?php echo esc_html($meta['email'][0] ?? '-'); ?></td>
                    <td><?php echo $categorias ? implode(', ', wp_list_pluck($categorias, 'name')) : '-'; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($post->post_date)); ?></td>
                    <td>
                        <button class="button button-primary cnmx-btn-approve" data-id="<?php echo $post->ID; ?>">
                            Aprobar
                        </button>
                        <button class="button cnmx-btn-reject" data-id="<?php echo $post->ID; ?>">
                            Rechazar
                        </button>
                        <a href="<?php echo get_edit_post_link($post->ID); ?>" class="button">
                            Revisar
                        </a>
                    </td>
                </tr>
            <?php 
                endwhile;
                wp_reset_postdata();
            else:
            ?>
                <tr><td colspan="6">No hay negocios pendientes</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
