/**
 * WooCommerce Countdown Timer JavaScript
 */

(function($) {
    'use strict';

    class WooCountdownTimer {
        constructor() {
            this.timers = [];
            this.init();
        }

        init() {
            this.findTimers();
            this.startTimers();
            this.bindEvents();
        }

        findTimers() {
            $('.countdown-active').each((index, element) => {
                const $timer = $(element);
                const cutoffTimestamp = parseInt($timer.data('cutoff'));
                
                if (cutoffTimestamp && cutoffTimestamp > 0) {
                    this.timers.push({
                        element: $timer,
                        cutoffTimestamp: cutoffTimestamp,
                        $hours: $timer.find('.countdown-hours'),
                        $minutes: $timer.find('.countdown-minutes'),
                        $seconds: $timer.find('.countdown-seconds'),
                        $display: $timer.find('.countdown-display')
                    });
                }
            });
        }

        startTimers() {
            if (this.timers.length === 0) return;

            // Update immediately
            this.updateTimers();

            // Update every second
            this.intervalId = setInterval(() => {
                this.updateTimers();
            }, 1000);
        }

        updateTimers() {
            const currentTime = Math.floor(Date.now() / 1000);
            let activeTimers = 0;

            this.timers.forEach(timer => {
                const remainingSeconds = timer.cutoffTimestamp - currentTime;

                if (remainingSeconds <= 0) {
                    this.expireTimer(timer);
                } else {
                    this.updateTimerDisplay(timer, remainingSeconds);
                    activeTimers++;
                }
            });

            // Stop interval if no active timers
            if (activeTimers === 0 && this.intervalId) {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
        }

        updateTimerDisplay(timer, remainingSeconds) {
            const time = this.formatTime(remainingSeconds);
            
            timer.$hours.text(time.hours);
            timer.$minutes.text(time.minutes);
            timer.$seconds.text(time.seconds);

            // Add urgency classes
            timer.$display.removeClass('urgent very-urgent');
            if (remainingSeconds <= 300) { // 5 minutes
                timer.$display.addClass('very-urgent');
            } else if (remainingSeconds <= 1800) { // 30 minutes
                timer.$display.addClass('urgent');
            }

            // Update aria-live for accessibility
            timer.$display.attr('aria-live', 'polite');
            timer.$display.attr('aria-label', `${time.hours} hours, ${time.minutes} minutes, ${time.seconds} seconds remaining`);
        }

        formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            return {
                hours: this.padZero(hours),
                minutes: this.padZero(minutes),
                seconds: this.padZero(secs)
            };
        }

        padZero(num) {
            return num.toString().padStart(2, '0');
        }

        expireTimer(timer) {
            // Replace with expired message
            const expiredMessage = wooCountdownTimer.expiredMessage || 'Order today for next business day shipping.';
            const $parent = timer.element.parent();
            
            $parent.html(`
                <div class="countdown-expired">
                    <span class="countdown-message">${expiredMessage}</span>
                </div>
            `);

            // Add expired class to parent container
            $parent.addClass('countdown-timer-expired');

            // Log expiration event
            this.logEvent('timer_expired', {
                cutoff_timestamp: timer.cutoffTimestamp
            });
        }

        bindEvents() {
            // Handle page visibility changes
            $(document).on('visibilitychange', () => {
                if (document.hidden) {
                    // Page is hidden, stop timers to save resources
                    if (this.intervalId) {
                        clearInterval(this.intervalId);
                        this.intervalId = null;
                    }
                } else {
                    // Page is visible again, restart timers
                    this.startTimers();
                }
            });

            // Handle window focus/blur for better performance
            $(window).on('focus', () => {
                if (!this.intervalId && this.timers.length > 0) {
                    this.startTimers();
                }
            });

            $(window).on('blur', () => {
                // Reduce update frequency when window is not focused
                if (this.intervalId) {
                    clearInterval(this.intervalId);
                    this.intervalId = setInterval(() => {
                        this.updateTimers();
                    }, 5000); // Update every 5 seconds instead of 1
                }
            });
        }

        logEvent(eventType, data = {}) {
            if (!wooCountdownTimer || !wooCountdownTimer.ajaxurl) return;

            $.ajax({
                url: wooCountdownTimer.ajaxurl,
                type: 'POST',
                data: {
                    action: 'log_countdown_event',
                    event_type: eventType,
                    event_data: JSON.stringify(data),
                    nonce: wooCountdownTimer.nonce
                },
                success: function(response) {
                    // Optional: handle success
                },
                error: function(xhr, status, error) {
                    // Optional: handle error
                }
            });
        }

        // Public method to refresh timers (useful for AJAX loaded content)
        refresh() {
            this.timers = [];
            if (this.intervalId) {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
            this.init();
        }

        // Public method to manually expire all timers
        expireAll() {
            this.timers.forEach(timer => {
                this.expireTimer(timer);
            });
            this.timers = [];
            if (this.intervalId) {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        window.wooCountdownTimerInstance = new WooCountdownTimer();

        // Re-initialize for dynamically loaded content
        $(document.body).on('updated_wc_div', function() {
            if (window.wooCountdownTimerInstance) {
                window.wooCountdownTimerInstance.refresh();
            }
        });

        // Handle AJAX cart updates
        $(document.body).on('wc_fragments_refreshed', function() {
            if (window.wooCountdownTimerInstance) {
                window.wooCountdownTimerInstance.refresh();
            }
        });
    });

    // Utility functions for external use
    window.WooCountdownTimerUtils = {
        formatTimeRemaining: function(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            const parts = [];
            
            if (hours > 0) {
                parts.push(hours + (hours === 1 ? ' hour' : ' hours'));
            }
            if (minutes > 0) {
                parts.push(minutes + (minutes === 1 ? ' minute' : ' minutes'));
            }
            if (secs > 0 || parts.length === 0) {
                parts.push(secs + (secs === 1 ? ' second' : ' seconds'));
            }

            return parts.join(', ');
        },

        isBusinessHours: function(timestamp) {
            const date = new Date(timestamp * 1000);
            const day = date.getDay(); // 0 = Sunday, 6 = Saturday
            const hour = date.getHours();

            // Monday to Friday, 9 AM to 6 PM
            return (day >= 1 && day <= 5) && (hour >= 9 && hour < 18);
        },

        getNextBusinessDay: function(timestamp) {
            const date = new Date(timestamp * 1000);
            const day = date.getDay();
            
            let daysToAdd = 1;
            
            if (day === 5) { // Friday
                daysToAdd = 3; // Skip to Monday
            } else if (day === 6) { // Saturday
                daysToAdd = 2; // Skip to Monday
            }
            
            date.setDate(date.getDate() + daysToAdd);
            return Math.floor(date.getTime() / 1000);
        }
    };

})(jQuery);

// CSS for dynamic urgency classes
const urgencyStyles = `
    .countdown-display.urgent {
        animation: pulse-urgent 1.5s infinite;
        background: rgba(255, 193, 7, 0.3) !important;
        border-color: rgba(255, 193, 7, 0.5) !important;
    }
    
    .countdown-display.very-urgent {
        animation: pulse-very-urgent 1s infinite;
        background: rgba(220, 53, 69, 0.3) !important;
        border-color: rgba(220, 53, 69, 0.5) !important;
    }
    
    @keyframes pulse-urgent {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    @keyframes pulse-very-urgent {
        0%, 100% { transform: scale(1); }
        25% { transform: scale(1.1); }
        75% { transform: scale(1.05); }
    }
    
    .countdown-timer-expired {
        opacity: 0.8;
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 0.8; }
    }
`;

// Inject urgency styles
const styleSheet = document.createElement('style');
styleSheet.textContent = urgencyStyles;
document.head.appendChild(styleSheet);