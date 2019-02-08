define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/multiselect'
], function (_, registry, Multiselect) {
    'use strict';

    return Multiselect.extend({
        defaults: {
            links: {
                option: '${ $ .provider }:${ $ .parentScope }',
                assignedOptionsGroups: '${ $ .provider }:data.product.assigned_configurator_options'
            }
        },

        setInitialValue: function () {
            this.refreshVariants();
            return this._super();
        },
        refreshVariants: function () {
            var option,
                opt,
                variants,
                options = [],
                self = this;
            this.assignedOptionsGroups.some(function (group) {
                option = _.findWhere(group, {configurator_option_id: self.option.id});
                return !_.isUndefined(option);
            });
            variants = option.values;
            this.source.set(this.parentScope + '.name', option.name);
            variants.forEach(function (item) {
                opt = {
                    value: item.value_id,
                    label: item.title,
                    labeltitle: item.title
                };
                options.push(opt);
            });
            self.setOptions(options);
        }
    });
});