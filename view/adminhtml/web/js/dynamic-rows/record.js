define([
    'Magento_Ui/js/dynamic-rows/record'
], function(Record){
    'use strict';
    return Record.extend({
        /**
         * Get label for collapsible header
         *
         * @param {String} label
         *
         * @returns {String}
         */
        getLabel: function () {
            this.label(this.data().name)

            return this.label();
        },
    });
});