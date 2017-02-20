<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-02-16
 *
 * @package flexio
 * @subpackage Jobs
 */


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class NopJob extends Base
{
    public function run()
    {
        // pass on the streams
        $this->getOutput()->merge($this->getInput());
    }


    // job definition info
    const MIME_TYPE = 'flexio.nop';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.nop",
        "params": {
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
                "enum": ["flexio.nop"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
