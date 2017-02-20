/*
 * Big Grid Plugin - Column Double Click Rename
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    biggrid.plugin.columnhighlight = function(options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace
        var uid = this._generateUid(),
            prefix = 'bgg-colhighlight-',
            title_cls = 'bgg-colhighlight-title',
            ns = prefix+uid;

        var me = this,
            opts = $.extend({}, biggrid.plugin.columnhighlight.defaults, options),
            grid = undefined,
            grid_model = undefined,
            grid_opts = undefined,
            $grid_el = $(),
            $grid_data_header = $(),
            $grid_fixed_header = $(),
            $grid_header = $(),
            $grid_body = $();

        var highlight_cols = {};

// -- public methods --

        this.init = function(biggrid)
        {
            grid = biggrid;
            grid_model = grid.getModel();
            grid_opts = grid.getOptions();
            $grid_el = grid.getElement();
            $grid_data_header = $grid_el.find('.bgg-data-header');
            $grid_fixed_header = $grid_el.find('.bgg-fixed-header');
            $grid_header = $().add($grid_fixed_header).add($grid_data_header);
            $grid_body = $grid_el.find('.bgg-body');

            return me._initRender();
        }

        this.destroy = function()
        {
            return me._uninitRender();
        }

        this.getName = function()
        {
            return 'columnhighlight';
        }

        this.register = function()
        {
            if (grid instanceof $.biggrid)
                grid.registerPlugin('columnhighlight', me);

            return me;
        }

        this.unregister = function()
        {
            if (grid instanceof $.biggrid)
                grid.unregisterPlugin('columnhighlight');

            return me;
        }

        this.addColumn = function(col_id, col, refresh)
        {
            if (!_.isObject(col))
                return;

            highlight_cols[col_id] = col;

            if (refresh !== false)
                grid.refreshAll();
        }

        this.removeColumn = function(col_id, refresh)
        {
            highlight_cols = _.omit(highlight_cols, col_id);

            if (refresh !== false)
                grid.refreshAll();
        }

        this.setColumns = function(cols, refresh)
        {
            if (!_.isObject(cols))
                return;

            highlight_cols = cols;

            if (refresh !== false)
                grid.refreshAll();
        }

        this.clearColumns = function(refresh)
        {
            highlight_cols = {};

            if (refresh !== false)
                grid.refreshAll();
        }

// -- private methods --

        this._initEvents = function()
        {
            me._uninitEvents();

            $grid_el.on('renderheader.biggrid.'+ns, me.onGridRenderHeader);
            $grid_el.on('renderrows.biggrid.'+ns, me.onGridRenderRows);

            return me;
        }

        this._uninitEvents = function()
        {
            $grid_el.off('.'+ns);

            return me;
        }

        this._doRender = function()
        {
            $grid_header.find('.'+title_cls).remove();
            $grid_header.find('th').prepend('<div class="'+title_cls+'">&nbsp;</div>');

            _.each(highlight_cols, function(c, col_id) {
                var col_info = grid.getColumnInfo(col_id);

                if (!col_info)
                    return;

                var $th = $grid_header.find('th.'+col_id),
                    $td = $grid_body.find('td.'+col_id);

                $th.find('.'+title_cls)
                    .text(c.title)
                    .css('background', c.bg_color)
                    .css('color', c.color || '#333');

                $td.css('background', tinycolor.mix('#ffffff', c.bg_color, 12).toRgbString());
                $th.css('background', tinycolor.mix('#f5f5f5', c.bg_color, 16).toRgbString());
            });
            return me;
        }

        this._initRender = function()
        {
            grid.setHeaderHeight(46);

            me._doRender();
            me._initEvents();
            return me;
        }

        this._uninitRender = function()
        {
            me._uninitEvents();
            me.clearColumns(false);

            //$grid_el.css('border-top-width', this._stored['border-top-width']);
            $grid_header.find('.'+title_cls).remove();

            grid.setHeaderHeight('auto');
            grid.refreshAll();
            return;
        }

// -- event handlers --

        this.onGridRenderHeader = function(evt)
        {
            me._doRender();
        }

        this.onGridRenderRows = function(evt)
        {
            me._doRender();
        }
    }

    biggrid.plugin.columnhighlight.defaults = { };

})(jQuery, window, document);
