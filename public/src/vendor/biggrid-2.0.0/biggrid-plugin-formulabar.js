/*
 * Big Grid Plugin - Formula Bar
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    biggrid.plugin.formulabar = function(options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace
        var uid = this._generateUid(),
            ns = 'bgg-formulabar-'+uid;

        var me = this,
            opts = $.extend({}, biggrid.plugin.formulabar.defaults, options),
            grid = undefined,
            grid_model = undefined,
            grid_selected_col_id = undefined,
            grid_selected_col_name = '',
            $el = $(),
            $grid_el = $(),
            $grid_utility_bar = $(),
            $grid_data_table = $(),
            $input_formula = $(),
            $btn_cancel = $(),
            $btn_submit = $();

// -- public methods --

        this.init = function(biggrid)
        {
            grid = biggrid;
            grid_model = grid.getModel();
            $grid_el = grid.getElement();
            $grid_utility_bar = $grid_el.find('.bgg-utility-bar');
            $grid_data_table = $grid_el.find('.bgg-table-grid-data');

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
            return 'formulabar';
        }

        this.register = function()
        {
            if (grid instanceof $.biggrid)
                grid.registerPlugin('formulabar', me);

            return me;
        }

        this.unregister = function()
        {
            if (grid instanceof $.biggrid)
                grid.unregisterPlugin('formulabar');

            return me;
        }

        this.showFormulaBar = function(default_val, focus_input)
        {
            $el.removeClass('hidden');

            if (opts.appendTo === undefined)
                $el.siblings().addClass('hidden');

            var cols = grid.getSelectedColumns();
            if (cols.length != 1)
                return me;

            grid_selected_col_id = cols[0];
            grid_selected_col_name = grid.getColumnInfo(grid_selected_col_id, 'name');

            // don't allow column selection to change while we're editing a formula
            grid.setCellSelectionAllowed(false, true);
            grid.setRowSelectionAllowed(false, true);
            grid.setColumnSelectionAllowed(false, true);
            grid.setColumnRenameAllowed(false);
            grid.setColumnDropdownAllowed(false);

            // set formula bar input text
            if (default_val !== undefined)
                $input_formula.val(default_val);
                 else
                $input_formula.val('');

            if (focus_input !== false)
                $input_formula.focusEnd();

            return me;
        }

        this.hideFormulaBar = function()
        {
            $el.addClass('hidden');

            if (opts.appendTo === undefined)
                $el.siblings().removeClass('hidden');

            grid_selected_col_id = undefined;

            // allow column selection to change again
            grid.setCellSelectionAllowed(true);
            grid.setRowSelectionAllowed(true);
            grid.setColumnSelectionAllowed(true);
            grid.setColumnRenameAllowed(true);
            grid.setColumnDropdownAllowed(true);

            // empty out formula bar input text
            $input_formula.val('');

            return me;
        }

        this.updateGridColumn = function(action)
        {
            if (action === undefined)
                action = 'edit';

            if (grid_model.isCalculatedColumnAllowed())
            {
                var expr = $input_formula.val(),
                    row_start = grid.getRenderedRowStart(),
                    row_limit = grid.getRenderedRowCount();

                // get selected cell in the data grid
                var $grid_selected_cells = $grid_data_table.find('td.'+grid_selected_col_id);

                var alter_opts = {
                    action: action,
                    target_column: grid_selected_col_name,
                    name: grid_selected_col_name,
                    expression: expr,
                    start: row_start,
                    limit: row_limit,
                }

                var alter_cb = function(res)
                {
                    if (action == 'save-create')
                        $grid_el.trigger($.Event('alter.biggrid'), alter_opts);

                    if (action == 'save-create' || action == 'save-edit')
                        return;

                    // -- update the calculated column's cell text --

                    if (res.success)
                    {
                        var row_values = res.rows,
                            row_idx = 0,
                            val = '',
                            col = grid.getColumnInfo(grid_selected_col_id),
                            cell_renderer = grid.getCellRenderer(grid_selected_col_id),
                            cell_content = '';

                        // update the selected column's cell values
                        $grid_selected_cells.each(function() {
                            if (row_idx >= row_values.length)
                                return;

                            val = row_values[row_idx][grid_selected_col_name];

                            // the cell renderer will return to us everything we need to render the cell
                            cell_renderer.render(val, col);
                            cell_content = cell_renderer.getCellContent();

                            $(this).text(cell_content);
                            row_idx++;
                        });

                        // set the column's expression
                        grid.setColumnInfo(grid_selected_col_id, 'expression', expr);
                    }
                }

                grid_model.alterCalculatedColumn($.extend({}, alter_opts, {
                    callback: alter_cb
                }));
            }

            return me;
        }

        // update grid column throttle
        this.updateGridColumnThrottled = _.debounce(this.updateGridColumn, 100);

// -- private methods --

        this._initEvents = function()
        {
            me._uninitEvents();

            $grid_el.on('afterresize.biggrid.'+ns, me.onGridAfterResize);
            $grid_el.on('afterscroll.biggrid.'+ns, me.onGridAfterScroll);

            $el.find('.bgg-formulabar-formula-dropdown-menu').on('click.'+ns, 'li a', me.onFormulaDropdownItemClick);
            $el.find('.bgg-formulabar-btn-formula-submit').on('click.'+ns, me.onFormulaSubmitButtonClick);
            $el.find('.bgg-formulabar-btn-formula-cancel').on('click.'+ns, me.onFormulaCancelButtonClick);

            $input_formula.on('keydown.'+ns, me.onFormulaInputKeydown);
            $input_formula.on('keyup.'+ns, me.onFormulaInputKeyup);

            return me;
        }

        this._uninitEvents = function()
        {
            $grid_el.off('.'+ns);
            $el.off('.'+ns);
            $input_formula.off('.'+ns);

            return me;
        }

        this._render = function()
        {
            $el = $(opts.markup).addClass(opts.extraCls);
            $input_formula = $el.find('.bgg-formulabar-input-formula');
            $btn_cancel = $el.find('.bgg-formulabar-btn-formula-cancel');
            $btn_submit = $el.find('.bgg-formulabar-btn-formula-submit');

            if (opts.appendTo !== undefined)
                $(opts.appendTo).append($el);
                 else
                $grid_utility_bar.append($el.addClass('hidden bgg-utility-bar-inner'));

            return me;
        }

        this.onGridAfterResize = function(evt, dir)
        {
            if (!grid.isInsertingColumn())
                return;

            if (dir != 'vertical' && dir != 'both')
                return;

            me.updateGridColumnThrottled();
        }

        this.onGridAfterScroll = function(evt, dir)
        {
            if (!grid.isInsertingColumn())
                return;

            if (dir != 'vertical')
                return;

            me.updateGridColumnThrottled();
        }

        this.onFormulaDropdownItemClick = function(evt)
        {
            var fn_text = $(this).text().slice(0,-1);
            $input_formula.insertAtCaret(fn_text);
        }

        this.onFormulaSubmitButtonClick = function(evt)
        {
            me._submit();
        }

        this.onFormulaCancelButtonClick = function(evt)
        {
            me._cancel();
        }

        this.onFormulaInputKeydown = function(evt)
        {
            if (evt.which == 13) // enter
            {
                evt.preventDefault();
                me._submit();
            }
             else if (evt.which == 27) // escape
            {
                evt.preventDefault();
                me._cancel();
            }
        }

        this.onFormulaInputKeyup = function(evt)
        {
            me.updateGridColumnThrottled();
        }

        this._submit = function()
        {
            if (grid_model.edit_type == 'create')
            {
                me.updateGridColumn('save-create');
            }
             else if (grid_model.edit_type == 'edit')
            {
                me.updateGridColumn('save-edit');
            }

            me.hideFormulaBar();
        }

        this._cancel = function()
        {
            grid.endColumnInsert(false);
            grid.clearColumnSelection();
            grid.refreshAll();

            me.hideFormulaBar();
        }

// -- event handlers --

    }

    biggrid.plugin.formulabar.defaults = {
        appendTo: undefined,
        markup: '' +
            '<div class="bgg-formulabar">' +
                '<form class="form">' +
                    '<div class="input-group input-group-sm">' +
                        '<div class="input-group-btn">' +
                            '<button type="button" class="btn btn-default bgg-formulabar-btn-formula-function dropdown-toggle" data-toggle="dropdown">&fnof;&#8339;</button>' +
                            '<ul class="dropdown-menu bgg-dropdown-menu bgg-caret-dropdown-menu bgg-formulabar-formula-dropdown-menu">' +
                                '<li><a data-anchor="btn" href="#">IIF()</a></li>' +
                                '<li><a data-anchor="btn" href="#">VAL()</a></li>' +
                                '<li><a data-anchor="btn" href="#">LPAD()</a></li>' +
                                '<li><a data-anchor="btn" href="#">LOWER()</a></li>' +
                                '<li><a data-anchor="btn" href="#">UPPER()</a></li>' +
                                '<li><a data-anchor="btn" href="#">DAYNAME()</a></li>' +
                            '</ul>' +
                        '</div>' +
                        '<input type="text" class="form-control bgg-formulabar-input-formula" />' +
                        '<span class="input-group-btn">' +
                            '<button type="button" class="btn btn-default bgg-formulabar-btn-formula-cancel"><i18n>Cancel</i18n></button>' +
                            '<button type="button" class="btn btn-primary bgg-formulabar-btn-formula-submit"><i18n>Save</i18n></button>' +
                        '</span>' +
                    '</div>' +
                '</form>' +
            '</div>'
    };

})(jQuery, window, document);
