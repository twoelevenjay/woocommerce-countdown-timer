<?php
/**
 * Admin page template
 *
 * @package WooCountdownTimer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$settings = new WooCountdownTimer_Settings();
$admin = new WooCountdownTimer_Admin();
$stats = $admin->get_dashboard_stats();
?>

<div class="wrap">
    <h1><?php _e( 'WooCommerce Countdown Timer', 'woo-countdown-timer' ); ?></h1>
    
    <div class="woo-countdown-admin-container">
        <div class="woo-countdown-admin-main">
            <form method="post" action="">
                <?php wp_nonce_field( 'woo_countdown_timer_settings-options' ); ?>
                <?php settings_fields( 'woo_countdown_timer_settings' ); ?>
                <?php do_settings_sections( 'woo_countdown_timer_settings' ); ?>
                
                <div class="woo-countdown-preview">
                    <h3><?php _e( 'Preview', 'woo-countdown-timer' ); ?></h3>
                    <div class="preview-container">
                        <div class="woo-countdown-timer preview-timer">
                            <div class="countdown-active">
                                <span class="countdown-message">
                                    <?php 
                                    $template = $settings->get_option( 'message_template', 'Order within {time} for same-day shipping!' );
                                    $time_display = '<span class="countdown-display"><span class="countdown-hours">02</span>:<span class="countdown-minutes">34</span>:<span class="countdown-seconds">15</span></span>';
                                    echo wp_kses_post( str_replace( '{time}', $time_display, $template ) );
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <p class="description"><?php _e( 'This is how the countdown timer will appear on your product pages.', 'woo-countdown-timer' ); ?></p>
                </div>
                
                <?php submit_button(); ?>
            </form>
        </div>
        
        <div class="woo-countdown-admin-sidebar">
            <div class="woo-countdown-stats-widget">
                <h3><?php _e( 'Statistics (Last 30 Days)', 'woo-countdown-timer' ); ?></h3>
                
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format( $stats['total_views'] ); ?></div>
                    <div class="stat-label"><?php _e( 'Countdown Views', 'woo-countdown-timer' ); ?></div>
                </div>
                
                <?php if ( ! empty( $stats['popular_times'] ) ) : ?>
                <div class="popular-times">
                    <h4><?php _e( 'Popular Cutoff Times', 'woo-countdown-timer' ); ?></h4>
                    <ul>
                        <?php foreach ( $stats['popular_times'] as $time_stat ) : ?>
                        <li>
                            <span class="time"><?php echo esc_html( $time_stat->cutoff_time ); ?></span>
                            <span class="count">(<?php echo number_format( $time_stat->count ); ?> views)</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="woo-countdown-help-widget">
                <h3><?php _e( 'Quick Help', 'woo-countdown-timer' ); ?></h3>
                
                <div class="help-section">
                    <h4><?php _e( 'Message Templates', 'woo-countdown-timer' ); ?></h4>
                    <p><?php _e( 'Use {time} as a placeholder for the countdown timer in your messages.', 'woo-countdown-timer' ); ?></p>
                    
                    <h4><?php _e( 'Time Format', 'woo-countdown-timer' ); ?></h4>
                    <p><?php _e( 'Use 24-hour format (HH:MM) for cutoff times. Example: 14:00 for 2:00 PM.', 'woo-countdown-timer' ); ?></p>
                    
                    <h4><?php _e( 'Product-Level Settings', 'woo-countdown-timer' ); ?></h4>
                    <p><?php _e( 'You can override these settings for individual products in the Product Data > Shipping tab.', 'woo-countdown-timer' ); ?></p>
                </div>
            </div>
            
            <div class="woo-countdown-info-widget">
                <h3><?php _e( 'Plugin Information', 'woo-countdown-timer' ); ?></h3>
                <p><strong><?php _e( 'Version:', 'woo-countdown-timer' ); ?></strong> <?php echo WOO_COUNTDOWN_TIMER_VERSION; ?></p>
                <p><strong><?php _e( 'Status:', 'woo-countdown-timer' ); ?></strong> 
                    <?php if ( $settings->get_option( 'enabled', true ) ) : ?>
                        <span class="status-active"><?php _e( 'Active', 'woo-countdown-timer' ); ?></span>
                    <?php else : ?>
                        <span class="status-inactive"><?php _e( 'Inactive', 'woo-countdown-timer' ); ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.woo-countdown-admin-container {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.woo-countdown-admin-main {
    flex: 2;
}

.woo-countdown-admin-sidebar {
    flex: 1;
    min-width: 300px;
}

.woo-countdown-preview {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.preview-container {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin: 10px 0;
}

.preview-timer {
    font-size: 16px;
    text-align: center;
}

.countdown-display {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    color: #d63638;
    font-size: 1.2em;
}

.woo-countdown-stats-widget,
.woo-countdown-help-widget,
.woo-countdown-info-widget {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
}

.woo-countdown-stats-widget h3,
.woo-countdown-help-widget h3,
.woo-countdown-info-widget h3 {
    margin-top: 0;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.stat-item {
    text-align: center;
    margin: 20px 0;
}

.stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #0073aa;
}

.stat-label {
    color: #666;
    font-size: 0.9em;
}

.popular-times ul {
    list-style: none;
    padding: 0;
}

.popular-times li {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}

.popular-times li:last-child {
    border-bottom: none;
}

.time {
    font-weight: bold;
}

.count {
    color: #666;
    font-size: 0.9em;
}

.status-active {
    color: #46b450;
    font-weight: bold;
}

.status-inactive {
    color: #d63638;
    font-weight: bold;
}

.help-section h4 {
    margin-top: 15px;
    margin-bottom: 5px;
    color: #333;
}

.help-section p {
    margin-bottom: 10px;
    color: #666;
    font-size: 0.9em;
}
</style>