define([
    'jquery',
    "Magento_Ui/js/form/components/group",
    "uiRegistry",
], function ($, Component, registry) {
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
            var isVisibleSwitch = registry.get("configurator_option_form.configurator_option_form.general.container_is_visible.is_visible");
            self.visible(self.optionType() === 'file');
            if (!self.visible) {
                isVisibleSwitch.checked(false);
                isVisibleSwitch.disabled(false);
            } else {
                if (self.optionType() === 'file') {
                    isVisibleSwitch.checked(true);
                    isVisibleSwitch.disabled(true);
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

        /**
         * initialize subscribers
         */
        initSubscribers: function () {
            var self = this;

            //creating a knockout subscriber to observe any changes in the option type
            self.optionType.subscribe(
                function (optionType) {
                    var visible = optionType === 'file';
                    self.visible(visible);
                    if (!visible) {
                        $.each(self.elems(), function (index, elem) {
                            elem.value(null);
                        });
                    }
                }
            );
        },

        toggleVisibility: function () {
            var self = this,
                isRequiredSwitch = registry.get("configurator_option_form.configurator_option_form.general.container_is_visible.is_visible");

            this.visible(this.optionType() === 'file');
            if (!self.visible()) {
                if (!isRequiredSwitch.value()) {
                    isRequiredSwitch.checked(false);
                }
                isRequiredSwitch.disabled(false);
            } else {
                if (self.optionType() === 'file') {
                    isRequiredSwitch.checked(true);
                }
                isRequiredSwitch.disabled(true);
            }
        }
    });
});