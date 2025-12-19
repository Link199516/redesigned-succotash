<?php
function napoleon_essential_post_type_listing_get_valid_columns_options( $post_type = false ) {
	if ( function_exists( 'napoleon_post_type_listing_get_valid_columns_options' ) ) {
		return napoleon_post_type_listing_get_valid_columns_options( $post_type );
	} else {
		return array(
			'min'   => 3,
			'max'   => 3,
			'range' => array( 3 ),
		);
	}
}

/**
 * Returns the taxonomy that should be used for listing template, for a given post type.
 *
 * @param $post_type
 *
 * @return bool|string Returns false for no supported taxonomy, and a taxonomy name otherwise.
 */
function napoleon_post_type_listing_taxonomy( $post_type ) {
	$taxonomy = false;
	switch ( $post_type ) {
		case 'post':
			$taxonomy = 'category';
			break;
		case 'page':
			$taxonomy = false;
			break;
		default:
			$taxonomy = "{$post_type}_category";
	}

	return apply_filters( 'napoleon_post_type_listing_taxonomy', $taxonomy, $post_type );
}

/**
 * Sanitize post meta, as displayed by napoleon_print_metabox_tab_post_type_listing()
 *
 * @param $post_type string
 * @param $post_id int
 */
function napoleon_sanitize_metabox_tab_post_type_listing( $post_type, $post_id ) {
	// Ignore phpcs issues. nonce validation happens inside napoleon_can_save_meta(), from the caller of this function.
	// @codingStandardsIgnoreStart

	$taxonomy = napoleon_post_type_listing_taxonomy( $post_type );
	$base_category = isset( $_POST["{$post_type}_listing_base_category"] ) ? intval( $_POST["{$post_type}_listing_base_category"] ) : 0 ;
	if ( $taxonomy && $base_category > 0 && term_exists( $base_category, $taxonomy ) ) {
		update_post_meta( $post_id, "{$post_type}_listing_base_category", $base_category );
	} else {
		update_post_meta( $post_id, "{$post_type}_listing_base_category", 0 );
	}

	update_post_meta( $post_id, "{$post_type}_listing_loading_effect", napoleon_sanitize_grid_loading_effect( $_POST["{$post_type}_listing_loading_effect"] ) );
	if ( apply_filters( 'napoleon_post_type_listing_spacing_support', true, $post_type ) ) {
		update_post_meta( $post_id, "{$post_type}_listing_spacing", napoleon_sanitize_grid_spacing( $_POST["{$post_type}_listing_spacing"] ) );
	}
	update_post_meta( $post_id, "{$post_type}_listing_columns", intval( $_POST["{$post_type}_listing_columns"] ) );
	update_post_meta( $post_id, "{$post_type}_listing_masonry", isset( $_POST["{$post_type}_listing_masonry"] ) ? 1 : 0 );
	update_post_meta( $post_id, "{$post_type}_listing_isotope", isset( $_POST["{$post_type}_listing_isotope"] ) ? 1 : 0 );
	update_post_meta( $post_id, "{$post_type}_listing_posts_per_page", intval( $_POST["{$post_type}_listing_posts_per_page"] ) );
	// @codingStandardsIgnoreEnd
}

function napoleon_print_metabox_tab_post_type_listing( $post_type, $object, $box, $title = '' ) {
	napoleon_metabox_open_tab( $title );
		$taxonomy = napoleon_post_type_listing_taxonomy( $post_type );

		if ( $taxonomy ) {
			napoleon_metabox_guide( wp_kses( __( "Select a base category. Only items from the selected category will be displayed. If you don't select one (i.e. empty) items from all categories will be shown.", 'napoleon' ), napoleon_get_allowed_tags( 'guide' ) ) );
			?><p class="at-field-group at-field-dropdown"><label for="<?php echo esc_attr( "{$post_type}_listing_base_category" ); ?>"><?php esc_html_e( 'Base category:', 'napoleon' ); ?></label> <?php
			$category = get_post_meta( $object->ID, "{$post_type}_listing_base_category", true );
			wp_dropdown_categories( array(
				'selected'          => $category,
				'id'                => "{$post_type}_listing_base_category",
				'name'              => "{$post_type}_listing_base_category",
				'show_option_none'  => ' ',
				'option_none_value' => 0,
				'taxonomy'          => $taxonomy,
				'hierarchical'      => 1,
				'show_count'        => 1,
				'hide_empty'        => 0,
			) );
			?></p><?php
		}

		napoleon_metabox_dropdown( "{$post_type}_listing_loading_effect", napoleon_get_grid_loading_effect_choices(), esc_html__( 'Grid loading effect:', 'napoleon' ) );

		if ( apply_filters( 'napoleon_post_type_listing_spacing_support', true, $post_type ) ) {
			napoleon_metabox_dropdown( "{$post_type}_listing_spacing", napoleon_get_grid_spacing_choices(), esc_html__( 'Grid spacing:', 'napoleon' ) );
		}

		$options     = array();
		$col_options = napoleon_essential_post_type_listing_get_valid_columns_options( $post_type );
		foreach ( $col_options['range'] as $col ) {
			/* translators: %d is a number of columns. */
			$options[ $col ] = sprintf( _n( '%d Column', '%d Columns', $col, 'napoleon' ), $col );
		}
		napoleon_metabox_dropdown( "{$post_type}_listing_columns", $options, esc_html__( 'Listing columns:', 'napoleon' ) );
		napoleon_metabox_checkbox( "{$post_type}_listing_masonry", 1, esc_html__( 'Masonry effect.', 'napoleon' ) );
		napoleon_metabox_checkbox( "{$post_type}_listing_isotope", 1, wp_kses( __( 'Enable category filters (ignores <em>Items per page</em> setting).', 'napoleon' ), napoleon_get_allowed_tags( 'guide' ) ) );
		/* translators: %d is the current number of posts per page option. */
		napoleon_metabox_guide( wp_kses( sprintf( __( 'Set the number of items per page that you want to display. Setting this to <strong>-1</strong> will show <strong>all items</strong>, while setting it to zero or leaving it empty, will follow the global option set from <em>Settings -> Reading</em>, currently set to <strong>%d items per page</strong>.', 'napoleon' ), get_option( 'posts_per_page' ) ), napoleon_get_allowed_tags( 'guide' ) ) );
		napoleon_metabox_input( "{$post_type}_listing_posts_per_page", esc_html__( 'Items per page:', 'napoleon' ), array( 'input_type' => 'number' ) );
	napoleon_metabox_close_tab();
}

function napoleon_get_grid_loading_effect_choices() {
	return apply_filters( 'napoleon_grid_loading_effect_choices', napoleon_get_loading_effect_choices() );
}

function napoleon_sanitize_grid_loading_effect( $value ) {
	$choices = napoleon_get_grid_loading_effect_choices();
	if ( array_key_exists( $value, $choices ) ) {
		return $value;
	}

	return apply_filters( 'napoleon_sanitize_grid_loading_effect_default', '' );
}

function napoleon_get_grid_spacing_choices() {
	return apply_filters( 'napoleon_grid_spacing_choices', array(
		''           => esc_html__( 'With gutters', 'napoleon' ),
		'no-gutters' => esc_html__( 'No gutters', 'napoleon' ),
	) );
}

function napoleon_sanitize_grid_spacing( $value ) {
	$choices = napoleon_get_grid_spacing_choices();
	if ( array_key_exists( $value, $choices ) ) {
		return $value;
	}

	return apply_filters( 'napoleon_sanitize_grid_spacing_default', '' );
}
