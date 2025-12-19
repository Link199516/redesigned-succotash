<?php
	$wp_customize->add_setting( 'theme_social_target', array(
		'transport'         => 'postMessage',
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'theme_social_target', array(
		'type'    => 'checkbox',
		'section' => 'theme_social',
		'label'   => esc_html__( 'Open social and sharing links in a new tab.', 'napoleon' ),
	) );


	$wp_customize->add_setting( 'theme_contact_number', array(
		'transport'         => 'postMessage',
		'default'           => '',
		'sanitize_callback' => 'esc_html',
	) );
	$wp_customize->add_control( 'theme_contact_number', array(
		'type'    => 'text',
		'section' => 'theme_social',
		'label'   => esc_html__( 'Contact Number', 'napoleon' ),
		'description' => __('You can add a standard phone number like <strong>tel:XXXXXXXXXX</strong> or you can add whatsapp number like <strong>https://wa.me/1XXXXXXXXXX</strong>','napoleon'),
	) );



	$networks    = napoleon_get_social_networks();
	$social_mods = array();

	foreach ( $networks as $network ) {
		$social_mod    = 'theme_social_' . $network['name'];
		$social_mods[] = $social_mod;

		$wp_customize->add_setting( $social_mod, array(
			'transport'         => 'postMessage',
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( $social_mod, array(
			'type'    => 'url',
			'section' => 'theme_social',
			/* translators: %s is a social network's name, e.g.: Facebook URL */
			'label'   => esc_html( sprintf( _x( '%s URL', 'social network url', 'napoleon' ), $network['label'] ) ),
		) );
	}

	$wp_customize->add_setting( 'theme_rss_feed', array(
		'transport'         => 'postMessage',
		'default'           => get_bloginfo( 'rss2_url' ),
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'theme_rss_feed', array(
		'type'    => 'url',
		'section' => 'theme_social',
		'label'   => esc_html__( 'RSS Feed', 'napoleon' ),
	) );

	$wp_customize->selective_refresh->add_partial( 'theme_socials', array(
		'selector'            => '.list-social-icons',
		'render_callback'     => 'napoleon_the_social_icons',
		'settings'            => array_merge( array( 'theme_social_target', 'theme_rss_feed' ), $social_mods ),
		'container_inclusive' => true,
	) );
