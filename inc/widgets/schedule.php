<?php
if ( ! class_exists( 'AT_Widget_Schedule' ) ) :

	class AT_Widget_Schedule extends WP_Widget {

		protected $defaults = array(
			'title'           => '',
			'text'            => '',
			'schedule_fields' => array(),
		);

		public function __construct() {
			$widget_ops  = array( 'description' => esc_html__( 'Display a schedule.', 'napoleon' ) );
			$control_ops = array();
			parent::__construct( 'at-schedule', esc_html__( 'Theme - Schedule', 'napoleon' ), $widget_ops, $control_ops );
		}

		public function widget( $args, $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$id            = isset( $args['id'] ) ? $args['id'] : '';
			$before_widget = $args['before_widget'];
			$after_widget  = $args['after_widget'];

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			$text   = $instance['text'];
			$fields = $instance['schedule_fields'];

			echo wp_kses( $before_widget, napoleon_get_allowed_sidebar_wrappers() );

			if ( $title ) {
				echo wp_kses( $args['before_title'] . $title . $args['after_title'], napoleon_get_allowed_sidebar_wrappers() );
			}

			if ( $text ) {
				?><p class="at-schedule-widget-intro"><?php echo do_shortcode( wp_kses_post( $text ) ); ?></p><?php
			}

			if ( $fields ) {
				?><table class="at-schedule-widget-table"><tbody><?php

				foreach ( $fields as $field ) {
					$day  = $field['day'] ? $field['day'] : '&nbsp;';
					$time = $field['time'] ? $field['time'] : '&nbsp;';
					?>
					<tr>
						<th><?php echo esc_html( $day ); ?></th>
						<td><?php echo esc_html( $time ); ?></td>
					</tr>
					<?php
				}

				?></tbody></table><?php
			}

			echo wp_kses( $after_widget, napoleon_get_allowed_sidebar_wrappers() );

		} // widget

		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['title']           = sanitize_text_field( $new_instance['title'] );
			$instance['text']            = wp_kses_post( $new_instance['text'] );
			$instance['schedule_fields'] = $this->sanitize_schedule_fields( $new_instance );

			return $instance;
		}

		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$title  = $instance['title'];
			$text   = $instance['text'];
			$fields = $instance['schedule_fields'];

			$field_day_name  = $this->get_field_name( 'schedule_field_day' ) . '[]';
			$field_time_name = $this->get_field_name( 'schedule_field_time' ) . '[]';
			?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'napoleon' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" /></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Text (accepts HTML):', 'napoleon' ); ?></label><textarea id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" class="widefat"><?php echo esc_textarea( $text ); ?></textarea></p>

			<p><?php esc_html_e( 'Add as many items as you want by pressing the "Add Item" button. Remove any item by selecting "Remove me".', 'napoleon' ); ?></p>
			<fieldset class="at-repeating-fields">
				<div class="inner">
					<?php
						if ( ! empty( $fields ) ) {
							$count = count( $fields );
							for ( $i = 0; $i < $count; $i++ ) {
								?>
								<div class="post-field">
									<label class="post-field-item"><?php esc_html_e( 'Day:', 'napoleon' ); ?>
										<input type="text" name="<?php echo esc_attr( $field_day_name ); ?>" value="<?php echo esc_attr( $fields[ $i ]['day'] ); ?>" class="widefat" />
									</label>

									<label class="post-field-item"><?php esc_html_e( 'Time:', 'napoleon' ); ?>
										<input type="text" name="<?php echo esc_attr( $field_time_name ); ?>" value="<?php echo esc_attr( $fields[ $i ]['time'] ); ?>" class="widefat" />
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
						<label class="post-field-item"><?php esc_html_e( 'Day:', 'napoleon' ); ?>
							<input type="text" name="<?php echo esc_attr( $field_day_name ); ?>" value="" class="widefat" />
						</label>

						<label class="post-field-item"><?php esc_html_e( 'Time:', 'napoleon' ); ?>
							<input type="text" name="<?php echo esc_attr( $field_time_name ); ?>" value="" class="widefat" />
						</label>

						<p class="at-repeating-remove-action"><a href="#" class="button at-repeating-remove-field"><i class="dashicons dashicons-dismiss"></i><?php esc_html_e( 'Remove me', 'napoleon' ); ?></a></p>
					</div>
				</div>
				<a href="#" class="at-repeating-add-field button"><i class="dashicons dashicons-plus-alt"></i><?php esc_html_e( 'Add Item', 'napoleon' ); ?></a>
			</fieldset>

			<?php
		} // form

		protected function sanitize_schedule_fields( $instance ) {
			if ( empty( $instance ) || ! is_array( $instance ) ) {
				return array();
			}

			$days  = $instance['schedule_field_day'];
			$times = $instance['schedule_field_time'];

			$count = max( count( $days ), count( $times ) );

			$new_fields = array();

			$records_count = 0;

			for ( $i = 0; $i < $count; $i++ ) {
				if ( empty( $days[ $i ] ) && empty( $times[ $i ] ) ) {
					continue;
				}

				$new_fields[ $records_count ]['day']  = sanitize_text_field( $days[ $i ] );
				$new_fields[ $records_count ]['time'] = sanitize_text_field( $times[ $i ] );

				$records_count++;
			}
			return $new_fields;
		}
	}

endif;
