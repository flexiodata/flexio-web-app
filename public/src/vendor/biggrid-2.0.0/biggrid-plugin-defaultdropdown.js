/*
 * Big Grid Plugin - Default Dropdown
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    biggrid.plugin.defaultdropdown = function(options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace
        var uid = this._generateUid(),
            ns = 'bgg-defaultdropdown-'+uid;

        var me = this,
            opts = $.extend({}, biggrid.plugin.defaultdropdown.defaults, options),
            grid = undefined,
            grid_model = undefined,
            grid_opts = undefined,
            dropdown_visible = undefined,
            $action_th = $(),
            $dropdown_menu = $(),
            $grid_el = $(),
            $grid_data_header = $(),
            $grid_fixed_header = $();

// -- public methods --

        this.init = function(biggrid)
        {
            grid = biggrid;
            grid_model = grid.getModel();
            grid_opts = grid.getOptions();
            $grid_el = grid.getElement();
            $grid_data_header = $grid_el.find('.bgg-data-header');
            $grid_fixed_header = $grid_el.find('.bgg-fixed-header');

            me._initEvents();

            return me;
        }

        this.destroy = function()
        {
            return me._uninitEvents();
        }

        this.getName = function()
        {
            return 'defaultdropdown';
        }

        this.register = function()
        {
            if (grid instanceof $.biggrid)
                grid.registerPlugin('defaultdropdown', me);

            return me;
        }

        this.unregister = function()
        {
            if (grid instanceof $.biggrid)
                grid.unregisterPlugin('defaultdropdown');

            return me;
        }

        this.isVisible = function()
        {
            if ($dropdown_menu.length == 0)
                return false;

            return dropdown_visible ? true : false;
        }

        this.hideDropdown = function()
        {
            if ($dropdown_menu.length == 0)
               return me;

            // hide dropdown button manually
            $action_th.find('.dropdown').removeClass('open');
            $action_th = $();

            // since the dropdown is now appended to the body,
            // we must remove it from the DOM altogether
            $dropdown_menu.remove();
            $dropdown_menu = $();

            dropdown_visible = false;

            return me;
        }

// -- private methods --

        this._initEvents = function()
        {
            me._uninitEvents();

            // handy way to abstract out header events
            var $grid_headers = $().add($grid_data_header).add($grid_fixed_header);

            $grid_headers.on('mouseenter.biggrid.'+ns, 'th', me.onGridHeaderCellMouseEnter);
            $grid_headers.on('mousedown.biggrid.'+ns, 'th .bgg-cell-dropdown', me.onGridHeaderCellDropdownMouseDown);
            $grid_headers.on('click.biggrid.'+ns, 'th .bgg-cell-dropdown .dropdown-toggle', me.onGridHeaderCellDropdownButtonClick);

            $grid_el.on('shown.bs.dropdown.'+ns, '.dropdown', me.onGridDropdownShown);

            return me;
        }

        this._uninitEvents = function()
        {
            // handy way to abstract out header events
            var $grid_headers = $().add($grid_data_header).add($grid_fixed_header);

            $grid_headers.off('.'+ns);
            $grid_el.off('.'+ns);

            return me;
        }

        this._getDropdownMenuMarkup = function(col_id)
        {
            var col_info = grid.getColumnInfo(col_id),
                selected_cols = grid.getSelectedColumns();

            // begin dropdown list
            var menu_markup = '';

            var disabled_cls = 'disabled',
                remove_sort_filter_cls = '',
                remove_sort_cls = '',
                rename_cls = '',
                multi_column_selected_cls = '';

            /*
                if (!grid_model.isSorted() && !grid_model.isFiltered())
                    remove_sort_filter_cls = 'disabled';

                if (!grid_model.isSorted())
                    remove_sort_cls = 'disabled';
            */

            if (!grid.isColumnRenameAllowed())
                rename_cls = 'disabled';

            if (selected_cols.length > 1)
                multi_column_selected_cls = 'disabled';

                menu_markup += '' +
                    '<li><a data-anchor="btn" href="#" action="sort-ascending"><span class="fa fa-fw fa-sort-alpha-asc"></span> <i18n>Sort Ascending</i18n></a></li>' +
                    '<li><a data-anchor="btn" href="#" action="sort-descending"><span class="fa fa-fw fa-sort-alpha-desc"></span> <i18n>Sort Descending</i18n></a></li>' +
                    '<li><a data-anchor="btn" href="#" action="filter"><span class="fa-fw glyphicon glyphicon-filter"></span> <i18n>Filter...</i18n></a></li>';

            menu_markup += '' +
                '<li class="divider"></li>';

            menu_markup += '' +
                    '<li><a data-anchor="btn" href="#" action="group"><span class="fa-fw fa"></span> <i18n>Summarize...</i18n></a></li>';/* +
                    '<li><a data-anchor="btn" href="#" action="remove-sort-filter"><span class="fa fa-fw"></span> <i18n>Remove Sort/Filter</i18n></a></li>';
                    */
