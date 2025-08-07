<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/admin
 */

class Booking_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version           The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style($this->plugin_name, BOOKING_PLUGIN_URL . 'admin/css/booking-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_name, BOOKING_PLUGIN_URL . 'admin/js/booking-admin.js', array('jquery'), $this->version, false);

		wp_localize_script($this->plugin_name, 'booking_admin_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('booking_admin_nonce'),
		));
	}

	/**
	 * Add admin menu pages
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {
		// Main menu
		add_menu_page(
			__('Salon Booking', 'booking'),
			__('Salon Booking', 'booking'),
			'manage_options',
			'booking',
			array($this, 'display_bookings_page'),
			'dashicons-calendar-alt',
			30
		);

		// Bookings submenu
		add_submenu_page(
			'booking',
			__('Bookings', 'booking'),
			__('Bookings', 'booking'),
			'manage_options',
			'booking',
			array($this, 'display_bookings_page')
		);

		// Services submenu
		add_submenu_page(
			'booking',
			__('Services', 'booking'),
			__('Services', 'booking'),
			'manage_options',
			'booking-services',
			array($this, 'display_services_page')
		);

		// Employees submenu
		add_submenu_page(
			'booking',
			__('Employees', 'booking'),
			__('Employees', 'booking'),
			'manage_options',
			'booking-employees',
			array($this, 'display_employees_page')
		);

		// Customers submenu
		add_submenu_page(
			'booking',
			__('Customers', 'booking'),
			__('Customers', 'booking'),
			'manage_options',
			'booking-customers',
			array($this, 'display_customers_page')
		);

		// Settings submenu
		add_submenu_page(
			'booking',
			__('Settings', 'booking'),
			__('Settings', 'booking'),
			'manage_options',
			'booking-settings',
			array($this, 'display_settings_page')
		);
	}

	/**
	 * Display the bookings admin page
	 *
	 * @since    1.0.0
	 */
	public function display_bookings_page() {
		include_once BOOKING_PLUGIN_PATH . 'admin/partials/booking-admin-bookings.php';
	}

	/**
	 * Display the services admin page
	 *
	 * @since    1.0.0
	 */
	public function display_services_page() {
		// Process form submission for adding/editing services
		if (isset($_POST['add_service']) && isset($_POST['_wpnonce'])) {
			// Verify nonce
			if (wp_verify_nonce($_POST['_wpnonce'], 'add_service_nonce')) {

				// Sanitize input data
				$service_name = sanitize_text_field($_POST['service_name']);
				$service_description = sanitize_textarea_field($_POST['service_description']);
				$service_duration = intval($_POST['service_duration']);
				$service_price = floatval($_POST['service_price']);

				// Use the Service class to create a new service
				$service = new Booking_Service();
				$result = $service->create(array(
					'name' => $service_name,
					'description' => $service_description,
					'duration' => $service_duration,
					'price' => $service_price
				));

				if ($result) {
					// Success message - will be displayed on the admin page
					add_settings_error(
						'booking_service_messages',
						'service_added',
						'Service added successfully!',
						'updated'
					);
				} else {
					// Error message
					add_settings_error(
						'booking_service_messages',
						'service_error',
						'Error adding service. Please try again.',
						'error'
					);
				}
			}
		}
		include_once BOOKING_PLUGIN_PATH . 'admin/partials/booking-admin-services.php';
	}

	/**
	 * Display the employees admin page
	 *
	 * @since    1.0.0
	 */
	public function display_employees_page() {

		// Process form submission
		if (isset($_POST['booking_employee_nonce']) && wp_verify_nonce($_POST['booking_employee_nonce'], 'save_booking_employee')) {
			// Remove the check for 'submit' if using submit_button()

			// Collect and sanitize form data
			$employee_data = array(
				'name' => sanitize_text_field($_POST['employee_name']),
				'email' => sanitize_email($_POST['employee_email']),
				'phone' => sanitize_text_field($_POST['employee_phone']),
				'bio' => isset($_POST['employee_bio']) ? sanitize_textarea_field($_POST['employee_bio']) : '',
			);

			// Add services if present
			if (isset($_POST['employee_services']) && is_array($_POST['employee_services'])) {
				$employee_data['services'] = maybe_serialize(array_map('intval', $_POST['employee_services']));
			}

			// For debugging
			error_log('About to create employee with data: ' . print_r($employee_data, true));

			// Create new employee using the static create method
			$result = Booking_Employee::create($employee_data);

			// For debugging
			error_log('Create employee result: ' . var_export($result, true));

			if ($result) {
				// Success message
				add_settings_error(
					'booking_employee_messages',
					'booking_employee_message',
					'Employee added successfully.',
					'updated'
				);
			} else {
				// Error message
				add_settings_error(
					'booking_employee_messages',
					'booking_employee_message',
					'Failed to add employee.',
					'error'
				);
			}
		}

		if (isset($_POST['submit']) && isset($_POST['booking_employee_nonce'])) {
			if (wp_verify_nonce($_POST['booking_employee_nonce'], 'save_booking_employee')) {
				// Collect and sanitize form data
				$employee_data = array(
					'name' => sanitize_text_field($_POST['employee_name']),
					'email' => sanitize_email($_POST['employee_email']),
					'phone' => sanitize_text_field($_POST['employee_phone']),
					'bio' => isset($_POST['employee_bio']) ? sanitize_textarea_field($_POST['employee_bio']) : '',
				);

				// Add services if present
				if (isset($_POST['employee_services']) && is_array($_POST['employee_services'])) {
					$employee_data['services'] = maybe_serialize(array_map('intval', $_POST['employee_services']));
				}

				// Create new employee using the static create method
				$result = Booking_Employee::create($employee_data);

				if ($result) {
					// Success message
					add_settings_error(
						'booking_employee_messages',
						'booking_employee_message',
						'Employee added successfully.',
						'updated'
					);
				} else {
					// Error message
					add_settings_error(
						'booking_employee_messages',
						'booking_employee_message',
						'Failed to add employee.',
						'error'
					);
				}
			}
		}
		include_once BOOKING_PLUGIN_PATH . 'admin/partials/booking-admin-employees.php';
	}

	/**
	 * Display the customers admin page
	 *
	 * @since    1.0.0
	 */
	public function display_customers_page() {
		include_once BOOKING_PLUGIN_PATH . 'admin/partials/booking-admin-customers.php';
	}

	/**
	 * Display the settings admin page
	 *
	 * @since    1.0.0
	 */
	public function display_settings_page() {
		include_once BOOKING_PLUGIN_PATH . 'admin/partials/booking-admin-settings.php';
	}

	/**
	 * AJAX handler for getting bookings
	 *
	 * @since    1.0.0
	 */
	public function get_bookings() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$args = array();

		if (!empty($_POST['date_from'])) {
			$args['date_from'] = sanitize_text_field($_POST['date_from']);
		}

		if (!empty($_POST['date_to'])) {
			$args['date_to'] = sanitize_text_field($_POST['date_to']);
		}

		if (!empty($_POST['status'])) {
			$args['status'] = sanitize_text_field($_POST['status']);
		}

		if (!empty($_POST['customer_id'])) {
			$args['customer_id'] = intval($_POST['customer_id']);
		}

		if (!empty($_POST['service_id'])) {
			$args['service_id'] = intval($_POST['service_id']);
		}

		if (!empty($_POST['employee_id'])) {
			$args['employee_id'] = intval($_POST['employee_id']);
		}

		$bookings = Booking_Appointment::get_bookings($args);

		wp_send_json_success($bookings);
	}

	/**
	 * AJAX handler for adding a booking
	 *
	 * @since    1.0.0
	 */
	public function add_booking() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		// Get service price for cost calculation
		global $wpdb;
		$services_table = $wpdb->prefix . 'booking_services';
		$service_id = intval($_POST['service_id']);

		$service = $wpdb->get_row(
			$wpdb->prepare("SELECT price FROM $services_table WHERE id = %d", $service_id),
			ARRAY_A
		);

		if (!$service) {
			wp_send_json_error('Service not found');
		}

		// Add or get customer
		$customer_id = Booking_Customer::add_customer(array(
			'name' => sanitize_text_field($_POST['customer_name']),
			'email' => sanitize_email($_POST['customer_email']),
			'phone' => sanitize_text_field($_POST['customer_phone']),
		));

		if (!$customer_id) {
			wp_send_json_error('Failed to add customer');
		}

		// Add booking
		$booking_id = Booking_Appointment::add_booking(array(
			'customer_id' => $customer_id,
			'service_id' => $service_id,
			'employee_id' => intval($_POST['employee_id']),
			'appointment_date' => sanitize_text_field($_POST['appointment_date']),
			'appointment_time' => sanitize_text_field($_POST['appointment_time']),
			'status' => sanitize_text_field($_POST['status']),
			'notes' => sanitize_textarea_field($_POST['notes']),
			'cost' => $service['price'],
		));

		if (!$booking_id) {
			wp_send_json_error('Failed to add booking. Time slot might be unavailable.');
		}

		wp_send_json_success(array(
			'id' => $booking_id,
			'message' => 'Booking added successfully'
		));
	}

	/**
	 * AJAX handler for updating a booking
	 *
	 * @since    1.0.0
	 */
	public function update_booking() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$booking_id = intval($_POST['booking_id']);

		// Get service price for cost calculation
		global $wpdb;
		$services_table = $wpdb->prefix . 'booking_services';
		$service_id = intval($_POST['service_id']);

		$service = $wpdb->get_row(
			$wpdb->prepare("SELECT price FROM $services_table WHERE id = %d", $service_id),
			ARRAY_A
		);

		if (!$service) {
			wp_send_json_error('Service not found');
		}

		// Update customer
		$customer_id = intval($_POST['customer_id']);
		Booking_Customer::update_customer($customer_id, array(
			'name' => sanitize_text_field($_POST['customer_name']),
			'email' => sanitize_email($_POST['customer_email']),
			'phone' => sanitize_text_field($_POST['customer_phone']),
		));

		// Update booking
		$result = Booking_Appointment::update_booking($booking_id, array(
			'customer_id' => $customer_id,
			'service_id' => $service_id,
			'employee_id' => intval($_POST['employee_id']),
			'appointment_date' => sanitize_text_field($_POST['appointment_date']),
			'appointment_time' => sanitize_text_field($_POST['appointment_time']),
			'status' => sanitize_text_field($_POST['status']),
			'notes' => sanitize_textarea_field($_POST['notes']),
			'cost' => $service['price'],
		));

		if ($result === false) {
			wp_send_json_error('Failed to update booking. Time slot might be unavailable.');
		}

		wp_send_json_success(array(
			'message' => 'Booking updated successfully'
		));
	}

	/**
	 * AJAX handler for deleting a booking
	 *
	 * @since    1.0.0
	 */
	public function delete_booking() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$booking_id = intval($_POST['booking_id']);

		$result = Booking_Appointment::delete_booking($booking_id);

		if (!$result) {
			wp_send_json_error('Failed to delete booking');
		}

		wp_send_json_success(array(
			'message' => 'Booking deleted successfully'
		));
	}

	/**
	 * AJAX handler for getting services
	 *
	 * @since    1.0.0
	 */
	public function get_services() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$services = Booking_Service::get_services();

		wp_send_json_success($services);
	}

	/**
	 * AJAX handler for adding a service
	 *
	 * @since    1.0.0
	 */
	public function add_service() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$service_id = Booking_Service::add_service(array(
			'name' => sanitize_text_field($_POST['name']),
			'description' => sanitize_textarea_field($_POST['description']),
			'duration' => intval($_POST['duration']),
			'price' => floatval($_POST['price']),
		));

		if (!$service_id) {
			wp_send_json_error('Failed to add service');
		}

		wp_send_json_success(array(
			'id' => $service_id,
			'message' => 'Service added successfully'
		));
	}

	/**
	 * AJAX handler for updating a service
	 *
	 * @since    1.0.0
	 */
	public function update_service() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$service_id = intval($_POST['service_id']);

		$result = Booking_Service::update_service($service_id, array(
			'name' => sanitize_text_field($_POST['name']),
			'description' => sanitize_textarea_field($_POST['description']),
			'duration' => intval($_POST['duration']),
			'price' => floatval($_POST['price']),
		));

		if ($result === false) {
			wp_send_json_error('Failed to update service');
		}

		wp_send_json_success(array(
			'message' => 'Service updated successfully'
		));
	}

	/**
	 * AJAX handler for deleting a service
	 *
	 * @since    1.0.0
	 */
	public function delete_service() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$service_id = intval($_POST['service_id']);

		$result = Booking_Service::delete_service($service_id);

		if (!$result) {
			wp_send_json_error('Failed to delete service');
		}

		wp_send_json_success(array(
			'message' => 'Service deleted successfully'
		));
	}

	/**
	 * AJAX handler for getting employees
	 *
	 * @since    1.0.0
	 */
	public function get_employees() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$employees = Booking_Employee::get_employees();

		wp_send_json_success($employees);
	}

	/**
	 * AJAX handler for adding an employee
	 *
	 * @since    1.0.0
	 */
	public function add_employee() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$employee_id = Booking_Employee::add_employee(array(
			'name' => sanitize_text_field($_POST['name']),
			'email' => sanitize_email($_POST['email']),
			'phone' => sanitize_text_field($_POST['phone']),
		));

		if (!$employee_id) {
			wp_send_json_error('Failed to add employee');
		}

		wp_send_json_success(array(
			'id' => $employee_id,
			'message' => 'Employee added successfully'
		));
	}

	/**
	 * AJAX handler for updating an employee
	 *
	 * @since    1.0.0
	 */
	public function update_employee() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$employee_id = intval($_POST['employee_id']);

		$result = Booking_Employee::update_employee($employee_id, array(
			'name' => sanitize_text_field($_POST['name']),
			'email' => sanitize_email($_POST['email']),
			'phone' => sanitize_text_field($_POST['phone']),
		));

		if ($result === false) {
			wp_send_json_error('Failed to update employee');
		}

		wp_send_json_success(array(
			'message' => 'Employee updated successfully'
		));
	}

	/**
	 * AJAX handler for deleting an employee
	 *
	 * @since    1.0.0
	 */
	public function delete_employee() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$employee_id = intval($_POST['employee_id']);

		$result = Booking_Employee::delete_employee($employee_id);

		if (!$result) {
			wp_send_json_error('Failed to delete employee');
		}

		wp_send_json_success(array(
			'message' => 'Employee deleted successfully'
		));
	}

	/**
	 * AJAX handler for getting customers
	 *
	 * @since    1.0.0
	 */
	public function get_customers() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$customers = Booking_Customer::get_customers();

		wp_send_json_success($customers);
	}

	/**
	 * AJAX handler for adding a customer
	 *
	 * @since    1.0.0
	 */
	public function add_customer() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$customer_id = Booking_Customer::add_customer(array(
			'name' => sanitize_text_field($_POST['name']),
			'email' => sanitize_email($_POST['email']),
			'phone' => sanitize_text_field($_POST['phone']),
		));

		if (!$customer_id) {
			wp_send_json_error('Failed to add customer');
		}

		wp_send_json_success(array(
			'id' => $customer_id,
			'message' => 'Customer added successfully'
		));
	}

	/**
	 * AJAX handler for updating a customer
	 *
	 * @since    1.0.0
	 */
	public function update_customer() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$customer_id = intval($_POST['customer_id']);

		$result = Booking_Customer::update_customer($customer_id, array(
			'name' => sanitize_text_field($_POST['name']),
			'email' => sanitize_email($_POST['email']),
			'phone' => sanitize_text_field($_POST['phone']),
		));

		if ($result === false) {
			wp_send_json_error('Failed to update customer');
		}

		wp_send_json_success(array(
			'message' => 'Customer updated successfully'
		));
	}

	/**
	 * AJAX handler for deleting a customer
	 *
	 * @since    1.0.0
	 */
	public function delete_customer() {
		check_ajax_referer('booking_admin_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Permission denied');
		}

		$customer_id = intval($_POST['customer_id']);

		$result = Booking_Customer::delete_customer($customer_id);

		if (!$result) {
			wp_send_json_error('Failed to delete customer');
		}

		wp_send_json_success(array(
			'message' => 'Customer deleted successfully'
		));
	}
}