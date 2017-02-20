/*
 * Big Grid Array Model
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    window.BiggridArrayModel = function(options)
    {
        var me = this,
            cols = [],
            rows = [],
            opts = $.extend({}, BiggridArrayModel.defaults, options);

// -- public methods --

        this.init = function()
        {
            cols = opts.columns;
            rows = opts.rows;
            return me;
        }

        this.uninit                    = function()        { return me;            }

        this.reset                     = function()        { return me;            }
        this.resetRowCache             = function()        { return me;            }
        this.isFastGetRows             = function()        { return true;          }
        this.getKeyType                = function()        { return 'index-based'; }

        this.isCalculatedColumnAllowed = function()        { return false;         }
        this.alterCalculatedColumn     = function()        { return false;         }
        this.getColumnCount            = function()        { return cols.length;   }
        this.getRowCount               = function()        { return rows.length;   }

        this.getColumns = function(options)
        {
            if (options && options.callback && typeof options.callback == "function")
            {
                options.callback({
                    success: true,
                    columns: cols
                });
            }

            return cols;
        }

        this.getRows = function(options)
        {
            var start = options.start || opts.start,
                limit = options.limit || opts.limit,
                end = start + limit

            // handle out-of-bounds
            if (end > rows.length)
                end = rows.length;

            var row_slice = rows.slice(start, end);

            if (options && options.callback && typeof options.callback == "function")
            {
                options.callback({
                    success: true,
                    rows: row_slice
                });
            }

            return row_slice;
        }

        me.init();
    }
    BiggridArrayModel.prototype.constructor = BiggridArrayModel;

// -- default options --

    BiggridArrayModel.defaults = {
        columns: [],
        rows: [],
        start: 0,
        limit: 10
    };

})(jQuery, window, document);
