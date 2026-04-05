/**
 * Cuentanos MX - Main JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        initNavbar();
        initFavorites();
        initReviews();
        initSearch();
        initForms();
        initToastSystem();
    });
    
    function initToastSystem() {
        if (typeof CNMX !== 'undefined' && CNMX.Toast) {
            CNMX.Toast.init();
        }
    }
    
    function showToast(type, title, message) {
        if (typeof cnmxToastSuccess === 'function' && type === 'success') {
            cnmxToastSuccess(message, title);
        } else if (typeof cnmxToastError === 'function' && type === 'error') {
            cnmxToastError(message, title);
        } else if (typeof CNMX !== 'undefined' && CNMX.Toast) {
            CNMX.Toast.show({ type: type, title: title, message: message });
        } else {
            console.log(title + ': ' + message);
        }
    }
    
    // Navbar scroll effect
    function initNavbar() {
        const $navbar = $('.navbar');
        if (!$navbar.length) return;
        
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $navbar.addClass('scrolled');
            } else {
                $navbar.removeClass('scrolled');
            }
        });
    }
    
    // Favorites toggle
    function initFavorites() {
        $(document).on('click', '.btn-favorito, .business-card-fav', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $btn = $(this);
            const negocioId = $btn.data('id') || $btn.closest('[data-negocio-id]').data('negocio-id');
            
            if (!cnmxData.isLoggedIn) {
                showToast('error', 'Inicia sesión', 'Debes iniciar sesión para guardar favoritos');
                return;
            }
            
            $.ajax({
                url: cnmxData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cnmx_favorito',
                    negocio_id: negocioId,
                    nonce: cnmxData.nonce
                },
                beforeSend: function() {
                    $btn.addClass('loading');
                },
                success: function(response) {
                    if (response.success) {
                        $btn.toggleClass('active');
                        if (response.data.action === 'added') {
                            showToast('success', 'Guardado', 'Añadido a favoritos');
                        } else {
                            showToast('success', 'Eliminado', 'Eliminado de favoritos');
                        }
                    }
                },
                error: function() {
                    showToast('error', 'Error', 'No se pudo procesar la solicitud');
                }
            });
        });
    }
    
    // Reviews functionality
    function initReviews() {
        // Show review form
        $(document).on('click', '#btn-write-review', function() {
            $('#review-form').slideToggle();
        });
        
        // Star rating
        $(document).on('click', '.star-btn', function() {
            const rating = $(this).data('rating');
            const $container = $(this).closest('.rating-input');
            
            $container.find('.star-btn').each(function(i) {
                const $star = $(this);
                if (i < rating) {
                    $star.addClass('active').find('svg').attr('fill', 'currentColor');
                } else {
                    $star.removeClass('active').find('svg').attr('fill', 'none');
                }
            });
            
            $container.data('rating', rating);
        });
        
        // Submit review
        $(document).on('click', '#btn-submit-review', function() {
            const negocioId = $(this).data('negocio');
            const rating = $('.rating-input').data('rating') || 0;
            const contenido = $('#review-text').val();
            
            if (rating === 0) {
                showToast('error', 'Error', 'Selecciona una calificación');
                return;
            }
            
            if (!contenido.trim()) {
                showToast('error', 'Error', 'Escribe tu reseña');
                return;
            }
            
            $.ajax({
                url: cnmxData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cnmx_guardar_resena',
                    negocio_id: negocioId,
                    rating: rating,
                    texto: contenido,
                    nonce: cnmxData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', '¡Gracias!', 'Tu reseña ha sido enviada');
                        $('#review-text').val('');
                        $('.rating-input .star-btn svg').attr('fill', 'none');
                        $('#review-form').slideUp();
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        showToast('error', 'Error', response.data || 'No se pudo enviar la reseña');
                    }
                },
                error: function() {
                    showToast('error', 'Error', 'No se pudo enviar la reseña');
                }
            });
        });
    }
    
    // Search functionality
    function initSearch() {
        const $searchInput = $('#cnmx-buscar-negocios');
        
        if ($searchInput.length) {
            $searchInput.on('input', debounce(function() {
                const query = $(this).val().toLowerCase();
                
                $('.cnmx-negocio-card').each(function() {
                    const $card = $(this);
                    const title = $card.data('title') || '';
                    
                    if (title.toLowerCase().includes(query)) {
                        $card.show();
                    } else {
                        $card.hide();
                    }
                });
            }, 300));
        }
    }
    
    // Form handling
    function initForms() {
        // Registration form
        $(document).on('submit', '#cnmx-register-form', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            
            $.ajax({
                url: cnmxData.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=cnmx_register&nonce=' + cnmxData.nonce,
                beforeSend: function() {
                    $btn.prop('disabled', true).text('Creando cuenta...');
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', '¡Listo!', 'Cuenta creada exitosamente');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast('error', 'Error', response.data);
                        $btn.prop('disabled', false).text('Crear cuenta');
                    }
                },
                error: function() {
                    showToast('error', 'Error', 'No se pudo crear la cuenta');
                    $btn.prop('disabled', false).text('Crear cuenta');
                }
            });
        });
        
        // Login form
        $(document).on('submit', '#cnmx-login-form', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            
            $.ajax({
                url: cnmxData.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=cnmx_login&nonce=' + cnmxData.nonce,
                beforeSend: function() {
                    $btn.prop('disabled', true).text('Iniciando sesión...');
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', '¡Bienvenido!', 'Sesión iniciada');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast('error', 'Error', response.data);
                        $btn.prop('disabled', false).text('Iniciar sesión');
                    }
                },
                error: function() {
                    showToast('error', 'Error', 'Credenciales incorrectas');
                    $btn.prop('disabled', false).text('Iniciar sesión');
                }
            });
        });
    }
    
    // Debounce utility
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = function() {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
})(jQuery);
