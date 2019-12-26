<?php
/**
 * Utilities Functions for NOO Framework.
 * This file contains various functions for getting and preparing data.
 *
 * @package    NOO Framework
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */

if (!function_exists('smk_get_all_sidebars')):
	function smk_get_all_sidebars() {
		global $wp_registered_sidebars;
		$sidebars = array();
		$none_sidebars = array();
		for ($i = 1;$i <= 4;$i++) {
			$none_sidebars[] = "noo-top-{$i}";
			$none_sidebars[] = "noo-footer-{$i}";
		}
		if ($wp_registered_sidebars && !is_wp_error($wp_registered_sidebars)) {
			
			foreach ($wp_registered_sidebars as $sidebar) {
				// Don't include Top Bar & Footer Widget Area
				if (in_array($sidebar['id'], $none_sidebars)) continue;
				
				$sidebars[$sidebar['id']] = $sidebar['name'];
			}
		}
		return $sidebars;
	}
endif;

if (!function_exists('get_sidebar_name')):
	function get_sidebar_name($id = '') {
		if (empty($id)) return '';
		
		global $wp_registered_sidebars;
		if ($wp_registered_sidebars && !is_wp_error($wp_registered_sidebars)) {
			foreach ($wp_registered_sidebars as $sidebar) {
				if ($sidebar['id'] == $id) return $sidebar['name'];
			}
		}
		
		return '';
	}
endif;

