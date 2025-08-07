<?php
/**
 * Plugin Name: Booking
 * Plugin URI: https://example.com/booking
 * Description: A booking management plugin for salon services
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: booking
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// Define plugin constants
define('BOOKING_PLUGIN_VERSION', '1.0.0');
define('BOOKING_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BOOKING_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once BOOKING_PLUGIN_PATH . 'includes/class-booking-activator.php';
require_once BOOKING_PLUGIN_PATH . 'includes/class-booking-deactivator.php';
require_once BOOKING_PLUGIN_PATH . 'includes/class-booking.php';

// Activation and deactivation hooks
register_activation_hook(__FILE__, array('Booking_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('Booking_Deactivator', 'deactivate'));

/**
 * Load plugin textdomain for translations
 */
function booking_load_textdomain() {
	load_plugin_textdomain('booking', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'booking_load_textdomain');

/**
 * Begins execution of the plugin.
 */
function run_booking() {
	$plugin = new Booking();
	$plugin->run();
}
run_booking();