define([
    "uiRegistry",
    "Magento_Ui/js/form/components/button"
], function (registry, Button) {
    'use strict';

    return Button.extend({
        action: function () {
            var index = this.parentName.replace('.options_container.header', '').slice(-1);
            var insertListing = registry.get("product_form.product_form.assign_configurator_option_modal.assign_configurator_option_grid");
            insertListing.callerIndex = index;
            this._super();
        },
    });
});