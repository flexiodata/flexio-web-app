/*
 * Big Grid Flex.io App Grid Model
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, _, window, document, undefined) {
    'use strict';

    function _T(s) { return s; }

    window.FxAppGridModel = function(options)
    {
        var me = this,
            cols = [],
            cols_res_cache = undefined,
            row_cache = {},
            row_cache_count = 0,
            row_count = undefined,
            rows_res_cache = undefined,
            cols_xhr = undefined,
            rows_xhr = undefined,
            calc_column_xhr = undefined,
            row_query_str = undefined,
            col_query_str = undefined,
            fast_get_rows = false,
            opts = $.extend(true, {}, FxAppGridModel.defaults, options);

// -- public methods --

        this.init = function()
        {
            row_query_str = opts.rowsUrl
            col_query_str = opts.colsUrl

            return me;
        }

        this.uninit = function()
        {
            return me;
        }

        this.reset = function()
        {
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
            // we've already retrieved the columns, we're done
            if (cols_res_cache !== undefined)
            {
                options.callback(cols_res_cache);
                return me;
            }

            // if the response from the last request to the server isn't ready yet,
            // cancel that request since we're about to issue a new one
            if (cols_xhr && cols_xhr.readyState != 4)
                cols_xhr.abort();

            var start = options.start || opts.defaultStart,
                limit = options.limit || opts.defaultLimit,
                query_str = '';

            // make sure we don't query more than our forced row count
            if (opts.forceRowCount > 0)
                limit = Math.min(opts.forceRowCount, limit);

            var query_str = col_query_str+'?metadata=true';

            if (opts.getRowsWithColumns === true)
            {
                if (query_str.indexOf('?') === -1)
                    query_str += '?'
                     else
                    query_str += '&'

                query_str += 'start='+start+'&limit='+limit;
            }

            if (opts.useHandle === true)
            {
                if (query_str.indexOf('?') === -1)
                    query_str += '?'
                     else
                    query_str += '&'
            }

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
                        cols_res_cache = res;
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

            // make sure we don't query more than our forced row count
            if (opts.forceRowCount > 0)
                limit = Math.min(opts.forceRowCount, limit);

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

            var query_str = row_query_str+'?start='+start+'&limit='+limit;

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

        this._updateRowsXhrResultCache = function(res)
        {
            if (res.total_count)
                row_count = parseFloat(''+res.total_count);

            if (opts.forceRowCount > 0)
                row_count = Math.min(opts.forceRowCount, row_count);

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
            if ($.isEmptyObject(row_cache))
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
            row_cache = {};
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
    FxAppGridModel.prototype.constructor = FxAppGridModel;

// -- default options --

    FxAppGridModel.defaults = {
        rowsUrl: '',
        colsUrl: '',
        defaultStart: 0,
        defaultLimit: 100,
        defaultHandle: undefined,
        useHandle: false,
        cacheAll: false,
        forceRowCount: 0, // if set to a number above 0, the model will only return that many rows
        getRowsWithColumns: true,
        maxRowCountForCacheAll: 5000,
        maxRowCache: 5000
    };

})(jQuery, _, window, document);
