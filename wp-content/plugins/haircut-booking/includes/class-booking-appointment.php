<?php
/**
 * Class for handling appointment/booking operations
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/includes
 */

class Booking_Appointment {

	/**
	 * Get all bookings
	 *
	 * @since    1.0.0
	 * @param    array    $args    Query arguments
	 * @return   array    Array of bookings
	 */
	public static function get_bookings($args = array()) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_appointments';
		$customers_table = $wpdb->prefix . 'booking_customers';
		$services_table = $wpdb->prefix . 'booking_services';
		$employees_table = $wpdb->prefix . 'booking_employees';

		$defaults = array(
			'date_from' => '',
			'date_to' => '',
			'status' => '',
			'customer_id' => '',
			'service_id' => '',
			'employee_id' => '',
			'order_by' => 'booking_date',
			'order' => 'ASC',
		);

		$args = wp_parse_args($args, $defaults);

		$query = "SELECT b.*, 
                c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
                s.name as service_name, s.duration as service_duration,
                e.name as employee_name
                FROM $table_name b
                JOIN $customers_table c ON b.customer_id = c.id
                JOIN $services_table s ON b.service_id = s.id
                JOIN $employees_table e ON b.employee_id = e.id
                WHERE 1=1";

		$prepare_args = array();

		if (!empty($args['date_from'])) {
			$query .= " AND b.booking_date >= %s";
			$prepare_args[] = $args['date_from'];
		}

		if (!empty($args['date_to'])) {
			$query .= " AND b.booking_date <= %s";
			$prepare_args[] = $args['date_to'];
		}

		if (!empty($args['status'])) {
			$query .= " AND b.status = %s";
			$prepare_args[] = $args['status'];
		}

		if (!empty($args['customer_id'])) {
			$query .= " AND b.customer_id = %d";
			$prepare_args[] = $args['customer_id'];
		}

		if (!empty($args['service_id'])) {
			$query .= " AND b.service_id = %d";
			$prepare_args[] = $args['service_id'];
		}

		if (!empty($args['employee_id'])) {
			$query .= " AND b.employee_id = %d";
			$prepare_args[] = $args['employee_id'];
		}

		$query .= " ORDER BY b." . esc_sql($args['order_by']) . " " . esc_sql($args['order']);

		if (!empty($prepare_args)) {
			$query = $wpdb->prepare($query, $prepare_args);
		}

