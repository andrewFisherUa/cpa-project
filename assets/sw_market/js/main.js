jQuery(document).ready(function(){

	var $ = jQuery;
	resizeThumbs();

	$('body').on('keyup keypress', 'input.only-numbers', function(e) {
      if (e.keyCode == 8 || e.keyCode == 46) {}
      else {
           var letters='1234567890';
           return (letters.indexOf(String.fromCharCode(e.which))!=-1);
        }
    })

	 $(window).load(function() {
		  // The slider being synced must be initialized first
		  $('#flex-thumbnail-1').flexslider({
		    animation: "slide",
		    controlNav: false,
		    animationLoop: false,
		    slideshow: false,
		    itemMargin: 5,
		    asNavFor: '#flex-product-1'
		  });

		  $('#flex-product-1').flexslider({
		    animation: "slide",
		    controlNav: false,
		    animationLoop: false,
		    slideshow: false,
		    sync: "#flex-thumbnail-1"
		  });
	});

	$(window).resize(function() {
		$('.item-height').each(function(){
			var w = $(this).width();
			$(this).find('.products-thumb > a > div').css({
				'height' : w+'px',
				'line-height' : w+'px'
			});

			var hoverImg = $(this).find('.hover-image');

	        mLeft = Math.ceil(hoverImg.width() / -2);   // Note: $(this).width() will not
	        mTop =  Math.ceil(hoverImg.height() / -2); // work for in memory images.

	        hoverImg.css({
				'margin-top' : mTop + 'px',
				'margin-left' : mLeft + 'px',
				'left' : '50%',
				'top' : '50%'
			});
		})

		$('.item_product_in .products-thumb').each(function(){
			var w = $(this).width();
			$(this).find('a > div').css({
				'height' : w+'px',
				'line-height' : w+'px'
			});

			var hoverImg = $(this).find('.hover-image');

	        mLeft = Math.ceil(hoverImg.width() / -2);   // Note: $(this).width() will not
	        mTop =  Math.ceil(hoverImg.height() / -2); // work for in memory images.

	        hoverImg.css({
				'margin-top' : mTop + 'px',
				'margin-left' : mLeft + 'px',
				'left' : '50%',
				'top' : '50%'
			});
		})

		$('.flexslider-product').each(function(){
			var w = $(this).width();
			$(this).find("li > a").css({
				'height' : w+'px',
				'line-height' : w+'px',
				'text-align' : 'center'
			});
		})
	});

	if ($('.star-rating:not(#comment-score)').length) {
		$('.star-rating:not(#comment-score)').each(function(){
			var id = $(this).attr("id");
			$('#'+id).barrating({
		        theme: 'fontawesome-stars',
		        readonly: true
		      });
		})
	}

	if ($('#comment-score.star-rating').length) {
		$("#comment-score.star-rating").barrating({
	        theme: 'fontawesome-stars',
	      });
	}

	// Добавление отзыва
	$(document).on('click', '#review_form #submit', function(){
		var author = $('#review_form #author');
		var comment = $('#review_form #comment');

		$('#review_form .form-group').removeClass('has-error');
		$('#review_form .help-block').fadeOut();
		var error = false;

		var data = {
			name: author.val(),
			content: comment.val(),
			shop_id: $('#review_form #shop_id').val(),
			good_id: $('#review_form #good_id').val(),
			score: $('#review_form #comment-score').val(),
			country_code: $('#review_form #country_code').val(),
			action : "add-comment"
		}

		if (data.name == "") {
			author.parent().addClass('has-error');
			author.siblings(".help-block").fadeIn();
			error = true;
		}

		if (data.content == "") {
			comment.parent().addClass('has-error');
			comment.siblings(".help-block").fadeIn();
			error = true;
		}

		if (error) {
			return false;
		}

		$.ajax({
	        url: '/ajax/manage-comments/',
	        type: 'POST',
	        data: $.param(data),
	        success: function(){
	        	author.val("");
				comment.val("");
				$('#review_form #comment-score').val("5");
				if ($('#thanks-for-reply').length == 0) {
					$('#review_form .comment-reply-title').after("<p id='thanks-for-reply'>Спасибо! Ваш отзыв будет опубликован после модерации</p>");
				}
				$('html, body').animate({
			    	scrollTop: $('#review_form').offset().top
			    }, 150);
	        }
	    })
	})

	// Навигация по отзывам
	$(document).on('click', '#comments .sort-count ul a, #comments a.page-numbers', function(){
		var data = {
			page: $(this).attr('data-page'),
			length: $(this).attr('data-length'),
			product: $('#reviews input[name="product_id"]').val(),
			country_code: $('#reviews input[name="country_code"]').val(),
			shop_id: $('#reviews input[name="shop_id"]').val(),
			action: "paginate"
		}

		$.ajax({
	        url: '/ajax/manage-comments/',
	        type: 'POST',
	        dataType: 'json',
	        data: $.param(data),
	        success: function(response){
	        	$('#comments').html(response.rows).fadeIn(150);

	        	handleOrderBy();
	        	if ($('.star-rating:not(#comment-score)').length) {
					$('.star-rating:not(#comment-score)').each(function(){
						var id = $(this).attr("id");
						$('#'+id).barrating({
					        theme: 'fontawesome-stars',
					        readonly: true
					      });
					})
				}
	        }
	    })
	})

	$(document).on('click', '.woocommerce-review-link', function(){
		$('html, body').animate({
	    	scrollTop: $('#tab-reviews').offset().top - 35
	    }, 300);
	})




	$('.currency_switcher > li > a').click(function(){
		var country_code = $(this).data('country_code');

		$.ajax({
	        url: '/ajax/set-shop-country/',
	        type: 'POST',
	        data: "country_code="+country_code,
	        success: function(){
	          window.location.reload();
	        }
	    })
	})


	$('.sw-woo-tab').removeClass('pre-load');

	// Best Sellers informer
	(function(element){
		var $element = $(element);
		var $slider = $('.slider', $element)

		jQuery('.slider', $element).responsiver({
			interval: 0,
			speed: 1000,
			start: 0,
			step: 1,
			circular: true,
			preload: true,
			fx: 'slide',
			pause: '',
			control:{
				prev: '#sw_woo_slider_bestsells .control-button li[class="preview"]',
				next: '#sw_woo_slider_bestsells .control-button li[class="next"]'
			},
			getColumns: function(element){
				var match = $(element).attr('class').match(/cols-(\d+)/);
				if (match[1]){
					var column = parseInt(match[1]);
				} else {
					var column = 1;
				}
				if (!column) column = 1;
				return column;
			}
		});

		$slider.touchSwipeLeft(function(){
			$slider.responsiver('next');
		});

		$slider.touchSwipeRight(function(){
			$slider.responsiver('prev');
		});
		$('.control-button',$element).removeClass('preload');
	})('#sw_woo_slider_bestsells');

	// Latest informer
	(function(element){
		var $element = $(element);
		var $slider = $('.slider', $element)

		jQuery('.slider', $element).responsiver({
			interval: 0,
			speed: 1000,
			start: 0,
			step: 1,
			circular: true,
			preload: true,
			fx: 'slide',
			pause: '',
			control:{
				prev: '#sw_woo_slider_latest .control-button li[class="preview"]',
				next: '#sw_woo_slider_latest .control-button li[class="next"]'
			},
			getColumns: function(element){
				var match = $(element).attr('class').match(/cols-(\d+)/);
				if (match[1]){
					var column = parseInt(match[1]);
				} else {
					var column = 1;
				}
				if (!column) column = 1;
				return column;
			}
		});

		$slider.touchSwipeLeft(function(){
			$slider.responsiver('next');
		});

		$slider.touchSwipeRight(function(){
			$slider.responsiver('prev');
		});
		$('.control-button',$element).removeClass('preload');
	})('#sw_woo_slider_latest');

	// Related informer
	(function(element){
		var $element = $(element);
		var $slider = $('.slider', $element)

		jQuery('.slider', $element).responsiver({
			interval: 0,
			speed: 1000,
			start: 0,
			step: 1,
			circular: true,
			preload: true,
			fx: 'slide',
			pause: '',
			control:{
				prev: '#sw_woo_slider_related .control-button li[class="preview"]',
				next: '#sw_woo_slider_related .control-button li[class="next"]'
			},
			getColumns: function(element){
				var match = $(element).attr('class').match(/cols-(\d+)/);
				if (match[1]){
					var column = parseInt(match[1]);
				} else {
					var column = 1;
				}
				if (!column) column = 1;
				return column;
			}
		});

		$slider.touchSwipeLeft(function(){
			$slider.responsiver('next');
		});

		$slider.touchSwipeRight(function(){
			$slider.responsiver('prev');
		});
		$('.control-button',$element).removeClass('preload');
	})('#sw_woo_slider_related');
})

