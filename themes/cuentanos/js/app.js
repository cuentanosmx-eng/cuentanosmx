/**
 * Cuentanos MX - Main JavaScript
 */

(function($) {
    'use strict';
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        initNavbar();
        initFavorites();
        initReviews();
        initSearch();
    });
    
    // Navbar scroll effect
    function initNavbar() {
        const navbar = $('.navbar');
        if (!navbar.length) return;
        
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                navbar.addClass('scrolled');
            } else {
                navbar.removeClass('scrolled');
            }
        });
    }
    
    // Favorites toggle
    function initFavorites() {
        $('.business-card-fav, .action-btn-fav').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $btn = $(this);
            const negocioId = $btn.data('id') || $btn.closest('[data-id]').data('id');
            
            $btn.toggleClass('active');
            
            if (typeof cnmxData !== 'undefined') {
                $.ajax({
                    url: cnmxData.apiUrl + '/favorito',
                    method: 'POST',
                    headers: {
                        'X-WP-Nonce': cnmxData.nonce
                    },
                    data: {
                        negocio_id: negocioId
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', 'Guardado', 'Añadido a favoritos');
                        } else {
                            showToast('success', 'Eliminado', 'Eliminado de favoritos');
                        }
                    }
                });
            }
        });
    }
    
    // Reviews functionality
    function initReviews() {
        const $form = $('#review-form');
        const $btnWrite = $('#btn-write-review');
        
        if ($btnWrite.length && $form.length) {
            $btnWrite.on('click', function() {
                $form.slideToggle();
            });
        }
        
        // Star rating input
        $('.star-btn').on('click', function() {
            const rating = $(this).data('rating');
            $(this).closest('.rating-input').find('.star-btn').each(function(i) {
                if (i < rating) {
                    $(this).addClass('active').find('svg').attr('fill', 'currentColor');
                } else {
                    $(this).removeClass('active').find('svg').attr('fill', 'none');
                }
            });
            $(this).closest('.rating-input').data('selected-rating', rating);
        });
        
        // Submit review
        $('#btn-submit-review').on('click', function() {
            const negocioId = $(this).data('negocio');
            const rating = $('.rating-input').data('selected-rating') || 0;
            const contenido = $('#review-text').val();
            
            if (rating === 0) {
                alert('Por favor selecciona una calificación');
                return;
            }
            
            if (!contenido.trim()) {
                alert('Por favor escribe tu reseña');
                return;
            }
            
            if (typeof cnmxData !== 'undefined') {
                $.ajax({
                    url: cnmxData.apiUrl + '/resena',
                    method: 'POST',
                    headers: {
                        'X-WP-Nonce': cnmxData.nonce
                    },
                    data: {
                        negocio_id: negocioId,
                        calificacion: rating,
                        contenido: contenido
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', '¡Gracias!', 'Tu reseña ha sido enviada');
                            $('#review-text').val('');
                            $('.rating-input .star-btn svg').attr('fill', 'none');
                            $form.slideUp();
                        }
                    }
                });
            }
        });
    }
    
    // Search functionality
    function initSearch() {
        const $searchInput = $('#cnmx-buscar-negocios');
        
        if ($searchInput.length) {
            $searchInput.on('input', debounce(function() {
                const query = $(this).val().toLowerCase();
                
                $('.cnmx-negocio-card').each(function() {
                    const title = $(this).data('title') || '';
                    if (title.toLowerCase().includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }, 300));
        }
    }
    
    // Toast notification
    function showToast(type, title, message) {
        const $container = $('.toast-container');
        if (!$container.length) {
            $('body').append('<div class="toast-container"></div>');
        }
        
        const $toast = $(`
            <div class="toast ${type}">
                <div class="toast-icon">${type === 'success' ? '✓' : '!'}</div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
            </div>
        `);
        
        $('.toast-container').append($toast);
        
        setTimeout(function() {
            $toast.addClass('out');
            setTimeout(function() {
                $toast.remove();
            }, 400);
        }, 3000);
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
