/**
 * Cuentanos Hero Carousel - JavaScript
 */

(function($) {
    'use strict';
    
    class CNMXHeroCarousel {
        constructor(element) {
            this.container = element;
            this.slides = element.find('.cnmx-hero-slide');
            this.dots = element.find('.cnmx-hero-dot');
            this.prevBtn = element.find('.cnmx-hero-prev');
            this.nextBtn = element.find('.cnmx-hero-next');
            
            this.currentIndex = 0;
            this.totalSlides = this.slides.length;
            this.autoplayInterval = null;
            this.autoplayDelay = 6000;
            this.isPaused = false;
            
            this.init();
        }
        
        init() {
            if (this.totalSlides <= 1) {
                this.prevBtn.hide();
                this.nextBtn.hide();
                this.dots.hide();
                return;
            }
            
            this.bindEvents();
            this.startAutoplay();
        }
        
        bindEvents() {
            this.prevBtn.on('click', () => this.prev());
            this.nextBtn.on('click', () => this.next());
            
            this.dots.on('click', (e) => {
                const index = $(e.currentTarget).data('slide');
                this.goTo(index);
            });
            
            this.container.on('mouseenter', () => this.pause());
            this.container.on('mouseleave', () => this.resume());
            
            this.container.on('touchstart', () => this.pause());
            this.container.on('touchend', () => {
                setTimeout(() => this.resume(), 3000);
            });
            
            $(document).on('visibilitychange', () => {
                if (document.hidden) {
                    this.pause();
                } else {
                    this.resume();
                }
            });
        }
        
        goTo(index) {
            if (index === this.currentIndex) return;
            
            this.slides.eq(this.currentIndex).removeClass('active');
            this.dots.eq(this.currentIndex).removeClass('active');
            
            this.currentIndex = index;
            
            if (this.currentIndex >= this.totalSlides) {
                this.currentIndex = 0;
            } else if (this.currentIndex < 0) {
                this.currentIndex = this.totalSlides - 1;
            }
            
            this.slides.eq(this.currentIndex).addClass('active');
            this.dots.eq(this.currentIndex).addClass('active');
            
            this.resetAutoplay();
        }
        
        next() {
            this.goTo(this.currentIndex + 1);
        }
        
        prev() {
            this.goTo(this.currentIndex - 1);
        }
        
        startAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
            }
            
            this.autoplayInterval = setInterval(() => {
                if (!this.isPaused) {
                    this.next();
                }
            }, this.autoplayDelay);
        }
        
        pause() {
            this.isPaused = true;
        }
        
        resume() {
            this.isPaused = false;
        }
        
        resetAutoplay() {
            this.startAutoplay();
        }
        
        destroy() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
            }
            this.container.off();
        }
    }
    
    $(document).ready(function() {
        $('.cnmx-hero-carousel').each(function() {
            new CNMXHeroCarousel($(this));
        });
    });
    
})(jQuery);
