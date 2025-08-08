<?php
/**
 * Class for handling customer operations
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/includes
 */

class Booking_Customer {

	// Create a new customer
	public static function create($data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_customers';

		// Log the attempt to create a customer
		error_log('Attempting to insert customer into table: ' . $table_name);
		error_log('Data: ' . print_r($data, true));

		// Set the created_at date if not provided
		if (!isset($data['created_at'])) {
			$data['created_at'] = current_time('mysql');
		}

		// Insert the customer data
		$result = $wpdb->insert(
			$table_name,
			$data
		);

		// Log the result
		error_log('Create customer result: ' . ($result ? 'true' : 'false'));

		if ($result) {
			return $wpdb->insert_id;
		}

		return false;
	}

	/**
	 * Get all customers
	 *
	 * @return array Array of customer objects
	 */
	public static function get_all() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_customers';

		return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name ASC");
	}

	/**
	 * Get all customers
	 *
	 * @since    1.0.0
	 * @return   array    Array of customers
	 */
	public static function get_customers() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_customers';

		return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name ASC", ARRAY_A);
	}

	/**
	 * Get a single customer by ID
	 *
	 * @since    1.0.0
	 * @param    int    $id    Customer ID
	 * @return   array    Customer data
	 */
	public static function get_by_id($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_customers';

		return $wpdb->get_row(
			$wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id),
			ARRAY_A
		);
	}

	/**
	 * Get a customer by email
	 *
	 * @since    1.0.0
	 * @param    string    $email    Customer email
	 * @return   array    Customer data
	 */
	public static function get_customer_by_email($email) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_customers';

		return $wpdb->get_row(
			$wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $email),
			ARRAY_A
		);
	}

	/**
	 * Add a new customer
	 *
	 * @since    1.0.0
	 * @param    array    $customer_data    Customer data
	 * @return   int|false    The ID of the inserted customer or false on failure
	 */
	public static function add_customer($customer_data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_customers';

		// Check if customer already exists
		$existing_customer = self::get_customer_by_email($customer_data['email']);
		if ($existing_customer) {
			return $existing_customer['id'];
		}

		$result = $wpdb->insert(
			$table_name,
			array(
				'name' => sanitize_text_field($customer_data['name']),
				'email' => sanitize_email($customer_data['email']),
				'phone' => sanitize_text_field($customer_data['phone']),
			)
		);

		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Update an existing customer
	 *
	 * @since    1.0.0
	 * @param    int      $id              Customer ID
	 * @param    array    $customer_data    Customer data
	 * @return   bool     True on success, false on failure
	 */
	public static function update_customer($id, $customer_data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_customers';

		return $wpdb->update(
			$table_name,
			array(
				'name' => sanitize_text_field($customer_data['name']),
				'email' => sanitize_email($customer_data['email']),
				'phone' => sanitize_text_field($customer_data['phone']),
			),
			array('id' => $id)
		);
	}

	/**
	 * Delete a customer
	 *
	 * @since    1.0.0
	 * @param    int    $id    Customer ID
	 * @return   bool    True on success, false on failure
	 */
	public static function delete_customer($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_customers';

		return $wpdb->delete($table_name, array('id' => $id));
	}
}