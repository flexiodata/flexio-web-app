/*
 * Big Grid Test Model
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    window.BiggridTestModel = function(options)
    {
        var me = this,
            cols = [],
            row_cache = [],
            column_count = undefined,
            row_count = undefined,
            opts = $.extend({}, BiggridTestModel.defaults, options);

// -- public methods --

        this.init = function()
        {
            column_count = opts.column_count;
            row_count = opts.row_count;
            return me;
        }

        this.uninit                    = function()        { return me;            }

        this.reset                     = function()        { return me;            }
        this.resetRowCache             = function()        { return me;            }
        this.isFastGetRows             = function()        { return true;          }
        this.getKeyType                = function()        { return 'index-based'; }

        this.isCalculatedColumnAllowed = function()        { return false;         }
        this.alterCalculatedColumn     = function()        { return false;         }
        this.getColumnCount            = function()        { return column_count;  }
        this.getRowCount               = function()        { return row_count;     }

        this.getColumns = function(options)
        {
            var col_data = [];

            for (var i = 0; i < opts.column_count; ++i)
            {
                col_data[i] = {
                    name: 'Column '+(i+1)
                };
            }

            cols = col_data;

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
                limit = options.limit || opts.limit;

            var row_data = [],
                cell_data,
                end = (start+limit);

            for (var i = start; i < end; ++i)
            {
                cell_data = [];
                for (var j = 0; j < opts.column_count; ++j)
                {
                    var s = '\tRow '+(i+1)+', Column '+(j+1)+'\t';
                    cell_data.push(s);
                }
                row_data.push(cell_data);

                if (i >= opts.row_count)
                    break;
            }

            row_cache = row_data;

            if (options && options.callback && typeof options.callback == "function")
            {
                options.callback({
                    success: true,
                    rows: row_cache
                });
            }

            return row_cache;
        }

        me.init();
    }
    BiggridTestModel.prototype.constructor = BiggridTestModel;

// -- default options --

    BiggridTestModel.defaults = {
        column_count: 50,
        row_count: 100000,
        start: 0,
        limit: 10
    };

})(jQuery, window, document);
