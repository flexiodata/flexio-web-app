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
    "type": "flexio.echo",
    "params": {
        "msg": ""
    }
}
*/

class Echo1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Jobs\IProcess $process)
    {
        parent::run($process);

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $job_definition = $this->getProperties();
        $msg = $job_definition['params']['msg'] ?? '';

        $streamwriter = $outstream->getWriter();
        $streamwriter->write($msg);
    }
}
