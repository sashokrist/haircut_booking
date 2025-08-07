<?php
/**
 * Booking list template
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

<div class="booking-list-container">
	<h2><?php _e('Your Bookings', 'booking'); ?></h2>

	<?php if (empty($bookings)) : ?>
		<p><?php _e('You don\'t have any bookings yet.', 'booking'); ?></p>
	<?php else : ?>
		<table class="booking-list-table">
			<thead>
			<tr>
				<th><?php _e('Service', 'booking'); ?></th>
				<th><?php _e('Employee', 'booking'); ?></th>
				<th><?php _e('Date', 'booking'); ?></th>
				<th><?php _e('Time', 'booking'); ?></th>
				<th><?php _e('Status', 'booking'); ?></th>
				<th><?php _e('Cost', 'booking'); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($bookings as $booking) : ?>
				<tr>
					<td><?php echo esc_html($booking['service_name']); ?></td>
					<td><?php echo esc_html($booking['employee_name']); ?></td>
					<td><?php echo esc_html($booking['booking_date']); ?></td>
					<td><?php echo esc_html($booking['booking_time']); ?></td>
					<td>
                            <span class="booking-status booking-status-<?php echo esc_attr($booking['status']); ?>">
                                <?php echo esc_html(ucfirst($booking['status'])); ?>
                            </span>
					</td>
					<td>$<?php echo esc_html($booking['cost']); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>