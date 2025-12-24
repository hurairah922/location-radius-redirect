# Location Radius Redirect
Redirect users to an external ordering link based on their current location and a configurable radius.

## Overview
Location Radius Redirect allows site owners (restaurants, cafés, takeaways) to control access to an external ordering system based on a visitor’s current location.

When a user clicks the "Order Now" link:
- The browser asks for location permission.
- The plugin calculates the distance between the user and the restaurant.
- If the user is within the allowed radius, they are redirected to the configured ordering URL.
- If the user denies permission, is outside the range, or their browser does not support geolocation, a friendly message is shown in a lightbox.

The plugin only redirects users; it does not process payments.

## Privacy & Data Usage
This plugin uses the browser’s Geolocation API only after explicit user interaction (clicking the Order Now link).
- No location data is stored
- No location data is transmitted to the site server
- No tracking or analytics are performed
- No personal data is collected

All distance calculations are performed client-side in the user’s browser.

## Requirements
- WordPress 6.0+
- PHP 7.4+
- HTTPS recommended (most browsers require HTTPS for Geolocation API)

## Installation
1. Upload the `location-radius-redirect` folder to `/wp-content/plugins/`, or install via the WordPress Plugins screen.
2. Activate the plugin through the Plugins menu in WordPress.
3. Go to Settings -> Location Radius Redirect.
4. Enter the restaurant latitude, longitude, allowed radius (KM), and redirect URL.
5. Save changes.

## Usage
You can use the plugin in two ways.

### Shortcode
Add this shortcode anywhere in your site:
`[order_now_button]`

Optional attributes:
`[order_now_button text="ORDER NOW"]`
`[order_now_button text="ORDER NOW" class="my-custom-class"]`

### Menu Item Integration
If you already have an "Order Now" menu item:
1. Keep it as a normal menu item.
2. In plugin settings, set the Bind Selector to target that menu link.

Example selector:
`#menu-item-123 > a`

3. The plugin will intercept the click and apply the location check.

The plugin keeps menu markup clean and removes theme-injected dropdown classes (like `dropdown-toggle`) from the Order Now link to prevent layout issues on mobile/tablet.

## FAQ
### Does this plugin track users?
No. The plugin does not track, store, or transmit any user location data.

### Does this plugin work without HTTPS?
Most browsers require HTTPS for geolocation. HTTPS is strongly recommended.

### Can users bypass the location check?
The plugin relies on browser geolocation. Advanced users may spoof location on some devices. This plugin is intended for practical service-area control, not strict enforcement.

### Does this plugin work with WooCommerce?
Yes, but it does not depend on WooCommerce. It simply redirects users to an external URL.

## Screenshots
1. Admin settings page
2. Location permission prompt
3. Outside delivery range message

## Changelog
### 1.1.0
- Initial public release
- Location-based redirect logic
- Menu item compatibility
- Lightbox messages
- Privacy-safe implementation

## License
GPLv2 or later  
https://www.gnu.org/licenses/gpl-2.0.html

## Author
Abu Hurairah  
https://abuhurarrah.com