// Быстрый просмотр товара
jQuery(document).on('click', '.quick-overview', function(){
	var $ = jQuery;
	var data = {
		'shop_id' : $(this).data('shop'),
		'product_id' : $(this).data('product'),
		'countryCode' : $('body').data('country-code')
	}
	jQuery.ajax({
		type	: "POST",
		cache	: false,
		url		: "/ajax/get-product-overview/",
		data    : $.param(data),
		success: function(data) {
			jQuery.fancybox(data, {
				width: 850,
				height: 510,
				fitToView: false,
				autoSize: false
			});
		}
	});
})


function deleteCookie(name) {
  setCookie(name, "", {
    expires: -1
  })
}

function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options) {
  options = options || {};

  var expires = options.expires;

  if (typeof expires == "number" && expires) {
    var d = new Date();
    d.setTime(d.getTime() + expires * 1000);
    expires = options.expires = d;
  }
  if (expires && expires.toUTCString) {
    options.expires = expires.toUTCString();
  }

  value = encodeURIComponent(value);

  var updatedCookie = name + "=" + value;

  for (var propName in options) {
    updatedCookie += "; " + propName;
    var propValue = options[propName];
    if (propValue !== true) {
      updatedCookie += "=" + propValue;
    }
  }

  document.cookie = updatedCookie;
}

