
<?php if ( noo_get_option( 'noo_header_top_bar', false ) ) :
	$search_box = noo_get_option( 'noo_top_bar_search', true );
?>

<div class="noo-topbar">
	<div class="topbar-inner container-boxed max">
		<div class="row">
			<div class="col-xs-12">
				<div class="topbar-left"><?php echo noo_get_option( 'noo_top_bar_content', '' ); ?></div>
			</div>
		</div>
	</div> <!-- /.topbar-inner -->
</div> <!-- /.noo-topbar -->

<?php endif; ?>
