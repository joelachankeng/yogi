<?php
list($heading, $archive_title, $archive_desc) = get_page_heading();
if( ! empty($heading) ) :
	$heading_image = get_page_heading_image();
?>
	<?php if( noo_get_option( 'noo_page_heading', true ) ) : ?>
		<?php if(!noo_get_post_meta(get_the_ID(), '_noo_wp_page_hide_page_title', false) ) { ?>
			<?php if( !empty( $heading_image ) ) : ?>
				<header class="noo-page-heading heading-bg-image" style="background-image: url('<?php echo esc_url($heading_image); ?>');" >
			<?php else : ?>
				<header class="noo-page-heading">
			<?php endif; ?>
				<div class="container-boxed max">
					<div class="page-heading-info ">
						<h1 class="page-title"><?php noo_nth_word(esc_html($heading),'first'); ?></h1>
					</div>
					<div class="noo-page-breadcrumb">
						<?php if( noo_get_option( 'noo_breadcrumbs', true ) ) noo_get_layout('breadcrumbs'); ?>
					</div>
				</div><!-- /.container-boxed -->
			</header>
		<?php } ?>
	<?php endif; ?>
<?php endif; ?>
