<?php
/**
 * Custom template tags for this theme
 */
add_action( 'napoleon_before_head_mast', 'napoleon_top_head');

add_action( 'napoleon_head_mast', 'napoleon_header_branding', 10 );

add_action( 'napoleon_head_mast', 'napoleon_main_nav', 20 );

add_action( 'napoleon_head_mast', 'napoleon_header_icons', 30 );

add_action( 'napoleon_head_mast', 'napoleon_product_header', 40 );


add_action( 'napoleon_head_search', 'napoleon_header_search' );


add_action( 'napoleon_the_post_header', 'napoleon_the_post_entry_date', 10 );
add_action( 'napoleon_the_post_header', 'napoleon_the_post_entry_title', 20 );
add_action( 'napoleon_the_post_header', 'napoleon_the_post_entry_meta', 30 );

add_action( 'napoleon_the_post_entry_meta', 'napoleon_the_post_entry_sticky_label', 10 );
add_action( 'napoleon_the_post_entry_meta', 'napoleon_the_post_entry_categories', 20 );
add_action( 'napoleon_the_post_entry_meta', 'napoleon_the_post_entry_author', 30 );
add_action( 'napoleon_the_post_entry_meta', 'napoleon_the_post_entry_comments_link', 40 );

function napoleon_header() {
	do_action( 'napoleon_before_header' );

	?>

	<header class="<?php napoleon_the_header_classes(); ?> ">

		<?php do_action( 'napoleon_before_head_mast' );


		$sticky = '';
			if ( get_theme_mod( 'theme_header_primary_menu_sticky' ) ) {
				$sticky = 'head-sticky';
			}

		?>
		
		<div class="head-mast <?php echo esc_attr( $sticky ); ?> ">
			<div class="head-mast-container">
				
				<div class="head-mast-row  ">
					<?php
						/**
						 * napoleon_head_mast hook.
						 *
						 * @hooked napoleon_header_branding - 10
						 * @hooked napoleon_main_nav - 20
						 * @hooked napoleon_header_search_icon - 30
						 * @hooked napoleon_header_minicart - 40
						 */
						do_action( 'napoleon_head_mast' );
					?>
				</div>
			</div>
			<?php do_action( 'napoleon_head_search' ); ?>

		</div>

		<?php do_action( 'napoleon_after_head_mast' ); ?>

	</header>
	<?php

	do_action( 'napoleon_after_header' );
}

function napoleon_header_icons() { ?>

	<div class="head-icons">
		
		<?php napoleon_header_minicart(); ?>

		<div class="header-search-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" role="img" class="sellzy-open-search fill-current"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.6002 12.0498C9.49758 12.8568 8.13777 13.3333 6.66667 13.3333C2.98477 13.3333 0 10.3486 0 6.66667C0 2.98477 2.98477 0 6.66667 0C10.3486 0 13.3333 2.98477 13.3333 6.66667C13.3333 8.15637 12.8447 9.53194 12.019 10.6419C12.0265 10.6489 12.0338 10.656 12.0411 10.6633L15.2935 13.9157C15.6841 14.3063 15.6841 14.9394 15.2935 15.33C14.903 15.7205 14.2699 15.7205 13.8793 15.33L10.6269 12.0775C10.6178 12.0684 10.6089 12.0592 10.6002 12.0498ZM11.3333 6.66667C11.3333 9.244 9.244 11.3333 6.66667 11.3333C4.08934 11.3333 2 9.244 2 6.66667C2 4.08934 4.08934 2 6.66667 2C9.244 2 11.3333 4.08934 11.3333 6.66667Z"></path></svg>
         </div>
   	</div>

    <?php 

}


function napoleon_main_nav() {
		
		if ( has_nav_menu( 'menu-1' ) ) : ?>

		<div class="head-nav ">
			<nav class="nav">
				<?php
					wp_nav_menu( array(
						'theme_location' => 'menu-1',
						'container'      => '',
						'menu_id'        => 'header-menu-1',
						'menu_class'     => 'navigation-main',
					) );
				?>
			</nav>
		</div>
		<?php endif; 

}

