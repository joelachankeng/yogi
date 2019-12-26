<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<!-- Favicon-->
<?php 
	$favicon = noo_get_image_option('noo_custom_favicon', '');
	if ($favicon != ''): ?>
	<link rel="shortcut icon" href="<?php echo esc_url($favicon); ?>" />
<?php
endif; ?>
<!--[if lt IE 9]>
<script src="<?php echo NOO_FRAMEWORK_URI . '/vendor/respond.min.js'; ?>"></script>
<![endif]-->
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<div class="site">

	<?php
	$rev_slider_pos = home_slider_position(); ?>
	<?php
		if($rev_slider_pos == 'above') {
			noo_get_layout( 'slider-revolution');
		}
	?>
	<header class="noo-header <?php noo_header_class(); ?>" id="noo-header" role="banner">
		<?php noo_get_layout('topbar'); ?>
		<?php noo_get_layout('navbar'); ?>
	</header>

	<?php
		if($rev_slider_pos == 'below') {
			noo_get_layout( 'slider-revolution');
		}
	?>

	<?php noo_get_layout('heading'); ?>