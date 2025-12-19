<?php
	$scss = new napoleon_SCSS_Colors( get_theme_file_path( '/css/inc/_variables.scss' ) );

	$wp_customize->add_setting( 'header_top_bar_bg_color', array(
		'transport'         => 'postMessage',
		'default'           => '#4C3BCF',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_top_bar_bg_color', array(
		'section' => 'theme_colors_primary_menu_bar',
		'label'   => esc_html__( 'Top Bar background color', 'napoleon' ),
	) ) );
	
	$wp_customize->add_setting( 'header_background_color', array(
		'transport'         => 'postMessage',
		'default'           => '#fff',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_background_color', array(
		'section' => 'theme_colors_primary_menu_bar',
		'label'   => esc_html__( 'Header background color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'top_bar_text_color', array(
		'transport'         => 'postMessage',
		'default'           => '#ffffff',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'top_bar_text_color', array(
		'section' => 'theme_colors_primary_menu_bar',
		'label'   => esc_html__( 'Top Bar Text color', 'napoleon' ),
	) ) );
	$wp_customize->add_setting( 'header_text_color', array(
		'transport'         => 'postMessage',
		'default'           => '#000000',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_text_color', array(
		'section' => 'theme_colors_primary_menu_bar',
		'label'   => esc_html__( 'Header Text color', 'napoleon' ),
	) ) );
	$wp_customize->add_setting( 'header_primary_menu_text_color', array(
		'transport'         => 'postMessage',
		'default'           => '#333',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_primary_menu_text_color', array(
		'section' => 'theme_colors_primary_menu_bar',
		'label'   => esc_html__( 'Menu Text color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'header_primary_menu_active_color', array(
		'transport'         => 'postMessage',
		'default'           => '#4C3BCF',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_primary_menu_active_color', array(
		'section' => 'theme_colors_primary_menu_bar',
		'label'   => esc_html__( 'Menu active & hover color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'header_primary_submenu_bg_color', array(
		'transport'         => 'postMessage',
		'default'           => '#fff',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_primary_submenu_bg_color', array(
		'section' => 'theme_colors_primary_menu_bar',
		'label'   => esc_html__( 'Sub-menu background color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'header_primary_submenu_text_color', array(
		'transport'         => 'postMessage',
		'default'           => '#333',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_primary_submenu_text_color', array(
		'section' => 'theme_colors_primary_menu_bar',
		'label'   => esc_html__( 'Sub-menu text color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'header_primary_submenu_active_text_color', array(
		'transport'         => 'postMessage',
		'default'           => '#4C3BCF',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_primary_submenu_active_text_color', array(
		'section' => 'theme_colors_primary_menu_bar',
		'label'   => esc_html__( 'Sub-menu active text color', 'napoleon' ),
	) ) );

	$partial = $wp_customize->selective_refresh->get_partial( 'theme_style' );
	$partial->settings = array_merge( $partial->settings, array(
		'header_background_color',
		'header_primary_menu_text_color',
		'header_primary_menu_active_color',
		'header_primary_submenu_bg_color',
		'header_primary_submenu_text_color',
		'header_primary_submenu_active_text_color',
	) );
