<?php

/**
 * Check if WooCommerce is activated
 */


include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (!is_plugin_active('woocommerce/woocommerce.php')) {
    return;
}
// Check if CODPLUGIN or TASHEEL are active
if (is_plugin_active('codplugin/codplugin.php') || is_plugin_active('tasheel/tasheel.php')) {
    return;
}



if (get_theme_mod('block_desktop_visitors', 0) == 1 && !current_user_can('manage_options') && !wp_is_mobile()) {
    return;
}

if (!get_theme_mod('display_order_form', 1)) {
    return;
}



/**
 * Load core files
 */
// function to remove add to cart from the product page
function remove_add_to_cart_codplugin()
{
    require_once get_theme_file_path('/inc/order-form/include/core/customizer.php');
    require_once get_theme_file_path('/inc/order-form/include/core/customizer-variations.php');
}
require_once get_theme_file_path('/inc/order-form/include/core/main.php');
require_once get_theme_file_path('/inc/order-form/include/core/php-orders.php');
require_once get_theme_file_path('/inc/order-form/include/core/ajax.php');
require_once get_theme_file_path('/inc/order-form/include/abandoned-carts/abandoned-carts.php');



/**
 * Display COD form in elementor 
 * 
 */

function register_cod_checkout_form_widget($widgets_manager)
{
    require_once get_theme_file_path('/inc/order-form/include/core/codplugin-elementor.php');

    $widgets_manager->register(new \COD_Plugin_Checkout_Form_Widget());
}
add_action('elementor/widgets/register', 'register_cod_checkout_form_widget');


