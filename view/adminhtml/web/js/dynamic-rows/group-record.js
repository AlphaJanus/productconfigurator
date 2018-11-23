define([
    'ko',
    'Magento_Ui/js/dynamic-rows/record'
], function(ko, Record){
    'use strict';
    return Record.extend({
        defaults: {
            opened: false,
            collapsible: true
        },
        opened: ko.observable(),

        /**
         * Initializes observable properties.
         *
         * @returns {Collapsible} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe('opened');

            return this;
        },

        /**
         * Toggles value of the 'opened' property.
         *
         * @returns {Collapsible} Chainable.
         */
        toggleOpened: function () {
            this.opened() ?
                this.close() :
                this.open();

            return this;
        },

        /**
         * Sets 'opened' flag to false.
         *
         * @returns {Collapsible} Chainable.
         */
        close: function () {
            if (this.collapsible) {
                this.opened(false);
            }

            return this;
        },

        /**
         * Sets 'opened' flag to true.
         *
         * @returns {Collapsible} Chainable.
         */
        open: function () {
            if (this.collapsible) {
                this.opened(true);
            }

            return this;
        }
    });
});