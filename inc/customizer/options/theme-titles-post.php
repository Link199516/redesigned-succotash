<?php
	$wp_customize->add_setting( 'title_post_related_title', array(
		'default'           => esc_html__( 'Related articles', 'napoleon' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'title_post_related_title', array(
		'type'    => 'text',
		'section' => 'theme_titles_post',
		'label'   => esc_html__( 'Related posts title', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'title_post_related_subtitle', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'title_post_related_subtitle', array(
		'type'    => 'text',
		'section' => 'theme_titles_post',
		'label'   => esc_html__( 'Related posts subtitle', 'napoleon' ),
	) );
