<?php


/* =============================================================================
 *
 * Function for specific theme, remember to keep all the functions
 * specified for this theme inside this file.
 *
 * ============================================================================*/

// Define theme specific constant
if (!defined('NOO_THEME_NAME'))
{
  define('NOO_THEME_NAME', 'noo-yogi');
}

if (!defined('NOO_THEME_VERSION'))
{
  define('NOO_THEME_VERSION', '0.0.1');
}

if( !function_exists('noo_relative_time') ) :
function noo_relative_time($a=''){
	$c = strtotime($a);
	return human_time_diff($c);
}
endif;

if( !function_exists('noo_get_term_meta') ) :
function noo_get_term_meta( $term_id = null, $meta_key = '', $default = null ) {
	if( empty( $term_id ) || empty( $meta_key) ) {
		return null;
	}

	$term_meta = get_option( 'taxonomy_' . $term_id );
	$value = isset( $term_meta[$meta_key] ) ? $term_meta[$meta_key] : null;

	if( ( $value === null || $value === '' ) && ( $default != null && $default != '' ) ) {
		$value = $default;
	}

	return apply_filters( 'noo_term_meta', $value, $term_id, $meta_key, $default );
}
endif;

//get time for event countdown
if( !function_exists('noo_countdown_time') ) :
function noo_countdown_time($a=''){

	//get current timestampt
	$b = strtotime("now");
	//get difference
	$d = $a - $b;
	if($d > 0) {
		return $d;
	} else {
		return 0;
	}
}
endif;

if( !function_exists('noo_nth_word') ) :
function noo_nth_word($text, $nth = 1, $echo = true,$is_typed = false,$typed_color = ''){
	$text = strip_shortcodes($text);
	$text = wp_strip_all_tags( $text );
	if ( 'characters' == _x( 'words', 'word count: words or characters?','noo' ) && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
		$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
		preg_match_all( '/./u', $text, $words_array );
		$sep = '';
	} else {
		$words_array = preg_split( "/[\n\r\t ]+/", $text, null, PREG_SPLIT_NO_EMPTY );
		$sep = ' ';
	}
	if(count($words_array) == 1){
		if($echo){
			echo $text;
			return;
		}else{
			return $text;
		}
	}
	$nth_class=$nth;
	if($nth == 'last')
		$nth = count($words_array) - 1;
	if($nth == 'first')
		$nth = 0;

	if(isset($words_array[$nth]) && !$is_typed){
		$words_array[$nth] = '<span class="nth-word-'.$nth_class.'">'.$words_array[$nth].'</span>';
	}
	if($is_typed){
		$string =  $words_array[$nth];
		$words_array[$nth] = '<span'.(!empty($typed_color) ? ' style="color:'.$typed_color.'" ' :'').'><span class="nth-typed"></span></span>';
		return array(implode($sep, $words_array),$string);
	}
	if($echo)
		echo implode($sep, $words_array);
	else
		return implode($sep, $words_array);
}
endif;

