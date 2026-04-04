<?php
/**
 * Generador de Reseñas de Muestra
 * Ejecutar una vez para crear reseñas de ejemplo
 */

if (!defined('ABSPATH')) exit;

function cnmx_generar_resenas_muestra() {
    $negocios = get_posts(array(
        'post_type' => 'negocio',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ));
    
    if (empty($negocios)) {
        echo "No hay negocios para generar reseñas.\n";
        return;
    }
    
    $usuarios = get_users(array('number' => 5));
    if (empty($usuarios)) {
        $usuarios = array((object)array('ID' => 1));
    }
    
    $resenas_data = array(
        array(
            'texto' => 'Lugares increíbles, la atención es de primera. Definitivamente volveré pronto. El ambiente es muy tranquilo y la comida estaba deliciosa.',
            'rating' => 5,
            'fotos' => array(
                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400',
                'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400',
            ),
        ),
        array(
            'texto' => 'Muy buen servicio, pero esperaba un poco más de variedad en el menú. Los precios son accesibles y la ubicación es perfecta.',
            'rating' => 4,
            'fotos' => array(),
        ),
        array(
            'texto' => 'Una experiencia inolvidable. Desde que llegas te sientes muy bien atendido. Los detalles importan y aquí los cuidan mucho.',
            'rating' => 5,
            'fotos' => array(
                'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=400',
            ),
        ),
        array(
            'texto' => 'El lugar es bonito pero estaba un poco ruidoso. La comida llegó rápida aunque fría. Creo que pueden mejorar.',
            'rating' => 3,
            'fotos' => array(),
        ),
        array(
            'texto' => '¡Best discovery ever! Me encanta este lugar. Vengo cada fin de semana con mi familia. Los niños aman el menú infantil.',
            'rating' => 5,
            'fotos' => array(
                'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400',
            ),
        ),
        array(
            'texto' => 'Buena relación calidad-precio. El personal es muy amable y servicial. Lo recomiendo para citas o reuniones de trabajo.',
            'rating' => 4,
            'fotos' => array(),
        ),
        array(
            'texto' => 'Exceeded my expectations! The food was amazing and the atmosphere was perfect for our celebration. Will come back for sure.',
            'rating' => 5,
            'fotos' => array(
                'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=400',
                'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=400',
            ),
        ),
        array(
            'texto' => 'Regular, nada especial. Había mejores opciones en la zona. El tiempo de espera fue un poco largo.',
            'rating' => 3,
            'fotos' => array(),
        ),
        array(
            'texto' => 'Me encanta cómo decoran el lugar. Cada vez que vengo hay detalles nuevos. La atención es de 10.',
            'rating' => 5,
            'fotos' => array(
                'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=400',
            ),
        ),
        array(
            'texto' => 'Perfecto para una tarde con amigos. Las bebidas son originales y los snacks deliciosos. El WiFi es bueno para trabajar.',
            'rating' => 4,
            'fotos' => array(),
        ),
        array(
            'texto' => 'La comida casera es simplemente increíble. Sabe a hogar. Los postres son una delicia que no te puedes perder.',
            'rating' => 5,
            'fotos' => array(
                'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400',
            ),
        ),
        array(
            'texto' => 'Buen lugar para eventos. Fuimos a una boda y todo estuvo perfecto. El catering estaba exquisito.',
            'rating' => 5,
            'fotos' => array(
                'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=400',
            ),
        ),
    );
    
    $count = 0;
    $dias_atras = 30;
    
    foreach ($negocios as $negocio) {
        $num_resenas = rand(2, 4);
        
        for ($i = 0; $i < $num_resenas; $i++) {
            $resena = $resenas_data[array_rand($resenas_data)];
            $usuario = $usuarios[array_rand($usuarios)];
            
            $args = array(
                'post_type' => 'cnmx_resena',
                'post_status' => 'publish',
                'post_title' => 'Reseña de ' . $usuario->display_name . ' sobre ' . $negocio->post_title,
                'post_content' => $resena['texto'],
            );
            
            $resena_id = wp_insert_post($args);
            
            if ($resena_id && !is_wp_error($resena_id)) {
                update_post_meta($resena_id, 'cnmx_negocio_id', $negocio->ID);
                update_post_meta($resena_id, 'cnmx_user_id', $usuario->ID);
                update_post_meta($resena_id, 'cnmx_rating', $resena['rating']);
                
                if (!empty($resena['fotos'])) {
                    update_post_meta($resena_id, 'cnmx_fotos', implode("\n", $resena['fotos']));
                }
                
                $fecha_resena = date('Y-m-d H:i:s', strtotime("-{$dias_atras} days"));
                update_post_meta($resena_id, 'cnmx_fecha_resena', $fecha_resena);
                
                $count++;
                $dias_atras = max(1, $dias_atras - rand(1, 3));
            }
        }
    }
    
    echo "Se generaron {$count} reseñas de muestra.\n";
    return $count;
}

add_action('admin_init', function() {
    if (isset($_GET['cnmx_generar_resenas']) && current_user_can('manage_options')) {
        cnmx_generar_resenas_muestra();
        wp_die('Reseñas generadas. <a href="' . admin_url('edit.php?post_type=cnmx_resena') . '">Ver reseñas</a>');
    }
});
