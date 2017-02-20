/*
 * jQuery Big Grid
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, _, window, document, undefined) {
    'use strict';

    /*
        requires:
            - jQuery resize event (http://benalman.com/projects/jquery-resize-plugin)
            - _.withoutObject
            - $.fn.prevUntil2
            - $.fn.nextUntil2
            - $.fn.adjacentUntil
    */

    // translate token function
    function _T(s) { return s; }








    // TODO: remove this; added to remove dependency on dateutil.js
    window.FxDateUtil = function()
    {
        // establish solid scope variable
        var me = this;

        // -- public methods --

        this.parse = function(str)
        {
            return Date.parse(str);
        }

        this.format = function(dt, fmt)
        {
            if (dt === undefined)
                return '';

            if (fmt === undefined || fmt == '')
                return dt.toString();

            var retval = '',
                replace = me.replaceChars;

            for (var i = 0; i < fmt.length; i++)
            {
                var cur_char = fmt.charAt(i);
                if (i - 1 >= 0 && fmt.charAt(i - 1) == "\\")
                {
                    retval += cur_char;
                }
                 else if (replace[cur_char])
                {
                    retval += replace[cur_char].call(dt);
                }
                 else if (cur_char != "\\")
                {
                    retval += cur_char;
                }
            }

            return retval;
        }

        // these date strings are used in the individual format functions below
        this.locale = {
            shortMonths: [
                _T('Jan'),
                _T('Feb'),
                _T('Mar'),
                _T('Apr'),
                _T('May'),
                _T('Jun'),
                _T('Jul'),
                _T('Aug'),
                _T('Sep'),
                _T('Oct'),
                _T('Nov'),
                _T('Dec')
            ],

            longMonths:[
                _T('January'),
                _T('February'),
                _T('March'),
                _T('April'),
                _T('May'),
                _T('June'),
                _T('July'),
                _T('August'),
                _T('September'),
                _T('October'),
                _T('November'),
                _T('December')
            ],

            twoLetterDays: [
                _T('Su'),
                _T('Mo'),
                _T('Tu'),
                _T('We'),
                _T('Th'),
                _T('Fr'),
                _T('Sa')
            ],

            shortDays: [
                _T('Sun'),
                _T('Mon'),
                _T('Tue'),
                _T('Wed'),
                _T('Thu'),
                _T('Fri'),
                _T('Sat')
            ],

            longDays: [
                _T('Sunday'),
                _T('Monday'),
                _T('Tuesday'),
                _T('Wednesday'),
                _T('Thursday'),
                _T('Friday'),
                _T('Saturday')
            ]
        };

        // these functions are use in the main format function above
        this.replaceChars = {
            // day
            d: function () {
                return (this.getDate() < 10 ? '0' : '') + this.getDate();
            },
            D: function () {
                return me.locale.shortDays[this.getDay()];
            },
            j: function () {
                return this.getDate();
            },
            l: function () {
                return me.locale.longDays[this.getDay()];
            },
            N: function () {
                return this.getDay() + 1;
            },
            S: function () {
                return (this.getDate() % 10 == 1 && this.getDate() != 11 ? 'st' : (this.getDate() % 10 == 2 && this.getDate() != 12 ? 'nd' : (this.getDate() % 10 == 3 && this.getDate() != 13 ? 'rd' : 'th')));
            },
            w: function () {
                return this.getDay();
            },
            z: function () {
                var d = new Date(this.getFullYear(), 0, 1);
                return Math.ceil((this - d) / 86400000);
            },

            // week
            W: function () {
                var d = new Date(this.getFullYear(), 0, 1);
                return Math.ceil((((this - d) / 86400000) + d.getDay() + 1) / 7);
            },

            // month
            F: function () {
                return me.locale.longMonths[this.getMonth()];
            },
            m: function () {
                return (this.getMonth() < 9 ? '0' : '') + (this.getMonth() + 1);
            },
            M: function () {
                return me.locale.shortMonths[this.getMonth()];
            },
            n: function () {
                return this.getMonth() + 1;
            },
            t: function () {
                var d = new Date();
                return new Date(d.getFullYear(), d.getMonth(), 0).getDate()
            },

            // year
            L: function () {
                var year = this.getFullYear();
                return (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0));
            },
            o: function () {
                var d = new Date(this.valueOf());
                d.setDate(d.getDate() - ((this.getDay() + 6) % 7) + 3);
                return d.getFullYear();
            },
            Y: function () {
                return this.getFullYear();
            },
            y: function () {
                return ('' + this.getFullYear()).substr(2);
            },

            // time
            a: function () {
                return this.getHours() < 12 ? 'am' : 'pm';
            },
            A: function () {
                return this.getHours() < 12 ? 'AM' : 'PM';
            },
            B: function () {
                return Math.floor((((this.getUTCHours() + 1) % 24) + this.getUTCMinutes() / 60 + this.getUTCSeconds() / 3600) * 1000 / 24);
            },
            g: function () {
                return this.getHours() % 12 || 12;
            },
            G: function () {
                return this.getHours();
            },
            h: function () {
                return ((this.getHours() % 12 || 12) < 10 ? '0' : '') + (this.getHours() % 12 || 12);
            },
            H: function () {
                return (this.getHours() < 10 ? '0' : '') + this.getHours();
            },
            i: function () {
                return (this.getMinutes() < 10 ? '0' : '') + this.getMinutes();
            },
            s: function () {
                return (this.getSeconds() < 10 ? '0' : '') + this.getSeconds();
            },
            u: function () {
                var m = this.getMilliseconds();
                return (m < 10 ? '00' : (m < 100 ?
                    '0' : '')) + m;
            },

            // timezone
            e: function () {
                return "Not Yet Supported";
            },
            I: function () {
                return "Not Yet Supported";
            },
            O: function () {
                return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + '00';
            },
            P: function () {
                return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + ':00';
            },
            T: function () {
                var m = this.getMonth();
                this.setMonth(0);
                var result = this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/, '$1');
                this.setMonth(m);
                return result;
            },
            Z: function () {
                return -this.getTimezoneOffset() * 60;
            },

            // full date/time
            c: function () {
                return this.format("Y-m-d\\TH:i:sP");
            },
            r: function () {
                return this.toString();
            },
            U: function () {
                return this.getTime() / 1000;
            }
        };

        return this;
    }
    FxDateUtil.prototype.constructor = FxDateUtil;

    // instantiate a date util object for use below...
    var DateUtil = new FxDateUtil;


    // TODO: remove this; added to remove dependency on util.js
    var Util = {
        sprintf: function() {
            // minimal sprintf - works only with %s and %p[n]
            var idx, argn = 0, retval = arguments[0];
            while ((idx = retval.indexOf("%s")) != -1)
                retval = retval.replace(/%s/, arguments[++argn]);

            while ((idx = retval.indexOf("%p1")) != -1)
                retval = retval.replace(/%p1/, arguments[1]);
            while ((idx = retval.indexOf("%p2")) != -1)
                retval = retval.replace(/%p2/, arguments[2]);
            while ((idx = retval.indexOf("%p3")) != -1)
                retval = retval.replace(/%p3/, arguments[3]);

            return retval;
        },

        formatDate: function(val, fmt) {
            if (fmt === null || fmt === undefined || fmt == '')
                fmt = 'm/d/Y';
            return DateUtil.format(val, fmt);
        },

        formatNumber: function(val, fmt) {
            if (fmt === undefined)
                fmt = '0,000.00';
            fmt = ""+fmt;

            if (val === undefined)
                return "";

            if (val.length == 0)
                val = "0";

            val = parseFloat(val);

            var curr = '';
            if (fmt.substr(0,1) == '$')
                curr = '$';

            var pct = '';
            if (fmt.indexOf('%') != -1)
            {
                pct = '%';
                fmt = fmt.replace('%','');
            }

            var decs = 0;
            var a = fmt.split('.');
            if (a.length == 2)
                decs = a[1].length;

            var sep = '';
            if (fmt.indexOf(',') != -1)
                sep = ',';

            var res = val.toFixed(decs);

            if (sep.length > 0)
            {
                var left_digits = ""+res;
                var right_digits = "";
                if (left_digits.indexOf('.') != -1)
                {
                    var arr = left_digits.split('.');
                    left_digits = arr[0];
                    right_digits = arr[1];
                }

                var regex = /(\d+)(\d{3})/;
                while (regex.test(left_digits))
                    left_digits = left_digits.replace(regex, '$1'+(',')+'$2');

                res = left_digits;
                if (right_digits.length > 0)
                    res += ('.')+right_digits;
            }
             else
            {
                res = res.replace(/\./g, '.');
            }

            if (curr.length > 0)
                res = curr + res;

            if (pct.length > 0)
                res = res + pct;

            return res;
        }
    }








    // establish biggrid object
    if (window.biggrid === undefined)
        window.biggrid = {};
    if (window.biggrid.cellrenderer === undefined)
        window.biggrid.cellrenderer = {};
    if (window.biggrid.plugin === undefined)
        window.biggrid.plugin = {};

    var cls_prefix = 'bgg',
        cls_table  = 'bgg-table',
        cls_thead  = 'bgg-thead',
        cls_tbody  = 'bgg-tbody',
        cls_th     = 'bgg-th',
        cls_tr     = 'bgg-tr',
        cls_td     = 'bgg-td',
        cls_col    = 'bgg-col';

    var biggrid = window.biggrid;

    // biggrid constants
    $.extend(biggrid, {
        TYPE_CHARACTER: 'character',
        TYPE_NUMERIC:   'numeric',
        TYPE_INTEGER:   'integer',
        TYPE_FLOAT:     'float',
        TYPE_DOUBLE:    'double',
        TYPE_DATE:      'date',
        TYPE_DATETIME:  'datetime',
        TYPE_BOOLEAN:   'boolean'
    });

    // -- helper functions --

    var _parseFloat = function(val, thousands_separator, decimals_separator)
    {
        if (val === null || val === undefined || val == '')
            return 0.0;
        if (typeof(val) == "number")
            return val;
        val = val.replace(/ /g,'');
        val = val.replace(thousands_separator || ',', '', 'g');
        val = val.replace(decimals_separator || '.', '.', 'g');
        return parseFloat(val);
    }

    var isCharacterColumn = function(col_info) {
        return col_info.type === biggrid.TYPE_CHARACTER ? true : false;
    }

    var isBooleanColumn = function(col_info) {
        return col_info.type === biggrid.TYPE_BOOLEAN ? true : false;
    }

    var isNumberColumn = function(col_info) {
        return (col_info.type === biggrid.TYPE_NUMERIC ||
                col_info.type === biggrid.TYPE_INTEGER ||
                col_info.type === biggrid.TYPE_FLOAT   ||
                col_info.type === biggrid.TYPE_DOUBLE) ? true : false;
    }

    var isDateColumn = function(col_info) {
        return (col_info.type === biggrid.TYPE_DATE ||
                col_info.type === biggrid.TYPE_DATETIME) ? true : false;
    }

    // -- biggrid --

    $.biggrid = function(_el, options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace for each biggrid
        var uid = this._generateUid(),
            ns = 'bgg-'+uid;

        // local variables for each instance of the plugin (avoids scope issues)
        var me = this,
            grid = this,
            el = _el,
            $el = $(el),
            $doc = $(document),
            $win = $(window),
            $html = $('html'),
            $body = $('body'),

            $grid_overlay_cell_selection = $(),
            $grid_overlay_fixed_col_selection = $(),
            $grid_overlay_col_selection = $(),
            $grid_overlay_row_selection = $(),

            $grid_body_wrapper = $(),
            $grid_body = $(),

            $grid_fixed_header = $(),
            $grid_fixed_header_thead = $(),
            $grid_fixed_columns = $(),
            $grid_fixed_table = $(),
            $grid_fixed_table_thead = $(),
            $grid_fixed_table_tbody = $(),

            $grid_data_header = $(),
            $grid_data_header_thead = $(),
            $grid_data_table = $(),
            $grid_data_table_thead = $(),
            $grid_data_table_tbody = $(),

            $grid_faux_table = $(),
            $grid_faux_table_thead = $(),
            $grid_faux_table_tbody = $(),

            $grid_yardstick = $(),
            $grid_message = $(),
            $grid_resize_col = $(),

            grid_cell_selection = undefined,
            grid_fixed_col_selection = undefined,
            grid_col_selection = undefined,
            grid_row_selection = undefined,

            grid_events = undefined,
            grid_model = undefined,
            grid_font = undefined,
            grid_sort = [],
            grid_filter = [],
            grid_saved_view = undefined,
            grid_column_dropdown_handler = undefined,
            grid_column_min_width = undefined,
            grid_column_max_width = undefined,
            grid_column_info = {}, // all columns with 'col_id' as each key
            grid_column_order = [], // user-specified order of columns
            grid_column_visual_order = [], // visual order of columns on screen
            grid_columns = [], // array of columns populated from the model
            grid_rows = [],
            grid_row_start = undefined, // starting row requested
            grid_row_limit = undefined, // number of rows requested
            grid_auto_row_limit = undefined, // if opts.limit == 'auto', use this calculated value as the limit
            grid_insert_col_id = undefined,
            grid_row_handle_width = 0,
            grid_fixed_columns_width = 0,
            grid_discovered_row_count = 0, // only for grids where the row count is indeterminate
            grid_discovered_eof = false,
            grid_visible_row_start = undefined,
            grid_visible_row_end = undefined,
            grid_visible_row_count = undefined,
            grid_rendered_row_start = undefined,
            grid_rendered_row_end = undefined,
            grid_faux_row_count = undefined,
            grid_row_count = undefined,
            grid_row_count_text = undefined,
            grid_row_height = undefined,
            grid_auto_row_height = undefined,
            grid_header_height = undefined,
            grid_cell_vertical_align = undefined,
            grid_needs_faux_rows_rendered = true,
            grid_needs_fixed_column_width_recalc = true,
            grid_needs_header_render = true,
            grid_needs_full_render = true,
            grid_needs_auto_column_resize = true,
            grid_loading_timer = undefined,
            grid_cursor = undefined,
            grid_custom_scrollbars = undefined,
            grid_lock_scroll_to_row_height = undefined,
            grid_live_scroll = undefined,
            grid_scroll_left = 0,
            grid_scroll_top = 0,
            grid_body_width = undefined,
            grid_body_height = undefined,
            grid_default_cell_renderer = undefined,
            grid_condition_engine = undefined,
            grid_conditional_formatter = undefined,
            grid_conditional_formatting = [],
            grid_cell_renderers = {},
            grid_plugins = {},
            metrics_inited = false,
            parent_offset = undefined,
            mouse_x = undefined,
            mouse_y = undefined,
            scrollbar_size = undefined,
            screen_height = window.screen.height,
            opts = $.extend(true, {}, $.biggrid.defaults, options);

        var grid_states = [
            'bgg-state-fullscreen',
            'bgg-state-allow-cell-selection',
            'bgg-state-allow-col-dropdown',
            'bgg-state-allow-col-rename',
            'bgg-state-allow-col-selection',
            'bgg-state-allow-row-selection',
            'bgg-state-show-loading',
            'bgg-state-show-faux-rows',
            'bgg-state-show-striped-rows',
            'bgg-state-show-vert-lines',
            'bgg-state-show-horz-lines',
            'bgg-state-show-row-numbers',
            'bgg-state-show-bgg-tr-handles',
            'bgg-state-show-fixed-cols',
            'bgg-state-show-utility-bar',
            'bgg-state-show-border',
            'bgg-state-col-dragging',
            'bgg-state-col-renaming',
            'bgg-state-col-resizing',
            'bgg-state-row-handle-dragging',
            'bgg-state-selection-area-dragging'
        ];

        var html_entity_map = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': '&quot;',
            "'": '&#39;',
            "/": '&#x2F;'
        };

        var whitespace_map = {
            " ": '<span class="bgg-whitespace">&middot;</span>',
            "\t": '<span class="bgg-whitespace">&horbar;</span>'
        };

