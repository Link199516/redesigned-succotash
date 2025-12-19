<?php

add_action('wp_enqueue_scripts', 'enqueue_scripts');

    function enqueue_scripts() {
        wp_enqueue_script('jquery');

        wp_enqueue_script(
            'abandoned-carts-js',
            get_theme_file_uri('/inc/order-form/include/abandoned-carts/assets/abandoned-carts.js'),
            ['jquery'],
            '1.0',
            true
        );

        wp_localize_script(
            'abandoned-carts-js',
            'ajax_helper',
            [
                'security' => wp_create_nonce('abandoned_carts_nonce'),
                'ajaxurl' => admin_url('admin-ajax.php'),
            ]
        );
    }