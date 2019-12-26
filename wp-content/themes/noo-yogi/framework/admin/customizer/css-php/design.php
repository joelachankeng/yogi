<?php
// Variables
$default_link_color = noo_default_text_color();

$noo_site_link_color = $default_link_color;
$noo_site_link_hover_color = noo_get_option( 'noo_site_link_hover_color',  noo_default_primary_color() );

$noo_site_link_color_lighten_10 = lighten( $noo_site_link_hover_color, '10%' );
$noo_site_link_color_darken_5   = darken( $noo_site_link_hover_color, '5%' );
$noo_site_link_color_darken_15   = darken( $noo_site_link_hover_color, '15%' );
$noo_site_link_color_fade_50   = fade( $noo_site_link_hover_color, '50%' );

$default_font_color = noo_default_text_color();
$default_headings_color = noo_default_headings_color();

$noo_typo_use_custom_fonts_color = noo_get_option( 'noo_typo_use_custom_fonts_color', false );
$noo_typo_body_font_color = $noo_typo_use_custom_fonts_color ? noo_get_option( 'noo_typo_body_font_color', $default_font_color ) : $default_font_color;
$noo_typo_headings_font_color = $noo_typo_use_custom_fonts_color ? noo_get_option( 'noo_typo_headings_font_color', '' ) : '';

$noo_header_custom_nav_font = noo_get_option( 'noo_header_custom_nav_font', false );
$noo_header_nav_link_color = $noo_header_custom_nav_font ? noo_get_option( 'noo_header_nav_link_color', '' ) : '';
$noo_header_nav_link_hover_color = $noo_header_custom_nav_font ? noo_get_option( 'noo_header_nav_link_hover_color', $noo_site_link_hover_color ) : $noo_site_link_hover_color;

$noo_site_hide_icon = noo_get_option( 'noo_site_hide_icon' , false);
$noo_site_custom_icon = noo_get_image_option( 'noo_site_custom_icon', '' );

?>

body {
color: <?php echo esc_html($noo_typo_body_font_color); ?>;
}
<?php if($noo_typo_headings_font_color != '') : ?>
	h1, h2, h3, h4, h5, h6,
	.h1, .h2, .h3, .h4, .h5, .h6,
	h1 a, h2 a, h3 a, h4 a, h5 a, h6 a,
	.h1 a, .h2 a, .h3 a, .h4 a, .h5 a, .h6 a {
	color: <?php echo esc_html($noo_typo_headings_font_color); ?>;
	}
<?php endif; ?>

