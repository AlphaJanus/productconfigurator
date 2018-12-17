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
            var disabledObserver = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.attributeName === "disabled") {
                        self.updateImages(mutation.target);
                    }
                })
            });
            var config = { attributes: true };
            // Start observing myElem
            $(this.element.context).find('select option').each(function (index, el) {
                disabledObserver.observe(el, config);
            });
            var selectEl = $('#' + this.options.select);
            selectEl.on('change', this.updateSelected.bind(this));
            selectEl.on('visibilityChanged', this.updateSelected.bind(this));
        },

        updateValue: function (event) {
            var valueElement = $(event.currentTarget);
            this.value(valueElement.data('value'));
            var selectEl = $('#' + this.options.select);
            selectEl.find('option[data-id="' + valueElement.attr('id') + '"]').prop('selected','selected');
            selectEl.val(valueElement.data('id').toString());
            selectEl.trigger('change');
            return this;
        },

        updateImages: function (option) {
            if ($(option).prop('disabled')) {
                $(this.element.context).find('.value[data-id="' + option.value + '"]').addClass('hide');
            } else {
                $(this.element.context).find('.value[data-id="' + option.value + '"]').removeClass('hide');
            }
        },

        updateSelected: function (event) {
            $(this.element.context).find('.value').removeClass('selected');
            $(this.element.context).find('.value[data-id="' + event.target.value + '"]').addClass('selected');
        }
    });
    return $.mage.imageField;
});