define([
    'jquery',
    "Magento_Ui/js/form/components/group"
], function ($, Component) {
    'use strict';

    return Component.extend({

        defaults: {
            imports: {
                optionType: '${ $ .provider }:data.option.type',
            }
        },

        /**
         * execution starts
         */
        initialize: function () {
            var self = this;
            this._super();
            self.initSubscribers();
            self.visible(self.optionType() === 'text');
        },

        /**
         * init observers
         */
        initObservable: function () {
            this._super().observe(
                'optionType'
            );

            return this;
        },

        /**
         * initialize subscribers
         */
        initSubscribers: function () {
            var self = this;

            //creating a knockout subscriber to observe any changes in the option type
            self.optionType.subscribe(
                function (optionType) {
                    var visible = optionType === 'text';
                    self.visible(visible);
                    if (!visible) {
                        $.each(self.elems(), function (index, elem) {
                            elem.value(null);
                        });
                    }
                }
            );
        },
    });
});