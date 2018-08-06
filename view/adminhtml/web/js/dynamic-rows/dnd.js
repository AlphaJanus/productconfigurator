define([
    'Magento_Ui/js/dynamic-rows/dnd'
], function(Dnd){
    'use strict';

    return Dnd.extend({
        mouseupHandler: function (event) {
            this._super(event);
            this.trigger('sortOrderChanged');
        }
    });
});