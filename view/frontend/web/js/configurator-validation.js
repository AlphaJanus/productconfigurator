define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {
    'use strict';
    return function () {
        $.validator.setDefaults({
            ignore: '[style*="display: none"]',
            showErrors: function (errorsMap, errorsList) {
                var element;

                if (errorsList.length) {
                    element = errorsList[0].element;
                }
                var index = $(element).closest(".item.content")
                    .index(".item.content");
                $("#configurator-options-accordion").accordion("activate", index);

                jQuery('#product_addtocart_form').validate().defaultShowErrors();
            }
        });
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

        $.validator.addMethod(
            "fileextensions",
            function (value, element, params) {
                if (!value || !params) {
                    return true;
                }
                var ext = value.substring(value.lastIndexOf('.') + 1);
                var extensions = params.split(',');
                for (var i=0; i < extensions.length; i++) {
                    if (ext === extensions[i]) {
                        return true;
                    }
                }
                return false;
            },
            $.mage.__('Disallowed file type.')
        );

        $.validator.addMethod(
            "digits-two-after-comma",
            function (value) {
                var pattern = /^\d+(\.\d{1,2})?$/;
                if (pattern.test(value)) {
                    return true;
                }
                return false;
            },
            function () {
                return $.mage.__('Only 2 digits after comma');
            }
        );
    };
});