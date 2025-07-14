/**
 * Countdown Timer for WooCommerce - Frontend JavaScript
 */

(function($) {
    'use strict';
    
    class WooCommerceCountdownTimer {
        constructor() {
            this.timer = null;
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.startCountdown();
        }
        
        bindEvents() {
            // Restart countdown when page becomes visible (handles tab switching)
            $(document).on('visibilitychange', () => {
                if (!document.hidden) {
                    this.startCountdown();
                }
            });
            
            // Handle AJAX cart updates
            $(document.body).on('updated_wc_div', () => {
                this.startCountdown();
            });
        }
        
        startCountdown() {
            const $timer = $('.countdown-timer-for-woocommerce');
            
            if ($timer.length === 0) {
                return;
            }
            
            // Clear any existing timer
            if (this.timer) {
                clearInterval(this.timer);
            }
            
            const cutoffTime = $timer.data('cutoff-time');
            const enableWeekends = $timer.data('enable-weekends') === '1';
            
            this.timer = setInterval(() => {
                this.updateCountdown($timer, cutoffTime, enableWeekends);
            }, 1000);
            
            // Initial update
            this.updateCountdown($timer, cutoffTime, enableWeekends);
        }
        
        updateCountdown($timer, cutoffTime, enableWeekends) {
            const now = Date.now();
            const pageLoadTime = countdownTimerForWooCommerce.page_load_time || now;
            const initialTimeRemaining = countdownTimerForWooCommerce.initial_time_remaining || 0;
            
            // Calculate how much time has passed since page load
            const elapsedSeconds = Math.floor((now - pageLoadTime) / 1000);
            
            // Calculate current time remaining
            const timeRemaining = Math.max(0, initialTimeRemaining - elapsedSeconds);
            
            if (timeRemaining <= 0) {
                this.hideCountdown($timer);
                return;
            }
            
            this.updateDisplay($timer, timeRemaining);
        }
        
        calculateTimeRemaining(now, cutoffTime, enableWeekends) {
            const [hours, minutes] = cutoffTime.split(':').map(Number);
            
            // Create cutoff time for today
            let cutoffDate = new Date(now);
            cutoffDate.setHours(hours, minutes, 0, 0);
            
            // If cutoff has passed today, return 0 (no same-day shipping)
            if (now >= cutoffDate) {
                return 0;
            }
            
            // If weekends are disabled and today is weekend, return 0
            if (!enableWeekends && this.isWeekend(now)) {
                return 0;
            }
            
            return Math.floor((cutoffDate - now) / 1000);
        }
        
        getNextBusinessDay(date, enableWeekends) {
            const nextDay = new Date(date);
            nextDay.setDate(nextDay.getDate() + 1);
            
            if (!enableWeekends) {
                // Skip weekends
                while (this.isWeekend(nextDay)) {
                    nextDay.setDate(nextDay.getDate() + 1);
                }
            }
            
            return nextDay;
        }
        
        isWeekend(date) {
            const day = date.getDay();
            return day === 0 || day === 6; // Sunday or Saturday
        }
        
        updateDisplay($timer, timeRemaining) {
            const formattedTime = this.formatTime(timeRemaining);
            const $timeElement = $timer.find('.countdown-time');
            
            // Update time display
            $timeElement.text(formattedTime);
            
            // Update urgency classes
            this.updateUrgencyClasses($timer, timeRemaining);
            
            // Update ARIA live region
            $timer.attr('aria-label', `Time remaining for same-day shipping: ${formattedTime}`);
        }
        
        formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }
        
        updateUrgencyClasses($timer, timeRemaining) {
            const urgencyThreshold = (countdownTimerForWooCommerce.urgency_threshold || 60) * 60; // Convert to seconds
            const veryUrgentThreshold = (countdownTimerForWooCommerce.very_urgent_threshold || 30) * 60;
            
            // Remove existing urgency classes
            $timer.removeClass('urgent very-urgent');
            
            // Add appropriate urgency class
            if (timeRemaining <= veryUrgentThreshold) {
                $timer.addClass('very-urgent');
            } else if (timeRemaining <= urgencyThreshold) {
                $timer.addClass('urgent');
            }
        }
        
        hideCountdown($timer) {
            $timer.fadeOut(500, function() {
                $(this).remove();
            });
            
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        }
        
        // Public method to manually refresh countdown
        refresh() {
            this.startCountdown();
        }
    }
    
    // Initialize countdown timer when DOM is ready
    $(document).ready(function() {
        // Initialize countdown timer
        window.countdownTimerForWooCommerceInstance = new WooCommerceCountdownTimer();
        
        // Handle AJAX requests (for dynamic content updates)
        $(document).ajaxComplete(function(event, xhr, settings) {
            // Reinitialize if new content contains countdown timer
            if ($('.countdown-timer-for-woocommerce').length > 0) {
                setTimeout(() => {
                    window.countdownTimerForWooCommerceInstance.refresh();
                }, 100);
            }
        });
    });
    
    // Expose timer instance globally for debugging
    window.WooCommerceCountdownTimer = WooCommerceCountdownTimer;
    
})(jQuery);