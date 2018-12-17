define([
    'jquery',
    "Magento_Ui/js/form/components/fieldset",
    'uiRegistry',
], function ($, Component, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            imports: {
                optionType: '${ $ .provider }:data.option.type',
            },
            applicableTo: [
                'select',
                'radio',
                'image'
            ]
        },

        /**
         * execution starts
         */
        initialize: function () {
            var self = this;
            this._super();
            self.initSubscribers();
            self.visible(self.applicableTo.includes(self.optionType()));
        },

        /**
         * init observers
         */
        initObservable: function () {
            this._super().observe(
                'optionType'
            );

            return this;
        },

        /**
         * initialize subscribers
         */
        initSubscribers: function () {
            var self = this;

            //creating a knockout subscriber to observe any changes in the option type
            self.optionType.subscribe(
                function (optionType) {
                    var visible = self.applicableTo.includes(optionType);
                    self.visible(visible);
                    var rowsComponent = registry.get('configurator_option_form.configurator_option_form.general.container_values.values');
                    if (visible) {
                        rowsComponent.addChild();
                        setTimeout(function () {
                                rowsComponent.showSpinner(false);
                            }, 500);
                    } else {
                        rowsComponent.clear();
                    }
                }
            );
        }
    });
});