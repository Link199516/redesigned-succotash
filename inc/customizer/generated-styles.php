<?php
	/**
	 * Generates CSS based on standard customizer settings.
	 *
	 * @return string
	 */
function napoleon_get_customizer_css() {
	ob_start();

	//
	// FONTS
	//

	$selected_font = get_theme_mod('font_options', 'cairo'); ?>
		body, .navigation-main > li[class*="fa-"], .mm-listview > li[class*="fa-"], .widget_nav_menu li[class*="fa-"], button, input, optgroup, select, textarea, .woocommerce-thankyou-order-details li strong, .wc-bacs-bank-details li strong, .woocommerce-EditAccountForm legend { font-family: '<?php echo $selected_font; ?>', sans-serif !important; }

		<?php 


	//
	// Logo
	//
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( get_theme_mod( 'limit_logo_size' ) && ! empty( $custom_logo_id ) ) {
		$image_metadata = wp_get_attachment_metadata( $custom_logo_id );
		$max_width      = floor( $image_metadata['width'] / 2 );
		?>
			.header img.custom-logo {
				width: <?php echo intval( $max_width ); ?>px;
				max-width: 100%;
			}
			<?php
	}

	if ( apply_filters( 'napoleon_customizable_header', true ) ) {

		//
		// Header Main Menu Bar
		//
		$header_primary_menu_padding = get_theme_mod( 'header_primary_menu_padding' );

		if ( ! empty( $header_primary_menu_padding ) ) {
			?>
				.head-mast {
					padding-top: <?php echo intval( $header_primary_menu_padding ); ?>px;
					padding-bottom: <?php echo intval( $header_primary_menu_padding ); ?>px;
				}
				<?php
		}

		$header_primary_menu_text_size = get_theme_mod( 'header_primary_menu_text_size' );

		if ( ! empty( $header_primary_menu_text_size ) ) {
			?>
				.navigation-main > li > a {
					font-size: <?php echo intval( $header_primary_menu_text_size ); ?>px;
				}
				<?php
		}

		$header_top_bar_bg_color = get_theme_mod( 'header_top_bar_bg_color', '#4C3BCF' );
		

		if ( ! empty( $header_top_bar_bg_color ) ) {
			?>
				.top-head-wrap {
					background-color: <?php echo sanitize_hex_color( $header_top_bar_bg_color ); ?>;
				}
				<?php
		}
		

		$header_background_color = get_theme_mod( 'header_background_color' );

		if ( ! empty( $header_background_color ) ) {
			?>
				.head-mast {
					background-color: <?php echo sanitize_hex_color( $header_background_color ); ?>;
				}
				<?php
		}

		$header_primary_menu_bg_color = get_theme_mod( 'header_primary_menu_bg_color' );

		if ( ! empty( $header_primary_menu_bg_color ) ) {
			?>
				.head-nav {
					background-color: <?php echo sanitize_hex_color( $header_primary_menu_bg_color ); ?>;
				}
				<?php
		}

		$top_bar_text_color = get_theme_mod( 'top_bar_text_color' );

		if ( ! empty( $top_bar_text_color ) ) {
			?>
				.navigation-top > li > a,
				.top-head-wrap {
					color: <?php echo sanitize_hex_color( $top_bar_text_color ); ?>;
				}
				<?php
		}
		$header_text_color = get_theme_mod( 'header_text_color' );

		if ( ! empty( $header_text_color ) ) {
			?>
				.site-logo a,
				.site-tagline,
				.mobile-nav-trigger .fas {
					color: <?php echo sanitize_hex_color( $header_text_color ); ?>;
				}

				.header-search-icon svg {
					fill: <?php echo sanitize_hex_color( $header_text_color ); ?>;
				}
				<?php
		}

		$header_primary_menu_text_color = get_theme_mod( 'header_primary_menu_text_color' );

		if ( ! empty( $header_primary_menu_text_color ) ) {
			?>
				.navigation-main > li > a {
					color: <?php echo sanitize_hex_color( $header_primary_menu_text_color ); ?>;
				}
				<?php
		}


		$header_primary_menu_active_color = get_theme_mod( 'header_primary_menu_active_color' );

		if ( ! empty( $header_primary_menu_active_color ) ) {
			?>
				.navigation-main > li:hover > a,
				.navigation-main > li > a:focus,
				.navigation-main > .current-menu-item > a,
				.navigation-main > .current-menu-parent > a,
				.navigation-main > .current-menu-ancestor > a,
				.navigation-main .nav-button > a:hover,
				.navigation-main > li.fas::before,
				.navigation-main > li.far::before,
				.navigation-main > li.fab::before,
				.navigation-main .menu-item-has-children > a::after {
					color: <?php echo sanitize_hex_color( $header_primary_menu_active_color ); ?>;
				}

				.navigation-main .nav-button > a:hover {
					border-color: <?php echo sanitize_hex_color( $header_primary_menu_active_color ); ?>;
				}
				<?php
		}

		$header_primary_submenu_bg_color = get_theme_mod( 'header_primary_submenu_bg_color' );

		if ( ! empty( $header_primary_submenu_bg_color ) ) {
			?>
				.navigation-main ul {
					background-color: <?php echo sanitize_hex_color( $header_primary_submenu_bg_color ); ?>;
				}

				.navigation-main > li > ul::before {
					border-bottom-color: <?php echo sanitize_hex_color( $header_primary_submenu_bg_color ); ?>;
				}
				<?php
		}

		$header_primary_submenu_text_color = get_theme_mod( 'header_primary_submenu_text_color' );

		if ( ! empty( $header_primary_submenu_text_color ) ) {
			?>
				.navigation-main li li a {
					color: <?php echo sanitize_hex_color( $header_primary_submenu_text_color ); ?>;
				}
				<?php
		}

		$header_primary_submenu_active_text_color = get_theme_mod( 'header_primary_submenu_active_text_color' );

		if ( ! empty( $header_primary_submenu_active_text_color ) ) {
			?>
				.navigation-main li li:hover > a,
				.navigation-main li li > a:focus,
				.navigation-main li .current-menu-item > a,
				.navigation-main li .current-menu-parent > a,
				.navigation-main li .current-menu-ancestor > a {
					color: <?php echo sanitize_hex_color( $header_primary_submenu_active_text_color ); ?>;
				}
				<?php
		}
	} // filter napoleon_customizable_header

	if ( apply_filters( 'napoleon_customizable_footer', true ) ) {
		//
		// Footer Colors
		//
		$footer_bg_color = get_theme_mod( 'footer_bg_color', '#fafafa' );

			?>
				.footer-widgets {
					background-color: <?php echo sanitize_hex_color( $footer_bg_color ); ?>;
				}
				<?php
		

		$footer_text_color = get_theme_mod( 'footer_text_color' );

		if ( ! empty( $footer_text_color ) ) {
			?>
				.footer-widgets,
				.footer-widgets .widget,
				.footer-widgets .widget-title,
				.footer h1,
				.footer h2,
				.footer h3,
				.footer h4,
				.footer h5,
				.footer h6,
				.footer-widgets .at-contact-widget-item i {
					color: <?php echo sanitize_hex_color( $footer_text_color ); ?>;
				}
				<?php
		}

		$footer_link_color = get_theme_mod( 'footer_link_color' );

		if ( ! empty( $footer_link_color ) ) {
			?>
				.footer-widgets a,
				.footer-widgets .widget_nav_menu li a,
				.footer-widgets .widget a,
				.footer-widgets .widget a:hover,
				.footer-widgets .item-title a,
				.footer-widgets .item-title a:hover {
					color: <?php echo sanitize_hex_color( $footer_link_color ); ?>;
				}
				<?php
		}

		$footer_border_color = get_theme_mod( 'footer_border_color' );

		if ( ! empty( $footer_border_color ) ) {
			?>
				.footer-widgets .widget-title:after {
					background-color: <?php echo sanitize_hex_color( $footer_border_color ); ?>;
				}
				<?php
		}

		$footer_bottom_bg_color = get_theme_mod( 'footer_bottom_bg_color' );

		if ( ! empty( $footer_bottom_bg_color ) ) {
			?>
				.footer-info {
					background-color: <?php echo sanitize_hex_color( $footer_bottom_bg_color ); ?>;
				}
				<?php
		}

		$footer_bottom_text_color = get_theme_mod( 'footer_bottom_text_color' );

		if ( ! empty( $footer_bottom_text_color ) ) {
			?>
				.footer-info,
				.footer-info-addons .social-icon {
					color: <?php echo sanitize_hex_color( $footer_bottom_text_color ); ?>;
				}
				<?php
		}

		$footer_bottom_link_color = get_theme_mod( 'footer_bottom_link_color' );

		if ( ! empty( $footer_bottom_link_color ) ) {
			?>
				.footer-info a,
				.footer-info a:hover {
					color: <?php echo sanitize_hex_color( $footer_bottom_link_color ); ?>;
				}
				<?php
		}

		$footer_titles_color = get_theme_mod( 'footer_titles_color' );

		if ( ! empty( $footer_titles_color ) ) {
			?>
				.footer .widget-title,
				.footer h1,
				.footer h2,
				.footer h3,
				.footer h4,
				.footer h5,
				.footer h6 {
					color: <?php echo sanitize_hex_color( $footer_titles_color ); ?>;
				}
				<?php
		}
	} // filter napoleon_customizable_footer

	//
	// Sidebar Colors
	//
	$sidebar_bg_color = get_theme_mod( 'sidebar_bg_color' );

	if ( ! empty( $sidebar_bg_color ) ) {
		?>
			.sidebar {
				background-color: <?php echo sanitize_hex_color( $sidebar_bg_color ); ?>;
				padding: 20px;
			}
			<?php
	}

	$sidebar_text_color = get_theme_mod( 'sidebar_text_color' );

	if ( ! empty( $sidebar_text_color ) ) {
		?>
			.sidebar,
			.sidebar .widget,
			.sidebar .at-contact-widget-item i {
				color: <?php echo sanitize_hex_color( $sidebar_text_color ); ?>;
			}
			<?php
	}

	$sidebar_link_color = get_theme_mod( 'sidebar_link_color' );

	if ( ! empty( $sidebar_link_color ) ) {
		?>
			.sidebar a,
			.sidebar .widget a {
				color: <?php echo sanitize_hex_color( $sidebar_link_color ); ?>;
			}
			<?php
	}

	$sidebar_link_hover_color = get_theme_mod( 'sidebar_link_hover_color' );

	if ( ! empty( $sidebar_link_hover_color ) ) {
		?>
			.sidebar a:hover,
			.sidebar .widget a:hover {
				color: <?php echo sanitize_hex_color( $sidebar_link_hover_color ); ?>;
			}
			<?php
	}

	$sidebar_border_color = get_theme_mod( 'sidebar_border_color' );

	if ( ! empty( $sidebar_border_color ) ) {
		?>
			.sidebar select,
			.sidebar input,
			.sidebar textarea {
				border-color: <?php echo sanitize_hex_color( $sidebar_border_color ); ?>;
			}

			.sidebar .widget_recent_comments li,
			.sidebar .widget_recent_entries li,
			.sidebar .widget_rss li,
			.widget_meta li a,
			.widget_pages li a,
			.widget_categories li a,
			.widget_archive li a,
			.widget_nav_menu li a,
			.widget_product_categories li a,
			.widget_layered_nav li a,
			.widget_rating_filter li a {
				border-bottom-color: <?php echo sanitize_hex_color( $sidebar_border_color ); ?>;
			}
			<?php
	}

	$sidebar_titles_color = get_theme_mod( 'sidebar_titles_color' );

	if ( ! empty( $sidebar_titles_color ) ) {
		?>
			.sidebar .widget-title {
				color: <?php echo sanitize_hex_color( $sidebar_titles_color ); ?>;
			}
			<?php
	}


	//
	// Contact Icon Colors
	//
	$contact_bg_color = get_theme_mod( 'contact_bg_color' );

	if ( ! empty( $contact_bg_color ) ) {
		?>
			.callus-icon {
				background-color: <?php echo sanitize_hex_color( $contact_bg_color ); ?>;
			}
			<?php
	}

	$contact_animation = get_theme_mod( 'contact_animation', '1' );

	if (  $contact_animation == 0  ) {
		?>
			.callus-icon i {
				animation: none;
			}
			<?php
	}


	$contact_rotation = get_theme_mod( 'contact_rotation', '1' );

	if (  $contact_rotation == 0  ) {
		?>
			.callus-icon {
				transform: none;
			}
			<?php
	}



	//
	// Photo Border options
	//
	$photo_border_color = get_theme_mod( 'photo_border_color' );

	if ( get_theme_mod( 'add_photo_border' ) == 1 ) {
		?>
			.item-thumb img,.woocommerce-product-gallery .flex-control-thumbs li img {
				border: 2px solid #4C3BCF;
				border-color: <?php echo sanitize_hex_color( $photo_border_color ); ?>;
			}
			.woocommerce-product-gallery .flex-viewport {
				border: 4px solid #4C3BCF;
				border-color: <?php echo sanitize_hex_color( $photo_border_color ); ?>;

			}
			<?php
	}



	//
	// Button colors
	//
	$site_button_bg_color = get_theme_mod( 'site_button_bg_color' );

	if ( ! empty( $site_button_bg_color ) ) {
		?>
			.btn,
			.button,
			.comment-reply-link,
			input[type="submit"],
			input[type="reset"],
			button[type="submit"],
			.wc-block-grid__products .add_to_cart_button,
			.wc-block-grid__products .added_to_cart {
				background-color: <?php echo sanitize_hex_color( $site_button_bg_color ); ?>;
			}
			<?php
	}

	$site_button_text_color = get_theme_mod( 'site_button_text_color' );

	if ( ! empty( $site_button_text_color ) ) {
		?>
			.btn,
			.button,
			.comment-reply-link,
			input[type="submit"],
			input[type="reset"],
			button[type="submit"],
			.wc-block-grid__products .add_to_cart_button,
			.wc-block-grid__products .added_to_cart {
				color: <?php echo sanitize_hex_color( $site_button_text_color ); ?>;
			}
			<?php
	}

	$site_button_hover_bg_color = get_theme_mod( 'site_button_hover_bg_color' );

	if ( ! empty( $site_button_hover_bg_color ) ) {
		?>
			.btn:hover,
			.button:hover,
			.comment-reply-link:hover,
			input[type="submit"]:hover,
			input[type="reset"]:hover,
			button[type="submit"]:hover,
			.wc-block-grid__products .add_to_cart_button:hover, .wc-block-grid__products .added_to_cart:hover,
			.row-items [class*="col-"]:hover  .item-product .button {
				background-color: <?php echo sanitize_hex_color( $site_button_hover_bg_color ); ?>;
			}
			.row-items [class*="col-"]:hover {
				border-color: <?php echo sanitize_hex_color( $site_button_hover_bg_color ); ?>;
			}
			<?php
	}

	$site_button_hover_text_color = get_theme_mod( 'site_button_hover_text_color' );

	if ( ! empty( $site_button_hover_text_color ) ) {
		?>
			.btn:hover,
			.button:hover,
			.comment-reply-link:hover,
			input[type="submit"]:hover,
			input[type="reset"]:hover,
			button[type="submit"]:hover,
			.wc-block-grid__products .add_to_cart_button:hover, .wc-block-grid__products .added_to_cart:hover {
				color: <?php echo sanitize_hex_color( $site_button_hover_text_color ); ?>;
			}
			<?php
	}

	//
	// Global Colors
	//
	$site_secondary_accent_color = get_theme_mod( 'site_secondary_accent_color' );

	if ( ! empty( $site_secondary_accent_color ) ) {
		?>
			a,
			a:hover,
			.site-tagline,
			.section-title > a,
			.entry-author-socials .social-icon,
			.widget-newsletter-content {
				color: <?php echo sanitize_hex_color( $site_secondary_accent_color ); ?>;
			}
			<?php
	}

	$site_accent_color = get_theme_mod( 'site_accent_color' );

	if ( ! empty( $site_accent_color ) ) {
		?>
			.entry-title a:hover,
			.item-title a:hover,
			.woocommerce-pagination a:hover,
			.woocommerce-pagination .current,
			.navigation a:hover,
			.navigation .current,
			.page-links .page-number:hover,
			.category-search-results-item a,
			.text-theme,
			.sidebar .social-icon:hover,
			.entry-social-share .social-icon:hover,
			.widget-newsletter-content-wrap .fas,
			.widget-newsletter-content-wrap .far,
			.widget_meta li a:hover,
			.widget_pages li a:hover,
			.widget_categories li a:hover,
			.widget_archive li a:hover,
			.widget_nav_menu li a:hover,
			.widget_product_categories li a:hover,
			.widget_layered_nav li a:hover,
			.widget_rating_filter li a:hover,
			.widget_recent_entries a:hover,
			.widget_recent_comments a:hover,
			.widget_rss a:hover,
			.shop-actions .product-number a.product-number-active,
			.shop-filter-toggle i,
			.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a:hover,
			.product_list_widget .product-title:hover,
			.wc-block-grid__products .wc-block-grid__product-title:hover,
			.cart-count,
			.entry-summary .price  {
				color: <?php echo sanitize_hex_color( $site_accent_color ); ?>;
			}

			.sidebar .social-icon:hover,
			.category-search-form,
			.header-mini-cart,
			.woocommerce .item:hover,
			.woocommerce-order-details .woocommerce-table--order-details,
			#codplugin-thanks #codplugin-thanks-box #codplugin_show_hide
			 {
				border-color: <?php echo sanitize_hex_color( $site_accent_color ); ?>;
			}

			.onsale,
			.wc-block-grid__products .wc-block-grid__product-onsale,
			.row-slider-nav .slick-arrow:hover,
			.napoleon-slick-slider .slick-arrow:hover,
			.section-title:after,
			.woocommerce-table--order-details thead,
			.woocommerce-table__product-name .product-quantity ,
			#thanks-order-summary .order-summary-title {
				background-color: <?php echo sanitize_hex_color( $site_accent_color ); ?>;
			}
			<?php
	}

	$site_text_color = get_theme_mod( 'site_text_color' );

	if ( ! empty( $site_text_color ) ) {
		$site_text_color_light = napoleon_color_luminance( $site_text_color, 0.3 );
		?>
			body,
			blockquote cite,
			.instagram-pics li a,
			.category-search-select,
			.section-subtitle a,
			.entry-title a,
			.woocommerce-ordering select,
			.shop_table .product-name a,
			.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a,
			.woocommerce-MyAccount-content mark,
			.woocommerce-MyAccount-downloads .download-file a,
			.woocommerce-Address-title a,
			.sidebar .widget_layered_nav_filters a,
			.row-slider-nav .slick-arrow {
				color: <?php echo sanitize_hex_color( $site_text_color ); ?>;
			}

			.comment-metadata a,
			.entry-meta,
			.item-meta,
			.item-meta a,
			.sidebar .widget_recent_entries .post-date,
			.sidebar .tag-cloud-link,
			.breadcrumb,
			.woocommerce-breadcrumb,
			.woocommerce-product-rating .woocommerce-review-link,
			.wc-tabs a,
			.sidebar .product_list_widget .quantity,
			.woocommerce-mini-cart__total {
				color: <?php echo sanitize_hex_color( $site_text_color_light ); ?>;
			}
			<?php
	}

	$site_text_color_secondary = get_theme_mod( 'site_text_color_secondary' );

	if ( ! empty( $site_text_color_secondary ) ) {
		?>
			.entry-meta a,
			.entry-tags a,
			.item-title a,
			.woocommerce-pagination a,
			.woocommerce-pagination span,
			.navigation a,
			.navigation .page-numbers,
			.page-links .page-number,
			.page-links > .page-number,
			.sidebar .social-icon,
			.entry-social-share .social-icon,
			.sidebar-dismiss,
			.sidebar-dismiss:hover,
			.sidebar .widget_meta li a,
			.sidebar .widget_pages li a,
			.sidebar .widget_categories li a,
			.sidebar .widget_archive li a,
			.sidebar .widget_nav_menu li a,
			.sidebar .widget_product_categories li a,
			.sidebar .widget_layered_nav li a,
			.sidebar .widget_rating_filter li a,
			.sidebar .widget_recent_entries a,
			.sidebar .widget_recent_comments a,
			.sidebar .widget_rss a,
			.woocommerce-message a:not(.button),
			.woocommerce-error a:not(.button),
			.woocommerce-info a:not(.button),
			.woocommerce-noreview a:not(.button),
			.breadcrumb a,
			.woocommerce-breadcrumb a,
			.shop-actions a,
			.shop-filter-toggle,
			.entry-summary .product_title,
			.product_meta a,
			.entry-product-info .price,
			.tagged_as a,
			.woocommerce-grouped-product-list-item__label a,
			.reset_variations,
			.wc-tabs li.active a,
			.shop_table .remove,
			.shop_table .product-name a:hover,
			.shop_table .product-subtotal .woocommerce-Price-amount,
			.shipping-calculator-button,
			.sidebar .product_list_widget .product-title,
			.wc-block-grid__products .wc-block-grid__product-title {
				color: <?php echo sanitize_hex_color( $site_text_color_secondary ); ?>;
			}

			.price_slider .ui-slider-handle {
				background-color: <?php echo sanitize_hex_color( $site_text_color_secondary ); ?>;
			}

			.block-item::before {
				background-color: <?php echo napoleon_hex2rgba( sanitize_hex_color( $site_text_color_secondary ), 0.25 ); ?>;
			}
			<?php
	}

	$site_text_color_supplementary = get_theme_mod( 'site_text_color_supplementary' );

	if ( ! empty( $site_text_color_supplementary ) ) {
		?>
			.item .price,
			.entry-summary .price, 
			.single-product .main .row p.price,
			.item-inset,
			.woocommerce-grouped-product-list-item__price .woocommerce-Price-amount,
			.woocommerce-grouped-product-list-item__price del,
			.sidebar .product_list_widget .woocommerce-Price-amount,
			.sidebar .product_list_widget del,
			.woocommerce-mini-cart__total .woocommerce-Price-amount,
			.wc-block-grid__products .wc-block-grid__product-price {
				color: <?php echo sanitize_hex_color( $site_text_color_supplementary ); ?>;
			}
			<?php
	}

	$site_border_color = get_theme_mod( 'site_border_color' );

	if ( ! empty( $site_border_color ) ) {
		$site_border_color_dark = napoleon_color_luminance( $site_border_color, -0.2 );
		?>
			hr,
			blockquote,
			.entry-content th,
			.entry-content td,
			textarea,
			select,
			input,
			.no-comments,
			.header-mini-cart-contents,
			.entry-thumb img,
			.item,
			.item-media .item-thumb img,
			.sidebar .social-icon,
			.entry-social-share .social-icon,
			.sidebar .at-schedule-widget-table tr,
			.sidebar .widget_meta li a,
			.sidebar .widget_pages li a,
			.sidebar .widget_categories li a,
			.sidebar .widget_archive li a,
			.sidebar .widget_nav_menu li a,
			.sidebar .widget_product_categories li a,
			.sidebar .widget_layered_nav li a,
			.sidebar .widget_rating_filter li a,
			.sidebar .widget_recent_entries li,
			.sidebar .widget_recent_comments li,
			.sidebar .widget_rss li,
			.demo_store,
			.woocommerce-product-gallery .flex-viewport,
			.woocommerce-product-gallery .flex-contorl-thumbs li img,
			.woocommerce-product-gallery__wrapper,
			.single-product-table-wrapper,
			.wc-tabs,
			.shop_table.cart,
			.shop_table.cart th,
			.shop_table.cart td,
			.cart-collaterals .shop_table,
			.cart-collaterals .shop_table th,
			.cart-collaterals .shop_table td,
			#order_review_heading,
			.wc_payment_method,
			.payment_box,
			.woocommerce-order-received .customer_details,
			.woocommerce-thankyou-order-details,
			.wc-bacs-bank-details,
			.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a,
			.woocommerce-EditAccountForm fieldset,
			.wc-form-login,
			.sidebar .product_list_widget .product-thumb img,
			.header .widget_shopping_cart li.empty,
			.woocommerce-mini-cart__empty-message,
			.row-slider-nav .slick-arrow,
			.wp-block-pullquote {
				border-color: <?php echo sanitize_hex_color( $site_border_color ); ?>;
			}

			textarea,
			select,
			input,
			.select2-container .select2-selection--single,
			.select2-container .select2-search--dropdown .select2-search__field,
			.select2-dropdown {
				border-color: <?php echo sanitize_hex_color( $site_border_color_dark ); ?>;
			}

			.price_slider
			.price_slider .ui-slider-range {
				background-color: <?php echo sanitize_hex_color( $site_border_color ); ?>;
			}
			<?php
	}

	$css = ob_get_clean();
	return $css;
}


	/**
	 * Generates CSS based on customizer settings.
	 *
	 * @return string
	 */

