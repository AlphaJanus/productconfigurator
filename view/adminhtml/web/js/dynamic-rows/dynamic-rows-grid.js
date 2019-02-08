/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function ($, _, registry, dynamicRows) {
    'use strict';

    return dynamicRows.extend({
        defaults: {
            dataProvider: '',
            insertData: [],
            map: null,
            cacheGridData: [],
            deleteProperty: false,
            positionProvider: 'position',
            dataLength: 0,
            identificationProperty: 'id',
            identificationDRProperty: 'id',
            listens: {
                'insertData': 'processingInsertData',
                'recordData': 'initElements setToInsertData'
            },
            mappingSettings: {
                enabled: true,
                distinct: true
            },
            links: {
                groups: '${ $ .provider }:${ $ .parentScope }'
            },
            optionsParents: [],
            sorted: false
        },

        initConfig: function (config) {
            config.groupIndex = config.dataScope.replace(/data.product.configurator_option_groups./, "");
            config.links.insertData =  config.provider + ':data.product.assign_configurator_option_grid';
            config.links.recordData =  config.provider + ':data.product.' + config.index;
            config.dataProvider = 'data.product.assign_configurator_option_grid.' + config.groupIndex;
            return this._super(config)
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super();
            this.observe([
                    'insertData'
                ]);
            return this;
        },

        /**
         * Set data from recordData to insertData
         */
        setToInsertData: function () {
            var insertData = [],
                recData = this.recordData()[this.groupIndex] ? this.recordData()[this.groupIndex] : [],
                obj;

            if (!_.isArray(recData)) {
                recData = Object.values(recData);
            }

            if (recData.length && !this.update) {
                _.each(recData, function (recordData) {
                    obj = {};
                    obj[this.map[this.identificationProperty]] = recordData[this.identificationProperty];
                    insertData.push(obj);
                }, this);

                if (insertData.length) {
                    this.source.set(this.dataProvider, insertData);
                }
            } else {
                this.source.set(this.dataProvider, insertData) // This is workaround for deleting last record. Not sure. (By Andrew Stepanchuk)
            }
        },

        /**
         * Initialize children
         *
         * @returns {Object} Chainable.
         */
        initChildren: function () {
            this.getChildItems().forEach(function (data, index) {
                this.processingAddChild(data, this.startIndex + index, data[this.identificationDRProperty]);
            }, this);

            return this;
        },

        /**
         * Initialize elements from grid
         *
         * @param {Array} data
         *
         * @returns {Object} Chainable.
         */
        initElements: function (data) {
            data = data[this.groupIndex];
            if (!Array.isArray(data)) {
                data = Object.values(data);
            }
            var newData = this.getNewData(data);
            var insertData = this.insertData();

            this.parsePagesData(data);

            if (newData.length) {
                if (insertData[this.groupIndex] && insertData[this.groupIndex].length) {
                    this.processingAddChild(newData[0], data.length - 1, newData[0][this.identificationProperty]);
                }
            }

            return this;
        },

        /**
         * Delete record instance
         * update data provider dataScope
         *
         * @param {String|Number} index - record index
         * @param {String|Number} recordId
         */
        deleteRecord: function (index, recordId) {
            this.updateInsertData(recordId);
            var recordInstance,
                lastRecord,
                recordsData;

            if (this.deleteProperty) {
                recordsData = this.recordData();
                recordInstance = _.find(this.elems(), function (elem) {
                    return elem.index === index;
                });
                recordInstance.destroy();
                this.elems([]);
                this._updateCollection();
                this.removeMaxPosition();
                recordsData[this.groupIndex][recordInstance.index][this.deleteProperty] = this.deleteValue;
                this.recordData(recordsData);
                this.reinitRecordData();
                registry.get('product_form.product_form_data_source').data.product.configurator_option_groups[this.groupIndex].assigned_configurator_options = recordsData[this.groupIndex];
                this.reload();
            } else {
                this.update = true;

                if (~~this.currentPage() === this.pages()) {
                    lastRecord =
                        _.findWhere(this.elems(), {
                            index: this.startIndex + this.getChildItems().length - 1
                        }) ||
                        _.findWhere(this.elems(), {
                            index: (this.startIndex + this.getChildItems().length - 1).toString()
                        });

                    lastRecord.destroy();
                }

                this.removeMaxPosition();
                recordsData = this._getDataByProp(recordId);
                this._updateData(recordsData);
                this.update = false;
            }

            this._reducePages();
            this._sort();
        },

        /**
         * Updates insertData when record is deleted
         *
         * @param {String|Number} recordId
         */
        updateInsertData: function (recordId) {
            var insertData = this.insertData();
            var data = this.getElementData(insertData[this.groupIndex], recordId),
                prop = this.map[this.identificationDRProperty];

            insertData[this.groupIndex] = _.reject(this.source.get(this.dataProvider), function (recordData) {
                return ~~recordData[prop] === ~~data[prop];
            }, this);
            this.insertData(insertData);
        },

        /**
         * Find data object by index
         *
         * @param {Array} array - data collection
         * @param {Number} index - element index
         * @param {String} property - to find by property
         *
         * @returns {Object} data object
         */
        getElementData: function (array, index, property) {
            var obj = {},
                result;

            property ? obj[property] = index : obj[this.map[this.identificationDRProperty]] = index;
            result = _.findWhere(array, obj);

            if (!result) {
                property ?
                    obj[property] = index.toString() :
                    obj[this.map[this.identificationDRProperty]] = index.toString();
            }

            result = _.findWhere(array, obj);

            return result;
        },

        /**
         * Processing pages before addChild
         *
         * @param {Object} ctx - element context
         * @param {Number|String} index - element index
         * @param {Number|String} prop - additional property to element
         */
        processingAddChild: function (ctx, index, prop) {
            if (this._elems.length > this.pageSize) {
                return false;
            }

            this.showSpinner(true);
            this.addChild(ctx, index, prop);
        },

        /**
         * Contains old data with new
         *
         * @param {Array} data
         *
         * @returns {Array} changed data
         */
        getNewData: function (data) {
            var changes = [],
                tmpObj = {};

            if (data.length !== this.relatedData.length) {
                _.each(data, function (obj) {
                    tmpObj[this.identificationDRProperty] = obj[this.identificationDRProperty];

                    if (!_.findWhere(this.relatedData, tmpObj)) {
                        changes.push(obj);
                    }
                }, this);
            }

            return changes;
        },

        /**
         * Processing insert data
         *
         */
        processingInsertData: function (data) {
            var changes,
                obj = {};

            if (typeof(data) === "undefined") {
                return false;
            }

            data = data[this.groupIndex] ? data[this.groupIndex] : [];

            changes = this._checkGridData(data);
            this.cacheGridData = data;

            if (changes.length) {
                obj[this.identificationDRProperty] = changes[0][this.map[this.identificationProperty]];

                if (_.findWhere(this.recordData(), obj)) {
                    return false;
                }

                changes.each(function (changedObject) {
                    this.mappingValue(changedObject);
                }, this);
            }
        },

        /**
         * Mapping value from grid
         *
         * @param {Array} data
         */
        mappingValue: function (data) {
            var obj = {},
                recData = this.recordData()[this.groupIndex] ? this.recordData()[this.groupIndex] : [],
                tmpObj = {};

            if (!Array.isArray(recData)) {
                recData = Object.values(recData);
            }

            if (this.mappingSettings.enabled) {
                _.each(this.map, function (prop, index) {
                    obj[index] = !_.isUndefined(data[prop]) ? data[prop] : '';
                }, this);
            } else {
                obj = data;
            }

            if (this.mappingSettings.distinct) {
                tmpObj[this.identificationDRProperty] = obj[this.identificationDRProperty];

                if (_.findWhere(this.recordData()[this.groupIndex], tmpObj)) {
                    return false;
                }
            }

            if (!obj.hasOwnProperty(this.positionProvider)) {
                this.setMaxPosition();
                obj[this.positionProvider] = this.maxPosition;
            }

            this.source.set(this.dataScope + '.' + this.index + '.' + recData.length, obj);
            this.source.set('data.product.' + this.index + '.' + this.groupIndex + '.' + recData.length, obj);

        },

        /**
         * Check changed records
         *
         * @param {Array} data - array with records data
         * @returns {Array} Changed records
         */
        _checkGridData: function (data) {
            var cacheLength = this.cacheGridData.length,
                curData = data.length,
                max = cacheLength > curData ? this.cacheGridData : data,
                changes = [],
                obj = {};

            max.each(function (record, index) {
                obj[this.map[this.identificationDRProperty]] = record[this.map[this.identificationDRProperty]];

                if (!_.where(this.cacheGridData, obj).length) {
                    changes.push(data[index]);
                }
            }, this);

            return changes;
        },

        sort: function (position, elem) {
            var that = this,
                sorted,
                updatedCollection;

            if (this.elems().filter(function (el) {
                return el.position || el.position === 0;
            }).length !== this.getChildItems().length) {
                return false;
            }

            if (!elem.containers.length) {
                registry.get(elem.name, function () {
                    that.sort(position, elem);
                });
                return false;
            }

            sorted = this.elems().sort(function (propOne, propTwo) {
                return ~~propOne.position - ~~propTwo.position;
            });

            updatedCollection = this.updatePosition(sorted, position, elem.name);
            this.elems(updatedCollection);
            if (this._isSortComplete(sorted)) {
                /*if ((!this.hasChanged() && elem.data().record_id === this._getLastRecordId()) ||     uncomment for better performance, but have some bugs
                    (this.hasChanged() && elem.data().record_id !== this._getLastRecordId())) {*/
                    var parentOptionPromise = registry.promise('index = parent_option');
                    parentOptionPromise.then(
                        result => this.updateParentOptions()
                    );
                /*}*/ //uncomment for better performance, but have some bugs
            }

        },

        updateParentOptions: function () {
            var parentOptions = registry.filter('index = parent_option');
            parentOptions.forEach(function (item) {
                item.updateOptions();
            });
        },

        _isSortComplete: function (collection) {
            var positions = _.pluck(collection, 'position').map(Number);
            return _.uniq(positions).length === this.getChildItems().length
        },

        _getLastRecordId: function () {
            var lastId = null,
                index = this.groups.length,
                lastGroupOpts = [];
            do {
                --index;
                lastGroupOpts = (this.groups[index].assigned_configurator_options) ?
                    this.groups[index].assigned_configurator_options : [];
            } while (!lastGroupOpts.length && index>=0);
            if (lastGroupOpts.length) {
                lastId = lastGroupOpts.last().record_id;
            }
            return lastId;
        },

        /**
         * Checking loader visibility
         *
         * @param {Array} elems
         */
        checkSpinner: function (elems) {
            var recData = this.recordData()[this.groupIndex] ? this.recordData()[this.groupIndex] : [];
            this.showSpinner(!(!recData.length || elems && elems.length === this.getChildItems().length));
        },

        /**
         * Reinit record data in order to remove deleted values
         *
         * @return void
         */
        reinitRecordData: function () {
            var recData = this.recordData();
            recData[this.groupIndex] = _.filter(this.recordData()[this.groupIndex], function (elem) {
                return elem && elem[this.deleteProperty] !== this.deleteValue;
            }, this);
            this.recordData(recData);
        },

        /**
         * Get items to rendering on current page
         *
         * @returns {Array} data
         */
        getChildItems: function (data, page) {
            var dataRecord = data || this.relatedData,
                startIndex;

            this.startIndex = (~~this.currentPage() - 1) * this.pageSize;

            startIndex = page || this.startIndex;

            return dataRecord.slice(startIndex, this.startIndex + this.pageSize);
        },

        /**
         * Set initial property to records data
         *
         * @returns {Object} Chainable.
         */
        setInitialProperty: function () {
            var recordData = this.recordData();
            if (_.isArray(recordData[this.groupIndex])) {
                recordData[this.groupIndex].each(function (data, index) {
                    this.source.set(this.dataScope + '.' + this.index + '.' + index + '.initialize', true);
                }, this);
            }

            return this;
        },
    });
});
