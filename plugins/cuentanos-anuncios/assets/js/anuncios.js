/**
 * Cuentanos Anuncios Slider - JavaScript
 */

(function($) {
    'use strict';
    
    class CNMXAnunciosSlider {
        constructor(container) {
            this.container = container;
            this.slides = container.find('.cnmx-anuncio-slide');
            this.dots = container.find('.cnmx-anuncio-dot');
            this.prevBtn = container.find('.cnmx-anuncio-arrow.prev');
            this.nextBtn = container.find('.cnmx-anuncio-arrow.next');
            this.currentIndex = 0;
            this.count = this.slides.length;
            this.autoplay = true;
            this.interval = 5000;
            this.autoplayTimer = null;
            
            this.init();
        }
        
        init() {
            if (this.count <= 1) return;
            
            this.bindEvents();
            this.startAutoplay();
        }
        
        bindEvents() {
            this.prevBtn.on('click', () => this.prev());
            this.nextBtn.on('click', () => this.next());
            
            this.dots.on('click', (e) => {
                const index = $(e.currentTarget).data('index');
                this.goTo(index);
            });
            
            this.container.on('mouseenter', () => this.stopAutoplay());
            this.container.on('mouseleave', () => this.startAutoplay());
            
            this.slides.each((_, slide) => {
                const link = $(slide).find('.cnmx-anuncio-link');
                if (link.length) {
                    link.on('click', () => this.stopAutoplay());
                }
            });
        }
        
        goTo(index) {
            this.slides.removeClass('active');
            this.dots.removeClass('active');
            
            this.slides.eq(index).addClass('active');
            this.dots.eq(index).addClass('active');
            
            this.currentIndex = index;
        }
        
        prev() {
            const index = this.currentIndex === 0 ? this.count - 1 : this.currentIndex - 1;
            this.goTo(index);
        }
        
        next() {
            const index = this.currentIndex === this.count - 1 ? 0 : this.currentIndex + 1;
            this.goTo(index);
        }
        
        startAutoplay() {
            if (!this.autoplay) return;
            
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
