<?php
/**
 * HTML Functions for NOO Framework.
 * This file contains various functions used for rendering site's small layouts.
 *
 * @package    NOO Framework
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */

// Shortcodes
require_once NOO_FRAMEWORK_FUNCTION . '/noo-html-shortcodes.php';

// Featured Content
require_once NOO_FRAMEWORK_FUNCTION . '/noo-html-featured.php';

// Pagination
require_once NOO_FRAMEWORK_FUNCTION . '/noo-html-pagination.php';

require_once NOO_FRAMEWORK_FUNCTION . '/noo-html-breadcrumbs.php';


if (!function_exists('noo_get_readmore_link')):
	function noo_get_readmore_link() {
		return '<a href="' . get_permalink() . '" class="read-more">'
		. '<i class="fa fa-chevron-circle-right"></i>'
		. __('Read More', 'noo' ) 
		. '</a>';
	}
endif;

if (!function_exists('noo_readmore_link')):
	function noo_readmore_link() {
		if( noo_get_option('noo_blog_show_readmore', 1 ) ) {
			echo noo_get_readmore_link();
		} else {
			echo '';
		}
	}
endif;

if (!function_exists('noo_list_comments')):
	function noo_list_comments($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		GLOBAL $post;
		$avatar_size = isset($args['avatar_size']) ? $args['avatar_size'] : 60;
?>
		<li id="li-comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
			<div class="comment-wrap">
				<div class="comment-img">
					<div class="img-thumbnail">
						<?php echo get_avatar($comment, $avatar_size); ?>
					</div>
					<?php if ($comment->user_id === $post->post_author): ?>
					<div class="ispostauthor">
						<?php _e('Post<br/>Author', 'noo'); ?>
					</div>
					<?php
		endif; ?>
				</div>
				<article id="comment-<?php comment_ID(); ?>" class="comment-block">
					<header class="comment-header">
						<cite class="comment-author"><?php echo get_comment_author_link(); ?></cite>
						
						<div class="comment-meta">
							<time datetime="<?php echo get_comment_time('c'); ?>">
								<?php echo sprintf(__('%1$s at %2$s', 'noo') , get_comment_date() , get_comment_time()); ?>
							</time>
							<span class="comment-edit">
								<?php edit_comment_link('<i class="fa fa-edit"></i> ' . __('Edit', 'noo')); ?>
							</span>
						</div>
						<?php if ('0' == $comment->comment_approved): ?>
							<p class="comment-pending"><?php _e('Your comment is awaiting moderation.', 'noo'); ?></p>
						<?php
		endif; ?>
					</header>
					<section class="comment-content">
						<?php comment_text(); ?>
					</section>
					<span class="pull-left">
							<?php comment_reply_link(array_merge($args, array(
			'reply_text' => (__('Reply', 'noo') . ' <span class="comment-reply-link-after"><i class="fa fa-reply"></i></span>') ,
			'depth' => $depth,
			'max_depth' => $args['max_depth']
		))); ?>
						</span>
				</article>
			</div>
		<?php
	}
endif;

if ( ! function_exists( 'noo_trainer_attributes' ) ) :
	function noo_trainer_attributes( $post_id = null ) {
		if ( noo_get_option( 'noo_trainer_enable_attribute', true ) === false) {
			return '';
		}

		$post_id = (null === $post_id) ? get_the_id() : $post_id;
		$attributes = get_the_terms( $post_id, 'portfolio_tag' );

		$html = array();
		$html[] = '<ul class="list-unstyled attribute-list">';
		$i=0;
		foreach( $attributes as $attribute ) {
			$html[] = '<li class="'.($i % 2 == 0 ? 'odd':'even').'">';
			$html[] = '<a href="' . get_term_link( $attribute->slug, 'portfolio_tag' ) . '">';
			$html[] = '<i class="fa fa-check"></i>';
			$html[] = $attribute->name;
			$html[] = '</a>';
			$html[] = '</li>';
			$i++;
		};
		$html[] = '</ul>';

		echo implode($html, "\n");
	}
endif;


if (!function_exists('noo_social_icons')):
	function noo_social_icons($position = 'topbar', $direction = '') {
		if ($position == 'topbar') {
			// Top Bar social
		} else {
			// Bottom Bar social
		}
		
		$class = isset($direction) ? $direction : '';
		$html = array();
		$html[] = '<div class="noo-social social-icons ' . $class . '">';
		
		$social_list = array(
			'facebook' => __('Facebook', 'noo') ,
			'twitter' => __('Twitter', 'noo') ,
			'google-plus' => __('Google+', 'noo') ,
			'pinterest' => __('Pinterest', 'noo') ,
			'linkedin' => __('LinkedIn', 'noo') ,
			'rss' => __('RSS', 'noo') ,
			'youtube' => __('YouTube', 'noo') ,
			'instagram' => __('Instagram', 'noo') ,
		);
		
		$social_html = array();
		foreach ($social_list as $key => $title) {
			$social = noo_get_option("noo_social_{$key}", '');
			if ($social) {
				$social_html[] = '<a href="' . $social . '" title="' . $title . '" target="_blank">';
				$social_html[] = '<i class="fa fa-' . $key . '"></i>';
				$social_html[] = '</a>';
			}
		}
		
		if(empty($social_html)) {
			$social_html[] = __('No Social Media Link','noo');
		}
		
		$html[] = implode($social_html, "\n");
		$html[] = '</div>';
		
		echo implode($html, "\n");
	}
endif;

if(!function_exists('noo_gototop')):
	function noo_gototop(){
		if( noo_get_option( 'noo_back_to_top', true ) ) {
			echo '<a href="#" class="go-to-top hidden-print"><i class="fa fa-angle-up"></i></a>';
		}
		return ;
	}
	add_action('wp_footer','noo_gototop');
endif;

