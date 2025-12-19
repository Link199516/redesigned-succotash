<?php
	$wp_customize->add_setting( 'post_show_featured', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'post_show_featured', array(
		'type'    => 'checkbox',
		'section' => 'theme_post_options',
		'label'   => esc_html__( 'Show featured image', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'post_show_date', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'post_show_date', array(
		'type'    => 'checkbox',
		'section' => 'theme_post_options',
		'label'   => esc_html__( 'Show date', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'post_show_author', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'post_show_author', array(
		'type'    => 'checkbox',
		'section' => 'theme_post_options',
		'label'   => esc_html__( 'Show author', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'post_show_comments', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'post_show_comments', array(
		'type'    => 'checkbox',
		'section' => 'theme_post_options',
		'label'   => esc_html__( 'Show comments', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'post_show_categories', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'post_show_categories', array(
		'type'    => 'checkbox',
		'section' => 'theme_post_options',
		'label'   => esc_html__( 'Show categories', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'post_show_tags', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'post_show_tags', array(
		'type'    => 'checkbox',
		'section' => 'theme_post_options',
		'label'   => esc_html__( 'Show tags', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'post_show_authorbox', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'post_show_authorbox', array(
		'type'    => 'checkbox',
		'section' => 'theme_post_options',
		'label'   => esc_html__( 'Show author box', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'post_show_related', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'post_show_related', array(
		'type'    => 'checkbox',
		'section' => 'theme_post_options',
		'label'   => esc_html__( 'Show related posts', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'post_show_sharing', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'post_show_sharing', array(
		'type'    => 'checkbox',
		'section' => 'theme_post_options',
		'label'   => esc_html__( 'Show social sharing icons', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'theme_lightbox', array(
		'transport'         => 'postMessage',
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'theme_lightbox', array(
		'type'    => 'checkbox',
		'section' => 'theme_post_options',
		'label'   => esc_html__( 'Enable lightbox for galleries', 'napoleon' ),
	) );
