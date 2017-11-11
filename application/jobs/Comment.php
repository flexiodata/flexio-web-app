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

/*
// EXAMPLE:
{
    "type": "flexio.comment",
    "params": {
    }
}
*/

class Comment extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        parent::run($context);
    }


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
