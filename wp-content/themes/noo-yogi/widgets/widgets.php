<?php
if ( !class_exists('Noo_Wiget') ) {

class Noo_Wiget extends WP_Widget {

	public $widget_cssclass;

	public $widget_description;

	public $widget_id;

	public $widget_name;

	public $settings;

	public $cached = true;

	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );
		
		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );
		if ( $this->cached ) {
			add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
			add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
			add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
		}
	}

	/**
	 * get_cached_widget function.
	 */
	function get_cached_widget( $args ) {
		$cache = wp_cache_get( apply_filters( 'dh_cached_widget_id', $this->widget_id ), 'widget' );
		
		if ( ! is_array( $cache ) ) {
			$cache = array();
		}
		
		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return true;
		}
		
		return false;
	}

	/**
	 * Cache the widget
	 * @param string $content
	 */
	public function cache_widget( $args, $content ) {
		$cache[$args['widget_id']] = $content;
		
		wp_cache_set( apply_filters( 'dh_cached_widget_id', $this->widget_id ), $cache, 'widget' );
	}

	/**
	 * Flush the cache
	 *
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( apply_filters( 'dh_cached_widget_id', $this->widget_id ), 'widget' );
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		if ( ! $this->settings ) {
			return $instance;
		}
		
		foreach ( $this->settings as $key => $setting ) {
			
			if ( isset( $setting['multiple'] ) ) :
				$instance[$key] = implode( ',', $new_instance[$key] );
			 else :
				if ( isset( $new_instance[$key] ) ) {
					$instance[$key] = sanitize_text_field( $new_instance[$key] );
				} elseif ( 'checkbox' === $setting['type'] ) {
					$instance[$key] = 0;
				}
			endif;
		}
		if ( $this->cached ) {
			$this->flush_widget_cache();
		}
		
		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @param array $instance
	 */
	public function form( $instance ) {
		if ( ! $this->settings ) {
			return;
		}
		foreach ( $this->settings as $key => $setting ) {
			$value = isset( $instance[$key] ) ? $instance[$key] : $setting['std'];
			switch ( $setting['type'] ) {
				case "text" :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
					break;
				
				case "number" :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
					break;
				case "select" :
					if ( isset( $setting['multiple'] ) ) :
						$value = explode( ',', $value );					
					endif;
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" <?php if(isset($setting['multiple'])):?> multiple="multiple" <?php endif;?> name="<?php echo $this->get_field_name( $key ); ?><?php if(isset($setting['multiple'])):?>[]<?php endif;?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>"
								<?php if(isset($setting['multiple'])): selected( in_array ( $option_key, $value ) , true ); else: selected( $option_key, $value ); endif; ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
					break;
				
				case "checkbox" :
					?>
					<p>
						<input id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> /> <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					</p>
					<?php
					break;
			}
		}
	}
}

class Noo_Tweets extends WP_Widget {

	public function __construct() {
		parent::__construct( 
			'noo_tweets',  // Base ID
			'Recent Tweets',  // Name
			array( 'classname' => 'tweets-widget', 'description' => __( 'Display recent tweets', 'noo' ) ) );
	}

