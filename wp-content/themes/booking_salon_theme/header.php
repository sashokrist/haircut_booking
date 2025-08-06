<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="bg-dark text-white py-3 mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="h4 mb-0"><?php bloginfo('name'); ?></h1>
        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container' => '',
            'menu_class' => 'nav',
            'items_wrap' => '<ul class="nav">%3$s</ul>',
            'walker' => new class extends Walker_Nav_Menu {
                function start_el(&$output, $item, $depth = 0, $args = [], $id = 0) {
                    $output .= '<li class="nav-item"><a class="nav-link text-white" href="' . $item->url . '">' . $item->title . '</a></li>';
                }
            }
        ]);
        ?>
    </div>
</header>