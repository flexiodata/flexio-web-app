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

/*
// EXAMPLE:
{
    "type": "flexio.exit",
    "params": {
        "code": ""
    }
}
*/

class Exit1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Jobs\IProcess $process)
    {
        parent::run($process);

        $job_definition = $this->getProperties();
        $code = $job_definition['params']['code'] ?? \Flexio\Jobs\Process::RESPONSE_NORMAL;

        // this next line will cause the proces loop to exit
        // and return the http response code in $code
        $process->setResponseCode((int)$code);
    }
}
