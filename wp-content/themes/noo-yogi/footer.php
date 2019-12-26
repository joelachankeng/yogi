<?php
	$noo_bottom_bar_content = noo_get_option( 'noo_bottom_bar_content', '' );
?>

<?php noo_get_layout( 'footer', 'widgetized' ); ?>
<?php if ( !empty( $noo_bottom_bar_content ) ) : ?>
	<footer class="colophon site-info hidden-print" role="contentinfo">
		<div class="container-full">
			<div class="footer-more">
				<div class="container-boxed">
					<div class="row">
						<div class="col-md-12">
						<?php if ( $noo_bottom_bar_content != '' ) : ?>
							<div class="noo-bottom-bar-content">
								<?php echo noo_html_content_filter($noo_bottom_bar_content); ?>
							</div>
						<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div> <!-- /.container-boxed -->
	</footer> <!-- /.colophon.site-info -->
<?php endif; ?>


</div> <!-- /#div.site -->
<?php wp_footer(); ?>
</body>
</html>
