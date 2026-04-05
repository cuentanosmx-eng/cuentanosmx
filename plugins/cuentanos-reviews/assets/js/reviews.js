/**
 * Cuentanos Reviews - JavaScript
 */

(function($) {
    'use strict';
    
    var fotoUrls = [];
    
    $(document).ready(function() {
        initReacciones();
        initFormResena();
    });
    
    function showToast(type, title, msg) {
        if (window.cnmxToast) {
            window.cnmxToast({ type: type, title: title, message: msg });
        } else if (window.CNMX && CNMX.Toast) {
            CNMX.Toast.show({ type: type, title: title, message: msg });
        } else {
            alert(title + ': ' + msg);
        }
    }
    
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
                    showToast('error', 'Error', 'No se pudo procesar la reacción');
                }
            });
        });
    }
    
    function updateReaccionUI($btn, data) {
        var $card = $btn.closest('.review-card');
        var resenaId = $card.data('resena-id');
        
        if (data.action === 'added') {
            $btn.addClass('active');
            showToast('success', 'Guardado', 'Reacción guardada');
        } else {
            $btn.removeClass('active');
        }
    }
    
    function initFormResena() {
        var $form = $('#cnmx-resena-form');
        if (!$form.length) return;
        
        $form.on('click', '.rating-star', function() {
            var rating = $(this).data('rating');
            $('#resena-rating-value').val(rating);
            $('.rating-star').removeClass('active');
            $('.rating-star').each(function() {
                if ($(this).data('rating') <= rating) {
                    $(this).addClass('active');
                }
            });
        });
        
        $('.rating-star').each(function() {
            if ($(this).data('rating') <= 5) {
                $(this).addClass('active');
            }
        });
        
        $('#upload-foto-device').on('change', function(e) {
            handleFotoUpload(e.target.files[0], 'device');
        });
        
        $('#upload-foto-camera').on('change', function(e) {
            handleFotoUpload(e.target.files[0], 'camera');
        });
        
        $(document).on('click', '.remove-foto', function() {
            var index = $(this).closest('.foto-item').index();
            fotoUrls.splice(index, 1);
            renderFotoPreview();
            updateFotosInput();
        });
        
        $form.on('submit', function(e) {
            e.preventDefault();
            
            var $submitBtn = $form.find('.resena-submit-btn');
            var originalText = $submitBtn.text();
            
            $submitBtn.text('Publicando...').prop('disabled', true);
            
            $.ajax({
                url: cnmxReviews.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cnmx_guardar_resena',
                    nonce: cnmxReviews.nonce,
                    negocio_id: $form.find('[name="negocio_id"]').val(),
                    rating: $('#resena-rating-value').val(),
                    texto: $form.find('[name="texto"]').val(),
                    fotos: fotoUrls
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', '¡Listo!', 'Tu reseña ha sido publicada');
                        $form[0].reset();
                        fotoUrls = [];
                        renderFotoPreview();
                        $('.rating-star').removeClass('active');
                        $('.rating-star[data-rating="5"]').addClass('active');
                        $('#resena-rating-value').val(5);
                        location.reload();
                    } else {
                        showToast('error', 'Error', response.data || 'No se pudo guardar la reseña');
                    }
                },
                error: function() {
                    showToast('error', 'Error', 'No se pudo procesar la solicitud');
                },
                complete: function() {
                    $submitBtn.text(originalText).prop('disabled', false);
                }
            });
        });
    }
    
    function handleFotoUpload(file, source) {
        if (!file) return;
        
        if (!file.type.match('image.*')) {
            showToast('error', 'Error', 'Selecciona una imagen válida');
            return;
        }
        
        var formData = new FormData();
        formData.append('foto', file);
        formData.append('action', 'cnmx_subir_foto');
        formData.append('nonce', cnmxReviews.nonce);
        
        $.ajax({
            url: cnmxReviews.ajaxUrl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    fotoUrls.push(response.data.url);
                    renderFotoPreview();
                    updateFotosInput();
                    showToast('success', '¡Foto agregada!', 'Imagen subida correctamente');
                } else {
                    showToast('error', 'Error', response.data || 'No se pudo subir la imagen');
                }
            },
            error: function() {
                showToast('error', 'Error', 'No se pudo subir la imagen');
            }
        });
    }
    
    function renderFotoPreview() {
        var $preview = $('#fotos-preview');
        $preview.empty();
        
        fotoUrls.forEach(function(url, index) {
            var html = '<div class="foto-item">';
            html += '<img src="' + url + '" alt="Foto">';
            html += '<button type="button" class="remove-foto">×</button>';
            html += '</div>';
            $preview.append(html);
        });
    }
    
    function updateFotosInput() {
        $('#resena-fotos-value').val(JSON.stringify(fotoUrls));
    }
    
})(jQuery);
