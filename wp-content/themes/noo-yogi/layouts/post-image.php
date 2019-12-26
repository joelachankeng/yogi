
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( !is_singular() ) : ?>
		<div class="row">
	<?php endif; ?>

	<?php if ( is_singular() ) : ?>
		<header class="content-header clearfix">
			<h1 class="content-title">
				<?php the_title(); ?>
			</h1>
			<?php noo_content_meta(); ?>
		</header>
	<?php endif; ?>

	<?php if( has_featured_content() ) : ?>
		<?php if (!is_singular()) : ?>
			<div class="content-featured col-sm-6">
				<?php noo_featured_image(); ?>
			</div>
		<?php else : ?>
			<div class="content-featured">
				<?php noo_featured_image(); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( !is_singular() ) : ?>
		<div class="content-wrap col-sm-6">
				<span class="content-category">
					<?php
						$categories = get_the_category();
						$separator = ' ';
						$output = '';
						if($categories){
							foreach($categories as $category) {
								$output .= '<a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s", 'noo' ), $category->name ) ) . '">'.$category->cat_name.'</a>'.$separator;
							}
						echo trim($output, $separator);
						}
					?>
				</span>
				<h3 class="content-title">
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
				</h3>
				<div class="content-excerpt">
					<?php if(get_the_excerpt()):?>
						<?php the_excerpt(); ?>
					<?php endif;?>
				</div>
				<?php noo_content_meta(); ?>
				<?php noo_readmore_link();?>
		</div>
	<?php else: ?>	
		<div class="content-wrap">
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>

			<?php if(has_tag() && noo_get_option('noo_blog_post_show_post_tag',true)) : ?>
				<div class="entry-tags">
				<?php the_tags(sprintf('<i class="fa fa-tags"></i>'),',')?>
				</div>
			<?php endif;?>

			<div class="noo-social">
				<?php echo noo_social_share(get_the_ID(),false); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php noo_get_layout('post', 'footer'); ?>

	<?php if ( !is_singular() ) : ?>
		</div>
	<?php endif; ?>

</article> <!-- /#post- -->