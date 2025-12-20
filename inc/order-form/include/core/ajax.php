<?php

defined("ABSPATH") or die("You cannot access it in this way .");

add_action('wp_ajax_get_shipping_methods', 'get_shipping_methods');
add_action('wp_ajax_nopriv_get_shipping_methods', 'get_shipping_methods');

function get_shipping_methods()
{
    if (isset($_POST["action"])) {
        $state = $_POST['state'];

        $delivery_zones = array_filter(WC_Shipping_Zones::get_zones(), function ($zone) use ($state) {
            return stripos($zone["formatted_zone_location"], $state) !== false;
        });

        $shipping_methods_for_zone = [];
        foreach ((array) $delivery_zones as $key => $zone) {
            foreach ($zone["shipping_methods"] as $method) {
                // Only consider flat_rate and local_pickup for now, as per codplugin_woo_order_ajax_function
                if (strpos($method->id, 'flat_rate') !== false || strpos($method->id, 'local_pickup') !== false) {
                    $shipping_methods_for_zone[] = $method;
                }
            }
        }

        $response_data = [];
        $output_html = '';

        if (count($shipping_methods_for_zone) === 1) {
            $method = $shipping_methods_for_zone[0];
            $rate_id = $method->get_rate_id();
            $response_data = [
                'type' => 'single',
                'rate_id' => $rate_id,
                'title' => $method->get_title(),
            ];
        } elseif (count($shipping_methods_for_zone) > 1) {
            foreach ($shipping_methods_for_zone as $method) {
                $rate_id = $method->get_rate_id();
                $input_id = 'shipping_method_0_' . esc_attr(str_replace(':', '_', $rate_id));
                $output_html .= '<div class="codplugin-shipping-option">';
                $output_html .= '<input class="codplugin-shipping-input radio-button-color" type="radio" name="shipping_method" id="' . $input_id . '" value="' . esc_attr($rate_id) . '">';
                $output_html .= '<label class="codplugin-shipping-label" for="' . $input_id . '">' . esc_html($method->get_title()) . '</label>';
                $output_html .= '</div>';
            }
            $response_data = [
                'type' => 'multiple',
                'html' => $output_html,
            ];
        } else {
            $response_data = [
                'type' => 'none',
            ];
        }

        wp_send_json($response_data);
    }
}


add_action(
    "wp_ajax_codplugin_woo_order_action",
    "codplugin_woo_order_ajax_function"
);

add_action(
    "wp_ajax_nopriv_codplugin_woo_order_action",
    "codplugin_woo_order_ajax_function"
);

