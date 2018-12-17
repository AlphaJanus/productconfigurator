define([
    'Magento_Ui/js/form/element/single-checkbox',
    'uiRegistry'
], function (Checkbox, registry) {
    'use strict';

    return Checkbox.extend({
        defaults: {
            clearing: false,
            parentContainer: '',
            parentSelections: '',
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super().
                observe('elementTmpl');

            return this;
        },

        /**
         * @inheritdoc
         */
        initConfig: function () {
            this._super();

            return this;
        },

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            if (this.prefer === 'radio' && this.checked() && !this.clearing) {
                this.clearValues();
            }

            this._super();
        },

        /**
         * Clears values in components like this.
         */
        clearValues: function () {
            var records = registry.get('configurator_option_form.configurator_option_form.general.container_values.values'),
                index = this.index,
                uid = this.uid;

            records.elems.each(function (record) {
                record.elems.filter(function (comp) {
                    return comp.index === index && comp.uid !== uid
                }).every(function (elem) {
                    elem.clearing = true;
                    elem.clear();
                    elem.clearing = false;
                });

            });
        }
    });
});
