<?php
// Variables
$noo_typo_use_custom_fonts = noo_get_option( 'noo_typo_use_custom_fonts', false );

$noo_typo_headings_font = $noo_typo_use_custom_fonts ? noo_get_option( 'noo_typo_headings_font', noo_default_headings_font_family() ) : noo_default_headings_font_family();
$noo_typo_headings_font_style = $noo_typo_use_custom_fonts ? noo_get_option( 'noo_typo_headings_font_style', 'normal' ) : 'normal';
$noo_typo_headings_font_weight = $noo_typo_use_custom_fonts ? noo_get_option( 'noo_typo_headings_font_weight', 'bold' ) : 'bold';
$noo_typo_headings_uppercase = noo_get_option( 'noo_typo_headings_uppercase', false );

$noo_typo_body_font_size = noo_get_option( 'noo_typo_body_font_size', noo_default_font_size() );
$noo_typo_body_font = $noo_typo_use_custom_fonts ? noo_get_option( 'noo_typo_body_font', noo_default_font_family() ) : noo_default_font_family(); 
$noo_typo_body_font_style = $noo_typo_use_custom_fonts ? noo_get_option( 'noo_typo_body_font_style', 'normal' ) : 'normal';
$noo_typo_body_font_weight = $noo_typo_use_custom_fonts ? noo_get_option( 'noo_typo_body_font_weight', noo_default_font_weight() ) : noo_default_font_weight();

?>

/* Body style */
/* ===================== */
body {
	font-family: "<?php echo esc_html($noo_typo_body_font); ?>", sans-serif;
	font-size: <?php echo esc_html($noo_typo_body_font_size) . 'px'; ?>;
	font-style: <?php echo esc_html($noo_typo_body_font_style); ?>;
	font-weight: <?php echo esc_html($noo_typo_body_font_weight); ?>;
}

/* Headings */
/* ====================== */
h1, h2, h3, h4, h5, h6,
.h1, .h2, .h3, .h4, .h5, .h6 {
	font-family: "<?php echo esc_html($noo_typo_headings_font); ?>", "Open Sans", sans-serif;
	font-style: <?php echo esc_html($noo_typo_headings_font_style); ?>;
	font-weight: <?php echo esc_html($noo_typo_headings_font_weight); ?>;	
	<?php if ( !empty( $noo_typo_headings_uppercase ) ) : ?>
		text-transform: uppercase;
	<?php else : ?>
		text-transform: none;
	<?php endif; ?>
}


<?php if( class_exists('Hc_Insert_Html_Widget') ) :?>
	.site div.healcode{
		font-size: <?php echo esc_html($noo_typo_body_font_size) . 'px'; ?>;
	}
	.site div.healcode select{
		font-size: <?php echo esc_html($noo_typo_body_font_size) . 'px'; ?>;
	}
		
<?php endif;?>