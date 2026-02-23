=== Countdown Timer for WooCommerce ===
Contributors: twoelevenjay
Tags: woocommerce, countdown, timer, shipping, urgency
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A countdown timer for WooCommerce that shows sentence-format messages like "Order within 02:34:15 for same-day shipping!"

== Description ==

Countdown Timer for WooCommerce adds a dynamic countdown timer to your WooCommerce product pages that encourages customers to place orders before your same-day shipping cutoff time.

The timer displays in a natural sentence format — "Order within 02:34:15 for same-day shipping!" — making it easy for customers to understand exactly how long they have left.

**Features:**

* Sentence-format display with customizable message template
* Configurable daily cutoff time for same-day shipping
* Smart display logic — only shows when applicable (before cutoff, in-stock products)
* Weekend shipping control
* Visual urgency states as the deadline approaches
* Fully responsive design
* Accessibility ready with ARIA labels
* Per-product override support via the WooCommerce product Shipping tab

== Installation ==

1. Upload the `countdown-timer-for-woocommerce` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **WooCommerce > Settings > Products > Countdown Timer** to configure the plugin.
4. Set your cutoff time and customize your message.

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =

Yes. WooCommerce must be installed and active. The plugin will display an admin notice if WooCommerce is not detected.

= Can I set different cutoff times per product? =

Yes. You can override the global cutoff time on individual products in the Product Data > Shipping tab.

= What happens after the cutoff time? =

The countdown timer hides automatically after the cutoff time passes. You can also configure a custom message to display instead.

= Does the timer work on weekends? =

By default, the timer does not display on weekends. You can enable weekend display in the settings.

== Screenshots ==

1. Countdown timer displayed on a WooCommerce product page.

== Changelog ==

= 1.0.0 =
* Initial release.
* Core countdown timer functionality with sentence-format display.
* Admin settings via WooCommerce Settings API.
* Per-product cutoff time overrides.
* Responsive design and accessibility features.
