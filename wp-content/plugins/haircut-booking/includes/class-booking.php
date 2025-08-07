<?php
/**
 * The core plugin class
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/includes
 */

class Booking {

	/**
	 * The loader that's responsible for maintaining and registering all hooks.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $loaders    Array to store actions and filters
	 */
	protected $loaders = array(
		'actions' => array(),
		'filters' => array()
	);

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->plugin_name = 'booking';
		$this->version = BOOKING_PLUGIN_VERSION;

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		// Load models
		require_once BOOKING_PLUGIN_PATH . 'includes/class-booking-service.php';
		require_once BOOKING_PLUGIN_PATH . 'includes/class-booking-employee.php';
		require_once BOOKING_PLUGIN_PATH . 'includes/class-booking-customer.php';
		require_once BOOKING_PLUGIN_PATH . 'includes/class-booking-appointment.php';

		// Load controllers
		require_once BOOKING_PLUGIN_PATH . 'admin/class-booking-admin.php';
		require_once BOOKING_PLUGIN_PATH . 'public/class-booking-public.php';
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Booking_Admin($this->get_plugin_name(), $this->get_version());

		$this->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->add_action('admin_menu', $plugin_admin, 'add_admin_menu');

		// Register AJAX actions for admin
		$this->add_action('wp_ajax_get_bookings', $plugin_admin, 'get_bookings');
		$this->add_action('wp_ajax_add_booking', $plugin_admin, 'add_booking');
		$this->add_action('wp_ajax_update_booking', $plugin_admin, 'update_booking');
		$this->add_action('wp_ajax_delete_booking', $plugin_admin, 'delete_booking');

		// Service management
		$this->add_action('wp_ajax_get_services', $plugin_admin, 'get_services');
		$this->add_action('wp_ajax_add_service', $plugin_admin, 'add_service');
		$this->add_action('wp_ajax_update_service', $plugin_admin, 'update_service');
		$this->add_action('wp_ajax_delete_service', $plugin_admin, 'delete_service');

		// Employee management
		$this->add_action('wp_ajax_get_employees', $plugin_admin, 'get_employees');
		$this->add_action('wp_ajax_add_employee', $plugin_admin, 'add_employee');
		$this->add_action('wp_ajax_update_employee', $plugin_admin, 'update_employee');
		$this->add_action('wp_ajax_delete_employee', $plugin_admin, 'delete_employee');

		// Customer management
		$this->add_action('wp_ajax_get_customers', $plugin_admin, 'get_customers');
		$this->add_action('wp_ajax_add_customer', $plugin_admin, 'add_customer');
		$this->add_action('wp_ajax_update_customer', $plugin_admin, 'update_customer');
		$this->add_action('wp_ajax_delete_customer', $plugin_admin, 'delete_customer');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Booking_Public($this->get_plugin_name(), $this->get_version());

		$this->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->add_action('init', $plugin_public, 'register_shortcodes');

		// Register AJAX actions for public
		$this->add_action('wp_ajax_nopriv_submit_booking', $plugin_public, 'submit_booking');
		$this->add_action('wp_ajax_submit_booking', $plugin_public, 'submit_booking');
		$this->add_action('wp_ajax_nopriv_get_available_times', $plugin_public, 'get_available_times');
		$this->add_action('wp_ajax_get_available_times', $plugin_public, 'get_available_times');
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		$this->loaders['actions'][] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		$this->loaders['filters'][] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);
	}

	/**
	 * Register all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		// Register actions
		foreach ($this->loaders['actions'] as $hook) {
			add_action(
				$hook['hook'],
				array($hook['component'], $hook['callback']),
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		// Register filters
		foreach ($this->loaders['filters'] as $hook) {
			add_filter(
				$hook['hook'],
				array($hook['component'], $hook['callback']),
				$hook['priority'],
				$hook['accepted_args']
			);
		}
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}