=== Countdown Timer for WooCommerce ===
Contributors: countdowntimer
Tags: woocommerce, countdown timer, shipping deadline, urgency, same day shipping
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
Requires WooCommerce: 5.0
Tested WooCommerce up to: 9.9.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add urgency to your WooCommerce store with countdown timers that show customers exactly how much time they have left for same-day shipping.

== Description ==

Transform your WooCommerce product pages with dynamic countdown timers that create urgency and drive sales. This plugin displays professional, sentence-format countdown messages like "Order within 02:34:15 for same-day shipping!" directly on your product pages.

**Why Choose This Countdown Timer?**

Unlike generic countdown plugins, this tool is specifically designed for WooCommerce stores to increase conversion rates by creating shipping deadline urgency.

= Key Features =

* **Smart Countdown Logic** - Automatically calculates time remaining until your daily shipping cutoff
* **Sentence-Format Display** - Shows natural messages like "Order within 02:34:15 for same-day shipping!"
* **Business Day Awareness** - Handles weekends and holidays intelligently
* **Visual Urgency States** - Timer appearance changes as deadline approaches
* **Mobile Optimized** - Looks perfect on all devices and screen sizes
* **Developer Friendly** - Clean code, WordPress standards, translation ready
* **Performance First** - Lightweight with minimal database queries
* **Accessibility Compliant** - WCAG 2.1 AA standards with screen reader support

= How It Works =

1. Set your daily cutoff time (e.g., 2:00 PM)
2. Customize the countdown message template
3. Configure weekend shipping preferences
4. The countdown automatically appears on product pages
5. Time remaining updates in real-time

= Use Cases =

* Same-day shipping deadlines
* Next-day delivery cutoffs
* Limited-time promotions
* Stock availability urgency
* Custom shipping schedules

== Installation ==

= Automatic Installation (Recommended) =

1. Go to WordPress Admin → Plugins → Add New
2. Search for "Countdown Timer for WooCommerce"
3. Click "Install Now" and then "Activate"
4. Navigate to WooCommerce → Countdown Timer to configure settings

= Manual Installation =

1. Download the plugin zip file
2. Upload to `/wp-content/plugins/countdown-timer-for-woo/`
3. Activate through the 'Plugins' screen in WordPress
4. Configure via WooCommerce → Countdown Timer

= Quick Setup =

1. Set your daily shipping cutoff time (e.g., 2:00 PM)
2. Customize the countdown message template
3. Choose weekend shipping preferences
4. Save settings - countdowns appear automatically on product pages!

== Frequently Asked Questions ==

= Will this work with my WooCommerce theme? =

Absolutely! The plugin uses standard WooCommerce hooks and integrates seamlessly with any properly coded WooCommerce theme, including Storefront, Astra, GeneratePress, and custom themes.

= How do I customize the countdown message? =

Go to WooCommerce → Countdown Timer and edit the "Countdown Message" field. Use {time} as a placeholder where you want the countdown to appear. For example: "Order within {time} for same-day shipping!"

= What happens when the cutoff time passes? =

The countdown automatically calculates the next business day cutoff. If weekend shipping is disabled, it skips to Monday. If enabled, it shows the next day's cutoff time.

= Can I show different countdowns for different products? =

Currently, the plugin applies one countdown rule to all products. Product-specific countdowns may be added in future versions based on user feedback.

= Does this impact site performance? =

No! The plugin is optimized for performance with minimal database queries, efficient JavaScript, and only loads assets when needed on product pages.

= Is the countdown mobile-friendly? =

Yes, the countdown timer is fully responsive and looks great on all devices, from desktop to mobile phones, with touch-friendly interactions.

= Can I translate the plugin into my language? =

Absolutely! The plugin is translation-ready with full internationalization support. You can translate it using WPML, Polylang, or WordPress's built-in translation tools. Translation files (.po) are included for Chinese, Spanish, Hindi, Arabic, Portuguese, and Russian.

= Will this plugin slow down my site? =

No. The plugin is highly optimized with:
* Only loads assets on product pages where needed
* Minimal database queries (settings cached)
* Lightweight JavaScript (no jQuery dependency)
* Efficient CSS with no render-blocking resources

= Is this plugin compatible with page builders? =

Yes! The plugin works with popular page builders like Elementor, Divi, Beaver Builder, and WPBakery. It uses standard WooCommerce hooks that are respected by all major page builders.

= Can I style the countdown timer to match my theme? =

Yes. The countdown timer uses semantic HTML with CSS classes that can be easily customized. You can override styles in your theme's CSS file or use the WordPress Customizer's Additional CSS feature.

= What happens if JavaScript is disabled? =

The plugin gracefully degrades without JavaScript. The initial countdown time is rendered server-side, so users will still see the message, though it won't update in real-time.

== Screenshots ==

1. Plugin settings page with live preview showing countdown configuration options
2. Countdown timer displayed on WooCommerce product page with real-time updates
3. Mobile responsive countdown display on smartphone screens
4. Urgency state styling when approaching shipping deadline (red/orange indicators)
5. Weekend shipping toggle and business day configuration
6. Customizable message template with {time} placeholder preview

== Changelog ==

= 1.0.0 - 2025-07-12 =
* Initial release
* Feature: Customizable countdown messages with {time} placeholder
* Feature: Daily cutoff time configuration (24-hour format)
* Feature: Weekend shipping toggle for business day calculations
* Feature: Real-time JavaScript countdown updates
* Feature: Responsive design for all devices
* Feature: WCAG 2.1 AA accessibility compliance
* Feature: Professional admin interface with live preview
* Feature: Visual urgency states (normal, urgent, very urgent)
* Feature: Translation ready with full i18n support
* Feature: Compatible with WooCommerce High-Performance Order Storage (HPOS)

== Upgrade Notice ==

= 1.0.0 =
Initial release of Countdown Timer for WooCommerce.

== Developer Notes ==

This plugin follows WordPress coding standards and security best practices:

* All inputs are sanitized and validated
* All outputs are properly escaped
* Nonce verification for all forms
* Capability checks for admin functions
* Translation ready with proper text domains
* Responsive design with accessibility support
* Performance optimized with minimal database queries

== Additional Information ==

= Requirements =

* WordPress 5.0 or higher
* WooCommerce 5.0 or higher
* PHP 7.4 or higher
* Modern browser with JavaScript enabled

= Plugin Features =

* **Zero Configuration** - Works out of the box with sensible defaults
* **Lightweight** - Only 50KB total plugin size
* **No External Dependencies** - No jQuery required, pure vanilla JavaScript
* **GDPR Compliant** - No personal data collection or cookies
* **Multisite Compatible** - Works on WordPress multisite installations
* **REST API Support** - Fully compatible with WooCommerce REST API

= Roadmap =

Future enhancements based on user feedback:
* Product-specific countdown rules
* Multiple shipping zones support
* Holiday calendar integration
* Email notification integration
* Countdown widget for sidebars
* Shortcode support for custom placements

== Privacy Policy ==

This plugin does not:
* Collect any personal data
* Set any cookies
* Make external API calls
* Store user behavior data

All countdown calculations are performed locally in the user's browser using JavaScript.