h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
.h1 a:hover, .h2 a:hover, .h3 a:hover, .h4 a:hover, .h5 a:hover, .h6 a:hover {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

input[type="radio"]:checked:before{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
input[type="file"]:focus, input[type="radio"]:focus, input[type="checkbox"]:focus{
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
/* Global Link */
/* ====================== */
a {
color: <?php echo esc_html($noo_site_link_color); ?>;
}
a:hover,
a:focus,
.text-primary,
a.text-primary:hover,
.noo-page-heading .noo-page-breadcrumb ul li a:hover {
color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.btn-default:hover,
.btn-default:focus,
.btn-default:active,
.btn-default.active,
.open > .dropdown-toggle.btn-default,
.bg-primary,
.btn-default:hover {
background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
a.bg-primary:hover {
background-color: <?php echo esc_html($noo_site_link_color_darken_15); ?>;
}
.bg-primary-overlay {
background: <?php echo fade($noo_site_link_hover_color, '90%'); ?>;
}

<?php if( $noo_site_custom_icon != '' ) : ?>
.custom-title-home,
.custom-title-home-center {
background-image: url("<?php echo esc_html($noo_site_custom_icon); ?>");
}
<?php endif; ?>

<?php if ( $noo_site_hide_icon ) : ?>
.custom-title-home,
.custom-title-home-center {
background-position: -100px; padding-top:0;
}
<?php else : ?>
.custom-title-home {
background-position: inherit; padding-top: 55px;
}
.custom-title-home-center {
background-position: top center; padding-top: 55px;
}
<?php endif; ?>

/* Navigation Color */
/* ====================== */

/* Default menu style */
.noo-menu li > a {
color: <?php echo esc_html($noo_header_nav_link_color); ?>;
}
.noo-menu li > a:hover,
.noo-menu li > a:active,
.noo-menu li.current-menu-item > a {
color: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
}

/* NavBar: Link */
<?php  if($noo_header_nav_link_color != '') : ?>
	.navbar-nav li > a {
		color: <?php echo esc_html($noo_header_nav_link_color); ?>;
	}
<?php endif; ?>

body.page-menu-white .navbar:not(.navbar-fixed-top) .navbar-nav > li.current-menu-item > a, 
body.page-menu-white .navbar:not(.navbar-fixed-top) .navbar-nav > li.current-menu-parent > a, 
body.page-menu-white .navbar:not(.navbar-fixed-top) .navbar-nav > li.active > a,
body.page-menu-white .navbar:not(.navbar-fixed-top) .navbar-nav > li > a:hover,
.navbar-nav li > a:hover,
.navbar-nav li > a:focus,
.navbar-nav li:hover > a,
.navbar-nav li.sfHover > a,
.navbar-nav > li.current-menu-item > a,
.navbar-nav > li.current-menu-parent > a{
	color: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
}
@media (min-width: 992px){
	.navbar-fixed-top.navbar-shrink .navbar-nav > li > a:hover,
	.navbar-fixed-top.navbar-shrink .navbar-nav > li.current-menu-item > a, 
	.navbar-fixed-top.navbar-shrink .navbar-nav > li.current-menu-parent > a, 
	.navbar-fixed-top.navbar-shrink .navbar-nav > li.active > a {
	    color: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
	}
}
.navbar-nav > li > a:before {
	background: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
}
<?php if(!noo_get_option('noo_header_logo_overlay', true)) : ?>
.navbar-fixed-top .navbar-brand,
body.page-menu-center-vertical:not(.boxed-layout) .navbar.navbar-default:not(.navbar-fixed-top) .navbar-brand {
	background: transparent;
}
<?php else : ?>
.navbar-fixed-top .navbar-brand,
body.page-menu-center-vertical:not(.boxed-layout) .navbar.navbar-default:not(.navbar-fixed-top) .navbar-brand {
	background: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
}
<?php endif; ?>

/* Border color */
@media (min-width: 992px) {
.navbar-default .navbar-nav.sf-menu > li > ul.sub-menu {
	background: <?php echo esc_html($noo_site_link_color); ?> ;
}
.navbar-default .navbar-nav.sf-menu > li > ul.sub-menu:before,
.navbar-nav.sf-menu > li.align-center > ul.sub-menu:before,
.navbar-nav.sf-menu > li.align-right > ul.sub-menu:before,
.navbar-nav.sf-menu > li.align-left > ul.sub-menu:before,
.navbar-nav.sf-menu > li.full-width.sfHover > a:before {
border-bottom-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
}

/* Dropdown Color */
.navbar-nav ul.sub-menu li > a:hover,
.navbar-nav ul.sub-menu li > a:focus,
.navbar-nav ul.sub-menu li:hover > a,
.navbar-nav ul.sub-menu li.sfHover > a,
.navbar-nav ul.sub-menu li.current-menu-item > a,
.navbar-nav .cat-mega-content .cat-mega-extra .cat-mega-extra-item h3 a:hover,
.navbar-nav .cat-mega-content .cat-mega-extra .cat-mega-extra-item h3 a:focus
{
	background : <?php echo esc_html($noo_site_link_color_darken_5 ); ?>;
}


/* Other Text/Link Color */
/* ====================== */

.hentry.format-quote a:hover, .hentry.format-link a:hover,
.navbar-right .member-links a:hover
{
color: <?php echo esc_html($noo_site_link_hover_color).'!important'; ?>;
}

.loadmore-loading span
{
background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
/*Social*/

.noo-social a:hover,
.single .hentry .noo-social a:hover, .single .hentry .noo-social a:focus {
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Rating Count and Social Count*/
.noo_rating_point,
.button-social.box,
.noo-recent-news .loop-item-featured a:hover:before
{
background: <?php echo esc_html($noo_site_link_color_fade_50); ?>;
}

.rating_item .rating-slider-range .ui-slider-range {
background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
/* WordPress Element */
/* ====================== */

/* Comment */
h2.comments-title span,
.comment-reply-link:hover,
.comment-author a:hover,
.comments-title span {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.ispostauthor{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/*List Posts*/
.noo-slider .slider-control {
background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.standard-blog .rated_count {
background: <?php echo esc_html($noo_site_link_color_fade_50); ?>;
}
.read-more:hover, .read-more:focus{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Post */
.content-meta > span > a:hover,
.hentry.format-quote a:hover,
.hentry.format-link a:hover,
.single .hentry.format-quote .content-title:hover,
.single .hentry.format-link .content-title:hover,
.single .hentry.format-quote a:hover,
.single .hentry.format-link a:hover,
.sticky h2.content-title:before {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.content-thumb:before{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.entry-tags,
.entry-tags a,
.author-connect .connect-button{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.post-navigation .prev-post h4:hover, .post-navigation .next-post h4:hover, .post-navigation .prev-post h4:focus, .post-navigation .next-post h4:focus,
.post-navigation .prev-post h4:hover a, .post-navigation .next-post h4:hover a, .post-navigation .prev-post h4:focus a, .post-navigation .next-post h4:focus a{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.pagination .page-numbers.current,
.pagination a.page-numbers:hover {
border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.content-social a:hover {
color: <?php echo esc_html($noo_site_link_hover_color); ?>;
border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/*Button Loadmore*/

#loader, #loader:before, #loader:after {
border-top-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

@keyframes preload_audio_wave {
0% {height:5px;transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
25% {height:30px;transform:translateY(15px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
50% {height:5px;transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
100% {height:5px;transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
}
@-webkit-keyframes preload_audio_wave {
0% {height:5px;-webkit-transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
25% {height:30px;-webkit-transform:translateY(15px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
50% {height:5px;-webkit-transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
100% {height:5px;-webkit-transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
}

@-moz-keyframes preload_audio_wave {
0% {height:5px;-moz-transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
25% {height:30px;-moz-transform:translateY(15px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
50% {height:5px;-moz-transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
100% {height:5px;-moz-transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
}

@keyframes preload_audio_wave {
0% {height:5px;transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
25% {height:30px;transform:translateY(15px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
50% {height:5px;transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
100% {height:5px;transform:translateY(0px);background:<?php echo esc_html($noo_site_link_hover_color); ?>;}
}

/* Widget */
.wigetized .widget ul li a:hover,
.wigetized .widget ol li a:focus,
.wigetized .widget ul li a:hover,
.wigetized .widget ol li a:focus
{
color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
/* Shortcode */
/* ====================== */

.btn-primary,
.wpcf7-submit,
.form-submit input[type="submit"],
.widget_newsletterwidget .newsletter-submit {
background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.btn-darker:hover, .btn-darker:focus,
.button:hover, .button:focus,
.form-submit input[type="submit"]:hover,
.form-submit input[type="submit"]:focus {
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.btn-primary:hover, .btn-primary:focus, .widget_newsletterwidget .newsletter-submit:hover, .widget_newsletterwidget .newsletter-submit:focus{
	background: <?php echo esc_html($default_font_color); ?>;
}

.noo-post-navi .noo_slider_prev:hover,
.noo-post-navi .noo_slider_next:hover,
.noo-post-navi .noo_slider_prev:focus,
.noo-post-navi .noo_slider_next:focus {
background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.pagination .page-numbers.current {
background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.btn-primary.pressable {
-webkit-box-shadow: 0 4px 0 0 <?php echo esc_html($noo_site_link_color_darken_15); ?>,0 4px 9px rgba(0,0,0,0.75) !important;
box-shadow: 0 4px 0 0  <?php echo esc_html($noo_site_link_color_darken_15); ?>,0 4px 9px rgba(0,0,0,0.75) !important;
}

.btn-link,
.btn.btn-white:hover,
.wpcf7-submit.btn-white:hover,
.widget_newsletterwidget .newsletter-submit.btn-white:hover {
color: <?php echo esc_html($noo_site_link_color); ?>;
}

.btn-link:hover,
.btn-link:focus {
color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.footer-top .noo-social a:hover {
border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/*Style Color for homepage*/

.contact-register .contact-register-header {
    background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.contact-register .contact-register-header .arrow:after {
    border-top: 80px solid <?php echo esc_html($noo_site_link_hover_color); ?>;
}


/* Title Color */
.custom-title,
.custom-title-home,
.custom-title-home-center,
.noo-sidebar .widget-title,
.noo-page-heading .page-title,
.single-noo_class .related-class .related-title,
.noo-event .noo_event_map .event-info h5, .noo-event .noo_event_map .noo-pricing-table .event-info h5,
.single-noo_class .noo_class .timetable_week .day,
.single-noo_class .noo_class .timetable_week .res-sche-content .item-weekday,
.trainer-content .content .content-title{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.custom-title:before,
.custom-title-home:before,
.custom-title-home-center:before,
.noo-sidebar .widget-title:before,
.noo-page-heading .page-title:before,
.single-noo_class .related-class .related-title:before,
.noo-event .noo_event_map .event-info h5:before, .noo-event .noo_event_map .noo-pricing-table .event-info h5:before,
.trainer-content .content .content-title:before{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
/* Home */
.noo-sc-testimonial .testimonial-quote ul li .name{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.noo-sc-testimonial .testimonial-author ul li:after{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.our-services > div:hover .noo-icon,
.videos.posts-loop .loop-thumb .loop-thumb-content .carousel-inner .item > a .icon,
.videos.posts-loop .video .loop-item-wrap .loop-item-content .icon,
.posts-loop.slider .loop-thumb-content .carousel-indicators li.active,
.noo-trainers .hentry .trainer-item-wrap .content-wrap .trainer-category:before,
.noo-trainers .hentry .trainer-item-wrap:hover,
.noo-recent-news .item-featured .icon,
.noo-recent-class .noo-rn-content.info_overlay .item-wrap:hover .item-title,
.noo-pricing-table .noo-pricing-column.featured .pricing-content .pricing-header{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.noo-pricing-table .noo-pricing-column.featured .pricing-content .pricing-header .arrow:after{
	border-top-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.noo-pricing-table .noo-pricing-column .pricing-content .pricing-footer .btn:hover, .noo-pricing-table .noo-pricing-column .pricing-content .pricing-footer .btn:focus, .noo-pricing-table .noo-pricing-column .pricing-content .pricing-footer .widget_newsletterwidget .newsletter-submit:hover, .noo-pricing-table .noo-pricing-column .pricing-content .pricing-footer .widget_newsletterwidget .newsletter-submit:focus,
.noo-pricing-table .noo-pricing-column.featured .pricing-content .pricing-footer .btn, .noo-pricing-table .noo-pricing-column.featured .pricing-content .pricing-footer .widget_newsletterwidget .newsletter-submit{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.videos.posts-loop .loop-thumb .loop-thumb-content .carousel-inner .item > a,
.testimonial-content:before,
.noo-menu-item-cart .noo-minicart .minicart-body .cart-product .cart-product-details .cart-product-title a:hover,
.noo-recent-news .item-title a:hover,
.noo-recent-news .read-more:hover, .noo-recent-news .read-more:focus{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.recent-tweets li a{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>!important;
}
.custom-footer .footer-social a:hover{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.posts-loop .loop-item-category,
.posts-loop .posts-loop-title a.active,
.posts-loop .loop-item-category a,
input[type="checkbox"]:checked:before{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Class */
.noo-responsive-schedule-wrap .res-sche-navigation .next:hover,
.noo-responsive-schedule-wrap .res-sche-navigation .next:focus,
.noo-responsive-schedule-wrap .res-sche-navigation .prev:hover,
.noo-responsive-schedule-wrap .res-sche-navigation .prev:focus,
.noo-class-schedule-shortcode .fc-toolbar .fc-button:focus,
.noo-class-schedule-shortcode .fc-toolbar .fc-button:hover {
	background: <?php echo esc_html($noo_site_link_hover_color); ?> !important;
	color: #fff !important;
}

.noo-responsive-schedule-wrap .res-sche-navigation .prev:focus,
.noo-responsive-schedule-wrap .res-sche-navigation .next:focus,
.noo-responsive-schedule-wrap .res-sche-navigation .prev:hover,
.noo-responsive-schedule-wrap .res-sche-navigation .next:hover,
.noo-class-schedule .class-schedule-filter ul li a.selected, .noo-class-schedule .class-schedule-filter ul li a:hover, .noo-class-schedule .class-schedule-filter ul li a.active{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.noo-class-schedule .fc-toolbar .fc-left .fc-state-default:hover, .noo-class-schedule .fc-toolbar .fc-right .fc-state-default:hover, .noo-class-schedule .fc-toolbar .fc-left .fc-state-default:focus, .noo-class-schedule .fc-toolbar .fc-right .fc-state-default:focus{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.single-noo_class .noo_class .content-meta span,
.noo-class-schedule .fc-view .fc-widget-content .fc-time-grid .fc-time-grid-event .fc-content .fc-trainer{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.noo-recent-class .noo-rn-content.info_overlay .item-featured .infor-overlay .inner span a:hover,
.single .hentry .content-wrap ul li:before,
.single-noo_class .noo_class .content-footer #author-bio .view-profile,
.single-noo_class .single-sidebar .class-info-sidebar > div i,
.single-noo_class .single-sidebar .class-info-sidebar > div span,
.single-noo_class .related-class .noo-class-slider-content .noo-class-slider-item .class-slider-content .class-slider-category a{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.single-noo_class .related-class .slider-indicators a.selected,
.noo-recent-class .noo-rn-content.info_overlay .hvr-bounce-to-bottom:before {
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
/* Event */
.noo-events .event-info > div i,
.noo-event .event-header .content-meta i,
.noo-event ul li:before,
.noo-event .noo_event_map .event-info > div i, .noo-event .noo_event_map .noo-pricing-table .event-info > div i{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Trainer*/
.noo-trainers .masonry-filters ul li a.selected, .noo-trainers .masonry-filters ul li a:hover{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.trainer-details .trainer-info .noo-progress-bar .noo-single-bar .noo-bar,
.trainer-details .trainer-info .trainer-category:before{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.trainer-details .trainer-info .noo-progress-bar .noo-single-bar .label-bar .noo-label-units,
.trainer-details .trainer-info .view_class,
.trainer-content .content ul li:before{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Blog */
.hentry .content-wrap .content-category a,
.widget_class_slider .widget-class-slider-content .noo-class-slider-item .class-slider-content .class-slider-category a{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.widget_categories ul li a:before{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.widget_tag_cloud .tagcloud a:hover, .widget_product_tag_cloud .tagcloud a:hover,
.widget_class_slider .slider-indicators a.selected{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Shop */
.woocommerce ul.products li.product figure .product-wrap .product-overlay{
	background: <?php echo esc_html($noo_site_link_color_fade_50); ?>;
}
.woocommerce ul.products li.product figure .product-wrap .shop-loop-actions a:hover, .woocommerce ul.products li.product figure .product-wrap .shop-loop-actions a:focus{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.woocommerce .cart .button, .woocommerce .cart input.button{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.woocommerce .cart .button:hover, .woocommerce .cart input.button:hover{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.noo-gmaps ~ .contact-info ul li i{
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.woocommerce .star-rating span{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.product-masonry .masonry-filters ul li a.selected{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
	border-bottom-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.posts-loop .loop-item-featured .is_featured {
    background:  <?php echo esc_html($noo_site_link_hover_color); ?>;
}

<?php if( class_exists('Hc_Insert_Html_Widget') ) :?>
	.site div.healcode .trainer_list .trainer_teaches_link, .site div.healcode .class_list .class_offered_link{
		background: <?php echo esc_html($noo_site_link_hover_color); ?>;
	}
	.pricing-footer a:hover, .pricing-footer a:focus{
		background: <?php echo esc_html($noo_site_link_hover_color); ?>;
	}
	.site div.healcode a.hc-button, .site div.healcode input.hc-button, .site div.healcode .hc-actions input[type="submit"]{
		background: <?php echo esc_html($noo_site_link_hover_color); ?>;
	}
<?php endif;?>