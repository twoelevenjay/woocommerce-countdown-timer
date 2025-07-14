<?php
/**
 * Settings management class
 *
 * @package Countdown_Timer_For_WooCommerce
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Countdown_Timer_For_WooCommerce_Settings {
    
    private static $instance = null;
    private $settings = array();
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        $this->load_settings();
    }
    
    private function load_settings() {
        $defaults = array(
            'cutoff_time' => '14:00',
            'message_template' => 'Order within {time} for same-day shipping!',
            'enable_weekends' => false,
            'urgency_threshold' => 60,
            'very_urgent_threshold' => 30
        );
        
        // Load from WooCommerce settings format (primary)
        $wc_settings = array(
            'cutoff_time' => get_option( 'countdown_timer_cutoff_time', '14:00' ),
            'message_template' => get_option( 'countdown_timer_message_template', 'Order within {time} for same-day shipping!' ),
            'enable_weekends' => get_option( 'countdown_timer_enable_weekends', 'no' ) === 'yes',
            'urgency_threshold' => absint( get_option( 'countdown_timer_urgency_threshold', 60 ) ),
            'very_urgent_threshold' => absint( get_option( 'countdown_timer_very_urgent_threshold', 30 ) )
        );
        
        // Check for legacy settings as fallback
        $legacy_settings = get_option( 'countdown_timer_for_woocommerce_settings', array() );
        
        // Use WooCommerce settings if they exist, otherwise use legacy, otherwise use defaults
        if ( ! empty( $wc_settings['cutoff_time'] ) && $wc_settings['cutoff_time'] !== '14:00' ) {
            // WooCommerce settings exist and have been customized
            $this->settings = wp_parse_args( $wc_settings, $defaults );
        } elseif ( ! empty( $legacy_settings ) ) {
            // Fall back to legacy settings
            $this->settings = wp_parse_args( $legacy_settings, $defaults );
        } else {
            // Use defaults with WooCommerce format
            $this->settings = wp_parse_args( $wc_settings, $defaults );
        }
    }
    
    
    public function sanitize_settings( $input ) {
        $sanitized = array();
        
        // Validate cutoff time format (HH:MM)
        if ( isset( $input['cutoff_time'] ) ) {
            $time = sanitize_text_field( $input['cutoff_time'] );
            if ( preg_match( '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time ) ) {
                $sanitized['cutoff_time'] = $time;
            } else {
                $sanitized['cutoff_time'] = '14:00'; // Default fallback
            }
        }
        
        // Validate message template contains {time} placeholder
        if ( isset( $input['message_template'] ) ) {
            $message = sanitize_textarea_field( $input['message_template'] );
            if ( ! empty( $message ) && strpos( $message, '{time}' ) !== false ) {
                $sanitized['message_template'] = $message;
            } else {
                $sanitized['message_template'] = 'Order within {time} for same-day shipping!';
            }
        }
        
        if ( isset( $input['enable_weekends'] ) ) {
            $sanitized['enable_weekends'] = (bool) $input['enable_weekends'];
        }
        
        // Validate urgency thresholds with constraints
        $urgency_threshold = isset( $input['urgency_threshold'] ) ? absint( $input['urgency_threshold'] ) : 60;
        $very_urgent_threshold = isset( $input['very_urgent_threshold'] ) ? absint( $input['very_urgent_threshold'] ) : 30;
        
        // Ensure thresholds are within reasonable bounds
        $urgency_threshold = max( 5, min( 1440, $urgency_threshold ) ); // 5 minutes to 24 hours
        $very_urgent_threshold = max( 1, min( $urgency_threshold - 1, $very_urgent_threshold ) ); // Must be less than urgency threshold
        
        $sanitized['urgency_threshold'] = $urgency_threshold;
        $sanitized['very_urgent_threshold'] = $very_urgent_threshold;
        
        return $sanitized;
    }
    
    public function get_setting( $key, $default = null ) {
        return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : $default;
    }
    
    public function get_all_settings() {
        return $this->settings;
    }
    
    public function update_setting( $key, $value ) {
        $this->settings[ $key ] = $value;
        update_option( 'countdown_timer_for_woocommerce_settings', $this->settings );
    }
    
    public function get_cutoff_time() {
        return $this->get_setting( 'cutoff_time', '14:00' );
    }
    
    public function get_message_template() {
        return $this->get_setting( 'message_template', 'Order within {time} for same-day shipping!' );
    }
    
    public function is_weekends_enabled() {
        return $this->get_setting( 'enable_weekends', false );
    }
    
    public function get_urgency_threshold() {
        return $this->get_setting( 'urgency_threshold', 60 );
    }
    
    public function get_very_urgent_threshold() {
        return $this->get_setting( 'very_urgent_threshold', 30 );
    }
}