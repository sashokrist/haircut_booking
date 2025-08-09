<?php
/**
 * Provide a admin area view for managing services
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
	<h1>Услуги</h1>

	<!-- Add New Service Form -->
	<!-- Add New Service Form -->
	<div class="booking-admin-container">
		<h2>Всички услуги</h2>
		<form method="post" action="">
			<?php wp_nonce_field('add_service_nonce'); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="service_name">Service Name</label></th>
					<td><input type="text" id="service_name" name="service_name" class="regular-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="service_description">Description</label></th>
					<td><textarea id="service_description" name="service_description" rows="5" cols="50"></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="service_duration">Duration (minutes)</label></th>
					<td><input type="number" id="service_duration" name="service_duration" min="5" step="5" value="60" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="service_price">Price</label></th>
					<td><input type="number" id="service_price" name="service_price" min="0" step="0.01" required></td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="add_service" class="button button-primary" value="Добави услуга">
			</p>
		</form>
	</div>

	<!-- Services List -->
	<div class="booking-list-container">
		<h2>Услуги</h2>
		<?php
		// Your code to display services
		global $wpdb;
		$services_table = $wpdb->prefix . 'booking_services';

		// Check if table exists before querying
		if($wpdb->get_var("SHOW TABLES LIKE '$services_table'") == $services_table) {
			$services = $wpdb->get_results("SELECT * FROM $services_table ORDER BY name ASC");

			if($services) {
				// Display services table
				?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Description</th>
						<th>Duration</th>
						<th>Price</th>
						<th>Actions</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($services as $service): ?>
						<tr>
							<td><?php echo esc_html($service->id); ?></td>
							<td><?php echo esc_html($service->name); ?></td>
							<td><?php echo wp_trim_words(esc_html($service->description), 10); ?></td>
							<td><?php echo esc_html($service->duration) . ' min'; ?></td>
							<td><?php echo esc_html(number_format($service->price, 2)); ?></td>
							<td>
								<a href="?page=booking-services&action=edit&id=<?php echo esc_attr($service->id); ?>" class="button action">Edit</a>
								<a href="?page=booking-services&action=delete&id=<?php echo esc_attr($service->id); ?>" class="button action" onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php
			} else {
				echo '<p>No services found. Add your first service using the form above.</p>';
			}
		} else {
			echo '<p>Services table not found. Please check plugin installation.</p>';
		}
		?>
	</div>
</div>