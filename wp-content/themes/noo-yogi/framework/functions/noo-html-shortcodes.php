<?php

// Incremental ID Counter for Templates
if ( ! function_exists( 'noo_vc_elements_id_increment' ) ) :
	function noo_vc_elements_id_increment() {
		static $count = 0; $count++;
		return $count;
	}
endif;

// Function for handle element's visibility
if ( ! function_exists( 'noo_visibility_class' ) ) :
	function noo_visibility_class( $visibility = '' ) {
		switch ($visibility) {
		    case 'hidden-phone':
		        return ' hidden-xs';
		    case 'hidden-tablet':
		        return ' hidden-sm';
		    case 'hidden-pc':
		        return ' hidden-md hidden-lg';
		    case 'visible-phone':
		        return ' visible-xs';
		    case 'visible-tablet':
		        return ' visible-sm';
		    case 'visible-pc':
		        return ' visible-md visible-lg';
		    default:
		        return '';
		}
	}
endif;

// [animation]
// ============================
if( !function_exists( 'noo_shortcode_animation' ) ) {
	function noo_shortcode_animation( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'class'                 => '',
			'id'                    => '',
			'custom_style'          => '',
			'animation'        		=> '',
			'animation_offset'   	=> '',
			'animation_duration'    => '1000',
			'animation_delay'		=>'0'
		), $atts));

		wp_enqueue_script( 'vendor-appear' );

		$class             = ( $class != '' ) ? 'noo-animation ' . esc_attr( $class ) : 'noo-animation';
		$class            .= !empty( $animation ) ? ' animated '. $animation : '';

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . $class . '"'  : '';
		$html  = array();
		if(!empty($animation)) {
			$html[] = "<div class=\"animatedParent\" data-appear-top-offset=\"{$animation_offset}\">";
			$custom_style = ";-webkit-animation-duration:".$animation_duration."ms;animation-duration:".$animation_duration."ms; -webkit-animation-delay:".$animation_delay."ms;animation-delay:".$animation_delay."ms; ";
		}
		
		$custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';
		
		$html[] = "<div {$id} {$class} {$custom_style}>" . do_shortcode( $content ) . "</div>";
		
		if(!empty($animation))
			$html[] = "</div>";
		
		return implode( "\n", $html );
	}
}
add_shortcode('animation', 'noo_shortcode_animation');

// [gap]
// ============================
if( !function_exists( 'noo_shortcode_gap' ) ) {
	function noo_shortcode_gap( $atts, $content = null ) {
		extract( shortcode_atts( array(
			"size"                 => "",
			"visibility"           => "",
			"class"                => "",
			"id"                   => "",
			"custom_style"         => ""
		), $atts));

		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class            = ( $class           != ''     ) ? 'noo-gap ' . esc_attr( $class ) : 'noo-gap';
		$class           .= noo_visibility_class( $visibility );

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style  .= ( $size != '' ) ? " margin: {$size}px 0 0 0;" : '';
		
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';

		$html = "<hr {$id} {$class} {$custom_style}>";

		return $html;
	}
}
add_shortcode('gap', 'noo_shortcode_gap');

// [clear]
// ============================
if( !function_exists( 'noo_shortcode_clear' ) ) {
	function noo_shortcode_clear( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'class'           => '',
			'id'              => '',
			'custom_style'    => ''
		), $atts));

		$html = array();

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="noo-clear ' . esc_attr( $class ) . '"' : 'class="noo-clear"';

		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';

		$html = "<hr {$id} {$class} {$custom_style}>";

		return $html;
	}
}
add_shortcode('clear', 'noo_shortcode_clear');

// [dropcap]
// ============================
if( !function_exists( 'noo_shortcode_dropcap' ) ) {
	function noo_shortcode_dropcap( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'style'           => 'transparent',
			'letter'		  => '',
			'color'           => '',
			'bg_color'        => '',
			'class'           => '',
			'id'              => '',
			'custom_style'    => ''
		), $atts));

		$html = array();

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'noo-dropcap ' . esc_attr( $class ) : 'noo-dropcap';

		$custom_style .= ( $color != '' ) ? ' color: ' . esc_attr( $color ) . ';' : '';
		if( $style == 'transparent' ) {
			$custom_style .= ' background-color: transparent;';
		} else {
			$class .= ' dropcap-' . $style;
			$custom_style .= ( $bg_color != '' ) ? ' background-color: ' . esc_attr( $bg_color ) . ';' : '';
		}

		$class        = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';

		$content = empty($content) ? $letter : $content;
		$html = "<span {$id} {$class} {$custom_style}>{$content}</span>";

		return $html;
	}
}
add_shortcode('dropcap', 'noo_shortcode_dropcap');

// [quote]
// ============================
if( !function_exists( 'noo_shortcode_quote' ) ) {
	function noo_shortcode_quote( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'cite'            => '',
			'type'            => 'block',
			'alignment'       => 'left',
			'position'        => '',
			"visibility"      => "",
			'class'           => '',
			'id'              => '',
			'custom_style'    => ''
		), $atts));

		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class            = ( $class           != ''     ) ? 'noo-quote ' . esc_attr( $class ) : 'noo-quote';
		$class           .= noo_visibility_class( $visibility );

		$html = array();

		$class .= ( $alignment != '' ) ? ' text-' . $alignment : '';

		if( $type == 'pull' ) {
			$class .= ' pullquote';
			$class .= ( $position != '' ) ? ' pullquote-' . $position : '';
		}

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';

		$html[] = "<blockquote {$id} {$class} {$custom_style}>";
		$html[] = '  <p>' . noo_handler_shortcode_content( $content, true ) . '</p>';
		if( $cite != '' ) {
			$html[] = "  <cite title=\"{$cite}\">{$cite}</cite>";
		}
		$html[] = '</blockquote>';

		return implode( "\n", $html );
	}
}
add_shortcode('quote', 'noo_shortcode_quote');

// [icon]
// ============================
if( !function_exists( 'noo_shortcode_icon' ) ) {
	function noo_shortcode_icon( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'icon'                 => '',
			'size'                 => '',
			'custom_size'          => '',
			'icon_color'           => '',
			'hover_icon_color'     => '',
			'shape'                => '',
			'style'                => '',
			'bg_color'             => '',
			'hover_bg_color'       => '',
			'border_color'         => '',
			'hover_border_color'   => '',
			"visibility"           => '',
			'class'                => '',
			'id'                   => '',
			'custom_style'         => ''
		), $atts));

		if( $icon == '' ) {
			return '';
		}
 
		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class            = ( $class           != ''     ) ? 'noo-icon ' . esc_attr( $class ) : 'noo-icon';
		$class           .= noo_visibility_class( $visibility );

		$html = array();

		$id     = ( $id    != '' ) ? esc_attr( $id ) : 'noo-icon-' . noo_vc_elements_id_increment();
		$custom_style  = ( $custom_style != '' ) ? esc_attr( $custom_style ) : '';
		$hover_custom_style  = '';

		$class .= ( $shape != '' ) ? ' icon-' . $shape : '';
		$class .= ( $style == 'stack_filled' || $style == 'stack_bordered' ) ? ' fa-stack' : '';
		$class .= ( $size != '' && $size != 'custom' ) ? ' fa-' . $size : '';

		$icon  .= ( $style == 'stack_filled' || $style == 'stack_bordered' ) ? ' fa-stack-1x' : '';
		$icon  .= ( $style == 'stack_filled' ) ? ' fa-inverse' : '';

		$custom_style .= ( $size == 'custom' && $custom_size != '' ) ? ' font-size: ' . $custom_size . 'px;' : '';
		$custom_style .= ( $icon_color != '' ) ? ' color: ' . esc_attr( $icon_color ) . ';' : '';
		$hover_custom_style .= ( $hover_icon_color != '' ) ? ' color: ' . esc_attr( $hover_icon_color ) . ';' : '';
		$icon_custom_style = '';

		if( $style == 'custom' ) {
			$class .= ' icon-style-custom';
			$font_size_base = noo_get_option( 'noo_typo_body_font_size', '14' );
			$icon_alignment = 14 * 2.35;
			switch( $size ) {
				case 'lg':
					$icon_alignment = $font_size_base * 1.33;
					break;
				case '2x':
					$icon_alignment = $font_size_base * 2;
					break;
				case '3x':
					$icon_alignment = $font_size_base * 3;
				case '4x':
					$icon_alignment = $font_size_base * 4;
				case '5x':
					$icon_alignment = $font_size_base * 5;
					break;
				case 'custom';
					if( $custom_size != '' ) {
						$icon_alignment = $custom_size;
					}
					else {
						$icon_alignment = $font_size_base;
					}
					break;
				default:
					$icon_alignment = $font_size_base;

			}
			$icon_alignment = $icon_alignment * 2.35;

			$icon_custom_style .= ' width: ' . $icon_alignment . 'px;';
			$icon_custom_style .= ' height: ' . $icon_alignment . 'px;';
			$icon_custom_style .= ' line-height: ' . $icon_alignment . 'px;';
			$custom_style .= ( $bg_color != '' ) ? ' background-color: ' . esc_attr( $bg_color ) . ';' : '';
			$hover_custom_style .= ( $hover_bg_color != '' ) ? ' background-color: ' . esc_attr( $hover_bg_color ) . ';' : '';
			$custom_style .= ( $border_color != '' ) ? ' border: 1px solid ' . esc_attr( $border_color ) . ';' : '';
			$hover_custom_style .= ( $hover_border_color != '' ) ? ' border: 1px solid ' . esc_attr( $hover_border_color ) . ';' : '';
		}

		$class = ( $class != '' ) ? 'class="' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';
		$icon_custom_style = ( $icon_custom_style != '' ) ? 'style="' . $icon_custom_style . '"' : '';

		if( $hover_custom_style != '' ) {
			$html[] = "<style scoped>#{$id} i:hover { {$hover_custom_style} }</style>";
		}
		$html[] = "<span id=\"{$id}\" {$class} {$custom_style}>";
		if( $style == 'stack_filled' ) {
			$html[] = "<i class=\"fa fa-{$shape} fa-stack-2x\"></i>";
		} elseif( $style == 'stack_bordered' ) {
			$html[] = "<i class=\"fa fa-{$shape}-o fa-stack-2x\"></i>";
		}
		$html[] = "<i class=\"fa {$icon}\" {$icon_custom_style}></i>";
		$html[] = '</span>';

		return implode( "\n", $html );
	}
}
add_shortcode('icon', 'noo_shortcode_icon');

// [social_icon]
// ============================
if( !function_exists( 'noo_shortcode_social_icon' ) ) {
	function noo_shortcode_social_icon( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'icon'                 => '',
			'href'                 => '',
			'target'               => '',
			'size'                 => '',
			'custom_size'          => '',
			'icon_color'           => '',
			'hover_icon_color'     => '',
			'shape'                => '',
			'style'                => '',
			'bg_color'             => '',
			'hover_bg_color'       => '',
			'border_color'         => '',
			'hover_border_color'   => '',
			"visibility"           => '',
			'class'                => '',
			'id'                   => '',
			'custom_style'         => ''
		), $atts));

		if( $icon == '' ) {
			return '';
		}
 
		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class            = ( $class           != ''     ) ? 'noo-icon ' . esc_attr( $class ) : 'noo-icon';
		$class           .= noo_visibility_class( $visibility );

		$html = array();

		$id     = ( $id    != '' ) ? esc_attr( $id ) : 'noo-icon-' . noo_vc_elements_id_increment();
		$custom_style  = ( $custom_style != '' ) ? esc_attr( $custom_style ) : '';
		$hover_custom_style  = '';

		$class .= ( $shape != '' ) ? ' icon-' . $shape : '';
		$class .= ( $style == 'stack_filled' || $style == 'stack_bordered' ) ? ' fa-stack' : '';
		$class .= ( $size != '' && $size != 'custom' ) ? ' fa-' . $size : '';

		$icon  .= ( $style == 'stack_filled' || $style == 'stack_bordered' ) ? ' fa-stack-1x' : '';
		$icon  .= ( $style == 'stack_filled' ) ? ' fa-inverse' : '';

		$custom_style .= ( $size == 'custom' && $custom_size != '' ) ? ' font-size: ' . $custom_size . 'px;' : '';
		$custom_style .= ( $icon_color != '' ) ? ' color: ' . esc_attr( $icon_color ) . ';' : '';
		$hover_custom_style .= ( $hover_icon_color != '' ) ? ' color: ' . esc_attr( $hover_icon_color ) . ';' : '';

		if( $style == 'custom' ) {
			$custom_style .= ( $bg_color != '' ) ? ' background-color: ' . esc_attr( $bg_color ) . ';' : '';
			$hover_custom_style .= ( $hover_bg_color != '' ) ? ' background-color: ' . esc_attr( $hover_bg_color ) . ';' : '';
			$custom_style .= ( $border_color != '' ) ? ' border: 1px solid ' . esc_attr( $border_color ) . ';' : '';
			$hover_custom_style .= ( $hover_border_color != '' ) ? ' border: 1px solid ' . esc_attr( $hover_border_color ) . ';' : '';
		}

		$class = ( $class != '' ) ? 'class="' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';

		if( $hover_custom_style != '' ) {
			$html[] = "<style scoped>#{$id} i:hover { {$hover_custom_style} }</style>";
		}
		$html[] = "<span id=\"{$id}\" {$class} {$custom_style}>";
		if( $href != '' ) {
			$target = ( $target == true ) ? 'target="_BLANK"' : '';
			$html[] = "  <a href=\"{$href}\" {$target} >";
		}
		if( $style == 'stack_filled' ) {
			$html[] = "    <i class=\"fa fa-{$shape} fa-stack-2x\"></i>";
		} elseif( $style == 'stack_bordered' ) {
			$html[] = "    <i class=\"fa fa-{$shape}-o fa-stack-2x\"></i>";
		}
		$html[] = "    <i class=\"fa {$icon}\"></i>";
		if( $href != '' ) {
			$html[] = "  </a>";
		}
		$html[] = '</span>';

		return implode( "\n", $html );
	}
}
add_shortcode('social_icon', 'noo_shortcode_social_icon');

