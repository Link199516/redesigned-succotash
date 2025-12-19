<?php
// Hook into WooCommerce order status changed to processing
add_action('woocommerce_checkout_update_order_meta', 'convert_abandoned_to_real_order', 10, 2);
add_action('woocommerce_thankyou', 'delete_abandoned_order_on_thankyou', 10, 1);

function convert_abandoned_to_real_order($order_id, $data) {
    // Check if there's an abandoned order ID in the session
    if (isset($_SESSION['current_abandoned_cart_id']) && absint($_SESSION['current_abandoned_cart_id']) > 0) {
        $abandoned_order_id = absint($_SESSION['current_abandoned_cart_id']);
        $abandoned_order = wc_get_order($abandoned_order_id);

        // If the abandoned order exists and is still pending, delete it
        if ($abandoned_order) {
            if ($abandoned_order->get_status() === 'pending') {
                $deleted = false;
                try {
                    $abandoned_order->delete(true); // Use WC_Order's delete method
                    $deleted = true;
                } catch (Exception $e) {
                    // Silently catch exceptions as per user request to remove debugging
                }
                
                if ($deleted) {
                    // No action needed here if deletion was successful and no logs are desired.
                }
            }
        }
        // Clear the session variable after conversion
        unset($_SESSION['current_abandoned_cart_id']);
    }
}

function delete_abandoned_order_on_thankyou($order_id) {
    // This function ensures that the specific abandoned order in the session
    // is cleaned up after a successful checkout, if it wasn't already converted.
    if (isset($_SESSION['current_abandoned_cart_id']) && absint($_SESSION['current_abandoned_cart_id']) > 0) {
        $abandoned_order_id = absint($_SESSION['current_abandoned_cart_id']);
        
        // Ensure we don't delete the current confirmed order if, by some chance,
        // its ID matches the abandoned one (e.g., if the same ID was reused, though unlikely).
        if ($abandoned_order_id != $order_id) {
            $order = wc_get_order($abandoned_order_id);
            if ($order) {
                if ($order->get_status() === 'pending') {
                    $deleted = false;
                    try {
                        $order->delete(true); // Use WC_Order's delete method
                        $deleted = true;
                    } catch (Exception $e) {
                        // Silently catch exceptions as per user request to remove debugging
                    }
                    
                    if ($deleted) {
                        // No action needed here if deletion was successful and no logs are desired.
                    }
                }
            }
        }
        // Clear the session variable after processing
        unset($_SESSION['current_abandoned_cart_id']);
    }
}