if (!function_exists('get_sidebar_id')):
	function get_sidebar_id() {
		// Normal Page or Static Front Page
		if ( is_page() || (is_front_page() && get_option('show_on_front') == 'page') ) {
			// Get the sidebar setting from
			$sidebar = noo_get_post_meta(get_the_ID(), '_noo_wp_page_sidebar', '');
			
			return $sidebar;
		}

		// NOO Class
		if( is_post_type_archive( 'noo_class' )
			|| is_tax( 'class_category' ) ) {

			$class_layout = noo_get_option('noo_classes_layout', 'sidebar');
			if ($class_layout != 'fullwidth') {
				return noo_get_option('noo_classes_sidebar', '');
			}

			return '';
		}
		
		// Single Class
		if (is_singular('noo_class')) {
			$post_layout = noo_get_option('noo_class_layout', 'same_as_classes');
			$sidebar = '';
			if ($post_layout == 'same_as_classes') {
				$post_layout = noo_get_option('noo_classes_layout', 'sidebar');
				$sidebar = noo_get_option('noo_classes_sidebar', '');
			} else {
				$sidebar = noo_get_option('noo_class_sidebar', '');
			}
			
			if($post_layout == 'fullwidth'){
				return '';
			}
			
			return $sidebar;
		}

		// NOO Trainer
		if( is_post_type_archive( 'noo_trainer' )
			|| is_tax( 'trainer_category' ) ) {
			
			$trainer_layout = noo_get_option('noo_trainer_layout', 'sidebar');
			if ($trainer_layout != 'fullwidth') {
				return noo_get_option('noo_trainer_sidebar', '');
			}

			return '';
		}

		// NOO Event
		if( is_post_type_archive( 'noo_event' )
			|| is_tax( 'event_category' )
			|| is_tax( 'event_location' ) ) {

			$event_layout = noo_get_option('noo_events_layout', 'sidebar');
			if ($event_layout != 'fullwidth') {
				return noo_get_option('noo_events_sidebar', '');
			}

			return '';
		}
		
		// Single event
		if (is_singular('noo_event')) {
			$post_layout = noo_get_option('noo_event_layout', 'same_as_events');
			$sidebar = '';
			if ($post_layout == 'same_as_events') {
				$post_layout = noo_get_option('noo_events_layout', 'sidebar');
				$sidebar = noo_get_option('noo_events_sidebar', '');
			} else {
				$sidebar = noo_get_option('noo_event_sidebar', '');
			}
			
			if($post_layout == 'fullwidth'){
				return '';
			}
			
			return $sidebar;
		}

		// NOO Portfolio
		if( is_post_type_archive( 'portfolio_project' )
			|| is_tax( 'portfolio_category' )
			|| is_tax( 'portfolio_tag' )
			|| is_singular( 'portfolio_project' ) ) {

			$trainer_layout = noo_get_option('noo_trainer_layout', 'fullwidth');
			if ($trainer_layout != 'fullwidth') {
				return noo_get_option('noo_trainer_sidebar', '');
			}

			return '';
		}

		// WooCommerce Product
		if( NOO_WOOCOMMERCE_EXIST ) {
			if( is_product() ) {
				$product_layout = noo_get_option('noo_woocommerce_product_layout', 'same_as_shop');
				$sidebar = '';
				if ( $product_layout == 'same_as_shop' ) {
					$product_layout = noo_get_option('noo_shop_layout', 'fullwidth');
					$sidebar = noo_get_option('noo_shop_sidebar', '');
				} else {
					$sidebar = noo_get_option('noo_woocommerce_product_sidebar', '');
				}
				
				if ( $product_layout == 'fullwidth' ) {
					return '';
				}
				
				return $sidebar;
			}

			// Shop, Product Category, Product Tag, Cart, Checkout page
			if( is_shop() || is_product_category() || is_product_tag() ) {
				$shop_layout = noo_get_option('noo_shop_layout', 'fullwidth');
				if($shop_layout != 'fullwidth'){
					return noo_get_option('noo_shop_sidebar', '');
				}

				return '';
			}
		}
		
		// Single post page
		if (is_single()) {
			// Check if there's overrode setting in this post.
			$post_id = get_the_ID();
			$override_setting = noo_get_post_meta($post_id, '_noo_wp_post_override_layout', false);
			if ($override_setting) {
				// overrode
				$overrode_layout = noo_get_post_meta($post_id, '_noo_wp_post_layout', 'fullwidth');
				if ($overrode_layout != 'fullwidth') {
					return noo_get_post_meta($post_id, '_noo_wp_post_sidebar', 'sidebar-main');
				}
			} else{

				$post_layout = noo_get_option('noo_blog_post_layout', 'same_as_blog');
				$sidebar = '';
				if ($post_layout == 'same_as_blog') {
					$post_layout = noo_get_option('noo_blog_layout', 'sidebar');
					$sidebar = noo_get_option('noo_blog_sidebar', 'sidebar-main');
				} else {
					$sidebar = noo_get_option('noo_blog_post_sidebar', 'sidebar-main');
				}
				
				if($post_layout == 'fullwidth'){
					return '';
				}
				
				return $sidebar;
			}

			return '';
		}

		// Archive page
		if( is_archive() ) {
			$archive_layout = noo_get_option('noo_blog_archive_layout', 'same_as_blog');
			$sidebar = '';
			if ($archive_layout == 'same_as_blog') {
				$archive_layout = noo_get_option('noo_blog_layout', 'sidebar');
				$sidebar = noo_get_option('noo_blog_sidebar', 'sidebar-main');
			} else {
				$sidebar = noo_get_option('noo_blog_archive_sidebar', 'sidebar-main');
			}
			
			if($archive_layout == 'fullwidth'){
				return '';
			}
			
			return $sidebar;
		}

		// Archive, Index or Home
		if (is_home() || is_archive() || (is_front_page() && get_option('show_on_front') == 'posts')) {
			
			$blog_layout = noo_get_option('noo_blog_layout', 'sidebar');
			if ($blog_layout != 'fullwidth') {
				return noo_get_option('noo_blog_sidebar', 'sidebar-main');
			}
			
			return '';
		}
		
		return '';
	}
endif;

if ( !function_exists('noo_default_primary_color') ) :
	function noo_default_primary_color() {
		return '#fe6367';
	}
