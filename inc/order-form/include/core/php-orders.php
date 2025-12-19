<?php



if (get_theme_mod('create_orders_with_php', 0) == 0) {
    return;
}

/**
 *  Create new orders with php
 */

add_action('init', 'napoleon_order_form_submission');

// Create new orders through php
function napoleon_order_form_submission()
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

    global $woocommerce;

    $countries_obj = new WC_Countries();
    $countries = $countries_obj->__get("countries");
    $default_country = $countries_obj->get_base_country();

    //  if (isset($_POST['codplugin-submit'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Verify the nonce field to prevent CSRF attacks
        if (isset($_POST['rwc-form-nonce'])) {
            // Get the form data

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

            if (isset($_POST["codplugin_email"])) {
                $codplugin_email = $_POST["codplugin_email"];
            }


            if ($default_country == "DZ") {
                $states_dz = array(
                    'DZ-01' => '01 Adrar - أدرار',
                    'DZ-02' => '02 Chlef - الشلف',
                    'DZ-03' => '03 Laghouat - الأغواط',
                    'DZ-04' => '04 Oum El Bouaghi - أم البواقي',
                    'DZ-05' => '05 Batna - باتنة',
                    'DZ-06' => '06 Béjaïa - بجاية',
                    'DZ-07' => '07 Biskra - بسكرة',
                    'DZ-08' => '08 Bechar - بشار',
                    'DZ-09' => '09 Blida - البليدة',
                    'DZ-10' => '10 Bouira - البويرة',
                    'DZ-11' => '11 Tamanrasset - تمنراست ',
                    'DZ-12' => '12 Tébessa - تبسة ',
                    'DZ-13' => '13 Tlemcene - تلمسان',
                    'DZ-14' => '14 Tiaret - تيارت',
                    'DZ-15' => '15 Tizi Ouzou - تيزي وزو',
                    'DZ-16' => '16 Alger - الجزائر',
                    'DZ-17' => '17 Djelfa - الجلفة',
                    'DZ-18' => '18 Jijel - جيجل',
                    'DZ-19' => '19 Sétif - سطيف',
                    'DZ-20' => '20 Saïda - سعيدة',
                    'DZ-21' => '21 Skikda - سكيكدة',
                    'DZ-22' => '22 Sidi Bel Abbès - سيدي بلعباس',
                    'DZ-23' => '23 Annaba - عنابة',
                    'DZ-24' => '24 Guelma - قالمة',
                    'DZ-25' => '25 Constantine - قسنطينة',
                    'DZ-26' => '26 Médéa - المدية',
                    'DZ-27' => '27 Mostaganem - مستغانم',
                    'DZ-28' => '28 MSila - مسيلة',
                    'DZ-29' => '29 Mascara - معسكر',
                    'DZ-30' => '30 Ouargla - ورقلة',
                    'DZ-31' => '31 Oran - وهران',
                    'DZ-32' => '32 El Bayadh - البيض',
                    'DZ-33' => '33 Illizi - إليزي ',
                    'DZ-34' => '34 Bordj Bou Arreridj - برج بوعريريج',
                    'DZ-35' => '35 Boumerdès - بومرداس',
                    'DZ-36' => '36 El Tarf - الطارف',
                    'DZ-37' => '37 Tindouf - تندوف',
                    'DZ-38' => '38 Tissemsilt - تيسمسيلت',
                    'DZ-39' => '39 Eloued - الوادي',
                    'DZ-40' => '40 Khenchela - خنشلة',
                    'DZ-41' => '41 Souk Ahras - سوق أهراس',
                    'DZ-42' => '42 Tipaza - تيبازة',
                    'DZ-43' => '43 Mila - ميلة',
                    'DZ-44' => '44 Aïn Defla - عين الدفلى',
                    'DZ-45' => '45 Naâma - النعامة',
                    'DZ-46' => '46 Aïn Témouchent - عين تموشنت',
                    'DZ-47' => '47 Ghardaïa - غرداية',
                    'DZ-48' => '48 Relizane- غليزان',
                    'DZ-49' => '49 Timimoun - تيميمون',
                    'DZ-50' => '50 Bordj Baji Mokhtar - برج باجي مختار',
                    'DZ-51' => '51 Ouled Djellal - أولاد جلال',
                    'DZ-52' => '52 Béni Abbès - بني عباس',
                    'DZ-53' => '53 Aïn Salah - عين صالح',
                    'DZ-54' => '54 In Guezzam - عين قزام',
                    'DZ-55' => '55 Touggourt - تقرت',
                    'DZ-56' => '56 Djanet - جانت',
                    'DZ-57' => '57 El MGhair - المغير',
                    'DZ-58' => '58 El Menia - المنيعة'
                );

                $codplugin_state = array_search($codplugin_state, $states_dz);
            }

            // --- Stock Check Logic ---
            // --- Stock Check Logic ---
            $is_cart_mode = isset($_POST['codplugin_is_cart']) && $_POST['codplugin_is_cart'] == 1;

            if ($is_cart_mode) {
                // Check stock for ALL items in cart
                if (WC()->cart->is_empty()) {
                    wc_add_notice(__('Your cart is empty.', 'napoleon'), 'error');
                    return;
                }

                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $item_product = $cart_item['data'];
                    if ($item_product && $item_product->managing_stock()) {
                        $available_stock = $item_product->get_stock_quantity();
                        $requested_qty = $cart_item['quantity'];

                        if ($available_stock < $requested_qty) {
                            wc_add_notice(sprintf(
                                __('Sorry, we only have %1$d of "%2$s" in stock. Please reduce the quantity.', 'napoleon'),
                                $available_stock,
                                $item_product->get_name()
                            ), 'error');
                            return;
                        }
                    }
                }

            } else {
                // Existing Single Product Stock Logic
                $product_to_check_id = isset($_POST["var_id"]) && !empty($_POST["var_id"]) ? $_POST["var_id"] : $product_id;
                $product_to_check = wc_get_product($product_to_check_id);
                $requested_qty = (int) $count_number;

                if ($product_to_check && $product_to_check->managing_stock()) {
                    $available_stock = $product_to_check->get_stock_quantity();

                    // Check if enough stock is available
                    if ($available_stock < $requested_qty) {
                        // Add an error notice and stop processing
                        wc_add_notice(sprintf(
                            /* translators: 1: available stock quantity, 2: product name */
                            __('Sorry, we only have %1$d of "%2$s" in stock. Please reduce the quantity.', 'napoleon'),
                            $available_stock,
                            $product_to_check->get_name()
                        ), 'error');
                        return; // Stop processing the order
                    }
                }
            }
            // --- End Stock Check ---

            $address = [
                "first_name" => $full_name,
                "phone" => $phone_number,
                "address_1" => $full_address,
                "state" => $codplugin_state,
                "city" => $codplugin_city,
                "email" => isset($codplugin_email) ? $codplugin_email : null,

            ];

            // Create a new WooCommerce order
            $order_data = array();
            if (is_user_logged_in()) {
                $order_data['customer_id'] = get_current_user_id();
            }

            $order = wc_create_order($order_data);


            if ($order) {

                if ($is_cart_mode) {
                    // Add all cart items to order
                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                        $order->add_product($cart_item['data'], $cart_item['quantity']);
                    }
                    // IMPORTANT: Empty the cart after order is created from it
                    WC()->cart->empty_cart();

                } else {
                    if (isset($_POST["var_id"])) {
                        $order->add_product(wc_get_product($_POST["var_id"]), $count_number);
                    } else {
                        $order->add_product(wc_get_product($product_id), $count_number);
                    }
                }

                // add shipping
                $shipping_rate_id = $codplugin_d_method; // This holds something like 'local_pickup:144'
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
                        // Fallback if method instance not found
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


                // Update shipping address

                $order->set_address($address, "billing");
                $order->set_address($address, "shipping");


                $payment_method_selected = isset($_POST['codplugin_payment_method']) ? wc_clean($_POST['codplugin_payment_method']) : 'cod';

                $order->calculate_totals();
                $order->save(); // Save once to get ID and ensure totals are calculated

                // Order Note (ensure it's saved if not already handled by Chargily flow exit)
                if (isset($_POST['order_notes']) && !empty($_POST['order_notes'])) {
                    $order->set_customer_note($_POST['order_notes']);
                }

                // Handle File Upload if enabled and file is present
                if (get_theme_mod('codform_enable_file_upload', false) && isset($_FILES['codform_uploaded_file']) && $_FILES['codform_uploaded_file']['error'] == UPLOAD_ERR_OK) {
                    $uploaded_file = $_FILES['codform_uploaded_file'];
                    $max_size_mb = get_theme_mod('codform_max_file_size_mb', 10);
                    $max_size_bytes = $max_size_mb * 1024 * 1024;
                    $allowed_types_str = get_theme_mod('codform_allowed_file_types', 'jpg,png,pdf');
                    $allowed_types_array = !empty($allowed_types_str) ? array_map('trim', explode(',', $allowed_types_str)) : array();
                    $file_upload_error = false;

                    // Basic Validation
                    if ($uploaded_file['size'] > $max_size_bytes) {
                        wc_add_notice(sprintf(__('File is too large. Maximum size is %s MB.', 'napoleon'), $max_size_mb), 'error');
                        $file_upload_error = true;
                    } else {
                        $file_type_info = wp_check_filetype(basename($uploaded_file['name']));
                        $file_extension = strtolower($file_type_info['ext']);

                        if (!empty($allowed_types_array) && !in_array($file_extension, $allowed_types_array)) {
                            wc_add_notice(sprintf(__('Invalid file type. Allowed types: %s.', 'napoleon'), $allowed_types_str), 'error');
                            $file_upload_error = true;
                        } else {
                            // WordPress upload overrides
                            $upload_overrides = array('test_form' => false);
                            // Before calling wp_handle_upload, ensure the uploads directory is writable or create it.
                            // WordPress usually handles this, but good to be aware.
                            $movefile = wp_handle_upload($uploaded_file, $upload_overrides);

                            if ($movefile && !isset($movefile['error'])) {
                                $attachment = array(
                                    'guid' => $movefile['url'],
                                    'post_mime_type' => $movefile['type'],
                                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($movefile['file'])),
                                    'post_content' => '',
                                    'post_status' => 'inherit'
                                );
                                $attach_id = wp_insert_attachment($attachment, $movefile['file'], $order->get_id());
                                if (!is_wp_error($attach_id)) {
                                    wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $movefile['file']));
                                    $order->add_meta_data('_codform_uploaded_file_id', $attach_id, true);
                                } else {
                                    wc_add_notice(__('Error creating attachment for uploaded file: ', 'napoleon') . $attach_id->get_error_message(), 'error');
                                    $file_upload_error = true;
                                }
                            } else {
                                wc_add_notice(__('Error uploading file: ', 'napoleon') . (isset($movefile['error']) ? $movefile['error'] : __('Unknown error', 'napoleon')), 'error');
                                $file_upload_error = true;
                            }
                        }
                    }
                    // If a file upload error occurred, you might want to change order status or prevent redirect
                    // For now, it will proceed and show notices on the thank you page.
                }

                // Save order notes and file meta before payment processing
                $order->save();

                if ($payment_method_selected === 'chargily_pay' && isset($_POST['chargily_pay_active_for_form'])) {
                    $order->set_payment_method('chargily_pay');
                    $order->save(); // Save payment method

                    if (class_exists('WC_chargily_pay')) {
                        $chargily_gateway = new WC_chargily_pay();
                        $result = $chargily_gateway->process_payment($order->get_id());

                        if (isset($result['result']) && $result['result'] == 'success' && isset($result['redirect'])) {
                            // Order status is typically set to 'pending' by Chargily's process_payment
                            wp_redirect($result['redirect']);
                            exit;
                        } else {
                            wc_add_notice(__('Chargily payment processing failed. Please try again or select Cash on Delivery.', 'napoleon'), 'error');
                            $order->update_status('failed', __('Chargily payment failed via quick order form.', 'napoleon'));
                            // Fall through to standard thank you page, which will show the notice
                        }
                    } else {
                        wc_add_notice(__('Chargily Pay gateway class not found.', 'napoleon'), 'error');
                        $order->update_status('failed', __('Chargily Pay gateway class not found for quick order.', 'napoleon'));
                        // Fall through
                    }
                } else {
                    // Default to Cash on Delivery
                    $order->set_payment_method('cod');
                    $order->update_status('processing');
                }

                // Final save for COD or if Chargily failed and fell through
                $order->save();

                // Redirect to thank you page for COD or failed Chargily
                $order_received_url = wc_get_endpoint_url('order-received', $order->get_id(), wc_get_checkout_url());
                $order_received_url = add_query_arg('key', $order->get_order_key(), $order_received_url);
                $order_received_url = apply_filters('woocommerce_get_checkout_order_received_url', $order_received_url, $order);

                wp_redirect($order_received_url);
                exit; // Ensure script stops after redirect

            } else {
                // Handle order creation failure
                wc_add_notice(__('There was an issue creating your order. Please try again.', 'napoleon'), 'error');
                // Optionally redirect to cart or product page
                // wp_redirect(wc_get_cart_url());
                // exit;
            }

        }  // End nonce check
    } // End POST check
}




