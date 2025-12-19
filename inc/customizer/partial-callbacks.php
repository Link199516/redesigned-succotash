<?php
if ( ! function_exists( 'napoleon_customize_preview_blogname' ) ) {
	function napoleon_customize_preview_blogname() {
		bloginfo( 'name' );
	}
}

if ( ! function_exists( 'napoleon_customize_preview_blogdescription' ) ) {
	function napoleon_customize_preview_blogdescription() {
		bloginfo( 'description' );
	}
}

/**
 * Renders pagination preview for archive pages.
 *
 * Its results may not be accurate as the actual call may include arguments,
 * however it should be good enough for preview purposes.
 * napoleon_posts_pagination() cannot be used directly as the render callback passes $this and $container_context
 * as the first two arguments.
 */
if ( ! function_exists( 'napoleon_customize_preview_pagination' ) ) {
	function napoleon_customize_preview_pagination( $_this, $container_context ) {
		napoleon_posts_pagination();
	}
}

if ( ! function_exists( 'napoleon_customize_preview_hero' ) ) {
	function napoleon_customize_preview_hero() {
		get_template_part( 'template-parts/hero' );
	}
}

if ( ! function_exists( 'napoleon_customize_preview_google_fonts' ) ) {
	function napoleon_customize_preview_google_fonts( $_this, $container_context ) {
		napoleon_enqueue_google_fonts();
		wp_print_styles( 'napoleon-user-google-fonts' );
	}
}
