<?php
/**
 * Provide an admin area view for plugin settings
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

// Get existing options with defaults
$booking_options = get_option('booking_settings', array(
	'business_name' => '',
	'business_email' => '',
	'business_phone' => '',
	'business_address' => '',
	'business_hours' => array(
		'monday' => array('start' => '09:00', 'end' => '17:00', 'closed' => false),
		'tuesday' => array('start' => '09:00', 'end' => '17:00', 'closed' => false),
		'wednesday' => array('start' => '09:00', 'end' => '17:00', 'closed' => false),
		'thursday' => array('start' => '09:00', 'end' => '17:00', 'closed' => false),
		'friday' => array('start' => '09:00', 'end' => '17:00', 'closed' => false),
		'saturday' => array('start' => '10:00', 'end' => '16:00', 'closed' => false),
		'sunday' => array('start' => '10:00', 'end' => '16:00', 'closed' => true),
	),
	'time_slot_duration' => 30,
	'minimum_notice' => 24,
	'maximum_days_advance' => 60,
	'confirmation_email' => true,
	'reminder_email' => true,
	'reminder_hours' => 24,
	'cancellation_policy' => '',
	'google_api_key' => '',
));
?>

<div class="wrap">
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>

	<?php
	// Show settings saved message
	if (isset($_GET['settings-updated'])) {
		echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully!</p></div>';
	}
	?>

	<form method="post" action="options.php">
		<?php
		settings_fields('booking_settings_group');
		do_settings_sections('booking-settings');
		?>

		<div class="booking-settings-container">
			<h2 class="nav-tab-wrapper">
				<a href="#general-settings" class="nav-tab nav-tab-active">General</a>
				<a href="#business-hours" class="nav-tab">Business Hours</a>
				<a href="#notification-settings" class="nav-tab">Notifications</a>
				<a href="#advanced-settings" class="nav-tab">Advanced</a>
			</h2>

			<!-- General Settings Tab -->
			<div id="general-settings" class="tab-content active">
				<h2>Business Information</h2>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="business_name">Business Name</label></th>
						<td>
							<input type="text" id="business_name" name="booking_settings[business_name]"
							       value="<?php echo esc_attr($booking_options['business_name']); ?>" class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="business_email">Email Address</label></th>
						<td>
							<input type="email" id="business_email" name="booking_settings[business_email]"
							       value="<?php echo esc_attr($booking_options['business_email']); ?>" class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="business_phone">Phone Number</label></th>
						<td>
							<input type="text" id="business_phone" name="booking_settings[business_phone]"
							       value="<?php echo esc_attr($booking_options['business_phone']); ?>" class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="business_address">Address</label></th>
						<td>
                            <textarea id="business_address" name="booking_settings[business_address]"
                                      rows="3" class="large-text"><?php echo esc_textarea($booking_options['business_address']); ?></textarea>
						</td>
					</tr>
				</table>

				<h2>Appointment Settings</h2>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="time_slot_duration">Time Slot Duration (minutes)</label></th>
						<td>
							<select id="time_slot_duration" name="booking_settings[time_slot_duration]">
								<?php
								$durations = array(10, 15, 20, 30, 45, 60, 90, 120);
								foreach ($durations as $duration) {
									echo '<option value="' . esc_attr($duration) . '" ' .
									     selected($booking_options['time_slot_duration'], $duration, false) . '>' .
									     esc_html($duration) . ' minutes</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="minimum_notice">Minimum Notice Required (hours)</label></th>
						<td>
							<input type="number" id="minimum_notice" name="booking_settings[minimum_notice]"
							       value="<?php echo esc_attr($booking_options['minimum_notice']); ?>" min="0" max="168" step="1">
							<p class="description">Minimum hours in advance a customer must book an appointment.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="maximum_days_advance">Maximum Days in Advance</label></th>
						<td>
							<input type="number" id="maximum_days_advance" name="booking_settings[maximum_days_advance]"
							       value="<?php echo esc_attr($booking_options['maximum_days_advance']); ?>" min="1" max="365" step="1">
							<p class="description">How many days in advance customers can book appointments.</p>
						</td>
					</tr>
				</table>
			</div>

			<!-- Business Hours Tab -->
			<div id="business-hours" class="tab-content" style="display:none;">
				<h2>Business Hours</h2>
				<p>Set your regular business hours. You can override these for specific dates in the calendar.</p>

				<table class="form-table business-hours-table">
					<tr>
						<th>Day</th>
						<th>Open</th>
						<th>Close</th>
						<th>Closed</th>
					</tr>
					<?php
					$days = array(
						'monday' => 'Monday',
						'tuesday' => 'Tuesday',
						'wednesday' => 'Wednesday',
						'thursday' => 'Thursday',
						'friday' => 'Friday',
						'saturday' => 'Saturday',
						'sunday' => 'Sunday'
					);

					foreach ($days as $day_key => $day_label) :
						$hours = $booking_options['business_hours'][$day_key];
						?>
						<tr>
							<td><?php echo esc_html($day_label); ?></td>
							<td>
								<input type="time"
								       name="booking_settings[business_hours][<?php echo esc_attr($day_key); ?>][start]"
								       value="<?php echo esc_attr($hours['start']); ?>"
									<?php echo !empty($hours['closed']) ? 'disabled' : ''; ?>>
							</td>
							<td>
								<input type="time"
								       name="booking_settings[business_hours][<?php echo esc_attr($day_key); ?>][end]"
								       value="<?php echo esc_attr($hours['end']); ?>"
									<?php echo !empty($hours['closed']) ? 'disabled' : ''; ?>>
							</td>
							<td>
								<input type="checkbox"
								       name="booking_settings[business_hours][<?php echo esc_attr($day_key); ?>][closed]"
								       value="1"
									<?php checked(!empty($hours['closed']), true); ?>
									   class="business-day-closed">
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>

			<!-- Notification Settings Tab -->
			<div id="notification-settings" class="tab-content" style="display:none;">
				<h2>Email Notifications</h2>
				<table class="form-table">
					<tr>
						<th scope="row">Confirmation Emails</th>
						<td>
							<label>
								<input type="checkbox" name="booking_settings[confirmation_email]" value="1"
									<?php checked($booking_options['confirmation_email'], true); ?>>
								Send confirmation emails to customers when appointments are booked
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">Reminder Emails</th>
						<td>
							<label>
								<input type="checkbox" name="booking_settings[reminder_email]" value="1"
									<?php checked($booking_options['reminder_email'], true); ?>>
								Send reminder emails to customers
							</label>

							<div id="reminder-settings" <?php echo empty($booking_options['reminder_email']) ? 'style="display:none;"' : ''; ?>>
								<label for="reminder_hours">Hours before appointment:</label>
								<input type="number" id="reminder_hours" name="booking_settings[reminder_hours]"
								       value="<?php echo esc_attr($booking_options['reminder_hours']); ?>" min="1" max="72" step="1">
							</div>
						</td>
					</tr>
				</table>

				<h2>Cancellation Policy</h2>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="cancellation_policy">Cancellation Policy</label></th>
						<td>
                            <textarea id="cancellation_policy" name="booking_settings[cancellation_policy]"
                                      rows="5" class="large-text"><?php echo esc_textarea($booking_options['cancellation_policy']); ?></textarea>
							<p class="description">This will be displayed to customers during the booking process.</p>
						</td>
					</tr>
				</table>
			</div>

			<!-- Advanced Settings Tab -->
			<div id="advanced-settings" class="tab-content" style="display:none;">
				<h2>Integration Settings</h2>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="google_api_key">Google Maps API Key</label></th>
						<td>
							<input type="text" id="google_api_key" name="booking_settings[google_api_key]"
							       value="<?php echo esc_attr($booking_options['google_api_key']); ?>" class="regular-text">
							<p class="description">Required for location features and maps on the front-end.</p>
						</td>
					</tr>
				</table>

				<h2>Database Management</h2>
				<table class="form-table">
					<tr>
						<th scope="row">Database Tools</th>
						<td>
							<a href="?page=booking-settings&action=export_data" class="button">Export Data</a>
							<p class="description">Export all booking data as CSV files.</p>

							<a href="?page=booking-settings&action=clear_data" class="button"
							   onclick="return confirm('WARNING: This will permanently delete all booking data. This action cannot be undone. Are you sure you want to proceed?');">
								Clear All Data
							</a>
							<p class="description">WARNING: This will delete all booking data from the database.</p>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<?php submit_button('Save Settings'); ?>
	</form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();

            // Hide all tab contents
            $('.tab-content').hide();

            // Remove active class from all tabs
            $('.nav-tab').removeClass('nav-tab-active');

            // Add active class to clicked tab
            $(this).addClass('nav-tab-active');

            // Show the corresponding tab content
            $($(this).attr('href')).show();
        });

        // Business hours closed checkbox
        $('.business-day-closed').on('change', function() {
            var timeInputs = $(this).closest('tr').find('input[type="time"]');
            if ($(this).is(':checked')) {
                timeInputs.prop('disabled', true);
            } else {
                timeInputs.prop('disabled', false);
            }
        });

        // Toggle reminder settings visibility
        $('input[name="booking_settings[reminder_email]"]').on('change', function() {
            if ($(this).is(':checked')) {
                $('#reminder-settings').show();
            } else {
                $('#reminder-settings').hide();
            }
        });
    });
</script>