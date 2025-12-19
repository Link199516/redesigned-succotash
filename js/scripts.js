/* global napoleon_vars */

jQuery(function ($) {
	'use strict';

	var $window = $(window);
	var $body = $('body');
	var isRTL = $body.hasClass('rtl');


	jQuery('#commentform').attr("enctype","multipart/form-data");


	/* -----------------------------------------
    Mobile Menu
    ----------------------------------------- */
	jQuery(document).ready(function ($) {
    	$( '.mobile-nav-trigger' ).on( 'click', function( event ) {
	        $('#mobilemenu').toggleClass('open');
	        $('.fly-menu-fade ').toggleClass('active');
	    });

    	$('.fly-menu-fade, .close-btn ').on( 'click', function( event ) {
    		$('#mobilemenu').removeClass('open');
	        $('.fly-menu-fade ').removeClass('active');

    	});


	    $( '.mobile-menu-list .menu-item-has-children > a' ).on( 'click', function( event ) {
	          event.preventDefault();
	          // $('.menu-item-has-children > .sub-menu').slideToggle();
	           $(this).parent().children('.sub-menu').slideToggle();
	           $(this).toggleClass('active');
	    });
	

	});

	/* -----------------------------------------
    Show / Hide header search box
    ----------------------------------------- */

    $( '.header-search-icon' ).on( 'click', function( event ) {
        $('.head-search-form-wrap').slideToggle("400");
    });


	/* -----------------------------------------
	 Sticky Header
	 ----------------------------------------- */
	$('.head-sticky').stick_in_parent({
		parent: 'body',
		sticky_class: 'is-stuck'
	});


	/* -----------------------------------------
	 Product name in header
	 ----------------------------------------- */

	$(document).ready(function () {
        // Get the initial position of the scroll-trigger element
        var $header = $('header.header');
    	var triggerOffset = $header.length ? $header.offset().top : 0;

        // Initially hide the button
        $('.head-mast-row  .product-name , .head-mast-row  .product-price').hide();

        // On scroll, check if the user has scrolled past the trigger element
        $(window).scroll(function () {
            // Calculate the current scroll position
            var scrollPosition = $(window).scrollTop();
            // Compare the scroll position with the trigger element's position
				if (scrollPosition > triggerOffset &&  (!$('.head-mast ').hasClass('thanks-active')) )  {
	                // User has scrolled past the trigger element, hide the button
	                $('.single-product .head-mast-row  .head-nav ,.single-product .head-mast-row  .head-icons').fadeOut(100);
	                $('.head-mast-row  .product-name , .head-mast-row  .product-price').fadeIn(100);
	                 if ($(window).width() < 991 ){
	                 	$('.single-product .head-mast-row  .header-branding-wrap').fadeOut(100);
	                 }
	            } else {
	                // User is above the trigger element, show the button
	                $('.head-mast-row  .product-name , .head-mast-row  .product-price').fadeOut(100);
	                $('.single-product .head-mast-row  .head-nav , .single-product .head-mast-row  .head-icons').fadeIn(100);
	                if ($(window).width() < 991 ){
	                 	$('.single-product .head-mast-row  .header-branding-wrap').fadeIn(100);
	                 }
	            }
        	
            
        });
    });

	/* -----------------------------------------
	Shop filters toggle && Mini Cart visibility
	----------------------------------------- */
	var $filtersWrap    = $('.sidebar-drawer');
	var $filtersToggle  = $('.shop-filter-toggle');
	var $filtersDismiss = $('.sidebar-dismiss');
	var $miniCartTrigger = $('.header-mini-cart-trigger');
	var $miniCart = $('.header-mini-cart-contents');

	function isFiltersVisible() {
		return $filtersWrap.hasClass('sidebar-drawer-visible');
	}

	function dismissFilters(event) {
		if (event) {
			event.preventDefault();
		}
		$filtersWrap.removeClass('sidebar-drawer-visible')
	}

	function displayFilters(event) {
		if (event) {
			event.preventDefault();
		}
		$filtersWrap.addClass('sidebar-drawer-visible');
	}

	$filtersToggle.on('click', displayFilters);
	$filtersDismiss.on('click', dismissFilters);

	function isMiniCartVisible() {
		return $miniCart.is(':visible');
	}

	function dismissMiniCart() {
		$miniCart.addClass('visible');
		$miniCart.fadeOut('fast');
	}

	function displayMiniCart() {
		$miniCart.removeClass('visible');
		$miniCart.fadeIn('fast');
	}

	$miniCartTrigger.on('click', function (event) {
		event.preventDefault();

		if (isMiniCartVisible()) {
			dismissMiniCart();
		} else {
			displayMiniCart();
		}
	});

	/* Event propagations */
	$(document).on('keydown', function (event) {
		if (event.keyCode === 27) {
			dismissFilters(event);
			dismissMiniCart();
		}
	});

	$body
		.on('click', function (event) {
			if (isFiltersVisible()) {
				dismissFilters();
			}

			if (isMiniCartVisible()) {
				dismissMiniCart();
			}

			dismissSearchResults();
		})
		.find('.shop-filter-toggle, ' +
			'.sidebar-drawer, ' +
			'.header-mini-cart-contents, ' +
			'.header-mini-cart-trigger, ' +
			'.category-search-input ',
			'.category-search-select')
		.on('click', function (event) {
			event.stopPropagation();
		});

	/* -----------------------------------------
	 Responsive Videos with fitVids
	 ----------------------------------------- */
	$body.fitVids();

	/* -----------------------------------------
	 Ajax Search
	 ----------------------------------------- */
	var $productSearchForm = $('.category-search-form');
	var $categoriesSelect = $('.category-search-select');
	var $searchInput = $('.category-search-input');
	var $categoryResults = $('.category-search-results');
	var $categoryResultsTemplate = $('.category-search-results-item');
	var $spinner = $('.category-search-spinner');

	function dismissSearchResults() {
		$categoryResults.hide();
	}

	function queryProducts(category, string) {
		return $.ajax({
			url: napoleon_vars.ajaxurl,
			method: 'get',
			data: {
				action: 'napoleon_search_products',
				product_cat: category,
				s: string,
			},
		});
	}

	function queryProductsAndPopulateResults(category, string) {
		if (string.trim().length < 3) {
			dismissSearchResults();
			return;
		}

		$spinner.addClass('visible');

		return queryProducts(category, string)
			.done(function (response) {
				$spinner.removeClass('visible');

				if (response.error) {
					var $errorMessage = $categoryResultsTemplate.clone();
					var errorString = response.errors.join(', ');

					$errorMessage
						.addClass('error')
						.find('.category-search-results-item-title')
						.text('Error: ' + errorString);
					$categoryResults.html($errorMessage).show();

					return;
				}

				var products = response.data;

				if (products.length === 0) {
					var $notFoundMessage = $categoryResultsTemplate.clone();

					$notFoundMessage
						.find('.category-search-results-item-title')
						.text(napoleon_vars.search_no_products);
					$categoryResults.html($notFoundMessage).show();

					return;
				}

				var $items = products.map(function (product) {
					var $template = $categoryResultsTemplate.clone();
					$template.find('a').attr('href', product.url);
					$template.find('.category-search-results-item-title')
						.text(product.title);

					return $template;
				});

				$categoryResults.html($items);
				$categoryResults.show();
			});
	}

	var throttledQuery = throttle(queryProductsAndPopulateResults, 500);

	if ($productSearchForm.hasClass('form-ajax-enabled')) {
		$searchInput.on('change keyup focus', function (event) {
			// Do nothing on arrow up / down as we're using them for navigation
			if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
				return;
			}

			var $this = $(this);
			var string = $this.val();

			if (string.trim().length < 3) {
				dismissSearchResults();
				return;
			}

			throttledQuery($categoriesSelect.val(), $this.val());
		});

		// Bind up / down arrow navigation on search results
		$searchInput.on('keydown', function (event) {
			if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') {
				return;
			}

			var $items = $categoryResults.children();
			var $highlighted = $categoryResults.find('.highlighted');
			var currentIndex = $highlighted.index();

			if ($items.length === 0 || !$items) {
				return;
			}

			if (event.key === 'ArrowDown') {
				var $next = $items.eq(currentIndex + 1);

				if ($next.length) {
					$items.removeClass('highlighted');
					$next.addClass('highlighted');
				}
			}

			if (event.key === 'ArrowUp') {
				var $prev = $items.eq(currentIndex - 1);

				if ($prev.length) {
					$items.removeClass('highlighted');
					$prev.addClass('highlighted');
				}
			}
		});

		// Bind form submit to go the highlighted item on submit
		// instead of normal search
		$productSearchForm.on('submit', function (event) {
			var $highlighted = $categoryResults.find('.highlighted');

			if ($highlighted.length > 0) {
				event.preventDefault();
				window.location = $highlighted.find('a').attr('href');
			}
		});
	}

	/* -----------------------------------------
	Category Slideshow
	----------------------------------------- */

	function getBreakpointsFromClasses(classes) {
		return classes.split(' ').map(function (c) {
			var classData = c.split('-');
			var breakpoint;
			var slideNo;

			if (classData[1] === 'xl') {
				breakpoint = 9999;
				slideNo = 12 / parseInt(classData[2]);
			} else if (classData[1] === 'lg') {
				breakpoint = 1350;
				slideNo = 12 / parseInt(classData[2]);
			} else if (classData[1] === 'md' ) {
				breakpoint = 992;
				slideNo = 12 / parseInt(classData[2]);
			} else if (classData[1] === 'sm') {
				breakpoint = 768;
				slideNo = 12 / parseInt(classData[2]);
			} else if (classData[1] === '12') {
				breakpoint = 576;
				slideNo = 1;
			}

			return {
				breakpoint: breakpoint,
				settings: {
					slidesToShow: slideNo,
					slidesToScroll: slideNo
				}
			}
		});
	}

	function initializeRowSliders($rowSliders) {
		$rowSliders.each(function () {
			var $this = $(this);
			var classes = $this
				.find('div[class^="col"]')
				.first()
				.attr('class');
			var slidesNo = 12 / parseInt(classes.split(' ')[0].split('-')[2]);

			$this.not('.slick-initialized').slick({
				infinite: false,
				slidesToShow: slidesNo,
				slidesToScroll: slidesNo,
				rtl: isRTL,
				appendArrows: $this.parent().parent().find('.row-slider-nav'),
				prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-angle-left"></i></button>',
				nextArrow: '<button type="button" class="slick-next"><i class="fas fa-angle-right"></i></button>',
				responsive: getBreakpointsFromClasses(classes),
			});
		});
	}


	/* -----------------------------------------
	Sticky product image in product page
	----------------------------------------- */
    $(function(){
    if ($(window).width() > 768) {
    	if ($('.head-sticky').length) {
            $('.product-img-gallery, .product-desc ').theiaStickySidebar({
                additionalMarginTop: 100
            });
       } else {
       		$('.product-img-gallery, .product-desc ').theiaStickySidebar({
                additionalMarginTop: 30
            });

       }


            
    }});

	/* -----------------------------------------
	Elementor Init
	----------------------------------------- */
	$(document).on('elementor/render/latest_posts', function(e, data) {
		var $rowSliders = $(this).find('.row-slider');
		initializeRowSliders($rowSliders);
	});

	$(document).on('elementor/render/latest_products', function(e, data) {
		var $rowSliders = $(this).find('.wc-slider .row-items');
		initializeRowSliders($rowSliders);
	});

	$(document).on('elementor/render/post_type_items', function(e, data) {
		var $rowSliders = $(this).find('.row-slider');
		initializeRowSliders($rowSliders);
	});

	$window.on('load', function () {
		initializeRowSliders($('.row-slider, .wc-slider .row-items'));

		/* -----------------------------------------
		 Hero Slideshow
		 ----------------------------------------- */
		var $heroSlideshow = $('.page-hero-slideshow');
		var navigation = $heroSlideshow.data('navigation');
		var effect = $heroSlideshow.data('effect');
		var speed = $heroSlideshow.data('slide-speed');
		var auto = $heroSlideshow.data('autoslide');

		if ($heroSlideshow.length) {
			$heroSlideshow.slick({
				arrows: navigation === 'arrows' || navigation === 'both',
				dots: navigation === 'dots' || navigation === 'both',
				fade: effect === 'fade',
				autoplaySpeed: speed,
				autoplay: auto === true,
				slide: '.page-hero',
				rtl: isRTL,
				appendArrows: '.page-hero-slideshow-nav',
				prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-angle-left"></i></button>',
				nextArrow: '<button type="button" class="slick-next"><i class="fa fa-angle-right"></i></button>',
				responsive: [
					{
						breakpoint: 992,
						settings: {
							dots: true,
						}
					},
				]
			});
		}
	});

	/**
	 * Returns a function, that, when invoked, will only be triggered at most once
	 * during a given window of time. Normally, the throttled function will run
	 * as much as it can, without ever going more than once per `wait` duration;
	 * but if you'd like to disable the execution on the leading edge, pass
	 * `{leading: false}`. To disable execution on the trailing edge, ditto.
	 *
	 * @param {Function} func - The function to be throttled
	 * @param {Number} wait - Wait time in millis
	 * @param {Object} [options]
	 * @returns {function(): *} - The throttled function
	 */
	function throttle( func, wait, options ) {
		var context, args, result;
		var timeout  = null;
		var previous = 0;

		if ( ! options ) {
			options = {};
		}

		var later = function () {
			previous = options.leading === false ? 0 : Date.now();
			timeout  = null;
			result   = func.apply( context, args );
			if ( ! timeout ) {
				context = args = null;
			}
		};

		return function () {
			var now = Date.now();

			if ( ! previous && options.leading === false ) {
				previous = now;
			}

			var remaining = wait - (now - previous);
			context       = this;
			args          = arguments;

			if ( remaining <= 0 || remaining > wait ) {
				if ( timeout ) {
					clearTimeout( timeout );
					timeout = null;
				}
				previous = now;
				result   = func.apply( context, args );
				if ( ! timeout ) {
					context = args = null;
				}
			} else if ( ! timeout && options.trailing !== false ) {
				timeout = setTimeout( later, remaining );
			}
			return result;
		};
	}

	/* -----------------------------------------
	Smooth Scroll to Elements
	----------------------------------------- */
	$(document).on('click', '.sticky-atc-btn a[href^="#"]', function (event) {
        event.preventDefault();

        var target = $($.attr(this, 'href'));

        if ($('#tasheel-messages').length) {
        	var target = $('#tasheel-messages');
        }

        $('html, body').animate({
            scrollTop: target.offset().top
        }, 500);
    });

	/* -----------------------------------------
	Top Bar Carousel
	----------------------------------------- */
	$(document).ready(function(){
	   $('.top-slider').slick({
		    autoplay: true,
		    autoplaySpeed: 2000, // Adjust autoplay speed as needed
		    vertical: true,
		    slidesToShow: 1,
		    slidesToScroll: 1,
            arrows: false,
        });  
	});



	// Remove the demo phone number
    $(document).ready(function() {
    	  $(".footer-widgets  .widget_nav_menu .menu-item a[href='tel:+213-675-154-560']").hide();
    	  $(".footer-widgets  .widget_nav_menu .menu-item a:contains('0634222560')").hide();
	});

});