function napoleon_product_header() {

		if  ( class_exists( 'WooCommerce' ) &&  is_product() ):  
			$product_id = get_the_ID(); // Get the current product ID
		    // Get the product object
		    $product = wc_get_product($product_id);
		    ?>
				<div class="product-name">
					<?php the_title(); 
					if ($product->get_average_rating() ) { ?>
						<div class="star-rating" role="img" aria-label="Rated 5 out of 5"></div>
					<?php } ?>
				</div>
				<div class="product-price"><?php  echo $product->get_price_html(); ?></div>
		<?php endif; 
}

function napoleon_footer() {
	$sidebars           = array( 'footer-1', 'footer-2', 'footer-3', 'footer-4' );
	$classes            = napoleon_footer_widget_area_classes( get_theme_mod( 'footer_layout', napoleon_footer_layout_default() ) );
	$has_active_sidebar = false;
	foreach ( $sidebars as $sidebar ) {
		if ( is_active_sidebar( $sidebar ) && $classes[ $sidebar ]['active'] ) {
			$has_active_sidebar = true;
			break;
		}
	}

	do_action( 'napoleon_before_footer' );

	?>
	<footer class="<?php napoleon_the_footer_classes(); ?>">
		<?php if ( $has_active_sidebar ) : ?>
			<div class="footer-widgets">
				<div class="container">
					<div class="row">
						<?php foreach ( $sidebars as $sidebar ) : ?>
							<?php if ( $classes[ $sidebar ]['active'] ) : ?>
								<div class="<?php echo esc_attr( $classes[ $sidebar ]['class'] ); ?>">
									<?php dynamic_sidebar( $sidebar ); ?>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php napoleon_footer_bottom_bar(); ?>
	</footer>
	<?php

	do_action( 'napoleon_after_footer' );
}

function napoleon_footer_bottom_bar() {

	do_action( 'napoleon_before_footer_info' );

	?>
	<div class="footer-info">
		<div class="container">
			<div class="row align-items-center">


				<div class="col-sm-6">
					<div class="footer-copyrights">

					<?php
					if ( get_theme_mod('napoleon_footer_text', '') ) :
						
						echo esc_html( get_theme_mod('napoleon_footer_text') );
					
					else : 	
						$copyright = '&copy; %year% %blogname%' ;
						$copyright = str_replace( '%year%', date( 'Y' ), $copyright );
						$copyright = str_replace( '%blogname%', get_bloginfo( 'name' ), $copyright );

						echo esc_html( $copyright );
					endif; ?>

					</div>
				</div>


				<div class="col-sm-6">

				<?php if ( get_theme_mod( 'footer_show_developer', 1 ) == 1 ) : ?>

					
					<p class="footer-credit">
						<?php /* translators: %s is a URL. */


						if ( is_front_page() || is_home() ) {
 
							$text = sprintf( __( 'Powered by <a href=%s>Napoleon</a>', 'napoleon' ),
								esc_url( 'https://bitherhood.com/' ) );
						} else {
							$text = sprintf( __( 'Powered by <a rel="nofollow" href=%s  >Napoleon</a>', 'napoleon' ),
								esc_url( 'https://bitherhood.com/' ) );
						}


							echo $text;
						?>
				
					</p>

				<?php endif; ?>
				</div>

			</div>
		</div>
	</div>
	<?php

	do_action( 'napoleon_after_footer_info' );

}

function napoleon_get_default_footer_card_icons() {
	return apply_filters( 'napoleon_default_footer_card_icons', napoleon_sanitize_footer_card_icons( 'fa-cc-visa, fa-cc-mastercard, fa-cc-amex, fa-cc-discover, fa-cc-diners-club, fa-cc-paypal, fa-cc-apple-pay, fa-cc-amazon-pay' ) );
}