function codplugin_woo_single_product_order()
{
    if (is_product() || is_page()) {
        wp_enqueue_style(
            "codplugin_woo_style",
            get_template_directory_uri() . "/inc/order-form/include/assets/css/codplugin.css",
            array(),
            '3.1'
        );


        wp_enqueue_script("jquery");
        wp_enqueue_script(
            "codplugin_woo_script",
            get_template_directory_uri() . "/inc/order-form/include/assets/js/codplugin.js",
            [],
            "3.0",
            true
        );

        wp_enqueue_script(
            "codplugin_confetti_script",
            get_template_directory_uri() . "/inc/order-form/include/assets/js/confetti.browser.min.js",
            [],
            "1.0",
            true
        );

        if (get_theme_mod('napoleon_payment_carousel_enable', false) || get_theme_mod('napoleon_payment_slider_enabled', 1)) {
            wp_enqueue_style(
                "swiper-css",
                "https://unpkg.com/swiper/swiper-bundle.min.css",
                array(),
                '6.8.4'
            );

            wp_enqueue_style(
                "font-awesome",
                "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css",
                array(),
                '6.0.0-beta3'
            );

            wp_enqueue_script(
                "swiper-js",
                "https://unpkg.com/swiper/swiper-bundle.min.js",
                [],
                "6.8.4",
                true
            );

            wp_enqueue_script(
                "swiper-init",
                get_template_directory_uri() . "/inc/order-form/include/assets/js/swiper-init.js",
                ['swiper-js'],
                "1.0",
                true
            );

            // Settings are passed via codplugin_swiper_settings() function in customizer.php
        }

        wp_localize_script("codplugin_woo_script", "codplugin_order", [
            "ajax_url" => admin_url("admin-ajax.php"),
            "variable_message" => __('Please Select Product Type', 'napoleon'),
            "processing_message" => __('Processing...', 'napoleon'),
            "completed_message" => __('Order was successfully submitted! ', 'napoleon'),
            "error_message" => __('An error occurred!', 'napoleon'),
            "stock_limit_message" => __('Maximum stock reached (%d available)', 'napoleon'), // New string
            "stock_limit_alert" => __('Quantity exceeds available stock.', 'napoleon'), // New string
            "form_city_placeholder" => esc_js(get_theme_mod('form_city', __('City', 'napoleon'))),
            "form_state_placeholder" => esc_js(get_theme_mod('form_state', __('State', 'napoleon'))),
            "is_bordrou_active" => is_plugin_active('woo-bordereau-generator/woocommerce-bordereau-generator.php'), // Check if Bordrou is active
            "currency_symbol" => get_woocommerce_currency_symbol(), // Add currency symbol
            "no_file_chosen_text" => __('No file chosen', 'napoleon'), // Text for file upload
            "current_abandoned_cart_id" => isset($_SESSION['current_abandoned_cart_id']) ? absint($_SESSION['current_abandoned_cart_id']) : 0, // Pass abandoned cart ID
            "shipping_method_selected_text" => __('has been selected', 'napoleon'),
            "searching_stopdesk_text" => __('يجري البحث عن مكاتب التوصيل', 'napoleon'),
            "no_stopdesk_text" => __('There is no available stopdesk in', 'napoleon'),
            "no_stopdesk_text_arabic" => __('', 'napoleon'),
            "no_stopdesk_advice_text" => __('Please select the nearest city or switch to flat rate.', 'napoleon'),
            "no_stopdesk_advice_text_arabic" => __('يرجى اعادة اختيار البلدية', 'napoleon'),
            "select_color_text" => __('اللون 🎨', 'napoleon'),
            "select_size_text" => __('المقاس 📏', 'napoleon'),
            "select_option_text" => __('الخيار 🛒', 'napoleon'),
            "please_select_text" => __('يرجى تحديد', 'napoleon')
        ]);

        // Conditionally enqueue Chargily Pay assets if integration is enabled
        // Control removed from Customizer, defaulting to true.
        if (get_theme_mod('codplugin_enable_chargily_pay', true)) {
            // Define the base path to the Chargily plugin file for plugins_url() context
            // Ensure the chargily-pay directory name is correct
            $chargily_plugin_file = WP_PLUGIN_DIR . '/chargily-pay/chargily.php';

            // Check if the main plugin file exists before trying to enqueue assets relative to it
            if (file_exists($chargily_plugin_file)) {
                // Enqueue Chargily CSS
                wp_enqueue_style('chargily-style-front', plugins_url('assets/css/css-front.css', $chargily_plugin_file), array(), '2.3.0'); // Use version from chargily.php if possible, or remove version query string
                if (is_rtl()) {
                    wp_enqueue_style('chargily-rtl-style', plugins_url('assets/css/css-front-rtl.css', $chargily_plugin_file), array('chargily-style-front'), '2.3.0'); // Use a distinct handle like 'chargily-rtl-style'
                }
                // Enqueue Chargily JS (needed for payment method selection cookie)
                wp_enqueue_script('chargily-script-front', plugins_url('assets/js/js-front.js', $chargily_plugin_file), array('jquery'), '1.1.6', true); // Use version from chargily.php if possible
            }
        }

        if (get_theme_mod('autocomplete_state_list', 0) == 1) {
            wp_enqueue_style(
                "codplugin_chosen_css",
                get_template_directory_uri() . "/inc/order-form/include/assets/chosen/chosen.min.css"
            );
            wp_enqueue_script("codplugin_chosen_js", get_template_directory_uri() . "/inc/order-form/include/assets/chosen/chosen.jquery.min.js", [], "1.8.7", true);
        }
    }


}
add_action('wp_enqueue_scripts', 'codplugin_woo_single_product_order');




if (!function_exists('codplugin_woocommerce_template_single_add_to_cart')):
    add_action("init", function () {
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    });

    function codplugin_woocommerce_template_single_add_to_cart()
    {
        // Only display the form on single product pages
        if (!is_product()) {
            return '';
        }
        global $product;
        codplugin_checkout_form($product->get_id());
    }
endif;



add_action('woocommerce_thankyou', 'woocommerce_thankyou_change_order_status', 10, 1);
function woocommerce_thankyou_change_order_status($order_id)
{
    if (!$order_id)
        return;

    $order = wc_get_order($order_id);

    if ($order->get_status() == 'pending')
        $order->update_status('processing');

    ?>
    <script> jQuery(document).ready(function ($) {
            launchConfetti();
            function launchConfetti() {
                var duration = 1 * 1000;
                var end = Date.now() + duration;

                (function frame() {
                    // launch a few confetti from the left edge
                    confetti({
                        particleCount: 7,
                        angle: 60,
                        spread: 55,
                        origin: { x: 0 }
                    });
                    // and launch a few from the right edge
                    confetti({
                        particleCount: 7,
                        angle: 120,
                        spread: 55,
                        origin: { x: 1 }
                    });

                    // keep going until we are out of time
                    if (Date.now() < end) {
                        requestAnimationFrame(frame);
                    }
                })();
            }
        });
    </script>
    <?php
}