	public function widget( $args, $instance ) {
		extract( $args );
		if ( ! empty( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}
		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}
		
		// check settings and die if not set
		if ( empty( $instance['consumerkey'] ) || empty( $instance['consumersecret'] ) || empty( 
			$instance['accesstoken'] ) || empty( $instance['accesstokensecret'] ) || empty( $instance['cachetime'] ) ||
			 empty( $instance['username'] ) ) {
			echo '<strong>' . __( 'Please fill all widget settings!', 'noo' ) . '</strong>' . $after_widget;
			return;
		}
		
		$noo_widget_recent_tweets_cache_time = get_option( 'noo_widget_recent_tweets_cache_time' );
		$diff = time() - $noo_widget_recent_tweets_cache_time;
		
		$crt = (int) $instance['cachetime'] * 3600;
		
		if(1==1 || $diff >= $crt || empty($noo_widget_recent_tweets_cache_time)){
			
			if ( ! require_once ( dirname(__FILE__) . '/twitteroauth.php' ) ) {
				echo '<strong>' . __( 'Couldn\'t find twitteroauth.php!', 'noo' ) . '</strong>' . $after_widget;
				return;
			}

			function getConnectionWithAccessToken( $cons_key, $cons_secret, $oauth_token, $oauth_token_secret ) {
				$connection = new TwitterOAuth( $cons_key, $cons_secret, $oauth_token, $oauth_token_secret );
				return $connection;
			}
			
			$connection = getConnectionWithAccessToken( 
				$instance['consumerkey'], 
				$instance['consumersecret'], 
				$instance['accesstoken'], 
				$instance['accesstokensecret'] );
			$tweets = $connection->get( 
				"https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=" . $instance['username'] .
					 "&count=10&exclude_replies=" . $instance['excludereplies'] );
			
			if ( ! empty( $tweets->errors ) ) {
				if ( $tweets->errors[0]->message == 'Invalid or expired token' ) {
					echo '<strong>' . $tweets->errors[0]->message . '!</strong><br/>' . __( 
						'You\'ll need to regenerate it <a href="#" target="_blank">here</a>!', 
						'noo' ) . $after_widget;
				} else {
					echo '<strong>' . $tweets->errors[0]->message . '</strong>' . $after_widget;
				}
				return;
			}
			
			$tweets_array = array();
			for ( $i = 0; $i <= count( $tweets ); $i++ ) {
				if ( ! empty( $tweets[$i] ) ) {
					$tweets_array[$i]['created_at'] = $tweets[$i]->created_at;
					$tweets_array[$i]['name']	=	$tweets[$i]->user->name;
					$tweets_array[$i]['screen_name'] = $tweets[$i]->user->screen_name;
					$tweets_array[$i]['profile_image_url'] = $tweets[$i]->user->profile_image_url;
					// clean tweet text
					$tweets_array[$i]['text'] = preg_replace( '/[\x{10000}-\x{10FFFF}]/u', '', $tweets[$i]->text );
					
					if ( ! empty( $tweets[$i]->id_str ) ) {
						$tweets_array[$i]['status_id'] = $tweets[$i]->id_str;
					}
				}
			}
			update_option( 'noo_widget_recent_tweets', serialize( $tweets_array ) );
			update_option( 'noo_widget_recent_tweets_cache_time', time() );
		}
		
		$noo_widget_recent_tweets = maybe_unserialize( get_option( 'noo_widget_recent_tweets' ) );
		if ( ! empty( $noo_widget_recent_tweets ) ) {
			echo '<div class="recent-tweets"><ul>';
			$i = '1';
			foreach ( $noo_widget_recent_tweets as $tweet ) {
				
				if ( ! empty( $tweet['text'] ) ) {
					if ( empty( $tweet['status_id'] ) ) {
						$tweet['status_id'] = '';
					}
					if ( empty( $tweet['created_at'] ) ) {
						$tweet['created_at'] = '';
					}
					
					echo '<li><span class="twitter_username">@'.$tweet['screen_name'].'</span><span>' . $this->_convert_links( $tweet['text'] ) .
						 '</span></li>';
					if ( $i == $instance['tweetstoshow'] ) {
						break;
					}
					$i++;
				}
			}
			
			echo '</ul></div>';
		}
		
		echo $after_widget;
	}

	protected function _convert_links( $status, $targetBlank = true, $linkMaxLen = 50 ) {
		// the target
		$target = $targetBlank ? " target=\"_blank\" " : "";
		
		// convert link to url
		$status = preg_replace( 
			"/((http:\/\/|https:\/\/)[^ )]+)/i", 
			"<a href=\"$1\" title=\"$1\" $target >$1</a>", 
			$status );
		
		// convert @ to follow
		$status = preg_replace( 
			"/(@([_a-z0-9\-]+))/i", 
			"<a href=\"http://twitter.com/$2\" title=\"Follow $2\" $target >$1</a>", 
			$status );
		
		// convert # to search
		$status = preg_replace( 
			"/(#([_a-z0-9\-]+))/i", 
			"<a href=\"https://twitter.com/search?q=$2\" title=\"Search $1\" $target >$1</a>", 
			$status );
		
		// return the status
		return $status;
	}

	protected function _relative_time( $a = '' ) {
		// get current timestampt
		$b = strtotime( "now" );
		// get timestamp when tweet created
		$c = strtotime( $a );
		// get difference
		$d = $b - $c;
		// calculate different time values
		$minute = 60;
		$hour = $minute * 60;
		$day = $hour * 24;
		$week = $day * 7;
		
		if ( is_numeric( $d ) && $d > 0 ) {
			// if less then 3 seconds
			if ( $d < 3 )
				return "right now";
				// if less then minute
			if ( $d < $minute )
				return sprintf( __( "%s seconds ago", 'noo' ), floor( $d ) );
				// if less then 2 minutes
			if ( $d < $minute * 2 )
				return __( "about 1 minute ago", 'noo' );
				// if less then hour
			if ( $d < $hour )
				return sprintf( __( '%s minutes ago', 'noo' ), floor( $d / $minute ) );
				// if less then 2 hours
			if ( $d < $hour * 2 )
				return __( "about 1 hour ago", 'noo' );
				// if less then day
			if ( $d < $day )
				return sprintf( __( "%s hours ago", 'noo' ), floor( $d / $hour ) );
				// if more then day, but less then 2 days
			if ( $d > $day && $d < $day * 2 )
				return __( "yesterday", 'noo' );
				// if less then year
			if ( $d < $day * 365 )
				return sprintf( __( '%s days ago', 'noo' ), floor( $d / $day ) );
				// else return more than a year
			return __( "over a year ago", 'noo' );
		}
	}

