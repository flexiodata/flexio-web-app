(function(undefined) {

    var me = this,
        system = require('system'),
        fs = require('fs'),
        args = system.args,
        page = undefined,
        opts = undefined;

    // pagination variables
    var item_start = undefined,
        total_count = undefined,
        count_per_page = undefined,
        max_count_per_page = 100,
        rendered_count = 0,
        stored_items = '';

    this.run = function()
    {
        var params_file = args[1];

        try
        {
            var f = fs.open(params_file, 'r');
            var content = f.read();
            opts = JSON.parse(content);
        }
        catch (e)
        {
            console.log("could not read params file");
            phantom.exit(1);
        }

        // defaults
        if (opts.paper_size === undefined)
            opts.paper_size = 'letter';
        if (opts.orientation === undefined)
            opts.orientation = 'portrait';
        if (opts.margin_top === undefined)
            opts.margin_top = '0.5in';
        if (opts.margin_right === undefined)
            opts.margin_right = '0.5in';
        if (opts.margin_bottom === undefined)
            opts.margin_bottom = '0.5in';
        if (opts.margin_left === undefined)
            opts.margin_left = '0.5in';

        // make sure we know about the session
        if (opts.session_id !== undefined && opts.domain !== undefined)
        {
            phantom.addCookie({
                'name': 'PHPSESSID',
                'value': opts.session_id,
                'domain': opts.domain
            });
        }

        me.initPage();
        me.initPagination();
        me.render();
    }

    this.initPage = function()
    {
        page = require('webpage').create();
        me.setTaskInfo(5, 'Initializing page');

        page.onConsoleMessage = function (msg, line, source)
        {
            console.log('console> ' + msg);
        }

        page.onResourceError = function(err) {
            console.log('Page Error: ' + err.errorString);
        }

        // set page variables
        page.viewportSize = { width: 600, height: 600 };

        // a global PhantomJSPriting object can also be created to override these values
        page.paperSize = {
            format: opts.paper_size || 'letter',
            orientation: opts.orientation || 'portrait',
            margin: {
                left: opts.margin_left || '0.5in',
                top: opts.margin_top || '0.5in',
                right: opts.margin_right || '0.5in',
                bottom: opts.margin_bottom || '0.5in'
            },
            // default header/footer for pages that
            // don't have custom overrides (see below)
            header: {
                contents: ''
            },
            footer: {
                height: '0.325in', // make height a little more than below to avoid clipping
                contents: phantom.callback(function(page_idx, page_count) {
                    if (page_count == 1)
                        return '';

                    return '' +
                        '<div style="position: relative; height: 0.25in">' +
                            '<div style="position: absolute; bottom: 0; width: 100%; font-family: san-serif; font-size: 9px; text-align: center">' +
                                'Page ' + page_idx + ' of ' + page_count +
                            '</div>' +
                        '</div>';
                })
            }
        };

        // give ourselves access to jQuery in the page
        page.injectJs('jquery-1.7.2.min.js');
        page.injectJs('jquery.deparam.js');

        return page;
    }

    this.initPagination = function()
    {
        // paginated page; find out what we want to render
        if (opts.item_start !== undefined)
        {
            var start = undefined,
                count = undefined;

            if (opts.item_range == 'all')
            {
                start = 0;
                count = opts.total_count;
            }
             else if (opts.item_range == 'current_page')
            {
                start = opts.item_start;
                count = opts.count_per_page;
            }
             else if (opts.item_range == 'custom')
            {
                // custom range specified
                start = Math.max(opts.item_range_start - 1, 0);
                count = Math.min(opts.item_range_end - opts.item_range_start + 1, opts.total_count);
            }

            item_start = start;
            count_per_page = Math.min(count, max_count_per_page);
            total_count = count;
        }
    }

    this.debug = function()
    {
        page.evaluate(function(opts) {
            $.each(opts, function(key, val) {
                console.log(key+': '+val);
            });
        }, opts);

        phantom.exit();
    }

    // update url with new start/count params
    // for large item count pagination scenarios
    this.updateUrl = function(start, count)
    {
        // update the query string in the url with the specified start and count parameters
        var new_url = page.evaluate(function(url, query_string, start, count) {
            // get the existing query string and convert it to a JSON object
            var params = $.deparam(query_string.substring(1));

            // overwrite the 'start' and 'cnt' params
            params.start = start;
            params.cnt = count;

            // always show all detail rows for reports
            params.detailcnt = 'all';

            // trim off the existing query string
            url = url.substring(0, url.indexOf('?')+1);

            // append the new query string
            url += $.param(params);

            // return the url
            return url;
        }, opts.url, opts.query_string, start, count);

        // overwrite the url
        opts.url = new_url;
    }

    this.openUrl = function(callback)
    {
        page.open(opts.url, function(status) {
            if (status !== 'success')
            {
                console.log('Unable to load the url!');
                phantom.exit();
            }
             else
            {
                // render the page to PDF
                window.setTimeout(callback, 200);
            }
        });
    }

    this.render = function()
    {
        me.setTaskInfo(10, 'Rendering page');

        // basic page load; either non-paginated page or a paginated
        // page with less items than our max count per page
        if (item_start === undefined || count_per_page == total_count)
        {
            // paginated, but only one page load needed
            if (item_start !== undefined && count_per_page == total_count)
                me.updateUrl(item_start, total_count);

            // basic page load (no pagination)
            me.setTaskInfo(10, 'Rendering page');
            me.openUrl(function() {
                // allow dynamic report titles
                page.evaluate(function(title) {
                    $('.report-title').html(title);
                }, opts.report_title);

                // expand all collapsible items for reports (for now)
                page.evaluate(function() {
                    $('.collapsible .collapse').addClass('in');
                });

                me.setTaskInfo(90, 'Writing file');
                page.render(opts.output_path);
                me.setTaskInfo(95, 'Writing file');
                phantom.exit();
            });
        }
         else
        {
            // large paginated data set (requiring multiple
            // page loads and stitching together items)
            me.renderMany();
        }
    }

    this.renderMany = function()
    {
        // make sure we're working with a new page
        me.initPage();

        // render the last chunk
        var final_render = false;
        if (rendered_count+count_per_page >= total_count)
        {
            count_per_page = total_count-rendered_count;
            final_render = true;
        }

        me.updateUrl(item_start, count_per_page);
        item_start += count_per_page;
        rendered_count += count_per_page;

        me.openUrl(function() {
            if (final_render)
            {
                // allow dynamic report titles
                page.evaluate(function(title, prev_items) {
                    $('.report-title').html(title);
                    $('.report-body').prepend(prev_items);
                }, opts.report_title, stored_items);

                // we're done; render the page to PDF
                me.setTaskInfo(90, 'Writing file');
                page.render(opts.output_path);
                me.setTaskInfo(95, 'Render complete');
                phantom.exit();
            }
             else
            {
                pct = 10 + ((rendered_count/total_count)*80); // page rendering takes up 80% of the job progress
                pct = Math.min(90, pct);
                me.setTaskInfo(pct, 'Rendering items ' + (item_start-count_per_page+1) + ' - ' + (item_start));

                // get item markup from this page
                var items = page.evaluate(function() {
                    var html = $('.report-body').html();
                    return html;
                });

                // store these items so we can append them later
                stored_items += items;

                // get more items
                me.renderMany();
            }
        });
    }

    this.setTaskInfo = function(percent, message) {
        page.evaluate(function(task_id, percent, message) {
            $.ajax({
                type: 'GET',
                url: '/api/v1/settaskinfo',
                data: { "task_id": task_id, "percent": percent, "message": message },
                dataType: 'json',
                async: false
            });
        }, opts.task_id, percent, message);
    }

    // run the script
    me.run();

})();
