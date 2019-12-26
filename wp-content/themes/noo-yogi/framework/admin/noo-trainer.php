<?php
/**
 * Register NOO Trainer.
 * This file register Item and Category for NOO Trainer.
 *
 * @package    NOO Framework
 * @subpackage NOO Trainer
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */

// add_action( 'pre_get_posts', 'noo_trainer_pre_get_posts');
// if ( ! function_exists('noo_trainer_pre_get_posts')) :
// 	function noo_trainer_pre_get_posts($q){
// 		if( is_admin() ) {
// 			return;
// 		}

// 		$items_per_page = noo_get_option('noo_trainer_num','');
// 		if ( ! $q->is_main_query() ) {
// 			return;
// 		}
// 		if($q->get('post_type') == 'noo_trainer'){
// 			$q->set('posts_per_page',$items_per_page);
// 		}
		
// 		return $q;
// 	}
// endif;

if ( ! function_exists('noo_init_trainer')) :
	function noo_init_trainer() {

		// Text for NOO Trainer.
		$trainer_labels = array(
			'name' => __('Trainer', 'noo') ,
			'singular_name' => __('Trainer', 'noo') ,
			'menu_name' => __('Trainer', 'noo') ,
			'add_new' => __('Add New', 'noo') ,
			'add_new_item' => __('Add New Trainer Item', 'noo') ,
			'edit_item' => __('Edit Trainer Item', 'noo') ,
			'new_item' => __('Add New Trainer Item', 'noo') ,
			'view_item' => __('View Trainer', 'noo') ,
			'search_items' => __('Search Trainer', 'noo') ,
			'not_found' => __('No Trainer items found', 'noo') ,
			'not_found_in_trash' => __('No Trainer items found in trash', 'noo') ,
			'parent_item_colon' => ''
		);
		
		$admin_icon = NOO_FRAMEWORK_ADMIN_URI . '/assets/images/noo20x20.png';
		if ( floatval( get_bloginfo( 'version' ) ) >= 3.8 ) {
			$admin_icon = 'dashicons-businessman';
		}

		if ( get_transient( 'noo_trainer_slug_before' ) != get_transient( 'noo_trainer_slug_after' ) ) {
			flush_rewrite_rules( false );
			delete_transient( 'noo_trainer_slug_before' );
			delete_transient( 'noo_trainer_slug_after' );
		}
		
		$trainer_page = noo_get_option('noo_trainer_page', '');
		$trainer_slug = !empty($trainer_page) ? get_post( $trainer_page )->post_name : 'noo-trainer';

		// Options
		$trainer_args = array(
			'labels' => $trainer_labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			// 'menu_position' => 5,
			'menu_icon' => $admin_icon,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array(
				'title',
				 'editor',
				// 'excerpt',
				'thumbnail',
				// 'comments',
				// 'custom-fields',
				'revisions'
			) ,
			'has_archive' => true,
			'rewrite' => array(
				'slug' => $trainer_slug,
				'with_front' => false
			)
		);
		
		register_post_type('noo_trainer', $trainer_args);

		// Register a taxonomy for Trainer Categories.
		$category_labels = array(
			'name' => __('Trainer Categories', 'noo') ,
			'singular_name' => __('Trainer Category', 'noo') ,
			'menu_name' => __('Trainer Categories', 'noo') ,
			'all_items' => __('All Trainer Categories', 'noo') ,
			'edit_item' => __('Edit Trainer Category', 'noo') ,
			'view_item' => __('View Trainer Category', 'noo') ,
			'update_item' => __('Update Trainer Category', 'noo') ,
			'add_new_item' => __('Add New Trainer Category', 'noo') ,
			'new_item_name' => __('New Trainer Category Name', 'noo') ,
			'parent_item' => __('Parent Trainer Category', 'noo') ,
			'parent_item_colon' => __('Parent Trainer Category:', 'noo') ,
			'search_items' => __('Search Trainer Categories', 'noo') ,
			'popular_items' => __('Popular Trainer Categories', 'noo') ,
			'separate_items_with_commas' => __('Separate Trainer Categories with commas', 'noo') ,
			'add_or_remove_items' => __('Add or remove Trainer Categories', 'noo') ,
			'choose_from_most_used' => __('Choose from the most used Trainer Categories', 'noo') ,
			'not_found' => __('No Trainer Categories found', 'noo') ,
		);
		
		$category_args = array(
			'labels' => $category_labels,
			'public' => false,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud' => true,
			'show_admin_column' => true,
			'hierarchical' => true,
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'trainer_category',
				'with_front' => false
			) ,
		);
		
		register_taxonomy('trainer_category', array(
			'noo_trainer'
		) , $category_args);
	}
