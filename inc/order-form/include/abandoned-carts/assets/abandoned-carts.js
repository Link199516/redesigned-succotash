jQuery(document).ready(function ($) {
    var buyBtnClicked = false;
    var phoneNumber = "";

    $('#nrwooconfirm, #place_order').on('click', function () {
        buyBtnClicked = true;
    });

    // Store the phone number whenever it is entered
    $('#billing_phone, #codplugin_woo_single_form input[name=phone_number]').on('blur', function () {
        phoneNumber = $(this).val();
    });

    $(window).on('beforeunload', function () {
        if (!buyBtnClicked && /^\d+$/.test(phoneNumber) && phoneNumber.length > 9) {
            $('#codplugin_state option:selected').val(function () {
                return $(this).text();
            });

            $('#codplugin_city option:selected').val(function () {
                return $(this).text();
            });

            $.ajax({
                url: ajax_helper.ajaxurl,
                type: "POST",
                data: {
                    action: "abandoned_carts",
                    security: ajax_helper.security,
                    'fields': $('form.checkout, #codplugin_woo_single_form').serializeArray(),
                },
                success: function (res) {
                    console.log(res);
                },
                error: function (error) {
                    console.error(error);
                },
            });
        }


    });
});