/**
 *  Display One click upsell in thank you page
 */
function add_upsells_to_order()
{

    if (isset($_POST["order_id"]) && isset($_POST["product_id"])) {
        $order = wc_get_order($_POST["order_id"]);
        $order->add_product(wc_get_product($_POST["product_id"]), 1);
        $order->calculate_totals();
    }
    wp_die();
}

add_action('wp_ajax_add_upsells_to_order', 'add_upsells_to_order');
add_action('wp_ajax_nopriv_add_upsells_to_order', 'add_upsells_to_order');



function order_upsells_popup()
{

    if (get_theme_mod('show_upsells', 0) == 1 && is_order_received_page()) {

        $order_id = get_query_var('order-received');
        $order = wc_get_order($order_id);

        if ($order) {
            // Loop through order items
            foreach ($order->get_items() as $item_id => $item) {
                // Get the product ID
                $product_id = $item->get_product_id();

                // Get the product object
                $product = wc_get_product($product_id);

                // Check if the product has upsells
                $upsell_ids = $product->get_upsell_ids();

                if (!empty($upsell_ids)) {
                    break;
                }
            }

            if (!empty($upsell_ids)) {
                $upsell_id = $upsell_ids[0]; ?>
                <div id="cod-upsell">
                    <div id="cod-upsell-box">
                        <h2 class="cod-upsell-heading">
                            <?php if (get_theme_mod('upsell_title')) {
                                echo esc_html(get_theme_mod('upsell_title'));
                            } else {
                                echo esc_html__('Wait! Your order is not completed!', 'napoleon');
                            } ?>
                        </h2>
                        <div class="cod-upsell-product">
                            <div class="cod-upsell-product-title">
                                <?php $upsell = wc_get_product($upsell_id);
                                echo $upsell->get_title(); ?>
                            </div>
                            <img src="<?php echo get_the_post_thumbnail_url($upsell_id); ?>" class="img-responsive" alt="" />
                            <span class="price">
                                <?php echo $upsell->get_price_html(); ?>
                            </span>
                            <div id="upsell-submit">
                                <input type="hidden" id="upsell_product_id" name="upsell_product_id" value="<?php echo $upsell_id; ?>">
                                <input type="hidden" id="current_order_id" name="current_order_id" value="<?php echo $order_id; ?>">
                                <button id="cod-add-upsell"><?php _e('Add To Cart', 'napoleon'); ?></button>
                                <button id="cod-upsell-cancel"><?php _e('No, Thanks', 'napoleon'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    #upsell-submit button,
                    .cod-upsell-product img {
                        display: block;
                        margin: 20px auto
                    }

                    #cod-upsell {
                        display: none;
                        position: fixed;
                        z-index: 25;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        overflow: auto;
                        background-color: rgba(0, 0, 0, .5)
                    }

                    @keyframes dropDown {
                        from {
                            transform: translateY(-100%);
                            opacity: 0
                        }

                        to {
                            transform: translateY(0);
                            opacity: 1
                        }
                    }

                    #cod-upsell-box {
                        background-color: #fff;
                        margin: 5% auto;
                        padding: 20px;
                        border: 1px solid #888;
                        width: 90%;
                        animation: .5s ease-in-out dropDown
                    }

                    #cod-upsell-box .cod-upsell-heading {
                        color: red;
                        text-align: center;
                        font-weight: 700;
                        font-size: 45px;
                        margin: 0 0 20px;
                        padding-bottom: 20px;
                        border-bottom: 3px dotted #eee
                    }

                    .cod-upsell-product-title {
                        text-align: center;
                        font-size: 24px;
                        color: #000
                    }

                    .cod-upsell-product .price {
                        display: block;
                        text-align: center;
                        color: red;
                        margin-bottom: 25px
                    }

                    #upsell-submit button {
                        color: #fff;
                        background-color: #4caf50;
                        padding: 15px 50px;
                        font-size: 30px;
                        border: none
                    }

                    #upsell-submit #cod-upsell-cancel {
                        background-color: transparent;
                        color: #bababa;
                        padding: 0;
                        font-size: 20px
                    }

                    #upsell-submit button:hover {
                        cursor: pointer
                    }

                    #upsell-submit #cod-add-upsell:hover {
                        background-color: #222
                    }

                    #upsell-submit #cod-upsell-cancel:hover {
                        color: #555
                    }
                </style>

                <script>
                    jQuery(document).ready(function ($) {

                        var popupShown = localStorage.getItem('popupShown');

                        if (!popupShown) {
                            $('#cod-upsell').fadeIn();
                            localStorage.setItem('popupShown', true);
                        }

                        $("#cod-upsell-cancel").click(function () {
                            $("#cod-upsell").fadeOut();
                        });
                        $('#cod-add-upsell').on('click', function () {
                            productID = $("#upsell_product_id").val();
                            orderID = $("#current_order_id").val();

                            $.ajax({
                                type: 'POST',
                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                data: {
                                    action: 'add_upsells_to_order',
                                    order_id: orderID,
                                    product_id: productID,
                                },
                                success: function (response) {
                                    location.reload();
                                },
                                error: function (error) {
                                    // Handle the error response
                                    console.error(error);
                                }
                            });
                        });
                    });
                </script>

            <?php }
        }
    }
}
add_action('wp_footer', 'order_upsells_popup');
