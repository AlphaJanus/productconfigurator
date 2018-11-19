define([
    "jquery",
    "Magento_Ui/js/form/components/insert-listing",
    'underscore'
], function ($, InsertListing, _) {
    'use strict';
    return InsertListing.extend({

        initConfig: function (config) {
            config.dataScope = "data.product.assign_configurator_option_grid";
            this._super(config);
        },

        /**
         * Updates externalValue, from selectionsProvider data
         * (which only stores data of the current page rows)
         *  + from already saved data
         *  so we can avoid request to server
         *
         * @param {Array} selected - ids of selected rows
         * @param {Object} rows
         */
        updateFromClientData: function (selected, rows) {
            var value,
                rowIds,
                valueIds = [],
                newValue = [],
                self = this;

            if (!selected || !selected.length) {
                this.setExternalValue([]);

                return;
            }

            value = this.externalValue();
            rowIds = _.pluck(rows, this.indexField);
            if(typeof(value) === 'undefined'){
                value = [];
            }
            $.each(value, function(index, item){
                valueIds[index] = _.pluck(item,'entity_id');
            });

            newValue[this.callerIndex] = [];
            selected.forEach(function(selectedId){
                if(_.contains(rowIds,selectedId)){

                    rows.forEach(function(row){
                        if(row[this.indexField] === selectedId) {
                            newValue[this.callerIndex].push(row);
                        }
                    }, this)
                }


            }, this);
            $.each(value, function(index, group){
                group.forEach(function(val){
                    if(_.contains(selected, val[self.indexField])) {
                        if(typeof(newValue[index]) === 'undefined') {
                            newValue[index] = [];
                        }
                        newValue[index].push(val);
                    }
                }, this);
            }, this);
            this.setExternalValue(newValue);
        },

        /**
         * Updates grid selections
         * every time, when extenalValue is updated,
         * so grid is re-selected according to externalValue updated
         * Also suppress dataLinks so import/export of selections will not activate each other in circle
         *
         * @param {Object} rows
         */
        updateSelections: function (rows) {
            var provider,
                ids = [],
                self = this;

            if (!this.dataLinks.exports || this.suppressDataLinks) {
                this.suppressDataLinks = false;
                this.initialExportDone = true;

                return;
            }

            provider = this.selections();

            if (!provider) {
                this.needInitialListingUpdate = true;

                return;
            }

            this.suppressDataLinks = true;
            provider.deselectAll();

            if (_.isString(rows)) {
                provider.selected([rows] || []);
            } else {
                if (rows.length) {
                    rows.forEach(function (items) {
                        ids = _.union(ids, (_.pluck(items || [], self.indexField)
                            .map(function (item) {
                                return item.toString();
                            })));
                    });
                }

                provider.selected(ids || []);
            }
            this.initialExportDone = true;
        },

        /**
         * Updates external filter (if externalFilterMode is on)
         * every time, when value is updated,
         * so grid is re-filtered to exclude or include selected rows only
         *
         * @param {Object} items
         */
        updateExternalFiltersModifier: function (items) {
            var provider,
                filter = {};

            if (!this.externalFilterMode) {
                return;
            }

            provider = this.selections();

            if (!provider) {
                this.needInitialListingUpdate = true;

                return;
            }

            filter[this.indexField] = {
                'condition_type': this.externalCondition,
                value: _.pluck(_.flatten(items), this.indexField)
            };
            this.set('externalFiltersModifier', filter);
        },

        /**
         * Check if the selected rows data can be taken from selectionsProvider data
         * (which only stores data of the current page rows)
         *  + from already saved data
         *
         * @param {Boolean} totalSelected - total rows selected (include rows that were filtered out)
         * @param {Array} selected - ids of selected rows
         * @param {Object} rows
         */
        canUpdateFromClientData: function (totalSelected, selected, rows) {
            var alreadySavedSelectionsIds = _.pluck(_.flatten(this.externalValue()), this.indexField),
                rowsOnCurrentPageIds = _.pluck(rows, this.indexField);

            return totalSelected === selected.length &&
                _.intersection(_.union(alreadySavedSelectionsIds, rowsOnCurrentPageIds), selected).length ===
                selected.length;
        },

        /**
         * Updates externalValue, from ajax request to grab selected rows data
         *
         * @param {Object} selections
         * @param {String} itemsType
         *
         * @returns {Object} request - deferred that will be resolved when ajax is done
         */
        updateFromServerData: function (selections, itemsType) {
            var filterType = selections && selections.excludeMode ? 'nin' : 'in',
                selectionsData = {},
                items = {},
                request;

            _.extend(selectionsData, this.params || {}, selections.params);

            if (selections[itemsType] && selections[itemsType].length) {
                selectionsData.filters = {};
                selectionsData['filters_modifier'] = {};
                selectionsData['filters_modifier'][this.indexField] = {
                    'condition_type': filterType,
                    value: selections[itemsType]
                };
            }

            selectionsData.paging = {
                notLimits: 1
            };

            request = this.requestData(selectionsData, {
                method: this.requestConfig.method
            });
            request
                .done(function (data) {
                    items[this.callerIndex] = data.items;
                    this.setExternalValue(items || data);
                    this.loading(false);
                }.bind(this))
                .fail(this.onError);

            return request;
        },

    });
});