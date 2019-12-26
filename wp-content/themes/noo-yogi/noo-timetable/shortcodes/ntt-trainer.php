<?php
/**
 * Shortcode Noo Trainer
 *
 * This template can be overridden by copying it to yourtheme/noo-timetable/shortcodes/ntt-trainer.php.
 *
 * @author      NooTheme
 * @package     NooTimetable/Templates/Shortcodes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'shortcode_ntt_trainer' ) ) :
	
	function shortcode_ntt_trainer( $atts ) {

		extract( shortcode_atts( array(
            'title'             => '',
            'sub_title'         => '',
            'layout_style'      => 'grid',
            'columns'           => '4',
            'filter'            => '',
            'cat'               => 'all',
            'orderby'           => 'default',
            'limit'             => '4',
            'visibility'        => '',
            'class'             => '',
            'id'                => '',
            'custom_style'      => ''
        ), $atts ) );

        $categories = $cat;

        if ( $layout_style === 'list' ) {
            $columns = 1;
        }

        $order = 'DESC';
        switch ($orderby) {
            case 'latest':
            $orderby = 'date';
            break;

            case 'oldest':
            $orderby = 'date';
            $order = 'ASC';
            break;

            case 'alphabet':
            $orderby = 'title';
            $orderby = 'ASC';
            break;

            case 'ralphabet':
            $orderby = 'title';
            break;

            default:
            $orderby = 'default';
            break;
        }
        wp_enqueue_script('vendor-isotope');

        $args = array(
            'post_type'       => "noo_trainer",
            'posts_per_page'  => $limit,
            );

        if ('default' != $orderby) {
            $args['orderby'] = $orderby;
            $args['order']   = $order;
        }

        if(!empty($categories) && $categories != 'all'){
            $args['tax_query'][] =  array(
                    'taxonomy' => 'class_category',
                    'terms'    => explode(',',$categories),
                    'field'    => 'id'
            );
        }
        $prefix = "_noo_trainer";
        $q = new WP_Query( $args );

        $html = array();
        
        $class          = ( $class      != '' ) ? 'noo-trainer ' . esc_attr( $class ) : 'noo-trainers';
        $visibility     = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
        $class         .= noo_visibility_class( $visibility );
        
        $id    = ( $id    != '' ) ? ' id="' . esc_attr( $id ) . '"' : '';
        $class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
        $custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';

        $masonry_class  = 'masonry '.$layout_style;
        
        $html = array();
        $trainer_class  = '';
        $masonry_gutter   = '40';
        // if( $layout == 'masonry' ) {
        //  $trainer_class = 'masonry-trainer';
        //  if( $layout_style == 'masonry' ) {
        //      //$trainer_class .= ' no-gap';
        //      $masonry_gutter   = '0';
        //  }
        // }
        $item_class='';
        
        $html[] ='<div '.$id.$class.$custom_style.'>';
        
        $html[] = (!empty($title) ? '<h2 class="title custom-title-home-center">'.noo_nth_word($title,'first', false).(!empty($sub_title) ? '<p class="small sub-title">'.$sub_title.'</p>' : '').'</h2>' : '');
        
        wp_enqueue_script('vendor-isotope');
        
        $html[] = "<div class=\"{$masonry_class}\">";
        ?>
        <?php 
        $categories_html = array();
        $category_arr = explode(',', $categories);
        if( count( $category_arr ) > 0 && $filter == 'true' ):
        ob_start()
        ?>
        <div class="masonry-header<?php echo ($layout_style != 'true' ? ' masonry-header-gap':'') ?>">
            <div class="">
                <div class="masonry-filters">
                    <ul data-option-key="filter" >
                        <li>
                            <a class="selected" href="#" data-option-value= "*"><?php echo __('All','noo') ?></a>
                        </li>
                        <?php
                        if ( class_exists('Noo__Timetable__Class') ) :
                        foreach ($category_arr as $cat):
                            if($cat == 'all')
                                continue;
                            $category = get_term($cat, 'class_category');
                            if($category):
                            ?>
                            <li>
                                <a href="#" data-option-value= ".<?php echo 'mansonry-filter-'.$category->slug?>"><?php echo esc_html($category->name); ?></a>
                            </li>
                            <?php endif;?>
                    <?php endforeach;
                        endif;
                    ?>
                    </ul>
                </div>
            </div>
        </div><!-- /.masonry-header -->
        <?php
        $html[] = ob_get_clean();
        endif;
        ?>
        <?php
        $html[] = '<div class="mansonry-content">';
        $html[] = '<div id="masonry-container" data-masonry-gutter="' . $masonry_gutter . '" data-masonry-column="'.$columns.'" class="masonry-container columns-'.$columns.'">';
        
        ob_start();
        if ( $q->have_posts() ) : while ( $q->have_posts() ) : $q->the_post(); 
        global $post;
        $post_id = get_the_id();
        $post_type = get_post_type($post_id);
        $post_format = noo_get_post_format($post_id, $post_type);
        $masonry_size = noo_get_post_meta($post_id, "_noo_trainer_masonry_{$post_format}_size", 'regular');
        
        $cat_class = array();
        foreach ( (array) get_the_terms($post->ID,'class_category') as $cat ) {
            if ( empty($cat->slug ) )
                continue;
            $cat_class[] = 'mansonry-filter-' . sanitize_html_class($cat->slug, $cat->term_id);
        }
        $item_class = 'masonry-item '.$masonry_size.' '.implode(' ', $cat_class);
        $post_format = ('' === $post_format) ? noo_get_post_meta($post_id, "{$prefix}_media_type", 'image') : $post_format;

        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class($item_class)?>>
            <div class="trainer-item-wrap">
                <?php if($layout_style == 'grid'):?>
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
                <?php if($layout_style == 'list'):
                    $prefix = '_noo_trainer';
                    // Trainer's info
                    $position       = noo_get_post_meta( get_the_ID(), "{$prefix}_position", '' );
                    $experience     = noo_get_post_meta( get_the_ID(), "{$prefix}_experience", '' );
                    $email          = noo_get_post_meta( get_the_ID(), "{$prefix}_email", '' );
                    $phone          = noo_get_post_meta( get_the_ID(), "{$prefix}_phone", '' );
                    $phone_esc      = preg_replace('/\s+/', '', $phone);
                    $biography      = noo_get_post_meta( get_the_ID(), "{$prefix}_biography", '' );
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
        <?php
        endwhile;
        endif;
        
        $html[] = ob_get_clean();
        $html[] = '</div><!-- /#masonry-container -->';
        $html[] = '</div><!-- /.mansonry-content -->';
        $html[] = '</div><!-- /.mansonry -->';
        
        $html[] = '</div>';
        return implode( "\n", $html );

	}

	add_shortcode( 'ntt_trainer', 'shortcode_ntt_trainer' );

endif;