if( !function_exists('noo_social_share') ) :
function noo_social_share( $post_id = null,$echo = true,$prefix = 'noo_blog' ) {
	$post_id = (null === $post_id) ? get_the_id() : $post_id;
	$post_type =  get_post_type($post_id);

	if(noo_get_option("{$prefix}_social", true ) === false) {
		return '';
	}

	$share_url     = urlencode( get_permalink() );
	$share_title   = urlencode( get_the_title() );
	$share_source  = urlencode( get_bloginfo( 'name' ) );
	$share_content = urlencode( get_the_content() );
	$share_media   = wp_get_attachment_thumb_url( get_post_thumbnail_id() );
	$popup_attr    = 'resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0';

	$facebook     = noo_get_option( "{$prefix}_social_facebook", 1 );
	$twitter      = noo_get_option( "{$prefix}_social_twitter", 1 );
	$google		  = noo_get_option( "{$prefix}_social_google", 1 );
	$pinterest    = noo_get_option( "{$prefix}_social_pinterest", 1 );
	$linkedin     = noo_get_option( "{$prefix}_social_linkedin", 0 );

	$html = array();

	if ( $facebook || $twitter || $google || $pinterest || $linkedin ) {
		if($facebook) {
			$html[] = '<a href="#share" class="noo-share"'
					. ' title="' . __( 'Share on Facebook', 'noo' ) . '"'
							. ' onclick="window.open('
									. "'http://www.facebook.com/sharer.php?u={$share_url}&amp;t={$share_title}','popupFacebook','width=650,height=270,{$popup_attr}');"
									. ' return false;">';
			$html[] = '<i class="fa fa-facebook"></i>';
			$html[] = '</a>';
		}

		if($twitter) {
			$html[] = '<a href="#share" class="noo-share"'
					. ' title="' . __( 'Share on Twitter', 'noo' ) . '"'
							. ' onclick="window.open('
									. "'https://twitter.com/intent/tweet?text={$share_title}&amp;url={$share_url}','popupTwitter','width=500,height=370,{$popup_attr}');"
									. ' return false;">';
			$html[] = '<i class="fa fa-twitter"></i></a>';
		}

		if($google) {
			$html[] = '<a href="#share" class="noo-share"'
					. ' title="' . __( 'Share on Google+', 'noo' ) . '"'
							. ' onclick="window.open('
							. "'https://plus.google.com/share?url={$share_url}','popupGooglePlus','width=650,height=226,{$popup_attr}');"
							. ' return false;">';
							$html[] = '<i class="fa fa-google-plus"></i></a>';
		}

		if($pinterest) {
			$html[] = '<a href="#share" class="noo-share"'
					. ' title="' . __( 'Share on Pinterest', 'noo' ) . '"'
							. ' onclick="window.open('
									. "'http://pinterest.com/pin/create/button/?url={$share_url}&amp;media={$share_media}&amp;description={$share_title}','popupPinterest','width=750,height=265,{$popup_attr}');"
									. ' return false;">';
			$html[] = '<i class="fa fa-pinterest"></i></a>';
		}

		if($linkedin) {
			$html[] = '<a href="#share" class="noo-share"'
					. ' title="' . __( 'Share on LinkedIn', 'noo' ) . '"'
							. ' onclick="window.open('
									. "'http://www.linkedin.com/shareArticle?mini=true&amp;url={$share_url}&amp;title={$share_title}&amp;summary={$share_content}&amp;source={$share_source}','popupLinkedIn','width=610,height=480,{$popup_attr}');"
									. ' return false;">';
			$html[] = '<i class="fa fa-linkedin"></i></a>';
		}
	}
	if($echo)
		echo implode("\n", $html);
	else 
		return implode("\n", $html);
}
endif;

if( !function_exists('noo_list_comments') ) :
function noo_list_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	GLOBAL $post;
	$avatar_size = isset($args['avatar_size']) ? $args['avatar_size'] : 60;
	?>
		<li id="li-comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
			<div class="comment-wrap">
				<div class="comment-img">
					<?php echo get_avatar($comment, $avatar_size); ?>
				</div>
				<article id="comment-<?php comment_ID(); ?>" class="comment-block">
					<header class="comment-header">
						<cite class="comment-author">
							<?php echo get_comment_author_link(); ?> 
							<?php if ($comment->user_id === $post->post_author): ?>
							<span class="ispostauthor">
								<?php _e('Author', 'noo'); ?>
							</span>
							<?php endif; ?>
						</cite>
						<div class="comment-meta">
							<span class="time">
								<?php echo sprintf(__('%1$s ago', 'noo') , esc_html(noo_relative_time(get_comment_time()))); ?>
							</span>
							<span class="comment-edit">
								<?php edit_comment_link('' . __('Edit', 'noo')); ?>
							</span>
						</div>
						<?php if ('0' == $comment->comment_approved): ?>
							<p class="comment-pending"><?php _e('Your comment is awaiting moderation.', 'noo'); ?></p>
						<?php endif; ?>
					</header>
					<section class="comment-content">
						<?php comment_text(); ?>
					</section>
					<span class="comment-reply">
						<?php comment_reply_link(array_merge($args, array(
							'reply_text' => (__('<i class="fa fa-reply"></i>Reply', 'noo') . '') ,
							'depth' => $depth,
							'max_depth' => $args['max_depth']
						))); ?>
					</span>
				</article>
			</div>
		<?php
}
endif;

