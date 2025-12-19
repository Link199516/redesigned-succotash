<?php
//
// WooCommerce integration
//

// This needs to be here. Don't remove. Don't wrap this file's include/require in a check like this.
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}


add_action( 'woocommerce_product_query', 'change_posts_per_page', 999 );
function change_posts_per_page( $query ) {

    if( is_admin() )
        return;

    $query->set( 'posts_per_page', 12);
    
}

add_action( 'after_setup_theme', 'napoleon_woocommerce_activation' );
function napoleon_woocommerce_activation() {

	add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width'         => 630,
		'single_image_width'            => 690,
		'gallery_thumbnail_image_width' => 160,
		'product_grid'                  => array(
			'default_columns' => 3,
			'min_columns'     => 2,
			'max_columns'     => 4,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'wc-product-gallery-lightbox' );

}

add_action( 'init', 'napoleon_woocommerce_integration' );
function napoleon_woocommerce_integration() {

	add_filter( 'woocommerce_upsells_total', 'napoleon_woocommerce_upsells_total' );
	add_filter( 'woocommerce_cross_sells_total', 'napoleon_woocommerce_cross_sells_total' );

	// Only add filter when the query param is set, otherwise the 'woocommerce_catalog_rows' customizer option doesn't appear.
	if ( isset( $_GET['view'] ) ) {
		add_filter( 'loop_shop_per_page', 'napoleon_woocommerce_loop_shop_per_page_view' );
	}

	// Shop page
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	add_action( 'woocommerce_before_shop_loop', 'napoleon_woocommerce_shop_filters', 20 );
	add_action( 'woocommerce_before_shop_loop', 'napoleon_woocommerce_shop_actions', 30 );

	// Shop item
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	add_action( 'woocommerce_shop_loop_item_title', 'napoleon_woocommerce_show_product_loop_categories', 5 );
	//remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	//add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 10 );

	// Category
	remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
	remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
	remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
	add_action( 'woocommerce_before_subcategory', 'napoleon_woocommerce_subcategory_thumbnail', 10 );

	// Single product
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 ); // Make sure default meta is removed
	// add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 3 ); // Remove theme's added meta
	//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	// add_action( 'woocommerce_single_product_summary', 'napoleon_woocommerce_single_product_tags', 30 ); // Remove theme's added tags

	if ( 0 === get_theme_mod( 'show_breadcrumb', 0 ) ) {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	}

}

add_action( 'wp', 'napoleon_woocommerce_integration_late' );

function napoleon_woocommerce_integration_late() {
	// These require features that are not yet ready on 'init'.
	// For example, is_product() (which is also implicitly called from napoleon_has_sidebar()) doesn't work properly on init.

	// Single product
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

	// Only add these in single products, as the 'woocommerce_after_main_content' hook exists in other templates too.
	if ( is_product() ) {
		if ( napoleon_has_sidebar() ) {
			add_action( 'woocommerce_after_single_product', 'woocommerce_output_product_data_tabs', 10 );
		} else {
			add_action( 'woocommerce_after_main_content', 'woocommerce_output_product_data_tabs', 4 );
		}

		add_action( 'woocommerce_after_main_content', 'woocommerce_upsell_display', 6 );

		if ( 1 === get_theme_mod( 'show_related_products', 0 ) ) {
			add_action( 'woocommerce_after_single_product', 'napoleon_related_products_by_category_or_tag', 8 );
		}
		
	}

	if ( 0 === get_theme_mod( 'product_show_add_to_cart', 0 ) ) {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		add_action( 'woocommerce_after_shop_loop_item', 'napoleon_template_loop_product_link', 10 );

	}
}

if ( ! function_exists( 'napoleon_get_shop_layout_choices' ) ) :
	function napoleon_get_shop_layout_choices() {
		return apply_filters( 'napoleon_shop_layout_choices', array(
			'left'  => esc_html__( 'Left Sidebar', 'napoleon' ),
			'right' => esc_html__( 'Right Sidebar', 'napoleon' ),
			'full'  => esc_html__( 'Full Width', 'napoleon' ),
		) );
	}
endif;

if ( ! function_exists( 'napoleon_sanitize_shop_layout' ) ) :
	function napoleon_sanitize_shop_layout( $value ) {
		$choices = napoleon_get_shop_layout_choices();
		if ( array_key_exists( $value, $choices ) ) {
			return $value;
		}

		return apply_filters( 'napoleon_sanitize_shop_layout_default', 'right' );
	}
endif;

if ( ! function_exists( 'napoleon_get_product_layout_choices' ) ) :
	function napoleon_get_product_layout_choices() {
		return apply_filters( 'napoleon_product_layout_choices', array(
			'left'  => esc_html__( 'Left Sidebar', 'napoleon' ),
			'right' => esc_html__( 'Right Sidebar', 'napoleon' ),
			'none'  => esc_html__( 'No Sidebar', 'napoleon' ),
		) );
	}
endif;

if ( ! function_exists( 'napoleon_sanitize_product_layout' ) ) :
	function napoleon_sanitize_product_layout( $value ) {
		$choices = napoleon_get_product_layout_choices();
		if ( array_key_exists( $value, $choices ) ) {
			return $value;
		}

		return apply_filters( 'napoleon_sanitize_product_layout_default', 'right' );
	}
endif;


add_filter( 'woocommerce_breadcrumb_defaults', 'napoleon_woocommerce_breadcrumb_defaults' );
function napoleon_woocommerce_breadcrumb_defaults( $args ) {
	$args['wrap_before'] = '<div class="col-12">' . $args['wrap_before'];
	$args['wrap_after']  = $args['wrap_after'] . '</div>';
	$args['delimiter']   = '<span>&sol;</span>';

	return $args;
}


remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'napoleon_template_loop_product_title', 10 );

function napoleon_template_loop_product_title() {
	?>
	<p class="item-title">
		<a href="<?php the_permalink(); ?>">
			<?php the_title(); ?>
		</a>
	</p>
	<?php
}

