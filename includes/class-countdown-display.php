<?php
/**
 * Countdown display functionality
 *
 * @package Countdown_Timer_For_WooCommerce
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Countdown_Timer_For_WooCommerce_Display {
    
    private static $instance = null;
    private $settings;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        $this->settings = Countdown_Timer_For_WooCommerce_Settings::instance();
        
        // Hook into WooCommerce
        add_action( 'woocommerce_single_product_summary', array( $this, 'display_countdown' ), 25 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_countdown_timer_for_woocommerce_get_time', array( $this, 'ajax_get_time' ) );
        add_action( 'wp_ajax_nopriv_countdown_timer_for_woocommerce_get_time', array( $this, 'ajax_get_time' ) );
    }
    
    public function enqueue_assets() {
        if ( ! is_product() ) {
            return;
        }
        
        wp_enqueue_style(
            'countdown-timer-for-woocommerce-style',
            COUNTDOWN_TIMER_FOR_WOOCOMMERCE_PLUGIN_URL . 'assets/css/countdown-timer.css',
            array(),
            COUNTDOWN_TIMER_FOR_WOOCOMMERCE_VERSION
        );
        
        wp_enqueue_script(
            'countdown-timer-for-woocommerce-script',
            COUNTDOWN_TIMER_FOR_WOOCOMMERCE_PLUGIN_URL . 'assets/js/countdown-timer.js',
            array( 'jquery' ),
            COUNTDOWN_TIMER_FOR_WOOCOMMERCE_VERSION,
            true
        );
        
        // Get current time remaining for JavaScript
        $initial_time_remaining = $this->get_time_remaining();
        
        // Localize script
        wp_localize_script( 'countdown-timer-for-woocommerce-script', 'countdownTimerForWooCommerce', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'countdown_timer_for_woocommerce_nonce' ),
            'urgency_threshold' => $this->settings->get_urgency_threshold(),
            'very_urgent_threshold' => $this->settings->get_very_urgent_threshold(),
            'initial_time_remaining' => $initial_time_remaining,
            'page_load_time' => time() * 1000 // JavaScript timestamp for when page loaded
        ) );
    }
    
    public function display_countdown() {
        // Allow other plugins to disable countdown display
        if ( ! apply_filters( 'countdown_timer_for_woo_display_countdown', true ) ) {
            return;
        }
        
        if ( ! $this->should_display_countdown() ) {
            return;
        }
        
        $time_remaining = $this->get_time_remaining();
        
        if ( $time_remaining <= 0 ) {
            return;
        }
        
        $message = $this->get_countdown_message( $time_remaining );
        $urgency_class = $this->get_urgency_class( $time_remaining );
        
        // Allow customization of countdown HTML
        $countdown_html = apply_filters( 'countdown_timer_for_woo_countdown_html', '', $time_remaining, $message, $urgency_class );
        
        if ( ! empty( $countdown_html ) ) {
            echo $countdown_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            return;
        }
        
        ?>
        <div class="countdown-timer-for-woocommerce <?php echo esc_attr( $urgency_class ); ?>" 
             data-cutoff-time="<?php echo esc_attr( $this->settings->get_cutoff_time() ); ?>"
             data-enable-weekends="<?php echo esc_attr( $this->settings->is_weekends_enabled() ? '1' : '0' ); ?>"
             aria-live="polite">
            <div class="countdown-active">
                <span class="countdown-message"><?php echo wp_kses_post( $message ); ?></span>
            </div>
        </div>
        <?php
    }
    
    private function should_display_countdown() {
        // Check if weekends are disabled and today is weekend
        if ( ! $this->settings->is_weekends_enabled() && $this->is_weekend() ) {
            return false;
        }
        
        return true;
    }
    
    private function is_weekend() {
        $day_of_week = current_time( 'N' );
        return $day_of_week >= 6; // Saturday (6) or Sunday (7)
    }
    
    private function get_time_remaining() {
        $cutoff_time = $this->settings->get_cutoff_time();
        
        // Get current time in WordPress timezone
        $now = new DateTime( 'now', wp_timezone() );
        
        // Parse cutoff time (e.g., "20:00")
        $cutoff_parts = explode( ':', $cutoff_time );
        $cutoff_hour = (int) $cutoff_parts[0];
        $cutoff_minute = isset( $cutoff_parts[1] ) ? (int) $cutoff_parts[1] : 0;
        
        // Create cutoff DateTime for today
        $cutoff_today = new DateTime( 'now', wp_timezone() );
        $cutoff_today->setTime( $cutoff_hour, $cutoff_minute, 0 );
        
        // If cutoff has passed today, return 0
        if ( $now >= $cutoff_today ) {
            return 0;
        }
        
        // Calculate time remaining in seconds
        $time_remaining = $cutoff_today->getTimestamp() - $now->getTimestamp();
        
        // Apply filter for extensibility
        return apply_filters( 'countdown_timer_for_woo_time_remaining', $time_remaining, $cutoff_today->getTimestamp(), $now->getTimestamp() );
    }
    
    private function get_today_cutoff_timestamp() {
        $cutoff_time = $this->settings->get_cutoff_time();
        $cutoff_parts = explode( ':', $cutoff_time );
        $cutoff_hour = (int) $cutoff_parts[0];
        $cutoff_minute = isset( $cutoff_parts[1] ) ? (int) $cutoff_parts[1] : 0;
        
        $cutoff_today = new DateTime( 'now', wp_timezone() );
        $cutoff_today->setTime( $cutoff_hour, $cutoff_minute, 0 );
        
        return $cutoff_today->getTimestamp();
    }
    
    private function get_next_business_day_cutoff( $cutoff_time ) {
        $tomorrow = strtotime( '+1 day', strtotime( current_time( 'Y-m-d' ) ) );
        
        // If weekends are disabled, skip to Monday if tomorrow is weekend
        if ( ! $this->settings->is_weekends_enabled() ) {
            $day_of_week = gmdate( 'N', $tomorrow );
            if ( $day_of_week >= 6 ) { // Weekend
                $days_to_add = ( $day_of_week == 6 ) ? 2 : 1; // Saturday: +2, Sunday: +1
                $tomorrow = strtotime( '+' . $days_to_add . ' days', $tomorrow );
            }
        }
        
        return strtotime( gmdate( 'Y-m-d', $tomorrow ) . ' ' . $cutoff_time );
    }
    
    private function get_countdown_message( $time_remaining ) {
        $message_template = $this->settings->get_message_template();
        $formatted_time = $this->format_time_remaining( $time_remaining );
        
        return str_replace( '{time}', '<span class="countdown-time">' . $formatted_time . '</span>', $message_template );
    }
    
    private function format_time_remaining( $seconds ) {
        $hours = floor( $seconds / 3600 );
        $minutes = floor( ( $seconds % 3600 ) / 60 );
        $seconds = $seconds % 60;
        
        return sprintf( '%02d:%02d:%02d', $hours, $minutes, $seconds );
    }
    
    private function get_urgency_class( $time_remaining ) {
        $urgency_threshold = $this->settings->get_urgency_threshold() * 60; // Convert to seconds
        $very_urgent_threshold = $this->settings->get_very_urgent_threshold() * 60;
        
        if ( $time_remaining <= $very_urgent_threshold ) {
            return 'very-urgent';
        } elseif ( $time_remaining <= $urgency_threshold ) {
            return 'urgent';
        }
        
        return '';
    }
    
    public function ajax_get_time() {
        check_ajax_referer( 'countdown_timer_for_woocommerce_nonce', 'nonce' );
        
        $time_remaining = $this->get_time_remaining();
        
        if ( $time_remaining <= 0 ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Cutoff time has passed', 'countdown-timer-for-woocommerce' ) ) );
        }
        
        $response = array(
            'time_remaining' => $time_remaining,
            'formatted_time' => $this->format_time_remaining( $time_remaining ),
            'urgency_class' => $this->get_urgency_class( $time_remaining ),
            'message' => $this->get_countdown_message( $time_remaining )
        );
        
        wp_send_json_success( $response );
    }
}