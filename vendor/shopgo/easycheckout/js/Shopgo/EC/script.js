/* 
Shopgo EasyCheckout version 2.2.2 
*/
var debug = false;
var performance = false;
var verbose = false;
var consoleLog = false;
var currentPaymentMethod = null;
var currentShippingMethod = null;
var defaultPaymentMethod = null;
var defaultShippingMethod = null;
var currentBillingAddressId = null;
var currentShippingAddressId = null;
var use_billing_address = true;
var isBillingSaved = false;
var isBillingAddressValid = false;
var isShippingAddressValid = false;
var paymentRedirectURL = null;
var cities = [];
var billingCityInput;
var shippingCityInput;
var mena_countries = ['JO', 'PS', 'LB', 'SY', 'IQ','EG', 'TN','DZ', 'LY', 'MA', 'AE', 'SD', 'SA', 'YE', 'QA', 'BH', 'OM', 'MR', 'DJ', 'KW', 'SO'];

// CITIES INTEGRATION: START
var autocomplete_request_delay = 400;
var autocomplete_input = '';
var last_autocomplete_queried_term = '';
var autocomplete_changed_by_selecting_from_list = false;
var special_case_timeout;
var autocomplete_allow_any_value_as_in_settings;
var clearAutocompleteField;
var checkIfAutocompleteInputIsValid;
// CITIES INTEGRATION: END

// Aramex city lookup
var aramexCities = null;
var City = new Array();
var CCity = new Array();
var ZipCode = new Array();
var CZipCode = new Array();
var State = new Array();   
var CState = new Array();

