<?php
	if ( current_theme_supports( 'napoleon-hero' ) ) {
		$wp_customize->add_setting( 'title_blog', array(
			'default'           => esc_html__( 'From the blog', 'napoleon' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'title_blog', array(
			'type'    => 'text',
			'section' => 'theme_titles_general',
			'label'   => esc_html__( 'Blog title', 'napoleon' ),
		) );
	} // theme supports 'napoleon-hero'

	$wp_customize->add_setting( 'title_search', array(
		'default'           => esc_html__( 'Search results', 'napoleon' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'title_search', array(
		'type'    => 'text',
		'section' => 'theme_titles_general',
		'label'   => esc_html__( 'Search title', 'napoleon' ),
	) );

	$wp_customize->add_setting( 'title_404', array(
		'default'           => esc_html__( 'Page not found', 'napoleon' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'title_404', array(
		'type'    => 'text',
		'section' => 'theme_titles_general',
		'label'   => esc_html__( '404 (not found) title', 'napoleon' ),
	) );