if( !function_exists('noo_content_meta') ) :
function noo_content_meta($is_shortcode=false,$hide_author = false,$hide_date = false,$hide_category = false,$hide_comment = false) {
	$post_type = get_post_type();

	if ( $post_type == 'post' ) {
		if ((!is_single() && noo_get_option( 'noo_blog_show_post_meta' ) === false)
				|| (is_single() && noo_get_option( 'noo_blog_post_show_post_meta' ) === false)) {
					return;
				}
	} elseif ($post_type == 'portfolio_project') {
		if (noo_get_option( 'noo_trainer_show_post_meta' ) === false) {
			return;
		}
	}

	$html = array();
	$html[] = '<p class="content-meta">';
	// $html[] = '<span class="post_type_class">';

	// if(get_post_format() =='video')
	// 	$html[] = '<i class="fa fa-file-video-o"></i>';
	// elseif (get_post_format() == 'audio')
	// 	$html[] = '<i class="fa fa-file-audio-o"></i>';
	// elseif (get_post_format() == 'gallery')
	// 	$html[] = '<i class="fa fa-th-large"></i>';
	// elseif (get_post_format() == 'quote')
	// 	$html[] = '<i class="fa fa-file-quote-left"></i>';
	// elseif (get_post_format() == 'link')
	// 	$html[] = '<i class="fa fa-file-link-o"></i>';
	// elseif (get_post_format() == 'image')
	// 	$html[] = '<i class="fa fa-file-image-o"></i>';
	// else
	// $html[] = '<i class="fa fa-file-image-o"></i>';
	// $html[] = '</span>';
	// Author
	if(!$hide_author):
		$html[] = '<span>';
		$html[] = '<i class="fa fa-user"></i> ';
		ob_start();
		the_author_posts_link();
		$html[] = ob_get_clean();
		$html[] = '</span>';
	endif;
	// Date
	// Date
	if(!$hide_date):
		$html[] = '<span>';
		$html[] = '<time class="entry-date" datetime="' . esc_attr(get_the_date()) . '">';
		$html[] = '<i class="fa fa-calendar"></i> ';
		$html[] = esc_html(get_the_date());
		$html[] = '</time>';
		$html[] = '</span>';
	endif;
	
	// Comments
	$comments_html = '';

	if (comments_open()) {
		$comment_title = '';
		$comment_number = '';
		if (get_comments_number() == 0) {
			$comment_title = sprintf(__('Leave a comment on: &ldquo;%s&rdquo;', 'noo') , get_the_title());
			$comment_number = __('Comment', 'noo');
		} else if (get_comments_number() == 1) {
			$comment_title = sprintf(__('View a comment on: &ldquo;%s&rdquo;', 'noo') , get_the_title());
			$comment_number = ' 1 ' . __('Comment', 'noo');
		} else {
			$comment_title = sprintf(__('View all comments on: &ldquo;%s&rdquo;', 'noo') , get_the_title());
			$comment_number =  ' ' . get_comments_number() . ' ' . __('Comments', 'noo');
		}
			
		$comments_html.= '<span><a' . ' href="' . esc_url(get_comments_link()) . '"' . ' title="' . esc_attr($comment_title) . '"' . ' class="meta-comments">';
		
		$comments_html.= '<i class="fa fa-comments-o"></i> ';
		$comments_html.=  $comment_number . '</a></span>';
	}
	if(!$hide_comment)
		$html[] = $comments_html;

	$html[] = '</p>';
	echo implode($html, "\n");
}
endif;

