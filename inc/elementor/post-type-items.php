<?php
namespace Elementor;

class Widget_Post_Type_Items extends \Elementor\Widget_Base {

	public function get_name() {
		return 'post_type_items';
	}

	public function get_title() {
		return __( 'Post Type Items', 'napoleon' );
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
				'label' => __( 'Post Type Items', 'napoleon' ),
			]
		);

		$this->add_control(
			'html_msg',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Displays a hand-picked selection of posts from a selected post type.', 'napoleon' ),
				'content_classes' => 'at-description',
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => __( 'Element Title', 'napoleon' ),
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
			'post_type',
			[
				'label'   => __( 'Post Type', 'napoleon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'post',
				'options' => napoleon_get_available_post_types(),
			]
		);

		$this->add_control(
			'selected_items',
			[
				'label'    => __( 'Select Items', 'napoleon' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'options'  => '',
				'multiple' => true,
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
				'label' => __( 'Post Type Items Element Styles', 'napoleon' ),
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

		if ( empty( $settings['selected_items'] ) ) {
			return;
		}

		$title           = $settings['title'];
		$subtitle        = $settings['subtitle'];
		$carousel        = 'yes' === $settings['carousel'] ? true : false;
		$columns         = intval( $settings['columns'] );
		$post_type_items = $settings['selected_items'];
		$post_type       = $settings['post_type'];

		$slider_class = '';
		if ( $carousel ) {
			$slider_class = 'row-slider';
		}

		if ( $title || $subtitle ) {
			?>
			<div class="section-heading">
				<div class="section-heading-content">
			<?php

				if ( $title ) {
					?><h2 class="section-title"><?php echo esc_html( $title ); ?></h2><?php
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

		$q = new \WP_Query( array(
			'post_type'      => $post_type,
			'posts_per_page' => - 1,
			'post__in'       => $post_type_items,
			'orderby'        => 'post__in',
		) );

		if ( $q->have_posts() ) {
			?><div class="row row-items <?php echo esc_attr( $slider_class ); ?>"><?php

				while ( $q->have_posts() ) {
					$q->the_post();

					?><div class="<?php echo esc_attr( napoleon_get_columns_classes( $columns ) ); ?>"><?php

						get_template_part( 'template-parts/widgets/home-item', get_post_type() );

					?></div><?php
				}
				wp_reset_postdata();

			?></div><?php
		}

		?>
		<script>
			jQuery(document).ready(function(){
				jQuery(document).trigger('elementor/render/post_type_items','#at-pti-<?php echo esc_attr( $this->get_id() ); ?>');
			});
		</script>
		<?php
	}

}
