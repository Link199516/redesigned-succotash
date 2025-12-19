console.log("codplugin.js file parsed."); // Added for debugging file loading

jQuery(document).ready(function($) {
    console.log("codplugin.js loaded and jQuery ready."); // Added for debugging

    var orderID;
    var orderKey;
    var abandonedOrderId = 0; // To store the ID of the draft order
    var stateChangeAjax = null; // AJAX controller for state changes
    var cityAjax = null; // AJAX controller for city fetching
    var shippingCostAjax = null; // AJAX controller for shipping cost
    var preservedShippingMethod = null; // Variable to hold shipping method during variation change
    var preservedStandardCity = null; // Variable to hold city value for standard shipping
    var preservedPickupCity = null; // Variable to hold city value for local pickup
    var preservedStandardCityText = null; // Variable to hold city text for standard shipping
    var preservedPickupCityText = null; // Variable to hold city text for local pickup
    var noticeShownOnce = false; // Flag for persistent notice logic

    // --- Debounce Function ---
    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    // Function to validate variation selection
    function validateVariationSelection() {
        var isVariableProduct = $('.variations_form').length > 0 || $('.variation-prices').length > 0;
        
        if (!isVariableProduct) {
            return true; // Simple product, always valid
        }
        
        var hasVariationSelection = false;
        
        // Check for CFVSW plugin variations
        if ($('.variations_form').length > 0) {
            var allVariationsSelected = true;
            $('.variations_form select').each(function() {
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

    // Listen for changes on the city dropdown and preserve the value and text based on the current shipping method
    $(document).on('change', 'select#codplugin_city', function() {
        var currentShippingMethod = $('#shipping-methods input[name="shipping_method"]:checked').val();
        var selectedText = $(this).find('option:selected').text();
        if (currentShippingMethod && currentShippingMethod.startsWith('local_pickup')) {
            preservedPickupCity = $(this).val();
            preservedPickupCityText = selectedText;
        } else {
            preservedStandardCity = $(this).val();
            preservedStandardCityText = selectedText;
        }
        
        if ($('#codplugin_state').val() && $(this).val()) {
            updateVariationPrompt(true); // Pass true to indicate it's a city selection
        }
    });

    // Store initial delivery price HTML
    var initialDeliveryPriceHTML = $("#codplugin_d_price").html();

    function restoreInitialDeliveryPricePlaceholder() {
        if (initialDeliveryPriceHTML) {
            $("#codplugin_d_price").html(initialDeliveryPriceHTML);
            $('#codplugin_d_has_price').show();
            $('#codplugin_d_free').hide();
        } else {
            // If no initial HTML, create same pattern as original PHP
            var stateLabel = codplugin_order && codplugin_order.form_state_placeholder ? codplugin_order.form_state_placeholder : 'State';
            var chooseText = codplugin_order && codplugin_order.please_select_text ? codplugin_order.please_select_text : 'Choose';
            $("#codplugin_d_price").html('<span class="summary-select-state">' + chooseText + ' ' + stateLabel + '</span>');
            $('.codplugin_currency').hide();
            $('#codplugin_d_has_price').show();
            $('#codplugin_d_free').hide();
        }
        if ($("#codplugin_state").val() === '') { 
            $("#d_price").attr('value', 0);
            updateTotalPrice(); 
        }
    }
    
    // --- Stock Validation Logic ---
    function checkStockLimit() {
        var $qtyContainer = $('.form-qte'); 
        var $qtyInput = $('#codplugin_c_number'); 
        var $qtyDisplay = $('#codplugin_count_button'); 
        var $addButton = $('#codplugin_add_button');
        var $removeButton = $('#codplugin_remove_button');

        var maxQty = $qtyContainer.data('max-qty');
        var currentQty = parseInt($qtyInput.val());

        if (typeof maxQty === 'number' && !isNaN(maxQty)) {
            if (currentQty >= maxQty) {
                $addButton.addClass('disabled');
                if (currentQty > maxQty) {
                    $qtyInput.val(maxQty);
                    $qtyDisplay.text(maxQty);
                    updateTotalPrice(); 
                }
            } else {
                $addButton.removeClass('disabled');
            }
            if (currentQty <= 1) { $removeButton.addClass('disabled'); } else { $removeButton.removeClass('disabled'); }
        } else {
            $addButton.removeClass('disabled');
            if (currentQty <= 1) { $removeButton.addClass('disabled'); } else { $removeButton.removeClass('disabled'); }
         }
     }
 
    var updatePreset2ShippingLabels = function() {
        if (!$('body').hasClass('codform-preset-2')) {
            return; 
        }

        var $checkedInput = $('#shipping-methods input[name="shipping_method"]:checked');
        var currencySymbol = codplugin_order.currency_symbol || ''; 

        $('#shipping-methods .codplugin-shipping-label').each(function() {
            var $label = $(this);
            var originalText = $label.data('original-text');
            if (originalText && $label.text() !== originalText) { 
                $label.text(originalText);
            }
        });

        if ($checkedInput.length > 0) {
            var $checkedLabel = $checkedInput.next('.codplugin-shipping-label');
            var shippingCost = parseFloat($('#d_price').val()); 
            var originalText = $checkedLabel.data('original-text'); 

            if (originalText) { 
                var emoji = '🚚'; 
                var lowerOriginalText = originalText.toLowerCase();
                if (originalText.includes('منزل') || lowerOriginalText.includes('home') || lowerOriginalText.includes('domicile')) {
                    emoji = '🏠';
                } else if (originalText.includes('مكتب') || lowerOriginalText.includes('office') || lowerOriginalText.includes('desk') || lowerOriginalText.includes('bureau')) {
                    emoji = '🏢';
                } else if (lowerOriginalText.includes('stop desk')) {
                     emoji = '🏬'; 
                }

                var newText = emoji + ' ';
                if (!isNaN(shippingCost) && shippingCost > 0) {
                    newText += shippingCost.toFixed(0) + ' ' + currencySymbol;
                } else {
                    newText += (shippingCost === 0 ? (codplugin_order.free_shipping_text || 'FREE') : originalText); 
                }
                $checkedLabel.text(newText);
            }
        }
    }; 

    function updateTotalPrice() {
        var count_number = $("#codplugin_c_number").val();
        var price = $("#codplugin_price").val();
        var d_price = $("#d_price").val();
        
        var num_count = parseInt(count_number);
        var num_price = parseFloat(price);
        var num_d_price = parseFloat(d_price);

        if (isNaN(num_d_price)) {
            num_d_price = 0;
        }

        if (!isNaN(num_price) && !isNaN(num_count)) {
            var update_price = num_price * num_count;
            var total_price = update_price + num_d_price;
            $("#codplugin_total_price").html(total_price.toFixed(0)); 
        } else {
             $("#codplugin_total_price").html('...'); 
        }
    }

    function updateVariationPrompt(isCitySelection) {
        var stateSelected = $('#codplugin_state').val();
        var citySelected = $('#codplugin_city').val();
        var notice = $('#codplugin-city-notice');
        var unselectedAttributes = [];

        // If user just selected a city, we know the notice should be active.
        if (isCitySelection) {
            noticeShownOnce = true;
        }

        // If the notice has been shown once, it should stay visible as long as a state is selected
        // and there are attributes to select.
        if (noticeShownOnce && stateSelected) {
            // Check for both CFVSW and default variations
            if ($('.variations_form').length > 0) {
                $('.variations_form select').each(function() {
                    if (!$(this).val()) {
                        var label = $("label[for='" + $(this).attr('id') + "']").text();
                        
                        if (/لون|color|coulor|couleur/i.test(label)) {
                            unselectedAttributes.push(codplugin_order.select_color_text);
                        } else if (/مقاس|size|حجم|طاي|taille/i.test(label)) {
                            unselectedAttributes.push(codplugin_order.select_size_text);
                        } else {
                            unselectedAttributes.push(codplugin_order.select_option_text);
                        }
                    }
                });
            } else if ($('.variation-prices').length > 0) {
                // For default radio variations, check if any is selected
                if (!$('input[type=radio][name=var_price]:checked').length) {
                    unselectedAttributes.push(codplugin_order.select_option_text || 'Please select an option');
                }
            }

            if (unselectedAttributes.length > 0) {
                var uniqueAttributes = [...new Set(unselectedAttributes)];
                var promptText = codplugin_order.please_select_text + ' ' + uniqueAttributes.join(' و ');
                notice.html(promptText).addClass('visible shake');
                setTimeout(function() {
                    notice.removeClass('shake');
                }, 500);
            } else {
                // All attributes are selected, so we can hide the notice.
                notice.removeClass('visible');
            }
        } else {
            // Hide the notice if no state is selected.
            notice.removeClass('visible');
            // If we are hiding the notice because the state was cleared, reset the flag.
            if (!stateSelected) {
                noticeShownOnce = false;
            }
        }

        // Enable/disable the place order button based on notice visibility
        updatePlaceOrderButtonState();
        // Enable/disable the add to cart button based on validations
        updateAddToCartButtonState();
    }

    // Function to enable/disable the place order button based on notice visibility and validations
    function updatePlaceOrderButtonState() {
        var notice = $('#codplugin-city-notice');
        var placeOrderButton = $('#nrwooconfirm');
        var placeOrderSubmit = $('input[name="codplugin-submit"]');
        var stateSelected = $('#codplugin_state').val();
        var variationsValid = validateVariationSelection();
        // Removed shipping method requirement - button enables when variation + state are selected
        
        // Disable button if notice is visible OR if no state is selected OR if variations are not valid
        if (notice.hasClass('visible') || !stateSelected || !variationsValid) {
            // Disable the container div
            placeOrderButton.prop('disabled', true).addClass('disabled');
            // Disable the submit input
            placeOrderSubmit.prop('disabled', true).addClass('disabled');
            
            // Store original values if not already stored
            if (!placeOrderSubmit.data('original-value')) {
                placeOrderSubmit.data('original-value', placeOrderSubmit.val());
            }
            
            // Change button text to indicate what's missing
            var missingText = '';
            if (!variationsValid) {
                missingText = codplugin_order && codplugin_order.select_variation_text ? codplugin_order.select_variation_text : 'يرجى تحديد الخيارات المتاحة';
            } else if (!stateSelected) {
                missingText = codplugin_order && codplugin_order.please_select_text && codplugin_order.form_state_placeholder ? 
                    codplugin_order.please_select_text + ' ' + codplugin_order.form_state_placeholder : 
                    'يرجى تحديد ولاية';
            } else {
                missingText = 'يرجى استكمال جميع الحقول المطلوبة';
            }
            
            placeOrderSubmit.val(missingText);
        } else {
            // All validations pass, enable the button
            placeOrderButton.prop('disabled', false).removeClass('disabled');
            placeOrderSubmit.prop('disabled', false).removeClass('disabled');
            
            // Restore original value
            if (placeOrderSubmit.data('original-value')) {
                placeOrderSubmit.val(placeOrderSubmit.data('original-value'));
            }
        }
    }

    // Function to enable/disable the add to cart button based on validations
    function updateAddToCartButtonState() {
        var variationsValid = validateVariationSelection();
        var addToCartButton = $('a.custom-atc-btn.product_type_variable');
        
        // Disable button if variations are not valid (state not required for add to cart)
        if (!variationsValid) {
            // Remove href to prevent navigation and add disabled styling
            addToCartButton.removeAttr('href').prop('disabled', true).addClass('disabled');
        } else {
            // All validations pass, restore href and enable button
            var variationId = '';
            if ($('.variations_form').length > 0) {
                variationId = $("input[name=variation_id]").val();
            } else if ($('.variation-prices').length > 0) {
                var selectedRadio = $("input[type=radio][name=var_price]:checked");
                if (selectedRadio.length > 0) {
                    variationId = selectedRadio.attr('id');
                }
            }
            
            if (variationId) {
                addToCartButton.attr('href', '?add-to-cart=' + variationId).prop('disabled', false).removeClass('disabled');
            } else {
                addToCartButton.removeAttr('href').prop('disabled', true).addClass('disabled');
            }
        }
    }
    
    var v_price =  $("#codplugin_price").val(); 
    var product_id = $("input[name=product_id]").val();

    $("#codplugin_v_price").html(v_price);
    $(".codplugin-field select").prop('required',true);

    function populateStandardCities(stateValue) {
        var selectedCity = preservedStandardCity;
        var citiesByState = window.codplugin_cities || {};
        var formCityPlaceholder = codplugin_order.form_city_placeholder || 'City';
        var city_options = '<option value="">' + formCityPlaceholder + '</option>';

        if (citiesByState && citiesByState[stateValue] && typeof citiesByState[stateValue] === 'object' && !Array.isArray(citiesByState[stateValue])) {
            $.each(citiesByState[stateValue], function(city_id, city_name) {
                if (typeof city_id === 'string' || typeof city_id === 'number') {
                     if (typeof city_name === 'string') {
                        city_options += '<option value="' + city_id + '">' + city_name + '</option>';
                     }
                }
            });
        }
        else if (citiesByState && citiesByState[stateValue] && Array.isArray(citiesByState[stateValue])) {
            $.each(citiesByState[stateValue], function(index, city_item) {
                if (typeof city_item === 'string') {
                    city_options += '<option value="' + city_item + '">' + city_item + '</option>';
                } else if (typeof city_item === 'object' && city_item.id && city_item.name) {
                    city_options += '<option value="' + city_item.id + '">' + city_item.name + '</option>';
                }
            });
        }
        $('select#codplugin_city').html(city_options);
        if (selectedCity && $('select#codplugin_city option[value="' + selectedCity + '"]').length > 0) {
            $('select#codplugin_city').val(selectedCity).trigger('chosen:updated');
        } else if (preservedPickupCityText) {
            var matchFound = false;
            var searchText = preservedPickupCityText.toUpperCase().replace(/\s+/g, ' ').trim().substring(0, 4);
            $('select#codplugin_city option').each(function() {
                var optionText = $(this).text().toUpperCase().replace(/\s+/g, ' ').trim();
                if (optionText.includes(searchText)) {
                    $(this).prop('selected', true);
                    matchFound = true;
                    return false;
                }
            });
        }
    }

    function fetchAndPopulateFilteredCommunes(selectedStateValue, selectedShippingMethodValue) {
        var selectedCity = preservedPickupCity;
        var placeholder = codplugin_order.form_city_placeholder || 'City';
        $('select#codplugin_city').html('<option value="">' + placeholder + '</option>');

        if (!selectedStateValue || !selectedShippingMethodValue || !selectedShippingMethodValue.startsWith('local_pickup')) {
            populateStandardCities(selectedStateValue);
            return;
        }

        // Abort any pending city AJAX request
        if (cityAjax && cityAjax.readyState !== 4) {
            cityAjax.abort();
        }

        $("#codplugin_gif").css('display', 'block');

        cityAjax = $.ajax({
            url: codplugin_order.ajax_url,
            type: 'POST',
            data: {
                action: 'get_filtered_communes',
                wilaya_id: selectedStateValue, 
                shipping_method_id: selectedShippingMethodValue
            },
            success: function(response) {
                if (response.success && response.data && (Array.isArray(response.data) && response.data.length > 0 || typeof response.data === 'object' && Object.keys(response.data).length > 0)) {
                    var city_options = '<option value="">' + placeholder + '</option>';
                    
                    if (Array.isArray(response.data)) {
                        $.each(response.data, function(index, commune) {
                            if (commune && typeof commune.id === 'string' && typeof commune.name === 'string') {
                                city_options += '<option value="' + commune.id + '">' + commune.name + '</option>'; 
                            }
                        });
                    } else if (typeof response.data === 'object') {
                        $.each(response.data, function(key, commune) {
                             if (commune && typeof commune.id === 'string' && typeof commune.name === 'string') {
                                city_options += '<option value="' + commune.id + '">' + commune.name + '</option>';
                             }
                        });
                    }
                    $('select#codplugin_city').html(city_options);
                    var matchFound = false;
                    if (selectedCity && $('select#codplugin_city option[value="' + selectedCity + '"]').length > 0) {
                        $('select#codplugin_city').val(selectedCity).trigger('chosen:updated');
                        matchFound = true;
                    } else if (preservedStandardCityText) {
                        var searchText = preservedStandardCityText.toUpperCase().replace(/\s+/g, ' ').trim();
                        $('select#codplugin_city option').each(function() {
                            var optionText = $(this).text().toUpperCase().replace(/\s+/g, ' ').trim();
                            if (optionText.includes(searchText)) {
                                $(this).prop('selected', true);
                                matchFound = true;
                                return false;
                            }
                        });
                    }

                    if (!matchFound && preservedStandardCityText) {
                        var notice = $('#codplugin-city-notice');
                        notice.html(codplugin_order.no_stopdesk_advice_text_arabic);
                        notice.addClass('visible');
                    } else {
                        $('#codplugin-city-notice').removeClass('visible');
                    }
                } else {
                    populateStandardCities(selectedStateValue);
                }
            },
            error: function(jqXHR) {
                if (jqXHR.statusText === 'abort') {
                    console.log('City fetch AJAX aborted');
                    return;
                }
                populateStandardCities(selectedStateValue);
            },
            complete: function() {
                $("#codplugin_gif").css('display', 'none');
            }
        });
    }

    function updateShippingCostAndStock(stateText, shippingMethodValue, variation_id) {
        var is_variable_product = $('.variations_form').length > 0 || $('.variation-prices').length > 0;

        if (is_variable_product && !variation_id) {
            return;
        }

        // Abort any pending shipping cost AJAX request
        if (shippingCostAjax && shippingCostAjax.readyState !== 4) {
            shippingCostAjax.abort();
        }

        if (stateText !== '' && $("#codplugin_state").val() !== '' && shippingMethodValue) { 
            $("#codplugin_gif").css('display', 'block');
            var costData = {
                action: 'codplugin_woo_order_action',
                value: stateText, 
                product_id: $("input[name=product_id]").val(),
                variation_id: variation_id,
                d_method: shippingMethodValue,
            };
            shippingCostAjax = $.ajax({
                url: codplugin_order.ajax_url,
                type: "post",
                data: costData,
                success: function(costVal) {
                    var costResponse = typeof costVal === 'string' ? $.parseJSON(costVal) : costVal;
                    var cost = parseFloat(costResponse.cost);
                    var stock = costResponse.stock;
                    var $qtyContainer = $('.form-qte');
                    if (typeof stock === 'number' && !isNaN(stock)) { $qtyContainer.data('max-qty', stock).attr('data-max-qty', stock); } else { $qtyContainer.removeData('max-qty').removeAttr('data-max-qty'); }
                    
                    if (cost === 0) { 
                        $('#codplugin_d_has_price').hide(); 
                        $('#codplugin_d_free').show(); 
                        $("#codplugin_d_price").html('');
                    } else if (!isNaN(cost) && cost > 0) { 
                        $('#codplugin_d_has_price').show(); 
                        $('#codplugin_d_free').hide(); 
                        $("#codplugin_d_price").html(cost.toFixed(0));
                    } else {
                        $('#codplugin_d_has_price').show(); 
                        $('#codplugin_d_free').hide(); 
                        $("#codplugin_d_price").html('Error');
                    }
                    $("#d_price").attr('value', isNaN(cost) ? 0 : cost);

                    var $stockDisplaySpan = $('.codplugin-available-stock .stock-value');
                    if ($stockDisplaySpan.length) { if (typeof stock === 'number' && !isNaN(stock)) { var inStockText = codplugin_order && codplugin_order.in_stock_text ? codplugin_order.in_stock_text : 'in stock'; $stockDisplaySpan.text(stock + ' ' + inStockText); } else { $stockDisplaySpan.text(''); } }
                    updateTotalPrice(); 
                    checkStockLimit(); 
                    $('.codplugin_currency').show();
                    updatePreset2ShippingLabels();
                },
                error: function(jqXHR) {
                    if (jqXHR.statusText === 'abort') {
                        console.log('Shipping cost AJAX aborted');
                        return;
                    }
                    $("#d_price").attr('value', 0); $("#codplugin_d_price").html('Error');
                    updateTotalPrice(); checkStockLimit();
                },
                complete: function() {
                    $("#codplugin_gif").css('display', 'none');
                }
            });
        } else {
            $("#d_price").attr('value', 0);
            if ($("#codplugin_state").val() === '') {
                restoreInitialDeliveryPricePlaceholder();
            } else {
                 $("#codplugin_d_price").html('...');
                 $('#codplugin_d_has_price').show();
                 $('#codplugin_d_free').hide();
            }
            updateTotalPrice();
            checkStockLimit();
        }
    }

    $("#shipping-methods").on('change', function() {
        var var_d_method = $('#shipping-methods input[name="shipping_method"]:checked').val();
        var stateValue = $("#codplugin_state").val(); 
        var stateText = $("#codplugin_state option:selected").text(); 
        var notice = $('#codplugin-city-notice');
        
        $("#codplugin_d_method").attr('value', var_d_method);

        // Always repopulate the city list on shipping change to ensure the correct list is shown
        // and the preserved city can be re-selected if it exists.
        if (codplugin_order.is_bordrou_active && var_d_method && var_d_method.startsWith('local_pickup')) {
            notice.html('<span class="spinner"></span>' + codplugin_order.searching_stopdesk_text).addClass('visible');
            fetchAndPopulateFilteredCommunes(stateValue, var_d_method);
        } else {
            notice.removeClass('visible');
            // Otherwise (Bordereau inactive OR a standard shipping method is chosen),
            // populate the standard city list for the selected state.
            if (stateValue !== '') {
                 populateStandardCities(stateValue);
            } else {
                 // If no state is selected, just clear the city dropdown.
                 $('select#codplugin_city').html('<option value="">' + (codplugin_order.form_city_placeholder || 'City') + '</option>');
            }
        }

        // This part remains unchanged and always runs to update the cost.
        var variation_id = null;
        if ($('.variations_form').length > 0) {
            variation_id = $("input[name=variation_id]").val();
        } else if ($('.variation-prices').length > 0) {
            var selectedRadio = $("input[type=radio][name=var_price]:checked");
            if (selectedRadio.length > 0) {
                variation_id = selectedRadio.attr('id');
            }
        }
        
        updateShippingCostAndStock(stateText, var_d_method, variation_id);
        var shippingAjaxCompleted = false;
        $(document).off('ajaxComplete.shippingUpdate').on('ajaxComplete.shippingUpdate', function(event, xhr, settings) {
            if (!shippingAjaxCompleted && settings.data && settings.data.includes('action=codplugin_woo_order_action')) {
                shippingAjaxCompleted = true;
                updatePreset2ShippingLabels();
                 $(document).off('ajaxComplete.shippingUpdate');
            }
        });
    });

    $('#codplugin_state').on('change', function() {
        var stateValue = $(this).val(); 
        var stateText = $("#codplugin_state option:selected").text(); 
        var variation_id = null;
        var is_variable = $('.variations_form').length > 0 || $('.variation-prices').length > 0;

        // Get variation ID based on the type of variation system
        if ($('.variations_form').length > 0) {
            variation_id = $("input[name=variation_id]").val();
        } else if ($('.variation-prices').length > 0) {
            var selectedRadio = $("input[type=radio][name=var_price]:checked");
            if (selectedRadio.length > 0) {
                variation_id = selectedRadio.attr('id');
            }
        }

        // Preserve the current shipping method before clearing it
        var currentShippingMethod = $('#shipping-methods input[name="shipping_method"]:checked').val();
        if (currentShippingMethod) {
            preservedShippingMethod = currentShippingMethod;
        }

        // Immediately populate cities based on the selected state
        if (stateValue !== '') {
            populateStandardCities(stateValue);
        } else {
            // If state is cleared, clear cities
            $('select#codplugin_city').html('<option value="">' + (codplugin_order.form_city_placeholder || 'City') + '</option>');
        }
        
        // Clear shipping methods and delivery method value
        $('#shipping-methods').html('');
        $("#codplugin_d_method").attr('value', '');
        
        // Check if all variations are selected for CFVSW
        var allVariationsSelected = true;
        if ($('.variations_form').length > 0) {
            $('.variations_form select').each(function() {
                if (!$(this).val()) {
                    allVariationsSelected = false;
                }
            });
        } else if ($('.variation-prices').length > 0) {
            // For default radio variations, check if any is selected
            allVariationsSelected = $('input[type=radio][name=var_price]:checked').length > 0;
        }

        if (is_variable && !allVariationsSelected) {
            $("#codplugin_d_price").html('<span class="summary-select-state">حدد خيارا 🛒</span>');
            $('.codplugin_currency').hide();
            $('#codplugin_d_has_price').show();
            $('#codplugin_d_free').hide();
            $("#d_price").attr('value', 0);
            updateTotalPrice();
            updateVariationPrompt(); // Check if a notice should be shown
        } else if (stateValue === '') {
            // This runs when all variations are selected but no state is selected
            var stateLabel = codplugin_order && codplugin_order.form_state_placeholder ? codplugin_order.form_state_placeholder : 'ولاية';
            $("#codplugin_d_price").html('<span class="summary-select-state">يرجى تحديد ' + stateLabel + '</span>');
            $('.codplugin_currency').hide();
            $('#codplugin_d_has_price').show();
            $('#codplugin_d_free').hide();
            $("#d_price").attr('value', 0);
            updateTotalPrice();
        } else {
            // This runs for simple products or when all variations ARE selected and state is selected
            restoreInitialDeliveryPricePlaceholder(); 
        }
        
        checkStockLimit(); 
        updatePlaceOrderButtonState(); // Update button state when state changes

        // ALWAYS fetch shipping methods if a state is selected.
        if (stateValue !== '') {
            // Abort any pending state change AJAX request
            if (stateChangeAjax && stateChangeAjax.readyState !== 4) {
                stateChangeAjax.abort();
            }

            var spinnerTimeoutId = null;
            spinnerTimeoutId = setTimeout(function() { $("#codplugin_gif").css('display', 'block'); }, 2000);
            
            stateChangeAjax = $.ajax({
                url: codplugin_order.ajax_url,
                type: 'POST',
                data: { action: 'get_shipping_methods', state: stateText },
                success: function(response) {
                    if (spinnerTimeoutId) { clearTimeout(spinnerTimeoutId); spinnerTimeoutId = null; }
                    $("#codplugin_gif").css('display', 'none');
                    
                    $('#shipping-methods').html(''); // Always clear first

                    // Get variation ID based on the type of variation system
                    var variation_id = null;
                    if ($('.variations_form').length > 0) {
                        variation_id = $("input[name=variation_id]").val();
                    } else if ($('.variation-prices').length > 0) {
                        var selectedRadio = $("input[type=radio][name=var_price]:checked");
                        if (selectedRadio.length > 0) {
                            variation_id = selectedRadio.attr('id');
                        }
                    }
                    
                    var is_variable = $('.variations_form').length > 0 || $('.variation-prices').length > 0;

                    // If it's a variable product and no variation is selected, we still show shipping, but don't calculate price.
                    if (is_variable && !variation_id) {
                        if (response.type === 'multiple') {
                             $('#shipping-methods').html(response.html);
                        }
                        // Don't return, allow the rest of the logic to run to select a default shipping method.
                    }

                    if (response.type === 'single') {
                        $("#codplugin_d_method").attr('value', response.rate_id);
                        if (allVariationsSelected) {
                            updateShippingCostAndStock(stateText, response.rate_id, variation_id);
                        }
                    } else if (response.type === 'multiple') {
                        $('#shipping-methods').html(response.html);
                        if ($('body').hasClass('codform-preset-2')) {
                            $('#shipping-methods .codplugin-shipping-label').each(function() {
                                var $label = $(this);
                                $label.data('original-text', $label.text());
                            });
                        }
                        
                        var $methodToSelect = null;
                        if (preservedShippingMethod && $('#shipping-methods input[value="' + preservedShippingMethod + '"]').length > 0) {
                            $methodToSelect = $('#shipping-methods input[value="' + preservedShippingMethod + '"]');
                        } else {
                            $methodToSelect = $('#shipping-methods input[name="shipping_method"]:first');
                        }
                        preservedShippingMethod = null; // Reset after use

                        if ($methodToSelect && $methodToSelect.length) {
                            $methodToSelect.prop('checked', true).trigger('change'); // Trigger change to run all related logic
                        } else {
                            $("#d_price").attr('value', 0);
                            $('#codplugin_d_has_price').hide();
                            $('#codplugin_d_free').show();
                            $("#codplugin_d_price").html('');
                            updateTotalPrice();
                        }
                    } else { // response.type === 'none'
                        $("#d_price").attr('value', 0);
                        $('#codplugin_d_has_price').hide();
                        $('#codplugin_d_free').show();
                        $("#codplugin_d_price").html('');
                        updateTotalPrice();
                    }
                },
                error: function(jqXHR) {
                     if (jqXHR.statusText === 'abort') {
                        console.log('State change AJAX aborted');
                        if (spinnerTimeoutId) { clearTimeout(spinnerTimeoutId); spinnerTimeoutId = null; }
                        $("#codplugin_gif").css('display', 'none'); 
                        return;
                    }
                     if (spinnerTimeoutId) { clearTimeout(spinnerTimeoutId); spinnerTimeoutId = null; }
                     $("#codplugin_gif").css('display', 'none'); 
                     $("#d_price").attr('value', 0);
                     $('#codplugin_d_has_price').hide();
                     $('#codplugin_d_free').show();
                     $("#codplugin_d_price").html('');
                     updateTotalPrice();
                }
            });
        }
    });

    if ($(".has-no-states")[0]){
        var everyprice = parseFloat($("#everyprice").val());
        $("#d_price").attr('value', everyprice);
        
        if (everyprice === 0) { 
            $('#codplugin_d_has_price').hide(); 
            $('#codplugin_d_free').show(); 
            $("#codplugin_d_price").html('');
        } else if (!isNaN(everyprice) && everyprice > 0) { 
            $('#codplugin_d_has_price').show(); 
            $('#codplugin_d_free').hide(); 
            $("#codplugin_d_price").html(everyprice.toFixed(0));
        } else {
            $('#codplugin_d_has_price').show(); 
            $('#codplugin_d_free').hide(); 
            $("#codplugin_d_price").html('...'); 
        }
        $('.codplugin_currency').show();
        updateTotalPrice();
    } else {
        if ($("#codplugin_state").val() === '') {
            restoreInitialDeliveryPricePlaceholder();
        }
    }

    // Function to get all variation data from the form
    function getVariationData() {
        var variationData = [];
        try {
            var variationsJson = $('form.variations_form').data('product_variations');
            if (variationsJson && typeof variationsJson === 'string') {
                variationData = JSON.parse(variationsJson);
            } else if (variationsJson && Array.isArray(variationsJson)) {
                variationData = variationsJson;
            }
        } catch (e) {
            console.log('Error parsing variation data:', e);
        }
        return variationData;
    }

    // Function to find the cheapest available variation price
    function getCheapestVariationPrice() {
        var variations = getVariationData();
        if (!variations || variations.length === 0) {
            return null;
        }

        var cheapestPrice = null;
        var cheapestVariation = null;

        for (var i = 0; i < variations.length; i++) {
            var variation = variations[i];
            // Only consider variations that are in stock and have a price
            if (variation.is_in_stock !== false && variation.display_price && variation.display_price > 0) {
                if (cheapestPrice === null || parseFloat(variation.display_price) < parseFloat(cheapestPrice)) {
                    cheapestPrice = variation.display_price;
                    cheapestVariation = variation;
                }
            }
        }

        return cheapestVariation;
    }

    // Function to display the cheapest variation price
    function displayCheapestPrice() {
        console.log('displayCheapestPrice called');
        var cheapestVariation = getCheapestVariationPrice();
        console.log('Cheapest variation found:', cheapestVariation);
        
        if (cheapestVariation) {
            var price = cheapestVariation.display_price;
            var regularPrice = cheapestVariation.display_regular_price;
            
            console.log('Setting cheapest price:', price, 'Regular price:', regularPrice);
            
            // Update all price elements
            $("#codplugin_price").attr('value', price);
            $("#codplugin_v_price").html(price);
            $('.price ins .woocommerce-Price-amount.amount bdi').html(price + '&nbsp;' + '<span class="woocommerce-Price-currencySymbol">' + codplugin_order.currency_symbol + '</span>');
            
            // Handle regular price (sale price)
            if (regularPrice && parseFloat(regularPrice) > parseFloat(price)) {
                $('.price del .woocommerce-Price-amount.amount bdi').html(regularPrice + '&nbsp;' + '<span class="woocommerce-Price-currencySymbol">' + codplugin_order.currency_symbol + '</span>');
                $('.price del').show();
            } else {
                $('.price del').hide();
            }
            
            // Update total price as well
            updateTotalPrice();
            
            return true;
        } else {
            console.log('No cheapest variation found');
        }
        return false;
    }

    function handleVariationChange(variationPrice, variationId, variationRegularPrice) {
        // Store the current shipping method and city before they get reset by the change trigger
        var currentShippingMethod = $('#shipping-methods input[name="shipping_method"]:checked').val();
        if (currentShippingMethod) {
            preservedShippingMethod = currentShippingMethod;
            // Preserve both types of cities
            if (currentShippingMethod.startsWith('local_pickup')) {
                preservedPickupCity = $('#codplugin_city').val();
            } else {
                preservedStandardCity = $('#codplugin_city').val();
            }
        }

        // Always get the shipping method from the reliable hidden input
        var var_d_method = $("#codplugin_d_method").val();
        var stateText = $("#codplugin_state option:selected").text();
        var stateValue = $("#codplugin_state").val();

        $("#var_id").val(variationId);

        // Handle different scenarios for price display
        if (variationId === '') {
            // Variation is cleared - show cheapest price
            var displayedCheapest = displayCheapestPrice();
            if (!displayedCheapest) {
                // Fallback to empty if no variations available
                $("#codplugin_price").attr('value', '');
                $("#codplugin_v_price").html('');
                $('.price ins .woocommerce-Price-amount.amount bdi').html('');
                $('.price del').hide();
            }
        } else {
            // Specific variation is selected - show its price
            $("#codplugin_price").attr('value', variationPrice);
            $("#codplugin_v_price").html(variationPrice);
            $('.price ins .woocommerce-Price-amount.amount bdi').html(variationPrice + '&nbsp;' + '<span class="woocommerce-Price-currencySymbol">' + codplugin_order.currency_symbol + '</span>');

            if (variationRegularPrice && variationRegularPrice !== variationPrice) {
                $('.price del .woocommerce-Price-amount.amount bdi').html(variationRegularPrice + '&nbsp;' + '<span class="woocommerce-Price-currencySymbol">' + codplugin_order.currency_symbol + '</span>');
                $('.price del').show();
            } else {
                $('.price del').hide();
            }
        }

        if (variationId === '') {
            $("a.custom-atc-btn.product_type_variable").attr("href", "").removeClass("add_to_cart_button popup-alert");
            
            // When variation is reset, clear shipping methods and show the "Select an option" message.
            if (stateValue !== '') {
                $('#shipping-methods').html('');
                $("#codplugin_d_method").attr('value', '');
                $("#codplugin_d_price").html('<span class="summary-select-state">حدد خيارا 🛒</span>');
                $('.codplugin_currency').hide();
                $('#codplugin_d_has_price').show();
                $('#codplugin_d_free').hide();
                $("#d_price").attr('value', 0);
                updateTotalPrice();
            }
            updateVariationPrompt(); // This will now check for state/city and show the notice if needed
            
        } else {
            $("a.custom-atc-btn.product_type_variable").attr("href", "?add-to-cart=" + variationId).attr("data-product_id", variationId).addClass("add_to_cart_button popup-alert");
            
            // Check if all variations are selected before triggering state change
            var allVariationsSelected = true;
            if ($('.variations_form').length > 0) {
                $('.variations_form select').each(function() {
                    if (!$(this).val()) {
                        allVariationsSelected = false;
                    }
                });
            }

            updateVariationPrompt(); // Check for notice regardless of selection state

            if (allVariationsSelected) {
                if (stateValue && stateValue !== '') {
                    // All variations are selected and a state exists.
                    $('#codplugin_state').trigger('change');
                } else {
                    // All variations selected, but no state yet - show state selection message
                    var stateLabel = codplugin_order && codplugin_order.form_state_placeholder ? codplugin_order.form_state_placeholder : 'ولاية';
                    $("#codplugin_d_price").html('<span class="summary-select-state">يرجى تحديد ' + stateLabel + '</span>');
                    $('.codplugin_currency').hide();
                    $('#codplugin_d_has_price').show();
                    $('#codplugin_d_free').hide();
                    $("#d_price").attr('value', 0);
                    updateTotalPrice();
                }
            } else {
                // Not all variations are selected yet. Keep the "Select Option" message.
                $("#codplugin_d_price").html('<span class="summary-select-state">حدد خيارا 🛒</span>');
                $('.codplugin_currency').hide();
                $('#codplugin_d_has_price').show();
                $('#codplugin_d_free').hide();
                $("#d_price").attr('value', 0);
                updateTotalPrice();
            }
        }
    }

    jQuery( '.variations_form' ).each( function() {
        jQuery(this).on( 'found_variation', function( event, variation ) {
            console.log('found_variation event triggered:', variation);
            handleVariationChange(variation.display_price, variation.variation_id, variation.display_regular_price);
        });
        jQuery(this).on('reset_data', function() {
            console.log('reset_data event triggered');
            handleVariationChange('', '', ''); 
        });
        
        // Additional event listeners for variation swatch plugins
        jQuery(this).on( 'woocommerce_variation_select', function( event, variation ) {
            console.log('woocommerce_variation_select event triggered:', variation);
            if (variation) {
                handleVariationChange(variation.display_price, variation.variation_id, variation.display_regular_price);
            } else {
                handleVariationChange('', '', '');
            }
        });
        
        jQuery(this).on( 'variation_reset', function() {
            console.log('variation_reset event triggered');
            handleVariationChange('', '', '');
        });
    });

    jQuery( '.variation-prices' ).each( function() {
        $("input[type=radio][name=var_price]").on('change', function() {
           var regularPrice = $(this).data('regular-price');
           handleVariationChange(this.value, this.id, regularPrice);
        });
    });
    
    // Additional global listeners for variation swatch plugins
    $(document).on('click', '.reset_variations', function() {
        console.log('Reset variations button clicked');
        setTimeout(function() {
            handleVariationChange('', '', '');
        }, 100);
    });
    
    // Watch for changes in variation select elements (for swatch plugins)
    $(document).on('change', '.variations_form select', function() {
        var allSelects = $('.variations_form select');
        var allSelected = true;
        allSelects.each(function() {
            if (!$(this).val()) {
                allSelected = false;
            }
        });
        
        if (!allSelected) {
            console.log('Not all variations selected - showing cheapest price');
            setTimeout(function() {
                handleVariationChange('', '', '');
            }, 100);
        }
    });

    checkStockLimit();

    $("#codplugin_add_button").on("click", function() {
        if ($(this).hasClass('disabled')) return;
        var count_number = $("#codplugin_c_number").val();
        var update_number = parseInt(count_number) + 1;
        $("#codplugin_c_number").attr('value', update_number);
        $("#codplugin_count_button").html(update_number);
        $("#codplugin_count_number").html(update_number);
        $("a.custom-atc-btn").attr("data-quantity", update_number);
        updateTotalPrice();
        checkStockLimit();
    });

    $("#codplugin_remove_button").on("click", function() {
        if ($(this).hasClass('disabled')) return;
        var count_number = $("#codplugin_c_number").val();
        var update_number = parseInt(count_number) - 1;
        if (update_number < 1) update_number = 1;
        $("#codplugin_c_number").attr('value', update_number);
        $("#codplugin_count_button").html(update_number);
        $("#codplugin_count_number").html(update_number);
        $("a.custom-atc-btn").attr("data-quantity", update_number);
        updateTotalPrice();
        checkStockLimit();
    });
   
    $("#codplugin_order_history").click(function() {
        $("#codplugin_show_hide").toggle();
    });

    if ($('input[name="chargily_pay_active_for_form"]').length > 0) {
        $('input[name="codplugin_payment_method"]').on('change', function() {
            if ($(this).val() === 'chargily_pay') {
                $('#chargily_pay_fields_container').slideDown();
            } else {
                $('#chargily_pay_fields_container').slideUp();
            }
        });
        $('input[name="chargily_payment_method"]:checked').trigger('change');
    }

    $("#codplugin_woo_single_form").on("submit", function(e) {
        // Check if the place order button is disabled (notice is visible)
        if ($('#nrwooconfirm').prop('disabled')) {
            e.preventDefault();
            return false;
        }

        if ($(this).attr('method') !== 'POST') { 
            var $qtyContainer = $('.form-qte');
            var maxQty = $qtyContainer.data('max-qty');
            var currentQty = parseInt($('#codplugin_c_number').val());

            if (typeof maxQty === 'number' && !isNaN(maxQty) && currentQty > maxQty) {
                e.preventDefault(); 
                checkStockLimit(); 
                alert(codplugin_order && codplugin_order.stock_limit_alert ? codplugin_order.stock_limit_alert : 'Quantity exceeds available stock.');
            }
        }

        // Check for variation selection before allowing form submission
        if (!validateVariationSelection()) {
            e.preventDefault();
            alert(codplugin_order && codplugin_order.select_variation_text ? codplugin_order.select_variation_text : 'Please select a product variation before placing your order.');
            return false;
        }
    });

	$(document).on('click', '.codform-file-upload-button', function() {
		$(this).closest('.codform-custom-file-input').find('#codform_uploaded_file').trigger('click');
	});

	$(document).on('change', '#codform_uploaded_file', function() {
		var filename = $(this).val().split('\\').pop();
		var $filenameSpan = $(this).closest('.codform-custom-file-input').find('.codform-file-upload-filename');
		if (filename) {
			$filenameSpan.text(filename);
		} else {
			$filenameSpan.text(codplugin_order.no_file_chosen_text || 'No file chosen'); 
		}
	});
    
    var initialSelectedState = $('#codplugin_state').val();
    if (initialSelectedState && initialSelectedState !== '') {
        populateStandardCities(initialSelectedState);
        $('#codplugin_state').trigger('change');
    }

    // Initial check for add to cart button state when page loads
    updateAddToCartButtonState();

    if ($.fn.chosen) {
        $('#codplugin_state, #codplugin_city').chosen({
            search_contains: true,
            placeholder_text_single: $(this).attr('placeholder')
        });
    }

    $(window).on('scroll', function() {
        var notice = $('#codplugin-city-notice');
        if (notice.hasClass('visible')) {
            var form = $('#codplugin-checkout');
            var formTop = form.offset().top;
            var formBottom = formTop + form.outerHeight();
            var windowTop = $(window).scrollTop();
            var windowBottom = windowTop + $(window).height();

            if (windowBottom < formTop || windowTop > formBottom) {
                notice.css('opacity', 0);
            } else {
                notice.css('opacity', 1);
            }
        }
    });

    if ($('body').hasClass('single-product') && $('.variations_form').length > 0) {
        var initialVariationId = $('input[name=variation_id]').val();
        if (!initialVariationId || initialVariationId === '0' || initialVariationId === '') {
            // Show cheapest price when no variation is initially selected
            // Add delay to ensure variation data is loaded
            setTimeout(function() {
                var displayedCheapest = displayCheapestPrice();
                if (!displayedCheapest) {
                    // Fallback if no variations available
                    restoreInitialDeliveryPricePlaceholder();
                }
            }, 500);
        }
    }
    
    // Also check periodically for variation data availability
    var priceCheckInterval = setInterval(function() {
        if ($('body').hasClass('single-product') && $('.variations_form').length > 0) {
            var currentVariationId = $('input[name=variation_id]').val();
            var currentPrice = $("#codplugin_price").val();
            
            // If no variation is selected and no price is showing, try to show cheapest
            if ((!currentVariationId || currentVariationId === '0' || currentVariationId === '') && 
                (!currentPrice || currentPrice === '' || currentPrice === '0')) {
                console.log('Price check interval - showing cheapest price');
                displayCheapestPrice();
            }
        } else {
            clearInterval(priceCheckInterval);
        }
    }, 1000);

    $('input[name="phone_number"], input[name="billing_phone"]').on('keyup change', function() {
        var phoneNumber = $(this).val();
        if (phoneNumber.length >= 5) {
            debouncedSaveAbandonedCartDraft();
        }
    });

    $('#codplugin_woo_single_form input, #codplugin_woo_single_form select, #codplugin_woo_single_form textarea').on('change', function() {
        var phoneNumberInput = $('input[name="phone_number"]').val() || $('input[name="billing_phone"]').val();
        if (phoneNumberInput && phoneNumberInput.length >= 5) {
            debouncedSaveAbandonedCartDraft();
        }
    });

    const debouncedSaveAbandonedCartDraft = debounce(saveAbandonedCartDraft, 1500);

    function saveAbandonedCartDraft() {
        console.log("Attempting to save abandoned cart draft...");
        var formDataArray = $("#codplugin_woo_single_form").serializeArray();
        
        var productId = $("input[name=product_id]").val();
        if (productId && !formDataArray.some(item => item.name === 'product_id')) {
            formDataArray.push({ name: 'product_id', value: productId });
        }
        var variationId = $("#var_id").val();
        if (variationId && !formDataArray.some(item => item.name === 'var_id')) {
            formDataArray.push({ name: 'var_id', value: variationId });
        }
        
        $.ajax({
            url: codplugin_order.ajax_url,
            type: "post",
            data: {
                action: "abandoned_carts",
                fields: formDataArray,
                abandoned_order_id: abandonedOrderId
            },
            success: function(response) {
                var obj = $.parseJSON(response);
                if (obj.order_id) {
                    abandonedOrderId = obj.order_id;
                    console.log("Abandoned cart draft saved/updated: " + obj.order_id);
                } else {
                    console.error("Failed to save abandoned cart draft: " + obj.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error saving abandoned cart draft: " + textStatus, errorThrown);
            }
        });
    }
});

jQuery(document).ready(function($) {
    
    function updateCheckedClass() {
        $('input[name="var_price"]').each(function() {
            if ($(this).is(':checked')) {
                $(this).closest('tr').addClass('checked-var');
            } else {
                $(this).closest('tr').removeClass('checked-var');
            }
        });
    }

    updateCheckedClass();
    $('input[name="var_price"]').change(function() {
        updateCheckedClass();
    });

    $('.radio-variation-prices tr').click(function() {
        $(this).find('input[name="var_price"]').prop('checked', true).trigger('change');
    });

    // Prevent "Add to Cart" button click when disabled
    $(document).on('click', 'a.custom-atc-btn.product_type_variable', function(e) {
        if ($(this).prop('disabled') || $(this).hasClass('disabled')) {
            e.preventDefault();
            return false;
        }
    });

    // Prevent "Most Popular" badge from auto-selecting variations
    $(document).on('click', '.most-popular-badge', function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    });

    $(window).on('scroll resize', function() {
        $('#codplugin-checkout').each(function() {
             var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            var viewportMiddle = (viewportTop + viewportBottom) / 2;
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var elementMiddle = (elementTop + elementBottom) / 2;

            if (elementMiddle >= viewportTop && elementMiddle <= viewportBottom) {
                $(this).css({
                    'transform': 'scale(1.04)', 
                    'box-shadow': '2px -3px 60px 0 rgb(54 57 73 / 9%)', 
                    'transition': 'transform 0.5s, box-shadow 0.5s' ,
                });
            } else {
                $(this).css({
                    'transform': 'scale(1)',
                    'box-shadow': 'none',
                    'transition': 'transform 0.5s, box-shadow 0.5s', 
                });
            }
        }); 
    }); 
});
