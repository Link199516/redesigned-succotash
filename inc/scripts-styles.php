<?php
/**
 * napoleon scripts and styles related functions.
 */


/**
 * Register scripts and styles unconditionally.
 */
function napoleon_register_scripts() {
	$theme = wp_get_theme();

	if ( ! wp_script_is( 'alpha-color-picker', 'enqueued' ) && ! wp_script_is( 'alpha-color-picker', 'registered' ) ) {
		wp_register_style( 'alpha-color-picker', get_template_directory_uri() . '/assets/vendor/alpha-color-picker/alpha-color-picker.css', array(
			'wp-color-picker',
		), '1.0.0' );
		wp_register_script( 'alpha-color-picker', get_template_directory_uri() . '/assets/vendor/alpha-color-picker/alpha-color-picker.js', array(
			'jquery',
			'wp-color-picker',
		), '1.0.0', true );
	}

	if ( ! wp_script_is( 'slick', 'enqueued' ) && ! wp_script_is( 'slick', 'registered' ) ) {
				wp_register_style( 'slick', get_template_directory_uri() . '/assets/vendor/slick/slick.min.css', array(), '1.6.0' );
				wp_register_script( 'slick', get_template_directory_uri() . '/assets/vendor/slick/slick.min.js', array('jquery', ), '1.6.0', true );
	}

	wp_register_style( 'napoleon-repeating-fields', get_template_directory_uri() . '/css/admin/repeating-fields.css', array(), $theme->get( 'Version' ) );
	wp_register_script( 'napoleon-repeating-fields', get_template_directory_uri() . '/js/admin/repeating-fields.js', array(
		'jquery',
		'jquery-ui-sortable',
	), $theme->get( 'Version' ), true );

	wp_register_style( 'font-awesome-5', get_template_directory_uri() . '/assets/vendor/fontawesome/css/font-awesome.min.css', array(), '5.1.0' );

	wp_register_style( 'jquery-magnific-popup', get_template_directory_uri() . '/assets/vendor/magnific-popup/magnific.min.css', array(), '1.0.0' );
	wp_register_script( 'jquery-magnific-popup', get_template_directory_uri() . '/assets/vendor/magnific-popup/jquery.magnific-popup.min.js', array( 'jquery' ), '1.0.0', true );
	wp_register_script( 'napoleon-magnific-init', get_template_directory_uri() . '/js/magnific-init.min.js', array( 'jquery' ), $theme->get( 'Version' ), true );


	wp_register_style( 'napoleon-base', get_template_directory_uri() . '/css/base.min.css', array(), $theme->get( 'Version' ) );

	wp_register_style( 'napoleon-dependencies', false, array(
		'napoleon-base',
		'napoleon-common',
		'slick',
		'font-awesome-5',
	), $theme->get( 'Version' ) );

	$main_dependencies = array(
		'napoleon-dependencies',
	);

	if ( is_child_theme() ) {
		wp_register_style( 'napoleon-style-parent', get_template_directory_uri() . '/style.css', $main_dependencies, $theme->get( 'Version' ) );
	}

	wp_register_style( 'napoleon-style', get_stylesheet_uri(), $main_dependencies, $theme->get( 'Version' ) );


	wp_register_script( 'fitVids', get_template_directory_uri() . '/js/jquery.fitvids.min.js', array( 'jquery' ), '1.1', true );
	wp_register_script( 'sticky-kit', get_template_directory_uri() . '/js/jquery.sticky-kit.min.js', array( 'jquery' ), '1.1.4', true );
	wp_register_script( 'theia-sticky-sidebar', get_template_directory_uri() . '/js/theia-sticky-sidebar.js', array( 'jquery' ), '1.5.0', true );

	wp_register_script( 'napoleon-dependencies', false, array(
		'jquery',
		'slick',
		'fitVids',
		'sticky-kit',
	), $theme->get( 'Version' ), true );

	wp_register_script( 'napoleon-front-scripts', get_template_directory_uri() . '/js/scripts.js', array(
		'napoleon-dependencies',
	), $theme->get( 'Version' ), true );

	$vars = array(
		'ajaxurl'            => admin_url( 'admin-ajax.php' ),
		'search_no_products' => __( 'No products match your query.', 'napoleon' ),
	);
	wp_localize_script( 'napoleon-front-scripts', 'napoleon_vars', $vars );


	wp_register_script( 'napoleon-elementor-ajax', get_template_directory_uri() . '/js/admin/elementor-ajax.js', array(), $theme->get( 'Version' ), true );

	$params = array(
		'ajaxurl'         => admin_url( 'admin-ajax.php' ),
		'no_posts_found'  => esc_html__( 'No posts found.', 'napoleon' ),
		'get_posts_nonce' => wp_create_nonce( 'napoleon_get_posts_nonce' ),
	);
	wp_localize_script( 'napoleon-elementor-ajax', 'napoleon_elementor_ajax', $params );

	// Register Customizer controls script
	wp_register_script( 'napoleon-customizer-controls', get_template_directory_uri() . '/js/admin/customizer-controls.js', array( 'jquery', 'customize-controls' ), $theme->get( 'Version' ), true );

}
add_action( 'init', 'napoleon_register_scripts' );

