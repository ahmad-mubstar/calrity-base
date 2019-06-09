(function ($) {

// ****** Convert magento classes to semantic-ui validation Rules ****** //

    $.fn.form.settings.rules.checkDate = function(value) {
        var ccExpMonth   = value;
        var ccExpYear    = document.getElementById("creditcardpci_expiration_yr").value;
        var currentTime  = new Date();
        var currentMonth = currentTime.getMonth() + 1;
        var currentYear  = currentTime.getFullYear();
        if (ccExpMonth < currentMonth && ccExpYear == currentYear) {
            return false;
        }
        return true;
    };

    $.fn.form.settings.rules.checkDateSaved = function(value) {
        var ccExpMonth   = value;
        var ccExpYear    = document.getElementById("ccsave_expiration_yr").value;
        var currentTime  = new Date();
        var currentMonth = currentTime.getMonth() + 1;
        var currentYear  = currentTime.getFullYear();
        if (ccExpMonth < currentMonth && ccExpYear == currentYear) {
            return false;
        }
        return true;
    };

    $.fn.form.settings.rules.checkCreditCardNumberValid = function(value) {
        // remove non-numerics
        var v = "0123456789";
        var w = "";
        for (i=0; i < value.length; i++) {
            x = value.charAt(i);
            if (v.indexOf(x,0) != -1)
            w += x;
        }
        // validate number
        j = w.length / 2;
        k = Math.floor(j);
        m = Math.ceil(j) - k;
        c = 0;
        for (i=0; i<k; i++) {
            a = w.charAt(i*2+m) * 2;
            c += a > 9 ? Math.floor(a/10 + a%10) : a;
        }
        for (i=0; i<k+m; i++) c += w.charAt(i*2+1-m) * 1;
        return (c%10 == 0);
    };


    $.fn.form.settings.rules.checkCreditCardNumber = function(value) {
        // remove non-numerics
        value = removeDelimiters(value);
        var CardType = document.getElementById("creditcardpci_cc_type").value;
        var CardTypeCheck = CardTypeValid(value);
        if (CardType == CardTypeCheck) {
            return true;
        }
        return false;
    };

    function removeDelimiters (v) {
        v = v.replace(/\s/g, '');
        v = v.replace(/\-/g, '');
        return v;
    }

    function CardTypeValid(number) {
        // visa
        var re = new RegExp("^4[0-9]{12}([0-9]{3})?$");
        if (number.match(re) != null)
            return "VI";

        // Mastercard
        re = new RegExp("^5[1-5][0-9]{14}$");
        if (number.match(re) != null)
            return "MC";

        // AMEX
        re = new RegExp("^(6334[5-9]([0-9]{11}|[0-9]{13,14}))|(6767([0-9]{12}|[0-9]{14,15}))$");
        if (number.match(re) != null)
            return "SO";

        // Discover
        re = new RegExp("(^(5[0678])[0-9]{11,18}$)|(^(6[^05])[0-9]{11,18}$)|(^(601)[^1][0-9]{9,16}$)|(^(6011)[0-9]{9,11}$)|(^(6011)[0-9]{13,16}$)|(^(65)[0-9]{11,13}$)|(^(65)[0-9]{15,18}$)|(^(49030)[2-9]([0-9]{10}$|[0-9]{12,13}$))|(^(49033)[5-9]([0-9]{10}$|[0-9]{12,13}$))|(^(49110)[1-2]([0-9]{10}$|[0-9]{12,13}$))|(^(49117)[4-9]([0-9]{10}$|[0-9]{12,13}$))|(^(49118)[0-2]([0-9]{10}$|[0-9]{12,13}$))|(^(4936)([0-9]{12}$|[0-9]{14,15}$))");
        if (number.match(re) != null)
            return "SM";

        // Diners
        re = new RegExp("^3[47][0-9]{13}$");
        if (number.match(re) != null)
            return "AE";

        // Diners - Carte Blanche
        re = new RegExp("^6011[0-9]{12}$");
        if (number.match(re) != null)
            return "DI";

        // JCB
        re = new RegExp("^(3[0-9]{15}|(2131|1800)[0-9]{11})$");
        if (number.match(re) != null)
            return "JCB";

        // Visa Electron
        re = new RegExp("^([0-9]{3}|[0-9]{4})?$");
        if (number.match(re) != null)
            return "OT";

        return "";
    }


    $.fn.form.settings.rules.checkCvvCode = function(value) {

        var result = validateCvvCode();
        if (result)
            return true;
        else
            return false;
    };

    function validateCvvCode() {

        //Get the text of the selected card type
        var cardType = document.getElementById('creditcardpci_cc_type').value;
        // Get the value of the CVV code
        var cvvCode = document.getElementById('creditcardpci_cc_cid').value;

        var digits = 0;
        switch (cardType.toUpperCase()) {
            case 'VI':
            case 'MC':
            case 'DI':
                digits = 3;
                break;
            case 'SO':
            case 'SM':
            case 'AE':
                digits = 4;
                break;
            case 'JCB':
                digits = 3,4;
                break;    
            default:
                return false;
        }

        var regExp = new RegExp('^[0-9]{' + digits + '}$');
        return (cvvCode.length == digits && regExp.test(cvvCode))
    };


// ****** End of Convert magento classes to semantic-ui validation Rules ****** //


    var EasyCheckout = function () {
        //
    };
    EasyCheckout.prototype = {
        init: function () {

            //Disable placeOrderBtn by default
            $('#placeOrderBtn').prop('disabled', true);
            $('#placeOrderBtn').addClass('disabled');
            
            // initialize shipping methods radio button
            $('#shipping-method .ui.checkbox').checkbox({
                onChange: function () {
                    EasyCheckout.prototype.saveShippingMethod($(this).val());
                },
                debug: debug,
                performance: performance,
                verbose: verbose
            });

            // initialize payment methods radio button
            $('#payment-form .ui.checkbox').checkbox({
                onChange: function () {
                    var paymentMethodCode = $('#payment-form .ui.checkbox input:checked').val();
                    $('.payment_form_details').hide();
                    $('#payment_form_' + paymentMethodCode).closest('.payment_form_details').show();
                    $('#payment_form_' + paymentMethodCode).show();
                    EasyCheckout.prototype.savePayment(paymentMethodCode);
                },
                debug: debug,
                performance: performance,
                verbose: verbose
            });

            $('.selection.dropdown').dropdown({
                onChange: function (value, text) {
                    $(this).find('input').val(value);
                    $(this).find('.text').html(text);
                },
                debug: debug,
                performance: performance,
                verbose: verbose
            });

            $('#billing-address-select').kendoDropDownList({
                change: function(e) {
                    var value = this.value();
                    if(!value) {
                        currentBillingAddressId = '';
                        $('.billingNewAddressForm').show();
                        return;
                    } else {
                        currentBillingAddressId = value;
                        $('.billingNewAddressForm').hide();
                    }
                    EasyCheckout.prototype.getAddress('billing', value);
                }
            });

            $('#shipping-address-select').kendoDropDownList({
                change: function(e) {
                    var value = this.value();
                    if(!value) {
                        currentShippingAddressId = '';
                        $('.shippingNewAddressForm').show();
                        return;
                    } else {
                        currentBillingAddressId = value;
                        $('.shippingNewAddressForm').hide();
                    }
                    EasyCheckout.prototype.getAddress('shipping', value);
                }
            });

            // spread the use of popup
            // quick fix for the popup in order not to stuck.
            $('.ui.popup').remove();
            $('.usePopup').popup();

            // show current payment method form
            if($('#payment-form .ui.checkbox input:checked').length > 0) {
                currentPaymentMethod = $('#payment-form .ui.checkbox input:checked').val();
                if(!$('#payment_form_' + currentPaymentMethod).is(':visible')) {
                    $('.payment_form_details').hide();
                    $('#payment_form_' + currentPaymentMethod).closest('.payment_form_details').show();
                    $('#payment_form_' + currentPaymentMethod).show();
                }
                $('#placeOrderBtn').prop('disabled', false);
                $('#placeOrderBtn').removeClass('disabled');
            }
        },
        // save billing function
        saveBilling: function (ignoreValidation) {
            if (ignoreValidation === undefined) {
                if (!EasyCheckout.prototype.validateBillingForm()) {
                    return false;
                }
            } else {
                //
            }
            $('.form.billing .field').removeClass('error');
            if (!isVirtual) {
                $('.shipping-method').dimmer({closable: false}).dimmer('show');
            } else {
                $('.form.payment').dimmer({closable: false}).dimmer('show');
            }
            EasyCheckout.prototype.hideMessage();
            var request = $.ajax({
                url: baseUrl + "easycheckout/index/saveBilling",
                type: "POST",
                data: $('#shopgo_easy_checkout :input').serialize(),
                dataType: "JSON"
            });
            request.done(function (data) {
                if (!isVirtual) {
                    $('.shipping-method').dimmer('hide');
                } else {
                    $('.form.payment').dimmer('hide');
                }
                if (data.error) {
                    EasyCheckout.prototype.showMessage(data.message);
                } else {
                    isBillingSaved = true;
                    if (!isVirtual) {
                        $('.shipping-error-message').remove();
                        $('.sp-methods').remove();
                        $('.shipping-method .ui.header').after(data.update_section.html);
                        currentShippingMethod = $('#shipping-method .ui.checkbox input:checked').val();
                        EasyCheckout.prototype.saveShippingMethod();
                    }
                    if (data.goto_section == 'payment') {
                        $('#payment-form').remove();
                        $('.form.payment .ui.header').after(data.update_section.html);
                    }
                }
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
            request.always(function () {
                EasyCheckout.prototype.init();
            });
        },
        // save shipping function
        saveShipping: function (ignoreValidation) {
            if (ignoreValidation === undefined) {
                if (!EasyCheckout.prototype.validateShippingForm()) {
                    return false;
                }
            } else {
                //
            }
            if (!EasyCheckout.prototype.validateBillingForm()) {
                return false;
            }
            $('.form.shipping .field').removeClass('error');
            $('.shipping-method').dimmer({closable: false}).dimmer('show');
            EasyCheckout.prototype.hideMessage();
            var request = $.ajax({
                url: baseUrl + "easycheckout/index/saveShipping",
                type: "POST",
                data: $('#shopgo_easy_checkout :input').serialize(),
                dataType: "JSON"
            });
            request.done(function (data) {
                $('.shipping-method').dimmer('hide');
                if (data.error) {
                    EasyCheckout.prototype.showMessage(data.message);
                } else {
                    $('.shipping-error-message').remove();
                    $('.sp-methods').remove();
                    $('.shipping-method .ui.header').after(data.shippingMethod);
                    currentShippingMethod = $('#shipping-method .ui.checkbox input:checked').val();
                    EasyCheckout.prototype.saveShippingMethod();
                }
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
            request.always(function () {
                EasyCheckout.prototype.init();
            });
        },
        // save saveShippingMethod function
        saveShippingMethod: function (shippingMethodCode) {
            if (shippingMethodCode == undefined) {
                var shippingMethodCode = currentShippingMethod;
            } else {
                currentShippingMethod = shippingMethodCode;
            }
            if(!shippingMethodCode) return;
            if(!EasyCheckout.prototype.validateShippingMethodForm()) return false;
            $('.review').dimmer({closable: false}).dimmer('show');
            $('.form.payment').dimmer({closable: false}).dimmer('show');
            easyCheckoutDataLayer.push({'event': 'save-shipping-method', 'eventAction': 'Shipping method selected', 'eventLabel': 'Shipping method selected:' + shippingMethodCode, 'eventValue': 1});
            var shippingMethodData = {
                shipping_method: shippingMethodCode
            };
            EasyCheckout.prototype.hideMessage();
            var request = $.ajax({
                url: baseUrl + "easycheckout/index/saveShippingMethod",
                type: "POST",
                data: $('#shopgo_easy_checkout :input').serialize(),
                dataType: "JSON"
            });
            request.done(function (data) {
                $('.payment-error-message').remove();
                $('.review').dimmer('hide');
                $('.form.payment').dimmer('hide');
                if (data.error) {
                    EasyCheckout.prototype.showMessage(data.message);
                } else {
                    EasyCheckout.prototype.savePayment();
                    $('#payment-form').remove();
                    $('.form.payment .ui.header').after(data.payment);
                    $('#checkout-review-table-wrapper').remove();
                    $('.review .reviewTop').after(data.review);
                }
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
            request.always(function () {
                EasyCheckout.prototype.init();
            });
        },
        // save payment method
        savePayment: function (paymentMethodCode) {
            // reset paymentRedirectURL
            paymentRedirectURL = null;
            if (paymentMethodCode == undefined) {
                paymentMethodCode = currentPaymentMethod;
            } else {
                currentPaymentMethod = paymentMethodCode;
            }
            if(!paymentMethodCode) return;
            easyCheckoutDataLayer.push({'event': 'save-payment-method', 'eventAction': 'Payment menthod selected', 'eventLabel': 'Payment menthod selected: ' + paymentMethodCode, 'eventValue': '1'});
            if($('#payment_form_' + paymentMethodCode + ' :input').filter(function() {return $(this).val() == "";}).length != 0) return false;
            if(!EasyCheckout.prototype.validatePaymentForm() || !EasyCheckout.prototype.validatePaymentMethodForm()) return false;
            $('.review').dimmer({closable: false}).dimmer('show');
            var paymentMethodData = {
                method: paymentMethodCode
            };
            EasyCheckout.prototype.hideMessage();
            $('#placeOrderBtn').prop('disabled', true);
            $('#placeOrderBtn').addClass('disabled');
            var request = $.ajax({
                url: baseUrl + "easycheckout/index/savePayment",
                type: "POST",
                data: encodeURIComponent('payment[method]') + '=' + paymentMethodCode + '&' + $('#payment_form_' + paymentMethodCode + ':visible :input').serialize() + '&' + $('#aw-orderattributes-checkoutonepage-attributes-container-payment-method :input').serialize(),
                dataType: "JSON"
            });
            request.done(function (data) {
                $('#placeOrderBtn').prop('disabled', false);
                $('#placeOrderBtn').removeClass('disabled');
                $('.review').dimmer('hide');
                if (data.error) {
                    EasyCheckout.prototype.showMessage(data.message);
                    easyCheckoutDataLayer.push({'event': 'save-payment-method', 'eventAction': 'Payment menthod save error', 'eventLabel': 'Payment menthod save error message: ' + data.message, 'eventValue': '1'});
                    return false;
                } else {
                    if (data.update_section) {
                        $('#checkout-review-table-wrapper').remove();
                        $('.review .reviewTop').after(data.update_section.html);
                    }
                    if (data.redirect) {
                        paymentRedirectURL = data.redirect;
                    }
                }
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
                EasyCheckout.prototype.setLocation(baseUrl + 'checkout/cart');
                return false;
            });
            request.always(function () {
                EasyCheckout.prototype.init();
            });
        },
        // save order function
        saveOrder: function (callback) {
            easyCheckoutDataLayer.push({'event': 'place-order', 'eventAction': 'Save order button click', 'eventLabel': 'Save order button click', 'eventValue': '1'});
            if (!EasyCheckout.prototype.validateBillingForm()) {
                if (!$('.billing.form').is(':visible')) {
                    $('.shipping').transition({
                        animation: 'fade down',
                        //duration: '2s',
                        complete: function () {},
                        debug: debug,
                        performance: performance,
                        verbose: verbose
                    });
                    $('.shippingOpen').transition({
                           animation: 'fade down',
                           //duration: '2s',
                           complete: function () {},
                           debug: debug,
                           performance: performance,
                           verbose: verbose
                    });
                    $('.billingOpen').transition({
                        animation: 'fade down',
                        complete: function () {
                            $('.billing').transition('fade up');
                        },
                        debug: debug,
                        performance: performance,
                        verbose: verbose
                    });
                }
                return false;
            }
            if (use_billing_address) {
                if (!EasyCheckout.prototype.validateBillingForm()) {
                    return false;
                }
            } else {
                if (!EasyCheckout.prototype.validateShippingForm()) {
                    if (!$('.shipping.form').is(':visible')) {
                        $('.billing').transition({
                            animation: 'fade up',
                            //duration: '2s',
                            complete: function () {
                                $('.billingOpen').transition('fade up');
                                $('.shipping').transition('fade up');
                                $('.shippingOpen').transition('fade down');
                            },
                            debug: debug,
                            performance: performance,
                            verbose: verbose
                        });
                    }
                    return false;
                }
            }
            if (!isBillingSaved) {
                EasyCheckout.prototype.saveBilling();
                return false;
            }
            if (!EasyCheckout.prototype.validateShippingMethodForm()) return false;
            if (!EasyCheckout.prototype.validatePaymentMethodForm()) return false;
            if (!EasyCheckout.prototype.validatePaymentForm()) return false;
            if (!EasyCheckout.prototype.validateOrderReviewForm()) return false;
            easyCheckoutDataLayer.push({'event': 'place-order', 'eventAction': 'Save order validation passed', 'eventLabel': 'Save order validation passed', 'eventValue': '1'});
            EasyCheckout.prototype.placeOrder(function(data) {
                callback(data);
            });
        },
        // place order function
        placeOrder: function (callback) {
            $('.review').dimmer({closable: false}).dimmer('show');
            // disable the button
            $('#placeOrderBtn').prop('disabled', true);
            $('#placeOrderBtn').addClass('disabled');
            EasyCheckout.prototype.hideMessage();

            var request = $.ajax({
                url: baseUrl + "easycheckout/index/saveOrder",
                type: "POST",
                data: $('#shopgo_easy_checkout :input:not("#payment-form :input")').serialize() + '&' + encodeURIComponent('payment[method]') + '=' + currentPaymentMethod + '&' + $('#payment_form_' + currentPaymentMethod + ':visible :input').serialize(),
                dataType: "JSON"
            });
            request.done(function (data) {
                $('.review').dimmer('hide');
                // enable the button
                $('#placeOrderBtn').prop('disabled', false);
                $('#placeOrderBtn').removeClass('disabled');
                if (data.success) {
                    if (data.redirect) {
                        $('#pageDimmer .redirectDimmer').show();
                        $('#pageDimmer').dimmer({closable: false}).dimmer('show');
                        $('.ui.page.dimmer').unbind('click');
                        setTimeout(function () {
                            window.location.href = data.redirect;
                        }, 3000);
                    } else {
                        $('#pageDimmer .successDimmer').show();
                        $('#pageDimmer').dimmer({closable: false}).dimmer('show');
                        $('.ui.page.dimmer').unbind('click');
                        setTimeout(function () {
                            window.location.href = baseUrl + 'checkout/onepage/success';
                        }, 3000);
                    }
                    easyCheckoutDataLayer.push({'event': 'place-order', 'eventAction': 'Place order success', 'eventLabel': 'Place order success', 'eventValue': '1'});
                } else {
                    if (data.error === -1) {
                        EasyCheckout.prototype.saveBilling();
                        return false;
                    }
                    EasyCheckout.prototype.showMessage(EasyCheckout.prototype.translate(data.message));
                    easyCheckoutDataLayer.push({'event': 'place-order', 'eventAction': 'Place order failed', 'eventLabel': 'Place order failed message: ' + EasyCheckout.prototype.translate(data.message), 'eventValue': '1'});
                }
                callback(data);
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
                callback(null);
            });
            return;
        },
        // reload payment
        reloadPayment: function() {
            EasyCheckout.prototype.hideMessage();
            var request = $.ajax({
                url: baseUrl + "easycheckout/index/reloadPayment",
                type: "POST",
                dataType: "JSON"
            });
            request.done(function (data) {
                if(data.error) {
                    EasyCheckout.prototype.showMessage(EasyCheckout.prototype.translate(data.message));
                } else {
                    $('#payment-form').remove();
                    $('.form.payment .ui.header').after(data.payment);
                    EasyCheckout.prototype.init();
                }
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
        },
        // reload review
        reloadReview: function() {
            EasyCheckout.prototype.hideMessage();
            var request = $.ajax({
                url: baseUrl + "easycheckout/index/reloadReview",
                type: "POST",
                dataType: "JSON"
            });
            request.done(function (data) {
                if(data.error) {
                    EasyCheckout.prototype.showMessage(EasyCheckout.prototype.translate(data.message));
                } else {
                    $('#checkout-review-table-wrapper').remove();
                    $('.review .reviewTop').after(data.review);
                }
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
        },
        // update Qty function
        updateQty: function () {
            if (!EasyCheckout.prototype.validateQtyUpdateForm()) {
                return;
            }
            $('.review').dimmer({closable: false}).dimmer('show');
            cartData = new Object();
            $('#checkout-review-table-wrapper .qty').each(function (index, value) {
                cartData[$(value).attr('name')] = $(value).val();
            });
            EasyCheckout.prototype.hideMessage();
            var request = $.ajax({
                url: baseUrl + "easycheckout/index/updateQty",
                type: "POST",
                data: cartData,
                dataType: "JSON"
            });
            request.done(function (data) {
                if (data.totalQty) {
                    EasyCheckout.prototype.savePayment();
                }
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
        },
        // updateRegion function
        updateRegion: function (addressType, countryCode) {
            $('#' + addressType + '\\:region').closest('.field').show();
            $('#' + addressType + '\\:region_id').closest('.field').hide();
            $('#' + addressType + '\\:region_id').val(' ');
            $.each(countryRegions, function (ccode, regions) {
                if (countryCode === ccode) {
                    $('#' + addressType + '\\:region_id').empty();
                    $.each(regions, function (index, region) {
                        $('#' + addressType + '\\:region_id').append('<option value="' + index + '">' + region.name + '</option>');
                    });
                    $('#' + addressType + '\\:region').closest('.field').hide();
                    $('#' + addressType + '\\:region_id').val(null);
                    $('#' + addressType + '\\:region_id').kendoDropDownList();
                    $('#' + addressType + '\\:region_id').closest('.field').show();
                }
            });
        },
        // validateBillingForm function
        validateBillingForm: function () {
            var valid = false;
            var billingValidationRules = {
                firstName: {
                    identifier: 'billing[firstname]',
                    rules: [{
                        type: 'empty',
                        prompt: EasyCheckout.prototype.translate('Please enter your first name')
                    }]
                },
                lastName: {
                    identifier: 'billing[lastname]',
                    rules: [{
                        type: 'empty',
                        prompt: EasyCheckout.prototype.translate('Please enter your last name')
                    }]
                },
                email: {
                    identifier: 'billing[email]',
                    rules: [{
                        type: 'email',
                        prompt: EasyCheckout.prototype.translate('Please enter a valid email address')
                    }]
                },
                billingAddress: {
                    identifier: 'billing\\:street1',
                    rules: [{
                        type: 'empty',
                        prompt: EasyCheckout.prototype.translate('Please enter your address')
                    }]
                },
                countryId: {
                    identifier: 'billing[country_id]',
                    rules: [{
                        type: 'empty',
                        prompt: EasyCheckout.prototype.translate('Please select your country')
                    }]
                },
                regionId: {
                    identifier: 'billing[region_id]',
                    rules: [{
                        type: 'empty',
                        prompt: EasyCheckout.prototype.translate('Please select your state/province')
                    }]
                },
                city: {
                    identifier: 'billing\\:city',
                    rules: [{
                        type: 'empty',
                        prompt: EasyCheckout.prototype.translate('Please enter your city')
                    }]
                },
                postcode: 
                ($('#billing\\:postcode').is(':visible')) ? 
                        {
                            identifier: 'billing[postcode]',
                            rules: [
                                {
                                type: 'empty',
                                prompt: EasyCheckout.prototype.translate('Please enter your postcode')
                               },
                                /*{
                                type: 'number',
                                prompt: EasyCheckout.prototype.translate('Please enter valid postcode')
                                }*/
                            ]
                        } 
                    : 
                        {
                            // no postcode validation
                        },
                telephone: {
                    identifier: 'billing[telephone]',
                    rules: [
                       {
                        type: 'empty',
                        prompt: EasyCheckout.prototype.translate('Please enter your telephone number')
                       }
                    ]
                },
                customer_password: {
                    identifier: 'billing[customer_password]',
                    rules: [{
                        type: 'empty',
                        prompt: EasyCheckout.prototype.translate('Please enter your password.')
                    }]
                },
                confirm_password: {
                    identifier: 'billing[confirm_password]',
                    rules: [{
                        type: 'empty',
                        prompt: EasyCheckout.prototype.translate('Please confirm your password.')
                    }]
                }
            };
            if(isNumericTel) {
                billingValidationRules.telephone.rules.push({
                    type: 'number',
                    prompt: EasyCheckout.prototype.translate('Please enter valid phone number')
                });
                billingValidationRules.telephone.rules.push({
                    type: 'length[6]',
                    prompt: EasyCheckout.prototype.translate('Phone number must be at least 6 digits')
                });
            }
            $('#aw-orderattributes-checkoutonepage-attributes-container-billing-address .field :input.required-entry').each(function() {
                var el = $(this);
                var label = $("label[for='" + el.attr('id') + "']").text();
                var rule = new Object();
                rule.identifier = el.attr('id');
                rule.rules = [{
                    type: 'empty',
                    prompt: EasyCheckout.prototype.translate(label + EasyCheckout.prototype.translate(' is required field.'))
                }];
                billingValidationRules[rule.identifier] = rule;
            });
            $('.ui.form.billing')
                .form(billingValidationRules, {
                    onSuccess: function () {
                        valid = true;
                        isBillingAddressVaild = true;
                    },
                    onFailure: function () {
                        valid = false;
                        isBillingAddressVaild = false;
                    },
                    debug: debug,
                    performance: performance,
                    verbose: verbose
                }).form('validate form');;
            return valid;
        },
        // validateShippingForm function
        validateShippingForm: function () {
            if(use_billing_address || isVirtual) return true;
            var valid = false;
            var shippingValidationRules = {
                    firstName: {
                        identifier: 'shipping[firstname]',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your first name')
                        }]
                    },
                    lastName: {
                        identifier: 'shipping[lastname]',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your last name')
                        }]
                    },
                    email: {
                        identifier: 'shipping[email]',
                        rules: [{
                            type: 'email',
                            prompt: EasyCheckout.prototype.translate('Please enter a valid email address')
                        }]
                    },
                    shippingAddress: {
                        identifier: 'shipping\\:street1',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your address')
                        }]
                    },
                    countryId: {
                        identifier: 'shipping[country_id]',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please select your country')
                        }]
                    },
                    regionId: {
                        identifier: 'shipping[region_id]',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please select your state/province')
                        }]
                    },
                    city: {
                        identifier: 'shipping\\:city',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your city')
                        }]
                    },
                    postcode: 
                    ($('#shipping\\:postcode').is(':visible')) ? 
                        {
                            identifier: 'shipping[postcode]',
                            rules: [
                                {
                                type: 'empty',
                                prompt: EasyCheckout.prototype.translate('Please enter your postcode')
                               },
                                /*{
                                type: 'number',
                                prompt: EasyCheckout.prototype.translate('Please enter valid postcode')
                                }*/
                            ]
                        } 
                    : 
                        {
                            // no postcode validation
                        },
                    telephone: {
                        identifier: 'shipping[telephone]',
                        rules: [
                          {
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your telephone number')
                          }
                        ]
                    }
            };
            if(isNumericTel) {
                shippingValidationRules.telephone.rules.push({
                    type: 'number',
                    prompt: EasyCheckout.prototype.translate('Please enter valid phone number')
                });
                shippingValidationRules.telephone.rules.push({
                    type: 'length[6]',
                    prompt: EasyCheckout.prototype.translate('Phone number must be at least 6 digits')
                });
            }
            $('#aw-orderattributes-checkoutonepage-attributes-container-shipping-address .field :input.required-entry').each(function() {
                var el = $(this);
                var label = $("label[for='" + el.attr('id') + "']").text();
                var rule = new Object();
                rule.identifier = el.attr('id');
                rule.rules = [{
                    type: 'empty',
                    prompt: EasyCheckout.prototype.translate(label + EasyCheckout.prototype.translate(' is required field.'))
                }];
                shippingValidationRules[rule.identifier] = rule;
            });
            $('.ui.form.shipping')
                .form(shippingValidationRules, {
                    onSuccess: function () {
                        valid = true;
                        isShippingAddressVaild = true;
                    },
                    onFailure: function () {
                        valid = false;
                        isShippingAddressVaild = false;
                    },
                    debug: debug,
                    performance: performance,
                    verbose: verbose
                }).form('validate form');;
            return valid;
        },
        // validatePaymentForm function
        validatePaymentForm: function () {
            var valid = false;
            var paymentValidationRules = {
                    payment_cc_type: {
                        identifier: 'payment[cc_type]',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please select your credit card type')
                        }]
                    },
                    payment_cc_number: {
                        identifier: 'payment[cc_number]',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your credit card number')
                        },
                        {
                            type: 'checkCreditCardNumberValid',
                            prompt: EasyCheckout.prototype.translate('Your credit card number is invalid')
                        },
                        {
                            type: 'checkCreditCardNumber',
                            prompt: EasyCheckout.prototype.translate('Credit card number is mismatch with credit card type')
                        }]
                    },
                    creditcardpci_expiration: {
                        identifier: 'creditcardpci_expiration',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your credit card expiration month')
                        },
                        {
                            type: 'checkDate',
                            prompt: EasyCheckout.prototype.translate('Your credit card expiration month is invalid')
                        }]
                    },
                    creditcardpci_expiration_yr: {
                        identifier: 'creditcardpci_expiration_yr',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your credit card expiration year')
                        },
                        {
                            type: 'checkDate',
                            prompt: EasyCheckout.prototype.translate('Your credit card expiration year is invalid')
                        }]

                    },
                    ccsave_expiration: {
                        identifier: 'ccsave_expiration',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your credit card expiration month')
                        },
                        {
                            type: 'checkDateSaved',
                            prompt: EasyCheckout.prototype.translate('Your credit card expiration month is invalid')
                        }]
                    },
                    ccsave_expiration_yr: {
                        identifier: 'ccsave_expiration_yr',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your credit card expiration year')
                        },
                        {
                            type: 'checkDateSaved',
                            prompt: EasyCheckout.prototype.translate('Your credit card expiration year is invalid')
                        }]

                    },
                    payment_cc_cid: {
                        identifier: 'payment[cc_cid]',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please select your credit card verification number')
                        },
                        {
                            type: 'checkCvvCode',
                            prompt: EasyCheckout.prototype.translate('Please enter a valid credit card verification number.')
                        }]
                    },
                    payment_cc_owner: {
                        identifier: 'payment[cc_owner]',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your name (as on the card)')
                        }]
                    }
                };
            if ($('[id^="payment_form_"]:visible').length == 0) {
                return true;
            }
            $('[id^="payment_form_"]:visible')
                .form(paymentValidationRules, {
                    onSuccess: function () {
                        valid = true;
                    },
                    onFailure: function () {
                        valid = false;
                    },
                    debug: debug,
                    performance: performance,
                    verbose: verbose
                }).form('validate form');;
            return valid;
        },
        // validatePaymentMethodForm function
        validatePaymentMethodForm: function () {
            var valid = false;
            var paymentMethodValidationRules = {};
            $('#aw-orderattributes-checkoutonepage-attributes-container-payment-method .field :input.required-entry').each(function() {
                var el = $(this);
                var label = $("label[for='" + el.attr('id') + "']").text();
                var rule = new Object();
                rule.identifier = el.attr('id');
                rule.rules = [{
                    type: 'empty',
                    prompt: EasyCheckout.prototype.translate(label + EasyCheckout.prototype.translate(' is required field.'))
                }];
                paymentMethodValidationRules[rule.identifier] = rule;
            });
            // Make sure that there is a payment method selected. 
            paymentMethodValidationRules['payment[method]'] = {
                identifier: 'payment[method]',
                rules: [{
                    type: 'checked',
                    prompt: EasyCheckout.prototype.translate(EasyCheckout.prototype.translate('Please select a payment method.'))
                }]
            }
            $('.form.payment')
                .form(paymentMethodValidationRules, {
                    onSuccess: function () {
                        valid = true;
                    },
                    onFailure: function () {
                        valid = false;
                    },
                    debug: debug,
                    performance: performance,
                    verbose: verbose
                }).form('validate form');;
            return valid;
        },
        // validateShippingForm function
        validateShippingMethodForm: function () {
            if(isVirtual) return true;
            var valid = false;
            var shippingMethodValidationRules = {};
            $('#aw-orderattributes-checkoutonepage-attributes-container-shipping-method .field :input.required-entry').each(function() {
                var el = $(this);
                var label = $("label[for='" + el.attr('id') + "']").text();
                var rule = new Object();
                rule.identifier = el.attr('id');
                rule.rules = [{
                    type: 'empty',
                    prompt: EasyCheckout.prototype.translate(label + EasyCheckout.prototype.translate(' is required field.'))
                }];
                shippingMethodValidationRules[rule.identifier] = rule;
            });
            // Make sure that there is a shipping method selected. 
            shippingMethodValidationRules['shipping_method'] = {
                identifier: 'shipping_method',
                rules: [{
                    type: 'checked',
                    prompt: EasyCheckout.prototype.translate(EasyCheckout.prototype.translate('Please select a shipping method.'))
                }]
            }
            $('.form.shipping-method')
                .form(shippingMethodValidationRules, {
                    onSuccess: function () {
                        valid = true;
                    },
                    onFailure: function () {
                        valid = false;
                    },
                    debug: debug,
                    performance: performance,
                    verbose: verbose
                }).form('validate form');;
            return valid;
        },
        // validateOrderReviewForm function
        validateOrderReviewForm: function () {
            var valid = false;
            var orderReviewValidationRules = {};
            $('#aw-orderattributes-checkoutonepage-attributes-container-order-review .field :input.required-entry').each(function() {
                var el = $(this);
                var label = $("label[for='" + el.attr('id') + "']").text();
                var rule = new Object();
                rule.identifier = el.attr('id');
                rule.rules = [{
                    type: 'empty',
                    prompt: EasyCheckout.prototype.translate(label + EasyCheckout.prototype.translate(' is required field.'))
                }];
                orderReviewValidationRules[rule.identifier] = rule;
            });
            $('.form.review')
                .form(orderReviewValidationRules, {
                    onSuccess: function () {
                        valid = true;
                    },
                    onFailure: function () {
                        valid = false;
                    },
                    debug: debug,
                    performance: performance,
                    verbose: verbose
                }).form('validate form');;
            return valid;
        },
        // validateLoginForm function
        validateLoginForm: function () {
            var valid = false;
            $('#loginForm')
                .form({
                    email: {
                        identifier: 'login[email]',
                        rules: [{
                            type: 'email',
                            prompt: EasyCheckout.prototype.translate('Please enter valid email address.')
                        }]
                    },
                    password: {
                        identifier: 'login[password]',
                        rules: [{
                            type: 'empty',
                            prompt: EasyCheckout.prototype.translate('Please enter your password')
                        }]
                    }
                }, {
                    onSuccess: function () {
                        valid = true;
                    },
                    onFailure: function () {
                        valid = false;
                    },
                    debug: debug,
                    performance: performance,
                    verbose: verbose
                }).form('validate form');;
            return valid;
        },
        // validateQtyUpdateForm function
        validateQtyUpdateForm: function () {
            var valid = true;
            $('#checkout-review-table .qty').each(function (index, value) {
                if ($(value).val() == '' || $(value).val() == 0 || !RegExp('^[0-9]+$').test($(value).val())) {
                    $(value).popup({
                        content: 'You must use a number more that zero.'
                    }).popup('show');
                    valid = false;
                    return;
                }
            });
            return valid;
        },
        //setCustomerAddress
        setCustomerAddress: function () {

        },
        // delay function
        delay: (function () {
            var timer = 0;
            return function (callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })(),
        // getCountryCities function
        getCountryCities: function (countryCode, startWith) {
            cities = [];
            EasyCheckout.prototype.hideMessage();
            var request = $.ajax({
                url: "http://api.geonames.org/searchJSON",
                type: "GET",
                data: {
                    country: countryCode,
                    name_startsWith: startWith,
                    username: geonamesUsername
                },
                dataType: "JSON"
            });
            request.done(function (data) {
                $.each(data.geonames, function (index, cityObject) {
                    if ($.inArray(cityObject.name, cities) == -1 && RegExp('^\s*([0-9a-zA-Z]*)\s*$').test(cityObject.name)) {
                        cities.push($.trim(cityObject.countryName), $.trim(cityObject.name));
                    }
                });
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
        },
        // getAramexLocationAPICountryCities function
        getAramexLocationAPICountryCities: function (countryCode, state, startWith) {
            cities = [];
            EasyCheckout.prototype.hideMessage();
            var request = $.ajax({
                url: baseUrl + 'easycheckout/api/fetchCities',
                type: "POST",
                data: {
                    countryCode: countryCode,
                    state: state,
                    nameStartsWith: startWith
                },
                dataType: "JSON"
            });
            request.done(function (data) {
                if(data.error) {
                    console.log('%c An unknown issue has occurred: ' + data.message, 'background: red; color: white; font-size:15px');
                    return false;
                }
                $.each(data.data, function (index, cityName) {
                    cities.push($.trim(cityName));
                });
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
        },
        // get getAramexCountryCities 
        getAramexCountryCities: function(data) {
            var request = jQuery.ajax({
                url: "https://www.aramex.com/CityLookupService.aspx",
                type: "POST",
                data: data,
                dataType: "JSONP"
            });
            request.done(function( data ) {
                eval(data);
            });
            request.fail(function( jqXHR, textStatus ) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
        },
        //update country cities datasource
        updateCitiesDataSource: function (countryCode, target, state) {

            // CITIES INTEGRATION: START
            if (citiesDataSource === 'cities_api' && cityInput === 'autocomplete') {
                $('[id$=":city"]').closest('.field.loading').find('i').removeClass('fa-circle-o-notch');
                return;
            }
            // CITIES INTEGRATION: END

            switch(cityInput) {
                case 'dropdown':
                    // target.enable(false);
                    target.readonly(true);
                break;
                case 'autocomplete':
                    $('[id$=":city"]').attr('placeholder', EasyCheckout.prototype.translate('Loading ...')).prop('readonly', true);
                    $('[id$=":city"]').closest('.field.loading').find('i').addClass('fa-circle-o-notch');
                break;
            }
            NProgress.set(0.4);
            EasyCheckout.prototype.hideMessage();
            switch(citiesDataSource) {
                case 'no_data_source':
                    // nothing to do!
                    var cities = [];
                    if(target !== undefined)
                        target.setDataSource(cities);
                    switch(cityInput) {
                        case 'dropdown':
                            // target.enable(true);
                            target.readonly(false);
                        break;
                        case 'autocomplete':
                            $('[id$=":city"]').attr('placeholder', EasyCheckout.prototype.translate('Select city...')).prop('readonly', false);
                            $('[id$=":city"]').closest('.field.loading').find('i').removeClass('fa-circle-o-notch');
                        break;
                    }
                    NProgress.done();
                break;

                // CITIES INTEGRATION: START
                case 'cities_api':
                    var request = $.ajax({
                    url: cities_url,
                    type: "GET",
                    data: {
                        username: cities_username,
                        country: countryCode,
                        state: function() {
                            var selectedCountry = countryCode;
                            if (selectedCountry !== 'US') return '';
                            var selectedRegionIndex = state;
                            return countryRegions[selectedCountry][selectedRegionIndex].code;
                        },
                        term: '',
                        lang: store_code,
                        hostname: store_hostname
                    },
                    dataType: "JSONP"
                    });
                    request.done(function (data) {

                        switch(cityInput) {
                            case 'dropdown':
                                var cities = [];
                                if(data.data) {
                                    $.each(data.data, function (index, cityObject) {
                                        if ($.inArray($.trim(cityObject.en), cities) == -1) {
                                            if(cityObject.en) cities.push({value: $.trim(cityObject.en), text: $.trim(cityObject[store_code])});
                                        }
                                    });
                                }
                                
                                target.setDataSource(cities);
                                target.readonly(false);
                            break;
                            case 'autocomplete':
                                $('[id$=":city"]').attr('placeholder', EasyCheckout.prototype.translate('Select city...')).prop('readonly', false);
                                $('[id$=":city"]').closest('.field.loading').find('i').removeClass('fa-circle-o-notch');
                            break;
                        }
                        NProgress.done();
                    });
                    request.fail(function (jqXHR, textStatus) {
                        switch(cityInput) {
                            case 'dropdown':
                                target.readonly(false);
                            break;
                            case 'autocomplete':
                                $('[id$=":city"]').attr('placeholder', EasyCheckout.prototype.translate('Select city...')).prop('readonly', false);
                                $('[id$=":city"]').closest('.field.loading').find('i').removeClass('fa-circle-o-notch');
                            break;
                        }
                        NProgress.done();
                        console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
                    });
                break;
                // CITIES INTEGRATION: END

                case 'geonames_api':
                    var request = $.ajax({
                    url: "http://api.geonames.org/searchJSON",
                    type: "GET",
                    data: {
                        country: countryCode,
                        name_startsWith: '',
                        username: geonamesUsername
                    },
                    // Firefox fix
                    dataType: "JSONP"
                    });
                    request.done(function (data) {
                        var cities = [];
                        if(data.geonames) {
                            $.each(data.geonames, function (index, cityObject) {
                                if ($.inArray($.trim(cityObject.name), cities) == -1 && RegExp('^\s*([0-9a-zA-Z]*)\s*$').test($.trim(cityObject.name))) {
                                    if(cityObject.name) cities.push($.trim(cityObject.name));
                                }
                            });
                        }
                        if(target !== undefined)
                            target.setDataSource(cities);
                        switch(cityInput) {
                            case 'dropdown':
                                // target.enable(true);
                                target.readonly(false);
                            break;
                            case 'autocomplete':
                                $('[id$=":city"]').attr('placeholder', EasyCheckout.prototype.translate('Select city...')).prop('readonly', false);
                                $('[id$=":city"]').closest('.field.loading').find('i').removeClass('fa-circle-o-notch');
                            break;
                        }
                        NProgress.done();
                    });
                    request.fail(function (jqXHR, textStatus) {
                        switch(cityInput) {
                            case 'dropdown':
                                // target.enable(true);
                                target.readonly(false);
                            break;
                            case 'autocomplete':
                                $('[id$=":city"]').attr('placeholder', EasyCheckout.prototype.translate('Select city...')).prop('readonly', false);
                                $('[id$=":city"]').closest('.field.loading').find('i').removeClass('fa-circle-o-notch');
                            break;
                        }
                        NProgress.done();
                        console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
                    });
                break;
                case 'aramex_api':
                case 'aramex_api_shipping_module':
                    var request = $.ajax({
                    url: baseUrl + 'easycheckout/api/fetchCities',
                    type: "POST",
                    data: {
                        countryCode: countryCode,
                        state: state,
                        nameStartsWith: ''
                    },
                    // Firefox fix
                    dataType: "JSON"
                    });
                    request.done(function (data) {
                        if(data.error) {
                            switch(cityInput) {
                                case 'dropdown':
                                    // target.enable(true);
                                    target.readonly(false);
                                break;
                                case 'autocomplete':
                                    $('[id$=":city"]').attr('placeholder', EasyCheckout.prototype.translate('Select city...')).prop('readonly', false);
                                    $('[id$=":city"]').closest('.field.loading').find('i').removeClass('fa-circle-o-notch');
                                break;
                            }
                            NProgress.done();
                            console.log('%c An unknown issue has occurred: ' + data.message, 'background: red; color: white; font-size:15px');
                            return false;
                        }
                        var cities = [];
                        if(data.data) {
                            $.each(data.data, function (index, cityName) {
                                if(cityName) cities.push($.trim(cityName));
                            });
                        }
                        if(target !== undefined)
                            target.setDataSource(cities);
                        switch(cityInput) {
                            case 'dropdown':
                                // target.enable(true);
                                target.readonly(false);
                            break;
                            case 'autocomplete':
                                $('[id$=":city"]').attr('placeholder', EasyCheckout.prototype.translate('Select city...')).prop('readonly', false);
                                $('[id$=":city"]').closest('.field.loading').find('i').removeClass('fa-circle-o-notch');
                            break;
                        }
                        NProgress.done();
                    });
                    request.fail(function (jqXHR, textStatus) {
                        switch(cityInput) {
                            case 'dropdown':
                                // target.enable(true);
                                target.readonly(false);
                            break;
                            case 'autocomplete':
                                $('[id$=":city"]').attr('placeholder', EasyCheckout.prototype.translate('Select city...')).prop('readonly', false);
                                $('[id$=":city"]').closest('.field.loading').find('i').removeClass('fa-circle-o-notch');
                            break;
                        }
                        NProgress.done();
                        console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
                    });
                break;
                case 'csv_table':
                    //
                break;
                default:
                    //
                break;
            }
        },
        //Translate function
        translate: function (key) {
            if (translationData[key] != undefined) {
                return translationData[key];
            }
            return key;
        },
        //Switch checkout method
        switchCheckoutMethod: function (method) {
            EasyCheckout.prototype.hideMessage();
            var request = $.ajax({
                url: baseUrl + "easycheckout/index/switchMethod",
                type: "POST",
                data: {
                    method: method
                },
                dataType: "HTML"
            });
            request.done(function (data) {

            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
        },
        //Get address
        getAddress: function(type, value) {
            if(!type) return false;
            EasyCheckout.prototype.hideMessage();
            NProgress.set(0.4);
            var request = $.ajax({
                url: baseUrl + 'easycheckout/index/getAddress',
                type: "POST",
                data: {
                    address: value
                },
                dataType: "JSON"
            });
            request.done(function (data) {
                /*$.each(data, function (index, value) {
                    if ($('#' + type + '\\:' + index) !== undefined) {
                        $('#' + type + '\\:' + index).val(value);
                    }
                });*/
                NProgress.done();
                for(var el in data) {
                    if ($('#' + type + '\\:' + el) !== undefined) {
                        $('#' + type + '\\:' + el).val(data[el]);
                    }
                }
                if(type == 'billing') {
                    if(!EasyCheckout.prototype.validateBillingForm()) {
                        $('.billingNewAddressForm').show();
                        return false;
                    } else {
                        EasyCheckout.prototype.saveBilling();
                    }
                } else if(type == 'shipping') {
                    if(!EasyCheckout.prototype.validateShippingForm()) {
                        $('.shippingNewAddressForm').show();
                        return false;
                    } else {
                        EasyCheckout.prototype.saveShipping();
                    }
                } else {
                    return false
                }
            });
            request.fail(function (jqXHR, textStatus) {
                console.log('%c An unknown issue has occurred: ' + textStatus, 'background: red; color: white; font-size:15px');
            });
        },
        //Show login modal
        showLoginModal: function () {
            $('#loginModal')
                .modal('show');
        },
        //Checkout login function
        checkoutLogin: function () {

        },
        // show global messages
        showMessage: function(message) {
            /*$('#globalMessages').html($('#globalMessages .icon')).append(message);
            $('#globalMessages').transition('fade in');
            $("html, body").animate({
                scrollTop: 0
            }, "slow");*/
            //alertify.showMessage(message);
            alertify.error(message);
        },
        hideMessage: function() {
            if($('#globalMessages').is(':visible')) $('#globalMessages').transition('fade out');
        },
        isNumeric: function(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        },
        // set location function
        setLocation: function (url) {
            window.location.href = url;
        }
    };
    //
    window.easyCheckout = new EasyCheckout();
})(jQuery);