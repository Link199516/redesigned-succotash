<?php
/**
 * napoleon onboarding related code.
 */
add_filter( 'ocdi/plugin_page_setup', 'napoleon_ocdi_plugin_page_setup' );
function napoleon_ocdi_plugin_page_setup( $default_settings ) {
  $default_settings['parent_slug'] = 'themes.php';
  $default_settings['page_title']  = esc_html__( 'One Click Demo Import' , 'napoleon' );
  $default_settings['menu_title']  = esc_html__( 'Import Demo Data' , 'napoleon' );
  $default_settings['capability']  = 'import';

  return $default_settings;
}


add_filter( 'ocdi/timeout_for_downloading_import_file', 'napoleon_ocdi_download_timeout' );
function napoleon_ocdi_download_timeout( $timeout ) {
	return 60;
}


add_filter( 'ocdi/register_plugins', 'napoleon_ocdi_register_plugins' );
function napoleon_ocdi_register_plugins( $plugins ) {
  $theme_plugins = [

    [ // Woocommerce Plugin
      'name'     => __( 'Woocommerce', 'napoleon' ),
      'slug'     => 'woocommerce', 
      'description'=> __( 'Sell anything, beautifully.', 'napoleon'),
      'required' => true,  
                  
    ],


    [ // Elementor Plugin
      'name'     => __( 'Elementor', 'napoleon' ),
      'slug'     => 'elementor',        
   	  'description'=>__('Elementor is a front-end drag & drop page builder for WordPress.','napoleon'),     
	  'required' => false,

    ],
	
    [ // Elementor Pro Plugin
      'name'     => __( 'Elementor Pro', 'napoleon' ),
      'slug'     => 'elementor-pro',
      'description'        => __( 'Add a fast order form in product pages', 'napoleon' ),
      'required' => false,
    ],

	[ // JetWooBuilder Plugin
		'name'     => __( 'JetWooBuilder', 'napoleon' ),
		'slug'     => 'jet-woo-builder',
		'description'=>__('Set of modules for WooCommerce based on Elementor Page Builder.','napoleon'),
		'required' => false,
	 
	  ],

    [ // Woo Friendly User Agent Plugin
      'name'     => __( 'Woo Friendly User Agent', 'napoleon' ),
      'slug'     => 'woo-friendly-user-agent',
      'description'=> __( 'Enhances WooCommerce with user agent based features.', 'napoleon' ),
      'required' => true,
    ],

    [ // Variation Swatches for WooCommerce Plugin
      'name'     => __( 'Variation Swatches for WooCommerce', 'napoleon' ),
      'slug'     => 'variation-swatches-woo',
      'description'=> __( 'Adds color and image swatches to WooCommerce product variations.', 'napoleon'), // Added a description
      'required' => true,
    ],

    [ // Maxslider Plugin
      'name'     => __( 'MaxSlider', 'napoleon' ),
      'slug'     => 'maxslider',
	  'description'=> __('Add a custom responsive slider to any page of your website.', 'napoleon'),
      'required' => false, // Ensure it's optional
    ],

    [ // One Click Demo Import Plugin - Moved to end
      'name'     => __( 'One Click Demo Import', 'napoleon' ),
      'slug'     => 'one-click-demo-import',
      'description'=> __( 'Import your demo content, widgets and theme settings with one click.', 'napoleon' ),
      'required' => true, // Keep required as per original logic for OCDI
    ],

  ];
 
  return array_merge( $plugins, $theme_plugins );
}


add_filter( 'ocdi/import_files', 'napoleon_ocdi_import_files' );
function napoleon_ocdi_import_files( $files ) {

	$demo_dir_url = untrailingslashit( apply_filters( 'napoleon_ocdi_demo_dir_url', 'https://bitherhood.com/napoleon-demo/napoleon' ) );


	// When having more that one predefined imports, set a preview image, preview URL, and categories for isotope-style filtering.
	return array(
		array(
	      'import_file_name'           => esc_html__( 'Napoleon', 'napoleon' ),
	      'import_file_url'            => $demo_dir_url . '/napoleon-content.xml',
	      'import_widget_file_url'     => $demo_dir_url . '/napoleon-widgets.wie',
	      'import_customizer_file_url' => $demo_dir_url . '/napoleon-customizer.dat',
	      'import_preview_image_url'   => $demo_dir_url . '/napoleon-screenshot.png',
	    ),	
	);
}

add_action( 'ocdi/before_content_import', 'napoleon_ocdi_before_import_setup' );
function napoleon_ocdi_before_import_setup() {
    // Delete original WooCommerce pages before import to prevent -2 suffix.
    $pages_to_delete = array( 'shop', 'cart', 'checkout', 'my-account' );

    foreach ( $pages_to_delete as $slug ) {
        $page = get_page_by_path( $slug );
        if ( $page ) {
            wp_delete_post( $page->ID, true ); // true for force delete
            error_log( "Deleted original page before import: {$slug}" );
        }
    }
}

