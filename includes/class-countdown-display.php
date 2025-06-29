<?php
/**
 * Countdown display functionality
 *
 * @package WooCountdownTimer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WooCountdownTimer_Display {
    
    private $settings;
    
    public function __construct() {
        $this->settings = new WooCountdownTimer_Settings();
        
        // Hook into WooCommerce product display
        add_action( 'woocommerce_single_product_summary', array( $this, 'display_countdown_timer' ), 25 );
        add_action( 'woocommerce_after_shop_loop_item', array( $this, 'display_countdown_timer_shop' ), 15 );
        
        // AJAX handler for countdown updates
        add_action( 'wp_ajax_get_countdown_time', array( $this, 'ajax_get_countdown_time' ) );
        add_action( 'wp_ajax_nopriv_get_countdown_time', array( $this, 'ajax_get_countdown_time' ) );
    }
    
    public function display_countdown_timer() {
        if ( ! $this->settings->get_option( 'enabled', true ) ) {
            return;
        }
        
        global $product;
        
        if ( ! $product || ! $product->is_in_stock() ) {
            return;
        }
        
        $countdown_data = $this->get_countdown_data();
        
        if ( ! $countdown_data ) {
            return;
        }
        
        $this->render_countdown_html( $countdown_data );
    }
    
    public function display_countdown_timer_shop() {
        if ( ! $this->settings->get_option( 'enabled', true ) ) {
            return;
        }
        
        global $product;
        
        if ( ! $product || ! $product->is_in_stock() ) {
            return;
        }
        
        $countdown_data = $this->get_countdown_data();
        
        if ( ! $countdown_data ) {
            return;
        }
        
        echo '<div class="woo-countdown-timer-shop">';
        $this->render_countdown_html( $countdown_data, true );
        echo '</div>';
    }
    
    private function get_countdown_data() {
        $cutoff_time = $this->settings->get_option( 'cutoff_time', '14:00' );
        $weekend_shipping = $this->settings->get_option( 'weekend_shipping', false );
        
        $current_time = current_time( 'timestamp' );
        $current_date = date( 'Y-m-d', $current_time );
        $current_day = date( 'N', $current_time ); // 1 = Monday, 7 = Sunday
        
        // Check if today is weekend and weekend shipping is disabled
        if ( ! $weekend_shipping && ( $current_day == 6 || $current_day == 7 ) ) {
            return false;
        }
        
        // Calculate cutoff timestamp for today
        $cutoff_timestamp = strtotime( $current_date . ' ' . $cutoff_time );
        
        // If current time is past cutoff, no countdown
        if ( $current_time >= $cutoff_timestamp ) {
            return array(
                'expired' => true,
                'message' => $this->settings->get_option( 'expired_message', 'Order today for next business day shipping.' )
            );
        }
        
        // Calculate remaining time
        $remaining_seconds = $cutoff_timestamp - $current_time;
        
        return array(
            'expired' => false,
            'remaining_seconds' => $remaining_seconds,
            'cutoff_timestamp' => $cutoff_timestamp,
            'message_template' => $this->settings->get_option( 'message_template', 'Order within {time} for same-day shipping!' )
        );
    }
    
    private function render_countdown_html( $countdown_data, $compact = false ) {
        $class = $compact ? 'woo-countdown-timer-compact' : 'woo-countdown-timer';
        
        echo '<div class="' . esc_attr( $class ) . '">';
        
        if ( $countdown_data['expired'] ) {
            echo '<div class="countdown-expired">';
            echo '<span class="countdown-message">' . esc_html( $countdown_data['message'] ) . '</span>';
            echo '</div>';
        } else {
            echo '<div class="countdown-active" data-cutoff="' . esc_attr( $countdown_data['cutoff_timestamp'] ) . '">';
            
            $time_placeholder = '<span class="countdown-display">
                <span class="countdown-hours">00</span>:<span class="countdown-minutes">00</span>:<span class="countdown-seconds">00</span>
            </span>';
            
            $message = str_replace( '{time}', $time_placeholder, $countdown_data['message_template'] );
            echo '<span class="countdown-message">' . wp_kses_post( $message ) . '</span>';
            
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    public function ajax_get_countdown_time() {
        check_ajax_referer( 'woo_countdown_timer_nonce', 'nonce' );
        
        $countdown_data = $this->get_countdown_data();
        
        if ( ! $countdown_data ) {
            wp_die();
        }
        
        wp_send_json_success( $countdown_data );
    }
    
    public function format_time_remaining( $seconds ) {
        $hours = floor( $seconds / 3600 );
        $minutes = floor( ( $seconds % 3600 ) / 60 );
        $seconds = $seconds % 60;
        
        return array(
            'hours' => sprintf( '%02d', $hours ),
            'minutes' => sprintf( '%02d', $minutes ),
            'seconds' => sprintf( '%02d', $seconds )
        );
    }
    
    public static function is_business_day( $timestamp = null ) {
        if ( $timestamp === null ) {
            $timestamp = current_time( 'timestamp' );
        }
        
        $day_of_week = date( 'N', $timestamp );
        return $day_of_week >= 1 && $day_of_week <= 5; // Monday to Friday
    }
}