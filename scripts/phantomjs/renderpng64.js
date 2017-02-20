(function(undefined) {

    var me = this,
        system = require('system'),
        fs = require('fs'),
        args = system.args,
        page = require('webpage').create(),
        opts = undefined;

    var params_file = args[1];

    var ar = { x: 4, y: 3};

    page.zoomFactor = 0.2;
    page.viewportSize = { width: ar.x * 400, height: ar.y * 400 };
    page.clipRect = {
        top: 0,
        left: 0,
        width: ar.x * 70,
        height: ar.y * 70
    };

    this.parseParamsFile = function()
    {
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

    this.openUrl = function(callback)
    {
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

            page.evaluate(function() {
                $('#fx-app-content')
                    .css('top', 0)
                    .appendTo($('body').empty());
                $('.bgg-body').css('overflow', 'hidden');
            });

            // render the page to the specified image file
            //page.render(opts.output_path);

            // output the path to the browser
            //console.log(opts.output_path);

            // render the page to a base-64 png
            var base64 = page.renderBase64('PNG'),
                img = '<img src="data:image/png;base64,'+base64+'" />';

            if (opts.output_image === true)
                console.log(img);
                 else
                console.log(base64);

            phantom.exit();
        });
    }

    this.run = function()
    {
        me.parseParamsFile();
        me.setupSession();
        me.renderPage();
    }

    // run the script
    me.run();

})();