add_action( 'ocdi/after_import', 'napoleon_ocdi_after_import_setup' );
function napoleon_ocdi_after_import_setup() {

    // Set up nav menus.
    $main_menu  = get_term_by( 'name', 'Main Top Menu', 'nav_menu' );
    if ( $main_menu ) {
        set_theme_mod( 'nav_menu_locations', array(
            'menu-1' => $main_menu->term_id,
        ));
    }

    // Set up home and blog pages.
    $front_page_id = get_page_by_path( 'home' );
    if ( $front_page_id ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $front_page_id->ID );
    }

    // WooCommerce setup.
    if ( class_exists( 'WooCommerce' ) ) {

        // Set default currency to Algerian Dinar (DZD).
        update_option( 'woocommerce_currency', 'DZD' ); // 'DZD' is the currency code for Algerian Dinar

        // Set default customer location to Algeria (DZ) and region (DZ-16).
        update_option( 'woocommerce_default_country', 'DZ:DZ-16' ); // Set default country and region

        // Set WooCommerce pages to the newly imported ones (without -2 suffix).
        $wc_shop_page_id      = get_page_by_path( 'shop' );
        $wc_cart_page_id      = get_page_by_path( 'cart' );
        $wc_checkout_page_id  = get_page_by_path( 'checkout' );
        $wc_myaccount_page_id = get_page_by_path( 'my-account' );

        if ( $wc_shop_page_id ) {
            update_option( 'woocommerce_shop_page_id', $wc_shop_page_id->ID );
        }
        if ( $wc_cart_page_id ) {
            update_option( 'woocommerce_cart_page_id', $wc_cart_page_id->ID );
        }
        if ( $wc_checkout_page_id ) {
            update_option( 'woocommerce_checkout_page_id', $wc_checkout_page_id->ID );
        }
        if ( $wc_myaccount_page_id ) {
            update_option( 'woocommerce_myaccount_page_id', $wc_myaccount_page_id->ID );
        }

    }
	
    // Set WooCommerce currency position to left space.
    update_option( 'woocommerce_currency_pos', 'left_space' );

    // Set WordPress language to Arabic.
    update_option( 'language', 'ar' );
    switch_to_locale( 'ar' );

    // Disable WooCommerce "Coming Soon" mode.
    update_option( 'woocommerce_coming_soon', 'no' );
	
    // Enable and configure Cash on Delivery payment method.
    $cod_settings = array(
        'enabled' => 'yes',
        'title' => 'الدفع عند الاستلام', // Cash on Delivery
        'description' => '',
        'instructions' => '',
        'enable_for_methods' => array(),
        'accept_virtual' => 'no',
    );
    update_option( 'woocommerce_cod_settings', $cod_settings );
}

function napoleon_get_theme_recommended_plugins() {
	return apply_filters( 'napoleon_theme_recommended_plugins', array(

		'woocommerce'           => array(
			'title'              => __( 'WooCommerce', 'napoleon' ),
			'description'        => __( 'Sell anything, beautifully.', 'napoleon' ),
			'required_by_sample' => true,
		),
		'elementor'             => array(
			'title'              => __( 'Elementor', 'napoleon' ),
			'description'        => __( 'Elementor is a front-end drag & drop page builder for WordPress.', 'napoleon' ),
			'required_by_sample' => true,
		),
		'elementor-pro'             => array(
			'title'              => __( 'Elementor Pro', 'napoleon' ),
			'description'        => __( 'Elevate your designs and unlock the full power of Elementor Pro.', 'napoleon' ),
			'bundled'   => true,
			'required_by_sample' => true,
		),		
		'jet-woo-builder'             => array(
			'title'              => __( 'JetWooBuilder', 'napoleon' ),
			'description'        => __( 'Set of modules for WooCommerce based on Elementor Page Builder.', 'napoleon' ),
			'bundled'   => true,
			'required_by_sample' => true,
		),
		'woo-friendly-user-agent' => array(
			'title'              => __( 'Woo Friendly User Agent', 'napoleon' ),
			'description'        => __( 'Enhances WooCommerce with user agent based features.', 'napoleon' ),
			'bundled'   => true,
			'required_by_sample' => true,
		),
		'variation-swatches-woo' => array(
			'title'              => __( 'Variation Swatches for WooCommerce', 'napoleon' ),
			'description'        => __( 'Adds color and image swatches to WooCommerce product variations.', 'napoleon' ), // Added a description
			'required_by_sample' => true,
		),
		'maxslider'             => array(
			'title'              => __( 'MaxSlider', 'napoleon' ),
			'description'        => __( 'Add a custom responsive slider to any page of your website.', 'napoleon' ),
			'required_by_sample' => false, // Ensure it's optional
		),
		'one-click-demo-import' => array( // Moved to end
			'title'              => __( 'One Click Demo Import', 'napoleon' ),
			'description'        => __( 'Import your demo content, widgets and theme settings with one click.', 'napoleon' ),
			'required_by_sample' => true, // Keep required as per original logic for OCDI
		)
	) );
}

add_action( 'init', 'napoleon_onboarding_page_init' );

function napoleon_onboarding_page_init() {

	$data = array(
		'show_page'                => true,
'description'              => __( 'A fast, lightweight WooCommerce theme built for cash-on-delivery stores. Seamlessly integrates with local shipping and payment systems. Perfect for sellers who want a simple, ready-to-use store with RTL support.', 'napoleon' ),
		'recommended_plugins_page' => array(
			'plugins' => napoleon_get_theme_recommended_plugins(),
		),
	);

	$onboarding = new napoleon_Onboarding_Page();
	$onboarding->init( apply_filters( 'napoleon_onboarding_page_array', $data ) );
}


/**
 * User onboarding.
 */
require_once get_theme_file_path( '/inc/onboarding/onboarding-page.php' );
