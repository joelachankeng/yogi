<?php
if ( ! class_exists( 'Noo_Class' ) ) :
class Noo_Class {

	public function __construct() {
		add_action( 'init', array( &$this, 'register_post_type' ), 0 );
		add_filter( 'template_include', array( $this, 'template_loader' ) );
		add_shortcode('noo_class_schedule', array(&$this,'schedule_shortcode'));
		if ( is_admin() ) :
			add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ), 30 );
		
			// add_action( 'admin_init', array(&$this,'permalink_setting') );
			// add_action( 'admin_init',array(&$this, 'permalink_setting_save') );
			
			add_action( 'class_category_add_form_fields',array($this,'class_category_add_meta_field'), 100, 2 );
			add_action( 'class_category_edit_form_fields',array($this,'class_category_edit_meta_field'), 100, 2 );
			add_action( 'edited_class_category', array($this,'class_category_save_meta_field'), 10, 2 );
			add_action( 'create_class_category', array($this,'class_category_save_meta_field'), 10, 2 );
			
			//Classes Columns
			add_filter( 'manage_edit-noo_class_columns', array($this,'manage_edit_columns') );
			add_filter( 'manage_noo_class_posts_custom_column',  array($this,'manage_custom_column'), 2 );

			add_action( 'customize_save', array($this,'customizer_set_transients_before_save') );
			add_action( 'customize_save_after', array($this,'customizer_set_transients_after_save') );
			
		else:
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ),100);
		endif;

		add_action( 'wp_ajax_noo_class_filter', array(&$this, 'class_filter') );
		add_action( 'wp_ajax_nopriv_noo_class_filter', array(&$this, 'class_filter') );

		add_action( 'wp_ajax_noo_class_responsive_navigation', array(&$this, 'class_responsive_navigation') );
        add_action( 'wp_ajax_nopriv_noo_class_responsive_navigation', array(&$this, 'class_responsive_navigation') );

        add_action( 'wp_ajax_noo_class_get_count_arrange', array(&$this, 'class_get_count_arrange') );
        add_action( 'wp_ajax_nopriv_noo_class_get_count_arrange', array(&$this, 'class_get_count_arrange') );

		// Set schedule for update next_class
		add_action( 'noo-class-update-next-day', array( $this, 'update_next_day' ) );
        add_action( 'save_post', array( $this, 'setup_one_class_schedule' ) );
        add_action( 'init', array( &$this, 'setup_classes' ), 0 );
	}


	public function setup_one_class_schedule( $post_id ) {
        // Check if this post is noo_class
        // Check if publish
        if ( 'noo_class' == get_post_type( $post_id ) ) {
        	// Clear old schedule
		    wp_clear_scheduled_hook( 'noo-class-update-next-day', array( $post_id ) );
        	if ( 'publish' == get_post_status( $post_id ) ) {	
		        $this->update_next_day( $post_id );
        	}
        }
    }

	public function setup_classes() {
        // Once time
        // 
        if( get_option( 'has_setup_classes' ) ) {
			return;
        }
        update_option( 'has_setup_classes', 1 );
        // Filter class and setup the first chedule
        // Loop for all classes
		global $wpdb;
		$classes = (array) $wpdb->get_results(
				"SELECT $wpdb->posts.ID, $wpdb->postmeta.meta_key, $wpdb->postmeta.meta_value
				FROM $wpdb->posts
				LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
				WHERE $wpdb->posts.post_status = 'publish' 
				AND $wpdb->posts.post_type = 'noo_class'
				AND ($wpdb->postmeta.meta_key = '_open_date' OR $wpdb->postmeta.meta_key = '_number_of_weeks' OR $wpdb->postmeta.meta_key = '_number_day' OR $wpdb->postmeta.meta_key = '_open_time') " );

		$newarr = array();
		if ( $classes ){
			foreach ($classes as $k => $v) {
				$newarr[$v->ID][$v->meta_key] = $v->meta_value;
			}
		}
		if ( !empty( $newarr ) ){
			foreach ($newarr as $k => $v) {
				if (!isset($v['_open_date']) || $v['_open_date'] == '')
					continue;
				$class_id = $k;
				$number_of_weeks = $v['_number_of_weeks'] != '' ? $v['_number_of_weeks'] : 1;
				$end_date = strtotime("+" . $number_of_weeks . " week", $v['_open_date']);
				$next_date = noo_get_post_meta( $class_id, "_next_date", '' );
				
				if ( $next_date == '' || ( time() >= $next_date && time() <= $end_date ) ){
					$next_date = self::_get_next_date(array(
						'open_date'       => $v['_open_date'],
						'open_time'       => $v['_open_time'],
						'number_of_weeks' => $v['_number_of_weeks'],
						'number_days'     => (array) noo_json_decode( $v['_number_day'] )
					));
					if ( $next_date != '' ){
						update_post_meta( $class_id, '_next_date', $next_date );
						// Create cron
						wp_schedule_single_event( $next_date, 'noo-class-update-next-day', array( $class_id ) );
					}
					else{
						update_post_meta( $class_id, '_next_date', $end_date );
					}
				}
			}
		}
    }

    public function update_next_day( $class_id ) {
        // Calculate and update next day.

		$open_date       = noo_get_post_meta( $class_id, "_open_date", '' );
		$open_time       = noo_get_post_meta( $class_id, "_open_time", '' );
		$number_of_weeks =  (int) noo_get_post_meta( $class_id, "_number_of_weeks", '1' );
		$number_days     =  noo_get_post_meta( $class_id, "_number_day", '' );
    	
		$next_date = self::_get_next_date(array(
			'open_date'       => $open_date,
			'open_time'       => $open_time,
			'number_of_weeks' => $number_of_weeks,
			'number_days'     => (array) noo_json_decode( $number_days )
		));
		
		update_post_meta( $class_id, '_next_date', $next_date );
		if ( $next_date != '' ){
			// Create cron
			wp_schedule_single_event( $next_date, 'noo-class-update-next-day', array( $class_id ) );
		}
    }

	public function template_loader( $template ) {
		if ( is_post_type_archive( 'noo_class' ) || is_tax( 'class_category' ) || is_tax( 'class_level' ) ) {
			$template = locate_template( 'archive-noo_class.php' );
		}
		return $template;
	}
	
	public function pre_get_posts($q){
		if ( ! $q->is_main_query() ) {
			return;
		}

		if ( is_post_type_archive('noo_class') && $q->get('post_type') == 'noo_class' ) {
			$orderby = noo_get_option('noo_classes_orderby', 'opendate');
			$order = noo_get_option('noo_classes_order', 'asc');
	        if( $orderby == 'opendate' ) {
	        	$q->set('meta_key', '_open_date');
	        	$q->set('orderby', 'meta_value_num');
	        	$q->set('order', ( $order == 'asc' ? 'ASC' : 'DESC' ));
	        } else {
	        	$q->set('orderby', 'date');
	        	$q->set('order', ( $order == 'asc' ? 'ASC' : 'DESC' ));
	        }

	        if ( noo_get_option('noo_classes_show_loadmore', true) ) {

		        $number = noo_get_option('noo_classes_number_class', 6);
				if ( is_numeric($number) ){
					$q->set('posts_per_page', $number);
				}
	        } else {
				$q->set('posts_per_page', -1);
			}

			if ( noo_get_option('noo_classes_hide_past', false) ) {

				$q->set('meta_query', array(
	                array(
	                    'key'     => '_next_date',
	                    'value'   => time(),
	                    'compare' => '>='
	                ),
	            ));
			}

		}
		
		if(isset($_GET['trainer']) && is_post_type_archive('noo_class') && $q->get('post_type') == 'noo_class'){
			if( isset($_GET['load']) && $_GET['load'] == 'all' ) {
				$q->set('posts_per_page', -1);
			}
			// $trainer = absint($_GET['trainer']);
			// if(get_post_type($trainer) === 'noo_trainer'){
			// 	$meta_query = array(
			// 	    'key'     => '_trainer',
			// 	    'value'   => $trainer,
			// 	    'compare' => '=',
			// 		'type'	  => 'NUMERIC'
			// 	);
			// 	$q_meta_query = $q->get('meta_query');
			// 	if ( ! is_array( $q_meta_query ) )
			// 		$q_meta_query = array();
			// 	$q_meta_query[] = $meta_query;
			// 	$q->set( 'meta_query', $q_meta_query );
			// }
		}
	}
	
	public function manage_edit_columns( $columns ) {
		$new_columns = array();
		$new_columns['cb'] = $columns['cb'];
		$new_columns['title'] = $columns['title'];
		$new_columns['open_date'] = __('Open Date','noo');
		$new_columns['number_day'] = __('Number Days','noo');
		$new_columns['trainer'] = __( 'Trainer', 'noo' );
		unset( $columns['cb'] );
		unset( $columns['title'] );
		return array_merge( $new_columns, $columns );
	}
	
	public function manage_custom_column( $column) {
		global $post,$wp_locale;
		$open_date		    = noo_get_post_meta( $post->ID, "_open_date", '' );
		$number_days		= noo_json_decode( noo_get_post_meta( get_the_ID(), "_number_day", '' ) );
		if($column === 'trainer'){
			if($trainer_id = noo_get_post_meta(get_the_ID(),'_trainer')){
				if (!is_array($trainer_id)) {
					$trainer_id = str_replace(array('[', ']', '"'), array('', '', ''), $trainer_id);	
					$trainer_id = explode(',', $trainer_id);
				} 
				foreach ($trainer_id as $itd => $tid) {
					echo edit_post_link(get_the_title($tid),'','',$tid);
					if ( ($itd + 1) < count($trainer_id) ) echo ', ';	
				}
			}else{
				echo '&ndash;';
			}
		}elseif ($column === 'open_date'){
			if ($open_date != '')
				echo date_i18n(__( 'Y/m/d', 'noo' ),$open_date);
		}elseif ($column === 'number_day'){
			$number_day_arr = array();
			?>
			<?php foreach ((array)$number_days as $number_day) :?>
            	<?php if( $number_day !== '' && $number_day !== null ) $number_day_arr[] = esc_html($wp_locale->get_weekday_abbrev($wp_locale->get_weekday($number_day))); ?>
			<?php endforeach; ?>
			<?php echo implode(' - ', $number_day_arr)?>
			<?php
		}
		return $column;
	}
	
	public function add_meta_boxes(){
		global $wp_locale;
		$weekday_options = array();
		$start_of_week = (int)get_option('start_of_week');
		for ($day_index = $start_of_week; $day_index <= 7 + $start_of_week; $day_index++) :
			$weekday_options[$day_index%7] = $wp_locale->get_weekday($day_index%7);
		endfor;
		
		$args = array(
			'post_type'     => 'noo_trainer',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'suppress_filters' => 0
		);
			
		$trainers = get_posts($args); //new WP_Query($args);
		$trainer_options = array();
		// $trainer_options[] = array('label'=>__('Select Trainer&hellip;','noo'),'value'=>'');

		if(!empty($trainers)){
			foreach ($trainers as $trainer){
				// $trainer_options[] = array('label'=>$trainer->post_title,'value'=>$trainer->ID);
				$trainer_options[$trainer->ID] = $trainer->post_title;
			}
		}

		$meta_box = array(
			'id' => "class_settings",
			'title' => __( 'Settings', 'noo' ),
			'page' => 'noo_class',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				array(
					'id' => '_open_date',
					'label' => __( 'Open Date', 'noo' ),
					'type' => 'datepicker',
					'callback' => array( &$this, 'meta_box_datepicker' ) ),
				array(
					'id' => '_open_time',
					'label' => __( 'Open Time', 'noo' ),
					'type' => 'timepicker',
					'callback' => array( &$this, 'meta_box_timepicker' ) ),
				array(
					'id' => '_close_time',
					'label' => __( 'Close Time', 'noo' ),
					'type' => 'timepicker',
					'callback' => array( &$this, 'meta_box_timepicker' ) ),
				// array( 'id' => '_trainer', 'label' => __( 'Trainer', 'noo' ), 'type' => 'select','options'=>$trainer_options ),
				array( 
					'id' => '_trainer', 
					'label' => __( 'Trainer', 'noo' ), 
					'type' => 'select_multiple', 
					'options' => $trainer_options, 
					'callback' => array( &$this, 'meta_box_select_multiple' ) ),
				array( 'id' => '_address', 'label' => __( 'Address', 'noo' ), 'type' => 'text' ),
				array( 'id' => '_map', 'label' => __( 'Link map', 'noo' ), 'type' => 'text' ),
				array( 'id' => '_number_of_weeks', 'label' => __( 'Number of Weeks', 'noo' ), 'type' => 'text' ),
				array( 
					'id' => '_number_day', 
					'label' => __( 'Number Days', 'noo' ), 
					'type' => 'select_multiple', 
					'options' => $weekday_options, 
					'callback' => array( &$this, 'meta_box_select_multiple' ) ),
				array( 
					'id' => '_use_advanced_multi_time', 
					'label' => __( 'Use Advanced Schedule', 'noo' ), 
					'type' => 'checkbox'
				),
				array( 
					'id' => '_advanced_multi_time', 
					'label' => __( 'Advanced Schedule', 'noo' ), 
					'options' => $weekday_options,
					'type' => 'select_multiple',
					'callback' => array( &$this, 'meta_box_tab_multiple' )
				),
				array( 'type' => 'divider' ),
				array( 'id' => '_register_link',
					'label' => __( 'Register Link', 'noo' ),
					'type' => 'text',
					'desc' => __( 'Use this if you want to link the registration somewhere.', 'noo' ),
					'std' => ''
					) ) );
		// Create a callback function
		$callback = create_function( '$post,$meta_box', 'noo_create_meta_box( $post, $meta_box["args"] );' );
		add_meta_box(
			$meta_box['id'],
			$meta_box['title'],
			$callback,
			$meta_box['page'],
			$meta_box['context'],
			$meta_box['priority'],
			$meta_box 
		);
	}

	public function meta_box_tab_multiple($post, $id, $type, $meta, $std, $field){
		wp_register_style('noo-jquery-ui', NOO_FRAMEWORK_URI.'/admin/assets/css/jquery-ui.css');
		wp_enqueue_style('noo-jquery-ui');
		wp_enqueue_script( 'jquery-ui-tabs' );

		wp_enqueue_script( 'vendor-datetimepicker' );
		wp_enqueue_style( 'vendor-datetimepicker' );

		$args = array(
			'post_type'     => 'noo_trainer',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'suppress_filters' => 0
		);

		$trainers = get_posts($args); //new WP_Query($args);
		$trainer_options = array();
		$trainer_options[] = array('label'=>__('Select Trainer&hellip;','noo'),'value'=>'');

		if(!empty($trainers)){
			foreach ($trainers as $trainer){
				$trainer_options[] = array('label'=>$trainer->post_title,'value'=>$trainer->ID);
			}
		}

		?>
		<div id="tabs">
			<ul>
			<?php
			if( isset( $field['options'] ) && !empty( $field['options'] ) ) {
				foreach ( $field['options'] as $key=>$option ) {
					echo '<li><a href="#tabs-'.$key.'">'.$option.'</a></li>';
				}
			}
			?>
			</ul>
			<?php
				$time_format = 'H:i';
				foreach ( $field['options'] as $key=>$option ) {
					$opt_value      = $key;
					$opt_label      = $option;
					$new_open_id    = '_open_time_' . $key;
					$new_closed_id  = '_closed_time_' . $key;	
					$new_trainer_id = '_trainer_' . $key;
					$new_address_id = '_address_' . $key;
					$new_map_id = '_map_' . $key;

					$meta_open    = noo_get_post_meta( $post->ID, $new_open_id, '' );
					$meta_closed  = noo_get_post_meta( $post->ID, $new_closed_id, '' );
					$meta_trainer = noo_get_post_meta( $post->ID, $new_trainer_id, '' );
					$meta_address = noo_get_post_meta( $post->ID, $new_address_id, '' );
					$meta_map = noo_get_post_meta( $post->ID, $new_map_id, '' );
					
					$meta_open    = (array) noo_json_decode($meta_open);
					$meta_closed  = (array) noo_json_decode($meta_closed);
					$meta_trainer = (array) noo_json_decode($meta_trainer);
					$meta_address = (array) noo_json_decode($meta_address);
					$meta_map = (array) noo_json_decode($meta_map);

					echo '<div id="tabs-'.$opt_value.'">';
					?>
					<div class="noo-control">
						<div class="row-append">
							<table class="row-schedule-item" cellpadding="0" cellspacing="0">
								<thead>
									<tr>
										<th></th>
										<th></th>
										<th><?php echo __('Open Time', 'noo'); ?></th>
										<th><?php echo __('Close Time', 'noo'); ?></th>
										<th><?php echo __('Trainer', 'noo'); ?></th>
										<th><?php echo __('Address', 'noo'); ?></th>
										<th><?php echo __('Link Map', 'noo'); ?></th>
									</tr>
								</thead>
								<tbody>
								<?php if ( is_array($meta_open) && count($meta_open) > 0 ) : foreach ($meta_open as $k => $mopen) :
									$time_open_text = is_numeric( $mopen ) ? date( $time_format, $mopen ) : $mopen;
									$time_open = is_numeric( $mopen ) ? $mopen : strtotime( $mopen );

									$time_closed_text = '';
									$time_closed = '';
									if ( isset($meta_closed[$k]) ){
										$time_closed_text = is_numeric( $meta_closed[$k] ) ? date( $time_format, $meta_closed[$k] ) : $meta_closed[$k];
										$time_closed = is_numeric( $meta_closed[$k] ) ? $meta_closed[$k] : strtotime( $meta_closed[$k] );
									}
								?>
								<tr>
									<td><div class="button-action minus">-</div></td>
									<td class="sort"><i class="dashicons-grid-view dashicons-before"></i></td>
									<td>
										<input class="add_time" placeholder="<?php echo __( 'Leave a blank will automatically take the first settings', 'noo' ); ?>" type="text" readonly="" class="input_text" id="<?php echo $new_open_id; ?>" value="<?php echo esc_attr( $time_open_text ); ?>">
										<input type="hidden" name="noo_meta_boxes[<?php echo $new_open_id; ?>][]" value="<?php echo esc_attr( $time_open ); ?>">
									</td>
									<td>
										<input class="add_time" placeholder="<?php echo __( 'Leave a blank will automatically take the first settings', 'noo' ); ?>" type="text" readonly="" class="input_text" id="<?php echo $new_closed_id; ?>" value="<?php echo esc_attr( $time_closed_text ); ?>">
										<input type="hidden" name="noo_meta_boxes[<?php echo $new_closed_id; ?>][]" value="<?php echo esc_attr( $time_closed ); ?>">
									</td>
									<td>
										<select class="alter_parent_trainer" name="noo_meta_boxes[<?php echo $new_trainer_id; ?>][]">
											<?php foreach ($trainer_options as $trainer) :
												echo '<option value='.$trainer['value'];
												if( isset($meta_trainer[$k]) && $meta_trainer[$k] == $trainer['value'] ):
													echo ' selected ';
												endif;
												echo '>'.$trainer['label'].'</option>';
											endforeach; ?>
										</select>
									</td>
									<td><input type="text" name="noo_meta_boxes[<?php echo $new_address_id; ?>][]" class="input_text" id="" value="<?php echo isset($meta_address[$k]) ? esc_attr($meta_address[$k]) : ''; ?>"></td>
									<td><input type="text" name="noo_meta_boxes[<?php echo $new_map_id; ?>][]" class="input_text" id="" value="<?php echo isset($meta_map[$k]) ? esc_attr($meta_map[$k]) : ''; ?>"></td>
								</tr>
								<?php endforeach;
								else:
									?>
								<tr>
									<td><div class="button-action minus">-</div></td>
									<td class="sort"><i class="dashicons-grid-view dashicons-before"></i></td>
									<td>
										<input class="add_time" placeholder="<?php echo __( 'Leave a blank will automatically take the first settings', 'noo' ); ?>" type="text" readonly="" class="input_text" id="<?php echo $new_open_id; ?>" value="">
										<input type="hidden" name="noo_meta_boxes[<?php echo $new_open_id; ?>][]" value="">
									</td>
									<td>
										<input class="add_time" placeholder="<?php echo __( 'Leave a blank will automatically take the first settings', 'noo' ); ?>" type="text" readonly="" class="input_text" id="<?php echo $new_closed_id; ?>" value="">
										<input type="hidden" name="noo_meta_boxes[<?php echo $new_closed_id; ?>][]" value="">
									</td>
									<td>
										<select class="alter_parent_trainer" name="noo_meta_boxes[<?php echo $new_trainer_id; ?>][]">
											<?php foreach ($trainer_options as $trainer) :
												echo '<option value='.$trainer['value'].'>'.$trainer['label'].'</option>';
											endforeach; ?>
										</select>
									</td>
									<td><input type="text" name="noo_meta_boxes[<?php echo $new_address_id; ?>][]" class="input_text" id="" value=""></td>
								</tr>
								</tbody>
								<?php
								endif; ?>
							</table>
						</div>
						<div class="button-action add">+</div>
						<script type="text/javascript">
							jQuery(document).ready(function($) {
								$('#<?php echo $new_open_id; ?>, #<?php echo $new_closed_id; ?>').datetimepicker({
									format:"H:i",
									step:15,
									timepicker: true,
									datepicker: false,
									scrollInput: false,
									onChangeDateTime:function(dp,$input){
										$input.next('input[type="hidden"]').val(parseInt(dp.getTime()/1000)-60*dp.getTimezoneOffset()); // correct the timezone of browser.
									}
								});
							});
						</script>
					</div>
					<?php
					echo '</div>';
				}
			?>
		</div> <!--# Tab -->
		<script>
			jQuery('document').ready(function ($) {
				$( "#tabs" ).tabs();
				$('#_use_advanced_multi_time').change(function(){
					show_hide_multi_time($(this));
				});
				$('#_number_day').change(function(){
					show_hide_multi_time($('#_use_advanced_multi_time'));
				});
				show_hide_multi_time($('#_use_advanced_multi_time'));

				$('.row-schedule-item tbody').sortable({
			        items:'tr',
			        cursor:'move',
			        axis:'y',
			        handle: 'td.sort',
			        scrollSensitivity:40,
			        forcePlaceholderSize: true,
			        helper: 'clone',
			        opacity: 0.65
			    });

				$('.button-action.add').click(function(){
					var _html = $(this).closest('.noo-control').find('table tbody tr:last-child').html();
					$(this).closest('.noo-control').find('table tbody').append('<tr>'+_html+'</tr>');
					$(this).closest('.noo-control').find('table tbody tr:last-child').find('input, select').val('');

					$('.add_time').datetimepicker({
						format:"H:i",
						step:15,
						timepicker: true,
						datepicker: false,
						scrollInput: false,
						onChangeDateTime:function(dp,$input){
							$input.next('input[type="hidden"]').val(parseInt(dp.getTime()/1000)-60*dp.getTimezoneOffset()); // correct the timezone of browser.
						}
					});

					$('.button-action.minus').click(function(){
						if ($(this).closest('.noo-control').find('table tbody tr').length > 1){
							$(this).closest('tr').hide(300, function(){
								$(this).remove();
							});
						}
					});

					$('.alter_parent_trainer').change(function(){
						reload_multi_trainer($(this));
					});

				});

				$('.alter_parent_trainer').change(function(){
					reload_multi_trainer($(this));
				});

				$('.button-action.minus').click(function(){
					if ($(this).closest('.noo-control').find('table tbody tr').length > 1){
						$(this).closest('tr').hide(300, function(){
							$(this).remove();
						});
					}
				});

				function reload_multi_trainer(){
					var string = [];
					$('.alter_parent_trainer').each(function(){
						if ( $(this).val() != '' ){
							string.push( $(this).val() );
						}
					});
					$('#_trainer').val(string);
				}

				function show_hide_multi_time(obj){
					if(obj.prop('checked') == false){
						$('._advanced_multi_time').hide();
					}else{
						$('._advanced_multi_time').show();
					}
					$arr = $('#_number_day').val();
					$('.ui-tabs-nav li').addClass('ui-state-disabled');
					if ($arr) {
						for(var i=0; i<$arr.length; i++){
							$('.ui-tabs-nav li[aria-controls="tabs-'+$arr[i]+'"]').removeClass('ui-state-disabled');
						}
					}
					$('.ui-tabs-nav li').each(function(index){
						if( ! $(this).hasClass('ui-state-disabled') ){
							$( "#tabs" ).tabs({ active: index });		
							return false;
						}
					})
				}
		  	});
		</script>
		<?php
	}

	public function meta_box_select_multiple($post, $id, $type, $meta, $std, $field){
		$meta = $meta ? $meta : $std;
		$meta = noo_json_decode( $meta );

		echo'<select id='.$id.' name="noo_meta_boxes[' . $id . '][]" multiple>';
		if( isset( $field['options'] ) && !empty( $field['options'] ) ) {
			foreach ( $field['options'] as $key=>$option ) {
				$opt_value  = $key;
				$opt_label  = $option;
				echo '<option';
				echo ' value="'.$opt_value.'"';
				if ( in_array($opt_value, (array) $meta)  ) echo ' selected="selected"';
				echo '>' . $opt_label . '</option>';
			}
		}
		echo '</select>';
	}
	
	public function meta_box_timepicker( $post, $id, $type, $meta, $std, $field ) {
		wp_enqueue_script( 'vendor-datetimepicker' );
		wp_enqueue_style( 'vendor-datetimepicker' );
		$date_format = 'H:i';
	
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
					timepicker: true,
					datepicker: false,
					scrollInput: false,
					onChangeDateTime:function(dp,$input){
						$input.next('input[type="hidden"]').val(parseInt(dp.getTime()/1000)-60*dp.getTimezoneOffset()); // correct the timezone of browser.
					}
				});
			});
		</script>
	<?php
	}
	
	public function meta_box_datepicker( $post, $id, $type, $meta, $std, $field ) {
		wp_enqueue_script( 'vendor-datetimepicker' );
		wp_enqueue_style( 'vendor-datetimepicker' );
		$date_format = 'm/d/Y';
	
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
					timepicker: false,
					datepicker: true,
					scrollInput: false,
					onChangeDateTime:function(dp,$input){
						$input.next('input[type="hidden"]').val(parseInt(dp.getTime()/1000)-60*dp.getTimezoneOffset()); // correct the timezone of browser.
					}
				});
			});
		</script>
	<?php
	}
	
	public function class_category_add_meta_field(){
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		?>
		<div class="form-field">
            <label for="term_meta-category_color"><?php _e( 'Color', 'noo' ); ?></label>
            <input type="text"  name="term_meta[category_color]" class="category_color" id="term_meta-category_color" value="" />
        </div>
        <script>
			jQuery(document).ready(function($) {
            	$('.category_color').wpColorPicker();
			});
		</script>
		<?php
	}
	
	public function class_category_edit_meta_field($term){
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		?>
		<tr class="form-field">
            <th scope="row" valign="top"><label for="term_meta-category_color"><?php _e( 'Color', 'noo' ); ?></label></th>
            <td>
                <?php
                $color_pk	= noo_get_term_meta( $term->term_id, 'category_color', '' );
                ?>
                <input type="text"  class="category_color" name="term_meta[category_color]" id="term_meta-category_color" value="<?php echo esc_html($color_pk); ?>" />
            	<script>
					jQuery(document).ready(function($) {
                        $('.category_color').wpColorPicker();
					});
				</script>
            </td>
        </tr>
		<?php
	}
	
	public function class_category_save_meta_field($term_id){
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "taxonomy_{$t_id}" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][$key] ) ) {
					$term_meta[$key] = $_POST['term_meta'][$key];
				}
			}
		
			// Save the option array.
			update_option( "taxonomy_{$t_id}", $term_meta );
		}
	}
	
	public static function get_trainer_list($trainer_ids){
		$trainer_ids = (array) noo_json_decode($trainer_ids);
		foreach ($trainer_ids as $k => $trainer_id) :
			?>
			<a href="<?php echo get_permalink($trainer_id)?>"><?php echo get_the_title($trainer_id) ?></a><?php	if ( ($k+1) < count($trainer_ids) ) echo ',';
		endforeach;
	}

	public static function loop_display($args=''){
		wp_enqueue_script('vendor-isotope');
		
		global $wp_query;
		$defaults = array(
			'echo'=>true,
			'title'=>'',
			'excerpt_length'=>30,
			'view_mode'=>''
		);
		$args = wp_parse_args($args,$defaults);
		extract($args);
		ob_start();
		include(locate_template("layouts/class-loop.php"));
		wp_reset_query();
		if($echo)
			echo ob_get_clean();
		else 
			return ob_get_clean();
	}

	public static function _get_next_date( $args='' ) {
		$defaults = array(
			'open_date'       => '',
			'open_time'       => '',
			'number_of_weeks' => 0,
			'number_days'     => array()
		);
		$args = wp_parse_args( $args, $defaults );
		extract($args);

		$next_date = null;
		if ( $number_of_weeks > 0 && !empty( $number_days ) && !empty($open_date) ) {
			// Reorder Number_days
			asort($number_days);
			$number_days = array_values($number_days);
			$today = getdate();
			$wday = $today['wday'];
			$week_next = $number_days[0];
			foreach ($number_days as $k => $numday) {				
				if ( $numday > $wday ) {
					$week_next = $numday;
					break;
				}
			}
			$this_time =  ( $open_date > time() ) ? $open_date : time();
			
			if ($open_time != '')
				$next_date = strtotime( "next ".self::_get_week_day( $week_next )."+ ".date('H', $open_time)." hours + ".date('i', $open_time)." minutes", $this_time );
			else
				$next_date = strtotime( "next ".self::_get_week_day( $week_next ), $this_time );
			// Get max date of this week, max is Sunday (0)
			$indi = ($number_days[0] == 0) ? 0 : end($number_days);
			$max_date = strtotime( "next ".self::_get_week_day($indi) , strtotime("yesterday", $open_date) );
			// Get end date depend on Max date to check this time()
			$end_date = strtotime( "+".($number_of_weeks-1)." week" , $max_date );
			if ( $next_date > $end_date )
				$next_date = null;
		}
		if ( time() >= $next_date )
			$next_date = null;

		return $next_date;
	}

	public static function get_coming_class_ids(){
		global $wpdb;
		$classes = (array) $wpdb->get_results(
				"SELECT $wpdb->posts.ID, $wpdb->postmeta.meta_key, $wpdb->postmeta.meta_value
				FROM $wpdb->posts
				LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
				WHERE $wpdb->posts.post_status = 'publish' 
				AND $wpdb->posts.post_type = 'noo_class'
				AND ($wpdb->postmeta.meta_key = '_open_date' OR $wpdb->postmeta.meta_key = '_number_of_weeks' OR $wpdb->postmeta.meta_key = '_number_day') " );

		foreach ($classes as $k => $v) {
			$newarr[$v->ID][$v->meta_key] = $v->meta_value;
		}
		
		$rearr = array();
		foreach ($newarr as $k => $v) {
			if (!isset($v['_open_date']) || $v['_open_date'] == '')
				continue;
			$number_of_weeks = $v['_number_of_weeks'] != '' ? $v['_number_of_weeks'] : 1;
			$end_date = strtotime("+" . $number_of_weeks . " week", $v['_open_date']);
			if ( time() <= $end_date  ) {
				$rearr[] = $k;
			}
		}
		
		return $rearr;
	}

	public static function get_open_date_display( $args='' ) {
		$defaults = array(
			'open_date'       => '',
			'number_of_weeks' => 0,
			'number_days'     => array()
		);
		$args = wp_parse_args( $args, $defaults );
		extract($args);

		$open_date_display = noo_get_option( 'noo_class_open_date', 'all' );

		$next_date = null;
		if ( ( $open_date_display == 'next' || $open_date_display == 'all' ) ) {
			$next_date = self::_get_next_date(array(
				'open_date'       => $open_date,
				'number_of_weeks' => $number_of_weeks,
				'number_days'     => $number_days
			));
		}

		// return array by get options display open date
		if ( $open_date_display == 'open' ) {
			$arr['open_date'] = $open_date;	
		} elseif ( $open_date_display == 'next' ) {
			$arr['next_date'] = $next_date;
		} else {
			$arr = array(
				'open_date' => $open_date,
				'next_date' => $next_date
			);	
		}
		if ( time() < $open_date && ( $open_date_display == 'next' || $open_date_display == 'all' ) ) {
			$arr = array(
				'open_date' => $open_date
			);
		}
		return $arr;
	}

	public function schedule_shortcode($atts, $content = null){
		wp_enqueue_script('noo-event-calendar');
		wp_enqueue_script('noo-event-calendar-lang');
		extract( shortcode_atts( array(
			'min_time'            => '01:00:00',
			'max_time'            => '21:00:00',
			'content_height'      => '',
			'default_view'        => 'agendaWeek',
			'hide_time_range'     => '',
			'class_categories'    => 'all',
			'show_weekends'       => 'yes',
			'visibility'          => '',
			'class'               => '',
			'custom_style'        => '',
		), $atts ) );
		$class            = ( $class              != '' ) ? 'class-schedule '.esc_attr( $class ) : 'class-schedule' ;
		$class 			  = $class . ' view-' . $default_view;
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
		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style   = ( $custom_style  != '' ) ? 'style="' . $custom_style . '"' : '';
		$id = uniqid('noo_class_schedule_');
		if ( !empty( $class_categories ) && $class_categories != 'all' ) {
			$classes_arr = $this->_get_schedule_class_list($category = $class_categories);
		} else {
			$classes_arr = $this->_get_schedule_class_list();
		}

		$content_height = is_numeric( $content_height ) ? $content_height : "'auto'";

		$header['next'] = 'next';
		$header['prev'] = 'prev';
		// RTL Options
		if ( is_rtl() ){
			$header['next'] = 'prev';
			$header['prev'] = 'next';
		}	
		
		// Weekend option
		$weekends = ( $show_weekends == 'no' ) ? 'false' : 'true';		
		
		ob_start();
		?>
		<div class="noo-class-schedule">
			<div class="class-schedule-filter">
				<?php 
					$categories = get_terms('class_category');
					if($categories):
				?>
				<ul>
					<li>
						<a href="#" class="active" data-filter="<?php if( !empty( $class_categories ) && $class_categories != 'all' ) echo esc_attr($class_categories); ?>">
							<?php _e('All','noo')?>
						</a>
					</li>
					<?php 
						if( !empty( $class_categories ) && $class_categories != 'all' ){
							$class_categories = explode(',', $class_categories);
							foreach ($categories as $category) {
								if( in_array($category->term_id, $class_categories) == true ){ ?>
									<li><a href="#" data-filter="<?php echo esc_attr($category->term_id)?>"><?php echo esc_html($category->name)?></a></li>
								<?php
								}
							}
						} else {
							foreach ((array)$categories as $category){
					?>
								<li><a href="#" data-filter="<?php echo esc_attr($category->term_id)?>"><?php echo esc_html($category->name)?></a></li>
					<?php 	}
						}
					?>
				</ul>
				<?php endif;?>
			</div>
			<div id="<?php echo esc_attr($id)?>"  <?php echo ($class)?> <?php echo ($custom_style)?>></div>
			<script>
				var source = <?php echo json_encode($classes_arr)?>;
			</script>
			<script>
				jQuery(document).ready(function($) {
					$("#<?php echo esc_attr($id)?>").fullCalendar({
						header: {
							left: '<?php echo $header['prev']; ?>',
							center: 'title',
							right: '<?php echo $header['next']; ?>'
						},
						axisFormat: 'HH:mm',
						minTime: '<?php echo apply_filters('noo-class-schedule-mintime', $min_time)?>',
						maxTime: '<?php echo apply_filters('noo-class-schedule-maxtime', $max_time)?>',
						timeFormat: '<?php echo self::convertPHPToMomentFormat( get_option('time_format') ); ?>',
						axisFormat: '<?php echo self::convertPHPToMomentFormat( get_option('time_format') ); ?>',
						defaultView: '<?php echo esc_attr( $default_view ); ?>',
						firstDay: <?php echo get_option('start_of_week'); ?>,
						slotDuration: '01:00:00',
						columnFormat: 'dddd',
						allDaySlot: false,
						defaultDate: '<?php echo current_time('Y-m-d')?>',
						editable: false,
						lang:'<?php echo get_locale()?>',
						eventLimit: true, // allow "more" link when too many events
						events: source,
						weekends: <?php echo $weekends; ?>,
						<?php if ( $default_view == 'agendaWeek'  ) : ?>
						hideTimeRange: '<?php echo $hide_time_range; ?>',
						contentHeight: <?php echo $content_height; ?>
						<?php endif; ?>
					});

					$(".class-schedule-filter a").on("click", function(e){
						e.preventDefault();
						// var filterData = $(this).data("filter");
						// if (filterData == "all") {
						// 	$(".class-schedule-filter a.active").removeClass("active");
						// 	$(this).addClass("active");
						// 	$(".fc-content-skeleton a.fc-event.hide").removeClass("hide");
						// } else {
						// 	$(".class-schedule-filter a.active").removeClass("active");
						// 	$(this).addClass("active");
						// 	$(".fc-content-skeleton a.fc-event.hide").removeClass("hide");
						// 	$(".fc-content-skeleton a.fc-event").not("."+filterData).addClass("hide");
						// }
						var $this = $(this);

						$.ajax({
							type: 'POST',
							url: nooL10n.ajax_url,
							data: {
								action			: 'noo_class_filter',
								class_category	: $(this).data("filter"),
								sercurity		: '<?php echo wp_create_nonce( 'class_filter' ); ?>'
							},
							beforeSend: function() {
								$('.noo-class-schedule .fc-view').addClass('class-schedule-overlay-loading');
								$(".class-schedule-filter a.active")
									.removeClass("active")
									.removeClass('class-schedule-infi-pulse');
								$this
									.addClass("active")
									.addClass('class-schedule-infi-pulse');
							},
							success: function(res){
								var newsource = res;

								$('.noo-class-schedule .fc-view').removeClass('class-schedule-overlay-loading');
								$(".class-schedule-filter a.active")
									.removeClass("active")
									.removeClass('class-schedule-infi-pulse');
								$this
									.addClass("active")
									.removeClass('class-schedule-infi-pulse');

								if(newsource){
									$("#<?php echo esc_attr($id)?>").fullCalendar('removeEventSource', source)
									$("#<?php echo esc_attr($id)?>").fullCalendar('refetchEvents')
									$("#<?php echo esc_attr($id)?>").fullCalendar('addEventSource', newsource)
									$("#<?php echo esc_attr($id)?>").fullCalendar('refetchEvents');
									source = newsource;
								}
							},
							error: function () {
			                	location.reload();
			                }
						});
					});

					// $('body').on('click', 'button.fc-button', function() {
					// 	$(".class-schedule-filter a.active").removeClass("active");
					// 	$(".class-schedule-filter a[data-filter='all']").addClass("active");
					// });
				});
			</script>
		</div>

		<div class="noo-responsive-schedule-wrap">
            <?php
                if ( get_option('start_of_week') == date( "w") ) {
                    $first_week_day = date('Y-m-d');
                } else {
                    $start_of_week = Noo_Class::_get_week_day( get_option('start_of_week') );
                    $first_week_day = date( 'Y-m-d', strtotime('last ' . $start_of_week) );
                }
                $end_week_day = date( 'Y-m-d', strtotime($first_week_day . ' +7 days') );

                //Create label
                $label_start = date_i18n( get_option( 'date_format' ), strtotime($first_week_day) );
                $label_end = date_i18n( get_option( 'date_format' ), strtotime($first_week_day . ' +6 days') );

                // Create nav
                $prev_from = date('Y-m-d',( strtotime ( '-1 week' , strtotime ( $first_week_day ) ) ) );
                $prev_to = date('Y-m-d',( strtotime ( '-1 week' , strtotime ( $end_week_day ) ) ) );

                $next_from = date('Y-m-d',( strtotime ( '+1 week' , strtotime ( $first_week_day ) ) ) );
                $next_to = date('Y-m-d',( strtotime ( '+1 week' , strtotime ( $end_week_day ) ) ) );
            ?>
            
            <div class="res-sche-navigation">
                <a href="#" class="prev" data-from="<?php echo esc_attr( $prev_from ); ?>" data-to="<?php echo esc_attr( $prev_to ); ?>"><span class="fc-icon fc-icon-left-single-arrow"></span></a>
                <h3><?php echo esc_attr( $label_start ); ?> - <?php echo esc_attr( $label_end ); ?></h3>
                <a href="#" class="next" data-from="<?php echo esc_attr( $next_from ); ?>" data-to="<?php echo esc_attr( $next_to ); ?>"><span class="fc-icon fc-icon-right-single-arrow"></span></a>
            </div>

            <div class="res-sche-content">
                <?php
                    $doituong = new Noo_Class();
                    $doituong->_schedule_class_list_mobile($first_week_day, $end_week_day, true);
                ?>
            </div>

            <div class="res-sche-navigation">
                <a href="#" class="prev" data-from="<?php echo esc_attr( $prev_from ); ?>" data-to="<?php echo esc_attr( $prev_to ); ?>"><span class="fc-icon fc-icon-left-single-arrow"></span></a>
                <h3><?php echo esc_attr( $label_start ); ?> - <?php echo esc_attr( $label_end ); ?></h3>
                <a href="#" class="next" data-from="<?php echo esc_attr( $next_from ); ?>" data-to="<?php echo esc_attr( $next_to ); ?>"><span class="fc-icon fc-icon-right-single-arrow"></span></a>
            </div>

        </div> <!-- noo-responsive-schedule-wrap -->

        <script>
        jQuery(document).ready(function($) {
            
            $(".res-sche-navigation a").on("click", function(e){
                e.preventDefault();
                var $this = $(this);

                $.ajax({
                    type: 'POST',
                    url: nooL10n.ajax_url,
                    data: {
                        action          : 'noo_class_responsive_navigation',
                        from            : $this.attr("data-from"),
                        to              : $this.attr("data-to"),
                        weekends        : true,
                        sercurity       : '<?php echo wp_create_nonce( 'class_responsive_navigation' ); ?>'
                    },
                    beforeSend: function() {
                        var sche_wrap = $this.closest('.noo-responsive-schedule-wrap');
                        sche_wrap.find('.res-sche-content').addClass('overlay-loading-tripped');
                    },
                    success: function(res){

                        var sche_wrap = $this.closest('.noo-responsive-schedule-wrap');
                        sche_wrap.find('.res-sche-content').removeClass('overlay-loading-tripped');
                        sche_wrap.find('.res-sche-content').html(res);

                        label_start = sche_wrap.find('.label-start').val();
                        label_end = sche_wrap.find('.label-end').val();

                        sche_wrap.find('.res-sche-navigation h3').html(label_start + ' - ' + label_end);

                        var _nav_prev = sche_wrap.find('.res-sche-navigation .prev');
                        var _nav_next = sche_wrap.find('.res-sche-navigation .next');

                        _nav_prev.attr( 'data-from', sche_wrap.find('.prev-from-hidden').val() );
                        _nav_prev.attr( 'data-to', sche_wrap.find('.prev-to-hidden').val() );

                        _nav_next.attr( 'data-from', sche_wrap.find('.next-from-hidden').val() );
                        _nav_next.attr( 'data-to', sche_wrap.find('.next-to-hidden').val() );

                    },
                    error: function () {
                        location.reload();
                    }
                });
            });

        });

        </script>

		<?php
		return ob_get_clean();
	}

	public function class_filter() {
		if( check_ajax_referer('class_filter','security' , false) ) {
			wp_send_json('');
		}
		$category = isset( $_POST['class_category'] ) ? $_POST['class_category'] : '';
		wp_send_json($this->_get_schedule_class_list($category));
	}

	public static function _get_week_day( $day, $get_text = false ) {

		if ( $get_text ) {

			global $wp_locale;
            return $wp_locale->get_weekday( $day );

		} else {
			// Not change
			switch( $day ) {
				case 0: return 'sunday';
				case 1: return 'monday';
				case 2: return 'tuesday';
				case 3: return 'wednesday';
				case 4: return 'thursday';
				case 5: return 'friday';
				case 6: return 'saturday';
			}
			
		}
		return '';
	}

	private function _get_schedule_class_list( $category = '' ) {
		$args = array(
			'posts_per_page'=>'-1',
			'post_type'=>'noo_class'
		);
		if( !empty( $category ) ) {
			$category = explode(',', $category);
			$arr = array();
			if( count($category) > 1 ) {
				$arr= array('relation' => 'OR');
				foreach ($category as $terms) {
					$arr[] = array(
						'taxonomy'  => 'class_category',
						'terms'   	=> $terms
					);
				}
			} else {
				$arr[] = array(
					'taxonomy'  => 'class_category',
					'terms'   	=> $category
				);
			}
			$args['tax_query'] = $arr;
		}
		$classes = get_posts($args);
		$classes_arr = array();
		if($classes){
			foreach ($classes as $cl){
				$open_date		    = noo_get_post_meta( $cl->ID, "_open_date", '' );
				$number_days		= noo_json_decode( noo_get_post_meta( $cl->ID, "_number_day", '' ) );
				$number_weeks		= (int) noo_get_post_meta( $cl->ID, "_number_of_weeks", '1' );
				
				if (empty($open_date)) continue;
				
				$post_category = get_the_terms( $cl->ID, 'class_category' );
				$color = '#fbe1c7';
				$category_id = '';
				if(!empty($post_category)){
					$post_category = reset($post_category);
					$category_parent = $post_category->parent;
				 	if(!empty($category_parent)):
				  		$color = noo_get_term_meta( $post_category->parent, 'category_color', '' );
				 		$category_id = $post_category->parent;
				  	else:
				  		$color = noo_get_term_meta( $post_category->term_id, 'category_color', '#fbe1c7' );
				  		$category_id = $post_category->term_id;
				  	endif;
				}
				$cl_obj = new stdClass();
				$cl_obj->id = $cl->ID;
				$cl_obj->title = $cl->post_title;
				$cl_obj->color = $color;
				$cl_obj->url = get_the_permalink($cl->ID);
				$cl_obj->address = noo_get_post_meta( $cl->ID, "_address", '' );
				if(!empty($category_id))
					$cl_obj->className = 'fc-class-'.$category_id;

				$trainer = (array) noo_json_decode( noo_get_post_meta($cl->ID, '_trainer') );
				if($trainer){
					$cl_obj->trainer = get_the_title($trainer[0]);
				}else{
					$cl_obj->trainer = '';
				}
				$time_format = get_option('time_format');
				if( $number_weeks > 0 && !empty( $number_days ) ) {
					$use_advanced_multi_time = noo_get_post_meta( $cl->ID, "_use_advanced_multi_time", false );
					foreach( $number_days as $day ) {
						
						$week_day = self::_get_week_day( $day );

						$open_time		    = noo_get_post_meta(  $cl->ID, "_open_time", '1470301200' );
						$close_time		    = noo_get_post_meta(  $cl->ID, "_close_time", '1470308400' );

						if($use_advanced_multi_time){
							$meta_open    = noo_get_post_meta(  $cl->ID, "_open_time_".$day, $open_time );
							$meta_closed  = noo_get_post_meta(  $cl->ID, "_closed_time_".$day, $close_time );
							$meta_trainer = noo_get_post_meta(  $cl->ID, "_trainer_".$day, '' );
							$meta_address = noo_get_post_meta(  $cl->ID, "_address_".$day, '' );

							$meta_open    = (array) noo_json_decode($meta_open);
							$meta_closed  = (array) noo_json_decode($meta_closed);
							$meta_trainer = (array) noo_json_decode($meta_trainer);
							$meta_address = (array) noo_json_decode($meta_address);

							if( $meta_open && $meta_open[0] == '' )
								$meta_open[] = $open_time;
							if( $meta_closed && $meta_closed[0] == '' )
								$meta_closed[] = $close_time;

							foreach ($meta_open as $k => $mopen) {
								if ( $mopen && $meta_closed[$k] ) {
									$start_day = strtotime("yesterday", $open_date);
									
									// Time class less than 2 hour
									$class_short = '';
									$t3 = strtotime(date('H:i', $meta_closed[$k])) - strtotime(date('H:i', $mopen));
									if ( $t3 <= 1800 )
										$class_short = 'time-short-1';
									if ( $t3 <= 3600 && $t3 > 1800 )
										$class_short = 'time-short-2';
									if ( isset($meta_address[$k]) ){
										if ( $t3 >= 3600 && $t3 < 7200 && strlen($meta_address[$k]) > 30 )
											$class_short = 'time-short-2';	
									}

									for( $week = 1; $week <= $number_weeks; $week++ ) {
										$new_date = strtotime( "next " . $week_day, $start_day );
										$clone_cl_obj = new stdClass();
										$clone_cl_obj = clone $cl_obj;
										$clone_cl_obj->start = date_i18n('Y-m-d',$new_date).'T'.date_i18n('H:i',$mopen);
										$clone_cl_obj->end = date_i18n('Y-m-d',$new_date).'T'.date_i18n('H:i',$meta_closed[$k]);
										$clone_cl_obj->className = $clone_cl_obj->className . ' ' .$class_short;

										if( isset($meta_trainer[$k]) ){
											$trainer_name = ( $meta_trainer[$k] != '' && is_numeric($meta_trainer[$k]) ) ? get_the_title($meta_trainer[$k]) : get_the_title($trainer[0]);
											$clone_cl_obj->trainer = $trainer_name;
										}
										if( isset($meta_address[$k]) )
											$clone_cl_obj->address = $meta_address[$k];
										

										$classes_arr[] = $clone_cl_obj;

										$start_day = strtotime("+1 week", $start_day);
									}
								}
							}

						} else {
							$start_day = strtotime("yesterday", $open_date);
							if( empty( $week_day ) ) continue;
							
							// Time class less than 2 hour
							$class_short = '';
							$t3 = strtotime(date('H:i', $close_time)) - strtotime(date('H:i', $open_time));
							if ( $t3 <= 1800 )
								$class_short = 'time-short-1';
							if ( $t3 <= 3600 && $t3 > 1800 )
								$class_short = 'time-short-2';
							if ( isset($cl_obj->address) ){
								if ( $t3 >= 3600 && $t3 < 7200 && strlen($cl_obj->address) > 30 )
									$class_short = 'time-short-2';	
							}

							for( $week = 1; $week <= $number_weeks; $week++ ) {
								$new_date = strtotime( "next " . $week_day, $start_day );
								$clone_cl_obj = new stdClass();
								$clone_cl_obj = clone $cl_obj;
								$clone_cl_obj->start = date_i18n('Y-m-d',$new_date).'T'.date_i18n('H:i',$open_time);
								$clone_cl_obj->end = date_i18n('Y-m-d',$new_date).'T'.date_i18n('H:i',$close_time);
								$clone_cl_obj->className = $clone_cl_obj->className . ' ' .$class_short;

								$classes_arr[] = $clone_cl_obj;

								$start_day = strtotime("+1 week", $start_day);
							}
						}
					}
				}
			}
		}

		return $classes_arr;
	}

	function getCheckBox($string, $text_explode) {
		$day = array();
		$posDay = $this->strpos_all( $string, $text_explode );
		foreach ($posDay as $pos) {
			$day[] = substr($string, $pos + strlen($text_explode), 1);
		}

		return $day;
	}

	function getSelect($string, $text_explode) {
		$pos = strpos( $string, $text_explode );
		$kq = '';
		
		if ($pos !== false) {
			$i = 0;
			while( $i < strlen($string) ) {
				$i++;
				$kq = substr($string, $pos + strlen($text_explode), $i);
				if ( !is_numeric($kq) ){
					$kq = substr($kq, 0, strlen($kq) - 1);
					break;
				}
			}
		}
		return $kq;
	}

	function strpos_all($haystack, $needle) {
	    $offset = 0;
	    $allpos = array();
	    while (($pos = strpos($haystack, $needle, $offset)) !== FALSE) {
	        $offset   = $pos + 1;
	        $allpos[] = $pos;
	    }
	    return $allpos;
	}

	public function class_get_count_arrange() {
		$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '*';

		$filter_days    = $this->getCheckBox($filter, 'filter-day-');
		$filter_trainer = $this->getSelect($filter, 'filter-trainer-');
		$filter_level   = $this->getSelect($filter, 'filter-level-');
		$filter_cat     = $this->getSelect($filter, 'filter-cat-');

		$hide_past = noo_get_option('noo_classes_hide_past', false);

		$args = array(
			'posts_per_page' =>'-1',
			'post_type'      =>'noo_class'
		);

		if ( $filter_level ) {

			$args['tax_query'][] = array(
				'taxonomy'  => 'class_level',
				'terms'   	=> $filter_level,
			);
		}

		if ( $filter_cat ) {		
			$args['tax_query'][] = array(
				'taxonomy'  => 'class_category',
				'terms'   	=> $filter_cat,
			);
		}

		if ( $hide_past ) {

			$args['meta_query'] = array(
		        array(
					'key'     => '_next_date',
	                'value'   => time(),
	                'compare' => '>='
		        )
		    );
		}


		$classes = get_posts($args);

		$new_c = array();
		if ( $classes ) {

            foreach ($classes as $cl){
            	$flag = 1;
            	if ( $filter_days ) {
            		$flag = 0;
					$number_days = (array) noo_json_decode( noo_get_post_meta( $cl->ID, "_number_day", '' ) );

					foreach ($number_days as $day) {
						if ( in_array( $day, $filter_days ) ) {
							$flag = 1;
						}
					}
				}

				if ( $filter_trainer ) {
            		$flag = 0;
					$trainers = (array) noo_json_decode( noo_get_post_meta($cl->ID, '_trainer') );

					if ( in_array( $filter_trainer, $trainers ) ) {
						$flag = 1;
					}
				}

				if ( $flag == 1 ) {
					$new_c[] = $cl;
				}
            }
        }

        echo count($new_c);

		exit();
	}

	public function class_responsive_navigation() {

        if( check_ajax_referer('class_responsive_navigation','security' , false) ) {
            return '';
        }

        $from     = isset( $_POST['from'] ) ? $_POST['from'] : '';
        $to       = isset( $_POST['to'] ) ? $_POST['to'] : '';
        $weekends = isset( $_POST['weekends'] ) ? $_POST['weekends'] : true;
        
        $this->_schedule_class_list_mobile($from, $to, $weekends);

        exit();
    }

    public function _schedule_class_list_mobile( $from = '', $to = '', $weekends = true ) {
        
        global $wp_locale;
        $classes_arr = $this->_get_schedule_class_list();

        $new_arr = array();

        foreach ($classes_arr as $key => $class) :

            $kq = null;
            $kq = str_replace('T', ' ', $class->start);
            $kq = strtotime($kq);

            if ( $kq !== null ) {

                // Remove class out of range
                if ($from != '' && $to != '') {

                    if ( $kq < strtotime($from) || $kq > strtotime($to) ) {
                        continue;
                    }
                }

                // Remove weekends
                if ( !$weekends && ( date( "w", $kq) == 0 || date( "w", $kq) == 6 ) ) {
                    continue;
                }

                $new_arr[$kq][] = array(
                    'id'            => $class->id,
                    'title'         => $class->title,
                    'color' 		=> $class->color,
                    'url'           => $class->url,
                    'address'       => $class->address,
                    'className'     => $class->className,
                    'trainer'       => $class->trainer,
                    'start'         => $class->start,
                    'end'           => $class->end,
                    'start_time'    => date_i18n( get_option( 'time_format' ), strtotime( str_replace('T', ' ', $class->start) ) ),
                    'end_time'      => date_i18n( get_option( 'time_format' ), strtotime( str_replace('T', ' ', $class->end) ) ),
                    'weekday'       => $wp_locale->get_weekday( date( "w", $kq) ),
                );

            }

        endforeach;

        ksort($new_arr);

        if ( count($new_arr) > 0 ) {

            $last_weekday = '';
            foreach ($new_arr as $key => $value) {

                foreach ($value as $k => $cl) {
                        if ( $last_weekday != $cl['weekday'] ) :
                            $today = '';
                            if ( strtotime(date('Y-m-d', $key)) === strtotime(date('Y-m-d')) ) {
                                $today = 'today';
                            }
                            ?>
                        <div class="item-weekday <?php echo esc_attr($today); ?>"><?php echo $cl['weekday']; ?> (<?php echo date_i18n( get_option( 'date_format' ), $key ); ?>)</div>
                    <?php endif; ?>
                    <div class="item-day" style="background-color: <?php echo esc_attr( $cl['color'] ); ?>">
                        <span><?php echo esc_attr( $cl['start_time'] ); ?> - </span>
                        <span><?php echo esc_attr( $cl['end_time'] ); ?></span>
                        <a href="<?php echo esc_url( $cl['url'] ); ?>"><?php echo esc_attr( $cl['title'] ); ?></a> <i>- <?php echo esc_html__('with', 'noo'); ?> <?php echo esc_attr( $cl['trainer'] ); ?></i>
                    </div>
                    <?php
                    $last_weekday = $cl['weekday'];
                }
            }

        } else {
            echo '<center><p>'.esc_html('Class not found.', 'noo').'</p></center>';
        }

        //Create label
        $label_start = date_i18n( get_option( 'date_format' ), strtotime($from) );
        $label_end = date_i18n( get_option( 'date_format' ), strtotime($from . ' +6 days') );

        // Create nav
        $prev_from = date('Y-m-d',( strtotime ( '-1 week' , strtotime ( $from ) ) ) );
        $prev_to = date('Y-m-d',( strtotime ( '-1 week' , strtotime ( $to ) ) ) );

        $next_from = date('Y-m-d',( strtotime ( '+1 week' , strtotime ( $from ) ) ) );
        $next_to = date('Y-m-d',( strtotime ( '+1 week' , strtotime ( $to ) ) ) );


        ?>
        <input type="hidden" class="prev-from-hidden" value="<?php echo $prev_from; ?>" />
        <input type="hidden" class="prev-to-hidden" value="<?php echo $prev_to; ?>" />

        <input type="hidden" class="next-from-hidden" value="<?php echo $next_from; ?>" />
        <input type="hidden" class="next-to-hidden" value="<?php echo $next_to; ?>" />

        <input type="hidden" class="label-start" value="<?php echo $label_start; ?>" />
        <input type="hidden" class="label-end" value="<?php echo $label_end; ?>" />
        <?php
    }

	public static function convertPHPToMomentFormat($format) {
	    $replacements = array(
	        'd' => 'DD',
	        'D' => 'ddd',
	        'j' => 'D',
	        'l' => 'dddd',
	        'N' => 'E',
	        'S' => 'o',
	        'w' => 'e',
	        'z' => 'DDD',
	        'W' => 'W',
	        'F' => 'MMMM',
	        'm' => 'MM',
	        'M' => 'MMM',
	        'n' => 'M',
	        't' => '', // no equivalent
	        'L' => '', // no equivalent
	        'o' => 'YYYY',
	        'Y' => 'YYYY',
	        'y' => 'YY',
	        'a' => 'a',
	        'A' => 'A',
	        'B' => '', // no equivalent
	        'g' => 'h',
	        'G' => 'H',
	        'h' => 'hh',
	        'H' => 'HH',
	        'i' => 'mm',
	        's' => 'ss',
	        'u' => 'SSS',
	        'e' => 'zz', // deprecated since version 1.6.0 of moment.js
	        'I' => '', // no equivalent
	        'O' => '', // no equivalent
	        'P' => '', // no equivalent
	        'T' => '', // no equivalent
	        'Z' => '', // no equivalent
	        'c' => '', // no equivalent
	        'r' => '', // no equivalent
	        'U' => 'X',
	    );
	    $momentFormat = strtr($format, $replacements);
	    return $momentFormat;
	}
	
	public function  permalink_setting(){
		add_settings_field(
		'archive_class_slug',      		// id
		__( 'Archive Class Base', 'noo' ), 	// setting title
		array(&$this,'archive_class_slug_input'),  		// display callback
		'permalink',                 				// settings page
		'optional'                  				// settings section
		);
	}
	
	public function archive_class_slug_input(){
		$permalinks = get_option( 'noo_class_permalinks' );
		?>
		<input name="archive_class_slug_input" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['archive_class_slug'] ) ) echo esc_attr( $permalinks['archive_class_slug'] ); ?>" placeholder="<?php echo _x('classes', 'slug', 'noo') ?>" />
		<?php
	}
	
	public function permalink_setting_save(){
		if (is_admin()){
			if (isset($_POST['archive_class_slug_input'])){
					
				$archive_class_slug = sanitize_text_field( $_POST['archive_class_slug_input'] );
					
				$permalinks = get_option( 'noo_class_permalinks' );
				if ( ! $permalinks )
					$permalinks = array();
					
				$permalinks['archive_class_slug'] 	= untrailingslashit( $archive_class_slug );
					
				update_option( 'noo_class_permalinks', $permalinks );
			}
		}
		return;
	}
	
	
	public function register_post_type() {
		if ( post_type_exists( 'noo_class' ) )
			return;

		if ( get_transient( 'noo_class_slug_before' ) != get_transient( 'noo_class_slug_after' ) ) {
			flush_rewrite_rules( false );
			delete_transient( 'noo_class_slug_before' );
			delete_transient( 'noo_class_slug_after' );
		}
		
		$class_page = noo_get_option('noo_class_page', '');
		$class_slug = !empty($class_page) ? get_post( $class_page )->post_name : 'classes';

		register_post_type( 
			'noo_class', 
			array( 
				'labels' => array( 
					'name' => __( 'Classes', 'noo' ), 
					'singular_name' => __( 'Class', 'noo' ), 
					'add_new' => __( 'Add New Class', 'noo' ), 
					'add_new_item' => __( 'Add Class', 'noo' ), 
					'edit' => __( 'Edit', 'noo' ), 
					'edit_item' => __( 'Edit Class', 'noo' ), 
					'new_item' => __( 'New Class', 'noo' ), 
					'view' => __( 'View', 'noo' ), 
					'view_item' => __( 'View Class', 'noo' ), 
					'search_items' => __( 'Search Class', 'noo' ), 
					'not_found' => __( 'No Classes found', 'noo' ), 
					'not_found_in_trash' => __( 'No Classes found in Trash', 'noo' ), 
					'parent' => __( 'Parent Class', 'noo' ) ), 
				'public' => true, 
				'has_archive' => true, 
				'menu_icon' => 'dashicons-groups', 
				'rewrite' => array( 'slug' => $class_slug, 'with_front' => false ), 
				'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments','custom-fields' ), 
				'can_export' => true ) );
		
		register_taxonomy( 
			'class_category', 
			'noo_class', 
			array( 
				'labels' => array( 
					'name' => __( 'Class Category', 'noo' ), 
					'add_new_item' => __( 'Add New Class Category', 'noo' ), 
					'new_item_name' => __( 'New Class Category', 'noo' ) ), 
				'hierarchical' => true, 
				'query_var' => true, 
				'rewrite' => array( 'slug' => 'class-category' ) ) );
		register_taxonomy(
			'class_level',
			'noo_class',
			array(
				'labels' => array(
					'name' => __( 'Class Level', 'noo' ),
					'add_new_item' => __( 'Add New Class Level', 'noo' ),
					'new_item_name' => __( 'New Class Level', 'noo' ) ),
				'hierarchical' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'class-level' ) ) );
	}

	public function customizer_set_transients_before_save() {
		set_transient( 'noo_class_slug_before', noo_get_option( 'noo_class_page', 'classes' ), 60 );
	}

	public function customizer_set_transients_after_save() {
		set_transient( 'noo_class_slug_after', get_option( 'noo_class_page', 'classes' ), 60 );
	}
}

new Noo_Class();
endif;