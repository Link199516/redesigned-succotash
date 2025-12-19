<?php


function codplugin_checkout_form($product_id)
{
    $is_cart_mode = empty($product_id);

    $product = null;
    $pid = 0;
    $product_price = 0;
    $product_currency = '';
    $product_type = 'simple';
    $max_qty_attr = '';
    $_product = null;

    if (!$is_cart_mode) {
        $product = wc_get_product($product_id);
        if (!$product)
            return; // specific safety
        $pid = $product->get_id();
        $_product = wc_get_product($pid);
        $product_price = $_product->get_price();
        $product_currency = $product->get_price_html();
        $product_type = $product->get_type(); // Use get_type() method


        // --- Determine Max Quantity for Data Attribute ---
        $product_for_stock = $product; // Use the main product by default

        // If it's a variable product, try to get the default variation for initial stock check
        // Note: JS will need to update this if a different variation is selected
        if ($product_type === 'variable') {
            $default_attributes = $product->get_default_attributes();
            $variations = $product->get_available_variations();
            $default_variation_id = 0;

            // Find default variation ID based on default attributes
            foreach ($variations as $variation_data) {
                $is_default = true;
                foreach ($default_attributes as $key => $value) {
                    if ($variation_data['attributes']['attribute_' . $key] !== $value) {
                        $is_default = false;
                        break;
                    }
                }
                if ($is_default) {
                    $default_variation_id = $variation_data['variation_id'];
                    break;
                }
            }

            if ($default_variation_id) {
                $default_variation_product = wc_get_product($default_variation_id);
                if ($default_variation_product) {
                    $product_for_stock = $default_variation_product;
                }
            } else {
                // If no default variation, check if parent manages stock
                if ($product->managing_stock()) {
                    $product_for_stock = $product;
                } else {
                    $product_for_stock = null; // Cannot determine stock without variation selected
                }
            }
        }

        // Get max quantity if stock is managed for the determined product/variation
        if ($product_for_stock && $product_for_stock->managing_stock()) {
            $max_qty = $product_for_stock->get_stock_quantity();
            // Only add attribute if stock is a number >= 0
            if (is_numeric($max_qty) && $max_qty >= 0) {
                $max_qty_attr = ' data-max-qty="' . esc_attr($max_qty) . '"';
                // Also add data attribute for the product/variation ID being checked initially
                $max_qty_attr .= ' data-stock-pid="' . esc_attr($product_for_stock->get_id()) . '"';
            }
        }
        // --- End Determine Max Quantity ---
    } else {
        // Cart Mode Logic
        $product_price = WC()->cart->get_total('edit'); // Unformatted total
        // We will need to output cart total logic instead of single product logic
    }


    global $woocommerce;
    $countries_obj = new WC_Countries();
    $countries = $countries_obj->__get("countries");
    $default_country = $countries_obj->get_base_country();
    //$codplugin_country_shortname = get_option("codplugin_country_shortname");
    $default_county_states = $countries_obj->get_states($default_country);

    $delivery_zones = WC_Shipping_Zones::get_zones();

    // Get all your existing shipping zones IDS
    $zone_ids = array_keys([""] + WC_Shipping_Zones::get_zones());


    // Loop through shipping Zones IDs

    $everyprice = 0;

    foreach ($zone_ids as $zone_id) {
        // Get the shipping Zone object
        $shipping_zone = new WC_Shipping_Zone($zone_id);

        // Get all shipping method values for the shipping zone
        $shipping_methods = $shipping_zone->get_shipping_methods(true, "values");

        // Loop through each shipping methods set for the current shipping zone
        foreach ($shipping_methods as $instance_id) {

            if ('free_shipping' === $instance_id->id) {
                $everyprice = 0;

            } else {

                $everyprice = $instance_id->cost;
            }
        }
    }


    $checkout_page_id = wc_get_page_id('checkout');
    $codplugin_thanks_url = $checkout_page_id ? get_permalink($checkout_page_id) : '';

    // Get layout preset setting
    $layout_preset = get_theme_mod('codform_layout_preset', 'default');

    // Check if Chargily Pay should be enabled for this form
    $chargily_enabled_for_cod = false;
    if (get_theme_mod('codplugin_enable_chargily_pay', false)) {
        if (class_exists('WC_Payment_Gateways') && class_exists('WC_chargily_pay')) {
            $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
            if (isset($available_gateways['chargily_pay'])) {
                $chargily_enabled_for_cod = true;
            }
        }
    }

    ?>

    <div id="codplugin-checkout">
        <?php if (get_theme_mod('add_info')): ?>
            <div class="codplugin-checkout-title">
                <h3><?php echo esc_html(get_theme_mod('add_info')); ?></h3>
            </div>
        <?php endif; ?>


        <form id="codplugin_woo_single_form" class="checkout woocommerce-checkout" <?php if (get_theme_mod('create_orders_with_php', 0) == 1)
            echo 'method="POST"'; ?>     <?php if (get_theme_mod('create_orders_with_php', 0) == 1 && get_theme_mod('codform_enable_file_upload', false))
                       echo 'enctype="multipart/form-data"'; ?>>

            <input type="hidden" id="rwc-form-nonce" name="rwc-form-nonce"
                value="<?php echo wp_create_nonce('rwc_form_nonce'); ?>">
            <input type="hidden" id="codplugin_thankyou_url" value="<?php echo $codplugin_thanks_url; ?>">
            <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
            <?php if ($is_cart_mode): ?>
                <input type="hidden" name="codplugin_is_cart" value="1">
                <input type="hidden" name="codplugin_cart_total" value="<?php echo WC()->cart->get_total('edit'); ?>">
            <?php endif; ?>
            <input type="hidden" name="everyprice" id="everyprice" value="<?php echo $everyprice; ?>">
            <input type="hidden" name="d_price" id="d_price">
            <input type="hidden" name="codplugin_c_number" id="codplugin_c_number" value="1">
            <input type="hidden" name="codplugin_d_method" id="codplugin_d_method">
            <?php if ($chargily_enabled_for_cod): ?>
                <input type="hidden" name="chargily_pay_active_for_form" value="1">
            <?php endif; ?>

            <input type="text" name="full_name"
                placeholder="<?php echo esc_html(get_theme_mod('form_full_name', __('Full Name', 'napoleon'))); ?>"
                required autocomplete="off">

            <input type="tel" <?php if (get_theme_mod('codform_tel_settings')) {
                echo get_theme_mod('codform_tel_settings');
            } ?> name="phone_number"
                placeholder="<?php echo esc_html(get_theme_mod('form_phone', __('Phone Number', 'napoleon'))); ?>"
                required autocomplete="off">


            <?php if (!empty($default_county_states)):
                if (get_theme_mod('display_city_field', 0) == 1 && file_exists(get_theme_file_path() . '/inc/order-form/include/states/' . $default_country . '.php')):

                    do_action('codplugin_state_city');

                else: ?>
                    <select id='codplugin_state' name="codplugin_state" required>
                        <option value="" disabled selected hidden>
                            <?php echo esc_html(get_theme_mod('form_state', __('State', 'napoleon'))); ?>

                        </option>
                        <?php foreach ($default_county_states as $state) {
                            echo "<option id='codplugin_state_click'>" . $state . "</option>";
                        } ?>
                    </select>

                <?php endif;

            else: ?>

                <input class="has-no-states" type="text" name="codplugin_state"
                    placeholder="<?php echo esc_html(get_theme_mod('form_state', __('State', 'napoleon'))); ?>" required
                    autocomplete="off">
            <?php endif; ?>


            <?php if (get_theme_mod('hide_address_field', 1) == 0): ?>

                <input type="text" name="full_address"
                    placeholder="<?php echo esc_html(get_theme_mod('form_address', __('Full Address', 'napoleon'))); ?>"
                    required autocomplete="off">

            <?php else: ?>

                <input type="hidden" name="full_address">

            <?php endif; ?>

            <?php // File Upload Field
                if (get_theme_mod('codform_enable_file_upload', false)):
                    $allowed_file_types_str = get_theme_mod('codform_allowed_file_types', 'jpg,png,pdf');
                    $allowed_file_types_array = !empty($allowed_file_types_str) ? array_map('trim', explode(',', $allowed_file_types_str)) : array();
                    $accept_attribute_parts = array();
                    foreach ($allowed_file_types_array as $type) {
                        // Convert simple extensions to MIME types or image/*, application/* etc.
                        if (in_array($type, array('jpg', 'jpeg', 'png', 'gif', 'webp'))) {
                            $accept_attribute_parts[] = 'image/' . ($type === 'jpg' ? 'jpeg' : $type);
                        } elseif ($type === 'pdf') {
                            $accept_attribute_parts[] = 'application/pdf';
                        } elseif ($type === 'doc') {
                            $accept_attribute_parts[] = 'application/msword';
                        } elseif ($type === 'docx') {
                            $accept_attribute_parts[] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                        }
                        // Add more specific MIME types if needed, or keep it simple with extensions
                        if (!str_contains($type, '/')) { // If it's a simple extension like 'txt'
                            $accept_attribute_parts[] = '.' . $type;
                        }
                    }
                    $accept_attribute = implode(',', array_unique($accept_attribute_parts));
                    $is_required = get_theme_mod('codform_file_upload_required', false);
                    $file_upload_label_text = get_theme_mod('codform_file_upload_label', __('Upload File', 'napoleon'));
                    $trimmed_label_text = trim($file_upload_label_text);
                    ?>
                <div class="codform-file-upload-field-container">
                    <?php if (!empty($trimmed_label_text)): ?>
                        <label for="codform_uploaded_file" class="codform-file-upload-label">
                            <?php echo esc_html($file_upload_label_text); ?>
                            <?php if ($is_required):  // Asterisk shown if required and label is not empty ?>
                                <span class="required" style="color:red; font-weight:bold; margin-left: 5px;">*</span>
                            <?php endif; ?>
                        </label>
                    <?php endif; ?>
                    <div class="codform-custom-file-input">
                        <button type="button" class="codform-file-upload-button"><i class="fa-solid fa-file-arrow-up"></i>
                            <?php echo esc_html(get_theme_mod('codform_file_upload_button_text', __('Choose File', 'napoleon'))); ?></button>
                        <span class="codform-file-upload-filename"><?php esc_html_e('No file chosen', 'napoleon'); ?></span>
                        <input type="file" name="codform_uploaded_file" id="codform_uploaded_file"
                            accept="<?php echo esc_attr($accept_attribute); ?>" style="display: none;" <?php if ($is_required) {
                                   echo 'required';
                               } ?>>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (get_theme_mod('hide_order_notes', 0) == 1): ?>

                <textarea id="order-notes" name="order_notes"
                    placeholder="<?php echo esc_html(get_theme_mod('form_order_notes', __('Add Note', 'napoleon'))); ?>"
                    rows="1" cols="50"></textarea>

            <?php else: ?>

                <input type="hidden" name="order_notes">

            <?php endif; ?>

            <?php if (get_theme_mod('hide_email_field', 1) == 0): ?>

                <input type="email" name="codplugin_email"
                    placeholder="<?php echo esc_html(get_theme_mod('form_email', __('Email', 'napoleon'))); ?>" required
                    autocomplete="off">

            <?php endif; ?>






        </form>

        <?php // Conditionally output shipping methods below form for preset 2 ?>
        <?php if ($layout_preset === 'preset2'): ?>
            <div id="codplugin-preset2-shipping" class="codplugin-preset2-shipping">
                <?php /* <h3 class="shipping-preset2-title"><?php _e('Shipping Method', 'napoleon'); ?></h3> <?php // Title Removed ?> */ ?>
                <div id="shipping-methods"></div>
            </div>
        <?php endif; ?>


        <?php


        if ($product_type == 'variable'):

            // Check if we should use the Napoleon default variations for this product
            $use_napoleon_default = get_post_meta($pid, '_napoleon_use_default_variations', true);

            // Show swatches only if CFVSW plugin is active AND the checkbox is NOT checked
            if (defined('CFVSW_GLOBAL') && is_product() && !$use_napoleon_default):

                woocommerce_template_single_add_to_cart();

                ?>

                <input type="hidden" name="var_id" id="var_id" form="codplugin_woo_single_form">
                <input type="hidden" name="codplugin_price" id="codplugin_price" form="codplugin_woo_single_form">
                <?php

            else:

                $variations = $product->get_available_variations();
                $default_attributes = $product->get_default_attributes(); ?>

                <div
                    class="variation-prices <?php echo (get_theme_mod('enable_variation_styling', 0) == 1) ? 'radio-variation-prices' : ''; ?>">
                    <table>
                        <?php
                        foreach ($variations as $variation) {

                            $attributes = wc_get_formatted_variation($variation['attributes'], true, false);

                            $found_variations = 0;

                            foreach ($default_attributes as $key => $default_attribute_value) {

                                if ($variation['attributes']['attribute_' . $key] == $default_attribute_value) {
                                    $found_variations++;
                                }
                            }

                            if (count($default_attributes) == $found_variations) {
                                $default_variation_id = $variation['variation_id'];

                            }

                            $is_most_popular = get_post_meta($variation['variation_id'], '_most_popular_variation', true);

                            if ('yes' === $is_most_popular) {
                                // Don't auto-select most popular variation - just show badge
                                $default_var_id = "";
                                $badge_text = get_post_meta($variation['variation_id'], '_most_popular_badge_text', true);
                                if (empty($badge_text)) {
                                    $badge_text = __('Most Popular', 'napoleon');
                                }
                                $best_offer_label = "<span class='most-popular-badge'>" . esc_html($badge_text) . "</span>";
                            } else {
                                $default_var_id = "";
                                $best_offer_label = "";
                            }

                            $regular_price = wc_price($variation['display_regular_price']);
                            $sale_price = wc_price($variation['display_price']);

                            $price_html = $sale_price;
                            if ($variation['display_regular_price'] !== $variation['display_price']) {
                                $price_html .= '<del class="crossed-price">' . $regular_price . '</del>';
                            }


                            echo '<tr class="' . $default_var_id . '"><td><input type="radio" required name="var_price" id="' . $variation['variation_id'] . '" value="' . $variation['display_price'] . '" data-regular-price="' . $variation['display_regular_price'] . '" ' . $default_var_id . ' >' . $best_offer_label . '</td><td>' . $attributes . '</td><td>' . $price_html . '</td></tr>';
                        } ?>

                    </table>
                </div>


                <input type="hidden" name="var_id" id="var_id" form="codplugin_woo_single_form"
                    value="<?php echo isset($default_variation_id) ? $default_variation_id : ''; ?>">
                <input type="hidden" name="codplugin_price" id="codplugin_price" form="codplugin_woo_single_form"
                    value="<?php echo isset($default_variation_id) ? wc_get_product($default_variation_id)->get_price() : ''; ?>">

                <?php
            endif; ?>



        <?php else: ?>

            <input type="hidden" name="codplugin_price" id="codplugin_price" value="<?php echo $product_price; ?>"
                form="codplugin_woo_single_form">

        <?php endif; ?>

        <div id="codplugin_gif">
            <img loading="lazy"
                src="<?php echo get_template_directory_uri() . "/inc/order-form/include/assets/img/processing.gif"; ?>" />
        </div>

        <?php // START: Added Stock Display Above Buttons
            // Only show for non-variable products AND if stock management is enabled
            if ($product_type !== 'variable' && $product_for_stock && $product_for_stock->managing_stock()):
                $initial_stock_display = __('N/A', 'napoleon'); // Default text
                // Note: $max_qty is already calculated based on managing_stock() earlier, 
                // so we can directly use it if available.
                if (isset($max_qty) && is_numeric($max_qty) && $max_qty >= 0) {
                    $initial_stock_display = $max_qty;
                } elseif ($product_for_stock && !$product_for_stock->managing_stock()) {
                    // Optionally display different text if stock isn't managed
                    // $initial_stock_display = __('Unlimited', 'napoleon');
                }
                // Define inline styles - Adjusted for position above buttons
                $stock_div_style = 'margin-bottom: 10px; font-size: 0.95em; color: #333;'; // Adjusted style - text-align removed
                $stock_value_style = 'color: green; font-weight: bold;'; // Style for the value part
                ?>
            <div class="codplugin-available-stock" style="<?php echo esc_attr($stock_div_style); ?>">
                <?php _e('Availability:', 'napoleon'); ?> <span class="stock-value"
                    style="<?php echo esc_attr($stock_value_style); ?>"><?php echo esc_html($initial_stock_display); ?>
                    <?php _e('in stock', 'napoleon'); ?></span>
            </div>
        <?php
            endif; // END: Added Stock Display Above Buttons
            ?>

        <div class="form-footer clear <?php echo $_product->is_sold_individually() ? 'sold-individual' : ''; ?>">

            <?php if (!$_product->is_sold_individually()): ?>
                <div class="form-qte" <?php echo $max_qty_attr; // Add data attribute here ?>>
                    <span id="codplugin_add_button">+</span>
                    <span id="codplugin_count_button">1</span>
                    <span id="codplugin_remove_button">-</span>

                </div>
            <?php endif; ?>

            <?php // START: Added Stock Display
                $initial_stock_display = __('N/A', 'napoleon'); // Default text
                if (isset($max_qty) && is_numeric($max_qty) && $max_qty >= 0) {
                    $initial_stock_display = $max_qty;
                } elseif ($product_for_stock && !$product_for_stock->managing_stock()) {
                    // Optionally display different text if stock isn't managed
                    // $initial_stock_display = __('Unlimited', 'napoleon');
                }
                // Define inline styles - Adjusted for better layout within grid
                $stock_div_style = 'text-align: center; margin-top: 8px; margin-bottom: 0; font-size: 0.9em; color: #555; width: 100%; box-sizing: border-box;'; // Removed clear:both, added width/box-sizing
                ?>
            <?php // END: Added Stock Display - MOVED BELOW ?>


            <?php if ($_product->is_in_stock()):

                $ip_address = WC_Geolocation::get_ip_address();

                if ((get_theme_mod('block_ip_reordering') == 1 && the_ip_address_has_order($ip_address)) || (get_theme_mod('block_cookies_reordering') == 1 && the_cookies_has_order())): ?>
                    <input id="nrwooconfirm" type="submit" disabled name="submit"
                        value="<?php _e('You can order after 24 hours', 'napoleon'); ?>">

                <?php elseif (get_theme_mod('display_atc_button', 0) == 1): ?>

                    <?php

                    $add_to_cart_url = '?add-to-cart=' . $product->get_id();

                    $button_text = esc_html(get_theme_mod('form_atc_button', __('Add to cart', 'napoleon')));

                    $link = sprintf(
                        '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="custom-atc-btn button  add_to_cart_button ajax_add_to_cart product_type_%s" data-product_type="%s" >%s</a>',
                        esc_url($add_to_cart_url),
                        esc_attr($product->get_id()),
                        esc_attr($product->get_sku()),
                        esc_attr(isset($quantity) ? $quantity : 1),
                        esc_attr($product->get_type()),
                        esc_attr($product->get_type()),
                        esc_html($button_text)
                    );

                    ?>

                    <div id="nrwooconfirm" class="atc-buy-button">
                        <?php echo $link; ?>

                        <input type="submit" name="codplugin-submit"
                            value="<?php echo esc_html(get_theme_mod('form_button', 'Click here to confirm the order')); ?>"
                            form="codplugin_woo_single_form">
                    </div>

                <?php else: ?>

                    <input id="nrwooconfirm" type="submit" name="codplugin-submit"
                        value="<?php echo esc_html(get_theme_mod('form_button', __('Click here to confirm the order', 'napoleon'))); ?>"
                        form="codplugin_woo_single_form">

                <?php endif; ?>


            <?php else: ?>

                <input id="nrwooconfirm" type="submit" disabled name="submit" value="<?php _e('Out of stock', 'napoleon'); ?>">

            <?php endif; ?>


        </div>

        <?php // Stock display was moved above form-footer ?>

        <?php if ($is_cart_mode): ?>
            <style>
                .form-footer .form-qte {
                    display: none !important;
                }

                /* Hide simple '1' if using JS to update count usually, but in cart mode we rely on cart total */
            </style>
        <?php endif; ?>

        <?php if (get_theme_mod('whatsapp_number')): ?>
            <div class="whatsapp-order-section">
                <button id="whatsapp-orders">
                    <i class="fab fa-whatsapp"></i>
                    <span><?php echo esc_html(get_theme_mod('whatsapp_text', __('Order from Whatsapp', 'napoleon'))); ?></span>
                </button>
            </div>
        <?php endif; ?>

        <?php // Start Dynamic Payment Method Selector ?>
        <?php
        // NEW LOGIC STARTS HERE
        $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
        $show_payment_selector = true; // Default to showing the selector
    
        if (!empty($available_gateways)) {
            if (count($available_gateways) === 1) {
                reset($available_gateways); // Ensure pointer is at the beginning
                $first_gateway_id = key($available_gateways);
                if ($first_gateway_id === 'cod') {
                    $show_payment_selector = false; // Hide selector if only COD is available
                }
            }
        } else {
            // If there are no gateways at all, we definitely don't show the selector.
            $show_payment_selector = false;
        }
        // NEW LOGIC ENDS HERE
    
        // PAYMENT METHOD DISPLAY SECTION - GRID ONLY
        if ($show_payment_selector && !empty($available_gateways)):  // Show selector only if conditions met AND gateways exist
            // Note: $available_gateways is already fetched.
            $default_gateway_id = get_option('woocommerce_default_gateway');
            $has_default_gateway_been_set = false; // To ensure only one is checked by default if no explicit WC default
            $first_gateway = true; // Helper to select the very first one if no WC default
            ?>
            <div class="codplugin-payment-methods-container">
                <div class="codplugin-payment-methods styled-selector">
                    <?php
                    foreach ($available_gateways as $gateway_id => $gateway):
                        $checked_attr = '';
                        // Check if this gateway is the WooCommerce default, or if it's the first one and no default is set
                        if ($default_gateway_id === $gateway_id) {
                            $checked_attr = 'checked="checked"';
                            $has_default_gateway_been_set = true;
                        } elseif (empty($default_gateway_id) && !$has_default_gateway_been_set && $first_gateway) {
                            $checked_attr = 'checked="checked"';
                            $has_default_gateway_been_set = true;
                        }
                        $first_gateway = false; // No longer the first after the first iteration
                        ?>
                        <input class="payment-option-input" id="payment-<?php echo esc_attr($gateway_id); ?>" type="radio"
                            name="codplugin_payment_method" value="<?php echo esc_attr($gateway_id); ?>" <?php echo $checked_attr; ?> form="codplugin_woo_single_form" />
                        <label class="payment-option" for="payment-<?php echo esc_attr($gateway_id); ?>">
                            <?php
                            $icon_html = $gateway->get_icon();
                            if (!empty($icon_html)) {
                                // Ensure our class is added to the img tag from get_icon()
                                if (strpos($icon_html, '<img') !== false) {
                                    $icon_html = preg_replace('/<img/', '<img class="payment-option-icon" ', $icon_html, 1);
                                }
                                echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Gateway HTML
                            } elseif ($gateway_id === 'cod') { // Fallback icon for COD
                                echo '<img src="https://images.vexels.com/media/users/3/202463/isolated/preview/1b4829ac9ea7a1093b4abb1fb0b2f4cf-american-dollar-bill-flat-icon.png?w=360" alt="' . esc_attr__('Cash on Delivery Icon', 'napoleon') . '" class="payment-option-icon">';
                            } elseif ($gateway_id === 'chargily_pay') { // Fallback icon for Chargily
                                echo '<img src="https://www.baridimob.com/wp-content/uploads/Baridiweb-logo.png" alt="' . esc_attr__('Chargily Icon', 'napoleon') . '" class="payment-option-icon chargily-icon">';
                            }
                            ?>
                            <span class="payment-option-label"><?php echo esc_html($gateway->get_title()); ?></span>
                            <span class="payment-option-indicator"></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <?php
                // Conditionally display Chargily Pay specific fields if the gateway is available and enabled via theme option
                $theme_mod_chargily_enabled = get_theme_mod('codplugin_enable_chargily_pay', false);
                if ($theme_mod_chargily_enabled && isset($available_gateways['chargily_pay']) && class_exists('WC_chargily_pay')):
                    $chargily_gateway_obj = new WC_chargily_pay(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
                    if (method_exists($chargily_gateway_obj, 'payment_fields')):
                        ?>
                        <div id="chargily_pay_fields_container"
                            style="display:none; margin-top: 15px; padding:10px; border: 1px solid #e0e0e0; border-radius: 4px;">
                            <?php $chargily_gateway_obj->payment_fields(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase ?>
                        </div>
                        <?php
                    endif;
                endif;
                ?>
            </div>
        <?php elseif (empty($available_gateways)):  // This specifically handles the "No payment methods" message ?>
            <p><?php esc_html_e('No payment methods are available.', 'napoleon'); ?></p>
        <?php endif; ?>
        <?php // End Dynamic Payment Method Selector ?>

        <div id="codplugin_order_history" class="clear">

            <div id="codplugin_h_right">
                <span id="codplugin_h_o">
                    <i class="fas fa-shopping-cart "></i>
                    <?php _e('Order Summary', 'napoleon'); ?>
                </span>

            </div>

            <div id="codplugin_h_left">
                <i class="fas fa-chevron-down"></i>
            </div>

        </div>

        <?php if ($product_type == "variable"): ?>

            <div id="codplugin_show_hide">
                <table>
                    <tr>
                        <td class="summary-product-title"><?php echo $product->get_title(); ?></td>
                        <td class="summary-item-price-cell">
                            <span id="codplugin_count_number">1</span>
                            <span id="codplugin_v_price"></span>
                            <span> <?php echo get_woocommerce_currency_symbol(); ?></span>
                        </td>
                    </tr>
                    <tr class="shipping-row"> <?php // Added class for targeting ?>
                        <td>
                            <span>
                                <?php _e("Deliver Price", "napoleon"); ?>
                            </span>
                            <?php // Conditionally output shipping methods inside summary for default preset ?>
                            <?php if ($layout_preset === 'default'): ?>
                                <div id="shipping-methods"></div>
                            <?php endif; ?>
                        </td>
                        <td id="codplugin_d_has_price">
                            <span id="codplugin_d_price">
                                <span class="summary-select-state">حدد خيارا <img draggable="false" role="img" class="emoji"
                                        alt="🛒" src="https://s.w.org/images/core/emoji/16.0.1/svg/1f6d2.svg"></span>
                            </span>
                            <span class="codplugin_currency" style="display: none;"> د.ج </span>
                        </td>
                        <td id="codplugin_d_free" style="display:none;"><span><?php _e('FREE', 'napoleon'); ?></span></td>

                    </tr>
                    <tr class="full-price">
                        <td><?php _e("Total Price", "napoleon"); ?></td>
                        <td><span id="codplugin_total_price"></span> <?php echo get_woocommerce_currency_symbol(); ?></td>
                    </tr>
                </table>
            </div>

        <?php elseif ($is_cart_mode): ?>

            <div id="codplugin_show_hide">
                <table>
                    <?php
                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item):
                        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                        $product_name = $_product->get_name();
                        $quantity = $cart_item['quantity'];
                        $line_total = WC()->cart->get_product_subtotal($_product, $cart_item['quantity']);
                        ?>
                        <tr>
                            <td class="summary-product-title"><?php echo $product_name . ' <strong>x ' . $quantity . '</strong>'; ?>
                            </td>
                            <td class="summary-item-price-cell">
                                <span><?php echo $line_total; ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <tr class="shipping-row">
                        <td>
                            <span><?php _e("Deliver Price", "napoleon"); ?></span>
                            <?php if ($layout_preset === 'default'): ?>
                                <div id="shipping-methods"></div>
                            <?php endif; ?>
                        </td>
                        <td id="codplugin_d_has_price">
                            <span id="codplugin_d_price">
                                <span class="summary-select-state">
                                    <?php if (!empty($default_county_states)) {
                                        echo __('Choose', 'napoleon') . ' ' . esc_html(get_theme_mod('form_state', __('State', 'napoleon')));
                                    } ?>
                                </span>
                            </span>
                            <span class="codplugin_currency"> <?php echo get_woocommerce_currency_symbol(); ?></span>
                        </td>
                        <td id="codplugin_d_free" style="display:none;"><span><?php _e('FREE', 'napoleon'); ?></span></td>
                    </tr>
                    <tr class="full-price">
                        <td><?php _e("Total Price", "napoleon"); ?></td>
                        <td><span id="codplugin_total_price"></span> <span>
                                <?php echo get_woocommerce_currency_symbol(); ?></span></td>
                    </tr>
                </table>
            </div>

        <?php else: ?>

            <div id="codplugin_show_hide">
                <table>
                    <tr>
                        <td class="summary-product-title"><?php echo $product->get_title(); ?></td>
                        <td class="summary-item-price-cell">
                            <span id="codplugin_count_number">1</span>
                            <span id="codplugin_v_price"><?php echo $product_price; ?></span>
                            <span> <?php echo get_woocommerce_currency_symbol(); ?></span>
                        </td>
                    </tr>
                    <tr class="shipping-row"> <?php // Added class for targeting ?>
                        <td>
                            <span>
                                <?php _e("Deliver Price", "napoleon"); ?>
                            </span>
                            <?php // Conditionally output shipping methods inside summary for default preset ?>
                            <?php if ($layout_preset === 'default'): ?>
                                <div id="shipping-methods"></div>
                            <?php endif; ?>

                        </td>
                        <td id="codplugin_d_has_price">
                            <span id="codplugin_d_price">
                                <span class="summary-select-state">
                                    <?php if (!empty($default_county_states)) {
                                        echo __('Choose', 'napoleon') . ' ' . esc_html(get_theme_mod('form_state', __('State', 'napoleon')));
                                    } ?>

                                </span>
                            </span>
                            <span class="codplugin_currency"> <?php echo get_woocommerce_currency_symbol(); ?>
                            </span>
                        </td>
                        <td id="codplugin_d_free" style="display:none;"><span><?php _e('FREE', 'napoleon'); ?></span></td>
                    </tr>
                    <tr class="full-price">
                        <td><?php _e("Total Price", "napoleon"); ?></td>
                        <td><span id="codplugin_total_price"></span> <span>
                                <?php echo get_woocommerce_currency_symbol(); ?></span></td>
                    </tr>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php
    $upsells = $product->get_upsell_ids();
    if (get_theme_mod('show_upsells', 0) == 1 && $upsells && get_theme_mod('create_orders_with_php', 0) == 0):
        $upsell_id = $upsells[0];
        ?>
        <div id="cod-upsell">
            <div id="cod-upsell-box">
                <div id="cod-upsell-loader">
                    <img loading="lazy"
                        src="<?php echo get_template_directory_uri() . "/inc/order-form/include/assets/img/processing.gif"; ?>" />
                </div>
                <h2 class="cod-upsell-heading">
                    <?php

                    if (get_theme_mod('upsell_title')) {
                        echo esc_html(get_theme_mod('upsell_title'));
                    } else {
                        echo esc_html__('Wait! Your order is not completed!', 'napoleon');
                    }

                    ?>
                </h2>
                <div class="cod-upsell-product">
                    <div class="cod-upsell-product-title">
                        <?php
                        $upsell = wc_get_product($upsell_id);
                        echo $upsell->get_title();
                        ?>
                    </div>

                    <img src="<?php echo get_the_post_thumbnail_url($upsell_id); ?>" class="img-responsive" alt="" />


                    <span class="price">
                        <?php echo $upsell->get_price_html(); ?>
                    </span>

                    <div id="upsell-submit">
                        <input type="hidden" id="upsell_product_id" name="upsell_product_id" value="<?php echo $upsell_id; ?>">
                        <button id="cod-add-upsell"><?php _e('Add To Cart', 'napoleon'); ?></button>
                        <button id="cod-upsell-cancel"><?php _e('No, Thanks', 'napoleon'); ?></button>
                    </div>


                </div>
            </div>
        </div>

    <?php endif;
}
