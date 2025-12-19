<?php


	$wp_customize->add_setting( 'contact_icon', array(
		'transport'         => 'refresh',
		'default'           => 'fas fa-phone-volume',
		'sanitize_callback' => 'sanitize_text_field',
	) );
		$wp_customize->add_control( 'contact_icon', array(
			'section' => 'theme_colors_contact',
			'label'   => esc_html__( 'Contact Icon', 'napoleon' ),
			'type'    => 'text',
		) );


	$wp_customize->add_setting( 'contact_bg_color', array(
		'transport'         => 'refresh',
		'default'           => '#4C3BCF',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'contact_bg_color', array(
			'section' => 'theme_colors_contact',
			'label'   => esc_html__( 'Button Color', 'napoleon' ),
		) ) );

	$wp_customize->add_setting( 'contact_animation', array(
			'transport'         => 'refresh',
			'default'           => 1,
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( 'contact_animation', array(
			'type'     => 'checkbox',
			'section'  => 'theme_colors_contact',
			'label'    => esc_html__( 'Animate contact icon', 'napoleon' ),
		) );

	$wp_customize->add_setting( 'contact_rotation', array(
			'transport'         => 'refresh',
			'default'           => 1,
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( 'contact_rotation', array(
			'type'     => 'checkbox',
			'section'  => 'theme_colors_contact',
			'label'    => esc_html__( 'Rotate contact icon', 'napoleon' ),
		) );

