define([
    "Magento_Ui/js/dynamic-rows/dynamic-rows-grid"
], function (Grid) {
    'use strict';

    return Grid.extend({

        initialize: function(){
            this._super();
            this.visible(this.recordData().length);
            return this;
        },
        initObservable: function(){
            this._super();

            this.observe(['recordData']);
            this.on('recordData', this.switchVisibility.bind(this));
            return this;
        },

        switchVisibility: function () {
            this.visible(this.recordData().length);
        }
    })
});