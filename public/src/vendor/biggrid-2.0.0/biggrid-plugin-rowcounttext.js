/*
 * Big Grid Plugin - Row Count Text
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    biggrid.plugin.rowcounttext = function(options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace
        var uid = this._generateUid(),
            ns = 'bgg-trcounttext-'+uid;

        var me = this,
            opts = $.extend({}, biggrid.plugin.rowcounttext.defaults, options),
            grid = undefined,
            $el = $(),
            $grid_el = $(),
            $grid_utility_bar = $(),
            $rowcounttext_label = $();

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
            return me;
        }

        this.getName = function()
        {
            return 'rowcounttext';
        }

        this.register = function()
        {
            if (grid instanceof $.biggrid)
                grid.registerPlugin('rowcounttext', me);

            return me;
        }

        this.unregister = function()
        {
            if (grid instanceof $.biggrid)
                grid.unregisterPlugin('rowcounttext');

            return me;
        }

// -- private methods --

        this._initEvents = function()
        {
            me._uninitEvents();

            $grid_el.on('rowcounttextupdate.biggrid.'+ns, me.onGridRowCountTextUpdate);

            return me;
        }

        this._uninitEvents = function()
        {
            $grid_el.off('.'+ns);

            return me;
        }

        this._render = function()
        {
            $el = $(opts.markup);
            $rowcounttext_label = $el.find('.bgg-trcounttext-label');

            if (opts.appendTo !== undefined)
                $(opts.appendTo).append($el);
                 else
                $grid_utility_bar.append($el.addClass('bgg-utility-bar-inner'));

            return me;
        }

// -- event handlers --

        this.onGridRowCountTextUpdate = function(evt, str)
        {
            $rowcounttext_label.text(str);
        }
    }

    biggrid.plugin.rowcounttext.defaults = {
        appendTo: undefined,
        markup: '' +
            '<div class="bgg-trcounttext pull-right">' +
                '<label class="bgg-trcounttext-label"></label>' +
            '</div>'
    };

})(jQuery, window, document);
