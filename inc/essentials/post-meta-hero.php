<?php
function napoleon_sanitize_metabox_tab_hero( $post_id ) {
	// Ignore phpcs issues. nonce validation happens inside napoleon_can_save_meta(), from the caller of this function.
	// @codingStandardsIgnoreStart

	$support = get_theme_support( 'napoleon-hero' );
	$support = $support[0];
	if ( ! $support['required'] ) {
		$show = isset( $_POST['hero_show'] ) ? sanitize_key( $_POST['hero_show'] ) : '';
		$show = 'inherit' === $show ? 'inherit' : absint( $show );
		update_post_meta( $post_id, 'hero_show', $show );
	}

	update_post_meta( $post_id, 'hero_text_align', napoleon_sanitize_text_align( $_POST['hero_text_align'] ) );
	update_post_meta( $post_id, 'page_title_hide', isset( $_POST['page_title_hide'] ) ? 1 : 0 );

	update_post_meta( $post_id, 'hero_image_id', napoleon_sanitize_intval_or_empty( $_POST['hero_image_id'] ) );
	update_post_meta( $post_id, 'hero_bg_color', sanitize_hex_color( $_POST['hero_bg_color'] ) );
	update_post_meta( $post_id, 'hero_text_color', sanitize_hex_color( $_POST['hero_text_color'] ) );
	update_post_meta( $post_id, 'hero_overlay_color', napoleon_sanitize_rgba_color( $_POST['hero_overlay_color'] ) );
	update_post_meta( $post_id, 'hero_image_repeat', napoleon_sanitize_image_repeat( $_POST['hero_image_repeat'] ) );
	update_post_meta( $post_id, 'hero_image_position_x', napoleon_sanitize_image_position_x( $_POST['hero_image_position_x'] ) );
	update_post_meta( $post_id, 'hero_image_position_y', napoleon_sanitize_image_position_y( $_POST['hero_image_position_y'] ) );
	update_post_meta( $post_id, 'hero_image_attachment', napoleon_sanitize_image_attachment( $_POST['hero_image_attachment'] ) );
	update_post_meta( $post_id, 'hero_image_cover', isset( $_POST['hero_image_cover'] ) ? 1 : 0 );
	// @codingStandardsIgnoreEnd
}