if( !function_exists('noo_comment_form') ) :
function noo_comment_form( $args = array(), $post_id = null ) {
	global $id;
	$user = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	if ( null === $post_id ) {
		$post_id = $id;
	}
	else {
		$id = $post_id;
	}

	if ( comments_open( $post_id ) ) :
	?>
	<div id="respond-wrap">
		<?php 
			$commenter = wp_get_current_commenter();
			$req = get_option( 'require_name_email' );
			$aria_req = ( $req ? " aria-required='true'" : '' );
			$fields =  array(
				'author' => '<div class="row"><div class="col-sm-4">
				<p class="comment-form-author"><input id="author" name="author" type="text" placeholder="' . __( 'Name:', 'noo' ) . '" class="form-control" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>
				</div>
				',
				'email' => '<div class="col-sm-4"><p class="comment-form-email"><input id="email" name="email" type="text" placeholder="' . __( 'Email:', 'noo' ) . '" class="form-control" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>
				</div>
				',
				'url' => '<div class="col-sm-4"><p class="comment-form-url"><input id="url" name="url" type="text" placeholder="' . __( 'Website:', 'noo' ) . '" class="form-control" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>
				</div></div>',
				'comment_field'		   => '<div class="row"><div class="col-xs-12"><p class="comment-form-comment"><textarea class="form-control"  id="comment" placeholder="' . __( 'Comment:', 'noo' ) . '" name="comment" cols="40" rows="6" aria-required="true"></textarea></p>
				</div></div>'
			);
			$comments_args = array(
					'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
					'logged_in_as'		   => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'noo' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
					'title_reply'          => sprintf('<span>%s</span>',__( 'Leave your thought', 'noo' )),
					'title_reply_to'       => sprintf('<span>%s</span>',__( 'Leave a reply to %s', 'noo' )),
					'cancel_reply_link'    => __( 'Click here to cancel the reply', 'noo' ),
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'label_submit'         => __( 'Submit', 'noo' ),
					'comment_field'		   =>'',
					'must_log_in'		   => ''
			);
			if(is_user_logged_in()){
				$comments_args['comment_field'] = '<p class="comment-form-comment"><textarea class="form-control" id="comment" name="comment" cols="40" rows="6" aria-required="true"></textarea></p>';
			}
		comment_form($comments_args); 
		?>
	</div>

	<?php
	endif;
}
endif;


if( !function_exists('noo_post_nav') ) :
function noo_post_nav() {
	global $post;

	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous )
		return;
	?>
	<?php $prev_link = get_previous_post_link( '%link', _x( '%title', 'Previous post link', 'noo' ) ); ?>
	<?php $next_link = get_next_post_link( '%link', _x( '%title', 'Next post link', 'noo' ) ); ?>
	<nav class="post-navigation<?php echo( (!empty($prev_link) || !empty($next_link) ) ? ' post-navigation-line':'' )?>" role="navigation">		
		<?php if($prev_link):?>
			<div class="prev-post">
				<h4>
					<i class="fa fa-arrow-circle-left">&nbsp;</i>
					<?php echo $prev_link?>
				</h4>
			</div>
		<?php endif;?>
				
		<?php if(!empty($next_link)):?>
			<div class="next-post">
				<h4>
					<?php echo $next_link;?>
					<i class="fa fa-arrow-circle-right">&nbsp;</i>
				</h4>
			</div>
		<?php endif;?>
	</nav>
	<?php
}
endif;

if( !function_exists('noo_excerpt_read_more') ) :
function noo_excerpt_read_more( $more ) {
	return '';
}
add_filter( 'excerpt_more', 'noo_excerpt_read_more' );
endif;

if( !function_exists('noo_content_read_more') ) :
function noo_content_read_more( $more ) {
	return '';
}

add_filter( 'the_content_more_link', 'noo_content_read_more' );
endif;


