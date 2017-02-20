/*
 * Big Grid Plugin - Column List
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    // translate token function
    function _T(s) { return s; }

    biggrid.plugin.columnlist = function(options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace
        var uid = this._generateUid(),
            ns = 'bgg-collist-'+uid;

        var me = this,
            opts = $.extend({}, biggrid.plugin.columnlist.defaults, options),
            grid = undefined,
            grid_opts = undefined,
            grid_columns = [],
            item_selector = undefined,
            search_query = '',
            sort_order = '',
            selected_items = [], // only applies to 'toggle' and 'sticky' selection mode
            $el = $(),
            $grid_el = $(),
            $list_el = $(),
            $search_input = $();

        var allowed_sort_orders = [
            'natural',
            'view',
            'ascending',
            'descending'
        ];

// -- public methods --

        this.init = function(biggrid)
        {
            grid = biggrid;
            grid_opts = grid.getOptions();
            $grid_el = grid.getElement();

            me._render();
            me._initEvents();

            /*
            // let the column drag reorder plugin for this grid
            // know that this column list is a valid drag source
            if (opts.isColumnDragAllowed === true && opts.isColumnReorderDragSource === true)
            {
                if (grid.hasPlugin('columndragreorder'))
                    grid.getPlugin('columndragreorder').addDragSource(ns);
            }
            */

            return me;
        }

        this.destroy = function()
        {
            $el.remove();
            $el = $();

            me._uninitEvents();

            if (me._hasItemSelector())
                item_selector.uninit();

            return me;
        }

        this.getName = function()
        {
            return 'columnlist';
        }

        this.register = function()
        {
            if (me._hasGrid())
                grid.registerPlugin('columnlist', me);

            return me;
        }

        this.unregister = function()
        {
            if (grid instanceof $.biggrid)
                grid.unregisterPlugin('columnlist');

            return me;
        }

        this.setStructure = function(structure)
        {
            grid_columns = structure;

            me._render();
            me._initEvents();

            return me;
        }

        this.setSortOrder = function(new_sort_order)
        {
            if (_.indexOf(allowed_sort_orders, new_sort_order) == -1)
                new_sort_order = 'natural';

            var $menu_item = $el.find('.bgg-collist-btn-order-'+new_sort_order);

            // use natural order as a fallback in case the sort order isn't available
            if ($menu_item.length == 0 || $menu_item.hasClass('hidden'))
            {
                $menu_item = $el.find('.bgg-collist-btn-order-natural');
                new_sort_order = 'natural';
            }

            $menu_item.find('.fa')
                      .addClass('fa-check')
                      .closest('li')
                      .siblings('li')
                      .find('.fa')
                      .removeClass('fa-check');

            $search_input.val('').blur();

            search_query = '';
            sort_order = new_sort_order;

            return me;
        }

        // only applies to 'toggle' and 'sticky' selection modes
        this.selectItem = function(col_key)
        {
            if (_.indexOf(['toggle', 'sticky'], opts.selectionMode) == -1)
                return;

            $list_el.find('[data-key-value="'+col_key+'"]').addClass('active');

            if (opts.selectionMode == 'toggle' || opts.selectionMode == 'sticky')
                selected_items = _.union(selected_items, [col_key]);
        }

        // only applies to 'toggle' and 'sticky' selection modes
        this.deselectItem = function(col_key)
        {
            if (_.indexOf(['toggle', 'sticky'], opts.selectionMode) == -1)
                return;

            $list_el.find('[data-key-value="'+col_key+'"]').removeClass('active');

            if (opts.selectionMode == 'toggle' || opts.selectionMode == 'sticky')
                selected_items = _.without(selected_items, col_key);
        }

        // only applies to 'toggle' and 'sticky' selection modes
        this.clearSelectedItems = function()
        {
            if (_.indexOf(['toggle', 'sticky'], opts.selectionMode) == -1)
                return;

            $list_el.find('.bgg-collist-item').removeClass('active');

            if (opts.selectionMode == 'toggle' || opts.selectionMode == 'sticky')
                selected_items = [];
        }

        this.isItemSelected = function(col_key)
        {
            if (_.indexOf(['toggle', 'sticky'], opts.selectionMode) == -1)
                return false;

            if (opts.selectionMode == 'toggle' || opts.selectionMode == 'sticky')
                return _.indexOf(selected_items, col_key) == -1 ? false : true;

            return false;
        }

        // only applies to 'toggle' and 'sticky' selection modes
        this.getSelectedItems = function()
        {
            if (_.indexOf(['toggle', 'sticky'], opts.selectionMode) == -1)
                return [];

            if (opts.selectionMode == 'toggle' || opts.selectionMode == 'sticky')
                return selected_items;

            return [];
        }

        this.refresh = function()
        {
            var cols = [],
                frozen_cls = '',
                hidden_cls = '',
                active_cls = '',
                list_markup = '';

            if (me._hasGrid())
                cols = grid.getColumns(true);
                 else
                cols = grid_columns;

            switch (sort_order)
            {
                default:
                case 'natural':
                    // do nothing
                    break;
                case 'view':
                    if (me._hasGrid())
                        cols = grid.getColumnOrder(true, true);
                    break;
                case 'ascending':
                    cols = _.sortBy(cols, function(col) {
                        // sort 'cols' array by name (case insensitive)
                        return col.name.toLowerCase();
                    });
                    break;
                case 'descending':
                    cols = _.sortBy(cols, function(col) {
                        // sort 'cols' array by name (case insensitive)
                        return col.name.toLowerCase();
                    });
                    cols = cols.reverse();
                    break;
            }

            if (search_query.length > 0)
            {
                cols = _.filter(cols, function(col) {
                    return col.name.contains(search_query);
                });
            }

            _.each(cols, function(col) {
                frozen_cls = (grid_opts && col.frozen === true) ? grid_opts.colFrozenCls : '';
                hidden_cls = (grid_opts && col.hidden === true) ? grid_opts.colHiddenCls : '';
                active_cls = '';

                // remember selected columns for 'toggle' and 'sticky' selection mode
                if (opts.selectionMode == 'toggle' || opts.selectionMode == 'sticky')
                {
                    if (_.indexOf(selected_items, col[opts.keyValue]) != -1)
                        active_cls = 'active';
                }

                list_markup += '' +
                    '<li class="bgg-collist-item clearfix '+frozen_cls+' '+hidden_cls+' '+active_cls+'" ' +
                        'data-name="'+col.name+'" ' +
                        'data-display-name="'+col.display_name+'" ' +
                        'data-type="'+col.type+'" ' +
                        'data-width="'+col.width+'" ' +
                        'data-scale="'+col.scale+'" ' +
                        'data-expr="'+col.expression+'" ' +
                        'data-key-value="'+col[opts.keyValue]+'">' +
                        me._getHandleIcon(col) +
                        '&nbsp;' +
                        me._getColumnName(col) +
                        '<div class="pull-right">' +
                            me._getCalculatedIcon(col) +
                            me._getFrozenIcon(col) +
                            me._getHiddenIcon(col) +
                            me._getTypeIcon(col) +
                        '</div>' +
                    '</li>';
            });

            $list_el.html(list_markup);

            if (opts.isColumnDragAllowed === true)
                $el.find('.bgg-collist-item-handle').css('cursor', 'grab');

            // if we don't have a grid, don't show the 'view order' menu item
            if (!me._hasGrid())
                $el.find('.bgg-collist-btn-order-view').closest('li').addClass('hidden');

            // if we already had an item selector, uninitialize it
            if (me._hasItemSelector())
            {
                item_selector.uninit();
                item_selector = undefined;
            }

            if (opts.selectionMode == 'normal')
            {
                // instantiate our item selector
                $list_el.itemselector({
                    selectedCls: 'active',
                    itemSelector: 'li',
                    allowItemDrag: true
                });

                // store item selector for use elsewhere
                item_selector = $list_el.data('itemselector');
            }

            return me;
        }