endif;
if ( !function_exists('noo_default_font_family') ) :
	function noo_default_font_family() {
		return 'PT Sans';
	}
endif;
if ( !function_exists('noo_default_text_color') ) :
	function noo_default_text_color() {
		return '#5f5f5f';
	}
endif;
if ( !function_exists('noo_default_headings_font_family') ) {
	function noo_default_headings_font_family() {
		return 'PT Sans';
	}
}
if ( !function_exists('noo_default_headings_color') ) {
	function noo_default_headings_color() {
		return '#454545';
	}
}
if ( !function_exists('noo_default_header_bg') ) {
	function noo_default_header_bg() {
		return '#FFFFFF';
	}
}
if ( !function_exists('noo_default_nav_font_family') ) {
	function noo_default_nav_font_family() {
		return noo_default_headings_font_family();
	}
}
if ( !function_exists('noo_default_logo_font_family') ) {
	function noo_default_logo_font_family() {
		return noo_default_headings_font_family();
	}
}
if ( !function_exists('noo_default_logo_color') ) {
	function noo_default_logo_color() {
		return '#fff';
	}
}
if ( !function_exists('noo_default_font_size') ) {
	function noo_default_font_size() {
		return '14';
	}
}
if ( !function_exists('noo_default_font_weight') ) {
	function noo_default_font_weight() {
		return '400';
	}
}

//
// This function help to create the dynamic thumbnail width,
// but we don't use it at the moment.
// 
if (!function_exists('noo_thumbnail_width')) :
	function noo_thumbnail_width() {
		$site_layout	= noo_get_option('noo_site_layout', 'fullwidth');
		$page_layout	= get_page_layout();
		$width			= 1200; // max width

		if($site_layout == 'boxed') {
			$site_width = (int) noo_get_option('noo_layout_site_width', '90');
			$site_max_width = (int) noo_get_option('noo_layout_site_max_width', '1200');
			$width = min($width * $site_width / 100, $site_max_width);
		}

		if($page_layout != 'fullwidth') {
			$width = $width * 75 / 100; // 75% of col-9
		}

		return $width;
	}
endif;

if (!function_exists('get_thumbnail_width')) :
	function get_thumbnail_width() {

		// if( is_admin()) {
		// 	return 'thumbnail';
		// }

		// NOO Portfolio
		if( is_post_type_archive( 'portfolio_project' ) ) {
			// if it's portfolio page, check if the masonry size is fixed or original
			if(noo_get_option('noo_trainer_masonry_item_size', 'original' ) == 'fixed') {
				$masonry_size = noo_get_post_meta($post_id, '_noo_trainer_image_masonry_size', 'regular');
				return "masonry-fixed-{$masonry_size}";
			}
		}

		$site_layout	= noo_get_option('noo_site_layout', 'fullwidth');
		$page_layout	= get_page_layout();

		if($site_layout == 'boxed') {
			if($page_layout == 'fullwidth') {
				return 'boxed-fullwidth';
			} else {
				return 'boxed-sidebar';
			}
		} else {
			if($page_layout == 'fullwidth') {
				return 'fullwidth-fullwidth';
			} else {
				return 'fullwidth-sidebar';
			}
		}

		return 'fullwidth-fullwidth';
	}
endif;

