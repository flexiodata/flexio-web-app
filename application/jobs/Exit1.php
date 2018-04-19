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
    "op": "exit",
    "params": {
        "code": "",
        "response": ""
    }
}
*/

class Exit1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $job_definition = $this->getProperties();
        $code = $job_definition['params']['code'] ?? \Flexio\Jobs\Process::RESPONSE_NORMAL;
        $response = $job_definition['params']['response'] ?? '';

        if (is_array($response) || is_object($response))
        {
            $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
            $response = json_encode($response);
        }

        $streamwriter = $outstream->getWriter();
        $streamwriter->write($response);

        // this next line will cause the process loop to exit
        // and return the http response code in $code
        $process->setResponseCode((int)$code);
    }
}
