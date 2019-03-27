define([
    "jquery",
    "underscore",
    'mage/template',
    "configuratorOptions",
], function ($, _, mageTemplate) {
    'use strict';
    var globalOptions = {
        formSelector:  '#product_addtocart_form',
        optionTemplate: '<dt class="<%= data.option %>"><%= data.label %></dt>' +
            '<dd class="<%= data.type %>-value <% if (data.type === \'image\') {%><%= data.slug %><% } %> ' +
            '<%= data.option %>"><span><%= data.value %></span></dd>',
        dependencyConfig: null
    };

    $.widget('mage.selectionWidget', {
        options: globalOptions,

        _create: function createSelectionWidget()
        {
            var form = $(this.options.formSelector);
            if (form.data('mageConfiguratorOptions') &&
                form.configuratorOptions('option') &&
                form.configuratorOptions('option').dependencyConfig) {
                this._setOption('dependencyConfig', form.configuratorOptions('option').dependencyConfig);
                this._setOption('optionsSelector', form.configuratorOptions('option').optionsSelector);
                var options = $(this.options.optionsSelector, form);
                options.on('change', this._onOptionChanged.bind(this));
            }
        },
        _init: function initWidget()
        {
            $(this.options.optionsSelector).trigger('change');
        },
        _onOptionChanged: function onOptionChanged()
        {
            var optionId,
                parts,
                optionConfig,
                self = this,
                template = this.options.optionTemplate,
                options = $(this.options.optionsSelector);
            var content = "<dl>";
            template = mageTemplate(template);
            $.each(options, function (index, option) {
                var toTemplate,
                    value,
                    currentVal = $(option).val();
                parts = /^(configurator_options\[)(\d+)(\])$/.exec($(option).data('selector'));
                optionId = parts[2];
                optionConfig = self.getDependencyConfig(optionId);

                if (optionConfig.is_visible === "1" && optionConfig.type !=='static' && !_.isNull(currentVal)) {
                    switch (optionConfig.type) {
                        case 'text':
                        case 'expression':
                            value = $(option).val();
                            break;
                        case 'select':
                        case 'image':
                            var valObj = _.findWhere(optionConfig.values, {'value_id': $(option).val()});
                            value = valObj.title;
                            break;
                        default:
                            value = 'test';
                    }
                    toTemplate = {
                        data: {
                            label: optionConfig.name,
                            type: optionConfig.type,
                            slug: self.string_to_slug(value),
                            value: value,
                            option: self.string_to_slug(optionConfig.name)
                        }
                    };
                    content += template(toTemplate);
                }
            });
            content += '</dl>';
            self.element.html(content);
        },
        getDependencyConfig: function (optionId) {
            if (this.options) {
                return this.options.dependencyConfig[optionId];
            }
            return null;
        },
        string_to_slug: function (str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
            var to   = "aaaaeeeeiiiioooouuuunc------";
            for (var i=0, l=from.length; i<l; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            return str;
        }
    });

    return $.mage.selectionWidget;
});