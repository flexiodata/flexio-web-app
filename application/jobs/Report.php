<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-18
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

require_once __DIR__ . '/Execute.php';

/*
// EXAMPLE:
{
    "type": "flexio.report",
    "params": {
    }
}
*/


class ReportScriptHost extends ScriptHost
{
    public function func_getReportPayload()
    {
        return "<html><body><h1>Hello world!</h1></body></html";
    }
}


class Report extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);
/*
        $code = <<<EOT

        exports.flexio_handler = function(context) {

            var payload = context.proxy.invokeSync('getReportPayload', [])

            context.output.write(payload)
        }
EOT;*/

        $code = <<<EOT

        const puppeteer = require('puppeteer');
        
        exports.flexio_handler = function(context) {
            
            var html = context.proxy.invokeSync('getReportPayload', [])

            var f = (async () => {
                const browser = await puppeteer.launch({args:['--no-sandbox']});
                const page = await browser.newPage();
                //await page.goto('https://www.flex.io', {waitUntil: 'networkidle2'});
                await page.setContent(html)
                await page.pdf({path: '/tmp/document.pdf', format: 'A4'});
                
                await browser.close();

                //var rs = require('fs').createReadStream('/tmp/document.pdf');
                //rs.pipe(context.output);

                context.output.contentType = 'application/pdf'
                var data = require('fs').readFileSync('/tmp/document.pdf')
                context.output.write(data)
            });

            f().then(v => {

            }).catch(v => {
                context.output.write("Problem! " + v)
            })
        }

EOT;

        $dockerbin = \Flexio\System\System::getBinaryPath('docker');
        if (is_null($dockerbin))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxruntime sh -c '(echo ".base64_encode($code)." | base64 -d > /fxnodejs/script.js && timeout 30s nodejs /fxnodejs/run.js unmanaged /fxnodejs/script.js)'";

        $script_host = new ReportScriptHost();
        $script_host->setProcess($process);

        $ep = new ExecuteProxy;
        $ep->initialize($cmd, $script_host);
        $ep->run();

        $err = $ep->getStdError();

        if (isset($err))
        {
            //die("<pre>".$err);
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, $err);
        }

        return true;
    }
}