// [icon_list]
// ============================
if( !function_exists( 'noo_shortcode_icon_list' ) ) {
	function noo_shortcode_icon_list( $atts, $content = null ) {
		extract( shortcode_atts( array(
			"visibility"           => '',
			'class'                => '',
			'id'                   => '',
			'custom_style'         => ''
		), $atts));

		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class            = ( $class           != ''     ) ? 'noo-ul-icon fa-ul ' . esc_attr( $class ) : 'noo-ul-icon fa-ul';
		$class           .= noo_visibility_class( $visibility );

		$html = array();

		$id     = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . esc_attr( $custom_style ) . '"' : '';

		$html[] = "<ul {$id} {$class} {$custom_style}>";
		$html[] = do_shortcode( $content );
		$html[] = '</ul>';

		return implode( "\n", $html );
	}
}
add_shortcode('icon_list', 'noo_shortcode_icon_list');

// [icon_list_item]
// ============================
if( !function_exists( 'noo_shortcode_icon_list_item' ) ) {
	function noo_shortcode_icon_list_item( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'icon'                 => '',
			'icon_size'            => '',
			'icon_color'           => '',
			'text_same_size'       => '',
			'text_size'            => '',
			'text_same_color'      => '',
			'text_color'           => '',
			'class'                => '',
			'id'                   => '',
			'custom_style'         => ''
		), $atts));

		$class         = ( $class        != ''     ) ? 'noo-li-icon ' . esc_attr( $class ) : 'noo-li-icon';
		$icon_class    = ( $icon         != ''     ) ? 'fa-li fa ' . esc_attr( $icon ) : '';
		$custom_style  = ( $custom_style != '' ) ? esc_attr( $custom_style ) : '';
		$icon_style    = '';

		if( $text_same_size == 'true' ) {
			$custom_style .= ( $icon_size != '' ) ? ' font-size: ' . $icon_size . 'px;' : '';
		} else {
			$custom_style .= ( $text_size != '' ) ? ' font-size: ' . $text_size . 'px;' : '';
			$icon_style   .= ( $icon_size != '' ) ? ' font-size: ' . $icon_size . 'px;' : '';
		}

		if( $text_same_color == 'true' ) {
			$custom_style .= ( $icon_color != '' ) ? ' color: ' . $icon_color . ';' : '';
		} else {
			$custom_style .= ( $text_color != '' ) ? ' color: ' . $text_color . ';' : '';
			$icon_style   .= ( $icon_color != '' ) ? ' color: ' . $icon_color . ';' : '';
		}

		$html = array();

		$id           = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class        = ( $class != '' ) ? 'class="' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';
		$icon_class   = ( $icon_class != '' ) ? 'class="' . esc_attr( $icon_class ) . '"' : '';
		$icon_style   = ( $icon_style != '' ) ? 'style="' . $icon_style . '"' : '';
		
		$html[] = "<li {$id} {$class} {$custom_style}><i {$icon_class} {$icon_style}></i>".noo_handler_shortcode_content( $content, false ).'</li>';

		return implode( "\n", $html );
	}
}
add_shortcode('icon_list_item', 'noo_shortcode_icon_list_item');

// [label]
// ============================
if( !function_exists( 'noo_shortcode_label' ) ) {
	function noo_shortcode_label( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'word'            => '...',
			'color'           => 'default',
			'custom_color'    => '',
			'rounded'         => 'false',
			'class'           => '',
			'id'              => '',
			'custom_style'    => ''
		), $atts));

		$html = array();

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'label ' . esc_attr( $class ) : 'label';

		$custom_style .= ( $color != '' ) ? ' color: ' . esc_attr( $color ) . ';' : '';
		if( $color != 'custom' ) {
			$class .= ' label-' . $color;
		} else {

			$custom_style .= ( $custom_color != '' ) ? ' background-color: ' . esc_attr( $custom_color ) . ';' : '';
		}

		if( $rounded === 'false' ) {
			$custom_style .= ' border-radius: 0;';
		}

		$class = ( $class != '' ) ? 'class=" ' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';

		$html = "<span {$id} {$class} {$custom_style}>{$word}</span>";

		return $html;
	}
}
add_shortcode('label', 'noo_shortcode_label');

// [code]
// ============================
if( !function_exists( 'noo_shortcode_code' ) ) {
	function noo_shortcode_code( $atts, $content = null ) {
		extract( shortcode_atts( array(
			"visibility"           => '',
			'class'                => '',
			'id'                   => '',
			'custom_style'         => ''
		), $atts));

		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class            = ( $class           != ''     ) ? esc_attr( $class ) : '';
		$class           .= noo_visibility_class( $visibility );

		$html = array();

		$id     = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . esc_attr( $custom_style ) . '"' : '';

		$html[] = "<pre><code {$id} {$class} {$custom_style}>";
		$html[] = $content;
		$html[] = '</code></pre>';

		return implode( "\n", $html );
	}
}
add_shortcode('code', 'noo_shortcode_code');

// [block_grid]
// ============================
if( !function_exists( 'noo_shortcode_block_grid' ) ) {
	function noo_shortcode_block_grid( $atts, $content = null ) {
		extract( shortcode_atts( array(
			"columns"              => "3",
			"visibility"           => "",
			"class"                => "",
			"id"                   => "",
			"custom_style"         => ""
		), $atts));

		$class            = ( $class           != ''     ) ? 'noo-block-grid ' . esc_attr( $class ) : 'noo-block-grid';
		switch( $columns ) {
			case '1':
				$class .= ' one-col';
				break;
			case '2':
				$class .= ' two-col';
				break;
			case '3':
				$class .= ' three-col';
				break;
			case '4':
				$class .= ' four-col';
				break;
			case '5':
				$class .= ' five-col';
				break;
			case '6':
				$class .= ' six-col';
				break;
			default:
				$class .= ' three-col';
				break;
		}

		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class           .= noo_visibility_class( $visibility );

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';

		$html = "<ul {$id} {$class} {$custom_style}>" . do_shortcode( $content ) . "</ul>";

		return $html;
	}
}
add_shortcode('block_grid', 'noo_shortcode_block_grid');

// [block_grid_item]
// ============================
if( !function_exists( 'noo_shortcode_block_grid_item' ) ) {
	function noo_shortcode_block_grid_item( $atts, $content = null ) {
		extract( shortcode_atts( array(
			"visibility"           => "",
			"class"                => "",
			"id"                   => "",
			"custom_style"         => ""
		), $atts));

		$class            = ( $class           != ''     ) ? 'noo-block-grid ' . esc_attr( $class ) : 'noo-block-grid-item';
		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class           .= noo_visibility_class( $visibility );

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';

		$html = "<li {$id} {$class} {$custom_style}>" . do_shortcode( $content ) . "</li>";

		return $html;
	}
}
add_shortcode('block_grid_item', 'noo_shortcode_block_grid_item');

// [progress_bar]
// ============================
if( !function_exists( 'noo_shortcode_progress_bar' ) ) {
	function noo_shortcode_progress_bar( $atts, $content = null ) {
		extract( shortcode_atts( array(
			"title"                => "",
			"style"                => "",
			"rounded"              => "",
			"visibility"           => "",
			"class"                => "",
			"id"                   => "",
			"custom_style"         => ""
		), $atts));

		wp_enqueue_script( 'vendor-countTo' );

		$class        = ( $class    != ''     ) ? 'noo-progress-bar ' . esc_attr( $class ) : 'noo-progress-bar';
		$class       .= ( $style    != ''     ) ? ' ' . $style . '-bars' : '';
		$class       .= ( $rounded  == 'true' ) ? ' rounded-bars' : '';

		$visibility       = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class           .= noo_visibility_class( $visibility );

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';

		$html = "<div {$id} {$class} {$custom_style}>" . do_shortcode( $content ) . "</div>";

		return $html;
	}
}
add_shortcode('progress_bar', 'noo_shortcode_progress_bar');

// [progress_bar_item]
// ============================
if( !function_exists( 'noo_shortcode_progress_bar_item' ) ) {
	function noo_shortcode_progress_bar_item( $atts, $content = null ) {
		extract( shortcode_atts( array(
			"title"                => "",
			"progress"             => "50",
			"color"                => "primary",
			"color_effect"         => "",
			"class"                => "",
			"id"                   => "",
			"custom_style"         => ""
		), $atts));

		$class            = ( $class           != ''     ) ? 'progress ' . esc_attr( $class ) : 'progress';
		$class           .= ( $color_effect    != ''     ) ? ' progress-striped' : '';
		$class           .= ( $color_effect    == 'striped_animation' ) ? ' active' : '';

		$bar_class        = ( $color           != ''     ) ? 'progress-bar progress-bar-' . $color : 'progress-bar';

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$bar_class = ( $bar_class != '' ) ? 'class="' . $bar_class . '"' : '';
		$custom_style  = ( $custom_style != '' ) ? 'style="' . esc_attr( $custom_style ) . '"' : '';

		$html = array();
		$html[] = "<div {$class}>";
		$html[] = "  <div {$id} {$bar_class} {$custom_style} role=\"progressbar\" data-valuenow=\"{$progress}\" aria-valuenow=\"{$progress}\" aria-valuemin=\"0\" aria-valuemax=\"100\" >";
		if( $title != '' ) {
			$html[] = '  <div class="progress_title">' . esc_attr( $title ) . '</div>';
		}
		$html[] = "    <div class=\"progress_label\"><span>{$progress}</span>%</div>";
		$html[] = "  </div>";
		$html[] = "</div>";

		return implode( "\n", $html );
	}
}
add_shortcode('progress_bar_item', 'noo_shortcode_progress_bar_item');

// [pricing_table]
// ============================
if( !function_exists( 'noo_shortcode_pricing_table' ) ) {
	function noo_shortcode_pricing_table( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'title'             => '',
			'columns'           => '3',
			'style'             => 'classic',
			'visibility'        => '',
			'class'             => '',
			'id'                => '',
			'custom_style'      => ''
		), $atts));

		$class     = ( $class    != ''     ) ? 'noo-pricing-table ' . esc_attr( $class ) : 'noo-pricing-table';
		$class    .= ( $style != '' ) ? ' ' . $style : '';
		switch( $columns ) {
			case '1':
				$class .= ' one-col';
				break;
			case '2':
				$class .= ' two-col';
				break;
			case '3':
				$class .= ' three-col';
				break;
			case '4':
				$class .= ' four-col';
				break;
			case '5':
				$class .= ' five-col';
				break;
			case '6':
				$class .= ' six-col';
				break;
			default:
				$class .= ' three-col';
				break;
		}

		$visibility       = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class           .= noo_visibility_class( $visibility );

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';

		$html = "<div {$id} {$class} {$custom_style}>" . do_shortcode( $content ) . "</div>";

		return $html;
	}
}
add_shortcode('pricing_table', 'noo_shortcode_pricing_table');