function napoleon_top_head() {
	if ( get_theme_mod( 'top_bar_display', 1 ) ) { ?>

	<div class="top-head-wrap clear">
		<div class="container">

			<?php if ( get_theme_mod( 'header_announcement' ) ) : ?>
				<?php if ( get_theme_mod( 'header_announcement_2' ) ) : ?>
					<div class="top-head-center top-slider">
						<div><?php echo esc_html( get_theme_mod( 'header_announcement' ) ); ?></div>
						<div><?php echo esc_html( get_theme_mod( 'header_announcement_2' ) ); ?></div>
						<?php if ( get_theme_mod( 'header_announcement_3' ) ) : ?>
							<div><?php echo esc_html( get_theme_mod( 'header_announcement_3' ) ); ?></div>
						<?php endif; ?>
					</div>
				<?php else: ?>
					<div class="top-head-center">
						<?php echo esc_html( get_theme_mod( 'header_announcement' ) ); ?>
					</div>
				<?php endif; ?>
			<?php else: ?>
				<div class="top-head-left">
					<div class="share-links">
						<?php napoleon_the_social_icons(); ?>
					</div>
				</div>
				<div class="top-head-right">
					<div class="top-menu">
						<?php
						wp_nav_menu( array(
							'theme_location' => 'menu-top',
							'container'      => '',
							'menu_id'        => 'top-head-menu',
							'menu_class'     => 'navigation-top',
						) );
						?>
					</div>	
				</div>
			<?php endif; ?>

		</div>
	</div>

	<?php }
}

function napoleon_header_branding() {
	?>
	<div class="header-branding-wrap">
		<?php
			ob_start();
			wp_nav_menu( array(
				'theme_location' => 'menu-1',
				'container'      => '',
				'menu_id'        => 'header-menu-1',
				'menu_class'     => 'navigation-main navigation-main-right',
			) );
			$menu = trim( ob_get_clean() );
		?>
		<?php if ( ! empty( $menu ) ) : ?>
			<div class="mobile-nav-trigger"><i class="fas fa-bars"></i> <span class="sr-only"><?php esc_html_e( 'Menu', 'napoleon' ); ?></span></div>
		<?php endif; ?>

		<?php napoleon_the_site_identity(); ?>
	</div>
	<?php
}

function napoleon_header_search() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}


	$ajax_class   = '';
	$autocomplete = 'on';

	if ( get_theme_mod( 'header_search_ajax', 1 ) ) {
		$ajax_class   = 'form-ajax-enabled';
		$autocomplete = 'off';
	}
	
	if ( get_theme_mod( 'header_search_display', 1 ) ) { ?>
	<div class="head-search-form-wrap">
		<div class="head-search-container">
			<form class="category-search-form <?php echo esc_attr( $ajax_class ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
				<label for="category-search-name" class="sr-only" >
					<?php esc_html_e( 'Category name', 'napoleon' ); ?>
				</label>

				<?php wp_dropdown_categories( array(
					'taxonomy'          => 'product_cat',
					'show_option_none'  => esc_html__( 'Search all categories', 'napoleon' ),
					'option_none_value' => '',
					'value_field'       => 'slug',
					'hide_empty'        => 1,
					'echo'              => 1,
					'hierarchical'      => 1,
					'name'              => 'product_cat',
					'id'                => 'category-search-name',
					'class'             => 'category-search-select',
				) ); ?>

				<div class="category-search-input-wrap">
					<label for="category-search-input" class="sr-only">
						<?php esc_html_e( 'Search text', 'napoleon' ); ?>
					</label>
					<input
						type="text"
						class="category-search-input"
						id="category-search-input"
						placeholder="<?php esc_attr_e( 'What are you looking for?', 'napoleon' ); ?>"
						name="s"
						autocomplete="<?php echo esc_attr( $autocomplete ); ?>"
					/>

					<ul class="category-search-results">
						<li class="category-search-results-item">
							<a href="">
								<span class="category-search-results-item-title"></span>
							</a>
						</li>
					</ul>
					<span class="category-search-spinner"></span>
					<input type="hidden" name="post_type" value="product" />
				</div>

				<button type="submit" class="category-search-btn">
					<i class="fas fa-search"></i><span class="sr-only"><?php echo esc_html_x( 'Search', 'submit button', 'napoleon' ); ?></span>
				</button>
			</form>
		</div>
	</div>
	<?php }
}

