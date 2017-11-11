<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-10-09
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Exit1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        parent::run($context);

        // process stdin
        $stdin = $context->getStdin();
        $context->setStdout($stdin);

        $job_definition = $this->getProperties();
        $code = $job_definition['params']['code'] ?? 200;

        // this next line will cause the proces loop to exit
        // and return the http response code in $code
        $context->setExitCode((int)$code);
    }


    // job definition info
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.exit",
        "params": {
            "code": ""
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
                "enum": ["flexio.exit"]
            },
            "params": {
                "type": "object",
                "required": ["code"],
                "properties": {
                }
            }
        }
    }
EOD;
}
