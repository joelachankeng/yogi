<?php

if ( !function_exists('noo_yogi_update_options_timetable') ) {

	function noo_yogi_update_options_timetable() {
		// update_option( 'has_update_options_timetable', 0 );
		if( get_option( 'has_update_options_timetable' ) ) {
            return;
        }
        update_option( 'has_update_options_timetable', 1 );

        noo_yogi_update_category_color();
        noo_yogi_update_settings_general();
        noo_yogi_update_event();

        flush_rewrite_rules();
		
	}

	add_action( 'init', 'noo_yogi_update_options_timetable' );

}

if ( !function_exists('noo_yogi_update_category_color') ) {
	function noo_yogi_update_category_color() {
		$args = array(
		    'posts_per_page' => '-1',
		    'post_type'      => 'noo_class'
		);

		$classes = get_posts($args);
		foreach ( $classes as $cl ) {
			$post_category = get_the_terms( $cl->ID, 'class_category' );

			if ( !empty($post_category) ) {

				$post_category = reset($post_category);
				$category_parent = $post_category->parent;

				if( !empty($category_parent) ) :
				    $color = noo_get_term_meta( $post_category->parent, 'category_color', '' );
				else:
				    $color = noo_get_term_meta( $post_category->term_id, 'category_color', '#fe6367' );
				endif;
			}

			add_term_meta( $post_category->term_id, 'category_color', $color, true );
		}
	}
}

if ( !function_exists('noo_yogi_update_settings_general') ) {
	function noo_yogi_update_settings_general() {

		$class_page = noo_get_option('noo_class_page', '');
		$class_slug = !empty($class_page) ? get_post( $class_page )->post_name : 'classes';

		$event_page = noo_get_option('noo_events_page', '');
        $event_slug = !empty($event_page) ? get_post( $event_page )->post_name : 'events';

        $trainer_page = noo_get_option('noo_trainer_page', '');
		$trainer_slug = !empty($trainer_page) ? get_post( $trainer_page )->post_name : 'trainer';

		$timetable_settings = array(
			'noo_class_page'           => $class_slug,
			'noo_classes_number_class' => noo_get_option('noo_classes_number_class', 6),
			'noo_classes_style'        => noo_get_option('noo_classes_style', 'grid'),
			'noo_classes_grid_columns' => noo_get_option('noo_classes_grid_columns', 2),
			'noo_classes_orderby'      => noo_get_option('noo_classes_orderby', 'opendate'),
			'noo_classes_order'        => noo_get_option('noo_classes_order', 'asc'),
			
			'noo_trainer_page'         => $trainer_slug,
			'noo_trainer_num'          => noo_get_option('noo_trainer_num', 12),
			// 'noo_trainer_style'     => 'noo_trainer_style',
			'noo_trainer_columns'      => noo_get_option('noo_trainer_columns', 4),
			
			'noo_event_page'           => $event_slug,
			'noo_event_num'            => noo_get_option( 'noo_event_num', '10' ),
			'noo_event_default_layout' => noo_get_option( 'noo_events_choose_style', 'grid' ),
			// 'noo_event_grid_column' => 'noo_event_grid_column',
			'noo_schedule_general_header_background' => '#fff',
			'noo_schedule_general_header_color'      => '#333',
			'noo_schedule_class_item_style'          => 'background',
			'noo_schedule_general_popup'          => 'no',
		);

		update_option( 'timetable_settings', $timetable_settings );
	}
}

if ( !function_exists('noo_yogi_update_event') ) {
	function noo_yogi_update_event() {
		$args = array(
		    'posts_per_page' => '-1',
		    'post_type'      => 'noo_event'
		);

		$events = get_posts($args);

		foreach ($events as $ev) {
			$event_author		= noo_get_post_meta( $ev->ID, "_author", '' );
			$event_phone		= noo_get_post_meta( $ev->ID, "_phone", '' );
			$event_website		= noo_get_post_meta( $ev->ID, "_website", '' );
			$event_email		= noo_get_post_meta( $ev->ID, "_email", '' );
			
			$event_address		= noo_get_post_meta( $ev->ID, "_address", '' );

			$event_startdate	= Noo_Event::get_start_date( $ev->ID, 'm/d/Y' );
			$event_starttime      = Noo_Event::get_start_date( $ev->ID, 'H:i' );

			$event_enddate		= Noo_Event::get_end_date( $ev->ID, 'm/d/Y' );
			$event_endtime      = Noo_Event::get_end_date( $ev->ID, 'H:i' );

			$event_lat		= noo_get_post_meta( $ev->ID, "_gmap_latitude", '' );
			$event_lon		= noo_get_post_meta( $ev->ID, "_gmap_longitude", '' );

			$my_post = array(
				'post_title'  => wp_strip_all_tags( $event_author ),
				'post_status' => 'publish',
				'post_author' => 1,
				'post_type'   => 'event_organizers',
			);
			 
			// Insert the post into the database
			$insert_id = wp_insert_post( $my_post );

			if ( $insert_id ) {
				update_post_meta( $ev->ID, '_noo_event_organizers', $insert_id);

				add_post_meta($insert_id, '_noo_event_author', $event_author);
				add_post_meta($insert_id, '_noo_event_phone', $event_phone);
				add_post_meta($insert_id, '_noo_event_website', $event_website);
				add_post_meta($insert_id, '_noo_event_email', $event_email);
			}


			update_post_meta( $ev->ID, '_noo_event_address', $event_address );

			update_post_meta( $ev->ID, '_noo_event_start_date', strtotime($event_startdate) );
			update_post_meta( $ev->ID, '_noo_event_start_time', strtotime($event_starttime) );

			update_post_meta( $ev->ID, '_noo_event_end_date', strtotime($event_enddate) );
            update_post_meta( $ev->ID, '_noo_event_end_time', strtotime($event_endtime) );

			update_post_meta( $ev->ID, '_noo_event_gmap_latitude', $event_lat );
			update_post_meta( $ev->ID, '_noo_event_gmap_longitude', $event_lon );

		}
	}
}