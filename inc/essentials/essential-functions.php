<?php

/**
 * Enqueue admin scripts and styles.
 */
function napoleon_essential_admin_scripts( $hook ) {

	wp_register_style( 'napoleon-post-meta', get_theme_file_uri( 'inc/essentials/assets/css/post-meta.css' ), array(
		'alpha-color-picker',
	), wp_get_theme()->get( 'Version' ) );
	wp_register_script( 'napoleon-post-meta', get_theme_file_uri( 'inc/essentials/assets/js/post-meta.js' ), array(
		'media-editor',
		'jquery',
		'jquery-ui-sortable',
		'alpha-color-picker',
	), wp_get_theme()->get( 'Version' ), true );

	$settings = array(
		'ajaxurl'             => admin_url( 'admin-ajax.php' ),
		'tSelectFile'         => esc_html__( 'Select file', 'napoleon' ),
		'tSelectFiles'        => esc_html__( 'Select files', 'napoleon' ),
		'tUseThisFile'        => esc_html__( 'Use this file', 'napoleon' ),
		'tUseTheseFiles'      => esc_html__( 'Use these files', 'napoleon' ),
		'tUpdateGallery'      => esc_html__( 'Update gallery', 'napoleon' ),
		'tLoading'            => esc_html__( 'Loading...', 'napoleon' ),
		'tPreviewUnavailable' => esc_html__( 'Gallery preview not available.', 'napoleon' ),
		'tRemoveImage'        => esc_html__( 'Remove image', 'napoleon' ),
		'tRemoveFromGallery'  => esc_html__( 'Remove from gallery', 'napoleon' ),
	);
	wp_localize_script( 'napoleon-post-meta', 'napoleon_PostMeta', $settings );


	wp_register_script( 'napoleon-term-meta', get_theme_file_uri( 'inc/essentials/assets/js/term-meta.js' ), array(
		'jquery',
		'wp-color-picker',
	), wp_get_theme()->get( 'Version' ), true );


	//
	// Enqueue
	//
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		wp_enqueue_media();
		wp_enqueue_style( 'napoleon-post-meta' );
		wp_enqueue_script( 'napoleon-post-meta' );
	}

	if ( in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) ) {
		wp_enqueue_media();
		wp_enqueue_style( 'napoleon-post-meta' );
		wp_enqueue_script( 'napoleon-post-meta' );

		wp_enqueue_script( 'napoleon-term-meta' );
		wp_enqueue_style( 'wp-color-picker' );
	}

}
add_action( 'admin_enqueue_scripts', 'napoleon_essential_admin_scripts' );


if ( ! function_exists( 'napoleon_get_hero_show_choices' ) ) :
	function napoleon_get_hero_show_choices() {
		return apply_filters( 'napoleon_hero_show_choices', array(
			''    => esc_html__( 'Default', 'napoleon' ),
			'yes' => esc_html__( 'Yes', 'napoleon' ),
			'no'  => esc_html__( 'No', 'napoleon' ),
		) );
	}
endif;

if ( ! function_exists( 'napoleon_sanitize_hero_show' ) ) :
	function napoleon_sanitize_hero_show( $value ) {
		$choices = napoleon_get_hero_show_choices();
		if ( array_key_exists( $value, $choices ) ) {
			return $value;
		}

		return apply_filters( 'napoleon_sanitize_hero_show_default', '' );
	}
endif;


/**
 * Custom fields / post types / taxonomies.
 */
require get_theme_file_path( '/inc/essentials/custom-fields-post.php' );
require get_theme_file_path( '/inc/essentials/custom-fields-page.php' );

/**
 * Post meta helpers.
 */
require get_theme_file_path( '/inc/essentials/post-meta.php' );
require get_theme_file_path( '/inc/essentials/post-meta-title-subtitle.php' );
require get_theme_file_path( '/inc/essentials/post-meta-hero.php' );
require get_theme_file_path( '/inc/essentials/post-meta-sidebar.php' );

/**
 * Post types listing related functions.
 */
require get_theme_file_path( '/inc/essentials/items-listing.php' );

/**
 * User fields.
 */
require get_theme_file_path( '/inc/essentials/user-meta.php' );

/**
 * Term metadata.
 */
require get_theme_file_path( '/inc/essentials/term-meta.php' );

/**
 * Shortcodes.
 */
require get_theme_file_path( '/inc/essentials/shortcodes.php' );
