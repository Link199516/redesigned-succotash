<?php
/**
 * Standard Customizer Sections and Settings
 */
add_action( 'customize_register', 'napoleon_customize_register' );
function napoleon_customize_register( $wp_customize ) {

	// Partial for various settings that affect the customizer styles, but can't have a dedicated icon, e.g. 'limit_logo_size'
	$wp_customize->selective_refresh->add_partial(
		'theme_style',
		array(
			'selector'            => '#napoleon-style-inline-css',
			'render_callback'     => 'napoleon_get_all_customizer_css',
			'settings'            => array(),
			'container_inclusive' => false,
		)
	);

	//
	// Header
	//
	if ( apply_filters( 'napoleon_customizable_header', true ) ) {
		$wp_customize->add_panel(
			'theme_header',
			array(
				'title'    => esc_html_x( 'Header', 'customizer section title', 'napoleon' ),
				'priority' => 10, // Before site_identity, 20
			)
		);

		$wp_customize->add_section(
			'theme_header_style',
			array(
				'title'    => esc_html_x( 'Header style', 'customizer section title', 'napoleon' ),
				'panel'    => 'theme_header',
				'priority' => 10,
			)
		);
		require_once get_theme_file_path( 'inc/customizer/options/theme-header-style.php' );

		$wp_customize->add_section(
			'theme_header_primary_menu',
			array(
				'title'    => esc_html_x( 'Primary menu bar', 'customizer section title', 'napoleon' ),
				'panel'    => 'theme_header',
				'priority' => 30,
			)
		);
		require_once get_theme_file_path( 'inc/customizer/options/theme-header-primary-menu.php' );
	} // filter napoleon_customizable_header

	//
	// Blog
	//
	$wp_customize->add_panel(
		'theme_blog',
		array(
			'title'    => esc_html_x( 'Blog settings', 'customizer section title', 'napoleon' ),
			'priority' => 30, // After site_identity, 20
		)
	);

	$wp_customize->add_section(
		'theme_archive_options',
		array(
			'title'       => esc_html_x( 'Archive options', 'customizer section title', 'napoleon' ),
			'panel'       => 'theme_blog',
			'description' => esc_html__( 'Customize the default archive pages, such as the blog, category, tag, date archives, etc.', 'napoleon' ),
			'priority'    => 10,
		)
	);
	require_once get_theme_file_path( 'inc/customizer/options/theme-archive-options.php' );

	$wp_customize->add_section(
		'theme_post_options',
		array(
			'title'    => esc_html_x( 'Post options', 'customizer section title', 'napoleon' ),
			'panel'    => 'theme_blog',
			'priority' => 20,
		)
	);
	require_once get_theme_file_path( 'inc/customizer/options/theme-post-options.php' );

	//
	// Colors
	//
	$wp_customize->add_panel(
		'theme_colors',
		array(
			'title'    => esc_html_x( 'Colors & Fonts', 'customizer section title', 'napoleon' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_section(
		'theme_colors_fonts',
		array(
			'title'    => esc_html_x( 'Fonts', 'customizer section title', 'napoleon' ),
			'panel'    => 'theme_colors',
		)
	);
	require_once get_theme_file_path( 'inc/customizer/options/theme-colors-fonts.php' );


	if ( apply_filters( 'napoleon_customizable_header', true ) ) {
		$wp_customize->add_section(
			'theme_colors_primary_menu_bar',
			array(
				'title'    => esc_html_x( 'Primary menu bar', 'customizer section title', 'napoleon' ),
				'panel'    => 'theme_colors',
				'priority' => 20,
			)
		);
		require_once get_theme_file_path( 'inc/customizer/options/theme-colors-primary-menu-bar.php' );
	} // filter napoleon_customizable_header

	if ( get_theme_support( 'napoleon-hero' ) ) {
		$wp_customize->add_section(
			'theme_colors_hero',
			array(
				'title'    => esc_html_x( 'Hero', 'customizer section title', 'napoleon' ),
				'panel'    => 'theme_colors',
				'priority' => 30,
			)
		);
		require_once get_theme_file_path( 'inc/customizer/options/theme-colors-hero.php' );
	}

	$wp_customize->add_section(
		'theme_colors_global',
		array(
			'title'    => esc_html_x( 'Global', 'customizer section title', 'napoleon' ),
			'panel'    => 'theme_colors',
			'priority' => 40,
		)
	);
	require_once get_theme_file_path( 'inc/customizer/options/theme-colors-global.php' );

	$wp_customize->add_section(
		'theme_colors_sidebar',
		array(
			'title'    => esc_html_x( 'Sidebar', 'customizer section title', 'napoleon' ),
			'panel'    => 'theme_colors',
			'priority' => 50,
		)
	);
	require_once get_theme_file_path( 'inc/customizer/options/theme-colors-sidebar.php' );

	if ( apply_filters( 'napoleon_customizable_footer', true ) ) {
		$wp_customize->add_section(
			'theme_colors_footer',
			array(
				'title'    => esc_html_x( 'Footer', 'customizer section title', 'napoleon' ),
				'panel'    => 'theme_colors',
				'priority' => 60,
			)
		);
		require_once get_theme_file_path( 'inc/customizer/options/theme-colors-footer.php' );
	} // filter napoleon_customizable_footer


	$wp_customize->add_section(
		'theme_colors_contact',
		array(
			'title'    => esc_html_x( 'Contact Button', 'customizer section title', 'napoleon' ),
			'panel'    => 'theme_colors',
			'priority' => 70,
		)
	);
	require_once get_theme_file_path( 'inc/customizer/options/theme-colors-contact.php' );


	$wp_customize->add_section(
		'theme-colors-photo',
		array(
			'title'    => esc_html_x( 'Photo options', 'customizer section title', 'napoleon' ),
			'panel'    => 'theme_colors',
			'priority' => 80,
		)
	);
	require_once get_theme_file_path( 'inc/customizer/options/theme-colors-photo.php' );

	
	//
	// Social
	//
	$wp_customize->add_section(
		'theme_social',
		array(
			'title'       => esc_html_x( 'Social Networks', 'customizer section title', 'napoleon' ),
			'description' => esc_html__( 'Enter your social network URLs. Leaving a URL empty will hide its respective icon.', 'napoleon' ),
			'priority'    => 80,
		)
	);
	require_once get_theme_file_path( 'inc/customizer/options/theme-social.php' );

	//
	// Footer
	//
	if ( apply_filters( 'napoleon_customizable_footer', true ) ) {
		$wp_customize->add_panel(
			'theme_footer',
			array(
				'title'    => esc_html_x( 'Footer', 'customizer section title', 'napoleon' ),
				'priority' => 90,
			)
		);

		$wp_customize->add_section(
			'theme_footer_style',
			array(
				'title'    => esc_html_x( 'Footer style', 'customizer section title', 'napoleon' ),
				'panel'    => 'theme_footer',
				'priority' => 10,
			)
		);
		require_once get_theme_file_path( 'inc/customizer/options/theme-footer-style.php' );

		$wp_customize->add_section(
			'theme_footer_bottom_bar',
			array(
				'title'    => esc_html_x( 'Bottom bar', 'customizer section title', 'napoleon' ),
				'panel'    => 'theme_footer',
				'priority' => 20,
			)
		);
		require_once get_theme_file_path( 'inc/customizer/options/theme-footer-bottom-bar.php' );
	} // filter napoleon_customizable_footer

	//
	// Titles
	//
	$wp_customize->add_panel(
		'theme_titles',
		array(
			'title'    => esc_html_x( 'Titles', 'customizer section title', 'napoleon' ),
			'priority' => 100,
		)
	);

	$wp_customize->add_section(
		'theme_titles_general',
		array(
			'title'    => esc_html_x( 'General', 'customizer section title', 'napoleon' ),
			'panel'    => 'theme_titles',
			'priority' => 10,
		)
	);
	require_once get_theme_file_path( 'inc/customizer/options/theme-titles-general.php' );

	$wp_customize->add_section(
		'theme_titles_post',
		array(
			'title'    => esc_html_x( 'Posts', 'customizer section title', 'napoleon' ),
			'panel'    => 'theme_titles',
			'priority' => 20,
		)
	);
	require_once get_theme_file_path( 'inc/customizer/options/theme-titles-post.php' );

	//
	// WooCommerce
	//
	if ( class_exists( 'WooCommerce' ) ) {
		require_once get_theme_file_path( 'inc/customizer/options/woocommerce-product-catalog.php' );

		$wp_customize->add_section(
			'theme_woocommerce_single_product',
			array(
				'title'    => esc_html_x( 'Single Product', 'customizer section title', 'napoleon' ),
				'panel'    => 'woocommerce',
				'priority' => 20,
			)
		);
		require_once get_theme_file_path( 'inc/customizer/options/woocommerce-single-product.php' );

		$wp_customize->add_section(
			'theme_woocommerce_spam',
			array(
				'title'    => esc_html_x( 'Spam Protection', 'customizer section title', 'napoleon' ),
				'panel'    => 'woocommerce',
				'priority' => 30,
			)
		);

		require_once get_theme_file_path( 'inc/customizer/options/woocommerce-spam.php' );

	}

	//
	// Site identity
	//
	require_once get_theme_file_path( 'inc/customizer/options/site-identity.php' );

}



add_action( 'customize_register', 'napoleon_customize_register_custom_controls', 9 );
/**
 * Registers custom Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Reference to the customizer's manager object.
 */
function napoleon_customize_register_custom_controls( $wp_customize ) {
	require_once get_template_directory() . '/inc/customizer/controls/static-text/static-text.php';
	require_once get_template_directory() . '/inc/customizer/controls/alpha-color-picker/alpha-color-picker.php';
}

add_action( 'customize_preview_init', 'napoleon_customize_preview_js' );
/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function napoleon_customize_preview_js() {
	$theme = wp_get_theme();

	wp_enqueue_script( 'napoleon-customizer-preview', get_template_directory_uri() . '/js/admin/customizer-preview.js', array( 'customize-preview' ), $theme->get( 'Version' ), true );
	wp_enqueue_style( 'napoleon-customizer-preview', get_template_directory_uri() . '/css/admin/customizer-preview.css', array( 'customize-preview' ), $theme->get( 'Version' ) );

	// Generic preview code.
	wp_enqueue_style( 'napoleon-customizer-preview-new', get_theme_file_uri( '/inc/customizer/preview/preview.css' ), array(), wp_get_theme()->get( 'Version' ) );

	// Options-specific preview code.
}




add_action('wp_head', 'customizer_custom_fonts');
function customizer_custom_fonts() { 
	$selected_font = get_theme_mod('font_options', 'cairo'); 
	if ($selected_font === 'IBM Plex Sans Arabic') { ?>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@500;700&display=swap" rel="stylesheet">
	<?php }

}

add_action( 'customize_controls_enqueue_scripts', 'napoleon_customize_controls_js' );
function napoleon_customize_controls_js() {
	$theme = wp_get_theme();

	wp_enqueue_style(
		'alpha-color-picker-customizer',
		get_template_directory_uri() . '/inc/customizer/controls/alpha-color-picker/alpha-color-picker.css',
		array(
			'wp-color-picker',
		),
		'1.0.0'
	);
	wp_enqueue_script(
		'alpha-color-picker-customizer',
		get_template_directory_uri() . '/inc/customizer/controls/alpha-color-picker/alpha-color-picker.js',
		array(
			'jquery',
			'wp-color-picker',
		),
		'1.0.0',
		true
	);

	wp_enqueue_style( 'napoleon-customizer-controls', get_theme_file_uri( '/inc/customizer/controls/style.css' ), array(), wp_get_theme()->get( 'Version' ) );
	wp_enqueue_script( 'napoleon-customizer-controls', get_theme_file_uri( '/inc/customizer/controls/scripts.js' ), array(), wp_get_theme()->get( 'Version' ), true );
}

/**
 * Customizer partial callbacks.
 */
require_once get_theme_file_path( '/inc/customizer/partial-callbacks.php' );

/**
 * Customizer generated styles.
 */
require_once get_theme_file_path( '/inc/customizer/generated-styles.php' );

