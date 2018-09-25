define([
    "Magento_Ui/js/form/element/textarea",
    "uiRegistry"
], function (Textarea, registry) {
    'use strict';
    return Textarea.extend({
        defaults: {
            modules: {
                parent: '${ $.parentName }'
                //parent: '${ $.parentName }:visible'
            }
        },
        initialize: function () {
            this._super();
            this.parentModule = registry.get(this.modules.parent);
            this.visible = this.parentModule.visible;

            return this;
        }
    });
});