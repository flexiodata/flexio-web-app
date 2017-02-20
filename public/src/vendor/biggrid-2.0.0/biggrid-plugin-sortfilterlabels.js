/*
 * Big Grid Plugin - Sort/Filter Labels
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    // translate token function
    function _T(s) { return s; }

    biggrid.plugin.sortfilterlabels = function(options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace
        var uid = this._generateUid(),
            ns = 'bgg-sortfilterlabels-'+uid;

        var me = this,
            opts = $.extend({}, biggrid.plugin.sortfilterlabels.defaults, options),
            grid = undefined,
            $el = $(),
            $grid_el = $(),
            $grid_utility_bar = $();

// -- public methods --

        this.init = function(biggrid)
        {
            grid = biggrid;
            $grid_el = grid.getElement();
            $grid_utility_bar = $grid_el.find('.bgg-utility-bar');

            me._render();
            me._initEvents();

            return me;
        }

        this.destroy = function()
        {
            $el.remove();
            $el = $();
            return me._uninitEvents();
        }

        this.getName = function()
        {
            return 'sortfilterlabels';
        }

        this.register = function()
        {
            if (grid instanceof $.biggrid)
                grid.registerPlugin('sortfilterlabels', me);

            return me;
        }

        this.unregister = function()
        {
            if (grid instanceof $.biggrid)
                grid.unregisterPlugin('sortfilterlabels');

            return me;
        }

        this.refresh = function()
        {
            var sort = grid.getSort(),
                filter = grid.getFilter();

            var new_filter = [];

            // create new filter array to modify
            _.each(filter, function(item) {
                new_filter.push($.extend({}, item));
            });

            // create column string
            _.each(new_filter, function(item) {
                item.column_str = _.map(item.columns, 'name').join(_T(' or '));
            });

            $el.empty();
            $(opts.sortLabelTmpl).tmpl(sort).appendTo($el);
            $(opts.filterLabelTmpl).tmpl(new_filter).appendTo($el);

            $el.find('.label').css(opts.labelStyle);

            return me;
        }

// -- private methods --

        this._initEvents = function()
        {
            me._uninitEvents();

            $grid_el.on('sort.biggrid.'+ns, me.onGridSort);
            $grid_el.on('filter.biggrid.'+ns, me.onGridFilter);
            $el.on('click.'+ns, '.bgg-label-sort .close', me.onRemoveSort);
            $el.on('click.'+ns, '.bgg-label-filter .close', me.onRemoveFilter);

            return me;
        }

        this._uninitEvents = function()
        {
            $grid_el.off('.'+ns);
            $el.off('.'+ns);

            return me;
        }

        this._render = function()
        {
            $el = $(opts.markup);

            if (opts.appendTo !== undefined)
                $(opts.appendTo).append($el);
                 else
                $grid_utility_bar.append($el.addClass('bgg-utility-bar-inner'));

            return me;
        }

// -- event handlers --

        this.onGridSort = function(sort)
        {
            me.refresh();
        }

        this.onGridFilter = function(filter)
        {
            me.refresh();
        }

        this.onRemoveSort = function()
        {
            var sort = $(this).closest('.bgg-label-sort').tmplItem().data;
            grid.removeSort(sort, true, function() { $('.tooltip').remove(); });
        }

        this.onRemoveFilter = function()
        {
            var filter = $(this).closest('.bgg-label-filter').tmplItem().data;
            delete filter.column_str;

            grid.removeFilter(filter, true, function() { $('.tooltip').remove(); });
        }
    }

    biggrid.plugin.sortfilterlabels.defaults = {
        appendTo: undefined,
        markup: '' +
            '<span class="bgg-sortfilterlabels"></span>',
        labelStyle: {},
        sortLabelTmpl: '' +
            '<div>' +
                '<span class="label label-primary label-inline label-removable bgg-label-sort">' +
                    '{{if direction == "desc"}}' +
                        '<i18n>Sort Descending</i18n>:&nbsp;' +
                    '{{else}}' +
                        '<i18n>Sort Ascending</i18n>:&nbsp;' +
                    '{{/if}}' +
                    '${column}' +
                    '<a data-anchor="btn" href="#" class="close tipsy" title="'+_T('Remove this sort')+'" data-placement="right">&times;</a>' +
                '</span>' +
            '</div>',
        filterLabelTmpl: '' +
            '<div>' +
                '<span class="label label-primary label-inline label-removable bgg-label-filter">' +
                    '<i18n>Filter</i18n>:&nbsp;' +
                    '${column_str}&nbsp;' +
                    '{{if operator == "like"}}' +
                        '&asymp;' +
                    '{{else operator == "eq"}}' +
                        '=' +
                    '{{else operator == "neq"}}' +
                        '&ne;' +
                    '{{else operator == "gt"}}' +
                        '&gt;' +
                    '{{else operator == "gte"}}' +
                        '&ge;' +
                    '{{else operator == "lt"}}' +
                        '&lt;' +
                    '{{else operator == "lte"}}' +
                        '&le;' +
                    '{{/if}}' +
                    "&nbsp;'${value}'" +
                    '<a data-anchor="btn" href="#" class="close tipsy" title="'+_T('Remove this filter')+'" data-placement="right">&times;</a>' +
                '</span>' +
            '</div>'
    };

})(jQuery, window, document);