// -- helper classes --

        var BiggridEvents = function()
        {
            var me = this,
                first_time = true;

            this.init = function()
            {
                // kill any old event handlers that are still active
                me.uninit();

                // handy way to abstract out header events
                var $grid_headers = $().add($grid_data_header).add($grid_fixed_header);

                $grid_body.on('scroll.biggrid.'+ns, me.onGridBodyHorizontalScroll);
                $grid_body.on('scroll.biggrid.'+ns, me.onGridBodyVerticalScroll);
                $grid_body.on('resize.biggrid.'+ns, me.onGridBodyResize);

                $grid_headers.on('mousedown.biggrid.'+ns, '.'+cls_th, me.onGridHeaderCellMouseDown);
                $grid_headers.on('dblclick.biggrid.'+ns, '.'+cls_th, me.onGridHeaderCellDoubleClick);
                $grid_headers.on('mousedown.biggrid.'+ns, '.'+opts.colResizerCls, me.onGridColumnResizerMouseDown);
                $grid_headers.on('dblclick.biggrid.'+ns, '.'+opts.colResizerCls, me.onGridColumnResizerDoubleClick);

                if (opts.forwardContextMenuEvent === true)
                    $grid_headers.on('contextmenu.biggrid.'+ns, '.'+cls_th, me.onGridHeaderCellContextMenu);

                $grid_fixed_columns.on('mousedown.biggrid.'+ns, '.'+opts.rowHandleCls, me.onGridRowHandleCellMouseDown);

                $grid_data_table.on('mousedown.biggrid.'+ns, '.'+cls_td, me.onGridDataCellMouseDown);

                $el.on('mouseenter.biggrid.'+ns, '.'+cls_th+', .'+cls_td, me.onGridCellMouseEnter);
                $el.on('mousedown.biggrid.'+ns, '.'+cls_th+', .'+cls_td, me.onGridCellMouseDown);

                $doc.on('mousemove.biggrid.'+ns, me.onMouseMove);
                $doc.on('mousedown.biggrid.'+ns, me.onDocumentMouseDown);
                $doc.on('mouseup.biggrid.'+ns, me.onDocumentMouseUp);

                $win.on('beforeunload.biggrid.'+ns, me.onBeforeUnload);

                return me;
            }

            this.uninit = function()
            {
                // handy way to abstract out header events
                var $grid_headers = $().add($grid_data_header).add($grid_fixed_header);

                $grid_body.off('.'+ns);
                $grid_headers.off('.'+ns);
                $grid_fixed_columns.off('.'+ns);
                $grid_data_table.off('.'+ns);
                $el.off('.'+ns);
                $doc.off('.'+ns);
                $win.off('.'+ns);

                return me;
            }

            this.onGridBodyHorizontalScroll = function(evt)
            {
                // only handle horizontal scrolls
                if (grid_scroll_left == this.scrollLeft)
                    return;

                // don't allow scrolling if we're dragging a column (for now)
                if (grid.hasState('bgg-state-col-dragging'))
                {
                    evt.preventDefault();
                    return;
                }

                var delta = grid_scroll_left - this.scrollLeft;

                // sync cell selection
                grid_cell_selection.setProperty('left', delta, '+=');
                grid_cell_selection.refresh();

                // sync column selection
                grid_col_selection.setProperty('left', delta, '+=');
                grid_col_selection.refresh();

                // sync header scroll offset with body scroll offset
                $grid_data_header.css('left', -1*this.scrollLeft);

                grid_scroll_left = this.scrollLeft;

                // scrolling should always close any open dropdown menus
                grid._hideColumnDropdownMenu();

                // update the scrollbars
                if (grid_custom_scrollbars === true)
                    $grid_body.data('biggridscrollbars').update();

                // fire the 'afterscroll.biggrid' event
                $el.trigger($.Event('afterscroll.biggrid'), 'horizontal');
            }

            // horizontal scroll throttle
            if (opts.horzScrollThrottle > 0)
                this.onGridBodyHorizontalScroll = _.throttle(this.onGridBodyHorizontalScroll, opts.horzScrollThrottle);

            this.onGridBodyVerticalScroll = function(evt)
            {
                // only handle vertical scrolls
                if (grid_scroll_top == this.scrollTop)
                    return;

                // don't allow scrolling if we're dragging a column (for now)
                if (grid.hasState('bgg-state-col-dragging'))
                {
                    evt.preventDefault();
                    return;
                }

                clearTimeout(grid_loading_timer);

                if (grid_lock_scroll_to_row_height === true)
                {
                    var first_vis;

                    // use Math.floor() or Math.ceil() to calculate next scroll offset,
                    // based on the scroll direction
                    if (this.scrollTop < grid_scroll_top)
                        first_vis = Math.floor(this.scrollTop/grid_row_height);
                         else
                        first_vis = Math.ceil(this.scrollTop/grid_row_height);

                    this.scrollTop = first_vis*grid_row_height;
                }

                var delta = grid_scroll_top - this.scrollTop;

                // sync cell selection
                grid_cell_selection.setProperty('top', delta, '+=');
                grid_cell_selection.refresh();

                // sync row selection
                grid_row_selection.setProperty('top', delta, '+=');
                grid_row_selection.refresh();

                // sync fixed body scroll offset with body scroll offset
                $grid_fixed_columns.css('top', -1*this.scrollTop);

                grid_scroll_top = this.scrollTop;

                // load rows based on the scroll position in the grid body
                grid_visible_row_start = Math.floor(this.scrollTop/grid_row_height);
                grid_visible_row_end = Math.ceil((this.scrollTop+this.clientHeight)/grid_row_height);

                // update row count text
                grid._updateRowCountText();

                // scrolling should always close any open dropdown menus
                grid._hideColumnDropdownMenu();

                // update the scrollbars
                if (grid_custom_scrollbars === true)
                    $grid_body.data('biggridscrollbars').update();

                var row_count;
                if (grid_row_count === undefined && grid_discovered_eof === true)
                    row_count = grid_discovered_row_count;
                     else
                    row_count = grid_row_count;

                // new starting row offset for getRows() call below
                var new_data_start;

                if (grid_model.isFastGetRows() || opts.limit == 'auto')
                    new_data_start = grid_visible_row_start;
                     else
                    new_data_start = Math.max(grid_visible_row_start-(grid_row_limit*opts.dataRowsTopOffsetPct), 0);

                // new starting row offset for faux grid
                var new_faux_start = grid_visible_row_start;

                // once we get to the bottom of the grid, make sure
                // we don't load rows past the row count of the grid
                if (new_data_start > row_count-grid_row_limit)
                    new_data_start = row_count-grid_row_limit;

                // make sure we can render the faux grid all the way
                // down to the last row of where the data grid will render
                if (new_faux_start > row_count-grid_faux_row_count)
                    new_faux_start = row_count-grid_faux_row_count;

                // new top offset for the grid body table based on
                // the new starting row that we're loading
                var new_data_grid_top = new_data_start*grid_row_height,
                    new_faux_grid_top = new_faux_start*grid_row_height;

                // show an empty grid background when scrolling
                if (grid_row_count === undefined)
                {
                    var faux_grid_height = grid_faux_row_count*grid_row_height;

                    // we have to do this check here, otherwise moving
                    // the faux grid messes with the growable grid body height
                    if (new_faux_grid_top+faux_grid_height <= $grid_yardstick.height())
                        $grid_faux_table.css('top', new_faux_grid_top+'px');
                }
                 else
                {
                    $grid_faux_table.css('top', new_faux_grid_top+'px');
                }

                var bottom_threshold = grid_rendered_row_start+(grid_row_limit*opts.loadingThresholdPct),
                    need_render = false;

                // scrolling up past first rendered row
                if (grid_visible_row_start < grid_rendered_row_start)
                    need_render = true;

                // scrolling down past the loading threshold on a grid with a known row count
                // when we're not already at the very bottom of the grid
                if (grid_row_count !== undefined && grid_visible_row_end > bottom_threshold && grid_rendered_row_end < grid_row_count)
                    need_render = true;

                // scrolling down past the loading threshold on a grid with an indeterminate row count
                if (grid_row_count === undefined && grid_visible_row_end > bottom_threshold)
                    need_render = true;

                // if we need to render new rows and 'grid_live_scroll' is false,
                // reposition the existing data to keep up with the scroll
                // instead of showing faux rows or a white background
                if (need_render && grid_live_scroll === false)
                {
                    // move grid body table so it is always in the viewport
                    $grid_data_table.css('top', new_data_grid_top+'px');
                    $grid_fixed_table.css('top', new_data_grid_top+'px');
                }

                if (need_render)
                {
                    var renderRowsInternal = function()
                    {
                        grid.getRows({
                            start: new_data_start,
                            limit: grid_row_limit,
                            refreshRows: true,
                            callback: function(res) {
                                // occasionally when doing a bunch of jerky scrolling ending at the top,
                                // the position of the data grid does not catch up to the final position
                                // the user ends up at; this check here makes sure these positions sync up
                                if (grid_scroll_top == 0 && new_data_grid_top != 0)
                                {
                                    // update new data start and new data grid top variable and re-render
                                    new_data_start = 0;
                                    new_data_grid_top = 0;
                                    renderRowsInternal();
                                }
                                 else
                                {
                                    // doing this check here helps avoid flicker when live scroll
                                    // is set to false (since the data table make alrady be in the right place)
                                    if ($grid_data_table.css('top') != new_data_grid_top+'px')
                                    {
                                        // update the position of the fixed grid and data grid so they are both in the viewport
                                        $grid_data_table.css('top', new_data_grid_top+'px');
                                        $grid_fixed_table.css('top', new_data_grid_top+'px');
                                    }
                                }
                            }
                        });
                    }

                    if (grid_model.isFastGetRows())
                    {
                        renderRowsInternal();
                    }
                     else
                    {
                        var wait = opts.loadingScrollDelay;
                        grid_loading_timer = setTimeout(renderRowsInternal, wait);
                    }
                }

                // fire the 'afterscroll.biggrid' event
                $el.trigger($.Event('afterscroll.biggrid'), 'vertical');
            }

            // vertical scroll throttle
            if (opts.vertScrollThrottle > 0)
                this.onGridBodyVerticalScroll = _.throttle(this.onGridBodyVerticalScroll, opts.vertScrollThrottle);

            this.onGridBodyResize = function(evt)
            {
                var dir = 'none';

                if (grid_body_width !== this.clientWidth)
                    dir = 'horizontal';
                if (grid_body_height !== this.clientHeight)
                    dir = 'vertical';
                if (grid_body_width !== this.clientWidth && grid_body_height !== this.clientHeight)
                    dir = 'both';

                grid_body_width = this.clientWidth;
                grid_body_height = this.clientHeight;

                if (dir == 'vertical' || dir == 'both')
                {
                    // load rows based on the scroll position in the grid body
                    grid_visible_row_start = Math.floor(this.scrollTop/grid_row_height);
                    grid_visible_row_end = Math.ceil((this.scrollTop+this.clientHeight)/grid_row_height);
                    grid_visible_row_count = grid_visible_row_end-grid_visible_row_start;

                    // as we resize, update the number of rows that we need to render
                    // for fast grid models (since they are fast, they really only need
                    // to render the number of rows that are visible to the user)
                    if (grid_model.isFastGetRows())
                    {
                        grid._calcFauxRowCount();
                        grid._invalidateFauxRows();
                        grid.getRows({ start: grid_row_start, refreshRows: true });
                    }

                    // update row count text
                    grid._updateRowCountText();
                }

                if (dir != 'none')
                {
                    // update scroll overlay clipping rectangle
                    grid._updateOverlayClipRect();

                    // fire the 'afterresize.biggrid' event
                    $el.trigger($.Event('afterresize.biggrid'), dir);
                }

                evt.stopPropagation();
            }

            // resize throttle
            if (opts.resizeThrottle > 0)
                this.onGridBodyResize = _.throttle(this.onGridBodyResize, opts.resizeThrottle);

            this.onGridHeaderCellMouseDown = function(evt)
            {
                // mouse down anywhere should always close any open dropdown menus
                grid._hideColumnDropdownMenu();

                var $selected_col = $(this),
                    col_id = $selected_col.data('col-id');

                // don't allow middle-click
                if (evt.which == 2)
                {
                    evt.preventDefault();
                    evt.stopPropagation();
                }

                // don't allow right-click; right-clicks will be
                // handled by the 'contextmenu' event if it is enabled
                if (evt.which == 3)
                {
                    evt.preventDefault();
                    evt.stopPropagation();

                    // remove cell selection
                    if (grid.isCellSelectionAllowed())
                        grid.clearCellSelection();

                    // remove row selection
                    if (grid.isRowSelectionAllowed())
                        grid.clearRowSelection();

                    if (grid.isColumnSelectionAllowed() && !grid.isColumnSelected(col_id))
                    {
                        // ctrl+click; add column to selection
                        if (evt.metaKey || evt.ctrlKey)
                            grid.selectColumn(col_id, true);
                             else
                            grid.selectColumn(col_id);
                    }

                    return;
                }

                // handle left-click

                if (grid.isColumnSelectionAllowed())
                {
                    // clicking on top-left corner cell; select all columns
                    if ($selected_col.hasClass(opts.rowHandleCls))
                    {
                        grid.selectAllColumns();

                        // don't bubble up to grid event handler
                        evt.stopPropagation();
                        return;
                    }

                    // no shift+click or ctrl+click; remove existing column selection
                    if (!evt.shiftKey && !evt.metaKey && !evt.ctrlKey)
                        grid.clearColumnSelection();

                    if (evt.shiftKey)
                    {
                        if (grid.isColumnFrozen(col_id))
                        {
                            var selected_cols = grid_fixed_col_selection.getSelectedColumns(),
                                last_selected_col_id = grid_fixed_col_selection.getLastSelectedColumn();

                            // shift+click; add columns between the last selected column and this one
                            if (selected_cols.length == 0 || last_selected_col_id === undefined)
                            {
                                // single column selection
                                grid.selectColumn(col_id);
                            }
                             else
                            {
                                // determine selection based on the last selected column
                                var $last_selected_col = $grid_fixed_header.find('.'+cls_th+'.'+last_selected_col_id),
                                    $selection_range = $last_selected_col.adjacentUntil('.'+cls_th+'.'+col_id, true, true);

                                // add columns that are in the selection range (inclusive)
                                $selection_range.each(function() {
                                    var range_col_id = $(this).data('col-id');
                                    grid.selectColumn(range_col_id, true);
                                });
                            }
                        }
                         else
                        {
                            var selected_cols = grid_col_selection.getSelectedColumns(),
                                last_selected_col_id = grid_col_selection.getLastSelectedColumn();

                            // shift+click; add columns between the last selected column and this one
                            if (selected_cols.length == 0 || last_selected_col_id === undefined)
                            {
                                // single column selection
                                grid.selectColumn(col_id);
                            }
                             else
                            {
                                // determine selection based on the last selected column
                                var $last_selected_col = $grid_data_header.find('.'+cls_th+'.'+last_selected_col_id),
                                    $selection_range = $last_selected_col.adjacentUntil('.'+cls_th+'.'+col_id, true, true);

                                // add columns that are in the selection range (inclusive)
                                $selection_range.each(function() {
                                    var range_col_id = $(this).data('col-id');
                                    grid.selectColumn(range_col_id, true);
                                });
                            }
                        }
                    }
                     else if (evt.metaKey || evt.ctrlKey)
                    {
                        if (!grid.isColumnSelected(col_id))
                        {
                            // ctrl+click; add column to selection
                            grid.selectColumn(col_id, true);
                        }
                    }
                     else
                    {
                        // single column selection
                        grid.selectColumn(col_id);

                        // remove row selection
                        if (grid.isRowSelectionAllowed())
                            grid.clearRowSelection();
                    }

                    // don't allow selection areas with column selection
                    if (grid.isCellSelectionAllowed())
                        grid.clearCellSelection();
                }

                // don't bubble up to grid event handler
                evt.stopPropagation();
            }

            this.onGridHeaderCellDoubleClick = function(evt)
            {
                var $th = $(this),
                    col_id = $th.data('col-id'),
                    col_info = grid.getColumnInfo(col_id);

                // don't allow middle-click or right-click
                if (evt.which == 2 || evt.which == 3)
                {
                    evt.preventDefault();
                    evt.stopPropagation();
                    return;
                }

                // fire the 'headercelldblclick.biggrid' event
                $el.trigger($.Event('headercelldblclick.biggrid'), {
                    $th: $th,
                    col_id: col_id,
                    columnInfo: col_info
                });

                // don't bubble up
                evt.stopPropagation();
            }

            this.onGridColumnResizerMouseDown = function(evt)
            {
                // mouse down anywhere should always close any open dropdown menus
                grid._hideColumnDropdownMenu();

                // don't allow middle-click or right-click
                if (evt.which == 2 || evt.which == 3)
                {
                    evt.preventDefault();
                    evt.stopPropagation();
                    return;
                }

                var $th = $(this).closest('.'+cls_th),
                    col_id = $th.data('col-id');

                $grid_resize_col = $el.find('.'+cls_th+'.'+col_id);
                grid.addState('bgg-state-col-resizing');

                // don't bubble up to the grid header cell event handler
                evt.stopPropagation();
            }

            this.onGridColumnResizerDoubleClick = function(evt)
            {
                var $th = $(this).closest('.'+cls_th),
                    col_id = $th.data('col-id');

                // don't allow middle-click or right-click
                if (evt.which == 2 || evt.which == 3)
                {
                    evt.preventDefault();
                    evt.stopPropagation();
                    return;
                }

                if (grid.isColumnSelected(col_id))
                {
                    _.each(grid.getSelectedColumns(), function(col_id) {
                        grid.autoResizeColumn(col_id);
                    });
                }
                 else
                {
                    grid.autoResizeColumn(col_id);
                }

                // don't bubble up to the grid header cell event handler
                evt.stopPropagation();
            }

            this.onGridHeaderCellContextMenu = function(evt, params)
            {
                // don't allow native right-click context menu
                evt.preventDefault();

                // don't allow right-click on column resizer
                var $target = $(evt.target);
                if ($target.hasClass(opts.colResizerCls))
                    return;

                $(this).find('.bgg-cell-dropdown .dropdown-toggle').click();
            }

            this.onGridRowHandleCellMouseDown = function(evt)
            {
                // mouse down anywhere should always close any open dropdown menus
                grid._hideColumnDropdownMenu();

                if (grid.isRowSelectionAllowed())
                {
                    var $cell = $(this),
                        $selected_row = $cell.closest('tr'),
                        row_id = $selected_row.data('row-id');

                    // no shift+click or ctrl+click; remove existing row selection
                    if (!evt.shiftKey && !evt.metaKey && !evt.ctrlKey)
                        grid.clearRowSelection();

                    if (evt.shiftKey)
                    {
                        var selected_rows = grid_row_selection.getSelectedRows(),
                            last_selected_row_id = grid_row_selection.getLastSelectedRow();

                        // shift+click; add rows between the last selected row and this one
                        if (selected_rows.length == 0 || last_selected_row_id === undefined)
                        {
                            // single row selection
                            grid.selectRow(row_id);
                        }
                         else
                        {
                            // determine selection based on the last selected row
                            var $last_selected_row = $grid_fixed_columns.find('tr.'+last_selected_row_id),
                                $selection_range = $last_selected_row.adjacentUntil('tr.'+row_id, true, true);

                            // add rows that are in the selection range (inclusive)
                            $selection_range.each(function() {
                                var range_row_id = $(this).data('row-id');
                                grid.selectRow(range_row_id, true);
                            });
                        }
                    }
                     else if (evt.metaKey || evt.ctrlKey)
                    {
                        if (!grid.isRowSelected(row_id))
                        {
                            // ctrl+click; add row to selection
                            grid.selectRow(row_id, true);
                        }
                    }
                     else
                    {
                        // single row selection
                        grid.selectRow(row_id);

                        // remove column selection
                        if (grid.isColumnSelectionAllowed())
                            grid.clearColumnSelection();
                    }

                    // start our row drag selection
                    grid_row_selection.startDragSelection($cell);
                    grid.addState('bgg-state-row-handle-dragging');

                    // don't allow selection areas with row selection
                    if (grid.isCellSelectionAllowed())
                        grid.clearCellSelection();
                }

                // don't bubble up to grid event handler
                evt.stopPropagation();
            }

            this.onGridDataCellMouseDown = function(evt)
            {
                // mouse down anywhere should always close any open dropdown menus
                grid._hideColumnDropdownMenu();

                // make sure we end our header cell edits
                if (grid.hasState('bgg-state-col-renaming'))
                    grid.endHeaderCellEdit(true);

                // remove column selection
                if (grid.isColumnSelectionAllowed())
                    grid.clearColumnSelection();

                // remove row selection
                if (grid.isRowSelectionAllowed())
                    grid.clearRowSelection();

                if (grid.isCellSelectionAllowed())
                {
                    // remove cell selection
                    if (grid.isCellSelectionAllowed())
                        grid.clearCellSelection();

                    var $cell = $(this);

                    // start our selection area drag selection
                    grid_cell_selection.startDragSelection($cell);

                    grid.addState('bgg-state-selection-area-dragging');
                }

                // don't bubble up to grid event handler
                evt.stopPropagation();
            }

            this.onGridCellMouseEnter = function(evt)
            {
                var $cell = $(this);

                // update selection area selection
                if (grid.hasState('bgg-state-selection-area-dragging'))
                {
                    if (this.tagName.toUpperCase() == 'TD')
                    {
                        if (!$cell.hasClass(opts.colFrozenCls))
                            grid_cell_selection.updateDragEndCell($cell);
                    }
                }

                // update row drag selection
                if (grid.hasState('bgg-state-row-handle-dragging'))
                {
                    if (this.tagName.toUpperCase() == 'TD')
                        grid_row_selection.updateDragEndCell($cell);
                }
            }

            this.onGridCellMouseDown = function(evt)
            {
                // mouse down anywhere should always close any open dropdown menus
                grid._hideColumnDropdownMenu();

                // remove cell selection
                if (grid.isCellSelectionAllowed())
                    grid.clearCellSelection();

                // remove column selection
                if (grid.isColumnSelectionAllowed())
                    grid.clearColumnSelection();

                // remove row selection
                if (grid.isRowSelectionAllowed())
                    grid.clearRowSelection();
            }

            this.onMouseMove = function(evt)
            {
                // track mouse position
                mouse_x = evt.pageX;
                mouse_y = evt.pageY;

                if ($grid_resize_col.length > 0)
                {
                    grid._updateCursor('ew-resize');
                    grid._resizeColumnThrottled();

                    // update the scrollbars
                    if (grid_custom_scrollbars === true)
                        $grid_body.data('biggridscrollbars').update();
                }
            }

            this.onDocumentMouseDown = function(evt)
            {
                // mouse down anywhere should always close any open dropdown menus
                grid._hideColumnDropdownMenu();

                // make sure we end our header cell edits
                if (grid.hasState('bgg-state-col-renaming'))
                    grid.endHeaderCellEdit(true);

                grid.removeState('bgg-state-col-renaming');
            }

            this.onDocumentMouseUp = function(evt)
            {
                if (grid.hasState('bgg-state-selection-area-dragging'))
                    grid_cell_selection.endDragSelection();

                if (grid.hasState('bgg-state-row-handle-dragging'))
                    grid_row_selection.endDragSelection();

                grid.removeState('bgg-state-col-resizing');
                grid.removeState('bgg-state-col-dragging');
                grid.removeState('bgg-state-row-handle-dragging');
                grid.removeState('bgg-state-selection-area-dragging');

                // reset the cursor
                grid._updateCursor('');
                $grid_resize_col = $();
            }

            this.onBeforeUnload = function(evt)
            {
                grid.uninit();
            }
        }
        BiggridEvents.prototype.constructor = BiggridEvents;

        var BiggridDefaultModel = function()
        {
            var me = this,
                cols = [],
                row_cache = [],
                column_count = undefined,
                row_count = undefined,
                opts = $.extend({}, BiggridDefaultModel.defaults, options);

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
                var cols = [];

                for (var i = 0; i < column_count; ++i)
                {
                    if (i == 0)
                        cols.push({ name: 'My First Big Grid Column' });
                         else
                        cols.push({ name: 'Column' + ' ' + (i+1) });
                }

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
                    for (var j = 0; j < column_count; ++j)
                    {
                        if (i == 0 && j == 0)
                        {
                            var cell_str = "Welcome to Big Grid! This is the default grid model. Create your own model or use one of the pre-packaged models to get started.";
                            cell_data.push(cell_str);
                        }
                         else
                        {
                            var s = '\tRow '+(i+1)+', Column '+(j+1)+'    ';
                            cell_data.push(s);
                        }
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
        BiggridDefaultModel.prototype.constructor = BiggridDefaultModel;

        BiggridDefaultModel.defaults = {
            column_count: 20,
            row_count: 1000,
            start: 0,
            limit: 10
        };

        var BiggridCellProperties = function($cell)
        {
            var me = this;

            this.top = 0;
            this.left = 0;
            this.width = 0;
            this.height = 0;
            this.bottom = 0;
            this.right = 0;

            this.refresh = function($_cell)
            {
                if (!($_cell instanceof jQuery))
                    return;

                var pos = $_cell.position();

                me.top = pos.top,
                me.left = pos.left;
                me.width = $_cell.outerWidth(true),
                me.height = $_cell.outerHeight(true),
                me.bottom = me.top+me.height;
                me.right = me.left+me.width,

                me.row_id = $_cell.data('row-id');
                me.col_id = $_cell.data('col-id');
            }

            me.refresh($cell);
        }
        BiggridCellProperties.prototype.constructor = BiggridCellProperties;

        var BiggridCellArea = function(type, cls, style, tweaks)
        {
            var me = this;

            this.top = 0;
            this.left = 0;
            this.bottom = 0;
            this.right = 0;
            this.height = 0;
            this.width = 0;

            var start_cell_props = undefined,
                end_cell_props = undefined,
                $start_cell = $(),
                $end_cell = $(),
                $region = $(),
                region_type = type || 'cell-selection',
                region_cls = cls || '',
                region_style = style || {},
                region_tweaks = { top: 0, left: 0, bottom: 0, right: 0, height: 0, width: 0 };

            if (_.isObject(tweaks))
                region_tweaks = $.extend({}, region_tweaks, tweaks);

            this.init = function($start_cell, $end_cell)
            {
                if (!($end_cell instanceof jQuery))
                    $end_cell = $start_cell;

                me.setStartCell($start_cell);
                me.setEndCell($end_cell);
                me.calcDimensions();
            }

            this.destroy = function()
            {
                me.top = 0;
                me.left = 0;
                me.bottom = 0;
                me.right = 0;
                me.height = 0;
                me.width = 0;

                me.setStartCell($());
                me.setEndCell($());

                $region.remove();
                $region = $();
            }

            this.setStartCell = function($cell)
            {
                if (!($cell instanceof jQuery))
                    return;

                if ($cell.hasClass(opts.colInvalidCls))
                    return;

                $start_cell = $cell;

                if ($cell.length == 0)
                    start_cell_props = undefined;
                     else
                    start_cell_props = new BiggridCellProperties($cell);
            }

            this.setEndCell = function($cell)
            {
                if (!($cell instanceof jQuery))
                    return;

                if ($cell.hasClass(opts.colInvalidCls))
                    return;

                $end_cell = $cell;

                if ($cell.length == 0)
                    end_cell_props = undefined;
                     else
                    end_cell_props = new BiggridCellProperties($cell);
            }

            this.setProperty = function(key, val, operator)
            {
                var delta = 0;

                if (operator === '+=')
                {
                    delta = val;
                    me[key] += val;
                }
                 else if (operator === '-=')
                {
                    delta = -1 * val;
                    me[key] -= val;
                }
                 else
                {
                    delta = val - me[key];
                    me[key] = val;
                }

                // key values in sync
                if (key == 'top')    { me['bottom'] += delta }
                if (key == 'left')   { me['right']  += delta }
                if (key == 'bottom') { me['height'] += delta }
                if (key == 'right')  { me['width']  += delta }
                if (key == 'height') { me['bottom'] += delta }
                if (key == 'width')  { me['right']  += delta }
            }

            this.getStartCell = function()
            {
                return start_cell_props;
            }

            this.getEndCell = function()
            {
                return end_cell_props;
            }

            this.getRegion = function()
            {
                return $region;
            }

            this.refreshCellProperties = function()
            {
                if (start_cell_props !== undefined)
                    start_cell_props.refresh($start_cell);

                if (end_cell_props !== undefined)
                    end_cell_props.refresh($end_cell);
            }

            this.calcDimensions = function()
            {
                if (start_cell_props === undefined || end_cell_props === undefined)
                    return;

                me.top = Math.min(start_cell_props.top, end_cell_props.top),
                me.left = Math.min(start_cell_props.left, end_cell_props.left),
                me.bottom = Math.max(start_cell_props.bottom, end_cell_props.bottom),
                me.right = Math.max(start_cell_props.right, end_cell_props.right),
                me.height = me.bottom - me.top,
                me.width = me.right - me.left;

                if (region_type == 'cell-selection' || region_type == 'row-selection')
                {
                    var table_pos = $grid_data_table.position();

                    // cell selection is relative to grid data table,
                    // but also take into account header row
                    me.setProperty('top', (table_pos.top - grid_row_height), '+=');
                }

                if (region_type == 'fixed-column-selection' || region_type == 'column-selection')
                {
                    // column selection overlays should start right next to the grid header cells
                    me.setProperty('top', grid_header_height);
                }

                if (region_type == 'cell-selection' || region_type == 'column-selection')
                {
                    // take into account fixed column width and horizontal scroll position
                    me.setProperty('left', (grid_fixed_columns_width - grid_scroll_left), '+=');
                }

                if (region_type == 'row-selection')
                {
                    // row selection overlays should start right next to the row handle cells
                    me.setProperty('left', grid_row_handle_width);
                }

                // user-defined tweaks
                if (region_tweaks.top != 0)    { me.setProperty('top',    region_tweaks.top,    '+=') };
                if (region_tweaks.left != 0)   { me.setProperty('left',   region_tweaks.left,   '+=') };
                if (region_tweaks.bottom != 0) { me.setProperty('bottom', region_tweaks.bottom, '+=') };
                if (region_tweaks.right != 0)  { me.setProperty('right',  region_tweaks.right,  '+=') };
                if (region_tweaks.height != 0) { me.setProperty('height', region_tweaks.height, '+=') };
                if (region_tweaks.width != 0)  { me.setProperty('width',  region_tweaks.width,  '+=') };
            }

            this.updateRegion = function(create_new)
            {
                if (create_new === true)
                {
                    $region.remove();
                    $region = $();
                }

                if ($region.length == 0)
                {
                    // create UI and append it to the DOM
                    $region = $('<svg class="'+region_cls+'" xmlns="http://www.w3.org/2000/svg" pointer-events="none"></svg>');
                    $region.css(region_style);

                    if (region_type == 'cell-selection')
                        $region.appendTo($grid_overlay_cell_selection);
                    if (region_type == 'fixed-column-selection')
                        $region.appendTo($grid_overlay_fixed_col_selection);
                    if (region_type == 'column-selection')
                        $region.appendTo($grid_overlay_col_selection);
                    if (region_type == 'row-selection')
                        $region.appendTo($grid_overlay_row_selection);
                }

                // position the UI
                $region.css({
                    top: me.top,
                    left: me.left,
                    width: me.width,
                    height: me.height
                });
            }
        }
        BiggridCellArea.prototype.constructor = BiggridCellArea;

        var BiggridCellSelection = function()
        {
            var me = this;

            this.cell_area = undefined;
            this.drag_cells = [];
            this.singleton_cells = [];

            this.destroy = function()
            {
                me.drag_cells = [];
                me.singleton_cells = [];

                if (me.cell_area !== undefined)
                    me.cell_area.destroy();
            }

            this.selectCell = function(row, col, add_to_selection)
            {
                if (col == opts.colInvalidCls)
                    return;

                if (add_to_selection === true)
                    me.singleton_cells = _.union(me.singleton_cells, [{ 'row': row, 'column': col }]);
                     else
                    me.singleton_cells = [{ 'row': row, 'column': col }];
            }

            this.deselectCell = function(row, col)
            {
                if (col == opts.colInvalidCls)
                    return;

                me.singleton_cells = _.withoutObject(me.singleton_cells, { 'row': row, 'column': col });
            }

            this.clearSelection = function()
            {
                me.destroy();
            }

            this.isCellSelected = function(row, col)
            {
                if (col == opts.colInvalidCls)
                    return false;

                var found_cell = _.find(me.getSelectedCells(), function(cell) {
                    return (cell.row == row && cell.column == col)
                });

                return (found_cell !== undefined) ? true : false;
            }

            this.getSelectedCells = function()
            {
                return _.union(me.drag_cells, me.singleton_cells);
            }

            this.startDragSelection = function($start_cell)
            {
                if (me.cell_area === undefined)
                    return;

                me.drag_cells = [];
                me.singleton_cells = [];

                me.cell_area.init($start_cell);
                me.cell_area.updateRegion(true);
            }

            this.updateDragEndCell = function($cell)
            {
                if (me.cell_area === undefined)
                    return;

                me.cell_area.setEndCell($cell);
                me.cell_area.calcDimensions();
                me.cell_area.updateRegion();
            }

            this.endDragSelection = function()
            {
                if (me.cell_area === undefined)
                    return;

                var start_cell = me.cell_area.getStartCell(),
                    end_cell = me.cell_area.getEndCell();

                if (start_cell === undefined || end_cell === undefined)
                    return;

                var $start_row = $grid_data_table.find('tr.'+start_cell.row_id),
                    $rows = $start_row.adjacentUntil('tr.'+end_cell.row_id, true, true);

                me.drag_cells = [];

                $rows.each(function() {
                    var $row = $(this),
                        $start_cell = $row.find('td.'+start_cell.col_id),
                        $cells = $start_cell.adjacentUntil('td.'+end_cell.col_id, true, true);

                    $cells.each(function() {
                        me.drag_cells.push({
                            row: this.getAttribute('data-row-id'),
                            column: this.getAttribute('data-col-id')
                        });
                    });
                });
            }

            this.setProperty = function(key, val, operator)
            {
                if (me.cell_area === undefined)
                    return;

                me.cell_area.setProperty(key, val, operator);
            }

            this.calcDimensions = function()
            {
                if (me.cell_area === undefined)
                    return;

                me.cell_area.refreshCellProperties();
                me.cell_area.calcDimensions();
            }

            this.refresh = function()
            {
                if (me.cell_area === undefined)
                    return;

                if (me.cell_area.getRegion().length == 0)
                    return;

                me.cell_area.updateRegion();
            }

            this._init = function()
            {
                var cell_selection_area_cls = 'bgg-cell-selection-overlay',
                    cell_selection_area_style = {},
                    cell_selection_area_top = grid_header_height;

                cell_selection_area_style['background-color'] = opts.cellSelectionBackgroundColor;

                if (opts.cellSelectionBorderColor !== undefined && opts.cellSelectionBorderColor.length > 0)
                    cell_selection_area_style['outline-color'] = opts.cellSelectionBorderColor;

                me.cell_area = new BiggridCellArea(
                    'cell-selection',
                    cell_selection_area_cls,
                    cell_selection_area_style, {
                        top: cell_selection_area_top
                    });

                me.drag_cells = [];
                me.singleton_cells = [];
            }

            me._init();
        }
        BiggridCellSelection.prototype.constructor = BiggridCellSelection;

        var BiggridColumnSelection = function(type)
        {
            var me = this;

            this.type = type || 'data'; // 'fixed' or 'data'
            this.col_areas = [];
            this.cols = [];
            this.sorted_cols = [];
            this.grouped_cols = [];
            this.last_selected_col = undefined;

            this.destroy = function()
            {
                me.cols = [];
                me.sorted_cols = [];
                me.grouped_cols = [];
                me.last_selected_col = undefined;

                me._destroyColumnAreas();
            }

            // 'col' can be a string or array
            this.selectColumn = function(col, add_to_selection)
            {
                if (col == opts.colInvalidCls)
                    return;

                var is_array = $.isArray(col);

                if (add_to_selection === true)
                    me.cols = _.union(me.cols, is_array ? col : [col]);
                     else
                    me.cols = is_array ? col : [col];

                if (!is_array)
                    me.last_selected_col = col;

                me._updateSortedSelectedColumns();
                me._updateGroupedSelectedColumns();
            }

            // 'col' can be a string or array
            this.deselectColumn = function(col)
            {
                if (col == opts.colInvalidCls)
                    return;

                var is_array = $.isArray(col);

                if (is_array)
                    me.cols = _.difference(me.cols, col);
                     else
                    me.cols = _.without(me.cols, col);

                me.last_selected_col = undefined;

                me._updateSortedSelectedColumns();
                me._updateGroupedSelectedColumns();
            }

            this.clearSelection = function()
            {
                me.cols = [];
                me.sorted_cols = [];
                me.grouped_cols = [];
                me.last_selected_col = undefined;

                me.refresh(true);
            }

            // 'col' can be a string or array; if an array, we will only return
            // true if all columns are selected
            this.isColumnSelected = function(col)
            {
                if (col == opts.colInvalidCls)
                    return;

                if ($.isArray(col))
                    return (_.intersection(me.cols, col).length == col.length) ? true : false;

                return (_.indexOf(me.cols, col) >= 0) ? true : false;
            }

            this.getSelectedColumns = function(processed)
            {
                if (processed === 'sorted')
                    return me.sorted_cols;
                if (processed === 'grouped')
                    return me.grouped_cols;
                return me.cols;
            }

            this.getLastSelectedColumn = function()
            {
                return me.last_selected_col;
            }

            this.setProperty = function(key, val, operator)
            {
                _.each(me.col_areas, function(col_area) {
                    col_area.setProperty(key, val, operator);
                });
            }

            this.refresh = function(hard)
            {
                if (hard === true)
                {
                    me._createColAreas();
                }
                 else
                {
                    _.each(me.col_areas, function(col_area) {
                        col_area.updateRegion();
                    });
                }
            }

            this.calcDimensions = function()
            {
                _.each(me.col_areas, function(col_area) {
                    col_area.refreshCellProperties();
                    col_area.calcDimensions();
                });
            }

            this._updateSortedSelectedColumns = function()
            {
                // add column position to each column
                var cols = _.map(me.cols, function(col) {
                    return {
                        col_id: col,
                        col_pos: grid.getColumnVisualPosition(col)
                    };
                });

                // sort the columns by position
                cols = _.sortBy(cols, function(col) {
                    return col.col_pos;
                });

                me.sorted_cols = cols;
            }

            this._updateGroupedSelectedColumns = function()
            {
                var sorted_cols = me.sorted_cols;

                var last_pos = -2,
                    group_id = 0;

                // add group id to each column
                sorted_cols = _.map(sorted_cols, function(col) {
                    if (col.col_pos != last_pos+1)
                        group_id++;

                    last_pos = col.col_pos;

                    return _.extend(col, { col_group: group_id });
                });

                me.grouped_cols = _.toArray(_.groupBy(sorted_cols, 'col_group'));
            }

            this._createColAreas = function()
            {
                // remove selection class from all header cells
                var $header_cells;

                if (me.type === 'fixed')
                    $header_cells = $grid_fixed_header.find('.'+cls_th).removeClass(opts.colSelectedCls);
                     else
                    $header_cells = $grid_data_header.find('.'+cls_th).removeClass(opts.colSelectedCls);

                // add back selection class to selected header cells
                if (me.cols.length > 0)
                {
                    var selector = '.'+cls_th+'.'+me.cols.join(',.'+cls_th+'.');
                    $header_cells.filter(selector).addClass(opts.colSelectedCls);
                }

                // we need to render a new group of column selections
                me._destroyColumnAreas();

                var col_selelction_area_cls = 'bgg-col-selection-overlay',
                    col_selection_area_style = {};

                col_selection_area_style['background-color'] = opts.columnSelectionBackgroundColor;

                if (opts.columnSelectionBorderColor !== undefined && opts.columnSelectionBorderColor.length > 0)
                    col_selection_area_style['outline-color'] = opts.columnSelectionBorderColor;

                // create our column selection areas
                _.each(me.grouped_cols, function(col_group) {
                    var col_id_arr = _.map(col_group, 'col_id'),
                        start_col_id = _.first(col_id_arr),
                        end_col_id = _.last(col_id_arr),
                        $start_cell = $header_cells.filter('.'+start_col_id),
                        $end_cell = $header_cells.filter('.'+end_col_id);

                    var col_selection_area = new BiggridCellArea(
                        (me.type === 'fixed') ? 'fixed-column-selection' : 'column-selection',
                        col_selelction_area_cls,
                        col_selection_area_style, {
                            height: 10000
                        });

                    col_selection_area.init($start_cell, $end_cell);
                    col_selection_area.updateRegion();

                    me.col_areas.push(col_selection_area);
                });
            }

            this._destroyColumnAreas = function()
            {
                _.each(me.col_areas, function(col_area) {
                    col_area.destroy();
                });

                me.col_areas = [];
            }
        }
        BiggridColumnSelection.prototype.constructor = BiggridColumnSelection;

        var BiggridRowSelection = function()
        {
            var me = this;

            this.row_areas = [];
            this.rows = [];
            this.sorted_rows = [];
            this.grouped_rows = [];
            this.last_selected_row = undefined;

            // private
            var $drag_start_row = $(),
                $drag_rows = $(),
                rows_before_drag = [],
                drag_rows = [];

            this.destroy = function()
            {
                me.rows = [];
                me.sorted_rows = [];
                me.grouped_rows = [];
                me.last_selected_row = undefined;

                $drag_start_row = $();
                $drag_rows = $();
                rows_before_drag = [];
                drag_rows = [];

                me._destroyRowAreas();
            }

            // 'row' can be a string or array
            this.selectRow = function(row, add_to_selection)
            {
                var is_array = $.isArray(row);

                if (add_to_selection === true)
                    me.rows = _.union(me.rows, is_array ? row : [row]);
                     else
                    me.rows = is_array ? row : [row];

                if (!is_array)
                    me.last_selected_row = row;

                me._updateSortedSelectedRows();
                me._updateGroupedSelectedRows();
            }

            // 'row' can be a string or array
            this.deselectRow = function(row)
            {
                var is_array = $.isArray(row);

                if (is_array)
                    me.rows = _.difference(me.rows, row);
                     else
                    me.rows = _.without(me.rows, row);

                me.last_selected_row = undefined;

                me._updateSortedSelectedRows();
                me._updateGroupedSelectedRows();
            }

            this.clearSelection = function()
            {
                me.rows = [];
                me.sorted_rows = [];
                me.grouped_rows = [];
                me.last_selected_row = undefined;

                me.refresh(true);
            }

            // 'row' can be a string or array; if an array, we will only return
            // true if all rows are selected
            this.isRowSelected = function(row)
            {
                if ($.isArray(row))
                    return (_.intersection(me.rows, row).length == row.length) ? true : false;

                return (_.indexOf(me.rows, row) >= 0) ? true : false;
            }

            this.getSelectedRows = function(processed)
            {
                if (processed === 'sorted')
                    return me.sorted_rows;
                if (processed === 'grouped')
                    return me.grouped_rows;
                return me.rows;
            }

            this.getLastSelectedRow = function()
            {
                return me.last_selected_row;
            }

            this.startDragSelection = function($start_cell)
            {
                $drag_start_row = $start_cell.closest('tr');
                $drag_rows = $drag_start_row;

                rows_before_drag = me.rows;
                drag_rows = [$drag_start_row.data('row-id')];

                me.rows = _.union(rows_before_drag, drag_rows);
                me._updateSortedSelectedRows();
                me._updateGroupedSelectedRows();
                me.refresh(true);
            }

            this.updateDragEndCell = function($cell)
            {
                var end_row_id = $cell.data('row-id');
                $drag_rows = $drag_start_row.adjacentUntil('tr.'+end_row_id, true, true);

                drag_rows = [];

                $drag_rows.each(function() {
                    drag_rows.push(this.getAttribute('data-row-id'));
                });

                me.rows = _.union(rows_before_drag, drag_rows);
                me._updateSortedSelectedRows();
                me._updateGroupedSelectedRows();
                me.refresh(true);
            }

            this.endDragSelection = function()
            {
                me.rows = _.union(rows_before_drag, drag_rows);
                me._updateSortedSelectedRows();
                me._updateGroupedSelectedRows();
                me.refresh(true);

                $drag_start_row = $();
                $drag_rows = $();
                rows_before_drag = [];
                drag_rows = [];
            }

            this.setProperty = function(key, val, operator)
            {
                _.each(me.row_areas, function(row_area) {
                    row_area.setProperty(key, val, operator);
                });
            }

            this.refresh = function(hard)
            {
                if (hard === true)
                {
                    me._createRowAreas();
                }
                 else
                {
                    _.each(me.row_areas, function(row_area) {
                        row_area.updateRegion();
                    });
                }
            }

            this._updateSortedSelectedRows = function()
            {
                // add row position to each row
                var rows = _.map(me.rows, function(row) {
                    return {
                        row_id: row,
                        row_idx: grid.getRowIdx(row)
                    };
                });

                // sort the rows by position
                rows = _.sortBy(rows, function(row) {
                    return row.row_idx;
                });

                me.sorted_rows = rows;
            }

            this._updateGroupedSelectedRows = function()
            {
                var sorted_rows = me.sorted_rows;

                var last_idx = -2,
                    group_id = 0;

                // add group id to each row
                sorted_rows = _.map(sorted_rows, function(row) {
                    if (row.row_idx != last_idx+1)
                        group_id++;

                    last_idx = row.row_idx;

                    return _.extend(row, { row_group: group_id });
                });

                me.grouped_rows = _.toArray(_.groupBy(sorted_rows, 'row_group'));
            }

            this._createRowAreas = function()
            {
                // remove selection class from all row handle cells
                var $row_handle_cells = $grid_fixed_columns.find('.'+opts.rowHandleCls).removeClass(opts.rowSelectedCls);

                // add back selection class to selected row handle cells
                if (me.rows.length > 0)
                {
                    var selector = 'td.'+me.rows.join(',td.');
                    $row_handle_cells.filter(selector).addClass(opts.rowSelectedCls);
                }

                // we need to render a new group of row selections
                me._destroyRowAreas();

                // create our row selection areas
                _.each(me.grouped_rows, function(row_group) {
                    var row_id_arr = _.map(row_group, 'row_id'),
                        start_row_id = _.first(row_id_arr),
                        end_row_id = _.last(row_id_arr),
                        $start_cell = $row_handle_cells.filter('.'+start_row_id),
                        $end_cell = $row_handle_cells.filter('.'+end_row_id);

                    var row_selection_area_cls = 'bgg-tr-selection-overlay',
                        row_selection_area_style = {},
                        row_selection_area_top = grid_header_height;

                    row_selection_area_style['background-color'] = opts.rowSelectionBackgroundColor;

                    if (opts.rowSelectionBorderColor !== undefined && opts.rowSelectionBorderColor.length > 0)
                        row_selection_area_style['outline-color'] = opts.rowSelectionBorderColor;

                    var row_selection_area = new BiggridCellArea(
                        'row-selection',
                        row_selection_area_cls,
                        row_selection_area_style, {
                            top: row_selection_area_top,
                            width: 10000
                        });

                    row_selection_area.init($start_cell, $end_cell);
                    row_selection_area.updateRegion();

                    me.row_areas.push(row_selection_area);
                });
            }

            this._destroyRowAreas = function()
            {
                _.each(me.row_areas, function(row_area) {
                    row_area.destroy();
                });

                me.row_areas = [];
            }
        }
        BiggridRowSelection.prototype.constructor = BiggridRowSelection;

        var BiggridConditionEngine = function()
        {
            var me = this;

            this.init = function()
            {
                return me;
            }

            this.evaluate = function(val, condition_obj, col_info)
            {
                if (condition_obj.type === undefined)
                    return false;

                switch (condition_obj.type)
                {
                    case 'condition-empty': return me._evaluateConditionEmpty(val, condition_obj, col_info);
                    case 'condition-value': return me._evaluateConditionValue(val, condition_obj, col_info);
                    case 'condition-range': return me._evaluateConditionRange(val, condition_obj, col_info);
                    case 'condition-color-scale': return me._evaluateConditionColorScale(val, condition_obj, col_info);
                }

                return false;
            }

            this._evaluateConditionEmpty = function(val, condition_obj, col_info)
            {
                var c = condition_obj;

                if (!_.isObject(c.condition))
                    return false;

                if (val === undefined)
                    return false;

                switch (c.condition.operator)
                {
                    case 'empty':
                        if (typeof val == 'string' && isCharacterColumn(col_info))
                            return (val.length == 0) ? true : false;

                        return (val === null) ? true : false;
                }

                return false;
            }

            this._evaluateConditionValue = function(val, condition_obj, col_info)
            {
                var c = condition_obj;

                if (!_.isObject(c.condition))
                    return false;

                var cval = c.condition.val;

                if (val === undefined || cval === undefined)
                    return false;

                if (val.length == 0 || cval.length == 0)
                    return false;

                var nval = val;

                if (typeof val == 'string' && isNumberColumn(col_info))
                    nval = _parseFloat(val);

                switch (c.condition.operator)
                {
                    case 'like':
                        if (typeof val == 'number')
                            val = val.toString();

                        if (typeof val == 'string')
                            return (val.indexOf(cval) != -1) ? true : false;

                        // TODO: date? boolean?
                        return false;

                    case 'nlike':
                        if (typeof val == 'number')
                            val = val.toString();

                        if (typeof val == 'string')
                            return (val.indexOf(cval) == -1) ? true : false;

                        // TODO: date? boolean?
                        return false;

                    case 'startswith':
                        if (typeof val == 'number')
                            val = val.toString();

                        if (typeof val == 'string')
                            return (val.indexOf(cval) == 0) ? true : false;

                        // TODO: date? boolean?
                        return false;

                    case 'endswith':
                        if (typeof val == 'number')
                            val = val.toString();

                        if (typeof val == 'string')
                            return (val.indexOf(cval, val.length - cval.length) != - 1) ? true : false;

                        // TODO: date? boolean?
                        return false;

                    case 'eq':
                        if (typeof val == 'string' || typeof val == 'number')
                            return (val == cval) ? true : false;

                        // TODO: date? boolean?
                        return false;

                    case 'neq':
                        if (typeof val == 'string' || typeof val == 'number')
                            return (val != cval) ? true : false;

                        // TODO: date? boolean?
                        return false;

                    case 'gt':
                        if (typeof val == 'number')
                            return (val > cval) ? true : false;

                        // TODO: date? string? boolean?
                        return false;

                    case 'gte':
                        if (typeof val == 'number')
                            return (val >= cval) ? true : false;

                        // TODO: date? string? boolean?
                        return false;

                    case 'lt':
                        if (typeof val == 'number')
                            return (val < cval) ? true : false;

                        // TODO: date? string? boolean?
                        return false;

                    case 'lte':
                        if (typeof val == 'number')
                            return (val <= cval) ? true : false;

                        // TODO: date? string? boolean?
                        return false;
                }

                return false;
            }

            this._evaluateConditionRange = function(val, condition_obj, col_info)
            {
                var c = condition_obj;

                if (!_.isObject(c.condition))
                    return false;

                var min_val = c.condition.min_val,
                    max_val = c.condition.max_val;

                if (val === undefined || min_val === undefined || max_val === undefined)
                    return false;

                switch (c.condition.operator)
                {
                    case 'between':
                        if (typeof val == 'number')
                            return (val < max_val && val > min_val) ? true : false;

                        // TODO: date? string? boolean?
                        return false;

                    case 'nbetween':
                        if (typeof val == 'number')
                            return (val < max_val && val > min_val) ? false : true;

                        // TODO: date? string? boolean?
                        return false;
                }

                return false;
            }

            this._evaluateConditionColorScale = function(val, condition_obj, col_info)
            {
                var c = condition_obj;

                if (!_.isObject(c.condition))
                    return false;

                var min_val = c.condition.min_val,
                    max_val = c.condition.max_val;

                if (val === undefined || min_val === undefined || max_val === undefined)
                    return false;

                if (val.length == 0 || min_val.length == 0 || max_val.length == 0)
                    return false;

                switch (c.condition.operator)
                {
                    case 'colorscale':
                        if (typeof val == 'number')
                            return true;

                        // TODO: date? string? boolean?
                        return false;

                    case 'databar':
                        if (typeof val == 'number')
                            return true;

                        // TODO: date? string? boolean?
                        return false;

                    case 'gradientdatabar':
                        if (typeof val == 'number')
                            return true;

                        // TODO: date? string? boolean?
                        return false;
                }

                return false;
            }

            return me.init();
        }
        BiggridConditionEngine.prototype.constructor = BiggridConditionEngine;

        var BiggridConditionalFormatter = function()
        {
            var me = this;

            /*
                general condition object structure:

                    {
                        type: <condition_type>,
                        condition: {
                            columns: [] (or * for all),
                            operator: 'like',
                            val: 'BOISE'
                        },
                        style: {
                            'color': '#333333',
                            'background-color': '#ffff00'
                        }
                    }

                NOTE: parameters are just an example; this will vary by type
            */

            this.format = function(val, conditions, col_info)
            {
                if (conditions.length == 0)
                    return '';

                var style = {};

                _.each(conditions, function(condition_obj) {
                    if (!_.isObject(condition_obj))
                        return;

                    $.extend(style, me._getConditionStyle(val, condition_obj, col_info));
                });

                style = _.map(style, function(val, key) {
                    return key+':'+val;
                });
                style = style.join(';');

                return style;
            }

            this._getConditionStyle = function(val, condition_obj, col_info)
            {
                var c = condition_obj;

                if (!_.isObject(c.condition))
                    return {};

                if (_.indexOf(c.condition.columns, '*') == -1)
                {
                    // this column isn't included; we're done
                    if (_.indexOf(c.condition.columns, col_info.name) == -1)
                        return {};
                }

                var match = grid_condition_engine.evaluate(val, condition_obj, col_info);

                if (match === true)
                {
                    if (c.type == 'condition-color-scale')
                    {
                        if (c.condition.operator == 'colorscale')
                            return me._getConditionColorScaleStyle(val, condition_obj, col_info);

                        if (c.condition.operator == 'databar')
                            return me._getConditionColorDataBarStyle(val, condition_obj, col_info);

                        if (c.condition.operator == 'gradientdatabar')
                            return me._getConditionColorGradientDataBarStyle(val, condition_obj, col_info);
                    }

                    return c.style;
                }

                return {};
            }

            this._getConditionColorScaleStyle = function(val, condition_obj, col_info)
            {
                if (typeof val != 'number')
                    return {};

                var c = condition_obj;

                var pct = Math.max(Math.min((val-c.condition.min_val)/c.condition.max_val, 1), 0) * 100,
                    pct = pct.toFixed(2);

                var bg_color = tinycolor.mix(
                    c.style['background-color-start'],
                    c.style['background-color-end'],
                    pct
                );

                return {
                    'color': c.style['color'],
                    'background-color': bg_color.toString('rgb')
                };
            }

            this._getConditionColorDataBarStyle = function(val, condition_obj, col_info)
            {
                if (typeof val != 'number')
                    return {};

                var c = condition_obj,
                    color1 = c.style['background-color-start'],
                    color2 = c.style['background-color-end'];

                var pct = Math.max(Math.min((val-c.condition.min_val)/c.condition.max_val, 1), 0) * 100,
                    pct = pct.toFixed(0);

                return {
                    'color': c.style['color'],
                    'background-image': 'linear-gradient(to right, '+color1+', '+color1+' '+pct+'%, '+color2+' '+pct+'%)',
                    'background-repeat': 'no-repeat'
                };
            }

            this._getConditionColorGradientDataBarStyle = function(val, condition_obj, col_info)
            {
                if (typeof val != 'number')
                    return {};

                var c = condition_obj,
                    color1 = c.style['background-color-start'],
                    color2 = c.style['background-color-end'];

                var pct = Math.max(Math.min((val-c.condition.min_val)/c.condition.max_val, 1), 0) * 100,
                    pct = pct.toFixed(0);

                return {
                    'color': c.style['color'],
                    'background-image': 'linear-gradient(to right, '+color1+', '+color2+' '+pct+'%, '+color2+')',
                    'background-repeat': 'no-repeat'
                };
            }
        }
        BiggridConditionalFormatter.prototype.constructor = BiggridConditionalFormatter;

        var BiggridDefaultCellRenderer = function()
        {
            var me = this,
                this_cell = {};

            var cell_base = {
                cell_content: '',
                cell_style: ''
            };

            this.render = function(val, col_info)
            {
                this_cell = $.extend({}, cell_base);

                // -- render cell content --

                this_cell.cell_content = val;

                // if no column info is provided, just return the value provided
                if (col_info === undefined)
                    return me;

                if (val === null)
                {
                    this_cell.cell_content = opts.nullText;
                }
                 else
                {
                    // date type formatting
                    if (col_info.type === biggrid.TYPE_DATE)
                        this_cell.cell_content = me._getDateString(val);
                }

                // -- render cell styling --

                var cf = grid_conditional_formatting;

                if (val === null)
                    this_cell.cell_style = 'font-style: italic; color: #999';

                // style cells based on conditional formatting options
                if (cf.length > 0)
                    this_cell.cell_style = grid_conditional_formatter.format(val, cf, col_info);

                return me;
            }

            // if this is true, the cell content will be wrapped in a div
            // with class 'bgg-td-wrapper' which has overflow: hidden
            this.isWrapped = function()
            {
                return false;
            }

            this.getCellStyle = function()
            {
                return this_cell.cell_style;
            }

            this.getCellContent = function()
            {
                return this_cell.cell_content;
            }

            // should return either 'text' or 'html'
            this.getContentType = function()
            {
                return 'text';
            }

            this._getDateString = function(val)
            {
                var dt = new Date(val);

                // check valid date
                if (_.isDate(dt) && !isNaN(dt.getTime()))
                    return Util.formatDate(dt);

                return val;
            }
        }
        BiggridDefaultCellRenderer.prototype.constructor = BiggridDefaultCellRenderer;

        var BiggridColumnInfo = function(col_info)
        {
            var me = this;

            this.name = 'Column1';
            this.type = biggrid.TYPE_CHARACTER;
            this.width = 20;
            this.scale = 0;
            this.expression = '';

            // display values
            this.col_id = '';
            this.frozen = false;
            this.hidden = false;
            this.dropped = false;
            this.font = opts.colDefaultFont;
            this.fg_color = opts.colDefaultFgColor;
            this.bg_color = opts.colDefaultBgColor;
            this.pixel_width = opts.colDefaultWidth;
            this.display_name = '';
            this.text_alignment = ''; // 'left'; 'center'; 'right' or ''
            this.header_text_alignment = ''; // 'left'; 'center'; 'right' or ''
            this.header_icon = '';
            this.cell_renderer = ''; // custom cell renderer name

            this.show_default_header_icons = true; // default icons are lock and flash

            var allowed_keys = [
                'name',
                'type',
                'width',
                'scale',
                'expression',

                'col_id',
                'frozen',
                'hidden',
                'dropped',
                'font',
                'fg_color',
                'bg_color',
                'pixel_width',
                'display_name',
                'text_alignment',
                'header_text_alignment',
                'header_icon',
                'cell_renderer',

                'show_default_header_icons'
            ];

            this._init = function()
            {
                $.extend(true, me, col_info);

                return me;
            }

            this._get = function()
            {
                return _.pick(me, allowed_keys);
            }

            return me._init()._get();
        }
        BiggridColumnInfo.prototype.constructor = BiggridColumnInfo;

// -- public methods --

        this.init = function()
        {
            // remove any existing data
            var old_me = $el.data('biggrid');
            if (old_me !== undefined)
                $el.removeData('biggrid');

            // add a reverse reference to the DOM element
            $el.data('biggrid', me);

            // make sure we have scrollbar dimensions immediately
            me._getScrollbarSize(true);

            // set variables based on options
            me.setFont(opts.font);
            me.setMinColumnWidth(opts.colMinWidth);
            me.setMaxColumnWidth(opts.colMaxWidth);

            // "hide" the grid visually from the user without actually hiding it
            $el.addClass('bgg-invisible');

            // render the grid markup
            me._render();

            // create our cell selection helper
            grid_cell_selection = new BiggridCellSelection();

            // create our fixed column selection helper
            grid_fixed_col_selection = new BiggridColumnSelection('fixed');

            // create our column selection helper
            grid_col_selection = new BiggridColumnSelection('data');

            // create our row selection helper
            grid_row_selection = new BiggridRowSelection();

            // create our cell renderer
            grid_default_cell_renderer = new BiggridDefaultCellRenderer();

            // create the condition engine (used by conditional formatter)
            grid_condition_engine = new BiggridConditionEngine();

            // create our conditional formatter (used by cell renderer)
            grid_conditional_formatter = new BiggridConditionalFormatter();

            // set the model
            grid_model = opts.model;

            // if no model is provided, use test model
            if (grid_model === undefined)
                grid_model = new BiggridDefaultModel();

            // set the column dropdown handler
            grid_column_dropdown_handler = opts.colDropdownHandler;

            // if no dropdown handler is provided, use default dropdown
            if (grid_column_dropdown_handler === undefined)
                grid_column_dropdown_handler = new biggrid.plugin.defaultdropdown();

            // initialize the dropdown handler
            grid_column_dropdown_handler.init(me);

            // initialize cell renderers
            me._registerOptionsCellRenderers();

            // initialize plugins
            me._registerOptionsPlugins();

            // initialize our default state now that we have the markup
            // and other variables filled out above
            me._initState();

            // start checking to see if when we become visible for the first time
            // calculate default metrics (row height, fixed column width, etc.)
            me._initMetrics(function() {
                if (me.hasState('bgg-state-show-loading'))
                    me.showMessage(opts.loadingOverlayText, 'overlay-center');
            });

            // create event handler for all grid events
            grid_events = new BiggridEvents();
            grid_events.init();

            // this will get the initial structure from the model and get things going
            if (opts.autoRefreshModel === true)
            {
                me.refreshModel(function() {
                    // if this is the first time we're doing this, render the header
                    if (grid_needs_full_render === true)
                    {
                        // if we haven't set a view yet, do so now
                        if (grid_column_order.length == 0)
                            me.setView(opts.view);

                        if (opts.autoLoadRows !== true)
                            me._renderHeader();
                    }

                    if (opts.autoLoadRows === true)
                        me.getRows({ refreshRows: true });
                });
            }

            return me;
        }

        this.uninit = function(callback)
        {
            if (me.isInsertingColumn())
                me.endColumnInsert(false);

            grid_cell_selection.destroy();
            grid_fixed_col_selection.destroy();
            grid_col_selection.destroy();
            grid_row_selection.destroy();

            // not yet implemented
            // grid_default_cell_renderer.destroy();
            // grid_conditional_formatter.destroy();

            grid_model.uninit();
            grid_events.uninit();

            // uninitialize all plugins
            _.each(grid_plugins, function(plugin, plugin_name) {
                me.unregisterPlugin(plugin_name);
            });

            // uninitialize the dropdown
            if (grid_column_dropdown_handler)
                grid_column_dropdown_handler.destroy();

            if (typeof callback == 'function')
                callback();

            // fire the 'uninit.biggrid' event
            $el.trigger($.Event('uninit.biggrid'));

            return me;
        }

        this.initEvents = function()
        {
            grid_events.init();
            return me;
        }

        this.uninitEvents = function()
        {
            grid_events.uninit();
            return me;
        }

        this.getNamespace = function()
        {
            return ns;
        }

        this.getElement = function()
        {
            return $el;
        }

        this.getOptions = function()
        {
            // copy object to avoid Javascript object references
            return $.extend(true, {}, opts);
        }

        this.getModel = function()
        {
            return grid_model;
        }

        this.setModel = function(model)
        {
            // update/reset variables
            grid_model = model;
            grid_row_count = grid_model.getRowCount();
            grid_needs_full_render = false;

            me.resetColumnInfo();
            me._updateRowCountText();
            me._updateGridBodyHeight();
            me._updateFixedColumnBottom();

            return me;
        }

        this.refreshModel = function(callback)
        {
            grid_model.getColumns({
                start: grid_rendered_row_start || 0,
                limit: (opts.limit == 'auto') ? grid_auto_row_limit : opts.limit,
                callback: function(res) {
                    if (res.success)
                    {
                        grid_columns = res.columns;
                        grid_row_count = grid_model.getRowCount();

                        if (typeof callback == 'function')
                            callback(res);

                        // fire the 'refreshmodel.biggrid' event
                        $el.trigger($.Event('refreshmodel.biggrid'));
                    }
                     else
                    {
                        me.showMessage(res.message, 'overlay');
                    }
                }
            });

            return me;
        }

        this.getFont = function()
        {
            return grid_font;
        }

        this.setFont = function(font)
        {
            grid_font = font;

            var $els = $().add($grid_fixed_header).add($grid_data_header).add($grid_body_wrapper);

            if (font !== undefined && font.length > 0)
                $els.css('font-family', font+', Arial, sans-serif');
                 else
                $els.css('font-family', 'Arial, sans-serif');

            return me;
        }

        this.setMinColumnWidth = function(width)
        {
            grid_column_min_width = width;
            return me;
        }

        this.setMaxColumnWidth = function(width)
        {
            grid_column_max_width = width;
            return me;
        }

        this.setHeaderHeight = function(height) {
            if (height === undefined)
                return me;

            // use default height from CSS
            if (height == 'auto')
            {
                me._updateStyle('header-height', '');

                // update local variable
                grid_header_height = $grid_data_header.outerHeight(true);

                // create our cell selection helper
                if (grid_cell_selection instanceof BiggridCellSelection)
                    grid_cell_selection.destroy();

                grid_cell_selection = new BiggridCellSelection();

                return me;
            }

            var style = '' +
                '.'+ns+' .bgg-fixed-header, ' +
                '.'+ns+' .bgg-data-header { ' +
                    'height: '+height+'px; ' +
                '}\n' +
                '.'+ns+' .bgg-body-wrapper { ' +
                    'top: '+(height-2)+'px; ' + // TODO: assumes 1px border width
                '}\n';

            me._updateStyle('header-height', style);

            // update local variable
            grid_header_height = height;

            // create our cell selection helper
            if (grid_cell_selection instanceof BiggridCellSelection)
                grid_cell_selection.destroy();

            grid_cell_selection = new BiggridCellSelection();

            return me;
        }

        this.getView = function()
        {
            // if we're currently viewing a temporary view,
            // return the saved view as the view
            if (grid_saved_view !== undefined)
                return grid_saved_view;

            // copy object to avoid Javascript object references
            return $.extend(true, {}, {
                version: 1,
                subversion: 0,
                build: 0,
                columns: me.getColumnInfo(),
                columnOrder: me.getColumnOrder(),
                conditionalFormatting: me.getConditionalFormatting()
            });
        }

        this.getInitialView = function()
        {
            // copy object to avoid Javascript object references
            return $.extend(true, {}, {
                version: 1,
                subversion: 0,
                build: 0,
                columns: me.getInitialColumnInfo(),
                columnOrder: me.getInitialColumnOrder(),
                conditionalFormatting: []
            });
        }

        this.getTemporaryView = function()
        {
            if (grid_saved_view === undefined)
                return undefined;

            // copy object to avoid Javascript object references
            return $.extend(true, {}, {
                version: 1,
                subversion: 0,
                build: 0,
                columns: me.getColumnInfo(),
                columnOrder: me.getColumnOrder(),
                conditionalFormatting: me.getConditionalFormatting()
            });
        }

        this.setView = function(view)
        {
            if (view === undefined || !_.isObject(view) || $.isEmptyObject(view))
            {
                // start fresh
                me.resetColumnInfo();
                me.resetColumnOrder();

                return me;
            }

            var initial_columns = me.getInitialColumnInfo();

            // if we have a view object, update the initial column info
            // with the view's column info
            if (_.isObject(view.columns))
            {
                var columns = view.columns;

                // use initial columns as a baseline, filling out the view
                _.each(initial_columns, function(col, key) {
                    if (columns[key] === undefined)
                        columns[key] = col;
                         else
                        columns[key] = $.extend(true, {}, col, columns[key]);
                });

                // strip out view columns that aren't part of ourgrid structure
                var column_names = _.map(grid_columns, 'name');
                columns = _.omit(columns, function(val, key, obj) {
                    return _.includes(column_names, key) ? false : true;
                });

                me.setColumnInfo(columns);
            }
             else
            {
                me.setColumnInfo(initial_columns);
            }

            if (_.isObject(view.columnOrder))
                grid_column_order = view.columnOrder;
                 else
                grid_column_order = me.getInitialColumnOrder();

            // update conditional formatting
            if (view.conditionalFormatting !== undefined)
                me.setConditionalFormatting(view.conditionalFormatting);

            // make sure we recalculate the fixed column widths next time we do a refresh
            me._invalidateFixedColumnWidth();

            // we've successfully loaded a view;
            // there's no need to auto resize columns
            grid_needs_auto_column_resize = false;

            return me;
        }

        this.setTemporaryView = function(view)
        {
            grid_saved_view = grid.getView();
            me.setView(view);
            return me;
        }

        this.clearTemporaryView = function()
        {
            me.setView(grid_saved_view);
            grid_saved_view = undefined;
            return me;
        }

        this.resetView = function()
        {
            me.setView(me.getInitialView());

            // auto resize all columns
            if (opts.autoResizeColumns === true)
            {
                _.each(me.getColumns(), function(col_id) {
                    me.autoResizeColumn(col_id);
                });
            }

            return me;
        }

        this.addState = function(state)
        {
            $el.addClass(state);
            return me;
        }

        this.removeState = function(state)
        {
            $el.removeClass(state);
            return me;
        }

        this.toggleState = function(state)
        {
            $el.toggleClass(state);
            return me;
        }

        this.hasState = function(state)
        {
            return ($el.hasClass(state)) ? true : false;
        }

        this.getPlugin = function(name)
        {
            if (me.hasPlugin(name))
                return grid_plugins[name];
            return null;
        }

        this.hasPlugin = function(name)
        {
            return (_.isObject(grid_plugins[name])) ? true : false;
        }

        this.registerPlugin = function(name, instance)
        {
            if (name === undefined || instance === undefined)
                return me;

            // don't allow duplicate instances of the same plugin
            if (me.hasPlugin(name))
                return me;

            if (typeof instance.init == 'function')
                instance.init(me);

            // map this plugin name to the instantiated instance of the plugin
            grid_plugins[name] = instance;

            return me;
        }

        this.unregisterPlugin = function(name)
        {
            if (name === undefined || grid_plugins[name] === undefined)
                return me;

            if (typeof grid_plugins[name].destroy == 'function')
                grid_plugins[name].destroy();

            grid_plugins[name] = undefined;
            delete grid_plugins[name];

            return me;
        }

        this.getDefaultCellRenderer = function()
        {
            return grid_default_cell_renderer;
        }

        this.getCellRenderer = function(name)
        {
            if (me.hasCellRenderer(name))
                return grid_cell_renderers[name];
            return me.getDefaultCellRenderer();
        }

        this.hasCellRenderer = function(name)
        {
            return (_.isObject(grid_cell_renderers[name])) ? true : false;
        }

        this.registerCellRenderer = function(name, instance)
        {
            if (name === undefined || instance === undefined)
                return me;

            // don't allow duplicate instances of the same cell renderer
            if (me.hasCellRenderer(name))
                return me;

            if (typeof instance.init == 'function')
                instance.init(me);

            // map this cell renderer name to the instantiated instance of the cell renderer
            grid_cell_renderers[name] = instance;

            return me;
        }

        this.unregisterCellRenderer = function(name)
        {
            if (name === undefined || grid_cell_renderers[name] === undefined)
                return me;

            if (typeof grid_cell_renderers[name].destroy == 'function')
                grid_cell_renderers[name].destroy();

            grid_cell_renderers[name] = undefined;
            delete grid_cell_renderers[name];

            return me;
        }

        this.getConditionalFormatting = function()
        {
            return grid_conditional_formatting;
        }

        this.setConditionalFormatting = function(val)
        {
            if (_.isObject(val) && !$.isArray(val))
                val = [val];

            if (!$.isArray(val))
                return me;

            grid_conditional_formatting = val;
        }

        this.clearConditionalFormatting = function()
        {
            grid_conditional_formatting = [];
        }

        // -- cell selection methods --

        this.selectCell = function(row_id, col_id, add_to_selection)
        {
            if (!me.isCellSelectionAllowed())
                return me;

            if (row_id === undefined || row_id.length == 0 ||
                col_id === undefined || col_id.length == 0)
            {
                return me;
            }

            grid_cell_selection.selectCell(row_id, col_id, add_to_selection);
            grid_cell_selection.refresh();

            // fire the 'selectcell.biggrid' event
            $el.trigger($.Event('selectcell.biggrid'), {
                row: row_id,
                column: col_id
            });

            return me;
        }

        this.deselectCell = function(row_id, col_id)
        {
            if (row_id === undefined || row_id.length == 0 ||
                col_id === undefined || col_id.length == 0)
            {
                return me;
            }

            grid_cell_selection.deselectCell(row_id, col_id);
            grid_cell_selection.refresh();

            // fire the 'deselectcell.biggrid' event
            $el.trigger($.Event('deselectcell.biggrid'), {
                row: row_id,
                column: col_id
            });

            return me;
        }

        this.clearCellSelection = function()
        {
            grid_cell_selection.clearSelection();

            // fire the 'clearcellselection.biggrid' event
            $el.trigger($.Event('clearcellselection.biggrid'));

            return me;
        }

        this.isCellSelected = function(row_id, col_id)
        {
            return grid_cell_selection.isCellSelected(row_id, col_id) ? true : false;
        }

        this.getSelectedCells = function()
        {
            return grid_cell_selection.getSelectedCells();
        }

        this.getSelectedCellCount = function()
        {
            return me.getSelectedCells().length;
        }

        this.setCellSelectionAllowed = function(val, keep_selection)
        {
            if (val === true)
            {
                me.addState('bgg-state-allow-cell-selection');
            }
             else
            {
                me.removeState('bgg-state-allow-cell-selection');

                if (keep_selection !== true)
                    me.clearCellSelection();
            }

            return me;
        }

        this.isCellSelectionAllowed = function()
        {
            return (me.hasState('bgg-state-allow-cell-selection')) ? true : false;
        }

        // -- column methods --

        this.createColumn = function(col_info)
        {
            return new BiggridColumnInfo(col_info);
        }

        this.getInitialColumnInfo = function()
        {
            // TODO: store initial columns as a variable so we don't have
            //       to do this every time we want to get info for a column
            var cols = {};

            var key_type = grid_model.getKeyType(),
                col_idx = 0,
                col_id = '';

            var default_col_info = new BiggridColumnInfo();

            _.each(grid_columns, function(col) {
                // make sure column has default info
                var col_info = new BiggridColumnInfo(col);

                // match display name with column name
                if (!col_info.display_name || col_info.display_name.length == 0)
                    col_info.display_name = col.name;

                // readable cell id and class
                if (key_type == 'name-based')
                    col_id = col.name.toLowerCase().replace(/ /g,'-');
                     else
                    col_id = opts.colClsPrefix+col_idx;

                // add 'col_id' to column info
                col_info.col_id = col_id;

                // associate column info with each column class
                cols[col_id] = col_info;

                col_idx++;
            });

            return cols;
        }

        this.getColumnInfo = function(col_id, key)
        {
            // if no parameters were passed, return all info for all columns
            if (col_id === undefined)
            {
                // copy object to avoid Javascript object references
                return $.extend(true, {}, grid_column_info);
            }

            // if no column was specified; bail out
            if (col_id === '')
                return undefined;

            // if we can't find info for the specified column, bail out
            var col_info = grid_column_info[col_id];
            if (col_info === undefined)
                return undefined;

            // if no key was provided, return all info for the specified column
            if (key === undefined)
            {
                // copy object to avoid Javascript object references
                return $.extend(true, {}, col_info);
            }

            // if we can't find the specified property for the column, bail out
            var val = col_info[key];
            if (val === undefined)
                return undefined;

            if (_.isObject(val))
            {
                // copy object to avoid Javascript object references
                return $.extend(true, {}, val);
            }

            return val;
        }

        this.setColumnInfo = function(col_id, key, val)
        {
            if (col_id === undefined || col_id == '')
                return me;

            // if we pass an object as the first parameter, do a "deep"
            // overwrite of the existing column info
            if (_.isObject(col_id))
            {
                var old_obj = grid_column_info,
                    new_obj = col_id;
                grid_column_info = $.extend(true, {}, old_obj, new_obj);
                return me;
            }

            // if we pass an object as the second parameter,
            // update a single column's info
            if (_.isObject(key))
            {
                var old_obj = grid_column_info[col_id],
                    new_obj = key;
                grid_column_info[col_id] = $.extend(true, {}, old_obj, new_obj);
                return me;
            }

            grid_column_info[col_id][key] = val;

            return me;
        }

        this.resetColumnInfo = function()
        {
            // set up the grid column types associative lookup array
            grid_column_info = me.getInitialColumnInfo();

            // make sure we re-render the faux grid next time we refresh rows
            me._invalidateFauxRows();

            // make sure we recalculate the fixed column widths next time we do a refresh
            me._invalidateFixedColumnWidth();

            return me;
        }

        this.getInitialColumnOrder = function(include_info)
        {
            if (include_info === true)
                return _.toArray(me.getInitialColumnInfo());

            return _.keys(me.getInitialColumnInfo());
        }

        this.getColumnOrder = function(include_info, frozen_first)
        {
            var cols = grid_column_order;

            if (include_info === true)
            {
                cols = _.map(cols, function(col) {
                    return grid.getColumnInfo(col);
                });
            }

            if (frozen_first === true)
            {
                // move frozen columns to the front of the array
                cols = _.sortBy(cols, function(col) {
                    var frozen = false;

                    if (include_info === true)
                        frozen = col.frozen;
                         else
                        frozen = me.isColumnFrozen(col);

                    return (frozen === false) ? true : false;
                });
            }

            return cols;
        }

        // NOTE: the result set will not include any hidden columns;
        //       to include hidden columns, use getColumnOrder()
        this.getVisualColumnOrder = function(include_info)
        {
            var cols = grid_column_visual_order;

            if (include_info === true)
            {
                cols = _.map(cols, function(col) {
                    return grid.getColumnInfo(col);
                });
            }

            return cols;
        }

        this.setColumnOrder = function(col_order)
        {
            grid_column_order = col_order;

            // make sure we re-render the faux grid next time we refresh rows
            me._invalidateFauxRows();

            // make sure we recalculate the fixed column widths next time we do a refresh
            me._invalidateFixedColumnWidth();

            // fire the 'setcolumnorder.biggrid' event
            $el.trigger($.Event('setcolumnorder.biggrid'), {
                columnOrder: col_order
            });

            return me;
        }

        this.resetColumnOrder = function()
        {
            return me.setColumnOrder(me.getInitialColumnOrder());
        }

        this.getColumns = function(include_info, include_dropped)
        {
            // add 'col_id' to info
            var cols = _.map(grid_column_info, function(col, key) {
                return _.extend({}, col, { col_id: key });
            });

            // even though we still have access to dropped columns, they are not
            // to be included in the column list unless explicitly requested
            if (include_dropped !== true)
            {
                cols = _.filter(cols, function(col) {
                    return col.dropped === false;
                });
            }

            if (include_info === true)
                return cols;
                 else
                return _.map(cols, 'col_id');
        }

        this.getColumnCount = function()
        {
            return me.getColumns().length;
        }

        this.setColumnPosition = function(col_id, new_idx)
        {
            // out-of-bounds check
            if (new_idx >= me.getColumnCount())
                return me;

            var cur_idx = me.getColumnPosition(col_id);

            // we're done
            if (new_idx == cur_idx)
                return me;

            grid_column_order = _.move(grid_column_order, cur_idx, new_idx);

            // make sure we re-render the faux grid next time we refresh rows
            me._invalidateFauxRows();

            // fire the 'setcolumnposition.biggrid' event
            $el.trigger($.Event('setcolumnposition.biggrid'), {
                column: col_id,
                oldPosition: cur_idx,
                newPosition: new_idx
            });

            return me;
        }

        this.getColumnPosition = function(col_id)
        {
            if (col_id == opts.colInvalidCls)
                return grid_column_order.length;
            return _.indexOf(grid_column_order, col_id);
        }

        this.getColumnVisualPosition = function(col_id)
        {
            if (col_id == opts.colInvalidCls)
                return grid_column_visual_order.length;
            return _.indexOf(grid_column_visual_order, col_id);
        }

        this.renameColumn = function(col_id, new_val)
        {
            if (!me.isColumnRenameAllowed())
                return me;

            if (new_val === undefined || new_val.length == 0)
                return false;

            var col = me.getColumnInfo(col_id);
            if (col === undefined)
                return false;

            me.setColumnInfo(col_id, 'display_name', new_val);

            // fire the 'renamecolumn.biggrid' event
            $el.trigger($.Event('renamecolumn.biggrid'), {
                column: col_id,
                oldName: col.display_name,
                newName: new_val
            });

            return true;
        }

        this.setColumnDropdownAllowed = function(val)
        {
            if (val === true)
            {
                me.addState('bgg-state-allow-col-dropdown');
            }
             else
            {
                me.removeState('bgg-state-allow-col-dropdown');
            }

            return me;
        }

        this.isColumnDropdownAllowed = function()
        {
            return (me.hasState('bgg-state-allow-col-dropdown')) ? true : false;
        }

        this.setColumnRenameAllowed = function(val)
        {
            if (val === true)
            {
                me.addState('bgg-state-allow-col-rename');
            }
             else
            {
                me.removeState('bgg-state-allow-col-rename');
            }

            return me;
        }

        this.isColumnRenameAllowed = function()
        {
            return (me.hasState('bgg-state-allow-col-rename')) ? true : false;
        }

        this.setColumnCellRenderer = function(col_id, val)
        {
            if (val === undefined)
                return me;

            var col = me.getColumnInfo(col_id);
            if (col === undefined)
                return me;

            // revert to default cell renderer
            if (val === '')
                return me.setColumnInfo(col_id, 'cell_renderer', '');

            if (grid_cell_renderers[val] === undefined)
                return me;

            return me.setColumnInfo(col_id, 'cell_renderer', val);
        }

        // 'left', 'center', 'right' or '' (default)
        this.setColumnAlignment = function(col_id, val, include_header)
        {
            if (val === undefined)
                return me;

            if (val != 'left' && val != 'center' && val != 'right' && val != '')
                return me;

            var col = me.getColumnInfo(col_id);
            if (col === undefined)
                return me;

            if (include_header === true)
                me.setColumnInfo(col_id, 'header_text_alignment', val);

            return me.setColumnInfo(col_id, 'text_alignment', val);
        }

        // 'left', 'center', 'right' or '' (default)
        this.setColumnHeaderAlignment = function(col_id, val)
        {
            if (val === undefined)
                return me;

            if (val != 'left' && val != 'center' && val != 'right' && val != '')
                return me;

            var col = me.getColumnInfo(col_id);
            if (col === undefined)
                return me;

            return me.setColumnInfo(col_id, 'header_text_alignment', val);
        }

        // 'fa fa-*', 'glyphicon glyphicon-*, true (default) or false (hide)
        this.setColumnHeaderIcon = function(col_id, val)
        {
            if (val === undefined)
                return me;

            var col = me.getColumnInfo(col_id);
            if (col === undefined)
                return me;

            // poorly-formatted icon string; bail out
            var arr_vals = val.split(' ');
            if (_.indexOf(arr_vals, 'fa') == -1 && _.indexOf(arr_vals, 'glyphicon') == -1)
                return me;

            return me.setColumnInfo(col_id, 'header_icon', val);
        }

        // lock and flash icons
        this.showColumnHeaderDefaultIcons = function(col_id)
        {
            var col = me.getColumnInfo(col_id);
            if (col === undefined)
                return me;

            return me.setColumnInfo(col_id, 'show_default_header_icons', true);
        }

        // lock and flash icons
        this.hideColumnHeaderDefaultIcons = function(col_id)
        {
            var col = me.getColumnInfo(col_id);
            if (col === undefined)
                return me;

            return me.setColumnInfo(col_id, 'show_default_header_icons', false);
        }

        // -- visible/hidden column methods --

        this.showColumn = function(col_id)
        {
            // column was already shown; we're done
            var was_hidden = me.getColumnInfo(col_id, 'hidden');
            if (was_hidden === false)
                return me;

            me.setColumnInfo(col_id, 'hidden', false);

            // make sure we re-render the faux grid next time we refresh rows
            me._invalidateFauxRows();

            // make sure we recalculate the fixed column widths next time we do a refresh
            if (me.getColumnInfo(col_id, 'frozen') === true)
                me._invalidateFixedColumnWidth();

            me.clearCellSelection();
            me.clearColumnSelection();

            // fire the 'showcolumn.biggrid' event
            $el.trigger($.Event('showcolumn.biggrid'), { column: col_id });

            return me;
        }

        this.hideColumn = function(col_id)
        {
            // column was already hidden; we're done
            var was_hidden = me.getColumnInfo(col_id, 'hidden');
            if (was_hidden === true)
                return me;

            me.setColumnInfo(col_id, 'hidden', true);

            // make sure we re-render the faux grid next time we refresh rows
            me._invalidateFauxRows();

            // make sure we recalculate the fixed column widths next time we do a refresh
            if (me.getColumnInfo(col_id, 'frozen') === true)
                me._invalidateFixedColumnWidth();

            me.clearCellSelection();
            me.clearColumnSelection();

            // fire the 'hidecolumn.biggrid' event
            $el.trigger($.Event('hidecolumn.biggrid'), { column: col_id });

            return me;
        }

        this.showAllColumns = function()
        {
            var cols = me.getColumns();

            _.each(cols, function(col_id) {
                me.setColumnInfo(col_id, 'hidden', false);
            });

            // make sure we re-render the faux grid next time we refresh rows
            me._invalidateFauxRows();

            // make sure we recalculate the fixed column widths next time we do a refresh
            me._invalidateFixedColumnWidth();

            me.clearCellSelection();
            me.clearColumnSelection();

            // fire the 'showallcolumns.biggrid' event
            $el.trigger($.Event('showallcolumns.biggrid'));

            return me;
        }

        this.isColumnVisible = function(col_id)
        {
            return _.includes(me.getVisibleColumns(), col_id) ? true : false;
        }

        this.isColumnHidden = function(col_id)
        {
            return _.includes(me.getHiddenColumns(), col_id) ? true : false;
        }

        this.getVisibleColumns = function(include_info)
        {
            var cols = _.filter(me.getColumns(true), function(col) {
                return col.hidden === false;
            });

            if (include_info === true)
                return cols;
                 else
                return _.map(cols, 'col_id');
        }

        this.getHiddenColumns = function(include_info)
        {
            var cols = _.filter(me.getColumns(true), function(col) {
                return col.hidden === true;
            });

            if (include_info === true)
                return cols;
                 else
                return _.map(cols, 'col_id');
        }

        this.getVisibleColumnCount = function()
        {
            return me.getVisibleColumns().length;
        }

        this.getHiddenColumnCount = function()
        {
            return me.getHiddenColumns().length;
        }

        // -- dropped column methods --

        this.dropColumn = function(col_id)
        {
            // column was already dropped; we're done
            var was_dropped = me.getColumnInfo(col_id, 'dropped');
            if (was_dropped === true)
                return me;

            me.setColumnInfo(col_id, 'dropped', true);

            // make sure we re-render the faux grid next time we refresh rows
            me._invalidateFauxRows();

            // make sure we recalculate the fixed column widths next time we do a refresh
            if (me.getColumnInfo(col_id, 'frozen') === true)
                me._invalidateFixedColumnWidth();

            me.clearCellSelection();
            me.clearColumnSelection();

            // fire the 'dropcolumn.biggrid' event
            $el.trigger($.Event('dropcolumn.biggrid'), { column: col_id });

            return me;
        }

        this.restoreColumn = function(col_id)
        {
            // column was already restored; we're done
            var was_dropped = me.getColumnInfo(col_id, 'dropped');
            if (was_dropped === false)
                return me;

            me.setColumnInfo(col_id, 'dropped', false);

            // make sure we re-render the faux grid next time we refresh rows
            me._invalidateFauxRows();

            // make sure we recalculate the fixed column widths next time we do a refresh
            if (me.getColumnInfo(col_id, 'frozen') === true)
                me._invalidateFixedColumnWidth();

            me.clearCellSelection();
            me.clearColumnSelection();

            // fire the 'restorecolumn.biggrid' event
            $el.trigger($.Event('restorecolumn.biggrid'), { column: col_id });

            return me;
        }

        this.isColumnDropped = function(col_id)
        {
            return _.includes(me.getDroppedColumns(), col_id) ? true : false;
        }

        this.getDroppedColumns = function(include_info)
        {
            var cols = _.filter(me.getColumns(true, true), function(col) {
                return col.dropped === true;
            });

            if (include_info === true)
                return cols;
                 else
                return _.map(cols, 'col_id');
        }

        this.getDroppedColumnCount = function()
        {
            return me.getDroppedColumns().length;
        }

        // -- frozen column methods --

        this.freezeColumn = function(col_id, freeze)
        {
            var was_frozen = me.getColumnInfo(col_id, 'frozen');

            // column was already frozen; we're done
            if (freeze !== false && was_frozen === true)
                return me;

            // column was already unfrozen; we're done
            if (freeze === false && was_frozen === false)
                return me;

            me.setColumnInfo(col_id, 'frozen', freeze === false ? false : true);

            // make sure we re-render the faux grid next time we refresh rows
            me._invalidateFauxRows();

            // make sure we recalculate the fixed column widths next time we do a refresh
            me._invalidateFixedColumnWidth();

            me.clearCellSelection();
            me.clearColumnSelection();

            // fire the 'freezecolumn.biggrid' or 'unfreezecolumn.biggrid' event
            if (freeze === false)
                $el.trigger($.Event('unfreezecolumn.biggrid'), { column: col_id });
                 else
                $el.trigger($.Event('freezecolumn.biggrid'), { column: col_id });

            return me;
        }

        this.unfreezeColumn = function(col_id)
        {
            return me.freezeColumn(col_id, false);
        }

        this.bulkFreezeColumns = function(col_ides, freeze)
        {
            // no array provided; bail out
            if (!$.isArray(col_ides))
                return me;

            // empty array; we're done
            if (col_ides.length == 0)
                return me;

            var all_frozen = _.every(col_ides, function(col_id) {
                return me.isColumnFrozen(col_id) ? true : false;
            });

            // columns were already frozen; we're done
            if (freeze !== false && all_frozen === true)
                return me;

            var all_unfrozen = _.every(col_ides, function(col_id) {
                return me.isColumnFrozen(col_id) ? false : true;
            });

            // column were already unfrozen; we're done
            if (freeze === false && all_unfrozen === true)
                return me;

            _.each(col_ides, function(col_id) {
                me.setColumnInfo(col_id, 'frozen', freeze === false ? false : true);
            });

            // make sure we re-render the faux grid next time we refresh rows
            me._invalidateFauxRows();

            // make sure we recalculate the fixed column widths next time we do a refresh
            me._invalidateFixedColumnWidth();

            me.clearCellSelection();
            me.clearColumnSelection();

            // fire the 'bulkfreezecolumns.biggrid' or 'bulkunfreezecolumns.biggrid' event
            if (freeze === false)
                $el.trigger($.Event('bulkunfreezecolumns.biggrid'), { columns: col_ides });
                 else
                $el.trigger($.Event('bulkfreezecolumns.biggrid'), { columns: col_ides });

            return me;
        }

        this.isColumnFrozen = function(col_id)
        {
            return _.includes(me.getFrozenColumns(), col_id) ? true : false;
        }

        this.getFrozenColumns = function(include_info)
        {
            var cols = _.filter(me.getColumns(true), function(col) {
                return col.frozen === true;
            });

            if (include_info === true)
                return cols;
                 else
                return _.map(cols, 'col_id');
        }

        this.getFrozenColumnCount = function()
        {
            return me.getFrozenColumns().length;
        }

        // -- selected column methods --

        this.selectColumn = function(col_id, add_to_selection)
        {
            if (!me.isColumnSelectionAllowed())
                return me;

            if (col_id === undefined || col_id.length == 0)
                return me;

            // don't allow hidden columns to be selected
            if (!me.isColumnVisible(col_id))
                return me;

            if (me.isColumnFrozen(col_id))
            {
                grid_fixed_col_selection.selectColumn(col_id, add_to_selection);
                grid_fixed_col_selection.refresh(true);
            }
             else
            {
                grid_col_selection.selectColumn(col_id, add_to_selection);
                grid_col_selection.refresh(true);
            }

            // fire the 'selectcolumn.biggrid' event
            $el.trigger($.Event('selectcolumn.biggrid'), { column: col_id });

            return me;
        }

        this.deselectColumn = function(col_id)
        {
            if (col_id === undefined || col_id.length == 0)
                return me;

            if (me.isColumnFrozen(col_id))
            {
                grid_fixed_col_selection.deselectColumn(col_id);
                grid_fixed_col_selection.refresh(true);
            }
             else
            {
                grid_col_selection.deselectColumn(col_id);
                grid_col_selection.refresh(true);
            }

            // fire the 'deselectcolumn.biggrid' event
            $el.trigger($.Event('deselectcolumn.biggrid'), { column: col_id });

            return me;
        }

        this.selectAllColumns = function()
        {
            if (!me.isColumnSelectionAllowed())
                return me;

            grid_cell_selection.clearSelection();
            grid_row_selection.clearSelection();

            var visible_cols = me.getVisibleColumns(),
                frozen_cols = _.intersection(me.getFrozenColumns(), visible_cols),
                cols = _.difference(visible_cols, frozen_cols);

            grid_fixed_col_selection.selectColumn(frozen_cols);
            grid_fixed_col_selection.refresh(true);

            grid_col_selection.selectColumn(cols);
            grid_col_selection.refresh(true);

            // fire the 'selectallcolumns.biggrid' event
            $el.trigger($.Event('selectallcolumns.biggrid'));

            return me;
        }

        this.clearColumnSelection = function()
        {
            grid_fixed_col_selection.clearSelection();
            grid_col_selection.clearSelection();

            // fire the 'clearcolumnselection.biggrid' event
            $el.trigger($.Event('clearcolumnselection.biggrid'));

            return me;
        }

        this.isColumnSelected = function(col_id)
        {
            var fixed_col_selected = grid_fixed_col_selection.isColumnSelected(col_id) ? true : false,
                col_selected = grid_col_selection.isColumnSelected(col_id) ? true : false;

            return (fixed_col_selected || col_selected) ? true : false;
        }

        this.getSelectedColumns = function(include_info)
        {
            var fixed_selected_cols = grid_fixed_col_selection.getSelectedColumns(),
                selected_cols = grid_col_selection.getSelectedColumns(),
                all_selected_cols = _.union(fixed_selected_cols, selected_cols),
                all_selected_cols = _.intersection(me.getVisibleColumns(), all_selected_cols);

            if (include_info !== true)
                return all_selected_cols;

            var cols = [];

            _.each(all_selected_cols, function(col_id) {
                cols.push(me.getColumnInfo(col_id));
            });

            return cols;
        }

        this.getSelectedColumnCount = function()
        {
            return me.getSelectedColumns().length;
        }

        this.setColumnSelectionAllowed = function(val, keep_selection)
        {
            if (val === true)
            {
                me.addState('bgg-state-allow-col-selection');
            }
             else
            {
                me.removeState('bgg-state-allow-col-selection');

                if (keep_selection !== true)
                    me.clearColumnSelection();
            }

            return me;
        }

        this.isColumnSelectionAllowed = function()
        {
            return (me.hasState('bgg-state-allow-col-selection')) ? true : false;
        }

        // -- selected row methods --

        this.selectRow = function(row_id, add_to_selection)
        {
            if (!me.isRowSelectionAllowed())
                return me;

            if (row_id === undefined || row_id.length == 0)
                return me;

            grid_row_selection.selectRow(row_id, add_to_selection);
            grid_row_selection.refresh(true);

            // fire the 'selectrow.biggrid' event
            $el.trigger($.Event('selectrow.biggrid'), { row: row_id });

            return me;
        }

        this.deselectRow = function(row_id)
        {
            if (row_id === undefined || row_id.length == 0)
                return me;

            grid_row_selection.deselectRow(row_id);
            grid_row_selection.refresh(true);

            // fire the 'deselectrow.biggrid' event
            $el.trigger($.Event('deselectrow.biggrid'), { row: row_id });

            return me;
        }

        this.clearRowSelection = function()
        {
            grid_row_selection.clearSelection();

            // fire the 'clearrowselection.biggrid' event
            $el.trigger($.Event('clearrowselection.biggrid'));

            return me;
        }

        this.isRowSelected = function(row_id)
        {
            return grid_row_selection.isRowSelected(row_id) ? true : false;
        }

        this.getSelectedRows = function()
        {
            return grid_row_selection.getSelectedRows();
        }

        this.getSelectedRowCount = function()
        {
            return me.getSelectedRows().length;
        }

        this.setRowSelectionAllowed = function(val, keep_selection)
        {
            if (val === true)
            {
                me.addState('bgg-state-allow-row-selection');
            }
             else
            {
                me.removeState('bgg-state-allow-row-selection');

                if (keep_selection !== true)
                    me.clearRowSelection();
            }

            return me;
        }

        this.isRowSelectionAllowed = function()
        {
            return (me.hasState('bgg-state-allow-row-selection')) ? true : false;
        }

        // -- row methods --

        this.getRowCount = function()
        {
            return grid_row_count;
        }

        this.getRowCountString = function()
        {
            return grid_row_count_text;
        }

        this.getVisibleRows = function()
        {
            var start = grid_visible_row_start-grid_rendered_row_start,
                end = grid_visible_row_end-grid_rendered_row_start;

            return grid_rows.slice(start, end);
        }

        this.getVisibleRowStart = function()
        {
            return grid_visible_row_start;
        }

        this.getVisibleRowEnd = function()
        {
            return grid_visible_row_end;
        }

        this.getVisibleRowCount = function()
        {
            return grid_visible_row_count;
        }

        this.getRenderedRows = function()
        {
            return grid_rows;
        }

        this.getRenderedRowStart = function()
        {
            return grid_rendered_row_start;
        }

        this.getRenderedRowEnd = function()
        {
            return grid_rendered_row_end;
        }

        this.getRenderedRowCount = function()
        {
            return (grid_rendered_row_end-grid_rendered_row_start);
        }

        this.setCellVerticalAlignment = function(alignment)
        {
            if (alignment === undefined)
                return me;

            // use default height from CSS
            if (alignment == 'auto')
            {
                grid_cell_vertical_align = alignment;
                me._updateStyle('cell-vertical-align', '');
                return me;
            }

            grid_cell_vertical_align = alignment;

            var cell_style = '' +
                '.'+ns+' .'+cls_td+' { ' +
                    'vertical-align: '+grid_cell_vertical_align+'; ' +
                '}\n';

            me._updateStyle('cell-vertical-align', cell_style);

            return me;
        }

        this.getCellVerticalAlignment = function()
        {
            return grid_cell_vertical_align;
        }

        this.setRowHeight = function(height)
        {
            if (height === undefined)
                return me;

            // use default height from CSS
            if (height == 'auto')
                grid_row_height = grid_auto_row_height;
                 else
                grid_row_height = height;

            var cell_style = '' +
                '.'+ns+' .'+cls_td+' { ' +
                    'height: '+grid_row_height+'px; ' +
                '}\n' +
                '.'+ns+' .'+opts.cellWrapperCls+' { ' +
                    'height: '+(grid_row_height-1)+'px; ' + // TODO: assumes 1px border width
                '}\n';

            me._updateStyle('row-height', cell_style);

            return me;
        }

        this.getRowHeight = function()
        {
            return grid_row_height;
        }

        this.getRowIdx = function(row_id)
        {
            var $tr = $grid_fixed_columns.find('tr.'+row_id);
            if ($tr.length == 0)
                return undefined;
            return $tr.data('row-idx');
        }

        this.getRows = function(row_opts)
        {
            row_opts = $.extend({}, row_opts);

            var default_limit = (opts.limit == 'auto') ? grid_auto_row_limit : opts.limit,
                start = row_opts.start || opts.start,
                limit = row_opts.limit || default_limit,
                refresh_rows = row_opts.refreshRows === true ? true : false,
                callback = row_opts.callback;

            // if we have a fast grid model, we only need to render the rows
            // that are visible to the user
            if (grid_model.isFastGetRows())
                limit = grid_visible_row_count;

            grid_row_start = start;
            grid_row_limit = limit;

            // add new loading element
            if (me.hasState('bgg-state-show-loading'))
                me.showMessage(opts.loadingText, 'bottom');

            grid_model.getRows({
                start: start,
                limit: limit,
                sort: row_opts.sort,
                filter: row_opts.filter,
                callback: function(res) {
                    if (res.success)
                    {
                        var old_row_count = grid_row_count;

                        grid_row_count = grid_model.getRowCount();
                        grid_rows = res.rows;

                        if (grid_needs_header_render === true)
                            me._renderHeader();

                        if (refresh_rows === true)
                        {
                            me.refreshRows(grid_rows, function() {
                                if (row_opts.sort !== undefined || row_opts.filter !== undefined)
                                {
                                    // calling just .scrollTop(0) here causes the vertical
                                    // scrollbar handle to become "stuck" on some browsers
                                    $grid_body.scrollTop(1);
                                    $grid_body.scrollTop(0);

                                    // move grid body table so it is always in the viewport
                                    $grid_data_table.css('top', '0px');
                                    $grid_fixed_table.css('top', '0px');

                                    // update the scrollbars
                                    if (grid_custom_scrollbars === true)
                                        $grid_body.data('biggridscrollbars').update();
                                }

                                if (old_row_count != grid_row_count || grid_needs_full_render === true)
                                {
                                    me._updateRowCountText();
                                    me._updateGridBodyHeight();
                                    me._updateFixedColumnBottom();
                                }

                                me.hideMessage();

                                if (typeof callback == 'function')
                                    callback(res);

                                // fire the 'getrows.biggrid' event
                                $el.trigger($.Event('getrows.biggrid'), res);
                            });
                        }
                         else
                        {
                            if (old_row_count != grid_row_count || grid_needs_full_render === true)
                            {
                                me._updateRowCountText();
                                me._updateGridBodyHeight();
                                me._updateFixedColumnBottom();
                            }

                            me.hideMessage();

                            if (typeof callback == 'function')
                                callback(res);

                            // fire the 'getrows.biggrid' event
                            $el.trigger($.Event('getrows.biggrid'), res);
                        }
                    }
                     else
                    {
                        var msg = _T('There was a problem getting the requested rows. Try refreshing the page to resolve the problem.');
                        me.showMessage(msg, 'overlay');
                    }
                }
            });

            return me;
        }

        // -- option methods --

        this.setCustomScrollbars = function(val, scrollbar_opts)
        {
            // destroy the custom scrollbars if they exist
            if ($grid_body.data('biggridscrollbars') !== undefined)
                $grid_body.data('biggridscrollbars').destroy();

            if (val === true)
            {
                // initialize the custom scrollbars
                if ($grid_body.data('biggridscrollbars') === undefined)
                    $grid_body.biggridscrollbars(scrollbar_opts || {});

                grid_custom_scrollbars = true;
            }
             else
            {
                grid_custom_scrollbars = false;
            }

            me._updateFixedColumnBottom();
            return me;
        }

        this.setFullscreen = function(val)
        {
            if (val === true)
            {
                me.addState('bgg-state-fullscreen');

                // remove custom scrollbars when entering fullscreen mode
                if (opts.customScrollbars === true && opts.customScrollbarsFullscreen !== true)
                    me.setCustomScrollbars(false);
            }
             else
            {
                me.removeState('bgg-state-fullscreen');

                // remove custom scrollbars when leaving fullscreen mode
                if (opts.customScrollbars === true)
                    me.setCustomScrollbars(true);
            }

            // sync header offset with body offset
            if ($grid_body.length > 0)
                $grid_data_header.css('left', -1*($grid_body.get(0)).scrollLeft);

            // update parent offset
            parent_offset = $el.offset();

            return me;
        }

        this.setShowLoading = function(val)
        {
            if (val === true)
                me.addState('bgg-state-show-loading');
                 else
                me.removeState('bgg-state-show-loading');

            return me;
        }

        this.setFauxRowsVisible = function(val)
        {
            if (val === true)
                me.addState('bgg-state-show-faux-rows');
                 else
                me.removeState('bgg-state-show-faux-rows');

            return me;
        }

        this.setStripedRowsVisible = function(val)
        {
            if (val === true)
                me.addState('bgg-state-show-striped-rows');
                 else
                me.removeState('bgg-state-show-striped-rows');

            return me;
        }

        this.setVerticalLinesVisible = function(val)
        {
            if (val === true)
                me.addState('bgg-state-show-vert-lines');
                 else
                me.removeState('bgg-state-show-vert-lines');

            return me;
        }

        this.setHorizontalLinesVisible = function(val)
        {
            if (val === true)
                me.addState('bgg-state-show-horz-lines');
                 else
                me.removeState('bgg-state-show-horz-lines');

            return me;
        }

        this.setWhitespaceVisible = function(val)
        {
            opts.showWhitespace = val === true ? true : false;
            return me;
        }

        this.setRowNumbersVisible = function(val)
        {
            if (val === true)
                me.addState('bgg-state-show-row-numbers');
                 else
                me.removeState('bgg-state-show-row-numbers');

            me._handleRowHandleResize();

            return me;
        }

        this.setRowHandlesVisible = function(val)
        {
            if (val === true)
                me.addState('bgg-state-show-bgg-tr-handles');
                 else
                me.removeState('bgg-state-show-bgg-tr-handles');

            me._handleRowHandleResize();

            return me;
        }

        this.setFixedColumnsVisible = function(val)
        {
            if (val === true)
                me.addState('bgg-state-show-fixed-cols');
                 else
                me.removeState('bgg-state-show-fixed-cols');

            me._handleRowHandleResize();

            return me;
        }

        this.setBorderVisible = function(val)
        {
            if (val === true)
                me.addState('bgg-state-show-border');
                 else
                me.removeState('bgg-state-show-border');

            return me;
        }

        this.setUtilityBarVisible = function(val)
        {
            if (val === true)
                me.addState('bgg-state-show-utility-bar');
                 else
                me.removeState('bgg-state-show-utility-bar');

            return me;
        }

        this.setLockScrollToRowHeight = function(val)
        {
            grid_lock_scroll_to_row_height = (val === false) ? false : true;
            return me;
        }

        this.setLiveScroll = function(val)
        {
            grid_live_scroll = (val === false) ? false : true;
            return me;
        }

        // -- message/feedback methods --

        // positions: 'top', 'bottom', 'overlay', 'overlay-fixed'
        this.showMessage = function(message, position)
        {
            // remove the previous message if it existed
            me.hideMessage();

            if (position === undefined)
                position = 'bottom';

            // make sure nothing is selected when showing top message
            if (position === 'top')
            {
                me.clearCellSelection();
                me.clearColumnSelection();
                me.clearRowSelection();
            }

            if (position === 'overlay' || position === 'overlay-fixed' || position == 'overlay-center')
            {
                $grid_message = $.tmpl(opts.messageOverlayTemplate, { msg: message }).appendTo($el);

                var h = $grid_message.height().toFixed(),
                    tm = (-1 * (h/2)).toFixed();

                $grid_message.filter('.bgg-message-overlay').css('margin-top', tm+'px');

                $grid_message.css('visibility', 'visible');

                if (position === 'overlay-fixed')
                    $grid_message.addClass('bgg-message-overlay-fixed');

                if (position === 'overlay-center')
                    $grid_message.addClass('bgg-message-overlay-center');
            }
             else if (position === 'top')
            {
                // default to showing the message over the grid rows
                $grid_message = $.tmpl(opts.messageTemplate, { msg: message }).appendTo($grid_body_wrapper);
            }
             else
            {
                // default to showing the loading in the bottom-left corner
                $grid_message = $.tmpl(opts.messageBottomTemplate, { msg: message }).appendTo($el);
            }

            return me;
        }

        this.hideMessage = function()
        {
            $grid_message.remove();
            $grid_message = $();
            return me;
        }

        this.startHeaderCellEdit = function(col_id)
        {
            if (!me.isColumnRenameAllowed())
                return me;

            var $th = $grid_data_header.find('.'+col_id);

            if ($th.length == 0)
                $th = $grid_fixed_header.find('.'+col_id);

            // couldn't find header cell; bail out
            if ($th.length == 0)
                return me;

            me.addState('bgg-state-col-renaming');
            $th.addClass('bgg-cell-editing');

            var $cell_text = $th.find('.bgg-cell-text'),
                old_text = $cell_text.text(),
                $input = $('<input type="text" class="form-control" id="'+ns+'-cell-input" value="'+old_text+'" data-old-value="'+old_text+'" />');

            $input.appendTo($cell_text.empty())
                .on('mousedown', function(evt) {
                    // remove text selection
                    this.selectionStart = null;
                    this.selectionEnd = null;

                    evt.stopPropagation();
                })
                .on('dblclick', function(evt) {
                    var val = $(this).val();

                    // select all text in the input
                    this.selectionStart = 0;
                    this.selectionEnd = val.length;

                    evt.stopPropagation();
                })
                .on('keydown', function(evt) {
                    if (evt.which == 27) // escape
                    {
                        me.endHeaderCellEdit(false);
                    }
                     else if (evt.which == 13) // enter
                    {
                        me.endHeaderCellEdit(true);
                    }
                     else if (evt.which == 9) // tab
                    {
                        // save and end the current edit
                        me.endHeaderCellEdit(true);

                        // move to the previous or next header cell
                        var $next_th;
                        if (evt.shiftKey)
                            $next_th = $th.prev('.'+cls_th);
                             else
                            $next_th = $th.next('.'+cls_th);

                        // start editing that cell
                        if ($next_th.length > 0)
                        {
                            var next_col_id = $next_th.data('col-id');
                            me.startHeaderCellEdit(next_col_id);
                        }
                    }

                    evt.stopPropagation();
                });

            $input.focusSelectAll();

            return me;
        }

        // delay this call slightly so we can process onDocumentMouseDown()
        // and onDocumentMouseUp() first -- this fixes an issue that occurs
        // if the header cell edit is started via a double click
        this.startHeaderCellEdit = _.debounce(this.startHeaderCellEdit, 10);

        this.endHeaderCellEdit = function(accept)
        {
            var $input = $('#'+ns+'-cell-input');

            // couldn't find input; bail out
            if ($input.length == 0)
                return me;

            var $th = $input.closest('.'+cls_th),
                $cell_text = $th.find('.bgg-cell-text'),
                col_id = $th.data('col-id'),
                new_val = $input.val().trim(),
                old_val = $input.data('old-value');

            if (accept === true && new_val != old_val && new_val.length > 0)
            {
                // try the rename and update cell text accordingly
                if (me.renameColumn(col_id, new_val) === true)
                    $cell_text.text(new_val);
                     else
                    $cell_text.text(old_val);
            }
             else
            {
                // revert cell text to old value
                $cell_text.text(old_val);
            }

            $th.removeClass('bgg-cell-editing');
            me.removeState('bgg-state-col-renaming');

            return me;
        }

        this.startColumnInsert = function(col_id)
        {
            // only one column can be inserted at a time
            if (me.isInsertingColumn())
                return '';

            // create column info object
            var col = new BiggridColumnInfo();

            // get the position of the specified column
            // (this will be our insert position)
            var col_idx = me.getColumnPosition(col_id);

            // readable cell id and class
            var insert_col_id = opts.colClsPrefix+'insert-'+me._getRandomUid();

            // make sure the random 'col-id' does not alread exist
            while (grid_column_info[insert_col_id] !== undefined)
            {
                insert_col_id = opts.colClsPrefix+'insert-'+me._getRandomUid();
            }

            // add the column to the grid column info
            grid_column_info[insert_col_id] = col;

            // insert the column at the appropriate position
            grid_column_order.splice(col_idx+1, 0, insert_col_id);

            // store insert column for use elsewhere
            grid_insert_col_id = insert_col_id;

            // make sure we re-render the faux grid next time we refresh rows
            me._invalidateFauxRows();

            return insert_col_id;
        }

        this.endColumnInsert = function(accept)
        {
            if (accept === false)
            {
                delete grid_column_info[grid_insert_col_id];
                grid_column_order = _.without(grid_column_order, grid_insert_col_id);

                // make sure we re-render the faux grid next time we refresh rows
                me._invalidateFauxRows();
            }

            grid_insert_col_id = undefined;
            return me;
        }

        this.getInsertColumn = function()
        {
            if (!me.isInsertingColumn())
                return undefined;

            return grid_insert_col_id;
        }

        this.isInsertingColumn = function()
        {
            return (grid_insert_col_id !== undefined) ? true : false;
        }

        // -- refresh/render methods --

        this.refreshColumnHeader = function()
        {
            var e = undefined;

            // fire the 'beforerefreshcolumnheader.biggrid' event
            $el.trigger(e = $.Event('beforerefreshcolumnheader.biggrid'));

            if (e.result === false)
                return me;

            me._renderHeader();

            // fire the 'refreshcolumnheader.biggrid' event
            $el.trigger($.Event('refreshcolumnheader.biggrid'));

            return me;
        }

        this.refreshRows = function(rows, callback)
        {
            var e = undefined;

            // fire the 'beforerefreshrows.biggrid' event
            $el.trigger(e = $.Event('beforerefreshrows.biggrid'));

            if (e.result === false)
                return me;

            // render the markup
            me._renderRows(rows);

            if (typeof callback == 'function')
                callback({ rows: rows });

            // fire the 'refreshrows.biggrid' event
            $el.trigger($.Event('refreshrows.biggrid'), { rows: rows });

            return me;
        }

        this.refreshAll = function(callback)
        {
            var e = undefined;

            // fire the 'beforerefreshall.biggrid' event
            $el.trigger(e = $.Event('beforerefreshall.biggrid'));

            if (e.result === false)
                return me;

            me._renderHeader();
            me._renderRows();
            me._updateFixedColumnWidth();

            if (typeof callback == 'function')
                callback();

            // fire the 'refreshall.biggrid' event
            $el.trigger($.Event('refreshall.biggrid'));

            return me;
        }

        this.resizeColumn = function(col_id, new_width)
        {
            if (new_width === undefined)
                return me;

            if (!$grid_resize_col.first().hasClass(col_id))
                $grid_resize_col = $el.find('.'+cls_th+'.'+col_id);

            me._resizeColumn(new_width, function() {
                $grid_resize_col = $();
            });

            return me;
        }

        this.autoResizeColumn = function(col_id)
        {
            var $grid_resize_col_data_cells = $(),
                new_width = 0;

            $grid_resize_col = $el.find('.'+cls_th+'.'+col_id);

            // force column header cells to 'auto' width since this updates
            // the "style=" which supercedes any CSS style that is specified
            $grid_resize_col.css({
                'min-width': grid_column_min_width+'px',
                'max-width': grid_column_max_width+'px',
                'width': 'auto'
            });

            // remove class which constrains min-width and max-width
            $grid_resize_col_data_cells = $grid_data_table_tbody.find('.'+col_id);
            $grid_resize_col_data_cells.removeClass(opts.cellCls);

            // get the max. width of the header cells from each grid (visible header, data and faux)
            $grid_resize_col.each(function() {
                // TODO: maybe we shouldn't hard-code this value
                // allow a bit of breathing room for the cell content
                new_width = Math.max(new_width+10, this.clientWidth);
            });

            $grid_resize_col_data_cells.each(function() {
                new_width = Math.max(new_width, this.clientWidth);
            });

            // make sure our new width is no less than the min width
            // and no greater than the max width
            new_width = Math.max(new_width, grid_column_min_width);
            new_width = Math.min(new_width, grid_column_max_width);

            // add back class which constrains min-width and max-width
            $grid_resize_col_data_cells.addClass(opts.cellCls);

            // resize the column (in pixels) using the pixel
            // width we measured after setting "width: auto"
            me._resizeColumn(new_width, function() {
                // reset the cursor
                me._updateCursor('');
                $grid_resize_col = $();
                me.removeState('bgg-state-col-resizing');
            });

            return me;
        }

// -- private methods --

        this._render = function()
        {
            $el.addClass('biggrid bgg')
               .addClass(ns)
               .addClass(opts.theme)
               .empty()
               .append(opts.template);

            $grid_overlay_cell_selection = $el.find('.bgg-overlay-wrapper-cell-selection');
            $grid_overlay_fixed_col_selection = $el.find('.bgg-overlay-wrapper-fixed-column-selection');
            $grid_overlay_col_selection = $el.find('.bgg-overlay-wrapper-column-selection');
            $grid_overlay_row_selection = $el.find('.bgg-overlay-wrapper-row-selection');

            $grid_body_wrapper = $el.find('.bgg-body-wrapper');
            $grid_body = $grid_body_wrapper.find('.bgg-body');

            $grid_fixed_header = $el.find('.bgg-fixed-header');
            $grid_fixed_header_thead = $grid_fixed_header.find('.'+cls_thead);
            $grid_fixed_columns = $grid_body_wrapper.find('.bgg-fixed-columns');
            $grid_fixed_table = $grid_fixed_columns.find('.bgg-table-grid-fixed');
            $grid_fixed_table_thead = $grid_fixed_table.find('.'+cls_thead);
            $grid_fixed_table_tbody = $grid_fixed_table.find('.'+cls_tbody);

            $grid_data_header = $el.find('.bgg-data-header');
            $grid_data_header_thead = $grid_data_header.find('.'+cls_thead);
            $grid_data_table = $grid_body.find('.bgg-table-grid-data');
            $grid_data_table_thead = $grid_data_table.find('.'+cls_thead);
            $grid_data_table_tbody = $grid_data_table.find('.'+cls_tbody);

            $grid_faux_table = $grid_body.find('.bgg-table-grid-faux');
            $grid_faux_table_thead = $grid_faux_table.find('.'+cls_thead);
            $grid_faux_table_tbody = $grid_faux_table.find('.'+cls_tbody);

            $grid_yardstick = $el.find('.bgg-yardstick');

            return me;
        }

        this._renderHeader = function()
        {
            var fixed_thead_row = '',
                thead_row = '',
                thead_cell = '',
                col_style = '',
                col_idx = 0,
                col_width = 0,
                col_id = '',
                frozen_cls = '',
                calculated = false;

            fixed_thead_row += '<tr class="'+cls_tr+'">';
            thead_row += '<tr class="'+cls_tr+'">';

            // readable cell id and class
            col_id = opts.rowHandleCls;

            // add row number column to fixed header row
            thead_cell = '<th class="'+cls_th+' '+col_id+'" data-col-id="'+col_id+'">&nbsp;</th>';
            fixed_thead_row += thead_cell;

            var grid_frozen_column_visual_order = [];
            grid_column_visual_order = [];

            _.each(grid_column_order, function(col_id) {
                var col = grid_column_info[col_id];

                if (col === undefined)
                    return false;

                if (col.dropped === true)
                    return false;

                if (col.hidden === true)
                    return false;

                calculated = col.expression.length > 0 ? true : false;

                // frozen cell class
                frozen_cls = col.frozen === true ? opts.colFrozenCls : '';

                // readable column width
                col_width = col.pixel_width;

                // header cells are centered by default
                var text_align_cls = '';

                // override default text alignment (if specified)
                if (col.header_text_alignment == 'left' ||
                    col.header_text_alignment == 'center' ||
                    col.header_text_alignment == 'right')
                {
                    text_align_cls = 'bgg-col-text-'+col.header_text_alignment;
                }

                // cell markup
                thead_cell = '' +
                    '<th class="'+cls_th+' '+col_id+' '+text_align_cls+' '+frozen_cls+'" ' +
                        'data-col-id="'+col_id+'" ' +
                        'data-name="'+col.name+'" ' +
                        'data-type="'+col.type+'" ' +
                        'data-width="'+col.width+'" ' +
                        'data-scale="'+col.scale+'" ' +
                        'style="' +
                            'min-width: '+col_width+'px; ' +
                            'max-width: '+col_width+'px">' +
                        '<div class="bgg-cell-inner clearfix">' +
                            '<div class="'+opts.colIconCls+'">' +
                                (col.show_default_header_icons === true && col.frozen === true ? '<span class="fa fa-lock"></span>' : '') +
                                (col.show_default_header_icons === true && calculated === true ? '<span class="fa fa-flash"></span>' : '') +
                                (col.header_icon.length > 0 ? '<span class="'+col.header_icon+'"></span>' : '') +
                            '</div>' +
                            '<div class="bgg-cell-text">' +
                                col.display_name +
                            '</div>' +
                            '<div class="'+opts.colResizerCls+'"></div>' +
                            '<div class="'+opts.colReorderDropTargetCls+'"></div>' +
                            '<div class="'+opts.colHighlightCls+'"></div>' +
                        '</div>' +
                    '</th>';

                // add header cell to fixed header or data header
                if (col.frozen === true)
                    fixed_thead_row += thead_cell;
                     else
                    thead_row += thead_cell;

                // left-align all cells by default
                var text_align = 'left';

                // center dates
                if (col.type == biggrid.TYPE_DATE)
                    text_align = 'center';

                // right-align numbers
                if (col.type == biggrid.TYPE_NUMERIC)
                    text_align = 'right';

                // add cell style
                col_style += '' +
                    '.'+ns+' td.'+col_id+' { ' +
                        'text-align: '+text_align+'; ' +
                        'color: '+col.fg_color+'; ' +
                        'background-color: '+col.bg_color+'; ' +
                        'font-family: '+col.font+'; ' +
                    '}\n';

                // append this column to the visual order array
                if (col.frozen === true)
                    grid_frozen_column_visual_order.push(col_id);
                     else
                    grid_column_visual_order.push(col_id);

                col_idx++;
            });

            // prepend frozen columns to visual order
            grid_column_visual_order = _.union(grid_frozen_column_visual_order, grid_column_visual_order);

            if (opts.showInvalidColumn === true)
            {
                // readable cell id and class
                col_id = opts.colInvalidCls;

                // add invalid cell
                thead_cell = '' +
                    '<th class="'+cls_th+' '+col_id+'" data-col-id="'+col_id+'">' +
                        '<div class="bgg-cell-inner clearfix">' +
                            '<div class="bgg-cell-text">&nbsp;</div>' +
                            '<div class="'+opts.colReorderDropTargetCls+'"></div>' +
                        '</div>' +
                    '</th>';
                thead_row += thead_cell;
            }

            thead_row += '</tr>';

            // end fixed header row
            fixed_thead_row += '</tr>';

            // add invalid cell style
            col_style += '' +
                '.'+ns+' .'+cls_th+'.'+col_id+' { ' +
                    'min-width: '+opts.colInvalidWidth+'px; ' +
                    'width: 100%; ' +
                '}\n';

            // add column resizer style
            col_style += '' +
                '.'+ns+' .'+opts.colResizerCls+' { ' +
                    'margin-right: -'+opts.colResizeThreshold+'px; ' +
                    'width: '+(opts.colResizeThreshold*2)+'px; ' +
                '}\n';

            me._updateStyle('column-base', col_style);

            // remove old headers cells and append new ones to the DOM
            $grid_data_header_thead[0].innerHTML = '';
            $grid_data_header_thead.append(thead_row);
            $grid_data_table_thead[0].innerHTML = '';
            $grid_data_table_thead.append(thead_row);
            $grid_faux_table_thead[0].innerHTML = '';
            $grid_faux_table_thead.append(thead_row);

            // append fixed header row to the DOM
            $grid_fixed_header_thead[0].innerHTML = '';
            $grid_fixed_header_thead.append(fixed_thead_row);
            $grid_fixed_table_thead[0].innerHTML = '';
            $grid_fixed_table_thead.append(fixed_thead_row);

            // fire the 'renderheader.biggrid' event
            $el.trigger($.Event('renderheader.biggrid'));

            grid_needs_header_render = false;

            return me;
        }

        this._renderRows = function(rows)
        {
            var row_num = 0,
                row_idx = 0,
                col_idx = 0,
                col_id = '',
                col_keys = [],
                row_id = '',
                cell = '',
                cell_id = '',
                cell_cls = opts.cellCls,
                frozen_cls = '',
                data_rows = '',
                fixed_rows = '',
                faux_rows = '',
                val = '',
                content_type = '',
                use_index_as_keys = false;

            if (grid_model.getKeyType() == 'index-based')
                use_index_as_keys = true;

            // if a new set of rows is not passed in, just refresh the existing grid rows
            if (rows === undefined)
                rows = grid_rows;

            _.each(rows, function(row) {
                row_num = grid_row_start+row_idx;
                col_idx = 0;

                // readable cell id and class
                row_id = opts.rowClsPrefix + row_num;
                col_id = opts.rowHandleCls;
                cell_id = row_id+col_id;

                // start the row
                data_rows += '<tr class="'+cls_tr+' '+row_id+'" data-row-id="'+row_id+'" data-row-idx="'+row_num+'" data-row-data-idx="'+row_idx+'">';
                fixed_rows += '<tr class="'+cls_tr+' '+row_id+'" data-row-id="'+row_id+'" data-row-idx="'+row_num+'" data-row-data-idx="'+row_idx+'">';
                if (grid_needs_faux_rows_rendered === true && row_idx < grid_faux_row_count)
                    faux_rows += '<tr class="'+cls_tr+'">';

                // add row number cell
                cell = '' +
                    '<td id="'+cell_id+'" class="'+cell_cls+' '+row_id+' '+col_id+' '+'" data-row-id="'+row_id+'" data-col-id="'+col_id+'" data-cell-id="'+cell_id+'">' +
                        (row_num+1) +
                        //'<div class="bgg-cell-inner clearfix">' +
                        //    '<div class="bgg-cell-text">'+(row_num+1)+'</div>' +
                        //    '<div class="'+opts.rowHighlightCls+'"></div>' +
                        //'</div>' +
                    '</td>';
                fixed_rows += cell;

                _.each(grid_column_order, function(col_id) {
                    var col = grid_column_info[col_id],
                        cell_style = '';

                    if (col === undefined)
                        return false;

                    if (col.dropped === true)
                        return false;

                    if (col.hidden === true)
                        return false;

                    // frozen cell class
                    frozen_cls = col.frozen === true ? opts.colFrozenCls : '';

                    // cells are left-justified by default
                    var text_align_cls = '';

                    // override default text alignment (if specified)
                    if (col.text_alignment == 'left' ||
                        col.text_alignment == 'center' ||
                        col.text_alignment == 'right')
                    {
                        text_align_cls = 'bgg-col-text-'+col.text_alignment;
                    }

                    if (use_index_as_keys === true)
                    {
                        // index-based models need to use column key index
                        if (col_keys.length == 0)
                            col_keys = _.keys(grid_column_info);

                        var col_key_idx = _.indexOf(col_keys, col_id);
                        val = row[col_key_idx];
                    }
                     else
                    {
                        // key-based models
                        val = row[col.name];
                    }

                    if (val === undefined)
                        val = '';

                    var cell_renderer = undefined,
                        cell_style ='',
                        cell_content = '',
                        content_type = 'text';

                    // if this column has a custom cell renderer, use it
                    if (col.cell_renderer !== undefined && col.cell_renderer.length > 0)
                        cell_renderer = grid_cell_renderers[col.cell_renderer];

                    // if we still don't have a cell renderer, use the default cell renderer
                    if (cell_renderer === undefined)
                        cell_renderer = grid_default_cell_renderer;

                    // the cell renderer will return to us everything we need to render the cell
                    cell_renderer.render(val, col);
                    cell_style = cell_renderer.getCellStyle();
                    cell_content = cell_renderer.getCellContent();
                    content_type = cell_renderer.getContentType();

                    // make sure we use HTML entities in our cell content if we're just rendering text
                    if (content_type == 'text')
                        cell_content = me._escapeHtml(cell_content);

                    // make sure we do this *AFTER* we escape HTML above as we don't want this HTML to appear escaped
                    if (opts.showWhitespace === true)
                        cell_content = me._renderWhitespace(cell_content);

                    if (cell_renderer.isWrapped())
                        cell_content = '<div class="'+opts.cellWrapperCls+'">'+cell_content+'</div>';

                    // build up the cell markup
                    cell_id = row_id+col_id;
                    cell = '<td id="'+cell_id+'" class="'+cell_cls+' '+row_id+' '+col_id+' '+text_align_cls+' '+frozen_cls+'" data-row-id="'+row_id+'" data-col-id="'+col_id+'" data-cell-id="'+cell_id+'" style="'+cell_style+'">'+cell_content+'</td>';

                    if (col.frozen === true)
                    {
                        // add frozen data cell
                        fixed_rows += cell;
                    }
                     else
                    {
                        // add data cell
                        data_rows += cell;

                        // add faux cell
                        if (grid_needs_faux_rows_rendered === true && row_idx < grid_faux_row_count)
                            faux_rows += '<td class="'+cell_cls+' '+row_id+' '+col_id+' '+text_align_cls+' '+frozen_cls+'" data-row-id="'+row_id+'" data-col-id="'+col_id+'">&nbsp;</td>';
                    }

                    col_idx++;
                });

                if (opts.showInvalidColumn === true)
                {
                    // readable cell id and class
                    col_id = opts.colInvalidCls;

                    // add invalid cells
                    cell = '<td class="'+cell_cls+' '+row_id+' '+col_id+'" data-col-id="'+col_id+'" data-row-id="'+row_id+'">&nbsp;</td>';
                    data_rows += cell;
                    if (grid_needs_faux_rows_rendered === true && row_idx < grid_faux_row_count)
                        faux_rows += cell;
                }

                // end the row
                data_rows += '</tr>';
                fixed_rows += '</tr>';
                if (grid_needs_faux_rows_rendered === true && row_idx < grid_faux_row_count)
                    faux_rows += '</tr>';

                row_idx++;
            });

            // remove old rows and append rows to the DOM
            $grid_data_table_tbody[0].innerHTML = '';
            $grid_data_table_tbody.append(data_rows);

            $grid_fixed_table_tbody[0].innerHTML = '';
            $grid_fixed_table_tbody.append(fixed_rows);

            if (grid_needs_faux_rows_rendered === true)
            {
                $grid_faux_table_tbody[0].innerHTML = '';
                $grid_faux_table_tbody.append(faux_rows);
            }

            // store member variables
            grid_rendered_row_start = grid_row_start;
            grid_rendered_row_end = grid_row_start+row_idx;

            // update our discovered row count
            grid_discovered_row_count = Math.max(grid_discovered_row_count, grid_rendered_row_end);

            // faux rows have now been rendered
            grid_needs_faux_rows_rendered = false;

            // set the scroll position to the top-left of the grid on first load since some
            // browsers remember the scroll position, which, if at the bottom of the grid,
            // would cause the grid to continually load more rows
            if (grid_needs_full_render === true)
            {
                // calling just .scrollTop(0) here causes the vertical
                // scrollbar handle to become "stuck" on some browsers
                $grid_body.scrollTop(1);
                $grid_body.scrollTop(0);
                $grid_body.scrollLeft(1);
                $grid_body.scrollLeft(0);

                // resize the grid body and fixed columns to get things going
                me._updateGridBodyHeight();
                me._invalidateFixedColumnWidth();
                me._updateRowHandleWidth();
                me._updateFixedColumnWidth();
                me._updateFixedColumnBottom();

                // auto resize all columns
                if (grid_needs_auto_column_resize === true)
                {
                    _.each(me.getColumns(), function(col_id) {
                        me.autoResizeColumn(col_id);
                    });
                }

                // update the scrollbars
                if (grid_custom_scrollbars === true)
                    $grid_body.data('biggridscrollbars').update();
            }

            // if our row count is indeterminate, grow the grid body
            if (grid_row_count === undefined)
            {
                // if we loaded less rows than we requested, we're at
                // the end of the file and we now know the total row count
                if (row_idx < grid_row_limit)
                {
                    grid_row_count = grid_discovered_row_count;
                    grid_discovered_eof = true;
                }

                me._updateGridBodyHeight();
                me._updateFixedColumnBottom();
            }

            grid_needs_auto_column_resize = false;
            grid_needs_full_render = false;

            // fire the 'renderrows.biggrid' event
            $el.trigger($.Event('renderrows.biggrid'), { rows: rows });

            return me;
        }

        // compliments of Mustache.js (https://github.com/janl/mustache.js/blob/master/mustache.js#L52)
        this._escapeHtml = function(text)
        {
            return String(text).replace(/[&<>"'\/]/g, function(s) {
              return html_entity_map[s];
            });
        }

        this._renderWhitespace = function(text)
        {
            return String(text).replace(/[\t ]/g, function(s) {
              return whitespace_map[s];
            });
        }

        this._resizeColumn = function(new_width, callback)
        {
            var $col = $grid_resize_col.first(),
                col_id = $col.data('col-id');

            // if no new width was specified, use the distance between
            // the cell's left border and the mouse position
            if (new_width === undefined)
            {
                // without a column, the offset call below will bomb out
                if ($col.length == 0)
                    return me;

                var off = $col.offset(),
                    left = Math.floor(off.left);

                new_width = mouse_x - left;
            }

            if (new_width < grid_column_min_width)
                new_width = grid_column_min_width;

            if (new_width > grid_column_max_width)
                new_width = grid_column_max_width;

            $grid_resize_col.css({
                'min-width': new_width+'px',
                'max-width': new_width+'px'
            });

            // update the column's info
            me.setColumnInfo(col_id, 'pixel_width', new_width);

            if (me.isColumnFrozen(col_id))
            {
                me._invalidateFixedColumnWidth();
                me._updateFixedColumnWidth();
            }

            // make sure our cell selection is resized based on the new column width
            grid_cell_selection.calcDimensions();
            grid_cell_selection.refresh();

            // make sure our fixed column selection is resized based on the new column width
            grid_fixed_col_selection.calcDimensions();
            grid_fixed_col_selection.refresh();

            // make sure our column selection is resized based on the new column width
            grid_col_selection.calcDimensions();
            grid_col_selection.refresh();

            // since we may be throttling this function, it is important to provide a callback function
            if (typeof callback == 'function')
                callback();

            return me;
        }

        // column resize throttle
        if (opts.columnResizeThrottle > 0)
            this._resizeColumnThrottled = _.throttle(this._resizeColumn, opts.columnResizeThrottle);

        this._updateFixedColumnBottom = function()
        {
            var s = me._getScrollbarSize(true),
                bottom_offset = s.height;

            // since the fixed body container is outside the scrollable body,
            // we need to compensate for the horizontal scrollbar height here
            $grid_fixed_columns.css('bottom', bottom_offset+'px');
        }

        this._invalidateFixedColumnWidth = function()
        {
            grid_needs_fixed_column_width_recalc = true;
        }

        this._updateFixedColumnWidth = function()
        {
            if (grid_needs_fixed_column_width_recalc !== true)
                return me;

            if ($grid_fixed_columns.is(':visible'))
            {
                $grid_fixed_header.css({ 'width': 'auto' });
                $grid_fixed_columns.css({ 'width': 'auto' });

                var w1 = $grid_fixed_header.find('table').outerWidth(true),
                    w2 = $grid_fixed_columns.find('table').outerWidth(true);

                grid_fixed_columns_width = Math.max(w1,w2);
            }
             else
            {
                grid_fixed_columns_width = 0;
            }

            $grid_fixed_header.css({ 'width': grid_fixed_columns_width+'px' });
            $grid_fixed_columns.css({ 'width': grid_fixed_columns_width+'px' });

            $grid_data_header.css({ 'padding-left': grid_fixed_columns_width+'px' });
            $grid_body.css({ 'padding-left': grid_fixed_columns_width+'px' });

            grid_needs_fixed_column_width_recalc = false;

            me._updateOverlayClipRect();

            return me;
        }

        // column resize scroll throttle
        if (opts.updateFixedColumnWidthThrottle > 0)
            this._updateFixedColumnWidth = _.throttle(this._updateFixedColumnWidth, opts.updateFixedColumnWidthThrottle);

        this._updateRowHandleWidth = function()
        {
            if (!me.hasState('bgg-state-show-fixed-cols'))
                grid_row_handle_width = 0;
                 else if (!me.hasState('bgg-state-show-bgg-tr-handles'))
                grid_row_handle_width = 0;
                 else if (me.hasState('bgg-state-show-row-numbers'))
                grid_row_handle_width = opts.rowHandleWithNumbersWidth;
                 else
                grid_row_handle_width = opts.rowHandleWidth;

            // add cell style
            var row_handle_style = '' +
                '.'+ns+' .'+cls_td+'.'+opts.rowHandleCls + ', ' +
                '.'+ns+' .'+cls_th+'.'+opts.rowHandleCls + ' { ' +
                    'min-width: ' + grid_row_handle_width + 'px; ' +
                    'max-width: ' + grid_row_handle_width + 'px; ' +
                    'width: ' + grid_row_handle_width + 'px; ' +
                '}\n';

            me._updateStyle('bgg-tr-handle', row_handle_style);
        }

        // helper function for use when showing/hiding row handles,
        // row numbers in handles or fixed columns altogether
        this._handleRowHandleResize = function()
        {
            var delta = grid_row_handle_width;

            me._updateRowHandleWidth();
            me._invalidateFixedColumnWidth();
            me._updateFixedColumnWidth();

            delta = grid_row_handle_width - delta;

            // sync cell selection
            if (grid_cell_selection !== undefined)
            {
                grid_cell_selection.setProperty('left', delta, '+=');
                grid_cell_selection.refresh();
            }

            // sync fixed column selection
            if (grid_fixed_col_selection !== undefined)
            {
                grid_fixed_col_selection.setProperty('left', delta, '+=');
                grid_fixed_col_selection.refresh();
            }

            // sync column selection
            if (grid_col_selection !== undefined)
            {
                grid_col_selection.setProperty('left', delta, '+=');
                grid_col_selection.refresh();
            }

            // sync row selection
            if (grid_row_selection !== undefined)
            {
                grid_row_selection.setProperty('left', delta, '+=');
                grid_row_selection.refresh();
            }
        }

        this._updateOverlayClipRect = function()
        {
            // we haven't done our initial render yet; bail out
            if (grid_header_height === undefined)
                return;

            // clip selection areas properly
            var top = grid_header_height - 3,
                right = el.clientWidth - scrollbar_size.width,
                bottom = el.clientHeight - scrollbar_size.height,
                left = grid_fixed_columns_width - 2;

            $grid_overlay_cell_selection.css('clip', 'rect('+top+'px, '+right+'px, '+bottom+'px, '+left+'px)');
            $grid_overlay_col_selection.css('clip', 'rect('+top+'px, '+right+'px, '+bottom+'px, '+left+'px)');

            left = grid_row_handle_width - 2;
            $grid_overlay_row_selection.css('clip', 'rect('+top+'px, '+right+'px, '+bottom+'px, '+left+'px)');

            right = grid_fixed_columns_width + 2;
            $grid_overlay_fixed_col_selection.css('clip', 'rect('+top+'px, '+right+'px, '+bottom+'px, '+left+'px)');
        }

        this._updateRowCountText = function()
        {
            var str = '';

            // show visible row text and total row count text (if we know it)
            if (grid_row_count === 0)
            {
                str = '';
            }
             else if (grid_row_count === undefined)
            {
                var end = grid_visible_row_end,
                    start = Math.min(grid_visible_row_start+1, end);

                end = Util.formatNumber(end, '0,000');
                start = Util.formatNumber(start, '0,000');

                str = Util.sprintf(_T('Showing rows %p1 - %p2'), start, end);
            }
             else
            {
                var end = Math.min(grid_visible_row_end, grid_row_count),
                    start = Math.min(grid_visible_row_start+1, end),
                    total = grid_row_count;

                end = Util.formatNumber(end, '0,000'),
                start = Util.formatNumber(start, '0,000'),
                total = Util.formatNumber(total, '0,000');

                str = Util.sprintf(_T('Showing rows %p1 - %p2 of %p3'), start, end, total);
            }

            grid_row_count_text = str;

            // fire the 'rowcounttextupdate.biggrid' event
            $el.trigger($.Event('rowcounttextupdate.biggrid'), grid_row_count_text);

            return me;
        }

        this._updateGridBodyHeight = function()
        {
            if (grid_row_count === undefined)
            {
                // unknown row count; size grid body based on the height of discovered rows
                var h = (grid_discovered_row_count*grid_row_height);
                $grid_yardstick.css('height', h+'px');
            }
             else
            {
                // fixed row count; size grid body to fit the specified number of rows
                var h = (grid_row_count*grid_row_height);

                if (opts.showInvalidRow === true)
                    h += opts.rowInvalidHeight;

                $grid_yardstick.css('height', h+'px');
            }

            if (opts.height == 'fill')
                return me;

            if (opts.height == 'auto')
            {
                var header_height = $grid_data_header.outerHeight();

                var s = me._getScrollbarSize(true),
                    scrollbar_height = s.height;

                $el.height(h+header_height+scrollbar_height);
            }
             else if (typeof opts.height == 'number')
            {
                $el.outerHeight(opts.height);
            }

            return me;
        }

        this._calcAutoRowHeight = function()
        {
            var $tr = $('<tr class="'+cls_tr+'"><td class="'+cls_td+'">&nbsp;</td></tr>').appendTo($grid_data_table_tbody),
                $td = $tr.find('.'+cls_td);

            grid_auto_row_height = $td.outerHeight(true);
            grid_row_height = grid_auto_row_height;
            $tr.remove();

            return me;
        }

        this._calcAutoRowLimit = function()
        {
            grid_auto_row_limit = Math.ceil(screen_height/grid_row_height);

            // make sure our auto row limit is a nice round number
            grid_auto_row_limit += (10 - grid_auto_row_limit % 10);

            return me;
        }

        this._calcFauxRowCount = function()
        {
            // avoid division by zero
            if (grid_row_height == 0)
                return me;

            if (grid_model.isFastGetRows())
            {
                // fast grid models only need as many faux rows as are visible to the user
                grid_faux_row_count = grid_visible_row_count;
            }
             else
            {
                // make sure we have enough rows to allow for super-fast scrolling
                // and still never see the bottom or top of the faux grid
                grid_faux_row_count = Math.ceil(screen_height/grid_row_height);
            }

            return me;
        }

        this._invalidateFauxRows = function()
        {
            grid_needs_faux_rows_rendered = true;
            return me;
        }

        this._getScrollbarSize = function(hard)
        {
            if (hard === true || scrollbar_size === undefined)
            {
                var scrollbars = $grid_body.data('biggridscrollbars');
                if (scrollbars !== undefined)
                    scrollbar_size = scrollbars.getScrollbarSize();
                     else
                    scrollbar_size = $.getScrollbarSize();

                if ($grid_body.length > 0)
                {
                    var grid_body = $grid_body[0],
                        cli_w = grid_body.clientWidth,
                        cli_h = grid_body.clientHeight,
                        scroll_w = grid_body.scrollWidth,
                        scroll_h = grid_body.scrollHeight;

                    // if no horizontal scrollbar exists, the height is zero
                    if (scroll_w == cli_w)
                        scrollbar_size.height = 0;

                    // if no vertical scrollbar exists, the width is zero
                    if (scroll_h == cli_h)
                        scrollbar_size.width = 0;
                }
            }

            return scrollbar_size;
        }

        this._getRandomUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        this._updateCursor = function(cursor)
        {
            if (grid_cursor === cursor)
                return;

            // remove cursor style
            if (cursor === '')
            {
                me._updateStyle('cursor', '');
                grid_cursor = '';
                return;
            }

            // add cursor style
            var cursor_style = '' +
                '.'+ns+', ' +
                '.'+ns+' .'+cls_td+', ' +
                '.'+ns+' .'+cls_th+' { ' +
                    'cursor: ' + cursor + '; ' +
                '}\n';

            me._updateStyle('cursor', cursor_style);
            grid_cursor = cursor;

            return me;
        }

        this._updateStyle = function(id_suffix, style_str)
        {
            var $style = $('#'+ns+'-'+id_suffix);

            if (style_str === undefined || style_str.length == 0)
            {
                // no style specified; remove existing style
                $style.remove();
                return;
            }

            // if the style already exists, just update its contents
            if ($style.length > 0)
            {
                $style.text(style_str);
            }
             else
            {
                var style = '<style id="'+ns+'-'+id_suffix+'" type="text/css">'+style_str+'</style>';
                $(style).appendTo('head');
            }

            return me;
        }

        this._hideColumnDropdownMenu = function()
        {
            if (grid_column_dropdown_handler.isVisible())
                grid_column_dropdown_handler.hideDropdown();

            return me;
        }

        this._registerOptionsCellRenderers = function()
        {
            _.each(opts.cellRenderers, function(cell_renderer) {
                var cr_name,
                    cr_instance;

                if (typeof cell_renderer == 'string')
                {
                    cr_name = cell_renderer;

                    var cr_constructor = window.biggrid.cellrenderer[cr_name];

                    if (typeof cr_constructor != 'function')
                        return me;

                    cr_instance = new cr_constructor();
                }
                 else if (typeof cell_renderer == 'object')
                {
                    if (typeof cell_renderer.getName != 'function')
                        return me;

                    cr_name = cell_renderer.getName();
                    cr_instance = cell_renderer;
                }

                me.registerCellRenderer(cr_name, cr_instance);
                return me;
            });

            return me;
        }

        this._registerOptionsPlugins = function()
        {
            _.each(opts.plugins, function(plugin) {
                var plugin_name,
                    plugin_instance;

                if (typeof plugin == 'string')
                {
                    plugin_name = plugin;

                    var plugin_constructor = window.biggrid.plugin[plugin_name];

                    if (typeof plugin_constructor != 'function')
                        return me;

                    plugin_instance = new plugin_constructor();
                }
                 else if (typeof plugin == 'object')
                {
                    if (typeof plugin.getName != 'function')
                        return me;

                    plugin_name = plugin.getName();
                    plugin_instance = plugin;
                }

                me.registerPlugin(plugin_name, plugin_instance);
                return me;
            });
        }

        this._initState = function()
        {
            // set options before loading rows
            me.setCustomScrollbars(opts.customScrollbars === true ? true : false);
            me.setFullscreen(opts.fullscreen === true ? true : false);
            me.setShowLoading(opts.showLoading === true ? true : false);
            me.setFauxRowsVisible(opts.showFauxRows === true ? true : false);
            me.setStripedRowsVisible(opts.showStripedRows === true ? true : false);
            me.setVerticalLinesVisible(opts.showVerticalLines === true ? true : false);
            me.setHorizontalLinesVisible(opts.showHorizontalLines === true ? true : false);
            me.setWhitespaceVisible(opts.showWhitespace === true ? true : false);
            me.setRowNumbersVisible(opts.showRowNumbers === true ? true : false);
            me.setRowHandlesVisible(opts.showRowHandles === true ? true : false);
            me.setFixedColumnsVisible(opts.showFixedColumns === true ? true : false);
            me.setBorderVisible(opts.showBorder === true ? true : false);
            me.setUtilityBarVisible(opts.showUtilityBar === true ? true : false);
            me.setColumnDropdownAllowed(opts.allowColumnDropdown === true ? true : false);
            me.setColumnRenameAllowed(opts.allowColumnRename === true ? true : false);
            me.setColumnSelectionAllowed(opts.allowColumnSelection === true ? true : false);
            me.setRowSelectionAllowed(opts.allowRowSelection === true ? true : false);
            me.setCellSelectionAllowed(opts.allowCellSelection === true ? true : false);
            me.setLockScrollToRowHeight(opts.lockScrollToRowHeight === true ? true : false);
            me.setLiveScroll(opts.liveScroll === true ? true : false);

            // if this flag is set to true, we will auto resize
            // all columns in the grid the next time we render rows
            grid_needs_auto_column_resize = (opts.autoResizeColumns === true) ? true : false;

            return me;
        }

        this._initMetrics = function(callback)
        {
            if (metrics_inited === true)
                return;

            if ($el.is(':visible'))
            {
                // "show" the grid visually to the user
                $el.removeClass('bgg-invisible');

                // force first render again -- even if we've already done it
                // (since we're now just becoming visible)
                grid_needs_full_render = true;

                // calculate row height (so we can calculate the grid body height
                // when the grid has a fixed row count)
                me._calcAutoRowHeight();
                me._calcAutoRowLimit();

                // now that we have the auto (default) row height, we can do this
                me.setRowHeight(opts.rowHeight);

                me.setCellVerticalAlignment(opts.cellVerticalAlignment);

                // we must do this calculation after we have our grid model
                // (and after we have a row height)
                me._calcFauxRowCount();

                // invalidate these values to make sure they get recalcuated
                me._invalidateFauxRows();
                me._invalidateFixedColumnWidth();

                // update fixed column width for the first time
                me._updateFixedColumnWidth();

                // update local variable
                grid_header_height = $grid_data_header.outerHeight(true);

                // calculate these variables here to get things going (this helps
                // us eliminate some flicker since these variables are next
                // calculated in the resize event)
                grid_visible_row_start = 0;
                grid_visible_row_end = Math.ceil(el.clientHeight/grid_row_height);
                grid_visible_row_count = grid_visible_row_end-grid_visible_row_start;

                // TODO: fix this
                // these selection helpers must be created here since they
                // rely on the 'grid_header_height' variable

                // create our cell selection helper
                if (grid_cell_selection instanceof BiggridCellSelection)
                    grid_cell_selection.destroy();

                grid_cell_selection = new BiggridCellSelection();

                // create our fixed column selection helper
                if (grid_fixed_col_selection instanceof BiggridColumnSelection)
                    grid_fixed_col_selection.destroy();

                grid_fixed_col_selection = new BiggridColumnSelection('fixed');

                // create our column selection helper
                if (grid_col_selection instanceof BiggridColumnSelection)
                    grid_col_selection.destroy();

                grid_col_selection = new BiggridColumnSelection('data');

                // create our row selection helper
                if (grid_row_selection instanceof BiggridRowSelection)
                    grid_row_selection.destroy();

                grid_row_selection = new BiggridRowSelection();

                metrics_inited = true;

                if (typeof callback == 'function')
                    callback();

                // fire the 'init.biggrid' event
                $el.trigger($.Event('init.biggrid'));
            }
             else
            {
                // do this check often until the first time the grid is visible
                setTimeout(function() {
                    me._initMetrics(callback);
                }, 100);
            }

            return me;
        }

// -- initialize ourself --

        me.init();
    };

// -- default options --

    $.biggrid.defaults = {
        title: '',
        autoLoadRows: true,
        autoRefreshModel: true,
        // auto resize columns on first load (if a view is not specified)
        autoResizeColumns: true,
        fullscreen: false,
        showFauxRows: true,
        showStripedRows: false,
        showVerticalLines: true,
        showHorizontalLines: true,
        // TODO: make this a state?
        showWhitespace: false,
        showRowNumbers: true,
        showRowHandles: true,
        showFixedColumns: true,
        // TODO: make this a state?
        showInvalidColumn: true,
        // TODO: make this a state?
        showInvalidRow: true,
        showLoading: true,
        showBorder: false,
        showUtilityBar: false,
        allowColumnDropdown: true,
        allowColumnRename: true,
        allowColumnSelection: true,
        allowRowSelection: true,
        allowCellSelection: true,
        // if set to true, the 'contextmenu' event
        // will fire a dropdown click event
        forwardContextMenuEvent: true,
        // when set to 'true', vertical scroll offsets will be locked
        // to row offsets (first visible row * row height)
        lockScrollToRowHeight: false,
        // when set to 'true', show faux rows or white space on scroll,
        // otherwise continue to show the current data rows that have already
        // been rendered on the screen until new data rows have been loaded
        liveScroll: true,
        customScrollbars: false,
        // requires customScrollbars: true
        customScrollbarsFullscreen: false,
        // leave blank to inherit from theme CSS -- @brand-primary
        cellSelectionBorderColor: '',
        // leave blank to inherit from theme CSS -- rgba(0,0,0,0.225)
        cellSelectionBackgroundColor: '',
        // leave blank to inherit from theme CSS -- @brand-primary
        columnSelectionBorderColor: '',
        // leave blank to inherit from theme CSS -- rgba(0,0,0,0.225)
        columnSelectionBackgroundColor: '',
        // leave blank to inherit from theme CSS -- @brand-primary
        //columnHeaderSelectionAccentColor: '',
        // leave blank to inherit from theme CSS -- @brand-primary
        rowSelectionBorderColor: '',
        // leave blank to inherit from theme CSS -- rgba(0,0,0,0.225)
        rowSelectionBackgroundColor: '',
        // leave blank to inherit from theme CSS -- @brand-primary
        //rowFixedSelectionAccentColor: '',
        theme: 'bgg-theme-default',
        // leave empty to use default
        font: '',
        // 'top', 'middle', 'bottom' or 'auto' (use CSS)
        cellVerticalAlignment: 'middle',
        cellCls: cls_td,
        cellWrapperCls: cls_td+'-wrapper',
        // a number or 'auto' (use CSS)
        rowHeight: 'auto',
        // empty space below the table
        rowInvalidHeight: 50,
        rowHandleWidth: 20,
        rowHandleWithNumbersWidth: 80,
        rowHandleCls: 'bgg-tr-handle',
        rowSelectedCls: 'bgg-tr-selected',
        rowHighlightCls: 'bgg-tr-highlight',
        rowClsPrefix: cls_tr+'-',
        // only applies to index-based model columns
        colClsPrefix: cls_col+'-',
        colFrozenCls: 'bgg-col-frozen',
        colHiddenCls: 'bgg-col-hidden',
        colIconCls: 'bgg-col-icon',
        colResizerCls: 'bgg-col-resizer',
        colSelectedCls: 'bgg-col-selected',
        colHighlightCls: 'bgg-col-highlight',
        colReorderDropTargetCls: 'bgg-col-reorder-drop-target',
        colInvalidCls: cls_col+'-x',
        // empty space to the right of the table
        colInvalidWidth: 200,
        colMinWidth: 30,
        colMaxWidth: 1200,
        colDefaultWidth: 130,
        colDefaultFont: 'inherit',
        colDefaultFgColor: '#333',
        colDefaultBgColor: '#fff',
        // number of pixels adjacent to a column border to signal a column resize
        colResizeThreshold: 4,
        // while the dropdown handler is technically a plugin,
        // it is important enough to warrant its own option and variable
        colDropdownHandler: undefined,
        cellRenderers: [],
        plugins: [],
        // 'fill': used with absolute positioning
        // 'auto': used with static positioning
        // <number>: specify the overall height regardless
        height: 'fill',
        view: undefined,
        model: undefined,
        start: 0,
        // a number or 'auto' to calculate based on the screen height
        limit: 'auto',
        // when a data operation happens on more than this number of rows,
        // show the big loading text
        showBigLoadingRowCount: 100000,
        // percentage of newly rendered rows to place above
        // the viewport when limit is not set to 'auto'
        dataRowsTopOffsetPct: 0.2,
        // percentage of rendered content scrolled before loading more rows
        loadingThresholdPct: 0.8,
        // milliseconds to wait before starting an AJAX call when scrolling
        // (this value plus the vertical scroll timer should be at least 50ms)
        loadingScrollDelay: 50,
        // limit the number of times the scroll event is fired (this actually
        // speeds up the effect of scrolling greatly since we don't hammer
        // the render function)
        horzScrollThrottle: 10,
        // limit the number of times the scroll event is fired (this actually
        // speeds up the effect of scrolling greatly since we don't hammer
        // the render function)
        vertScrollThrottle: 10,
        // limit the number of times column resize can
        // be called right next to each other
        columnResizeThrottle: 15,
        // limit the number of times we recalculate
        //the fixed column width (this function is rather expensive)
        updateFixedColumnWidthThrottle: 20,
        // limit the number of times the resize event is fired (this actually
        // speeds up the effect of resizing greatly since we don't hammer
        // the render function)
        resizeThrottle: 40,
        loadingText: _T('Loading...'),
        loadingOverlayText: _T('Loading...'),
        nullText: '<null>',
        messageTemplate: '' +
            '<div class="bgg-message">{{html msg}}</div>' +
            '<div class="bgg-backdrop"></div>',
        messageOverlayTemplate: '' +
            '<div class="bgg-message-overlay">' +
                '<div class="bgg-message-inner">{{html msg}}</div>' +
            '</div>' +
            '<div class="bgg-backdrop"></div>',
        messageBottomTemplate: '' +
            '<div class="bgg-message-bottom">{{html msg}}</div>',
        template: '' +
            '<div class="bgg-overlay-wrapper bgg-overlay-wrapper-cell-selection"></div>' +
            '<div class="bgg-overlay-wrapper bgg-overlay-wrapper-fixed-column-selection"></div>' +
            '<div class="bgg-overlay-wrapper bgg-overlay-wrapper-column-selection"></div>' +
            '<div class="bgg-overlay-wrapper bgg-overlay-wrapper-row-selection"></div>' +
            '<div class="bgg-utility-bar"></div>' +
            '<div class="bgg-fixed-header">' +
                '<table class="'+cls_table+'"><thead class="'+cls_thead+'"></thead></table>' +
            '</div>' +
            '<div class="bgg-data-header">' +
                '<table class="'+cls_table+'"><thead class="'+cls_thead+'"></thead></table>' +
            '</div>' +
            '<div class="bgg-body-wrapper">' +
                '<div class="bgg-fixed-columns">' +
                    '<table class="'+cls_table+' bgg-table-grid-fixed">' +
                        '<thead class="'+cls_thead+'"></thead>' +
                        '<tbody class="'+cls_tbody+'"></tbody>' +
                    '</table>' +
                '</div>' +
                '<div class="bgg-body">' +
                    '<table class="'+cls_table+' bgg-table-grid-data">' +
                        '<thead class="'+cls_thead+'"></thead>' +
                        '<tbody class="'+cls_tbody+'"></tbody>' +
                    '</table>' +
                    '<table class="'+cls_table+' bgg-table-grid-faux">' +
                        '<thead class="'+cls_thead+'"></thead>' +
                        '<tbody class="'+cls_tbody+'"></tbody>' +
                    '</table>' +
                    '<div class="bgg-yardstick"></div>' +
                '</div>' +
            '</div>'
    };

    $.fn.biggrid = function(options) {
        return this.each(function() {
            (new $.biggrid(this, options));
        });
    };

})(jQuery, _, window, document);
