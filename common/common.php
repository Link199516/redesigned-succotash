<?php
/**
 * Common theme features.
 */

/**
 * Common assets registration
 */
function napoleon_register_common_assets() {
	$theme = wp_get_theme();
	wp_register_style( 'napoleon-common', get_template_directory_uri() . '/common/css/global.css', array(), $theme->get( 'Version' ) );
}
add_action( 'init', 'napoleon_register_common_assets', 8 );
