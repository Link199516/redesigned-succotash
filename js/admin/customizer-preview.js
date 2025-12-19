/**
 * Base Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Base Theme Customizer preview reload changes asynchronously.
 *
 * https://developer.wordpress.org/themes/customize-api/tools-for-improved-user-experience/#using-postmessage-for-improved-setting-previewing
 */

(function ($) {
	function createStyleSheet(settingName, styles) {
		var $styleElement;

		style = '<style class="' + settingName + '">';
		style += styles.reduce(function (rules, style) {
			rules += style.selectors + '{' + style.property + ':' + style.value + ';} ';
			return rules;
		}, '');
		style += '</style>';

		$styleElement = $('.' + settingName);

		if ($styleElement.length) {
			$styleElement.replaceWith(style);
		} else {
			$('head').append(style);
		}
	}

	//
	// Site title and description.
	//
	wp.customize('blogname', function (value) {
		value.bind(function (to) {
			$('.site-logo a').text(to);
		});
	});

	wp.customize('blogdescription', function (value) {
		value.bind(function (to) {
			$('.site-tagline').text(to);
		});
	});

	//
	// Hero section
	//
	wp.customize('hero_text_color', function (value) {
		value.bind(function (to) {
			$('.page-hero').css('color', to);
		});
	});

	wp.customize('hero_image', function (value) {
		value.bind(function (to) {
			$('.page-hero').css('background-image', 'url(' + to + ')');
		});
	});

	wp.customize('hero_bg_color', function (value) {
		value.bind(function (to) {
			$('.page-hero').css('background-color', to);
		});
	});

	wp.customize('hero_image_repeat', function (value) {
		value.bind(function (to) {
			$('.page-hero').css('background-repeat', to);
		});
	});

	wp.customize('hero_image_position_x', function (value) {
		value.bind(function (to) {
			var $pageHero = $('.page-hero');
			var currentPosition = $pageHero.css('background-position');
			var newPosition = currentPosition.split(' ').map(function (pos, index) {
				return index === 0 ? to : pos;
			}).join(' ');

			$pageHero.css('background-position', newPosition);
		});
	});

	wp.customize('hero_image_position_y', function (value) {
		value.bind(function (to) {
			var $pageHero = $('.page-hero');
			var currentPosition = $pageHero.css('background-position');
			var newPosition = currentPosition.split(' ').map(function (pos, index) {
				return index === 1 ? to : pos;
			}).join(' ');

			$pageHero.css('background-position', newPosition);
		});
	});

	wp.customize('hero_image_attachment', function (value) {
		value.bind(function (to) {
			$('.page-hero').css('background-attachment', to);
		});
	});

	wp.customize('hero_image_cover', function (value) {
		value.bind(function (to) {
			if (!to) {
				$('.page-hero').css('background-size', 'auto');
			} else {
				$('.page-hero').css('background-size', 'cover');
			}
		});
	});

	//
	// Header Main Menu Bar
	//
	wp.customize('header_primary_menu_padding', function (value) {
		value.bind(function (to) {
			$('.head-mast').css({
				paddingTop: to + 'px',
				paddingBottom: to + 'px'
			});
		});
	});

	wp.customize('header_primary_menu_text_size', function (value) {
		value.bind(function (to) {
			$('.navigation-main > li > a').css('.navigation-main > li > a', to + 'px');
		});
	});
	wp.customize('header_top_bar_bg_color', function (value) {
		value.bind(function (to) {
			$('.top-head-wrap').css('background-color', to);
		});
	});
	wp.customize('header_background_color', function (value) {
		value.bind(function (to) {
			$('.head-mast').css('background-color', to);
		});
	});


	wp.customize('top_bar_text_color', function (value) {
		value.bind(function (to) {
			$('.navigation-top > li > a,' +
				'.top-head-wrap').css('color', to);
		});
	});
	wp.customize('header_text_color', function (value) {
		value.bind(function (to) {
			$('.site-logo a, ' +
				'.site-tagline,' +
				'.header-mini-cart-trigger,' +
				'.header-mini-cart-trigger .fas,' + 
				'.mobile-nav-trigger .fas').css('color', to);

		});
	});
	wp.customize('header_text_color', function (value) {
		value.bind(function (to) {
			$('.header-search-icon svg').css('fill', to);

		});
	});
	wp.customize('header_primary_menu_text_color', function (value) {
		value.bind(function (to) {
			$('.navigation-main > li > a').css('color', to);

			$('.navigation-main .nav-button > a').css('border-color', to);
		});
	});
	wp.customize('header_primary_menu_text_color', function (value) {
		value.bind(function (to) {
			$('.navigation-main > li > a').css('color', to);

			$('.navigation-main .nav-button > a').css('border-color', to);
		});
	});

	wp.customize('header_primary_menu_active_color', function (value) {
		value.bind(function (to) {
			$('.navigation-main > .current-menu-item > a,' +
				'.navigation-main > .current-menu-parent > a,' +
				'.navigation-main > .current-menu-ancestor > a').css('color', to);
		});
	});

	wp.customize('header_primary_submenu_bg_color', function (value) {
		value.bind(function (to) {
			$('.navigation-main ul').css('background-color', to);
		});
	});

	wp.customize('header_primary_submenu_text_color', function (value) {
		value.bind(function (to) {
			$('.navigation-main li li a').css('color', to);
		});
	});

	wp.customize('header_primary_submenu_active_text_color', function (value) {
		value.bind(function (to) {
			createStyleSheet('header_primary_submenu_active_text_color', [
				{
					property: 'color',
					value: to,
					selectors: '.navigation-main li li:hover > a,' +
						'.navigation-main li li > a:focus,' +
						'.navigation-main li .current-menu-item > a,' +
						'.navigation-main li .current-menu-parent > a,' +
						'.navigation-main li .current-menu-ancestor > a'
				}
			]);
		});
	});

	wp.customize('theme_header_primary_menu_sticky', function (value) {
		wp.customize.selectiveRefresh.bind('partial-content-rendered', function (placement) {
			$('.head-sticky').stick_in_parent({
				parent: 'body',
				sticky_class: 'is-stuck'
			});
		});
	});

	//
	// Footer Colors
	//
	wp.customize('footer_bg_color', function (value) {
		value.bind(function (to) {
			$('.footer-widgets').css('background-color', to);
		});
	});

	wp.customize('footer_text_color', function (value) {
		value.bind(function (to) {
			$('.footer-widgets,' +
				'.footer-widgets .widget,' +
				'.footer-widgets .widget-title,' +
				'.footer h1,.footer h2,.footer h3,' +
				'.footer h4,.footer h5,.footer h6,' +
				'.footer-widgets .at-contact-widget-item i').css('color', to);
		});
	});

	wp.customize('footer_link_color', function (value) {
		value.bind(function (to) {
			$('.footer-widgets a,' +
				'.footer-widgets .widget a').css('color', to);
		});
	});

	wp.customize('footer_border_color', function (value) {
		value.bind(function (to) {
			$('.footer-widgets .widget-title:after').css('background-color', to);
		});
	});

	wp.customize('footer_bottom_bg_color', function (value) {
		value.bind(function (to) {
			$('.footer-info').css('background-color', to);
		});
	});

	wp.customize('footer_bottom_text_color', function (value) {
		value.bind(function (to) {
			$('.footer-info, .footer-info-addons .social-icon').css('color', to);
		});
	});

	wp.customize('footer_bottom_link_color', function (value) {
		value.bind(function (to) {
			$('.footer-info a').css('color', to);
		});
	});

	wp.customize('footer_titles_color', function (value) {
		value.bind(function (to) {
			$('.footer .widget-title, .footer h1,.footer h2, ' +
				'.footer h3, .footer h4, .footer h5, .footer h6').css('color', to);
		});
	});

	//
	// Sidebar Colors
	//
	wp.customize('sidebar_bg_color', function (value) {
		value.bind(function (to) {
			$('.sidebar').css({
				backgroundColor: to,
				padding: '20px',
			});
		});
	});

	wp.customize('sidebar_text_color', function (value) {
		value.bind(function (to) {
			$('.sidebar,' +
				'.sidebar .widget,' +
				'.sidebar .at-contact-widget-item i').css('color', to);
		});
	});

	wp.customize('sidebar_link_color', function (value) {
		value.bind(function (to) {
			$('.sidebar a, .sidebar .widget a').css('color', to);
		});
	});

	wp.customize('sidebar_border_color', function (value) {
		value.bind(function (to) {
			$('.sidebar select, .sidebar input, .sidebar textarea').css('border-color', to);

			$('.sidebar .widget_recent_comments li,' +
				'.sidebar .widget_recent_entries li,' +
				'.sidebar .widget_rss li,' +
				'.sidebar .widget_meta li a,' +
				'.sidebar .widget_pages li a,' +
				'.sidebar .widget_categories li a,' +
				'.sidebar .widget_archive li a,' +
				'.sidebar .widget_nav_menu li a').css('border-bottom-color', to);
		});
	});

	wp.customize('sidebar_titles_color', function (value) {
		value.bind(function (to) {
			$('.sidebar .widget-title').css('color', to);
		});
	});

	//
	// Button colors
	//
	wp.customize('site_button_bg_color', function (value) {
		value.bind(function (to) {
			$('.btn,' +
				'.button,' +
				'.comment-reply-link,' +
				'input[type="submit"],' +
				'input[type="reset"],' +
				'.wc-block-grid__products .add_to_cart_button,' + '.wc-block-grid__products .added_to_cart,' +
				'button[type="submit"]').css('background-color', to);
		});


	});

	wp.customize('site_button_text_color', function (value) {
		value.bind(function (to) {
			$('.btn,' +
				'.button,' +
				'.comment-reply-link,' +
				'input[type="submit"],' +
				'input[type="reset"],' +
				'.wc-block-grid__products .add_to_cart_button,' + '.wc-block-grid__products .added_to_cart,' +
				'button[type="submit"]').css('color', to);
		});
	});

	wp.customize('site_button_hover_bg_color', function (value) {
		value.bind(function (to) {
			var style = '<style class="site_button_hover_bg_color">' +
				'.btn:hover,' +
				'.button:hover,' +
				'.comment-reply-link:hover,' +
				'input[type="submit"]:hover,' +
				'input[type="reset"]:hover,' +
				'.wc-block-grid__products .add_to_cart_button:hover,' + '.wc-block-grid__products .added_to_cart:hover,' +
				'button[type="submit"]:hover' +
				'.row-items [class*="col-"]:hover  .item-product .button' +
				'{ background-color: ' + to + ' !important; }</style>';

			var $el = $('.site_button_hover_bg_color');

			if ($el.length) {
				$el.replaceWith(style);
			} else {
				$('head').append(style);
			}
		});
	});

	wp.customize('site_button_hover_text_color', function (value) {
		value.bind(function (to) {
			var style = '<style class="site_button_hover_text_color">' +
				'.btn:hover,' +
				'.button:hover,' +
				'.comment-reply-link:hover,' +
				'input[type="submit"]:hover,' +
				'input[type="reset"]:hover,' +
				'.wc-block-grid__products .add_to_cart_button:hover,' + '.wc-block-grid__products .added_to_cart:hover,' +
				'button[type="submit"]:hover' +
				'{ color: ' + to + ' !important; }</style>';

			var $el = $('.site_button_hover_bg_color');

			if ($el.length) {
				$el.replaceWith(style);
			} else {
				$('head').append(style);
			}
		});
	});


	wp.customize('theme_lightbox', function (value) {
		value.bind(function (to) {
			if (to) {
				$(".napoleon-lightbox, a[data-lightbox^='gal']").magnificPopup({
					type: 'image',
					mainClass: 'mfp-with-zoom',
					gallery: {
						enabled: true
					},
					zoom: {
						enabled: true
					}
				});
			} else {
				$(".napoleon-lightbox, a[data-lightbox^='gal']").off('click');
			}
		});
	});


	//
	// Theme global colors
	//
	wp.customize('site_secondary_accent_color', function (value) {
		value.bind(function (to) {
			createStyleSheet('site_secondary_accent_color', [
				{
					property: 'color',
					value: to,
					selectors: 'a,' +
						'a:hover,' +
						'.site-tagline,' +
						'.section-title > a,' +
						'.entry-author-socials .social-icon,' +
						'.widget-newsletter-content'
				}
			]);
		});
	});

	wp.customize('site_accent_color', function (value) {
		value.bind(function (to) {
			createStyleSheet('site_accent_color', [
				{
					property: 'color',
					value: to,
					selectors: '.entry-title a:hover,' +
						'.item-title a:hover,' +
						'.woocommerce-pagination a:hover,' +
						'.woocommerce-pagination .current,' +
						'.navigation a:hover,' +
						'.navigation .current,' +
						'.page-links .page-number:hover,' +
						'.text-theme,' +
						'.sidebar .social-icon:hover,' +
						'.widget-newsletter-content-wrap .fas,' +
						'.widget-newsletter-content-wrap .far,' +
						'.widget_meta li a:hover,' +
						'.widget_pages li a:hover,' +
						'.widget_categories li a:hover,' +
						'.widget_archive li a:hover,' +
						'.widget_nav_menu li a:hover,' +
						'.widget_product_categories li a:hover,' +
						'.widget_layered_nav li a:hover,' +
						'.widget_rating_filter li a:hover,' +
						'.widget_recent_entries a:hover,' +
						'.widget_recent_comments a:hover,' +
						'.widget_rss a:hover,' +
						'.shop-actions .product-number a.product-number-active,' +
						'.shop-filter-toggle i,' +
						'.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a:hover,' +
						'.product_list_widget .product-title:hover,' +
						'.wc-block-grid__products .wc-block-grid__product-title:hover'
				},
				{
					property: 'border-color',
					value: to,
					selectors: '.sidebar .social-icon:hover',
				},
				{
					property: 'background-color',
					value: to,
					selectors: '.section-title:after, .onsale,.wc-block-grid__products .wc-block-grid__product-onsale',
				},
			]);
		});
	});

	wp.customize('site_text_color', function (value) {
		value.bind(function (to) {
			createStyleSheet('site_text_color', [
				{
					property: 'color',
					value: to,
					selectors: 'body,' +
						'blockquote cite,' +
						'.instagram-pics li a,' +
						'.category-search-select,' +
						'.section-subtitle a,' +
						'.entry-title a,' +
						'.woocommerce-ordering select,' +
						'.shop_table .product-name a,' +
						'.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a,' +
						'.woocommerce-MyAccount-content mark,' +
						'.woocommerce-MyAccount-downloads .download-file a,' +
						'.woocommerce-Address-title a,' +
						'.sidebar .widget_layered_nav_filters a,' +
						'.row-slider-nav .slick-arrow,.comment-metadata a,' +
						'.entry-meta,' +
						'.item-meta,' +
						'.item-meta a,' +
						'.sidebar .widget_recent_entries .post-date,' +
						'.sidebar .tag-cloud-link,' +
						'.breadcrumb,' +
						'.woocommerce-breadcrumb,' +
						'.woocommerce-product-rating .woocommerce-review-link,' +
						'.wc-tabs a,' +
						'.sidebar .product_list_widget .quantity,' +
						'.woocommerce-mini-cart__total'
				},
			]);
		});
	});

	wp.customize('site_text_color_secondary', function (value) {
		value.bind(function (to) {
			createStyleSheet('site_text_color_secondary', [
				{
					property: 'color',
					value: to,
					selectors: '.entry-meta a,' +
						'.entry-tags a,' +
						'.item-title a,' +
						'.woocommerce-pagination a,' +
						'.woocommerce-pagination span,' +
						'.navigation a,' +
						'.navigation .page-numbers,' +
						'.page-links .page-number,' +
						'.page-links > .page-number,' +
						'.sidebar .social-icon,' +
						'.sidebar-dismiss,' +
						'.sidebar-dismiss:hover,' +
						'.sidebar .widget_meta li a,' +
						'.sidebar .widget_pages li a,' +
						'.sidebar .widget_categories li a,' +
						'.sidebar .widget_archive li a,' +
						'.sidebar .widget_nav_menu li a,' +
						'.sidebar .widget_product_categories li a,' +
						'.sidebar .widget_layered_nav li a,' +
						'.sidebar .widget_rating_filter li a,' +
						'.sidebar .widget_recent_entries a,' +
						'.sidebar .widget_recent_comments a,' +
						'.sidebar .widget_rss a,' +
						'.woocommerce-message a:not(.button),' +
						'.woocommerce-error a:not(.button),' +
						'.woocommerce-info a:not(.button),' +
						'.woocommerce-noreview a:not(.button),' +
						'.breadcrumb a,' +
						'.woocommerce-breadcrumb a,' +
						'.shop-actions a,' +
						'.shop-filter-toggle,' +
						'.entry-summary .product_title,' +
						'.product_meta a,' +
						'.entry-product-info .price,' +
						'.tagged_as a,' +
						'.woocommerce-grouped-product-list-item__label a,' +
						'.reset_variations,' +
						'.wc-tabs li.active a,' +
						'.shop_table .remove,' +
						'.shop_table .product-name a:hover,' +
						'.shop_table .product-subtotal .woocommerce-Price-amount,' +
						'.shipping-calculator-button,' +
						'.sidebar .product_list_widget .product-title,' +
						'.wc-block-grid__products .wc-block-grid__product-title'
				},
				{
					property: 'background-color',
					value: to,
					selectors: '.price_slider .ui-slider-handle',
				},
			]);
		});
	});

	wp.customize('site_text_color_supplementary', function (value) {
		value.bind(function (to) {
			createStyleSheet('site_text_color_supplementary', [
				{
					property: 'color',
					value: to,
					selectors: '.item .price,' +
						'.item-inset,' +
						'.woocommerce-grouped-product-list-item__price .woocommerce-Price-amount,' +
						'.woocommerce-grouped-product-list-item__price del,' +
						'.sidebar .product_list_widget .woocommerce-Price-amount,' +
						'.sidebar .product_list_widget del,' +
						'.entry-summary .price,'+
						' .single-product .main .row p.price,' +
						'.wc-block-grid__products .wc-block-grid__product-price,' +
						'.woocommerce-mini-cart__total .woocommerce-Price-amount',
				},
			]);
		});
	});

	wp.customize('site_border_color', function (value) {
		value.bind(function (to) {
			createStyleSheet('site_border_color', [
				{
					property: 'border-color',
					value: to,
					selectors: 'hr,' +
						'blockquote,' +
						'.entry-content th,' +
						'.entry-content td,' +
						'textarea,' +
						'select,' +
						'input,' +
						'.no-comments,' +
						'.header-mini-cart-contents,' +
						'.entry-thumb img,' +
						'.item,' +
						'.item-media .item-thumb img,' +
						'.sidebar .social-icon,' +
						'.sidebar .at-schedule-widget-table tr,' +
						'.sidebar .widget_meta li a,' +
						'.sidebar .widget_pages li a,' +
						'.sidebar .widget_categories li a,' +
						'.sidebar .widget_archive li a,' +
						'.sidebar .widget_nav_menu li a,' +
						'.sidebar .widget_product_categories li a,' +
						'.sidebar .widget_layered_nav li a,' +
						'.sidebar .widget_rating_filter li a,' +
						'.sidebar .widget_recent_entries li,' +
						'.sidebar .widget_recent_comments li,' +
						'.sidebar .widget_rss li,' +
						'.demo_store,' +
						'.woocommerce-product-gallery .flex-viewport,' +
						'.woocommerce-product-gallery .flex-contorl-thumbs li img,' +
						'.woocommerce-product-gallery__wrapper,' +
						'.single-product-table-wrapper,' +
						'.wc-tabs,' +
						'.shop_table.cart,' +
						'.shop_table.cart th,' +
						'.shop_table.cart td,' +
						'.cart-collaterals .shop_table,' +
						'.cart-collaterals .shop_table th,' +
						'.cart-collaterals .shop_table td,' +
						'#order_review_heading,' +
						'.wc_payment_method,' +
						'.payment_box,' +
						'.woocommerce-order-received .customer_details,' +
						'.woocommerce-thankyou-order-details,' +
						'.wc-bacs-bank-details,' +
						'.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a,' +
						'.woocommerce-EditAccountForm fieldset,' +
						'.wc-form-login,' +
						'.sidebar .product_list_widget .product-thumb img,' +
						'.header .widget_shopping_cart li.empty,' +
						'.woocommerce-mini-cart__empty-message,' +
						'.row-slider-nav .slick-arrow,' +
						'textarea,' +
						'select,' +
						'input,' +
						'.select2-container .select2-selection--single,' +
						'.select2-container .select2-search--dropdown .select2-search__field,' +
						'.select2-dropdown,' +
						'wp-block-pullquote'
				},
				{
					property: 'background-color',
					value: to,
					selectors: '.price_slider' +
						'.price_slider .ui-slider-range'
				},
			]);
		});
	});
})(jQuery);