/**
 * Add custom inline css .
 */

function codplugin_inline_style()
{
    $accent_color = get_theme_mod('site_accent_color', '#4C3BCF');
    $secondary_color = get_theme_mod('secondary_color', '#259bea');
    ?>
    <style>
        :root {
            --napoleon-accent-color:
                <?php echo esc_attr($accent_color); ?>
            ;
        }
    </style>
    <?php
    if (get_theme_mod('display_order_summary', 1) == 1): ?>

        <style>
            #codplugin_show_hide {
                display: block;
            }
        </style>

    <?php else: ?>
        <style>
            #codplugin_show_hide {
                display: none;
            }
        </style>

    <?php endif;

    if (get_theme_mod('animate_order_btn', 0) == 1): ?>

        <style>
            #nrwooconfirm:not(.atc-buy-button) {
                animation: shaking 1.8s cubic-bezier(0.36, 0.07, 0.19, 0.97) infinite;
                transform: translate3d(0, 0, 0);
                perspective: 1000px;
            }
        </style>
    <?php endif; ?>
    <?php // Add RTL fix for Chargily QR code image alignment ?>
    <style>
        html[dir="rtl"] .codplugin-payment-methods-container img.appCardImage {
            margin-right: auto !important;
            /* Push image to the left in RTL */
            margin-left: inherit !important;
            /* Reset left margin */
        }

        /* Force text alignment for summary item price cell */
        #codplugin_show_hide .summary-item-price-cell {
            text-align: right !important;
        }

        .rtl #codplugin_show_hide .summary-item-price-cell {
            text-align: left !important;
        }

        .rtl #codplugin_d_free {
            text-align: left !important;
        }

        /* --- Styled Payment Selector --- */
        .codplugin-payment-methods.styled-selector {
            display: flex;
            gap: 10px;
            /* Adjust gap between buttons */
            margin-bottom: 15px;
            /* Keep space before Chargily fields */
            flex-wrap: nowrap;
            /* Force buttons onto one line */
        }

        .payment-option-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        label.payment-option {
            display: flex;
            align-items: center;
            padding: 8px 5px;
            /* Further reduced padding */
            border: 2px solid #bce0f7;
            /* Use secondary color for border */
            border-radius: 5px;
            background-color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            /* Needed for indicator positioning */
            flex-grow: 1;
            /* Allow buttons to grow */
            justify-content: center;
            /* Center content */
            min-width: 150px;
            /* Ensure minimum width */
            height: 45px;
            /* Shorter height */
            box-sizing: border-box;
        }

        label.payment-option:hover {
            background-color: #f8f8f8;
            border-color: #a0c8e0;
        }

        .payment-option-icon {
            width: 24px;
            /* Adjust icon size */
            height: 24px;
            margin-right: 8px;
            object-fit: contain;
            /* Prevent distortion */
            vertical-align: middle;
            /* Align icon better with text */
        }

        .rtl .payment-option-icon {
            margin-left: 8px;
            margin-right: 0;
        }

        .payment-option-label {
            font-size: 14px;
            /* Adjust font size */
            font-weight: 500;
            line-height: 1.2;
            /* Adjust line height */
            display: inline-block;
            /* Ensure label text respects alignment */
            vertical-align: middle;
        }

        .payment-option-indicator {
            display: none;
            /* Hide the example indicator for now, selection is shown by border/bg */
        }

        /* Selected state */
        .payment-option-input:checked+label.payment-option {
            border-color: #4C3BCF !important;
            /* Use accent color for selected border */
            background-color: #f0f9ff !important;
            /* Light accent background */
            box-shadow: 0 0 5px rgba(37, 155, 234, 0.3);
            /* Optional subtle glow */
            /* You could add back the indicator here if desired */
        }

        .codplugin-payment-methods.styled-selector .swiper-slide {
            width:
                <?php echo get_theme_mod('napoleon_payment_button_width', 50); ?>
                % !important;
        }

        /* Reduce bottom margin for the payment container */
        .codplugin-payment-methods-container {
            margin-bottom: 5px !important;
            /* Further reduced margin */
        }

        /* --- End Styled Payment Selector --- */

        /* Your new style for padding */
        #codplugin-checkout .codplugin-payment-methods-container {
            padding-bottom: 10px;
        }

        /* Responsive styles for payment options */
        @media (max-width: 600px) {

            /* You can adjust this breakpoint if needed */
            .codplugin-payment-methods.styled-selector {
                flex-wrap: wrap;
                /* Allow buttons to wrap to the next line */
                gap: 8px;
                /* Adjust gap if needed for wrapped items */
            }

            label.payment-option {
                min-width: 0;
                /* Allow shrinking below the original 150px */
                flex-grow: 1;
                /* Allow buttons to grow and share space on a line */
                flex-basis: 130px;
                /* Suggest a base size; they can grow or wrap. */
                padding: 8px 12px;
                /* Maintain reasonable padding */
                margin-bottom: 8px;
                /* Add space below if they wrap and stack */
            }

            .payment-option-label {
                font-size: 13px;
                /* Slightly reduce font size for better fit */
            }
        }

        .codplugin-payment-methods-container {
            position: relative;
            overflow: hidden;
        }

        .swiper-container {
            width: 100%;
            height: 100%;
        }

        .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #fff;
            /* Center slide text vertically */
            display: -webkit-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
        }

        .swipe-tooltip {
            display: none;
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 100;
            font-size: 32px;
            color:
                <?php echo get_theme_mod('napoleon_slider_hand_icon_color', '#4C3BCF'); ?>
                !important;
            opacity:
                <?php echo get_theme_mod('napoleon_slider_hand_icon_opacity', 1); ?>
                !important;
            animation: drag-right 2s ease-out infinite;
            transition: opacity 0.5s ease-out;
        }

        .swipe-tooltip.fade-out {
            opacity: 0 !important;
        }

        @keyframes drag-right {
            0% {
                transform: translateX(0) translateY(-50%) scale(1);
                opacity: 1;
            }

            10% {
                transform: translateX(0) translateY(-50%) scale(0.9);
                opacity: 1;
            }

            50% {
                transform: translateX(<?php echo get_theme_mod('napoleon_slider_hand_animation_width', 20); ?>px) translateY(-50%) scale(0.9);
                opacity: 1;
            }

            60% {
                transform: translateX(<?php echo get_theme_mod('napoleon_slider_hand_animation_width', 20); ?>px) translateY(-50%) scale(1);
                opacity: 1;
            }

            100% {
                transform: translateX(0) translateY(-50%) scale(1);
                opacity: 1;
            }
        }

        /* Swiper Navigation and Pagination */
        .swiper-button-next,
        .swiper-button-prev {
            color: #4C3BCF !important;
            background-color: white;
            border-radius: 50%;
            width: 30px !important;
            height: 30px !important;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 14px !important;
            font-weight: bold;
        }

        .swiper-pagination-bullet-active {
            background: #4C3BCF !important;
        }

        /* Payment Grid Layout (when carousel is disabled) */
        .codplugin-payment-methods.payment-grid-layout {
            display: grid;
            gap: 15px;
            align-items: stretch;
        }

        .codplugin-payment-methods.payment-grid-layout .payment-option {
            margin: 0;
            min-width: auto;
            flex-basis: auto;
        }

        .codplugin-payment-methods.payment-grid-layout .payment-option-input:checked+label.payment-option {
            border-color: #4C3BCF !important;
            background-color: #f0f9ff !important;
        }

        /* Floating Notice Styles */
        #codplugin-city-notice {
            display: none;
            position: fixed;
            bottom: 30px;
            /* Position higher */
            left: 50%;
            transform: translateX(-50%);
            background-color:
                <?php echo esc_attr(get_theme_mod('secondary_color', '#bce0f7')); ?>
            ;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 1001;
            font-size: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease-in-out;
            opacity: 0;
            text-align: center;
        }

        #codplugin-city-notice.visible {
            display: block;
            opacity: 1;
            bottom: 50px;
            /* Animate upwards */
        }

        #codplugin-city-notice .spinner {
            display: inline-block;
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .variation-prices tr.checked::after {
            background-color:
                <?php echo esc_attr($secondary_color); ?>
            ;
        }

        .most-popular-badge {
            background-color:
                <?php echo esc_attr($secondary_color); ?>
            ;
        }
    </style>
    <?php
}
add_action('wp_head', 'codplugin_inline_style');



