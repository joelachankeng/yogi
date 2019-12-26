<?php
/**
 * The Template for displaying trainer archives
 *
 * This template can be overridden by copying it to yourtheme/noo-timetable/archive-noo_trainer.php.
 *
 * @author 		NooTheme
 * @package 	NooTimetable/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header();

$layout_style = NOO_Settings()->get_option('noo_trainer_style', 'grid');
$columns = NOO_Settings()->get_option('noo_trainer_columns', 4);

$class_layout = ($layout_style == 'grid') ? ' grid' : ' list';
$class_shortcode = 'noo-trainer-shortcode' . $class_layout;

?>

    <div class="container-wrap">
        <div class="main-content container-boxed max offset">
            <div class="row">
                <div class="<?php noo_main_class(); ?>" role="main">
                    <!-- Begin The loop -->
                    <?php if(have_posts()):?>
                        <?php trainer_listing('',__('Trainer Listing','noo'))?>
                    <?php else:?>
                        <?php noo_get_layout( 'no-content' ); ?>
                    <?php endif;?>
                    <!-- End The loop -->
                    <?php noo_pagination(); ?>
                </div> <!-- /.main -->
                <?php get_sidebar(); ?>
            </div><!--/.row-->
        </div><!--/.container-boxed-->
    </div><!--/.container-wrap-->

<?php get_footer(); ?>