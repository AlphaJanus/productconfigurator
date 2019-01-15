define([
    "Magento_Ui/js/form/element/single-checkbox"
], function (Checkbox) {
    'use strict';

    return Checkbox.extend({
        defaults: {
            imports: {
                parentOption: '${ $ .provider }:${ $ .dataScope.replace(/.values.[(0-9)]+.is_dependent/, "") }.parent_option',
                isEnabled: '${ $ .provider }:${ $ .parentScope }.enabled'
            }
        },

        initObservable: function () {
            this._super();
            this.observe(['parentOption', 'isEnabled']);
            this.on('parentOption', this.refreshDisabled.bind(this));
            this.on('isEnabled', this.refreshDisabled.bind(this));
            return this;
        },

        refreshDisabled: function () {
            if (this.parentOption() !== 0 && typeof(this.parentOption()) !== 'undefined' && parseInt(this.isEnabled(), 10)) {
                this.disabled(false);
            } else {
                this.disabled(true);
                if (typeof(this.isEnabled()) !== 'undefined') {
                    this.value(0);
                }
            }
        }
    });
});