	public function form( $instance ) {
		$defaults = array( 
			'title' => '', 
			'consumerkey' => '', 
			'consumersecret' => '', 
			'accesstoken' => '', 
			'accesstokensecret' => '', 
			'cachetime' => '', 
			'username' => '', 
			'tweetstoshow' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		echo '
		<p>
			<label>' . __( 'Title', 'noo' ) . ':</label>
			<input type="text" name="' .
			 $this->get_field_name( 'title' ) . '" id="' . $this->get_field_id( 'title' ) . '" value="' .
			 esc_attr( $instance['title'] ) . '" class="widefat" />
		</p>
		<p>
			<label>' . __( 'Consumer Key', 'noo' ) . ':</label>
			<input type="text" name="' .
			 $this->get_field_name( 'consumerkey' ) . '" id="' . $this->get_field_id( 'consumerkey' ) . '" value="' .
			 esc_attr( $instance['consumerkey'] ) . '" class="widefat" />
		</p>
		<p>
			<label>' . __( 'Consumer Secret', 'noo' ) . ':</label>
			<input type="text" name="' .
			 $this->get_field_name( 'consumersecret' ) . '" id="' . $this->get_field_id( 'consumersecret' ) . '" value="' .
			 esc_attr( $instance['consumersecret'] ) . '" class="widefat" />
		</p>
		<p>
			<label>' . __( 'Access Token', 'noo' ) . ':</label>
			<input type="text" name="' .
			 $this->get_field_name( 'accesstoken' ) . '" id="' . $this->get_field_id( 'accesstoken' ) . '" value="' .
			 esc_attr( $instance['accesstoken'] ) . '" class="widefat" />
		</p>
		<p>
			<label>' . __( 'Access Token Secret', 'noo' ) . ':</label>
			<input type="text" name="' .
			 $this->get_field_name( 'accesstokensecret' ) . '" id="' . $this->get_field_id( 'accesstokensecret' ) .
			 '" value="' . esc_attr( $instance['accesstokensecret'] ) . '" class="widefat" />
		</p>
		<p>
			<label>' .
			 __( 'Cache Tweets in every', 'noo' ) . ':</label>
			<input type="text" name="' .
			 $this->get_field_name( 'cachetime' ) . '" id="' . $this->get_field_id( 'cachetime' ) . '" value="' .
			 esc_attr( $instance['cachetime'] ) . '" class="small-text" />' . __( 'hours', 'noo' ) . '
		</p>
		<p>
			<label>' . __( 'Twitter Username', 'noo' ) . ':</label>
			<input type="text" name="' .
			 $this->get_field_name( 'username' ) . '" id="' . $this->get_field_id( 'username' ) . '" value="' .
			 esc_attr( $instance['username'] ) . '" class="widefat" />
		</p>
		<p>
			<label>' . __( 'Tweets to display', 'noo' ) . ':</label>
			<select type="text" name="' .
			 $this->get_field_name( 'tweetstoshow' ) . '" id="' . $this->get_field_id( 'tweetstoshow' ) . '">';
		$i = 1;
		for ( $i; $i <= 10; $i++ ) {
			echo '<option value="' . $i . '"';
			if ( $instance['tweetstoshow'] == $i ) {
				echo ' selected="selected"';
			}
			echo '>' . $i . '</option>';
		}
		echo '
			</select>
		</p>
		<p>
			<label>' . __( 'Exclude replies', 'noo' ) . ':</label>
			<input type="checkbox" name="' .
			 $this->get_field_name( 'excludereplies' ) . '" id="' . $this->get_field_id( 'excludereplies' ) .
			 '" value="true"';
		if ( ! empty( $instance['excludereplies'] ) && esc_attr( $instance['excludereplies'] ) == 'true' ) {
			echo ' checked="checked"';
		}
		echo '/></p>';
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['consumerkey'] = strip_tags( $new_instance['consumerkey'] );
		$instance['consumersecret'] = strip_tags( $new_instance['consumersecret'] );
		$instance['accesstoken'] = strip_tags( $new_instance['accesstoken'] );
		$instance['accesstokensecret'] = strip_tags( $new_instance['accesstokensecret'] );
		$instance['cachetime'] = strip_tags( $new_instance['cachetime'] );
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['tweetstoshow'] = strip_tags( $new_instance['tweetstoshow'] );
		$instance['excludereplies'] = strip_tags( $new_instance['excludereplies'] );
		
		if ( $old_instance['username'] != $new_instance['username'] ) {
			delete_option( 'noo_widget_recent_tweets_cache_time' );
		}
		
		return $instance;
	}
}



// class Noo_Class_Widget extends Noo_Wiget {

// 	public function __construct() {
// 		$this->widget_cssclass = 'noo-class-widget';
// 		$this->widget_description = __( "Display Noo Class.", 'noo' );
// 		$this->widget_id = 'noo_class_widget';
// 		$this->widget_name = __( 'Noo Class', 'noo' );
// 		$this->true = false;
// 		$categories = get_terms( 'class_category', array( 'orderby' => 'NAME', 'order' => 'ASC' ) );
// 		$categories_options = array( '' => __('All', 'noo') );
// 		foreach ( (array) $categories as $category ) {
// 			$categories_options[$category->term_id] = $category->name;
// 		}
// 		$this->settings = array( 
// 			'title' => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ), 
// 			'posts_per_page' => array( 
// 				'type' => 'number', 
// 				'step' => 1, 
// 				'min' => 1, 
// 				'max' => '', 
// 				'std' => 5, 
// 				'label' => __( 'Number of Class to show', 'noo' ) ), 
// 			'categories' => array( 
// 				'type' => 'select', 
// 				'std' => '', 
// 				'multiple' => true, 
// 				'label' => __( 'Categories', 'noo' ), 
// 				'options' => $categories_options ), 
// 			'orderby' => array( 
// 				'type' => 'select', 
// 				'std' => 'date', 
// 				'label' => __( 'Order by', 'noo' ), 
// 				'options' => array( 
// 					'latest' => __( 'Latest', 'noo' ),
// 					'oldest' => __( 'Oldest', 'noo' ),
// 					'comment' => __( 'Most Popular', 'noo' ) ) ));
// 		parent::__construct();
// 	}

// 	public function widget( $args, $instance ) {
// 		ob_start();
// 		extract( $args );
// 		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
// 		$posts_per_page = absint( $instance['posts_per_page'] );
// 		$orderby = sanitize_title( $instance['orderby'] );
// 		$categories = $instance['categories'];
// 		$locations = $instance['locations'];
// 		$query_args = array( 
// 			'posts_per_page' => $posts_per_page, 
// 			'post_status' => 'publish', 
// 			'ignore_sticky_posts' => 1, 
// 			'orderby' => 'date', 
// 			'post_type' => 'noo_class', 
// 			"meta_key" => "_thumbnail_id", 
// 			'order' => ($orderby == 'oldest') ? 'ASC' : 'DESC' );
// 		if ( $orderby == 'comment' ) {
// 			$query_args['orderby'] = 'comment_count';
// 		}

// 		$tax_query = array();
// 		if ( ! empty( $categories ) ) {
// 			$tax_query[] = array( 'taxonomy' => 'cause_category',
// 					'field' => 'term_id',
// 					'terms' => explode(',', $categories)
// 				);
// 		}
// 		if ( ! empty( $locations ) ) {
// 			$tax_query[] = array( 'taxonomy' => 'cause_location',
// 					'field' => 'term_id',
// 					'terms' => explode(',', $locations)
// 				);
// 		}

// 		if ( ! empty( $tax_query ) ) {
// 			$query_args['tax_query'] = $tax_query;
// 		}

// 		$r = new WP_Query( $query_args );
// 		if ( $r->have_posts() ) :
// 			echo $before_widget;
// 			if ( $title )
// 				echo $before_title . $title . $after_title;
// 			echo '<ul class="noo-cause-list">';
// 			while ( $r->have_posts() ) :
// 				$r->the_post();
// 				echo '<li>';
// 				echo '<div class="noo-cause-item">';
// 				echo '<a href="' . esc_url( get_the_permalink() ) . '">' . get_the_post_thumbnail( 
// 					null, 
// 					'noo-thumbnail-square', 
// 					array( 'title' => strip_tags( get_the_title() ) ) ) . '</a>';
// 				echo '</div>';
// 				echo '<div class="noo-cause-content">';
// 				echo '<h4><a href="' . esc_url( get_the_permalink() ) . '" title="' . esc_attr( get_the_title() ) . '">' .
// 					 get_the_title() . '</a></h4>';
// 				echo '<div class="content-meta">';
// 				echo '<span>';	
// 				echo  get_the_term_list(get_the_ID(), 'cause_location','',', ');
// 				echo '</span>';	
// 				echo '</div>';
// 				echo '</div>';
// 				echo '</li>';
// 			endwhile
// 			;
// 			echo '</ul>';
// 			echo $after_widget;
		
		
		
// 		endif;
// 		wp_reset_postdata();
// 		wp_reset_query();
// 		$content = ob_get_clean();
		
// 		echo $content;
// 	}
// }

class Noo_Event_Widget extends Noo_Wiget {