(function ($) {
    
    $(window).load(function(){
        // full load
        NProgress.done();
    });
    
    $(document).ready(function () {

        // Disable console
        !consoleLog && (window.console = {
          log   : function(){},
          info  : function(){},
          error : function(){},
          warn  : function(){}
        });
        
        NProgress.start(); 

        easyCheckout.init();
        
        alertify.set({
            labels : {
                ok     : "OK",
                cancel : "Cancel"
            },
            delay : 5000,
            buttonReverse : false,
            buttonFocus   : "ok"
        });
        
        alertify.showMessage = alertify.extend("custom");
        
        $('#billing\\:country_id').kendoDropDownList();

        $('#shipping\\:country_id').kendoDropDownList();
        
        $('#billing\\:region_id').kendoDropDownList();

        $('#shipping\\:region_id').kendoDropDownList();

        // global messages close btn
        $('html').on('click', '#globalMessages .close.icon', function () {
            $('#globalMessages').transition('fade out');
        });

        // switch to shipping form
        $('.billing #billing\\:ship_to_different_address.ui.checkbox').checkbox({
            onChange: function () {
                //
            },
            onEnable: function () {
                easyCheckout.getAddress('shipping', $('#shipping-address-select :selected').val());
                use_billing_address = false;
                $('#billing\\:use_for_shipping').val(2);
                $('.billing').transition({
                    animation: 'fade up',
                    //duration: '2s',
                    complete: function () {
                        $('.billingOpen').transition('fade up');
                        $('.shipping').transition('fade up');
                    },
                    debug: debug,
                    performance: performance,
                    verbose: verbose
                });
                easyCheckoutDataLayer.push({'event': 'ship-to-different-address', 'eventAction': 'Enable ship to different address', 'eventLabel': 'Enable ship to different address', 'eventValue': 1});
            },
            onDisable: function () {
                easyCheckout.getAddress('billing', $('#billing-address-select :selected').val());
                use_billing_address = true;
                $('#billing\\:use_for_shipping').val(1);
                /*$('.shipping').transition({
                    animation: 'fade down',
                    //duration: '2s',
                    complete: function () {*/
                        $('.shippingOpen').transition('fade down');
                        /*$('.billing').transition('fade up');
                    },
                    debug: debug,
                    performance: performance,
                    verbose: verbose
                });*/
                      
                easyCheckoutDataLayer.push({'event': 'ship-to-different-address', 'eventAction': 'Disable ship to different address', 'eventLabel': 'Disable ship to different address', 'eventValue': 0});
            },
            debug: debug,
            performance: performance,
            verbose: verbose
        });

        // switch to billing form
        $('html').on('click', '.billingOpen', function () {
            $(this).transition('remove looping');
            $('.shipping').transition({
                animation: 'fade down',
                //duration: '2s',
                complete: function () {},
                debug: debug,
                performance: performance,
                verbose: verbose
            });
            $('.shippingOpen').transition('fade up');
            $(this).transition({
                animation: 'fade down',
                complete: function () {
                    $('.billing').transition('fade up');
                },
                debug: debug,
                performance: performance,
                verbose: verbose
            });
            /*$('.shipping').transition({
                animation: 'fade up',
                //duration: '2s',
                complete: function () {
                $('.billing').transition('fade up');
                },
                debug: debug,
                performance: performance,
                verbose: verbose
            });*/
        });
        // switch to shipping form
        $('html').on('click', '.shippingOpen', function () {
                $(this).transition('remove looping');
                $('.billing').transition({
                    animation: 'fade down',
                    //duration: '2s',
                    complete: function () {},
                    debug: debug,
                    performance: performance,
                    verbose: verbose
                });
                $('.billingOpen').transition('fade up');
                $(this).transition({
                    animation: 'fade down',
                    complete: function () {
                    $('.shipping').transition('fade up');
                },
                    debug: debug,
                    performance: performance,
                    verbose: verbose
                });
        });

        // show current payment method form
        if($('#payment-form .ui.checkbox input:checked').length > 0) {
            currentPaymentMethod = $('#payment-form .ui.checkbox input:checked').val();
            $('.payment_form_details').hide();
            $('#payment_form_' + currentPaymentMethod).closest('.payment_form_details').show();
            $('#payment_form_' + currentPaymentMethod).show();
        }
        
        // set current shipping method
        if($('#shipping-method .ui.checkbox input:checked').length > 0) {
            currentShippingMethod = $('#shipping-method .ui.checkbox input:checked').val();
        }

        // set loading text while the city input is dropdown
        // $('[id$=":city"]').parent().find('.k-input').html('Loading');


        // CITIES INTEGRATION: START
        

        // $('#placeOrderBtn').on('click', function() {
        //     var text = $('#shopgo_easy_checkout :input').serialize();
        //     console.log(text);
        //     var index = text.indexOf('billing%5Bcity_displayed_value');
        //     text = text.substr(index);
        //     text = text.split('&');
        //     console.log('------------------------------------------');
        //     console.log(text[0]);
        //     console.log(text[1]);
        //     console.log(text[14]);
        //     console.log(text[15]);
        //     console.log('------------------------------------------');
        //     console.log(autocomplete_allow_any_value);
        // });


        var citiesAPIInputSetup = function(billingOrShipping) {


            var billingOrShippingCityInput;

            switch(cityInput) {                
                case 'dropdown':
                    //create DropDownList UI component
                    billingOrShippingCityInput = $("#" + billingOrShipping + "\\:city").kendoDropDownList({
                        dataSource: [],
                        placeholder: easyCheckout.translate("Select city..."),
                        autoBind: true,
                        dataValueField: 'value',
                        dataTextField: 'text'
                    }).data("kendoDropDownList");
                break;
                case 'autocomplete':
                    autocomplete_allow_any_value_as_in_settings = autocomplete_allow_any_value;
                    //create AutoComplete UI component
                    billingOrShippingCityInput = $("#" + billingOrShipping + "\\:city").kendoAutoComplete({
                        placeholder: easyCheckout.translate("Select city..."),
                        suggest: true,
                        delay: autocomplete_request_delay,
                        dataValueField: 'value',
                        dataTextField: 'text',
                        dataSource: new kendo.data.DataSource({
                            serverFiltering: true,
                            transport: {
                                read:  {
                                    url: cities_url,
                                    dataType: "JSONP",
                                    // Triggers a timeout error if the response takes longer than this.
                                    timeout: 10000,
                                    data: {
                                        username: cities_username,
                                        limit: '10',
                                        country: function() {
                                            return $('#' + billingOrShipping + '\\:country_id :selected').val();
                                        },
                                        state: function() {
                                            var selectedCountry = $('#' + billingOrShipping + '\\:country_id :selected').val();
                                            if (selectedCountry !== 'US') return '';
                                            var selectedRegionIndex = $('#' + billingOrShipping + '\\:region_id :selected').val();
                                            return countryRegions[selectedCountry][selectedRegionIndex].code;
                                        },
                                        term: function() {
                                            last_autocomplete_queried_term = $('#' + billingOrShipping + '\\:city').data('kendoAutoComplete').value();
                                            return last_autocomplete_queried_term;
                                        },
                                        hostname: store_hostname
                                    }
                                },
                                parameterMap: function(data, type) {
                                    // Remove extra auto-generated params
                                    data['filter'] = {};
                                    return data;
                                }
                            },
                            schema: {
                                data: function(response) {
                                    return response.data;
                                },
                                model: {
                                    fields: {
                                        value: 'en',
                                        text: 'auto'
                                    }
                                }
                            },
                            requestStart: function(e) {

                                clearTimeout(special_case_timeout);


                                var selected_country = $('#' + billingOrShipping + '\\:country_id :selected').val();
                                
                                if (mena_countries.indexOf(selected_country) >= 0) {

                                    $('[id$=":city"]').closest('.field.loading').find('i').addClass('fa-circle-o-notch');

                                } else {

                                    // Cancel requests for non-MENA countries.
                                    e.preventDefault();

                                    autocomplete_input = $('#' + billingOrShipping + '\\:city').val();
                                    popup_term = autocomplete_input;
                                    $('#' + billingOrShipping + '\\:city_hidden_field').val(autocomplete_input);
                                    
                                }
                            },
                            requestEnd: function(e) {
                                $('[id$=":city"]').closest('.field.loading').find('i').removeClass('fa-circle-o-notch');

                                autocomplete_allow_any_value = autocomplete_allow_any_value_as_in_settings;
                            },
                            // This is the 'change' event for *DataSource*, that is, the data binding event.
                            change: function(e) {

                                // Check validity after data binding only if the user has left the text field before getting the latest results.
                                if (!($('#' + billingOrShipping + '\\:city').is(":focus"))) {
                                    autocomplete_input = $('#' + billingOrShipping + '\\:city').val();
                                    checkIfAutocompleteInputIsValid(autocomplete_input);
                                }
                            },
                            // The error event is triggered on timeout (check out the dataSource definition above) or any other connection error.
                            // The fallback is to allow any value so as not to stop the checkout process.
                            error: function(e) {
                                // console.log(e);
                                
                                autocomplete_allow_any_value = true;

                                autocomplete_input = $('#' + billingOrShipping + '\\:city').val();
                                checkIfAutocompleteInputIsValid(autocomplete_input);
                            }

                        }),
                        select: function(e) {
                            autocomplete_changed_by_selecting_from_list = true;
                        },
                        // This is the 'change' event for *kendoAutoComplete*
                        change: function(e) {

                            autocomplete_input = $('#' + billingOrShipping + '\\:city').val();


                            var autocomplete_matching_result = $.grep($('#' + billingOrShipping + '\\:city').data('kendoAutoComplete').dataSource.data(), function(item) {
                                return item.text.toLowerCase() === autocomplete_input.toLowerCase();
                            });

                            // If change event was triggered by selecting from the autocomplete dropdown list
                            if (autocomplete_changed_by_selecting_from_list) {

                                autocomplete_changed_by_selecting_from_list = false;

                                $('#' + billingOrShipping + '\\:city_hidden_field').val(autocomplete_matching_result[0].value);

                            // If change event was triggered by leaving the autocomplete field
                            } else {


                                


                                checkIfAutocompleteInputIsValid = function (autocomplete_input) {


                                    var selected_country = $('#' + billingOrShipping + '\\:country_id :selected').val();


                                    // When the only values allowed are those suggested by autocomplete.
                                    // This restriction is currently limited to MENA countries. For non-MENA countries, any input is accepted.
                                    if (!autocomplete_allow_any_value &&
                                        mena_countries.indexOf(selected_country) >= 0) {

                                        var autocomplete_matching_result = $.grep($('#' + billingOrShipping + '\\:city').data('kendoAutoComplete').dataSource.data(), function(item) {
                                            return item.text.toLowerCase() === autocomplete_input.toLowerCase();
                                        });

                                        // When the input is *not* one of the suggestions (regardless of the case).
                                        if (autocomplete_matching_result.length === 0) {

                                            clearAutocompleteField(billingOrShipping);


                                            // A terrible hack. Keep an eye on this.
                                            window.setTimeout(function() {
                                                clearAutocompleteField(billingOrShipping);
                                            }, 10);

                                        // When the input *is* one of the suggestions.
                                        } else {
                                            $('#' + billingOrShipping + '\\:city_hidden_field').val(autocomplete_matching_result[0].value);
                                        }


                                    // When any input value is allowed.
                                    } else {
                                        $('#' + billingOrShipping + '\\:city_hidden_field').val(autocomplete_input);
                                    }

                                }


                                // When we leave the autocomplete field after the results have been received
                                // and no changes were made to the queried term. Otherwise, the checking function
                                // will be called upon data binding after the new request ends.
                                if (autocomplete_input == last_autocomplete_queried_term) {

                                    checkIfAutocompleteInputIsValid(autocomplete_input);

                                } else {

                                }



                            }
                        },

                        open: function(e) {


                            autocomplete_input = $('#' + billingOrShipping + '\\:city').val();


                            var autocomplete_matching_result = $.grep($('#' + billingOrShipping + '\\:city').data('kendoAutoComplete').dataSource.data(), function(item) {
                                return item.text.toLowerCase() === autocomplete_input.toLowerCase();
                            });

                            var selected_country = $('#' + billingOrShipping + '\\:country_id :selected').val();


                            // This is for when a user enters parts of a name and accepts the first option which is auto-filled.
                            // The problem is that it won't trigger a select event, so it won't toggle "autocomplete_changed_by_selecting_from_list",
                            // nor will it trigger a request event to update the datasource.
                            // In other words, it's a case that falls in between cases.

                            if (mena_countries.indexOf(selected_country) >= 0) {

                                // special_case_timeout = setTimeout(function() {

                                    if (autocomplete_matching_result.length > 0 &&
                                        autocomplete_matching_result[0].value ==
                                        $('#' + billingOrShipping + '\\:city').data('kendoAutoComplete').dataSource.data()[0].value) {

                                        $('#' + billingOrShipping + '\\:city_hidden_field').val(autocomplete_matching_result[0].value);


                                    } else {
                                        // clearAutocompleteField(billingOrShipping);
                                    }

                                    
                                // }, autocomplete_request_delay + 50);

                            }

                        }

                    }).data("kendoAutoComplete");
                    
                    // For formatting purposes
                    $("#" + billingOrShipping + "\\:city").removeClass('k-input');

                    // The 'name' attribute is replaced with 'billing[city]' so that no additional changes to the backend are needed.
                    $("#" + billingOrShipping + "\\:city").attr('name', billingOrShipping + '[city_displayed_value]');
                    $("#" + billingOrShipping + "\\:city_hidden_field").attr('name', billingOrShipping + '[city]');

                break;
            }

            if (billingOrShipping === 'billing') {
                billingCityInput = billingOrShippingCityInput;
            } else if (billingOrShipping === 'shipping') {
                shippingCityInput = billingOrShippingCityInput;  
            }


            clearAutocompleteField = function(billingOrShipping) {


                $('#' + billingOrShipping + '\\:city').attr('placeholder', easyCheckout.translate('Select a valid city name...'));
                $('#' + billingOrShipping + '\\:city').val('');
                $('#' + billingOrShipping + '\\:city_hidden_field').val('');


                

                if (billingOrShipping == 'billing') {
                    easyCheckout.validateBillingForm();
                } else if (billingOrShipping == 'shipping') {
                    easyCheckout.validateShippingForm();
                }

            }

            
        
        };
        // CITIES INTEGRATION: END




        // CITIES INTEGRATION: START

        if (citiesDataSource === 'cities_api') {

            citiesAPIInputSetup('billing');
                        
        } else {

        // CITIES INTEGRATION: END

            switch(cityInput) {
                case 'dropdown':
                    //create DropDownList UI component
                    billingCityInput = $("#billing\\:city").kendoDropDownList({
                        dataSource: [],
                        placeholder: easyCheckout.translate("Select city..."),
                        // optionLabel: " -- Select -- ",
                        autoBind: true
                    }).data("kendoDropDownList");
                break;
                case 'autocomplete':
                    //create AutoComplete UI component
                    billingCityInput = $("#billing\\:city").kendoAutoComplete({
                        dataSource: [],
                        filter: "startswith",
                        placeholder: easyCheckout.translate("Select city..."),
                        //            separator: ", ",
                        suggest: true,
                    }).data("kendoAutoComplete");
                break;
    
            }

            //**********Allow any city other autocomplete suggestions //Billing--Shipping
            $('html').on('change', '#billing\\:city', function () {
                 switch(cityInput) {
                    case 'dropdown':
                    break;
                    case 'autocomplete':
                        var cty = $(this).val();
                        //var CitiesLowerCase=$.map(billingCityInput.dataSource._data, String.toLowerCase);
                        var CitiesLowerCase = billingCityInput.dataSource.data().map(function(item) { return item.toLowerCase() });
                        if ((!autocomplete_allow_any_value) && (CitiesLowerCase.indexOf(cty.toLowerCase())<0))
                        {
                            $('#billing\\:city').attr("placeholder", "Select City...").val("");   
                        }
                    break;
                }            
            });

        // CITIES INTEGRATION: START
        }
        // CITIES INTEGRATION: END



        // CITIES INTEGRATION: START
        if (citiesDataSource !== 'cities_api') {
        // CITIES INTEGRATION: END

            $('html').on('keyup', '[id^="billing\\:"],#aw-orderattributes-checkoutonepage-attributes-container-billing-address [id^="aw_oa"]', function () {
                easyCheckout.delay(function(){
                    if(
                        easyCheckout.validateBillingForm()
                    )
                    easyCheckout.saveBilling();
                }, delayInterval );
            });

        // CITIES INTEGRATION: START
        } else {

            $('html').on('change', '[id^="billing\\:"],#aw-orderattributes-checkoutonepage-attributes-container-billing-address [id^="aw_oa"]', function () {
                easyCheckout.delay(function(){
                    if(
                        easyCheckout.validateBillingForm()
                    )
                    easyCheckout.saveBilling();
                }, delayInterval );
            });

        }
        // CITIES INTEGRATION: END


        
        $('html').on('change', '[id^=billing\\:]select,#aw-orderattributes-checkoutonepage-attributes-container-billing-address [id^="aw_oa"]select', function () {
            easyCheckout.delay(function(){
                if(
                    easyCheckout.validateBillingForm()
                )
                easyCheckout.saveBilling();
            }, delayInterval );
        });

        $('html').on('change', '#billing\\:country_id', function () {
            var countryCode = $(this).val();
            easyCheckout.updateRegion('billing', countryCode);
            easyCheckout.updateCitiesDataSource(countryCode, billingCityInput, $('#billing\\:region_id').val());
            $('#billing\\:city').val(null);
            if($.inArray(countryCode, noPostcodeCountries) != -1) {
                $('#billing\\:postcode').closest('.field').hide();
            } else {
                $('#billing\\:postcode').closest('.field').show();
            }
            //Hide State For Mena 
             if ($.inArray(countryCode, mena_countries) !== -1){
                $('#billing\\:region').closest('.field').hide();         
            }
        });

        $('html').on('change', '#billing\\:region_id', function () {
            var regionCode = $(this).val();
            easyCheckout.updateCitiesDataSource($('#billing\\:country_id').val(), billingCityInput, regionCode);
            $('#billing\\:city').val(null);
        });

        
        // CITIES INTEGRATION: START

        if (citiesDataSource === 'cities_api') {

            citiesAPIInputSetup('shipping');
                        
        } else {

        // CITIES INTEGRATION: END

            switch(cityInput) {
                case 'dropdown':
                    //create DropDownList UI component
                    shippingCityInput = $("#shipping\\:city").kendoDropDownList({
                        dataSource: [],
                        placeholder: easyCheckout.translate("Select city..."),
                        autoBind: true,
                    }).data("kendoDropDownList");
                break;
                case 'autocomplete':
                    //create AutoComplete UI component
                    shippingCityInput = $("#shipping\\:city").kendoAutoComplete({
                        dataSource: [],
                        filter: "startswith",
                        placeholder: easyCheckout.translate("Select city..."),
                        //            separator: ", ",
                        suggest: true,
                    }).data("kendoAutoComplete");
                break;
            }


            //**********Allow any city other autocomplete suggestions //Billing--Shipping
            $('html').on('change', '#shipping\\:city', function () {
                 switch(cityInput) {
                    case 'dropdown':
                    break;
                    case 'autocomplete':
                        var cty = $(this).val();
                        //var CitiesLowerCase=$.map(shippingCityInput.dataSource._data, String.toLowerCase);
                        var CitiesLowerCase = shippingCityInput.dataSource.data().map(function(item) { return item.toLowerCase() });
                        if ((!autocomplete_allow_any_value) && (CitiesLowerCase.indexOf(cty.toLowerCase())<0))
                        {
                            $('#shipping\\:city').attr("placeholder", "Select City...").val("");
                        }
                    break;
                }            
            });

        // CITIES INTEGRATION: START
        }
        // CITIES INTEGRATION: END
        
        

        // CITIES INTEGRATION: START
        if (citiesDataSource !== 'cities_api') {
        // CITIES INTEGRATION: END

            $('html').on('keyup', '[id^="shipping\\:"],#aw-orderattributes-checkoutonepage-attributes-container-shipping-address [id^="aw_oa"]', function () {
                easyCheckout.delay(function(){
                    if(
                        easyCheckout.validateShippingForm()
                    )
                    easyCheckout.saveShipping();
                }, delayInterval );
            });

        // CITIES INTEGRATION: START
        } else {

            $('html').on('change', '[id^="shipping\\:"],#aw-orderattributes-checkoutonepage-attributes-container-shipping-address [id^="aw_oa"]', function () {
                easyCheckout.delay(function(){
                    if(
                        easyCheckout.validateShippingForm()
                    )
                    easyCheckout.saveShipping();
                }, delayInterval );
            });

        }
        // CITIES INTEGRATION: END
        
        $('html').on('change', '[id^=shipping\\:]select, #aw-orderattributes-checkoutonepage-attributes-container-shipping-address [id^="aw_oa"]select', function () {
            easyCheckout.delay(function(){
                if(
                    easyCheckout.validateShippingForm()
                )
                easyCheckout.saveShipping();
            }, delayInterval );
        });

        $('html').on('change', '#shipping\\:country_id', function () {
            var countryCode = $(this).val();
            easyCheckout.updateRegion('shipping', countryCode);
            easyCheckout.updateCitiesDataSource(countryCode, shippingCityInput, $('#shipping\\:region_id').val());
            $('#shipping\\:city').val(null);
            if($.inArray(countryCode, noPostcodeCountries) != -1) {
                $('#shipping\\:postcode').closest('.field').hide();
            } else {
                $('#shipping\\:postcode').closest('.field').show();
            }
            //Hide State For Mena 
            if ($.inArray(countryCode, mena_countries) !== -1){
                $('#shipping\\:region').closest('.field').hide();         
            }
        });

        $('html').on('change', '#shipping\\:region_id', function () {
            var regionCode = $(this).val();
            easyCheckout.updateCitiesDataSource($('#shipping\\:country_id').val(), shippingCityInput, regionCode);
            $('#shipping\\:city').val(null);
        });

        $('html').on('change keyup', '#payment-form :input:not(:radio), #aw-orderattributes-checkoutonepage-attributes-container-payment-method', function () {
            easyCheckout.delay(function(){
                if(
                    easyCheckout.validatePaymentForm()
                )
                easyCheckout.savePayment();
            }, delayInterval );
        });
        
        // CITIES INTEGRATION: START
        if (citiesDataSource !== 'cities_api') {
        // CITIES INTEGRATION: END

            $('html').on('keyup', '#aw-orderattributes-checkoutonepage-attributes-container-shipping-method', function () {
                easyCheckout.delay(function(){
                    if(
                        easyCheckout.validateShippingMethodForm()
                    )
                    easyCheckout.saveShippingMethod();
                }, delayInterval );
            });

        // CITIES INTEGRATION: START
        }
        // CITIES INTEGRATION: END

        $('html').on('change', '#aw-orderattributes-checkoutonepage-attributes-container-shipping-method', function () {
            easyCheckout.delay(function(){
                if(
                    easyCheckout.validateShippingMethodForm()
                )
                easyCheckout.saveShippingMethod();
            }, delayInterval );
        });
        
        $('html').on('click', '#applyCoupon', function () {
            if (
                !$('.form.review')
                .form(
                    {
                        coupon_code: {
                        identifier  : 'coupon_code',
                        rules: [
                            {
                              type   : 'empty',
                              prompt : easyCheckout.translate('Please enter your coupon code')
                            }
                          ]
                        }
                    })
                .form('validate form')
            ) return false;
            $('.payment').dimmer({closable: false}).dimmer('show');
            $('.review').dimmer({closable: false}).dimmer('show');
            easyCheckout.hideMessage();
            var request = $.ajax({
                url: baseUrl + 'easycheckout/index/coupon',
                type: "POST",
                data: {
                    coupon_code: $('#coupon_code').val(),
                    remove: false
                },
                dataType: "JSON"
            });
            request.done(function (data) {
                $('.payment').dimmer('hide');
                $('.review').dimmer('hide');
                if(data.error) {
                    easyCheckout.showMessage(easyCheckout.translate(data.message));
                    easyCheckoutDataLayer.push({'event': 'apply-coupon', 'eventAction': 'Apply coupon error', 'eventLabel': 'Apply coupon error message: ' + easyCheckout.translate(data.message), 'eventValue': 0});
                } else {
                    easyCheckout.reloadPayment();
                    easyCheckout.reloadReview();
                    easyCheckoutDataLayer.push({'event': 'apply-coupon', 'eventAction': 'Apply coupon success', 'eventLabel': 'Apply coupon success', 'eventValue': 1});
                }
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
        });

        //Checkout agreements checkboxes
        $('.checkout-agreements .ui.checkbox').checkbox({
            onChange: function () {

            },
            onEnable: function () {

            },
            onDisable: function () {

            },
            debug: debug,
            performance: performance,
            verbose: verbose
        });

        //Checkout agreements modal
        $('html').on('click', '.checkout-agreements a', function() {
            modalId = $(this).attr('data-agreement-modal-id');
            $('#agreement-' + modalId).modal('show');
            return false;
        });

        //set maxlength on input fields
        $('input[type="text"]').attr({maxLength: inputMaxLength});

        //Custom responsive fix Tareq Hassan;
        var main = $('.three.column.page.grid');
        var target = main.find('.column:nth-child(2)');
        var width = $(window).width();

        if (width <= 1200 && width >= 760) {
            main.removeClass('three').addClass('two');
            target.append($('.column:nth-child(3)').not('.stackable'));
            $('.column:nth-child(3)').remove;
        } else {
            main.removeClass('two').addClass('three');
            $('.column .review').parent().appendTo(main);
        }

        $(window).resize(function () {
            var width = $(window).width();

            if (width <= 1200 && width >= 760) {
                main.removeClass('three').addClass('two');
                target.append($('.column:nth-child(3)').not('.stackable'));
                $('.column:nth-child(3)').remove;
            } else {
                main.removeClass('two').addClass('three');
                $('.column .review').parent().appendTo(main);
            }
        });
        
        // Load payment methods on page load.
        //easyCheckout.reloadPayment();
        // Load review section on page load.
        easyCheckout.reloadReview();

        $('.ec-select').kendoDropDownList({});

        $('.datetime-picker').kendoDatePicker({
            value: new Date(),
            min: new Date()
        });

        $(".ec-multi-select").kendoMultiSelect().data("kendoMultiSelect");

        // Handle country region on page load.
        easyCheckout.updateRegion('billing', $('#billing\\:country_id :selected').val());
        easyCheckout.updateRegion('shipping', $('#shipping\\:country_id :selected').val());
        
        //Hide State filed to Mena Country on page load
        if ($.inArray($('#billing\\:country_id :selected').val(), mena_countries) !== -1)
            {$('#billing\\:region').closest('.field').hide();}
        if ($.inArray($('#shipping\\:country_id :selected').val(), mena_countries) !== -1)
            {$('#shipping\\:region').closest('.field').hide();}

        // Handle the zip code field on page load.
        if($.inArray($('#billing\\:country_id').val(), noPostcodeCountries) != -1) {$('#billing\\:postcode').closest('.field').hide();}
        if($.inArray($('#shipping\\:country_id').val(), noPostcodeCountries) != -1) {$('#shipping\\:postcode').closest('.field').hide();}
        
        // Load default country cities on page load.
        easyCheckout.updateCitiesDataSource($('#billing\\:country_id :selected').val(), billingCityInput, $('#billing\\:region_id').val());
        easyCheckout.updateCitiesDataSource($('#shipping\\:country_id :selected').val(), shippingCityInput, $('#shipping\\:region_id').val());
        
        // Load saved Addresses
        if($('#billing-address-select').length != 0) {
            easyCheckout.getAddress('billing', $('#billing-address-select :selected').val());
        }

        // if($('#shipping-address-select').length != 0) {
        //  easyCheckout.getAddress('shipping', $('#shipping-address-select :selected').val());
        // }

        // Save payment
        var paymentMethodCode = $('#payment-form .ui.checkbox input:checked').val();
        easyCheckout.savePayment(paymentMethodCode);

        // Login form
        $('#loginForm').submit(function(event) {
            event.preventDefault();
            if(!easyCheckout.validateLoginForm()) return false;
            $('#loginForm').removeClass('error');
            $('#loginForm').dimmer({closable: false}).dimmer('show');
            var formData = $('#loginForm').serialize();
            easyCheckout.hideMessage();
            var request = $.ajax({
                url: baseUrl + "easycheckout/index/loginPost",
                type: "POST",
                data: formData,
                dataType: "JSON"
            });
            request.done(function (data) {
                if (data.redirect) {
                    document.location.reload();
                } else {
                    $('#loginForm').addClass('error');
                    $('#loginForm').dimmer('hide');
                    $('#loginForm .error.message').html($('#loginForm .error.message .icon')).append(data.message);
                }
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
            return false;
        });

        // Show Aramex city lookup form popup.
        $('.aramexCityLookup').click(function(event) {
            event.preventDefault();
            window.open("http://www.aramex.com/CityLookup.aspx",null,"height=300,width=400,status=no,toolbar=no,menubar=no,location=no");
        });
        
        // Adding new validation rules here.
        // Validate number only.
        $.fn.form.settings.rules.number = function (a){var b=new RegExp("^\\d*$");return b.test(a)};

        console.log('%c ShopGo EasyCheckout :)', 'color:#AAA; font-size:20px;');
        console.log('%c Like playing around in the console? Why not help us build ShopGo? - http://shopgo.me', 'color:#AAA;');

    });
    
})(jQuery);
