<?php
/**
 * Template Name: Booking Page
 *
 * This template is used for the booking page.
 */

get_header();
?>

	<main class="container py-5">
		<h1 class="text-center mb-4">Book Your Appointment</h1>

		<div class="row justify-content-center">
			<div class="col-md-8">
				<div class="card">
					<div class="card-body">
						<form id="booking-form" method="post" action="">
							<!-- Customer Information -->
							<div class="mb-4">
								<h3>Your Information</h3>
								<div class="mb-3">
									<label for="customer_name" class="form-label">Name *</label>
									<input type="text" class="form-control" id="customer_name" name="customer_name" required>
								</div>
								<div class="mb-3">
									<label for="customer_email" class="form-label">Email *</label>
									<input type="email" class="form-control" id="customer_email" name="customer_email" required>
								</div>
								<div class="mb-3">
									<label for="customer_phone" class="form-label">Phone *</label>
									<input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
								</div>
							</div>

							<!-- Service Selection -->
							<div class="mb-4">
								<h3>Select Service</h3>
								<div class="mb-3">
									<label for="service" class="form-label">Service *</label>
									<select class="form-select" id="service" name="service_id" required>
										<option value="">Select a service</option>
										<?php
										// Get all services
										global $wpdb;
										$services = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}booking_services ORDER BY name ASC");

										foreach ($services as $service) {
											echo '<option value="' . esc_attr($service->id) . '">' . esc_html($service->name) . ' - $' . esc_html($service->price) . '</option>';
										}
										?>
									</select>
								</div>
							</div>

							<!-- Employee Selection -->
							<div class="mb-4">
								<h3>Select Staff</h3>
								<div class="mb-3">
									<label for="employee" class="form-label">Staff Member *</label>
									<select class="form-select" id="employee" name="employee_id" required>
										<option value="">Select a staff member</option>
										<?php
										// Get all employees
										$employees = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}booking_employees ORDER BY name ASC");

										foreach ($employees as $employee) {
											echo '<option value="' . esc_attr($employee->id) . '">' . esc_html($employee->name) . '</option>';
										}
										?>
									</select>
								</div>
							</div>

							<!-- Date and Time Selection -->
							<div class="mb-4">
								<h3>Select Date & Time</h3>
								<div class="mb-3">
									<label for="booking_date" class="form-label">Date *</label>
									<input type="date" class="form-control" id="booking_date" name="booking_date"
									       min="<?php echo date('Y-m-d'); ?>" required>
								</div>
								<div class="mb-3">
									<label for="booking_time" class="form-label">Time *</label>
									<select class="form-select" id="booking_time" name="booking_time" required>
										<option value="">Select a time</option>
										<?php
										// Generate time slots from 9 AM to 5 PM with 30-minute intervals
										$start = 9 * 60; // 9 AM in minutes
										$end = 17 * 60; // 5 PM in minutes
										$interval = 30; // 30-minute intervals

										for ($time = $start; $time < $end; $time += $interval) {
											$hour = floor($time / 60);
											$minute = $time % 60;
											$am_pm = $hour >= 12 ? 'PM' : 'AM';
											$hour_12 = $hour > 12 ? $hour - 12 : ($hour === 0 ? 12 : $hour);

											$time_value = sprintf('%02d:%02d', $hour, $minute);
											$time_display = sprintf('%d:%02d %s', $hour_12, $minute, $am_pm);

											echo '<option value="' . esc_attr($time_value) . '">' . esc_html($time_display) . '</option>';
										}
										?>
									</select>
								</div>
							</div>

							<!-- Additional Notes -->
							<div class="mb-4">
								<label for="notes" class="form-label">Special Requests or Notes</label>
								<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
							</div>

							<!-- Submit Button -->
							<div class="text-center">
								<button type="submit" name="submit_booking" class="btn btn-primary btn-lg">Book Appointment</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<?php
		// Process the booking form submission
		if (isset($_POST['submit_booking'])) {
			// Sanitize input
			$customer_name = sanitize_text_field($_POST['customer_name']);
			$customer_email = sanitize_email($_POST['customer_email']);
			$customer_phone = sanitize_text_field($_POST['customer_phone']);
			$service_id = intval($_POST['service_id']);
			$employee_id = intval($_POST['employee_id']);
			$booking_date = sanitize_text_field($_POST['booking_date']);
			$booking_time = sanitize_text_field($_POST['booking_time']);
			$notes = sanitize_textarea_field($_POST['notes']);

			// First, store or get the customer
			$customer = $wpdb->get_row($wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}booking_customers WHERE email = %s LIMIT 1",
				$customer_email
			));

			if (!$customer) {
				// Create new customer
				$wpdb->insert(
					$wpdb->prefix . 'booking_customers',
					array(
						'name' => $customer_name,
						'email' => $customer_email,
						'phone' => $customer_phone,
						'created_at' => current_time('mysql')
					)
				);
				$customer_id = $wpdb->insert_id;
			} else {
				$customer_id = $customer->id;
			}

			// Create the booking
			$booking_result = $wpdb->insert(
				$wpdb->prefix . 'bookings',
				array(
					'customer_id' => $customer_id,
					'service_id' => $service_id,
					'employee_id' => $employee_id,
					'booking_date' => $booking_date,
					'booking_time' => $booking_time,
					'notes' => $notes,
					'status' => 'pending',
					'created_at' => current_time('mysql')
				)
			);

			if ($booking_result) {
				echo '<div class="alert alert-success mt-4 text-center">Your appointment has been booked successfully! We will confirm shortly.</div>';
			} else {
				echo '<div class="alert alert-danger mt-4 text-center">There was an error booking your appointment. Please try again.</div>';
			}
		}
		?>
	</main>

<?php get_footer(); ?>