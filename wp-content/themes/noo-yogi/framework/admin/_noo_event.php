<?php
if ( ! class_exists( 'Noo_Event' ) ) :
	class Noo_Event {
		public function __construct() {
			add_action( 'init', array( &$this, 'register_post_type' ),0 );
			add_filter( 'template_include', array( $this, 'template_loader' ) );
			add_shortcode('noo_event',array(&$this,'noo_event_shortcode'));
			add_action('pre_get_posts', array(&$this,'pre_get_posts'));
			add_filter('post_class', array(&$this,'post_class'),10,3);
			if ( is_admin() ) :
				add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ), 30 );
				add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
				
				// add_action( 'admin_init', array(&$this,'permalink_setting') );
				// add_action( 'admin_init',array(&$this, 'permalink_setting_save') );
				
				//Events Columns
				add_filter( 'manage_edit-noo_event_columns', array($this,'manage_edit_columns') );
				add_filter( 'manage_noo_event_posts_custom_column',  array($this,'manage_custom_column'), 2 );

				add_action( 'customize_save', array($this,'customizer_set_transients_before_save') );
				add_action( 'customize_save_after', array($this,'customizer_set_transients_after_save') );
				
			endif;
		}
		
		public function template_loader($template){
			if(is_post_type_archive( 'noo_event' ) || is_tax( 'event_category' )  || is_tax( 'event_location' ) ) {
				$template       = locate_template( 'archive-noo_event.php' );
			}
			return $template;
		}

		public static function get_up_comming_events() {
			global $wpdb;

			$events = (array) $wpdb->get_col(
					"SELECT $wpdb->posts.ID
					FROM $wpdb->posts
					LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
					WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'noo_event' AND $wpdb->postmeta.meta_key = '_start_date' AND $wpdb->postmeta.meta_value > UNIX_TIMESTAMP()" );

			return $events;
		}


		public static function get_past_events() {
			global $wpdb;

			$events = (array) $wpdb->get_col(
					"SELECT $wpdb->posts.ID
					FROM $wpdb->posts
					LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
					WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'noo_event' AND $wpdb->postmeta.meta_key = '_end_date' AND $wpdb->postmeta.meta_value <= UNIX_TIMESTAMP()" );

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


		public function add_meta_boxes() {
			$meta_box = array( 
				'id' => "event_settings", 
				'title' => __( 'Event Settings', 'noo' ), 
				'page' => 'noo_event', 
				'context' => 'normal', 
				'priority' => 'high', 
				'fields' => array( 
					array( 
						'id' => '_start_date', 
						'label' => __( 'Start Date', 'noo' ), 
						'type' => 'datetimepicker', 
						'callback' => array( &$this, 'meta_box_datetimepicker' ) ),
					array( 
						'id' => '_end_date', 
						'label' => __( 'End Date', 'noo' ), 
						'type' => 'datetimepicker', 
						'callback' => array( &$this, 'meta_box_datetimepicker' ) ),
					array( 'id' => '_author', 'label' => __( 'Author', 'noo' ), 'type' => 'text' ), 
					array( 'id' => '_address', 'label' => __( 'Address', 'noo' ), 'type' => 'text' ), 
					array( 'id' => '_phone', 'label' => __( 'Phone', 'noo' ), 'type' => 'text' ), 
					array( 'id' => '_website', 'label' => __( 'Website', 'noo' ), 'type' => 'text' ), 
					array( 'id' => '_email', 'label' => __( 'Email', 'noo' ), 'type' => 'text' ),
				) );
			// Create a callback function
			$callback = create_function( '$post,$meta_box', 'noo_create_meta_box( $post, $meta_box["args"] );' );
			add_meta_box( 
				$meta_box['id'], 
				$meta_box['title'], 
				$callback, 
				$meta_box['page'], 
				$meta_box['context'], 
				$meta_box['priority'], 
				$meta_box );
			
			$meta_box = array( 
				'id' => "event_map", 
				'title' => __( 'Place in Map', 'noo' ), 
				'page' => 'noo_event', 
				'context' => 'normal', 
				'priority' => 'high', 
				'fields' => array( 
					array( 
						'id' => '_gmap', 
						'type' => 'gmap', 
						'callback' => array( &$this, 'meta_box_google_map' ) ), 
					array( 
						'label' => __( 'Latitude', 'noo' ), 
						'id' => '_gmap_latitude', 
						'default'=>'40.71421714027808',
						'type' => 'text',
					),
					array( 
						'label' => __( 'Longitude', 'noo' ), 
						'id' => '_gmap_longitude',
						'default'=>'-74.00538682937622',
						'type' => 'text',
					)
				)
			);
			$callback = create_function( '$post,$meta_box', 'noo_create_meta_box( $post, $meta_box["args"] );' );
			add_meta_box( 
				$meta_box['id'], 
				$meta_box['title'], 
				$callback, 
				$meta_box['page'], 
				$meta_box['context'], 
				$meta_box['priority'], 
				$meta_box );
		}
		
		public function manage_edit_columns( $columns ) {
			$new_columns = array();
			$new_columns['cb'] = $columns['cb'];
			$new_columns['title'] = $columns['title'];
			$new_columns['start_date'] = __('Start Date','noo');
			$new_columns['end_date'] = __( 'End Date', 'noo' );
			unset( $columns['cb'] );
			unset( $columns['title'] );
			return array_merge( $new_columns, $columns );
		}
		
		public function manage_custom_column( $column) {
			global $post;
			$event_startdate	= Noo_Event::get_start_date( get_the_ID(), false );
			$event_enddate		= Noo_Event::get_end_date( get_the_ID(), false );
			if($column === 'start_date'){
				echo date_i18n(__( 'Y/m/d g:i:s a', 'noo' ),$event_startdate);
			}elseif ($column === 'end_date'){
				echo date_i18n(__( 'Y/m/d g:i:s a', 'noo' ),$event_enddate);
			}
			return $column;
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
		
		public function pre_get_posts($query){
			if( is_admin() || $query->is_singular ) {
				return;
			}

			if ( $query->is_main_query() && self::is_noo_event_query($query) ) {
				$orderby = noo_get_option('noo_events_orderby', 'startdate');
				$order = noo_get_option('noo_events_order', 'asc');
		        if( $orderby == 'startdate' ) {
		        	$query->set('meta_key', '_start_date');
		        	$query->set('orderby', 'meta_value_num');
		        	$query->set('order', ( $order == 'asc' ? 'ASC' : 'DESC' ));
		        } else {
		        	$query->set('orderby', 'date');
		        	$query->set('order', ( $order == 'asc' ? 'ASC' : 'DESC' ));
		        }

		        if( noo_get_option('noo_events_hide_past', false) ) {
		        	$query->set('post__not_in', self::get_past_events());
		        }
			}
		}

		public static function is_noo_event_query( $query = null ) {
			if( empty( $query ) ) return false;

			if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] === 'noo_event' )
				return true;

			if( $query->is_tax ) {
				if( ( isset( $query->query_vars['event_category'] ) && !empty( $query->query_vars['event_category'] ) ) ) {
					return true;
				}
			}

			return false;
		}

		public static function loop_display( $args = '' ) {
			wp_enqueue_script('vendor-isotope');
			
			global $wp_query;
			$defaults = array(
				'echo'=>true,
				'title'=>'',
				'excerpt_length'=>30
			);
			$post_class_col = noo_get_option('noo_events_number_columns', 3);
			$args = wp_parse_args($args,$defaults);
			extract($args);
			ob_start();
			// Load display style
			$choose_style_class = '';
			$choose_style = noo_get_option( 'noo_events_choose_style', 'grid' );
			if ( $choose_style == 'list' ) {
				$post_class = 'loadmore-item masonry-item col-md-12';
				$choose_style_class = 'col-sm-6';
			} else {
				$post_class = 'loadmore-item masonry-item col-sm-6 col-md-'.absint((12 / $post_class_col));
			}
			
			?>
			<div class="posts-loop masonry  noo-events">
				<?php /*?>
				<div class="posts-loop-title">
					<h3><?php echo $title?></h3>
					<span class="loop-view-mode">
						<a class="grid-mode<?php echo ($current_view_mode == 'grid' ? ' active' :'')?>" title="<?php esc_attr_e('Grid','noo')?>" <?php echo ($grid_mode_href)?>><i class="fa fa-th"></i></a>
						<a class="list-mode<?php echo ($current_view_mode == 'list' ? ' active' :'')?>" title="<?php esc_attr_e('List','noo')?>" <?php echo ($list_mode_href) ?>><i class="fa fa-th-list"></i></a>	
					</span>
				</div>
				*/ ?>
				<div class="row posts-loop-content loadmore-wrap">
					<div class="masonry-container">
						<?php

						?>
						<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); global $post; ?>
						<article <?php  post_class($post_class); ?>>
							<div class="loop-item-wrap">
								<?php if(has_post_thumbnail()):?>
							    <div class="loop-item-featured <?php echo $choose_style_class ?>">
							        <a href="<?php the_permalink() ?>">
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
										$event_startdate	= Noo_Event::get_start_date( get_the_ID(), false );
										?>
										<div class="event-info">
											<?php if( !empty( $event_startdate ) && !noo_get_option('noo_events_hide_time', false) ) : ?>
												<div>
													<i class="fa fa-clock-o"></i>&nbsp;<?php echo date_i18n(get_option('time_format'),$event_startdate); ?>
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

		public function meta_box_datetimepicker( $post, $id, $type, $meta, $std, $field ) {
			wp_enqueue_script( 'vendor-datetimepicker' );
			wp_enqueue_style( 'vendor-datetimepicker' );
			$date_format = 'm/d/Y H:i';

			// @TODO: this function is kept because of the old data in previous version
			// should change to the version in core file generate-meta-box.php in near future.
			$date_text = is_numeric( $meta ) ? date( $date_format, $meta ) : $meta;
			$date = is_numeric( $meta ) ? $meta : strtotime( $meta );

			echo '<div>';
			echo '<input type="text" readonly class="input_text" id="' . $id . '" value="' .
				 esc_attr( $date_text ) . '" /> ';
			echo '<input type="hidden" name="noo_meta_boxes[' . $id . ']" value="' .
				 esc_attr( $date ) . '" /> ';
			echo '</div>';
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$('#<?php echo esc_js($id); ?>').datetimepicker({
						format:"<?php echo esc_html( $date_format ); ?>",
						step:15,
						onChangeDateTime:function(dp,$input){
							$input.next('input[type="hidden"]').val(parseInt(dp.getTime()/1000)-60*dp.getTimezoneOffset()); // correct the timezone of browser.
						}
					});
				});
			</script>
<?php
		}

		public function meta_box_google_map( $post, $meta_box ) {
		?>

<style>
<!--
._gmap .noo-control {
	float: none;
	width: 100%;
}
-->
</style>
<div class="noo_event_google_map">
	<div id="noo_event_google_map" class="noo_event_google_map"
		style="height: 380px; margin-bottom: 30px; overflow: hidden; position: relative; width: 100%;">
	</div>
	<div class="noo_event_google_map_search">
		<input
			placeholder="<?php echo __('Search your map','noo')?>"
			type="text" autocomplete="off" id="noo_event_google_map_search_input">
	</div>
</div>
<?php
		}

		public function enqueue_scripts() {
			if(get_post_type() === 'noo_event'){
				global $post;
				wp_enqueue_style( 'noo-event', NOO_FRAMEWORK_ADMIN_URI . '/assets/css/noo_event.css');
				
				$latitude = '40.714398';
				if($lat = noo_get_post_meta($post->ID,'_gmap_latitude'))
					$latitude = $lat;
				
				$longitude = '-74.005279';
				if($long = noo_get_post_meta($post->ID,'_gmap_longitude'))
					$longitude = $long;

				$nooEventMap = array(
					'latitude'=>$latitude,
					'longitude'=>$longitude,
					'localtion_disable'=>false
				);
				wp_register_script('google-map','http'.(is_ssl() ? 's':'').'://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places',array('jquery'), '1.0', false);
				wp_register_script( 'noo-event', NOO_FRAMEWORK_ADMIN_URI . '/assets/js/noo_event.js', array( 'jquery','google-map'), null, true );
				wp_localize_script('noo-event', 'nooEventMap', $nooEventMap);
				wp_enqueue_script('noo-event');
			}
		}
		
		public function  permalink_setting(){
			add_settings_field(
			'archive_event_slug',      		// id
			__( 'Archive Event Base', 'noo' ), 	// setting title
			array(&$this,'archive_event_slug_input'),  		// display callback
			'permalink',                 				// settings page
			'optional'                  				// settings section
			);
		}
		
		public function archive_event_slug_input(){
			$permalinks = get_option( 'noo_event_permalinks' );
			?>
				<input name="archive_event_slug_input" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['archive_event_slug'] ) ) echo esc_attr( $permalinks['archive_event_slug'] ); ?>" placeholder="<?php echo _x('events', 'slug', 'noo') ?>" />
				<?php
			}
			
			public function permalink_setting_save(){
				if (is_admin()){
					if (isset($_POST['archive_event_slug_input'])){
							
						$archive_event_slug = sanitize_text_field( $_POST['archive_event_slug_input'] );
							
						$permalinks = get_option( 'noo_event_permalinks' );
						if ( ! $permalinks )
							$permalinks = array();
							
						$permalinks['archive_event_slug'] 	= untrailingslashit( $archive_event_slug );
							
						update_option( 'noo_event_permalinks', $permalinks );
					}
				}
				return;
			}

		public function register_post_type() {
			if ( post_type_exists( 'noo_event' ) )
				return;

			if ( get_transient( 'noo_event_slug_before' ) != get_transient( 'noo_event_slug_after' ) ) {
				flush_rewrite_rules( false );
				delete_transient( 'noo_event_slug_before' );
				delete_transient( 'noo_event_slug_after' );
			}
			
			$event_page = noo_get_option('noo_events_page', '');
			$event_slug = !empty($event_page) ? get_post( $event_page )->post_name : 'events';
				
			
			register_post_type( 
				'noo_event', 
				array( 
					'labels' => array( 
						'name' => __( 'Events', 'noo' ), 
						'singular_name' => __( 'Event', 'noo' ), 
						'add_new' => __( 'Add New Event', 'noo' ), 
						'add_new_item' => __( 'Add Event', 'noo' ), 
						'edit' => __( 'Edit', 'noo' ), 
						'edit_item' => __( 'Edit Event', 'noo' ), 
						'new_item' => __( 'New Event', 'noo' ), 
						'view' => __( 'View', 'noo' ), 
						'view_item' => __( 'View Event', 'noo' ), 
						'search_items' => __( 'Search Event', 'noo' ), 
						'not_found' => __( 'No Events found', 'noo' ), 
						'not_found_in_trash' => __( 'No Events found in Trash', 'noo' ), 
						'parent' => __( 'Parent Event', 'noo' ) ), 
					'public' => true, 
					'has_archive' => true, 
					'menu_icon' => 'dashicons-megaphone', 
					'rewrite' => array( 'slug' => $event_slug, 'with_front' => false ), 
					'supports' => array( 'title', 'editor','excerpt', 'thumbnail', 'comments' ), 
					'can_export' => true ) );
			
			register_taxonomy( 
				'event_category', 
				'noo_event', 
				array( 
					'labels' => array( 
						'name' => __( 'Event Category', 'noo' ), 
						'add_new_item' => __( 'Add New Event Category', 'noo' ), 
						'new_item_name' => __( 'New Event Category', 'noo' ) ), 
					'hierarchical' => true, 
					'query_var' => true, 
					'rewrite' => array( 'slug' => 'event-category' ) ) );
			
			register_taxonomy(
				'event_location',
				'noo_event',
				array(
					'labels' => array(
						'name' => __( 'Event Location', 'noo' ),
						'add_new_item' => __( 'Add New Event Location', 'noo' ),
						'new_item_name' => __( 'New Event Location', 'noo' ) ),
					'hierarchical' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'event-location' ) ) );
		}

		public function customizer_set_transients_before_save() {
			set_transient( 'noo_event_slug_before', noo_get_option( 'noo_events_page', 'events' ), 60 );
		}

		public function customizer_set_transients_after_save() {
			set_transient( 'noo_event_slug_after', get_option( 'noo_events_page', 'events' ), 60 );
		}
	}
	new Noo_Event();
endif;