if (!function_exists('get_page_layout')):
	function get_page_layout() {
		
		// Normal Page or Static Front Page
		if (is_page() || (is_front_page() && get_option('show_on_front') == 'page')) {
			// WP page,
			// get the page template setting
			$page_id = get_the_ID();
			$page_template = noo_get_post_meta($page_id, '_wp_page_template', 'default');
			
			if (strpos($page_template, 'sidebar') !== false) {
				if (strpos($page_template, 'left') !== false) {
					return 'left_sidebar';
				}
				
				return 'sidebar';
			}
			
			return 'fullwidth';
		}

		// NOO trainer
		if( is_post_type_archive( 'noo_trainer' )
			|| is_tax( 'trainer_category' ) ) {
			return noo_get_option('noo_trainer_layout', 'sidebar');
		}

		// NOO Class
		if( is_post_type_archive( 'noo_class' )
			|| is_tax( 'class_category' ) ) {

			return noo_get_option('noo_classes_layout', 'sidebar');
		}
		
		// Single Class
		if( is_singular( 'noo_class' ) ) {
			$post_layout = noo_get_option('noo_class_layout', 'sidebar');
			// if ($post_layout == 'same_as_classes') {
			// 	$post_layout = noo_get_option('noo_classes_layout', 'sidebar');
			// }
			
			return $post_layout;
		}
		
		// NOO Event
		if( is_post_type_archive( 'noo_event' )
			|| is_tax( 'event_category' )
			|| is_tax( 'event_location' ) ) {

			return noo_get_option('noo_events_layout', 'sidebar');
		}
		
		// Single Event
		if( is_singular( 'noo_event' ) ) {
			$post_layout = noo_get_option('noo_event_layout', 'same_as_events');
			if ($post_layout == 'same_as_events') {
				$post_layout = noo_get_option('noo_events_layout', 'sidebar');
			}
			
			return $post_layout;
		}
	

		// WooCommerce
		if( NOO_WOOCOMMERCE_EXIST ) {
			if( is_shop() || is_product_category() || is_product_tag() ){
				return noo_get_option('noo_shop_layout', 'fullwidth');
			}

			if( is_product() ) {
				$product_layout = noo_get_option('noo_woocommerce_product_layout', 'same_as_shop');
				if ($product_layout == 'same_as_shop') {
					$product_layout = noo_get_option('noo_shop_layout', 'fullwidth');
				}
				return $product_layout;
			}
		}
		
		// Single post page
		if (is_single()) {

			// WP post,
			// check if there's overrode setting in this post.
			$post_id = get_the_ID();
			$override_setting = noo_get_post_meta($post_id, '_noo_wp_post_override_layout', false);
			
			if ( !$override_setting ) {
				$post_layout = noo_get_option('noo_blog_post_layout', 'same_as_blog');
				if ($post_layout == 'same_as_blog') {
					$post_layout = noo_get_option('noo_blog_layout', 'sidebar');
				}
				return $post_layout;
			}

			// overrode
			return noo_get_post_meta($post_id, '_noo_wp_post_layout', 'sidebar-main');
		}

		// Archive
		if (is_archive()) {
			$archive_layout = noo_get_option('noo_blog_archive_layout', 'same_as_blog');
			if ($archive_layout == 'same_as_blog') {
				$archive_layout = noo_get_option('noo_blog_layout', 'sidebar');
			}
			
			return $archive_layout;
		}

		// Index or Home
		if (is_home() || (is_front_page() && get_option('show_on_front') == 'posts')) {
			
			return noo_get_option('noo_blog_layout', 'sidebar');
		}
		
		return '';
	}
endif;

if(!function_exists('is_fullwidth')){
	function is_fullwidth(){
		return get_page_layout() == 'fullwidth';
	}
}

if (!function_exists('is_one_page_enabled')):
	function is_one_page_enabled() {
		if( (is_front_page() && get_option('show_on_front' == 'page')) || is_page()) {
			$page_id = get_the_ID();
			return ( noo_get_post_meta( $page_id, '_noo_wp_page_enable_one_page', false ) );
		}

		return false;
	}
endif;

if (!function_exists('get_one_page_menu')):
	function get_one_page_menu() {
		if( is_one_page_enabled() ) {
			if( (is_front_page() && get_option('show_on_front' == 'page')) || is_page()) {
				$page_id = get_the_ID();
				return noo_get_post_meta( $page_id, '_noo_wp_page_one_page_menu', '' );
			}
		}

		return '';
	}
endif;

