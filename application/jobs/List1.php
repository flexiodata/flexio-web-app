<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-14
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class List1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        // process stdin
        $stdin = $context->getStdin();
        $stdout = $stdin->copy()->setPath(\Flexio\Base\Util::generateHandle());
        $context->setStdout($stdout);

        $streamwriter = \Flexio\Object\StreamWriter::create($stdout);
        $streamwriter->write("Hello");
    }


    // job definition info
    const MIME_TYPE = 'flexio.list';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.list",
        "params": {
            "path": ""
        }
    }
EOD;
    // direction is "asc" or "desc"
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.list"]
            },
            "params": {
                "type": "object",
                "required": ["value"],
                "properties": {

                }
            }
        }
    }
EOD;
}