		return $wpdb->get_results($query, ARRAY_A);
	}

	/**
	 * Get a single booking by ID
	 *
	 * @since    1.0.0
	 * @param    int    $id    Booking ID
	 * @return   array    Booking data
	 */
	public static function get_booking($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_appointments';
		$customers_table = $wpdb->prefix . 'booking_customers';
		$services_table = $wpdb->prefix . 'booking_services';
		$employees_table = $wpdb->prefix . 'booking_employees';

		$query = $wpdb->prepare(
			"SELECT b.*, 
            c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
            s.name as service_name, s.duration as service_duration,
            e.name as employee_name
            FROM $table_name b
            JOIN $customers_table c ON b.customer_id = c.id
            JOIN $services_table s ON b.service_id = s.id
            JOIN $employees_table e ON b.employee_id = e.id
            WHERE b.id = %d",
			$id
		);

		return $wpdb->get_row($query, ARRAY_A);
	}

	/**
	 * Check if time slot is available
	 *
	 * @since    1.0.0
	 * @param    int     $employee_id       Employee ID
	 * @param    string  $date              Date in Y-m-d format
	 * @param    string  $time              Time in H:i format
	 * @param    int     $service_id        Service ID for duration check
	 * @param    int     $exclude_booking_id  Booking ID to exclude (for updates)
	 * @return   bool    True if available, false if not
	 */
	public static function is_time_slot_available($employee_id, $date, $time, $service_id, $exclude_booking_id = 0) {
		global $wpdb;
		$bookings_table = $wpdb->prefix . 'booking_appointments';
		$services_table = $wpdb->prefix . 'booking_services';

		// Get service duration
		$service = $wpdb->get_row(
			$wpdb->prepare("SELECT duration FROM $services_table WHERE id = %d", $service_id),
			ARRAY_A
		);

		if (!$service) {
			return false;
		}

		$duration = $service['duration'];

		// Convert time to minutes for calculation
		list($hours, $minutes) = explode(':', $time);
		$time_in_minutes = ($hours * 60) + $minutes;

		// New booking end time
		$new_booking_end_time = $time_in_minutes + $duration;

		// Get all bookings for the employee on that date
		$query = $wpdb->prepare(
			"SELECT b.*, s.duration
            FROM $bookings_table b
            JOIN $services_table s ON b.service_id = s.id
            WHERE b.employee_id = %d
            AND b.booking_date = %s
            AND b.id != %d",
			$employee_id,
			$date,
			$exclude_booking_id
		);

		$bookings = $wpdb->get_results($query, ARRAY_A);

		foreach ($bookings as $booking) {
			// Convert booking time to minutes
			list($booking_hours, $booking_minutes) = explode(':', $booking['booking_time']);
			$booking_time_in_minutes = ($booking_hours * 60) + $booking_minutes;

			// Booking end time
			$booking_end_time = $booking_time_in_minutes + $booking['duration'];

			// Check for overlap
			if (
				// New booking starts during existing booking
				($time_in_minutes >= $booking_time_in_minutes && $time_in_minutes < $booking_end_time) ||
				// Existing booking starts during new booking
				($booking_time_in_minutes >= $time_in_minutes && $booking_time_in_minutes < $new_booking_end_time)
			) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get available time slots for a specific date
	 *
	 * @since    1.0.0
	 * @param    int     $employee_id    Employee ID
	 * @param    string  $date           Date in Y-m-d format
	 * @param    int     $service_id     Service ID
	 * @return   array   Array of available time slots
	 */
	public static function get_available_time_slots($employee_id, $date, $service_id) {
		// Business hours
		$start_hour = 9; // 9 AM
		$end_hour = 18; // 6 PM
		$slot_interval = 30; // 30-minute intervals

		$available_slots = array();

		// Generate all possible time slots
		for ($hour = $start_hour; $hour < $end_hour; $hour++) {
			for ($minute = 0; $minute < 60; $minute += $slot_interval) {
				$time = sprintf('%02d:%02d', $hour, $minute);

				// Check if slot is available
				if (self::is_time_slot_available($employee_id, $date, $time, $service_id)) {
					$available_slots[] = $time;
				}
			}
		}

		return $available_slots;
	}

	/**
	 * Add a new booking
	 *
	 * @since    1.0.0
	 * @param    array    $booking_data    Booking data
	 * @return   int|false    The ID of the inserted booking or false on failure
	 */
	public static function add_booking($booking_data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_appointments';

		// Check if the time slot is available
		if (!self::is_time_slot_available(
			$booking_data['employee_id'],
			$booking_data['booking_date'],
			$booking_data['booking_time'],
			$booking_data['service_id']
		)) {
			return false;
		}

		$result = $wpdb->insert(
			$table_name,
			array(
				'customer_id' => intval($booking_data['customer_id']),
				'service_id' => intval($booking_data['service_id']),
				'employee_id' => intval($booking_data['employee_id']),
				'booking_date' => sanitize_text_field($booking_data['booking_date']),
				'booking_time' => sanitize_text_field($booking_data['booking_time']),
				'status' => sanitize_text_field($booking_data['status']),
				'notes' => sanitize_textarea_field($booking_data['notes']),
				'cost' => floatval($booking_data['cost']),
			)
		);

		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Update an existing booking
	 *
	 * @since    1.0.0
	 * @param    int      $id             Booking ID
	 * @param    array    $booking_data    Booking data
	 * @return   bool     True on success, false on failure
	 */
	public static function update_booking($id, $booking_data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_appointments';

		// Check if the time slot is available
		if (!self::is_time_slot_available(
			$booking_data['employee_id'],
			$booking_data['booking_date'],
			$booking_data['booking_time'],
			$booking_data['service_id'],
			$id
		)) {
			return false;
		}

		$result = $wpdb->update(
			$table_name,
			array(
				'customer_id' => intval($booking_data['customer_id']),
				'service_id' => intval($booking_data['service_id']),
				'employee_id' => intval($booking_data['employee_id']),
				'booking_date' => sanitize_text_field($booking_data['booking_date']),
				'booking_time' => sanitize_text_field($booking_data['booking_time']),
				'status' => sanitize_text_field($booking_data['status']),
				'notes' => sanitize_textarea_field($booking_data['notes']),
				'cost' => floatval($booking_data['cost']),
			),
			array('id' => $id)
		);

// For debugging issues
		if ($result === false) {
			error_log('Booking update failed. Error: ' . $wpdb->last_error);
		}

		return $result;
	}

	/**
	 * Delete a booking
	 *
	 * @since    1.0.0
	 * @param    int    $id    Booking ID
	 * @return   bool    True on success, false on failure
	 */
	public static function delete_booking($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'booking_appointments';

		return $wpdb->delete($table_name, array('id' => $id));
	}
}