// [pricing_table_column]
// ============================
if( !function_exists( 'noo_shortcode_pricing_table_column' ) ) {
	function noo_shortcode_pricing_table_column( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'title'                => '',
			'featured'             => 'false',
			'price'                => '',
			'symbol'               => '$',
			'before_price'         => '',
			'after_price'          => '',
			'purchase_btn_type'	   => 'default',
			'healcode_btn_code'    => '',
		    'button_text'          => 'Purchase',
		    'size'                 => '',
		    'href'                 => '#',
		    'target'               => '',
		    'button_shape'         => '',
		    'button_style'         => '',
		    'skin'                 => '',
		    'text_color'           => '',
		    'hover_text_color'     => '',
		    'bg_color'             => '',
		    'hover_bg_color'       => '',
		    'border_color'         => '',
		    'hover_border_color'   => '',
			'visibility'           => '',
			'class'                => '',
			'id'                   => '',
			'custom_style'         => ''
		), $atts));

		$class            = ( $class           != ''     ) ? 'noo-pricing-column ' . esc_attr( $class ) : 'noo-pricing-column';
		$class           .= ( $featured        == 'true' ) ? ' featured' : '';

		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class           .= noo_visibility_class( $visibility );

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : 'id="noo-pricing-column-' . noo_vc_elements_id_increment() . '"';
		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style  = ( $custom_style != '' ) ? esc_attr( $custom_style ) : '';
		$custom_style  = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';
		$price = ( $symbol       != '' ) ? '<i class="currency">' . esc_attr( $symbol ) . '</i>' . esc_attr( $price ) : esc_attr( $price );
		$price = '<span class="noo-price"><span class="amount">' . $price  . '</span></span>';
		$price = ( $before_price != '' ) ? esc_attr( $before_price ) . ' ' . $price : $price;
		$price = ( $after_price  != '' ) ? $price . ' ' . esc_attr( $after_price ) : $price;
		$href         = ( $href != '' ) ? 'href="' . $href . '"' : 'href="#"';
		$target       = ( $target == 'true' ) ? 'target="_BLANK"' : '';
		$btn_class    = 'btn';
		switch($size) {
		    case 'x_small':
		        $btn_class .= ' btn-xs';
		        break;
		    case 'small':
		        $btn_class .= ' btn-sm';
		        break;
		    case 'large':
		        $btn_class .= ' btn-lg';
		        break;
		}
		$btn_class .=  ($button_style != '' ) ? ' ' . $button_style : '';

		if($skin != '' && $skin != 'custom') {
		    $btn_class .= " btn-{$skin}";
		} else {
		    $btn_class .= ' btn-default';
		}
		$btn_class    = ( $btn_class != '' ) ? 'class="' . $btn_class . '"' : '';

		$btn_custom_style       = '';
		$hover_btn_custom_style = '';
		if( $skin == 'custom' ) {
		    $btn_custom_style       .= ( $text_color != '' ) ? ' color: ' . esc_attr( $text_color ) . ';' : '';
		    $hover_btn_custom_style .= ( $hover_text_color != '' ) ? ' color: ' . esc_attr( $hover_text_color ) . ';' : '';
		    $btn_custom_style       .= ( $bg_color != '' ) ? ' background-color: ' . esc_attr( $bg_color ) . ';' : '';
		    $hover_btn_custom_style .= ( $hover_bg_color != '' ) ? ' background-color: ' . esc_attr( $hover_bg_color ) . ';' : '';
		    $btn_custom_style       .= ( $border_color != '' ) ? ' border: 1px solid ' . esc_attr( $border_color ) . ';' : '';
		    $hover_btn_custom_style .= ( $hover_border_color != '' ) ? ' border: 1px solid ' . esc_attr( $hover_border_color ) . ';' : '';
		}

		$btn_custom_style  = ($btn_custom_style != '' ) ? 'style="' . $btn_custom_style . '"' : '';

		$html   = array();
		if( $hover_btn_custom_style != '' ) {
		    $html[] = "<style scoped>#{$id} .pricing-footer > a.btn:hover { {$hover_btn_custom_style} }</style>";
		}

		$html[] = "<div {$id} {$class} {$custom_style} >";
		$html[] = '  <div class="pricing-content" >';
		$html[] = '    <div class="pricing-header" >';
		$html[] = "      <h2 class=\"pricing-title\">{$title}</h2>";
		$html[] = "    <span class=\"arrow\">&nbsp;</span>";
		$html[] = "    </div>";
		$html[] = '    <div class="pricing-price" >';
		$html[] = "    <h3 class=\"pricing-value\">{$price}</h3>";
		$html[] = "    </div>";
		$html[] = '    <div class="pricing-info">';
		$html[] = do_shortcode( $content );
		$html[] = "    </div>";
		$html[] = '    <div class="pricing-footer" >';
		if($purchase_btn_type == 'default'){
			$html[] = "      <a {$href} {$target} {$btn_class} {$btn_custom_style} role=\"button\">" . esc_attr( $button_text ) . '</a>';
		}elseif ($purchase_btn_type == 'healcode') {
			
			$healcode_btn_code = preg_match( '/^#E\-8_/', $healcode_btn_code ) ? rawurldecode( base64_decode( preg_replace( '/^#E\-8_/', '', $healcode_btn_code ) ) ) : $healcode_btn_code;
			$html[] = $healcode_btn_code;
		}
		$html[] = "    </div>";
		$html[] = "  </div>";
		$html[] = '</div>';

		return implode( "\n", $html );
	}
}
add_shortcode('pricing_table_column', 'noo_shortcode_pricing_table_column');

// [counter]
// ============================
if( !function_exists( 'noo_shortcode_counter' ) ) {
	function noo_shortcode_counter ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			"visibility"        => "",
			"class"             => "",
			"id"                => "",
			"custom_style"      => "",
			'number'            => '',
			'size'              => '50',
			'color'             => '',
			'alignment'         => 'center',
			), $atts ) );

		if( empty($number) ) {
			return '';
		}

		wp_enqueue_script( 'vendor-countTo' );

		$class            = ( $class           != ''     ) ? esc_attr( $class ) : '';
		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class           .= noo_visibility_class( $visibility );

		$class .= ( $alignment != '' ) ? ' text-' . $alignment : '';

		$html  = array();

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="noo-counter-holder ' . $class . '"' : 'class="noo-counter-holder"';
		$counter_style  = '';
		$counter_style .= ( $size  != '' ) ? ' font-size: ' . $size . 'px;' : '';
		$counter_style .= ( $color != '' ) ? ' color: ' . $color . ';' : '';
		$counter_style  = ( $counter_style != '' ) ? 'style="' . $counter_style . '"' : '';

		$custom_style   = ( $custom_style  != '' ) ? 'style="' . $custom_style . '"' : '';

		$html[] = "<div {$class} {$custom_style}>";
        $html[] = "  <span class=\"noo-counter\" {$counter_style}>" . $number . '</span>';
        if( !empty( $content ) ) {
        	$html[] = '<div class="counter-text">' . noo_handler_shortcode_content( $content, true ) . '</div>';
        }
        $html[] = '</div>';

		return implode( "\n", $html );
	}
}

add_shortcode( 'counter', 'noo_shortcode_counter' );

if(!function_exists('noo_recent_news_shortcode')){
	function noo_recent_news_shortcode($atts, $content = null){
		extract( shortcode_atts( array(
			'title'				=> '',
			'columns'			=> '3',
			'posts_per_page'	=> '3',
			'excerpt_length'	=> '20',
			'visibility'        => '',
			'class'             => '',
			'custom_style'      => ''
		), $atts ) );
	
		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class            = ( $class           != ''     ) ? 'noo-recent-news ' . esc_attr( $class ) : 'noo-recent-news ';
		$class           .= noo_visibility_class( $visibility );
		$class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';
	
		$html = '';
		$r = new WP_Query(array(
			'posts_per_page'      => $posts_per_page,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'meta_key' 			  => "_thumbnail_id",
		) );
		ob_start();
		if($r->have_posts()):
		?>
		<div <?php echo ( $class . ' ' . $custom_style);?> >
			
			<?php if(!empty($title)): ?>
				<h3 class="custom-title-home-center"><?php echo noo_nth_word($title,'first', false);?></h3>
			<?php endif;?>
			<div class="noo-rn-content">
				<?php 
				$i = 0;
				?>
				<?php while ($r->have_posts()): $r->the_post(); global $post;?>
					<?php if($i++ % $columns == 0):?>
					<div class="row">
					<?php endif;?>
					<div class="noo-rn-item loop-item col-sm-6 col-md-<?php echo absint((12 / $columns)) ?>">
						<div class="item-wrap">
							<?php if(has_post_thumbnail()):?>
						    <div class="item-featured">
						        <a href="<?php the_permalink() ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>">
									<?php the_post_thumbnail('noo-thumbnail-square')?>
								</a>
								<span class="icon">
									<?php 
										$icon = '';
										if(get_post_format() =='video')
											$icon= '<i class="fa fa-file-video-o"></i>';
										elseif (get_post_format() == 'audio')
											$icon = '<i class="fa fa-file-audio-o"></i>';
										elseif (get_post_format() == 'gallery')
											$icon= '<i class="fa fa-th-large"></i>';
										elseif (get_post_format() == 'image')
											$icon= '<i class="fa fa-file-image-o"></i>';
										else
											$icon = '<i class="fa fa-file-image-o"></i>';
											echo ($icon);
									?>
								</span>
								
								
						    </div>
						    <?php endif;?>
							<div class="item-content">
								<h4 class="item-title">
									<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
								</h4>
								<div class="item-date">
									<span><?php echo get_the_date()?></span>
								</div>
								<div class="item-excerpt">
									<?php
									$excerpt = $post->post_excerpt;
									if(empty($excerpt))
										$excerpt = $post->post_content;
									$excerpt = strip_shortcodes($excerpt);
									echo '<p>' . wp_trim_words($excerpt,$excerpt_length,'...') . '</p>';
									?>
								</div>
								<a class="read-more" href="<?php the_permalink(); ?>" title="<?php _e( 'Read more', 'noo' ); ?>">
									<i class="fa fa-chevron-circle-right">&nbsp;</i>
									<?php _e( 'Read more', 'noo' ); ?>
								</a>
							</div>
						</div>
					</div>
					<?php if($i % $columns == 0 || $i == $r->post_count):?>
					</div>
					<?php endif;?>
				<?php endwhile;?>
			</div>
		</div>
		<?php
		endif;
		$html .= ob_get_clean();
		wp_reset_query();
		return $html;
	}
	add_shortcode('noo_recent_news', 'noo_recent_news_shortcode');
}

if(!function_exists('noo_recent_class_shortcode')){
    function noo_recent_class_shortcode($atts, $content = null){

    	if ( ! class_exists('Noo__Timetable__Class') ) return;

        extract( shortcode_atts( array(
            'title'				=> '',
            'columns'			=> '3',
            'posts_per_page'	=> '3',
            'orderby'           =>  'latest',
            'layout_style'      => 'default',
           	'show_info'         => 'all',
           	'categories'        => 'all',
           	'trainer'           => '0',
            'visibility'        => '',
            'class'             => '',
            'custom_style'      => ''
        ), $atts ) );

        $visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
        $class            = ( $class           != ''     ) ? 'noo-recent-news ' . esc_attr( $class ) : 'noo-recent-class '.'col-'.$columns;
        $class           .= noo_visibility_class( $visibility );
        $class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
        $custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';

        $html = '';

        $trainer = explode(",", $trainer);

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
	        default:
	            $orderby = 'date';
	            break;
	    }

        $args = array(
            'posts_per_page'      => $posts_per_page,
            'no_found_rows'       => true,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'post_type'           => 'noo_class',
            'orderby'             => $orderby,
            'order'               => $order,
        );
        $arr = array( 'relation' => 'OR' );
        foreach ($trainer as $value) {
        	$arr[] = array(
        		'key' => '_trainer', 
        		'value' => '"'.$value.'"',
        		'compare' => 'LIKE'
    		);
        }

        if ( $trainer > 0 && in_array('0', $trainer) == false ) {
        	$args['meta_query'] = array(
        		$arr
			);
        }

        if ( !empty( $categories ) && $categories != 'all' ) {
	        $args['tax_query'][]  = array(
	            'taxonomy' =>  'class_category',
	            'field'    =>  'id',
	            'terms'    =>   array($categories)
	        );
        }

        $r = new WP_Query( $args );

        ob_start();
        if($r->have_posts()):
            ?>
            <div <?php echo ( $class . ' ' . $custom_style);?> >

                <?php if(!empty($title)): ?>
                    <h3 class="custom-title-home-center"><?php echo noo_nth_word($title,'first', false);?></h3>
                <?php endif;?>
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
                    <?php while ($r->have_posts()): $r->the_post(); global $post;?>

                    	<?php
					        $class_address		= noo_get_post_meta( get_the_ID(), '_address', '' );
					        $open_date		    = noo_get_post_meta( get_the_ID(), '_open_date', '' );
					        $open_time		    = noo_get_post_meta( get_the_ID(), '_open_time', '' );
					        $close_time		    = noo_get_post_meta( get_the_ID(), '_close_time', '' );
					        $class_trainer      = noo_get_post_meta( get_the_ID(), '_trainer', '' );
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
		                                        	<?php if( !empty( $open_date) && $show_date) : ?>
									                    <span><i class="fa fa-calendar">&nbsp;</i><?php echo esc_html(date_i18n(get_option('date_format'),$open_date)); ?></span>
									                <?php endif; ?>
									                <?php if( ( !empty( $open_time) || !empty( $close_time) )  && $show_time ) : ?>
									                    <span><i class="fa fa-clock-o">&nbsp;</i><?php echo date_i18n(get_option('time_format'),$open_time).'-'. date_i18n(get_option('time_format'),$close_time); ?></span>
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
                                    				if( !empty( $open_date) && $show_date)
                                						$datetime_arr[] = esc_html(date_i18n(get_option('date_format'),$open_date));
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

                    <?php endwhile;?>
                </div>
            </div>
        <?php
        endif;
        $html .= ob_get_clean();
        wp_reset_query();
        return $html;
    }
    add_shortcode('noo_recent_class', 'noo_recent_class_shortcode');
}

