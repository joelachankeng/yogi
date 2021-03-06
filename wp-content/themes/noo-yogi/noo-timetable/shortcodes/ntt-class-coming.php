<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'shortcode_ntt_class_coming' ) ) :
	
	function shortcode_ntt_class_coming( $atts ) {

		extract( shortcode_atts( array(
            'title'             => '',
            'sub_title'         => '',
            'layout_style'      => 'default',
            'show_info'         => 'all',
            'columns'           => '4',
            'cat'               => 'all',
            'orderby'           => 'default',
            'limit'             => '4',
            'trainer'           => '0',
            'visibility'        => '',
            'class'             => '',
            'custom_style'      => ''
        ), $atts ) );

        $categories = $cat;

        $visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
        $class            = ( $class           != ''     ) ? 'noo-recent-news ' . esc_attr( $class ) : 'noo-recent-class '.'col-'.$columns;
        $class           .= noo_visibility_class( $visibility );
        $class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
        $custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';

        $html = '';

        $order = 'DESC';
        switch ($orderby) {
            case 'open_date':
                $orderby = 'meta_value_num';
                $order = 'ASC';
                break;
            case 'latest':
                $orderby = 'date';
                break;
            case 'oldest':
                $orderby = 'date';
                $order = 'ASC';
                break;
            case 'alphabet':
                $orderby = 'title';
                $order = 'ASC';
                break;
            case 'ralphabet':
                $orderby = 'title';
                break;
            default:
                $orderby = 'date';
                break;
        }
        
        $comming_class_ids = Noo_Class::get_coming_class_ids();
        $args = array(
            'posts_per_page'      => $limit,
            'no_found_rows'       => true,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'post_type'           => 'noo_class',
            'orderby'             => 'meta_value_num',
            'order'               => 'ASC',
            'meta_key'            => '_next_date',
            'post__in'            => $comming_class_ids
        );

        
        if ( $trainer > 0 ) {
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => '_trainer',
                    'value' => $trainer,
                ),
                array(
                    'key' => '_trainer',
                    'value' => '"' . $trainer . '"',
                    'compare' => 'LIKE'
                )
            );
        }

        if ( !empty( $categories ) && $categories != 'all' ) {
            $args['tax_query'][]  = array(
                'taxonomy' =>  'class_category',
                'field'    =>  'term_id',
                'terms'    =>   array($categories)
            );
        }

        $r = new WP_Query( $args );

        ob_start();
        if($r->have_posts()):
            ?>
            <div <?php echo ( $class . ' ' . $custom_style);?> >

                <?php if ( !empty( $title ) || !empty( $sub_title ) ) : ?>
                <h3 class="custom-title-home-center">

                    <?php if ( !empty( $title ) ) : ?>
                        <?php noo_nth_word( $title ,'first' ); ?>
                    <?php endif; ?>

                    <?php if ( !empty( $sub_title ) ) : ?>
                        <p class="small sub-title"><?php echo esc_html( $sub_title ); ?></p>
                    <?php endif; ?>
                </h3>
                <?php endif; ?>

                <div class="noo-rn-content <?php if($layout_style == 'default') {echo 'default';} else {echo 'info_overlay';} ?>">
                    <?php
                    // Handle option [show_info]
                    switch ($show_info) {
                        case 'null':
                            $show_date = false;
                            $show_time = false;
                            break;
                        case 'date':
                            $show_date = true;
                            $show_time = false;
                            break;
                        case 'time':
                            $show_date = false;
                            $show_time = true;
                            break;
                        default:
                            $show_date = true;
                            $show_time = true;
                            break;
                    }

                    $i = 0;
                    ?>
                    <?php if ( count($comming_class_ids) > 0 ) : while ($r->have_posts()): $r->the_post(); global $post;?>
                        <?php
                            $class_address      = noo_get_post_meta( get_the_ID(), '_address', '' );
                            $open_date          = noo_get_post_meta( get_the_ID(), '_open_date', '' );
                            $open_time          = noo_get_post_meta( get_the_ID(), '_open_time', '' );
                            $close_time         = noo_get_post_meta( get_the_ID(), '_close_time', '' );
                            $class_trainer      = noo_get_post_meta( get_the_ID(), '_trainer', '' );

                            $next_date          = noo_get_post_meta( get_the_ID(), '_next_date', '' );
                        ?>
                        <?php if($i++ % $columns == 0):?>
                            <div class="row">
                        <?php endif;?>
                        <div class="noo-rn-item loop-item col-sm-<?php echo absint((12 / $columns)) ?>">
                            <div class="item-wrap">
                                <?php if(has_post_thumbnail()):?>
                                    <div class="item-featured">
                                        <a href="<?php the_permalink() ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>">
                                            <?php the_post_thumbnail('noo-thumbnail-square')?>
                                        </a>
                                        <?php if($layout_style == 'info_overlay') : ?>
                                            <div class="infor-overlay">
                                                <div class="inner">
                                                    <?php if( !empty( $next_date) && $show_date) : ?>
                                                        <span><i class="fa fa-calendar">&nbsp;</i><?php echo esc_html(date_i18n(get_option('date_format'),$next_date)); ?></span>
                                                    <?php endif; ?>
                                                    <?php if( ( !empty( $open_time) || !empty( $close_time) )  && $show_time ) : ?>
                                                        <span><i class="fa fa-clock-o">&nbsp;</i>
                                                        <?php echo date_i18n(get_option('time_format'),$open_time); ?>
                                                        <?php if(  !empty( $close_time )  && $show_time ) : ?>
                                                            <?php echo '-'. date_i18n(get_option('time_format'),$close_time); ?>
                                                        <?php endif; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if( !empty( $class_trainer ) ) : ?>
                                                        <span><em><?php _e('By ', 'noo'); ?><?php Noo__Timetable__Class::get_trainer_list($class_trainer); ?></em></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif;?>
                                    </div>
                                <?php endif;?>
                                <div class="item-content">
                                    <h3 class="item-title hvr-bounce-to-bottom">
                                        <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <?php if($layout_style == 'default') : ?>
                                        <div class="info">
                                            <span class="time">
                                                <?php
                                                    $datetime_arr = array();
                                                    if( !empty( $next_date) && $show_date)
                                                        $datetime_arr[] = esc_html(date_i18n(get_option('date_format'),$next_date));
                                                    if( !empty( $open_time)  && $show_time )
                                                        $datetime_arr[] = date_i18n(get_option('time_format'),$open_time);
                                                    if( !empty( $close_time)  && $show_time )
                                                        $datetime_arr[] = date_i18n(get_option('time_format'),$close_time);
                                                    if ( $datetime_arr )
                                                        echo implode(' - ', $datetime_arr);
                                                ?>
                                            </span>
                                            <?php if( !empty( $class_trainer ) ) : ?>
                                                <span><?php _e('By ', 'noo'); ?><em><?php Noo__Timetable__Class::get_trainer_list($class_trainer); ?></em></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="learn-more">
                                            <a class="btn-darker" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>">
                                                <?php _e('Learn more', 'noo'); ?>
                                            </a>
                                        </div>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                        <?php if($i % $columns == 0 || $i == $r->post_count):?>
                            </div>
                        <?php endif;?>
                    <?php endwhile; endif;?>
                </div>
            </div>
        <?php
        endif;
        $html .= ob_get_clean();
        wp_reset_query();
        return $html;

	}

	add_shortcode( 'ntt_class_coming', 'shortcode_ntt_class_coming' );

endif;