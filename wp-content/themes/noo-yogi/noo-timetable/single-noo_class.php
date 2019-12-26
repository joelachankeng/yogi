<?php
/**
 * The Template for displaying all single classes
 *
 * This template can be overridden by copying it to yourtheme/noo-timetable/single-noo_class.php.
 *
 * @author 		NooTheme
 * @package 	NooTimetable/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_locale;

?>
<?php get_header(); ?>
<div class="container-boxed max offset">
	<div class="row">
		<div class="<?php noo_main_class(); ?>" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>			
				<header class="content-header clearfix">
					<h1 class="content-title">
						<?php the_title(); ?>
					</h1>
					<p class="content-meta">
						<?php if($trainer_ids = noo_get_post_meta(get_the_ID(),'_trainer')):?>
						<span>
							<?php _e('Trainer:','noo')?>
							<?php Noo__Timetable__Class::get_trainer_list($trainer_ids);?>
						</span>
						<?php endif;?>
						<?php if($category_list = get_the_term_list(get_the_ID(), 'class_category',' ',', ')):?>
						<span>
							<?php _e('Category:','noo')?>
							<?php echo ($category_list)?>
						</span>
						<?php endif;?>
						<?php if($level_list = get_the_term_list(get_the_ID(), 'class_level',' ',', ')):?>
						<span>
							<?php _e('Level:','noo')?>
							<?php echo ($level_list)?>
						</span>
						<?php endif;?>
					</p>
				</header>
				<?php if( has_featured_content() ) : ?>
					<div class="content-featured">
						<?php noo_featured_default(); ?>
					</div>
				<?php endif; ?>
				<div class="content-wrap">
					<?php the_content(); ?>
					<?php wp_link_pages(); ?>
					<?php if(has_tag() && noo_get_option('noo_blog_post_show_post_tag',true)) : ?>
						<div class="entry-tags">
						<?php the_tags(sprintf('<i class="fa fa-tags"></i>'),',')?>
						</div>
					<?php endif;?>
					<?php Noo__Timetable__Class::get_timetable(); ?>
					
					<div class="noo-social">
						<?php echo noo_social_share(get_the_ID(),true, 'noo_class'); ?>
					</div>
				</div>
				<?php if(apply_filters('noo_class_trainer_bio', true)):?>
					<footer class="content-footer">
					<?php
					$trainer_decode = (array) noo_json_decode($trainer_ids);
					if ( count($trainer_decode) > 0 && $trainer_decode[0] != '' ) :
						foreach ( $trainer_decode as $trainer ) : ?>
						<div id="author-bio">
							<div class="author-avatar">
	                            <?php echo get_the_post_thumbnail($trainer, 'thumbnail') ?>
							</div>
							<div class="author-info">
								<h4>
									<a title="<?php printf( __( 'Post by %s','noo'), get_the_title($trainer) ); ?>" href="<?php echo esc_url( get_permalink($trainer) ); ?>" rel="author">
										<?php echo get_the_title($trainer) ?>
									</a>
								</h4>
								<p>
									<?php
										$biography = noo_get_post_meta( $trainer, "_noo_trainer_biography", '' );
										if ( !empty( $biography ) ) {
											echo wp_kses_post($biography);
										} else {
											echo noo_get_the_excerpt($trainer);
										}
									?>
								</p>
	                            <a class="view-profile" title="<?php printf( __( 'Post by %s','noo'), get_the_title($trainer) ); ?>" href="<?php echo esc_url( get_permalink($trainer) ); ?>" rel="author"><i class="fa fa-share-alt"></i>&nbsp;<?php _e('View my profile', 'noo'); ?></a>
							</div>
						</div>
						<?php endforeach; ?>
					<?php endif;?>
					</footer>
					<?php endif;?>
				</article> <!-- /#post- -->
				<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'noo' ), 'after' => '</div>' ) ); ?>
				<?php if ( comments_open() ) : ?>
					<?php comments_template( '', true ); ?>
				<?php endif; ?>
			<?php endwhile; ?>
		</div>
		<div class="<?php noo_sidebar_class(); ?> hidden-print">
		    <?php if(is_singular('noo_class')): ?>
		        <?php
		        //Class Information Location
		        $class_address		= noo_get_post_meta( get_the_ID(), "_address", '' );
		        $number_of_weeks	= noo_get_post_meta( get_the_ID(), "_number_of_weeks", '' );
		        $open_date		    = noo_get_post_meta( get_the_ID(), "_open_date", '' );
		        $number_days		= (array) noo_json_decode( noo_get_post_meta( get_the_ID(), "_number_day", '' ) );
		        $open_time		    = noo_get_post_meta( get_the_ID(), "_open_time", '' );
		        $close_time		    = noo_get_post_meta( get_the_ID(), "_close_time", '' );
		        $register_link		= noo_get_post_meta( get_the_ID(), "_register_link", '' );

		        // Get Open Date & Next Date
		        $args = array(
					'open_date'       => $open_date,
					'number_of_weeks' => $number_of_weeks,
					'number_days'     => $number_days
		        );
		        $class_dates = Noo__Timetable__Class::get_open_date_display( $args );
		        $use_advanced_multi_time = noo_get_post_meta( get_the_ID(), "_use_advanced_multi_time", false );
		        ?>
		        <div class="single-sidebar">
		            <h4 class="widget-title"><?php noo_nth_word(esc_html__('Class Information','noo'),'first');?></h4>
		            <div class="class-info-sidebar">
		            	<?php if( !$use_advanced_multi_time ) : ?>
							<?php if( !empty( $class_address ) ) : ?>
								<div class="clearfix"><i class="fa fa-map-marker"></i>&nbsp;<?php echo esc_html($class_address); ?></div>
							<?php endif; ?>
						<?php endif; ?>
		                <?php if( !empty( $number_of_weeks ) ) : ?>
		                    <div class="clearfix"><i class="fa fa-file-text-o"></i>&nbsp;<?php echo _e('Number of Week','noo');?><span><?php echo esc_html($number_of_weeks); ?></span></div>
		                <?php endif; ?>
		                <?php if( !empty( $class_dates['open_date'] ) ) : ?>
		                    <div class="clearfix"><i class="fa fa-calendar"></i>&nbsp;<?php echo __('Open:', 'noo'); ?> <?php echo esc_html(date_i18n(get_option('date_format'),$open_date)); ?></div>
		                <?php endif; ?>
		                <?php if( !empty( $class_dates['next_date'] ) ) : ?>
		                    <div class="clearfix"><i class="fa fa-calendar"></i>&nbsp;<?php echo __('Next:', 'noo'); ?> <?php echo esc_html(date_i18n(get_option('date_format'),$class_dates['next_date'])); ?></div>
		                <?php endif; ?>
		                <?php if( !empty( $number_days ) ) : 
		                	$start_of_week = get_option('start_of_week');
		                    $ndays1 = array();
		                    $ndays2 = array();
		                    asort( $number_days );
		                    foreach ($number_days as $k => $nday) {
		                        if ( $nday >= $start_of_week ) {
		                            $ndays1[] = $nday;
		                        } else {
		                            $ndays2[] = $nday;
		                        }
		                    }
		                    $number_days = array_merge($ndays1, $ndays2);
		                ?>
		                    <div class="clearfix tag-days">
		                    	<i class="fa fa-check"></i>&nbsp;<?php echo _e('Days','noo');?>
		                    	<div class="wrap-days">
		                    	<?php foreach ($number_days as $number_day) : ?>
		                    		<?php if ( is_numeric($number_day) ) : ?>
		                    			<span><?php echo esc_html($wp_locale->get_weekday_abbrev($wp_locale->get_weekday($number_day))) ?></span>
		                    		<?php endif; ?>
								<?php endforeach; ?>
								</div>
		                    </div>
		                <?php endif; ?>
		                <?php if( !$use_advanced_multi_time ) : ?>
			                <?php if( !empty( $open_time ) || !empty( $close_time ) ) :?>
			                	<div class="clearfix"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date_i18n(get_option('time_format'),$open_time).'-'. date_i18n(get_option('time_format'),$close_time); ?></div>
			                <?php endif; ?>
			            <?php else : ?>
			            	<div class="clearfix"><a href="#time-table"><i class="fa fa-clock-o"></i>&nbsp;<?php echo __('Multiple time', 'noo'); ?> <i class="fa fa-long-arrow-right"></i></a></div>
			            <?php endif; ?>
		            </div>
		            <?php if( !empty( $register_link ) ) : ?>
		            	<a href="<?php echo esc_url( $register_link );?>" class="btn-primary register_button"><?php echo _e('Register Now','noo');?></a>
		            <?php endif; ?>
		        </div>
		    <?php endif;?>
		    
            <?php
            noo_related_class();
            ?>
		    <?php
			$sidebar = get_sidebar_id();
			if( ! empty( $sidebar ) ) :
			?>
			<div class="noo-sidebar-wrap">

				<?php // Dynamic Sidebar
				if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( $sidebar ) ) : ?>
					<!-- Sidebar fallback content -->
			
				<?php endif; // End Dynamic Sidebar sidebar-main ?>
			</div>
			<?php endif; // End sidebar ?> 
		</div>
	</div> <!-- /.row -->
</div> <!-- /.container-boxed.max.offset -->
<?php get_footer(); ?>