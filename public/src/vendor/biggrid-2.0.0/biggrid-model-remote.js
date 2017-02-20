/*
 * Big Grid Remote Model
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    // translate token function
    function _T(s) { return s; }

    window.BiggridRemoteModel = function(options)
    {
        var me = this,
            cols = [],
            row_cache = [],
            row_cache_count = 0,
            row_count = undefined,
            rows_res_cache = undefined,
            handle = undefined,
            cols_xhr = undefined,
            rows_xhr = undefined,
            calc_column_xhr = undefined,
            fast_get_rows = false,
            opts = $.extend(true, {}, BiggridRemoteModel.defaults, options);

// -- public methods --

        this.init = function()
        {
            handle = opts.defaultHandle;
            return me;
        }

        this.uninit = function()
        {
            return me._closeHandle(false);
        }

        this.reset = function()
        {
            // close out our old handle and clear out the row cache
            me._closeHandle(true);
            return me._invalidateRowCache();
        }

        this.resetRowCache = function()
        {
            return me._invalidateRowCache();
        }

        this.isFastGetRows = function()
        {
            return fast_get_rows;
        }

        this.getKeyType = function()
        {
            return 'name-based';
        }

        this.isCalculatedColumnAllowed = function()
        {
            return true;
        }

        this.alterCalculatedColumn = function(options)
        {
            if (handle === undefined)
                return false;

            // if the response from the last request to the server isn't ready yet,
            // cancel that request since we're about to issue a new one
            if (calc_column_xhr && calc_column_xhr.readyState != 4)
                calc_column_xhr.abort();

            var alter_action = options.action,
                query_str = opts.rowsQuery+'/alter';

            // omit the handle when saving the changes
            if (alter_action.indexOf('save') == -1)
                query_str += '?handle='+handle;

            var post_options = $.extend({}, options),
                post_data = {};

            // we'll take care of specifying this below
            delete post_options.action;

            if (alter_action.indexOf('create') == -1)
                delete post_options.name;
                 else
                delete post_options.target_column;

            // don't pass the callback function to the backend
            if (post_options.callback !== undefined)
                delete post_options.callback;

            switch (alter_action)
            {
                // creating a new calculated column
                default:
                case 'create':
                    post_data = $.extend({}, {
                        action: 'create',
                        name: undefined,
                        type: undefined,
                        width: undefined,
                        scale: undefined,
                        expression: undefined,
                        calculated: undefined
                    }, post_options);
                    break;

                // saving a newly created calculated column
                case 'save-create':
                    post_data = $.extend({}, {
                        action: 'create',
                        name: undefined,
                        type: undefined,
                        width: undefined,
                        scale: undefined,
                        expression: undefined,
                        calculated: undefined
                    }, post_options);
                    break;

                // saving an existing calculated column
                case 'save-edit':
                    post_data = $.extend({}, {
                        action: 'modify',
                        target_column: undefined,
                        expression: undefined
                    }, post_options);
                    break;

                // starting an edit of an existing calculated column
                case 'edit':
                    post_data = $.extend({}, {
                        action: 'modify',
                        target_column: undefined,
                        expression: undefined
                    }, post_options);
                    break;

                // modifying a newly created or existing calculated column
                case 'modify':
                    post_data = $.extend({}, {
                        action: 'modify',
                        target_column: undefined,
                        expression: undefined
                    }, post_options);
                    break;
            }

            calc_column_xhr = $.ajax({
                type: 'POST',
                dataType: 'json',
                data: post_data,
                url: query_str,
                success: function(res) {
                    if (typeof options.callback == 'function')
                        options.callback(res);
                },
                error: me._xhrErrorFn
            });

            return me;
        }

        this.getColumnCount = function()
        {
            return cols.length;
        }

        this.getRowCount = function()
        {
            return row_count;
        }

        this.getColumns = function(options)
        {
            // if the response from the last request to the server isn't ready yet,
            // cancel that request since we're about to issue a new one
            if (cols_xhr && cols_xhr.readyState != 4)
                cols_xhr.abort();

            var start = options.start || opts.defaultStart,
                limit = options.limit || opts.defaultLimit,
                query_str = '';

            var query_str = opts.rowsQuery+'/content?metadata=true';

            if (opts.getRowsWithColumns === true)
            {
                if (query_str.indexOf('?') === -1)
                    query_str += '?start='+start+'&limit='+limit;
                     else
                    query_str += '&start='+start+'&limit='+limit;
            }

            if (handle === undefined)
                query_str += '&handle=create';
                 else
                query_str += '&handle='+handle;

            cols_xhr = $.ajax({
                dataType: 'json',
                url: query_str,
                success: function(res) {
                    if (!res)
                    {
                        if (typeof options.callback == 'function')
                        {
                            options.callback({
                                success: false,
                                msg: _T('Column query result is undefined')
                            });
                        }

                        return;
                    }

                    if (res.success)
                    {
                        cols = res.columns;

                        // check to see if other information was passed along with the columns;
                        // if rows were passed, fill out all the same variables as the getRows() call
                        if (res.rows !== undefined)
                        {
                            me._updateRowsXhrResultCache(res);
                            me._populateRowCache(res.rows, start, limit);
                        }

                        if (typeof options.callback == 'function')
                            options.callback(res);

                        // if the total row count is less than the background
                        // loading threshold, cache all rows
                        if (opts.cacheAll === true && row_count <= opts.maxRowCountForCacheAll)
                            me._cacheAllRows();
                    }
                     else
                    {
                        res.columns = null;
                        res.rows = null;

                        if (typeof options.callback == 'function')
                            options.callback(res);
                    }
                },
                error: me._xhrErrorFn
            });

            return me;
        }

        this.getRows = function(options)
        {
            var start = options.start || opts.defaultStart,
                limit = options.limit || opts.defaultLimit;

            // try to use the local row cache
            var rows = [];
            if (me._getResultFromRowCache(rows, start, limit) !== false)
            {
                // asking for a subset of rows in our row cache
                var res = rows_res_cache;
                res.rows = rows;

                if (typeof options.callback == 'function')
                    options.callback(res);

                return me;
            }

            // if the response from the last request to the server isn't ready yet,
            // cancel that request since we're about to issue a new one
            if (rows_xhr && rows_xhr.readyState != 4)
                rows_xhr.abort();

            var query_str = opts.rowsQuery+'/content'+'?start='+start+'&limit='+limit;

            if (handle === undefined)
                query_str += '&handle=create';
                 else
                query_str += '&handle='+handle;

            if (options.extraParams !== undefined)
            {
                $.each(options.extraParams, function(key, val) {
                    query_str += '&'+key+'='+val;
                });
            }

            rows_xhr = $.ajax({
                dataType: 'json',
                url: query_str,
                success: function(res) {
                    if (!res)
                    {
                        if (typeof options.callback == 'function')
                        {
                            options.callback({
                                success: false,
                                msg: _T('Row query result is undefined')
                            });
                        }

                        return;
                    }

                    if (res.success)
                    {
                        me._updateRowsXhrResultCache(res);
                        me._populateRowCache(res.rows, start, limit);

                        if (typeof options.callback == 'function')
                            options.callback(res);

                        // if the total row count is less than the background
                        // loading threshold, cache all rows
                        if (opts.cacheAll === true && row_count <= opts.maxRowCountForCacheAll)
                            me._cacheAllRows();
                    }
                     else
                    {
                        res.rows = null;

                    if (typeof options.callback == 'function')
                            options.callback(res);
                    }
                },
                error: me._xhrErrorFn
            });

            return me;
        }

// -- public methods (this model only) --

        this.setHandle = function(_handle)
        {
            // NOTE: it is up to the caller to make sure the old handle is closed!
            handle = _handle;
            return me;
        }

        this.getHandle = function()
        {
            return handle;
        }

        /*
        this.buildSortString = function(sort_arr)
        {
            if (sort_arr.length == 0)
                return '';

            var sort_strs = [],
                sort_str = '';

            _.each(sort_arr, function(sort) {
                var dir = (sort.direction.toLowerCase() == 'desc') ? 'desc' : 'asc',
                    col_name = sort.column,
                    col_sort_str = '';

                if (col_name.indexOf(' ') != -1)
                    col_name = '"' + col_name + '"';

                col_sort_str = col_name + ' ' + dir;

                // append sort string to sort strings array
                sort_strs.push(col_sort_str);
            });

            // build up sort string from multiple sort criteria
            if (sort_strs.length > 1)
            {
                sort_str = sort_strs.join(', ');
            }
             else
            {
                sort_str = sort_strs[0];
            }

            return sort_str;
        }

        this.buildFilterString = function(filter_arr)
        {
            if (filter_arr.length == 0)
                return '';

            var filter_strs = [],
                filter_str = '';

            _.each(filter_arr, function(filter) {
                // create this filter string
                var str = _.map(filter.columns, function(col) {
                    var col_name = col.name,
                        col_type = col.type || 'character',
                        filter_val = filter.value,
                        filter_op = filter.operator,
                        expr = '';

                    if (col_name.indexOf(' ') != -1)
                        col_name = '"' + col_name + '"';

                    if (col_type == 'character' || col_type == 'widecharacter')
                    {
                        if (filter_op != 'like')
                            filter_val = "'"+filter_val+"'";
                    }

                    if (col_type == 'numeric' || col_type == 'double' || col_type == 'boolean' || col_type == 'datetime'  || col_type == 'date')
                    {
                        if (filter.operator == 'like')
                            filter_op = 'eq';
                    }

                    if (filter_op == 'like')
                        expr = col_name + " LIKE '%"+filter_val+"%'";
                         else if (filter_op == 'eq')
                        expr = col_name + " = " + filter_val;
                         else if (filter_op == 'neq')
                        expr = col_name + " != " + filter_val;
                         else if (filter_op == 'gt')
                        expr = col_name + " > " + filter_val;
                         else if (filter_op == 'gte')
                        expr = col_name + " >= " + filter_val;
                         else if (filter_op == 'lt')
                        expr = col_name + " < " + filter_val;
                         else if (filter_op == 'lte')
                        expr = col_name + " <= " + filter_val;

                    return expr;
                }).join(' OR ');

                // append filter string to filter strings array
                filter_strs.push(str);
            });

            // build up filter string from multiple filter criteria
            if (filter_strs.length > 1)
            {
                filter_str =_.map(filter_strs, function(str) {
                    return '('+ str + ')';
                }).join(' AND ');
            }
             else
            {
                filter_str = filter_strs[0];
            }

            return filter_str;
        }
        */

