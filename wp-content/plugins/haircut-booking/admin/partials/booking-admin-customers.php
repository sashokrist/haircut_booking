<?php
/**
 * Provide an admin area view for managing customers
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

	<!-- Add New Customer Form -->
	<div class="booking-admin-container">
		<h2>Add New Customer</h2>
		<form method="post" action="">
			<?php wp_nonce_field('save_booking_customer', 'booking_customer_nonce'); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="customer_name">Name</label></th>
					<td><input type="text" id="customer_name" name="customer_name" class="regular-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="customer_email">Email</label></th>
					<td><input type="email" id="customer_email" name="customer_email" class="regular-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="customer_phone">Phone</label></th>
					<td><input type="text" id="customer_phone" name="customer_phone" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="customer_address">Address</label></th>
					<td><textarea id="customer_address" name="customer_address" rows="3" cols="50"></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="customer_notes">Notes</label></th>
					<td><textarea id="customer_notes" name="customer_notes" rows="3" cols="50"></textarea></td>
				</tr>
			</table>
			<?php submit_button('Add Customer'); ?>
		</form>
	</div>

	<!-- Customers List -->
	<div class="booking-list-container">
		<h2>Customers</h2>
		<?php
		// Your code to display customers
		global $wpdb;
		$customers_table = $wpdb->prefix . 'booking_customers';

		// Check if table exists before querying
		if($wpdb->get_var("SHOW TABLES LIKE '$customers_table'") == $customers_table) {
			$customers = $wpdb->get_results("SELECT * FROM $customers_table ORDER BY name ASC");

			if($customers) {
				// Display customers table
				?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Appointments</th>
						<th>Actions</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($customers as $customer): ?>
						<tr>
							<td><?php echo esc_html($customer->id); ?></td>
							<td><?php echo esc_html($customer->name); ?></td>
							<td><?php echo esc_html($customer->email); ?></td>
							<td><?php echo esc_html($customer->phone); ?></td>
							<td>
								<?php
								// Count appointments for this customer
								$appointments_table = $wpdb->prefix . 'booking_appointments';
								if($wpdb->get_var("SHOW TABLES LIKE '$appointments_table'") == $appointments_table) {
									$count = $wpdb->get_var($wpdb->prepare(
										"SELECT COUNT(*) FROM $appointments_table WHERE customer_id = %d",
										$customer->id
									));
									echo '<a href="?page=booking&customer_id=' . esc_attr($customer->id) . '">' .
									     esc_html($count) . ' ' . _n('appointment', 'appointments', $count) .
									     '</a>';
								}
								?>
							</td>
							<td>
								<a href="?page=booking-customers&action=edit&id=<?php echo esc_attr($customer->id); ?>" class="button action">Edit</a>
								<a href="?page=booking-customers&action=delete&id=<?php echo esc_attr($customer->id); ?>" class="button action" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php
			} else {
				echo '<p>No customers found. Add your first customer using the form above.</p>';
			}
		} else {
			echo '<p>Customers table not found. Please check plugin installation.</p>';
		}
		?>
	</div>
</div>