/**
 * Create custom shortcode for cod form 
 */
function codplugin_shortcode($atts)
{
    // Only display the form on single product pages
    if (!is_product()) {
        return '';
    }

    // Ensure 'id' attribute exists before using it
    if (!isset($atts['id']) || empty($atts['id'])) {
        return '<p>' . esc_html__('Product ID missing in shortcode.', 'napoleon') . '</p>';
    }

    $product_id = $atts['id'];
    // Capture the output of codplugin_checkout_form instead of echoing directly
    ob_start();
    codplugin_checkout_form($product_id);
    return ob_get_clean();
}
add_shortcode('codform', 'codplugin_shortcode');

/**
 * Create custom shortcode for cod CART form 
 */
function codplugin_cart_shortcode($atts)
{

    // Capture the output of codplugin_checkout_form instead of echoing directly
    // Passing 0 or null triggers "Cart Mode"
    ob_start();
    codplugin_checkout_form(0);
    return ob_get_clean();
}
add_shortcode('cod_cart_checkout', 'codplugin_cart_shortcode');




/**
 * Add States and Places To COD Form 
 */

add_action('codplugin_state_city', 'my_custom_checkout_fields');
function my_custom_checkout_fields()
{
    global $woocommerce;
    $countries_obj = new WC_Countries();
    $countries = $countries_obj->__get("countries");
    $default_country = $countries_obj->get_base_country();

    include(get_theme_file_path() . '/inc/order-form/include/states/' . $default_country . '.php');
    include(get_theme_file_path() . '/inc/order-form/include/places/' . $default_country . '.php');

    $states = $states[$default_country];
    $cities = $places[$default_country];

    // Get excluded states from Customizer (now returns an array)
    $excluded_states_arr = get_theme_mod('codplugin_excluded_states', array());
    // Ensure it's an array, even if something unexpected was saved.
    if (!is_array($excluded_states_arr)) {
        $excluded_states_arr = array();
    }

    // Create state options array with unique IDs, filtering out excluded states
    $state_options = array('' => get_theme_mod('form_state', __('State', 'napoleon')));
    if (is_array($states)) { // Ensure $states is an array before looping
        foreach ($states as $state_id => $state_name) {
            if (!in_array($state_id, $excluded_states_arr)) {
                $state_options[$state_id] = $state_name;
            }
        }
    }

    woocommerce_form_field('codplugin_state', array(
        'type' => 'select',
        'name' => 'codplugin_state',
        'class' => array('codplugin-field'),
        'placeholder' => get_theme_mod('form_state', __('State', 'napoleon')),
        'required' => true,
        'options' => $state_options,
    ));

    // Create city options array with unique IDs
    $city_options = array('' => get_theme_mod('form_city', __('City', 'napoleon')));

    woocommerce_form_field('codplugin_city', array(
        'type' => 'select',
        'class' => array('codplugin-field'),
        'placeholder' => get_theme_mod('form_city', __('City', 'napoleon')),
        'required' => true,
        'options' => $city_options,
    ));

    // Add JavaScript to dynamically update city field options based on the selected state
    ?>

    <script type="text/javascript">
        // Make cities data globally accessible
        window.codplugin_cities = <?php echo json_encode($cities); ?>;

        jQuery(document).ready(function ($) {
            // Disable the first option of the state select element
            $('select#codplugin_state option:first').attr('disabled', true);

            // All other city population logic is now handled by inc/order-form/include/assets/js/codplugin.js
        });
    </script>
    <?php
}