// -- private methods --

        this._initEvents = function()
        {
            me._uninitEvents();

            $grid_el.on('refreshmodel.biggrid.'+ns, me.onRefreshColumnList);
            $grid_el.on('refreshcolumnheader.biggrid.'+ns, me.onRefreshColumnList);
            $grid_el.on('refreshall.biggrid.'+ns, me.onRefreshColumnList);
            $grid_el.on('renamecolumn.biggrid.'+ns, me.onRefreshColumnList);

            if (opts.wholeRowDrag === true)
                $el.on('mouseenter.'+ns, '.bgg-collist-item', me.onDragHandleMouseEnter);
                 else
                $el.on('mouseenter.'+ns, '.bgg-collist-item-handle', me.onDragHandleMouseEnter);

            $el.on('keyup.'+ns, 'input[name="q"]', me.onSearchInputKeyUp);

            $el.on('click.'+ns, '.bgg-collist-btn-order-view', me.onViewOrderButtonClick);
            $el.on('click.'+ns, '.bgg-collist-btn-order-natural', me.onNaturalOrderButtonClick);
            $el.on('click.'+ns, '.bgg-collist-btn-order-ascending', me.onAscendingOrderButtonClick);
            $el.on('click.'+ns, '.bgg-collist-btn-order-descending', me.onDescendingOrderButtonClick);

            $list_el.on('mousedown.'+ns, '.bgg-collist-btn-toggle-frozen', function(evt) { evt.stopPropagation(); });
            $list_el.on('mousedown.'+ns, '.bgg-collist-btn-toggle-hidden', function(evt) { evt.stopPropagation(); });
            $list_el.on('mousedown.'+ns, '.bgg-collist-item', me.onItemMouseDown);

            $list_el.on('click.'+ns, '.bgg-collist-btn-toggle-frozen', me.onFrozenToggleButtonClick);
            $list_el.on('click.'+ns, '.bgg-collist-btn-toggle-hidden', me.onHiddenToggleButtonClick);

            return me;
        }

        this._uninitEvents = function()
        {
            $grid_el.off('.'+ns);
            $el.off('.'+ns);
            $list_el.off('.'+ns);

            return me;
        }

        this._render = function()
        {
            $el = $(opts.markup);
            $list_el = $el.find('.bgg-collist-body > ul');

            if (opts.appendTo !== undefined)
            {
                var $append_to = $(opts.appendTo),
                    pos = $append_to.css('position');

                // parent element has to be able to position the column list elements
                if (pos != 'fixed' && pos != 'absolute' && pos != 'relative')
                    $append_to.css('position', 'relative');

                $append_to.append($el);
            }

            if (opts.showHeader === false)
            {
                $el.find('.bgg-collist-header').remove();
                $el.find('.bgg-collist-body').css('top', '0px');
            }
             else
            {
                var $search_container = $el.find('.bgg-collist-search');
                $search_container.htmlPlaceholder();

                $search_input = $search_container.find('input');
            }

            return me;
        }

        this._hasGrid = function()
        {
            return (grid instanceof $.biggrid) ? true : false;
        }

        this._hasItemSelector = function()
        {
            return (item_selector instanceof $.itemselector) ? true : false;
        }

        this._getHandleIcon = function(col)
        {
            if (opts.showColumnHandle !== true)
                return '';

            return '<span class="fa-fw glyphicon glyphicon-menu-hamburger bgg-collist-item-handle"></span>';
        }

        this._getColumnName = function(col)
        {
            return '' +
                '<div class="bgg-collist-item-name">' +
                    (col.display_name || col.name) +
                '</div>';
        }

        this._getTypeIcon = function(col)
        {
            switch (col.type)
            {
                case biggrid.TYPE_CHARACTER:
                    return '' +
                        '<span class="bgg-collist-item-type tipsy" title="'+_T('Character')+'" data-placement="left">' +
                            '<span style="font-size: 10px; font-weight: bold; position: relative; top: -1px;">abc</span>' +
                        '</span>';

                case biggrid.TYPE_INTEGER:
                    return '' +
                        '<span class="bgg-collist-item-type tipsy" title="'+_T('Integer')+'" data-placement="left">' +
                            '<span style="font-size: 10px; font-weight: bold; position: relative; top: -1px;">123</span>' +
                        '</span>';

                case biggrid.TYPE_NUMERIC:
                    return '' +
                        '<span class="bgg-collist-item-type tipsy" title="'+_T('Numeric')+'" data-placement="left">' +
                            '<span style="font-size: 10px; font-weight: bold; position: relative; top: -1px;">123</span>' +
                        '</span>';

                case biggrid.TYPE_DATE:
                    return '' +
                        '<span class="bgg-collist-item-type tipsy" title="'+_T('Date')+'" data-placement="left">' +
                            '<span class="fa fa-fw fa-calendar" style="font-size: 14px"></span>' +
                        '</span>';

                case biggrid.TYPE_DATETIME:
                    return '' +
                        '<span class="bgg-collist-item-type tipsy" title="'+_T('Date/Time')+'" data-placement="left">' +
                            '<span class="fa fa-fw fa-clock-o" style="font-size: 14px"></span>' +
                        '</span>';

                case biggrid.TYPE_BOOLEAN:
                    return '' +
                        '<span class="bgg-collist-item-type tipsy" title="'+_T('Boolean')+'" data-placement="left">' +
                            '<span class="fa fa-fw fa-check-square-o" style="font-size: 14px"></span>' +
                        '</span>';
            }

            return '' +
                '<span class="bgg-collist-item-type">' +
                    '<span class="fa fa-fw"></span>' +
                '</span>';
        }

        this._getCalculatedIcon = function(col)
        {
            if (opts.showColumnCalculated !== true)
                return '';

            if (col.expression.length > 0)
            {
                return '' +
                    '<span class="tipsy" title="'+_T('Calculated')+'" data-placement="left">' +
                        '<span class="fa fa-fw fa-flash bgg-icon" style="font-size: 14px"></span>' +
                    '</span>';
            }

            return '<span><span class="fa fa-fw"></span></span>';
        }

        this._getFrozenIcon = function(col)
        {
            if (opts.showColumnFrozen !== true)
                return '';

             return '' +
                '<span>' +
                    '<a data-anchor="btn" href="#" class="bgg-collist-btn-toggle-frozen text-default tipsy" title="'+_T('Freeze/Unfreeze')+'" data-placement="left">' +
                        '<span class="fa fa-fw fa-lock" style="font-size: 14px; top: 1px"></span>' +
                    '</a>' +
                '</span>';
        }

        this._getHiddenIcon = function(col)
        {
            if (opts.showColumnHidden !== true)
                return '';

             return '' +
                '<span>' +
                    '<a data-anchor="btn" href="#" class="bgg-collist-btn-toggle-hidden text-default tipsy" title="'+_T('Show/Hide')+'" data-placement="left">' +
                        '<span class="fa fa-fw fa-eye-slash" style="font-size: 14px"></span>' +
                    '</a>' +
                '</span>';
        }

        this._createDragHelper = function()
        {
            var $helper = $('<div />'),
                $cells = $(),
                columns = [],
                top = 0,
                left = 0;

            $cells = $list_el.find('.active');

            $cells.each(function() {
                var $li = $(this),
                    $cell = $cells.first().find('.bgg-collist-item-name').clone();

                $cell.addClass('bgg-col-drag-helper')
                $cell.css({
                    'position': 'absolute',
                    'top': top+'px',
                    'left': left+'px'
                });

                $helper.append($cell);

                columns.push($li.data('key-value'));

                top += 3;
                left += 5;
            });

            // add a count circle when dragging more than one item
            if ($cells.length > 1)
            {
                var $count_el = $('<span>'+($cells.length)+'</span>').appendTo('body');
                $count_el.makeSquare(2);

                $count_el.addClass('text-center img-circle bg-primary bgg-drag-helper');
                $count_el.css({
                    'position': 'absolute',
                    'top': '-8px',
                    'left': '-12px'
                });

                $helper.append($count_el);
            }

            return $helper
                .data('drag_data', {
                    'columns': columns,
                    'source_id': ns
                });
        }

