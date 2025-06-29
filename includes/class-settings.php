<?php
/**
 * Settings management class
 *
 * @package WooCountdownTimer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WooCountdownTimer_Settings {
    
    private $options;
    
    public function __construct() {
        $this->options = get_option( 'woo_countdown_timer_options', array() );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }
    
    public function register_settings() {
        register_setting( 'woo_countdown_timer_settings', 'woo_countdown_timer_options' );
        
        add_settings_section(
            'woo_countdown_timer_general',
            __( 'General Settings', 'woo-countdown-timer' ),
            array( $this, 'general_section_callback' ),
            'woo_countdown_timer_settings'
        );
        
        add_settings_field(
            'enabled',
            __( 'Enable Countdown Timer', 'woo-countdown-timer' ),
            array( $this, 'enabled_callback' ),
            'woo_countdown_timer_settings',
            'woo_countdown_timer_general'
        );
        
        add_settings_field(
            'cutoff_time',
            __( 'Same-Day Shipping Cutoff Time', 'woo-countdown-timer' ),
            array( $this, 'cutoff_time_callback' ),
            'woo_countdown_timer_settings',
            'woo_countdown_timer_general'
        );
        
        add_settings_field(
            'message_template',
            __( 'Countdown Message Template', 'woo-countdown-timer' ),
            array( $this, 'message_template_callback' ),
            'woo_countdown_timer_settings',
            'woo_countdown_timer_general'
        );
        
        add_settings_field(
            'expired_message',
            __( 'Message After Cutoff', 'woo-countdown-timer' ),
            array( $this, 'expired_message_callback' ),
            'woo_countdown_timer_settings',
            'woo_countdown_timer_general'
        );
        
        add_settings_field(
            'weekend_shipping',
            __( 'Weekend Shipping Available', 'woo-countdown-timer' ),
            array( $this, 'weekend_shipping_callback' ),
            'woo_countdown_timer_settings',
            'woo_countdown_timer_general'
        );
    }
    
    public function general_section_callback() {
        echo '<p>' . __( 'Configure the countdown timer settings for same-day shipping.', 'woo-countdown-timer' ) . '</p>';
    }
    
    public function enabled_callback() {
        $enabled = isset( $this->options['enabled'] ) ? $this->options['enabled'] : true;
        echo '<input type="checkbox" id="enabled" name="woo_countdown_timer_options[enabled]" value="1"' . checked( 1, $enabled, false ) . ' />';
        echo '<label for="enabled">' . __( 'Enable the countdown timer display', 'woo-countdown-timer' ) . '</label>';
    }
    
    public function cutoff_time_callback() {
        $cutoff_time = isset( $this->options['cutoff_time'] ) ? $this->options['cutoff_time'] : '14:00';
        echo '<input type="time" id="cutoff_time" name="woo_countdown_timer_options[cutoff_time]" value="' . esc_attr( $cutoff_time ) . '" />';
        echo '<p class="description">' . __( 'Orders placed before this time will qualify for same-day shipping.', 'woo-countdown-timer' ) . '</p>';
    }
    
    public function message_template_callback() {
        $message = isset( $this->options['message_template'] ) ? $this->options['message_template'] : 'Order within {time} for same-day shipping!';
        echo '<input type="text" id="message_template" name="woo_countdown_timer_options[message_template]" value="' . esc_attr( $message ) . '" class="regular-text" />';
        echo '<p class="description">' . __( 'Use {time} as placeholder for the countdown timer. HTML allowed.', 'woo-countdown-timer' ) . '</p>';
    }
    
    public function expired_message_callback() {
        $message = isset( $this->options['expired_message'] ) ? $this->options['expired_message'] : 'Order today for next business day shipping.';
        echo '<input type="text" id="expired_message" name="woo_countdown_timer_options[expired_message]" value="' . esc_attr( $message ) . '" class="regular-text" />';
        echo '<p class="description">' . __( 'Message displayed after the cutoff time has passed.', 'woo-countdown-timer' ) . '</p>';
    }
    
    public function weekend_shipping_callback() {
        $weekend_shipping = isset( $this->options['weekend_shipping'] ) ? $this->options['weekend_shipping'] : false;
        echo '<input type="checkbox" id="weekend_shipping" name="woo_countdown_timer_options[weekend_shipping]" value="1"' . checked( 1, $weekend_shipping, false ) . ' />';
        echo '<label for="weekend_shipping">' . __( 'Enable same-day shipping on weekends', 'woo-countdown-timer' ) . '</label>';
    }
    
    public function get_option( $key, $default = '' ) {
        return isset( $this->options[ $key ] ) ? $this->options[ $key ] : $default;
    }
    
    public function get_all_options() {
        return $this->options;
    }
}