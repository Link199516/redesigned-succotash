<?php

class COD_Plugin_Checkout_Form_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'cod_checkout_form';
	}

	public function get_title() {
		return esc_html__( 'COD Checkout Form', 'napoleon' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_custom_help_url() {
		return 'https://bitherhood.com';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	public function get_keywords() {
		return [ 'form', 'checkout', 'Woocommerce', 'cod' ];
	}

	// public function get_script_depends(){
	// 	wp_register_script( 'codplugin-script', plugins_url( 'assets/js/codplugin.js', __FILE__ ) );
	// 	return ['codplugin-script'];
	// }

	// public function get_style_depends() {
	// 	wp_register_style( 'codplugin-style', plugins_url( 'assets/css/codplugin.css', __FILE__ ) );
	// 	wp_register_style( 'codplugin-style-rtl', plugins_url( 'assets/css/codplugin-rtl.css', __FILE__ ) );
	// 	return ['codplugin-style', 'codplugin-style-rtl'];
	// }

	protected function register_controls(){
		$this->start_controls_section(
			'section_product',
			[
				'label' => esc_html__( 'Product', 'napoleon' ),
			]
		);

		$args = array(
		    'limit'  => -1,
		);

		$products = wc_get_products( $args );
		foreach( $products as $product ) {
		    $items[$product->get_id()] = $product->get_name();
		   }
		$this->add_control(
			'product_id',
			[
				'label' => esc_html__( 'Product', 'napoleon' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => $items,
				// 'label_block' => true,
				// 'autocomplete' => [
				// 	'object' => Module::QUERY_OBJECT_POST,
				// 	'query' => [
				// 		'post_type' => [ 'product' ],
				// 	],
				// ],
			]
		);

		$this->end_controls_section();

	}


	protected function render() {
		// Only render the form if the theme license is valid
		if ( ! function_exists('napoleon_is_theme_license_valid') || ! napoleon_is_theme_license_valid() ) {
			// Optionally display a message in Elementor editor preview
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'COD Checkout Form requires an active theme license.', 'napoleon' ) . '</p>';
			}
			return; // Exit early if license is not valid
		}

		$product_id = $this->get_settings_for_display('product_id');

		if ( empty($product_id) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Please select a product in the widget settings.', 'napoleon' ) . '</p>';
			}
			return;
		}

		if ( wc_get_product($product_id) ) {
    		codplugin_checkout_form($product_id);
		} else {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Selected product not found.', 'napoleon' ) . '</p>';
			}
		}
	}

}
