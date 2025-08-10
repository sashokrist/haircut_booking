<?php
/**
 * Provide a admin area view for managing bookings
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/admin/partials
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$editing = false;
$booking = null;

// Check if we're in edit mode
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
    $booking = Booking_Appointment::get_booking($booking_id); // Fetch booking by ID
    if ($booking) {
        $editing = true;
    }
}
?>

<div class="wrap">
    <h1><?php echo $editing ? 'Edit Booking' : 'Bookings'; ?></h1>

    <?php if ($editing): ?>
        <form method="post" action="">
            <?php wp_nonce_field('update_booking_nonce', 'update_booking_nonce'); ?>
            <input type="hidden" name="booking_id" value="<?php echo esc_attr($booking['id']); ?>">

            <table class="form-table">
                <tr>
                    <th><label for="customer_id">Customer</label></th>
                    <td>
                        <select name="customer_id" id="customer_id" required>
                            <?php foreach (Booking_Customer::get_all() as $customer): ?>
                                <option value="<?php echo esc_attr($customer->id); ?>"
                                        <?php selected($booking['customer_id'], $customer->id); ?>>
                                    <?php echo esc_html($customer->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="service_id">Service</label></th>
                    <td>
                        <select name="service_id" id="service_id" required>
                            <?php foreach (Booking_Service::get_services() as $service): ?>
                                <option value="<?php echo esc_attr($service->id); ?>"
                                        <?php selected($booking['service_id'], $service->id); ?>>
                                    <?php echo esc_html($service->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="employee_id">Employee</label></th>
                    <td>
                        <select name="employee_id" id="employee_id" required>
                            <?php foreach (Booking_Employee::get_employees() as $employee): ?>
                                <option value="<?php echo esc_attr($employee->id); ?>"
                                        <?php selected($booking['employee_id'], $employee->id); ?>>
                                    <?php echo esc_html($employee->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="booking_date">Date</label></th>
                    <td>
                        <input type="date" name="booking_date" id="booking_date" class="regular-text"
                               value="<?php echo esc_attr($booking['booking_date']); ?>" required>
                    </td>
                </tr>
                <tr>
                    <th><label for="booking_time">Time</label></th>
                    <td>
                        <input type="time" name="booking_time" id="booking_time" class="regular-text"
                               value="<?php echo esc_attr($booking['booking_time']); ?>" required>
                    </td>
                </tr>
                <tr>
                    <th><label for="status">Status</label></th>
                    <td>
                        <select name="status" id="status" required>
                            <option value="pending" <?php selected($booking['status'], 'pending'); ?>>Pending</option>
                            <option value="confirmed" <?php selected($booking['status'], 'confirmed'); ?>>Confirmed</option>
                            <option value="cancelled" <?php selected($booking['status'], 'cancelled'); ?>>Cancelled</option>
                        </select>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="submit_booking" class="button button-primary" value="Update Booking">
            </p>
        </form>
    <?php else: ?>
        <!-- Bookings List -->
        <div class="booking-list-container">
            <?php
            global $wpdb;
            $bookings_table = $wpdb->prefix . 'bookings';
            $all_bookings = Booking_Appointment::get_bookings();

            if ($all_bookings): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($all_bookings as $booking): ?>
                        <tr data-customer-id="<?php echo esc_attr($booking['customer_id']); ?>"
                            data-service-id="<?php echo esc_attr($booking['service_id']); ?>"
                            data-booking-date="<?php echo esc_attr($booking['booking_date']); ?>"
                            data-booking-time="<?php echo esc_attr($booking['booking_time']); ?>"
                            data-status="<?php echo esc_attr($booking['status']); ?>">
                            <td><?php echo esc_html($booking['id']); ?></td>
                            <td><?php echo esc_html($booking['customer_name']); ?></td>
                            <td><?php echo esc_html($booking['service_name']); ?></td>
                            <td><?php echo esc_html($booking['employee_name']); ?></td>
                            <td><?php echo esc_html($booking['booking_date']); ?></td>
                            <td><?php echo esc_html($booking['booking_time']); ?></td>
                            <td><?php echo esc_html($booking['status']); ?></td>
                            <td>
                                <a href="#" class="button button-small edit-booking" data-id="<?php echo esc_attr($booking['id']); ?>">Edit</a>
                                <a href="#" class="button button-small delete-booking" data-id="<?php echo esc_attr($booking['id']); ?>"
                                   onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No bookings found.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Modal for Editing Bookings -->
    <div id="edit-booking-modal" style="display:none;">
        <form id="edit-booking-form">
            <input type="hidden" id="edit-booking-id" name="booking_id">
            <div>
                <label for="edit-customer-id">Customer</label>
<!--                <select id="edit-customer-id" name="customer_id">-->
<!--                    <!-- Populate dynamically -->-->
<!--                </select>-->
                <select name="customer_id" id="edit-customer-id">
                    <?php foreach (Booking_Customer::get_all() as $customer): ?>
                        <option value="<?php echo esc_attr($customer->id); ?>"
                                <?php selected($customer->id, $booking['customer_id']); ?>>
                            <?php echo esc_html($customer->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

            </div>
            <div>
                <label for="edit-service-id">Service</label>
<!--                <select id="edit-service-id" name="service_id">-->
<!--                    <!-- Populate dynamically -->-->
<!--                </select>-->
                <select name="service_id" id="service_id" required>
                    <option value="">Select a service</option>
                    <?php foreach (Booking_Service::get_services() as $service): ?>
                        <option value="<?php echo esc_attr($service['id']); ?>"
                                <?php selected($service['id'], $booking['service_id']); ?>>
                            <?php echo esc_html($service['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="edit-appointment-date">Date</label>
                <input type="date" id="edit-appointment-date" name="booking_date">
            </div>
            <div>
                <label for="edit-appointment-time">Time</label>
                <input type="time" id="edit-appointment-time" name="booking_time">
            </div>
            <div>
                <label for="edit-status">Status</label>
                <select id="edit-status" name="status">
                    <option value="confirmed">Confirmed</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <button type="submit" class="button button-primary">Save</button>
            <button type="button" class="modal-close button">Cancel</button>
        </form>
    </div>
</div>