function handleOrderBy() {
	jQuery('ul.orderby.order-dropdown li ul').hide(); //hover in
    jQuery("ul.orderby.order-dropdown li span.current-li-content,ul.orderby.order-dropdown li ul").hover(function() {
        jQuery('ul.orderby.order-dropdown li ul').stop().fadeIn("fast");
    }, function() {
        jQuery('ul.orderby.order-dropdown li ul').stop().fadeOut("fast");
    });

    jQuery('.orderby-order-container ul.sort-count li ul').hide();

    jQuery('.sort-count.order-dropdown li span.current-li,.orderby-order-container ul.sort-count li ul').hover(function(){
        jQuery('.orderby-order-container ul.sort-count li ul').stop().fadeIn("fast");
    },function(){
        jQuery('.orderby-order-container ul.sort-count li ul').stop().fadeOut("fast");
    });

    /*Product listing select box*/
	jQuery('.catalog-ordering .orderby .current-li a').html(jQuery('.catalog-ordering .orderby ul li.current a').html());
	jQuery('.catalog-ordering .sort-count .current-li a').html(jQuery('.catalog-ordering .sort-count ul li.current a').html());
}

jQuery(document).ready(function(){

	var $ = jQuery;

	handleOrderBy();

	$('.ya-tooltip').tooltip();
	// fix accordion heading state
	$('.accordion-heading').each(function(){
		var $this = $(this), $body = $this.siblings('.accordion-body');
		if (!$body.hasClass('in')){
			$this.find('.accordion-toggle').addClass('collapsed');
		}
	});
	/*currency Selectbox*/
	$('.currency_switcher li a').click(function(){
		$current = $(this).attr('data-currencycode');
		jQuery('.currency_w > li > a').html($current);
	});

	// twice click
	$(document).on('click.twice', '.open [data-toggle="dropdown"]', function(e){
		var $this = $(this), href = $this.attr('href');
		e.preventDefault();
		window.location.href = href;
		return false;
	});

    $('#cpanel').collapse();

    $('#cpanel-reset').on('click', function(e) {

    	if (document.cookie && document.cookie != '') {
    		var split = document.cookie.split(';');
    		for (var i = 0; i < split.length; i++) {
    			var name_value = split[i].split("=");
    			name_value[0] = name_value[0].replace(/^ /, '');

    			if (name_value[0].indexOf(cpanel_name)===0) {
    				$.cookie(name_value[0], 1, { path: '/', expires: -1 });
    			}
    		}
    	}

    	location.reload();
    });

	$('#cpanel-form').on('submit', function(e){
		var $this = $(this), data = $this.data(), values = $this.serializeArray();

		var checkbox = $this.find('input:checkbox');
		$.each(checkbox, function() {

			if( !$(this).is(':checked') ) {
				name = $(this).attr('name');
				name = name.replace(/([^\[]*)\[(.*)\]/g, '$1_$2');

				$.cookie( name , 0, { path: '/', expires: 7 });
			}

		})

		$.each(values, function(){
			var $nvp = this;
			var name = $nvp.name;
			var value = $nvp.value;

			if ( !(name.indexOf(cpanel_name + '[')===0) ) return ;

			//console.log('name: ' + name + ' -> value: ' +value);
			name = name.replace(/([^\[]*)\[(.*)\]/g, '$1_$2');

			$.cookie( name , value, { path: '/', expires: 7 });

		});

		location.reload();

		return false;

	});

	$('a[href="#cpanel-form"]').on( 'click', function(e) {
		var parent = $('#cpanel-form'), right = parent.css('right'), width = parent.width();

		if ( parseFloat(right) < -10 ) {
			parent.animate({
				right: '0px',
			}, "slow");
		} else {
			parent.animate({
				right: '-' + width ,
			}, "slow");
		}

		if ( $(this).hasClass('active') ) {
			$(this).removeClass('active');
		} else $(this).addClass('active');

		e.preventDefault();
	});
	jQuery(function($){
	// back to top
	$("#ya-totop").hide();
	$(function () {
		var wh = $(window).height();
		var whtml = $(document).height();
		$(window).scroll(function () {
			if ($(this).scrollTop() > whtml/10) {
					$('#ya-totop').fadeIn();
				} else {
					$('#ya-totop').fadeOut();
				}
			});
		$('#ya-totop').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
			});
	});
	// end back to top
	});
	//jQuery('.currency_w .currency_switcher li a').html(jQuery('.currency_w .currency_switcher li.current a').html());
	jQuery('.currency_w .current-li a').html(jQuery('.currency_w ul li.current a').html());
	jQuery('.fancybox').fancybox({
		'width'     : 800,
		'height'   : '500',
		'autoSize' : false,
		'maxWidth': 800,
	});
		$('.currency_switcher li a').click(function(){
			$current = $(this).attr('data-currencycode');
			jQuery('.currency_w > li > a').html($current);
		});
	// lavalamp
	$.fn.lavalamp = function(options){
		var defaults = {
    			elm_class: 'active',
				durations: 400
 		    },
            settings = $.extend(defaults, options);
		this.each( function(){
			var elm = ('> li');
			var current_check = $(elm, this).hasClass( settings.elm_class );
			current = elm + '.' + settings.elm_class;
			if( current_check ){
				var $this=jQuery(this), left0 = $(this).offset().left, dleft0 = $(current, this).offset().left - left0, dwidth0 = $('>ul>li.active', this).width();
				$(this).append('<div class="floatr"></div>');
				var $lava = jQuery('.floatr', $this), move = function(l, w){
					$lava.stop().animate({
						left: l,
						width: w
					}, {
						duration: settings.durations,
						easing: 'linear'
					});
				};

				var $li = jQuery('>li', this);
				//console.log( $li );
				// 1st time

				move(dleft0, dwidth0);
				$lava.show();
				$li.hover(function(e){
					var dleft = $(this).offset().left - left0;
					var dwidth = $(this).width();
					//console.log(dleft);
					move(dleft, dwidth);
				}, function(e){
					move(dleft0, dwidth0);
				});
			}
		});
	}
	jQuery(document).ready(function(){
		var currency_show = jQuery('ul.currency_switcher li a.active').html();
		jQuery('.currency_to_show').html(currency_show);
		/* Search */
		var submitIcon = jQuery('.searchsubmit');
		var inputBox = jQuery('.search-query');
		var searchBox = jQuery('.form-search');
		var isOpen = false;
		submitIcon.click(function(){
			if(isOpen == false){
				searchBox.addClass('searchbox-open');
				inputBox.focus();
				isOpen = true;
			} else {
				searchBox.removeClass('searchbox-open');
				inputBox.focusout();
				isOpen = false;
			}
		});
		 submitIcon.mouseup(function(){
			return false;
		});
		searchBox.mouseup(function(){
			return false;
		});
		jQuery(document).mouseup(function(){
			if(isOpen == true){
				jQuery('.searchsubmit').css('display','block');
				submitIcon.click();
			}
		});
	});
	/* Language select */
	$('#lang_sel ul > li > a').click(function(){
		$('#lang_sel ul > li ul').slideToggle();
	});

	$current ='';

	$('#lang_sel ul > li > ul li a').on('click',function(){

		//console.log($(this).html());
		$current = $(this).html();

		$('#lang_sel ul > li > a.lang_sel_sel').html($current);

		$a = $.cookie('lang_select', $current, { expires: 7, path: '/'});

	});

	if( $.cookie('lang_select') && $.cookie('lang_select').length > 0 ) {
		$('#lang_sel ul > li > a.lang_sel_sel').html($.cookie('lang_select'));
	}

	$('.yt_language .yt_lg_active').on('click', function(){
		if($('.yt_list_lg').hasClass('open')){
			$('.yt_list_lg').removeClass('open');
		}else{
			$('.yt_list_lg').addClass('open');
		}
	});

	$current_lg = '';

	$('.yt_language .yt_list_lg > li').on('click', function() {
		$check = 1;
		if($check == 1){
			$current_lg = $(this).children().html();
			$('.yt_language .yt_lg_active' ).html($current_lg);
			$a = $.cookie('lang_select', $current_lg, { expires: 7, path: '/'});
			$('.yt_lg_active a').attr('href','#');
		}
	});

	if( $.cookie('lang_select') && $.cookie('lang_select').length > 0 ) {
		$('.yt_language .yt_lg_active').html($.cookie('lang_select'));
	}

	// $("body:not('.yt_language')").on('click', function () {
 //        if (typeof $check == 'undefined') {
 //            return;
 //        }
 //        if ($check == 1) {
 //            $check = 0;
 //            return;
 //        }
 //        if ($('.yt_list_lg').hasClass('open')) {
 //            $('.yt_list_lg').removeClass('open');
 //        }
 //    });

	jQuery(document).ready(function(){
		jQuery('.wpcf7-form-control-wrap').hover(function(){
			$(this).find('.wpcf7-not-valid-tip').css('display', 'none');
		});
	 });
})


