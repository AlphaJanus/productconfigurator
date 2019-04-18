define([
    'uiRegistry',
    'underscore',
    'Magento_Ui/js/dynamic-rows/dynamic-rows-grid'
], function (registry, _, dynamicRows) {
    'use strict';

    Object.byString = function (o, s) {
        s = s.replace(/\[(\w+)\]/g, '.$1'); // convert indexes to properties
        s = s.replace(/^\./, '');           // strip a leading dot
        var a = s.split('.');
        for (var i = 0, n = a.length; i < n; ++i) {
            var k = a[i];
            if (k in o) {
                o = o[k];
            } else {
                return;
            }
        }
        return o;
    };

    return dynamicRows.extend({
        switchDataScope: function () {
            this.links.recordData = this.depScope + '.dependencies';

            this.dataScope = this.depScope;
            this.dataProvider = this.depScope + '.dependencies';
            this.recordData(Object.byString(registry.get(this.provider), this.dataProvider));
            this.reload();
        },

        /**
         * Set data from recordData to insertData
         */
        setToInsertData: function () {
            var insertData = [],
                obj,
                tmpObj = {};

            if (this.recordData().length && !this.update) {
                _.each(this.recordData(), function (recordData) {
                    obj = {};
                    if (this.mappingSettings.enabled) {
                        _.each(this.map, function (prop, index) {
                            obj[index] = !_.isUndefined(recordData[prop]) ? recordData[prop] : '';
                        }, this);
                    } else {
                        obj = recordData;
                    }

                    if (this.mappingSettings.distinct) {
                        tmpObj[this.identificationDRProperty] = obj[this.identificationDRProperty];

                        if (_.findWhere(this.recordData(), tmpObj)) {
                            return false;
                        }
                    }
                    insertData.push(obj);
                }, this);

                if (insertData.length) {
                    this.source.set(this.dataProvider, insertData);
                }
            }
        },
    });
});