if(!function_exists('noo_coming_class_shortcode')){
    function noo_coming_class_shortcode($atts, $content = null){

    	if ( ! class_exists('Noo__Timetable__Class') ) return;

        extract( shortcode_atts( array(
            'title'				=> '',
            'columns'			=> '3',
            'posts_per_page'	=> '3',
            'orderby'           =>  'latest',
            'layout_style'      => 'default',
           	'show_info'         => 'all',
           	'categories'        => 'all',
           	'trainer'           => '0',
            'visibility'        => '',
            'class'             => '',
            'custom_style'      => ''
        ), $atts ) );

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
            'posts_per_page'      => $posts_per_page,
            'no_found_rows'       => true,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'post_type'           => 'noo_class',
            'orderby'             => 'meta_value_num',
            'order'               => 'ASC',
            'meta_key'            => '_next_date',
            'post__in'		      => $comming_class_ids
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

                <?php if(!empty($title)): ?>
                    <h3 class="custom-title-home-center"><?php echo noo_nth_word($title,'first', false);?></h3>
                <?php endif;?>
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
					        $class_address		= noo_get_post_meta( get_the_ID(), '_address', '' );
					        $open_date		    = noo_get_post_meta( get_the_ID(), '_open_date', '' );
					        $open_time		    = noo_get_post_meta( get_the_ID(), '_open_time', '' );
					        $close_time		    = noo_get_post_meta( get_the_ID(), '_close_time', '' );
					        $class_trainer      = noo_get_post_meta( get_the_ID(), '_trainer', '' );

					        $next_date		    = noo_get_post_meta( get_the_ID(), '_next_date', '' );
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
							                    <span><?php _e('By ', 'noo'); ?><em><?php echo get_the_title($class_trainer); ?></em></span>
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
    add_shortcode('noo_coming_class', 'noo_coming_class_shortcode');
}

// Shortcode Event
//=============================

if ( !function_exists( 'noo_event_shortcode' )  ) {
	function noo_event_shortcode($atts, $content = null) {

		if ( ! class_exists('Noo__Timetable__Event') ) return;

		extract( shortcode_atts( array(
			'choose_style'         => 'grid',
			'columns'              => '1',
			'categories'           => 'all',
			'locations'            => 'all',
			'orderby'              => 'latest',
			'hide_past_events'     => false,
			'hide_events_time'     => false,
			'hide_events_date'     => false,
			'hide_events_location' => false,
			'posts_per_page'       => 3,
			'show_pagination'      => 'yes',
			'visibility'           => '',
			'class'                => '',
			'custom_style'         => '',
			'excerpt_length'       => 30
        ), $atts ) );

        $visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
        $class            = ( $class           != ''     ) ? 'noo-event ' . esc_attr( $class ) : 'noo-event '.'col-'.$columns;
        $class           .= noo_visibility_class( $visibility );
        $class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
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
				$meta_key = '_start_date';
				break;
			case 'startdate_asc':
				$orderby = 'meta_value_num';
				$meta_key = '_start_date';
				$order = 'ASC';
				break;
			default:
				$orderby = 'date';
				break;
		}

        $args = array(
        	'paged' => $paged,
			'orderby'             => $orderby,
			'order'               => $order,
			'posts_per_page'      => $posts_per_page,
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
        
        if ( $locations != 'all' ) {
        	$locations = explode(',', $locations);
	        $args['tax_query'][]  = array(
	            'taxonomy' =>  'event_location',
	            'field'    =>  'term_id',
	            'terms'    =>   $locations
	        );
        }
        
        $wp_query = new WP_Query( $args );
        
        wp_enqueue_script('vendor-isotope');
		
		ob_start();
		$html = '';
		// Load display style
		$choose_style_class = '';
		if ( $choose_style == 'list' ) {
			$post_class = 'loadmore-item masonry-item col-md-12';
			$choose_style_class = 'col-sm-6';
		} else {
			$post_class = 'loadmore-item masonry-item col-sm-6 col-md-'.absint((12 / $columns));
		}
		
		?>
		<div class="posts-loop masonry  noo-events">
			<div class="row posts-loop-content loadmore-wrap">
				<div class="masonry-container">
					<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); global $post; ?>
					<article <?php  post_class($post_class); ?>>
						<?php $is_featured = get_post_meta( $post->ID, 'event_is_featured', true ); ?>
						<div class="loop-item-wrap">
							<?php if(has_post_thumbnail()):?>
						    <div class="loop-item-featured <?php echo $choose_style_class ?>">
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
							<div class="loop-item-content <?php echo $choose_style_class ?>">
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
									
									$event_address		= noo_get_post_meta( get_the_ID(), "_address", '' );
									$event_startdate	= Noo_Event::get_start_date( get_the_ID(), false );
									?>
									<div class="event-info">
										<?php if( !empty( $event_startdate ) && !$hide_events_time ) : ?>
											<div>
												<i class="fa fa-clock-o"></i>&nbsp;<?php echo date_i18n(get_option('time_format'),$event_startdate); ?>
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
	add_shortcode('noo_event_listing', 'noo_event_shortcode');
}

// Shortcode Video
//=============================
if(!function_exists('noo_shortcode_video_slider')) {
    function noo_shortcode_video_slider($atts, $content = null){
        extract( shortcode_atts( array(
            'title'					=>'',
            'archive_link'			=>'no',
            'posts_per_page'		=> '3',
            'excerpt_length'		=> '15',
            'visibility'         	=> '',
            'class'              	=> '',
            'id'                 	=> '',
            'custom_style'       	=> '',
        ), $atts ) );

        $class            = ( $class              != '' ) ? esc_attr( $class ) : '' ;
        $visibility       = ( $visibility         != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
        switch ($visibility) {
            case 'hidden-phone':
                $class .= ' hidden-xs';
                break;
            case 'hidden-tablet':
                $class .= ' hidden-sm hidden-md';
                break;
            case 'hidden-pc':
                $class .= ' hidden-lg';
                break;
            case 'visible-phone':
                $class .= ' visible-xs-block visible-xs-inline visible-xs-inline-block';
                break;
            case 'visible-tablet':
                $class .= ' visible-sm-block visible-sm-inline visible-sm-inline-block visible-md-block visible-md-inline visible-md-inline-block';
                break;
            case 'visible-phone':
                $class .= ' visible-lg-block visible-lg-inline visible-lg-inline-block';
                break;
        }
        if( is_front_page() || is_home()) {
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
        } else {
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        }

        $args = array(
            'paged'			  	  => $paged,
            'orderby'         	  => "date",
            'order'           	  => "DESC",
            'posts_per_page'      => $posts_per_page,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
        );
        $args['tax_query'][]= array(
            'taxonomy' => 'post_format',
            'field'    => 'slug',
            'terms'    => 'post-format-video'
        );

        $tax_query = array();

        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }
        $video_id = uniqid('videos-');
        $r = new WP_Query($args);
        ob_start();
        if($r->have_posts()):
            $i = 0;
        ?>
            <?php
            $slider_thumb = '';
            $thumb_video_id = uniqid('thumb-video-');
            $slider_thumb .= '<div class="loop-thumb">';
            $slider_thumb .= '<div class="loop-thumb-wrap">';
            $slider_thumb .='<div class="loop-thumb-content">';
            $slider_thumb .='<div id="'.$thumb_video_id.'" class="carousel slide fade">';
            $slider_thumb .='<div class="carousel-inner" role="listbox">';
            $indicators = '';
            $j = $k = $i = 0;
            while ( $r->have_posts() ) : $r->the_post(); global $post;
                $active = '';
                // if($k == 0)
                $active =' active';
                // if($j++ % 3 == 0){
                $slider_thumb .= '<div class="item '.$active.'">';
                    // $indicators .= '<li data-target="#'.$thumb_video_id.'" data-slide-to="'.$k.'" class="'.$active.'"></li>';
                //     $k++;
                // }
                $slider_thumb .='<a href="#" data-target="#'.$video_id.'" data-slide-to="'.$i.'">';
                $slider_thumb .= '<span class="icon"><i class="fa fa-film"></i></span>';
                $slider_thumb .= get_the_title();
                $slider_thumb .= '<span class="thumbnail-content">'.wp_trim_words(get_the_excerpt() , 8).'</span>';
                $slider_thumb .='</a>';
                if($j % 3 == 0 || $j == $r->post_count){
                    $slider_thumb .= '</div>';
                }
                $i++;
            endwhile;
            $slider_thumb .='</div>';
            // $slider_thumb .='<ol class="carousel-indicators">'.$indicators.'</ol>';
            $slider_thumb .='</div>';
            $slider_thumb .='</div>';
            $slider_thumb .='</div>';
            $slider_thumb .='</div>';
            ?>
            <div class="videos posts-loop slider">
                <div class="posts-loop-content row">
                    <div class="col-sm-6">
                        <?php echo $slider_thumb;?>
                    </div>
                    <div id="<?php echo esc_attr($video_id)?>" class="video carousel slide fade <?php echo esc_attr($video_id)?> col-md-6 col-sm-6">
                        <div class="carousel-inner" role="listbox">
                        	<?php
                        		$yt_pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=))([\w-]{10,12})/';
								$vimeo_pattern = '#https?://(.+\.)?vimeo\.com/.*#';
                        	?>
                            <?php $i=0; ?>
                            <?php while ( $r->have_posts() ) : $r->the_post(); global $post; $i++ ?>
                                <?php
                                $active = '';
                                if($i == 1)
                                    $active .=' active';
                                // Get embed & check data-type
                                $data_type = '';
                                $is_vimeo = $is_youtube = false;
                                $gEmbed = noo_get_post_meta( get_the_ID(), "_noo_wp_post_video_embed", '' );
                                $is_vimeo = ( preg_match( $vimeo_pattern, $gEmbed ) );
								$is_youtube = (  preg_match( $yt_pattern, $gEmbed, $matches ) );

								if ( $is_vimeo ) {
									$data_type = 'vimeo';
								}
								if ( $is_youtube ) {
									$data_type = 'youtube';
									$gEmbed = str_replace(array('?enablejsapi=1', $matches[1]), array('', $matches[1] . '?enablejsapi=1'), $gEmbed);
								}
                                ?>
                                <article <?php post_class('item '.$active); ?> data-type="<?php echo $data_type; ?>">
                                    <div class="loop-item-wrap">
                                        <?php if(has_post_thumbnail()):?>
                                            <div class="loop-item-featured">
                                                <a href="<?php the_permalink() ?>">
                                                    <?php the_post_thumbnail('noo-thumbnail-square')?>
                                                </a>
                                            </div>
                                            <div class="hidden-video-item reload-video-item" style="display:none">
		                                    	<?php if( has_featured_content() ) : ?>
		                                    		<?php echo $gEmbed; ?>
		                                    	<?php endif; ?>
		                                    </div>
	                                        <div class="loop-item-content">
	                                            <a class="hvr-shadow-radial icon click-to-show-video"><i class="fa fa-play"></i></a>
	                                            <h2 class="loop-item-title">
	                                                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
	                                            </h2>
	                                            <div class="loop-item-excerpt">
	                                                <?php
	                                                echo '<p>' . wp_trim_words(get_the_excerpt() , 8) . '</p>';
	                                                ?>
	                                            </div>
	                                        </div>
	                                    <?php elseif ( has_featured_content() ) : ?>
	                                    	<div class="loop-item-featured reload-video-item only-video-item" style="min-height: 427px;">
		                                    	<?php echo $gEmbed; ?>
                                            </div>
                                        <?php endif;?>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    </div>

                </div>
                <?php wp_enqueue_script( 'froogaloop' ); ?>
                <script>
                	var tag = document.createElement('script');
					tag.src = "//www.youtube.com/player_api";
					var firstScriptTag = document.getElementsByTagName('script')[0];
					firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                	jQuery(document).ready(function($){
                		$('.loop-thumb-content .carousel .carousel-inner .item').click(function(){
            				$('.reload-video-item').each(function(){
            					$(this).html( $(this).html() );
            					// $(this).hide();
            				});
            				// $('.click-to-show-video')
            				// 		.removeClass('click-to-pause-video')
	               //  				.find('.fa').removeClass('fa-stop').addClass('fa-play');
                		});

                		$('.only-video-item').each(function(){
                			$(this).find('iframe').css('height', '427px');
                		});

						$('.click-to-show-video').click(function(){
							var pa  = $(this).closest('article');
							var hiv = pa.find('.hidden-video-item');
	                		var ifr = hiv.find('iframe');
							// if not has class pause-video
							if ( !$(this).hasClass('click-to-pause-video') ) {
		                		// add height to style
		                		var new_h = parseInt(pa.find('.loop-item-featured a img').height());
		                		if (new_h < 427 || isNaN(new_h) ) new_h = 427;
		                		hiv.css('height', new_h+'px');
		                		ifr.css('height', (new_h-70)+'px');
		                		// Reset other
		                		var cpa = $(this).closest('.carousel-inner').find('article');
		                		cpa.find('.hidden-video-item').hide();
		                		cpa.find('.loop-item-featured').show();
		                		cpa.find('.click-to-show-video').removeClass('click-to-pause-video');
		                		cpa.find('.fa').removeClass('fa-stop').addClass('fa-play');
		                		cpa.find('iframe').removeAttr('id');
	                			// Hide & show
		                		pa.find('.loop-item-featured').hide();
		                		ifr.attr('id', 'video');
		                		// Control play video
		                		var type = pa.data('type');
								if ( type == 'vimeo' ) {
									var iframe = document.getElementById('video');
									var player = $f(iframe);
									player.api("play");
		                		} else if ( type == 'youtube' ) {
		                			// Disable on Firefox
		                			var isFirefox = typeof InstallTrigger !== 'undefined';
		                			if ( !isFirefox ){
		                				var playerYT;
										playerYT = new YT.Player('video', {
											events: {
												'onReady': onPlayerReady
											}
										})
										function onPlayerReady(event) {
											playerYT.playVideo();
										}	
		                			}
		                		}
		                		$(this)
		                			.addClass('click-to-pause-video')
		                			.find('.fa').addClass('fa-stop').removeClass('fa-play');
		                		hiv.show();
							} else {
								pa.find('.loop-item-featured').show();
								hiv.html( hiv.html() );
	                			hiv.hide();
	                			$(this)
	                				.removeClass('click-to-pause-video')
	                				.find('.fa').removeClass('fa-stop').addClass('fa-play');
							}
	                	});
					})
                </script>
            </div>

        <?php endif;
        return ob_get_clean();
    }
    add_shortcode('noo_video_slider', 'noo_shortcode_video_slider');
}
// [blog]
// ============================
if( !function_exists( 'noo_shortcode_blog' ) ) {
	function noo_shortcode_blog( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'layout'            => 'masonry',
			'columns'           => '3',
			'categories'        => 'all',
			'filter'            => '',
			'orderby'           => 'latest',
			'post_count'     	=> '4',
			'excerpt_length'	=> '55',
			'hide_readmore'		=> '',
			'title'             => '',
			'sub_title'         => '',
			'hide_author'		=> '',
			'hide_date'			=> '',
			'hide_category'		=> '',
			'hide_comment'		=> '',
			'visibility'        => '',
			'class'             => '',
			'id'                => '',
			'custom_style'      => ''
			), $atts ) );
		$hide_author = !empty($hide_author) ? true : false;
		$hide_date = !empty($hide_date) ? true : false;
		$hide_category = !empty($hide_category) ? true : false;
		$hide_comment = !empty($hide_comment) ? true : false;
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
			$orderby = 'date';
			break;
		}

		$args = array(
			'orderby'         => "{$orderby}",
			'order'           => "{$order}",
			'post_type'       => "post",
			'posts_per_page'  => "{$post_count}"
			);

		if(!empty($categories) && $categories != 'all'){
			$args['cat'] = $categories;
		}
		$q = new WP_Query( $args );
		
		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class            = ( $class           != ''     ) ? 'noo-post-list ' . esc_attr( $class ) : 'noo-post-list';
		$class           .= noo_visibility_class( $visibility );
		
		$id    = ( $id    != '' ) ? ' id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';

		$html = array();
		$item_class='';
		$html[] ='<div '.$id.$class.$custom_style.'>';
		
		$html[] = (!empty($title) ? '<h2 class="title">'.$title.(!empty($sub_title) ? '<p class="small sub-title">'.$sub_title.'</p>' : '').'</h2>' : '');
		
		if( $layout == 'masonry' ) {
			
			wp_enqueue_script('vendor-isotope');
			
			ob_start();
		?>
			<div class="masonry">
				<?php 
				$category_arr = explode(',', $categories);
				if( count( $category_arr ) > 0 && $filter == 'true' ):
				?>
				<div class="masonry-header">
					<div class="masonry-filters">
						<ul data-option-key="filter" >
							<li>
								<a class="selected" href="#" data-option-value= "*"><?php echo __('All','noo') ?></a>
							</li>
							<?php
							foreach ($category_arr as $cat):
								if($cat == 'all')
									continue;
								
								$category = get_term($cat, 'category');
							?>
							<li>
								<a href="#" data-option-value= ".<?php echo 'mansonry-filter-'.$category->slug?>"><?php echo esc_html($category->name); ?></a>
							</li>
						<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<?php
				endif;
				?>
				<div class="mansonry-content">
					<div id="masonry-container" data-masonry-gutter="40" class="masonry-container columns-<?php echo esc_attr($columns); ?>">
		<?php 
			$html[]= ob_get_clean();
		}
		?>
		<?php		
		if ( $q->have_posts() ) : 
			while ( $q->have_posts() ) : $q->the_post();
				global $post;
				ob_start();
				?>
				<?php 
				$post_id = get_the_id();
				$post_type = get_post_type($post_id);
				$post_format = noo_get_post_format($post_id, $post_type);
				
				
				?>
				<?php 
				if( $layout == 'masonry' ) {
					// Categories class
					$cat_class = array();
					if ( is_object_in_taxonomy( $post->post_type, 'category' ) ) {
						foreach ( (array) get_the_category($post->ID) as $cat ) {
							if ( empty($cat->slug ) )
								continue;
							$cat_class[] = 'mansonry-filter-' . sanitize_html_class($cat->slug, $cat->term_id);
						}
					}
					$masonry_size = noo_get_post_meta($post_id, "_noo_wp_post_masonry_{$post_format}_size", 'regular');
					$item_class = 'masonry-item '.$masonry_size.' '.implode(' ', $cat_class);
				}
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class($item_class); ?>>
					
					<div class="content-wrap">
						<header class="content-header">
							<?php if($post_format=='quote'):?>
								<?php 
								$quote = '';
								$post_id = get_the_ID();
								$quote = noo_get_post_meta($post_id , '_noo_wp_post_quote', get_the_title( $post_id ) );
								$cite = noo_get_post_meta($post_id , '_noo_wp_post_quote_citation', '');
								?>
								<h2 class="content-title content-quote">
									<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(sprintf(__('Permanent link to: "%s"', 'noo') , the_title_attribute('echo=0'))); ?>">
										<?php echo $quote; ?>
									</a>
								</h2>
								<cite class="content-sub-title content-cite"><i class="fa fa-quote-left"></i> <?php echo esc_html($cite); ?></cite>
							<?php else:?>
								<h2 class="content-title">
									<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
								</h2>
								<?php if($post_format == 'link'):?>
									<?php $link = noo_get_post_meta( $post_id, '_noo_wp_post_link',  '' ); ?>
									<?php if($link != '') : ?>
									<span class="content-sub-title content-link">
										<a href="<?php echo esc_url($link); ?>" title="<?php echo esc_attr( sprintf( __( 'Shared link from post: "%s"', 'noo' ), the_title_attribute( 'echo=0' ) ) ); ?>" target="_blank">
										<i class="fa fa-link"></i>
										<?php echo esc_html($link); ?>
										</a>
									</span>
									<?php endif; ?>
								<?php endif;?>
							<?php endif;?>
							<?php if($post_format == 'link' || $post_format == 'quote' ):?>
									
							<?php else:?>
								<?php noo_content_meta(true); ?>
							<?php endif; ?>
							
						</header>
						<?php if( has_featured_content() ) : ?>
						<div class="content-featured">
							<?php 
							switch ($post_format){
								case 'audio':
									noo_featured_audio();
								break;
								case 'video':
									noo_featured_video();
								break;
								case 'gallery':
									noo_featured_gallery();
								break;
								case 'image':
									noo_featured_image();
								break;
								case 'link':
								case 'quote':
								default:
									noo_featured_default();
								break;
							}
							?>
						</div>
						<?php endif; ?>
						<?php if($post_format == 'link' || $post_format == 'quote' ):?>
						<?php else:?>
						<div class="content-excerpt">
							<?php 
							$excerpt = $post->post_excerpt;
							if(empty($excerpt))
								$excerpt = $post->post_content;
							
							$excerpt = strip_shortcodes($excerpt);
							$excerpt = wp_trim_words($excerpt,$excerpt_length,'...');
							echo '<p>' . $excerpt . '</p>';
							?>
							<?php if(empty($hide_readmore)) echo noo_get_readmore_link();?>
						</div>
						<?php endif;?>

					</div>
				</article>
				<?php
				$html[] = ob_get_clean();
			endwhile;
		endif;
		?>
		<?php if( $layout == 'masonry' ) {?>
		<?php ob_start();?>
				</div><!-- /#masonry-container -->
			</div><!-- /#masonry-content -->
		</div><!-- /#masonry -->
		<?php $html[] = ob_get_clean();?>
		<?php }?>
		<?php
		$html[] = '</div>';
		wp_reset_query();
		wp_reset_postdata();
		
		return implode( "\n", $html );
	}
}