jQuery(document).ready(function(){
	var $ = jQuery;
	$.fn.megamenu = function(options) {
		options = jQuery.extend({
			  wrap:'.nav-mega',
			  speed: 300,
			  justify: "",
			  mm_timeout: 200
		  }, options);
		var menuwrap = $(this);
		buildmenu(menuwrap);
		// Build menu
		function buildmenu(mwrap){
			mwrap.find('li').each(function(){
				var menucontent 		= $(this).find(".dropdown-menu");
				var menuitemlink 		= $(this).find(".item-link:first");
		    	var menucontentinner 	= $(this).find(".nav-level1");
		    	var mshow_timer = 0;
		    	var mhide_timer = 0;
		     	var li = $(this);
		     	var islevel1 = (li.hasClass('level1'))?true:false;
				var havechild = (li.hasClass('dropdown'))?true:false;
				if(menucontent){
		     		menucontent.hide();
		     	}
				li.mouseenter(function(el){
					el.stopPropagation();
					clearTimeout(mhide_timer);
					clearTimeout(mshow_timer);
					addHover(li);
					if(havechild){
						positionSubMenu(li, islevel1);
						mshow_timer = setTimeout(function(){ //Emulate HoverIntent
							showSubMenu(li, menucontent, menucontentinner);
						}, options.mm_timeout);
					}
				}).mouseleave(function(el){ //return;
					clearTimeout(mshow_timer);
					clearTimeout(mhide_timer);
					if(havechild){
						mhide_timer = setTimeout(function(){ //Emulate HoverIntent
							hideSubMenu(li, menucontent, menucontentinner);
						}, options.mm_timeout);

						//hideSubMenu(li, menucontent, menucontentinner);
					}
					removeHover(li);
			    });
			});
		}
		// Show Submenu
		function showSubMenu(li, mcontent, mcontentinner){
			mcontentinner.animate({
				  opacity: 1
				}, 100, function() {
			});
			mcontent.css('opacity','1').stop(true, true).slideDown({ duration: options.speed});
		}
		// Hide Submenu
		function hideSubMenu(li, mcontent, mcontentinner){
			mcontentinner.animate({
				  opacity: 0
				}, 2*options.mm_timeout, function() {
			});
			mcontent.slideUp({ duration: options.mm_timeout});
		}
		// Add class hover to li
		function addHover(el){
			$(el).addClass('hover');

		}
		// Remove class hover to li
		function removeHover(el){
			$(el).removeClass('hover');
		}
		// Position Submenu
		function positionSubMenu(el, islevel1){
			menucontent 		= $(el).find(".dropdown-menu");
			menuitemlink 		= $(el).find(".item-link:first");
	    	menucontentinner 	= $(el).find(".nav-level1");
	    	wrap_O				= menuwrap.offset().left;
	    	wrap_W				= menuwrap.outerWidth();
	    	menuitemli_O		= menuitemlink.parent('li').offset().left;
	    	menuitemli_W		= menuitemlink.parent('li').outerWidth();
	    	menuitemlink_H		= menuitemlink.outerHeight();
	    	menuitemlink_W		= menuitemlink.outerWidth();
	    	menuitemlink_O		= menuitemlink.offset().left;
	    	menucontent_W		= menucontent.outerWidth();

			if (islevel1) {
				menucontent.css({
					'top': menuitemlink_H + "px",
					'left': menuitemlink_O - menuitemli_O + 'px'
				})

				if(options.justify == "left"){
					var wrap_RE = wrap_O + wrap_W;
											// Coordinates of the right end of the megamenu object
					var menucontent_RE = menuitemlink_O + menucontent_W;
											// Coordinates of the right end of the megamenu content
					if( menucontent_RE >= wrap_RE ) { // Menu content exceeding the outer box
						menucontent.css({
							'left':wrap_RE - menucontent_RE + menuitemlink_O - menuitemli_O + 'px'
						}); // Limit megamenu inside the outer box
					}
				} else if( options.justify == "right" ) {
					var wrap_LE = wrap_O;
											// Coordinates of the left end of the megamenu object
					var menucontent_LE = menuitemlink_O - menucontent_W + menuitemlink_W;
											// Coordinates of the left end of the megamenu content
					if( menucontent_LE <= wrap_LE ) { // Menu content exceeding the outer box
						menucontent.css({
							'left': wrap_O
							- (menuitemli_O - menuitemlink_O)
							- menuitemlink_O + 'px'
						}); // Limit megamenu inside the outer box
					} else {
						menucontent.css({
							'left':  menuitemlink_W
							+ (menuitemlink_O - menuitemli_O)
							- menucontent_W + 'px'
						}); // Limit megamenu inside the outer box
					}
				}
			}else{
				_leftsub = 0;
				menucontent.css({
					'top': menuitemlink_H*0 +"px",
					'left': menuitemlink_W + _leftsub + 'px'
				})

				if(options.justify == "left"){
					var wrap_RE = wrap_O + wrap_W;
											// Coordinates of the right end of the megamenu object
					var menucontent_RE = menuitemli_O + menuitemli_W + _leftsub + menucontent_W;
											// Coordinates of the right end of the megamenu content
					//console.log(menucontent_RE+' vs '+wrap_RE);
					if( menucontent_RE >= wrap_RE ) { // Menu content exceeding the outer box
						menucontent.css({
							'left': _leftsub - menucontent_W + 'px'
						}); // Limit megamenu inside the outer box
					}
				} else if( options.justify == "right" ) {
					var wrap_LE = wrap_O;
											// Coordinates of the left end of the megamenu object
					var menucontent_LE = menuitemli_O - menucontent_W + _leftsub;
											// Coordinates of the left end of the megamenu content
					//console.log(menucontent_LE+' vs '+wrap_LE);
					if( menucontent_LE <= wrap_LE ) { // Menu content exceeding the outer box
						menucontent.css({
							'left': menuitemli_W - _leftsub + 'px'
						}); // Limit megamenu inside the outer box
					} else {
						menucontent.css({
							'left':  - _leftsub - menucontent_W + 'px'
						}); // Limit megamenu inside the outer box
					}
				}
			}
		}
	};
	jQuery(function($){
		$('.nav-mega').megamenu({
			'wrap':'#primary-menu .navbar-inner'
		});
	});


	$(document).ready(function() {
		if($('body').hasClass('home')|| $('body').hasClass('page-template-home_page1') || $('body').hasClass('page-template-home_page2') || $('body').hasClass('page-template-home_page4')) {
			$('.vertical-megamenu').addClass('mn_home');
		} else {
			$('.vertical-megamenu').removeClass('mn_home');
			$('.vertical_megamenu').addClass('not_home');
		}

		$('.currency_w >li a').click(function() {
			$('.currency_switcher').toggleClass('open');
		});


		$('.top-search .search-cate .sl_option' ).on( 'change',function(){
			var $value = $(this).val();
			$( '.topsearch-entry .sproduct-cat' ).val($value);
			console.log( $value );
		});

		$('.top-search .ic-search').click(function() {
			$('.form-search').toggleClass('open');
		});

		$('.vertical_megamenu .widget-inner h3').click(function() {
			$('.vertical-megamenu').toggleClass('open');
		});

		$('.menu-header').click(function() {
			$('.ya-selectmenu').toggleClass('open');
		});

		$(document).on('click touchstart ','.mini_cart_icon', function(){
			//alert('touchstart');
			var wrapp_minicart = $(this).siblings('.wrapp-minicart');
			// if(wrapp_minicart.hasClass('open')) {
				// alert('hasOpen- removeOpen');
				// wrapp_minicart.removeClass('open');
			// }
			// else{
				// alert('noOpen- addOpen');
				// wrapp_minicart.addClass('open');

			// }

			wrapp_minicart.toggleClass('open');

		});
		$(document).on('click touchstart touchend','.cart-contents', function(e){
			if($(window).width() <= 1024  && $(window).width() > 767){
				e.preventDefault();
				return;
			}
		});
	});

    jQuery('.page-template-home_page1 .vertical-megamenu')
        .find(' > li:gt(14) ') //you want :gt(4) since index starts at 0 and H3 is not in LI
        .hide()
        .end()
        .each(function(){
            if($(this).children('li').length > 14){ //iterates over each UL and if they have 5+ LIs then adds Show More...
                $(this).append(
                    $('<li><a class="open-more-cat">Еще категории</a></li>')
                        .addClass('showMore')
                        .click(function(){
                            if($(this).siblings(':hidden').length > 0){
                                $(this).html('<a class="close-more-cat">Свернуть меню </a>').siblings(':hidden').show(400);
                            }else{
                                $(this).html('<a class="open-more-cat">Еще категории </a>').show().siblings('li:gt(14)').hide(400);
                            }
                        })
                );
            }
        });

        $('document').ready(function() {
        	$('.nav-mega-horizontal > li').hover(function() {
    			var offset_this = $(this).offset();

    			var offset_left = 0;
    			if( offset_this){
    				offset_left = offset_this.left;
    			}
    			var $width_windows = $(window).width();
    			var $width_this = $(this).width();
    			var $offset_right = $width_windows - offset_left -$width_this;

    			var $width_ul = $(this).children('ul').width();
    			if($width_ul){
    				if($width_ul > $offset_right){
    					if($(this).hasClass('left')){
    						$(this).removeClass('left');
    					}
    					$(this).addClass('right');
    				} else{
						if($(this).hasClass('right')){
    						$(this).removeClass('right');
    					}
    					$(this).addClass('left');
    				}
    			}
        	});

        	$('.nav-mega-horizontal > li .dropdown-menu > li').hover(function() {
        		var offset_this = $(this).offset();

    			var offset_left = 0;
    			if( offset_this){
    				offset_left = offset_this.left;
    			}
    			var $width_windows = $(window).width();

    			var $width_this = $(this).width();
    			var $offset_right = $width_windows - offset_left - $width_this;

    			if($(this).children('ul')) {
    				var $width_ul = $(this).children('ul').width();
    			}

    			if($width_ul){
    				if($width_ul > $offset_right){
    					if($(this).hasClass('left-child')){
    						$(this).removeClass('left-child');
    					}
    					$(this).addClass('right-child');
    				} else{
						if($(this).hasClass('right-child')){
    						$(this).removeClass('right-child');
    					}
    					$(this).addClass('left-child');
    				}
    			}
			});

        });
})