	public function __construct() {
		$this->widget_cssclass = 'noo-event-widget';
		$this->widget_description = __( "Display Noo Event.", 'noo' );
		$this->widget_id = 'noo_event_widget';
		$this->widget_name = __( 'Noo Event', 'noo' );
		$this->cached = true;
		$categories = get_terms( 'event_category', array( 'orderby' => 'NAME', 'order' => 'ASC' ) );
		$categories_options = array( '' => __('All', 'noo') );
		if ( is_array($categories) && count($categories) > 0 ) {
			foreach ( (array) $categories as $category ) {
				$categories_options[$category->term_id] = $category->name;
			}
		}
		$locations = get_terms( 'event_location', array( 'orderby' => 'NAME', 'order' => 'ASC' ) );
		$locations_options = array( '' => __('All', 'noo') );
		if ( is_array($locations) && count($locations) > 0 ) {
			foreach ( (array) $locations as $location ) {
				$locations_options[$location->term_id] = $location->name;
			}
		}
		$this->settings = array( 
			'title' => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ), 
			'posts_per_page' => array( 
				'type' => 'number', 
				'step' => 1, 
				'min' => 1, 
				'max' => '', 
				'std' => 5, 
				'label' => __( 'Number of Events to show', 'noo' ) ),
			'categories' => array( 
				'type' => 'select', 
				'std' => '', 
				'multiple' => true, 
				'label' => __( 'Categories', 'noo' ), 
				'options' => $categories_options ), 
			'locations' => array( 
				'type' => 'select', 
				'std' => '', 
				'multiple' => true, 
				'label' => __( 'Locations', 'noo' ), 
				'options' => $locations_options ), 
			'status' => array( 
				'type' => 'select', 
				'std' => '', 
				'label' => __( 'Event Status', 'noo' ), 
				'options' => array(
					'' => __( 'All Events', 'noo' ), 
					'up_comming' => __( 'Up Comming', 'noo' ), 
					'past' => __( 'Past Event', 'noo' ) ) ),
			'orderby' => array( 
				'type' => 'select', 
				'std' => 'startdate', 
				'label' => __( 'Order Events by', 'noo' ), 
				'options' => array(
					'startdate' => __( 'Start Date', 'noo' ),
					'adddate' => __( 'Added Date', 'noo' ),
					'comment' => __( 'Most Popular', 'noo' ) ) ), 
			'order' => array( 
				'type' => 'select', 
				'std' => 'desc', 
				'label' => __( 'Order Direction', 'noo' ), 
				'options' => array(
					'desc' => __( 'Descending ( Newest )', 'noo' ),
					'asc' => __( 'Assending ( Oldest )', 'noo' ) ) )
			);
		parent::__construct();
	}

