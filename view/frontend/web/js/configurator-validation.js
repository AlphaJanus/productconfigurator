define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {
    'use strict';

    return function () {
        $.validator.addMethod(
            "lte",
            function (value, element, params) {
                if ($.isNumeric(params) && $.isNumeric(value)) {
                    this.lteToVal = params;

                    return parseFloat(value) <= parseFloat(params);
                }

                return true;
            },
            function () {
                var message = $.mage.__('Please enter a value less than or equal to %s.');

                return message.replace('%s', this.lteToVal);
            }
        );
        $.validator.addMethod(
            "gte",
            function (value, element, params) {
                if ($.isNumeric(params) && $.isNumeric(value)) {
                    this.gteToVal = params;

                    return parseFloat(value) >= parseFloat(params);
                }

                return true;
            },
            function () {
                var message = $.mage.__('Please enter a value greater than or equal to %s.');

                return message.replace('%s', this.gteToVal);
            }
        );
    }
});