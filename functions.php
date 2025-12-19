<?php
/**
 * napoleon functions and definitions
 */

if ( ! function_exists( 'napoleon_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function napoleon_setup() {

	// Default content width.
	$GLOBALS['content_width'] = 960;

	// Make theme available for translation.
	load_theme_textdomain( 'napoleon', get_template_directory() . '/languages' );

    // REMOVED these two lines as they are handled by filters below and were causing a "Notice"
    // load_textdomain( 'maxslider', get_template_directory() . '/languages' );
    // load_textdomain( 'one-click-demo-import', get_template_directory() . '/languages' );


	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	$menus = array(
		'menu-top' => esc_html__( 'Top Menu', 'napoleon' ),
		'menu-1' => esc_html__( 'Main Menu', 'napoleon' ),
	);
	register_nav_menus( $menus );

	// Switch default core markup for search form, comment form, and comments to output valid HTML5.
	add_theme_support( 'html5', apply_filters( 'napoleon_add_theme_support_html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) ) );

	// Add theme support for custom logos.
	add_theme_support( 'custom-logo', apply_filters( 'napoleon_add_theme_support_custom_logo', array() ) );

	// Set up the WordPress core custom background feature.
	$scss = new napoleon_SCSS_Colors( get_theme_file_path( '/css/inc/_variables.scss' ) );
	add_theme_support( 'custom-background', apply_filters( 'napoleon_custom_background_args', array(
		'default-color' => '#fff',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );



	// Image sizes
	set_post_thumbnail_size( 960, 540, true );
	add_image_size( 'napoleon_item', 630, 630, true );
	add_image_size( 'napoleon_item_media_sm', 90, 90, true );
	add_image_size( 'napoleon_hero', 1920, 300, true );
	add_image_size( 'napoleon_block_item_lg', 910, 510, true );
	add_image_size( 'napoleon_block_item_long', 1290, 215, true );
	add_image_size( 'napoleon_block_item_md', 630, 345, true );
	add_image_size( 'napoleon_block_item_xl', 1290, 725, true );

    // Corrected the invisible characters on the lines below
	add_theme_support( 'napoleon-hero', apply_filters( 'napoleon_theme_support_hero_args', wp_parse_args( array(
		'show-default'       => true, // Corrected space here
		'front-page-classes' => 'page-hero-lg',
	), napoleon_theme_support_hero_defaults() ) ) );


	add_theme_support( 'napoleon-hide-single-featured', apply_filters( 'napoleon_theme_support_hide_single_featured_post_types', array(
		'post',
		'page',
	) ) );

	add_theme_support( 'editor-styles' );
	add_editor_style( 'css/admin/editor-styles.css' );

	// This provides back-compat for author descriptions on WP < 4.9. Remove by WP 5.1.
	if ( ! has_filter( 'get_the_author_description', 'wpautop' ) ) {
		add_filter( 'get_the_author_description', 'wpautop' );
	}

	/**
	 * Theme Auto Update.
	 */
	require_once get_theme_file_path( '/inc/updater/theme-updater.php' );

	/**
	 * Include trial notice functionality.
	 */
	require_once get_template_directory() . '/inc/trial-notice.php';


	// Restoring the classic Widgets Editor
	remove_theme_support( 'widgets-block-editor' );

}
endif;
add_action( 'after_setup_theme', 'napoleon_setup' );


/*
 * Load translations for Maxslider plugin
 */
add_filter( 'load_textdomain_mofile', 'load_maxslider_translation_file', 10, 2 );

function load_maxslider_translation_file( $mofile, $domain ) {
	if ( 'maxslider' === $domain ) {
		$mofile =  get_template_directory() . '/languages/maxslider-'. get_locale() .'.mo';
	}
	return $mofile;
}

/*
 * Load translations for one click demo install plugin
 */
add_filter( 'load_textdomain_mofile', 'load_ocdi_translation_file', 10, 2 );

function load_ocdi_translation_file( $mofile, $domain ) {

	if ( 'one-click-demo-import' === $domain ) {
		$mofile =  get_template_directory() . '/languages/one-click-demo-import-'. get_locale() .'.mo';
	}
	return $mofile;
}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function napoleon_content_width() {
	$content_width = $GLOBALS['content_width'];

	if ( is_page_template( 'templates/full-width-page.php' )
		|| is_page_template( 'templates/front-page.php' )
		|| is_page_template( 'templates/builder.php' )
		|| is_page_template( 'templates/builder-contained.php' )
	) {
		$content_width = 1290;
	} elseif ( is_singular() || is_home() || is_archive() ) {
        // Corrected space here
		$info            = napoleon_get_layout_info();
		$content_width = $info['content_width'];
	}

	$GLOBALS['content_width'] = apply_filters( 'napoleon_content_width', $content_width );
}
add_action( 'template_redirect', 'napoleon_content_width', 0 );


add_filter( 'wp_page_menu', 'napoleon_wp_page_menu', 10, 2 );
function napoleon_wp_page_menu( $menu, $args ) {
	$menu = preg_replace( '#^<div .*?>#', '', $menu, 1 );
	$menu = preg_replace( '#</div>$#', '', $menu, 1 );
	$menu = preg_replace( '#^<ul>#', '<ul id="' . esc_attr( $args['menu_id'] ) . '" class="' . esc_attr( $args['menu_class'] ) . '">', $menu, 1 );
	return $menu;
}

if ( ! function_exists( 'napoleon_get_columns_classes' ) ) :
	function napoleon_get_columns_classes( $columns ) {
		switch ( intval( $columns ) ) {
			case 1:
				$classes = 'col-12';
				break;
			case 2:
				$classes = 'col-sm-6 col-6';
				break;
			case 3:
				$classes = 'col-lg-4 col-sm-6 col-6';
				break;
			case 4:
			default:
				$classes = 'col-xl-3 col-lg-4 col-sm-6 col-6';
				break;
		}

		return apply_filters( 'napoleon_get_columns_classes', $classes, $columns );
	}
endif;

if ( ! function_exists( 'napoleon_has_sidebar' ) ) :
/**
 * Determine if a sidebar is being displayed.
 */
function napoleon_has_sidebar() {
	$has_sidebar = false;

	if ( class_exists( 'WooCommerce' ) && is_woocommerce() ) {
		if ( is_active_sidebar( 'shop' ) ) {
			$has_sidebar = true;
		}

		if ( is_product() ) {
			$sidebar_option = get_theme_mod( 'theme_product_layout', 'right' );
			if ( ! $has_sidebar || 'none' === $sidebar_option ) {
				$has_sidebar = false;
			}
		}
	} elseif ( is_home() || is_archive() ) {
		if ( get_theme_mod( 'archive_sidebar', 1 ) && is_active_sidebar( 'sidebar-1' ) ) {
			$has_sidebar = true;
		}
	} elseif ( ! is_page() && is_active_sidebar( 'sidebar-1' ) ) {
		$has_sidebar = true;
	} elseif ( is_page() && is_active_sidebar( 'sidebar-2' ) ) {
		$has_sidebar = true;
	}

	return apply_filters( 'napoleon_has_sidebar', $has_sidebar );
}
endif;

if ( ! function_exists( 'napoleon_get_layout_info' ) ) :
/**
 * Return appropriate layout information.
 */
function napoleon_get_layout_info() {
	$has_sidebar = napoleon_has_sidebar();

	$classes = array(
		'container_classes' => $has_sidebar ? 'col-lg-9 col-12' : 'col-xl-8 col-lg-10 col-12',
        // Corrected space here
		'sidebar_classes'   => $has_sidebar ? 'col-lg-3 col-12' : '',
        // Corrected space here
		'content_width'     => 960,
        // Corrected space here
		'has_sidebar'       => $has_sidebar,
	);

	$sidebar_option = '';
	if ( is_singular() ) {
		$sidebar_option = get_post_meta( get_queried_object_id(), 'napoleon_sidebar', true );
	}

	$shop_layout = get_theme_mod( 'theme_shop_layout', 'right' );

	if ( class_exists( 'WooCommerce' ) && is_woocommerce() ) {

		if ( is_product() ) {
			$sidebar_option = get_theme_mod( 'theme_product_layout', 'right' );

			if ( ! $has_sidebar || 'none' === $sidebar_option ) {
				$classes = array(
					'container_classes' => 'col-12',
                    // Corrected space here
					'sidebar_classes'   => '',
                    // Corrected space here
					'content_width'     => 740,
                    // Corrected space here
					'has_sidebar'       => false,
				);
			} else {
				$classes = array(
					'container_classes' => 'col-lg-9 col-12',
                    // Corrected space here
					'sidebar_classes'   => 'col-lg-3 col-12',
                    // Corrected space here
					'content_width'     => 550,
                    // Corrected space here
					'has_sidebar'       => $has_sidebar,
				);
			}
		} elseif ( ( is_shop() || is_product_taxonomy() ) && 'full' === $shop_layout ) {
			$classes = array(
				'container_classes' => 'col-12',
                // Corrected space here
				'sidebar_classes'   => '',
                // Corrected space here
				'content_width'     => 960,
                // Corrected space here
				'has_sidebar'       => false,
			);
		} else {
			$classes = array(
				'container_classes' => $has_sidebar ? 'col-lg-9 col-12' : 'col-12',
                // Corrected space here
				'sidebar_classes'   => $has_sidebar ? 'col-lg-3 col-12' : '',
                // Corrected space here
				'content_width'     => 960,
                // Corrected space here
				'has_sidebar'       => $has_sidebar,
			);
		}
	} elseif ( is_singular() ) {
		if ( 'none' === get_post_meta( get_the_ID(), 'napoleon_sidebar', true ) ) {
			$classes = array(
				'container_classes' => 'col-xl-8 col-lg-10 col-12',
                // Corrected space here
				'sidebar_classes'   => '',
                // Corrected space here
				'content_width'     => 960,
                // Corrected space here
				'has_sidebar'       => false,
			);
		}
	}


	$classes['row_classes'] = '';
	if ( is_singular() ) {
		if ( ! $has_sidebar || 'none' === $sidebar_option ) {
			$classes['row_classes'] = 'justify-content-center';
		} elseif ( 'left' === $sidebar_option ) {
			$classes['row_classes'] = 'flex-row-reverse';
		}
	} elseif ( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_taxonomy() ) && 'left' === $shop_layout ) {
		$classes['row_classes'] = 'flex-row-reverse';
	} elseif ( ! $has_sidebar ) {
		$classes['row_classes'] = 'justify-content-center';
	}

	return apply_filters( 'napoleon_layout_info', $classes, $has_sidebar );
}
endif;

add_filter( 'tiny_mce_before_init', 'napoleon_insert_wp_editor_formats' );
function napoleon_insert_wp_editor_formats( $init_array ) {
	$style_formats = array(
		array(
            // Corrected space here
			'title'   => esc_html__( 'Intro text (big text)', 'napoleon' ),
            // Corrected space here
			'block'   => 'div',
			'classes' => 'entry-content-intro',
			'wrapper' => true,
		),
		array(
            // Corrected space here
			'title'   => esc_html__( '2 Column Text', 'napoleon' ),
            // Corrected space here
			'block'   => 'div',
			'classes' => 'entry-content-column-split',
			'wrapper' => true,
		),
	);

	$init_array['style_formats'] = wp_json_encode( $style_formats );

	return $init_array;
}

add_filter( 'mce_buttons_2', 'napoleon_mce_buttons_2' );
function napoleon_mce_buttons_2( $buttons ) {
	array_unshift( $buttons, 'styleselect' );

	return $buttons;
}

add_action( 'admin_init', 'napoleon_admin_setup_hide_single_featured' );
function napoleon_admin_setup_hide_single_featured() {
	if ( current_theme_supports( 'napoleon-hide-single-featured' ) ) {
		$hide_featured_support = get_theme_support( 'napoleon-hide-single-featured' );
		$hide_featured_support = $hide_featured_support[0];

		foreach ( $hide_featured_support as $supported_post_type ) {
			add_meta_box( 'napoleon-single-featured-visibility', esc_html__( 'Featured Image Visibility', 'napoleon' ), 'napoleon_single_featured_visibility_metabox', $supported_post_type, 'side', 'default' );
		}
	}

	add_action( 'save_post', 'napoleon_hide_single_featured_save_post' );
}

add_action( 'init', 'napoleon_setup_hide_single_featured' );
function napoleon_setup_hide_single_featured() {
	if ( current_theme_supports( 'napoleon-hide-single-featured' ) ) {
		add_filter( 'get_post_metadata', 'napoleon_hide_single_featured_get_post_metadata', 10, 4 );
	}
}

function napoleon_single_featured_visibility_metabox( $object, $box ) {
	$fieldname = 'napoleon_hide_single_featured';
    // Corrected space here
	$checked   = get_post_meta( $object->ID, $fieldname, true );

	?>
		<input type="checkbox" id="<?php echo esc_attr( $fieldname ); ?>" class="check" name="<?php echo esc_attr( $fieldname ); ?>" value="1" <?php checked( $checked, 1 ); ?> />
		<label for="<?php echo esc_attr( $fieldname ); ?>"><?php esc_html_e( "Hide when viewing this post's page", 'napoleon' ); ?></label>
	<?php
	wp_nonce_field( 'napoleon_hide_single_featured_nonce', '_napoleon_hide_single_featured_meta_box_nonce' );
}

function napoleon_hide_single_featured_get_post_metadata( $value, $post_id, $meta_key, $single ) {
	$hide_featured_support = get_theme_support( 'napoleon-hide-single-featured' );
	$hide_featured_support = $hide_featured_support[0];

	if ( ! in_array( get_post_type( $post_id ), $hide_featured_support, true ) ) {
		return $value;
	}

	if ( '_thumbnail_id' === $meta_key && ( is_single( $post_id ) || is_page( $post_id ) ) && get_post_meta( $post_id, 'napoleon_hide_single_featured', true ) ) {
		return false;
	}

	return $value;
}

function napoleon_hide_single_featured_save_post( $post_id ) {
	$hide_featured_support = get_theme_support( 'napoleon-hide-single-featured' );
	$hide_featured_support = $hide_featured_support[0];

	if ( ! in_array( get_post_type( $post_id ), $hide_featured_support, true ) ) {
		return;
	}

	if ( isset( $_POST['_napoleon_hide_single_featured_meta_box_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['_napoleon_hide_single_featured_meta_box_nonce'] ), 'napoleon_hide_single_featured_nonce' ) ) {
		update_post_meta( $post_id, 'napoleon_hide_single_featured', isset( $_POST['napoleon_hide_single_featured'] ) ); // Input var okay.
	}
}

if ( ! function_exists( 'napoleon_get_template_part' ) ) :
/**
 * Load a template part into a template, optionally passing an associative array
 * that will be available as variables.
 *
 * Makes it easy for a theme to reuse sections of code in a easy to overload way
 * for child themes.
 *
 * Includes the named template part for a theme or if a name is specified then a
 * specialised part will be included. If the theme contains no {slug}.php file
 * then no template will be included.
 *
 * The template is included using require, not require_once, so you may include the
 * same template part multiple times.
 *
 * For the $name parameter, if the file is called "{slug}-special.php" then specify
 * "special".
 *
 * When $data is an array, the key of each value becomes the name of the variable,
 * and the value becomes the variable's value.
 *
 * $data_overwrite should be one of the extract() flags, as described in http://www.php.net/extract
 *
 * @uses locate_template()
 * @uses do_action() Calls 'get_template_part_{$slug}' action.
 * @uses do_action() Calls 'at_get_template_part_{$slug}' action.
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 * @param array $data A key-value array of data to be available as variables.
 * @param int $data_overwrite The EXTR_* constant to pass to extract( $data ).
 */
function napoleon_get_template_part( $slug, $name = null, $data = array(), $data_overwrite = EXTR_PREFIX_SAME ) {
	// Code similar to get_template_part() as of WP v4.9.8

	// Retain the same action hook, so that calls to our function respond to the same hooked functions.
	do_action( "get_template_part_{$slug}", $slug, $name );

	// Add our own action hook, so that we can hook using $data also.
	do_action( "at_get_template_part_{$slug}", $slug, $name, $data );

	$templates = array();
    // Corrected space here
	$name      = (string) $name;

	if ( '' !== $name ) {
		$templates[] = "{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";

	// Don't load the template ( it would normally call load_template() )
	$_template_file = locate_template( $templates, false, false );

	// Code similar to load_template()
	global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

	if ( is_array( $wp_query->query_vars ) ) {
		extract( $wp_query->query_vars, EXTR_SKIP );
	}

	if ( is_array( $data ) and ( count( $data ) > 0 ) ) {
		extract( $data, $data_overwrite, 'imp' );
	}

	require( $_template_file );
}
endif;


// Add classes to body
add_filter( 'body_class','my_body_classes' );
function my_body_classes( $classes ) {

		if ( get_theme_mod( 'display_buy_button', 1) == 0 ) { // Corrected space here
			$classes[] = 'no-buy-button';
		}

		if ( get_theme_mod( 'add_shadow_effect', 1) == 0 ) { // Corrected space here
			$classes[] = 'no-shadow-effect';
		}

    return $classes;

}

require_once get_theme_file_path( '/inc/updater/activate-notice.php' );

/**
 * Essential functions for napoleon theme.
 */
require_once get_theme_file_path( '/inc/essentials/essential-functions.php' );

/**
 * Common theme features.
 */
require_once get_theme_file_path( '/common/common.php' );

/**
 * Template tags.
 */
require_once get_theme_file_path( '/inc/template-tags.php' );

/**
 * Sanitization functions.
 */
require_once get_theme_file_path( '/inc/sanitization.php' );

/**
 * Hooks.
 */
require_once get_theme_file_path( '/inc/default-hooks.php' );

/**
 * Scripts and styles.
 */
require_once get_theme_file_path( '/inc/scripts-styles.php' );

/**
 * Sidebars and widgets.
 */
require_once get_theme_file_path( '/inc/sidebars-widgets.php' );

/**
 * Hero support. Needed even if theme doesn't support hero.
 */
require_once get_theme_file_path( '/inc/hero.php' );

/**
 * Various helper functions, so that this functions.php is cleaner.
 */
require_once get_theme_file_path( '/inc/helpers.php' );

/**
 * WooCommerce related code.
 */
require_once get_theme_file_path( '/inc/woocommerce.php' );

/**
 * Elementor related code.
 */
require_once get_theme_file_path( '/inc/elementor.php' );

/**
 * MaxSlider related code.
 */
require_once get_theme_file_path( '/inc/maxslider.php' );

/**
 * Post types listing related functions.
 */
require_once get_theme_file_path( '/inc/items-listing.php' );

/**
 * SCSS Colors reader.
 */
require_once get_theme_file_path( '/inc/class-scss-colors.php' );


/**
 * One Page Order Form
 */
require_once get_theme_file_path( '/inc/order-form/codplugin.php' );

/**
 * Customizer functions.
 */
require_once get_theme_file_path( '/inc/customizer-functions.php' );


// --- Add Meta Box for Napoleon Variation Option ---

/**
 * Adds a meta box to the product edit screen.
 */
function napoleon_add_variation_options_metabox() {
    add_meta_box(
        'napoleon_variation_options',         // Unique ID
        __( 'Napoleon Variation Options', 'napoleon' ), // Box title
        'napoleon_variation_options_metabox_html', // Content callback, must be of type callable
        'product',                           // Post type
        'side',                              // Context ('normal', 'side', 'advanced')
        'default'                            // Priority ('high', 'low', 'default')
    );
}
add_action( 'add_meta_boxes', 'napoleon_add_variation_options_metabox' );

/**
 * Renders the HTML for the meta box.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function napoleon_variation_options_metabox_html( $post ) {
    wp_nonce_field( 'napoleon_variation_options_nonce', 'napoleon_variation_options_nonce_field' );

    // Get saved meta values
    $use_default = get_post_meta( $post->ID, '_napoleon_use_default_variations', true );
    $use_specific = get_post_meta( $post->ID, '_napoleon_use_specific_variations', true );
    $position = get_post_meta( $post->ID, '_napoleon_variation_position', true );
    $columns = get_post_meta( $post->ID, '_napoleon_variation_columns', true );
    $hide_label = get_post_meta( $post->ID, '_napoleon_hide_variation_label', true );
    ?>
    <style>
        .napoleon-metabox-tabs {
            display: flex;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px;
        }
        .napoleon-metabox-tabs .tab-link {
            padding: 10px 15px;
            cursor: pointer;
            border: 1px solid #ccc;
            border-bottom: none;
            margin-right: 5px;
            background: #f1f1f1;
        }
        .napoleon-metabox-tabs .tab-link.current {
            background: #fff;
            border-bottom: 1px solid #fff;
        }
        .napoleon-metabox-tab-content {
            display: none;
        }
        .napoleon-metabox-tab-content.current {
            display: block;
        }
        .napoleon-specific-settings {
            display: <?php echo $use_specific ? 'block' : 'none'; ?>;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
    </style>

    <div class="napoleon-metabox-tabs">
        <div class="tab-link current" data-tab="tab-1"><?php _e('Variation Type', 'napoleon'); ?></div>
        <div class="tab-link" data-tab="tab-2"><?php _e('Variation Placement', 'napoleon'); ?></div>
    </div>

    <div id="tab-1" class="napoleon-metabox-tab-content current">
        <p>
            <input type="checkbox" id="napoleon_use_default_variations" name="napoleon_use_default_variations" value="1" <?php checked( $use_default, '1' ); ?> />
            <label for="napoleon_use_default_variations"><?php _e( 'استخدام متغيرات نابليون الافتراضية لهذا المنتج', 'napoleon' ); ?></label>
        </p>
    </div>

    <div id="tab-2" class="napoleon-metabox-tab-content">
        <p>
            <input type="checkbox" id="napoleon_use_specific_variations" name="napoleon_use_specific_variations" value="1" <?php checked( $use_specific, '1' ); ?> />
            <label for="napoleon_use_specific_variations"><?php _e( 'Use specific display settings for this product', 'napoleon' ); ?></label>
        </p>
        <div class="napoleon-specific-settings">
            <p>
                <label for="napoleon_variation_position"><?php _e( 'Position', 'napoleon' ); ?></label><br>
                <select id="napoleon_variation_position" name="napoleon_variation_position">
                    <option value="bottom" <?php selected( $position, 'bottom' ); ?>><?php _e( 'Bottom', 'napoleon' ); ?></option>
                    <option value="top" <?php selected( $position, 'top' ); ?>><?php _e( 'Top', 'napoleon' ); ?></option>
                </select>
            </p>
            <p>
                <label for="napoleon_variation_columns"><?php _e( 'Columns', 'napoleon' ); ?></label><br>
                <input type="number" id="napoleon_variation_columns" name="napoleon_variation_columns" value="<?php echo esc_attr( $columns ); ?>" min="1" max="10" />
            </p>
            <p>
                <input type="checkbox" id="napoleon_hide_variation_label" name="napoleon_hide_variation_label" value="1" <?php checked( $hide_label, '1' ); ?> />
                <label for="napoleon_hide_variation_label"><?php _e( 'Hide Variation Label', 'napoleon' ); ?></label>
            </p>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('.napoleon-metabox-tabs .tab-link').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');

                $('.napoleon-metabox-tabs .tab-link').removeClass('current');
                $('.napoleon-metabox-tab-content').removeClass('current');

                $(this).addClass('current');
                $('#' + tab).addClass('current');
            });

            $('#napoleon_use_specific_variations').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.napoleon-specific-settings').slideDown();
                } else {
                    $('.napoleon-specific-settings').slideUp();
                }
            });
        });
    </script>
    <?php
}

/**
 * Saves the custom meta box data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function napoleon_save_variation_options_meta( $post_id ) {
    // Check if our nonce is set.
    if ( ! isset( $_POST['napoleon_variation_options_nonce_field'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( sanitize_key($_POST['napoleon_variation_options_nonce_field']), 'napoleon_variation_options_nonce' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'product' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_product', $post_id ) ) {
            return;
        }
    } else {
         // Assuming it's a post if not a product, adjust if needed for other post types
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    // Save "Use Default Variations"
    $use_default = isset( $_POST['napoleon_use_default_variations'] ) ? '1' : '0';
    update_post_meta( $post_id, '_napoleon_use_default_variations', $use_default );

    // Save "Use Specific Variations"
    if ( isset( $_POST['napoleon_use_specific_variations'] ) ) {
        update_post_meta( $post_id, '_napoleon_use_specific_variations', '1' );
    } else {
        update_post_meta( $post_id, '_napoleon_use_specific_variations', '0' );
    }

    // Save Position
    // Always save a value, defaulting to the theme's default if the specific settings are not used.
    $position = get_theme_mod( 'napoleon_variation_swatches_position', 'bottom' );
    if ( isset( $_POST['napoleon_use_specific_variations'] ) && '1' === $_POST['napoleon_use_specific_variations'] && isset( $_POST['napoleon_variation_position'] ) ) {
        $position = sanitize_text_field( $_POST['napoleon_variation_position'] );
    }
    update_post_meta( $post_id, '_napoleon_variation_position', $position );

    // Save Columns
    // Always save a value, defaulting to the theme's default if the specific settings are not used.
    $columns = get_theme_mod( 'napoleon_variation_swatches_columns', 5 );
    if ( isset( $_POST['napoleon_use_specific_variations'] ) && '1' === $_POST['napoleon_use_specific_variations'] && isset( $_POST['napoleon_variation_columns'] ) ) {
        $columns = absint( $_POST['napoleon_variation_columns'] );
    }
    update_post_meta( $post_id, '_napoleon_variation_columns', $columns );

    // Save "Hide Variation Label"
    // Always save a value, defaulting to the theme's default if the specific settings are not used.
    $hide_label = get_theme_mod( 'napoleon_hide_variation_label', false ) ? '1' : '0';
    if ( isset( $_POST['napoleon_use_specific_variations'] ) && '1' === $_POST['napoleon_use_specific_variations'] && isset( $_POST['napoleon_hide_variation_label'] ) ) {
        $hide_label = '1' === $_POST['napoleon_hide_variation_label'] ? '1' : '0';
    } else {
        // If specific settings are not used, explicitly save the default value.
        $hide_label = get_theme_mod( 'napoleon_hide_variation_label', false ) ? '1' : '0';
    }
    update_post_meta( $post_id, '_napoleon_hide_variation_label', $hide_label );
}
add_action( 'save_post_product', 'napoleon_save_variation_options_meta' ); // Use save_post_{post_type} for specific post types
// --- End Meta Box Code ---

/**
 * Periodically checks the Napoleon theme license with a soft-fallback mechanism.
 *  * - Checks every 12 hours.
 * - On 1st API connection failure, it schedules a single retry in 3 hours.
 * - On 2nd consecutive failure (the retry), it waits for the next 12-hour cycle.
 * - The license status is NEVER changed if the API server cannot be reached.
 */


function napoleon_related_products_by_category_or_tag() {
    global $product;
    if ( ! is_product() ) {
        return;
    }

    $related_products = array();
    $product_id = $product->get_id();

    // Get related products by category
    $related_by_cat = wc_get_related_products( $product_id, 4, array() );
    if ( ! empty( $related_by_cat ) ) {
        $related_products = array_merge( $related_products, $related_by_cat );
    }

    // Get related products by tag
    $related_by_tag = wc_get_related_products( $product_id, 4, array() );
    if ( ! empty( $related_by_tag ) ) {
        $related_products = array_merge( $related_products, $related_by_tag );
    }

    $related_products = array_unique( $related_products );
    $related_products = array_slice( $related_products, 0, 4 );

    if ( $related_products ) {
        echo '<div class="col-12">';
		echo '<div class="section-heading">';
		echo '<h3 class="section-title">' . esc_html__( 'Related products', 'napoleon' ) . '</h3>';
		echo '</div>';
		echo '<div class="row row-items columns-4">';
        foreach ( $related_products as $related_product_id ) {
            $post_object = get_post( $related_product_id );
            setup_postdata( $GLOBALS['post'] =& $post_object );
			echo '<div class="col-xl-3 col-lg-4 col-sm-6 col-6">';
            wc_get_template_part( 'content', 'product' );
			echo '</div>';
        }
		echo '</div>';
		echo '</div>';
    }
    wp_reset_postdata();
}

// Add "Most Popular" checkbox to variation options
add_action( 'woocommerce_variation_options_pricing', 'napoleon_add_most_popular_checkbox', 10, 3 );
function napoleon_add_most_popular_checkbox( $loop, $variation_data, $variation ) {
    echo '<div class="options_group">';
    woocommerce_wp_checkbox(
        array(
            'id'            => '_most_popular_variation[' . $variation->ID . ']',
            'label'         => __( 'Most Popular', 'napoleon' ),
            'description'   => __( 'Set this variation as the most popular.', 'napoleon' ),
            'value'         => get_post_meta( $variation->ID, '_most_popular_variation', true ),
            'cbvalue'       => 'yes',
        )
    );

    woocommerce_wp_text_input(
        array(
            'id'                => '_most_popular_badge_text[' . $variation->ID . ']',
            'label'             => __( 'Badge Text', 'napoleon' ),
            'placeholder'       => __( 'Most Popular', 'napoleon' ),
            'desc_tip'          => 'true',
            'description'       => __( 'Enter the text to display on the badge. Defaults to "Most Popular".', 'napoleon' ),
            'value'             => get_post_meta( $variation->ID, '_most_popular_badge_text', true ),
            'wrapper_class'     => 'form-row form-row-full most-popular-badge-text-wrapper',
        )
    );
    echo '</div>';
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            function toggleBadgeText(checkbox) {
                var textField = $(checkbox).closest('.options_group').find('.most-popular-badge-text-wrapper');
                if ($(checkbox).is(':checked')) {
                    textField.show();
                } else {
                    textField.hide();
                }
            }

            $('input[name^="_most_popular_variation"]').each(function() {
                toggleBadgeText(this);
            });

            $(document).on('change', 'input[name^="_most_popular_variation"]', function() {
                toggleBadgeText(this);
            });
        });
    </script>
    <?php
}

// Save "Most Popular" checkbox value
add_action( 'woocommerce_save_product_variation', 'napoleon_save_most_popular_checkbox', 10, 2 );
function napoleon_save_most_popular_checkbox( $variation_id, $i ) {
    $most_popular = isset( $_POST['_most_popular_variation'][ $variation_id ] ) ? 'yes' : 'no';
    update_post_meta( $variation_id, '_most_popular_variation', $most_popular );

    if ( isset( $_POST['_most_popular_badge_text'][ $variation_id ] ) ) {
        $badge_text = sanitize_text_field( $_POST['_most_popular_badge_text'][ $variation_id ] );
        update_post_meta( $variation_id, '_most_popular_badge_text', $badge_text );
    }
}
