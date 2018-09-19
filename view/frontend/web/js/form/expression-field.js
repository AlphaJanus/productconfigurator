define([
    "jquery",
    'underscore',
    'mage/translate'
], function ($,_,$t) {
    'use strict';

    var configuratorOptions = $('#product_addtocart_form').data('mageConfiguratorOptions');

    $.widget('mage.expressionField', {

        /**
         * @private
         */
        _init: function initPriceBundle() {
            $(configuratorOptions.options.optionsSelector, this.element).trigger('change');
        },

        _create: function () {
            var form = document.getElementById('product_addtocart_form')
            this.currentOptions = $(configuratorOptions.options.optionsSelector, form);
            this.optionsData = configuratorOptions.options.dependencyConfig;
            this.input = document.getElementById(this.options.input);
            this.optionId = this.options.input.replace('expression-', '');

            this.currentOptions.on('change', this._recalculateValue.bind(this));
        },

        _recalculateValue: function() {
            var optionVal,
                optionId,
                parts,
                expr,
                val,
                valueData,
                self = this,
                tempExpr = '';

            $.each(this.currentOptions, function(index, option){
                if($(option).val()) {
                    switch (option.type) {
                        case 'select-one':
                            parts = /^(configurator_options\[)(\d+)(\])$/.exec($(option).data('selector'));
                            optionId = parts[2];
                            valueData = _.findWhere(self.optionsData[optionId].values, {'value_id': $(option).val().toString()})
                            optionVal = valueData.value;
                            break;
                        case 'text':
                        default:
                            optionVal = $(option).val();
                            break;
                    }
                    optionVal = (isNaN(optionVal)) ? '\"' + optionVal + '\"' : optionVal;
                    tempExpr += "var " + $(option).data('code') + "=" + optionVal + ";\n";
                }
            });
            expr = tempExpr + this.options.expressionCode;
            try {
                val = eval(expr);
            } catch (err) {
                console.log($t('*** Problem when evaluating the expression for the option "' + $(this.input).data('code') + '": ') + err);
            }
            if(self.optionsData[self.optionId].add_to_price) {
                var changes = {};
                changes[this.input.name] = {
                    basePrice: {
                        amount: val
                    },
                    finalPrice: {
                        amount: val
                    },
                    oldPrice: {
                        amount: val
                    },
                };
                $(configuratorOptions.options.priceHolderSelector).trigger('updatePrice', changes);
            }
            this.input.value = val;


        }
    });
    return $.mage.expressionField;
});