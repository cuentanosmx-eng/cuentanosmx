/**
 * Cuentanos Hero Carousel - JavaScript minimalista
 */

(function($) {
    'use strict';
    
    class CNMXHeroCarousel {
        constructor(element) {
            this.container = element;
            this.slides = element.find('.cnmx-hero-slide');
            this.progressBars = element.find('.cnmx-hero-progress-segment');
            this.currentIndex = 0;
            this.totalSlides = this.slides.length;
            this.autoplayDelay = 6000;
            this.isPaused = false;
            this.autoplayTimeout = null;
            this.animationFrame = null;
            
            this.init();
        }
        
        init() {
            if (this.totalSlides <= 1) {
                return;
            }
            
            this.resetAllProgressBars();
            this.startAutoplay();
            this.bindEvents();
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
        
        resetAllProgressBars() {
            this.progressBars.find('.cnmx-hero-progress-bar').css({
                'width': '0%',
                'animation': 'none'
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
            
            this.resetAllProgressBars();
            this.startProgressForCurrent();
            this.resetAutoplay();
        }
        
        startProgressForCurrent() {
            const segment = this.progressBars.eq(this.currentIndex);
            const progressBar = segment.find('.cnmx-hero-progress-bar');
            
            setTimeout(() => {
                progressBar.css({
                    'width': '0%',
                    'transition': `width ${this.autoplayDelay}ms linear`
                });
                setTimeout(() => {
                    progressBar.css('width', '100%');
                }, 50);
            }, 100);
        }
        
        startAutoplay() {
            this.slides.eq(0).addClass('active');
            this.startProgressForCurrent();
            this.scheduleNext();
        }
        
        scheduleNext() {
            if (this.autoplayTimeout) {
                clearTimeout(this.autoplayTimeout);
            }
            this.autoplayTimeout = setTimeout(() => this.next(), this.autoplayDelay);
        }
        
        resetAutoplay() {
            this.scheduleNext();
        }
        
        next() {
            this.goTo(this.currentIndex + 1);
        }
        
        pause() {
            if (this.isPaused) return;
            this.isPaused = true;
            
            if (this.autoplayTimeout) {
                clearTimeout(this.autoplayTimeout);
            }
            
            const segment = this.progressBars.eq(this.currentIndex);
            const progressBar = segment.find('.cnmx-hero-progress-bar');
            const currentWidth = progressBar.width();
            
            progressBar.css('transition', 'none');
            const storedWidth = progressBar.data('width') || currentWidth;
            progressBar.css('width', storedWidth + 'px');
        }
        
        resume() {
            if (!this.isPaused) return;
            this.isPaused = false;
            
            const segment = this.progressBars.eq(this.currentIndex);
            const progressBar = segment.find('.cnmx-hero-progress-bar');
            const currentWidth = progressBar.width();
            const containerWidth = segment.width();
            const percentComplete = (currentWidth / containerWidth) * 100;
            const remainingTime = ((100 - percentComplete) / 100) * this.autoplayDelay;
            
            progressBar.data('width', currentWidth);
            progressBar.css('transition', `width ${remainingTime}ms linear`);
            
            setTimeout(() => {
                progressBar.css('width', '100%');
            }, 50);
            
            this.scheduleNext();
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
