<?php
/**
 * Uninstall script for Countdown Timer for WooCommerce
 *
 * @package Countdown_Timer_For_WooCommerce
 */

// If uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete plugin options (legacy format)
delete_option( 'countdown_timer_for_woocommerce_settings' );

// Delete WooCommerce format options
delete_option( 'countdown_timer_cutoff_time' );
delete_option( 'countdown_timer_message_template' );
delete_option( 'countdown_timer_enable_weekends' );
delete_option( 'countdown_timer_urgency_threshold' );
delete_option( 'countdown_timer_very_urgent_threshold' );

// For multisite installations
if ( is_multisite() ) {
    $blog_ids = get_sites( array( 'fields' => 'ids' ) );
    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );
        delete_option( 'countdown_timer_for_woocommerce_settings' );
        delete_option( 'countdown_timer_cutoff_time' );
        delete_option( 'countdown_timer_message_template' );
        delete_option( 'countdown_timer_enable_weekends' );
        delete_option( 'countdown_timer_urgency_threshold' );
        delete_option( 'countdown_timer_very_urgent_threshold' );
        restore_current_blog();
    }
}

// Clear any cached data
wp_cache_flush();