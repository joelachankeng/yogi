<?php global $post;?>
<?php if(is_singular()):?>
	<?php if(noo_get_option('noo_blog_post_author_bio', true)):?>
	<footer class="content-footer">
		<?php if(noo_get_option('noo_blog_post_author_bio', true)):?>
		<div id="author-bio">
			<div class="author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ),120); ?>
			</div>
			<div class="author-info">
				<h4>
					<a title="<?php printf( __( 'Post by %s','noo'), get_the_author() ); ?>" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
						<?php echo get_the_author() ?>
					</a>
				</h4>
				<p>
					<?php the_author_meta( 'description' ) ?>
				</p>
				<div class="author-connect">
					<span class="connect-button"><i class="fa fa-share-alt"></i><?php _e('Connect me', 'noo'); ?></span>
					<div class="connect">
						<a target="_blank" href="<?php echo esc_html(get_the_author_meta('google_profile'));?>" title="<?php _e('Connect Google', 'noo'); ?>">
							<i class="fa fa-google">&nbsp;</i>
						</a>
						<a target="_blank" href="<?php echo esc_html(get_the_author_meta('twitter_profile'));?>" title="<?php _e('Connect Twitter', 'noo'); ?>">
							<i class="fa fa-twitter">&nbsp;</i>
						</a>
						<a target="_blank" href="<?php echo esc_html(get_the_author_meta('facebook_profile'));?>" title="<?php _e('Connect Facebook', 'noo'); ?>">
							<i class="fa fa-facebook">&nbsp;</i>
						</a>
						<a target="_blank" href="<?php echo esc_html(get_the_author_meta('linkedin_profile'));?>" title="<?php _e('Connect Linkedin', 'noo'); ?>">
							<i class="fa fa-linkedin">&nbsp;</i>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php endif;?>
	</footer>
	<?php endif;?>
<?php endif;?>