function napoleon_header_minicart() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	?>

	<?php if ( get_theme_mod( 'cart_display', 1 ) ) { ?>
	<div class="head-mini-cart-wrap">
		<div class="header-mini-cart">
			<a href="#" class="header-mini-cart-trigger">
				<i class="fas fa-shopping-cart"></i><span class="cart-count"><?php echo class_exists( 'WooCommerce' ) && WC()->cart ? esc_html( WC()->cart->get_cart_contents_count() ) : 0; ?></span>
			</a>

			<div class="header-mini-cart-contents">

				<aside class="widget woocommerce widget_shopping_cart">
					<h3 class="widget-title"><?php esc_html_e( 'Cart', 'napoleon' ); ?></h3>

					<div class="widget_shopping_cart_content">
						<?php woocommerce_mini_cart(); ?>

						<p class="buttons">
							<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="button wc-forward"><?php esc_html_e( 'View Cart', 'napoleon' ); ?></a>
							<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button checkout wc-forward"><?php esc_html_e( 'Checkout', 'napoleon' ); ?></a>
						</p>
					</div>
				</aside>

			</div>
		</div>
	</div>
	<?php }
}



// Ajax refreshing mini cart count and content
add_filter( 'woocommerce_add_to_cart_fragments', 'my_header_add_to_cart_fragment' );
function my_header_add_to_cart_fragment( $fragments ) {
    $count = WC()->cart->get_cart_contents_count();

    $fragments['.cart-count'] = '</span> <span class="cart-count">' .  esc_attr( $count ) . '</span>';

    ob_start();
    ?>
    <?php napoleon_header_minicart(); ?>
    <?php

    $fragments['#mini-cart-content'] = ob_get_clean();

    return $fragments;
}


/**
 * Echoes the logo / site title / description, depending on customizer options.
 */
function napoleon_the_site_identity() {
	do_action( 'napoleon_before_site_identity' );

	?><div class="site-branding"><?php

	if ( has_custom_logo() && get_theme_mod( 'show_site_title', 1 ) ) {
		the_custom_logo();

		?><div class="site-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></div><?php
	} elseif ( has_custom_logo() ) {
		?><div class="site-logo"><?php the_custom_logo(); ?></div><?php
	} elseif ( get_theme_mod( 'show_site_title', 1 ) ) {
		?><div class="site-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></div><?php
	}

	if ( get_theme_mod( 'show_site_description', 1 ) ) {
		$description = get_bloginfo( 'description', 'display' );
		if ( $description || is_customize_preview() ) {
			?><p class="site-tagline"><?php echo $description; /* WPCS: xss ok. */ ?></p><?php
		}
	}

	?></div><?php

	do_action( 'napoleon_after_site_identity' );
}

/**
 * Echoes header classes based on customizer options
 */
function napoleon_the_header_classes() {
	$classes = apply_filters( 'napoleon_header_classes', array(
		'header',
		get_theme_mod( 'header_fullwidth' ) ? 'header-fullwidth' : '',
		get_theme_mod( 'header_search_display', 1 ) ? '' : 'hide-search',
	) );

	$classes = array_filter( $classes );

	echo esc_attr( implode( ' ', $classes ) );
}

/**
 * Echoes header classes based on customizer options
 */
