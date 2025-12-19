<?php
	$wp_customize->add_setting( 'footer_show_developer', array(
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
		$wp_customize->add_control( 'footer_show_developer', array(
			'type'    => 'checkbox',
			'section' => 'theme_footer_bottom_bar',
			'label'   => esc_html__( 'Show Theme Developer', 'napoleon' ),
		) );

	$wp_customize->selective_refresh->get_partial( 'theme_footer_layout' )->settings[] = 'footer_show_bottom_bar';


	$wp_customize->add_setting( 'napoleon_footer_text', array(
		'transport'         => 'refresh',
		'default'        => '',
		'sanitize_callback' => 'esc_html',
 	) );
		$wp_customize->add_control( 'napoleon_footer_text',
			array(
			    'section'   => 'theme_footer_bottom_bar',
			    'label'     => __( 'Custom Footer Credit Text', 'napoleon' ),
			    'type' => 'textarea'
			)
		);
