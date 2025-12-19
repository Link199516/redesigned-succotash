<?php
/**
 * Customizer Functions
 *
 * @package Napoleon
 */

add_action( 'customize_controls_enqueue_scripts', 'napoleon_customizer_scripts' );
function napoleon_customizer_scripts() {
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array( 'jquery' ), false, true );
    wp_enqueue_script( 'napoleon-customizer', get_template_directory_uri() . '/assets/js/admin/customizer.js', array( 'jquery', 'customize-controls', 'select2', 'jquery-ui-sortable' ), false, true );
    wp_enqueue_style( 'napoleon-customizer', get_template_directory_uri() . '/assets/css/admin/customizer.css' );
    wp_enqueue_style( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css' );
}

add_action( 'wp_ajax_napoleon_search_products', 'napoleon_search_products' );
function napoleon_search_products() {
    $search = isset( $_REQUEST['q'] ) ? sanitize_text_field( $_REQUEST['q'] ) : '';
    $products = array();
    $query = new WP_Query( array(
        's' => $search,
        'post_type' => 'product',
        'posts_per_page' => -1,
    ) );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $products[] = array(
                'id' => get_the_ID(),
                'text' => get_the_title(),
            );
        }
    }

    wp_send_json( $products );
}

function napoleon_sanitize_repeater( $value ) {
    $value = json_decode( $value, true );
    if ( is_array( $value ) ) {
        foreach ( $value as $key => $item ) {
            foreach ( $item as $field => $field_value ) {
                $value[$key][$field] = sanitize_text_field( $field_value );
            }
        }
        return json_encode( $value );
    }
    return array();
}
