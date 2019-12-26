<?php
if ( ! class_exists( 'Noo_Class' ) ) :
class Noo_Class {

	public function __construct() {
        add_action( 'wp_ajax_noo_class_get_count_arrange', array(&$this, 'class_get_count_arrange') );
        add_action( 'wp_ajax_nopriv_noo_class_get_count_arrange', array(&$this, 'class_get_count_arrange') );

        if ( !is_admin() ) :
            add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 100);
        endif;
	}

	public function pre_get_posts($q){
        if ( ! $q->is_main_query() ) {
            return;
        }
        if ( is_post_type_archive('noo_class') && $q->get('post_type') == 'noo_class' ) {
        	$hide_past = noo_get_option('noo_classes_hide_past', false);
        	if ( $hide_past ) {
				$meta_query = array(
			        array(
						'key'     => '_next_date',
		                'value'   => time(),
		                'compare' => '>='
			        )
			    );
			    $q->set('meta_query', $meta_query);
			}
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
			'posts_per_page'   =>'-1',
			'post_type'        =>'noo_class',
			'suppress_filters' => 0
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
}

new Noo_Class();
endif;