/*
            if (grid_model.isSortAllowed())
            {
                menu_markup += '' +
                    '<li><a data-anchor="btn" href="#" action="sort-ascending"><span class="fa fa-fw fa-sort-alpha-asc"></span> <i18n>Sort Ascending</i18n></a></li>' +
                    '<li><a data-anchor="btn" href="#" action="sort-descending"><span class="fa fa-fw fa-sort-alpha-desc"></span> <i18n>Sort Descending</i18n></a></li>';
            }

            if (grid_model.isFilterAllowed())
            {
                menu_markup += '' +
                    '<li><a data-anchor="btn" href="#" action="filter"><span class="fa-fw glyphicon glyphicon-filter"></span> <i18n>Filter...</i18n></a></li>';
            }

            if (grid_model.isSortAllowed() && grid_model.isFilterAllowed())
            {
                menu_markup += '' +
                    '<li class="'+remove_sort_filter_cls+'"><a data-anchor="btn" href="#" action="remove-sort-filter"><span class="fa fa-fw"></span> <i18n>Remove Sort/Filter</i18n></a></li>';
            }
             else if (grid_model.isSortAllowed())
            {
                menu_markup += '' +
                    '<li class="'+remove_sort_cls+'"><a data-anchor="btn" href="#" action="remove-sort"><span class="fa fa-fw"></span> <i18n>Remove Sort</i18n></a></li>';
            }
             else if (grid_model.isFilterAllowed())
            {
                menu_markup += '' +
                    '<li class="'+remove_sort_cls+'"><a data-anchor="btn" href="#" action="remove-filter"><span class="fa fa-fw"></span> <i18n>Remove Filter</i18n></a></li>';
            }

            if (menu_markup.length > 0)
            {
                menu_markup += '' +
                    '<li class="divider"></li>';
            }

            menu_markup += '' +
                '<li class="'+multi_column_selected_cls+' '+rename_cls+'"><a data-anchor="btn" href="#" action="rename"><span class="fa fa-fw"></span> <i18n>Rename...</i18n></a></li>';

            if (grid.hasPlugin('formulabar'))
            {
                menu_markup += '' +
                    '<li class="'+multi_column_selected_cls+'"><a data-anchor="btn" href="#" action="insert-calculated-column"><span class="fa fa-fw fa-flash"></span> <i18n>Insert Calculated Column...</i18n></a></li>';

                if (col_info.expression.length > 0)
                {
                    menu_markup += '' +
                        '<li class="'+multi_column_selected_cls+'"><a data-anchor="btn" href="#" action="edit-calculated-column"><span class="fa fa-fw"></span> <i18n>Edit Calculated Column...</i18n></a></li>';
                }
            }
            */

            menu_markup += '' +
                '<li class="divider"></li>';

            if (col_info.frozen)
            {
                menu_markup += '' +
                    '<li><a data-anchor="btn" href="#" action="unfreeze-column"><span class="fa fa-fw"></span> <i18n>Unfreeze Column</i18n></a></li>' +
                    '<li><a data-anchor="btn" href="#" action="hide-column"><span class="fa fa-fw"></span> <i18n>Hide Column</i18n></a></li>';
            }
             else
            {
                if (selected_cols.length > 1)
                {
                    menu_markup += '' +
                        '<li><a data-anchor="btn" href="#" action="freeze-column"><span class="fa fa-fw fa-lock"></span> <i18n>Freeze Selected Columns</i18n></a></li>' +
                        '<li><a data-anchor="btn" href="#" action="hide-column"><span class="fa fa-fw"></span> <i18n>Hide Selected Columns</i18n></a></li>';
                }
                 else
                {
                    menu_markup += '' +
                        '<li><a data-anchor="btn" href="#" action="freeze-column"><span class="fa fa-fw fa-lock"></span> <i18n>Freeze Column</i18n></a></li>' +
                        '<li><a data-anchor="btn" href="#" action="hide-column"><span class="fa fa-fw"></span> <i18n>Hide Column</i18n></a></li>';
                }
            }

