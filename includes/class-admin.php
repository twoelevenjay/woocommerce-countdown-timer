<?php
/**
 * Admin functionality
 *
 * @package WooCountdownTimer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WooCountdownTimer_Admin {
    
    public function __construct() {
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
        add_action( 'admin_init', array( $this, 'handle_settings_save' ) );
        
        // Add product-level settings
        add_action( 'woocommerce_product_options_shipping', array( $this, 'add_product_shipping_options' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_shipping_options' ) );
    }
    
    public function admin_notices() {
        if ( isset( $_GET['settings-updated'] ) && $_GET['page'] === 'woo-countdown-timer' ) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>' . __( 'Countdown timer settings saved successfully!', 'woo-countdown-timer' ) . '</p>';
            echo '</div>';
        }
    }
    
    public function handle_settings_save() {
        if ( isset( $_POST['submit'] ) && isset( $_POST['woo_countdown_timer_options'] ) ) {
            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                return;
            }
            
            if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'woo_countdown_timer_settings-options' ) ) {
                return;
            }
            
            $options = $_POST['woo_countdown_timer_options'];
            
            // Sanitize options
            $sanitized_options = array();
            $sanitized_options['enabled'] = isset( $options['enabled'] ) ? 1 : 0;
            $sanitized_options['cutoff_time'] = sanitize_text_field( $options['cutoff_time'] );
            $sanitized_options['message_template'] = wp_kses_post( $options['message_template'] );
            $sanitized_options['expired_message'] = wp_kses_post( $options['expired_message'] );
            $sanitized_options['weekend_shipping'] = isset( $options['weekend_shipping'] ) ? 1 : 0;
            
            // Validate cutoff time format
            if ( ! preg_match( '/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $sanitized_options['cutoff_time'] ) ) {
                add_settings_error( 'woo_countdown_timer_options', 'invalid_time', __( 'Invalid time format. Please use HH:MM format.', 'woo-countdown-timer' ) );
                return;
            }
            
            update_option( 'woo_countdown_timer_options', $sanitized_options );
            
            wp_redirect( add_query_arg( array( 'settings-updated' => 'true' ), wp_get_referer() ) );
            exit;
        }
    }
    
    public function add_product_shipping_options() {
        global $post;
        
        echo '<div class="options_group">';
        
        woocommerce_wp_checkbox( array(
            'id' => '_disable_countdown_timer',
            'label' => __( 'Disable Countdown Timer', 'woo-countdown-timer' ),
            'description' => __( 'Hide the countdown timer for this product', 'woo-countdown-timer' )
        ) );
        
        woocommerce_wp_text_input( array(
            'id' => '_custom_cutoff_time',
            'label' => __( 'Custom Cutoff Time', 'woo-countdown-timer' ),
            'description' => __( 'Override the global cutoff time for this product (HH:MM format)', 'woo-countdown-timer' ),
            'type' => 'time',
            'desc_tip' => true
        ) );
        
        woocommerce_wp_textarea_input( array(
            'id' => '_custom_countdown_message',
            'label' => __( 'Custom Countdown Message', 'woo-countdown-timer' ),
            'description' => __( 'Override the global countdown message for this product. Use {time} as placeholder.', 'woo-countdown-timer' ),
            'desc_tip' => true
        ) );
        
        echo '</div>';
    }
    
    public function save_product_shipping_options( $post_id ) {
        $disable_countdown = isset( $_POST['_disable_countdown_timer'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_disable_countdown_timer', $disable_countdown );
        
        if ( isset( $_POST['_custom_cutoff_time'] ) ) {
            $custom_cutoff = sanitize_text_field( $_POST['_custom_cutoff_time'] );
            if ( preg_match( '/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $custom_cutoff ) ) {
                update_post_meta( $post_id, '_custom_cutoff_time', $custom_cutoff );
            }
        }
        
        if ( isset( $_POST['_custom_countdown_message'] ) ) {
            $custom_message = wp_kses_post( $_POST['_custom_countdown_message'] );
            update_post_meta( $post_id, '_custom_countdown_message', $custom_message );
        }
    }
    
    public function get_dashboard_stats() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'woo_countdown_timer_logs';
        
        // Get stats for the last 30 days
        $thirty_days_ago = date( 'Y-m-d H:i:s', strtotime( '-30 days' ) );
        
        $stats = array();
        
        // Total countdown views
        $stats['total_views'] = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE created_at >= %s",
            $thirty_days_ago
        ) );
        
        // Most popular cutoff times
        $stats['popular_times'] = $wpdb->get_results( $wpdb->prepare(
            "SELECT cutoff_time, COUNT(*) as count FROM $table_name WHERE created_at >= %s GROUP BY cutoff_time ORDER BY count DESC LIMIT 5",
            $thirty_days_ago
        ) );
        
        return $stats;
    }
    
    public static function log_countdown_view( $product_id, $cutoff_time ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'woo_countdown_timer_logs';
        
        $wpdb->insert(
            $table_name,
            array(
                'product_id' => intval( $product_id ),
                'cutoff_time' => sanitize_text_field( $cutoff_time ),
                'created_at' => current_time( 'mysql' )
            ),
            array( '%d', '%s', '%s' )
        );
    }
}