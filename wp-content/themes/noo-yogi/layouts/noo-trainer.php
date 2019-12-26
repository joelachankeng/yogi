
<?php
$attribute_enable = noo_get_option('noo_trainer_enable_attribute', true);
$attribute_title = '';
if($attribute_enable !== false) {
	$attribute_title = noo_get_option('noo_trainer_attribute_title', __( 'Project Attribute', 'noo' ));
}

$link_enable = noo_get_option('noo_trainer_enable_project_link', true);
$project_link = '';
$link_title = '';
$link_button = '';
if( $link_enable !== false) {
	$project_link = noo_get_post_meta(get_the_id() , '_project_url', '');
	$link_title = noo_get_option('noo_noo_trainer_link_title', __( 'Launch Project', 'noo'));
	$link_button = noo_get_option('noo_noo_trainer_link_button_text', __( 'See it in Action!', 'noo' ));
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if (has_featured_content()): ?>
		<div class="content-featured">
			<?php noo_featured_portfolio(); ?>
		</div>
	<?php endif; ?>
	<div class="content-wrap">
		<?php if (is_singular()): ?>
			<div class="row">
				<div class="content-info col-md-9">
					<header class="content-header">
						<h1 class="content-title content-title-portfolio"><?php the_title(); ?></h1>
						<?php noo_content_meta(); ?>
					</header>
					<div class="content">
						<?php the_content(); ?>
						<?php wp_link_pages(); ?>
					</div>
				</div>
				<div class="portfolio-detail col-md-3">
					<?php if ($attribute_enable !== false && has_term('', 'portfolio_tag')): ?>
						<?php if($attribute_title != '') : ?>
							<h3 class="attribute-title"><?php echo esc_html($attribute_title); ?></h3>
						<?php endif; ?>
						<?php noo_trainer_attributes(); ?>
					<?php endif; ?>
					<?php if ($link_enable !== false && $project_link): ?>
						<?php if($link_title != '') : ?>
							<h3 class="project-link-title"><?php echo esc_html($link_title); ?></h3>
						<?php endif; ?>
						<a href="<?php echo esc_url($project_link); ?>" title="<?php echo esc_attr($link_title); ?>" class="btn btn-primary project-link-btn" target="_blank"><?php echo esc_html($link_button); ?></a>
					<?php endif; ?>
					<?php noo_social_share(); ?>
				</div>
			</div>
		<?php else : ?>
		<header class="content-header">
			<h2 class="content-title content-title-portfolio">
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(sprintf(__('Permanent link to: "%s"', 'noo') , the_title_attribute('echo=0'))); ?>"><?php the_title(); ?></a>
			</h2>
		</header>
		<?php endif; ?>
	</div>
</article> <!-- /#post-<?php the_ID(); ?> -->