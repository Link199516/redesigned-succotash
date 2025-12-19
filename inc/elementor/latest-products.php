<?php
namespace Elementor;

class Widget_Latest_Products extends \Elementor\Widget_Base {

	public function get_name() {
		return 'latest_products';
	}

	public function get_title() {
		return __( 'Latest Products', 'napoleon' );
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
				'label' => __( 'Latest Products', 'napoleon' ),
			]
		);

		$this->add_control(
			'html_msg',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Displays a number of the latest (or random) products, optionally from a specific product category.', 'napoleon' ),
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
			'orderby',
			[
				'label'   => __( 'Order Products By:', 'napoleon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date'       => __( 'Date', 'napoleon' ),
					'rating'     => __( 'Rating', 'napoleon' ),
					'popularity' => __( 'Popularity', 'napoleon' ),
					'rand'       => __( 'Random', 'napoleon' ),
				],
			]
		);

		$this->add_control(
			'carousel',
			[
				'label'        => __( 'Display as a carousel', 'napoleon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'napoleon' ),
				'label_off'    => __( 'No', 'napoleon' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'term_id',
			[
				'label'   => __( 'Product Category', 'napoleon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_term_choices( 'product_cat' ),
			]
		);

		$this->add_control(
			'count',
			[
				'label'   => __( 'Number of products to show:', 'napoleon' ),
				'type'    => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'size' => 3,
				],
				'range'   => [
					'min'  => 1,
					'max'  => 250,
					'step' => 1,
				],
			]
		);

		$this->add_control(
			'columns',
			[
				'label'   => __( 'Columns', 'napoleon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'2' => __( 'Two', 'napoleon' ),
					'3' => __( 'Three', 'napoleon' ),
					'4' => __( 'Four', 'napoleon' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Latest Products Element Styles', 'napoleon' ),
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
		$orderby  = $settings['orderby'];
		$carousel = 'yes' === $settings['carousel'] ? true : false;
		$count    = $settings['count']['size'];
		$columns  = $settings['columns'];
		$term_id  = $settings['term_id'];

		$carousel_class = $carousel ? 'wc-slider' : '';

		$order = 'DESC';

		if ( 'title' === $orderby ) {
			$order = 'ASC';
		}

		if ( $title || $subtitle ) {
			?>
			<div class="section-heading">
				<div class="section-heading-content">
			<?php

			$more_url = get_permalink( wc_get_page_id( 'shop' ) );

			if ( $term_id ) {
				$more_url = get_term_link( intval( $term_id ) );
			}

			if ( $title ) {
				echo '<h2 class="section-title">' . esc_html( $title ) . ' <a href="' . esc_url( $more_url ) . '">' . esc_html__( 'See More', 'napoleon' ) . '</a></h2>';
			}

			if ( $subtitle ) {
				echo '<p class="section-subtitle">' . esc_html( $subtitle ) . '</p>';
			}

			?>
				</div>

				<?php if ( $carousel ) : ?>
					<div class="row-slider-nav"></div>
				<?php endif; ?>

			</div>
			<?php
		}

		echo do_shortcode( sprintf( '[products limit="%1$s" columns="%2$s" orderby="%3$s" order="%4$s" category="%5$s" class="%6$s" ]',
			$count,
			$columns,
			$orderby,
			$order,
			$term_id,
			$carousel_class
		) );

		?>
		<script>
			jQuery(document).ready(function(){
				jQuery(document).trigger('elementor/render/latest_products','#at-lpr-<?php echo esc_attr( $this->get_id() ); ?>');
			});
		</script>
		<?php
	}

	protected function get_term_choices( $taxonomy ) {
		$terms = get_terms( array(
			'taxonomy' => $taxonomy,
			'fields'   => 'id=>name',
		) );

		$choices = array( '0' => '' );

		foreach ( $terms as $id => $name ) {
			$choices[ (string) $id ] = $name;
		}

		return $choices;
	}
}
