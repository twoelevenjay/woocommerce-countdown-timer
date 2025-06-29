<?php
/**
 * Plugin Name: WooCommerce Countdown Timer
 * Plugin URI: https://github.com/twoelevenjay/woocommerce-countdown-timer
 * Description: A sane same-day shipping countdown timer for WooCommerce products that encourages customers to order before cutoff time.
 * Version: 1.0.0
 * Author: Leon @ 211J
 * Author URI: http://211j.com/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: woo-countdown-timer
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * WC requires at least: 6.0
 * WC tested up to: 9.9
 * Network: false
 *
 * @package WooCountdownTimer
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return;
}

// Define plugin constants
define( 'WOO_COUNTDOWN_TIMER_VERSION', '1.0.0' );
define( 'WOO_COUNTDOWN_TIMER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WOO_COUNTDOWN_TIMER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Main plugin class
class WooCountdownTimer {
    
    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
    }
    
    public function init() {
        // Load plugin files
        $this->load_includes();
        
        // Initialize components
        add_action( 'init', array( $this, 'init_components' ) );
        
        // Add admin menu
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        
        // Enqueue scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }
    
    private function load_includes() {
        require_once WOO_COUNTDOWN_TIMER_PLUGIN_DIR . 'includes/class-settings.php';
        require_once WOO_COUNTDOWN_TIMER_PLUGIN_DIR . 'includes/class-countdown-display.php';
        require_once WOO_COUNTDOWN_TIMER_PLUGIN_DIR . 'includes/class-admin.php';
    }
    
    public function init_components() {
        new WooCountdownTimer_Settings();
        new WooCountdownTimer_Display();
        new WooCountdownTimer_Admin();
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'Countdown Timer', 'woo-countdown-timer' ),
            __( 'Countdown Timer', 'woo-countdown-timer' ),
            'manage_woocommerce',
            'woo-countdown-timer',
            array( $this, 'admin_page' )
        );
    }
    
    public function admin_page() {
        include WOO_COUNTDOWN_TIMER_PLUGIN_DIR . 'admin/admin-page.php';
    }
    
    public function enqueue_frontend_assets() {
        wp_enqueue_style( 
            'woo-countdown-timer-style',
            WOO_COUNTDOWN_TIMER_PLUGIN_URL . 'assets/css/countdown-timer.css',
            array(),
            WOO_COUNTDOWN_TIMER_VERSION
        );
        
        wp_enqueue_script(
            'woo-countdown-timer-script',
            WOO_COUNTDOWN_TIMER_PLUGIN_URL . 'assets/js/countdown-timer.js',
            array( 'jquery' ),
            WOO_COUNTDOWN_TIMER_VERSION,
            true
        );
        
        wp_localize_script( 'woo-countdown-timer-script', 'wooCountdownTimer', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'woo_countdown_timer_nonce' )
        ) );
    }
    
    public function enqueue_admin_assets( $hook ) {
        if ( 'woocommerce_page_woo-countdown-timer' !== $hook ) {
            return;
        }
        
        wp_enqueue_style(
            'woo-countdown-timer-admin-style',
            WOO_COUNTDOWN_TIMER_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            WOO_COUNTDOWN_TIMER_VERSION
        );
        
        wp_enqueue_script(
            'woo-countdown-timer-admin-script',
            WOO_COUNTDOWN_TIMER_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            WOO_COUNTDOWN_TIMER_VERSION,
            true
        );
    }
    
    public function activate() {
        // Set default options
        $default_options = array(
            'cutoff_time' => '14:00',
            'enabled' => true,
            'message_template' => 'Order within {time} for same-day shipping!',
            'expired_message' => 'Order today for next business day shipping.',
            'weekend_shipping' => false,
            'exclude_holidays' => true
        );
        
        add_option( 'woo_countdown_timer_options', $default_options );
        
        // Create database table if needed
        $this->create_table();
    }
    
    public function deactivate() {
        // Clean up if needed
    }
    
    private function create_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'woo_countdown_timer_logs';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            cutoff_time time NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}

// Initialize the plugin
new WooCountdownTimer();