function napoleon_the_footer_classes() {
	$classes = apply_filters( 'napoleon_footer_classes', array(
		'footer',
		get_theme_mod( 'footer_fullwidth' ) ? 'footer-fullwidth' : '',
	) );

	$classes = array_filter( $classes );

	echo esc_attr( implode( ' ', $classes ) );
}

function napoleon_the_post_thumbnail( $size = false ) {
	if ( ! $size ) {
		$size = 'post-thumbnail';
	}

	if ( ! has_post_thumbnail() || ! get_theme_mod( 'post_show_featured', 1 ) ) {
		return;
	}

	do_action( 'napoleon_before_the_post_thumbnail' );

	if ( is_singular() && get_the_ID() === get_queried_object_id() ) {
		$caption = napoleon_get_image_lightbox_caption( get_post_thumbnail_id() );
		?>
		<figure class="entry-thumb">
			<a class="napoleon-lightbox" href="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>" title="<?php echo esc_attr( $caption ); ?>">
				<?php the_post_thumbnail( $size ); ?>
			</a>
		</figure>
		<?php
	} else {
		?>
		<figure class="entry-thumb">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( $size ); ?>
			</a>
		</figure>
		<?php
	}

	do_action( 'napoleon_after_the_post_thumbnail' );
}

function napoleon_the_post_header() {
	ob_start();

	/**
	 * napoleon_the_post_header hook.
	 *
	 * @hooked napoleon_the_post_entry_date - 10
	 * @hooked napoleon_the_post_entry_title - 20
	 * @hooked napoleon_the_post_entry_meta - 30
	 */
	do_action( 'napoleon_the_post_header' );

	$html = ob_get_clean();

	if ( trim( $html ) ) {
		$html = sprintf( '<header class="entry-header">%s</header>', $html );
	}

	do_action( 'napoleon_before_the_post_header', $html );

	echo $html; // WPCS: XSS ok.

	do_action( 'napoleon_after_the_post_header', $html );
}

function napoleon_the_post_entry_title() {
	if ( is_singular() && get_the_ID() === get_queried_object_id() ) {
		$hero = napoleon_get_hero_data();

		if ( ! $hero['page_title_hide'] ) {
			?>
			<h1 class="entry-title">
				<?php the_title(); ?>
			</h1>
			<?php
		}
	} else {
		?>
		<h1 class="entry-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h1>
		<?php
	}
}

function napoleon_the_post_entry_meta() {
	ob_start();

	/**
	 * napoleon_the_post_entry_meta hook.
	 *
	 * @hooked napoleon_the_post_entry_sticky_label - 10
	 * @hooked napoleon_the_post_entry_categories - 20
	 * @hooked napoleon_the_post_entry_author - 30
	 * @hooked napoleon_the_post_entry_comments_link - 40
	 */
	do_action( 'napoleon_the_post_entry_meta' );

	$html = ob_get_clean();

	if ( trim( $html ) ) {
		$html = sprintf( '<div class="entry-meta">%s</div>', $html );
	}

	do_action( 'napoleon_before_the_post_entry_meta', $html );

	echo $html; // WPCS: XSS ok.

	do_action( 'napoleon_after_the_post_entry_meta', $html );
}

function napoleon_the_post_entry_sticky_label() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( ! is_singular() && is_sticky() ) {
		?>
		<span class="entry-meta-item entry-sticky">
			<?php esc_html_e( 'Featured', 'napoleon' ); ?>
		</span>
		<?php
	}
}

function napoleon_the_post_entry_date() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( get_theme_mod( 'post_show_date', 1 ) ) {
		?>
		<div class="entry-meta">
			<span class="entry-meta-item">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo get_the_date(); ?></time>
			</span>
		</div>
		<?php
	}
}

