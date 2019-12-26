<?php
/**
 * The Template for displaying all single trainers
 *
 * This template can be overridden by copying it to yourtheme/noo-timetable/single-noo_trainer.php.
 *
 * @author 		NooTheme
 * @package 	NooTimetable/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header(); ?>

<?php

    wp_enqueue_script( 'wow' );

	$prefix = '_noo_trainer';
	// Trainer's info
	$position		= noo_get_post_meta( get_the_ID(), "{$prefix}_position", '' );
	$experience		= noo_get_post_meta( get_the_ID(), "{$prefix}_experience", '' );
    $email          = noo_get_post_meta( get_the_ID(), "{$prefix}_email", '' );
    $phone          = noo_get_post_meta( get_the_ID(), "{$prefix}_phone", '' );
	$phone_esc		= preg_replace('/\s+/', '', $phone);
	$biography		= noo_get_post_meta( get_the_ID(), "{$prefix}_biography", '' );
	$facebook		= noo_get_post_meta( get_the_ID(), "{$prefix}_facebook", '' );
	$twitter		= noo_get_post_meta( get_the_ID(), "{$prefix}_twitter", '' );
	$google     	= noo_get_post_meta( get_the_ID(), "{$prefix}_google", '' );
	$linkedin		= noo_get_post_meta( get_the_ID(), "{$prefix}_linkedin", '' );
	$pinterest		= noo_get_post_meta( get_the_ID(), "{$prefix}_pinterest", '' );


    $new_skill_label = '_noo_trainer_skill_label';
    $new_skill_value = '_noo_trainer_skill_value';

    $skill_label = noo_get_post_meta( get_the_ID(), $new_skill_label, '' );
    $skill_value = noo_get_post_meta( get_the_ID(), $new_skill_value, '' );

    $skill_label = (array) noo_json_decode($skill_label);
    $skill_value = (array) noo_json_decode($skill_value);

?>
<?php while ( have_posts() ) : the_post();?>
<div class="container-boxed max offset">
	<div class="row">
		<div class="col-md-12 trainer-details" role="main">

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="content-wrap">
						<div class="row">
							<div class="col-md-4">
								<div class="content-featured">
                                    <?php the_post_thumbnail('trainer-thumbnail'); ?>
								</div>
							</div>
							<div class="trainer-info col-md-8">
                                <a class="view_class" href="<?php echo esc_url(add_query_arg(array('trainer'=>get_the_ID(), 'load' => 'all'), get_post_type_archive_link('noo_class'))) ?>"><i class="fa fa-chevron-circle-right text-primary">&nbsp;</i><?php _e('View Classes', 'noo'); ?></a>
								<h1 class="content-title trainer-name">
									<?php the_title(); ?>
								</h1>
                                <div class="trainer-info">
                                    <span class="trainer-category">
                                        <?php
                                        $categories = get_the_term_list(get_the_ID(),'class_category');
                                        echo $categories;
                                        ?>
                                    </span>

                                    <?php if( !empty( $position ) ) : ?>
                                        <div class="trainer-position"><span><?php _e('Position:','noo')?></span><?php echo esc_html($position); ?></div>
                                    <?php endif; ?>
                                    <?php if( !empty( $experience ) ) : ?>
                                        <div class="trainer-experience"><span><?php _e('Experience:','noo')?></span><?php echo esc_html($experience); ?></div>
                                    <?php endif; ?>
                                    <?php if( !empty( $email ) ) : ?>
                                        <div class="trainer-email"><span><?php _e('Email:','noo')?></span><a href="mailto:<?php echo esc_html($email); ?>"><?php echo esc_html($email); ?></a></div>
                                    <?php endif; ?>
                                    <?php if( !empty( $phone ) ) : ?>
                                        <div class="trainer-phone"><span><?php _e('Phone:','noo')?></span><a href="tel:<?php echo esc_html($phone_esc); ?>"><?php echo esc_html($phone); ?></a></div>
                                    <?php endif; ?>
                                    <?php if( !empty( $biography ) ) : ?>
                                        <div class="trainer-biography"><span><?php _e('Biography:','noo')?></span><div><?php echo $biography; ?></div></div>
                                    <?php endif; ?>
                                    <?php if ( is_array($skill_label) && count($skill_label) > 0 && $skill_label[0] != '' ) : ?>
                                        <div class="trainer-skill">
                                            <span><?php _e('Skill:','noo')?></span>
                                            <div class="noo-progress-bar">
                                            <?php
                                            foreach ($skill_label as $k => $label) :
                                                $lab = isset($skill_label[$k]) ? $skill_label[$k] : '';
                                                $val = isset($skill_value[$k]) ? $skill_value[$k] : '';
                                                if ( $lab != '' && $val != ''  ) :
                                                ?>

                                                <div class="noo-single-bar">
                                                    <small style="width:<?php echo esc_attr( $val ); ?>%" class="label-bar">
                                                        <span class="noo-progress-label"><?php echo esc_attr( $lab ); ?></span>
                                                        <span class="noo-label-units"><?php echo esc_attr( $val ); ?>%</span>
                                                    </small>
                                                    <span class="noo-bar wow loadSkill" data-wow-duration="1.5s" data-wow-delay="0.6s" style="max-width: <?php echo esc_attr( $val ); ?>%;"></span>
                                                </div>

                                                <?php
                                                endif;
                                            endforeach; ?>
                                            </div>
                                        </div>
                                        <script>
                                            jQuery(document).ready(function($) {
                                                new WOW().init();
                                            });
                                        </script>
                                    <?php endif; ?>
                                </div>
                                <?php if(!empty($facebook) || !empty($twitter) || !empty($google) || !empty($linkedin) || !empty($pinterest)):?>
                                    <div class="trainer-social clearfix noo-social">
                                        <h5>
                                            <?php _e('Social Info','noo'); ?>
                                        </h5>
                                        <div class="noo-social-content">
                                            <?php echo ( !empty($facebook) ? '<a href="' . $facebook . '"><i class="fa fa-facebook"></i></a>' : '' ); ?>
                                            <?php echo ( !empty($twitter) ? '<a href="' . $twitter . '"><i class="fa fa-twitter"></i></a>' : '' ); ?>
                                            <?php echo ( !empty($google) ? '<a href="' . $google . '"><i class="fa fa-google-plus"></i></a>' : '' ); ?>
                                            <?php echo ( !empty($linkedin) ? '<a href="' . $linkedin . '"><i class="fa fa-linkedin"></i></a>' : '' ); ?>
                                            <?php echo ( !empty($pinterest) ? '<a href="' . $pinterest . '"><i class="fa fa-pinterest"></i></a>' : '' ); ?>
                                        </div>

                                    </div>
                                <?php endif; ?>
							</div>

							
						</div>
					</div>
				</article> <!-- /#post-<?php the_ID(); ?> -->

		</div>
	</div> <!-- /.row -->



</div> <!-- /.container-boxed.max.offset -->
<div class="trainer-content">
    <div class="container-boxed max offset">
        <div class="row">
            <div class="content col-md-12">
                <h2 class="content-title">
                    <?php noo_nth_word(esc_html__('About my classes','noo'),'first'); ?>
                </h2>
                <div class="content-excerpt">
                    <?php the_content();?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>