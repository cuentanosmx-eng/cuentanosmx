/**
 * Cuentanos Hero Carousel - JavaScript minimalista
 */

(function($) {
    'use strict';
    
    class CNMXHeroCarousel {
        constructor(element) {
            this.container = element;
            this.slides = element.find('.cnmx-hero-slide');
            this.currentIndex = 0;
            this.totalSlides = this.slides.length;
            this.autoplayDelay = 6000;
            this.isPaused = false;
            this.autoplayTimeout = null;
            
            this.init();
        }
        
        init() {
            if (this.totalSlides <= 1) {
                return;
            }
            
            this.bindEvents();
            this.startAutoplay();
        }
        
        bindEvents() {
            this.container.on('click', '.cnmx-hero-progress-segment', (e) => {
                const index = $(e.currentTarget).data('index');
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
            this.container.find('.cnmx-hero-progress-segment').eq(this.currentIndex).removeClass('active');
            this.container.find('.cnmx-hero-progress-segment').eq(this.currentIndex).find('.cnmx-hero-progress-bar').css('animation', 'none');
            
            this.currentIndex = index;
            
            if (this.currentIndex >= this.totalSlides) {
                this.currentIndex = 0;
            } else if (this.currentIndex < 0) {
                this.currentIndex = this.totalSlides - 1;
            }
            
            this.slides.eq(this.currentIndex).addClass('active');
            
            const segment = this.container.find('.cnmx-hero-progress-segment').eq(this.currentIndex);
            segment.addClass('active');
            segment.find('.cnmx-hero-progress-bar').css('animation', 'none');
            segment.find('.cnmx-hero-progress-bar').offset();
            segment.find('.cnmx-hero-progress-bar').css('animation', `segmentProgress ${this.autoplayDelay}ms linear forwards`);
            
            this.resetAutoplay();
        }
        
        startAutoplay() {
            const segment = this.container.find('.cnmx-hero-progress-segment').eq(this.currentIndex);
            segment.addClass('active');
            segment.find('.cnmx-hero-progress-bar').css('animation', `segmentProgress ${this.autoplayDelay}ms linear forwards`);
            
            this.autoplayTimeout = setTimeout(() => this.next(), this.autoplayDelay);
        }
        
        next() {
            this.goTo(this.currentIndex + 1);
        }
        
        resetAutoplay() {
            if (this.autoplayTimeout) {
                clearTimeout(this.autoplayTimeout);
            }
            this.autoplayTimeout = setTimeout(() => this.next(), this.autoplayDelay);
        }
        
        pause() {
            if (this.isPaused) return;
            this.isPaused = true;
            
            const segment = this.container.find('.cnmx-hero-progress-segment').eq(this.currentIndex);
            const progressBar = segment.find('.cnmx-hero-progress-bar');
            const currentWidth = (progressBar.width() / segment.width()) * 100;
            
            progressBar.css({
                'animation-play-state': 'paused',
                'width': currentWidth + '%'
            });
            
            if (this.autoplayTimeout) {
                clearTimeout(this.autoplayTimeout);
            }
        }
        
        resume() {
            if (!this.isPaused) return;
            this.isPaused = false;
            
            const segment = this.container.find('.cnmx-hero-progress-segment').eq(this.currentIndex);
            const progressBar = segment.find('.cnmx-hero-progress-bar');
            
            progressBar.css('animation-play-state', 'running');
            
            const currentWidth = (progressBar.width() / segment.width()) * 100;
            const remainingTime = ((100 - currentWidth) / 100) * this.autoplayDelay;
            
            this.autoplayTimeout = setTimeout(() => this.next(), remainingTime);
        }
        
        destroy() {
            if (this.autoplayTimeout) {
                clearTimeout(this.autoplayTimeout);
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
