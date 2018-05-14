define([
    'jquery',
    "Magento_Ui/js/form/components/fieldset",
    'uiRegistry',
    'Magento_Ui/js/dynamic-rows/action-delete'
], function($, Component, registry, actionDelete) {
    'use strict';

    return Component.extend({
        defaults: {
            imports: {
                optionType: '${ $ .provider }:data.option.type',
            }
        },

        /**
         * execution starts
         */
        initialize: function () {
            var self = this;
            this._super();
            self.initSubscribers();
            self.visible(self.optionType() === 'select');
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
                    var visible = optionType === 'select';
                    self.visible(visible);
                }
            );
        },
    });
}
);