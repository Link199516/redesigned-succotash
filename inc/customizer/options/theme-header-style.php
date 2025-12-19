<?php
	$wp_customize->add_setting( 'header_fullwidth', array(
		'transport'         => 'postMessage',
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'header_fullwidth', array(
		'type'    => 'checkbox',
		'section' => 'theme_header_style',
		'label'   => esc_html__( 'Full width header', 'napoleon' ),
	) );

	$wp_customize->selective_refresh->add_partial( 'theme_header_layout', array(
		'selector'            => '.header',
		'render_callback'     => 'napoleon_header',
		'settings'            => array( 'header_fullwidth', 'theme_header_primary_menu_sticky' ),
		'container_inclusive' => true,
	) );

	$wp_customize->add_setting( 'top_bar_display', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'top_bar_display', array(
		'type'        => 'checkbox',
		'section'     => 'theme_header_style',
		'label'       => esc_html__( 'Display top bar?', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'header_search_display', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'header_search_display', array(
		'type'        => 'checkbox',
		'section'     => 'theme_header_style',
		'label'       => esc_html__( 'Display search form in header?', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'header_search_ajax', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'header_search_ajax', array(
		'type'        => 'checkbox',
		'section'     => 'theme_header_style',
		'label'       => esc_html__( 'AJAX Search form', 'napoleon' ),
		'description' => esc_html__( 'Display up to 5 matching products as the user types on the header search bar.', 'napoleon' ),
	) );


	$wp_customize->add_setting( 'cart_display', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'cart_display', array(
		'type'        => 'checkbox',
		'section'     => 'theme_header_style',
		'label'       => esc_html__( 'Display cart icon in header?', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'header_announcement', array(
		'default'           => esc_html__( 'Welcome to our online shop', 'napoleon' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'header_announcement', array(
		'type'    => 'text',
		'section' => 'theme_header_style',
		'label'   => esc_html__( 'Announcement Text', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'header_announcement_2', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'header_announcement_2', array(
		'type'    => 'text',
		'section' => 'theme_header_style',
	) );

	$wp_customize->add_setting( 'header_announcement_3', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'header_announcement_3', array(
		'type'    => 'text',
		'section' => 'theme_header_style',
	) );

