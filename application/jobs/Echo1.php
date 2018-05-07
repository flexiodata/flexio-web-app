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
    "op": "echo",
    "msg": ""
}
*/

class Echo1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $params = $this->getJobParameters();
        $msg = $params['msg'] ?? '';


        if (is_array($msg) || is_object($msg))
        {
            $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
            $msg = json_encode($msg);
        }

        $streamwriter = $outstream->getWriter();
        $streamwriter->write($msg);

       // echo $msg;
    }
}
