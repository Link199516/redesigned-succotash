<?php
namespace Elementor;

class Widget_Latest_Posts extends \Elementor\Widget_Base {
	
	public function get_name() {
		return 'latest_posts';
	}

	public function get_title() {
		return __( 'Latest Posts', 'napoleon' );
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
				'label' => __( 'Latest Posts', 'napoleon' ),
			]
		);

		$this->add_control(
			'html_msg',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Displays a number of the latest (or random) posts, optionally from a specific category.', 'napoleon' ),
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
			'random',
			[
				'label'        => __( 'Display in random order', 'napoleon' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'napoleon' ),
				'label_off'    => __( 'No', 'napoleon' ),
				'return_value' => 'yes',
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
				'label'   => __( 'Category', 'napoleon' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_term_choices( 'category' ),
			]
		);

		$this->add_control(
			'count',
			[
				'label'   => __( 'Number of posts to show:', 'napoleon' ),
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
		$random   = 'yes' === $settings['random'] ? true : false;
		$carousel = 'yes' === $settings['carousel'] ? true : false;
		$count    = $settings['count']['size'];
		$columns  = $settings['columns'];
		$term_id  = $settings['term_id'];

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

				<?php if ( $carousel ) : ?>
					<div class="row-slider-nav"></div>
				<?php endif; ?>

			</div>
			<?php
		}

		echo do_shortcode( sprintf( '[latest-post-type post_type="%1$s" count="%2$s" columns="%3$s" random="%4$s" carousel="%5$s" taxonomy="%6$s" term_ids="%7$s"]',
			'post',
			$count,
			$columns,
			$random,
			$carousel,
			'category',
			$term_id
		) );
		?>
		<script>
			jQuery(document).ready(function(){
				jQuery(document).trigger('elementor/render/latest_posts','#at-lp-<?php echo esc_attr( $this->get_id() ); ?>');
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
