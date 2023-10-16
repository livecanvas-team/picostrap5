<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>

	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- wp_head -->
	<?php wp_head(); ?>
	<!-- /wp_head -->
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<?php 
    // Custom filter to check if header elements should be displayed. To disable, use: add_filter('picostrap_enable_header_elements', '__return_false');
    if (apply_filters('picostrap_enable_header_elements', true)):

        //check if LC option is set to "Handle Header"    
        if (!function_exists('lc_custom_header')) {
            //use the built-in theme header elements 
            get_template_part( 'partials/header', 'optional-topbar' ); 
            get_template_part( 'partials/header', 'navbar' );
        } else {
            //use the LiveCanvas Custom Header
            lc_custom_header(); 
        }

    endif;
    
	?>

	<main id='theme-main'>
