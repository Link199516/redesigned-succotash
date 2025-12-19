<?php

if ( ! get_theme_mod( 'enable_abandoned_carts', 0 ) ) {
    return;
}


function abandoned_carts_main() {

    if (!session_id()) {
        session_start();
    }

    require_once get_theme_file_path('/inc/order-form/include/abandoned-carts/assets-manager.php');
    require_once get_theme_file_path('/inc/order-form/include/abandoned-carts/ajax.php');
    require_once get_theme_file_path('/inc/order-form/include/abandoned-carts/process-orders.php');

}

add_action('init', 'abandoned_carts_main');