	public function widget( $args, $instance ) {
		ob_start();
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$posts_per_page = absint( $instance['posts_per_page'] );
		$orderby = sanitize_title( $instance['orderby'] );
		$order = sanitize_title( $instance['order'] );
		$status = sanitize_title( $instance['status'] );
		$categories = isset($instance['categories']) ? $instance['categories'] : 0;
		$locations = isset($instance['locations']) ? $instance['locations'] : 0;

		global $wpdb;
		$query_args = array( 
			'posts_per_page' => $posts_per_page, 
			'post_status' => 'publish', 
			'ignore_sticky_posts' => 1, 
			'post_type' => 'noo_event' );
			// "meta_key" => "_thumbnail_id", 
			// 'order' => ($orderby == 'oldest') ? 'ASC' : 'DESC' );
		if ( $orderby == 'comment' ) {
			$query_args['orderby'] = 'comment_count';
			$query_args['order'] = ($order == 'asc') ? 'ASC' : 'DESC';
		} elseif ( $orderby == 'adddate' ) {
			$query_args['orderby'] = 'date';
			$query_args['order'] = ($order == 'asc') ? 'ASC' : 'DESC';
		} else {
			$query_args['meta_key'] = '_noo_event_start_date';
			$query_args['orderby'] = 'meta_value_num date';
			$query_args['order'] = ($order == 'asc') ? 'ASC' : 'DESC';
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
			$query_args['tax_query'] = $tax_query;
		}

		if( $status == 'up_comming' ) {
			$up_comming = Noo_Event::get_up_comming_events();
			if( ! is_array( $up_comming ) || empty( $up_comming ) ) {
				$up_comming = array(0);	
			}
			$query_args['post__in'] = $up_comming;
		}

		if( $status == 'past' ) {
			$past = Noo_Event::get_past_events();
			if( ! is_array( $past ) || empty( $past ) ) {
				$past = array(0);
			}
			$query_args['post__in'] = $past;
		}

		$r = new WP_Query( $query_args );
		if ( $r->have_posts() ) :
			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;
			echo '<ul class="noo-event-list">';
			while ( $r->have_posts() ) :
				$r->the_post();
				$event_address		= noo_get_post_meta( get_the_ID(), "_noo_event_address", '' );
				$event_starttime	= noo_get_post_meta( get_the_ID(), "_noo_event_start_time", '' );
				$event_startdate	= noo_get_post_meta( get_the_ID(), "_noo_event_start_date", '' );
				// $location = get_the_term_list(get_the_ID(), 'event_location','',', ');
				echo '<li>';
				echo '<div class="noo-event-item">';
				echo '<a href="' . esc_url( get_the_permalink() ) . '">' . get_the_post_thumbnail( 
					null, 
					'noo-thumbnail-square', 
					array( 'title' => strip_tags( get_the_title() ) ) ) . '</a>';
				echo '</div>';
				echo '<div class="noo-event-content">';
				echo '<h4><a href="' . esc_url( get_the_permalink() ) . '" title="' . esc_attr( get_the_title() ) . '">' .
					 get_the_title() . '</a></h4>';
				echo '<div class="content-meta">';
				if( !empty( $event_starttime ) ) :
				echo '<span>';	
				echo date(get_option('time_format'),$event_starttime);
				echo '</span>';	
				endif;
				if( !empty( $event_startdate ) ) :
				echo '<span>';	
				echo date(get_option('date_format'),$event_startdate);
				echo '</span>';	
				endif;
				if( !empty( $location ) ) :
				echo '<span>';	
				echo $location;
				echo '</span>';	
				endif;
				echo '</div>';
				echo '</div>';
				echo '</li>';
			endwhile
			;
			echo '</ul>';
			echo $after_widget;
		
		

		endif;
		wp_reset_postdata();
		wp_reset_query();
		$content = ob_get_clean();
		
		echo $content;
	}
}

class Noo_Class_Category_Widget extends Noo_Wiget {

