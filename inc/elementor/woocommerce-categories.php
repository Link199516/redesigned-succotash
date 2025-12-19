<?php
namespace Elementor;

class Widget_WooCommerce_Categories extends \Elementor\Widget_Base {

	public function get_name() {
		return 'woocommerce_categories';
	}

	public function get_title() {
		return __( 'WooCommerce Categories', 'napoleon' );
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
				'label' => __( 'WooCommerce Categories', 'napoleon' ),
			]
		);

		$this->add_control(
			'html_msg',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Displays a hand-picked selection of WooCommerce categories.', 'napoleon' ),
				'content_classes' => 'at-description',
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => __( 'Title:', 'napoleon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your title', 'napoleon' ),
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label'       => __( 'Subtitle:', 'napoleon' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your subtitle', 'napoleon' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __( 'Layout', 'napoleon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => __( 'Layout #1 (2 Categories)', 'napoleon' ),
					'2' => __( 'Layout #2 (3 Categories)', 'napoleon' ),
					'3' => __( 'Layout #3 (3 Categories)', 'napoleon' ),
					'4' => __( 'Layout #4 (3 Categories)', 'napoleon' ),
					'5' => __( 'Layout #5 (4 Categories)', 'napoleon' ),
					'6' => __( 'Layout #6 (1 Category)', 'napoleon' ),
					'7' => __( 'Layout #7 (1 Category)', 'napoleon' ),
					'8' => __( 'Layout #8 (3 Categories)', 'napoleon' ),
					'9' => __( 'Layout #9 (4 Categories)', 'napoleon' ),
				],
			]
		);

		$this->add_control(
			'product_categories',
			[
				'label'    => __( 'Product Categories', 'napoleon' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => $this->get_product_categories(),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Latest Posts Element Styles', 'napoleon' ),
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
				//	'type'  => Scheme_Color::get_type(),
				//	'value' => Scheme_Color::COLOR_3,
				//],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		$title    = $settings['title'];
		$subtitle = $settings['subtitle'];
		$layout   = $settings['layout'];
		$cat_ids  = $settings['product_categories'] ? array_map( 'intval', $settings['product_categories'] ) : [];

		if ( $title || $subtitle ) {
			?>
			<div class="section-heading">
				<div class="section-heading-content">
			<?php

			if ( $title ) {
				echo '<h2 class="section-title">' . esc_html( $title ) . '</h2>';
			}

			if ( $subtitle ) {
				echo '<p class="section-subtitle">' . esc_html( $subtitle ) . '</p>';
			}

			?>
				</div>

			</div>
			<?php
		}

		napoleon_get_template_part( 'template-parts/categories/layout', $layout, array(
			'term_ids' => $cat_ids,
		) );

		?>
		<script>
			jQuery(document).ready(function(){
				jQuery(document).trigger('elementor/render/woocommerce_categories','#at-wc-<?php echo esc_attr( $this->get_id() ); ?>');
			});
		</script>
		<?php
	}

	protected function get_product_categories() {
		$product_cats = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
		) );

		if ( empty( $product_cats ) ) {
			return;
		}

		$cat_array = [];

		foreach ( $product_cats as $key => $category ) {
			$cat_array[ $category->term_id ] = $category->name;
		}

		return $cat_array;
	}
}