if (!function_exists('has_home_slider')):
	function has_home_slider() {
		if (class_exists( 'RevSlider' )) {
			if( (is_front_page() && get_option('show_on_front' == 'page')) || is_page()) {
				$page_id = get_the_ID();
				return ( noo_get_post_meta( $page_id, '_noo_wp_page_enable_home_slider', false ) )
					&& ( noo_get_post_meta( $page_id, '_noo_wp_page_slider_rev', '' ) != '' );
			}
		}

		return false;
	}
endif;

if (!function_exists('home_slider_position')):
	function home_slider_position() {
		if (has_home_slider()) {
			return noo_get_post_meta( get_the_ID(), '_noo_wp_page_slider_position', 'below' );
		}

		return '';
	}
endif;

if (!function_exists('is_masonry_style')):
	function is_masonry_style() {
		if( is_post_type_archive( 'portfolio_project' ) || is_tax('portfolio_category') || is_tax('portfolio_tag')  ) {
			return true;
		}

		if(is_home()) {
			return (noo_get_option( 'noo_blog_style' ) == 'masonry');
		}
		
		if(is_archive()) {
			$archive_style = noo_get_option( 'noo_blog_archive_style', 'same_as_blog' );
			if ($archive_style == 'same_as_blog') {
				return (noo_get_option( 'noo_blog_style', 'standard' ) == 'masonry');
			} else {
				return ($archive_style == 'masonry');
			}
		}

		return false;
	}
endif;

if (!function_exists('get_page_heading')):
	function get_page_heading() {
		$heading = '';
		$archive_title = '';
		$archive_desc = '';
		if( ! noo_get_option( 'noo_page_heading', true ) ) {
			return array($heading, $archive_title, $archive_desc);
		}
		if ( is_home() ) {
			$heading = noo_get_option( 'noo_blog_heading_title', __( 'Blog', 'noo' ) );
		} elseif ( NOO_WOOCOMMERCE_EXIST && is_shop() ) {
			if( is_search() ) {
				$heading =__( 'Search Results:', 'noo' ) . ' ' . esc_attr( get_search_query() );
			} else {
				$heading = noo_get_option( 'noo_shop_heading_title', __( 'Shop', 'noo' ) );
			}
		} elseif ( is_search() ) {
			$heading = __( 'Search Results', 'noo' );
			global $wp_query;
			if(!empty($wp_query->found_posts)) {
				if($wp_query->found_posts > 1) {
					$heading =  $wp_query->found_posts ." ". __('Search Results for:','noo')." ".esc_attr( get_search_query() );
				} else {
					$heading =  $wp_query->found_posts ." ". __('Search Result for:','noo')." ".esc_attr( get_search_query() );
				}
			} else {
				if(!empty($_GET['s'])) {
					$heading = __('Search Results for:','noo')." ".esc_attr( get_search_query() );
				} else {
					$heading = __('To search the site please enter a valid term','noo');
				}
			}
		} elseif ( is_author() ) {
			$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
			$heading = __('Author Archive','noo');

			if(isset($curauth->nickname)) $heading .= ' ' . __('for:','noo')." ".$curauth->nickname;
		
		} elseif ( is_year() ) {
    		$heading = __( 'Post Archive by Year: ', 'noo' ) . get_the_date( 'Y' );
		} elseif ( is_month() ) {
    		$heading = __( 'Post Archive by Month: ', 'noo' ) . get_the_date( 'F,Y' );
		} elseif ( is_day() ) {
    		$heading = __( 'Post Archive by Day: ', 'noo' ) . get_the_date( 'F j, Y' );
		} elseif ( is_404() ) {
    		$heading = __( 'Oops! We could not find anything to show to you.', 'noo' );
    		$archive_title =  __( 'Would you like going else where to find your stuff.', 'noo' );
		} elseif ( is_category() ) {
			$heading        = single_cat_title( '', false );
			// $archive_desc   = term_description();
		}elseif (is_tag()){
			$heading        = single_tag_title( '', false );
		}
		elseif(is_tax())
		{
			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$heading = $term->name;
		
		} elseif ( is_singular( 'product' ) ) {
			$heading = noo_get_option( 'noo_woocommerce_product_disable_heading', true ) ? '' : get_the_title();
		}  elseif ( is_single() ) {
			if(is_singular('post'))
				$heading = __('Blog Detail','noo');
			elseif(is_singular('noo_event'))
				$heading = __('Event Detail','noo');
			elseif(is_singular('noo_class'))
				$heading = __('Class Detail','noo');
			elseif(is_singular('noo_trainer'))
				$heading = __('Trainer Profile','noo');
			else
				$heading = get_the_title();
		} elseif( is_page() ) {
			//if( ! noo_get_post_meta(get_the_ID(), '_noo_wp_page_hide_page_title', false) ) {
				$heading = get_the_title();
			//}
		} elseif( is_post_type_archive('noo_event') ) {
			$heading = noo_get_option( 'noo_events_heading_title', __( 'Events List', 'noo' ) );
		} elseif( is_post_type_archive('noo_class') ) {
			$heading = noo_get_option( 'noo_class_heading_title', __( 'Class List', 'noo' ) );
		} elseif( is_post_type_archive('noo_trainer') ) {
			$heading = noo_get_option( 'noo_trainer_heading_title', __( 'Trainer List', 'noo' ) );
		}
		return array($heading, $archive_title, $archive_desc);
	}
