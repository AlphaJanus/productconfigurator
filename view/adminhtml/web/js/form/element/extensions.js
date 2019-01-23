define([
    "Magento_Ui/js/form/element/abstract",
    "uiRegistry"
], function (Abstract, registry) {
    'use strict';
    return Abstract.extend({
        defaults: {
            modules: {
                parent: '${ $.parentName }'
            }
        },
        initObservable: function () {
            this._super();
            this.parentModule = registry.get(this.modules.parent);
            this.visible = this.parentModule.visible;

            return this;
        }
    });
});