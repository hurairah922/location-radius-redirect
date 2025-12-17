<?php
/**
 * Plugin Name: Location Radius Redirect
 * Description: Checks user's current location on "Order Now" click; redirects within radius, otherwise shows a lightbox message.
 * Version: 1.0.53
 * Author: Abu Hurarrah
 * Author URI: https://abuhurarrah.com
 * Requires at least: 6.0
 * Requires PHP: 7.4
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

add_action('plugins_loaded', function () {
  \LRR\Plugin::instance()->init();
});
