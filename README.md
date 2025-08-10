# Haircut Booking — WordPress Booking System

A WordPress site with a custom **booking plugin** and **Bootstrap theme** designed for a hair salon, easily adaptable to other appointment-based businesses (beauty, nails, spa, barbers, clinics, etc.).

The repository contains a full WordPress install plus your custom code under `wp-content/plugins/` and `wp-content/themes/`.

---

## Features

- Admin management for:
    - **Bookings** (date, time, status, notes)
    - **Customers** (name, email, phone, address)
    - **Services** (name, duration, price)
    - **Employees** (name, email, phone, bio)
- Custom **theme** with a modern, responsive landing page and “services” grid
- Clear DB schema with straightforward relations
- Built to be **rebranded** and repurposed for different verticals quickly

---

## Repository Structure

wp-content/
plugins/
haircut-booking/ # Custom booking plugin (admin pages, DB, logic)
themes/
booking_salon_theme/ # Custom Bootstrap theme (landing + pages)

(The repo includes the rest of a standard WordPress install)
yaml
Копиране

---

## Requirements

- PHP ≥ 7.4
- MySQL/MariaDB
- WordPress (the repo already includes a WP tree)
- Web server (Apache/Nginx) with pretty permalinks enabled

---

## Quick Start (Local)

1. **Clone** the repository into your web root:
   ```bash
   git clone https://github.com/sashokrist/haircut_booking.git
Create a database and user, then configure wp-config.php.

Start the site (e.g., http://haircut-booking.test/ via hosts + vhost or http://localhost/...).

Log in to WP Admin and activate:

Theme: Booking Salon Theme

Plugin: Haircut Booking

Visit Bookings in the admin to view/manage records.

If you prefer a clean WP rather than the one in the repo, copy only:

bash
Копиране
wp-content/plugins/haircut-booking
wp-content/themes/booking_salon_theme
into a fresh install.

Database Schema
The plugin uses four tables (prefix wp_ may vary):

sql
Копиране
-- Bookings
CREATE TABLE `wp_bookings` (
`id` int NOT NULL AUTO_INCREMENT,
`customer_id` int NOT NULL,
`employee_id` int NOT NULL,
`customer_name` varchar(255) NOT NULL,
`customer_email` varchar(255) NOT NULL,
`service_id` int NOT NULL,
`booking_date` date NOT NULL,
`booking_time` time NOT NULL,
`status` varchar(50) DEFAULT 'pending',
`notes` text NOT NULL,
`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
);

-- Customers
CREATE TABLE `wp_booking_customers` (
`id` mediumint NOT NULL AUTO_INCREMENT,
`name` varchar(100) NOT NULL,
`email` varchar(100) NOT NULL,
`phone` varchar(20) DEFAULT NULL,
`address` text NOT NULL,
`notes` text NOT NULL,
`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
);

-- Employees
CREATE TABLE `wp_booking_employees` (
`id` mediumint NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL,
`phone` varchar(50) DEFAULT NULL,
`created_at` datetime DEFAULT '0000-00-00 00:00:00',
`bio` text NOT NULL,
`services` text,
PRIMARY KEY (`id`)
);

-- Services
CREATE TABLE `wp_booking_services` (
`id` mediumint NOT NULL AUTO_INCREMENT,
`name` varchar(100) NOT NULL,
`description` text,
`duration` int NOT NULL,
`price` decimal(10,2) NOT NULL,
`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
);
How the Admin List Pages Pull Names
sql
Копиране
SELECT
b.id,
COALESCE(c.name, b.customer_name) AS customer_name,
COALESCE(c.email, b.customer_email) AS customer_email,
COALESCE(s.name, 'Unknown Service') AS service_name,
COALESCE(e.name, 'Unknown Employee') AS employee_name,
CONCAT(b.booking_date, ' ', b.booking_time) AS booking_time,
b.status
FROM wp_bookings b
LEFT JOIN wp_booking_customers c ON b.customer_id = c.id
LEFT JOIN wp_booking_services  s ON b.service_id  = s.id
LEFT JOIN wp_booking_employees e ON b.employee_id = e.id
ORDER BY b.booking_date DESC, b.booking_time DESC;
Theme Notes
Theme folder: booking_salon_theme

Uses Bootstrap classes and an image grid for services.

Images live in:

swift
Копиране
wp-content/themes/booking_salon_theme/assets/images/
Example:

php
Копиране
<img src="<?php echo get_template_directory_uri(); ?>/assets/images/haircut.png" class="img-fluid" alt="Haircut">
Configure for Other Businesses
Rename labels: Services ⇒ Treatments/Consultations/etc.

Seed wp_booking_services with business-specific offerings.

Update theme imagery in assets/images/.

Adjust durations/prices.

Create employee profiles for providers/staff.

Public Booking Form Shortcode
You can add a frontend booking form by adding this to haircut-booking/public/booking-form.php and registering a shortcode:

php
Копиране
function hb_booking_form_shortcode() {
ob_start(); ?>
<form method="post">
<label>Your Name</label>
<input type="text" name="customer_name" required>

        <label>Your Email</label>
        <input type="email" name="customer_email" required>
        
        <label>Service</label>
        <select name="service_id" required>
            <?php
            global $wpdb;
            $services = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}booking_services");
            foreach ($services as $service) {
                echo "<option value='{$service->id}'>" . esc_html($service->name) . "</option>";
            }
            ?>
        </select>
        
        <label>Date</label>
        <input type="date" name="booking_date" required>
        
        <label>Time</label>
        <input type="time" name="booking_time" required>
        
        <button type="submit" name="submit_booking">Book Now</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('booking_form', 'hb_booking_form_shortcode');
Then place [booking_form] in any page.

Troubleshooting
Names show as “Unknown”: check foreign keys & table prefixes.

Images not showing: verify path & extension.

Enable debug mode in wp-config.php for detailed logs.

License
GPL v2 or later (matches WordPress licensing).