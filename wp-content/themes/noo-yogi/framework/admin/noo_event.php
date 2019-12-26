<?php
if ( ! class_exists( 'Noo_Event' ) ) :
	class Noo_Event {
		public function __construct() {
			
			add_shortcode('noo_event',array(&$this,'noo_event_shortcode'));
			add_filter('post_class', array(&$this,'post_class'),10.1,3);

			if ( ! is_admin() ) :
                add_action( 'pre_get_posts', array( &$this, 'pre_get_posts' ) );
            endif;
		}

		public function pre_get_posts($query){
			if( is_admin() || $query->is_singular ) {
				return;
			}

			if ( $query->is_main_query() && $query->is_post_type_archive( 'noo_event' ) ) {
				$orderby = noo_get_option('noo_events_orderby', 'startdate');
				$order = noo_get_option('noo_events_order', 'asc');
		        if( $orderby == 'startdate' ) {
		        	$query->set('meta_key', '_noo_event_start_date');
		        	$query->set('orderby', 'meta_value_num');
		        	$query->set('order', ( $order == 'asc' ? 'ASC' : 'DESC' ));
		        } else {
		        	$query->set('orderby', 'date');
		        	$query->set('order', ( $order == 'asc' ? 'ASC' : 'DESC' ));
		        }

		        if( noo_get_option('noo_events_hide_past', false) || NOO_Settings()->get_option('noo_event_hide_past', 'no')=='yes') {
		        	$query->set('meta_query', array(
		        		'relation' => 'AND',
		        		array(
		                    'key'     => '_noo_event_end_date',
		                    'value'   => strtotime(date('Y-m-d')),
		                    'compare' => '>='
		                ),
		                array(
		                    'key'     => '_noo_event_end_time',
		                    'value'   => strtotime(date('H:i:s')),
		                    'compare' => '>='
		                )
	        		));
		        }
			}
		}

		public static function get_up_comming_events() {
			global $wpdb;

			$events = (array) $wpdb->get_col(
					"SELECT $wpdb->posts.ID
					FROM $wpdb->posts
					LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
					WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'noo_event' AND $wpdb->postmeta.meta_key = '_noo_event_start_date' AND $wpdb->postmeta.meta_value > UNIX_TIMESTAMP()" );

			return $events;
		}


		public static function get_past_events() {
			global $wpdb;

			$events = (array) $wpdb->get_col(
					"SELECT $wpdb->posts.ID
					FROM $wpdb->posts
					LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
					WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'noo_event' AND $wpdb->postmeta.meta_key = '_noo_event_end_date' AND $wpdb->postmeta.meta_value <= UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -1 DAY))" );

			return $events;
		}

		/**
		 * Get the Event's Start date
		 * @param  int Event ID
		 * @param  mix $format date format, can be specific PHP format, false for now format or blank to get format from WordPress setting
		 * @return Unixtime if $format === false or Date base on format.
		 */
		public static function get_start_date( $event_id = null, $format = '' ) {
			$event_id = empty($event_id) ? get_the_ID() : $event_id;
			if( empty( $event_id ) ) return false;

			$start_date = noo_get_post_meta( $event_id, '_start_date', '' );

			if( empty( $start_date ) ) return false;

			if( !is_numeric($start_date) ) {
				// Convert old version to new unix format
				$start_date = strtotime($start_date);
				update_post_meta( $event_id, '_start_date', $start_date );
			}

			if( $format === false ) {
				return ( int ) $start_date;
			}

			$date_format = empty( $format ) ? get_option('date_format') . ' ' . get_option('time_format') : $format;

			return date_i18n($date_format, $start_date);
		}

		/**
		 * Get the Event's End date
		 * @param  int Event ID
		 * @param  mix $format date format, can be specific PHP format, false for now format or blank to get format from WordPress setting
		 * @return Unixtime if $format === false or Date base on format.
		 */
		public static function get_end_date( $event_id = null, $format = '' ) {
			$event_id = empty($event_id) ? get_the_ID() : $event_id;
			if( empty( $event_id ) ) return false;

			$end_date = noo_get_post_meta( $event_id, '_end_date', '' );

			if( empty( $end_date ) ) return false;

			if( !is_numeric($end_date) ) {
				// Convert old version to new unix format
				$end_date = strtotime($end_date);
				update_post_meta( $event_id, '_end_date', $end_date );
			}

			if( $format === false ) {
				return ( int ) $end_date;
			}

			$date_format = empty( $format ) ? get_option('date_format') . ' ' . get_option('time_format') : $format;

			return date_i18n($date_format, $end_date);
		}

		public function noo_event_shortcode($atts, $content = null){
			extract( shortcode_atts( array(
				'title'					=> '',
				'archive_link'			=>'no',
				'style'					=> '',
				'grid_col'				=> '2',
				'categories'			=> '',
				'locations'				=> '',
				'posts_per_page'		=> '10',
				'status'				=> '',
				'orderby'				=> 'startdate',
				'order'					=> 'desc',
				'excerpt_length'		=> '25',
				'show_pagination'		=> '',
				'visibility'         	=> '',
				'class'              	=> '',
				'id'                 	=> '',
				'custom_style'       	=> '',
			), $atts ) );
			
			$class            = ( $class              != '' ) ? esc_attr( $class ) : '' ;
			$visibility       = ( $visibility         != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
			switch ($visibility) {
				case 'hidden-phone':
					$class .= ' hidden-xs';
					break;
				case 'hidden-tablet':
					$class .= ' hidden-sm hidden-md';
					break;
				case 'hidden-pc':
					$class .= ' hidden-lg';
					break;
				case 'visible-phone':
					$class .= ' visible-xs-block visible-xs-inline visible-xs-inline-block';
					break;
				case 'visible-tablet':
					$class .= ' visible-sm-block visible-sm-inline visible-sm-inline-block visible-md-block visible-md-inline visible-md-inline-block';
					break;
				case 'visible-phone':
					$class .= ' visible-lg-block visible-lg-inline visible-lg-inline-block';
					break;
			}
			if( is_front_page() || is_home()) {
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
			} else {
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			}
			$args = array(
				'paged'			  	  => $paged,
				'orderby'         	  => "date",
				'order'           	  => "DESC",
				'posts_per_page'      => $posts_per_page,
				'post_status'         => 'publish',
				'post_type'			  =>'noo_event',
				
			);

			if ( $orderby == 'adddate' ) {
				$args['orderby'] = 'date';
				$args['order'] = ($order == 'asc') ? 'ASC' : 'DESC';
			} else {
				$args['meta_key'] = '_start_date';
				$args['orderby'] = 'meta_value_num date';
				$args['order'] = ($order == 'asc') ? 'ASC' : 'DESC';
			}

			$tax_query = array();
			if ( ! empty( $categories ) ) {
				$tax_query[] = array( 'taxonomy' => 'event_category',
						'field' => 'term_id',
						'terms' => explode(',', $categories)
					);
			}
			if ( ! empty( $locations ) ) {
				$tax_query[] = array( 'taxonomy' => 'event_location',
						'field' => 'term_id',
						'terms' => explode(',', $locations)
					);
			}

			if ( ! empty( $tax_query ) ) {
				$args['tax_query'] = $tax_query;
			}

			if( $status == 'up_comming' ) {
				$up_comming = self::get_up_comming_events();
				if( is_array( $up_comming ) && !empty( $up_comming ) ) {
					$args['post__in'] = $up_comming;
				}
			}

			if( $status == 'past' ) {
				$past = self::get_past_events();
				if( is_array( $past ) && !empty( $past ) ) {
					$args['post__in'] = $past;
				}
			}

			$pagination  = $show_pagination === 'yes' ? 1 : 0;
			$r = new WP_Query($args);
			ob_start();
			self::loop_display(array(
				'title'=>$title,
				'archive_link' => ($archive_link === 'yes' ? 1 : 0 ),
				'mode_style'=>$style,
				'query'=>$r,
				'col'=>$grid_col,
				'pagination'=>$pagination,
				'excerpt_length'=>$excerpt_length
			));
			return ob_get_clean();
		}
		
		
		public function post_class($classes, $class, $post_id){
			if('noo_event' === get_post_type($post_id) ){
				$start_date = Noo_Event::get_start_date( $post_id, false );
				$end_date = Noo_Event::get_end_date( $post_id, false );
				
				if($start_date && $start_date < $end_date && $start_date > time()){
					$classes[] = 'noo-upcoming-event';
				}
				if($end_date && $start_date < $end_date && $end_date < time()){
					$classes[] = 'noo-past-event';
				}
			}
			return $classes;
		}

		public static function loop_display( $args = '' ) {
			wp_enqueue_script('vendor-isotope');
			
			global $wp_query;
			$defaults = array(
				'echo'=>true,
				'title'=>'',
				'excerpt_length'=>30
			);
			$post_class_col = NOO_Settings()->get_option('noo_event_grid_column', 2);
			$args = wp_parse_args($args,$defaults);
			extract($args);
			ob_start();
			// Load display style
			$choose_style_class = '';
			$choose_style = NOO_Settings()->get_option('noo_event_default_layout', 'grid');
			if ( $choose_style == 'list' ) {
				$post_class = 'loadmore-item masonry-item col-md-12';
				$choose_style_class = 'col-sm-6';
			} else {
				$post_class = 'loadmore-item masonry-item col-sm-6 col-md-'.absint((12 / $post_class_col));
			}
			
			?>
			<div class="posts-loop masonry  noo-events">
				<div class="row posts-loop-content loadmore-wrap">
					<div class="masonry-container">
						<?php

						?>
						<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); global $post; ?>
						<article <?php  post_class($post_class); ?>>

							<?php $is_featured = get_post_meta( $post->ID, 'event_is_featured', true ); ?>
							<div class="loop-item-wrap">
								<?php if(has_post_thumbnail()):?>
							    <div class="loop-item-featured <?php echo $choose_style_class ?>">
							        <a href="<?php the_permalink() ?>">
									        <?php
									          	if ( $is_featured == '1' ) :
							                        echo '<span class="is_featured">' . esc_html__( 'Featured', 'noo-timetable' ) . '</span>';
							                    endif;
									        ?>
										<?php the_post_thumbnail('noo-thumbnail-square')?>
									</a>
							    </div>
							    <?php endif;?>
								<div class="loop-item-content <?php echo $choose_style_class ?>">
									<div class="loop-item-content-summary">
										<div class="loop-item-category"><?php echo get_the_term_list(get_the_ID(), 'event_category',' ',', ')?></div>
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
										<?php 
										
										$event_address		= noo_get_post_meta( get_the_ID(), "_address", '' );
										
										$event_startdate	= noo_get_post_meta( get_the_ID(), "_noo_event_start_date", '' );
										$event_starttime	= noo_get_post_meta( get_the_ID(), "_noo_event_start_time", '' );

										?>
										<div class="event-info">
											<?php if( !empty( $event_starttime ) && !noo_get_option('noo_events_hide_time', false) ) : ?>
												<div>
													<i class="fa fa-clock-o"></i>&nbsp;<?php echo date_i18n(get_option('time_format'),$event_starttime); ?>
												</div>
											<?php endif; ?>
											<?php if( !empty( $event_startdate ) && !noo_get_option('noo_events_hide_date', false) ) : ?>
												<div>
													<i class="fa fa-calendar"></i>&nbsp;<?php echo date_i18n(get_option('date_format'),$event_startdate); ?>
												</div>
											<?php endif; ?>
											<?php if( !empty($event_address) && !noo_get_option('noo_events_hide_location', false) ) : ?>
												<div>
													<i class="fa fa-map-marker"></i>&nbsp;<?php echo esc_html($event_address);?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="loop-item-action">
                                        <a class="btn btn-default btn-block text-uppercase" href="<?php the_permalink()?>"><?php echo __('Learn More','noo')?></a>
                                    </div>
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
			<?php
			wp_reset_query();
			if($echo)
				echo ob_get_clean();
			else 
				return ob_get_clean();
		}
	}
	new Noo_Event();
endif;