<?php
function napoleon_sanitize_metabox_tab_sub_title( $post_id ) {
	// Ignore phpcs issues. nonce validation happens inside napoleon_can_save_meta(), from the caller of this function.
	// @codingStandardsIgnoreStart
	update_post_meta( $post_id, 'title', wp_kses( $_POST['title'], napoleon_get_allowed_tags() ) );
	update_post_meta( $post_id, 'subtitle', wp_kses( $_POST['subtitle'], napoleon_get_allowed_tags( 'guide' ) ) );
	// @codingStandardsIgnoreEnd
}

function napoleon_print_metabox_tab_sub_title( $object, $box ) {

	napoleon_metabox_open_tab( esc_html__( 'Title / Subtitle', 'napoleon' ) );

		napoleon_metabox_guide( array(
			wp_kses( __( 'You can provide an HTML version of your title, in order to format it according to your needs. If you leave it empty, the normal title will be used instead.', 'napoleon' ), napoleon_get_allowed_tags( 'guide' ) ),
			/* translators: %1$s is an opening HTML tag. %2$s is a closing HTML tag. */
			wp_kses( sprintf( __( 'You can wrap some text within <code>%1$s</code> and <code>%2$s</code> in order to make it stand out.', 'napoleon' ), esc_html( '<span class="text-theme">' ), esc_html( '</span>' ) ), napoleon_get_allowed_tags( 'guide' ) ),
		) );
		napoleon_metabox_input( 'title', esc_html__( 'Page Title (overrides the normal title):', 'napoleon' ) );
		napoleon_metabox_input( 'subtitle', esc_html__( 'Page Subtitle:', 'napoleon' ) );

	napoleon_metabox_close_tab();
}
