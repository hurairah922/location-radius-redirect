<?php
/**
 * Plugin Name: Location Radius Redirect
 * Description: Checks user's current location on "Order Now" click; redirects within radius, otherwise shows a lightbox message.
 * Version: 1.1.0
 * Author: Abu Hurarrah
 * Author URI: https://abuhurarrah.com
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

// Define core plugin constants if not already defined.
if ( ! defined( 'LRR_VERSION' ) ) {
  $plugin_data = get_file_data( __FILE__, [ 'Version' => 'Version' ], 'plugin' );
  define( 'LRR_VERSION', $plugin_data['Version'] );
}
define('LRR_PATH', plugin_dir_path(__FILE__));
define('LRR_URL', plugin_dir_url(__FILE__));

require_once LRR_PATH . 'includes/class-lrr-plugin.php';

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
  $settings_url = admin_url('options-general.php?page=location-radius-redirect');

  // Add Settings link at the beginning
  array_unshift($links, '<a href="' . esc_url($settings_url) . '">Settings</a>');
  return $links;
});

add_action('plugins_loaded', function () {
  \LRR\Plugin::instance()->init();
});