endif;

if (!function_exists('get_page_heading_image')):
	function get_page_heading_image() {
		$image = '';
		if( ! noo_get_option( 'noo_page_heading', true ) ) {
			return $image;
		}
		if( NOO_WOOCOMMERCE_EXIST && is_shop() ) {
			$image = noo_get_image_option( 'noo_shop_heading_image', '' );
		} elseif ( is_home() ) {
			$image = noo_get_image_option( 'noo_blog_heading_image', '' );
		} elseif( is_category() || is_tag() ) {
			$queried_object = get_queried_object();
			$image			= noo_get_term_meta( $queried_object->term_id, 'heading_image', '' );
			$image			= empty( $image ) ? noo_get_image_option( 'noo_blog_heading_image', '' ) : $image;
		} elseif( is_tax( 'portfolio_category' ) ) {
			$queried_object = get_queried_object();
			$image			= noo_get_term_meta( $queried_object->term_id, 'heading_image', '' );
			$image			= empty( $image ) ? noo_get_image_option( 'noo_trainer_heading_image', '' ) : $image;
		} elseif( NOO_WOOCOMMERCE_EXIST && ( is_product_category() || is_product_tag() ) ) {
			$queried_object = get_queried_object();
			$image			= noo_get_term_meta( $queried_object->term_id, 'heading_image', '' );
			$image			= empty( $image ) ? noo_get_image_option( 'noo_shop_heading_image', '' ) : $image;
		} elseif ( is_singular('product' ) || is_page() ) {
			$image = noo_get_post_meta(get_the_ID(), '_heading_image', '');
		} elseif (is_single()) {
			$image = noo_get_image_option( 'noo_blog_heading_image', '' );
		} elseif( is_post_type_archive('noo_event') ) {
			$image = noo_get_image_option( 'noo_events_heading_image', '' );
		} elseif( is_post_type_archive('noo_class') ) {
			$image = noo_get_image_option( 'noo_class_heading_image', '' );
		} elseif( is_post_type_archive('noo_trainer') ) {
			$image = noo_get_image_option( 'noo_trainer_heading_image', '' );
		} elseif( is_tax('class_category') ) {
			$image = noo_get_image_option( 'noo_class_heading_image', '' );
		}

		if( !empty( $image ) && is_numeric( $image ) ) $image = wp_get_attachment_url( $image );

		return $image;
	}
endif;

