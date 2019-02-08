define([
    "uiRegistry",
    "Magento_Ui/js/form/components/button"
], function (registry, Button) {
    'use strict';

    return Button.extend({
        defaults: {
            links: {
                rowData: '${ $ .provider }:${ $ .dataScope }'
            },
        },
        action: function () {
            registry.get('index=dependencies').valueIndex = this.rowData.value_id;
            registry.get('index=dependencies').depScope = this.dataScope;
            registry.get('index=dependency_modal').setTitle(this.rowData.title);
            this._super();
        },
    });
});