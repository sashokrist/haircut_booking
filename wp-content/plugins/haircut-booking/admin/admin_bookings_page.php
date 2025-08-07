<?php
/**
 * Admin bookings page
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/admin/partials
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
?>

<div class="wrap booking-wrap">
	<div class="booking-header">
		<h1 class="booking-title"><?php echo esc_html(get_admin_page_title()); ?></h1>
		<button id="add-booking-btn" class="button button-primary booking-add-new">Add New Booking</button>
	</div>

	<div class="booking-message"></div>

	<div class="booking-filters">
		<form id="booking-filter-form">
			<div class="filter-row">
				<div class="filter-group">
					<label for="date_from">Date From</label>
					<input type="text" id="date_from" name="date_from" class="datepicker">
				</div>

				<div class="filter-group">
					<label for="date_to">Date To</label>
					<input type="text" id="date_to" name="date_to" class="datepicker">
				</div>

				<div class="filter-group">
					<label for="status">Status</label>
					<select id="status" name="status">
						<option value="">All</option>
						<option value="pending">Pending</option>
						<option value="confirmed">Confirmed</option>
						<option value="cancelled">Cancelled</option>
						<option value="completed">Completed</option>
					</select>
				</div>
			</div>

			<div class="filter-actions">
				<button type="submit" class="button">Apply Filters</button>
				<button type="button" id="booking-filter-reset" class="button">Reset Filters</button>
			</div>
		</form>
	</div>

	<table id="booking-list" class="widefat striped booking-table">
		<thead>
		<tr>
			<th>Customer</th>
			<th>Service</th>
			<th>Date</th>
			<th>Time</th>
			<th>Status</th>
			<th>Cost</th>
			<th>Actions</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td colspan="7">Loading...</td>
		</tr>
		</tbody>
	</table>

	<!-- Add Booking Modal -->
	<div id="add-booking-modal" class="booking-modal">
		<div class="booking-modal-content">
			<div class="booking-modal-header">
				<h2 class="booking-modal-title">Add New Booking</h2>
				<span class="booking-modal-close">&times;</span>
			</div>

			<form id="add-booking-form" class="booking-form">
				<div class="form-row">
					<div class="form-group">
						<label for="customer_name">Customer Name</label>
						<input type="text" id="customer_name" name="customer_name" required>
					</div>

					<div class="form-group">
						<label for="customer_email">Customer Email</label>
						<input type="email" id="customer_email" name="customer_email" required>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="customer_phone">Customer Phone</label>
						<input type="tel" id="customer_phone" name="customer_phone" required>
					</div>

					<div class="form-group">
						<label for="service_id">Service</label>
						<select id="service_id" name="service_id" required>
							<option value="">Select a service</option>
							<?php
							$services = Booking_Service::get_services();
							foreach ($services as $service) {
								echo '<option value="' . esc_attr($service['id']) . '">' . esc_html($service['name']) . ' - $' . esc_html($service['price']) . '</option>';
							}
							?>
						</select>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="employee_id">Employee</label>
						<select id="employee_id" name="employee_id" required>
							<option value="">Select an employee</option>
							<?php
							$employees = Booking_Employee::get_employees();
							foreach ($employees as $employee) {
								echo '<option value="' . esc_attr($employee['id']) . '">' . esc_html($employee['name']) . '</option>';
							}
							?>
						</select>
					</div>

					<div class="form-group">
						<label for="status">Status</label>
						<select id="status" name="status" required>
							<option value="pending">Pending</option>
							<option value="confirmed">Confirmed</option>
							<option value="cancelled">Cancelled</option>
							<option value="completed">Completed</option>
						</select>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="booking_date">Date</label>
						<input type="text" id="booking_date" name="booking_date" class="datepicker" required>
					</div>

					<div class="form-group">
						<label for="booking_time">Time</label>
						<input type="time" id="booking_time" name="booking_time" required>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="notes">Notes</label>
						<textarea id="notes" name="notes" rows="3"></textarea>
					</div>
				</div>

				<div class="form-row">
					<button type="submit" class="button button-primary">Add Booking</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Edit Booking Modal -->
	<div id="edit-booking-modal" class="booking-modal">
		<div class="booking-modal-content">
			<div class="booking-modal-header">
				<h2 class="booking-modal-title">Edit Booking</h2>
				<span class="booking-modal-close">&times;</span>
			</div>

			<form id="edit-booking-form" class="booking-form">
				<input type="hidden" id="edit-booking-id" name="booking_id">
				<input type="hidden" id="edit-customer-id" name="customer_id">

				<div class="form-row">
					<div class="form-group">
						<label for="edit-customer-name">Customer Name</label>
						<input type="text" id="edit-customer-name" name="customer_name" required>
					</div>

					<div class="form-group">
						<label for="edit-customer-email">Customer Email</label>
						<input type="email" id="edit-customer-email" name="customer_email" required>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="edit-customer-phone">Customer Phone</label>
						<input type="tel" id="edit-customer-phone" name="customer_phone" required>
					</div>

					<div class="form-group">
						<label for="edit-service-id">Service</label>
						<select id="edit-service-id" name="service_id" required>
							<option value="">Select a service</option>
							<?php
							foreach ($services as $service) {
								echo '<option value="' . esc_attr($service['id']) . '">' . esc_html($service['name']) . ' - $' . esc_html($service['price']) . '</option>';
							}
							?>
						</select>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="edit-employee-id">Employee</label>
						<select id="edit-employee-id" name="employee_id" required>
							<option value="">Select an employee</option>
							<?php
							foreach ($employees as $employee) {
								echo '<option value="' . esc_attr($employee['id']) . '">' . esc_html($employee['name']) . '</option>';
							}
							?>
						</select>
					</div>

					<div class="form-group">
						<label for="edit-status">Status</label>
						<select id="edit-status" name="status" required>
							<option value="pending">Pending</option>
							<option value="confirmed">Confirmed</option>
							<option value="cancelled">Cancelled</option>
							<option value="completed">Completed</option>
						</select>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="edit-appointment-date">Date</label>
						<input type="text" id="edit-appointment-date" name="booking_date" class="datepicker" required>
					</div>

					<div class="form-group">
						<label for="edit-appointment-time">Time</label>
						<input type="time" id="edit-appointment-time" name="booking_time" required>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="edit-notes">Notes</label>
						<textarea id="edit-notes" name="notes" rows="3"></textarea>
					</div>
				</div>

				<div class="form-row">
					<button type="submit" class="button button-primary">Update Booking</button>
				</div>
			</form>
		</div>
	</div>
</div>