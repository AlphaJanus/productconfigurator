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
            $('#' + this.options.select).on('change', this.updateSelected.bind(this));
            $('#' + this.options.select).on('visibilityChanged', this.updateSelected.bind(this));
        },

        updateValue: function (event) {
            var valueElement = $(event.currentTarget);
            this.value(valueElement.data('value'));
            $('#' + this.options.select).find('option[data-id="' + valueElement.attr('id') + '"]').prop('selected','selected');
            $('#' + this.options.select).val(valueElement.data('id').toString());
            $('#' + this.options.select).trigger('change');
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