add_shortcode( 'blog', 'noo_shortcode_blog' );

// [author]
// ============================
// if( !function_exists( 'noo_shortcode_team_member' ) ) {
// 	function noo_shortcode_team_member ( $atts, $content = null ) {
// 		extract( shortcode_atts( array(
// 			'author'            => '',
// 			'custom_avatar'     => '',
// 			'role'              => '',
// 			'description'       => '',
// 			'facebook'          => '',
// 			'twitter'           => '',
// 			'googleplus'        => '',
// 			'linkedin'          => '',
// 			'visibility'        => '',
// 			'class'             => '',
// 			'id'                => '',
// 			'custom_style'      => ''
// 			), $atts ) );

// 		if( empty($author) ) {
// 			return '';
// 		}

// 		$user = get_userdata( $author );
// 		if( empty($user) ) {
// 			return '';
// 		}

// 		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
// 		$class            = ( $class           != ''     ) ? 'noo-member ' . esc_attr( $class ) : 'noo-member';
// 		$class           .= noo_visibility_class( $visibility );
		
// 		$id    = ( $id    != '' ) ? ' id="' . esc_attr( $id ) . '"' : '';
// 		$class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
// 		$custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';
		
// 		$custom_avatar = !empty( $custom_avatar ) ? wp_get_attachment_image_src( $custom_avatar,'fullwdith') : array();
// 		$avatar = empty( $custom_avatar ) ? get_avatar( $user->ID, 250 ) : '<img src="' . $custom_avatar[0] . '" />';
// 		$description = empty( $description ) ? get_user_meta( $author, 'description', true ) : $description;

// 		$html[] = '<div '.$class.$id.$custom_style.'>';
// 		$html[] = '<div class="member-avatar">';
// 		$html[] = $avatar;
// 		if(!empty($facebook) || !empty($twitter) || !empty($googleplus) || !empty($linkedin)):
// 			$html[] = '<div class="member-social">';
// 			if(!empty($facebook)){
// 				$html[] = '<a href="'.$facebook.'" target="_blank"><i class="fa fa-facebook"></i></a>';
// 			}
// 			if(!empty($twitter)){
// 				$html[] = '<a href="'.$twitter.'" target="_blank"><i class="fa fa-twitter"></i></a>';
// 			}
// 			if(!empty($googleplus)){
// 				$html[] = '<a href="'.$googleplus.'" target="_blank"><i class="fa fa-google-plus"></i></a>';
// 			}
// 			if(!empty($linkedin)){
// 				$html[] = '<a href="'.$linkedin.'" target="_blank"><i class="fa fa-linkedin"></i></a>';
// 			}
// 			$html[] = '</div>';
// 		endif;
// 		$html[] ='</div>';
// 		$html[] = '<div class="member-info">';
// 		$html[] = '<h3 class="team-meta">'.$user->display_name.($role !== '' ? ' <small>' . $role . '</small>':'').'</h3>';
// 		$html[] = '<p class="member-description">' . $description . '</p>';
// 		$html[] = '</div>';
// 		$html[] = '</div>';

// 		return implode( "\n", $html );
// 	}
// }

// add_shortcode( 'team_member', 'noo_shortcode_team_member' );

