define([
    "jquery"
], function ($) {
    'use strict';

    return function (widget) {

        $.widget('mage.catalogAddToCart', widget, {
            ajaxSubmit: function () {
                var form = arguments[0];
                // Find disabled inputs, and remove the "disabled" attribute
                var disabled = $(form).find(':input:disabled.product-configurator-option').removeAttr('disabled');

                var priceBox = $('.price-box', $('.product-configurator-option').element);

                var configuratedPrice;


                if (priceBox.data('magePriceBox') &&
                    priceBox.data('magePriceBox').cache &&
                    priceBox.data('magePriceBox').cache.displayPrices &&
                    priceBox.data('magePriceBox').cache.displayPrices.finalPrice) {
                    configuratedPrice = priceBox.data('magePriceBox').cache.displayPrices.finalPrice.amount;

                    var input = document.createElement("input");
                    input.setAttribute("type", "hidden");
                    input.setAttribute("name", "configured_price");
                    input.setAttribute("id", "configured_price");
                    input.setAttribute("value", configuratedPrice);
                    $(form[0]).append(input);
                }
                var res =  this._super(form);
                // re-disabled the set of inputs that you previously enabled
                disabled.attr('disabled','disabled');
                return res;
            },
            submitForm: function (form) {
                this._super(form);
                $(form).find('#configured_price').remove();
            }
        });

        return $.mage.catalogAddToCart;
    }
});