//// Include specific widgets
// require_once( $widget_path . '/<widgets_name>.php');
if ( !function_exists( 'trainer_listing' ) ) :
function trainer_listing($query='',$title=''){
    global $wp_query;
    if(!empty($query)){
        $wp_query = $query;
    }
	$post_class_col = NOO_Settings()->get_option('noo_trainer_columns', 4);
	$post_style     = NOO_Settings()->get_option('noo_trainer_style', 'grid');
	$post_class     = 'col-sm-6 col-md-'.absint((12 / $post_class_col));
    if(empty($title) && is_tax())
        $title = single_term_title( "", false );
    if($wp_query->have_posts()):
        ?>
        <div class="noo-trainers <?php if($post_style =='grid') echo 'row';?>">
        	<div class="<?php echo $post_style; ?>">
	            <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); global $post; ?>
	                <?php
	                $post_id = get_the_id();

	                // Variables
	                $prefix = '_noo_trainer';

	                ?>
	                <article id="post-<?php the_ID(); ?>" class="noo-trainer hentry <?php if( $post_style == 'grid') echo $post_class;?>">
	                    <div class="trainer-item-wrap">
							<?php if($post_style == 'grid'):?>
			                <div class="content-wrap">
			                    <header class="content-header">
			                        <div class="trainer-info">
			                            <h3 class="content-title">
			                                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
			                            </h3>
			                            <span class="trainer-category">
			                                <?php
			                                $categories = get_the_term_list($post_id,'class_category');
			                                echo $categories;
			                                ?>
			                            </span>
			                        </div>
			                    </header>
			                </div>
			                <?php endif; ?>
			                <div class="content-featured">
			                    <?php the_post_thumbnail('trainers-thumbnail'); ?>
			                    <?php //noo_featured_trainer(null,'trainers-thumbnail','',$lightbox,true); ?>
			                </div>
			                <?php if($post_style == 'list'):
				                $prefix = '_noo_trainer';
								// Trainer's info
								$position		= noo_get_post_meta( get_the_ID(), "{$prefix}_position", '' );
								$experience		= noo_get_post_meta( get_the_ID(), "{$prefix}_experience", '' );
								$email          = noo_get_post_meta( get_the_ID(), "{$prefix}_email", '' );
								$phone          = noo_get_post_meta( get_the_ID(), "{$prefix}_phone", '' );
								$phone_esc		= preg_replace('/\s+/', '', $phone);
								$biography		= noo_get_post_meta( get_the_ID(), "{$prefix}_biography", '' );
			                ?>
			                <div class="content-wrap">
			                    <div class="trainer-info">
			                        <h3 class="content-title">
			                            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
			                        </h3>
			                        <span class="trainer-category">
			                            <?php
			                            $categories = get_the_term_list($post_id,'class_category');
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
			                    </div>
			                </div>
			                <?php endif; ?>

						</div>
	                </article>
	            <?php endwhile; ?>
        	</div>
        </div>
    <?php
    endif;
}
endif;
// Related post function

