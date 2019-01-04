define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/multiselect'
], function (_, registry, Multiselect) {
    'use strict';

    return Multiselect.extend({
        defaults: {
            imports: {
                rows: '${ $ .provider }:${ $ .dataScope.replace(/.[(0-9)]+.allowed_variants/, "") }',
                parentOption: '${ $ .provider }:${ $ .dataScope.replace(/.allowed_variants/, "") }.parent_option',
            }
        },

        initObservable: function () {
            this._super();
            this.observe(['rows', 'parentOption', 'isDependent', 'isEnabled']);
            this.on('parentOption', this.refreshVariants.bind(this));
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
                self.refreshVisible();
            } else {
                self.setOptions([]);
                self.refreshVisible();
            }

        },

        refreshVisible: function () {
            this.visible(!!this.parentOption());
        }
    });
});