function codplugin_woo_order_ajax_function()
{
    if (isset($_POST["action"]) && isset($_POST["value"])) {

        global $woocommerce;

        $state = $_POST["value"];
        error_log("codplugin_woo_order_ajax_function called. State: " . $state);
        error_log("d_method received: " . (isset($_POST["d_method"]) ? $_POST["d_method"] : 'NOT SET'));

        if (isset($_POST["variation_id"]) && !empty($_POST["variation_id"])) {
            $variation_id = $_POST["variation_id"];
            $_product = wc_get_product($variation_id);
            $product_class_id = $_product->get_shipping_class_id();
        } elseif (isset($_POST["product_id"])) {
            $product_id = $_POST["product_id"];
            $_product = wc_get_product($product_id);
            $product_class_id = $_product->get_shipping_class_id();
        }

        if (isset($_POST["d_method"])) {
            $d_method = $_POST["d_method"];
        }

        $delivery_zones = array_filter(WC_Shipping_Zones::get_zones(), function ($zone) use ($state) {
            return stripos($zone["formatted_zone_location"], $state) !== false;
        });

        $cost = 0; // Initialize price to 0

        foreach ((array) $delivery_zones as $key => $the_zone) {
            foreach ($the_zone["shipping_methods"] as $value) {
                // Only consider flat_rate and local_pickup methods
                if (strpos($value->id, 'flat_rate') !== false || strpos($value->id, 'local_pickup') !== false) {
                    // If a specific delivery method is provided, match it
                    if (isset($d_method) && $d_method === $value->get_rate_id()) {
                        $data = $value->instance_settings;

                        // Determine cost based on shipping class or default
                        if (isset($product_class_id) && !empty($product_class_id) && isset($data['class_cost_' . $product_class_id])) {
                            $class_cost = $data['class_cost_' . $product_class_id];
                            // If the class cost is empty string, it means free, so set to 0.
                            $cost = ($class_cost === '') ? 0 : $class_cost;
                        } elseif (isset($product_class_id) && empty($product_class_id) && isset($data['no_class_cost']) && $data['no_class_cost'] > 0) {
                            $cost = $data['no_class_cost'];
                        } else {
                            $cost = $data['cost'];
                        }
                        break 2; // Exit both loops once the method is found
                    } elseif (!isset($d_method) && count($the_zone["shipping_methods"]) === 1) {
                        // If no d_method is set (e.g., initial load with single method)
                        // and there's only one method in the zone, use its cost.
                        $data = $value->instance_settings;
                        if (isset($product_class_id) && !empty($product_class_id) && isset($data['class_cost_' . $product_class_id])) {
                            $class_cost = $data['class_cost_' . $product_class_id];
                            $cost = ($class_cost === '') ? 0 : $class_cost;
                        } elseif (isset($product_class_id) && empty($product_class_id) && isset($data['no_class_cost']) && $data['no_class_cost'] > 0) {
                            $cost = $data['no_class_cost'];
                        } else {
                            $cost = $data['cost'];
                        }
                        break 2; // Exit both loops
                    }
                }
            }
        }
        error_log("Final calculated cost: " . $cost);

        // --- Get Stock Quantity ---
        $stock_qty = null; // Default to null (meaning not managed or not applicable)
        if ($_product && $_product->managing_stock()) {
            $current_stock = $_product->get_stock_quantity();
            // Ensure stock is a valid number before returning
            if (is_numeric($current_stock)) {
                $stock_qty = (int) $current_stock;
            }
        }
        // --- End Get Stock Quantity ---

        // Return JSON object with cost and stock
        wp_send_json(array(
            'cost' => $cost,
            'stock' => $stock_qty
        ));
        // die(); // wp_send_json includes die()
    }
}