// -- private methods --

        this._xhrErrorFn = function(_xhr)
        {
            try
            {
                var res_text = _xhr.responseText,
                    res = JSON.parse(res_text);

                res.columns = null;
                res.rows = null;

                // TODO: fix this; we're outside of the calling scope,
                //       so options is always undefined here
                if (typeof options.callback == 'function')
                    options.callback(res);
            }
            catch (e)
            {
                // TODO: fix this; we're outside of the calling scope,
                //       so options is always undefined here
                if (typeof options.callback == 'function')
                {
                    options.callback({
                        success: false,
                        message: _T('An unexpected error occurred.  Please try again shortly.')
                    });
                }
            }
        }

        this._closeHandle = function(async)
        {
            if (opts.rowsQuery !== undefined && handle !== undefined)
            {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: opts.rowsQuery + '?m=close&handle=' + handle,
                    async: (async === false) ? false : true
                });
            }

            handle = undefined;

            return me;
        }

        this._updateRowsXhrResultCache = function(res)
        {
            if (res.handle)
                handle = res.handle;

            if (res.total_count)
                row_count = parseFloat(''+res.total_count);

            rows_res_cache = res;

            return me;
        }

        this._cacheAllRows = function()
        {
            // caching all rows is disabled, we're done
            if (opts.cacheAll !== true)
                return me;

            // we've already cached all the rows, we're done
            if (row_cache_count == row_count)
                return me;

            me.getRows({ start: 0, limit: row_count });

            return me;
        }

        this._getResultFromRowCache = function(rows, start, limit)
        {
            if (row_cache.length == 0)
                return false;

            // if all desired rows are present in the row cache,
            // return a row array with the requested rows,
            // otherwise return false
            for (var r = start; r < start+limit && r < row_count; ++r)
            {
                if (row_cache.hasOwnProperty(r))
                    rows.push(row_cache[r]);
                     else
                    return false;
            }

            return rows;
        }

        this._populateRowCache = function(rows, start, limit)
        {
            var idx = 0;
            for (var r = start; r < start+limit && r < row_count; ++r)
            {
                if (row_cache_count == opts.maxRowCache)
                    return me;

                // if the row didn't exist in the cache, increment our row cache count variable
                if (row_cache[r] === undefined)
                    row_cache_count++;

                if (row_cache_count == row_count)
                    fast_get_rows = true;

                row_cache[r] = rows[idx];
                idx++;
            }

            return me;
        }

        this._invalidateRowCache = function()
        {
            fast_get_rows = false;
            row_cache = [];
            row_cache_count = 0;
            return me;
        }

        this._invalidateColumns = function()
        {
            cols = [];
            return me;
        }

        me.init();
    }
    BiggridRemoteModel.prototype.constructor = BiggridRemoteModel;

// -- default options --

    BiggridRemoteModel.defaults = {
        columnsQuery: '',
        rowsQuery: '',
        defaultStart: 0,
        defaultLimit: 120,
        defaultHandle: undefined,
        cacheAll: true,
        getRowsWithColumns: true,
        maxRowCountForCacheAll: 5000,
        maxRowCache: 5000
    };

})(jQuery, window, document);