// [team_member]
// ============================
if( !function_exists( 'noo_shortcode_team_member' ) ) {
	function noo_shortcode_team_member ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'name'            => '',
			'avatar'     => '',
			'role'              => '',
			'description'       => '',
			'facebook'          => '',
			'twitter'           => '',
			'googleplus'        => '',
			'linkedin'          => '',
			'visibility'        => '',
			'class'             => '',
			'id'                => '',
			'custom_style'      => ''
			), $atts ) );

		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class            = ( $class           != ''     ) ? 'noo-member ' . esc_attr( $class ) : 'noo-member';
		$class           .= noo_visibility_class( $visibility );
		
		$id    = ( $id    != '' ) ? ' id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';
		
		$avatar = !empty( $avatar ) ? wp_get_attachment_image_src( $avatar,'fullwdith') : array();
		$avatar = !empty( $avatar ) ? '<img src="' . $avatar[0] . '" />' : '';
		$description = !empty( $description ) ? $description : '';

		$html[] = '<div '.$class.$id.$custom_style.'>';
		$html[] = '<div class="member-avatar">';
		$html[] = $avatar;
		if(!empty($facebook) || !empty($twitter) || !empty($googleplus) || !empty($linkedin)):
			$html[] = '<div class="member-social">';
			if(!empty($facebook)){
				$html[] = '<a href="'.$facebook.'" target="_blank"><i class="fa fa-facebook"></i></a>';
			}
			if(!empty($twitter)){
				$html[] = '<a href="'.$twitter.'" target="_blank"><i class="fa fa-twitter"></i></a>';
			}
			if(!empty($googleplus)){
				$html[] = '<a href="'.$googleplus.'" target="_blank"><i class="fa fa-google-plus"></i></a>';
			}
			if(!empty($linkedin)){
				$html[] = '<a href="'.$linkedin.'" target="_blank"><i class="fa fa-linkedin"></i></a>';
			}
			$html[] = '</div>';
		endif;
		$html[] ='</div>';
		$html[] = '<div class="member-info">';
		$html[] = '<h3 class="team-meta">'.$name.($role !== '' ? ' <small>' . $role . '</small>':'').'</h3>';
		$html[] = '<p class="member-description">' . $description . '</p>';
		$html[] = '</div>';
		$html[] = '</div>';

		return implode( "\n", $html );
	}
}

add_shortcode( 'team_member', 'noo_shortcode_team_member' );


// [noo_rev_slider] Revolution Slider
// ============================
if( !function_exists( 'noo_shortcode_noo_rev_slider' ) ) {
	function noo_shortcode_noo_rev_slider ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			"visibility"        => "",
			"class"             => "",
			"id"                => "",
			"custom_style"      => "",
			'slider'            => ''
			), $atts ) );

		if( empty($slider) ) {
			return '';
		}

		$class            = ( $class           != ''     ) ? 'noo-rev-slider ' . esc_attr( $class ) : 'noo-rev-slider';
		$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class           .= noo_visibility_class( $visibility );

		$html  = array();

		$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style   = ( $custom_style  != '' ) ? 'style="' . $custom_style . '"' : '';

		$html[] = "<div {$class} {$custom_style}>";
        $html[] = do_shortcode('[rev_slider ' . $slider . ']');
        $html[] = '</div>';

		return implode( "\n", $html );
	}
}

add_shortcode( 'noo_rev_slider', 'noo_shortcode_noo_rev_slider' );

// [slider] Responsive Slider
// ============================
if( !function_exists( 'noo_shortcode_slider' ) ) {
	function noo_shortcode_slider ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'visibility'         => '',
			'class'              => '',
			'id'                 => '',
			'custom_style'       => '',
			'animation'          => 'slide',
			'visible_items'      => '1',
			'slider_time'        => '3000',
			'slider_speed'       => '600',
			'auto_play'          => '',
			'pause_on_hover'     => '',
			'random'             => '',
			'indicator'          => '',
			'indicator_position' => '',
			'prev_next_control'  => '',
			'timer'              => '',
			'swipe'              => '',
			), $atts ) );

		wp_enqueue_script( 'vendor-carouFredSel' );
		$indicator_class  = ( $indicator_position != '' ) ? $indicator_position . '-indicators' : 'top-indicators';
		
		$class            = ( $class              != '' ) ? 'noo-slider ' . $indicator_class . '' . esc_attr( $class ) : 'noo-slider ' . $indicator_class ;
		$visibility       = ( $visibility         != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class           .= noo_visibility_class( $visibility );

		$html  = array();

		$id    = ( $id    != '' ) ? esc_attr( $id ) : 'noo-slider-' . noo_vc_elements_id_increment();
		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style   = ( $custom_style  != '' ) ? 'style="' . $custom_style . '"' : '';

		$indicator_html = array();
		$indicator_js   = array();
		if( $indicator == 'true') {
			$indicator_js[] = '    pagination: {';
			$indicator_js[] = '      container: "#' . $id . '-pagination"';
			$indicator_js[] = '    },';

			$indicator_html[] = '  <div id="' . $id . '-pagination" class="slider-indicators"></div>';
		}

		$prev_next_control_html = array();
		$prev_next_control_js   = array();
		if( $prev_next_control == 'true') {
			$prev_next_control_js[]   = '    prev: {';
			$prev_next_control_js[]   = '      button: "#' . $id . '-prev"';
			$prev_next_control_js[]   = '    },';
			$prev_next_control_js[]   = '    next: {';
			$prev_next_control_js[]   = '      button: "#' . $id . '-next"';
			$prev_next_control_js[]   = '    },';

			$prev_next_control_html[] = '  <a id="' . $id . '-prev" class="slider-control prev-btn" role="button" href="#"><span class="slider-icon-prev"></span></a>';
			$prev_next_control_html[] = '  <a id="' . $id . '-next" class="slider-control next-btn" role="button" href="#"><span class="slider-icon-next"></span></a>';
		}

		$timer_html = array();
		$timer_js   = array();
		if( $timer == 'true' ) {
			$timer_js[]  = '      progress: {';
			$timer_js[]  = '        bar: "#' . $id . '-timer"';
			$timer_js[]  = '      },';

			$timer_html[] = '  <div id="' . $id . '-timer" class="slider-timer"></div>';
		}

		$swipe  = ( $swipe == 'true' ) ? 'true' : 'false';
		$animation = ( $animation == 'slide' ) ? 'scroll' : $animation; // Not allow fading with carousel

		$html[] = "<div id=\"{$id}\" {$class} {$custom_style}>";
		$html[] = '  <ul class="sliders">';
		$html[] = do_shortcode( $content );
		$html[] = '  </ul>';
		$html[] = '  <div class="clearfix"></div>';
		$html[] = implode( "\n", $timer_html );
		$html[] = implode( "\n", $indicator_html );
		$html[] = implode( "\n", $prev_next_control_html );
		$html[] = '</div>';

		// slider script
		$html[] = '<script>';
		$html[] = "jQuery('document').ready(function ($) {";
		$html[] = "  $('#{$id} .sliders').carouFredSel({";
		$html[] = "    infinite: true,";
		$html[] = "    circular: true,";
		$html[] = "    responsive: true,";
		$html[] = "    debug : false,";
		$html[] = '    items: {';
		$html[] = ( $random == 'true'     ) ? '      start: "random"' : '      start: 0';
		$html[] = '    },';
		$html[] = '    scroll: {';
		$html[] = '      items: 1,';
		$html[] = ( $slider_speed   != ''         ) ? '      duration: ' . $slider_speed . ',' : '';
		$html[] = ( $pause_on_hover == 'true'     ) ? '      pauseOnHover: "resume",' : '';
		$html[] = '      fx: "' . $animation . '"';
		$html[] = '    },';
		$html[] = '    auto: {';
		$html[] = ( $slider_time    != ''     ) ? '      timeoutDuration: ' . $slider_time . ',' : '';
		$html[] = implode( "\n", $timer_js );
		$html[] = ( $auto_play      == 'true' ) ? '      play: true' : '      play: false';
		$html[] = '    },';
		$html[] = implode( "\n", $prev_next_control_js );
		$html[] = implode( "\n", $indicator_js );
		$html[] = '    swipe: {';
		$html[] = "      onTouch: {$swipe},";
		$html[] = "      onMouse: {$swipe}";
		$html[] = '    }';
		$html[] = '  });';
		$html[] = '});';
		$html[] = '</script>';

		return implode( "\n", $html );
	}
}
add_shortcode( 'slider', 'noo_shortcode_slider' );

// [slide]
// ============================
if( !function_exists( 'noo_shortcode_slide' ) ) {
	function noo_shortcode_slide ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'class'             => '',
			'id'                => '',
			'custom_style'      => '',
			'type'              => 'image',
			'image'             => '',
			'caption'           => '',
			'video_url'         => '',
			'video_poster'      => ''
			), $atts ) );

		$id    = ( $id    != '' ) ? esc_attr( $id ) : 'noo-slider-item-' . noo_vc_elements_id_increment();
		$slide_content = array();
		if( $type == 'image' ) {
			$image = wp_get_attachment_image_src( $image, 'fullwidth-fullwidth' );

			$alt          = get_post_meta($image, '_wp_attachment_image_alt', true);
			$alt          = ( $alt != '' ) ? $alt : get_the_title( $image );
			$slide_content[] = '<img class="slide-image" alt="'. $alt .'" src="' . $image[0] . '">';
			$slide_content[] = ( $caption != '' ) ? '<p class="slide-caption">' . $caption . '</p>' : '';
		} elseif( $type == 'video' && $video_url != '') {
			wp_enqueue_script( 'vendor-jplayer' );
			ob_start();
			?>
			<script>
				jQuery(document).ready(function($){
					if($().jPlayer) {
						$('#jplayer_<?php echo esc_attr($id); ?>').jPlayer({
							ready: function () {
								$(this).jPlayer('setMedia', {
									m4v: '<?php echo esc_url($video_url); ?>',
									poster: '<?php $poster = wp_get_attachment_image_src( $video_poster ); echo esc_url($poster[0]); ?>'
								});
							},
							size: {
								width: '100%',
								height: '100%'
							},
							swfPath: '<?php echo get_template_directory_uri(); ?>/framework/vendor/jplayer',
							cssSelectorAncestor: '#jp_interface_<?php echo esc_attr($id); ?>',
							supplied: 'm4v'
						});
					}
				});
			</script>
			<div class="noo-video-container">
				<div class="video-inner">
					<div id="jplayer_<?php echo esc_attr($id); ?>" class="jp-jplayer jp-jplayer-video"></div>
					<div class="jp-controls-container jp-video">
						<div id="jp_interface_<?php echo esc_attr($id); ?>" class="jp-interface">
							<ul class="jp-controls">
								<li><a href="#" class="jp-play" tabindex="1"><span><?php echo __('Play','noo') ?></span></a></li>
								<li><a href="#" class="jp-pause" tabindex="1"><span><?php echo __('Pause','noo') ?></span></a></li>
								<li><a href="#" class="jp-mute" tabindex="1"><span><?php echo __('Mute','noo') ?></span></a></li>
								<li><a href="#" class="jp-unmute" tabindex="1"><span><?php echo __('UnMute','noo') ?></span></a></li>
							</ul>
							<div class="jp-progress-container">
								<div class="jp-progress">
									<div class="jp-seek-bar">
										<div class="jp-play-bar"></div>
									</div>
								</div>
								<div class="jp-volume-bar">
									<div class="jp-volume-bar-value"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			$slide_content[] = ob_get_contents();
			ob_end_clean();
		} elseif ($type == 'content' ) {
			$slide_content[] = '<div class="slide-content">';
			$slide_content[] = noo_handler_shortcode_content( $content, true );
			$slide_content[] = '</div>';
		}

		if( empty( $slide_content ) ) {
			return '';
		}

		$class = ( $class != ''     ) ? 'slide-item ' . esc_attr( $class ) : 'slide-item';

		$html  = array();

		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style   = ( $custom_style  != '' ) ? 'style="' . $custom_style . '"' : '';

		$html[] = "<li {$class} {$custom_style}>";

		$html[] = implode( "\n", $slide_content );
        $html[] = '</li>';

		return implode( "\n", $html );
	}
}

add_shortcode( 'slide', 'noo_shortcode_slide' );

