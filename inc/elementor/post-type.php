<?php
namespace Elementor;

class Widget_Post_Type extends \Elementor\Widget_Base {

	public function get_name() {
		return 'post_type_widget';
	}

	public function get_title() {
		return __( 'napoleon Post Type', 'napoleon' );
	}

	public function get_icon() {
		return 'eicon-wordpress';
	}

	public function get_categories() {
		return [ 'napoleon-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'napoleon Post Type', 'napoleon' ),
			]
		);

		$this->add_control(
			'html_msg',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Display any post type item from napoleon by first selecting the post type and then the item itself.', 'napoleon' ),
				'content_classes' => 'at-description',
			]
		);

		$this->add_control(
			'post_types',
			[
				'label'   => __( 'Post Type', 'napoleon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => napoleon_get_available_post_types(),
			]
		);

		$this->add_control(
			'selected_post',
			[
				'label'   => __( 'Post', 'napoleon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => '',
			]
		);

		$this->add_control(
			'view',
			[
				'label'   => __( 'View', 'napoleon' ),
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Post Type Element Styles', 'napoleon' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Text Color', 'napoleon' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => 'color: {{VALUE}};',
				],
				//'scheme'    => [
				//	'type'  =>  \Elementor\Core\Schemes\Color::get_type(), 
				//	'value' =>  \Elementor\Core\Schemes\Color::COLOR_3,
				//	],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		if ( empty( $settings['selected_post'] ) ) {
			return;
		}

		$post_id = $settings['selected_post'];

		$q = new \WP_Query( array(
			'post_type' => get_post_type( $post_id ),
			'p'         => $post_id,
		) );

		while ( $q->have_posts() ) : $q->the_post();
			get_template_part( 'template-parts/widgets/home-item', get_post_type() );
		endwhile;

		wp_reset_postdata();
	}

}
