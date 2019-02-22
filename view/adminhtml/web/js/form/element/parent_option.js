define([
    'jquery',
    'underscore',
    'uiRegistry',
    'mageUtils',
    'Magento_Ui/js/form/element/ui-select'
], function ($, _, registry, utils, Select) {
    'use strict';

    /**
     * Preprocessing options list
     *
     * @param {Array} nodes - Options list
     *
     * @return {Object} Object with property - options(options list)
     *      and cache options with plain and tree list
     */
    function parseOptions(nodes)
    {
        var value,
            cacheNodes,
            copyNodes;

        nodes = setProperty(nodes, 'optgroup');
        copyNodes = JSON.parse(JSON.stringify(nodes));
        cacheNodes = flattenCollection(copyNodes, 'optgroup');

        nodes = _.map(nodes, function (node) {
            value = node.value;

            if (value == null || value === '') {
                if (_.isUndefined(node.caption)) {
                    node.caption = node.label;
                    node.is_active = '0';
                }
                return node;
            } else {
                return node;
            }
        });

        return {
            options: _.compact(nodes),
            cacheOptions: {
                plain: _.compact(cacheNodes),
                tree: _.compact(nodes)
            }
        };
    }

    /**
     * Set levels to options list
     *
     * @param {Array} array - Property array
     * @param {String} separator - Level separator
     * @param {Number} level - Starting level
     * @param {String} path - path to root
     *
     * @returns {Array} Array with levels
     */
    function setProperty(array, separator, level, path)
    {
        var i = 0,
            length,
            nextLevel,
            nextPath;

        array = _.compact(array);
        length = array.length;
        level = level || 0;
        path = path || '';

        for (i; i < length; i++) {
            if (array[i]) {
                _.extend(array[i], {
                    level: level,
                    path: path
                });
            }

            if (array[i].hasOwnProperty(separator)) {
                nextLevel = level + 1;
                nextPath = path ? path + '.' + array[i].label : array[i].label;
                setProperty.call(this, array[i][separator], separator, nextLevel, nextPath);
            }
        }

        return array;
    }

    /**
     * Processing options list
     *
     * @param {Array} array - Property array
     * @param {String} separator - Level separator
     * @param {Array} created - list to add new options
     *
     * @return {Array} Plain options list
     */
    function flattenCollection(array, separator, created)
    {
        var i = 0,
            length,
            childCollection;

        array = _.compact(array);
        length = array.length;
        created = created || [];

        for (i; i < length; i++) {
            created.push(array[i]);

            if (array[i].hasOwnProperty(separator)) {
                childCollection = array[i][separator];
                delete array[i][separator];
                flattenCollection.call(this, childCollection, separator, created);
            }
        }

        return created;
    }

    return Select.extend({
        defaults: {
            imports: {
                groups: '${ $ .provider }:data.product.configurator_option_groups',
                currentGroup: '${ $ .provider }:${ $ .dataScope.split(\'.assigned_configurator_options\')[0] }',
                currentRecord: '${ $ .provider }:${ $ .parentScope }',
                parent_option: 'currentRecord.parent_option'
            },
            listens: {
                parent_option: 'updateModalProvider'
            }
        },

        tmpl: {
            id: '${ $.$data.id }',
            name: '',
            values: [],
        },

        updateOptions: function () {
            var self = this,
                optionsWithVariants = [
                    'select',
                    'radio',
                    'image'
                ],
                groups = _.filter(this.groups, function (group) {
                    return !_.isUndefined(self.currentGroup) && ~~group.position <= self.currentGroup.position;
                });
            groups.forEach(function (group, index, groups) {
                group.value = 'g.' + group.group_id;
                group.label = group.name;
                group.parent = 'root';
                if (_.isArray(group.assigned_configurator_options) || _.isObject(group.assigned_configurator_options)) {
                    var assignedOptions = group.assigned_configurator_options;
                    var grid = _.findWhere(registry.filter("index=assigned_configurator_options"), {groupIndex: group.record_id.toString()});
                    if (self.currentRecord.group_id === group.group_id) {
                        assignedOptions = _.filter(assignedOptions, function (option) {
                            var elem = _.findWhere(grid.elems(), {recordId: option.record_id});
                            return elem.position < self.currentRecord.position
                        });
                    }
                    if (!assignedOptions.length) {
                        groups.splice(index, 1);
                    }
                    assignedOptions = _.filter(assignedOptions, function (option) {
                        return optionsWithVariants.indexOf(option.type) !== -1;
                    });
                    assignedOptions.forEach(function (assignedOption) {
                        assignedOption.label = assignedOption.name;
                        assignedOption.value = assignedOption.configurator_option_id;
                        assignedOption.parent = group.value;
                    });
                    group.optgroup = assignedOptions;
                }
            });
            var result = parseOptions(groups);
            this.cacheOptions = result.cacheOptions;
            this.options(result.options);
        },

        updateModalProvider: function (data) {
            var self = this,
                dependencies = this.currentRecord.dependencies;
            if (!_.isArray(dependencies)) {
                dependencies = [];
            }
            if (data) {
                data.forEach(function (parentOptionId) {
                    if (parentOptionId) {
                        if (!_.findWhere(dependencies, {id: parentOptionId})) {
                            dependencies.push(utils.template(self.tmpl, {id: parentOptionId}))
                        }
                    }
                });
                dependencies = _.filter(dependencies, function (dep) {
                    return data.indexOf(dep.id) !== -1;
                });


                this.currentRecord.values.forEach(function (value) {
                    data.forEach(function (parentOptionId) {
                        if (parentOptionId) {
                            if (!_.isArray(value.dependencies)) {
                                value.dependencies = [];
                            }
                            if (!_.findWhere(value.dependencies, {id: parentOptionId})) {
                                value.dependencies.push(utils.template(self.tmpl, {id: parentOptionId}))
                            }
                        }
                    });
                    value.dependencies = _.filter(value.dependencies, function (dep) {
                        return data.indexOf(dep.id) !== -1;
                    });
                });
                this.source.set(this.parentScope + '.values' , this.currentRecord.values);
            }
            this.source.set(this.parentScope + '.dependencies', dependencies);
        }
    })
});