// Create new orders with ajax
if (get_theme_mod('create_orders_with_php', 0) == 0):

    add_action(
        "wp_ajax_codplugin_order_form_action",
        "codplugin_order_form_action_function"
    );

    add_action(
        "wp_ajax_nopriv_codplugin_order_form_action",
        "codplugin_order_form_action_function"
    );

    function codplugin_order_form_action_function()
    {
        // Ensure WordPress file handling functions are available
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        if (!function_exists('wp_generate_attachment_metadata')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }
        if (!function_exists('wp_read_image_metadata')) { // Often needed by wp_generate_attachment_metadata
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }

        if (
            isset($_POST["action"]) &&
            isset($_POST["full_name"]) &&
            isset($_POST["phone_number"]) &&
            isset($_POST["full_address"]) &&
            isset($_POST["codplugin_state"]) &&
            isset($_POST["codplugin_c_number"]) &&
            isset($_POST["codplugin_price"])
        ) {

            global $woocommerce;
            $countries_obj = new WC_Countries();
            $countries = $countries_obj->__get("countries");
            $default_country = $countries_obj->get_base_country();

            $product_id = $_POST["product_id"];
            $full_name = $_POST["full_name"];
            $phone_number = $_POST["phone_number"];
            $full_address = $_POST["full_address"];
            $codplugin_state = $_POST["codplugin_state"];
            $codplugin_city = isset($_POST["codplugin_city"]) ? $_POST["codplugin_city"] : '';
            $count_number = $_POST["codplugin_c_number"];
            $product_price = $_POST["codplugin_price"];
            $d_price = $_POST["d_price"];
            $codplugin_d_method = $_POST["codplugin_d_method"];
            $total_price = (int) $count_number * (int) $product_price + (int) $d_price;
            $order_notes = isset($_POST['order_notes']) ? wc_clean($_POST['order_notes']) : ''; // Retrieve order notes
            if (isset($_POST["codplugin_email"])) {
                $codplugin_email = $_POST["codplugin_email"];
            }

            // --- Stock Check Logic ---
            $product_to_check_id = isset($_POST["var_id"]) && !empty($_POST["var_id"]) ? $_POST["var_id"] : $product_id;
            $product_to_check = wc_get_product($product_to_check_id);
            $requested_qty = (int) $count_number;

            if ($product_to_check && $product_to_check->managing_stock()) {
                $available_stock = $product_to_check->get_stock_quantity();

                // Check if enough stock is available
                if ($available_stock < $requested_qty) {
                    // Send error response and stop execution
                    wp_send_json_error(array(
                        'message' => sprintf(
                            /* translators: 1: available stock quantity, 2: product name */
                            __('Sorry, we only have %1$d of "%2$s" in stock. Please reduce the quantity.', 'napoleon'),
                            $available_stock,
                            $product_to_check->get_name()
                        )
                    ));
                    // die(); // wp_send_json_error includes die()
                }
            }
            // --- End Stock Check ---


            if ($default_country == "DZ") {
                $states_dz = array('Adrar' => '01 Adrar - أدرار', 'Chlef' => '02 Chlef - الشلف', 'Laghouat' => '03 Laghouat - الأغواط', 'Oum El Bouaghi' => '04 Oum El Bouaghi - أم البواقي', 'Batna' => '05 Batna - باتنة', 'Béjaïa' => '06 Béjaïa - بجاية', 'Biskra' => '07 Biskra - بسكرة', 'Bechar' => '08 Bechar - بشار', 'Blida' => '09 Blida - البليدة', 'Bouira' => '10 Bouira - البويرة', 'Tamanrasset' => '11 Tamanrasset - تمنراست ', 'Tébessa' => '12 Tébessa - تبسة ', 'Tlemcene' => '13 Tlemcene - تلمسان', 'Tiaret' => '14 Tiaret - تيارت', 'Tizi Ouzou' => '15 Tizi Ouzou - تيزي وزو', 'Alger' => '16 Alger - الجزائر', 'Djelfa' => '17 Djelfa - الجلفة', 'Jijel' => '18 Jijel - جيجل', 'Sétif' => '19 Sétif - سطيف', 'Saïda' => '20 Saïda - سعيدة', 'Skikda' => '21 Skikda - سكيكدة', 'Sidi Bel Abbès' => '22 Sidi Bel Abbès - سيدي بلعباس', 'Annaba' => '23 Annaba - عنابة', 'Guelma' => '24 Guelma - قالمة', 'Constantine' => '25 Constantine - قسنطينة', 'Médéa' => '26 Médéa - المدية', 'Mostaganem' => '27 Mostaganem - مستغانم', 'MSila' => '28 MSila - مسيلة', 'Mascara' => '29 Mascara - معسكر', 'Ouargla' => '30 Ouargla - ورقلة', 'Oran' => '31 Oran - وهران', 'El Bayadh' => '32 El Bayadh - البيض', 'Illizi' => '33 Illizi - إليزي ', 'Bordj Bou Arreridj' => '34 Bordj Bou Arreridj - برج بوعريريج', 'Boumerdès' => '35 Boumerdès - بومرداس', 'El Tarf' => '36 El Tarf - الطارف', 'Tindouf' => '37 Tindouf - تندوف', 'Tissemsilt' => '38 Tissemsilt - تيسمسيلت', 'Eloued' => '39 Eloued - الوادي', 'Khenchela' => '40 Khenchela - خنشلة', 'Souk Ahras' => '41 Souk Ahras - سوق أهراس', 'Tipaza' => '42 Tipaza - تيبازة', 'Mila' => '43 Mila - ميلة', 'Aïn Defla' => '44 Aïn Defla - عين الدفلى', 'Naâma' => '45 Naâma - النعامة', 'Aïn Témouchent' => '46 Aïn Témouchent - عين تموشنت', 'Ghardaïa' => '47 Ghardaïa - غرداية', 'Relizane' => '48 Relizane- غليزان', 'Timimoun' => '49 Timimoun - تيميمون', 'Bordj Baji Mokhtar' => '50 Bordj Baji Mokhtar - برج باجي مختار', 'Ouled Djellal' => '51 Ouled Djellal - أولاد جلال', 'Béni Abbès' => '52 Béni Abbès - بني عباس', 'Aïn Salah' => '53 Aïn Salah - عين صالح', 'In Guezzam' => '54 In Guezzam - عين قزام', 'Touggourt' => '55 Touggourt - تقرت', 'Djanet' => '56 Djanet - جانت', 'El MGhair' => '57 El MGhair - المغير', 'El Menia' => '58 El Menia - المنيعة', );

                $codplugin_state = array_search($codplugin_state, $states_dz);
            }

            $address = [
                "first_name" => $full_name,
                "phone" => $phone_number,
                "address_1" => $full_address,
                "state" => $codplugin_state,
                "city" => $codplugin_city,
                "email" => isset($codplugin_email) ? $codplugin_email : null,
            ];

            $order_data = array();
            if (is_user_logged_in()) {
                $order_data['customer_id'] = get_current_user_id();
            }

            $order = wc_create_order($order_data);

            if (isset($_POST["var_id"])) {
                $order->add_product(wc_get_product($_POST["var_id"]), $count_number);
            } else {
                $order->add_product(wc_get_product($product_id), $count_number);
            }

            // add shipping
            $shipping_rate_id = $codplugin_d_method; // This now holds something like 'local_pickup:130'
            $shipping_item = new \WC_Order_Item_Shipping();
            $shipping_item->set_total($d_price);

            // Find the actual shipping method instance to get its title and ID
            $rate_parts = explode(':', $shipping_rate_id);
            if (count($rate_parts) === 2) {
                $method_id = $rate_parts[0];
                $instance_id = $rate_parts[1];
                $shipping_method_instance = \WC_Shipping_Zones::get_shipping_method($instance_id);

                if ($shipping_method_instance) {
                    $shipping_item->set_method_title($shipping_method_instance->get_title());
                    $shipping_item->set_method_id($method_id); // Store the base method ID (e.g., 'local_pickup')
                    $shipping_item->set_instance_id($instance_id); // Store the instance ID
                } else {
                    // Fallback if method instance not found (shouldn't normally happen)
                    $shipping_item->set_method_title($shipping_rate_id);
                    $shipping_item->set_method_id($shipping_rate_id);
                }
            } else {
                // Fallback for unexpected format
                $shipping_item->set_method_title($shipping_rate_id);
                $shipping_item->set_method_id($shipping_rate_id);
            }

            // Add the shipping item to the order
            $order->add_item($shipping_item);

            $order->set_address($address, "billing");
            $order->set_address($address, "shipping");

            $payment_method_selected = isset($_POST['codplugin_payment_method']) ? wc_clean($_POST['codplugin_payment_method']) : 'cod';

            $order->set_address($address, "billing");
            $order->set_address($address, "shipping");

            // Set customer note if provided
            if (!empty($order_notes)) {
                $order->set_customer_note($order_notes);
            }

            // Calculate totals before processing payment, especially for gateways that might need it.
            $order->calculate_totals();
            // Save initially to get an order ID, payment gateway might update status later.
            $order->save();

            $order_id = $order->get_id();
            $order_key = $order->get_order_key();
            $nr_array = array("order_id" => $order_id, "order_key" => $order_key);

            // --- Delete Abandoned Order after successful real order creation ---
            // Retrieve abandoned order ID from POST data (sent from frontend)
            $abandoned_order_id_from_post = isset($_POST['abandoned_order_id']) ? absint($_POST['abandoned_order_id']) : 0;

            if ($abandoned_order_id_from_post > 0) {
                // Ensure it's not the same order being created (though unlikely)
                if ($abandoned_order_id_from_post !== $order_id) {
                    $abandoned_order_to_delete = wc_get_order($abandoned_order_id_from_post);

                    if ($abandoned_order_to_delete) {
                        if ($abandoned_order_to_delete->get_status() === 'pending') {
                            wp_delete_post($abandoned_order_id_from_post, true); // Permanently delete
                        }
                    }
                }
                // Clear the session variable as well, to be safe
                if (isset($_SESSION['current_abandoned_cart_id'])) {
                    unset($_SESSION['current_abandoned_cart_id']);
                }
            }
            // --- End Delete Abandoned Order ---

            if ($payment_method_selected === 'chargily_pay' && isset($_POST['chargily_pay_active_for_form'])) {
                $order->set_payment_method('chargily_pay');
                // Save payment method before calling process_payment
                $order->save();

                if (class_exists('WC_chargily_pay')) {
                    $chargily_gateway = new WC_chargily_pay();
                    $result = $chargily_gateway->process_payment($order_id);

                    if (isset($result['result']) && $result['result'] == 'success' && isset($result['redirect'])) {
                        $nr_array['redirect_url'] = $result['redirect'];
                        // Chargily's process_payment usually sets order to 'pending' or similar.
                        // No explicit status update here needed if Chargily handles it.
                    } else {
                        $nr_array['error'] = __('Chargily payment processing failed. Please try again or select Cash on Delivery.', 'napoleon');
                        $order->update_status('failed', __('Chargily payment failed via quick order form.', 'napoleon'));
                    }
                } else {
                    $nr_array['error'] = __('Chargily Pay gateway class not found.', 'napoleon');
                    $order->update_status('failed', __('Chargily Pay gateway class not found for quick order.', 'napoleon'));
                }
            } else {
                // Default to Cash on Delivery
                $order->set_payment_method('cod');
                $order->update_status('processing');
            }

            // Handle File Upload if enabled and file is present
            if (get_theme_mod('codform_enable_file_upload', false) && isset($_FILES['codform_uploaded_file']) && $_FILES['codform_uploaded_file']['error'] == UPLOAD_ERR_OK) {
                $uploaded_file = $_FILES['codform_uploaded_file'];
                $max_size_mb = get_theme_mod('codform_max_file_size_mb', 10);
                $max_size_bytes = $max_size_mb * 1024 * 1024;
                $allowed_types_str = get_theme_mod('codform_allowed_file_types', 'jpg,png,pdf');
                $allowed_types_array = !empty($allowed_types_str) ? array_map('trim', explode(',', $allowed_types_str)) : array();

                // Basic Validation
                if ($uploaded_file['size'] > $max_size_bytes) {
                    $nr_array['error'] = sprintf(__('File is too large. Maximum size is %s MB.', 'napoleon'), $max_size_mb);
                    // Potentially update order status to on-hold or failed if file upload is critical
                } else {
                    $file_type_info = wp_check_filetype(basename($uploaded_file['name']));
                    $file_extension = strtolower($file_type_info['ext']);

                    if (!empty($allowed_types_array) && !in_array($file_extension, $allowed_types_array)) {
                        $nr_array['error'] = sprintf(__('Invalid file type. Allowed types: %s.', 'napoleon'), $allowed_types_str);
                        // Potentially update order status
                    } else {
                        // WordPress upload overrides
                        $upload_overrides = array('test_form' => false);
                        $movefile = wp_handle_upload($uploaded_file, $upload_overrides);

                        if ($movefile && !isset($movefile['error'])) {
                            $attachment = array(
                                'guid' => $movefile['url'],
                                'post_mime_type' => $movefile['type'],
                                'post_title' => preg_replace('/\.[^.]+$/', '', basename($movefile['file'])),
                                'post_content' => '',
                                'post_status' => 'inherit'
                            );
                            $attach_id = wp_insert_attachment($attachment, $movefile['file'], $order_id);
                            if (!is_wp_error($attach_id)) {
                                wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $movefile['file']));
                                $order->add_meta_data('_codform_uploaded_file_id', $attach_id, true);
                                // Or save URL: $order->add_meta_data( '_codform_uploaded_file_url', $movefile['url'], true );
                            } else {
                                $nr_array['error'] = __('Error creating attachment for uploaded file.', 'napoleon') . ' ' . $attach_id->get_error_message();
                            }
                        } else {
                            $nr_array['error'] = __('Error uploading file:', 'napoleon') . ' ' . (isset($movefile['error']) ? $movefile['error'] : __('Unknown error', 'napoleon'));
                        }
                    }
                }
            }

            // Final save after all potential modifications
            $order->save();

            // If there was an error during file processing but not before, send it now.
            if (isset($nr_array['error']) && $order_id) {
                // We have an order ID, but an error occurred (likely file upload related)
                // We still want to return the order_id and order_key for the thank you page,
                // but also the error message.
                wp_send_json($nr_array); // This will include order_id, order_key, and the error
                die();
            }


            echo json_encode($nr_array);
            die();
        } else {
            // Handle cases where essential POST data is missing
            wp_send_json_error(array('message' => __('Missing required form data.', 'napoleon')));
            die();
        }
    }



    add_action(
        "wp_ajax_codplugin_add_upsell_product",
        "codplugin_add_upsell_product"
    );

    add_action(
        "wp_ajax_nopriv_codplugin_add_upsell_product",
        "codplugin_add_upsell_product"
    );


    function codplugin_add_upsell_product()
    {
        if (isset($_POST["order_id"]) && isset($_POST["product_id"])) {
            $order = wc_get_order($_POST["order_id"]);
            $order->add_product(wc_get_product($_POST["product_id"]), 1);
            $order->calculate_totals();

            $return = array(
                'message' => __('Saved', 'napoleon'),
                'ID' => 1
            );
            wp_send_json_success($return);
            // die();
        } else {
            wp_send_json_error();
        }
    }

