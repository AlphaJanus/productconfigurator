define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
],function(_, registry, Select){
    'use strict';

    /**
     * Parses incoming options, considers options with undefined value property
     *     as caption
     *
     * @param  {Array} nodes
     * @return {Object}
     */
    function parseOptions(nodes, captionValue) {
        var caption,
            value;

        nodes.sort(compare);
        nodes = _.map(nodes, function (node) {
            value = node.value;

            if (value === null || value === captionValue) {
                if (_.isUndefined(caption)) {
                    caption = node.label;
                }
            } else {
                return node;
            }
        });

        return {
            options: _.compact(nodes),
            caption: _.isString(caption) ? caption : false
        };
    }

    function compare(a,b) {
        if (a.position < b.position)
            return -1;
        if (a.position > b.position)
            return 1;
        return 0;
    }

    /**
     * Recursively set to object item like value and item.value like key.
     *
     * @param {Array} data
     * @param {Object} result
     * @returns {Object}
     */
    function indexOptions(data, result) {
        var value;

        result = result || {};

        data.forEach(function (item) {
            value = item.value;

            if (Array.isArray(value)) {
                indexOptions(value, result);
            } else {
                result[value] = item;
            }
        });

        return result;
    }

    return Select.extend({
        defaults: {
            imports: {
                assignedOptions: '${ $ .provider }:data.product.assigned_configurator_options',
                rowData: '${ $ .provider }:${ $ .parentScope }'
            }
        },

        initialize: function(){
            this._super();
            return this;
        },

        /**
         * Sets initial value of the element and subscribes to it's changes.
         *
         * @returns {Abstract} Chainable.
         */
        setInitialValue: function () {
            this.initialValue = this.getInitialValue();
            if (typeof(this.initialValue) === 'undefined') {
                this.initialValue = this.rowData.parent_option;
            }

            if (this.value.peek() !== this.initialValue) {
                this.value(this.initialValue);
            }
            this.on('value', this.onUpdate.bind(this));
            this.isUseDefault(this.disabled());

            return this;
        },

        initObservable: function () {
            this._super();
            var self = this,
                rows = registry.get('product_form.product_form.configurator_options_group.assigned_configurator_options'),
                dnd = registry.get('product_form.product_form.configurator_options_group.assigned_configurator_options_dnd');
            rows.on('sortOrderChanged', function(data){
                self.updateOptions(data);
            });
            dnd.on('sortOrderChanged', function(){
                self.updateOptions();
            });

            return this;
        },

        updateOptions: function(data){

            var option,
                component,
                value,
                self = this;

            if(data && data.elem.index){
                if(self.assignedOptions[data.elem.index].record_id !== self.rowData.record_id){
                    return;
                }

                if(self.assignedOptions[data.elem.index].position === data.position && self.options().length !== 0){
                    return;
                }
            }
            if(Array.isArray(self.assignedOptions)) {
                self.assignedOptions.forEach(function (row, index) {
                    if(data && data.elem.index !== index.toString()) {
                        return;
                    }
                    var options = [];
                    component = registry.get("product_form.product_form.configurator_options_group.assigned_configurator_options." + index + ".dependency_container.parent_option");
                    self.assignedOptions.forEach(function (item) {
                        if ((item.configurator_option_id !== row.configurator_option_id) &&
                            (parseInt(item.position) < parseInt(row.position))) {
                            option = {
                                value: item.configurator_option_id,
                                label: item.name,
                                labeltitle: item.name,
                                position: parseInt(item.position)
                            };
                            options.push(option);
                        }
                    });
                    if (typeof(component) !== 'undefined') {
                        value = component.value();
                        component.setOptions(options);
                        if (typeof(_.findWhere(component.options(), {value: value})) !== "undefined") {
                            component.value(value);
                        }
                    }
                });
            }
        },

        /**
         * Sets 'data' to 'options' observable array, if instance has
         * 'customEntry' property set to true, calls 'setHidden' method
         *  passing !options.length as a parameter
         *
         * @param {Array} data
         * @returns {Object} Chainable
         */
        setOptions: function (data) {
            var captionValue = this.captionValue || '',
                result = parseOptions(data, captionValue),
                isVisible;

            this.indexedOptions = indexOptions(result.options);

            this.options(result.options);

            if (!this.caption()) {
                this.caption(result.caption);
            }

            if (this.customEntry) {
                isVisible = !!result.options.length;

                this.setVisible(isVisible);
                this.toggleInput(!isVisible);
            }

            return this;
        },
    });
});