// [lightbox]
// ============================
if( !function_exists( 'noo_shortcode_lightbox' ) ) {
	function noo_shortcode_lightbox ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'visibility'        => '',
			'class'             => '',
			'id'                => '',
			'custom_style'      => '',
			'gallery_id'        => '',
			'type'              => 'image',
			'image'             => '',
			'image_title'       => '',
			'iframe_url'        => '#',
			'thumbnail_type'    => 'image',
			'thumbnail_image'   => '',
			'thumbnail_style'   => '',
			'thumbnail_title'   => ''
			), $atts ) );

		wp_enqueue_script( 'vendor-nivo-lightbox-js' );
		wp_enqueue_style( 'vendor-nivo-lightbox-default-css' );

		$id             = ( $id         != '' ) ? esc_attr( $id ) : 'noo-lightbox-item-' . noo_vc_elements_id_increment();
		$class          = ( $class      != '' ) ? 'noo-lightbox-item ' . esc_attr( $class ) : 'noo-lightbox-item';
		$visibility     = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class         .= noo_visibility_class( $visibility );

		$lightbox_link    = '';
		$lightbox_type    = '';
		$lightbox_title   = '';
		$lightbox_gallery = ( $gallery_id != '' ) ? 'data-lightbox-gallery="' . $gallery_id . '"' : '';
		if( $type == 'image' ) {
			$lightbox_link  = wp_get_attachment_image_src( $image, 'fullwidth-fullwidth' );
			if( !empty( $lightbox_link ) ) {
				$lightbox_link = 'href="' . $lightbox_link[0] . '"';
			}
			$lightbox_title = ( $image_title != '' ) ? 'title="' . $image_title . '"' : '';
		} elseif( $type == 'iframe' ) {
			$lightbox_link = 'href="' . ( ( $iframe_url != '' ) ? $iframe_url : '#' ) . '"';
			$lightbox_type = 'data-lightbox-type="iframe"';
		} else {
			$lightbox_link = 'href="#hidden-html-' . $id . '"';
			$lightbox_type = 'data-lightbox-type="inline"';
		}

		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style   = ( $custom_style  != '' ) ? 'style="' . $custom_style . '"' : '';

		$html   = array();
		$html[] = "<a {$lightbox_link} {$lightbox_type} {$lightbox_title} {$lightbox_gallery} id=\"{$id}\" {$class} {$custom_style} >";
		if( $thumbnail_type == 'image' ) {
			if( empty($thumbnail_image) ) {
				$thumbnail_image = $image;
			}
			$thumbnail = wp_get_attachment_image_src( $thumbnail_image );
			if( !empty( $thumbnail ) ) {
				$thumbnail = 'src="' . $thumbnail[0] . '"';
			}
			$thumbnail_class = ( $thumbnail_style != '' ) ? 'class="' . $thumbnail_style . '"' : '';
			$html[]    = "<img {$thumbnail} {$thumbnail_class} {$lightbox_title} >";
		} elseif( $thumbnail_type == 'link') {
			$html[] = $thumbnail_title;
		}
		$html[] = '</a>';

		if( $type == 'inline' ) {
			$html[] = '<div id="hidden-html-' . $id . '" style="display:none;" >';
			$html[] = noo_handler_shortcode_content( $content, true );
			$html[] = '</div>';
		}

		return implode( "\n", $html );
	}
}

add_shortcode( 'lightbox', 'noo_shortcode_lightbox' );

// [video_player]
// ============================
if( !function_exists( 'noo_shortcode_video_player' ) ) {
	function noo_shortcode_video_player ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'visibility'        => '',
			'class'             => '',
			'id'                => '',
			'hide_controls'		=> '',
			'show_play_icon'	=> '',
			'custom_style'      => '',
			'video_m4v'         => '',
			'video_ogv'         => '',
			'video_ratio'       => '',
			'video_poster'      => '',
			'auto_play'         => '',
			), $atts ) );
		wp_enqueue_script( 'vendor-jplayer' );

		$id             = ( $id         != '' ) ? esc_attr( $id ) : 'noo-video-player-' . noo_vc_elements_id_increment();
		$class          = ( $class      != '' ) ? 'noo-video-player noo-video-container ' . esc_attr( $class ) : 'noo-video-player noo-video-container';
		switch($video_ratio) {
			case '16:9':
				$class .= ' 16-9-ratio';
				break;
			case '5:3':
				$class .= ' 5-3-ratio';
				break;
			case '5:4':
				$class .= ' 5-4-ratio';
				break;
			case '4:3':
				$class .= ' 4-3-ratio';
				break;
			case '3:2':
				$class .= ' 3-2-ratio';
				break;
		}

		$visibility     = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class         .= noo_visibility_class( $visibility );

		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style   = ( $custom_style  != '' ) ? 'style="' . $custom_style . '"' : '';

		$html   = array();

		ob_start();
		?>
		<script>
			jQuery(document).ready(function($){
				if($().jPlayer) {
					$('#jplayer_<?php echo esc_attr($id); ?>').jPlayer({
						ready: function () {
							$(this).jPlayer('setMedia', {
								<?php if ( $video_m4v != '' ) : ?>
								m4v: '<?php echo esc_url($video_m4v); ?>',
								<?php endif; ?>
								<?php if ( $video_ogv != '' ) : ?>
								ogv: '<?php echo esc_url($video_ogv); ?>',
								<?php endif; ?>
								<?php 
								if ( !empty( $video_poster ) ) :
									$poster = wp_get_attachment_image_src( $video_poster , 'full');
								?>
								poster: '<?php echo esc_url($poster[0]); ?>'
								<?php endif; ?>
							})<?php echo ( $auto_play == 'true' ? '.jPlayer("play");' : '' ); ?>
						},
						size: {
							width: '100%',
							height: '100%'
						},
						swfPath: '<?php echo get_template_directory_uri(); ?>/framework/vendor/jplayer',
						cssSelectorAncestor: '#jp_interface_<?php echo esc_attr($id); ?>',
						supplied: '<?php if( $video_m4v != "" ) echo 'm4v,'; ?><?php if ( $video_ogv != "" ) echo 'ogv,'; ?>'
					});
				}
			});
		</script>
		<div <?php echo ( $class . ' ' . $custom_style ); ?>>
			<div class="video-inner">
				<div id="jplayer_<?php echo esc_attr($id); ?>" class="jp-jplayer jp-jplayer-video"></div>
				<div id="jp_interface_<?php echo esc_attr($id); ?>" >
					<?php if(!empty($show_play_icon)):?>
					<div class="jp-video-play-icon">
						<a class="jp-video-play" tabindex="1" href="javascript:;"><i class="fa inpulse fa-play"></i></a>
					</div>
					<?php endif;?>
					<div class="jp-controls-container jp-video<?php echo (!empty($hide_controls) ? ' hidden' : '')?>">
						<div class="jp-interface">
							<ul class="jp-controls">
								<li><a href="#" class="jp-play" tabindex="1"><span><?php echo __('Play','noo') ?></span></a></li>
								<li><a href="#" class="jp-pause" tabindex="1"><span><?php echo __('Pause','noo') ?></span></a></li>
								<li><a href="#" class="jp-mute" tabindex="1"><span><?php echo __('Mute','noo') ?></span></a></li>
								<li><a href="#" class="jp-unmute" tabindex="1"><span><?php echo __('UnMute','noo') ?></span></a></li>
							</ul>
							<div class="jp-progress-container">
								<div class="jp-progress">
									<div class="jp-seek-bar">
										<div class="jp-play-bar"></div>
									</div>
								</div>
								<div class="jp-volume-bar">
									<div class="jp-volume-bar-value"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		$html[] = ob_get_contents();
		ob_end_clean();

		return implode( "\n", $html );
	}
}

add_shortcode( 'video_player', 'noo_shortcode_video_player' );

// [video_embed]
// ============================
if( !function_exists( 'noo_shortcode_video_embed' ) ) {
	function noo_shortcode_video_embed ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'visibility'        => '',
			'class'             => '',
			'id'                => '',
			'custom_style'      => '',
			'video_ratio'       => '',
			), $atts ) );

		$id             = ( $id         != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class          = ( $class      != '' ) ? 'noo-video-embed ' . esc_attr( $class ) : 'noo-video-embed';
		switch($video_ratio) {
			case '16:9':
				$class = ' 16-9-ratio';
				break;
			case '5:3':
				$class = ' 5-3-ratio';
				break;
			case '5:4':
				$class = ' 5-4-ratio';
				break;
			case '4:3':
				$class = ' 4-3-ratio';
				break;
			case '3:2':
				$class = ' 3-2-ratio';
				break;
		}

		$visibility     = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class         .= noo_visibility_class( $visibility );

		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style   = ( $custom_style  != '' ) ? 'style="' . $custom_style . '"' : '';

		$content = trim(vc_value_from_safe($content));

		$html   = array();
		$html[] = "<div {$id} {$class} {$custom_style}>{$content}</div>";
  

		return implode( "\n", $html );
	}
}

add_shortcode( 'video_embed', 'noo_shortcode_video_embed' );

// [audio_player]
// ============================
if( !function_exists( 'noo_shortcode_audio_player' ) ) {
	function noo_shortcode_audio_player ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'visibility'        => '',
			'class'             => '',
			'id'                => '',
			'custom_style'      => '',
			'audio_mp3'         => '',
			'audio_oga'         => '',
			'auto_play'         => '',
			), $atts ) );
		wp_enqueue_script( 'vendor-jplayer' );

		$id             = ( $id         != '' ) ? esc_attr( $id ) : 'noo-audio-player-' . noo_vc_elements_id_increment();
		$class          = ( $class      != '' ) ? 'noo-audio-player noo-audio-container ' . esc_attr( $class ) : 'noo-audio-player noo-audio-container';

		$visibility     = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class         .= noo_visibility_class( $visibility );

		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style   = ( $custom_style  != '' ) ? 'style="' . $custom_style . '"' : '';

		$html   = array();

		ob_start();
		?>
		<script>
			jQuery(document).ready(function($){
				if($().jPlayer) {
					$('#jplayer_<?php echo esc_attr($id); ?>').jPlayer({
						ready: function () {
							$(this).jPlayer('setMedia', {
								<?php if ( $audio_mp3 != '' ) : ?>
								mp3: '<?php echo esc_url($audio_mp3); ?>',
								<?php endif; ?>
								<?php if ( $audio_oga != '' ) : ?>
								oga: '<?php echo esc_url($audio_oga); ?>',
								<?php endif; ?>
							})<?php echo ( $auto_play == 'true' ? '.jPlayer("play");' : '' ); ?>
						},
						size: {
							width: '100%',
							height: '0'
						},
						swfPath: '<?php echo get_template_directory_uri(); ?>/framework/vendor/jplayer',
						cssSelectorAncestor: '#jp_interface_<?php echo esc_attr($id); ?>',
						supplied: '<?php if( $audio_mp3 != "" ) echo 'mp3,'; ?><?php if ( $audio_oga != "" ) echo 'oga,'; ?>'
					});
				}
			});
		</script>
		<div <?php echo ( $class . ' ' . $custom_style ); ?>>
			<div class="audio-inner">
				<div id="jplayer_<?php echo esc_attr($id); ?>" class="jp-jplayer jp-jplayer-audio"></div>
				<div class="jp-controls-container jp-audio">
					<div id="jp_interface_<?php echo esc_attr($id); ?>" class="jp-interface">
						<ul class="jp-controls">
							<li><a href="#" class="jp-play" tabindex="1"><span><?php echo __('Play','noo') ?></span></a></li>
							<li><a href="#" class="jp-pause" tabindex="1"><span><?php echo __('Pause','noo') ?></span></a></li>
							<li><a href="#" class="jp-mute" tabindex="1"><span><?php echo __('Mute','noo') ?></span></a></li>
							<li><a href="#" class="jp-unmute" tabindex="1"><span><?php echo __('UnMute','noo') ?></span></a></li>
						</ul>
						<div class="jp-progress-container">
							<div class="jp-progress">
								<div class="jp-seek-bar">
									<div class="jp-play-bar"></div>
								</div>
							</div>
							<div class="jp-volume-bar">
								<div class="jp-volume-bar-value"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		$html[] = ob_get_contents();
		ob_end_clean();

		return implode( "\n", $html );
	}
}

add_shortcode( 'audio_player', 'noo_shortcode_audio_player' );

// [audio_embed]
// ============================
if( !function_exists( 'noo_shortcode_audio_embed' ) ) {
	function noo_shortcode_audio_embed ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'visibility'        => '',
			'class'             => '',
			'id'                => '',
			'custom_style'      => '',
			), $atts ) );

		$id             = ( $id         != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class          = ( $class      != '' ) ? 'noo-audio-embed ' . esc_attr( $class ) : 'noo-audio-embed';

		$visibility     = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class         .= noo_visibility_class( $visibility );

		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style   = ( $custom_style  != '' ) ? 'style="' . $custom_style . '"' : '';

		$content = trim(vc_value_from_safe($content));

		$html   = array();
		$html[] = "<div {$id} {$class} {$custom_style}>{$content}</div>";
  

		return implode( "\n", $html );
	}
}

add_shortcode( 'audio_embed', 'noo_shortcode_audio_embed' );