// -- event handlers --

        this.onRefreshColumnList = function(evt)
        {
            me.refresh();
        }

        // we do this on mouse enter instead of when we render the column list
        // because this is an slow/expensive operation
        this.onDragHandleMouseEnter = function(evt)
        {
            if (item_selector === undefined)
                return;

            if (!me._hasGrid())
                return;

            if (!grid.hasPlugin('columndragreorder'))
                return;

            if (grid.hasState('bgg-state-col-dragging'))
                return;

            var $li = $(this).closest('.bgg-collist-item'),
                handle = opts.wholeRowDrag === false
                            ? '.bgg-collist-item-handle'
                            : false /* use entire item */;

            if ($li.hasClass('ui-draggable'))
                return;

            // TODO: we should really break out the draggable into a separate grid plugin
            if (opts.isColumnDragAllowed === true && opts.isColumnReorderDragSource === true)
                grid.getPlugin('columndragreorder').initDraggable($li, handle, me._createDragHelper);
        }

        this.onSearchInputKeyUp = function(evt)
        {
            search_query = this.value;

             // escape key
            if (evt.which == 27)
            {
                $search_input.val('');
                search_query = '';
            }

            me.refresh();
        }

        this.onFrozenToggleButtonClick = function(evt)
        {
            if (!me._hasGrid())
                return;

            var col_key = $(this).closest('.bgg-collist-item').data('key-value'),
                frozen = grid.isColumnFrozen(col_key);

            grid[frozen?'unfreezeColumn':'freezeColumn'](col_key);
            grid.refreshAll();

            $('.tooltip').remove();

            evt.stopPropagation();
        }

        this.onHiddenToggleButtonClick = function(evt)
        {
            if (!me._hasGrid())
                return;

            var col_key = $(this).closest('.bgg-collist-item').data('key-value'),
                hidden = grid.isColumnHidden(col_key);

            grid[hidden?'showColumn':'hideColumn'](col_key);
            grid.refreshAll();

            $('.tooltip').remove();

            evt.stopPropagation();
        }

        this.onItemMouseDown = function(evt)
        {
            var $item = $(this),
                col_key = $item.data('key-value');

            if (opts.selectionMode == 'toggle')
            {
                $item.toggleClass('active');

                if ($item.hasClass('active'))
                    selected_items = _.union(selected_items, [col_key]);
                     else
                    selected_items = _.without(selected_items, col_key);
            }
             else if (opts.selectionMode == 'sticky')
            {
                $item.addClass('active');

                selected_items = _.union(selected_items, [col_key]);
            }
        }

        this.onNaturalOrderButtonClick = function(evt)
        {
            me.setSortOrder('natural').refresh();
        }

        this.onViewOrderButtonClick = function(evt)
        {
            me.setSortOrder('view').refresh();
        }

        this.onAscendingOrderButtonClick = function(evt)
        {
            me.setSortOrder('ascending').refresh();
        }

        this.onDescendingOrderButtonClick = function(evt)
        {
            me.setSortOrder('descending').refresh();
        }

        // if a grid is specified in the options, do the init right away
        if (opts.grid instanceof $.biggrid)
            me.init(opts.grid);
             else
            me.setStructure(opts.structure);

        // initialize our sort order
        me.setSortOrder(opts.sortOrder).refresh();
    }

    biggrid.plugin.columnlist.defaults = {
        appendTo: undefined,
        grid: undefined,
        structure: [], // optionally just pass the structure to use (for cases where a grid isn't available)
        selectionMode: 'normal', // 'normal', 'toggle', 'sticky', 'none'
        sortOrder: 'view', // 'view', 'natural', 'ascending', 'descending'
        showHeader: true,
        showColumnHandle: true,
        showColumnCalculated: true,
        showColumnFrozen: true,
        showColumnHidden: true,
        wholeRowDrag: true,
        isColumnDragAllowed: true,
        isColumnReorderDragSource: true,
        keyValue: 'col_id',
        markup: '' +
            '<div class="bgg-collist">' +
                '<div class="bgg-collist-header clearfix">' +
                    '<div class="clearfix pull-right">' +
                        '<div class="dropdown pull-right">' +
                            '<a data-anchor="btn" href="#" class="dropdown-toggle text-muted" data-toggle="dropdown">' +
                                '<span class="fa fa-sort"></span>' +
                            '</a>' +
                            '<ul class="dropdown-menu fx-dropdown-menu-sm" style="margin-right: -4px">' +
                                '<li><a data-anchor="btn" href="#" class="bgg-collist-btn-order-view"><span class="fa fa-fw"></span> <i18n>View Order</i18n></a></li>' +
                                '<li><a data-anchor="btn" href="#" class="bgg-collist-btn-order-natural"><span class="fa fa-fw fa-check"></span> <i18n>Natural Order</i18n></a></li>' +
                                '<li><a data-anchor="btn" href="#" class="bgg-collist-btn-order-ascending"><span class="fa fa-fw"></span> <i18n>Sort Ascending</i18n></a></li>' +
                                '<li><a data-anchor="btn" href="#" class="bgg-collist-btn-order-descending"><span class="fa fa-fw"></span> <i18n>Sort Descending</i18n></a></li>' +
                            '</ul>' +
                        '</div>' +
                    '</div>' +
                    '<div class="bgg-collist-search">' +
                        '<input type="text" class="form-control input-sm fx-form-control-bare hidden" name="q" placeholder="'+_T('Search')+'" autocomplete=off>' +
                        '<div class="html-placeholder"><span class="fa fa-search fa-flip-horizontal text-muted"></span></div>' +
                    '</div>' +
                '</div>' +
                '<div class="bgg-collist-body">' +
                    '<ul class="list-unstyled"></ul>' +
                '</div>' +
            '</div>'
    };

})(jQuery, window, document);