/**
 * Enqueue scripts and styles.
 */
function napoleon_enqueue_scripts() {
	if ( is_singular() ) {
		wp_enqueue_script( 'theia-sticky-sidebar' );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( get_theme_mod( 'theme_lightbox', 0 ) && is_singular() ) {
		wp_enqueue_style( 'jquery-magnific-popup' );
		wp_enqueue_script( 'jquery-magnific-popup' );
		wp_enqueue_script( 'napoleon-magnific-init' );
	}

	if ( is_child_theme() ) {
		wp_enqueue_style( 'napoleon-style-parent' );
	}

	$selected_font = get_theme_mod('font_options', 'cairo');
	if ($selected_font === 'cairo') {
		wp_enqueue_style( 'cairo-font', get_template_directory_uri() . '/fonts/cairo/cairo.css' ); 
	} elseif ($selected_font === 'Avenir Next World') {
		wp_enqueue_style( 'avenir-font', get_template_directory_uri() . '/fonts/avenir/avenir.css' ); 
	}

	wp_enqueue_style( 'napoleon-style' );
	if ( napoleon_is_theme_license_valid() ) {
		wp_add_inline_style( 'napoleon-style', napoleon_get_all_customizer_css() );
	}
	wp_enqueue_script( 'napoleon-front-scripts' );


}
add_action( 'wp_enqueue_scripts', 'napoleon_enqueue_scripts' );


/**
 * Enqueue admin scripts and styles.
 */
function napoleon_admin_scripts( $hook ) {
	$theme = wp_get_theme();

	wp_register_style( 'napoleon-widgets', get_template_directory_uri() . '/css/admin/widgets.css', array(
		'napoleon-repeating-fields',
		'napoleon-post-meta',
		'alpha-color-picker',
	), $theme->get( 'Version' ) );

	wp_register_script( 'napoleon-widgets', get_template_directory_uri() . '/js/admin/widgets.js', array(
		'jquery',
		'napoleon-repeating-fields',
		'napoleon-post-meta',
		'alpha-color-picker',
	), $theme->get( 'Version' ), true );
	$params = array(
		'ajaxurl'                      => admin_url( 'admin-ajax.php' ),
		'widget_post_type_items_nonce' => wp_create_nonce( 'napoleon-post-type-items' ),
		'term_layouts_dir_uri'         => get_template_directory_uri() . '/images/term-layouts/',
	);
	wp_localize_script( 'napoleon-widgets', 'ThemeWidget', $params );


	//
	// Enqueue
	//
	if ( in_array( $hook, array( 'widgets.php', 'customize.php' ), true ) ) {
		wp_enqueue_style( 'napoleon-repeating-fields' );
		wp_enqueue_script( 'napoleon-repeating-fields' );

		wp_enqueue_media();
		wp_enqueue_style( 'napoleon-widgets' );
		wp_enqueue_script( 'napoleon-widgets' );
		wp_enqueue_script( 'napoleon-customizer-controls' ); // Enqueue here for customize.php
	}

	wp_enqueue_style( 'napoleon-admin-style', get_template_directory_uri() . '/css/admin/admin-style.css', array(), $theme->get( 'Version' ) );

}
add_action( 'admin_enqueue_scripts', 'napoleon_admin_scripts' );


/**
 * Enqueue scripts and styles for Customizer controls.
 */
function napoleon_customizer_controls_assets() {
	$theme = wp_get_theme();

	// Enqueue Select2 CSS from CDN
	wp_enqueue_style( 'select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0-rc.0' );

	// Enqueue Select2 JS from CDN
	wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), '4.1.0-rc.0', true );

	// Enqueue custom script for initializing Select2 on our control
	wp_enqueue_script( 'napoleon-customizer-multi-select', get_template_directory_uri() . '/js/admin/customizer-multi-select.js', array( 'jquery', 'customize-controls', 'select2-js' ), $theme->get( 'Version' ), true );
}
add_action( 'customize_controls_enqueue_scripts', 'napoleon_customizer_controls_assets' );


if ( ! function_exists( 'napoleon_call_scripts' ) ) :
/**
 * Call script functions in  footer
 */
function napoleon_call_scripts() { ?>
<script>
/* <![CDATA[ */
	jQuery(document).ready(function($) {

	
	// 	Display sticky add to cart button
	
	<?php if ( is_product()  && 1 === get_theme_mod( 'show_sticky_atc', 1 ) ) : ?>

		    $(window).scroll(function() {
		       var hT = $('#product-buy-form').offset().top,
		           hH = $('#product-buy-form').outerHeight(),
		           wH = $(window).height(),
		           wS = $(this).scrollTop();
		       if (wS > (hT+hH)){
		           $(".sticky-atc-btn").slideDown(400);

		       } else{
		            $(".sticky-atc-btn").slideUp(400); 
		        }
		    });

	<?php endif; ?>
		
	});

/* ]]> */
</script>
<?php
}
endif;

add_action( 'wp_footer', 'napoleon_call_scripts' );
