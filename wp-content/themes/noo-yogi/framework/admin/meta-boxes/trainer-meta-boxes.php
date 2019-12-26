<?php
/**
 * NOO Meta Boxes Package
 *
 * Setup NOO Meta Boxes for Portfolio
 * This file add Meta Boxes to Trainer edit page.
 *
 * @package    NOO Framework
 * @subpackage NOO Meta Boxes
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */

if( NOO_SUPPORT_TRAINER ) :
	if ( ! function_exists( 'noo_trainer_meta_boxes' ) ) :
		function noo_trainer_meta_boxes() {

			// Remove Meta box
			remove_meta_box( 'mymetabox_revslider_0', 'noo_trainer', 'normal' );

			// Declare helper object
			$prefix = '_noo_trainer';
			$helper = new NOO_Meta_Boxes_Helper( $prefix, array( 'page' => 'noo_trainer' ) );

			// $helper->add_meta_box( $meta_box );
            $meta_box = array(
                'id' => "{$prefix}_meta_box_noo_trainer",
                'title' => __('Trainer Information:', 'noo'),
                'fields' => array(
                    array(
                        'id' => "{$prefix}_position",
                        'label' => __( 'Position', 'noo' ),
                        'type' => 'text',
                    ),
                    array(
                        'id' => "{$prefix}_experience",
                        'label' => __( 'Experience', 'noo' ),
                        'type' => 'text',
                    ),
                    array(
                        'id' => "{$prefix}_email",
                        'label' => __( 'Email', 'noo' ),
                        'type' => 'text',
                    ),
                    array(
                        'id' => "{$prefix}_phone",
                        'label' => __( 'Phone', 'noo' ),
                        'type' => 'text',
                    ),
                    array(
                        'id' => "{$prefix}_biography",
                        'label' => __( 'Biography', 'noo' ),
                        'type' => 'textarea',
                    ),
                )
            );

            $helper->add_meta_box($meta_box);
            // Data type: Social Media
            $meta_box = array(
                'id'           => "{$prefix}_meta_box_social",
                'title'        => __( 'Media Data: Social', 'noo' ),
                'fields'       => array(
                    array(
                        'id' => "{$prefix}_facebook",
                        'label' => __( 'Facebook URL', 'noo' ),
                        'type' => 'text',
                    ),
                    array(
                        'id' => "{$prefix}_twitter",
                        'label' => __( 'Twitter URL', 'noo' ),
                        'type' => 'text',
                    ),
                    array(
                        'id' => "{$prefix}_google",
                        'label' => __( 'Google URL', 'noo' ),
                        'type' => 'text',
                    ),
                    array(
                        'id' => "{$prefix}_pinterest",
                        'label' => __( 'Pinterest URL', 'noo' ),
                        'type' => 'text',
                    ),
                )
            );
            $helper->add_meta_box($meta_box);
		}

	endif;

	add_action( 'add_meta_boxes', 'noo_trainer_meta_boxes' );
endif;