function napoleon_woocommerce_show_product_loop_categories() {
	if ( ! get_theme_mod( 'product_show_categories', 1 ) ) {
		return;
	}

	/** @var $product WC_Product */
	global $product;
	echo wp_kses( wc_get_product_category_list( $product->get_id(), ', ', '<div class="item-meta"><span class="item-categories">', '</span></div>' ), napoleon_get_allowed_tags( 'woocommerce_terms' ) );
}

if ( ! function_exists( 'napoleon_woocommerce_shop_filters' ) ) :
	function napoleon_woocommerce_shop_filters() {
		$layout = get_theme_mod( 'theme_shop_layout', 'right' );

		if ( 'full' !== $layout || ! is_active_sidebar( 'shop' ) ) {
			return;
		}

		?>
		<div class="sidebar sidebar-drawer">
			<div class="sidebar-drawer-header">
				<a href="#" class="sidebar-dismiss">&times; <span class="screen-reader-text"><?php esc_html_e( 'Close drawer', 'napoleon' ); ?></span></a>
			</div>

			<div class="sidebar-drawer-content custom-scrollbar">
				<?php dynamic_sidebar( 'shop' ); ?>
			</div>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'napoleon_woocommerce_shop_actions' ) ) :
	function napoleon_woocommerce_shop_actions() {
		$layout = get_theme_mod( 'theme_shop_layout', 'right' );

		$actions_class = '';
		if ( ! is_active_sidebar( 'shop' ) ) {
			$actions_class = 'shop-actions-no-filter';
		} elseif ( 'full' !== $layout ) {
			$actions_class = 'with-sidebar';
		}

		?>
		<div class="shop-actions <?php echo esc_attr( $actions_class ); ?>">

			<?php if ( is_active_sidebar( 'shop' ) ) : ?>
				<a href="#"	class="shop-filter-toggle">
					<i class="fa fa-bars"></i> <?php esc_html_e( 'Filters', 'napoleon' ); ?>
				</a>
			<?php endif; ?>

			<div class="shop-action-results">
				<?php woocommerce_result_count(); ?>

				<?php
					$first  = absint( apply_filters( 'napoleon_products_view_first', 25 ) );
					$second = absint( apply_filters( 'napoleon_products_view_second', 50 ) );

					$active_class = 'product-number-active';

					$classes = array(
						'first'  => '',
						'second' => '',
						'all'    => '',
					);

					if ( ! empty( $_GET['view'] ) ) {
						if ( 'all' === $_GET['view'] ) {
							$classes['all'] = $active_class;
						} else {
							$view = absint( $_GET['view'] );
							if ( $view === $first ) {
								$classes['first'] = $active_class;
							} elseif ( $view === $second ) {
								$classes['second'] = $active_class;
							}
						}
					}
				?>
				<div class="product-number">
					<span><?php esc_html_e( 'View:', 'napoleon' ); ?></span>
					<a href="<?php echo esc_url( add_query_arg( 'view', $first, get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>" class="<?php echo esc_attr( $classes['first'] ); ?>"><?php echo esc_html( $first ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'view', $second, get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>" class="<?php echo esc_attr( $classes['second'] ); ?>"><?php echo esc_html( $second ); ?></a>
					<?php if ( apply_filters( 'napoleon_products_view_all', true ) ) : ?>
						<a href="<?php echo esc_url( add_query_arg( 'view', 'all', get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>" class="<?php echo esc_attr( $classes['all'] ); ?>"><?php esc_html_e( 'All', 'napoleon' ); ?></a>
					<?php endif; ?>
				</div>
			</div>

			<?php woocommerce_catalog_ordering(); ?>
		</div>
		<?php
	}
endif;



remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
add_action( 'woocommerce_shop_loop_subcategory_title', 'napoleon_template_loop_category_title', 10 );

function napoleon_template_loop_category_title( $category ) {
	?>
	<p class="item-title">
		<a href="<?php echo esc_url( get_term_link( $category, 'product_cat' ) ); ?>">
			<?php
			echo esc_html( $category->name );

			if ( $category->count > 0 ) {
				echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . esc_html( $category->count ) . ')</mark>', $category ); // WPCS: XSS ok.
			}
			?>
		</a>
	</p>
	<?php
}

function napoleon_woocommerce_upsells_total() {
	return 4;
}

function napoleon_woocommerce_cross_sells_total() {
	return 2;
}

function napoleon_woocommerce_loop_shop_per_page_view( $posts_per_page ) {

	if ( empty( $_GET['view'] ) ) {
		return $posts_per_page;
	}

	if ( 'all' === $_GET['view'] ) {
		$view = - 1;
	} else {
		$view = absint( $_GET['view'] );
	}

	$first  = absint( apply_filters( 'napoleon_products_view_first', 25 ) );
	$second = absint( apply_filters( 'napoleon_products_view_second', 50 ) );

	$valid_values = array( $posts_per_page );

	if ( $first ) {
		$valid_values[] = $first;
	}

	if ( $second ) {
		$valid_values[] = $second;
	}

	if ( apply_filters( 'napoleon_products_view_all', true ) ) {
		$valid_values[] = -1;
	}

	if ( in_array( $view, $valid_values, true ) ) {
		return $view;
	}

	return $posts_per_page;
}


remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action('woocommerce_before_shop_loop_item_title', 'napoleon_template_loop_product_thumbnail', 10);

function napoleon_template_loop_product_thumbnail() {
	/** @var $product WC_Product */
	global $product;
	?>
	<div class="item-thumb">
		<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
			<?php echo woocommerce_get_product_thumbnail(); // WPCS: XSS ok. ?>
		</a>
	</div>
	<?php
}

function napoleon_woocommerce_subcategory_thumbnail( $category ) {
	?>
	<div class="item-thumb">
		<a href="<?php echo esc_url( get_term_link( $category, 'product_cat' ) ); ?>">
			<?php woocommerce_subcategory_thumbnail( $category ); ?>
		</a>
	</div>
	<?php
}