function napoleon_get_hero_styles() {
	$hero  = napoleon_get_hero_data();
	$style = '';

	if ( ! $hero['show'] ) {
		return apply_filters( 'napoleon_hero_styles', $style, $hero );
	}

	$styles_selector  = '.page-hero';
	$overlay_selector = '.page-hero::before';

	$support = get_theme_support( 'napoleon-hero' );
	$support = $support[0];
	if ( is_page_template( 'templates/builder.php' ) && true === $support['required'] ) {
		$styles_selector  = '.header';
		$overlay_selector = '.header::before';
	}

	$styles_selector  = apply_filters( 'napoleon_hero_styles_selector', $styles_selector );
	$overlay_selector = apply_filters( 'napoleon_hero_styles_overlay_selector', $overlay_selector );

	if ( $hero['overlay_color'] ) {
		$style .= $overlay_selector . ' { ';
		$style .= sprintf(
			'background-color: %s; ',
			$hero['overlay_color']
		);
		$style .= '} ' . PHP_EOL;
	}

	if ( $hero['bg_color'] || $hero['image'] || $hero['text_color'] ) {
		$style .= $styles_selector . ' { ';

		if ( $hero['bg_color'] ) {
			$style .= sprintf(
				'background-color: %s; ',
				$hero['bg_color']
			);
		}

		if ( $hero['text_color'] ) {
			$style .= sprintf(
				'color: %s; ',
				$hero['text_color']
			);
		}

		if ( $hero['image'] ) {
			$style .= sprintf(
				'background-image: url(%s); ',
				$hero['image']
			);

			if ( $hero['image_repeat'] ) {
				$style .= sprintf(
					'background-repeat: %s; ',
					$hero['image_repeat']
				);
			}

			if ( $hero['image_position_x'] && $hero['image_position_y'] ) {
				$style .= sprintf(
					'background-position: %s %s; ',
					$hero['image_position_x'],
					$hero['image_position_y']
				);
			}

			if ( $hero['image_attachment'] ) {
				$style .= sprintf(
					'background-attachment: %s; ',
					$hero['image_attachment']
				);
			}

			if ( ! $hero['image_cover'] ) {
				$style .= 'background-size: auto; ';
			}
		}

		$style .= '}';
	}

	return apply_filters( 'napoleon_hero_styles', $style, $hero );
}

if ( ! function_exists( 'napoleon_get_all_customizer_css' ) ) :
	function napoleon_get_all_customizer_css() {
		$styles = array(
			'customizer' =>  napoleon_get_customizer_css(),
			'hero'       => napoleon_get_hero_styles(),
		);

		$styles = apply_filters( 'napoleon_all_customizer_css', $styles );

		if ( is_customize_preview() ) {
			$styles[] = '/* Placeholder for preview. */';
		}

		return implode( PHP_EOL, $styles );
	}
endif;

add_filter( 'napoleon_customizer_css', 'napoleon_minimize_css' );
function napoleon_minimize_css( $css ) {
	$css = preg_replace( '/\s+/', ' ', $css );
	return $css;
}