/**
 * Cuentanos Reviews - JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        initReacciones();
        initLightbox();
    });
    
    function initReacciones() {
        $(document).on('click', '.reaccion-btn', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var emoji = $btn.data('emoji');
            var resenaId = $btn.data('resena');
            
            $.ajax({
                url: cnmxReviews.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cnmx_reaccionar',
                    nonce: cnmxReviews.nonce,
                    emoji: emoji,
                    resena_id: resenaId
                },
                success: function(response) {
                    if (response.success) {
                        updateReaccionUI($btn, response.data);
                    }
                },
                error: function() {
                    alert('Error al procesar la reacción');
                }
            });
        });
        
        $(document).on('click', '.reaccion-badge', function() {
            var resenaId = $(this).closest('.review-card').data('resena-id');
            console.log('Ver reacciones de:', resenaId);
        });
    }
    
    function updateReaccionUI($btn, data) {
        var $card = $btn.closest('.review-card');
        var resenaId = $card.data('resena-id');
        
        if (data.action === 'added') {
            $btn.addClass('active');
        } else {
            $btn.removeClass('active');
        }
        
        $.ajax({
            url: cnmxReviews.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cnmx_get_resena_reacciones',
                nonce: cnmxReviews.nonce,
                resena_id: resenaId
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    $card.find('.review-reacciones-list').html(response.data.html);
                }
            }
        });
    }
    
    function initLightbox() {
        $(document).on('click', '.review-foto img', function(e) {
            e.preventDefault();
            var src = $(this).attr('src');
            
            var lightbox = $('<div class="lightbox-overlay"><button class="lightbox-close">&times;</button><img src="' + src + '"></div>');
            $('body').append(lightbox).css('overflow', 'hidden');
            
            lightbox.fadeIn();
            
            lightbox.on('click', '.lightbox-close', function() {
                lightbox.fadeOut(function() {
                    $(this).remove();
                    $('body').css('overflow', '');
                });
            });
            
            lightbox.on('click', function(e) {
                if (e.target === this) {
                    lightbox.fadeOut(function() {
                        $(this).remove();
                        $('body').css('overflow', '');
                    });
                }
            });
        });
    }
    
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('.lightbox-overlay').fadeOut(function() {
                $(this).remove();
                $('body').css('overflow', '');
            });
        }
    });
    
})(jQuery);