// Make some WooCommerce pages get the fullwidth template
add_filter( 'template_include', 'napoleon_woocommerce_fullwidth_pages' );
if ( ! function_exists( 'napoleon_woocommerce_fullwidth_pages' ) ) :
	function napoleon_woocommerce_fullwidth_pages( $template ) {
		$filename = 'templates/full-width-page.php';
		$located  = '';
		if ( file_exists( get_stylesheet_directory() . '/' . $filename ) ) {
			$located = get_stylesheet_directory() . '/' . $filename;
		} elseif ( file_exists( get_template_directory() . '/' . $filename ) ) {
			$located = get_template_directory() . '/' . $filename;
		} else {
			$located = '';
		}

		if ( ! empty( $located ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
			return $located;
		}

		return $template;
	}
endif;

function napoleon_woocommerce_get_wrap_login_templates() {
	return array(
		'myaccount/form-login.php',
		'myaccount/form-lost-password.php',
		'myaccount/lost-password-confirmation.php',
		'myaccount/form-reset-password.php',
	);
}

add_action( 'woocommerce_before_template_part', 'napoleon_woocommerce_wrap_login_forms_open', 10, 4 );
add_action( 'woocommerce_after_template_part', 'napoleon_woocommerce_wrap_login_forms_close', 10, 4 );
function napoleon_woocommerce_wrap_login_forms_open( $template_name, $template_path, $located, $args ) {
	if ( ! in_array( $template_name, napoleon_woocommerce_get_wrap_login_templates(), true ) ) {
		return;
	}
	$registration_class = '';
	if ( 'myaccount/form-login.php' === $template_name && 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) {
		$registration_class = 'with-register';
	}
	?>
	<div class="wc-form-login <?php echo esc_attr( $registration_class ); ?>">
		<style>.entry-title { display: none; }</style>
	<?php

}
function napoleon_woocommerce_wrap_login_forms_close( $template_name, $template_path, $located, $args ) {
	if ( ! in_array( $template_name, napoleon_woocommerce_get_wrap_login_templates(), true ) ) {
		return;
	}

	?></div><?php
}

function napoleon_woocommerce_single_product_tags() {
	global $product;
	echo wp_kses( wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'napoleon' ) . ' ', '</span>' ), napoleon_get_allowed_tags( 'woocommerce_terms' ) );
}

add_action( 'wp_ajax_napoleon_search_products', 'napoleon_ajax_search_products' );
add_action( 'wp_ajax_nopriv_napoleon_search_products', 'napoleon_ajax_search_products' );
function napoleon_ajax_search_products() {
	$s   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // Input var okay.
	$cat = isset( $_GET['product_cat'] ) ? sanitize_title_for_query( wp_unslash( $_GET['product_cat'] ) ) : false; // Input var okay.

	if ( ! is_string( $s ) || mb_strlen( $s ) < 3 ) {
		$response = array(
			'error'  => true,
			'errors' => array( 'Search term too short' ),
			'data'   => array(),
		);

		wp_send_json( $response );
	}

	$q_args = array(
		'post_type'           => 'product',
		'posts_per_page'      => 5,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		's'                   => $s,
	);

	$tax_args = array();

	if ( ! empty( $cat ) ) {
		$tax_args = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array( $cat ),
			),
		);
	}

	if ( ! empty( $tax_args ) ) {
		$q_args['tax_query'] = $tax_args;
	}

	$q = new WP_Query( $q_args );

	$response = array(
		'error'  => false,
		'errors' => array(),
		'data'   => array(),
	);

	while ( $q->have_posts() ) {
		$q->the_post();

		$result = array(
			'title' => html_entity_decode( get_the_title() ),
			'url'   => get_permalink(),
			'image' => get_the_post_thumbnail_url( get_the_ID(), 'napoleon_item_media_sm' ),
		);

		$response['data'][] = $result;
	}
	wp_reset_postdata();

	wp_send_json( $response );
}

add_filter( 'napoleon_get_allowed_tags', 'napoleon_get_allowed_tags_woocommerce_terms', 10, 2 );
function napoleon_get_allowed_tags_woocommerce_terms( $tags, $context ) {
	if ( 'woocommerce_terms' !== $context ) {
		return $tags;
	}

	$tags['div'] = array( 'class' => true );

	return $tags;
}


// Adds a link to product page insead of add to cart button in widgets

if ( ! function_exists( 'napoleon_template_loop_product_link' ) ) :
	function napoleon_template_loop_product_link() { 
		 global $product;
		if (  $product->is_in_stock() && get_theme_mod( 'display_buy_button', 1)  == 1 ) :  ?>

			<a href="<?php the_permalink(); ?>" class="button product_type_simple add_to_cart_button"><?php esc_html_e( get_theme_mod( 'buy_button_text', 'Buy it now'));  ?></a>
		<?php elseif  ( ! $product->is_in_stock() ) :  ?>
			 <span class="button product_type_simple add_to_cart_button disabledbutton" ><?php  _e('Out of stock', 'napoleon'); ?></span>
			<?php 
		endif;

}
endif;


add_filter( 'woocommerce_product_tabs', 'my_remove_description_tab', 11 );
 
function my_remove_description_tab( $tabs ) {
  unset( $tabs['description'] );
  unset( $tabs['additional_information'] );    // Remove the additional information tab

  return $tabs;
}


/**
 * Add Algeria New States Or Display old states if city field is active in form 
 */
add_filter( 'woocommerce_states', 'DZ_woocommerce_states' );

