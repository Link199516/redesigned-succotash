<?php
    add_action('wp_ajax_abandoned_carts', 'abandoned_carts');
    add_action('wp_ajax_nopriv_abandoned_carts', 'abandoned_carts');

    function abandoned_carts() {
        $data = [];

        // Loop through posted data array transmitted via jQuery
        foreach ($_POST['fields'] as $values) {
            // Set each key / value pairs in an array
            $data[$values['name']] = $values['value'];
        }

        $order = null;
        $order_id = 0;

        // Try to load existing abandoned order from session first
        if (isset($_SESSION['current_abandoned_cart_id']) && absint($_SESSION['current_abandoned_cart_id']) > 0) {
            $session_order_id = absint($_SESSION['current_abandoned_cart_id']);
            $existing_order = wc_get_order($session_order_id);

            // Ensure it's a valid order and still in 'pending' status (i.e., not converted or deleted)
            if ($existing_order && $existing_order->get_status() === 'pending') {
                $order = $existing_order;
                $order_id = $session_order_id;
            } else {
                // If session ID is invalid or order is not pending, clear it
                unset($_SESSION['current_abandoned_cart_id']);
            }
        }

        // If no existing order found, create a new one
        if (!$order) {
            $order = new \WC_Order();
        }

        // Set order status to pending or a custom abandoned status
        $order->set_status('pending'); // Or 'abandoned' if you register a custom status

        $cart = WC()->cart;
        $checkout = WC()->checkout;

        $data['product_id'] = isset($data['product_id']) ? $data['product_id'] : '';
        $data['count_number'] = isset($data['codplugin_c_number']) ? $data['codplugin_c_number'] : 1;
        if (isset($data['var_id'])) {
            $data['var_id'] = $data['var_id'];
        }
        if (!isset($data['billing_first_name'])) {
            $data['billing_first_name'] = isset($data['full_name']) ? $data['full_name'] : '';
        }
        if (!isset($data['billing_phone'])) {
            $phone_number = isset($data['phone_number']) ? $data['phone_number'] : '';
            // Sanitize phone number: remove all non-digit characters
            $sanitized_phone_number = preg_replace('/[^0-9]/', '', $phone_number);

            // Validate phone number length
            if (strlen((string)$sanitized_phone_number) < 10) {
                // If phone number is less than 10 digits, stop processing and do not save/update
                echo json_encode(['error' => __('Phone number must be at least 10 digits.', 'napoleon')]);
                wp_die();
            }
            $data['billing_phone'] = $sanitized_phone_number;
        }

        if (!isset($data['billing_address_1'])) {
            $data['billing_address_1'] = isset($data['full_address']) ? $data['full_address'] : '';
        }

        if (!isset($data['billing_state'])) {
            $data['billing_state'] = isset($data['codplugin_state']) ? $data['codplugin_state'] : '';
        }

        if (!isset($data['billing_city'])) {
            $data['billing_city'] = isset($data['codplugin_city']) ? $data['codplugin_city'] : '';
        }

        $cart_hash = md5(json_encode(wc_clean($cart->get_cart_for_session())) . $cart->total);
        $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

        // Loop through the data array
        foreach ($data as $key => $value) {
            // Use WC_Order setter methods if they exist
            if (is_callable(array($order, "set_{$key}"))) {
                $order->{"set_{$key}"}($value);

                // Store custom fields prefixed with either shipping_ or billing_
            } elseif ((0 === stripos($key, 'billing_') || 0 === stripos($key, 'shipping_')) && !in_array($key, array('shipping_method', 'shipping_total', 'shipping_tax'))) {
                $order->update_meta_data('_' . $key, $value);
            }
        }

        $order->set_created_via('checkout');
        if (isset($data['product_id'])) {
            $order->set_cart_hash($cart_hash);
        }
        $order->set_customer_id(apply_filters('woocommerce_checkout_customer_id', isset($_POST['user_id']) ? $_POST['user_id'] : ''));
        $order->set_currency(get_woocommerce_currency());
        $order->set_prices_include_tax('yes' === get_option('woocommerce_prices_include_tax'));
        // $order->set_customer_ip_address( \WC_Geolocation::get_ip_address() );
        $order->set_customer_user_agent(wc_get_user_agent());
        $order->set_customer_note(isset($data['order_comments']) ? $data['order_comments'] : '');
        $order->set_shipping_total($cart->get_shipping_total());
        $order->set_discount_total($cart->get_discount_total());
        $order->set_discount_tax($cart->get_discount_tax());
        $order->set_cart_tax($cart->get_cart_contents_tax() + $cart->get_fee_tax());
        $order->set_shipping_tax($cart->get_shipping_tax());
        $order->set_total($cart->get_total('edit'));

        // Clear existing line items before adding new ones for updates
        $order->remove_order_items('line_item');
        $order->remove_order_items('fee');
        $order->remove_order_items('shipping');
        $order->remove_order_items('tax');
        $order->remove_order_items('coupon');

        $checkout->create_order_line_items($order, $cart);
        $checkout->create_order_fee_lines($order, $cart);
        $checkout->create_order_shipping_lines($order, WC()->session->get('chosen_shipping_methods'), WC()->shipping->get_packages());
        $checkout->create_order_tax_lines($order, $cart);
        $checkout->create_order_coupon_lines($order, $cart);

        if (isset($data['var_id']) &&  !empty($data['var_id']) ) {
            $order->add_product(wc_get_product($data['var_id']), $data['count_number']);
            $order->calculate_totals();
        } elseif (isset($data['product_id'])) {
            $order->add_product(wc_get_product($data['product_id']), $data['count_number']);
            $order->calculate_totals();
        }

        /**
         * Action hook to adjust the order before save.
         * @since 3.0.0
         */
        do_action('woocommerce_checkout_create_order', $order, $data);

        // Save the order.
        $order_id = $order->save();

        // Store the abandoned order ID in session or user meta if logged in
        // For simplicity, we'll use session for now, but a more robust solution
        // might involve user meta or a custom database table for logged-in users.
        if (!isset($_SESSION['abandoned_carts_order_ids'])) {
            $_SESSION['abandoned_carts_order_ids'] = [];
        }
        // Add or update the current order ID in the session
        // Store the current abandoned order ID in session
        $_SESSION['current_abandoned_cart_id'] = $order_id;

        // Remove the old array-based session variable if it still exists
        if (isset($_SESSION['abandoned_carts_order_ids'])) {
            unset($_SESSION['abandoned_carts_order_ids']);
        }

        echo json_encode(['order_id' => $order_id, 'message' => 'Draft order saved/updated with ID: #' . $order_id]);

        wp_die();
    }
