/*
 * Big Grid Plugin - Column Double Click Rename
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    biggrid.plugin.columndblclickrename = function(options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace
        var uid = this._generateUid(),
            ns = 'bgg-coldblclickrename-'+uid;

        var me = this,
            opts = $.extend({}, biggrid.plugin.columndblclickrename.defaults, options),
            grid = undefined,
            grid_model = undefined,
            grid_opts = undefined,
            $grid_el = $();

// -- public methods --

        this.init = function(biggrid)
        {
            grid = biggrid;
            grid_model = grid.getModel();
            grid_opts = grid.getOptions();
            $grid_el = grid.getElement();

            me._initEvents();

            return me;
        }

        this.destroy = function()
        {
            return me._uninitEvents();
        }

        this.getName = function()
        {
            return 'columndblclickrename';
        }

        this.register = function()
        {
            if (grid instanceof $.biggrid)
                grid.registerPlugin('columndblclickrename', me);

            return me;
        }

        this.unregister = function()
        {
            if (grid instanceof $.biggrid)
                grid.unregisterPlugin('columndblclickrename');

            return me;
        }

// -- private methods --

        this._initEvents = function()
        {
            me._uninitEvents();

            $grid_el.on('headercelldblclick.biggrid.'+ns, me.onGridHeaderCellDoubleClick);

            return me;
        }

        this._uninitEvents = function()
        {
            $grid_el.off('.'+ns);

            return me;
        }

// -- event handlers --

        this.onGridHeaderCellDoubleClick = function(evt, data)
        {
            if (data.col_id === grid_opts.colInvalidCls)
                return;

            grid.startHeaderCellEdit(data.col_id);
        }
    }

    biggrid.plugin.columndblclickrename.defaults = { };

})(jQuery, window, document);
