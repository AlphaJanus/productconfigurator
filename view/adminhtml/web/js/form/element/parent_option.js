define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
],function($, _, registry, Select){
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