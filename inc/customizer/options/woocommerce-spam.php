<?php


        $wp_customize->add_setting( 'block_ip_reordering', array(
            'default'           => 0,
            'sanitize_callback' => 'absint',
        ) );
            $wp_customize->add_control( 'block_ip_reordering', array(
                'type'    => 'checkbox',
                'section' => 'theme_woocommerce_spam',
                'label'   => esc_html__( 'Stop same IP from ordering again within 24 hours ', 'napoleon' ),
            ) );


        $wp_customize->add_setting( 'block_cookies_reordering', array(
            'default'           => 0,
            'sanitize_callback' => 'absint',
        ) );
            $wp_customize->add_control( 'block_cookies_reordering', array(
                'type'    => 'checkbox',
                'section' => 'theme_woocommerce_spam',
                'label'   => esc_html__( 'Stop ordering again within 24 hours using browser cookies ', 'napoleon' ),
            ) );


        $wp_customize->add_setting( 'block_desktop_visitors', array(
            'default'           => 0,
            'sanitize_callback' => 'absint',
        ) );
            $wp_customize->add_control( 'block_desktop_visitors', array(
                'type'    => 'checkbox',
                'section' => 'theme_woocommerce_spam',
                'label'   => esc_html__( 'Display cod form only for phone visitors', 'napoleon' ),
            ) );



        $wp_customize->add_setting( 'disable_infos_autofill', array(
            'default'           => 0,
            'sanitize_callback' => 'absint',
        ) );
            $wp_customize->add_control( 'disable_infos_autofill', array(
                'type'    => 'checkbox',
                'section' => 'theme_woocommerce_spam',
                'label'   => esc_html__( 'Disable autofill & pasting infos in the form', 'napoleon' ),
            ) );


        $wp_customize->add_setting( 'disable_text_copying', array(
            'default'           => 0,
            'sanitize_callback' => 'absint',
        ) );
            $wp_customize->add_control( 'disable_text_copying', array(
                'type'    => 'checkbox',
                'section' => 'theme_woocommerce_spam',
                'label'   => esc_html__( 'Disable text copying', 'napoleon' ),
            ) );


        $wp_customize->add_setting( 'codform_tel_settings', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
            $wp_customize->add_control( 'codform_tel_settings', array(
                'type'    => 'text',
                'section' => 'theme_woocommerce_spam',
                'label'   => esc_html__( 'Add pattern for phone number:', 'napoleon' ),
            ) );