function napoleon_random_products_on_thankyou_page()
{

    if (get_theme_mod('thanks_related_products', 0) == 0) {
        return;
    }
    // Query for random products from the shop
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 15,
        'orderby' => 'rand', // Order by random
    );

    $random_query = new WP_Query($args);

    if ($random_query->have_posts()) {
        echo '<div class="section-heading"><h3 class="section-title">' . __('You may also like&hellip;', 'napoleon') . '</h3></div>';
        echo '<div class="thanks-products">';

        while ($random_query->have_posts()):
            $random_query->the_post();
            wc_get_template_part('content', 'product');
        endwhile;

        echo '</div>';

        wp_reset_postdata();
    }
}

add_action('woocommerce_thankyou', 'napoleon_random_products_on_thankyou_page', 10);

function add_whatsapp_order_button()
{


    if (get_theme_mod('whatsapp_number')): ?>

        <script>

            jQuery(document).ready(function ($) {
                // WhatsApp button click event
                $('#whatsapp-orders').on('click', function () {
                    // Get the product URL and product name dynamically using WooCommerce functions
                    var whatsappPhoneNumber = '<?php echo get_theme_mod('whatsapp_number'); ?>';
                    var productURL = '<?php echo get_permalink(); ?>';
                    var productName = '<?php echo get_the_title(); ?>';

                    // Customize your WhatsApp message
                    var message = '<?php _e('Hi, I am interested in this product: ', 'napoleon'); ?>' + productName + '\n' + productURL;

                    // Encode the message for WhatsApp
                    var encodedMessage = encodeURIComponent(message);

                    // WhatsApp API link
                    var whatsappURL = 'https://api.whatsapp.com/send?phone=' + whatsappPhoneNumber + '&text=' + encodedMessage;

                    // Redirect to WhatsApp
                    window.location.href = whatsappURL;
                });
            });
        </script>

    <?php endif;

}

