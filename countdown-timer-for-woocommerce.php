<?php
/**
 * Plugin Name: Countdown Timer for WooCommerce
 * Plugin URI: https://211j.com/countdown-timer-for-woocommerce/
 * Description: A professional countdown timer for WooCommerce that shows sentence-format messages like "Order within 02:34:15 for same-day shipping!"
 * Version: 1.0.0
 * Author: Countdown Timer Team
 * Author URI: https://211j.com/about
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: countdown-timer-for-woocommerce
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 9.9.5
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'COUNTDOWN_TIMER_FOR_WOOCOMMERCE_VERSION', '1.0.0' );
define( 'COUNTDOWN_TIMER_FOR_WOOCOMMERCE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'COUNTDOWN_TIMER_FOR_WOOCOMMERCE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Check if WooCommerce is active
if ( ! function_exists( 'is_plugin_active' ) ) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
    add_action( 'admin_notices', 'countdown_timer_for_woocommerce_missing_wc_notice' );
    return;
}

/**
 * Admin notice for missing WooCommerce
 */
function countdown_timer_for_woocommerce_missing_wc_notice() {
    $message = sprintf(
        /* translators: 1: Plugin name 2: WooCommerce */
        esc_html__( '%1$s requires %2$s to be installed and active.', 'countdown-timer-for-woocommerce' ),
        '<strong>Countdown Timer for WooCommerce</strong>',
        '<strong>WooCommerce</strong>'
    );
    
    printf( '<div class="notice notice-error"><p>%s</p></div>', wp_kses_post( $message ) );
}

// Main plugin class
class Countdown_Timer_For_WooCommerce {
    
    private static $instance = null;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }
    
    public function init() {
        // Load plugin classes
        $this->load_classes();
        
        // Initialize plugin functionality
        $this->init_hooks();
    }
    
    private function load_classes() {
        require_once COUNTDOWN_TIMER_FOR_WOOCOMMERCE_PLUGIN_PATH . 'includes/class-settings.php';
        require_once COUNTDOWN_TIMER_FOR_WOOCOMMERCE_PLUGIN_PATH . 'includes/class-countdown-display.php';
        require_once COUNTDOWN_TIMER_FOR_WOOCOMMERCE_PLUGIN_PATH . 'includes/class-admin.php';
    }
    
    private function init_hooks() {
        // Initialize classes
        Countdown_Timer_For_WooCommerce_Settings::instance();
        Countdown_Timer_For_WooCommerce_Display::instance();
        Countdown_Timer_For_WooCommerce_Admin::instance();
    }
    
    public static function activate() {
        // Set default settings on activation
        $default_settings = array(
            'cutoff_time' => '14:00',
            'message_template' => 'Order within {time} for same-day shipping!',
            'enable_weekends' => false,
            'urgency_threshold' => 60,
            'very_urgent_threshold' => 30
        );
        
        if ( ! get_option( 'countdown_timer_for_woocommerce_settings' ) ) {
            update_option( 'countdown_timer_for_woocommerce_settings', $default_settings );
        }
    }
    
    public static function deactivate() {
        // Clean up if needed
    }
}

// Initialize plugin
function countdown_timer_for_woocommerce() {
    return Countdown_Timer_For_WooCommerce::instance();
}

// Start the plugin
countdown_timer_for_woocommerce();

// Activation/Deactivation hooks
register_activation_hook( __FILE__, array( 'Countdown_Timer_For_WooCommerce', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Countdown_Timer_For_WooCommerce', 'deactivate' ) );