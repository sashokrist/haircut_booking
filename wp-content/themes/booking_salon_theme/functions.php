<?php
function salon_theme_enqueue_scripts() {
    // Bootstrap 5 CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

    // Custom CSS
    wp_enqueue_style('salon-style', get_stylesheet_uri());
    wp_enqueue_style('salon-custom', get_template_directory_uri() . '/assets/css/custom.css');

    // Bootstrap 5 JS
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'salon_theme_enqueue_scripts');

// Enable menus
add_theme_support('menus');