function napoleon_the_post_entry_categories() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( get_theme_mod( 'post_show_categories', 1 ) ) {
		?>
		<span class="entry-meta-item entry-categories">
			<?php the_category( ', ' ); ?>
		</span>
		<?php
	}
}

function napoleon_the_post_entry_author() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( get_theme_mod( 'post_show_author', 1 ) ) {
		?>
		<span class="entry-meta-item entry-author">
			<?php
				printf(
					/* translators: %s is the author's name. */
					esc_html_x( 'by %s', 'post author', 'napoleon' ),
					'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
				);
			?>
		</span>
		<?php
	}
}

function napoleon_the_post_entry_comments_link() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( get_theme_mod( 'post_show_comments', 1 ) ) {
		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			?>
			<span class="entry-meta-item entry-comments-link">
				<?php
					/* translators: %s: post title */
					comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'napoleon' ), array(
						'span' => array(
							'class' => array(),
						),
					) ), get_the_title() ) );
				?>
			</span>
			<?php
		}
	}
}

function napoleon_the_post_author_box() {

	do_action( 'napoleon_before_the_post_author_box' );

	get_template_part( 'template-parts/authorbox' );

	do_action( 'napoleon_after_the_post_author_box' );
}

/**
 * @param string $$context May be 'global' or 'user'. If false, it will try to decide by itself.
 */
function napoleon_the_social_icons( $context = false ) {
	$networks    = napoleon_get_social_networks();

	$global_urls = array();
	$user_urls   = array();
	$used_urls   = array();

	$global_rss  = get_theme_mod( 'theme_rss_feed', get_bloginfo( 'rss2_url' ) );
	$user_rss    = get_author_feed_link( get_the_author_meta( 'ID' ) );
	$used_rss    = '';

	foreach ( $networks as $network ) {
		if ( get_theme_mod( 'theme_social_' . $network['name'] ) ) {
			$global_urls[ $network['name'] ] = get_theme_mod( 'theme_social_' . $network['name'] );
		}
	}

	foreach ( $networks as $network ) {
		if ( get_the_author_meta( 'user_' . $network['name'] ) ) {
			$user_urls[ $network['name'] ] = get_the_author_meta( 'user_' . $network['name'] );
		}
	}

	if ( 'user' === $context ) {
		$used_urls = $user_urls;
		$used_rss  = $user_rss;
	} elseif ( 'global' === $context ) {
		$used_urls = $global_urls;
		$used_rss  = $global_rss;
	} else {
		$used_urls = $global_urls;
		$used_rss  = $global_rss;

		if ( in_the_loop() ) {
			$used_urls = $user_urls;
			$used_rss  = $user_rss;
		}
	}

	$used_urls = apply_filters( 'napoleon_social_icons_used_urls', $used_urls, $context, $global_urls, $user_urls );
	$used_rss  = apply_filters( 'napoleon_social_icons_used_rss', $used_rss, $context, $global_rss, $user_rss );

	$has_rss = $used_rss ? true : false;

	// Set the target attribute for social icons.
	$add_target = false;
	if ( get_theme_mod( 'theme_social_target', 1 ) ) {
		$add_target = true;
	}

	if ( count( $used_urls ) > 0 || $has_rss ) {
		do_action( 'napoleon_before_the_social_icons' );
		?>
<ul class="list-social-icons">
    <?php
    foreach ( $networks as $network ) {
        if ( ! empty( $used_urls[ $network['name'] ] ) ) {
            $link_label = sprintf( __( 'Link to %s', 'napoleon' ), $network['name'] );
            ?>
            <li>
                <a href="<?php echo esc_url( $used_urls[ $network['name'] ] ); ?>" class="social-icon" aria-label="<?php echo esc_attr( $link_label ); ?>" target="_blank">
                    <i class="<?php echo esc_attr( $network['icon'] ); ?>"></i>
                </a>
            </li>
            <?php
        }
    }

    if ( $has_rss ) {
        $rss_label = __( 'Link to RSS Feed', 'your-textdomain' );
        ?>
        <li>
            <a href="<?php echo esc_url( $used_rss ); ?>" class="social-icon" aria-label="<?php echo esc_attr( $rss_label ); ?>">
                <i class="<?php echo esc_attr( 'fas fa-rss' ); ?>"></i>
            </a>
        </li>
        <?php
    }
    ?>
</ul>


		<?php
		do_action( 'napoleon_after_the_social_icons' );
	}
}