function napoleon_print_metabox_tab_hero( $object, $box ) {
	$support = get_theme_support( 'napoleon-hero' );
	$support = $support[0];

	$page_title_hide_default    = $support['required'] || $support['show-default'] ? 1 : 0;
	$page_title_hide_guide_text = __( 'Since the hero section shows the title by default, you may want to disable the page title (shown before the content).', 'napoleon' );

	if ( 'post' === get_post_type( $object->ID ) ) {
		$page_title_hide_default = 0;
		/* translators: %s is a user-provided title. */
		$page_title_hide_guide_text = sprintf( __( 'When checked, the title will appear on the hero section, replacing the blog title you have set from <em>Customize &rarr; Titles &rarr; General &rarr; Blog title</em>, currently set to: <em>%s</em>.', 'napoleon' ), get_theme_mod( 'title_blog', __( 'From the blog', 'napoleon' ) ) );
	}

	$page_title_hide_default    = apply_filters( 'napoleon_hero_page_title_hide_default', $page_title_hide_default, get_post_type( $object->ID ), $object->ID );
	$page_title_hide_guide_text = apply_filters( 'napoleon_hero_page_title_hide_guide_text', $page_title_hide_guide_text, get_post_type( $object->ID ), $object->ID, $page_title_hide_default );

	napoleon_metabox_open_tab( esc_html__( 'Hero section', 'napoleon' ) );

		if ( ! $support['required'] ) {
			$options = array(
				'inherit' => esc_html__( 'Default', 'napoleon' ),
				'1'       => esc_html__( 'Show', 'napoleon' ),
				'0'       => esc_html__( 'Hide', 'napoleon' ),
			);
			napoleon_metabox_dropdown( 'hero_show', $options, esc_html__( 'Show hero section.', 'napoleon' ), array( 'default' => 'inherit' ) );
		}

		napoleon_metabox_dropdown( 'hero_text_align', napoleon_get_text_align_choices(), esc_html__( 'Title / subtitle alignment:', 'napoleon' ), array( 'default' => $support['text-align'] ) );

		napoleon_metabox_guide( wp_kses( $page_title_hide_guide_text, napoleon_get_allowed_tags( 'guide' ) ) );
		napoleon_metabox_checkbox( 'page_title_hide', 1, esc_html__( 'Hide page title.', 'napoleon' ), array( 'default' => $page_title_hide_default ) );

		?><p class="at-field-group at-field-input"><?php
			napoleon_metabox_input( 'hero_bg_color', esc_html__( 'Background Color:', 'napoleon' ), array( 'input_class' => 'napoleon-color-picker widefat', 'before' => '', 'after' => '' ) );
		?></p><?php
		?><p class="at-field-group at-field-input"><?php
			napoleon_metabox_input( 'hero_text_color', esc_html__( 'Text Color:', 'napoleon' ), array( 'input_class' => 'napoleon-color-picker widefat', 'before' => '', 'after' => '' ) );
		?></p><?php
		?><p class="at-field-group at-field-input"><?php
			napoleon_metabox_input( 'hero_overlay_color', esc_html__( 'Overlay Color:', 'napoleon' ), array( 'input_class' => 'napoleon-alpha-color-picker widefat', 'before' => '', 'after' => '' ) );
		?></p><?php

		napoleon_metabox_guide( array(
			wp_kses( __( 'The following image options are only applicable when a Hero image is selected.', 'napoleon' ), napoleon_get_allowed_tags( 'guide' ) ),
		) );

		$hero_image_id = get_post_meta( $object->ID, 'hero_image_id', true );
		?>
		<div class="at-field-group at-field-input">
			<label for="header_image_id"><?php esc_html_e( 'Hero image:', 'napoleon' ); ?></label>
			<div class="at-upload-preview">
				<div class="upload-preview">
					<?php if ( ! empty( $hero_image_id ) ) : ?>
						<?php
							$image_url = wp_get_attachment_image_url( $hero_image_id, 'napoleon_featgal_small_thumb' );
							echo sprintf( '<img src="%s" /><a href="#" class="close media-modal-icon" title="%s"></a>',
								esc_url( $image_url ),
								esc_attr__( 'Remove image', 'napoleon' )
							);
						?>
					<?php endif; ?>
				</div>
				<input name="hero_image_id" type="hidden" class="at-uploaded-id" value="<?php echo esc_attr( $hero_image_id ); ?>" />
				<input id="hero_image_id" type="button" class="button at-media-button" value="<?php esc_attr_e( 'Select Image', 'napoleon' ); ?>" />
			</div>
		</div>
		<?php

		napoleon_metabox_dropdown( 'hero_image_repeat', napoleon_get_image_repeat_choices(), esc_html__( 'Image repeat:', 'napoleon' ), array( 'default' => 'no-repeat' ) );
		napoleon_metabox_dropdown( 'hero_image_position_x', napoleon_get_image_position_x_choices(), esc_html__( 'Image horizontal position:', 'napoleon' ), array( 'default' => 'center' ) );
		napoleon_metabox_dropdown( 'hero_image_position_y', napoleon_get_image_position_y_choices(), esc_html__( 'Image vertical position:', 'napoleon' ), array( 'default' => 'center' ) );
		napoleon_metabox_dropdown( 'hero_image_attachment', napoleon_get_image_attachment_choices(), esc_html__( 'Image attachment:', 'napoleon' ), array( 'default' => 'scroll' ) );
		napoleon_metabox_checkbox( 'hero_image_cover', 1, esc_html__( 'Scale the image to cover its container.', 'napoleon' ), array( 'default' => 1 ) );
		?><?php

	napoleon_metabox_close_tab();
}