if( !function_exists('noo_related_class') ) :
function noo_related_class(){
    global $post;

    $class_category = wp_get_post_terms( $post->ID, 'class_category', array("fields" => "ids") );
    
    $posts_in_column = 1;
    $args = array(
        'posts_per_page' => 3,
        'post_type' => 'noo_class',
        'post__not_in' => array($post->ID),
        "meta_key" => "_thumbnail_id",
        'tax_query'         =>  array(
            array(
                'taxonomy'      =>  'class_category',
                'field'         =>  'term_id',
                'terms'         =>  $class_category
            )
        )
    );
    wp_enqueue_script('vendor-imagesloaded');
    wp_enqueue_script('vendor-carouFredSel');
    $noo_post_uid  		= uniqid('noo_class_');
    $class = '';
    $class .= ' '.$noo_post_uid;
    $class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
    $r = new WP_Query($args);
    if($r->have_posts()):
        ?>
        <div class="related-class" >
            <div <?php echo $class?>>
                <h4 class="related-title">
                    <?php noo_nth_word(esc_html__('Related Class','noo'),'first'); ?>
                </h4>
                <div class="row">
                    <div class="noo-class-slider-content">
                        <?php $i=0; ?>
                        <?php while ($r->have_posts()): $r->the_post(); global $post;
                            ?>

                            <?php if($i++ % $posts_in_column == 0 ): ?>
                                <div class="noo-class-slider-item col-sm-12">
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
                        <?php endwhile; ?>
                    </div>
                    <div id="noo-class-slider-pagination" class="slider-indicators"></div>
                </div>


            </div>

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
                        items: 1,
                        duration: 600,
                        pauseOnHover: "resume",
                        fx: "scroll"
                    },
                    auto: {
                        timeoutDuration: 3000,
                        play: false
                    },
                    swipe: {
                        onTouch: true,
                        onMouse: true
                    },
                    items: {
                        visible: {
                            min: 1,
                            max: 1
                        },
                        height:'variable'
                    },
                    pagination: {
                        container: "#noo-class-slider-pagination"
                    }
                };
                jQuery('.<?php echo $noo_post_uid ?> .noo-class-slider-content').carouFredSel(postSliderOptions);
                imagesLoaded('<?php echo $noo_post_uid ?> .noo-class-slider-content',function(){
                    jQuery('.<?php echo $noo_post_uid ?> .noo-class-slider-content').trigger('updateSizes');
                });
                jQuery(window).resize(function(){
                    jQuery('.<?php echo $noo_post_uid ?> .noo-class-slider-content').trigger("destroy").carouFredSel(postSliderOptions);
                });

            });
        </script>
    <?php
    endif;
    wp_reset_query();
}
endif;

if( class_exists('Hc_Insert_Html_Widget') ) :
	// // stop wp removing healcode-widget tags
	// function noo_healcode_tinymce_fix($initArray) {
	// 	$valid_elements = '-strong/b,-em/i,-u,-span,-p,-ol,-ul,-li,-h1,-h2,-h3,-h4,-h5,-h6,' +
	// 					'-p/div,-a[href|name],sub,sup,strike,br,del,table[width],tr,' +
	// 					'td[colspan|rowspan|width],th[colspan|rowspan|width],thead,tfoot,tbody,healcode-widget[*]';

	// 	$initArray['valid_elements'] = $valid_elements;
	// 	$initArray['extended_valid_elements'] = $opts;
	// 	return $initArray;
	// }
	// add_filter('tiny_mce_before_init', 'noo_healcode_tinymce_fix');

	if( !function_exists('noo_enqueue_healcode_scripts') ) :
		function noo_enqueue_healcode_scripts() {
			wp_enqueue_script( 'healcode-widget', 'https://widgets.healcode.com/javascripts/healcode.js', array(), null, false );
			wp_enqueue_style('noo-healcode',NOO_ASSETS_URI."/css/healcode.css",null,null,'all');
		}
		add_action( 'wp_enqueue_scripts', 'noo_enqueue_healcode_scripts' );
	endif;
endif;


// highlight active custom post page in nav
add_filter( 'nav_menu_css_class', 'namespace_menu_classes', 10, 3 );
function namespace_menu_classes( $classes , $item ){

	if ( class_exists('Noo__Timetable__Class') ) :

		$post_type = get_post_type();
		if ( $post_type )
		{
		    $post_type_data = get_post_type_object( $post_type );
		    $post_type_slug = $post_type_data->rewrite['slug'];
		    if ( !empty($post_type_slug) ){
		    	$cur_url = str_replace( get_home_url(), '', $item->url);
	    		if ( strpos($cur_url, $post_type_slug) !== false ) {
					if ( in_array('menu-item-has-children', $classes) ) {
				    	$classes[] = 'current-menu-parent';
				    }
	    		}
		    }
		}

	endif;
	
	return $classes;
}