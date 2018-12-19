define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/multiselect'
], function (_, registry, Multiselect) {
    'use strict';

    return Multiselect.extend({
        defaults: {
            imports: {
                rows: '${ $ .provider }:${ $ .dataScope.replace(/.[(0-9)+].values.[(0-9)+].allowed_variants/, "") }',
                parentOption: '${ $ .provider }:${ $ .dataScope.replace(/.values.[(0-9)+].allowed_variants/, "") }.parent_option',
                isDependent: '${ $ .provider }:${ $ .parentScope }.is_dependent',
                isEnabled: '${ $ .provider }:${ $ .parentScope }.enabled',
            }
        },

        initObservable: function () {
            this._super();
            this.observe(['rows', 'parentOption', 'isDependent', 'isEnabled']);
            this.on('parentOption', this.refreshVariants.bind(this));
            this.on('isDependent', this.refreshDisable.bind(this));
            this.on('isEnabled', this.refreshDisable.bind(this));
            return this;
        },

        refreshVariants: function (value) {
            var option,
                self = this;
            var parentOption = _.findWhere(this.rows(), {configurator_option_id: value});
            if (typeof (parentOption) !== 'undefined' && parentOption !== 0) {
                var options = [],
                    variants = parentOption.values;
                variants.forEach(function (item) {
                    option = {
                        value: item.value_id,
                        label: item.title,
                        labeltitle: item.title
                    };
                    options.push(option);
                });
                self.setOptions(options);
                self.refreshDisable();
            } else {
                self.setOptions([]);
                self.refreshDisable();
            }

        },

        refreshDisable: function () {
            if (parseInt(this.isEnabled(), 10) && parseInt(this.isDependent(), 10)) {
                this.disabled(false);
            } else {
                this.disabled(true);
            }
        }
    });
});