function DZ_woocommerce_states( $states ) {

	global $woocommerce;
    $countries_obj = new WC_Countries();
    $countries = $countries_obj->__get("countries");
    $default_country = $countries_obj->get_base_country();

 	
 	if ( get_theme_mod( 'display_city_field', 0)  == 1 && file_exists(get_theme_file_path() . '/inc/order-form/include/states/' . $default_country . '.php') ) : 

    	include(get_theme_file_path() . '/inc/order-form/include/states/' . $default_country . '.php');

	else: 

	$states['DZ'] = array(
	    'DZ1' => '01 Adrar - أدرار',
	    'DZ2' => '02 Chlef - الشلف',
	    'DZ3' => '03 Laghouat - الأغواط',
	    'DZ4' => '04 Oum El Bouaghi - أم البواقي',
	    'DZ5' => '05 Batna - باتنة', 
	    'DZ6' => '06 Béjaïa - بجاية',
	    'DZ7' => '07 Biskra - بسكرة',
	    'DZ8' => '08 Bechar - بشار',
	    'DZ9' => '09 Blida - البليدة',
	    'DZ10' => '10 Bouira - البويرة',
	    'DZ11' => '11 Tamanrasset - تمنراست ',
	    'DZ12' => '12 Tébessa - تبسة ',
	    'DZ13' => '13 Tlemcene - تلمسان',
	    'DZ14' => '14 Tiaret - تيارت',
	    'DZ15' => '15 Tizi Ouzou - تيزي وزو',
	    'DZ16' => '16 Alger - الجزائر',
	    'DZ17' => '17 Djelfa - الجلفة',
	    'DZ18' => '18 Jijel - جيجل',
	    'DZ19' => '19 Sétif - سطيف',
	    'DZ20' => '20 Saïda - سعيدة',
	    'DZ21' => '21 Skikda - سكيكدة',
	    'DZ22' => '22 Sidi Bel Abbès - سيدي بلعباس',
	    'DZ23' => '23 Annaba - عنابة',
	    'DZ24' => '24 Guelma - قالمة',
	    'DZ25' => '25 Constantine - قسنطينة',
	    'DZ26' => '26 Médéa - المدية',
	    'DZ27' => '27 Mostaganem - مستغانم',
	    'DZ28' => '28 MSila - مسيلة',
	    'DZ29' => '29 Mascara - معسكر',
	    'DZ30' => '30 Ouargla - ورقلة',
	    'DZ31' => '31 Oran - وهران',
	    'DZ32' => '32 El Bayadh - البيض',
	    'DZ33' => '33 Illizi - إليزي ',
	    'DZ34' => '34 Bordj Bou Arreridj - برج بوعريريج',
	    'DZ35' => '35 Boumerdès - بومرداس',
	    'DZ36' => '36 El Tarf - الطارف',
	    'DZ37' => '37 Tindouf - تندوف',
	    'DZ38' => '38 Tissemsilt - تيسمسيلت',
	    'DZ39' => '39 Eloued - الوادي',
	    'DZ40' => '40 Khenchela - خنشلة',
	    'DZ41' => '41 Souk Ahras - سوق أهراس',
	    'DZ42' => '42 Tipaza - تيبازة',
	    'DZ43' => '43 Mila - ميلة',
	    'DZ44' => '44 Aïn Defla - عين الدفلى',
	    'DZ45' => '45 Naâma - النعامة',
	    'DZ46' => '46 Aïn Témouchent - عين تموشنت',
	    'DZ47' => '47 Ghardaïa - غرداية',
	    'DZ48' => '48 Relizane- غليزان',
	    'DZ49' => '49 Timimoun - تيميمون',
	    'DZ50' => '50 Bordj Baji Mokhtar - برج باجي مختار',
	    'DZ51' => '51 Ouled Djellal - أولاد جلال',
	    'DZ52' => '52 Béni Abbès -  بني عباس',
	    'DZ53' => '53 Aïn Salah - عين صالح',
	    'DZ54' => '54 In Guezzam - عين قزام',
	    'DZ55' => '55 Touggourt - تقرت',
	    'DZ56' => '56 Djanet - جانت',
	    'DZ57' => '57 El MGhair - المغير',
	    'DZ58' => '58 El Menia - المنيعة',
	  );

	endif;

  return $states;
}


add_filter('scpwoo_custom_states_dz', 'napoleon_add_states_of_algeria' );

function napoleon_add_states_of_algeria($states) {

  global $states;

  // ...or overwriting the name of an existing one
	$states['DZ']['DZ-01'] =  '01 Adrar - أدرار';
    $states['DZ']['DZ-02'] =  '02 Chlef - الشلف';
    $states['DZ']['DZ-03'] =  '03 Laghouat - الأغواط';
    $states['DZ']['DZ-04'] =  '04 Oum El Bouaghi - أم البواقي';
    $states['DZ']['DZ-05'] =  '05 Batna - باتنة'; 
    $states['DZ']['DZ-06'] =  '06 Béjaïa - بجاية';
    $states['DZ']['DZ-07'] =  '07 Biskra - بسكرة';
    $states['DZ']['DZ-08'] =  '08 Bechar - بشار';
    $states['DZ']['DZ-09'] =  '09 Blida - البليدة';
    $states['DZ']['DZ-10'] =  '10 Bouira - البويرة';
    $states['DZ']['DZ-11'] =  '11 Tamanrasset - تمنراست ';
    $states['DZ']['DZ-12'] =  '12 Tébessa - تبسة ';
    $states['DZ']['DZ-13'] =  '13 Tlemcene - تلمسان';
    $states['DZ']['DZ-14'] =  '14 Tiaret - تيارت';
    $states['DZ']['DZ-15'] =  '15 Tizi Ouzou - تيزي وزو';
    $states['DZ']['DZ-16'] =  '16 Alger - الجزائر';
    $states['DZ']['DZ-17'] =  '17 Djelfa - الجلفة';
    $states['DZ']['DZ-18'] =  '18 Jijel - جيجل';
    $states['DZ']['DZ-19'] =  '19 Sétif - سطيف';
    $states['DZ']['DZ-20'] =  '20 Saïda - سعيدة';
    $states['DZ']['DZ-21'] =  '21 Skikda - سكيكدة';
    $states['DZ']['DZ-22'] =  '22 Sidi Bel Abbès - سيدي بلعباس';
    $states['DZ']['DZ-23'] =  '23 Annaba - عنابة';
    $states['DZ']['DZ-24'] =  '24 Guelma - قالمة';
    $states['DZ']['DZ-25'] =  '25 Constantine - قسنطينة';
    $states['DZ']['DZ-26'] =  '26 Médéa - المدية';
    $states['DZ']['DZ-27'] =  '27 Mostaganem - مستغانم';
    $states['DZ']['DZ-28'] =  '28 MSila - مسيلة';
    $states['DZ']['DZ-29'] =  '29 Mascara - معسكر';
    $states['DZ']['DZ-30'] =  '30 Ouargla - ورقلة';
    $states['DZ']['DZ-31'] =  '31 Oran - وهران';
    $states['DZ']['DZ-32'] =  '32 El Bayadh - البيض';
    $states['DZ']['DZ-33'] =  '33 Illizi - إليزي ';
    $states['DZ']['DZ-34'] =  '34 Bordj Bou Arreridj - برج بوعريريج';
    $states['DZ']['DZ-35'] =  '35 Boumerdès - بومرداس';
    $states['DZ']['DZ-36'] =  '36 El Tarf - الطارف';
    $states['DZ']['DZ-37'] =  '37 Tindouf - تندوف';
    $states['DZ']['DZ-38'] =  '38 Tissemsilt - تيسمسيلت';
    $states['DZ']['DZ-39'] =  '39 Eloued - الوادي';
    $states['DZ']['DZ-40'] =  '40 Khenchela - خنشلة';
    $states['DZ']['DZ-41'] =  '41 Souk Ahras - سوق أهراس';
    $states['DZ']['DZ-42'] =  '42 Tipaza - تيبازة';
    $states['DZ']['DZ-43'] =  '43 Mila - ميلة';
    $states['DZ']['DZ-44'] =  '44 Aïn Defla - عين الدفلى';
    $states['DZ']['DZ-45'] =  '45 Naâma - النعامة';
    $states['DZ']['DZ-46'] =  '46 Aïn Témouchent - عين تموشنت';
    $states['DZ']['DZ-47'] =  '47 Ghardaïa - غرداية';
    $states['DZ']['DZ-48'] =  '48 Relizane- غليزان';
    $states['DZ']['DZ-49'] =  '49 Timimoun - تيميمون';
    $states['DZ']['DZ-50'] =  '50 Bordj Baji Mokhtar - برج باجي مختار';
    $states['DZ']['DZ-51'] =  '51 Ouled Djellal - أولاد جلال';
    $states['DZ']['DZ-52'] =  '52 Béni Abbès -  بني عباس';
    $states['DZ']['DZ-53'] =  '53 Aïn Salah - عين صالح';
    $states['DZ']['DZ-54'] =  '54 In Guezzam - عين قزام';
    $states['DZ']['DZ-55'] =  '55 Touggourt - تقرت';
    $states['DZ']['DZ-56'] =  '56 Djanet - جانت';
    $states['DZ']['DZ-57'] =  '57 El MGhair - المغير';
    $states['DZ']['DZ-58'] =  '58 El Menia - المنيعة';
  	
  	return $states['DZ'];
}
add_filter('scpwoo_custom_places_dz', 'napoleon_add_cities_of_algeria' );

