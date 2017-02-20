/*
 * Big Grid Plugin - Column Drag Reorder
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    biggrid.plugin.columndragreorder = function(options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace
        var uid = this._generateUid(),
            ns = 'bgg-coldragreorder-'+uid;

        var me = this,
            opts = $.extend({}, biggrid.plugin.columndragreorder.defaults, options),
            grid = undefined,
            grid_model = undefined,
            grid_opts = undefined,
            grid_namespace = '',
            $body = $('body'),
            $grid_el = $(),
            $grid_body_wrapper = $(),
            $grid_fixed_header = $(),
            $grid_fixed_table = $(),
            $grid_data_header = $(),
            $grid_data_table = $(),
            $grid_hover_drop_target = $(),
            $grid_lock_drop_target = $(),
            $grid_column_drop_targets = $(),
            $grid_drop_targets = $(),
            grid_drag_sources = [], // array of drag source ids that are valid to drag from
            $draggables = $(),
            gutter_pct = Math.min(0.5, opts.gutterPct);

// -- public methods --

        this.init = function(biggrid)
        {
            grid = biggrid;
            grid_model = grid.getModel();
            grid_opts = grid.getOptions();
            grid_namespace = grid.getNamespace();
            $grid_el = grid.getElement();
            $grid_body_wrapper = $grid_el.find('.bgg-body-wrapper');
            $grid_fixed_header = $grid_el.find('.bgg-fixed-header');
            $grid_fixed_table = $grid_el.find('.bgg-table-grid-fixed');
            $grid_data_header = $grid_el.find('.bgg-data-header');
            $grid_data_table = $grid_el.find('.bgg-table-grid-data');

            // allow this grid to be a drag source for itself
            //me.addDragSource(grid_namespace);

            me._initEvents();

            return me;
        }

        this.destroy = function()
        {
            return me._uninitEvents().uninitDraggable();
        }

        this.getName = function()
        {
            return 'columndragreorder';
        }

        this.register = function()
        {
            if (grid instanceof $.biggrid)
                grid.registerPlugin('columndragreorder', me);

            return me;
        }

        this.unregister = function()
        {
            if (grid instanceof $.biggrid)
                grid.unregisterPlugin('columndragreorder');

            return me;
        }

        this.addDragSource = function(drag_source_id)
        {
            grid_drag_sources.push(drag_source_id);
            return me;
        }

        this.removeDragSource = function(drag_source_id)
        {
            grid_drag_sources = _.without(grid_drag_sources, drag_source_id);
            return me;
        }

        this.isValidDragSource = function(drag_source_id)
        {
            // should we deprecate this???
            return true;

            // only allow valid drag sources
            //if (_.indexOf(grid_drag_sources, drag_source_id) == -1)
            //    return me;
        }

        // if a custom helper is specified, it must have 'col-id' data
        // attached to it, otherwise the drop will fail
        this.initDraggable = function(selector, handle, helper)
        {
            if (typeof $.fn.draggable != 'function')
                return;

            if (selector === undefined)
                return;

            if (helper === undefined)
                helper = 'clone';

            if (handle === undefined)
                handle = false;

            var $drag_el = $(selector).addClass(opts.dragAcceptCls);

            $drag_el.draggable({
                appendTo: 'body',
                handle: handle,
                helper: helper,
                cursor: 'default',
                cursorAt: { top: 20, left: 40 },
                distance: 5,
                scroll: false,
                start: me._draggableStart,
                drag: me._draggableDrag
            });

            $draggables = $draggables.add($drag_el);

            return me;
        }

        this.uninitDraggable = function()
        {
            if (typeof $.fn.draggable != 'function')
                return;

            $draggables.each(function() {
                var $drag_el = $(this);
                if ($drag_el.hasClass('ui-draggable'))
                    $drag_el.draggable('destroy');
            });

            $draggables = $();

            return me;
        }

// -- private methods --

        this._initEvents = function()
        {
            me._uninitEvents();

            $grid_fixed_header.on('mouseenter.'+ns, 'th', me.onGridHeaderCellMouseEnter);
            $grid_data_header.on('mouseenter.'+ns, 'th', me.onGridHeaderCellMouseEnter);

            return me;
        }

        this._uninitEvents = function()
        {
            $grid_fixed_header.off('.'+ns);
            $grid_data_header.off('.'+ns);

            return me;
        }

        // -- draggable methods --

        this._draggableStart = function(evt, ui)
        {
            var drag_info = ui.helper.data('drag_data'),
                drag_source_id = drag_info.source_id;

            // only allow valid drag sources
            if (!me.isValidDragSource(drag_source_id))
                return me;

            // don't start the drag if we're renaming or resizing a column
            if (grid.hasState('bgg-state-col-renaming') ||
                grid.hasState('bgg-state-col-resizing'))
            {
                evt.preventDefault();
                return;
            }

            var drag_info = ui.helper.data('drag_data'),
                drag_cols = drag_info.columns,
                drag_source_id = drag_info.source_id;

            if (drag_cols === undefined)
                drag_cols = [];

            grid.addState('bgg-state-col-dragging');

            // don't allow scrollbars on body element while dragging
            $body.css('overflow', 'hidden');

            // make sure nothing is selected when dragging
            grid.clearCellSelection();
            grid.clearColumnSelection();
            grid.clearRowSelection();

            me._initDroppable();
            me._initChildDropTargets();

            if (drag_cols.length > 0)
            {
                // convert column array to a CSS string selector
                drag_cols = _.map(drag_cols, function(col_id) {
                    return '.'+col_id;
                });
                drag_cols = drag_cols.join(',');

                // shade out the drag column
                $grid_data_header.find(drag_cols).addClass('bgg-col-faded');
                $grid_data_table.find(drag_cols).addClass('bgg-col-faded');
            }

            me._updateHoverDropTarget(evt.pageX, evt.pageY, ui);

            return me;
        }

        this._draggableDrag = function(evt, ui)
        {
            var drag_info = ui.helper.data('drag_data'),
                drag_source_id = drag_info.source_id;

            // only allow valid drag sources
            if (!me.isValidDragSource(drag_source_id))
                return me;

            me._updateHoverDropTarget(evt.pageX, evt.pageY, ui);
            return me;
        }

        this._draggableDrag = _.throttle(this._draggableDrag, 1);

        // -- droppable methods --

        this._initDroppable = function()
        {
            if (typeof $.fn.droppable != 'function')
                return me;

            $grid_el.droppable({
                accept: '.'+opts.dragAcceptCls,
                deactivate: me._droppableDeactivate,
                drop: me._droppableDrop
            });

            return me;
        }

        this._uninitDroppable = function()
        {
            if (typeof $.fn.droppable != 'function')
                return;

            $grid_el.droppable('destroy');

            return me;
        }

        this._droppableDeactivate = function(evt, ui)
        {
            var drag_info = ui.helper.data('drag_data'),
                drag_cols = drag_info.columns,
                drag_source_id = drag_info.source_id;

            // only allow valid drag sources
            if (!me.isValidDragSource(drag_source_id))
                return me;

            if (drag_cols === undefined)
                drag_cols = [];

            if (drag_cols.length > 0)
            {
                // convert column array to a CSS string selector
                drag_cols = _.map(drag_cols, function(col_id) {
                    return '.'+col_id;
                });
                drag_cols = drag_cols.join(',');

                // restore the drag column
                $grid_data_header.find(drag_cols).removeClass('bgg-col-faded');
                $grid_data_table.find(drag_cols).removeClass('bgg-col-faded');
            }

            // get rid of the column drop targets
            $grid_column_drop_targets.height(0);

            // remove the column lock drop target
            $grid_lock_drop_target.remove();

            ui.helper.remove();

            // reset body overflow to whatever it was before the drag
            $body.css('overflow', '');

            grid.removeState('bgg-state-col-dragging');

            me._uninitChildDropTargets();
            me._uninitDroppable();

            return me;
        }

        this._droppableDrop = function(evt, ui)
        {
            var drag_info = ui.helper.data('drag_data'),
                drag_cols = drag_info.columns,
                drag_source_id = drag_info.source_id;

            // only allow valid drag sources
            if (!me.isValidDragSource(drag_source_id))
                return me;

            if (drag_cols.length == 0)
                return me;

            // we're not dropping on a valid drop target; bail out
            if ($grid_hover_drop_target.length == 0)
                return me;

            var drop_col_id = $grid_hover_drop_target.data('col-id'),
                drop_position = grid.getColumnPosition(drop_col_id);

            if (drop_col_id == grid_opts.rowHandleCls) // dropping on freeze column drop target
            {
                grid.bulkFreezeColumns(drag_cols).refreshAll();
            }
             else if (drag_cols.length == 1) // only dragging one column
            {
                var drag_col_id = drag_cols[0];

                if (drop_col_id != drag_col_id)
                {
                    // if the dragged column's position is greater than
                    // the drop position, decrement the drop position by one
                    if (drop_position > grid.getColumnPosition(drag_col_id))
                        drop_position -= 1;

                    grid.showColumn(drag_col_id)
                        .unfreezeColumn(drag_col_id)
                        .setColumnPosition(drag_col_id, drop_position)
                        .refreshAll();
                }
            }
             else // dragging multiple columns
            {
                drag_cols = _.sortBy(drag_cols, function(col_id) {
                    return grid.getColumnPosition(col_id);
                });

                _.each(drag_cols, function(col_id) {
                    var drag_position = grid.getColumnPosition(col_id);

                    // if the first dragged column's position is greater than
                    // the drop position, decrement the drop position by one
                    if (drop_position > drag_position)
                        drop_position -= 1;

                    grid.showColumn(col_id)
                        .unfreezeColumn(col_id)
                        .setColumnPosition(col_id, drop_position);

                    drop_position++;
                });

                grid.refreshAll();
            }

            return me;
        }

        // -- child/hover drop target methods --

        this._initChildDropTargets = function()
        {
            // make the column drop targets extend to the bottom of the grid
            var h = 0 +
                $grid_data_header.height() +
                $grid_body_wrapper.height() -
                grid._getScrollbarSize().height;

            // make the freeze column drop target the same width as the row handle
            var $top_left_cell = $grid_fixed_header.find('.'+grid_opts.rowHandleCls),
                w = $top_left_cell.outerWidth(true);

            $grid_lock_drop_target = $(opts.lockDropTargetMarkup).insertBefore($grid_fixed_header);
            $grid_lock_drop_target.width(w).attr('data-col-id', grid_opts.rowHandleCls);

            $grid_column_drop_targets = $grid_data_header.find('.'+grid_opts.colReorderDropTargetCls);
            $grid_column_drop_targets.height(h);

            $grid_drop_targets = $().add($grid_column_drop_targets).add($grid_lock_drop_target);

            // add the lock icon to the top-left cell
            $grid_lock_drop_target.html('<h4><span class="fa fa-lock fa-2x"></span></h4>');

            return me;
        }

        this._uninitChildDropTargets = function()
        {
            // remove the column lock drop target
            $grid_lock_drop_target.remove();

            $grid_hover_drop_target = $();
            $grid_lock_drop_target = $();
            $grid_column_drop_targets = $();
            $grid_drop_targets = $();

            return me;
        }

        // since we are using a single drop target and want to create
        // the effect of having a bunch of child drop targets, we need
        // to get the bounding rectangle of these child drop targets
        // in order to test whether we're hovering over them
        this._getHoverDropTarget = function(x, y, ui)
        {
            var hover_idx = undefined;

            $grid_drop_targets.each(function(idx) {
                var rect = this.getBoundingClientRect(),
                    width = this.clientWidth,
                    gutter = width*gutter_pct;

                // vertically out-of-bounds
                if (y < rect.top || y > rect.bottom)
                    return;

                // always return the row handle drop target
                if (this.getAttribute('data-col-id') == grid_opts.rowHandleCls)
                {
                    if (x >= rect.left && x <= rect.right)
                    {
                        hover_idx = idx;
                        return false;
                    }
                }

                // we're in the center of the column drop target
                if (x >= rect.left+gutter && x <= rect.right-gutter)
                {
                    // use stored hover drop target
                    if ($grid_hover_drop_target.length > 0)
                    {
                        hover_idx = 'keep-existing';
                        return false;
                    }

                    hover_idx = idx;
                    return false;
                }

                // we're on the left of the column drop target
                if (x >= rect.left && x < rect.left+gutter)
                {
                    hover_idx = idx;
                    return false;
                }

                // we're on the right of the column drop target
                if (x > rect.right-gutter && x <= rect.right)
                {
                    hover_idx = idx+1;
                    return false;
                }
            });

            if (hover_idx === undefined)
                return $();

            if (hover_idx == 'keep-existing')
                return $grid_hover_drop_target;

            // handle lower and upper bounds
            hover_idx = Math.max(hover_idx, 0);
            hover_idx = Math.min(hover_idx, $grid_drop_targets.length-1)

            var el = $grid_drop_targets[hover_idx];
            return $(el);
        }

        this._updateHoverDropTarget = function(x, y, ui)
        {
            var $el = me._getHoverDropTarget(x, y, ui);

            // we're hovering over the same element; we're done
            if ($el.is($grid_hover_drop_target))
                return me;

            if ($el.length == 0)
            {
                // remove hover class from old drop target
                $grid_hover_drop_target.removeClass('bgg-droppable-state-hover');
                $grid_hover_drop_target = $();
                return me;
            }

            var drop_col_id = $el.closest('th').data('col-id');

            // remove hover class from old drop target
            $grid_hover_drop_target.removeClass('bgg-droppable-state-hover');

            // update the drop target item
            $grid_hover_drop_target = $el;

            // add drop column class data to new drop target
            $grid_hover_drop_target.data('col-id', drop_col_id);

            // add the hover styling
            $grid_hover_drop_target.addClass('bgg-droppable-state-hover');

            return me;
        }

        this._createDragHandle = function()
        {
            var $th = $(this),
                $handle = $('<div />'),
                $cells = $(),
                columns = [],
                top = 0,
                left = 0;

            if ($th.hasClass(grid_opts.colFrozenCls))
                $cells = $grid_fixed_header.find('th.bgg-col-selected');
                 else
                $cells = $grid_data_header.find('th.bgg-col-selected');

            $cells.each(function() {
                var $th = $(this),
                    $cell = $cells.first().find('.bgg-cell-text').clone();

                $cell.addClass('bgg-col-drag-helper');
                $cell.css({
                    'position': 'absolute',
                    'top': top+'px',
                    'left': left+'px'
                });

                $handle.append($cell);

                columns.push($th.data('col-id'));

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

                $handle.append($count_el);
            }

            return $handle
                .data('drag_data', {
                    'columns': columns,
                    'source_id': grid_namespace
                });
        }

// -- event handlers --

        // we do this on mouse enter instead of when we render the header
        // because this is an slow/expensive operation
        this.onGridHeaderCellMouseEnter = function(evt)
        {
            if (grid.hasState('bgg-state-col-dragging'))
                return;

            var $th = $(this),
                col_id = $th.data('col-id');

            // don't allow the column handle to be dragged
            if ($th.hasClass(grid_opts.rowHandleCls))
                return;

            // column is already draggable; we're done
            if ($th.hasClass('ui-draggable'))
                return;

            me.initDraggable($th, '.bgg-cell-text', me._createDragHandle);
        }
    }

    biggrid.plugin.columndragreorder.defaults = {
        gutterPct: 0.25,
        dragAcceptCls: 'bgg-draggable-accept',
        lockDropTargetMarkup: '<div class="bgg-col-freeze-drop-target">&nbsp;</div>'
    };

})(jQuery, window, document);
