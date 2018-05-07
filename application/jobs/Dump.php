<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2018-02-27
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "dump",
    "msg": ""
}
*/

class Dump extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $outstream->copyFrom($instream);

        $params = $this->getJobParameters();
        $msg = $params['msg'] ?? '';

        if (is_array($msg) || is_object($msg))
        {
            $msg = @json_encode($msg);
        }

        echo $msg;
    }
}
