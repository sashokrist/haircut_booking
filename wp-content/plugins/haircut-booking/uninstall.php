<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

// Delete all plugin options
delete_option('booking_settings');

// Drop custom tables
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}booking_services");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}booking_employees");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}booking_customers");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}booking_appointments");