endif;

add_action('init', 'noo_init_trainer');

// if ( ! function_exists('noo_trainer_template_loader')) :
// 	function noo_trainer_template_loader($template){
// 		if(is_post_type_archive( 'noo_trainer' ) || is_tax( 'trainer_category' ) || is_tax( 'trainer_tag' ) ){
// 			$template       = locate_template( 'archive-noo_trainer.php' );
// 		}
// 		return $template;
// 	}

// endif;

// add_filter( 'template_include', 'noo_trainer_template_loader' );

// Add Thumbnail Column to the Trainer Admin page
// if ( ! function_exists('noo_add_trainer_thumbnail_column')) :
// 	function noo_add_trainer_thumbnail_column($columns) {
// 		$before = array_slice($columns, 0, 1);
// 		$after = array_slice($columns, 1);
		
// 		$thumbnail_column = array(
// 			'thumbnail' => __('Thumbnail', 'noo')
// 		);
		
// 		$columns = array_merge($before, $thumbnail_column, $after);
// 		return $columns;
// 	}
// endif;

// if ( ! function_exists('noo_display_trainer_thumbnail_column')) :
// 	function noo_display_trainer_thumbnail_column($column) {
// 		GLOBAL $post;
		
// 		if ($column == 'thumbnail') {
// 			$prefix = '_noo_trainer';
// 			$admin_thumb = 'thumbnail';
// 			$post_id = get_the_ID();
// 			$post_format = noo_get_post_meta($post_id, "{$prefix}_media_type", 'image');
// 			$thumb = '';

// 			switch ($post_format) {
// 				case 'image':
// 					$main_image = noo_get_post_meta($post_id, "{$prefix}_main_image", 'featured');
// 					if( $main_image == 'featured') {
// 						$thumb = get_the_post_thumbnail($post_id, $admin_thumb);
// 					} else {
// 						$image_id = (int) noo_get_post_meta($post_id, "{$prefix}_image", '');
// 						$thumb = !empty($image_id) ? wp_get_attachment_image( $image_id, $admin_thumb) : '';
// 					}

// 					break;
// 				case 'link':
// 					$link = noo_get_post_meta($post_id, "{$prefix}_url", '#');
// 					$thumb = get_the_post_thumbnail($post_id, $admin_thumb);
// 					break;
// 				case 'gallery':
// 					$gallery_ids = noo_get_post_meta($post_id, "{$prefix}_gallery", '');
// 					if(!empty($gallery_ids)) {
// 						$gallery_arr = explode(',', $gallery_ids);
// 						$image_id = (int) $gallery_arr[0];
// 						$thumb = !empty($image_id) ? wp_get_attachment_image( $image_id, $admin_thumb) : '';
// 					}

// 					break;
// 				default:
// 					$thumb = get_the_post_thumbnail($post_id, $admin_thumb);
// 					break;
// 			}

// 			echo '<a href="' . get_edit_post_link() . '">' . $thumb . '</a>';
// 		}
// 	}
// endif;

// add_filter('manage_edit-noo_trainer_columns', 'noo_add_trainer_thumbnail_column');
// add_action('manage_posts_custom_column', 'noo_display_trainer_thumbnail_column');

// if ( ! function_exists('noo_trainer_customizer_before_save')) :
// 	function noo_trainer_customizer_before_save() {
// 		set_transient( 'noo_trainer_slug_before', noo_get_option( 'noo_trainer_page', 'trainers' ), 60 );
// 	}
// endif;

// if ( ! function_exists('noo_trainer_customizer_after_save')) :
// 	function noo_trainer_customizer_after_save() {
// 		set_transient( 'noo_trainer_slug_after', get_option( 'noo_trainer_page', 'trainers' ), 60 );
// 	}
// endif;
// add_action( 'customize_save', 'noo_trainer_customizer_before_save');
// add_action( 'customize_save_after', 'noo_trainer_customizer_after_save');