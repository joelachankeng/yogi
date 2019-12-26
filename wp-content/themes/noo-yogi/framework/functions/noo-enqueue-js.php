<?php
/**
 * NOO Framework Site Package.
 *
 * Register Script
 * This file register & enqueue scripts used in NOO Themes.
 *
 * @package    NOO Framework
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */
// =============================================================================

//
// Site scripts
//
if ( ! function_exists( 'noo_enqueue_site_scripts' ) ) :
	function noo_enqueue_site_scripts() {

		// Main script

		// vendor script
		wp_register_script( 'vendor-modernizr', NOO_FRAMEWORK_URI . '/vendor/modernizr-2.7.1.min.js', null, null, false );
		wp_register_script( 'vendor-touchSwipe', NOO_FRAMEWORK_URI . '/vendor/jquery.touchSwipe.js', array( 'jquery' ), null, true );
		wp_register_script( 'vendor-bootstrap', NOO_FRAMEWORK_URI . '/vendor/bootstrap.min.js', array( 'vendor-touchSwipe' ), null, true );
		
		wp_register_script( 'vendor-hoverIntent', NOO_FRAMEWORK_URI . '/vendor/hoverIntent-r7.min.js', array( 'jquery' ), null, true );
		wp_register_script( 'vendor-superfish', NOO_FRAMEWORK_URI . '/vendor/superfish-1.7.4.min.js', array( 'jquery', 'vendor-hoverIntent' ), null, true );
    	wp_register_script( 'vendor-jplayer', NOO_FRAMEWORK_URI . '/vendor/jplayer/jplayer-2.5.0.min.js', array( 'jquery' ), null, true );
		
		wp_register_script( 'vendor-imagesloaded', NOO_FRAMEWORK_URI . '/vendor/imagesloaded.pkgd.min.js', null, null, true );
		wp_register_script( 'vendor-isotope', NOO_FRAMEWORK_URI . '/vendor/isotope-2.0.0.min.js', array('vendor-imagesloaded'), null, true );
		wp_register_script( 'vendor-infinitescroll', NOO_FRAMEWORK_URI . '/vendor/infinitescroll-2.0.2.min.js', null, null, true );
		wp_register_script( 'vendor-carouFredSel', NOO_FRAMEWORK_URI . '/vendor/carouFredSel/jquery.carouFredSel-6.2.1-packed.js', array( 'jquery', 'vendor-touchSwipe' ), null, true );

		wp_register_script( 'vendor-easing', NOO_FRAMEWORK_URI . '/vendor/easing-1.3.0.min.js', array( 'jquery' ), null, true );
		wp_register_script( 'vendor-appear', NOO_FRAMEWORK_URI . '/vendor/jquery.appear.js', array( 'jquery','vendor-easing' ), null, true );
		wp_register_script( 'vendor-countTo', NOO_FRAMEWORK_URI . '/vendor/jquery.countTo.js', array( 'jquery', 'vendor-appear' ), null, true );
		wp_register_script( 'vc_pie_custom', NOO_ASSETS_URI . '/js/jquery.vc_chart.custom.js',array('jquery','progressCircle','vendor-appear'), null, true);
		
		// wp_register_script( 'vendor-nivo-lightbox-js', NOO_FRAMEWORK_URI . '/vendor/nivo-lightbox/nivo-lightbox.min.js', array( 'jquery' ), null, true );
		
		wp_register_script( 'vendor-parallax', NOO_FRAMEWORK_URI . '/vendor/jquery.parallax-1.1.3.js', array( 'jquery'), null, true );
		//wp_register_script( 'vendor-nicescroll', NOO_FRAMEWORK_URI . '/vendor/nicescroll-3.5.4.min.js', array( 'jquery' ), null, true );
		
		// BigVideo scripts.
		wp_register_script( 'vendor-bigvideo-video',        NOO_FRAMEWORK_URI . '/vendor/bigvideo/video-4.1.0.min.js',        array( 'jquery', 'jquery-ui-slider', 'vendor-imagesloaded' ), NULL, true );
		wp_register_script( 'vendor-bigvideo-bigvideo',     NOO_FRAMEWORK_URI . '/vendor/bigvideo/bigvideo-1.0.0.min.js',     array( 'jquery', 'jquery-ui-slider', 'vendor-imagesloaded', 'vendor-bigvideo-video' ), NULL, true );
		// wp_register_script( 'noo-countdown',     NOO_FRAMEWORK_URI . '/vendor/noo_countdown.js',, null, null, false );
		
		wp_register_script( 'noo-script', NOO_ASSETS_URI . '/js/noo.js', array( 'jquery','vendor-bootstrap', 'vendor-superfish', 'vendor-imagesloaded' ), null, true );
		
		//fulldatecalendar
		
		wp_register_script( 'noo-event-moment', NOO_FRAMEWORK_URI . '/vendor/fullcalendar/lib/moment.min.js',null, null, true );
		wp_register_script( 'noo-event-calendar-lang', NOO_FRAMEWORK_URI . '/vendor/fullcalendar/lang-all.js',null, null, true );
		wp_register_script( 'noo-event-calendar', NOO_FRAMEWORK_URI . '/vendor/fullcalendar/fullcalendar.custom.js',array('noo-event-moment','jquery','vendor-bootstrap'), null, true );
		
		
		if ( ! is_admin() ) {
			wp_enqueue_script( 'vendor-modernizr' );
			
			// if( noo_get_option( 'noo_smooth_scrolling', true ) ) {
			// 	wp_enqueue_script('vendor-nicescroll');
			// }	

			// Required for nested reply function that moves reply inline with JS
			if ( is_singular() ) wp_enqueue_script( 'comment-reply' );

			if ( is_post_type_archive( 'portfolio_project' ) || is_tax( 'portfolio_category') ) {
				wp_enqueue_script('vendor-isotope');
			}

			$is_shop				= NOO_WOOCOMMERCE_EXIST && is_shop();
			$is_project             = is_singular( 'portfolio_project' ) ? 'true' : 'false';
			$is_portfolio_attribute = is_tax( 'portfolio_tag' ) ? 'true' : 'false';
			$nooL10n = array(
				'ajax_url'        => admin_url( 'admin-ajax.php', 'relative' ),
				'home_url'        => home_url( '/' ),
				'is_blog'         => is_home() ? 'true' : 'false',
				'is_archive'      => is_post_type_archive('post') ? 'true' : 'false',
				'is_single'       => is_single() ? 'true' : 'false',
				'is_trainer'      => is_post_type_archive( 'noo_trainer' ) || is_tax( 'trainer_category') ? 'true' : 'false',
				'is_shop'         => NOO_WOOCOMMERCE_EXIST && is_shop() ? 'true' : 'false',
				'is_product'      => NOO_WOOCOMMERCE_EXIST && is_product() ? 'true' : 'false',
				'is_classes'       => is_post_type_archive( 'noo_class' ) || is_tax( 'class_category') ? 'true' : 'false',
				'is_class'        => is_singular( 'noo_class' ) ? 'true' : 'false',
				'is_events'       => is_post_type_archive( 'noo_event' ) || is_tax( 'event_category') || is_tax( 'event_location') ? 'true' : 'false',
				'is_event'        => is_singular( 'noo_event' ) ? 'true' : 'false',
				'infinite_scroll_end_msg' => __('All posts displayed', 'noo'),
				'ajax_finishedMsg'=>__('All posts displayed','noo'),
			);
			
			
			wp_localize_script('noo-script', 'nooL10n', $nooL10n);
			wp_enqueue_script( 'noo-script' );
		}
	}
add_action( 'wp_enqueue_scripts', 'noo_enqueue_site_scripts' );
endif;
