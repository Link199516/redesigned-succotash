<?php
	$scss = new napoleon_SCSS_Colors( get_theme_file_path( '/css/inc/_variables.scss' ) );

	// Rename & Reposition the header image section.
	$wp_customize->get_control( 'background_color' )->section      = 'theme_colors_global';
	$wp_customize->get_control( 'background_image' )->section      = 'theme_colors_global';
	$wp_customize->get_control( 'background_preset' )->section     = 'theme_colors_global';
	$wp_customize->get_control( 'background_position' )->section   = 'theme_colors_global';
	$wp_customize->get_control( 'background_size' )->section       = 'theme_colors_global';
	$wp_customize->get_control( 'background_repeat' )->section     = 'theme_colors_global';
	$wp_customize->get_control( 'background_attachment' )->section = 'theme_colors_global';

	$wp_customize->add_setting( 'site_secondary_accent_color', array(
		'transport'         => 'postMessage',
		'default'           => '#ea593c',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_secondary_accent_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Link & Secondary Text Color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'site_accent_color', array(
		'transport'         => 'postMessage',
		'default'           => '#4C3BCF',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_accent_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Accent color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'site_text_color', array(
		'transport'         => 'postMessage',
		'default'           => '#4A4A4A',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_text_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Text color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'site_text_color_secondary', array(
		'transport'         => 'postMessage',
		'default'           => '#333',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_text_color_secondary', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Text color secondary (darker)', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'site_text_color_supplementary', array(
		'transport'         => 'postMessage',
		'default'           => '#4C3BCF',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_text_color_supplementary', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Supplementary text color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'site_border_color', array(
		'transport'         => 'postMessage',
		'default'           => '#DDDDDD',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_border_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Border color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'site_button_bg_color', array(
		'transport'         => 'postMessage',
		'default'           => '#4C3BCF',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_button_bg_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Button background color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'site_button_text_color', array(
		'transport'         => 'postMessage',
		'default'           => '#fff',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_button_text_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Button text color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'site_button_hover_bg_color', array(
		'transport'         => 'postMessage',
		'default'           => napoleon_color_luminance( '#4C3BCF', -0.3 ),
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_button_hover_bg_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Button hover background color', 'napoleon' ),
	) ) );

	$wp_customize->add_setting( 'site_button_hover_text_color', array(
		'transport'         => 'postMessage',
		'default'           => '#fff',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_button_hover_text_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Button hover text color', 'napoleon' ),
	) ) );

	$partial = $wp_customize->selective_refresh->get_partial( 'theme_style' );
	$partial->settings = array_merge( $partial->settings, array(
		'site_secondary_accent_color',
		'site_accent_color',
		'site_text_color',
		'site_text_color_secondary',
		'site_text_color_supplementary',
		'site_border_color',
		'site_button_bg_color',
		'site_button_text_color',
		'site_button_hover_bg_color',
		'site_button_hover_text_color',
	) );