	public function __construct() {
		$this->widget_cssclass = 'noo-cause-category-widget';
		$this->widget_description = __( "Display Noo Class Categories.", 'noo' );
		$this->widget_id = 'noo_class_category_widget';
		$this->widget_name = __( 'Noo Class Categories', 'noo' );
		$this->cached = true;
		$this->settings = array( 
			'title' => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ), 
			'count' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show post counts', 'noo' )
			),
			'hierarchical' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show hierarchy', 'noo' )
			),
		);
		parent::__construct();
	}

	public function widget( $args, $instance ) {
		ob_start();
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$cat_args = array('taxonomy'=>'class_category','orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h);
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		$categories = get_categories( $cat_args );
		echo '<ul>';
		echo  walk_category_tree( $categories, 0, array(
			'style'=>'list',
			'show_count'=>$c,
			'hierarchical'=>$h,
			'use_desc_for_title'=>1,
		));
		echo '</ul>';
		
		echo $args['after_widget'];
	}
}


class Noo_Event_Category_Widget extends Noo_Wiget {

	public function __construct() {
		$this->widget_cssclass = 'noo-event-category-widget';
		$this->widget_description = __( "Display Noo Event Categories.", 'noo' );
		$this->widget_id = 'noo_event_category_widget';
		$this->widget_name = __( 'Noo Event Categories', 'noo' );
		$this->cached = true;
		$this->settings = array(
			'title' => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ),
			'count' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show post counts', 'noo' )
			),
			'hierarchical' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show hierarchy', 'noo' )
			),
		);
		parent::__construct();
	}

	public function widget( $args, $instance ) {
		ob_start();
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$cat_args = array('taxonomy'=>'event_category','orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h);
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		$categories = get_categories( $cat_args );
		echo '<ul>';
		echo  walk_category_tree( $categories, 0, array(
			'style'=>'list',
			'show_count'=>$c,
			'hierarchical'=>$h,
			'use_desc_for_title'=>1,
		));
		echo '</ul>';

		echo $args['after_widget'];
	}
}


class WP_widget_latest_posts extends WP_Widget {

	public function __construct() {
		$widget_ops = array('classname' => 'widget_latest_posts', 'description' => __( "Your site&#8217;s latest Posts.",'noo') );
		parent::__construct('recent-news', __('Latest Posts','noo'), $widget_ops);
		$this->alt_option_name = 'widget_latest_posts';

		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	public function widget($args, $instance) {
		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_latest_posts', 'widget' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Latest Posts','noo' );

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		/**
		 * Filter the arguments for the Recent Posts widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Query::get_posts()
		 *
		 * @param array $args An array of arguments used to retrieve the recent posts.
		 */
		$r = new WP_Query( apply_filters( 'widget_news_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'order'          => 'DESC',
		) ) );

		if ($r->have_posts()) :
?>
		
		<?php echo $args['before_widget']; ?>
		<?php if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
		<ul>
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li class="clearfix">
			 <?php if ( has_post_thumbnail() ):
	            the_post_thumbnail(array(70, 70));
	        else: ?>
	            <img width="70" height="70" src="<?php echo NOO_ASSETS_URI.'/images/no-image.png' ; ?>" alt="<?php the_title_attribute(); ?>" />
	        <?php endif;  ?>
				<h5><a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a></h5>
			<?php if ( $show_date ) : ?>
				<span class="post-date"><?php echo get_the_date(); ?></span>
			<?php endif; ?>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $args['after_widget']; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_latest_posts', $cache, 'widget' );
		} else {
			ob_end_flush();
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_entries']) )
			delete_option('widget_recent_entries');

		return $instance;
	}

	public function flush_widget_cache() {
		wp_cache_delete('widget_latest_posts', 'widget');
	}

	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','noo' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:','noo' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?','noo' ); ?></label></p>
<?php
	}
}

