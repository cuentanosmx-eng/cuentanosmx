/**
 * Cuentanos Hero Carousel - JavaScript
 */

(function($) {
    'use strict';
    
    class CNMXHeroCarousel {
        constructor(element) {
            this.container = element;
            this.slides = element.find('.cnmx-hero-slide');
            this.prevBtn = element.find('.cnmx-hero-prev');
            this.nextBtn = element.find('.cnmx-hero-next');
            
            this.currentIndex = 0;
            this.totalSlides = this.slides.length;
            this.autoplayDelay = 6000;
            this.progressInterval = null;
            this.isPaused = false;
            
            this.init();
        }
        
        init() {
            if (this.totalSlides <= 1) {
                this.prevBtn.hide();
                this.nextBtn.hide();
                return;
            }
            
            this.bindEvents();
            this.startAutoplay();
        }
        
        bindEvents() {
            this.prevBtn.on('click', () => this.prev());
            this.nextBtn.on('click', () => this.next());
            
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
            
            this.currentIndex = index;
            
            if (this.currentIndex >= this.totalSlides) {
                this.currentIndex = 0;
            } else if (this.currentIndex < 0) {
                this.currentIndex = this.totalSlides - 1;
            }
            
            this.slides.eq(this.currentIndex).addClass('active');
            this.startProgress();
        }
        
        next() {
            this.goTo(this.currentIndex + 1);
        }
        
        prev() {
            this.goTo(this.currentIndex - 1);
        }
        
        startProgress() {
            const slide = this.slides.eq(this.currentIndex);
            const progressBar = slide.find('.cnmx-hero-progress-bar');
            
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
            }
            
            slide.find('.cnmx-hero-progress-bar').css('animation', 'none');
            slide.find('.cnmx-hero-progress-bar').offset();
            slide.find('.cnmx-hero-progress-bar').css('animation', `progressSlide ${this.autoplayDelay}ms linear forwards`);
            
            if (this.autoplayTimeout) {
                clearTimeout(this.autoplayTimeout);
            }
            
            this.autoplayTimeout = setTimeout(() => {
                if (!this.isPaused) {
                    this.next();
                }
            }, this.autoplayDelay);
        }
        
        startAutoplay() {
            this.startProgress();
        }
        
        pause() {
            this.isPaused = true;
            
            const slide = this.slides.eq(this.currentIndex);
            const progressBar = slide.find('.cnmx-hero-progress-bar');
            const currentWidth = progressBar.width() / slide.width() * 100;
            
            progressBar.css({
                'animation-play-state': 'paused',
                'width': currentWidth + '%'
            });
            
            if (this.autoplayTimeout) {
                clearTimeout(this.autoplayTimeout);
            }
        }
        
        resume() {
            this.isPaused = false;
            
            const slide = this.slides.eq(this.currentIndex);
            const progressBar = slide.find('.cnmx-hero-progress-bar');
            
            progressBar.css('animation-play-state', 'running');
            
            const currentWidth = progressBar.width() / slide.width() * 100;
            const remainingTime = (100 - currentWidth) / 100 * this.autoplayDelay;
            
            this.autoplayTimeout = setTimeout(() => {
                this.next();
            }, remainingTime);
        }
        
        destroy() {
            if (this.autoplayTimeout) {
                clearTimeout(this.autoplayTimeout);
            }
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
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
