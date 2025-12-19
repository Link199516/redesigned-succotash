<?php
if ( ! class_exists( 'AT_Widget_Home_Post_Type_Items' ) ) :

	class AT_Widget_Home_Post_Type_Items extends WP_Widget {

		public $ajax_posts = 'napoleon_home_post_type_items_widget_post_type_ajax_get_posts';

		protected $defaults = array(
			'title'     => '',
			'subtitle'  => '',
			'post_type' => 'post',
			'rows'      => array(),
			'columns'   => 3,
			'carousel'  => 0,
		);

		public function __construct() {
			$widget_ops  = array( 'description' => esc_html__( 'Homepage widget. Displays a hand-picked selection of posts from a selected post type.', 'napoleon' ) );
			$control_ops = array();
			parent::__construct( 'at-home-post-type-items', esc_html__( 'Theme (home) - Post Type Items', 'napoleon' ), $widget_ops, $control_ops );

			if ( is_admin() === true ) {
				add_action( 'wp_ajax_' . $this->ajax_posts, 'AT_Widget_Home_Post_Type_Items::ajax_get_posts' );
			}

		}

		public function widget( $args, $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$id            = isset( $args['id'] ) ? $args['id'] : '';
			$before_widget = $args['before_widget'];
			$after_widget  = $args['after_widget'];

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			$subtitle  = $instance['subtitle'];
			$post_type = $instance['post_type'];
			$rows      = $instance['rows'];
			$columns   = $instance['columns'];
			$carousel  = $instance['carousel'];

			if ( 1 === intval( $columns ) ) {
				$carousel = false;
			}

			if ( empty( $post_type ) || empty( $rows ) ) {
				return;
			}

			$ids = wp_list_pluck( $rows, 'post_id' );
			$ids = array_filter( $ids );

			$q = new WP_Query( array(
				'post_type'      => $post_type,
				'posts_per_page' => - 1,
				'post__in'       => $ids,
				'orderby'        => 'post__in',
			) );

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
					echo wp_kses( $args['before_title'] . $title . $args['after_title'], napoleon_get_allowed_sidebar_wrappers() );
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

			$slider_class = '';
			if ( $carousel ) {
				$slider_class = 'row-slider';
			}

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
			$instance['post_type'] = in_array( $new_instance['post_type'], $this->get_available_post_types( 'names' ), true ) ? $new_instance['post_type'] : $this->defaults['post_type'];
			$instance['rows']      = $this->sanitize_instance_rows( $new_instance );
			$instance['columns']   = absint( $new_instance['columns'] );
			$instance['carousel']  = isset( $new_instance['carousel'] );

			return $instance;
		}

		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$title     = $instance['title'];
			$subtitle  = $instance['subtitle'];
			$post_type = $instance['post_type'];
			$rows      = $instance['rows'];
			$columns   = $instance['columns'];
			$carousel  = $instance['carousel'];

			$post_types       = $this->get_available_post_types();
			$row_post_id_name = $this->get_field_name( 'row_post_id' ) . '[]';
			?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'napoleon' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" /></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>"><?php esc_html_e( 'Subtitle:', 'napoleon' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'subtitle' ) ); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>" class="widefat" /></p>

			<p data-ajaxposts="<?php echo esc_attr( $this->ajax_posts ); ?>">
				<label for="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>"><?php esc_html_e( 'Post type:', 'napoleon' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>" class="widefat napoleon-post-type-select">
					<?php foreach ( $post_types as $key => $pt ) {
						?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $post_type ); ?>><?php echo esc_html( $pt->labels->name ); ?></option><?php
					} ?>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>"><?php esc_html_e( 'Output Columns:', 'napoleon' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>">
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

			<p><?php esc_html_e( 'Add as many items as you want by pressing the "Add Item" button. Remove any item by selecting "Remove me".', 'napoleon' ); ?></p>
			<fieldset class="at-repeating-fields">
				<div class="inner">
					<?php
						if ( ! empty( $rows ) ) {
							$count = count( $rows );
							for ( $i = 0; $i < $count; $i ++ ) {
								?>
								<div class="post-field">
									<label class="post-field-item" data-value="<?php echo esc_attr( $rows[ $i ]['post_id'] ); ?>"><?php esc_html_e( 'Item:', 'napoleon' ); ?>
										<?php
											napoleon_dropdown_posts( array(
												'post_type'            => $post_type,
												'selected'             => $rows[ $i ]['post_id'],
												'class'                => 'widefat posts_dropdown',
												'show_option_none'     => '&nbsp;',
												'select_even_if_empty' => true,
											), $row_post_id_name );
										?>
									</label>

									<p class="at-repeating-remove-action"><a href="#" class="button at-repeating-remove-field"><i class="dashicons dashicons-dismiss"></i><?php esc_html_e( 'Remove me', 'napoleon' ); ?></a></p>
								</div>
								<?php
							}
						}
					?>
					<?php
					//
					// Add an empty and hidden set for jQuery
					//
					?>
					<div class="post-field field-prototype" style="display: none;">
						<label class="post-field-item"><?php esc_html_e( 'Item:', 'napoleon' ); ?>
							<?php
								napoleon_dropdown_posts( array(
									'post_type'            => $post_type,
									'class'                => 'widefat posts_dropdown',
									'show_option_none'     => '&nbsp;',
									'select_even_if_empty' => true,
								), $row_post_id_name );
							?>
						</label>

						<p class="at-repeating-remove-action"><a href="#" class="button at-repeating-remove-field"><i class="dashicons dashicons-dismiss"></i><?php esc_html_e( 'Remove me', 'napoleon' ); ?></a></p>
					</div>
				</div>
				<a href="#" class="at-repeating-add-field button"><i class="dashicons dashicons-plus-alt"></i><?php esc_html_e( 'Add Item', 'napoleon' ); ?></a>
			</fieldset>

			<?php
		}

		protected function sanitize_instance_rows( $instance ) {
			if ( empty( $instance ) || ! is_array( $instance ) ) {
				return array();
			}

			$ids = $instance['row_post_id'];

			$count = count( $ids );

			$new_fields = array();

			$records_count = 0;

			for ( $i = 0; $i < $count; $i++ ) {
				if ( empty( $ids[ $i ] ) ) {
					continue;
				}

				$new_fields[ $records_count ]['post_id'] = ! empty( $ids[ $i ] ) ? intval( $ids[ $i ] ) : '';

				$records_count++;
			}
			return $new_fields;
		}


		protected function get_available_post_types( $return = 'objects' ) {
			$return = in_array( $return, array( 'objects', 'names' ), true ) ? $return : 'objects';

			$post_types = get_post_types( array(
				'public' => true,
			), $return );

			unset( $post_types['attachment'] );

			$post_types = apply_filters( 'napoleon_widget_post_types_dropdown', $post_types, __CLASS__ );

			return $post_types;
		}

		public static function ajax_get_posts() {
			check_ajax_referer( 'napoleon-post-type-items', 'nonce' );

			$post_type_name = isset( $_POST['post_type_name'] ) ? sanitize_key( wp_unslash( $_POST['post_type_name'] ) ) : 'post';
			$name_field     = isset( $_POST['name_field'] ) ? sanitize_text_field( wp_unslash( $_POST['name_field'] ) ) : '';

			napoleon_dropdown_posts( array(
				'echo'                 => true,
				'post_type'            => $post_type_name,
				'class'                => 'widefat posts_dropdown',
				'show_option_none'     => '&nbsp;',
				'select_even_if_empty' => true,
			), $name_field );

			die;
		}

	}

endif;
