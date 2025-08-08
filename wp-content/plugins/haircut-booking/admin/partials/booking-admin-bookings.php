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
?>

<div class="wrap">
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>

	<!-- Add your bookings management interface here -->
	<div class="booking-admin-container">
		<!-- Bookings list, filters, pagination, etc. -->
		<div class="booking-list-container">
			<?php
			// Your code to display bookings
			global $wpdb;
			$bookings_table = $wpdb->prefix . 'bookings';

			// Check if table exists before querying
			if($wpdb->get_var("SHOW TABLES LIKE '$bookings_table'") == $bookings_table) {
				$bookings = $wpdb->get_results("SELECT * FROM $bookings_table ORDER BY booking_date DESC");

				if($bookings) {
					// Display bookings table
					?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
						<tr>
							<th>ID</th>
							<th>Customer</th>
							<th>Service</th>
							<th>Employee</th>
							<th>Date & Time</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach($bookings as $booking): ?>
							<tr>
								<td><?php echo esc_html($booking->id); ?></td>
								<td><?php echo esc_html($booking->customer_id); ?></td>
								<td><?php echo esc_html($booking->service_id); ?></td>
								<td><?php echo esc_html($booking->employee_id); ?></td>
								<td><?php echo esc_html($booking->booking_time); ?></td>
								<td><?php echo esc_html($booking->status); ?></td>
								<td>
									<a href="#" class="button action">Edit</a>
									<a href="#" class="button action">Delete</a>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<?php
				} else {
					echo '<p>No bookings found.</p>';
				}
			} else {
				echo '<p>Bookings table not found. Please check plugin installation.</p>';
			}
			?>
		</div>
	</div>
</div>