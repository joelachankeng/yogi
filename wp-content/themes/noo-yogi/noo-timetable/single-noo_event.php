<?php
/**
 * The Template for displaying all single events
 *
 * This template can be overridden by copying it to yourtheme/noo-timetable/single-noo_event.php.
 *
 * @author 		NooTheme
 * @package 	NooTimetable/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


// Variables
$avatar_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
if( empty($avatar_src) ) {
	$avatar_src		= NOO_ASSETS_URI . '/images/default-avatar.png';
} else {
	$avatar_src		= $avatar_src[0];
}
// Handle option [show hide map]
$extra_class      = '';
$extra_wrap_class = '';
if ( !noo_get_option('noo_events_hide_map', false) ) {
	// Maps info

	$google_map_latitude = NOO_Settings()->get_option( 'noo_google_map_latitude', '51.508742' );
	$google_map_longitude = NOO_Settings()->get_option( 'noo_google_map_longitude', '-0.120850' );
	$google_zoom = NOO_Settings()->get_option( 'noo_google_map_zoom', '11' );

	$map_latitude		= noo_get_post_meta( get_the_ID(), "_noo_event_gmap_latitude", $google_map_latitude );
	$map_longitude		= noo_get_post_meta( get_the_ID(), "_noo_event_gmap_longitude", $google_map_longitude );

	$google_api = NOO_Settings()->get_option( 'noo_google_map_api_key', '' );
	wp_enqueue_script('google-map','http'.(is_ssl() ? 's':'').'://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places'. ( !empty( $google_api ) ? '&key=' .$google_api : '' ), array('jquery'), '1.0', true);
	wp_enqueue_style( 'noo-event', NOO_FRAMEWORK_ADMIN_URI . '/assets/css/noo_event.css');

	$nooEventMap = array(
		'latitude'          =>  $map_latitude != '' ? $map_latitude : $google_map_latitude,
		'longitude'         =>  $map_longitude != '' ? $map_longitude : $google_map_longitude,
		'def_zoom'          =>  $google_zoom != '' ? $google_zoom : 11,
		'localtion_disable' =>  1,
	);
	wp_register_script( 'noo-event', NOO_ASSETS_URI . '/js/event_map.js', array( 'jquery','google-map'), null, true );
	wp_localize_script('noo-event', 'nooEventMap', $nooEventMap);
	wp_enqueue_script('noo-event');	
} else {
	$extra_class = 'clear-absolute';
	$extra_wrap_class = 'content-wrap container-boxed max';
}
//Event Location
$event_address		= noo_get_post_meta( get_the_ID(), "_noo_event_address", '' );

$event_startdate	= noo_get_post_meta( get_the_ID(), "_noo_event_start_date", '' );
$event_enddate		= noo_get_post_meta( get_the_ID(), "_noo_event_end_date", '' );

$event_starttime	= noo_get_post_meta( get_the_ID(), "_noo_event_start_time", '' );
$event_endtime		= noo_get_post_meta( get_the_ID(), "_noo_event_end_time", '' );

$facebook			= noo_get_post_meta( get_the_ID(), "_facebook", '' );
$twitter			= noo_get_post_meta( get_the_ID(), "_twitter", '' );
$google     		= noo_get_post_meta( get_the_ID(), "_google", '' );
$linkedin			= noo_get_post_meta( get_the_ID(), "_linkedin", '' );
$pinterest			= noo_get_post_meta( get_the_ID(), "_pinterest", '' );

$organizers_id 		= get_post_meta( get_the_ID(), "_noo_event_organizers", true );
$event_author  		= get_post_meta( $organizers_id, "_noo_event_author", true );
$event_phone   		= get_post_meta( $organizers_id, "_noo_event_phone", true );
$event_website 		= get_post_meta( $organizers_id, "_noo_event_website", true );
$event_email   		= get_post_meta( $organizers_id, "_noo_event_email", true );

?>

<?php get_header(); ?>
<div class="main-content container-fullwidth max offset">
	<div class="row">
		<div class="noo-main col-md-12" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" class="noo-event">
					<div class="content-wrap container-boxed max offset">
						<div class="content row">
							<div class="col-sm-8 col-sm-offset-2">
                                <div class="event-header">
                                    <h2><?php the_title()?></h2>
                                    <div class="content-meta">
                                    	<?php Noo__Timetable__Event::show_meta(); ?>
                                    	<?php Noo__Timetable__Event::show_repeat_info(); ?>
                                    </div>
                                </div>
								<?php if( has_featured_content()) : ?>
									<div class="content-featured">
										<?php noo_featured_default(); ?>
									</div>
								<?php endif; ?>
								<?php the_content(); ?>
								<?php wp_link_pages(); ?>
								
							</div>
						</div>
					</div>

					<div class="noo_event_map hidden-print <?php echo $extra_wrap_class; ?>">
						<?php if ( !noo_get_option('noo_events_hide_map', false) ) : ?>
							<div id="noo_event_google_map" class="noo_event_google_map"></div>
						<?php else : ?>
							<div class="content row">
								<div class="col-sm-8 col-sm-offset-2">
						<?php endif; ?>

                        <div class="event-info <?php echo $extra_class; ?>">
                            <h5>
                                <?php noo_nth_word(esc_html__('Event information','noo'),'first'); ?>
                            </h5>
                            <?php if( !empty( $event_address ) ) : ?>
                                <div class=""><i class="fa fa-map-marker"></i>&nbsp;<?php echo esc_html($event_address); ?></div>
                            <?php endif; ?>
                            <?php if( !empty( $event_author ) ) : ?>
                                <div class=""><i class="fa fa-user"></i>&nbsp;<?php echo esc_html($event_author); ?></div>
                            <?php endif; ?>
                            <?php if( !empty( $event_phone) ) : ?>
                                <div class=""><i class="fa fa-phone"></i>&nbsp;<?php echo esc_html($event_phone); ?></div>
                            <?php endif; ?>
                            <?php if( !empty( $event_website ) ) : ?>
                                <div class=""><a href="<?php echo esc_url($event_website); ?>"><i class="fa fa-globe"></i>&nbsp;<?php echo esc_html($event_website); ?></a></div>
                            <?php endif; ?>
                            <?php if( !empty( $event_email) ) : ?>
                                <div class=""><i class="fa fa-envelope"></i>&nbsp;<?php echo esc_html($event_email); ?></div>
                            <?php endif; ?>
                            <div class="noo-social">
                                <?php noo_social_share(get_the_ID(),true, 'noo_event');?>
                            </div>
                        </div>

                    <?php if ( noo_get_option('noo_events_hide_map', false) ) : ?>
                    		</div>
                    	</div>
                    <?php endif; ?>
					</div>
					<div class="clearfix"></div>
				</article> <!-- /#post- -->
				<?php if ( comments_open() ) : ?>
				<div class="content-wrap container-boxed max offset">
					<div class="content row">
						<div class="col-sm-8 col-sm-offset-2">
							<?php comments_template( '', true ); ?>
						</div>
					</div>
				</div>
				<?php endif; ?>
			<?php endwhile; ?>
		</div>
		<?php //get_sidebar(); ?>
	</div> <!-- /.row -->

</div> <!-- /.container-boxed.max.offset -->
<?php get_footer(); ?>