add_action('wp_footer', 'add_whatsapp_order_button');


// Footer Scripts
function codplugin_footer_scripts()
{


    $order_currency = get_woocommerce_currency();

    ?>

    <script>


        // Add alert to select a variation before click add to cart
        jQuery(document).ready(function ($) {

            // Function to validate variation selection (same as in codplugin.js)
            function validateVariationSelection() {
                var isVariableProduct = $('.variations_form').length > 0 || $('.variation-prices').length > 0;

                if (!isVariableProduct) {
                    return true; // Simple product, always valid
                }

                var hasVariationSelection = false;

                // Check for CFVSW plugin variations
                if ($('.variations_form').length > 0) {
                    var allVariationsSelected = true;
                    $('.variations_form select').each(function () {
                        if (!$(this).val()) {
                            allVariationsSelected = false;
                        }
                    });
                    hasVariationSelection = allVariationsSelected && $('input[name=variation_id]').val() && $('input[name=variation_id]').val() !== '0';
                }

                // Check for default radio variations
                if ($('.variation-prices').length > 0) {
                    var selectedRadio = $('input[type=radio][name=var_price]:checked');
                    hasVariationSelection = selectedRadio.length > 0 && selectedRadio.val() && selectedRadio.val() !== '';
                }

                return hasVariationSelection;
            }

            function launchConfetti() {
                var duration = 1 * 1000;
                var end = Date.now() + duration;

                (function frame() {
                    // launch a few confetti from the left edge
                    confetti({
                        particleCount: 7,
                        angle: 60,
                        spread: 55,
                        origin: { x: 0 }
                    });
                    // and launch a few from the right edge
                    confetti({
                        particleCount: 7,
                        angle: 120,
                        spread: 55,
                        origin: { x: 1 }
                    });

                    // keep going until we are out of time
                    if (Date.now() < end) {
                        requestAnimationFrame(frame);
                    }
                })();
            }


            var variationId = $('#var_id').val();
            var $productLink = $("a.custom-atc-btn.product_type_variable:not(.add_to_cart_button)");


            if (variationId === '') {
                $("a.custom-atc-btn.product_type_variable").attr("href", "");
                $("a.custom-atc-btn.product_type_variable").removeClass("add_to_cart_button popup-alert");

            } else {
                $("a.custom-atc-btn.product_type_variable").attr("href", "?add-to-cart=" + variationId);
                $("a.custom-atc-btn.product_type_variable").attr("data-product_id", variationId);
                $("a.custom-atc-btn.product_type_variable").addClass("add_to_cart_button popup-alert");
            }

            // Event delegation to handle click event with simplified logic (same as place order button)
            $(document).on('click', 'a.custom-atc-btn.product_type_variable:not(.popup-alert)', function (e) {
                e.preventDefault();

                // Check if all required fields are selected (same logic as place order button)
                var stateSelected = $('#codplugin_state').val();
                var variationsValid = validateVariationSelection();
                var notice = $('#codplugin-city-notice');

                // Show appropriate error message based on what's missing
                var missingText = '';
                if (!variationsValid) {
                    missingText = codplugin_order.select_variation_text || 'يرجى تحديد الخيارات المتاحة';
                } else if (!stateSelected) {
                    missingText = codplugin_order.please_select_text + ' ' + (codplugin_order.form_state_placeholder || 'ولاية');
                } else {
                    missingText = 'يرجى استكمال جميع الحقول المطلوبة';
                }

                alert(missingText);
            });



            // Form Submission

            <?php if (get_theme_mod('create_orders_with_php', 0) == 1): ?>
                $("#codplugin_woo_single_form").on("submit", function (e) {

                    if ($('input.variation_id').val() == 0) {
                        e.preventDefault();
                        alert(codplugin_order.variable_message);
                        return;
                    }
                    $("#nrwooconfirm").attr('disabled', true);

                    $('#codplugin_state option:selected').val(function () {
                        return $(this).text();
                    });
                    $('#codplugin_city option:selected').val(function () {
                        return $(this).text();
                    });

                    $("#nrwooconfirm:not(.atc-buy-button)").val(codplugin_order.processing_message);

                    // Show spinner after a delay
                    var spinnerTimeoutId = setTimeout(function () {
                        $("#codplugin_gif").css('display', 'block');
                    }, 2000);

                });

            <?php else: ?>

                var codplugin_thankyou_url = $("#codplugin_thankyou_url").val();

                $("#codplugin_woo_single_form").on("submit", function (e) {
                    e.preventDefault();

                    if ($('input.variation_id').val() == 0) {
                        alert(codplugin_order.variable_message);
                        return;
                    }

                    $("#nrwooconfirm").attr('disabled', true);

                    var spinnerTimeoutId = null;
                    spinnerTimeoutId = setTimeout(function () {
                        $("#codplugin_gif").css('display', 'block');
                    }, 2000);

                    $("#nrwooconfirm:not(.atc-buy-button)").val(codplugin_order.processing_message);

                    // If #codplugin-processing is still used, its logic should be reviewed separately.
                    // For now, focusing on #codplugin_gif as per user request.
                    // $('#codplugin-processing').show(); 


                    $('#codplugin_state option:selected').val(function () {
                        return $(this).text();
                    });
                    $('#codplugin_city option:selected').val(function () {
                        return $(this).text();
                    });

                    if ($("#thanks-order-summary").length > 0) {
                        $(' .main, .footer').hide();
                        $(".head-mast").addClass("thanks-active");
                        $('html, body').animate({ scrollTop: 0 }, 'slow');

                        $('#codplugin-thanks').show();
                        $('#codplugin_show_hide').clone().appendTo('#thanks-order-summary');
                        launchConfetti();

                        var order_total = $("#codplugin_total_price").text()

                        if (typeof fbq !== 'undefined') {
                            fbq('track', 'Purchase', {
                                value: order_total,
                                currency: '<?php echo $order_currency; ?>',
                                content_type: 'product'
                            });
                        }
                    }


                    var formdata = new FormData(this);
                    formdata.append("action", "codplugin_order_form_action");


                    $.ajax({
                        url: codplugin_order.ajax_url,
                        action: "codplugin_order_form_action",
                        type: "post",
                        data: formdata,
                        contentType: false,
                        processData: false,
                        success: function (val) {
                            if (spinnerTimeoutId) { clearTimeout(spinnerTimeoutId); spinnerTimeoutId = null; }
                            $("#codplugin_gif").css('display', 'none');
                            // $('#codplugin-processing').hide(); // Hide if it was shown

                            var obj = $.parseJSON(val);
                            orderID = obj.order_id;
                            orderID = obj.order_id;
                            orderKey = obj.order_key;

                            // Check for Chargily redirect URL first
                            if (obj.redirect_url) {
                                window.location.href = obj.redirect_url;
                                return; // Stop further processing since we are redirecting
                            }

                            // Check for AJAX errors returned from backend
                            if (obj.error) {
                                alert(obj.error); // Display error message
                                $("#nrwooconfirm").attr('disabled', false); // Re-enable button
                                $("#nrwooconfirm:not(.atc-buy-button)").val(codplugin_order.error_message); // Reset button text
                                return; // Stop processing on error
                            }

                            // Original logic for COD success (upsell or thank you page)
                            if ($("#cod-upsell").length == 0 && $("#thanks-order-summary").length == 0) { // there is no upsell, no fast thank you
                                window.location.href = codplugin_thankyou_url + 'order-received/' + orderID + '/?key=' + orderKey;
                            } else {
                                $("#cod-upsell").show();
                            }
                        },
                        error: function (val) {
                            if (spinnerTimeoutId) { clearTimeout(spinnerTimeoutId); spinnerTimeoutId = null; }
                            $("#codplugin_gif").css('display', 'none');
                            // $('#codplugin-processing').hide(); // Hide if it was shown
                            console.log(val);
                            $("#nrwooconfirm").attr('disabled', false); // Re-enable button on error
                            $("#nrwooconfirm:not(.atc-buy-button)").val(codplugin_order.error_message); // Show error on button
                        }

                    });
                });

                $("#cod-upsell-cancel").click(function () {
                    window.location.href = codplugin_thankyou_url + 'order-received/' + orderID + '/?key=' + orderKey;
                });

                $("#cod-add-upsell").click(function () {
                    productID = $("#upsell_product_id").val();
                    $("#cod-add-upsell").val(codplugin_order.completed_message);
                    $("#cod-add-upsell").attr('disabled', true);
                    // Consider spinner logic for upsell if needed, similar to main form.
                    // For now, existing timeout logic for button text and loader remains.
                    setTimeout(() => {
                        $("#cod-add-upsell").attr('disabled', false);
                        $("#cod-add-upsell").val(codplugin_order.error_message);
                    }, 8000);
                    $("#cod-upsell-loader").css('display', 'block');
                    setTimeout(() => {
                        $("#cod-upsell-loader").css('display', 'none');
                    }, 8000);

                    var data = {
                        action: "codplugin_add_upsell_product",
                        order_id: orderID,
                        product_id: productID,
                    };
                    $.ajax({
                        url: codplugin_order.ajax_url,
                        type: "post",
                        data: data,
                        success: function (val) {
                            console.log(val);
                            window.location.href = codplugin_thankyou_url + 'order-received/' + orderID + '/?key=' + orderKey;
                        },
                        error: function (error) {
                            console.log(error);
                        },

                    });

                });

            <?php endif; ?>

        });
    </script>
    <?php

}
add_action('wp_footer', 'codplugin_footer_scripts');



