<?php
/**
 * Admin functionality
 *
 * @package Countdown_Timer_For_WooCommerce
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Countdown_Timer_For_WooCommerce_Admin {
    
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
        
        add_filter( 'woocommerce_get_sections_products', array( $this, 'add_products_section' ) );
        add_filter( 'woocommerce_get_settings_products', array( $this, 'add_products_settings' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }
    
    
    public function enqueue_admin_assets( $hook ) {
        if ( 'woocommerce_page_wc-settings' !== $hook ) {
            return;
        }
        
        // Only load on products tab, countdown timer section
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for conditional asset loading
        if ( ! isset( $_GET['tab'] ) || sanitize_text_field( wp_unslash( $_GET['tab'] ) ) !== 'products' ) {
            return;
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for conditional asset loading
        if ( ! isset( $_GET['section'] ) || sanitize_text_field( wp_unslash( $_GET['section'] ) ) !== 'countdown_timer' ) {
            return;
        }
        
        // Only enqueue files that exist
        $admin_css_path = COUNTDOWN_TIMER_FOR_WOOCOMMERCE_PLUGIN_PATH . 'assets/css/admin.css';
        if ( file_exists( $admin_css_path ) ) {
            wp_enqueue_style(
                'countdown-timer-for-woocommerce-admin-style',
                COUNTDOWN_TIMER_FOR_WOOCOMMERCE_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                COUNTDOWN_TIMER_FOR_WOOCOMMERCE_VERSION
            );
        }
        
        $admin_js_path = COUNTDOWN_TIMER_FOR_WOOCOMMERCE_PLUGIN_PATH . 'assets/js/admin.js';
        if ( file_exists( $admin_js_path ) ) {
            wp_enqueue_script(
                'countdown-timer-for-woocommerce-admin-script',
                COUNTDOWN_TIMER_FOR_WOOCOMMERCE_PLUGIN_URL . 'assets/js/admin.js',
                array( 'jquery' ),
                COUNTDOWN_TIMER_FOR_WOOCOMMERCE_VERSION,
                true
            );
        }
    }
    
    
    public function add_products_section( $sections ) {
        $sections['countdown_timer'] = __( 'Countdown Timer', 'countdown-timer-for-woocommerce' );
        return $sections;
    }
    
    public function add_products_settings( $settings, $current_section ) {
        if ( 'countdown_timer' === $current_section ) {
            $countdown_settings = array(
                array(
                    'title' => __( 'Countdown Timer Settings', 'countdown-timer-for-woocommerce' ),
                    'type'  => 'title',
                    'desc'  => __( 'Configure countdown timer display for same-day shipping deadlines.', 'countdown-timer-for-woocommerce' ),
                    'id'    => 'countdown_timer_options'
                ),
                array(
                    'title'    => __( 'Daily Cutoff Time', 'countdown-timer-for-woocommerce' ),
                    'desc'     => __( 'Set the daily cutoff time for same-day shipping (24-hour format).', 'countdown-timer-for-woocommerce' ),
                    'id'       => 'countdown_timer_cutoff_time',
                    'type'     => 'time',
                    'default'  => '14:00',
                    'css'      => 'width: 120px;',
                    'custom_attributes' => array(
                        'step' => '60'
                    )
                ),
                array(
                    'title'    => __( 'Countdown Message', 'countdown-timer-for-woocommerce' ),
                    'desc'     => __( 'Use {time} as a placeholder for the countdown timer. Example: "Order within {time} for same-day shipping!"', 'countdown-timer-for-woocommerce' ),
                    'id'       => 'countdown_timer_message_template',
                    'type'     => 'textarea',
                    'default'  => 'Order within {time} for same-day shipping!',
                    'css'      => 'width: 400px; height: 75px;'
                ),
                array(
                    'title'   => __( 'Weekend Shipping', 'countdown-timer-for-woocommerce' ),
                    'desc'    => __( 'Enable countdown timer on weekends', 'countdown-timer-for-woocommerce' ),
                    'id'      => 'countdown_timer_enable_weekends',
                    'default' => 'no',
                    'type'    => 'checkbox',
                    'desc_tip' => __( 'If disabled, the countdown timer will not appear on Saturdays and Sundays.', 'countdown-timer-for-woocommerce' )
                ),
                array(
                    'title'    => __( 'Urgency Threshold', 'countdown-timer-for-woocommerce' ),
                    'desc'     => __( 'When time remaining is below this threshold, the countdown will appear with urgency styling (in minutes).', 'countdown-timer-for-woocommerce' ),
                    'id'       => 'countdown_timer_urgency_threshold',
                    'type'     => 'number',
                    'default'  => '60',
                    'css'      => 'width: 80px;',
                    'custom_attributes' => array(
                        'min'  => '5',
                        'max'  => '1440',
                        'step' => '1'
                    )
                ),
                array(
                    'title'    => __( 'Very Urgent Threshold', 'countdown-timer-for-woocommerce' ),
                    'desc'     => __( 'When time remaining is below this threshold, the countdown will appear with high urgency styling (in minutes).', 'countdown-timer-for-woocommerce' ),
                    'id'       => 'countdown_timer_very_urgent_threshold',
                    'type'     => 'number',
                    'default'  => '30',
                    'css'      => 'width: 80px;',
                    'custom_attributes' => array(
                        'min'  => '1',
                        'max'  => '1439',
                        'step' => '1'
                    )
                ),
                array(
                    'type' => 'sectionend',
                    'id'   => 'countdown_timer_options'
                )
            );
            return $countdown_settings;
        }
        return $settings;
    }
}