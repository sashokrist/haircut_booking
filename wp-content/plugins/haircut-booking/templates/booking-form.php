<?php
/**
 * Booking form template
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Booking
 * @subpackage Booking/templates
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
?>

<div class="booking-form-container">
	<h2><?php _e('Book Your Appointment', 'booking'); ?></h2>

	<form id="booking-form" class="booking-form">
		<div class="booking-message"></div>

		<div class="form-row">
			<div class="form-group">
				<label for="service_id"><?php _e('Service', 'booking'); ?></label>
				<select id="service_id" name="service_id" required>
					<option value=""><?php _e('Select a service', 'booking'); ?></option>
					<?php foreach ($services as $service) : ?>
						<option value="<?php echo esc_attr($service['id']); ?>" data-price="<?php echo esc_attr($service['price']); ?>">
							<?php echo esc_html($service['name']); ?> - $<?php echo esc_html($service['price']); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="form-group">
				<label for="employee_id"><?php _e('Employee', 'booking'); ?></label>
				<select id="employee_id" name="employee_id" required>
					<option value=""><?php _e('Select an employee', 'booking'); ?></option>
					<?php foreach ($employees as $employee) : ?>
						<option value="<?php echo esc_attr($employee['id']); ?>">
							<?php echo esc_html($employee['name']); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group">
				<label for="appointment_date"><?php _e('Date', 'booking'); ?></label>
				<input type="text" id="appointment_date" name="appointment_date" class="datepicker" required>
			</div>

			<div class="form-group">
				<label for="appointment_time"><?php _e('Time', 'booking'); ?></label>
				<select id="appointment_time" name="appointment_time" required disabled>
					<option value=""><?php _e('Select date first', 'booking'); ?></option>
				</select>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group">
				<label for="customer_name"><?php _e('Your Name', 'booking'); ?></label>
				<input type="text" id="customer_name" name="customer_name" required>
			</div>

			<div class="form-group">
				<label for="customer_email"><?php _e('Email', 'booking'); ?></label>
				<input type="email" id="customer_email" name="customer_email" required>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group">
				<label for="customer_phone"><?php _e('Phone', 'booking'); ?></label>
				<input type="tel" id="customer_phone" name="customer_phone" required>
			</div>

			<div class="form-group">
				<label for="notes"><?php _e('Special Requests/Notes', 'booking'); ?></label>
				<textarea id="notes" name="notes" rows="3"></textarea>
			</div>
		</div>

		<div class="form-row">
			<div class="booking-summary">
				<h3><?php _e('Booking Summary', 'booking'); ?></h3>
				<div class="summary-row">
					<span class="summary-label"><?php _e('Service:', 'booking'); ?></span>
					<span class="summary-value service-name">-</span>
				</div>
				<div class="summary-row">
					<span class="summary-label"><?php _e('Employee:', 'booking'); ?></span>
					<span class="summary-value employee-name">-</span>
				</div>
				<div class="summary-row">
					<span class="summary-label"><?php _e('Date & Time:', 'booking'); ?></span>
					<span class="summary-value date-time">-</span>
				</div>
				<div class="summary-row">
					<span class="summary-label"><?php _e('Price:', 'booking'); ?></span>
					<span class="summary-value price">-</span>
				</div>
			</div>
		</div>

		<div class="form-row">
			<button type="submit" class="booking-submit-btn"><?php _e('Book Appointment', 'booking'); ?></button>
		</div>

		<?php wp_nonce_field('booking_public_nonce', 'booking_nonce'); ?>
	</form>
</div>

<script>
    jQuery(document).ready(function($) {
        // Initialize datepicker
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0,
            beforeShowDay: function(date) {
                // Disable Sundays (0 = Sunday)
                return [date.getDay() !== 0, ''];
            }
        });

        // Update booking summary
        function updateSummary() {
            var service = $('#service_id option:selected');
            var employee = $('#employee_id option:selected');
            var date = $('#appointment_date').val();
            var time = $('#appointment_time').val();

            $('.summary-value.service-name').text(service.text() || '-');
            $('.summary-value.employee-name').text(employee.text() || '-');
            $('.summary-value.date-time').text((date && time) ? date + ' ' + time : '-');
            $('.summary-value.price').text(service.data('price') ? '$' + service.data('price') : '-');
        }

        // Form field change events
        $('#service_id, #employee_id, #appointment_date, #appointment_time').on('change', function() {
            updateSummary();

            // If date and employee are selected, get available times
            if ($('#appointment_date').val() && $('#employee_id').val() && $('#service_id').val()) {
                getAvailableTimes();
            }
        });

        // Get available times
        function getAvailableTimes() {
            $.ajax({
                url: booking_public_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_available_times',
                    nonce: booking_public_ajax.nonce,
                    employee_id: $('#employee_id').val(),
                    date: $('#appointment_date').val(),
                    service_id: $('#service_id').val()
                },
                beforeSend: function() {
                    $('#appointment_time').prop('disabled', true);
                    $('#appointment_time').html('<option value=""><?php _e('Loading...', 'booking'); ?></option>');
                },
                success: function(response) {
                    if (response.success) {
                        $('#appointment_time').prop('disabled', false);

                        var options = '<option value=""><?php _e('Select a time', 'booking'); ?></option>';

                        if (response.data.times.length > 0) {
                            $.each(response.data.times, function(index, time) {
                                options += '<option value="' + time + '">' + time + '</option>';
                            });
                        } else {
                            options = '<option value=""><?php _e('No available times', 'booking'); ?></option>';
                        }

                        $('#appointment_time').html(options);
                    }
                },
                error: function() {
                    $('#appointment_time').prop('disabled', true);
                    $('#appointment_time').html('<option value=""><?php _e('Error loading times', 'booking'); ?></option>');
                }
            });
        }

        // Form submission
        $('#booking-form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: booking_public_ajax.ajax_url,
                type: 'POST',
                data: formData + '&action=submit_booking&nonce=' + booking_public_ajax.nonce,
                beforeSend: function() {
                    form.find('.booking-message').html('<div class="message info"><?php _e('Processing your booking...', 'booking'); ?></div>');
                    form.find('button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        form.find('.booking-message').html('<div class="message success"><?php _e('Booking successful! We will contact you to confirm your appointment.', 'booking'); ?></div>');
                        form[0].reset();
                        $('.summary-value').text('-');
                    } else {
                        form.find('.booking-message').html('<div class="message error">' + response.data + '</div>');
                    }
                    form.find('button[type="submit"]').prop('disabled', false);
                },
                error: function() {
                    form.find('.booking-message').html('<div class="message error"><?php _e('An error occurred. Please try again.', 'booking'); ?></div>');
                    form.find('button[type="submit"]').prop('disabled', false);
                }
            });
        });
    });
</script>