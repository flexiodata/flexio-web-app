(function(undefined) {

    var me = this,
        system = require('system'),
        fs = require('fs'),
        args = system.args,
        page = require('webpage').create(),
        opts = undefined;

    this.parseParamsFile = function()
    {
        var params_file = args[1];

        try
        {
            var f = fs.open(params_file, 'r');
            var content = f.read();
            opts = JSON.parse(content);
            opts.url = decodeURIComponent(opts.url);
        }
        catch (e)
        {
            console.log("could not read params file");
            phantom.exit(1);
        }
    }

    this.setupSession = function()
    {
        // make sure we know about the session
        if (opts.session_id !== undefined && opts.domain !== undefined)
        {
            phantom.addCookie({
                'name': 'PHPSESSID',
                'value': opts.session_id,
                'domain': opts.domain
            });
        }
         else
        {
            phantom.exit(1);
        }
    }

    this.setupPage = function()
    {
        // establish thumbnail aspect ratio
        var ar = { x: 16, y: 10 };

        page.zoomFactor = 0.225;
        page.viewportSize = { width: ar.x * 100, height: ar.y * 100 };
        page.clipRect = {
            top: 0,
            left: 0,
            width: ar.x * 19,
            height: ar.y * 19
        };
    }

    this.openUrl = function(callback)
    {
        // useful for debugging
        // page.onResourceError = function(resourceError) { page.reason = resourceError.errorString; page.reason_url = resourceError.url; };

        page.open(opts.url, function(status) {
            if (status !== 'success')
            {
                console.log('unable to load the url');
                phantom.exit();
            }
             else
            {
                // render the page
                window.setTimeout(callback, 200);
            }
        });
    }

    this.renderPage = function()
    {
        me.openUrl(function() {
            // give ourselves access to jQuery in the page
            page.injectJs('jquery-1.7.2.min.js');
            page.injectJs('jquery.deparam.js');

            // render the page to a base-64 png
            var base64 = page.renderBase64('PNG');

            if (opts.output_image === true)
                console.log('<img src="data:image/png;base64,' + base64 + '" />');
                 else
                console.log(base64);

            phantom.exit();
        });
    }

    this.run = function()
    {
        me.parseParamsFile();
        me.setupSession();
        me.setupPage();
        me.renderPage();
    }

    // run the script
    me.run();

})();
