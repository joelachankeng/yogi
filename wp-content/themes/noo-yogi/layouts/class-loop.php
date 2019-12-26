<?php
$post_class='loadmore-item masonry-item';
$post_class_col = NOO_Settings()->get_option('noo_classes_grid_columns', 2);
$current_view_mode = NOO_Settings()->get_option('noo_classes_style', 'grid');
//		$current_view_mode = get_option('noo_loop_view_mode','grid');
if(isset($_GET['mode']) && in_array($_GET['mode'], array('grid','list'))){
	update_option('noo_loop_view_mode', $_GET['mode']);
    $current_view_mode = $_GET['mode'];
}

$grid_mode_href= ($current_view_mode == 'list' ? ' href="'.esc_url(add_query_arg('mode','grid')).'"' :'');
$list_mode_href= ($current_view_mode == 'grid' ? ' href="'.esc_url(add_query_arg('mode','list')).'"' :'');

$show_meta_date    = noo_get_option('noo_classes_meta_date', true);
$show_meta_trainer = noo_get_option('noo_classes_meta_trainer', true);
$show_meta_address = noo_get_option('noo_classes_meta_address', false);
$show_loadmore     = noo_get_option('noo_classes_show_loadmore', true);

if(!empty($mode))
	$current_view_mode = $view_mode;

if($current_view_mode=='grid')
	$post_class .= ' col-sm-6 col-md-'.absint((12 / $post_class_col));
?>
<div class="posts-loop masonry <?php echo esc_attr($current_view_mode)?> noo-classes row ">
	<?php if($title !==false):?>
	<?php 
		if(empty($title)) {
			$title = sprintf(__('We found <span class="text-primary">%s</span> available classes for you', 'noo'), '');
		}
	?>
	<div class="posts-loop-title">
		<h3>
			<?php echo $title?>
			&nbsp;<i class="fa fa-refresh fa-spin fa-fw"></i>
		</h3>
		<span class="loop-view-mode">
			<a class="grid-mode<?php echo ($current_view_mode == 'grid' ? ' active' :'')?>" title="<?php esc_attr_e('Grid','noo')?>" <?php echo ($grid_mode_href)?>><i class="fa fa-th"></i></a>
			<a class="list-mode<?php echo ($current_view_mode == 'list' ? ' active' :'')?>" title="<?php esc_attr_e('List','noo')?>" <?php echo ($list_mode_href) ?>><i class="fa fa-th-list"></i></a>	
		</span>
		<div class="load-title"></div>
	</div>
	<?php endif;?>
	<div class=" posts-loop-content loadmore-wrap">
		<div class="masonry-container">
			<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); global $post; ?>
			<?php 
			$mansory_filter_class = array();
			$mansory_filter_class[] = $post_class;
			foreach ( (array) get_the_terms($post->ID,'class_category') as $cat ) {
				if ( empty($cat->slug ) )
					continue;
				$mansory_filter_class[] =  'filter-cat-'.$cat->term_id;
			}
			foreach ( (array) get_the_terms($post->ID,'class_level') as $level ) {
				if ( empty($level->slug ) )
					continue;
				$mansory_filter_class[] =  'filter-level-'.$level->term_id;
			}
			
			$trainer = (array) noo_json_decode( noo_get_post_meta( get_the_ID(), '_trainer') );
			foreach ($trainer as $trainer_id) {
				$mansory_filter_class[]='filter-trainer-'.$trainer_id;
			}
			foreach ((array)noo_json_decode( noo_get_post_meta( get_the_ID(), "_number_day", '' ) ) as $dayindex){
				$mansory_filter_class[] = 'filter-day-'.$dayindex;
			}
			
			?>
			<article <?php post_class($mansory_filter_class); ?>>
				<div class="loop-item-wrap">
					<?php if(has_post_thumbnail()):?>
				    <div class="loop-item-featured">
				        <a href="<?php the_permalink() ?>">
							<?php the_post_thumbnail('noo-thumbnail-square')?>
						</a>
				    </div>
				    <?php endif;?>
					<div class="loop-item-content">
						<div class="loop-item-content-summary">
							<div class="loop-item-category"><?php echo get_the_term_list(get_the_ID(), 'class_category',' ',', ')?></div>
							<h2 class="loop-item-title">
								<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
							</h2>
							<div class="loop-item-excerpt">
								<?php
								$excerpt = $post->post_excerpt;
								if(empty($excerpt))
									$excerpt = $post->post_content;
								
								$excerpt = strip_shortcodes($excerpt);
								echo '<p>' . wp_trim_words($excerpt,$excerpt_length,'...') . '</p>';
								?>
							</div>
							<div class="content-meta">
								
								<?php $open_date = noo_get_post_meta(get_the_ID(),'_open_date');?>
								<?php
									$number_days		= (array) noo_json_decode( noo_get_post_meta( get_the_ID(), "_number_day", '' ) );
									$class_dates = Noo__Timetable__Class::get_open_date_display(array(
										'open_date'       => $open_date,
										'number_of_weeks' => noo_get_post_meta( get_the_ID(), "_number_of_weeks", '1'),
										'number_days'     => $number_days
									));
									$trainer_ids = noo_get_post_meta(get_the_ID(),'_trainer');
									$address = noo_get_post_meta(get_the_ID(),'_address');
									$map = noo_get_post_meta(get_the_ID(),'_map');
								?>
								<?php if( $show_meta_date && !empty( $class_dates['open_date']) ) : ?>
									<span class="meta-date">
										<time datetime="<?php echo mysql2date( 'c',$open_date) ?>">
											<i class="fa fa-calendar"></i>
											<?php echo __('Open:', 'noo'); ?> <?php echo esc_html(date_i18n(get_option('date_format'),$open_date)); ?>
										</time>
									</span>
								<?php endif;?>
								<?php if( $show_meta_date && !empty( $class_dates['next_date'] ) ) : ?>
									<span class="meta-date">
										<time datetime="<?php echo mysql2date( 'c', $class_dates['next_date']) ?>">
											<i class="fa fa-calendar"></i>
											<?php echo __('Next:', 'noo'); ?> <?php echo esc_html(date_i18n(get_option('date_format'),$class_dates['next_date'])); ?>
										</time>
									</span>
								<?php endif;?>
								<?php if( $show_meta_trainer && !empty( $trainer_ids ) ):?>
								<?php Noo__Timetable__Class::get_trainer_list($trainer_ids);?>
								<?php endif;?>
								<?php if( $show_meta_address && !empty( $address ) ):?>
									<span class="meta-address">
										<i class="fa fa-map-marker"></i>
										<?php 
											if( !empty($map) ){
												echo '<a href="'.esc_attr( $map ).'" target="_blank" title="'.esc_attr( $address ).'">'.esc_html($address).'</a>';
											} else {
												echo esc_html($address); 
											}
										?>
									</span>
								<?php endif;?>
							</div>
						</div>
						<?php if($current_view_mode=='grid'):?>
						<div class="loop-item-action">
							<a class="btn btn-default btn-block text-uppercase" href="<?php the_permalink()?>"><?php echo __('Learn More','noo')?></a>
						</div>
						<?php endif;?>
					</div>
				</div>
			</article>
			<?php endwhile;?>
		</div>
		<?php if(1 < $wp_query->max_num_pages):?>
		<div class="loadmore-action">			
			<a href="#" class="btn-loadmore btn-primary" title="<?php _e('Load More','noo')?>"><?php _e('Load More','noo')?></a>
			<div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span></div>
		</div>
		<?php noo_pagination(array(),$wp_query)?>
		<?php endif;?>
	</div>
</div>