function napoleon_add_cities_of_algeria($places) {

  global $places;

  
  
  // ...or overwriting the name of an existing one
    $places['DZ']['DZ-01']= array('Adrar', 'Tamest', 'Reggane', 'Inozghmir', 'Tit', 'Tsabit', 'Zaouiet Kounta', 'Aoulef', 'Timokten', 'Tamentit', 'Fenoughil', 'Sali', 'Akabli', 'Ouled Ahmed Timmi', 'Bouda', 'Sbaa'); 

    $places['DZ']['DZ-07']=  array( 'Biskra', 'Oumache', 'Branis', 'Chetma', 'Ras El Miaad', 'Sidi Okba', 'Mchouneche', 'El Haouch', 'Ain Naga', 'Zeribet El Oued', 'El Feidh', 'El Kantara', 'Ain Zaatout', 'El Outaya', 'Djemorah', 'Tolga', 'Lioua', 'Lichana', 'Ourlal', 'Mlili', 'Foughala', 'Bordj Ben Azzouz', 'Meziraa', 'Bouchagroun', 'Mekhadma', 'El Ghrous', 'El Hadjab', 'Khanguet Sidinadji' );

    $places['DZ']['DZ-08']= array( 'Bechar', 'Erg Ferradj', 'Meridja', 'Lahmar', 'Mechraa Houari B', 'Kenedsa', 'Taghit', 'Boukais', 'Mogheul', 'Abadla', 'Beni Ounif', );
    $places['DZ']['DZ-11'] = array( 'Tamanghasset', 'Abalessa', 'Idles', 'Tazouk', 'In Amguel', );

    $places['DZ']['DZ-30'] = array( 'Ouargla', 'Ain Beida', 'Ngoussa', 'Hassi Messaoud', 'Rouissat', 'Sidi Khouiled', 'Hassi Ben Abdellah', 'El Borma', );

  $places['DZ']['DZ-33']=  array( 'Illizi', 'Debdeb', 'Bordj Omar Driss', 'In Amenas', );
  $places['DZ']['DZ-39']= array( 'El Oued', 'Robbah', 'Oued El Alenda', 'Bayadha', 'Nakhla', 'Guemar', 'Kouinine', 'Reguiba', 'Hamraia', 'Taghzout', 'Debila', 'Hassani Abdelkrim', 'Hassi Khelifa', 'Taleb Larbi', 'Douar El Ma', 'Sidi Aoun', 'Trifaoui', 'Magrane', 'Beni Guecha', 'Ourmas', 'El Ogla', 'Mih Ouansa', );

    $places['DZ']['DZ-47'] = array( 'Ghardaia', 'Dhayet Bendhahoua', 'Berriane', 'Metlili', 'El Guerrara', 'El Atteuf', 'Zelfana', 'Sebseb', 'Bounoura', 'Mansoura', );

    $places['DZ']['DZ-49']= array('Timimoun', 'Charouine', 'Ksar Kaddour', 'Ouled Said', 'Tinerkouk', 'Deldoul', 'Metarfa', 'Aougrout', 'Talmine', 'Ouled Aissa' ); 
    $places['DZ']['DZ-50']= array('B Badji Mokhtar',  'Timiaouine'); 
    $places['DZ']['DZ-51']= array('Ouled Djellal', 'Sidi Khaled', 'Ras El Miad ','Besbes', 'Chaiba', 'Doucen' ); 
    $places['DZ']['DZ-52']= array('Beni Abbes', 'Tamtert', 'Kerzaz', 'Timoudi', 'Beni Ikhlef', 'El Ouata', 'Tabelbala', 'Ouled Khoudir', 'Ksabi', 'Igli'); 

    $places['DZ']['DZ-53']= array('In Salah', 'In Ghar', 'Foggaret Azzaouia'); 
    $places['DZ']['DZ-54']= array('In Guezzam', 'Tinzaouatine'); 

    $places['DZ']['DZ-55']= array('Touggourt', 'Nezla', 'Tebesbest', 'Zaouia El Abidia', 'Temacine', 'Blidet Amor', 'Megarine',  'Mnaguer' ,'Taibet', 'Benaceur', 'Sidi Slimane', 'El-hadjira', 'El Alia'); 
    $places['DZ']['DZ-56']= array('Djanet', 'Bordj El Haouasse'); 

    $places['DZ']['DZ-57']= array('El-mghair', 'Oum Touyour', 'Still', 'Sidi Khelil', 'Djamaa', 'Sidi Amrane', 'Tenedla', 'Mrara' ); 

    $places['DZ']['DZ-58']= array('El Meniaa', 'Hassi Gara', 'Hassi Fehal' ); 


    


  return $places['DZ'];
}


