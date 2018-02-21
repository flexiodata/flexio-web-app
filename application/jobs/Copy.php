<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-13
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "copy",
    "params": {
        "from": "",
        "to": ""
    }
}
*/

class Copy extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $params = $this->getJobParameters();

        $this->copyFile($params['from'], $params['to']);
    }

    private function copyFile(string $from, string $to)
    {
        $data = \Flexio\Base\Stream::create();

        $subprocess = \Flexio\Jobs\Process::create();
        $subprocess->setStdout($data);
        $subprocess->execute([ 'op' => 'read', 'params' => [ 'path' => $from ] ]);

        $subprocess = \Flexio\Jobs\Process::create();
        $subprocess->setStdin($data);
        $subprocess->execute([ 'op' => 'write', 'params' => [ 'path' => $to ] ]);
    }
}
