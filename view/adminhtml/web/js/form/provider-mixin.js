define([
    'mage/utils/wrapper'
], function (wrapper) {
    'use strict';

    return function (provider) {
        provider.prototype.save = wrapper.wrap(provider.prototype.save, function (_super, options) {
            delete this.data.product.assigned_configurator_options;
            delete this.data.product.assign_configurator_option_grid;
            _super(options);
        });
        return provider;
    }
});