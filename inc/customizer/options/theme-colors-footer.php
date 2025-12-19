<?php
	$scss = new napoleon_SCSS_Colors( get_theme_file_path( '/css/inc/_variables.scss' ) );

	$wp_customize->add_setting( 'footer_bg_color', array(
		'transport'         => 'postMessage',
		'default'           => '#fafafa',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_bg_color', array(
		'section' => 'theme_colors_footer',
		'label'   => esc_html__( 'Main footer background color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'footer_text_color', array(
		'transport'         => 'postMessage',
		'default'           => '#333',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_text_color', array(
		'section' => 'theme_colors_footer',
		'label'   => esc_html__( 'Main footer text color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'footer_link_color', array(
		'transport'         => 'postMessage',
		'default'           => '#333',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_link_color', array(
		'section' => 'theme_colors_footer',
		'label'   => esc_html__( 'Main footer link color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'footer_border_color', array(
		'transport'         => 'postMessage',
		'default'           => '#4C3BCF',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_border_color', array(
		'section' => 'theme_colors_footer',
		'label'   => esc_html__( 'Main footer border color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'footer_bottom_bg_color', array(
		'transport'         => 'postMessage',
		'default'           => '#efefef',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_bottom_bg_color', array(
		'section' => 'theme_colors_footer',
		'label'   => esc_html__( 'Bottom bar background color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'footer_bottom_text_color', array(
		'transport'         => 'postMessage',
		'default'           => '#333',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_bottom_text_color', array(
		'section' => 'theme_colors_footer',
		'label'   => esc_html__( 'Bottom bar text color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'footer_bottom_link_color', array(
		'transport'         => 'postMessage',
		'default'           => '#4C3BCF',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_bottom_link_color', array(
		'section' => 'theme_colors_footer',
		'label'   => esc_html__( 'Bottom bar link color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'footer_titles_color', array(
		'transport'         => 'postMessage',
		'default'           => '#333',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_titles_color', array(
		'section' => 'theme_colors_footer',
		'label'   => esc_html__( 'Widget titles text color', 'napoleon' ),
	) ) );

	$partial = $wp_customize->selective_refresh->get_partial( 'theme_style' );
	$partial->settings = array_merge( $partial->settings, array(
		'footer_bg_color',
		'footer_text_color',
		'footer_link_color',
		'footer_border_color',
		'footer_bottom_bg_color',
		'footer_bottom_text_color',
		'footer_bottom_link_color',
		'footer_titles_color',
	) );
