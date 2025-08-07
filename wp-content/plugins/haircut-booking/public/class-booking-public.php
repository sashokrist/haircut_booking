<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/public
 */

class Booking_Public {

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
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version           The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style($this->plugin_name, BOOKING_PLUGIN_URL . 'public/css/booking-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_name, BOOKING_PLUGIN_URL . 'public/js/booking-public.js', array('jquery'), $this->version, false);

		wp_localize_script($this->plugin_name, 'booking_public_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('booking_public_nonce'),
		));
	}

	/**
	 * Register shortcodes
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode('booking_form', array($this, 'booking_form_shortcode'));
		add_shortcode('booking_list', array($this, 'booking_list_shortcode'));
	}

	/**
	 * Booking form shortcode
	 *
	 * @since    1.0.0
	 * @param    array    $atts    Shortcode attributes
	 * @return   string   Shortcode output
	 */
	public function booking_form_shortcode($atts) {
		// Enqueue necessary scripts and styles
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

		// Get services for the form
		$services = Booking_Service::get_services();

		// Get employees for the form
		$employees = Booking_Employee::get_employees();

		// Start output buffering
		ob_start();

		// Include the template
		include BOOKING_PLUGIN_PATH . 'templates/booking-form.php';

		// Return the buffered content
		return ob_get_clean();
	}

	/**
	 * Booking list shortcode
	 *
	 * @since    1.0.0
	 * @param    array    $atts    Shortcode attributes
	 * @return   string   Shortcode output
	 */
	public function booking_list_shortcode($atts) {
		// Check if user is logged in
		if (!is_user_logged_in()) {
			return '<p>' . __('Please log in to view your bookings.', 'booking') . '</p>';
		}

		// Get current user
		$current_user = wp_get_current_user();

		// Try to find customer by email
		$customer = Booking_Customer::get_customer_by_email($current_user->user_email);

		if (!$customer) {
			return '<p>' . __('You don\'t have any bookings yet.', 'booking') . '</p>';
		}

		// Get bookings for this customer
		$bookings = Booking_Appointment::get_bookings(array(
			'customer_id' => $customer['id']
		));

		// Start output buffering
		ob_start();

		// Include the template
		include BOOKING_PLUGIN_PATH . 'templates/booking-list.php';

		// Return the buffered content
		return ob_get_clean();
	}

	/**
	 * AJAX handler for submitting a booking
	 *
	 * @since    1.0.0
	 */
	public function submit_booking() {
		check_ajax_referer('booking_public_nonce', 'nonce');

		// Add or get customer
		$customer_id = Booking_Customer::add_customer(array(
			'name' => sanitize_text_field($_POST['customer_name']),
			'email' => sanitize_email($_POST['customer_email']),
			'phone' => sanitize_text_field($_POST['customer_phone']),
		));

		if (!$customer_id) {
			wp_send_json_error('Failed to add customer');
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

		// Add booking
		$booking_id = Booking_Appointment::add_booking(array(
			'customer_id' => $customer_id,
			'service_id' => $service_id,
			'employee_id' => intval($_POST['employee_id']),
			'appointment_date' => sanitize_text_field($_POST['appointment_date']),
			'appointment_time' => sanitize_text_field($_POST['appointment_time']),
			'status' => 'pending',
			'notes' => sanitize_textarea_field($_POST['notes']),
			'cost' => $service['price'],
		));

		if (!$booking_id) {
			wp_send_json_error('Failed to add booking. Time slot might be unavailable.');
		}

		// Send email notification
		$this->send_booking_notification($booking_id);

		wp_send_json_success(array(
			'id' => $booking_id,
			'message' => 'Booking submitted successfully'
		));
	}

	/**
	 * AJAX handler for getting available times
	 *
	 * @since    1.0.0
	 */
	public function get_available_times() {
		check_ajax_referer('booking_public_nonce', 'nonce');

		$employee_id = intval($_POST['employee_id']);
		$date = sanitize_text_field($_POST['date']);
		$service_id = intval($_POST['service_id']);

		$available_times = Booking_Appointment::get_available_time_slots($employee_id, $date, $service_id);

		wp_send_json_success(array(
			'times' => $available_times
		));
	}

	/**
	 * Send booking notification emails
	 *
	 * @since    1.0.0
	 * @param    int    $booking_id    Booking ID
	 */
	private function send_booking_notification($booking_id) {
		$booking = Booking_Appointment::get_booking($booking_id);

		if (!$booking) {
			return;
		}

		// Admin notification
		$admin_email = get_option('admin_email');
		$admin_subject = sprintf(__('New Booking: %s', 'booking'), $booking['service_name']);

		$admin_message = sprintf(
			__('New booking details:
            
Customer: %s
Email: %s
Phone: %s
Service: %s
Employee: %s
Date: %s
Time: %s
Cost: $%s
            
View booking in admin: %s', 'booking'),
			$booking['customer_name'],
			$booking['customer_email'],
			$booking['customer_phone'],
			$booking['service_name'],
			$booking['employee_name'],
			$booking['appointment_date'],
			$booking['appointment_time'],
			$booking['cost'],
			admin_url('admin.php?page=booking')
		);

		wp_mail($admin_email, $admin_subject, $admin_message);

		// Customer notification
		$customer_subject = sprintf(__('Your Booking Confirmation - %s', 'booking'), get_bloginfo('name'));

		$customer_message = sprintf(
			__('Dear %s,
            
Thank you for your booking. Your appointment details are as follows:
            
Service: %s
Employee: %s
Date: %s
Time: %s
Cost: $%s
            
If you need to cancel or reschedule, please contact us.
            
Regards,
%s', 'booking'),
			$booking['customer_name'],
			$booking['service_name'],
			$booking['employee_name'],
			$booking['appointment_date'],
			$booking['appointment_time'],
			$booking['cost'],
			get_bloginfo('name')
		);

		wp_mail($booking['customer_email'], $customer_subject, $customer_message);
	}
}