// [social_share]
// ============================
if( !function_exists( 'noo_shortcode_social_share' ) ) {
	function noo_shortcode_social_share ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'visibility'        => '',
			'class'             => '',
			'id'                => '',
			'custom_style'      => '',
			'title'             => '',
			'facebook'          => '',
			'twitter'           => '',
			'googleplus'        => '',
			'linkedin'          => '',
			'pinterest'         => ''
			), $atts ) );

		$class          = ( $class      != '' ) ? 'content-share ' . esc_attr( $class ) : 'content-share';
		$visibility     = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class         .= noo_visibility_class( $visibility );

		$share_url     = urlencode( get_permalink() );
		$share_title   = urlencode( get_the_title() );
		$share_source  = urlencode( get_bloginfo( 'name' ) );
		$share_content = urlencode( get_the_content() );
		$share_media   = wp_get_attachment_thumb_url( get_post_thumbnail_id() );
		$popup_attr    = 'resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0';

		$id             = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class          = ( $class != '' ) ? 'class="' . $class . '"' : '';
		$custom_style   = ( $custom_style  != '' ) ? 'style="' . $custom_style . '"' : '';

		$html   = array();

		if ( $facebook || $twitter || $googleplus || $pinterest || $linkedin ) {
			$html[] = '<div class="content-share">';
			if( $title != '' ) {
				$html[] = '  <p class="share-title">';
				$html[] = '    ' . $title;
				$html[] = '  </p>';
			}
			$html[] = '<div class="noo-social social-share">';

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
								. "https://twitter.com/intent/tweet?text={$share_title}&amp;url={$share_url}','popupTwitter','width=500,height=370,{$popup_attr}');"
								. ' return false;">';
				$html[] = '<i class="fa fa-twitter"></i></a>';
			}

			if($googleplus) {
				$html[] = '<a href="#share" class="noo-share"'
							. ' title="' . __( 'Share on Google+', 'noo' ) . '"'
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

			$html[] = '</div>'; // .noo-social.social-share
			$html[] = '</div>'; // .share-wrap
		}

		// echo implode("\n", $html);
  

		return implode( "\n", $html );
	}
}

add_shortcode( 'social_share', 'noo_shortcode_social_share' );




if( !function_exists('noo_shortcode_testimonial') ){
    function noo_shortcode_testimonial($atts){
    	wp_enqueue_script( 'vendor-carouFredSel' );
        extract(shortcode_atts(array(
        	'title'        => '',
            'testimonial_image_per_page'    =>  '15',
            'autoplay'          =>  'true',
            'duration'          =>  '10000',
            'show_name'         =>  'yes',
            'show_position'     =>  'yes',
            'orderby'           =>  'latest',
            'visibility'        =>  '',
            'class'             =>  '',
			'custom_style'      =>  ''
        ),$atts));

        $visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class            = ( $class           != ''     ) ? 'noo-sc-testimonial ' . esc_attr( $class ) : 'noo-sc-testimonial ';
		$class           .= noo_visibility_class( $visibility );
		$class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';

        ob_start();
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

				default:
				$orderby = 'date';
				break;
			}
            $args = array(
                'post_type'         =>  'testimonial',
                'orderby'           =>   $orderby,
                'order'             =>   $order,
                'posts_per_page'    =>   '-1',
            );
           
            $query = new WP_Query( $args );

        ?>
        <div <?php echo ( $class . ' ' . $custom_style ); ?> >
        	<?php if(!empty($title)): ?>
				<h3 class="sc-title">
					<?php echo (esc_html( $title ));?>
				</h3>
			<?php endif;?>
			<?php if( $query->have_posts() ): ?>
				<?php
					$quote_list = array();
					$author_list = array();
					$count = 0;
				?>
				<?php
				while( $query->have_posts() ): $query->the_post(); global $post;
	            	
	            	$name     = get_post_meta(get_the_ID(),'_noo_wp_post_name', true);
					$position = get_post_meta(get_the_ID(),'_noo_wp_post_position', true);
					$url = get_post_meta(get_the_ID(),'_noo_wp_post_image', true);

					$quote_list[] = '<li data-page="' . $count . '">';
					$quote_list[] = '<p class="quote"><em>' . get_the_content() . '</em></p>';
					if($show_name=='yes') {
			            $quote_list[] = '<p class="name">' . $name . '</p>';
			        }
					if($show_position=='yes') {
			            $quote_list[] = '<p class="position"><em>' . $position . '</em></p>';
			        }
			        $quote_list[] = '</li>';

			        $author_list[] = '<li data-source="' . $count . '">';
                    // $author_list[] = '<a data-rel="' . $count . '" href="#">';
                    $author_list[] = '<img data-source="' . $count . '" class="grayscale" src="' . wp_get_attachment_url(esc_attr($url)) . '" alt="' . esc_html($post->post_title) . '" width="90" height="100"/>';
                    // $author_list[] = '</a>';
                    // $author_list[] = '<small>' . $name . '</small>';
                    $author_list[] = '</li>';

                    $count++;
                endwhile;
                wp_reset_postdata();
	            ?>

				<div class="testimonial-quote clearfix">
			        <ul class="noo-content">
			            <?php echo implode("\n", $quote_list); ?>
			        </ul>
			    </div>
		        <div class="testimonial-author clearfix">
			        <ul class="noo-thumb">
			            <?php echo implode("\n", $author_list); ?>
			        </ul>
			    </div>
	        <?php endif; ?>

	    </div>

	    <?php if(absint($query->post_count) > 1) :?>
	        <script type="text/javascript">
					jQuery('document').ready(function ($) {
						var $quoteCarousel = $('.testimonial-quote ul');
						var $authorCarousel = $('.testimonial-author ul');

						function getCenterThumb() {
							var $visible = $authorCarousel.triggerHandler( 'currentVisible' ),
							center = Math.floor( ( $visible.length - 1 ) / 2);

							return center;
						}

						var testimonialOptions = {
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
						    	fx: "scroll",
						    	onBefore: function( data ) {
						    		var page = data.items.visible.first().data( 'page' );
						    		$authorCarousel.trigger( 'slideTo', [ $authorCarousel.find("li[data-source=" + page +  "]" ), -getCenterThumb() ] );
						    		$authorCarousel.find( 'li' ).removeClass( 'selected' );
						    	},
						    	onAfter: function( data ) {
						    		var page = data.items.visible.first().data( 'page' );
						    		$authorCarousel.find("li[data-source=" + page +  "]" ).addClass( 'selected' );
						    	}
						    },
						    auto: {
						    	timeoutDuration: <?php echo esc_js($duration);?>,
						    	play: <?php echo esc_js($autoplay);?>
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
							// pagination: {
					  //         container: ".noo-thumb",
					  //         anchorBuilder: false
					  //       }
						};
						$quoteCarousel.carouFredSel(testimonialOptions);

				        var testimonialThumbOptions = {
						    infinite: true,
						    circular: true,
						    responsive: true,
						    debug : false,
							width:'100%',
						    height: 120,
						    align: 'center',
						    scroll: {
						      items: 2,
						      duration: 600,
						      pauseOnHover: "resume",
						      fx: "scroll"
						    },
						    auto: false,
						    swipe: {
						      onTouch: true,
						      onMouse: true
						    },
						    items: {
						        visible: <?php echo esc_js($testimonial_image_per_page);?>,
							    width: "variable",
							    height: 100,
							    align: 'center'
							},
							onCreate: function() {
								var center = <?php echo floor( ( $testimonial_image_per_page - 1 ) / 2);?>;//getCenterThumb();
								$authorCarousel.trigger( 'slideTo', [ -center, { duration: 0 } ] );
								$authorCarousel.find( 'li' ).eq( center ).addClass( 'selected' );
							}
						};

						$authorCarousel.carouFredSel(testimonialThumbOptions);

						$(document).on('click','.testimonial-author li',function(e){
							e.stopPropagation();
							e.preventDefault();
							// $(this).closest('ul').find('.selected').removeClass('selected');
							// $(this).closest('li').addClass('selected');
							var source = $(this).data('source');
							$quoteCarousel.trigger('slideTo', $quoteCarousel.find("li[data-page=" + source +  "]" ));
						});
						// $(".testimonial-author ul").on('click',function() {
						// 	$(".testimonial-author ul").trigger("destroy").carouFredSel(testimonialThumbOptions);
						// });

						// imagesLoaded('.testimonial-author ul',function(){
						// 	$authorCarousel.trigger('updateSizes');
						// });
						// $(window).resize(function(){
						// 	$authorCarousel.trigger("destroy").carouFredSel(testimonialThumbOptions);
						// });
					});
				</script>
			<?php endif ?>
        <?php

        $testimonial = ob_get_contents();
        ob_end_clean();
        return $testimonial;
    }
    add_shortcode('noo_testimonial','noo_shortcode_testimonial');
}

if( !function_exists('noo_shortcode_mailchimp') ){
    function noo_shortcode_mailchimp($atts){
        extract(shortcode_atts(array(
            'title' =>  ''
        ),$atts));
        echo '<div class="noo-sh-mailchimp">';
         if( isset($title) && !empty($title) ): ?><h3 class="noo-mail-title"><?php echo esc_html($title); ?></h3><?php endif;
         if( function_exists('mc4wp_show_form') ){
            mc4wp_show_form();
         }
        echo '</div>';
    }
    add_shortcode('noo_mailchimp','noo_shortcode_mailchimp');
}

// [noo_trainer]
// ============================
if( !function_exists( 'noo_shortcode_trainer' ) ) {
	function noo_shortcode_trainer ( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'title'             => '',
			'sub_title'         => '',
			'grid_style'        => 'grid',
			'columns'           => '3',
			'categories'        => 'all',
			'filter'            => '',
			'orderby'           => 'default',
			'project_count'     => '4',
			'visibility'        => '',
			'class'             => '',
			'id'                => '',
			'custom_style'      => ''
			), $atts ) );

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
			'posts_per_page'  => $project_count,
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

		$masonry_class  = 'masonry '.$grid_style;
		
		$html = array();
		$trainer_class  = '';
		$masonry_gutter   = '40';
		// if( $layout == 'masonry' ) {
		// 	$trainer_class = 'masonry-trainer';
		// 	if( $grid_style == 'masonry' ) {
		// 		//$trainer_class .= ' no-gap';
		// 		$masonry_gutter   = '0';
		// 	}
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
		<div class="masonry-header<?php echo ($grid_style != 'true' ? ' masonry-header-gap':'') ?>">
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
		if ( is_object_in_taxonomy( $post->post_type, 'class_category' ) ) {
			foreach ( (array) get_the_terms($post->ID,'class_category') as $cat ) {
				if ( empty($cat->slug ) )
					continue;
				$cat_class[] = 'mansonry-filter-' . sanitize_html_class($cat->slug, $cat->term_id);
			}
		}
		$item_class = 'masonry-item '.$masonry_size.' '.implode(' ', $cat_class);
		$post_format = ('' === $post_format) ? noo_get_post_meta($post_id, "{$prefix}_media_type", 'image') : $post_format;

		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class($item_class)?>>
			<div class="trainer-item-wrap">
				<?php if($grid_style == 'grid'):?>
                <div class="content-wrap">
                    <header class="content-header">
                        <div class="trainer-info">
                            <h3 class="content-title">
                                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
                            </h3>
                            <span class="trainer-category">
                                <?php
                                // $categories = get_the_term_list($post_id,'class_category');
                                // echo $categories;
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
                <?php if($grid_style == 'list'):
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

	add_shortcode( 'noo_trainer', 'noo_shortcode_trainer' );
}

// The following shortcode need to check if there's VC or not
// =============================================================================
if ( ! defined( 'WPB_VC_VERSION' ) ) :

	// Row
	function noo_shortcode_row ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_row.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_row', 'noo_shortcode_row' );
	add_shortcode( 'vc_row_inner', 'noo_shortcode_row' );

	// Column
	function noo_shortcode_column ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_column.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_column', 'noo_shortcode_column' );
	add_shortcode( 'vc_column_inner', 'noo_shortcode_column' );

	// Separator
	function noo_shortcode_separator ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_separator.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_separator', 'noo_shortcode_separator' );

	// Text Separator
	function noo_shortcode_text_separator ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_text_separator.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_text_separator', 'noo_shortcode_text_separator' );

	// Text Block ( Column Text )
	function noo_shortcode_column_text ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_column_text.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_column_text', 'noo_shortcode_column_text' );

	// Button
	function noo_shortcode_button ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_button.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_button', 'noo_shortcode_button' );

	// Accordion
	function noo_shortcode_accordion ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_accordion.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_accordion', 'noo_shortcode_accordion' );

	// Accordion Tab
	function noo_shortcode_accordion_tab ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_accordion_tab.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_accordion_tab', 'noo_shortcode_accordion_tab' );

	// Tabs/Tour
	function noo_shortcode_tabs ( $atts, $content = null, $tag = 'vc_tabs' ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_tabs.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_tabs', 'noo_shortcode_tabs' );
	add_shortcode( 'vc_tour', 'noo_shortcode_tabs' );

	// Tab
	function noo_shortcode_tab ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_tab.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_tab', 'noo_shortcode_tab' );

	// Pie Chart
	function noo_shortcode_pie ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_pie.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_pie', 'noo_shortcode_pie' );

	// Message
	function noo_shortcode_message ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_message.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_message', 'noo_shortcode_message' );

	// Widget Sidebar
	function noo_shortcode_widget_sidebar ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_widget_sidebar.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_widget_sidebar', 'noo_shortcode_widget_sidebar' );

	// Google Maps
	function noo_shortcode_gmaps ( $atts, $content = null ) {
		ob_start();
		include( get_template_directory() . '/vc_templates/vc_gmaps.php' );
		$html = ob_get_clean();

		return $html;
	}

	add_shortcode( 'vc_gmaps', 'noo_shortcode_gmaps' );

endif;

?>