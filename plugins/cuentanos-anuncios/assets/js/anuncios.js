/**
 * Cuentanos Anuncios Slider - JavaScript
 */

(function($) {
    'use strict';
    
    class CNMXAnunciosSlider {
        constructor(container) {
            this.container = container;
            this.slides = container.find('.cnmx-anuncio-slide');
            this.currentIndex = 0;
            this.count = this.slides.length;
            this.interval = cnmxAnuncios.interval || 4000;
            this.autoplayTimer = null;
            
            this.init();
        }
        
        init() {
            if (this.count <= 1) return;
            
            this.startAutoplay();
        }
        
        goTo(index) {
            this.slides.removeClass('active');
            this.slides.eq(index).addClass('active');
            this.currentIndex = index;
        }
        
        next() {
            const index = (this.currentIndex + 1) % this.count;
            this.goTo(index);
        }
        
        startAutoplay() {
            this.stopAutoplay();
            this.autoplayTimer = setInterval(() => this.next(), this.interval);
        }
        
        stopAutoplay() {
            if (this.autoplayTimer) {
                clearInterval(this.autoplayTimer);
                this.autoplayTimer = null;
            }
        }
    }
    
    $(document).ready(function() {
        $('.cnmx-anuncios-slider').each(function() {
            new CNMXAnunciosSlider($(this));
        });
    });
    
})(jQuery);