function napoleon_fast_thankyou()
{
    ?>
    <!-- Floating notice for city matching -->
    <div id="codplugin-city-notice"></div>
    <?php
    if (get_theme_mod('enable_fast_thanks', 0) == 1): ?>

        <div id="codplugin-thanks">
            <div id="codplugin-thanks-box">
                <p class="thanks-box-title blink-me"><?php _e('Your order has been placed', 'napoleon'); ?></p>
                <div class="thanks-box-content"><?php echo wp_kses_post(get_theme_mod('thanks_editor')); ?></div>
                <div id="thanks-order-summary">
                    <div class="order-summary-title">
                        <i class="fas fa-shopping-cart "></i>
                        <?php _e('Order Summary', 'napoleon'); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
    endif;
}

add_action('wp_footer', 'napoleon_fast_thankyou');


// Add body class based on layout preset
add_filter('body_class', 'codplugin_add_preset_body_class');
function codplugin_add_preset_body_class($classes)
{
    // Existing layout preset
    $layout_preset = get_theme_mod('codform_layout_preset', 'default');
    if ($layout_preset === 'preset2') {
        $classes[] = 'codform-preset-2';
    } else {
        $classes[] = 'codform-preset-1'; // Default preset class
    }

    // New: Force Compact Form Layout
    if (get_theme_mod('force_compact_form_layout', false)) {
        $classes[] = 'force-compact-layout';
    }

    // New: Force Single-Line Quantity & Button
    if (get_theme_mod('force_single_line_qty_button', false)) {
        $classes[] = 'force-single-line-qty';
    }

    return $classes;
}

/**
 * Display uploaded file link on the order edit page.
 */
add_action('woocommerce_admin_order_data_after_billing_address', 'codform_display_uploaded_file_in_admin_order', 10, 1);
function codform_display_uploaded_file_in_admin_order($order)
{
    $attachment_id = $order->get_meta('_codform_uploaded_file_id');

    if ($attachment_id) {
        $file_url = wp_get_attachment_url($attachment_id);
        $file_name = basename(get_attached_file($attachment_id)); // Get the actual filename

        if ($file_url) {
            echo '<div class="form-field form-field-wide">';
            echo '<h4>' . esc_html__('Uploaded File', 'napoleon') . '</h4>';
            echo '<p><a href="' . esc_url($file_url) . '" target="_blank">' . esc_html($file_name) . '</a></p>';
            echo '</div>';
        }
    }
}
