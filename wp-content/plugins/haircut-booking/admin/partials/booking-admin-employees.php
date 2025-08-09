<?php
/**
 * Provide an admin area view for managing employees
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
	<h1>Служители</h1>

	<!-- Add New Employee Form -->
	<div class="booking-admin-container">
		<h2>Добави нов служител</h2>
        <form method="post" action="">
			<?php wp_nonce_field('save_booking_employee', 'booking_employee_nonce'); ?>>

        <?php wp_nonce_field('save_booking_employee', 'booking_employee_nonce'); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="employee_name">Име</label></th>
					<td><input type="text" id="employee_name" name="employee_name" class="regular-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="employee_email">Имейл</label></th>
					<td><input type="email" id="employee_email" name="employee_email" class="regular-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="employee_phone">Телефон</label></th>
					<td><input type="text" id="employee_phone" name="employee_phone" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="employee_services">Услуги</label></th>
					<td>
						<?php
						// Fetch available services
						global $wpdb;
						$services_table = $wpdb->prefix . 'booking_services';

						if($wpdb->get_var("SHOW TABLES LIKE '$services_table'") == $services_table) {
							$services = $wpdb->get_results("SELECT * FROM $services_table ORDER BY name ASC");

							if($services) {
								echo '<div class="services-checkboxes">';
								foreach($services as $service) {
									echo '<label>';
									echo '<input type="checkbox" name="employee_services[]" value="' . esc_attr($service->id) . '">';
									echo esc_html($service->name);
									echo '</label><br>';
								}
								echo '</div>';
							} else {
								echo '<p>No services available. <a href="?page=booking-services">Add services first</a>.</p>';
							}
						}
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="employee_bio">Bio/Description</label></th>
					<td><textarea id="employee_bio" name="employee_bio" rows="5" cols="50"></textarea></td>
				</tr>
			</table>
	        <?php submit_button('Add Employee', 'primary', 'submit'); ?>
        </form>
	</div>

	<!-- Employees List -->
	<div class="booking-list-container">
		<h2>Служители</h2>
		<?php
		// Your code to display employees
		global $wpdb;
		$employees_table = $wpdb->prefix . 'booking_employees';

		// Check if table exists before querying
		if($wpdb->get_var("SHOW TABLES LIKE '$employees_table'") == $employees_table) {
			$employees = $wpdb->get_results("SELECT * FROM $employees_table ORDER BY name ASC");

			if($employees) {
				// Display employees table
				?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Services</th>
						<th>Actions</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($employees as $employee): ?>
						<tr>
							<td><?php echo esc_html($employee->id); ?></td>
							<td><?php echo esc_html($employee->name); ?></td>
							<td><?php echo esc_html($employee->email); ?></td>
							<td><?php echo esc_html($employee->phone); ?></td>
							<td>
								<?php
								// Display employee services (this would need to be adapted to your database structure)
								if(!empty($employee->services)) {
									$service_ids = maybe_unserialize($employee->services);
									if(is_array($service_ids)) {
										$service_names = array();
										foreach($service_ids as $service_id) {
											$service_name = $wpdb->get_var($wpdb->prepare(
												"SELECT name FROM $services_table WHERE id = %d",
												$service_id
											));
											if($service_name) {
												$service_names[] = $service_name;
											}
										}
										echo esc_html(implode(', ', $service_names));
									}
								}
								?>
							</td>
							<td>
								<a href="?page=booking-employees&action=edit&id=<?php echo esc_attr($employee->id); ?>" class="button action">Edit</a>
								<a href="?page=booking-employees&action=delete&id=<?php echo esc_attr($employee->id); ?>" class="button action" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</a>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php
			} else {
				echo '<p>No employees found. Add your first employee using the form above.</p>';
			}
		} else {
			echo '<p>Employees table not found. Please check plugin installation.</p>';
		}
		?>
	</div>
</div>