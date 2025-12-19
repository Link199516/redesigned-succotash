<?php


	$wp_customize->add_setting( 'display_order_form', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
		$wp_customize->add_control( 'display_order_form', array(
			'type'    => 'checkbox',
			'section' => 'theme_woocommerce_single_product',
			'label'   => esc_html__( 'Display quick order form', 'napoleon' ),
		) );

	$wp_customize->add_setting( 'product_title_before_image', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
		$wp_customize->add_control( 'product_title_before_image', array(
			'type'    => 'checkbox',
			'section' => 'theme_woocommerce_single_product',
			'label'   => esc_html__( 'Display product title before the image in mobiles', 'napoleon' ),
		) );

	$wp_customize->add_setting( 'product_image_zoom', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
		$wp_customize->add_control( 'product_image_zoom', array(
			'type'    => 'checkbox',
			'section' => 'theme_woocommerce_single_product',
			'label'   => esc_html__( 'Disable zoom in product image', 'napoleon' ),
		) );


	$wp_customize->add_setting( 'theme_product_layout', array(
		'default'           => 'right',
		'sanitize_callback' => 'napoleon_sanitize_product_layout',
	) );
	$wp_customize->add_control( 'theme_product_layout', array(
		'type'    => 'select',
		'section' => 'theme_woocommerce_single_product',
		'label'   => esc_html__( 'Product layout', 'napoleon' ),
		'choices' => napoleon_get_product_layout_choices(),
	) );
	
	$wp_customize->add_setting( 'show_related_products', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'show_related_products', array(
		'type'    => 'checkbox',
		'section' => 'theme_woocommerce_single_product',
		'label'   => esc_html__( 'Show related products', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'show_breadcrumb', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'show_breadcrumb', array(
		'type'    => 'checkbox',
		'section' => 'theme_woocommerce_single_product',
		'label'   => esc_html__( 'Show breadcrumb', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'show_sticky_atc', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
		$wp_customize->add_control( 'show_sticky_atc', array(
			'type'    => 'checkbox',
			'section' => 'theme_woocommerce_single_product',
			'label'   => esc_html__( 'Show sticky add to cart', 'napoleon' ),
		) );


	$wp_customize->add_setting( 'sticky_atc_text', array(
		'default'           => esc_html__( 'Buy it now', 'napoleon' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
		$wp_customize->add_control( 'sticky_atc_text', array(
			'type'    => 'text',
			'section' => 'theme_woocommerce_single_product',
			'label'   => esc_html__( 'Sticky button text', 'napoleon' ),
		) );


