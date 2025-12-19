<?php
if ( ! class_exists( 'AT_Widget_Socials' ) ) :

	class AT_Widget_Socials extends WP_Widget {

		protected $defaults = array(
			'title' => '',
		);

		public function __construct() {
			$widget_ops  = array( 'description' => esc_html__( "Displays the site's social icons.", 'napoleon' ) );
			$control_ops = array();
			parent::__construct( 'at-socials', esc_html__( 'Theme - Social Icons', 'napoleon' ), $widget_ops, $control_ops );
		}

		public function widget( $args, $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			echo wp_kses( $args['before_widget'], napoleon_get_allowed_sidebar_wrappers() );

			if ( $title ) {
				echo wp_kses( $args['before_title'] . $title . $args['after_title'], napoleon_get_allowed_sidebar_wrappers() );
			}

			napoleon_the_social_icons();

			echo wp_kses( $args['after_widget'], napoleon_get_allowed_sidebar_wrappers() );
		} // widget

		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['title'] = sanitize_text_field( $new_instance['title'] );

			return $instance;
		}

		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$title = $instance['title'];

			?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'napoleon' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" /></p>
			<p><?php echo wp_kses( __( "This widget displays your site's social icons. In order to set them up, you need to visit <strong>Appearance > Customize > Social Networks</strong> and provide the appropriate URLs where desired.", 'napoleon' ), array( 'strong' => array() ) ); ?></p>
			<?php
		} // form

	}

endif;