/**
 * Editing checkout form to suit cash on delivery business 
 */

add_filter( 'woocommerce_checkout_fields' , 'napoleon_remove_checkout_fields', 50 ); 

function napoleon_remove_checkout_fields( $fields ) { 

	unset($fields['billing']['billing_last_name']); 

    $fields['billing']['billing_first_name'] = array(
        'label'   => __('Full name', 'napoleon'),
    	'placeholder'=>  '',	
    	'required' => true,
  		'class'    => array('form-row-wide'),
  		'clear'    => true
     );

	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_email']);
	unset($fields['billing']['billing_postcode']);
	unset( $fields['order']['order_comments'] );


    $fields['billing']['billing_phone']['priority'] = 15;


return $fields; 

}

/**
 * Hide shipping information
 */

add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );


/**
 * Change priority and "required" attribution in checkout form
 */
add_filter( 'woocommerce_default_address_fields' , 'napoleon_filter_default_address_fields', 20, 1 );
function napoleon_filter_default_address_fields( $address_fields ) {
    // Only on checkout page
    if( ! is_checkout() ) return $address_fields;
    $address_fields['city']['priority'] = 110;

    $address_fields['address_1']['priority'] = 111;

    // All field keys in this array
    $key_fields = array('address_1','address_2','city');

    // Loop through each address fields (billing and shipping)
    foreach( $key_fields as $key_field )
        $address_fields[$key_field]['required'] = false;



    return $address_fields;
}

/**
 * Add Quantity Input Beside Product Name in checkout page
 */
add_filter( 'woocommerce_checkout_cart_item_quantity', 'napoleon_checkout_item_quantity_input', 9999, 3 );
  
function napoleon_checkout_item_quantity_input( $product_quantity, $cart_item, $cart_item_key ) {
   $product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
   $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
   if ( ! $product->is_sold_individually() ) {
   	  $product_quantity = '<br><span>'. __('QTY: ', 'napoleon') . '</span>';
      $product_quantity .= woocommerce_quantity_input( array(
         'input_name'  => 'shipping_method_qty_' . $product_id,
         'input_value' => $cart_item['quantity'],
         'max_value'   => $product->get_max_purchase_quantity(),
         'min_value'   => '0',

      ), $product, false );
      $product_quantity .= '<input type="hidden" name="product_key_' . $product_id . '" value="' . $cart_item_key . '">';


   }
   return $product_quantity;
}
 

 /**
 * Detect Quantity Change and Recalculate Totals
 */
add_action( 'woocommerce_checkout_update_order_review', 'napoleon_update_item_quantity_checkout' );
 
function napoleon_update_item_quantity_checkout( $post_data ) {
   parse_str( $post_data, $post_data_array );
   $updated_qty = false;
   foreach ( $post_data_array as $key => $value ) {   
      if ( substr( $key, 0, 20 ) === 'shipping_method_qty_' ) {         
         $id = substr( $key, 20 );   
         WC()->cart->set_quantity( $post_data_array['product_key_' . $id], $post_data_array[$key], false );
         $updated_qty = true;
      }      
   }   
   if ( $updated_qty ) WC()->cart->calculate_totals();
}


 /**
 * Add remove button to checkout page
 */

add_filter( 'woocommerce_cart_item_name', 'napoleon_woocommerce_checkout_remove_item', 10, 3 );

function napoleon_woocommerce_checkout_remove_item( $product_name, $cart_item, $cart_item_key ) {
if ( is_checkout() ) {
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

    $remove_link = apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">×</a>',
         esc_url(wc_get_cart_remove_url( $cart_item_key ) ),
        __( 'Remove this item', 'woocommerce' ),
        esc_attr( $product_id ),
        esc_attr( $_product->get_sku() )
    ), $cart_item_key );

    return '<span>' . $remove_link . '</span> <span>' . $product_name . '</span>';
}

return $product_name;
}



// Display saving "%" after price in single product page

add_action( 'woocommerce_single_product_summary', 'display_product_savings', 15 );
function display_product_savings() {
    global $product;
    if ( $product->is_type('simple') && $product->get_sale_price() ) {
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $discount_percent = round( ( ($regular_price - $sale_price) / $regular_price ) * 100 );
        echo '<p class="price-savings">'. $discount_percent . '%</p>';
    }
    elseif  ( $product->is_type('variable') ) {
   $variations = $product->get_available_variations();

    // Sort the variations by price, from lowest to highest
    usort($variations, function($a, $b) {
        return $a['display_price'] - $b['display_price'];
    });

    // Check if the lowest price variation has a sale price set
	    if (!empty($variations[0]['display_price']) && ( $variations[0]['display_price'] !== $variations[0]['display_regular_price'])) {
			
	    	//Regular price
			$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
			$price = $prices[0];
			 
			// Sale Price
			$prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
			sort( $prices );
			$saleprice = $prices[0];

			$discount_percent = round( ( ($saleprice - $price) / $saleprice ) * 100 );
	        echo '<p class="price-savings">'. $discount_percent . '%</p>';    
	    }
    	
	}

}


 /**
 * Display product title above images in mobile 
 * 
 **/

