/*
 * Big Grid Plugin - Filter Bar
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    // translate token function
    function _T(s) { return s; }

    biggrid.plugin.filterbar = function(options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace
        var uid = this._generateUid(),
            ns = 'bgg-filterbar-'+uid;

        var me = this,
            opts = $.extend({}, biggrid.plugin.filterbar.defaults, options),
            grid = undefined,
            grid_model = undefined,
            grid_filter_strs = [],
            $el = $(),
            $grid_el = $(),
            $grid_utility_bar = $(),
            $filterbar_btn_filter_operator = $(),
            $filterbar_input_filter = $(),
            $filterbar_btn_filter_submit = $();

// -- public methods --

        this.init = function(biggrid)
        {
            grid = biggrid;
            grid_model = grid.getModel();
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
            return 'filterbar';
        }

        this.register = function()
        {
            if (grid instanceof $.biggrid)
                grid.registerPlugin('filterbar', me);

            return me;
        }

        this.unregister = function()
        {
            if (grid instanceof $.biggrid)
                grid.unregisterPlugin('filterbar');

            return me;
        }

        this.focusFilterInput = function()
        {
            $filterbar_input_filter.focusEnd();
            return me;
        }

        this.showFilterBar = function(default_val, focus_input)
        {
            $el.removeClass('hidden');

            // set filter bar input text
            if (default_val !== undefined)
                $filterbar_input_filter.val(default_val);
                 else
                $filterbar_input_filter.val('');

            if (focus_input === true)
                $filterbar_input_filter.focusEnd();

            return me;
        }

        this.hideFilterBar = function()
        {
            $el.addClass('hidden');

            // empty out filter bar input text
            $filterbar_input_filter.val('');

            return me;
        }

// -- private methods --

        this._initEvents = function()
        {
            me._uninitEvents();

            $filterbar_input_filter.on('keydown.'+ns, me.onFilterInputKeydown);
            $filterbar_btn_filter_submit.on('click.'+ns, me.onFilterSubmitButtonClick);

            $grid_el.on('selectcolumn.biggrid.'+ns, me.onGridColumnSelectionChange);
            $grid_el.on('deselectcolumn.biggrid.'+ns, me.onGridColumnSelectionChange);
            $grid_el.on('clearcolumnselection.biggrid.'+ns, me.onGridColumnSelectionChange);

            return me;
        }

        this._uninitEvents = function()
        {
            $filterbar_input_filter.off('.'+ns);
            $filterbar_btn_filter_submit.off('.'+ns);
            $grid_el.off('.'+ns);

            return me;
        }

        this._render = function()
        {
            $el = $(opts.markup);
            $filterbar_btn_filter_operator = $el.find('.bgg-filterbar-btn-filter-operator').buttonSelect();
            $filterbar_input_filter = $el.find('.bgg-filterbar-input-filter');
            $filterbar_btn_filter_submit = $el.find('.bgg-filterbar-btn-filter-submit');

            if (opts.appendTo !== undefined)
                $(opts.appendTo).append($el);
                 else
                $grid_utility_bar.append($el.addClass('hidden bgg-utility-bar-inner'));

            return me;
        }

        this._doFilter = function()
        {
            var selected_cols = grid.getSelectedColumns(true),
                filter_operator = $filterbar_btn_filter_operator.data('val'),
                filter_val = $filterbar_input_filter.val();

            if (selected_cols.length == 0)
                return me;

            if (filter_val.length == 0)
                return me;

            // we must include the type here so the filter knows
            // how to differentiate between column types
            var filter_items = _.pickMany(selected_cols, 'name', 'type');

            filter_items = _.map(filter_items, function(filter_item) {
                return {
                    name: filter_item.name,
                    type: filter_item.type,
                    operator: filter_operator,
                    value: filter_val
                };
            });

            // clear out the filter input
            $filterbar_input_filter.val('');

            // fire the 'sort.biggrid' event
            $grid_el.trigger($.Event('filter.biggrid'), [filter_items, 'or']);

            return me;
        }

// -- event handlers --

        this.onFilterInputKeydown = function(evt)
        {
            if (evt.which == 13)
            {
                evt.preventDefault();
                me._doFilter();
            }
        }

        this.onFilterSubmitButtonClick = function(evt)
        {
            me._doFilter();
        }

        this.onGridColumnSelectionChange = function(evt)
        {
            var col_count = grid.getSelectedColumnCount();

            if (col_count > 0)
                me.showFilterBar();
                 else
                me.hideFilterBar();
        }
    }

    biggrid.plugin.filterbar.defaults = {
        appendTo: undefined,
        markup: '' +
            '<div class="bgg-filterbar pull-right">' +
                '<form>' +
                    '<div class="input-group input-group-sm">' +
                        '<div class="input-group-btn">' +
                            '<button type="button" class="btn btn-default bgg-filterbar-btn-filter-operator dropdown-toggle tipsy" data-toggle="dropdown" data-placement="left">' +
                                '<span class="btn-text">&nbsp;</span>' +
                            '</button>' +
                            '<ul class="dropdown-menu bgg-dropdown-menu bgg-caret-dropdown-menu" style="margin-left: -6px">' +
                                '<li selected><a data-anchor="btn" href="#" data-val="like"><span class="btn-text">&asymp;</span> : <span class="btn-title"><i18n>Contains</i18n></span></a></li>' +
                                '<li><a data-anchor="btn" href="#" data-val="eq"><span class="btn-text">=</span> : <span class="btn-title"><i18n>Equal to</i18n></span></a></li>' +
                                '<li><a data-anchor="btn" href="#" data-val="neq"><span class="btn-text">&ne;</span> : <span class="btn-title"><i18n>Not equal to</i18n></span></a></li>' +
                                '<li><a data-anchor="btn" href="#" data-val="gt"><span class="btn-text">&gt;</span> : <span class="btn-title"><i18n>Greater than</i18n></span></a></li>' +
                                '<li><a data-anchor="btn" href="#" data-val="gte"><span class="btn-text">&ge;</span> : <span class="btn-title"><i18n>Greater than or equal to</i18n></span></a></li>' +
                                '<li><a data-anchor="btn" href="#" data-val="lt"><span class="btn-text">&lt;</span> : <span class="btn-title"><i18n>Less than</i18n></span></a></li>' +
                                '<li><a data-anchor="btn" href="#" data-val="lte"><span class="btn-text">&le;</span> : <span class="btn-title"><i18n>Less than or equal to</i18n></span></a></li>' +
                            '</ul>' +
                        '</div>' +
                        '<input type="text" class="form-control bgg-filterbar-input-filter" placeholder="'+_T('Filter value...')+'" />' +
                        '<span class="input-group-btn">' +
                            '<button type="button" class="btn btn-default bgg-filterbar-btn-filter-submit"><i18n>Go</i18n></button>' +
                        '</span>' +
                    '</div>' +
                '</form>' +
            '</div>'
    };

})(jQuery, window, document);
