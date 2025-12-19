<?php
function napoleon_sanitize_metabox_tab_sidebar( $post_id ) {
	// Ignore phpcs issues. nonce validation happens inside napoleon_can_save_meta(), from the caller of this function.
	// @codingStandardsIgnoreStart
	update_post_meta( $post_id, 'napoleon_sidebar', napoleon_sanitize_sidebar( $_POST['napoleon_sidebar'] ) );
	// @codingStandardsIgnoreEnd
}

function napoleon_print_metabox_tab_sidebar( $object, $box ) {

	napoleon_metabox_open_tab( esc_html__( 'Sidebar', 'napoleon' ) );

		$options = napoleon_get_sidebar_choices();
		foreach ( $options as $key => $value ) {
			napoleon_metabox_radio( 'napoleon_sidebar', "sidebar-$key", $key, $value, array( 'default' => apply_filters( 'napoleon_sanitize_sidebar_default', 'right' ) ) );
		}

	napoleon_metabox_close_tab();
}

function napoleon_get_sidebar_choices() {
	return apply_filters( 'napoleon_sidebar_choices', array(
		'left'  => esc_html__( 'Left sidebar', 'napoleon' ),
		'right' => esc_html__( 'Right sidebar', 'napoleon' ),
		'none'  => esc_html__( 'No sidebar', 'napoleon' ),
	) );
}

function napoleon_sanitize_sidebar( $value ) {
	$choices = napoleon_get_sidebar_choices();
	if ( array_key_exists( $value, $choices ) ) {
		return $value;
	}

	return apply_filters( 'napoleon_sanitize_sidebar_default', 'right' );
}
