<?php
if ( ! class_exists( 'AT_Widget_Home_Latest_Posts' ) ) :

	class AT_Widget_Home_Latest_Posts extends WP_Widget {

		protected $defaults = array(
			'title'     => '',
			'subtitle'  => '',
			'post_type' => 'post',
			'taxonomy'  => 'category',
			'term_id'   => '',
			'random'    => false,
			'count'     => 3,
			'columns'   => 3,
			'carousel'  => 0,
		);

		public function __construct() {
			$widget_ops  = array( 'description' => __( 'Homepage widget. Displays a number of the latest (or random) posts, optionally from a specific category.', 'napoleon' ) );
			$control_ops = array();
			parent::__construct( 'at-home-latest-posts', esc_html__( 'Theme (home) - Latest Posts', 'napoleon' ), $widget_ops, $control_ops );
		}


		public function widget( $args, $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$id            = isset( $args['id'] ) ? $args['id'] : '';
			$before_widget = $args['before_widget'];
			$after_widget  = $args['after_widget'];

			$title     = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$subtitle  = $instance['subtitle'];
			$post_type = $instance['post_type'];
			$taxonomy  = $instance['taxonomy'];
			$term_id   = $instance['term_id'];
			$random    = $instance['random'];
			$count     = $instance['count'];
			$columns   = $instance['columns'];
			$carousel  = $instance['carousel'];

			if ( 1 === intval( $columns ) ) {
				$carousel = false;
			}

			if ( 0 === $count ) {
				return;
			}

			echo wp_kses( $before_widget, napoleon_get_allowed_sidebar_wrappers() );


			if ( in_array( $id, napoleon_get_fullwidth_sidebars(), true ) ) {
				?>
				<div class="container">
					<div class="row">
						<div class="col-12">
				<?php
			}

			if ( $title || $subtitle ) {
				?>
				<div class="section-heading">
					<div class="section-heading-content">
				<?php

				if ( $title ) {
					echo wp_kses( $args['before_title'] .$title . $args['after_title'], napoleon_get_allowed_sidebar_wrappers() );
				}

				if ( $subtitle ) {
					?><p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p><?php
				}

				?>
					</div>

					<?php if ( $carousel ) : ?>
						<div class="row-slider-nav"></div>
					<?php endif; ?>

				</div>
				<?php
			}

			echo do_shortcode( sprintf( '[latest-post-type post_type="%1$s" count="%2$s" columns="%3$s" random="%4$s" carousel="%5$s" taxonomy="%6$s" term_ids="%7$s"]',
				$post_type,
				$count,
				$columns,
				$random,
				$carousel,
				$taxonomy,
				$term_id
			) );


			if ( in_array( $id, napoleon_get_fullwidth_sidebars(), true ) ) {
				?>
						</div>
					</div>
				</div>
				<?php
			}

			echo wp_kses( $after_widget, napoleon_get_allowed_sidebar_wrappers() );

		}

		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['title']     = sanitize_text_field( $new_instance['title'] );
			$instance['subtitle']  = sanitize_text_field( $new_instance['subtitle'] );
			$instance['post_type'] = $this->defaults['post_type'];
			$instance['taxonomy']  = $this->defaults['taxonomy'];
			$instance['term_id']   = napoleon_sanitize_intval_or_empty( $new_instance['term_id'] );
			$instance['random']    = isset( $new_instance['random'] );
			$instance['count']     = absint( $new_instance['count'] );
			$instance['columns']   = absint( $new_instance['columns'] );
			$instance['carousel']  = isset( $new_instance['carousel'] );

			return $instance;
		} // save

		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$title    = $instance['title'];
			$subtitle = $instance['subtitle'];
			$taxonomy = $instance['taxonomy'];
			$term_id  = $instance['term_id'];
			$random   = $instance['random'];
			$count    = $instance['count'];
			$columns  = $instance['columns'];
			$carousel = $instance['carousel'];

			?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'napoleon' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" /></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>"><?php esc_html_e( 'Subtitle:', 'napoleon' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'subtitle' ) ); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>" class="widefat" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'term_id' ) ); ?>"><?php esc_html_e( 'Category to display the latest posts from (optional):', 'napoleon' ); ?></label>
			<?php wp_dropdown_categories( array(
				'taxonomy'          => $taxonomy,
				'show_option_all'   => '',
				'show_option_none'  => ' ',
				'option_none_value' => '',
				'show_count'        => 1,
				'echo'              => 1,
				'selected'          => $term_id,
				'hierarchical'      => 1,
				'name'              => $this->get_field_name( 'term_id' ),
				'id'                => $this->get_field_id( 'term_id' ),
				'class'             => 'postform widefat',
			) ); ?>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'random' ) ); ?>"><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'random' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'random' ) ); ?>" value="1" <?php checked( $random, 1 ); ?> /><?php esc_html_e( 'Show random posts.', 'napoleon' ); ?></label></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of posts to show:', 'napoleon' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" min="1" step="1" value="<?php echo esc_attr( $count ); ?>" class="widefat"/></p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>"><?php esc_html_e( 'Output Columns:', 'napoleon' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>" class="widefat">
					<?php
						$col_options = napoleon_post_type_listing_get_valid_columns_options();
						foreach ( $col_options['range'] as $col ) {
							echo sprintf( '<option value="%s" %s>%s</option>',
								esc_attr( $col ),
								selected( $columns, $col, false ),
								/* translators: %d is a number of columns. */
								esc_html( sprintf( _n( '%d Column', '%d Columns', $col, 'napoleon' ), $col ) )
							);
						}
					?>
				</select>
			</p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>"><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'carousel' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>" value="1" <?php checked( $carousel, 1 ); ?> /><?php esc_html_e( 'Show items as a carousel.', 'napoleon' ); ?></label></p>
			<?php
		}

	}

endif;
