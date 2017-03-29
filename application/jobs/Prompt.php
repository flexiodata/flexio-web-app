<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-09-06
 *
 * @package flexio
 * @subpackage Jobs
 */


namespace Flexio\Jobs;


class Prompt extends \Flexio\Jobs\Base
{
    public function run()
    {
        // pass on the streams
        $this->getOutput()->merge($this->getInput());
    }


    // job definition info
    const MIME_TYPE = 'flexio.prompt';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.prompt",
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
                "enum": ["flexio.prompt"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