if (  (get_theme_mod( 'product_title_before_image', 0 ) === 1 ) && wp_is_mobile() ) {

	    
	   	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	   	add_action( 'woocommerce_before_single_product', 'woocommerce_template_single_price', 10 );


	   	remove_action( 'woocommerce_single_product_summary', 'display_product_savings', 15 );
	   	add_action( 'woocommerce_before_single_product', 'display_product_savings', 15 );


	   	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	   	add_action( 'woocommerce_before_single_product', 'woocommerce_template_single_rating', 20);


	   	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	   	add_action( 'woocommerce_before_single_product', 'woocommerce_template_single_excerpt', 25);



}



// Remove the product description Title
add_filter( 'woocommerce_product_description_heading', '__return_null' );



// Add image upload field to review form
add_action( 'comment_form_logged_in_after', 'woocommerce_review_image_field' );
add_action( 'comment_form_after_fields', 'woocommerce_review_image_field' );
function woocommerce_review_image_field() {
	if ( is_product() ) {
    	echo '<p class="comment-form-review-image">
              <label for="review_image">' . esc_html__( 'Upload an image:', 'napoleon' ) . '</label>
              <input type="file" name="review_image" id="review_image" accept="image/*">
          </p>';
    }
}

// Save uploaded image as comment meta data
add_action( 'comment_post', 'woocommerce_save_review_image' );
function woocommerce_save_review_image( $comment_id ) {
    if ( ! empty( $_FILES['review_image']['name'] ) && ! empty( $_FILES['review_image']['tmp_name'] ) ) {

        $comment = get_comment( $comment_id );
        $upload = wp_upload_bits( $_FILES['review_image']['name'], null, file_get_contents( $_FILES['review_image']['tmp_name'] ) );
        if ( isset( $upload['error'] ) && $upload['error'] != 0 ) {
            wp_die( 'There was an error uploading your file. The error is: ' . $upload['error'] );
        } else {
            add_comment_meta( $comment_id, 'review_image', $upload );
        }
    }
}

// Display review image before review meta
add_action( 'woocommerce_review_before_comment_meta', 'woocommerce_display_review_image', 5 );

function woocommerce_display_review_image( ) {
    $comment_id = get_comment_ID();
    $comment_image = get_comment_meta( $comment_id, 'review_image', true );
    if ( is_array( $comment_image ) && isset( $comment_image['url'] ) ) {
       echo '<img src="' . $comment_image['url'] . '" style="max-width:100%;height:auto;" alt="'. __('review','napoleon') .'">';
    }
}





// Move reviw stars position below "review meta"
remove_action( 'woocommerce_review_before_comment_meta',
               'woocommerce_review_display_rating', 10 );
add_action( 'woocommerce_review_meta',
               'woocommerce_review_display_rating', 10 );


// Display regular price before sale price
add_filter( 'woocommerce_format_sale_price', 'invert_formatted_sale_price', 10, 3 );
function invert_formatted_sale_price( $price, $regular_price, $sale_price ) {
    return '<ins>' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins> <del>' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del>';
}



// Show only lowest prices in WooCommerce variable products

add_filter( 'woocommerce_variable_sale_price_html', 'napoleon_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'napoleon_variation_price_format', 10, 2 );
 
function napoleon_variation_price_format( $price, $product ) {
 
	// Main Price
	$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
	$price = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
	 
	// Sale Price
	$regular_prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
	sort( $regular_prices );
	$regular_price = $regular_prices[0] !== $regular_prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $regular_prices[0] ) ) : wc_price( $regular_prices[0] );
	 
	if ( $price !== $regular_price ) {
		$price = '<ins>' . $price . $product->get_price_suffix() . '</ins> <del>' . $regular_price . $product->get_price_suffix() . '</del>';
	}

	return $price;
}



if ( 1 === get_theme_mod( 'product_image_zoom', 1 ) ) {

	// Disable zoom in product image
	function remove_image_zoom_support_webtalkhub() {
	    remove_theme_support( 'wc-product-gallery-zoom' );
	}
	add_action( 'wp', 'remove_image_zoom_support_webtalkhub', 100 );
}





/**
 * Disable Autofill / Pasting infos in Checkout Form 
 */

function disable_browser_features() {
    if ( (is_product() || is_page() )  && get_theme_mod('disable_infos_autofill')  == 1 ) {
        echo '<script type="text/javascript">
            jQuery(document).ready(function($) {
                $("#codplugin-checkout input").attr("autocomplete", "new-password");
            });

            jQuery(document).ready(function($) {
                $("#codplugin-checkout input").on("paste", function(e) {
                    e.preventDefault();
                    alert("Enter your information manually!");
                });
            });

        </script>';
    }

    if ( is_checkout()  && get_theme_mod('disable_infos_autofill')  == 1   ) {
        echo '<script type="text/javascript">
            jQuery(document).ready(function($) {
                $("input").attr("autocomplete", "new-password");
            });

            jQuery(document).ready(function($) {
                $("input").on("paste", function(e) {
                    e.preventDefault();
                    alert("Enter your information manually!");
                });
            });

        </script>';
    }
    if (  get_theme_mod('disable_text_copying')  == 1   ) { ?>
        <style>
	        body {
	            -webkit-touch-callout: none; /* Disable iOS touch-callout */
	            -webkit-user-select: none; /* Disable text selection */
	            -khtml-user-select: none;
	            -moz-user-select: none;
	            -ms-user-select: none;
	            user-select: none;
	        }
	    </style>
        <script>
	        // Disable text selection (excluding input fields and textareas)
	        document.addEventListener('selectstart', function (event) {
	            if (event.target.nodeName !== 'INPUT' && event.target.nodeName !== 'TEXTAREA') {
	                event.preventDefault();
	            }
	        });				
    	</script>


    <?php }

}
add_action( 'wp_footer', 'disable_browser_features' );



/**
 * Disable Checkout button if visitor made an order within 24 hours by IP
 */
