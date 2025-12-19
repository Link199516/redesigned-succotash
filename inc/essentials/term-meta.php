<?php
/*
 * Term Meta
 */

add_action( 'product_cat_add_form_fields', 'napoleon_term_meta_product_cat_add_form', 10 );
add_action( 'product_cat_edit_form_fields', 'napoleon_term_meta_product_cat_edit_form', 10, 2 );

if ( ! function_exists( 'napoleon_term_meta_product_cat_add_form' ) ) :
	function napoleon_term_meta_product_cat_add_form( $taxonomy ) {
		?>
		<div class="form-field">
			<label for="napoleon-term-meta-subtitle"><?php esc_html_e( 'Subtitle', 'napoleon' ); ?></label>
			<input type="text" id="napoleon-term-meta-subtitle" name="napoleon-term-meta-subtitle" value="" />
			<p class="description"><?php echo wp_kses( __( 'The subtitle is used by the <strong>Theme (home) - WooCommerce Categories</strong> widget.', 'napoleon' ), napoleon_get_allowed_tags( 'guide' ) ); ?></p>
		</div>


		<div class="form-field">
			<label for="napoleon-term-meta-hero-show"><?php esc_html_e( 'Show hero section', 'napoleon' ); ?></label>
			<select id="napoleon-term-meta-hero-show" name="napoleon-term-meta-hero-show">
				<?php $options = napoleon_get_hero_show_choices(); ?>
				<?php foreach ( $options as $value => $description ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, '' ); ?>><?php echo wp_kses( $description, array() ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="form-field">
			<label for="napoleon-term-meta-hero-text-color"><?php esc_html_e( 'Hero text color', 'napoleon' ); ?></label>
			<input type="text" id="napoleon-term-meta-hero-text-color" name="napoleon-term-meta-hero-text-color" class="napoleon-colorpckr" value="" />
		</div>

		<div class="form-field">
			<label for="napoleon-term-meta-hero-bg-color"><?php esc_html_e( 'Hero background color', 'napoleon' ); ?></label>
			<input type="text" id="napoleon-term-meta-hero-bg-color" name="napoleon-term-meta-hero-bg-color" class="napoleon-colorpckr" value="" />
		</div>

		<div class="form-field term-image-wrap">
			<label for="napoleon-term-meta-hero-image"><?php esc_html_e( 'Hero image', 'napoleon' ); ?></label>
			<div class="at-upload-preview">
				<div class="upload-preview"></div>
				<input type="hidden" class="at-uploaded-id" name="napoleon-term-meta-hero-image" value="" />
				<input id="napoleon-term-meta-hero-image" type="button" class="button at-media-button" value="<?php esc_attr_e( 'Select Image', 'napoleon' ); ?>" />
			</div>
		</div>

		<div class="form-field">
			<label for="napoleon-term-meta-hero-image-repeat"><?php esc_html_e( 'Hero image repeat', 'napoleon' ); ?></label>
			<select id="napoleon-term-meta-hero-image-repeat" name="napoleon-term-meta-hero-image-repeat">
				<?php $options = napoleon_get_image_repeat_choices(); ?>
				<?php foreach ( $options as $value => $description ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"><?php echo wp_kses( $description, array() ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="form-field">
			<label for="napoleon-term-meta-hero-image-position-x"><?php esc_html_e( 'Hero image horizontal position', 'napoleon' ); ?></label>
			<select id="napoleon-term-meta-hero-image-position-x" name="napoleon-term-meta-hero-image-position-x">
				<?php $options = napoleon_get_image_position_x_choices(); ?>
				<?php foreach ( $options as $value => $description ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'center' ); ?>><?php echo wp_kses( $description, array() ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="form-field">
			<label for="napoleon-term-meta-hero-image-position-y"><?php esc_html_e( 'Hero image vertical position', 'napoleon' ); ?></label>
			<select id="napoleon-term-meta-hero-image-position-y" name="napoleon-term-meta-hero-image-position-y">
				<?php $options = napoleon_get_image_position_y_choices(); ?>
				<?php foreach ( $options as $value => $description ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'center' ); ?>><?php echo wp_kses( $description, array() ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="form-field">
			<label for="napoleon-term-meta-hero-image-attachment"><?php esc_html_e( 'Hero image attachment', 'napoleon' ); ?></label>
			<select id="napoleon-term-meta-hero-image-attachment" name="napoleon-term-meta-hero-image-attachment">
				<?php $options = napoleon_get_image_attachment_choices(); ?>
				<?php foreach ( $options as $value => $description ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"><?php echo wp_kses( $description, array() ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="form-field">
			<label for="napoleon-term-meta-hero-image-cover">
				<input type="checkbox" id="napoleon-term-meta-hero-image-cover" name="napoleon-term-meta-hero-image-cover" value="1"/>
				<?php esc_html_e( 'Scale the image to cover the hero area.', 'napoleon' ); ?>
			</label>
		</div>

		<div class="form-field">
			<label for="napoleon-term-meta-hero-text-align"><?php esc_html_e( 'Hero text align', 'napoleon' ); ?></label>
			<select id="napoleon-term-meta-hero-text-align" name="napoleon-term-meta-hero-text-align">
				<?php $options = napoleon_get_text_align_choices(); ?>
				<?php foreach ( $options as $value => $description ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"><?php echo wp_kses( $description, array() ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'napoleon_term_meta_product_cat_edit_form' ) ) :
	function napoleon_term_meta_product_cat_edit_form( $term, $taxonomy ) {
		$subtitle = get_term_meta( $term->term_id, 'subtitle', true );

		$hero       = napoleon_sanitize_hero_show( get_term_meta( $term->term_id, 'hero_show', true ) );
		$color      = get_term_meta( $term->term_id, 'hero_text_color', true );
		$bg_color   = get_term_meta( $term->term_id, 'hero_bg_color', true );
		$image_id   = get_term_meta( $term->term_id, 'hero_image_id', true );
		$repeat     = napoleon_sanitize_image_repeat( get_term_meta( $term->term_id, 'hero_image_repeat', true ) );
		$position_x = napoleon_sanitize_image_position_x( get_term_meta( $term->term_id, 'hero_image_position_x', true ) );
		$position_y = napoleon_sanitize_image_position_y( get_term_meta( $term->term_id, 'hero_image_position_y', true ) );
		$attachment = napoleon_sanitize_image_attachment( get_term_meta( $term->term_id, 'hero_image_attachment', true ) );
		$cover      = get_term_meta( $term->term_id, 'hero_image_cover', true );
		$align      = get_term_meta( $term->term_id, 'hero_text_align', true );
		?>
		<tr class="form-field">
			<th scope="row"><label for="napoleon-term-meta-subtitle"><?php esc_html_e( 'Subtitle', 'napoleon' ); ?></label></th>
			<td>
				<input type="text" id="napoleon-term-meta-subtitle" name="napoleon-term-meta-subtitle" value="<?php echo esc_attr( $subtitle ); ?>" />
				<p class="description"><?php echo wp_kses( __( 'The subtitle is used by the <strong>Theme (home) - WooCommerce Categories</strong> widget.', 'napoleon' ), napoleon_get_allowed_tags( 'guide' ) ); ?></p>
			</td>
		</tr>


		<tr class="form-field">
			<th scope="row"><label for="napoleon-term-meta-hero-show"><?php esc_html_e( 'Show hero section', 'napoleon' ); ?></label></th>
			<td>
				<select id="napoleon-term-meta-hero-show" name="napoleon-term-meta-hero-show">
					<?php $options = napoleon_get_hero_show_choices(); ?>
					<?php foreach ( $options as $value => $description ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $hero, $value ); ?>><?php echo wp_kses( $description, array() ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row"><label for="napoleon-term-meta-hero-text-color"><?php esc_html_e( 'Hero text color', 'napoleon' ); ?></label></th>
			<td>
				<input type="text" id="napoleon-term-meta-hero-text-color" name="napoleon-term-meta-hero-text-color" class="napoleon-colorpckr" value="<?php echo esc_attr( $color ); ?>" />
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row"><label for="napoleon-term-meta-hero-bg-color"><?php esc_html_e( 'Hero background color', 'napoleon' ); ?></label></th>
			<td>
				<input type="text" id="napoleon-term-meta-hero-bg-color" name="napoleon-term-meta-hero-bg-color" class="napoleon-colorpckr" value="<?php echo esc_attr( $bg_color ); ?>" />
			</td>
		</tr>

		<tr class="form-field term-image-wrap">
			<th scope="row"><label for="napoleon-term-meta-hero-image"><?php esc_html_e( 'Hero image', 'napoleon' ); ?></label></th>
			<td>
				<div class="at-upload-preview">
					<div class="upload-preview">
						<?php if ( ! empty( $image_id ) ) : ?>
							<?php
								$image_url = wp_get_attachment_image_url( $image_id, 'napoleon_featgal_small_thumb' );
								echo sprintf( '<img src="%s" /><a href="#" class="close media-modal-icon" title="%s"></a>',
									esc_url( $image_url ),
									esc_attr( esc_html__( 'Remove image', 'napoleon' ) )
								);
							?>
						<?php endif; ?>
					</div>
					<input type="hidden" class="at-uploaded-id" name="napoleon-term-meta-hero-image" value="<?php echo esc_attr( $image_id ); ?>" />
					<input id="napoleon-term-meta-hero-image" type="button" class="button at-media-button" value="<?php esc_attr_e( 'Select Image', 'napoleon' ); ?>" />
				</div>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row"><label for="napoleon-term-meta-hero-image-repeat"><?php esc_html_e( 'Hero image repeat', 'napoleon' ); ?></label></th>
			<td>
				<select id="napoleon-term-meta-hero-image-repeat" name="napoleon-term-meta-hero-image-repeat">
					<?php $options = napoleon_get_image_repeat_choices(); ?>
					<?php foreach ( $options as $value => $description ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $repeat, $value ); ?>><?php echo wp_kses( $description, array() ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row"><label for="napoleon-term-meta-hero-image-position-x"><?php esc_html_e( 'Hero image horizontal position', 'napoleon' ); ?></label></th>
			<td>
				<select id="napoleon-term-meta-hero-image-position-x" name="napoleon-term-meta-hero-image-position-x">
					<?php $options = napoleon_get_image_position_x_choices(); ?>
					<?php foreach ( $options as $value => $description ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $position_x, $value ); ?>><?php echo wp_kses( $description, array() ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row"><label for="napoleon-term-meta-hero-image-position-y"><?php esc_html_e( 'Hero image vertical position', 'napoleon' ); ?></label></th>
			<td>
				<select id="napoleon-term-meta-hero-image-position-y" name="napoleon-term-meta-hero-image-position-y">
					<?php $options = napoleon_get_image_position_y_choices(); ?>
					<?php foreach ( $options as $value => $description ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $position_y, $value ); ?>><?php echo wp_kses( $description, array() ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row"><label for="napoleon-term-meta-hero-image-attachment"><?php esc_html_e( 'Hero image attachment', 'napoleon' ); ?></label></th>
			<td>
				<select id="napoleon-term-meta-hero-image-attachment" name="napoleon-term-meta-hero-image-attachment">
					<?php $options = napoleon_get_image_attachment_choices(); ?>
					<?php foreach ( $options as $value => $description ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $attachment, $value ); ?>><?php echo wp_kses( $description, array() ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row"><?php esc_html_e( 'Hero image cover', 'napoleon' ); ?></th>
			<td>
				<label for="napoleon-term-meta-hero-image-cover">
					<input type="checkbox" id="napoleon-term-meta-hero-image-cover" name="napoleon-term-meta-hero-image-cover" value="1" <?php checked( $cover, 1 ); ?>/>
					<?php esc_html_e( 'Scale the image to cover the page hero area.', 'napoleon' ); ?>
				</label>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row"><label for="napoleon-term-meta-hero-text-align"><?php esc_html_e( 'Hero text align', 'napoleon' ); ?></label></th>
			<td>
				<select id="napoleon-term-meta-hero-text-align" name="napoleon-term-meta-hero-text-align">
					<?php $options = napoleon_get_text_align_choices(); ?>
					<?php foreach ( $options as $value => $description ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $align, $value ); ?>><?php echo wp_kses( $description, array() ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php
	}
endif;


add_action( 'create_term', 'napoleon_term_created_edited', 10, 3 );
add_action( 'edit_term', 'napoleon_term_created_edited', 10, 3 );
if ( ! function_exists( 'napoleon_term_created_edited' ) ) :
	function napoleon_term_created_edited( $term_id, $tt_id, $taxonomy ) {
		$taxonomies = array(
			'product_cat',
		);

		if ( ! in_array( $taxonomy, $taxonomies, true ) ) {
			return;
		}

		// Make sure we are in the term-related.
		if ( ! isset( $_POST['napoleon-term-meta-subtitle'] ) ) {
			return;
		}

		update_term_meta( $term_id, 'subtitle', sanitize_text_field( $_POST['napoleon-term-meta-subtitle'] ) );

		update_term_meta( $term_id, 'hero_show', napoleon_sanitize_hero_show( $_POST['napoleon-term-meta-hero-show'] ) );
		update_term_meta( $term_id, 'hero_text_color', sanitize_hex_color( $_POST['napoleon-term-meta-hero-text-color'] ) );
		update_term_meta( $term_id, 'hero_bg_color', sanitize_hex_color( $_POST['napoleon-term-meta-hero-bg-color'] ) );
		update_term_meta( $term_id, 'hero_image_id', napoleon_sanitize_intval_or_empty( $_POST['napoleon-term-meta-hero-image'] ) );
		update_term_meta( $term_id, 'hero_image_repeat', napoleon_sanitize_image_repeat( $_POST['napoleon-term-meta-hero-image-repeat'] ) );
		update_term_meta( $term_id, 'hero_image_position_x', napoleon_sanitize_image_position_x( $_POST['napoleon-term-meta-hero-image-position-x'] ) );
		update_term_meta( $term_id, 'hero_image_position_y', napoleon_sanitize_image_position_y( $_POST['napoleon-term-meta-hero-image-position-y'] ) );
		update_term_meta( $term_id, 'hero_image_attachment', napoleon_sanitize_image_attachment( $_POST['napoleon-term-meta-hero-image-attachment'] ) );
		update_term_meta( $term_id, 'hero_image_cover', isset( $_POST['napoleon-term-meta-hero-image-cover'] ) ? 1 : 0 );
		update_term_meta( $term_id, 'hero_text_align', napoleon_sanitize_text_align( $_POST['napoleon-term-meta-hero-text-align'] ) );
	}
endif;
