<?php
/**
 * Class for handling employee operations
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/includes
 */

class Booking_Employee {

	/**
	 * Get all employees
	 *
	 * @since    1.0.0
	 * @return   array    Array of employees
	 */
	public static function get_employees() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_employees';

		return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name ASC", ARRAY_A);
	}

	/**
	 * Get a single employee by ID
	 *
	 * @since    1.0.0
	 * @param    int    $id    Employee ID
	 * @return   array    Employee data
	 */
	public static function get_employee($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_employees';

		return $wpdb->get_row(
			$wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id),
			ARRAY_A
		);
	}

	/**
	 * Create a new employee
	 *
	 * @param array $data Employee data
	 * @return int|false The employee ID on success, false on failure
	 */
	public static function create($data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_employees';

		// Check if table exists
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			error_log('Employees table does not exist!');
			return false;
		}

		// Add created_at timestamp
		$data['created_at'] = current_time('mysql');

		// Debug information
		error_log('Attempting to insert employee into table: ' . $table_name);
		error_log('Data: ' . print_r($data, true));

		// Insert data
		$result = $wpdb->insert(
			$table_name,
			$data
		);

		if ($result) {
			return $wpdb->insert_id; // Return the ID of the new employee
		}

		return false;
	}

	/**
	 * Add a new employee (alias for backward compatibility)
	 *
	 * @since    1.0.0
	 * @param    array    $employee_data    Employee data
	 * @return   int|false    The ID of the inserted employee or false on failure
	 */
	public static function add_employee($employee_data) {
		return self::create($employee_data);
	}

	/**
	 * Update an existing employee
	 *
	 * @since    1.0.0
	 * @param    int      $id              Employee ID
	 * @param    array    $employee_data    Employee data
	 * @return   bool     True on success, false on failure
	 */
	public static function update_employee($id, $employee_data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_employees';

		return $wpdb->update(
			$table_name,
			$employee_data,
			array('id' => $id)
		);
	}

	/**
	 * Delete an employee
	 *
	 * @since    1.0.0
	 * @param    int    $id    Employee ID
	 * @return   bool    True on success, false on failure
	 */
	public static function delete_employee($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_employees';

		return $wpdb->delete($table_name, array('id' => $id));
	}
}