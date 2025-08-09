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

$editing = false;
$customer = null;

// Check if we're in edit mode
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
	$customer_id = intval($_GET['id']);
	$customer = Booking_Customer::get_by_id($customer_id);
	if ($customer) {
		$editing = true;
	}
}

// Customer form
?>
    <div class="wrap">
        <h1><?php echo $editing ? 'Edit Customer' : 'Add New Customer'; ?></h1>

        <form method="post" action="">
			<?php if ($editing): ?>
                <input type="hidden" name="customer_id" value="<?php echo $customer['id'];
                ?>">
			<?php endif; ?>

            <table class="form-table">
                <tr>
                    <th><label for="name">Name</label></th>
                    <td>
                        <input type="text" name="name" id="name" class="regular-text"
                               value="<?php echo $editing ? esc_attr($customer['name']) : ''; ?>" required>
                    </td>
                </tr>
                <tr>
                    <th><label for="email">Email</label></th>
                    <td>
                        <input type="email" name="email" id="email" class="regular-text"
                               value="<?php echo $editing ? esc_attr($customer['email']) : ''; ?>" required>
                    </td>
                </tr>
                <tr>
                    <th><label for="phone">Phone</label></th>
                    <td>
                        <input type="text" name="phone" id="phone" class="regular-text"
                               value="<?php echo $editing ? esc_attr($customer['phone']) : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="address">Address</label></th>
                    <td>
                    <textarea name="address" id="address" class="large-text" rows="3"><?php
	                    echo $editing ? esc_textarea($customer['address']) : '';
	                    ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label for="notes">Notes</label></th>
                    <td>
                    <textarea name="notes" id="notes" class="large-text" rows="3"><?php
	                    echo $editing ? esc_textarea($customer['notes']) : '';
	                    ?></textarea>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="submit_customer" class="button button-primary"
                       value="<?php echo $editing ? 'Update Customer' : 'Add Customer'; ?>">
            </p>
        </form>

        <hr>

        <h2>All Customers</h2>

		<?php
		// Display customers list
		$customers = Booking_Customer::get_all();

		if (empty($customers)) {
			echo '<p>No customers found.</p>';
		} else {
			?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ($customers as $cust): ?>
                    <tr>
                        <td><?php echo esc_html($cust->id ?? ''); ?></td>
                        <td><?php echo esc_html($cust->name ?? ''); ?></td>
                        <td><?php echo esc_html($cust->email ?? ''); ?></td>
                        <td><?php echo esc_html($cust->phone ?? ''); ?></td>
                        <td><?php echo esc_html($cust->address ?? ''); ?></td>
                        <td><?php echo esc_html($cust->notes ?? ''); ?></td>
                        <td>
                            <a href="?page=booking-customers&action=edit&id=<?php echo $cust->id; ?>"
                               class="button button-small">Edit</a>
                            <a href="?page=booking-customers&action=delete&id=<?php echo $cust->id; ?>"
                               class="button button-small"
                               onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>
			<?php
		}
		?>
    </div>
<?php