<?php
/**
 * Class for plugin activation functionality
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/includes
 */

class Booking_Activator {

	/**
	 * Create necessary database tables and initial settings
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


		// Create services table
		$table_services = $wpdb->prefix . 'booking_services';
		$sql_services = "CREATE TABLE $table_services (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            description text,
            duration int NOT NULL,
            price decimal(10,2) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

		// Create employees table
		$sql_employees = "CREATE TABLE {$wpdb->prefix}booking_employees (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(50),
        bio text,
        services text,
        created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";


		dbDelta($sql_employees);


		// Create customers table
		$table_customers = $wpdb->prefix . 'booking_customers';
		$sql_customers = "CREATE TABLE $table_customers (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20),
            address text,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

		// Create bookings table
		$table_bookings = $wpdb->prefix . 'booking_appointments';
		$sql_bookings = "CREATE TABLE $table_bookings (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            customer_id mediumint(9) NOT NULL,
            service_id mediumint(9) NOT NULL,
            employee_id mediumint(9) NOT NULL,
            booking_date date NOT NULL,
            booking_time time NOT NULL,
            status varchar(20) DEFAULT 'pending',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql_services);
		dbDelta($sql_employees);
		dbDelta($sql_customers);
		dbDelta($sql_bookings);

		// Add default services
		$default_services = array(
			array(
				'name' => 'Haircut',
				'description' => 'Standard haircut service',
				'duration' => 30,
				'price' => 35.00
			),
			array(
				'name' => 'Color Hair',
				'description' => 'Hair coloring service',
				'duration' => 90,
				'price' => 75.00
			),
			array(
				'name' => 'Styling',
				'description' => 'Hair styling service',
				'duration' => 45,
				'price' => 45.00
			)
		);

		foreach ($default_services as $service) {
			$wpdb->insert(
				$table_services,
				$service
			);
		}
	}
}