define([
    "uiRegistry",
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function (registry, Rows) {
    'use strict';

    return Rows.extend({

        /**
         * Filters out deleted items from array
         *
         * @param {Array} data
         *
         * @returns {Array} filtered array
         */
        arrayFilter: function (data) {
            var prop;

            /*eslint-disable no-loop-func*/
            data.forEach(function (elem) {
                for (prop in elem) {
                    if (_.isArray(elem[prop])) {
                        elem[prop] = _.filter(elem[prop], function (elemProp) {
                            return typeof(elemProp) !== 'undefined' && elemProp[this.deleteProperty] !== this.deleteValue;
                        }, this);

                        elem[prop].forEach(function (elemProp) {
                            if (_.isArray(elemProp)) {
                                elem[prop] = this.arrayFilter(elemProp);
                            }
                        }, this);
                    }
                }
            }, this);

            /*eslint-enable no-loop-func*/

            return data;
        },

        deleteRecord: function (index, recordId) {
            delete registry.get("product_form.product_form_data_source").data.product.assigned_configurator_options[index];
            this._super(index, recordId);
        }
    });
});