class Noo_MailChimp extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'noo_mailchimp_widget',  // Base ID
            'Noo MailChimps',  // Name
            array( 'classname' => 'mailchimp-widget', 'description' => esc_html__( 'Display simple MailChimp subscribe form.', 'noo' ) ) );
    }

    public function widget( $args, $instance ) {
        extract( $args );
        if ( ! empty( $instance['title'] ) ) {
            $title = apply_filters( 'widget_title', $instance['title'] );
        }
        echo $before_widget;
        if ( ! empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }

        $mail_list;
        ?>
        <form method="post" class="mc-subscribe-form<?php echo (isset($_COOKIE['noo_subscribed']) ? ' submited':'')?>">
            <?php if( isset($_COOKIE['noo_subscribed']) ) : ?>
                <label class="noo-message alert" role="alert"><?php _e( 'You\'ve already subscribed.', 'noo' ); ?></label>
            <?php else: ?>
                <label for="mc_email"><span><?php echo esc_attr( $instance['subscribe_text'] ); ?></span></label>
                <div class="mc-content">
                    <input type="email" id="mc_email" name="mc_email" class="mc-email" value="" placeholder="<?php _e( 'Enter your email', 'noo' ); ?>" />
                    <button class="btn-primary mailchip-sm"><?php _e( 'Submit', 'noo' ); ?></button>
                </div>
                <input type="hidden" name="mc_list_id" value="<?php echo esc_attr( $instance['mail_list'] ); ?>" />
                <input type="hidden" name="action" value="noo_mc_subscribe" />
                <?php wp_nonce_field('noo-subscribe','nonce'); ?>
            <?php endif; ?>
        </form>
        <?php
        echo $after_widget;
    }

    public function form( $instance ) {
        $defaults = array(
            'title' => '',
            'subscribe_text' => esc_html__( 'Subscribe to stay update', 'noo' ),
            'mail_list' => ''
        );
        $instance = wp_parse_args( (array) $instance, $defaults );

        global $noo_mailchimp;
        $api_key = noo_get_option('noo_mailchimp_api_key', '');
        $mail_list = !empty( $api_key ) ? $noo_mailchimp->get_mail_lists( $api_key ) : '';

        echo '
		<p>
			<label>' . esc_html__( 'Title', 'noo' ) . ':</label>
			<input type="text" name="' .
            $this->get_field_name( 'title' ) . '" id="' . $this->get_field_id( 'title' ) . '" value="' .
            esc_attr( $instance['title'] ) . '" class="widefat" />
		</p>
		<p>
			<label>' . esc_html__( 'Subscribe Text', 'noo' ) . ':</label>
			<input type="text" name="' .
            $this->get_field_name( 'subscribe_text' ) . '" id="' . $this->get_field_id( 'subscribe_text' ) . '" value="' .
            esc_attr( $instance['subscribe_text'] ) . '" class="widefat" />
		</p>';
        if(!empty($mail_list)) {
            echo '
		<p>
			<label>' . esc_html__( 'Subscribe Mail List', 'noo' ) . ':</label>
			<select name="' .
                $this->get_field_name( 'mail_list' ) . '" id="' . $this->get_field_id( 'mail_list' ) . '" class="widefat" >';
            foreach($mail_list as $id => $list_name) {
                echo '<option value="' . esc_attr($id) . '" ' . selected( $instance['mail_list'], $id, false ) . '>' . esc_html($list_name) . '</option>';
            }
            echo '	</select>
		</p>';
        } else {
            echo '<p>' . esc_html__( 'There\'s problem get your mail list, please check your MailChimp API Key settings in Customizer', 'noo' ) . '</p>';
        }
    }
}


function noo_register_widget() {
	register_widget('WP_widget_latest_posts');
	if( class_exists('Noo__Timetable__Event') ) {
		register_widget( 'Noo_Event_Widget' );
		register_widget('Noo_Event_Category_Widget');
	}

	if( class_exists('Noo__Timetable__Class')) {
		register_widget('Noo_Class_Category_Widget');
	}

	// if( noo_get_option( 'noo_mailchimp', true ) ) {
		
 //        $subscribe_mail_list = noo_get_option( 'noo_mailchimp_api_key', '' );
 //        if( ! empty( $subscribe_mail_list ) ) {
 //            register_widget( 'Noo_MailChimp' );
 //        }
        
 //    }

	register_widget( 'Noo_Tweets' );
}
add_action( 'widgets_init', 'noo_register_widget' );

class WP_Widget_Class_Slider extends WP_Widget {

    public function __construct() {
        $widget_ops = array('classname' => 'widget_class_slider', 'description' => __( "Your site&#8217;s most recent Posts.",'noo') );
        parent::__construct('class-slider', __('Class Slider','noo'), $widget_ops);
        $this->alt_option_name = 'widget_class_slider';

        add_action( 'save_post', array($this, 'flush_widget_cache') );
        add_action( 'deleted_post', array($this, 'flush_widget_cache') );
        add_action( 'switch_theme', array($this, 'flush_widget_cache') );
    }

    public function widget($args, $instance) {
        $cache = array();
        if ( ! $this->is_preview() ) {
            $cache = wp_cache_get( 'widget_class_slider', 'widget' );
        }

        if ( ! is_array( $cache ) ) {
            $cache = array();
        }

        if ( ! isset( $args['widget_id'] ) ) {
            $args['widget_id'] = $this->id;
        }

        if ( isset( $cache[ $args['widget_id'] ] ) ) {
            echo $cache[ $args['widget_id'] ];
            return;
        }

        ob_start();

        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Post Slider','noo' );

        /** This filter is documented in wp-includes/default-widgets.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number )
            $number = 5;
        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

        /**
         * Filter the arguments for the Recent Posts widget.
         *
         * @since 3.4.0
         *
         * @see WP_Query::get_posts()
         *
         * @param array $args An array of arguments used to retrieve the recent posts.
         */
        $show_type  = isset( $instance['show_type'] ) ? $instance['show_type'] : 'all';
        $new_args = array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_type'           => 'noo_class',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true
        );

        if ( 'coming' == $show_type ) {
        	$new_args['meta_key'] = '_next_date';
        	$new_args['orderby'] = 'meta_value_num';
        	$new_args['order'] = 'ASC';
        	$new_args['post__in'] = Noo_Class::get_coming_class_ids();
        }

