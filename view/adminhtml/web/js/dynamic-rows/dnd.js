define([
    'jquery',
    'Magento_Ui/js/dynamic-rows/dnd'
], function($, Dnd){
    'use strict';

    return Dnd.extend({
        mouseupHandler: function (event) {
            this._super(event);
        },

        /**
         * Get dependency element
         *
         * @param {Object} curInstance - current element instance
         * @param {Number} position
         * @param {Object} row
         */
        getDepElement: function (curInstance, position, row) {
            var tableSelector = 'table.admin__dynamic-rows.nested-table tr',
                $table = $(row).parents('table').eq(0),
                $curInstance = $(curInstance),
                recordsCollection = $table.find('table').length ?
                    $table.find('tbody > tr').filter(function (index, elem) {
                        return !$(elem).parents(tableSelector).length;
                    }) :
                    $table.find('tbody > tr'),
                curInstancePositionTop = $curInstance.position().top,
                curInstancePositionBottom = curInstancePositionTop + $curInstance.height();

            if (position < 0) {
                return this._getDepElement(recordsCollection, 'before', curInstancePositionTop);
            } else if (position > 0) {
                return this._getDepElement(recordsCollection, 'after', curInstancePositionBottom);
            }
        }
    });
});