if (get_theme_mod('block_ip_reordering')  == 1 ) : 
    // Hook into the WooCommerce checkout process
   add_action( 'woocommerce_after_checkout_validation', 'woo_ip_order_limit_check', 10, 2 );

    function woo_ip_order_limit_check( $data, $errors ) {
        // Get the customer's IP address
        $ip_address = WC_Geolocation::get_ip_address();

        // Check if the IP address has placed an order within the last 24 hours
        if ( the_ip_address_has_order( $ip_address ) ) {
            $errors->add( 'order_error', __( 'Sorry, you can only place one order per 24 hours.', 'napoleon' ) );

        }
    }

	function the_ip_address_has_order( $ip_address ) {
	    // Query WooCommerce orders to check if the IP address has placed an order within the last 24 hours

	        // Get the current time and calculate the time 24 hours ago
		    $current_time = current_time('timestamp');
		    $twenty_four_hours_ago = $current_time - (24 * 60 * 60);


	    $args = array(
	        'post_type'      => 'shop_order',
			'post_status'    => array( 'wc-processing', 'wc-completed' ), 
			'date_query'     => array(
	            array(
	                'column' => 'post_date_gmt',
	                'after'  => date('Y-m-d H:i:s', $twenty_four_hours_ago),
	            ),
		     ),
	        'posts_per_page' => 1,
	        'meta_query'     => array(
	            array(
	                'key'     => '_customer_ip_address',
	                'value'   => $ip_address,
	                'compare' => '=',
	            ),
	        ),
	    );

	    $orders = get_posts( $args );
	    return ! empty( $orders );
	}
	
endif;




/**
 * Disable Checkout button if visitor made an order within 24 hours by cookies
 */
if (get_theme_mod('block_cookies_reordering')  == 1 ) : 

	// Hook into the WooCommerce thank you page to set the cookie for successful orders
	add_action( 'woocommerce_thankyou', 'napoleon_set_last_order_cookie_on_thankyou', 10, 1 );

	function napoleon_set_last_order_cookie_on_thankyou( $order_id ) {
	    $order = wc_get_order( $order_id );
	    // Set a cookie to store the current timestamp when an order is created, but only if it's not pending payment
	    if ( $order && $order->get_status() !== 'pending' ) {
	        setcookie( 'last_order_timestamp', time(), time() + (24 * 60 * 60), '/' );  // Cookie valid for 24 hours
	    }
	}

	// Hook into the WooCommerce checkout process
	add_action( 'woocommerce_after_checkout_validation', 'woo_cookies_order_limit_check', 10, 2 );

	function woo_cookies_order_limit_check( $data, $errors ) {
	    // Check if the user has placed an order within the last 24 hours
	    if ( the_cookies_has_order() ) {
	        $errors->add( 'order_error', __( 'Sorry, you can only place one order per 24 hours.', 'napoleon' ) );
	    }
	}

	function the_cookies_has_order() {
	    // Check if the user has a cookie indicating a previous order within the last 24 hours
	    if ( isset( $_COOKIE['last_order_timestamp'] ) ) {
	        $last_order_timestamp = intval( $_COOKIE['last_order_timestamp'] );

	        // Check if the last order was within the last 24 hours
	        $twenty_four_hours_ago = time() - (24 * 60 * 60);
	        return $last_order_timestamp > $twenty_four_hours_ago;
	    }

	    return false;
	}

endif;



/**
 * Add subheadline to woocommerce products
 */
	// Step 1: Add Custom Meta Box
add_action('add_meta_boxes', 'napoleon_product_subheadline_meta_box');

function napoleon_product_subheadline_meta_box() {
    add_meta_box(
        'product_subheadline_meta_box',
        __('Product Subheadline', 'napoleon'),
        'napoleon_subheadline_meta_box_callback',
        'product',
        'aftertitle',
        'high'
    );
}

function subheadline_meta_move() {
        # Get the globals:
        global $post, $wp_meta_boxes;

        # Output the "advanced" meta boxes:
        do_meta_boxes( get_current_screen(), 'aftertitle', $post );

        # Remove the initial "advanced" meta boxes:
        unset($wp_meta_boxes['post']['aftertitle']);
    }

add_action('edit_form_after_title', 'subheadline_meta_move');

function napoleon_subheadline_meta_box_callback($post) {
    $subheadline = get_post_meta($post->ID, 'product_subheadline', true);
    ?>
    <p>
        <label for="product_subheadline"><?php _e('Subheadline:', 'napoleon'); ?></label>
        <input type="text" id="product_subheadline" name="product_subheadline" value="<?php echo esc_attr($subheadline); ?>" />
    </p>
    <?php
}

	// Step 2: Save Custom Field Data
add_action('save_post', 'save_napoleon_subheadline_meta_box');

function save_napoleon_subheadline_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['product_subheadline'])) {
        update_post_meta($post_id, 'product_subheadline', sanitize_text_field($_POST['product_subheadline']));
    }
}

	// Step 3: Display Subheadline
if (  (get_theme_mod( 'product_title_before_image', 0 ) === 1 ) && wp_is_mobile() ) {
		remove_action('woocommerce_single_product_summary','woocommerce_template_single_title',5);
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_napoleon_title', 5 );
		add_action('woocommerce_before_single_product', 'woocommerce_napoleon_title',5);
	} else {
		remove_action('woocommerce_single_product_summary','woocommerce_template_single_title',5);
		add_action('woocommerce_single_product_summary', 'woocommerce_napoleon_title',5);
	}
		

if ( ! function_exists( 'woocommerce_napoleon_title' ) ) {
   function woocommerce_napoleon_title() {
	   
	global $product;
    $subheadline = get_post_meta($product->get_id(), 'product_subheadline', true);

    if (!empty($subheadline)) : ?>
        <h1 itemprop="name" class="product_title entry-title product_subheadline"><?php the_title(); ?><span><?php echo $subheadline ;  ?></span></h1>
	<?php  else : ?>
		 <h1 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h1>
	<?php endif;
    }  

}


/**
 * Speed up woocommerce stores
 */
add_filter('wp_lazy_loading_enabled', '__return_true');
add_filter( 'woocommerce_defer_transactional_emails','__return_true' );

add_filter( 'wc_price_args', 'napoleon_custom_woocommerce_price_format' );
function napoleon_custom_woocommerce_price_format( $args ) {
    $args['decimal_separator'] = '';
    $args['thousand_separator'] = '';
    $args['decimals'] = 0;
    return $args;
}
