<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-05-04
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Comment extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        // pass on the streams
        $this->getOutput()->merge($this->getInput());
    }


    // job definition info
    const MIME_TYPE = 'flexio.comment';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.comment",
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
                "enum": ["flexio.comment"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
