<?php
/**
 * The Template for displaying class archives
 *
 * This template can be overridden by copying it to yourtheme/noo-timetable/archive-noo_class.php.
 *
 * @author 		NooTheme
 * @package 	NooTimetable/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$layout_style = NOO_Settings()->get_option('noo_classes_style', 'grid');
$columns = NOO_Settings()->get_option('noo_classes_grid_columns', 2);

$class_layout = ($layout_style == 'grid') ? ' grid' : ' list';
$class_shortcode = 'noo-class-shortcode' . $class_layout;

?>

<?php global $wp_locale; ?>
<?php get_header(); ?>
<div class="container-wrap">
	<div class="main-content container-boxed max offset">
		<div class="row">
			<?php
			// Show/ hide filter
			$filter_title         = noo_get_option('noo_classes_filter_title');
			$show_level_filter    = noo_get_option('noo_classes_show_level_filter', true);
			$show_level_filter    = $show_level_filter && !is_tax('class_level') && ($levels = get_terms('class_level'));
			
			$show_category_filter = noo_get_option('noo_classes_show_category_filter', true);
			$show_category_filter = $show_category_filter && !is_tax('class_category') && ($categories = get_terms('class_category'));

			$show_trainer_filter  = noo_get_option('noo_classes_show_trainer_filter', true);
			$show_trainer_filter  = $show_trainer_filter && $trainers = get_posts(array('post_type'=>'noo_trainer','posts_per_page'=>-1));

			$show_days_filter     = noo_get_option('noo_classes_show_days_filter', true);

			$show_filter = ( $show_level_filter || $show_category_filter || $show_trainer_filter || $show_days_filter );

			// Layout sidebar
			$class_sidebar = '';
			$noo_classes_layout = noo_get_option('noo_classes_layout', 'sidebar');
			if( $noo_classes_layout != 'fullwidth' ) :
				switch ( $noo_classes_layout ) {
					case 'left_sidebar':
						$class_sidebar = 'col-md-3 sidebar-left';
						break;
					default: case 'sidebar':
						$class_sidebar = 'col-md-3 sidebar-right';
						break;
				}
			?>
				<div class=" noo-sidebar hidden-print <?php echo $class_sidebar ?>">
					<div class="noo-sidebar-wrap">
						<?php if( $show_filter ) : ?>
							<div class="widget widget-search-classes widget-classes-filters">
								<?php if ( $filter_title != '' ) : ?>
								<h4 class="widget-title">
									<?php noo_nth_word( $filter_title ,'first')?>
								</h4>
								<?php endif; ?>
								<?php if($show_level_filter):?>
								<div class="widget-class-filter search-class-level" data-group="level">
									<select class="widget-class-filter-control">
										<option value=""><?php esc_html_e('&ndash; Select Level &ndash;','noo')?></option>
										<?php foreach ((array)$levels as $level):?>
											<option value="filter-level-<?php echo esc_attr($level->term_id)?>"><?php echo esc_html($level->name)?></option>
										<?php endforeach;?>
									</select>
								</div>
								<?php endif;?>
								<?php if($show_category_filter):?>
								<div class="widget-class-filter search-class-category" data-group="category">
									<select class="widget-class-filter-control">
										<option value=""><?php esc_html_e('&ndash; Select Category &ndash;','noo')?></option>
										<?php foreach ((array)$categories as $category):?>
											<option value="filter-cat-<?php echo esc_attr($category->term_id)?>"><?php echo esc_html($category->name)?></option>
										<?php endforeach;?>
									</select>
								</div>
								<?php endif;?>
								<?php if($show_trainer_filter):
									$current_trainer = isset( $_GET['trainer'] ) && !empty( $_GET['trainer'] ) ? $_GET['trainer'] : '';
								?>
								<div class="widget-class-filter search-class-trainer" data-group="trainer">
									<select class="widget-class-filter-control">
										<option value=""><?php esc_html_e('&ndash; Select Trainer &ndash;','noo')?></option>
										<?php foreach ((array)$trainers as $trainer):?>
											<option <?php selected( $current_trainer, $trainer->ID ); ?> value="filter-trainer-<?php echo esc_attr($trainer->ID)?>"><?php echo esc_html($trainer->post_title)?></option>
										<?php endforeach;?>
									</select>
								</div>
								<?php endif;?>
								<?php if ( $show_days_filter ) : ?>
								<div class="widget-class-filter search-class-weekday" data-group="day">
									<span><?php _e('Filter class by days:','noo')?></span>
									<?php for ($day_index = 0; $day_index <= 6; $day_index++) : ?>
									<label class="col-xs-6">
										<input type="checkbox" class="widget-class-filter-control" value="filter-day-<?php echo esc_attr($day_index)?>"> <?php echo esc_html($wp_locale->get_weekday($day_index)) ?>
									</label>
									<?php
									endfor;
									?>
								</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?php
						$sidebar = get_sidebar_id();
							if( ! empty( $sidebar ) ) :
							?>
							<?php // Dynamic Sidebar
							if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( $sidebar ) ) : ?>
								<!-- Sidebar fallback content -->
							<?php endif; // End Dynamic Sidebar sidebar-main ?>
						<?php endif; // End sidebar ?> 
					</div>
				</div>
			<?php elseif( $show_filter ) : 
				$number_of_cols = count(array_filter( array($show_level_filter, $show_category_filter, $show_trainer_filter) ) );
				$col_class = 12 / $number_of_cols;
				$col_class = ' col-sm-' . $col_class;
			?>
				<div class="noo-sidebar sidebar-fullwidth hidden-print col-md-12 widget widget-search-classes widget-classes-filters">
					<?php if ( $filter_title != '' ) : ?>
						<h4 class="widget-title">
							<?php noo_nth_word( $filter_title ,'first')?>
						</h4>
					<?php endif; ?>
					<?php if($show_level_filter):?>
						<div class="widget-class-filter search-class-level <?php echo $col_class; ?>" data-group="level">
							<select class="widget-class-filter-control">
								<option value=""><?php esc_html_e('&ndash; Select Level &ndash;','noo')?></option>
								<?php foreach ((array)$levels as $level):?>
									<option value="filter-level-<?php echo esc_attr($level->term_id)?>"><?php echo esc_html($level->name)?></option>
								<?php endforeach;?>
							</select>
						</div>
					<?php endif;?>
					<?php if($show_category_filter):?>
					<div class="widget-class-filter search-class-category <?php echo $col_class; ?>" data-group="category">
						<select class="widget-class-filter-control">
							<option value=""><?php esc_html_e('&ndash; Select Category &ndash;','noo')?></option>
							<?php foreach ((array)$categories as $category):?>
								<option value="filter-cat-<?php echo esc_attr($category->term_id)?>"><?php echo esc_html($category->name)?></option>
							<?php endforeach;?>
						</select>
					</div>
					<?php endif;?>
					<?php if($show_trainer_filter):
						$current_trainer = isset( $_GET['trainer'] ) && !empty( $_GET['trainer'] ) ? $_GET['trainer'] : '';
					?>
					<div class="widget-class-filter search-class-trainer <?php echo $col_class; ?>" data-group="trainer">
						<select class="widget-class-filter-control">
							<option value=""><?php esc_html_e('&ndash; Select Trainer &ndash;','noo')?></option>
							<?php foreach ((array)$trainers as $trainer):?>
								<option <?php selected( $current_trainer, $trainer->ID ); ?> value="filter-trainer-<?php echo esc_attr($trainer->ID)?>"><?php echo esc_html($trainer->post_title)?></option>
							<?php endforeach;?>
						</select>
					</div>
					<?php endif;?>
					<?php if ( $show_days_filter ) : ?>
					<div class="widget-class-filter search-class-weekday" data-group="day">
						<span><?php _e('Filter class by days:','noo')?></span>
						<?php
						$start_of_week = (int)get_option('start_of_week');
						for ($day_index = $start_of_week; $day_index <= 7 + $start_of_week; $day_index++) :
						?>
						<label class="col-sm-3 col-xs-4">
							<input type="checkbox" class="widget-class-filter-control" value="filter-day-<?php echo esc_attr($day_index)?>"> <?php echo esc_html($wp_locale->get_weekday($day_index%7)) ?>
						</label>
						<?php
						endfor;
						?>
					</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="<?php noo_main_class(); ?>" role="main">
				<?php Noo_Class::loop_display()?>
			</div>
			
		</div><!--/.row-->
	</div><!--/.container-boxed-->
</div><!--/.container-wrap-->
	
<?php get_footer(); ?>