function resizeThumbs() {
	var $ = jQuery;

	$('.item-height').each(function(){
		var w = $(this).width();
		$(this).find('.products-thumb > a > div').css({
			'height' : w+'px',
			'line-height' : w+'px'
		});

		var hoverImg = $(this).find('.hover-image');

		hoverImg.load(function() {
	        mLeft = Math.ceil(this.width / -2);   // Note: $(this).width() will not
	        mTop =  Math.ceil(this.height / -2); // work for in memory images.

	        $(this).css({
				'margin-top' : mTop + 'px',
				'margin-left' : mLeft + 'px',
				'left' : '50%',
				'top' : '50%'
			});
	    });
	})

	$('.item_product_in .products-thumb').each(function(){
		var w = $(this).width();
		$(this).find('a > div').css({
			'height' : w+'px',
			'line-height' : w+'px'
		});

		var hoverImg = $(this).find('.hover-image');

		hoverImg.load(function() {
	        mLeft = Math.ceil(this.width / -2);   // Note: $(this).width() will not
	        mTop =  Math.ceil(this.height / -2); // work for in memory images.

	        $(this).css({
				'margin-top' : mTop + 'px',
				'margin-left' : mLeft + 'px',
				'left' : '50%',
				'top' : '50%'
			});
	    });
	})
}