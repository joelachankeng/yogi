/*!
* Simple jQuery Equal Heights
*
* Copyright (c) 2013 Matt Banks
* Dual licensed under the MIT and GPL licenses.
* Uses the same license as jQuery, see:
* http://docs.jquery.com/License
*
* @version 1.5.1
*/
(function($) {
	"use strict";
    $.fn.equalHeights = function() {
        var maxHeight = 0,
            $this = $(this);

        $this.each( function() {
            var height = $(this).innerHeight();

            if ( height > maxHeight ) { maxHeight = height; }
        });

        return $this.css('height', maxHeight);
    };

    // auto-initialize plugin
    $('[data-equal]').each(function(){
        var $this = $(this),
            target = $this.data('equal');
        // $this.find(target).equalHeights();
        // Using imagesLoaded to fix the height issues.
        imagesLoaded($(this),function(){
			$this.find(target).equalHeights();
		});
    });

})(jQuery);

/*!
 * NOO Site Script.
 *
 * Javascript used in NOO-Framework
 * This file contains base script used on the frontend of NOO theme.
 *
 * @package    NOO Framework
 * @subpackage NOO Site
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */
// =============================================================================

;(function($){
	"use strict";
	var nooGetViewport = function() {
	    var e = window, a = 'inner';
	    if (!('innerWidth' in window )) {
	        a = 'client';
	        e = document.documentElement || document.body;
	    }
	    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
	};
	var nooGetURLParameters = function(url) {
	    var result = {};
	    var searchIndex = url.indexOf("?");
	    if (searchIndex == -1 ) return result;
	    var sPageURL = url.substring(searchIndex +1);
	    var sURLVariables = sPageURL.split('&');
	    for (var i = 0; i < sURLVariables.length; i++)
	    {       
	        var sParameterName = sURLVariables[i].split('=');      
	        result[sParameterName[0]] = sParameterName[1];
	    }
	    return result;
	};
	
	$.fn.nooLoadmore = function(options,callback){
		var defaults = {
				contentSelector: null,
				contentWrapper:null,
				nextSelector: "div.navigation a:first",
				navSelector: "div.navigation",
				itemSelector: "div.post",
				dataType: 'html',
				finishedMsg: "<em>Congratulations, you've reached the end of the internet.</em>",
				loading:{
					speed:'fast',
					start: undefined
				},
				state: {
			        isDuringAjax: false,
			        isInvalidPage: false,
			        isDestroyed: false,
			        isDone: false, // For when it goes all the way through the archive.
				    isPaused: false,
				    isBeyondMaxPage: false,
				    currPage: 1
				}
		};
		var options = $.extend(defaults, options);
		
		return this.each(function(){
			var self = this;
			var $this = $(this),
				wrapper = $this.find('.loadmore-wrap'),
				action = $this.find('.loadmore-action'),
				btn = action.find(".btn-loadmore"),
				loading = action.find('.loadmore-loading');
			
			options.contentWrapper = options.contentWrapper || wrapper;
				
			var _determinepath = function(path){
				if (path.match(/^(.*?)\b2\b(.*?$)/)) {
	                path = path.match(/^(.*?)\b2\b(.*?$)/).slice(1);
	            } else if (path.match(/^(.*?)2(.*?$)/)) {
	                if (path.match(/^(.*?page=)2(\/.*|$)/)) {
	                    path = path.match(/^(.*?page=)2(\/.*|$)/).slice(1);
	                    return path;
	                }
	                path = path.match(/^(.*?)2(.*?$)/).slice(1);
	
	            } else {
	                if (path.match(/^(.*?page=)1(\/.*|$)/)) {
	                    path = path.match(/^(.*?page=)1(\/.*|$)/).slice(1);
	                    return path;
	                } else {
	                	options.state.isInvalidPage = true;
	                }
	            }
				return path;
			}
			if(!$(options.nextSelector).length){
				return;
			}
			
			
			// callback loading
			options.callback = function(data, url) {
	            if (callback) {
	                callback.call($(options.contentSelector)[0], data, options, url);
	            }
	        };
	        
	        options.loading.start = options.loading.start || function() {
				 	btn.hide();
	                $(options.navSelector).hide();
	                loading.show(options.loading.speed, $.proxy(function() {
	                	loadAjax(options);
	                }, self));
	         };
			
			var loadAjax = function(options){
				var path = $(options.nextSelector).attr('href');
					path = _determinepath(path);
				
				var callback=options.callback,
					desturl,frag,box,children,data;
				
				options.state.currPage++;
				// Manually control maximum page
	            if ( options.maxPage !== undefined && options.state.currPage > options.maxPage ){
	            	options.state.isBeyondMaxPage = true;
	                return;
	            }
	            desturl = path.join(options.state.currPage);
	            box = $('<div/>');
	            box.load(desturl + ' ' + options.itemSelector,undefined,function(responseText){
	            	children = box.children();
	            	if (children.length === 0) {
	            		//loading.hide();
	            		btn.hide();
	            		action.append('<div style="margin-top:5px;">' + options.finishedMsg + '</div>').animate({ opacity: 1 }, 2000, function () {
	            			action.fadeOut(options.loading.speed);
	                    });
	                    return ;
	                }
	            	frag = document.createDocumentFragment();
	                while (box[0].firstChild) {
	                    frag.appendChild(box[0].firstChild);
	                }
	                $(options.contentWrapper)[0].appendChild(frag);
	                data = children.get();
	                loading.hide();
	                btn.show(options.loading.speed);
	                options.callback(data);
	               
	            });
			}
			
			
			btn.on('click',function(e){
				 e.stopPropagation();
				 e.preventDefault();
				 options.loading.start.call($(options.contentWrapper)[0],options);
			});
		});
	};
	
	
	
	var nooInit = function() {
		
		//Enable swiping...
		var isTouch = 'ontouchstart' in window;
		if(isTouch){
			$(".carousel-inner").swipe( {
				//Generic swipe handler for all directions
				swipeLeft: function(event, direction, distance, duration, fingerCount) {
					$(this).parent().carousel('prev');
				},
				swipeRight: function(event, direction, distance, duration, fingerCount) {
					$(this).parent().carousel('next');
				},
				//Default is 75px, set to 0 for demo so any distance triggers swipe
				threshold:0
			}); 
		}
		if($( '.navbar' ).length){
			var $window = $( window );
			var $body   = $( 'body' ) ;
			var navTop = $( '.navbar' ).offset().top;
			var adminbarHeight = 0;
			if ( $body.hasClass( 'admin-bar' ) ) {
				adminbarHeight = $( '#wpadminbar' ).outerHeight();
			}
			var lastScrollTop = 0,
				navHeight = 0,
				defaultnavHeight = $( '.navbar-nav' ).outerHeight();
			
			function adjustModalMaxHeightAndPosition(){
				$('.modal').each(function(){
					if($(this).find('.modal-dialog').hasClass('modal-dialog-center')){
				        if($(this).hasClass('in') === false){
				            $(this).show(); /* Need this to get modal dimensions */
				        };
				        var contentHeight = nooGetViewport().height - 60;
				        var headerHeight = $(this).find('.modal-dialog-center .modal-header').outerHeight() || 2;
				        var footerHeight = $(this).find('.modal-dialog-center .modal-footer').outerHeight() || 2;
	
				        $(this).find('.modal-dialog-center .modal-content').css({
				            'max-height': function () {
				                return contentHeight;
				            }
				        });
	
				        $(this).find('.modal-dialog-center .modal-body').css({
				            'max-height': function () {
				                return (contentHeight - (headerHeight + footerHeight));
				            }
				        });
	
				        $(this).find('.modal-dialog-center').css({
				            'margin-top': function () {
				                return -($(this).outerHeight() / 2);
				            },
				            'margin-left': function () {
				                return -($(this).outerWidth() / 2);
				            }
				        });
				        if($(this).hasClass('in') === false){
				            $(this).hide(); /* Hide modal */
				        };
					}
			    });
			}
			if (nooGetViewport().height >= 320){
			    $(window).resize(adjustModalMaxHeightAndPosition).trigger("resize");
			}
			
			var navbarInit = function () {
				if(nooGetViewport().width > 992){
					var $this = $( window );
					var $navbar = $( '.navbar' );

					if ( $navbar.hasClass( 'fixed-top' ) ) {
						var navFixedClass = 'navbar-fixed-top';
						if( $navbar.hasClass( 'shrinkable' )  && !$body.hasClass('one-page-layout')) {
							navFixedClass += ' navbar-shrink';
						}
						var adminbarHeight = 0;
						if ( $body.hasClass( 'admin-bar' ) ) {
							adminbarHeight = $( '#wpadminbar' ).outerHeight();
						}
						var checkingPoint = navTop;// + defaultnavHeight;

						if ( ($this.scrollTop() + adminbarHeight) > checkingPoint ) {
							if( $navbar.hasClass( 'navbar-fixed-top' ) ) {
								return;
							}

							if( ! $navbar.hasClass('navbar-fixed-top') ) {								
								navHeight = $navbar.hasClass( 'shrinkable' ) ? Math.max(Math.round($( '.navbar-nav' ).outerHeight() - ($this.scrollTop() + adminbarHeight) + navTop),60) : $( '.navbar-nav' ).outerHeight();
								if( $body.hasClass('page-menu-center-vertical') ) {
									// $navbar.closest('.noo-header').css({'height': '1px'});
									$navbar.closest('.noo-header').css({'position': 'relative'});
								} else {
									$('.navbar-wrapper').css({'min-height': navHeight+'px'});
								}

								$navbar.closest('.noo-header').css({'position': 'relative'});
								$navbar.css({'min-height': navHeight+'px'});
								$navbar.find('.navbar-nav > li > a').css({'line-height': navHeight+'px'});
								$navbar.find('.navbar-brand').css({'height': navHeight+'px'});
								$navbar.find('.navbar-brand img').css({'max-height': navHeight+'px'});
								$navbar.find('.navbar-brand').css({'line-height': navHeight+'px'});
								$navbar.addClass( navFixedClass );
								$navbar.css('top', adminbarHeight);

								return;
							}
						}

						$navbar.removeClass( navFixedClass );
						$navbar.css({'top': ''});
						
						if( $body.hasClass('page-menu-center-vertical') ) {
							$navbar.closest('.noo-header').css({'height': ''});
							$navbar.closest('.noo-header').css({'position': ''});
						}
						$('.navbar-wrapper').css({'min-height': 'none'});
						$navbar.closest('.noo-header').css({'position': ''});
						$navbar.css({'min-height': ''});
						$navbar.find('.navbar-nav > li > a').css({'line-height': ''});
						$navbar.find('.navbar-brand').css({'height': ''});
						$navbar.find('.navbar-brand img').css({'max-height': ''});
						$navbar.find('.navbar-brand').css({'line-height': ''});
					}
				}
			};
			$window.bind('scroll',navbarInit).resize(navbarInit);
			if( $body.hasClass('one-page-layout') ) {
	
				// Scroll link
				$('.navbar-scrollspy > .nav > li > a[href^="#"]').click(function(e) {
					e.preventDefault();
					var target = $(this).attr('href').replace(/.*(?=#[^\s]+$)/, '');
					if (target && ($(target).length)) {
						var position = Math.max(0, $(target).offset().top );
							position = Math.max(0,position - (adminbarHeight + $('.navbar').outerHeight()) + 5);
						
						$('html, body').animate({
							scrollTop: position
						},{
							duration: 800, 
				            easing: 'easeInOutCubic',
				            complete: window.reflow
						});
					}
				});
				
				// Initialize scrollspy.
				$body.scrollspy({
					target : '.navbar-scrollspy',
					offset : (adminbarHeight + $('.navbar').outerHeight())
				});
				
				// Trigger scrollspy when resize.
				$(window).resize(function() {
					$body.scrollspy('refresh');
				});
	
			}
			
		}

		// Slider scroll bottom button
		$('.noo-slider-revolution-container .noo-slider-scroll-bottom').click(function(e) {
			e.preventDefault();
			var sliderHeight = $('.noo-slider-revolution-container').outerHeight();
			$('html, body').animate({
				scrollTop: sliderHeight
			}, 900, 'easeInOutExpo');
		});
		
		//Portfolio hover overlay
		$('body').on('mouseenter', '.masonry-style-elevated .masonry-portfolio.no-gap .masonry-item', function(){
			$(this).closest('.masonry-container').find('.masonry-overlay').show();
			$(this).addClass('masonry-item-hover');
		});
	
		$('body').on('mouseleave ', '.masonry-style-elevated .masonry-portfolio.no-gap .masonry-item', function(){
			$(this).closest('.masonry-container').find('.masonry-overlay').hide();
			$(this).removeClass('masonry-item-hover');
		});
		
		//Init masonry isotope
		$('.masonry').each(function(){
			var self = $(this);
			var $container = $(this).find('.masonry-container');
			var $filter = $(this).find('.masonry-filters a');
			$container.isotope({
				itemSelector : '.masonry-item',
				transitionDuration : '0.8s',
				masonry : {
					'gutter' : 0
				}
			});
			
			imagesLoaded(self,function(){
				$container.isotope('layout');
			});
			
			$filter.click(function(e){
				e.stopPropagation();
				e.preventDefault();
				
				var $this = jQuery(this);
				// don't proceed if already selected
				if ($this.hasClass('selected')) {
					return false;
				}
				self.find('.masonry-result h3').text($this.text());
				var filters = $this.closest('ul');
				filters.find('.selected').removeClass('selected');
				$this.addClass('selected');
	
				var options = {
					layoutMode : 'masonry',
					transitionDuration : '0.8s',
					'masonry' : {
						'gutter' : 0
					}
				}, 
				key = filters.attr('data-option-key'), 
				value = $this.attr('data-option-value');
	
				value = value === 'false' ? false : value;
				options[key] = value;
	
				$container.isotope(options);
				
			});
		});
		
		// Class filters
		var grop_class_filters ={};
		var onArrange = function () {

			if ( getFilterStr() && $('.load-title').length > 0 ) {
				$.ajax({
	                type: 'POST',
	                url: nooL10n.ajax_url,
	                data: {
	                    action : 'noo_class_get_count_arrange',
	                    filter : getFilterStr(),
	                },
	                success: function(res){
	                    $(".posts-loop-title h3 span").html(res);
	                }
	            });
            }
            else
            {
            	$(".posts-loop-title h3 span").html($('.masonry-item:visible').length);
            }
			if( $('.masonry-item:visible').length <= 0 && $('.loadmore-action .btn-loadmore').is(':visible') ) {
				$('.loadmore-action .btn-loadmore').click();
			}
		}
		if($('.widget-classes-filters').length){
			var class_filters = $('.widget-classes-filters');
			var masonrycontainer = class_filters.closest('body').find('.noo-classes .masonry-container');
			if(masonrycontainer.length){
				var getFilterStr = function ($filter_item) {
					class_filters.find(':input').each( function() {
						var $filter_item = $(this);
						var group = $filter_item.closest('.widget-class-filter').data('group');
						var filterGroup = grop_class_filters[ group ];
						if(!filterGroup)
							filterGroup = grop_class_filters[ group ] = [];

						if($filter_item.is('select')){
							if($filter_item.val()==''){
								delete grop_class_filters[ group ];
							}else{
								grop_class_filters[ group ] = '.'+$filter_item.val();
							}
						}else if($filter_item.is('input[type="checkbox"]')){
							grop_class_filters[ group ] = [];
							$filter_item.closest('.widget-class-filter').find('.widget-class-filter-control').each(function(){
								if($(this).is(':checked')){
									grop_class_filters[ group ].push('.'+$(this).val());
								}
							});
						}
					});

					var filter_arr = [];
					var filter_arr2 = [];
					var filter_string = '';
					$.each(grop_class_filters,function(index,values){
						if($.isArray(values)){
							filter_arr2 = values;
						}else{
							filter_arr.push(values);
						}
					});
					filter_arr = filter_arr.join('');
					var new_filter_arr=[];
					if(filter_arr2.length){
						$.each(filter_arr2,function(k2,v2){
							new_filter_arr.push((v2 + '' + filter_arr));
						});
					}else{
						new_filter_arr.push(filter_arr);
					}
					if(new_filter_arr.length){
						filter_string = new_filter_arr.join(',');
					}else{
						filter_string = '*';
					}
					if(filter_string == ''){
						filter_string = '*';
					}

					return filter_string;
				};

				masonrycontainer.isotope( 'on', 'arrangeComplete', onArrange);
				
				class_filters.find('.widget-class-filter-control').on('change', function () {
					var filter_string = getFilterStr();
					
					var options = {
						layoutMode : 'masonry',
						transitionDuration : '0.8s',
						'masonry' : {
							'gutter' : 0
						}
					}
					options['filter'] = filter_string;
					masonrycontainer.isotope(options);
				});
				
				imagesLoaded(masonrycontainer,function(){
					var filter_string = getFilterStr();
					var options = {
						layoutMode : 'masonry',
						transitionDuration : '0.8s',
						'masonry' : {
							'gutter' : 0
						}
					}
					// if( filter_string != '' && filter_string !='*' ) {
						options['filter'] = filter_string;
						masonrycontainer.isotope(options);
					// }
				});
			}
		}
		
		if($('.masonry').length){
			$('.masonry').each(function(){
				var $this = $(this);
				$this.find('div.pagination').hide();
				$this.find('.loadmore-loading').hide();
				$this.nooLoadmore({
					navSelector  : $this.find('div.pagination'),            
			   	    nextSelector : $this.find('div.pagination a.next'),
			   	    itemSelector : '.loadmore-item',
			   	    contentWrapper: $this.find('.masonry-container'),
			   	    loading:{
						speed:1,
						start: undefined
					},
			   	    finishedMsg  : nooL10n.ajax_finishedMsg
				},function(newElements){
					var masonrycontainer = $this.find('.masonry-container');
					$this.find('.masonry-container').isotope('appended', $(newElements));

					masonrycontainer.isotope( 'on', 'layoutComplete', function() {
						setTimeout(onArrange, 850);
					});
					$(window).unbind('.infscr');

					imagesLoaded(masonrycontainer,function(){
						masonrycontainer.isotope('layout');
					});
				});
			});
		}
		
		
		//Go to top
		$(window).scroll(function () {
			if ($(this).scrollTop() > 500) {
				$('.go-to-top').addClass('on');
			}
			else {
				$('.go-to-top').removeClass('on');
			}
		});
		$('body').on( 'click', '.go-to-top', function () {
			$("html, body").animate({
				scrollTop: 0
			}, 800);
			return false;
		});
		
		//Search
		$('body').on( 'click', '.search-button', function() {
			if ($('.searchbar').hasClass('hide'))
			{
				$('.searchbar').removeClass('hide').addClass('show');
				$('.searchbar #s').focus();
			}
			return false;
		});
		$('body').on('mousedown', $.proxy( function(e){
			var element = $(e.target);
			if(!element.is('.searchbar') && element.parents('.searchbar').length === 0)
			{
				$('.searchbar').removeClass('show').addClass('hide');
			}
		}, this) );
		
		//Shop mini cart
		$(document).on("mouseenter", ".noo-menu-item-cart", function() {
			clearTimeout($(this).data('timeout'));
			$('.searchbar').removeClass('show').addClass('hide');
			$('.noo-minicart').fadeIn(50);
		});
		$(document).on("mouseleave", ".noo-menu-item-cart", function() {
			var t = setTimeout(function() {
				$('.noo-minicart').fadeOut(50);
			}, 400);
			$(this).data('timeout', t);
		});	
		$('.noo-user-navbar-collapse').on('show.bs.collapse',function(){
			if($('.noo-navbar-collapse').hasClass('in')){
				$('.noo-navbar-collapse').collapse('hide');
			}
		});
		$('.noo-navbar-collapse').on('show.bs.collapse',function(){
			if($('.noo-user-navbar-collapse').hasClass('in')){
				$('.noo-user-navbar-collapse').collapse('hide');
			}
		});
		
	};
	$( document ).ready( function () {
		nooInit();
		$('[data-toggle="tooltip"]').tooltip();
		$(".button-social").click(function(){
		    $(".content-share").addClass("active");

		});
		$('body').on('mousedown', $.proxy( function(e){
			var element = jQuery(e.target);
			if(!element.is('.content-share') && element.parents('.content-share').length === 0 )
			{
				$('.content-share').removeClass('active');
			}
			if((!element.is('.tooltip') && element.parents('.tooltip').length === 0) &&
					((!element.is('a[data-toggle="tooltip"].tooltip-share-icon') && element.parents('a[data-toggle="tooltip"].tooltip-share-icon').length === 0)))
			{
				$('[data-toggle="tooltip"]').tooltip('hide');
			}
		}, this) );
		
		//Shop QuickView
		$(document).on('click','.shop-loop-quickview',function(e){
			var $this = $(this);
			$this.addClass('loading');
			$.post(nooL10n.ajax_url,{
				action: 'woocommerce_quickview',
				product_id: $(this).data('product_id')
			},function(responsive){
				$this.removeClass('loading');
				var $modal = $(responsive);
				$('body').append($modal);
				$modal.modal('show');
				$modal.on('hidden.bs.modal',function(){
					$modal.remove();
				});
			});
			e.preventDefault();
			e.stopPropagation();
		});
		
	});
	
	$(document).bind('noo-layout-changed',function(){
		nooInit();	
	});
})(jQuery);

//Author Connect Blog Single
jQuery(document).ready(function($) {
   $('.author-connect .connect-button').on('click',function(){
        $(".author-connect .connect").toggleClass("active");
   });
   jQuery('body').on('mousedown', jQuery.proxy( function(e){
        var element = jQuery(e.target);
        if(!element.is('.author-connect .connect') && element.parents('.author-connect .connect').length === 0 )
        {
            jQuery('.author-connect .connect').removeClass('active');
        }
    }, this) );

   jQuery('.fc-time-grid-event').on('touchstart', function(e){
      "use strict"; //satisfy the code inspectors
        var link = jQuery(this); //preselect the link
        if (link.hasClass('hover')) {
            return true;
        } else {
            link.addClass("hover");
            jQuery('.fc-time-grid-event').not(this).removeClass("hover");
            e.preventDefault();
            return false; //extra, and to make sure the function has consistent return points
        }      
   });
});
