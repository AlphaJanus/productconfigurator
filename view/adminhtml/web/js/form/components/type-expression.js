define([
    'jquery',
    "Magento_Ui/js/form/components/group",
    "uiRegistry",
], function ($, Component, registry) {
    'use strict';

    return Component.extend({

        defaults: {
            imports: {
                optionType: "${ $ .provider }:data.option.type"
            }
        },

        /**
         * execution starts
         */
        initialize: function () {
            var self = this;
            var isRequiredSwitch = registry.get("configurator_option_form.configurator_option_form.general.container_is_required.is_required");
            this._super();
            self.visible(self.optionType() === 'expression');
            if (!self.visible) {
                isRequiredSwitch.checked(false);
                isRequiredSwitch.disabled(false);
            } else {
                if (self.optionType() === 'expression') {
                    isRequiredSwitch.checked(true);
                }
            }
        },

        /**
         * init observers
         */
        initObservable: function () {
            this._super().observe(
                'optionType'
            );
            this.on('optionType', this.toggleVisibility.bind(this));


            return this;
        },

        toggleVisibility: function () {
            var self = this,
                isRequiredSwitch = registry.get("configurator_option_form.configurator_option_form.general.container_is_required.is_required");

            this.visible(this.optionType() === 'expression');
            if (!self.visible()) {
                isRequiredSwitch.checked(false);
                isRequiredSwitch.disabled(false);
            } else {
                if (self.optionType() === 'expression') {
                    isRequiredSwitch.checked(true);
                }
                isRequiredSwitch.disabled(true);
            }
        }
    });
});