/**
 * Echoes pagination links if applicable. Output depends on pagination method selected from the customizer.
 *
 * @uses the_post_pagination()
 * @uses previous_posts_link()
 * @uses next_posts_link()
 *
 * @param array $args An array of arguments to change default behavior.
 * @param WP_Query|null $query A WP_Query object to paginate. Defaults to null and uses the global $wp_query
 *
 * @return void
 */
function napoleon_posts_pagination( $args = array(), WP_Query $query = null ) {
	$args = wp_parse_args( $args, apply_filters( 'napoleon_posts_pagination_default_args', array(
		'mid_size'           => 1,
		'prev_text'          => _x( 'Previous', 'previous post', 'napoleon' ),
		'next_text'          => _x( 'Next', 'next post', 'napoleon' ),
		'screen_reader_text' => __( 'Posts navigation', 'napoleon' ),
		'container_id'       => '',
		'container_class'    => '',
	), $query ) );

	global $wp_query;

	if ( ! is_null( $query ) ) {
		$old_wp_query = $wp_query;
		$wp_query     = $query;
	}

	$output = '';
	$method = get_theme_mod( 'pagination_method', 'numbers' );

	switch ( $method ) {
		case 'text':
			$output = get_the_posts_navigation( $args );
			break;
		case 'numbers':
		default:
			$output = get_the_posts_pagination( $args );
			break;
	}

	if ( ! empty( $output ) && ! empty( $args['container_id'] ) || ! empty( $args['container_class'] ) ) {
		$output = sprintf( '<div id="%2$s" class="%3$s">%1$s</div>', $output, esc_attr( $args['container_id'] ), esc_attr( $args['container_class'] ) );
	}

	if ( ! is_null( $query ) ) {
		$wp_query = $old_wp_query;
	}

	// All markup is from native WordPress functions. The wrapping div is properly escaped above.
	$output_safe = $output;

	echo $output_safe;
}

/**
 * Echoes row classes based on whether the current template has a visible sidebar or not,
 * and depending on sidebar visibility option on single post/pages/etc.
 */
function napoleon_the_row_classes() {
	$info = napoleon_get_layout_info();
	echo esc_attr( $info['row_classes'] );
}

/**
 * Echoes container classes based on whether
 * the current template has a visible sidebar or not
 */
function napoleon_the_container_classes() {
	$info = napoleon_get_layout_info();
	echo esc_attr( $info['container_classes'] );
}

/**
 * Echoes container classes based on whether
 * the current template has a visible sidebar or not
 */
function napoleon_the_sidebar_classes() {
	$info = napoleon_get_layout_info();
	echo esc_attr( $info['sidebar_classes'] );
}


function napoleon_the_item_thumbnail( $size = false ) {
	if ( ! $size ) {
		$size = 'post-thumbnail';
	}

	if ( ! has_post_thumbnail() || ! get_theme_mod( 'post_show_featured', 1 ) ) {
		return;
	}

	do_action( 'napoleon_before_the_item_thumbnail' );

	?>
	<div class="item-thumb">
		<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( $size ); ?>
		</a>
	</div>
	<?php

	do_action( 'napoleon_after_the_item_thumbnail' );
}

function napoleon_the_post_item_date() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( get_theme_mod( 'post_show_date', 1 ) ) {
		?>
		<div class="item-meta">
			<time class="item-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo get_the_date(); ?></time>
		</div>
		<?php
	}
}
