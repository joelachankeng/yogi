<?php
/**
 * Shortcode Noo Event
 *
 * This template can be overridden by copying it to yourtheme/noo-timetable/shortcodes/ntt-event.php.
 *
 * @author      NooTheme
 * @package     NooTimetable/Templates/Shortcodes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'shortcode_ntt_event' ) ) :
	
	function shortcode_ntt_event( $atts ) {

		extract( shortcode_atts( array(
            'title'                => '',
            'sub_title'            => '',
            'layout_style'         => 'grid',
            'autoplay'             => 'true',
            'columns'              => '4',
            'cat'                  => 'all',
            'orderby'              => 'default',
            'limit'                => '4',
            'hide_past_events'     => false,
            'hide_events_time'     => false,
            'hide_events_date'     => false,
            'hide_events_location' => false,
            'show_pagination'      => 'yes',
            'visibility'           => '',
            'class'                => '',
            'custom_style'         => '',
            'excerpt_length'       => 30,
        ), $atts ) );

        $categories = $cat;

        $visibility   = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
        $class        = ( $class           != ''     ) ? 'noo-event ' . esc_attr( $class ) : 'noo-event '.'col-'.$columns;
        $class        .= noo_visibility_class( $visibility );
        $class        = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
        $custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';

        $paged = ( get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );

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
                $order = 'ASC';
            break;
            case 'ralphabet':
                $orderby = 'title';
                break;
            case 'startdate_desc':
                $orderby = 'meta_value_num';
                $meta_key = '_noo_event_start_date';
                break;
            case 'start_date':
            case 'startdate_asc':
                $orderby = 'meta_value_num';
                $meta_key = '_noo_event_start_date';
                $order = 'ASC';
                break;
            default:
                $orderby = 'date';
                break;
        }

        $args = array(
            'paged'               => $paged,
            'orderby'             => $orderby,
            'order'               => $order,
            'posts_per_page'      => $limit,
            'no_found_rows'       => false,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'post_type'           => 'noo_event'
        );

        if (isset($meta_key) && $meta_key != '')
            $args['meta_key'] = $meta_key;

        if ( $hide_past_events ) {
           $args['post__not_in'] = Noo_Event::get_past_events();
        }

        if ( $categories != 'all' ) {
            $categories = explode(',', $categories);
            $args['tax_query'][]  = array(
                'taxonomy' =>  'event_category',
                'field'    =>  'term_id',
                'terms'    =>  $categories
            );
        }

        $wp_query = new WP_Query( $args );
        
        wp_enqueue_script('vendor-isotope');
        
        ob_start();
        $html = '';
        // Load display style
        $layout_style_class = '';
        if ( $layout_style == 'list' ) {
            $post_class = 'loadmore-item masonry-item col-md-12';
            $layout_style_class = 'col-sm-6';
        } else {
            $post_class = 'loadmore-item masonry-item col-sm-6 col-md-'.absint((12 / $columns));
        }
        
        ?>
        <div class="posts-loop masonry  noo-events">

            <?php if ( !empty( $title ) || !empty( $sub_title ) ) : ?>
            <h2 class="custom-title-home-center">

                <?php if ( !empty( $title ) ) : ?>
                    <?php noo_nth_word( $title ,'first' ); ?>
                <?php endif; ?>

                <?php if ( !empty( $sub_title ) ) : ?>
                    <p class="small sub-title"><?php echo esc_html( $sub_title ); ?></p>
                <?php endif; ?>
            </h2>
            <?php endif; ?>
            
            <div class="row posts-loop-content loadmore-wrap">
                <div class="masonry-container">
                    <?php 
                    while ( $wp_query->have_posts() ) : $wp_query->the_post(); global $post;
                    ?>
                    <article <?php  post_class($post_class); ?>>

                        <?php $is_featured = get_post_meta( $post->ID, 'event_is_featured', true ); ?>
                        <div class="loop-item-wrap">
                            <?php if(has_post_thumbnail()):?>
                            <div class="loop-item-featured <?php echo $layout_style_class ?>">
                                <a href="<?php the_permalink() ?>">
                                        <?php
                                                if ( $is_featured == '1' ) :
                                                    echo '<span class="is_featured">' . esc_html__( 'Featured', 'noo-timetable' ) . '</span>';
                                                endif;
                                            ?>
                                    <?php the_post_thumbnail('noo-thumbnail-square')?>
                                </a>
                            </div>
                            <?php endif;?>
                            <div class="loop-item-content <?php echo $layout_style_class ?>">
                                <div class="loop-item-content-summary">
                                    <div class="loop-item-category"><?php echo get_the_term_list(get_the_ID(), 'event_category',' ',', ')?></div>
                                    <h2 class="loop-item-title">
                                        <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <div class="loop-item-excerpt">
                                        <?php
                                        $excerpt = $post->post_excerpt;
                                        if(empty($excerpt))
                                            $excerpt = $post->post_content;
                                        
                                        $excerpt = strip_shortcodes($excerpt);
                                        echo '<p>' . wp_trim_words($excerpt,$excerpt_length,'...') . '</p>';
                                        ?>
                                    </div>
                                    <?php 
                                    
                                    $event_address      = noo_get_post_meta( get_the_ID(), "_noo_event_address", '' );
                                    $event_starttime    = noo_get_post_meta( get_the_ID(), "_noo_event_start_time", '' );
                                    $event_startdate    = noo_get_post_meta( get_the_ID(), "_noo_event_start_date", '' );
                                    ?>
                                    <div class="event-info">
                                        <?php if( !empty( $event_starttime ) && !$hide_events_time ) : ?>
                                            <div>
                                                <i class="fa fa-clock-o"></i>&nbsp;<?php echo date_i18n(get_option('time_format'),$event_starttime); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if( !empty( $event_startdate ) && !$hide_events_date ) : ?>
                                            <div>
                                                <i class="fa fa-calendar"></i>&nbsp;<?php echo date_i18n(get_option('date_format'),$event_startdate); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if( !empty($event_address) && !$hide_events_location ) : ?>
                                            <div>
                                                <i class="fa fa-map-marker"></i>&nbsp;<?php echo esc_html($event_address);?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="loop-item-action">
                                    <a class="btn btn-default btn-block text-uppercase" href="<?php the_permalink()?>"><?php echo __('Learn More','noo')?></a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endwhile;?>
                </div>
                <?php if($show_pagination == 'yes' && 1 < $wp_query->max_num_pages):?>
                <div class="loadmore-action">           
                    <a href="#" class="btn-loadmore btn-primary" title="<?php _e('Load More','noo')?>"><?php _e('Load More','noo')?></a>
                    <div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span></div>
                </div>
                <?php noo_pagination(array(),$wp_query)?>
                <?php endif;?>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();
        wp_reset_query();
        return $html;

	}

	add_shortcode( 'ntt_event', 'shortcode_ntt_event' );

endif;