        $r = new WP_Query( apply_filters( 'widget_news_args', $new_args ) );
        wp_enqueue_script('vendor-imagesloaded');
        wp_enqueue_script('vendor-carouFredSel');
        $posts_in_column = 1;
        $columns = 1;
        $noo_post_uid  		= uniqid('noo_class_');
        $class = '';
        $class .= ' '.$noo_post_uid;
        $class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
        if ($r->have_posts()) :
            ?>
            <?php echo $args['before_widget']; ?>
            <?php if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        } ?>
            <div <?php echo $class?>>

                <div class="row">
                    <div class="widget-class-slider-content">

                        <?php $i=0; ?>
                        <?php while ($r->have_posts()): $r->the_post(); global $post;
                            ?>

                            <?php if($i++ % $posts_in_column == 0 ): ?>
                                <div class="noo-class-slider-item col-sm-<?php echo absint((12 / $columns)) ?>">
                            <?php endif; ?>
                            <div class="noo-class-slider-inner">
                                <div class="class-slider-featured" >
                                    <a href="<?php the_permalink() ?>">
                                        <?php the_post_thumbnail('noo-thumbnail-square')?>
                                    </a>
                                </div>
                                <div class="class-slider-content">
                                    <div class="class-slider-category"><?php echo get_the_term_list(get_the_ID(), 'class_category',' ',', ')?></div>
                                    <h5 class="class-slider-title">
                                        <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
                                    </h5>
                                </div>
                            </div>
                            <?php if($i % $posts_in_column == 0  || $i == $r->post_count): ?>
                                </div>
                            <?php endif;?>

                        <?php endwhile;?>
                    </div>
                </div>
                <div id="noo-class-slider-pagination" class="slider-indicators"></div>
            </div>
            <script type="text/javascript">
                jQuery('document').ready(function ($) {
                    var postSliderOptions = {
                        infinite: true,
                        circular: true,
                        responsive: true,
                        debug : false,
                        width: '100%',
                        height: 'variable',
                        scroll: {
                            items: <?php echo $columns;?>,
                            duration: 600,
                            pauseOnHover: "resume",
                            fx: "scroll"
                        },
                        auto: {
                            timeoutDuration: 3000,
                            play: false
                        },

                        prev : {button:".<?php echo $noo_post_uid ?> .noo_slider_prev"},
                        next : {button:".<?php echo $noo_post_uid ?> .noo_slider_next"},
                        swipe: {
                            onTouch: true,
                            onMouse: true
                        },
                        items: {
                            visible: {
                                min: 1,
                                max: <?php echo $columns;?>
                            },
                            height:'variable'
                        },
                        pagination: {
                            container: "#noo-class-slider-pagination"
                        }
                    };
                    jQuery('.<?php echo $noo_post_uid ?> .widget-class-slider-content').carouFredSel(postSliderOptions);
                    imagesLoaded('<?php echo $noo_post_uid ?> .widget-class-slider-content',function(){
                        jQuery('.<?php echo $noo_post_uid ?> .widget-class-slider-content').trigger('updateSizes');
                    });
                    jQuery(window).resize(function(){
                        jQuery('.<?php echo $noo_post_uid ?> .widget-class-slider-content').trigger("destroy").carouFredSel(postSliderOptions);
                    });

                });
            </script>
            <?php echo $args['after_widget']; ?>
            <?php
            // Reset the global $the_post as this query will have stomped on it
            wp_reset_postdata();

        endif;

        if ( ! $this->is_preview() ) {
            $cache[ $args['widget_id'] ] = ob_get_flush();
            wp_cache_set( 'widget_class_slider', $cache, 'widget' );
        } else {
            ob_end_flush();
        }
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
        $instance['show_type'] = isset( $new_instance['show_type'] ) ?  strip_tags( $new_instance['show_type'] ) : 'all';
        $this->flush_widget_cache();

        $alloptions = wp_cache_get( 'alloptions', 'options' );
        if ( isset($alloptions['widget_recent_entries']) )
            delete_option('widget_recent_entries');

        return $instance;
    }

    public function flush_widget_cache() {
        wp_cache_delete('widget_class_slider', 'widget');
    }

    public function form( $instance ) {
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        $show_type = isset( $instance['show_type'] ) ? esc_attr( $instance['show_type'] ) : 'all';
        ?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','noo' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

        <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:','noo' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

        <p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?','noo' ); ?></label></p>
		<?php
		$array_show_type = array(
			'all'    => __('All Class', 'noo'),
			'recent' => __('Recent Class', 'noo'),
			'coming' => __('Coming Class', 'noo'),
		);
		?>
       <!--  <p>
			<label for="<?php echo $this->get_field_id( 'show_type' ); ?>"><?php _e('Show type', 'noo'); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_type' ) ); ?>" name="<?php echo $this->get_field_name( 'show_type' ); ?>">
				<?php foreach ( $array_show_type as $option_key => $option_value ) : ?>
					<option value="<?php echo esc_attr( $option_key ); ?>"
					<?php if($option_key == $show_type): echo 'selected'; endif; ?>><?php echo esc_html( $option_value ); ?></option>
				<?php endforeach; ?>
			</select>
		</p> -->
    <?php
    }
}

if ( class_exists('Noo__Timetable__Class') ) :
	register_widget('WP_Widget_Class_Slider');
endif;

}