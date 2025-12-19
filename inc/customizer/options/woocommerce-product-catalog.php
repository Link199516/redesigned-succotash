<?php
	$wp_customize->add_setting( 'theme_shop_layout', array(
		'default'           => 'right',
		'sanitize_callback' => 'napoleon_sanitize_shop_layout',
	) );
	$wp_customize->add_control( 'theme_shop_layout', array(
		'type'    => 'select',
		'section' => 'woocommerce_product_catalog',
		'label'   => esc_html__( 'Shop layout', 'napoleon' ),
		'choices' => napoleon_get_shop_layout_choices(),
	) );

	$wp_customize->add_setting( 'product_show_categories', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'product_show_categories', array(
		'type'    => 'checkbox',
		'section' => 'woocommerce_product_catalog',
		'label'   => esc_html__( 'Show categories', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'product_show_rating', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'product_show_rating', array(
		'type'    => 'checkbox',
		'section' => 'woocommerce_product_catalog',
		'label'   => esc_html__( 'Show star rating', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'product_show_add_to_cart', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'product_show_add_to_cart', array(
		'type'    => 'checkbox',
		'section' => 'woocommerce_product_catalog',
		'label'   => esc_html__( 'Show add to cart button', 'napoleon' ),
	) );


	$wp_customize->add_setting( 'theme_shop_tax_hero_show', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'theme_shop_tax_hero_show', array(
		'type'        => 'checkbox',
		'section'     => 'woocommerce_product_catalog',
		'label'       => esc_html__( 'Show hero section in taxonomy listings.', 'napoleon' ),
		'description' => esc_html__( 'Applies to product categories and product tags listings. Individual categories can override this setting.', 'napoleon' ),
	) );


	$wp_customize->add_setting( 'display_buy_button', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
		$wp_customize->add_control( 'display_buy_button', array(
			'type'    => 'checkbox',
			'section' => 'woocommerce_product_catalog',
			'label'   => esc_html__( 'Display buy now button', 'napoleon' ),
		) );


	$wp_customize->add_setting( 'buy_button_text', array(
		'default'           => esc_html__( 'Buy it now', 'napoleon' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
		$wp_customize->add_control( 'buy_button_text', array(
			'type'    => 'text',
			'section' => 'woocommerce_product_catalog',
			'label'   => esc_html__( 'Buy button text', 'napoleon' ),
		) );

	$wp_customize->add_setting( 'add_shadow_effect', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
		$wp_customize->add_control( 'add_shadow_effect', array(
			'type'    => 'checkbox',
			'section' => 'woocommerce_product_catalog',
			'label'   => esc_html__( 'Add shadow effect to product box', 'napoleon' ),
		) );

