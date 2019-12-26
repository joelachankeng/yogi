<?php
// Variables
$default_primary_color = noo_default_primary_color();

$default_header_bg_color = noo_default_header_bg();
$default_nav_font_size = noo_default_font_size();
$default_font_color = noo_default_text_color();
$default_font = noo_default_font_family();
$default_logo_font_color = noo_default_logo_color();

$noo_header_bg_color = noo_get_option( 'noo_header_bg_color', $default_header_bg_color );

$noo_header_nav_position = noo_get_option( 'noo_header_nav_position', 'fixed_top' );

$noo_header_nav_icon_cart   = noo_get_option( 'noo_header_nav_icon_cart', false );

$noo_header_custom_nav_font = noo_get_option( 'noo_header_custom_nav_font', false );

$noo_header_nav_font = $noo_header_custom_nav_font ? noo_get_option( 'noo_header_nav_font', $default_font ) : $default_font;
$noo_header_nav_font_style = $noo_header_custom_nav_font ? noo_get_option( 'noo_header_nav_font_style', 'normal' ) : 'normal';
$noo_header_nav_font_weight = $noo_header_custom_nav_font ? noo_get_option( 'noo_header_nav_font_weight', 'bold' ) : 'bold';
$noo_header_nav_font_subset = $noo_header_custom_nav_font ? noo_get_option( 'noo_header_nav_font_subset', 'latin' ) : 'latin';
$noo_header_nav_font_size = noo_get_option( 'noo_header_nav_font_size', $default_nav_font_size );
$noo_header_nav_uppercase = noo_get_option( 'noo_header_nav_uppercase', true );

$noo_header_use_image_logo = noo_get_option( 'noo_header_use_image_logo', false );

$noo_header_logo_font = noo_get_option( 'noo_header_logo_font', noo_default_logo_font_family() );
$noo_header_logo_font_size = noo_get_option( 'noo_header_logo_font_size', '30' );
$noo_header_logo_font_color = noo_get_option( 'noo_header_logo_font_color', noo_default_logo_color() );
$noo_header_logo_font_style = noo_get_option( 'noo_header_logo_font_style', 'normal' );
$noo_header_logo_font_weight = noo_get_option( 'noo_header_logo_font_weight', '700' );
$noo_header_logo_font_subset = noo_get_option( 'noo_header_logo_font_subset', 'latin' );
$noo_header_logo_uppercase = noo_get_option( 'noo_header_logo_uppercase', true );

$noo_header_logo_image_height = noo_get_option( 'noo_header_logo_image_height', '' );

$noo_header_nav_height = noo_get_option( 'noo_header_nav_height', '' );
$noo_header_nav_link_spacing = noo_get_option( 'noo_header_nav_link_spacing', '' );

$noo_header_nav_toggle_size = noo_get_option( 'noo_header_nav_toggle_size', '' );

?>

/* Header */
/* ====================== */
<?php if ( $noo_header_bg_color != '' ) : ?>
<!-- .noo-header {
	background-color: <?php echo esc_html($noo_header_bg_color); ?>;
} -->
<?php endif; ?>

/* Navigation Typography */
/* ====================== */

/* NavBar: Typo */
.navbar-nav li > a {
	<?php if($noo_header_custom_nav_font ):?>
		font-family: "<?php echo esc_html($noo_header_nav_font); ?>", sans-serif;
		font-size: <?php echo esc_html($noo_header_nav_font_size) . 'px'; ?>;
	<?php endif;?>
}

.navbar-nav > li > a {
	<?php if($noo_header_custom_nav_font ):?>
	font-style: <?php echo esc_html($noo_header_nav_font_style); ?>;
	font-weight: <?php echo esc_html($noo_header_nav_font_weight); ?>;
	<?php endif; ?>
	<?php if ( !empty( $noo_header_nav_uppercase ) ) : ?>
		text-transform: uppercase;
	<?php else : ?>
		text-transform: none;
	<?php endif; ?>
}
<?php if($noo_header_nav_height != '') :?>
	.navbar {
		min-height: <?php echo esc_html($noo_header_nav_height) . 'px'; ?>;
	}
<?php endif;?>

<?php if ( $noo_header_nav_position == 'fixed_top' || $noo_header_nav_position == 'static_top' ) : ?>
@media (min-width: 992px) {
	<?php if($noo_header_nav_link_spacing != '') :?>
	.navbar-nav > li > a {
		padding-left: <?php echo esc_html($noo_header_nav_link_spacing) . 'px'; ?>;
		padding-right: <?php echo esc_html($noo_header_nav_link_spacing) . 'px'; ?>;
	}
	<?php endif;?>
	<?php if($noo_header_nav_height != '') :?>
		.navbar:not(.navbar-shrink) .navbar-nav > li > a {
			line-height: <?php echo esc_html($noo_header_nav_height) . 'px'; ?>;
		}
	<?php endif;?>
}
	<?php if($noo_header_nav_height != '') :?>
		.navbar-toggle {
			height: <?php echo esc_html($noo_header_nav_height) . 'px'; ?>;
		}
	<?php endif;?>
	<?php if($noo_header_nav_height != '') :?>
		.navbar:not(.navbar-shrink) .navbar-brand {
			line-height: <?php echo esc_html($noo_header_nav_height) . 'px'; ?>;
			height: <?php echo esc_html($noo_header_nav_height) . 'px'; ?>;
		}
	<?php endif;?>
<?php endif; ?>

/* Logo */
/* ====================== */
<?php if( ! $noo_header_use_image_logo ) : ?>
.navbar-brand{
	color: <?php echo esc_html($noo_header_logo_font_color); ?>;
	font-family: "<?php echo esc_html($noo_header_logo_font); ?>", "Open Sans", sans-serif;

	<?php if($noo_header_logo_font_size != '') :?>
		font-size: <?php echo esc_html($noo_header_logo_font_size) . 'px'; ?>;
	<?php endif; ?>

	font-style: <?php echo esc_html($noo_header_logo_font_style); ?>;
	font-weight: <?php echo esc_html($noo_header_logo_font_weight); ?>;

	<?php if ( !empty( $noo_header_logo_uppercase ) ) : ?>
		text-transform: uppercase;
	<?php endif; ?>
}
.navbar-brand,.navbar-brand:hover { color: <?php echo esc_html($noo_header_logo_font_color); ?>; }
<?php else : ?>
	<?php if($noo_header_logo_image_height != '') : ?>
		.navbar-brand .noo-logo-img,
		.navbar-brand .noo-logo-retina-img {
			height: <?php echo esc_html($noo_header_logo_image_height) . 'px'; ?>;
		}
	<?php endif; ?>
<?php endif; ?>

/* Mobile Icons */
/* ====================== */
<?php if($noo_header_nav_toggle_size != '') : ?>
	.navbar-toggle{
		font-size: <?php echo esc_html($noo_header_nav_toggle_size) . 'px'; ?>;
	}
<?php endif; ?>