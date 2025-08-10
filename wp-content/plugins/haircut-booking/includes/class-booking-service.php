<?php
/**
 * Class for handling service operations
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/includes
 */

class Booking_Service {

	/**
	 * Get all services
	 *
	 * @return array Array of service objects
	 */
	public static function get_services() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_services';

		$services = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name ASC", ARRAY_A);
		error_log('Services Data: ' . print_r($services, true));

		return $services;
	}

	/**
	 * Get a single service by ID
	 *
	 * @since    1.0.0
	 * @param    int    $id    Service ID
	 * @return   array    Service data
	 */
	public static function get_service($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_services';

		return $wpdb->get_row(
			$wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id),
			ARRAY_A
		);
	}

	/**
	 * Create a new service
	 *
	 * @param array $data Service data
	 * @return int|false The service ID on success, false on failure
	 */
	public static function create($data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_services';

		// Add created_at timestamp
		$data['created_at'] = current_time('mysql');

		// Insert data
		$result = $wpdb->insert(
			$table_name,
			$data,
			array('%s', '%s', '%d', '%f', '%s') // Format: string, string, integer, float, string
		);

		if ($result) {
			return $wpdb->insert_id; // Return the ID of the new service
		}

		return false;
	}

	/**
	 * Alias for create for consistency with other methods
	 *
	 * @param array $data Service data
	 * @return int|false The service ID on success, false on failure
	 */
	public static function add_service($data) {
		return self::create($data);
	}

	/**
	 * Update an existing service
	 *
	 * @since    1.0.0
	 * @param    int      $id             Service ID
	 * @param    array    $service_data    Service data
	 * @return   bool     True on success, false on failure
	 */
	public static function update_service($id, $service_data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_services';

		return $wpdb->update(
			$table_name,
			array(
				'name' => sanitize_text_field($service_data['name']),
				'description' => sanitize_textarea_field($service_data['description']),
				'duration' => intval($service_data['duration']),
				'price' => floatval($service_data['price']),
			),
			array('id' => $id)
		);
	}

	/**
	 * Delete a service
	 *
	 * @since    1.0.0
	 * @param    int    $id    Service ID
	 * @return   bool    True on success, false on failure
	 */
	public static function delete_service($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_services';

		return $wpdb->delete($table_name, array('id' => $id));
	}
}