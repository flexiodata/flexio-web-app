<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-06
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "type": "flexio.write",
    "params": {
        "path": ""
    }
}
*/

class Write extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Jobs\IProcess $process)
    {
        parent::run($process);

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $outstream->copy($instream);

        $job_definition = $this->getProperties();
        $path = $job_definition['params']['path'] ?? null;

        if (is_null($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'path'");

        $streamreader = $instream->getReader();

        $vfs = new \Flexio\Services\Vfs();
        $files = $vfs->write($path, function($length) use (&$streamreader) {
            return $streamreader->read($length);
        });
    }
}