endif;

// New AJAX action to get filtered communes from Bordrou Generator
add_action('wp_ajax_get_filtered_communes', 'get_bordrou_filtered_communes_action');
add_action('wp_ajax_nopriv_get_filtered_communes', 'get_bordrou_filtered_communes_action');


function get_bordrou_filtered_communes_action()
{
    if (!isset($_POST['wilaya_id'])) {
        wp_send_json_error(array('message' => 'Missing wilaya_id parameter.'));
        return;
    }

    $wilaya_id_param = sanitize_text_field($_POST['wilaya_id']);
    // Extract ID (e.g. "DZ-01" -> 1)
    // Assuming format is always DZ-XX
    $wilaya_key_numeric = (int) str_replace('DZ-', '', $wilaya_id_param);

    // Path to the JSON file
    $json_path = get_theme_file_path('/inc/json/baladiya.json');

    if (file_exists($json_path)) {
        $json_content = file_get_contents($json_path);
        $data = json_decode($json_content, true);

        if (is_array($data)) {
            // Find the cities for this wilaya ID (using "1", "2" etc keys)
            // JSON keys act as strings or numbers, but json_decode assoc=true helps.
            // We use the numeric key we extracted.
            if (isset($data[$wilaya_key_numeric])) {
                $communes_list = $data[$wilaya_key_numeric];

                // Format for frontend (array of objects with id/name or just array of strings?)
                // codplugin.js handles both:
                // 1. Array of strings: value=name, text=name
                // 2. Object/Array of objects: value=id, text=name
                // The DZ.php logic returns an array of strings (names). 
                // To keep it simple and consistent with how the JSON is structured (array of strings),
                // we will return an array of objects where id=name and name=name, 
                // OR just the array of strings if the JS supports it.
                // Looking at codplugin.js: if (Array.isArray(response.data)) ... if (typeof commune === 'string') ...
                // So returning the simple array of strings ['Adrar', 'Tamest'] works perfect.

                // However, to be extra safe and provide "id" structure if ever needed:
                $formatted_communes = array();
                foreach ($communes_list as $commune_name) {
                    $formatted_communes[] = array(
                        'id' => $commune_name,
                        'name' => $commune_name
                    );
                }

                wp_send_json_success($formatted_communes);
                return;

            } else {
                wp_send_json_error(array('message' => 'No communes found for Wilaya ID: ' . $wilaya_key_numeric));
                return;
            }
        } else {
            wp_send_json_error(array('message' => 'Error decoding baladiya.json'));
            return;
        }
    } else {
        wp_send_json_error(array('message' => 'baladiya.json file not found.'));
        return;
    }
}

