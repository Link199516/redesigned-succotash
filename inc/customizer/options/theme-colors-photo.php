<?php

		$wp_customize->add_setting( 'add_photo_border', array(
			'transport'         => 'refresh',
			'default'           => 0,
			'sanitize_callback' => 'absint',
		) );
			$wp_customize->add_control( 'add_photo_border', array(
				'type'     => 'checkbox',
				'section'  => 'theme-colors-photo',
				'label'    => esc_html__( 'Add border to product photos', 'napoleon' ),
			) );

		$wp_customize->add_setting( 'photo_border_color', array(
			'transport'         => 'refresh',
			'default'           => '#4C3BCF',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'photo_border_color', array(
			'section' => 'theme-colors-photo',
			'label'   => esc_html__( 'Photo Border Color', 'napoleon' ),
		) ) );
