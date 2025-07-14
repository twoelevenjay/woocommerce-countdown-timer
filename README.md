# Countdown Timer for WooCommerce

A professional countdown timer for WooCommerce that displays sentence-format messages like "Order within 02:34:15 for same-day shipping!" to encourage customers to order before cutoff time.

## Description

Countdown Timer for WooCommerce adds a dynamic countdown timer to your WooCommerce product pages that encourages customers to place orders before your same-day shipping cutoff time. This plugin helps increase urgency and can boost conversion rates by clearly communicating shipping deadlines in an easy-to-understand sentence format.

## Features

* **Sentence-Format Display** - Shows time in natural language: "Order within 02:34:15 for same-day shipping!"
* **Configurable Cutoff Time** - Set your daily same-day shipping cutoff time
* **Smart Display Logic** - Only shows when applicable (before cutoff, in stock products)
* **Weekend Shipping Control** - Option to enable/disable weekend shipping
* **Custom Messages** - Fully customizable countdown message with {time} placeholder
* **Visual Urgency States** - Changes appearance as deadline approaches (1 hour, 30 minutes, 15 minutes)
* **Responsive Design** - Works perfectly on all devices
* **Accessibility Ready** - ARIA labels and keyboard navigation support
* **Performance Optimized** - Efficient JavaScript with real-time updates
* **WordPress.org Compliant** - Follows all WordPress coding standards and security best practices

## Installation

1. Upload the plugin files to `/wp-content/plugins/countdown-timer-for-woocommerce/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to WooCommerce > Countdown Timer to configure the plugin
4. Set your cutoff time and customize your messages

## Requirements

* WordPress 5.0 or higher
* WooCommerce 5.0 or higher
* PHP 7.4 or higher

## Directory Structure

```
countdown-timer-for-woocommerce/
├── countdown-timer-for-woocommerce.php  # Main plugin file
├── includes/                            # Core plugin classes
│   ├── class-settings.php               # Settings management
│   ├── class-countdown-display.php      # Frontend display functionality
│   └── class-admin.php                  # Admin interface
├── admin/
│   └── admin-page.php                   # Admin settings page template
├── assets/
│   ├── css/
│   │   └── countdown-timer.css          # Frontend styles
│   ├── js/
│   │   └── countdown-timer.js           # Frontend JavaScript
│   └── [images/screenshots]             # Plugin assets
├── languages/                           # Translation files
├── readme.txt                           # WordPress.org readme
└── uninstall.php                        # Cleanup on uninstall
```

## Configuration

After activation, navigate to **WooCommerce > Countdown Timer** to configure:

1. **Daily Cutoff Time** - Set the time when same-day shipping ends (e.g., 3:00 PM)
2. **Message Template** - Customize the countdown message (use {time} for the countdown)
3. **Weekend Shipping** - Enable/disable countdown display on weekends
4. **Urgency Thresholds** - Configure when visual urgency indicators appear

## Development

This plugin follows WordPress and WooCommerce coding standards and is ready for WordPress.org submission.

### Coding Standards

* WordPress coding standards (WPCS)
* 4-space indentation
* Proper sanitization and escaping
* Nonce verification for forms
* Capability checks for admin actions

### Security Features

* Direct file access prevention
* Input sanitization
* Output escaping
* CSRF protection with nonces
* Proper capability checks

## Internationalization

The plugin is fully translation-ready with the text domain `countdown-timer-for-woocommerce`. All user-facing strings are translatable.

## License

This plugin is licensed under the GNU General Public License v2 or later.

## Support

For support and feature requests, please visit the [WordPress.org support forum](https://wordpress.org/support/plugin/countdown-timer-for-woocommerce/) or the [GitHub repository](https://github.com/yourusername/countdown-timer-for-woocommerce).

## Changelog

### 1.0.0
* Initial release
* Core countdown timer functionality
* Admin settings page
* Responsive design
* Accessibility features
* WordPress.org compliance