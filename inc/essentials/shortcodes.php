<?php
add_shortcode( 'latest-post-type', 'napoleon_shortcode_latest_post_type' );
if ( ! function_exists( 'napoleon_shortcode_latest_post_type' ) ) :
	function napoleon_shortcode_latest_post_type( $params,  $shortcode, $content = null ) {

		$params = shortcode_atts( array(
			'post_type' => 'post',
			'random'    => false,
			'count'     => 3,
			'columns'   => 3,
			'carousel'  => false,
			'taxonomy'  => 'category',
			'term_ids'  => '',
		), $params, $shortcode );

		$post_type = $params['post_type'];
		$random    = $params['random'];
		$count     = intval( $params['count'] );
		$columns   = intval( $params['columns'] );
		$carousel  = $params['carousel'];
		$taxonomy  = $params['taxonomy'];
		$term_ids  = $params['term_ids'];

		if ( 0 === $count ) {
			return '';
		}

		if ( empty( $post_type ) ) {
			$post_type = 'post';
		}

		if ( empty( $random ) || 'false' === $random || 'FALSE' === $random || '0' === $random || false === (bool) $random ) {
			$random = false;
		} else {
			$random = true;
		}

		if ( 'true' === $carousel || 'TRUE' === $carousel || '1' === $carousel || true === $carousel ) {
			$carousel = true;
		} else {
			$carousel = false;
		}

		$slider_class = '';
		if ( $carousel ) {
			$slider_class = 'row-slider';
		}

		$col_options = napoleon_essential_post_type_listing_get_valid_columns_options();

		if ( $columns < $col_options['min'] ) {
			$columns = $col_options['min'];
		}

		if ( $columns > $col_options['max'] ) {
			$columns = $col_options['max'];
		}

		$query_args = array(
			'post_type'           => $post_type,
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'posts_per_page'      => $count,
			'post_status'         => 'publish',
		);

		if ( $random ) {
			$query_args['orderby'] = 'rand';
			unset( $query_args['order'] );
		}

		$tax_args = array();

		if ( ! empty( $taxonomy ) && ! empty( $term_ids ) ) {
			$term_ids = explode( ',', $term_ids );
			$term_ids = array_map( 'trim', $term_ids );
			$term_ids = array_map( 'intval', $term_ids );
			$term_ids = array_filter( $term_ids );

			if ( ! empty( $term_ids ) ) {
				$tax_args = array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => $term_ids,
					),
				);
			}
		}

		if ( ! empty( $tax_args ) ) {
			$query_args['tax_query'] = $tax_args;
		}

		$q = new WP_Query( $query_args );

		ob_start();

		if ( $q->have_posts() ) {
			?><div class="row row-items <?php echo 'columns-'.$columns; echo esc_attr( $slider_class ); ?>"><?php

				while ( $q->have_posts() ) {
					$q->the_post();

					?><div class="<?php echo esc_attr( napoleon_get_columns_classes( $columns ) ); ?>"><?php

					get_template_part( 'template-parts/widgets/home-item', get_post_type() );

					?></div><?php
				}
				wp_reset_postdata();

			?></div><?php
		}

		$output = ob_get_clean();

		return $output;
	}
endif;