/*
            menu_markup += '' +
                '<li class="divider"></li>';

            menu_markup += '' +
                '<li><a data-anchor="btn" href="#" action="format"><span class="fa fa-fw"></span> <i18n>Conditional Formatting...</i18n></a></li>';

            menu_markup += '' +
                '<li class="divider"></li>';

            if (selected_cols.length > 1)
            {
                menu_markup += '' +
                    '<li><a data-anchor="btn" href="#" action="drop-column"><span class="fa fa-fw"></span> <i18n>Drop Selected Columns</i18n></a></li>';
            }
             else
            {
                menu_markup += '' +
                    '<li><a data-anchor="btn" href="#" action="drop-column"><span class="fa fa-fw"></span> <i18n>Drop Column</i18n></a></li>';
            }
*/

            // wrap list items with dropdown menu unordered list element
            menu_markup = '' +
                '<ul class="dropdown-menu bgg-dropdown-menu bgg-caret-dropdown-menu text-left">' +
                    menu_markup +
                '</ul>';

            return menu_markup;
        }

// -- event handlers --

        this.onGridHeaderCellMouseEnter = function(evt)
        {
            var $th = $(this),
                $cell_inner = $th.find('.bgg-cell-inner'),
                $dropdown = $cell_inner.find('.bgg-cell-dropdown');

            if (!grid.isColumnDropdownAllowed())
                return;

            if ($dropdown.hasClass('open'))
                return;

            $dropdown.remove();

            if ($th.hasClass(grid_opts.colInvalidCls))
                return;

            $(opts.template).prependTo($cell_inner);
        }

        this.onGridHeaderCellDropdownMouseDown = function(evt)
        {
            var $btn = $(this),
                $th = $btn.closest('th'),
                col_id = $th.data('col-id'),
                col_info = grid.getColumnInfo(col_id);

            // don't allow middle-click
            if (evt.which == 2)
            {
                evt.preventDefault();
                evt.stopPropagation();
                return;
            }

            // mouse down anywhere should always close any open dropdown menus
            if (me.isVisible())
                me.hideDropdown();

            // remove selection area when showing the dropdown menu
            if (grid.isCellSelectionAllowed())
                grid.clearCellSelection();

            // remove row selection when showing the dropdown menu
            if (grid.isRowSelectionAllowed())
                grid.clearRowSelection();

            // if we clicked on a dropdown for a column that is not selected,
            // remove existing column selection and select this column
            if (grid.isColumnSelectionAllowed())
            {
                if (!grid.isColumnSelected(col_id))
                    grid.selectColumn(col_id);
            }

            // don't bubble up
            evt.stopPropagation();
        }

        this.onGridHeaderCellDropdownButtonClick = function(evt)
        {
            var $btn = $(this),
                $th = $btn.closest('th'),
                col_id = $th.data('col-id'),
                col_info = grid.getColumnInfo(col_id);

            var menu_markup = me._getDropdownMenuMarkup(col_id);

            $action_th = $th;

            $btn.closest('.bgg-cell-dropdown')
                .find('.dropdown-menu')
                .remove()
                .end()
                .append(menu_markup);

            // toggle dropdown manually
            $btn.dropdown('toggle');

            dropdown_visible = true;

            // don't bubble up
            evt.stopPropagation();

            // ignore anchor ref.
            evt.preventDefault();
        }

        this.onGridDropdownShown = function(evt)
        {
            // store the dropdown menu for later use
            var $dropdown = $(this),
                $th = $dropdown.closest('th'),
                $cell_inner = $th.find('.bgg-cell-inner');

            $dropdown_menu = $dropdown.find('.dropdown-menu');

            // position the dropdown menu relative to the document body
            var off = $dropdown_menu.offset();

            $dropdown_menu.css({
                'position': 'fixed',
                'top': off.top+'px',
                'left': off.left+'px'
            });

            // move the dropdown menu to be a child of the document body
            $dropdown_menu.addClass('open').appendTo('body');

            // hook up the dropdown menu item events
            $dropdown_menu.on('click', 'li a', me.onGridHeaderDropdownMenuItemClick);

            // hook up the dropdown menu item events
            $dropdown_menu.on('mousedown', me.onGridHeaderDropdownMenuMouseDown);

            // now remove the dropdown from the cell and create a new one
            // (in case we want to click it again)
            $dropdown.remove();

            $(opts.template).prependTo($cell_inner).addClass('open');
        }

        // don't allow this event to bubble up to the document
        this.onGridHeaderDropdownMenuMouseDown = function(evt)
        {
            evt.stopPropagation();
        }

        this.onGridHeaderDropdownMenuItemClick = function(evt)
        {
            var $menu_item = $(this),
                $list_item = $menu_item.closest('li'),
                action = $menu_item.attr('action'),
                col_name = $action_th.data('name'),
                col_id = $action_th.data('col-id'),
                col_expr = grid.getColumnInfo(col_id, 'expression'),
                selected_cols = grid.getSelectedColumns();

            if ($list_item.hasClass('disabled'))
                return;

            var _doColumnSort = function(dir)
            {
                var selected_cols = grid.getSelectedColumns(true),
                    col_names = _.map(selected_cols, 'name');

                var sort = _.map(col_names, function(col_name) {
                    return {
                        column: col_name,
                        direction: dir
                    }
                });

                // make sure the sorts are stored as an array
                if (!$.isArray(sort))
                    sort = [sort];

                // fire the 'sort.biggrid' event
                $grid_el.trigger($.Event('sort.biggrid'), [sort]);

                return me;
            }

            switch (action)
            {
                case 'sort-ascending':
                    _doColumnSort('asc');
                    break;

                case 'sort-descending':
                    _doColumnSort('desc');
                    break;

                /*
                case 'remove-sort-filter':
                    // fire the 'removesortfilter.biggrid' events
                    $grid_el.trigger($.Event('removesortfilter.biggrid'));
                    break;

                case 'remove-sort':
                    // fire the 'removesort.biggrid' event
                    $grid_el.trigger($.Event('removesort.biggrid'));
                    break;

                case 'remove-filter':
                    // fire the 'removefilter.biggrid' event
                    $grid_el.trigger($.Event('removefilter.biggrid'));
                    break;
                */

                case 'filter':
                    $grid_el.trigger($.Event('filterclick.biggrid'));
                    /*
                    if (grid.hasPlugin('filterbar'))
                    {
                        grid.getPlugin('filterbar').focusFilterInput();
                    }
                    */
                    break;

                case 'group':
                    $grid_el.trigger($.Event('groupclick.biggrid'));
                    break;

                case 'format':
                    $grid_el.trigger($.Event('formatclick.biggrid'));
                    break;

                /*
                case 'rename':
                    grid.startHeaderCellEdit(col_id);
                    break;

                case 'edit-calculated-column':
                    if (grid.hasPlugin('formulabar') && grid_model.isCalculatedColumnAllowed())
                    {
                        grid_model.edit_type = 'edit';
                        grid_model.alterCalculatedColumn({
                            action: 'edit',
                            target_column: col_name,
                            callback: function(res) {
                                grid.refreshAll()
                                    .selectColumn(col_id)
                                    .getPlugin('formulabar').showFormulaBar(col_expr);
                            }
                        });

                        grid.getPlugin('formulabar').showFormulaBar(col_expr);
                    }
                    break;

                case 'insert-calculated-column':
                    if (grid.hasPlugin('formulabar') && grid_model.isCalculatedColumnAllowed())
                    {
                        grid_model.edit_type = 'create';
                        grid_model.alterCalculatedColumn({
                            action: 'create',
                            callback: function(res) {
                                var insert_col_id = grid.startColumnInsert(col_id);

                                var new_col_info = grid.createColumn({
                                    name: res.data.name,
                                    type: res.data.type,
                                    width: res.data.width,
                                    scale: res.data.scale,
                                    expression: res.data.expression,
                                    display_name: res.data.name
                                });

                                grid.setColumnInfo(insert_col_id, new_col_info);

                                grid.refreshAll()
                                    .selectColumn(insert_col_id)
                                    .getPlugin('formulabar').showFormulaBar();
                            }
                        });
                    }
                    break;
                */

                case 'freeze-column':
                    $.each(selected_cols, function(idx, selected_col_id) {
                        grid.freezeColumn(selected_col_id);
                    });
                    grid.refreshAll();
                    break;

                case 'unfreeze-column':
                    $.each(selected_cols, function(idx, selected_col_id) {
                        grid.unfreezeColumn(selected_col_id);
                    });
                    grid.refreshAll();
                    break;

                case 'hide-column':
                    $.each(selected_cols, function(idx, selected_col_id) {
                        grid.hideColumn(selected_col_id);
                    });
                    grid.refreshAll();
                    break;

                /*
                case 'drop-column':
                    $.each(selected_cols, function(idx, selected_col_id) {
                        grid.dropColumn(selected_col_id);
                    });
                    grid.refreshAll();
                    break;
                */
            }

            // don't bubble up
            evt.stopPropagation();

            // ignore anchor ref.
            evt.preventDefault();

            // force hide dropdown
            me.hideDropdown();
        }
    }

    biggrid.plugin.defaultdropdown.defaults = {
        template: '' +
            '<div class="bgg-cell-dropdown dropdown">' +
                '<a data-anchor="btn" href="#" class="dropdown-toggle" data-toggle="dropdown">' +
                    '<span class="caret"></span>' +
                '</a>' +
            '</div>'
    };

})(jQuery, window, document);
