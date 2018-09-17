define([
    "jquery",
    'ko'
], function ($, ko) {
    'use strict';

    $.widget('mage.imageField',{
        options: {
        },

        value: ko.observable(),

        _init: function () {
            var self = this;
            $(this.element.context).find('.value').on('click', this.updateValue.bind(this));
            var disabledObserver = new MutationObserver(function(mutations){
                mutations.forEach(function(mutation){
                    if (mutation.attributeName === "disabled") {
                        self.updateImages(mutation.target);
                    }
                })
            });
            var config = { attributes: true };
            // Start observing myElem
            $(this.element.context).find('select option').each(function(index, el){
                disabledObserver.observe(el, config);
            });
            $(this.element.context).find('select').on('change, visibilityChanged', this.updateSelected.bind(this));
        },

        updateValue: function (event) {
            var valueElement = $(event.currentTarget);
            $(this.element.context).find('.value').removeClass('selected');
            valueElement.addClass('selected');
            this.value(valueElement.data('value'));
            $(this.element.context).find('select option[data-id="' + valueElement.attr('id') + '"]').prop('selected',true)
            $(this.element.context).find('select').trigger('change');
            return this;
        },

        updateImages: function(option){
            if($(option).prop('disabled')){
                $(this.element.context).find('.value[data-id="' + option.value + '"]').addClass('hide');
            } else {
                $(this.element.context).find('.value[data-id="' + option.value + '"]').removeClass('hide');
            }
        },

        updateSelected: function(event){
            $(this.element.context).find('.value').removeClass('selected');
            $(this.element.context).find('.value[data-id="' + event.target.value + '"]').addClass('selected');
        }
    });
    return $.mage.imageField;
});