if (!function_exists('noo_get_post_format')):
	function noo_get_post_format($post_id = null, $post_type = '') {
		$post_id = (null === $post_id) ? get_the_ID() : $post_id;
		$post_type = ('' === $post_type) ? get_post_type($post_id) : $post_type;

		$post_format = '';
		
		if ($post_type == 'post') {
			$post_format = get_post_format($post_id);
		}
		
		if ($post_type == 'portfolio_project') {
			$post_format = noo_get_post_meta($post_id, '_noo_trainer_media_type', 'image');
		}

		return $post_format;
	}
endif;

if (!function_exists('has_featured_content')):
	function has_featured_content($post_id = null) {
		$post_id = (null === $post_id) ? get_the_ID() : $post_id;

		$post_type = get_post_type($post_id);
		$prefix = '';
		$post_format = '';
		
		if ($post_type == 'post') {
			$prefix = '_noo_wp_post';
			$post_format = get_post_format($post_id);
		}
		
		if ($post_type == 'portfolio_project') {
			$prefix = '_noo_trainer';
			$post_format = noo_get_post_meta($post_id, "{$prefix}_media_type", 'image');
		}
		
		switch ($post_format) {
			case 'image':
				$main_image = noo_get_post_meta($post_id, "{$prefix}_main_image", 'featured');
				if( $main_image == 'featured') {
					return has_post_thumbnail($post_id);
				}

				return has_post_thumbnail($post_id) || ( (bool)noo_get_post_meta($post_id, "{$prefix}_image", '') );
			case 'gallery':
				if (!is_singular()) {
					$preview_content = noo_get_post_meta($post_id, "{$prefix}_gallery_preview", 'slideshow');
					if ($preview_content == 'featured') {
						return has_post_thumbnail($post_id);
					}
				}
				
				return (bool)noo_get_post_meta($post_id, "{$prefix}_gallery", '');
			case 'video':
				if (!is_singular()) {
					$preview_content = noo_get_post_meta($post_id, "{$prefix}_preview_video", 'both');
					if ($preview_content == 'featured') {
						return has_post_thumbnail($post_id);
					}
				}
				
				$m4v_video = (bool)noo_get_post_meta($post_id, "{$prefix}_video_m4v", '');
				$ogv_video = (bool)noo_get_post_meta($post_id, "{$prefix}_video_ogv", '');
				$embed_video = (bool)noo_get_post_meta($post_id, "{$prefix}_video_embed", '');
				
				return $m4v_video || $ogv_video || $embed_video;
			case 'link':
			case 'quote':
				return false;
				
			case 'audio':
				$mp3_audio = (bool)noo_get_post_meta($post_id, "{$prefix}_audio_mp3", '');
				$oga_audio = (bool)noo_get_post_meta($post_id, "{$prefix}_audio_oga", '');
				$embed_audio = (bool)noo_get_post_meta($post_id, "{$prefix}_audio_embed", '');
				return $mp3_audio || $oga_audio || $embed_audio;
			default: // standard post format
				return has_post_thumbnail($post_id);
		}
		
		return false;
	}
endif;

if (!function_exists('noo_get_page_link_by_template')):
	function noo_get_page_link_by_template( $page_template ) {
		$pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => $page_template
		));

		if( $pages ){
			$link = get_permalink( $pages[0]->ID );
		}else{
			$link = home_url();
		}
		return $link;
	}
endif;

