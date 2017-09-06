<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-09-06
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class HttpRequest extends \Flexio\Jobs\Base
{
    public function run()
    {

        // TODO: create an HTTP request; pass on the streams


        // pass on the streams
        $this->getOutput()->merge($this->getInput());
    }


    // job definition info
    const MIME_TYPE = 'flexio.httprequest';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.httprequest",
        "params": {
            "value": 1
        }
    }
EOD;
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.httprequest"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
