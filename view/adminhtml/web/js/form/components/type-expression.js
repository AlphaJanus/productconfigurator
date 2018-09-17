define([
    'jquery',
    "Magento_Ui/js/form/components/group",
    "uiRegistry",
], function($, Component, registry){
    'use strict';

    return Component.extend({

        defaults: {
            imports: {
                optionType: '${ $ .provider }:data.option.type'
            }
        },

        /**
         * execution starts
         */
        initialize: function () {
            var self = this;
            var overriden = false;
            var isRequiredSwitch = registry.get("configurator_option_form.configurator_option_form.general.container_is_required.is_required");
            this._super();
            self.initSubscribers();
            self.visible(self.optionType() === 'expression');
            if(!self.visible) {
                isRequiredSwitch.checked(false);
                isRequiredSwitch.disabled(false);
            } else {
                if(self.optionType() === 'expression'){
                    isRequiredSwitch.checked(true);
                }
            }
            $("#save_and_continue,#save").on('click',function(e){
                if (overriden) {
                    overriden = false; // reset flag
                    return true; // let the event bubble away
                }
                e.preventDefault();

                self.overrideValidation();

                overriden = true; // set flag
                $(this).trigger('click');
            });
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
                    var visible = optionType === 'expression';
                    var isRequiredSwitch = registry.get("configurator_option_form.configurator_option_form.general.container_is_required.is_required");
                    self.visible(visible);
                    $.each(self.elems(), function(index, elem){
                        elem.visible(self.visible());
                    });

                    if(!visible) {
                        $.each(self.elems(), function (index, elem) {
                            elem.value(null);
                        });
                        isRequiredSwitch.checked(false);
                        isRequiredSwitch.disabled(false);
                    } else {
                        if(optionType === 'expression'){
                            isRequiredSwitch.checked(true);
                        }
                        isRequiredSwitch.disabled(true);
                    }
                }
            );
        },

        overrideValidation: function(){
            var self = this;
            $.each(self.elems(), function(index, elem){
                if(!self.visible) {
                    elem.reset();
                }
                elem.visible(self.visible());
            });
        }
    });
});