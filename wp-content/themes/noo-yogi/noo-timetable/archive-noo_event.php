<?php
/**
 * The Template for displaying event archives
 *
 * This template can be overridden by copying it to yourtheme/noo-timetable/archive-noo_event.php.
 *
 * @author 		NooTheme
 * @package 	NooTimetable/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$layout_style = NOO_Settings()->get_option('noo_event_default_layout', 'grid');
$columns = NOO_Settings()->get_option('noo_event_grid_column', 2);

$class_layout = ($layout_style == 'grid') ? ' grid' : ' list';
$class_shortcode = 'noo-event-shortcode' . $class_layout;

?>

<?php get_header(); ?>
<div class="container-wrap">
	<div class="main-content container-boxed max offset">
		<div class="row">
			<div class="<?php noo_main_class(); ?>" role="main">
			<?php
				Noo_Event::loop_display();
			?>
			</div> <!-- /.main -->
			<?php get_sidebar(); ?>
		</div><!--/.row-->
	</div><!--/.container-boxed-->
</div><!--/.container-wrap-->
	
<?php get_footer(); ?>