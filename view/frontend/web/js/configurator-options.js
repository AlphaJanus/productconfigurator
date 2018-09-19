define([
    "jquery",
    'underscore',
    'mage/template',
    'priceUtils',
    'priceBox',
    'jquery/ui'
], function ($, _, mageTemplate, utils) {
    'use strict';

    var globalOptions = {
        productId: null,
        priceHolderSelector: '.price-box', //data-role="priceBox"
        optionsSelector: '.product-configurator-option',
        optionConfig: {},
        optionHandlers: {},
        optionTemplate: '<%= data.label %>' +
            '<% if (data.finalPrice.value) { %>' +
            ' +<%- data.finalPrice.formatted %>' +
            '<% } %>',
        controlContainer: 'dd'
    };

    /**
     * Custom option preprocessor
     * @param  {jQuery} element
     * @param  {Object} optionsConfig - part of config
     * @return {Object}
     */
    function defaultGetOptionValue(element, optionsConfig) {
        var changes = {},
            optionValue = element.val(),
            optionId = utils.findOptionId(element[0]),
            optionName = element.prop('name'),
            optionType = element.prop('type'),
            optionConfig = optionsConfig[optionId],
            optionHash = optionName;

        switch (optionType) {
            case 'text':
            case 'textarea':
                changes[optionHash] = optionValue ? optionConfig.prices : {};
                break;

            case 'radio':
                if (element.is(':checked')) {
                    changes[optionHash] = optionConfig[optionValue] && optionConfig[optionValue].prices || {};
                }
                break;

            case 'select-one':
                changes[optionHash] = optionConfig[optionValue] && optionConfig[optionValue].prices || {};
                break;

            case 'select-multiple':
                _.each(optionConfig, function (row, optionValueCode) {
                    optionHash = optionName + '##' + optionValueCode;
                    changes[optionHash] = _.contains(optionValue, optionValueCode) ? row.prices : {};
                });
                break;

            case 'checkbox':
                optionHash = optionName + '##' + optionValue;
                changes[optionHash] = element.is(':checked') ? optionConfig[optionValue].prices : {};
                break;

            case 'file':
                // Checking for 'disable' property equal to checking DOMNode with id*="change-"
                changes[optionHash] = optionValue || element.prop('disabled') ? optionConfig.prices : {};
                break;
        }

        return changes;
    }

    $.widget('mage.configuratorOptions', {
        options: globalOptions,
        /**
         * @private
         */
        _init: function initPriceBundle() {
            $(this.options.optionsSelector, this.element).trigger('change');
        },

        /**
         * Widget creating method.
         * Triggered once.
         * @private
         */
        _create: function createConfiguratorOptions() {
            var form = this.element,
                options = $(this.options.optionsSelector, form),
                priceBox = $(this.options.priceHolderSelector, $(this.options.optionsSelector).element);

            if (priceBox.data('magePriceBox') &&
                priceBox.priceBox('option') &&
                priceBox.priceBox('option').priceConfig
            ) {
                if (priceBox.priceBox('option').priceConfig.optionTemplate) {
                    this._setOption('optionTemplate', priceBox.priceBox('option').priceConfig.optionTemplate);
                }
                this._setOption('priceFormat', priceBox.priceBox('option').priceConfig.priceFormat);
            }

            this._applyOptionNodeFix(options);

            options.on('change', this._onOptionChanged.bind(this));
            form.on('invalid-form', function(event,data){
                data.errorList[0].element.scrollIntoView(false);
            });
        },

        /**
         * Custom option change-event handler
         * @param {Event} event
         * @private
         */
        _onOptionChanged: function onOptionChanged(event) {
            var changes,
                option = $(event.target),
                handler = this.options.optionHandlers[option.data('role')];

            option.data('optionContainer', option.closest(this.options.controlContainer));

            if (handler && handler instanceof Function) {
                changes = handler(option, this.options.optionConfig, this);
            } else {
                changes = defaultGetOptionValue(option, this.options.optionConfig);
            }
            $(this.options.priceHolderSelector).trigger('updatePrice', changes);
            this.updateOptions();
        },

        /**
         * Helper to fix issue with option nodes:
         *  - you can't place any html in option ->
         *    so you can't style it via CSS
         * @param {jQuery} options
         * @private
         */
        _applyOptionNodeFix: function applyOptionNodeFix(options) {
            var config = this.options,
                format = config.priceFormat,
                template = config.optionTemplate;

            template = mageTemplate(template);
            options.filter('select').each(function (index, element) {
                var $element = $(element),
                    optionId = utils.findOptionId($element),
                    optionConfig = config.optionConfig && config.optionConfig[optionId];

                $element.find('option').each(function (idx, option) {
                    var $option,
                        optionValue,
                        toTemplate,
                        prices;

                    $option = $(option);
                    optionValue = $option.val();

                    if (!optionValue && optionValue !== 0) {
                        return;
                    }

                    toTemplate = {
                        data: {
                            label: optionConfig[optionValue] && optionConfig[optionValue].name
                        }
                    };
                    prices = optionConfig[optionValue] ? optionConfig[optionValue].prices : null;

                    if (prices) {
                        _.each(prices, function (price, type) {
                            var value = +price.amount;

                            value += _.reduce(price.adjustments, function (sum, x) { //eslint-disable-line
                                return sum + x;
                            }, 0);
                            toTemplate.data[type] = {
                                value: value,
                                formatted: utils.formatPrice(value, format)
                            };
                        });

                        $option.text(template(toTemplate));
                    }
                });
            });
        },

        updateOptions: function(){
            var optionId,
                parts,
                tempId,
                dependencyConfig,
                valueConfig,
                allowedVariants,
                parentOption,
                parentElement,
                options = $(this.options.optionsSelector),
                self = this;


            $.each(options, function (index, option) {
                /** to get Id of option */
                parts = /^(configurator_options\[)(\d+)(\])$/.exec($(option).data('selector'));
                optionId = parts[2];
                dependencyConfig = self.getDependencyConfig(optionId);
                tempId = parseInt(dependencyConfig.parent_option);
                if (tempId) {
                    parentOption = _.findWhere(self.options.dependencyConfig, {configurator_option_id: tempId.toString()})
                    parentElement = $('*[data-selector="configurator_options[' + parentOption.option_id + ']"]');
                    $.each(option.options, function (n, optionHtml) {
                        var optionValue = $(optionHtml).val();
                        valueConfig = _.findWhere(dependencyConfig.values, {value_id:optionValue.toString()});
                        if(valueConfig) {
                            allowedVariants = valueConfig.allowed_variants;
                        }
                        if(optionValue && parseInt(valueConfig.is_dependent) && !allowedVariants.includes(parentElement.val().toString()) ) {
                            $(optionHtml).attr('disabled','disabled');
                            if(optionHtml.parentElement.value === optionValue) {
                                optionHtml.parentElement.value = '';
                                $(optionHtml.parentElement).trigger('change');
                            }
                        } else {
                            $(optionHtml).removeAttr('disabled');
                            if(typeof(valueConfig) !== "undefined" && valueConfig.is_default == "1"){
                                optionHtml.parentElement.value = optionValue;
                            }
                            var availableOptions = _.filter(optionHtml.parentElement.options, function(el){
                                return (el.value !== "" && !el.disabled);
                            });
                            if(availableOptions.length == 1){
                                optionHtml.parentElement.value = availableOptions[0].value
                            }
                        }
                    });
                }
                if(option.type ==="select-one"){
                    if (_.where(option.options, {disabled: true}).length + 1 >= option.options.length){
                        $(option).parents('.field').addClass('hidden');
                        $(option).addClass('hidden');
                    } else {
                        $(option).parents('.field').removeClass('hidden');
                        $(option).removeClass('hidden');
                    }
                    $(option).trigger('visibilityChanged');
                }

            });
        },

        getDependencyConfig: function(optionId){
            return this.options.dependencyConfig[optionId];
        }
    });

    return $.mage.configuratorOptions;
});