if (!function_exists('noo_current_url')):
	function noo_current_url($encoded = false) {
		global $wp;
		$current_url = esc_url( add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
		if( $encoded ) {
			return urlencode($current_url);
		}
		return $current_url;
	}
endif;

if (!function_exists('noo_upload_dir_name')):
	function noo_upload_dir_name() {
		return 'noo_yogi';
	}
endif;

if (!function_exists('noo_upload_dir')):
	function noo_upload_dir() {
		$upload_dir = wp_upload_dir();

		return $upload_dir['basedir'] . '/' . noo_upload_dir_name();
	}
endif;

if (!function_exists('noo_upload_url')):
	function noo_upload_url() {
		$upload_dir = wp_upload_dir();

		return $upload_dir['baseurl'] . '/' . noo_upload_dir_name();
	}
endif;

if (!function_exists('noo_create_upload_dir')):
	function noo_create_upload_dir( $wp_filesystem = null ) {
		if( empty( $wp_filesystem ) ) {
			return false;
		}

		$upload_dir = wp_upload_dir();
		global $wp_filesystem;

		$noo_upload_dir = $wp_filesystem->find_folder( $upload_dir['basedir'] ) . noo_upload_dir_name();
		if ( ! $wp_filesystem->is_dir( $noo_upload_dir ) ) {
			if ( $wp_filesystem->mkdir( $noo_upload_dir, 0777 ) ) {
				return $noo_upload_dir;
			}

			return false;
		}

		return $noo_upload_dir;
	}
endif;

/**
 * This function is original from Visual Composer. Redeclare it here so that it could be used for site without VC.
 */
if ( !function_exists('noo_handler_shortcode_content') ):
	function noo_handler_shortcode_content( $content, $autop = false ) {
		$wpb_js_function = 'wpb_js_remove_wp' . 'autop';
		if( function_exists($wpb_js_function) ) {
			$content = $wpb_js_function( $content, $autop );
		}
		return do_shortcode( shortcode_unautop( $content) );
	}
endif;

// Allow only unharmed HTML tag.
if( !function_exists('noo_html_content_filter') ) :
	function noo_html_content_filter( $content = '' ) {
		$allowed_html = array(
			'a' => array(
				'href' => array(),
				'target' => array(),
				'title' => array(),
				'rel' => array(),
				'class' => array(),
				'style' => array(),
			),
			'img' => array(
				'src' => array(),
				'class' => array(),
				'style' => array(),
			),
			'h1' => array(),
			'h2' => array(),
			'h3' => array(),
			'h4' => array(),
			'h5' => array(),
			'p' => array(
				'class' => array(),
				'style' => array()
			),
			'br' => array(
				'class' => array(),
				'style' => array()
			),
			'hr' => array(
				'class' => array(),
				'style' => array()
			),
			'span' => array(
				'class' => array(),
				'style' => array()
			),
			'em' => array(
				'class' => array(),
				'style' => array()
			),
			'strong' => array(
				'class' => array(),
				'style' => array()
			),
			'small' => array(
				'class' => array(),
				'style' => array()
			),
			'b' => array(
				'class' => array(),
				'style' => array()
			),
			'i' => array(
				'class' => array(),
				'style' => array()
			),
			'u' => array(
				'class' => array(),
				'style' => array()
			),
			'ul' => array(
				'class' => array(),
				'style' => array()
			),
			'ol' => array(
				'class' => array(),
				'style' => array()
			),
			'li' => array(
				'class' => array(),
				'style' => array()
			),
			'blockquote' => array(
				'class' => array(),
				'style' => array()
			),
		);

		$allowed_html = apply_filters( 'noo_allowed_html', $allowed_html );

		return wp_kses( $content, $allowed_html );
	}
endif;

if ( ! function_exists( 'noo_get_the_excerpt' ) ) {
	function noo_get_the_excerpt($post_id = '') {
		if( empty( $post_id ) ) return get_the_excerpt();

		global $post;  
		$save_post = $post;
		$post = get_post($post_id);
		setup_postdata( $post );
		$output = get_the_excerpt();
		$post = $save_post;
		wp_reset_postdata();
		return $output;
	}
}

// Backwards compatibility for wp_title
if ( ! function_exists( '_wp_render_title_tag' ) ) {
	function noo_render_title() {
?>
<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php
	}
	add_action( 'wp_head', 'noo_render_title' );
}